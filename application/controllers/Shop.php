<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shop extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('query_model');
        $this->load->helper('common_helper');
        $this->load->helper('global_helper');
        $this->load->library('template');
        $this->load->library('funcomplex_lib');
        $this->load->library('gvcomplex_lib');
        $this->load->library('thumbnail_lib');
        $this->load->library('global_lib');
        $this->load->library('partner_lib');
        $this->load->library('common_lib');
        $this->load->vars(array('is_seometa' => '', 'pg' => ''));
        $this->load->config('shop_extend');
        $this->load->config('goodsinfo');
        $device = $this->input->get('device');
        if (isset($device)) {
            $_SESSION['device'] = $device;
        }
    }

    #listtype
    public function listtype()
    {
        $default = $this->load->get_var('default');
        $type = $this->input->get('type');
        $title = $this->preListtype();
        $this->template->header();
        $this->listtypeSkin($title);
        $this->template->footer();
    }

    private function preListtype()
    {
        $default = $this->load->get_var('default');
        $type = $this->input->get('type');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/listtype?type=' . $type);
        }
        if ($type == 1)      $tb['title'] = $default['de_pname_1']; // bid
        else if ($type == 2) $tb['title'] = $default['de_pname_2']; // 베스트셀러
        else if ($type == 3) $tb['title'] = $default['de_pname_3']; // 신상품
        else if ($type == 4) $tb['title'] = $default['de_pname_4']; // 인기상품
        else if ($type == 5) $tb['title'] = $default['de_pname_5']; // 추천상품
        else
            alert('상품유형이 아닙니다.', base_url());
        return $tb['title'];
    }

    private function listtypeSkin($title)
    {
        $type = $this->input->get('type');
        $sort = $this->input->get('sort');
        $sortodr = $this->input->get('sortodr');
        $page_rows = $this->input->get('page_rows');
        $page = $this->input->get('page');
        $gw_psort = $this->config->item('gw_psort');
        $pt_id = $this->load->get_var('pt_id');
        $config = $this->load->get_var('config');
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $sql_search = "";
        // 상품 정렬
        if ($sort && $sortodr)
            $sql_order = " order by a.{$sort} {$sortodr}, a.index_no desc ";
        else
            $sql_order = " order by a.index_no desc ";

        $res = $this->global_lib->query_itemtype($pt_id, $type, $sql_search, $sql_order);
        $total_count = $res->num_rows();

        $mod = 4; // 가로 출력 수
        $rows = $page_rows ? (int)$page_rows : ($mod * 6);
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $resultObj = $this->global_lib->query_itemtype($pt_id, $type, $sql_search, $sql_order . " limit $from_record, $rows ");
        $result = $resultObj->result_array();

        $qstr1 = 'type=' . $type . '&page_rows=' . $page_rows . '&sort=' . $sort . '&sortodr=' . $sortodr;
        $qstr2 = 'type=' . $type . '&page_rows=' . $page_rows;
        $qstr3 = 'type=' . $type . '&sort=' . $sort . '&sortodr=' . $sortodr;

        $sort_str = '';

        for ($i = 0; $i < count($gw_psort); $i++) {
            list($tsort, $torder, $tname) = $gw_psort[$i];

            $sct_sort_href = $requestUrl . '?' . $qstr2 . '&sort=' . $tsort . '&sortodr=' . $torder;

            $active = '';
            if ($sort == $tsort && $sortodr == $torder)
                $active = ' class="active"';
            if ($i == 0 && !($sort && $sortodr))
                $active = ' class="active"';

            $sort_str .= '<li><a href="' . $sct_sort_href . '"' . $active . '>' . $tname . '</a></li>';
        }
        $selectOpt = "";

        //리스트 쿼리문
        $listTag = $this->listTb($result);
        $empty = "";
        if (!$total_count) {
            $empty = "<div class=\"empty_list bb\">자료가 없습니다.</div>";
        }
        $changeSel = $requestUrl . '?' . $qstr3;
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $qstr1 . '&page=');
        $data = array(
            'sort_str' => $sort_str,
            'title' => $title,
            'total_count' => $total_count,
            'selectOpt' => $selectOpt,
            "listTag" => $listTag,
            "empty" => $empty,
            'pagination' => $pagination,
            'changeSel' => $changeSel
        );
        $this->load->view('pc/shop/listtyle', $data);
    }

    private function listTb($result)
    {
        $str = "";
        $i = 0;

        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= "<div class=\"pr_desc mart80\">\n
                            <ul>\n";

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
                $onclickTxt = "fbuyform_submit(this)";
            }

            $str .= "<li class=\"recommend\">\n";
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

            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">{$sale}{$it_price}";
            $str .=     "<p class=\"ic_bx\">
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

        if ($i > 0)
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    #카테고리 리스트를 클릭시 나오는 리스트
    public function cateList()
    {
        $ca_id = $this->input->get('ca_id');
        $pt_id = $this->input->get('pt_id');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/list?ca_id=' . $ca_id);
        }

        $this->template->header();
        $this->listSkin("all");
        $this->template->footer();
    }

    #열린 장터 상품만 나오게
    public function normal()
    {
        $ca_id = $this->input->get('ca_id');
        $pt_id = $this->input->get('pt_id');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/normal?ca_id=' . $ca_id);
        }

        $this->template->header();
        $this->listSkin("normal");
        $this->template->footer();
    }

    private function listSkin($type)
    {
        $ca_id = $this->input->get('ca_id');
        $pt_id = $this->input->get('pt_id');
        $sort = $this->input->get('sort');
        $sortodr = $this->input->get('sortodr');
        $page = $this->input->get('page');
        $gw_psort = $this->config->item('gw_psort');
        $page_rows = $this->input->get('page_rows');

        $config = $this->load->get_var('config');

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $sql = " select * from shop_category where catecode = ? and cateuse = '0' and find_in_set('$pt_id', catehide) = '0' ";
        $param = array($ca_id);
        $ca = $this->query_model->returnOneArr($sql, $param);

        // if(!$ca['catecode'])
        //     alert('등록된 분류가 없습니다.');

        $title = $ca['catename'];

        $sql_search = " and (ca_id like '$ca_id%' or ca_id2 like '$ca_id%' or ca_id3 like '$ca_id%') ";
        $sql_common = $this->gvcomplex_lib->sql_goods_list($sql_search);

        if ($type == "normal") {
            $sql_common .= "and type='{$type}'"; // 일반 상품만 조회
        } else if ($type == "all") {
            $sql_common .= " AND (
                   `type` = 'normal'
                 OR (`type` = 'timesale' AND `eb_date` > NOW())
                ) ";
        }

        // 상품 정렬
        if ($sort && $sortodr)
            $sql_order = " order by {$sort} {$sortodr}, `rank` desc, index_no desc ";
        else
            $sql_order = " order by `rank` desc, index_no desc ";
        // 테이블의 전체 레코드수만 얻음
        $sql = " select count(*) as cnt $sql_common ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $mod = 4; // 가로 출력 수
        $rows = $page_rows ? (int)$page_rows : ($mod * 6);
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $qstr1 = 'ca_id=' . $ca_id . '&page_rows=' . $page_rows . '&sort=' . $sort . '&sortodr=' . $sortodr;
        $qstr2 = 'ca_id=' . $ca_id . '&page_rows=' . $page_rows;
        $qstr3 = 'ca_id=' . $ca_id . '&sort=' . $sort . '&sortodr=' . $sortodr;

        $sort_str = '';
        for ($i = 0; $i < count($gw_psort); $i++) {
            list($tsort, $torder, $tname) = $gw_psort[$i];
            $sct_sort_href = $requestUrl . '?' . $qstr2 . '&sort=' . $tsort . '&sortodr=' . $torder;
            $active = '';
            if ($sort == $tsort && $sortodr == $torder)
                $active = ' class="active"';
            if ($i == 0 && !($sort && $sortodr))
                $active = ' class="active"';
            $sort_str .= '<li><a href="' . $sct_sort_href . '"' . $active . '>' . $tname . '</a></li>';
        }
        $getMove = $this->common_lib->get_move($ca_id);

        $cgy = $this->funcomplex_lib->get_category_head_image($ca_id);

        $heading = isset($cgy['headimg']) ? $cgy['headimg'] : ''; // 분류별 상단이미지


        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            $is_mobile = true;
        } else {
            $is_mobile = false;
        }
        $treeCate = $this->gvcomplex_lib->tree_category($is_mobile, $ca_id, $type); // 하위분류


        $selOpt = array('', '', '');
        // $selOpt[0]=option_selected(($mod*5),  $page_rows, '5줄 정렬');
        // $selOpt[1]=option_selected(($mod*10), $page_rows, '10줄 정렬');
        // $selOpt[2]=option_selected(($mod*15),  $page_rows, '15줄 정렬');

        $pageRowchange = $requestUrl . "?" . $qstr3;

        $listTb = $this->listTable($result);

        $empty = "";
        if (!$total_count) {
            $empty = "<div class=\"empty_list bb\">자료가 없습니다.</div>";
        }
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $qstr1 . '&page=');
        $data = array(
            'title' => $title,
            'ca' => $ca,
            'getMove' => $getMove,
            'heading' => $heading,
            'treeCate' => $treeCate,
            'sort_str' => $sort_str,
            'selOpt' => $selOpt,
            'pageRowchange' => $pageRowchange,
            'listTb' => $listTb,
            'empty' => $empty,
            'pagination' => $pagination,
            'total_count' => $total_count
        );
        $this->load->view('pc/shop/list', $data);
    }

    private function listTable($result)
    {
        $str = "";
        $i = 0;

        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= "<div class=\"pr_desc mart80\">\n
                            <ul>\n";

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
                $onclickTxt = "fbuyform_submit(this)";
            }

            $str .= "<li class=\"goodsList\">\n";
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

            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">{$sale}{$it_price}";
            $str .=     "<p class=\"ic_bx\">
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

        if ($i > 0)
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    #bid
    public function timesale()
    {
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/timesale');
        }
        $this->template->header();
        $this->timesaleSkin();
        $this->template->footer();
    }

    private function timesaleSkin()
    {
        $ca_id = $this->input->get('ca_id') ?? '';
        $pt_id = $this->input->get('pt_id');
        $status = $this->input->get('status'); // 오픈 상태 (pending or active)

        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $gw_psort = $this->config->item('gw_psort');

        $default = $this->load->get_var('default');
        $config = $this->load->get_var('config');

        $page = $this->input->get('page');

        $sql = " select * from shop_category where catecode = ? and cateuse = '0' and find_in_set('$pt_id', catehide) = '0' ";
        $param = array($ca_id);
        $ca = $this->query_model->returnOneArr($sql, $param);

        $title = $default['de_pname_8'];
        $sql_search = " and (ca_id like '$ca_id%' or ca_id2 like '$ca_id%' or ca_id3 like '$ca_id%') ";
        $sql_search .= " and type='timesale' ";
        if ($status == "pending") {
            $sql_search .= " and sb_date >= '$OM_TIME_YMDHIS'";
        } else if ($status == "active") {
            $sql_search .= " and sb_date <= '$OM_TIME_YMDHIS' and eb_date >= '$OM_TIME_YMDHIS'";
        } else if ($status == "end") {
            $sql_search .= " and isopen = 5";
        }

        $sql_common = $this->gvcomplex_lib->sql_goods_list($sql_search);

        $sort = $this->input->get('sort');
        $sortodr = $this->input->get('sortodr');
        $page_rows = $this->input->get('page_rows');

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        // 상품 정렬
        if ($sort && $sortodr)
            $sql_order = " order by {$sort} {$sortodr}, `rank` desc, index_no desc  ";
        else
            $sql_order = " order by `rank` desc, index_no desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql = "select count(*) as cnt $sql_common ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $mod = 4; // 가로 출력 수
        $rows = $page_rows ? (int)$page_rows : ($mod * 6);
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $sql = "select * $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $qstr1 = 'status=active' . '&page_rows=' . $page_rows . '&sort=' . $sort . '&sortodr=' . $sortodr;
        $qstr2 = 'status=active' . '&page_rows=' . $page_rows;
        $qstr3 = 'status=active' . '&sort=' . $sort . '&sortodr=' . $sortodr;

        $sort_str = '';
        for ($i = 0; $i < count($gw_psort); $i++) {
            list($tsort, $torder, $tname) = $gw_psort[$i];

            $sct_sort_href = $requestUrl . '?' . $qstr2 . '&sort=' . $tsort . '&sortodr=' . $torder;

            $active = '';
            if ($sort == $tsort && $sortodr == $torder)
                $active = ' class="active"';
            if ($i == 0 && !($sort && $sortodr))
                $active = ' class="active"';

            $sort_str .= '<li><a href="' . $sct_sort_href . '"' . $active . '>' . $tname . '</a></li>';
        }
        $selectOpt = "";
        // $selectOpt.=option_selected(($mod*5),  $page_rows, '5줄 정렬');
        // $selectOpt.=option_selected(($mod*10),  $page_rows, '10줄 정렬');
        // $selectOpt.=option_selected(($mod*15),  $page_rows, '15줄 정렬');

        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            $is_mobile = true;
        } else {
            $is_mobile = false;
        }
        $treeCate = $this->gvcomplex_lib->tree_category($is_mobile, $ca_id, "timesale", $status); // 하위분류

        $tiemList = $this->timelist($result);
        $empty = "";
        if (!$total_count)
            $empty = "<div class=\"empty_list bb\">자료가 없습니다.</div>";

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $qstr1 . '&page=');
        $changeSel = $requestUrl . '?' . $qstr3;

        $data = array(
            'title' => $title,
            'total_count' => $total_count,
            'sort_str' => $sort_str,
            'selectOpt' => $selectOpt,
            'tiemList' => $tiemList,
            'empty' => $empty,
            'pagination' => $pagination,
            'changeSel' => $changeSel,
            'treeCate' => $treeCate
        );
        $this->load->view('pc/shop/timesale', $data);
    }

    private function timelist($result)
    {
        $str = "";
        $i = 0;
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);

        foreach ($result as $key => $row) {
            $is_social_ing = false;
            $is_social_wait = false;
            $is_social_end = false;

            if ($i == 0)
                $str .= "<div class=\"pr_desc mart80\">
                            <ul>";

            $it_href = base_url() . 'shop/view?index_no=' . $row['index_no'];
            $it_name = cut_str($row['gname'], 100);
            $it_price = $this->funcomplex_lib->get_timesale_goods_price($row['index_no']);
            $it_amount = $this->gvcomplex_lib->get_timesale_goods_price($row['index_no']);
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

            // 옵션 있는 상품은 상세 페이지로 이동
            $option_item = $this->funcomplex_lib->get_item_options($index_no, $gs['opt_subject']);
            $supply_item = $this->funcomplex_lib->get_item_supply($index_no, $gs['spl_subject']);

            if ((!empty($option_item) || !empty($supply_item)) && $scrtipt_msg != 'soldout') {
                $onclickTxt = "location.href='" . base_url() . "shop/view?index_no={$index_no}'";
            } else {
                $onclickTxt = "fbuyform_submit(this)";
            }

            $is_open = "open";
            if (substr($gs['sb_date'], 0, 1) != '0' && substr($gs['eb_date'], 0, 1) != '0') {
                // 종료 날짜가 현재 시간보다 작을때 => 마감
                if ($gs['eb_date'] < $OM_TIME_YMDHIS) {
                    $is_social_end    = true;
                    $is_open = "end";
                    // 시작날짜가 현재시간보다 클 때 => 대기중
                } else if ($gs['sb_date'] > $OM_TIME_YMDHIS) {
                    $is_social_wait    = true;
                    $is_open = "wait";
                    // 시작날짜가 현재시간보다 작거나 같고 종료날짜가 현재시간보다 크거나 같을때 => 판매중
                } else if ($gs['sb_date'] <= $OM_TIME_YMDHIS && $gs['eb_date'] >= $OM_TIME_YMDHIS) {
                    $is_social_ing    = true;
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
            $openTime = (new DateTime($row['eb_date']))->format('H:i');
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
            $str .=         "<div class=\"price\">
                                {$sale}{$it_price}";
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

        if ($i > 0)
            $str .= "</ul>\n
                </div>\n";

        return $str;
    }

    #기획전
    public function plan()
    {
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/plan');
        }
        $this->template->header();
        $this->planSkin();
        $this->template->footer();
    }

    private function planSkin()
    {
        $default = $this->load->get_var('default');
        $pt_id = $this->load->get_var('pt_id');
        $title = $default['de_pname_7'];
        $sql = "select * from shop_goods_plan where mb_id IN('admin','{$pt_id}') and pl_use = '1' ";
        $res = $this->query_model->returnArr($sql);
        $planList = "";
        foreach ($res as $key => $row) {
            $href = base_url() . 'shop/planlist?pl_no=' . $row['pl_no'];
            $bimg = './data/plan/' . $row['pl_limg'];
            if (is_file($bimg) && $row['pl_limg']) {
                $bimg = base_url() . 'data/plan/' . $row['pl_limg'];
                $pl_limgurl = rpc($bimg);
            } else {
                $pl_limgurl = base_url() . 'img/plan_noimg.gif';
            }
            $planList .= "
            <li>
                <a href=\"{$href}\">
                <p class=\"plan_img\"><img src=\"{$pl_limgurl}\"></p>
                <p class=\"plan_tit\">{$row['pl_name']}</p>
                </a>
            </li>";
        }
        $data = array('title' => $title, 'planList' => $planList);
        $this->load->view('pc/shop/plan', $data);
    }

    #planlist
    public function planlist()
    {
        $pl_no = $this->input->get('pl_no');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/planlist?pl_no=' . $pl_no);
        }
        $pl_no = $this->input->get('pl_no');
        $pt_id = $this->load->get_var('pt_id');
        $sql_where = " where pl_no = '$pl_no' ";
        if ($pt_id != 'admin') {
            $sql_where .= " and mb_id IN('admin','$pt_id') ";
        }
        $pl = $this->query_model->returnOneArr("select * from shop_goods_plan {$sql_where} ");
        if (!$pl['pl_no']) {
            alert('기획전이 존재하지 않습니다.');
        }
        $tb['title'] = $pl['pl_name'];
        $this->load->vars(array('tb' => $tb));
        $this->template->header();
        $this->planlistSkin($pl);
        $this->template->footer();
    }

    private function planlistSkin($pl)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $default = $this->load->get_var('default');

        $gw_msort = $this->config->item('gw_msort');
        $sort = $this->input->get('sort');
        $sortodr = $this->input->get('sortodr');
        $page = $this->input->get('page');
        $pl_no = $this->input->get('pl_no');
        $page_rows = $this->input->get('page_rows');


        $qstr1 = 'pl_no=' . $pl_no . '&page_rows=' . $page_rows . '&sort=' . $sort . '&sortodr=' . $sortodr;
        $qstr2 = 'pl_no=' . $pl_no . '&page_rows=' . $page_rows;
        $qstr3 = 'pl_no=' . $pl_no . '&sort=' . $sort . '&sortodr=' . $sortodr;

        $bimg_url = "";
        $bimg = './data/plan/' . $pl['pl_bimg'];
        if (is_file($bimg) && $pl['pl_bimg']) {
            $bimg = base_url() . 'data/plan/' . $pl['pl_bimg'];
            $bimg_url = rpc($bimg);
        }
        // 상품코드 \n -> , 변환
        $pl_it_code = explode("\n", $pl['pl_it_code']);
        $pl_it = mb_comma($pl_it_code);
        if (!$pl_it) $pl_it = 'NULL';

        $sql_search = " and gcode IN({$pl_it}) ";
        $sql_common = $this->gvcomplex_lib->sql_goods_list($sql_search);

        // 상품 정렬
        if ($sort && $sortodr)
            $sql_order = " order by {$sort} {$sortodr}, index_no desc ";
        else
            $sql_order = " order by index_no desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql = " select count(*) as cnt $sql_common ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $mod = 4; // 가로 출력 수
        $rows = $page_rows ? (int)$page_rows : ($mod * 6);
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함   

        $sql = " select * $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $planlistTag = $this->planlistTable($total_count, $result);
        $qstr1 = 'pl_no=' . $pl_no . '&sort=' . $sort . '&sortodr=' . $sortodr;
        $qstr2 = 'pl_no=' . $pl_no;

        $sort_str = '';
        for ($i = 0; $i < count($gw_msort); $i++) {
            list($tsort, $torder, $tname) = $gw_msort[$i];

            $sct_sort_href = $requestUrl . '?' . $qstr2 . '&sort=' . $tsort . '&sortodr=' . $torder;

            if ($sort == $tsort && $sortodr == $torder)
                $sort_name = $tname;
            if ($i == 0 && !($sort && $sortodr))
                $sort_name = $tname;

            $sort_str .= '<li><a href="' . $sct_sort_href . '">' . $tname . '</a></li>';
        }
        $bming_urlTag = "";
        if ($bimg_url) {
            $bming_urlTag = "<div class=\"plan_v_img\"><img src=\"{$bimg_url}\" width=\"1000\"></div>";
        }
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $qstr1 . '&page=');
        $tb['title'] = $pl['pl_name'];

        $selectOpt = "";
        // $selectOpt.=option_selected(($mod*5),  $page_rows, '5줄 정렬');
        // $selectOpt.=option_selected(($mod*10),  $page_rows, '10줄 정렬');
        // $selectOpt.=option_selected(($mod*15),  $page_rows, '15줄 정렬');

        $data = array(
            "sort_str" => $sort_str,
            'total_count' => $total_count,
            'sort_name' => $sort_name,
            'bming_urlTag' => $bming_urlTag,
            'planlistTag' => $planlistTag,
            'pagination' => $pagination,
            'tb' => $tb,
            'defalut' => $default,
            'selectOpt' => $selectOpt,
            'requestUrl' => $requestUrl,
            'qstr3' => $qstr3
        );
        $this->load->view('pc/shop/planlist', $data);
    }

    private function planlistTable($total_count, $result)
    {
        $str = "";
        $i = 0;

        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= "<div class=\"pr_desc mart80\">\n
                            <ul>\n";

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
                $onclickTxt = "fbuyform_submit(this)";
            }

            $str .= "<li class=\"plan\">\n";
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

            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">{$sale}{$it_price}";
            $str .=     "<p class=\"ic_bx\">
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

        if ($i > 0)
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    #브랜드
    public function brand()
    {
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/listtype?type=' . $type);
        }
        $this->template->header();
        $this->brandSkin();
        $this->template->footer();
    }

    private function brandSkin()
    {
        $OM_ALPHAUPPER = 1;
        $OM_HANGUL = 16;
        $default = $this->load->get_var('default');
        $pt_id = $this->load->get_var('pt_id');
        $title = $default['de_pname_6'];
        $where = array();
        $sql_where = "";
        $qstx = $this->input->get('qstx');
        $qstx = utf8_strcut(get_search_string(trim($qstx)), 30, "");
        $qword = trim($this->input->get('qword'));
        $qsort = $this->input->get('qsort');
        if (isset($qsort)) {
            $qsort = trim($qsort);
            $qsort = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\s]/", "", $qsort);
        } else
            $qsort = '';

        $qorder = $this->input->get('qorder');
        if (isset($qorder))
            $qorder = preg_match("/^(asc|desc)$/i", $qorder) ? $qorder : '';
        else
            $qorder = '';

        $qsort  = strtolower($qsort);
        $qorder = strtolower($qorder);

        if (!$qsort) {
            $qsort = "br_name";
            $qorder = "asc";
        }

        $where[] = " ( br_user_yes = 0 or (br_user_yes = 1 and mb_id = '$pt_id') ) and {$qsort} <> '' ";

        if ($qstx) {
            $qstx = $this->db->escape_str($qstx);
            $where[] = " concat(br_name,' ',br_name_eng) like '%$qstx%' ";
        }
        if (check_string($qword, $OM_HANGUL) && $qword == 'ㄱ')
            $where[] = " br_name between '가' and '나' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㄴ')
            $where[] = " br_name between '나' and '다' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㄷ')
            $where[] = " br_name between '다' and '라' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㄹ')
            $where[] = " br_name between '라' and '마' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㅁ')
            $where[] = " br_name between '마' and '바' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㅂ')
            $where[] = " br_name between '바' and '사' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㅅ')
            $where[] = " br_name between '사' and '아' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㅇ')
            $where[] = " br_name between '아' and '자' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㅈ')
            $where[] = " br_name between '자' and '차' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㅊ')
            $where[] = " br_name between '차' and '카' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㅋ')
            $where[] = " br_name between '카' and '타' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㅌ')
            $where[] = " br_name between '타' and '파' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㅍ')
            $where[] = " br_name between '파' and '하' ";
        else if (check_string($qword, $OM_HANGUL) && $qword == 'ㅎ')
            $where[] = " br_name >= '하' ";
        else if (check_string($qword, $OM_ALPHAUPPER) && $qword)
            $where[] = " SUBSTRING(br_name_eng,1,1) = '$qword' ";
        else if (check_string($qword, $OM_ALPHAUPPER) && $qword)
            $where[] = " {$qsort} REGEXP '^[0-9]' ";
        if ($where)
            $sql_where .= " where " . implode(" and ", $where);

        $sql_common = " from shop_brand {$sql_where} ";
        // 테이블의 전체 레코드수만 얻음
        $sql = " select count(*) as cnt {$sql_common} ";
        $row = $this->query_model->returnOneArr($sql);
        #$total_count = (int)$row['cnt'];
        $sql = " select * {$sql_common} order by {$qsort} {$qorder} ";
        $result = $this->query_model->returnArr($sql);
        $i = 0;
        foreach ($result as $key => $row) {
            $list[$i] = $row;
            $list[$i]['br_name'] = ($qsort == 'br_name') ? $row['br_name'] : $row['br_name_eng'];
            $list[$i]['br_href'] = base_url() . 'shop/brandlist?br_id=' . $row['br_id'];

            $bimg = './data/brand/' . $row['br_logo'];
            if (is_file($bimg) && $row['br_logo'])
                $list[$i]['br_logo'] = rpc($bimg);
            else
                $list[$i]['br_logo'] = base_url() . 'img/brlogo_sam.jpg';
            $i++;
        }
        if ($qsort == 'br_name')
            $charAt = explode('|', 'ㄱ|ㄴ|ㄷ|ㄹ|ㅁ|ㅂ|ㅅ|ㅇ|ㅈ|ㅊ|ㅋ|ㅌ|ㅍ|ㅎ');
        else
            $charAt = explode('|', 'A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z');

        $all = ($qword == '') ? 'active' : '';
        $charAtStr = "";
        foreach ($charAt as $word) {
            $word_active = '';
            if ($qword == $word)
                $word_active = ' class="active"';
            $charAtStr .= "<li{$word_active} onclick=\"set_word('{$word}');\">{$word}</li>\n";
        }
        $ect = ($qword == 'etc') ? 'active' : '';

        $brand_str = "<div class='br_list'>\n";
        $brand_str .= "<ul>\n";
        if (isset($list)) {
            for ($i = 0; $i < count($list); $i++) {
                $brand_str .= "<li>\n<a href='{$list[$i]['br_href']}'>\n";
                if ($qsort != 'br_name_eng') {
                    $brand_str .= "<img src='{$list[$i]['br_logo']}' title='{$list[$i]['br_name']}'>\n";
                    $brand_str .= "<p class='mart8'>{$list[$i]['br_name']}</p>\n";
                } else {
                    $brand_str .= "<img src='{$list[$i]['br_logo']}' title='{$list[$i]['br_name_eng']}'>\n";
                    $brand_str .= "<p class='mart8'>{$list[$i]['br_name_eng']}</p>\n";
                }

                $brand_str .= "</a>\n</li>\n";
            }
        }
        $brand_str .= "</ul>\n";
        $brand_str .= "</div>\n";

        $data = array('title' => $title, 'qsort' => $qsort, 'qorder' => $qorder, 'qword' => $qword, 'qstx' => $qstx, 'all' => $all, 'charAtStr' => $charAtStr, 'ect' => $ect, 'brand_str' => $brand_str);
        $this->load->view('pc/shop/brand', $data);
    }

    #브랜드 리스트
    public function brandlist()
    {
        $br_id = $this->input->get('br_id');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/brandlist?br_id=' . $br_id);
        }
        $this->template->header();
        $this->brandlistSkin();
        $this->template->footer();
    }

    private function brandlistSkin()
    {
        $default = $this->load->get_var('default');
        $config = $this->load->get_var('config');

        $gw_psort = $this->config->item('gw_psort');

        $br_id = $this->input->get('br_id');
        $sort = $this->input->get('sort');
        $sortodr = $this->input->get('sortodr');
        $page = $this->input->get('page');
        $page_rows = $this->input->get('page_rows');
        $qstx = $this->input->get('qstx');
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        if ($br_id) {
            $br =  $this->query_model->returnOneArr("select * from shop_brand where br_id = '$br_id'");
            $sql_search = " and brand_uid = '$br_id' ";
        } else {
            $sql_search = " and brand_uid <> '' ";
            $br['br_name'] = $default['de_pname_6'];
        }
        $title = $br['br_name'];
        $bimg = './data/brand/' . $br['br_logo'];
        $br_logo = "";
        if (is_file($bimg) && $br['br_logo']) {
            $bimg = base_url() . 'data/brand/' . $br['br_logo'];
            $br_logo = rpc($bimg);
        } else
            $br_logo = base_url() . 'img/brlogo_sam.jpg';

        $sql_common = $this->gvcomplex_lib->sql_goods_list($sql_search);
        if (!empty($qstx)) {
            $sql_common .= "and gname like '%{$qstx}%'";
        }

        // 상품 정렬
        if ($sort && $sortodr)
            $sql_order = " order by {$sort} {$sortodr}, index_no desc ";
        else
            $sql_order = " order by index_no desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql = " select count(*) as cnt $sql_common ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];
        $mod = 4; // 가로 출력 수

        $rows = $page_rows ? (int)$page_rows : ($mod * 6);
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $tableTag = $this->brandlistTable($result);

        $qstr1 = 'br_id=' . $br_id . '&page_rows=' . $page_rows . '&sort=' . $sort . '&sortodr=' . $sortodr;
        $qstr2 = 'br_id=' . $br_id . '&page_rows=' . $page_rows;
        $qstr3 = 'br_id=' . $br_id . '&sort=' . $sort . '&sortodr=' . $sortodr;

        $sort_str = '';
        for ($i = 0; $i < count($gw_psort); $i++) {
            list($tsort, $torder, $tname) = $gw_psort[$i];

            $sct_sort_href = $requestUrl . '?' . $qstr2 . '&sort=' . $tsort . '&sortodr=' . $torder;

            $active = '';
            if ($sort == $tsort && $sortodr == $torder)
                $active = ' class="active"';
            if ($i == 0 && !($sort && $sortodr))
                $active = ' class="active"';

            $sort_str .= '<li><a href="' . $sct_sort_href . '"' . $active . '>' . $tname . '</a></li>';
        }
        $changeSel = $requestUrl . '?' . $qstr3;

        $selectOpt = "";

        $empty = "";
        if (!$total_count)
            $empty = '<div class="empty_list bb">자료가 없습니다.</div>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $qstr1 . '&page=');

        $data = array(
            'title' => $title,
            'default' => $default,
            'br_logo' => $br_logo,
            'changeSel' => $changeSel,
            'sort_str' => $sort_str,
            'selectOpt' => $selectOpt,
            'total_count' => $total_count,
            'tableTag' => $tableTag,
            'empty' => $empty,
            'pagination' => $pagination,
            'br_id' => $br_id,
            'qstx' => $qstx
        );

        $this->load->view('pc/shop/brandlist', $data);
    }

    private function brandlistTable($result)
    {
        $str = "";
        $i = 0;

        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= "<div class=\"pr_desc mart80\">\n
                            <ul>\n";

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
                $onclickTxt = "fbuyform_submit(this)";
            }

            $str .= "<li class=\"goodsList\">\n";
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

            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">{$sale}{$it_price}";
            $str .=     "<p class=\"ic_bx\">
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

        if ($i > 0)
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    // 검색
    public function search()
    {
        $ss_tx = $this->input->post('ss_tx');
        $ss_tx = isset($ss_tx) ? $ss_tx : $this->input->get('ss_tx');
        $ss_tx = trim(strip_tags($ss_tx));
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/search?ss_tx=' . $ss_tx);
        }
        // if (!$ss_tx) {
        //     alert('검색어가 넘어오지 않았습니다.');
        // }
        $this->template->header();
        $this->searchSkin();
        $this->template->footer();
    }

    private function searchSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);

        $pt_id = $this->load->get_var('pt_id');
        $config = $this->load->get_var('config');

        $ss_tx = $this->input->post('ss_tx');
        $excludeSoldOut = $this->input->post('excludeSoldOut'); // 판매종료 상품 제외
        $onlyBid = $this->input->post('onlyBid'); // bid 상품만 보기

        $ss_tx = isset($ss_tx) ? $ss_tx : $this->input->get('ss_tx');
        $sort = $this->input->get('sort');
        if (empty($sort)) {
            $sort = "readcount";
        }
        $sortodr = $this->input->get('sortodr');
        if (empty($sortodr)) {
            $sortodr = "desc";
        }
        $page = $this->input->get('page');
        $page_rows = $this->input->get('page_rows');
        $gw_psort = $this->config->item('gw_psort');

        $OM_HASH_TOKEN = md5(base_url() . $OM_TIME_YMD . $_SERVER['REMOTE_ADDR']);
        $title = "상품 검색 결과";

        $ss_tx = trim(strip_tags($ss_tx));
        $ss_tx = addslashes($ss_tx);
        $ss_tx = $this->db->escape_str($ss_tx);

        $concat = array();
        $concat[] = "a.gname";
        $concat[] = "a.explan";
        $concat[] = "a.gcode";
        $concat_fields = "concat(" . implode(",' ',", $concat) . ")";
        // 인기검색어
        if (isset($_POST['hash_token']) && $OM_HASH_TOKEN == $_POST['hash_token']) {
            $this->global_lib->insert_popular($pt_id, $ss_tx);
        }
        $sql_search = " and ( $concat_fields like '%$ss_tx%' or find_in_set('$ss_tx', a.keywords) >= 1 ) ";

        // 판매종료된 상품 제외
        if (!empty($excludeSoldOut)) {
            $sql_search .= "and isopen != '5'";
        }

        // bid 상품만 보기
        if (!empty($onlyBid)) {
            $sql_search .= "and type = 'timesale'";
        }

        $sql_common = $this->gvcomplex_lib->sql_goods_search($sql_search);

        // 상품 정렬
        if ($sort && $sortodr) {
            if ($sort == "readcount") {
                $sql_order = " order by
                CASE 
                    WHEN a.isopen = '5' THEN 1
                    ELSE 0
                END ASC, a.{$sort} {$sortodr}, a.index_no desc ";
            } else {
                $sql_order = " order by a.{$sort} {$sortodr}, a.index_no desc ";
            }
        } else {
            $sql_order = " order by a.index_no desc ";
        }

        // 테이블의 전체 레코드수만 얻음
        $sql = " select count(a.index_no) as cnt $sql_common ";

        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $mod = 4; // 가로 출력 수
        $rows = $page_rows ? (int)$page_rows : ($mod * 2);
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select a.* $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $qstr1 = 'ss_tx=' . $ss_tx . '&page_rows=' . $page_rows . '&sort=' . $sort . '&sortodr=' . $sortodr;
        $qstr2 = 'ss_tx=' . $ss_tx . '&page_rows=' . $page_rows;
        $qstr3 = 'ss_tx=' . $ss_tx . '&sort=' . $sort . '&sortodr=' . $sortodr;

        $sort_str = '';
        for ($i = 0; $i < count($gw_psort); $i++) {
            list($tsort, $torder, $tname) = $gw_psort[$i];

            $sct_sort_href = $requestUrl . '?' . $qstr2 . '&sort=' . $tsort . '&sortodr=' . $torder;

            $active = '';
            if ($sort == $tsort && $sortodr == $torder)
                $active = ' class="active"';
            if ($i == 0 && !($sort && $sortodr))
                $active = ' class="active"';

            $sort_str .= '<li><a href="' . $sct_sort_href . '"' . $active . '>' . $tname . '</a></li>';
        }
        $selectOpt = array('', '', '');
        // $selectOpt[0]=option_selected(($mod*5),  $page_rows, '5줄 정렬');
        // $selectOpt[1]=option_selected(($mod*10),  $page_rows, '10줄 정렬');
        // $selectOpt[2]=option_selected(($mod*15),  $page_rows, '15줄 정렬');

        $searchTb = $this->searchTable($result);
        $empty = "";
        if (!$total_count) {
            $empty = "<div class=\"empty_list bb\">자료가 없습니다.</div>";
        }
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $qstr1 . '&page=');

        $data = array(
            "title" => $title,
            'OM_HASH_TOKEN' => $OM_HASH_TOKEN,
            'total_count' => $total_count,
            'ss_tx' => $ss_tx,
            'sort_str' => $sort_str,
            'selectOpt' => $selectOpt,
            'searchTb' => $searchTb,
            'empty' => $empty,
            'pagination' => $pagination,
            'requestUrl' => $requestUrl,
            'qstr3' => $qstr3,
            'excludeSoldOut' => $excludeSoldOut,
            'onlyBid' => $onlyBid
        );

        $this->load->view('pc/shop/search', $data);
    }

    private function searchTable($result)
    {
        $str = "";
        $i = 0;
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

        foreach ($result as $key => $row) {
            $is_social_end = false;
            $is_social_wait = false;
            $is_social_ing = false;

            if ($i == 0)
                $str .= "<div class=\"pr_desc mart80\">\n
                            <ul>\n";

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
                $onclickTxt = "fbuyform_submit(this)";
            }

            // bid 상품 시간
            $is_open = "open";
            if (substr($gs['sb_date'], 0, 1) != '0' && substr($gs['eb_date'], 0, 1) != '0') {
                // 종료 날짜가 현재 시간보다 작을때 => 마감
                if ($gs['eb_date'] < $OM_TIME_YMDHIS) {
                    $is_social_end    = true;
                    $is_open = "end";
                    // 시작날짜가 현재시간보다 클 때 => 대기중
                } else if ($gs['sb_date'] > $OM_TIME_YMDHIS) {
                    $is_social_wait    = true;
                    $is_open = "wait";
                    // 시작날짜가 현재시간보다 작거나 같고 종료날짜가 현재시간보다 크거나 같을때 => 판매중
                } else if ($gs['sb_date'] <= $OM_TIME_YMDHIS && $gs['eb_date'] >= $OM_TIME_YMDHIS) {
                    $is_social_ing    = true;
                }
            }

            $is_social_tag = "";
            if ($is_social_ing) {
                $is_social_tag = $this->funcomplex_lib->get_mb_timesaleTag($gs, $is_open, "main", $this->funcomplex_lib->is_soldout($gs['index_no']));
            }
            if ($is_social_end) {
                $is_social_tag = $this->funcomplex_lib->get_mb_timesaleTag($gs, $is_open, "main", $this->funcomplex_lib->is_soldout($gs['index_no']));
            }
            if ($is_social_wait) {
                $is_social_tag = $this->funcomplex_lib->get_mb_timesaleTag($gs, $is_open, "main", $this->funcomplex_lib->is_soldout($gs['index_no']));
            }

            $str .= "<li class=\"search\">\n";
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
            $str .=         $is_social_tag;
            $str .=     "</dd>";

            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">{$sale}{$it_price}";
            $str .=     "<p class=\"ic_bx\">
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

        if ($i > 0)
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    #상품보기
    public function view()
    {
        $index_no = $this->input->get('index_no');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/view?index_no=' . $index_no);
        }

        $is_seometa = 'it'; // SEO 메타태그
        if (!isset($index_no)) {
            $index_no = $this->input->get('index_no');
        }
        $pt_id = $this->load->get_var('pt_id');

        // 상품의 정보를 얻음
        $sql = "SELECT a.*, b.cateuse
                FROM shop_goods a, shop_category b
                WHERE a.index_no = ? AND a.ca_id = b.catecode AND find_in_set('{$pt_id}', a.use_hide) = '0' AND find_in_set('{$pt_id}', b.catehide) = '0'";
        $param = array($index_no);
        $gs = $this->query_model->returnOneArr($sql, $param);
        $sr = $this->global_lib->get_seller_cd($gs['mb_id']);
        $this->load->vars(array('is_seometa' => 'it', 'gs' => $gs, "sr" => $sr));

        $this->template->header();
        $this->viewSkin();
        $this->template->footer();
    }

    private function createOptionRow($opt_1, $opt_2, $opt_3, $opt_stock_qty)
    {
        $tmp_opt = $opt_1;
        if ($opt_2) $tmp_opt .= ' &gt; ' . $opt_2;
        if ($opt_3) $tmp_opt .= ' &gt; ' . $opt_3;

        return "
            <div>
                <span class=\"opt-cell\">{$tmp_opt}</span>
                <span>:</span>
                <span>{$opt_stock_qty}개</span>
            </div>";
    }

    private function viewSkin()
    {
        $index_no = $this->input->get('index_no');
        $referralCode = $this->input->get('referralCode'); // 리뷰공유코드
        $pt_id = $this->load->get_var('pt_id');
        $member = $this->load->get_var('member');
        $default = $this->load->get_var('default');
        $config = $this->load->get_var('config');
        $is_member = $this->load->get_var('is_member');

        $item_info = $this->config->item('item_info');

        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $gpoint = 0;

        // 상품의 정보를 얻음
        $sql = "SELECT a.*, b.cateuse 
                FROM shop_goods a, shop_category b
                WHERE a.index_no = ? AND a.ca_id = b.catecode AND find_in_set(?, a.use_hide) = '0' AND find_in_set(?, b.catehide) = '0'";
        $param = array($index_no, $pt_id, $pt_id);
        $gs = $this->query_model->returnOneArr($sql, $param);

        $type = $gs['type'];

        if (!$gs['index_no']) {
            alert('등록된 상품이 없습니다');
        }
        if ($gs['shop_state'] || $gs['cateuse']) {
            if (!$this->gvcomplex_lib->is_seller($member['id'])) {
                if (!$this->gvcomplex_lib->is_admin())
                    alert('현재 판매가능한 상품이 아닙니다.');
            }
        }

        // 오늘 본 상품 저장 시작
        if (get_cookie('ss_pr_idx')) {
            $arr_ss_pr_idx = get_cookie('ss_pr_idx');
            $arr_tmps = explode("|", $arr_ss_pr_idx);
            if (!in_array($index_no, $arr_tmps)) {
                $ss_pr_idx = $index_no . "|" . get_cookie('ss_pr_idx');
                set_cookie('ss_pr_idx', $ss_pr_idx, 86400 * 1);
            }
        } else {
            set_cookie('ss_pr_idx', $index_no, 86400 * 1);
        }

        // 공급업체 정보
        $sr = $this->global_lib->get_seller_cd($gs['mb_id']);
        if ($gs['use_aff']) {
            $sr = $this->partner_lib->get_partner($gs['mb_id']);
        }

        // 고객선호도 별점수
        $star_score = $this->funcomplex_lib->get_star_image($index_no);

        // 고객선호도 평점
        $aver_score = ($star_score * 10) * 2;

        // 상품조회 카운터하기
        $sql = "update shop_goods set readcount = readcount + 1 where index_no=?";
        $param = array($index_no);
        $this->query_model->returnNull($sql, $param);

        // 페이지경로
        $navi = "<a href='" . base_url() . "' class='fs11'>HOME</a>" . $this->common_lib->get_move($gs['ca_id']);

        // 수량체크
        if (!$gs['stock_mod']) {
            $gs['stock_qty'] = 999999999;
        }

        if ($gs['odr_min']) {
            // 최소구매수량
            $odr_min = (int)$gs['odr_min'];
        } else {
            $odr_min = 1;
        }

        if ($gs['odr_max']) { // 최대구매수량
            $odr_max = (int)$gs['odr_max'];
        } else {
            $odr_max = 0;
        }

        $is_only = false;
        $is_buy_only = false;
        $is_pr_msg = false;

        // 품절체크
        $is_soldout = $this->funcomplex_lib->is_soldout($index_no);
        // $is_soldout_timesale = $this->funcomplex_lib->is_soldout_timesale($index_no);
        $is_sale_end = $this->funcomplex_lib->is_sale_end($index_no);
        $is_open = "";

        if ($is_soldout) {
            $script_msg = "품절된 상품입니다.";
        } else if ($is_sale_end) {
            $script_msg = "판매종료된 상품입니다.";
        } else {
            if ($gs['price_msg']) {
                $is_pr_msg = true;
                $script_msg = "현재상품은 구매신청 하실 수 없습니다.";
            } else if ($gs['buy_only'] == 1 && $member['grade'] > $gs['buy_level']) {
                $is_only = true;
                $script_msg = "현재상품은 구매신청 하실 수 없습니다.";
            } else if ($gs['buy_only'] == 0 && $member['grade'] > $gs['buy_level']) {
                if (!$is_member) {
                    $is_buy_only = true;
                    $script_msg = "현재상품은 회원만 구매 하실 수 있습니다.";
                } else {
                    $script_msg = "현재상품을 구매하실 권한이 없습니다.";
                }
            } else {
                $script_msg = "";
            }
        }

        // bid 상품 시간
        $is_open = "open";
        if (substr($gs['sb_date'], 0, 1) != '0' && substr($gs['eb_date'], 0, 1) != '0') {
            // 종료 날짜가 현재 시간보다 작을때 => 마감
            if ($gs['eb_date'] < $OM_TIME_YMDHIS) {
                $is_open = "end";
                // 시작날짜가 현재시간보다 클 때 => 대기중
            } else if ($gs['sb_date'] > $OM_TIME_YMDHIS) {
                $is_open = "wait";
                // 시작날짜가 현재시간보다 작거나 같고 종료날짜가 현재시간보다 크거나 같을때 => 판매중
            }
        }

        // 필수 옵션
        $option_item = $this->funcomplex_lib->get_item_options($index_no, $gs['opt_subject']);

        // 추가 옵션
        $supply_item = $this->funcomplex_lib->get_item_supply($index_no, $gs['spl_subject']);

        $cp_used = "";
        // 가맹점상품은 쿠폰발급안함
        if (!$gs['use_aff'] && $config['coupon_yes']) {
            $cp_used = $this->funcomplex_lib->is_used_coupon('0', $index_no, $member['id']);

            // 쿠폰발급 (적용가능쿠폰)
            if ($is_member)
                $cp_btn = "<a href=\"" . base_url() . "shop/pop_coupon?gs_id=$index_no\" onclick=\"win_open(this,'win_coupon','670','500','yes');return false\" class=\"btn_coupon\">적용가능쿠폰</a>";
            else
                $cp_btn = "<a href=\"javascript:alert('로그인 후 이용 가능합니다.')\" class=\"btn_coupon\">적용가능쿠폰</a>";
        }

        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);

        $title = $gs['gname'];
        if ($type == "timesale") {
            $price = $this->gvcomplex_lib->get_timesale_goods_price($index_no);
        } else {
            $price = $this->gvcomplex_lib->get_sale_price($index_no);
        }

        if ($gs['simg_type'] == 0 && !empty($gs['simg2'])) {
            $bimg = "<img src=\"" . base_url() . "data/goods/{$gs['simg2']}\">";
        } else if ($gs['simg_type'] == 1 && !empty($gs['simg2'])) {
            $bimg = "<img src=\"{$gs['simg2']}\">";
        } else {
            $bimg = "<img src=\"" . base_url() . "img/noimage.gif\">";
        }


        $simg_li = "";
        for ($i = 2; $i <= 6; $i++) {
            $it_image = $gs['simg' . $i];
            if (!$it_image) continue;
            if ($gs['simg_type'] == 0) {
                $simg_li .= "<li><img src=\"" . base_url() . "data/goods/{$gs['simg' .$i]}\" onmouseover=\"document.all['big'].src=\"" . base_url() . "data/goods/{$gs['simg' .$i]}\"></li>";
            } else {
                $simg_li .= "<li><img src=\"{$gs['simg' .$i]}\" onmouseover=\"document.all['big'].src=\"{$gs['simg' .$i]}\"></li>";
            }
        }
        $adminEdit = "";
        if ($this->gvcomplex_lib->is_admin()) {
            $adminEdit = "<a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$index_no}&type={$gs['type']}\" target=\"_blank\" class=\"btn_small\">수정</a>";
        }

        $explan = "";
        if ($gs['explan']) {
            $explan = "<p class=\"stxt\">{$gs['explan']}</p>";
        }

        $priceTag = "";
        $priceTag .= "<div class=\"price_bx\">";
        if ($gs['normal_price'] > 0) {
            $it_amount = $this->gvcomplex_lib->get_sale_price($gs['index_no']);
            $sett = ($gs['normal_price'] - $it_amount) / $gs['normal_price'] * 100;
            $sale = number_format($sett, 0);
            $priceTag .= "
			<div>
				<span class=\"sale\">{$sale}%</span>
				<span class=\"norPrice\">" . display_price2($gs['normal_price']) . "</span>
			</div>";
        }

        $priceText = ($gs['type'] == "timesale") ? "판매가(온라인최저가)" : "판매가";
        $sale_text = "";
        if ($gs['type'] == "timesale") {
            $sale_text = "({$sale}%)";
        }
        $priceTag .= "
            <div>
                <span class=\"text\">{$priceText}</span>
                <span class=\"supPrice\"><span class=\"mpr\">" . number_format($price) . "<span>원</span></span> {$sale_text}</span>";
        if ($gs['type'] == "timesale") {
            $priceTag .= $this->funcomplex_lib->get_pc_timesaleTag($gs, $is_open, "normal", $this->funcomplex_lib->is_soldout($gs['index_no']));
        }
        $priceTag .= "    
            </div>";

        if ($gs['type'] == "timesale") {
            $priceTag .= "<p class=\"caution\" style=\"text-align: left; margin-top: 10px;\">입찰 종료 후 실제 판매가가 표시 됩니다.</p>";
        }

        $priceTag .= "</div>";

        // 포인트 적용에 따른 출력형태
        if (!empty($is_member) && $gs['is_point'] > 0) {
            $grade = $member['grade'];

            $sql = "SELECT gb_buy_point FROM shop_member_grade WHERE gb_no = ?";
            $param = array($grade);
            $result = $this->query_model->returnOneArr($sql, $param);

            $gb_point = $result['gb_buy_point']; // 적립률
            $point = (int) round($price * ($gb_point * (1 / 100))); // 적립 포인트

            $rate = number_format($gb_point, 1);
            $gpoint = display_point($point) . " <span class='rate'>($rate%)</span>";
        } else if (empty($is_member) && $gs['is_point'] > 0) {
            $gpoint = "로그인 후 확인 가능";
        } else {
            $gpoint = "적립 제외 품목";
        }

        $pointTag = "";
        if (!$is_only && !$is_pr_msg && !$is_buy_only && !$is_soldout && $gpoint) {
            $pointText = "";
            if ($gs['type'] == "timesale") {
                $pointText = "<span class=\"fc_red\">　(최종 구매가격에 따라서 포인트 적립금액이 상이합니다.)</span>";
            }
            $pointTag = "<dl><dt>포인트</dt><dd>{$gpoint}{$pointText}</dd></dl>";
        }
        $couponTag = "";
        if (!$is_only && !$is_pr_msg && !$is_buy_only && !$is_soldout && $cp_used) {
            $couponTag = "<dl><dt>쿠폰</dt><dd>{$cp_btn}</dd></dl>";
        }
        $maker = "";
        if ($gs['maker']) {
            $maker = "<dl><dt>제조사</dt><dd>{$gs['maker']}</dd></dl>";
        }
        $origin = "";
        if ($gs['origin']) {
            $origin = "<dl><dt>원산지</dt><dd>{$gs['origin']}</dd></dl>";
        }
        $brand = "";
        if ($gs['brand_nm']) {
            $brand = "<dl><dt>브랜드</dt><dd>{$gs['brand_nm']}</dd></dl>";
        }
        $model = "";
        if ($gs['model']) {
            $model = "<dl><dt>모델명</dt><dd>{$gs['model']}</dd></dl>";
        }
        $sendcost = $this->funcomplex_lib->get_sendcost_amt();
        $odr_minTag = "";
        if ($type == "normal" && $gs['odr_min']) {
            $odr_minTag = "<dl><dt>최소구매수량</dt><dd>" . display_qty($gs['odr_min']) . "</dd></dl>";
        }
        $odr_maxTag = "";
        if ($gs['odr_max']) {
            $odr_maxTag = "<dl><dt>최대구매수량</dt><dd>" . display_qty($gs['odr_max']) . "</dd></dl>";
        }
        $odr_stockTag = "";
        if ($type == "timesale") {
            // 재고수량
            $opt_subject = explode(',', $gs['opt_subject']);
            $opt1_subject = $opt_subject[0] ?? '';
            $opt2_subject = $opt_subject[1] ?? '';
            $opt3_subject = $opt_subject[2] ?? '';

            // 옵션 테이블 생성
            $sql = "SELECT * FROM shop_goods_option WHERE io_type = '0' AND gs_id = ? ORDER BY io_no ASC";
            $param = [$gs['index_no']];
            $resultObj = $this->query_model->returnQueryObj($sql, $param);
            $result = $resultObj->result_array();

            $tableOne = '';
            $po_run = $resultObj->num_rows() > 0;

            if ($po_run) {
                if ($gs['index_no']) {
                    foreach ($result as $row) {
                        $opt_id = $row['io_id'];
                        $opt_val = explode(' ', $opt_id);
                        $opt_1 = $opt_val[0] ?? '';
                        $opt_2 = $opt_val[1] ?? '';
                        $opt_3 = $opt_val[2] ?? '';
                        $opt_stock_qty = $row['io_stock_qty'];

                        $tableOne .= $this->createOptionRow($opt_1, $opt_2, $opt_3, $opt_stock_qty);
                    }
                } else {
                    // 새로운 옵션 조합을 생성하는 로직
                    for ($i = 0; $i < $opt1_count; $i++) {
                        for ($j = 0; $j < $opt2_count; $j++) {
                            for ($k = 0; $k < $opt3_count; $k++) {
                                $opt_1 = strip_tags(trim($opt1[$i] ?? ''));
                                $opt_2 = strip_tags(trim($opt2[$j] ?? ''));
                                $opt_3 = strip_tags(trim($opt3[$k] ?? ''));

                                $opt_id = trim($opt_1 . ' ' . $opt_2 . ' ' . $opt_3);
                                $opt_stock_qty = 0;

                                // 기존 값이 있을 경우
                                if ($w === 'u') {
                                    $sql = "SELECT io_no, io_price, io_stock_qty, io_noti_qty, io_use FROM shop_goods_option WHERE gs_id = ? AND io_id = ? AND io_type = '0'";
                                    $gs_id = $this->ci->input->post('gs_id');
                                    $param = [$gs_id, $opt_id];
                                    $row = $this->ci->query_model->returnOneArr($sql, $param);

                                    if ($row['io_no']) {
                                        $opt_stock_qty = (int)$row['io_stock_qty'];
                                    }
                                }

                                $tableOne .= $this->createOptionRow($opt_1, $opt_2, $opt_3, $opt_stock_qty);
                            }
                        }
                    }
                }
            }

            $stock_qty = $this->global_lib->get_it_timesale_stock_qty($gs['index_no']);
            $stock_qty = $stock_qty < 0 ? 0 : $stock_qty;
            $odr_stockTag = "<dl><dt>상품수량</dt><dd>" . display_qty($stock_qty) . "</dd></dl>";

            if (!empty($tableOne)) {
                $odr_stockTag .= "<div class=\"stock_box\">";
                $odr_stockTag .= $tableOne;
                $odr_stockTag .= "</div>";
            }
        }
        $odr_salesTag = "";
        if ($type == "timesale") {
            $order_qty = $this->global_lib->get_order_cnt($gs['index_no']);
            $order_qty = ($gs['original_stock_qty'] < $order_qty) ? $gs['original_stock_qty'] : $order_qty;
            $odr_salesTag = "<dl><dt>판매수량</dt><dd>" . display_qty($order_qty) . "</dd></dl>";
        }
        $odr_periodTag = "";
        if ($type == "timesale") {
            $odr_periodTag = "<dl><dt>판매기간</dt><dd>" . $gs['sb_date'] . " ~ " . $gs['eb_date'] . "</dd></dl>";
        }
        $optionTag = "";
        if (!$is_only && !$is_pr_msg && !$is_buy_only && !$is_soldout) {
            if ($option_item || $supply_item) {
                $optionTag .= "<div class=\"vi_txt_li\">";
                if ($option_item) {
                    $optionTag .= "
                    <dl>
                        <dt>주문옵션</dt>
                        <dd>아래옵션은 필수선택 옵션입니다</dd>
                    </dl>
                    {$option_item}";
                }
                if ($supply_item) {
                    $optionTag .= "
                    <dl>
                        <dt>추가구성</dt>
                        <dd>추가구매를 원하시면 선택하세요</dd>
                    </dl>
                    {$supply_item}";
                }
                $optionTag .= "</div>";
            }
            $optionTag .= "
                <!-- 선택된 옵션 시작 { -->
                <div id=\"option_set_list\">";
            if (!$option_item) {
                $optionTag .= "
                <ul id=\"option_set_added\" class=\"" . $gs['type'] . "\">
					<li class=\"sit_opt_list vi_txt_li\">
						<dl>
							<input type=\"hidden\" name=\"io_type[{$index_no}][]\" value=\"0\">
							<input type=\"hidden\" name=\"io_id[{$index_no}][]\" value=\"\">
							<input type=\"hidden\" name=\"io_value[{$index_no}][]\" value=\"{$gs['gname']}\">
							<input type=\"hidden\" class=\"io_price\" value=\"0\">
							<input type=\"hidden\" class=\"io_stock\" value=\"{$gs['stock_qty']}\">
							<dt>
								<span class=\"sit_opt_subj\">수량</span>
								<span class=\"sit_opt_prc\"></span>
							</dt>
							<dd class=\"li_ea\">
								<span>
									<button type=\"button\" class=\"defbtn_minus\">-</button>
                                    <input type=\"text\" name=\"ct_qty[{$index_no}][]\" value=\"{$odr_min}\" class=\"inp_opt\" title=\"수량설정\" size=\"2\">
                                    <button type=\"button\" class=\"defbtn_plus\">+</button>
								</span>
								<!-- <span class=\"marl7\">( 재고 : " . ($gs['stock_mod'] ? display_qty($gs['stock_qty']) : '무제한 ') . ")</span> -->
							</dd>
						</dl>
					</li>
				</ul>
                <script>
				$(function() {
					price_calculate();
				});
				</script>";
            }
            $optionTag .= "
            </div>
            <div id=\"sit_tot_views\" class=\"dn\">
				<span class=\"fl\">총 합계금액</span>
				<span id=\"sit_tot_price\" class=\"prdc_price\"></span>
			</div>";
            if ($gs['type'] == "timesale") {
                $optionTag .= "<p class=\"caution\">※ ‘입찰 완료’ 완료 이후 취소는 입찰수수료 10%를 제외한 금액이 환급됩니다.</p>";
                $optionTag .= "<p class=\"caution\">※ ‘입찰 완료’ 전 취소는 취소수수료 없이 전액 환급됩니다.</p>";
            }
        }
        $buyBtn = "";
        $naverpay_button_js = $this->load->get_var('naverpay_button_js');
        $naverpay_request_js = $this->load->get_var('naverpay_request_js');
        if (!$is_pr_msg) {
            $buyBtn .= "<div class=\"vi_btn\">" . $this->gvcomplex_lib->get_buy_button($script_msg, $index_no, $is_open, $is_soldout) . "</div>";
            if ($naverpay_button_js) {
                $buyBtn .= "<div class=\"naverpay-item\">{$naverpay_request_js}.{$naverpay_button_js}</div>";
            }
        }
        $related = $this->related($index_no);
        $thumbTag = $this->thumbnail_lib->get_view_thumbnail($this->funcomplex_lib->conv_content($gs['memo'], 1), 1000);

        $info_value_tag = "";
        if ($gs['info_value']) {
            $info_data = unserialize(stripslashes($gs['info_value']));
            if (is_array($info_data)) {
                $gubun = $gs['info_gubun'];
                $info_array = (isset($gubun) && $gubun != "") ? $item_info[$gubun]['article'] : "";
                $info_value_tag .= "
                <div class=\"mart20 marb30\">
                    <h2 class=\"anc_tit\">전자상거래 등에서의 상품정보제공고시</h2>
                    <div class=\"tbl_frm01 tbl_wrap\">
                        <table>
                        <colgroup>
                            <col width=\"25%\">
                            <col width=\"75%\">
                        </colgroup>";
                foreach ($info_data as $key => $val) {
                    $ii_title = $info_array[$key][0];
                    $ii_value = $val;
                    $info_value_tag .= "<tr><th scope=\"row\">{$ii_title}</th><td>{$ii_value}</td></tr>";
                } //foreach
                $info_value_tag .= "
                        </table>
                    </div>
                </div>";
            } //array
        } //if
        $view_user = $this->view_user($index_no, $gs, $token);
        $view_qa = $this->view_qa($index_no);
        $thumbTag2 = $this->thumbnail_lib->get_view_thumbnail($this->funcomplex_lib->conv_content($this->funcomplex_lib->get_policy_content($index_no), 1), 1000);

        // bid 관련 태그
        $timesale = "";
        if ($type == "timesale") {
            $timesale_list = $this->global_lib->get_timesale_list2($gs['index_no']);

            $order_qty = $this->global_lib->get_order_cnt($gs['index_no']);
            $stock_qty = $gs['original_stock_qty'];
            $percent = ($order_qty / $stock_qty) * 100;
            $percent = $percent > 100 ? 100 : $percent;
            $percent_class = ($percent == 100 || $percent == 0) ? "hide" : "show";

            $timesale .=
                "<div class=\"timesale\">
					<div class=\"title\">
						<span class=\"tit-text\">남은 구매시간</span>";
            $timesale .= $this->funcomplex_lib->get_pc_timesaleTag($gs, $is_open, "normal", $this->funcomplex_lib->is_soldout($gs['index_no']));
            // $timesale .=
            //     "</div>
            //         <div class=\"progress-container\">
            //             <div class=\"progress-bar\">
            //                 <div class=\"progress-label {$percent_class}\"></div>
            //             </div>
            //             <div class=\"progress-min\">0</div>
            //             <div class=\"progress-max\">" . $stock_qty . "</div>
            //         </div>
            //         <script>
            //             // 진행률 퍼센트 설정
            //             const progressPercentage = " . $percent . ";
            //             document.querySelector('.progress-bar').style.width = progressPercentage + '%';
            //             document.querySelector('.progress-label').textContent = " . $order_qty . ";
            //         </script>
            //         <table>
            //             <colgroup>
            //                 <col style=\"width: 20%\">
            //                 <col style=\"width: 20%\">
            //                 <col style=\"width: 30%\">
            //                 <col style=\"width: 30%\">
            //             </colgroup>
            //                 <thead>
            //                 <tr>
            //                     <td>구매순서</td>
            //                     <td>아이디</td>
            //                     <td>구매시간</td>
            //                     <td>
            //                         <p>구매가격</p>
            //                         <p>(판매종료 후 공개)</p>
            //                     </td>
            //                 </tr>
            //             </thead>
            //         </table>";

            if ($is_member) {
                $member = $this->load->get_var('member');
                // 구매내역 있는 경우에만 구매 정보 노출
                $already_order_data = $this->global_lib->get_timesale_info($gs['index_no'], $member['id']);

                if (!empty($already_order_data)) {
                    $timesale .=
                        "<div class=\"table_box myInfo\">
                        <table>
                            <colgroup>
                                <col style=\"width: 20%\">
                                <col style=\"width: 20%\">
                                <col style=\"width: 30%\">
                                <col style=\"width: 30%\">
                            </colgroup>
                            <tbody>";
                    foreach ($already_order_data as $row) {
                        $length = strlen($row['mb_id']);
                        $start = substr($row['mb_id'], 0, 3);
                        $masking = "";

                        for ($i = 0; $i < ($length - 3); $i++) {
                            $masking .= "*";
                        }

                        $hiddenId = $start . $masking;

                        // 딜 마감 전에는 normal_price 노출
                        // 딜 마감 후에는 실제 구매가 노출
                        $price = empty($row['gts_no']) ? "" : number_format($row['real_price']) . "원";

                        //     $timesale .=
                        //         "<tr>
                        //                 <td>" . $row['num'] . "</td>
                        //                 <td>" . $hiddenId . "</td>
                        //                 <td>" . $row['od_time'] . "</td>
                        //                 <td>" . $price . "</td>
                        //             </tr>";
                    }
                    // $timesale .=
                    //     "</tbody>
                    //     </table>
                    // </div>";
                }
            }

            // $timesale .=
            //     "<div class=\"table_box\">
            //             <table>
            //                 <colgroup>
            //                     <col style=\"width: 20%\">
            //                     <col style=\"width: 20%\">
            //                     <col style=\"width: 30%\">
            //                     <col style=\"width: 30%\">
            //                 </colgroup>
            //                 <tbody>";

            foreach ($timesale_list as $key => $row) {
                $num = $key + 1;

                $length = strlen($row['mb_id']);
                $start = substr($row['mb_id'], 0, 3);
                $masking = "";

                for ($i = 0; $i < ($length - 3); $i++) {
                    $masking .= "*";
                }

                $hiddenId = $start . $masking;

                // 딜 마감 전에는 normal_price 노출
                // 딜 마감 후에는 실제 구매가 노출
                $price = empty($row['gts_no']) ? "" : number_format($row['real_price']) . "원";

                // 상품구매현황 안나오게함
                // $timesale .=
                //     "<tr>
                // 				<td>" . $num . "</td>
                // 				<td>" . $hiddenId . "</td>
                // 				<td>" . $row['od_time'] . "</td>
                // 				<td>" . $price . "</td>
                // 			</tr>";
            }

            // $timesale .=
            //     "</tbody>
            //             </table>
            //         </div>
            //         <button class=\"more_btn\" onclick=\"order_more()\">구매현황 더보기</button>
            // 	</div>";
            $timesale .=
                "
                    </div>
				</div>";
        }

        // 상품 찜하기 체크
        $zzim = $this->gvcomplex_lib->zzimCheck($index_no);

        $data = array(
            'title' => $title,
            'index_no' => $index_no,
            'referralCode' => $referralCode,
            'price' => $price,
            'gs' => $gs,
            'navi' => $navi,
            'default' => $default,
            'bimg' => $bimg,
            'simg_li' => $simg_li,
            'adminEdit' => $adminEdit,
            'explan' => $explan,
            'priceTag' => $priceTag,
            "pointTag" => $pointTag,
            'couponTag' => $couponTag,
            'maker' => $maker,
            'origin' => $origin,
            'brand' => $brand,
            'model' => $model,
            'sendcost' => $sendcost,
            'star_score' => $star_score,
            'odr_min' => $odr_min,
            'odr_max' => $odr_max,
            'odr_minTag' => $odr_minTag,
            'odr_maxTag' => $odr_maxTag,
            'odr_salesTag' => $odr_salesTag,
            'odr_stockTag' => $odr_stockTag,
            'odr_periodTag' => $odr_periodTag,
            "optionTag" => $optionTag,
            'buyBtn' => $buyBtn,
            'related' => $related,
            'thumbTag' => $thumbTag,
            'info_value_tag' => $info_value_tag,
            "thumbTag2" => $thumbTag2,
            'view_user' => $view_user,
            'view_qa' => $view_qa,
            'type' => $type,
            'is_open' => $is_open,
            'timesale' => $timesale,
            'zzim' => $zzim
            // 'is_soldout_timesale' => $is_soldout_timesale
        );
        $this->load->view('pc/shop/view', $data);
    }

    private function related($index_no)
    {
        $returnTag = "";
        $sql = "select b.* from shop_goods_relation a join shop_goods b ON (a.gs_id2=b.index_no) where a.gs_id = ?";
        $prepareArr = array($index_no);
        $resObj = $this->query_model->returnQueryObj($sql, $prepareArr);
        $rel_count = $resObj->num_rows();
        $res = $resObj->result_array();
        if ($rel_count > 0) {
            $returnTag .= "
            <section class=\"vi_rel\">
            <h3><span>현재상품과 연관된 상품</span></h3>
            <div " . (($rel_count <= 5) ? 'class = "ofh"' : '') . ">";
            foreach ($res as $key => $row) {
                $it_href = base_url() . 'shop/view?index_no=' . $row['index_no'];
                $it_image = $this->funcomplex_lib->get_it_image($row['index_no'], $row['simg1'], 174, 174);
                $it_name = cut_str($row['gname'], 100);
                $it_price = $this->funcomplex_lib->get_price($row['index_no']);
                $it_amount = $this->gvcomplex_lib->get_sale_price($row['index_no']);
                $it_point = display_point($row['gpoint']);
                // (시중가 - 할인판매가) / 시중가 X 100 = 할인률%
                $it_sprice = $sale = '';
                if ($row['normal_price'] > $it_amount && !$this->funcomplex_lib->is_uncase($row['index_no'])) {
                    $sett = ($row['normal_price'] - $it_amount) / $row['normal_price'] * 100;
                    $sale = '<p class="sale">' . number_format($sett, 0) . '<span class="percent">%</span></p>';
                    $it_sprice = display_price2($row['normal_price']);
                }
                $returnTag .= "
                <dl>
                    <a href=\"{$it_href}\">
                        <dt>{$it_image}</dt>
                        <dd class=\"pname\">{$it_name}</dd>
                        <dd class=\"price\">{$it_sprice} {$it_price}</dd>
                    </a>
                </dl>";
            }
            $returnTag .= "</div>";
        }
        if ($rel_count > 5) {
            $returnTag .= "	<script>
            $(document).ready(function(){
                $('.vi_rel div').slick({
                    autoplay: false,
                    dots: false,
                    arrows: true,
                    infinite: false,
                    slidesToShow: 5,
                    slidesToScroll: 1
                });
            });
            </script>";
        }
        $returnTag .= "</section>";
        return $returnTag;
    }

    private function view_user($index_no, $gs, $token)
    {
        $default = $this->load->get_var('default');
        $member = $this->load->get_var('member');
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');

        $returnData = $this->funcomplex_lib->getReviewData($index_no);
        $result = $returnData['result'];
        $item_use_count = count($result);

        $tableTag = "";
        foreach ($result as $key => $row) {
            $wr_id = substr($row['mb_id'], 0, 3) . str_repeat("*", strlen($row['mb_id']) - 3);
            $wr_time = substr($row['reg_time'], 0, 10);
            $tableTag .= "
                        <tr>
                            <td class=\"text\">
                                <p>" . $row['memo'] . "</p>
                                <div>";

            foreach (['img1', 'img2', 'img3', 'img4'] as $imgKey) {
                if (!empty($row[$imgKey])) {
                    $media_type = $this->funcomplex_lib->getMediaType($row[$imgKey]); // 사진/동영상 출력

                    if ($media_type == "img") {
                        $tableTag .= "<img src=\"" . base_url() . "data/review/{$row[$imgKey]}\" onclick=\"open_review_modal(" . $row['index_no'] . "," . $index_no . ")\">";
                    } else {
                        $tableTag .= "<div onclick=\"open_review_modal(" . $row['index_no'] . "," . $index_no . ")\">
											<video preload=\"metadata\" src=\"" . base_url() . "data/review/{$row[$imgKey]}\"></video>
											<i class='fa fa-play-circle'></i>
										</div>";
                    }
                }
            }

            $tableTag .= "      </div>
                            </td>";
            $tableTag .= "
                            <td width=\"130\" class=\"tac\">{$wr_id}</td>
                            <td width=\"80\" class=\"tac\">{$wr_time}</td>
                            <td width=\"100\" class=\"tac\">";
            for ($i = 0; $i < (int)$row['score']; $i++) {
                $tableTag .= "<img src=\"" . base_url() . "dist/img/pc/star.png\" align=\"absmiddle\">";
            }

            $key = "share";
            $referralCode = base64_encode(openssl_encrypt($row['mb_id'], "AES-256-CBC", $key, true, str_repeat(chr(0), 16)));

            $tableTag .= "    </td>
                            <td class=\"share_area\" width=\"50\">
                                <div class=\"share\" onclick=\"clip('" . $referralCode . "')\"></div>
                            </td>
                        </tr>";
        }
        $memberName = "";
        if ($is_member)
            $memberName = $member['name'];
        else
            $memberName = "로그인 후 작성하여 주십시오.";

        $scoreTag = "";
        for ($i = 1; $i <= 5; $i++) {
            $checked = "";
            if ($i == 1) $checked = "checked";

            $scoreTag .= "<input type=\"radio\" name=\"score\" value=\"{$i}\" {$checked}><img src=\"" . base_url() . "img/sub/score_{$i}.gif\" align=\"absmiddle\">";
        }

        $data = array(
            'item_use_count' => $item_use_count,
            'index_no' => $index_no,
            'tableTag' => $tableTag,
            'gs' => $gs,
            'token' => $token,
            'memberName' => $memberName,
            'scoreTag' => $scoreTag,
            'urlencode' => $urlencode
        );
        return $this->load->view('pc/shop/view/view_user', $data, TRUE);
    }

    private function view_qa($index_no)
    {
        $returnTag = "";
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $page = $this->input->get('page');


        $sql_common = " from shop_goods_qa ";
        $sql_search = " where gs_id = '$index_no' ";
        $sql_order  = " order by iq_id desc ";
        $sql = " select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $tableTag = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $iq_subject = cut_str(htmlspecialchars($row['iq_subject']), 66);
            if (substr($row['iq_time'], 0, 10) == $OM_TIME_YMD) {
                $iq_subject .= '&nbsp;<img src="' . base_url() . 'img/icon/icon_new.gif" alt="new">';
            }
            $is_secret = false;
            if ($row['iq_secret']) {
                $icon_secret = '<img src="' . base_url() . 'img/icon/icon_secret.jpg" alt="비밀글">';

                if ($this->gvcomplex_lib->is_admin() || $member['id'] == $row['mb_id']) {
                    $iq_answer = $row['iq_answer'];
                } else {
                    $iq_answer = "";
                    $is_secret = true;
                }
            } else {
                $icon_secret = "";
                $iq_answer = $row['iq_answer'];
            }
            if ($row['iq_answer']) {
                $icon_answer = '<img src="' . base_url() . 'img/icon/icon_answer.jpg" alt="답변완료">';
            } else {
                $icon_answer = '<img src="' . base_url() . 'img/icon/icon_standby.jpg" alt="미답변">';
            }
            $len = strlen($row['mb_id']);
            $str = substr($row['mb_id'], 0, 3);
            $mb_id = $str . str_repeat("*", $len - 3);

            $hash = md5($row['iq_id'] . $row['iq_time'] . $row['iq_ip']);

            $bg = 'list' . $i % 2;
            $tableTag .= "
            <table class=\"wfull\">
                <colgroup>
                    <col width=\"8%\">
                    <col width=\"14%\">
                    <col width=\"6%\">
                    <col width=\"2%\">
                    <col>
                    <col width=\"12%\">
                    <col width=\"14%\">
                </colgroup>
                <tbody>
                    <tr class=\"{$bg}\">
                        <td class=\"tac\">" . ($num--) . "</td>
                        <td class=\"tac\">{$row['iq_ty']}</td>
                        <td class=\"tac\">{$icon_answer}</td>
                        <td class=\"tac\">{$icon_secret}</td>
                        <td>";
            if (!$is_secret) {
                $tableTag .= "<a href='javascript:void(0);' onclick=\"js_qna('" . $i . "')\">";
            }
            $tableTag .= $iq_subject;
            if (!$is_secret) {
                $tableTag .= "</a>";
            }
            $tableTag .= "
                        </td>
                        <td>{$mb_id}</td>
                        <td class=\"tac\">{$row['iq_time']}</td>
                    </tr>
                </tbody>
            </table>
    
            <div id=\"sod_qa_con_{$i}\" class=\"sod_qa_con\" style=\"display:none;\">
                <table class=\"wfull\">
                    <colgroup>
                        <col width=\"16\">
                        <col>
                    </colgroup>
                    <tr>
                        <td class=\"vat tal padt10 padb10 padl5 padr5\"><img src=\"" . base_url() . "img/sub/FAQ_Q.gif\" align=\"absmiddle\"></td>
                        <td class=\"vat tal padt10 padb10 padl5 padr5\">";
            $tableTag .= nl2br(htmlspecialchars($row['iq_question']));
            if ($this->gvcomplex_lib->is_admin() || $member['id'] == $row['mb_id'] && !$iq_answer) {
                $tableTag .= "<div class=\"padt10\"><a href=\"" . base_url() . "shop/qaform?gs_id={$row['gs_id']}&iq_id={$row['iq_id']}&w=u\" onclick=\"win_open(this,'upd','600','530','yes');return false\"><span class=\"tu\">수정</span></a>&nbsp;<a href=\"" . base_url() . "process/qaform_update_del?gs_id={$row['gs_id']}&iq_id={$row['iq_id']}&w=d&hash={$hash}\" class=\"itemqa_delete\"><span class=\"tu\">삭제</span></a></div>";
            }
            $tableTag .= "</td>
                    </tr>";
            if ($iq_answer) {
                $tableTag .= "
                        <tr>
                            <td class=\"vat tal padt10 padb10 padl5 padr5\"><img src=\"" . base_url() . "img/sub/FAQ_A.gif\" align=\"absmiddle\"></td>
                            <td class=\"vat tal padt10 padb10 padl5 padr5\">" . nl2br(htmlspecialchars($iq_answer)) . "</td>
                        </tr>";
            }
            $tableTag .= "
                </table>
            </div>";
            $i++;
        }
        if (!$total_count) {
            $tableTag .= "<div class=\"empty_list bb\">문의 내역이 없습니다.</div>";
        }

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?index_no=' . $index_no . '&page=');

        $data = array('total_count' => $total_count, 'index_no' => $index_no, 'tableTag' => $tableTag, 'pagination' => $pagination);
        return $this->load->view('pc/shop/view/view_qa', $data, TRUE);
    }

    #wish페이지
    public function wish()
    {
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/wish');
        }
        if (!$is_member) {
            goto_url(base_url() . 'bbs/login?url=' . $urlencode);
        }
        $this->template->header();
        $this->wishSkin();
        $this->template->footer();
    }

    private function wishSkin()
    {
        $member = $this->load->get_var('member');
        $title = "찜한상품";
        $sql  = " select a.wi_id, a.wi_time, a.gs_id, b.* 
                    from shop_wish a left join shop_goods b ON ( a.gs_id = b.index_no )
                where a.mb_id = ?
                order by a.wi_id desc ";
        $param = array($member['id']);
        $resultObj = $this->query_model->returnQueryObj($sql, $param);
        $wish_count = $resultObj->num_rows();
        $result = $resultObj->result_array();
        $aside_my = $this->template->aside_my();
        $wishTable = $this->wishTable($result);

        $data = array('title' => $title, 'aside_my' => $aside_my, 'wish_count' => $wish_count, 'wishTable' => $wishTable);
        $this->load->view('pc/shop/wish', $data);
    }

    private function wishTable($result)
    {
        $returnTag = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $out_cd = '';
            $sql = " select count(*) as cnt from shop_goods_option where gs_id = ? and io_type = '0' ";
            $param = array($row['gs_id']);
            $tmp = $this->query_model->returnOneArr($sql, $param);
            if ($tmp['cnt'])
                $out_cd = 'no';

            if ($row['price_msg']) {
                $out_cd = 'price_msg';
            }
            $returnTag .= "
            <tr>
                <td class=\"tac\">";
            if ($this->funcomplex_lib->is_soldout($row['gs_id'])) {
                $returnTag .= "품절";
            } else {
                $returnTag .= "<input type=\"checkbox\" name=\"chk_gs_id[{$i}]\" value=\"1\" onclick=\"out_cd_check(this, '{$out_cd}');\">";
            }
            $returnTag .= "
                    <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$row['gs_id']}\">
                    <input type=\"hidden\" name=\"io_type[{$row['gs_id']}][0]\" value=\"0\">
                    <input type=\"hidden\" name=\"io_id[{$row['gs_id']}][0]\" value=\"\">
                    <input type=\"hidden\" name=\"io_value[{$row['gs_id']}][0]\" value=\"{$row['gname']}\">
                    <input type=\"hidden\" name=\"ct_qty[{$row['gs_id']}][0]\" value=\"1\">
                </td>
                <td class=\"tac\"><a href=\"" . base_url() . "shop/view?index_no={$row['gs_id']}\">" . $this->funcomplex_lib->get_it_image($row['gs_id'], $row['simg1'], 70, 70) . "</a></td>
                <td class=\"td_name\">
                    <a href=\"" . base_url() . "shop/view?index_no={$row['gs_id']}\">{$row['gname']}</a>
                    <p class=\"fc_999\">{$row['explan']}</p>
                </td>			
                <td class=\"tar\">" . $this->funcomplex_lib->get_price($row['gs_id']) . "</td>
                <td class=\"tac\"><a href=\"" . base_url() . "process/wishupdate?w=d&wi_id={$row['wi_id']}\" class=\"btn_small bx-red\">삭제</td>
            </tr>";
            $i++;
        }
        if ($i == 0) {
            $returnTag = '<tr><td colspan="5" class="empty_list">보관함이 비었습니다.</td></tr>';
        }
        return $returnTag;
    }

    #cart페이지
    public function cart()
    {
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/cart');
        }
        $this->template->header();
        $this->cartSkin();
        $this->template->footer();
    }

    private function cartSkin()
    {
        $title = "장바구니";
        $set_mb_id = "";
        $is_member = $this->load->get_var('is_member');
        if (!$is_member) {
            $urlencode = $this->load->get_var('urlencode');
            goto_url(base_url() . 'bbs/login?url=' . $urlencode);
        } else {
            $set_mb_id = $this->session->userdata['ss_mb_id'];
        }
        $sql = "SELECT a.*, b.isopen
                FROM shop_cart AS a
                RIGHT JOIN shop_goods AS b
                ON a.gs_id = b.index_no
                WHERE a.mb_id = ? AND a.ct_select = '0' AND b.isopen = '1' AND is_direct = 0
                ORDER BY a.index_no";
        $param = array($set_mb_id);
        $resultObj = $this->query_model->returnQueryObj($sql, $param);
        $cart_count = $resultObj->num_rows();
        $cart_action_url = base_url() . 'process/cartupdate';
        $cartTbObj = $this->cartTbObj($resultObj, $set_mb_id);
        $cartTb = $cartTbObj['returnTag'];
        $cartInTag = $cartTbObj['cartInTag'];
        $continue_ca_id = $cartTbObj['continue_ca_id'];
        $i = $cartTbObj['i'];
        $btnConfirmTag = "";
        if ($i == 0) {
            $btnConfirmTag = "<a href=\"" . base_url() . "\" class=\"btn_large\">쇼핑계속하기</a>";
        } else {
            $naverpay_button_js = $this->load->get_var('naverpay_button_js');
            $naverpay_request_js = $this->load->get_var('naverpay_request_js');
            $btnConfirmTag = "
            <input type=\"hidden\" name=\"url\" value=\"" . base_url() . "shop/orderform\">
            <input type=\"hidden\" name=\"records\" value=\"{$i}\">
            <input type=\"hidden\" name=\"act\" value=\"\">
            <button type=\"button\" onclick=\"return form_check('buy');\" class=\"btn_large wset\">선택상품주문</button>";
            if ($naverpay_button_js) {
                $btnConfirmTag = "<div class=\"cart-naverpay\">{$naverpay_request_js} {$naverpay_button_js}</div>";
            }
        }

        $data = array(
            'title' => $title,
            'cart_action_url' => $cart_action_url,
            'cart_count' => $cart_count,
            'cartTb' => $cartTb,
            'cartInTag' => $cartInTag,
            'btnConfirmTag' => $btnConfirmTag
        );
        $this->load->view('pc/shop/cart', $data);
    }

    private function cartTbObj($resultObj, $set_mb_id)
    {
        if (!$set_mb_id) {
            $urlencode = $this->load->get_var('urlencode');
            goto_url(base_url() . 'bbs/login?url=' . $urlencode);
        }

        $tot_point        = 0;
        $tot_sell_price = 0;
        $tot_opt_price    = 0;
        $tot_sell_qty    = 0;
        $tot_sell_amt    = 0;
        $i = 0;
        $returnTag = "";
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $result = $resultObj->result_array();
        $continue_ca_id = "";

        foreach ($result as $key => $row) {
            $gs = $this->global_lib->get_goods($row['gs_id']);

            // 합계금액 계산
            $sql = "SELECT SUM(IF(io_type = 1, (io_price * ct_qty),((io_price + ct_price) * ct_qty))) as price,
                            SUM(IF(io_type = 1, (0),(ct_point * ct_qty))) as point,
                            SUM(ct_qty) as qty,
                            SUM(io_price * ct_qty) as opt_price
                    FROM  shop_cart
                    WHERE index_no = ?
                    AND   mb_id = ?
                    AND   ct_select = 0";
            $data = array($row['index_no'], $set_mb_id);
            $sum = $this->query_model->returnOneArr($sql, $data);

            if ($i == 0) { // 계속쇼핑
                $continue_ca_id = $row['ca_id'];
            }

            $sell_price = $sum['price'];
            $sell_opt_price = $sum['opt_price'];
            $sell_qty = $sum['qty'];
            $sell_amt = $sum['price'] - $sum['opt_price'];

            $it_name = "";
            $mod_options = "";
            $this->load->vars(array("gs" => $gs));
            $it_options = $this->funcomplex_lib->print_item_options($row['index_no'], $set_mb_id);

            if ($it_options) {
                $mod_options = '<div class="sod_option_btn"><button type="button" class="btn_small bx-white mod_options">옵션변경/추가</button></div>';
                $it_name = '<div class="sod_opt">' . $it_options . '</div>';
            }

            if ($gs['type'] == "timesale") {
                $price = $this->gvcomplex_lib->get_timesale_goods_price($gs['index_no']);
            } else {
                $price = $this->gvcomplex_lib->get_sale_price($gs['index_no']);
            }

            // 포인트
            $point = 0;
            if ($is_member && $gs['is_point'] > 0) {
                $grade = $member['grade'];

                $sql = "SELECT gb_buy_point FROM shop_member_grade WHERE gb_no = ?";
                $param = array($grade);
                $result = $this->query_model->returnOneArr($sql, $param);

                $gb_point = $result['gb_buy_point']; // 적립률
                $point = (int) round((($price + $sell_opt_price) * $sell_qty) * ($gb_point * (1 / 100))); // 적립 포인트
            }

            // 배송비
            if ($gs['use_aff'])
                $sr = $this->partner_lib->get_partner($gs['mb_id']);
            else
                $sr = $this->global_lib->get_seller_cd($gs['mb_id']);

            $info = $this->funcomplex_lib->get_item_sendcost($row, $sell_price);
            $item_sendcost[] = $info['pattern'];

            $it_href = base_url() . 'shop/view?index_no=' . $row['gs_id'];
            $info['price'] = isset($info['price']) ? $info['price'] : 0;
            $returnTag .= "
            <tr>
                <td class=\"tac\">
                    <label for=\"ct_chk_{$i}\" class=\"sound_only\">상품</label>
                    <input type=\"checkbox\" name=\"ct_chk[{$i}]\" value=\"1\" id=\"ct_chk_{$i}\" checked=\"checked\">
                </td>
                <td class=\"tac\"><a href=\"{$it_href}\">" . ($this->funcomplex_lib->get_it_image($row['gs_id'], $gs['simg1'], 100, 100)) . "</a></td>
                <td class=\"td_name\">
                    <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$row['gs_id']}\">
                    <input type=\"hidden\" name=\"index_no[{$i}]\" value=\"{$row['index_no']}\">
                    <a href=\"{$it_href}\">{$gs['gname']}</a>
                    {$it_name}{$mod_options}
                </td>
                <td class=\"tac\">" . number_format($sell_qty) . "</td>
                <td class=\"tar\">" . number_format($sell_amt) . "</td>
                <td class=\"tar\">" . number_format($sell_price) . "</td>
                <td class=\"tar\">" . number_format($point) . "</td>
                <td class=\"tar\">" . number_format($info['price']) . "</td>
            </tr>";
            $tot_point        += $point;
            $tot_sell_price += $sell_price;
            $tot_opt_price    += $sell_opt_price;
            $tot_sell_qty    += $sell_qty;
            $tot_sell_amt    += $sell_amt;

            if (!$is_member)
                $tot_point = 0;

            $i++;
        }
        if ($i == 0) {
            $returnTag = '<tr><td colspan="8" class="empty_table">장바구니에 담긴 상품이 없습니다.</td></tr>';
        }
        // 배송비 검사
        $send_cost = 0;
        $com_send_cost = 0;
        $sep_send_cost = 0;
        $max_send_cost = 0;
        $cartInTag = "";
        if ($i > 0) {
            $k = 0;
            $condition = array();
            foreach ($item_sendcost as $key) {
                list($userid, $bundle, $price) = explode('|', $key);
                $condition[$userid][$bundle][$k] = ($price != "") ? $price : 0;
                $k++;
            }

            $com_array = array();
            $val_array = array();
            foreach ($condition as $key => $value) {
                if (isset($condition[$key]['묶음'])) {
                    $com_send_cost += array_sum($condition[$key]['묶음']); // 묶음배송 합산
                    $max_send_cost += max($condition[$key]['묶음']); // 가장 큰 배송비 합산
                    $com_array[] = max(array_keys($condition[$key]['묶음'])); // max key
                    $val_array[] = max(array_values($condition[$key]['묶음'])); // max value
                }
                if (isset($condition[$key]['개별'])) {
                    $sep_send_cost += array_sum($condition[$key]['개별']); // 묶음배송불가 합산
                    $com_array[] = array_keys($condition[$key]['개별']); // 모든 배열 key
                    $val_array[] = array_values($condition[$key]['개별']); // 모든 배열 value
                }
            }

            $send_cost = $com_send_cost + $sep_send_cost; // 총 배송비합계
            $tot_send_cost = $max_send_cost + $sep_send_cost; // 최종배송비
            $tot_final_sum = $send_cost - $tot_send_cost; // 배송비할인
            $tot_price = $tot_sell_price + $tot_send_cost; // 결제예정금액
            $cartInTag = $this->cartInFun($send_cost, $tot_send_cost, $tot_final_sum, $tot_price, $tot_point, $tot_sell_amt, $tot_opt_price, $tot_sell_qty, $tot_sell_price);
        }
        $returnObj = array('returnTag' => $returnTag, 'cartInTag' => $cartInTag, 'i' => $i, 'continue_ca_id' => $continue_ca_id);
        return $returnObj;
    }

    private function cartInFun($send_cost, $tot_send_cost, $tot_final_sum, $tot_price, $tot_point, $tot_sell_amt, $tot_opt_price, $tot_sell_qty, $tot_sell_price)
    {
        $member = $this->load->get_var('member');
        $member['point'] = isset($member['point']) ? $member['point'] : 0;
        $return2Tag = "
        <div id=\"sod_bsk_btn\">
            <div class=\"palt\"><button type=\"button\" onclick=\"return form_check('seldelete');\" class=\"btn_lsmall bx-red\">선택상품 삭제</button></div>
            <div class=\"part\"><button type=\"button\" onclick=\"return form_check('alldelete');\" class=\"btn_lsmall bx-white\">장바구니 비우기</button></div>
        </div>

        <div id=\"sod_bsk_tot\">
            <table class=\"wfull\">
            <tr>
                <td class=\"w50p\">
                    <h2 class=\"anc_tit\">장바구니에 담긴 상품통계</h2>
                    <div class=\"tbl_frm01 tbl_wrap\">
                        <table>
                        <colgroup>
                            <col class=\"w140\">
                            <col class=\"w140\">
                            <col>
                        </colgroup>
                        <tr>
                            <th scope=\"row\">포인트</th>
                            <td class=\"tar\">적립 포인트</td>
                            <td class=\"tar bl\">" . display_point($tot_point) . "</td>
                        </tr>
                        <tr>
                            <th scope=\"row\" rowspan=\"3\">상품</th>
                            <td class=\"tar\">상품금액 합계</td>
                            <td class=\"tar bl\">" . display_price2($tot_sell_amt) . "</td>
                        </tr>
                        <tr>
                            <td class=\"tar\">옵션금액 합계</td>
                            <td class=\"tar bl\">" . display_price2($tot_opt_price) . "</td>
                        </tr>
                        <tr>
                            <td class=\"tar\">주문수량 합계</td>
                            <td class=\"tar bl\">" . display_qty($tot_sell_qty) . "</td>
                        </tr>
                        <tr>
                            <td class=\"list2 tac bold\" colspan=\"2\">현재 포인트 보유잔액</td>
                            <td class=\"list2 tar bold\">" . display_point($member['point']) . "</td>
                        </tr>
                        </table>
                    </div>
                </td>
                <td class=\"w50p\">
                    <h2 class=\"anc_tit\">결제 예상금액 통계</h2>
                    <div class=\"tbl_frm01 tbl_wrap\">
                        <table>
                        <colgroup>
                            <col class=\"w140\">
                            <col class=\"w140\">
                            <col>
                        </colgroup>
                        <tr>
                            <th scope=\"row\">주문</th>
                            <td class=\"tar\">(A) 주문금액 합계</td>
                            <td class=\"tar bl\">" . display_price2($tot_sell_price) . "</td>
                        </tr>
                        <tr>
                            <th scope=\"row\" rowspan=\"3\">배송비</th>
                            <td class=\"tar\">상품별 배송비합계</td>
                            <td class=\"tar bl\">" . display_price2($send_cost) . "</td>
                        </tr>
                        <tr>
                            <td class=\"tar\">배송비할인</td>
                            <td class=\"tar bl\">(-) " . display_price2($tot_final_sum) . "</td>
                        </tr>
                        <tr>
                            <td class=\"tar\">(B) 최종배송비</td>
                            <td class=\"tar bl\">" . display_price2($tot_send_cost) . "</td>
                        </tr>
                        <tr>
                            <td class=\"list2 tac bold\" colspan=\"2\">결제예정금액 (A+B)</td>
                            <td class=\"list2 tar bold fc_red\">" . display_price2($tot_price) . "</td>
                        </tr>
                        </table>
                    </div>
                </td>
            </tr>
            </table>
        </div>";
        return $return2Tag;
    }

    #cart페이지에서 추가옵션
    public function cartoption()
    {
        $gs_id = trim($_REQUEST['gs_id']);
        $gs = $this->global_lib->get_goods($gs_id);
        $gs['goods_price'] = $this->gvcomplex_lib->get_sale_price($gs_id);
        $ca_id = $gs['ca_id'];

        $member = $this->load->get_var('member');

        // 최소, 최대 주문수량체크
        $it_buy_min_qty = 1;
        $it_buy_max_qty = 0;
        if (isset($gs['odr_min']) && $gs['odr_min'] >= 1) {
            $it_buy_min_qty    = (int)$gs['odr_min'];
        }

        if (isset($gs['odr_max']) && $gs['odr_max'] >= 0) {
            $it_buy_max_qty    = (int)$gs['odr_max'];
        }

        if (!isset($gs['index_no'])) {
            die('no-item');
        }

        $sql_search = " mb_id='{$member['id']}' and ct_select='0' and gs_id='$gs_id' ";

        // 장바구니 자료
        $sql = " select * from shop_cart where $sql_search order by io_type asc, index_no asc ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $result = $resultObj->result_array();

        // 판매가격
        $sql2 = " select * from shop_cart where $sql_search order by index_no asc limit 1 ";
        $row2 = $this->query_model->returnOneArr($sql2);

        if (!$resultObj->num_rows()) {
            die('no-cart');
        }

        $option_1 = $this->funcomplex_lib->get_item_options($gs_id, $gs['opt_subject']);
        $option1_tag = "";
        if ($option_1) {
            $option1_tag = "
            <div class=\"vi_txt_li\">
                <dl>
                    <dt>주문옵션(필수선택)</dt>
                </dl>
                {$option_1}
            </div>";
        }
        $option2_tag = "";
        $option_2 = $this->funcomplex_lib->get_item_supply($gs_id, $gs['spl_subject']);
        if ($option_2) {
            $option2_tag = "
            <div class=\"vi_txt_li\">
                <dl>
                    <dt>추가구성</dt>
                    <dd>추가구매를 원하시면 선택하세요</dd>
                </dl>
                {$option_2}
            </div>";
        }


        $optList = "";
        foreach ($result as $key => $row) {
            if (!$row['io_id'])
                $it_stock_qty = $this->global_lib->get_it_stock_qty($row['gs_id']);
            else
                $it_stock_qty = $this->global_lib->get_option_stock_qty($row['gs_id'], $row['io_id'], $row['io_type']);
            $plus = '';
            if ($row['io_price'] >= 0)
                $plus = '+';

            if (!$row['io_type'])
                $io_price = $plus . display_price(($row['io_price'] + $gs['goods_price']) * $row['ct_qty']);
            else
                $io_price = $plus . display_price($row['io_price'] * $row['ct_qty']);
            $cls = 'opt';
            if ($row['io_type'])
                $cls = 'spl';
            $optList .= "
            <li class=\"sit_{$cls}_list vi_txt_li\">
                <dl>
                    <input type=\"hidden\" name=\"io_type[{$gs_id}][]\" value=\"{$row['io_type']}\">
                    <input type=\"hidden\" name=\"io_id[{$gs_id}][]\" value=\"{$row['io_id']}\">
                    <input type=\"hidden\" name=\"io_value[{$gs_id}][]\" value=\"{$row['ct_option']}\">
                    <input type=\"hidden\" class=\"io_price\" value=\"{$row['io_price']}\">
                    <input type=\"hidden\" class=\"io_stock\" value=\"{$it_stock_qty}\">
                    <input type=\"hidden\" class=\"io_index_no\" value=\"{$row['index_no']}\">
                    <input type=\"hidden\" class=\"io_gs_id\" value=\"{$gs_id}\">
                    <dt class=\"op_vi_tit\"><span class=\"sit_opt_subj\">{$row['ct_option']}</span></dt>
                    <dd class=\"op_vi_txt\">
                        <button type=\"button\" class=\"defbtn_minus\">-</button>
                        <input type=\"text\" name=\"ct_qty[{$gs_id}][]\" value=\"{$row['ct_qty']}\" class=\"inp_opt\" size=\"2\">
                        <button type=\"button\" class=\"defbtn_plus\">+</button>
                        <span class=\"sit_opt_prc\">{$io_price}</span>
                        <button type=\"button\" class=\"defbtn_delete\">옵션삭제</button>
                    </dd>
                </dl>
            </li>";
        }
        $data = array(
            'gs_id' => $gs_id,
            'row2' => $row2,
            'option1_tag' => $option1_tag,
            'option2_tag' => $option2_tag,
            'cart_obt_list' => $optList,
            'it_buy_min_qty' => $it_buy_min_qty,
            'it_buy_max_qty' => $it_buy_max_qty,
            'ca_id' => $ca_id
        );
        $this->load->view('pc/shop/cart/cartOpt', $data);
    }

    private function cart_option_list($result, $gs_id, $gs)
    {
        $returnTag = "";
        foreach ($result as $key => $row) {
            if (!$row['io_id'])
                $it_stock_qty = $this->global_lib->get_it_stock_qty($row['gs_id']);
            else
                $it_stock_qty = $this->global_lib->get_option_stock_qty($row['gs_id'], $row['io_id'], $row['io_type']);
            $plus = '';
            if ($row['io_price'] >= 0)
                $plus = '+';

            if (!$row['io_type'])
                $io_price = $plus . display_price(($row['io_price'] + $gs['goods_price']) * $row['ct_qty']);
            else
                $io_price = $plus . display_price($row['io_price'] * $row['ct_qty']);
            $cls = 'opt';
            if ($row['io_type'])
                $cls = 'spl';
            $returnTag .= "
            <li class=\"sit_{$cls}_list vi_txt_li\">
                <dl>
                    <input type=\"hidden\" name=\"io_type[{$gs_id}][]\" value=\"{$row['io_type']}\">
                    <input type=\"hidden\" name=\"io_id[{$gs_id}][]\" value=\"{$row['io_id']}\">
                    <input type=\"hidden\" name=\"io_value[{$gs_id}][]\" value=\"{$row['ct_option']}\">
                    <input type=\"hidden\" class=\"io_price\" value=\"{$row['io_price']}\">
                    <input type=\"hidden\" class=\"io_stock\" value=\"{$it_stock_qty}\">
                    <dt class=\"op_vi_tit\"><span class=\"sit_opt_subj\">{$row['ct_option']}</span></dt>
                    <dd class=\"op_vi_txt\">
                        <button type=\"button\" class=\"defbtn_minus\">-</button>
                        <input type=\"text\" name=\"ct_qty[{$gs_id}][]\" value=\"{$row['ct_qty']}\" class=\"inp_opt\" size=\"2\">
                        <button type=\"button\" class=\"defbtn_plus\">+</button>
                        <span class=\"sit_opt_prc\">{$io_price}</span>
                        <button type=\"button\" class=\"defbtn_delete\">삭제</button>
                    </dd>
                </dl>
            </li>";
        }
        return $returnTag;
    }

    #orderinquiry주문한것 더보기
    public function orderinquiry()
    {
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/orderinquiry');
        }
        $this->template->header();
        $this->orderinquirySkin();
        $this->template->footer();
    }

    private function orderinquirySkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');

        $od_id = $this->input->post('od_id');
        $od_pwd = $this->input->post('od_pwd');
        $urlencode = $this->input->post('urlencode');

        $page = $this->input->get('page');

        // 회원인 경우
        if ($is_member)
            $sql_common = " from shop_order where mb_id = '{$member['id']}' and dan <> '0' ";
        else if ($od_id && $od_pwd) { // 비회원인 경우 주문서번호와 비밀번호가 넘어왔다면
            #주문번호로 주문등록한 날짜 추출
            $sql = "select od_time from shop_order where od_id=?";
            $param = array($od_id);
            $res = $this->query_model->returnOneArr($sql, $param);
            $od_time = $res['od_time'];
            #날짜와 지금 입력한 패스워드를 통해서 이전에 등록한 비번과 동일하게 추출
            $od_pwd = $this->global_lib->reg_sql_password($od_pwd, $od_time);
            #그리고 조회하기
            $sql_common = " from shop_order where od_id = '$od_id' and od_pwd = '$od_pwd' and dan <> '0' ";
        } else // 그렇지 않다면 로그인으로 가기
            goto_url(base_url() . 'bbs/login?url=' . $urlencode);

        // 테이블의 전체 레코드수만 얻음
        $sql = " select count(DISTINCT od_id) as cnt " . $sql_common;
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];
        // 비회원 주문확인시 비회원의 모든 주문이 다 출력되는 오류 수정
        // 조건에 맞는 주문서가 없다면
        if ($total_count == 0) {
            if ($is_member) // 회원일 경우는 메인으로 이동
                alert('주문이 존재하지 않습니다.', base_url());
            else // 비회원일 경우는 이전 페이지로 이동
                alert('주문이 존재하지 않습니다.');
        }
        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        // 비회원 주문확인의 경우 바로 주문서 상세조회로 이동
        if (!$is_member) {
            $sql = "select od_id, od_time, od_ip from shop_order where od_id = ? and od_pwd = ?";
            $data = array($od_id, $od_pwd);
            $row = $this->query_model->returnOneArr($sql, $data);
            if ($row['od_id']) {
                $uid = md5($row['od_id'] . $row['od_time'] . $row['od_ip']);
                set_session('ss_orderview_uid', $uid);
                goto_url(base_url() . 'shop/orderinquiryview?od_id=' . $row['od_id'] . '&uid=' . $uid);
            }
        }
        $sql_order = " group by od_id order by index_no desc ";
        $sql = " select * $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $title = "주문/배송조회";
        $aside_my = $this->template->aside_my();
        $tableTag = $this->orderinquiryTable($result);
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?page=');
        $data = array('title' => $title, 'aside_my' => $aside_my, 'tableTag' => $tableTag, 'pagination' => $pagination);
        $this->load->view('pc/shop/orderinquiry', $data);
    }

    private function orderinquiryTable($result)
    {
        $returnTable = "";
        $gw_status = $this->config->item('gw_status');
        $i = 0;
        foreach ($result as $key => $row) {
            $sql = " select * from shop_cart where od_id = '$row[od_id]' ";
            $sql .= " order by io_type asc, index_no asc ";
            $resObj = $this->query_model->returnQueryObj($sql);
            $rowspan = $resObj->num_rows() + 1;
            $res = $resObj->result_array();
            $k = 0;
            foreach ($res as $key => $ct) {
                $od = $this->global_lib->get_order($ct['od_no']);
                $gs = unserialize($od['od_goods']);

                $hash = md5($od['gs_id'] . $od['od_no'] . $od['od_id']);
                $dlcomp = explode('|', trim($od['delivery']));
                $href = base_url() . 'shop/view?index_no=' . $od['gs_id'];
                $review_flag = $od['review_flag'];
                $user_ok = $od['user_ok'];

                if ($k == 0) {
                    $returnTable .= "
                    <tr>
                        <td class=\"tac\" rowspan=\"{$rowspan}\">
                            <p class=\"bold\">" . substr($od['od_time'], 0, 10) . "</p>
                            <p class=\"padt5\"><a href=\"" . base_url() . "shop/orderinquiryview?od_id={$od['od_id']}\" class=\"btn_small grey\">상세보기</a></p>
                        </td>
                    </tr>";
                }
                $returnTable .= "
                <tr class=\"rows\">
                    <td>
                        <div class=\"ini_wrap\">
                            <table class=\"wfull\">
                            <colgroup>
                                <col class=\"w70\">
                                <col>
                            </colgroup>
                            <tr>
                                <td class=\"vat tal\"><a href=\"{$href}\">" . $this->funcomplex_lib->get_od_image($od['od_id'], $gs['simg1'], 60, 60) . "</a></td>
                                <td class=\"vat tal\">
                                <a href=\"{$href}\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a>
                                <p class=\"padt3 fc_999\">주문번호 : {$od['od_id']} / 수량 : " . display_qty($od['sum_qty']) . "  / 배송비 : " . display_price($od['baesong_price']) . "</p>";
                if ($od['dan'] == 5) {
                    $returnTable .= "<p class=\"padt3\">";
                    if (is_null_time($od['user_date'])) {
                        $returnTable .= "<a href=\"javascript:final_confirm('{$hash}');\" class=\"btn_ssmall red\">구매확정</a>";
                    }
                    if ($review_flag == 1 && $user_ok == 1) {
                        $returnTable .= "<a href=\"" . base_url() . "shop/orderreview?gs_id={$od['gs_id']}&od_id={$od['od_id']}&od_no={$od['od_no']}\" onclick=\"win_open(this, 'winorderreview', '650', '700','yes');return false;\" class=\"btn_ssmall bx-white\">구매후기 작성</a>";
                    }
                    $returnTable .= "</p>";
                }
                $returnTable .= "
                                </td>
					        </tr>
					        </table>
				        </div>
			        </td>
                    <td class=\"tar\">" . display_price($od['use_price']) . "</td>
                    <td class=\"tac\">";
                $gw_status[$od['dan']] = isset($gw_status[$od['dan']]) ? $gw_status[$od['dan']] : "";
                $returnTable .= "<p>{$gw_status[$od['dan']]}</p>";
                if (isset($dlcomp[0]) && $od['delivery_no']) {
                    $returnTable .= "<p class=\"padt3 fc_90\">{$dlcomp[0]}<br>{$od['delivery_no']}</p>";
                }
                if (isset($dlcomp[1]) && $od['delivery_no']) {
                    $returnTable .= "<p class=\"padt3\">" . get_delivery_inquiry($od['delivery'], $od['delivery_no'], 'btn_ssmall') . "</p>";
                }
                $returnTable .= "
                    </td>
		        </tr>";
                $k++;
            }
            $i++;
        }
        if ($i == 0) {
            $returnTable = '<tr><td colspan="4" class="empty_list">자료가 없습니다.</td></tr>';
        }
        return $returnTable;
    }

    #결제가 완료된후 나오는 페이지들 시작
    #orderinquiryview화면
    public function orderinquiryview()
    {
        $od_id = $this->input->get('od_id');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/orderinquiryview?od_id=' . $od_id);
        }
        $this->template->header();
        $this->orderinquiryviewSkin();
        $this->template->footer();
    }

    private function orderinquiryviewSkin()
    {
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $default = $this->load->get_var('default');
        $title = "주문상세내역";
        $od_id = $this->input->get('od_id');
        $uid = $this->input->get('uid');
        if (!$is_member) {
            if (get_session('ss_orderview_uid') != $uid)
                alert("직접 링크로는 주문서 조회가 불가합니다.\\n\\n주문조회 화면을 통하여 조회하시기 바랍니다.", base_url());
        }


        $sql = "select * from shop_order where od_id = ?";
        $data = array($od_id);
        $od = $this->query_model->returnOneArr($sql, $data);
        if (!$od['od_id'] || (!$is_member && md5($od['od_id'] . $od['od_time'] . $od['od_ip']) != get_session('ss_orderview_uid')) || $od['mb_id'] != $member['id']) {
            alert("조회하실 주문서가 없습니다.");
        }

        $gs = $this->global_lib->get_goods($od['gs_id']);

        $lgTxt = "";

        $stotal = $this->global_lib->get_order_spay($od_id); // 총계
        // 결제정보처리
        $app_no_subj = '';
        $disp_bank = true;
        $disp_receipt = false;
        $easy_pay_name = '';

        if ($od['paymethod'] == '신용카드' || $od['paymethod'] == 'KAKAOPAY') {
            $app_no_subj = '승인번호';
            $app_no = $od['od_app_no'];
            $disp_bank = false;
            $disp_receipt = true;
        } else if ($od['paymethod'] == '간편결제') {
            $app_no_subj = '승인번호';
            $app_no = $od['od_app_no'];
            $disp_bank = false;
            switch ($od['od_pg']) {
                case 'lg':
                    $easy_pay_name = 'PAYNOW';
                    break;
                case 'inicis':
                    $easy_pay_name = 'KPAY';
                    break;
                case 'kcp':
                    $easy_pay_name = 'PAYCO';
                    break;
                default:
                    break;
            }
        } else if ($od['paymethod'] == '휴대폰') {
            $app_no_subj = '휴대폰번호';
            $app_no = $od['bank'];
            $disp_bank = false;
            $disp_receipt = true;
        } else if ($od['paymethod'] == '가상계좌' || $od['paymethod'] == '계좌이체') {
            $app_no_subj = '거래번호';
            $app_no = $od['od_tno'];
        }

        // 불법접속을 할 수 없도록 세션에 아무값이나 저장하여 hidden 으로 넘겨서 다음 페이지에서 비교함
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        $aside_my = $this->template->aside_my();
        $orderObj = $this->orderinquirtyTable($od_id);
        $returnTb = $orderObj['returnTag'];
        $custom_cancel = $orderObj['custom_cancel'];
        $st_cancel_price = $orderObj['st_cancel_price'];
        #주문총액
        $tot_price = display_price($stotal['price']);
        $cancel_price = $stotal['cancel'];
        $refund_mileage_price = display_price($stotal['refund_mileage_price']);
        $use_price = display_price($stotal['useprice']);
        $couDis = "";
        if ($stotal['coupon']) {
            $couDis = "
            <dt>쿠폰할인</dt>
            <dd><strong>" . display_price($stotal['coupon']) . "</strong></dd>";
        }
        $usepoint = "";
        if ($stotal['usepoint']) {
            $usepoint = "
            <dt>포인트결제</dt>
            <dd><strong>" . display_point($stotal['usepoint']) . "</strong></dd>";
        }
        $usemileage = "";
        if ($stotal['usemileage']) {
            $usemileage = "
            <dt>예치금결제</dt>
            <dd><strong>" . display_point($stotal['usemileage']) . "</strong></dd>";
        }
        $baesong = "";
        if ($stotal['baesong']) {
            $baesong = "
            <dt>배송비</dt>
            <dd><strong>" . display_price($stotal['baesong']) . "</strong></dd>";
        }

        $goodsPrice = display_price($stotal['price']);  #포인트적립 
        $totprice = display_price($stotal['price'] + $stotal['baesong']); #총계
        $totpoint = display_point($stotal['point']);  #포인트적립 
        $paymethod = ($easy_pay_name ? $easy_pay_name . '(' . $od['paymethod'] . ')' : $od['paymethod']);
        $pay_price = display_price($stotal['useprice']); #결재금액
        #결제일시
        $receipt_time = "";
        if (!is_null_time($od['receipt_time'])) {
            $receipt_time = "
            <tr>
				<th scope=\"row\">결제일시</th>
				<td>{$od['receipt_time']}</td>
			</tr>";
        }
        $app_noTag = "";
        if ($app_no_subj) {
            $app_noTag = "
            <tr>
				<th scope=\"row\">{$app_no_subj}</th>
				<td>{$app_no}</td>
			</tr>";
        }

        $disp_bankTag = "";
        // 계좌정보
        if ($disp_bank) {
            $disp_bankTag = "
            <tr>
                <th scope=\"row\">입금자명</th>
                <td>" . $this->funcomplex_lib->get_text($od['deposit_name']) . "</td>
            </tr>
            <tr>
                <th scope=\"row\">입금계좌</th>
                <td>" . $this->funcomplex_lib->get_text($od['bank']) . "</td>
            </tr>";
        }


        $disp_receiptTag = "";
        if (!($od['paymethod'] == '무통장' || $od['paymethod'] == '포인트' || $od['paymethod'] == '예치금')) { #무통장이면 안 나오게 수정
            include_once('./Bill/iamport.php');
            $merchant_uid = $od['merchant_uid'];
            $result = $iamport->findByMerchantUID($merchant_uid); //IamportResult 를 반환(success, data, error)
            $tid = $result->data->pg_tid;
            if ($disp_receipt) {
                $disp_receiptTag .= "
                <tr>
                    <th scope=\"row\">영수증</th>
                    <td>";
                $card_receipt_script = 'window.open(\'https://test.smilepay.co.kr/issue/IssueLoader.jsp?TID=' . $tid . '&type=0\',\'showreceipt\',\'width=420,height=700,scrollbars=no,resizable=no\');';
                $disp_receiptTag .= "<a href=\"javascript:;\" onclick=\"{$card_receipt_script}\" class=\"btn_small\">영수증 출력</a>";
                $disp_receiptTag .= "
                    </td>
                </tr>";
            }
        }
        $de_taxsave_useTag = "";
        $OM_CASH_RECEIPT_UR = $this->load->get_var('OM_CASH_RECEIPT_UR');
        // 현금영수증 발급을 사용하는 경우에만
        #현금영수증 발급을 우선 안 하는 것으로 변경
        if ($default['de_taxsave_use'] && false) {
            // 미수금이 없고 현금일 경우에만 현금영수증을 발급 할 수 있습니다.
            if (!is_null_time($od['receipt_time']) && ($od['paymethod'] == '무통장' || $od['paymethod'] == '계좌이체' || $od['paymethod'] == '가상계좌')) {
                $de_taxsave_useTag .= "
                <tr>
                    <th scope=\"row\">현금영수증</th>
                    <td>";
                if ($od['od_cash']) {
                    if ($od['od_pg'] == 'inicis') {
                        $cash = unserialize($od['od_cash_info']);
                        $cash_receipt_script = 'window.open(\'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/Cash_mCmReceipt.jsp?noTid=' . $cash['TID'] . '&clpaymethod=22\',\'showreceipt\',\'width=380,height=540,scrollbars=no,resizable=no\');';
                    }
                    $de_taxsave_useTag .= "<a href=\"javascript:;\" onclick=\"{$cash_receipt_script}\" class=\"btn_small\">현금영수증 확인하기</a>";
                } else {
                    $de_taxsave_useTag .= "<a href=\"javascript:;\" onclick=\"window.open('" . base_url() . "shop/taxsave?od_id={$od_id}', 'taxsave', 'width=550,height=400,scrollbars=1,menus=0');\" class=\"btn_small\">현금영수증 발급하기</a>";
                }
                $de_taxsave_useTag .= "
                    </td>
                </tr>";
            }
        }

        #주문하신 분
        $odName = $this->funcomplex_lib->get_text($od['name']);
        $odTelepone = $this->funcomplex_lib->get_text($od['telephone']);
        $odCellepone = $this->funcomplex_lib->get_text($od['cellphone']);
        $odAddress = ($this->funcomplex_lib->get_text(sprintf("(%s)", $od['zip'])) . ' ' . ($this->funcomplex_lib->print_address($od['addr1'], $od['addr2'], $od['addr3'], $od['addr_jibeon'])));
        $odEmail = $this->funcomplex_lib->get_text($od['email']);

        #받으시는 분
        $odBName = $this->funcomplex_lib->get_text($od['b_name']);
        $odBTelepone = $this->funcomplex_lib->get_text($od['b_telephone']);
        $odBCellepone = $this->funcomplex_lib->get_text($od['b_cellphone']);
        $odBAddress = ($this->funcomplex_lib->get_text(sprintf("(%s)", $od['b_zip'])) . ' ' . ($this->funcomplex_lib->print_address($od['b_addr1'], $od['b_addr2'], $od['b_addr3'], $od['b_addr_jibeon'])));
        $odBMemo = "";
        if ($od['memo']) {
            $odBMemo = "
            <tr>
				<th scope=\"row\">전하실 말씀</th>
				<td>" . ($this->funcomplex_lib->conv_content($od['memo'], 0)) . "</td>
			</tr>";
        }

        $noCancelHistory = "";
        // 취소한 내역이 없다면
        if (($gs['type'] == "timesale" && $gs['isopen'] == 1 && $st_cancel_price == 0 && $custom_cancel) || ($gs['type'] == "normal" && $st_cancel_price == 0 && $custom_cancel)) {
            $noCancelHistory .= "
            <section id=\"sod_fin_cancel\">
                <h2>주문취소</h2>
                <button type=\"button\" onclick=\"document.getElementById('sod_fin_cancelfrm').style.display='block';\" class=\"btn_medium wset\">주문 취소하기</button>

                <div id=\"sod_fin_cancelfrm\">
                    <form method=\"post\" action=\"" . base_url() . "process/orderinquirycancel\" onsubmit=\"return fcancel_check(this);\">
                    <input type=\"hidden\" name=\"od_id\"  value=\"{$od_id}\">
                    <input type=\"hidden\" name=\"token\"  value=\"{$token}\">
                    <input type=\"hidden\" name=\"type\"  value=\"{$gs['type']}\">
                    <label for=\"cancel_memo\">취소사유</label>
                    <input type=\"text\" name=\"cancel_memo\" id=\"cancel_memo\" required class=\"frm_input required\" size=\"40\" maxlength=\"100\">
                    <input type=\"submit\" value=\"확인\" class=\"btn_small\">
                    </form>
                </div>
            </section>";
        }
        $data = array(
            'title' => $title,
            'lgTxt' => $lgTxt,
            'aside_my' => $aside_my,
            'od_id' => $od_id,
            'returnTb' => $returnTb,
            'tot_price' => $tot_price,
            'cancel_price' => $cancel_price,
            'use_price' => $use_price,
            'refund_mileage_price' => $refund_mileage_price,
            'couDis' => $couDis,
            'usepoint' => $usepoint,
            'usemileage' => $usemileage,
            'baesong' => $baesong,
            'goodsPrice' => $goodsPrice,
            'totprice' => $totprice,
            'totpoint' => $totpoint,
            'od' => $od,
            'gs' => $gs,
            'pay_price' => $pay_price,
            'paymethod' => $paymethod,
            'pay_price' => $pay_price,
            'receipt_time' => $receipt_time,
            'app_noTag' => $app_noTag,
            'disp_bankTag' => $disp_bankTag,
            'disp_receiptTag' => $disp_receiptTag,
            'de_taxsave_useTag' => $de_taxsave_useTag,
            'odName' => $odName,
            'odTelepone' => $odTelepone,
            'odCellepone' => $odCellepone,
            'odAddress' => $odAddress,
            'odEmail' => $odEmail,
            'odBName' => $odBName,
            'odBTelepone' => $odBTelepone,
            'odBCellepone' => $odBCellepone,
            'odBAddress' => $odBAddress,
            'odBMemo' => $odBMemo,
            'noCancelHistory' => $noCancelHistory
        );
        $this->load->view('pc/shop/orderinquiryview', $data);
    }

    private function orderinquirtyTable($od_id)
    {
        $gw_status = $this->config->item('gw_status');
        $member = $this->load->get_var('member');
        $returnTag = "";
        $st_count1 = $st_count2 = $st_cancel_price = 0;
        $custom_cancel = false;

        $sql = " SELECT * FROM shop_order WHERE od_id = ? GROUP BY od_id ORDER BY index_no DESC ";
        $param = array($od_id);
        $result = $this->query_model->returnArr($sql, $param);
        foreach ($result as $key => $row) {
            $sql = " SELECT * FROM shop_cart WHERE od_id = '$od_id' ";
            $sql .= " ORDER BY io_type ASC, index_no ASC ";
            $resObj = $this->query_model->returnQueryObj($sql);
            $rowspan = $resObj->num_rows() + 1;
            $res = $resObj->result_array();
            $k = 0;

            foreach ($res as $key => $ct) {
                $rw = $this->global_lib->get_order($ct['od_no']);
                $gs = unserialize($rw['od_goods']);

                $hash = md5($rw['gs_id'] . $rw['od_no'] . $rw['od_id']);
                $dlcomp = explode('|', trim($rw['delivery']));

                $href = base_url() . 'shop/view?index_no=' . $rw['gs_id'];

                $it_name = "";
                $it_options = $this->funcomplex_lib->print_complete_options($ct['gs_id'], $ct['od_no']);
                if ($it_options) {
                    $it_name = '<div class="sod_opt">' . $it_options . '</div>';
                }
                if ($k == 0) {
                    $returnTag .= "
                    <tr>
                        <td class=\"tac\" rowspan=\"{$rowspan}\">
                            <p class=\"bold\">" . substr($rw['od_time'], 0, 10) . "</p>
                            <p class=\"padt5\"><a href=\"" . base_url() . "shop/orderprint?od_id={$rw['od_id']}\" onclick=\"win_open(this,'winorderprint','670','600','yes');return false;\" class=\"btn_small bx-white\"><i class=\"fa fa-print fs14 vam marb2 marr3\"></i> 인쇄</a></p>
                        </td>
                    </tr>";
                }
                $returnTag .= "
                    <tr class=\"rows\">
                        <td>
                            <div class=\"ini_wrap\">
                                <table class=\"wfull\">
                                <colgroup>
                                    <col class=\"w70\">
                                    <col>
                                </colgroup>
                                <tr>
                                    <td class=\"vat tal\"><a href=\"{$href}\">" . $this->funcomplex_lib->get_od_image($rw['od_id'], $gs['simg1'], 60, 60) . "</a></td>
                                    <td class=\"vat tal\">
                                        <a href=\"{$href}\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a>{$it_name}
                                        <p class=\"padt3 fc_999\">주문번호 : {$rw['od_id']} / 수량 : " . display_qty($rw['sum_qty']) . " / 배송비 : " . display_price($rw['baesong_price']) . "</p>";
                if ($rw['dan'] == 5) {
                    $returnTag .= "<p class=\"padt3\">";
                    if (is_null_time($rw['user_date'])) {
                        $returnTag .= "<a href=\"javascript:final_confirm('{$hash}');\" class=\"btn_ssmall red\">구매확정</a>";
                    }
                    if ($rw['user_ok'] == 1 && $rw['review_flag'] == 1) {
                        $returnTag .= "<a href=\"" . base_url() . "shop/orderreview?gs_id={$rw['gs_id']}&od_id={$rw['od_id']}&od_no={$rw['od_no']}\" onclick=\"win_open(this, 'winorderreview', '650', '700','yes');return false;\" class=\"btn_ssmall bx-white\">구매후기 작성</a>";
                    }
                    $returnTag .= "</p>";
                }
                $returnTag .= "
                                    </td>
                                </tr>
                                </table>
                            </div>
                        </td>
                        <td class=\"tar\">" . display_price($rw['use_price']) . "</td>
                        <td class=\"tac\">
                            <p>{$gw_status[$rw['dan']]}</p>";
                if (isset($dlcomp[0]) && $rw['delivery_no']) {
                    $returnTag .= "<p class=\"padt3 fc_90\">{$dlcomp[0]}<br>{$rw['delivery_no']}</p>";
                }
                if (isset($dlcomp[1]) && $rw['delivery_no']) {
                    $returnTag .= "<p class=\"padt3\">" . get_delivery_inquiry($rw['delivery'], $rw['delivery_no'], 'btn_ssmall') . "</p>";
                }
                $returnTag .= "</td>
                    </tr>";
                $st_count1++;
                if (in_array($rw['dan'], array('1', '2', '3')))
                    $st_count2++;

                $st_cancel_price += $rw['cancel_price'];

                $k++;
            }
        }
        // 주문상태가 배송중 이전 단계이면 고객 취소 가능
        if ($st_count1 > 0 && $st_count1 == $st_count2)
            $custom_cancel = true;
        $returnObj = array('returnTag' => $returnTag, 'st_cancel_price' => $st_cancel_price, 'custom_cancel' => $custom_cancel);
        return $returnObj;
    }

    #orderkakaopay화면
    public function orderkakaopay()
    {
        $od_id = $this->input->get('od_id');
        $sql = "select * from shop_order where od_id=?";
        $param = array($od_id);
        $od = $this->query_model->returnOneArr($sql, $param);
        if (!$od['od_id']) {
            alert("결제할 주문서가 없습니다.");
        }
        $this->template->header();
        $this->orderkakaopaySkin($od_id, $od);
        $this->template->footer();
    }

    private function orderkakaopaySkin($od_id, $od)
    {
        $title = "결제하기";
        $default = $this->load->get_var('default');
        set_session('ss_order_id', $od_id);
        $stotal = $this->global_lib->get_order_spay($od_id); // 총계
        $tot_price = get_session('tot_price'); // 결제금액
        $order_action_url = base_url() . 'process/orderformresult';
        $kakaoJs = "";
        $goods = '';
        $goods_count = -1;
        $goods_cnt = 0;

        $sql = " select * from shop_cart where od_id = ? and ct_select = '0' group by gs_id order by index_no ";
        $param = array($od_id);
        $result = $this->query_model->returnArr($sql, $param);
        $kakaoTableTag = "";
        foreach ($result as $key => $row) {
            $rw = $this->global_lib->get_order($row['od_no']);
            $gs = $this->global_lib->get_goods($row['gs_id'], 'gname,simg1');

            if (!$goods)
                $goods = preg_replace("/\'|\"|\||\,|\&|\;/", "", $gs['gname']);

            $goods_count++;

            $it_name = stripslashes($gs['gname']);
            $it_options = $this->funcomplex_lib->print_complete_options($row['gs_id'], $od_id);
            if ($it_options) {
                $it_name .= '<div class="sod_opt">' . $it_options . '</div>';
            }
            $goods_cnt += $rw['sum_qty'];
            $kakaoTableTag .= "
            <tr>
                <td class=\"tac taimg\">" . $this->funcomplex_lib->get_it_image($row['gs_id'], $gs['simg1'], 80, 80) . "</td>
                <td class=\"td_name\">{$it_name}</td>
                <td class=\"tac\">" . number_format($rw['sum_qty']) . "</td>
                <td class=\"tar\">" . number_format($rw['goods_price']) . "</td>
                <td class=\"tar\">" . number_format($rw['use_price']) . "</td>
                <td class=\"tar\">" . number_format($rw['sum_point']) . "</td>
                <td class=\"tar\">" . number_format($rw['baesong_price']) . "</td>
            </tr>";
        }
        if ($goods_count) $goods .= ' 외 ' . $goods_count . '건';
        // 복합과세처리
        $comm_tax_mny  = 0; // 과세금액
        $comm_vat_mny  = 0; // 부가세
        $comm_free_mny = 0; // 면세금액
        $de_tax_flagJs = "";
        $de_tax_flagJs2 = "";
        $de_tax_flagJs3 = "";
        if ($default['de_tax_flag_use']) {
            $info = $this->global_lib->comm_tax_flag($od_id);
            $comm_tax_mny  = $info['comm_tax_mny'];
            $comm_vat_mny  = $info['comm_vat_mny'];
            $comm_free_mny = $info['comm_free_mny'];
            $de_tax_flagJs = "
            f.tax.value         = f.comm_vat_mny.value;
            f.taxfree.value     = f.comm_free_mny.value;";
            $de_tax_flagJs2 = "
            var comm_tax_mny =f.comm_tax_mny.value;
            var comm_vat_mny =f.comm_vat_mny.value;
            var comm_free_mny =f.comm_free_mny.value;
            ";
            $de_tax_flagJs3 = "
            'comm_tax_mny':comm_tax_mny,
            'comm_vat_mny':comm_vat_mny,
            'comm_free_mny':comm_free_mny,
            ";
        }

        $stotal_price = display_price($stotal['price']);
        $stotal_baesong = display_price($stotal['baesong']);
        $stotal_coupon = display_price($stotal['coupon']);
        $stotal_usepoint = display_price($stotal['usepoint']);
        $stotal_useprice = display_price($stotal['useprice']);
        $odAdd = $this->funcomplex_lib->print_address($od['addr1'], $od['addr2'], $od['addr3'], $od['addr_jibeon']);
        $odMemo = "";
        if ($od['memo']) {
            $odMemo = "
            <tr>
                <th scope=\"row\">전하실 말씀</th>
                <td>" . $this->funcomplex_lib->conv_content($od['memo'], 0) . "</td>
            </tr>";
        }
        $dis_tot_price = display_price($tot_price);
        $kakaoPayTag = "";
        if ($default['de_kakao_use']) {
            $kakaoPayTag = "
            <div id=\"display_pay_button\" class=\"btn_confirm\">
                <input type=\"button\" value=\"결제하기\" onclick=\"forderform_check(this.form);\" class=\"btn_large wset\">
                <a href=\"" . base_url() . "\" class=\"btn_large bx-white\">취소</a>
            </div>
            <div id=\"display_pay_process\" style=\"display:none\">
                <img src=\"" . base_url() . "img/ajax-loader.gif\" alt=\"\">
                <span>주문완료 중입니다. 잠시만 기다려 주십시오.</span>
            </div>";
        }
        $kakaoOrderForm2 = "";
        if ($default['de_kakao_use']) {
            $kakaoOrderForm2 = $this->KakaoOrderform2($od_id, $od, $tot_price, $comm_tax_mny, $comm_vat_mny, $comm_free_mny, $goods, $goods_cnt);
        }

        $data = array(
            'title' => $title,
            'order_action_url' => $order_action_url,
            'kakaoTableTag' => $kakaoTableTag,
            'stotal_price' => $stotal_price,
            'stotal_baesong' => $stotal_baesong,
            'stotal_coupon' => $stotal_coupon,
            'stotal_usepoint' => $stotal_usepoint,
            'stotal_useprice' => $stotal_useprice,
            'odAdd' => $odAdd,
            'odMemo' => $odMemo,
            'dis_tot_price' => $dis_tot_price,
            'kakaoPayTag' => $kakaoPayTag,
            'kakaoOrderForm2' => $kakaoOrderForm2,
            'de_tax_flagJs' => $de_tax_flagJs,
            'od_id' => $od_id,
            'de_tax_flagJs2' => $de_tax_flagJs2,
            'de_tax_flagJs3' => $de_tax_flagJs3
        );
        $this->load->view('pc/shop/orderkakaopay', $data);
    }

    private function orderKakaoJs()
    {
        $returnJs = "
            <script type=\"text/javascript\">

            function getTxnId(frm) {
                // console.log(frm);
                var base_url=document.getElementById('base_url').value;
                $.ajax({
                    url: base_url+\"process/kakaopay_getTxnId\",
                    type: \"POST\",
                    data: $(\"#kakaopay_request input\").serialize(),
                    dataType: \"json\",
                    async: false,
                    cache: false,
                    success: function(data) {
                        if(data.msg){
                            alert(data.msg);
                        }else{
                            if(data.pc != ''){
                                popup_kakao(data.pc);
                            }else if(data.mobile != ''){
                                popup_kakao(data.mobile);
                            }
                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }

            // pc에서 결제할 경우.
            function popup_kakao(link){
                var win = window.open('','','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=540,height=700,left=100,top=100');
                win.document.write('<iframe width=100%, height=650 src='+link+'frameborder=0 allowfullscreen></iframe><script>var meta = document.createElement(\"meta\"); meta.name = \"viewport\"; meta.content=\"user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width\"; document.head.append(meta);<\/script>');
            }

            function setChildValue(name){
                name = JSON.parse(name);
                var tid = $(\"<input type='hidden' value='\"+name.tid+\"' name = 'tid' >\");
                var aid = $(\"<input type='hidden' value='\"+name.aid+\"' name = 'aid' >\");
                var time = $(\"<input type='hidden' value='\"+name.approved_at+\"' name = 'app_time' >\");
                var total = $(\"<input type='hidden' value='\"+name.total+\"' name='amount' >\");

                $(\"#forderform\").append(tid);
                $(\"#forderform\").append(aid);
                $(\"#forderform\").append(time);
                $(\"#forderform\").append(total);
                
                $(\"#forderform\").submit();
            }
            </script>
        ";
        return $returnJs;
    }

    private function KakaoOrderform2($od_id, $od, $tot_price, $comm_tax_mny, $comm_vat_mny, $comm_free_mny, $goods, $goods_cnt)
    {
        $default = $this->load->get_var('default');
        $remoteaddr = $_SERVER['REMOTE_ADDR'];
        $serveraddr = $_SERVER['SERVER_ADDR'];
        $taxTag = "";
        if ($default['de_tax_flag_use']) {
            $taxTag = "
                <input type=\"hidden\" name=\"comm_tax_mny\" value=\"{$comm_tax_mny}\"> <!-- 과세금액 -->
                <input type=\"hidden\" name=\"comm_vat_mny\" value=\"{$comm_vat_mny}\"> <!-- 부가세 -->
                <input type=\"hidden\" name=\"comm_free_mny\" value=\"{$comm_free_mny}\"> <!-- 비과세 금액 -->
            ";
        }
        $data = array(
            "od_id" => $od_id,
            'od' => $od,
            'tot_price' => $tot_price,
            'taxTag' => $taxTag,
            'goods' => $goods,
            'goods_cnt' => $goods_cnt,
            'comm_free_mny' => $comm_free_mny,
            'comm_vat_mny' => $comm_vat_mny
        );
        return $this->load->view('pc/shop/kakao/orderform2', $data, TRUE);
    }

    public function kakaopay()
    {
        $segment = $this->uri->segment(3);
        switch ($segment) {
            case 'orderSuccess':
                $this->orderSuccess();
                break;
            case 'orderCancle':
                echo '
                <script language=\'javascript\'>
                    var childWindow = window.parent;
                    alert(\'결제가 취소/실패 하였습니다.\n\n다시시도해주세요!\');
                    childWindow.close();
                </script>';
                break;

            default:
                # code...
                break;
        }
    }

    private function orderSuccess()
    {
        $default = $this->load->get_var('default');
        $member = $this->load->get_var('member');
        include('./Bill/kakaopay/incKakaopayCommon.php');

        $url = "https://kapi.kakao.com/v1/payment/approve";  // 신청 url
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($kakao_params));
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $kakao_header);
        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        $response_res = json_decode($body);

        // print_r($response_res);

        curl_close($ch);

        $data['tid'] = $response_res->tid;
        $data['aid'] = $response_res->aid;
        $data['approved_at'] = $response_res->approved_at;
        $data['total'] = $response_res->amount->total;

        //print_r($data);

        // echo $od_tno;

        $data = json_encode($data);
        echo "<script>";
        echo "top.opener.setChildValue('" . $data . "');";
        echo "top.window.close();";
        echo "</script>";
    }

    #orderinicis 화면
    public function orderinicis()
    {
        $od_id = $this->input->get('od_id');
        $sql = "select * from shop_order where od_id=?";
        $param = array($od_id);
        $od = $this->query_model->returnOneArr($sql, $param);
        if (!$od['od_id']) {
            alert("결제할 주문서가 없습니다.");
        }
        $this->template->header();
        $this->orderinicisSkin($od_id, $od);
        $this->template->footer();
    }

    private function orderinicisSkin($od_id, $od)
    {
        $default = $this->load->get_var('default');

        $title = "결제하기";
        set_session('ss_order_id', $od_id);
        set_session('ss_order_inicis_id', $od_id);
        $stotal = $this->global_lib->get_order_spay($od_id); // 총계
        $tot_price = get_session('tot_price'); // 결제금액
        if ($tot_price == "") {
            alert('올바른 방식으로 접근해주세요.', base_url());
        }
        $order_action_url = base_url() . 'process/orderformresult';
        $orderformJs = $this->InicisOrderform1();
        $inicisTable = "";
        $goods = '';
        $goods_count = -1;

        $sql = " select *   from shop_cart  where od_id = ? and ct_select = '0'  group by gs_id order by index_no ";
        $param = array($od_id);
        $result = $this->query_model->returnArr($sql, $param);
        foreach ($result as $key => $row) {
            $rw = $this->global_lib->get_order($row['od_no']);
            $gs = $this->global_lib->get_goods($row['gs_id'], 'gname,simg1');

            if (!$goods)
                $goods = preg_replace("/\'|\"|\||\,|\&|\;/", "", $gs['gname']);

            $goods_count++;

            $it_name = stripslashes($gs['gname']);
            $it_options = $this->funcomplex_lib->print_complete_options($row['gs_id'], $od_id);
            if ($it_options) {
                $it_name .= '<div class="sod_opt">' . $it_options . '</div>';
            }
            $inicisTable .= "
            <tr>
                <td class=\"tac\">" . $this->funcomplex_lib->get_it_image($row['gs_id'], $gs['simg1'], 80, 80) . "</td>
                <td class=\"td_name\">{$it_name}</td>
                <td class=\"tac\">" . number_format($rw['sum_qty']) . "</td>
                <td class=\"tar\">" . number_format($rw['goods_price']) . "</td>
                <td class=\"tar\">" . number_format($rw['use_price']) . "</td>
                <td class=\"tar\">" . number_format($rw['sum_point']) . "</td>
                <td class=\"tar\">" . number_format($rw['baesong_price']) . "</td>
            </tr>";
        }
        // 복합과세처리
        $comm_tax_mny  = 0; // 과세금액
        $comm_vat_mny  = 0; // 부가세
        $comm_free_mny = 0; // 면세금액
        if ($default['de_tax_flag_use']) {
            $info = $this->global_lib->comm_tax_flag($od_id);
            $comm_tax_mny  = $info['comm_tax_mny'];
            $comm_vat_mny  = $info['comm_vat_mny'];
            $comm_free_mny = $info['comm_free_mny'];
        }
        $stot_price = display_price($stotal['price']);
        $stot_baesong = display_price($stotal['baesong']);
        $stot_coupon = display_price($stotal['coupon']);
        $stot_usepoint = display_price($stotal['usepoint']);
        $stot_useprice = display_price($stotal['useprice']);

        $orderform2 = $this->InicisOrderform2($od_id, $od, $tot_price, $comm_tax_mny, $comm_vat_mny, $comm_free_mny, $goods);

        #주문하시는 분 주소
        $od_addr = $this->funcomplex_lib->print_address($od['addr1'], $od['addr2'], $od['addr3'], $od['addr_jibeon']);
        #받으시는 분 주소
        $od_baddr = $this->funcomplex_lib->print_address($od['b_addr1'], $od['b_addr2'], $od['b_addr3'], $od['b_addr_jibeon']);
        $sendMsg = "";

        if ($od['memo']) {
            $sendMsg = "
            <tr>
                <th scope=\"row\">전하실 말씀</th>
                <td>" . $this->funcomplex_lib->conv_content($od['memo'], 0) . "</td>
            </tr>";
        }

        $payPrice = display_price($tot_price);
        $de_tax_flagJs = "";
        if ($default['de_tax_flag_use']) {
            $de_tax_flagJs = "
            f.tax.value         = f.comm_vat_mny.value;
            f.taxfree.value     = f.comm_free_mny.value;";
        }

        $data = array(
            'title' => $title,
            'order_action_url' => $order_action_url,
            'orderformJs' => $orderformJs,
            'inicisTable' => $inicisTable,
            'stot_price' => $stot_price,
            'stot_baesong' => $stot_baesong,
            'stot_coupon' => $stot_coupon,
            'stot_usepoint' => $stot_usepoint,
            'stot_useprice' => $stot_useprice,
            'orderform2' => $orderform2,
            'od_addr' => $od_addr,
            'od_baddr' => $od_baddr,
            'sendMsg' => $sendMsg,
            'payPrice' => $payPrice,
            'de_tax_flagJs' => $de_tax_flagJs
        );
        $this->load->view('pc/shop/orderinicis', $data);
    }

    private function InicisOrderform1()
    {
        $returnJs = "";
        $default = $this->load->get_var('default');
        $od_id = $this->input->get('od_id');
        $payBtnJs = "";
        $importJs = "";
        #include('./Bill/settle_inicis.inc.php');#orderform1에 쓸 변수가 여기에 들어가있음
        if ($default['de_tax_flag_use']) {
            $payBtnJs = "
            var comm_tax_mny =f.comm_tax_mny.value;
            var comm_vat_mny =f.comm_vat_mny.value;
            var comm_free_mny =f.comm_free_mny.value;
            ";
            $importJs = "
                'comm_tax_mny':comm_tax_mny,
                'comm_vat_mny':comm_vat_mny,
                'comm_free_mny':comm_free_mny,
            ";
        }
        // 전자결제를 사용할 때만 실행
        if ($default['de_iche_use'] || $default['de_vbank_use'] || $default['de_hp_use'] || $default['de_card_use'] || $default['de_easy_pay_use']) {
            $returnJs = "
            <script type=\"text/javascript\" src=\"https://cdn.iamport.kr/v1/iamport.js\"></script>
            <script language=javascript>
            var base_url=document.getElementById('base_url');
            var IMP = window.IMP; // 생략 가능
            var od_id='$od_id';
            IMP.init('imp52034655'); // 예: imp00000000
                function paybtn(f) {
                    var pay_method=f.od_settle_case.value;
                    var buyer_tel=f.od_hp.value;
                    var name=f.goodname.value;
                    var amount=f.price.value;
                    var buyer_email=f.buyeremail.value;
                    var buyer_addr=f.od_addr1.value;
                    var buyer_name=f.od_name.value;
                    var buyer_postcode=f.od_zip.value;
                    var merchant_uid = 'omni' + new Date().getTime();
                    {$payBtnJs}                    
                    if(pay_method=='신용카드'){
                        pay_method='card';
                    }else if(pay_method=='휴대폰'){
                        pay_method='phone';
                    }else if(pay_method=='계좌이체'){
                        pay_method='trans';
                    }else if(pay_method=='가상계좌'){
                        pay_method='vbank';
                    }

                    $.ajax({
                        url:base_url+\"process/preimport\",
                        data:{
                            'merchant_uid':merchant_uid,
                            'od_id':od_id,
                            {$importJs}
                        },
                        type:\"POST\",
                        success:function(res){
                            var result=JSON.parse(res);
                            if(result['res']=='true'){
                                importProcess(pay_method,merchant_uid,name,amount,buyer_email,buyer_name,buyer_tel,buyer_addr,buyer_postcode);
                            }else{
                                alert('다시 실행해주세요.');
                            }
                        },error:function(a,b,c){
                            alert('다시 실행해주세요.');
                            history.back();
                        }
                    })
                    function importProcess(pay_method,merchant_uid,name,amount,buyer_email,buyer_name,buyer_tel,buyer_addr,buyer_postcode){
                        IMP.request_pay({ // param
                            pg: \"smartro_v2\",
                            pay_method: pay_method,
                            merchant_uid: merchant_uid,
                            name: name,
                            amount: amount,
                            buyer_email: buyer_email,
                            buyer_name: buyer_name,
                            buyer_tel: buyer_tel,
                            buyer_addr: buyer_addr,
                            buyer_postcode: buyer_postcode,
                            m_redirect_url: base_url + 'm/process/order_import', // 모바일에서 리디렉션 될 주소
                        }, function (rsp) { // callback
                            if (rsp.error_code != null) {
                                // 결제 실패 시 로직,
                                var msg = \"결제에 실패하였습니다.실패사유: \";
                                msg += rsp.error_msg;
                                alert(msg);
                            } else {
                                var imp_uid=rsp.imp_uid;
                                var merchant_uid=rsp.merchant_uid;
                                
                                $.ajax({
                                    url:base_url+'process/orderformresult',
                                    data:{
                                        'od_id':od_id,
                                        'merchant_uid':merchant_uid,
                                        'imp_uid':imp_uid,
                                    },
                                    type:'POST',
                                    success:function(e){
                                        console.log(e);
                                        $('.import_loading').hide();
                                        var res=JSON.parse(e);
                                        if(res['res']){
                                            alert('결제가 완료되었습니다.');
                                            var od_id=res['od_id'];
                                            var uid=res['uid'];
                                            location.href=base_url+'shop/orderinquiryview?od_id='+od_id+'&uid='+uid;
                                        }else{
                                            alert(res['msg']);
                                            location.href=base_url;
                                        }
                                    },error:function(a,b,c){
                                        console.log(a,b,c);
                                    }, beforeSend: function () {
                                        $('.import_loading').show();      
                                    }
                                })
                            }
                        });
                    }
                    return false;
                }
            </script>";
        }
        return $returnJs;
    }

    private function InicisOrderform2($od_id, $od, $tot_price, $comm_tax_mny, $comm_vat_mny, $comm_free_mny, $goods)
    {
        $default = $this->load->get_var('default');
        include('./Bill/settle_inicis.inc.php'); #orderform1에 쓸 변수가 여기에 들어가있음
        $de_tax_flag = "";
        $de_tax_flag2 = "";
        if ($default['de_tax_flag_use']) {
            $de_tax_flag = "
            <input type=\"hidden\" name=\"comm_tax_mny\" value=\"{$comm_tax_mny}\"> <!-- 과세금액 -->
            <input type=\"hidden\" name=\"comm_vat_mny\" value=\"{$comm_vat_mny}\"> <!-- 부가세 -->
            <input type=\"hidden\" name=\"comm_free_mny\" value=\"{$comm_free_mny}\"> <!-- 비과세 금액 -->";
            $de_tax_flag2 = "
            <input type=\"hidden\" name=\"tax\" value=\"{$comm_vat_mny}\">
            <input type=\"hidden\" name=\"taxfree\" value=\"{$comm_free_mny}\">";
        }
        $data = array(
            'od_id' => $od_id,
            'od' => $od,
            'tot_price' => $tot_price,
            'comm_tax_mny' => $comm_tax_mny,
            'comm_vat_mny' => $comm_vat_mny,
            'comm_free_mny' => $comm_free_mny,
            'mid' => $mid,
            'goods' => $goods,
            'acceptmethod' => $acceptmethod,
            'returnUrl' => $returnUrl,
            'closeUrl' => $closeUrl,
            'popupUrl' => $popupUrl,
            'cardNoInterestQuota' => $cardNoInterestQuota,
            'cardQuotaBase' => $cardQuotaBase,
            'de_tax_flag' => $de_tax_flag,
            'de_tax_flag2' => $de_tax_flag2
        );
        return $this->load->view('pc/shop/inicis/orderform2', $data, TRUE);
    }

    #결제가 완료된후 나오는 페이지들 끝
    #orderprint 화면
    public function orderprint()
    {
        $this->template->header_sub();
        $this->orderprintSkin();
        $this->template->footer_sub();
    }

    private function orderprintSkin()
    {
        $title = "주문내역인쇄";
        $od_id = $this->input->get('od_id');
        $od = $this->global_lib->get_order($od_id); // 주문정보
        if (!$od['od_id'])
            alert_close("존재하는 주문이 아닙니다.");

        $stotal = $this->global_lib->get_order_spay($od_id); // 총계
        $sql = " select * from shop_cart where od_id = ? group by gs_id order by index_no ";
        $data = array($od_id);
        $printTb = "";
        $result = $this->query_model->returnArr($sql, $data);
        foreach ($result as $key => $row) {
            $rw = $this->global_lib->get_order($row['od_no']);
            $gs = unserialize($rw['od_goods']);

            $it_name = stripslashes($gs['gname']);
            $it_options = $this->funcomplex_lib->print_complete_options($row['gs_id'], $row['od_id']);
            if ($it_options) {
                $it_name .= '<div class="sod_opt">' . $it_options . '</div>';
            }
            $printTb .= "
            <tr>
                <td>{$it_name}</td>
                <td class=\"tar\">" . display_price($rw['use_price']) . "</td>
                <td class=\"tac\">" . display_qty($rw['sum_qty']) . "</td>
                <td class=\"tar\">" . display_price($rw['baesong_price']) . "</td>
            </tr>";
        }
        $stotal_price = display_price($stotal['price']);
        $stotal_coupon = "";
        if ($stotal['coupon']) {
            $stotal_coupon = "
            <dt>쿠폰할인</dt>
            <dd><strong>" . display_price($stotal['coupon']) . "</strong></dd>";
        }
        $stotal_usepoint = "";
        if ($stotal['usepoint']) {
            $stotal_usepoint = "
            <dt>포인트결제</dt>
            <dd><strong>" . display_point($stotal['usepoint']) . "</strong></dd>";
        }
        $stotal_usemileage = "";
        if ($stotal['usemileage']) {
            $stotal_usemileage = "
            <dt>예치금결제</dt>
            <dd><strong>" . display_point($stotal['usemileage']) . "</strong></dd>";
        }
        $stotal_baesong = "";
        if ($stotal['baesong']) {
            $stotal_baesong = "
            <dt>배송비</dt>
            <dd><strong>" . display_price($stotal['baesong']) . "</strong></dd>";
        }
        $stotal_useprice = display_price($stotal['useprice']);
        $stotal_point = display_price($stotal['point']);
        $od_name = $this->funcomplex_lib->get_text($od['name']);
        $od_tele = $this->funcomplex_lib->get_text($od['telephone']);
        $od_cell = $this->funcomplex_lib->get_text($od['cellphone']);
        $od_addr = $this->funcomplex_lib->get_text(sprintf("(%s)", $od['zip']) . ' ' . $this->funcomplex_lib->print_address($od['addr1'], $od['addr2'], $od['addr3'], $od['addr_jibeon']));
        $od_email = $this->funcomplex_lib->get_text($od['email']);

        $od_Bname = $this->funcomplex_lib->get_text($od['b_name']);
        $od_Btele = $this->funcomplex_lib->get_text($od['b_telephone']);
        $od_Bcell = $this->funcomplex_lib->get_text($od['b_cellphone']);
        $od_Baddr = $this->funcomplex_lib->get_text(sprintf("(%s)", $od['b_zip']) . ' ' . $this->funcomplex_lib->print_address($od['b_addr1'], $od['b_addr2'], $od['b_addr3'], $od['b_addr_jibeon']));
        $od_memo = "";
        if ($od['memo']) {
            $od_memo = "
			<tr>
				<th scope=\"row\">전하실 말씀</th>
				<td>" . $this->funcomplex_lib->conv_content($od['memo'], 0) . "</td>
			</tr>";
        }
        $data = array(
            'title' => $title,
            'printTb' => $printTb,
            'stotal_price' => $stotal_price,
            'stotal_coupon' => $stotal_coupon,
            'stotal_usepoint' => $stotal_usepoint,
            'stotal_usemileage' => $stotal_usemileage,
            'stotal_baesong' => $stotal_baesong,
            'stotal_useprice' => $stotal_useprice,
            'stotal_point' => $stotal_point,
            'od_name' => $od_name,
            'od_tele' => $od_tele,
            'od_cell' => $od_cell,
            'od_addr' => $od_addr,
            'od_email' => $od_email,
            'od_Bname' => $od_Bname,
            'od_Btele' => $od_Btele,
            'od_Bcell' => $od_Bcell,
            'od_Baddr' => $od_Baddr,
            'od_memo' => $od_memo
        );
        $this->load->view('pc/shop/orderprint', $data);
    }

    #현금영수증
    public function taxsave()
    {
        $member = $this->load->get_var('member');
        $default = $this->load->get_var('default');
        $od_id = $this->input->get('od_id');
        $tb['title'] = '주문번호 ' . $od_id . ' 현금영수증 발행';
        $this->load->vars(array('tb' => $tb));
        $this->template->header_sub();
        $sql = " select * from shop_order where od_id = ? and mb_id=?";
        $param = array($od_id, $member['id']);
        $od = $this->query_model->returnOneArr($sql, $param);
        if (!$od)
            alert_close('주문서가 존재하지 않습니다.');

        $goods = $this->global_lib->get_full_name($od['od_id']);
        $goods_name = $goods['full_name'];
        $dir = $od['od_pg'];
        $od_name = $od['name'];
        $od_email = $this->funcomplex_lib->get_text($od['email']);
        $od_tel = $this->funcomplex_lib->get_text($od['cellphone']);

        $amt_tot = (int)$od['od_tax_mny'] + (int)$od['od_vat_mny'] + (int)$od['od_free_mny'];
        $amt_sup = (int)$od['od_tax_mny'] + (int)$od['od_free_mny'];
        $amt_tax = (int)$od['od_vat_mny'];
        $amt_svc = 0;

        $trad_time = date("YmdHis");
        // 신청폼
        $this->template->header_sub();

        if (!$default['de_pg_service'])
            $dir = $default['de_pg_service'];
        if ($dir == "inicis") {
            $tx = $this->input->get('tx');
            $viewData = array(
                'tb' => $tb,
                'od_id' => $od_id,
                'goods_name' => $goods_name,
                'od_name' => $od_name,
                'od_email' => $od_email,
                'od_tel' => $od_tel,
                'tx' => $tx,
                'trad_time' => $trad_time,
                'od_email' => $od_email,
                'amt_tot' => $amt_tot,
                'amt_sup' => $amt_sup,
                'amt_svc' => $amt_svc,
                'amt_tax' => $amt_tax
            );
            $this->load->view('pc/shop/inicis/taxsave_form', $viewData);
        }
        $this->template->footer_sub();
    }

    public function inicis_taxsave_result()
    {
        $od_id = $this->input->post('od_id');
        $default = $this->load->get_var('default');
        include_once('./Bill/settle_inicis.inc.php');
        $sql = " select * from shop_order where od_id =?";
        $param = array($od_id);
        $od = $this->query_model->returnOneArr($sql, $param);
        if (!$od)
            die('<p id="scash_empty">주문서가 존재하지 않습니다.</p>');
        if ($od['od_cash'] == 1)
            alert('이미 등록된 현금영수증 입니다.');

        $buyername = $od['name'];
        $goods     = $this->global_lib->get_full_name($od['od_id']);
        $goodname  = $goods['full_name'];
        $amt_tot   = (int)$od['od_tax_mny'] + (int)$od['od_vat_mny'] + (int)$od['od_free_mny'];
        $amt_sup   = (int)$od['od_tax_mny'] + (int)$od['od_free_mny'];
        $amt_tax   = (int)$od['od_vat_mny'];
        $amt_svc   = 0;


        $reg_num  = $id_info;
        $useopt   = $tr_code;
        $currency = 'WON';

        /*********************
         * 3. 발급 정보 설정 *
         *********************/
        $inipay->SetField("type", "receipt");    // 고정
        $inipay->SetField("pgid", "INIphpRECP"); // 고정
        $inipay->SetField("paymethod", "CASH");       // 고정 (요청분류)
        $inipay->SetField("currency", $currency);    // 화폐단위 (고정)
        /**************************************************************************************************
         * admin 은 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
         * 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
         * 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
         * 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
         **************************************************************************************************/
        $inipay->SetField("admin", $default['de_inicis_admin_key']); // 키패스워드(상점아이디에 따라 변경)
        $inipay->SetField("mid", $default['de_inicis_mid']);       // 상점아이디
        $inipay->SetField("goodname", iconv_euckr($goodname));          // 상품명
        $inipay->SetField("cr_price", $amt_tot);                        // 총 현금결제 금액
        $inipay->SetField("sup_price", $amt_sup);                        // 공급가액
        $inipay->SetField("tax", $amt_tax);                        // 부가세
        $inipay->SetField("srvc_price", $amt_svc);                        // 봉사료
        $inipay->SetField("buyername", iconv_euckr($buyername));         // 구매자 성명
        $inipay->SetField("buyeremail", $buyeremail);                     // 구매자 이메일 주소
        $inipay->SetField("buyertel", $buyertel);                       // 구매자 전화번호
        $inipay->SetField("reg_num", $reg_num);                        // 현금결제자 주민등록번호
        $inipay->SetField("useopt", $useopt);                         // 현금영수증 발행용도 ("1" - 소비자 소득공제용, "2" - 사업자 지출증빙용)
        $inipay->SetField("companynumber", $companynumber);                  // 서브몰 사업자번호


        /****************
         * 4. 발급 요청 *
         ****************/
        $inipay->startAction();

        /********************************************************************************
         * 5. 발급 결과                                                                 *
         *                                                                              *
         * 결과코드 : $inipay->GetResult('ResultCode') ("00" 이면 발행 성공)            *
         * 승인번호 : $inipay->GetResult('ApplNum') (현금영수증 발행 승인번호)          *
         * 승인날짜 : $inipay->GetResult('ApplDate') (YYYYMMDD)                         *
         * 승인시각 : $inipay->GetResult('ApplTime') (HHMMSS)                           *
         * 거래번호 : $inipay->GetResult('TID')                                         *
         * 총현금결제 금액 : $inipay->GetResult('CSHR_ApplPrice')                       *
         * 공급가액 : $inipay->GetResult('CSHR_SupplyPrice')                            *
         * 부가세 : $inipay->GetResult('CSHR_Tax')                                      *
         * 봉사료 : $inipay->GetResult('CSHR_ServicePrice')                             *
         * 사용구분 : $inipay->GetResult('CSHR_Type')                                   *
         ********************************************************************************/


        // DB 반영
        if ($inipay->GetResult('ResultCode') == '00') {
            $cash_no = $inipay->GetResult('ApplNum');

            $cash = array();
            $cash['TID']       = $inipay->GetResult('TID');
            $cash['ApplNum']   = $inipay->GetResult('ApplNum');
            $cash['ApplDate']  = $inipay->GetResult('ApplDate');
            $cash['ApplTime']  = $inipay->GetResult('ApplTime');
            $cash['CSHR_Type'] = $inipay->GetResult('CSHR_Type');
            $cash_info = serialize($cash);

            $sql = " update shop_order
                        set od_cash = '1',
                            od_cash_no = ?,
                            od_cash_info = ?
                    where od_id = ?";
            $param = array($cash_no, $cash_info, $od_id);
            $result = $this->query_model->returnNull($sql, $param);
            if (!$result)
                $cancelFlag = "true";
            if ($cancelFlag == "true") {
                include_once('./Bill/settle_inicis.inc.php');

                $TID = $tno;
                $inipay->SetField("type", "cancel"); // 고정
                $inipay->SetField("tid", $TID); // 고정
                $inipay->SetField("cancelmsg", "DB FAIL"); // 취소사유
                $inipay->startAction();
                if ($inipay->GetResult('ResultCode') == "00") {
                    $inipay->MakeTXErrMsg(MERCHANT_DB_ERR, "Merchant DB FAIL");
                }
            }
        }

        $tb['title'] = '현금영수증 발급';
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            $this->template->mb_header_sub();
        } else {
            $this->template->header_sub();
        }

        include_once('./Bill/iamport.php');
        $merchant_uid = $od['merchant_uid'];
        $result = $iamport->findByMerchantUID($merchant_uid); //IamportResult 를 반환(success, data, error)
        $tid = $result->data->pg_tid;
        $resultMsg = $inipay->GetResult('ResultMsg');
        $applTime = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $inipay->GetResult('ApplDate') . $inipay->GetResult('ApplTime'));
        $data = array('inipay' => $inipay, 'resultMsg' => $resultMsg, 'applTime' => $applTime, 'tid' => $tid);
        $this->load->view('pc/shop/inicis/taxsave_result', $data);
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            $this->template->mb_footer_sub();
        } else {
            $this->template->footer_sub();
        }
    }

    #orderreview 구매후기 작성 화면
    public function orderreview()
    {
        $is_member = $this->load->get_var('is_member');
        if (!$is_member) {
            alert_close("로그인 후 작성 가능합니다.");
        }
        $this->template->header_sub();
        $this->orderreviewSkin();
        $this->template->footer_sub();
    }

    private function orderreviewSkin()
    {
        $title = "구매후기 작성";
        $member = $this->load->get_var('member');
        $gs_id = $this->input->get('gs_id');
        $od_id = $this->input->get('od_id');
        $od_no = $this->input->get('od_no');
        $od_index = $this->input->get('od_index');
        $w = $this->input->get('w') ?? "";
        $gs = $this->global_lib->get_goods($gs_id);

        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);

        if ($w == "") { // 구매후기 작성 전
            $sql = "SELECT * FROM shop_order WHERE dan = 5 AND user_ok = 1 AND od_id = ? AND od_no = ?";
            $preData = array($od_id, $od_no);
            $result = $this->query_model->returnOneArr($sql, $preData);
            $review_data = array();

            if ($result['mb_id'] != $member['id']) {
                alert("잘못된 접근입니다.");
            } else if ($result['review_flag'] != 1) {
                alert_close("이미 구매후기를 작성하셨습니다.");
            }
        } else {
            $sql2 = "SELECT * FROM shop_goods_review WHERE od_id = ? AND mb_id = ? AND od_index = ?";
            $param = array($od_id, $member['id'], $od_index);
            $review_data = $this->query_model->returnOneArr($sql2, $param);
        }

        $form_action_url = base_url() . 'process/orderreview_update';
        $gs_name = $this->funcomplex_lib->get_text($gs['gname']);

        $data = array(
            'title' => $title,
            'form_action_url' => $form_action_url,
            'w' => $w,
            'gs_id' => $gs_id,
            'gs' => $gs,
            'token' => $token,
            'od_id' => $od_id,
            'od_no' => $od_no,
            'gs_name' => $gs_name,
            'member' => $member,
            'review_data' => $review_data
        );

        $this->load->view('pc/shop/orderreview', $data);
    }

    #review 고객상품평
    public function review()
    {
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/review');
        }
        if (!$is_member) {
            goto_url(base_url() . 'bbs/login?url=' . $urlencode);
        }
        $this->template->header();
        $this->reviewSkin();
        $this->template->footer();
    }

    private function reviewSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');
        $default = $this->load->get_var('default');

        $page = $this->input->get('page');
        $title = "리뷰";

        $sql_common = "FROM shop_goods_review ";
        if ($this->gvcomplex_lib->is_admin()) {
            $sql_search = " WHERE (left(seller_id,3) = 'AP-' OR seller_id = 'admin' OR seller_id = '$pt_id') ";
        } else {
            $sql_search = " WHERE mb_id = '" . $member['id'] . "'";
        }
        if ($default['de_review_wr_use']) {
            $sql_search .= " AND pt_id = '$pt_id' ";
        }
        $sql_order  = "ORDER BY index_no DESC ";
        $sql = " SELECT count(*) AS cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = (int)$row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = "SELECT * $sql_common $sql_search $sql_order LIMIT $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $aside_my = $this->template->aside_my();

        $reviewTb = $this->reviewTable($result, $total_count, $num);

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?page=');
        $data = array(
            'title' => $title,
            'aside_my' => $aside_my,
            'total_count' => $total_count,
            'reviewTb' => $reviewTb,
            'pagination' => $pagination
        );

        $this->load->view('pc/bbs/review', $data);
    }

    private function reviewTable($result, $total_count, $num)
    {
        $returnTb = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $href = base_url() . 'shop/view?index_no=' . $row['gs_id'];
            $gs = $this->global_lib->get_goods($row['gs_id'], 'gname, simg1');

            if (!empty($gs)) {
                $gname = $gs['gname'];
            } else {
                $gname = "삭제된 상품입니다";
            }

            $wr_id = substr($row['mb_id'], 0, 3) . str_repeat("*", strlen($row['mb_id']) - 3);
            $wr_time = substr($row['reg_time'], 0, 10);

            $bg = 'list' . ($i % 2);
            $returnTb .= "
            <tr class=\"{$bg}\">
                <td class=\"tac\">" . $num-- . "</td>
                <td class=\"tac\">
                    <a href=\"{$href}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($row['gs_id'], $gs['simg1'], 50, 50) . "</a>
                </td>
                <td class=\"td_name\">";
            if (!empty($gs)) {
                $returnTb .= "<a href=\"{$href}\" target=\"_blank\">" . cut_str($gname, 55) . "</a>";
            } else {
                $returnTb .= "<p>" . cut_str($gname, 55) . "</p>";
            }
            $returnTb .= "
                    <p class=\"fc_999\">" . cut_str($row['memo'], 100) . "</p>
                </td>
                <td class=\"tac\">{$wr_id}</td>
                <td class=\"tac\">{$wr_time}</td>
                <td class=\"tac\"><img src=\"" . base_url() . "img/sub/score_{$row['score']}.gif\"></td>";
            if (!empty($gs)) {
                $returnTb .= "<td class=\"tac\"><a href=\"" . base_url() . "shop/orderreview?gs_id={$row['gs_id']}&od_id={$row['od_id']}&od_index={$row['od_index']}&w=u\" onclick=\"win_open(this, 'winorderreview', '650', '700','yes');return false;\" class=\"btn_ssmall bx-white\">구매후기 수정</a></td>";
            } else {
                $returnTb .= "<td class=\"tac\"><a href=\"#\" onclick=\"alert('삭제된 상품으로 리뷰 수정이 불가능합니다.');\" class=\"btn_ssmall bx-white\">수정</a></td>";
            }
            $returnTb .= "</tr>";
            $i++;
        }
        if ($total_count == 0) {
            $returnTb = '<tr><td colspan="6" class="empty_list">자료가 없습니다.</td></tr>';
        }
        return $returnTb;
    }

    #point 포인트 조회 화면
    public function point()
    {
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/point');
        }
        if (!$is_member) {
            goto_url(base_url() . 'bbs/login?url=' . $urlencode);
        }
        $this->template->header();
        $this->pointSkin();
        $this->template->footer();
    }

    private function pointSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');

        $page = $this->input->get('page');
        $title = "포인트 조회";

        $sql_common = " from shop_point ";
        $sql_search = " where mb_id = '{$member['id']}' ";
        $sql_order  = " order by po_id desc ";

        $sql = " select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        // 총 포인트 계산
        $sum_point3 = 0;
        $sql = " select sum(po_point) as point_sum $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $sum_point3 = $row['point_sum'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $aside_my = $this->template->aside_my();
        $tableTag = "";
        $sum_point1 = $sum_point2 = 0;
        $i = 0;
        foreach ($result as $key => $row) {
            $point1 = $point2 = 0;
            if ($row['po_point'] > 0) {
                $point1 = '+' . number_format($row['po_point']);
                $sum_point1 += $row['po_point'];
            } else {
                $point2 = number_format($row['po_point']);
                $sum_point2 += $row['po_point'];
            }

            $tableTag .= "
            <tr>
                <td class=\"tac\">{$row['po_datetime']}</td>
                <td>{$row['po_content']}</td>
                <td class=\"td_num\">{$point1}</td>
                <td class=\"td_num\">{$point2}</td>
            </tr>";
            $i++;
        }
        if ($i == 0) {
            $tableTag = '<tr><td colspan="4" class="empty_table">자료가 없습니다.</td></tr>';
        } else {
            if ($sum_point1 > 0) {
                $sum_point1 = "+" . number_format($sum_point1);
            } else {
                $sum_point2 = number_format($sum_point2);
            }
        }
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?page=');
        $data = array(
            'title' => $title,
            'aside_my' => $aside_my,
            'total_count' => $total_count,
            'tableTag' => $tableTag,
            'sum_point1' => $sum_point1,
            'sum_point2' => $sum_point2,
            'sum_point3' => $sum_point3,
            'member' => $member,
            'pagination' => $pagination
        );
        $this->load->view('pc/shop/point', $data);
    }

    #mileage 예치금 조회 화면
    public function mileage()
    {
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/point');
        }
        if (!$is_member) {
            goto_url(base_url() . 'bbs/login?url=' . $urlencode);
        }
        $this->template->header();
        $this->mileageSkin();
        $this->template->footer();
    }

    private function mileageSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');

        $page = $this->input->get('page');
        $title = "예치금 조회";

        $sql_common = " from shop_mileage ";
        $sql_search = " where mb_id = '{$member['id']}' ";
        $sql_order  = " order by po_id desc ";

        $sql = " select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        // 총 포인트 계산
        $sum_point3 = 0;
        $sql = " select sum(po_point) as point_sum $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $sum_point3 = $row['point_sum'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $aside_my = $this->template->aside_my();
        $tableTag = "";
        $sum_point1 = $sum_point2 = 0;
        $i = 0;
        foreach ($result as $key => $row) {
            $point1 = $point2 = 0;
            if ($row['po_point'] > 0) {
                $point1 = '+' . number_format($row['po_point']);
                $sum_point1 += $row['po_point'];
            } else {
                $point2 = number_format($row['po_point']);
                $sum_point2 += $row['po_point'];
            }

            $tableTag .= "
            <tr>
                <td class=\"tac\">{$row['po_datetime']}</td>
                <td>{$row['po_content']}</td>
                <td class=\"td_num\">{$point1}</td>
                <td class=\"td_num\">{$point2}</td>
            </tr>";
            $i++;
        }
        if ($i == 0) {
            $tableTag = '<tr><td colspan="4" class="empty_table">자료가 없습니다.</td></tr>';
        } else {
            if ($sum_point1 > 0)
                $sum_point1 = "+" . number_format($sum_point1);
            $sum_point2 = number_format($sum_point2);
        }
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?page=');
        $data = array(
            'title' => $title,
            'aside_my' => $aside_my,
            'total_count' => $total_count,
            'tableTag' => $tableTag,
            'sum_point1' => $sum_point1,
            'sum_point2' => $sum_point2,
            'sum_point3' => $sum_point3,
            'member' => $member,
            'pagination' => $pagination
        );
        $this->load->view('pc/shop/mileage', $data);
    }

    #gift 쿠폰 인증 화면
    public function gift()
    {
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/gift');
        }
        $config = $this->load->get_var('config');
        if (!$config['gift_yes']) {
            alert("쿠폰사용이 중지 되었습니다.");
        }
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if (!$is_member) {
            goto_url(base_url() . 'bbs/login?url=' . $urlencode);
        }
        $this->template->header();
        $this->giftSkin();
        $this->template->footer();
    }

    private function giftSkin()
    {
        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');
        $qstr = $this->load->get_var('qstr');
        $stx = $this->input->post('stx');

        $page = $this->input->get('page');
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $title = "쿠폰인증";
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        $form_action_url = base_url() . 'process/gift_update';
        $aside_my = $this->template->aside_my();

        $sql_common = " from shop_gift ";
        $sql_search = " where mb_id = '$member[id]' ";
        $stx = addslashes($stx);
        if ($stx) {
            $sql_search .= " and gi_num like '$stx%' ";
        }

        $sql = " select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search order by no desc limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $giftTable = $this->giftTable($result, $num);

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . $qstr . '&page=');

        $data = array(
            'title' => $title,
            'form_action_url' => $form_action_url,
            'aside_my' => $aside_my,
            'token' => $token,
            'total_count' => $total_count,
            'stx' => $stx,
            'giftTable' => $giftTable,
            'pagination' => $pagination
        );
        $this->load->view('pc/shop/gift', $data);
    }

    private function giftTable($result, $num)
    {
        $returnTag = "";
        $i = 0;
        foreach ($result as $key => $row) {
            if (is_null_time($row['mb_wdate'])) {
                $row['mb_wdate'] = '';
            }
            $bg = 'list' . ($i % 2);
            $returnTag .= "
            <tr class=\"{$bg}\">
                <td class=\"tac\">" . ($num--) . "</td>
                <td>{$row['gi_num']}</td>
                <td class=\"tac\">" . display_price($row['gr_price']) . "</td>
                <td class=\"tac\">" . $row['gr_sdate'] . "</td>
                <td class=\"tac\">" . $row['gr_edate'] . "</td>
                <td class=\"tac\">" . ($row['gi_use'] ? 'yes' : '') . "</td>
                <td class=\"tac\">" . $row['mb_wdate'] . "</td>
            </tr>";
            $i++;
        }
        if ($i == 0) {
            $returnTag = '<tr><td colspan="7" class="empty_list">내역이 없습니다.</td></tr>';
        }
        return $returnTag;
    }

    #coupon 쿠폰 관리 화면
    public function coupon()
    {
        $config = $this->load->get_var('config');
        $is_member = $this->load->get_var('is_member');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/coupon');
        }
        if (!$config['coupon_yes']) {
            alert("쿠폰사용이 중지 되었습니다.");
        }
        if (!$is_member) {
            goto_url(base_url() . 'bbs/login?url=' . $urlencode);
        }
        $this->template->header();
        $this->couponSkin();
        $this->template->footer();
    }

    private function couponSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');

        $sca = $this->input->get('sca');
        $page = $this->input->get('page');
        $title = "쿠폰관리";
        $u_part = array();
        $u_part[0] = "전체상품";
        $u_part[1] = "일부 상품 사용가능";
        $u_part[2] = "일부 카테고리 사용가능";
        $u_part[3] = "일부 상품 사용불가";
        $u_part[4] = "일부 카테고리 사용불가";

        $sql_common = " from shop_coupon_log ";
        $sql_search = " where mb_id = '$member[id]' ";
        $sql_order  = " order by cp_wdate desc ";

        $selected1 = '';
        $selected2 = '';
        if ($sca) {
            // 사용완료 및 기한만료 쿠폰
            $sql_search .= " and mb_use='1' or ( (cp_inv_type='0' and cp_inv_edate != '9999999999' and cp_inv_edate < curdate()) or (cp_inv_type='1' and date_add(`cp_wdate`, interval `cp_inv_day` day) < now()) ) ";
            $selected2 = ' class="active"';
        } else {
            // 사용가능한 쿠폰
            $sql_search .= " and mb_use='0' and ( (cp_inv_type='0' and (cp_inv_edate = '9999999999' or cp_inv_edate > curdate())) or (cp_inv_type='1' and date_add(`cp_wdate`, interval `cp_inv_day` day) > now()) ) ";
            $selected1 = ' class="active"';
        }
        $sql = " select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $couponTable = $this->couponTable($result, $num, $u_part);
        $aside_my = $this->template->aside_my();
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $qstr . '&page=');
        $data = array(
            'title' => $title,
            'couponTable' => $couponTable,
            'aside_my' => $aside_my,
            'total_count' => $total_count,
            'pagination' => $pagination,
            'selected1' => $selected1,
            'selected2' => $selected2
        );
        $this->load->view('pc/shop/coupon', $data);
    }

    private function couponTable($result, $num, $u_part)
    {
        $returnTag = "";
        $i = 0;
        foreach ($result as $key => $row) {
            // 할인금액(율)
            if ($row['cp_sale_type'] == '0') {
                if ($row['cp_sale_amt_max'] > 0)
                    $cp_sale_amt_max = "&nbsp;(최대 " . display_price($row['cp_sale_amt_max']) . ")";
                else
                    $cp_sale_amt_max = "";

                $sale_amt = $row['cp_sale_percent'] . '%' . $cp_sale_amt_max;
            } else {
                $sale_amt = display_price($row['cp_sale_amt']);
            }

            // 쿠폰 사용기한
            if ($row['cp_inv_type'] == '0') {
                if ($row['cp_inv_sdate'] == '9999999999') $cp_inv_sdate = '무제한';
                else $cp_inv_sdate = $this->funcomplex_lib->date_conv($row['cp_inv_sdate'], 4);

                if ($row['cp_inv_edate'] == '9999999999') $cp_inv_edate = '무제한';
                else $cp_inv_edate = $this->funcomplex_lib->date_conv($row['cp_inv_edate'], 4);

                if ($row['cp_inv_sdate'] == '9999999999' && $row['cp_inv_edate'] == '9999999999')
                    $inv_date = '무제한';
                else
                    $inv_date = $cp_inv_sdate . " ~ " . $cp_inv_edate;
            } else {
                $inv_date = '다운로드 후 ' . $row['cp_inv_day'] . '일간';
            }
            $bg = 'list' . ($i % 2);
            $returnTag .= "
            <tr class=\"{$bg}\">
                <td class=\"tac\">" . ($num--) . "</td>
                <td>" . $this->funcomplex_lib->get_text(cut_str($row['cp_subject'], 44)) . "</td>
                <td>{$sale_amt}</td>
                <td class=\"tac\">{$u_part[$row['cp_use_part']]}</td>
                <td class=\"tac\">{$inv_date}</td>
                <td class=\"tac\">{$row['cp_wdate']}</td>
            </tr>";
            $i++;
        }
        if ($i == 0) {
            $returnTag = '<tr><td colspan="6" class="empty_list">자료가 없습니다.</td></tr>';
        }
        return $returnTag;
    }

    #mypage 화면
    public function mypage()
    {
        $urlencode = $this->load->get_var('urlencode');
        $is_member = $this->load->get_var('is_member');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'mb/shop/mypage');
        }
        if (!$is_member) {
            goto_url(base_url() . 'bbs/login?url=' . $urlencode);
        }
        $this->template->header();
        $this->mypageSkin();
        $this->template->footer();
    }

    private function mypageSkin()
    {
        $member = $this->load->get_var('member');
        $title = "마이페이지";
        $sql_common = " from shop_coupon_log ";
        $sql_search = " where mb_id = '{$member['id']}' ";

        // 사용가능한 쿠폰
        $sql_search .= " and mb_use='0' and ( ";
        $sql_search .= " (cp_inv_type='0' and (cp_inv_edate = '9999999999' or cp_inv_edate > curdate())) ";
        $sql_search .= " or (cp_inv_type='1' and date_add(`cp_wdate`, interval `cp_inv_day` day) > now()) ";
        $sql_search .= " ) ";
        $sql = " select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $cp_count = $row['cnt'];
        $aside_my = $this->template->aside_my();
        $memberPoint = display_point($member['point']);
        $cpCount = display_qty($cp_count);
        $mem_cell = $member['cellphone'] ? $member['cellphone'] : '미등록';
        $mem_email = $member['email'] ? $member['email'] : '미등록';
        $mem_address = sprintf("(%s)", $member['zip']) . ' ' . $this->funcomplex_lib->print_address($member['addr1'], $member['addr2'], $member['addr3'], $member['addr_jibeon']);
        $orderOrderTable = $this->mypageOrderTable();
        $orderZzimTable = $this->mypageZzimTable();
        $data = array(
            'title' => $title,
            'aside_my' => $aside_my,
            'member' => $member,
            'memberPoint' => $memberPoint,
            'cpCount' => $cpCount,
            'mem_cell' => $mem_cell,
            'mem_email' => $mem_email,
            'mem_address' => $mem_address,
            'orderOrderTable' => $orderOrderTable,
            'orderZzimTable' => $orderZzimTable,
            'aside_my' => $aside_my
        );
        $this->load->view('pc/shop/mypage', $data);
    }

    private function mypageOrderTable()
    {
        $member = $this->load->get_var('member');
        $gw_status = $this->config->item('gw_status');
        $returnTb = "";
        $sql = " select * from shop_order where mb_id = ? and dan <> '0' group by od_id order by index_no desc limit 0, 5 ";
        $param = array($member['id']);
        $result = $this->query_model->returnArr($sql, $param);
        $i = 0;
        foreach ($result as $key => $row) {
            $sql = " select * from shop_cart where od_id = '$row[od_id]' ";
            $sql .= " group by gs_id order by io_type asc, index_no asc ";
            $resObj = $this->query_model->returnQueryObj($sql);
            $rowspan = $resObj->num_rows() + 1;
            $res = $resObj->result_array();
            $k = 0;
            foreach ($res as $key => $ct) {
                $od = $this->global_lib->get_order($ct['od_no']);
                $gs = unserialize($od['od_goods']);
                $dlcomp = explode('|', trim($od['delivery']));
                $href = base_url() . 'shop/view?index_no=' . $od['gs_id'];
                if ($k == 0) {
                    $returnTb .= "
                    <tr>
                        <td class=\"tac\" rowspan=\"{$rowspan}\">
                            <p class=\"bold\">" . substr($od['od_time'], 0, 10) . "</p>
                            <p class=\"padt5\"><a href=\"" . base_url() . "shop/orderinquiryview?od_id={$od['od_id']}\" class=\"btn_small grey\">상세보기</a></p>
                        </td>
                    </tr>";
                }
                $returnTb .= "
                <tr class=\"rows\">
                    <td>
                        <div class=\"ini_wrap\">
                            <table class=\"wfull\">
                                <colgroup>
                                    <col class=\"w70\">
                                    <col>
                                </colgroup>
                                <tr>
                                    <td class=\"vat tal\"><a href=\"{$href}\">" . $this->funcomplex_lib->get_od_image($od['od_id'], $gs['simg1'], 60, 60) . "</a></td>
                                    <td class=\"vat tal\">
                                        <a href=\"{$href}\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a>
                                        <p class=\"padt3 fc_999\">주문번호 : {$od['od_id']} / 수량 : " . display_qty($od['sum_qty']) . " / 배송비 : " . display_price($od['baesong_price']) . "</p>";
                if ($od['dan'] == 5 && $od['review_flag'] == 1 && $od['user_ok'] == 1) {
                    $returnTb .= "<p class=\"padt3\"><a href=\"" . base_url() . "shop/orderreview?gs_id={$od['gs_id']}&od_id={$od['od_id']}&od_no={$od['od_no']}\" onclick=\"win_open(this, 'winorderreview', '650', '700','yes');return false;\" class=\"btn_ssmall bx-white\">구매후기 작성</a></p>";
                }
                $returnTb .= "
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td class=\"tar\">" . display_price($od['use_price']) . "</td>
                    <td class=\"tac\">";
                $gw_status[$od['dan']] = isset($gw_status[$od['dan']]) ? $gw_status[$od['dan']] : "";
                $returnTb .= "<p>" . $gw_status[$od['dan']] . "</p>";
                if (isset($dlcomp[0]) && $od['delivery_no']) {
                    $returnTb .= "<p class=\"padt3 fc_90\">{$dlcomp[0]}<br>{$od['delivery_no']}</p>";
                }
                if (isset($dlcomp[1]) && $od['delivery_no']) {
                    $returnTb .= "<p class=\"padt3\">" . get_delivery_inquiry($od['delivery'], $od['delivery_no'], 'btn_ssmall') . "</p>";
                }
                $returnTb .= "
                    </td>
                </tr>";
                $k++;
            }
            $i++;
        }

        if ($i == 0) {
            $returnTb = '<tr><td colspan="4" class="empty_table">자료가 없습니다.</td></tr>';
        }

        return $returnTb;
    }

    private function mypageZzimTable()
    {
        $returnTb = "";
        $member = $this->load->get_var('member');
        $sql  = " select a.wi_id, a.wi_time, a.gs_id, b.* from shop_wish a left join shop_goods b ON ( a.gs_id = b.index_no ) 
                where a.mb_id = ? order by a.wi_id desc limit 0, 3 ";
        $param = array($member['id']);
        $result = $this->query_model->returnArr($sql, $param);
        $i = 0;
        foreach ($result as $key => $row) {
            $href = base_url() . 'shop/view?index_no=' . $row['gs_id'];
            $returnTb .= "
            <tr>
                <td class=\"tac\"><a href=\"{$href}\">" . $this->funcomplex_lib->get_it_image($row['gs_id'], $row['simg1'], 70, 70) . "</a></td>
                <td class=\"td_name\">
                    <a href=\"{$href}\">" . stripslashes($row['gname']) . "</a>
                    <p class=\"fc_999\">{$row['explan']}</p>
                </td>
                <td class=\"tar\">" . $this->funcomplex_lib->get_price($row['gs_id']) . "</td>
                <td class=\"tac\">{$row['wi_time']}</td>
            </tr>";
            $i++;
        }
        if ($i == 0) {
            $returnTb = '<tr><td colspan="4" class="empty_table">보관 내역이 없습니다.</td></tr>';
        }
        return $returnTb;
    }

    #주문하기 orderform
    public function orderform()
    {
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/orderform');
        }
        $ss_cart_id = get_session('ss_cart_id');
        if (!$ss_cart_id) {
            alert("주문하실 상품이 없습니다.", base_url());
        }

        set_session('tot_price', '');
        set_session('use_point', '');

        // 새로운 주문번호 생성
        $od_id = $this->global_lib->get_uniqid();
        set_session('ss_order_id', $od_id);

        $this->template->header();
        $this->orderformSkin();
        $this->template->footer();
    }

    private function orderformSkin()
    {
        $member = $this->load->get_var('member');
        $is_member = $this->load->get_var('is_member');
        $pt_id = $this->load->get_var('pt_id');
        $config = $this->load->get_var('config');
        $default = $this->load->get_var('default');

        $title = "주문서작성";
        $mb_recommend = "";
        if ($is_member) { // 회원일때
            // 주문자가 가맹점이면 추천인을 자신으로 변경
            $mb_recommend = $member['pt_id'];
            if ($this->funcomplex_lib->is_partner($member['id'])) {
                $mb_recommend = $member['id'];
            }
        } else {
            $mb_recommend = $pt_id;
            $member['point'] = 0;
        }

        $order_action_url = base_url() . 'process/orderformupdate';
        $orderformTb = $this->orderFormTb();
        $orderFormTag = $orderformTb['returnTag'];
        $item_sendcost = $orderformTb['item_sendcost'];
        $tot_sell_price = $orderformTb['tot_sell_price'];
        $tot_point = $orderformTb['tot_point'];
        $tot_sell_amt = $orderformTb['tot_sell_amt'];
        $tot_opt_price = $orderformTb['tot_opt_price'];
        $tot_sell_qty = $orderformTb['tot_sell_qty'];
        $is_timesale = $orderformTb['is_timesale'];

        // 배송비 검사
        $send_cost = 0;
        $com_send_cost = 0;
        $sep_send_cost = 0;
        $max_send_cost = 0;
        $k = 0;
        $condition = array();
        foreach ($item_sendcost as $key) {
            list($userid, $bundle, $price) = explode('|', $key);
            $price = ($price == "") ? 0 : $price;
            $condition[$userid][$bundle][$k] = $price;
            $k++;
        }
        $com_array = array();
        $val_array = array();
        foreach ($condition as $key => $value) {
            if (isset($condition[$key]['묶음'])) {
                $com_send_cost += array_sum($condition[$key]['묶음']); // 묶음배송 합산
                $max_send_cost += max($condition[$key]['묶음']); // 가장 큰 배송비 합산
                $com_array[] = max(array_keys($condition[$key]['묶음'])); // max key
                $val_array[] = max(array_values($condition[$key]['묶음'])); // max value
            }
            if (isset($condition[$key]['개별'])) {
                $sep_send_cost += array_sum($condition[$key]['개별']); // 묶음배송불가 합산
                $com_array[] = array_keys($condition[$key]['개별']); // 모든 배열 key
                $val_array[] = array_values($condition[$key]['개별']); // 모든 배열 value
            }
        }
        $baesong_price = $this->gvcomplex_lib->get_tune_sendcost($com_array, $val_array, $item_sendcost);

        $send_cost = $com_send_cost + $sep_send_cost; // 총 배송비합계
        $tot_send_cost = $max_send_cost + $sep_send_cost; // 최종배송비
        $tot_final_sum = $send_cost - $tot_send_cost; // 배송비할인
        $tot_price = $tot_sell_price + $tot_send_cost; // 결제예정금액

        $totPoint = display_point($tot_point);
        $totSellAmt = display_price2($tot_sell_amt);
        $totOptPrice = display_price2($tot_opt_price);
        $totQty = display_qty($tot_sell_qty);
        $memPoint = display_point($member['point']);
        $totSellPrice = display_price2($tot_sell_price);
        $sumSendCost = display_price2($send_cost);
        $totFinalSum = display_price2($tot_final_sum);
        $totSendCose = display_price2($tot_send_cost);
        $totPrice = display_price2($tot_price);
        $ss_cart_id = get_session('ss_cart_id');
        $usepoint = "";
        if (!$is_member || !$config['usepoint_yes']) {
            $usepoint = "<input type=\"hidden\" name=\"use_point\" value=\"0\">";
        }
        $usemileage = "";
        if (!$is_member || !$config['usemileage_yes']) {
            $usemileage = "<input type=\"hidden\" name=\"use_mileage\" value=\"0\">";
        }
        $notMemberTag = "";
        if (!$is_member) {
            $notMemberTag = "
            <tr>
                <th scope=\"row\">비밀번호</th>
                <td>
                    <input type=\"password\" name=\"od_pwd\" required itemname=\"비밀번호\" class=\"frm_input required\" size=\"20\">
                    <span class=\"frm_info\">영,숫자 3~20자 (주문서 조회시 필요)</span>
                </td>
            </tr>";
        }
        $arrivalList = "";
        if ($is_member) {
            $arrivalList = "<label><input type=\"radio\" name=\"ad_sel_addr\" value=\"3\"> 배송지목록</label>";
        }

        $payMethod = "";
        // if(in_array(true, $is_timesale)) {
        //     if($default['de_card_use']) {
        //         $payMethod.='<input type="radio" name="paymethod" id="paymethod_card" value="신용카드" onclick="calculate_paymethod(this.value);"> <label for="paymethod_card">신용카드</label>';
        //     }
        //     if($default['de_iche_use']) {
        //         $payMethod.='<input type="radio" name="paymethod" id="paymethod_iche" value="계좌이체" onclick="calculate_paymethod(this.value);"> <label for="paymethod_iche">계좌이체</label>';
        //     }
        // } else {
        if ($default['de_bank_use']) {
            $payMethod .= '<input type="radio" name="paymethod" id="paymethod_bank" value="무통장" onclick="calculate_paymethod(this.value);"> <label for="paymethod_bank">무통장입금</label>';
        }
        if ($default['de_card_use']) {
            $payMethod .= '<input type="radio" name="paymethod" id="paymethod_card" value="신용카드" onclick="calculate_paymethod(this.value);"> <label for="paymethod_card">신용카드</label>';
        }
        if ($default['de_iche_use']) {
            $payMethod .= '<input type="radio" name="paymethod" id="paymethod_iche" value="계좌이체" onclick="calculate_paymethod(this.value);"> <label for="paymethod_iche">계좌이체</label>';
        }
        // }

        if ($is_member && $config['usepoint_yes'] && ($tot_price <= $member['point'])) {
            $payMethod .= '<input type="radio" name="paymethod" id="paymethod_point" value="포인트" onclick="calculate_paymethod(this.value);"> <label for="paymethod_point">포인트결제</label>';
        }
        if ($is_member && $config['usemileage_yes'] && ($tot_price <= $member['mileage'])) {
            $payMethod .= '<input type="radio" name="paymethod" id="paymethod_mileage" value="예치금" onclick="calculate_paymethod(this.value);"> <label for="paymethod_mileage">예치금결제</label>';
        }

        $tot1Price = display_price($tot_price);
        $haveCoupon = "";
        if ($is_member && $config['coupon_yes']) { // 보유쿠폰
            $cp_count = $this->global_lib->get_cp_precompose($member['id']);
            $haveCoupon = "
            <tr>
                <th scope=\"row\">할인쿠폰</th>
                <td>(-) <strong><span id=\"dc_amt\">0</span>원 <span id=\"dc_cancel\" style=\"display:none\"><a href=\"javascript:coupon_cancel();\">X</a></span></strong>
                <span id=\"dc_coupon\"><a href=\"" . base_url() . "shop/ordercoupon\" onclick=\"win_open(this,'win_coupon','670','500','yes');return false\"><span class='fc_197 tu'>사용 가능 쿠폰 {$cp_count[3]}장</a> </span></span></td>
            </tr>";
        }
        $pointUse = "";
        if ($is_member && $config['usepoint_yes']) {
            $pointUse .= "
            <tr>
                <th scope=\"row\">포인트결제</th>
                <td>
                    <input type=\"text\" name=\"use_point\" value=\"0\" class=\"frm_input\" size=\"12\" onkeyup=\"calculate_temp_point(this.value);this.value=number_format(this.value);\" onfocus=\"removeLeadingZero(this)\" onblur=\"restoreZeroIfEmpty(this)\" style=\"font-weight:bold;\"> 원 보유포인트 : " . display_point($member['point']);
            if ($config['usepoint'] >= 0) {
                $pointUse .= " (<strong>" . display_point($config['usepoint']) . "</strong> 부터 사용가능)";
            }
            $pointUse .= "</td>
            </tr>";
        }
        $mileageUse = "";
        if ($is_member && $config['usemileage_yes']) {
            $mileageUse .= "
            <tr>
                <th scope=\"row\">예치금결제</th>
                <td>
                    <input type=\"text\" name=\"use_mileage\" value=\"0\" class=\"frm_input\" size=\"12\" onkeyup=\"calculate_temp_mileage(this.value);this.value=number_format(this.value);\" onfocus=\"removeLeadingZero(this)\" onblur=\"restoreZeroIfEmpty(this)\" style=\"font-weight:bold;\"> 원 보유예치금 : " . display_price($member['mileage']);
            if ($config['usemileage'] >= 0) {
                $mileageUse .= " (<strong>" . display_price($config['usemileage']) . "</strong> 부터 사용가능)";
            }
            $mileageUse .= "</td>
            </tr>";
        }
        $bank = $this->funcomplex_lib->get_bank_account("bank");
        $company_type = "";
        if (!$config['company_type']) {
            $company_type = $this->companyType();
        }
        $guest_privacy = "";
        if (!$is_member) {
            $guest_privacy = $this->quest_privacy();
        }
        $company_type_js = "";
        if (!$config['company_type']) {
            $company_type_js = "
            if(getRadioVal(f.paymethod) == '무통장' && getRadioVal(f.taxsave_yes) == 'Y') {
                check_field(f.tax_hp, \"핸드폰번호를 입력하세요\");
            }
    
            if(getRadioVal(f.paymethod) == '무통장' && getRadioVal(f.taxsave_yes) == 'S') {
                check_field(f.tax_saupja_no, \"사업자번호를 입력하세요\");
            }
    
            if(getRadioVal(f.paymethod) == '무통장' && getRadioVal(f.taxbill_yes) == 'Y') {
                check_field(f.company_saupja_no, \"사업자번호를 입력하세요\");
                check_field(f.company_name, \"상호명을 입력하세요\");
                check_field(f.company_owner, \"대표자명을 입력하세요\");
                check_field(f.company_addr, \"사업장소재지를 입력하세요\");
                check_field(f.company_item, \"업태를 입력하세요\");
                check_field(f.company_service, \"종목을 입력하세요\");
            }";
        }
        $taxjs = "";
        $taxpointJS = "";
        if (!$config['company_type']) {
            $taxjs .= '$("#tax_section").show();';
            $taxpointJS = '
            $("#tax_section").hide();
			$(".taxbill_fld").hide();
			$("#taxsave_3").attr("checked", true);
			$("#taxbill_2").attr("checked", true);';
        }
        $member['name'] = isset($member['name']) ? $member['name'] : "";
        $member['telephone'] = isset($member['telephone']) ? $member['telephone'] : "";
        $member['cellphone'] = isset($member['cellphone']) ? $member['cellphone'] : "";
        $member['zip'] = isset($member['zip']) ? $member['zip'] : "";
        $member['addr1'] = isset($member['addr1']) ? $member['addr1'] : "";
        $member['addr2'] = isset($member['addr2']) ? $member['addr2'] : "";
        $member['addr3'] = isset($member['addr3']) ? $member['addr3'] : "";
        $member['addr_jibeon'] = isset($member['addr_jibeon']) ? $member['addr_jibeon'] : "";
        $member['email'] = isset($member['email']) ? $member['email'] : "";


        $data = array(
            'title' => $title,
            'order_action_url' => $order_action_url,
            'orderFormTag' => $orderFormTag,
            'totPoint' => $totPoint,
            'totSellAmt' => $totSellAmt,
            'totOptPrice' => $totOptPrice,
            'totQty' => $totQty,
            'memPoint' => $memPoint,
            'totSellPrice' => $totSellPrice,
            'sumSendCost' => $sumSendCost,
            'totFinalSum' => $totFinalSum,
            'totSendCose' => $totSendCose,
            'totPrice' => $totPrice,
            'ss_cart_id' => $ss_cart_id,
            'member' => $member,
            'mb_recommend' => $mb_recommend,
            'pt_id' => $pt_id,
            'baesong_price' => $baesong_price,
            'tot_price' => $tot_price,
            'usepoint' => $usepoint,
            'usemileage' => $usemileage,
            'notMemberTag' => $notMemberTag,
            'arrivalList' => $arrivalList,
            'payMethod' => $payMethod,
            'tot1Price' => $tot1Price,
            'haveCoupon' => $haveCoupon,
            'pointUse' => $pointUse,
            'mileageUse' => $mileageUse,
            'bank' => $bank,
            'company_type' => $company_type,
            'guest_privacy' => $guest_privacy,
            "company_type_js" => $company_type_js,
            'taxjs' => $taxjs,
            'taxpointJS' => $taxpointJS
        );
        $this->load->view('pc/shop/orderform', $data);
    }

    private function orderFormTb()
    {
        $returnTag = "";
        $tot_point = 0;
        $tot_sell_price = 0;
        $tot_opt_price = 0;
        $tot_sell_qty = 0;
        $tot_sell_amt = 0;
        $seller_id = array();
        $is_timesale = array();

        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $sw_direct = get_session("sw_direct");
        $ss_cart_id = get_session("ss_cart_id");
        $set_mb_id = $this->session->userdata['ss_mb_id'];

        $sql = "SELECT *
                FROM shop_cart
                WHERE index_no IN ({$ss_cart_id}) AND ct_select = '0' AND is_direct='{$sw_direct}' ORDER BY index_no";
        $result = $this->query_model->returnArr($sql);

        $i = 0;
        $buySql = "";

        foreach ($result as $key => $row) {
            $gs = $this->global_lib->get_goods($row['gs_id']);
            $this->load->vars(array('gs' => $gs));

            // bid 상품 포함되어 있는지 확인
            if ($gs['type'] == "timesale") {
                array_push($is_timesale, true);
            }

            // 합계금액 계산
            $sql = "SELECT  SUM(IF(io_type = 1, (io_price * ct_qty), ((io_price + ct_price) * ct_qty))) as price,
                            SUM(IF(io_type = 1, (io_price * ct_qty), ((io_price + ct_supply_price) * ct_qty))) as supply_price,
                            SUM(IF(io_type = 1, (0),(ct_point * ct_qty))) as point,
                            SUM(IF(io_type = 1, (0),(ct_qty))) as qty,
                            SUM(io_price * ct_qty) as opt_price
                    FROM shop_cart
                    WHERE index_no IN ({$row['index_no']})
                    AND ct_select = '0'
                    AND is_direct='{$sw_direct}'";
            $sum = $this->query_model->returnOneArr($sql);

            $it_name = stripslashes($gs['gname']);
            $it_options = $this->funcomplex_lib->print_item_options($row['index_no'], $set_mb_id);
            $point = 0;
            if ($it_options) {
                $it_name .= '<div class="sod_opt">' . $it_options . '</div>';
            }

            if ($gs['type'] == "timesale") {
                $price = $this->gvcomplex_lib->get_timesale_goods_price($gs['index_no']);
            } else {
                $price = $this->gvcomplex_lib->get_sale_price($gs['index_no']);
            }

            $supply_price = $sum['supply_price'];
            $sell_price = $sum['price'];
            $sell_opt_price = $sum['opt_price'];
            $sell_qty = $sum['qty'];
            $sell_amt = $sum['price'] - $sum['opt_price'];
            $io_price = $row['io_price'];

            // 포인트
            $point = 0;
            if ($is_member && $gs['is_point'] > 0) {
                $grade = $member['grade'];

                $sql = "SELECT gb_buy_point FROM shop_member_grade WHERE gb_no = ?";
                $param = array($grade);
                $result = $this->query_model->returnOneArr($sql, $param);

                $gb_point = $result['gb_buy_point']; // 적립률
                $point = (int) round((($price + $sell_opt_price) * $sell_qty) * ($gb_point * (1 / 100))); // 적립 포인트
            }

            // 배송비
            $info = $this->funcomplex_lib->get_item_sendcost($row, $sell_price);
            $item_sendcost[] = $info['pattern'];
            $seller_id[$i] = $gs['mb_id'];

            $href = base_url() . 'shop/view?index_no=' . $row['gs_id'];
            //판매자 코드로 판매자 아이디 가져오기 2019-08-06 By kim=
            $seller_code = $gs['mb_id'];
            $get_seller_id_sql = "select mb_id,company_name from shop_seller where seller_code = ?";
            $param = array($seller_code);
            $seller_data = $this->query_model->returnOneArr($get_seller_id_sql, $param);
            $info['price'] = isset($info['price']) ? $info['price'] : 0;
            $returnTag .= "
            <tr>
                <td class=\"tac\">
                    <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$row['gs_id']}\">
                    <input type=\"hidden\" name=\"type[{$i}]\" value=\"{$gs['type']}\">
                    <input type=\"hidden\" name=\"gs_notax[{$i}]\" value=\"{$gs['notax']}\">
                    <input type=\"hidden\" name=\"gs_price[{$i}]\" value=\"{$sell_price}\">
                    <input type=\"hidden\" name=\"seller_id[{$i}]\" value=\"{$gs['mb_id']}\">
                    <input type=\"hidden\" name=\"seller_real_id[{$i}]\" value=\"{$seller_data['mb_id']}\">
                    <input type=\"hidden\" name=\"seller_name[{$i}]\" value=\"{$seller_data['company_name']}\">
                    <input type=\"hidden\" name=\"supply_price[{$i}]\" value=\"{$supply_price}\">
                    <input type=\"hidden\" name=\"io_price[{$i}]\" value=\"{$io_price}\">
                    <input type=\"hidden\" name=\"sum_point[{$i}]\" value=\"{$point}\">
                    <input type=\"hidden\" name=\"sum_qty[{$i}]\" value=\"{$sell_qty}\">
                    <input type=\"hidden\" name=\"cart_id[{$i}]\" value=\"{$row['od_no']}\">
                    <input type=\"hidden\" name=\"referral_code[{$i}]\" value=\"{$row['referral_code']}\">
                    " . $this->funcomplex_lib->get_it_image($row['gs_id'], $gs['simg1'], 80, 80) . "
                </td>
                <td class=\"td_name\">{$it_name}</td>
                <td class=\"tac\">" . number_format($sell_qty) . "</td>
                <td class=\"tar\">" . number_format($sell_amt) . "</td>
                <td class=\"tar\">" . number_format($sell_price) . "</td>
                <td class=\"tar\">" . number_format($point) . "</td>
                <td class=\"tar\">" . number_format($info['price']) . "</td>
            </tr>";
            $tot_point += (int)$point;
            $tot_sell_price += (int)$sell_price;
            $tot_opt_price += (int)$sell_opt_price;
            $tot_sell_qty += (int)$sell_qty;
            $tot_sell_amt += (int)$sell_amt;
            $i++;
        }
        $returnObj = array(
            'item_sendcost' => $item_sendcost,
            'returnTag' => $returnTag,
            'tot_point' => $tot_point,
            'tot_sell_price' => $tot_sell_price,
            'tot_opt_price' => $tot_opt_price,
            'tot_sell_qty' => $tot_sell_qty,
            'tot_sell_amt' => $tot_sell_amt,
            'is_timesale' => $is_timesale
        );
        return $returnObj;
    }

    private function companyType()
    {
        $config = $this->load->get_var('config');
        return "<section id=\"tax_section\" style=\"display:none;\">
            <h2 class=\"anc_tit\">증빙서류발급 요청</h2>
            <div class=\"tbl_frm01 tbl_wrap\">
                <table>
                <colgroup>
                    <col class=\"w140\">
                    <col>
                </colgroup>
                <tr>
                    <th scope=\"row\">현금영수증발행</th>
                    <td class=\"td_label\">
                        <input type=\"radio\" id=\"taxsave_1\" name=\"taxsave_yes\" value=\"Y\" onclick=\"tax_bill(1);\">
                        <label for=\"taxsave_1\">개인 소득공제용</label>
                        <input type=\"radio\" id=\"taxsave_2\" name=\"taxsave_yes\" value=\"S\" onclick=\"tax_bill(2);\">
                        <label for=\"taxsave_2\">사업자 지출증빙용</label>
                        <input type=\"radio\" id=\"taxsave_3\" name=\"taxsave_yes\" value=\"N\" onclick=\"tax_bill(3);\" checked>
                        <label for=\"taxsave_3\">미발행</label>
                    </td>
                </tr>
                <tr id=\"taxsave_fld_1\" style=\"display:none\">
                    <th scope=\"row\">핸드폰번호</th>
                    <td>
                        <input type=\"text\" name=\"tax_hp\" class=\"frm_input\" size=\"20\">
                        <span class=\"frm_info\">
                            현금영수증은 1원이상 현금 구매시 발급이 가능합니다.<br>
                            현금영수증은 구매대금 입금확인일 다음날 발급됩니다.<br>
                            현금영수증 홈페이지 :<A href=\"http://taxsave.go.kr/\" target=\"_balnk\"><b>http://www.taxsave.go.kr</b></a>
                        </span>
                    </td>
                </tr>
                <tr id=\"taxsave_fld_2\" style=\"display:none\">
                    <th scope=\"row\">사업자등록번호</th>
                    <td><input type=\"text\" name=\"tax_saupja_no\" class=\"frm_input\" size=\"20\"></td>
                </tr>
                <tr>
                    <th scope=\"row\">세금계산서발행</th>
                    <td class=\"td_label\">
                        <input type=\"radio\" id=\"taxbill_1\" name=\"taxbill_yes\" value=\"Y\" onclick=\"tax_bill(4);\">
                        <label for=\"taxbill_1\">발행요청</label>
                        <input type=\"radio\" id=\"taxbill_2\" name=\"taxbill_yes\" value=\"N\" onclick=\"tax_bill(5);\" checked>
                        <label for=\"taxbill_2\">미발행</label>
                    </td>
                </tr>
                <tr class=\"taxbill_fld\">
                    <th scope=\"row\">사업자등록번호</td>
                    <td><input type=\"text\" name=\"company_saupja_no\" size=\"20\" class=\"frm_input\"></td>
                </tr>
                <tr class=\"taxbill_fld\">
                    <th scope=\"row\">상호(법인명)</th>
                    <td><input type=\"text\" name=\"company_name\" class=\"frm_input\" size=\"20\"> 예 : {$config['company_name']}</td>
                </tr>
                <tr class=\"taxbill_fld\">
                    <th scope=\"row\">대표자</th>
                    <td><input type=\"text\" name=\"company_owner\" class=\"frm_input\" size=\"20\"> 예 : 홍길동</td>
                </tr>
                <tr class=\"taxbill_fld\">
                    <th scope=\"row\">사업장주소</th>
                    <td><input type=\"text\" name=\"company_addr\" class=\"frm_input\" size=\"60\"></td>
                </tr>
                <tr class=\"taxbill_fld\">
                    <th scope=\"row\">업태</th>
                    <td><input type=\"text\" name=\"company_item\" class=\"frm_input\" size=\"20\"> 예 : 도소매</td>
                </tr>
                <tr class=\"taxbill_fld\">
                    <th scope=\"row\">종목</th>
                    <td><input type=\"text\" name=\"company_service\" class=\"frm_input\" size=\"20\"> 예 : 전자부품</td>
                </tr>
                </table>
            </div>
        </section>";
    }

    private function quest_privacy()
    {
        return "
        <section id=\"guest_privacy\">
            <h3 class=\"anc_tit\">개인정보 수집 및 이용</h3>
            <p>비회원으로 주문 시 포인트적립 및 추가 혜택을 받을 수 없습니다.</p>
            <div class=\"tbl_head02 tbl_wrap\">
                <table>
                <thead>
                <tr>
                    <th scope=\"col\">목적</th>
                    <th scope=\"col\">항목</th>
                    <th scope=\"col\">보유기간</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>이용자 식별 및 본인 확인</td>
                    <td>이름, 비밀번호</td>
                    <td>5년(전자상거래등에서의 소비자보호에 관한 법률)</td>
                </tr>
                <tr>
                    <td>배송 및 CS대응을 위한 이용자 식별</td>
                    <td>주소, 연락처(이메일, 휴대전화번호)</td>
                    <td>5년(전자상거래등에서의 소비자보호에 관한 법률)</td>
                </tr>
                </tbody>
                </table>
            </div>

            <fieldset id=\"guest_agree\">
                <input type=\"checkbox\" id=\"agree\" value=\"1\">
                <label for=\"agree\">개인정보 수집 및 이용 내용을 읽었으며 이에 동의합니다.</label>
            </fieldset>
        </section>";
    }

    public function ordercoupon()
    {
        $config = $this->load->get_var('config');
        $is_member = $this->load->get_var('is_member');

        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            goto_url(base_url() . 'm/shop/ordercoupon');
        }

        $ss_cart_id = get_session('ss_cart_id');

        if (!$config['coupon_yes']) {
            alert_close("쿠폰사용이 중지 되었습니다.");
        }

        if (!$is_member) {
            alert_close("회원만 이용가능한 서비스 입니다");
        }

        if (!$ss_cart_id) {
            alert_close("장바구니에 담긴 상품이 없습니다.");
        }

        $this->template->header_sub();
        $this->ordercouponSkin();
        $this->template->footer_sub();
    }

    private function ordercouponSkin()
    {
        $ss_cart_id = get_session('ss_cart_id');
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $page = $this->input->get('page');
        $title = '쿠폰적용 및 조회';
        $cp_tmp = array();

        $query = $this->global_lib->get_cp_precompose($member['id']);
        $sql_common  = $query[0];
        $sql_search  = $query[1];
        $sql_order     = $query[2];
        $total_count = $query[3];

        $rows = 10;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = "SELECT * $sql_common $sql_search $sql_order LIMIT $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $sql2 = "SELECT * FROM shop_cart WHERE index_no IN ({$ss_cart_id}) GROUP BY gs_id ORDER BY index_no ";
        $result2Obj = $this->query_model->returnQueryObj($sql2);
        $cart_count = $result2Obj->num_rows();
        $result2 = $result2Obj->result_array();
        $result2Table = $this->ordercouponTable2($result2);

        $cp_tmp = $this->orderCptmp($result2);
        $result1TableObj = $this->ordercouponTable1($result, $cart_count, $cp_tmp);
        $is_possible = $result1TableObj['is_possible'];
        $result1Table = $result1TableObj['returnTag'];

        $empty = "";
        if (!$total_count) {
            $empty = '<tr><td colspan="2" class="empty_list">자료가 없습니다.</td></tr>';
        }
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?page=');
        $result2Table2 = $this->ordercouponTable3($result2, $is_possible);

        $data = array(
            'title' => $title,
            'result2Table' => $result2Table,
            'total_count' => $total_count,
            'member' => $member,
            'result1Table' => $result1Table,
            'empty' => $empty,
            'pagination' => $pagination,
            'result2Table2' => $result2Table2,
            'cart_count' => $cart_count
        );

        $this->load->view('pc/shop/ordercoupon', $data);
    }

    private function orderCptmp($result2)
    {
        $cp_tmp = array();
        $member = $this->load->get_var('member');

        foreach ($result2 as $key => $row) {
            $gs = $this->global_lib->get_goods($row['gs_id']);

            // 합계금액 계산
            $sql = "SELECT SUM(IF(io_type = 1, (io_price * ct_qty),((io_price + ct_price) * ct_qty))) as price,
                            SUM(IF(io_type = 1, (0),(ct_point * ct_qty))) as point,
                            SUM(ct_qty) as qty,
                            SUM(io_price * ct_qty) as opt_price
                    FROM  shop_cart
                    WHERE index_no = ?
                    AND   mb_id = ?
                    AND   ct_select = 0";
            $data = array($row['index_no'], $member['id']);
            $sum = $this->query_model->returnOneArr($sql, $data);

            $price = $sum['price'];
            // 소속 카테고리를 콤마로 구분하여 추출
            $ca_list = $this->funcomplex_lib->get_extract($row['gs_id']);
            $cp_tmp[] = $price . "|" . $row['gs_id'] . "|" . $ca_list . "|" . $gs['use_aff'];
        }
        return $cp_tmp;
    }

    private function ordercouponTable1($result, $cart_count, $cp_tmp)
    {
        $returnTag = "";
        $i = 0;
        $is_possible = array();
        foreach ($result as $key => $row) {
            $lo_id = $row['lo_id'];
            $str = $this->funcomplex_lib->get_cp_contents($row);
            for ($j = 0; $j < $cart_count; $j++) {
                $is_coupon = false;
                $is_gubun = explode("|", $cp_tmp[$j]);
                switch ($row['cp_use_part']) {
                    case '0': // 전체상품에 쿠폰사용 가능
                        $is_coupon = true;
                        break;
                    case '1': // 일부 상품만 쿠폰사용 가능
                        if ($row['cp_use_goods']) {
                            $fields_cnt = get_substr_count($is_gubun[1], $row['cp_use_goods']);
                            if ($fields_cnt)
                                $is_coupon = true;
                        }
                        break;
                    case '2': // 일부 카테고리만 쿠폰사용 가능
                        if ($row['cp_use_category']) {
                            $fields_cnt = get_substr_count($is_gubun[2], $row['cp_use_category']);
                            if ($fields_cnt)
                                $is_coupon = true;
                        }
                        break;
                    case '3': // 일부 상품은 쿠폰사용 불가
                        if ($row['cp_use_goods']) {
                            $fields_cnt = get_substr_count($is_gubun[1], $row['cp_use_goods']);
                            if (!$fields_cnt)
                                $is_coupon = true;
                        }
                        break;
                    case '4': // 일부 카테고리는 쿠폰사용 불가
                        if ($row['cp_use_category']) {
                            $fields_cnt = get_substr_count($is_gubun[2], $row['cp_use_category']);
                            if (!$fields_cnt)
                                $is_coupon = true;
                        }
                        break;
                }

                // 적용여부 && 가맹점상품제외 && 최대금액 <= 상품금액
                $seq = array();
                if ($is_coupon && !$is_gubun[3] && ($row['cp_low_amt'] <= (int)$is_gubun[0])) {
                    // 할인해택 검사
                    $amt =  $this->funcomplex_lib->get_cp_sale_amt($row, (int)$is_gubun[0]);
                    $seq[] = $is_gubun[1];
                    $seq[] = $lo_id;
                    $seq[] = $row['cp_id'];
                    $seq[] = $row['cp_dups'];
                    $seq[] = $amt[1];
                    $seq[] = $amt[0];
                    $seq[] = $row['cp_subject'];
                    $is_possible[] = implode("|", $seq);
                }
            }

            $bg = 'list' . ($i % 2);
            $returnTag .= "
                <tr class=\"{$bg}\">
                    <td class=\"tac\">{$row['cp_id']}</td>
                    <td>{$str}</td>
                </tr>";
            $i++;
        }
        $returnObj = array('is_possible' => $is_possible, 'returnTag' => $returnTag);
        return $returnObj;
    }

    private function ordercouponTable2($result2)
    {
        $set_cart_id = get_cookie('ck_guest_cart_id');
        $member = $this->load->get_var('member');
        $returnTag = "";
        $i = 0;
        foreach ($result2 as $key => $row) {
            $gs = $this->global_lib->get_goods($row['gs_id']);
            // 합계금액 계산
            $sql = "SELECT SUM(IF(io_type = 1, (io_price * ct_qty),((io_price + ct_price) * ct_qty))) as price,
                            SUM(IF(io_type = 1, (0),(ct_point * ct_qty))) as point,
                            SUM(ct_qty) as qty,
                            SUM(io_price * ct_qty) as opt_price
                    FROM  shop_cart
                    WHERE index_no = ?
                    AND   mb_id = ?
                    AND   ct_select = 0";
            $data = array($row['index_no'], $member['id']);
            $sum = $this->query_model->returnOneArr($sql, $data);

            $price = $sum['price'];
            // 소속 카테고리를 콤마로 구분하여 추출
            $ca_list = $this->funcomplex_lib->get_extract($row['gs_id']);
            $cp_tmp[] = $price . "|" . $row['gs_id'] . "|" . $ca_list . "|" . $gs['use_aff'];

            $bg = 'list' . ($i % 2);
            $returnTag .= "
            <tr class=\"{$bg}\">
                <td class=\"tac\">
                    <input type=\"hidden\" name=\"gd_dc_amt_{$i}\">
                    <input type=\"hidden\" name=\"gd_cp_info_{$i}\">
                    <input type=\"hidden\" name=\"gd_cp_no_{$i}\">
                    <input type=\"hidden\" name=\"gd_cp_idx_{$i}\">";
            $returnTag .= $this->funcomplex_lib->get_it_image($row['gs_id'], $gs['simg1'], 60, 60);
            $returnTag .= "
                </td>
                <td class=\"td_name\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</td>
                <td class=\"tac\">
                    <div>" . display_price2($price) . "</div>
                    <div class='padt5 fc_197'>(수량:{$sum['qty']})</div>
                </td>
                <td class=\"tac\">
                    <span id=\"cp_avail_button_{$i}\">
                    <a href=\"#\" onclick=\"show_coupon('{$i}');return false;\" class=\"btn_small\">쿠폰선택</a>
                    </span>
                </td>
                <td class=\"tar\">
                    <span id=\"dc_amt_{$i}\">0</span>원
                    <span id=\"dc_cancel_bt_{$i}\" style=\"display:none\"><a href=\"javascript:coupon_cancel('{$row['gs_id']}','{$row['index_no']}','{$i}');\">X</a></span>
                </td>
            </tr>";
            $i++;
        }
        return $returnTag;
    }

    private function ordercouponTable3($result2, $is_possible)
    {
        $i = 0;
        $returnTag = "";
        foreach ($result2 as $key => $row) {
            $returnTag .= "
            <div id=\"cp_list{$i}\" class=\"apply_cmd\" style=\"display:none;\">
                <table width=\"306\">
                    <tr>
                        <td>
                            <img src=\"" . base_url() . "img/coupon_apply_title.gif\" usemap=\"#coupon_apply{$i}\">
                            <map name=\"coupon_apply{$i}\">
                            <area shape=\"rect\" coords=\"286,0,304,14\" href=\"#\" onclick=\"hide_cp_list('{$i}'); return false;\">
                            </map>
                        </td>
                    </tr>
                </table>

                <div class=\"tbl_head02\">
                    <table width=\"306\">
                        <thead>
                            <tr>
                                <th scope=\"col\">쿠폰번호</th>
                                <th scope=\"col\">쿠폰이름</th>
                                <th scope=\"col\">할인금액(율)</th>
                                <th scope=\"col\">할인가</th>
                            </tr>
                        </thead>
                        <tbody>";
            //5|1|8|0|10%|37496
            // 상품주키|쿠폰주키|쿠폰번호|동시사용 여부|할인금액(율)|할인가
            $chk = 0;
            $returnSubTag = "";
            for ($j = 0; $j < count($is_possible); $j++) {
                $arr = explode("|", $is_possible[$j]);
                if ($row['gs_id'] == $arr[0]) {
                    $chk++;
                    $returnSubTag .= "
                                    <tr>
                                        <td class=\"tac\"><input type=\"radio\" name=\"use_cp_{$row['gs_id']}_{$row['index_no']}\" value=\"{$arr[2]}|{$arr[5]}|{$arr[1]}|{$arr[3]}\"> <b>{$arr[2]}</b></td>
                                        <td class=\"tac\">{$arr[6]}</td>
                                        <td class=\"tac\">{$arr[4]}</td>
                                        <td class=\"tac\">" . display_price2($arr[5]) . "</td>
                                    </tr>";
                }
            }

            if ($chk == 0) {
                $returnSubTag = '<tr><td colspan="3" class="empty_table">사용할 수 있는 쿠폰이 없습니다.</td></tr>';
            }
            $returnTag .= $returnSubTag;
            $returnTag .= "
                        </tbody>
                    </table>
                </div>
                <div class=\"btn_confirm mart10\">
                    <button type=\"button\" onclick=\"return applycoupon('{$row['gs_id']}','{$row['index_no']}','{$i}');\" class=\"btn_small\">쿠폰적용하기</button>
                </div>
            </div>";
            $i++;
        }
        return $returnTag;
    }

    #orderaddress 배송지 목록으로 했을 때 보여질 화면
    public function orderaddress()
    {
        $is_member = $this->load->get_var('is_member');
        if (!$is_member) {
            alert_close("회원 전용 서비스입니다.");
        }
        $this->template->header_sub();
        $this->orderaddressSkin();
        $this->template->footer_sub();
    }

    private function orderaddressSkin()
    {
        $member = $this->load->get_var('member');
        $title = "배송지 목록";

        $sql_common = " from shop_order ";
        $sql_search = " where mb_id = '{$member['id']}' ";
        $sql_order  = " group by od_id order by od_time desc ";

        $sql = " select COUNT(DISTINCT od_id) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $sql = " select * $sql_common $sql_search $sql_order ";
        $result = $this->query_model->returnArr($sql);
        $sep = ' ';
        $k = 0;
        $ar_mk = array();
        $addressList = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $info = array();
            $info[] = $row['b_name'];
            $info[] = $row['b_cellphone'];
            $info[] = $row['b_telephone'];
            $info[] = $row['b_zip'];
            $info[] = $row['b_addr1'];
            $info[] = $row['b_addr2'];
            $info[] = $row['b_addr3'];
            $info[] = $row['b_addr_jibeon'];

            $addr = implode($sep, $info);
            $addr = $this->funcomplex_lib->get_text($addr);

            if (!in_array($addr, $ar_mk)) {
                $k++;
                $ar_mk[$i] = $addr;

                $bg = 'list' . ($k % 2);
                $addressList .= "
                <tr class=\"{$bg}\">
                    <td class=\"tac\">{$k}</td>
                    <td>" . $this->funcomplex_lib->print_address($row['b_addr1'], $row['b_addr2'], $row['b_addr3'], $row['b_addr_jibeon']) . "</td>
                    <td class=\"tac\">
                        <input type=\"hidden\" value=\"{$addr}\">
                        <a href=\"javascript:void(0);\" class=\"sel_address btn_small\">선택</a>
                    </td>
                </tr>";
            }
            $i++;
        }
        if ($k == 0) {
            $addressList = '<tr><td colspan="3" class="empty_list">자료가 없습니다.</td></tr>';
        }

        $data = array('title' => $title, 'addressList' => $addressList);
        $this->load->view('pc/shop/orderaddress', $data);
    }

    #pop_coupon
    public function pop_coupon()
    {
        $config = $this->load->get_var('config');
        $is_member = $this->load->get_var('is_member');
        if (!$config['coupon_yes']) {
            alert_close("쿠폰사용이 중지 되었습니다.");
        }

        if (!$is_member) {
            alert_close('회원만 이용가능한 서비스 입니다');
        }
        $this->template->header_sub();
        $this->pop_couponSkin();
        $this->template->footer_sub();
    }

    private function pop_couponSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $title = "쿠폰 다운받기";

        $gs_id = $this->input->get('gs_id');
        $page = $this->input->get('page');

        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');
        $gs = $this->global_lib->get_goods($gs_id);
        $cp_list = $this->funcomplex_lib->is_used_coupon('0', $gs_id, $member['id']);
        if (empty($cp_list)) $cp_list = "NULL";

        $sql_common = " FROM shop_coupon ";
        $sql_search = " WHERE cp_id IN ({$cp_list}) AND (cp_dlevel = '99' OR cp_dlevel <= '{$member['grade']}')";
        $sql_order  = " ORDER BY cp_id ";

        $sql = "SELECT count(*) AS cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 10;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $token = md5(uniqid(rand(), true));
        set_session('ss_token', $token);
        $itImage = $this->funcomplex_lib->get_it_image($gs['index_no'], $gs['simg1'], 60, 60);
        $gsName = $this->funcomplex_lib->get_text($gs['gname']);
        $gsPrice = $this->funcomplex_lib->get_price($gs['index_no']);
        $popTable = $this->popCouponTable($result);
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?page=');
        $data = array(
            'title' => $title,
            'itImage' => $itImage,
            'gsName' => $gsName,
            'gsPrice' => $gsPrice,
            'total_count' => $total_count,
            'popTable' => $popTable,
            'gs_id' => $gs_id,
            'page' => $page,
            'token' => $token,
            'pagination' => $pagination
        );

        $this->load->view('pc/shop/pop_coupon', $data);
    }

    private function popCouponTable($res)
    {
        $i = 0;
        $returnStr = "";
        $member = $this->load->get_var('member');
        foreach ($res as $key => $row) {
            $cp_id = $row['cp_id'];

            $str  = "";
            $str .= "<div>&#183; <strong>" . $this->funcomplex_lib->get_text($row['cp_subject']) . "</strong></div>";
            if ($row['cp_explan'])
                $str .= "<div class='part5 fc_197'>&#183; " . $this->funcomplex_lib->get_text($row['cp_explan']) . "</div>";

            // 동시사용 여부
            $str .= "<div class='part5 fc_eb7'>&#183; ";
            if (!$row['cp_dups']) {
                $str .= '동일한 주문건에 다른 쿠폰과 동시 사용가능';
            } else {
                $str .= '동일한 주문건에 다른 쿠폰과 동시 사용불가 (이 쿠폰만 사용가능)';
            }
            $str .= "</div>";

            // 쿠폰발행 기간
            $str .= "<div class='part5'>&#183; 다운로드 기간 : ";
            if ($row['cp_type'] != '3') {
                if ($row['cp_pub_sdate'] == '9999999999') $cp_pub_sdate = '';
                else $cp_pub_sdate = $row['cp_pub_sdate'];

                if ($row['cp_pub_edate'] == '9999999999') $cp_pub_edate = '';
                else $cp_pub_edate = $row['cp_pub_edate'];

                if ($row['cp_pub_sdate'] == '9999999999' && $row['cp_pub_edate'] == '9999999999')
                    $str .= "제한없음";
                else
                    $str .= $cp_pub_sdate . " ~ " . $cp_pub_edate;

                // 쿠폰발행 요일
                if ($row['cp_type'] == '1') {
                    $str .= "&nbsp;-&nbsp;매주 (" . $row['cp_week_day'] . ")";
                }
            } else {
                $str .= "생일 (" . $row['cp_pub_sday'] . "일 전 ~ " . $row['cp_pub_eday'] . "일 이후까지)";
            }
            $str .= "</div>";


            // 쿠폰유효 기간
            $str .= "<div class='part5'>&#183; 쿠폰유효 기간 : ";
            if (!$row['cp_inv_type']) {
                // 날짜
                if ($row['cp_inv_sdate'] == '9999999999') $cp_inv_sdate = '';
                else $cp_inv_sdate = $row['cp_inv_sdate'];

                if ($row['cp_inv_edate'] == '9999999999') $cp_inv_edate = '';
                else $cp_inv_edate = $row['cp_inv_edate'];

                if ($row['cp_inv_sdate'] == '9999999999' && $row['cp_inv_edate'] == '9999999999')
                    $str .= '제한없음';
                else
                    $str .= $cp_inv_sdate . " ~ " . $cp_inv_edate;

                // 시간대
                $str .= "&nbsp;(시간대 : ";
                if ($row['cp_inv_shour1'] == '99') $cp_inv_shour1 = '';
                else $cp_inv_shour1 = $row['cp_inv_shour1'] . "시부터";

                if ($row['cp_inv_shour2'] == '99') $cp_inv_shour2 = '';
                else $cp_inv_shour2 = $row['cp_inv_shour2'] . "시까지";

                if ($row['cp_inv_shour1'] == '99' && $row['cp_inv_shour2'] == '99') $str .= '제한없음';
                else $str .= $cp_inv_shour1 . " ~ " . $cp_inv_shour2;
                $str .= ")";
            } else {
                $cp_inv_day = date("Y-m-d", strtotime("+{$row['cp_inv_day']} days", time()));
                $str .= '다운로드 완료 후 ' . $row['cp_inv_day'] . '일간 사용가능, 만료일(' . $cp_inv_day . ')';
            }
            $str .= "</div>";
            // 혜택
            $str .= "<div class='part5'>&#183; ";
            if (!$row['cp_sale_type']) {
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
                $str .= "<div class='part5'>&#183; " . display_price($row['cp_low_amt']) . " 이상 구매시</div>";
            }
            // 사용가능대상
            $gw_usepart = $this->config->item('gw_usepart');
            $str .= "<div class='part5'>&#183; " . $gw_usepart[$row['cp_use_part']] . "</div>";

            $s_upd = "<a href=\"javascript:post_update('" . base_url() . "process/pop_coupon_update', '$cp_id');\" class=\"btn_small red\">다운로드</a>";

            $bg = 'list' . ($i % 2);
            $returnStr .= "
            <tr class=\"{$bg}\">
                <td>{$str}</td>
                <td class=\"tac\">{$s_upd}</td>
            </tr>";
            $i++;
        }
        if ($i == 0) {
            $returnStr = '<tr><td colspan="2" class="empty_list">자료가 없습니다.</td></tr>';
        }
        return $returnStr;
    }

    #qaform
    public function qaform()
    {
        $gs_id = $this->input->get('gs_id');
        $is_member = $this->load->get_var('is_member');
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && ($this->agent->is_mobile() || $this->load->get_var('is_mobile'))) {
            goto_url(base_url() . 'm/shop/qaform?gs_id=' . $gs_id);
        }
        if (!$is_member) {
            alert_close("로그인 후 작성 가능합니다.");
        }
        $this->template->header_sub();
        $this->qaformSkin();
        $this->template->footer_sub();
    }

    private function qaformSkin()
    {
        $gs_id = $this->input->get('gs_id');
        $iq_id = $this->input->get('iq_id');
        $iq_ty = $this->input->get('iq_ty');

        $member = $this->load->get_var('member');

        $w = $this->input->get('w');
        $title = "상품문의 쓰기";
        $gs = $this->global_lib->get_goods($gs_id);
        $iq_name = $iq_email = $iq_hp = $iq_subject = $iq_question = $iq_secret = "";
        if ($w == "") {
            $iq_name     = $member['name'];
            $iq_email     = $member['email'];
            $iq_hp         = replace_tel($member['cellphone']);
        } else if ($w == "u") {
            $sql = "select * from shop_goods_qa where iq_id=?";
            $param = array($iq_id);
            $iq = $this->query_model->returnOneArr($sql, $param);
            $iq_ty         = $iq['iq_ty'];
            $iq_name     = $iq['iq_name'];
            $iq_email     = $iq['iq_email'];
            $iq_hp         = $iq['iq_hp'];
            $iq_subject  = $iq['iq_subject'];
            $iq_question = $iq['iq_question'];
            $iq_secret     = nl2br($iq['iq_secret']);

            if (!$this->gvcomplex_lib->is_admin()) {
                if ($iq['mb_id'] != $member['id']) {
                    alert('잘못된 접근입니다.');
                }
            }
        }
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        $form_action_url = base_url() . 'process/qaform_update';
        $selectoptTheme = get_selected($iq_ty, "");
        $selectoptGs = get_selected($iq_ty, "상품");
        $selectoptDeliver = get_selected($iq_ty, "배송");
        $selectoptCancel = get_selected($iq_ty, "반품/환불/취소");
        $selectoptChange = get_selected($iq_ty, "교환/변경");
        $selectoptEct = get_selected($iq_ty, "기타");
        $secretTag = get_checked($iq_secret, '1');
        $data = array(
            'title' => $title,
            'token' => $token,
            'form_action_url' => $form_action_url,
            'w' => $w,
            'gs_id' => $gs_id,
            'member' => $member,
            'gs' => $gs,
            'iq_id' => $iq_id,
            'selectoptTheme' => $selectoptTheme,
            'selectoptGs' => $selectoptGs,
            'selectoptDeliver' => $selectoptDeliver,
            'selectoptCancel' => $selectoptCancel,
            'selectoptChange' => $selectoptChange,
            'selectoptEct' => $selectoptEct,
            'secretTag' => $secretTag,
            'iq_name' => $iq_name,
            'iq_email' => $iq_email,
            'iq_hp' => $iq_hp,
            'iq_subject' => $iq_subject,
            'iq_question' => $iq_question
        );
        $this->load->view('pc/shop/qaform', $data);
    }

    #price 결제 관리자 가격비교사이트 연동에서 클릭시 이동
    public function price()
    {
        $param = $this->uri->segment(3);
        switch ($param) {
            case 'naver':
                $this->naver_price();
                break;
            case 'naver_summary':
                $this->naver_summary();
                break;
            case 'daum':
                $this->daum_price();
                break;
            case 'daum_summary':
                $this->daum_summary();
                break;
            default:
                # code...
                break;
        }
    }

    private function naver_price()
    {
        $tab = "\t";

        ob_start();

        echo "id{$tab}title{$tab}price_pc{$tab}link{$tab}image_link{$tab}category_name1{$tab}category_name2{$tab}category_name3{$tab}model_number{$tab}brand{$tab}maker{$tab}origin{$tab}point{$tab}shipping";

        $sql_cost = "";
        $sql_common = $this->gvcomplex_lib->sql_goods_list($sql_cost);
        $sql_order = " order by index_no desc ";

        $sql = " select * $sql_common $sql_order ";
        $result = $this->query_model->returnArr($sql);
        $i = 0;
        foreach ($result as $key => $row) {
            $cate1 = $cate2 = $cate3 = '';
            $caid1 = $caid2 = $caid3 = '';

            if (strlen($row['ca_id']) >= 9) {
                $caid3 = substr($row['ca_id'], 0, 9);
                $sql = "select catename from shop_category where catecode = ?";
                $param = array($caid3);
                $row2 = $this->query_model->returnOneArr($sql, $param);
                $cate3 = $row2['catename'];
            }
            if (strlen($row['ca_id']) >= 6) {
                $caid2 = substr($row['ca_id'], 0, 6);
                $sql = "select catename from shop_category where catecode = ?";
                $param = array($caid2);
                $row2 = $this->query_model->returnOneArr($sql, $param);
                $cate2 = $row2['catename'];
            }
            if (strlen($row['ca_id']) >= 3) {
                $caid1 = substr($row['ca_id'], 0, 3);
                $sql = "select catename from shop_category where catecode = ?";
                $param = array($caid1);
                $row2 = $this->query_model->returnOneArr($sql, $param);
                $cate1 = $row2['catename'];
            }

            // 배송비계산
            $delivery = $this->funcomplex_lib->get_sendcost_amt2($row['index_no'], $row['goods_price']);

            // 상품이미지
            $img_url = $this->funcomplex_lib->get_it_image_url($row['index_no'], $row['simg2'], 400, 400);

            $item_link = base_url() . 'shop/view?index_no=' . $row['index_no'];

            echo "\n{$row['index_no']}{$tab}{$row['gname']}{$tab}{$row['goods_price']}{$tab}{$item_link}{$tab}{$img_url}{$tab}{$cate1}{$tab}{$cate2}{$tab}{$cate3}{$tab}{$row['model']}{$tab}{$row['brand_nm']}{$tab}{$row['maker']}{$tab}{$row['origin']}{$tab}{$row['gpoint']}{$tab}{$delivery}";
            $i++;
        }

        $content = ob_get_contents();
        ob_end_clean();

        echo $content;
    }

    private function naver_summary()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        // clean the output buffer
        ob_end_clean();
        /*
        EP 버전 3.0

        네이버지식쇼핑상품EP (Engine Page) 제작및연동가이드 (제휴사제공용)
        http://join.shopping.naver.com/misc/download/ep_guide.nhn

        Field                   Status  Notes
        id                      필수    판매하는 상품의 유니크한 상품ID
        title                   필수    실제 서비스에 반영될 상품명(Title)
        price_pc                필수    상품가격
        link                    필수    상품URL
        image_link              필수    해당 상품의 이미지URL
        category_name1          필수    카테고리명(대분류)
        category_name2          권장    카테고리명(중분류)
        category_name3          권장    카테고리명(소분류)
        category_name4          권장    카테고리명(세분류)
        model_number            권장    모델명
        brand                   권장    브랜드
        maker                   권장    제조사
        origin                  권장    원산지
        event_words             권장    이벤트
        coupon                  권장    쿠폰
        interest_free_event     권장    무이자
        point                   권장    포인트
        shipping                필수    배송료
        seller_id               권장    셀러 ID (오픈마켓에 한함)
        class                   필수(요약)  I (신규상품) / U (업데이트 상품) / D (품절상품)
        update_time             필수(요약)  상품정보 생성 시각
        */

        $tab = "\t";

        ob_start();

        echo "id{$tab}title{$tab}price_pc{$tab}link{$tab}image_link{$tab}category_name1{$tab}category_name2{$tab}category_name3{$tab}model_number{$tab}brand{$tab}maker{$tab}origin{$tab}point{$tab}shipping{$tab}class{$tab}update_time";

        $sql_search = " and SUBSTRING(update_time, 1, 10) = '" . $OM_TIME_YMD . "' ";
        $sql_common = $this->gvcomplex_lib->sql_goods_list($sql_search);
        $sql_order  = " order by index_no desc ";

        $sql = " select * $sql_common $sql_order ";
        $result = $this->query_model->returnArr($sql);
        $i = 0;
        foreach ($result as $key => $row) {
            $cate1 = $cate2 = $cate3 = '';
            $caid1 = $caid2 = $caid3 = '';

            if (strlen($row['ca_id']) >= 9) {
                $caid3 = substr($row['ca_id'], 0, 9);
                $sql = "select catename from shop_category where catecode = ?";
                $param = array($caid3);
                $row2 = $this->query_model->returnOneArr($sql, $param);
                $cate3 = $row2['catename'];
            }
            if (strlen($row['ca_id']) >= 6) {
                $caid2 = substr($row['ca_id'], 0, 6);
                $sql = "select catename from shop_category where catecode = ?";
                $param = array($caid2);
                $row2 = $this->query_model->returnOneArr($sql, $param);
                $cate2 = $row2['catename'];
            }
            if (strlen($row['ca_id']) >= 3) {
                $caid1 = substr($row['ca_id'], 0, 3);
                $sql = "select catename from shop_category where catecode = ?";
                $param = array($caid1);
                $row2 = $this->query_model->returnOneArr($sql, $param);
                $cate1 = $row2['catename'];
            }

            // 배송비계산
            $delivery = $this->funcomplex_lib->get_sendcost_amt2($row['index_no'], $row['goods_price']);

            // 상품이미지
            $img_url = $this->funcomplex_lib->get_it_image_url($row['index_no'], $row['simg2'], 400, 400);

            $item_link = base_url() . 'shop/view?index_no=' . $row['index_no'];

            // 상태
            $class = 'U';
            $stock_qty = $this->global_lib->get_it_stock_qty($row['index_no']);

            if (substr($row['reg_time'], 0, 10) == $OM_TIME_YMD && $row['update_time'] >= $row['reg_time'])
                $class = 'I';

            if ($row['isopen'] > 1 || $stock_qty < 0)
                $class = 'D';

            echo "\n{$row['index_no']}{$tab}{$row['gname']}{$tab}{$row['goods_price']}{$tab}{$item_link}{$tab}{$img_url}{$tab}{$cate1}{$tab}{$cate2}{$tab}{$cate3}{$tab}{$row['model']}{$tab}{$row['brand_nm']}{$tab}{$row['maker']}{$tab}{$row['origin']}{$tab}{$row['gpoint']}{$tab}{$delivery}{$tab}{$class}{$tab}{$row['update_time']}";
            $i++;
        }

        $content = ob_get_contents();
        ob_end_clean();

        echo $content;
    }

    private function daum_price()
    {
        ob_start();

        header("Content-Type: text/html; charset=utf-8");

        /*
        구분 태그명 내용    설명    크기
        <<<tocnt>>>     전체상품수
        <<<begin>>>     시작    상품시작 알림   필수
        <<<mapid>>>     상품ID  해당사 상품 ID  필수
        <<<lprice>>>    원판매가(할인전가격)    선택적필수
        <<<price>>>     할인적용가  할인후가격  필수
        <<<mprice>>>    모바일 할인적용가  할인후가격  선택적필수
        <<<pname>>>     상품명  상품명  필수,varchar(500)
        <<<pgurl>>>     상품링크    해당 상품으로 갈 상품URL    필수
        <<<igurl>>>     이미지링크  상품이미지 링크
                        (상품이미지 중 제일 큰이미지링크)   필수,varchar(255)
        <<<cate1>>>     카테고리명  대분류명 필수
        <<<caid1>>>     카테고리 ID(대분류)  필수
        <<<cate2>>>     카테고리명  중분류명
        <<<caid2>>>     카테고리 ID(중분류)
        <<<cate3>>>     카테고리명  소분류명
        <<<caid3>>>     카테고리 ID(소분류)
        <<<cate4>>>     카테고리명  세분류명
        <<<caid4>>>     카테고리 ID(세분류)

        <<<model>>>     모델명
        <<<brand>>>     브랜드명
        <<<maker>>>     제조사

        <<<coupo>>>     쿠폰/제휴쿠폰
        <<<mcoupo>>>    모바일 쿠폰/제휴쿠폰
        <<<pcard>>>     무이자할부
        <<<point>>>     적립금/포인트
        <<<deliv>>>     배송비  무료일 때는 0, 유료일 때는 배송금액, 착불은 -1
        <<<event>>>     이벤트
        <<<weight>>>    가중치값

        <<<selid>>>     셀러 ID   선택
        <<<insco>>>     별도설치비
        <<<ftend>>>     끝알림 필수
        */

        $lt = "<<<";
        $gt = ">>>";
        $shop_url = base_url() . 'shop';
        $data_url = base_url() . 'data';

        $sql_search = "";
        $sql_common = $this->gvcomplex_lib->sql_goods_list($sql_search);
        $sql_order  = " order by index_no desc ";

        $sql = " select * $sql_common $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $result = $resultObj->result_array();
        $totcnt = $resultObj->num_rows();

        echo $lt . 'tocnt' . $gt . $totcnt;

        foreach ($result as $key => $row) {
            $cate1 = $cate2 = $cate3 = '';
            $caid1 = $caid2 = $caid3 = '';

            if (strlen($row['ca_id']) >= 9) {
                $caid3 = substr($row['ca_id'], 0, 9);
                $sql = "select catename from shop_category where catecode = ?";
                $param = array($caid3);
                $row2 = $this->query_model->returnOneArr($sql, $param);
                $cate3 = $row2['catename'];
            }
            if (strlen($row['ca_id']) >= 6) {
                $caid2 = substr($row['ca_id'], 0, 6);
                $sql = "select catename from shop_category where catecode = ?";
                $param = array($caid2);
                $row2 = $this->query_model->returnOneArr($sql, $param);
                $cate2 = $row2['catename'];
            }
            if (strlen($row['ca_id']) >= 3) {
                $caid1 = substr($row['ca_id'], 0, 3);
                $sql = "select catename from shop_category where catecode = ?";
                $param = array($caid1);
                $row2 = $this->query_model->returnOneArr($sql, $param);
                $cate1 = $row2['catename'];
            }

            $point = (int)$row['gpoint'];
            if ($point) {
                $point .= '원';
            }

            // 배송비계산
            $deliv = $this->funcomplex_lib->get_sendcost_amt2($row['index_no'], $row['goods_price']);

            // 상품이미지
            $img_url = $this->funcomplex_lib->get_it_image_url($row['index_no'], $row['simg2'], 400, 400);

            $str = "{$lt}begin{$gt}";
            $str .= "{$lt}mapid{$gt}{$row['index_no']}";
            $str .= "{$lt}price{$gt}{$row['goods_price']}";
            $str .= "{$lt}pname{$gt}{$row['gname']}";
            $str .= "{$lt}pgurl{$gt}$shop_url/view?index_no={$row['index_no']}";
            $str .= "{$lt}igurl{$gt}$img_url";
            $str .= "{$lt}cate1{$gt}$cate1";
            $str .= "{$lt}caid1{$gt}$caid1";
            if ($cate2) {
                $str .= "{$lt}cate2{$gt}$cate2";
            }
            if ($caid2) {
                $str .= "{$lt}caid2{$gt}$caid2";
            }
            if ($cate3) {
                $str .= "{$lt}cate3{$gt}$cate3";
            }
            if ($caid3) {
                $str .= "{$lt}caid3{$gt}$caid3";
            }
            if ($row['model']) {
                $str .= "{$lt}model{$gt}{$row['model']}";
            }
            if ($row['brand_nm']) {
                $str .= "{$lt}brand{$gt}{$row['brand_nm']}";
            }
            if ($row['maker']) {
                $str .= "{$lt}maker{$gt}{$row['maker']}";
            }
            $str .= "{$lt}point{$gt}$point";
            $str .= "{$lt}deliv{$gt}$deliv";
            $str .= "{$lt}ftend{$gt}";

            echo iconv('utf-8', 'euc-kr', $str);
        }

        $content = ob_get_contents();
        ob_end_clean();

        echo $content;
    }

    private function daum_summary()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        ob_start();
        header("Content-Type: text/html; charset=utf-8");
        /*
        구분 태그명 내용    설명    크기
        <<<begin>>>     시작    상품시작 알림   필수
        <<<mapid>>>     상품ID  해당사 상품 ID  필수
        <<<lprice>>>    원판매가(할인전가격)    선택적필수
        <<<price>>>     할인적용가  할인후가격  필수
        <<<mprice>>>    모바일 할인적용가  할인후가격  선택적필수
        <<<pname>>>     상품명  상품명  필수,varchar(500)
        <<<pgurl>>>     상품링크    해당 상품으로 갈 상품URL    필수
        <<<igurl>>>     이미지링크  상품이미지 링크
                        (상품이미지 중 제일 큰이미지링크)   필수,varchar(255)
        <<<cate1>>>     카테고리명  대분류명 필수
        <<<caid1>>>     카테고리 ID(대분류)  필수
        <<<cate2>>>     카테고리명  중분류명
        <<<caid2>>>     카테고리 ID(중분류)
        <<<cate3>>>     카테고리명  소분류명
        <<<caid3>>>     카테고리 ID(소분류)
        <<<cate4>>>     카테고리명  세분류명
        <<<caid4>>>     카테고리 ID(세분류)

        <<<model>>>     모델명
        <<<brand>>>     브랜드명
        <<<maker>>>     제조사

        <<<coupo>>>     쿠폰/제휴쿠폰
        <<<mcoupo>>>    모바일 쿠폰/제휴쿠폰
        <<<pcard>>>     무이자할부
        <<<point>>>     적립금/포인트
        <<<deliv>>>     배송비  무료일 때는 0, 유료일 때는 배송금액, 착불은 -1
        <<<event>>>     이벤트
        <<<weight>>>    가중치값

        <<<selid>>>     셀러 ID   선택
        <<<insco>>>     별도설치비
        <<<ftend>>>     끝알림 필수
        */

        $lt = "<<<";
        $gt = ">>>";
        $shop_url = base_url() . 'shop';
        $data_url = base_url() . 'data';

        $sql_search = " and SUBSTRING(update_time, 1, 10) = '" . $OM_TIME_YMD . "' ";
        $sql_common = $this->gvcomplex_lib->sql_goods_list($sql_search);
        $sql_order  = " order by index_no desc ";

        $sql = " select * $sql_common $sql_order ";
        $result = $this->query_model->returnArr($sql);

        foreach ($result as $key => $row) {
            $cate1 = $cate2 = $cate3 = '';
            $caid1 = $caid2 = $caid3 = '';

            if (strlen($row['ca_id']) >= 9) {
                $caid3 = substr($row['ca_id'], 0, 9);
                $sql = "select catename from shop_category where catecode = ?";
                $param = array($caid3);
                $row2 = $this->query_model->returnOneArr($sql, $param);
                $cate3 = $row2['catename'];
            }
            if (strlen($row['ca_id']) >= 6) {
                $caid2 = substr($row['ca_id'], 0, 6);
                $sql = "select catename from shop_category where catecode = ?";
                $param = array($caid2);
                $row2 = $this->query_model->returnOneArr($sql, $param);
                $cate2 = $row2['catename'];
            }
            if (strlen($row['ca_id']) >= 3) {
                $caid1 = substr($row['ca_id'], 0, 3);
                $sql = "select catename from shop_category where catecode = ?";
                $param = array($caid1);
                $row2 = $this->query_model->returnOneArr($sql, $param);
                $cate1 = $row2['catename'];
            }

            $point = (int)$row['gpoint'];
            if ($point) {
                $point .= '원';
            }

            // 배송비계산
            $deliv = $this->funcomplex_lib->get_sendcost_amt2($row['index_no'], $row['goods_price']);

            // 상품이미지
            $img_url = $this->funcomplex_lib->get_it_image_url($row['index_no'], $row['simg2'], 400, 400);

            // 상태
            $class = 'U';
            $stock_qty = get_it_stock_qty($row['index_no']);

            if (substr($row['reg_time'], 0, 10) == $OM_TIME_YMD && $row['update_time'] >= $row['reg_time'])
                $class = 'I';

            if ($row['isopen'] > 1 || $stock_qty < 0)
                $class = 'D';

            // 수정시간
            $utime = str_replace(array('-', ' ', ':'), '', $row['update_time']);

            $str = "{$lt}begin{$gt}";
            $str .= "{$lt}mapid{$gt}{$row['index_no']}";
            $str .= "{$lt}price{$gt}{$row['goods_price']}";
            $str .= "{$lt}class{$gt}$class";
            $str .= "{$lt}utime{$gt}$utime";
            $str .= "{$lt}pname{$gt}{$row['gname']}";
            $str .= "{$lt}pgurl{$gt}$shop_url/view?index_no={$row['index_no']}";
            $str .= "{$lt}igurl{$gt}$img_url";
            $str .= "{$lt}cate1{$gt}$cate1";
            $str .= "{$lt}caid1{$gt}$caid1";
            if ($cate2) {
                $str .= "{$lt}cate2{$gt}$cate2";
            }
            if ($caid2) {
                $str .= "{$lt}caid2{$gt}$caid2";
            }
            if ($cate3) {
                $str .= "{$lt}cate3{$gt}$cate3";
            }
            if ($caid3) {
                $str .= "{$lt}caid3{$gt}$caid3";
            }
            if ($row['model']) {
                $str .= "{$lt}model{$gt}{$row['model']}";
            }
            if ($row['brand_nm']) {
                $str .= "{$lt}brand{$gt}{$row['brand_nm']}";
            }
            if ($row['maker']) {
                $str .= "{$lt}maker{$gt}{$row['maker']}";
            }
            $str .= "{$lt}point{$gt}$point";
            $str .= "{$lt}deliv{$gt}$deliv";
            $str .= "{$lt}ftend{$gt}";

            echo iconv('utf-8', 'euc-kr', $str);
        }

        $content = ob_get_contents();
        ob_end_clean();

        echo $content;
    }
}
