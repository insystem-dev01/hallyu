<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
// 문자열 암복호화
class str_encrypt
{
    var $salt;
    var $lenght;

    function __construct($salt = '')
    {
        if (!$salt)
            $this->salt = md5(preg_replace('/[^0-9A-Za-z]/', substr('root', -1), '')); #실제 디비의 아이디와 패스워드 자리 substr('디비접속 아이디', -1), '디비접속 비밀번호')
        else
            $this->salt = $salt;

        $this->length = strlen($this->salt);
    }

    function encrypt($str)
    {
        $length = strlen($str);
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $char    = substr($str, $i, 1);
            $keychar = substr($this->salt, ($i % $this->length) - 1, 1);
            $char    = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return base64_encode($result);
    }

    function decrypt($str)
    {
        $result = '';
        $str    = base64_decode($str);
        $length = strlen($str);

        for ($i = 0; $i < $length; $i++) {
            $char    = substr($str, $i, 1);
            $keychar = substr($this->salt, ($i % $this->length) - 1, 1);
            $char    = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }
}
#함수와 함수연결, 글로벌 변수까지 사용하는 함수들의 집합체
class Funcomplex_lib
{
    protected $ci;
    protected $OM_TIME_YMDHIS;
    protected $OM_TIME_YMD;
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('global_lib');
        $this->ci->load->library('gvcomplex_lib');
        $this->ci->load->library('user_agent');
        $this->ci->load->library('thumbnail_lib');
        $this->ci->load->library('partner_lib');
        $this->ci->load->helper('global_helper');
        $this->ci->load->helper('common_helper');
        $this->ci->load->model('Query_model');
        $this->OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $this->OM_TIME_YMD = substr($this->OM_TIME_YMDHIS, 0, 10);
        $this->ci->load->config('shop_extend');
    }
    #함수와 함수가 이루어진 함수들
    #common.lib.php에 있던것
    // 회원 레이어
    function get_sideview($mb_id, $name)
    {
        // 에러방지를 위해 기호를 치환
        $name = $this->get_text($name, 0, true);

        if (!$this->ci->gvcomplex_lib->is_admin() || !$mb_id || $mb_id == 'admin')
            return $name;

        // 사이드뷰 시작
        $mb = $this->ci->global_lib->get_member($mb_id, 'email, cellphone');

        $member = $this->ci->load->get_var('member');

        $phone = conv_number($mb['cellphone']);

        $email = get_email_address($mb['email']);
        $email_enc = new str_encrypt();
        $email = $email_enc->encrypt($email);
        $email = $this->get_text($email);

        $str = "<span class=\"sv_wrap\">\n";
        $str .= "<a href=\"javascript:void(0);\" class=\"sv_member\">{$name}</a>\n";

        $str2 = "<span class=\"sv\">\n";

        $str2 .= "<a href=\"" . base_url() . "admin/pop_memberform?mb_id={$mb_id}\" onclick=\"win_open(this,'win_member','1200','600','yes');return false;\">회원정보수정</a>\n";

        if ($this->ci->gvcomplex_lib->is_seller($mb_id))
            $str2 .= "<a href=\"" . base_url() . "admin/pop_sellerform?mb_id={$mb_id}\" onclick=\"win_open(this,'win_seller','1200','600','yes');return false;\">입점사정보수정</a>\n";

        if ($email)
            $str2 .= "<a href=\"" . base_url() . "admin/formmail?mb_id=" . $mb_id . "&name=" . urlencode($name) . "&email=" . $email . "\" onclick=\"win_open(this,'win_email','650','580','no'); return false;\">메일보내기</a>\n";

        if ($phone)
            $str2 .= "<a href=\"" . base_url() . "admin/sms/sms_user?ph={$phone}\" onclick=\"win_open(this,'win_sms','300','360','no'); return false;\">SMS보내기</a>\n";

        if ($member['grade'] == "99") {
            $str2 .= "<a href=\"" . base_url() . "AdminProcess/admin_ss_login?mb_id={$mb_id}\" target=\"_blank\">쇼핑몰로그인</a>\n";
        }

        if ($this->is_partner($mb_id))
            $str2 .= "<a href=\"" . base_url() . "AdminProcess/admin_ss_login?mb_id={$mb_id}&lg_type=P\" target=\"_blank\">가맹점로그인</a>\n";

        if ($this->ci->gvcomplex_lib->is_seller($mb_id))
            $str2 .= "<a href=\"" . base_url() . "AdminProcess/admin_ss_login?mb_id={$mb_id}&lg_type=S\" target=\"_blank\">입점사로그인</a>\n";

        $str2 .= "</span>\n";
        $str .= $str2;
        $str .= "\n<noscript class=\"sv_nojs\">" . $str2 . "</noscript>";

        $str .= "</span>";

        return $str;
    }

    // 인기검색어
    function get_popular($rows, $pt_id)
    {
        $str = "";
        $sql = " select pp_word, count(*) as cnt
                        from shop_popular
                        where pt_id = ?
                            and TRIM(pp_word) <> ''
                        group by pp_word
                        order by cnt desc
                        limit {$rows} ";
        $data = array($pt_id);
        $result = $this->ci->Query_model->returnArr($sql, $data);
        foreach ($result as $key => $row) {
            $word = $this->get_text($row['pp_word']);
            $href = base_url() . 'shop/search?ss_tx=' . $word;
            $str .= "<li><a href=\"{$href}\">{$word}</a></li>\n";
        }
        return $str;
    }

    // 주문관리(엑셀저장) 공통
    function excel_order_list($row, $amount)
    {
        // 결제수단
        $od_paytype = '';
        if ($row['paymethod']) {
            $od_paytype = $row['paymethod'];

            if ($row['paymethod'] == '간편결제') {
                switch ($row['od_pg']) {
                    case 'lg':
                        $od_paytype = 'PAYNOW';
                        break;
                    case 'inicis':
                        $od_paytype = 'KPAY';
                        break;
                    case 'kcp':
                        $od_paytype = 'PAYCO';
                        break;
                    default:
                        $od_paytype = $row['paymethod'];
                        break;
                }
            }
        } else {
            $od_paytype = '결제수단없음';
        }

        // 포인트결제가 포함되어있나?
        if ($amount['usepoint'] > 0 && $row['paymethod'] != '포인트')
            $od_paytype .= '+포인트';

        // 예치금결제가 포함되어있나?
        if ($amount['usemileage'] > 0 && $row['paymethod'] != '예치금')
            $od_paytype .= '+예치금';

        // 에스크로 결제인가?
        if ($row['od_escrow'])
            $od_paytype .= '(에스크로)';

        // 테스트 주문인가?
        $od_test = '';
        if ($row['od_test'])
            $od_test = '(테스트)';

        // 모바일 주문인가?
        $od_mobile = 'PC';
        if ($row['od_mobile'])
            $od_mobile = '모바일';

        // 주문자가 회원인가?
        if ($row['mb_id'])
            $od_mb_id = $row['mb_id'];
        else
            $od_mb_id = '비회원';

        if (!$row['pt_id'] || $row['pt_id'] == 'admin')
            $od_pt_id = '본사';
        else {
            $mb = $this->ci->global_lib->get_member($row['pt_id'], 'name');
            if (!$mb['name']) $mb['name'] = '정보없음';
            $od_pt_id = $mb['name'] . '(' . $row['pt_id'] . ')';
        }

        // 거래증빙 요청이있나?
        $od_taxbill = '';
        if ($row['taxbill_yes'] == 'Y')
            $od_taxbill = "세금계산서 발급요청";
        else if ($row['taxsave_yes'] == 'Y' || $row['taxsave_yes'] == 'S')
            $od_taxbill = "현금영수증 발급요청";

        // 배송정보 (예:배송회사|배송추적URL)
        $delivery_company = "";
        $delivery_url = "";
        if ($row['delivery'] != "") {
            list($delivery_company, $delivery_url) = explode('|', $row['delivery']);
        }

        // 옵션정보
        $it_options = $this->print_complete_options($row['gs_id'], $row['od_id'], 1);

        // 판매자정보
        if ($row['seller_id'] == 'admin') {
            $od_seller_id = '본사';
        } else if (substr($row['seller_id'], 0, 3) == 'AP-') {
            $sr = $this->ci->global_lib->get_seller_cd($row['seller_id'], 'company_name');
            if (!$sr['company_name']) $sr['company_name'] = '정보없음';
            $od_seller_id = $sr['company_name'] . '(' . $row['seller_id'] . ')';
        } else {
            $mb = $this->ci->global_lib->get_member($row['seller_id'], 'name');
            if (!$mb['name']) $mb['name'] = '정보없음';
            $od_seller_id = $mb['name'] . '(' . $row['seller_id'] . ')';
        }

        // 입금일시가 시간이 비었다면 값을 비운다.
        if (is_null_time($row['receipt_time'])) {
            $row['receipt_time'] = '';
        }

        $info = array();
        $info['od_paytype']             = $od_paytype;
        $info['od_test']             = $od_test;
        $info['od_mobile']             = $od_mobile;
        $info['od_mb_id']             = $od_mb_id;
        $info['od_pt_id']             = $od_pt_id;
        $info['od_seller_id']         = $od_seller_id;
        $info['od_taxbill']             = $od_taxbill;
        $info['it_options']             = $it_options;
        $info['od_delivery_company'] = $delivery_company;
        $info['od_receipt_time']     = $row['receipt_time'];

        return $info;
    }

    // 적립금 (상품수정)
    function get_gpoint($price, $marper, $point)
    {
        if ($marper) {
            return round($price * $marper / 100);
        } else {
            return conv_number($point);
        }
    }

    // 권한체크 후 링크호출
    function get_admin($mb_id)
    {
        if (!$mb_id) return;
        if ($this->ci->gvcomplex_lib->is_admin())
            return base_url() . 'admin/index';
        if ($this->is_partner($mb_id))
            return base_url() . 'mypage/page?code=partner_info';
        if ($this->ci->gvcomplex_lib->is_seller($mb_id))
            return base_url() . 'mypage/page?code=seller_main';

        return '';
    }

    // 상품 선택옵션
    function get_item_options($gs_id, $subject)
    {
        if (!$gs_id || !$subject)
            return '';

        $gs = $this->ci->global_lib->get_goods($gs_id, 'type');
        $type = $gs['type'];

        if ($type == "timesale") {
            $amt = $this->ci->gvcomplex_lib->get_timesale_goods_price($gs_id);
        } else {
            $amt = $this->ci->gvcomplex_lib->get_sale_price($gs_id);
        }

        $sql = " select * from shop_goods_option where io_type = '0' and gs_id = ? and io_use = '1' order by io_no asc ";
        $data = array($gs_id);
        $resultObj = $this->ci->Query_model->returnQueryObj($sql, $data);
        $result = $resultObj->result_array();
        #if(!sql_num_rows($result))
        if (!$resultObj->num_rows())
            return '';

        $str = '';
        $subj = explode(',', $subject);
        $subj_count = count($subj);
        if ($subj_count > 1) {
            $options = array();

            // 옵션항목 배열에 저장
            foreach ($result as $key => $row) {
                $opt_id = explode(' ', $row['io_id']);

                for ($k = 0; $k < $subj_count; $k++) {
                    if (!isset($options[$k]) || !is_array($options[$k])) {
                        $options[$k] = array();
                    }

                    // $opt_id[$k]가 존재하는지 체크하고, 존재하면 처리
                    if (isset($opt_id[$k]) && !in_array($opt_id[$k], $options[$k])) {
                        $options[$k][] = $opt_id[$k];
                    }
                }
            }


            // 옵션선택목록 만들기
            for ($i = 0; $i < $subj_count; $i++) {
                $opt = isset($options[$i]) ? $options[$i] : array();
                $opt_count = count($opt);
                $disabled = '';
                if ($opt_count) {
                    $seq = $i + 1;
                    if ($i > 0)
                        $disabled = ' disabled="disabled"';
                    $str .= '<dl>' . PHP_EOL;
                    $str .= '<dt><label for="it_option_' . $seq . '">' . $subj[$i] . '</label></dt>';

                    $select  = '<select id="it_option_' . $seq . '" class="it_option wfull"' . $disabled . '>';
                    $select .= '<option value="">(필수) 선택하세요</option>';
                    for ($k = 0; $k < $opt_count; $k++) {
                        $opt_val = $opt[$k];
                        if ($opt_val) {
                            $select .= '<option value="' . $opt_val . '">' . $opt_val . '</option>';
                        }
                    }
                    $select .= '</select>';

                    $str .= '<dd class="li_select">' . $select . '</dd>';
                    $str .= '</dl>';
                }
            }
        } else {
            $str .= '<dl>';
            $str .= '<dt><label for="it_option_1">' . $subj[0] . '</label></dt>';

            $select  = '<select id="it_option_1" class="it_option wfull">';
            $select .= '<option value="">(필수) 선택하세요</option>';
            foreach ($result as $key => $row) {
                if ($row['io_price'] >= 0)
                    $price = '&nbsp;&nbsp;(+' . display_price($row['io_price']) . ')';
                else
                    $price = '&nbsp;&nbsp;(' . display_price($row['io_price']) . ')';

                if (!$row['io_stock_qty'])
                    $soldout = '&nbsp;&nbsp;[품절]';
                else
                    $soldout = '';

                $select .= '<option value="' . $row['io_id'] . ',' . $row['io_price'] . ',' . $row['io_stock_qty'] . ',' . $amt . '">' . $row['io_id'] . $price . $soldout . '</option>' . PHP_EOL;
            }
            $select .= '</select>';

            $str .= '<dd class="li_select">' . $select . '</dd>';
            $str .= '</dl>';
        }

        return $str;
    }

    // 상품 추가옵션
    function get_item_supply($gs_id, $subject)
    {
        if (!$gs_id || !$subject)
            return '';

        $sql = " select * from shop_goods_option where io_type = '1' and gs_id = ? and io_use = '1' order by io_no asc ";
        $data = array($gs_id);
        $resultObj = $this->ci->Query_model->returnQueryObj($sql, $data);
        $result = $resultObj->result_array();
        if (!$resultObj->num_rows())
            return '';

        $str = '';

        $subj = explode(',', $subject);
        $subj_count = count($subj);
        $options = array();

        // 옵션항목 배열에 저장
        foreach ($result as $key => $row) {
            $opt_id = explode(' ', $row['io_id']);

            if ($opt_id[0] && !array_key_exists($opt_id[0], $options))
                $options[$opt_id[0]] = array();

            if (isset($opt_id[1])) {
                if ($row['io_price'] >= 0)
                    $price = '&nbsp;&nbsp;(+' . display_price($row['io_price']) . ')';
                else
                    $price = '&nbsp;&nbsp;(' . display_price($row['io_price']) . ')';
                $io_stock_qty = $this->ci->global_lib->get_option_stock_qty($gs_id, $row['io_id'], $row['io_type']);

                if ($io_stock_qty < 1)
                    $soldout = '&nbsp;&nbsp;[품절]';
                else
                    $soldout = '';

                $options[$opt_id[0]][] = '<option value="' . $opt_id[1] . ',' . $row['io_price'] . ',' . $io_stock_qty . ',0">' . $opt_id[1] . $price . $soldout . '</option>';
            }
        }

        // 옵션항목 만들기
        for ($i = 0; $i < $subj_count; $i++) {
            $opt = $options[$subj[$i]];
            $opt_count = count($opt);
            if ($opt_count) {
                $seq = $i + 1;
                $str .= '<dl>';
                $str .= '<dt><label for="it_supply_' . $seq . '">' . $subj[$i] . '</label></dt>';

                $select = '<select id="it_supply_' . $seq . '" class="it_supply wfull">';
                $select .= '<option value="">선택안함</option>';
                for ($k = 0; $k < $opt_count; $k++) {
                    $opt_val = $opt[$k];
                    if ($opt_val) {
                        $select .= $opt_val;
                    }
                }
                $select .= '</select>';

                $str .= '<dd class="li_select">' . $select . '</dd>';
                $str .= '</dl>';
            }
        }

        return $str;
    }

    // 상품 선택옵션 (API)
    function get_item_options_api($gs_id, $subject)
    {
        if (!$gs_id || !$subject)
            return '';

        $amt = $this->ci->gvcomplex_lib->get_sale_price($gs_id);

        $sql = " select * from shop_goods_option where io_type = '0' and gs_id = ? and io_use = '1' order by io_no asc ";
        $data = array($gs_id);
        $resultObj = $this->ci->Query_model->returnQueryObj($sql, $data);
        $result = $resultObj->result_array();
        if (!$resultObj->num_rows())
            return '';

        $subj = explode(',', $subject);
        $options = array();

        // 옵션항목 배열에 저장
        foreach ($result as $key => $row) {
            $options[$key] = array(
                'io_type' => $row['io_type'],
                'io_id' => $row['io_id'],
                'io_price' => $row['io_price'],
                'io_stock_qty' => $row['io_stock_qty']
            );
        }

        return $options;
    }

    // 상품 추가옵션 (API)
    function get_item_supply_api($gs_id, $subject)
    {
        if (!$gs_id || !$subject)
            return '';

        $sql = " select * from shop_goods_option where io_type = '1' and gs_id = ? and io_use = '1' order by io_no asc ";
        $data = array($gs_id);
        $resultObj = $this->ci->Query_model->returnQueryObj($sql, $data);
        $result = $resultObj->result_array();
        #if(!sql_num_rows($result))
        if (!$resultObj->num_rows())
            return '';

        $str = '';

        $subj = explode(',', $subject);
        $subj_count = count($subj);
        $options = array();

        // 옵션항목 배열에 저장
        foreach ($result as $key => $row) {
            $options[$key] = array(
                'io_type' => $row['io_type'],
                'io_id' => $row['io_id'],
                'io_price' => $row['io_price'],
                'io_stock_qty' => $row['io_stock_qty']
            );
        }

        return $options;
    }

    // 주문완료 옵션호출
    function print_complete_options($gs_id, $od_no, $xls = '')
    {
        $sql = "SELECT io_id, ct_option, ct_qty, io_type, io_price
                        FROM shop_cart
                        WHERE od_no = ? and gs_id = ?
                        ORDER BY io_type ASC, index_no ASC ";
        $data = array($od_no, $gs_id);
        $result = $this->ci->Query_model->returnArr($sql, $data);

        $str = '';
        $comma = '';
        $i = 0;
        foreach ($result as $key => $row) {
            if ($i == 0 && !$xls)
                $str .= '<ul>';

            if (!$row['io_id']) continue;

            $price_plus = '';
            if ($row['io_price'] >= 0)
                $price_plus = '+';

            if (!$xls) {

                if ($row['io_type'])
                    $str .= "<li class='ny'>" . $row['ct_option'] . " " . display_qty($row['ct_qty']) . " (" . $price_plus . display_price($row['io_price']) . ")</li>";
                else
                    $str .= "<li class='ty'>" . $row['ct_option'] . " " . display_qty($row['ct_qty']) . " (" . $price_plus . display_price($row['io_price']) . ")</li>";
            } else {

                $str .= $comma . $row['ct_option'] . " " . display_qty($row['ct_qty']) . " (" . $price_plus . display_price($row['io_price']) . ")";

                $str = trim($str);
                $comma = '|';
            }
            $i++;
        }

        if ($i > 0 && !$xls)
            $str .= '</ul>';

        return $str;
    }

    // 장바구니 옵션호출
    function print_item_options($gs_id, $set_mb_id)
    {
        $sql = "SELECT io_id, ct_option, ct_qty, io_type, io_price
                        FROM shop_cart
                        WHERE index_no = ? AND mb_id=? AND ct_select='0' AND is_direct = 0
                        ORDER BY io_type ASC, index_no ASC";
        $data = array($gs_id, $set_mb_id);
        $result = $this->ci->Query_model->returnArr($sql, $data);
        $str = '';
        $i = 0;
        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= '<ul>';

            if (!$row['io_id']) continue;

            $price_plus = '';
            if ($row['io_price'] >= 0)
                $price_plus = '+';

            // 추가상품일때
            if ($row['io_type'])
                $str .= "<li class='ny'>" . $row['ct_option'] . " " . display_qty($row['ct_qty']) . " (" . $price_plus . display_price($row['io_price']) . ")</li>";
            else
                $str .= "<li class='ty'>" . $row['ct_option'] . " " . display_qty($row['ct_qty']) . " (" . $price_plus . display_price($row['io_price']) . ")</li>";

            $i++;
        }

        if ($i > 0)
            $str .= '</ul>';

