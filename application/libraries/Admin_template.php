<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property CI_Config $config
 * 
 */
class Admin_template
{
    protected $ci;
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('Query_model');

        $this->ci->load->helper('global_helper');

        $this->ci->load->library('gvcomplex_lib');
        $this->ci->load->library('global_lib');
        $this->ci->load->library('funcomplex_lib');
        $this->ci->load->library('common_lib');

        $this->ci->load->config('admin_menu');
        // Do something with $params
    }
    public function admin_access()
    {
        $is_member = $this->ci->load->get_var('is_member');
        $urlencode = $this->ci->load->get_var('urlencode');
        if (!$is_member) {
            goto_url(base_url() . 'bbs/login?url=' . $urlencode);
        }

        // admin 세션 변수에 등록
        $admin_id = get_session('admin_ss_mb_id');
        if ($admin_id) {
            set_session('ss_mb_id', $admin_id);
            $sql = "select * from shop_member where id = ?";
            $param = array($admin_id);
            $member = $this->ci->query_model->returnOneArr($sql, $param);
            // 초기화
            unset($admin_id);
            set_session('admin_ss_mb_id', '');
        }

        if (!$this->ci->gvcomplex_lib->is_admin()) {
            alert('관리자 권한이 없습니다.', base_url());
        }
    }
    public function admin_head($title = "")
    {
        if (!$title)
            $title = '관리자 페이지';
        $favTag = "";
        if ($ico = $this->ci->funcomplex_lib->display_logo_url('favicon_ico')) { // 파비콘
            $favTag = "<link rel=\"shortcut icon\" href=\"{$ico}\" type=\"image/x-icon\">";
        }
        $data = array('title' => $title, 'favTag' => $favTag);
        $this->ci->load->view('admin/template/admin_head', $data);
    }
    public function admin_topmenu($pg_title = "", $notIndex = true, $pg_title2 = "", $snb_icon = "")
    {
        $config = $this->ci->load->get_var('config');
        $super = $this->ci->load->get_var('super');
        $OM_VERSION = '주식회사 더한류';
        $ADMIN_MENU1 = "";
        if ($pg_title == $this->ci->config->item('ADMIN_MENU1')) {
            $ADMIN_MENU1 = "active";
        }

        $ADMIN_MENU3 = "";
        if ($pg_title == $this->ci->config->item('ADMIN_MENU3')) {
            $ADMIN_MENU3 = "active";
        }
        $ADMIN_MENU4 = "";
        if ($pg_title == $this->ci->config->item('ADMIN_MENU4')) {
            $ADMIN_MENU4 = "active";
        }
        $ADMIN_MENU5 = "";
        if ($pg_title == $this->ci->config->item('ADMIN_MENU5')) {
            $ADMIN_MENU5 = "active";
        }
        $ADMIN_MENU5 = "";
        if ($pg_title == $this->ci->config->item('ADMIN_MENU5')) {
            $ADMIN_MENU5 = "active";
        }
        $ADMIN_MENU6 = "";
        if ($pg_title == $this->ci->config->item('ADMIN_MENU6')) {
            $ADMIN_MENU6 = "active";
        }
        $ADMIN_MENU7 = "";
        if ($pg_title == $this->ci->config->item('ADMIN_MENU7')) {
            $ADMIN_MENU7 = "active";
        }
        $ADMIN_MENU8 = "";
        if ($pg_title == $this->ci->config->item('ADMIN_MENU8')) {
            $ADMIN_MENU8 = "active";
        }
        $ADMIN_MENU9 = "";
        if ($pg_title == $this->ci->config->item('ADMIN_MENU9')) {
            $ADMIN_MENU9 = "active";
        }
        $ADMIN_MENU10 = "";
        if ($pg_title == $this->ci->config->item('ADMIN_MENU10')) {
            $ADMIN_MENU10 = "active";
        }
        $ADMIN_MENU_Text = array(
            'ADMIN_MENU1' => $this->ci->config->item('ADMIN_MENU1'),
            'ADMIN_MENU3' => $this->ci->config->item('ADMIN_MENU3'),
            'ADMIN_MENU4' => $this->ci->config->item('ADMIN_MENU4'),
            'ADMIN_MENU5' => $this->ci->config->item('ADMIN_MENU5'),
            'ADMIN_MENU6' => $this->ci->config->item('ADMIN_MENU6'),
            'ADMIN_MENU7' => $this->ci->config->item('ADMIN_MENU7'),
            'ADMIN_MENU8' => $this->ci->config->item('ADMIN_MENU8'),
            'ADMIN_MENU9' => $this->ci->config->item('ADMIN_MENU9'),
            'ADMIN_MENU10' => $this->ci->config->item('ADMIN_MENU10')
        );
        $adminSnb = "";
        $content = "";
        if (!$notIndex) {
            $adminSnb = $this->admin_snbTag($snb_icon, $pg_title, $pg_title2);
            $content = "
            <div id=\"content\">
                <div class=\"breadcrumb\">
                    <span>HOME</span> <i class=\"ionicons ion-ios-arrow-right\"></i> {$pg_title} <i class=\"ionicons ion-ios-arrow-right\"></i> {$pg_title2}
                </div>";
        }
        $data = array(
            'config' => $config,
            'super' => $super,
            'OM_VERSION' => $OM_VERSION,
            'ADMIN_MENU1' => $ADMIN_MENU1,
            'ADMIN_MENU3' => $ADMIN_MENU3,
            'ADMIN_MENU4' => $ADMIN_MENU4,
            'ADMIN_MENU5' => $ADMIN_MENU5,
            'ADMIN_MENU6' => $ADMIN_MENU6,
            'ADMIN_MENU7' => $ADMIN_MENU7,
            'ADMIN_MENU8' => $ADMIN_MENU8,
            'ADMIN_MENU9' => $ADMIN_MENU9,
            'ADMIN_MENU10' => $ADMIN_MENU10,
            'ADMIN_MENU_Text' => $ADMIN_MENU_Text,
            'adminSnb' => $adminSnb,
            'content' => $content
        );
        $this->ci->load->view('admin/template/admin_topmenu', $data);
    }
    public function admin_footer($notIndex = false)
    {
        $config = $this->ci->load->get_var('config');
        $appendTag = "";
        if (!$notIndex) {
            $appendTag = '</div>';
        }
        $data = array('appendTag' => $appendTag, 'config' => $config);
        $this->ci->load->view('admin/template/admin_footer', $data);
        $this->admin_footer_sub();
    }
    public function admin_footer_sub()
    {
        $newin = $this->admin_footer_subTag(true);
        $data = array('newin' => $newin);
        $this->ci->load->view('admin/template/footerSub', $data);
    }
    private function admin_footer_subTag($newin = false)
    {
        $returnStr = "";
        if (!$newin) {
            $returnStr = "<div id=\"anc_header\"><a href=\"#anc_hd\"><span></span>TOP</a></div>";
        }
        return $returnStr;
    }
    private function admin_snbTag($snb_icon, $pg_title, $pg_title2)
    {
        $config = $this->ci->load->get_var('config');
        $member = $this->ci->load->get_var('member');
        $ADMIN_MENU1 = $this->ci->config->item('ADMIN_MENU1');
        $ADMIN_MENU1_01 = $this->ci->config->item('ADMIN_MENU1_01');
        $ADMIN_MENU1_02 = $this->ci->config->item('ADMIN_MENU1_02');
        $ADMIN_MENU1_03 = $this->ci->config->item('ADMIN_MENU1_03');
        $ADMIN_MENU1_04 = $this->ci->config->item('ADMIN_MENU1_04');
        $ADMIN_MENU1_10 = $this->ci->config->item('ADMIN_MENU1_10');
        $ADMIN_MENU1_07 = $this->ci->config->item('ADMIN_MENU1_07');
        $ADMIN_MENU1_11 = $this->ci->config->item('ADMIN_MENU1_11');
        $ADMIN_MENU1_12 = $this->ci->config->item('ADMIN_MENU1_12');
        $ADMIN_MENU1_13 = $this->ci->config->item('ADMIN_MENU1_13');
        // $ADMIN_MENU1_08=$this->ci->config->item('ADMIN_MENU1_08');
        // $ADMIN_MENU1_09=$this->ci->config->item('ADMIN_MENU1_09');
        $ADMIN_MENU1_05 = $this->ci->config->item('ADMIN_MENU1_05');
        $ADMIN_MENU1_06 = $this->ci->config->item('ADMIN_MENU1_06');

        $ADMIN_MENU3 = $this->ci->config->item('ADMIN_MENU3');
        $ADMIN_MENU3_01 = $this->ci->config->item('ADMIN_MENU3_01');
        $ADMIN_MENU3_02 = $this->ci->config->item('ADMIN_MENU3_02');
        // $ADMIN_MENU3_03=$this->ci->config->item('ADMIN_MENU3_03'); // 입점사 일괄등록
        // $ADMIN_MENU3_06=$this->ci->config->item('ADMIN_MENU3_06'); // 입점사 일괄메일발송
        $ADMIN_MENU3_04 = $this->ci->config->item('ADMIN_MENU3_04');
        $ADMIN_MENU3_05 = $this->ci->config->item('ADMIN_MENU3_05');

        $ADMIN_MENU4 = $this->ci->config->item('ADMIN_MENU4');
        $ADMIN_MENU4_01 = $this->ci->config->item('ADMIN_MENU4_01');
        $ADMIN_MENU4_02 = $this->ci->config->item('ADMIN_MENU4_02');
        $ADMIN_MENU4_03 = $this->ci->config->item('ADMIN_MENU4_03');

        $ADMIN_MENU5 = $this->ci->config->item('ADMIN_MENU5');
        $ADMIN_MENU5_01 = $this->ci->config->item('ADMIN_MENU5_01');
        $ADMIN_MENU5_18 = $this->ci->config->item('ADMIN_MENU5_18');
        $ADMIN_MENU5_24 = $this->ci->config->item('ADMIN_MENU5_24');
        $ADMIN_MENU5_02 = $this->ci->config->item('ADMIN_MENU5_02');
        //$ADMIN_MENU5_27=$this->ci->config->item('ADMIN_MENU5_27'); //카테고리별 인기상품 관리
        // $ADMIN_MENU5_03=$this->ci->config->item('ADMIN_MENU5_03'); // 브랜드 관리
        $ADMIN_MENU5_04 = $this->ci->config->item('ADMIN_MENU5_04');
        $ADMIN_MENU5_06 = $this->ci->config->item('ADMIN_MENU5_06');
        $ADMIN_MENU5_07 = $this->ci->config->item('ADMIN_MENU5_07');
        $ADMIN_MENU5_08 = $this->ci->config->item('ADMIN_MENU5_08');
        $ADMIN_MENU5_09 = $this->ci->config->item('ADMIN_MENU5_09');
        $ADMIN_MENU5_10 = $this->ci->config->item('ADMIN_MENU5_10');
        // $ADMIN_MENU5_11=$this->ci->config->item('ADMIN_MENU5_11');
        $ADMIN_MENU5_12 = $this->ci->config->item('ADMIN_MENU5_12');
        // $ADMIN_MENU5_13=$this->ci->config->item('ADMIN_MENU5_13');
        // $ADMIN_MENU5_14=$this->ci->config->item('ADMIN_MENU5_14');
        // $ADMIN_MENU5_15=$this->ci->config->item('ADMIN_MENU5_15');
        // $ADMIN_MENU5_16=$this->ci->config->item('ADMIN_MENU5_16');
        // $ADMIN_MENU5_17=$this->ci->config->item('ADMIN_MENU5_17');
        $ADMIN_MENU5_20 = $this->ci->config->item('ADMIN_MENU5_20');
        $ADMIN_MENU5_21 = $this->ci->config->item('ADMIN_MENU5_21');
        // $ADMIN_MENU5_22=$this->ci->config->item('ADMIN_MENU5_22');
        // $ADMIN_MENU5_23=$this->ci->config->item('ADMIN_MENU5_23');
        $ADMIN_MENU5_05 = $this->ci->config->item('ADMIN_MENU5_05');
        $ADMIN_MENU5_25 = $this->ci->config->item('ADMIN_MENU5_25');
        $ADMIN_MENU5_26 = $this->ci->config->item('ADMIN_MENU5_26');
        $ADMIN_MENU5_28 = $this->ci->config->item('ADMIN_MENU5_28');

        $ADMIN_MENU6 = $this->ci->config->item('ADMIN_MENU6');
        $ADMIN_MENU6_01 = $this->ci->config->item('ADMIN_MENU6_01');
        $ADMIN_MENU6_02 = $this->ci->config->item('ADMIN_MENU6_02');
        $ADMIN_MENU6_03 = $this->ci->config->item('ADMIN_MENU6_03');
        $ADMIN_MENU6_04 = $this->ci->config->item('ADMIN_MENU6_04');
        $ADMIN_MENU6_05 = $this->ci->config->item('ADMIN_MENU6_05');
        $ADMIN_MENU6_06 = $this->ci->config->item('ADMIN_MENU6_06');
        $ADMIN_MENU6_07 = $this->ci->config->item('ADMIN_MENU6_07');
        $ADMIN_MENU6_08 = $this->ci->config->item('ADMIN_MENU6_08');
        $ADMIN_MENU6_09 = $this->ci->config->item('ADMIN_MENU6_09');
        $ADMIN_MENU6_10 = $this->ci->config->item('ADMIN_MENU6_10');
        $ADMIN_MENU6_11 = $this->ci->config->item('ADMIN_MENU6_11');
        $ADMIN_MENU6_12 = $this->ci->config->item('ADMIN_MENU6_12');
        // $ADMIN_MENU6_13=$this->ci->config->item('ADMIN_MENU6_13');

        $ADMIN_MENU7 = $this->ci->config->item('ADMIN_MENU7');
        $ADMIN_MENU7_01 = $this->ci->config->item('ADMIN_MENU7_01');
        $ADMIN_MENU7_02 = $this->ci->config->item('ADMIN_MENU7_02');
        $ADMIN_MENU7_03 = $this->ci->config->item('ADMIN_MENU7_03');
        $ADMIN_MENU7_04 = $this->ci->config->item('ADMIN_MENU7_04');
        $ADMIN_MENU7_05 = $this->ci->config->item('ADMIN_MENU7_05');
        $ADMIN_MENU7_06 = $this->ci->config->item('ADMIN_MENU7_06');
        $ADMIN_MENU7_07 = $this->ci->config->item('ADMIN_MENU7_07');
        $ADMIN_MENU7_08 = $this->ci->config->item('ADMIN_MENU7_08');
        $ADMIN_MENU7_09 = $this->ci->config->item('ADMIN_MENU7_09');
        $ADMIN_MENU7_10 = $this->ci->config->item('ADMIN_MENU7_10');
        $ADMIN_MENU7_11 = $this->ci->config->item('ADMIN_MENU7_11');
        $ADMIN_MENU7_12 = $this->ci->config->item('ADMIN_MENU7_12');
        $ADMIN_MENU7_13 = $this->ci->config->item('ADMIN_MENU7_13');
        $ADMIN_MENU7_14 = $this->ci->config->item('ADMIN_MENU7_14');
        $ADMIN_MENU7_15 = $this->ci->config->item('ADMIN_MENU7_15');

        $ADMIN_MENU8 = $this->ci->config->item('ADMIN_MENU8');
        $ADMIN_MENU8_01 = $this->ci->config->item('ADMIN_MENU8_01');
        $ADMIN_MENU8_02 = $this->ci->config->item('ADMIN_MENU8_02');
        $ADMIN_MENU8_03 = $this->ci->config->item('ADMIN_MENU8_03');
        $ADMIN_MENU8_04 = $this->ci->config->item('ADMIN_MENU8_04');

        $ADMIN_MENU9 = $this->ci->config->item('ADMIN_MENU9');
        $ADMIN_MENU9_01 = $this->ci->config->item('ADMIN_MENU9_01');
        $ADMIN_MENU9_02 = $this->ci->config->item('ADMIN_MENU9_02');
        $ADMIN_MENU9_03 = $this->ci->config->item('ADMIN_MENU9_03');
        $ADMIN_MENU9_04 = $this->ci->config->item('ADMIN_MENU9_04');
        $ADMIN_MENU9_05 = $this->ci->config->item('ADMIN_MENU9_05');
        // $ADMIN_MENU9_06=$this->ci->config->item('ADMIN_MENU9_06');
        $ADMIN_MENU9_07 = $this->ci->config->item('ADMIN_MENU9_07');

        $ADMIN_MENU10 = $this->ci->config->item('ADMIN_MENU10');
        $ADMIN_MENU10_01 = $this->ci->config->item('ADMIN_MENU10_01');
        $ADMIN_MENU10_02 = $this->ci->config->item('ADMIN_MENU10_02');
        $ADMIN_MENU10_03 = $this->ci->config->item('ADMIN_MENU10_03');
        $ADMIN_MENU10_04 = $this->ci->config->item('ADMIN_MENU10_04');
        $ADMIN_MENU10_05 = $this->ci->config->item('ADMIN_MENU10_05');
        $ADMIN_MENU10_06 = $this->ci->config->item('ADMIN_MENU10_06');
        $ADMIN_MENU10_07 = $this->ci->config->item('ADMIN_MENU10_07');
        $ADMIN_MENU10_08 = $this->ci->config->item('ADMIN_MENU10_08');
        $ADMIN_MENU10_09 = $this->ci->config->item('ADMIN_MENU10_09');
        // $ADMIN_MENU10_10=$this->ci->config->item('ADMIN_MENU10_10');
        // $ADMIN_MENU10_11=$this->ci->config->item('ADMIN_MENU10_11');
        $ADMIN_MENU10_12 = $this->ci->config->item('ADMIN_MENU10_12');
        $ADMIN_MENU10_13 = $this->ci->config->item('ADMIN_MENU10_13');
        $ADMIN_MENU10_14 = $this->ci->config->item('ADMIN_MENU10_14');
        $ADMIN_MENU10_15 = $this->ci->config->item('ADMIN_MENU10_15');
        $ADMIN_MENU10_16 = $this->ci->config->item('ADMIN_MENU10_16');
        $ADMIN_MENU10_17 = $this->ci->config->item('ADMIN_MENU10_17');

        $subNavTag = "";
        $subNavTag .= "<dl>";

        if ($pg_title == $ADMIN_MENU1) {
            $subNavTag .= $this->printMenu1('m10', '회원관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'm10', $ADMIN_MENU1_01, base_url() . 'admin/member?code=list');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'm10', $ADMIN_MENU1_02, base_url() . 'admin/member?code=level_form');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'm10', $ADMIN_MENU1_03, base_url() . 'admin/member?code=register_form');
            if ($member['grade'] == "99") {
                $subNavTag .= $this->printMenu2_1($pg_title2, 'm10', $ADMIN_MENU1_04, base_url() . 'admin/member?code=xls');
            }
            $subNavTag .= $this->printMenu2_1($pg_title2, 'm10', $ADMIN_MENU1_10, base_url() . 'admin/member?code=mail_list');
            $subNavTag .= $this->printMenu1('m20', '포인트관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'm20', $ADMIN_MENU1_07, base_url() . 'admin/member?code=point');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'm20', $ADMIN_MENU1_11, base_url() . 'admin/member?code=mileage');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'m20', $ADMIN_MENU1_08, base_url().'admin/member?code=pointxls');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'m20', $ADMIN_MENU1_09, base_url().'admin/member?code=point_select_form');
            $subNavTag .= $this->printMenu1('m30', '쿠폰관리');
            if ($member['grade'] == "99") {
                $subNavTag .= $this->printMenu2_1($pg_title2, 'm30', $ADMIN_MENU1_12, base_url() . 'admin/member?');
            }
            $subNavTag .= $this->printMenu2_1($pg_title2, 'm30', $ADMIN_MENU1_13, base_url() . 'admin/member?code=coupon');
            $subNavTag .= $this->printMenu1('m40', '가입통계');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'm40', $ADMIN_MENU1_05, base_url() . 'admin/member?code=month');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'm40', $ADMIN_MENU1_06, base_url() . 'admin/member?code=day');
        } else if ($pg_title == $ADMIN_MENU3) {
            $anewCnt = $this->ci->common_lib->admRequest("shop_seller");
            $subNavTag .= $this->printMenu1('s10', '입점사 관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 's10', $ADMIN_MENU3_01, base_url() . 'admin/seller?code=list', $anewCnt);
            $subNavTag .= $this->printMenu2_1($pg_title2, 's10', $ADMIN_MENU3_02, base_url() . 'admin/seller?code=register');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'s10', $ADMIN_MENU3_03, base_url().'admin/seller?code=xls');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'s10', $ADMIN_MENU3_06, base_url().'admin/seller?code=mail_select_form');
            $subNavTag .= $this->printMenu1('s20', '입점사 정산');
            $subNavTag .= $this->printMenu2_1($pg_title2, 's20', $ADMIN_MENU3_04, base_url() . 'admin/seller?code=pay');
            $subNavTag .= $this->printMenu2_1($pg_title2, 's20', $ADMIN_MENU3_05, base_url() . 'admin/seller?code=pay_history');
        } else if ($pg_title == $ADMIN_MENU4) {
            $subNavTag .= $this->printMenu1('c10', '카테고리 관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'c10', $ADMIN_MENU4_01, base_url() . 'admin/category?code=list');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'c10', $ADMIN_MENU4_02, base_url() . 'admin/category?code=view');
            $subNavTag .= $this->printMenu1('c20', '메뉴 관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'c10', $ADMIN_MENU4_03, base_url() . 'admin/category?code=menu_list');
        } else if ($pg_title == $ADMIN_MENU5) {
            $goods1 = $this->ci->common_lib->admRequest("shop_goods1", "and use_aff = 0");
            $goods2 = $this->ci->common_lib->admRequest("shop_goods2", "and use_aff = 0");
            $qaCnt  = $this->ci->common_lib->admRequest("shop_goods_qa", "and (left(seller_id,3)='AP-' or seller_id = 'admin')");
            $jaego1 = $this->ci->common_lib->admin_gs_jaego_bujog("and shop_state = 0 and use_aff = 0"); // 재고부족
            $jaego2 = $this->ci->common_lib->admin_io_jaego_bujog("and b.shop_state = 0 and b.use_aff = 0"); // 옵션재고부족
            $subNavTag .= $this->printMenu1('g10', '상품관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'g10', $ADMIN_MENU5_01, base_url() . 'admin/goods?code=list');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'g70', $ADMIN_MENU5_28, base_url() . 'admin/goods?code=timesale');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'g10', $ADMIN_MENU5_18, base_url() . 'admin/goods?code=supply', $goods1);
            $subNavTag .= $this->printMenu2_1($pg_title2, 'g10', $ADMIN_MENU5_24, base_url() . 'admin/goods?code=supply2', $goods2);
            // $subNavTag.=$this->printMenu2_1($pg_title2,'g10', $ADMIN_MENU5_02, base_url().'admin/goods?code=type'); 
            // $subNavTag.=$this->printMenu2_1($pg_title2,'g10', $ADMIN_MENU5_27, base_url().'admin/goods?code=best_item'); 
            // $subNavTag.=$this->printMenu2_1($pg_title2,'g10', $ADMIN_MENU5_03, base_url().'admin/goods?code=brand_list'); 
            $subNavTag .= $this->printMenu2_1($pg_title2, 'g10', $ADMIN_MENU5_04, base_url() . 'admin/goods?code=plan');
            $subNavTag .= $this->printMenu1('g20', '재고관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'g20', $ADMIN_MENU5_06, base_url() . 'admin/goods?code=stock', $jaego1);
            $subNavTag .= $this->printMenu2_1($pg_title2, 'g20', $ADMIN_MENU5_07, base_url() . 'admin/goods?code=optstock', $jaego2);
            // $subNavTag.=$this->printMenu1('g30', '일괄처리');
            if ($member['grade'] == "99") {
                $subNavTag .= $this->printMenu2_1($pg_title2, 'g30', $ADMIN_MENU5_08, base_url() . 'admin/goods?code=xls_reg');
                $subNavTag .= $this->printMenu2_1($pg_title2, 'g30', $ADMIN_MENU5_09, base_url() . 'admin/goods?code=xls_option_reg');
                $subNavTag .= $this->printMenu2_1($pg_title2, 'g30', $ADMIN_MENU5_10, base_url() . 'admin/goods?code=xls_mod');
                $subNavTag .= $this->printMenu2_1($pg_title2, 'g30', $ADMIN_MENU5_12, base_url() . 'admin/goods?code=getpoint');
            }
            // $subNavTag.=$this->printMenu2_1($pg_title2,'g30', $ADMIN_MENU5_11, base_url().'admin/goods?code=getprice');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'g30', $ADMIN_MENU5_13, base_url().'admin/goods?code=getuse');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'g30', $ADMIN_MENU5_14, base_url().'admin/goods?code=getmove');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'g30', $ADMIN_MENU5_15, base_url().'admin/goods?code=getbrand');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'g30', $ADMIN_MENU5_16, base_url().'admin/goods?code=getdelivery');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'g30', $ADMIN_MENU5_17, base_url().'admin/goods?code=getbuylevel');
            $subNavTag .= $this->printMenu1('g40', '문의 / 후기');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'g40', $ADMIN_MENU5_20, base_url() . 'admin/goods?code=qa', $qaCnt);
            $subNavTag .= $this->printMenu2_1($pg_title2, 'g40', $ADMIN_MENU5_21, base_url() . 'admin/goods?code=review');
            $subNavTag .= $this->printMenu1('g60', '기타관리');
            if ($member['grade'] == "99") {
                $subNavTag .= $this->printMenu2_1($pg_title2, 'g60', $ADMIN_MENU5_05, base_url() . 'admin/goods?code=price');
            }
            $subNavTag .= $this->printMenu2_1($pg_title2, 'g60', $ADMIN_MENU5_25, base_url() . 'admin/goods?code=popular_list');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'g60', $ADMIN_MENU5_26, base_url() . 'admin/goods?code=popular_rank');
            // $subNavTag.=$this->printMenu1('g70', '함께장터 관리');


        } else if ($pg_title == $ADMIN_MENU6) {
            $sodrr = $this->ci->common_lib->admin_order_status_sum("where dan > 0 "); // 총 주문내역
            $sodr1 = $this->ci->common_lib->admin_order_status_sum("where dan = 1 "); // 총 입금대기
            $sodr2 = $this->ci->common_lib->admin_order_status_sum("where dan = 2 "); // 총 입금완료
            $sodr3 = $this->ci->common_lib->admin_order_status_sum("where dan = 3 "); // 총 배송준비
            $sodr4 = $this->ci->common_lib->admin_order_status_sum("where dan = 4 "); // 총 배송중
            $sodr5 = $this->ci->common_lib->admin_order_status_sum("where dan = 5 "); // 총 배송완료
            $sodr6 = $this->ci->common_lib->admin_order_status_sum("where dan = 6 "); // 총 입금전 취소
            $sodr7 = $this->ci->common_lib->admin_order_status_sum("where dan = 7 "); // 총 배송후 반품
            $sodr8 = $this->ci->common_lib->admin_order_status_sum("where dan = 8 "); // 총 배송후 교환
            $sodr9 = $this->ci->common_lib->admin_order_status_sum("where dan = 9 "); // 총 배송전 환불
            $memoCnt = $this->ci->common_lib->admin_order_memo(); // 총 관리자메모
            $subNavTag .= $this->printMenu1('o10', '주문관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'o10', $ADMIN_MENU6_01, base_url() . 'admin/order?code=list', $sodrr['cnt']);
            $subNavTag .= $this->printMenu2_1($pg_title2, 'o10', $ADMIN_MENU6_02, base_url() . 'admin/order?code=1', $sodr1['cnt']);
            $subNavTag .= $this->printMenu2_1($pg_title2, 'o10', $ADMIN_MENU6_03, base_url() . 'admin/order?code=2', $sodr2['cnt']);
            $subNavTag .= $this->printMenu2_1($pg_title2, 'o10', $ADMIN_MENU6_04, base_url() . 'admin/order?code=3', $sodr3['cnt']);
            $subNavTag .= $this->printMenu2_1($pg_title2, 'o10', $ADMIN_MENU6_05, base_url() . 'admin/order?code=4', $sodr4['cnt']);
            $subNavTag .= $this->printMenu2_1($pg_title2, 'o10', $ADMIN_MENU6_06, base_url() . 'admin/order?code=5', $sodr5['cnt']);
            if ($member['grade'] == "99") {
                $subNavTag .= $this->printMenu2_1($pg_title2, 'o10', $ADMIN_MENU6_07, base_url() . 'admin/order?code=delivery');
            }
            $subNavTag .= $this->printMenu1('o20', '취소/교환/반품/환불 관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'o20', $ADMIN_MENU6_08, base_url() . 'admin/order?code=6', $sodr6['cnt']);
            $subNavTag .= $this->printMenu2_1($pg_title2, 'o20', $ADMIN_MENU6_09, base_url() . 'admin/order?code=9', $sodr9['cnt']);
            $subNavTag .= $this->printMenu2_1($pg_title2, 'o20', $ADMIN_MENU6_10, base_url() . 'admin/order?code=7', $sodr7['cnt']);
            $subNavTag .= $this->printMenu2_1($pg_title2, 'o20', $ADMIN_MENU6_11, base_url() . 'admin/order?code=8', $sodr8['cnt']);
            $subNavTag .= $this->printMenu1('o30', '기타관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'o30', $ADMIN_MENU6_12, base_url() . 'admin/order?code=memo', $memoCnt);
            // $subNavTag.=$this->printMenu1('o40', '함께장터');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'o40', $ADMIN_MENU6_13, base_url().'admin/order?code=timesale_state_list');
        } else if ($pg_title == $ADMIN_MENU7) {
            $subNavTag .= $this->printMenu1('v10', '접속자통계');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v10', $ADMIN_MENU7_01, base_url() . 'admin/visit?code=hour');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v10', $ADMIN_MENU7_02, base_url() . 'admin/visit?code=date');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v10', $ADMIN_MENU7_03, base_url() . 'admin/visit?code=week');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v10', $ADMIN_MENU7_04, base_url() . 'admin/visit?code=month');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v10', $ADMIN_MENU7_05, base_url() . 'admin/visit?code=year');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v10', $ADMIN_MENU7_06, base_url() . 'admin/visit?code=browser');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v10', $ADMIN_MENU7_07, base_url() . 'admin/visit?code=os');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v10', $ADMIN_MENU7_08, base_url() . 'admin/visit?code=domain');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v10', $ADMIN_MENU7_09, base_url() . 'admin/visit?code=search');
            $subNavTag .= $this->printMenu1('v20', '주문통계');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v20', $ADMIN_MENU7_10, base_url() . 'admin/visit?code=order1');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v20', $ADMIN_MENU7_11, base_url() . 'admin/visit?code=order2');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v20', $ADMIN_MENU7_12, base_url() . 'admin/visit?code=cancel');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v20', $ADMIN_MENU7_13, base_url() . 'admin/visit?code=return');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v20', $ADMIN_MENU7_14, base_url() . 'admin/visit?code=change');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'v20', $ADMIN_MENU7_15, base_url() . 'admin/visit?code=refund');
        } else if ($pg_title == $ADMIN_MENU8) {
            $qaCnt = $this->ci->common_lib->sel_count("shop_qa", "where result_yes = '0'");
            $subNavTag .= $this->printMenu1('h10', '고객지원');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'h10', $ADMIN_MENU8_01, base_url() . 'admin/help?code=qa', $qaCnt);
            $subNavTag .= $this->printMenu2_1($pg_title2, 'h10', $ADMIN_MENU8_02, base_url() . 'admin/help?code=leave');
            $subNavTag .= $this->printMenu1('h20', 'FAQ 관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'h20', $ADMIN_MENU8_03, base_url() . 'admin/help?code=faq_group');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'h20', $ADMIN_MENU8_04, base_url() . 'admin/help?code=faq');
        } else if ($pg_title == $ADMIN_MENU9) {
            $subNavTag .= $this->printMenu1('d10', '배너관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'd10', $ADMIN_MENU9_01, base_url() . 'admin/design?code=banner_list');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'d10', $ADMIN_MENU9_02, base_url().'admin/design?code=mbanner_list');
            $subNavTag .= $this->printMenu1('d20', '로고/페이지관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'd20', $ADMIN_MENU9_03, base_url() . 'admin/design?code=logo');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'd20', $ADMIN_MENU9_04, base_url() . 'admin/design?code=contentlist');
            $subNavTag .= $this->printMenu1('d30', '기타관리');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'d30', $ADMIN_MENU9_05, base_url().'admin/design?code=menu_form');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'d30', $ADMIN_MENU9_06, base_url().'admin/design?code=best_item');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'd30', $ADMIN_MENU9_07, base_url() . 'admin/design?code=popup_list');
        } else if ($pg_title == $ADMIN_MENU10) {
            $subNavTag .= $this->printMenu1('q10', '기본환경설정');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'q10', $ADMIN_MENU10_01, base_url() . 'admin/config?code=default');
            if ($member['grade'] == "99") {
                $subNavTag .= $this->printMenu2_1($pg_title2, 'q10', $ADMIN_MENU10_02, base_url() . 'admin/config?code=meta');
                $subNavTag .= $this->printMenu2_1($pg_title2, 'q10', $ADMIN_MENU10_03, base_url() . 'admin/config?code=sns');
            }
            $subNavTag .= $this->printMenu2_1($pg_title2, 'q10', $ADMIN_MENU10_04, base_url() . 'admin/config?code=register');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'q10', $ADMIN_MENU10_05, base_url() . 'admin/config?code=sendmail_test');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'q10', $ADMIN_MENU10_06, base_url() . 'admin/config?code=sms');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'q10', $ADMIN_MENU10_07, base_url() . 'admin/config?code=supply');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'q10', $ADMIN_MENU10_08, base_url() . 'admin/config?code=super');
            $subNavTag .= $this->printMenu1('q20', '결제관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'q20', $ADMIN_MENU10_09, base_url() . 'admin/config?code=pg');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'q20', $ADMIN_MENU10_10, base_url().'admin/config?code=kakaopay');
            // $subNavTag.=$this->printMenu2_1($pg_title2,'q20', $ADMIN_MENU10_11, base_url().'admin/config?code=naverpay');
            $subNavTag .= $this->printMenu1('q30', '배송관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'q30', $ADMIN_MENU10_12, base_url() . 'admin/config?code=baesong');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'q30', $ADMIN_MENU10_13, base_url() . 'admin/config?code=islandlist');
            $subNavTag .= $this->printMenu1('q40', '보안관리');
            if ($member['grade'] == "99") {
                $subNavTag .= $this->printMenu2_1($pg_title2, 'q40', $ADMIN_MENU10_14, base_url() . 'admin/config?code=nicecheck');
            }
            $subNavTag .= $this->printMenu2_1($pg_title2, 'q40', $ADMIN_MENU10_15, base_url() . 'admin/config?code=ipaccess');
            $subNavTag .= $this->printMenu1('q50', '게시판관리');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'q50', $ADMIN_MENU10_16, base_url() . 'admin/config?code=board_group_list');
            $subNavTag .= $this->printMenu2_1($pg_title2, 'q50', $ADMIN_MENU10_17, base_url() . 'admin/config?code=board_list');
        }
        $subNavTag .= "</dl>";

        $data = array('snb_icon' => $snb_icon, 'pg_title' => $pg_title, 'subNavTag' => $subNavTag);
        return $this->ci->load->view('admin/template/admin_snb', $data, TRUE);
    }
    function printMenu1($svc_class, $subject)
    {
        if (get_cookie("ck_{$svc_class}")) {
            $svc_class .= ' menu_close';
        }

        return '<dt class="' . $svc_class . ' menu_toggle">' . $subject . '</dt>';
    }
    #admin_snb에 있는 printMenu2
    function printMenu2_1($pg_title2, $svc_class, $subject, $url, $menu_cnt = '')
    {
        if (get_cookie("ck_{$svc_class}")) {
            $svc_class .= ' menu_close';
        }
        if ($pg_title2 == $subject)
            $svc_class .= ' active';

        $current_class = '';
        $count_class = '';
        if (is_numeric($menu_cnt)) {
            if ($menu_cnt > 0)
                $current_class = ' class="snb_air"';
            $count_class = '<em' . $current_class . '>' . $menu_cnt . '</em>';
        }

        return '<dd class="' . $svc_class . '"><a href="' . $url . '">' . $subject . $count_class . '</a></dd>';
    }
    #관리자의 마이페이지와 관리자 페이지에 쓸 데이터 픽크
    public function datePicker()
    {
        #$uiCss=$this->ci->gvcomplex_lib->add_stylesheet('<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/themes/base/jquery-ui.css" rel="stylesheet">', 0);
        #$uiStyleCss=$this->ci->gvcomplex_lib->add_stylesheet('<link type="text/css" href="'.base_url().'plugin/jquery-ui/style.css">', 0);
        #$data=array('uiCss'=>$uiCss,'uiStyleCss'=>$uiStyleCss);
        #return $this->ci->load->view('admin/template/datepicker',$data,TRUE);
        $data = array();
        return $this->ci->load->view('admin/template/datepicker', $data, TRUE);
    }

    #goods_options
    public function goods_options($gs)
    {
        $po_run = false;
        $OM_OPTION_ID_FILTER = '/[\'\"\\\'\\\"]/';
        $result = null;
        $opt1 = $this->ci->input->post('opt1');
        $opt2 = $this->ci->input->post('opt2');
        $opt3 = $this->ci->input->post('opt3');
        $w = $this->ci->input->post('w');


        if (isset($gs['index_no'])) {
            $opt_subject = explode(',', $gs['opt_subject']);
            $opt1_subject = $opt_subject[0];
            $opt2_subject = (isset($opt_subject[1]) && ($opt_subject[1] != null)) ? $opt_subject[1] : "";
            $opt3_subject = (isset($opt_subject[2]) && ($opt_subject[2] != null)) ? $opt_subject[3] : "";

            $sql = " select * from shop_goods_option where io_type = '0' and gs_id = ? order by io_no asc ";
            $param = array($gs['index_no']);
            $resultObj = $this->ci->query_model->returnQueryObj($sql, $param);
            $result = $resultObj->result_array();
            if ($resultObj->num_rows())
                $po_run = true;
        } else if (!empty($_POST)) {
            $opt1_subject = preg_replace($OM_OPTION_ID_FILTER, '', trim(stripslashes($this->ci->input->post('opt1_subject'))));
            $opt2_subject = preg_replace($OM_OPTION_ID_FILTER, '', trim(stripslashes($this->ci->input->post('opt2_subject'))));
            $opt3_subject = preg_replace($OM_OPTION_ID_FILTER, '', trim(stripslashes($this->ci->input->post('opt3_subject'))));

            $opt1_val = preg_replace($OM_OPTION_ID_FILTER, '', trim(stripslashes($this->ci->input->post('opt1'))));
            $opt2_val = preg_replace($OM_OPTION_ID_FILTER, '', trim(stripslashes($this->ci->input->post('opt2'))));
            $opt3_val = preg_replace($OM_OPTION_ID_FILTER, '', trim(stripslashes($this->ci->input->post('opt3'))));

            if (!$opt1_subject || !$opt1_val) {
                echo '옵션1과 옵션1 항목을 입력해 주십시오.';
                exit;
            }

            $po_run = true;

            $opt1_count = $opt2_count = $opt3_count = 0;

            if ($opt1_val) {
                $opt1 = explode(',', $opt1_val);
                $opt1_count = count($opt1);
            }

            if ($opt2_val) {
                $opt2 = explode(',', $opt2_val);
                $opt2_count = count($opt2);
            }

            if ($opt3_val) {
                $opt3 = explode(',', $opt3_val);
                $opt3_count = count($opt3);
            }
        }
        $tableOne = "";
        if ($po_run) {
            if ($gs['index_no']) {
                $i = 0;
                foreach ($result as $key => $row) {
                    $opt_id = $row['io_id'];
                    $opt_val = explode(' ', $opt_id);
                    $opt_1 = isset($opt_val[0]) ? $opt_val[0] : "";
                    $opt_2 = isset($opt_val[1]) ? $opt_val[1] : "";
                    $opt_3 = isset($opt_val[2]) ? $opt_val[2] : "";
                    $opt_price = $row['io_price'];
                    $opt_stock_qty = $row['io_stock_qty'];
                    $opt_noti_qty = $row['io_noti_qty'];
                    $opt_use = $row['io_use'];

                    $tmp_opt  = "";
                    $tmp_opt .= $opt_1;
                    if ($opt_2)
                        $tmp_opt .= ' &gt; ' . $opt_2;
                    if ($opt_3)
                        $tmp_opt .= ' &gt; ' . $opt_3;
                    $tableOne .= "
                    <tr>
                        <td class=\"tac\">
                            <input type=\"hidden\" name=\"opt_id[]\" value=\"{$opt_id}\">
                            <input type=\"checkbox\" name=\"opt_chk[]\" value=\"1\">
                        </td>
                        <td class=\"opt-cell\">{$tmp_opt}</td>
                        <td class=\"tac\">
                            <input type=\"text\" name=\"opt_price[]\" value=\"{$opt_price}\" class=\"frm_input wfull\" onkeyup=\"opt_price_chk(this)\">
                        </td>
                        <td class=\"tac\">
                            <input type=\"text\" name=\"opt_stock_qty[]\" value=\"{$opt_stock_qty}\" class=\"frm_input wfull\" onkeyup=\"stock_total()\">
                        </td>
                        <td class=\"tac\">
                            <input type=\"text\" name=\"opt_noti_qty[]\" value=\"{$opt_noti_qty}\" class=\"frm_input wfull\">
                        </td>
                        <td class=\"tac\">
                            <select name=\"opt_use[]\">
                                <option value=\"1\"" . get_selected('1', $opt_use) . ">사용함</option>
                                <option value=\"0\"" . get_selected('0', $opt_use) . ">미사용</option>
                            </select>
                        </td>
                    </tr>";
                    $i++;
                } // for
            } else {
                for ($i = 0; $i < $opt1_count; $i++) {
                    $j = 0;
                    do {
                        $k = 0;
                        do {
                            #문자열인 상태에서 객체로 전환이 필요함
                            if ($opt1 == "") {
                                $opt1 = array();
                            }
                            $opt1[$i] = isset($opt1[$i]) ? $opt1[$i] : "";

                            if ($opt2 == "") {
                                $opt2 = array();
                            }
                            $opt2[$j] = isset($opt2[$j]) ? $opt2[$j] : "";

                            if ($opt3 == "") {
                                $opt3 = array();
                            }
                            $opt3[$k] = isset($opt3[$k]) ? $opt3[$k] : "";

                            $opt_1 = strip_tags(trim($opt1[$i]));

                            $opt_2 = strip_tags(trim($opt2[$j]));
                            $opt_3 = strip_tags(trim($opt3[$k]));


                            $opt_id = $opt_1;
                            if (isset($opt_2) && $opt_2 != "")
                                $opt_id .= ' ' . $opt_2;
                            if (isset($opt_3) && $opt_3 != "")
                                $opt_id .= ' ' . $opt_3;
                            $opt_price = 0;
                            $opt_stock_qty = 0;
                            $opt_noti_qty = 0;
                            $opt_use = 1;

                            // 기존에 설정된 값이 있는지 체크
                            if ($w == 'u') {
                                $sql = " select io_no, io_price, io_stock_qty, io_noti_qty, io_use
                                        from shop_goods_option where gs_id = ? and io_id = ? and io_type = '0' ";
                                $gs_id = $this->ci->input->post('gs_id');
                                $param = array($gs_id, $opt_id);
                                $row = $this->ci->query_model->returnOneArr($sql, $param);
                                if ($row['io_no']) {
                                    $opt_price = (int)$row['io_price'];
                                    $opt_stock_qty = (int)$row['io_stock_qty'];
                                    $opt_noti_qty = (int)$row['io_noti_qty'];
                                    $opt_use = (int)$row['io_use'];
                                }
                            }

                            $tmp_opt  = "";
                            $tmp_opt .= $opt_1;
                            if ($opt_2) $tmp_opt .= ' &gt; ' . $opt_2;
                            if ($opt_3) $tmp_opt .= ' &gt; ' . $opt_3;
                            $tableOne .= "
                            <tr>
                                <td class=\"tac\">
                                    <input type=\"hidden\" name=\"opt_id[]\" value=\"{$opt_id}\">
                                    <input type=\"checkbox\" name=\"opt_chk[]\" value=\"1\">
                                </td>
                                <td class=\"opt1-cell\">{$tmp_opt}</td>
                                <td class=\"tac\">
                                    <input type=\"text\" name=\"opt_price[]\" value=\"{$opt_price}\" class=\"frm_input wfull\" onkeyup=\"opt_price_chk(this)\">
                                </td>
                                <td class=\"tac\">
                                    <input type=\"text\" name=\"opt_stock_qty[]\" value=\"{$opt_stock_qty}\" class=\"frm_input wfull\" onkeyup=\"stock_total()\">
                                </td>
                                <td class=\"tac\">
                                    <input type=\"text\" name=\"opt_noti_qty[]\" value=\"{$opt_noti_qty}\" class=\"frm_input wfull\">
                                </td>
                                <td class=\"tac\">
                                    <select name=\"opt_use[]\">
                                        <option value=\"1\"" . get_selected('1', $opt_use) . ">사용함</option>
                                        <option value=\"0\"" . get_selected('0', $opt_use) . ">미사용</option>
                                    </select>
                                </td>
                            </tr>";
                            $k++;
                        } while ($k < $opt3_count);

                        $j++;
                    } while ($j < $opt2_count);
                } // for
            }
            $data = array('tableOne' => $tableOne);
            $tag = $this->ci->load->view('admin/template/goods_option', $data, TRUE);
            $return = array('tag' => $tag, 'po_run' => $po_run);
            return $return;
        }
    }
    #goods_spl
    public function goods_spl($gs)
    {
        $OM_OPTION_ID_FILTER = '/[\'\"\\\'\\\"]/';
        $result = null;
        $ps_run = false;
        $gs_id = $this->ci->input->post('gs_id');
        $w = $this->ci->input->post('w');
        $subject = $this->ci->input->post('subject');
        $supply = $this->ci->input->post('supply');


        if (isset($gs['index_no'])) {
            $sql = " select *
                    from shop_goods_option
                    where io_type = '1'
                        and gs_id = '{$gs['index_no']}'
                    order by io_no asc ";
            $resultObj = $this->ci->query_model->returnQueryObj($sql);
            $result = $resultObj->result_array();
            if ($resultObj->num_rows())
                $ps_run = true;
        } else if (!empty($_POST)) {
            $subject_count = count($subject);
            $supply_count = count($supply);

            if (!$subject_count || !$supply_count) {
                echo '추가옵션명과 추가옵션항목을 입력해 주십시오.';
                exit;
            }

            $ps_run = true;
        }
        if ($ps_run) {
            $tableTag = "";
            if ($gs['index_no']) {
                $i = 0;
                foreach ($result as $key => $row) {
                    $spl_id = $row['io_id'];
                    $spl_val = explode(' ', $spl_id);
                    $spl_subject = $spl_val[0];
                    $spl = isset($spl_val[1]) ? $spl_val[1] : "";
                    $spl_price = $row['io_price'];
                    $spl_stock_qty = $row['io_stock_qty'];
                    $spl_noti_qty = $row['io_noti_qty'];
                    $spl_use = $row['io_use'];

                    $tableTag .= "
                        <tr>
                            <td class=\"tac\">
                                <input type=\"hidden\" name=\"spl_id[]\" value=\"{$spl_id}\">
                                <input type=\"checkbox\" name=\"spl_chk[]\" value=\"1\">
                            </td>
                            <td class=\"spl-subject-cell\">{$spl_subject}</td>
                            <td class=\"spl-cell\">{$spl}</td>
                            <td class=\"tac\">
                                <input type=\"text\" name=\"spl_price[]\" value=\"{$spl_price}\" class=\"frm_input wfull\">
                            </td>
                            <td class=\"tac\">
                                <input type=\"text\" name=\"spl_stock_qty[]\" value=\"{$spl_stock_qty}\" class=\"frm_input wfull\">
                            </td>
                            <td class=\"tac\">
                                <input type=\"text\" name=\"spl_noti_qty[]\" value=\"{$spl_noti_qty}\" class=\"frm_input wfull\">
                            </td>
                            <td class=\"tac\">
                                <select name=\"spl_use[]\">
                                    <option value=\"1\"" . get_selected('1', $spl_use) . ">사용함</option>
                                    <option value=\"0\"" . get_selected('0', $spl_use) . ">미사용</option>
                                </select>
                            </td>
                        </tr>";
                    $i++;
                } // for
            } else {
                for ($i = 0; $i < $subject_count; $i++) {
                    $spl_subject = preg_replace($OM_OPTION_ID_FILTER, '', trim(stripslashes($subject[$i])));
                    $spl_val = explode(',', preg_replace($OM_OPTION_ID_FILTER, '', trim(stripslashes($supply[$i]))));
                    $spl_count = count($spl_val);

                    for ($j = 0; $j < $spl_count; $j++) {
                        $spl = trim($spl_val[$j]);
                        if ($spl_subject && $spl) {
                            $spl_id = $spl_subject . ' ' . $spl;
                            $spl_price = 0;
                            $spl_stock_qty = 0;
                            $spl_noti_qty = 0;
                            $spl_use = 1;

                            // 기존에 설정된 값이 있는지 체크
                            if ($_POST['w'] == 'u') {
                                $sql = " select io_no, io_price, io_stock_qty, io_noti_qty, io_use
                                            from shop_goods_option
                                            where gs_id = '{$gs_id}'
                                              and io_id = '$spl_id'
                                              and io_type = '1' ";
                                $row = $this->ci->query_model->returnOneArr($sql);
                                if ($row['io_no']) {
                                    $spl_price = (int)$row['io_price'];
                                    $spl_stock_qty = (int)$row['io_stock_qty'];
                                    $spl_noti_qty = (int)$row['io_noti_qty'];
                                    $spl_use = (int)$row['io_use'];
                                }
                            } // w==u
                            $tableTag .= "
                            <tr>
                                <td class=\"tac\">
                                    <input type=\"hidden\" name=\"spl_id[]\" value=\"{$spl_id}\">
                                    <input type=\"checkbox\" name=\"spl_chk[]\" value=\"1\">
                                </td>
                                <td class=\"spl-subject-cell\">{$spl_subject}</td>
                                <td class=\"spl-cell\">{$spl}</td>
                                <td class=\"tac\">
                                    <input type=\"text\" name=\"spl_price[]\" value=\"{$spl_price}\" class=\"frm_input wfull\">
                                </td>
                                <td class=\"tac\">
                                    <input type=\"text\" name=\"spl_stock_qty[]\" value=\"{$spl_stock_qty}\" class=\"frm_input wfull\">
                                </td>
                                <td class=\"tac\">
                                    <input type=\"text\" name=\"spl_noti_qty[]\" value=\"{$spl_noti_qty}\" class=\"frm_input wfull\">
                                </td>
                                <td class=\"tac\">
                                    <select name=\"spl_use[]\">
                                        <option value=\"1\"" . get_selected('1', $spl_use) . ">사용함</option>
                                        <option value=\"0\"" . get_selected('0', $spl_use) . ">미사용</option>
                                    </select>
                                </td>
                            </tr>";
                        } //if
                    } // for2
                } // for
            }
            $data = array('tableTag' => $tableTag);
            $tag = $this->ci->load->view('admin/template/goods_spl', $data, TRUE);
            $return = array('tag' => $tag, 'ps_run' => $ps_run);
            return $return;
        }
    }
    #goods_info
    public function goods_info($gs)
    {
        $item_info = $this->ci->config->item('item_info');
        $gubun = "";
        if (isset($gs['index_no'])) {
            $gubun = $gs['info_gubun'];
        } else {
            $gubun = $this->ci->input->post('gubun') ? $this->ci->input->post('gubun') : 'wear';
        }
        $info_value = null;

        if (isset($gs['info_value']) && $gs['info_value'] != "0")
            $info_value = @unserialize($gs['info_value']);

        $article = (isset($gubun) && $gubun != "") ? $item_info[$gubun]['article'] : "";
        $articleTag = "";
        if ($article) {
            // $el_no : 분류적용, 전체적용을 한번만 넣기 위해, $el_length : 수직병합할 셀 값
            $el_no = 0;
            $el_length = count($article);
            foreach ($article as $key => $value) {
                $el_name    = $key;
                $el_title   = $value[0];
                $el_example = $value[1];
                $el_value = '상품페이지 참고';
                if ($gubun == $gs['info_gubun'] && isset($info_value[$key]))
                    $el_value = $info_value[$key];
                $articleTag .= "
                <tr>
                    <th scope=\"row\">{$el_title}</th>
                    <td>
                        <input type=\"hidden\" name=\"ii_article[]\" value=\"{$el_name}\">
                        <label for=\"ii_article_{$el_name}\" class=\"sound_only\">{$el_title}</label>
                        <input type=\"text\" name=\"ii_value[]\" value=\"{$el_value}\" id=\"ii_article_{$el_name}\" required class=\"frm_input required\" size=\"60\">";
                if ($el_example) {
                    $articleTag .= help($el_example, 1);
                }
                $articleTag .= "
                    </td>
                </tr>";
                $el_no++;
            }
        }

        $data = array('articleTag' => $articleTag);
        return $this->ci->load->view('admin/template/goods_info', $data, TRUE);
    }
}
