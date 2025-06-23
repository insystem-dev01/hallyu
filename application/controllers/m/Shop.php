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
        $this->load->config('shop_extend');
        $this->load->config('goodsinfo');
        $this->load->vars(array('is_seometa' => '', 'pg' => ''));
    }

    #listtype
    public function listtype()
    {
        $default = $this->load->get_var('default');
        $type = $this->input->get('type');
        $type = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\s]/", "", $type);
        $tb = array();
        if ($type == 1)      $tb['title'] = $default['de_pname_1']; // bid
        else if ($type == 2) $tb['title'] = $default['de_pname_2']; // 베스트셀러
        else if ($type == 3) $tb['title'] = $default['de_pname_3']; // 신상품
        else if ($type == 4) $tb['title'] = $default['de_pname_4']; // 인기상품
        else if ($type == 5) $tb['title'] = $default['de_pname_5']; // 추천상품
        else
            alert('상품유형이 아닙니다.', base_url());
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->listtypeSkin();
        $this->template->mb_footer();
    }

    private function listtypeSkin()
    {
        $sort = $this->input->get('sort');
        $sortodr = $this->input->get('sortodr');
        $page = $this->input->get('page');
        $type = $this->input->get('type');

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $gw_msort = $this->config->item('gw_msort');

        $pt_id = $this->load->get_var('pt_id');
        $config = $this->load->get_var('config');

        $sql_search = "";
        // 상품 정렬
        if ($sort && $sortodr)
            $sql_order = " order by a.{$sort} {$sortodr}, a.index_no desc ";
        else
            $sql_order = " order by a.index_no desc ";
        $res = $this->global_lib->query_itemtype($pt_id, $type, $sql_search, $sql_order);
        $total_count = $res->num_rows();

        $mod = 4; // 가로 출력 수
        $rows = ($mod * 6);
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $resultObj = $this->global_lib->query_itemtype($pt_id, $type, $sql_search, $sql_order . " limit $from_record, $rows ");
        $result = $resultObj->result_array();
        $qstr1 = 'type=' . $type . '&sort=' . $sort . '&sortodr=' . $sortodr;
        $qstr2 = 'type=' . $type;

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
        $listtpeList = $this->listtypeList($result);
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?' . $qstr1 . '&page=');
        $data = array(
            'sort_str' => $sort_str,
            'total_count' => $total_count,
            'sort_name' => $sort_name,
            'listtpeList' => $listtpeList,
            'pagination' => $pagination
        );
        $this->load->view('mb/shop/listtype', $data);
    }

    private function listtypeList($result)
    {
        $str = "";
        $i = 0;

        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= "<div class=\"pr_desc_list \">
                            <ul class=\"list\">";

            $it_href = base_url() . 'm/shop/view?index_no=' . $row['index_no'];
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
            // 옵션 있는 상품은 상세 페이지로 이동
            $option_item = $this->funcomplex_lib->get_item_options($index_no, $gs['opt_subject']);
            $supply_item = $this->funcomplex_lib->get_item_supply($index_no, $gs['spl_subject']);

            if ((!empty($option_item) || !empty($supply_item)) && $scrtipt_msg != 'soldout') {
                $onclickTxt = "location.href='" . base_url() . "m/shop/view?index_no={$index_no}'";
            } else {
                $onclickTxt = "fbuyform_submit(this)";
            }
            $status = ($scrtipt_msg == "soldout") ? "disabled" : "";

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
            // $str .=     "<p class=\"ic_bx\">
            //                 <span onclick=\"javascript:itemlistwish('{$row['index_no']}');\" id=\"{$row['index_no']}\" class=\"".($row['index_no'].' '.$this->gvcomplex_lib->zzimCheck($row['index_no']))."\"></span>
            //             </p>";
            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">{$sale}{$it_price}";
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
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    #카테고리 리스트를 클릭시 나오는 리스트
    public function cateList()
    {
        $pt_id = $this->load->get_var('pt_id');
        $ca_id = $this->input->get('ca_id');

        $sql = " select * from shop_category where catecode = ? and cateuse = '0' and find_in_set(?, catehide) = '0' ";
        $data = array($ca_id, $pt_id);
        $ca = $this->query_model->returnOneArr($sql, $data);

        // if(!$ca['catecode'])
        //     alert('등록된 분류가 없습니다.');

        $tb['title'] = $ca['catename'];
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->listSkin("all");
        $this->template->mb_footer();
    }

    #카테고리 리스트를 클릭시 나오는 리스트
    public function normal()
    {
        $pt_id = $this->load->get_var('pt_id');
        $ca_id = $this->input->get('ca_id');

        $sql = " select * from shop_category where catecode = ? and cateuse = '0' and find_in_set(?, catehide) = '0' ";
        $data = array($ca_id, $pt_id);
        $ca = $this->query_model->returnOneArr($sql, $data);

        // if(!$ca['catecode'])
        //     alert('등록된 분류가 없습니다.');

        $tb['title'] = $ca['catename'];
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->listSkin("normal");
        $this->template->mb_footer();
    }

    private function listSkin($type)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');

        $ca_id = $this->input->get('ca_id');
        $sort = $this->input->get('sort');
        $sortodr = $this->input->get('sortodr');
        $page = $this->input->get('page');

        $gw_msort = $this->config->item('gw_msort');
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

        $mod = 2; // 가로 출력 수
        $rows = ($mod * 4);
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $sql = " select * $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $qstr1 = 'ca_id=' . $ca_id . '&sort=' . $sort . '&sortodr=' . $sortodr;
        $qstr2 = 'ca_id=' . $ca_id;

        $sort_str = '';
        $sort_name = "";
        for ($i = 0; $i < count($gw_msort); $i++) {
            list($tsort, $torder, $tname) = $gw_msort[$i];

            $sct_sort_href = $requestUrl . '?' . $qstr2 . '&sort=' . $tsort . '&sortodr=' . $torder;

            if ($sort == $tsort && $sortodr == $torder)
                $sort_name = $tname;
            if ($i == 0 && !($sort && $sortodr))
                $sort_name = $tname;

            $sort_str .= '<li><a href="' . $sct_sort_href . '">' . $tname . '</a></li>';
        }
        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            $is_mobile = true;
        } else {
            $is_mobile = false;
        }
        $mb_treeCate = $this->gvcomplex_lib->tree_category($is_mobile, $ca_id, $type);

        $listTable = $this->listTable($result);
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?' . $qstr1 . '&page=');


        $data = array(
            'mb_treeCate' => $mb_treeCate,
            'total_count' => $total_count,
            'sort_name' => $sort_name,
            'sort_str' => $sort_str,
            'listTable' => $listTable,
            'pagination' => $pagination
        );
        $this->load->view('mb/shop/list', $data);
    }

    private function listTable($result)
    {
        $str = "";
        $i = 0;


        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= "<div class=\"pr_desc_list\">
                            <ul class=\"list\">";

            $it_href = base_url() . 'm/shop/view?index_no=' . $row['index_no'];
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


            if ((!empty($option_item) || !empty($supply_item)) && $scrtipt_msg != 'soldout') {
                $onclickTxt = "location.href='" . base_url() . "m/shop/view?index_no={$index_no}'";
            } else {
                $onclickTxt = "fbuyform_submit(this)";
            }

            $status = ($scrtipt_msg == "soldout") ? "disabled" : "";

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
            // $str .=     "<p class=\"ic_bx\">
            //                 <span onclick=\"javascript:itemlistwish('{$row['index_no']}');\" id=\"{$row['index_no']}\" class=\"" . ($row['index_no'] . ' ' . $this->gvcomplex_lib->zzimCheck($row['index_no'])) . "\"></span>
            //             </p>";
            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">{$sale}{$it_price}";
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
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    #bid
    public function timesale()
    {
        $default = $this->load->get_var('default');
        $tb['title'] = $default['de_pname_8'];
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->timesaleSkin();
        $this->template->mb_footer();
    }

    private function timesaleSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $sort = $this->input->get('sort');
        $sortodr = $this->input->get('sortodr');
        $page = $this->input->get('page');
        $page_rows = $this->input->get('page_rows');
        $status = $this->input->get('status'); // 오픈 상태 (pending or active)
        $ca_id = $this->input->get('ca_id') ?? '';

        $gw_msort = $this->config->item('gw_msort');

        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
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

        // 상품 정렬
        if ($sort && $sortodr)
            $sql_order = " order by {$sort} {$sortodr}, `rank` desc, index_no desc ";
        else
            $sql_order = " order by `rank` desc, index_no desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql = " select count(*) as cnt $sql_common ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $mod = 2; // 가로 출력 수
        $rows = ($mod * 4);
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        if (empty($result)) {
            $timeTable = "<div class=\"empty_list\">등록된 상품이 없습니다.</div>";
        } else {
            $timeTable = $this->timeTable($result);
        }

        $qstr1 = 'page_rows=' . $page_rows . '&sort=' . $sort . '&sortodr=' . $sortodr;
        $qstr2 = 'page_rows=' . $page_rows;

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

        if ((!isset($_SESSION['device']) || $_SESSION['device'] != "pc") && $this->agent->is_mobile()) {
            $is_mobile = true;
        } else {
            $is_mobile = false;
        }
        $mb_treeCate = $this->gvcomplex_lib->tree_category($is_mobile, $ca_id, 'timesale', $status);

        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?' . $qstr1 . '&page=');
        $data = array(
            'total_count' => $total_count,
            'sort_name' => $sort_name,
            'sort_str' => $sort_str,
            'timeTable' => $timeTable,
            'pagination' => $pagination,
            'mb_treeCate' => $mb_treeCate
        );
        $this->load->view('mb/shop/timesale', $data);
    }

    private function timeTable($result)
    {
        $str = "";
        $i = 0;
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

        foreach ($result as $key => $row) {
            $is_social_end = false;
            $is_social_wait = false;
            $is_social_ing = false;

            if ($i == 0) {
                $str .= "<div class=\"pr_desc_horizontal mart30\">
                            <ul>";
            }

            $it_href = base_url() . 'm/shop/view?index_no=' . $row['index_no'];
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
            $scrtipt_msg = "";
            if ($this->funcomplex_lib->is_soldout($index_no)) {
                $scrtipt_msg = 'soldout';
            }

            // 옵션 있는 상품은 상세 페이지로 이동
            $option_item = $this->funcomplex_lib->get_item_options($index_no, $gs['opt_subject']);
            $supply_item = $this->funcomplex_lib->get_item_supply($index_no, $gs['spl_subject']);

            if ((!empty($option_item) || !empty($supply_item)) && $scrtipt_msg != 'soldout') {
                $onclickTxt = "location.href='" . base_url() . "m/shop/view?index_no={$index_no}'";
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

            $str .= "<li>\n";
            $str .=     "<div class=\"timesale_image\">";
            $str .=         "<a href=\"{$it_href}\">\n";
            $str .=             "<dl>\n";
            if (!empty($row['simg1'])) {
                $imgSrc = ($row['simg_type'] == 0) ? base_url() . "data/goods/{$row['simg1']}" : $row['simg1'];
            } else {
                $imgSrc = base_url() . "img/noimage.gif";
            }
            $str .= "<dt><img src=\"{$imgSrc}\"></dt>\n";
            $str .=             "</dl>\n";
            $str .=         "</a>\n";
            $str .=         $is_social_tag;
            $str .=     "</div>";
            // $str .=     "<p class=\"ic_bx\">
            //                 <span onclick=\"javascript:itemlistwish('{$row['index_no']}');\" id=\"{$row['index_no']}\" class=\"".($row['index_no'].' '.$this->gvcomplex_lib->zzimCheck($row['index_no']))."\"></span>
            //             </p>";
            $str .=     "<div class=\"timesale_content\">";
            $openDate = substr($row['sb_date'], 2, 2) . '/' . substr($row['sb_date'], 5, 2) . '/' . substr($row['sb_date'], 8, 2);
            $openTime = (new DateTime($row['eb_date']))->format('H:i');
            $endDate = substr($row['eb_date'], 2, 2) . '/' . substr($row['eb_date'], 5, 2) . '/' . substr($row['eb_date'], 8, 2);
            $endTime = (new DateTime($row['eb_date']))->format('H:i');
            $str .=         "<p class=\"pname\"><a href=\"{$it_href}\">{$it_name}</a></p>\n";
            $str .=         "<dd class=\"price_box\">\n";
            $str .=             "<p class=\"price\">{$it_sprice}</p>";
            $str .=             "<div class=\"price\">{$sale}{$it_price}";
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
            $str .=         "</dd>\n";
            $str .=         "<div class=\"timebox\">
                                <div class=\"calendar\"></div>
                                <span class=\"date\">{$openDate}</span>
                                <span class=\"time\">{$openTime}</span>
                                <span class=\"dash\">~</span>
                                <span class=\"date\">{$endDate}</span>
                                <span class=\"time\">{$endTime}</span>
                            </div>\n";
            $str .=     "</div>";
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
        $default = $this->load->get_var('default');
        $tb['title'] = $default['de_pname_7'];
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->planSkin();
        $this->template->mb_footer();
    }

    private function planSkin()
    {
        $planTable = "";
        $pt_id = $this->load->get_var('pt_id');
        $sql = "select * from shop_goods_plan where mb_id IN('admin',?) and pl_use = '1' ";
        $param = array($pt_id);
        $res = $this->query_model->returnArr($sql, $param);
        foreach ($res as $key => $row) {
            $href = base_url() . 'm/shop/planlist?pl_no=' . $row['pl_no'];
            $bimg = './data/plan/' . $row['pl_limg'];
            if (is_file($bimg) && $row['pl_limg']) {
                $bimg = base_url() . 'data/plan/' . $row['pl_limg'];
                $pl_limgurl = rpc($bimg);
            } else {
                $pl_limgurl = base_url() . 'img/plan_noimg.gif';
            }
            $planTable .= "
            <li>
                <a href=\"{$href}\">
                <p class=\"plan_img\"><img src=\"{$pl_limgurl}\"></p>
                <p class=\"plan_tit\">{$row['pl_name']}</p>
                </a>
            </li>";
        }
        $data = array("planTable" => $planTable);
        $this->load->view('mb/shop/plan', $data);
    }

    #기획전 planlist
    public function planlist()
    {
        $pl_no = $this->input->get('pl_no');
        $pt_id = $this->load->get_var('pt_id');
        $sql_where = " where pl_no = '$pl_no' ";
        if ($pt_id != 'admin') {
            $sql_where .= " and mb_id IN('admin','$pt_id') ";
        }
        $pl = $this->query_model->returnOneArr("select * from shop_goods_plan {$sql_where} ");
        if (!$pl['pl_no'])
            alert('자료가 없습니다.');
        $tb['title'] = $pl['pl_name'];
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->planlistSkin($pl);
        $this->template->mb_footer();
    }

    private function planlistSkin($pl)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $gw_msort = $this->config->item('gw_msort');

        $sort = $this->input->get('sort');
        $sortodr = $this->input->get('sortodr');
        $page = $this->input->get('page');
        $pl_no = $this->input->get('pl_no');

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

        $mod = 2; // 가로 출력 수
        $rows = ($mod * 9);
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함   

        $sql = " select * $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $planlistTable = $this->planlistTable($total_count, $result);
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
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?' . $qstr1 . '&page=');
        $data = array(
            "sort_str" => $sort_str,
            'total_count' => $total_count,
            'sort_name' => $sort_name,
            'bming_urlTag' => $bming_urlTag,
            'planlistTable' => $planlistTable,
            'pagination' => $pagination
        );
        $this->load->view('mb/shop/planlist', $data);
    }

    private function planlistTable($total_count, $result)
    {
        $str = "";
        $i = 0;

        foreach ($result as $key => $row) {
            if ($i == 0)
                $str .= "<div class=\"pr_desc_list\">
                            <ul class=\"list\">";

            $it_href = base_url() . 'm/shop/view?index_no=' . $row['index_no'];
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


            // 옵션 있는 상품은 상세 페이지로 이동
            $option_item = $this->funcomplex_lib->get_item_options($index_no, $gs['opt_subject']);
            $supply_item = $this->funcomplex_lib->get_item_supply($index_no, $gs['spl_subject']);

            if ((!empty($option_item) || !empty($supply_item)) && $scrtipt_msg != 'soldout') {
                $onclickTxt = "location.href='" . base_url() . "m/shop/view?index_no={$index_no}'";
            } else {
                $onclickTxt = "fbuyform_submit(this)";
            }
            $status = ($scrtipt_msg == "soldout") ? "disabled" : "";

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
            $str .=     "<p class=\"ic_bx\">
                            <span onclick=\"javascript:itemlistwish('{$row['index_no']}');\" id=\"{$row['index_no']}\" class=\"" . ($row['index_no'] . ' ' . $this->gvcomplex_lib->zzimCheck($row['index_no'])) . "\"></span>
                        </p>";
            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">{$sale}{$it_price}";
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
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    #브랜드 샵
    public function brand()
    {
        $default = $this->load->get_var('default');
        $tb['title'] = $default['de_pname_6'];
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->brandSkin();
        $this->template->mb_footer();
    }

    private function brandSkin()
    {
        $OM_ALPHALOWER = 2;
        $OM_ALPHAUPPER = 1;
        $OM_HANGUL = 16;

        $pt_id = $this->load->get_var('pt_id');
        $default = $this->load->get_var('default');

        $qstx = $this->input->get('qstx');
        $qword = $this->input->get('qword');
        $qsort = $this->input->get('qsort');
        $qorder = $this->input->get('qorder');

        $where = array();
        $sql_where = "";

        $qstx = utf8_strcut(get_search_string(trim($qstx)), 30, "");
        $qword = trim($qword);

        if (isset($qsort)) {
            $qsort = trim($qsort);
            $qsort = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\s]/", "", $qsort);
        } else {
            $qsort = '';
        }
        if (isset($qorder)) {
            $qorder = preg_match("/^(asc|desc)$/i", $qorder) ? $qorder : '';
        } else {
            $qorder = '';
        }
        $qsort  = strtolower($qsort);
        $qorder = strtolower($qorder);

        if (!$qsort) {
            $qsort = "br_name";
            $qorder = "asc";
        }

        $where[] = " ( br_user_yes = '0' or (br_user_yes = '1' and mb_id = '$pt_id') ) and {$qsort} <> '' ";

        if ($qstx) {
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
        else if (check_string($qword, $OM_ALPHALOWER) && $qword)
            $where[] = " {$qsort} REGEXP '^[0-9]' ";

        if ($where) {
            $sql_where .= " where " . implode(" and ", $where);
        }
        $sql_common = " from shop_brand {$sql_where} ";

        // 테이블의 전체 레코드수만 얻음
        $sql = " select count(*) as cnt {$sql_common} ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];
        $sql = " select * {$sql_common} order by {$qsort} {$qorder} ";
        $result = $this->query_model->returnArr($sql);
        $i = 0;
        $list = array();
        foreach ($result as $key => $row) {
            $list[$i] = $row;
            $list[$i]['br_name'] = ($qsort == 'br_name') ? $row['br_name'] : $row['br_name_eng'];
            $list[$i]['br_href'] = base_url() . 'm/shop/brandlist?br_id=' . $row['br_id'];

            $bimg = './data/brand/' . $row['br_logo'];
            if (is_file($bimg) && $row['br_logo']) {
                $bimg = base_url() . 'data/brand/' . $row['br_logo'];
                $list[$i]['br_logo'] = rpc($bimg);
            } else
                $list[$i]['br_logo'] = base_url() . 'img/brlogo_sam.jpg';
            $i++;
        }
        if ($qsort == 'br_name')
            $charAt = explode('|', 'ㄱ|ㄴ|ㄷ|ㄹ|ㅁ|ㅂ|ㅅ|ㅇ|ㅈ|ㅊ|ㅋ|ㅌ|ㅍ|ㅎ');
        else
            $charAt = explode('|', 'A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z');

        $br_nameClass = "";
        if ($qsort == 'br_name') {
            $br_nameClass = ' class="active"';
        }
        $br_name_engClass = "";
        if ($qsort == 'br_name_eng') {
            $br_name_engClass = ' class="active"';
        }
        $noSelect = "";
        if ($qword == '') {
            $noSelect = ' active';
        }
        $wordSeg = "";
        foreach ($charAt as $word) {
            $word_active = '';
            if ($qword == $word)
                $word_active = ' class="active"';
            $wordSeg .= "<li{$word_active} onclick=\"set_word('{$word}');\">{$word}</li>\n";
        }
        $etcClass = "";
        if ($qword == 'etc') {
            $etcClass = ' active';
        }
        $brand_str = "<div class='br_list'>\n";
        $brand_str .= "<ul>\n";

        for ($i = 0; $i < count($list); $i++) {
            $brand_str .= "<li>\n<a href='" . $list[$i]['br_href'] . "'>\n";
            if ($qsort != 'br_name_eng') {
                $brand_str .= "<img src='" . $list[$i]['br_logo'] . "' title='" . $list[$i]['br_name'] . "'>\n";
                $brand_str .= "<p class='mart8'>" . $list[$i]['br_name'] . "</p>\n";
            } else {
                $brand_str .= "<img src='" . $list[$i]['br_logo'] . "' title='" . $list[$i]['br_name_eng'] . "'>\n";
                $brand_str .= "<p class='mart8'>" . $list[$i]['br_name_eng'] . "</p>\n";
            }

            $brand_str .= "</a>\n</li>\n";
        }
        $brand_str .= "</ul>\n</div>\n";
        $data = array(
            'qsort' => $qsort,
            'qorder' => $qorder,
            'qword' => $qword,
            'default' => $default,
            'qstx' => $qstx,
            'br_nameClass' => $br_nameClass,
            'br_name_engClass' => $br_name_engClass,
            'noSelect' => $noSelect,
            'wordSeg' => $wordSeg,
            'etcClass' => $etcClass,
            'brand_str' => $brand_str
        );
        $this->load->view('mb/shop/brand', $data);
    }

    public function brandlist()
    {
        $default = $this->load->get_var('default');
        $tb['title'] = $default['de_pname_6'];
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->brandlistSkin();
        $this->template->mb_footer();
    }

    private function brandlistSkin()
    {
        $br_id = $this->input->get('br_id');
        $default = $this->load->get_var('default');
        $config = $this->load->get_var('config');
        $sort = $this->input->get('sort');
        $sortodr = $this->input->get('sortodr');
        $page = $this->input->get('page');
        $qstx = $this->input->get('qstx');
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $br = array();
        if ($br_id) {
            $sql = "select * from shop_brand where br_id = ?";
            $param = array($br_id);
            $br = $this->query_model->returnOneArr($sql, $param);
            $sql_search = " and brand_uid = '$br_id' ";
        } else {
            $sql_search = " and brand_uid <> '' ";
            $br['br_name'] = $default['de_pname_6'];
        }
        $bimg = './data/brand/' . $br['br_logo'];
        $br_logo = "";
        if (is_file($bimg) && $br['br_logo']) {
            $br_logo = rpc($bimg, '', '');
            $br_logo = base_url() . $br_logo;
        } else {
            $br_logo = base_url() . 'img/brlogo_sam.jpg';
        }

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

        $mod = 2; // 가로 출력 수
        $rows = ($mod * 9);
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $qstr1 = 'br_id=' . $br_id . '&sort=' . $sort . '&sortodr=' . $sortodr;
        $qstr2 = 'br_id=' . $br_id;
        $gw_msort = $this->config->item('gw_msort');

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

        $tableTag = "";
        $i = 0;

        foreach ($result as $key => $row) {
            if ($i == 0)
                $tableTag .= "<div class=\"pr_desc_list\">
                            <ul class=\"list\">";

            $it_href = base_url() . 'm/shop/view?index_no=' . $row['index_no'];
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
            // 옵션 있는 상품은 상세 페이지로 이동
            $option_item = $this->funcomplex_lib->get_item_options($index_no, $gs['opt_subject']);
            $supply_item = $this->funcomplex_lib->get_item_supply($index_no, $gs['spl_subject']);

            if ((!empty($option_item) || !empty($supply_item)) && $scrtipt_msg != 'soldout') {
                $onclickTxt = "location.href='" . base_url() . "m/shop/view?index_no={$index_no}'";
            } else {
                $onclickTxt = "fbuyform_submit(this)";
            }

            $status = ($scrtipt_msg == "soldout") ? "disabled" : "";

            $tableTag .= "<li class=\"goodsList\">\n";
            $tableTag .=     "<a href=\"{$it_href}\">\n";
            $tableTag .=         "<dl>\n";
            if (!empty($row['simg1'])) {
                $imgSrc = ($row['simg_type'] == 0) ? base_url() . "data/goods/{$row['simg1']}" : $row['simg1'];
            } else {
                $imgSrc = base_url() . "img/noimage.gif";
            }
            $tableTag .= "<dt><img src=\"{$imgSrc}\"></dt>\n";
            $tableTag .=         "</dl>\n";
            $tableTag .=     "</a>\n";
            $tableTag .=     "</dd>";
            $tableTag .=     "<p class=\"pname\">{$it_name}</p>\n";
            $tableTag .=     "<dd class=\"price_box\">\n";
            $tableTag .=         "<p class=\"price\">{$it_sprice}</p>";
            $tableTag .=         "<div class=\"price\">{$sale}{$it_price}";
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
            $tableTag .=     "</dd>\n";

            $tableTag .= "</li>\n";
            $i++;
        }

        if ($i > 0)
            $tableTag .= "</ul>\n</div>\n";

        $tableTag .= get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?' . $qstr1 . '&page=');

        $data = array(
            'br' => $br,
            'br_logo' => $br_logo,
            'total_count' => $total_count,
            'sort_name' => $sort_name,
            'sort_str' => $sort_str,
            'tableTag' => $tableTag,
            'br_id' => $br_id,
            'qstx' => $qstx
        );

        $this->load->view('mb/shop/brandlist', $data);
    }

    #검색화면
    public function search()
    {
        $ss_tx = $this->input->get('ss_tx');
        $ss_tx = trim(strip_tags($ss_tx));
        if (!$ss_tx) {
            alert('검색어가 넘어오지 않았습니다.');
        }
        $tb['title'] = "상품 검색 결과";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->searchSkin();
        $this->template->mb_footer();
    }

    private function searchSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $gw_msort = $this->config->item('gw_msort');
        $page = $this->input->get('page');
        $hash_token = $this->input->get('hash_token');

        $ss_tx = $this->input->get('ss_tx');
        $ss_tx = trim(strip_tags($ss_tx));
        $ss_tx = addslashes($ss_tx);
        $ss_tx = $this->db->escape_str($ss_tx);
        $sort = $this->input->get('sort');
        if (empty($sort)) {
            $sort = "readcount";
        }

        $excludeSoldOut = $this->input->get('excludeSoldOut'); // 판매종료 상품 제외
        $onlyBid = $this->input->get('onlyBid'); // bid 상품만 보기

        $sortodr = $this->input->get('sortodr');
        if (empty($sortodr)) {
            $sortodr = "desc";
        }

        $pt_id = $this->load->get_var('pt_id');
        $config = $this->load->get_var('config');

        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $OM_HASH_TOKEN = md5(base_url() . $OM_TIME_YMD . $_SERVER['REMOTE_ADDR']);

        $concat = array();
        $concat[] = "a.gname";
        $concat[] = "a.explan";
        $concat[] = "a.gcode";
        $concat_fields = "concat(" . implode(",' ',", $concat) . ")";

        // 인기검색어
        if (isset($hash_token) && $OM_HASH_TOKEN == $hash_token) {
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

        $mod = 2; // 가로 출력 수
        $rows = ($mod * 9);
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select a.* $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $searchTb = $this->searchTable($result);
        $qstr1 = 'ss_tx=' . $ss_tx . '&sort=' . $sort . '&sortodr=' . $sortodr;
        $qstr2 = 'ss_tx=' . $ss_tx;

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
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?' . $qstr1 . '&page=');
        $data = array(
            'sort_str' => $sort_str,
            'total_count' => $total_count,
            'sort_name' => $sort_name,
            'searchTb' => $searchTb,
            'pagination' => $pagination,
            'excludeSoldOut' => $excludeSoldOut,
            'onlyBid' => $onlyBid
        );
        $this->load->view('mb/shop/search', $data);
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
                $str .= "<div class=\"pr_desc_list\">
                            <ul class=\"list\">";

            $it_href = base_url() . 'm/shop/view?index_no=' . $row['index_no'];
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
                $onclickTxt = "location.href='" . base_url() . "m/shop/view?index_no={$index_no}'";
            } else {
                $onclickTxt = "fbuyform_submit(this)";
            }
            // bid 상품 시간
            $is_open = "open";
            if ($gs['type'] == "timesale" && (substr($gs['sb_date'], 0, 1) != '0' && substr($gs['eb_date'], 0, 1) != '0')) {
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
            $str .=         $is_social_tag;
            $str .=     "</dd>";
            // $str .=     "<p class=\"ic_bx\">
            //                 <span onclick=\"javascript:itemlistwish('{$row['index_no']}');\" id=\"{$row['index_no']}\" class=\"".($row['index_no'].' '.$this->gvcomplex_lib->zzimCheck($row['index_no']))."\"></span>
            //             </p>";
            $str .=     "<p class=\"pname\">{$it_name}</p>\n";
            $str .=     "<dd class=\"price_box\">\n";
            $str .=         "<p class=\"price\">{$it_sprice}</p>";
            $str .=         "<div class=\"price\">{$sale}{$it_price}";
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
            $str .= "</ul>\n</div>\n";

        return $str;
    }

    #상품보기 페이지 view
    public function view()
    {
        $pt_id = $this->load->get_var('pt_id');
        $index_no = $this->input->get('index_no');
        $this->load->vars(array('is_seometa' => 'it'));
        $pg['pagename'] = '상품 상세보기';
        $sql = " select a.*, b.cateuse  from shop_goods a, shop_category b  where a.index_no = ? and a.ca_id = b.catecode
                and find_in_set(?, a.use_hide) = '0'
                and find_in_set(?, b.catehide) = '0' ";
        $param = array($index_no, $pt_id, $pt_id);
        $gs = $this->query_model->returnOneArr($sql, $param);
        $sr = $this->global_lib->get_seller_cd($gs['mb_id']);
        $this->load->vars(array('pg' => $pg, 'gs' => $gs, 'sr' => $sr));
        $this->template->mb_header();
        $this->viewSkin();
        $this->template->mb_footer();
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
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);

        $index_no = $this->input->get('index_no');
        $referralCode = $this->input->get('referralCode'); // 리뷰공유코드
        // 상품의 정보를 얻음
        $default = $this->load->get_var('default');
        $member = $this->load->get_var('member');
        $is_member = $this->load->get_var('is_member');
        $pt_id = $this->load->get_var('pt_id');
        $config = $this->load->get_var('config');

        $sql = " select a.*, b.cateuse  from shop_goods a, shop_category b  where a.index_no = ? and a.ca_id = b.catecode
                and find_in_set(?, a.use_hide) = '0'
                and find_in_set(?, b.catehide) = '0' ";
        $param = array($index_no, $pt_id, $pt_id);
        $gs = $this->query_model->returnOneArr($sql, $param);
        $type = $gs['type'];

        if (!$gs['index_no'])
            alert('등록된 상품이 없습니다');
        if ($gs['shop_state'] || $gs['cateuse']) {
            if (!$this->gvcomplex_lib->is_seller($member['id'])) {
                if (!$this->gvcomplex_lib->is_admin())
                    alert('현재 판매가능한 상품이 아닙니다.');
            }
        }
        // 오늘 본 상품 저장 시작
        if (get_session('ss_pr_idx')) {
            $arr_ss_pr_idx = get_session('ss_pr_idx');
            $arr_tmps = explode(",", $arr_ss_pr_idx);
            if (!in_array($index_no, $arr_tmps)) {
                $ss_pr_idx = get_session('ss_pr_idx') . "," . $index_no;
                set_session('ss_pr_idx', $ss_pr_idx);
            }
        } else {
            $ss_pr_idx = get_session('ss_pr_idx') . $index_no;
            set_session('ss_pr_idx', $ss_pr_idx);
        }
        // 공급업체 정보
        $sr = $this->global_lib->get_seller_cd($gs['mb_id']);
        if ($gs['use_aff']) {
            $sr = $this->partner_lib->get_partner($gs['mb_id']);
        }
        $gpoint = 0;

        // 상품문의 건수구하기
        $sql = "select count(*) as cnt from shop_goods_qa where gs_id = ?";
        $param = array($index_no);
        $row = $this->query_model->returnOneArr($sql, $param);
        $itemqa_count = (int)$row['cnt'];

        // 고객선호도 별점수
        $star_score = $this->funcomplex_lib->get_star_image($index_no);

        // 고객선호도 평점
        $aver_score = ($star_score * 10) * 2;

        // 상품조회 카운터하기
        $sql = "update shop_goods set readcount = readcount + 1 where index_no=?";
        $param = array($index_no);
        $this->query_model->returnNull($sql, $param);

        // 수량체크
        if (!$gs['stock_mod']) {
            $gs['stock_qty'] = 999999999;
        }

        if ($gs['odr_min']) { // 최소구매수량
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
        $M_TIMESALE = "";

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
            } else if ($gs['sb_date'] <= $OM_TIME_YMDHIS && $gs['eb_date'] >= $OM_TIME_YMDHIS) {
            }
        }

        // 필수 옵션
        $option_item = $this->funcomplex_lib->mobile_item_options($index_no, $gs['opt_subject'], " style='width:100%'");

        // 추가 옵션
        $supply_item = $this->funcomplex_lib->mobile_item_supply($index_no, $gs['spl_subject'], " style='width:100%'");
        $cp_used = "";
        // 가맹점상품은 쿠폰발급안함
        if (!$gs['use_aff'] && $config['coupon_yes']) {
            // 쿠폰발급 (회원직접 다운로드)
            $cp_used = $this->funcomplex_lib->is_used_coupon('0', $index_no, $member['id']);

            if ($is_member)
                $cp_btn = "<a href=\"javascript:window.open('" . base_url() . "m/shop/pop_coupon?gs_id=$index_no','_blank');\" class=\"\">쿠폰다운로드</a>";
            else
                $cp_btn = "<a href=\"javascript:alert('로그인 후 이용 가능합니다.')\" class=\"\">쿠폰다운로드</a>";
        }

        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        $slide_img = array();
        for ($i = 2; $i <= 6; $i++) { // 슬라이드 이미지
            $it_image = trim($gs['simg' . $i]);
            if (!$it_image) continue;

            if (preg_match("/^(http[s]?:\/\/)/", $it_image) == false) {
                $file = "./data/goods/" . $it_image;
                if (is_file($file)) {
                    $file = base_url() . "data/goods/" . $it_image;
                    $slide_img[] = rpc($file);
                }
            } else {
                $slide_img[] = $it_image;
            }
        }

        $slide_url = implode('|', $slide_img);
        $slide_cnt = count($slide_img);
        if ($type == "normal") {
            $getSalePrice = $this->gvcomplex_lib->get_sale_price($index_no);
        } else {
            $getSalePrice = $this->gvcomplex_lib->get_timesale_goods_price($index_no);
        }

        $v_horizImg = $this->funcomplex_lib->get_it_image($index_no, $gs['simg2'], "200", "200", 'name="slideshow"');
        $gsName = $this->funcomplex_lib->get_text($gs['gname']);
        $gsExplain = "";
        if ($gs['explan']) {
            $gsExplain = "<p class=\"sub_txt\">" . $this->funcomplex_lib->get_text($gs['explan']) . "</p>";
        }

        $no_is_onlyTag = "";
        if (!$is_only) {
            $no_is_onlyTag .= "
            <div class=\"price\">";
            if (!$is_pr_msg && !$is_buy_only && $gs['normal_price']) {
                $it_amount = $this->gvcomplex_lib->get_sale_price($gs['index_no']);
                $is_uncase = $this->funcomplex_lib->is_uncase($gs['index_no']);
                // if ($gs['normal_price'] > $it_amount) {
                //     $sett = ($gs['normal_price'] - $it_amount) / $gs['normal_price'] * 100;
                //     $sale = '<li class="sale">' . number_format($sett, 0) . '<span class="percent">%</span></li>';
                // }

                $sett = ($gs['normal_price'] - $it_amount) / $gs['normal_price'] * 100;
                $sale = '<li class="sale">' . number_format($sett, 0) . '<span class="percent">%</span></li>';


                $no_is_onlyTag .= "
					<ul class=\"sale_box\">
						{$sale}
						<li class='trst'>" . display_price2($gs['normal_price']) . "</li>
					</ul>";
            }

            $priceText = ($gs['type'] == "timesale") ? "판매가(온라인최저가)" : "판매가";
            $sale_text = "";
            if ($gs['type'] == "timesale") {
                $sale_text = "<span style=\"font-size: 3.33vw;\">(" . number_format($sett, 0) . "%)</span>";
            }

            $no_is_onlyTag .= "
                <ul class=\"price_box\">
                    <li class='tlst'>{$priceText}</li>
                    <li class='trst'>
                        <div class='trst-amt'><span class=\"mpr\">" . number_format($getSalePrice) . "<span>원</span></span> {$sale_text}</div>
                    </li>";

            if ($gs['type'] == "timesale") {
                $no_is_onlyTag .= $this->funcomplex_lib->get_mb_timesaleTag($gs, $is_open, "normal", $this->funcomplex_lib->is_soldout($gs['index_no']));
            }
            $no_is_onlyTag .= "
                </ul>";

            if ($gs['type'] == "timesale") {
                $no_is_onlyTag .= "<p class=\"caution\" style=\"text-align: left; margin-top: 1.39;\">입찰 종료 후 실제 판매가가 표시 됩니다.</p>";
            }

            $no_is_onlyTag .= "
            </div>";
        }

        // 포인트 적용에 따른 출력형태
        if (!empty($is_member) && $gs['is_point'] > 0) {
            $grade = $member['grade'];

            $sql = "SELECT gb_buy_point FROM shop_member_grade WHERE gb_no = ?";
            $param = array($grade);
            $result = $this->query_model->returnOneArr($sql, $param);

            $gb_point = $result['gb_buy_point']; // 적립률
            $point = (int) round($getSalePrice * ($gb_point * (1 / 100))); // 적립 포인트

            $rate = number_format($gb_point, 1);
            $gpoint = display_point($point) . " <span class='rate'>($rate%)</span>";
        } else if (empty($is_member) && $gs['is_point'] > 0) {
            $gpoint = "로그인 후 확인 가능";
        } else {
            $gpoint = "적립 제외 품목";
        }

        $gPointTag = "";
        if (!$is_only && !$is_pr_msg && !$is_buy_only && !$is_soldout && $gpoint) {
            $pointText = "";
            if ($gs['type'] == "timesale") {
                $pointText = "<span class=\"fc_red\">(최종 구매가격에 따라 적립금액 상이)</span>";
            }
            $gPointTag = "
            <div class=\"sp_tbox\">
                <ul>
                    <li class='tlst'>포인트</li>
                    <li class='trst'>{$gpoint}</li>
                    <li class='trst'>{$pointText}</li>
                </ul>
            </div>";
        }
        $cp_btnTag = "";
        if (!$is_only && !$is_pr_msg && !$is_buy_only && !$is_soldout && $cp_used) {
            $cp_btnTag = "
            <div class=\"sp_tbox\">
                <ul>
                    <li class='tlst'>쿠폰</li>
                    <li class='trst-cp'>{$cp_btn}</li>
                </ul>
            </div>";
        }
        $gsMarkerTag = "";
        if ($gs['maker']) {
            $gsMarkerTag = "
            <div class=\"sp_tbox\">
                <ul>
                    <li class='tlst'>제조사</li>
                    <li class='trst'>{$gs['maker']}</li>
                </ul>
            </div>";
        }
        $gsOriginTag = "";
        if ($gs['origin']) {
            $gsOriginTag = "
            <div class=\"sp_tbox\">
                <ul>
                    <li class='tlst'>원산지</li>
                    <li class='trst'>{$gs['origin']}</li>
                </ul>
            </div>";
        }
        $gsBrandNmTag = "";
        if ($gs['brand_nm']) {
            $gsBrandNmTag = "
            <div class=\"sp_tbox\">
                <ul>
                    <li class='tlst'>브랜드</li>
                    <li class='trst'>{$gs['brand_nm']}</li>
                </ul>
            </div>";
        }
        $gsModelTag = "";
        if ($gs['model']) {
            $gsModelTag = "
            <div class=\"sp_tbox\">
                <ul>
                    <li class='tlst'>모델명</li>
                    <li class='trst'>{$gs['model']}</li>
                </ul>
            </div>";
        }
        $gsOdrMin = "";
        if ($type == "normal" && $gs['odr_min']) {
            $gsOdrMin = "
            <div class=\"sp_tbox\">
                <ul>
                    <li class='tlst'>최소구매수량</li>
                    <li class='trst'>" . display_qty($gs['odr_min']) . "</li>
                </ul>
            </div>";
        }
        $gsOdrMax = "";
        if ($type == "normal" && $gs['odr_max']) {
            $gsOdrMax = "
            <div class=\"sp_tbox\">
                <ul>
                    <li class='tlst'>최대구매수량</li>
                    <li class='trst'>" . display_qty($gs['odr_max']) . "</li>
                </ul>
            </div>";
        }
        $gsOdrStock = "";
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
            $gsOdrStock = "
            <div class=\"sp_tbox\">
                <ul>
                    <li class='tlst'>상품수량</li>
                    <li class='trst'>" . display_qty($stock_qty) . "</li>
                </ul>
            </div>";

            if (!empty($tableOne)) {
                $gsOdrStock .= "<div class=\"stock_box\">";
                $gsOdrStock .= $tableOne;
                $gsOdrStock .= "</div>";
            }
        }
        $gsOdrSales = "";
        if ($type == "timesale") {
            $order_qty = $this->global_lib->get_order_cnt($gs['index_no']);
            $order_qty = ($gs['original_stock_qty'] < $order_qty) ? $gs['original_stock_qty'] : $order_qty;
            $gsOdrSales = "
			<div class=\"sp_tbox\">
				<ul>
					<li class='tlst'>판매수량</li>
					<li class='trst'>" . display_qty($order_qty) . "</li>
				</ul>
			</div>";
        }
        $gsOdrPeriod = "";
        if ($type == "timesale") {
            $gsOdrPeriod = "
            <div class=\"sp_tbox\">
                <ul>
                    <li class='tlst'>판매기간</li>
                    <li class='trst'>" . $gs['sb_date'] . " ~ " . $gs['eb_date'] . "</li>
                </ul>
            </div>";
        }
        $sc_class = "sp_tbox";
        if (in_array($gs['sc_type'], array('2', '3')) && $gs['sc_method'] == '2') {
            $sc_class = "sp_obox";
        }
        $msa = $this->funcomplex_lib->mobile_sendcost_amt();

        $orderOpt = "";
        if (!$is_only && !$is_pr_msg && !$is_buy_only && !$is_soldout) {
            if ($option_item) {
                $orderOpt .= "
                <div class=\"sp_tbox\">
                    <ul>
                        <li class='tlst'>주문옵션</li>
                        <li class='trst'>아래옵션은 필수선택 옵션입니다</li>
                    </ul>
                </div>
                {$option_item}";
            }
            if ($supply_item) {
                $orderOpt .= "
                <div class=\"sp_tbox\">
                    <ul>
                        <li class='tlst strong'>추가구성</li>
                        <li class='trst fs11 padt2'>추가구매를 원하시면 선택하세요</li>
                    </ul>
                </div>
                {$supply_item}";
            }
            #<!-- 선택된 옵션 시작 { -->
            $orderOpt .= "
                <div id=\"option_set_list\">";
            if (!$option_item) {
                $orderOpt .= "
                    <ul id=\"option_set_added\" class=\"" . $gs['type'] . "\">
                        <li class=\"sit_opt_list\">
                            <div class=\"sp_tbox\">
                                <input type=\"hidden\" name=\"io_type[{$index_no}][]\" value=\"0\">
                                <input type=\"hidden\" name=\"io_id[{$index_no}][]\" value=\"\">
                                <input type=\"hidden\" name=\"io_value[{$index_no}][]\" value=\"{$gs['gname']}\">
                                <input type=\"hidden\" class=\"io_price\" value=\"0\">
                                <input type=\"hidden\" class=\"io_stock\" value=\"{$gs['stock_qty']}\">
                                <ul>
                                    <li class='tlst'>
                                        <span class=\"sit_opt_subj\">수량</span>
                                        <span class=\"sit_opt_prc\"></span>
                                    </li>
                                    <li class='trst'>
                                        <dl>
                                            <dt class='fl'><button type=\"button\" class=\"btn_stock\">-</button></dt>
                                            <dt class='fl'><input type=\"text\" name=\"ct_qty[{$index_no}][]\"
                                            value=\"{$odr_min}\" title=\"수량설정\"></dt>
                                            <dt class='fl'><button type=\"button\" class=\"btn_stock\">+</button><dt>
                                            <!-- <dt class='fl'> (남은수량 : " . ($gs['stock_mod'] ? $gs['stock_qty'] . '개' : '무제한') . ")</dt> -->
                                        </dl>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                    <script>
                    $(function() {
                        price_calculate();
                    });
                    </script>";
            }
            $orderOpt .= "
                </div>
                <!-- } 선택된 옵션 끝 -->
        
                <!-- 총 구매액 -->
                <div id=\"sit_tot_views\" class=\"dn\">
                    <div class=\"sp_tot\">
                        <ul>
                            <li class='tlst strong'>총 합계금액</li>
                            <li class='trst'><span id=\"sit_tot_price\" class=\"trss-amt\"></span><span class=\"trss-amt\">원</span></li>
                        </ul>
                    </div>
                </div>";
            if ($gs['type'] == "timesale") {
                $orderOpt .= "<p class=\"caution\">※ ‘입찰 완료’ 완료 이후 취소는</p>";
                $orderOpt .= "<p class=\"caution\">입찰수수료 10%를 제외한 금액이 환급됩니다.</p>";
                $orderOpt .= "<p class=\"caution\">※ ‘입찰 완료’ 전 취소는 취소수수료 없이 전액 환급됩니다.</p>";
            }
        }
        $naverpay_button_js = $this->load->get_var('naverpay_button_js');
        $naverpay_request_js = $this->load->get_var('naverpay_request_js');
        $naverPayTag = "";
        if (!$is_pr_msg) {
            $naverPayTag = "
            <div class=\"sp_vbox tac\">" . $this->gvcomplex_lib->mobile_buy_button($script_msg, $index_no, $is_open, $is_soldout);
            if ($naverpay_button_js) {
                $naverPayTag .= "<div class=\"naverpay-item\">{$naverpay_request_js} {$naverpay_button_js}</div>";
            }
            $naverPayTag .= "
            </div>";
        }

        $sql = "select b.* from shop_goods_relation a join shop_goods b ON (a.gs_id2=b.index_no) where a.gs_id = ?";
        $param = array($index_no);
        $resObj = $this->query_model->returnQueryObj($sql, $param);
        $rel_count = $resObj->num_rows();
        $res = $resObj->result_array();
        $relatedgs = "";
        if ($rel_count > 0) {
            $relatedgs .= "
            <div class=\"sp_rel\">
                <h3><span>현재상품과 연관된 상품</span></h3>
                <div>";
            $i = 0;
            foreach ($res as $key => $row) {
                $it_href = base_url() . 'm/shop/view?index_no=' . $row['index_no'];
                $it_name = cut_str($row['gname'], 50);
                $it_imageurl = $this->funcomplex_lib->get_it_image_url($row['index_no'], $row['simg2'], 400, 400);
                $it_price = $this->funcomplex_lib->mobile_price($row['index_no']);
                $it_amount = $this->gvcomplex_lib->get_sale_price($row['index_no']);
                $it_point = display_point($row['gpoint']);

                // (시중가 - 할인판매가) / 시중가 X 100 = 할인률%
                $it_sprice = $sale = '';
                if ($row['normal_price'] > $it_amount && !$this->funcomplex_lib->is_uncase($row['index_no'])) {
                    $sett = ($row['normal_price'] - $it_amount) / $row['normal_price'] * 100;
                    $sale = '<span class="sale">[' . number_format($sett, 0) . '%]</span>';
                    $it_sprice = display_price2($row['normal_price']);
                }
                $relatedgs .= "
                        <dl>
                            <a href=\"{$it_href}\">
                                <dt><img src=\"{$it_imageurl}\"></dt>
                                <dd class=\"pname\">{$it_name}</dd>
                                <dd class=\"price\">{$it_sprice} {$it_price}</dd>
                            </a>
                        </dl>";
                $i++;
            }
            $relatedgs .= "
                </div>";
            if ($rel_count > 3) {
                $relatedgs .= "
                    <script>
                    $(document).ready(function(){
                        $('.sp_rel div').slick({
                            autoplay: false,
                            dots: false,
                            arrows: true,
                            infinite: false,
                            slidesToShow: 3,
                            slidesToScroll: 1
                        });
                    });
                    </script>";
            }
            $relatedgs .= "
            </div>";
        }
        $gs_infoTag = "";
        $item_info = $this->config->item('item_info');
        if ($gs['info_value']) {
            $info_data = unserialize(stripslashes($gs['info_value']));
            if (is_array($info_data)) {
                $gubun = $gs['info_gubun'];
                $info_array = $item_info[$gubun]['article'];
                $pd_t2 = '';
                $gs_infoTag .= "
                <div class=\"sp_vbox\" id=\"ids_extra\" style=\"display:none;\">";
                foreach ($info_data as $key => $val) {
                    $ii_title = $info_array[$key][0];
                    $ii_value = $val;
                    $gs_infoTag .= "
                        <ul>
                            <li class='tlst{$pd_t2}'>&#183;&nbsp;&nbsp;{$ii_title}</li>
                            <li class='trst{$pd_t2}'>{$ii_value}</li>
                        </ul>";
                    $pd_t2 = ' padt2';
                } //foreach
                $gs_infoTag .= "
                </div>";
            } //array
        } //if
        $gs_memoTag = $this->funcomplex_lib->get_image_resize($gs['memo']);
        $gs_reviewTag = $this->funcomplex_lib->mobile_goods_review("구매후기", $index_no);
        $gs_qnaTag = $this->funcomplex_lib->mobile_goods_qa("Q&A", $itemqa_count, $index_no);
        $gs_policy = $this->funcomplex_lib->get_policy_content($index_no);

        // bid 관련 태그
        $timesale = "";
        if ($type == "timesale") {
            $timesale_list = $this->global_lib->get_timesale_list2($index_no);

            $order_qty = $this->global_lib->get_order_cnt($gs['index_no']);
            $stock_qty = $gs['original_stock_qty'];
            $percent = ($order_qty / $stock_qty) * 100;
            $percent = $percent > 100 ? 100 : $percent;
            $percent_class = ($percent == 100 || $percent == 0) ? "hide" : "show";

            $timesale .=
                "<div class=\"timesale\">
                    <div class=\"title\">
                        <span class=\"tit-text\">상품구매현황</span>";
            $timesale .= $this->funcomplex_lib->get_mb_timesaleTag($gs, $is_open, "normal", $this->funcomplex_lib->is_soldout($gs['index_no']));
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
                                        <col style=\"width: 15%\">
                                        <col style=\"width: 20%\">
                                        <col style=\"width: 40%\">
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

                        // $timesale .=
                        //     "<tr>
                        //                     <td>" . $row['num'] . "</td>
                        //                     <td>" . $hiddenId . "</td>
                        //                     <td>" . $row['od_time'] . "</td>
                        //                     <td>" . $price . "</td>
                        //                 </tr>";
                    }
                    // $timesale .=
                    //     "</tbody>
                    //             </table>
                    //         </div>";
                }
            }
            // $timesale .=
            //     "<div class=\"table_box\">
            //             <table>
            //                 <colgroup>
            //                     <col style=\"width: 15%\">
            //                     <col style=\"width: 20%\">
            //                     <col style=\"width: 40%\">
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

                // $timesale .=
                //     "<tr>
                //                 <td>" . $num . "</td>
                //                 <td>" . $hiddenId . "</td>
                //                 <td>" . $row['od_time'] . "</td>
                //                 <td>" . $price . "</td>
                //             </tr>";
            }

            // $timesale .=
            //     "</tbody>
            //             </table>
            //         </div>
            //         <button class=\"more_btn\" onclick=\"order_more()\">구매현황 더보기</button>
            //     </div>";
            $timesale .=
                "
                    </div>
				</div>";
        }

        // 상품 찜하기 체크
        $zzim = $this->gvcomplex_lib->zzimCheck($index_no);

        $data = array(
            'gs_id' => $index_no,
            'gs' => $gs,
            'referralCode' => $referralCode,
            'getSalePrice' => $getSalePrice,
            'v_horizImg' => $v_horizImg,
            'gsName' => $gsName,
            'gsExplain' => $gsExplain,
            'aver_score' => $aver_score,
            'no_is_onlyTag' => $no_is_onlyTag,
            'gPointTag' => $gPointTag,
            'cp_btnTag' => $cp_btnTag,
            'gsMarkerTag' => $gsMarkerTag,
            'gsOriginTag' => $gsOriginTag,
            'gsBrandNmTag' => $gsBrandNmTag,
            'gsModelTag' => $gsModelTag,
            'gsOdrMin' => $gsOdrMin,
            'gsOdrMax' => $gsOdrMax,
            'gsOdrStock' => $gsOdrStock,
            'gsOdrSales' => $gsOdrSales,
            'gsOdrPeriod' => $gsOdrPeriod,
            'sc_class' => $sc_class,
            'msa' => $msa,
            'orderOpt' => $orderOpt,
            'naverPayTag' => $naverPayTag,
            'relatedgs' => $relatedgs,
            'gs_infoTag' => $gs_infoTag,
            'gs_memoTag' => $gs_memoTag,
            'gs_reviewTag' => $gs_reviewTag,
            'gs_qnaTag' => $gs_qnaTag,
            'gs_policy' => $gs_policy,
            'slide_url' => $slide_url,
            'slide_cnt' => $slide_cnt,
            'odr_max' => $odr_max,
            'odr_min' => $odr_min,
            'type' => $type,
            'is_open' => $is_open,
            'timesale' => $timesale,
            'zzim' => $zzim
            // 'is_soldout_timesale' => $is_soldout_timesale
        );
        $this->load->view('mb/shop/view', $data);
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
        $tb['title'] = "쿠폰 다운받기";
        $gs_id = $this->input->get('gs_id');
        $gs = $this->global_lib->get_goods($gs_id);
        $this->load->vars(array('tb' => $tb, 'gs' => $gs));
        $this->template->mb_header_sub();
        $this->pop_couponSkin();
        $this->template->mb_footer_sub();
    }

    private function pop_couponSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $title = "쿠폰 다운받기";
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');
        $gs_id = $this->input->get('gs_id');
        $gs = $this->global_lib->get_goods($gs_id);

        $page = $this->input->get('page');

        $cp_list = $this->funcomplex_lib->is_used_coupon('0', $gs_id, $member['id']);

        if (!$cp_list) $cp_list = "NULL";

        $sql_common = " from shop_coupon ";
        $sql_search = " where cp_id in ({$cp_list}) ";
        $sql_order  = " order by cp_id ";

        $sql = " select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 5;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $token = md5(uniqid(rand(), true));
        set_session('ss_token', $token);
        $imageTag = $this->funcomplex_lib->get_it_image($gs['index_no'], $gs['simg1'], 60, 60);
        $gsName = $this->funcomplex_lib->get_text($gs['gname']);
        $gsPrice = $this->funcomplex_lib->mobile_price($gs['index_no']);
        $popTable = $this->popTable($total_count, $result);
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?gs_id=' . $gs_id . '&page=');

        $data = array(
            'token' => $token,
            'title' => $title,
            'imageTag' => $imageTag,
            'gsName' => $gsName,
            'gsPrice' => $gsPrice,
            'popTable' => $popTable,
            'pagination' => $pagination,
            'gs_id' => $gs_id,
            'page' => $page
        );
        $this->load->view('mb/shop/pop_coupon', $data);
    }

    private function popTable($total_count, $result)
    {
        $returnStr = "";
        if (!$total_count) {
            $returnStr = "<p class=\"empty_list\">사용가능한 쿠폰이 없습니다.</p>";
        } else {
            $returnStr .= "
            <table class=\"tbl_cp mart10\">
                <tbody>";
            foreach ($result as $key => $row) {
                $cp_id = $row['cp_id'];
                $str  = "";
                $str .= "<div>&#183; <strong>" . $this->funcomplex_lib->get_text($row['cp_subject']) . "</strong></div>";

                // 쿠폰발행 기간
                $str .= "<div class='padt5'>&#183; 다운로드 기간 : ";
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
                // 혜택
                $str .= "<div class='padt5'>&#183; ";
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
                    $str .= "<div class='padt5'>&#183; " . display_price($row['cp_low_amt']) . " 이상 구매시</div>";
                }

                // 사용가능대상
                $gw_usepart = $this->config->item('gw_usepart');
                $str .= "<div class='padt5'>&#183; " . $gw_usepart[$row['cp_use_part']] . "</div>";

                $s_upd = "<button type=\"button\" onclick=\"post_update('" . base_url() . "m/process/pop_coupon_update', '$cp_id');\" class=\"btn_small\">현재 쿠폰 다운로드</button>";
                $returnStr .= "
                    <tr>
                        <td class=\"cbtn\">
                            {$str}
                            <p class=\"padt10\">{$s_upd}</p>
                        </td>
                    </tr>";
            }
            $returnStr .= "
                    </tbody>
                </table>";
        }
        return $returnStr;
    }

    #qaform
    public function qaform()
    {
        $gs_id = $this->input->get('gs_id');
        $is_member = $this->load->get_var('is_member');
        if (!$is_member) {
            alert_close("로그인 후 작성 가능합니다.");
        }
        $tb['title'] = '상품문의 쓰기';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header_sub();
        $this->qaformSkin();
        $this->template->mb_footer_sub();
    }

    private function qaformSkin()
    {
        $member = $this->load->get_var('member');
        $gs_id = $this->input->get('gs_id');
        $iq_id = $this->input->get('iq_id');
        $iq_ty = $this->input->get('iq_ty');
        $w = $this->input->get('w');
        $gs = $this->global_lib->get_goods($gs_id);
        $iq_name = "";
        $iq_email = "";
        $iq_hp = "";
        $iq_subject = "";
        $iq_question = "";
        $iq_secret = "";
        $title = '상품문의 쓰기';

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
        $form_action_url = base_url() . 'm/process/qaform_update';
        $theme = get_selected($iq_ty, "");
        $goods = get_selected($iq_ty, "상품");
        $delivery = get_selected($iq_ty, "배송");
        $refund = get_selected($iq_ty, "반품/환불/취소");
        $change = get_selected($iq_ty, "교환/변경");
        $selectEct = get_selected($iq_ty, "기타");
        $is_secretTag = get_checked($iq_secret, '1');
        $iqName = $this->funcomplex_lib->get_text($iq_name);
        $iqEmail = $this->funcomplex_lib->get_text($iq_email);
        $iqHp = $this->funcomplex_lib->get_text($iq_hp);
        $iqSubject = $this->funcomplex_lib->get_text($iq_subject);

        $data = array(
            'title' => $title,
            'token' => $token,
            'form_action_url' => $form_action_url,
            'w' => $w,
            'member' => $member,
            'gs_id' => $gs_id,
            'iq_id' => $iq_id,
            'gs' => $gs,
            'theme' => $theme,
            'goods' => $goods,
            'delivery' => $delivery,
            'refund' => $refund,
            'change' => $change,
            'selectEct' => $selectEct,
            'is_secretTag' => $is_secretTag,
            'iqName' => $iqName,
            'iqEmail' => $iqEmail,
            'iqHp' => $iqHp,
            'iqSubject' => $iqSubject,
            'iq_question' => $iq_question
        );
        $this->load->view('mb/shop/qaform', $data);
    }

    #구매후기 화면
    public function view_user()
    {
        $tb['title'] = "구매후기";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header_sub();
        $this->view_userSkin();
        $this->template->mb_footer_sub();
    }

    private function view_userSkin()
    {
        $title = "구매후기";
        $default = $this->load->get_var('default');
        $config = $this->load->get_var('config');
        $pt_id = $this->load->get_var('pt_id');

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $gs_id = $this->input->get('gs_id');
        $page = $this->input->get('page');

        $gs = $this->global_lib->get_goods($gs_id);
        $q1 = "gs_id=$gs_id";
        $sql_common = " from shop_goods_review ";
        $sql_search = " where gs_id = '$gs_id' ";
        if ($default['de_review_wr_use']) {
            $sql_search .= " and pt_id = '$pt_id' ";
        }
        $sql_order  = " order by index_no desc ";

        $sql = "select count(*) as cnt $sql_common $sql_search ";
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
        $view_userTable = $this->view_userTable($result);
        $reviewImg = $this->funcomplex_lib->get_it_image($gs_id, $gs['simg1'], 80, 80);
        $gsName = $this->funcomplex_lib->get_text($gs['gname']);
        $mbPrice = $this->funcomplex_lib->mobile_price($gs_id);

        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'total_count' => $total_count,
            'page' => $page,
            'title' => $title,
            'reviewImg' => $reviewImg,
            'gsName' => $gsName,
            'view_userTable' => $view_userTable,
            'pagination' => $pagination,
            'gs' => $gs,
            'mbPrice' => $mbPrice,
            'gs_id' => $gs_id
        );
        $this->load->view('mb/shop/view_user', $data);
    }

    private function view_userTable($result)
    {
        $returnStr = "<ul>";
        $i = 0;
        $gw_star = $this->config->item('gw_star');
        $member = $this->load->get_var('member');
        foreach ($result as $key => $row) {
            $wr_star = $gw_star[$row['score']];
            $wr_id   = substr($row['mb_id'], 0, 3) . str_repeat("*", strlen($row['mb_id']) - 3);
            $wr_time = substr($row['reg_time'], 0, 10);

            $hash = md5($row['index_no'] . $row['reg_time'] . $row['mb_id']);

            $returnStr .= "<li class=\"lst\">\n";
            $returnStr .= "   <span class=\"lst_post\">" . $row['memo'] . "</span>\n";
            $returnStr .= "   <span class=\"lst_h\"><span class=\"fc_255\">{$wr_star}</span>\n";
            $returnStr .= "   <span class=\"fc_999\"> / {$wr_id} / {$wr_time}";
            if ($this->gvcomplex_lib->is_admin() || ($member['id'] == $row['mb_id'])) {
                // echo "<a href=\"javascript:window.open('".OM_MSHOP_URL."/orderreview.php?gs_id=$row[gs_id]&me_id=$row[index_no]&w=u');\" class=\"marl10 tu fc_blk\">수정</a>\n";
                $returnStr .= "<a href=\"" . base_url() . "m/process/orderreview_update?gs_id=$row[gs_id]&me_id=$row[index_no]&w=d&hash=$hash&p=1\" class=\"marl5 tu fc_blk itemqa_delete\">삭제</a>\n";
            }
            $returnStr .= "   </span>\n";
            $returnStr .= "</li>\n";
            $i++;
        }
        if ($i == 0) {
            $returnStr .= "<li class=\"empty_list\">자료가 없습니다.</li>";
        }
        $returnStr .= "</ul>";
        return $returnStr;
    }

    public function reviewDetail()
    {
        $index_no = $this->input->get('index_no');
        $gs_id = $this->input->get('gs_id');
        $current = $this->input->get('current');

        $returnData = $this->funcomplex_lib->getReviewData($gs_id);
        $result = $returnData['result'];
        $is_copy = $returnData['is_copy'];
        $copy_id = $returnData['copy_id'];
        $item_use_count = count($result);
        $original_data = $returnData['original_data'];
        $index_arr = $returnData['index_arr'];

        foreach ($result as $row) {
            if ($row['index_no'] == $index_no) {
                $current = $row['num'];
            }
        }

        // 선택한 리뷰 데이터 가져오기
        $sql = "SELECT * FROM shop_goods_review WHERE index_no = ?";
        $param = array($index_no);
        $review_data = $this->query_model->returnOneArr($sql, $param);
        $gs = $this->global_lib->get_goods($review_data['gs_id'], 'gname');
        $gname = $gs['gname'];
        $member = $this->global_lib->get_member($review_data['mb_id']);

        // 이전, 다음 리뷰 가져오기
        // 카피 상품일경우
        $res = array();
        if ($is_copy == 'y') { // 카피 상품
            $param = array($index_no, $copy_id, $gs_id);
            $where_sql = "AND (gs_id = ? or gs_id = ?)";
        } else if (!empty($original_data)) { // 오리지날 상품
            $param = array($index_no, $index_arr, $gs_id);
            $where_sql = "AND (gs_id in ? or gs_id = ?)";
        } else {
            $param = array($index_no, $gs_id);
            $where_sql = "AND gs_id = ?";
        }

        $prev_sql = "SELECT * FROM shop_goods_review WHERE index_no > ? {$where_sql} ORDER BY reg_time ASC LIMIT 1";
        $next_sql = "SELECT * FROM shop_goods_review WHERE index_no < ? {$where_sql} ORDER BY reg_time DESC LIMIT 1";

        $prev_data = $this->query_model->returnOneArr($next_sql, $param);
        $next_data = $this->query_model->returnOneArr($prev_sql, $param);

        $prev_onclick = "";
        $next_onclick = "";

        if (!empty($prev_data)) {
            $prev_onclick = "open_review_modal(" . $prev_data['index_no'] . "," . $gs_id . ")";
        } else {
            $prev_onclick = "alert('리뷰의 끝입니다.')";
        }
        if (!empty($next_data)) {
            $next_onclick = "open_review_modal(" . $next_data['index_no'] . "," . $gs_id . ")";
        } else {
            $next_onclick = "alert('리뷰의 끝입니다.')";
        }

        // 리뷰 데이터로 html 짜기
        $returnData = "";
        $returnData .= "<div class=\"content_area\">
							<div class=\"left_btn\" onclick=\"{$next_onclick}\"></div>
							<div class=\"right_btn\" onclick=\"{$prev_onclick}\"></div>
							<div class=\"review_modal_box\">
								<div class=\"content_box\">
									<div class=\"info\">
										<div class=\"row\">
											<div class=\"user_icon\">
												<img src=\"" . base_url() . "data/member/{$member['mb_img']}\">
											</div>
											<div class=\"vertical\">
												<div class=\"id\">{$review_data['mb_id']}</div>
												<div class=\"row\">
													<div class=\"star\">";
        for ($i = 0; $i < (int)$review_data['score']; $i++) {
            $returnData .=                              "<img src=\"" . base_url() . "dist/img/mb/star.png\" align=\"absmiddle\">";
        }
        $returnData .=
            "</div>
													<div class=\"date\">" . substr($review_data['reg_time'], 0, 10) . "</div>
												</div>
												<div class=\"name\">{$gname}</div>
											</div>
										</div>
									</div>
								</div>
								<div class=\"photo_box\">";
        foreach (['img1', 'img2', 'img3', 'img4'] as $imgKey) {
            if (!empty($review_data[$imgKey])) {
                $media_type = $this->funcomplex_lib->getMediaType($review_data[$imgKey]); // 사진/동영상 출력

                if ($media_type == "img") {
                    $returnData .= "<img src=\"" . base_url() . "data/review/{$review_data[$imgKey]}\">";
                } else {
                    $returnData .= "<video controls src=\"" . base_url() . "data/review/{$review_data[$imgKey]}\"></video>";
                }
            }
        }
        $returnData .=          "</div>
								<div class=\"content\">
									{$review_data['memo']}
								</div>
							</div>
						</div>";

        $data = array(
            'result' => $returnData,
            'item_use_count' => $item_use_count,
            'current' => $current
        );
        $this->template->mb_header_sub();
        $this->load->view('mb/shop/orderreviewdetail', $data);
        $this->template->mb_footer_sub();
    }

    #orderreview
    public function orderreview()
    {
        $is_member = $this->load->get_var('is_member');
        if (!$is_member) {
            alert_close("로그인 후 작성 가능합니다.");
        }
        $tb['title'] = "구매후기 작성";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header_sub();
        $this->orderreviewSkin();
        $this->template->mb_footer_sub();
    }

    private function orderreviewSkin()
    {
        $title = "구매후기 작성";
        $member = $this->load->get_var('member');
        $gs_id = $this->input->get('gs_id');
        $od_id = $this->input->get('od_id');
        $od_no = $this->input->get('od_no');
        $od_index = $this->input->get('od_index');
        $w = $this->input->get('w');

        $gs = $this->global_lib->get_goods($gs_id);

        $wr_score = "";
        $wr_content = "";

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

        $form_action_url = base_url() . 'm/process/orderreview_update';
        $imageTag = $this->funcomplex_lib->get_it_image($gs_id, $gs['simg1'], 80, 80);
        $gsName = $this->funcomplex_lib->get_text($gs['gname']);
        $mbPrice = $this->funcomplex_lib->mobile_price($gs_id);

        $data = array(
            'title' => $title,
            'form_action_url' => $form_action_url,
            'w' => $w,
            'gs_id' => $gs_id,
            'od_id' => $od_id,
            'od_no' => $od_no,
            'gs' => $gs,
            'token' => $token,
            'imageTag' => $imageTag,
            'gsName' => $gsName,
            'mbPrice' => $mbPrice,
            'wr_content' => $wr_content,
            'review_data' => $review_data
        );
        $this->load->view('mb/shop/orderreview', $data);
    }
    #orderinicis
    public function orderinicis()
    {
        $od_id = $this->input->get('od_id');
        $sql = "select * from shop_order where od_id=?";
        $param = array($od_id);
        $od = $this->query_model->returnOneArr($sql, $param);
        if (!$od['od_id']) {
            alert("결제할 주문서가 없습니다.");
        }
        $tb['title'] = '결제하기';
        $this->load->vars(array('tb' => $tb));

        $this->template->mb_header();
        $this->orderinicisSkin($od_id, $od);
        $this->template->mb_footer();
    }
    private function orderinicisSkin($od_id, $od)
    {
        $default = $this->load->get_var('default');
        include('./Bill/settle_inicis.inc.php'); #orderform1에 쓸 변수가 여기에 들어가있음

        set_session('ss_order_id', $od_id);
        set_session('ss_order_inicis_id', $od_id);
        $stotal = $this->global_lib->get_order_spay($od_id); // 총계
        $tot_price = get_session('tot_price'); // 결제금액
        $order_action_url = base_url() . 'm/process/orderformresult';

        $goods = '';
        $goods_count = -1;

        $sql = " select * from shop_cart where od_id = ? and ct_select = '0' group by gs_id order by index_no ";
        $param = array($od_id);
        $i = 0;
        $ulList = "";
        $result = $this->query_model->returnArr($sql, $param);
        foreach ($result as $key => $row) {
            $rw = $this->global_lib->get_order($row['od_no']);

            $gs = $this->global_lib->get_goods($row['gs_id'], 'gname,simg1');

            if (!$goods)
                $goods = preg_replace("/\?|\'|\"|\||\,|\&|\;/", "", $gs['gname']);

            $goods_count++;

            unset($it_name);
            $it_options = $this->funcomplex_lib->mobile_print_complete_options($row['gs_id'], $od_id);
            if ($it_options) {
                $it_name = '<div class="li_name_od">' . $it_options . '</div>';
            }


            $ulList .= "
            <li class=\"sod_li\">
                <div class=\"li_opt\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</div>
                {$it_name}
                <div class=\"li_prqty\">
                    <span class=\"prqty_price li_prqty_sp\"><span>상품금액 </span>" . number_format($rw['goods_price']) . "</span>
                    <span class=\"prqty_qty li_prqty_sp\"><span>수량 </span>" . number_format($rw['sum_qty']) . "</span>
                    <span class=\"prqty_sc li_prqty_sp\"><span>배송비 </span>" . number_format($rw['baesong_price']) . "</span>
                    <span class=\"prqty_stat li_prqty_sp\"><span>상태 </span>주문대기</span>
                </div>
                <div class=\"li_total\" style=\"padding-left:60px;height:auto !important;height:50px;min-height:50px;\">
                    <span class=\"total_img\">" . $this->funcomplex_lib->get_od_image($rw['od_id'], $gs['simg1'], 50, 50) . "</span>
                    <span class=\"total_price total_span\"><span>결제금액 </span>" . number_format($rw['use_price']) . "</span>
                    <span class=\"total_point total_span\"><span>적립포인트 </span>" . number_format($rw['sum_point']) . "</span>
                </div>
            </li>";
        }

        if ($goods_count) $goods .= ' 외 ' . $goods_count . '건';
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
        $totPrice = display_price($stotal['price']);
        $couponTag = "";
        if ($stotal['coupon']) {
            $couponTag = "
            <dt class=\"sod_bsk_dvr\"><span>쿠폰할인</span></dt>
            <dd class=\"sod_bsk_dvr\"><strong>" . display_price($stotal['coupon']) . "</strong></dd>";
        }
        $usePointTag = "";
        if ($stotal['usepoint']) {
            $usePointTag = "
            <dt class=\"sod_bsk_dvr\"><span>포인트결제</span></dt>
            <dd class=\"sod_bsk_dvr\"><strong>" . display_point($stotal['usepoint']) . "</strong></dd>";
        }
        $baesongTag = "";
        if ($stotal['baesong']) {
            $baesongTag = "
            <dt class=\"sod_bsk_dvr\"><span>배송비</span></dt>
            <dd class=\"sod_bsk_dvr\"><strong>" . display_price($stotal['baesong']) . "</strong></dd>";
        }
        $stotalUsePrice = display_price($stotal['useprice']);
        $stotalPrice = display_point($stotal['point']);
        $orderform1Tag = $this->Inicisorderform1($od_id, $goods, $tot_price, $next_url, $noti_url);
        $od_name = $this->funcomplex_lib->get_text($od['name']);
        $od_tel = $this->funcomplex_lib->get_text($od['telephone']);
        $od_cel = $this->funcomplex_lib->get_text($od['cellphone']);
        $od_add = $this->funcomplex_lib->get_text(sprintf("(%s)", $od['zip']) . ' ' . $this->funcomplex_lib->print_address($od['addr1'], $od['addr2'], $od['addr3'], $od['addr_jibeon']));
        $od_email = $this->funcomplex_lib->get_text($od['email']);

        $od_bname = $this->funcomplex_lib->get_text($od['b_name']);
        $od_btel = $this->funcomplex_lib->get_text($od['b_telephone']);
        $od_bcel = $this->funcomplex_lib->get_text($od['b_cellphone']);
        $od_baddr = $this->funcomplex_lib->get_text(sprintf("(%s)", $od['b_zip']) . ' ' . $this->funcomplex_lib->print_address($od['b_addr1'], $od['b_addr2'], $od['b_addr3'], $od['b_addr_jibeon']));
        $od_memo = "";
        if ($od['memo']) {
            $od_memo = "
			<tr>
				<th scope=\"row\">전하실 말씀</th>
				<td>" . $this->funcomplex_lib->conv_content($od['memo'], 0) . "</td>
			</tr>";
        }
        $Inicisorderform2 = $this->Inicisorderform2($od_id, $od, $tot_price, $comm_tax_mny, $comm_vat_mny, $comm_free_mny, $goods);
        $flag_useJs = "";
        $payBtnJs = "";
        $preimportJs = "";
        if ($default['de_tax_flag_use']) {
            $flag_useJs = "
            f.P_TAX.value = f.comm_vat_mny.value;
            f.LGD_TAXFREEAMOUNT.value = f.comm_free_mny.value;";
            $payBtnJs = "
                var comm_tax_mny =f.comm_tax_mny.value;
                var comm_vat_mny =f.comm_vat_mny.value;
                var comm_free_mny =f.comm_free_mny.value;
            ";
            $preimportJs = "
            'comm_tax_mny':comm_tax_mny,
            'comm_vat_mny':comm_vat_mny,
            'comm_free_mny':comm_free_mny,
            ";
        }
        $returnurl = base_url() . "m/shop/orderinicis?od_id=";
        $data = array(
            'order_action_url' => $order_action_url,
            'ulList' => $ulList,
            'totPrice' => $totPrice,
            'couponTag' => $couponTag,
            'usePointTag' => $usePointTag,
            'baesongTag' => $baesongTag,
            'stotalUsePrice' => $stotalUsePrice,
            'stotalPrice' => $stotalPrice,
            'orderform1Tag' => $orderform1Tag,
            'od_name' => $od_name,
            'od_tel' => $od_tel,
            'od_cel' => $od_cel,
            'od_add' => $od_add,
            'od_email' => $od_email,
            'od_bname' => $od_bname,
            'od_btel' => $od_btel,
            'od_bcel' => $od_bcel,
            'od_baddr' => $od_baddr,
            'od_memo' => $od_memo,
            'Inicisorderform2' => $Inicisorderform2,
            'flag_useJs' => $flag_useJs,
            'od_id' => $od_id,
            'return_url' => $return_url,
            'payBtnJs' => $payBtnJs,
            'preimportJs' => $preimportJs
        );
        $this->load->view('mb/shop/orderinicis', $data);
    }
    private function Inicisorderform1($od_id, $goods, $tot_price, $next_url, $noti_url)
    {
        $default = $this->load->get_var('default');
        $returnStr = "
        <form name=\"sm_form\" method=\"POST\" action=\"\" accept-charset=\"euc-kr\">
            <input type=\"hidden\" name=\"P_OID\"		 value=\"{$od_id}\">
            <input type=\"hidden\" name=\"P_GOODS\"		 value=\"{$goods}\">
            <input type=\"hidden\" name=\"P_AMT\"		 value=\"{$tot_price}\">
            <input type=\"hidden\" name=\"P_UNAME\"	     value=\"\">
            <input type=\"hidden\" name=\"P_MOBILE\"	 value=\"\">
            <input type=\"hidden\" name=\"P_EMAIL\"		 value=\"\">
            <input type=\"hidden\" name=\"P_MID\"		 value=\"{$default['de_inicis_mid']}\">
            <input type=\"hidden\" name=\"P_NEXT_URL\"	 value=\"{$next_url}\">
            <input type=\"hidden\" name=\"P_NOTI_URL\"	 value=\"{$noti_url}\">
            <input type=\"hidden\" name=\"P_RETURN_URL\" value=\"\">
            <input type=\"hidden\" name=\"P_HPP_METHOD\" value=\"2\">
            <input type=\"hidden\" name=\"P_RESERVED\"   value=\"" . $this->get_inicis_app_scheme() . "bank_receipt=N&twotrs_isp=Y&block_isp=Y{$useescrow}\">
            <input type=\"hidden\" name=\"DEF_RESERVED\" value=\"" . $this->get_inicis_app_scheme() . "bank_receipt=N&twotrs_isp=Y&block_isp=Y{$useescrow}\">
            <input type=\"hidden\" name=\"P_NOTI\"		 value=\"{$od_id}\">
            <input type=\"hidden\" name=\"P_QUOTABASE\"  value=\"01:02:03:04:05:06:07:08:09:10:11:12\"> <!-- 할부기간 설정 01은 일시불 -->
            <input type=\"hidden\" name=\"P_SKIP_TERMS\" value=\"\">

            <input type=\"hidden\" name=\"good_mny\"	 value=\"{$tot_price}\" >";

        if ($default['de_tax_flag_use']) {
            $returnStr .= "
                <input type=\"hidden\" name=\"P_TAX\"		 value=\"\">
                <input type=\"hidden\" name=\"P_TAXFREE\"	 value=\"\">";
        }
        $returnStr .= "
        </form>";
        return $returnStr;
    }
    private function get_inicis_app_scheme()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $iPod = stripos($user_agent, "iPod");
        $iPhone  = stripos($user_agent, "iPhone");
        $iPad    = stripos($user_agent, "iPad");

        if ($iPod || $iPhone || $iPad) {    //IOS 의 앱브라우저에서 ISP결제시 리다이렉트 safari로 돌아가는 문제가 있음
            if (preg_match('/NAVER\(inapp;/', $user_agent)) {       //네이버
                return 'app_scheme=naversearchapp://&';
            } else if (preg_match('/CriOS/', $user_agent)) {          //크롬
                return 'app_scheme=googlechromes://&';
            } else if (preg_match('/DaumDevice/', $user_agent)) {      //다음
                return 'app_scheme=daumapps://&';
            } else if (preg_match('/KAKAOTALK/', $user_agent)) {          //카카오톡
                return 'app_scheme=kakaotalk://&';
            } else if (preg_match('/(FBAN|FBAV)/', $user_agent)) {        //페이스북
                return 'app_scheme=fb://&';
            }
        }

        return '';
    }
    private function Inicisorderform2($od_id, $od, $tot_price, $comm_tax_mny, $comm_vat_mny, $comm_free_mny, $goods)
    {
        $default = $this->load->get_var('default');
        $returnStr = "";
        $returnStr .= "
        <input type=\"hidden\" name=\"od_id\" value=\"{$od_id}\">
        <input type=\"hidden\" name=\"od_settle_case\" value=\"{$od['paymethod']}\">
        <input type=\"hidden\" name=\"od_name\" value=\"{$od['name']}\">
        <input type=\"hidden\" name=\"od_tel\" value=\"{$od['telephone']}\">
        <input type=\"hidden\" name=\"od_hp\" value=\"{$od['cellphone']}\">
        <input type=\"hidden\" name=\"od_zip\" value=\"{$od['zip']}\">
        <input type=\"hidden\" name=\"od_addr1\" value=\"{$od['addr1']}\">
        <input type=\"hidden\" name=\"od_addr2\" value=\"{$od['addr2']}\">
        <input type=\"hidden\" name=\"od_addr3\" value=\"{$od['addr3']}\">
        <input type=\"hidden\" name=\"od_addr_jibeon\" value=\"{$od['addr_jibeon']}\">
        <input type=\"hidden\" name=\"od_email\" value=\"{$od['email']}\">
        <input type=\"hidden\" name=\"od_b_name\" value=\"{$od['b_name']}\">
        <input type=\"hidden\" name=\"od_b_tel\" value=\"{$od['b_telephone']}\">
        <input type=\"hidden\" name=\"od_b_hp\" value=\"{$od['b_cellphone']}\">
        <input type=\"hidden\" name=\"od_b_zip\" value=\"{$od['b_zip']}\">
        <input type=\"hidden\" name=\"od_b_addr1\" value=\"{$od['b_addr1']}\">
        <input type=\"hidden\" name=\"od_b_addr2\" value=\"{$od['b_addr2']}\">
        <input type=\"hidden\" name=\"od_b_addr3\" value=\"{$od['b_addr3']}\">
        <input type=\"hidden\" name=\"od_b_addr_jibeon\" value=\"{$od['b_addr_jibeon']}\">

        <input type=\"hidden\" name=\"gopaymethod\" value=\"\">
        <input type=\"hidden\" name=\"acceptmethod\" value=\"\">

        <input type=\"hidden\" name=\"good_mny\" value=\"{$tot_price}\">

        <input type=\"hidden\" name=\"P_HASH\" value=\"\">
        <input type=\"hidden\" name=\"P_TYPE\" value=\"\">
        <input type=\"hidden\" name=\"P_UNAME\" value=\"\">
        <input type=\"hidden\" name=\"P_AUTH_DT\" value=\"\">
        <input type=\"hidden\" name=\"P_AUTH_NO\" value=\"\">
        <input type=\"hidden\" name=\"P_HPP_CORP\" value=\"\">
        <input type=\"hidden\" name=\"P_APPL_NUM\" value=\"\">
        <input type=\"hidden\" name=\"P_VACT_NUM\" value=\"\">
        <input type=\"hidden\" name=\"P_VACT_NAME\" value=\"\">
        <input type=\"hidden\" name=\"P_VACT_BANK\" value=\"\">
        
        <input type=\"hidden\" name=\"P_TAX\" value=\"\">
        <input type=\"hidden\" name=\"LGD_TAXFREEAMOUNT\" value=\"\">
        
        <input type=\"hidden\" name=\"P_CARD_ISSUER\" value=\"\">

        <input type=\"hidden\" name=\"version\" value=\"1.0\">
        <input type=\"hidden\" name=\"mid\" value=\"\">
        <input type=\"hidden\" name=\"oid\" value=\"$od_id\">
        <input type=\"hidden\" name=\"goodname\" value=\"$goods\">
        <input type=\"hidden\" name=\"price\" value=\"$tot_price\">
        <input type=\"hidden\" name=\"buyername\" value=\"\">
        <input type=\"hidden\" name=\"buyeremail\" value=\"\">
        <input type=\"hidden\" name=\"parentemail\"	value=\"\">
        <input type=\"hidden\" name=\"buyertel\" value=\"\">
        <input type=\"hidden\" name=\"recvname\" value=\"\">
        <input type=\"hidden\" name=\"recvtel\" value=\"\">
        <input type=\"hidden\" name=\"recvaddr\" value=\"\">
        <input type=\"hidden\" name=\"recvpostnum\"	value=\"\">";

        if ($default['de_tax_flag_use']) {
            $returnStr .= "
            <input type=\"hidden\" name=\"comm_tax_mny\" value=\"{$comm_tax_mny}\">  <!-- 과세금액 -->
            <input type=\"hidden\" name=\"comm_vat_mny\" value=\"{$comm_vat_mny}\">  <!-- 부가세 -->
            <input type=\"hidden\" name=\"comm_free_mny\" value=\"{$comm_free_mny}\"> <!-- 비과세 금액 -->";
        }
        $returnStr .= "
        <div id=\"display_pay_button\" class=\"btn_confirm\">
            <input type=\"button\" name=\"submitChecked\" onClick=\"forderform_check(this.form);\" value=\"결제하기\" class=\"btn_medium wset\" id=\"show_req_btn\">
            <a href=\"" . base_url() . "m/main\" class=\"btn_medium bx-white\">취소</a>
        </div>";
        return $returnStr;
    }
    public function orderkakaopay()
    {
        $od_id = $this->input->get('od_id');
        $sql = "select * from shop_order where od_id=?";
        $param = array($od_id);
        $od = $this->query_model->returnOneArr($sql, $param);
        if (!$od['od_id']) {
            alert("결제할 주문서가 없습니다.");
        }

        $tb['title'] = '결제하기';
        $this->load->vars(array('tb' => $tb));

        $this->template->mb_header();
        $this->orderkakaopaySkin($od_id, $od);
        $this->template->mb_footer();
    }
    private function orderkakaopaySkin($od_id, $od)
    {
        set_session('ss_order_id', $od_id);

        $stotal = $this->global_lib->get_order_spay($od_id); // 총계
        $tot_price = get_session('tot_price'); // 결제금액
        $order_action_url = base_url() . 'm/process/orderformresult';
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
                $it_name = '<div class="li_name_od">' . $it_options . '</div>';
            }
            $goods_cnt += $rw['sum_qty'];
            $kakaoTableTag .= "
            <li class=\"sod_li\">
                <div class=\"li_opt\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</div>
                {$it_name}
                <div class=\"li_prqty\">
                    <span class=\"prqty_price li_prqty_sp\"><span>상품금액 </span>" . number_format($rw['goods_price']) . "</span>
                    <span class=\"prqty_qty li_prqty_sp\"><span>수량 </span>" . number_format($rw['sum_qty']) . "</span>
                    <span class=\"prqty_sc li_prqty_sp\"><span>배송비 </span>" . number_format($rw['baesong_price']) . "</span>
                    <span class=\"prqty_stat li_prqty_sp\"><span>상태 </span>주문대기</span>
                </div>
                <div class=\"li_total\" style=\"padding-left:60px;height:auto !important;height:50px;min-height:50px;\">
                    <span class=\"total_img\">" . $this->funcomplex_lib->get_od_image($rw['od_id'], $gs['simg1'], 50, 50) . "</span>
                    <span class=\"total_price total_span\"><span>결제금액 </span>" . number_format($rw['use_price']) . "</span>
                    <span class=\"total_point total_span\"><span>적립포인트 </span>" . number_format($rw['sum_point']) . "</span>
                </div>
            </li>";
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
        $couponStr = "";
        if ($stotal['coupon']) {
            $couponStr = "
            <dt class=\"sod_bsk_dvr\"><span>쿠폰할인</span></dt>
            <dd class=\"sod_bsk_dvr\"><strong>" . display_price($stotal['coupon']) . "/strong></dd>";
        }
        $usepointStr = "";
        if ($stotal['usepoint']) {
            $usepointStr = "
            <dt class=\"sod_bsk_dvr\"><span>포인트결제</span></dt>
            <dd class=\"sod_bsk_dvr\"><strong>" . display_point($stotal['usepoint']) . "</strong></dd>";
        }
        $baesongStr = "";
        if ($stotal['baesong']) {
            $baesongStr = "
            <dt class=\"sod_bsk_dvr\"><span>배송비</span></dt>
            <dd class=\"sod_bsk_dvr\"><strong>" . display_price($stotal['baesong']) . "</strong></dd>";
        }

        $stotal_point = display_price($stotal['point']);
        $stotal_useprice = display_price($stotal['useprice']);


        $kakaoOrderForm2 = "";
        if ($default['de_kakao_use']) {
            $kakaoOrderForm2 = $this->KakaoOrderform2($od_id, $od, $tot_price, $comm_tax_mny, $comm_vat_mny, $comm_free_mny, $goods, $goods_cnt);
        }
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



        $data = array(
            'kakaoTableTag' => $kakaoTableTag,
            'stotal_price' => $stotal_price,
            'stotal_point' => $stotal_point,
            'stotal_useprice' => $stotal_useprice,
            'couponStr' => $couponStr,
            'usepointStr' => $usepointStr,
            'baesongStr' => $baesongStr,
            'order_action_url' => $order_action_url,
            'kakaoOrderForm2' => $kakaoOrderForm2,
            'od_name' => $od_name,
            'od_tele' => $od_tele,
            'od_cell' => $od_cell,
            'od_addr' => $od_addr,
            'od_email' => $od_email,
            'od_Bname' => $od_Bname,
            'od_Btele' => $od_Btele,
            'od_Bcell' => $od_Bcell,
            'od_Baddr' => $od_Baddr,
            'od_memo' => $od_memo,
            'kakaoPayTag' => $kakaoPayTag,
            'de_tax_flagJs' => $de_tax_flagJs,
            'od_id' => $od_id,
            'de_tax_flagJs2' => $de_tax_flagJs2,
            'de_tax_flagJs3' => $de_tax_flagJs3
        );
        $this->load->view('mb/shop/orderkakaopay', $data);
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
    #장바구니
    public function cart()
    {
        $tb['title'] = '장바구니';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->cartSkin();
        $this->template->mb_footer();
    }

    private function cartSkin()
    {
        $title = "장바구니";
        $set_mb_id = "";
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        if (!$is_member) {
            $urlencode = $this->load->get_var('urlencode');
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);
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
        $cart_action_url = base_url() . 'm/process/cartupdate';
        $result = $resultObj->result_array();
        $notEmpty = "";
        if ($cart_count) {
            $notEmpty = "
            <div id=\"sod_chk\">
                <input type=\"checkbox\" name=\"ct_all\" value=\"1\" id=\"ct_all\" checked=\"checked\">
                <label for=\"ct_all\">전체상품 선택</label>
            </div>";
        }
        $tot_point        = 0;
        $tot_sell_price = 0;
        $tot_opt_price    = 0;
        $tot_sell_qty    = 0;
        $tot_sell_amt    = 0;
        $i = 0;
        $cartTable = "";
        foreach ($result as $key => $row) {
            $gs = $this->global_lib->get_goods($row['gs_id']);
            $this->load->vars(array('gs' => $gs));

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

            $it_options = $this->funcomplex_lib->mobile_print_item_options($row['index_no'], $set_mb_id);

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
            $info = $this->funcomplex_lib->get_item_sendcost($row, $sell_price);
            $item_sendcost[] = $info['pattern'];

            $href = base_url() . 'm/shop/view?index_no=' . $row['gs_id'];
            $info['price'] = isset($info['price']) ? $info['price'] : 0;
            $cartTable .= "
            <li class=\"sod_li\">
			<input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$row['gs_id']}\">
            <div class=\"li_chk\">
                <label for=\"ct_chk_{$i}\" class=\"sound_only\">상품</label>
                <input type=\"checkbox\" name=\"ct_chk[{$i}]\" value=\"1\" id=\"ct_chk_{$i}\" checked=\"checked\">
            </div>
            <div class=\"li_name\">
                <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$row['gs_id']}\">
                <input type=\"hidden\" name=\"index_no[{$i}]\" value=\"{$row['index_no']}\">
                <a href=\"{$href}\">" . stripslashes($gs['gname']) . "</a>";
            if ($it_options) {
                $cartTable .= "
				<div class=\"sod_opt\">{$it_options}</div>";
            }
            $cartTable .= "
                <span class=\"total_img\">" . $this->funcomplex_lib->get_it_image($row['gs_id'], $gs['simg1'], 80, 80) . "</span> 
				<div class=\"li_mod\" style=\"padding-left:100px;\">";
            if ($it_options) {
                $cartTable .= "<button type=\"button\" id=\"mod_opt_{$row['gs_id']}\" class=\"mod_btn mod_options\">옵션변경/추가</button>";
            }
            $cartTable .= "
				</div>				
            </div>
            <div class=\"li_prqty\">
                <span class=\"prqty_price li_prqty_sp\"><span>판매가</span>" . number_format($sell_amt) . "</span>
                <span class=\"prqty_qty li_prqty_sp\"><span>수량</span>" . number_format($sell_qty) . "</span>
                <span class=\"prqty_sc li_prqty_sp\"><span>배송비</span>" . number_format($info['price']) . "</span>
            </div>
            <div class=\"li_total\">
                <span class=\"total_price total_span\"><span>소계</span>
				<strong>" . number_format($sell_price) . "</strong></span>
                <span class=\"total_point total_span\"><span>적립포인트</span>
				<strong>" . number_format($point) . "</strong></span>
            </div>
            </li>";
            $tot_point        += $point;
            $tot_sell_price += $sell_price;
            $tot_opt_price    += $sell_opt_price;
            $tot_sell_qty    += $sell_qty;
            $tot_sell_amt    += $sell_amt;
            if (!$is_member) {
                $tot_point = 0;
            }
            $i++;
        }
        // 배송비 검사
        $send_cost = 0;
        $com_send_cost = 0;
        $sep_send_cost = 0;
        $max_send_cost = 0;
        $tot_price = 0;

        if ($i > 0) {
            $k = 0;
            $condition = array();
            foreach ($item_sendcost as $key) {
                list($userid, $bundle, $price) = explode('|', $key);
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

            $tune = $this->gvcomplex_lib->get_tune_sendcost($com_array, $val_array, $item_sendcost);

            $send_cost = $com_send_cost + $sep_send_cost; // 총 배송비합계
            $tot_send_cost = $max_send_cost + $sep_send_cost; // 최종배송비
            $tot_final_sum = $send_cost - $tot_send_cost; // 배송비할인
            $tot_price = $tot_sell_price + $tot_send_cost; // 결제예정금액
        }
        if ($i == 0) {
            $cartTable = '<li class="empty_list">장바구니에 담긴 상품이 없습니다.</li>';
        }
        $deliveryPay = "";
        if ($i > 0) {
            $deliveryPay .= "
            <dl id=\"sod_bsk_tot\">";
            if ($tot_send_cost > 0) { // 배송비가 0 보다 크다면 (있다면)
                $deliveryPay .= "
                    <dt class=\"sod_bsk_dvr\"><span>배송비</span></dt>
                    <dd class=\"sod_bsk_dvr\"><strong>" . number_format($tot_send_cost) . " 원</strong></dd>";
            }
            if ($tot_price > 0) {
                $deliveryPay .= "
                    <dt class=\"sod_bsk_cnt\"><span>총계</span></dt>
                    <dd class=\"sod_bsk_cnt\"><strong>" . number_format($tot_price) . " 원</strong></dd>
                    <dt><span>포인트</span></dt>
                    <dd><strong>" . number_format($tot_point) . " P</strong></dd>";
            }
            $deliveryPay .= "
            </dl>";
        }
        $shopKeep = "";
        $naverpay_button_js = $this->load->get_var('naverpay_button_js');
        $naverpay_request_js = $this->load->get_var('naverpay_request_js');

        if ($i == 0) {
            $shopKeep = "<a href=\"" . base_url() . "m/main\" class=\"btn_medium bx-black\">쇼핑 계속하기</a>";
        } else {
            $shopKeep .= "
            <input type=\"hidden\" name=\"url\" value=\"" . base_url() . "m/shop/orderform\">
            <input type=\"hidden\" name=\"act\" value=\"\">
            <input type=\"hidden\" name=\"records\" value=\"{$i}\">
            <button type=\"button\" onclick=\"return form_check('buy');\" class=\"btn_medium wset\">주문하기</button>
            <div><button type=\"button\" onclick=\"return form_check('seldelete');\" class=\"btn01\">선택삭제</button>
            <button type=\"button\" onclick=\"return form_check('alldelete');\" class=\"btn01\">비우기</button></div>";
            if ($naverpay_button_js) {
                $shopKeep .= "<div class=\"naverpay-cart\">{$naverpay_request_js} {$naverpay_button_js}</div>";
            }
        }
        $data = array(
            'title' => $title,
            'cart_action_url' => $cart_action_url,
            'cart_count' => $cart_count,
            'notEmpty' => $notEmpty,
            'cartTable' => $cartTable,
            'deliveryPay' => $deliveryPay,
            'shopKeep' => $shopKeep
        );
        $this->load->view('mb/shop/cart', $data);
    }
    #마이페이지
    public function mypage()
    {
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if (!$is_member) {
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);
        }
        $tb['title'] = "마이페이지";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->mypageSkin();
        $this->template->mb_footer();
    }
    private function mypageSkin()
    {
        $gw_status = $this->config->item('gw_status');
        $member = $this->load->get_var('member');
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
        $cp_countTag = display_qty($cp_count);
        $mbPoint = display_point($member['point']);
        $mbmileage = display_point($member['mileage']);
        $mbTel = ($member['telephone'] ? $member['telephone'] : '미등록');
        $mbEmail = ($member['email'] ? $member['email'] : '미등록');
        $mbAddr = sprintf("(%s)", $member['zip']) . ' ' . $this->funcomplex_lib->print_address($member['addr1'], $member['addr2'], $member['addr3'], $member['addr_jibeon']);
        $sql = " select * from shop_order where mb_id = ? and dan != '0' group by od_id order by index_no desc limit 3 ";
        $param = array($member['id']);
        $result = $this->query_model->returnArr($sql, $param);
        $i = 0;
        $mypageTable = "";
        foreach ($result as $key => $row) {
            $mypageTable .= '<li>';
            $sql = " select * from shop_cart where od_id = '$row[od_id]' ";
            $sql .= " group by gs_id order by io_type asc, index_no asc ";
            $res = $this->query_model->returnArr($sql);
            $k = 0;
            foreach ($res as $key => $ct) {
                $rw = $this->global_lib->get_order($ct['od_no']);
                $gs = unserialize($rw['od_goods']);

                $href = base_url() . 'm/shop/view?index_no=' . $rw['gs_id'];

                $dlcomp = explode('|', trim($rw['delivery']));

                $delivery_str = '';
                if ($dlcomp[0] && $rw['delivery_no']) {
                    $delivery_str = $this->funcomplex_lib->get_text($dlcomp[0]) . ' ' . $this->funcomplex_lib->get_text($rw['delivery_no']);
                }

                $uid = md5($rw['od_id'] . $rw['od_time'] . $rw['od_ip']);
                if ($k == 0) {
                    $mypageTable .= "
                    <div class=\"inquiry_idtime\">
                        <a href=\"" . base_url() . "m/shop/orderinquiryview?od_id={$rw['od_id']}&uid={$uid}\" class=\"idtime_link\">{$rw['od_id']}</a>
                        <span class=\"idtime_time\">" . substr($rw['od_time'], 2, 8) . "</span>
                    </div>";
                }
                $mypageTable .= "
                <div class=\"inquiry_info\">
					<div class=\"inquiry_name\">
						<a href=\"{$href}\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a>
					</div>
					<div class=\"inquiry_price\">" . display_price($rw['use_price']) . "</div>
					<div class=\"inquiry_inv\">
						<span class=\"inv_status\">{$gw_status[$rw['dan']]}</span>
						<span class=\"inv_inv\">{$delivery_str}</span>
					</div>
				</div>";
                $k++;
            }
            $mypageTable .= '</li>';
            $i++;
        }
        if ($i == 0) {
            $mypageTable .= '<li class="empty_list">주문 내역이 없습니다.</li>';
        }
        $sql = " select * from shop_wish a, shop_goods b where a.mb_id = ? and a.gs_id = b.index_no order by a.wi_id desc limit 0, 3 ";
        $param = array($member['id']);
        $result = $this->query_model->returnArr($sql, $param);
        $i = 0;
        $wishList = "";
        foreach ($result as $key => $row) {
            $image_w = 50;
            $image_h = 50;
            $image = $this->funcomplex_lib->get_it_image($row['gs_id'], $row['simg1'], $image_w, $image_h, true);
            $list_left_pad = $image_w + 10;
            $wishList .= "
            <li style=\"padding-left:{$list_left_pad}\">
                <div class=\"wish_img\">{$image}</div>
                <div class=\"wish_info\"><a href=\"" . base_url() . "m/shop/view?index_no={$row['gs_id']}\">" . stripslashes($row['gname']) . "</a></div>
                <span class=\"info_date\">보관일 " . substr($row['wi_time'], 2, 8) . "</span>
            </li>";
            $i++;
        }
        if ($i == 0) {
            $wishList .= '<li class="empty_list">보관 내역이 없습니다.</li>';
        }

        $data = array(
            'cp_countTag' => $cp_countTag,
            'mbPoint' => $mbPoint,
            'mbmileage' => $mbmileage,
            'mbTel' => $mbTel,
            'mbEmail' => $mbEmail,
            'member' => $member,
            'mbAddr' => $mbAddr,
            'mypageTable' => $mypageTable,
            'wishList' => $wishList
        );

        $this->load->view('mb/shop/mypage', $data);
    }
    #wish 위시리스트
    public function wish()
    {
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if (!$is_member)
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);
        $tb['title'] = "찜한상품";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->wishSkin();
        $this->template->mb_footer();
    }
    private function wishSkin()
    {
        $member = $this->load->get_var('member');
        $sql  = " select a.wi_id, a.wi_time, a.gs_id, b.* from shop_wish a left join shop_goods b ON ( a.gs_id = b.index_no ) where a.mb_id = ?
            order by a.wi_id desc ";
        $param = array($member['id']);
        $resultObj = $this->query_model->returnQueryObj($sql, $param);
        $wish_count = $resultObj->num_rows();
        $result = $resultObj->result_array();
        $wishTable = $this->wishTable($wish_count, $result);
        $data = array('member' => $member, 'wish_count' => $wish_count, 'wishTable' => $wishTable);
        $this->load->view('mb/shop/wish', $data);
    }
    private function wishTable($wish_count, $result)
    {
        $returnStr = "";
        if (!$wish_count) {
            $returnStr .= "<p class=\"empty_list\">보관함이 비었습니다.</p>";
        } else {
            $returnStr .= "
            <div class=\"ws_wrap\">
                <table>
                    <tbody>";
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
                $returnStr .= "
                    <tr>
                        <th>";
                if ($this->funcomplex_lib->is_soldout($row['gs_id'])) {
                    $returnStr .= "
                                <span class=\"fc_red tx_small\">품절</span>";
                } else {
                    $returnStr .= "
                                <input type=\"checkbox\" name=\"chk_gs_id[{$i}]\" value=\"1\" id=\"ct_chk_{$i}\" onclick=\"out_cd_check(this, '{$out_cd}');\" class=\"css-checkbox\"><label for=\"ct_chk_{$i}\" class=\"css-label\"></label>";
                }
                $returnStr .= "
                            <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$row['gs_id']}\">
                            <input type=\"hidden\" name=\"io_type[{$row['gs_id']}][0]\" value=\"0\">
                            <input type=\"hidden\" name=\"io_id[{$row['gs_id']}][0]\" value=\"\">
                            <input type=\"hidden\" name=\"io_value[{$row['gs_id']}][0]\" value=\"{$row['gname']}\">
                            <input type=\"hidden\" name=\"ct_qty[{$row['gs_id']}][0]\" value=\"1\">
                        </th>
                        <td class=\"wish_img\"><a href=\"" . base_url() . "m/shop/view?index_no={$row['gs_id']}\">" . $this->funcomplex_lib->get_it_image($row['gs_id'], $row['simg1'], 60, 60) . "</a></td>
                        <td class=\"wish_info\">
                            <div class=\"wish_gname\">
                                <a href=\"" . base_url() . "m/shop/view?index_no={$row['gs_id']}\">" . cut_str($row['gname'], 60) . "</a>
                                <div class=\"bold mart5\">" . $this->funcomplex_lib->mobile_price($row['gs_id']) . "</div>
                            </div>
                            <div class=\"wish_del\"><a href=\"" . base_url() . "m/process/wishupdate?w=d&wi_id={$row['wi_id']}\" class=\"btn_small grey\">삭제</a></div>
                        </td>
                    </tr>";
                $i++;
            }
            $returnStr .= "
                    </tbody>
                </table>
            </div>
        
            <div class=\"btn_confirm\">
                <button type=\"button\" onclick=\"return fwishlist_check(document.fwishlist,'');\" class=\"btn_medium bx-white\">장바구니 담기</button>
                <button type=\"button\" onclick=\"return fwishlist_check(document.fwishlist,'direct_buy');\" class=\"btn_medium wset\">상품 주문하기</button>
            </div>";
        }
        return $returnStr;
    }
    #orderinquiry 주문내역 보기
    public function orderinquiry()
    {
        $tb['title'] = '주문/배송조회';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->orderinquirySkin();
        $this->template->mb_footer();
    }
    private function orderinquirySkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');
        $urlencode = $this->load->get_var('urlencode');

        $page = $this->input->post('page');
        $od_id = $this->input->post('od_id');
        $od_pwd = $this->input->post('od_pwd');
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
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);

        // 테이블의 전체 레코드수만 얻음
        $sql = "select count(DISTINCT od_id) as cnt " . $sql_common;
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        // 비회원 주문확인시 비회원의 모든 주문이 다 출력되는 오류 수정
        // 조건에 맞는 주문서가 없다면
        if ($total_count == 0) {
            if ($is_member) // 회원일 경우는 메인으로 이동
                alert('주문이 존재하지 않습니다.', base_url() . 'm/main');
            else // 비회원일 경우는 이전 페이지로 이동
                alert('주문이 존재하지 않습니다.');
        }

        $rows = 10;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        // 비회원 주문확인의 경우 바로 주문서 상세조회로 이동
        if (!$is_member) {
            $sql = " select od_id, od_time, od_ip from shop_order where od_id = ? and od_pwd = ?";
            $param = array($od_id, $od_pwd);
            $row = $this->query_model->returnOneArr($sql, $param);
            if ($row['od_id']) {
                $uid = md5($row['od_id'] . $row['od_time'] . $row['od_ip']);
                set_session('ss_orderview_uid', $uid);
                goto_url(base_url() . 'm/shop/orderinquiryview?od_id=' . $row['od_id'] . '&uid=' . $uid);
            }
        }

        $sql_order = " group by od_id order by index_no desc ";

        $sql = " select * $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $orderTable = $this->orderinquiryTable($result);
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?page=');
        $data = array('total_count' => $total_count, 'orderTable' => $orderTable, 'pagination' => $pagination);
        $this->load->view('mb/shop/orderinquiry', $data);
    }
    private function orderinquiryTable($result)
    {
        $gw_status = $this->config->item('gw_status');
        $returnStr = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $returnStr .= "<li>";
            $sql = " select * from shop_cart where od_id = '$row[od_id]' ";
            $sql .= " order by io_type asc, index_no asc ";
            $res = $this->query_model->returnArr($sql);
            $k = 0;
            foreach ($res as $key => $ct) {
                $rw = $this->global_lib->get_order($ct['od_no']);
                $gs = unserialize($rw['od_goods']);
                $href = base_url() . 'm/shop/view?index_no=' . $rw['gs_id'];
                $dlcomp = explode('|', trim($rw['delivery']));
                $delivery_str = '';
                if ($dlcomp[0] && $rw['delivery_no']) {
                    $delivery_str = $this->funcomplex_lib->get_text($dlcomp[0]) . ' ' . $this->funcomplex_lib->get_text($rw['delivery_no']);
                }
                $uid = md5($rw['od_id'] . $rw['od_time'] . $rw['od_ip']);
                if ($k == 0) {
                    $returnStr .= "
                    <div class=\"inquiry_idtime\">
                        <a href=\"" . base_url() . "m/shop/orderinquiryview?od_id={$rw['od_id']}&uid={$uid}\" class=\"idtime_link\">{$rw['od_id']}</a>
                        <span class=\"idtime_time\">" . substr($rw['od_time'], 2, 8) . "</span>
                    </div>";
                }
                $returnStr .= "
                <div class=\"inquiry_info\">
                    <div class=\"inquiry_name\">
                        <a href=\"{$href}\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a>
                    </div>
                    <div class=\"inquiry_price\">" . display_price($rw['use_price']) . "</div>
                    <div class=\"inquiry_inv\">
                        <span class=\"inv_status\">" . $gw_status[$rw['dan']] . "</span>
                        <span class=\"inv_inv\">{$delivery_str}</span>
                    </div>
                </div>";
                $k++;
            }
            $i++;
            $returnStr .= '</li>';
        }
        if ($i == 0) {
            $returnStr .= '<li class="empty_list">주문 내역이 없습니다.</li>';
        }
        return $returnStr;
    }
    #orderinquiryview 주문내역 상세보기
    public function orderinquiryview()
    {
        $tb['title'] = '주문상세내역';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->orderinquiryviewSkin();
        $this->template->mb_footer();
    }
    private function orderinquiryviewSkin()
    {
        $default = $this->load->get_var('default');
        $member = $this->load->get_var('member');
        $is_member = $this->load->get_var('is_member');
        $od_id = $this->input->get('od_id');

        if (!$is_member) {
            if (get_session('ss_orderview_uid') != $_GET['uid'])
                alert("직접 링크로는 주문서 조회가 불가합니다.\\n\\n주문조회 화면을 통하여 조회하시기 바랍니다.", base_url() . 'm/main');
        }
        $sql = "select * from shop_order where od_id = ?";
        $param = array($od_id);
        $od = $this->query_model->returnOneArr($sql, $param);
        if (!$od['od_id'] || (!$is_member && md5($od['od_id'] . $od['od_time'] . $od['od_ip']) != get_session('ss_orderview_uid')) || $od['mb_id'] != $member['id']) {
            alert("조회하실 주문서가 없습니다.");
        }
        // LG 현금영수증 JS
        $LgJs = "";
        if ($od['od_pg'] == 'lg') {
            if ($default['de_card_test']) {
                $LgJs = '<script language="JavaScript" src="http://pgweb.uplus.co.kr:7085/WEB_SERVER/js/receipt_link.js"></script>';
            } else {
                $LgJs = '<script language="JavaScript" src="http://pgweb.uplus.co.kr/WEB_SERVER/js/receipt_link.js"></script>';
            }
        }
        $stotal = $this->global_lib->get_order_spay($od_id); // 총계
        // 결제정보처리
        $app_no_subj = '';
        $disp_bank = true;
        $disp_receipt = false;
        $easy_pay_name = '';
        if ($od['paymethod'] == '신용카드' || $od['paymethod'] == 'KAKAOPAY' || $od['paymethod'] == '삼성페이') {
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
        set_session('ss_token', $token);
        $st_count1 = $st_count2 = $st_cancel_price = 0;
        $custom_cancel = false;
        $orderObj = $this->orderinquirtyTable($od_id);
        $custom_cancel = $orderObj['custom_cancel'];
        $st_cancel_price = $orderObj['st_cancel_price'];


        $sql = "SELECT * FROM shop_cart WHERE od_id = ? ORDER BY index_no";
        $param = array($od_id);
        $result = $this->query_model->returnArr($sql, $param);
        $orderTb = "";
        $gw_status = $this->config->item('gw_status');
        foreach ($result as $key => $row) {
            $rw = $this->global_lib->get_order($row['od_no']);
            $gs = unserialize($rw['od_goods']);
            $gs = $this->global_lib->get_goods($gs['index_no']);

            $hash = md5($rw['gs_id'] . $rw['od_no'] . $rw['od_id']);
            $dlcomp = explode('|', trim($rw['delivery']));
            $href = base_url() . 'm/shop/view?index_no=' . $rw['gs_id'];

            $it_name = "";
            $it_options = $this->funcomplex_lib->mobile_print_complete_options($row['gs_id'], $row['od_no']);
            if ($it_options) {
                $it_name = '<div class="li_name_od">' . $it_options . '</div>';
            }

            $li_btn = '';
            if ($rw['dan'] == 5) {
                if (is_null_time($rw['user_date']))
                    $li_btn .= '<a href="javascript:final_confirm(\'' . $hash . '\');" class="btn_ssmall red">구매확정</a>';
                if ($rw['review_flag'] == 1 && $rw['user_ok'] == 1)
                    $li_btn .= '<a href="' . base_url() . "m/shop/orderreview?gs_id={$rw['gs_id']}&od_id={$od_id}&od_no={$rw['od_no']}" . '"onclick="win_open(this, \'winorderreview\');return false;" class="btn_ssmall bx-white">구매후기</a>';
            }

            if (isset($dlcomp[1]) && $rw['delivery_no']) {
                $li_btn .= get_delivery_inquiry($rw['delivery'], $rw['delivery_no'], 'btn_ssmall bx-white');
            }

            if ($li_btn)
                $li_btn = '<div class="li_btn">' . $li_btn . '</div>';
            $orderTb .= "
            <li class=\"sod_li\">
            <div class=\"li_opt\"><a href=\"{$href}\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></div>
            {$it_name} {$li_btn}
            <div class=\"li_prqty\">
                <span class=\"prqty_price li_prqty_sp\"><span>상품금액 </span>" . number_format($rw['goods_price']) . "</span>
                <span class=\"prqty_qty li_prqty_sp\"><span>수량 </span>" . number_format($rw['sum_qty']) . "</span>
                <span class=\"prqty_sc li_prqty_sp\"><span>배송비 </span>" . number_format($rw['baesong_price']) . "</span>
                <span class=\"prqty_stat li_prqty_sp\"><span>상태 </span>" . $gw_status[$rw['dan']] . "</span>
            </div>
            <div class=\"li_total\" style=\"padding-left:60px;height:auto !important;height:50px;min-height:50px;\">
                <a href=\"{$href}\" class=\"total_img\">" . $this->funcomplex_lib->get_od_image($rw['od_id'], $gs['simg1'], 50, 50) . "</a>
                <span class=\"total_price total_span\"><span>결제금액 </span>" . number_format($rw['use_price']) . "</span>
                <span class=\"total_point total_span\"><span>적립포인트 </span>" . number_format($rw['sum_point']) . "</span>
            </div>";
            if ($dlcomp[0] && $rw['delivery_no']) {
                $orderTb .= "
                <div class=\"li_dvr\">
                    <strong class=\"fc_107\">배송정보</strong>
                    {$dlcomp[0]}(송장번호 : {$rw['delivery_no']})
                </div>";
            }
            $orderTb .= "
            </li> ";
            $st_count1++;
            if (in_array($rw['dan'], array('1', '2', '3')))
                $st_count2++;
            $st_cancel_price += $rw['cancel_price'];
        }
        $stotalPrice = display_price($stotal['price']);
        $stotalCoupon = "";
        if ($stotal['coupon']) {
            $stotalCoupon = "
            <dt class=\"sod_bsk_dvr\"><span>쿠폰할인</span></dt>
            <dd class=\"sod_bsk_dvr\"><strong>" . display_price($stotal['coupon']) . "</strong></dd>";
        }
        $stotalUsept = "";
        if ($stotal['usepoint']) {
            $stotalUsept = "
            <dt class=\"sod_bsk_dvr\"><span>포인트결제</span></dt>
            <dd class=\"sod_bsk_dvr\"><strong>" . display_point($stotal['usepoint']) . "</strong></dd>";
        }
        $stotalUseMile = "";
        // if ($stotal['usemileage']) {
        //     $stotalUseMile = "
        //     <dt class=\"sod_bsk_dvr\"><span>예치금결제</span></dt>
        //     <dd class=\"sod_bsk_dvr\"><strong>" . display_point($stotal['usemileage']) . "</strong></dd>";
        // }
        $stotalBaesong = "";
        if ($stotal['baesong']) {
            $stotalBaesong = "
            <dt class=\"sod_bsk_dvr\"><span>배송비</span></dt>
            <dd class=\"sod_bsk_dvr\"><strong>" . display_price($stotal['baesong']) . "</strong></dd>";
        }

        $stoalGoodsPrice = display_price($stotal['price']);
        $stoalPrice = display_price($stotal['price'] + $stotal['baesong']);
        $stoalPoint = display_point($stotal['point']);
        $stotalUsePrice = display_price($stotal['useprice']);
        $stotalCancelPrice = display_price($stotal['cancel']);
        $payMethod = ($easy_pay_name ? $easy_pay_name . '(' . $od['paymethod'] . ')' : $od['paymethod']);
        $refund_mileage_price = display_price($stotal['refund_mileage_price']);
        $use_price = display_price($stotal['useprice']);

        $payDate = "";
        if (!is_null_time($od['receipt_time'])) {
            $payDate = "
			<tr>
				<th scope=\"row\">결제일시</th>
				<td>{$od['receipt_time']}</td>
			</tr>";
        }

        $app_no_subjTag = "";
        // 승인번호, 휴대폰번호, 거래번호
        if ($app_no_subj) {
            $app_no_subjTag = "
            <tr>
                <th scope=\"row\">{$app_no_subj}</th>
                <td>{$app_no}</td>
            </tr>";
        }
        // 계좌정보
        $disp_bankTag = "";
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
        if (!($od['paymethod'] == '무통장' || $od['paymethod'] == '포인트')) { #무통장이면 안 나오게 수정
            if ($disp_receipt) {
                include_once('./Bill/iamport.php');
                $merchant_uid = $od['merchant_uid'];
                $result = $iamport->findByMerchantUID($merchant_uid); //IamportResult 를 반환(success, data, error)
                $tid = $result->data->pg_tid;
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
        $taxsaveTag = "";
        // 현금영수증 발급을 사용하는 경우에만
        #현금영수증 발급을 안 하는 것으로 우선 변경
        if ($default['de_taxsave_use'] && false) {
            // 미수금이 없고 현금일 경우에만 현금영수증을 발급 할 수 있습니다.
            if (!is_null_time($od['receipt_time']) && ($od['paymethod'] == '무통장' || $od['paymethod'] == '계좌이체' || $od['paymethod'] == '가상계좌')) {
                $taxsaveTag .= "
                <tr>
                    <th scope=\"row\">현금영수증</th>
                    <td>";
                if ($od['od_cash']) {
                    if ($od['od_pg'] == 'inicis') {
                        $cash = unserialize($od['od_cash_info']);
                        $cash_receipt_script = 'window.open(\'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/Cash_mCmReceipt.jsp?noTid=' . $tid . '&clpaymethod=22\',\'showreceipt\',\'width=380,height=540,scrollbars=no,resizable=no\');';
                    }
                    $taxsaveTag .= "<a href=\"javascript:;\" onclick=\"{$cash_receipt_script}\" class=\"btn_small\">현금영수증 확인하기</a>";
                } else {
                    $taxsaveTag .= "<a href=\"javascript:;\" onclick=\"window.open('" . base_url() . "shop/taxsave?od_id={$od_id}', 'taxsave', 'width=550,height=400,scrollbars=1,menus=0');\" class=\"btn_small\">현금영수증 발급하기</a>";
                }
                $taxsaveTag .= "
                    </td>
                </tr>";
            }
        }
        #주문하신 분
        $odName = $this->funcomplex_lib->get_text($od['name']);
        $odCell = $this->funcomplex_lib->get_text($od['cellphone']);
        $odAddr = $this->funcomplex_lib->get_text(sprintf("(%s)", $od['zip']) . ' ' . $this->funcomplex_lib->print_address($od['addr1'], $od['addr2'], $od['addr3'], $od['addr_jibeon']));
        $odEmail = $this->funcomplex_lib->get_text($od['email']);

        #받으시는 분
        $odBName = $this->funcomplex_lib->get_text($od['b_name']);
        $odBCell = $this->funcomplex_lib->get_text($od['b_cellphone']);
        $odMemo = "";
        if ($od['memo']) {
            $odMemo = "
			<tr>
				<th scope=\"row\">전하실 말씀</th>
				<td>" . $this->funcomplex_lib->conv_content($od['memo'], 0) . "</td>
			</tr>";
        }
        $odBAddr = $this->funcomplex_lib->get_text(sprintf("(%s)", $od['b_zip']) . ' ' . $this->funcomplex_lib->print_address($od['b_addr1'], $od['b_addr2'], $od['b_addr3'], $od['b_addr_jibeon']));

        // 취소한 내역이 없다면
        $not_cancelTag = "";
        if (($gs['type'] == "timesale" && $gs['isopen'] == 1 && $st_cancel_price == 0 && $custom_cancel) || ($gs['type'] == "normal" && $st_cancel_price == 0 && $custom_cancel)) {
            $not_cancelTag = "
            <section id=\"sod_fin_cancel\">
                <h2>주문취소</h2>
                <button type=\"button\" onclick=\"document.getElementById('sod_fin_cancelfrm').style.display='block';\" class=\"btn_medium wset\">주문 취소하기</button>

                <div id=\"sod_fin_cancelfrm\">
                    <form method=\"post\" action=\"" . base_url() . "m/process/orderinquirycancel\" onsubmit=\"return fcancel_check(this);\">
                    <input type=\"hidden\" name=\"od_id\"  value=\"{$od_id}\">
                    <input type=\"hidden\" name=\"token\"  value=\"{$token}\">
                    <input type=\"hidden\" name=\"type\"  value=\"{$gs['type']}\">
                    <label for=\"cancel_memo\">취소사유</label>
                    <input type=\"text\" name=\"cancel_memo\" id=\"cancel_memo\" required class=\"frm_input required\" maxlength=\"100\">
                    <input type=\"submit\" value=\"확인\" class=\"btn_small\">
                    </form>
                </div>
            </section>";
        }

        $data = array(
            'LgJs' => $LgJs,
            'token' => $token,
            'od_id' => $od_id,
            'od' => $od,
            'gs' => $gs,
            'orderTb' => $orderTb,
            'stotalPrice' => $stotalPrice,
            'stotalCoupon' => $stotalCoupon,
            'stotalUsept' => $stotalUsept,
            'stotalUseMile' => $stotalUseMile,
            'stotalBaesong' => $stotalBaesong,
            'stoalPrice' => $stoalPrice,
            'stoalGoodsPrice' => $stoalGoodsPrice,
            'stotalCancelPrice' => $stotalCancelPrice,
            'refund_mileage_price' => $refund_mileage_price,
            'use_price' => $use_price,
            'stoalPoint' => $stoalPoint,
            'payMethod' => $payMethod,
            'stotalUsePrice' => $stotalUsePrice,
            'payDate' => $payDate,
            'app_no_subjTag' => $app_no_subjTag,
            'disp_bankTag' => $disp_bankTag,
            'disp_receiptTag' => $disp_receiptTag,
            'taxsaveTag' => $taxsaveTag,
            'odName' => $odName,
            'odCell' => $odCell,
            'odAddr' => $odAddr,
            'odEmail' => $odEmail,
            'odBName' => $odBName,
            'odBCell' => $odBCell,
            'odBAddr' => $odBAddr,
            'odMemo' => $odMemo,
            'not_cancelTag' => $not_cancelTag
        );
        $this->load->view('mb/shop/orderinquiryview', $data);
    }
    private function orderinquirtyTable($od_id)
    {
        $gw_status = $this->config->item('gw_status');
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
        $returnObj = array('st_cancel_price' => $st_cancel_price, 'custom_cancel' => $custom_cancel);
        return $returnObj;
    }

    #review 리뷰
    public function review()
    {
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if (!$is_member)
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);

        $tb['title'] = "구매후기";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->reviewSkin();
        $this->template->mb_footer();
    }

    private function reviewSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $default = $this->load->get_var('default');
        $member = $this->load->get_var('member');
        $pt_id = $this->load->get_var('pt_id');

        $page = $this->input->get('page');

        $sql_common = " from shop_goods_review ";
        $sql_search = " where (left(seller_id,3) = 'AP-' or seller_id = 'admin' or seller_id = '$pt_id') ";
        $addquery = "";
        if (!$this->gvcomplex_lib->is_admin() || $this->gvcomplex_lib->is_seller($member['id'])) {
            $addquery = "and mb_id ='" . $member['id'] . "'";
        }
        if ($default['de_review_wr_use']) {
            $sql_search .= " and pt_id = '$pt_id' ";
        }
        $sql_order = "order by index_no desc ";
        $sql = "select count(*) as cnt $sql_common $sql_search $addquery";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = (int)$row['cnt'];

        $rows = 10;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = "select * $sql_common $sql_search $addquery $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $reviewTable = $this->reviewTable($total_count, $result);
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?page=');

        $data = array(
            'total_count' => $total_count,
            'reviewTable' => $reviewTable,
            'pagination' => $pagination
        );

        $this->load->view('mb/bbs/review', $data);
    }

    private function reviewTable($total_count, $result)
    {
        $returnStr = "";
        $gw_star = $this->config->item('gw_star');
        if (!$total_count) {
            $returnStr = "<p class=\"empty_list\">구매후기가 없습니다.</p>";
        } else {
            $returnStr .= "
            <table>
                <colgroup>
                    <col width=\"80px\">
                    <col>
                </colgroup>
                <tbody>";
            foreach ($result as $key => $row) {
                $href = base_url() . 'm/shop/view?index_no=' . $row['gs_id'];
                $gs = $this->global_lib->get_goods($row['gs_id'], 'gname, simg1');

                if (!empty($gs)) {
                    $gname = $gs['gname'];
                } else {
                    $gname = "삭제된 상품입니다";
                }

                $wr_star = $gw_star[$row['score']];
                $wr_id   = substr($row['mb_id'], 0, 3) . str_repeat("*", strlen($row['mb_id']) - 3);
                $wr_time = substr($row['reg_time'], 0, 10);

                $returnStr .= "
                    <tr>
                        <td class=\"image\"><a href=\"{$href}\">" . $this->funcomplex_lib->get_it_image($row['gs_id'], $gs['simg1'], 80, 80) . "</a></td>
                        <td class=\"gname\">";

                if (!empty($gs)) {
                    $returnStr .= "<p><a href=\"{$href}\" target=\"_blank\">" . cut_str($gname, 55) . "</a></p>";
                } else {
                    $returnStr .= "<p>" . cut_str($gname, 55) . "</p>";
                }
                $returnStr .= "
                            <p class=\"mart3 fc_999\">" . cut_str($row['memo'], 100) . "</p>
                            <p class=\"mart5\"><span class=\"fc_255\">{$wr_star}</span><span class=\"fc_999\"> / {$wr_id} / {$wr_time}</span></p>
                        </td>";
                if (!empty($gs)) {
                    $returnStr .= "<td><a href='" . base_url() . "m/shop/orderreview?gs_id={$row['gs_id']}&od_id={$row['od_id']}&od_index={$row['od_index']}&w=u " . " onclick=\"win_open(this, 'winorderreview');return false;\" class=\"btn_ssmall bx-white\">수정</a></td>";
                } else {
                    $returnStr .= "<td><a href=\"#\" onclick=\"alert('삭제된 상품으로 리뷰 수정이 불가능합니다.');\" class=\"btn_ssmall bx-white\">수정</a></td>";
                }
                $returnStr .= "
                </tr>";
            }
        }
        return $returnStr;
    }

    #포인트 조회
    public function point()
    {
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if (!$is_member)
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);
        $tb['title'] = '포인트조회';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->pointSkin();
        $this->template->mb_footer();
    }

    private function pointSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $page = $this->input->get('page');

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');
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

        $rows = 15;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $sum_point1 = $sum_point2 = 0;
        $i = 0;
        $pointTb = "";
        foreach ($result as $key => $row) {
            $point1 = $point2 = 0;
            if ($row['po_point'] > 0) {
                $point1 = '+' . number_format($row['po_point']);
                $sum_point1 += $row['po_point'];
            } else {
                $point2 = number_format($row['po_point']);
                $sum_point2 += $row['po_point'];
            }

            $pointTb .= "
            <li>
                <div class=\"point_wrap01\">
                    <span class=\"point_date\">" . conv_date_format('y-m-d H시', $row['po_datetime']) . "</span>
                    <span class=\"point_log\">{$row['po_content']}</span>
                </div>
                <div class=\"point_wrap02\">
                    <span class=\"point_inout\">";
            if ($point1) {
                $pointTb .= $point1;
            } else {
                $pointTb .= $point2;
            }
            $pointTb .= "
                    </span>
                </div>
            </li>";
            $i++;
        }
        if ($i == 0) {
            $pointTb .= '<li class="empty_list">자료가 없습니다.</li>';
        } else {
            if ($sum_point1 > 0)
                $sum_point1 = "+" . number_format($sum_point1);
            $sum_point2 = number_format($sum_point2);
        }
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?page=');

        $data = array(
            'total_count' => $total_count,
            'pointTb' => $pointTb,
            'sum_point1' => $sum_point1,
            'sum_point2' => $sum_point2,
            'sum_point3' => $sum_point3,
            'member' => $member,
            'pagination' => $pagination
        );
        $this->load->view('mb/shop/point', $data);
    }

    #마일리지 조회
    public function mileage()
    {
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if (!$is_member)
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);
        $tb['title'] = '포인트조회';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->mileageSkin();
        $this->template->mb_footer();
    }

    private function mileageSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $page = $this->input->get('page');

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');
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

        $rows = 15;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $sum_point1 = $sum_point2 = 0;
        $i = 0;
        $pointTb = "";
        foreach ($result as $key => $row) {
            $point1 = $point2 = 0;
            if ($row['po_point'] > 0) {
                $point1 = '+' . number_format($row['po_point']);
                $sum_point1 += $row['po_point'];
            } else {
                $point2 = number_format($row['po_point']);
                $sum_point2 += $row['po_point'];
            }

            $pointTb .= "
            <li>
                <div class=\"point_wrap01\">
                    <span class=\"point_date\">" . conv_date_format('y-m-d H시', $row['po_datetime']) . "</span>
                    <span class=\"point_log\">{$row['po_content']}</span>
                </div>
                <div class=\"point_wrap02\">
                    <span class=\"point_inout\">";
            if ($point1) {
                $pointTb .= $point1;
            } else {
                $pointTb .= $point2;
            }
            $pointTb .= "
                    </span>
                </div>
            </li>";
            $i++;
        }
        if ($i == 0) {
            $pointTb .= '<li class="empty_list">자료가 없습니다.</li>';
        } else {
            if ($sum_point1 > 0)
                $sum_point1 = "+" . number_format($sum_point1);
            $sum_point2 = number_format($sum_point2);
        }
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?page=');

        $data = array(
            'total_count' => $total_count,
            'pointTb' => $pointTb,
            'sum_point1' => $sum_point1,
            'sum_point2' => $sum_point2,
            'sum_point3' => $sum_point3,
            'member' => $member,
            'pagination' => $pagination
        );
        $this->load->view('mb/shop/point', $data);
    }

    #gift 쿠폰인증화면
    public function gift()
    {
        $config = $this->load->get_var('config');
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if (!$config['gift_yes']) {
            alert("쿠폰사용이 중지 되었습니다.");
        }
        if (!$is_member) {
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);
        }
        $tb['title'] = "쿠폰인증";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->giftSkin();
        $this->template->mb_footer();
    }
    private function giftSkin()
    {
        $member = $this->load->get_var('member');
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        $sql = " select * from shop_gift where mb_id = ? order by no desc ";
        $param = array($member['id']);
        $resultObj = $this->query_model->returnQueryObj($sql, $param);
        $result = $resultObj->result_array();
        $giftTable = $this->giftTable($result);
        $total_count = $resultObj->num_rows();
        $form_action_url = base_url() . 'm/process/gift_update';
        $data = array('total_count' => $total_count, 'form_action_url' => $form_action_url, 'token' => $token, 'giftTable' => $giftTable);
        $this->load->view('mb/shop/gift', $data);
    }
    private function giftTable($result)
    {
        $returnStr = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $cp_date = substr($row['gr_sdate'], 2, 8) . ' ~ ' . substr($row['gr_edate'], 2, 8);
            $returnStr .= "
            <li>
                <div class=\"li_title\">" . $this->funcomplex_lib->get_text($row['gr_subject']) . "</div>
                <div class=\"li_pd\">
                    <span class=\"pd_price\">" . display_point($row['gr_price']) . "</span>
                    <span class=\"pd_date\">{$cp_date}</span>
                </div>
                <div class=\"li_target\">" . $this->funcomplex_lib->get_text($row['gi_num']) . "</div>
            </li>";
            $i++;
        }
        if ($i == 0)
            $returnStr .= '<li class="empty_list">내역이 없습니다.</li>';
        return $returnStr;
    }
    #coupon 쿠폰관리
    public function coupon()
    {
        $config = $this->load->get_var('config');
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if (!$config['coupon_yes']) {
            alert("쿠폰사용이 중지 되었습니다.");
        }

        if (!$is_member) {
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);
        }

        $tb['title'] = '쿠폰관리';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->couponSkin();
        $this->template->mb_footer();
    }
    private function couponSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $sca = $this->input->get('sca');
        $page = $this->input->get('page');

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');
        $qstr = $this->load->get_var('qstr');

        $sql_common = " from shop_coupon_log ";
        $sql_search = " where mb_id = '{$member['id']}' ";
        $sql_order  = " order by cp_wdate desc ";

        $selected1 = '';
        $selected2 = '';

        if ($sca) {
            // 사용완료 및 기한만료 쿠폰
            $sql_search .= " and mb_use='1' or ( (cp_inv_type='0' and cp_inv_edate != '9999999999' and cp_inv_edate < curdate()) or (cp_inv_type='1' and date_add(`cp_wdate`, interval `cp_inv_day` day) < now()) ) ";

            $selected2 = " class='selected'";
        } else {
            // 사용가능한 쿠폰
            $sql_search .= " and mb_use='0' and ( (cp_inv_type='0' and (cp_inv_edate = '9999999999' or cp_inv_edate > curdate())) or (cp_inv_type='1' and date_add(`cp_wdate`, interval `cp_inv_day` day) > now()) ) ";

            $selected1 = " class='selected'";
        }
        $sql = " select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 5;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $couponTb = $this->couponTable($total_count, $result);


        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?' . $qstr . '&page=');
        $data = array('total_page' => $total_page, 'member' => $member, 'pagination' => $pagination, 'selected1' => $selected1, 'selected2' => $selected2, 'couponTb' => $couponTb);
        $this->load->view('mb/shop/coupon', $data);
    }
    private function couponTable($total_count, $result)
    {
        $u_part = array();
        $u_part[0] = "전체상품";
        $u_part[1] = "일부 상품 사용가능";
        $u_part[2] = "일부 카테고리 사용가능";
        $u_part[3] = "일부 상품 사용불가";
        $u_part[4] = "일부 카테고리 사용불가";

        $returnStr = "";
        if (!$total_count) {
            $returnStr .= "<p class=\"empty_list\">자료가 없습니다.</p>";
        } else {
            $returnStr .= "
            <div class=\"my_list\">
                <table class=\"my_box\">
                    <colgroup>
                        <col width=\"50%\">
                        <col width=\"50%\">
                    </colgroup>
                    <tbody>";
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
                    else $cp_inv_sdate = $row['cp_inv_sdate'];

                    if ($row['cp_inv_edate'] == '9999999999') $cp_inv_edate = '무제한';
                    else $cp_inv_edate = $row['cp_inv_edate'];

                    if ($row['cp_inv_sdate'] == '9999999999' && $row['cp_inv_edate'] == '9999999999')
                        $inv_date = '무제한';
                    else
                        $inv_date = $cp_inv_sdate . " ~ " . $cp_inv_edate;
                } else {
                    $inv_date = '다운로드 후 ' . $row['cp_inv_day'] . '일간';
                }
                $returnStr .= "
                        <tr class=\"tit\">
                            <td class=\"mi_dt tal bold\">{$row['cp_wdate']}</td>
                            <td class=\"mi_at tar\">{$u_part[$row['cp_use_part']]}</td>
                        </tr>
                        <tr>
                            <td class=\"mi_dt tal\" colspan=\"2\">" . $this->funcomplex_lib->get_text($row['cp_subject']) . "</td>
                        </tr>
                        <tr>
                            <td class=\"mi_dt tal\">할인금액(율)</td>
                            <td class=\"mi_at tar\">{$sale_amt}</td>
                        </tr>
                        <tr>
                            <td class=\"mi_dt tal\">사용기한</td>
                            <td class=\"mi_at tar\">{$inv_date}</td>
                        </tr>";
            }
            $returnStr .= "
                    </tbody>
                </table>
            </div>";
        }
        return $returnStr;
    }
    #최근본상품
    public function today()
    {
        $tb['title'] = "최근본상품";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->todaySkin();
        $this->template->mb_footer();
    }
    private function todaySkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $pr_idx = get_session("ss_pr_idx");
        $page = $this->input->get('page');
        $total_count = 0;
        $total_page = 0;
        $result = array();
        if ($pr_idx) {
            $sql_common = " from shop_goods ";
            $sql_search = " where index_no IN ({$pr_idx}) ";
            $sql_order = "order by index_no desc ";

            $sql = " select count(*) as cnt $sql_common $sql_search ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            $rows = 10;
            $total_page  = ceil($total_count / $rows); // 전체 페이지 계산
            if ($page == "") {
                $page = 1;
            } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함

            $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
            $result = $this->query_model->returnArr($sql);
        }
        $todayTb = $this->todayTable($total_count, $result);
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?page=');

        $data = array('total_count' => $total_count, 'todayTb' => $todayTb, 'pagination' => $pagination);
        $this->load->view('mb/shop/today', $data);
    }
    private function todayTable($total_count, $result)
    {
        $returnStr = "";
        if (!$total_count) {
            $returnStr .= "<p class=\"empty_list\">최근 본 상품이 없습니다.</p>";
        } else {
            $returnStr .= "
                <div class=\"ws_wrap\">
                    <table>
                        <tbody>";
            foreach ($result as $key => $row) {
                $gs_id = $row['index_no'];
                $it_href = base_url() . 'm/shop/view?index_no=' . $gs_id;
                $returnStr .= "
                            <tr>
                                <td class=\"wish_img\"><a href=\"{$it_href}\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 60, 60) . "</a></td>
                                <td class=\"wish_info\">
                                    <div class=\"wish_gname\">
                                        <a href=\"{$it_href}\">" . cut_str($row['gname'], 80) . "</a>
                                        <div class=\"bold mart5\">" . $this->funcomplex_lib->mobile_price($gs_id) . "</div>
                                    </div>
                                    <div class=\"wish_del\"><a href=\"" . base_url() . "m/process/today?gs_id={$gs_id}\" class=\"btn_small grey\">삭제</a></div>
                                </td>
                            </tr>";
            }
            $returnStr .= "
                        </tbody>
                    </table>
                </div>";
        }
        return $returnStr;
    }
    #orderform
    public function orderform()
    {
        $tb['title'] = '주문서작성';
        $this->load->vars(array('tb' => $tb));

        $this->template->mb_header();
        $this->orderformSkin();
        $this->template->mb_footer();
    }

    private function orderformSkin()
    {
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $set_mb_id = $this->session->userdata['ss_mb_id'];
        $default = $this->load->get_var('default');
        $config = $this->load->get_var('config');
        $pt_id = $this->load->get_var('pt_id');
        $sw_direct = get_session("sw_direct") ?? 0;

        $ss_cart_id = get_session('ss_cart_id');
        if (!$ss_cart_id) {
            alert("주문하실 상품이 없습니다.", base_url() . "m/main");
        }

        set_session('tot_price', '');
        set_session('use_point', '');

        // 새로운 주문번호 생성
        $od_id = $this->global_lib->get_uniqid();
        set_session('ss_order_id', $od_id);

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
        // add_javascript('js 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
        $OM_POSTCODE_JS = "";
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {   //https 통신일때 daum 주소 js
            $OM_POSTCODE_JS = '<script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>';
        } else {  //http 통신일때 daum 주소 js
            $OM_POSTCODE_JS = '<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>';
        }
        $order_action_url = base_url() . 'm/process/orderformupdate';

        #주문서 작성 
        $tot_point = 0;
        $tot_sell_price = 0;
        $tot_opt_price = 0;
        $tot_sell_qty = 0;
        $tot_sell_amt = 0;
        $seller_id = array();
        $is_timesale = array();

        $sql = "SELECT *
                FROM shop_cart
                WHERE index_no IN ({$ss_cart_id}) AND ct_select = '0' AND is_direct='{$sw_direct}' ORDER BY index_no";
        $result = $this->query_model->returnArr($sql);

        $i = 0;
        $item_sendcost = array();
        $content = "";
        $content .= "
        <p>주문하실 상품을 확인하세요.</p>
        <ul class=\"sod_list\">";
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

            $it_name = '<strong>' . stripslashes($gs['gname']) . '</strong>';
            $it_options = $this->funcomplex_lib->print_item_options($row['index_no'], $set_mb_id);
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

            // 포인트
            $point = 0;
            if ($is_member && $gs['is_point'] > 0) {
                $grade = $member['grade'];

                $sql = "SELECT gb_buy_point FROM shop_member_grade WHERE gb_no = ?";
                $param = array($grade);
                $result = $this->query_model->returnOneArr($sql, $param);

                $gb_point = $result['gb_buy_point']; // 적립률
                $point = (int) round((($price + (int)$sell_opt_price) * $sell_qty) * ($gb_point * (1 / 100))); // 적립 포인트
            }

            // 회원이 아니면 포인트초기화
            if (!$is_member) $point = 0;

            // 배송비
            $info = $this->funcomplex_lib->get_item_sendcost($row, $sell_price);
            $item_sendcost[] = $info['pattern'];

            $seller_id[$i] = $gs['mb_id'];
            $seller_code = $gs['mb_id'];

            $get_seller_id_sql = "select mb_id,company_name from shop_seller where seller_code = ?";
            $param = array($seller_code);
            $seller_data = $this->query_model->returnOneArr($get_seller_id_sql, $param);

            $href = base_url() . 'm/shop/view?index_no=' . $row['gs_id'];
            $info['price'] = isset($info['price']) ? $info['price'] : 0;
            $content .= "
            <li class=\"sod_li\">
                <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$row['gs_id']}\">
                <input type=\"hidden\" name=\"type[{$i}]\" value=\"{$gs['type']}\">
                <input type=\"hidden\" name=\"gs_notax[{$i}]\" value=\"{$gs['notax']}\">		
                <input type=\"hidden\" name=\"gs_price[{$i}]\" value=\"{$sell_price}\">
                <input type=\"hidden\" name=\"seller_id[{$i}]\" value=\"{$gs['mb_id']}\">
                <input type=\"hidden\" name=\"seller_real_id[{$i}]\" value=\"{$seller_data['mb_id']}\">
                <input type=\"hidden\" name=\"seller_name[{$i}]\" value=\"{$seller_data['company_name']}\">
                <input type=\"hidden\" name=\"supply_price[{$i}]\" value=\"{$supply_price}\">
                <input type=\"hidden\" name=\"sum_point[{$i}]\" value=\"{$point}\">
                <input type=\"hidden\" name=\"sum_qty[{$i}]\" value=\"{$sell_qty}\">
                <input type=\"hidden\" name=\"cart_id[{$i}]\" value=\"{$row['od_no']}\">
                <input type=\"hidden\" name=\"referral_code[{$i}]\" value=\"{$row['referral_code']}\">
				
                <div class=\"li_name\">{$it_name}
                    <div class=\"li_mod\" style=\"padding-left:100px;\"></div>
                    <span class=\"total_img\">" . $this->funcomplex_lib->get_it_image($row['gs_id'], $gs['simg1'], 80, 80) . "</span>
                </div>
                <div class=\"li_prqty\">
                    <span class=\"prqty_price li_prqty_sp\"><span>판매가</span>" . number_format($sell_amt) . "</span>
                    <span class=\"prqty_qty li_prqty_sp\"><span>수량</span>" . number_format($sell_qty) . "</span>
                    <span class=\"prqty_sc li_prqty_sp\"><span>배송비</span>" . number_format($info['price']) . "</span>
                </div>
                <div class=\"li_total\">
                    <span class=\"total_price total_span\"><span>소계</span>
                    <strong>" . number_format($sell_price) . "</strong></span>
                    <span class=\"total_point total_span\"><span>적립포인트</span>
                    <strong>" . number_format($point) . "</strong></span>
                </div>
            </li>";
            $tot_point += (int)$point;
            $tot_sell_price += (int)$sell_price;
            $tot_opt_price += (int)$sell_opt_price;
            $tot_sell_qty += (int)$sell_qty;
            $tot_sell_amt += (int)$sell_amt;
            $i++;
        }
        // 배송비 검사
        $send_cost = 0;
        $com_send_cost = 0;
        $sep_send_cost = 0;
        $max_send_cost = 0;

        $k = 0;
        $condition = array();
        foreach ($item_sendcost as $key) {
            list($userid, $bundle, $price) = explode('|', $key);
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
        if ($i == 0) {
            alert('장바구니가 비어 있습니다.', base_url() . 'm/shop/cart');
        }
        $content .= "
        </ul>

        <dl id=\"sod_bsk_tot\">
            <dt class=\"sod_bsk_sell\"><span>주문</span></dt>
            <dd class=\"sod_bsk_sell\"><strong>" . number_format($tot_sell_price) . " 원</strong></dd>
            <dt class=\"sod_bsk_dvr\"><span>배송비</span></dt>
            <dd class=\"sod_bsk_dvr\"><strong>" . number_format($tot_send_cost) . " 원</strong></dd>
            <dt class=\"sod_bsk_cnt\"><span>총계</span></dt>
            <dd class=\"sod_bsk_cnt\"><strong>" . number_format($tot_price) . " 원</strong></dd>
            <dt class=\"sod_bsk_point\"><span>포인트</span></dt>
            <dd class=\"sod_bsk_point\"><strong>" . number_format($tot_point) . " P</strong></dd>
        </dl>";

        $usePointTag = "";
        if (!$is_member || !$config['usepoint_yes']) {
            $usePointTag = "
            <input type=\"hidden\" name=\"use_point\" value=\"0\">";
        }
        $isNotMember = "";
        if (!$is_member) { // 비회원이면
            $isNotMember = "
			<tr>
				<th scope=\"row\">비밀번호</th>
				<td>
					<input type=\"password\" name=\"od_pwd\" required class=\"frm_input required\" maxlength=\"20\">
					<span class=\"frm_info\">영,숫자 3~20자 (주문서 조회시 필요)</span>
				</td>
			</tr>";
        }
        $deliverList = "";
        if ($is_member) {
            $deliverList = "
            <br><input type=\"radio\" name=\"ad_sel_addr\" value=\"3\" id=\"sel_addr3\" class=\"css-checkbox lrg\">
            <label for=\"sel_addr3\" class=\"css-label\">배송지목록</label>";
        }

        $escrow_title = "";
        if ($default['de_escrow_use']) {
            $escrow_title = "에스크로 ";
        }

        $multi_settle = '';
        if (in_array(true, $is_timesale)) {
            if ($default['de_card_use']) {
                $multi_settle .= "<option value='신용카드'>신용카드</option>\n";
            }
            if ($default['de_iche_use']) {
                $multi_settle .= "<option value='계좌이체'>계좌이체</option>\n";
            }
        } else {
            // if ($default['de_bank_use']) {
            //     $multi_settle .= "<option value='무통장'>무통장입금</option>\n";
            // }
            if ($default['de_card_use']) {
                $multi_settle .= "<option value='신용카드'>신용카드</option>\n";
            }
            if ($default['de_iche_use']) {
                $multi_settle .= "<option value='계좌이체'>계좌이체</option>\n";
            }
        }

        // if ($is_member && $config['usepoint_yes'] && ($tot_price <= $member['point'])) {
        //     $multi_settle .= "<option value='포인트'>포인트결제</option>\n";
        // }

        // if ($is_member && $config['usemileage_yes'] && ($tot_price <= $member['mileage'])) {
        //     $multi_settle .= "<option value='예치금'>예치금결제</option>\n";
        // }

        // // 이니시스를 사용중일때만 삼성페이 결제가능
        // if ($default['de_samsung_pay_use'] && ($default['de_pg_service'] == 'inicis')) {
        //     $multi_settle .= "<option value='삼성페이'>삼성페이</option>\n";
        // }
        $totPrice = display_price($tot_price);

        $couponTag = "";
        if ($is_member && $config['coupon_yes']) { // 보유쿠폰
            $sp_count = $this->global_lib->get_cp_precompose($member['id']);
            $couponTag = "
            <tr>
                <th scope=\"row\">할인쿠폰</th>
                <td>
                    <span id=\"dc_coupon\"><a href=\"javascript:window.open('" . base_url() . "m/shop/ordercoupon');\" class=\"btn_small bx-red\">사용 가능 쿠폰 {$sp_count[3]}장</a>&nbsp;</span>(-)&nbsp;&nbsp;<strong><span id=\"dc_amt\">0</strong>원&nbsp;<span id=\"dc_cancel\" style=\"display:none;\"><a href=\"javascript:coupon_cancel();\" class=\"btn_small grey\">삭제</a></span></span>
                </td>
            </tr>";
        }
        $pointUseTag = "";
        if ($is_member && $config['usepoint_yes']) {
            $pointUseTag = "
			<tr>
				<th scope=\"row\">포인트결제</th>
				<td>
					<input type=\"text\" name=\"use_point\" value=\"0\" onkeyup=\"calculate_temp_point(this.value); this.value=number_format(this.value);\" onfocus=\"removeLeadingZero(this)\" onblur=\"restoreZeroIfEmpty(this)\" class=\"frm_input w100\"> 원
					<div>잔액 : <b>" . display_point($member['point']) . "</b> (" . display_point($config['usepoint']) . " 부터 사용가능)</div>
				</td>
			</tr>";
        }
        $mileageUseTag = "";
        // if ($is_member && $config['usemileage_yes']) {
        //     $mileageUseTag = "
		// 	<tr>
		// 		<th scope=\"row\">예치금결제</th>
		// 		<td>
		// 			<input type=\"text\" name=\"use_mileage\" value=\"0\" onkeyup=\"calculate_temp_mileage(this.value); this.value=number_format(this.value);\" onfocus=\"removeLeadingZero(this)\" onblur=\"restoreZeroIfEmpty(this)\" class=\"frm_input w100\"> 원
		// 			<div>잔액 : <b>" . display_price($member['mileage']) . "</b> (" . display_point($config['usemileage']) . " 부터 사용가능)</div>
		// 		</td>
		// 	</tr>";
        // }
        $bankTag = $this->funcomplex_lib->mobile_bank_account("bank");

        $noMemberTag = "";
        if (!$is_member) {
            $noMemberTag = "
            <section id=\"guest_privacy\">
                <h2 class=\"anc_tit\">비회원 구매</h2>
                <div class=\"tbl_head01 tbl_wrap\">
                    <table>
                    <thead>
                    <tr>
                        <th>목적</th>
                        <th>항목</th>
                        <th>보유기간</th>
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
        
                <div id=\"guest_agree\">
                    <input type=\"checkbox\" id=\"agree\" value=\"1\" class=\"css-checkbox lrg\">
                    <label for=\"agree\" class=\"css-label\">개인정보 수집 및 이용 내용을 읽었으며 이에 동의합니다.</label>
                </div>
            </section>";
        }
        $company_typeNotJs = "";
        if (!$config['company_type']) {
            $company_typeNotJs = "
            if(getSelectVal(f[\"paymethod\"]) == '무통장' && getSelectVal(f[\"taxsave_yes\"]) == 'Y') {
                check_field(f.tax_hp, \"핸드폰번호를 입력하세요\");
            }
            
            if(getSelectVal(f[\"paymethod\"]) == '무통장' && getSelectVal(f[\"taxsave_yes\"]) == 'S') {
                check_field(f.tax_saupja_no, \"사업자번호를 입력하세요\");
            }
            
            if(getSelectVal(f[\"paymethod\"]) == '무통장' && getSelectVal(f[\"taxbill_yes\"]) == 'Y') {
                check_field(f.company_saupja_no, \"사업자번호를 입력하세요\");
                check_field(f.company_name, \"상호명을 입력하세요\");
                check_field(f.company_owner, \"대표자명을 입력하세요\");
                check_field(f.company_addr, \"사업장소재지를 입력하세요\");
                check_field(f.company_item, \"업태를 입력하세요\");
                check_field(f.company_service, \"종목을 입력하세요\");
            }";
        }
        $taxJs = "";
        if (!$config['company_type']) {
            $taxJs = "
            $(\"#taxsave_section\").show();";
        }
        $taxHideJs = "";
        if (!$config['company_type']) {
            $taxHideJs = '
			$("#taxsave_section").hide();
			$("#taxbill_section").hide();
			$("#taxsave_fld_1").hide();
			$("#taxsave_fld_2").hide();';
        }
        $member['name'] = isset($member['name']) ? $member['name'] : "";
        $member['cellphone'] = isset($member['cellphone']) ? $member['cellphone'] : "";
        $member['zip'] = isset($member['zip']) ? $member['zip'] : "";
        $member['addr1'] = isset($member['addr1']) ? $member['addr1'] : "";
        $member['addr2'] = isset($member['addr2']) ? $member['addr2'] : "";
        $member['addr3'] = isset($member['addr3']) ? $member['addr3'] : "";
        $member['addr_jibeon'] = isset($member['addr_jibeon']) ? $member['addr_jibeon'] : "";
        $member['email'] = isset($member['email']) ? $member['email'] : "";
        $data = array(
            'OM_POSTCODE_JS' => $OM_POSTCODE_JS,
            'order_action_url' => $order_action_url,
            'content' => $content,
            'ss_cart_id' => $ss_cart_id,
            'member' => $member,
            'mb_recommend' => $mb_recommend,
            'pt_id' => $pt_id,
            'baesong_price' => $baesong_price,
            'tot_price' => $tot_price,
            'usePointTag' => $usePointTag,
            'mileageUseTag' => $mileageUseTag,
            'isNotMember' => $isNotMember,
            'deliverList' => $deliverList,
            'multi_settle' => $multi_settle,
            'totPrice' => $totPrice,
            'couponTag' => $couponTag,
            'pointUseTag' => $pointUseTag,
            'bankTag' => $bankTag,
            'noMemberTag' => $noMemberTag,
            'company_typeNotJs' => $company_typeNotJs,
            'config' => $config,
            'taxJs' => $taxJs,
            'taxHideJs' => $taxHideJs
        );
        $this->load->view('mb/shop/orderform', $data);
    }
    #배송지 목록
    public function orderaddress()
    {
        $is_member = $this->load->get_var('is_member');
        if (!$is_member) {
            alert_close("회원 전용 서비스입니다.");
        }
        $tb['title'] = '배송지 찾기';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header_sub();
        $this->orderaddressSkin();
        $this->template->mb_footer_sub();
    }
    private function orderaddressSkin()
    {
        $member = $this->load->get_var('member');

        $title = '배송지 찾기';
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
        $addrList = "";
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
                $addrList .= "
                <li>
                    <div class=\"addr_addr\">" . $this->funcomplex_lib->print_address($row['b_addr1'], $row['b_addr2'], $row['b_addr3'], $row['b_addr_jibeon']) . "</div>
                    <div class=\"addr_name\">{$row['b_name']}</div>
                    <div class=\"addr_tel\">{$row['b_cellphone']}</div>
                    <div class=\"addr_btn\">
                        <input type=\"hidden\" value=\"{$addr}\">
                        <button type=\"button\" id=\"btn_sel\" class=\"btn_lsmall sel_address\">선택</button>
                    </div>
                </li>";
            }
            $i++;
        }
        if (!$total_count) {
            $addrList .= '<li class="empty_list">자료가 없습니다.</li>';
        }
        $data = array('addrList' => $addrList);
        $this->load->view('mb/shop/orderaddress', $data);
    }
    #ordercoupon 결제시 나오는 보유하고 있는 쿠폰
    public function ordercoupon()
    {
        $config = $this->load->get_var('config');
        $is_member = $this->load->get_var('is_member');

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

        $tb['title'] = "쿠폰적용 및 조회";

        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header_sub();
        $this->ordercouponSkin();
        $this->template->mb_footer_sub();
    }
    private function ordercouponSkin()
    {
        $member = $this->load->get_var('member');
        $page = $this->input->get('page');
        $ss_cart_id = get_session('ss_cart_id');

        $title = "쿠폰적용 및 조회";
        $is_possible = array();
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

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $sql2 = " select * from shop_cart where index_no IN ({$ss_cart_id}) group by gs_id order by index_no ";
        $result2Obj = $this->query_model->returnQueryObj($sql2);
        $cart_count = $result2Obj->num_rows();
        $result2 = $result2Obj->result_array();
        $tot_price = 0;
        $couponTable = "";
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
            $tot_price += $price;

            // 소속 카테고리를 콤마로 구분하여 추출
            $ca_list = $this->funcomplex_lib->get_extract($row['gs_id']);
            $cp_tmp[] = $price . "|" . $row['gs_id'] . "|" . $ca_list . "|" . $gs['use_aff'];
            $couponTable .= "
            <div class=\"sod_cpuse\">
                <input type=\"hidden\" name=\"gd_dc_amt_{$i}\">
                <input type=\"hidden\" name=\"gd_cp_info_{$i}\">
                <input type=\"hidden\" name=\"gd_cp_no_{$i}\">
                <input type=\"hidden\" name=\"gd_cp_idx_{$i}\">
                <table class=\"us_box\">
                    <tbody>
                        <tr>
                            <td class=\"mi_dt\">" . $this->funcomplex_lib->get_it_image($row['gs_id'], $gs['simg1'], 60, 60) . "</td>
                            <td class=\"mi_bt\">" . $this->funcomplex_lib->get_text($gs['gname']) . "<br>
                                <span class=\"strong\">" . display_price($price) . "</span>
                            </td>
                        </tr>
                        <tr id=\"cp_avail_button_{$i}\">
                            <td colspan='2'><button type=\"button\" class=\"avail_button\" onclick=\"show_coupon('{$i}');return false;\">적용가능 쿠폰선택</button></td>
                        </tr>
                    </tbody>
                </table>
                
                <table class=\"th_box\">
                    <tbody>
                        <tr>
                            <td class=\"tal\">수량</td>
                            <td class=\"tar\">" . display_qty($sum['qty']) . "</td>
                        </tr>
                        <tr>
                            <td class=\"tal\">할인금액</td>
                            <td class=\"tar\">
                                <span id=\"dc_amt_{$i}\">0</span>원
                                <span id=\"dc_cancel_bt_{$i}\" style=\"display:none\">&nbsp;<a href=\"javascript:coupon_cancel('{$row['gs_id']}','{$row['index_no']}','{$i}');\" class='btn_ssmall bx-white'>삭제</a></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>";
            $i++;
        }
        $totPrice = display_price($tot_price);
        $i = 0;
        foreach ($result as $key => $row) {
            $lo_id = $row['lo_id'];
            //$str = mobile_cp_contents();
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
        }
        $result2 = $this->query_model->returnArr($sql2);
        $coupontTable2 = "";
        $i = 0;
        foreach ($result2 as $key => $row) {
            $coupontTable2 .= "
            <div id=\"cp_list{$i}\" class=\"mw\" style='display:none;'>
                <div class=\"bg\"></div>
                <div class=\"fg\">
                    <table class=\"ty_box\">
                    <tr>
                        <td class=\"tal\">※ 적용할 쿠폰선택</td>
                        <td class=\"tar\"><a href=\"#\" onclick=\"hide_cp_list('{$i}'); return false;\" class=\"btn_small bx-white\">닫기</a></td>
                    </tr>
                    </table>

                    <table class=\"ly_box mart5\">
                        <tbody>
                            <tr>
                                <td class=\"cell\">쿠폰번호</td>
                                <td class=\"cell\">쿠폰이름</td>
                                <td class=\"cell\">할인</td>
                                <td class=\"cell\">할인가</td>
                            </tr>";
            //5|1|8|0|10%|37496

            // 상품주키|쿠폰주키|쿠폰번호|동시사용 여부|할인금액(율)|할인가
            $chk = 0;
            for ($j = 0; $j < count($is_possible); $j++) {
                $arr = explode("|", $is_possible[$j]);
                if ($row['gs_id'] == $arr[0]) {
                    $chk++;
                    $coupontTable2 .= "
                                    <tr>
                                        <td class=\"tac\"><input id=\"ids_shown{$j}\" type=\"radio\" name=\"use_cp_{$row['gs_id']}_{$row['index_no']}\" value=\"{$arr[2]}|{$arr[5]}|{$arr[1]}|{$arr[3]}\">
                                        <label for=\"ids_shown{$j}\">{$arr[2]}</label></td>
                                        <td class=\"tac\">{$arr[6]}</td>
                                        <td class=\"tac\">{$arr[4]}</td>
                                        <td class=\"tac\">" . display_price($arr[5]) . "</td>
                                    </tr>";
                }
            }
            if (!$chk) {
                $coupontTable2 .= "
                                <tr><td colspan=\"3\" class=\"empty_list\">사용할 수 있는 쿠폰이 없습니다.</td></tr>";
            }
            $coupontTable2 .= "
                        <tbody>
                    </table>

                    <div class=\"btn_confirm\">
                        <button type=\"button\" onclick=\"return applycoupon('{$row['gs_id']}','{$row['index_no']}','{$i}');\" class=\"btn_medium red\">쿠폰적용하기</button>
                    </div>
                </div>
            </div>";
            $i++;
        }
        $data = array(
            'title' => $title,
            'total_count' => $total_count,
            'couponTable' => $couponTable,
            'totPrice' => $totPrice,
            'coupontTable2' => $coupontTable2,
            'cart_count' => $cart_count
        );
        $this->load->view('mb/shop/ordercoupon', $data);
    }
    #cartoption 결제시 변경하는 옵션
    public function cartoption()
    {
        $gs_id = $this->input->post('gs_id');
        $gs_id = trim($gs_id);
        $member = $this->load->get_var('member');

        $gs = $this->global_lib->get_goods($gs_id);
        $gs['goods_price'] = $this->gvcomplex_lib->get_sale_price($gs_id);
        $ca_id = $gs['ca_id'];

        // 최소, 최대 주문수량체크
        $it_buy_min_qty = 1;
        // $it_buy_max_qty = 0;
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

        $option_1 = $this->funcomplex_lib->mobile_item_options($gs_id, $gs['opt_subject'], " style='width:100%'");
        $opt1Tag = "";
        if ($option_1) {
            $opt1Tag = "
            <div class=\"sp_tbox\" style=\"border-top:0\">
                <ul>
                    <li class='tlst strong'>주문옵션</li>
                    <li class='trst'>아래옵션은 필수선택 옵션입니다</li>
                </ul>
            </div>{$option_1}";
        }

        $option_2 = $this->funcomplex_lib->mobile_item_supply($gs_id, $gs['spl_subject'], " style='width:100%'");
        $opt2Tag = "";
        if ($option_2) {
            $opt2Tag = "
            <div class=\"sp_tbox\"" . ((!$option_1) ? "style='border-top:0;'" : "") . ">
                <ul>
                    <li class='tlst strong'>추가구성</li>
                    <li class='trst'>추가구매를 원하시면 선택하세요</li>
                </ul>
            </div>{$option_2}";
        }

        $optList = "";
        $i = 0;
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
            <li class=\"sit_{$cls}_list\">
                <div class=\"sp_opt_set\">
					<input type=\"hidden\" name=\"io_type[{$gs_id}][]\" value=\"{$row['io_type']}\">
					<input type=\"hidden\" name=\"io_id[{$gs_id}][]\" value=\"{$row['io_id']}\">
					<input type=\"hidden\" name=\"io_value[{$gs_id}][]\" value=\"{$row['ct_option']}\">
					<input type=\"hidden\" class=\"io_price\" value=\"{$row['io_price']}\">
					<input type=\"hidden\" class=\"io_stock\" value=\"{$it_stock_qty}\">
					<input type=\"hidden\" class=\"io_index_no\" value=\"{$row['index_no']}\">
					<input type=\"hidden\" class=\"io_gs_id\" value=\"{$gs_id}\">
					<ul>
						<li class=\"it_name\"><span class=\"sit_opt_subj\">{$row['ct_option']}</span></li>
						<li class=\"it_qty\">
							<dl class=\"fl\">
								<dt class=\"stock\"><button type=\"button\">-</button></dt>
								<dt class=\"fl\"><input type=\"text\" name=\"ct_qty[{$gs_id}][]\" value=\"{$row['ct_qty']}\"></dt>
								<dt class=\"stock\"><button type=\"button\" >+</button></dt>
							</dl>
							<dl class=\"fr\">
								<dt class=\"fl\"><span class=\"sit_opt_prc\">{$io_price}</span></dt>
								<dt class=\"fl\"><button type=\"button\" class=\"delete_btn\">옵션삭제</button></dt>
							</dl>
						</li>
					</ul>
                </div>
            </li>";
            $i++;
        }
        $data = array(
            'gs_id' => $gs_id,
            'row2' => $row2,
            'opt1Tag' => $opt1Tag,
            'opt2Tag' => $opt2Tag,
            'optList' => $optList,
            'it_buy_min_qty' => $it_buy_min_qty,
            'it_buy_max_qty' => $it_buy_max_qty,
            'ca_id' => $ca_id
        );
        $this->load->view('mb/shop/cartoption', $data);
    }
}
