<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
#함수와 함수연결, 글로벌 변수까지 사용하는 함수들의 집합체
class Gvcomplex_lib {
    protected $ci;
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->library('user_agent');
        $this->ci->load->helper('global_helper');
        $this->ci->load->model('Query_model');
    }
     #함수와 글로벌 변수로만 이루어진 함수들   
        #coomon.lib.php에 있던 함수들

            // 날짜, 조회수의 경우 높은 순서대로 보여져야 하므로 $flag 를 추가
            // $flag : asc 낮은 순서 , desc 높은 순서
            // 제목별로 컬럼 정렬하는 QUERY STRING
            function subject_sort_link($col, $query_string){
                $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
                $filed=$this->ci->load->get_var('filed');
                $orderby=$this->ci->load->get_var('orderby');
                if($orderby == 'asc') {
                    $q2 = "&filed=$col&orderby=desc";
                } else {
                    $q2 = "&filed=$col&orderby=asc";
                }
                return "<a href=\"{$requestUrl}?{$query_string}{$q2}\">";
            }

            // 5차카테고리
            function tree_category($catecode){
                $pt_id=$this->ci->load->get_var('pt_id');
                $str = "";
                $t_catecode = $catecode;

                $sql_common = " from shop_category ";
                $sql_where  = " where cateuse = '0' and find_in_set( '{$pt_id}' , catehide) = '0' ";
                $sql_order  = " order by caterank, catecode ";

                $sql = " select count(*) as cnt {$sql_common} {$sql_where} and upcate = ? ";
                $data=array($catecode);
                $res=$this->ci->Query_model->returnOneArr($sql,$data);
                if($res['cnt'] < 1) {
                    $catecode = substr($catecode,0,-3);
                }

                $mod = 5; // 1줄당 노출 수
                $li_width = (int)(100 / $mod);
                $sql_common=$this->ci->db->escape_str($sql_common);
                $sql = "select * {$sql_common} {$sql_where} and upcate = ? {$sql_order}";
                $data=array($catecode);
                $result=$this->ci->Query_model->returnArr($sql,$data);
                $i=0;
                foreach($result as $key => $row){
                    if($i==0) $str .= '<ul class="sub_tree">';

                    $addclass = "";
                    if($t_catecode==$row['catecode'])
                        $addclass = ' class="active"';

                    $href = base_url().'shop/cateList?ca_id='.$row['catecode'];
                    $str .= "<li style=\"width:{$li_width}%\"{$addclass}><a href=\"{$href}\">{$row['catename']}</a></li>";
                    $i++;
                }

                if($i > 0) $str .= '</ul>';

                return $str;
            }

            // 5차카테고리 (bid)
            function tree_category_timesale($catecode, $status){
                $pt_id=$this->ci->load->get_var('pt_id');
                $str = "";
                $t_catecode = $catecode;

                $sql_common = " from shop_category ";
                $sql_where  = " where cateuse = '0' and find_in_set( '{$pt_id}' , catehide) = '0' ";
                $sql_order  = " order by caterank, catecode ";

                $sql = " select count(*) as cnt {$sql_common} {$sql_where} and upcate = ? ";
                $data=array($catecode);
                $res=$this->ci->Query_model->returnOneArr($sql,$data);
                if($res['cnt'] < 1) {
                    $catecode = substr($catecode,0,-3);
                }

                $mod = 5; // 1줄당 노출 수
                $li_width = (int)(100 / $mod);
                $sql_common=$this->ci->db->escape_str($sql_common);
                $sql = "select * {$sql_common} {$sql_where} and upcate = ? {$sql_order}";
                $data=array($catecode);
                $result=$this->ci->Query_model->returnArr($sql,$data);
                $i=0;
                foreach($result as $key => $row){
                    if($i==0) $str .= '<ul class="sub_tree">';

                    $addclass = "";
                    if($t_catecode==$row['catecode'])
                        $addclass = ' class="active"';

                        $href = base_url().'shop/timesale?ca_id='.$row['catecode'].'&status='.$status;
                        $str .= "<li style=\"width:{$li_width}%\"{$addclass}><a href=\"{$href}\">{$row['catename']}</a></li>";
                        $i++;
                }

                if($i > 0) $str .= '</ul>';

                return $str;
            }

            //  송장번호 일괄등록시 배송추척 URL 추출 (본사, 업체 공용)
            function get_info_delivery($company){
                $config=$this->ci->load->get_var('config');
                if(!$company) return '';
                $fld = trim($company);
                $info = array_filter(explode(",",$config['delivery_company']));
                foreach($info as $k=>$v) {
                    $arr = explode("|",trim($info[$k]));
                    if(trim($arr[0]) == trim($company)){
                        $fld = trim($info[$k]);
                        break;
                    }
                }
                return $fld;
            }
            //  상품 상세페이지 구매하기, 장바구니, 찜 버튼
            function get_buy_button($msg, $gs_id, $is_open, $is_soldout) {
                $gs = $this->ci->load->get_var('gs');
                $pt_id = $this->ci->load->get_var('pt_id');
            
                // 버튼 설정값을 배열로 정의
                $buttons = [
                    1 => [
                        'css' => 'cart',
                        'name' => '장바구니 담기',
                        'direct' => 'cart',
                    ],
                    2 => [
                        'css' => 'buy',
                        'name' => '바로 구매',
                        'direct' => 'buy',
                    ],
                ];
            
                // 타임세일 상품
                if ($gs['type'] == "timesale") {
                    $buttons[2] = [
                        'css' => 'buy',
                        'name' => '입찰하기',
                        'direct' => 'buy',
                    ];
                }

                if ($gs['type'] == "timesale" && $is_open == 'wait') {
                    $buttons[2] = [
                        'css' => 'wait',
                        'name' => '오픈예정',
                        'direct' => '',
                    ];
                }

                if ($gs['type'] == "timesale" && $is_open == 'end') {
                    $buttons[2] = [
                        'css' => 'wait',
                        'name' => '입찰완료',
                        'direct' => '',
                    ];
                }

                if ($gs['type'] == "timesale" && $is_soldout) {
                    $buttons[2] = [
                        'css' => 'wait',
                        'name' => '품절',
                        'direct' => '',
                    ];
                }
            
                $str = '';
                
                // 각 버튼에 대해 HTML 생성
                foreach ($buttons as $i => $button) {
                    if ($is_open != 'wait') {
                        if ($msg) {
                            $str .= "<span><a href=\"javascript:alert('$msg');\" class=\"btn_buy {$button['css']}\">{$button['name']}</a></span>";
                        } else {
                            $str .= ($button['direct'] == 'wish')
                                ? "<span><a href=\"javascript:item_wish(document.fbuyform);\" class=\"btn_buy {$button['css']}\">{$button['name']}</a></span>"
                                : "<span><a href=\"javascript:fbuyform_submit('{$button['direct']}');\" class=\"btn_buy {$button['css']}\">{$button['name']}</a></span>";
                        }
                    } else {
                        $str .= "<span><a href=\"javascript:alert('오픈 예정 상품입니다.');\" class=\"btn_buy {$button['css']}\">{$button['name']}</a></span>";
                    }
                }
            
                return $str;
            }

            function get_seller_name($mb_id){
                $config=$this->ci->load->get_var('config');
                $sellerName = '';
                if(substr($mb_id,0,3) == 'AP-') {
                    $sql="select company_name from shop_seller where seller_code = ?";
                    $data=array($mb_id);
                    $row = $this->ci->Query_model->returnOneArr($sql,$data);
                    $sellerName = $row['company_name'];
                } else if($mb_id == 'admin') {
                    $sellerName = $config['company_name'];
                } else if($mb_id != 'admin') {
                    $sql="select company_name from shop_partner where mb_id = ?";
                    $data=array($mb_id);
                    $row=$this->ci->Query_model->returnOneArr($sql,$data);
                    $sellerName = $row['company_name'];
                }

                return $sellerName;
            }

        #global.lip.php에 있던 함수들
            // goo.gl 짧은주소 만들기
            function google_short_url($longUrl){
                $default=$this->ci->load->get_var('default');

                // Get API key from : http://code.google.com/apis/console/
                // URL Shortener API ON
                $apiKey = $default['de_googl_shorturl_apikey'];

                $postData = array('longUrl' => $longUrl, 'key' => $apiKey);
                $jsonData = json_encode($postData);

                $curlObj = curl_init();

                curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key=' . $apiKey);
                curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($curlObj, CURLOPT_HEADER, 0);
                curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
                curl_setopt($curlObj, CURLOPT_POST, 1);
                curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

                $response = curl_exec($curlObj);

                // Change the response json string to object
                $json = json_decode($response);

                curl_close($curlObj);

                return $json->id;
            }
            // 쿠폰 : 구매 가능한 상품
            function sql_coupon_log($lo_id){
                $member=$this->ci->load->get_var('member');

                // 쿠폰유효 기간 (시간대)
                $fr_hour = "(cp_inv_shour1 = '99' or cp_inv_shour1 <= date_format(now(),'%H'))";
                $to_hour = "(cp_inv_shour2 = '99' or cp_inv_shour2 > date_format(now(),'%H'))";

                $sql_common = " from shop_coupon_log ";
                $sql_where  = " where mb_id='{$member['id']}' and mb_use='0' and lo_id='{$lo_id}' ";
                $sql_where .= " and ((cp_inv_type='0' and ('{$fr_hour}' and '{$to_hour}')) ";
                $sql_where .= " or (cp_inv_type='1' and date_add(`cp_wdate`, interval `cp_inv_day` day) > now())) ";

                $sql_common=$this->ci->db->escape_str($sql_common);
                $sql="select * {$sql_common} {$sql_where}";
                $row=$this->ci->Query_model->returnOneArr($sql);

                switch($row['cp_use_part']) {
                    case '0': // 전체상품에 쿠폰사용 가능
                        $sql_search = "";
                        break;
                    case '1': // 일부 상품만 쿠폰사용 가능
                        if($row['cp_use_goods']) {
                            $sql_search = " and index_no IN($row[cp_use_goods]) ";
                        }
                        break;
                    case '2': // 일부 카테고리만 쿠폰사용 가능
                        if($row['cp_use_category']) {
                            $sql_search = " and ca_id IN($row[cp_use_category]) ";
                        }
                        break;
                    case '3': // 일부 상품은 쿠폰사용 불가
                        if($row['cp_use_goods']) {
                            $sql_search = " and index_no NOT IN($row[cp_use_goods]) ";
                        }
                        break;
                    case '4': // 일부 카테고리는 쿠폰사용 불가
                        if($row['cp_use_category']) {
                            $sql_search = " and ca_id NOT IN($row[cp_use_category]) ";
                        }
                        break;
                }

                return $sql_search;
            }
            // 출력멘트 리턴
            function get_head_title($fild, $mb_id){
                $config=$this->ci->load->get_var('config');
                #$row = sql_fetch("select $fild from shop_partner where mb_id='$mb_id' ");
                $sql="select {$fild} from shop_partner where mb_id=?";
                $data=array($mb_id);
                $row = $this->ci->Query_model->returnOneArr($sql,$data);
                if(!isset($row[$fild]))
                    $row[$fild] = $config[$fild];

                $str = $row[$fild];

                return $str;
            }
            // 관리자 체크.
            function is_admin($grade=''){
                $member=$this->ci->load->get_var('member');
                $grade = $grade ? $grade : $member['grade'];

                switch($grade){
                    case '1' :
                        return true;
                        break;
                    case '99' :
                        return true;
                        break;
                    default :
                        return false;
                }
            }
            // 입점사인가?
            function is_seller($mb_id){
                $config=$this->ci->load->get_var('config');

                if(!$mb_id) return '';
                $sql="select state from shop_seller where mb_id = ?";
                $data=array($mb_id);
                $sr=$this->ci->Query_model->returnOneArr($sql,$data);

                if((!isset($config['shop_state']) || $config['shop_state'] == 0) && $sr['state']) {
                    return true;
                } else {
                    return false;
                }
            }
            // 장바구니에 담긴 상품수
            function get_cart_count($mb_id){
                $sql = "SELECT a.*
                        FROM shop_cart as a
                        JOIN shop_goods as b
                        ON a.gs_id = b.index_no
                        WHERE a.mb_id = ? AND a.ct_select = '0' AND a.is_direct = '0' AND b.isopen = '1'";
                $result=$this->ci->Query_model->returnQueryObj($sql,$mb_id);
                $cart_count=$result->num_rows();
                return (int)$cart_count;
            }
            // 배송비를 구분자로 나눔 (주문폼으로 넘기기위한 작업)
            function get_tune_sendcost($com_array, $val_array, $item_sendcost){
                if(!$item_sendcost)
                    return;

                $com = array();
                $val = array();
                for($i=0; $i<count($com_array); $i++) {
                    if(is_array($com_array[$i])) {
                        for($j=0; $j<count($com_array[$i]); $j++) {
                            $com[] = $com_array[$i][$j];
                            $val[] = $val_array[$i][$j];
                        }
                    } else {
                        $com[] = $com_array[$i];
                        $val[] = $val_array[$i];
                    }
                }

                // 배열 재정렬
                $dlcomb = array_combine($com,$val);

                // 빈 배열을 채움.
                $dltune = array();
                for($i=0; $i<count($item_sendcost); $i++) {
                    if(isset($dlcomb[$i])) {
                        $dltune[$i] = $dlcomb[$i];
                    } else {
                        $dltune[$i] = 0;
                    }
                }

                return implode('|', $dltune);
            }
            // 상품 공통쿼리
            function sql_goods_list($sql_search=''){
                $pt_id=$this->ci->load->get_var('pt_id');
                $auth_good=$this->ci->load->get_var('auth_good');

                $pt_id=$this->ci->db->escape_str($pt_id); 
                $addsql="";
                if($auth_good) // 가맹점 상품판매권한이 있나?
                    $addsql=" or ( use_aff = '1' and mb_id = '$pt_id' ) ";

                $sql = " from shop_goods
                        where shop_state = '0'
                        and isopen IN('1','2','5')
                        and (use_aff = '0'{$addsql})
                        and find_in_set('$pt_id', use_hide) = '0'
                        {$sql_search} ";

                return $sql;
            }
            // 상품 검색쿼리
            function sql_goods_search($sql_search=''){
                $auth_good=$this->ci->load->get_var('auth_good');
                $pt_id=$this->ci->load->get_var('pt_id');
                $pt_id=$this->ci->db->escape_str($pt_id);

                $addsql="";
                if($auth_good) // 가맹점 상품판매권한이 있나?
                    $addsql = " or ( a.use_aff = '1' and a.mb_id = '$pt_id' ) ";
                
                $sql = " from shop_goods a, shop_category b
                        where a.ca_id = b.catecode
                        and a.shop_state = '0'
                        and a.isopen IN('1','2','5')
                        and b.cateuse = '0'
                        and (a.use_aff = '0'{$addsql})
                        and find_in_set('$pt_id', a.use_hide) = '0'
                        and find_in_set('$pt_id', b.catehide) = '0'
                        {$sql_search} ";

                return $sql;
            }

            // 상품 가격정보의 배열을 리턴
            function get_sale_price($gs_id){
                $member=$this->ci->load->get_var('member');

                $sql="select * from shop_member_grade where gb_no = ?";
                $data=array($member['grade']);
                $gb=$this->ci->Query_model->returnOneArr($sql,$data);

                $sql2="select goods_price,use_aff from shop_goods where index_no = ?";
                $data2=array($gs_id);
                $gs=$this->ci->Query_model->returnOneArr($sql2,$data2);

                $price = $gs['goods_price'];

                if($gb['gb_sale'] > 0 && $member['id'] && !$gs['use_aff']) {
                    if($gb['gb_sale_rate'] == 1) {
                        // 금액으로 할인
                        $price = $gs['goods_price'] - $gb['gb_sale'];
                    } else  { // 퍼센트로 할인 
                        $price = round($gs['goods_price'] - (($gs['goods_price'] / 100) * $gb['gb_sale']),-1);
                    } if(strlen($price) > 1 && $gb['gb_sale_unit']) {
                        $price = floor((int)$price/(int)$gb['gb_sale_unit']) * (int)$gb['gb_sale_unit'];
                    }
                }

                return (int)$price;
            }

            // 상품 가격정보의 배열을 리턴 (bid)
            function get_timesale_goods_price($gs_id){
                $sql="select goods_price,use_aff from shop_goods where index_no = ?";
                $data=array($gs_id);
                $gs=$this->ci->Query_model->returnOneArr($sql,$data);

                $price = $gs['goods_price'];

                return (int)$price;
            }

            // 상품 가격정보의 배열을 리턴 (bid)
            function get_timesale_normal_price($gs_id){
                $sql="select normal_price,use_aff from shop_goods where index_no = ?";
                $data=array($gs_id);
                $gs=$this->ci->Query_model->returnOneArr($sql,$data);

                $price = $gs['normal_price'];

                return (int)$price;
            }

            // 연속배너 sql
            function sql_banner_rows($code, $mb_id){
                $config=$this->ci->load->get_var('config');
                $mk=$this->ci->load->get_var('mk');
                $code=$this->ci->db->escape_str($code);
                $mb_id=$this->ci->db->escape_str($mb_id);
                $sql_where = " where bn_device = 'pc' and bn_theme = '{$mk['theme']}' ";
                $sql_where .= " and bn_code = '{$code}' and bn_use = '1' ";
                $sql_order  = " order by bn_order asc ";

                $sql = " select * from shop_banner {$sql_where} and mb_id = '{$mb_id}' {$sql_order}";
                $row=$this->ci->Query_model->returnOneArr($sql);
                if(!$row['bn_id'] && $mb_id != 'admin')
                    $sql = " select * from shop_banner {$sql_where} and mb_id = 'admin' {$sql_order} ";

                return $sql;
            }
            // 랜덤배너 sql
            function sql_banner($code, $mb_id){
                $config=$this->ci->load->get_var('config');
                $mk=$this->ci->load->get_var('mk');
                $code=$this->ci->db->escape_str($code);
                $mb_id=$this->ci->db->escape_str($mb_id);
                $sql_where = " where bn_device = 'pc' and bn_theme = '{$mk['theme']}' ";
                $sql_where .= " and bn_code = '{$code}' and bn_use = '1' ";
                $sql_order  = " order by rand() limit 1 ";
                $sql = " select * from shop_banner {$sql_where} and mb_id = '{$mb_id}' {$sql_order} ";
                $row=$this->ci->Query_model->returnOneArr($sql);
                if(!$row['bn_id'] && $mb_id != 'admin')
                    $sql = " select * from shop_banner {$sql_where} and mb_id = 'admin' {$sql_order} ";

                return $sql;
            }
            // 찜하기
            function zzimCheck($gs_id){
                $member=$this->ci->load->get_var('member');

                $sql = "select count(*) as cnt from shop_wish where mb_id=? and gs_id=?";
                $data=array($member['id'],$gs_id);
                $row=$this->ci->Query_model->returnOneArr($sql,$data);

                return ($row['cnt']) ? "zzim on" : "zzim";
            }

            // 분류 쿼리
            function sql_query_cgy($upcate, $type='', $rows=''){
                $pt_id=$this->ci->load->get_var('pt_id');
                if($upcate == 'all')
                    $sql_search = " length(catecode) = '3' and catecode != 999";
                else
                    $sql_search = " upcate = '$upcate' and upcate <> '' ";

                $sql_search .= " and cateuse = '0' and find_in_set('$pt_id', catehide) = '0' ";

                if($type == 'COUNT') {
                    $sql = "SELECT count(*) AS cnt FROM shop_category WHERE {$sql_search}";
                    return $this->ci->Query_model->returnOneArr($sql);
                } else if($type == 'LIMIT') {
                    $sql = "SELECT * FROM shop_category WHERE {$sql_search} ORDER BY caterank, catecode LIMIT {$rows}";
                    return $this->ci->Query_model($sql);
                } else {
                    $sql = "SELECT * FROM shop_category WHERE {$sql_search} ORDER BY caterank, catecode ";
                    return $this->ci->Query_model->returnArr($sql);
                }
            }

            function sql_field_names($table){
                $columns = array();

                $table=$this->ci->db->escape_str($table);
                $sql = " select * from $table limit 1 ";
                $result=$this->ci->Query_model->returnOneArr($sql);
                foreach($result as $key => $row){
                    $columns[]= $key;
                }
                return $columns;
            }
            function add_stylesheet($stylesheet, $order=0){
                $this->ci->load->library('html_process');
                if(trim($stylesheet))
                    return $this->ci->html_process->merge_stylesheet($stylesheet, $order);
            }
            function add_javascript($javascript, $order=0){
                $this->ci->load->library('html_process');
                if(trim($javascript))
                    return  $this->ci->html_process->merge_javascript($javascript, $order);
            }
        #mobile.lib.php에 있던 함수들
            // 5차카테고리
            function mobile_tree_category($catecode){
                $returnCate="";
                $pt_id=$this->ci->load->get_var('pt_id');

                $t_catecode = $catecode;

                $sql_common = " from shop_category ";
                $sql_where  = " where cateuse = '0' and find_in_set('$pt_id', catehide) = '0' ";
                $sql_order  = " order by caterank, catecode ";

                $sql = " select count(*) as cnt {$sql_common} {$sql_where} and upcate = ? ";
                $data=array($catecode);
                $res=$this->ci->Query_model->returnOneArr($sql,$data);
                if($res['cnt'] < 1) {
                    $catecode = substr($catecode,0,-3);
                }

                $sql = "select * {$sql_common} {$sql_where} and upcate = ? {$sql_order} ";
                $data=array($catecode);
                $result=$this->ci->Query_model->returnArr($sql,$data);
                $i=0;
                foreach($result as $key => $row){
                    if($i==0) {
                        $returnCate.='<div id="sct_ct">';
                        $returnCate.='<ul>';
                    }
                    $addclass = "";
                    if($t_catecode==$row['catecode'])
                        $addclass = ' class="sct_here"';

                    $href = base_url().'m/shop/cateList?ca_id='.$row['catecode'];

                    $returnCate.="<li><a href=\"{$href}\"{$addclass}>{$row['catename']}</a></li>\n";
                    $i++;
                }

                if($i > 0) {
                    $returnCate.= '</ul>';
                    $returnCate.= '</div>';
                }
                return $returnCate;
            }

            function mobile_timesale_tree_category($catecode, $status){
                $returnCate="";
                $pt_id=$this->ci->load->get_var('pt_id');

                $t_catecode = $catecode;

                $sql_common = " from shop_category ";
                $sql_where  = " where cateuse = '0' and find_in_set('$pt_id', catehide) = '0' ";
                $sql_order  = " order by caterank, catecode ";

                $sql = " select count(*) as cnt {$sql_common} {$sql_where} and upcate = ? ";
                $data=array($catecode);
                $res=$this->ci->Query_model->returnOneArr($sql,$data);
                if($res['cnt'] < 1) {
                    $catecode = substr($catecode,0,-3);
                }

                $sql = "select * {$sql_common} {$sql_where} and upcate = ? {$sql_order} ";
                $data=array($catecode);
                $result=$this->ci->Query_model->returnArr($sql,$data);
                $i=0;
                foreach($result as $key => $row){
                    if($i==0) {
                        $returnCate.='<div id="sct_ct">';
                        $returnCate.='<ul>';
                    }
                    $addclass = "";
                    if($t_catecode==$row['catecode'])
                        $addclass = ' class="sct_here"';

                        $href = base_url().'m/shop/timesale?ca_id='.$row['catecode'].'&status='.$status;
                        $returnCate.="<li><a href=\"{$href}\"{$addclass}>{$row['catename']}</a></li>\n";
                        $i++;
                }

                if($i > 0) {
                    $returnCate.= '</ul>';
                    $returnCate.= '</div>';
                }
                return $returnCate;
            }
            
            //  상품 상세페이지 : 구매하기, 장바구니, 찜 버튼
            function mobile_buy_button($msg, $gs_id, $is_open, $is_soldout) {
                $gs = $this->ci->load->get_var('gs');
                $pt_id = $this->ci->load->get_var('pt_id');
            
                // 버튼 설정 배열
                $buttons = [
                    1 => [
                        'css' => 'cart',
                        'name' => '장바구니 담기',
                        'direct' => 'cart',
                    ],
                    2 => [
                        'css' => 'buy',
                        'name' => '바로 구매',
                        'direct' => 'buy',
                    ],
                ];
            
                // 타입세일 관련 조건 처리
                if ($gs['type'] == "timesale") {
                    $buttons[2] = [
                        'css' => 'buy',
                        'name' => '입찰하기',
                        'direct' => 'buy',
                    ];
            
                    if ($is_open == 'wait') {
                        $buttons[2] = [
                            'css' => 'wait',
                            'name' => '오픈예정',
                            'direct' => '',
                        ];
                    } elseif ($is_open == 'end') {
                        $buttons[2] = [
                            'css' => 'wait',
                            'name' => '입찰완료',
                            'direct' => '',
                        ];
                    } elseif ($is_soldout) {
                        $buttons[2] = [
                            'css' => 'wait',
                            'name' => '품절',
                            'direct' => '',
                        ];
                    }
                }
            
                $str = "<div class=\"sp_btn\">";
                foreach ($buttons as $i => $button) {
                    $css_class = "btn_buy {$button['css']}";
                    $btn_name = $button['name'];
                    $sw_direct = $button['direct'];
            
                    if ($msg) {
                        $str .= "<span><button type=\"button\" onclick=\"alert('$msg');\" class='$css_class'>$btn_name</button></span>";
                    } else {
                        if ($sw_direct == "wish") {
                            $str .= "<span><button type=\"button\" onclick=\"item_wish(document.fbuyform);\" class='$css_class'>$btn_name</button></span>";
                        } elseif ($sw_direct) {
                            $str .= "<span><button type=\"button\" onclick=\"fbuyform_submit('$sw_direct');\" class='$css_class'>$btn_name</button></span>";
                        } else {
                            $str .= "<span><button type=\"button\" onclick=\"alert('오픈 예정 상품입니다.');\" class='$css_class'>$btn_name</button></span>";
                        }
                    }
                }
                $str .= "</div>";
            
                return $str;
            }
            
        #navberpay.lib.php에 있던 함수들
            function get_naverpay_return_info($mb_id){
                $config=$this->ci->load->get_var('config');
            
                $data = '';
                $address1 = trim($config['company_addr']);
                $address2 = ' ';
            
                $data .= '<returnInfo>';
                $data .= '<zipcode><![CDATA['.$config['company_zip'].']]></zipcode>';
                $data .= '<address1><![CDATA['.$address1.']]></address1>';
                $data .= '<address2><![CDATA['.$address2.']]></address2>';
                $data .= '<sellername><![CDATA['.$config['company_name'].']]></sellername>';
                $data .= '<contact1><![CDATA['.$config['company_tel'].']]></contact1>';
                $data .= '</returnInfo>';
            
                return $data;
            }
        #partner.lib.php
            // 만료일 기간연장
            function get_term_date($term='1'){
                $config=$this->ci->load->get_var('config');

                // 관리비를 사용중인가?
                if($config['pf_expire_use']) {
                    $term_date = date("Y-m-d", strtotime("+{$term} month", time()));
                } else {
                    $term_date = '9999-12-31';
                }

                return $term_date;
            }
            // 실제 도메인만 추출
            function get_basedomain($url){
                $config=$this->ci->load->get_var('config');
                // 모든 공백을 제거
                $basedomain = preg_replace("/\s+/", "", $config['pf_basedomain']);

                $value = strtolower(trim($url));
                $host="";
                if(preg_match('/^(?:(?:[0-9a-z_]+):\/\/)?((?:[0-9a-z_\d\-]{2,}\.)+[0-9a-z_]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?$/i', $value)) {
                    preg_match('/([0-9a-z_\d\-]+(?:\.(?:'.$basedomain.')){1,2})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?$/i', $value, $matches);
                    $host =(!isset($matches[1])) ? $value : $matches[1];
                }

                return $host;
            }
        #register.lib.php
            function reserve_mb_id($reg_mb_id){
                $config=$this->ci->load->get_var('config');

                if(preg_match("/[\,]?{$reg_mb_id}/i", $config['prohibit_id']))
                    return "이미 예약된 단어로 사용할 수 없는 회원아이디 입니다.";
                else
                    return "";
            }
            // 금지 메일 도메인 검사
            function prohibit_mb_email($reg_mb_email){
                $config=$this->ci->load->get_var('config');

                list($id, $domain) = explode("@", $reg_mb_email);
                $email_domains = explode("\n", trim($config['prohibit_email']));
                $email_domains = array_map('trim', $email_domains);
                $email_domains = array_map('strtolower', $email_domains);
                $email_domain = strtolower($domain);

                if(in_array($email_domain, $email_domains))
                    return "$domain 메일은 사용할 수 없습니다.";

                return "";
            }
        #login-oauth.php
            function get_login_oauth($type, $img='') {
                
                if(!$type) return;
                $url=$this->ci->load->get_var('url');
                $urlencode=$this->ci->load->get_var('urlencode');
                $return_url = (isset($url) && $url) ? $url : $urlencode;
                $strJs='<script>';
                $strJs.='function login_oauth(type,ww,wh) {'.PHP_EOL;
                $strJs.='var url = "'.base_url().'plugin/login_oauth/login_with_" + type;';
                $strJs.='var opt = "width=" + ww + ",height=" + wh + ",left=0,top=0,scrollbars=1,toolbars=no,resizable=yes";';
                $strJs.='window.open(url,type,opt);}';
                $strJs.='</script>';
                $strJs.='<input type="hidden" name="slr_url" value="'.$return_url.'">';
                
                // Size
                $ww="";
                $wh="";
                switch($type) {
                    case 'facebook'	: $ww = 1024; $wh = 640; break;
                    case 'twitter'	: $ww = 600; $wh = 600; break;
                    case 'google'	: $ww = 460; $wh = 640; break;
                    case 'naver'	: $ww = 460; $wh = 517; break;
                    case 'kakao'	: $ww = 480; $wh = 680;	break;
                    default			: $ww = 600; $wh = 600; break;
                }
                    
                $str = "login_oauth('".$type."','".$ww."','".$wh."');";
                if($img) {
                    if($img == '1') { // Link
                        switch($type) {
                            case 'facebook':
                                $str = '<a href="javascript:'.$str.'" class="bt_face"><span class="bt_ic"><img src="'.base_url().'img/ic_face.png" alt="Sign in with '.$type.'"></span> 페이스북 로그인</a>';
                                break;
                            case 'naver':
                                $str= '<a href="javascript:'.$str.'" class="bt_naver"><span class="bt_ic"><img src="'.base_url().'img/ic_naver.png" alt="Sign in with '.$type.'"></span> 네이버 로그인</a>';
                                break;
                            case 'kakao':
                                $str= '<a href="javascript:'.$str.'" class="bt_kakao"><span class="bt_ic"><img src="'.base_url().'img/ic_kakao.png" alt="Sign in with '.$type.'"></span> 카카오톡 로그인</a>';
                                break;
                            case 'google':
                                $str= '<a href="javascript:'.$str.'" class="bt_google"><span class="bt_ic"><img src="'.base_url().'img/ic_google.png" alt="Sign in with '.$type.'"></span> 구글 로그인</a>';
                                break;
                        }
                    } else {
                        $str.= '<a href="javascript:'.$str.'"><img src="'.$img.'" alt="Sign in with '.$type.'"></a>';
                    }
                } else {
                    $img = base_url().'plugin/login_oauth/img/'.$type;
                    $str.= '<a href="javascript:'.$str.'"><img src="'.$img.'" alt="Sign in with '.$type.'"></a>';
                }
                $returnObj['str']=$str;
                $returnObj['strJs']=$strJs;
                return $returnObj;
            }
            //함수와 글러벌 변수가 같이 섞여있는 함수들
}
?>