<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Summary of Main
 * @property Query_model $query_model
 * @property Gvcomplex_lib $gvcomplex_lib
 * @property Global_lib $global_lib
 * @property Funcomplex_lib $funcomplex_lib
 * @property Template $template
 * @property CI_Input $input
 * @property CI_User_agent $agent
 */
class Main extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('global_helper');
        $this->load->model('query_model');
        $this->load->library('gvcomplex_lib');
        $this->load->library('global_lib');
        $this->load->library('funcomplex_lib');
        $this->load->library('template');
        $device = $this->input->get('device');
        if (isset($device)) {
            $_SESSION['device'] = $device;
        }
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && ($this->agent->is_mobile() || $this->load->get_var('is_mobile'))) {
            redirect('./m/main/');
        }
    }

    public function index()
    {


        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && ($this->agent->is_mobile() || $this->load->get_var('is_mobile'))) {
            redirect('./m/main/');
        }
        $is_member = $this->load->get_var('is_member');
        $config = $this->load->get_var('config');
        if ($is_member && $config['shop_intro_yes']) {
            redirect(base_url() . 'main/intro');
        }
        $index = true;
        $this->template->header($index);

        $this->main();
        $this->template->footer($index);
    }

    //가장처음 나오는 화면 구성
    public function main()
    {
        $pt_id = $this->load->get_var('pt_id');
        $default = $this->load->get_var('default');

        //마감임박 상품 -> eb_date가 얼마 안남은 순으로 출력됨.
        $limited_timesale_active_items = $this->get_timesale_items('active', "limited");

        //함께장터 상품 -> 카테고리 별로 나오게해야함.
        $normal_timesale_active_items = $this->get_timesale_items('active', "normal");

        //지금 가장 인기있는 함께장터 상품 -> 조회수가 가장 높은 상품순으로 나와야함.
        $hottest_timesale_active_items = $this->get_timesale_items('active', "hottest");

        $category_list = $this->get_category_list();

        // bid - 오픈예정
        // $timesale_pending = $this->timesale('pending');

        // 배너
        $mainBan = $this->template->mainSlider();

        // 기획전
        // $plan = $this->plan();

        // 관심도 급상승 // 여기에 7초 성능 저하 걸림 
        // $attention = $this->attention();

        // 미니 배너
        // $miniBan = $this->funcomplex_lib->display_banner(2, $pt_id);

        // 브랜드관
        // $brand = $this->brand();
        $start = microtime(true);
        // 실속관 // 여기에 8초 성능 저하
        // $substance = $this->substance();

        $substance_items = $this->get_substance_items();

        $end = microtime(true);

        $bottom_notice_list = $this->get_bottom_notice_list();

        $bottom_request_goods_list = $this->get_bottom_request_goods_list();

        $data = array(
            'mainBan' => $mainBan,
            'default' => $default,
            'limited_timesale_active_items' => $limited_timesale_active_items,
            'normal_timesale_active_items' => $normal_timesale_active_items,
            'hottest_timesale_active_items' => $hottest_timesale_active_items,
            // 'timesale_pending' => $timesale_pending,
            // 'plan' => $plan,
            // 'attention' => $attention,
            // 'miniBan' => $miniBan,
            // 'brand' => $brand,
            'bottom_notice_list' => $bottom_notice_list,
            'bottom_request_goods_list' => $bottom_request_goods_list,
            // 'substance' => $substance,
            'substance_items' => $substance_items,
            'category_list' => $category_list,
        );
        $this->load->view('pc/index', $data);


        log_message('error', '[성능측정] Main::main() 실행 시간: ' . ($end - $start) . '초');
    }

    private function get_category_list()
    {
        $category = $this->gvcomplex_lib->sql_query_cgy('all');

        return $category;
    }

    // type이 limited, normal, hottest
    private function get_timesale_items($status, $type)
    {
        $pt_id = $this->load->get_var('pt_id');
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

        $sql_search = "";
        $sql_order = "";
        if ($status == "pending") {
            $sql_search .= " and sb_date >= '$OM_TIME_YMDHIS'";
            $sql_order .= "ORDER BY sb_date asc, `rank` desc, index_no desc ";
        } else if ($status == "active") {
            $sql_search .= " and sb_date <= '$OM_TIME_YMDHIS' and eb_date >= '$OM_TIME_YMDHIS'";

            if ($type == "limited") {
                $sql_order .=  "ORDER BY ABS(TIMESTAMPDIFF(SECOND, '$OM_TIME_YMDHIS', eb_date)) ASC ,`rank` desc, index_no desc ";
            } else if ($type == "normal") {
                $sql_order .= "ORDER BY `rank` desc, index_no desc ";
            } else if ($type == "hottest") {
                $sql_order .= "ORDER BY readcount desc, `rank` desc, index_no desc ";
            }
        }

        $resultObj = $this->global_lib->display_timesale($pt_id, "20", $sql_search, $sql_order);
        $result = $resultObj->result_array();

        $items = [];
        foreach ($result as $row) {
            $index_no = $row['index_no'];
            $href = base_url() . 'shop/view?index_no=' . $index_no;
            $name = cut_str($row['gname'], 100);
            $price = $this->funcomplex_lib->get_timesale_goods_price($index_no);
            $amount = $this->gvcomplex_lib->get_timesale_goods_price($index_no);
            $is_uncase = $this->funcomplex_lib->is_uncase($index_no);

            $sale = null;
            $normal_price = null;
            if ($row['normal_price'] > $amount && !$is_uncase) {
                $rate = ($row['normal_price'] - $amount) / $row['normal_price'] * 100;
                $sale = number_format($rate, 0);
                $normal_price = display_price2($row['normal_price']);
            }

            $sql = "SELECT a.*, b.cateuse FROM shop_goods a, shop_category b WHERE a.index_no = ? AND a.ca_id = b.catecode AND a.shop_state = '0'";
            $gs = $this->query_model->returnOneArr($sql, [$index_no]);
            if (!$gs['stock_mod']) $gs['stock_qty'] = 999999999;

            $is_soldout = $this->funcomplex_lib->is_soldout($index_no);

            $option_item = $this->funcomplex_lib->get_item_options($index_no, $gs['opt_subject']);
            $supply_item = $this->funcomplex_lib->get_item_supply($index_no, $gs['spl_subject']);
            $onclick = ((!empty($option_item) || !empty($supply_item)) && !$is_soldout)
                ? "location.href='" . $href . "'"
                : "fbuyform_submite(this)";

            $is_open = "open";
            $is_social_status = '';
            if (substr($gs['sb_date'], 0, 1) !== '0' && substr($gs['eb_date'], 0, 1) !== '0') {
                if ($gs['eb_date'] < $OM_TIME_YMDHIS) {
                    $is_social_status = 'end';
                    $is_open = 'end';
                } elseif ($gs['sb_date'] > $OM_TIME_YMDHIS) {
                    $is_social_status = 'wait';
                    $is_open = 'wait';
                } else {
                    $is_social_status = 'ing';
                }
            }

            $tag = $this->funcomplex_lib->get_pc_timesaleTag($gs, $is_open, "main", $is_soldout);
            $img = !empty($row['simg1'])
                ? (($row['simg_type'] == 0) ? base_url() . "data/goods/{$row['simg1']}" : $row['simg1'])
                : base_url() . "img/noimage.gif";

            $items[] = [
                'index_no' => $index_no,
                'href' => $href,
                'name' => $name,
                'price' => $price,
                'normal_price' => $normal_price,
                'sale_percent' => $sale,
                'img' => $img,
                'tag' => $tag,
                'open_date' => substr($row['sb_date'], 2, 2) . '/' . substr($row['sb_date'], 5, 2) . '/' . substr($row['sb_date'], 8, 2),
                'open_time' => (new DateTime($row['sb_date']))->format('H:i'),
                'end_date' => substr($row['eb_date'], 2, 2) . '/' . substr($row['eb_date'], 5, 2) . '/' . substr($row['eb_date'], 8, 2),
                'end_time' => (new DateTime($row['eb_date']))->format('H:i'),
                'is_open' => $is_open,
                'onclick' => $onclick,
                'is_soldout' => $is_soldout,
                'ca_id' => $gs['ca_id'],
                'goods_price' => $gs['goods_price'],
                'gname' => $gs['gname'],
            ];
        }
        return $items;
    }

    // 타임특가
    private function timesale($status)
    {
        $pt_id = $this->load->get_var('pt_id');
        $str = "";
        $i = 0;
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

        $sql_search = "";
        $sql_order = "";
        if ($status == "pending") {
            $sql_search .= " and sb_date >= '$OM_TIME_YMDHIS'";
            $sql_order .= "ORDER BY sb_date asc, `rank` desc, index_no desc ";
        } else if ($status == "active") {
            $sql_search .= " and sb_date <= '$OM_TIME_YMDHIS' and eb_date >= '$OM_TIME_YMDHIS'";
            $sql_order .= "ORDER BY `rank` desc, index_no desc ";
        }

        $resultObj = $this->global_lib->display_timesale($pt_id, "20", $sql_search, $sql_order);
        $result = $resultObj->result_array();

        foreach ($result as $key => $row) {
            $is_social_ing = false;
            $is_social_wait = false;
            $is_social_end = false;

            if ($i == 0)
                $str .= "<div class=\"pr_desc\">
                            <ul class=\"timesale_slide_{$status}\">\n";

            $it_href = base_url() . 'shop/view?index_no=' . $row['index_no'];
            $it_name = cut_str($row['gname'], 100);
            $it_price = $this->funcomplex_lib->get_timesale_goods_price($row['index_no']);
            $it_amount = $this->gvcomplex_lib->get_timesale_goods_price($row['index_no']);
            $is_uncase = $this->funcomplex_lib->is_uncase($row['index_no']);

            // 할인율 계산
            $it_sprice = $sale = '';
            if ($row['normal_price'] > $it_amount && !$is_uncase) {
                $sett = ($row['normal_price'] - $it_amount) / $row['normal_price'] * 100;
                $sale = '<p class="sale">' . number_format($sett, 0) . '<span class="percent">%</span></p>';
                $it_sprice = display_price2($row['normal_price']);
            }

            $index_no = $row['index_no'];

            $sql = "SELECT a.*, b.cateuse FROM shop_goods a, shop_category b WHERE a.index_no = ? AND a.ca_id = b.catecode AND a.shop_state = '0'";
            $param = array($index_no);
            $gs = $this->query_model->returnOneArr($sql, $param);
            if (!$gs['stock_mod']) {
                $gs['stock_qty'] = 999999999;
            }

            // 품절 확인
            $scrtipt_msg  = "";
            if ($this->funcomplex_lib->is_soldout($index_no)) {
                $scrtipt_msg = 'soldout';
            }

            // 옵션 있는 상품은 상세 페이지로 이동
            $option_item = $this->funcomplex_lib->get_item_options($index_no, $gs['opt_subject']);
            $supply_item = $this->funcomplex_lib->get_item_supply($index_no, $gs['spl_subject']);

            if ((!empty($option_item) || !empty($supply_item)) && $scrtipt_msg != 'soldout') {
                $onclickTxt = "location.href='" . base_url() . "shop/view?index_no={$index_no}'";
            } else {
                $onclickTxt = "fbuyform_submite(this)";
            }

            // 타임세일 상품 시간
            $is_open = "open";
            if (substr($gs['sb_date'], 0, 1) != '0' && substr($gs['eb_date'], 0, 1) != '0') {
                if ($gs['eb_date'] < $OM_TIME_YMDHIS) {
                    $is_social_end = true;
                    $is_open = "end";
                } else if ($gs['sb_date'] > $OM_TIME_YMDHIS) {
                    $is_social_wait = true;
                    $is_open = "wait";
                } else if ($gs['sb_date'] <= $OM_TIME_YMDHIS && $gs['eb_date'] >= $OM_TIME_YMDHIS) {
                    $is_social_ing = true;
                }
            }

            $is_social_tag = "";
            if ($is_social_ing) {
                $is_social_tag = $this->funcomplex_lib->get_pc_timesaleTag($gs, $is_open, "main", $this->funcomplex_lib->is_soldout($gs['index_no']));
            }
            if ($is_social_end) {
                $is_social_tag = $this->funcomplex_lib->get_pc_timesaleTag($gs, $is_open, "main", $this->funcomplex_lib->is_soldout($gs['index_no']));
            }
            if ($is_social_wait) {
                $is_social_tag = $this->funcomplex_lib->get_pc_timesaleTag($gs, $is_open, "main", $this->funcomplex_lib->is_soldout($gs['index_no']));
            }

            if ($i % 4 == 0 && $i != 0) {  // 4개마다 닫는 div 추가
                $str .= "</div>";  // 이전에 열린 div를 닫음
            }

            if ($i % 4 == 0) {
                $str .= "<div>";  // 새로운 div를 열 때마다
            }

            $str .= "<li class=\"timesale {$is_open}\">\n";
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
            $str .=     $is_social_tag;
            $openDate = substr($row['sb_date'], 2, 2) . '/' . substr($row['sb_date'], 5, 2) . '/' . substr($row['sb_date'], 8, 2);
            $openTime = (new DateTime($row['sb_date']))->format('H:i');
            $endDate = substr($row['eb_date'], 2, 2) . '/' . substr($row['eb_date'], 5, 2) . '/' . substr($row['eb_date'], 8, 2);
            $endTime = (new DateTime($row['eb_date']))->format('H:i');

            $str .=     "<div class=\"timebox\">
                            <div class=\"calendar\"></div>
                            <span class=\"date\">{$openDate}</span>
                            <span class=\"time\">{$openTime}</span>
                            <span class=\"dash\">~</span>
                            <span class=\"date\">{$endDate}</span>
                            <span class=\"time\">{$endTime}</span>
                        </div>\n";
            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">";
            $str .=             $sale . $it_price;
            $str .=             "<p class=\"ic_bx\">
                                    <span onclick=\"javascript:itemlistwish('{$row['index_no']}');\" id=\"{$row['index_no']}\" class=\"" . ($row['index_no'] . ' ' . $this->gvcomplex_lib->zzimCheck($row['index_no'])) . "\"></span>
                                </p>";
            $str .=             "<p class=\"ct_bx\">
                                    <input type=\"hidden\" name=\"io_type[{$index_no}][]\" value=\"0\">
                                    <input type=\"hidden\" name=\"it_price[{$index_no}][]\" value=\"{$gs['goods_price']}\">
                                    <input type=\"hidden\" name=\"io_id[{$index_no}][]\" value=\"\">
                                    <input type=\"hidden\" name=\"ca_id[{$index_no}][]\" value=\"{$gs['ca_id']}\">
                                    <input type=\"hidden\" name=\"io_value[{$index_no}][]\" value=\"{$gs['gname']}\">
                                    <input type=\"hidden\" name='ct_qty[{$index_no}][]' value='1' class='inp_opt' title='수량설정' size='2'>
                                    <input type=\"hidden\" data-auth=\"{$scrtipt_msg}\" class='check' name='gs_id[]' value='{$index_no}'>
                                    <span onclick=\"{$onclickTxt};\"></span>
                                </p>
                            </div>";
            $str .=     "</dd>\n";
            $str .= "</li>\n";

            $i++;
        }

        // 마지막 div 닫기
        if ($i % 4 != 0) {
            $str .= "</div>";  // 남은 div 닫기
        }

        if ($i > 0)
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    // 기획전
    private function plan()
    {
        $pt_id = $this->load->get_var('pt_id');
        $sql = "SELECT * FROM shop_goods_plan WHERE mb_id IN('admin','{$pt_id}') AND pl_use = '1' ";
        $res = $this->query_model->returnArr($sql);

        $str = "";
        $str .= "<div class=\"plan_slide\">\n";
        foreach ($res as $row) {
            $str .= "<div class=\"plan_box\">\n";
            $str .=     "<div class=\"plan_icon\">\n";
            $str .=         "<a href=\"" . base_url() . "shop/planlist?pl_no={$row['pl_no']}\">\n";
            $str .=             "<img src=\"" . base_url() . "data/plan/{$row['pl_icon']}\" alt=\"{$row['pl_name']}\">\n";
            $str .=         "</a>\n";
            $str .=     "</div>\n";
            $str .=     "<div class=\"plan_title\">\n";
            $str .=         "<span>{$row['pl_name']}</span>\n";
            $str .=     "</div>\n";
            $str .= "</div>\n";
        }

        $str .= "</div>\n";

        return $str;
    }

    // 관심도 급상승 상품
    private function attention()
    {
        $pt_id = $this->load->get_var('pt_id');
        $str = "";
        $i = 0;

        $resultObj = $this->global_lib->display_attention($pt_id, "20");
        $result = $resultObj->result_array();

        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= "<div class=\"pr_desc attention\">\n
                            <ul class=\"attention_slide\">\n";

            $it_href = base_url() . 'shop/view?index_no=' . $row['index_no'];
            $it_name = cut_str($row['gname'], 100);
            if ($row['type'] == "timesale") {
                $it_price = $this->funcomplex_lib->get_timesale_goods_price($row['index_no']);
                $it_amount = $this->gvcomplex_lib->get_timesale_goods_price($row['index_no']);
            } else {
                $it_price = $this->funcomplex_lib->get_price($row['index_no']);
                $it_amount = $this->gvcomplex_lib->get_sale_price($row['index_no']);
            }
            $is_uncase = $this->funcomplex_lib->is_uncase($row['index_no']);

            // (시중가 - 할인판매가) / 시중가 X 100 = 할인률%
            $it_sprice = $sale = '';
            if ($row['normal_price'] > $it_amount && !$is_uncase) {
                $sett = ($row['normal_price'] - $it_amount) / $row['normal_price'] * 100;
                $sale = '<p class="sale">' . number_format($sett, 0) . '<span class="percent">%</span></p>';
                $it_sprice = display_price2($row['normal_price']);
            }

            $index_no = $row['index_no'];

            $sql = "SELECT a.*, b.cateuse FROM shop_goods a, shop_category b WHERE a.index_no = ? AND a.ca_id = b.catecode AND a.shop_state = '0'";
            $param = array($index_no);
            $gs = $this->query_model->returnOneArr($sql, $param);
            if (!$gs['stock_mod']) {
                $gs['stock_qty'] = 999999999;
            }

            // 품절 확인
            $scrtipt_msg  = "";
            if ($this->funcomplex_lib->is_soldout($index_no)) {
                $scrtipt_msg = 'soldout';
            }
            $status = ($scrtipt_msg == "soldout") ? "disabled" : "";

            // 옵션 있는 상품은 상세 페이지로 이동
            $option_item = $this->funcomplex_lib->get_item_options($index_no, $gs['opt_subject']);
            $supply_item = $this->funcomplex_lib->get_item_supply($index_no, $gs['spl_subject']);

            if ((!empty($option_item) || !empty($supply_item)) && $scrtipt_msg != 'soldout') {
                $onclickTxt = "location.href='" . base_url() . "shop/view?index_no={$index_no}'";
            } else {
                $onclickTxt = "fbuyform_submite(this)";
            }

            $str .= "<li>\n";
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
            $str .=     "<div class=\"wish_box\">";
            $str .=         "<p class=\"ic_bx\">
                                <span onclick=\"javascript:itemlistwish('{$row['index_no']}');\" id=\"{$row['index_no']}\" class=\"" . ($row['index_no'] . ' ' . $this->gvcomplex_lib->zzimCheck($row['index_no'])) . "\"></span>
                            </p>";
            $str .=         "<p class=\"ct_bx\">
                                <input type=\"hidden\" name=\"io_type[{$index_no}][]\" value=\"0\">
                                <input type=\"hidden\" name=\"it_price[{$index_no}][]\" value=\"{$gs['goods_price']}\">
                                <input type=\"hidden\" name=\"io_id[{$index_no}][]\" value=\"\">
                                <input type=\"hidden\" name=\"ca_id[{$index_no}][]\" value=\"{$gs['ca_id']}\">
                                <input type=\"hidden\" name=\"io_value[{$index_no}][]\" value=\"{$gs['gname']}\">
                                <input type=\"hidden\" name='ct_qty[{$index_no}][]' value='1' class='inp_opt' title='수량설정' size='2'>
                                <input type=\"hidden\" data-auth=\"{$scrtipt_msg}\" class='check' name='gs_id[]' value='{$index_no}'>
                                <span onclick=\"{$onclickTxt};\"></span>
                            </p>";
            $str .=     "</div>";
            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">{$sale}{$it_price}</div>";
            $str .=     "</dd>\n";

            $str .= "</li>\n";
            $i++;
        }

        if ($i > 0)
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    // 브랜드관
    private function brand()
    {
        $pt_id = $this->load->get_var('pt_id');
        $str = "";
        $i = 0;

        $resultObj = $this->global_lib->display_brand($pt_id, "40");
        $result = $resultObj->result_array();

        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= "<div class=\"pr_desc brand\">\n
                            <ul class=\"brand_slide\">\n";

            $it_href = base_url() . 'shop/view?index_no=' . $row['index_no'];
            $it_name = cut_str($row['gname'], 100);
            if ($row['type'] == "timesale") {
                $it_price = $this->funcomplex_lib->get_timesale_goods_price($row['index_no']);
                $it_amount = $this->gvcomplex_lib->get_timesale_goods_price($row['index_no']);
            } else {
                $it_price = $this->funcomplex_lib->get_price($row['index_no']);
                $it_amount = $this->gvcomplex_lib->get_sale_price($row['index_no']);
            }
            $is_uncase = $this->funcomplex_lib->is_uncase($row['index_no']);

            // (시중가 - 할인판매가) / 시중가 X 100 = 할인률%
            $it_sprice = $sale = '';
            if ($row['normal_price'] > $it_amount && !$is_uncase) {
                $sett = ($row['normal_price'] - $it_amount) / $row['normal_price'] * 100;
                $sale = '<p class="sale">' . number_format($sett, 0) . '<span class="percent">%</span></p>';
                $it_sprice = display_price2($row['normal_price']);
            }

            $index_no = $row['index_no'];

            $sql = "SELECT a.*, b.cateuse FROM shop_goods a, shop_category b WHERE a.index_no = ? AND a.ca_id = b.catecode AND a.shop_state = '0'";
            $param = array($index_no);
            $gs = $this->query_model->returnOneArr($sql, $param);
            if (!$gs['stock_mod']) {
                $gs['stock_qty'] = 999999999;
            }

            // 품절 확인
            $scrtipt_msg  = "";
            if ($this->funcomplex_lib->is_soldout($index_no)) {
                $scrtipt_msg = 'soldout';
            }
            $status = ($scrtipt_msg == "soldout") ? "disabled" : "";

            // 옵션 있는 상품은 상세 페이지로 이동
            $option_item = $this->funcomplex_lib->get_item_options($index_no, $gs['opt_subject']);
            $supply_item = $this->funcomplex_lib->get_item_supply($index_no, $gs['spl_subject']);

            if ((!empty($option_item) || !empty($supply_item)) && $scrtipt_msg != 'soldout') {
                $onclickTxt = "location.href='" . base_url() . "shop/view?index_no={$index_no}'";
            } else {
                $onclickTxt = "fbuyform_submite(this)";
            }

            if ($i % 4 == 0 && $i != 0) {  // 4개마다 닫는 div 추가
                $str .= "</div>";  // 이전에 열린 div를 닫음
            }

            if ($i % 4 == 0) {
                $str .= "<div>";  // 새로운 div를 열 때마다
            }

            $str .= "<li class=\"brand\">\n";
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
            $str .=     "<div class=\"wish_box\">";
            $str .=         "<p class=\"ic_bx\">
                                <span onclick=\"javascript:itemlistwish('{$row['index_no']}');\" id=\"{$row['index_no']}\" class=\"" . ($row['index_no'] . ' ' . $this->gvcomplex_lib->zzimCheck($row['index_no'])) . "\"></span>
                            </p>";
            $str .=         "<p class=\"ct_bx\">
                                <input type=\"hidden\" name=\"io_type[{$index_no}][]\" value=\"0\">
                                <input type=\"hidden\" name=\"it_price[{$index_no}][]\" value=\"{$gs['goods_price']}\">
                                <input type=\"hidden\" name=\"io_id[{$index_no}][]\" value=\"\">
                                <input type=\"hidden\" name=\"ca_id[{$index_no}][]\" value=\"{$gs['ca_id']}\">
                                <input type=\"hidden\" name=\"io_value[{$index_no}][]\" value=\"{$gs['gname']}\">
                                <input type=\"hidden\" name='ct_qty[{$index_no}][]' value='1' class='inp_opt' title='수량설정' size='2'>
                                <input type=\"hidden\" data-auth=\"{$scrtipt_msg}\" class='check' name='gs_id[]' value='{$index_no}'>
                                <span onclick=\"{$onclickTxt};\"></span>
                            </p>";
            $str .=     "</div>";
            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">{$sale}{$it_price}</div>";
            $str .=     "</dd>\n";

            $str .= "</li>\n";
            $i++;
        }

        // 마지막 div 닫기
        if ($i % 4 != 0) {
            $str .= "</div>";  // 남은 div 닫기
        }

        if ($i > 0)
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    /**
     * 실속관 상품 슬라이드 HTML 생성
     * - 타임세일 상품(timesale)과 일반 상품(normal)을 함께 처리
     * 
     * @return string 완성된 HTML 문자열
     */
    private function substance()
    {
        $pt_id = $this->load->get_var('pt_id');
        $str   = "";

        // 1. display_substance()에서 상품 목록 가져오기
        //    예: global_lib->display_substance($pt_id, "40")
        $resultObj = $this->global_lib->display_substance($pt_id, "40");
        $result    = $resultObj->result_array();

        // 2. 상품이 없는 경우 “등록된 상품이 없습니다” 메시지 출력 후 리턴
        if (empty($result)) {
            return '<div class="goods_empty">등록된 상품이 없습니다.</div>';
        }

        // 3. 슬라이더 컨테이너 오픈
        //    <div class="pr_desc substance">
        //      <ul class="substance_slide">
        $str .= '<div class="pr_desc substance">' . "\n";
        $str .= '  <ul class="substance_slide">' . "\n";

        // 4. 반복문: 4개 항목마다 하나의 내부 블록(<div class="sub-block">)으로 묶기
        $i = 0;
        foreach ($result as $row) {
            // 4-1. 4개씩 새로운 <div class="sub-block"> 열기
            if ($i % 4 === 0) {
                $str .= '    <div class="sub-block">' . "\n";
            }

            // 4-2. 상품별 변수 세팅
            $index_no = $row['index_no'];
            $it_href  = base_url() . 'shop/view?index_no=' . $index_no;
            $it_name  = cut_str($row['gname'], 100);

            // 4-3. 가격 계산: type이 timesale이면 타임세일 가격, else 일반 가격
            if ($row['type'] === "timesale") {
                $it_price  = $this->funcomplex_lib->get_timesale_goods_price($index_no);
                $it_amount = $this->gvcomplex_lib->get_timesale_goods_price($index_no);
            } else {
                $it_price  = $this->funcomplex_lib->get_price($index_no);
                $it_amount = $this->gvcomplex_lib->get_sale_price($index_no);
            }

            // 4-4. 정상가 대비 할인가(할인율) 계산
            $is_uncase    = $this->funcomplex_lib->is_uncase($index_no);
            $it_sprice    = '';
            $sale_html    = '';
            if ($row['normal_price'] > $it_amount && !$is_uncase) {
                $rate     = ($row['normal_price'] - $it_amount) / $row['normal_price'] * 100;
                $sale_txt = number_format($rate, 0);
                $sale_html   = '<p class="sale">' . $sale_txt . '<span class="percent">%</span></p>';
                $it_sprice = display_price2($row['normal_price']);
            }

            // 4-5. 재고/품절 여부 조회
            $sql = "
            SELECT a.*, b.cateuse 
            FROM shop_goods a
            JOIN shop_category b ON (a.ca_id = b.catecode)
            WHERE a.index_no = ?
              AND a.shop_state = '0'
        ";
            $gs = $this->query_model->returnOneArr($sql, array($index_no));

            // 만약 stock_mod가 false라면, 재고를 매우 큰 수로 설정
            if (empty($gs['stock_mod'])) {
                $gs['stock_qty'] = 999999999;
            }

            // 4-6. 품절 여부(is_soldout) 확인
            $is_soldout    = $this->funcomplex_lib->is_soldout($index_no);
            $scrtipt_msg   = $is_soldout ? 'soldout' : '';
            $status        = $is_soldout ? 'disabled' : '';

            // 4-7. 옵션/추가상품 여부에 따라 클릭 이벤트 결정
            $option_item  = $this->funcomplex_lib->get_item_options($index_no, $gs['opt_subject']);
            $supply_item  = $this->funcomplex_lib->get_item_supply($index_no, $gs['spl_subject']);
            if ((!empty($option_item) || !empty($supply_item)) && !$is_soldout) {
                // 옵션이 있거나, 추가상품이 있고, 품절이 아닐 때
                $onclickTxt = "location.href='" . base_url() . "shop/view?index_no={$index_no}'";
            } else {
                // 옵션/추가상품이 없거나 품절된 경우 직접 구매 폼으로
                $onclickTxt = "fbuyform_submite(this)";
            }

            // 4-8. 상품 이미지 설정 (simg1이 없으면 기본 noimage)
            if (!empty($row['simg1'])) {
                $imgSrc = ($row['simg_type'] == 0)
                    ? base_url() . "data/goods/{$row['simg1']}"
                    : $row['simg1'];
            } else {
                $imgSrc = base_url() . "img/noimage.gif";
            }

            // 4-9. 개별 상품 <li> 템플릿 생성
            $str .= '      <li class="brand">' . "\n";
            $str .= '        <a href="' . $it_href . '">' . "\n";
            $str .= '          <dl>' . "\n";
            $str .= '            <dt><img src="' . $imgSrc . '" alt="' . htmlspecialchars($it_name) . '"></dt>' . "\n";
            $str .= '          </dl>' . "\n";
            $str .= '        </a>' . "\n";
            $str .= '        <div class="wish_box">' . "\n";
            $str .= '          <p class="ic_bx">' . "\n";
            $str .= '            <span onclick="itemlistwish(\'' . $index_no . '\');" '
                . 'id="' . $index_no . '" class="' . $index_no . ' '
                . $this->gvcomplex_lib->zzimCheck($index_no) . '"></span>' . "\n";
            $str .= '          </p>' . "\n";
            $str .= '          <p class="ct_bx">' . "\n";
            $str .= '            <input type="hidden" name="io_type[' . $index_no . '][]" value="0">' . "\n";
            $str .= '            <input type="hidden" name="it_price[' . $index_no . '][]" value="' . $gs['goods_price'] . '">' . "\n";
            $str .= '            <input type="hidden" name="io_id[' . $index_no . '][]" value="">' . "\n";
            $str .= '            <input type="hidden" name="ca_id[' . $index_no . '][]" value="' . $gs['ca_id'] . '">' . "\n";
            $str .= '            <input type="hidden" name="io_value[' . $index_no . '][]" value="' . htmlspecialchars($gs['gname']) . '">' . "\n";
            $str .= '            <input type="hidden" name="ct_qty[' . $index_no . '][]" value="1" '
                . 'class="inp_opt" title="수량설정" size="2">' . "\n";
            $str .= '            <input type="hidden" data-auth="' . $scrtipt_msg .
                '" class="check" name="gs_id[]" value="' . $index_no . '">' . "\n";
            $str .= '            <span onclick="' . $onclickTxt . '"></span>' . "\n";
            $str .= '          </p>' . "\n";
            $str .= '        </div>' . "\n"; // .wish_box
            $str .= '        <p class="pname">' . htmlspecialchars($it_name) . '</p>' . "\n";
            $str .= '        <dd class="price_box">' . "\n";
            $str .= '          <p class="price">' . $it_sprice . '</p>' . "\n";
            $str .= '          <div class="price">' . $sale_html . $it_price . '</div>' . "\n";
            $str .= '        </dd>' . "\n";
            $str .= '      </li>' . "\n";

            $i++;

            // 4-10. 4개가 찼거나 마지막 요소일 때 <div> 닫기
            if ($i % 4 === 0 || $i === count($result)) {
                $str .= '    </div>' . "\n";  // sub-block 닫기
            }
        }

        // 5. <ul> 및 <div class="pr_desc"> 닫기
        $str .= '  </ul>' . "\n";
        $str .= '</div>' . "\n";

        return $str;
    }

    // Main.php 에 추가

    /**
     * 실속관 상품 데이터를 배열로 리턴
     * - 타임세일 상품(timesale)과 일반 상품(normal)을 함께 처리
     *
     * @return array
     */
    private function get_substance_items()

    {
        $pt_id        = $this->load->get_var('pt_id');
        $OM_TIME_NOW  = date("Y-m-d H:i:s", time());

        // 1) display_substance() 에서 결과 가져오기 (30개 한정, 라이브러리 호출)
        $resultObj = $this->global_lib->display_substance($pt_id, "30");
        $rows      = $resultObj->result_array();

        // 2) rows가 비어 있으면 빈 배열 리턴
        if (empty($rows)) {
            return [];
        }

        // 3) 최종 item들을 담을 배열
        $items = [];

        foreach ($rows as $row) {
            $index_no = $row['index_no'];
            $href     = base_url() . 'shop/view?index_no=' . $index_no;
            $name     = cut_str($row['gname'], 100);

            // 4) 가격 계산 분기: timesale vs normal
            if ($row['type'] === "timesale") {
                $price  = $this->funcomplex_lib->get_timesale_goods_price($index_no);
                $amount = $this->gvcomplex_lib->get_timesale_goods_price($index_no);
            } else {
                $price  = $this->funcomplex_lib->get_price($index_no);
                $amount = $this->gvcomplex_lib->get_sale_price($index_no);
            }

            // 5) 정상가 대비 할인률, 정상 가격(sprice) 계산
            $is_uncase    = $this->funcomplex_lib->is_uncase($index_no);
            $sale_percent = null;   // 할인률(정수)
            $sprice       = null;   // 정상가(화면에 “취소선” 생김)
            if ($row['normal_price'] > $amount && !$is_uncase) {
                $rate           = ($row['normal_price'] - $amount) / $row['normal_price'] * 100;
                $sale_percent   = (int) round($rate, 0);
                $sprice         = display_price2($row['normal_price']);
            }

            // 6) shop_goods + shop_category 조인해서 재고/카테고리 상태 조회
            $sql = "
            SELECT a.*, b.cateuse 
            FROM shop_goods a
            JOIN shop_category b ON a.ca_id = b.catecode
            WHERE a.index_no = ? 
              AND a.shop_state = '0'
        ";
            $gs = $this->query_model->returnOneArr($sql, [$index_no]);
            if (empty($gs['stock_mod'])) {
                $gs['stock_qty'] = 999999999;
            }

            // 7) 품절 여부 확인
            $is_soldout = $this->funcomplex_lib->is_soldout($index_no);
            $soldout_msg = $is_soldout ? 'soldout' : '';

            // 8) 옵션/추가상품이 있으면 상세페이지 링크, 없으면 장바구니 폼 호출
            $option_item  = $this->funcomplex_lib->get_item_options($index_no, $gs['opt_subject']);
            $supply_item  = $this->funcomplex_lib->get_item_supply($index_no, $gs['spl_subject']);
            if ((!empty($option_item) || !empty($supply_item)) && !$is_soldout) {
                $onclick = "location.href='" . $href . "'";
            } else {
                $onclick = "fbuyform_submite(this)";
            }

            // 9) 실제 이미지 URL 결정 (simg1이 없으면 noimage)
            if (!empty($row['simg1'])) {
                $img = ($row['simg_type'] == 0)
                    ? base_url() . "data/goods/{$row['simg1']}"
                    : $row['simg1'];
            } else {
                $img = base_url() . "img/noimage.gif";
            }

            // 10) 결과 배열에 push
            $items[] = [
                'index_no'     => $index_no,
                'href'         => $href,
                'name'         => $name,
                'type'         => $row['type'],          // timesale or normal
                'price'        => $price,                // 최종 판매가
                'normal_price' => $sprice,               // 정상가 (취소선) — 없으면 null
                'sale_percent' => $sale_percent,          // 할인률 (정수) — 없으면 null
                'img'          => $img,                  // 상품 이미지
                'is_soldout'   => $is_soldout,            // 품절 여부 Boolean
                'soldout_msg'  => $soldout_msg,           // 'soldout' 또는 '' 
                'onclick'      => $onclick,               // 클릭 시 js 함수 or 상세페이지 이동
                'goods_price'  => $gs['goods_price'],     // shop_goods.goods_price
                'ca_id'        => $gs['ca_id'],           // 카테고리 코드
                'gname'        => $gs['gname'],           // 원래 상품명 (풀네임)
                'stock_qty'    => $gs['stock_qty'],       // 재고수량
            ];
        }

        return $items;
    }

    public function ajax_load_category_items()
    {
        $cate = $this->input->post('cate');

        $items = $this->get_substance_items();

        $filtered_items = array_filter($items, function ($item) use ($cate) {
            return substr($item['ca_id'], 0, 3) === $cate;
        });

        $sliced = array_slice($filtered_items, 0, 24);

        foreach ($sliced as $item) {
            echo '
        <div class="swiper-slide" data-cate="' . $item['ca_id'] . '">
            <li class="timesale ' . $item['soldout_msg'] . '">
                <a href="' . $item['href'] . '">
                    <img src="' . $item['img'] . '" alt="">
                    <p class="pname">' . $item['name'] . '</p>
                </a>
                <div class="price-box">
                    ' . ($item['normal_price'] ? '<del>' . $item['normal_price'] . '</del>' : '') . '
                    <strong>' . $item['price'] . '</strong>
                    ' . ($item['sale_percent'] ? '<span class="sale">' . $item['sale_percent'] . '%↓</span>' : '') . '
                </div>
            </li>
        </div>';
        }
    }

    //인트로 화면 구성
    public function intro()
    {
        $this->template->header_sub();
        $conifg = $this->load->get_var('config');
        $pt_id = $this->load->get_var('pt_id');

        $logo = $this->funcomplex_lib->display_logo();
        $ban = $this->funcomplex_lib->display_banner_rows(100, $pt_id);

        $data = array('logo' => $logo, 'conifg' => $conifg, 'ban' => $ban);
        $this->load->view('pc/intro', $data);
        $this->template->footer_sub();
    }

    // 추천상품
    private function recommend()
    {
        $pt_id = $this->load->get_var('pt_id');
        $str = "";
        $i = 0;

        $resultObj = $this->global_lib->display_itemtype($pt_id, "5", "8");
        $result = $resultObj->result_array();

        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= "<div class=\"pr_desc recommend_swiper\">\n
                            <ul class=\"swiper-wrapper\">\n";

            $it_href = base_url() . 'shop/view?index_no=' . $row['index_no'];
            $it_name = cut_str($row['gname'], 100);
            if ($row['type'] == "timesale") {
                $it_price = $this->funcomplex_lib->get_timesale_goods_price($row['index_no']);
                $it_amount = $this->gvcomplex_lib->get_timesale_goods_price($row['index_no']);
            } else {
                $it_price = $this->funcomplex_lib->get_price($row['index_no']);
                $it_amount = $this->gvcomplex_lib->get_sale_price($row['index_no']);
            }
            $is_uncase = $this->funcomplex_lib->is_uncase($row['index_no']);

            // (시중가 - 할인판매가) / 시중가 X 100 = 할인률%
            $it_sprice = $sale = '';
            if ($row['normal_price'] > $it_amount && !$is_uncase) {
                $sett = ($row['normal_price'] - $it_amount) / $row['normal_price'] * 100;
                $sale = '<p class="sale">' . number_format($sett, 0) . '<span class="percent">%</span></p>';
                $it_sprice = display_price2($row['normal_price']);
            }

            $index_no = $row['index_no'];

            $sql = "SELECT a.*, b.cateuse FROM shop_goods a, shop_category b WHERE a.index_no = ? AND a.ca_id = b.catecode AND a.shop_state = '0'";
            $param = array($index_no);
            $gs = $this->query_model->returnOneArr($sql, $param);
            if (!$gs['stock_mod']) {
                $gs['stock_qty'] = 999999999;
            }

            // 품절 확인
            $scrtipt_msg  = "";
            if ($this->funcomplex_lib->is_soldout($index_no)) {
                $scrtipt_msg = 'soldout';
            }
            $status = ($scrtipt_msg == "soldout") ? "disabled" : "";

            $str .= "<li class=\"recommend swiper-slide\">\n";
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
                            <span onclick=\"javascript:itemlistwish('{$row['index_no']}');\" id=\"{$row['index_no']}\" class=\"" . ($row['index_no'] . ' ' . $this->gvcomplex_lib->zzimCheck($row['index_no'])) . "\"></span>
                        </p>";
            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">{$sale}{$it_price}</div>";
            $str .=     "</dd>\n";

            $str .= "</li>\n";
            $i++;
        }

        if ($i > 0)
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    // 카테고리 인기상품
    private function cateBest()
    {
        $default = $this->load->get_var('default');
        $return = "";
        if ($default['de_maintype_best']) {
            $result = unserialize(base64_decode($default['de_maintype_best']));
            $best_arr = [];

            foreach ($result as $array) {
                // 'subj'가 비어있지 않은 경우에만 추가
                if (!empty($array['subj'])) {
                    $best_arr[] = $array;
                }
            }

            $return .= "<div class=\"mtit mart65\">
                            <span>{$default['de_maintype_title']}</span>
                        </div>
            <ul class=\"bestca_tab\">";
            foreach ($best_arr as $key => $row) {
                if ($row['subj'] != "") {
                    $return .=
                        "<li data-tab=\"bstab_c{$key}\" class=\"default\">
                        <img class=\"bstab_c{$key} hide\" src=\"" . base_url() . "data/logo/{$row['logo_01']}\"></img>
                        <img class=\"bstab_c{$key} active\" src=\"" . base_url() . "data/logo/{$row['logo_02']}\"></img>
                        <span>" . trim($row['subj']) . "</span>
                    </li>";
                }
            }
            $return .= "</ul>
            <div class=\"bestca\">" . $this->funcomplex_lib->get_listtype_cate($best_arr) . "</div>
            <script>
                $(document).ready(function(){
                    $(\".bestca_tab>li:eq(0)\").attr('class','active');
                    $(\"#bstab_c0\").show();
                    $(\".bestca_tab>li\").click(function() {
                        var activeTab = $(this).attr('data-tab');
                        $(\".bestca_tab>li\").attr('class','default');
                        $(\".bestca ul\").hide();
                        $(this).attr('class','active');
                        $(\"#\"+activeTab).fadeIn(250);
                    });
                });
            </script>";
        }
        return $return;
    }

    //하단 공지사항 링크 추가
    private function get_bottom_notice_list()
    {
        // db 테이블의 번호임.......
        $board_id = 13;

        //btype이 1은 공지사항 2는 일반사항?
        $sql = "select * from shop_board_{$board_id} order by fid desc ";
        $rst = $this->query_model->returnArr($sql);

        $notice_list = [];

        foreach ($rst as $key => $row) {

            $tag = $row['btype'] == "1" ? "[공지]" : "[일반]";

            $notice_list[] = [
                'title' => $tag . $row['subject'],
                'date' => date("Y.m.d", $row['wdate']),
                'url' => base_url() . "bbs/read?index_no={$row['index_no']}&boardid={$board_id}"
            ];
        }

        return $notice_list;
    }

    //하단 공지사항 링크 추가
    private function get_bottom_request_goods_list()
    {
        // db 테이블의 번호임.......
        $board_id = 37;

        //btype이 1은 공지사항 2는 일반사항?
        $sql = "select * from shop_board_{$board_id} order by fid desc ";
        $rst = $this->query_model->returnArr($sql);

        $notice_list = [];

        foreach ($rst as $key => $row) {

            $notice_list[] = [
                'title' =>  $row['subject'],
                'date' => date("Y.m.d", $row['wdate']),
                'url' => base_url() . "bbs/read?index_no={$row['index_no']}&boardid={$board_id}"
            ];
        }

        return $notice_list;
    }
}
