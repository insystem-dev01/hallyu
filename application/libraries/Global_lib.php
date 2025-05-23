<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Global_lib {
    protected $ci;
    protected $OM_TIME_YMDHIS;
    protected $OM_TIME_HIS;
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('Query_model');
        $this->OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $this->OM_TIME_HIS==date("H:i:s", time());
        // Do something with $params
    }

    // 상품명과 건수를 반환
    function get_full_name($cart_id){
        // 상품명만들기
        $sql="select a.gs_id, b.gname from shop_cart a, shop_goods b where a.gs_id = b.index_no and a.od_id = ? order by a.index_no limit 1";
        $data=array($cart_id);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);
        // 상품명에 "(쌍따옴표)가 들어가면 오류 발생함
        $goods['gs_id'] = $row['gs_id'];
        $goods['full_name']= $goods['name'] = addslashes($row['gname']);
        // 특수문자제거
        $goods['full_name'] = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "",  $goods['full_name']);

        // 상품건수
        $sql2="select count(*) as cnt from shop_cart where od_id = ?";
        $data2=array($cart_id);
        $row=$this->ci->Query_model->returnOneArr($sql2,$data2);
        $cnt = $row['cnt'] - 1;
        if($cnt)
            $goods['full_name'] .= ' 외 '.$cnt.'건';
        $goods['count'] = $row['cnt'];
        return $goods;
    }

    // 복합과세
    function comm_tax_flag($od_id){
        $comm_tax_mny	= 0; // 과세금액
        $comm_vat_mny	= 0; // 부가세
        $comm_free_mny	= 0; // 면세금액
        $tot_tax_mny	= 0;
        $tot_baesong	= 0;

        $sql=" select * from shop_order where od_id =? order by index_no ";
        $data=array($od_id);
        $result=$this->ci->Query_model->returnArr($sql,$data);
        foreach($result as $key => $row){
            if($row['gs_notax']) // 과세
                $tot_tax_mny += ($row['use_price'] - $row['baesong_price']);
            else // 면세
                $comm_free_mny += ($row['use_price'] - $row['baesong_price']);

            $tot_baesong += $row['baesong_price'];
        }
        $comm_tax_mny = round(($tot_tax_mny + $tot_baesong) / 1.1);
        $comm_vat_mny = ($tot_tax_mny + $tot_baesong) - $comm_tax_mny;

        $info = array();
        $info['comm_tax_mny']  = $comm_tax_mny;
        $info['comm_vat_mny']  = $comm_vat_mny;
        $info['comm_free_mny'] = $comm_free_mny;

        return $info;
    }

    /*******************************************************************************
    유일한 키를 얻는다.

    결과 :

        년월일시분초00 ~ 년월일시분초99
        년(4) 월(2) 일(2) 시(2) 분(2) 초(2) 100분의1초(2)
        총 16자리이며 년도는 2자리로 끊어서 사용해도 됩니다.
        예) 2008062611570199 또는 08062611570199 (2100년까지만 유일키)

    사용하는 곳 :
    1. 주문번호 생성시에 사용한다.
    2. 기타 유일키가 필요한 곳에서 사용한다.
    *******************************************************************************/
    function get_uniqid(){
        #sql_query(" LOCK TABLE shop_uniqid WRITE ");
        $sql="LOCK TABLE shop_uniqid WRITE";
        $this->ci->Query_model->returnNull($sql);
        while (1) {
            $key = date('ymdHis', time());
            $key .= sprintf('%02d', (int)(microtime(true) * 100) % 100);

            $sql2="insert into shop_uniqid set uq_id = ?, uq_ip=?";
            $data2=array($key,$_SERVER['REMOTE_ADDR']);
            $result=$this->ci->Query_model->returnQueryObj($sql2,$data2);
            if($result) break; // 쿼리가 정상이면 빠진다.

            // insert 하지 못했으면 일정시간 쉰다음 다시 유일키를 만든다.
            usleep(10000); // 100분의 1초를 쉰다
        }

        $sql="UNLOCK TABLES";
        $this->ci->Query_model->returnNull($sql);
        return $key;
    }

    // 장바구니 유일키검사
    function cart_uniqid(){
        while(1) {
            srand((double)microtime()*1000000);
            $key = rand(1000000000,9999999999);
            $sql1="select count(*) as cnt from shop_cart where od_no =?";
            $data=array($key);
            $row1=$this->ci->Query_model->returnOneArr($sql1,$data);
            $sql2="select count(*) as cnt from shop_order where od_no = ?";
            $data2=array($key);
            $row2=$this->ci->Query_model->returnOneArr($sql2,$data2);
            if(!$row1['cnt'] && !$row2['cnt']) break; // 없다면 빠진다.

            // count 하지 못했으면 일정시간 쉰다음 다시 유일키를 검사한다.
            usleep(10000); // 100분의 1초를 쉰다
        }
        return $key;
    }

    // 상품의 재고 (창고재고수량)
    function get_it_stock_qty($gs_id){
        $sql = "SELECT stock_qty,stock_mod FROM shop_goods WHERE index_no = ? ";
        $data=array($gs_id);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);
        $jaego = (int)($row['stock_qty']);
        if(!($row['stock_mod'])) {
            $jaego = 999999999;
        }
        return $jaego;
    }

    // bid 상품 재고 수량(안전재고 고려 X)
    function get_it_timesale_stock_qty($gs_id){
        // 판매 개수 확인
        $sql = "SELECT sum(sum_qty) as cnt
                FROM shop_order
                WHERE dan IN ('2','3','4','5') and gs_id = ?";
        $data=array($gs_id);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);
        $sales_qty = $row['cnt'];

        // 상품 재고확인
        $sql = "SELECT original_stock_qty FROM shop_goods WHERE index_no = ? ";
        $row2 = $this->ci->Query_model->returnOneArr($sql,$data);
        $original_stock_qty = $row2['original_stock_qty'];

        // 상품 재고 - 판매 개수
        $stock_qty = $original_stock_qty - $sales_qty;

        return $stock_qty;
    }

    // bid 상품 재고 수량(안전재고 고려 O)
    function get_it_timesale_safety_stock_qty($gs_id){
        $sql = "SELECT sum(sum_qty) as cnt
                FROM shop_order
                WHERE dan IN ('2','3','4','5') and gs_id = ?";
        $data=array($gs_id);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);
        $sales_qty = $row['cnt'];

        $sql = "SELECT safety_stock_qty, original_stock_qty FROM shop_goods WHERE index_no = ? ";
        $row2 = $this->ci->Query_model->returnOneArr($sql,$data);
        $safety_stock_qty = $row2['safety_stock_qty'];
        $original_stock_qty = $row2['original_stock_qty'];

        $stock_qty = ($safety_stock_qty + $original_stock_qty) - $sales_qty;

        return $stock_qty;
    }

    // 쿠폰 : 사용 가능한 쿠폰
    function get_cp_precompose($mb_id){
        $query = array();

        // 쿠폰유효 기간 (날짜)
        $fr_date = "(cp_inv_sdate = '9999999999' or cp_inv_sdate <= curdate())";
        $to_date = "(cp_inv_edate = '9999999999' or cp_inv_edate >= curdate())";

        // 쿠폰유효 기간 (시간대)
        $fr_hour = "(cp_inv_shour1 = '99' or cp_inv_shour1 <= date_format(now(),'%H'))";
        $to_hour = "(cp_inv_shour2 = '99' or cp_inv_shour2 > date_format(now(),'%H'))";

        $query[0]  = " from shop_coupon_log ";
        $query[1]  = " where mb_id='$mb_id' and mb_use='0' ";
        $query[1] .= " and ((cp_inv_type='0' and ($fr_date and $to_date) and ($fr_hour and $to_hour)) ";
        $query[1] .= " or (cp_inv_type='1' and date_add(`cp_wdate`, interval `cp_inv_day` day) > now())) ";
        $query[2]  = " order by lo_id ";

        $sql = " select count(*) as cnt {$query[0]} {$query[1]}";
        $row=$this->ci->Query_model->returnOneArr($sql);
        $query[3] = (int)$row['cnt'];

        return $query;
    }

    #문자설정 정보
    function get_sms($mb_id){
        $sql="select * from shop_sms where mb_id=?";
        $data=array($mb_id);
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }

    // 회원의 정보를 추출 ($mb_no는 회원의 주키값)
    function get_member_no($mb_no, $fileds='*'){
        if($fileds=="*"){
            $sql="select * from shop_member where index_no=?";
            $data=array($mb_no);
        }else{
            $sql="select ? from shop_member where index_no=?";
            $data=array($fileds,$mb_no);
        }
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }

    // 회원의 정보를 리턴
    function get_member($mb_id, $fileds='*'){
        $sql="";
        $sql="select {$fileds} from shop_member where id = TRIM(?)";
        $param=array($mb_id);
        return $this->ci->Query_model->returnOneArr($sql,$param);
    }

    // 회원레벨 인덱스번호 체크
    function get_grade($gb_no){
        $sql="select * from shop_member_grade where gb_no = ?";
        $data=array($gb_no);
        $row = $this->ci->Query_model->returnOneArr($sql,$data);
        $gb_name = $row['gb_name'];

        return $gb_name;
    }

    // 사용포인트 입력
    function insert_use_point($mb_id, $point, $po_id=''){
        $point1 = abs($point);
        $sql = " select po_id, po_point, po_use_point from shop_point where mb_id = ? and po_id <> ? and po_point > po_use_point order by po_id asc ";
        $data=array($mb_id,$po_id);
        $result=$this->ci->Query_model->returnArr($sql,$data);
        foreach($result as $key => $row){
            $point2 = $row['po_point'];
            $point3 = $row['po_use_point'];
            if(($point2 - $point3) > $point1) {
                $sql2 = " update shop_point set po_use_point = po_use_point + ? where po_id = ? ";
                $data2=array($point1,$row['po_id']);
                $this->ci->Query_model->returnNull($sql2,$data2);
                break;
            }else{
                $point4 = $point2 - $point3;
                $sql2 = " update shop_point set po_use_point = po_use_point + ? where po_id = ? ";
                $data2=array($point4,$row['po_id']);
                $this->ci->Query_model->returnNull($sql2,$data2);
                $point1 -= $point4;
            }
        }
    }

    // 사용포인트 삭제
    function delete_use_point($mb_id, $point){
        $point1 = abs($point);
        $sql = " select po_id, po_use_point from shop_point where mb_id = ? and po_use_point > 0 order by po_id desc ";
        $data=array($mb_id);
        $result=$this->ci->Query_model->returnArr($sql,$data);
        foreach($result as $key => $row){
            $point2 = $row['po_use_point'];

            if($point2 > $point1) {
                $sql2 = " update shop_point set po_use_point = po_use_point - ? where po_id = ? ";
                $data2=array($point1,$row['po_id']);
                $this->ci->Query_model->returnNull($sql2,$data2);
                break;
            } else {
                $sql2 = " update shop_point set po_use_point = '0' where po_id = ? ";
                $data2=array($row['po_id']);
                $this->ci->Query_model->returnNull($sql2,$data2);

                $point1 -= $point2;
            }
        }
    }

    // 포인트 내역 합계
    function get_point_sum($mb_id){
        // 포인트합
        $sql = " select sum(po_point) as sum_po_point from shop_point where mb_id = ? ";
        $data=array($mb_id);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);
        return (int)$row['sum_po_point'];
    }

    // 예치금 내역 합계
    function get_mileage_sum($mb_id){
        // 포인트합
        $sql = " select sum(po_point) as sum_po_point from shop_mileage where mb_id = ? ";
        $data=array($mb_id);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);
        return (int)$row['sum_po_point'];
    }

    // 상품 진열,1
    function display_itemtype($mb_id, $type, $rows=''){
        $sql = "SELECT a.* FROM shop_goods a LEFT JOIN shop_goods_type b ON (a.index_no=b.gs_id) WHERE b.mb_id = ?
                    AND a.shop_state = '0'
                    AND a.isopen IN ('1','2','5')
                    AND find_in_set(?, a.use_hide) = '0'
                    AND b.it_type{$type} = '1'
                ORDER BY a.`rank` desc, a.index_no DESC ";
        $data=array($mb_id,$mb_id);
        if($rows){
            $sql .= " limit {$rows} ";
        } 
        $result=$this->ci->Query_model->returnQueryObj($sql,$data);
        $type_count = $result->num_rows();

        if(!$type_count && $mb_id != 'admin') {
            $sql = " SELECT a.* FROM shop_goods a LEFT JOIN shop_goods_type b ON (a.index_no=b.gs_id) WHERE b.mb_id = 'admin'
                        AND a.shop_state = '0'
                        AND a.isopen IN ('1','2','5')
                        AND find_in_set({'$mb_id'}, a.use_hide) = '0'
                        AND b.it_type{$type} = '1'
                    ORDER BY a.index_no DESC ";
            if($rows){ 
                $sql .= " limit {$rows}";
            }
            $result = $this->ci->Query_model->returnQueryObj($sql);
        }
        return $result;
    }

    // 상품 진열,2
    function query_itemtype($mb_id, $type, $sql_search, $sql_order){
        $sql = "SELECT a.* 
                FROM shop_goods a LEFT JOIN shop_goods_type b ON (a.index_no=b.gs_id) WHERE b.mb_id = '{$mb_id}'
                    AND a.shop_state = '0' AND a.isopen IN ('1','2','5') AND find_in_set('{$mb_id}', a.use_hide) = '0'
                    AND b.it_type{$type} = '1' {$sql_search} {$sql_order} ";
        $result=$this->ci->Query_model->returnQueryObj($sql);
        $type_count = $result->num_rows();

        if(!$type_count && $mb_id != 'admin') {
            $sql = "SELECT a.*
                    FROM shop_goods a LEFT JOIN shop_goods_type b ON (a.index_no=b.gs_id)
                    WHERE b.mb_id = 'admin'
                        AND a.shop_state = '0'
                        AND a.isopen IN ('1','2','5')
                        AND find_in_set('{$mb_id}', a.use_hide) = '0'
                        AND b.it_type{$type} = '1' {$sql_search} {$sql_order}";
            $result=$this->ci->Query_model->returnQueryObj($sql);
        }
        return $result;
    }

    // bid 상품 진열
    function display_timesale($mb_id, $rows='', $sql_search='', $sql_order=''){
        $sql = "SELECT *
                FROM shop_goods WHERE shop_state = '0' AND isopen IN ('1','2','5') AND find_in_set(?, use_hide) = '0' AND type = 'timesale' {$sql_search}
                {$sql_order}";
        $data = array($mb_id);
        if($rows){
            $sql .= " limit {$rows} ";
        } 
        $result = $this->ci->Query_model->returnQueryObj($sql,$data);
        return $result;
    }

    // 관심도 급상승 상품 진열(조회수)
    function display_attention($mb_id, $rows=''){
        $sql = "SELECT *
                FROM shop_goods WHERE shop_state = '0' AND isopen IN ('1','2','5') AND find_in_set(?, use_hide) = '0' AND type != 'timesale'
                ORDER BY readcount desc, `rank` desc, index_no desc ";
        $data = array($mb_id);
        if($rows){
            $sql .= " limit {$rows} ";
        } 
        $result = $this->ci->Query_model->returnQueryObj($sql,$data);
        return $result;
    }

    // 브랜드관
    function display_brand($mb_id, $rows=''){
        $sql = "SELECT *
                FROM shop_goods WHERE shop_state = '0' AND isopen IN ('1','2','5') AND find_in_set(?, use_hide) = '0' AND type != 'timesale' AND brand_uid != ''
                ORDER BY `rank` desc, index_no desc ";
        $data = array($mb_id);
        if($rows){
            $sql .= " limit {$rows} ";
        } 
        $result = $this->ci->Query_model->returnQueryObj($sql,$data);
        return $result;
    }

    // 실속관
    function display_substance($mb_id, $rows=''){
        $sql = "SELECT *
                FROM shop_goods WHERE shop_state = '0' AND isopen IN ('1','2','5') AND find_in_set(?, use_hide) = '0' AND type != 'timesale'
                ORDER BY `rank` desc, index_no desc ";
        $data = array($mb_id);
        if($rows){
            $sql .= " limit {$rows} ";
        } 
        $result = $this->ci->Query_model->returnQueryObj($sql,$data);
        return $result;
    }

    // 입점사 정보를 리턴
    function get_seller($mb_id, $fileds='*'){
        $sql="select {$fileds} from shop_seller where mb_id = TRIM(?)";
        $data=array($mb_id);
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }

    // 입점사 정보를 리턴
    function get_seller_cd($code, $fileds='*'){
        $sql="select {$fileds} from shop_seller where seller_code = TRIM(?)";
        $data=array($code);
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }

    // 공급업체 유일키검사
    function code_uniqid(){
        for($i=100001; $i<=999999; $i++) {
            $code = 'AP-'.sprintf("%06d", $i);

            // 주문서에 탈퇴한 입점사코드가 있는지 검사
            $sql = " select count(*) as cnt from shop_order where seller_id = ? ";
            $data=array($code);
            $row=$this->ci->Query_model->returnOneArr($sql,$data);
            if($row['cnt']) // 있다면 건너뛴다.
                continue;

            $sql = " select count(*) as cnt from shop_seller where seller_code = ? ";
            $row=$this->ci->Query_model->returnOneArr($sql,$data);
            if(!$row['cnt']) { // 없다면 빠진다.
                return $code;
                break;
            }

            // count 하지 못했으면 일정시간 쉰다음 다시 유일키를 검사한다.
            usleep(10000); // 100분의 1초를 쉰다
        }
        return '';
    }

    // 옵션의 재고 (창고재고수량)
    function get_option_stock_qty($gs_id, $io_id, $type){
        $sql = " select io_stock_qty
                    from shop_goods_option
                    where gs_id = ?
                    and io_id = ?
                    and io_type = ?
                    and io_use = '1' ";
        $data=array($gs_id,$io_id,$type);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);
        $jaego = (int)$row['io_stock_qty'];
        return $jaego;
    }

    // 주문서별 실입금액 합계
    function get_order_ipgum($od_id){
        $sql = " select SUM(use_price) as useprice from shop_order where od_id = ? ";
        $data=array($od_id);
        $orw=$this->ci->Query_model->returnOneArr($sql,$data);

        return $row;
    }

    // 주문서별 총합계
    function get_order_spay($od_id, $sql_search=''){
        $sql = " select SUM(goods_price) as price,
                        SUM(baesong_price) as baesong,
                        SUM(goods_price + baesong_price) as buyprice,
                        SUM(supply_price) as supply,
                        SUM(cancel_price) as cancel,
                        SUM(refund_price) as refund,
                        SUM(coupon_price) as coupon,
                        SUM(refund_mileage_price) as refund_mileage_price,
                        SUM(use_point) as usepoint,
                        SUM(use_price) as useprice,
                        SUM(use_mileage) as usemileage,
                        SUM(sum_qty) as qty,
                        SUM(sum_point) as point
                from shop_order
                where od_id = ? {$sql_search} ";
        $data=array($od_id);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);
        return $row;
    }
    
    // 주문정보 주문번호
    function get_order($order_id, $fileds='*'){
        if(strlen($order_id) < 14)
            $sql_where = " where od_no = {$order_id}"; // 주문일련번호
        else
            $sql_where = " where od_id = {$order_id}"; // 주문번호

        $sql="select * from shop_order {$sql_where}";
        return $this->ci->Query_model->returnOneArr($sql);
    }

    // 신규가입 쿠폰일경우 다시 사용할 수 있도록 돌려준다.
    function subtract_coupon_log($od_no){
        $sql = " select lo_id from shop_coupon_log where od_no = ? and cp_type = '5' ";
        $data=array($od_no);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);
        if($row['lo_id']) {
            $sql = " update shop_coupon_log
                        set mb_use = '0'
                        , od_no = ''
                        , cp_udate = ''
                    where lo_id = ?";
            $data=array($row['lo_id']);
            $this->ci->Query_model->returnNull($sql,$data);
        }
    }

    // 상품옵션별재고 또는 상품재고에 더하기
    function add_io_stock($gs_id, $od_id){
        $sql = " select * from shop_cart where od_id = ? and gs_id= ?";
        $data=array($od_id,$gs_id);
        $res=$this->ci->Query_model->returnArr($sql,$data);
        foreach($res as $key => $ct){
            if($ct['io_id']) { // 옵션 : 재고수량 증가
                $sql = " update shop_goods_option
                            set io_stock_qty = io_stock_qty + ?
                        where io_id = ?
                            and gs_id = ?
                            and io_type = ?
                            and io_stock_qty <> '999999999' ";
                $data=array($ct['ct_qty'],$ct['io_id'],$ct['gs_id'],$ct['io_type']);
                $this->ci->Query_model->returnNull($sql,$data);
            } else { // 상품 : 재고수량 증가
                $sql = " update shop_goods
                            set stock_qty = stock_qty + ?
                        where index_no = ?
                            and stock_mod = '1' ";
                $data=array($ct['ct_qty'],$ct['gs_id']);
                $this->ci->Query_model->returnNull($sql,$data);
            }
        }
    }

    // 상품 판매수량 반영
    function add_sum_qty($gs_id){
        // 배송완료 된 상품만 수량을 더한다.
        $sql = " select sum(sum_qty) as it_sum_qty from shop_order where gs_id = ? and dan IN(5,8) ";
        $data=array($gs_id);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);

        $sql = " update shop_goods set sum_qty = ? where index_no = ?";
        $data=array($row['it_sum_qty'],$gs_id);
        $this->ci->Query_model->returnNull($sql,$data);
    }

    // '배송완료' > '구매확정' 상태로 변경
    function change_status_final($od_no){
        $OM_TIME_YMDHIS=$this->OM_TIME_YMDHIS;
        $sql = " update shop_order set user_ok = '1', user_date = ? where od_no = ? and user_ok = '0' and dan = '5' ";
        $data=array($OM_TIME_YMDHIS,$od_no);
        $this->ci->Query_model->returnNull($sql,$data);
    }

    // '구매확정' > '구매확정취소' 상태로 변경
    function change_status_final_cancel($od_no){
        $sql = " update shop_order
                    set user_ok = '0'
                    , user_date = '0000-00-00 00:00:00'
                where od_no = ?
                    and user_ok = '1'
                    and dan = '5' ";
        $data=array($od_no);
        $this->ci->Query_model->returnNull($sql,$data);
    }

    // '입금대기' 상태로 변경
    function change_order_status_1($od_no){
        $sql = " update shop_order
                    set dan = '1'
                    , receipt_time = '0000-00-00 00:00:00'
                where od_no = ?
                    and dan = '2' ";
        $data=array($od_no);
        $this->ci->Query_model->returnNull($sql,$data);
    }

    // '입금완료' 상태로 변경 (일련번호)
    function change_order_status_2($od_no){
        $OM_TIME_YMDHIS=$this->OM_TIME_YMDHIS;
        $sql = " update shop_order
                    set dan = '2'
                    , receipt_time = ?
                where od_no = ?
                    and dan = '1' ";
        $data=array($OM_TIME_YMDHIS,$od_no);
        $this->ci->Query_model->returnNull($sql,$data);
    }

    // '입금완료' 상태로 변경 (주문번호)
    function change_order_status_ipgum($od_id){
        $OM_TIME_YMDHIS=$this->OM_TIME_YMDHIS;
        $sql = " update shop_order
                    set dan = '2'
                    , receipt_time = ?
                where od_id = ?
                    and dan = '1' ";
        $data=array($OM_TIME_YMDHIS,$od_id);
        $this->ci->Query_model->returnNull($sql,$data);
    }

    // '배송준비' 상태로 변경
    function change_order_status_3($od_no, $delivery='', $delivery_no=''){
        $sql = " update shop_order
                    set dan = '3'
                    , delivery = ?
                    , delivery_no = ?
                where od_no = ? ";
        $data=array($delivery,$delivery_no,$od_no);
        $this->ci->Query_model->returnNull($sql,$data);
    }

    // '배송중' 상태로 변경
    function change_order_status_4($od_no, $delivery='', $delivery_no=''){
        $OM_TIME_YMDHIS=$this->OM_TIME_YMDHIS;
        $sql = " update shop_order
                    set dan = '4'
                    , delivery_date = ?
                    , delivery = ?
                    , delivery_no = ?
                where od_no = ? ";
        $data=array($OM_TIME_YMDHIS,$delivery,$delivery_no,$od_no);
        $this->ci->Query_model->returnNull($sql,$data);
    }

    // '배송후 교환' 상태로 변경
    function change_order_status_8($od_no){
        $OM_TIME_YMDHIS=$this->OM_TIME_YMDHIS;
        $sql = " update shop_order
                    set dan = '8'
                    , change_date = ?
                where od_no = ? ";
        $data=array($OM_TIME_YMDHIS,$od_no);
        $this->ci->Query_model->returnNull($sql,$data);
    }

    // 게시판설정
    function get_board($boardid){
        $sql="select * from shop_board_conf where index_no=?";
        $data=array($boardid);
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }

    // 카테고리
    function get_cate($catecode){
        $sql="select * from shop_category where catecode=?";
        $data=array($catecode);
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }

    // 카테고리 이름
    function get_catename($code){
        $sql="select catename from shop_category where catecode=?";
        $data=array($code);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);
        return $row['catename'];
    }

    // 장바구니의 정보를 리턴.
    // $idnex는 장바구니 주키 값
    function get_cart_id($cart_id){
        $sql="select * from shop_cart where index_no=?";
        $data=array($cart_id);
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }

    // 장바구니의 정보를 리턴.
    // $od_no는 장바구니 주문번호
    function get_shop_cart($od_no){
        $sql="select * from shop_cart where od_no=?";
        $data=array($od_no);
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }

    // 상품 정보의 배열을 리턴
    function get_goods($gs_id, $fileds='*'){
        $sql="select {$fileds} from shop_goods where index_no=?";
        $data=array($gs_id);
        
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }

    // 상품 정보의 배열을 리턴(상품코드)
    function get_goods_gcode($gcode, $fileds='*'){
        $sql="select {$fileds} from shop_goods where gcode=?";
        $data=array($gcode);
        
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }

    // 상품 브랜드주키 정보의 배열을 리턴
    function get_brand($br_id){
        $sql="select br_id,br_name from shop_brand where br_id=?";
        $data=array($br_id);
        $br=$this->ci->Query_model->returnOneArr($sql,$data);
        $br_name="";
        if($br['br_id'])
            $br_name = $br['br_name'];

        return $br_name;
    }

    // 인기검색어 입력
    function insert_popular($pt_id, $str){
        if(!$str) return;
        $OM_TIME_YMDHIS=$this->OM_TIME_YMDHIS;
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if(!preg_match("/bot|slurp/", $agent)) {
            $sql = " insert ignore into shop_popular set pt_id = ?, pp_word = ?, pp_date = ?, pp_ip = ?";
            $data=array($pt_id,$str,$OM_TIME_YMDHIS,$_SERVER['REMOTE_ADDR']);
            $this->ci->Query_model->returnNull($sql,$data);
        }
    }

    // 게시판의 다음글 번호를 얻는다.
    function get_next_num($table){
        // 가장 큰 번호를 얻어
        $sql = " select max(index_no) as max_num from {$table} ";
        $row=$this->ci->Query_model->returnOneArr($sql);
        // 가장 큰 번호에 1을 더해서 넘겨줌
        return (int)($row['max_num'] + 1);
    }

    // 다음글 번호를 얻는다.
    function get_next_wr_num($table, $val, $option=''){
        // 가장 큰 번호를 얻어
        $sql = "select max($val) as max_num from {$table} {$option}";
        $row=$this->ci->Query_model->returnOneArr($sql);
        // 가장 큰 번호에 1을 더해서 넘겨줌
        return (int)($row['max_num'] + 1);
    }

    // 특정필드는 제외
    function get_columns($tablename){
        $columns = array();
        $sql="SHOW COLUMNS FROM {$tablename}";
        $res=$this->ci->Query_model->returnArr($sql);
        foreach($res as $key => $row){
            $columns[] = "`".$row["Field"]."`";
        }
        return $columns;
    }

    // 본인확인내역 기록
    function insert_cert_history($mb_id, $company, $method){
        $OM_TIME_YMD=$this->OM_TIME_YMD;
        $OM_TIME_HIS=$this->OM_TIME_HIS;
        $sql = " insert into shop_cert_history set mb_id = ?,cr_company = ?,cr_method = ?,cr_ip = ?,cr_date = ?,cr_time = ? ";
        $data=array($mb_id,$company,$method,$_SERVER['REMOTE_ADDR'],$OM_TIME_YMD,$OM_TIME_HIS);
        $this->ci->Query_model->returnNull($sql,$data);
    }

    // 문자열 암호화,#주문번호로 비회원 패스워드 변환 하기
    function reg_sql_password($str,$time){
        $encrypt = hash('sha256',(hash('sha256', $str).hash('sha256', $time)));
        return $encrypt;
    }

    #비밀번호 변경시 세션에 있는 아이디, 로그인 할 시에는 입력한 아이디로 등록날짜 조회후 재 변환 시키기
    function changePassword($mb_password,$id){
        $sql="select reg_time from shop_member where id=?";
        $param=array($id);
        $res=$this->ci->query_model->returnOneArr($sql,$param);
        $reg_time=$res['reg_time'];
        $encrypt = hash('sha256',(hash('sha256',$mb_password).hash('sha256', $reg_time)));
        return $encrypt;
    }

    function editor_html($id, $content, $is_dhtml_editor=true){
        $js=($this->ci->load->get_var('js')==null || $this->ci->load->get_var('js')=='true')?'true':'false';
        
        $editor_url = base_url().'plugin/editor/smarteditor2';

        $html = "";
        $html .= "<span class=\"sound_only\">웹에디터 시작</span>";
        if($is_dhtml_editor && $js=="true") {
            $html .= "\n".'<script src="'.$editor_url.'/js/HuskyEZCreator.js"></script>';
            $html .= "\n".'<script>var tw_editor_url = "'.$editor_url.'", oEditors = [];</script>';
            $html .= "\n".'<script src="'.$editor_url.'/config.js"></script>';
            $this->ci->load->vars(array('js'=>'false'));
        }
        $smarteditor_class = $is_dhtml_editor ? "smarteditor2" : "";
        $html .= "\n<textarea id=\"$id\" name=\"$id\" class=\"$smarteditor_class\" maxlength=\"65536\" style=\"width:100%\">$content</textarea>";
        $html .= "\n<span class=\"sound_only\">웹 에디터 끝</span>";
        return $html;
    }

    // textarea 로 값을 넘긴다. javascript 반드시 필요
    function get_editor_js($id, $is_dhtml_editor=true){
        if($is_dhtml_editor) {
            return "var {$id}_editor_data = oEditors.getById['{$id}'].getIR();\noEditors.getById['{$id}'].exec('UPDATE_CONTENTS_FIELD', []);\nif(jQuery.inArray(document.getElementById('{$id}').value.toLowerCase().replace(/^\s*|\s*$/g, ''), ['&nbsp;','<p>&nbsp;</p>','<p><br></p>','<div><br></div>','<p></p>','<br>','']) != -1){document.getElementById('{$id}').value='';}\n";
        } else {
            return "var {$id}_editor = document.getElementById('{$id}');\n";
        }
    }
    //  textarea 의 값이 비어 있는지 검사
    function chk_editor_js($id, $is_dhtml_editor=true){
        if($is_dhtml_editor) {
            return "if(!{$id}_editor_data || jQuery.inArray({$id}_editor_data.toLowerCase(), ['&nbsp;','<p>&nbsp;</p>','<p><br></p>','<p></p>','<br>']) != -1) { alert(\"내용을 입력해 주십시오.\"); oEditors.getById['{$id}'].exec('FOCUS'); return false; }\n";
        } else {
            return "if(!{$id}_editor.value) { alert(\"내용을 입력해 주십시오.\"); {$id}_editor.focus(); return false; }\n";
        }
    }

    // 게시판 스킨경로를 얻는다
    function get_skin_dir($skin=''){
        $result_array = array();

        $dirname = "./bbs/";
        if(is_dir($dirname)){
            $handle = opendir($dirname);
            while($file = readdir($handle)){
                if($file == "."||$file == "..") continue;
    
                if(is_dir($dirname.$file)) $result_array[] = $file;
            }
            closedir($handle);
            sort($result_array);
        };

        return $result_array;
    }

    // bid 데이터가 있는지 확인
    function get_timesale_cnt($gs_id) {
        $sql = "SELECT count(*) as cnt FROM shop_goods_timesale WHERE gs_id = ?";
        $param = array($gs_id);
        $row = $this->ci->Query_model->returnOneArr($sql,$param);

        return $row['cnt'];
    }

    // 구매건수 확인
    function get_order_cnt($gs_id) {
        $sql = "SELECT count(*) as cnt FROM (SELECT * FROM shop_order WHERE gs_id = ? AND (dan IN ('2','3','4','5')) GROUP BY od_id) AS a";
        $param = array($gs_id);
        $row = $this->ci->Query_model->returnOneArr($sql,$param);

        return $row['cnt'];
    }

    // bid 내역 조회 (관리자)
    function get_timesale_list($gs_id) {
        $param = array($gs_id);
        $sql = "SET @rownum1 := 0, @rownum2 := 0";
        $this->ci->db->query($sql);
        
        $sql2 = "SELECT a.index_no, b.purchase_date, b.od_id, b.mb_id, b.mb_name, a.normal_price, a.goods_price, a.real_price, a.difference, a.is_confirm, c.receipt_time
                FROM (
                    SELECT @rownum1 := @rownum1 + 1 AS num, shop_goods_timesale.*
                    FROM shop_goods_timesale
                    WHERE gs_id = ?
                ) AS a
                LEFT JOIN (
                    SELECT @rownum2 := @rownum2 + 1 AS num2, b.*
                    FROM shop_order_timesale AS b
                    WHERE gs_id = ? AND flag = 'buy'
                ) AS b ON a.num = b.num2
                LEFT JOIN (
                    SELECT *
                    FROM shop_order
                ) AS c ON b.od_id = c.od_id";
        $param = array($gs_id,$gs_id);
        $result = $this->ci->query_model->returnArr($sql2,$param);

        return $result;
    }

    // bid 현황 엑셀 저장
    function get_timesale_list_excel($gs_id) {
        $param = array($gs_id);
        $sql = "SET @rownum1 := 0, @rownum2 := 0";
        $this->ci->db->query($sql);
        
        $sql2 = "SELECT b.od_id, c.receipt_time, b.mb_id, b.mb_name, a.normal_price, a.goods_price, a.real_price, a.difference, d.gname, d.sb_date, d.eb_date, d.gcode
                FROM (
                    SELECT @rownum1 := @rownum1 + 1 AS num, shop_goods_timesale.*
                    FROM shop_goods_timesale
                    WHERE gs_id = ?
                ) AS a
                {$join} JOIN (
                    SELECT @rownum2 := @rownum2 + 1 AS num2, b.*
                    FROM shop_order_timesale AS b
                    WHERE gs_id = ? AND flag = 'buy'
                ) AS b ON a.num = b.num2
                LEFT JOIN (
                    SELECT *
                    FROM shop_order
                ) AS c ON b.od_id = c.od_id
                LEFT JOIN shop_goods as d
                ON a.gs_id = d.index_no";
        $param = array($gs_id,$gs_id);
        $result = $this->ci->query_model->returnArr($sql2,$param);

        return $result;
    }

    function get_timesale_list_temp($gs_id) {
        $param = array($gs_id);
        $sql = "select count(*) as cnt from shop_goods_timesale where gs_id = ?";
        $row = $this->ci->Query_model->returnOneArr($sql,$param);

        $time_data = $this->get_goods($gs_id,'sb_date, eb_date');

        $shop_goods_timesale_cnt = $row['cnt'];

        $sql = "SET @rownum1 := 0, @rownum2 := 0,  @rownum3 := 0";
        $this->ci->db->query($sql);
        
        $sql2 = "SELECT a.index_no, a.normal_price, a.goods_price, a.real_price, a.difference, a.is_confirm, c.id as mb_id, f.receipt_time 
                FROM (
                    SELECT @rownum1:=@rownum1 + 1 AS num, shop_goods_timesale.* 
                    FROM shop_goods_timesale 
                    WHERE gs_id = ?
                ) AS a 
                LEFT JOIN (
                    SELECT @rownum2:=@rownum2 + 1 AS num2, b.* 
                    FROM (
                        SELECT * 
                        FROM shop_member 
                        WHERE id NOT IN ('admin', 'paytest', 'omni') AND supply = ''
                        ORDER BY RAND() 
                        LIMIT 0, {$shop_goods_timesale_cnt}
                    ) AS b
                ) AS c ON a.num = c.num2 
                LEFT JOIN (
                    SELECT @rownum3:=@rownum3 + 1 AS num3, e.* 
                    FROM (
                        SELECT FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP('{$time_data['sb_date']}') + (RAND() * (UNIX_TIMESTAMP('{$time_data['eb_date']}') - UNIX_TIMESTAMP('{$time_data['sb_date']}'))))) AS receipt_time 
                        FROM (SELECT * FROM shop_goods_timesale LIMIT 0, {$shop_goods_timesale_cnt}) as d
                        ORDER BY receipt_time ASC 
                    ) AS e
                ) AS f ON a.num = f.num3";
        $param = array($gs_id);
        $result = $this->ci->query_model->returnArr($sql2,$param);

        return $result;
    }

    // bid 내역 조회 (뷰페이지)
    function get_timesale_list2($gs_id) {
        $sql = "SET @rownum1 := 0, @rownum2 := 0";
        $this->ci->db->query($sql);
        
        $sql2 = "SELECT a.*, b.*, c.*
                FROM (
                    SELECT @rownum1 := @rownum1 + 1 AS num, shop_goods_timesale.*
                    FROM shop_goods_timesale
                    WHERE gs_id = ?
                ) AS a
                LEFT JOIN (
                    SELECT @rownum2 := @rownum2 + 1 AS num2, b.*
                    FROM shop_order_timesale AS b
                    WHERE gs_id = ? AND flag = 'buy'
                ) AS b ON a.num = b.num2
                LEFT JOIN shop_order as c
                ON b.od_id = c.od_id
                ORDER BY a.num ASC";
        $param = array($gs_id,$gs_id);
        $result = $this->ci->query_model->returnArr($sql2,$param);

        return $result;
    }

    // bid 내역 조회 (상품 결제시)
    function get_timesale_list3($gs_id) {
        $sql = "SET @rownum1 := 0, @rownum2 := 0";
        $this->ci->db->query($sql);
        
        $sql2 = "SELECT a.index_no, a.normal_price, a.goods_price, a.real_price, a.difference
                FROM (
                    SELECT @rownum1 := @rownum1 + 1 AS num, shop_goods_timesale.*
                    FROM shop_goods_timesale
                    WHERE gs_id = ?
                ) AS a
                LEFT JOIN (
                    SELECT @rownum2 := @rownum2 + 1 AS num2, b.*
                    FROM shop_order_timesale AS b
                    WHERE gs_id = ? AND flag = 'buy'
                ) AS b ON a.num = b.num2
                LEFT JOIN shop_order as c
                ON b.od_id = c.od_id
                WHERE b.num2 IS NULL
                LIMIT 1";
        $param = array($gs_id,$gs_id);
        $result = $this->ci->query_model->returnOneArr($sql2,$param);

        return $result;
    }

    function get_timesale_info($gs_id, $mb_id) {
        $sql = "SET @rownum1 := 0, @rownum2 := 0";
        $this->ci->db->query($sql);
        
        $sql2 = "SELECT a.*, b.*, c.*
                FROM (
                    SELECT @rownum1 := @rownum1 + 1 AS num, shop_goods_timesale.*
                    FROM shop_goods_timesale
                    WHERE gs_id = ?
                ) AS a
                LEFT JOIN (
                    SELECT @rownum2 := @rownum2 + 1 AS num2, b.*
                    FROM shop_order_timesale AS b
                    WHERE gs_id = ? AND flag = 'buy'
                ) AS b ON a.num = b.num2
                LEFT JOIN shop_order as c
                ON b.od_id = c.od_id
                WHERE c.mb_id = ?
                ORDER BY a.num ASC";
        $param = array($gs_id, $gs_id, $mb_id);
        $result = $this->ci->query_model->returnArr($sql2,$param);

        return $result;
    }

    function get_menu() {
        $sql = "SELECT * 
FROM shop_menu 
WHERE `rank` IS NOT NULL AND `rank` != ''
ORDER BY `rank` ASC, index_no ASC";
        $result = $this->ci->Query_model->returnArr($sql);

        return $result;
    }

    function get_menu_info($index_no) {
        $sql = "SELECT * FROM shop_menu WHERE index_no = ? ORDER BY `rank` asc, index_no asc";
        
        return $this->ci->Query_model->returnOneArr($sql, array($index_no));
    }
}
###쿼리문 들어가 있는 것 끝

?>