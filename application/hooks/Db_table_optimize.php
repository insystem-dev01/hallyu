<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Db_table_optimize  {
    protected $ci;
    public $super_hp;
    function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('Query_model');
        $this->ci->load->library('global_lib');
        $this->ci->load->library('common_lib');
        $this->ci->load->library('partner_lib');
        $this->ci->load->helper('url');
        $this->ci->load->helper('global_helper');
    }

    function common(){
        // 실행일 비교
        $default=$this->ci->load->get_var('default');
        $config=$this->ci->load->get_var('config');
        $OM_SERVER_TIME=time();
        $OM_TIME_YMDHIS=date('Y-m-d H:i:s',$OM_SERVER_TIME);
        $OM_TIME_YMD=substr($OM_TIME_YMDHIS, 0, 10);

        // 품절된 상품 상태 변경
        // $sql_condition = "SELECT index_no FROM shop_goods WHERE stock_qty = 0 AND isopen = 1";
        // $query = $this->ci->db->query($sql_condition);
        // $results = $query->result(); // 여러 행을 반환

        // foreach ($results as $result) {
        //     $index_no = $result->index_no; // 각 행의 index_no
        //     $sql = "UPDATE shop_goods SET isopen = 2 WHERE index_no = ?";
        //     $this->ci->Query_model->returnNull($sql,$index_no);
        // }

        // 타임 세일 종료된 상품 있는지 확인
        $sql_condition = "SELECT index_no, stock_qty FROM shop_goods WHERE type='timesale' AND eb_date <= ? AND isopen = 1 GROUP BY index_no";
        $query = $this->ci->db->query($sql_condition, array($OM_TIME_YMDHIS));
        $results = $query->result(); // 여러 행을 반환

        foreach ($results as $result) {
            $index_no = $result->index_no;

            // bid 상품 업데이트
            $this->update_timesale_goods($index_no, $OM_TIME_YMDHIS);

            // bid 성공 여부 확인
            if ($this->ci->funcomplex_lib->is_soldout_ignore_safety($index_no)) {
                $this->process_timesale_orders($index_no, $OM_TIME_YMDHIS, true);
            } else {
                $this->process_timesale_orders($index_no, $OM_TIME_YMDHIS, false);
            }
        }

        // 매달 1일에 gb_period 기준으로 전날 23:59:59 까지의 구매금액을 기준으로 등급 계산
        // 기간은 관리자에서 설정함
        $OM_TIME_YMDHIS=date('Y-m-d H:i:s',$OM_SERVER_TIME);
        $year = date('Y',$OM_SERVER_TIME);
        $month = date('m',$OM_SERVER_TIME);
        $day = date('d',$OM_SERVER_TIME);

        // 이미 실행한 내역 있는지 확인
        $sql = "SELECT `year`, `month`, `day` FROM shop_grade_history_log ORDER BY index_no DESC LIMIT 0,1";
        $update_log_data = $this->ci->Query_model->returnOneArr($sql);

        $continue_update = false;

        if (($update_log_data['year'] < $year) || ($update_log_data['year'] == $year && $update_log_data['month'] < $month && $update_log_data['day'] < $day)) {
            $continue_update = true;
        }

        // 해당 기능 활성화시 false 삭제해주세요
        if($day == "01" && $continue_update && false) {
            if(isset($config['gb_period'])) {
                $gb_period_ed = date('Y-m-d',strtotime($OM_TIME_YMD."-1 day"));
                $gb_period_st = date('Y-m-d',strtotime($OM_TIME_YMD."-{$config['gb_period']} month"));
    
                $gb_period_st = $gb_period_st ." 00:00:00";
                $gb_period_ed = $gb_period_ed ." 23:59:59";

                $sql = "SELECT gb_no AS grade, gb_minimum, gb_maximum FROM shop_member_grade WHERE gb_no != 1";
                $res= $this->ci->Query_model->returnArr($sql);

                $grade_update_cnt = 0;

                foreach($res as $key => $row) {
                    // 구매확정일 기준으로 조회
                    $sql2 = "SELECT SUM(use_price) as sum, mb_id, (SELECT grade FROM shop_member as b WHERE b.id = a.mb_id) AS grade FROM shop_order as a WHERE user_ok = 1 AND user_date BETWEEN '{$gb_period_st}' AND '{$gb_period_ed}' GROUP BY mb_id";
                    $res2 = $this->ci->Query_model->returnArr($sql2);

                    $grade = $row['grade'];
                    $gb_minimum = $row['gb_minimum'];
                    $gb_maximum = $row['gb_maximum'];

                    foreach($res2 as $key2 => $row2) {

                        if($row2['sum'] > $gb_minimum && $row2['sum'] <= $gb_maximum) {
                            if($grade != $row2['grade']) {

                                // 등급에 걸리는데 현재 등급과 다를 경우 실행
                                $updown = ($grade > $row2['grade']) ? "up" : "down";

                                // 업데이트 상세 로그
                                $update_log_value = array(
                                    'mb_id' => $row2['mb_id'],
                                    'old_grade' => $row2['grade'],
                                    'new_grade' => $grade,
                                    'sum_price' => $row2['sum'],
                                    'regdate' => $OM_TIME_YMDHIS,
                                    'is_grade_updown' => $updown
                                );

                                $this->ci->query_model->insertObj("shop_grade_update_log", $update_log_value);
                                
                                // 회원 등급 변경
                                $update_value = array(
                                    'grade' => $grade
                                );

                                $this->ci->query_model->updateObj("shop_member", $update_value, "id = '{$row2['mb_id']}'");

                                $grade_update_cnt++;
                            }
                        }
                    }
                }

                // 업데이트 실행 로그
                $history_value = array(
                    'regdate' => $OM_TIME_YMDHIS,
                    'year' => date('Y',$OM_SERVER_TIME),
                    'month' => date('m',$OM_SERVER_TIME),
                    'day' => date('d',$OM_SERVER_TIME),
                    'memo' => $OM_TIME_YMDHIS . " 회원 등급 업데이트 완료 ({$grade_update_cnt}명)"
                );

                $this->ci->query_model->insertObj("shop_grade_history_log", $history_value);

            }
        }
        
        if(isset($default['de_optimize_date']) && $default['de_optimize_date'] >= $OM_TIME_YMD)
            return;

        // 설정일이 지난 장바구니 상품 삭제
        if($default['de_cart_keep_term'] > 0) {
            $tmp_before_date = date("Y-m-d", $OM_SERVER_TIME - ($default['de_cart_keep_term'] * 86400));
            $sql = " delete from shop_cart where left(ct_time,10) < ? and ct_select='0' and od_id='' ";
            $param=array($tmp_before_date);
            $this->ci->Query_model->returnNull($sql,$param);
        }

        // 설정일이 지난 찜상품 삭제
        if($default['de_wish_keep_term'] > 0) {
            $tmp_before_date = date("Y-m-d", $OM_SERVER_TIME - ($default['de_wish_keep_term'] * 86400));
            $sql = " delete from shop_wish where left(wi_time,10) < ?";
            $param=array($tmp_before_date);
            $this->ci->Query_model->returnNull($sql,$param);
        }

        // 설정일이 지난 배송완료상품 구매확정
        if($default['de_final_keep_term'] > 0) {
            $tmp_before_date = date("Y-m-d", $OM_SERVER_TIME - ($default['de_final_keep_term'] * 86400));

            $sql = "SELECT *
                    FROM shop_order
                    WHERE left(invoice_date,10) < ? AND user_ok = '0' AND dan = '5'";
            $result = $this->ci->query_model->returnArr($sql,array($tmp_before_date));

            foreach($result as $row) {
                $this->insert_review_referral($row);

                $sql = "update shop_order set user_ok = '1', user_date = ?
                        where left(invoice_date,10) < ? and user_ok = '0' and dan = '5' ";
                $param=array($OM_TIME_YMDHIS,$tmp_before_date);
                $this->ci->Query_model->returnNull($sql,$param);

                if($row['sum_point'] > 0) {
                    // 포인트 적립
                    $this->ci->funcomplex_lib->insert_point($row['mb_id'], $row['sum_point'], "주문번호 {$row['od_id']} ({$row['od_no']}) 구매확정 적립", "@deliveryConfirm", $row['mb_id'], "{$row['od_id']},{$row['od_no']}");
                }

                if(!empty($row['referral_decode'])) {
                    // 리뷰 구매 포인트 적립
                    $this->insert_review_referral($row);
                }
            }
        }

        // 설정일이 지난 미입금된 주문내역 자동취소
        if($default['de_misu_keep_term'] > 0) {
            $tmp_before_date = date("Y-m-d", $OM_SERVER_TIME - ($default['de_misu_keep_term'] * 86400));
            $sql = " select *
                    from shop_order
                    where left(od_time,10) < ?
                        and dan = '1'
                    order by index_no ";
            #$res = sql_query($sql);
            $param=array($tmp_before_date);
            $res=$this->ci->Query_model->returnArr($sql,$param);
            foreach($res as $key => $row){
                $this->ci->funcomplex_lib->change_order_status_6($row['od_no']);

                // 메모남김
                $sql = " update shop_order
                            set shop_memo = CONCAT(shop_memo,\"\\n미입금 자동 주문취소 - ".$OM_TIME_YMDHIS." (취소이유 : {$default['de_misu_keep_term']}일경과)\")
                        where od_no = ?";
                $param=array($row['od_no']);
                $this->ci->Query_model->returnNull($sql,$param);
            }
        }

        // 실행일 기록
        if(isset($default['de_optimize_date'])) {
            // sql_query(" update shop_default set de_optimize_date = '".$OM_TIME_YMD."' ");
            $sql=" update shop_default set de_optimize_date = ?";
            $data=array($OM_TIME_YMD);
            $this->ci->Query_model->returnNull($sql,$data);
        }

        unset($tmp_before_date);
    } 

    /**
     * 리뷰 공유 적립 업데이트
     */
    private function insert_review_referral($row) {
        $OM_TIME_YMDHIS=date('Y-m-d H:i:s');

        // 리뷰 작성한 회원의 회원 정보 확인
        $referral_mb_id = $row['referral_decode'];
        $member	= $this->ci->global_lib->get_member($referral_mb_id, "grade");
        $grade = $member['grade'];

        // 회원 정보에 맞는 등급에 따라서 포인트 부여
        $sql = "SELECT gb_review_point FROM shop_member_grade WHERE gb_no = ?";
        $param = array($grade);
        $result = $this->ci->query_model->returnOneArr($sql,$param);

        $gb_point = $result['gb_payback_point']; // 적립률
        $point = (int) round($row['goods_price'] * ($gb_point * (1/100))); // 적립 포인트

        $gs = $this->ci->global_lib->get_goods($row['gs_id'], "gname");

        
        if($row['mb_id'] != $referral_mb_id) {
            // 포인트 적립
            $this->ci->funcomplex_lib->insert_point($referral_mb_id, $point, "리뷰 공유 [{$gs['gname']}] 구매확정 적립", "@reviewReferral", $referral_mb_id, "");

            // 리뷰 적립 테이블 저장
            $value = array(
                'od_id' => $row['od_id'],
                'mb_id' => $row['mb_id'],
                'referral_mb_id' => $referral_mb_id,
                'referral_code' => $row['referral_code'],
                'po_point' => $point,
                'regdate' => $OM_TIME_YMDHIS
            );

            $this->ci->query_model->insertObj("shop_review_referral", $value);
        }
    }

    /**
     * bid 상품 업데이트
     */
    private function update_timesale_goods($index_no, $end_time) {
        $sql_update = "UPDATE shop_goods SET isopen = 5, end_time = ? WHERE type='timesale' AND index_no = ?";
        $this->ci->db->query($sql_update, array($end_time, $index_no));
    }

    /**
     * 주문 처리 (성공)
     */
    private function process_timesale_orders($index_no, $OM_TIME_YMDHIS, $is_success) {
        $orders = $this->ci->global_lib->get_timesale_list($index_no);
    
        foreach ($orders as $order_index => $order_row) {
            if(isset($order_row['od_id'])) {
                // 주문 정보 가져오기
                $order_details = $this->ci->global_lib->get_order($order_row['od_id']);
                
                // bid 성공 여부에 따라서 진행, 전체 환불
                if ($is_success) {
                    $this->update_order($order_row, $order_details, $OM_TIME_YMDHIS);
                } else {
                    $this->process_refund($order_details, $order_row, $OM_TIME_YMDHIS);
                }
            }
        }
    }

    /**
     * 주문 업데이트
     */
    private function update_order($row, $od, $OM_TIME_YMDHIS) {
        $gs = $this->ci->global_lib->get_goods($od['gs_id']);

        // 포인트 적립하는 상품의 경우 shop_order sum_point 업데이트 시키기
        $point = 0;
        $member = $this->ci->global_lib->get_member($od['mb_id']);
        $grade = $member['grade'];

        $sql = "SELECT gb_buy_point FROM shop_member_grade WHERE gb_no = ?";
        $param = array($grade);
        $result = $this->ci->query_model->returnOneArr($sql,$param);

        if($gs['is_point'] > 0) {
            $gb_point = $result['gb_buy_point']; // 적립률
            $point = (int) round(($row['real_price'] * $od['sum_qty']) * ($gb_point * (1/100))); // 적립 포인트
        }

        // shop_order 테이블 업데이트
        $this->ci->Query_model->returnNull("UPDATE shop_order SET use_price = ((? + baesong_price + io_price) - coupon_price), goods_price = ?, refund_mileage_price = ?, sum_point = ? WHERE od_id = ? AND mb_id = ?", array($row['real_price'], $row['real_price'], $row['difference'], $point, $row['od_id'], $row['mb_id']));
        
        // shop_order_timesale 테이블 업데이트
        $this->ci->Query_model->returnNull("UPDATE shop_order_timesale SET gts_no = ?, goods_price = ?, real_price = ?, difference = ? WHERE od_id = ? AND mb_id = ?", array($row['index_no'], $row['normal_price'], $row['real_price'], $row['difference'], $row['od_id'], $row['mb_id']));
        
        // shop_goods_timesale 테이블 업데이트
        $this->ci->Query_model->returnNull("UPDATE shop_goods_timesale SET mb_id = ?, od_id = ?, is_confirm = 'y', confirm_update_date = ? WHERE gs_id = ?", array($row['mb_id'], $row['od_id'], $OM_TIME_YMDHIS, $row['index_no']));

        // 예치금 적립
        $this->ci->funcomplex_lib->insert_mileage($od['mb_id'], $row['difference'], "주문번호 {$od['od_id']} ({$od['od_no']}) 예치금 적립");
    }

    /**
     * 환불 처리
     */
    private function process_refund($od, $row, $OM_TIME_YMDHIS) {
        $this->ci->Query_model->returnNull("UPDATE shop_order SET dan = '6', cancel_price = (goods_price + baesong_price), cancel_date = ? WHERE od_id = ?", array($OM_TIME_YMDHIS, $row['od_id']));
        
        if ($od['paymethod'] == '신용카드' || $od['paymethod'] == '계좌이체') {
            $this->refund_payment($od);
        }

        // 포인트 및 예치금 반환
        if ($od['mb_id']) {
            if ($od['use_point']) {
                $this->ci->funcomplex_lib->insert_point($od['mb_id'], $od['use_point'], "주문번호 {$od['od_id']} ({$od['od_no']}) 주문취소");
            }
            if ($od['use_mileage']) {
                $this->ci->funcomplex_lib->insert_mileage($od['mb_id'], $od['use_mileage'], "주문번호 {$od['od_id']} ({$od['od_no']}) 주문취소");
            }
        }

        $this->ci->funcomplex_lib->change_order_status_timesale($od['od_id']);
    }

    /**
     * 결제 환불 처리
     */
    private function refund_payment($od) {
        include_once('./Bill/iamport.php');
        $iamport = new Iamport('4482313508533864', 'P84FHcHJo90l8UsjSE22SrcccZ5SlvY2goKRdq54SSLmVbbHqqbeXPJgsOYbfmgdKDapzYCmnjiCWqGw');
        $result = $iamport->cancel(array(
            'imp_uid' => $od['imp_uid'],
            'merchant_uid' => $od['merchant_uid'],
            'reason' => "함께장터 구매 실패로 인한 환불",
        ));
    }
}