        return $str;
    }

    // 분류별 상단배너
    function get_category_head_image($ca_id)
    {
        $cgy = array();

        $sql = "select * from shop_category where catecode = ? limit 1 ";
        $data = array(substr($ca_id, 0, 3));
        $row = $this->ci->Query_model->returnOneArr($sql, $data);
        $file = './data/category/' . ($row['headimg'] ?? '');
        if (is_file($file) && $row['headimg']) {
            $a1 = "";
            $a2 = "";
            if ($row['headimgurl']) {
                $a1 = '<a href="' . $row['headimgurl'] . '">';
                $a2 = '</a>';
            }

            $file = base_url() . 'data/category/' . $row['headimg'];
            $cgy['headimg'] = $a1 . '<img src="' . $file . '">' . $a2;
        }

        return $cgy;
    }

    // 1차분류를 select 형식으로 얻음
    function get_category_select_1($name, $selected, $event = '')
    {
        $ca_id1 = substr($selected, 0, 3);

        $str = "<select id=\"{$name}\" name=\"{$name}\"{$event}>\n";
        $str .= "<option value=\"\">=카테고리선택=</option>\n";

        $sql = "select * from shop_category where length(catecode)='3' order by caterank, catecode";
        $res = $this->ci->Query_model->returnArr($sql);
        foreach ($res as $key => $row) {
            $str .= option_selected($row['catecode'], $ca_id1, $row['catename']);
        }

        $str .= "</select>\n";

        return $str;
    }

    // 2차분류를 select 형식으로 얻음
    function get_category_select_2($name, $selected, $event = '')
    {
        $ca_id1 = substr($selected, 0, 3);
        $ca_id2 = substr($selected, 0, 6);

        $str = "<select id=\"{$name}\" name=\"{$name}\"{$event}>\n";
        $str .= "<option value=\"\">=카테고리선택=</option>\n";

        if ($ca_id1) {
            $sql = "select * from shop_category where length(catecode)='6' and upcate=? order by caterank, catecode";
            $data = array($ca_id1);
            $res = $this->ci->Query_model->returnArr($sql, $data);
            foreach ($res as $key => $row) {
                $str .= option_selected($row['catecode'], $ca_id2, $row['catename']);
            }
        }

        $str .= "</select>\n";

        return $str;
    }

    // 3차분류를 select 형식으로 얻음
    function get_category_select_3($name, $selected, $event = '')
    {
        $ca_id2 = substr($selected, 0, 6);
        $ca_id3 = substr($selected, 0, 9);

        $str = "<select id=\"{$name}\" name=\"{$name}\"{$event}>\n";
        $str .= "<option value=\"\">=카테고리선택=</option>\n";

        if ($ca_id2) {
            $sql = "select * from shop_category where length(catecode)='9' and upcate=? order by caterank, catecode";
            $data = array($ca_id2);
            $res = $this->ci->Query_model->returnArr($sql, $data);
            foreach ($res as $key => $row) {
                $str .= option_selected($row['catecode'], $ca_id3, $row['catename']);
            }
        }

        $str .= "</select>\n";

        return $str;
    }

    // 4차분류를 select 형식으로 얻음
    function get_category_select_4($name, $selected, $event = '')
    {
        $ca_id3 = substr($selected, 0, 9);
        $ca_id4 = substr($selected, 0, 12);

        $str = "<select id=\"{$name}\" name=\"{$name}\"{$event}>\n";
        $str .= "<option value=\"\">=카테고리선택=</option>\n";

        if ($ca_id3) {
            $sql = "select * from shop_category where length(catecode)='9' and upcate=? order by caterank, catecode";
            $data = array($ca_id3);
            $res = $this->ci->Query_model->returnArr($sql, $data);
            foreach ($res as $key => $row) {
                $str .= option_selected($row['catecode'], $ca_id4, $row['catename']);
            }
        }

        $str .= "</select>\n";

        return $str;
    }

    // 5차분류를 select 형식으로 얻음
    function get_category_select_5($name, $selected, $event = '')
    {
        $ca_id4 = substr($selected, 0, 12);
        $ca_id5 = substr($selected, 0, 15);

        $str = "<select id=\"{$name}\" name=\"{$name}\"{$event}>\n";
        $str .= "<option value=\"\">=카테고리선택=</option>\n";

        if ($ca_id4) {
            $sql = "select * from shop_category where length(catecode)='9' and upcate=? order by caterank, catecode";
            $data = array($ca_id4);
            $res = $this->ci->Query_model->returnArr($sql, $data);
            foreach ($res as $key => $row) {
                $str .= option_selected($row['catecode'], $ca_id5, $row['catename']);
            }
        }

        $str .= "</select>\n";

        return $str;
    }

    // 카테고리정보 불러오기
    function get_cgy_info($gs)
    {
        $str = "";
        $cnt = -1;

        if ($ca_id = $this->ci->common_lib->adm_category_navi($gs['ca_id'])) {
            $str = '<span class="fsitem">' . $ca_id . '</span>';
            $cnt++;
        }
        if ($this->ci->common_lib->adm_category_navi($gs['ca_id2'])) $cnt++;
        if ($this->ci->common_lib->adm_category_navi($gs['ca_id3'])) $cnt++;

        if ($cnt > 0) $str .= ' 외 ' . $cnt . '건';

        return $str;
    }

    // input vars 체크
    function check_input_vars()
    {
        $max_input_vars = ini_get('max_input_vars');

        if ($max_input_vars) {
            $post_vars = count($_POST, COUNT_RECURSIVE);
            $get_vars = count($_GET, COUNT_RECURSIVE);
            $cookie_vars = count($_COOKIE, COUNT_RECURSIVE);

            $input_vars = $post_vars + $get_vars + $cookie_vars;

            if ($input_vars > $max_input_vars) {
                alert('폼에서 전송된 변수의 개수가 max_input_vars 값보다 큽니다.\\n전송된 값중 일부는 유실되어 DB에 기록될 수 있습니다.\\n\\n문제를 해결하기 위해서는 서버 php.ini의 max_input_vars 값을 변경하십시오.');
            }
        }
    }

    // 배너 자체만 리턴
    function display_banner($code, $mb_id)
    {
        $str = "";
        $bg1 = "";
        $bg2 = "";
        $a1 = "";
        $a2 = "";
        $sql = $this->ci->gvcomplex_lib->sql_banner($code, $mb_id);
        $row = $this->ci->Query_model->returnOneArr($sql);

        $file = './data/banner/' . $row['bn_file'];

        if (is_file($file) && $row['bn_file']) {
            if ($row['bn_link']) {
                $a1 = "<a href=\"{$row['bn_link']}\" target=\"{$row['bn_target']}\">";
                $a2 = "</a>";
            }

            $row['bn_bg'] = preg_replace("/([^a-zA-Z0-9])/", "", $row['bn_bg']);
            if ($row['bn_bg']) {
                $bg1 = "<p style=\"background-color:#{$row['bn_bg']};\">";
                $bg2 = "</p>";
            }

            $file = base_url() . 'data/banner/' . $row['bn_file'];
            $str = "{$bg1}{$a1}<img src=\"{$file}\" width=\"{$row['bn_width']}\" height=\"{$row['bn_height']}\">{$a2}{$bg2}";
        }

        return $str;
    }

    // 배너 bg
    function display_banner_bg($code, $mb_id)
    {
        $str = "";
        $a1 = $a2 = "";
        $sql = $this->ci->gvcomplex_lib->sql_banner($code, $mb_id);
        $row = $this->ci->Query_model->returnOneArr($sql);
        $bg = "";
        $file = './data/banner/' . ($row['bn_file'] ?? '');
        if (is_file($file) && $row['bn_file']) {
            if ($row['bn_link']) {
                $a1 = "<a href=\"{$row['bn_link']}\" target=\"{$row['bn_target']}\">";
                $a2 = "</a>";
            }

            $row['bn_bg'] = preg_replace("/([^a-zA-Z0-9])/", "", $row['bn_bg']);
            if ($row['bn_bg']) $bg = "#{$row['bn_bg']} ";

            $file = base_url() . 'data/banner/' . $row['bn_file'];
            $str = "<p style=\"background:{$bg}url({$file}) no-repeat center;height:{$row['bn_height']}px;\">{$a1}{$a2}</p>";
        }

        return $str;
    }

    // 배너 (동일한 배너코드가 부여될경우 세로로 계속하여 출력)
    function display_banner_rows($code, $mb_id)
    {
        $str = "";

        $sql = $this->ci->gvcomplex_lib->sql_banner_rows($code, $mb_id);
        $result = $this->ci->Query_model->returnArr($sql);
        $i = 0;
        foreach ($result as $key => $row) {
            $a1 = $a2 = $bg = '';

            $file = './data/banner/' . $row['bn_file'];
            if (is_file($file) && $row['bn_file']) {
                if ($row['bn_link']) {
                    $a1 = "<a href=\"{$row['bn_link']}\" target=\"{$row['bn_target']}\">";
                    $a2 = "</a>";
                }

                $row['bn_bg'] = preg_replace("/([^a-zA-Z0-9])/", "", $row['bn_bg']);
                if ($row['bn_bg']) $bg = " style=\"background-color:#{$row['bn_bg']};\"";

                $file = base_url() . 'data/banner/' . $row['bn_file'];
                $str .= "<li{$bg}>{$a1}<img src=\"{$file}\" width=\"{$row['bn_width']}\" height=\"{$row['bn_height']}\">{$a2}</li>\n";
            }
            $i++;
        }

        if ($i > 0)
            $str = "<ul>\n{$str}</ul>\n";

        return $str;
    }

    // 이미지 배경고정 텍스트 입력 배너
    function mask_banner($code, $mb_id)
    {
        $str = "";
        $a1 = "";
        $a2 = "";
        $sql = $this->ci->gvcomplex_lib->sql_banner($code, $mb_id);
        $row = $this->ci->Query_model->returnOneArr($sql);

        $file = './data/banner/' . $row['bn_file'];
        if (is_file($file) && $row['bn_file']) {
            if ($row['bn_link']) {
                $a1 = "<a href=\"{$row['bn_link']}\" target=\"{$row['bn_target']}\">";
                $a2 = "</a>";
            }

            $file = base_url() . 'data/banner/' . $row['bn_file'];
            $str = "<div class=\"mask_bn\" style=\"background:url('{$file}') no-repeat fixed center;background-size:cover;\">{$a1}<p><span>{$row['bn_text']}.</span></p>{$a2}</div>";
        }

        return $str;
    }

    //구매후기 가능 또는 불가로 변경
    function set_order_review_flag($flag, $index_no, $member_id, $od_no = null)
    {
        $data = array();
        $sql_where = "";
        if (isset($od_no)) {
            $sql_where = " and od_no = '{$od_no}'";
        }
        if ($this->ci->gvcomplex_lib->is_admin() || $this->ci->gvcomplex_lib->is_seller($member_id)) {
            $sql = "update shop_order set review_flag = {$flag} where index_no = ? {$sql_where}";
            $data = array($index_no);
        } else {
            $sql = "update shop_order set review_flag = {$flag} where index_no = ? and mb_id = ? {$sql_where}";
            $data = array($index_no, $member_id);
        }
        $this->ci->Query_model->returnNull($sql, $data);
    }

    //리뷰 데이터 
    function get_review_data($index_no, $member_id)
    {
        $data = array();
        if ($this->ci->gvcomplex_lib->is_admin() || $this->ci->gvcomplex_lib->is_seller($member_id)) {
            $sql = "select * from shop_goods_review where index_no = ?";
            $data = array($index_no);
        } else {
            $sql = "select * from shop_goods_review where index_no = ? and mb_id = ?";
            $data = array($index_no, $member_id);
        }
        return $this->ci->query_model->returnOneArr($sql, $data);
    }

    // get_listtype_cate('설정값', '이미지가로', '이미지세로')
    function get_listtype_cate($list_best)
    {
        $mod = 4;
        $ul_str = '';
        for ($i = 0; $i < count($list_best); $i++) {
            $str = '';

            $list_code = explode(",", $list_best[$i]['code']); // 배열을 만들고
            $list_code = array_unique($list_code); //중복된 아이디 제거
            $list_code = array_filter($list_code); // 빈 배열 요소를 제거
            $list_code = array_values($list_code); // index 값 주기

            $succ_count = 0;
            for ($g = 0; $g < count($list_code); $g++) {
                $gcode = trim($list_code[$g]);
                $sql = "SELECT * FROM shop_goods WHERE gcode = ?";
                $data = array($gcode);
                $row = $this->ci->Query_model->returnOneArr($sql, $data);
                if (!$row['index_no']) continue;
                if ($succ_count >= $mod) break;

                $it_href = base_url() . 'shop/view?index_no=' . $row['index_no'];
                $it_name = cut_str($row['gname'], 100);
                if ($row['type'] == "timesale") {
                    $it_price = $this->get_timesale_goods_price($row['index_no']);
                    $it_amount = $this->ci->gvcomplex_lib->get_timesale_goods_price($row['index_no']);
                } else {
                    $it_price = $this->get_price($row['index_no']);
                    $it_amount = $this->ci->gvcomplex_lib->get_sale_price($row['index_no']);
                }
                $it_point = display_point($row['gpoint']);

                // (시중가 - 할인판매가) / 시중가 X 100 = 할인률%
                $it_sprice = $sale = '';
                if ($row['normal_price'] > $it_amount && !$this->is_uncase($row['index_no'])) {
                    $sett = ($row['normal_price'] - $it_amount) / $row['normal_price'] * 100;
                    $sale = '<p class="sale">' . number_format($sett, 0) . '<span class="percent">%</span></p>';
                    $it_sprice = display_price2($row['normal_price']);
                }

                $index_no = $row['index_no'];

                $sql = "SELECT a.*, b.cateuse FROM shop_goods a, shop_category b WHERE a.index_no = ? AND a.ca_id = b.catecode AND a.shop_state = '0'";
                $param = array($index_no);
                $gs = $this->ci->query_model->returnOneArr($sql, $param);
                if (!$gs['stock_mod']) {
                    $gs['stock_qty'] = 999999999;
                }

                // 품절 확인
                $scrtipt_msg  = "";
                if ($this->is_soldout($index_no)) {
                    $scrtipt_msg = 'soldout';
                }
                $status = ($scrtipt_msg == "soldout") ? "disabled" : "";

                $str .= "<li class=\"best\">\n";
                $str .=     "<a href=\"{$it_href}\">\n";
                $str .=         "<dl>\n";
                if (!empty($row['simg1'])) {
                    $imgSrc = ($row['simg_type'] == 0) ? base_url() . "data/goods/{$row['simg1']}" : $row['simg1'];
                } else {
                    $imgSrc = base_url() . "img/noimage.gif";
                }
                $str .= "<dt><img src=\"{$imgSrc}\"></dt>\n";
                $str .=         "</dl>\n";
                $str .=     "</a>\n";
                $str .=     "</dd>";
                $str .=     "<p class=\"ic_bx\">
                                        <span onclick=\"javascript:itemlistwish('{$row['index_no']}');\" id=\"{$row['index_no']}\" class=\"" . ($row['index_no'] . ' ' . $this->ci->gvcomplex_lib->zzimCheck($row['index_no'])) . "\"></span>
                                    </p>";
                $str .=     "<p class=\"pname\">{$it_name}</p>\n";
                $str .=     "<dd class=\"price_box\">\n";
                $str .=         "<p class=\"price\">{$it_sprice}</p>";
                $str .=         "<div class=\"price\">{$sale}{$it_price}</div>";
                $str .=     "</dd>\n";

                $str .= "</li>\n";

                $succ_count++;
            } // for end

            // 나머지 li
            $cnt = $succ_count % $mod;
            if ($cnt) {
                for ($k = $cnt; $k < $mod; $k++) {
                    $str .= "<li></li>\n";
                }
            }

            if (!$str) $str = "<li class=\"empty_list\">자료가 없습니다.</li>\n";

            $ul_str .= "<ul id=\"bstab_c{$i}\">\n{$str}</ul>\n";
        }
        return $ul_str;
    }

    function get_listtype_cate_m($list_best)
    {
        $mod = 6;
        $ul_str = '';
        for ($i = 0; $i < count($list_best); $i++) {
            $str = '';

            $list_code = explode(",", $list_best[$i]['code']); // 배열을 만들고
            $list_code = array_unique($list_code); //중복된 아이디 제거
            $list_code = array_filter($list_code); // 빈 배열 요소를 제거
            $list_code = array_values($list_code); // index 값 주기

            $succ_count = 0;
            for ($g = 0; $g < count($list_code); $g++) {
                $gcode = trim($list_code[$g]);
                $sql = "SELECT * FROM shop_goods WHERE gcode = ?";
                $data = array($gcode);
                $row = $this->ci->Query_model->returnOneArr($sql, $data);
                if (!$row['index_no']) continue;
                if ($succ_count >= $mod) break;

                $it_href = base_url() . 'm/shop/view?index_no=' . $row['index_no'];
                $it_name = cut_str($row['gname'], 100);
                if ($row['type'] == "timesale") {
                    $it_price = $this->get_timesale_goods_price($row['index_no']);
                    $it_amount = $this->ci->gvcomplex_lib->get_timesale_goods_price($row['index_no']);
                } else {
                    $it_price = $this->get_price($row['index_no']);
                    $it_amount = $this->ci->gvcomplex_lib->get_sale_price($row['index_no']);
                }
                $it_point = display_point($row['gpoint']);

                // (시중가 - 할인판매가) / 시중가 X 100 = 할인률%
                $it_sprice = $sale = '';
                if ($row['normal_price'] > $it_amount && !$this->is_uncase($row['index_no'])) {
                    $sett = ($row['normal_price'] - $it_amount) / $row['normal_price'] * 100;
                    $sale = '<p class="sale">' . number_format($sett, 0) . '<span class="percent">%</span></p>';
                    $it_sprice = display_price2($row['normal_price']);
                }

                $index_no = $row['index_no'];

                $sql = "SELECT a.*, b.cateuse FROM shop_goods a, shop_category b WHERE a.index_no = ? AND a.ca_id = b.catecode AND a.shop_state = '0'";
                $param = array($index_no);
                $gs = $this->ci->query_model->returnOneArr($sql, $param);
                if (!$gs['stock_mod']) {
                    $gs['stock_qty'] = 999999999;
                }

                // 품절 확인
                $scrtipt_msg  = "";
                if ($this->is_soldout($index_no)) {
                    $scrtipt_msg = 'soldout';
                }
                $status = ($scrtipt_msg == "soldout") ? "disabled" : "";

                $str .= "<li class=\"best\">\n";
                $str .=     "<a href=\"{$it_href}\">\n";
                $str .=         "<dl>\n";
                if (!empty($row['simg1'])) {
                    $imgSrc = ($row['simg_type'] == 0) ? base_url() . "data/goods/{$row['simg1']}" : $row['simg1'];
                } else {
                    $imgSrc = base_url() . "img/noimage.gif";
                }
                $str .= "<dt><img src=\"{$imgSrc}\"></dt>\n";
                $str .=         "</dl>\n";
                $str .=     "</a>\n";
                $str .=     "</dd>";
                // $str .=     "<p class=\"ic_bx\">
                //                 <span onclick=\"javascript:itemlistwish('{$row['index_no']}');\" id=\"{$row['index_no']}\" class=\"".($row['index_no'].' '.$this->ci->gvcomplex_lib->zzimCheck($row['index_no']))."\"></span>
                //             </p>";
                $str .=     "<p class=\"pname\">{$it_name}</p>\n";
                $str .=     "<dd class=\"price_box\">\n";
                $str .=         "<p class=\"price\">{$it_sprice}</p>";
                $str .=         "<div class=\"price\">{$sale}{$it_price}</div>";
                $str .=     "</dd>\n";

                $str .= "</li>\n";

                $succ_count++;
            } // for end

            // 나머지 li
            $cnt = $succ_count % $mod;
            if ($cnt) {
                for ($k = $cnt; $k < $mod; $k++) {
                    $str .= "<li></li>\n";
                }
            }

            if (!$str) $str = "<li class=\"empty_list\">자료가 없습니다.</li>\n";

            $ul_str .= "<ul id=\"bstab_c{$i}\">\n{$str}</ul>\n";
        }
        return $ul_str;
    }

    #global.lib.php에 있던것
    // 에디터 썸네일 삭제
    function delete_editor_thumbnail($contents)
    {
        if (!$contents)
            return;

        // $contents 중 img 태그 추출
        $matchs = get_editor_image($contents, false);

        if (!$matchs)
            return;

        for ($i = 0; $i < count($matchs[1]); $i++) {
            // 이미지 path 구함
            $imgurl = @parse_url($matchs[1][$i]);
            $srcfile = $_SERVER['DOCUMENT_ROOT'] . $imgurl['path'];

            $filename = preg_replace("/\.[^\.]+$/i", "", basename($srcfile));
            $filepath = dirname($srcfile);
            $files = glob($filepath . '/thumb-' . $filename . '*');
            if (is_array($files)) {
                foreach ($files as $filename)
                    unlink($filename);
            }
        }
    }

    // 에디터 이미지 삭제
    function delete_editor_image($contents)
    {
        if (!$contents)
            return;

        // $contents 중 img 태그 추출
        $imgs = get_editor_image($contents, false);

        if (!$imgs)
            return;

        // 썸네일 삭제
        $this->delete_editor_thumbnail($contents);

        for ($i = 0; $i < count($imgs[1]); $i++) {
            $p = @parse_url($imgs[1][$i]);

            if (strpos($p['path'], '/data/') != 0)
                $data_path = preg_replace('/^\/.*\/data/', '/data', $p['path']);
            else
                $data_path = $p['path'];

            $destfile = $data_path;

            if (is_file($destfile))
                @unlink($destfile);
        }
    }

    // 불법접근을 막도록 토큰을 생성하면서 토큰값을 리턴
    function get_token()
    {
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);

        return $token;
    }

    // 내용을 변환
    function conv_content($content, $html, $filter = true)
    {
        if ($html) {
            $source = array();
            $target = array();

            $source[] = "//";
            $target[] = "";

            if ($html == 2) { // 자동 줄바꿈
                $source[] = "/\n/";
                $target[] = "<br/>";
            }

            // 테이블 태그의 개수를 세어 테이블이 깨지지 않도록 한다.
            $table_begin_count = substr_count(strtolower($content), "<table");
            $table_end_count = substr_count(strtolower($content), "</table");
            for ($i = $table_end_count; $i < $table_begin_count; $i++) {
                $content .= "</table>";
            }

            $content = preg_replace($source, $target, $content);

            if ($filter)
                $content = $this->html_purifier($content);
        } else { // text 이면

            // & 처리 : &amp; &nbsp; 등의 코드를 정상 출력함
            $content = html_symbol($content);

            // 공백 처리
            //$content = preg_replace("/  /", "&nbsp; ", $content);
            $content = str_replace("  ", "&nbsp; ", $content);
            $content = str_replace("\n ", "\n&nbsp;", $content);

            $content = $this->get_text($content, 1);
            $content = url_auto_link($content);
        }

        return $content;
    }

    // POST로 넘어온 토큰과 세션에 저장된 토큰 비교
    function check_token()
    {
        set_session('ss_token', '');
        return true;
    }

    // TEXT 형식으로 변환
    function get_text($str, $html = 0, $restore = false)
    {
        $source[] = "<";
        $target[] = "&lt;";
        $source[] = ">";
        $target[] = "&gt;";
        $source[] = "\"";
        $target[] = "&#034;";
        $source[] = "\'";
        $target[] = "&#039;";

        if ($restore)
            $str = str_replace($target, $source, $str);

        // 3.31
        // TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
        if ($html == 0) {
            $str = html_symbol($str);
        }

        if ($html) {
            $source[] = "\n";
            $target[] = "<br/>";
        }

        return str_replace($source, $target, $str);
    }

    // rm -rf 옵션 : exec(), system() 함수를 사용할 수 없는 서버 또는 win32용 대체
    // www.php.net 참고 : pal at degerstrom dot com
    function rm_rf($file)
    {
        if (file_exists($file)) {
            if (is_dir($file)) {
                $handle = opendir($file);
                while ($filename = readdir($handle)) {
                    if ($filename != '.' && $filename != '..') {
                        $this->rm_rf($file . '/' . $filename);
                    }
                }
                closedir($handle);

                @chmod($file, 0707);
                @rmdir($file);
            } else {
                @chmod($file, 0644);
                @unlink($file);
            }
        }
    }

    // 동일한 host url 인지
    function check_url_host($url, $msg = '', $return_url = null)
    {
        if ($return_url == null) {
            $return_url = base_url();
        }
        if (!$msg)
            $msg = 'url에 타 도메인을 지정할 수 없습니다.';

        $p = @parse_url($url);
        $host = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);

        if (stripos($url, 'http:') !== false) {
            if (!isset($p['scheme']) || !$p['scheme'] || !isset($p['host']) || !$p['host'])
                alert('url 정보가 올바르지 않습니다.', $return_url);
        }

        if ((isset($p['scheme']) && $p['scheme']) || (isset($p['host']) && $p['host'])) {
            //if($p['host'].(isset($p['port']) ? ':'.$p['port'] : '') != $_SERVER['HTTP_HOST']) {
            if ($p['host'] != $host) {
                echo '<script>';
                echo 'alert("url에 타 도메인을 지정할 수 없습니다.");';
                echo 'document.location.href = "' . $return_url . '";';
                echo '</script>';
                echo '<noscript>';
                echo '<p>' . $msg . '</p>';
                echo '<p><a href="' . $return_url . '">돌아가기</a></p>';
                echo '</noscript>';
                exit;
            }
        }
    }

    // 비밀번호 비교
    function check_password($pass, $hash, $mb_id)
    {
        $password = $this->ci->global_lib->changePassword($pass, $mb_id);
        return ($password === $hash);
    }

    // ftp 폴더삭제
    function rrmdir($dir)
    {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file))
                $this->rrmdir($file);
            else
                @unlink($file);
        }
        rmdir($dir);
    }

    // 쿠폰 : 카테고리 추출
    function get_extract($gs_id)
    {
        $str = '';
        $arr = array();

        $gs = $this->ci->global_lib->get_goods($gs_id, 'ca_id, ca_id2, ca_id3');

        if ($gs['ca_id'])  $arr[] = $gs['ca_id'];
        if ($gs['ca_id2']) $arr[] = $gs['ca_id2'];
        if ($gs['ca_id3']) $arr[] = $gs['ca_id3'];

        if ($arr) $str = implode(',', $arr);

        return $str;
    }

    // 날짜형식 변환
    function date_conv($date, $case = 1)
    {
        $date = conv_number($date);
        if ($case == 1) { // 년-월-일 로 만들어줌
            $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $date);
        } else if ($case == 2) { // 년월일 로 만들어줌
            $date = preg_replace("/-/", "", $date);
        } else if ($case == 3) { // 년 월 일 로 만들어줌
            $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1년 \\2월 \\3일", $date);
        } else if ($case == 4) { // 년.월.일 로 만들어줌
            $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1/\\2/\\3", $date);
        }

        return $date;
    }

    // 회원 삭제
    function member_delete($mb_id)
    {
        $mb = $this->ci->global_lib->get_member($mb_id);

        $banner_dir = "./data/banner";
        $goods_dir  = "./data/goods";
        $plan_dir   = "./data/plan";

        // 회원 탈퇴시 레그구성 변경 즉! a > b > c : b가 탈퇴를 하면 c는 a아래로 붙음
        $sql = "select id from shop_member where pt_id=?";
        $data = array($mb_id);
        $res = $this->ci->Query_model->returnArr($sql, $data);
        foreach ($res as $key => $row) {
            $sql = "update shop_member set pt_id=? where id=?";
            $data = array($mb['pt_id'], $row['id']);
            $this->ci->Query_model->returnNull($sql, $data);

            $memo = $mb_id . " 회원이 탈퇴하여 추천인 {$mb['pt_id']} 으로 변경 되었습니다.";
            $sql = "insert into shop_leave_log ( new_id, old_id, mb_id, reg_time, memo )
                            values ( ?,?,?,?,?)";
            $OM_TIME_YMDHIS = $this->OM_TIME_YMDHIS;
            $data = array($mb['pt_id'], $mb_id, $row['id'], $OM_TIME_YMDHIS, $memo);
        }

        // 가맹점정보
        $data = array($mb_id);
        $sql = "delete from shop_partner where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $data); // 가맹점 수수료
        $sql2 = "delete from shop_partner_pay where mb_id=?";
        $this->ci->Query_model->returnNull($sql2, $data); // 가맹점 출금요청
        $sql3 = "delete from shop_partner_payrun where mb_id=?";
        $this->ci->Query_model->returnNull($sql3, $data); // 가맹점 기간연장
        $sql4 = "delete from shop_partner_term where mb_id=?";
        $this->ci->Query_model->returnNull($sql4, $data); // 가맹점 추천인변경로그
        $sql5 = "delete from shop_leave_log where mb_id=?";
        $this->ci->Query_model->returnNull($sql5, $data);

        // 로고
        $sql = "select * from shop_logo where mb_id=?";
        $lg = $this->ci->Query_model->returnOneArr($sql, $data);
        if ($lg['basic_logo']) @unlink($banner_dir . '/' . $lg['basic_logo']);
        if ($lg['mobile_logo']) @unlink($banner_dir . '/' . $lg['mobile_logo']);
        if ($lg['sns_logo']) @unlink($banner_dir . '/' . $lg['sns_logo']);
        if ($lg['favicon_ico']) @unlink($banner_dir . '/' . $lg['favicon_ico']);
        $sql = "delete from shop_logo where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $data);

        // 배너
        $sql = "select * from shop_banner where mb_id=?";
        $res = $this->ci->Query_model->returnArr($sql, $data);
        foreach ($res as $key => $row) {
            if ($row['bn_file']) @unlink($banner_dir . '/' . $row['bn_file']);
        }

        $sql = "delete from shop_banner where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $data);

        // 기획전
        $sql = "select * from shop_goods_plan where mb_id=?";
        $res = $this->ci->Query_model->returnArr($sql, $data);
        foreach ($res as $key => $row) {
            if ($row['pl_limg']) @unlink($plan_dir . '/' . $row['pl_limg']);
            if ($row['pl_bimg']) @unlink($plan_dir . '/' . $row['pl_bimg']);
        }

        $sql = "delete from shop_goods_plan where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $data);

        // 상품정보
        $sql = "select seller_code from shop_seller where mb_id=?";
        $sr = $this->ci->Query_model->returnOneArr($sql, $data);
        $sql = "select * from shop_goods where mb_id=? or mb_id=?";
        $data = array($sr['seller_code'], $mb_id);
        $res = $this->ci->Query_model->returnArr($sql, $data);
        foreach ($res as $key => $row) {
            $dir_list = $goods_dir . '/' . $row['gcode'];

            for ($g = 1; $g <= 6; $g++) {
                if ($row['simg' . $g]) {
                    @unlink($goods_dir . '/' . $row['simg' . $g]);
                    delete_item_thumbnail($dir_list, $row['simg' . $g]);
                }
            }

            // 에디터 이미지 삭제
            $this->delete_editor_image($row['memo']);
            $data = array($row['index_no']);
            $sql = "delete from shop_goods_relation where gs_id = ?"; // 관련상품
            $this->ci->Query_model->returnNull($sql, $data); // 관련상품
            $sql = "delete from shop_goods_relation where gs_id2 = ?";
            $this->ci->Query_model->returnNull($sql, $data);
        }

        $mb_id_data = array($mb_id); // 팝업
        $sql = "delete from shop_popup where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data); // 회원 포인트
        $sql = "delete from shop_point where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data); // 입점사 상품
        $sr_data = array($sr['seller_code']);
        $sql = "delete from shop_goods where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $sr_data); // 가맹점 상품건수
        $sql = "delete from shop_goods where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data);  // 상품진열관리
        $sql = "delete from shop_goods_type where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data); // 상품문의
        $sql = "delete from shop_goods_qa where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data); // 브랜드정보
        $sql = "delete from shop_brand where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $sr_data); // 브랜드정보
        $sql = "delete from shop_brand where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data); // 입점사 신청정보
        $sql = "delete from shop_seller where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data); // 입점사 정산내역
        $sql = "delete from shop_seller_cal where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data); // 접속자집계
        $sql = "delete from shop_visit where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data); // 접속자집계
        $sql = "delete from shop_visit_sum where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data); // 키워드
        $sql = "delete from shop_popular where pt_id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data); // 문자설정
        $sql = "delete from shop_sms where mb_id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data); // 회원정보
        $sql = "delete from shop_member where id=?";
        $this->ci->Query_model->returnNull($sql, $mb_id_data);
    }

    // pc 테마 스킨디렉토리를 SELECT 형식으로 얻음
    function get_theme_select($name, $selected = '')
    {

        $str = "<select id=\"$name\" name=\"$name\">\n";
        $str .= "<option value=\"\">선택</option>\n";
        $str .= option_selected('basic', $selected, 'basic');
        $str .= "</select>";

        return $str;
    }

    // mobile 테마 스킨디렉토리를 SELECT 형식으로 얻음
    function get_mobile_theme_select($name, $selected = '')
    {

        $str = "<select id=\"$name\" name=\"$name\">\n";
        $str .= "<option value=\"\">선택</option>\n";
        $str .= option_selected('basic', $selected, 'basic');
        $str .= "</select>";
        return $str;
    }

    // 포인트 부여
    function insert_point($mb_id, $point, $content = '', $rel_table = '', $rel_id = '', $rel_action = '')
    {
        // 포인트가 없다면 업데이트 할 필요 없음
        if ($point == 0) {
            return 0;
        }

        // 회원아이디가 없다면 업데이트 할 필요 없음
        if ($mb_id == '') {
            return 0;
        }
        $sql = "select id from shop_member where id = ?";
        $data = array($mb_id);
        $mb = $this->ci->Query_model->returnOneArr($sql, $data);
        if (!$mb['id']) {
            return 0;
        }

        // 회원포인트
        $mb_point = $this->ci->global_lib->get_point_sum($mb_id);

        // 이미 등록된 내역이라면 건너뜀
        if ($rel_table || $rel_id || $rel_action) {
            $sql = " select count(*) as cnt
                            from shop_point
                            where mb_id = ?
                                and po_rel_table = ?
                                and po_rel_id = ?
                                and po_rel_action = ? ";
            $data = array($mb_id, $rel_table, $rel_id, $rel_action);
            $row = $this->ci->Query_model->returnOneArr($sql, $data);
            if ($row['cnt'])
                return -1;
        }

        $po_mb_point = $mb_point + $point;
        $OM_TIME_YMDHIS = $this->OM_TIME_YMDHIS;
        $value = array(
            'mb_id' => $mb_id,
            'po_datetime' => $OM_TIME_YMDHIS,
            'po_content' => addslashes($content),
            'po_point' => $point,
            'po_use_point' => $mb_point,
            'po_mb_point' => $po_mb_point,
            'po_rel_table' => $rel_table,
            'po_rel_id' => $rel_id,
            'po_rel_action' => $rel_action
        );
        $this->ci->Query_model->insertObj('shop_point', $value);

        // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
        if ($point < 0) {
            $this->ci->global_lib->insert_use_point($mb_id, $point);
        }

        // 포인트 UPDATE
        $sql = "update shop_member set point = ? where id = ? ";
        $data = array($po_mb_point, $mb_id);
        $this->ci->query_model->returnNull($sql, $data);
        return 1;
    }

    // 포인트 삭제
    function delete_point($mb_id, $rel_table, $rel_id, $rel_action)
    {
        $result = false;
        if ($rel_table || $rel_id || $rel_action) {
            // 포인트 내역정보
            $sql = " select * from shop_point
                                where mb_id = ?
                                and po_rel_table = ?
                                and po_rel_id = ?
                                and po_rel_action = ?";
            $data = array($mb_id, $rel_table, $rel_id, $rel_action);
            $row = $this->ci->Query_model->returnOneArr($sql, $data);

            if ($row['po_point'] < 0) {
                $mb_id = $row['mb_id'];
                $po_point = abs($row['po_point']);

                $this->ci->global_lib->delete_use_point($mb_id, $po_point);
            } else {
                if ($row['po_use_point'] > 0) {
                    $this->ci->global_lib->insert_use_point($row['mb_id'], $row['po_use_point'], $row['po_id']);
                }
            }

            $sql = "delete from shop_point where mb_id = ? and po_rel_table = ? and po_rel_id = ? and po_rel_action = ?";
            $data = array($mb_id, $rel_table, $rel_id, $rel_action);
            $result = $this->ci->Query_model->returnNull($sql, $data);
            // po_mb_point에 반영
            $sql = "update shop_point set po_mb_point = po_mb_point - ? where mb_id = ? and po_id > ?";
            $data = array($row['po_point'], $mb_id, $row['po_id']);
            $this->ci->Query_model->returnNull($sql, $data);

            // 포인트 내역의 합을 구하고
            $sum_point = $this->ci->global_lib->get_point_sum($mb_id);

            // 포인트 UPDATE
            $sql = " update shop_member set point = ? where id = ?";
            $data = array($sum_point, $mb_id);
            $result = $this->ci->Query_model->returnNull($sql, $data);
        }

        return $result;
    }

    // 예치금 부여
    function insert_mileage($mb_id, $point, $content = '', $rel_table = '', $rel_id = '', $rel_action = '')
    {
        // 포인트가 없다면 업데이트 할 필요 없음
        if ($point == 0) {
            return 0;
        }

        // 회원아이디가 없다면 업데이트 할 필요 없음
        if ($mb_id == '') {
            return 0;
        }
        $sql = "select id from shop_member where id = ?";
        $data = array($mb_id);
        $mb = $this->ci->Query_model->returnOneArr($sql, $data);
        if (!$mb['id']) {
            return 0;
        }

        // 회원포인트
        $mb_point = $this->ci->global_lib->get_mileage_sum($mb_id);

        // 이미 등록된 내역이라면 건너뜀
        if ($rel_table || $rel_id || $rel_action) {
            $sql = " select count(*) as cnt
                            from shop_mileage
                            where mb_id = ?
                                and po_rel_table = ?
                                and po_rel_id = ?
                                and po_rel_action = ? ";
            $data = array($mb_id, $rel_table, $rel_id, $rel_action);
            $row = $this->ci->Query_model->returnOneArr($sql, $data);
            if ($row['cnt'])
                return -1;
        }

        $po_mb_point = $mb_point + $point;
        $OM_TIME_YMDHIS = $this->OM_TIME_YMDHIS;
        $value = array(
            'mb_id' => $mb_id,
            'po_datetime' => $OM_TIME_YMDHIS,
            'po_content' => addslashes($content),
            'po_point' => $point,
            'po_use_point' => $mb_point,
            'po_mb_point' => $po_mb_point,
            'po_rel_table' => $rel_table,
            'po_rel_id' => $rel_id,
            'po_rel_action' => $rel_action
        );
        $this->ci->Query_model->insertObj('shop_mileage', $value);

        // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
        if ($point < 0) {
            $this->ci->global_lib->insert_use_point($mb_id, $point);
        }

        // 포인트 UPDATE
        $sql = "update shop_member set mileage = ? where id = ? ";
        $data = array($po_mb_point, $mb_id);
        $this->ci->query_model->returnNull($sql, $data);
        return 1;
    }

    // 예치금 삭제
    function delete_mileage($mb_id, $rel_table, $rel_id, $rel_action)
    {
        $result = false;
        if ($rel_table || $rel_id || $rel_action) {
            // 포인트 내역정보
            $sql = " select * from shop_point
                                where mb_id = ?
                                and po_rel_table = ?
                                and po_rel_id = ?
                                and po_rel_action = ?";
            $data = array($mb_id, $rel_table, $rel_id, $rel_action);
            $row = $this->ci->Query_model->returnOneArr($sql, $data);

            if ($row['po_point'] < 0) {
                $mb_id = $row['mb_id'];
                $po_point = abs($row['po_point']);

                $this->ci->global_lib->delete_use_point($mb_id, $po_point);
            } else {
                if ($row['po_use_point'] > 0) {
                    $this->ci->global_lib->insert_use_point($row['mb_id'], $row['po_use_point'], $row['po_id']);
                }
            }

            $sql = "delete from shop_point where mb_id = ? and po_rel_table = ? and po_rel_id = ? and po_rel_action = ?";
            $data = array($mb_id, $rel_table, $rel_id, $rel_action);
            $result = $this->ci->Query_model->returnNull($sql, $data);
            // po_mb_point에 반영
            $sql = "update shop_point set po_mb_point = po_mb_point - ? where mb_id = ? and po_id > ?";
            $data = array($row['po_point'], $mb_id, $row['po_id']);
            $this->ci->Query_model->returnNull($sql, $data);

            // 포인트 내역의 합을 구하고
            $sum_point = $this->ci->global_lib->get_point_sum($mb_id);

            // 포인트 UPDATE
            $sql = " update shop_member set mileage = ? where id = ?";
            $data = array($sum_point, $mb_id);
            $result = $this->ci->Query_model->returnNull($sql, $data);
        }

        return $result;
    }

    // 상품 이미지를 얻는다
    function get_it_image($gs_id, $it_img, $wpx, $hpx = 0, $img_id = '')
    {
        if (!$gs_id || !$wpx)
            return '';

        $gs = $this->ci->global_lib->get_goods($gs_id, 'gcode');

        if (preg_match("/^(http[s]?:\/\/)/", $it_img) == false) {
            $file = "./data/goods/" . $it_img;
            if (is_file($file) && $it_img) {
                #$file = base_url()."data/goods/".$it_img;
                $size = @getimagesize($file);
                $img_wpx  = $size[0];
                $img_hpx  = $size[1];
                $filepath = dirname($file);
                $filename = basename($file);

                if ($img_wpx != $wpx && $img_hpx != $hpx) {
                    if ($img_wpx && !$hpx)
                        $hpx = round(($wpx * $img_hpx) / $img_wpx);

                    if ($filename) {
                        $savepath = "./data/goods/" . $gs['gcode'];
                        $size = @getimagesize($file);
                        // Animated GIF는 썸네일 생성하지 않음
                        if ($size[2] == 1) {
                            if ($this->ci->thumbnail_lib->is_animated_gif($file))
                                $savepath = "./data/goods";
                        }
                        $thumb = $this->ci->thumbnail_lib->thumbnail($filename, $filepath, $savepath, $wpx, $hpx, false, true, 'center', false, $um_value = '80/0.5/3', false);
                    }

                    $file_url = base_url() . rpc($savepath);
                } else {
                    $file_url = base_url() . rpc($filepath);
                }

                if (isset($thumb)) $img = '<img src="' . $file_url . '/' . $thumb . '" width="' . $wpx . '" height="' . $hpx . '"';
                else $img = '<img src="' . $file_url . '/' . $filename . '" width="' . $wpx . '" height="' . $hpx . '"';
            } else {
                $img = '<img src="' . base_url() . 'img/noimage.gif" width="' . $wpx . '" height="' . $hpx . '"';
            }
        } else {
            $img = '<img src="' . $it_img . '" width="' . $wpx . '" height="' . $hpx . '"';
        }

        if ($img_id) {
            $img .= ' ' . $img_id;
        }
        $img .= '>';

        return $img;
    }

    // 상품 이미지 URL을 얻는다
    function get_it_image_url($gs_id, $it_img, $wpx, $hpx = 0)
    {
        if (!$gs_id || !$wpx)
            return '';

        $gs = $this->ci->global_lib->get_goods($gs_id, 'gcode');

        if (preg_match("/^(http[s]?:\/\/)/", $it_img) == false) {
            $file = "./data/goods/" . $it_img;
            if (is_file($file) && $it_img) {
                $size = @getimagesize($file);
                $img_wpx  = $size[0];
                $img_hpx  = $size[1];
                $file = "data/goods/" . $it_img;
                $filepath = dirname($file);
                $filename = basename($file);

                if ($img_wpx != $wpx && $img_hpx != $hpx) {
                    if ($img_wpx && !$hpx)
                        $hpx = round(($wpx * $img_hpx) / $img_wpx);

                    if ($filename) {
                        $savepath = "data/goods/" . $gs['gcode'];
                        $size = @getimagesize($file);
                        // Animated GIF는 썸네일 생성하지 않음
                        if ($size[2] == 1) {
                            if ($this->ci->thumbnail_lib->is_animated_gif($file))
                                $savepath = "data/goods";
                        }
                        $thumb = $this->ci->thumbnail_lib->thumbnail($filename, $filepath, $savepath, $wpx, $hpx, false, true, 'center', false, $um_value = '80/0.5/3', false);
                    }

                    $file_url = base_url() . rpc($savepath);
                } else {
                    $file_url = base_url() . rpc($filepath);
                }

                if (isset($thumb)) {
                    $img = $file_url . '/' . $thumb;
                } else {
                    $img = $file_url . '/' . $filename;
                }
            } else {
                $img = base_url() . 'img/noimage.gif';
            }
        } else {
            $img = $it_img;
        }

        return $img;
    }

    // 회원 이미지 URL 얻기
    function get_it_mb_image_url($mb_id, $it_img, $wpx, $hpx = 0)
    {
        if (!$wpx)
            return '';

        if (preg_match("/^(http[s]?:\/\/)/", $it_img) == false) {
            $file = "./data/member/" . $it_img;
            if (is_file($file) && $it_img) {
                $size = @getimagesize($file);
                $img_wpx  = $size[0];
                $img_hpx  = $size[1];
                $file = "data/member/" . $it_img;
                $filepath = dirname($file);
                $filename = basename($file);

                if ($img_wpx != $wpx && $img_hpx != $hpx) {
                    if ($img_wpx && !$hpx)
                        $hpx = round(($wpx * $img_hpx) / $img_wpx);

                    if ($filename) {
                        $savepath = "data/member/thumb/" . $mb_id;
                        $size = @getimagesize($file);
                        // Animated GIF는 썸네일 생성하지 않음
                        if ($size[2] == 1) {
                            if ($this->ci->thumbnail_lib->is_animated_gif($file))
                                $savepath = "data/member/thum";
                        }
                        $thumb = $this->ci->thumbnail_lib->thumbnail($filename, $filepath, $savepath, $wpx, $hpx, false, true, 'center', false, $um_value = '80/0.5/3', false);
                    }

                    $file_url = base_url() . rpc($savepath);
                } else {
                    $file_url = base_url() . rpc($filepath);
                }

                if ($thumb) $img = $file_url . '/' . $thumb;
                else $img = $file_url . '/' . $filename;
            } else {
                $img = base_url() . 'img/noimage.gif';
            }
        } else {
            $img = $it_img;
        }

        return $img;
    }

    // 상품 이미지 URL 얻기
    function get_it_review_image_url($it_img, $wpx, $hpx = 0)
    {
        if (!$wpx)
            return '';

        if (preg_match("/^(http[s]?:\/\/)/", $it_img) == false) {
            $file = "./data/review/" . $it_img;
            if (is_file($file) && $it_img) {
                $size = @getimagesize($file);
                $img_wpx  = $size[0];
                $img_hpx  = $size[1];
                $file = "data/review/" . $it_img;
                $filepath = dirname($file);
                $filename = basename($file);

                if ($img_wpx != $wpx && $img_hpx != $hpx) {
                    if ($img_wpx && !$hpx)
                        $hpx = round(($wpx * $img_hpx) / $img_wpx);

                    if ($filename) {
                        $savepath = "data/review/thumb/" . $it_img;
                        $size = @getimagesize($file);
                        // Animated GIF는 썸네일 생성하지 않음
                        if ($size[2] == 1) {
                            if ($this->ci->thumbnail_lib->is_animated_gif($file))
                                $savepath = "data/review/thumb";
                        }
                        $thumb = $this->ci->thumbnail_lib->thumbnail($filename, $filepath, $savepath, $wpx, $hpx, false, true, 'center', false, $um_value = '80/0.5/3', false);
                    }

                    $file_url = base_url() . rpc($savepath);
                } else {
                    $file_url = base_url() . rpc($filepath);
                }

                if ($thumb) $img = $file_url . '/' . $thumb;
                else $img = $file_url . '/' . $filename;
            } else {
                $img = base_url() . 'img/noimage.gif';
            }
        } else {
            $img = $it_img;
        }

        return $img;
    }

    // 상품 이미지 URL 얻기
    function get_it_plan_image_url($it_img, $wpx, $hpx = 0)
    {
        if (!$wpx)
            return '';

        if (preg_match("/^(http[s]?:\/\/)/", $it_img) == false) {
            $file = "./data/plan/" . $it_img;
            if (is_file($file) && $it_img) {
                $size = @getimagesize($file);
                $img_wpx  = $size[0];
                $img_hpx  = $size[1];
                $file = "data/review/" . $it_img;
                $filepath = dirname($file);
                $filename = basename($file);

                if ($img_wpx != $wpx && $img_hpx != $hpx) {
                    if ($img_wpx && !$hpx)
                        $hpx = round(($wpx * $img_hpx) / $img_wpx);

                    if ($filename) {
                        $savepath = "data/review/thumb/" . $it_img;
                        $size = @getimagesize($file);
                        // Animated GIF는 썸네일 생성하지 않음
                        if ($size[2] == 1) {
                            if ($this->ci->thumbnail_lib->is_animated_gif($file))
                                $savepath = "data/review/thumb";
                        }
                        $thumb = $this->ci->thumbnail_lib->thumbnail($filename, $filepath, $savepath, $wpx, $hpx, false, true, 'center', false, $um_value = '80/0.5/3', false);
                    }

                    $file_url = base_url() . rpc($savepath);
                } else {
                    $file_url = base_url() . rpc($filepath);
                }

                if ($thumb) $img = $file_url . '/' . $thumb;
                else $img = $file_url . '/' . $filename;
            } else {
                $img = base_url() . 'img/noimage.gif';
            }
        } else {
            $img = $it_img;
        }

        return $img;
    }

    // 주문상품 이미지를 얻는다
    function get_od_image($od_id, $it_img, $wpx, $hpx = 0)
    {
        if (!$od_id || !$wpx)
            return '';

        if (preg_match("/^(http[s]?:\/\/)/", $it_img) == false) {
            $file = "./data/order/" . substr($od_id, 0, 4) . "/" . $od_id . "/" . $it_img;
            if (is_file($file) && $it_img) {
                $size = @getimagesize($file);
                $img_wpx  = $size[0];
                $img_hpx  = $size[1];
                $filepath = dirname($file);
                $filename = basename($file);

                if ($img_wpx != $wpx && $img_hpx != $hpx) {
                    if ($img_wpx && !$hpx)
                        $hpx = round(($wpx * $img_hpx) / $img_wpx);

                    if ($filename) {
                        $thumb = $this->ci->thumbnail_lib->thumbnail($filename, $filepath, $filepath, $wpx, $hpx, false, true, 'center', false, $um_value = '80/0.5/3', false);
                    }
                }

                $file_url = base_url() . rpc($filepath);
                if ($thumb) $img = '<img src="' . $file_url . '/' . $thumb . '" width="' . $wpx . '" height="' . $hpx . '"';
                else $img = '<img src="' . $file_url . '/' . $filename . '" width="' . $wpx . '" height="' . $hpx . '"';
            } else {
                $img = '<img src="' . base_url() . 'img/noimage.gif" width="' . $wpx . '" height="' . $hpx . '"';
            }
        } else {
            $img = '<img src="' . $it_img . '" width="' . $wpx . '" height="' . $hpx . '"';
        }

        $img .= '>';

        return $img;
    }

    // 회원 레벨
    function get_search_level($field, $value, $start_id = 2, $end_id = 9)
    {
        $str = radio_checked($field, $value,  '', '전체');

        $sql = " select * from shop_member_grade where gb_no between ? and ? and gb_name <> '' order by gb_no desc ";
        $data = array($start_id, $end_id);
        $result = $this->ci->Query_model->returnArr($sql, $data);
        foreach ($result as $key => $row) {
            $str .= radio_checked($field, $value, $row['gb_no'], $row['gb_name']);
        }
        return $str;
    }

    // 불법접근을 막도록 토큰을 생성하면서 토큰값을 리턴
    function get_admin_token()
    {
        $token = md5(uniqid(rand(), true));
        set_session('ss_admin_token', $token);

        return $token;
    }

    // POST로 넘어온 토큰과 세션에 저장된 토큰 비교
    function check_admin_token()
    {
        $token = get_session('ss_admin_token');
        set_session('ss_admin_token', '');

        if (!$token || !$_REQUEST['token'] || $token != $_REQUEST['token'])
            alert('올바른 방법으로 이용해 주십시오.', base_url());

        return true;
    }

    // POST로 넘어온 토큰과 세션에 저장된 토큰 비교
    function ajax_admin_token()
    {
        $token = get_session('ss_admin_token');
        set_session('ss_admin_token', '');

        if (!$token || !$_REQUEST['token'] || $token != $_REQUEST['token'])
            die("{\"error\":\"올바른 방법으로 이용해 주십시오.\"}");

        return true;
    }

    // 관리자 페이지 referer 체크
    function admin_referer_check($return = false)
    {
        $referer = trim($_SERVER['HTTP_REFERER']);
        if (!$referer) {
            $msg = '정보가 올바르지 않습니다.';

            if ($return)
                return $msg;
            else
                alert($msg, base_url());
        }

        $p = @parse_url($referer);
        $host = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);

        if ($host != $p['host']) {
            $msg = '올바른 방법으로 이용해 주십시오.';

            if ($return)
                return $msg;
            else
                alert($msg, base_url());
        }
    }

    // 주소출력
    function print_address($addr1, $addr2, $addr3, $addr4)
    {
        $address = $this->get_text(trim($addr1));
        $addr2   = $this->get_text(trim($addr2));
        $addr3   = $this->get_text(trim($addr3));

        if ($addr4 == 'N') {
            if ($addr2)
                $address .= ' ' . $addr2;
        } else {
            if ($addr2)
                $address .= ', ' . $addr2;
        }

        if ($addr3)
            $address .= ' ' . $addr3;

        return $address;
    }

    // 로그인 후 이동할 URL
    function login_url($url = '')
    {
        if (!$url) $url = base_url();
        return urlencode($this->ci->security->xss_clean(urldecode($url)));
    }

    // 품절 확인
    // bid의 경우 안전재고까지 고려함
    function is_soldout($gs_id)
    {
        // 상품 정보 조회
        $sql = "SELECT isopen, stock_qty, stock_mod, type FROM shop_goods WHERE index_no = ?";
        $data = array($gs_id);
        $gs = $this->ci->Query_model->returnOneArr($sql, $data);

        // 기본 품절 조건
        if (($gs['type'] == "normal" && $gs['stock_mod'] && $gs['stock_qty'] == 0) || $gs['isopen'] == 2) {
            return true;
        }

        // 옵션 관련 변수 초기화
        $option_count = 0;
        $soldout_count = 0;

        // 옵션 조회
        $sql = "SELECT io_id, io_type, io_stock_qty FROM shop_goods_option WHERE gs_id = ? AND io_type = '0' AND io_use = '1'";
        $options = $this->ci->Query_model->returnArr($sql, $data);

        foreach ($options as $option) {
            $sum_qty = $this->get_cart_qty($gs_id, $option['io_id'], $option['io_type']);
            $stock_qty = $this->ci->global_lib->get_option_stock_qty($gs_id, $option['io_id'], $option['io_type']);
            $option_count++;

            if ($stock_qty <= 0) {
                $soldout_count++;
            }
        }

        // 모든 선택 옵션이 품절인지 확인
        if ($option_count > 0 && $option_count === $soldout_count) {
            return true;
        }

        // 옵션 없는 경우
        $sum_qty = $this->get_cart_qty($gs_id);
        $stock_qty = $this->ci->global_lib->get_it_stock_qty($gs_id);

        return $stock_qty <= 0;
    }

    // bid 상품 안전수량 결제인지 확인
    function is_soldout_timesale($gs_id)
    {
        // 상품 정보 조회
        $sql = "SELECT type, safety_stock_qty FROM shop_goods WHERE index_no = ?";
        $data = array($gs_id);
        $gs = $this->ci->Query_model->returnOneArr($sql, $data);

        // 타임 세일 상품 체크
        if ($gs['type'] == "timesale") {
            $timesale_stock_qty = $this->ci->global_lib->get_it_timesale_stock_qty($gs_id);
            $timesale_safety_stock_qty = $this->ci->global_lib->get_it_timesale_safety_stock_qty($gs_id);

            return $timesale_stock_qty <= 0 && $timesale_safety_stock_qty > 0;
        }

        return false;
    }

    // bid 상품 재고 다 소진하였는지 확인
    // 안전재고 고려하지 않음
    function is_soldout_ignore_safety($gs_id)
    {
        // 상품 정보 조회
        $sql = "SELECT type FROM shop_goods WHERE index_no = ?";
        $data = array($gs_id);
        $gs = $this->ci->Query_model->returnOneArr($sql, $data);

        // 타임 세일 상품일 경우 품절 여부 체크
        if ($gs['type'] == "timesale") {
            return $this->ci->global_lib->get_it_timesale_stock_qty($gs_id) <= 0;
        }

        return false;
    }

    // 판매종료 상품 확인
    function is_sale_end($gs_id)
    {
        // 상품 정보 조회
        $sql = "SELECT isopen FROM shop_goods WHERE index_no = ?";
        $data = array($gs_id);
        $gs = $this->ci->Query_model->returnOneArr($sql, $data);

        // 판매 종료 여부 체크
        return $gs['isopen'] == "5";
    }

    // 장바구니에서 주문 대기 수량 조회 함수
    private function get_cart_qty($gs_id, $io_id = null, $io_type = null)
    {
        $this->ci->db->select_sum('ct_qty', 'qty');
        $this->ci->db->from('shop_cart');
        $this->ci->db->where('gs_id', $gs_id);
        $this->ci->db->where('ct_select', '1');

        if ($io_id) {
            $this->ci->db->where('io_id', $io_id);
        }

        if ($io_type) {
            $this->ci->db->where('io_type', $io_type);
        }

        $query = $this->ci->db->get();
        return $query->row()->qty ?: 0; // 결과가 없으면 0 반환
    }

    // 주문서 삭제
    function order_delete($od_no, $od_id)
    {
        $sql = " select * from shop_order where od_no = ? and od_id = ?";
        $data = array($od_no, $od_id);
        $od = $this->ci->Query_model->returnOneArr($sql, $data);
        if (!$od) return;

        // 입금대기 상태인가?
        if ($od['dan'] == 1) {
            // 상품정보
            $gs = unserialize($od['od_goods']);

            // 신규가입 쿠폰일경우 다시 사용할 수 있도록 돌려준다.
            $this->ci->global_lib->subtract_coupon_log($od_no);

            // 상품옵션별재고 또는 상품재고에 더하기
            $this->ci->global_lib->add_io_stock($gs['index_no'], $od_id);

            // 주문취소 회원의 포인트를 되돌려 줌
            if ($od['mb_id'] && $od['use_point']) {
                $this->ci->global_lib->insert_point($od['mb_id'], $od['use_point'], "주문번호 {$od_id} ({$od_no}) 주문취소");
            }
        }

        // 삭제
        $sql = "delete from shop_cart where od_no = ? and od_id = ?";
        $sql2 = "delete from shop_order where od_no = ? and od_id =? ";
        $this->ci->Query_model->returnNull($sql, $data);
        $this->ci->Query_model->returnNull($sql2, $data);
    }

    // '주문취소' 상태로 변경
    function change_order_status_6($od_no, $amount = null)
    {
        $OM_TIME_YMDHIS = $this->OM_TIME_YMDHIS;
        $od = $this->ci->global_lib->get_order($od_no);

        // bid 상품과 금액 구분
        if (!isset($amount)) {
            $amount = "(goods_price + baesong_price)";
        }

        // 상품정보
        $gs = unserialize($od['od_goods']);

        $sql = "UPDATE shop_order
                        SET dan = '6', cancel_price = {$amount}, cancel_date = ?
                        WHERE od_no = ?";
        $data = array($OM_TIME_YMDHIS, $od_no);
        $this->ci->Query_model->returnNull($sql, $data);

        // 신규가입 쿠폰일경우 다시 사용할 수 있도록 돌려준다.
        $this->ci->global_lib->subtract_coupon_log($od_no);

        // 상품옵션별재고 또는 상품재고에 더하기
        $gs['index_no'] = isset($gs['index_no']) ? $gs['index_no'] : "";
        $this->ci->global_lib->add_io_stock($gs['index_no'], $od['od_id']);

        // 상품 판매수량 반영
        $this->ci->global_lib->add_sum_qty($od['gs_id']);

        // 일반 상품일 경우에만 주문금액에서 바로 포인트, 예치금 반환
        if ($od['od_type'] == "normal") {
            // 사용한 회원의 포인트를 반환
            if ($od['mb_id'] && $od['use_point']) {
                $this->insert_point($od['mb_id'], $od['use_point'], "주문번호 {$od['od_id']} ({$od_no}) 주문취소");
            }

            // 사용한 회원의 예치금 반환
            if ($od['mb_id'] && $od['use_mileage']) {
                $this->insert_mileage($od['mb_id'], $od['use_mileage'], "주문번호 {$od['od_id']} ({$od_no}) 주문취소");
            }
        }

        // bid 관련 초기화
        if ($gs['type'] == "timesale") {
            $this->change_order_status_timesale($od['od_id']);
        }
    }

    // '배송후 반품' 상태로 변경
    function change_order_status_7($od_no)
    {
        $OM_TIME_YMDHIS = $this->OM_TIME_YMDHIS;
        $od = $this->ci->global_lib->get_order($od_no);

        // 상품정보
        $gs = unserialize($od['od_goods']);

        $sql = " update shop_order set dan = '7', return_date = ? where od_no = ?";
        $data = array($OM_TIME_YMDHIS, $od_no);
        $this->ci->Query_model->returnNull($sql, $data);

        // 판매수수료 환수처리
        $sql = " select * from shop_partner_pay where pp_rel_id = ? and pp_rel_action = ?";
        $data = array($od_no, $od['od_id']);
        $res = $this->ci->Query_model->returnArr($sql, $data);
        foreach ($res as $key => $row) {
            $this->delete_pay($row['mb_id'], "sale", $row['pp_rel_id'], $row['pp_rel_action']);
        }

        // 상품 판매수량 반영
        $this->ci->global_lib->add_sum_qty($od['gs_id']);

        // 신규가입 쿠폰일경우 다시 사용할 수 있도록 돌려준다.
        $this->ci->global_lib->subtract_coupon_log($od_no);

        // 상품옵션별재고 또는 상품재고에 더하기
        $this->ci->global_lib->add_io_stock($gs['index_no'], $od['od_id']);

        // 적립된 회원의 포인트를 뺀다
        if ($od['mb_id'] && $od['sum_point']) {
            $this->delete_point($od['mb_id'], "@delivery", $od['mb_id'], "{$od['od_id']},{$od_no}");
        }

        // 사용한 회원의 포인트를 되돌려 줌
        if ($od['mb_id'] && $od['use_point']) {
            $this->insert_point($od['mb_id'], $od['use_point'], "주문번호 {$od['od_id']} ({$od_no}) 반품");
        }
    }

    // '배송전 환불' 상태로 변경
    function change_order_status_9($od_no)
    {
        $OM_TIME_YMDHIS = $this->OM_TIME_YMDHIS;
        $od = $this->ci->global_lib->get_order($od_no);

        // 상품정보
        $gs = unserialize($od['od_goods']);

        $sql = " update shop_order set dan = '9', refund_date = ? where od_no = ?";
        $data = array($OM_TIME_YMDHIS, $od_no);
        $this->ci->Query_model->returnNull($sql, $data);

        // 신규가입 쿠폰일경우 다시 사용할 수 있도록 돌려준다.
        $this->ci->global_lib->subtract_coupon_log($od_no);

        // 상품옵션별재고 또는 상품재고에 더하기
        $gs['index_no'] = isset($gs['index_no']) ? $gs['index_no'] : "";
        $this->ci->global_lib->add_io_stock($gs['index_no'], $od['od_id']);

        // 상품 판매수량 반영
        $this->ci->global_lib->add_sum_qty($od['gs_id']);

        // 사용한 회원의 포인트를 반환
        if ($od['mb_id'] && $od['use_point']) {
            $this->insert_point($od['mb_id'], $od['use_point'], "주문번호 {$od['od_id']} ({$od_no}) 환불");
        }

        // 사용한 회원의 예치금 반환
        if ($od['mb_id'] && $od['use_mileage']) {
            $this->insert_mileage($od['mb_id'], $od['use_mileage'], "주문번호 {$od['od_id']} ({$od_no}) 주문취소");
        }

        // bid 관련 초기화
        if ($gs['type'] == "timesale") {
            $this->change_order_status_timesale($od['od_id']);
        }
    }

    // '구매확정' 상태로 변경
    function change_order_status_end($od_no)
    {
        $OM_TIME_YMDHIS = $this->OM_TIME_YMDHIS;
        $od = $this->ci->global_lib->get_order($od_no);

        $sql = "UPDATE shop_order SET user_ok = '1', user_date = ? WHERE od_no = ?";
        $data = array($OM_TIME_YMDHIS, $od_no);
        $this->ci->Query_model->returnNull($sql, $data);

        // 포인트 적립
        $this->insert_point($od['mb_id'], $od['sum_point'], "주문번호 {$od['od_id']} ({$od['od_no']}) 구매확정 적립", "@delivery", $od['mb_id'], "{$od['od_id']},{$od['od_no']}");

        // 리뷰 공유 페이백 적립
        $this->insert_review_referral($od);
    }

    // 주문취소 bid 상품 테이블 내용 변경
    function change_order_status_timesale($od_no)
    {
        $OM_TIME_YMDHIS = $this->OM_TIME_YMDHIS;

        // // bid 상품 테이블 데이터 초기화
        // $sql = "UPDATE shop_goods_timesale
        //         SET mb_id = '', od_id = '', status_update_date = ?
        //         WHERE od_id = ?";
        // $data=array($OM_TIME_YMDHIS,$od_no);
        // $this->ci->Query_model->returnNull($sql,$data);

        // bid 주문 테이블 상태 변경
        $sql = "UPDATE shop_order_timesale
                        SET flag = 'cancel'
                        WHERE od_id = ?";
        $data = array($od_no);
        $this->ci->Query_model->returnNull($sql, $data);
    }

    // 고객이 주문/배송조회를 위해 보관해 둔다.
    function save_goods_data($gs_id, $odrno, $od_id)
    {
        if (!$gs_id || !$odrno || !$od_id)
            return;

        // 특정필드는 제외한다.
        $columns = $this->ci->global_lib->get_columns("shop_goods");
        $columns = array_diff($columns, array("`info_value`", "`info_gubun`", "`memo`", "`admin_memo`"));

        $sql = " select " . implode(",", $columns) . " from shop_goods where index_no = ?";
        $data = array($gs_id);
        $cp = $this->ci->Query_model->returnOneArr($sql, $data);

        $data = serialize($cp);

        // 상품정보를 주문서에 업데이트한다.
        $sql = " update shop_order set od_goods = ? where index_no = ?";
        $data = array($data, $odrno);
        $this->ci->Query_model->returnNull($sql, $data);

        $ymd_dir = './data/order/' . date('ym', time());
        $upl_dir = $ymd_dir . '/' . $od_id; // 저장될 위치

        // 년도별로 따로 저장
        if (!is_dir($ymd_dir)) {
            @mkdir($ymd_dir, 0707);
            @chmod($ymd_dir, 0707);
        }

        // 주문번호별로 따로 저장
        if (!is_dir($upl_dir)) {
            @mkdir($upl_dir, 0707);
            @chmod($upl_dir, 0707);
        }

        if (preg_match("/^(http[s]?:\/\/)/", $cp['simg1']) == false) {
            $file = './data/goods/' . $cp['simg1'];
            if (is_file($file) && $cp['simg1']) {
                $file_url = $upl_dir . '/' . $cp['simg1'];
                @copy($file, $file_url);
                @chmod($file_url, 0644);
            }
        }
    }

    // 이미지를 추출하여 사이즈를 가로 재조정
    function get_image_resize($html)
    {
        $imgs = get_editor_image($html);

        $img = preg_replace("/<([a-z][a-z0-9]*)(?:[^>]*(\ssrc=['\"][^'\"]*['\"]))?[^>]*?(\/?)>/i", '<$1$2$3 class="img_fix">', $imgs[0]);

        $html = str_replace($imgs[0], $img, $html);

        return $html;
    }

    // 배너 URL
    function display_banner_url($code, $mb_id)
    {
        $str = "";

        $sql = $this->ci->gvcomplex_lib->sql_banner($code, $mb_id);
        $row = $this->ci->Query_model->returnOneArr($sql);

        $file = './data/banner/' . $row['bn_file'];
        if (is_file($file) && $row['bn_file']) {
            $file = base_url() . 'data/banner/' . $row['bn_file'];
            $str = rpc($file);
        }

        return $str;
    }

    // 개별배너 설정값이없으면 본사 설정을 그대로 복사.
    function check_banner_copy($mb_id, $device)
    {
        if (!$mb_id || !$device) return;

        $banner_dir = "./data/banner";

        // 배너
        $sql = "select * from shop_banner where mb_id = ? and bn_device = ?";
        $data = array($mb_id, $device);
        $result = $this->ci->Query_model->returnArr($sql, $data);
        foreach ($result as $key => $row) {
            @unlink($banner_dir . '/' . $row['bn_file']);
        }

        // 삭제
        $sql = "delete from shop_banner where mb_id = ? and bn_device = ?";
        $data = array($mb_id, $device);
        $this->ci->Query_model->returnNull($sql, $data);
        $sql = " select * from shop_banner where mb_id = 'admin' and bn_device = ? order by bn_id asc ";
        $data = array($device);
        $result = $this->ci->Query_model->returnArr($sql, $data);
        foreach ($result as $key => $cp) {
            $sql_common = "";
            $fields = $this->ci->gvcomplex_lib->sql_field_names("shop_banner");
            foreach ($fields as $fld) {
                if (in_array($fld, array('bn_id', 'mb_id', 'bn_file'))) continue;

                $sql_common .= " , $fld = '" . addslashes($cp[$fld]) . "' ";
            }

            $sql = " insert into shop_banner set mb_id = ? $sql_common ";
            $data = array($mb_id);
            $new_bn_id = $this->ci->Query_model->returnInsertId($sql, $data);

            $file = $banner_dir . '/' . $cp['bn_file'];
            if (is_file($file) && $cp['bn_file']) {
                $dstfile = $banner_dir . '/' . $new_bn_id . '_' . $cp['bn_file'];
                $new_bn_file = basename($dstfile);

                @copy($file, $dstfile);
                @chmod($dstfile, 0644);

                $sql = "update shop_banner set bn_file = ? where bn_id = ?";
                $data = array($new_bn_file, $new_bn_id);
                $this->ci->Query_model->returnNull($sql, $data);
            }
        }
    }

    #mobile.lib.php에 있던 것
    // 인기검색어 추출1
    function mobile_popular($title, $rows, $pt_id)
    {
        $str = "<h2>{$title}</h2>\n";
        $str .= "<ul id=\"ticker\">\n";

        $sql = " select pp_word, count(*) as cnt from shop_popular where pt_id = ? and TRIM(pp_word) <> '' group by pp_word order by cnt desc limit $rows";
        $data = array($pt_id);
        $result = $this->ci->Query_model->returnArr($sql, $data);
        $i = 0;
        foreach ($result as $key => $row) {
            $word = $this->get_text($row['pp_word']);
            $href = base_url() . 'm/shop/search?ss_tx=' . $word;
            $rank = $i + 1;
            $str .= "<li><a href=\"{$href}\"><span class=\"rkw_num\">{$rank}</span> {$word}</a></li>\n";
            $i++;
        }
        $str .= "</ul>\n";

        return $str;
    }

    // 인기검색어 추출2
    function mobile_popular_rank($rows, $pt_id)
    {
        $str = "<div class=\"m_rkw\">\n";
        $str .= "<ul>\n";

        $sql = " select pp_word, count(*) as cnt from shop_popular where pt_id = ? and TRIM(pp_word) <> '' group by pp_word order by cnt desc limit $rows ";
        $data = array($pt_id);
        $result = $this->ci->Query_model->returnArr($sql, $data);
        $i = 0;
        foreach ($result as $key => $row) {
            $word = $this->get_text($row['pp_word']);
            $href = base_url() . 'm/shop/search?ss_tx=' . $word;
            $rank = $i + 1;

            $str .= "<li><a href=\"{$href}\"><span class=\"rkw_num\">{$rank}</span> {$word}</a></li>\n";
            $i++;
        }
        $str .= "</ul>\n";
        $str .= "</div>\n";

        return $str;
    }

    // 상품 선택옵션
    function mobile_item_options($gs_id, $subject, $event = '')
    {
        if (!$gs_id || !$subject)
            return '';

        $gs = $this->ci->global_lib->get_goods($gs_id, 'type');
        $type = $gs['type'];

        if ($type == "timesale") {
            $amt = $this->ci->gvcomplex_lib->get_timesale_goods_price($gs_id);
        } else {
            $amt = $this->ci->gvcomplex_lib->get_sale_price($gs_id);
        }

        $sql = " select * from shop_goods_option where io_type = '0' and gs_id = ? and io_use = '1' order by io_no asc ";
        $data = array($gs_id);
        $resultObj = $this->ci->Query_model->returnQueryObj($sql, $data);
        $result = $resultObj->result_array();
        if (!$resultObj->num_rows())
            return '';

        $str = '';
        $subj = explode(',', $subject);
        $subj_count = count($subj);

        if ($subj_count > 1) {
            $options = array();

            // 옵션항목 배열에 저장
            foreach ($result as $key => $row) {
                $opt_id = explode(' ', $row['io_id']);

                for ($k = 0; $k < $subj_count; $k++) {
                    if (!isset($options[$k]) || !is_array($options[$k]))
                        $options[$k] = array();

                    if ($opt_id[$k] && !in_array($opt_id[$k], $options[$k]))
                        $options[$k][] = $opt_id[$k];
                }
            }

            // 옵션선택목록 만들기
            for ($i = 0; $i < $subj_count; $i++) {
                $opt = isset($options[$i]) ? $options[$i] : array();
                $opt_count = count($opt);
                $disabled = '';
                if ($opt_count) {
                    $seq = $i + 1;
                    if ($i > 0)
                        $disabled = ' disabled="disabled"';
                    $str .= '<div class=sp_obox>';
                    $str .= '<ul>';
                    $str .= '<li class=tlst><label for="it_option_' . $seq . '">' . $subj[$i] . '</label></li>';

                    $select  = '<select id="it_option_' . $seq . '" class="it_option"' . $disabled . ' ' . $event . '>';
                    $select .= '<option value="">(필수) 선택하세요</option>';
                    for ($k = 0; $k < $opt_count; $k++) {
                        $opt_val = $opt[$k];
                        if ($opt_val) {
                            $select .= '<option value="' . $opt_val . '">' . $opt_val . '</option>';
                        }
                    }
                    $select .= '</select>';

                    $str .= '<li class=trst>' . $select . '</li>';
                    $str .= '</ul>';
                    $str .= '</div>';
                }
            }
        } else {
            $str .= '<div class=sp_obox>';
            $str .= '<ul>';
            $str .= '<li class=tlst><label for="it_option_1">' . $subj[0] . '</label></li>';

            $select  = '<select id="it_option_1" class="it_option" ' . $event . '>';
            $select .= '<option value="">(필수) 선택하세요</option>';
            foreach ($result as $key => $row) {
                if ($row['io_price'] >= 0)
                    $price = '&nbsp;&nbsp;(+' . display_price($row['io_price']) . ')';
                else
                    $price = '&nbsp;&nbsp;(' . display_price($row['io_price']) . ')';

                if (!$row['io_stock_qty'])
                    $soldout = '&nbsp;&nbsp;[품절]';
                else
                    $soldout = '';

                $select .= '<option value="' . $row['io_id'] . ',' . $row['io_price'] . ',' . $row['io_stock_qty'] . ',' . $amt . '">' . $row['io_id'] . $price . $soldout . '</option>';
            }

            $select .= '</select>';

            $str .= '<li class=trst>' . $select . '</li>';
            $str .= '</ul>';
            $str .= '</div>';
        }

        return $str;
    }

    // 상품 추가옵션
    function mobile_item_supply($gs_id, $subject, $event = '')
    {
        if (!$gs_id || !$subject)
            return '';

        $sql = " select * from shop_goods_option where io_type = '1' and gs_id = ? and io_use = '1' order by io_no asc ";
        $data = array($gs_id);
        $resultObj = $this->ci->Query_model->returnQueryObj($sql, $data);
        $result = $resultObj->result_array();
        if (!$result->num_rows())
            return '';

        $str = '';

        $subj = explode(',', $subject);
        $subj_count = count($subj);
        $options = array();

        // 옵션항목 배열에 저장
        foreach ($result as $key => $row) {
            $opt_id = explode(' ', $row['io_id']);

            if ($opt_id[0] && !array_key_exists($opt_id[0], $options))
                $options[$opt_id[0]] = array();

            if ($opt_id[1]) {
                if ($row['io_price'] >= 0)
                    $price = '&nbsp;&nbsp;(+' . display_price($row['io_price']) . ')';
                else
                    $price = '&nbsp;&nbsp;(' . display_price($row['io_price']) . ')';
                $io_stock_qty = $this->ci->global_lib->get_option_stock_qty($gs_id, $row['io_id'], $row['io_type']);

                if ($io_stock_qty < 1)
                    $soldout = '&nbsp;&nbsp;[품절]';
                else
                    $soldout = '';

                $options[$opt_id[0]][] = '<option value="' . $opt_id[1] . ',' . $row['io_price'] . ',' . $io_stock_qty . ',0">' . $opt_id[1] . $price . $soldout . '</option>';
            }
        }

        // 옵션항목 만들기
        for ($i = 0; $i < $subj_count; $i++) {
            $opt = isset($options[$subj[$i]]) ? $options[$subj[$i]] : array();
            $opt_count = count($opt);
            if ($opt_count) {
                $seq = $i + 1;
                $str .= '<div class=sp_obox>';
                $str .= '<ul>';
                $str .= '<li class=tlst><label for="it_supply_' . $seq . '">' . $subj[$i] . '</label></li>';

                $select = '<select id="it_supply_' . $seq . '" class="it_supply" ' . $event . '>';
                $select .= '<option value="">선택안함</option>';
                for ($k = 0; $k < $opt_count; $k++) {
                    $opt_val = $opt[$k];
                    if ($opt_val) {
                        $select .= $opt_val;
                    }
                }
                $select .= '</select>';

                $str .= '<li class=trst>' . $select . '</li>';
                $str .= '</ul>';
                $str .= '</div>';
            }
        }

        return $str;
    }

    // 장바구니 옵션호출
    function mobile_print_item_options($gs_id, $set_mb_id)
    {
        $sql = "SELECT io_id, ct_option, ct_qty, io_type, io_price
                        FROM shop_cart
                        WHERE index_no = ? AND mb_id=? AND ct_select='0' AND is_direct = 0
                        ORDER BY io_type ASC, index_no ASC";
        $data = array($gs_id, $set_mb_id);
        $result = $this->ci->query_model->returnArr($sql, $data);
        $str = '';
        $i = 0;
        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= '<ul>';

            if (!$row['io_id']) continue;

            $price_plus = '';
            if ($row['io_price'] >= 0)
                $price_plus = '+';

            $str .= "<li>{$row['ct_option']} " . display_qty($row['ct_qty']) . " (" . $price_plus . display_price($row['io_price']) . ")</li>";
            $i++;
        }

        if ($i > 0)
            $str .= '</ul>';

        return $str;
    }

    // 주문완료 옵션호출
    function mobile_print_complete_options($gs_id, $od_no)
    {
        $sql = "SELECT io_id, ct_option, ct_qty, io_type, io_price
                        FROM shop_cart
                        WHERE od_no = ? and gs_id = ?
                        ORDER BY io_type ASC, index_no ASC ";
        $data = array($od_no, $gs_id);
        $result = $this->ci->Query_model->returnArr($sql, $data);

        $str = '';
        $comma = '';
        $i = 0;
        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= '<ul>';

            if (!$row['io_id']) continue;

            $price_plus = '';
            if ($row['io_price'] >= 0)
                $price_plus = '+';

            $str .= "<li class=\"fc_999\">{$row['ct_option']} " . display_qty($row['ct_qty']) . " (" . $price_plus . display_price($row['io_price']) . ")</li>";
            $i++;
        }

        if ($i > 0)
            $str .= '</ul>';

        return $str;
    }

    // 메인배너 출력
    function mobile_slider($code, $mb_id)
    {
        $str = "";
        $sql = $this->ci->gvcomplex_lib->sql_banner_rows($code, $mb_id);
        $result = $this->ci->Query_model->returnArr($sql);
        foreach ($result as $key => $row) {
            $a1 = $a2 = '';
            $file = './data/banner/' . $row['bn_file'];
            if (is_file($file) && $row['bn_file']) {
                if ($row['bn_link']) {
                    $a1 = "<a href=\"{$row['bn_link']}\" target=\"{$row['bn_target']}\">";
                    $a2 = "</a>";
                }

                $file = base_url() . 'data/banner/' . $row['bn_file'];
                $file = rpc($file);
                #var_dump($row['bn_file']);
                $str .= "{$a1}<img src=\"{$file}\">{$a2}\n";
            }
        }

        return $str;
    }

    // 배너 자체만 리턴
    function mobile_banner($code, $mb_id)
    {
        $str = "";

        $sql = $this->ci->gvcomplex_lib->sql_banner($code, $mb_id);
        $row = $this->ci->Query_model->returnOneArr($sql);

        $file = './data/banner/' . $row['bn_file'];
        $a1 = "";
        $a2 = "";
        if (is_file($file) && $row['bn_file']) {
            if ($row['bn_link']) {
                $a1 = "<a href=\"{$row['bn_link']}\" target=\"{$row['bn_target']}\">";
                $a2 = "</a>";
            }

            $file = base_url() . 'data/banner/' . $row['bn_file'];
            $file = rpc($file);
            $str = "{$a1}<img src=\"{$file}\">{$a2}";
        }

        return $str;
    }

    // 배너 (동일한 배너코드가 부여될경우 세로로 계속하여 출력)
    function mobile_banner_rows($code, $mb_id)
    {
        $str = "";

        $sql = $this->ci->gvcomplex_lib->sql_banner_rows($code, $mb_id);
        $result = $this->ci->Query_model->returnArr($sql);
        foreach ($result as $key => $row) {
            $a1 = $a2 = $bg = '';

            $file = './data/banner/' . $row['bn_file'];
            if (is_file($file) && $row['bn_file']) {
                if ($row['bn_link']) {
                    $a1 = "<a href=\"{$row['bn_link']}\" target=\"{$row['bn_target']}\">";
                    $a2 = "</a>";
                }

                $file = base_url() . 'data/banner/' . $row['bn_file'];
                $file = rpc($file);
                $str .= "<li>{$a1}<img src=\"{$file}\">{$a2}</li>\n";
            }
        }

        if ($i > 0)
            $str = "<ul>\n{$str}</ul>\n";

        return $str;
    }

    #partner.lib.php에 있던 것
    // 가맹점인가?
    function is_partner($mb_id)
    {
        if (!$mb_id) return '';

        $mb = $this->ci->global_lib->get_member($mb_id, 'grade');
        $pt = $this->ci->partner_lib->get_partner($mb_id, 'state');
        if (isset($pt['state']) && $pt['state'] != null) {
            return true;
        } else {
            return false;
        }
    }

    // 수수료 삭제
    function delete_pay($mb_id, $rel_table, $rel_id, $rel_action)
    {
        $result = false;
        if ($rel_table || $rel_id || $rel_action) {
            // 수수료 내역정보
            $sql = " select *
                            from shop_partner_pay
                            where mb_id = ?
                                and pp_rel_table = ?
                                and pp_rel_id = ?
                                and pp_rel_action = ?";
            $data = array($mb_id, $rel_table, $rel_id, $rel_action);
            $row = $this->ci->Query_model->returnOneArr($sql, $data);

            if ($row['pp_pay'] < 0) {
                $mb_id = $row['mb_id'];
                $pp_pay = abs($row['pp_pay']);

                $this->ci->partner_lib->delete_use_pay($mb_id, $pp_pay);
            } else {
                if ($row['pp_use_pay'] > 0) {
                    $this->ci->partner_lib->nsert_use_pay($row['mb_id'], $row['pp_use_pay'], $row['pp_id']);
                }
            }

            $sql = " delete from shop_partner_pay
                                where mb_id = ? and pp_rel_table = ? and pp_rel_id = ? and pp_rel_action = ?";
            $data = array($mb_id, $rel_table, $rel_id, $rel_action);
            $result = $this->ci->Query_model->returnNull($sql, $data);

            $sql = " update shop_partner_pay set pp_balance = pp_balance - ? where mb_id = ? and pp_id > ?";
            $data = array($row['pp_pay'], $mb_id, $row['pp_id']);
            $this->ci->Query_model->returnNull($sql, $data);

            // 수수료 내역의 합을 구하고
            $sum_pay = $this->ci->partner_lib->get_pay_sum($mb_id);

            // 수수료 UPDATE
            $sql = " update shop_member set pay = ? where id = ?";
            $data = array($sum_pay, $mb_id);
            $result = $this->ci->Query_model->returnNull($sql, $data);
            #$result = sql_query($sql);
        }

        return $result;
    }

    // 가맹점 계좌출력
    function print_partner_bank($mb_id)
    {
        $row = $this->ci->partner_lib->get_partner($mb_id, 'bank_name, bank_account, bank_holder');

        $info = array();
        $info[] = $this->get_text($row['bank_name']); // 은행명
        $info[] = $this->get_text($row['bank_account']); // 계좌번호
        $info[] = $this->get_text($row['bank_holder']); // 예금주명

        if ($info[0] && $info[1] && $info[2])
            $bank_str = implode(' ', $info);
        else
            $bank_str = '미등록';

        return $bank_str;
    }

    // 가맹점 계좌출력
    function print_partner_bank2($bank_name, $bank_account, $bank_holder)
    {
        $info = array();
        $info[] = $this->get_text($bank_name); // 은행명
        $info[] = $this->get_text($bank_account); // 계좌번호
        $info[] = $this->get_text($bank_holder); // 예금주명

        if ($info[0] && $info[1] && $info[2])
            $bank_str = implode(' ', $info);
        else
            $bank_str = '미등록';

        return $bank_str;
    }

    // 수수료 부여
    function insert_pay($mb_id, $pay, $content = '', $rel_table = '', $rel_id = '', $rel_action = '', $referer = '', $agent = '')
    {
        $OM_TIME_YMDHIS = $this->OM_TIME_YMDHIS;
        // 수수료가 없거나 승인된 가맹점이 아니라면 업데이트 할 필요 없음
        if ($pay == 0 || !$this->is_partner($mb_id)) {
            return 0;
        }

        // 이미 등록된 내역이라면 건너뜀
        if ($rel_table || $rel_id || $rel_action) {
            $sql = " select count(*) as cnt from shop_partner_pay where mb_id = ? and pp_rel_table = ? and pp_rel_id = ? and pp_rel_action = ?";
            $data = array($mb_id, $rel_table, $rel_id, $rel_action);
            $row = $this->ci->Query_model->returnOneArr($sql, $data);
            if ($row['cnt'])
                return -1;
        }

        $pt_pay = $this->ci->partner_lib->get_pay_sum($mb_id); // 회원수수료
        $pp_balance = $pt_pay + $pay; // 잔액

        $sql = " insert into shop_partner_pay set mb_id = ?, pp_datetime = ?, pp_content = ?
                            , pp_pay = ?, pp_use_pay = '0', pp_balance = ?, pp_rel_table = ?, pp_rel_id = ?
                            , pp_rel_action = ?, pp_referer = ?, pp_agent = ?";
        $data = array($mb_id, $OM_TIME_YMDHIS, addslashes($content), $pay, $pp_balance, $rel_table, $rel_id . $rel_action, $referer, $agent);
        $this->ci->Query_model->retunNull($sql, $data);

        // 수수료를 사용한 경우 수수료 내역에 사용금액 기록
        if ($pay < 0) {
            $this->ci->partner_lib->insert_use_pay($mb_id, $pay);
        }

        // 수수료 UPDATE
        $sql = " update shop_member set pay = ? where id = ?";
        $data = array($pp_balance, $mb_id);
        $this->ci->Query_model->returnNull($sql, $data);

        // 누적수수료에 따른 자동 레벨업
        $this->check_promotion($mb_id);

        return 1;
    }

    // 누적수수료에 따른 자동 레벨업
    function check_promotion($mb_id)
    {
        if (!$this->is_partner($mb_id))
            return;

        // 수수료 총적립액
        $info = $this->ci->partner_ci->get_pay_sheet($mb_id);
        $sum_pay = $info['pay'];
        if ($sum_pay <= 0)
            return;

        $mb = $this->ci->global_lib->get_member($mb_id, 'grade');

        // 최상위 레벨이면 리턴
        if ($mb['grade'] == 2)
            return;

        $sql = " select gb_no, gb_promotion from shop_member_grade where gb_no between '2' and '6' order by gb_no asc ";
        $result = $this->ci->Query_model->returnArr($sql);
        foreach ($result as $key => $row) {
            if ($mb['grade'] == $row['gb_no'])
                break;

            if (!$row['gb_promotion'])
                continue;

            if ($sum_pay >= $row['gb_promotion']) {
                #$sql = " update shop_member set grade = '{$row['gb_no']}' where id = '$mb_id' ";
                #sql_query($sql);
                $sql = " update shop_member set grade = ? where id = ?";
                $data = array($row['gb_no'], $mb_id);
                break;
            }
        }
    }

    #register.lib.php
    function valid_mb_name($mb_name)
    {
        if (!check_string($mb_name, 16))
            return "이름은 공백없이 한글만 입력 가능합니다.";
        else
            return "";
    }

    function exist_mb_hp($reg_mb_hp, $reg_mb_id)
    {
        if (!trim($reg_mb_hp)) return "";

        $reg_mb_hp = hyphen_hp_number($reg_mb_hp);

        $sql = "select count(*) as cnt from shop_member where cellphone = ? and id <> ?";
        $data = array($reg_mb_hp, $reg_mb_id);
        $row = $this->ci->Query_model->returnOneArr($sql, $data);

        if ($row['cnt'])
            return " 이미 사용 중인 휴대폰번호입니다. " . $reg_mb_hp;
        else
            return "";
    }

    #naverpay.lib.php
    function get_naverpay_item_option($gs_id, $subject)
    {
        if (!$gs_id || !$subject)
            return '';

        $sql = " select * from shop_goods_option where io_type = '0' and gs_id = ? and io_use = '1' order by io_no asc ";
        $data = array($gs_id);
        $resultObj = $this->ci->Query_model->returnQueryObj($sql, $data);
        $result = $resultObj->result_array();
        if (!$resultObj->num_rows())
            return '';

        $str = '';
        $subj = explode(',', $subject);
        $subj_count = count($subj);

        $option = '';
        if ($subj_count > 1) {
            $options = array();

            // 옵션항목 배열에 저장
            foreach ($result as $key => $row) {
                $osl_id = explode(' ', $row['io_id']);

                for ($k = 0; $k < $subj_count; $k++) {
                    if (!is_array($options[$k]))
                        $options[$k] = array();

                    if ($osl_id[$k] && !in_array($osl_id[$k], $options[$k]))
                        $options[$k][] = $osl_id[$k];
                }
            }

            // 옵션선택목록 만들기
            for ($i = 0; $i < $subj_count; $i++) {
                $opt = isset($options[$i]) ? $options[$i] : array();
                $osl_count = count($opt);
                if ($osl_count) {
                    $option .= '<option name="' . $this->get_text($subj[$i]) . '">';
                    for ($k = 0; $k < $osl_count; $k++) {
                        $osl_val = $opt[$k];
                        if (strlen($osl_val)) {
                            $option .= '<select><![CDATA[' . $osl_val . ']]></select>';
                        }
                    }
                    $option .= '</option>';
                }
            }
        } else {
            $option .= '<option name="' . ($this->get_text($subj[0])) . '">';
            foreach ($result as $key => $row) {
                $option .= '<select><![CDATA[' . $row['io_id'] . ']]></select>';
            }

            $option .= '</option>';
        }

        return '<options>' . $option . '</options>';
    }


    #함수와 글러벌 변수가 같이 섞여있는 함수들
    #common.lib.php
    //  주문관리에 사용될 배송업체 정보를 select로 얻음
    function get_delivery_select($name, $selected = '', $event = '')
    {
        $config = $this->ci->load->get_var('config');

        $str = "<select name=\"{$name}\"{$event}>\n";
        $str .= "<option value=\"\">배송사선택</option>\n";
        $info = array_filter(explode(",", trim($config['delivery_company'])));
        foreach ($info as $k => $v) {
            $arr = explode("|", trim($info[$k]));
            if (trim($arr[0])) {
                $str .= option_selected($info[$k], $selected, trim($arr[0]));
            }
        }
        $str .= "</select>";

        return $str;
    }

    // 쿠폰 : 상세내역
    function get_cp_contents($row)
    {
        $gw_usepart = $this->ci->config->item('gw_usepart');

        $str = "";
        $str .= "<div>&#183; <strong>" . ($this->get_text($row['cp_subject'])) . "</strong></div>";

        // 동시사용 여부
        $str .= "<div class='fc_eb7'>&#183; ";
        if (!$row['cp_dups']) {
            $str .= '동일한 주문건에 중복할인 가능';
        } else {
            $str .= '동일한 주문건에 중복할인 불가 (1회만 사용가능)';
        }
        $str .= "</div>";

        // 쿠폰유효 기간
        $str .= "<div>&#183; 쿠폰유효 기간 : ";
        if (!$row['cp_inv_type']) {
            // 날짜
            if ($row['cp_inv_sdate'] == '9999999999') $cp_inv_sdate = '';
            else $cp_inv_sdate = $row['cp_inv_sdate'];

            if ($row['cp_inv_edate'] == '9999999999') $cp_inv_edate = '';
            else $cp_inv_edate = $row['cp_inv_edate'];

            if ($row['cp_inv_sdate'] == '9999999999' && $row['cp_inv_sdate'] == '9999999999')
                $str .= '제한없음';
            else
                $str .= $cp_inv_sdate . " ~ " . $cp_inv_edate;

            // 시간대
            $str .= "&nbsp;(시간대 : ";
            if ($row['cp_inv_shour1'] == '99') $cp_inv_shour1 = '';
            else $cp_inv_shour1 = $row['cp_inv_shour1'] . "시부터";

            if ($row['cp_inv_shour2'] == '99') $cp_inv_shour2 = '';
            else $cp_inv_shour2 = $row['cp_inv_shour2'] . "시까지";

            if ($row['cp_inv_shour1'] == '99' && $row['cp_inv_shour1'] == '99')
                $str .= '제한없음';
            else
                $str .= $cp_inv_shour1 . " ~ " . $cp_inv_shour2;
            $str .= ")";
        } else {
            $cp_inv_day = date("Y-m-d", strtotime("+{$row['cp_inv_day']} days", strtotime($row['cp_wdate'])));
            $str .= '다운로드 완료 후 ' . $row['cp_inv_day'] . '일간 사용가능, 만료일(' . $cp_inv_day . ')';
        }
        $str .= "</div>";

        // 혜택
        $str .= "<div>&#183; ";
        if ($row['cp_sale_type'] == '0') {
            if ($row['cp_sale_amt_max'] > 0)
                $cp_sale_amt_max = "&nbsp;(최대 " . display_price($row['cp_sale_amt_max']) . "까지 할인)";
            else
                $cp_sale_amt_max = "";

            $str .= $row['cp_sale_percent'] . '% 할인' . $cp_sale_amt_max;
        } else {
            $str .= display_price($row['cp_sale_amt']) . ' 할인';
        }
        $str .= "</div>";

        // 최대금액
        if ($row['cp_low_amt'] > 0) {
            $str .= "<div>&#183; " . display_price($row['cp_low_amt']) . " 이상 구매시</div>";
        }

        // 사용가능대상
        $str .= "<div>&#183; " . $gw_usepart[$row['cp_use_part']] . "</div>";

        return $str;
    }

    // 상품 가격정보의 배열을 리턴
    function get_price($gs_id, $msg = '<span>원</span>')
    {
        $member = $this->ci->load->get_var('member');
        $is_member = $this->ci->load->get_var('is_member');

        $gs = $this->ci->global_lib->get_goods($gs_id, 'index_no, price_msg, buy_level, buy_only');
        $price = $this->ci->gvcomplex_lib->get_sale_price($gs_id);

        // 재고가 한정상태이고 재고가 없을때, 품절상태일때..
        if ($this->is_soldout($gs['index_no'])) {
            $str = "<span class=\"soldout\">품절</span>";
        } else {
            if ($gs['price_msg']) {
                $str = $gs['price_msg'];
            } else if ($gs['buy_only'] == 1 && $member['grade'] > $gs['buy_level']) {
                $str = "";
            } else if ($gs['buy_only'] == 0 && $member['grade'] > $gs['buy_level']) {
                if (!$is_member)
                    $str = "<span class=\"memopen\">회원공개</span>";
                else
                    $str = "<span class=\"mpr\">" . number_format($price) . $msg . "</span>";
            } else {
                $str = "<span class=\"mpr\">" . number_format($price) . $msg . "</span>";
            }
        }

        return $str;
    }

    // bid 가격 리턴
    function get_timesale_goods_price($gs_id, $msg = '<span>원</span>')
    {
        $member = $this->ci->load->get_var('member');
        $is_member = $this->ci->load->get_var('is_member');

        $gs = $this->ci->global_lib->get_goods($gs_id, 'index_no, price_msg, buy_level, buy_only');
        $price = $this->ci->gvcomplex_lib->get_timesale_goods_price($gs_id);

        // 재고가 한정상태이고 재고가 없을때, 품절상태일때..
        $str = "<span class=\"mpr\">" . number_format($price) . $msg . "</span>";

        return $str;
    }

    // 회원권한을 SELECT 형식으로 얻음
    function get_member_level_select($name, $start_id = 0, $end_id = 10, $selected = '', $event = '')
    {
        $board = $this->ci->load->get_var('board');

        $str  = "<select id=\"{$name}\" name=\"{$name}\"";
        if ($event) $str .= " $event";
        $str .= ">\n";
        for ($i = $start_id; $i <= $end_id; $i++) {
            $grade = $this->ci->global_lib->get_grade($i);
            if ($grade) {
                $str .= "<option value='$i'";
                if ($i == $selected)
                    $str .= " selected";
                $str .= ">$grade</option>\n";
            }
        }
        $sel = "";
        if ($board[$name] == '99')
            $sel = " selected";
        $str .= "<option value='99'{$sel}>비회원</option>\n";
        $str .= "</select>\n";

        return $str;
    }

    // 계좌정보를 select 박스 형식으로 얻는다
    function get_bank_account($name, $selected = '')
    {
        $default = $this->ci->load->get_var('default');
        $str  = '<select id="' . $name . '" name="' . $name . '">';
        $str .= '<option value="">선택하십시오</option>';

        $bank = unserialize($default['de_bank_account']);
        $limit = 5;
        for ($i = 0; $i < $limit; $i++) {
            $bank[$i]['name'] = isset($bank[$i]['name']) ? $bank[$i]['name'] : "";
            $bank[$i]['account'] = isset($bank[$i]['account']) ? $bank[$i]['account'] : "";
            $bank[$i]['holder'] = isset($bank[$i]['holder']) ? $bank[$i]['holder'] : "";
            $bank_account = $bank[$i]['name'] . ' ' . $bank[$i]['account'] . ' ' . $bank[$i]['holder'];
            if (trim($bank_account)) {
                $str .= option_selected($bank_account, $selected, $bank_account);
            }
        }
        $str .= '</select>';

        return $str;
    }

    // 계좌정보를 select 얻기
    function get_order_bank_account()
    {
        $default = $this->ci->load->get_var('default');
        $bank = unserialize($default['de_bank_account']);
        $bankInfo = array();
        $limit = 5;
        for ($i = 0; $i < $limit; $i++) {
            $bank[$i]['name'] = isset($bank[$i]['name']) ? $bank[$i]['name'] : "";
            $bank[$i]['account'] = isset($bank[$i]['account']) ? $bank[$i]['account'] : "";
            $bank[$i]['holder'] = isset($bank[$i]['holder']) ? $bank[$i]['holder'] : "";

            if ($bank[$i]['name'] != '' || $bank[$i]['account'] != '' || $bank[$i]['holder'] != '') {
                $bankInfo[$i]['name']      = $bank[$i]['name'];
                $bankInfo[$i]['account']   = $bank[$i]['account'];
                $bankInfo[$i]['holde']     = $bank[$i]['holder'];
            }
        }

        return $bankInfo;
    }

    // 주문 진행상태를 select로 얻음
    function get_change_select($name, $selected = '', $event = '')
    {
        $gw_status = $this->ci->config->item('gw_status');
        $gw_array_status = $this->ci->config->item('gw_array_status');

        // 취소,반품,교환,환불 건은 텍스트형식으로만 노출
        if (!in_array($selected, array(2, 3, 4, 5))) {
            return $gw_status[$selected];
        }

        $str = "<select name=\"{$name}\"{$event}>\n";
        foreach ($gw_array_status as $key => $val) {
            if ($key != $selected) continue;

            $str .= option_selected($key, $selected, $gw_status[$key]);
            foreach ($val as $dan) {
                $str .= option_selected($dan, '', $gw_status[$dan]);
            }
        }
        $str .= "</select>";

        return $str;
    }

    function cart_order_submit($od_id, $index_no)
    {

        $ss_cart_id = "";

        if (strlen($index_no) > 0) {
            $ss_cart_id .= rtrim($index_no, ',');
        }

        // 장바구니 주문완료 처리(무통장)
        $sql = " update shop_cart set od_id = ? , ct_select = '1' where index_no IN ({$index_no})";
        $param = array($od_id);
        $this->ci->query_model->returnNull($sql, $param);
    }

    // 상품상세페이지 : 배송비 구함
    function get_sendcost_amt()
    {
        $gs = $this->ci->load->get_var('gs');
        $config = $this->ci->load->get_var('config');
        $sr = $this->ci->load->get_var('sr');
        $str = "";

        // 공통설정
        if ($gs['sc_type'] == '0') {
            if ($gs['mb_id'] == 'admin') {
                $delivery_method  = $config['delivery_method'];
                $delivery_price   = $config['delivery_price'];
                $delivery_price2  = $config['delivery_price2'];
                $delivery_minimum = $config['delivery_minimum'];
            } else {
                $delivery_method  = $sr['delivery_method'];
                $delivery_price      = $sr['delivery_price'];
                $delivery_price2  = $sr['delivery_price2'];
                $delivery_minimum = $sr['delivery_minimum'];
            }

            switch ($delivery_method) {
                case '1':
                    $str = "무료배송";
                    break;
                case '2':
                    $str = "상품수령시 결제(착불)";
                    break;
                case '3':
                    $str = display_price($delivery_price);
                    break;
                case '4':
                    $str = display_price($delivery_price2) . "&nbsp;(" . display_price($delivery_minimum) . " 이상 구매시 무료)";
                    break;
            }

            // sc_type(배송비 유형)   0:공통설정, 1:무료배송, 2:조건부무료배송, 3:유료배송
            // sc_method(배송비 결제) 0:선불, 1:착불, 2:사용자선택
            if (in_array($delivery_method, array('3', '4'))) {
                if ($gs['sc_method'] == 1)
                    $str = '상품수령시 결제(착불)';
                else if ($gs['sc_method'] == 2) {
                    $str = "<select name=\"ct_send_cost\">
                                        <option value='0'>주문시 결제(선결제)</option>
                                        <option value='1'>상품수령시 결제(착불)</option>
                                    </select>";
                }
            }
        }

        // 무료배송
        else if ($gs['sc_type'] == '1') {
            $str = "무료배송";
        }

        // 조건부 무료배송
        else if ($gs['sc_type'] == '2') {
            $str = display_price($gs['sc_amt']) . "&nbsp;(" . display_price($gs['sc_minimum']) . " 이상 구매시 무료)";
        }

        // 유료배송
        else if ($gs['sc_type'] == '3') {
            $str = display_price($gs['sc_amt']);
        }

        // sc_type(배송비 유형)		0:공통설정, 1:무료배송, 2:조건부 무료배송, 3:유료배송
        // sc_method(배송비 결제)	0:선불, 1:착불, 2:사용자선택
        if (in_array($gs['sc_type'], array('2', '3'))) {
            if ($gs['sc_method'] == 1)
                $str = '상품수령시 결제(착불)';
            else if ($gs['sc_method'] == 2) {
                $str = "<select name=\"ct_send_cost\">
                                    <option value='0'>주문시 결제(선결제)</option>
                                    <option value='1'>상품수령시 결제(착불)</option>
                                </select>";
            }
        }

        return $str;
    }

    // 배송비 구함
    function get_sendcost_amt2($gs_id, $it_price)
    {
        $config = $this->ci->load->get_var('config');
        $sendcost = "";
        $gs = $this->ci->global_lib->get_goods($gs_id);

        if (!$gs['index_no'])
            return 0;

        if ($gs['use_aff'])
            $sr = $this->ci->partner_lib->get_partner($gs['mb_id']);
        else
            $sr = $this->ci->global_lib->get_seller_cd($gs['mb_id']);

        // 공통설정
        if ($gs['sc_type'] == '0') {

            if ($gs['mb_id'] == 'admin') {
                $delivery_method  = $config['delivery_method'];
                $delivery_price      = $config['delivery_price'];
                $delivery_price2  = $config['delivery_price2'];
                $delivery_minimum = $config['delivery_minimum'];
            } else {
                $delivery_method  = $sr['delivery_method'];
                $delivery_price      = $sr['delivery_price'];
                $delivery_price2  = $sr['delivery_price2'];
                $delivery_minimum = $sr['delivery_minimum'];
            }

            switch ($delivery_method) {
                case '1':
                case '2':
                    $sendcost = 0;
                    break;
                case '3':
                    $sendcost = (int)$delivery_price;
                    break;
                case '4':
                    if ($it_price >= (int)$delivery_minimum)
                        $sendcost = 0;
                    else
                        $sendcost = (int)$delivery_price2;
                    break;
            }

            // sc_type(배송비 유형)		0:공통설정, 1:무료배송, 2:조건부무료배송, 3:유료배송
            // sc_method(배송비 결제)	0:선불, 1:착불, 2:사용자선택
            if (in_array($delivery_method, array('3', '4'))) {
                if ($gs['sc_method'] == 1) {
                    $sendcost = 0;
                }
            }
        }

        // 무료배송
        else if ($gs['sc_type'] == '1') {
            $sendcost = 0;
        }

        // 조건부 무료배송
        else if ($gs['sc_type'] == '2') {
            if ($it_price >= (int)$gs['sc_minimum'])
                $sendcost = 0;
            else
                $sendcost = (int)$gs['sc_amt'];
        }

        // 유료배송
        else if ($gs['sc_type'] == '3') {
            $sendcost = (int)$gs['sc_amt'];
        }

        // sc_type(배송비 유형)   0:공통설정, 1:무료배송, 2:조건부 무료배송, 3:유료배송
        // sc_method(배송비 결제) 0:선불, 1:착불, 2:사용자선택
        if (in_array($gs['sc_type'], array('2', '3'))) {
            if ($gs['sc_method'] == 1) {
                $sendcost = 0;
            }
        }

        return $sendcost;
    }

    // 로고
    function display_logo($filed = 'basic_logo')
    {
        $pt_id = $this->ci->load->get_var('pt_id');

        #$row = sql_fetch("select $filed from shop_logo where mb_id='$pt_id'");
        $sql = "select $filed from shop_logo where mb_id=?";
        $data = array($pt_id);
        $row = $this->ci->Query_model->returnOneArr($sql, $data);
        if (!$row[$filed] && $pt_id != 'admin') {
            #$row = sql_fetch("select $filed from shop_logo where mb_id='admin'");
            $row = $this->ci->Query_model->returnOneArr("select $filed from shop_logo where mb_id='admin'");
        }

        $file = './data/banner/' . $row[$filed];
        if (is_file($file) && $row[$filed]) {
            $file = base_url() . 'data/banner/' . $row[$filed];
            return '<a href="' . base_url() . '"><img src="' . $file . '"></a>';
        } else {
            return '';
        }
    }

    // 게시판 리스트 가져오기
    function board_latest($boardid, $len, $rows, $pt_id)
    {
        $default = $this->ci->load->get_var('default');

        $sql_where = "";
        if ($default['de_board_wr_use']) {
            $sql_where = " where pt_id = '$pt_id' ";
        }

        $str = '';

        $sql = "select * from shop_board_{$boardid} $sql_where order by wdate desc limit $rows ";
        $res = $this->ci->Query_model->returnArr($sql);
        foreach ($res as $key => $row) {
            $subject = cut_str($row['subject'], $len);
            $wdate = date('Y-m-d', intval($row['wdate'], 10));
            $href  = base_url() . "bbs/read?boardid={$boardid}&index_no={$row['index_no']}";
            $str .= "<dd><a href=\"{$href}\">{$subject}</a><span class=\"day\">{$wdate}</span></dd>\n";
        }
        /*
                for($i=0;$row=sql_fetch_array($res);$i++){
                    $subject = cut_str($row['subject'],$len);
                    $wdate = date('Y-m-d',intval($row['wdate'],10));
                    $href  = OM_BBS_URL."/read.php?boardid={$boardid}&index_no={$row['index_no']}";

                    $str .= "<dd><a href=\"{$href}\">{$subject}</a><span class=\"day\">{$wdate}</span></dd>\n";
                }*/

        return $str;
    }

    #global.lib.php에 있는 것
    // http://htmlpurifier.org/
    // Standards-Compliant HTML Filtering
    // Safe  : HTML Purifier defeats XSS with an audited whitelist
    // Clean : HTML Purifier ensures standards-compliant output
    // Open  : HTML Purifier is open-source and highly customizable
    function html_purifier($html)
    {
        $f = file('./plugin/htmlpurifier/safeiframe.txt');
        $domains = array();
        foreach ($f as $domain) {
            // 첫행이 # 이면 주석 처리
            if (!preg_match("/^#/", $domain)) {
                $domain = trim($domain);
                if ($domain)
                    array_push($domains, $domain);
            }
        }
        // 내 도메인도 추가
        array_push($domains, $_SERVER['HTTP_HOST'] . '/');
        $safeiframe = implode('|', $domains);

        include_once('./plugin/htmlpurifier/HTMLPurifier.standalone.php');
        $config = HTMLPurifier_Config::createDefault();
        // data/cache 디렉토리에 CSS, HTML, URI 디렉토리 등을 만든다.
        $config->set('Cache.SerializerPath', './data/cache');
        $config->set('HTML.SafeEmbed', false);
        $config->set('HTML.SafeObject', false);
        $config->set('Output.FlashCompat', false);
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(' . $safeiframe . ')%');
        $config->set('Attr.AllowedFrameTargets', array('_blank'));
        $purifier = new HTMLPurifier($config);
        return $purifier->purify($html);
    }
    // 별 이미지
    function get_star_image($gs_id)
    {
        $default = $this->ci->load->get_var('default');
        $pt_id = $this->ci->load->get_var('pt_id');

        $sql = "select (SUM(score) / COUNT(*)) as avg
                        from shop_goods_review
                        where gs_id = ?";
        $data = array($gs_id);
        if ($default['de_review_wr_use']) {
            $sql .= " and pt_id = ? ";
            array_push($data, $pt_id);
        }
        $row = $this->ci->Query_model->returnOneArr($sql, $data);

        return (int)get_star($row['avg']);
    }

    // 쿠폰 체크
    function is_used_coupon($type, $gs_id, $mb_id)
    {
        $OM_TIME_YMD = $this->OM_TIME_YMD;
        $OM_TIME_MONTH = date('m', time());
        $OM_TIME_DAY = date('d', time());
        $OM_TIME_YEAR = date("Y", time());
        $ss_cpdown = "";
        $config = $this->ci->load->get_var('config');

        if (!$config['coupon_yes']) return '';

        $tmp_coupon = array();

        $mb = $this->ci->global_lib->get_member($mb_id);

        $sql = "SELECT * FROM shop_coupon WHERE cp_use = '1' AND cp_download = ?";
        $data = array($type);
        $result = $this->ci->Query_model->returnArr($sql, $data);

        foreach ($result as $key => $cp) {
            $sql_fld = " WHERE cp_id = '$cp[cp_id]' AND mb_id = '$mb[id]' ";
            if ($cp['cp_overlap']) { // 중복발급 허용시
                $sql_fld .= " AND mb_use = '0' ";
            }

            // 회원이 이미 발급받은 쿠폰 확인
            $sql = "SELECT count(*) AS cnt FROM shop_coupon_log {$sql_fld} ";
            $row = $this->ci->Query_model->returnOneArr($sql);
            $dwd_count = (int)$row['cnt'];

            $is_coupon = false;

            // 다운로드 레벨제한 검사
            if ($cp['cp_id'] && ($mb['grade'] <= $cp['cp_dlevel'])) {

                // 다운로드 누적제한 검사 (무제한이거나 다운로드 횟수가 아직 남아있을때)
                if ($cp['cp_dlimit'] == '99999999999' || ($dwd_count < $cp['cp_dlimit'])) {

                    // 전체상품에 쿠폰사용 가능
                    if ($cp['cp_use_part'] == '0' && $dwd_count == 0) {
                        $is_coupon = true;
                    }

                    // 일부 상품만 쿠폰사용 가능
                    else if ($cp['cp_use_part'] == '1') {

                        if ($cp['cp_use_goods']) {
                            $sql = "SELECT count(*) AS cnt FROM shop_coupon_log {$sql_fld} AND find_in_set(?, cp_use_goods) >= 1 ";
                            $data = array($gs_id);
                            $row = $this->ci->Query_model->returnOneArr($sql, $data);
                            $it_dw_count = (int)$row['cnt'];
                            $cp_el_goods = explode(',', $cp['cp_use_goods']);
                            if (!$it_dw_count && in_array($gs_id, $cp_el_goods)) {
                                $is_coupon = true;
                            }
                        }
                    }

                    // 일부 카테고리만 쿠폰사용 가능
                    else if ($cp['cp_use_part'] == '2') {

                        if ($cp['cp_use_category']) {
                            $ca_list = $this->get_extract($gs_id);
                            $cl = $this->ci->Query_model->returnOneArr("SELECT cp_use_category FROM shop_coupon_log {$sql_fld} ");
                            $ca_dw_count = get_substr_count($ca_list, $cl['cp_use_category']);
                            $ca_to_count = get_substr_count($ca_list, $cp['cp_use_category']);

                            if (!$ca_dw_count && $ca_to_count) {
                                $is_coupon = true;
                            }
                        }
                    }

                    // 일부 상품은 쿠폰사용 불가
                    else if ($cp['cp_use_part'] == '3') {
                        if ($cp['cp_use_goods']) {
                            $sql = "SELECT count(*) AS cnt FROM shop_coupon_log {$sql_fld} AND find_in_set(?, cp_use_goods) < 1 ";
                            $data = array($gs_id);
                            $row = $this->ci->Query_model->returnOneArr($sql, $data);

                            $it_dw_count = (int)$row['cnt'];
                            $cp_el_goods = explode(',', $cp['cp_use_goods']);
                            if (!$it_dw_count && !in_array($gs_id, $cp_el_goods)) {
                                $is_coupon = true;
                            }
                        }
                    }

                    // 일부 카테고리는 쿠폰사용 불가
                    else if ($cp['cp_use_part'] == '4') {

                        if ($cp['cp_use_category']) {
                            $ca_list = $this->get_extract($gs_id);

                            $cl = $this->ci->Query_model->returnOneArr("SELECT cp_use_category FROM shop_coupon_log {$sql_fld} ");
                            $ca_dw_count = get_substr_count($ca_list, $cl['cp_use_category']);
                            $ca_to_count = get_substr_count($ca_list, $cp['cp_use_category']);

                            if (!$ca_dw_count && !$ca_to_count) {
                                $is_coupon = true;
                            }
                        }
                    }
                }
            }

            if ($is_coupon) {
                switch ($cp['cp_type']) {
                    case '0': // 발행 날짜 지정
                        if (($cp['cp_pub_sdate'] <= $OM_TIME_YMD || $cp['cp_pub_sdate'] == '9999999999') &&
                            ($cp['cp_pub_edate'] >= $OM_TIME_YMD || $cp['cp_pub_edate'] == '9999999999')
                        ) {
                            $tmp_coupon[] = $cp['cp_id'];
                        }
                        break;
                    case '1': // 발행 시간/요일 지정
                        if (($cp['cp_pub_sdate'] <= $OM_TIME_YMD || $cp['cp_pub_sdate'] == '9999999999') &&
                            ($cp['cp_pub_edate'] >= $OM_TIME_YMD || $cp['cp_pub_edate'] == '9999999999')
                        ) {
                            $yoil = array(
                                "일" => "0",
                                "월" => "1",
                                "화" => "2",
                                "수" => "3",
                                "목" => "4",
                                "금" => "5",
                                "토" => "6"
                            );

                            $cp_week_day = explode(",", $cp['cp_week_day']);

                            $wr_week = array();
                            for ($j = 0; $j < count($cp_week_day); $j++) {
                                for ($k = 1; checkdate($OM_TIME_MONTH, $k, $OM_TIME_YEAR); $k++) {
                                    $thismonth = $OM_TIME_MONTH . '/' . substr(sprintf('%02d', $k), -2);
                                    $thistime = strtotime($thismonth);
                                    $thisweek = date("w", $thistime);
                                    if ($thisweek == $yoil[$cp_week_day[$j]]) {
                                        $wr_week[] = substr(sprintf('%02d', $k), -2);
                                    }
                                }
                            }

                            $wr_week = array_unique($wr_week, SORT_STRING);
                            $is_week = implode(",", $wr_week);

                            $cnt = substr_count($is_week, $OM_TIME_DAY);
                            if ($cnt) {
                                //쿠폰발행 시간
                                $tmp_cpdown = array();
                                for ($j = 1; $j <= 3; $j++) {
                                    $cp_pub_use = $cp['cp_pub_' . $j . '_use'];
                                    $cp_pub_cnt = $cp['cp_pub_' . $j . '_cnt'];
                                    $cp_pub_down = $cp['cp_pub_' . $j . '_down'];

                                    $cp_pub_shour = sprintf('%02d', $cp['cp_pub_shour' . $j]);
                                    $cp_pub_ehour = sprintf('%02d', $cp['cp_pub_ehour' . $j]);

                                    if (
                                        $cp_pub_use &&
                                        ((date('H') >= $cp_pub_shour || $cp_pub_shour == '99') &&
                                            (date('H') <= $cp_pub_ehour || $cp_pub_ehour == '99'))
                                    ) {

                                        if ($cp_pub_cnt && ($cp_pub_cnt > $cp_pub_down)) {
                                            $tmp_coupon[] = $cp['cp_id'];
                                            $tmp_cpdown[] = 'cp_pub_' . $j . '_down^' . $cp['cp_id'];
                                        }
                                    }
                                }

                                $tmp_cpdown = array_unique($tmp_cpdown, SORT_STRING);
                                $ss_cpdown = implode(",", $tmp_cpdown);
                            }
                        }
                        break;
                    case '2': // 성별구분으로 발급
                        $gender = strtoupper($mb['gender']);
                        if (($cp['cp_pub_sdate'] <= $OM_TIME_YMD || $cp['cp_pub_sdate'] == '9999999999') &&
                            ($cp['cp_pub_edate'] >= $OM_TIME_YMD || $cp['cp_pub_edate'] == '9999999999')
                        ) {
                            if (!$cp['cp_use_sex'] || $cp['cp_use_sex'] == $gender) {
                                $tmp_coupon[] = $cp['cp_id'];
                            }
                        }
                        break;
                    case '3': // 회원 생일자 발급
                        $mb_birth_month    = substr(conv_number($mb['mb_birth']), 4, 2);
                        $mb_birth_day    = substr(conv_number($mb['mb_birth']), 6, 2);
                        $cp_pub_sday    = conv_number($cp['cp_pub_sday']);
                        $cp_pub_eday    = conv_number($cp['cp_pub_eday']);

                        $is_check_vars = false;
                        if ($mb_birth_month && $mb_birth_day) {
                            $is_check_vars = true;
                        }

                        if ($is_check_vars) {
                            $year = date("Y");
                            $month = sprintf('%02d', $mb_birth_month);
                            $day = sprintf('%02d', $mb_birth_day);

                            // 생일 전
                            $fr_day = $day - (int)$cp_pub_sday;
                            $fr_birthday = date("Y-m-d", mktime(0, 0, 1, $month, $fr_day, $year));

                            // 생일 후
                            $to_day = $day + (int)$cp_pub_eday;
                            $to_birthday = date("Y-m-d", mktime(0, 0, 1, $month, $to_day, $year));

                            if ($OM_TIME_YMD >= $fr_birthday && $OM_TIME_YMD <= $to_birthday) {
                                $tmp_coupon[] = $cp['cp_id'];
                            }
                        }
                        break;
                    case '4': // 연령 구분으로 발급
                        if (($cp['cp_pub_sdate'] <= $OM_TIME_YMD || $cp['cp_pub_sdate'] == '9999999999') &&
                            ($cp['cp_pub_edate'] >= $OM_TIME_YMD || $cp['cp_pub_edate'] == '9999999999')
                        ) {

                            $mb_birth_year    = substr(conv_number($mb['mb_birth']), 0, 4);
                            $cp_use_sage    = conv_number($cp['cp_use_sage']);
                            $cp_use_eage    = conv_number($cp['cp_use_eage']);

                            $is_check_vars = false;
                            if (strlen($mb_birth_year) == 4) {
                                if (strlen($cp_use_sage) == 4 && strlen($cp_use_eage) == 4) {
                                    $is_check_vars = true;
                                }
                            }

                            if ($is_check_vars) {
                                if ($mb_birth_year >= $cp_use_sage && $mb_birth_year <= $cp_use_eage) {
                                    $tmp_coupon[] = $cp['cp_id'];
                                }
                            }
                        }
                        break;
                }
            }
        }

        if ($ss_cpdown)
            set_session('ss_pub_down', $ss_cpdown);
        else
            set_session('ss_pub_down', '');

        $tmp_coupon = array_unique($tmp_coupon, SORT_STRING);
        $tmp_list = implode(",", $tmp_coupon);

        return $tmp_list;
    }

    // 쿠폰 발급
    function insert_used_coupon($mb_id, $mb_name, $cp)
    {
        $config = $this->ci->load->get_var('config');
        $OM_TIME_YMDHIS = $this->OM_TIME_YMDHIS;

        if ($config['coupon_yes']) {
            unset($value);
            $value = array(
                'mb_id' => $mb_id,
                'mb_name' => $mb_name,
                'cp_id' => $cp['cp_id'],
                'cp_type' => $cp['cp_type'],
                'cp_dlimit' => $cp['cp_dlimit'],
                'cp_dlevel' => $cp['cp_dlevel'],
                'cp_subject' => $cp['cp_subject'],
                'cp_explan' => $cp['cp_explan'],
                'cp_use' => $cp['cp_use'],
                'cp_download' => $cp['cp_download'],
                'cp_overlap' => $cp['cp_overlap'],
                'cp_sale_type' => $cp['cp_sale_type'],
                'cp_sale_percent' => $cp['cp_sale_percent'],
                'cp_sale_amt_max' => $cp['cp_sale_amt_max'],
                'cp_sale_amt' => $cp['cp_sale_amt'],
                'cp_dups' => $cp['cp_dups'],
                'cp_pub_sdate' => $cp['cp_pub_sdate'],
                'cp_pub_edate' => $cp['cp_pub_edate'],
                'cp_pub_sday' => $cp['cp_pub_sday'],
                'cp_pub_eday' => $cp['cp_pub_eday'],
                'cp_use_sex' => $cp['cp_use_sex'],
                'cp_use_sage' => $cp['cp_use_sage'],
                'cp_use_eage' => $cp['cp_use_eage'],
                'cp_week_day' => $cp['cp_week_day'],
                'cp_pub_1_use' => $cp['cp_pub_1_use'],
                'cp_pub_shour1' => $cp['cp_pub_shour1'],
                'cp_pub_ehour1' => $cp['cp_pub_ehour1'],
                'cp_pub_1_cnt' => $cp['cp_pub_1_cnt'],
                'cp_pub_1_down' => $cp['cp_pub_1_down'],
                'cp_pub_2_use' => $cp['cp_pub_2_use'],
                'cp_pub_shour2' => $cp['cp_pub_shour2'],
                'cp_pub_ehour2' => $cp['cp_pub_ehour2'],
                'cp_pub_2_cnt' => $cp['cp_pub_2_cnt'],
                'cp_pub_2_down' => $cp['cp_pub_2_down'],
                'cp_pub_3_use' => $cp['cp_pub_3_use'],
                'cp_pub_shour3' => $cp['cp_pub_shour3'],
                'cp_pub_ehour3' => $cp['cp_pub_ehour3'],
                'cp_pub_3_cnt' => $cp['cp_pub_3_cnt'],
                'cp_pub_3_down' => $cp['cp_pub_3_down'],
                'cp_inv_type' => $cp['cp_inv_type'],
                'cp_inv_sdate' => $cp['cp_inv_sdate'],
                'cp_inv_edate' => $cp['cp_inv_edate'],
                'cp_inv_shour1' => $cp['cp_inv_shour1'],
                'cp_inv_shour2' => $cp['cp_inv_shour2'],
                'cp_inv_day' => $cp['cp_inv_day'],
                'cp_low_amt' => $cp['cp_low_amt'],
                'cp_use_part' => $cp['cp_use_part'],
                'cp_use_goods' => $cp['cp_use_goods'],
                'cp_use_category' => $cp['cp_use_category'],
                'cp_wdate' => $OM_TIME_YMDHIS
            );
            $this->ci->Query_model->insertObj('shop_coupon_log', $value);

            $ss_pub_down = get_session('ss_pub_down');
            if ($ss_pub_down) {
                unset($value);
                $arr_pub_down = explode(",", $ss_pub_down);
                for ($i = 0; $i < count($arr_pub_down); $i++) {
                    $pub_down = explode("^", $arr_pub_down[$i]);

                    $value[$pub_down[0]] = $cp[$pub_down[0]] + 1;
                    $this->ci->Query_model->updateObj("shop_coupon", $value, "cp_id='$pub_down[1]'");
                }

                set_session('ss_pub_down', '');
            }
        }

        return true;
    }

    // 문자전송 (회원가입)
    function moonjaro_register_sms_send($sms_id, $mb_id)
    {
        $config = $this->ci->load->get_var('config');
        $super = $this->ci->load->get_var('super');

        // 본사 문자설정 사용중인가?
        if (!$config['pf_auth_sms'] && $sms_id != 'admin') {
            $sms_id = 'admin';
        }

        $sm = $this->ci->global_lib->get_sms($sms_id);
        if (!$sm['cf_sms_use'])
            return;

        $mb = $this->ci->global_lib->get_member($mb_id);
        $pt = $this->ci->global_lib->get_member($mb['pt_id']);

        $mb_hp = $mb['cellphone'];
        $pt_hp = ($mb['pt_id'] != 'admin') ? $pt['cellphone'] : '';
        $ad_hp = $super['cellphone'];

        // SMS BEGIN --------------------------------------------------------
        if ($sm['cf_mb_use1'] || $sm['cf_ad_use1'] || $sm['cf_re_use1']) {
            $is_sms_send = false;

            // 충전식일 경우 잔액이 있는지 체크
            if ($sm['cf_moonjaro_key']) {
                $userinfo = get_moonjaro_userinfo($sm['cf_moonjaro_key']);

                if ($userinfo['code']) {
                    if ($userinfo['payment'] == 'C') { // 후불
                        $is_sms_send = true;
                    } else {
                        $minimum_coin = 50;
                        if (defined('OM_MOONJARO_COIN'))
                            $minimum_coin = intval(50);

                        if ((int)$userinfo['coin'] >= $minimum_coin)
                            $is_sms_send = true;
                    }
                }
            }

            if ($is_sms_send) {
                $sms_send_use = array($sm['cf_mb_use1'], $sm['cf_ad_use1'], $sm['cf_re_use1']);
                $recv_numbers = array($mb_hp, $ad_hp, $pt_hp);
                $send_number = conv_number($sm['cf_sms_recall']);

                $sms_count = 0;
                $sms_messages = array();
                for ($s = 0; $s < count($sms_send_use); $s++) {
                    $sms_content = $sm['cf_cont1'];
                    $recv_number = conv_number($recv_numbers[$s]);

                    $sms_content = rpc($sms_content, "{이름}", $mb['name']);
                    $sms_content = rpc($sms_content, "{아이디}", $mb['id']);

                    if ($sms_send_use[$s] && $recv_number) {
                        $sms_messages[] = array('to' => $recv_number, 'send' => $send_number, 'cont' => $sms_content);
                        $sms_count++;
                    }
                }

                // SMS 전송
                if ($sms_count > 0) {
                    include_once('./lib/moonjaro.sms.lib.php');
                    $SMS = new SMS; // SMS 연결
                    $SMS->SMS_con($sm['cf_moonjaro_location'], $sm['cf_moonjaro_key']);
                    for ($s = 0; $s < count($sms_messages); $s++) {
                        $recv_number = $sms_messages[$s]['to'];
                        $send_number = $sms_messages[$s]['send'];
                        $sms_content = $sms_messages[$s]['cont'];

                        $SMS->Add($recv_number, $send_number, trim($config['company_name']), $sms_content);
                    }
                    $SMS->Send();
                    $SMS->Init(); // 보관하고 있던 결과값을 지웁니다.
                }
            }
        }
        // SMS END   --------------------------------------------------------
    }

    // 문자전송 (주문관련)
    // 하나씩 다 따로 발송요청되어야함
    function moonjaro_order_sms_send($sms_id, $od_hp, $od_id, $fld)
    {
        $config = $this->ci->load->get_var('config');
        $super = $this->ci->load->get_var('super');

        // 본사 문자설정 사용중인가?
        if (!$config['pf_auth_sms'] && $sms_id != 'admin') {
            $sms_id = 'admin';
        }

        $sm = $this->ci->global_lib->get_sms($sms_id);
        if (!$sm['cf_sms_use'])
            return;

        $od = $this->ci->global_lib->get_order($od_id); // 주문정보
        $pt = $this->ci->global_lib->get_member($od['pt_id'], 'cellphone');
        $sr = $this->ci->global_lib->get_seller_cd($od['seller_id'], 'info_tel');

        $ad_hp = $super['cellphone'];
        $sr_hp = $sr['info_tel'];
        $pt_hp = ($od['pt_id'] != 'admin') ? $pt['cellphone'] : '';

        $dlcomp = explode("|", $od['delivery']);

        // SMS BEGIN --------------------------------------------------------
        if ($sm["cf_mb_use{$fld}"] || $sm["cf_ad_use{$fld}"] || $sm["cf_re_use{$fld}"] || $sm["cf_sr_use{$fld}"]) {
            $is_sms_send = false;

            // 충전식일 경우 잔액이 있는지 체크
            if ($sm['cf_moonjaro_key']) {
                $userinfo = get_moonjaro_userinfo($sm['cf_moonjaro_key']);

                if ($userinfo['code']) {
                    if ($userinfo['payment'] == 'C') { // 후불
                        $is_sms_send = true;
                    } else {
                        $minimum_coin = 50;
                        if (defined('OM_MOONJARO_COIN'))
                            $minimum_coin = intval(50);

                        if ((int)$userinfo['coin'] >= $minimum_coin)
                            $is_sms_send = true;
                    }
                }
            }

            if ($is_sms_send) {
                $sms_send_use = array(
                    $sm["cf_mb_use{$fld}"],
                    $sm["cf_ad_use{$fld}"],
                    $sm["cf_re_use{$fld}"],
                    $sm["cf_sr_use{$fld}"]
                );

                $recv_numbers = array($od_hp, $ad_hp, $pt_hp, $sr_hp);
                $send_number = conv_number($sm['cf_sms_recall']);

                $sms_count = 0;
                $sms_messages = array();
                for ($s = 0; $s < count($sms_send_use); $s++) {
                    $sms_content = $sm["cf_cont{$fld}"];
                    $recv_number = conv_number($recv_numbers[$s]);

                    $sms_content = rpc($sms_content, "{이름}", $od['name']);
                    $sms_content = rpc($sms_content, "{주문번호}", $od_id);
                    $sms_content = rpc($sms_content, "{업체}", $dlcomp[0]);
                    $sms_content = rpc($sms_content, "{송장번호}", $od['delivery_no']);

                    if ($sms_send_use[$s] && $recv_number) {
                        $sms_messages[] = array('recv' => $recv_number, 'send' => $send_number, 'cont' => $sms_content);
                        $sms_count++;
                    }
                }

                // SMS 전송
                if ($sms_count > 0) {
                    include_once('./lib/moonjaro.sms.lib.php');

                    $SMS = new SMS; // SMS 연결
                    $SMS->SMS_con($sm['cf_moonjaro_location'], $sm['cf_moonjaro_key']);

                    for ($s = 0; $s < count($sms_messages); $s++) {
                        $recv_number = $sms_messages[$s]['recv'];
                        $send_number = $sms_messages[$s]['send'];
                        $sms_content = $sms_messages[$s]['cont'];

                        $SMS->Add($recv_number, $send_number, trim($config['company_name']), $sms_content);
                    }
                    $SMS->Send();
                    $SMS->Init(); // 보관하고 있던 결과값을 지웁니다.

                }
            }
        }
        // SMS END   --------------------------------------------------------
    }
    // 문자전송 (개별전송)
    // 같은 문자내용으로 다 보냄.
    function moonjaro_direct_sms_send($sms_id, $recv_number, $sms_content, $count = 1)
    {
        $config = $this->ci->load->get_var('config');

        // 본사 문자설정 사용중인가?
        if (!$config['pf_auth_sms'] && $sms_id != 'admin') {
            $sms_id = 'admin';
        }

        $sm = $this->ci->global_lib->get_sms($sms_id);

        // SMS BEGIN --------------------------------------------------------
        if ($sm['cf_sms_use'] && $recv_number) {
            $is_sms_send = false;

            // 충전식일 경우 잔액이 있는지 체크
            if ($sm['cf_moonjaro_key']) {
                $userinfo = get_moonjaro_userinfo($sm['cf_moonjaro_key']);

                if ($userinfo['code']) {
                    if ($userinfo['payment'] == 'C') { // 후불
                        $is_sms_send = true;
                    } else {
                        $minimum_coin = 50;
                        if (defined('OM_MOONJARO_COIN'))
                            $minimum_coin = intval(50);

                        if ((int)$userinfo['coin'] >= ($minimum_coin * $count))
                            $is_sms_send = true;
                    }
                }
            }

            if ($is_sms_send) {
                $send_number = conv_number($sm['cf_sms_recall']);

                include_once('./lib/moonjaro.sms.lib.php');

                $SMS = new SMS; // SMS 연결
                $SMS->SMS_con($sm['cf_moonjaro_location'], $sm['cf_moonjaro_key']);

                $SMS->AddGroup($recv_number, $send_number, trim($config['company_name']), $sms_content);

                $SMS->Send();
                $SMS->Init(); // 보관하고 있던 결과값을 지웁니다.
            }
        }
        // SMS END   --------------------------------------------------------
    }
    // '배송완료' 상태로 변경
    function change_order_status_5($od_no)
    {
        $config = $this->ci->load->get_var('config');
        $OM_TIME_YMDHIS = $this->OM_TIME_YMDHIS;
        $od = $this->ci->global_lib->get_order($od_no);

        $sql = " update shop_order
                            set dan = '5'
                            , invoice_date = '{$OM_TIME_YMDHIS}'";
        if (is_null_time($od['delivery_date'])) { // 배송일이 비었나?
            $sql .= " , delivery_date = '" . $OM_TIME_YMDHIS . "' ";
        }
        $sql .= " where od_no = '{$od_no}'";
        $this->ci->Query_model->returnNull($sql);
        // 상품 판매수량 반영
        $this->ci->global_lib->add_sum_qty($od['gs_id']);

        // 상품정보
        $gs = unserialize($od['od_goods']);

        // 주문완료 후 배송완료시에 쿠폰발행
        if ($config['coupon_yes'] && !$gs['use_aff'] && $od['mb_id']) {
            $member = $this->ci->global_lib->get_member($od['mb_id']);
            $cp_used = $this->is_used_coupon('2', $od['gs_id'], $od['mb_id']);
            if ($cp_used) {
                $cp_id = explode(",", $cp_used);
                for ($g = 0; $g < count($cp_id); $g++) {
                    if ($cp_id[$g]) {
                        #$cp = sql_fetch("select * from shop_coupon where cp_id='{$cp_id[$g]}'");
                        $sql = "select * from shop_coupon where cp_id=?";
                        $data = array($cp_id[$g]);
                        $cp = $this->ci->Query_model->returnOneArr($sql, $data);
                        $this->insert_used_coupon($od['mb_id'], $od['name'], $cp);
                    }
                }
            }
        }

        // 가맹점 판매수수료 적립
        $this->insert_sale_pay($od['pt_id'], $od, $gs);
    }
    // 시중가등 가격을 보이기위한 검사
    function is_uncase($gs_id)
    {
        $member = $this->ci->load->get_var('member');
        $is_member = $this->ci->load->get_var('is_member');

        $sql = "select index_no,price_msg,buy_level,buy_only from shop_goods where index_no = ?";
        $data = array($gs_id);
        $gs = $this->ci->Query_model->returnOneArr($sql, $data);

        if ($this->is_soldout($gs['index_no'])) {
            // 재고가 한정상태이고 재고가 없을때, 품절상태일때..
            return true;
        } else {
            if ($gs['price_msg']) {
                // 가격대체 문구
                return true;
            } else if ($gs['buy_only'] == 1 && $member['grade'] > $gs['buy_level']) {
                // 특정 레벨이상 가격공개이고 레벨이 해당되지 않을때 가격을 감춤
                return true;
            } else if ($gs['buy_only'] == 0 && $member['grade'] > $gs['buy_level']) {
                // 가격은 모두 공개이지만 레벨이 해당되지 않을때
                if (!$is_member)
                    return true;
                else
                    return false;
            } else {
                return false;
            }
        }
    }

    // 무료배송 검사
    function is_free_baesong($row)
    {
        $config = $this->ci->load->get_var('config');

        $is_free = false;

        if ($row['sc_type'] == 0) {
            if ($row['mb_id'] == 'admin') { // 본사
                $delivery_method = $config['delivery_method'];
            } else { // 가맹점 및 입점사
                if ($row['use_aff'])
                    $sr = $this->ci->partner_lib->get_partner($row['mb_id'], 'delivery_method');
                else
                    $sr = $this->ci->global_lib->get_seller_cd($row['mb_id'], 'delivery_method');

                $delivery_method = $sr['delivery_method'];
            }

            if ($delivery_method == 1)
                $is_free = true;
        } else if ($row['sc_type'] == 1) {
            $is_free = true;
        }

        return $is_free;
    }

    // 조건부무료배송 검사
    function is_free_baesong2($row)
    {
        $config = $this->ci->load->get_var('config');

        $is_free = false;

        if ($row['sc_type'] == 0) {
            if ($row['mb_id'] == 'admin') { // 본사
                $delivery_method = $config['delivery_method'];
            } else { // 가맹점 및 입점사
                if ($row['use_aff'])
                    $sr = $this->ci->partner_lib->get_partner($row['mb_id'], 'delivery_method');
                else
                    $sr = $this->ci->global_lib->get_seller_cd($row['mb_id'], 'delivery_method');

                $delivery_method = $sr['delivery_method'];
            }

            if ($delivery_method == 4)
                $is_free = true;
        } else if ($row['sc_type'] == 2) {
            $is_free = true;
        }

        return $is_free;
    }

    // 배송비 구함
    function get_item_sendcost($row, $sell_price)
    {
        $gs = $this->ci->load->get_var('gs');
        $config = $this->ci->load->get_var('config');
        $info = array();

        // 공통설정
        if ($gs['sc_type'] == '0') {
            if ($gs['mb_id'] == 'admin') { // 본사
                $delivery_method  = $config['delivery_method'];
                $delivery_price      = $config['delivery_price'];
                $delivery_price2  = $config['delivery_price2'];
                $delivery_minimum = $config['delivery_minimum'];
            } else { // 가맹점 및 입점사
                $sr = $this->ci->global_lib->get_seller_cd($gs['mb_id']);

                $delivery_method  = $sr['delivery_method'];
                $delivery_price      = $sr['delivery_price'];
                $delivery_price2  = $sr['delivery_price2'];
                $delivery_minimum = $sr['delivery_minimum'];
            }

            switch ($delivery_method) { // 배송정책
                case '1': // 무료배송
                    $info['price'] = 0;
                    $info['content'] = '무료';
                    break;
                case '2': // 착불배송
                    $info['price'] = 0;
                    $info['content'] = '착불';
                    break;
                case '3': // 유료배송
                    $info['price'] = $delivery_price;
                    $info['content'] = display_price($delivery_price);
                    break;
                case '4': // 조건부무료배송
                    if ($sell_price >= $delivery_minimum) {
                        $info['price'] = 0;
                        $info['content'] = '무료';
                    } else {
                        $info['price'] = $delivery_price2;
                        $info['content'] = display_price($delivery_price2);
                    }
                    break;
            }

            // 조건부무료배송 과 유료배송일때
            if (in_array($delivery_method, array('3', '4'))) {
                if ($gs['sc_method'] == 1) { // 착불
                    $info['price'] = 0;
                    $info['content'] = '착불';
                } else if ($gs['sc_method'] == 2) { // 사용자선택
                    if ($row['ct_send_cost'] == 1) { // 착불
                        $info['price'] = 0;
                        $info['content'] = '착불';
                    }
                }
            }
        } else { // 개별설정
            switch ($gs['sc_type']) {
                case '1': // 무료배송
                    $info['price'] = 0;
                    $info['content'] = '무료';
                    break;
                case '2': // 조건부무료배송
                    if ($sell_price >= $gs['sc_minimum']) {
                        $info['price'] = 0;
                        $info['content'] = '무료';
                    } else {
                        $info['price'] = $gs['sc_amt'];
                        $info['content'] = display_price($gs['sc_amt']);
                    }
                    break;
                case '3': // 유료배송
                    $info['price'] = $gs['sc_amt'];
                    $info['content'] = display_price($gs['sc_amt']);
                    break;
            }

            // 조건부무료배송 과 유료배송일때
            if (in_array($gs['sc_type'], array('2', '3'))) {
                if ($gs['sc_method'] == 1) { // 착불
                    $info['price'] = 0;
                    $info['content'] = '착불';
                } else if ($gs['sc_method'] == 2) { // 사용자선택
                    if ($row['ct_send_cost'] == 1) { // 착불
                        $info['price'] = 0;
                        $info['content'] = '착불';
                    }
                }
            }
        }
        $arr = array();
        $arr[] = $gs['mb_id'];
        $arr[] = $gs['sc_each_use'] ? '개별' : '묶음';
        $arr[] = isset($info['price']) ? $info['price'] : "";
        $info['pattern'] = implode('|', $arr);

        return $info;
    }

    // 로고 url
    function display_logo_url($fld = 'basic_logo')
    {
        $pt_id = $this->ci->load->get_var('pt_id');

        #$row = sql_fetch("select $fld from shop_logo where mb_id='$pt_id'");
        $sql = "select $fld from shop_logo where mb_id=?";
        $data = array($pt_id);
        $row = $this->ci->Query_model->returnOneArr($sql, $data);
        if (!$row[$fld] && $pt_id != 'admin') {
            #$row = sql_fetch("select $fld from shop_logo where mb_id='admin'");
            $row = $this->ci->Query_model->returnOneArr("select $fld from shop_logo where mb_id='admin'");
        }

        $file = './data/banner/' . $row[$fld];
        if (is_file($file) && $row[$fld]) {
            $file = base_url() . 'data/banner/' . $row[$fld];
            return rpc($file);
        }

        return '';
    }

    // 주문완료 옵션호출 (이메일)
    function print_complete_options2($gs_id, $od_id)
    {
        $sql = " select ct_option, ct_qty, io_type, io_price from shop_cart where od_id = ? and gs_id = ? order by io_type asc, index_no asc ";
        $data = array($od_id, $gs_id);
        $result = $this->ci->Query_model->returnArr($sql, $data);

        $str = '';
        $ul_st = ' style="margin:0;padding:0"';
        $ny_st = ' style="list-style:none;font-size:11px;color:#888888"';
        $ty_st = ' style="list-style:none;font-size:11px;color:#7d62c3"';
        $i = 0;
        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= '<ul' . $ul_st . '>';

            $price_plus = '';
            if ($row['io_price'] >= 0)
                $price_plus = '+';

            if ($row['io_type'])
                $str .= "<li" . $ny_st . ">[추가상품]&nbsp;" . $row['ct_option'] . " " . display_qty($row['ct_qty']) . " (" . $price_plus . display_price($row['io_price']) . ")</li>";
            else
                $str .= "<li" . $ty_st . ">" . $row['ct_option'] . " " . display_qty($row['ct_qty']) . " (" . $price_plus . display_price($row['io_price']) . ")</li>";
            $i++;
        }

        if ($i > 0)
            $str .= '</ul>';

        return $str;
    }

    // 상세 페이지출력 (배송/교환/반품)
    function get_policy_content($gs_id)
    {
        $config = $this->ci->load->get_var('config');
        $sr = $this->ci->load->get_var('sr');

        $gs = $this->ci->global_lib->get_goods($gs_id);

        if ($gs['mb_id'] == 'admin') {
            if ($this->ci->agent->is_mobile())
                return $this->get_image_resize($config['baesong_cont2']);
            else
                return $config['baesong_cont1'];
        } else {
            if ($this->ci->agent->is_mobile())
                return $this->get_image_resize($sr['baesong_cont2']);
            else
                return $sr['baesong_cont1'];
        }
    }

    // 본인아래 모든 하위회원 (1대,2대....등)
    /*
            사용법
            $list = array();
            $list = mb_sublist($member['id']);
            $mid = mb_comma($list);
            */
    function mb_sublist($mb_recommend)
    {
        $list = $this->ci->load->get_var('list');

        $list[] = $mb_recommend;
        $sql = "select id from shop_member where pt_id=? order by index_no asc ";
        $data = array($mb_recommend);
        $rst = $this->ci->Query_model->returnArr($sql, $data);
        #$rst = sql_query($sql);
        foreach ($rst as $key => $row) {
            if ($mb_recommend == $row['id']) {
                break;
            } else {
                $this->mb_sublist($row['id']);
            }
        }

        return $list;
    }

    // 인증시도회수 체크
    function certify_count_check($mb_id, $type)
    {
        $config = $this->ci->load->get_var('config');
        $OM_TIME_YMD = $this->OM_TIME_YMD;
        if ($config['cf_cert_use'] != 2)
            return;

        if ($config['cf_cert_limit'] == 0)
            return;

        $sql = " select count(*) as cnt from shop_cert_history ";
        $data = array();
        if ($mb_id) {
            $sql .= " where mb_id = ?";
            array_push($data, $mb_id);
        } else {
            $sql .= " where cr_ip = ?";
            array_push($data, $_SERVER['REMOTE_ADDR']);
        }
        $sql .= " and cr_method = ? and cr_date = ? ";
        array_push($data, $type);
        array_push($data, $OM_TIME_YMD);

        $row = $this->ci->Query_model->returnOneArr($sql, $data);

        switch ($type) {
            case 'hp':
                $cert = '휴대폰';
                break;
            case 'ipin':
                $cert = '아이핀';
                break;
            default:
                break;
        }

        if ((int)$row['cnt'] >= (int)$config['cf_cert_limit'])
            alert_close('오늘 ' . $cert . ' 본인확인을 ' . $row['cnt'] . '회 이용하셔서 더 이상 이용할 수 없습니다.');
    }

    // 쿠폰 : 혜택
    function get_cp_sale_amt($row, $chk_engine)
    {

        $sale_amt = array();
        $sale_amt[0] = 0;
        $sale_amt[1] = '';

        if ($row['cp_sale_type'] == '0') {
            $sale_amt[0] = ($chk_engine / 100) * $row['cp_sale_percent'];
            $sale_amt[1] = $row['cp_sale_percent'] . '%';

            if ($row['cp_sale_amt_max'] > 0 && $sale_amt[0] > $row['cp_sale_amt_max']) {
                $sale_amt[0] = $row['cp_sale_amt_max'];
            }
        } else {
            $sale_amt[0] = $row['cp_sale_amt'];
            $sale_amt[1] = display_price($row['cp_sale_amt']);
        }

        return $sale_amt;
    }

    #mobile.lib.php에 있던 함수들
    // 쿠폰 : 상세내역
    function mobile_cp_contents()
    {
        $row = $this->ci->load->get_var('row');
        $gw_usepart = $this->ci->config->item('gw_usepart');

        $str = "";
        $str .= "<div>&#183; <strong>" . ($this->get_text($row['cp_subject'])) . "</strong></div>";

        // 동시사용 여부
        $str .= "<div class='fc_197'>&#183; ";
        if (!$row['cp_dups']) {
            $str .= '동일한 주문건에 중복할인 가능';
        } else {
            $str .= '동일한 주문건에 중복할인 불가 (1회만 사용가능)';
        }
        $str .= "</div>";

        // 쿠폰유효 기간
        $str .= "<div>&#183; 쿠폰유효 기간 : ";
        if (!$row['cp_inv_type']) {
            // 날짜
            if ($row['cp_inv_sdate'] == '9999999999') $cp_inv_sdate = '';
            else $cp_inv_sdate = $row['cp_inv_sdate'];

            if ($row['cp_inv_edate'] == '9999999999') $cp_inv_edate = '';
            else $cp_inv_edate = $row['cp_inv_edate'];

            if ($row['cp_inv_sdate'] == '9999999999' && $row['cp_inv_sdate'] == '9999999999')
                $str .= '제한없음';
            else
                $str .= $cp_inv_sdate . " ~ " . $cp_inv_edate;

            // 시간대
            $str .= "&nbsp;(시간대 : ";
            if ($row['cp_inv_shour1'] == '99') $cp_inv_shour1 = '';
            else $cp_inv_shour1 = $row['cp_inv_shour1'] . "시부터";

            if ($row['cp_inv_shour2'] == '99') $cp_inv_shour2 = '';
            else $cp_inv_shour2 = $row['cp_inv_shour2'] . "시까지";

            if ($row['cp_inv_shour1'] == '99' && $row['cp_inv_shour1'] == '99')
                $str .= '제한없음';
            else
                $str .= $cp_inv_shour1 . " ~ " . $cp_inv_shour2;
            $str .= ")";
        } else {
            $cp_inv_day = date("Y-m-d", strtotime("+{$row['cp_inv_day']} days", strtotime($row['cp_wdate'])));
            $str .= '다운로드 완료 후 ' . $row['cp_inv_day'] . '일간 사용가능, 만료일(' . $cp_inv_day . ')';
        }
        $str .= "</div>";

        // 혜택
        $str .= "<div>&#183; ";
        if ($row['cp_sale_type'] == '0') {
            if ($row['cp_sale_amt_max'] > 0)
                $cp_sale_amt_max = "&nbsp;(최대 " . display_price($row['cp_sale_amt_max']) . "까지 할인)";
            else
                $cp_sale_amt_max = "";

            $str .= $row['cp_sale_percent'] . '% 할인' . $cp_sale_amt_max;
        } else {
            $str .= display_price($row['cp_sale_amt']) . ' 할인';
        }
        $str .= "</div>";

        // 최대금액
        if ($row['cp_low_amt'] > 0) {
            $str .= "<div>&#183; " . display_price($row['cp_low_amt']) . " 이상 구매시</div>";
        }

        // 사용가능대상
        $str .= "<div>&#183; " . $gw_usepart[$row['cp_use_part']] . "</div>";

        return $str;
    }

    //  상품 상세페이지 : 배송비
    function mobile_sendcost_amt()
    {
        $gs = $this->ci->load->get_var('gs');
        $config = $this->ci->load->get_var('config');
        $sr = $this->ci->load->get_var('sr');
        $str = "";

        // 공통설정
        if ($gs['sc_type'] == '0') {
            if ($gs['mb_id'] == 'admin') {
                $delivery_method  = $config['delivery_method'];
                $delivery_price      = $config['delivery_price'];
                $delivery_price2  = $config['delivery_price2'];
                $delivery_minimum = $config['delivery_minimum'];
            } else {
                $delivery_method  = $sr['delivery_method'];
                $delivery_price   = $sr['delivery_price'];
                $delivery_price2  = $sr['delivery_price2'];
                $delivery_minimum = $sr['delivery_minimum'];
            }

            switch ($delivery_method) {
                case '1':
                    $str = "무료배송";
                    break;
                case '2':
                    $str = "상품수령시 결제(착불)";
                    break;
                case '3':
                    $str = display_price($delivery_price);
                    break;
                case '4':
                    $str = "무료~" . display_price($delivery_price2) . "&nbsp;(조건부무료)";
                    break;
            }

            // sc_type(배송비 유형)   0:공통설정, 1:무료배송, 2:조건부무료배송, 3:유료배송
            // sc_method(배송비 결제) 0:선불, 1:착불, 2:사용자선택
            if (in_array($delivery_method, array('3', '4'))) {
                if ($gs['sc_method'] == 1)
                    $str = '상품수령시 결제(착불)';
                else if ($gs['sc_method'] == 2) {
                    $str = "<select name=\"ct_send_cost\" style=\"width:100%\">
                                        <option value=\"0\">주문시 결제(선결제)</option>
                                        <option value=\"1\">상품수령시 결제(착불)</option>
                                    </select>";
                }
            }
        } else if ($gs['sc_type'] == '1') {
            $str = "무료배송";
        } else if ($gs['sc_type'] == '2') {
            $str = "무료~" . display_price($gs['sc_amt']) . "&nbsp;(조건부무료)";
        } else if ($gs['sc_type'] == '3') {
            $str = display_price($gs['sc_amt']);
        }

        // sc_type(배송비 유형)		0:공통설정, 1:무료배송, 2:조건부 무료배송, 3:유료배송
        // sc_method(배송비 결제)	0:선불, 1:착불, 2:사용자선택
        if (in_array($gs['sc_type'], array('2', '3'))) {
            if ($gs['sc_method'] == 1)
                $str = '상품수령시 결제(착불)';
            else if ($gs['sc_method'] == 2) {
                $str = "<select name=\"ct_send_cost\" style=\"width:100%\">
                                    <option value=\"0\">주문시 결제(선결제)</option>
                                    <option value=\"1\">상품수령시 결제(착불)</option>
                                </select>";
            }
        }

        return $str;
    }

    // 상품 가격정보의 배열을 리턴
    function mobile_price($gs_id, $msg = '<span>원</span>')
    {
        $member = $this->ci->load->get_var('member');
        $is_member = $this->ci->load->get_var('is_member');

        $gs = $this->ci->global_lib->get_goods($gs_id, 'index_no, price_msg, buy_level, buy_only');

        $price = $this->ci->gvcomplex_lib->get_sale_price($gs_id);

        // 재고가 한정상태이고 재고가 없을때, 품절상태일때..
        if ($this->is_soldout($gs['index_no'])) {
            $str = "<span class=\"soldout\">품절</span>";
        } else {
            if ($gs['price_msg']) {
                $str = $gs['price_msg'];
            } else if ($gs['buy_only'] == 1 && $member['grade'] > $gs['buy_level']) {
                $str = "";
            } else if ($gs['buy_only'] == 0 && $member['grade'] > $gs['buy_level']) {
                if (!$is_member)
                    $str = "<span class=\"memopen\">회원공개</span>";
                else
                    $str = "<span class=\"mpr\">" . number_format($price) . $msg . "</span>";
            } else {
                $str = "<span class=\"mpr\">" . number_format($price) . $msg . "</span>";
            }
        }

        return $str;
    }

    // 상품 상세페이지 : 고객상품평
    function mobile_goods_review($name, $gs_id, $rows = 10)
    {
        $tableTag = "";
        $gw_star = $this->ci->config->item('gw_star');

        $returnData = $this->getReviewData($gs_id);
        $result = $returnData['result'];
        $cnt = count($result);

        $tableTag .= "<div class=sp_vbox_mr>
                                <ul>
                                    <li class='tlst'>$name <span class=cate_dc>($cnt)</span></li>\n
                                </ul>
                            </div>";

        $tableTag .= "<ul class=lst_w>";

        $i = 0;

        foreach ($result as $key => $row) {
            $tmp_date  = substr($row['reg_time'], 0, 10);
            $tmp_score = $gw_star[$row['score']];

            $len = strlen($row['mb_id']);
            $str = substr($row['mb_id'], 0, 3);
            $tmp_name = $str . str_repeat("*", $len - 3);

            $key = "share";
            $referralCode = base64_encode(openssl_encrypt($row['mb_id'], "AES-256-CBC", $key, true, str_repeat(chr(0), 16)));

            $tableTag .= "<li class='lst'>
                                    <div class=\"top_area\">
                                        <span class=lst_post>" . $row['memo'] . "</span>
                                        <div class=\"share\" onclick=\"clip('" . $referralCode . "')\"></div>
                                    </div>
                                    <div class=\"review_img\">";

            foreach (['img1', 'img2', 'img3', 'img4'] as $imgKey) {
                if (!empty($row[$imgKey])) {
                    $media_type = $this->getMediaType($row[$imgKey]); // 사진/동영상 출력

                    if ($media_type == "img") {
                        $tableTag .= "<img src=\"" . base_url() . "data/review/{$row[$imgKey]}\" onclick=\"open_review_modal(" . $row['index_no'] . "," . $gs_id . ")\">";
                    } else {
                        $tableTag .= "<div onclick=\"open_review_modal(" . $row['index_no'] . "," . $gs_id . ")\">
                                                    <video preload=\"metadata\" src=\"" . base_url() . "data/review/{$row[$imgKey]}\"></video>
                                                    <i class='fa fa-play-circle'></i>
                                                </div>";
                    }
                }
            }
            $tableTag .= "    </div>";
            $tableTag .= "    <div class='lst_h'>
                                        <span class='fc_255'>";
            for ($i = 0; $i < (int)$row['score']; $i++) {
                $tableTag .= "    <img src=\"" . base_url() . "dist/img/mb/star.png\" align=\"absmiddle\">";
            }

            $tableTag .= "        <span class='fc_999'> / $tmp_name / $tmp_date
                                    </div>
                                </li>";
            $i++;
        }
        if ($i == 0) {
            $tableTag .= "<li class=lst><span class='lst_a tac'>자료가 없습니다</span></li>\n";
        }

        $tableTag .= "</ul>\n";
        return $tableTag;
    }

    //  상품 상세페이지 : Q&A
    function mobile_goods_qa($name, $cnt, $gs_id)
    {
        $returnStr = "";
        $member = $this->ci->load->get_var('member');

        $returnStr .= "<div class=sp_vbox_qa>\n";
        $returnStr .= " <ul>\n";
        $returnStr .= "  <li class='tlst'>$name <span class=cate_dc>($cnt)</span></li>\n";
        $returnStr .= "  <li class='trst'><a href=\"javascript:window.open('" . base_url() . "m/shop/qaform?gs_id=$gs_id');\" class='btn_lsmall bx-white'>Q&A쓰기</a></li>\n";
        $returnStr .= " </ul>\n";
        $returnStr .= "</div>\n";

        $returnStr .= "<ul class=lst_w>\n";

        $sql = " select * from shop_goods_qa where gs_id=? order by iq_time desc ";
        $data = array($gs_id);
        $result = $this->ci->Query_model->returnArr($sql, $data);
        $i = 0;
        foreach ($result as $key => $row) {
            $iq_time = substr($row['iq_time'], 0, 10);

            $is_secret = false;
            if ($row['iq_secret']) {
                $icon_secret = "<img src='" . base_url() . "img/icon/icon_secret.jpg' class='vam' alt='비밀글'>";

                if ($this->ci->gvcomplex_lib->is_admin() || $member['id'] == $row['mb_id']) {
                    $iq_answer = $row['iq_answer'];
                } else {
                    $iq_answer = "";
                    $is_secret = true;
                }
            } else {
                $icon_secret = "";
                $iq_answer = $row['iq_answer'];
            }

            if ($row['iq_answer'])
                $icon_answer = "<span class='fc_7d6'>답변완료</span>&nbsp;&nbsp;";
            else
                $icon_answer = "<span class='fc_999'>답변대기</span>&nbsp;&nbsp;";

            $iq_subject = "";
            if (!$is_secret) {
                $iq_subject .= "<a href='javascript:void(0);' onclick=\"qna('" . $i . "')\">";
            }
            $iq_subject .= "<span class=lst_post>" . $icon_answer . htmlspecialchars($row['iq_subject']) . "</span>";

            $len = strlen($row['mb_id']);
            $str = substr($row['mb_id'], 0, 3);
            $mb_id = $str . str_repeat("*", $len - 3);

            $hash = md5($row['iq_id'] . $row['iq_time'] . $row['iq_ip']);

            $returnStr .= "<li class='lst'>\n$iq_subject";
            $returnStr .= "<span class='lst_h'><span class='fc_255'>$row[iq_ty]</span> ";
            $returnStr .= "<span class='fc_999'> / $mb_id / $iq_time $icon_secret </span></span>";
            if (!$is_secret) {
                $returnStr .= "</a>";
            }

            $returnStr .= "<div class='faq' id='qna" . $i . "' style='display:none;'>\n";
            $returnStr .= "<table class='faqbody'>\n";
            $returnStr .= "<tbody>\n";
            $returnStr .= "<tr>\n";
            $returnStr .= "<td class='mi_dt'><img src='" . base_url() . "img/sub/FAQ_Q.gif'></td>\n";
            $returnStr .= "<td class='mi_bt fc_125'>\n" . nl2br(htmlspecialchars($row['iq_question']));

            if ($this->ci->gvcomplex_lib->is_admin() || $member['id'] == $row['mb_id'] && !$iq_answer) {
                $returnStr .= "<div class='padt10'><a href=\"javascript:window.open('" . base_url() . "m/shop/qaform?gs_id=$row[gs_id]&iq_id=$row[iq_id]&w=u');\" /><span class='under fc_blk'>수정</span></a>&nbsp;&nbsp;&nbsp;<a href=\"" . base_url() . "process/qaform_update_del?gs_id=$row[gs_id]&iq_id=$row[iq_id]&w=d&hash=$hash\" class='itemqa_delete'><span class='under fc_blk'>삭제</span></a></div>\n";
            }
            $returnStr .= "</td>\n";
            $returnStr .= "</tr>\n";

            if ($iq_answer) {
                $returnStr .= "<tr>\n";
                $returnStr .= "<td class='mi_dt padt20'><img src='" . base_url() . "img/sub/FAQ_A.gif'></td>\n";
                $returnStr .= "<td class='mi_bt padt20 fc_7d6'>" . nl2br(htmlspecialchars($iq_answer)) . "</td>\n";
                $returnStr .= "</tr>\n";
            }
            $returnStr .= "</tbody>\n";
            $returnStr .= "</table>\n";
            $returnStr .= "</div>\n";
            $returnStr .= "</li>\n";
            $i++;
        }
        if ($i == 0) {
            $returnStr .= "<li class=lst><span class='lst_a tac'>자료가 없습니다</span></li>\n";
        }
        $returnStr .= "</ul>\n";
        return $returnStr;
    }

    // 계좌정보를 select 박스 형식으로 얻는다
    function mobile_bank_account($name, $selected = '')
    {
        $default = $this->ci->load->get_var('default');

        $str  = '<select id="' . $name . '" name="' . $name . '" style="width:100%">';
        $str .= '<option value="">선택하십시오</option>';

        $bank = unserialize($default['de_bank_account']);
        if ($bank) {
            $limit = (count($bank) > 5) ? 5 : count($bank);
        } else {
            $limit = 0;
        }
        for ($i = 0; $i < $limit; $i++) {
            $bank_account = $bank[$i]['name'] . ' ' . $bank[$i]['account'] . ' ' . $bank[$i]['holder'];
            if (trim($bank_account)) {
                $str .= option_selected($bank_account, $selected, $bank_account);
            }
        }
        $str .= '</select>';

        return $str;
    }
    // 로고
    function mobile_display_logo($fld = 'mobile_logo')
    {
        $pt_id = $this->ci->load->get_var('pt_id');
        $sql = "select $fld from shop_logo where mb_id=?";
        $data = array($pt_id);
        $row = $this->ci->Query_model->returnOneArr($sql, $data);
        if (!$row[$fld] && $pt_id != 'admin') {
            $row = $this->ci->Query_model->returnOneArr("select $fld from shop_logo where mb_id='admin'");
        }

        $file = './data/banner/' . $row[$fld];
        if (is_file($file) && $row[$fld]) {
            $file = rpc($file);
            $file = base_url() . 'data/banner/' . $row[$fld];
            return '<a href="' . base_url() . 'm/main"><img src="' . $file . '" class="lg_wh"></a>';
        } else {
            return '';
        }
    }

    #naverpay.lib.php에 있던 것
    function get_naverpay_item_image_url($gs_id)
    {
        $default = $this->ci->load->get_var('default');

        $sql = " select index_no, simg1, simg2, simg3, simg4, simg5 from shop_goods where index_no = ?";
        $data = array($gs_id);
        $row = $this->ci->Query_model->returnOneArr($sql, $data);

        if (!$row['index_no'])
            return '';

        $url = '';

        for ($i = 1; $i <= 6; $i++) {
            $it_imageurl = $this->get_it_image_url($row['index_no'], $row['simg' . $i], $default['de_item_medium_wpx'], $default['de_item_medium_hpx']);

            if ($it_imageurl) {
                $url = $it_imageurl;

                if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) {
                    $url = preg_replace('#^https:#', '', $url);

                    $port_str = ':' . $_SERVER['SERVER_PORT'];

                    if (strpos($url, $port_str) !== false) {
                        $url = str_replace($port_str, '', $url);
                    }
                }

                //TLS(SSL/HTTPS) 프로토콜 사용 시 네이버페이/네이버 쇼핑 서버가 해당 경로로 접근하여 데이터를 취득할 수 없으므로, 반드시 http 를 사용해야 함
                $url = (preg_match('#^http:#', $url) ? '' : 'http:') . $url;

                break;
            }
        }

        return $url;
    }

    #partner.lib.php에 있던 함수들  
    // 가맹점 PG결제 정보를 default 변수에 담는다.
    function set_partner_value($mb_id)
    {
        $default = $this->ci->load->get_var('default');

        if (!$this->is_partner($mb_id))
            return $default;

        $pt = $this->ci->partner_lib->get_partner($mb_id);

        $default['de_bank_use'] = $pt['de_bank_use']; // 무통장입금
        $default['de_card_use'] = $pt['de_card_use']; // 신용카드
        $default['de_iche_use'] = $pt['de_iche_use']; // 계좌이체
        $default['de_vbank_use'] = $pt['de_vbank_use']; // 가상계좌
        $default['de_hp_use'] = $pt['de_hp_use']; // 휴대폰
        $default['de_card_test'] = $pt['de_card_test']; // 결제 테스트
        $default['de_pg_service'] = $pt['de_pg_service']; // 결제대행사
        $default['de_tax_flag_use'] = $pt['de_tax_flag_use']; // 복합과세 결제
        $default['de_taxsave_use'] = $pt['de_taxsave_use']; // 현금영수증 발급사용
        $default['de_card_noint_use'] = $pt['de_card_noint_use']; // 신용카드 무이자할부사용
        $default['de_easy_pay_use'] = $pt['de_easy_pay_use']; // PG사 간편결제 버튼사용
        $default['de_escrow_use'] = $pt['de_escrow_use']; // Escrow 사용여부
        $default['de_kcp_mid'] = $pt['de_kcp_mid']; // NHN KCP SITE CODE
        $default['de_kcp_site_key'] = $pt['de_kcp_site_key']; // NHN KCP SITE KEY
        $default['de_lg_mid'] = $pt['de_lg_mid']; // LG유플러스 상점아이디
        $default['de_lg_mert_key'] = $pt['de_lg_mert_key']; // LG유플러스 MertKey
        $default['de_inicis_mid'] = $pt['de_inicis_mid']; // KG이니시스 상점아이디
        $default['de_inicis_admin_key']    = $pt['de_inicis_admin_key']; // KG이니시스 키패스워드
        $default['de_inicis_sign_key'] = $pt['de_inicis_sign_key']; // KG이니시스 웹결제 사인키
        $default['de_samsung_pay_use'] = $pt['de_samsung_pay_use']; // KG이니시스 삼성페이 버튼
        $default['de_bank_account'] = $pt['de_bank_account']; // 무통장입금계좌
        $default['de_kakaopay_admin_key'] = $pt['de_kakaopay_admin_key']; // 카카오페이 Admin키
        $default['de_kakaopay_key'] = $pt['de_kakaopay_cid']; // 카카오페이 CID
        $default['de_naverpay_mid'] = $pt['de_naverpay_mid']; // 네이버페이 가맹점 아이디
        $default['de_naverpay_cert_key'] = $pt['de_naverpay_cert_key']; // 네이버페이 가맹점 인증키
        $default['de_naverpay_button_key'] = $pt['de_naverpay_button_key']; // 네이버페이 버튼 인증키
        $default['de_naverpay_test'] = $pt['de_naverpay_test']; // 네이버페이 결제테스트 아이디
        $default['de_naverpay_mb_id'] = $pt['de_naverpay_mb_id']; // 네이버페이 결제테스트 아이디
        $default['de_naverpay_sendcost'] = $pt['de_naverpay_sendcost']; // 네이버페이 추가배송비 안내

        return $default;
    }

    // 가맹점 설정정보를 default 변수에 담는다.
    function set_default_value($mb_id)
    {
        $default = $this->ci->load->get_var('default');
        $gw_menu = $this->ci->config->item('gw_menu');

        if (!$this->is_partner($mb_id))
            return $default;

        $pt = $this->ci->partner_lib->get_partner($mb_id);

        // 소셜네트워크서비스(SNS : Social Network Service)
        $default['de_sns_login_use'] = $pt['de_sns_login_use'];
        $default['de_naver_appid'] = $pt['de_naver_appid'];
        $default['de_naver_secret'] = $pt['de_naver_secret'];
        $default['de_facebook_appid'] = $pt['de_facebook_appid'];
        $default['de_facebook_secret'] = $pt['de_facebook_secret'];
        $default['de_google_appid'] = $pt['de_google_appid'];
        $default['de_google_secret'] = $pt['de_google_secret'];
        $default['de_kakao_rest_apikey'] = $pt['de_kakao_rest_apikey'];
        $default['de_googl_shorturl_apikey'] = $pt['de_googl_shorturl_apikey'];

        // INSTAGRAM / SNS 연결
        $default['de_insta_url'] = $pt['de_insta_url'];
        $default['de_insta_client_id'] = $pt['de_insta_client_id'];
        $default['de_insta_redirect_uri'] = $pt['de_insta_redirect_uri'];
        $default['de_insta_access_token'] = $pt['de_insta_access_token'];
        $default['de_sns_facebook'] = $pt['de_sns_facebook'];
        $default['de_sns_twitter'] = $pt['de_sns_twitter'];
        $default['de_sns_instagram'] = $pt['de_sns_instagram'];
        $default['de_sns_pinterest'] = $pt['de_sns_pinterest'];
        $default['de_sns_naverblog'] = $pt['de_sns_naverblog'];
        $default['de_sns_naverband'] = $pt['de_sns_naverband'];
        $default['de_sns_kakaotalk'] = $pt['de_sns_kakaotalk'];
        $default['de_sns_kakaostory'] = $pt['de_sns_kakaostory'];

        // 메인 카테고리별 베스트
        if ($pt['de_maintype_title']) $default['de_maintype_title'] = $pt['de_maintype_title'];
        if ($pt['de_maintype_best'])  $default['de_maintype_best']  = $pt['de_maintype_best'];

        // 메뉴 설정
        $pname_run = 0;
        for ($i = 0; $i < count($gw_menu); $i++) {
            $seq = ($i + 1);
            if ($pt['de_pname_' . $seq]) {
                $pname_run++;
            }
        }

        if ($pname_run) {
            for ($i = 0; $i < count($gw_menu); $i++) {
                $seq = ($i + 1);
                $default['de_pname_use_' . $seq] = $pt['de_pname_use_' . $seq];
                $default['de_pname_' . $seq]       = $pt['de_pname_' . $seq];
            }
        }

        return $default;
    }

    // 가맹점 설정정보를 config 변수에 담는다.
    function set_config_value($mb_id)
    {
        $config = $this->ci->load->get_var('config');

        if (!$this->is_partner($mb_id))
            return $config;

        $pt = $this->ci->partner_lib->get_partner($mb_id);

        if ($pt['saupja_yes']) {
            $config['company_type'] = $pt['company_type'];
            $config['shop_name'] = $pt['shop_name'];
            $config['company_name'] = $pt['company_name'];
            $config['company_saupja_no'] = $pt['company_saupja_no'];
            $config['tongsin_no'] = $pt['tongsin_no'];
            $config['company_tel'] = $pt['company_tel'];
            $config['company_fax'] = $pt['company_fax'];
            $config['company_owner'] = $pt['company_owner'];
            $config['company_zip'] = $pt['company_zip'];
            $config['company_addr'] = $pt['company_addr'];
            $config['company_hours'] = $pt['company_hours'];
            $config['company_lunch'] = $pt['company_lunch'];
            $config['company_close'] = $pt['company_close'];
            $config['info_name'] = $pt['info_name'];
            $config['info_email'] = $pt['info_email'];
        }

        $config['meta_author'] = $pt['meta_author'];
        $config['meta_description'] = $pt['meta_description'];
        $config['meta_keywords'] = $pt['meta_keywords'];
        $config['add_meta'] = $pt['add_meta'];
        $config['head_script'] = $pt['head_script'];
        $config['tail_script'] = $pt['tail_script'];

        if ($pt['shop_provision']) $config['shop_provision'] = $pt['shop_provision'];
        if ($pt['shop_private']) $config['shop_private'] = $pt['shop_private'];
        if ($pt['shop_marketing']) $config['shop_marketing'] = $pt['shop_marketing'];
        if ($pt['shop_policy']) $config['shop_policy'] = $pt['shop_policy'];

        return $config;
    }

    // 추천수수료 지급
    function insert_anew_pay($mb_id)
    {
        $config = $this->ci->load->get_var('config');

        // 추천수수료를 사용을 하지 않는다면 리턴
        if (!$config['pf_anew_use']) return;

        // 추천수수료를 적용할 단계가 없다면 리턴
        if (!$config['pf_anew_benefit_dan']) return;

        // 신청자가 가맹점이 아니면 리턴
        if (!$this->is_partner($mb_id)) return;

        // 신청자 정보
        $pt = $this->ci->partner_lib->get_partner($mb_id, 'mb_id, anew_grade, receipt_price');

        // 가맹점개설비가 없다면 리턴
        $pt['receipt_price'] = isset($pt['receipt_price']) ? $pt['receipt_price'] : 0;
        $reg_price = (int)$pt['receipt_price'];
        if ($reg_price == 0) return;

        // 신청자의 추천인을 담고
        $mb = $this->ci->global_lib->get_member($mb_id, 'pt_id');
        $pt_id = $mb['pt_id'];

        // 추천인이 가맹점이 아니면 리턴
        if (!$this->is_partner($pt_id)) return;

        // 추천인은 본인이 될 수 없음
        if ($mb_id == $pt_id) return;

        // 신청레벨에 따른 인센티브를 배열로 담는다
        $anew_benefit = explode(' ', $config['pf_anew_benefit_' . $pt['anew_grade']]);

        for ($i = 0; $i < $config['pf_anew_benefit_dan']; $i++) {
            // 추천인이 없거나 최고관리자라면 중지
            if (!$pt_id || $pt_id == 'admin')
                break;

            // 적용할 인센티브가 없다면 건너뜀
            $benefit = (int)trim($anew_benefit[$i]);
            if ($benefit <= 0) continue;

            if ($config['pf_anew_benefit_type'])
                $pt_pay = $benefit; // 설정금액(원)
            else
                $pt_pay = floor($reg_price * $benefit / 100); // 설정비율(%)

            $this->insert_pay($pt_id, $pt_pay, $mb_id . '님 가맹점가입 축하', 'anew', $mb_id, '추천수수료');

            // 단계별 상위 추천인을 담고 다시 배열로 돌린다
            $mb = $this->ci->global_lib->get_member($pt_id, 'pt_id');
            $pt_id = $mb['pt_id'];
        }
    }

    // 접속수수료 지급
    function insert_visit_pay($mb_id, $remote_addr, $referer, $user_agent)
    {
        $config = $this->ci->load->get_var('config');
        $OM_TIME_YMD = $this->OM_TIME_YMD;
        $OM_IP_DISPLAY = '\\1.♡.\\3.\\4';

        // 접속수수료를 사용을 하지 않는다면 리턴
        if (!$config['pf_visit_use']) return;

        // 가맹점이 아니면 리턴
        if (!$this->is_partner($mb_id)) return;

        // 가맹점 정보
        $mb = $this->ci->global_lib->get_member($mb_id, 'grade');

        // 레벨에 따른 접속수수료 설정값
        $pb = $this->ci->partner_lib->get_partner_basic($mb['grade']);

        // 접속수수료가 없다면 리턴
        $pay = (int)$pb['gb_visit_pay'];

        if ($pay == 0) return;

        $ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", $OM_IP_DISPLAY, $remote_addr);

        $this->insert_pay($mb_id, $pay, "접속자 ({$ip})", "visit", $remote_addr, $OM_TIME_YMD, $referer, $user_agent);
    }

    // 판매수수료 지급
    function insert_sale_pay($pt_id, $od, $gs)
    {
        $config = $this->ci->load->get_var('config');


        // 판매수수료를 사용을 하지 않는다면 리턴
        if (!$config['pf_sale_use']) return;

        // 가맹점상품이면 리턴
        if ($gs['use_aff']) return;

        // 가맹점이 아니면 리턴
        if (!$this->is_partner($pt_id)) return;

        // 가맹점 정보
        $mb = $this->ci->global_lib->get_member($pt_id, 'grade');

        $amount = 0;

        // 원가 계산
        if ($config['pf_sale_flag']) {
            if ($od['supply_price'] > 0) // 공급가
                $amount = $od['goods_price'] - $od['supply_price'];

            if ($config['pf_sale_flag'] == 1)
                $amount = $amount - ($od['coupon_price'] + $od['use_point']); // 할인쿠폰 + 포인트결제
        } else {
            $amount = $od['use_price'] - $od['baesong_price']; // 순수결제액 - 배송비
        }

        // 적용할 금액이 없다면 리턴
        if ($amount < 1) return;

        if ($gs['ppay_type']) { // 개별설정
            $sale_benefit_dan  = $gs['ppay_dan'];
            $sale_benefit_type = $gs['ppay_rate'];
            $sale_benefit       = explode(' ', $gs['ppay_fee']);
        } else { // 공통설정
            $sale_benefit_dan  = $config['pf_sale_benefit_dan'];
            $sale_benefit_type = $config['pf_sale_benefit_type'];
            $sale_benefit       = explode(' ', $config['pf_sale_benefit_' . $mb['grade']]);
        }

        // 판매수수료를 적용할 단계가 없다면 리턴
        if ($sale_benefit_dan < 1) return;

        for ($i = 0; $i < $sale_benefit_dan; $i++) {
            // 추천인이 없거나 최고관리자라면 중지
            if (!$pt_id || $pt_id == 'admin')
                break;

            // 적용할 인센티브가 없다면 건너뜀
            $benefit = (int)trim($sale_benefit[$i]);
            if ($benefit <= 0) continue;

            $pt_pay = 0;

            if ($sale_benefit_type)
                $pt_pay = (int)($benefit * $od['sum_qty']); // 설정금액(원)
            else
                $pt_pay = (int)($amount * $benefit / 100); // 설정비율(%)

            // 추천인 정보
            $mb = $this->ci->global_lib->get_member($pt_id, 'pt_id, payment, payflag');

            // 개별 추가 판매수수료
            if ($mb['payment']) {
                if ($mb['payflag'])
                    $pt_pay += (int)($mb['payment'] * $od['sum_qty']); // 설정금액(원)
                else
                    $pt_pay += (int)($amount * $mb['payment'] / 100); // 설정비율(%)
            }

            // 적용할 수수료가 없다면 건너뜀
            if ($pt_pay <= 0) continue;

            $this->insert_pay($pt_id, $pt_pay, "주문번호 {$od['od_id']} ({$od['od_no']}) 배송완료", 'sale', $od['od_no'], $od['od_id']);

            // 상위 추천인을 담고 다시 배열로 돌린다
            $pt_id = $mb['pt_id'];
        } // for
    }

    // 판매수수료 예상가
    function get_payment($gs_id)
    {
        $config = $this->ci->load->get_var('config');
        $member = $this->ci->load->get_var('member');

        // 판매수수료 노출여부 사용중이아니면 리턴
        if (!$config['pf_payment_yes']) return 0;

        // 가맹점이 아니면 리턴
        if (!$this->is_partner($member['id'])) return 0;

        $gs = $this->ci->global_lib->get_goods($gs_id);

        // 가맹점상품이면 리턴
        if ($gs['use_aff']) return 0;

        // 원가 계산
        if ($config['pf_sale_flag'])
            $amount = $gs['goods_price'] - $gs['supply_price']; // 판매가 - 공급가
        else
            $amount = $gs['goods_price']; // 판매가

        // 적용할 금액이 없다면 리턴
        if ($amount < 1) return 0;

        if ($gs['ppay_type']) { // 개별설정
            $sale_benefit_type = $gs['ppay_rate'];
            $sale_benefit       = explode(' ', $gs['ppay_fee']);
        } else { // 공통설정
            $sale_benefit_type = $config['pf_sale_benefit_type'];
            $sale_benefit      = explode(' ', $config['pf_sale_benefit_' . $member['grade']]);
        }

        $benefit = (int)trim($sale_benefit[0]);

        if ($sale_benefit_type)
            $pt_pay = $benefit; // 설정금액(원)
        else
            $pt_pay = (int)($amount * $benefit / 100); // 설정비율(%)

        // 개별 추가 판매수수료
        if ($member['payment']) {
            if ($member['payflag'])
                $pt_pay += $member['payment']; // 설정금액(원)
            else
                $pt_pay += (int)($amount * $member['payment'] / 100); // 설정비율(%)
        }

        if ($pt_pay < 0)
            $pt_pay = 0;

        return $pt_pay;
    }

    #관리자 페이지에서 쓰이는 함수
    function mb_pg_anchor($mb_id)
    {
        $returnStr = "";
        $returnStr .= "
            <ul class=\"anchor\">
                <li><a href=\"" . base_url() . "admin/pop_memberform?mb_id={$mb_id}\">회원정보수정</a></li>";
        if ($this->ci->gvcomplex_lib->is_seller($mb_id)) {
            $returnStr .= "
                <li><a href=\"" . base_url() . "admin/pop_sellerform?mb_id={$mb_id}\">입점사정보수정</a></li>
                <li><a href=\"" . base_url() . "admin/pop_sellerorder?mb_id={$mb_id}\">입점사판매내역</a></li>";
        }
        $returnStr .= "
                <li><a href=\"" . base_url() . "admin/pop_memberorder?mb_id={$mb_id}\">주문내역</a></li>
                <li><a href=\"" . base_url() . "admin/pop_memberpoint?mb_id={$mb_id}\">포인트</a></li>
            </ul>";
        return $returnStr;
    }

    // 주문관리 공통
    function get_order_list($row, $amount, $baesong_search = '')
    {
        // 결제수단
        $disp_paytype = '';
        if ($row['paymethod']) {
            $disp_paytype = $row['paymethod'];

            if ($row['paymethod'] == '간편결제') {
                switch ($row['od_pg']) {
                    case 'lg':
                        $disp_paytype = 'PAYNOW';
                        break;
                    case 'inicis':
                        $disp_paytype = 'KPAY';
                        break;
                    case 'kcp':
                        $disp_paytype = 'PAYCO';
                        break;
                    default:
                        $disp_paytype = $row['paymethod'];
                        break;
                }
            }
        } else {
            $disp_paytype = '결제수단없음';
        }

        // 포인트결제가 포함되어있나?
        if ($amount['usepoint'] > 0 && $row['paymethod'] != '포인트')
            $disp_paytype .= '<span class="list_point">포인트</span>';

        // 예치금결제가 포함되어있나?
        if ($amount['usemileage'] > 0 && $row['paymethod'] != '예치금')
            $disp_paytype .= '<span class="list_point">예치금</span>';

        // 에스크로 결제인가?
        if ($row['od_escrow'])
            $disp_paytype .= '<span class="list_escrow">에스크로</span>';

        // 테스트 주문인가?
        $disp_test = '';
        if ($row['od_test'])
            $disp_test = '<span class="list_test">테스트</span>';

        // 모바일 주문인가?
        $disp_mobile = '';
        if ($row['od_mobile'])
            $disp_mobile = '(M)';

        // 주문자가 회원인가?
        if ($row['mb_id'])
            $disp_mb_id = '<span class="list_mb_id">(' . $row['mb_id'] . ')</span>';
        else
            $disp_mb_id = '<span class="list_mb_id">(비회원)</span>';

        if (!$row['pt_id'] || $row['pt_id'] == 'admin')
            $disp_pt_id = '본사';
        else {
            $mb = $this->ci->global_lib->get_member($row['pt_id'], 'name');
            $mb_name = $this->ci->funcomplex_lib->get_sideview($row['pt_id'], $mb['name']);
            if (!$mb_name) $mb_name = '정보없음';
            $disp_pt_id = $mb_name . '<span class="list_mb_id">(' . $row['pt_id'] . ')</span>';
        }

        // 거래증빙 요청이있나?
        if ($row['taxbill_yes'] == 'Y')
            $disp_taxbill = "세금계산서 발급요청";
        else if ($row['taxsave_yes'] == 'Y' || $row['taxsave_yes'] == 'S')
            $disp_taxbill = "현금영수증 발급요청";
        else
            $disp_taxbill = '<span class="txt_expired">요청안함</span>';

        // 부분배송이 있는가?
        $disp_baesong = '';
        if ($baesong_search) {
            $sql = " select count(*) as cnt
                        from shop_order
                        where od_id = ?
                            {$baesong_search} ";
            $param = array($row['od_id']);
            $tmp = $this->ci->query_model->returnOneArr($sql);
            if ($tmp['cnt'])
                $disp_baesong = '<span class="list_baesong">부분배송</span>';
        }

        $info = array();
        $info['disp_paytype']     = $disp_paytype;
        $info['disp_test']         = $disp_test;
        $info['disp_mobile']     = $disp_mobile;
        $info['disp_mb_id']         = $disp_mb_id;
        $info['disp_pt_id']         = $disp_pt_id;
        $info['disp_taxbill']     = $disp_taxbill;
        $info['disp_od_name']     = $this->ci->funcomplex_lib->get_sideview($row['mb_id'], $row['name']);
        $info['disp_de_name']     = $this->ci->funcomplex_lib->get_sideview($row['mb_id'], $row['b_name']);
        $info['disp_baesong']     = $disp_baesong;
        $info['disp_price']         = number_format($amount['buyprice']);

        return $info;
    }

    // 주문관리 판매자
    function get_order_seller_id($seller_id)
    {
        if ($seller_id == 'admin') {
            $disp_sr_id = '본사';
        } else if (substr($seller_id, 0, 3) == 'AP-') {
            $sr = $this->ci->global_lib->get_seller_cd($seller_id, 'mb_id, company_name');
            $disp_sr_id = $this->ci->funcomplex_lib->get_sideview($sr['mb_id'], $sr['company_name']);
        } else {
            $disp_sr_id = $this->ci->funcomplex_lib->get_sideview($seller_id, $seller_id);
        }

        return $disp_sr_id;
    }

    // 타임특가(bid) 타이머 pc
    function get_pc_timesaleTag($gs, $is_open, $type, $is_soldout)
    {
        $is_running = false;
        $end_datetime = '';

        // 상품 종료 기간
        if ($is_soldout) {
            $class = ($type == "main") ? "main" : "goods";
            $returnTag = "<div class=\"time " . $class . " wait\">
                                <span class=\"day\">품절</span>
                              </div>";
        }
        if ($is_open == "open") {
            $is_running = true;
            $end_datetime = $gs['eb_date'];
        } else if ($is_open == "wait") {
            $is_running = true;
            $end_datetime = $gs['sb_date'];
        } else {
            $class = ($type == "main") ? "main" : "goods";
            $returnTag = "<div class=\"time " . $class . " wait\">
                                <span class=\"day\">판매종료</span>
                              </div>";
        }

        if ($is_running) {
            // $time_s = "00";

            // $eb_date = mktime($time_h, $time_i, $time_s, $time_m, $time_d, $time_y);
            // $t = getdate($eb_date);

            $data = [
                'end_datetime' => $end_datetime,
                'gs_id' => $gs['index_no'],
                'is_open' => $is_open
            ];

            if ($type == "main") {
                $url = "pc/shop/view/main_time_skin";
            } else if ($type == "normal") {
                $url = "pc/shop/view/time_skin";
            }

            $returnTag = $this->ci->load->view($url, $data, TRUE);
        }

        return $returnTag;
    }

    // 타임특가(bid) 타이머 모바일
    function get_mb_timesaleTag($gs, $is_open, $type, $is_soldout)
    {
        $is_running = false;
        $end_datetime = '';

        // 상품 종료 기간
        if ($is_soldout) {
            $class = ($type == "main") ? "main" : "goods";
            $returnTag = "<div class=\"time " . $class . " wait\">
                                <span class=\"day\">품절</span>
                              </div>";
        }
        if ($is_open == "open") {
            $is_running = true;
            $end_datetime = $gs['eb_date'];
        } else if ($is_open == "wait") {
            $is_running = true;
            $end_datetime = $gs['sb_date'];
        } else {
            $class = ($type == "main") ? "main" : "goods";
            $returnTag = "<div class=\"time " . $class . " wait\">
                                <span class=\"day\">판매종료</span>
                              </div>";
        }

        if ($is_running) {



            $data = [
                'end_datetime' => $end_datetime,
                'gs_id' => $gs['index_no'],
                'is_open' => $is_open
            ];

            if ($type == "main") {
                $url = "mb/template/main_time_skin";
            } else if ($type == "normal") {
                $url = "mb/template/time_skin";
            }

            $returnTag = $this->ci->load->view($url, $data, TRUE);
        }

        return $returnTag;
    }

    /**
     * 리뷰 공유 적립 업데이트
     */
    function insert_review_referral($od)
    {
        $OM_TIME_YMDHIS = date('Y-m-d H:i:s');

        // 리뷰 작성한 회원의 회원 정보 확인
        $referral_mb_id = $od['referral_decode'];
        $member    = $this->ci->global_lib->get_member($referral_mb_id, "grade");
        $grade = $member['grade'];

        // 회원 정보에 맞는 등급에 따라서 포인트 부여
        $sql = "SELECT gb_review_point FROM shop_member_grade WHERE gb_no = ?";
        $param = array($grade);
        $result = $this->ci->query_model->returnOneArr($sql, $param);

        $gb_point = $result['gb_review_point']; // 적립률
        $point = (int) round($od['goods_price'] * ($gb_point * (1 / 100))); // 적립 포인트

        $gs = $this->ci->global_lib->get_goods($od['gs_id'], "gname");

        if ($od['mb_id'] != $referral_mb_id) {
            // 포인트 적립
            $this->ci->funcomplex_lib->insert_point($referral_mb_id, $point, "리뷰 공유 [{$gs['gname']}] 구매확정 적립", "@reviewReferral", $referral_mb_id, "referral_{$od['od_id']}");

            // 리뷰 적립 테이블 저장
            $value = array(
                'od_id' => $od['od_id'],
                'mb_id' => $od['mb_id'],
                'referral_mb_id' => $referral_mb_id,
                'referral_code' => $od['referral_code'],
                'po_point' => $point,
                'regdate' => $OM_TIME_YMDHIS
            );

            $this->ci->query_model->insertObj("shop_review_referral", $value);
        }
    }

    /**
     * 미디어 확장자 타입 확인
     */
    function getMediaType($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION); // 확장자 추출
        $mediaTypes = [
            'img' => ['jpg', 'jpeg', 'png', 'gif'],
            'movie' => ['mp4', 'mov', 'wmv']
        ];

        foreach ($mediaTypes as $type => $extensions) {
            if (in_array(strtolower($ext), $extensions)) {
                return $type === 'img' ? 'img' : 'video';
            }
        }
        return '';
    }

    /**
     * 상품 상세 리뷰 데이터 합치기
     */
    function getReviewData($gs_id)
    {
        $default = $this->ci->load->get_var('default');

        // 복사 상품 여부 확인
        $param = array($gs_id);
        $sql = "SELECT is_copy, copy_id FROM shop_goods WHERE index_no = ?";
        $copy_data = $this->ci->query_model->returnOneArr($sql, $param);
        $is_copy = $copy_data['is_copy'];
        $copy_id = $copy_data['copy_id'];

        // 원본 상품의 경우 복사 상품 index_no 가져오기
        $index_arr = array();
        $sql = "SELECT index_no FROM shop_goods WHERE copy_id = ?";
        $original_data = $this->ci->query_model->returnArr($sql, $param);

        if (!empty($original_data)) {
            foreach ($original_data as $row) {
                array_push($index_arr, $row['index_no']);
            }
        }

        // 총 상품건 구하기
        $sql = "SET @rownum := 0";
        $this->ci->db->query($sql);

        if ($is_copy == 'y') { // 카피 상품
            $param = array($copy_id, $gs_id);
            $sql = "SELECT *, @rownum := @rownum + 1 AS num from shop_goods_review where gs_id = ? or gs_id = ? order by reg_time desc";
        } else if (!empty($original_data)) { // 오리지날 상품
            $param = array($index_arr, $gs_id);
            $sql = "SELECT *, @rownum := @rownum + 1 AS num from shop_goods_review where gs_id in ? or gs_id = ? order by reg_time desc";
        } else {
            $param = array($gs_id);
            $sql = "SELECT *, @rownum := @rownum + 1 AS num from shop_goods_review where gs_id = ? order by reg_time desc";
        }

        if ($default['de_review_wr_use']) {
            $sql .= " and pt_id = '$pt_id' ";
        }

        $result = $this->ci->query_model->returnArr($sql, $param);

        $returnData['is_copy'] = $is_copy;
        $returnData['copy_id'] = $copy_id;
        $returnData['result'] = $result;
        $returnData['original_data'] = $original_data;
        $returnData['index_arr'] = $index_arr;

        return $returnData;
    }
}
