<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mypage extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->library('mypage_template');
        $this->load->library('admin_template');

        $this->load->helper('global_helper');
        $this->load->helper('common_helper');
        
        $this->load->library('global_lib');
        $this->load->library('common_lib');
        $this->load->library('funcomplex_lib');
        $this->load->library('partner_lib');
        
        
        $this->load->model('query_model');
        $this->load->config('goodsinfo');
    }
    public function page(){
        $this->mypage_template->mypage_admin_head();
        $admin_snb_file=$this->load->get_var('admin_snb_file');
        $admin_snb="";
        if($admin_snb_file=="admin_snb1"){
            $admin_snb=$this->mypage_template->admin_snb1();
        }else if($admin_snb_file=="admin_snb2"){
            $admin_snb=$this->mypage_template->admin_snb2();
        }
        $code=$this->input->get('code');
        $snbTagStart="
        <div id=\"wrapper\">
            <div id=\"snb\">
                {$admin_snb}
            </div>
            <div id=\"content\">";
        $snbTagEnd="
            </div>
        </div>";
        switch ($code) {
            case 'seller_main':
                $this->seller_mainSkin($snbTagStart,$snbTagEnd);
                break;
            case 'seller_info':
                $this->seller_infoSkin($snbTagStart,$snbTagEnd);
                break;
            case 'seller_baesong':
                $this->seller_baesongSkin($snbTagStart,$snbTagEnd);
                break;
            case 'seller_goods_list':
                $this->seller_goods_listSkin($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_goods_form':
                $this->seller_goods_formSkin($snbTagStart,$snbTagEnd);
                break;
            case 'seller_goods_xls_reg':
                $this->seller_goods_xls_regSkin($snbTagStart,$snbTagEnd);
                break;
            case 'seller_goods_xls_reg_update':
                $this->seller_goods_xls_reg_updateSkin($snbTagStart,$snbTagEnd);
                break;
            case 'seller_goods_xls_mod':
                $this->seller_goods_xls_modSkin($snbTagStart,$snbTagEnd);
                break;
            case 'seller_goods_xls_mod_update':
                $this->seller_goods_xls_mod_updateSkin($snbTagStart,$snbTagEnd);
                break;
            case 'seller_goods_stock':
                $this->seller_goods_stockSkin($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_goods_optstock':
                $this->seller_goods_optstockSkin($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_goods_brand_list':
                $this->seller_goods_brand_list($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_goods_qa':
                $this->seller_goods_qa($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_goods_review':
                $this->seller_goods_review($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_odr_list':
                $this->seller_odr_list($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_odr_2':
                $this->seller_odr_2($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_odr_3':
                $this->seller_odr_3($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_odr_4':
                $this->seller_odr_4($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_odr_5':
                $this->seller_odr_5($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_odr_6':
                $this->seller_odr_6($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_odr_9':
                $this->seller_odr_9($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_odr_7':
                $this->seller_odr_7($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_odr_8':
                $this->seller_odr_8($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_odr_stats':
                $this->seller_odr_stats($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_odr_balance':
                $this->seller_odr_balance($code,$snbTagStart,$snbTagEnd);
                break;
            case 'seller_odr_account':
                $this->seller_odr_account($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_info':
                $this->partner_info($snbTagStart,$snbTagEnd);
                break;
            case 'partner_term':
                $this->partner_term($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_meta':
                $this->partner_meta($snbTagStart,$snbTagEnd);
                break;
            case 'partner_sns':
                $this->partner_sns($snbTagStart,$snbTagEnd);
                break;
            case 'partner_baesong':
                $this->partner_baesong($snbTagStart,$snbTagEnd);
                break;
            case 'partner_sms':
                $this->partner_sms($snbTagStart,$snbTagEnd);
                break;
            case 'partner_agree':
                $this->partner_agree($snbTagStart,$snbTagEnd);
                break;
            case 'partner_pg':
                $this->partner_pg($snbTagStart,$snbTagEnd);
                break;
            case 'partner_naverpay':
                $this->partner_naverpay($snbTagStart,$snbTagEnd);
                break;
            case 'partner_logo':
                $this->partner_logo($snbTagStart,$snbTagEnd);
                break;
            case 'partner_banner_list':
                $this->partner_banner_list($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_banner_form':
                $this->partner_banner_form($snbTagStart,$snbTagEnd);
                break;
            case 'partner_mbanner_list':
                $this->partner_mbanner_list($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_mbanner_form':
                $this->partner_mbanner_form($snbTagStart,$snbTagEnd);
                break;
            case 'partner_menu_form':
                $this->partner_menu_form($snbTagStart,$snbTagEnd);
                break;
            case 'partner_best_item':
                $this->partner_best_item($snbTagStart,$snbTagEnd);
                break;
            case 'partner_popup_list':
                $this->partner_popup_list($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_popup_form':
                $this->partner_popup_form($snbTagStart,$snbTagEnd);
                break;
            case 'partner_member_list':
                $this->partner_member_list($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_register_form':
                $this->partner_register_form($snbTagStart,$snbTagEnd);
                break;
            case 'partner_tree':
                $this->partner_tree($snbTagStart,$snbTagEnd);
                break;
            case 'partner_stats_day':
                $this->partner_stats_day($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_stats_month':
                $this->partner_stats_month($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_visit':
                $this->partner_visit($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_admlist':
                $this->partner_goods_admlist($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_order_admlist':
                $this->partner_order_admlist($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_category_list':
                $this->partner_category_list($snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_type':
                $this->partner_goods_type($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_plan':
                $this->partner_goods_plan($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_plan_form':
                $this->partner_goods_plan_form($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_popular_list':
                $this->partner_popular_list($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_popular_rank':
                $this->partner_popular_rank($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_list':
                $this->partner_goods_list($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_xls_reg':
                $this->partner_goods_xls_reg($snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_xls_reg_update':
                $this->partner_goods_xls_reg_update($snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_xls_mod':
                $this->partner_goods_xls_mod($snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_xls_mod_update':
                $this->partner_goods_xls_mod_update($snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_stock':
                $this->partner_goods_stock($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_form':
                $this->partner_goods_form($snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_optstock':
                $this->partner_goods_optstock($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_brand_list':
                $this->partner_goods_brand_list($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_qa':
                $this->partner_goods_qa($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_goods_review':
                $this->partner_goods_review($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_odr_list':
                $this->partner_odr_list($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_odr_1':
                $this->partner_odr_1($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_odr_2':
                $this->partner_odr_2($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_odr_3':
                $this->partner_odr_3($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_odr_4':
                $this->partner_odr_4($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_odr_5':
                $this->partner_odr_5($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_odr_6':
                $this->partner_odr_6($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_odr_9':
                $this->partner_odr_9($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_odr_7':
                $this->partner_odr_7($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_odr_8':
                $this->partner_odr_8($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_paylist':
                $this->partner_paylist($code,$snbTagStart,$snbTagEnd);
                break;
            case 'partner_payhistory':
                $this->partner_payhistory($code,$snbTagStart,$snbTagEnd);
                break;
            default:
                # code...
                break;
        }
        $this->mypage_template->mypage_admin_footer();
    }
    #공급자 관리자 화면들
        private function seller_mainSkin($snbTagStart,$snbTagEnd){
            $pg_title = "전체 거래진행 통계내역";
            $seller=$this->load->get_var('seller');
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $sql_where = " where seller_id = '{$seller['seller_code']}' ";
            $sodrr = $this->common_lib->admin_order_status_sum("{$sql_where} and dan > 1 "); // 총 주문내역
            $sodr2 = $this->common_lib->admin_order_status_sum("{$sql_where} and dan = 2 "); // 총 입금완료
            $sodr3 = $this->common_lib->admin_order_status_sum("{$sql_where} and dan = 3 "); // 총 배송준비
            $sodr4 = $this->common_lib->admin_order_status_sum("{$sql_where} and dan = 4 "); // 총 배송중
            $sodr5 = $this->common_lib->admin_order_status_sum("{$sql_where} and dan = 5 "); // 총 배송완료
            $sodr6 = $this->common_lib->admin_order_status_sum("{$sql_where} and dan = 6 "); // 총 입금전 취소
            $sodr7 = $this->common_lib->admin_order_status_sum("{$sql_where} and dan = 7 "); // 총 배송후 반품
            $sodr8 = $this->common_lib->admin_order_status_sum("{$sql_where} and dan = 8 "); // 총 배송후 교환
            $sodr9 = $this->common_lib->admin_order_status_sum("{$sql_where} and dan = 9 "); // 총 배송전 환불
            $final1 = $this->common_lib->admin_order_status_sum("{$sql_where} and dan = 5 and user_ok = 1 "); // 총 구매확정
            $final2 = $this->common_lib->admin_order_status_sum("{$sql_where} and dan = 5 and user_ok = 0 "); // 총 구매미확정

            $sql = " select * from shop_order {$sql_where} and dan > 1 group by od_id order by index_no desc limit 5 ";
            $res = $this->query_model->returnArr($sql);
            $i=0;
            $recentOdr="";
            foreach($res as $key => $row){
                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
                $recentOdr.="
                <tr class=\"tr_alignc\">
                    <td>{$row['od_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['b_name']}</td>
                    <td>{$row['cellphone']}</td>
                    <td>{$row['paymethod']}</td>
                    <td>".number_format($amount['buyprice'])."</td>
                    <td>".substr($row['od_time'],0,16)." (".get_yoil($row['od_time']).")</td>
                </tr>";
                $i++;
            }
            if($i==0)
                $recentOdr.='<tr><td colspan="7" class="empty_table">자료가 없습니다.</td></tr>';


            $noticeStr="";
            $sql = "select * from shop_board_20 order by wdate desc limit 5 ";
            $res = $this->query_model->returnArr($sql);
            $i=0;
            foreach($res as $key => $row){
                $bo_subject = cut_str($row['subject'],40);
                $bo_date = date('Y-m-d', $row['wdate']);
                $bo_href = base_url()."bbs/read?boardid=20&index_no={$row['index_no']}";
                $noticeStr.="
                <tr>
                    <td class=\"tac\">{$bo_date}</td>
                    <td class=\"tal\"><a href=\"{$bo_href}\">{$bo_subject}</a></td>
                </tr>";
                $i++;
            }
            if($i == 0)
                $noticeStr.='<tr><td colspan="2" class="empty_table">자료가 없습니다.</td></tr>';

            $qnaStr="";
            $sql = "select * from shop_board_21 order by wdate desc limit 5 ";
            $res = $this->query_model->returnArr($sql);
            $i=0;
            foreach($res as $key => $row){
                $bo_subject = cut_str($row['subject'],40);
                $bo_date = date('Y-m-d', $row['wdate']);
                $bo_href = base_url()."bbs/read?boardid=21&index_no=$row[index_no]";
                $qnaStr.="
                <tr>
                    <td class=\"tac\">{$bo_date}</td>
                    <td class=\"tal\"><a href=\"{$bo_href}\">{$bo_subject}</a></td>
                </tr>";
                $i++;
            }
            if($i == 0)
                $qnaStr.='<tr><td colspan="2" class="empty_table">자료가 없습니다.</td></tr>';

            $data=array('snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'sodrr'=>$sodrr,'sodr2'=>$sodr2,'sodr3'=>$sodr3,'sodr4'=>$sodr4,
                        'sodr5'=>$sodr5,'final1'=>$final1,'final2'=>$final2,'sodr6'=>$sodr6,'sodr7'=>$sodr7,'sodr8'=>$sodr8,'sodr9'=>$sodr9,
                        'recentOdr'=>$recentOdr,'noticeStr'=>$noticeStr,'qnaStr'=>$qnaStr,'admin_footer_sub'=>$admin_footer_sub);
            $this->load->view('mypage/seller/seller_main',$data);
        }
        private function seller_infoSkin($snbTagStart,$snbTagEnd){
            $pg_title = "업체 정보관리";
            $seller=$this->load->get_var('seller');
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $help=array('page'=>'');
            $help['page']=help('http://를 포함하여 입력하세요');
            $data=array('snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub,'seller'=>$seller,'help'=>$help);
            $this->load->view('mypage/seller/info',$data);
        }
        private function seller_baesongSkin($snbTagStart,$snbTagEnd){
            $pg_title = "업체 배송정책";
            $seller=$this->load->get_var('seller');
            $config=$this->load->get_var('config');
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            
            $spl_sorts = explode(",", $config['delivery_company']);
            $spl_count = count($spl_sorts);
            $i = 0;
            $deliverLinkstr="";
            do {
                $seq = explode('|', trim($spl_sorts[$i]));
                $name = isset($seq[0]) ? $seq[0] : '';
                $url  = isset($seq[1]) ? $seq[1] : '';
                $deliverLinkstr .= "
                <tr>
                    <td><input type=\"text\" value=\"{$name}\" class=\"frm_input wfull\"></td>
                    <td><input type=\"text\" value=\"{$url}\" class=\"frm_input wfull\"></td>
                </tr>";
                $i++;
            } while($i < $spl_count);
            $help=array('deliver'=>"");
            $help['deliver']=help('※ 배송업체등록은 본사에서만 가능하며 추가하실 업체는 본사로 문의주시기 바랍니다.');
            $deliverChk=array('','','','','');
            $deliverChk[1]=get_checked($seller['delivery_method'], "1");
            $deliverChk[2]=get_checked($seller['delivery_method'], "2");
            $deliverChk[3]=get_checked($seller['delivery_method'], "3");
            $deliverChk[4]=get_checked($seller['delivery_method'], "4");
            
            $baseEdit=$this->global_lib->editor_html('baesong_cont1', $this->funcomplex_lib->get_text($seller['baesong_cont1'], 0));       
            $baseMbEdit=$this->global_lib->editor_html('baesong_cont2', $this->funcomplex_lib->get_text($seller['baesong_cont2'], 0));

            $bassonCont1Js=$this->global_lib->get_editor_js('baesong_cont1');
            $bassonCont2Js=$this->global_lib->get_editor_js('baesong_cont2');
            $data=array('snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub,'deliverLinkstr'=>$deliverLinkstr,
                        'help'=>$help,'deliverChk'=>$deliverChk,'baseEdit'=>$baseEdit,'baseMbEdit'=>$baseMbEdit,'bassonCont1Js'=>$bassonCont1Js,
                        'bassonCont2Js'=>$bassonCont2Js);
            $this->load->view('mypage/seller/basesong',$data);
        }
        private function seller_goods_listSkin($code,$snbTagStart,$snbTagEnd){
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $pg_title = "전체 상품관리";
            $seller=$this->load->get_var('seller');
            $config=$this->load->get_var('config');
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();

            $jqueryUiDatePick=$this->admin_template->datePicker();

            $qstr=$this->load->get_var('qstr');
            $config=$this->load->get_var('config');

            $fr_date=$this->input->get('fr_date');
            $to_date=$this->input->get('to_date');
            $sel_ca1=$this->input->get('sel_ca1');
            $sel_ca2=$this->input->get('sel_ca2');
            $sel_ca3=$this->input->get('sel_ca3');
            $sel_ca4=$this->input->get('sel_ca4');
            $sel_ca5=$this->input->get('sel_ca5');
            $q_date_field=$this->input->get('q_date_field');
            $q_brand=$this->input->get('q_brand');
            $q_zone=$this->input->get('q_zone');
            $q_stock_field=$this->input->get('q_stock_field');
            $fr_stock=$this->input->get('fr_stock');
            $to_stock=$this->input->get('to_stock');
            $q_price_field=$this->input->get('q_price_field');
            $fr_price=$this->input->get('fr_price');
            $to_price=$this->input->get('to_price');
            $q_isopen=$this->input->get('q_isopen');
            $q_option=$this->input->get('q_option');
            $q_supply=$this->input->get('q_supply');
            $q_notax=$this->input->get('q_notax');
            $q_state=$this->input->get('q_state');
            $page=$this->input->get('page');
            $stx=$this->input->get('stx');
            $sfl=$this->input->get('sfl');
            $sca=$this->input->get('sca');
            $orderby=$this->input->get('orderby');

            $filed=$this->input->get('filed');
            $filed=$this->db->escape_str($filed);
            

            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

            if($sel_ca1) $sca = $sel_ca1;
            if($sel_ca2) $sca = $sel_ca2;
            if($sel_ca3) $sca = $sel_ca3;
            if($sel_ca4) $sca = $sel_ca4;
            if($sel_ca5) $sca = $sel_ca5;

            if(isset($sel_ca1))			$qstr .= "&sel_ca1=$sel_ca1";
            if(isset($sel_ca2))			$qstr .= "&sel_ca2=$sel_ca2";
            if(isset($sel_ca3))			$qstr .= "&sel_ca3=$sel_ca3";
            if(isset($sel_ca4))			$qstr .= "&sel_ca4=$sel_ca4";
            if(isset($sel_ca5))			$qstr .= "&sel_ca5=$sel_ca5";
            if(isset($q_date_field))	$qstr .= "&q_date_field=$q_date_field";
            if(isset($q_brand))			$qstr .= "&q_brand=$q_brand";
            if(isset($q_zone))			$qstr .= "&q_zone=$q_zone";
            if(isset($q_stock_field))	$qstr .= "&q_stock_field=$q_stock_field";
            if(isset($fr_stock))		$qstr .= "&fr_stock=$fr_stock";
            if(isset($to_stock))		$qstr .= "&to_stock=$to_stock";
            if(isset($q_price_field))	$qstr .= "&q_price_field=$q_price_field";
            if(isset($fr_price))		$qstr .= "&fr_price=$fr_price";
            if(isset($to_price))		$qstr .= "&to_price=$to_price";
            if(isset($q_isopen))		$qstr .= "&q_isopen=$q_isopen";
            if(isset($q_option))		$qstr .= "&q_option=$q_option";
            if(isset($q_supply))		$qstr .= "&q_supply=$q_supply";
            if(isset($q_notax))			$qstr .= "&q_notax=$q_notax";
            if(isset($q_state))			$qstr .= "&q_state=$q_state";

            $query_string = "code=$code$qstr";
            $q1 = $query_string;
            $q2 = $query_string."&page=$page";

            $sql_common = " from shop_goods ";
            $sql_search = " where mb_id = '{$seller['seller_code']}' ";

            if($sca) {
                $sql_search .= " and (ca_id like '$sca%' or ca_id2 like '$sca%' or ca_id3 like '$sca%') ";
            }

            // 검색어
            if($stx) {
                switch($sfl) {
                    case "gname" :
                    case "explan" :
                    case "maker" :
                    case "origin" :
                    case "model" :
                        $sql_search .= " and $sfl like '%$stx%' ";
                        break;
                    default :
                        $sql_search .= " and $sfl like '$stx%' ";
                        break;
                }
            }

            // 기간검색
            if($fr_date && $to_date)
                $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
            else if($fr_date && !$to_date)
                $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
            else if(!$fr_date && $to_date)
                $sql_search .= " and $q_date_field between '$to_date 00:00:00' and '$to_date 23:59:59' ";

            // 브랜드
            if(isset($q_brand) && $q_brand)
                $sql_search .= " and brand_uid = '$q_brand' ";

            // 배송가능 지역
            if(isset($q_zone) && $q_zone)
                $sql_search .= " and zone = '$q_zone' ";

            // 상품재고
            if($fr_stock && $to_stock)
                $sql_search .= " and $q_stock_field between '$fr_stock' and '$to_stock' ";

            // 상품가격
            if($fr_price && $to_price)
                $sql_search .= " and $q_price_field between '$fr_price' and '$to_price' ";

            // 판매여부
            if(isset($q_isopen) && is_numeric($q_isopen))
                $sql_search .= " and isopen='$q_isopen' ";

            // 과세유형
            if(isset($q_notax) && is_numeric($q_notax))
                $sql_search .= " and notax = '$q_notax' ";

            // 상품 필수옵션
            if(isset($q_option) && is_numeric($q_option)) {
                if($q_option)
                    $sql_search .= " and opt_subject <> '' ";
                else
                    $sql_search .= " and opt_subject = '' ";
            }

            // 상품 추가옵션
            if(isset($q_supply) && is_numeric($q_supply)) {
                if($q_supply)
                    $sql_search .= " and spl_subject <> '' ";
                else
                    $sql_search .= " and spl_subject = '' ";
            }

            // 승인상태
            if(isset($q_state) && is_numeric($q_state))
                $sql_search .= " and shop_state = '$q_state' ";

            if(!$orderby) {
                $filed = "index_no";
                $sod = "desc";
            } else {
                $sod = $orderby;
            }

            $sql_order = " order by $filed $sod ";

            // 테이블의 전체 레코드수만 얻음
            $sql = "select count(*) as cnt $sql_common $sql_search ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            if(isset($_SESSION['ss_page_rows']))
                $page_rows = $_SESSION['ss_page_rows'];
            else
                $page_rows = 30;

            $rows = $page_rows;
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);

            $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
            $result = $this->query_model->returnArr($sql);
            $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택상품복사\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"".base_url()."MypageProcess/seller_goods_list_excel?flag=normal&$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장(열린장터)</a>
            <a href=\"".base_url()."MypageProcess/seller_goods_list_excel?flag=timesale&$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장(함께장터)</a>
            <a href=\"".base_url()."MypageProcess/seller_goods_list_excel?flag=all&$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장(전체)</a>
            <a href=\"" . base_url() . "data/download/한류입점사 상품등록 메뉴얼.pdf\" download class=\"btn_lsmall\"> 상품등록 설명서</a>
            <a href=\"".base_url()."mypage/page?code=seller_goods_form&type=normal\" class=\"fr btn_lsmall red marl5\"><i class=\"ionicons ion-android-add\"></i> 열린장터 상품등록</a>
            <a href=\"".base_url()."mypage/page?code=seller_goods_form&type=timesale\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 함께장터 상품등록</a>";

            $sflArr=array('','','','','','');
            $sflArr[0]=option_selected('gname', $sfl, '상품명');
            $sflArr[1]=option_selected('gcode', $sfl, '상품코드');
            $sflArr[2]=option_selected('maker', $sfl, '제조사');
            $sflArr[3]=option_selected('origin', $sfl, '원산지');
            $sflArr[4]=option_selected('model', $sfl, '모델명');
            $sflArr[5]=option_selected('explan', $sfl, '짧은설명');

            $scaSelect=array('','','','','');
            $scaSelect[0]=$this->funcomplex_lib->get_category_select_1('sel_ca1', $sca);
            $scaSelect[1]=$this->funcomplex_lib->get_category_select_2('sel_ca2', $sca);
            $scaSelect[2]=$this->funcomplex_lib->get_category_select_3('sel_ca3', $sca);
            $scaSelect[3]=$this->funcomplex_lib->get_category_select_4('sel_ca4', $sca);
            $scaSelect[4]=$this->funcomplex_lib->get_category_select_5('sel_ca5', $sca);

            $updateTimeTag=option_selected('update_time', $q_date_field, "최근수정일");
            $regTimeTag=option_selected('reg_time', $q_date_field, "최초등록일");
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);

            $brandStr="";
            $sql = "select * from shop_brand where br_user_yes='0' order by br_name asc ";
            $res = $this->query_model->returnArr($sql);
            $brandStr.=option_selected('', $q_brand, '전체');
            foreach($res as $key => $row){
                $brandStr.=option_selected($row['br_id'], $q_brand, $row['br_name']);
            }

            $q_zoneStr=array('','','','','','','','','','');
            $q_zoneStr[0]=option_selected('',  $q_zone, '전체');
            $q_zoneStr[1]=option_selected('전국', $q_zone, '전국');
            $q_zoneStr[2]=option_selected('강원도', $q_zone, '강원도');
            $q_zoneStr[3]=option_selected('경기도', $q_zone, '경기도');
            $q_zoneStr[4]=option_selected('경상도', $q_zone, '경상도');
            $q_zoneStr[5]=option_selected('서울/경기도', $q_zone, '서울/경기도');
            $q_zoneStr[6]=option_selected('서울특별시', $q_zone, '서울특별시');
            $q_zoneStr[7]=option_selected('전라도', $q_zone, '전라도');
            $q_zoneStr[8]=option_selected('제주도', $q_zone, '제주도');
            $q_zoneStr[9]=option_selected('충청도', $q_zone, '충청도');

            $stock_qtyTag=option_selected('stock_qty', $q_stock_field, "재고수량");
            $noti_qtyTag=option_selected('noti_qty', $q_stock_field, "통보수량");

            $price_fieldArr=array('','','','');
            $price_fieldArr[0]=option_selected('goods_price', $q_price_field, "판매가격"); 
            $price_fieldArr[1]=option_selected('supply_price', $q_price_field, "공급가격");
            $price_fieldArr[2]=option_selected('normal_price', $q_price_field, "시중가격");
            $price_fieldArr[3]=option_selected('gpoint', $q_price_field, "포인트");
            $q_isopenArr=array('','','','','');
            $q_isopenArr[0]=radio_checked('q_isopen', $q_isopen,  '', '전체');
            $q_isopenArr[1]=radio_checked('q_isopen', $q_isopen, '1', '진열');
            $q_isopenArr[2]=radio_checked('q_isopen', $q_isopen, '2', '품절');
            $q_isopenArr[3]=radio_checked('q_isopen', $q_isopen, '3', '단종');
            $q_isopenArr[4]=radio_checked('q_isopen', $q_isopen, '4', '중지');

            $q_optionArr=array('all'=>'','use'=>'','unuse'=>'');
            $q_optionArr['all']=radio_checked('q_option', $q_option,  '', '전체');
            $q_optionArr['use']=radio_checked('q_option', $q_option, '1', '사용');
            $q_optionArr['unuse']=radio_checked('q_option', $q_option, '0', '미사용');

            $q_notaxArr=array('all'=>'','1'=>'','0'=>'');
            $q_notaxArr['all']=radio_checked('q_notax', $q_notax,  '', '전체');
            $q_notaxArr['1']=radio_checked('q_notax', $q_notax, '1', '과세');
            $q_notaxArr['0']=radio_checked('q_notax', $q_notax, '0', '비과세');

            $q_supplyArr=array('all'=>'','1'=>'','0'=>'');
            $q_supplyArr['all']=radio_checked('q_supply', $q_supply,  '', '전체');
            $q_supplyArr['1']=radio_checked('q_supply', $q_supply, '1', '사용');
            $q_supplyArr['0']=radio_checked('q_supply', $q_supply, '0', '미사용');

            $q_stateArr=array('all'=>'','0'=>'','1'=>'','2'=>'');
            $q_stateArr['all']=radio_checked('q_state', $q_state,  '', '전체');
            $q_stateArr['0']=radio_checked('q_state', $q_state, '0', '승인'); 
            $q_stateArr['1']=radio_checked('q_state', $q_state, '1', '대기');
            $q_stateArr['2']=radio_checked('q_state', $q_state, '2', '보류'); 

            $page_rowsOpt=array('30'=>'','50'=>'','100'=>'','150'=>'');
            $page_rowsOpt['30']=option_selected('30',  $page_rows, '30줄 정렬');
            $page_rowsOpt['50']=option_selected('50',  $page_rows, '50줄 정렬');
            $page_rowsOpt['100']=option_selected('100',  $page_rows, '100줄 정렬');
            $page_rowsOpt['150']=option_selected('150',  $page_rows, '150줄 정렬');

            $gcodeStr=$this->gvcomplex_lib->subject_sort_link('gcode',$q2);
            $gnameStr=$this->gvcomplex_lib->subject_sort_link('gname',$q2);
            $regTimeStr=$this->gvcomplex_lib->subject_sort_link('reg_time',$q2);
            $isopenStr=$this->gvcomplex_lib->subject_sort_link('isopen',$q2);

            $shop_stateStr=$this->gvcomplex_lib->subject_sort_link('shop_state',$q2);
            $updateTimeStr=$this->gvcomplex_lib->subject_sort_link('update_time',$q2);
            $stockQtyStr=$this->gvcomplex_lib->subject_sort_link('stock_qty',$q2);
            $normalPriceStr=$this->gvcomplex_lib->subject_sort_link('normal_price',$q2);
            $supplyPriceStr=$this->gvcomplex_lib->subject_sort_link('supply_price',$q2);
            $goodPriceStr=$this->gvcomplex_lib->subject_sort_link('goods_price',$q2);
            $gpointStr=$this->gvcomplex_lib->subject_sort_link('gpoint',$q2);

            $i=0;
            $goods_listTable="";
            $gw_isopen=$this->config->item('gw_isopen');
            $gw_state=$this->config->item('gw_state');
            foreach($result as $key => $row){
                $gs_id = $row['index_no'];
        
                if($row['stock_mod'])
                    $stockQty = number_format($row['stock_qty']);
                else
                    $stockQty = '<span class="txt_false">무제한</span>';
        
                $bg = 'list'.($i%2);
                $goods_listTable.="
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">".$num--."</td>
                    <td rowspan=\"2\"><a href=\"".base_url()."shop/view?index_no={$gs_id}\" target=\"_blank\">".$this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40)."</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">".$this->funcomplex_lib->get_text($row['gname'])."</td>
                    <td>".substr($row['reg_time'],2,8)."</td>
                    <td>".(isset($gw_isopen[$row['isopen']])?$gw_isopen[$row['isopen']]:'')."</td>
                    <td rowspan=\"2\" class=\"tar\">".number_format($row['normal_price'])."</td>
                    <td rowspan=\"2\" class=\"tar\">".number_format($row['supply_price'])."</td>
                    <td rowspan=\"2\" class=\"tar\">".number_format($row['goods_price'])."</td>
                    <td rowspan=\"2\" class=\"tar\"></td>
                    <td rowspan=\"2\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}&type={$row['type']}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">".$gw_state[$row['shop_state']]."</td>
                    <td class=\"tal txt_succeed\">".$this->gvcomplex_lib->get_seller_name($row['mb_id'])."</td>
                    <td class=\"tal txt_succeed\">".$this->funcomplex_lib->get_cgy_info($row)."</td>
                    <td class=\"fc_00f\">".substr($row['update_time'],2,8)."</td>
                    <td>{$stockQty}</td>
                </tr>";
                $i++;
            }
            if($i==0)
                $goods_listTable.= '<tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';
                
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $data=array('snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub,
                        'btn_frmline'=>$btn_frmline,'code'=>$code,'sflArr'=>$sflArr,'stx'=>$stx,'scaSelect'=>$scaSelect,'updateTimeTag'=>$updateTimeTag,
                        'regTimeTag'=>$regTimeTag,'get_search_date'=>$get_search_date,'brandStr'=>$brandStr,'q_zoneStr'=>$q_zoneStr,
                        'stock_qtyTag'=>$stock_qtyTag,'noti_qtyTag'=>$noti_qtyTag,'fr_stock'=>$fr_stock,'to_stock'=>$to_stock,'price_fieldArr'=>$price_fieldArr,
                        'fr_price'=>$fr_price,'to_price'=>$to_price,'q_isopenArr'=>$q_isopenArr,'q_optionArr'=>$q_optionArr,'q_notaxArr'=>$q_notaxArr,'q_supplyArr'=>$q_supplyArr,
                        'q_stateArr'=>$q_stateArr,'q1'=>$q1,'page'=>$page,'total_count'=>$total_count,'requestUrl'=>$requestUrl,'page_rowsOpt'=>$page_rowsOpt,
                        'gcodeStr'=>$gcodeStr,'gnameStr'=>$gnameStr,'regTimeStr'=>$regTimeStr,'isopenStr'=>$isopenStr,
                        'shop_stateStr'=>$shop_stateStr,'updateTimeStr'=>$updateTimeStr,'stockQtyStr'=>$stockQtyStr,
                        'normalPriceStr'=>$normalPriceStr,'supplyPriceStr'=>$supplyPriceStr,'goodPriceStr'=>$goodPriceStr,
                        'gpointStr'=>$gpointStr,'goods_listTable'=>$goods_listTable,'pagination'=>$pagination,'jqueryUiDatePick'=>$jqueryUiDatePick);
            $this->load->view('mypage/seller/goods_list',$data);
        }
        private function seller_goods_formSkin($snbTagStart,$snbTagEnd){
            $seller=$this->load->get_var('seller');
            $config=$this->load->get_var('config');
            $default=$this->load->get_var('default');
            $qstr=$this->load->get_var('qstr');
            $type=$this->input->get('type') ?? "normal";
            
            $w=$this->input->get('w');
            $pg_title = ($w=="u")?"상품 정보수정":"신규 상품등록";
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $gs=array();
            $readonly="";
            if($w == "") {
                $gs['mb_id']		= $seller['seller_code'];
                $gs['mb_name'] 		= $seller['company_name'];
                $gs['gcode']		= time();
                $gs['sc_type']		= 0; // 배송비 유형	0:공통설정, 1:무료배송, 2:조건부 무료배송, 3:유료배송
                $gs['sc_method']	= 0; // 배송비 결제	0:선불, 1:착불, 2:사용자선택
                $gs['stock_mod']	= 0;
                $gs['noti_qty']		= 999;
                $gs['simg_type']	= 0;
                $gs['isopen']		= 1;
                $gs['notax']		= 1;
                $gs['zone']			= '전국';
            } else if($w == "u") {
                $gs_id=$this->input->get('gs_id');
                $gs = $this->global_lib->get_goods($gs_id);
                if(!$gs)
                    alert("존재하지 않은 상품 입니다.");
            
                $readonly = " readonly style='background-color:#ddd;'";
            
                if(is_null_time($gs['sb_date'])) $gs['sb_date'] = '';
                if(is_null_time($gs['eb_date'])) $gs['eb_date'] = '';
                // 다른 협력사 상품을 수정 못하도록 추가 2019-10-31 -dladmstjr12
                if($gs['mb_id'] != $seller['seller_code']){
                    alert("잘못된 요청입니다.");
                }
            }
            $gs['brand_uid']=isset($gs['brand_uid'])?$gs['brand_uid']:"";
            $gs['opt_subject']=isset($gs['opt_subject'])?$gs['opt_subject']:"";
            $gs['index_no']=isset($gs['index_no'])?$gs['index_no']:"";
            $gs['spl_subject']=isset($gs['spl_subject'])?$gs['spl_subject']:"";
            $gs['buy_level']=isset($gs['buy_level'])?$gs['buy_level']:"";
            $gs['buy_only']=isset($gs['buy_only'])?$gs['buy_only']:"";
            $gs['sc_each_use']=isset($gs['sc_each_use'])?$gs['sc_each_use']:"";
            $gs['info_gubun']=isset($gs['info_gubun'])?$gs['info_gubun']:"";
            $gs['memo']=isset($gs['memo'])?$gs['memo']:"";
            $gs['simg1']=isset($gs['simg1'])?$gs['simg1']:"";
            $gs['simg2']=isset($gs['simg2'])?$gs['simg2']:"";
            $gs['simg3']=isset($gs['simg3'])?$gs['simg3']:"";
            $gs['simg4']=isset($gs['simg4'])?$gs['simg4']:"";
            $gs['simg5']=isset($gs['simg5'])?$gs['simg5']:"";
            $gs['simg6']=isset($gs['simg6'])?$gs['simg6']:"";
            $gs['gname']=isset($gs['gname'])?$gs['gname']:"";
            $gs['explan']=isset($gs['explan'])?$gs['explan']:"";
            $gs['keywords']=isset($gs['keywords'])?$gs['keywords']:"";
            $gs['model']=isset($gs['model'])?$gs['model']:"";
            $gs['origin']=isset($gs['origin'])?$gs['origin']:"";
            $gs['maker']=isset($gs['maker'])?$gs['maker']:"";
            $gs['normal_price']=isset($gs['normal_price'])?$gs['normal_price']:0;
            $gs['supply_price']=isset($gs['supply_price'])?$gs['supply_price']:0;
            $gs['calculate_price']=isset($gs['calculate_price'])?$gs['calculate_price']:0;
            $gs['goods_price']=isset($gs['goods_price'])?$gs['goods_price']:0;
            $gs['gpoint']=isset($gs['gpoint'])?$gs['gpoint']:0;
            $gs['price_msg']=isset($gs['price_msg'])?$gs['price_msg']:"";
            $gs['stock_qty']=isset($gs['stock_qty'])?$gs['stock_qty']:0;
            // $gs['safety_stock_qty']=isset($gs['safety_stock_qty'])?$gs['safety_stock_qty']:0;
            $gs['goods_total_price']=isset($gs['goods_total_price'])?$gs['goods_total_price']:0;
            $gs['odr_min']=isset($gs['odr_min'])?$gs['odr_min']:1;
            $gs['odr_max']=isset($gs['odr_max'])?$gs['odr_max']:1;
            $gs['sb_date']=isset($gs['sb_date'])?$gs['sb_date']:"";
            $gs['eb_date']=isset($gs['eb_date'])?$gs['eb_date']:"";
            $gs['zone_msg']=isset($gs['zone_msg'])?$gs['zone_msg']:"";
            $gs['delivery_period']=isset($gs['delivery_period'])?$gs['delivery_period']:"";
            $gs['sc_each_use']=isset($gs['sc_each_use'])?$gs['sc_each_use']:"";
            $gs['admin_memo']=isset($gs['admin_memo'])?$gs['admin_memo']:"";
            $gs['sc_amt']=isset($gs['sc_amt'])?$gs['sc_amt']:0;
            $gs['sc_minimum']=isset($gs['sc_minimum'])?$gs['sc_minimum']:0;
            $gs['is_point']=isset($gs['is_point'])?$gs['is_point']:1;
            
            $jqueryUiDatePick=$this->admin_template->datePicker();
            $sel_ca1=$this->input->get('sel_ca1');
            $sel_ca2=$this->input->get('sel_ca2');
            $sel_ca3=$this->input->get('sel_ca3');
            $sel_ca4=$this->input->get('sel_ca4');
            $sel_ca5=$this->input->get('sel_ca5');
            $q_date_field=$this->input->get('q_date_field');
            $q_brand=$this->input->get('q_brand');
            $q_zone=$this->input->get('q_zone');
            $q_stock_field=$this->input->get('q_stock_field');
            $fr_stock=$this->input->get('fr_stock');
            $to_stock=$this->input->get('to_stock');
            $q_price_field=$this->input->get('q_price_field');
            $fr_price=$this->input->get('fr_price');
            $to_price=$this->input->get('to_price');
            $q_isopen=$this->input->get('q_isopen');
            $q_option=$this->input->get('q_option');
            $q_supply=$this->input->get('q_supply');
            $q_notax=$this->input->get('q_notax');
            $q_state=$this->input->get('q_state');
            $gs_id=$this->input->get('gs_id');
            $page=$this->input->get('page');
            $bak=$this->input->get('bak');
            
            
            if(isset($sel_ca1))			$qstr .= "&sel_ca1=$sel_ca1";
            if(isset($sel_ca2))			$qstr .= "&sel_ca2=$sel_ca2";
            if(isset($sel_ca3))			$qstr .= "&sel_ca3=$sel_ca3";
            if(isset($sel_ca4))			$qstr .= "&sel_ca4=$sel_ca4";
            if(isset($sel_ca5))			$qstr .= "&sel_ca5=$sel_ca5";
            if(isset($q_date_field))	$qstr .= "&q_date_field=$q_date_field";
            if(isset($q_brand))			$qstr .= "&q_brand=$q_brand";
            if(isset($q_zone))			$qstr .= "&q_zone=$q_zone";
            if(isset($q_stock_field))	$qstr .= "&q_stock_field=$q_stock_field";
            if(isset($fr_stock))		$qstr .= "&fr_stock=$fr_stock";
            if(isset($to_stock))		$qstr .= "&to_stock=$to_stock";
            if(isset($q_price_field))	$qstr .= "&q_price_field=$q_price_field";
            if(isset($fr_price))		$qstr .= "&fr_price=$fr_price";
            if(isset($to_price))		$qstr .= "&to_price=$to_price";
            if(isset($q_isopen))		$qstr .= "&q_isopen=$q_isopen";
            if(isset($q_option))		$qstr .= "&q_option=$q_option";
            if(isset($q_supply))		$qstr .= "&q_supply=$q_supply";
            if(isset($q_notax))			$qstr .= "&q_notax=$q_notax";
            if(isset($q_state))			$qstr .= "&q_state=$q_state";

            $frm_submit = '<div class="btn_confirm">
                <input type="submit" value="저장" class="btn_large" accesskey="s">';
            if($w == "u" && $bak) {
                $frm_submit .= '<a href="'.base_url().'mypage/page?code='.$bak.$qstr.'&page='.$page.'" class="btn_large bx-white">목록</a>';
                $frm_submit .= '<a href="'.base_url().'mypage/page?code=seller_goods_form" class="btn_large bx-red">추가</a>';
            }
            $frm_submit .= '</div>';

            $pg_anchor = "
            <ul class=\"anchor\">
                <li><a href=\"#anc_sitfrm_cate\">카테고리</a></li>
                <li><a href=\"#anc_sitfrm_ini\">기본정보</a></li>
                <li><a href=\"#anc_sitfrm_option\">옵션정보</a></li>
                <li><a href=\"#anc_sitfrm_cost\">가격 및 재고</a></li>
                <li><a href=\"#anc_sitfrm_sendcost\">배송비</a></li>
                <li><a href=\"#anc_sitfrm_compact\">요약정보</a></li>
                <li><a href=\"#anc_sitfrm_relation\">관련상품</a></li>
                <li><a href=\"#anc_sitfrm_img\">상품이미지</a></li>
            </ul>";
            $sel_caTag=array('','','','','','');
            $sel_caTag[1]=$this->funcomplex_lib->get_category_select_1('sel_ca1', '', ' size="10" class="multiple-select"');
            $sel_caTag[2]=$this->funcomplex_lib->get_category_select_2('sel_ca2', '', ' size="10" class="multiple-select"');
            $sel_caTag[3]=$this->funcomplex_lib->get_category_select_3('sel_ca3', '', ' size="10" class="multiple-select"');
            $sel_caTag[4]=$this->funcomplex_lib->get_category_select_4('sel_ca4', '', ' size="10" class="multiple-select"');
            $sel_caTag[5]=$this->funcomplex_lib->get_category_select_5('sel_ca5', '', ' size="10" class="multiple-select"');

            $sel_cateOpt="";
            $recentChange="";
            if($w == "u") {
                $recentChange="
                <div class=\"local_desc02 local_desc\">
                    <p>상품 등록일시 : <b>{$gs['reg_time']}</b>, 최근 수정일시 : <b>{$gs['update_time']}</b></p>
                </div>";
                if($ca_id1 = $this->common_lib->adm_category_navi($gs['ca_id']))
                    $sel_cateOpt.='<option value="'.$gs['ca_id'].'">' .$ca_id1. '</option>';
                if($ca_id2 = $this->common_lib->adm_category_navi($gs['ca_id2']))
                    $sel_cateOpt.='<option value="'.$gs['ca_id2'].'">'.$ca_id2.'</option>';
                if($ca_id3 = $this->common_lib->adm_category_navi($gs['ca_id3']))
                    $sel_cateOpt.='<option value="'.$gs['ca_id3'].'">'.$ca_id3.'</option>';
            }
            $preview="";
            if($w == "u") { 
                $preview="<a href=\"".base_url()."shop/view?index_no={$gs_id}\" target=\"_blank\" class=\"btn_small\">미리보기</a>";
            }
            $help['keyword']=help('단어와 단어 사이는 콤마 ( , ) 로 구분하여 여러개를 입력할 수 있습니다. 예시) 빨강, 노랑, 파랑');
            $sql = "select * from shop_brand where br_user_yes = 0 order by br_name asc";
            $result = $this->query_model->returnArr($sql);
            $brandStr="";
            foreach($result as $key => $row){
                $brandStr.=option_selected($row['br_id'], $gs['brand_uid'], $row['br_name']);
            }
            $notax=array('1'=>'','0'=>'');
            $notax['1']=radio_checked('notax', $gs['notax'], '1', '과세');
            $notax['0']=radio_checked('notax', $gs['notax'], '0', '면세');

            $isopenArr=array('','','','');
            $isopenArr[0]=radio_checked('isopen', $gs['isopen'], '1', '진열');
            $isopenArr[1]=radio_checked('isopen', $gs['isopen'], '2', '품절'); 
            $isopenArr[2]=radio_checked('isopen', $gs['isopen'], '3', '단종');
            $isopenArr[3]=radio_checked('isopen', $gs['isopen'], '4', '중지');
            $isopenArr[4]=radio_checked('isopen', $gs['isopen'], '5', '판매종료');
            $opt_subject = explode(',', $gs['opt_subject']);
            $opt_subject[1]=isset($opt_subject[1])?$opt_subject[1]:"";
            $opt_subject[2]=isset($opt_subject[2])?$opt_subject[2]:"";

            $goods_optionObj=$this->admin_template->goods_options($gs);
            $goods_option=$goods_optionObj['tag'];
            $goods_splObj=$this->admin_template->goods_spl($gs);
            
            $optConfigJs="";
            $po_run=$goods_optionObj['po_run'];
            if($gs['index_no'] && $po_run) {
                $optConfigJs="
                //옵션항목설정
                var arr_opt1 = new Array();
                var arr_opt2 = new Array();
                var arr_opt3 = new Array();
                var opt1 = opt2 = opt3 = '';
                var opt_val;

                $(\".opt-cell\").each(function() {
                    opt_val = $(this).text().split(\" > \");
                    opt1 = $.trim(opt_val[0]);
                    opt2 = $.trim(opt_val[1]);
                    opt3 = $.trim(opt_val[2]);

                    if(opt1 && $.inArray(opt1, arr_opt1) == -1)
                        arr_opt1.push(opt1);

                    if(opt2 && $.inArray(opt2, arr_opt2) == -1)
                        arr_opt2.push(opt2);

                    if(opt3 && $.inArray(opt3, arr_opt3) == -1)
                        arr_opt3.push(opt3);
                });

                $(\"input[name=opt1]\").val(arr_opt1.join());
                $(\"input[name=opt2]\").val(arr_opt2.join());
                $(\"input[name=opt3]\").val(arr_opt3.join());";
            }
            $spl_subject = explode(',', $gs['spl_subject']);
            $spl_count = count($spl_subject);
            $i = 0;
            $addOptStr="";
            do {
                $seq = $i + 1;
                $addOptStr.="
                <tr>
                    <th scope=\"row\">추가{$seq}</th>
                    <td><input type=\"text\" name=\"spl_subject[]\" value=\"{$spl_subject[$i]}\" id=\"spl_subject_{$seq}\" class=\"frm_input wfull\"></td>
                    <th scope=\"row\">추가{$seq} 항목</th>
                    <td><input type=\"text\" name=\"spl[]\" id=\"spl_item_{$seq}\" value=\"\" class=\"frm_input wfull\"></td>
                    <td class=\"tac\">";
                    if($i > 0)
                        $addOptStr.='<button type="button" id="del_supply_row" class="btn_ssmall bx-white">삭제</button>';
                    $addOptStr.="</td>
                </tr>";
                $i++;
            } while($i < $spl_count);
            $addOptStr2="";
            $ps_run=$this->input->get('ps_run');
            if($gs['index_no'] && $ps_run) {
                $addOptStr2="
                // 추가옵션의 항목 설정
                var arr_subj = new Array();
                var subj, spl;

                $(\"input[name='spl_subject[]']\").each(function() {
                    subj = $.trim($(this).val());
                    if(subj && $.inArray(subj, arr_subj) == -1)
                        arr_subj.push(subj);
                });

                for(i=0; i<arr_subj.length; i++) {
                    var arr_spl = new Array();
                    $(\".spl-subject-cell\").each(function(index) {
                        subj = $.trim($(this).text());
                        if(subj == arr_subj[i]) {
                            spl = $.trim($(\".spl-cell:eq(\"+index+\")\").text());
                            arr_spl.push(spl);
                        }
                    });

                    $(\"input[name='spl[]']:eq(\"+i+\")\").val(arr_spl.join());
                }";
            }
            $stockMod=array('','');
            $stockMod[0]=get_checked('0', $gs['stock_mod']);
            $stockMod[1]=get_checked('1', $gs['stock_mod']);
            $get_goods_level_select=$this->common_lib->get_goods_level_select('buy_level', $gs['buy_level']);
            $buy_onlyStr=get_checked('1', $gs['buy_only']);

            $gsScType=array('','','','');
            $gsScType[0]=option_selected('0', $gs['sc_type'], '공통설정');
            $gsScType[1]=option_selected('1', $gs['sc_type'], '무료배송');
            $gsScType[2]=option_selected('2', $gs['sc_type'], '조건부무료배송');
            $gsScType[3]=option_selected('3', $gs['sc_type'], '유료배송');

            $sc_method=array('','','');
            $sc_method[0]=option_selected('0', $gs['sc_method'], '선불');
            $sc_method[1]=option_selected('1', $gs['sc_method'], '착불');
            $sc_method[2]=option_selected('2', $gs['sc_method'], '사용자선택');

            $sc_each_use=get_checked('1', $gs['sc_each_use']);

            $zoneArr=array('','','','','','','','','');
            $zoneArr[0]=option_selected('전국', $gs['zone'], '전국');
            $zoneArr[1]=option_selected('강원도', $gs['zone'], '강원도');
            $zoneArr[2]=option_selected('경기도', $gs['zone'], '경기도');
            $zoneArr[3]=option_selected('경상도', $gs['zone'], '경상도'); 
            $zoneArr[4]=option_selected('서울/경기도', $gs['zone'], '서울/경기도'); 
            $zoneArr[5]=option_selected('서울특별시', $gs['zone'], '서울특별시'); 
            $zoneArr[6]=option_selected('전라도', $gs['zone'], '전라도');
            $zoneArr[7]=option_selected('제주도', $gs['zone'], '제주도');
            $zoneArr[8]=option_selected('충청도', $gs['zone'], '충청도');

            $gsCate="";
            if(!$gs['info_gubun']) $gs['info_gubun'] = 'wear';
            $item_info=$this->config->item('item_info');
            foreach($item_info as $key=>$value) {
                $opt_value = $key;
                $opt_text  = $value['title'];
                $gsCate.='<option value="'.$opt_value.'" '.get_selected($opt_value, $gs['info_gubun']).'>'.$opt_text.'</option>';
            }
            $goods_info=$this->admin_template->goods_info($gs);
            $get_goods_sca_select=$this->common_lib->get_goods_sca_select('sch_relation');


            $str = array();
            $sql = " select b.index_no, b.gname, b.simg1
                    from shop_goods_relation a join shop_goods b ON (a.gs_id2=b.index_no)
                    where a.gs_id = ?
                    order by ir_no asc ";
            $para=array($gs_id);
            $result=$this->query_model->returnArr($sql,$para);
            $g=0;
            $reg_relationList="";
            foreach($result as $key => $row){
                $gname = $this->funcomplex_lib->get_it_image($row['index_no'], $row['simg1'], 50, 50).' '.$row['gname'];

                if($g==0)
                    $reg_relationList.='<ul>';
                $reg_relationList.="
                <li>
                    <input type=\"hidden\" name=\"re_gs_id[]\" value=\"{$row['index_no']}\">
                    <div class=\"list_item\">{$gname}</div>
                    <div class=\"list_item_btn\"><button type=\"button\" class=\"del_item btn_small\">삭제</button></div>
                </li>";
                $str[] = $row['index_no'];
                $g++;
            }
            $str = implode(",", $str);

            if($g > 0)
                $reg_relationList.='</ul>';
            else
                $reg_relationList.='<p>선택된 상품이 없습니다.</p>';

            $simg_typeArr=array('','');
            $simg_typeArr[0]=get_checked('0', $gs['simg_type']);
            $simg_typeArr[1]=get_checked('1', $gs['simg_type']);

            $imgUploadStr="";
            for($i=1; $i<=6; $i++) {
                if($i == 1) {
                    $item_wpx = $default['de_item_small_wpx'];
                    $item_hpx = $default['de_item_small_hpx'];
                } else {
                    $item_wpx = $default['de_item_medium_wpx'];
                    $item_hpx = $default['de_item_medium_hpx'];
                }

                $image_str = '';
                if(in_array($i,array(1,2))) {
                    $image_str = ' <strong class="fc_red">[필수]</strong>';
                }
                $imgUploadStr.="
                <tr class=\"item_img_fld\">
                    <th scope=\"row\">이미지{$i} <span class=\"fc_197\">({$item_wpx} * {$item_hpx})</span>{$image_str}</th>
                    <td>
                        <div class=\"item_file_fld\">
                            <input type=\"file\" name=\"simg{$i}\">
                            ".get_look_ahead($gs['simg'.$i], "simg{$i}_del")."
                        </div>
                        <div class=\"item_url_fld\">
                            <input type=\"text\" name=\"simg{$i}\" value=\"".$gs['simg'.$i]."\" class=\"frm_input\" size=\"80\" placeholder=\"http://\">
                        </div>
                    </td>
                </tr>";
            }
            $memoEdit=$this->global_lib->editor_html('memo', $this->funcomplex_lib->get_text(stripcslashes($gs['memo']), 0));
            $memoJs=$this->global_lib->get_editor_js('memo');
            $data = array(
                'snbTagStart' => $snbTagStart,
                'snbTagEnd' => $snbTagEnd,
                'admin_head_sub' => $admin_head_sub,
                'admin_footer_sub' => $admin_footer_sub,
                'jqueryUiDatePick' => $jqueryUiDatePick,
                'pg_anchor' => $pg_anchor,
                'jqueryUiDatePick' => $jqueryUiDatePick,
                'w' => $w,
                'gs_id' => $gs_id,
                'qstr' => $qstr,
                'page' => $page,
                'bak' => $bak,
                'sel_caTag' => $sel_caTag,
                'sel_cateOpt' => $sel_cateOpt,
                'frm_submit' => $frm_submit,
                'recentChange' => $recentChange,
                'gs' => $gs,
                'preview' => $preview,
                'help' => $help,
                'brandStr' => $brandStr,
                'notax' => $notax,
                'isopenArr' => $isopenArr,
                'opt_subject' => $opt_subject,
                'goods_option' => $goods_option,
                'optConfigJs' => $optConfigJs,
                'addOptStr' => $addOptStr,
                'goods_splObj' => $goods_splObj,
                'addOptStr2' => $addOptStr2,
                'stockMod' => $stockMod,
                'get_goods_level_select' => $get_goods_level_select,
                'buy_onlyStr' => $buy_onlyStr,
                'gsScType' => $gsScType,
                'sc_method' => $sc_method,
                'sc_each_use' => $sc_each_use,
                'zoneArr' => $zoneArr,
                'gsCate' => $gsCate,
                'goods_info' => $goods_info,
                'get_goods_sca_select' => $get_goods_sca_select,
                'reg_relationList' => $reg_relationList,
                'str' => $str,
                'simg_typeArr' => $simg_typeArr,
                'imgUploadStr' => $imgUploadStr,
                'memoEdit' => $memoEdit,
                'memoJs' => $memoJs,
                'readonly' => $readonly,
                'type' => $type
            );
            $this->load->view('mypage/seller/goods_form',$data);
        }
        private function seller_goods_xls_regSkin($snbTagStart,$snbTagEnd){
            $pg_title = "상품 엑셀일괄등록";
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $data=array('snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub);
            $this->load->view('mypage/seller/goods_xls_reg',$data);
        }
        private function seller_goods_xls_reg_updateSkin($snbTagStart,$snbTagEnd){
            $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
            $total_count=0;
            $succ_count=0;
            $fail_count=0;
            $fail_gcode="";
            if($_FILES['excelfile']['tmp_name']) {
                $file = $_FILES['excelfile']['tmp_name'];
            
                include_once('./lib/Excel/reader.php');
            
                $data = new Spreadsheet_Excel_Reader();
            
                // Set output Encoding.
                $data->setOutputEncoding('UTF-8');
            
                /***
                * if you want you can change 'iconv' to mb_convert_encoding:
                * $data->setUTFEncoder('mb');
                *
                **/
            
                /***
                * By default rows & cols indeces start with 1
                * For change initial index use:
                * $data->setRowColOffset(0);
                *
                **/
            
                /***
                * Some function for formatting output.
                * $data->setDefaultFormat('%.2f');
                * setDefaultFormat - set format for columns with unknown formatting
                *
                * $data->setColumnFormat(4, '%.3f');
                * setColumnFormat - set format for column (apply only to number fields)
                *
                **/
            
                $data->read($file);
            
                /*
                $data->sheets[0]['numRows'] - count rows
                $data->sheets[0]['numCols'] - count columns
                $data->sheets[0]['cells'][$i][$j] - data from $i-row $j-column
            
                $data->sheets[0]['cellsInfo'][$i][$j] - extended info about cell
            
                $data->sheets[0]['cellsInfo'][$i][$j]['type'] = "date" | "number" | "unknown"
                if 'type' == "unknown" - use 'raw' value, because  cell contain value with format '0.00';
                $data->sheets[0]['cellsInfo'][$i][$j]['raw'] = value if cell without format
                $data->sheets[0]['cellsInfo'][$i][$j]['colspan']
                $data->sheets[0]['cellsInfo'][$i][$j]['rowspan']
                */
            
                error_reporting(E_ALL ^ E_NOTICE);
            
                $dup_gcode = array();
                $fail_gcode = array();
                $dup_count = 0;
                $total_count = 0;
                $fail_count = 0;
                $succ_count = 0;
            
                for($i=4; $i<=$data->sheets[0]['numRows']; $i++){
                    if(trim($data->sheets[0]['cells'][$i][1]) == '')
                        continue;
            
                    $total_count++;
            
                    $j = 1;
            
                    $gcode			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 상품코드
                    $ca_id			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 대표분류
                    $ca_id2			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 추가분류2
                    $ca_id3			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 추가분류3
                    $gname			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 상품명
                    $explan			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 짧은설명
                    $keywords		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 검색키워드
                    $model			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 모델명
                    $brand_nm		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 브랜드
                    $notax			= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 과세설정
                    $zone			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매가능지역
                    $zone_msg		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매가능지역 추가설명
                    $origin			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 원산지
                    $maker			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 제조사
                    $isopen			= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 판매여부
                    $supply_price	= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 공급가격
                    $normal_price	= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 시중가격
                    $goods_price	= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 판매가격
                    $price_msg		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 가격대체문구
                    $stock_mod		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 재고적용타입
                    $stock_qty		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 재고수량
                    $noti_qty		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 재고통보수량
                    $odr_min		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 최소주문한도
                    $odr_max		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 최대주문한도
                    $gpoint			= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 포인트
                    $sb_date		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매기간 시작일
                    $eb_date		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매기간 종료일
                    $buy_level		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 구매가능레벨
                    $buy_only		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 가격공개
                    $sc_type		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 배송비유형
                    $sc_method		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 배송비결제
                    $sc_amt			= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 기본배송비
                    $sc_minimum		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 조건배송비
                    $simg_type		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 이미지등록방식
                    $simg1			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 소이미지
                    $simg2			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지1
                    $simg3			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지2
                    $simg4			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지3
                    $simg5			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지4
                    $simg6			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지5
                    $memo			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 상세설명
                    $admin_memo		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 관리자메모
            
                    if(!$gcode || !$gname) {
                        $fail_count++;
                        continue;
                    }
            
                    // 상품코드로 상품이 있는지 먼저 검사
                    
                    $sql2 = " select count(*) as cnt from shop_goods where gcode = ?";
                    $param2=array($gcode);
                    $row2 = $this->query_model->returnOneArr($sql2,$param2);
                    if($row2['cnt']) {
                        $fail_gcode[] = $gcode;
                        $dup_gcode[] = $gcode;
                        $dup_count++;
                        $fail_count++;
                        continue;
                    }
            
                    unset($value);
                    $value['use_aff']		= 0; // 본사상품으로 설정
                    $value['shop_state']	= $config['seller_reg_auto']; //자동승인여부
                    $value['mb_id']			= $seller['seller_code']; // 판매자ID
                    $value['gcode']			= $gcode; // 상품코드
                    $value['ca_id']			= $ca_id; // 대표분류
                    $value['ca_id2']		= $ca_id2; // 추가분류2
                    $value['ca_id3']		= $ca_id3; // 추가분류3
                    $value['gname']			= $gname; // 상품명
                    $value['explan']		= $explan; // 짧은설명
                    $value['keywords']		= $keywords; // 검색키워드
                    $value['model']			= $model; // 모델명
                    $value['brand_uid']		= $this->common_lib->get_brand_chk($brand_nm); // 브랜드주키
                    $value['brand_nm']		= $brand_nm; // 브랜드명
                    $value['notax']			= $notax; // 과세설정
                    $value['zone']			= $zone; // 판매가능지역
                    $value['zone_msg']		= $zone_msg; // 판매가능지역 추가설명
                    $value['origin']		= $origin; // 원산지
                    $value['maker']			= $maker; // 제조사
                    $value['isopen']		= $isopen; // 판매여부
                    $value['supply_price']	= $supply_price; // 공급가격
                    $value['normal_price']	= $normal_price; // 시중가격
                    $value['goods_price']	= $goods_price; // 판매가격
                    $value['price_msg']		= $price_msg; // 가격대체문구
                    $value['stock_mod']		= $stock_mod; // 재고적용타입
                    $value['stock_qty']		= $stock_qty; // 재고수량
                    $value['noti_qty']		= $noti_qty; // 재고통보수량
                    $value['odr_min']		= $odr_min; // 최소주문한도
                    $value['odr_max']		= $odr_max; // 최대주문한도
                    $value['gpoint']		= $gpoint; // 포인트
                    $value['sb_date']		= $sb_date; // 판매기간 시작일
                    $value['eb_date']		= $eb_date; // 판매기간 종료일
                    $value['buy_level']		= $buy_level; // 구매가능레벨
                    $value['buy_only']		= $buy_only; // 가격공개
                    $value['sc_type']		= $sc_type; // 배송비유형
                    $value['sc_method']		= $sc_method; // 배송비결제
                    $value['sc_amt']		= $sc_amt; // 기본배송비
                    $value['sc_minimum']	= $sc_minimum; // 조건배송비
                    $value['simg_type']		= $simg_type; // 이미지등록방식
                    $value['simg1']			= $simg1; // 소이미지
                    $value['simg2']			= $simg2; // 중이미지1
                    $value['simg3']			= $simg3; // 중이미지2
                    $value['simg4']			= $simg4; // 중이미지3
                    $value['simg5']			= $simg5; // 중이미지4
                    $value['simg6']			= $simg6; // 중이미지5
                    $value['memo']			= $memo; // 상세설명
                    $value['admin_memo']	= $admin_memo; // 관리자메모
                    $value['reg_time']		= $OM_TIME_YMDHIS; //등록일시
                    $value['update_time']	= $OM_TIME_YMDHIS; //수정일시
                    $this->query_model->insertObj("shop_goods", $value);
                    $succ_count++;
                }
            }
            $pg_title = "상품 엑셀일괄등록";
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $fail_countTag="";
            if($fail_count > 0) {
                $fail_countTag="
                <tr>
                    <th scope=\"row\">실패상품코드</th>
                    <td>".implode(', ', $fail_gcode)."</td>
                </tr>";
            }
            $dup_countTag="";
            if($dup_count > 0) {
                $dup_countTag="
                <tr>
                    <th scope=\"row\">상품코드중복건수</th>
                    <td>".number_format($dup_count)."</td>
                </tr>
                <tr>
                    <th scope=\"row\">중복상품코드</th>
                    <td>".implode('<br>', $dup_gcode)."</td>
                </tr>";
            }
            $data=array('snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub,
                        'total_count'=>$total_count,'succ_cout'=>$succ_count,'fail_count'=>$fail_count,'fail_countTag'=>$fail_countTag,'dup_countTag'=>$dup_countTag);
            $this->load->view('mypage/seller/goods_xls_reg_update',$data);
        }
        private function seller_goods_xls_modSkin($snbTagStart,$snbTagEnd){
            $pg_title = "상품 엑셀일괄수정";
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $data=array('snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub);
            $this->load->view('mypage/seller/goods_xls_mod',$data);
        }
        private function seller_goods_xls_mod_updateSkin($snbTagStart,$snbTagEnd){
            $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
            $config=$this->load->get_var('config');
            $pg_title = "상품 엑셀일괄수정";
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $fail_gcode = array();
            $fail_count = 0;
            $total_count = 0;
            $succ_count = 0;
            if($_FILES['excelfile']['tmp_name']) {
                $file = $_FILES['excelfile']['tmp_name'];
                include_once('./lib/Excel/reader.php');
                $data = new Spreadsheet_Excel_Reader();
                // Set output Encoding.
                $data->setOutputEncoding('UTF-8');
                $data->read($file);
                error_reporting(E_ALL ^ E_NOTICE);
                $fail_gcode = array();
                for($i=2; $i<=$data->sheets[0]['numRows']; $i++){
                    if(trim($data->sheets[0]['cells'][$i][1]) == '')
                        continue;
    
                    $total_count++;
    
                    $j = 1;
    
                    $gcode			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 상품코드
                    $ca_id			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 대표분류
                    $ca_id2			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 추가분류2
                    $ca_id3			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 추가분류3
                    $gname			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 상품명
                    $explan			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 짧은설명
                    $keywords		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 검색키워드
                    $model			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 모델명
                    $brand_nm		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 브랜드
                    $notax			= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 과세설정
                    $zone			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매가능지역
                    $zone_msg		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매가능지역 추가설명
                    $origin			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 원산지
                    $maker			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 제조사
                    $isopen			= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 판매여부
                    $supply_price	= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 공급가격
                    $normal_price	= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 시중가격
                    $goods_price	= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 판매가격
                    $price_msg		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 가격대체문구
                    $stock_mod		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 재고적용타입
                    $stock_qty		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 재고수량
                    $noti_qty		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 재고통보수량
                    $odr_min		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 최소주문한도
                    $odr_max		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 최대주문한도
                    $gpoint			= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 포인트
                    $sb_date		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매기간 시작일
                    $eb_date		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매기간 종료일
                    $buy_level		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 구매가능레벨
                    $buy_only		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 가격공개
                    $sc_type		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 배송비유형
                    $sc_method		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 배송비결제
                    $sc_amt			= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 기본배송비
                    $sc_minimum		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 조건배송비
                    $simg_type		= addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 이미지등록방식
                    $simg1			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 소이미지
                    $simg2			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지1
                    $simg3			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지2
                    $simg4			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지3
                    $simg5			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지4
                    $simg6			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지5
                    $memo			= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 상세설명
                    $admin_memo		= addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 관리자메모
    
                    if(!$gcode || !$gname) {
                        $fail_count++;
                        continue;
                    }
    
                    // 상품코드로 상품이 있는지 먼저 검사
                    $sql2 = " select count(*) as cnt from shop_goods where gcode = ?";
                    $param=array($gcode);
                    $row2=$this->query_model->returnOneArr($sql2,$param);
                    if(!$row2['cnt']) {
                        $fail_gcode[] = $gcode;
                        $fail_count++;
                        continue;
                    }
    
                    unset($value);
                    $value['use_aff']		= 0; // 본사상품으로 설정
                    $value['shop_state']	= $config['seller_mod_auto']; //자동승인여부
                    $value['mb_id']			= $seller['seller_code']; // 판매자ID
                    $value['gcode']			= $gcode; // 상품코드
                    $value['ca_id']			= $ca_id; // 대표분류
                    $value['ca_id2']		= $ca_id2; // 추가분류2
                    $value['ca_id3']		= $ca_id3; // 추가분류3
                    $value['gname']			= $gname; // 상품명
                    $value['explan']		= $explan; // 짧은설명
                    $value['keywords']		= $keywords; // 검색키워드
                    $value['model']			= $model; // 모델명
                    $value['brand_uid']		= $this->common_lib->get_brand_chk($brand_nm); // 브랜드주키
                    $value['brand_nm']		= $brand_nm; // 브랜드명
                    $value['notax']			= $notax; // 과세설정
                    $value['zone']			= $zone; // 판매가능지역
                    $value['zone_msg']		= $zone_msg; // 판매가능지역 추가설명
                    $value['origin']		= $origin; // 원산지
                    $value['maker']			= $maker; // 제조사
                    $value['isopen']		= $isopen; // 판매여부
                    $value['supply_price']	= $supply_price; // 공급가격
                    $value['normal_price']	= $normal_price; // 시중가격
                    $value['goods_price']	= $goods_price; // 판매가격
                    $value['price_msg']		= $price_msg; // 가격대체문구
                    $value['stock_mod']		= $stock_mod; // 재고적용타입
                    $value['stock_qty']		= $stock_qty; // 재고수량
                    $value['noti_qty']		= $noti_qty; // 재고통보수량
                    $value['odr_min']		= $odr_min; // 최소주문한도
                    $value['odr_max']		= $odr_max; // 최대주문한도
                    $value['gpoint']		= $gpoint; // 포인트
                    $value['sb_date']		= $sb_date; // 판매기간 시작일
                    $value['eb_date']		= $eb_date; // 판매기간 종료일
                    $value['buy_level']		= $buy_level; // 구매가능레벨
                    $value['buy_only']		= $buy_only; // 가격공개
                    $value['sc_type']		= $sc_type; // 배송비유형
                    $value['sc_method']		= $sc_method; // 배송비결제
                    $value['sc_amt']		= $sc_amt; // 기본배송비
                    $value['sc_minimum']	= $sc_minimum; // 조건배송비
                    $value['simg_type']		= $simg_type; // 이미지등록방식
                    $value['simg1']			= $simg1; // 소이미지
                    $value['simg2']			= $simg2; // 중이미지1
                    $value['simg3']			= $simg3; // 중이미지2
                    $value['simg4']			= $simg4; // 중이미지3
                    $value['simg5']			= $simg5; // 중이미지4
                    $value['simg6']			= $simg6; // 중이미지5
                    $value['memo']			= $memo; // 상세설명
                    $value['admin_memo']	= $admin_memo; // 관리자메모
                    $value['update_time']	= $OM_TIME_YMDHIS; //수정일시
                    $this->query_model->updateObj("shop_goods", $value,"gcode='$gcode'");
    
                    $succ_count++;
                }
            }
            
            $fail_countTag="";
            if($fail_count > 0) {
                $fail_countTag="
                <tr>
                    <th scope=\"row\">실패상품코드</th>
                    <td>".implode(', ', $fail_gcode)."</td>
                </tr>";
            }
            
            $data=array('snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub,
                        'total_count'=>$total_count,'succ_count'=>$succ_count,'fail_count'=>$fail_count,'fail_countTag'=>$fail_countTag);
            $this->load->view('mypage/seller/seller_goods_xls_mod_update',$data);
        }
        private function seller_goods_stockSkin($code,$snbTagStart,$snbTagEnd){
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];

            $pg_title = "상품 재고관리";
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();

            $qstr=$this->load->get_var('qstr');
            $seller=$this->load->get_var('seller');
            $config=$this->load->get_var('config');

            $fr_date=$this->input->get('fr_date');
            $to_date=$this->input->get('to_date');
            $sel_ca1=$this->input->get('sel_ca1');
            $sel_ca2=$this->input->get('sel_ca2');
            $sel_ca3=$this->input->get('sel_ca3');
            $sel_ca4=$this->input->get('sel_ca4');
            $sel_ca5=$this->input->get('sel_ca5');
            $q_date_field=$this->input->get('q_date_field');
            $q_brand=$this->input->get('q_brand');
            $q_zone=$this->input->get('q_zone');
            $q_stock_field=$this->input->get('q_stock_field');
            $fr_stock=$this->input->get('fr_stock');
            $to_stock=$this->input->get('to_stock');
            $q_price_field=$this->input->get('q_price_field');
            $fr_price=$this->input->get('fr_price');
            $to_price=$this->input->get('to_price');
            $q_isopen=$this->input->get('q_isopen');
            $q_option=$this->input->get('q_option');
            $q_supply=$this->input->get('q_supply');
            $q_notax=$this->input->get('q_notax');
            $q_state=$this->input->get('q_state');
            $stx=$this->input->get('stx');
            $sfl=$this->input->get('sfl');
            $orderby=$this->input->get('orderby');
            $page=$this->input->get('page');
            $sca=$this->input->get('sca');

            $filed=$this->input->get('filed');
            $filed=$this->db->escape_str($filed);
            


            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';
            
            if($sel_ca1) $sca = $sel_ca1;
            if($sel_ca2) $sca = $sel_ca2;
            if($sel_ca3) $sca = $sel_ca3;
            if($sel_ca4) $sca = $sel_ca4;
            if($sel_ca5) $sca = $sel_ca5;
            
            if(isset($sel_ca1))			$qstr .= "&sel_ca1=$sel_ca1";
            if(isset($sel_ca2))			$qstr .= "&sel_ca2=$sel_ca2";
            if(isset($sel_ca3))			$qstr .= "&sel_ca3=$sel_ca3";
            if(isset($sel_ca4))			$qstr .= "&sel_ca4=$sel_ca4";
            if(isset($sel_ca5))			$qstr .= "&sel_ca5=$sel_ca5";
            if(isset($q_date_field))	$qstr .= "&q_date_field=$q_date_field";
            if(isset($q_brand))			$qstr .= "&q_brand=$q_brand";
            if(isset($q_zone))			$qstr .= "&q_zone=$q_zone";
            if(isset($q_stock_field))	$qstr .= "&q_stock_field=$q_stock_field";
            if(isset($fr_stock))		$qstr .= "&fr_stock=$fr_stock";
            if(isset($to_stock))		$qstr .= "&to_stock=$to_stock";
            if(isset($q_price_field))	$qstr .= "&q_price_field=$q_price_field";
            if(isset($fr_price))		$qstr .= "&fr_price=$fr_price";
            if(isset($to_price))		$qstr .= "&to_price=$to_price";
            if(isset($q_isopen))		$qstr .= "&q_isopen=$q_isopen";
            if(isset($q_option))		$qstr .= "&q_option=$q_option";
            if(isset($q_supply))		$qstr .= "&q_supply=$q_supply";
            if(isset($q_notax))			$qstr .= "&q_notax=$q_notax";
            if(isset($q_state))			$qstr .= "&q_state=$q_state";
            
            $query_string = "code=$code$qstr";
            $q1 = $query_string;
            $q2 = $query_string."&page=$page";
            
            $sql_common = " from shop_goods ";
            $sql_search = " where mb_id = '{$seller['seller_code']}' ";
            $sql_search.= " and (stock_qty <= noti_qty and stock_mod = 1 and opt_subject = '') ";
            
            if($sca) {
                $sql_search .= " and (ca_id like '$sca%' or ca_id2 like '$sca%' or ca_id3 like '$sca%') ";
            }
            
            // 검색어
            if($stx) {
                switch($sfl) {
                    case "gname" :
                    case "explan" :
                    case "maker" :
                    case "origin" :
                    case "model" :
                        $sql_search .= " and $sfl like '%$stx%' ";
                        break;
                    default :
                        $sql_search .= " and $sfl like '$stx%' ";
                        break;
                }
            }
            
            // 기간검색
            if($fr_date && $to_date)
                $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
            else if($fr_date && !$to_date)
                $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
            else if(!$fr_date && $to_date)
                $sql_search .= " and $q_date_field between '$to_date 00:00:00' and '$to_date 23:59:59' ";
            
            // 브랜드
            if(isset($q_brand) && $q_brand)
                $sql_search .= " and brand_uid = '$q_brand' ";
            
            // 배송가능 지역
            if(isset($q_zone) && $q_zone)
                $sql_search .= " and zone = '$q_zone' ";
            
            // 상품재고
            if($fr_stock && $to_stock)
                $sql_search .= " and $q_stock_field between '$fr_stock' and '$to_stock' ";
            
            // 상품가격
            if($fr_price && $to_price)
                $sql_search .= " and $q_price_field between '$fr_price' and '$to_price' ";
            
            // 판매여부
            if(isset($q_isopen) && is_numeric($q_isopen))
                $sql_search .= " and isopen='$q_isopen' ";
            
            // 과세유형
            if(isset($q_notax) && is_numeric($q_notax))
                $sql_search .= " and notax = '$q_notax' ";
            
            // 상품 필수옵션
            if(isset($q_option) && is_numeric($q_option)) {
                if($q_option)
                    $sql_search .= " and opt_subject <> '' ";
                else
                    $sql_search .= " and opt_subject = '' ";
            }
            
            // 상품 추가옵션
            if(isset($q_supply) && is_numeric($q_supply)) {
                if($q_supply)
                    $sql_search .= " and spl_subject <> '' ";
                else
                    $sql_search .= " and spl_subject = '' ";
            }
            
            // 승인상태
            if(isset($q_state) && is_numeric($q_state))
                $sql_search .= " and shop_state = '$q_state' ";
            
            if(!$orderby) {
                $filed = "index_no";
                $sod = "desc";
            } else {
                $sod = $orderby;
            }
            
            $sql_order = " order by $filed $sod ";
            
            // 테이블의 전체 레코드수만 얻음
            $sql = " select count(*) as cnt $sql_common $sql_search ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];
            
            if(isset($_SESSION['ss_page_rows']))
                $page_rows = $_SESSION['ss_page_rows'];
            else
                $page_rows = 30;
            
            $rows = $page_rows;
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);
            
            $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
            $result = $this->query_model->returnArr($sql);
            
            $jqueryUiDatePick=$this->admin_template->datePicker();
            
            $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택상품복사\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"".base_url()."MypageProcess/seller_goods_list_excel?$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장</a>
            <a href=\"" . base_url() . "data/download/한류입점사 상품등록 메뉴얼.pdf\" download class=\"btn_lsmall\"> 상품등록 설명서</a>
            <a href=\"".base_url()."mypage/page?code=seller_goods_form&type=normal\" class=\"fr btn_lsmall red marl5\"><i class=\"ionicons ion-android-add\"></i> 열린장터 상품등록</a>
            <a href=\"".base_url()."mypage/page?code=seller_goods_form&type=timesale\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 함께장터 상품등록</a>";
            $sflArr=array('','','','','','');
            $sflArr[0]=option_selected('gname', $sfl, '상품명');
            $sflArr[1]=option_selected('gcode', $sfl, '상품코드');
            $sflArr[2]=option_selected('maker', $sfl, '제조사');
            $sflArr[3]=option_selected('origin', $sfl, '원산지');
            $sflArr[4]=option_selected('model', $sfl, '모델명');
            $sflArr[5]=option_selected('explan', $sfl, '짧은설명');

            $scaSelect=array('','','','','');
            $scaSelect[0]=$this->funcomplex_lib->get_category_select_1('sel_ca1', $sca);
            $scaSelect[1]=$this->funcomplex_lib->get_category_select_2('sel_ca2', $sca);
            $scaSelect[2]=$this->funcomplex_lib->get_category_select_3('sel_ca3', $sca);
            $scaSelect[3]=$this->funcomplex_lib->get_category_select_4('sel_ca4', $sca);
            $scaSelect[4]=$this->funcomplex_lib->get_category_select_5('sel_ca5', $sca);

            $updateTimeTag=option_selected('update_time', $q_date_field, "최근수정일");
            $regTimeTag=option_selected('reg_time', $q_date_field, "최초등록일");
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);

            $brandStr="";
            $sql = "select * from shop_brand where br_user_yes='0' order by br_name asc ";
            $res = $this->query_model->returnArr($sql);
            $brandStr.=option_selected('', $q_brand, '전체');
            foreach($res as $key => $row){
                $brandStr.=option_selected($row['br_id'], $q_brand, $row['br_name']);
            }
            $q_zoneStr=array('','','','','','','','','','');
            $q_zoneStr[0]=option_selected('',  $q_zone, '전체');
            $q_zoneStr[1]=option_selected('전국', $q_zone, '전국');
            $q_zoneStr[2]=option_selected('강원도', $q_zone, '강원도');
            $q_zoneStr[3]=option_selected('경기도', $q_zone, '경기도');
            $q_zoneStr[4]=option_selected('경상도', $q_zone, '경상도');
            $q_zoneStr[5]=option_selected('서울/경기도', $q_zone, '서울/경기도');
            $q_zoneStr[6]=option_selected('서울특별시', $q_zone, '서울특별시');
            $q_zoneStr[7]=option_selected('전라도', $q_zone, '전라도');
            $q_zoneStr[8]=option_selected('제주도', $q_zone, '제주도');
            $q_zoneStr[9]=option_selected('충청도', $q_zone, '충청도'); 

            $stock_qtyTag=option_selected('stock_qty', $q_stock_field, "재고수량");
            $noti_qtyTag=option_selected('noti_qty', $q_stock_field, "통보수량");

            $price_fieldArr=array('','','','');
            $price_fieldArr[0]=option_selected('goods_price', $q_price_field, "판매가격"); 
            $price_fieldArr[1]=option_selected('supply_price', $q_price_field, "공급가격");
            $price_fieldArr[2]=option_selected('normal_price', $q_price_field, "시중가격");
            $price_fieldArr[3]=option_selected('gpoint', $q_price_field, "포인트");

            $q_isopenArr=array('','','','','');
            $q_isopenArr[0]=radio_checked('q_isopen', $q_isopen,  '', '전체');
            $q_isopenArr[1]=radio_checked('q_isopen', $q_isopen, '1', '진열');
            $q_isopenArr[2]=radio_checked('q_isopen', $q_isopen, '2', '품절');
            $q_isopenArr[3]=radio_checked('q_isopen', $q_isopen, '3', '단종');
            $q_isopenArr[4]=radio_checked('q_isopen', $q_isopen, '4', '중지');

            $q_optionArr=array('all'=>'','use'=>'','unuse'=>'');
            $q_optionArr['all']=radio_checked('q_option', $q_option,  '', '전체');
            $q_optionArr['use']=radio_checked('q_option', $q_option, '1', '사용');
            $q_optionArr['unuse']=radio_checked('q_option', $q_option, '0', '미사용');

            $q_notaxArr=array('all'=>'','1'=>'','0'=>'');
            $q_notaxArr['all']=radio_checked('q_notax', $q_notax,  '', '전체');
            $q_notaxArr['1']=radio_checked('q_notax', $q_notax, '1', '과세');
            $q_notaxArr['0']=radio_checked('q_notax', $q_notax, '0', '비과세');

            $q_supplyArr=array('all'=>'','1'=>'','0'=>'');
            $q_supplyArr['all']=radio_checked('q_supply', $q_supply,  '', '전체');
            $q_supplyArr['1']=radio_checked('q_supply', $q_supply, '1', '사용');
            $q_supplyArr['0']=radio_checked('q_supply', $q_supply, '0', '미사용');

            $q_stateArr=array('all'=>'','0'=>'','1'=>'','2'=>'');
            $q_stateArr['all']=radio_checked('q_state', $q_state,  '', '전체');
            $q_stateArr['0']=radio_checked('q_state', $q_state, '0', '승인'); 
            $q_stateArr['1']=radio_checked('q_state', $q_state, '1', '대기');
            $q_stateArr['2']=radio_checked('q_state', $q_state, '2', '보류'); 

            $page_rowsOpt=array('30'=>'','50'=>'','100'=>'','150'=>'');
            $page_rowsOpt['30']=option_selected('30',  $page_rows, '30줄 정렬');
            $page_rowsOpt['50']=option_selected('50',  $page_rows, '50줄 정렬');
            $page_rowsOpt['100']=option_selected('100',  $page_rows, '100줄 정렬');
            $page_rowsOpt['150']=option_selected('150',  $page_rows, '150줄 정렬');

            $gcodeStr=$this->gvcomplex_lib->subject_sort_link('gcode',$q2);
            $gnameStr=$this->gvcomplex_lib->subject_sort_link('gname',$q2);
            $regTimeStr=$this->gvcomplex_lib->subject_sort_link('reg_time',$q2);
            $isopenStr=$this->gvcomplex_lib->subject_sort_link('isopen',$q2);
            
            $shop_stateStr=$this->gvcomplex_lib->subject_sort_link('shop_state',$q2);
            $updateTimeStr=$this->gvcomplex_lib->subject_sort_link('update_time',$q2);
            $stockQtyStr=$this->gvcomplex_lib->subject_sort_link('stock_qty',$q2);
            $normalPriceStr=$this->gvcomplex_lib->subject_sort_link('normal_price',$q2);
            $supplyPriceStr=$this->gvcomplex_lib->subject_sort_link('supply_price',$q2);
            $goodPriceStr=$this->gvcomplex_lib->subject_sort_link('goods_price',$q2);
            $gpointStr=$this->gvcomplex_lib->subject_sort_link('gpoint',$q2);

            $i=0;
            $goods_formTable="";
            $gw_isopen=$this->config->item('gw_isopen');
            $gw_state=$this->config->item('gw_state');

            foreach($result as $key => $row){
                $gs_id = $row['index_no'];
        
                if($row['stock_mod'])
                    $stockQty = number_format($row['stock_qty']);
                else
                    $stockQty = '<span class="txt_false">무제한</span>';
        
                $bg = 'list'.($i%2);
                $goods_formTable.="
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">".$num--."</td>
                    <td rowspan=\"2\"><a href=\"".base_url()."shop/view?index_no={$gs_id}\" target=\"_blank\">".$this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40)."</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">".$this->funcomplex_lib->get_text($row['gname'])."</td>
                    <td>".substr($row['reg_time'],2,8)."</td>
                    <td>".(isset($gw_isopen[$row['isopen']])?$gw_isopen[$row['isopen']]:'')."</td>
                    <td rowspan=\"2\" class=\"tar\">".number_format($row['normal_price'])."</td>
                    <td rowspan=\"2\" class=\"tar\">".number_format($row['supply_price'])."</td>
                    <td rowspan=\"2\" class=\"tar\">".number_format($row['goods_price'])."</td>
                    <td rowspan=\"2\" class=\"tar\">".number_format($row['gpoint'])."</td>
                    <td rowspan=\"2\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$gs_id} {$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">".$gw_state[$row['shop_state']]."</td>
                    <td class=\"tal txt_succeed\">".$this->gvcomplex_lib->get_seller_name($row['mb_id'])."</td>
                    <td class=\"tal txt_succeed\">".$this->funcomplex_lib->get_cgy_info($row)."</td>
                    <td class=\"fc_00f\">".substr($row['update_time'],2,8)."</td>
                    <td>{$stockQty}</td>
                </tr>";
                $i++;
            }
            if($i==0)
                $goods_formTable.='<tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $data=array('snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub,
                        'jqueryUiDatePick'=>$jqueryUiDatePick,'btn_frmline'=>$btn_frmline,'code'=>$code,'sflArr'=>$sflArr,'stx'=>$stx,'scaSelect'=>$scaSelect,
                        'updateTimeTag'=>$updateTimeTag,'regTimeTag'=>$regTimeTag,'get_search_date'=>$get_search_date,'brandStr'=>$brandStr,
                        'q_zoneStr'=>$q_zoneStr,'stock_qtyTag'=>$stock_qtyTag,'noti_qtyTag'=>$noti_qtyTag,'fr_stock'=>$fr_stock,'to_stock'=>$to_stock,
                        'price_fieldArr'=>$price_fieldArr,'fr_price'=>$fr_price,'to_price'=>$to_price,'q_isopenArr'=>$q_isopenArr,'q_optionArr'=>$q_optionArr,
                        'q_notaxArr'=>$q_notaxArr,'q_supplyArr'=>$q_supplyArr,'q_stateArr'=>$q_stateArr,'page_rowsOpt'=>$page_rowsOpt,'requestUrl'=>$requestUrl,
                        'q1'=>$q1,'gcodeStr'=>$gcodeStr,'gnameStr'=>$gnameStr,'regTimeStr'=>$regTimeStr,'isopenStr'=>$isopenStr,
                        'shop_stateStr'=>$shop_stateStr,'updateTimeStr'=>$updateTimeStr,'stockQtyStr'=>$stockQtyStr,'normalPriceStr'=>$normalPriceStr,
                        'supplyPriceStr'=>$supplyPriceStr,'goodPriceStr'=>$goodPriceStr,'gpointStr'=>$gpointStr,'goods_formTable'=>$goods_formTable,'pagination'=>$pagination,
                        'page'=>$page,'total_count'=>$total_count);
            $this->load->view('mypage/seller/goods_stock',$data);
        }
        private function seller_goods_optstockSkin($code,$snbTagStart,$snbTagEnd){
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];

            $pg_title = "상품 옵션재고관리";
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();

            $qstr=$this->load->get_var('qstr');
            $seller=$this->load->get_var('seller');
            $config=$this->load->get_var('config');

            $fr_date=$this->input->get('fr_date');
            $to_date=$this->input->get('to_date');
            $sel_ca1=$this->input->get('sel_ca1');
            $sel_ca2=$this->input->get('sel_ca2');
            $sel_ca3=$this->input->get('sel_ca3');
            $sel_ca4=$this->input->get('sel_ca4');
            $sel_ca5=$this->input->get('sel_ca5');
            $q_date_field=$this->input->get('q_date_field');
            $q_brand=$this->input->get('q_brand');
            $q_zone=$this->input->get('q_zone');
            $q_stock_field=$this->input->get('q_stock_field');
            $fr_stock=$this->input->get('fr_stock');
            $to_stock=$this->input->get('to_stock');
            $q_price_field=$this->input->get('q_price_field');
            $fr_price=$this->input->get('fr_price');
            $to_price=$this->input->get('to_price');
            $q_isopen=$this->input->get('q_isopen');
            $q_option=$this->input->get('q_option');
            $q_supply=$this->input->get('q_supply');
            $q_notax=$this->input->get('q_notax');
            $q_state=$this->input->get('q_state');
            $stx=$this->input->get('stx');
            $sfl=$this->input->get('sfl');
            $orderby=$this->input->get('orderby');
            $page=$this->input->get('page');
            $sca=$this->input->get('sca');

            $filed=$this->input->get('filed');
            $filed=$this->db->escape_str($filed);

            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

            if($sel_ca1) $sca = $sel_ca1;
            if($sel_ca2) $sca = $sel_ca2;
            if($sel_ca3) $sca = $sel_ca3;
            if($sel_ca4) $sca = $sel_ca4;
            if($sel_ca5) $sca = $sel_ca5;

            if(isset($sel_ca1))			$qstr .= "&sel_ca1=$sel_ca1";
            if(isset($sel_ca2))			$qstr .= "&sel_ca2=$sel_ca2";
            if(isset($sel_ca3))			$qstr .= "&sel_ca3=$sel_ca3";
            if(isset($sel_ca4))			$qstr .= "&sel_ca4=$sel_ca4";
            if(isset($sel_ca5))			$qstr .= "&sel_ca5=$sel_ca5";
            if(isset($q_date_field))	$qstr .= "&q_date_field=$q_date_field";
            if(isset($q_brand))			$qstr .= "&q_brand=$q_brand";
            if(isset($q_zone))			$qstr .= "&q_zone=$q_zone";
            if(isset($q_stock_field))	$qstr .= "&q_stock_field=$q_stock_field";
            if(isset($fr_stock))		$qstr .= "&fr_stock=$fr_stock";
            if(isset($to_stock))		$qstr .= "&to_stock=$to_stock";
            if(isset($q_price_field))	$qstr .= "&q_price_field=$q_price_field";
            if(isset($fr_price))		$qstr .= "&fr_price=$fr_price";
            if(isset($to_price))		$qstr .= "&to_price=$to_price";
            if(isset($q_isopen))		$qstr .= "&q_isopen=$q_isopen";
            if(isset($q_option))		$qstr .= "&q_option=$q_option";
            if(isset($q_supply))		$qstr .= "&q_supply=$q_supply";
            if(isset($q_notax))			$qstr .= "&q_notax=$q_notax";
            if(isset($q_state))			$qstr .= "&q_state=$q_state";

            $query_string = "code=$code$qstr";
            $q1 = $query_string;
            $q2 = $query_string."&page=$page";

            $sql_common = " from shop_goods_option a left join shop_goods b ON (a.gs_id=b.index_no) ";
            $sql_search = " where b.mb_id = '{$seller['seller_code']}' ";
            $sql_search.= "   and a.io_use = 1
                            and a.io_noti_qty <> '999999999'
                            and a.io_stock_qty <= a.io_noti_qty ";

            // 카테고리
            if($sca) {
                $sql_search .= " and (b.ca_id like '$sca%' or b.ca_id2 like '$sca%' or b.ca_id3 like '$sca%') ";
            }

            // 검색어
            if($stx) {
                switch($sfl) {
                    case "gname" :
                    case "explan" :
                    case "maker" :
                    case "origin" :
                    case "model" :
                        $sql_search .= " and b.$sfl like '%$stx%' ";
                        break;
                    default :
                        $sql_search .= " and b.$sfl like '$stx%' ";
                        break;
                }
            }

            // 기간검색
            if($fr_date && $to_date)
                $sql_search .= " and b.$q_date_field between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
            else if($fr_date && !$to_date)
                $sql_search .= " and b.$q_date_field between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
            else if(!$fr_date && $to_date)
                $sql_search .= " and b.$q_date_field between '$to_date 00:00:00' and '$to_date 23:59:59' ";

            // 브랜드
            if(isset($q_brand) && $q_brand)
                $sql_search .= " and b.brand_uid = '$q_brand' ";

            // 배송가능 지역
            if(isset($q_zone) && $q_zone)
                $sql_search .= " and b.zone = '$q_zone' ";

            // 상품재고
            if($fr_stock && $to_stock)
                $sql_search .= " and b.$q_stock_field between '$fr_stock' and '$to_stock' ";

            // 상품가격
            if($fr_price && $to_price)
                $sql_search .= " and b.$q_price_field between '$fr_price' and '$to_price' ";

            // 판매여부
            if(isset($q_isopen) && is_numeric($q_isopen))
                $sql_search .= " and b.isopen='$q_isopen' ";

            // 과세유형
            if(isset($q_notax) && is_numeric($q_notax))
                $sql_search .= " and b.notax = '$q_notax' ";

            // 상품 필수옵션
            if(isset($q_option) && is_numeric($q_option)) {
                if($q_option)
                    $sql_search .= " and b.opt_subject <> '' ";
                else
                    $sql_search .= " and b.opt_subject = '' ";
            }

            // 상품 추가옵션
            if(isset($q_supply) && is_numeric($q_supply)) {
                if($q_supply)
                    $sql_search .= " and b.spl_subject <> '' ";
                else
                    $sql_search .= " and b.spl_subject = '' ";
            }

            // 승인상태
            if(isset($q_state) && is_numeric($q_state))
                $sql_search .= " and b.shop_state = '$q_state' ";

            if(!$orderby) {
                $filed = "a.io_stock_qty";
                $sod = "asc";
            } else {
                $sod = $orderby;
            }

            $sql_order = " order by $filed $sod ";

            // 테이블의 전체 레코드수만 얻음
            $sql = " select count(*) as cnt $sql_common $sql_search ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            if(isset($_SESSION['ss_page_rows']))
                $page_rows = $_SESSION['ss_page_rows'];
            else
                $page_rows = 30;

            $rows = $page_rows;
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);

            $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
            $result = $this->query_model->returnArr($sql);

            $sflArr=array('','','','','','');
            $sflArr[0]=option_selected('gname', $sfl, '상품명');
            $sflArr[1]=option_selected('gcode', $sfl, '상품코드');
            $sflArr[2]=option_selected('maker', $sfl, '제조사');
            $sflArr[3]=option_selected('origin', $sfl, '원산지');
            $sflArr[4]=option_selected('model', $sfl, '모델명');
            $sflArr[5]=option_selected('explan', $sfl, '짧은설명');

            $scaSelect=array('','','','','');
            $scaSelect[0]=$this->funcomplex_lib->get_category_select_1('sel_ca1', $sca);
            $scaSelect[1]=$this->funcomplex_lib->get_category_select_2('sel_ca2', $sca);
            $scaSelect[2]=$this->funcomplex_lib->get_category_select_3('sel_ca3', $sca);
            $scaSelect[3]=$this->funcomplex_lib->get_category_select_4('sel_ca4', $sca);
            $scaSelect[4]=$this->funcomplex_lib->get_category_select_5('sel_ca5', $sca);
            $updateTimeTag=option_selected('update_time', $q_date_field, "최근수정일");
            $regTimeTag=option_selected('reg_time', $q_date_field, "최초등록일");
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);

            $brandStr="";
            $sql = "select * from shop_brand where br_user_yes='0' order by br_name asc ";
            $res = $this->query_model->returnArr($sql);
            $brandStr.=option_selected('', $q_brand, '전체');
            foreach($res as $key => $row){
                $brandStr.=option_selected($row['br_id'], $q_brand, $row['br_name']);
            }
            $q_zoneStr=array('','','','','','','','','','');
            $q_zoneStr[0]=option_selected('',  $q_zone, '전체');
            $q_zoneStr[1]=option_selected('전국', $q_zone, '전국');
            $q_zoneStr[2]=option_selected('강원도', $q_zone, '강원도');
            $q_zoneStr[3]=option_selected('경기도', $q_zone, '경기도');
            $q_zoneStr[4]=option_selected('경상도', $q_zone, '경상도');
            $q_zoneStr[5]=option_selected('서울/경기도', $q_zone, '서울/경기도');
            $q_zoneStr[6]=option_selected('서울특별시', $q_zone, '서울특별시');
            $q_zoneStr[7]=option_selected('전라도', $q_zone, '전라도');
            $q_zoneStr[8]=option_selected('제주도', $q_zone, '제주도');
            $q_zoneStr[9]=option_selected('충청도', $q_zone, '충청도'); 

            $stock_qtyTag=option_selected('stock_qty', $q_stock_field, "재고수량");
            $noti_qtyTag=option_selected('noti_qty', $q_stock_field, "통보수량");
            $price_fieldArr=array('','','','');
            $price_fieldArr[0]=option_selected('goods_price', $q_price_field, "판매가격"); 
            $price_fieldArr[1]=option_selected('supply_price', $q_price_field, "공급가격");
            $price_fieldArr[2]=option_selected('normal_price', $q_price_field, "시중가격");
            $price_fieldArr[3]=option_selected('gpoint', $q_price_field, "포인트");

            $q_isopenArr=array('','','','','');
            $q_isopenArr[0]=radio_checked('q_isopen', $q_isopen,  '', '전체');
            $q_isopenArr[1]=radio_checked('q_isopen', $q_isopen, '1', '진열');
            $q_isopenArr[2]=radio_checked('q_isopen', $q_isopen, '2', '품절');
            $q_isopenArr[3]=radio_checked('q_isopen', $q_isopen, '3', '단종');
            $q_isopenArr[4]=radio_checked('q_isopen', $q_isopen, '4', '중지');
            $q_optionArr=array('all'=>'','use'=>'','unuse'=>'');
            $q_optionArr['all']=radio_checked('q_option', $q_option,  '', '전체');
            $q_optionArr['use']=radio_checked('q_option', $q_option, '1', '사용');
            $q_optionArr['unuse']=radio_checked('q_option', $q_option, '0', '미사용');

            $q_notaxArr=array('all'=>'','1'=>'','0'=>'');
            $q_notaxArr['all']=radio_checked('q_notax', $q_notax,  '', '전체');
            $q_notaxArr['1']=radio_checked('q_notax', $q_notax, '1', '과세');
            $q_notaxArr['0']=radio_checked('q_notax', $q_notax, '0', '비과세');

            $q_supplyArr=array('all'=>'','1'=>'','0'=>'');
            $q_supplyArr['all']=radio_checked('q_supply', $q_supply,  '', '전체');
            $q_supplyArr['1']=radio_checked('q_supply', $q_supply, '1', '사용');
            $q_supplyArr['0']=radio_checked('q_supply', $q_supply, '0', '미사용');

            $q_stateArr=array('all'=>'','0'=>'','1'=>'','2'=>'');
            $q_stateArr['all']=radio_checked('q_state', $q_state,  '', '전체');
            $q_stateArr['0']=radio_checked('q_state', $q_state, '0', '승인'); 
            $q_stateArr['1']=radio_checked('q_state', $q_state, '1', '대기');
            $q_stateArr['2']=radio_checked('q_state', $q_state, '2', '보류'); 

            $page_rowsOpt=array('30'=>'','50'=>'','100'=>'','150'=>'');
            $page_rowsOpt['30']=option_selected('30',  $page_rows, '30줄 정렬');
            $page_rowsOpt['50']=option_selected('50',  $page_rows, '50줄 정렬');
            $page_rowsOpt['100']=option_selected('100',  $page_rows, '100줄 정렬');
            $page_rowsOpt['150']=option_selected('150',  $page_rows, '150줄 정렬');

            $gcodeStr=$this->gvcomplex_lib->subject_sort_link('gcode',$q2);
            $gnameStr=$this->gvcomplex_lib->subject_sort_link('gname',$q2);
            $regTimeStr=$this->gvcomplex_lib->subject_sort_link('reg_time',$q2);
            $isopenStr=$this->gvcomplex_lib->subject_sort_link('isopen',$q2);
            
            $shop_stateStr=$this->gvcomplex_lib->subject_sort_link('shop_state',$q2);
            $updateTimeStr=$this->gvcomplex_lib->subject_sort_link('update_time',$q2);
            $stockQtyStr=$this->gvcomplex_lib->subject_sort_link('stock_qty',$q2);
            $normalPriceStr=$this->gvcomplex_lib->subject_sort_link('normal_price',$q2);
            $supplyPriceStr=$this->gvcomplex_lib->subject_sort_link('supply_price',$q2);
            $goodPriceStr=$this->gvcomplex_lib->subject_sort_link('goods_price',$q2);
            $gpointStr=$this->gvcomplex_lib->subject_sort_link('gpoint',$q2);

            $i=0;
            $goods_optStockTable="";
            $gw_state=$this->config->item('gw_state');
            foreach($result as $key => $row){
                if($row['io_type'])
                    $io_type = '추가옵션';
                else
                    $io_type = '필수옵션';
        
                $bg = 'list'.($i%2);
                $goods_optStockTable.="
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"io_no[{$i}]\" value=\"{$row['io_no']}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">".$num--."</td>
                    <td rowspan=\"2\"><a href=\"".base_url()."shop/view?index_no={$row['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_it_image($row['gs_id'], $row['simg1'], 40, 40)."</a></td>
                    <td class=\"tal\">{$row['gcode']}</td>
                    <td rowspan=\"2\">{$io_type}</td>
                    <td class=\"tal\" colspan=\"2\">".$this->funcomplex_lib->get_text($row['gname'])."</td>
                    <td class=\"tar\">".number_format($row['io_stock_qty'])."</td>
                    <td rowspan=\"2\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row['gs_id']}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">".$gw_state[$row['shop_state']]."</td>
                    <td class=\"tal txt_succeed\">".$this->gvcomplex_lib->get_seller_name($row['mb_id'])."</td>
                    <td class=\"tal fc_00f\">".str_replace(' ',' &gt; ', $row['io_id'])."</td>
                    <td class=\"tar fc_00f\">".number_format($row['io_noti_qty'])."</td>
                </tr>";
                $i++;
            }
            if($i==0)
                $goods_optStockTable.='<tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';
            $jqueryUiDatePick=$this->admin_template->datePicker();
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');

            $data=array('snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub,
                        'jqueryUiDatePick'=>$jqueryUiDatePick,'code'=>$code,'sflArr'=>$sflArr,'stx'=>$stx,'scaSelect'=>$scaSelect,
                        'updateTimeTag'=>$updateTimeTag,'regTimeTag'=>$regTimeTag,'get_search_date'=>$get_search_date,'brandStr'=>$brandStr,
                        'q_zoneStr'=>$q_zoneStr,'stock_qtyTag'=>$stock_qtyTag,'noti_qtyTag'=>$noti_qtyTag,'fr_stock'=>$fr_stock,'to_stock'=>$to_stock,
                        'price_fieldArr'=>$price_fieldArr,'fr_price'=>$fr_price,'to_price'=>$to_price,'q_isopenArr'=>$q_isopenArr,'q_optionArr'=>$q_optionArr,
                        'q_notaxArr'=>$q_notaxArr,'q_supplyArr'=>$q_supplyArr,'q_stateArr'=>$q_stateArr,'page_rowsOpt'=>$page_rowsOpt,'requestUrl'=>$requestUrl,
                        'q1'=>$q1,'gcodeStr'=>$gcodeStr,'gnameStr'=>$gnameStr,'regTimeStr'=>$regTimeStr,'isopenStr'=>$isopenStr,
                        'shop_stateStr'=>$shop_stateStr,'updateTimeStr'=>$updateTimeStr,'stockQtyStr'=>$stockQtyStr,'normalPriceStr'=>$normalPriceStr,
                        'supplyPriceStr'=>$supplyPriceStr,'goodPriceStr'=>$goodPriceStr,'gpointStr'=>$gpointStr,'goods_optStockTable'=>$goods_optStockTable,'pagination'=>$pagination,
                        'page'=>$page,'total_count'=>$total_count);
            $this->load->view('mypage/seller/goods_optstock',$data);

        }
        private function seller_goods_brand_list($code,$snbTagStart,$snbTagEnd){
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $pg_title = "브랜드관리";
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();

            $qstr=$this->load->get_var('qstr');
            $config=$this->load->get_var('config');
            $seller=$this->load->get_var('seller');
            $stx=$this->input->get('stx');
            $sfl=$this->input->get('sfl');
            $orderby=$this->input->get('orderby');
            $page=$this->input->get('page');

            $filed=$this->input->get('filed');
            $filed=$this->db->escape_str($filed);
            
            $query_string = "code=$code$qstr";
            $q1 = $query_string;
            $q2 = $query_string."&page=$page";

            $sql_common = " from shop_brand ";
            $sql_search = " where br_user_yes = '0' ";

            if($sfl && $stx) {
                $sql_search .= " and $sfl like '%$stx%' ";
            }

            if(!$orderby) {
                $filed = "br_id";
                $sod = "desc";
            } else {
                $sod = $orderby;
            }

            $sql_order = " order by $filed $sod ";

            // 테이블의 전체 레코드수만 얻음
            $sql = " select count(*) as cnt $sql_common $sql_search ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            $rows = 30;
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);

            $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
            $result = $this->query_model->returnArr($sql);

            $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택수정\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">";
            $sflArr=array('','','');
            $sflArr[0]=option_selected('br_name', $sfl, '브랜드명 (KOR)');
            $sflArr[1]=option_selected('br_name_eng', $sfl, '브랜드명 (ENG)');
            $sflArr[2]=option_selected('mb_id', $sfl, '회원ID');

            $brNameTag=$this->gvcomplex_lib->subject_sort_link('br_name',$q2);
            $brNameEngTag=$this->gvcomplex_lib->subject_sort_link('br_name_eng',$q2);
            $mb_idTag=$this->gvcomplex_lib->subject_sort_link('mb_id',$q2);

            $i=0;
            $brandList="";
            foreach($result as $key => $row){
                $br_id = $row['br_id'];
                $sql="select count(*) as cnt from shop_goods where brand_uid=?";
                $param=array($row['br_id']);
                $row2 = $this->query_model->returnOneArr($sql,$param);
        
                if($row['mb_id'] == $seller['seller_code']) {		
                    $s_upd = "<a href='".base_url()."mypage/page?code=seller_goods_brand_form&w=u&br_id=$br_id$qstr&page=$page' class=\"btn_small\">수정</a>";
                    $readonly = "";
                    $disabled = "";
                    $td_bg = " style='background:yellow;'";
                } else {
                    $s_upd = '-';			
                    $readonly = " readonly style='background-color:#f4f4f4;'";
                    $disabled = " disabled";
                    $td_bg = "";
                }
        
                if($i==0)
                    $brandList.='<tbody class="list">';
                $bg = 'list'.($i%2);
                $brandList.="
                <tr class=\"{$bg}\">
                    <td {$td_bg}>			
                        <input type=\"hidden\" name=\"br_id[{$i}]\" value=\"{$br_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" {$disabled}>
                    </td>
                    <td>".$num--."</td>
                    <td><input type=\"text\" name=\"br_name[{$i}]\" value=\"{$row['br_name']}\" class=\"frm_input\" {$readonly}></td>
                    <td><input type=\"text\" name=\"br_name_eng[{$i}]\" value=\"{$row['br_name_eng']}\"class=\"frm_input\" {$readonly}></td>
                    <td>".($row['mb_id'] == 'admin' ? "본사":$row['mb_id'])."</td>
                    <td>".number_format($row2['cnt'])."</td>
                    <td><a href=\"".base_url()."/shop/brandlist?br_id={$br_id}\" target=\"_blank\" class=\"btn_small grey\">브랜드 바로가기</a></td>
                    <td>{$s_upd}</td>
                </tr>";
                $i++;
            }
            if($i==0)
                $brandList.= '<tbody><tr><td colspan="8" class="empty_table">자료가 없습니다.</td></tr>';
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $data=array('snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub,
                        'btn_frmline'=>$btn_frmline,'q1'=>$q1,'page'=>$page,'sflArr'=>$sflArr,'stx'=>$stx,'total_count'=>$total_count,'code'=>$code,
                        'brNameTag'=>$brNameTag,'brNameEngTag'=>$brNameEngTag,'mb_idTag'=>$mb_idTag,'brandList'=>$brandList,'pagination'=>$pagination);
            $this->load->view('mypage/seller/goods_brandlist',$data);
        }
        private function seller_goods_qa($code,$snbTagStart,$snbTagEnd){
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $pg_title = "상품 문의관리";
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();

            $qstr=$this->load->get_var('qstr');
            $config=$this->load->get_var('config');
            $seller=$this->load->get_var('seller');

            $page=$this->input->get('page');
            $sfl=$this->input->get('sfl');
            $stx=$this->input->get('stx');
            $sst=$this->input->get('sst');
            $orderby=$this->input->get('orderby');

            $filed=$this->input->get('filed');
            $filed=$this->db->escape_str($filed);

            $query_string = "code=$code$qstr";
            $q1 = $query_string;
            $q2 = $query_string."&page=$page";

            $sql_common = " from shop_goods_qa ";
            $sql_search = " where seller_id = '{$seller['seller_code']}' ";

            if($sfl && $stx) {
                $sql_search .= " and $sfl like '%$stx%' ";
            }

            if($sst) {
                $sql_search .= " and iq_ty = '$sst' ";
            }

            if(!$orderby) {
                $filed = "iq_reply";
                $sod = "asc, iq_id desc";
            } else {
                $sod = $orderby;
            }

            $sql_order = " order by $filed $sod ";

            // 테이블의 전체 레코드수만 얻음
            $sql = " select count(*) as cnt $sql_common $sql_search ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            $rows = 30;
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);

            $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
            $result = $this->query_model->returnArr($sql);
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">";
            $sstArr=array('goods'=>"",'deliver'=>"",'return'=>"",'change'=>"",'etc'=>"");
            $sstArr['goods']=get_selected($sst, '상품');
            $sstArr['deliver']=get_selected($sst, '배송');
            $sstArr['return']=get_selected($sst, '반품/환불/취소');
            $sstArr['change']=get_selected($sst, '교환/변경');
            $sstArr['etc']=get_selected($sst, '기타');

            $sflArr=array('','','','','','');
            $sflArr[0]=get_selected($sfl, 'iq_name');
            $sflArr[1]=get_selected($sfl, 'iq_email');
            $sflArr[2]=get_selected($sfl, 'iq_hp');
            $sflArr[3]=get_selected($sfl, 'iq_subject');
            $sflArr[4]=get_selected($sfl, 'iq_question');
            $sflArr[5]=get_selected($sfl, 'iq_answer');

            $iqTyTag=$this->gvcomplex_lib->subject_sort_link('iq_ty',$q2);
            $iqSubject=$this->gvcomplex_lib->subject_sort_link('iq_subject',$q2);
            $iqName=$this->gvcomplex_lib->subject_sort_link('iq_name',$q2);
            $iqTime=$this->gvcomplex_lib->subject_sort_link('iq_time',$q2);
            $iqReply=$this->gvcomplex_lib->subject_sort_link('iq_reply',$q2);

            $goods_qaTable="";
            $i=0;
            foreach($result as $key => $row){
                $iq_id = $row['iq_id'];
                
                $iq_url = "code=seller_goods_qa_form&w=u&iq_id=$iq_id$qstr&page=$page";
                $iq_upd = "<a href=\"".base_url()."mypage/page?{$iq_url}\" class=\"btn_small\">수정</a>";
                $iq_subject = "<a href=\"".base_url()."mypage/page?{$iq_url}\">".cut_str(htmlspecialchars($row['iq_subject']),50)."</a>";
        
                if($i==0)
                    $goods_qaTable.='<tbody class="list">';
        
                $bg = 'list'.($i%2);
                $goods_qaTable.="
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"iq_id[{$i}]\" value=\"{$iq_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>".$num--."</td>
                    <td>{$row['iq_ty']}</td>
                    <td class=\"tal\">{$iq_subject}</td>
                    <td>{$row['iq_name']}</a></td>
                    <td>".substr($row['iq_time'],0,10)."</td>
                    <td>".($row['iq_answer']?'yes':'')."</td>
                    <td>{$iq_upd}</td>
                </tr>";
                $i++;
            }
            if($i==0)
                $goods_qaTable.='<tbody><tr><td colspan="8" class="empty_table">자료가 없습니다.</td></tr>';

            $data=array('code'=>$code,'snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub,
                        'pagination'=>$pagination,'sstArr'=>$sstArr,'sflArr'=>$sflArr,'stx'=>$stx,'q1'=>$q1,'page'=>$page,'total_count'=>$total_count,'btn_frmline'=>$btn_frmline,
                        'iqTyTag'=>$iqTyTag,'iqSubject'=>$iqSubject,'iqName'=>$iqName,'iqTime'=>$iqTime,'iqReply'=>$iqReply,'goods_qaTable'=>$goods_qaTable);
            $this->load->view('mypage/seller/goods_qa',$data);

        }
        private function seller_goods_review($code,$snbTagStart,$snbTagEnd){
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $pg_title = "상품 평점관리";
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();

            $qstr=$this->load->get_var('qstr');
            $config=$this->load->get_var('config');
            $seller=$this->load->get_var('seller');

            $page=$this->input->get('page');
            $sfl=$this->input->get('sfl');
            $stx=$this->input->get('stx');
            $orderby=$this->input->get('orderby');

            $filed=$this->input->get('filed');
            $filed=$this->db->escape_str($filed);

            $query_string = "code=$code$qstr";

            $q1 = $query_string;
            $q2 = $query_string."&page=$page";
            
            $sql_common = " from shop_goods_review ";
            $sql_search = " where seller_id = '{$seller['seller_code']}' ";
            
            if($sfl && $stx) {
                $sql_search .= " and $sfl like '%$stx%' ";
            }
            
            if(!$orderby) {
                $filed = "index_no";
                $sod = "desc";
            } else {
                $sod = $orderby;
            }
            
            $sql_order = " order by $filed $sod ";
            // 테이블의 전체 레코드수만 얻음
            $sql = " select count(*) as cnt $sql_common $sql_search ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            $rows = 30;
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);

            $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
            $result = $this->query_model->returnArr($sql);

            $btn_frmline = '
            <input type="submit" name="act_button" value="선택삭제" class="btn_lsmall bx-white" onclick="document.pressed=this.value">';
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $mb_idTag=option_selected('mb_id', $sfl, '작성자');
            $seller_idTag=option_selected('seller_id', $sfl, '판매자');
            $mb_idLinkTag=$this->gvcomplex_lib->subject_sort_link('mb_id',$q2);
            $reg_timeLinkTag=$this->gvcomplex_lib->subject_sort_link('reg_time',$q2);
            $scoreLinkTag=$this->gvcomplex_lib->subject_sort_link('score',$q2);

            $i=0;
            $goods_reviewTable="";
            foreach($result as $key => $row){
                $gs = $this->global_lib->get_goods($row['gs_id'], 'simg1, gname');		
        
                if($i==0)
                    $goods_reviewTable.='<tbody class="list">';
        
                $bg = 'list'.($i%2);
                $goods_reviewTable.="
                <tr class=\"{$bg}\">
                    <td>			
                        <input type=\"hidden\" name=\"index_no[{$i}]\" value=\"{$row['index_no']}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>".$num--."</td>
                    <td><a href=\"".base_url()."shop/view?index_no={$row['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_it_image($row['gs_id'], $gs['simg1'], 40, 40)."</a></td>
                    <td class=\"tal\"><a href=\"".base_url()."shop/view?index_no={$row['gs_id']}\" target=\"_blank\"><b>".$this->funcomplex_lib->get_text($gs['gname'])."</b></a><p class=\"mart5 fc_137\">".$this->funcomplex_lib->get_text($row['memo'])."</p></td>
                    <td>{$row['mb_id']}</td>
                    <td>".substr($row['reg_time'],0,10)."</td>
                    <td><img src=\"".base_url()."img/sub/score_{$row['score']}.gif\"></td>
                </tr>";
                $i++;
            }
            if($i==0)
                $goods_reviewTable.='<tbody><tr><td colspan="7" class="empty_table">자료가 없습니다.</td></tr>';

            $data=array('pagination'=>$pagination,'btn_frmline'=>$btn_frmline,'code'=>$code,'snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,
                        'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub,'mb_idTag'=>$mb_idTag,'seller_idTag'=>$seller_idTag,
                        'stx'=>$stx,'q1'=>$q1,'page'=>$page,'total_count'=>$total_count,'mb_idLinkTag'=>$mb_idLinkTag,'reg_timeLinkTag'=>$reg_timeLinkTag,
                        'scoreLinkTag'=>$scoreLinkTag,'goods_reviewTable'=>$goods_reviewTable);
            $this->load->view('mypage/seller/goods_review',$data);
        }
        private function seller_odr_list($code,$snbTagStart,$snbTagEnd){
            $pg_title = '주문리스트(전체)';
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $config=$this->load->get_var('config');
            $qstr=$this->load->get_var('qstr');
            $seller=$this->load->get_var('seller');

            $fr_date=$this->input->get('fr_date');
            $to_date=$this->input->get('to_date');
            $sel_field=$this->input->get('sel_field');
            $od_settle_case=$this->input->get('od_settle_case');
            $od_status=$this->input->get('od_status');
            $od_final=$this->input->get('od_final');
            $od_memo=$this->input->get('od_memo');
            $od_receipt_point=$this->input->get('od_receipt_point');
            $od_coupon=$this->input->get('od_coupon');
            $od_escrow=$this->input->get('od_escrow');
            $page=$this->input->get('page');
            $sfl=$this->input->get('sfl');
            $stx=$this->input->get('stx');
            
            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

            if(isset($sel_field))		 $qstr .= "&sel_field=$sel_field";
            if(isset($od_settle_case))	 $qstr .= "&od_settle_case=".urlencode($od_settle_case);
            if(isset($od_status))		 $qstr .= "&od_status=$od_status";
            if(isset($od_final))		 $qstr .= "&od_final=$od_final";
            if(isset($od_memo))			 $qstr .= "&od_memo=$od_memo";
            if(isset($od_receipt_point)) $qstr .= "&od_receipt_point=$od_receipt_point";
            if(isset($od_coupon))		 $qstr .= "&od_coupon=$od_coupon";
            if(isset($od_escrow))		 $qstr .= "&od_escrow=$od_escrow";

            $query_string = "code=$code$qstr";
            $q1 = $query_string;
            $q2 = $query_string."&page=$page";

            $sql_common = " from shop_order ";

            $where = array();
            $where[] = " seller_id = '{$seller['seller_code']}' ";

            if($code == 'seller_odr_list') // 주문리스트(전체)
                $where[] = " dan > 1 ";
            else
                $where[] = " dan = '".conv_number($code)."' ";

            if($sfl && $stx)
                $where[] = " $sfl like '%$stx%' ";

            if($od_settle_case)
                $where[] = " paymethod = '$od_settle_case' ";

            if(is_numeric($od_status))
                $where[] = " dan = '$od_status' ";

            if(is_numeric($od_final))
                $where[] = " user_ok = '$od_final' ";

            if($od_memo)
                $where[] = " memo <> '' ";

            if($od_receipt_point)
                $where[] = " use_point != 0 ";

            if($od_coupon)
                $where[] = " coupon_price != 0 ";

            if($od_escrow)
                $where[] = " od_escrow = 1 ";

            if($fr_date && $to_date)
                $where[] = " left({$sel_field},10) between '$fr_date' and '$to_date' ";
            else if($fr_date && !$to_date)
                $where[] = " left({$sel_field},10) between '$fr_date' and '$fr_date' ";
            else if(!$fr_date && $to_date)
                $where[] = " left({$sel_field},10) between '$to_date' and '$to_date' ";

            if($where) {
                $sql_search = ' where '.implode(' and ', $where);
            }

            $sql_group = " group by od_id ";
            $sql_order = " order by index_no desc ";

            // 테이블의 전체 레코드수만 얻음
            $sql = " select od_id {$sql_common} {$sql_search} {$sql_group} ";
            $resultObj = $this->query_model->returnQueryObj($sql);
            $total_count =$resultObj->num_rows();

            if(isset($_SESSION['ss_page_rows']))
                $page_rows = $_SESSION['ss_page_rows'];
            else
                $page_rows = 30;

            $rows = $page_rows;
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);

            $sql = " select * {$sql_common} {$sql_search} {$sql_group} {$sql_order} limit {$from_record}, {$rows} ";
            $result = $this->query_model->returnArr($sql);

            $tot_orderprice = 0; // 총주문액
            $sql = " select od_id {$sql_common} {$sql_search} {$sql_group} {$sql_order} ";
            $res = $this->query_model->returnArr($sql);
            foreach($res as $key => $row){
                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
                $tot_orderprice += $amount['buyprice'];
            }
            $jqueryUiDatePick=$this->admin_template->datePicker();

            $btn_frmline = "
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"".base_url()."MypageProcess/seller_odr_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>
            <a href=\"" . base_url() . "data/download/한류입점사 주문관리 메뉴얼.pdf\" download class=\"btn_lsmall\">주문관리 설명서</a>";
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $sflOpt=array('','','','','','','','','','');
            $sflOpt[0]=option_selected('od_id', $sfl, '주문번호');
            $sflOpt[1]=option_selected('od_no', $sfl, '일련번호');
            $sflOpt[2]=option_selected("mb_id", $sfl, '회원아이디');
            $sflOpt[3]=option_selected('name', $sfl, '주문자명');
            $sflOpt[4]=option_selected('deposit_name', $sfl, '입금자명');
            $sflOpt[5]=option_selected('bank', $sfl, '입금계좌');
            $sflOpt[6]=option_selected('b_name', $sfl, '수령자명');
            $sflOpt[7]=option_selected('b_telephone', $sfl, '수령자집전화');
            $sflOpt[8]=option_selected('b_cellphone', $sfl, '수령자핸드폰');
            $sflOpt[9]=option_selected('delivery_no', $sfl, '운송장번호');

            $sel_fieldOpt=array('','','','','','','');
            $sel_fieldOpt[0]=option_selected('od_time', $sel_field, "주문일");
            $sel_fieldOpt[1]=option_selected('receipt_time', $sel_field, "입금완료일");
            $sel_fieldOpt[2]=option_selected('delivery_date', $sel_field, "배송일"); 
            $sel_fieldOpt[3]=option_selected('invoice_date', $sel_field, "배송완료일");
            $sel_fieldOpt[4]=option_selected('user_date', $sel_field, "구매확정일");
            $sel_fieldOpt[5]=option_selected('cancel_date', $sel_field, "주문취소일");
            $sel_fieldOpt[6]=option_selected('change_date', $sel_field, "교환완료일");
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);

            $payMethod=array('','','','','','');
            $payMethod[0]=radio_checked('od_settle_case', $od_settle_case,  '', '전체');
            $payMethod[1]=radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
            $payMethod[2]=radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
            $payMethod[3]=radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
            $payMethod[4]=radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
            $payMethod[5]=radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

            $gw_status=$this->config->item('gw_status');
            $od_statusArr=array('','','','','','','','','','');
            $od_statusArr[0]=radio_checked('od_status', $od_status,  '', '전체');
            $od_statusArr[1]="";
            $od_statusArr[2]=radio_checked('od_status', $od_status, '2', $gw_status[2]);
            $od_statusArr[3]=radio_checked('od_status', $od_status, '3', $gw_status[3]);
            $od_statusArr[4]=radio_checked('od_status', $od_status, '4', $gw_status[4]);
            $od_statusArr[5]=radio_checked('od_status', $od_status, '5', $gw_status[5]); 
            $od_statusArr[6]=radio_checked('od_status', $od_status, '6', $gw_status[6]);
            $od_statusArr[7]=radio_checked('od_status', $od_status, '7', $gw_status[7]);
            $od_statusArr[8]=radio_checked('od_status', $od_status, '8', $gw_status[8]);
            $od_statusArr[9]=radio_checked('od_status', $od_status, '9', $gw_status[9]);

            $od_finalArr=array('','','');
            $od_finalArr[0]=radio_checked('od_final', $od_final,  '', '전체');
            $od_finalArr[1]=radio_checked('od_final', $od_final, '1', '구매확정');
            $od_finalArr[2]=radio_checked('od_final', $od_final, '0', '구매미확정');

            $ectSelectChk=array('','','','');
            $ectSelectChk[0]=check_checked('od_memo', $od_memo, 'Y', '배송메세지');
            $ectSelectChk[1]=check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
            $ectSelectChk[2]=check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
            $ectSelectChk[3]=check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

            $page_rowsOpt=array('30'=>'','50'=>'','100'=>'','150'=>'');
            $page_rowsOpt['30']=option_selected('30',  $page_rows, '30줄 정렬');
            $page_rowsOpt['50']=option_selected('50',  $page_rows, '50줄 정렬');
            $page_rowsOpt['100']=option_selected('100',  $page_rows, '100줄 정렬');
            $page_rowsOpt['150']=option_selected('150',  $page_rows, '150줄 정렬');

            $i=0;
            $odr_listTable="";
            foreach($result as $key => $row){
                $bg = 'list'.($i%2);
        
                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
                $sodr = $this->funcomplex_lib->get_order_list($row, $amount);
        
                $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
                $param=array($row['od_id']);
                $resObj = $this->query_model->returnQueryObj($sql,$param);
                $rowspan = $resObj->num_rows();
                $res=$resObj->result_array();
                $k=0;
                foreach($res as $key => $row2){
                    $gs = unserialize($row2['od_goods']);
                    $odr_listTable.="
                    <tr class=\"{$bg}\">";
                        if($k == 0) {
                            $odr_listTable.="
                            <td rowspan=\"{$rowspan}\">".$num--."</td>
                            <td rowspan=\"{$rowspan}\">
                                ".substr($row['od_time'],2,14)."
                                ".$sodr['disp_test']."
                            </td>
                            <td rowspan=\"{$rowspan}\">
                                <a href=\"".base_url()."mypage/seller_odr_form?od_id={$row['od_id']}\" onclick=\"win_open(this,'seller_odr_form','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                                {$sodr['disp_mobile']}
                            </td>
                            <td rowspan=\"{$rowspan}\">
                                <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                                <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                                <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                            </td>";
                        }
                        $odr_listTable.="
                        <td class=\"td_img\"><a href=\"".base_url()."shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30)."</a></td>
                        <td class=\"td_itname\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_text($gs['gname'])."</a></td>
                        <td>".number_format($row2['sum_qty'])."</td>
                        <td class=\"tar\">".number_format($row2['goods_price'])."</td>
                        <td class=\"tar\">".number_format($row2['baesong_price'])."</td>
                        <td>".$gw_status[$row2['dan']]."</td>";
                    if($k == 0) {
                        $odr_listTable.="
                        <td rowspan=\"{$rowspan}\">
                            ".$sodr['disp_od_name']."
                            ".$sodr['disp_mb_id']."
                        </td>
                        <td rowspan=\"{$rowspan}\">{$row['b_name']}</td>
                        <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>
                        <td rowspan=\"{$rowspan}\">{$sodr['disp_paytype']}</td>";
                    }
                    $k++;
                }
                $i++;
            }
            if($i==0)
                $odr_listTable.='<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';

            $data = array(
                'code' => $code,
                'snbTagStart' => $snbTagStart,
                'snbTagEnd' => $snbTagEnd,
                'btn_frmline' => $btn_frmline,
                'pagination' => $pagination,
                'jqueryUiDatePick' => $jqueryUiDatePick,
                'admin_head_sub' => $admin_head_sub,
                'admin_footer_sub' => $admin_footer_sub,
                'sflOpt' => $sflOpt,
                'stx' => $stx,
                'sel_fieldOpt' => $sel_fieldOpt,
                'get_search_date' => $get_search_date,
                'payMethod' => $payMethod,
                'od_statusArr' => $od_statusArr,
                'od_finalArr' => $od_finalArr,
                'ectSelectChk' => $ectSelectChk,
                'page_rowsOpt' => $page_rowsOpt,
                'requestUrl' => $requestUrl,
                'tot_orderprice' => $tot_orderprice,
                'q1' => $q1,
                'page' => $page,
                'odr_listTable' => $odr_listTable,
                'total_count' => $total_count
            );
            $this->load->view('mypage/seller/ord_list',$data);
        }
        public function seller_odr_print(){
            $od_id=$this->input->get('od_id');
            $seller=$this->load->get_var('seller');
            if(!$od_id)
                alert_close("주문번호가 넘어오지 않았습니다.");
            $sql = " select * from shop_order where od_id IN ({$od_id}) and seller_id = ? group by od_id order by index_no desc ";
            $param=array($seller['seller_code']);

            $resultObj = $this->query_model->returnQueryObj($sql,$param);
            $cnt = $resultObj->num_rows();
            $result=$resultObj->result_array();
            if(!$cnt)
                alert_close("출력할 자료가 없습니다.");
            $title = "주문내역";
            $this->admin_template->admin_head($title);
            $printStr="";
            $i=0;
            $gw_status=$this->config->item('gw_status');
            $tot_tot_qty=0;
            $tot_tot_price=0;
            $tot_tot_price2=0;
            $tot_tot_baesong=0;
            foreach($result as $key => $row){
                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' "); // 주문별 합계
                $od_addr = sprintf("(%s)", $row['zip']).' '.$this->funcomplex_lib->print_address($row['addr1'], $row['addr2'], $row['addr3'], $row['addr_jibeon']);
                $od_b_addr = sprintf("(%s)", $row['b_zip']).' '.$this->funcomplex_lib->print_address($row['b_addr1'], $row['b_addr2'], $row['b_addr3'], $row['b_addr_jibeon']);

                $od_addr  = ($od_addr) ? $od_addr : '입력안함';
                $od_tel   = ($row['telephone']) ? $row['telephone'] : '입력안함';
                $od_hp    = ($row['cellphone']) ? $row['cellphone'] : '입력안함';
                $od_b_tel = ($row['b_telephone']) ? $row['b_telephone'] : '입력안함';
                $od_b_hp  = ($row['b_cellphone']) ? $row['b_cellphone'] : '입력안함';

                // 보내는 사람과 받는 사람이 완전 일치하면 간단하게 출력
                // 보내는 사람과 받는 사람이 부분 일치하더라도 원래 내용을 모두 출력
                if($row['name'] == $row['b_name'] && $od_addr == $od_b_addr && $od_tel == $od_b_tel &&  $od_hp == $od_b_hp && $od_hp != "&nbsp;") $samesamesame = 1;
                else $samesamesame = '';

                $od_memo = ($row['memo']) ? $this->funcomplex_lib->get_text(stripslashes($row['memo'])) : '';
                $od_shop_memo = ($row['shop_memo']) ? $this->funcomplex_lib->get_text(stripslashes($row['shop_memo'])) : '';
                $printStr.="
                <div class=\"sodr_print_pop_list\">
                    <h2>주문번호 {$row['od_id']}</h2>
                    <h3>주문하신 분 : ".$this->funcomplex_lib->get_text($row['name'])."</h3>
                    <dl>
                        <dt>주소</dt>
                        <dd>".$this->funcomplex_lib->get_text($od_addr)."</dd>
                        <dt>휴대폰</dt>
                        <dd>".$this->funcomplex_lib->get_text($od_hp)."</dd>
                        <dt>전화번호</dt>
                        <dd>".$this->funcomplex_lib->get_text($od_tel)."</dd>
                    </dl>";
                    if($samesamesame) {
                        $printStr.="<p class=\"sodr_print_pop_same\">보내는 사람과 받는 사람이 동일합니다.</p>";
                    } else {
                        $printStr.="
                        <h3>받으시는 분 : ".$this->funcomplex_lib->get_text($row['b_name'])."</h3>
                        <dl>
                            <dt>주소</dt>
                            <dd>".$this->funcomplex_lib->get_text($od_b_addr)."</dd>
                            <dt>휴대폰</dt>
                            <dd>".$this->funcomplex_lib->get_text($od_b_hp)."</dd>
                            <dt>전화번호</dt>
                            <dd>".$this->funcomplex_lib->get_text($od_b_tel)."</dd>
                        </dl>";
                    }
                    $printStr.="
                        <h3>주문 목록</h3>
                        <div class=\"tbl_head01\">
                            <table id=\"sodr_list\">
                            <colgroup>
                                <col>
                                <col class=\"w90\">
                                <col class=\"w60\">
                                <col class=\"w90\">
                                <col class=\"w90\">
                            </colgroup>
                            <thead>
                            <tr>
                                <th scope=\"col\">주문상품</th>
                                <th scope=\"col\">판매가</th>
                                <th scope=\"col\">수량</th>
                                <th scope=\"col\">소계</th>
                                <th scope=\"col\">상태</th>
                            </tr>
                            </thead>
                            <tbody>";
                        $sub_tot_price1 = $sub_tot_price2 = $sub_tot_qty = 0;

                        $sql2 = "select * from shop_cart where od_id = ? group by gs_id order by io_type, index_no ";
                        $param2=array($row['od_id']);
                        $res2 = $this->query_model->returnArr($sql2,$param2);
                        foreach($res2 as $key => $row2){
                            $sodr = $this->global_lib->get_order($row2['od_no'], 'od_goods, dan, seller_id');
                            $gs = unserialize($sodr['od_goods']);

                            // 자신의 상품주문건만 노출한다.
                            if($sodr['seller_id'] != $seller['seller_code'])
                                continue;

                            $it_name = stripslashes($gs['gname']);
                            $it_options = $this->funcomplex_lib->print_complete_options($row2['gs_id'], $row2['od_id']);
                            if($it_options){
                                $it_name .= '<div class="sod_opt">'.$it_options.'</div>';
                            }

                            // 합계금액 계산
                            $sql = " select SUM(IF(io_type = 1, (io_price), (io_price + ct_price))) as price,
                                            SUM(IF(io_type = 1, (io_price * ct_qty), ((io_price + ct_price) * ct_qty))) as sell_price,
                                            SUM(IF(io_type = 1, 0, ct_qty)) as qty
                                    from shop_cart
                                    where gs_id = ?
                                        and od_id = ?";
                            $param=array($row2['gs_id'],$row2['od_id']);
                            $sum = $this->query_model->returnOneArr($sql,$param);

                            $sub_tot_qty	+= $sum['qty'];
                            $sub_tot_price1 += $sum['price'];
                            $sub_tot_price2 += $sum['sell_price'];
                            $printStr.="
                            <tr>
                                <td class=\"tal\">{$it_name}</td>
                                <td class=\"tar\">".number_format($sum['price'])."</td>
                                <td class=\"tac\">".number_format($sum['qty'])."</td>
                                <td class=\"tar\">".number_format($sum['sell_price'])."</td>
                                <td class=\"tac\">".$gw_status[$sodr['dan']]."</td>
                            </tr>";
                        }
                        $printStr.="
                        <tr>
                            <td class=\"tac\">합계</td>
                            <td class=\"tar\">".number_format($sub_tot_price1)."</td>
                            <td class=\"tac\">".number_format($sub_tot_qty)."</td>
                            <td class=\"tar\">".number_format($sub_tot_price2)."</td>
                            <td></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class=\"tar\" colspan=\"5\">
                                (상품금액 : ".display_price($amount['price'])." +
                                배송비 : ".display_price($amount['baesong']).") -
                                (쿠폰할인 : ".display_price($amount['coupon'])."> +
                                포인트결제 : ".display_price($amount['usepoint']).") =
                                총계 : <strong class=\"fc_red\">".display_price($amount['useprice'])."</strong>
                            </td>
                        </tr>
                        </tfoot>
                        </table>
                    </div>";
                    $tot_tot_qty     += $amount['qty'];
                    $tot_tot_price   += $amount['buyprice'];
                    $tot_tot_price2  += $amount['useprice'];
                    $tot_tot_baesong += $amount['baesong'];

                    if($od_memo)
                        $printStr.="<p class=\"padt10\"><strong>비고</strong> $od_memo</p>";

                    if($od_shop_memo)
                        $printStr.="<p class=\"padt10\"><strong>메모</strong> $od_shop_memo</p>";
                $printStr.="                    
                </div>";
            }

            $data=array('tot_tot_qty'=>$tot_tot_qty,'tot_tot_price'=>$tot_tot_price,'tot_tot_price2'=>$tot_tot_price2,'tot_tot_baesong'=>$tot_tot_baesong,
                        'printStr'=>$printStr);
            $this->load->view('mypage/print/odr_print',$data);
        }
        private function seller_odr_2($code,$snbTagStart,$snbTagEnd){
            $pg_title = '입금완료(배송요청)';
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            
            
            #seller_odr_query 한세트
            $ord_query=$this->seller_odr_query($code);
            $tot_orderprice=$ord_query['tot_orderprice'];
            $jqueryUiDatePick=$ord_query['jqueryUiDatePick'];
            $result=$ord_query['result'];
            $q1=$ord_query['q1'];
            $total_page=$ord_query['total_page'];
            $page=$ord_query['page'];
            $sfl=$ord_query['sfl'];
            $fr_date=$ord_query['fr_date'];
            $to_date=$ord_query['to_date'];
            $od_settle_case=$ord_query['od_settle_case'];
            $od_memo=$ord_query['od_memo'];
            $od_receipt_point=$ord_query['od_receipt_point'];
            $sel_field=$ord_query['sel_field'];
            $od_coupon=$ord_query['od_coupon'];
            $od_escrow=$ord_query['od_escrow'];
            $page_rows=$ord_query['page_rows'];
            $stx=$ord_query['stx'];
            $total_count=$ord_query['total_count'];
            $sql_common=$ord_query['sql_common'];
            $sql_search=$ord_query['sql_search'];
            $num=$ord_query['num'];
            #seller_odr_query 한세트 끝

            $config=$this->load->get_var('config');
            $qstr=$this->load->get_var('qstr');
            $seller=$this->load->get_var('seller');
            $btn_frmline ="
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"".base_url()."MypageProcess/seller_odr_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";

            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $sflOpt=array('','','','','','','','','');
            $sflOpt[0]=option_selected('od_id', $sfl, '주문번호');
            $sflOpt[1]=option_selected('od_no', $sfl, '일련번호');
            $sflOpt[2]=option_selected("mb_id", $sfl, '회원아이디');
            $sflOpt[3]=option_selected('name', $sfl, '주문자명');
            $sflOpt[4]=option_selected('deposit_name', $sfl, '입금자명');
            $sflOpt[5]=option_selected('bank', $sfl, '입금계좌');
            $sflOpt[6]=option_selected('b_name', $sfl, '수령자명');
            $sflOpt[7]=option_selected('b_telephone', $sfl, '수령자집전화');
            $sflOpt[8]=option_selected('b_cellphone', $sfl, '수령자핸드폰');

            $sel_fieldOpt=array('','');
            $sel_fieldOpt[0]=option_selected('od_time', $sel_field, "주문일");
            $sel_fieldOpt[1]=option_selected('receipt_time', $sel_field, "입금완료일");
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);

            $payMethod=array('','','','','','');
            $payMethod[0]=radio_checked('od_settle_case', $od_settle_case,  '', '전체');
            $payMethod[1]=radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
            $payMethod[2]=radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
            $payMethod[3]=radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
            $payMethod[4]=radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
            $payMethod[5]=radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

            $ectSelectChk=array('','','','');
            $ectSelectChk[0]=check_checked('od_memo', $od_memo, 'Y', '배송메세지');
            $ectSelectChk[1]=check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
            $ectSelectChk[2]=check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
            $ectSelectChk[3]=check_checked('od_escrow', $od_escrow, 'Y', '에스크로');
            $page_rowsOpt=array('30'=>'','50'=>'','100'=>'','150'=>'');
            $page_rowsOpt['30']=option_selected('30',  $page_rows, '30줄 정렬');
            $page_rowsOpt['50']=option_selected('50',  $page_rows, '50줄 정렬');
            $page_rowsOpt['100']=option_selected('100',  $page_rows, '100줄 정렬');
            $page_rowsOpt['150']=option_selected('150',  $page_rows, '150줄 정렬');
            $chk_cnt = 0;

            $i=0;
            $odr2Table="";
            foreach($result as $key => $row){
                $bg = 'list'.($i%2);

                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
                $sodr = $this->funcomplex_lib->get_order_list($row, $amount, "and dan IN ('4','5')");

                $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
                $param=array($row['od_id']);
                $resObj = $this->query_model->returnQueryObj($sql,$param);
                $res=$resObj->result_array();
                $rowspan = $resObj->num_rows();
                $k=0;
                foreach($res as $key => $row2){
                    $gs = unserialize($row2['od_goods']);
                    $odr2Table.="
                    <tr class=\"{$bg}\">";
                    if($k == 0) {
                        $odr2Table.="
                        <td rowspan=\"{$rowspan}\">".$num--."</td>
                        <td rowspan=\"{$rowspan}\">
                            ".substr($row['od_time'],2,14)."
                            {$sodr['disp_test']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"".base_url()."mypage/seller_odr_form?od_id={$row['od_id']}\" onclick=\"win_open(this,'seller_odr_form','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                            {$sodr['disp_mobile']}
                            {$sodr['disp_baesong']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                            <label for=\"sit_sel_{$i}\" class=\"sound_only\">전체선택</label>
                            <input type=\"checkbox\" name=\"it_sel[]\" id=\"sit_sel_{$i}\">
                        </td>";
                    }
                    $odr2Table.="
                    <td class=\"td_chk\">
                        <input type=\"hidden\" name=\"od_no[{$chk_cnt}]\" value=\"{$row2['od_no']}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$chk_cnt}\" id=\"chk_{$chk_cnt}\" class=\"sct_sel_{$i}\">
                        <label for=\"chk_{$chk_cnt}\" class=\"sound_only\">{$gs['gname']}</label>
                    </td>
                    <td class=\"td_imgline\"><a href=\"".base_url()."shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30)."</a></td>
                    <td class=\"td_itname\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_text($gs['gname'])."</a></td>";
                    if($k == 0) {
                        $odr2Table.="
                        <td rowspan=\"{$rowspan}\">
                            {$sodr['disp_od_name']}
                            {$sodr['disp_mb_id']}
                        </td>
                        <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>
                        <td rowspan=\"{$rowspan}\">{$sodr['disp_paytype']}</td>";
                    }
                    $chk_cnt++;
                    $k++;
                }
                $i++;
            }
            if($i==0)
                $odr2Table.='<tr><td colspan="10" class="empty_table">자료가 없습니다.</td></tr>';
            $data = array(
                'code' => $code,
                'snbTagStart' => $snbTagStart,
                'snbTagEnd' => $snbTagEnd,
                'admin_head_sub' => $admin_head_sub,
                'admin_footer_sub' => $admin_footer_sub,
                'jqueryUiDatePick' => $jqueryUiDatePick,
                'pagination' => $pagination,
                'sflOpt' => $sflOpt,
                'stx' => $stx,
                'sel_fieldOpt' => $sel_fieldOpt,
                'get_search_date' => $get_search_date,
                'payMethod' => $payMethod,
                'ectSelectChk' => $ectSelectChk,
                'page_rowsOpt' => $page_rowsOpt,
                'requestUrl' => $requestUrl,
                'tot_orderprice' => $tot_orderprice,
                'q1' => $q1,
                'page' => $page,
                'btn_frmline' => $btn_frmline,
                'odr2Table' => $odr2Table,
                'total_count' => $total_count
            );
            $this->load->view('mypage/seller/odr_2',$data);
        }
        private function seller_odr_query($code){
            $qstr=$this->load->get_var('qstr');
            $seller=$this->load->get_var('seller');

            $fr_date=$this->input->get('fr_date');
            $to_date=$this->input->get('to_date');
            $sel_field=$this->input->get('sel_field');
            $od_settle_case=$this->input->get('od_settle_case');
            $od_status=$this->input->get('od_status');
            $od_final=$this->input->get('od_final');
            $od_memo=$this->input->get('od_memo');
            $od_receipt_point=$this->input->get('od_receipt_point');
            $od_coupon=$this->input->get('od_coupon');
            $od_escrow=$this->input->get('od_escrow');
            $page=$this->input->get('page');
            $sfl=$this->input->get('sfl');
            $stx=$this->input->get('stx');

            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

            if(isset($sel_field))		 $qstr .= "&sel_field=$sel_field";
            if(isset($od_settle_case))	 $qstr .= "&od_settle_case=".urlencode($od_settle_case);
            if(isset($od_status))		 $qstr .= "&od_status=$od_status";
            if(isset($od_final))		 $qstr .= "&od_final=$od_final";
            if(isset($od_memo))			 $qstr .= "&od_memo=$od_memo";
            if(isset($od_receipt_point)) $qstr .= "&od_receipt_point=$od_receipt_point";
            if(isset($od_coupon))		 $qstr .= "&od_coupon=$od_coupon";
            if(isset($od_escrow))		 $qstr .= "&od_escrow=$od_escrow";

            $query_string = "code=$code$qstr";
            $q1 = $query_string;
            $q2 = $query_string."&page=$page";

            $sql_common = " from shop_order ";

            $where = array();
            $where[] = " seller_id = '{$seller['seller_code']}' ";
            $sql_search="";
            if($code == 'seller_odr_list') // 주문리스트(전체)
                $where[] = " dan > 1 ";
            else
                $where[] = " dan = '".conv_number($code)."' ";

            if($sfl && $stx)
                $where[] = " $sfl like '%$stx%' ";

            if($od_settle_case)
                $where[] = " paymethod = '$od_settle_case' ";

            if(is_numeric($od_status))
                $where[] = " dan = '$od_status' ";

            if(is_numeric($od_final))
                $where[] = " user_ok = '$od_final' ";

            if($od_memo)
                $where[] = " memo <> '' ";

            if($od_receipt_point)
                $where[] = " use_point != 0 ";

            if($od_coupon)
                $where[] = " coupon_price != 0 ";

            if($od_escrow)
                $where[] = " od_escrow = 1 ";

            if($fr_date && $to_date)
                $where[] = " left({$sel_field},10) between '$fr_date' and '$to_date' ";
            else if($fr_date && !$to_date)
                $where[] = " left({$sel_field},10) between '$fr_date' and '$fr_date' ";
            else if(!$fr_date && $to_date)
                $where[] = " left({$sel_field},10) between '$to_date' and '$to_date' ";

            if($where) {
                $sql_search = ' where '.implode(' and ', $where);
            }

            $sql_group = " group by od_id ";
            $sql_order = " order by index_no desc ";

            // 테이블의 전체 레코드수만 얻음
            $sql = " select od_id {$sql_common} {$sql_search} {$sql_group} ";
            $result = $this->query_model->returnQueryObj($sql);
            $total_count = $result->num_rows();

            if(isset($_SESSION['ss_page_rows']))
                $page_rows = $_SESSION['ss_page_rows'];
            else
                $page_rows = 30;

            $rows = $page_rows;
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);

            $sql = " select * {$sql_common} {$sql_search} {$sql_group} {$sql_order} limit {$from_record}, {$rows} ";
            $result = $this->query_model->returnArr($sql);

            $tot_orderprice = 0; // 총주문액
            $sql = " select od_id {$sql_common} {$sql_search} {$sql_group} {$sql_order} ";
            $res = $this->query_model->returnArr($sql);
            foreach($res as $key => $row){
                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
                $tot_orderprice += $amount['buyprice'];
            }
            $jqueryUiDatePick=$this->admin_template->datePicker();
            
            $return=array('tot_orderprice'=>$tot_orderprice,'jqueryUiDatePick'=>$jqueryUiDatePick,'result'=>$result,'q1'=>$q1,'total_page'=>$total_page,'page'=>$page, 
                        'sfl'=>$sfl,'sel_field'=>$sel_field,'fr_date'=>$fr_date,'to_date'=>$to_date,'od_settle_case'=>$od_settle_case,'od_memo'=>$od_memo,
                        'od_receipt_point'=>$od_receipt_point,'sel_field'=>$sel_field,'od_coupon'=>$od_coupon,'od_escrow'=>$od_escrow,'page_rows'=>$page_rows,
                        'stx'=>$stx,'total_count'=>$total_count,'sql_common'=>$sql_common,'sql_search'=>$sql_search,'num'=>$num);
            return $return;

        }
        private function seller_odr_3($code,$snbTagStart,$snbTagEnd){
            $pg_title = '배송준비';
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $config=$this->load->get_var('config');
            $seller=$this->load->get_var('seller');
            #seller_odr_query 한세트
            $ord_query=$this->seller_odr_query($code);
            $tot_orderprice=$ord_query['tot_orderprice'];
            $jqueryUiDatePick=$ord_query['jqueryUiDatePick'];
            $result=$ord_query['result'];
            $q1=$ord_query['q1'];
            $total_page=$ord_query['total_page'];
            $page=$ord_query['page'];
            $sfl=$ord_query['sfl'];
            $fr_date=$ord_query['fr_date'];
            $to_date=$ord_query['to_date'];
            $od_settle_case=$ord_query['od_settle_case'];
            $od_memo=$ord_query['od_memo'];
            $od_receipt_point=$ord_query['od_receipt_point'];
            $sel_field=$ord_query['sel_field'];
            $od_coupon=$ord_query['od_coupon'];
            $od_escrow=$ord_query['od_escrow'];
            $page_rows=$ord_query['page_rows'];
            $stx=$ord_query['stx'];
            $total_count=$ord_query['total_count'];
            $sql_common=$ord_query['sql_common'];
            $sql_search=$ord_query['sql_search'];
            $num=$ord_query['num'];
            #seller_odr_query 한세트 끝
            $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"운송장번호수정\" class=\"btn_lsmall white\" onclick=\"document.pressed=this.value\">
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"".base_url()."MypageProcess/seller_odr_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";
            $sflOpt=array('','','','','','','','','','');
            $sflOpt[0]=option_selected('od_id', $sfl, '주문번호');
            $sflOpt[1]=option_selected('od_no', $sfl, '일련번호');
            $sflOpt[2]=option_selected("mb_id", $sfl, '회원아이디');
            $sflOpt[3]=option_selected('name', $sfl, '주문자명');
            $sflOpt[4]=option_selected('deposit_name', $sfl, '입금자명');
            $sflOpt[5]=option_selected('bank', $sfl, '입금계좌');
            $sflOpt[6]=option_selected('b_name', $sfl, '수령자명');
            $sflOpt[7]=option_selected('b_telephone', $sfl, '수령자집전화');
            $sflOpt[8]=option_selected('b_cellphone', $sfl, '수령자핸드폰');
            $sflOpt[9]=option_selected('delivery_no', $sfl, '운송장번호');

            $sel_fieldOpt=array('','');
            $sel_fieldOpt[0]=option_selected('od_time', $sel_field, "주문일");
            $sel_fieldOpt[1]=option_selected('receipt_time', $sel_field, "입금완료일");
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);

            $payMethod=array('','','','','','');
            $payMethod[0]=radio_checked('od_settle_case', $od_settle_case,  '', '전체');
            $payMethod[1]=radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
            $payMethod[2]=radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
            $payMethod[3]=radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
            $payMethod[4]=radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
            $payMethod[5]=radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

            $ectSelectChk=array('','','','');
            $ectSelectChk[0]=check_checked('od_memo', $od_memo, 'Y', '배송메세지');
            $ectSelectChk[1]=check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
            $ectSelectChk[2]=check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
            $ectSelectChk[3]=check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

            $page_rowsOpt=array('30'=>'','50'=>'','100'=>'','150'=>'');
            $page_rowsOpt['30']=option_selected('30',  $page_rows, '30줄 정렬');
            $page_rowsOpt['50']=option_selected('50',  $page_rows, '50줄 정렬');
            $page_rowsOpt['100']=option_selected('100',  $page_rows, '100줄 정렬');
            $page_rowsOpt['150']=option_selected('150',  $page_rows, '150줄 정렬');
            $chk_cnt = 0;
            $i=0;
            $odr3Table="";
            foreach($result as $key => $row){
                $bg = 'list'.($i%2);

                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
                $sodr = $this->funcomplex_lib->get_order_list($row, $amount, "and dan IN ('4','5')");

                $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
                $param=array($row['od_id']);
                $resObj = $this->query_model->returnQueryObj($sql,$param);
                $res=$resObj->result_array();
                $rowspan = $resObj->num_rows();
                $k=0;
                foreach($res as $key => $row2){
                    $gs = unserialize($row2['od_goods']);
                    $odr3Table.="
                    <tr class=\"{$bg}\">";
                    if($k == 0) {
                        $odr3Table.="
                        <td rowspan=\"{$rowspan}\">".$num--."</td>
                        <td rowspan=\"{$rowspan}\">
                            ".substr($row['od_time'],2,14)."
                            {$sodr['disp_test']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"".base_url()."mypage/seller_odr_form?od_id={$row['od_id']}\" onclick=\"win_open(this,'seller_odr_form','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                            {$sodr['disp_mobile']}
                            {$sodr['disp_baesong']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                            <label for=\"sit_sel_{$i}\" class=\"sound_only\">전체선택</label>
                            <input type=\"checkbox\" name=\"it_sel[]\" id=\"sit_sel_{$i}\">
                        </td>";
                    }
                    $odr3Table.="
                        <td class=\"td_chk\">
                            <input type=\"hidden\" name=\"od_no[{$chk_cnt}]\" value=\"{$row2['od_no']}\">
                            <input type=\"checkbox\" name=\"chk[]\" value=\"{$chk_cnt}\" id=\"chk_{$chk_cnt}\" class=\"sct_sel_{$i}\">
                            <label for=\"chk_{$chk_cnt}\" class=\"sound_only\">{$gs['gname']}</label>
                        </td>
                        <td class=\"td_imgline\"><a href=\"".base_url()."shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30)."</a></td>
                        <td class=\"td_itname\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_text($gs['gname'])."</a></td>
                        <td>
                            ".$this->funcomplex_lib->get_delivery_select("delivery[".$chk_cnt."]", $row2['delivery'])."
                        </td>
                        <td><input type=\"text\" name=\"delivery_no[{$chk_cnt}]\" value=\"{$row2['delivery_no']}\" class=\"frm_input\" placeholder=\"개별 운송장번호\"></td>";
                        if($k == 0) {
                        $odr3Table.="
                        <td rowspan=\"{$rowspan}\">
                            {$sodr['disp_od_name']}
                            {$sodr['disp_mb_id']}
                        </td>
                        <td rowspan=\"{$rowspan}\">{$row['b_name']}</td>
                        <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>";
                    }
                    $chk_cnt++;
                    $k++;
                }
                $i++;
            }
            if($i==0)
                $odr3Table.='<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';
            $deliver2Str=$this->funcomplex_lib->get_delivery_select("delivery2");
            $helpTag=help('선택한 주문을 일괄처리시에 입력하세요. 주문목록에서 개별 입력도 가능합니다.');
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $data = array(
                'code' => $code,
                'snbTagStart' => $snbTagStart,
                'snbTagEnd' => $snbTagEnd,
                'admin_head_sub' => $admin_head_sub,
                'admin_footer_sub' => $admin_footer_sub,
                'btn_frmline' => $btn_frmline,
                'sflOpt' => $sflOpt,
                'jqueryUiDatePick' => $jqueryUiDatePick,
                'stx' => $stx,
                'sel_fieldOpt' => $sel_fieldOpt,
                'get_search_date' => $get_search_date,
                'payMethod' => $payMethod,
                'ectSelectChk' => $ectSelectChk,
                'page_rowsOpt' => $page_rowsOpt,
                'requestUrl' => $requestUrl,
                'q1' => $q1,
                'page' => $page,
                'odr3Table' => $odr3Table,
                'deliver2Str' => $deliver2Str,
                'helpTag' => $helpTag,
                'pagination' => $pagination,
                'total_count' => $total_count,
                'tot_orderprice' => $tot_orderprice
            );
            $this->load->view('mypage/seller/odr_3',$data);
        }
        private function seller_odr_4($code,$snbTagStart,$snbTagEnd){
            $pg_title = '배송중';
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $config=$this->load->get_var('config');
            $seller=$this->load->get_var('seller');
            #seller_odr_query 한세트
            $ord_query=$this->seller_odr_query($code);
            $tot_orderprice=$ord_query['tot_orderprice'];
            $jqueryUiDatePick=$ord_query['jqueryUiDatePick'];
            $result=$ord_query['result'];
            $q1=$ord_query['q1'];
            $total_page=$ord_query['total_page'];
            $page=$ord_query['page'];
            $sfl=$ord_query['sfl'];
            $fr_date=$ord_query['fr_date'];
            $to_date=$ord_query['to_date'];
            $od_settle_case=$ord_query['od_settle_case'];
            $od_memo=$ord_query['od_memo'];
            $od_receipt_point=$ord_query['od_receipt_point'];
            $sel_field=$ord_query['sel_field'];
            $od_coupon=$ord_query['od_coupon'];
            $od_escrow=$ord_query['od_escrow'];
            $page_rows=$ord_query['page_rows'];
            $stx=$ord_query['stx'];
            $total_count=$ord_query['total_count'];
            $sql_common=$ord_query['sql_common'];
            $sql_search=$ord_query['sql_search'];
            $num=$ord_query['num'];
            #seller_odr_query 한세트 끝
            $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"운송장번호수정\" class=\"btn_lsmall white\" onclick=\"document.pressed=this.value\">
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"".base_url()."MypageProcess/seller_odr_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";

            $sflOpt=array('','','','','','','','','','');
            $sflOpt[0]=option_selected('od_id', $sfl, '주문번호');
            $sflOpt[1]=option_selected('od_no', $sfl, '일련번호');
            $sflOpt[2]=option_selected("mb_id", $sfl, '회원아이디');
            $sflOpt[3]=option_selected('name', $sfl, '주문자명');
            $sflOpt[4]=option_selected('deposit_name', $sfl, '입금자명');
            $sflOpt[5]=option_selected('bank', $sfl, '입금계좌');
            $sflOpt[6]=option_selected('b_name', $sfl, '수령자명');
            $sflOpt[7]=option_selected('b_telephone', $sfl, '수령자집전화');
            $sflOpt[8]=option_selected('b_cellphone', $sfl, '수령자핸드폰');
            $sflOpt[9]=option_selected('delivery_no', $sfl, '운송장번호');
            
            $sel_fieldOpt=array('','','');
            $sel_fieldOpt[0]=option_selected('od_time', $sel_field, "주문일");
            $sel_fieldOpt[1]=option_selected('receipt_time', $sel_field, "입금완료일");
            $sel_fieldOpt[2]=option_selected('delivery_date', $sel_field, "배송일");
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);

            $payMethod=array('','','','','','');
            $payMethod[0]=radio_checked('od_settle_case', $od_settle_case,  '', '전체');
            $payMethod[1]=radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
            $payMethod[2]=radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
            $payMethod[3]=radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
            $payMethod[4]=radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
            $payMethod[5]=radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

            $ectSelectChk=array('','','','');
            $ectSelectChk[0]=check_checked('od_memo', $od_memo, 'Y', '배송메세지');
            $ectSelectChk[1]=check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
            $ectSelectChk[2]=check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
            $ectSelectChk[3]=check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

            $page_rowsOpt=array('30'=>'','50'=>'','100'=>'','150'=>'');
            $page_rowsOpt['30']=option_selected('30',  $page_rows, '30줄 정렬');
            $page_rowsOpt['50']=option_selected('50',  $page_rows, '50줄 정렬');
            $page_rowsOpt['100']=option_selected('100',  $page_rows, '100줄 정렬');
            $page_rowsOpt['150']=option_selected('150',  $page_rows, '150줄 정렬');
            $chk_cnt = 0;
            $i=0;
            $odr4Table="";
            foreach($result as $key => $row){
                $bg = 'list'.($i%2);

                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
                $sodr = $this->funcomplex_lib->get_order_list($row, $amount, "and dan IN ('2','3','5')");

                $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
                $param=array($row['od_id']);
                $resObj = $this->query_model->returnQueryObj($sql,$param);
                $res=$resObj->result_array();
                $rowspan = $resObj->num_rows();
                $k=0;
                foreach($res as $key => $row2){
                    $gs = unserialize($row2['od_goods']);
                    $odr4Table.="
                    <tr class=\"{$bg}\">";
                    if($k == 0) {
                        $odr4Table.="
                        <td rowspan=\"{$rowspan}\">".$num--."</td>
                        <td rowspan=\"{$rowspan}\">
                            ".substr($row['od_time'],2,14)."
                            {$sodr['disp_test']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"".base_url()."mypage/seller_odr_form?od_id={$row['od_id']}\" onclick=\"win_open(this,'seller_odr_form','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                            {$sodr['disp_mobile']}
                            {$sodr['disp_baesong']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                            <label for=\"sit_sel_{$i}\" class=\"sound_only\">전체선택</label>
                            <input type=\"checkbox\" name=\"it_sel[]\" id=\"sit_sel_{$i}\">
                        </td>";
                    }
                    $odr4Table.="
                    <td class=\"td_chk\">
                        <input type=\"hidden\" name=\"od_no[{$chk_cnt}]\" value=\"{$row2['od_no']}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$chk_cnt}\" id=\"chk_{$chk_cnt}\" class=\"sct_sel_{$i}\">
                        <label for=\"chk_{$chk_cnt}\" class=\"sound_only\">{$gs['gname']}</label>
                    </td>
                    <td class=\"td_imgline\"><a href=\"".base_url()."shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30)."</a></td>
                    <td class=\"td_itname\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_text($gs['gname'])."</a></td>
                    <td>
                    ".$this->funcomplex_lib->get_delivery_select("delivery[".$chk_cnt."]", $row2['delivery'])."
                    </td>
                    <td><input type=\"text\" name=\"delivery_no[{$chk_cnt}]\" value=\"{$row2['delivery_no']}\" class=\"frm_input\" placeholder=\"개별 운송장번호\"></td>
                    <td>".get_delivery_inquiry($row2['delivery'], $row2['delivery_no'], 'btn_ssmall')."</td>";
                    if($k == 0) {
                        $odr4Table.="
                        <td rowspan=\"{$rowspan}\">
                            {$sodr['disp_od_name']}
                            {$sodr['disp_mb_id']}
                        </td>
                        <td rowspan=\"{$rowspan}\">{$row['b_name']}</td>
                        <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>";
                    }
                    $chk_cnt++;
                    $k++;
                }
                $i++;
            }
            if($i==0)
                $odr4Table.='<tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $deliver2Str=$this->funcomplex_lib->get_delivery_select("delivery2");
            $helpTag=help('선택한 주문을 일괄처리시에 입력하세요. 주문목록에서 개별 입력도 가능합니다.');
            
            $data = array(
                'code' => $code,
                'snbTagStart' => $snbTagStart,
                'snbTagEnd' => $snbTagEnd,
                'requestUrl' => $requestUrl,
                'admin_head_sub' => $admin_head_sub,
                'admin_footer_sub' => $admin_footer_sub,
                'jqueryUiDatePick' => $jqueryUiDatePick,
                'btn_frmline' => $btn_frmline,
                'sflOpt' => $sflOpt,
                'sel_fieldOpt' => $sel_fieldOpt,
                'payMethod' => $payMethod,
                'ectSelectChk' => $ectSelectChk,
                'page_rowsOpt' => $page_rowsOpt,
                'odr4Table' => $odr4Table,
                'pagination' => $pagination,
                'stx' => $stx,
                'tot_orderprice' => $tot_orderprice,
                'q1' => $q1,
                'page' => $page,
                'deliver2Str' => $deliver2Str,
                'helpTag' => $helpTag,
                'get_search_date' => $get_search_date,
                'total_count' => $total_count
            );
            $this->load->view('mypage/seller/odr_4',$data);
        }
        private function seller_odr_5($code,$snbTagStart,$snbTagEnd){
            $pg_title = '배송완료';
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $config=$this->load->get_var('config');
            $seller=$this->load->get_var('seller');
            $od_final=$this->input->get('od_final');
            #seller_odr_query 한세트
            $ord_query=$this->seller_odr_query($code);
            $tot_orderprice=$ord_query['tot_orderprice'];
            $jqueryUiDatePick=$ord_query['jqueryUiDatePick'];
            $result=$ord_query['result'];
            $q1=$ord_query['q1'];
            $total_page=$ord_query['total_page'];
            $page=$ord_query['page'];
            $sfl=$ord_query['sfl'];
            $fr_date=$ord_query['fr_date'];
            $to_date=$ord_query['to_date'];
            $od_settle_case=$ord_query['od_settle_case'];
            $od_memo=$ord_query['od_memo'];
            $od_receipt_point=$ord_query['od_receipt_point'];
            $sel_field=$ord_query['sel_field'];
            $od_coupon=$ord_query['od_coupon'];
            $od_escrow=$ord_query['od_escrow'];
            $page_rows=$ord_query['page_rows'];
            $stx=$ord_query['stx'];
            $total_count=$ord_query['total_count'];
            $sql_common=$ord_query['sql_common'];
            $sql_search=$ord_query['sql_search'];
            $num=$ord_query['num'];
            #seller_odr_query 한세트 끝
            $btn_frmline = "
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"".base_url()."MypageProcess/seller_odr_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";

            $sflOpt=array('','','','','','','','','','');
            $sflOpt[0]=option_selected('od_id', $sfl, '주문번호');
            $sflOpt[1]=option_selected('od_no', $sfl, '일련번호');
            $sflOpt[2]=option_selected("mb_id", $sfl, '회원아이디');
            $sflOpt[3]=option_selected('name', $sfl, '주문자명');
            $sflOpt[4]=option_selected('deposit_name', $sfl, '입금자명');
            $sflOpt[5]=option_selected('bank', $sfl, '입금계좌');
            $sflOpt[6]=option_selected('b_name', $sfl, '수령자명');
            $sflOpt[7]=option_selected('b_telephone', $sfl, '수령자집전화');
            $sflOpt[8]=option_selected('b_cellphone', $sfl, '수령자핸드폰');
            $sflOpt[9]=option_selected('delivery_no', $sfl, '운송장번호');

            $sel_fieldOpt=array('','','','','');
            $sel_fieldOpt[0]=option_selected('od_time', $sel_field, "주문일");
            $sel_fieldOpt[1]=option_selected('receipt_time', $sel_field, "입금완료일");
            $sel_fieldOpt[2]=option_selected('delivery_date', $sel_field, "배송일");
            $sel_fieldOpt[3]=option_selected('invoice_date', $sel_field, "배송완료일");
            $sel_fieldOpt[4]=option_selected('user_date', $sel_field, "구매확정일");
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);

            $payMethod=array('','','','','','');
            $payMethod[0]=radio_checked('od_settle_case', $od_settle_case,  '', '전체');
            $payMethod[1]=radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
            $payMethod[2]=radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
            $payMethod[3]=radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
            $payMethod[4]=radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
            $payMethod[5]=radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

            $ectSelectChk=array('','','','');
            $ectSelectChk[0]=check_checked('od_memo', $od_memo, 'Y', '배송메세지');
            $ectSelectChk[1]=check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
            $ectSelectChk[2]=check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
            $ectSelectChk[3]=check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

            $od_finalArr=array('','','');
            $od_finalArr[0]=radio_checked('od_final', $od_final,  '', '전체');
            $od_finalArr[1]=radio_checked('od_final', $od_final, '1', '구매확정');
            $od_finalArr[2]=radio_checked('od_final', $od_final, '0', '구매미확정');

            $page_rowsOpt=array('30'=>'','50'=>'','100'=>'','150'=>'');
            $page_rowsOpt['30']=option_selected('30',  $page_rows, '30줄 정렬');
            $page_rowsOpt['50']=option_selected('50',  $page_rows, '50줄 정렬');
            $page_rowsOpt['100']=option_selected('100',  $page_rows, '100줄 정렬');
            $page_rowsOpt['150']=option_selected('150',  $page_rows, '150줄 정렬');
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $odr5Table="";
            $i=0;
            foreach($result as $key => $row){
                $bg = 'list'.($i%2);
        
                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
                $sodr =  $this->funcomplex_lib->get_order_list($row, $amount, "and dan IN ('2','3','4')");
        
                $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
                $param=array($row['od_id']);
                $resObj = $this->query_model->returnQueryObj($sql,$param);
                $res=$resObj->result_array();
                $rowspan = $resObj->num_rows();
                $k=0;
                foreach($res as $key => $row2){
                    $gs = unserialize($row2['od_goods']);
        
                    // 배송정보 (예:배송회사|배송추적URL)
                    $delivery_company="";
                    $delivery_url="";
                    if($row2['delivery']!=""){
                        list($delivery_company, $delivery_url) = explode('|', $row2['delivery']);
                    }
        
                    if(is_null_time($row2['user_date']))
                        $disp_final = '<span class="fc_107">대기</span>';
                    else
                        $disp_final = '<span class="fc_00f">완료</span>';
                    $odr5Table.="
                    <tr class=\"{$bg}\">";
                    if($k == 0) { 
                        $odr5Table.="
                        <td rowspan=\"{$rowspan}\">".$num--."</td>
                        <td rowspan=\"{$rowspan}\">
                            ".substr($row['od_time'],2,14)."
                            {$sodr['disp_test']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"".base_url()."mypage/seller_odr_form?od_id={$row['od_id']}\" onclick=\"win_open(this,'seller_odr_form','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                            {$sodr['disp_mobile']}
                            {$sodr['disp_baesong']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                            <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                            <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                        </td>";
                    }
                    $odr5Table.="
                    <td class=\"td_img\"><a href=\"".base_url()."shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30)."</a></td>
                    <td class=\"td_itname\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_text($gs['gname'])."</a></td>
                    <td>{$disp_final}</td>
                    <td>{$delivery_company}</td>
                    <td>".get_delivery_inquiry($row2['delivery'], $row2['delivery_no'], 'btn_ssmall')."</td>";
                    if($k == 0) {
                        $odr5Table.="
                        <td rowspan=\"{$rowspan}\">
                            {$sodr['disp_od_name']}
                            {$sodr['disp_mb_id']}
                        </td>
                        <td rowspan=\"{$rowspan}\">{$row['b_name']}</td>
                        <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>";
                    }
                    $k++;
                }
                $i++;
            }
            if($i==0)
                $odr5Table.='<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';

            $data = array(
                'code' => $code,
                'snbTagStart' => $snbTagStart,
                'snbTagEnd' => $snbTagEnd,
                'requestUrl' => $requestUrl,
                'admin_head_sub' => $admin_head_sub,
                'admin_footer_sub' => $admin_footer_sub,
                'jqueryUiDatePick' => $jqueryUiDatePick,
                'btn_frmline' => $btn_frmline,
                'sflOpt' => $sflOpt,
                'sel_fieldOpt' => $sel_fieldOpt,
                'payMethod' => $payMethod,
                'ectSelectChk' => $ectSelectChk,
                'page_rowsOpt' => $page_rowsOpt,
                'odr5Table' => $odr5Table,
                'pagination' => $pagination,
                'stx' => $stx,
                'tot_orderprice' => $tot_orderprice,
                'q1' => $q1,
                'page' => $page,
                'get_search_date' => $get_search_date,
                'total_count' => $total_count,
                'od_finalArr' => $od_finalArr,
                'stx' => $stx,
                'q1' => $q1,
                'page' => $page
            );
            $this->load->view('mypage/seller/odr_5',$data);
        }
        public function seller_odr_form(){
            $seller=$this->load->get_var('seller');
            $sql_search = " and seller_id = '{$seller['seller_code']}' ";
            $od_id=$this->input->get('od_id');
            $sql = " select * from shop_order where od_id = ? {$sql_search} ";
            $param=array($od_id);
            $od = $this->query_model->returnOneArr($sql,$param);
            if(!$od['od_id']) {
                alert_close("주문서가 존재하지 않습니다.");
            }

            $od['mb_id'] = $od['mb_id'] ? $od['mb_id'] : "비회원";

            $amount = $this->global_lib->get_order_spay($od_id, $sql_search); // 결제정보 합계
            $default = $this->funcomplex_lib->set_partner_value($od['od_settle_pid']); // 가맹점 PG결제 정보
            $title = "주문내역 수정";
            $this->admin_template->admin_head($title);
            $pg_anchor = '<ul class="anchor">
            <li><a href="#anc_sodr_list">주문상품 목록</a></li>
            <li><a href="#anc_sodr_pay">주문결제 내역</a></li>
            <li><a href="#anc_sodr_memo">관리자메모</a></li>
            <li><a href="#anc_sodr_addr">주문자/배송지 정보</a></li>
            </ul>';
            $yoil_time=get_yoil($od['od_time']);

            $chk_cnt	= 0; // 전체 배열
            $chk_cancel = 0; // 클래임 수
            $sum_point  = 0; // 포인트적립

            $sql = " select * from shop_order where od_id = ? {$sql_search} order by od_time desc, index_no asc ";
            $param=array($od_id);
            $result = $this->query_model->returnArr($sql,$param);
            $i=0;
            $odr_formTable="";
            foreach($result as $key => $row){
                $gs = unserialize($row['od_goods']);
                $it_options = $this->funcomplex_lib->print_complete_options($row['gs_id'], $row['od_id']);
                if($it_options){
                    $it_options = '<div class="sod_opt">'.$it_options.'</div>';
                }
                // 취소.반품.환불 외
                if(!in_array($row['dan'], array(6,7,9))) {
                    $sum_point += (int)$row['sum_point'];
                }
                $bg = 'list'.($i%2);
                $odr_formTable.="
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"od_no[{$i}]\" value=\"{$row['od_no']}\">
                        <input type=\"hidden\" name=\"current_status[{$i}]\" value=\"{$row['dan']}\">
                        <label for=\"chk_{$i}\" class=\"sound_only\">{$row['od_id']}</label>
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                    </td>
                    <td>
                        <a href=\"".base_url()."shop/view?index_no={$row['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 40, 40)."</a>
                    </td>
                    <td class=\"tal\">
                        <a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_text($gs['gname'])."</a>";
                        if($row['od_tax_flag'] && !$gs['notax']){ 
                            $odr_formTable.='[비과세상품]';
                        }
                        $odr_formTable.=$it_options;
                        // 배송준비.배송중.배송완료
                        $baesong_run = 0;
                        if(in_array($row['dan'], array(3,4,5))) {
                            $baesong_run++;
                            $odr_formTable.="
                            <div class=\"frm_info\">
                                ".$this->funcomplex_lib->get_delivery_select("delivery[".$i."]", $row['delivery'])."
                                <input type=\"text\" name=\"delivery_no[{$i}]\" value=\"{$row['delivery_no']}\" class=\"frm_input w130\" placeholder=\"개별 운송장번호\">
                                ".get_delivery_inquiry($row['delivery'], $row['delivery_no'], 'btn_ssmall')."
                            </div>";
                        }
                        $odr_formTable.="
                    </td>
                    <td>
                        ".$this->funcomplex_lib->get_change_select("change_status[".$i."]", $row['dan']);
                        if(in_array($row['dan'], array(7,9)) && $row['refund_price'] == 0 && in_array($row['paymethod'], array('신용카드', '계좌이체', 'KAKAOPAY'))) {
                            $odr_formTable.="
                            <p class=\"padt3\"><a href=\"".base_url()."admin/pop_orderpartcancel?od_id={$row['od_id']}&od_no={$row['od_no']}\" class=\"btn_ssmall orderpartcancel red\">PG부분취소</a></p>";
                        }
                    $odr_formTable.="
                    </td>
                        <td>".number_format($row['sum_qty'])."</td>
                        <td class=\"tar\">".number_format($row['supply_price'])."</td>
                        <td class=\"tar\">".number_format($row['goods_price'])."</td>
                        <td class=\"tar\">".number_format($row['baesong_price'])."</td>
                        <td class=\"tar\">".number_format($row['coupon_price'])."</td>
                        <td class=\"tar\">".number_format($row['use_point'])."</td>
                        <td class=\"td_price\">".number_format($row['use_price'])."</td>
                    </tr>";
                $i++;
                $chk_cnt++;

                // 취소.반품.교환.환불 수
                if(in_array($row['dan'], array(6,7,8,9))) {
                    $chk_cancel++;
                }
            }
            $normalOrder="";
            if($chk_cnt != $chk_cancel) { // 정상 주문건만 노출
                $normalOrder.="
                <div class=\"btn_list marb20\">
                    <input type=\"hidden\" name=\"chk_cnt\" value=\"{$chk_cnt}\">
                    <strong class=\"marr5\">선택한 상품을</strong>
                    <input type=\"submit\" name=\"act_button\" value=\"주문상태저장\" class=\"btn_lsmall red\" onclick=\"document.pressed=this.value\">";
                    if($baesong_run) {
                        $normalOrder.="
                        <input type=\"submit\" name=\"act_button\" value=\"운송장번호수정\" class=\"btn_lsmall white\" onclick=\"document.pressed=this.value\">";
                    }
                $normalOrder.="
                </div>";
            }
            $odTestTag="";
            if($od['od_test']) {
                $odTestTag="<div class=\"od_test_caution\">주의) 이 주문은 테스트용으로 실제 결제가 이루어지지 않았으므로 절대 배송하시면 안됩니다.</div>";
            }
            $od_mod_historyTag="";
            if($od['od_mod_history']) {
                $od_mod_historyTag="
                <section id=\"sodr_qty_log\">
                    <h3>주문 전체취소 처리 내역</h3>
                    <div>
                        ".$this->funcomplex_lib->conv_content($od['od_mod_history'], 0)."
                    </div>
                </section>";
            }
            // 결제방법
            $s_receipt_way = $od['paymethod'];
            if($od['paymethod'] == '간편결제') {
                if($od['od_pg'] == 'lg')
                    $s_receipt_way = 'PAYNOW';
                else if($od['od_pg'] == 'inicis')
                    $s_receipt_way = 'KPAY';
                else if($od['od_pg'] == 'kcp')
                    $s_receipt_way = 'PAYCO';
                else
                    $s_receipt_way = $od['paymethod'];
            }
            if($amount['usepoint'] > 0)
                $s_receipt_way .= "+포인트";
            $buypriceStr=display_price($amount['buyprice']);
            $amountPriceStr=display_price($amount['price']);
            $refundMileagePriceStr=display_price($amount['refund_mileage_price']);
            $amountBaeStr=display_price($amount['baesong']);
            $amountCouStr=display_price($amount['coupon']);
            $amountUsePointStr=display_price($amount['usepoint']);
            $amountUsePriceStr=display_price($amount['useprice']);
            $amountRefundStr=display_price($amount['cancel']);
            $sumPointStr=display_price($sum_point);
            $od_device=$od['od_mobile']?'모바일':'PC';

            $payMethodBank="";
            if(in_array($od['paymethod'], array('무통장', '가상계좌', '계좌이체'))) { 
                if($od['paymethod'] == '무통장')
                    $bank_account = $this->funcomplex_lib->get_bank_account("bank", $od['bank']);
                else if($od['paymethod'] == '가상계좌')
                    $bank_account = $od['bank'].'<input type="hidden" name="bank" value="'.$od['bank'].'">';
                else if($od['paymethod'] == '계좌이체')
                    $bank_account = $od['paymethod'];

                if(in_array($od['paymethod'], array('무통장', '가상계좌'))) {
                    $payMethodBank.="
                    <tr>
                        <th scope=\"row\"><label for=\"bank\">계좌번호</label></th>
                        <td>{$bank_account}</td>
                    </tr>";
                }
                $payMethodBank.="
                <tr>
                    <th scope=\"row\"><label for=\"deposit_name\">입금자명</label></th>
                    <td><input type=\"text\" name=\"deposit_name\" value=\"".$this->funcomplex_lib->get_text($od['deposit_name'])."\" id=\"deposit_name\" class=\"frm_input\" placeholder=\"실 입금자명\"></td>
                </tr>
                <tr>
                    <th scope=\"row\">{$od['paymethod']} 입금액</th>
                    <td>".display_price($amount['useprice'])."</td>
                </tr>
                <tr>
                    <th scope=\"row\">입금확인일시</th>
                    <td>";
                        if(is_null_time($od['receipt_time']))
                            $payMethodBank.='입금 확인일시 정보가 없습니다.';
                        else
                            $payMethodBank.=$od['receipt_time'].' ('.get_yoil($od['receipt_time']).')';
                        $payMethodBank.="
                    </td>
                </tr>";
            }
            $payMethodPhone="";
            if($od['paymethod'] == '휴대폰') {
                $payMethodPhone.="
                <tr>
                    <th scope=\"row\">휴대폰번호</th>
                    <td>".$this->funcomplex_lib->get_text($od['bank'])."</td>
                </tr>
                <tr>
                    <th scope=\"row\">휴대폰 결제액</th>
                    <td>".display_price($amount['useprice'])."</td>
                </tr>
                <tr>
                    <th scope=\"row\">결제 확인일시</th>
                    <td>";
                        if(is_null_time($od['receipt_time']))
                            $payMethodPhone.='결제 확인일시 정보가 없습니다.';
                        else
                            $payMethodPhone.= $od['receipt_time'].' ('.get_yoil($od['receipt_time']).')';
                        $payMethodPhone.="
                    </td>
                </tr>";
            }
            $payMethodCredit="";
            if($od['paymethod'] == '신용카드') {
                $payMethodCredit.="
                <tr>
                    <th scope=\"row\" class=\"sodr_sppay\">신용카드 결제금액</th>
                    <td>";
                        if(is_null_time($od['receipt_time']))
                            $payMethodCredit.='0원';
                        else
                            $payMethodCredit.=display_price($amount['useprice']);
                        $payMethodCredit.="
                    </td>
                </tr>
                <tr>
                    <th scope=\"row\" class=\"sodr_sppay\">카드 승인일시</th>
                    <td>";
                        if(is_null_time($od['receipt_time']))
                            $payMethodCredit.='신용카드 결제 일시 정보가 없습니다.';
                        else
                            $payMethodCredit.=substr($od['receipt_time'], 0, 20);
                        $payMethodCredit.="
                    </td>
                </tr>";
            }
            $payMethodKakao="";
            if($od['paymethod'] == 'KAKAOPAY') {
                $payMethodKakao.="
                <tr>
                    <th scope=\"row\" class=\"sodr_sppay\">KAKOPAY 결제금액</th>
                    <td>";
                        if(is_null_time($od['receipt_time']))
                            $payMethodKakao.='0원';
                        else
                            $payMethodKakao.=display_price($amount['useprice']);
                        $payMethodKakao.="
                    </td>
                </tr>
                <tr>
                    <th scope=\"row\" class=\"sodr_sppay\">KAKAOPAY 승인일시</th>
                    <td>";
                        if(is_null_time($od['receipt_time']))
                            $payMethodKakao.='신용카드 결제 일시 정보가 없습니다.';
                        else
                            $payMethodKakao.=substr($od['receipt_time'], 0, 20);
                        $payMethodKakao.="
                    </td>
                </tr>";
            }
            $payMethodEasy="";
            if($od['paymethod'] == '간편결제' || ($od['od_pg'] == 'inicis' && $od['paymethod'] == '삼성페이') ) {
                $payMethodEasy.="
                <tr>
                    <th scope=\"row\" class=\"sodr_sppay\">{$s_receipt_way} 결제금액</th>
                    <td>";
                        if(is_null_time($od['receipt_time']))
                            $payMethodEasy.= '0원';
                        else
                            $payMethodEasy.=display_price($amount['useprice']);
                        $payMethodEasy.="
                    </td>
                </tr>
                <tr>
                    <th scope=\"row\" class=\"sodr_sppay\">{$s_receipt_way} 승인일시</th>
                    <td>";
                        if(is_null_time($od['receipt_time']))
                            $payMethodEasy.=$s_receipt_way. ' 결제 일시 정보가 없습니다.';
                        else
                            $payMethodEasy.= substr($od['receipt_time'], 0, 20);
                        $payMethodEasy.="
                    </td>
                </tr>";
            }
            $taxsaveSY="";
            if($od['taxsave_yes'] == 'S' || $od['taxsave_yes'] == 'Y') {
                $$taxsaveSY.="
                <tr>
                    <th scope=\"row\">현금영수증 신청여부</th>
                    <td class=\"lh4\">";
                        if($od['taxsave_yes'] == 'S') {
                            $$taxsaveSY.="
                            사업자 지출증빙용<br>
                            사업자번호 : {$od['tax_saupja_no']}";
                        } else if($od['taxsave_yes'] == 'Y') {
                            $$taxsaveSY.="
                            개인 소득공제용<br>
                            핸드폰 : {$od['tax_hp']}";
                        }
                    $$taxsaveSY.="
                    </td>
                </tr>";
            }
            $taxbillYes="";
            if($od['taxbill_yes'] == 'Y') { 
                $taxbillYes.="
                <tr>
                    <th scope=\"row\">세금계산서 신청여부</th>
                    <td class=\"lh4\">
                        회사명 : {$od['company_name']}<br>
                        대표자명 : {$od['company_owner']}<br>
                        사업자번호 : {$od['company_saupja_no']}<br>
                        사업장주소 : {$od['company_addr']}<br>
                        업태 : {$od['company_item']}<br>
                        종목 : {$od['company_service']}
                    </td>
                </tr>";
            }

            $odName=$this->funcomplex_lib->get_text($od['name']);
            $odTele=$this->funcomplex_lib->get_text($od['telephone']);
            $odPhone=$this->funcomplex_lib->get_text($od['cellphone']);
            $odAddr1=$this->funcomplex_lib->get_text($od['addr1']);
            $odAddr2=$this->funcomplex_lib->get_text($od['addr2']);
            $odAddr3=$this->funcomplex_lib->get_text($od['addr3']);
            $odAddrJibeon=$this->funcomplex_lib->get_text($od['addr_jibeon']);

            $odBName=$this->funcomplex_lib->get_text($od['b_name']);
            $odBTele=$this->funcomplex_lib->get_text($od['b_telephone']);
            $odBPhone=$this->funcomplex_lib->get_text($od['b_cellphone']);
            $odBAddr1=$this->funcomplex_lib->get_text($od['b_addr1']);
            $odBAddr2=$this->funcomplex_lib->get_text($od['b_addr2']);
            $odBAddr3=$this->funcomplex_lib->get_text($od['b_addr3']);
            $odBAddrJibeon=$this->funcomplex_lib->get_text($od['b_addr_jibeon']);
            $odMemo="";
            if($od['memo']) {
                $odMemo=$this->funcomplex_lib-> get_text($od['memo'], 1);
            }else {
                $odMemo="없음";
            }
            $data = array(
                'pg_anchor' => $pg_anchor,
                'title' => $title,
                'pg_anchor' => $pg_anchor,
                'od' => $od,
                'yoil_time' => $yoil_time,
                'amount' => $amount,
                'od_id' => $od_id,
                'odr_formTable' => $odr_formTable,
                'normalOrder' => $normalOrder,
                'odTestTag' => $odTestTag,
                'od_mod_historyTag' => $od_mod_historyTag,
                's_receipt_way' => $s_receipt_way,
                'buypriceStr' => $buypriceStr,
                'amountPriceStr' => $amountPriceStr,
                'refundMileagePriceStr' => $refundMileagePriceStr,
                'amountBaeStr' => $amountBaeStr,
                'amountCouStr' => $amountCouStr,
                'amountUsePointStr' => $amountUsePointStr,
                'amountUsePriceStr' => $amountUsePriceStr,
                'amountRefundStr' => $amountRefundStr,
                'sumPointStr' => $sumPointStr,
                'od_device' => $od_device,
                'payMethodBank' => $payMethodBank,
                'payMethodPhone' => $payMethodPhone,
                'payMethodCredit' => $payMethodCredit,
                'payMethodKakao' => $payMethodKakao,
                'payMethodEasy' => $payMethodEasy,
                'taxsaveSY' => $taxsaveSY,
                'taxbillYes' => $taxbillYes,
                'odName' => $odName,
                'odTele' => $odTele,
                'odPhone' => $odPhone,
                'odAddr1' => $odAddr1,
                'odAddr2' => $odAddr2,
                'odAddr3' => $odAddr3,
                'odAddrJibeon' => $odAddrJibeon,
                'odBName' => $odBName,
                'odBTele' => $odBTele,
                'odBPhone' => $odBPhone,
                'odBAddr1' => $odBAddr1,
                'odBAddr2' => $odBAddr2,
                'odBAddr3' => $odBAddr3,
                'odBAddrJibeon' => $odBAddrJibeon,
                'odMemo' => $odMemo
            );
            $this->load->view('mypage/seller/odr_form',$data);
            $this->admin_template->admin_footer_sub();
        }
        private function seller_odr_6($code,$snbTagStart,$snbTagEnd){
            $pg_title = '입금전 취소';
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $config=$this->load->get_var('config');
            $seller=$this->load->get_var('seller');
            $od_final=$this->input->get('od_final');
            #seller_odr_query 한세트
            $ord_query=$this->seller_odr_query($code);
            $tot_orderprice=$ord_query['tot_orderprice'];
            $jqueryUiDatePick=$ord_query['jqueryUiDatePick'];
            $result=$ord_query['result'];
            $q1=$ord_query['q1'];
            $total_page=$ord_query['total_page'];
            $page=$ord_query['page'];
            $sfl=$ord_query['sfl'];
            $fr_date=$ord_query['fr_date'];
            $to_date=$ord_query['to_date'];
            $od_settle_case=$ord_query['od_settle_case'];
            $od_memo=$ord_query['od_memo'];
            $od_receipt_point=$ord_query['od_receipt_point'];
            $sel_field=$ord_query['sel_field'];
            $od_coupon=$ord_query['od_coupon'];
            $od_escrow=$ord_query['od_escrow'];
            $page_rows=$ord_query['page_rows'];
            $stx=$ord_query['stx'];
            $total_count=$ord_query['total_count'];
            $sql_common=$ord_query['sql_common'];
            $sql_search=$ord_query['sql_search'];
            $num=$ord_query['num'];
            #seller_odr_query 한세트 끝
            $sflOpt=array('','','','','','','','','','');
            $sflOpt[0]=option_selected('od_id', $sfl, '주문번호');
            $sflOpt[1]=option_selected('od_no', $sfl, '일련번호');
            $sflOpt[2]=option_selected("mb_id", $sfl, '회원아이디');
            $sflOpt[3]=option_selected('name', $sfl, '주문자명');
            $sflOpt[4]=option_selected('deposit_name', $sfl, '입금자명');
            $sflOpt[5]=option_selected('bank', $sfl, '입금계좌');
            $sflOpt[6]=option_selected('b_name', $sfl, '수령자명');
            $sflOpt[7]=option_selected('b_telephone', $sfl, '수령자집전화');
            $sflOpt[8]=option_selected('b_cellphone', $sfl, '수령자핸드폰');
            $sflOpt[9]=option_selected('delivery_no', $sfl, '운송장번호');
            $sel_fieldOpt=array('','','','','');
            $sel_fieldOpt[0]=option_selected('od_time', $sel_field, "주문일");
            $sel_fieldOpt[1]=option_selected('cancel_date', $sel_field, "취소일");

            $payMethod=array('','','','','','');
            $payMethod[0]=radio_checked('od_settle_case', $od_settle_case,  '', '전체');
            $payMethod[1]=radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
            $payMethod[2]=radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
            $payMethod[3]=radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
            $payMethod[4]=radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
            $payMethod[5]=radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

            $ectSelectChk=array('','','','');
            $ectSelectChk[0]=check_checked('od_memo', $od_memo, 'Y', '배송메세지');
            $ectSelectChk[1]=check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
            $ectSelectChk[2]=check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
            $ectSelectChk[3]=check_checked('od_escrow', $od_escrow, 'Y', '에스크로');
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $odr6Table="";
            $i=0;
            foreach($result as $key => $row){
                $bg = 'list'.($i%2);
        
                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
                $sodr = $this->funcomplex_lib->get_order_list($row, $amount);
        
                $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
                $param=array($row['od_id']);
                $resObj = $this->query_model->returnQueryObj($sql,$param);
                $res=$resObj->result_array();
                $rowspan = $resObj->num_rows();
                $k=0;
                foreach($res as $key => $row2){
                    $gs = unserialize($row2['od_goods']);
                    $odr6Table.="
                    <tr class=\"{$bg}\">";
                    if($k == 0) {
                        $odr6Table.="
                        <td rowspan=\"{$rowspan}\">".$num--."</td>
                        <td rowspan=\"{$rowspan}\">
                            ".substr($row['od_time'],2,14)."
                            {$sodr['disp_test']}
                        </td>
                        <td rowspan=\"{$rowspan}\">".substr($row['cancel_date'],2,14)."</td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"".base_url()."mypage/seller_odr_form?od_id={$row['od_id']}\" onclick=\"win_open(this,'seller_odr_form','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                            {$sodr['disp_mobile']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                            <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                            <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                        </td>";
                    }
                    $odr6Table.="
                    <td class=\"td_img\"><a href=\"".base_url()."shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30)."</a></td>
                    <td class=\"td_itname\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_text($gs['gname'])."</a></td>";
                    if($k == 0) {
                        $odr6Table.="
                        <td rowspan=\"{$rowspan}\">
                            {$sodr['disp_od_name']}
                            {$sodr['disp_mb_id']}
                        </td>
                        <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>
                        <td rowspan=\"{$rowspan}\">{$sodr['disp_paytype']}</td>";
                    }
                    $k++;
                }
                $i++;
            }
            if($i==0)
                $odr6Table.='<tr><td colspan="10" class="empty_table">자료가 없습니다.</td></tr>';
            $page_rowsOpt=array('30'=>'','50'=>'','100'=>'','150'=>'');
            $page_rowsOpt['30']=option_selected('30',  $page_rows, '30줄 정렬');
            $page_rowsOpt['50']=option_selected('50',  $page_rows, '50줄 정렬');
            $page_rowsOpt['100']=option_selected('100',  $page_rows, '100줄 정렬');
            $page_rowsOpt['150']=option_selected('150',  $page_rows, '150줄 정렬');
            $btn_frmline = "
                <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
                <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
                <a href=\"".base_url()."MypageProcess/seller_odr_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);        
            $data = array(
                'code' => $code,
                'snbTagStart' => $snbTagStart,
                'snbTagEnd' => $snbTagEnd,
                'requestUrl' => $requestUrl,
                'admin_head_sub' => $admin_head_sub,
                'admin_footer_sub' => $admin_footer_sub,
                'jqueryUiDatePick' => $jqueryUiDatePick,
                'btn_frmline' => $btn_frmline,
                'sflOpt' => $sflOpt,
                'sel_fieldOpt' => $sel_fieldOpt,
                'payMethod' => $payMethod,
                'ectSelectChk' => $ectSelectChk,
                'page_rowsOpt' => $page_rowsOpt,
                'odr6Table' => $odr6Table,
                'pagination' => $pagination,
                'stx' => $stx,
                'tot_orderprice' => $tot_orderprice,
                'q1' => $q1,
                'page' => $page,
                'get_search_date' => $get_search_date,
                'total_count' => $total_count,
                'stx' => $stx,
                'q1' => $q1,
                'page' => $page
            );
            $this->load->view('mypage/seller/odr_6',$data);
        }
        private function seller_odr_9($code,$snbTagStart,$snbTagEnd){
            $pg_title = '배송전 환불';
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $config=$this->load->get_var('config');
            $od_final=$this->input->get('od_final');
            #seller_odr_query 한세트
            $ord_query=$this->seller_odr_query($code);
            $tot_orderprice=$ord_query['tot_orderprice'];
            $jqueryUiDatePick=$ord_query['jqueryUiDatePick'];
            $result=$ord_query['result'];
            $q1=$ord_query['q1'];
            $total_page=$ord_query['total_page'];
            $page=$ord_query['page'];
            $sfl=$ord_query['sfl'];
            $fr_date=$ord_query['fr_date'];
            $to_date=$ord_query['to_date'];
            $od_settle_case=$ord_query['od_settle_case'];
            $od_memo=$ord_query['od_memo'];
            $od_receipt_point=$ord_query['od_receipt_point'];
            $sel_field=$ord_query['sel_field'];
            $od_coupon=$ord_query['od_coupon'];
            $od_escrow=$ord_query['od_escrow'];
            $page_rows=$ord_query['page_rows'];
            $stx=$ord_query['stx'];
            $total_count=$ord_query['total_count'];
            $sql_common=$ord_query['sql_common'];
            $sql_search=$ord_query['sql_search'];
            $num=$ord_query['num'];
            #seller_odr_query 한세트 끝
            $sflOpt=array('','','','','','','','','','');
            $sflOpt[0]=option_selected('od_id', $sfl, '주문번호');
            $sflOpt[1]=option_selected('od_no', $sfl, '일련번호');
            $sflOpt[2]=option_selected("mb_id", $sfl, '회원아이디');
            $sflOpt[3]=option_selected('name', $sfl, '주문자명');
            $sflOpt[4]=option_selected('deposit_name', $sfl, '입금자명');
            $sflOpt[5]=option_selected('bank', $sfl, '입금계좌');
            $sflOpt[6]=option_selected('b_name', $sfl, '수령자명');
            $sflOpt[7]=option_selected('b_telephone', $sfl, '수령자집전화');
            $sflOpt[8]=option_selected('b_cellphone', $sfl, '수령자핸드폰');
            $sflOpt[9]=option_selected('delivery_no', $sfl, '운송장번호');

            $sel_fieldOpt=array('','');
            $sel_fieldOpt[0]=option_selected('od_time', $sel_field, "주문일");
            $sel_fieldOpt[1]=option_selected('refund_date', $sel_field, "환불완료일");

            $payMethod=array('','','','','','');
            $payMethod[0]=radio_checked('od_settle_case', $od_settle_case,  '', '전체');
            $payMethod[1]=radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
            $payMethod[2]=radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
            $payMethod[3]=radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
            $payMethod[4]=radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
            $payMethod[5]=radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

            $ectSelectChk=array('','','','');
            $ectSelectChk[0]=check_checked('od_memo', $od_memo, 'Y', '배송메세지');
            $ectSelectChk[1]=check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
            $ectSelectChk[2]=check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
            $ectSelectChk[3]=check_checked('od_escrow', $od_escrow, 'Y', '에스크로');
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $odr9Table="";
            $i=0;
            foreach($result as $key => $row){
                $bg = 'list'.($i%2);
        
                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
                $sodr = $this->funcomplex_lib->get_order_list($row, $amount);
        
                $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
                $param=array($row['od_id']);
                $resObj = $this->query_model->returnQueryObj($sql,$param);
                $res=$resObj->result_array();
                $rowspan = $resObj->num_rows();
                $k=0;
                foreach($res as $key => $row2){
                    $gs = unserialize($row2['od_goods']);
                    $odr9Table.="
                    <tr class=\"{$bg}\">";
                    if($k == 0) {
                        $odr9Table.="
                        <td rowspan=\"{$rowspan}\">".$num--."</td>
                        <td rowspan=\"{$rowspan}\">
                            ".substr($row['od_time'],2,14)."
                            {$sodr['disp_test']}
                        </td>
                        <td rowspan=\"{$rowspan}\">".substr($row['refund_date'],2,14)."</td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"".base_url()."mypage/seller_odr_form?od_id={$row['od_id']}\" onclick=\"win_open(this,'seller_odr_form','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                            {$sodr['disp_mobile']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                            <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                            <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                        </td>";
                    }
                    $odr9Table.="
                    <td class=\"td_img\"><a href=\"".base_url()."shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30)."</a></td>
                    <td class=\"td_itname\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_text($gs['gname'])."</a></td>";
                    if($k == 0) {
                        $odr9Table.="
                        <td rowspan=\"{$rowspan}\">
                            {$sodr['disp_od_name']}
                            {$sodr['disp_mb_id']}
                        </td>
                        <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>
                        <td rowspan=\"{$rowspan}\">{$sodr['disp_paytype']}</td>";
                    }
                    $k++;
                }
                $i++;
            }
            if($i==0)
                $odr9Table.='<tr><td colspan="10" class="empty_table">자료가 없습니다.</td></tr>';
            $page_rowsOpt=array('30'=>'','50'=>'','100'=>'','150'=>'');
            $page_rowsOpt['30']=option_selected('30',  $page_rows, '30줄 정렬');
            $page_rowsOpt['50']=option_selected('50',  $page_rows, '50줄 정렬');
            $page_rowsOpt['100']=option_selected('100',  $page_rows, '100줄 정렬');
            $page_rowsOpt['150']=option_selected('150',  $page_rows, '150줄 정렬');
            $btn_frmline = "
                <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
                <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
                <a href=\"".base_url()."MypageProcess/seller_odr_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);        

            $data = array(
                'code' => $code,
                'snbTagStart' => $snbTagStart,
                'snbTagEnd' => $snbTagEnd,
                'requestUrl' => $requestUrl,
                'admin_head_sub' => $admin_head_sub,
                'admin_footer_sub' => $admin_footer_sub,
                'jqueryUiDatePick' => $jqueryUiDatePick,
                'btn_frmline' => $btn_frmline,
                'sflOpt' => $sflOpt,
                'sel_fieldOpt' => $sel_fieldOpt,
                'payMethod' => $payMethod,
                'ectSelectChk' => $ectSelectChk,
                'page_rowsOpt' => $page_rowsOpt,
                'odr9Table' => $odr9Table,
                'pagination' => $pagination,
                'stx' => $stx,
                'tot_orderprice' => $tot_orderprice,
                'q1' => $q1,
                'page' => $page,
                'get_search_date' => $get_search_date,
                'total_count' => $total_count,
                'stx' => $stx,
                'q1' => $q1,
                'page' => $page
            );
            $this->load->view('mypage/seller/odr_9',$data);
        }
        private function seller_odr_7($code,$snbTagStart,$snbTagEnd){
            $pg_title = '배송후 환불';
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $config=$this->load->get_var('config');
            $od_final=$this->input->get('od_final');
            $seller=$this->input->get('seller');
            
            #seller_odr_query 한세트
            $ord_query=$this->seller_odr_query($code);
            $tot_orderprice=$ord_query['tot_orderprice'];
            $jqueryUiDatePick=$ord_query['jqueryUiDatePick'];
            $result=$ord_query['result'];
            $q1=$ord_query['q1'];
            $total_page=$ord_query['total_page'];
            $page=$ord_query['page'];
            $sfl=$ord_query['sfl'];
            $fr_date=$ord_query['fr_date'];
            $to_date=$ord_query['to_date'];
            $od_settle_case=$ord_query['od_settle_case'];
            $od_memo=$ord_query['od_memo'];
            $od_receipt_point=$ord_query['od_receipt_point'];
            $sel_field=$ord_query['sel_field'];
            $od_coupon=$ord_query['od_coupon'];
            $od_escrow=$ord_query['od_escrow'];
            $page_rows=$ord_query['page_rows'];
            $stx=$ord_query['stx'];
            $total_count=$ord_query['total_count'];
            $sql_common=$ord_query['sql_common'];
            $sql_search=$ord_query['sql_search'];
            $num=$ord_query['num'];
            #seller_odr_query 한세트 끝

            $sflOpt=array('','','','','','','','','','');
            $sflOpt[0]=option_selected('od_id', $sfl, '주문번호');
            $sflOpt[1]=option_selected('od_no', $sfl, '일련번호');
            $sflOpt[2]=option_selected("mb_id", $sfl, '회원아이디');
            $sflOpt[3]=option_selected('name', $sfl, '주문자명');
            $sflOpt[4]=option_selected('deposit_name', $sfl, '입금자명');
            $sflOpt[5]=option_selected('bank', $sfl, '입금계좌');
            $sflOpt[6]=option_selected('b_name', $sfl, '수령자명');
            $sflOpt[7]=option_selected('b_telephone', $sfl, '수령자집전화');
            $sflOpt[8]=option_selected('b_cellphone', $sfl, '수령자핸드폰');
            $sflOpt[9]=option_selected('delivery_no', $sfl, '운송장번호');

            $sel_fieldOpt=array('','','','','');
            $sel_fieldOpt[0]=option_selected('od_time', $sel_field, "주문일");
            $sel_fieldOpt[1]=option_selected('return_date', $sel_field, "반품완료일");

            $payMethod=array('','','','','','');
            $payMethod[0]=radio_checked('od_settle_case', $od_settle_case,  '', '전체');
            $payMethod[1]=radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
            $payMethod[2]=radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
            $payMethod[3]=radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
            $payMethod[4]=radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
            $payMethod[5]=radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

            $ectSelectChk=array('','','','');
            $ectSelectChk[0]=check_checked('od_memo', $od_memo, 'Y', '배송메세지');
            $ectSelectChk[1]=check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
            $ectSelectChk[2]=check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
            $ectSelectChk[3]=check_checked('od_escrow', $od_escrow, 'Y', '에스크로');
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $odr7Table="";
            $i=0;
            foreach($result as $key => $row){
                $bg = 'list'.($i%2);
        
                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
                $sodr = $this->funcomplex_lib->get_order_list($row, $amount);
        
                $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
                $param=array($row['od_id']);
                $resObj = $this->query_model->returnQueryObj($sql,$param);
                $res=$resObj->result_array();
                $rowspan = $resObj->num_rows();
                $k=0;
                foreach($res as $key => $row2){
                    $gs = unserialize($row2['od_goods']);
                    $odr7Table.="
                    <tr class=\"{$bg}\">";
                    if($k == 0) {
                        $odr7Table.="
                        <td rowspan=\"{$rowspan}\">".$num--."</td>
                        <td rowspan=\"{$rowspan}\">
                            ".substr($row['od_time'],2,14)."{$sodr['disp_test']}
                        </td>
                        <td rowspan=\"{$rowspan}\">".substr($row['return_date'],2,14)."</td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"".base_url()."mypage/seller_odr_form?od_id={$row['od_id']}\" onclick=\"win_open(this,'seller_odr_form','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                            {$sodr['disp_mobile']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                            <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                            <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                        </td>";
                    }
                    $odr7Table.="
                    <td class=\"td_img\"><a href=\"".base_url()."shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30)."</a></td>
                    <td class=\"td_itname\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_text($gs['gname'])."</a></td>";
                    if($k == 0) {
                        $odr7Table.="
                        <td rowspan=\"{$rowspan}\">
                            {$sodr['disp_od_name']}
                            {$sodr['disp_mb_id']}
                        </td>
                        <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>
                        <td rowspan=\"{$rowspan}\">{$sodr['disp_paytype']}</td>";
                    }
                    $k++;
                }
                $i++;
            }
            if($i==0)
                $odr7Table.='<tr><td colspan="10" class="empty_table">자료가 없습니다.</td></tr>';
            $page_rowsOpt=array('30'=>'','50'=>'','100'=>'','150'=>'');
            $page_rowsOpt['30']=option_selected('30',  $page_rows, '30줄 정렬');
            $page_rowsOpt['50']=option_selected('50',  $page_rows, '50줄 정렬');
            $page_rowsOpt['100']=option_selected('100',  $page_rows, '100줄 정렬');
            $page_rowsOpt['150']=option_selected('150',  $page_rows, '150줄 정렬');
            $btn_frmline = "
                <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
                <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
                <a href=\"".base_url()."MypageProcess/seller_odr_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);        

            $data = array(
                'code' => $code,
                'snbTagStart' => $snbTagStart,
                'snbTagEnd' => $snbTagEnd,
                'requestUrl' => $requestUrl,
                'admin_head_sub' => $admin_head_sub,
                'admin_footer_sub' => $admin_footer_sub,
                'jqueryUiDatePick' => $jqueryUiDatePick,
                'btn_frmline' => $btn_frmline,
                'sflOpt' => $sflOpt,
                'sel_fieldOpt' => $sel_fieldOpt,
                'payMethod' => $payMethod,
                'ectSelectChk' => $ectSelectChk,
                'page_rowsOpt' => $page_rowsOpt,
                'odr7Table' => $odr7Table,
                'pagination' => $pagination,
                'stx' => $stx,
                'tot_orderprice' => $tot_orderprice,
                'q1' => $q1,
                'page' => $page,
                'get_search_date' => $get_search_date,
                'total_count' => $total_count,
                'stx' => $stx,
                'q1' => $q1,
                'page' => $page
            );
            $this->load->view('mypage/seller/odr_7',$data);
        }
        private function seller_odr_8($code,$snbTagStart,$snbTagEnd){
            $pg_title = '배송후 교환';
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $config=$this->load->get_var('config');
            $od_final=$this->input->get('od_final');
            #seller_odr_query 한세트
            $ord_query=$this->seller_odr_query($code);
            $tot_orderprice=$ord_query['tot_orderprice'];
            $jqueryUiDatePick=$ord_query['jqueryUiDatePick'];
            $result=$ord_query['result'];
            $q1=$ord_query['q1'];
            $total_page=$ord_query['total_page'];
            $page=$ord_query['page'];
            $sfl=$ord_query['sfl'];
            $fr_date=$ord_query['fr_date'];
            $to_date=$ord_query['to_date'];
            $od_settle_case=$ord_query['od_settle_case'];
            $od_memo=$ord_query['od_memo'];
            $od_receipt_point=$ord_query['od_receipt_point'];
            $sel_field=$ord_query['sel_field'];
            $od_coupon=$ord_query['od_coupon'];
            $od_escrow=$ord_query['od_escrow'];
            $page_rows=$ord_query['page_rows'];
            $stx=$ord_query['stx'];
            $total_count=$ord_query['total_count'];
            $sql_common=$ord_query['sql_common'];
            $sql_search=$ord_query['sql_search'];
            $num=$ord_query['num'];
            #seller_odr_query 한세트 끝

            $sflOpt=array('','','','','','','','','','');
            $sflOpt[0]=option_selected('od_id', $sfl, '주문번호');
            $sflOpt[1]=option_selected('od_no', $sfl, '일련번호');
            $sflOpt[2]=option_selected("mb_id", $sfl, '회원아이디');
            $sflOpt[3]=option_selected('name', $sfl, '주문자명');
            $sflOpt[4]=option_selected('deposit_name', $sfl, '입금자명');
            $sflOpt[5]=option_selected('bank', $sfl, '입금계좌');
            $sflOpt[6]=option_selected('b_name', $sfl, '수령자명');
            $sflOpt[7]=option_selected('b_telephone', $sfl, '수령자집전화');
            $sflOpt[8]=option_selected('b_cellphone', $sfl, '수령자핸드폰');
            $sflOpt[9]=option_selected('delivery_no', $sfl, '운송장번호');

            $sel_fieldOpt=array('','');
            $sel_fieldOpt[0]=option_selected('od_time', $sel_field, "주문일");
            $sel_fieldOpt[1]=option_selected('change_date', $sel_field, "교환완료일");

            $payMethod=array('','','','','','');
            $payMethod[0]=radio_checked('od_settle_case', $od_settle_case,  '', '전체');
            $payMethod[1]=radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
            $payMethod[2]=radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
            $payMethod[3]=radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
            $payMethod[4]=radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
            $payMethod[5]=radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

            $ectSelectChk=array('','','','');
            $ectSelectChk[0]=check_checked('od_memo', $od_memo, 'Y', '배송메세지');
            $ectSelectChk[1]=check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
            $ectSelectChk[2]=check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
            $ectSelectChk[3]=check_checked('od_escrow', $od_escrow, 'Y', '에스크로');
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $odr8Table="";
            $i=0;
            foreach($result as $key => $row){
                $bg = 'list'.($i%2);
        
                $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
                $sodr = $this->funcomplex_lib->get_order_list($row, $amount);
        
                $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
                $param=array($row['od_id']);
                $resObj = $this->query_model->returnQueryObj($sql,$param);
                $res=$resObj->result_array();
                $rowspan = $resObj->num_rows();
                $k=0;
                foreach($res as $key => $row2){
                    $gs = unserialize($row2['od_goods']);
                    $odr8Table.="
                    <tr class=\"{$bg}\">";
                    if($k == 0) {
                        $odr8Table.="
                        <td rowspan=\"{$rowspan}\">".$num--."</td>
                        <td rowspan=\"{$rowspan}\">
                            ".substr($row['od_time'],2,14)."
                            {$sodr['disp_test']}
                        </td>
                        <td rowspan=\"{$rowspan}\">".substr($row['return_date'],2,14)."</td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"".base_url()."mypage/seller_odr_form?od_id={$row['od_id']}\" onclick=\"win_open(this,'seller_odr_form','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                            {$sodr['disp_mobile']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                            <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                            <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                        </td>";
                    }
                    $odr8Table.="
                    <td class=\"td_img\"><a href=\"".base_url()."shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30)."</a></td>
                    <td class=\"td_itname\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row2['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_text($gs['gname'])."</a></td>";
                    if($k == 0) {
                        $odr8Table.="
                        <td rowspan=\"{$rowspan}\">
                            {$sodr['disp_od_name']}
                            {$sodr['disp_mb_id']}
                        </td>
                        <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>
                        <td rowspan=\"{$rowspan}\">{$sodr['disp_paytype']}</td>";
                    }
                    $k++;
                }
                $i++;
            }
            if($i==0)
                $odr8Table.='<tr><td colspan="10" class="empty_table">자료가 없습니다.</td></tr>';
            $page_rowsOpt=array('30'=>'','50'=>'','100'=>'','150'=>'');
            $page_rowsOpt['30']=option_selected('30',  $page_rows, '30줄 정렬');
            $page_rowsOpt['50']=option_selected('50',  $page_rows, '50줄 정렬');
            $page_rowsOpt['100']=option_selected('100',  $page_rows, '100줄 정렬');
            $page_rowsOpt['150']=option_selected('150',  $page_rows, '150줄 정렬');
            $btn_frmline = "
                <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
                <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
                <a href=\"".base_url()."MypageProcess/seller_odr_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);        

            $data = array(
                'code' => $code,
                'snbTagStart' => $snbTagStart,
                'snbTagEnd' => $snbTagEnd,
                'requestUrl' => $requestUrl,
                'admin_head_sub' => $admin_head_sub,
                'admin_footer_sub' => $admin_footer_sub,
                'jqueryUiDatePick' => $jqueryUiDatePick,
                'btn_frmline' => $btn_frmline,
                'sflOpt' => $sflOpt,
                'sel_fieldOpt' => $sel_fieldOpt,
                'payMethod' => $payMethod,
                'ectSelectChk' => $ectSelectChk,
                'page_rowsOpt' => $page_rowsOpt,
                'odr8Table' => $odr8Table,
                'pagination' => $pagination,
                'stx' => $stx,
                'tot_orderprice' => $tot_orderprice,
                'q1' => $q1,
                'page' => $page,
                'get_search_date' => $get_search_date,
                'total_count' => $total_count,
                'stx' => $stx,
                'q1' => $q1,
                'page' => $page
            );
            $this->load->view('mypage/seller/odr_8',$data);
        }
        private function seller_odr_stats($code,$snbTagStart,$snbTagEnd){
            $pg_title = "주문통계분석";
            $seller=$this->load->get_var('seller');
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $OM_TIME_YEAR=date("Y", time());
            $OM_TIME_MONTH=date("m", time());
            $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
            $OM_TIME_YMD=substr($OM_TIME_YMDHIS, 0, 10);
            $year=$this->input->get('year');
            $month=$this->input->get('month');
            $min_year=$this->input->get('min_year');

            if(!$year) $year = $OM_TIME_YEAR;
            if(!$month)	$month = $OM_TIME_MONTH;
            $search_date = preg_replace("/([0-9]{4})([0-9]{2})/", "\\1-\\2", $year.$month);
            $sql = " select COUNT(*) as cnt,MIN(od_time) as min_year from shop_order where dan IN(2,3,4,5,8)
                        and left(od_time,7) = ?
                        and seller_id = ?";
            $param=array($search_date,$seller['seller_code']);
            $row = $this->query_model->returnOneArr($sql,$param);
            $sum_count = (int)$row['cnt']; // 총상품판매수
            $min_year = substr($row['min_year'],0,4); // 가장작은 년도
            
            if(!$min_year) $min_year = $OM_TIME_YEAR; // 내역이없다면 현재 년도로

            $yearOpt="";
            for($i=$min_year; $i<=$OM_TIME_YEAR; $i++) {
                $yearOpt.="<option value=\"{$i}\"".get_selected($year, $i).">{$i}년</option>\n";
            }
            $monthOpt="";
            for($i=1;$i<=12;$i++) {
                $k = sprintf('%02d',$i);
                $monthOpt.="<option value=\"{$k}\"".get_selected($month, $k).">{$k}월</option>\n";
            }
            $statsTable="";
            $tot_count    = 0;
            $tot_price    = 0;
            $tot_supply   = 0;
            $tot_baesong  = 0;
            $tot_subprice = 0;
            $sum_countInst=$sum_count;
            if(!$sum_count) $sum_countInst = 1;

            for($i=1;$i<=31;$i++) {
                $day = sprintf("%02d", $i);
                $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $year.$month.$day);

                $sql = " select COUNT(*) as cnt,
                                SUM(goods_price) as price,
                                SUM(supply_price) as supply,
                                SUM(baesong_price) as baesong,
                                SUM(supply_price + baesong_price) as subprice
                        from shop_order
                        where dan IN(2,3,4,5,8)
                            and left(od_time,10) = ?
                            and seller_id = ?";
                $param=array($date,$seller['seller_code']);
                $sum = $this->query_model->returnOneArr($sql,$param);

                $rate = ((int)$sum['cnt'] / $sum_countInst * 100);
                $s_rate = number_format($rate, 1);

                $tot_count    += (int)$sum['cnt'];
                $tot_price    += (int)$sum['price'];
                $tot_supply   += (int)$sum['supply'];
                $tot_baesong  += (int)$sum['baesong'];
                $tot_subprice += (int)$sum['subprice'];

                $bg = 'list'.($i%2);
                $statsTable.="
                <tr class=\"{$bg}\">
                    <td>{$date}</td>
                    <td><div class=\"graph\"><span class=\"bar\" style=\"width:{$s_rate}%\"></span></div></td>
                    <td>{$s_rate}</td>
                    <td>".number_format($sum['cnt'])."</td>
                    <td class=\"tar\">".number_format($sum['price'])."</td>
                    <td class=\"tar\">".number_format($sum['supply'])."</td>
                    <td class=\"tar\">".number_format($sum['baesong'])."</td>
                    <td class=\"tar\">".number_format($sum['subprice'])."</td>
                </tr>";
                // 오늘날짜와 같다면 중지
                if($date == $OM_TIME_YMD) break;
            }
            $data=array('code'=>$code,'snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'admin_footer_sub'=>$admin_footer_sub,
                        'yearOpt'=>$yearOpt,'monthOpt'=>$monthOpt,'sum_count'=>$sum_count,'statsTable'=>$statsTable,
                        'tot_count'=>$tot_count,'tot_price'=>$tot_price,'tot_supply'=>$tot_supply,'tot_baesong'=>$tot_baesong,'tot_subprice'=>$tot_subprice);
            $this->load->view('mypage/seller/odr_stats',$data);
        }
        private function seller_odr_balance($code,$snbTagStart,$snbTagEnd){
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];

            $pg_title = "정산대기목록";
            $seller=$this->load->get_var('seller');
            $qstr=$this->load->get_var('qstr');
            $config=$this->load->get_var('config');
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $jqueryUiDatePick=$this->admin_template->datePicker();

            $fr_date=$this->input->get('fr_date');
            $to_date=$this->input->get('to_date');
            $sfl=$this->input->get('sfl');
            $stx=$this->input->get('stx');
            $page=$this->input->get('page');
            
            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

            $query_string = "code=$code$qstr";
            $q1 = $query_string;
            $q2 = $query_string."&page=$page";

            $sql_common = " from shop_order ";
            $sql_search = " where seller_id = '{$seller['seller_code']}' and sellerpay_yes = 0 and user_ok = 1 ";

            if($sfl && $stx)
                $sql_search .= " and $sfl like '%$stx%' ";

            if($fr_date && $to_date)
                $sql_search .= " and left(od_time,10) between '$fr_date' and '$to_date' ";
            else if($fr_date && !$to_date)
                $sql_search .= " and left(od_time,10) between '$fr_date' and '$fr_date' ";
            else if(!$fr_date && $to_date)
                $sql_search .= " and left(od_time,10) between '$to_date' and '$to_date' ";

            $sql_order = " order by od_time desc, index_no asc ";

            // 테이블의 전체 레코드수만 얻음
            $sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            $rows = 30;
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);

            $sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
            $result = $this->query_model->returnArr($sql);
            $sflOpt=array('','','');
            $sflOpt[0]=option_selected('od_id', $sfl, '주문번호');
            $sflOpt[1]=option_selected('od_no', $sfl, '일련번호');
            $sflOpt[2]=option_selected('name', $sfl, '주문자명');
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);

            $balanceTable="";
            $i=0;
            foreach($result as $key => $row){
                $gs = unserialize($row['od_goods']);
                if($i==0)
                    $balanceTable.='<tbody class="list">';
                $bg = 'list'.($i%2);
                $balanceTable.="
                <tr class=\"{$bg}\">
                    <td>".$num--."</td>
                    <td>".substr($row['od_time'],2,14)."</td>
                    <td><a href=\"".base_url()."mypage/seller_odr_form?od_id={$row['od_id']}\" onclick=\"win_open(this,'seller_odr_form','1200','800','yes');return false;\" class=\"fc_197\">".$row['od_id']."</a></td>
                    <td>".$this->funcomplex_lib->get_text($row['name'])."</td>
                    <td class=\"td_img\"><a href=\"".base_url()."shop/view?index_no={$row['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30)."</a></td>
                    <td class=\"td_itname\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_text($gs['gname'])."</a></td>
                    <td>".number_format($row['sum_qty'])."</td>
                    <td class=\"tar\">".number_format($row['goods_price'])."</td>
                    <td class=\"tar\">".number_format($row['supply_price'])."</td>
                    <td class=\"tar\">".number_format($row['baesong_price'])."</td>
                    <td class=\"td_price\">".number_format($row['supply_price']+$row['baesong_price'])."</td>
                    <td class=\"td_price\">".number_format($row['goods_price']+$row['baesong_price'])."</td>
                    <td>{$row['paymethod']}</td>
                </tr>";
                $i++;
            }
            if($i==0)
                $balanceTable.='<tbody><tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $data=array('code'=>$code,'snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'total_count'=>$total_count,
                        'admin_footer_sub'=>$admin_footer_sub,'jqueryUiDatePick'=>$jqueryUiDatePick,'sflOpt'=>$sflOpt,'stx'=>$stx,'get_search_date'=>$get_search_date,
                        'balanceTable'=>$balanceTable,'pagination'=>$pagination);
            $this->load->view('mypage/seller/balance',$data);
        }
        private function seller_odr_account($code,$snbTagStart,$snbTagEnd){
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];

            $pg_title = "정산완료목록";
            $seller=$this->load->get_var('seller');
            $qstr=$this->load->get_var('qstr');
            $config=$this->load->get_var('config');
            $admin_head_sub=$this->mypage_template->admin_head_sub($pg_title);
            $admin_footer_sub=$this->mypage_template->admin_footer_sub();
            $jqueryUiDatePick=$this->admin_template->datePicker();

            $fr_date=$this->input->get('fr_date');
            $to_date=$this->input->get('to_date');
            $sfl=$this->input->get('sfl');
            $stx=$this->input->get('stx');
            $page=$this->input->get('page');
            
            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

            $query_string = "code=$code$qstr";
            $q1 = $query_string;
            $q2 = $query_string."&page=$page";

            $sql_common = " from shop_order ";
            $sql_search = " where seller_id = '{$seller['seller_code']}' and sellerpay_yes = 1 and user_ok = 1 ";

            if($sfl && $stx)
                $sql_search .= " and $sfl like '%$stx%' ";

            if($fr_date && $to_date)
                $sql_search .= " and left(od_time,10) between '$fr_date' and '$to_date' ";
            else if($fr_date && !$to_date)
                $sql_search .= " and left(od_time,10) between '$fr_date' and '$fr_date' ";
            else if(!$fr_date && $to_date)
                $sql_search .= " and left(od_time,10) between '$to_date' and '$to_date' ";

            $sql_order = " order by od_time desc, index_no asc ";

            // 테이블의 전체 레코드수만 얻음
            $sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            $rows = 30;
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);

            $sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
            $result = $this->query_model->returnArr($sql);
            $sflOpt=array('','','');
            $sflOpt[0]=option_selected('od_id', $sfl, '주문번호');
            $sflOpt[1]=option_selected('od_no', $sfl, '일련번호');
            $sflOpt[2]=option_selected('name', $sfl, '주문자명');
            $get_search_date=get_search_date("fr_date", "to_date", $fr_date, $to_date);

            $odr_accountTable="";
            $i=0;
            foreach($result as $key => $row){
                $gs = unserialize($row['od_goods']);
                if($i==0)
                    $odr_accountTable.='<tbody class="list">';
                $bg = 'list'.($i%2);
                $odr_accountTable.="
                <tr class=\"{$bg}\">
                    <td>".$num--."</td>
                    <td>".substr($row['od_time'],2,14)."</td>
                    <td><a href=\"".base_url()."mypage/seller_odr_form?od_id={$row['od_id']}\" onclick=\"win_open(this,'seller_odr_form','1200','800','yes');return false;\" class=\"fc_197\">".$row['od_id']."</a></td>
                    <td>".$this->funcomplex_lib->get_text($row['name'])."</td>
                    <td class=\"td_img\"><a href=\"".base_url()."shop/view?index_no={$row['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30)."</a></td>
                    <td class=\"td_itname\"><a href=\"".base_url()."mypage/page?code=seller_goods_form&w=u&gs_id={$row['gs_id']}\" target=\"_blank\">".$this->funcomplex_lib->get_text($gs['gname'])."</a></td>
                    <td>".number_format($row['sum_qty'])."</td>
                    <td class=\"tar\">".number_format($row['goods_price'])."</td>
                    <td class=\"tar\">".number_format($row['supply_price'])."</td>
                    <td class=\"tar\">".number_format($row['baesong_price'])."</td>
                    <td class=\"td_price\">".number_format($row['supply_price']+$row['baesong_price'])."</td>
                    <td class=\"td_price\">".number_format($row['goods_price']+$row['baesong_price'])."</td>
                    <td>{$row['paymethod']}</td>
                </tr>";
                $i++;
            }
            if($i==0)
                $odr_accountTable.='<tbody><tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$q1.'&page=');
            $data=array('code'=>$code,'snbTagStart'=>$snbTagStart,'snbTagEnd'=>$snbTagEnd,'admin_head_sub'=>$admin_head_sub,'total_count'=>$total_count,
                        'admin_footer_sub'=>$admin_footer_sub,'jqueryUiDatePick'=>$jqueryUiDatePick,'sflOpt'=>$sflOpt,'stx'=>$stx,'get_search_date'=>$get_search_date,
                        'odr_accountTable'=>$odr_accountTable,'pagination'=>$pagination);
            $this->load->view('mypage/seller/odr_account',$data);
        }
    #공급자 화면 함수들끝
}