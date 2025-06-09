<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('global_helper');
        $this->load->helper('common_helper');

        $this->load->library('global_lib');
        $this->load->library('common_lib');
        $this->load->library('gvcomplex_lib');
        $this->load->library('funcomplex_lib');
        $this->load->library('partner_lib');
        $this->load->library('thumbnail_lib');
        $this->load->library('admin_template');
        $this->load->library('template');

        $this->load->config('shop_extend');

        $this->load->model('query_model');
        $this->load->model('Api_model');

        $this->allowed_types = 'jpg|jpeg|bmp|png|gif';
        $this->max_size = '2048';
        $this->max_width = '5000';
        $this->max_height = '5000';
        $this->remove_spaces = TRUE;
        $this->encrypt_name = TRUE;
    }
    public function index()
    {
        $this->admin_template->admin_access();
        $this->admin_template->admin_head();
        $this->admin_template->admin_topmenu();
        $this->indexSkin();
        $this->admin_template->admin_footer();
    }
    private function indexSkin()
    {
        $sodrr = $this->common_lib->admin_order_status_sum("where dan > 0 "); // 총 주문내역
        $sodr1 = $this->common_lib->admin_order_status_sum("where dan = 1 "); // 총 입금대기
        $sodr2 = $this->common_lib->admin_order_status_sum("where dan = 2 "); // 총 입금완료
        $sodr3 = $this->common_lib->admin_order_status_sum("where dan = 3 "); // 총 배송준비
        $sodr4 = $this->common_lib->admin_order_status_sum("where dan = 4 "); // 총 배송중
        $sodr5 = $this->common_lib->admin_order_status_sum("where dan = 5 "); // 총 배송완료
        $sodr6 = $this->common_lib->admin_order_status_sum("where dan = 6 "); // 총 입금전 취소
        $sodr7 = $this->common_lib->admin_order_status_sum("where dan = 7 "); // 총 배송후 반품
        $sodr8 = $this->common_lib->admin_order_status_sum("where dan = 8 "); // 총 배송후 교환
        $sodr9 = $this->common_lib->admin_order_status_sum("where dan = 9 "); // 총 배송전 환불
        $final = $this->common_lib->admin_order_status_sum("where dan = 5 and user_ok = 0 "); // 총 구매미확정

        $sql = " select * from shop_order where dan > 0 group by od_id order by index_no desc limit 5 ";
        $result = $this->query_model->returnArr($sql);
        $i = 0;
        $recentOdrStr = "";
        foreach ($result as $key => $row) {
            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $recentOdrStr .= "
            <tr class=\"tr_alignc\">
                <td>{$row['od_id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['b_name']}</td>
                <td>{$row['cellphone']}</td>
                <td>{$row['paymethod']}</td>
                <td>" . number_format($amount['buyprice']) . "</td>
                <td>" . substr($row['od_time'], 0, 16) . "(" . get_yoil($row['od_time']) . ")</td>
            </tr>";
            $i++;
        }
        if ($i == 0)
            $recentOdrStr .= '<tr><td colspan="7" class="empty_table">자료가 없습니다.</td></tr>';


        $sql = "select * from shop_member where id <> 'admin' order by index_no desc limit 5";
        $result = $this->query_model->returnArr($sql);
        $recentJoinStr = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $recentJoinStr .= "
            <tr class=\"tr_alignc\">
                <td>{$row['name']}</td>
                <td>{$row['id']}</td>
                <td>" . $this->global_lib->get_grade($row['grade']) . "</td>
                <td>{$row['email']}</td>
                <td>{$row['login_sum']}</td>
                <td>{$row['pt_id']}</td>
                <td>" . substr($row['reg_time'], 0, 16) . "(" . get_yoil($row['reg_time']) . ")</td>
            </tr>";
            $i++;
        }
        if ($i == 0)
            $recentJoinStr .= '<tr><td colspan="7" class="empty_table">자료가 없습니다.</td></tr>';

        $data = array(
            'sodrr' => $sodrr,
            'sodr1' => $sodr1,
            'sodr2' => $sodr2,
            'sodr3' => $sodr3,
            'sodr4' => $sodr4,
            'sodr5' => $sodr5,
            'sodr6' => $sodr6,
            'sodr7' => $sodr7,
            'sodr8' => $sodr8,
            'sodr9' => $sodr9,
            'final' => $final,
            'recentOdrStr' => $recentOdrStr,
            'recentJoinStr' => $recentJoinStr
        );
        $this->load->view('admin/index', $data);
    }
    #회원관리(member)인 부분의 함수들 시작
    public function member()
    {
        $this->admin_template->admin_access();
        $this->admin_template->admin_head();

        $member = $this->load->get_var('member');

        $ADMIN_MENU1 = $this->config->item('ADMIN_MENU1');
        $ADMIN_MENU1_01 = $this->config->item('ADMIN_MENU1_01');
        $ADMIN_MENU1_02 = $this->config->item('ADMIN_MENU1_02');
        $ADMIN_MENU1_03 = $this->config->item('ADMIN_MENU1_03');
        $ADMIN_MENU1_04 = $this->config->item('ADMIN_MENU1_04');
        $ADMIN_MENU1_04 = $this->config->item('ADMIN_MENU1_04');
        $ADMIN_MENU1_05 = $this->config->item('ADMIN_MENU1_05');
        $ADMIN_MENU1_06 = $this->config->item('ADMIN_MENU1_06');
        $ADMIN_MENU1_07 = $this->config->item('ADMIN_MENU1_07');
        $ADMIN_MENU1_11 = $this->config->item('ADMIN_MENU1_11');
        $ADMIN_MENU1_12 = $this->config->item('ADMIN_MENU1_12');
        $ADMIN_MENU1_13 = $this->config->item('ADMIN_MENU1_13');
        $ADMIN_MENU1_08 = $this->config->item('ADMIN_MENU1_08');
        $ADMIN_MENU1_09 = $this->config->item('ADMIN_MENU1_09');
        $ADMIN_MENU1_10 = $this->config->item('ADMIN_MENU1_10');

        $code = $this->db->escape_str($this->input->get('code'));

        $pg_title = $ADMIN_MENU1;
        $pg_num = 1;
        $snb_icon = "<i class=\"ionicons ion-ios-people fs40\"></i>";

        if ($member['id'] != 'admin' && !$member['auth_' . $pg_num]) {
            alert("접근권한이 없습니다.");
        }
        $pg_title2 = "";
        if ($code == "list")                    $pg_title2 = $ADMIN_MENU1_01;
        if ($code == "level_form")            $pg_title2 = $ADMIN_MENU1_02;
        if ($code == "register_form")        $pg_title2 = $ADMIN_MENU1_03;
        if ($code == "xls")                    $pg_title2 = $ADMIN_MENU1_04;
        if ($code == "xls_update")            $pg_title2 = $ADMIN_MENU1_04;
        if ($code == "month")                $pg_title2 = $ADMIN_MENU1_05;
        if ($code == "day")                    $pg_title2 = $ADMIN_MENU1_06;
        if ($code == "point")                $pg_title2 = $ADMIN_MENU1_07;
        if ($code == "mileage")                $pg_title2 = $ADMIN_MENU1_11;
        if ($code == "pointxls")                $pg_title2 = $ADMIN_MENU1_08;
        if ($code == "pointxls_update")        $pg_title2 = $ADMIN_MENU1_08;
        if ($code == "point_select_form")    $pg_title2 = $ADMIN_MENU1_09;
        if ($code == "point_select_list")    $pg_title2 = $ADMIN_MENU1_09;
        if ($code == "mail_list")            $pg_title2 = $ADMIN_MENU1_10;
        if ($code == "mail_form")            $pg_title2 = $ADMIN_MENU1_10;
        if ($code == "mail_select_form")        $pg_title2 = $ADMIN_MENU1_10;
        if ($code == "mail_select_list")        $pg_title2 = $ADMIN_MENU1_10;
        if ($code == "mail_select_update")    $pg_title2 = $ADMIN_MENU1_10;
        if ($code == "gift")                    $pg_title2 = $ADMIN_MENU1_12;
        if ($code == "gift_form")            $pg_title2 = $ADMIN_MENU1_12;
        if ($code == "coupon")                $pg_title2 = $ADMIN_MENU1_13;
        if ($code == "coupon_form")            $pg_title2 = $ADMIN_MENU1_13;
        $this->admin_template->admin_topmenu($pg_title, false, $pg_title2, $snb_icon);
        $this->memberSkin($code, $pg_title2);
        $this->admin_template->admin_footer();
    }
    private function memberSkin($code, $pg_title2)
    {
        $headTitle = "
            <div class=\"s_wrap\">
                <h1>{$pg_title2}</h1>";
        $headTitleEnd = "</div>";
        switch ($code) {
            case 'list':
                $this->member_list($code, $headTitle, $headTitleEnd);
                break;
            case 'register_form':
                $this->member_register($headTitle, $headTitleEnd);
                break;
            case 'xls_update':
                $this->member_xls_update($headTitle, $headTitleEnd);
                break;
            case 'level_form':
                $this->member_level($headTitle, $headTitleEnd);
                break;
            case 'xls':
                $this->member_excel($headTitle, $headTitleEnd);
                break;
            case 'mail_list':
                $this->member_mail_list($code, $headTitle, $headTitleEnd);
                break;
            case 'mail_form':
                $this->member_mail_form($code, $headTitle, $headTitleEnd);
                break;
            case 'mail_select_form':
                $this->mail_select_form($headTitle, $headTitleEnd);
                break;
            case 'mail_select_list':
                $this->mail_select_list($headTitle, $headTitleEnd);
                break;
            case 'mail_select_update':
                $this->mail_select_update($headTitle, $headTitleEnd);
                break;
            case 'point':
                $this->member_point($code, $headTitle, $headTitleEnd);
                break;
            case 'mileage':
                $this->member_mileage($code, $headTitle, $headTitleEnd);
                break;
            case 'pointxls':
                $this->member_pointxls($headTitle, $headTitleEnd);
                break;
            case 'pointxls_update':
                $this->member_pointxls_update($headTitle, $headTitleEnd);
                break;
            case 'point_select_form':
                $this->member_point_select($headTitle, $headTitleEnd);
                break;
            case 'point_select_list':
                $this->point_select_list($headTitle, $headTitleEnd);
                break;
            case 'month':
                $this->member_month($code, $headTitle, $headTitleEnd);
                break;
            case 'day':
                $this->member_day($code, $headTitle, $headTitleEnd);
                break;
            case 'gift':
                $this->goods_gift($code, $headTitle, $headTitleEnd);
                break;
            case 'gift_form':
                $this->goods_gift_form($headTitle, $headTitleEnd);
                break;
            case 'coupon':
                $this->goods_coupon($code, $headTitle, $headTitleEnd);
                break;
            case 'coupon_form':
                $this->goods_coupon_form($headTitle, $headTitleEnd);
                break;
            default:
                # code...
                break;
        }
    }
    private function member_list($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');

        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');

        $page = $this->input->get('page');
        $page = $this->db->escape_str($page);

        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $sst = $this->input->get('sst');
        $spt = $this->input->get('spt');

        $orderby = $this->input->get('orderby');
        $orderby = $this->db->escape_str($orderby);

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);


        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_member ";
        $sql_search = " where id <> 'admin' ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if ($sst) {
            $sql_search .= " and grade = '$sst' ";
        }

        // 기간검색
        if ($fr_date && $to_date)
            $sql_search .= " and {$spt} between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and {$spt} between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and {$spt} between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        if (!$orderby) {
            $filed = "index_no";
            $sod = "desc";
        } else {
            $sod = $orderby;
        }

        $sql_search .= "and supply = ''";

        $sql_order = " order by $filed $sod ";

        // 테이블의 전체 레코드수만 얻음
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

        $is_intro = false;
        $colspan = 11;
        if ($config['cert_admin_yes']) {
            $is_intro = true;
            $colspan++;
        }
        $btn_frmline = "
            <a href=\"" . base_url() . "admin/member?code=mail_list\" class=\"btn_lsmall bx-white\">전체메일발송</a>
            <a href=\"" . base_url() . "admin/sms/sms_member\" onclick=\"win_open(this,'pop_sms','245','360','no');return false\" class=\"btn_lsmall bx-white\">전체문자발송</a>
            <a href=\"" . base_url() . "AdminProcess/member_list_excel?$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장</a>
            <a href=\"" . base_url() . "admin/member?code=register_form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 회원추가</a>";
        $selectOpt = array("", "", "", "", "");
        $selectOpt[0] = option_selected('id', $sfl, '아이디');
        $selectOpt[1] = option_selected('name', $sfl, '회원명');
        $selectOpt[2] = option_selected('pt_id', $sfl, '추천인');
        $selectOpt[3] = option_selected('cellphone', $sfl, '핸드폰');
        $selectOpt[4] = option_selected('telephone', $sfl, '전화번호');

        $sptOpt = array("", "");
        $sptOpt[0] = option_selected('reg_time', $spt, "가입날짜");
        $sptOpt[1] = option_selected('today_login', $spt, "최근접속");

        $timeSearch = get_search_date("fr_date", "to_date", $fr_date, $to_date);
        $levelSearch = $this->funcomplex_lib->get_search_level('sst', $sst, 2, 9);
        $tableIsIntro = array("", "", "");
        if ($is_intro) {
            $tableIsIntro[0] = '<col class="w40">';
            $tableIsIntro[1] = "<th scope=\"col\">" . $this->gvcomplex_lib->subject_sort_link('use_app', $q2) . "인증</a></th>";
        }
        $name = $this->gvcomplex_lib->subject_sort_link('name', $q2);
        $id = $this->gvcomplex_lib->subject_sort_link('id', $q2);
        $grade = $this->gvcomplex_lib->subject_sort_link('grade', $q2);
        $recom_id = $this->gvcomplex_lib->subject_sort_link('pt_id', $q2);
        $reg_time = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $login_sum = $this->gvcomplex_lib->subject_sort_link('login_sum', $q2);
        $intercept_date = $this->gvcomplex_lib->subject_sort_link('intercept_date', $q2);
        $point = $this->gvcomplex_lib->subject_sort_link('point', $q2);
        $mileage = $this->gvcomplex_lib->subject_sort_link('mileage', $q2);

        //사업자 등록증 오름차순 내림차순 링크추가
        // $business_cert_image_link = $this->gvcomplex_lib->subject_sort_link('business_cert_image', $q2);
        // $business_cert_image = "";
        // $cert = $this->Api_model->getBizCert($row['id']);
        // $business_cert_image = $cert['company_file_name'];

        //통장 사본 오름차순 내림차순 링크추가
        // $bank_account_image_link = $this->gvcomplex_lib->subject_sort_link('bank_account_image', $q2);
        $bank_account_image = "";

        $i = 0;
        $mebmerList = "";
        foreach ($result as $key => $row) {
            $cert = $this->Api_model->getBizCert($row['id']);
            $company_file_path = $cert['company_file_path'];
            $company_file_name = $cert['company_file_name'];
            $business_cert_image = $company_file_path.$company_file_name;

            if ($i == 0)
                $mebmerList .= '<tbody class="list">';
            $bg = 'list' . ($i % 2);
            $mebmerList .= "
                <tr class=\"{$bg}\">
                    <td>" . $num-- . "</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_sideview($row['id'], $row['name']) . "</td>
                    <td class=\"tal\">{$row['id']}</td>
                    <td class=\"tal\">";
            if (!empty($row['mb_img'])) {
                $mebmerList .= "<img src='" . base_url() . "data/member/{$row['mb_img']}' class=\"member_img\">";
            }

            $mebmerList .= "
                    </td>
                    <td>" . $this->global_lib->get_grade($row['grade']) . "</td>
                    <td> <button type=\"button\" class=\"btn btn-sm btn-primary view-image-btn\" data-toggle=\"modal\" data-target=\"#imageModal\" data-img=\"".$business_cert_image."\">
                        보기
                        </button>
                    </td>
                    <td>" . $bank_account_image . "</td>
                    <td class=\"tal\">{$row['pt_id']}</td>
                    <td>{$row['cellphone']}</td>
                    <td>{$row['reg_time']}</td>
                    <td>" . number_format($this->common_lib->shop_count($row['id'])) . "</td>
                    <td>" . number_format($row['login_sum']) . "</td>
                    <td>" . substr($row['intercept_date'], 2, 6) . "</td>";
            if ($is_intro) {
                $mebmerList .= "<td><input type=\"checkbox\" name=\"use_app\" value=\"1\"" . ($row['use_app'] ? ' checked' : '') . "onclick=\"chk_use_app('{$row['id']}');\"></td>";
            }
            $mebmerList .= "<td class=\"tar\">" . number_format($row['point']) . " P</td>
                                  <td class=\"tar\">" . number_format($row['mileage']) . " 원</td>
                </tr>";
            $i++;
        }

        if ($i == 0)
            $mebmerList .= '<tbody><tr><td colspan="' . $colspan . '" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $jqueryUiDatePick = $this->admin_template->datePicker();


        $data = array(
            "btn_frmline" => $btn_frmline,
            'code' => $code,
            'stx' => $stx,
            'total_count' => $total_count,
            'selectOpt' => $selectOpt,
            'sptOpt' => $sptOpt,
            'timeSearch' => $timeSearch,
            'levelSearch' => $levelSearch,
            'tableIsIntro' => $tableIsIntro,
            'name' => $name,
            'id' => $id,
            'grade' => $grade,
            'recom_id' => $recom_id,
            'business_cert_image' => $business_cert_image,
            'bank_account_image' => $bank_account_image,
            'reg_time' => $reg_time,
            'login_sum' => $login_sum,
            'intercept_date' => $intercept_date,
            'point' => $point,
            'mileage' => $mileage,
            'mebmerList' => $mebmerList,
            'pagination' => $pagination,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'jqueryUiDatePick' => $jqueryUiDatePick
        );
        $this->load->view('admin/member/list', $data);
    }

    private function member_register($headTitle, $headTitleEnd)
    {
        $help = help('영문자, 숫자만 입력 가능. 6자이상 입력하세요.');
        $data = array('help' => $help, 'headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/member/member_register_form', $data);
    }
    private function member_xls_update($headTitle, $headTitleEnd)
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

        // 자료가 많을 경우 대비 설정변경
        set_time_limit(0);
        ini_set('memory_limit', '50M');

        $total_count = 0; // 총회원수
        $fail_count = 0; // 실패건수
        $succ_count = 0; // 완료건수

        if ($_FILES['excelfile']['tmp_name']) {
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

            $dup_mb_id = array();

            for ($i = 4; $i <= $data->sheets[0]['numRows']; $i++) {
                if (trim($data->sheets[0]['cells'][$i][1]) == '')
                    continue;

                $total_count++;

                $j = 1;

                $mb_id            = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                $mb_password    = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                $mb_name        = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                $mb_birth        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++])));
                $mb_gender        = addslashes(trim(strtoupper($data->sheets[0]['cells'][$i][$j++])));
                $mb_recommend    = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                $mb_email        = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                $mb_telephone    = addslashes(trim(replace_tel($data->sheets[0]['cells'][$i][$j++])));
                $mb_cellphone    = addslashes(trim(replace_tel($data->sheets[0]['cells'][$i][$j++])));
                $mb_zip            = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++])));
                $mb_addr1        = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                $mb_addr2        = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                $mb_addr3        = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                $mb_point        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++])));

                // id, name 값이 없다면?
                if (!$mb_id || !$mb_name) {
                    $fail_count++;
                    continue;
                }

                // id 형식체크
                if (preg_match("/[^0-9a-z_]+/i", $mb_id)) {
                    $fail_count++;
                    continue;
                }

                // id 중복체크
                $sql = " select count(*) as cnt from shop_member where id = ?";
                $param = array($mb_id);
                $row = $this->query_model->returnOneArr($sql, $param);
                if ($row['cnt']) {
                    $dup_mb_id[] = $mb_id;
                    $fail_count++;
                    continue;
                }

                // 추천인이 없으면 최고관리자로 바꿈
                if (!$mb_recommend) $mb_recommend = 'admin';

                unset($value);
                $value['name']            = $mb_name; //회원명
                $value['id']            = $mb_id; //회원아이디
                $value['passwd']        = $this->global_lib->reg_sql_password($mb_password, $OM_TIME_YMDHIS); //비밀번호
                $value['mb_birth']        = $mb_birth; //생년월일
                $value['age']            = get_birth_age($mb_birth); //연령대
                $value['gender']        = $mb_gender ? $mb_gender : 'M'; //성별
                $value['email']            = $mb_email; //이메일
                $value['telephone']        = $mb_telephone; //전화번호
                $value['cellphone']        = $mb_cellphone; //핸드폰번호
                $value['zip']            = $mb_zip; //우편번호
                $value['addr1']            = $mb_addr1; //주소
                $value['addr2']            = $mb_addr2; //상세주소
                $value['addr3']            = $mb_addr3; //참고항목
                $value['pt_id']            = $mb_recommend; //추천인
                $value['point']            = (int)$mb_point; //포인트
                $value['mailser']        = 'Y'; //메일링 수신여부
                $value['smsser']        = 'Y'; //SMS 수신여부
                $value['grade']            = '2'; //레벨
                $value['mb_ip']            = $_SERVER['REMOTE_ADDR']; //IP
                $value['login_ip']        = $_SERVER['REMOTE_ADDR']; //최근 로그인IP
                $value['today_login']    = $OM_TIME_YMDHIS; //최근 로그인일시
                $value['reg_time']        = $OM_TIME_YMDHIS; // 가입일
                $this->query_model->insertObj("shop_member", $value);
                $succ_count++;
            }
        }
        $fail_countTag = "";
        if ($fail_count > 0) {
            $fail_countTag = "
                <tr>
                    <th scope=\"row\">중복된아이디</th>
                    <td>" . implode(', ', $dup_mb_id) . "</td>
                </tr>";
        }
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'total_count' => $total_count,
            'fail_count' => $fail_count,
            'succ_count' => $succ_count,
            'fail_countTag' => $fail_countTag
        );
        $this->load->view('admin/member/xls_update', $data);
    }
    private function member_level($headTitle, $headTitleEnd)
    {
        $page = $this->input->get('page');
        $page = $this->db->escape_str($page);

        $sql_common = " from shop_member_grade ";
        $sql_order  = " order by gb_no desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql = "select count(*) as cnt $sql_common ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $i = 0;
        $levelList = "";
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);
            $levelList .= "
                <tr class=\"{$bg}\">
                    <td class=\"bold\">
                        Lv." . $num-- . "
                        <input type=\"hidden\" name=\"gb_no[{$i}]\" value=\"{$row['gb_no']}\">
                        <input type=\"hidden\" name=\"chk[]\" value=\"{$i}\" checked>
                    </td>
                    <td><input type=\"text\" name=\"gb_name[{$i}]\" value=\"{$row['gb_name']}\" class=\"frm_input wfull\"></td>
                    <td>
                        <input type=\"text\" name=\"gb_sale[{$i}]\" value=\"{$row['gb_sale']}\" class=\"frm_input w70\">
                        <select name=\"gb_sale_rate[{$i}]\">
                            <option value=\"0\"" . get_selected($row['gb_sale_rate'], '0') . ">%</option>
                            <option value=\"1\"" . get_selected($row['gb_sale_rate'], '1') . ">원</option>
                        </select>
                    </td>
                    <td class=\"tac\">
                        <select name=\"gb_sale_unit[{$i}]\">
                            " . option_selected('0', $row['gb_sale_unit'], '사용안함') . "
                            " . option_selected('10', $row['gb_sale_unit'], '십원 단위절삭') . "
                            " . option_selected('100', $row['gb_sale_unit'], '백원 단위절삭') . "
                            " . option_selected('1000', $row['gb_sale_unit'], '천원 단위절삭') . "
                            " . option_selected('10000', $row['gb_sale_unit'], '만원 단위절삭') . "
                        </select>
                    </td>
                    <td><input type=\"text\" name=\"gb_buy_point[{$i}]\" value=\"{$row['gb_buy_point']}\" class=\"frm_input w70\">%</td>
                    <td><input type=\"text\" name=\"gb_review_point[{$i}]\" value=\"{$row['gb_review_point']}\" class=\"frm_input w70\">%</td>
                    <td><input type=\"text\" name=\"gb_payback_point[{$i}]\" value=\"{$row['gb_payback_point']}\" class=\"frm_input w70\">%</td>
                    <td><input type=\"text\" name=\"gb_minimum[{$i}]\" value=\"" . number_format($row['gb_minimum']) . "\" class=\"frm_input w80\" onkeyup=\"addComma(this);\">원</td>
                    <td><input type=\"text\" name=\"gb_maximum[{$i}]\" value=\"" . number_format($row['gb_maximum']) . "\" class=\"frm_input w80\" onkeyup=\"addComma(this);\">원</td>
                    <td>";
            if (!empty($row['gb_img'])) {
                $levelList .= "<img src=\"" . base_url() . "/data/grade/{$row['gb_img']}\" class=\"grade_img\"></img>";
                $levelList .= "<input type=\"checkbox\" name=\"del_img[$i]\" value='{$row['gb_img']}'> 삭제";
            }
            $levelList .= "
                    </td>
                    <td><input type=\"file\" name=\"gb_img[{$i}]\" value=\"{$row['gb_img']}\" class=\"frm_input wfull\"></td>
                    <td><input type=\"text\" name=\"gb_info[{$i}]\" value=\"{$row['gb_info']}\" class=\"frm_input wfull\"></td>
                </tr>";
            $i++;
        }

        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'levelList' => $levelList);
        $this->load->view('admin/member/meber_level_form', $data);
    }
    private function member_excel($headTitle, $headTitleEnd)
    {
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/member/xls', $data);
    }
    private function member_mail_list($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');

        $page = $this->input->get('page');
        $page = $this->db->escape_str($page);

        $sfl = $this->input->get('sfl');
        $sfl = $this->db->escape_str($sfl);

        $stx = $this->input->get('stx');
        $stx = $this->db->escape_str($stx);

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_mail ";
        $sql_search = " where (1) ";
        $sql_order  = " order by ma_id desc ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        // 테이블의 전체 레코드수만 얻음
        $sql = "select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = "select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "admin/member?code=mail_form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 메일내용추가</a>";

        $mailListTag = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $ma_id = $row['ma_id'];
            $ma_subject = $this->funcomplex_lib->get_text($row['ma_subject']);

            $s_frm = "<a href=\"" . base_url() . "admin/member?code=mail_select_form&ma_id=$ma_id$qstr&page=$page\" class=\"tu\">보내기</a>";
            $s_upd = "<a href=\"" . base_url() . "admin/member?code=mail_form&w=u&ma_id=$ma_id$qstr&page=$page\" class=\"btn_small bx-white\">수정</a>";

            if ($i == 0)
                $mailListTag .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $mailListTag .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"ma_id[{$i}]\" value=\"{$ma_id}\">
                        <label for=\"chk_{$i}\" class=\"sound_only\">{$ma_subject} 메일</label>
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td class=\"tal\">{$ma_subject}</td>
                    <td>{$row['ma_time']}</td>
                    <td><a href=\"" . base_url() . "admin/member_mail_test?ma_id={$ma_id}\" class=\"tu\">테스트</a></td>
                    <td>{$s_frm}</td>
                    <td class=\"btn_group\">{$s_upd}
                        <a href=\"" . base_url() . "admin/member_mail_preview?ma_id={$ma_id}\" target=\"_blank\" class=\"btn_small bx-white\">보기</a>
                    </td>
                </tr>";
            $i++;
        }
        if ($i == 0) {
            $mailListTag .= '<tbody><tr><td colspan="7" class="empty_table">자료가 없습니다.</td></tr>';
        }
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'code' => $code,
            'btn_frmline' => $btn_frmline,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'stx' => $stx,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'mailListTag' => $mailListTag,
            'pagination' => $pagination
        );
        $this->load->view('admin/member/mail_list', $data);
    }
    private function member_mail_form($code, $headTitle, $headTitleEnd)
    {
        $qstr = $this->load->get_var('qstr');

        $w = $this->input->get('w');
        $page = $this->input->get('page');
        $ma_id = $this->input->get('ma_id');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $ma = array();
        if ($w == "u") {
            $sql = " select * from shop_mail where ma_id = ? ";
            $param = array($ma_id);
            $ma = $this->query_model->returnOneArr($sql, $param);
            if (!$ma['ma_id'])
                alert('등록된 자료가 없습니다.');
        }
        $frm_submit = '<div class="btn_confirm">
                <input type="submit" value="저장" class="btn_large" accesskey="s">
                <a href="' . base_url() . 'admin/member?code=mail_list' . $qstr . '&page=' . $page . '" class="btn_large bx-white">목록</a>';
        if ($w == 'u')
            $frm_submit .= '<a href="' . base_url() . 'admin/member?code=mail_form" class="btn_large bx-red">추가</a>';
        $frm_submit .= '</div>';
        $helpArr = array("", "");
        $helpArr[0] = help('위 제공하는 치환코드를 메일내용에 입력하시면 자동으로 변환되어 메일에 적용 됩니다.', 'fc_084');
        $helpArr[1] = help('예) 최대 10,000포인트 득템찬스!! 이벤트에 참여하세요!');
        $ma['ma_subject'] = isset($ma['ma_subject']) ? $ma['ma_subject'] : "";
        $ma['ma_content'] = isset($ma['ma_content']) ? $ma['ma_content'] : "";
        $editor = $this->global_lib->editor_html('ma_content', $this->funcomplex_lib->get_text($ma['ma_content'], 0));
        $getEditorJs = $this->global_lib->get_editor_js("ma_content");
        $chkEditorJs = $this->global_lib->chk_editor_js("ma_content");
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'w' => $w,
            'sfl' => $sfl,
            'stx' => $stx,
            'ma_id' => $ma_id,
            'page' => $page,
            'helpArr' => $helpArr,
            'editor' => $editor,
            'getEditorJs' => $getEditorJs,
            'chkEditorJs' => $chkEditorJs,
            'ma' => $ma,
            'frm_submit' => $frm_submit
        );
        $this->load->view('admin/member/member_mail_form', $data);
    }
    private function mail_select_form($headTitle, $headTitleEnd)
    {
        $ma_id = $this->input->get('ma_id');
        $page = $this->input->get('page');
        $qstr = $this->load->get_var('qstr');
        $sql = " select * from shop_mail where ma_id = ?";
        $param = array($ma_id);
        $ma = $this->query_model->returnOneArr($sql, $param);
        if (!$ma['ma_id'])
            alert('보내실 내용을 선택하여 주십시오.');

        // 전체회원수
        $sql = " select COUNT(*) as cnt from shop_member ";
        $row = $this->query_model->returnOneArr($sql);
        $tot_cnt = $row['cnt'];

        // 차단된회원수
        $sql = " select COUNT(*) as cnt from shop_member where intercept_date <> '' ";
        $row = $this->query_model->returnOneArr($sql);
        $finish_cnt = $row['cnt'];

        $last_option = explode('||', $ma['ma_last_option']);
        for ($i = 0; $i < count($last_option); $i++) {
            $option = explode('=', $last_option[$i]);
            // 동적변수
            $var = isset($option[0]) ? $option[0] : "";
            $$var = isset($option[1]) ? $option[1] : "";
        }

        if (!isset($mb_id1)) $mb_id1 = 1;
        if (!isset($mb_level_from)) $mb_level_from = 9;
        if (!isset($mb_level_to)) $mb_level_to = 1;
        if (!isset($mb_mailling)) $mb_mailling = 1;
        $mb_all = $mb_id1 ? "checked" : "";
        $mb_allNo = !$mb_id1 ? "checked" : "";
        $mb_id1_from = $this->input->get('mb_id1_from');
        $mb_id1_to = $this->input->get('mb_id1_to');
        $mb_email = $this->input->get('mb_email');

        $help = help("메일 주소에 단어 포함 (예 : @" . preg_replace('#^(www[^\.]*\.){1}#', '', $_SERVER['HTTP_HOST']) . ")");
        $mb_level_from = $this->common_lib->get_member_select("mb_level_from", $mb_level_from);
        $mb_level_to = $this->common_lib->get_member_select("mb_level_to", $mb_level_to);
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'ma_id' => $ma_id,
            'tot_cnt' => $tot_cnt,
            'mb_all' => $mb_all,
            'mb_allNo' => $mb_allNo,
            'finish_cnt' => $finish_cnt,
            'mb_id1_from' => $mb_id1_from,
            'mb_id1_to' => $mb_id1_to,
            'mb_email' => $mb_email,
            'help' => $help,
            'mb_level_from' => $mb_level_from,
            'mb_level_to' => $mb_level_to,
            'qstr' => $qstr,
            'page' => $page
        );
        $this->load->view('admin/member/mail_select_form', $data);
    }
    private function mail_select_list($headTitle, $headTitleEnd)
    {
        $mb_id1 = $this->input->post('mb_id1');
        $mb_id1_from = $this->input->post('mb_id1_from');
        $mb_id1_to = $this->input->post('mb_id1_to');
        $mb_email = $this->input->post('mb_email');
        $mb_mailling = $this->input->post('mb_mailling');
        $mb_level_to = $this->input->post('mb_level_to');
        $mb_level_from = $this->input->post('mb_level_from');
        $ma_id = $this->input->post('ma_id');

        $ma_last_option = "";
        // 입점사, 차단된 회원은 제외
        $sql_common = " rom shop_member ";
        $sql_where  = " where supply = '' and intercept_date = '' ";

        // 회원ID ..에서 ..까지
        if ($mb_id1 != 1)
            $sql_where .= " and id between '{$mb_id1_from}' and '{$mb_id1_to}' ";

        // E-mail에 특정 단어 포함
        if ($mb_email != "")
            $sql_where .= " and email like '%{$mb_email}%' ";

        // 메일링
        if ($mb_mailling != "")
            $sql_where .= " and mailser = '{$mb_mailling}' ";

        // 권한
        $sql_where .= " and grade between '{$mb_level_to}' and '{$mb_level_from}' ";

        $sql = " select COUNT(*) as cnt {$sql_common} {$sql_where} ";
        $row = $this->query_model->returnOneArr($sql);
        $cnt = $row['cnt'];
        if ($cnt == 0)
            alert('선택하신 내용으로는 해당되는 회원자료가 없습니다.');

        // 마지막 옵션을 저장합니다.
        $ma_last_option .= "mb_id1={$mb_id1}";
        $ma_last_option .= "||mb_id1_from={$mb_id1_from}";
        $ma_last_option .= "||mb_id1_to={$mb_id1_to}";
        $ma_last_option .= "||mb_email={$mb_email}";
        $ma_last_option .= "||mb_mailling={$mb_mailling}";
        $ma_last_option .= "||mb_level_from={$mb_level_from}";
        $ma_last_option .= "||mb_level_to={$mb_level_to}";

        $this->query_model->returnNull(" update shop_mail set ma_last_option = '{$ma_last_option}' where ma_id = '{$ma_id}' ");
        $ma_list = $cr = "";
        $sql = " select * $sql_common $sql_where order by id ";
        $result = $this->query_model->returnArr($sql);
        $i = 0;
        $listTable = "";
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $ma_list .= $cr . $row['email'] . "||" . $row['id'] . "||" . $this->funcomplex_lib->get_text($row['name']) . "||" . $this->global_lib->get_grade($row['grade']) . "||" . $row['reg_time'];
            $cr = "\n";
            $listTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        " . ($i + 1) . "
                        <input type=\"hidden\" name=\"ma_list\" value=\"{$ma_list}\">
                    </td>
                    <td class=\"tal\">{$row['id']}</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_text($row['name']) . "</td>
                    <td>" . $this->global_lib->get_grade($row['grade']) . "</td>
                    <td class=\"tal\">{$row['email']}</td>
                </tr>";
            $i++;
        }

        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'ma_id' => $ma_id, 'cnt' => $cnt, 'listTable' => $listTable);
        $this->load->view('admin/member/mail_select_list', $data);
    }
    private function mail_select_update($headTitle, $headTitleEnd)
    {
        include_once("./lib/mailer.lib.php");
        $countgap  = 10; // 몇건씩 보낼지 설정
        $maxscreen = 500; // 몇건씩 화면에 보여줄건지?
        $sleepsec  = 200; // 천분의 몇초간 쉴지 설정
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/member/mail_select_update', $data);

        $ma_id = trim($this->input->post('ma_id'));
        $select_member_list = trim($this->input->post('ma_list'));

        //print_r2($_POST); EXIT;
        $member_list = explode("\n", conv_unescape_nl($select_member_list));

        // 메일내용 가져오기
        $sql = "select ma_subject, ma_content from shop_mail where ma_id =?";
        $param = array($ma_id);
        $ma = $this->query_model->returnOneArr($sql, $param);
        $subject = $ma['ma_subject'];
        $cnt = 0;
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');
        for ($i = 0; $i < count($member_list); $i++) {
            list($to_email, $mb_id, $name, $grade, $datetime) = explode("||", trim($member_list[$i]));

            $sw = preg_match("/[0-9a-zA-Z_]+(\.[0-9a-zA-Z_]+)*@[0-9a-zA-Z_]+(\.[0-9a-zA-Z_]+)*/", $to_email);
            // 올바른 메일 주소만
            if ($sw == true) {
                $cnt++;

                $mb_md5 = md5($mb_id . $to_email . $datetime);

                $content = $ma['ma_content'];
                $content = preg_replace("/{이름}/", $name, $content);
                $content = preg_replace("/{레벨명}/", $grade, $content);
                $content = preg_replace("/{아이디}/", $mb_id, $content);
                $content = preg_replace("/{이메일}/", $to_email, $content);

                $content = $content . "<hr size=0><p><span style='font-size:9pt; font-familye:굴림'>▶ 더 이상 정보 수신을 원치 않으시면 [<a href='" . base_url() . "AdminProcess/email_stop?mb_id={$mb_id}&amp;mb_md5={$mb_md5}' target='_blank'>수신거부</a>] 해 주십시오.</span></p>";

                mailer($config['company_name'], $member['email'], $to_email, $subject, $content, 1);

                echo "<script> document.all.cont.innerHTML += '$cnt. $to_email ($mb_id : $name)<br>'; </script>\n";
                //echo "+";
                usleep($sleepsec);
                if ($cnt % $countgap == 0) {
                    echo "<script> document.all.cont.innerHTML += '<br>'; document.body.scrollTop += 1000; </script>\n";
                }

                // 화면을 지운다... 부하를 줄임
                if ($cnt % $maxscreen == 0)
                    echo "<script> document.all.cont.innerHTML = ''; document.body.scrollTop += 1000; </script>\n";
            }
        }
        echo "<script> document.all.cont.innerHTML += \"<br>총 " . number_format($cnt) . "건 발송<br><br><font color=crimson><b>[끝]</b></font>\"; document.body.scrollTop += 1000; </script>";
    }
    private function member_point($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $page = $this->input->get('page');
        $page = $this->db->escape_str($page);
        $sfl = $this->input->get('sfl');
        $sfl = $this->db->escape_str($sfl);

        $stx = $this->input->get('stx');
        $stx = $this->db->escape_str($stx);

        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_point a, shop_member b ";
        $sql_search = " where a.mb_id = b.id ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if ($fr_date && $to_date)
            $sql_search .= " and a.po_datetime between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and a.po_datetime between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and a.po_datetime between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        if (!$orderby) {
            $filed = "a.po_id";
            $sod = "desc";
        } else {
            $sod = $orderby;
        }

        $sql_order = " order by $filed $sod ";

        // 테이블의 전체 레코드수만 얻음
        $sql = "select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select a.*, b.name, b.grade $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $row2 = $this->query_model->returnOneArr("select sum(a.po_point) as sum_point $sql_common $sql_search ");
        $sum_point = $row2['sum_point'];
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $btn_frmline = "<input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">";

        $keyOpt = array("", "");
        $keyOpt[0] = option_selected('a.mb_id', $sfl, '아이디');
        $keyOpt[1] = option_selected('b.name', $sfl, '회원명');
        $searchDate = get_search_date("fr_date", "to_date", $fr_date, $to_date);
        $Name = $this->gvcomplex_lib->subject_sort_link('b.name', $q2);
        $Mbid = $this->gvcomplex_lib->subject_sort_link('a.mb_id', $q2);
        $Grade = $this->gvcomplex_lib->subject_sort_link('b.grade', $q2);
        $Content = $this->gvcomplex_lib->subject_sort_link('a.po_content', $q2);
        $Datetime = $this->gvcomplex_lib->subject_sort_link('a.po_datetime', $q2);
        $Po_point = $this->gvcomplex_lib->subject_sort_link('a.po_point', $q2);

        $pointList = "";
        $i = 0;
        foreach ($result as $key => $row) {
            if ($i == 0)
                $pointList .= '<tbody class="list">';
            $bg = 'list' . ($i % 2);
            $pointList .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"po_id[{$i}]\" value=\"{$row['po_id']}\">
                        <input type=\"hidden\" name=\"mb_id[{$i}]\" value=\"{$row['mb_id']}\">
                        <label for=\"chk_{$i}\" class=\"sound_only\">{$row['po_content']}</label>
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                    </td>
                    <td>" . ($num--) . "</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_sideview($row['mb_id'], $row['name']) . "</td>
                    <td class=\"tal\">{$row['mb_id']}</td>
                    <td>" . $this->global_lib->get_grade($row['grade']) . "</td>
                    <td class=\"tal\">{$row['po_content']}</td>
                    <td>{$row['po_datetime']}</td>
                    <td class=\"tar\">" . number_format($row['po_point']) . "</td>
                    <td class=\"tar\">" . number_format($row['po_mb_point']) . "</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $pointList .= '<tbody><tr><td colspan="9" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'code' => $code,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'btn_frmline' => $btn_frmline,
            'keyOpt' => $keyOpt,
            'stx' => $stx,
            'searchDate' => $searchDate,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'sum_point' => $sum_point,
            'Name' => $Name,
            'Mbid' => $Mbid,
            'Grade' => $Grade,
            'Content' => $Content,
            'Datetime' => $Datetime,
            'Po_point' => $Po_point,
            'pointList' => $pointList,
            'pagination' => $pagination
        );
        $this->load->view('admin/member/member_point', $data);
    }
    private function member_pointxls($headTitle, $headTitleEnd)
    {
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/member/member_pointxls', $data);
    }
    private function member_pointxls_update($headTitle, $headTitleEnd)
    {
        $total_count = 0; // 총회원수
        $fail_count = 0; // 실패건수
        $succ_count = 0; // 완료건수
        if ($_FILES['excelfile']['tmp_name']) {
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

            $total_count = 0; // 총회원수
            $fail_count = 0; // 실패건수
            $succ_count = 0; // 완료건수

            for ($i = 4; $i <= $data->sheets[0]['numRows']; $i++) {
                if (trim($data->sheets[0]['cells'][$i][1]) == '')
                    continue;
                $total_count++;
                $j = 1;
                $mb_id        = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                $po_point    = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                $po_content    = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                if ($mb_id == "" || $po_point == "" ||  $po_content == "") {
                    $fail_count++;
                    continue;
                }
                $mb = $this->global_lib->get_member($mb_id, 'id, point');
                if (!$mb['id']) {
                    $fail_count++;
                    continue;
                }
                // 포인트를 차감하는 경우 현재 포인트보다 작으면 안됩니다.
                if (($po_point < 0) && ($po_point * (-1) > $mb['point'])) {
                    $fail_count++;
                    continue;
                }
                $this->funcomplex_lib->insert_point($mb_id, $po_point, $po_content, '@passive', $mb_id, $member['id'] . '-' . uniqid(''));
                $succ_count++;
            }
        }
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'total_count' => $total_count, 'fail_count' => $fail_count, 'succ_count' => $succ_count);
        $this->load->view('admin/member_pointxls_update', $data);
    }
    private function member_point_select($headTitle, $headTitleEnd)
    {
        // 전체회원수
        $sql = " select COUNT(*) as cnt from shop_member ";
        $row = $this->query_model->returnOneArr($sql);
        $tot_cnt = $row['cnt'];

        // 차단된회원수
        $sql = " select COUNT(*) as cnt from shop_member where intercept_date <> '' ";
        $row = $this->query_model->returnOneArr($sql);
        $finish_cnt = $row['cnt'];

        $mb_id1 = $this->input->get('mb_id1');
        $mb_level_from = $this->input->get('mb_level_from');
        $mb_level_to = $this->input->get('mb_level_to');

        $mb_id1_to = $this->input->get('mb_id1_to');
        $mb_id1_from = $this->input->get('mb_id1_from');

        if (!isset($mb_id1)) $mb_id1 = 1;
        if (!isset($mb_level_from)) $mb_level_from = 9;
        if (!isset($mb_level_to)) $mb_level_to = 1;

        $mb_idChk = $mb_id1 ? "checked" : "";
        $mb_idChkNot = !$mb_id1 ? "checked" : "";

        $mb_level_fromSelect = $this->common_lib->get_member_select("mb_level_from", $mb_level_from);
        $mb_level_toSelect = $this->common_lib->get_member_select("mb_level_to", $mb_level_to);
        $exHelp = help('예) 보너스 포인트 지급합니다. 감사합니다.');
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'tot_cnt' => $tot_cnt,
            'finish_cnt' => $finish_cnt,
            'mb_idChk' => $mb_idChk,
            'mb_idChkNot' => $mb_idChkNot,
            'mb_id1_from' => $mb_id1_from,
            'mb_id1_to' => $mb_id1_to,
            'mb_level_fromSelect' => $mb_level_fromSelect,
            'mb_level_toSelect' => $mb_level_toSelect,
            'exHelp' => $exHelp
        );
        $this->load->view('admin/member/point_select', $data);
    }
    private function point_select_list($headTitle, $headTitleEnd)
    {
        $mb_id1_from = $this->input->post('mb_id1_from');
        $mb_id1_to = $this->input->post('mb_id1_to');
        $mb_level_to = $this->input->post('mb_level_to');
        $mb_level_from = $this->input->post('mb_level_from');
        $mb_id1 = $this->input->post('mb_id1');
        $po_point = $this->input->post('po_point');
        $po_content = $this->input->post('po_content');

        // 입점사, 차단된 회원은 제외
        $sql_common = " from shop_member ";
        $sql_where  = " where supply = '' and intercept_date = '' ";

        // 회원ID ..에서 ..까지
        if ($mb_id1 != 1)
            $sql_where .= " and id between '{$mb_id1_from}' and '{$mb_id1_to}' ";

        // 권한
        $sql_where .= " and grade between '{$mb_level_to}' and '{$mb_level_from}' ";

        $sql = " select COUNT(*) as cnt {$sql_common} {$sql_where} ";
        $row = $this->query_model->returnOneArr($sql);
        $cnt = $row['cnt'];
        if ($cnt == 0)
            alert('선택하신 내용으로는 해당되는 회원자료가 없습니다.');
        $point_mb_id = $cr = "";
        $sql = " select * $sql_common $sql_where order by id ";
        $result = $this->query_model->returnArr($sql);
        $i = 0;
        $listTable = "";
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $isChk = '<span class="fc_00f">정상</span>';
            if (($po_point < 0) && ($po_point * (-1) > $row['point']))
                $isChk = '<span class="fc_red">잔액부족</span>';

            $point_mb_id .= $cr . $row['id'];
            $cr = ",";
            $listTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        " . ($i + 1) . "
                        <input type=\"hidden\" name=\"point_mb_id\" value=\"{$point_mb_id}\">
                    </td>
                    <td class=\"tal\">{$row['id']}</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_text($row['name']) . "</td>
                    <td>" . $this->global_lib->get_grade($row['grade']) . "</td>
                    <td class=\"tar\">" . number_format($row['point']) . "</td>
                    <td class=\"tar\">" . number_format($po_point) . "</td>
                    <td>{$isChk}</td>
                </tr>";
            $i++;
        }
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'po_point' => $po_point,
            'cnt' => $cnt,
            'po_content' => $po_content,
            'listTable' => $listTable
        );
        $this->load->view('admin/member/point_select_list', $data);
    }
    private function member_mileage($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $page = $this->input->get('page');
        $page = $this->db->escape_str($page);
        $sfl = $this->input->get('sfl');
        $sfl = $this->db->escape_str($sfl);

        $stx = $this->input->get('stx');
        $stx = $this->db->escape_str($stx);

        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_mileage a, shop_member b ";
        $sql_search = " where a.mb_id = b.id ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if ($fr_date && $to_date)
            $sql_search .= " and a.po_datetime between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and a.po_datetime between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and a.po_datetime between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        if (!$orderby) {
            $filed = "a.po_id";
            $sod = "desc";
        } else {
            $sod = $orderby;
        }

        $sql_order = " order by $filed $sod ";

        // 테이블의 전체 레코드수만 얻음
        $sql = "select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select a.*, b.name, b.grade $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $row2 = $this->query_model->returnOneArr("select sum(a.po_point) as sum_point $sql_common $sql_search ");
        $sum_point = $row2['sum_point'];
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $btn_frmline = "<input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">";

        $keyOpt = array("", "");
        $keyOpt[0] = option_selected('a.mb_id', $sfl, '아이디');
        $keyOpt[1] = option_selected('b.name', $sfl, '회원명');
        $searchDate = get_search_date("fr_date", "to_date", $fr_date, $to_date);
        $Name = $this->gvcomplex_lib->subject_sort_link('b.name', $q2);
        $Mbid = $this->gvcomplex_lib->subject_sort_link('a.mb_id', $q2);
        $Grade = $this->gvcomplex_lib->subject_sort_link('b.grade', $q2);
        $Content = $this->gvcomplex_lib->subject_sort_link('a.po_content', $q2);
        $Datetime = $this->gvcomplex_lib->subject_sort_link('a.po_datetime', $q2);
        $Po_point = $this->gvcomplex_lib->subject_sort_link('a.po_point', $q2);

        $pointList = "";
        $i = 0;
        foreach ($result as $key => $row) {
            if ($i == 0)
                $pointList .= '<tbody class="list">';
            $bg = 'list' . ($i % 2);
            $pointList .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"po_id[{$i}]\" value=\"{$row['po_id']}\">
                        <input type=\"hidden\" name=\"mb_id[{$i}]\" value=\"{$row['mb_id']}\">
                        <label for=\"chk_{$i}\" class=\"sound_only\">{$row['po_content']}</label>
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                    </td>
                    <td>" . ($num--) . "</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_sideview($row['mb_id'], $row['name']) . "</td>
                    <td class=\"tal\">{$row['mb_id']}</td>
                    <td>" . $this->global_lib->get_grade($row['grade']) . "</td>
                    <td class=\"tal\">{$row['po_content']}</td>
                    <td>{$row['po_datetime']}</td>
                    <td class=\"tar\">" . number_format($row['po_point']) . "</td>
                    <td class=\"tar\">" . number_format($row['po_mb_point']) . "</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $pointList .= '<tbody><tr><td colspan="9" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'code' => $code,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'btn_frmline' => $btn_frmline,
            'keyOpt' => $keyOpt,
            'stx' => $stx,
            'searchDate' => $searchDate,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'sum_point' => $sum_point,
            'Name' => $Name,
            'Mbid' => $Mbid,
            'Grade' => $Grade,
            'Content' => $Content,
            'Datetime' => $Datetime,
            'Po_point' => $Po_point,
            'pointList' => $pointList,
            'pagination' => $pagination
        );
        $this->load->view('admin/member/member_mileage', $data);
    }
    private function member_mileagexls($headTitle, $headTitleEnd)
    {
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/member/member_pointxls', $data);
    }
    private function member_mileagexls_update($headTitle, $headTitleEnd)
    {
        $total_count = 0; // 총회원수
        $fail_count = 0; // 실패건수
        $succ_count = 0; // 완료건수
        if ($_FILES['excelfile']['tmp_name']) {
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

            $total_count = 0; // 총회원수
            $fail_count = 0; // 실패건수
            $succ_count = 0; // 완료건수

            for ($i = 4; $i <= $data->sheets[0]['numRows']; $i++) {
                if (trim($data->sheets[0]['cells'][$i][1]) == '')
                    continue;
                $total_count++;
                $j = 1;
                $mb_id        = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                $po_point    = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                $po_content    = addslashes(trim($data->sheets[0]['cells'][$i][$j++]));
                if ($mb_id == "" || $po_point == "" ||  $po_content == "") {
                    $fail_count++;
                    continue;
                }
                $mb = $this->global_lib->get_member($mb_id, 'id, point');
                if (!$mb['id']) {
                    $fail_count++;
                    continue;
                }
                // 포인트를 차감하는 경우 현재 포인트보다 작으면 안됩니다.
                if (($po_point < 0) && ($po_point * (-1) > $mb['point'])) {
                    $fail_count++;
                    continue;
                }
                $this->funcomplex_lib->insert_point($mb_id, $po_point, $po_content, '@passive', $mb_id, $member['id'] . '-' . uniqid(''));
                $succ_count++;
            }
        }
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'total_count' => $total_count, 'fail_count' => $fail_count, 'succ_count' => $succ_count);
        $this->load->view('admin/member_pointxls_update', $data);
    }
    private function member_mileage_select($headTitle, $headTitleEnd)
    {
        // 전체회원수
        $sql = " select COUNT(*) as cnt from shop_member ";
        $row = $this->query_model->returnOneArr($sql);
        $tot_cnt = $row['cnt'];

        // 차단된회원수
        $sql = " select COUNT(*) as cnt from shop_member where intercept_date <> '' ";
        $row = $this->query_model->returnOneArr($sql);
        $finish_cnt = $row['cnt'];

        $mb_id1 = $this->input->get('mb_id1');
        $mb_level_from = $this->input->get('mb_level_from');
        $mb_level_to = $this->input->get('mb_level_to');

        $mb_id1_to = $this->input->get('mb_id1_to');
        $mb_id1_from = $this->input->get('mb_id1_from');

        if (!isset($mb_id1)) $mb_id1 = 1;
        if (!isset($mb_level_from)) $mb_level_from = 9;
        if (!isset($mb_level_to)) $mb_level_to = 1;

        $mb_idChk = $mb_id1 ? "checked" : "";
        $mb_idChkNot = !$mb_id1 ? "checked" : "";

        $mb_level_fromSelect = $this->common_lib->get_member_select("mb_level_from", $mb_level_from);
        $mb_level_toSelect = $this->common_lib->get_member_select("mb_level_to", $mb_level_to);
        $exHelp = help('예) 보너스 포인트 지급합니다. 감사합니다.');
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'tot_cnt' => $tot_cnt,
            'finish_cnt' => $finish_cnt,
            'mb_idChk' => $mb_idChk,
            'mb_idChkNot' => $mb_idChkNot,
            'mb_id1_from' => $mb_id1_from,
            'mb_id1_to' => $mb_id1_to,
            'mb_level_fromSelect' => $mb_level_fromSelect,
            'mb_level_toSelect' => $mb_level_toSelect,
            'exHelp' => $exHelp
        );
        $this->load->view('admin/member/point_select', $data);
    }
    private function mileage_select_list($headTitle, $headTitleEnd)
    {
        $mb_id1_from = $this->input->post('mb_id1_from');
        $mb_id1_to = $this->input->post('mb_id1_to');
        $mb_level_to = $this->input->post('mb_level_to');
        $mb_level_from = $this->input->post('mb_level_from');
        $mb_id1 = $this->input->post('mb_id1');
        $po_point = $this->input->post('po_point');
        $po_content = $this->input->post('po_content');

        // 입점사, 차단된 회원은 제외
        $sql_common = " from shop_member ";
        $sql_where  = " where supply = '' and intercept_date = '' ";

        // 회원ID ..에서 ..까지
        if ($mb_id1 != 1)
            $sql_where .= " and id between '{$mb_id1_from}' and '{$mb_id1_to}' ";

        // 권한
        $sql_where .= " and grade between '{$mb_level_to}' and '{$mb_level_from}' ";

        $sql = " select COUNT(*) as cnt {$sql_common} {$sql_where} ";
        $row = $this->query_model->returnOneArr($sql);
        $cnt = $row['cnt'];
        if ($cnt == 0)
            alert('선택하신 내용으로는 해당되는 회원자료가 없습니다.');
        $point_mb_id = $cr = "";
        $sql = " select * $sql_common $sql_where order by id ";
        $result = $this->query_model->returnArr($sql);
        $i = 0;
        $listTable = "";
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $isChk = '<span class="fc_00f">정상</span>';
            if (($po_point < 0) && ($po_point * (-1) > $row['point']))
                $isChk = '<span class="fc_red">잔액부족</span>';

            $point_mb_id .= $cr . $row['id'];
            $cr = ",";
            $listTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        " . ($i + 1) . "
                        <input type=\"hidden\" name=\"point_mb_id\" value=\"{$point_mb_id}\">
                    </td>
                    <td class=\"tal\">{$row['id']}</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_text($row['name']) . "</td>
                    <td>" . $this->global_lib->get_grade($row['grade']) . "</td>
                    <td class=\"tar\">" . number_format($row['point']) . "</td>
                    <td class=\"tar\">" . number_format($po_point) . "</td>
                    <td>{$isChk}</td>
                </tr>";
            $i++;
        }
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'po_point' => $po_point,
            'cnt' => $cnt,
            'po_content' => $po_content,
            'listTable' => $listTable
        );
        $this->load->view('admin/member/point_select_list', $data);
    }
    private function member_month($code, $headTitle, $headTitleEnd)
    {
        $OM_TIME_YEAR = date("Y", time());
        $OM_TIME_YM = date("Y-m", time());
        $year = $this->input->get('year');
        if (!$year) $year = $OM_TIME_YEAR;
        $tot_count1 = $this->common_lib->sel_count("shop_member", "where grade between 2 and 9 ");
        $tot_count2 = $this->common_lib->sel_count("shop_member", "where grade between 7 and 9 ");

        $sql = " select MIN(reg_time) as min_year from shop_member where grade between 2 and 9 ";
        $row = $this->query_model->returnOneArr($sql);

        $min_year = substr($row['min_year'], 0, 4); // 가장작은 년도
        if (!$min_year) $min_year = $OM_TIME_YEAR; // 내역이없다면 현재 년도로
        $yearOpt = "";
        for ($i = $min_year; $i <= $OM_TIME_YEAR; $i++) {
            $yearOpt .= "<option value=\"{$i}\"" . get_selected($year, $i) . ">{$i}년</option>\n";
        }

        if (!$tot_count1) $tot_count1 = 1;

        $monthList = "";
        for ($i = 1; $i <= 12; $i++) {
            $month = sprintf("%02d", $i);
            $date = preg_replace("/([0-9]{4})([0-9]{2})/", "\\1-\\2", $year . $month);

            $count1 = $this->common_lib->sel_count("shop_member", "where left(reg_time,7)='$date' and grade between 2 and 9 ");
            $count2 = $this->common_lib->sel_count("shop_member", "where left(reg_time,7)='$date' and grade between 7 and 9 ");

            $rate = ($count1 / $tot_count1 * 100);
            $s_rate = number_format($rate, 1);

            $bg = 'list' . ($i % 2);
            $monthList .= "
                <tr class=\"{$bg}\">
                    <td>{$date}</td>
                    <td><div class=\"graph\"><span class=\"bar\" style=\"width:{$s_rate}%\"></span></div></td>
                    <td>{$s_rate}</td>
                    <td>" . number_format($count1) . "</td>
                    <td>" . number_format($count2) . "</td>
                </tr>";
            // 이번달과 같다면 중지
            if ($date == $OM_TIME_YM) break;
        }

        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'code' => $code,
            'yearOpt' => $yearOpt,
            'tot_count1' => $tot_count1,
            'tot_count2' => $tot_count2,
            'monthList' => $monthList
        );
        $this->load->view('admin/member/month', $data);
    }
    private function member_day($code, $headTitle, $headTitleEnd)
    {
        $OM_TIME_YEAR = date("Y", time());
        $OM_TIME_MONTH = date("m", time());
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $min_year = $this->input->get('min_year');

        if (!$year)  $year  = $OM_TIME_YEAR;
        if (!$month) $month = $OM_TIME_MONTH;

        $tot_count1 = $this->common_lib->sel_count("shop_member", "where grade between 2 and 9 ");
        $tot_count2 = $this->common_lib->sel_count("shop_member", "where grade between 7 and 9 ");
        $tot_count3 = $this->common_lib->sel_count("shop_member", "where grade between 2 and 6 ");

        $sql = "select MIN(reg_time) as min_year from shop_member where grade between 2 and 9 ";
        $row = $this->query_model->returnOneArr($sql);

        $min_year = substr($row['min_year'], 0, 4); // 가장작은 년도
        if (!$min_year) $min_year = $OM_TIME_YEAR; // 내역이없다면 현재 년도로
        $yearList = "";
        for ($i = $min_year; $i <= $OM_TIME_YEAR; $i++) {
            $yearList .= "<option value=\"{$i}\"" . get_selected($year, $i) . ">{$i}년</option>\n";
        }
        $monthList = "";
        for ($i = 1; $i <= 12; $i++) {
            $k = sprintf('%02d', $i);
            $monthList .= "<option value=\"{$k}\"" . get_selected($month, $k) . ">{$k}월</option>\n";
        }
        if (!$tot_count1) $tot_count1 = 1;

        $dayList = "";
        for ($i = 1; $i <= 31; $i++) {
            $day = sprintf("%02d", $i);
            $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $year . $month . $day);

            $count1 = $this->common_lib->sel_count("shop_member", "where left(reg_time,10)='$date' and grade between 2 and 9 ");
            $count2 = $this->common_lib->sel_count("shop_member", "where left(reg_time,10)='$date' and grade between 7 and 9 ");

            $rate = ($count1 / $tot_count1 * 100);
            $s_rate = number_format($rate, 1);

            $bg = 'list' . ($i % 2);
            $dayList .= "
                <tr class=\"{$bg}\">
                    <td>{$date}</td>
                    <td><div class=\"graph\"><span class=\"bar\" style=\"width:{$s_rate}%\"></span></div></td>
                    <td>{$s_rate}</td>
                    <td>" . number_format($count1) . "</td>
                    <td>" . number_format($count2) . "</td>
                </tr>";
            // 오늘날짜와 같다면 중지
            if ($date == $OM_TIME_YMD) break;
        }
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'yearList' => $yearList,
            'monthList' => $monthList,
            'tot_count1' => $tot_count1,
            'tot_count2' => $tot_count2,
            'dayList' => $dayList
        );
        $this->load->view('admin/member/day', $data);
    }
    public function member_point_req()
    {
        $mb_id = $this->input->get('mb_id');
        $title = '포인트 적립, 차감';
        $this->admin_template->admin_head($title);
        $mb    = $this->global_lib->get_member($mb_id);
        $data = array('title' => $title, 'mb' => $mb, 'mb_id' => $mb_id);
        $this->load->view('admin/member/member_point_reg', $data);
        $this->admin_template->admin_footer_sub();
    }
    public function member_mileage_req()
    {
        $mb_id = $this->input->get('mb_id');
        $title = '예치금 적립, 차감';
        $this->admin_template->admin_head($title);
        $mb    = $this->global_lib->get_member($mb_id);
        $data = array('title' => $title, 'mb' => $mb, 'mb_id' => $mb_id);
        $this->load->view('admin/member/member_mileage_reg', $data);
        $this->admin_template->admin_footer_sub();
    }
    public function member_mail_preview()
    {
        $ma_id = $this->input->get('ma_id');
        $sql = "select ma_subject, ma_content from shop_mail where ma_id = ?";
        $param = array($ma_id);
        $se = $this->query_model->returnOneArr($sql, $param);

        $subject = $se['ma_subject'];
        $content = $this->funcomplex_lib->conv_content($se['ma_content'], 1) . "<hr size=0><p><span style='font-size:9pt; font-family:굴림'>▶ 더 이상 정보 수신을 원치 않으시면 [<a href='" . base_url() . "AdminProcess/email_stop?mb_id=***&amp;mb_md5=***' target='_blank'>수신거부</a>] 해 주십시오.</span></p>";
        echo "
            <!doctype html>
            <html lang=\"ko\">
            <head>
            <meta charset=\"utf-8\">
            <title>메일발송 테스트</title>
            </head>
            <body>
            <h1>{$subject}</h1>
            <p>
                {$content}
            </p>
            <p>
                <strong>주의!</strong> 이 화면에 보여지는 디자인은 실제 내용이 발송되었을 때 디자인과 다를 수 있습니다.
            </p>
            </body>
            </html>";
    }
    public function member_mail_test()
    {
        include_once("./lib/mailer.lib.php");
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');
        $ma_id = $this->input->get('ma_id');
        $tb['title'] = "회원메일 테스트";
        $name  = $this->funcomplex_lib->get_text($member['name']);
        $grade = $this->global_lib->get_grade($member['grade']);
        $mb_id = $member['id'];
        $email = $member['email'];

        $sql = "select ma_subject, ma_content from shop_mail where ma_id = ?";
        $param = array($ma_id);
        $ma = $this->query_model->returnOneArr($sql, $param);

        $subject = $ma['ma_subject'];
        $content = $ma['ma_content'];
        $content = preg_replace("/{이름}/", $name, $content);
        $content = preg_replace("/{레벨명}/", $grade, $content);
        $content = preg_replace("/{아이디}/", $mb_id, $content);
        $content = preg_replace("/{이메일}/", $email, $content);

        $mb_md5 = md5($member['id'] . $member['email'] . $member['reg_time']);

        $content = $content . '<p>더 이상 정보 수신을 원치 않으시면 [<a href="' . base_url() . 'AdminProcess/email_stop?mb_id=' . $mb_id . '&amp;mb_md5=' . $mb_md5 . '" target="_blank">수신거부</a>] 해 주십시오.</p>';

        mailer($config['company_name'], $member['email'], $member['email'], $subject, $content, 1);

        alert($member['name'] . '(' . $member['email'] . ')님께 테스트 메일을 발송하였습니다. 확인하여 주십시오.');
    }
    #회원관리(member)인 부분의 함수들 끝

    #공급자(seller) 관련된 함수들 시작
    public function seller()
    {
        $this->admin_template->admin_access();
        $this->admin_template->admin_head();
        $member = $this->load->get_var('member');
        $code = $this->input->get('code');
        $ADMIN_MENU3 = $this->config->item('ADMIN_MENU3');

        $ADMIN_MENU3_01 = $this->config->item('ADMIN_MENU3_01');
        $ADMIN_MENU3_02 = $this->config->item('ADMIN_MENU3_02');
        $ADMIN_MENU3_03 = $this->config->item('ADMIN_MENU3_03');
        $ADMIN_MENU3_04 = $this->config->item('ADMIN_MENU3_04');
        $ADMIN_MENU3_05 = $this->config->item('ADMIN_MENU3_05');
        $ADMIN_MENU3_06 = $this->config->item('ADMIN_MENU3_06');

        $pg_title = $ADMIN_MENU3;
        $pg_num = 3;
        $snb_icon = "<i class=\"fa fa-truck\"></i>";
        $pg_title2 = "";
        if ($member['id'] != 'admin' && !$member['auth_' . $pg_num]) {
            alert("���ٱ����� �����ϴ�.");
        }
        if ($code == "list")                    $pg_title2 = $ADMIN_MENU3_01;
        if ($code == "register")                $pg_title2 = $ADMIN_MENU3_02;
        if ($code == "xls")                    $pg_title2 = $ADMIN_MENU3_03;
        if ($code == "xls_update")            $pg_title2 = $ADMIN_MENU3_03;
        if ($code == "pay")                    $pg_title2 = $ADMIN_MENU3_04;
        if ($code == "pay_history")            $pg_title2 = $ADMIN_MENU3_05;
        if ($code == "mail_select_form")        $pg_title2 = $ADMIN_MENU3_06;
        if ($code == "mail_select_list")        $pg_title2 = $ADMIN_MENU3_06;
        if ($code == "mail_select_update")    $pg_title2 = $ADMIN_MENU3_06;

        $this->admin_template->admin_topmenu($pg_title, false, $pg_title2, $snb_icon);
        $this->sellerSkin($code, $pg_title2);
        $this->admin_template->admin_footer();
    }
    private function sellerSkin($code, $pg_title2)
    {
        $headTitle = "
            <div class=\"s_wrap\">
                <h1>{$pg_title2}</h1>";
        $headTitleEnd = "</div>";
        switch ($code) {
            case 'list':
                $this->seller_list($code, $headTitle, $headTitleEnd);
                break;
            case 'register':
                $this->seller_register($headTitle, $headTitleEnd);
                break;
            case 'xls':
                $this->seller_xls($headTitle, $headTitleEnd);
                break;
            case 'xls_update':
                $this->seller_xls_update($headTitle, $headTitleEnd);
                break;
            case 'mail_select_form':
                $this->seller_mail_select_form($headTitle, $headTitleEnd);
                break;
            case 'mail_select_list':
                $this->seller_mail_select_list($headTitle, $headTitleEnd);
                break;
            case 'pay':
                $this->seller_pay($code, $headTitle, $headTitleEnd);
                break;
            case 'pay_history':
                $this->seller_pay_history($code, $headTitle, $headTitleEnd);
                break;

            default:
                break;
        }
    }
    private function seller_list($code, $headTitle, $headTitleEnd)
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $jqueryUiDatePick = $this->admin_template->datePicker();

        $gw_isopen = $this->config->item('gw_isopen');

        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');

        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $query_string = "code=$code$qstr";
        $q1 = $query_string;

        $start = strpos($query_string, '&filed');
        #$q2 = $query_string."&page=$page";
        #echo "<script>console.log(`".substr( $query_string, 0, $start)."`)</script>";
        $query_string2 = substr($query_string, 0, $start);
        $q2 = $query_string2 . "&page=$page";

        $sql_common = " from shop_seller a, shop_member b ";
        $sql_search = " where a.mb_id = b.id ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if ($fr_date && $to_date)
            $sql_search .= " and a.reg_time between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and a.reg_time between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and a.reg_time between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        if (!$orderby) {
            $filed = "a.state";
            $sod = "asc, a.index_no desc";
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
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select a.*, b.name, b.email, b.today_login $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택승인\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "admin/seller?code=mail_select_form\" class=\"btn_lsmall bx-white\">전체메일발송</a>
            <a href=\"" . base_url() . "admin/sms/sms_seller\" onclick=\"win_open(this,'allsms','245','360','no');return false\" class=\"btn_lsmall bx-white\">전체문자발송</a>
            <a href=\"" . base_url() . "AdminProcess/seller_list_excel?$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장</a>";
        $company_name = option_selected('a.company_name', $sfl, '입점사명');
        $seller_code = option_selected('a.seller_code', $sfl, '업체코드');
        $info_name = option_selected('a.info_name', $sfl, '담당자명');
        $company_owner = option_selected('a.company_owner', $sfl, '대표자명');
        $mb_id = option_selected('a.mb_id', $sfl, '아이디');
        $name = option_selected('b.name', $sfl, '회원명');

        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $aState = $this->gvcomplex_lib->subject_sort_link('a.state', $q2);
        $aSeller_open = $this->gvcomplex_lib->subject_sort_link('a.seller_open', $q2);
        $bName = $this->gvcomplex_lib->subject_sort_link('b.name', $q2);
        $aMb_id = $this->gvcomplex_lib->subject_sort_link('a.mb_id', $q2);
        $aSeller_code = $this->gvcomplex_lib->subject_sort_link('a.seller_code', $q2);
        $aCompany_name = $this->gvcomplex_lib->subject_sort_link('a.company_name', $q2);
        $aReg_time = $this->gvcomplex_lib->subject_sort_link('a.reg_time', $q2);

        $i = 0;
        $sellerListTable = "";
        foreach ($result as $key => $row) {
            $disabled = '';
            if ($row['state']) $disabled = ' disabled';

            if ($i == 0)
                $sellerListTable .= "<tbody class=\"list\">";

            $bg = 'list' . ($i % 2);
            $sellerListTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"mb_id[{$i}]\" value=\"{$row['mb_id']}\">
                        <input type=\"hidden\" name=\"seller_code[{$i}]\" value=\"{$row['seller_code']}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>" . ($row['state'] ? '승인' : '대기') . "</td>
                    <td>{$gw_isopen[$row['seller_open']]}</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_sideview($row['mb_id'], $row['name']) . "</td>
                    <td class=\"tal\">{$row['mb_id']}</td>
                    <td>{$row['seller_code']}</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_text($row['company_name']) . "</td>
                    <td></td>
                    <td></td>
                    <td>{$row['company_tel']}</td>
                    <td>{$row['reg_time']}</td>
                    <td>{$row['today_login']}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $sellerListTable .= '<tbody><tr><td colspan="9" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'code' => $code,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'pagination' => $pagination,
            'btn_frmline' => $btn_frmline,
            'stx' => $stx,
            'company_name' => $company_name,
            'seller_code' => $seller_code,
            'info_name' => $info_name,
            'company_owner' => $company_owner,
            'mb_id' => $mb_id,
            'name' => $name,
            'get_search_date' => $get_search_date,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'aState' => $aState,
            'aSeller_open' => $aSeller_open,
            'bName' => $bName,
            'aMb_id' => $aMb_id,
            'aSeller_code' => $aSeller_code,
            'aCompany_name' => $aCompany_name,
            'aReg_time' => $aReg_time,
            'sellerListTable' => $sellerListTable
        );
        $this->load->view('admin/seller/list', $data);
    }
    private function seller_register($headTitle, $headTitleEnd)
    {
        $sql = "select * from shop_member where grade <> '1' and supply = '' order by name ";
        $res = $this->query_model->returnArr($sql);
        $sellerOpt = "";
        foreach ($res as $key => $row) {
            $sr = $this->global_lib->get_seller($row['id'], 'mb_id');
            if ($sr['mb_id']) continue;
            $sellerOpt .= "<option value='{$row['id']}'>[Lv.{$row['grade']}] {$row['name']} ($row[id])</option>";
        }
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'sellerOpt' => $sellerOpt);
        $this->load->view('admin/seller/register', $data);
    }
    private function seller_xls($headTitle, $headTitleEnd)
    {
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/seller/xls', $data);
    }
    private function seller_xls_update($headTitle, $headTitleEnd)
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

        // 자료가 많을 경우 대비 설정변경
        set_time_limit(0);
        ini_set('memory_limit', '50M');
        $dup_count = 0; // 중복건수
        $total_count = 0; // 총업체수
        $fail_count = 0; // 실패건수
        $succ_count = 0; // 완료건수
        $dup_mb_id = array();
        if ($_FILES['excelfile']['tmp_name']) {
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
            for ($i = 4; $i <= $data->sheets[0]['numRows']; $i++) {
                if (trim($data->sheets[0]['cells'][$i][1]) == '')
                    continue;

                $total_count++;

                $j = 1;

                $mb_id = addslashes(trim(strtolower($data->sheets[0]['cells'][$i][$j++]))); //아이디
                $passwd = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //비밀번호
                $seller_item = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //제공상품
                $company_name = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //업체(법인)명
                $company_saupja_no = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //사업자등록번호
                $company_tel = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //전화번호
                $company_fax = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //팩스번호
                $company_zip = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //우편번호
                $company_addr1 = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //주소
                $company_item = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //업태
                $company_service = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //종목
                $company_owner = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //대표자명
                $company_hompage = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //홈페이지
                $bank_name = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //은행명
                $bank_account = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //계좌번호
                $bank_holder = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //예금주명
                $info_name = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //담당자명
                $info_email = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //담당자이메일
                $info_tel = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); //담당자핸드폰

                // id, passwd, company_name 값이 없다면?
                if (!$mb_id || !$passwd || !$company_name) {
                    $fail_count++;
                    continue;
                }

                // id 형식체크
                if (preg_match("/[^0-9a-z_]+/i", $mb_id)) {
                    $fail_count++;
                    continue;
                }

                // id 중복체크
                $sql = " select count(*) as cnt from shop_member where id = ?";
                $param = array($mb_id);
                $row = $this->query_model->returnOneArr($sql, $param);
                if ($row['cnt']) {
                    $dup_mb_id[] = $mb_id;
                    $dup_count++;
                    $fail_count++;
                    continue;
                }

                unset($mfrm);
                $mfrm['id']                    = $mb_id; //회원아이디
                $mfrm['name']                = $company_name; //회원명
                $mfrm['passwd']                = $passwd; //비밀번호
                $mfrm['email']                = $info_email; //이메일
                $mfrm['cellphone']            = $info_tel; //핸드폰번호
                $mfrm['telephone']            = $company_tel; //전화번호
                $mfrm['zip']                = $company_zip; //우편번호
                $mfrm['addr1']                = $company_addr1; //주소
                $mfrm['gender']                = 'M'; //성별
                $mfrm['mailser']            = 'Y'; //메일링 수신여부
                $mfrm['smsser']                = 'Y'; //SMS 수신여부
                $mfrm['supply']                = 'Y'; //입점사 여부
                $mfrm['pt_id']                = 'admin'; //추천인
                $mfrm['grade']                = '9'; //레벨
                $mfrm['mb_ip']                = $_SERVER['REMOTE_ADDR']; //IP
                $mfrm['login_ip']            = $_SERVER['REMOTE_ADDR']; //최근 로그인IP
                $mfrm['today_login']        = $OM_TIME_YMDHIS; //최근 로그인일시
                $mfrm['reg_time']            = $OM_TIME_YMDHIS; // 가입일시
                $this->query_model->insertObj("shop_member", $mfrm);

                unset($sfrm);
                $sfrm['mb_id']                = $mb_id;
                $sfrm['seller_code']        = $this->global_lib->code_uniqid();
                $sfrm['seller_item']        = $seller_item;
                $sfrm['company_name']        = $company_name;
                $sfrm['company_saupja_no']    = $company_saupja_no;
                $sfrm['company_item']        = $company_item;
                $sfrm['company_service']    = $company_service;
                $sfrm['company_owner']        = $company_owner;
                $sfrm['company_tel']        = $company_tel;
                $sfrm['company_fax']        = $company_fax;
                $sfrm['company_zip']        = $company_zip;
                $sfrm['company_addr1']        = $company_addr1;
                $sfrm['company_hompage']    = $company_hompage;
                $sfrm['info_name']            = $info_name;
                $sfrm['info_tel']            = $info_tel;
                $sfrm['info_email']            = $info_email;
                $sfrm['bank_name']            = $bank_name;
                $sfrm['bank_account']        = $bank_account;
                $sfrm['bank_holder']        = $bank_holder;
                $sfrm['reg_time']            = $OM_TIME_YMDHIS;
                $sfrm['update_time']        = $OM_TIME_YMDHIS;
                $sfrm['state']                = 1;
                $sfrm['seller_open']        = 1;
                $this->query_model->insertObj("shop_seller", $sfrm);

                $succ_count++;
            }
        }
        $is_fail = "";
        if ($fail_count > 0) {
            $is_fail = "
                <tr>
                    <th scope=\"row\">중복된아이디</th>
                    <td>" . implode(', ', $dup_mb_id) . "</td>
                </tr>";
        }
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'dup_count' => $dup_count,
            'total_count' => $total_count,
            'fail_count' => $fail_count,
            'succ_count' => $succ_count,
            'is_fail' => $is_fail
        );
        $this->load->view('admin/seller/xls_update', $data);
    }
    private function seller_mail_select_form($headTitle, $headTitleEnd)
    {
        // 전체입점사수
        $sql = " select COUNT(*) as cnt from shop_seller ";
        $row = $this->query_model->returnOneArr($sql);
        $tot_cnt = $row['cnt'];

        // 미승인입점사수
        $sql = " select COUNT(*) as cnt from shop_seller where state = '0' ";
        $row = $this->query_model->returnOneArr($sql);
        $finish_cnt = $row['cnt'];
        $help1 = help('위 제공하는 치환코드를 메일내용에 입력하시면 자동으로 변환되어 메일에 적용 됩니다.', 'fc_084');
        $help2 = help('예) 최대 10,000포인트 득템찬스!! 이벤트에 참여하세요!');
        $editor_html = $this->global_lib->editor_html('ma_content', "");
        $get_editor_js = $this->global_lib->get_editor_js("ma_content");
        $chk_editor_js = $this->global_lib->chk_editor_js("ma_content");
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'tot_cnt' => $tot_cnt,
            'finish_cnt' => $finish_cnt,
            'help1' => $help1,
            'help2' => $help2,
            'editor_html' => $editor_html,
            'get_editor_js' => $get_editor_js,
            'chk_editor_js' => $chk_editor_js
        );
        $this->load->view('admin/seller/mail_select_form', $data);
    }
    private function seller_mail_select_list($headTitle, $headTitleEnd)
    {
        // 승인된입점사, 담당자 이메일이있는 입점사만
        $sql_common = " from shop_seller ";
        $sql_where  = " where state = '1' and info_email <> '' ";

        $sql = " select COUNT(*) as cnt {$sql_common} {$sql_where} ";
        $row = $this->query_model->returnOneArr($sql);
        $cnt = $row['cnt'];
        $ma_subject = $this->input->post('ma_subject');
        $ma_content = $this->input->post('ma_content');
        if ($cnt == 0)
            alert('선택하신 내용으로는 해당되는 입점사자료가 없습니다.');
        $ma_list = $cr = "";
        $sql = " select * {$sql_common} {$sql_where} order by mb_id ";
        $result = $this->query_model->returnArr($sql);
        $i = 0;
        $mail_select_list_Table = "";
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $info = array();
            $info[] = $row['info_email'];
            $info[] = $row['mb_id'];
            $info[] = $this->funcomplex_lib->get_text($row['company_name']);
            $info[] = $this->funcomplex_lib->get_text($row['company_owner']);
            $info[] = $row['company_saupja_no'];
            $info[] = $row['company_tel'];
            $info[] = $row['company_fax'];
            $info[] = $this->funcomplex_lib->get_text($row['info_name']);

            $ma_list .= $cr . implode("||", $info);
            $cr = "\n";
            $mail_select_list_Table .= "
                <tr class=\"{$bg}\">
                    <td>" . ($i + 1) . "
                        <input type=\"hidden\" name=\"ma_list\" value=\"{$ma_list}\">
                    </td>
                    <td class=\"tal\">{$row['mb_id']}</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_text($row['company_name']) . "</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_text($row['info_name']) . "</td>
                    <td class=\"tal\">{$row['info_email']}</td>
                </tr>";

            $i++;
        }
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'ma_subject' => $ma_subject,
            'ma_content' => $ma_content,
            'cnt' => $cnt,
            'mail_select_list_Table' => $mail_select_list_Table
        );
        $this->load->view('admin/seller/mail_select_list', $data);
    }
    private function seller_pay($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $jqueryUiDatePick = $this->admin_template->datePicker();


        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_order a, shop_seller b ";
        $sql_search = " where a.seller_id = b.seller_code
                            and left(a.seller_id,3)='AP-'
                            and a.dan IN(5,8)
                            and a.sellerpay_yes = '0'
                            and a.user_ok = '1' ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if ($fr_date && $to_date) {
            $sql_search .= " and left(a.od_time,10) between '$fr_date' and '$to_date' ";
        }

        $sql_order = " group by a.seller_id order by a.index_no desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql = "select count(DISTINCT a.seller_id) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = "select a.*, b.mb_id, b.company_name $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택정산\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "AdminProcess/seller_pay_excel?$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장</a>";
        $sflOpt = array("", "", "");
        $sflOpt[0] = option_selected('b.company_name', $sfl, '입점사명');
        $sflOpt[1] = option_selected('b.seller_code', $sfl, '업체코드');
        $sflOpt[2] = option_selected('b.mb_id', $sfl, '아이디');
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $i = 0;
        $payTable = "";
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $tot_price     = 0;
            $tot_point     = 0;
            $tot_mileage = 0;
            $tot_coupon     = 0;
            $tot_baesong = 0;
            $tot_supply     = 0;
            $tot_seller     = 0;
            $tot_partner = 0;
            $tot_admin     = 0;
            $order_idx     = array();
            $order_arr     = array();

            $sql2 = " select *
                            from shop_order
                           where seller_id = '{$row['seller_id']}'
                             and dan IN(5,8)
                             and sellerpay_yes = '0'
                             and user_ok = '1' ";
            if ($fr_date && $to_date) {
                $sql2 .= " and left(od_time,10) between '$fr_date' and '$to_date' ";
            }
            $res2 = $this->query_model->returnArr($sql2);
            foreach ($res2 as $key => $row2) {
                $psql = " select SUM(pp_pay) as sum_pay
                                from shop_partner_pay
                               where pp_rel_table = 'sale'
                                 and pp_rel_id = '{$row2['od_no']}'
                                 and pp_rel_action = '{$row2['od_id']}' ";
                $psum = $this->query_model->returnOneArr($psql);

                $tot_point   += (int)$row2['use_point']; // 포인트결제
                $tot_mileage += (int)$row2['use_mileage']; // 예치금결제
                $tot_supply  += (int)$row2['supply_price']; // 공급가
                $tot_price   += (int)$row2['goods_price']; // 판매가
                $tot_baesong += (int)$row2['baesong_price']; // 배송비
                $tot_coupon  += (int)$row2['coupon_price']; // 쿠폰할인
                $tot_partner += (int)$psum['sum_pay']; // 가맹점수수료
                $order_idx[] = $row2['index_no'];
                $order_arr['od_id'] = $row2['od_id'];
            }

            $temp_idx = implode(',', $order_idx);
            // 정산액 = (공급가합 + 배송비)
            $tot_seller = ($tot_supply + $tot_baesong);
            // 본사마진 = (판매가 - 공급가 - 가맹점수수료 - 포인트결제 - 예치금결제 - 쿠폰할인)
            $tot_admin = ($tot_price - $tot_supply - $tot_partner - $tot_point - $tot_mileage - $tot_coupon);
            $payTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"mb_id[{$i}]\" value=\"{$row['mb_id']}\">
                        <input type=\"hidden\" name=\"order_idx[{$i}]\" value=\"{$temp_idx}\">
                        <input type=\"hidden\" name=\"tot_price[{$i}]\" value=\"{$tot_price}\">
                        <input type=\"hidden\" name=\"tot_point[{$i}]\" value=\"{$tot_point}\">
                        <input type=\"hidden\" name=\"tot_mileage[{$i}]\" value=\"{$tot_mileage}\">
                        <input type=\"hidden\" name=\"tot_coupon[{$i}]\" value=\"{$tot_coupon}\">
                        <input type=\"hidden\" name=\"tot_baesong[{$i}]\" value=\"{$tot_baesong}\">
                        <input type=\"hidden\" name=\"tot_supply[{$i}]\" value=\"{$tot_supply}\">
                        <input type=\"hidden\" name=\"tot_seller[{$i}]\" value=\"{$tot_seller}\">
                        <input type=\"hidden\" name=\"tot_partner[{$i}]\" value=\"{$tot_partner}\">
                        <input type=\"hidden\" name=\"tot_admin[{$i}]\" value=\"{$tot_admin}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>" . ($num--) . "</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_sideview($row['mb_id'], $row['seller_id']) . "</td>
                    <td class=\"tal\">{$row['company_name']}</td>
                    <td>" . count($order_idx) . "</td>
                    <td class=\"tar\">" . number_format($tot_price) . "</td>
                    <td class=\"tar\">" . number_format($tot_point) . "</td>
                    <td class=\"tar\">" . number_format($tot_mileage) . "</td>
                    <td class=\"tar\">" . number_format($tot_coupon) . "</td>
                    <td class=\"tar\">" . number_format($tot_baesong) . "</td>
                    <td class=\"tar\">" . number_format($tot_supply) . "</td>
                    <td class=\"tar fc_00f bold\">" . number_format($tot_seller) . "</td>
                    <td class=\"tar\">" . number_format($tot_partner) . "</td>
                    <td class=\"tar fc_red bold\">" . number_format($tot_admin) . "</td>
                    <td><a href=\"" . base_url() . "admin/pop_sellerorder?mb_id={$row['mb_id']}&order_idx={$temp_idx}\" onclick=\"win_open(this,'pop_sellerorder','1200','600','yes');return false;\" class=\"btn_small\">내역</a></td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $payTable .= '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';


        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'sflOpt' => $sflOpt,
            'stx' => $stx,
            'get_search_date' => $get_search_date,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'payTable' => $payTable,
            'pagination' => $pagination
        );
        $this->load->view('admin/seller/pay', $data);
    }
    private function seller_pay_history($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $jqueryUiDatePick = $this->admin_template->datePicker();

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_seller_cal a, shop_seller b ";
        $sql_search = " where a.mb_id = b.mb_id ";
        $sql_order  = " order by a.index_no desc ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }
        // 기간검색
        if ($fr_date && $to_date)
            $sql_search .= " and a.reg_time between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and a.reg_time between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and a.reg_time between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        // 테이블의 전체 레코드수만 얻음
        $sql = "select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = "select a.*, b.seller_code, b.company_name $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $btn_frmline = '
            <input type="submit" name="act_button" value="선택삭제" class="btn_lsmall bx-white" onclick="document.pressed=this.value">
            <a href="' . base_url() . 'AdminProcess/seller_pay_history_excel?' . $q1 . '" class="btn_lsmall bx-white"><i class="fa fa-file-excel-o"></i> 엑셀저장</a>';
        $sflOpt = array('', '', '');
        $sflOpt[0] = option_selected('b.company_name', $sfl, '입점사명');
        $sflOpt[1] = option_selected('b.seller_code', $sfl, '업체코드');
        $sflOpt[2] = option_selected('a.mb_id', $sfl, '아이디');
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $i = 0;
        $pay_historyTag = "";
        foreach ($result as $key => $row) {
            $order_idx = explode(',', $row['order_idx']);

            $bg = 'list' . ($i % 2);
            $pay_historyTag .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"index_no[{$i}]\" value=\"{$row['index_no']}\">
                        <input type=\"hidden\" name=\"order_idx[{$i}]\" value=\"{$row['order_idx']}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_sideview($row['mb_id'], $row['seller_code']) . "</td>
                    <td class=\"tal\">{$row['company_name']}</td>
                    <td>{$row['reg_time']}</td>
                    <td>" . count($order_idx) . "</td>
                    <td class=\"tar\">" . number_format($row['tot_price']) . "</td>
                    <td class=\"tar\">" . number_format($row['tot_point']) . "</td>
                    <td class=\"tar\">" . number_format($row['tot_mileage']) . "</td>
                    <td class=\"tar\">" . number_format($row['tot_coupon']) . "</td>
                    <td class=\"tar\">" . number_format($row['tot_baesong']) . "</td>
                    <td class=\"tar\">" . number_format($row['tot_supply']) . "</td>
                    <td class=\"tar fc_00f bold\">" . number_format($row['tot_seller']) . "</td>
                    <td class=\"tar\">" . number_format($row['tot_partner']) . "</td>
                    <td class=\"tar fc_red bold\">" . number_format($row['tot_admin']) . "</td>
                    <td><a href=\"" . base_url() . "admin/pop_sellerorder?mb_id={$row['mb_id']}&order_idx={$row['order_idx']}\" onclick=\"win_open(this,'pop_sellerorder','1200','600','yes');return false;\" class=\"btn_small\">내역</a></td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $pay_historyTag .= '<tr><td colspan="15" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'btn_frmline' => $btn_frmline,
            'stx' => $stx,
            'sflOpt' => $sflOpt,
            'get_search_date' => $get_search_date,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'pay_historyTag' => $pay_historyTag,
            'pagination' => $pagination
        );
        $this->load->view('admin/seller/pay_history', $data);
    }
    #공급자(seller) 관련된 함수들 끝
    public function sms()
    {
        $segment = $this->uri->segment(3);
        switch ($segment) {
            case 'sms_member':
                $this->smsMemberSkin();
                break;
            case 'sms_seller':
                $this->smsSellerSkin();
                break;
            case 'sms_user':
                $this->smsUserSkin();
                break;
            default:
                break;
        }
    }
    private function smsMemberSkin()
    {
        $title = "문자전송";
        $this->admin_template->admin_head($title);
        $sms = $this->global_lib->get_sms('admin');
        if (!$sms['cf_sms_use']) {
            alert_close('문자서비스를 사용가능한 설정 상태가 아닙니다.');
        }
        $smsType = ($sms['cf_sms_type'] == 'LMS' ? 90 : 80);
        $smsReCall = replace_tel($sms['cf_sms_recall']);
        $sql = "select * from shop_member_grade where gb_name != '' and gb_no != '1' order by gb_no";
        $res = $this->query_model->returnArr($sql);
        $optList = "";
        foreach ($res as $key => $row) {
            $optList .= "<option value='$row[gb_no]'>$row[gb_name]</option>";
        }
        $smsTypeJs = "";
        if ($sms['cf_sms_type'] == 'LMS') {
            $smsTypeJs = "
            if(cnt > 90)
                max_bytes.innerHTML = 2000;
            else
                max_bytes.innerHTML = 90;
        
            if(cnt > 2000){
                exceed = cnt - 2000;
                alert('메시지 내용은 2000바이트를 넘을수 없습니다.작성하신 메세지 내용은 '+ exceed +'byte가 초과되었습니다.초과된 부분은 자동으로 삭제됩니다.');
                var tcnt = 0;
                var xcnt = 0;
                var tmp = conts.value;
                for(i=0; i<tmp.length; i++)
                {
                    ch = tmp.charAt(i);
                    if(escape(ch).length > 4) {
                        tcnt += 2;
                    } else {
                        tcnt += 1;
                    }
        
                    if(tcnt > 2000) {
                        tmp = tmp.substring(0,i);
                        break;
                    } else {
                        xcnt = tcnt;
                    }
                }
                conts.value = tmp;
                bytes.innerHTML = xcnt;
                return;
            }";
        } else {
            $smsTypeJs = "
            if(cnt > 80){
                exceed = cnt - 80;
                alert('메시지 내용은 80바이트를 넘을수 없습니다.작성하신 메세지 내용은 '+ exceed +'byte가 초과되었습니다.초과된 부분은 자동으로 삭제됩니다.');
                var tcnt = 0;
                var xcnt = 0;
                var tmp = conts.value;
                for(i=0; i<tmp.length; i++)
                {
                    ch = tmp.charAt(i);
                    if(escape(ch).length > 4) {
                        tcnt += 2;
                    } else {
                        tcnt += 1;
                    }
        
                    if(tcnt > 80) {
                        tmp = tmp.substring(0,i);
                        break;
                    } else {
                        xcnt = tcnt;
                    }
                }
                conts.value = tmp;
                bytes.innerHTML = xcnt;
                return;
            }";
        }
        $data = array('title' => $title, 'smsType' => $smsType, 'smsReCall' => $smsReCall, 'optList' => $optList, 'smsTypeJs' => $smsTypeJs);
        $this->load->view('admin/sms/sms_member', $data);
        $this->admin_template->admin_footer_sub();
    }
    private function smsSellerSkin()
    {
        $title = "문자전송";
        $this->admin_template->admin_head($title);
        $sms = $this->global_lib->get_sms('admin');
        if (!$sms['cf_sms_use']) {
            alert_close('문자서비스를 사용가능한 설정 상태가 아닙니다.');
        }
        $smsType = ($sms['cf_sms_type'] == 'LMS' ? 90 : 80);
        $sms_recall = replace_tel($sms['cf_sms_recall']);

        $smsTypeJs = "";
        if ($sms['cf_sms_type'] == 'LMS') {
            $smsTypeJs = "
            if(cnt > 90)
                max_bytes.innerHTML = 2000;
            else
                max_bytes.innerHTML = 90;
        
            if(cnt > 2000){
                exceed = cnt - 2000;
                alert('메시지 내용은 2000바이트를 넘을수 없습니다.작성하신 메세지 내용은 '+ exceed +'byte가 초과되었습니다.초과된 부분은 자동으로 삭제됩니다.');
                var tcnt = 0;
                var xcnt = 0;
                var tmp = conts.value;
                for(i=0; i<tmp.length; i++){
                    ch = tmp.charAt(i);
                    if(escape(ch).length > 4) {
                        tcnt += 2;
                    } else {
                        tcnt += 1;
                    }
        
                    if(tcnt > 2000) {
                        tmp = tmp.substring(0,i);
                        break;
                    } else {
                        xcnt = tcnt;
                    }
                }
                conts.value = tmp;
                bytes.innerHTML = xcnt;
                return;
            }";
        } else {
            $smsTypeJs = "
            if(cnt > 80){
                exceed = cnt - 80;
                alert('메시지 내용은 80바이트를 넘을수 없습니다.작성하신 메세지 내용은 '+ exceed +'byte가 초과되었습니다.초과된 부분은 자동으로 삭제됩니다.');
                var tcnt = 0;
                var xcnt = 0;
                var tmp = conts.value;
                for(i=0; i<tmp.length; i++)
                {
                    ch = tmp.charAt(i);
                    if(escape(ch).length > 4) {
                        tcnt += 2;
                    } else {
                        tcnt += 1;
                    }
        
                    if(tcnt > 80) {
                        tmp = tmp.substring(0,i);
                        break;
                    } else {
                        xcnt = tcnt;
                    }
                }
                conts.value = tmp;
                bytes.innerHTML = xcnt;
                return;
            }";
        }
        $data = array('title' => $title, 'smsType' => $smsType, 'sms_recall' => $sms_recall, 'smsTypeJs' => $smsTypeJs);
        $this->load->view('admin/sms/sms_seller', $data);
        $this->admin_template->admin_footer_sub();
    }
    private function smsUserSkin()
    {
        $title = "문자전송";
        $this->admin_template->admin_head($title);
        $sms = $this->global_lib->get_sms('admin');
        if (!$sms['cf_sms_use']) {
            alert_close('문자서비스를 사용가능한 설정 상태가 아닙니다.');
        }
        $ph = $this->input->get('ph');
        $cf_sms_typeNum = ($sms['cf_sms_type'] == 'LMS' ? 90 : 80);
        $cf_sms_recalStr = replace_tel($sms['cf_sms_recall']);
        $cf_sms_typeJs = "";
        if ($sms['cf_sms_type'] == 'LMS') {
            $cf_sms_typeJs = "
            if(cnt > 90)
                max_bytes.innerHTML = 2000;
            else
                max_bytes.innerHTML = 90;
        
            if(cnt > 2000){
                exceed = cnt - 2000;
                alert('메시지 내용은 2000바이트를 넘을수 없습니다.\\n\\n작성하신 메세지 내용은 '+ exceed +'byte가 초과되었습니다.\\n\\n초과된 부분은 자동으로 삭제됩니다.');
                var tcnt = 0;
                var xcnt = 0;
                var tmp = conts.value;
                for(i=0; i<tmp.length; i++)
                {
                    ch = tmp.charAt(i);
                    if(escape(ch).length > 4) {
                        tcnt += 2;
                    } else {
                        tcnt += 1;
                    }
        
                    if(tcnt > 2000) {
                        tmp = tmp.substring(0,i);
                        break;
                    } else {
                        xcnt = tcnt;
                    }
                }
                conts.value = tmp;
                bytes.innerHTML = xcnt;
                return;
            }";
        } else {
            $cf_sms_typeJs = "
            if(cnt > 80){
                exceed = cnt - 80;
                alert('메시지 내용은 80바이트를 넘을수 없습니다.\\n\\n작성하신 메세지 내용은 '+ exceed +'byte가 초과되었습니다.\\n\\n초과된 부분은 자동으로 삭제됩니다.');
                var tcnt = 0;
                var xcnt = 0;
                var tmp = conts.value;
                for(i=0; i<tmp.length; i++){
                    ch = tmp.charAt(i);
                    if(escape(ch).length > 4) {
                        tcnt += 2;
                    } else {
                        tcnt += 1;
                    }
        
                    if(tcnt > 80) {
                        tmp = tmp.substring(0,i);
                        break;
                    } else {
                        xcnt = tcnt;
                    }
                }
                conts.value = tmp;
                bytes.innerHTML = xcnt;
                return;
            }";
        }
        $data = array('ph' => $ph, 'title' => $title, 'cf_sms_typeNum' => $cf_sms_typeNum, 'cf_sms_recalStr' => $cf_sms_recalStr, 'cf_sms_typeJs' => $cf_sms_typeJs);
        $this->load->view('admin/sms/sms_user', $data);
    }
    public function seller_reglist()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $title = '회원선택';
        $this->admin_template->admin_head($title);

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $query_string = $qstr;
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_member a left join shop_seller b on (a.id=b.mb_id) ";
        $sql_search = " where a.grade <> '1' and a.supply='' and b.mb_id is null ";

        if ($sfl) {
            $sql_search .= " and a.id like '%$sfl%' ";
        }

        if ($stx) {
            $sql_search .= " and a.name like '%$stx%' ";
        }

        if (!$orderby) {
            $filed = "a.index_no";
            $sod = "desc";
        } else {
            $sod = $orderby;
        }

        $sql_order = " order by $filed $sod ";

        $sql = " select count(a.id) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select a.* $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $aId = $this->gvcomplex_lib->subject_sort_link('a.id', $q2);
        $aName = $this->gvcomplex_lib->subject_sort_link('a.name', $q2);
        $aRegTime = $this->gvcomplex_lib->subject_sort_link('a.reg_time', $q2);

        $i = 0;
        $sellerTable = "";
        foreach ($result as $key => $row) {
            if ($i == 0)
                $sellerTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $sellerTable .= "
            <tr class=\"{$bg}\">
                <td>" . $num-- . "</td>
                <td class=\"tal\">{$row['id']}</td>
                <td class=\"tal\">{$row['name']}</td>
                <td>" . substr($row['reg_time'], 0, 10) . "</td>
                <td><button type=\"button\" onClick=\"yes('{$row['id']}')\" class=\"btn_small grey\">선택</button></td>
            </tr>";
            $i++;
        }
        if ($i == 0)
            $sellerTable .= '<tbody><tr><td colspan="5" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'title' => $title,
            'sfl' => $sfl,
            'stx' => $stx,
            'total_count' => $total_count,
            'aId' => $aId,
            'aName' => $aName,
            'aRegTime' => $aRegTime,
            'sellerTable' => $sellerTable,
            'pagination' => $pagination
        );
        $this->load->view('admin/seller/seller_reglist', $data);
    }

    #팝업관련된(pop_) 함수들 시작
    public function pop_sellerorder()
    {
        $this->admin_template->admin_access();
        $title = "입점사판매내역";
        $this->admin_template->admin_head($title);
        $this->pop_sellerorderSkin();
        $this->admin_template->admin_footer_sub();
    }

    private function pop_sellerorderSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $jqueryUiDatePick = $this->admin_template->datePicker();

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $mb_id = $this->input->get('mb_id');
        $order_idx = $this->input->get('order_idx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $od_status = $this->input->get('od_status');
        $od_final = $this->input->get('od_final');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');

        $title = "입점사판매내역";

        $sr = $this->global_lib->get_seller($mb_id, 'seller_code');

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        if (isset($order_idx))         $qstr .= "&order_idx=$order_idx";
        if (isset($sel_field))         $qstr .= "&sel_field=$sel_field";
        if (isset($od_settle_case))     $qstr .= "&od_settle_case=" . urlencode($od_settle_case);
        if (isset($od_status))         $qstr .= "&od_status=$od_status";
        if (isset($od_final))         $qstr .= "&od_final=$od_final";
        if (isset($od_taxbill))         $qstr .= "&od_taxbill=$od_taxbill";
        if (isset($od_taxsave))         $qstr .= "&od_taxsave=$od_taxsave";
        if (isset($od_memo))             $qstr .= "&od_memo=$od_memo";
        if (isset($od_shop_memo))     $qstr .= "&od_shop_memo=$od_shop_memo";
        if (isset($od_receipt_point)) $qstr .= "&od_receipt_point=$od_receipt_point";
        if (isset($od_coupon))         $qstr .= "&od_coupon=$od_coupon";
        if (isset($od_escrow))         $qstr .= "&od_escrow=$od_escrow";

        $query_string = "mb_id=$mb_id$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_order ";

        $where = array();
        $where[] = " dan != '0' and seller_id = '{$sr['seller_code']}' ";

        if ($order_idx)
            $where[] = " index_no IN ({$order_idx}) ";

        if ($sfl && $stx)
            $where[] = " $sfl like '%$stx%' ";

        if ($od_settle_case)
            $where[] = " paymethod = '$od_settle_case' ";

        if (is_numeric($od_status))
            $where[] = " dan = '$od_status' ";

        if (is_numeric($od_final))
            $where[] = " user_ok = '$od_final' ";

        if ($od_taxbill)
            $where[] = " taxbill_yes = 'Y' ";

        if ($od_taxsave)
            $where[] = " taxsave_yes IN ('Y','S') ";

        if ($od_memo)
            $where[] = " memo <> '' ";

        if ($od_shop_memo)
            $where[] = " shop_memo <> '' ";

        if ($od_receipt_point)
            $where[] = " use_point != 0 ";

        if ($od_coupon)
            $where[] = " coupon_price != 0 ";

        if ($od_escrow)
            $where[] = " od_escrow = 1 ";

        if ($fr_date && $to_date)
            $where[] = " left({$sel_field},10) between '$fr_date' and '$to_date' ";
        else if ($fr_date && !$to_date)
            $where[] = " left({$sel_field},10) between '$fr_date' and '$fr_date' ";
        else if (!$fr_date && $to_date)
            $where[] = " left({$sel_field},10) between '$to_date' and '$to_date' ";

        if ($where) {
            $sql_search = ' where ' . implode(' and ', $where);
        }

        $sql_group = " group by od_id ";
        $sql_order = " order by index_no desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql = " select od_id {$sql_common} {$sql_search} {$sql_group} ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $total_count = $resultObj->num_rows();
        $result = $resultObj->result_array();
        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * {$sql_common} {$sql_search} {$sql_group} {$sql_order} limit {$from_record}, {$rows} ";
        $result = $this->query_model->returnArr($sql);

        $tot_orderprice  = 0; // 총 결제금액
        $sql = " select od_id {$sql_common} {$sql_search} {$sql_group} {$sql_order} ";
        $res = $this->query_model->returnArr($sql);
        foreach ($res as $key => $row) {
            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $tot_orderprice += $amount['buyprice'];
        }
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $mb_pg_anchor = $this->funcomplex_lib->mb_pg_anchor($mb_id);
        $sflOpt = array('', '', '', '', '', '', '', '', '', '', '');
        $sflOpt[0] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt[1] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt[2] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt[3] = option_selected('name', $sfl, '주문자명');
        $sflOpt[4] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt[5] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt[6] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt[7] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt[8] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt[9] = option_selected('delivery_no', $sfl, '운송장번호');
        $sflOpt[10] = option_selected('pt_id', $sfl, '가맹점ID');
        $sel_fieldOpt = array('', '', '', '', '', '', '', '', '');
        $sel_fieldOpt[0] = option_selected('od_time', $sel_field, "주문일");
        $sel_fieldOpt[1] = option_selected('receipt_time', $sel_field, "입금완료일");
        $sel_fieldOpt[2] = option_selected('delivery_date', $sel_field, "배송일");
        $sel_fieldOpt[3] = option_selected('invoice_date', $sel_field, "배송완료일");
        $sel_fieldOpt[4] = option_selected('user_date', $sel_field, "구매확정일");
        $sel_fieldOpt[5] = option_selected('cancel_date', $sel_field, "주문취소일");
        $sel_fieldOpt[6] = option_selected('change_date', $sel_field, "교환완료일");
        $sel_fieldOpt[7] = option_selected('return_date', $sel_field, "반품완료일");
        $sel_fieldOpt[8] = option_selected('refund_date', $sel_field, "환불완료일");
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);
        $payMethod = array('', '', '', '', '', '', '', '');
        $payMethod[0] = radio_checked('od_settle_case', $od_settle_case,  '', '전체');
        $payMethod[1] = radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
        $payMethod[2] = radio_checked('od_settle_case', $od_settle_case, '가상계좌', '가상계좌');
        $payMethod[3] = radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
        $payMethod[4] = radio_checked('od_settle_case', $od_settle_case, '휴대폰', '휴대폰');
        $payMethod[5] = radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
        $payMethod[6] = radio_checked('od_settle_case', $od_settle_case, '간편결제', 'PG간편결제');
        $payMethod[7] = radio_checked('od_settle_case', $od_settle_case, 'KAKAOPAY', 'KAKAOPAY');

        $gw_status = $this->config->item('gw_status');
        $od_statusArr = array('', '', '', '', '', '', '', '', '', '');
        $od_statusArr[0] = radio_checked('od_status', $od_status,  '', '전체');
        $od_statusArr[1] = radio_checked('od_status', $od_status, '1', $gw_status[1]);
        $od_statusArr[2] = radio_checked('od_status', $od_status, '2', $gw_status[2]);
        $od_statusArr[3] = radio_checked('od_status', $od_status, '3', $gw_status[3]);
        $od_statusArr[4] = radio_checked('od_status', $od_status, '4', $gw_status[4]);
        $od_statusArr[5] = radio_checked('od_status', $od_status, '5', $gw_status[5]);
        $od_statusArr[6] = radio_checked('od_status', $od_status, '6', $gw_status[6]);
        $od_statusArr[7] = radio_checked('od_status', $od_status, '7', $gw_status[7]);
        $od_statusArr[8] = radio_checked('od_status', $od_status, '8', $gw_status[8]);
        $od_statusArr[9] = radio_checked('od_status', $od_status, '9', $gw_status[9]);
        $od_finalArr = array('', '', '');
        $od_finalArr[0] = radio_checked('od_final', $od_final,  '', '전체');
        $od_finalArr[1] = radio_checked('od_final', $od_final, '1', '구매확정');
        $od_finalArr[2] = radio_checked('od_final', $od_final, '0', '구매미확정');

        $ectSelectChk = array('', '', '', '', '', '', '');
        $ectSelectChk[0] = check_checked('od_taxbill', $od_taxbill, 'Y', '세금계산서');
        $ectSelectChk[1] = check_checked('od_taxsave', $od_taxsave, 'Y', '현금영수증');
        $ectSelectChk[2] = check_checked('od_memo', $od_memo, 'Y', '배송메세지');
        $ectSelectChk[3] = check_checked('od_shop_memo', $od_shop_memo, 'Y', '관리자메모');
        $ectSelectChk[4] = check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
        $ectSelectChk[5] = check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
        $ectSelectChk[6] = check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

        $i = 0;
        $popSellerOrderTb = "";
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->get_order_list($row, $amount);

            $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
            $param = array($row['od_id']);
            $resObj = $this->query_model->returnQueryObj($sql, $param);
            $res = $resObj->result_array();
            $rowspan = $resObj->num_rows();
            $k = 0;

            foreach ($res as $key => $row2) {
                $gs = unserialize($row2['od_goods']);
                $type = isset($gs['type']) ? $gs['type'] : "normal";

                $tot_price     = 0;
                $tot_point     = 0;
                $tot_mileage = 0;
                $tot_coupon     = 0;
                $tot_baesong = 0;
                $tot_supply     = 0;
                $tot_seller     = 0;
                $tot_partner = 0;
                $tot_admin     = 0;

                $psql = "SELECT SUM(pp_pay) as sum_pay
                             FROM shop_partner_pay
                             WHERE pp_rel_table = 'sale'
                             AND pp_rel_id = '{$row2['od_no']}'
                             AND pp_rel_action = '{$row2['od_id']}' ";
                $psum = $this->query_model->returnOneArr($psql);

                $tot_point   = (int)$row2['use_point']; // 포인트결제
                $tot_mileage = (int)$row2['use_mileage']; // 예치금결제
                $tot_supply  = (int)$row2['supply_price']; // 공급가
                $tot_price   = (int)$row2['goods_price']; // 판매가
                $tot_baesong = (int)$row2['baesong_price']; // 배송비
                $tot_coupon  = (int)$row2['coupon_price']; // 쿠폰할인
                $tot_partner = (int)$psum['sum_pay']; // 가맹점수수료

                // 본사마진 = (판매가 - 공급가 - 가맹점수수료 - 포인트결제 - 예치금결제 - 쿠폰할인)
                $tot_admin = ($tot_price - $tot_supply - $tot_partner - $tot_point - $tot_mileage - $tot_coupon);

                $popSellerOrderTb .= "
                    <tr class=\"{$bg}\">";
                if ($k == 0) {
                    $popSellerOrderTb .= "
                            <td rowspan=\"{$rowspan}\">" . ($num--) . "</td>
                            <td rowspan=\"{$rowspan}\">
                                <a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a><br>
                                " . substr($row['od_time'], 2, 14) . " (" . get_yoil($row['od_time']) . ")
                            </td>";
                }
                $popSellerOrderTb .= "
                        <td class=\"td_img\"><a href=\"" . base_url() . "shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30) . "</a></td>
                        <td class=\"td_itname\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row2['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></td>
                        <td>{$gw_status[$row2['dan']]}</td>
                        <td>" . ($row2['sellerpay_yes'] ? '완료' : '대기') . "</td>
                        <td class=\"tar\">" . number_format($row2['supply_price']) . "</td>";
                if ($k == 0) {
                    $popSellerOrderTb .= "
                            <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>
                            <td rowspan=\"{$rowspan}\" class=\"td_price\">" . number_format($tot_admin) . "</td>
                            <td rowspan=\"{$rowspan}\">{$sodr['disp_paytype']}</td>
                            <td rowspan=\"{$rowspan}\">
                                {$sodr['disp_od_name']}
                                {$sodr['disp_mb_id']}
                            </td>";
                }
                $popSellerOrderTb .= "
                    </tr>";
                $k++;
            }
            $i++;
        }
        if ($i == 0)
            $popSellerOrderTb .= '<tr><td colspan="10" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'title' => $title,
            'mb_pg_anchor' => $mb_pg_anchor,
            'mb_id' => $mb_id,
            'order_idx' => $order_idx,
            'sflOpt' => $sflOpt,
            'stx' => $stx,
            'sel_fieldOpt' => $sel_fieldOpt,
            'get_search_date' => $get_search_date,
            'payMethod' => $payMethod,
            'od_statusArr' => $od_statusArr,
            'od_finalArr' => $od_finalArr,
            'total_count' => $total_count,
            'tot_orderprice' => $tot_orderprice,
            'popSellerOrderTb' => $popSellerOrderTb,
            'pagination' => $pagination,
            'ectSelectChk' => $ectSelectChk
        );
        $this->load->view('admin/pop/sellerorder', $data);
    }
    public function pop_orderform()
    {
        $od_id = $this->input->get('od_id');
        $sql = " select * from shop_order where od_id = ?";
        $param = array($od_id);
        $od = $this->query_model->returnOneArr($sql, $param);
        if (!$od['od_id']) {
            alert_close("주문서가 존재하지 않습니다.");
        }

        $od['mb_id'] = $od['mb_id'] ? $od['mb_id'] : "비회원";

        $amount = $this->global_lib->get_order_spay($od_id); // 결제정보 합계
        $default = $this->funcomplex_lib->set_partner_value($od['od_settle_pid']); // 가맹점 PG결제 정보

        $title = "주문내역 수정";
        $this->admin_template->admin_head($title);
        $this->pop_orderformSkin();
        $this->admin_template->admin_footer_sub();
    }
    private function pop_orderformSkin()
    {
        $member = $this->load->get_var('member');
        $od_id = $this->input->get('od_id');
        $sql = " select * from shop_order where od_id = ?";
        $param = array($od_id);
        $od = $this->query_model->returnOneArr($sql, $param);
        $od['mb_id'] = $od['mb_id'] ? $od['mb_id'] : "비회원";

        $amount = $this->global_lib->get_order_spay($od_id); // 결제정보 합계
        $default = $this->funcomplex_lib->set_partner_value($od['od_settle_pid']); // 가맹점 PG결제 정보

        $title = "주문내역 수정";
        $pg_anchor = '<ul class="anchor">
            <li><a href="#anc_sodr_list">주문상품 목록</a></li>
            <li><a href="#anc_sodr_pay">주문결제 내역</a></li>
            <li><a href="#anc_sodr_memo">관리자메모</a></li>
            <li><a href="#anc_sodr_addr">주문자/배송지 정보</a></li>
            </ul>';
        $yoil = get_yoil($od['od_time']);

        $chk_cnt    = 0; // 전체 배열
        $chk_count1 = 0; // 입금대기 수
        $chk_count2 = 0; // 입금완료 수
        $chk_count5 = 0; // 배송완료 수
        $chk_cancel = 0; // 클래임 수
        $sum_point  = 0; // 포인트적립

        $sql = " select * from shop_order where od_id = ? order by od_time desc, index_no asc ";
        $param = array($od_id);
        $result = $this->query_model->returnArr($sql, $param);
        $i = 0;
        $orderformList = "";
        foreach ($result as $key => $row) {
            $gs = unserialize($row['od_goods']);
            $type = isset($gs['type']) ? $gs['type'] : "normal";

            $it_options = $this->funcomplex_lib->print_complete_options($row['gs_id'], $row['od_id']);
            if ($it_options) {
                $it_options = '<div class="sod_opt">' . $it_options . '</div>';
            }
            // 취소.반품.환불 외
            if (!in_array($row['dan'], array(6, 7, 9))) {
                $sum_point += (int)$row['sum_point'];
            }
            $bg = 'list' . ($i % 2);
            $gs['simg1'] = isset($gs['simg1']) ? $gs['simg1'] : "";
            $gs['gname'] = isset($gs['gname']) ? $gs['gname'] : "";
            $gs['notax'] = isset($gs['notax']) ? $gs['notax'] : "";
            $orderformList .= "
                    <tr class=\"{$bg}\">
                        <td>
                            <input type=\"hidden\" name=\"od_no[{$i}]\" value=\"{$row['od_no']}\">
                            <input type=\"hidden\" name=\"current_status[{$i}]\" value=\"{$row['dan']}\">
                            <label for=\"chk_{$i}\" class=\"sound_only\">{$row['od_id']}</label>
                            <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                        </td>";
            if ($member['grade'] == 99) {
                $orderformList .= "
                        <td>
                            <a href=\"" . base_url() . "shop/view?index_no={$row['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 40, 40) . "</a>
                        </td>";
            }
            $orderformList .= "
                    <td class=\"tal\">
                        <a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a>";
            if ($row['od_tax_flag'] && !$gs['notax'])
                $orderformList . '[비과세상품]';
            $orderformList .= $it_options;
            // 배송준비.배송중.배송완료
            $baesong_run = 0;
            if (in_array($row['dan'], array(3, 4, 5))) {
                $baesong_run++;
                $orderformList .= "
                            <div class=\"frm_info\">
                                " . $this->funcomplex_lib->get_delivery_select("delivery[" . $i . "]", $row['delivery']) . "
                                <input type=\"text\" name=\"delivery_no[{$i}]\" value=\"{$row['delivery_no']}\" class=\"frm_input w130\" placeholder=\"개별 운송장번호\">
                                " . get_delivery_inquiry($row['delivery'], $row['delivery_no'], 'btn_ssmall') . "
                            </div>";
            }
            $orderformList .= "
                    </td>
                    <td>" . $this->funcomplex_lib->get_change_select("change_status[" . $i . "]", $row['dan']);
            if (in_array($row['dan'], array(7, 9)) && $row['refund_price'] == 0 && in_array($row['paymethod'], array('신용카드', '계좌이체', 'KAKAOPAY'))) {
                $orderformList .= "
                            <p class=\"padt3\"><a href=\"" . base_url() . "admin/pop_orderpartcancel?od_id={$row['od_id']}&od_no={$row['od_no']}\" class=\"btn_ssmall orderpartcancel red\">PG부분취소</a></p>";
            }
            $orderformList .= "
                    </td>
                    <td>" . $this->funcomplex_lib->get_order_seller_id($row['seller_id']) . "</td>
                    <td>" . number_format($row['sum_qty']) . "</td>
                    <td class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td class=\"tar\">" . number_format($row['baesong_price']) . "</td>
                    <td class=\"tar\">" . number_format($row['coupon_price']) . "</td>
                    <td class=\"tar\">" . number_format($row['use_point']) . "</td>
                    <td class=\"tar\">" . number_format($row['use_mileage']) . "</td>
                    <td class=\"td_price\">" . number_format($row['use_price']) . "</td>
                </tr>";
            $chk_cnt++;
            if ($row['dan'] == 1) $chk_count1++;
            if ($row['dan'] == 2) $chk_count2++;
            if ($row['dan'] == 5) $chk_count5++;

            // 취소.반품.교환.환불 수
            if (in_array($row['dan'], array(6, 7, 8, 9))) {
                $chk_cancel++;
            }
            $i++;
        }
        $completeOdr = "";
        if ($chk_cnt != $chk_cancel) { // 정상 주문건만 노출 
            $completeOdr .= "
                <div class=\"btn_list marb20\">
                    <input type=\"hidden\" name=\"chk_cnt\" value=\"{$chk_cnt}\">
                    <strong class=\"marr5\">선택한 상품을</strong>";
            if ($chk_cnt == $chk_count1) { // 모두 입금대기 상태인가?
                $completeOdr .= "
                    <input type=\"submit\" name=\"act_button\" value=\"입금완료\" class=\"btn_lsmall red\" onclick=\"document.pressed=this.value\">
                    <input type=\"submit\" name=\"act_button\" value=\"주문취소\" class=\"btn_lsmall white\" onclick=\"document.pressed=this.value\">";
            } else {
                $completeOdr .= "
                    <input type=\"submit\" name=\"act_button\" value=\"주문상태저장\" class=\"btn_lsmall red\" onclick=\"document.pressed=this.value\">";
                if ($baesong_run) {
                    $completeOdr .= "
                        <input type=\"submit\" name=\"act_button\" value=\"운송장번호수정\" class=\"btn_lsmall white\" onclick=\"document.pressed=this.value\">";
                }

                if ($chk_cnt == $chk_count2) { // 모두 입금완료 상태인가? 
                    $completeOdr .= "
                        <input type=\"submit\" name=\"act_button\" value=\"입금대기\" class=\"btn_lsmall white\" onclick=\"document.pressed=this.value\">
                        <input type=\"submit\" name=\"act_button\" value=\"전체환불\" class=\"btn_lsmall white\" onclick=\"document.pressed=this.value\">";
                }

                if ($chk_cnt == $chk_count5) { // 모두 배송완료 상태인가?
                    $completeOdr .= "
                        <input type=\"submit\" name=\"act_button\" value=\"전체반품\" class=\"btn_lsmall white\" onclick=\"document.pressed=this.value\">";
                }
            }
            $completeOdr .= "
                </div>";
        }
        $od_test = "";
        if ($od['od_test']) {
            $od_test = "
                <div class=\"od_test_caution\">주의) 이 주문은 테스트용으로 실제 결제가 이루어지지 않았으므로 절대 배송하시면 안됩니다.</div>";
        }

        $od_mod_history = "";
        if ($od['od_mod_history']) {
            $od_mod_history = "
                <section id=\"sodr_qty_log\">
                    <h3>주문 전체취소 처리 내역</h3>
                    <div>
                        " . $this->funcomplex_lib->conv_content($od['od_mod_history'], 0) . "
                    </div>
                </section>";
        }

        // 결제방법
        $s_receipt_way = $od['paymethod'];

        if ($od['paymethod'] == '간편결제') {
            if ($od['od_pg'] == 'lg')
                $s_receipt_way = 'PAYNOW';
            else if ($od['od_pg'] == 'inicis')
                $s_receipt_way = 'KPAY';
            else if ($od['od_pg'] == 'kcp')
                $s_receipt_way = 'PAYCO';
            else
                $s_receipt_way = $od['paymethod'];
        }

        if ($amount['usepoint'] > 0) {
            $s_receipt_way .= "+포인트";
        }
        if ($amount['usemileage'] > 0) {
            $s_receipt_way .= "+예치금";
        }
        $amBuyprice = display_price($amount['buyprice']);
        $amPrice = display_price($amount['price']);
        $refundMileagePrice = display_price($amount['refund_mileage_price']);
        $amBaesong = display_price($amount['baesong']);
        $amCoupon = display_price($amount['coupon']);
        $amUsepoint = display_price($amount['usepoint']);
        $amUsemileage = display_price($amount['usemileage']);
        $amUseprice = display_price($amount['useprice']);
        $amRefund = display_price($amount['cancel']);
        $sumPoint = display_price($sum_point);

        $odYoil = get_yoil($od['od_time']);
        $odChanel = $od['od_mobile'] ? '모바일' : 'PC';
        $chkCnt5 = $chk_count5 ? ' readonly' : '';


        $payMoney = "";
        if (in_array($od['paymethod'], array('무통장', '가상계좌', '계좌이체'))) {
            if ($od['paymethod'] == '무통장')
                $bank_account = $this->funcomplex_lib->get_bank_account("bank", $od['bank']);
            else if ($od['paymethod'] == '가상계좌')
                $bank_account = $od['bank'] . '<input type="hidden" name="bank" value="' . $od['bank'] . '">';
            else if ($od['paymethod'] == '계좌이체')
                $bank_account = $od['paymethod'];
            if (in_array($od['paymethod'], array('무통장', '가상계좌'))) {
                $payMoney .= "
                    <tr>
                        <th scope=\"row\"><label for=\"bank\">계좌번호</label></th>
                        <td>{$bank_account}</td>
                    </tr>";
            }
            $payMoney .= "
                <tr>
                    <th scope=\"row\"><label for=\"deposit_name\">입금자명</label></th>
                    <td><input type=\"text\" name=\"deposit_name\" value=\"" . $this->funcomplex_lib->get_text($od['deposit_name']) . "\" id=\"deposit_name\" class=\"frm_input\" placeholder=\"실 입금자명\"></td>
                </tr>
                <tr>
                    <th scope=\"row\">{$od['paymethod']} 입금액</th>
                    <td>" . display_price($amount['useprice']) . "</td>
                </tr>
                <tr>
                    <th scope=\"row\">입금확인일시</th>
                    <td>";
            if (is_null_time($od['receipt_time']))
                $payMoney .= '입금 확인일시 정보가 없습니다.';
            else
                $payMoney .= $od['receipt_time'] . ' (' . get_yoil($od['receipt_time']) . ')';
            $payMoney .= "
                    </td>
                </tr>";
        }
        $payPhone = "";
        if ($od['paymethod'] == '휴대폰') {
            $payPhone .= "
                <tr>
                    <th scope=\"row\">휴대폰번호</th>
                    <td>" . $this->funcomplex_lib->get_text($od['bank']) . "</td>
                </tr>
                <tr>
                    <th scope=\"row\">휴대폰 결제액</th>
                    <td>" . display_price($amount['useprice']) . "</td>
                </tr>
                <tr>
                    <th scope=\"row\">결제 확인일시</th>
                    <td>";
            if (is_null_time($od['receipt_time']))
                $payPhone .= '결제 확인일시 정보가 없습니다.';
            else
                $payPhone .= $od['receipt_time'] . ' (' . get_yoil($od['receipt_time']) . ')';
            $payPhone .= "
                    </td>
                </tr>";
        }
        $payCredit = "";
        if ($od['paymethod'] == '신용카드') {
            $payCredit .= "
                <tr>
                    <th scope=\"row\" class=\"sodr_sppay\">신용카드 결제금액</th>
                    <td>";
            if (is_null_time($od['receipt_time']))
                $payCredit .= '0원';
            else
                $payCredit .= display_price($amount['useprice']);
            $payCredit .= "
                    </td>
                </tr>
                <tr>
                    <th scope=\"row\" class=\"sodr_sppay\">카드 승인일시</th>
                    <td>";
            if (is_null_time($od['receipt_time']))
                $payCredit .= '신용카드 결제 일시 정보가 없습니다.';
            else
                $payCredit .= substr($od['receipt_time'], 0, 20);
            $payCredit .= "
                    </td>
                </tr>";
        }
        $payKakao = "";
        if ($od['paymethod'] == 'KAKAOPAY') {
            $payKakao .= "
                <tr>
                    <th scope=\"row\" class=\"sodr_sppay\">KAKOPAY 결제금액</th>
                    <td>";
            if (is_null_time($od['receipt_time']))
                $payKakao .= '0원';
            else
                $payKakao .= display_price($amount['useprice']);
            $payKakao .= "
                    </td>
                </tr>
                <tr>
                    <th scope=\"row\" class=\"sodr_sppay\">KAKAOPAY 승인일시</th>
                    <td>";
            if (is_null_time($od['receipt_time']))
                $payKakao .= '신용카드 결제 일시 정보가 없습니다.';
            else
                $payKakao .= substr($od['receipt_time'], 0, 20);
            $payKakao .= "
                    </td>
                </tr>";
        }
        $payApi = "";
        if ($od['paymethod'] == '간편결제' || ($od['od_pg'] == 'inicis' && $od['paymethod'] == '삼성페이')) {
            $payApi .= "
                <tr>
                    <th scope=\"row\" class=\"sodr_sppay\">{$s_receipt_way} 결제금액</th>
                    <td>";
            if (is_null_time($od['receipt_time']))
                $payApi .= '0원';
            else
                $payApi .= display_price($amount['useprice']);
            $payApi .= "
                    </td>
                </tr>
                <tr>
                    <th scope=\"row\" class=\"sodr_sppay\">{$s_receipt_way} 승인일시</th>
                    <td>";
            if (is_null_time($od['receipt_time']))
                $payApi .= $s_receipt_way . ' 결제 일시 정보가 없습니다.';
            else
                $payApi .= substr($od['receipt_time'], 0, 20);
            $payApi .= "
                    </td>
                </tr>";
        }
        $payNoMoney = "";
        if (!in_array($od['paymethod'], array('무통장', '포인트'))) {
            $payNoMoney .= "
                <tr>
                    <th scope=\"row\">결제대행사 링크</th>
                    <td>";
            switch ($od['od_pg']) {
                case 'lg':
                    $pg_url  = 'http://pgweb.uplus.co.kr';
                    $pg_test = 'LG유플러스 ';
                    if ($od['od_test']) {
                        $pg_url = 'http://pgweb.uplus.co.kr/tmert';
                        $pg_test .= '테스트 ';
                    }
                    break;
                case 'inicis':
                    $pg_url  = 'https://iniweb.inicis.com/';
                    $pg_test = 'KG이니시스 ';
                    break;
                case 'KAKAOPAY':
                    $pg_url  = 'https://mms.cnspay.co.kr';
                    $pg_test = 'KAKAOPAY ';
                    break;
                case 'kcp':
                    $pg_url  = 'http://admin8.kcp.co.kr';
                    $pg_test = 'NHN KCP ';
                    if ($od['od_test']) {
                        // 로그인 아이디 / 비번
                        // 일반 : test1234 / test12345
                        // 에스크로 : escrow / escrow913
                        $pg_url = 'http://testadmin8.kcp.co.kr';
                        $pg_test .= '테스트 ';
                    }
                    break;
            }
            $payNoMoney .= "<a href=\"{$pg_url}\" target=\"_blank\" class=\"btn_small blue\">{$pg_test}바로가기</a>";
            //------------------------------------------------------------------------------
            $payNoMoney .= "
                    </td>
                </tr>";
        }
        $taxYes = "";
        if ($od['taxsave_yes'] == 'S' || $od['taxsave_yes'] == 'Y') {
            $taxYes .= "
                <tr>
                    <th scope=\"row\">현금영수증 신청여부</th>
                    <td class=\"lh4\">";
            if ($od['taxsave_yes'] == 'S') {
                $taxYes .= "
                            사업자 지출증빙용<br>
                            사업자번호 : {$od['tax_saupja_no']}";
            } else if ($od['taxsave_yes'] == 'Y') {
                $taxYes .= "
                            개인 소득공제용<br>
                            핸드폰 : {$od['tax_hp']}";
            }
            $taxYes .= "
                    </td>
                </tr>";
        }
        $taxBillYes = "";
        if ($od['taxbill_yes'] == 'Y') {
            $taxBillYes = "
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

        $odName = $this->funcomplex_lib->get_text($od['name']);
        $odTel = $this->funcomplex_lib->get_text($od['telephone']);
        $odCell = $this->funcomplex_lib->get_text($od['cellphone']);
        $odAddr1 = $this->funcomplex_lib->get_text($od['addr1']);
        $odAddr2 = $this->funcomplex_lib->get_text($od['addr2']);
        $odAddr3 = $this->funcomplex_lib->get_text($od['addr3']);
        $odAddrJibeon = $this->funcomplex_lib->get_text($od['addr_jibeon']);

        $odBname = $this->funcomplex_lib->get_text($od['b_name']);
        $odBtell = $this->funcomplex_lib->get_text($od['b_telephone']);
        $odBcell = $this->funcomplex_lib->get_text($od['b_cellphone']);
        $odBAddr1 = $this->funcomplex_lib->get_text($od['b_addr1']);
        $odBAddr2 = $this->funcomplex_lib->get_text($od['b_addr2']);
        $odBAddr3 = $this->funcomplex_lib->get_text($od['b_addr3']);
        $odBAddrJibeon = $this->funcomplex_lib->get_text($od['b_addr_jibeon']);

        $odMeno = isset($od['memo']) ? $this->funcomplex_lib->get_text($od['memo'], 1) : "없음";

        $data = array(
            'title' => $title,
            'pg_anchor' => $pg_anchor,
            'od' => $od,
            'amount' => $amount,
            'yoil' => $yoil,
            'od_id' => $od_id,
            'orderformList' => $orderformList,
            'completeOdr' => $completeOdr,
            'od_test' => $od_test,
            'od_mod_history' => $od_mod_history,
            'amBuyprice' => $amBuyprice,
            'amPrice' => $amPrice,
            'refundMileagePrice' => $refundMileagePrice,
            'amBaesong' => $amBaesong,
            'amCoupon' => $amCoupon,
            'amUsepoint' => $amUsepoint,
            'amUsemileage' => $amUsemileage,
            'amUseprice' => $amUseprice,
            'amRefund' => $amRefund,
            'sumPoint' => $sumPoint,
            'odYoil' => $odYoil,
            'odChanel' => $odChanel,
            'chkCnt5' => $chkCnt5,
            's_receipt_way' => $s_receipt_way,
            'payMoney' => $payMoney,
            'payPhone' => $payPhone,
            'payCredit' => $payCredit,
            'payKakao' => $payKakao,
            'payApi' => $payApi,
            'payNoMoney' => $payNoMoney,
            'taxYes' => $taxYes,
            'taxBillYes' => $taxBillYes,
            'odName' => $odName,
            'odTel' => $odTel,
            'odCell' => $odCell,
            'odAddr1' => $odAddr1,
            'odAddr2' => $odAddr2,
            'odAddr3' => $odAddr3,
            'odAddrJibeon' => $odAddrJibeon,
            'odBname' => $odBname,
            'odBtell' => $odBtell,
            'odBcell' => $odBcell,
            'odBAddr1' => $odBAddr1,
            'odBAddr2' => $odBAddr2,
            'odBAddr3' => $odBAddr3,
            'odBAddrJibeon' => $odBAddrJibeon,
            'odMeno' => $odMeno,
            'member' => $member
        );
        $this->load->view('admin/pop/orderform', $data);
    }
    public function pop_memberform()
    {
        $this->admin_template->admin_access();
        $title = "회원정보수정";
        $this->admin_template->admin_head($title);
        $this->pop_memberformSkin();
        $this->admin_template->admin_footer_sub();
    }
    private function pop_memberformSkin()
    {
        $title = "회원정보수정";
        $mb_id = $this->input->get('mb_id');
        $mb = $this->global_lib->get_member($mb_id);
        $pt = $this->partner_lib->get_partner($mb_id);
        $gw_auth = $this->config->item('gw_auth');

        // 본인확인방법
        switch ($mb['mb_certify']) {
            case 'hp':
                $mb_certify_case = '휴대폰';
                $mb_certify_val = 'hp';
                break;
            case 'ipin':
                $mb_certify_case = '아이핀';
                $mb_certify_val = 'ipin';
                break;
            case 'admin':
                $mb_certify_case = '관리자 수정';
                $mb_certify_val = 'admin';
                break;
            default:
                $mb_certify_case = '';
                $mb_certify_val = 'admin';
                break;
        }

        // 본인확인
        $mb_certify_yes    =  $mb['mb_certify'] ? 'checked="checked"' : '';
        $mb_certify_no    = !$mb['mb_certify'] ? 'checked="checked"' : '';

        // 성인인증
        $mb_adult_yes    =  $mb['mb_adult']     ? 'checked="checked"' : '';
        $mb_adult_no    = !$mb['mb_adult']     ? 'checked="checked"' : '';
        $mb_pg_anchor = $this->funcomplex_lib->mb_pg_anchor($mb_id);

        $intercept_dateStr = "";
        if ($mb['intercept_date']) {
            $intercept_dateStr = "<strong class=\"fc_red\">[차단된회원]</strong>";
        }
        $mb_certify_ipin = "";
        if ($mb['mb_certify'] == 'ipin') $mb_certify_ipin = 'checked="checked"';

        $mb_certify_hp = "";
        if ($mb['mb_certify'] == 'hp') $mb_certify_hp = 'checked="checked"';

        $mb_genderArr = array('m' => '', 'f' => '');
        $mb_genderArr['m'] = get_checked($mb['gender'], 'M');
        $mb_genderArr['f'] = get_checked($mb['gender'], 'F');
        $mb_gradeStr = $this->common_lib->get_member_select("mb_grade", $mb['grade']);
        $sub_authStr = "";
        for ($i = 0; $i < 5; $i++) {
            $k = ($i + 1);
            $sub_authStr .= "
                <td>
                    <input id=\"auth_{$k}\" type=\"checkbox\" name=\"auth_{$k}\" value=\"1\" " . get_checked($mb['auth_' . $k], '1') . ">
                    <label for=\"auth_{$k}\">{$gw_auth[$i]}</label>
                </td>";
        }
        $sub_authStr2 = "";
        for ($i = 5; $i < 10; $i++) {
            $k = ($i + 1);
            $sub_authStr2 .= "
                <td>
                    <input id=\"auth_{$k}\" type=\"checkbox\" name=\"auth_{$k}\" value=\"1\" " . get_checked($mb['auth_' . $k], '1') . ">
                    <label for=\"auth_{$k}\">{$gw_auth[$i]}</label>
                </td>";
        }
        $themePc = $this->funcomplex_lib->get_theme_select('theme', $mb['theme']);
        $themeMb = $this->funcomplex_lib->get_mobile_theme_select('mobile_theme', $mb['mobile_theme']);

        $payflagArr = array('', '');
        $payflagArr[0] = option_selected('0', $mb['payflag'], '%');
        $payflagArr[1] = option_selected('1', $mb['payflag'], '원');
        $helpArr = array('', '', '');
        $helpArr[0] = help('판매수수료를 개별적으로 추가적립 하실 수 있습니다.');
        $helpArr[1] = help('단독서버인경우만 입력하세요. (포워딩으로 설정된 도메인은 입력금지)');
        $helpArr[2] = help('위 계좌정보는 수수료 정산시 이용 됩니다. 정확히 입력해주세요.');

        $mb_use_pg = get_checked($mb['use_pg'], '1');
        $mb_use_good = get_checked($mb['use_good'], '1');

        $mailser = array('y' => '', 'n' => '');
        $mailser['y'] = get_checked($mb['mailser'], 'Y');
        $mailser['n'] = get_checked($mb['mailser'], 'N');
        $smsser = array('y' => '', 'n' => '');
        $smsser['y'] = get_checked($mb['smsser'], 'Y');
        $smsser['n'] = get_checked($mb['smsser'], 'N');

        $recentLogin = (!is_null_time($mb['today_login'])) ? $mb['today_login'] : '';

        $buyCnt = number_format($this->common_lib->shop_count($mb['id']));
        $totalCnt = number_format($this->common_lib->shop_price($mb['id']));
        $is_partnerJs = "";
        if ($this->funcomplex_lib->is_partner($mb['id'])) {
            $is_partnerJs = "$(\".pt_pay_fld\").show();";
        }
        $mb_gradeOne = "";
        if ($mb['grade'] == 1) {
            $mb_gradeOne = "$(\".mb_adm_fld\").show();";
        }
        $data = array(
            'mb_id' => $mb_id,
            'title' => $title,
            'mb_pg_anchor' => $mb_pg_anchor,
            'mb' => $mb,
            'intercept_dateStr' => $intercept_dateStr,
            'mb_certify_ipin' => $mb_certify_ipin,
            'mb_certify_hp' => $mb_certify_hp,
            'mb_genderArr' => $mb_genderArr,
            'mb_certify_yes' => $mb_certify_yes,
            'mb_certify_no' => $mb_certify_no,
            'mb_adult_yes' => $mb_adult_yes,
            'mb_adult_no' => $mb_adult_no,
            'mb_gradeStr' => $mb_gradeStr,
            'sub_authStr' => $sub_authStr,
            'sub_authStr2' => $sub_authStr2,
            'themePc' => $themePc,
            'themeMb' => $themeMb,
            'payflagArr' => $payflagArr,
            'helpArr' => $helpArr,
            'pt' => $pt,
            'mb_use_pg' => $mb_use_pg,
            'mb_use_good' => $mb_use_good,
            'recentLogin' => $recentLogin,
            'buyCnt' => $buyCnt,
            'totalCnt' => $totalCnt,
            'is_partnerJs' => $is_partnerJs,
            'mb_gradeOne' => $mb_gradeOne,
            'mailser' => $mailser,
            'smsser' => $smsser
        );
        $this->load->view('admin/pop/pop_memberform', $data);
    }
    public function pop_memberorder()
    {
        $this->admin_template->admin_access();
        $title = "주문내역";
        $this->admin_template->admin_head($title);
        $this->pop_memberorderSkin();
        $this->admin_template->admin_footer_sub();
    }
    private function pop_memberorderSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $title = "주문내역";
        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $mb_id = $this->input->get('mb_id');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $od_status = $this->input->get('od_status');
        $od_final = $this->input->get('od_final');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        if (isset($sel_field))         $qstr .= "&sel_field=$sel_field";
        if (isset($od_settle_case))     $qstr .= "&od_settle_case=" . urlencode($od_settle_case);
        if (isset($od_status))         $qstr .= "&od_status=$od_status";
        if (isset($od_final))         $qstr .= "&od_final=$od_final";
        if (isset($od_taxbill))         $qstr .= "&od_taxbill=$od_taxbill";
        if (isset($od_taxsave))         $qstr .= "&od_taxsave=$od_taxsave";
        if (isset($od_memo))             $qstr .= "&od_memo=$od_memo";
        if (isset($od_shop_memo))     $qstr .= "&od_shop_memo=$od_shop_memo";
        if (isset($od_receipt_point)) $qstr .= "&od_receipt_point=$od_receipt_point";
        if (isset($od_coupon))         $qstr .= "&od_coupon=$od_coupon";
        if (isset($od_escrow))         $qstr .= "&od_escrow=$od_escrow";

        $query_string = "mb_id=$mb_id$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_order ";

        $where = array();
        $where[] = " dan != '0' and mb_id = '$mb_id' ";

        if ($sfl && $stx)
            $where[] = " $sfl like '%$stx%' ";

        if ($od_settle_case)
            $where[] = " paymethod = '$od_settle_case' ";

        if (is_numeric($od_status))
            $where[] = " dan = '$od_status' ";

        if (is_numeric($od_final))
            $where[] = " user_ok = '$od_final' ";

        if ($od_taxbill)
            $where[] = " taxbill_yes = 'Y' ";

        if ($od_taxsave)
            $where[] = " taxsave_yes IN ('Y','S') ";

        if ($od_memo)
            $where[] = " memo <> '' ";

        if ($od_shop_memo)
            $where[] = " shop_memo <> '' ";

        if ($od_receipt_point)
            $where[] = " use_point != 0 ";

        if ($od_coupon)
            $where[] = " coupon_price != 0 ";

        if ($od_escrow)
            $where[] = " od_escrow = 1 ";

        if ($fr_date && $to_date)
            $where[] = " left({$sel_field},10) between '$fr_date' and '$to_date' ";
        else if ($fr_date && !$to_date)
            $where[] = " left({$sel_field},10) between '$fr_date' and '$fr_date' ";
        else if (!$fr_date && $to_date)
            $where[] = " left({$sel_field},10) between '$to_date' and '$to_date' ";

        if ($where) {
            $sql_search = ' where ' . implode(' and ', $where);
        }

        $sql_group = " group by od_id ";
        $sql_order = " order by index_no desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql = " select od_id {$sql_common} {$sql_search} {$sql_group} ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $total_count = $resultObj->num_rows();

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * {$sql_common} {$sql_search} {$sql_group} {$sql_order} limit {$from_record}, {$rows} ";
        $result = $this->query_model->returnArr($sql);

        $tot_orderprice  = 0; // 총 결제금액
        $sql = " select od_id {$sql_common} {$sql_search} {$sql_group} {$sql_order} ";
        $res = $this->query_model->returnArr($sql);
        foreach ($res as $key => $row) {
            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $tot_orderprice += $amount['buyprice'];
        }
        $jqueryUiDatePick = $this->admin_template->datePicker();

        $mb_pg_anchor = $this->funcomplex_lib->mb_pg_anchor($mb_id);
        $sflOpt = array('', '', '', '', '', '', '', '', '', '', '');
        $sflOpt[0] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt[1] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt[2] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt[3] = option_selected('name', $sfl, '주문자명');
        $sflOpt[4] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt[5] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt[6] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt[7] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt[8] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt[9] = option_selected('delivery_no', $sfl, '운송장번호');
        $sflOpt[10] = option_selected('pt_id', $sfl, '가맹점ID');


        $sel_fieldOpt = array('', '', '', '', '', '', '', '', '');
        $sel_fieldOpt[0] = option_selected('od_time', $sel_field, "주문일");
        $sel_fieldOpt[1] = option_selected('receipt_time', $sel_field, "입금완료일");
        $sel_fieldOpt[2] = option_selected('delivery_date', $sel_field, "배송일");
        $sel_fieldOpt[3] = option_selected('invoice_date', $sel_field, "배송완료일");
        $sel_fieldOpt[4] = option_selected('user_date', $sel_field, "구매확정일");
        $sel_fieldOpt[5] = option_selected('cancel_date', $sel_field, "주문취소일");
        $sel_fieldOpt[6] = option_selected('change_date', $sel_field, "교환완료일");
        $sel_fieldOpt[7] = option_selected('return_date', $sel_field, "반품완료일");
        $sel_fieldOpt[8] = option_selected('refund_date', $sel_field, "환불완료일");
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);
        $payMethod = array('', '', '', '', '', '', '', '');
        $payMethod[0] = radio_checked('od_settle_case', $od_settle_case,  '', '전체');
        $payMethod[1] = radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
        $payMethod[2] = radio_checked('od_settle_case', $od_settle_case, '가상계좌', '가상계좌');
        $payMethod[3] = radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
        $payMethod[4] = radio_checked('od_settle_case', $od_settle_case, '휴대폰', '휴대폰');
        $payMethod[5] = radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
        $payMethod[6] = radio_checked('od_settle_case', $od_settle_case, '간편결제', 'PG간편결제');
        $payMethod[7] = radio_checked('od_settle_case', $od_settle_case, 'KAKAOPAY', 'KAKAOPAY');
        $gw_status = $this->config->item('gw_status');
        $od_statusArr = array('', '', '', '', '', '', '', '', '', '');
        $od_statusArr[0] = radio_checked('od_status', $od_status,  '', '전체');
        $od_statusArr[1] = radio_checked('od_status', $od_status, '1', $gw_status[1]);
        $od_statusArr[2] = radio_checked('od_status', $od_status, '2', $gw_status[2]);
        $od_statusArr[3] = radio_checked('od_status', $od_status, '3', $gw_status[3]);
        $od_statusArr[4] = radio_checked('od_status', $od_status, '4', $gw_status[4]);
        $od_statusArr[5] = radio_checked('od_status', $od_status, '5', $gw_status[5]);
        $od_statusArr[6] = radio_checked('od_status', $od_status, '6', $gw_status[6]);
        $od_statusArr[7] = radio_checked('od_status', $od_status, '7', $gw_status[7]);
        $od_statusArr[8] = radio_checked('od_status', $od_status, '8', $gw_status[8]);
        $od_statusArr[9] = radio_checked('od_status', $od_status, '9', $gw_status[9]);
        $od_finalArr = array('', '', '');
        $od_finalArr[0] = radio_checked('od_final', $od_final,  '', '전체');
        $od_finalArr[1] = radio_checked('od_final', $od_final, '1', '구매확정');
        $od_finalArr[2] = radio_checked('od_final', $od_final, '0', '구매미확정');
        $ectSelectChk = array('', '', '', '', '', '', '');
        $ectSelectChk[0] = check_checked('od_taxbill', $od_taxbill, 'Y', '세금계산서');
        $ectSelectChk[1] = check_checked('od_taxsave', $od_taxsave, 'Y', '현금영수증');
        $ectSelectChk[2] = check_checked('od_memo', $od_memo, 'Y', '배송메세지');
        $ectSelectChk[3] = check_checked('od_shop_memo', $od_shop_memo, 'Y', '관리자메모');
        $ectSelectChk[4] = check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
        $ectSelectChk[5] = check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
        $ectSelectChk[6] = check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

        $popList = "";
        $i = 0;
        $gw_status = $this->config->item('gw_status');
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->get_order_list($row, $amount);

            $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
            $param = array($row['od_id']);
            $resObj = $this->query_model->returnQueryObj($sql, $param);
            $res = $resObj->result_array();
            $rowspan = $resObj->num_rows();
            $k = 0;
            foreach ($res as $key => $row2) {
                $gs = unserialize($row2['od_goods']);
                $type = isset($gs['type']) ? $gs['type'] : "normal";

                $popList .= "
                    <tr class=\"{$bg}\">";
                if ($k == 0) {
                    $popList .= "
                        <td rowspan=\"{$rowspan}\">" . $num-- . "</td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a><br>
                            " . substr($row['od_time'], 2, 14) . " (" . get_yoil($row['od_time']) . ")
                        </td>";
                }
                $popList .= "
                    <td class=\"td_img\"><a href=\"" . base_url() . "shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30) . "</a></td>
                    <td class=\"td_itname\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row2['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></td>
                    <td>{$gw_status[$row2['dan']]}</td>";
                if ($k == 0) {
                    $popList .= "
                        <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>
                        <td rowspan=\"{$rowspan}\">{$sodr['disp_paytype']}</td>
                        <td rowspan=\"{$rowspan}\">
                            {$sodr['disp_od_name']}
                            {$sodr['disp_mb_id']}
                        </td>";
                }
                $k++;
            }
            $i++;
        }
        if ($i == 0)
            $popList .= '<tr><td colspan="8" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'title' => $title,
            'mb_pg_anchor' => $mb_pg_anchor,
            'mb_id' => $mb_id,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'sflOpt' => $sflOpt,
            'stx' => $stx,
            'sel_fieldOpt' => $sel_fieldOpt,
            'get_search_date' => $get_search_date,
            'payMethod' => $payMethod,
            'od_statusArr' => $od_statusArr,
            'od_finalArr' => $od_finalArr,
            'ectSelectChk' => $ectSelectChk,
            'total_count' => $total_count,
            'tot_orderprice' => $tot_orderprice,
            'popList' => $popList,
            'pagination' => $pagination
        );
        $this->load->view('admin/pop/pop_memberorder', $data);
    }
    public function pop_memberpoint()
    {
        $this->admin_template->admin_access();
        $title = "포인트내역";
        $this->admin_template->admin_head($title);
        $this->pop_memberpointSkin();
        $this->admin_template->admin_footer_sub();
    }
    private function pop_memberpointSkin()
    {
        $title = "포인트내역";
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $mb_id = $this->input->get('mb_id');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $page = $this->input->get('page');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $query_string = "mb_id=$mb_id";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_point ";
        $sql_search = " where mb_id = '$mb_id' ";

        if ($fr_date && $to_date)
            $sql_search .= " and po_datetime between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and po_datetime between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and po_datetime between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        $sql_order = " order by po_id desc ";

        // 테이블의 전체 레코드수만 얻음
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

        $row2 = $this->query_model->returnOneArr("select sum(po_point) as sum_point $sql_common $sql_search ");
        $sum_point = $row2['sum_point'];
        $mb_pg_anchor = $this->funcomplex_lib->mb_pg_anchor($mb_id);
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);
        $pointList = "";
        $i = 0;
        foreach ($result as $key => $row) {
            if ($i == 0)
                $pointList .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $pointList .= "
                <tr class=\"{$bg}\">
                    <td>" . $num-- . "</td>
                    <td class=\"tal\">{$row['po_content']}</td>
                    <td>{$row['po_datetime']}</td>
                    <td class=\"tar\">" . number_format($row['po_point']) . "</td>
                    <td class=\"tar\">" . number_format($row['po_mb_point']) . "</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $pointList .= '<tbody><tr><td colspan="5" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'title' => $title,
            'mb_pg_anchor' => $mb_pg_anchor,
            'mb_id' => $mb_id,
            'get_search_date' => $get_search_date,
            'total_count' => $total_count,
            'sum_point' => $sum_point,
            'pointList' => $pointList,
            'pagination' => $pagination
        );
        $this->load->view('admin/pop/memberpoint', $data);
    }
    public function formmail()
    {
        $is_member = $this->load->get_var('is_member');
        $mb_id = $this->input->get('mb_id');

        if (!$is_member)
            alert_close('회원만 이용하실 수 있습니다.');

        $mb = $this->global_lib->get_member($mb_id);
        if (!$mb['id'])
            alert_close('회원정보가 존재하지 않습니다.\\n\\n탈퇴한 회원일 수 있습니다.');

        $sendmail_count = (int)get_session('ss_sendmail_count') + 1;
        if ($sendmail_count > 3)
            alert_close('한번 접속후 일정수의 메일만 발송할 수 있습니다.\\n\\n계속해서 메일을 보내시려면 다시 로그인 또는 접속하여 주십시오.');

        $tb['title'] = '메일 쓰기';
        $this->load->vars(array('tb' => $tb));
        $this->template->header_sub();
        $this->formmailSkin();
        $this->template->footer_sub();
    }
    private function formmailSkin()
    {
        $member = $this->load->get_var('member');
        $email = $this->input->get('email');
        $name = $this->input->get('name');

        $email_enc = new str_encrypt();
        $email_dec = $email_enc->decrypt($email);

        $email = get_email_address($email_dec);
        if (!$email)
            alert_close('이메일이 올바르지 않습니다.');

        $email = $email_enc->encrypt($email);

        if (!$name)
            $name = $email;
        else
            $name = $this->funcomplex_lib->get_text(stripslashes($name), true);

        if (!isset($type))
            $type = 0;

        $type_checked[0] = $type_checked[1] = $type_checked[2] = "";
        $type_checked[$type] = 'checked';
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        $memberName = $this->funcomplex_lib->get_text($member['name']);
        $data = array('name' => $name, 'email' => $email, 'member' => $member, 'token' => $token, 'memberName' => $memberName);
        $this->load->view('admin/formmail', $data);
    }
    public function pop_sellerform()
    {
        $mb_id = $this->input->get('mb_id');
        $title = "입점사정보수정";
        $this->admin_template->admin_access();
        $this->admin_template->admin_head($title);
        $mb = $this->global_lib->get_member($mb_id);
        $sr = $this->global_lib->get_seller($mb_id);
        $mb_pg_anchor = $this->funcomplex_lib->mb_pg_anchor($mb_id);
        $stataArr = array('', '');
        $stataArr[1] = radio_checked('state', $sr['state'], '1', '승인');
        $stataArr[0] = radio_checked('state', $sr['state'], '0', '대기');
        $seller_openArr = array('', '', '', '', '');
        $seller_openArr[1] = radio_checked('seller_open', $sr['seller_open'], '1', '진열');
        $seller_openArr[2] = radio_checked('seller_open', $sr['seller_open'], '2', '품절');
        $seller_openArr[3] = radio_checked('seller_open', $sr['seller_open'], '3', '단종');
        $seller_openArr[4] = radio_checked('seller_open', $sr['seller_open'], '4', '중지');
        $data = array('title' => $title, 'mb_id' => $mb_id, 'sr' => $sr, 'mb_pg_anchor' => $mb_pg_anchor, 'stataArr' => $stataArr, 'seller_openArr' => $seller_openArr);
        $this->load->view('admin/pop/seller_form', $data);
        $this->admin_template->admin_footer_sub();
    }
    public function pop_orderpartcancel()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $od_id = $this->input->get('od_id');
        $od_no = $this->input->get('od_no');
        $sql = " select * from shop_order where od_id = ? and od_no = ?";
        $param = array($od_id, $od_no);
        $od = $this->query_model->returnOneArr($sql, $param);

        if (!$od['od_id'])
            alert_close('주문정보가 존재하지 않습니다.');

        if ($od['od_pg'] == 'inicis' && $od['paymethod'] == '계좌이체')
            alert_close('KG이니시스는 신용카드만 부분취소가 가능합니다.');

        if ($od['paymethod'] == '계좌이체' && substr($od['receipt_time'], 0, 10) >= $OM_TIME_YMD)
            alert_close('실시간 계좌이체건의 부분취소 요청은 결제일 익일에 가능합니다.');

        $amount = $this->global_lib->get_order_spay($od_id); // 결제정보 합계
        $od_receipt_price = $amount['useprice'] - $amount['refund'];

        if ($od_receipt_price <= 0)
            alert_close('부분취소 처리할 금액이 없습니다.');

        $title = $od['paymethod'] . ' 부분취소';
        $this->admin_template->admin_head($title);
        $od_receipt_priceStr = display_price($od_receipt_price);


        $data = array('od' => $od, 'title' => $title, 'od_id' => $od_id, 'od_no' => $od_no, 'od_receipt_priceStr' => $od_receipt_priceStr);
        $this->load->view('admin/pop/orderpartcancel', $data);
        $this->admin_template->admin_footer_sub();
    }
    public function pop_orderpartcancelupdate()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $tax_mny = $this->input->post('tax_mny');
        $free_mny = $this->input->post('free_mny');
        $mod_memo = $this->input->post('mod_memo');
        $od_id = $this->input->post('od_id');
        $od_no = $this->input->post('od_no');

        $tax_mny = preg_replace('/[^0-9]/', '', $_POST['mod_tax_mny']);
        $free_mny = preg_replace('/[^0-9]/', '', $_POST['mod_free_mny']);

        if (!$tax_mny && !$free_mny)
            alert('과세 취소금액 또는 비과세 취소금액을 입력해 주십시오.');

        if (!trim($mod_memo))
            alert('요청사유를 입력해 주십시오.');

        // 주문정보
        $sql = " select * from shop_order where od_id = ? and od_no = ?";
        $param = array($od_id, $od_no);
        $od = $this->query_model->returnOneArr($sql, $param);

        if (!$od['od_id'])
            alert_close('주문정보가 존재하지 않습니다.');

        if ($od['paymethod'] == '계좌이체' && substr($od['receipt_time'], 0, 10) >= $OM_TIME_YMD)
            alert_close('실시간 계좌이체건의 부분취소 요청은 결제일 익일에 가능합니다.');

        // 금액비교
        $amount = $this->global_lib->get_order_spay($od_id); // 결제정보 합계
        $od_receipt_price = $amount['useprice'] - $amount['refund'];

        if (($tax_mny && $free_mny) && ($tax_mny + $free_mny) > $od_receipt_price)
            alert('과세, 비과세 취소금액의 합을 ' . display_price($od_receipt_price) . ' 이하로 입력해 주십시오.');

        if ($tax_mny && $tax_mny > $od_receipt_price)
            alert('과세 취소금액을 ' . display_price($od_receipt_price) . ' 이하로 입력해 주십시오.');

        if ($free_mny && $free_mny > $od_receipt_price)
            alert('비과세 취소금액을 ' . display_price($od_receipt_price) . ' 이하로 입력해 주십시오.');

        // 가맹점 PG결제 정보
        $default = $this->funcomplex_lib->set_partner_value($od['od_settle_pid']);

        // PG사별 부분취소 실행
        #pg사 관련된 부분은 나중에 처리하기

        if ($od['od_pg'] == 'lg') {

            include_once('./Bill/settle_lg.inc.php');
            /*
                * [결제 부분취소 요청 페이지]
                *
                * LG유플러스으로 부터 내려받은 거래번호(LGD_TID)를 가지고 취소 요청을 합니다.(파라미터 전달시 POST를 사용하세요)
                * (승인시 LG유플러스으로 부터 내려받은 PAYKEY와 혼동하지 마세요.)
                */
            $LGD_TID                      = $od['od_tno']; //LG유플러스으로 부터 내려받은 거래번호(LGD_TID)
            $LGD_CANCELAMOUNT             = (int)$tax_mny; //부분취소 금액
            $LGD_REMAINAMOUNT             = (int)$od_receipt_price;   //취소전 남은금액

            $LGD_CANCELTAXFREEAMOUNT    = (int)$free_mny; //면세대상 부분취소 금액 (과세/면세 혼용상점만 적용)
            $LGD_CANCELREASON             = $mod_memo; //취소사유
            $LGD_RFACCOUNTNUM           = $_POST['LGD_RFACCOUNTNUM']; //환불계좌 번호(가상계좌 환불인경우만 필수)
            $LGD_RFBANKCODE             = $_POST['LGD_RFBANKCODE']; //환불계좌 은행코드(가상계좌 환불인경우만 필수)
            $LGD_RFCUSTOMERNAME         = $_POST['LGD_RFCUSTOMERNAME']; //환불계좌 예금주(가상계좌 환불인경우만 필수)
            $LGD_RFPHONE                = $_POST['LGD_RFPHONE']; //요청자 연락처(가상계좌 환불인경우만 필수)

            $xpay = new XPay($configPath, $CST_PLATFORM);

            // Mert Key 설정
            $xpay->set_config_value('t' . $LGD_MID, $default['de_lg_mert_key']);
            $xpay->set_config_value($LGD_MID, $default['de_lg_mert_key']);

            $xpay->Init_TX($LGD_MID);

            $xpay->Set("LGD_TXNAME",                "PartialCancel");
            $xpay->Set("LGD_TID",                   $LGD_TID);
            $xpay->Set("LGD_CANCELAMOUNT",          $LGD_CANCELAMOUNT);
            $xpay->Set("LGD_REMAINAMOUNT",          $LGD_REMAINAMOUNT);
            $xpay->Set("LGD_CANCELTAXFREEAMOUNT",   $LGD_CANCELTAXFREEAMOUNT);
            $xpay->Set("LGD_CANCELREASON",          $LGD_CANCELREASON);
            $xpay->Set("LGD_RFACCOUNTNUM",          $LGD_RFACCOUNTNUM);
            $xpay->Set("LGD_RFBANKCODE",            $LGD_RFBANKCODE);
            $xpay->Set("LGD_RFCUSTOMERNAME",        $LGD_RFCUSTOMERNAME);
            $xpay->Set("LGD_RFPHONE",               $LGD_RFPHONE);
            $xpay->Set("LGD_REQREMAIN",             "0");
            $xpay->Set("LGD_ENCODING",              "UTF-8");

            /*
                * 1. 결제 부분취소 요청 결과처리
                *
                */
            if ($xpay->TX()) {
                //1)결제 부분취소결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)
                /*
                    echo "결제 부분취소 요청이 완료되었습니다.  <br>";
                    echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
                    echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";

                    $keys = $xpay->Response_Names();
                        foreach($keys as $name) {
                            echo $name . " = " . $xpay->Response($name, 0) . "<br>";
                        }
                    echo "<p>";
                    */

                if ('0000' == $xpay->Response_Code()) {
                    // 환불금액기록
                    $tno = $xpay->Response("LGD_TID", 0);
                    $mod_mny = (int)$tax_mny + (int)$free_mny;

                    $sql = " update shop_order
                                    set refund_price = ?
                                where od_id = ?
                                    and od_no = ?
                                    and od_tno = ?";
                    $param = array($mod_mny, $od['od_id'], $od['od_no'], $tno);
                    $this->query_model->returnNull($sql, $param);

                    $sql = " update shop_order
                                    set shop_memo = concat(shop_memo, \"$mod_memo\")
                                where od_id = ?
                                    and od_tno = ?";
                    $param = array($od['od_id'], $tno);
                    $this->query_model->returnNull($sql, $param);
                } else {
                    alert($xpay->Response_Msg() . ' 코드 : ' . $xpay->Response_Code());
                }
            } else {
                //2)API 요청 실패 화면처리
                /*
                    echo "결제 부분취소 요청이 실패하였습니다.  <br>";
                    echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
                    echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
                    */
                alert('결제 부분취소 요청이 실패하였습니다.\\n\\n' . $xpay->Response_Code() . ' : ' . $xpay->Response_Msg());
            }
        } else if ($od['od_pg'] == 'kcp') {
            include_once('./Bill/settle_kcp.inc.php');
            // locale ko_KR.euc-kr 로 설정
            setlocale(LC_CTYPE, 'ko_KR.euc-kr');

            // 부분취소 실행
            $g_conf_site_cd   = $default['de_kcp_mid'];
            $g_conf_site_key  = $default['de_kcp_site_key'];
            $g_conf_home_dir  = './Bill/kcp';
            $g_conf_key_dir   = '';
            $g_conf_log_dir   = '';
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $g_conf_key_dir   = './Bill/kcp/bin/pub.key';
                $g_conf_log_dir   = './Bill/kcp/log';
            }

            if (preg_match("/^T000/", $g_conf_site_cd) || $default['de_card_test']) {
                $g_conf_gw_url  = "testpaygw.kcp.co.kr";
            } else {
                $g_conf_gw_url  = "paygw.kcp.co.kr";
            }

            include_once('./Bill/kcp/pp_cli_hub_lib.php');

            $tno            = $od['od_tno'];
            $req_tx         = 'mod';
            $mod_desc       = iconv_euckr($mod_memo);
            $cust_ip        = getenv('REMOTE_ADDR');
            $rem_mny        = (int)$od_receipt_price;
            $mod_mny        = (int)$tax_mny;
            $mod_free_mny   = (int)$free_mny;
            $mod_type       = 'RN07';
            if ($od['paymethod'] == '계좌이체')
                $mod_type   = 'STPA';

            if ($od['od_tax_flag']) {
                $mod_mny = $tax_mny + $free_mny;
            }

            $c_PayPlus  = new C_PAYPLUS_CLI_T;
            $c_PayPlus->mf_clear();

            if ($req_tx == "mod") {
                $tran_cd = "00200000";

                $c_PayPlus->mf_set_modx_data("tno", $tno);  // KCP 원거래 거래번호
                $c_PayPlus->mf_set_modx_data("mod_type", $mod_type);  // 원거래 변경 요청 종류
                $c_PayPlus->mf_set_modx_data("mod_ip", $cust_ip);  // 변경 요청자 IP
                $c_PayPlus->mf_set_modx_data("mod_desc", $mod_desc);  // 변경 사유
                $c_PayPlus->mf_set_modx_data("rem_mny", strval($rem_mny));  // 취소 가능 잔액
                $c_PayPlus->mf_set_modx_data("mod_mny", strval($mod_mny));  // 취소 요청 금액

                if ($od['od_tax_flag']) {
                    $mod_tax_mny = round((int)$tax_mny / 1.1);
                    $mod_vat_mny = (int)$tax_mny - $mod_tax_mny;

                    $c_PayPlus->mf_set_modx_data("tax_flag", "TG03");  // 복합과세 구분
                    $c_PayPlus->mf_set_modx_data("mod_tax_mny", strval($mod_tax_mny));     // 공급가 부분 취소 요청 금액
                    $c_PayPlus->mf_set_modx_data("mod_vat_mny", strval($mod_vat_mny));  // 부과세 부분 취소 요청 금액
                    $c_PayPlus->mf_set_modx_data("mod_free_mny", strval($mod_free_mny));  // 비관세 부분 취소 요청 금액
                }
            }

            if ($tran_cd != "") {
                $c_PayPlus->mf_do_tx(
                    "",
                    $g_conf_home_dir,
                    $g_conf_site_cd,
                    $g_conf_site_key,
                    $tran_cd,
                    "",
                    $g_conf_gw_url,
                    $g_conf_gw_port,
                    "payplus_cli_slib",
                    $ordr_idxx,
                    $cust_ip,
                    $g_conf_log_level,
                    "",
                    0,
                    $g_conf_key_dir,
                    $g_conf_log_dir
                );

                $res_cd  = $c_PayPlus->m_res_cd;  // 결과 코드
                $res_msg = $c_PayPlus->m_res_msg; // 결과 메시지
                /* $res_en_msg = $c_PayPlus->mf_get_res_data( "res_en_msg" );  // 결과 영문 메세지 */
            } else {
                $c_PayPlus->m_res_cd  = "9562";
                $c_PayPlus->m_res_msg = "연동 오류|Payplus Plugin이 설치되지 않았거나 tran_cd값이 설정되지 않았습니다.";
            }

            if ($res_cd != '0000') {
                $res_msg = iconv("euc-kr", "utf-8", $res_msg);

                alert("$res_cd : $res_msg");
            }

            /* ============================================================================== */
            /* =       취소 결과 처리                                                       = */
            /* = -------------------------------------------------------------------------- = */
            if ($req_tx == "mod") {
                if ($res_cd == "0000") {
                    $tno = $c_PayPlus->mf_get_res_data("tno");  // KCP 거래 고유 번호
                    $amount  = $c_PayPlus->mf_get_res_data("amount"); // 원 거래금액
                    $mod_mny = $c_PayPlus->mf_get_res_data("panc_mod_mny"); // 취소요청된 금액
                    $rem_mny = $c_PayPlus->mf_get_res_data("panc_rem_mny"); // 취소요청후 잔액

                    // 환불금액기록
                    $sql = " update shop_order
                                    set refund_price = ?
                                  where od_id = ?
                                    and od_no = ?
                                    and od_tno = ?";
                    $param = array($mod_mny, $od['od_id'], $od['od_no'], $tno);
                    $this->query_model->returnNull($sqlm, $param);

                    $sql = " update shop_order
                                    set shop_memo = concat(shop_memo, \"$mod_memo\")
                                  where od_id = ?
                                    and od_tno = ?";
                    $param = array($od['od_id'], $tno);
                    $this->query_model->returnNull($sql, $param);
                } // End of [res_cd = "0000"]
                /* = -------------------------------------------------------------------------- = */
                /* =       취소 실패 결과 처리                                                  = */
                /* = -------------------------------------------------------------------------- = */ else {
                }
            }

            // locale 설정 초기화
            setlocale(LC_CTYPE, '');
        } else if ($od['od_pg'] == 'inicis') {
            include_once('./Bill/settle_inicis.inc.php');
            $vat_mny       = round((int)$tax_mny / 1.1);
            $currency      = 'WON';
            $oldtid        = $od['od_tno'];
            $price         = (int)$tax_mny + (int)$free_mny;
            $confirm_price = (int)$od_receipt_price - $price;
            $buyeremail    = $od['email'];
            $tax           = (int)$tax_mny - $vat_mny;
            $taxfree       = (int)$free_mny;

            /***********************
             * 3. 재승인 정보 설정 *
             ***********************/
            $inipay->SetField("type",          "repay");                         // 고정 (절대 수정 불가)
            $inipay->SetField("pgid",          "INIphpRPAY");                    // 고정 (절대 수정 불가)
            $inipay->SetField("subpgip",       "203.238.3.10");                  // 고정
            $inipay->SetField("mid",           $default['de_inicis_mid']);       // 상점아이디
            $inipay->SetField("admin",         $default['de_inicis_admin_key']); // 비대칭 사용키 키패스워드
            $inipay->SetField("oldtid",        $oldtid);                         // 취소할 거래의 거래아이디
            $inipay->SetField("currency",      $currency);                       // 화폐단위
            $inipay->SetField("price",         $price);                          // 취소금액
            $inipay->SetField("confirm_price", $confirm_price);                  // 승인요청금액
            $inipay->SetField("buyeremail",    $buyeremail);                     // 구매자 이메일 주소
            $inipay->SetField("tax",           $tax);                            // 부가세금액
            $inipay->SetField("taxfree",       $taxfree);                        // 비과세금액

            /******************
             * 4. 재승인 요청 *
             ******************/
            $inipay->startAction();


            /*******************************************************************
             * 5. 재승인 결과                                                  *
             *                                                                 *
             * 신거래번호 : $inipay->getResult('TID')                                     *
             * 결과코드 : $inipay->getResult('ResultCode') ("00"이면 재승인 성공)         *
             * 결과내용 : $inipay->getResult('ResultMsg') (재승인결과에 대한 설명)        *
             * 원거래 번호 : $inipay->getResult('PRTC_TID')                                *
             * 최종결제 금액 : $inipay->getResult('PRTC_Remains')                              *
             * 부분취소 금액 : $inipay->getResult('PRTC_Price')                          *
             * 부분취소,재승인 구분값 : $inipay->getResult('PRTC_Type')              *
             *                          ("0" : 재승인, "1" : 부분취소)         *
             * 부분취소(재승인) 요청횟수 : $inipay->getResult('PRTC_Cnt')           *
             *******************************************************************/

            if ($inipay->getResult('ResultCode') == '00') {
                // 환불금액기록
                $tno      = $inipay->getResult('PRTC_TID');
                $re_price = $inipay->getResult('PRTC_Price');

                $sql = " update shop_order
                                set refund_price = ?
                              where od_id = ?
                                and od_no = ?
                                and od_tno = ?";
                $param = array($re_price, $od['od_id'], $od['od_no'], $tno);
                $this->query_model->returnNull($sql, $param);

                $sql = " update shop_order
                                set shop_memo = concat(shop_memo, \"$mod_memo\")
                              where od_id = ?
                                and od_tno = ?";
                $param = array($od['od_id'], $tno);
                $this->query_model->returnNull($sql, $param);
            } else {
                alert(iconv_utf8($inipay->GetResult("ResultMsg")) . ' 코드 : ' . $inipay->GetResult("ResultCode"));
            }
        }

        $this->admin_template->admin_head();
        $data = array('od' => $od);
        $this->load->view('admin/pop_orderpartcancelupdate', $data);
        $this->admin_template->admin_footer_sub();
    }
    #팝업관련된 함수들 끝
    #카테고리(category) 관련된 함수들 시작
    public function category()
    {
        $this->admin_template->admin_access();
        $this->admin_template->admin_head();
        $member = $this->load->get_var('member');

        $ADMIN_MENU4 = $this->config->item('ADMIN_MENU4');
        $ADMIN_MENU4_01 = $this->config->item('ADMIN_MENU4_01');
        $ADMIN_MENU4_02 = $this->config->item('ADMIN_MENU4_02');
        $ADMIN_MENU4_03 = $this->config->item('ADMIN_MENU4_03');

        $code = $this->input->get('code');

        $pg_title = $ADMIN_MENU4;
        $pg_num = 4;
        $snb_icon = "<i class=\"fa fa-sitemap\"></i>";

        if ($member['id'] != 'admin' && !$member['auth_' . $pg_num]) {
            alert("접근권한이 없습니다.");
        }
        $pg_title2 = "";
        if ($code == "list") $pg_title2 = $ADMIN_MENU4_01;
        if ($code == "view") $pg_title2 = $ADMIN_MENU4_02;
        if ($code == "menu_list") $pg_title2 = $ADMIN_MENU4_03;
        $this->admin_template->admin_topmenu($pg_title, false, $pg_title2, $snb_icon);
        $this->categorySkin($code, $pg_title2);
        $this->admin_template->admin_footer();
    }
    private function categorySkin($code, $pg_title2)
    {
        $headTitle = "
            <div class=\"s_wrap\">
                <h1>{$pg_title2}</h1>";
        $headTitleEnd = "</div>";
        switch ($code) {
            case 'list':
                $this->category_list($code, $headTitle, $headTitleEnd);
                break;
            case 'view':
                $this->category_view($headTitle, $headTitleEnd);
                break;
            case 'menu_list':
                $this->menu_list($headTitle, $headTitleEnd);
                break;
            default:
                break;
        }
    }
    private function category_list($code, $headTitle, $headTitleEnd)
    {
        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $qstr = $this->load->get_var('qstr');
        if (isset($sel_ca1)) $qstr .= '&sel_ca1=' . $sel_ca1;
        if (isset($sel_ca2)) $qstr .= '&sel_ca2=' . $sel_ca2;
        if (isset($sel_ca3)) $qstr .= '&sel_ca3=' . $sel_ca3;
        if (isset($sel_ca4)) $qstr .= '&sel_ca4=' . $sel_ca4;
        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $sql_order = " order by caterank, catecode ";
        $sel_ca = array('', '', '', '');
        $sel_ca[0] = $this->funcomplex_lib->get_category_select_1('sel_ca1', $sel_ca1);
        $sel_ca[1] = $this->funcomplex_lib->get_category_select_2('sel_ca2', $sel_ca2);
        $sel_ca[2] = $this->funcomplex_lib->get_category_select_3('sel_ca3', $sel_ca3);
        $sel_ca[3] = $this->funcomplex_lib->get_category_select_4('sel_ca4', $sel_ca4);
        $help1 = help('예시) /shop/view?index_no=1');

        $sql = "select * from shop_category where length(catecode)='3' $sql_order";
        $res = $this->query_model->returnArr($sql);
        $listTable = "";
        foreach ($res as $key => $row) {
            $count1 = $this->common_lib->sel_count("shop_category", "where upcate='{$row['catecode']}'");
            $href1 = "?code=list&sel_ca1={$row['catecode']}";
            $listTable .= "<li>\n";
            $listTable .= "
                <div>
                    <img src=\"" . base_url() . "img/icon/no_01_over.gif\" class=\"vam\" alt=\"1차\">
                    <b>{$row['catecode']}</b>
                    <a href=\"javascript:post_delete('" . base_url() . "AdminProcess/category_delete', '{$row['index_no']}');\" class=\"btn_ssmall red\">삭제</a>
                    <a href=\"javascript:modok('{$row['index_no']}');\" class=\"btn_ssmall\">수정</a>
                    <a href=\"{$href1}\"><b>{$row['catename']}</b></a> <b class=\"fc_255\">({$count1})</b>
                    <div id=\"co{$row['index_no']}\" style=\"display:none;\"><iframe id=\"cos{$row['index_no']}\" frameborder=\"0\" width=\"100%\" height=\"350\"></iframe></div>
                </div>";
            if ($sel_ca1 && $sel_ca1 == $row['catecode']) { //2차
                $listTable .= "<dl class=\"cate2_bx\">\n";
                $sql2 = "select * from shop_category where upcate='{$sel_ca1}' $sql_order ";
                $res2 = $this->query_model->returnArr($sql2);
                foreach ($res2 as $key => $row2) {
                    $count2 = $this->common_lib->sel_count("shop_category", "where upcate='{$row2['catecode']}'");
                    $href2 = "{$href1}&sel_ca2={$row2['catecode']}";
                    $listTable .= "
                        <dt>
                            <img src=\"" . base_url() . "img/icon/no_02.gif\" class=\"vam\" alt=\"2차\">
                            <b>{$row2['catecode']}</b>
                            <a href=\"javascript:post_delete('" . base_url() . "AdminProcess/category_delete', '{$row2['index_no']}');\" class=\"btn_ssmall red\">삭제</a>
                            <a href=\"javascript:modok('{$row2['index_no']}');\" class=\"btn_ssmall\">수정</a>
                            <a href=\"{$href2}\"><b>{$row2['catename']}</b></a> <b class=\"fc_255\">({$count2})</b>
                            <div style=\"display:none;\" id=\"co{$row2['index_no']}\"><iframe id=\"cos{$row2['index_no']}\" frameborder=\"0\" width=\"100%\" height=\"270\"></iframe></div>
                        </dt>";
                    if ($sel_ca2 && $sel_ca2 == $row2['catecode']) { // 3차
                        $listTable .= "<dd>\n<dl class=\"cate3_bx\">\n";
                        $sql3 = "select * from shop_category where upcate='{$sel_ca2}' $sql_order";
                        $res3 = $this->query_model->returnArr($sql3);
                        foreach ($res3 as $key => $row3) {
                            $count3 = $this->common_lib->sel_count("shop_category", "where upcate='{$row3['catecode']}'");
                            $href3 = "{$href2}&sel_ca3={$row3['catecode']}";
                            $listTable .= "
                                <dd>
                                    <img src=\"" . base_url() . "img/icon/no_03.gif\" class=\"vam\" alt=\"3차\">
                                    <b>{$row3['catecode']}</b>
                                    <a href=\"javascript:post_delete('" . base_url() . "AdminProcess/category_delete', '{$row3['index_no']}');\" class=\"btn_ssmall red\">삭제</a>
                                    <a href=\"javascript:modok('{$row3['index_no']}');\" class=\"btn_ssmall\">수정</a>
                                    <a href=\"{$href3}\"><b>{$row3['catename']}</b></a> <b class=\"fc_255\">({$count3})</b>
                                    <div style=\"display:none;\" id=\"co{$row3['index_no']}\"><iframe id=\"cos{$row3['index_no']}\" frameborder=\"0\" width=\"100%\" height=\"270\"></iframe></div>
                                </dd>";
                            if ($sel_ca3 && $sel_ca3 == $row3['catecode']) { // 4차
                                $listTable .= "<dd>\n<dl class=\"cate4_bx\">\n";
                                $sql4 = "select * from shop_category where upcate='{$sel_ca3}' $sql_order";
                                $res4 = $this->query_model->returnArr($sql4);
                                foreach ($res4 as $key => $row4) {
                                    $count4 = $this->common_lib->sel_count("shop_category", "where upcate='{$row4['catecode']}'");
                                    $href4 = "{$href3}&sel_ca4={$row4['catecode']}";
                                    $listTable .= "
                                        <dd>
                                            <img src=\"" . base_url() . "img/icon/no_04.gif\" class=\"vam\" alt=\"4차\">
                                            <b>{$row4['catecode']}</b>
                                            <a href=\"javascript:post_delete('" . base_url() . "AdminProcess/category_delete', '{$row4['index_no']}');\" class=\"btn_ssmall red\">삭제</a>
                                            <a href=\"javascript:modok('{$row4['index_no']}');\" class=\"btn_ssmall\">수정</a>
                                            <a href=\"{$href4}\"><b>{$row4['catename']}</b></a> <b class=\"fc_255\">({$count4})</b>
                                            <div style=\"display:none;\" id=\"co{$row4['index_no']}\"><iframe id=\"cos{$row4['index_no']}\" frameborder=\"0\" width=\"100%\" height=\"270\"></iframe></div>
                                        </dd>";
                                    if ($sel_ca4 && $sel_ca4 == $row4['catecode']) { // 5차
                                        $listTable .= "<dd>\n<dl class=\"cate5_bx\">\n";
                                        $sql5 = "select * from shop_category where upcate='{$sel_ca4}' $sql_order";
                                        $res5 = $this->query_model->returnArr($sql5);
                                        foreach ($res5 as $key => $row5) {
                                            $listTable .= "
                                                <dd>
                                                    <img src=\"" . base_url() . "img/icon/no_05.gif\" class=\"vam\" alt=\"5차\">
                                                    <b>{$row5['catecode']}</b>
                                                    <a href=\"javascript:post_delete('" . base_url() . "AdminProcess/category_delete', '{$row5['index_no']}');\" class=\"btn_ssmall red\">삭제</a>
                                                    <a href=\"javascript:modok('{$row5['index_no']}');\" class=\"btn_ssmall\">수정</a>
                                                    <b>{$row5['catename']}</b>
                                                    <div style=\"display:none;\" id=\"co{$row5['index_no']}\"><iframe id=\"cos{$row5['index_no']}\" frameborder=\"0\" width=\"100%\" height=\"270\"></iframe></div>
                                                </dd>";
                                        }
                                        $listTable .= "</dl>";
                                    }
                                }
                                $listTable .= "</dl>";
                            }
                        }
                        $listTable .= "</dl>";
                    }
                }
                $listTable .= "</dl>";
            }
        }
        $data = array('q1' => $q1, 'code' => $code, 'headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'sel_ca' => $sel_ca, 'help1' => $help1, 'listTable' => $listTable);
        $this->load->view('admin/category/list', $data);
    }
    private function category_view($headTitle, $headTitleEnd)
    {
        $sql = " select catecode, catename
					   from shop_category
					  where length(catecode) = '3'
					  order by caterank, catecode ";
        $result = $this->query_model->returnArr($sql);
        $selOpt = "";
        foreach ($result as $key => $row) {
            $selOpt .= '<option value="' . $row['catecode'] . '">' . $row['catename'] . '</option>';
        }
        $selboxInfo = "";
        for ($i = 2; $i <= 5; $i++) {
            $selboxInfo .= "
                <td class=\"w20p\">
                    <div id=\"load_sel_ca{$i}\">
                        <div class=\"sit_selbox_info\"></div>
                    </div>
                </td>";
        }
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'selOpt' => $selOpt, 'selboxInfo' => $selboxInfo);
        $this->load->view('admin/category/view', $data);
    }
    public function category_view_select()
    {
        $sel_id = $this->input->post('sel_id');
        $catecode = $this->input->post('catecode');
        switch ($sel_id):
            case "sel_ca2":
                $len = 6;
                break;
            case "sel_ca3":
                $len = 9;
                break;
            case "sel_ca4":
                $len = 12;
                break;
            case "sel_ca5":
                $len = 15;
                break;
        endswitch;
        $sql = " select catecode, catename
                    from shop_category
                    where length(catecode) = '$len'
                        and catecode like '$catecode%'
                    order by caterank, catecode ";
        $result = $this->query_model->returnArr($sql);
        $optList = "";
        foreach ($result as $key => $row) {
            $optList .= '<option value="' . $row['catecode'] . '">' . $row['catename'] . '</option>';
        }
        $data = array('sel_id' => $sel_id, 'optList' => $optList);
        $this->load->view('admin/category/category_view_select', $data);
    }
    public function category_mod()
    {
        $sql_commend = '';
        $cateimg1_del = $this->input->post('cateimg1_del');
        $cateimg2_del = $this->input->post('cateimg2_del');
        $headimg_del = $this->input->post('headimg_del');
        $cateuse = $this->input->post('cateuse');
        $headimgurl = $this->input->post('headimgurl');
        $catename = $this->input->post('catename');

        $uploadPath = "./data/category";
        $configArr = array();
        $configArr['upload_path'] = $uploadPath;

        $configArr['allowed_types'] = $this->allowed_types;
        $configArr['max_size'] = $this->max_size;
        $configArr['max_width'] = $this->max_width;
        $configArr['max_height'] = $this->max_height;
        $configArr['remove_spaces'] = $this->remove_spaces;
        $configArr['encrypt_name'] = $this->encrypt_name;
        $this->load->library('upload', $configArr);
        $this->upload->initialize($configArr);
        $srcfile = './data/category';
        if (!is_dir($srcfile)) {
            @mkdir($srcfile, 0707);
            @chmod($srcfile, 0707);
        }
        $mod_type = $this->input->post('mod_type');
        $ca_no = $this->input->get('index_no');
        if ($mod_type == "u") {
            $sql = "select * from shop_category where index_no=?";
            $param = array($ca_no);
            $ca = $this->query_model->returnOneArr($sql, $param);
            $error = "";
            if ($cateimg1_del) {
                unlink($uploadPath . '/' . $cateimg1_del);
                $sql_commend .= " , cateimg1 = '' ";
            }
            if ($cateimg2_del) {
                unlink($uploadPath . '/' . $cateimg2_del);
                $sql_commend .= " , cateimg2 = '' ";
            }
            if ($headimg_del) {
                unlink($uploadPath . '/' . $headimg_del);
                $sql_commend .= " , headimg = '' ";
            }
            $cateimg1 = "";
            if (isset($_FILES['cateimg1']['name']) && $_FILES['cateimg1']['name'] != "") {
                unlink($uploadPath . '/' . $ca['cateimg1']);
                if (!$this->upload->do_upload('cateimg1')) {
                    $error = $this->upload->display_errors();
                } else {
                    $uploadFile = $this->upload->data();
                    $new_file = $uploadFile['file_name'];
                    $cateimg1 = $new_file;
                }
                $sql_commend .= " , cateimg1 = '$cateimg1' ";
            }
            $cateimg2 = "";
            if (isset($_FILES['cateimg2']['name']) && $_FILES['cateimg2']['name'] != "") {
                unlink($uploadPath . '/' . $ca['cateimg2']);
                if (!$this->upload->do_upload('cateimg2')) {
                    $error = $this->upload->display_errors();
                } else {
                    $uploadFile = $this->upload->data();
                    $new_file = $uploadFile['file_name'];
                    $cateimg2 = $new_file;
                }
                $sql_commend .= " , cateimg2 = '$cateimg2' ";
            }
            $headimg = "";
            if (isset($_FILES['headimg']['name']) && $_FILES['headimg']['name'] != "") {
                unlink($uploadPath . '/' . $ca['headimg']);
                if (!$this->upload->do_upload('headimg')) {
                    $error = $this->upload->display_errors();
                } else {
                    $uploadFile = $this->upload->data();
                    $new_file = $uploadFile['file_name'];
                    $headimg = $new_file;
                }
                $sql_commend .= " , headimg = '$headimg' ";
            }
            if ($error != "") {
                echo "<script>alert('" . strip_tags($error) . "')</script>";
            }

            $len = strlen($ca['catecode']);
            $sql_where = " where SUBSTRING(catecode,1,$len) = '{$ca['catecode']}' ";

            // 카테고리 숨김
            $sql = "update shop_category set cateuse = '$cateuse' {$sql_where} ";
            $this->query_model->returnNull($sql);

            $sql = "update shop_category
                                set catename=?,
                                headimgurl = ?
                                {$sql_commend}
                            where index_no=? ";
            $param = array(trim($catename), trim($headimgurl), $ca_no);
            $this->query_model->returnNull($sql, $param);

            goto_url(base_url() . "admin/category_mod?index_no=$ca_no");
        }
        $sql = "select * from shop_category where index_no=?";
        $param = array($ca_no);
        $ca = $this->query_model->returnOneArr($sql, $param);
        $adm_category_navi = $this->common_lib->adm_category_navi($ca['catecode']);
        $categoryBan = "";
        if (strlen($ca['catecode']) == 3) {
            $categoryBan .= "
                <tr>
                    <th scope=\"row\">카테고리 상단배너</th>
                    <td>
                        <input type=\"file\" name=\"headimg\">";
            $himg_str = "";
            $himg = $srcfile . '/' . $ca['headimg'];
            if (is_file($himg) && $ca['headimg']) {
                $size = @getimagesize($himg);
                if ($size[0] && $size[0] > 300)
                    $width = 300;
                else
                    $width = $size[0];

                $himg = rpc($himg, "", "");

                $categoryBan .= '<input type="checkbox" name="headimg_del" value="' . $ca['headimg'] . '" id="headimg_del"> <label for="headimg_del">삭제</label>';
                $himg_str = '<img src="' . $himg . '" width="' . $width . '">';
            }
            if ($himg_str) {
                $categoryBan .= '<div class="banner_or_img">' . $himg_str . '</div>';
            }
            $categoryBan .= "
                    </td>
                </tr>
                <tr>
                    <th scope=\"row\">카테고리 상단배너 링크</th>
                    <td><input type=\"text\" name=\"headimgurl\" value=\"{$ca['headimgurl']}\" class=\"frm_input\" size=\"50\"></td>
                </tr>";
        }
        $data = array('ca_no' => $ca_no, 'ca' => $ca, 'adm_category_navi' => $adm_category_navi, 'categoryBan' => $categoryBan);
        $this->admin_template->admin_head();
        $this->load->view('admin/category/category_mod', $data);
        $this->admin_template->admin_footer_sub();
    }

    private function menu_list($headTitle, $headTitleEnd)
    {
        $sql = "select * from shop_menu order by index_no desc";
        $res = $this->query_model->returnArr($sql);
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'res' => $res
        );
        $this->load->view('admin/category/menu_list', $data);
    }

    #카테고리 관련된 함수들 끝
    #주문(order) 함수들 시작
    public function order_print()
    {
        $od_id = $this->input->get('od_id');
        $gw_status = $this->config->item('gw_status');
        if (!$od_id)
            alert_close("주문번호가 넘어오지 않았습니다.");

        $sql = " select * from shop_order where od_id IN ({$od_id}) group by od_id order by index_no desc ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert_close("출력할 자료가 없습니다.");
        $title = "주문내역";
        $this->admin_template->admin_head($title);
        $i = 0;
        $orderPintTable = "";
        $tot_tot_qty = 0;
        $tot_tot_price = 0;
        $tot_tot_price2 = 0;
        $tot_tot_baesong = 0;
        foreach ($result as $key => $row) {
            $amount = $this->global_lib->get_order_spay($row['od_id']); // 주문별 합계
            $od_addr = sprintf("(%s)", $row['zip']) . ' ' . $this->funcomplex_lib->print_address($row['addr1'], $row['addr2'], $row['addr3'], $row['addr_jibeon']);
            $od_b_addr = sprintf("(%s)", $row['b_zip']) . ' ' . $this->funcomplex_lib->print_address($row['b_addr1'], $row['b_addr2'], $row['b_addr3'], $row['b_addr_jibeon']);

            $od_addr  = ($od_addr) ? $od_addr : '입력안함';
            $od_tel   = ($row['telephone']) ? $row['telephone'] : '입력안함';
            $od_hp    = ($row['cellphone']) ? $row['cellphone'] : '입력안함';
            $od_b_tel = ($row['b_telephone']) ? $row['b_telephone'] : '입력안함';
            $od_b_hp  = ($row['b_cellphone']) ? $row['b_cellphone'] : '입력안함';

            // 보내는 사람과 받는 사람이 완전 일치하면 간단하게 출력
            // 보내는 사람과 받는 사람이 부분 일치하더라도 원래 내용을 모두 출력
            if ($row['name'] == $row['b_name'] && $od_addr == $od_b_addr && $od_tel == $od_b_tel &&  $od_hp == $od_b_hp && $od_hp != "&nbsp;") $samesamesame = 1;
            else $samesamesame = '';

            $od_memo = ($row['memo']) ? $this->funcomplex_lib->get_text(stripslashes($row['memo'])) : '';
            $od_shop_memo = ($row['shop_memo']) ? $this->funcomplex_lib->get_text(stripslashes($row['shop_memo'])) : '';
            $orderPintTable .= "
                <!-- 반복시작 -->
                <div class=\"sodr_print_pop_list\">
                    <h2>주문번호 {$row['od_id']}</h2>
                    <h3>주문하신 분 : " . $this->funcomplex_lib->get_text($row['name']) . "</h3>
                    <dl>
                        <dt>주소</dt>
                        <dd>" . $this->funcomplex_lib->get_text($od_addr) . "</dd>
                        <dt>휴대폰</dt>
                        <dd>" . $this->funcomplex_lib->get_text($od_hp) . "</dd>
                        <dt>전화번호</dt>
                        <dd>" . $this->funcomplex_lib->get_text($od_tel) . "</dd>
                    </dl>";
            if ($samesamesame) {
                $orderPintTable .= "
                        <p class=\"sodr_print_pop_same\">보내는 사람과 받는 사람이 동일합니다.</p>";
            } else {
                $orderPintTable .= "
                        <h3>받으시는 분 : " . $this->funcomplex_lib->get_text($row['b_name']) . "</h3>
                        <dl>
                            <dt>주소</dt>
                            <dd>" . $this->funcomplex_lib->get_text($od_b_addr) . "</dd>
                            <dt>휴대폰</dt>
                            <dd>" . $this->funcomplex_lib->get_text($od_b_hp) . "</dd>
                            <dt>전화번호</dt>
                            <dd>" . $this->funcomplex_lib->get_text($od_b_tel) . "</dd>
                        </dl>";
            }
            $orderPintTable .= "
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
            $sql2 = " select *
                                    from shop_cart
                                where od_id = ?
                                group by gs_id
                                order by io_type, index_no ";
            $param = array($row['od_id']);
            $res2 = $this->query_model->returnArr($sql2, $param);
            foreach ($res2 as $key => $row2) {
                $sodr = $this->global_lib->get_order($row2['od_no'], 'od_goods, dan');
                $gs = unserialize($sodr['od_goods']);
                $gs['gname'] = isset($gs['gname']) ? $gs['gname'] : "";
                $it_name = stripslashes($gs['gname']);
                $it_options = $this->funcomplex_lib->print_complete_options($row2['gs_id'], $row2['od_id']);
                if ($it_options) {
                    $it_name .= '<div class="sod_opt">' . $it_options . '</div>';
                }

                // 합계금액 계산
                $sql = " select SUM(IF(io_type = 1, (io_price), (io_price + ct_price))) as price,
                                            SUM(IF(io_type = 1, (io_price * ct_qty), ((io_price + ct_price) * ct_qty))) as sell_price,
                                            SUM(IF(io_type = 1, 0, ct_qty)) as qty
                                    from shop_cart
                                    where gs_id = ? and od_id = ?";
                $param = array($row2['gs_id'], $row2['od_id']);
                $sum = $this->query_model->returnOneArr($sql, $param);

                $sub_tot_qty    += $sum['qty'];
                $sub_tot_price1 += $sum['price'];
                $sub_tot_price2 += $sum['sell_price'];
                $orderPintTable .= "
                            <tr>
                                <td class=\"tal\">{$it_name}</td>
                                <td class=\"tar\">" . number_format($sum['price']) . "</td>
                                <td class=\"tac\">" . number_format($sum['qty']) . "</td>
                                <td class=\"tar\">" . number_format($sum['sell_price']) . "</td>
                                <td class=\"tac\">{$gw_status[$sodr['dan']]}</td>
                            </tr>";
            }
            $orderPintTable .= "
                            <tr>
                                <td class=\"tac\">합계</td>
                                <td class=\"tar\">" . number_format($sub_tot_price1) . "</td>
                                <td class=\"tac\">" . number_format($sub_tot_qty) . "</td>
                                <td class=\"tar\">" . number_format($sub_tot_price2) . "</td>
                                <td></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class=\"tar\" colspan=\"5\">
                                    (상품금액 : " . display_price($amount['price']) . " +
                                    배송비 : " . display_price($amount['baesong']) . ") -
                                    (쿠폰할인 : " . display_price($amount['coupon']) . " +
                                    포인트결제 : " . display_price($amount['usepoint']) . ") =
                                    총계 : <strong class=\"fc_red\">" . display_price($amount['useprice']) . "</strong>
                                </td>
                            </tr>
                        </tfoot>
                        </table>
                    </div>";
            $tot_tot_qty     += $amount['qty'];
            $tot_tot_price   += $amount['buyprice'];
            $tot_tot_price2  += $amount['useprice'];
            $tot_tot_baesong += $amount['baesong'];

            if ($od_memo)
                $orderPintTable .= "<p class=\"padt10\"><strong>비고</strong> $od_memo</p>";

            if ($od_shop_memo)
                $orderPintTable .= "<p class=\"padt10\"><strong>메모</strong> $od_shop_memo</p>";
            $orderPintTable .= "
                    </div>
                    <!-- 반복 끝 -->";
            $i++;
        }
        $data = array(
            'orderPintTable' => $orderPintTable,
            'tot_tot_qty' => $tot_tot_qty,
            'tot_tot_price' => $tot_tot_price,
            'tot_tot_price2' => $tot_tot_price2,
            'tot_tot_baesong' => $tot_tot_baesong
        );
        $this->load->view('admin/order/order_print', $data);
    }
    public function order()
    {
        $this->admin_template->admin_access();
        $this->admin_template->admin_head();
        $member = $this->load->get_var('member');
        $code = $this->input->get('code');
        $ADMIN_MENU6 = $this->config->item('ADMIN_MENU6');
        $pg_title = $ADMIN_MENU6;
        $pg_num = 6;
        $snb_icon = "<i class=\"ionicons ion-clipboard\"></i>";
        if ($member['id'] != 'admin' && !$member['auth_' . $pg_num]) {
            alert("접근권한이 없습니다.");
        }
        if ($code == "list")                $pg_title2 = $this->config->item('ADMIN_MENU6_01');
        if ($code == "1")                $pg_title2 = $this->config->item('ADMIN_MENU6_02');
        if ($code == "2")                $pg_title2 = $this->config->item('ADMIN_MENU6_03');
        if ($code == "3")                $pg_title2 = $this->config->item('ADMIN_MENU6_04');
        if ($code == "4")                $pg_title2 = $this->config->item('ADMIN_MENU6_05');
        if ($code == "5")                $pg_title2 = $this->config->item('ADMIN_MENU6_06');
        if ($code == "delivery")            $pg_title2 = $this->config->item('ADMIN_MENU6_07');
        if ($code == "delivery_update")    $pg_title2 = $this->config->item('ADMIN_MENU6_07');
        if ($code == "6")                $pg_title2 = $this->config->item('ADMIN_MENU6_08');
        if ($code == "9")                $pg_title2 = $this->config->item('ADMIN_MENU6_09');
        if ($code == "7")                $pg_title2 = $this->config->item('ADMIN_MENU6_10');
        if ($code == "8")                $pg_title2 = $this->config->item('ADMIN_MENU6_11');
        if ($code == "memo")                $pg_title2 = $this->config->item('ADMIN_MENU6_12');
        if ($code == "timesale_state_list")    $pg_title2 = $this->config->item('ADMIN_MENU6_13');
        if ($code == "timesale_state_detail")    $pg_title2 = $this->config->item('ADMIN_MENU6_13');
        $this->admin_template->admin_topmenu($pg_title, false, $pg_title2, $snb_icon);
        $this->orderSkin($code, $pg_title2);
        $this->admin_template->admin_footer();
    }
    private function orderSkin($code, $pg_title2)
    {
        $headTitle = "
            <div class=\"s_wrap\">
                <h1>{$pg_title2}</h1>";
        $headTitleEnd = "</div>";
        switch ($code) {
            case 'list':
                $this->order_list($code, $headTitle, $headTitleEnd);
                break;
            case '1':
                $this->order_1($code, $headTitle, $headTitleEnd);
                break;
            case '2':
                $this->order_2($code, $headTitle, $headTitleEnd);
                break;
            case '3':
                $this->order_3($code, $headTitle, $headTitleEnd);
                break;
            case '4':
                $this->order_4($code, $headTitle, $headTitleEnd);
                break;
            case '5':
                $this->order_5($code, $headTitle, $headTitleEnd);
                break;
            case 'delivery':
                $this->order_delivery($headTitle, $headTitleEnd);
                break;
            case 'delivery_update':
                $this->order_delivery_update($headTitle, $headTitleEnd);
                break;
            case '6':
                $this->order_6($code, $headTitle, $headTitleEnd);
                break;
            case '9':
                $this->order_9($code, $headTitle, $headTitleEnd);
                break;
            case '7':
                $this->order_7($code, $headTitle, $headTitleEnd);
                break;
            case '8':
                $this->order_8($code, $headTitle, $headTitleEnd);
                break;
            case 'memo':
                $this->order_memo($code, $headTitle, $headTitleEnd);
                break;
            case 'timesale_state_list':
                $this->timesale_state_list($code, $headTitle, $headTitleEnd);
                break;
            case 'timesale_state_detail':
                $this->timesale_state_detail($code, $headTitle, $headTitleEnd);
                break;
            default:
                break;
        }
    }

    private function order_list($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');

        $stx = $this->input->get('stx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $od_final = $this->input->get('od_final');
        $page = $this->input->get('page');
        $od_status = $this->input->get('od_status');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $page_rows = $this->input->get('page_rows');

        #order_query 함수 사용시 리턴의 값들 시작
        $orderQuery = $this->order_query();
        $jqueryUiDatePick = $orderQuery['jqueryUiDatePick'];
        $tot_orderprice = $orderQuery['tot_orderprice'];
        $total_count = $orderQuery['total_count'];
        $result = $orderQuery['result'];
        $q1 = $orderQuery['q1'];
        $sql_common = $orderQuery['sql_common'];
        $sql_search = $orderQuery['sql_search'];
        $num = $orderQuery['num'];
        $total_page = $orderQuery['total_page'];

        #order_query 함수 사용시 리턴의 값들 끝
        $btn_frmline = "
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"" . base_url() . "AdminProcess/order_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";

        $sflOpt = array(
            'od_id' => '',
            'od_no' => '',
            'mb_id' => '',
            'name' => '',
            'deposit_name' => '',
            'bank' => '',
            'b_name' => '',
            'b_telephone' => '',
            'b_cellphone' => '',
            'delivery_no' => '',
            'pt_id' => ''
        );
        $sflOpt['od_id'] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt['od_no'] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt['mb_id'] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt['name'] = option_selected('name', $sfl, '주문자명');
        $sflOpt['deposit_name'] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt['bank'] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt['b_name'] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt['b_telephone'] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt['b_cellphone'] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt['delivery_no'] = option_selected('delivery_no', $sfl, '운송장번호');

        $sel_fieldOpt = array(
            'od_time' => '',
            'receipt_time' => '',
            'delivery_date' => '',
            'invoice_date' => '',
            'user_date' => '',
            'cancel_date' => '',
            'refund_date' => '',
            'return_date' => '',
            'change_date' => ''
        );
        $sel_fieldOpt['od_time'] = option_selected('od_time', $sel_field, "주문일");
        $sel_fieldOpt['receipt_time'] = option_selected('receipt_time', $sel_field, "입금완료일");
        $sel_fieldOpt['delivery_date'] = option_selected('delivery_date', $sel_field, "배송일");
        $sel_fieldOpt['invoice_date'] = option_selected('invoice_date', $sel_field, "배송완료일");
        $sel_fieldOpt['user_date'] = option_selected('user_date', $sel_field, "구매확정일");
        $sel_fieldOpt['cancel_date'] = option_selected('cancel_date', $sel_field, "주문취소일");
        $sel_fieldOpt['refund_date'] = option_selected('refund_date', $sel_field, "환불완료일");
        $sel_fieldOpt['return_date'] = option_selected('return_date', $sel_field, "반품완료일");
        $sel_fieldOpt['change_date'] = option_selected('change_date', $sel_field, "교환완료일");

        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $payMethod = array('', '', '', '', '', '');
        $payMethod['all'] = radio_checked('od_settle_case', $od_settle_case,  '', '전체');
        $payMethod['noBank'] = radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
        $payMethod['at'] = radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
        $payMethod['credit'] = radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
        $payMethod['point'] = radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
        $payMethod['mileage'] = radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

        $gw_status = $this->config->item('gw_status');
        $od_statusArr = array('', '', '', '', '', '', '', '', '', '');
        $od_statusArr[0] = radio_checked('od_status', $od_status,  '', '전체');
        $od_statusArr[1] = radio_checked('od_status', $od_status, '1', $gw_status[1]);
        $od_statusArr[2] = radio_checked('od_status', $od_status, '2', $gw_status[2]);
        $od_statusArr[3] = radio_checked('od_status', $od_status, '3', $gw_status[3]);
        $od_statusArr[4] = radio_checked('od_status', $od_status, '4', $gw_status[4]);
        $od_statusArr[5] = radio_checked('od_status', $od_status, '5', $gw_status[5]);
        $od_statusArr[6] = radio_checked('od_status', $od_status, '6', $gw_status[6]);
        $od_statusArr[7] = radio_checked('od_status', $od_status, '7', $gw_status[7]);
        $od_statusArr[8] = radio_checked('od_status', $od_status, '8', $gw_status[8]);
        $od_statusArr[9] = radio_checked('od_status', $od_status, '9', $gw_status[9]);

        $od_finalArr = array('all' => '', 'pc' => '', 'pcn' => '');
        $od_finalArr['all'] = radio_checked('od_final', $od_final,  '', '전체');
        $od_finalArr['pc'] = radio_checked('od_final', $od_final, '1', '구매확정');
        $od_finalArr['pcn'] = radio_checked('od_final', $od_final, '0', '구매미확정');

        $ectSelectChk = array(
            'od_taxbill' => '',
            'od_taxsave' => '',
            'od_memo' => '',
            'od_shop_memo' => '',
            'od_receipt_point' => '',
            'od_coupon' => '',
            'od_escrow' => ''
        );
        $ectSelectChk['od_taxbill'] = check_checked('od_taxbill', $od_taxbill, 'Y', '세금계산서');
        $ectSelectChk['od_taxsave'] = check_checked('od_taxsave', $od_taxsave, 'Y', '현금영수증');
        $ectSelectChk['od_memo'] = check_checked('od_memo', $od_memo, 'Y', '배송메세지');
        $ectSelectChk['od_shop_memo'] = check_checked('od_shop_memo', $od_shop_memo, 'Y', '관리자메모');
        $ectSelectChk['od_receipt_point'] = check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
        $ectSelectChk['od_coupon'] = check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
        $ectSelectChk['od_escrow'] = check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $i = 0;
        $orderlistTable = "";
        $gw_status = $this->config->item('gw_status');
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->get_order_list($row, $amount);

            $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
            $param = array($row['od_id']);
            $resObj = $this->query_model->returnQueryObj($sql, $param);
            $res = $resObj->result_array();
            $rowspan = $resObj->num_rows();
            $k = 0;
            foreach ($res as $key => $row2) {
                $gs = unserialize($row2['od_goods']);
                $type = isset($gs['type']) ? $gs['type'] : "normal";

                $orderlistTable .= "
                        <tr class=\"{$bg}\">";
                if ($k == 0) {
                    $orderlistTable .= "
                            <td rowspan=\"{$rowspan}\">" . $num-- . "</td>
                            <td rowspan=\"{$rowspan}\">";
                    $orderlistTable .= substr($row['od_time'], 2, 14);
                    $orderlistTable .= $sodr['disp_test'];
                    $orderlistTable .= "
                            </td>
                            <td rowspan=\"{$rowspan}\">
                                <a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                                {$sodr['disp_mobile']}
                            </td>
                            <td rowspan=\"{$rowspan}\">
                                <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                                <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                                <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                            </td>";
                }
                $gs['simg1'] = isset($gs['simg1']) ? $gs['simg1'] : '';
                $gs['gname'] = isset($gs['gname']) ? $gs['gname'] : '';

                if ($member['grade'] == 99) {
                    $orderlistTable .= "
                            <td class=\"td_img\">
                                <a href=\"" . base_url() . "shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30) . "</a>
                            </td>";
                }
                $orderlistTable .= "
                        <td class=\"td_itname\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row2['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></td>
                        <td>" . number_format($row2['sum_qty']) . "</td>
                        <td class=\"tar\">" . number_format($row2['goods_price']) . "</td>
                        <td class=\"tar\">" . number_format($row2['baesong_price']) . "</td>
                        <td>{$gw_status[$row2['dan']]}</td>
                        <td>" . $this->funcomplex_lib->get_order_seller_id($row2['seller_id']) . "</td>";
                if ($k == 0) {
                    $orderlistTable .= "
                        <td rowspan=\"{$rowspan}\">
                            {$sodr['disp_od_name']}
                            {$sodr['disp_mb_id']}
                        </td>
                        <td rowspan=\"{$rowspan}\">{$row['b_name']}</td>
                        <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>
                        <td rowspan=\"{$rowspan}\">{$sodr['disp_paytype']}</td>";
                }
                $k++;
            }
            $i++;
        }
        if ($i == 0)
            $orderlistTable .= '<tr><td colspan="16" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'btn_frmline' => $btn_frmline,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'sflOpt' => $sflOpt,
            'stx' => $stx,
            'sel_fieldOpt' => $sel_fieldOpt,
            'get_search_date' => $get_search_date,
            'payMethod' => $payMethod,
            'od_statusArr' => $od_statusArr,
            'od_finalArr' => $od_finalArr,
            'ectSelectChk' => $ectSelectChk,
            'page_rowsOpt' => $page_rowsOpt,
            'tot_orderprice' => $tot_orderprice,
            'requestUrl' => $requestUrl,
            'q1' => $q1,
            'page' => $page,
            'orderlistTable' => $orderlistTable,
            'pagination' => $pagination,
            'total_count' => $total_count,
            'member' => $member
        );
        $this->load->view('admin/order/list', $data);
    }

    private function order_1($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');

        $stx = $this->input->get('stx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $od_final = $this->input->get('od_final');
        $page = $this->input->get('page');
        $od_status = $this->input->get('od_status');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $page_rows = $this->input->get('page_rows');

        #order_query 함수 사용시 리턴의 값들 시작
        $orderQuery = $this->order_query();
        $jqueryUiDatePick = $orderQuery['jqueryUiDatePick'];
        $tot_orderprice = $orderQuery['tot_orderprice'];
        $total_count = $orderQuery['total_count'];
        $result = $orderQuery['result'];
        $q1 = $orderQuery['q1'];
        $sql_common = $orderQuery['sql_common'];
        $sql_search = $orderQuery['sql_search'];
        $num = $orderQuery['num'];
        $total_page = $orderQuery['total_page'];
        #order_query 함수 사용시 리턴의 값들 끝
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall white\" onclick=\"document.pressed=this.value\">
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"" . base_url() . "AdminProcess/order_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";
        $sflOpt = array('', '', '', '', '', '', '', '', '', '', '');
        $sflOpt[0] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt[1] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt[2] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt[3] = option_selected('name', $sfl, '주문자명');
        $sflOpt[4] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt[5] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt[6] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt[7] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt[8] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt[9] = option_selected('delivery_no', $sfl, '운송장번호');

        $odTimeTag = option_selected('od_time', $sel_field, "주문일");
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $od_settleArr = array('', '', '', '', '', '');
        $od_settleArr[0] = radio_checked('od_settle_case', $od_settle_case, '', '전체');
        $od_settleArr[1] = radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
        $od_settleArr[2] = radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
        $od_settleArr[3] = radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
        $od_settleArr[4] = radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
        $od_settleArr[5] = radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

        $ectSelectChk = array('', '', '', '', '', '', '');
        $ectSelectChk[0] = check_checked('od_taxbill', $od_taxbill, 'Y', '세금계산서');
        $ectSelectChk[1] = check_checked('od_taxsave', $od_taxsave, 'Y', '현금영수증');
        $ectSelectChk[2] = check_checked('od_memo', $od_memo, 'Y', '배송메세지');
        $ectSelectChk[3] = check_checked('od_shop_memo', $od_shop_memo, 'Y', '관리자메모');
        $ectSelectChk[4] = check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
        $ectSelectChk[5] = check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
        $ectSelectChk[6] = check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $orderOneTable = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->get_order_list($row, $amount, "and dan IN ('4','5')");

            $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
            $param = array($row['od_id']);
            $resObj = $this->query_model->returnQueryObj($sql, $param);
            $res = $resObj->result_array();
            $rowspan = $resObj->num_rows();
            $k = 0;
            foreach ($res as $key => $row2) {
                $gs = unserialize($row2['od_goods']);
                $type = isset($gs['type']) ? $gs['type'] : "normal";

                $orderOneTable .= "
                    <tr class=\"{$bg}\">";
                if ($k == 0) {
                    $orderOneTable .= "
                            <td rowspan=\"{$rowspan}\">" . $num-- . "</td>
                            <td rowspan=\"{$rowspan}\">
                            " . substr($row['od_time'], 2, 14) . "
                            {$sodr['disp_test']}
                            </td>
                            <td rowspan=\"{$rowspan}\">
                                <a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                                {$sodr['disp_mobile']}
                                {$sodr['disp_baesong']}
                            </td>
                            <td rowspan=\"{$rowspan}\">
                                <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                                <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                                <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                            </td>";
                }
                if ($member['grade'] == 99) {
                    $orderOneTable .= "
                            <td class=\"td_img\">
                                <a href=\"" . base_url() . "shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30) . "</a>
                            </td>";
                }
                $orderOneTable .= "
                    <td class=\"td_itname\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row2['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></td>
                    <td>" . $this->funcomplex_lib->get_order_seller_id($row2['seller_id']) . "</td>";
                if ($k == 0) {
                    $orderOneTable .= "
                            <td rowspan=\"{$rowspan}\">
                                {$sodr['disp_od_name']}
                                {$sodr['disp_mb_id']}
                            </td>
                            <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>
                            <td rowspan=\"{$rowspan}\">{$sodr['disp_paytype']}</td>
                            <td rowspan=\"{$rowspan}\">{$row['deposit_name']}</td>
                            <td rowspan=\"{$rowspan}\"></td>
                            <td rowspan=\"{$rowspan}\" class=\"tal\">" . (isset($row['bank']) ? $row['bank'] : "") . "</td>";
                }
                $k++;
            }
            $i++;
        }
        if ($i == 0)
            $orderOneTable .= '<tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'code' => $code,
            'stx' => $stx,
            'sflOpt' => $sflOpt,
            'odTimeTag' => $odTimeTag,
            'get_search_date' => $get_search_date,
            'od_settleArr' => $od_settleArr,
            'ectSelectChk' => $ectSelectChk,
            'page_rowsOpt' => $page_rowsOpt,
            'tot_orderprice' => $tot_orderprice,
            'orderOneTable' => $orderOneTable,
            'pagination' => $pagination,
            'requestUrl' => $requestUrl,
            'q1' => $q1,
            'total_count' => $total_count,
            'page' => $page,
            'btn_frmline' => $btn_frmline,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'member' => $member
        );
        $this->load->view('admin/order/order_1', $data);
    }

    private function order_2($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');

        $stx = $this->input->get('stx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $od_final = $this->input->get('od_final');
        $page = $this->input->get('page');
        $od_status = $this->input->get('od_status');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $page_rows = $this->input->get('page_rows');

        #order_query 함수 사용시 리턴의 값들 시작
        $orderQuery = $this->order_query();
        $jqueryUiDatePick = $orderQuery['jqueryUiDatePick'];
        $tot_orderprice = $orderQuery['tot_orderprice'];
        $total_count = $orderQuery['total_count'];
        $result = $orderQuery['result'];
        $q1 = $orderQuery['q1'];
        $sql_common = $orderQuery['sql_common'];
        $sql_search = $orderQuery['sql_search'];
        $num = $orderQuery['num'];
        $total_page = $orderQuery['total_page'];
        #order_query 함수 사용시 리턴의 값들 끝
        $btn_frmline = "
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"./order/order_excel?{$q1}\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";
        $sflOpt = array('', '', '', '', '', '', '', '', '', '', '');
        $sflOpt[0] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt[1] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt[2] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt[3] = option_selected('name', $sfl, '주문자명');
        $sflOpt[4] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt[5] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt[6] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt[7] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt[8] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt[9] = option_selected('delivery_no', $sfl, '운송장번호');
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $sel_fieldOpt = array('', '');
        $sel_fieldOpt[0] = option_selected('od_time', $sel_field, "주문일");
        $sel_fieldOpt[1] = option_selected('receipt_time', $sel_field, "입금완료일");

        $payMethod = array('', '', '', '', '', '');
        $payMethod[0] = radio_checked('od_settle_case', $od_settle_case,  '', '전체');
        $payMethod[1] = radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
        $payMethod[2] = radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
        $payMethod[3] = radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
        $payMethod[4] = radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
        $payMethod[5] = radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

        $ectSelectChk = array('', '', '', '', '', '', '');
        $ectSelectChk[0] = check_checked('od_taxbill', $od_taxbill, 'Y', '세금계산서');
        $ectSelectChk[1] = check_checked('od_taxsave', $od_taxsave, 'Y', '현금영수증');
        $ectSelectChk[2] = check_checked('od_memo', $od_memo, 'Y', '배송메세지');
        $ectSelectChk[3] = check_checked('od_shop_memo', $od_shop_memo, 'Y', '관리자메모');
        $ectSelectChk[4] = check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
        $ectSelectChk[5] = check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
        $ectSelectChk[6] = check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $chk_cnt = 0;
        $order2Table = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->get_order_list($row, $amount, "and dan IN ('4','5')");

            $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
            $param = array($row['od_id']);
            $resObj = $this->query_model->returnQueryObj($sql, $param);
            $res = $resObj->result_array();
            $rowspan = $resObj->num_rows();
            $k = 0;
            foreach ($res as $key => $row2) {
                $gs = unserialize($row2['od_goods']);
                $type = isset($gs['type']) ? $gs['type'] : "normal";

                $order2Table .= "
                        <tr class=\"{$bg}\">";
                if ($k == 0) {
                    $order2Table .= "
                        <td rowspan=\"{$rowspan}\">" . $num-- . "</td>
                        <td rowspan=\"{$rowspan}\">
                            " . substr($row['od_time'], 2, 14) . "
                            {$sodr['disp_test']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                            {$sodr['disp_mobile']}
                            {$sodr['disp_baesong']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                            <label for=\"sit_sel_{$i}\" class=\"sound_only\">전체선택</label>
                            <input type=\"checkbox\" name=\"it_sel[]\" id=\"sit_sel_{$i}\">
                        </td>";
                }
                $gs['gname'] = isset($gs['gname']) ? $gs['gname'] : "";
                $gs['simg1'] = isset($gs['simg1']) ? $gs['simg1'] : "";
                $order2Table .= "
                        <td class=\"td_chk\">
                            <input type=\"hidden\" name=\"od_no[{$chk_cnt}]\" value=\"{$row2['od_no']}\">
                            <input type=\"checkbox\" name=\"chk[]\" value=\"{$chk_cnt}\" id=\"chk_{$chk_cnt}\" class=\"sct_sel_{$i}\">
                            <label for=\"chk_{$chk_cnt}\" class=\"sound_only\">{$gs['gname']}</label>
                        </td>";
                if ($member['grade'] == 99) {
                    $order2Table .= "
                            <td class=\"td_imgline\"><a href=\"" . base_url() . "shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30) . "</a></td>";
                }
                $order2Table .= "
                        <td class=\"td_itname\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row2['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></td>
                        <td>" . $this->funcomplex_lib->get_order_seller_id($row2['seller_id']) . "</td>";
                if ($k == 0) {
                    $order2Table .= "
                            <td rowspan=\"{$rowspan}\">
                                {$sodr['disp_od_name']}
                                {$sodr['disp_mb_id']}
                            </td>
                            <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>
                            <td rowspan=\"{$rowspan}\">{$sodr['disp_paytype']}</td>
                            <td rowspan=\"{$rowspan}\">{$sodr['disp_taxbill']}</td>";
                }
                $chk_cnt++;
                $k++;
            }
            $i++;
        }
        if ($i == 0)
            $order2Table .= '<tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'headTitle' => $headTitle,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'headTitleEnd' => $headTitleEnd,
            'code' => $code,
            'stx' => $stx,
            'btn_frmline' => $btn_frmline,
            'sflOpt' => $sflOpt,
            'get_search_date' => $get_search_date,
            'sel_fieldOpt' => $sel_fieldOpt,
            'payMethod' => $payMethod,
            'ectSelectChk' => $ectSelectChk,
            'page_rowsOpt' => $page_rowsOpt,
            'requestUrl' => $requestUrl,
            'q1' => $q1,
            'tot_orderprice' => $tot_orderprice,
            'page' => $page,
            'order2Table' => $order2Table,
            'pagination' => $pagination,
            'total_count' => $total_count
        );
        $this->load->view('admin/order/order_2', $data);
    }

    private function order_3($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');

        $stx = $this->input->get('stx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $page = $this->input->get('page');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $page_rows = $this->input->get('page_rows');

        #order_query 함수 사용시 리턴의 값들 시작
        $orderQuery = $this->order_query();
        $jqueryUiDatePick = $orderQuery['jqueryUiDatePick'];
        $tot_orderprice = $orderQuery['tot_orderprice'];
        $total_count = $orderQuery['total_count'];
        $result = $orderQuery['result'];
        $q1 = $orderQuery['q1'];
        $sql_common = $orderQuery['sql_common'];
        $sql_search = $orderQuery['sql_search'];
        $num = $orderQuery['num'];
        $total_page = $orderQuery['total_page'];
        #order_query 함수 사용시 리턴의 값들 끝
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"운송장번호수정\" class=\"btn_lsmall white\" onclick=\"document.pressed=this.value\">
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"" . base_url() . "AdminProcess/order_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";
        if ($member['grade'] == "99") {
            $btn_frmline .= "<a href=\"" . base_url() . "admin/order?code=delivery\" class=\"btn_lsmall\"><i class=\"fa fa-upload\"></i> 엑셀배송처리</a>";
        }

        $sflOpt = array('', '', '', '', '', '', '', '', '', '', '');
        $sflOpt[0] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt[1] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt[2] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt[3] = option_selected('name', $sfl, '주문자명');
        $sflOpt[4] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt[5] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt[6] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt[7] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt[8] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt[9] = option_selected('delivery_no', $sfl, '운송장번호');
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $sel_fieldOpt = array('', '');
        $sel_fieldOpt[0] = option_selected('od_time', $sel_field, "주문일");
        $sel_fieldOpt[1] = option_selected('receipt_time', $sel_field, "입금완료일");

        $payMethod = array('', '', '', '', '', '');
        $payMethod[0] = radio_checked('od_settle_case', $od_settle_case,  '', '전체');
        $payMethod[1] = radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
        $payMethod[2] = radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
        $payMethod[3] = radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
        $payMethod[4] = radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
        $payMethod[5] = radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

        $ectSelectChk = array('', '', '', '', '', '', '');
        $ectSelectChk[0] = check_checked('od_taxbill', $od_taxbill, 'Y', '세금계산서');
        $ectSelectChk[1] = check_checked('od_taxsave', $od_taxsave, 'Y', '현금영수증');
        $ectSelectChk[2] = check_checked('od_memo', $od_memo, 'Y', '배송메세지');
        $ectSelectChk[3] = check_checked('od_shop_memo', $od_shop_memo, 'Y', '관리자메모');
        $ectSelectChk[4] = check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
        $ectSelectChk[5] = check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
        $ectSelectChk[6] = check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $chk_cnt = 0;
        $order3Table = "";
        $i = 0;
        $chk_cnt = 0;
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->get_order_list($row, $amount, "and dan IN ('4','5')");

            $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
            $param = array($row['od_id']);
            $resObj = $this->query_model->returnQueryObj($sql, $param);
            $rowspan = $resObj->num_rows();
            $res = $resObj->result_array();
            $k = 0;
            foreach ($res as $key => $row2) {
                $gs = unserialize($row2['od_goods']);
                $type = isset($gs['type']) ? $gs['type'] : "normal";

                $order3Table .= "
                    <tr class=\"{$bg}\">";
                if ($k == 0) {
                    $order3Table .= "
                        <td rowspan=\"{$rowspan}\">" . $num-- . "</td>
                        <td rowspan=\"{$rowspan}\">
                            " . substr($row['od_time'], 2, 14) . "
                            {$sodr['disp_test']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                            {$sodr['disp_mobile']}
                            {$sodr['disp_baesong']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                            <label for=\"sit_sel_{$i}\" class=\"sound_only\">전체선택</label>
                            <input type=\"checkbox\" name=\"it_sel[]\" id=\"sit_sel_{$i}\">
                        </td>";
                }
                $gs['gname'] = isset($gs['gname']) ? $gs['gname'] : "";
                $gs['simg1'] = isset($gs['simg1']) ? $gs['simg1'] : "";
                $order3Table .= "
                        <td class=\"td_chk\">
                            <input type=\"hidden\" name=\"od_no[{$chk_cnt}]\" value=\"{$row2['od_no']}\">
                            <input type=\"checkbox\" name=\"chk[]\" value=\"{$chk_cnt}\" id=\"chk_{$chk_cnt}\" class=\"sct_sel_{$i}\">
                            <label for=\"chk_{$chk_cnt}\" class=\"sound_only\">{$gs['gname']}</label>
                        </td>";
                if ($member['grade'] == 99) {
                    $order3Table .= "
                            <td class=\"td_imgline\"><a href=\"" . base_url() . "shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30) . "</a></td>";
                }
                $order3Table .= "
                        <td class=\"td_itname\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row2['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></td>
                        <td>" . $this->funcomplex_lib->get_order_seller_id($row2['seller_id']) . "</td>
                        <td>
                            " . $this->funcomplex_lib->get_delivery_select("delivery[" . $chk_cnt . "]", $row2['delivery']) . "
                        </td>
                        <td><input type=\"text\" name=\"delivery_no[{$chk_cnt}]\" value=\"{$row2['delivery_no']}\" class=\"frm_input\" placeholder=\"개별 운송장번호\"></td>";
                if ($k == 0) {
                    $order3Table .= "
                        <td rowspan=\"{$rowspan}\">
                            {$sodr['disp_od_name']}
                            {$sodr['disp_mb_id']}
                        </td>
                        <td rowspan=\"{$rowspan}\">{$sodr['disp_de_name']}</td>
                        <td rowspan=\"{$rowspan}\" class=\"td_price\">{$sodr['disp_price']}</td>";
                }
                $chk_cnt++;
                $k++;
            }
            $i++;
        }
        if ($i == 0)
            $order3Table .= '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';
        $deliver2Tag = $this->funcomplex_lib->get_delivery_select("delivery2");
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $helpTag = help('선택한 주문을 일괄처리시에 입력하세요. 주문목록에서 개별 입력도 가능합니다.');
        $data = array(
            'code' => $code,
            'btn_frmline' => $btn_frmline,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'sflOpt' => $sflOpt,
            'stx' => $stx,
            'requestUrl' => $requestUrl,
            'page_rowsOpt' => $page_rowsOpt,
            'ectSelectChk' => $ectSelectChk,
            'payMethod' => $payMethod,
            'sel_fieldOpt' => $sel_fieldOpt,
            'get_search_date' => $get_search_date,
            'tot_orderprice' => $tot_orderprice,
            'q1' => $q1,
            'page' => $page,
            'order3Table' => $order3Table,
            'deliver2Tag' => $deliver2Tag,
            'helpTag' => $helpTag,
            'pagination' => $pagination,
            'total_count' => $total_count,
            'jqueryUiDatePick' => $jqueryUiDatePick
        );
        $this->load->view('admin/order/order_3', $data);
    }

    private function order_4($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');

        $stx = $this->input->get('stx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $page = $this->input->get('page');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $page_rows = $this->input->get('page_rows');

        #order_query 함수 사용시 리턴의 값들 시작
        $orderQuery = $this->order_query();
        $jqueryUiDatePick = $orderQuery['jqueryUiDatePick'];
        $tot_orderprice = $orderQuery['tot_orderprice'];
        $total_count = $orderQuery['total_count'];
        $result = $orderQuery['result'];
        $q1 = $orderQuery['q1'];
        $sql_common = $orderQuery['sql_common'];
        $sql_search = $orderQuery['sql_search'];
        $num = $orderQuery['num'];
        $total_page = $orderQuery['total_page'];
        #order_query 함수 사용시 리턴의 값들 끝
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"운송장번호수정\" class=\"btn_lsmall white\" onclick=\"document.pressed=this.value\">
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"" . base_url() . "AdminProcess/order_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";
        $sflOpt = array('', '', '', '', '', '', '', '', '', '', '');
        $sflOpt[0] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt[1] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt[2] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt[3] = option_selected('name', $sfl, '주문자명');
        $sflOpt[4] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt[5] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt[6] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt[7] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt[8] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt[9] = option_selected('delivery_no', $sfl, '운송장번호');
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $sel_fieldOpt = array('', '', '');
        $sel_fieldOpt[0] = option_selected('od_time', $sel_field, "주문일");
        $sel_fieldOpt[1] = option_selected('receipt_time', $sel_field, "입금완료일");
        $sel_fieldOpt[2] = option_selected('delivery_date', $sel_field, "배송일");

        $payMethod = array('', '', '', '', '', '');
        $payMethod[0] = radio_checked('od_settle_case', $od_settle_case,  '', '전체');
        $payMethod[1] = radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
        $payMethod[2] = radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
        $payMethod[3] = radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
        $payMethod[4] = radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
        $payMethod[5] = radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

        $ectSelectChk = array('', '', '', '', '', '', '');
        $ectSelectChk[0] = check_checked('od_taxbill', $od_taxbill, 'Y', '세금계산서');
        $ectSelectChk[1] = check_checked('od_taxsave', $od_taxsave, 'Y', '현금영수증');
        $ectSelectChk[2] = check_checked('od_memo', $od_memo, 'Y', '배송메세지');
        $ectSelectChk[3] = check_checked('od_shop_memo', $od_shop_memo, 'Y', '관리자메모');
        $ectSelectChk[4] = check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
        $ectSelectChk[5] = check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
        $ectSelectChk[6] = check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $chk_cnt = 0;
        $order4Table = "";
        $i = 0;
        $chk_cnt = 0;
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->get_order_list($row, $amount, "and dan IN ('2','3','5')");

            $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
            $param = array($row['od_id']);
            $resObj = $this->query_model->returnQueryObj($sql, $param);
            $rowspan = $resObj->num_rows();
            $res = $resObj->result_array();
            $k = 0;
            foreach ($res as $key => $row2) {
                $gs = unserialize($row2['od_goods']);
                $type = isset($gs['type']) ? $gs['type'] : "normal";

                $order4Table .= "
                    <tr class=\"{$bg}\">";
                if ($k == 0) {
                    $order4Table .= "
                        <td rowspan=\"{$rowspan}\">" . $num-- . "</td>
                        <td rowspan=\"{$rowspan}\">
                            " . substr($row['od_time'], 2, 14) . "
                            {$sodr['disp_test']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                            {$sodr['disp_mobile']}
                            {$sodr['disp_baesong']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                            <label for=\"sit_sel_{$i}\" class=\"sound_only\">전체선택</label>
                            <input type=\"checkbox\" name=\"it_sel[]\" id=\"sit_sel_{$i}\">
                        </td>";
                }
                $gs['gname'] = isset($gs['gname']) ? $gs['gname'] : "";
                $gs['simg1'] = isset($gs['simg1']) ? $gs['simg1'] : "";
                $order4Table .= "
                        <td class=\"td_chk\">
                            <input type=\"hidden\" name=\"od_no[{$chk_cnt}]\" value=\"{$row2['od_no']}\">
                            <input type=\"checkbox\" name=\"chk[]\" value=\"{$chk_cnt}\" id=\"chk_{$chk_cnt}\" class=\"sct_sel_{$i}\">
                            <label for=\"chk_{$chk_cnt}\" class=\"sound_only\">{$gs['gname']}</label>
                        </td>";
                if ($member['grade'] == 99) {
                    $order4Table .= "
                            <td class=\"td_imgline\"><a href=\"" . base_url() . "shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30) . "</a></td>";
                }
                $order4Table .= "
                        <td class=\"td_itname\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row2['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></td>
                        <td>" . $this->funcomplex_lib->get_order_seller_id($row2['seller_id']) . "</td>
                        <td>
                            " . $this->funcomplex_lib->get_delivery_select("delivery[" . $chk_cnt . "]", $row2['delivery']) . "
                        </td>
                        <td><input type=\"text\" name=\"delivery_no[{$chk_cnt}]\" value=\"{$row2['delivery_no']}\" class=\"frm_input\" placeholder=\"개별 운송장번호\"></td>
                        <td>" . get_delivery_inquiry($row2['delivery'], $row2['delivery_no'], 'btn_ssmall') . "</td>";
                if ($k == 0) {
                    $order4Table .= "
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
        if ($i == 0)
            $order4Table .= '<tr><td colspan="15" class="empty_table">자료가 없습니다.</td></tr>';

        $deliver2Tag = $this->funcomplex_lib->get_delivery_select("delivery2");
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $helpTag = help('선택한 주문을 일괄처리시에 입력하세요. 주문목록에서 개별 입력도 가능합니다.');
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'requestUrl' => $requestUrl,
            'stx' => $stx,
            'page' => $page,
            'btn_frmline' => $btn_frmline,
            'sflOpt' => $sflOpt,
            'sel_fieldOpt' => $sel_fieldOpt,
            'payMethod' => $payMethod,
            'ectSelectChk' => $ectSelectChk,
            'page_rowsOpt' => $page_rowsOpt,
            'order4Table' => $order4Table,
            'tot_orderprice' => $tot_orderprice,
            'q1' => $q1,
            'total_count' => $total_count,
            'deliver2Tag' => $deliver2Tag,
            'pagination' => $pagination,
            'helpTag' => $helpTag,
            'get_search_date' => $get_search_date,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'member' => $member
        );
        $this->load->view('admin/order/order_4', $data);
    }

    private function order_5($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');

        $stx = $this->input->get('stx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $page = $this->input->get('page');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $page_rows = $this->input->get('page_rows');
        $od_final = $this->input->get('od_final');

        #order_query 함수 사용시 리턴의 값들 시작
        $orderQuery = $this->order_query();
        $jqueryUiDatePick = $orderQuery['jqueryUiDatePick'];
        $tot_orderprice = $orderQuery['tot_orderprice'];
        $total_count = $orderQuery['total_count'];
        $result = $orderQuery['result'];
        $q1 = $orderQuery['q1'];
        $sql_common = $orderQuery['sql_common'];
        $sql_search = $orderQuery['sql_search'];
        $num = $orderQuery['num'];
        $total_page = $orderQuery['total_page'];
        #order_query 함수 사용시 리턴의 값들 끝
        $btn_frmline = "
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"" . base_url() . "AdminProcess/order_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";
        $sflOpt = array(
            'od_id' => '',
            'od_no' => '',
            'mb_id' => '',
            'name' => '',
            'deposit_name' => '',
            'bank' => '',
            'b_name' => '',
            'b_telephone' => '',
            'b_cellphone' => '',
            'delivery_no' => '',
            'pt_id' => ''
        );
        $sflOpt['od_id'] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt['od_no'] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt['mb_id'] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt['name'] = option_selected('name', $sfl, '주문자명');
        $sflOpt['deposit_name'] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt['bank'] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt['b_name'] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt['b_telephone'] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt['b_cellphone'] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt['delivery_no'] = option_selected('delivery_no', $sfl, '운송장번호');
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);


        $sel_fieldOpt = array('od_time' => '', 'receipt_time' => '', 'delivery_date' => '', 'invoice_date' => '', 'user_date' => '');
        $sel_fieldOpt['od_time'] = option_selected('od_time', $sel_field, "주문일");
        $sel_fieldOpt['receipt_time'] = option_selected('receipt_time', $sel_field, "입금완료일");
        $sel_fieldOpt['delivery_date'] = option_selected('delivery_date', $sel_field, "배송일");
        $sel_fieldOpt['invoice_date'] = option_selected('invoice_date', $sel_field, "배송완료일");
        $sel_fieldOpt['user_date'] = option_selected('user_date', $sel_field, "구매확정일");

        $payMethod = array('', '', '', '', '', '');
        $payMethod['all'] = radio_checked('od_settle_case', $od_settle_case,  '', '전체');
        $payMethod['noBank'] = radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
        $payMethod['at'] = radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
        $payMethod['credit'] = radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
        $payMethod['point'] = radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
        $payMethod['mileage'] = radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

        $ectSelectChk = array(
            'od_taxbill' => '',
            'od_taxsave' => '',
            'od_memo' => '',
            'od_shop_memo' => '',
            'od_receipt_point' => '',
            'od_coupon' => '',
            'od_escrow' => ''
        );
        $ectSelectChk['od_taxbill'] = check_checked('od_taxbill', $od_taxbill, 'Y', '세금계산서');
        $ectSelectChk['od_taxsave'] = check_checked('od_taxsave', $od_taxsave, 'Y', '현금영수증');
        $ectSelectChk['od_memo'] = check_checked('od_memo', $od_memo, 'Y', '배송메세지');
        $ectSelectChk['od_shop_memo'] = check_checked('od_shop_memo', $od_shop_memo, 'Y', '관리자메모');
        $ectSelectChk['od_receipt_point'] = check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
        $ectSelectChk['od_coupon'] = check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
        $ectSelectChk['od_escrow'] = check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');


        $od_finalArr = array('all' => '', 'pc' => '', 'pcn' => '');
        $od_finalArr['all'] = radio_checked('od_final', $od_final,  '', '전체');
        $od_finalArr['pc'] = radio_checked('od_final', $od_final, '1', '구매확정');
        $od_finalArr['pcn'] = radio_checked('od_final', $od_final, '0', '구매미확정');

        $chk_cnt = 0;
        $order5Table = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->get_order_list($row, $amount, "and dan IN ('2','3','4')");

            $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
            $param = array($row['od_id']);
            $resObj = $this->query_model->returnQueryObj($sql, $param);
            $res = $resObj->result_array();
            $rowspan = $resObj->num_rows();
            $k = 0;
            foreach ($res as $key => $row2) {
                $gs = unserialize($row2['od_goods']);
                $type = isset($gs['type']) ? $gs['type'] : "normal";

                // 배송정보 (예:배송회사|배송추적URL)
                $delivery_company = $delivery_url = "";
                if ($row2['delivery'] != "") {
                    list($delivery_company, $delivery_url) = explode('|', $row2['delivery']);
                }

                if (is_null_time($row2['user_date']))
                    $disp_final = '<span class="fc_107">대기</span>';
                else
                    $disp_final = '<span class="fc_00f">완료</span>';
                $order5Table .= "
                    <tr class=\"{$bg}\">";
                if ($k == 0) {
                    $order5Table .= "
                            <td rowspan=\"{$rowspan}\">" . $num-- . "</td>
                            <td rowspan=\"{$rowspan}\">
                                " . substr($row['od_time'], 2, 14) . "
                                {$sodr['disp_test']}
                            </td>
                            <td rowspan=\"{$rowspan}\">
                                <a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                                {$sodr['disp_mobile']}
                                {$sodr['disp_baesong']}
                            </td>
                            <td rowspan=\"{$rowspan}\">
                                <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                                <label for=\"sit_sel_{$i}\" class=\"sound_only\">전체선택</label>
                                <input type=\"checkbox\" name=\"it_sel[]\" id=\"sit_sel_{$i}\">
                            </td>";
                }
                $gs['gname'] = isset($gs['gname']) ? $gs['gname'] : "";
                $gs['simg1'] = isset($gs['simg1']) ? $gs['simg1'] : "";
                $order5Table .= "
                        <td class=\"td_chk\">
                            <input type=\"hidden\" name=\"od_no[{$chk_cnt}]\" value=\"{$row2['od_no']}\">
                            <input type=\"checkbox\" name=\"chk[]\" value=\"{$chk_cnt}\" id=\"chk_{$chk_cnt}\" class=\"sct_sel_{$i}\">
                            <label for=\"chk_{$chk_cnt}\" class=\"sound_only\">{$gs['gname']}</label>
                        </td>";
                if ($member['grade'] == 99) {
                    $order5Table .= "
                        <td class=\"td_imgline\"><a href=\"" . base_url() . "shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30) . "</a></td>";
                }
                $order5Table .= "   
                        <td class=\"td_itname\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row2['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></td>
                        <td>" . $this->funcomplex_lib->get_order_seller_id($row2['seller_id']) . "</td>
                        <td>{$disp_final}</td>
                        <td>{$delivery_company}</td>
                        <td>" . get_delivery_inquiry($row2['delivery'], $row2['delivery_no'], 'btn_ssmall') . "</td>";
                if ($k == 0) {
                    $order5Table .= "
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
        if ($i == 0)
            $order5Table .= '<tr><td colspan="15" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'requestUrl' => $requestUrl,
            'stx' => $stx,
            'page' => $page,
            'btn_frmline' => $btn_frmline,
            'sflOpt' => $sflOpt,
            'sel_fieldOpt' => $sel_fieldOpt,
            'payMethod' => $payMethod,
            'ectSelectChk' => $ectSelectChk,
            'page_rowsOpt' => $page_rowsOpt,
            'order5Table' => $order5Table,
            'tot_orderprice' => $tot_orderprice,
            'q1' => $q1,
            'total_count' => $total_count,
            'pagination' => $pagination,
            'get_search_date' => $get_search_date,
            'od_finalArr' => $od_finalArr,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'member' => $member
        );
        $this->load->view('admin/order/order_5', $data);
    }

    private function order_delivery($headTitle, $headTitleEnd)
    {
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/order/order_delivery', $data);
    }

    private function order_delivery_update($headTitle, $headTitleEnd)
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

        $total_count = 0;
        $succ_count = 0;
        $fail_count = 0;
        $fail_countTag = "";

        if ($_FILES['excelfile']['tmp_name']) {
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

            $fail_od_no = array();
            $total_count = 0;
            $fail_count = 0;
            $succ_count = 0;

            for ($k = 2; $k <= $data->sheets[0]['numRows']; $k++) {
                if (trim($data->sheets[0]['cells'][$k][2]))
                    continue;

                $total_count++;

                $od_id         = addslashes(trim($data->sheets[0]['cells'][$k][2]));  //주문번호
                $od_no         = addslashes(trim($data->sheets[0]['cells'][$k][3]));  //일련번호	
                $delivery     = addslashes(trim($data->sheets[0]['cells'][$k][13])); //배송회사
                $delivery_no = addslashes(trim($data->sheets[0]['cells'][$k][14])); //운송장번호

                $delivery = $this->gvcomplex_lib->get_info_delivery($delivery);

                if (!$od_id || !$od_no || !$delivery || !$delivery_no) {
                    $fail_count++;
                    $fail_od_no[] = $od_no;
                    continue;
                }

                // 주문정보
                $sql = "select dan from shop_order where od_id = ? and od_no = ?";
                $param = array($od_id, $od_no);
                $od = $this->query_model->returnOneArr($sql, $param);
                if (!$od) {
                    $fail_count++;
                    $fail_od_no[] = $od_no;
                    continue;
                }

                if ($od['dan'] != 3) {
                    $fail_count++;
                    $fail_od_no[] = $od_no;
                    continue;
                }

                $sql = " update shop_order set delivery = ? , delivery_no = ? , delivery_date = ? , dan = '4' where od_id = ? and od_no = ?";
                $param = array($delivery, $delivery_no, $OM_TIME_YMDHIS, $od_id, $od_no);
                $this->query_model->returnNull($sql, $param);

                $succ_count++;
            }
        }
        if ($fail_count > 0) {
            $fail_countTag = "
                <tr>
                    <th scope=\"row\">실패일련번호</th>
                    <td>" . implode(', ', $fail_od_no) . "</td>
                </tr>";
        }
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'total_count' => $total_count,
            'succ_count' => $succ_count,
            'fail_count' => $fail_count,
            'fail_countTag' => $fail_countTag
        );
        $this->load->view('admin/order/order_delivery_update', $data);
    }

    private function order_6($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');

        $stx = $this->input->get('stx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $od_final = $this->input->get('od_final');
        $page = $this->input->get('page');
        $od_status = $this->input->get('od_status');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $page_rows = $this->input->get('page_rows');

        #order_query 함수 사용시 리턴의 값들 시작
        $orderQuery = $this->order_query();
        $jqueryUiDatePick = $orderQuery['jqueryUiDatePick'];
        $tot_orderprice = $orderQuery['tot_orderprice'];
        $total_count = $orderQuery['total_count'];
        $result = $orderQuery['result'];
        $q1 = $orderQuery['q1'];
        $sql_common = $orderQuery['sql_common'];
        $sql_search = $orderQuery['sql_search'];
        $num = $orderQuery['num'];
        $total_page = $orderQuery['total_page'];
        #order_query 함수 사용시 리턴의 값들 끝
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall white\" onclick=\"document.pressed=this.value\">
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"" . base_url() . "AdminProcess/order_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";

        $sflOpt = array(
            'od_id' => '',
            'od_no' => '',
            'mb_id' => '',
            'name' => '',
            'deposit_name' => '',
            'bank' => '',
            'b_name' => '',
            'b_telephone' => '',
            'b_cellphone' => '',
            'delivery_no' => '',
            'pt_id' => ''
        );
        $sflOpt['od_id'] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt['od_no'] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt['mb_id'] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt['name'] = option_selected('name', $sfl, '주문자명');
        $sflOpt['deposit_name'] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt['bank'] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt['b_name'] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt['b_telephone'] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt['b_cellphone'] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt['delivery_no'] = option_selected('delivery_no', $sfl, '운송장번호');

        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $sel_fieldOpt = array('od_time' => '', 'cancel_date' => '');
        $sel_fieldOpt['od_time'] = option_selected('od_time', $sel_field, "주문일");
        $sel_fieldOpt['cancel_date'] = option_selected('cancel_date', $sel_field, "취소일");

        $od_settleArr = array('', '', '', '', '', '');
        $od_settleArr['all'] = radio_checked('od_settle_case', $od_settle_case, '', '전체');
        $od_settleArr['noBank'] = radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
        $od_settleArr['va'] = radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
        $od_settleArr['credit'] = radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
        $od_settleArr['point'] = radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
        $od_settleArr['mileage'] = radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

        $ectSelectChk = array(
            'od_taxbill' => '',
            'od_taxsave' => '',
            'od_memo' => '',
            'od_shop_memo' => '',
            'od_receipt_point' => '',
            'od_coupon' => '',
            'od_escrow' => ''
        );
        $ectSelectChk['od_taxbill'] = check_checked('od_taxbill', $od_taxbill, 'Y', '세금계산서');
        $ectSelectChk['od_taxsave'] = check_checked('od_taxsave', $od_taxsave, 'Y', '현금영수증');
        $ectSelectChk['od_memo'] = check_checked('od_memo', $od_memo, 'Y', '배송메세지');
        $ectSelectChk['od_shop_memo'] = check_checked('od_shop_memo', $od_shop_memo, 'Y', '관리자메모');
        $ectSelectChk['od_receipt_point'] = check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
        $ectSelectChk['od_coupon'] = check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
        $ectSelectChk['od_escrow'] = check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $i = 0;
        $order6Table = "";
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->get_order_list($row, $amount);

            $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
            $param = array($row['od_id']);
            $resObj = $this->query_model->returnQueryObj($sql, $param);
            $rowspan = $resObj->num_rows();
            $res = $resObj->result_array();
            $k = 0;
            foreach ($res as $key => $row2) {
                $gs = unserialize($row2['od_goods']);
                $type = isset($gs['type']) ? $gs['type'] : "normal";

                $order6Table .= "
                    <tr class=\"{$bg}\">";
                if ($k == 0) {
                    $order6Table .= "
                        <td rowspan=\"{$rowspan}\">" . $num-- . "</td>
                        <td rowspan=\"{$rowspan}\">
                            " . substr($row['od_time'], 2, 14) . "
                            {$sodr['disp_test']}
                        </td>
                        <td rowspan=\"{$rowspan}\">" . substr($row['cancel_date'], 2, 14) . "</td>
                        <td rowspan=\"{$rowspan}\">
                            <a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                            {$sodr['disp_mobile']}
                        </td>
                        <td rowspan=\"{$rowspan}\">
                            <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                            <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                            <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                        </td>";
                }
                $gs['gname'] = isset($gs['gname']) ? $gs['gname'] : "";
                $gs['simg1'] = isset($gs['simg1']) ? $gs['simg1'] : "";
                if ($member['grade'] == 99) {
                    $order6Table .= "
                            <td class=\"td_img\"><a href=\"" . base_url() . "shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30) . "</a></td>";
                }
                $order6Table .= "
                        <td class=\"td_itname\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row2['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></td>
                        <td>" . $this->funcomplex_lib->get_order_seller_id($row2['seller_id']) . "</td>";
                if ($k == 0) {
                    $order6Table .= "
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
        if ($i == 0)
            $order6Table .= '<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';

        $data = array(
            'pagination' => $pagination,
            'requestUrl' => $requestUrl,
            'stx' => $stx,
            'sflOpt' => $sflOpt,
            'get_search_date' => $get_search_date,
            'od_settleArr' => $od_settleArr,
            'page_rowsOpt' => $page_rowsOpt,
            'q1' => $q1,
            'page' => $page,
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'btn_frmline' => $btn_frmline,
            'tot_orderprice' => $tot_orderprice,
            'total_count' => $total_count,
            'sel_fieldOpt' => $sel_fieldOpt,
            'order6Table' => $order6Table,
            'pagination' => $pagination,
            'ectSelectChk' => $ectSelectChk,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'member' => $member
        );
        $this->load->view('admin/order/order_6', $data);
    }

    private function order_9($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');

        $stx = $this->input->get('stx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $od_final = $this->input->get('od_final');
        $page = $this->input->get('page');
        $od_status = $this->input->get('od_status');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $page_rows = $this->input->get('page_rows');

        #order_query 함수 사용시 리턴의 값들 시작
        $orderQuery = $this->order_query();
        $jqueryUiDatePick = $orderQuery['jqueryUiDatePick'];
        $tot_orderprice = $orderQuery['tot_orderprice'];
        $total_count = $orderQuery['total_count'];
        $result = $orderQuery['result'];
        $q1 = $orderQuery['q1'];
        $sql_common = $orderQuery['sql_common'];
        $sql_search = $orderQuery['sql_search'];
        $num = $orderQuery['num'];
        $total_page = $orderQuery['total_page'];
        #order_query 함수 사용시 리턴의 값들 끝
        $btn_frmline = "
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"" . base_url() . "AdminProcess/order_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";

        $sflOpt = array(
            'od_id' => '',
            'od_no' => '',
            'mb_id' => '',
            'name' => '',
            'deposit_name' => '',
            'bank' => '',
            'b_name' => '',
            'b_telephone' => '',
            'b_cellphone' => '',
            'delivery_no' => '',
            'pt_id' => ''
        );
        $sflOpt['od_id'] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt['od_no'] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt['mb_id'] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt['name'] = option_selected('name', $sfl, '주문자명');
        $sflOpt['deposit_name'] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt['bank'] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt['b_name'] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt['b_telephone'] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt['b_cellphone'] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt['delivery_no'] = option_selected('delivery_no', $sfl, '운송장번호');

        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $sel_fieldOpt = array('od_time' => '', 'refund_date' => '');
        $sel_fieldOpt['od_time'] = option_selected('od_time', $sel_field, "주문일");
        $sel_fieldOpt['refund_date'] = option_selected('refund_date', $sel_field, "환불완료일");

        $od_settleArr = array('', '', '', '', '', '');
        $od_settleArr['all'] = radio_checked('od_settle_case', $od_settle_case, '', '전체');
        $od_settleArr['noBank'] = radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
        $od_settleArr['va'] = radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
        $od_settleArr['credit'] = radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
        $od_settleArr['point'] = radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
        $od_settleArr['mileage'] = radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

        $ectSelectChk = array(
            'od_taxbill' => '',
            'od_taxsave' => '',
            'od_memo' => '',
            'od_shop_memo' => '',
            'od_receipt_point' => '',
            'od_coupon' => '',
            'od_escrow' => ''
        );
        $ectSelectChk['od_taxbill'] = check_checked('od_taxbill', $od_taxbill, 'Y', '세금계산서');
        $ectSelectChk['od_taxsave'] = check_checked('od_taxsave', $od_taxsave, 'Y', '현금영수증');
        $ectSelectChk['od_memo'] = check_checked('od_memo', $od_memo, 'Y', '배송메세지');
        $ectSelectChk['od_shop_memo'] = check_checked('od_shop_memo', $od_shop_memo, 'Y', '관리자메모');
        $ectSelectChk['od_receipt_point'] = check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
        $ectSelectChk['od_coupon'] = check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
        $ectSelectChk['od_escrow'] = check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $i = 0;
        $order9Table = "";
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->get_order_list($row, $amount);

            $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
            $param = array($row['od_id']);
            $resObj = $this->query_model->returnQueryObj($sql, $param);
            $rowspan = $resObj->num_rows();
            $res = $resObj->result_array();
            $k = 0;
            foreach ($res as $key => $row2) {
                $gs = unserialize($row2['od_goods']);
                $type = isset($gs['type']) ? $gs['type'] : "normal";

                $order9Table .= "
                    <tr class=\"{$bg}\">";
                if ($k == 0) {
                    $order9Table .= "
                            <td rowspan=\"{$rowspan}\">" . $num-- . "</td>
                            <td rowspan=\"{$rowspan}\">
                                " . substr($row['od_time'], 2, 14) . "
                                {$sodr['disp_test']}
                            </td>
                            <td rowspan=\"{$rowspan}\">" . substr($row['refund_date'], 2, 14) . "</td>
                            <td rowspan=\"{$rowspan}\">
                                <a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                                {$sodr['disp_mobile']}
                            </td>
                            <td rowspan=\"{$rowspan}\">
                                <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                                <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                                <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                            </td>";
                }
                $gs['simg1'] = isset($gs['simg1']) ? $gs['simg1'] : "";
                $gs['gname'] = isset($gs['gname']) ? $gs['gname'] : "";
                if ($member['grade'] == 99) {
                    $order9Table .= "
                        <td class=\"td_img\"><a href=\"" . base_url() . "shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30) . "</a></td>";
                }
                $order9Table .= "
                        <td class=\"td_itname\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row2['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></td>
                        <td>" . $this->funcomplex_lib->get_order_seller_id($row2['seller_id']) . "</td>";
                if ($k == 0) {
                    $order9Table .= "
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
        if ($i == 0)
            $order9Table .= '<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';
        $data = array(
            'pagination' => $pagination,
            'requestUrl' => $requestUrl,
            'stx' => $stx,
            'sflOpt' => $sflOpt,
            'get_search_date' => $get_search_date,
            'od_settleArr' => $od_settleArr,
            'page_rowsOpt' => $page_rowsOpt,
            'q1' => $q1,
            'page' => $page,
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'btn_frmline' => $btn_frmline,
            'tot_orderprice' => $tot_orderprice,
            'total_count' => $total_count,
            'sel_fieldOpt' => $sel_fieldOpt,
            'order9Table' => $order9Table,
            'pagination' => $pagination,
            'ectSelectChk' => $ectSelectChk,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'member' => $member
        );
        $this->load->view('admin/order/order_9', $data);
    }

    private function order_7($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');

        $stx = $this->input->get('stx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $od_final = $this->input->get('od_final');
        $page = $this->input->get('page');
        $od_status = $this->input->get('od_status');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $page_rows = $this->input->get('page_rows');

        #order_query 함수 사용시 리턴의 값들 시작
        $orderQuery = $this->order_query();
        $jqueryUiDatePick = $orderQuery['jqueryUiDatePick'];
        $tot_orderprice = $orderQuery['tot_orderprice'];
        $total_count = $orderQuery['total_count'];
        $result = $orderQuery['result'];
        $q1 = $orderQuery['q1'];
        $sql_common = $orderQuery['sql_common'];
        $sql_search = $orderQuery['sql_search'];
        $num = $orderQuery['num'];
        $total_page = $orderQuery['total_page'];
        #order_query 함수 사용시 리턴의 값들 끝
        $btn_frmline = "
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"" . base_url() . "AdminProcess/order_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";

        $sflOpt = array(
            'od_id' => '',
            'od_no' => '',
            'mb_id' => '',
            'name' => '',
            'deposit_name' => '',
            'bank' => '',
            'b_name' => '',
            'b_telephone' => '',
            'b_cellphone' => '',
            'delivery_no' => '',
            'pt_id' => ''
        );
        $sflOpt['od_id'] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt['od_no'] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt['mb_id'] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt['name'] = option_selected('name', $sfl, '주문자명');
        $sflOpt['deposit_name'] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt['bank'] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt['b_name'] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt['b_telephone'] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt['b_cellphone'] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt['delivery_no'] = option_selected('delivery_no', $sfl, '운송장번호');


        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $sel_fieldOpt = array('od_time' => '', 'return_date' => '');
        $sel_fieldOpt['od_time'] = option_selected('od_time', $sel_field, "주문일");
        $sel_fieldOpt['return_date'] = option_selected('return_date', $sel_field, "반품완료일");

        $od_settleArr = array('', '', '', '', '', '');
        $od_settleArr['all'] = radio_checked('od_settle_case', $od_settle_case, '', '전체');
        $od_settleArr['noBank'] = radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
        $od_settleArr['va'] = radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
        $od_settleArr['credit'] = radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
        $od_settleArr['point'] = radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
        $od_settleArr['mileage'] = radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

        $ectSelectChk = array(
            'od_taxbill' => '',
            'od_taxsave' => '',
            'od_memo' => '',
            'od_shop_memo' => '',
            'od_receipt_point' => '',
            'od_coupon' => '',
            'od_escrow' => ''
        );
        $ectSelectChk['od_taxbill'] = check_checked('od_taxbill', $od_taxbill, 'Y', '세금계산서');
        $ectSelectChk['od_taxsave'] = check_checked('od_taxsave', $od_taxsave, 'Y', '현금영수증');
        $ectSelectChk['od_memo'] = check_checked('od_memo', $od_memo, 'Y', '배송메세지');
        $ectSelectChk['od_shop_memo'] = check_checked('od_shop_memo', $od_shop_memo, 'Y', '관리자메모');
        $ectSelectChk['od_receipt_point'] = check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
        $ectSelectChk['od_coupon'] = check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
        $ectSelectChk['od_escrow'] = check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $i = 0;
        $order7Table = "";
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->get_order_list($row, $amount);

            $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
            $param = array($row['od_id']);
            $resObj = $this->query_model->returnQueryObj($sql, $param);
            $res = $resObj->result_array();
            $rowspan = $resObj->num_rows();
            $k = 0;
            foreach ($res as $key => $row2) {
                $gs = unserialize($row2['od_goods']);
                $type = isset($gs['type']) ? $gs['type'] : "normal";

                $order7Table .= "
                    <tr class=\"{$bg}\">";
                if ($k == 0) {
                    $order7Table .= "
                            <td rowspan=\"{$rowspan}\">" . $num-- . "</td>
                            <td rowspan=\"{$rowspan}\">
                                " . substr($row['od_time'], 2, 14) . "
                                {$sodr['disp_test']}
                            </td>
                            <td rowspan=\"{$rowspan}\">" . substr($row['return_date'], 2, 14) . "</td>
                            <td rowspan=\"{$rowspan}\">
                                <a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                                {$sodr['disp_mobile']}
                            </td>
                            <td rowspan=\"{$rowspan}\">
                                <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                                <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                                <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                            </td>";
                }
                if ($member['grade'] == 99) {
                    $order7Table .= "
                            <td class=\"td_img\"><a href=\"" . base_url() . "shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30) . "</a></td>";
                }
                $order7Table .= "
                        <td class=\"td_itname\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row2['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></td>
                        <td>" . $this->funcomplex_lib->get_order_seller_id($row2['seller_id']) . "</td>";
                if ($k == 0) {
                    $order7Table .= "
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
        if ($i == 0)
            $order7Table .= '<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';
        $data = array(
            'pagination' => $pagination,
            'requestUrl' => $requestUrl,
            'stx' => $stx,
            'sflOpt' => $sflOpt,
            'get_search_date' => $get_search_date,
            'od_settleArr' => $od_settleArr,
            'page_rowsOpt' => $page_rowsOpt,
            'q1' => $q1,
            'page' => $page,
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'btn_frmline' => $btn_frmline,
            'tot_orderprice' => $tot_orderprice,
            'total_count' => $total_count,
            'sel_fieldOpt' => $sel_fieldOpt,
            'order7Table' => $order7Table,
            'pagination' => $pagination,
            'ectSelectChk' => $ectSelectChk,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'member' => $member
        );
        $this->load->view('admin/order/order_7', $data);
    }

    private function order_8($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');

        $stx = $this->input->get('stx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $od_final = $this->input->get('od_final');
        $page = $this->input->get('page');
        $od_status = $this->input->get('od_status');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $page_rows = $this->input->get('page_rows');

        #order_query 함수 사용시 리턴의 값들 시작
        $orderQuery = $this->order_query();
        $jqueryUiDatePick = $orderQuery['jqueryUiDatePick'];
        $tot_orderprice = $orderQuery['tot_orderprice'];
        $total_count = $orderQuery['total_count'];
        $result = $orderQuery['result'];
        $q1 = $orderQuery['q1'];
        $sql_common = $orderQuery['sql_common'];
        $sql_search = $orderQuery['sql_search'];
        $num = $orderQuery['num'];
        $total_page = $orderQuery['total_page'];
        #order_query 함수 사용시 리턴의 값들 끝
        $btn_frmline = "
            <a href=\"#\" id=\"frmOrderPrint\" class=\"btn_lsmall white\"><i class=\"fa fa-print\"></i> 주문서출력</a>
            <a href=\"#\" id=\"frmOrderExcel\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 선택 엑셀저장</a>
            <a href=\"" . base_url() . "AdminProcess/order_excel?$q1\" class=\"btn_lsmall white\"><i class=\"fa fa-file-excel-o\"></i> 검색결과 엑셀저장</a>";

        $sflOpt = array(
            'od_id' => '',
            'od_no' => '',
            'mb_id' => '',
            'name' => '',
            'deposit_name' => '',
            'bank' => '',
            'b_name' => '',
            'b_telephone' => '',
            'b_cellphone' => '',
            'delivery_no' => '',
            'pt_id' => ''
        );
        $sflOpt['od_id'] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt['od_no'] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt['mb_id'] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt['name'] = option_selected('name', $sfl, '주문자명');
        $sflOpt['deposit_name'] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt['bank'] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt['b_name'] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt['b_telephone'] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt['b_cellphone'] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt['delivery_no'] = option_selected('delivery_no', $sfl, '운송장번호');

        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $sel_fieldOpt = array('od_time' => '', 'change_date' => '');
        $sel_fieldOpt['od_time'] = option_selected('od_time', $sel_field, "주문일");
        $sel_fieldOpt['change_date'] = option_selected('change_date', $sel_field, "교환완료일");

        $od_settleArr = array('', '', '', '', '', '');
        $od_settleArr['all'] = radio_checked('od_settle_case', $od_settle_case, '', '전체');
        $od_settleArr['noBank'] = radio_checked('od_settle_case', $od_settle_case, '무통장', '무통장');
        $od_settleArr['va'] = radio_checked('od_settle_case', $od_settle_case, '계좌이체', '계좌이체');
        $od_settleArr['credit'] = radio_checked('od_settle_case', $od_settle_case, '신용카드', '신용카드');
        $od_settleArr['point'] = radio_checked('od_settle_case', $od_settle_case, '포인트', '포인트');
        $od_settleArr['mileage'] = radio_checked('od_settle_case', $od_settle_case, '예치금', '예치금');

        $ectSelectChk = array(
            'od_taxbill' => '',
            'od_taxsave' => '',
            'od_memo' => '',
            'od_shop_memo' => '',
            'od_receipt_point' => '',
            'od_coupon' => '',
            'od_escrow' => ''
        );
        $ectSelectChk['od_taxbill'] = check_checked('od_taxbill', $od_taxbill, 'Y', '세금계산서');
        $ectSelectChk['od_taxsave'] = check_checked('od_taxsave', $od_taxsave, 'Y', '현금영수증');
        $ectSelectChk['od_memo'] = check_checked('od_memo', $od_memo, 'Y', '배송메세지');
        $ectSelectChk['od_shop_memo'] = check_checked('od_shop_memo', $od_shop_memo, 'Y', '관리자메모');
        $ectSelectChk['od_receipt_point'] = check_checked('od_receipt_point', $od_receipt_point, 'Y', '포인트주문');
        $ectSelectChk['od_coupon'] = check_checked('od_coupon', $od_coupon, 'Y', '쿠폰할인');
        $ectSelectChk['od_escrow'] = check_checked('od_escrow', $od_escrow, 'Y', '에스크로');

        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $i = 0;
        $order8Table = "";
        foreach ($result as $key => $row) {
            $bg = 'list' . ($i % 2);

            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->get_order_list($row, $amount);

            $sql = " select * {$sql_common} {$sql_search} and od_id = ? order by index_no ";
            $param = array($row['od_id']);
            $resObj = $this->query_model->returnQueryObj($sql, $param);
            $res = $resObj->result_array();
            $rowspan = $resObj->num_rows();
            $k = 0;
            foreach ($res as $key => $row2) {
                $gs = unserialize($row2['od_goods']);
                $type = isset($gs['type']) ? $gs['type'] : "normal";

                $order8Table .= "
                    <tr class=\"{$bg}\">";
                if ($k == 0) {
                    $order8Table .= "
                            <td rowspan=\"{$rowspan}\">" . $num-- . "</td>
                            <td rowspan=\"{$rowspan}\">
                                " . substr($row['od_time'], 2, 14) . "
                                {$sodr['disp_test']}
                            </td>
                            <td rowspan=\"{$rowspan}\">" . substr($row['return_date'], 2, 14) . "</td>
                            <td rowspan=\"{$rowspan}\">
                                <a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a>
                                {$sodr['disp_mobile']}
                            </td>
                            <td rowspan=\"{$rowspan}\">
                                <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                                <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                                <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                            </td>";
                }
                if ($member['grade'] == 99) {
                    $order8Table .= "
                            <td class=\"td_img\"><a href=\"" . base_url() . "shop/view?index_no={$row2['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_od_image($row['od_id'], $gs['simg1'], 30, 30) . "</a></td>";
                }
                $order8Table .= "
                        <td class=\"td_itname\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row2['gs_id']}&type={$type}\" target=\"_blank\">" . $this->funcomplex_lib->get_text($gs['gname']) . "</a></td>
                        <td>" . $this->funcomplex_lib->get_order_seller_id($row2['seller_id']) . "</td>";
                if ($k == 0) {
                    $order8Table .= "
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
        if ($i == 0)
            $order8Table .= '<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';
        $data = array(
            'pagination' => $pagination,
            'requestUrl' => $requestUrl,
            'stx' => $stx,
            'sflOpt' => $sflOpt,
            'get_search_date' => $get_search_date,
            'od_settleArr' => $od_settleArr,
            'page_rowsOpt' => $page_rowsOpt,
            'q1' => $q1,
            'page' => $page,
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'btn_frmline' => $btn_frmline,
            'tot_orderprice' => $tot_orderprice,
            'total_count' => $total_count,
            'sel_fieldOpt' => $sel_fieldOpt,
            'order8Table' => $order8Table,
            'pagination' => $pagination,
            'ectSelectChk' => $ectSelectChk,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'member' => $member
        );
        $this->load->view('admin/order/order_8', $data);
    }

    private function order_memo($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $sca = $this->input->get('sca');
        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_order ";
        $sql_search = " where shop_memo <> '' ";

        if ($sfl && $stx)
            $sql_search .= " and $sfl like '%$stx%' ";

        if (is_numeric($sca))
            $sql_search .= " and dan = '$sca' ";

        if ($fr_date && $to_date)
            $sql_search .= " and left(od_time,10) between '$fr_date' and '$to_date' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and left(od_time,10) between '$fr_date' and '$fr_date' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and left(od_time,10) between '$to_date' and '$to_date' ";

        $sql_group = " group by od_id ";
        $sql_order = " order by index_no desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql = " select od_id {$sql_common} {$sql_search} {$sql_group} ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $result = $resultObj->result_array();
        $total_count = $resultObj->num_rows();

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * {$sql_common} {$sql_search} {$sql_group} {$sql_order} limit {$from_record}, {$rows} ";
        $result = $this->query_model->returnArr($sql);
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $btn_frmline = '<input type="submit" name="act_button" value="선택삭제" class="btn_lsmall white" onclick="document.pressed=this.value">';
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);
        $sflOpt = array('', '', '', '', '', '', '', '', '', '', '');
        $sflOpt[0] = option_selected('od_id', $sfl, '주문번호');
        $sflOpt[1] = option_selected('od_no', $sfl, '일련번호');
        $sflOpt[2] = option_selected("mb_id", $sfl, '회원아이디');
        $sflOpt[3] = option_selected('name', $sfl, '주문자명');
        $sflOpt[4] = option_selected('deposit_name', $sfl, '입금자명');
        $sflOpt[5] = option_selected('bank', $sfl, '입금계좌');
        $sflOpt[6] = option_selected('b_name', $sfl, '수령자명');
        $sflOpt[7] = option_selected('b_telephone', $sfl, '수령자집전화');
        $sflOpt[8] = option_selected('b_cellphone', $sfl, '수령자핸드폰');
        $sflOpt[9] = option_selected('delivery_no', $sfl, '운송장번호');

        $scaArr = array('', '', '', '', '', '', '', '', '');
        $gw_status = $this->config->item('gw_status');
        $scaArr[0] = radio_checked('sca', $sca,  '', '전체');
        $scaArr[1] = radio_checked('sca', $sca, '1', $gw_status[1]);
        $scaArr[2] = radio_checked('sca', $sca, '2', $gw_status[2]);
        $scaArr[3] = radio_checked('sca', $sca, '3', $gw_status[3]);
        $scaArr[4] = radio_checked('sca', $sca, '4', $gw_status[4]);
        $scaArr[5] = radio_checked('sca', $sca, '5', $gw_status[5]);
        $scaArr[6] = radio_checked('sca', $sca, '6', $gw_status[6]);
        $scaArr[7] = radio_checked('sca', $sca, '7', $gw_status[7]);
        $scaArr[8] = radio_checked('sca', $sca, '8', $gw_status[8]);
        $i = 0;
        $orderMemoTable = "";
        foreach ($result as $key => $row) {
            if ($i == 0)
                $orderMemoTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $orderMemoTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"od_id[{$i}]\" value=\"{$row['od_id']}\">
                        <label for=\"chk_{$i}\" class=\"sound_only\">주문번호 {$row['od_id']}</label>
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td>" . substr($row['od_time'], 2, 14) . "</td>
                    <td><a href=\"" . base_url() . "admin/pop_orderform?od_id={$row['od_id']}\" onclick=\"win_open(this,'pop_orderform','1200','800','yes');return false;\" class=\"fc_197\">{$row['od_id']}</a></td>
                    <td>{$row['name']}</td>
                    <td>{$gw_status[$row['dan']]}</td>
                    <td class=\"tal\">{$row['shop_memo']}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $orderMemoTable .= '<tr><td colspan="7" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'btn_frmline' => $btn_frmline,
            'code' => $code,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'stx' => $stx,
            'get_search_date' => $get_search_date,
            'sflOpt' => $sflOpt,
            'scaArr' => $scaArr,
            'total_count' => $total_count,
            'q1' => $q1,
            'page' => $page,
            'orderMemoTable' => $orderMemoTable,
            'pagination' => $pagination
        );
        $this->load->view('admin/order_memo', $data);
    }

    private function timesale_state_list($code, $headTitle, $headTitleEnd)
    {
        $returnJson = $this->goods_sub_timesale($code, $headTitle, "timesale"); #goods_sub가 들어가는 화면은 서브에서 먼저 headTitle이 출력이 되고, 본 함수의 끝에 headTitleEnd에 들어가야함
        $q1 = $returnJson['q1'];
        $btn_frmline = $returnJson['btn_frmline'];
        $code = $this->input->get('code');
        $page = $this->input->get('page');
        $total_count = $returnJson['total_count'];
        $page_rows = $returnJson['page_rows'];
        $q2 = $returnJson['q2'];
        $result = $returnJson['result'];
        $num = $returnJson['num'];
        $total_page = $returnJson['total_page'];

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);
        $rankTag = $this->gvcomplex_lib->subject_sort_link('rank', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $updateTimeTag = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stockTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normalPriceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supplyPriceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $calculatePriceTag = $this->gvcomplex_lib->subject_sort_link('calculate_price', $q2);
        $goodsPriceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);
        $gPointTag = $this->gvcomplex_lib->subject_sort_link('gpoint', $q2);

        $i = 0;
        $listTable = "";
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            #var_dump((isset($gw_isopen[$row['isopen']])?$gw_isopen[$row['isopen']]:''));
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);
            $listTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['calculate_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['gpoint']) . "</td>
                    <td rowspan=\"2\"><input type=\"text\" name=\"rank[{$i}]\" value=\"{$row['rank']}\" class=\"frm_input\"></td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/order?code=timesale_state_detail&gs_id={$gs_id}\" class=\"btn_small\">현황</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $listTable .= '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'headTitleEnd' => $headTitleEnd,
            'q1' => $q1,
            'page' => $page,
            'btn_frmline' => $btn_frmline,
            'total_count' => $total_count,
            'page_rows' => $page_rows,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'rankTag' => $rankTag,
            'mb_idTag' => $mb_idTag,
            'updateTimeTag' => $updateTimeTag,
            'stockTag' => $stockTag,
            'normalPriceTag' => $normalPriceTag,
            'supplyPriceTag' => $supplyPriceTag,
            'calculatePriceTag' => $calculatePriceTag,
            'goodsPriceTag' => $goodsPriceTag,
            'gPointTag' => $gPointTag,
            'listTable' => $listTable,
            'pagination' => $pagination,
            'requestUrl' => $requestUrl,
            'return' => "timesale_state_list"
        );
        $this->load->view('admin/goods/timesale_list', $data);
    }

    private function timesale_state_detail($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $jqueryUiDatePick = $this->admin_template->datePicker();

        $qstr = $this->load->get_var('qstr');
        $sfl = $this->input->get('sfl');

        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $stx = $this->input->get('stx');
        $stx2 = $this->input->get('stx2');

        $page = $this->input->get('page');
        $gs_id = $this->input->get('gs_id');
        $qstr = $this->load->get_var('qstr');

        $query_string = "code=$code$qstr";
        $q1 = $query_string;

        $gs = $this->global_lib->get_goods($gs_id);

        $sflArr = array('', '');
        $sflArr[0] = option_selected('od_id', $sfl, '주문번호');
        $sflArr[1] = option_selected('name', $sfl, '구매자');
        $sflArr2 = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $imgTag = $this->funcomplex_lib->get_it_image($gs_id, $gs['simg1'], 40, 40);
        $order_cnt = $this->global_lib->get_order_cnt($gs_id);
        $stock_qty = $this->global_lib->get_it_timesale_stock_qty($gs['index_no']);
        $stock_qty = $stock_qty < 0 ? 0 : $stock_qty;

        $sql_search = "";

        if (!empty($stx)) {
            $sql_search .= " AND b.od_id='{$stx}'";
        }

        if ($fr_date && $to_date) {
            $sql_search .= " and b.purchase_date between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        } else if ($fr_date && !$to_date) {
            $sql_search .= " and b.purchase_date between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        } else if (!$fr_date && $to_date) {
            $sql_search .= " and b.purchase_date between '$to_date 00:00:00' and '$to_date 23:59:59' ";
        }

        $result = $this->global_lib->get_timesale_list($gs_id);
        $total_count = count($result);
        $sql2 = "SELECT sum(normal_price) AS sum_normal_price, sum(real_price) AS sum_real_price,sum(difference) AS sum_difference
                    FROM shop_goods_timesale
                    WHERE gs_id = ?
                    ORDER BY index_no ASC";
        $sum_data = $this->query_model->returnOneArr($sql2, $gs_id);

        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'code' => $code,
            'stx' => $stx,
            'stx2' => $stx2,
            'q1' => $q1,
            'page' => $page,
            'gs_id' => $gs_id,
            'sflArr' => $sflArr,
            'sflArr2' => $sflArr2,
            'gs' => $gs,
            'order_cnt' => $order_cnt,
            'stock_qty' => $stock_qty,
            'imgTag' => $imgTag,
            'result' => $result,
            'sum_data' => $sum_data,
            'total_count' => $total_count,
            'requestUrl' => $requestUrl,
            'jqueryUiDatePick' => $jqueryUiDatePick
        );
        $this->load->view('admin/goods/timesale_state_detail', $data);
    }

    private function order_query()
    {
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sel_field = $this->input->get('sel_field');
        $od_settle_case = $this->input->get('od_settle_case');
        $od_status = $this->input->get('od_status');
        $od_final = $this->input->get('od_final');
        $od_taxbill = $this->input->get('od_taxbill');
        $od_taxsave = $this->input->get('od_taxsave');
        $od_memo = $this->input->get('od_memo');
        $od_shop_memo = $this->input->get('od_shop_memo');
        $od_receipt_point = $this->input->get('od_receipt_point');
        $od_coupon = $this->input->get('od_coupon');
        $od_escrow = $this->input->get('od_escrow');
        $code = $this->input->get('code');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $page = $this->input->get('page');
        $where = array();
        $qstr = $this->load->get_var('qstr');


        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        if (isset($sel_field))         $qstr .= "&sel_field=$sel_field";
        if (isset($od_settle_case))     $qstr .= "&od_settle_case=" . urlencode($od_settle_case);
        if (isset($od_status))         $qstr .= "&od_status=$od_status";
        if (isset($od_final))         $qstr .= "&od_final=$od_final";
        if (isset($od_taxbill))         $qstr .= "&od_taxbill=$od_taxbill";
        if (isset($od_taxsave))         $qstr .= "&od_taxsave=$od_taxsave";
        if (isset($od_memo))             $qstr .= "&od_memo=$od_memo";
        if (isset($od_shop_memo))     $qstr .= "&od_shop_memo=$od_shop_memo";
        if (isset($od_receipt_point)) $qstr .= "&od_receipt_point=$od_receipt_point";
        if (isset($od_coupon))         $qstr .= "&od_coupon=$od_coupon";
        if (isset($od_escrow))         $qstr .= "&od_escrow=$od_escrow";

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_order ";

        $where = array();

        if ($code == 'list') // 전체주문내역
            $where[] = " dan != 0 ";
        else
            $where[] = " dan = '$code' ";

        if ($sfl && $stx)
            $where[] = " $sfl like '%$stx%' ";

        if ($od_settle_case)
            $where[] = " paymethod = '$od_settle_case' ";

        if (is_numeric($od_status))
            $where[] = " dan = '$od_status' ";

        if (is_numeric($od_final))
            $where[] = " user_ok = '$od_final' ";

        if ($od_taxbill)
            $where[] = " taxbill_yes = 'Y' ";

        if ($od_taxsave)
            $where[] = " taxsave_yes IN ('Y','S') ";

        if ($od_memo)
            $where[] = " memo <> '' ";

        if ($od_shop_memo)
            $where[] = " shop_memo <> '' ";

        if ($od_receipt_point)
            $where[] = " use_point != 0 ";

        if ($od_coupon)
            $where[] = " coupon_price != 0 ";

        if ($od_escrow)
            $where[] = " od_escrow = 1 ";

        if ($fr_date && $to_date)
            $where[] = " left({$sel_field},10) between '$fr_date' and '$to_date' ";
        else if ($fr_date && !$to_date)
            $where[] = " left({$sel_field},10) between '$fr_date' and '$fr_date' ";
        else if (!$fr_date && $to_date)
            $where[] = " left({$sel_field},10) between '$to_date' and '$to_date' ";

        if ($where) {
            $sql_search = ' where ' . implode(' and ', $where);
        }

        $sql_group = " group by od_id ";
        $sql_order = " order by index_no desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql = " select od_id {$sql_common} {$sql_search} {$sql_group} ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $total_count = $resultObj->num_rows();
        $result = $resultObj->result_array();

        if (isset($_SESSION['ss_page_rows']))
            $page_rows = $_SESSION['ss_page_rows'];
        else
            $page_rows = 30;

        $rows = $page_rows;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * {$sql_common} {$sql_search} {$sql_group} {$sql_order} limit {$from_record}, {$rows} ";
        $result = $this->query_model->returnArr($sql);

        $tot_orderprice = 0; // 총주문액
        $sql = " select od_id {$sql_common} {$sql_search} {$sql_group} {$sql_order} ";
        $res = $this->query_model->returnArr($sql);
        foreach ($res as $key => $row) {
            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $tot_orderprice += $amount['buyprice'];
        }
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $returnJson = array(
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'tot_orderprice' => $tot_orderprice,
            'total_count' => $total_count,
            'q1' => $q1,
            'result' => $result,
            'sql_common' => $sql_common,
            'sql_search' => $sql_search,
            'num' => $num,
            'total_page' => $total_page
        );
        return $returnJson;
    }

    #주문(order) 함수들 끝
    #상품(goods) 함수들
    public function goods()
    {
        $this->admin_template->admin_access();
        $this->admin_template->admin_head();
        $member = $this->load->get_var('member');
        $w = $this->input->get('w');

        $ADMIN_MENU5 = $this->config->item('ADMIN_MENU5');
        $ADMIN_MENU5_01 = $this->config->item('ADMIN_MENU5_01');
        $ADMIN_MENU5_02 = $this->config->item('ADMIN_MENU5_02');
        $ADMIN_MENU5_27 = $this->config->item('ADMIN_MENU5_27');
        $ADMIN_MENU5_03 = $this->config->item('ADMIN_MENU5_03');
        $ADMIN_MENU5_04 = $this->config->item('ADMIN_MENU5_04');
        $ADMIN_MENU5_05 = $this->config->item('ADMIN_MENU5_05');
        $ADMIN_MENU5_06 = $this->config->item('ADMIN_MENU5_06');
        $ADMIN_MENU5_07 = $this->config->item('ADMIN_MENU5_07');
        $ADMIN_MENU5_08 = $this->config->item('ADMIN_MENU5_08');
        $ADMIN_MENU5_09 = $this->config->item('ADMIN_MENU5_09');
        $ADMIN_MENU5_10 = $this->config->item('ADMIN_MENU5_10');
        $ADMIN_MENU5_11 = $this->config->item('ADMIN_MENU5_11');
        $ADMIN_MENU5_12 = $this->config->item('ADMIN_MENU5_12');
        $ADMIN_MENU5_13 = $this->config->item('ADMIN_MENU5_13');
        $ADMIN_MENU5_14 = $this->config->item('ADMIN_MENU5_14');
        $ADMIN_MENU5_15 = $this->config->item('ADMIN_MENU5_15');
        $ADMIN_MENU5_16 = $this->config->item('ADMIN_MENU5_16');
        $ADMIN_MENU5_17 = $this->config->item('ADMIN_MENU5_17');
        $ADMIN_MENU5_18 = $this->config->item('ADMIN_MENU5_18');
        $ADMIN_MENU5_20 = $this->config->item('ADMIN_MENU5_20');
        $ADMIN_MENU5_21 = $this->config->item('ADMIN_MENU5_21');
        // $ADMIN_MENU5_22=$this->config->item('ADMIN_MENU5_22');
        // $ADMIN_MENU5_23=$this->config->item('ADMIN_MENU5_23');
        $ADMIN_MENU5_24 = $this->config->item('ADMIN_MENU5_24');
        $ADMIN_MENU5_25 = $this->config->item('ADMIN_MENU5_25');
        $ADMIN_MENU5_26 = $this->config->item('ADMIN_MENU5_26');
        $ADMIN_MENU5_28 = $this->config->item('ADMIN_MENU5_28');
        $code = $this->input->get('code');

        $pg_title = $ADMIN_MENU5;
        $pg_num = 5;
        $snb_icon = "<i class=\"ionicons ion-bag\"></i>";

        if ($member['id'] != 'admin' && !$member['auth_' . $pg_num]) {
            alert("접근권한이 없습니다.");
        }
        $pg_title2 = "";
        if ($code == "form")                     $pg_title2 = ($w == "u") ? '상품 정보수정' : '신규 상품등록';
        if ($code == "list")                     $pg_title2 = $ADMIN_MENU5_01;
        if ($code == "type")                     $pg_title2 = $ADMIN_MENU5_02;
        if ($code == "best_item")             $pg_title2 = $ADMIN_MENU5_27;
        if ($code == "brand_list")             $pg_title2 = $ADMIN_MENU5_03;
        if ($code == "brand_form")             $pg_title2 = $ADMIN_MENU5_03;
        if ($code == "plan")                     $pg_title2 = $ADMIN_MENU5_04;
        if ($code == "plan_form")             $pg_title2 = $ADMIN_MENU5_04;
        if ($code == "price")                 $pg_title2 = $ADMIN_MENU5_05;
        if ($code == "stock")                 $pg_title2 = $ADMIN_MENU5_06;
        if ($code == "optstock")                 $pg_title2 = $ADMIN_MENU5_07;
        if ($code == "xls_reg")                 $pg_title2 = $ADMIN_MENU5_08;
        if ($code == "xls_reg_update")         $pg_title2 = $ADMIN_MENU5_08;
        if ($code == "xls_option_reg")         $pg_title2 = $ADMIN_MENU5_09;
        if ($code == "xls_option_reg_update") $pg_title2 = $ADMIN_MENU5_09;
        if ($code == "xls_mod")                 $pg_title2 = $ADMIN_MENU5_10;
        if ($code == "xls_mod_update")         $pg_title2 = $ADMIN_MENU5_10;
        if ($code == "getprice")                 $pg_title2 = $ADMIN_MENU5_11;
        if ($code == "getpoint")                 $pg_title2 = $ADMIN_MENU5_12;
        if ($code == "getuse")                 $pg_title2 = $ADMIN_MENU5_13;
        if ($code == "getmove")                 $pg_title2 = $ADMIN_MENU5_14;
        if ($code == "getbrand")                 $pg_title2 = $ADMIN_MENU5_15;
        if ($code == "getdelivery")             $pg_title2 = $ADMIN_MENU5_16;
        if ($code == "getbuylevel")             $pg_title2 = $ADMIN_MENU5_17;
        if ($code == "supply")                 $pg_title2 = $ADMIN_MENU5_18;
        if ($code == "qa")                     $pg_title2 = $ADMIN_MENU5_20;
        if ($code == "qa_form")                 $pg_title2 = $ADMIN_MENU5_20;
        if ($code == "review")                 $pg_title2 = $ADMIN_MENU5_21;
        // if($code == "gift")					 $pg_title2 = $ADMIN_MENU5_22;
        // if($code == "gift_form")			 $pg_title2 = $ADMIN_MENU5_22;
        // if($code == "coupon")				 $pg_title2 = $ADMIN_MENU5_23;
        // if($code == "coupon_form")			 $pg_title2 = $ADMIN_MENU5_23;
        if ($code == "supply2")                 $pg_title2 = $ADMIN_MENU5_24;
        if ($code == "popular_list")             $pg_title2 = $ADMIN_MENU5_25;
        if ($code == "popular_rank")             $pg_title2 = $ADMIN_MENU5_26;
        if ($code == "timesale")             $pg_title2 = $ADMIN_MENU5_28;
        $this->admin_template->admin_topmenu($pg_title, false, $pg_title2, $snb_icon);
        $this->goodsSkin($code, $pg_title2);
        $this->admin_template->admin_footer();
    }
    private function goodsSkin($code, $pg_title2)
    {
        $headTitle = "
            <div class=\"s_wrap\">
                <h1>{$pg_title2}</h1>";
        $headTitleEnd = "</div>";
        switch ($code) {
            case 'list':
                $this->goods_list($code, $headTitle, $headTitleEnd);
                break;
            case 'form':
                $this->goods_form($headTitle, $headTitleEnd);
                break;
            case 'supply':
                $this->goods_supply($code, $headTitle, $headTitleEnd);
                break;
            case 'supply2':
                $this->goods_supply2($code, $headTitle, $headTitleEnd);
                break;
            case 'userlist':
                $this->goods_userlist($code, $headTitle, $headTitleEnd);
                break;
            case 'type':
                $this->goods_type($code, $headTitle, $headTitleEnd);
                break;
            case 'best_item':
                $this->goods_best_item($code, $headTitle, $headTitleEnd);
                break;
            case 'brand_list':
                $this->goods_brand_list($code, $headTitle, $headTitleEnd);
                break;
            case 'brand_form':
                $this->goods_brand_form($headTitle, $headTitleEnd);
                break;
            case 'plan':
                $this->goods_plan($code, $headTitle, $headTitleEnd);
                break;
            case 'plan_form':
                $this->goods_plan_form($headTitle, $headTitleEnd);
                break;
            case 'stock':
                $this->goods_stock($code, $headTitle, $headTitleEnd);
                break;
            case 'optstock':
                $this->goods_optstock($code, $headTitle, $headTitleEnd);
                break;
            case 'xls_reg':
                $this->goods_xls_reg($headTitle, $headTitleEnd);
                break;
            case 'xls_reg_update':
                $this->goods_xls_reg_update($headTitle, $headTitleEnd);
                break;
            case 'xls_option_reg':
                $this->goods_xls_option_reg($headTitle, $headTitleEnd);
                break;
            case 'xls_option_reg_update':
                $this->goods_xls_option_reg_update($headTitle, $headTitleEnd);
                break;
            case 'xls_mod':
                $this->goods_xls_mod($headTitle, $headTitleEnd);
                break;
            case 'xls_mod_update':
                $this->goods_xls_mod_update($headTitle, $headTitleEnd);
                break;
            case 'getprice':
                $this->goods_getprice($code, $headTitle, $headTitleEnd);
                break;
            case 'getpoint':
                $this->goods_getpoint($code, $headTitle, $headTitleEnd);
                break;
            case 'getuse':
                $this->goods_getuse($code, $headTitle, $headTitleEnd);
                break;
            case 'getmove':
                $this->goods_getmove($code, $headTitle, $headTitleEnd);
                break;
            case 'getbrand':
                $this->goods_getbrand($code, $headTitle, $headTitleEnd);
                break;
            case 'getdelivery':
                $this->goods_getdelivery($code, $headTitle, $headTitleEnd);
                break;
            case 'getbuylevel':
                $this->goods_getbuylevel($code, $headTitle, $headTitleEnd);
                break;
            case 'qa':
                $this->goods_qa($code, $headTitle, $headTitleEnd);
                break;
            case 'qa_form':
                $this->goods_qa_form($headTitle, $headTitleEnd);
                break;
            case 'review':
                $this->goods_review($code, $headTitle, $headTitleEnd);
                break;
            case 'price':
                $this->goods_price($headTitle, $headTitleEnd);
                break;
            case 'popular_list':
                $this->goods_popular_list($code, $headTitle, $headTitleEnd);
                break;
            case 'popular_rank':
                $this->goods_popular_rank($code, $headTitle, $headTitleEnd);
                break;
            case 'timesale':
                $this->goods_timesale($code, $headTitle, $headTitleEnd);
                break;
            default:
                break;
        }
    }

    private function goods_sub($code, $headTitle, $type = null)
    {
        $qstr = $this->load->get_var('qstr');
        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $q_date_field = $this->input->get('q_date_field');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $q_stock_field = $this->input->get('q_stock_field');
        $fr_stock = $this->input->get('fr_stock');
        $to_stock = $this->input->get('to_stock');
        $q_price_field = $this->input->get('q_price_field');
        $fr_price = $this->input->get('fr_price');
        $to_price = $this->input->get('to_price');
        $q_isopen = $this->input->get('q_isopen');
        $q_option = $this->input->get('q_option');
        $q_supply = $this->input->get('q_supply');
        $q_notax = $this->input->get('q_notax');
        $page = $this->input->get('page');
        $orderby = $this->input->get('orderby');

        $stx = $this->input->get('stx');
        $sca = $this->input->get('sca');
        $sfl = $this->input->get('sfl');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');

        $q1 = $this->input->get('q1');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $sca = "";
        if ($sel_ca1) $sca = $sel_ca1;
        if ($sel_ca2) $sca = $sel_ca2;
        if ($sel_ca3) $sca = $sel_ca3;
        if ($sel_ca4) $sca = $sel_ca4;
        if ($sel_ca5) $sca = $sel_ca5;

        if (isset($sel_ca1))            $qstr .= "&sel_ca1=$sel_ca1";
        if (isset($sel_ca2))            $qstr .= "&sel_ca2=$sel_ca2";
        if (isset($sel_ca3))            $qstr .= "&sel_ca3=$sel_ca3";
        if (isset($sel_ca4))            $qstr .= "&sel_ca4=$sel_ca4";
        if (isset($sel_ca5))            $qstr .= "&sel_ca5=$sel_ca5";
        if (isset($q_date_field))    $qstr .= "&q_date_field=$q_date_field";
        if (isset($q_brand))            $qstr .= "&q_brand=$q_brand";
        if (isset($q_zone))            $qstr .= "&q_zone=$q_zone";
        if (isset($q_stock_field))    $qstr .= "&q_stock_field=$q_stock_field";
        if (isset($fr_stock))        $qstr .= "&fr_stock=$fr_stock";
        if (isset($to_stock))        $qstr .= "&to_stock=$to_stock";
        if (isset($q_price_field))    $qstr .= "&q_price_field=$q_price_field";
        if (isset($fr_price))        $qstr .= "&fr_price=$fr_price";
        if (isset($to_price))        $qstr .= "&to_price=$to_price";
        if (isset($q_isopen))        $qstr .= "&q_isopen=$q_isopen";
        if (isset($q_option))        $qstr .= "&q_option=$q_option";
        if (isset($q_supply))        $qstr .= "&q_supply=$q_supply";
        if (isset($q_notax))            $qstr .= "&q_notax=$q_notax";

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_goods ";
        $sql_search = " where use_aff = 0 and shop_state = 0 and type='normal'";
        include('./admin/good/goods_query.inc.php');
        if (!$orderby) {
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

        if (isset($_SESSION['ss_page_rows']))
            $page_rows = $_SESSION['ss_page_rows'];
        else
            $page_rows = 30;

        $rows = $page_rows;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $sflOpt = array('', '', '', '', '', '', '');
        $sflOpt[0] = option_selected('gname', $sfl, '상품명');
        $sflOpt[1] = option_selected('gcode', $sfl, '상품코드');
        $sflOpt[2] = option_selected('mb_id', $sfl, '업체코드');
        $sflOpt[3] = option_selected('seller_name', $sfl, '업체명');
        // $sflOpt[4]=option_selected('origin', $sfl, '원산지');
        // $sflOpt[5]=option_selected('model', $sfl, '모델명');
        // $sflOpt[6]=option_selected('explan', $sfl, '짧은설명');
        $scaSelect = array('', '', '', '', '');
        $scaSelect[0] = $this->funcomplex_lib->get_category_select_1('sel_ca1', $sca);
        $scaSelect[1] = $this->funcomplex_lib->get_category_select_2('sel_ca2', $sca);
        $scaSelect[2] = $this->funcomplex_lib->get_category_select_3('sel_ca3', $sca);
        $scaSelect[3] = $this->funcomplex_lib->get_category_select_4('sel_ca4', $sca);
        $scaSelect[4] = $this->funcomplex_lib->get_category_select_5('sel_ca5', $sca);
        $q_date_fieldArr = array('update' => '', "reg" => "");
        $q_date_fieldArr['update'] = option_selected('update_time', $q_date_field, "최근수정일");
        $q_date_fieldArr['reg'] = option_selected('reg_time', $q_date_field, "최초등록일");
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);
        $sql = "select * from shop_brand where br_user_yes='0' order by br_name asc ";
        $res = $this->query_model->returnArr($sql);
        $brandStr = "";
        $brandStr .= option_selected('', $q_brand, '전체');
        foreach ($res as $key => $row) {
            $brandStr .= option_selected($row['br_id'], $q_brand, $row['br_name']);
        }
        $q_zoneStr = array('', '', '', '', '', '', '', '', '', '');
        $q_zoneStr[0] = option_selected('',  $q_zone, '전체');
        $q_zoneStr[1] = option_selected('전국', $q_zone, '전국');
        $q_zoneStr[2] = option_selected('강원도', $q_zone, '강원도');
        $q_zoneStr[3] = option_selected('경기도', $q_zone, '경기도');
        $q_zoneStr[4] = option_selected('경상도', $q_zone, '경상도');
        $q_zoneStr[5] = option_selected('서울/경기도', $q_zone, '서울/경기도');
        $q_zoneStr[6] = option_selected('서울특별시', $q_zone, '서울특별시');
        $q_zoneStr[7] = option_selected('전라도', $q_zone, '전라도');
        $q_zoneStr[8] = option_selected('제주도', $q_zone, '제주도');
        $q_zoneStr[9] = option_selected('충청도', $q_zone, '충청도');

        $stock_fieldArr = array('stock' => "", 'noti' => "");
        $stock_fieldArr['stock'] = option_selected('stock_qty', $q_stock_field, "재고수량");
        $stock_fieldArr['noti'] = option_selected('noti_qty', $q_stock_field, "통보수량");

        $price_fieldArr = array('goods' => "", 'supply' => '', 'normal' => '', 'point' => '');
        $price_fieldArr['goods'] = option_selected('goods_price', $q_price_field, "판매가격");
        $price_fieldArr['supply'] = option_selected('supply_price', $q_price_field, "공급가격");
        $price_fieldArr['normal'] = option_selected('normal_price', $q_price_field, "시중가격");
        $price_fieldArr['point'] = option_selected('gpoint', $q_price_field, "포인트");
        $q_isopenArr = array('', '', '', '', '');
        $q_isopenArr[0] = radio_checked('q_isopen', $q_isopen,  '', '전체');
        $q_isopenArr[1] = radio_checked('q_isopen', $q_isopen, '1', '진열');
        $q_isopenArr[2] = radio_checked('q_isopen', $q_isopen, '2', '품절');
        $q_isopenArr[3] = radio_checked('q_isopen', $q_isopen, '3', '단종');
        $q_isopenArr[4] = radio_checked('q_isopen', $q_isopen, '4', '중지');

        $q_optionArr = array('all' => '', 'use' => '', 'unuse' => '');
        $q_optionArr['all'] = radio_checked('q_option', $q_option,  '', '전체');
        $q_optionArr['use'] = radio_checked('q_option', $q_option, '1', '사용');
        $q_optionArr['unuse'] = radio_checked('q_option', $q_option, '0', '미사용');


        $q_notaxArr = array('all' => '', '1' => '', '0' => '');
        $q_notaxArr['all'] = radio_checked('q_notax', $q_notax,  '', '전체');
        $q_notaxArr['1'] = radio_checked('q_notax', $q_notax, '1', '과세');
        $q_notaxArr['0'] = radio_checked('q_notax', $q_notax, '0', '비과세');

        $q_supplyArr = array('all' => '', '1' => '', '0' => '');
        $q_supplyArr['all'] = radio_checked('q_supply', $q_supply,  '', '전체');
        $q_supplyArr['1'] = radio_checked('q_supply', $q_supply, '1', '사용');
        $q_supplyArr['0'] = radio_checked('q_supply', $q_supply, '0', '미사용');

        $data = array(
            'headTitle' => $headTitle,
            'code' => $code,
            'stx' => $stx,
            'sflOpt' => $sflOpt,
            'scaSelect' => $scaSelect,
            'q_date_fieldArr' => $q_date_fieldArr,
            'get_search_date' => $get_search_date,
            'brandStr' => $brandStr,
            'q_zoneStr' => $q_zoneStr,
            'stock_fieldArr' => $stock_fieldArr,
            'fr_stock' => $fr_stock,
            'to_stock' => $to_stock,
            'price_fieldArr' => $price_fieldArr,
            'fr_price' => $fr_price,
            'to_price' => $to_price,
            'q_isopenArr' => $q_isopenArr,
            'q_optionArr' => $q_optionArr,
            'q_notaxArr' => $q_notaxArr,
            'q_supplyArr' => $q_supplyArr,
            'jqueryUiDatePick' => $jqueryUiDatePick
        );
        $this->load->view('admin/goods/sub', $data);
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택순위수정\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택상품복사(열린장터)\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택상품복사(함께장터)\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "AdminProcess/goods_list_excel?flag=normal&$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장(열린장터)</a>
            <a href=\"" . base_url() . "AdminProcess/goods_list_excel?flag=timesale&$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장(함께장터)</a>
            <a href=\"" . base_url() . "AdminProcess/goods_list_excel?flag=all&$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장(전체)</a>
            <a href=\"" . base_url() . "admin/goods?code=form&type=normal\" class=\"fr btn_lsmall red marl5\"><i class=\"ionicons ion-android-add\"></i> 상품등록</a>";
        $returnJson = array(
            'q1' => $q1,
            'q2' => $q2,
            'page' => $page,
            'total_count' => $total_count,
            'page_rows' => $page_rows,
            'result' => $result,
            'btn_frmline' => $btn_frmline,
            'num' => $num,
            'total_page' => $total_page
        );
        return $returnJson;
    }

    private function goods_sub_timesale($code, $headTitle, $type = null)
    {
        $qstr = $this->load->get_var('qstr');
        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $q_date_field = $this->input->get('q_date_field');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $q_stock_field = $this->input->get('q_stock_field');
        $fr_stock = $this->input->get('fr_stock');
        $to_stock = $this->input->get('to_stock');
        $q_price_field = $this->input->get('q_price_field');
        $fr_price = $this->input->get('fr_price');
        $to_price = $this->input->get('to_price');
        $q_isopen = $this->input->get('q_isopen');
        $q_option = $this->input->get('q_option');
        $q_supply = $this->input->get('q_supply');
        $q_notax = $this->input->get('q_notax');
        $page = $this->input->get('page');
        $orderby = $this->input->get('orderby');

        $stx = $this->input->get('stx');
        $sca = $this->input->get('sca');
        $sfl = $this->input->get('sfl');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $q_open = $this->input->get('q_open');

        $q1 = $this->input->get('q1');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $sca = "";
        if ($sel_ca1) $sca = $sel_ca1;
        if ($sel_ca2) $sca = $sel_ca2;
        if ($sel_ca3) $sca = $sel_ca3;
        if ($sel_ca4) $sca = $sel_ca4;
        if ($sel_ca5) $sca = $sel_ca5;

        if (isset($sel_ca1))            $qstr .= "&sel_ca1=$sel_ca1";
        if (isset($sel_ca2))            $qstr .= "&sel_ca2=$sel_ca2";
        if (isset($sel_ca3))            $qstr .= "&sel_ca3=$sel_ca3";
        if (isset($sel_ca4))            $qstr .= "&sel_ca4=$sel_ca4";
        if (isset($sel_ca5))            $qstr .= "&sel_ca5=$sel_ca5";
        if (isset($q_date_field))    $qstr .= "&q_date_field=$q_date_field";
        if (isset($q_brand))            $qstr .= "&q_brand=$q_brand";
        if (isset($q_zone))            $qstr .= "&q_zone=$q_zone";
        if (isset($q_stock_field))    $qstr .= "&q_stock_field=$q_stock_field";
        if (isset($fr_stock))        $qstr .= "&fr_stock=$fr_stock";
        if (isset($to_stock))        $qstr .= "&to_stock=$to_stock";
        if (isset($q_price_field))    $qstr .= "&q_price_field=$q_price_field";
        if (isset($fr_price))        $qstr .= "&fr_price=$fr_price";
        if (isset($to_price))        $qstr .= "&to_price=$to_price";
        if (isset($q_isopen))        $qstr .= "&q_isopen=$q_isopen";
        if (isset($q_option))        $qstr .= "&q_option=$q_option";
        if (isset($q_supply))        $qstr .= "&q_supply=$q_supply";
        if (isset($q_notax))            $qstr .= "&q_notax=$q_notax";
        if (isset($q_open))            $qstr .= "&q_open=$q_open";

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_goods ";
        $sql_search = " where use_aff = 0 and shop_state = 0 ";
        if ($type == "timesale") {
            $sql_search .= "and type='timesale'";
        }
        include('./admin/good/goods_query.inc.php');
        if (!$orderby) {
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

        if (isset($_SESSION['ss_page_rows']))
            $page_rows = $_SESSION['ss_page_rows'];
        else
            $page_rows = 30;

        $rows = $page_rows;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $sflOpt = array('', '', '', '', '', '', '');
        $sflOpt[0] = option_selected('gname', $sfl, '상품명');
        $sflOpt[1] = option_selected('gcode', $sfl, '상품코드');
        $sflOpt[2] = option_selected('mb_id', $sfl, '업체코드');
        $sflOpt[3] = option_selected('seller_name', $sfl, '업체명');

        $scaSelect = array('', '', '', '', '');
        $scaSelect[0] = $this->funcomplex_lib->get_category_select_1('sel_ca1', $sca);
        $scaSelect[1] = $this->funcomplex_lib->get_category_select_2('sel_ca2', $sca);
        $scaSelect[2] = $this->funcomplex_lib->get_category_select_3('sel_ca3', $sca);
        $scaSelect[3] = $this->funcomplex_lib->get_category_select_4('sel_ca4', $sca);
        $scaSelect[4] = $this->funcomplex_lib->get_category_select_5('sel_ca5', $sca);

        $q_date_fieldArr = array('', '', '');
        $q_date_fieldArr[0] = option_selected('sale_date', $q_date_field, "판매기간");
        $q_date_fieldArr[1] = option_selected('reg_time', $q_date_field, "등록일");
        $q_date_fieldArr[2] = option_selected('approval_time', $q_date_field, "승인일");
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);
        $sql = "select * from shop_brand where br_user_yes='0' order by br_name asc ";
        $res = $this->query_model->returnArr($sql);
        $brandStr = "";
        $brandStr .= option_selected('', $q_brand, '전체');
        foreach ($res as $key => $row) {
            $brandStr .= option_selected($row['br_id'], $q_brand, $row['br_name']);
        }

        $q_zoneStr = array('', '', '', '', '', '', '', '', '', '');
        $q_zoneStr[0] = option_selected('',  $q_zone, '전체');
        $q_zoneStr[1] = option_selected('전국', $q_zone, '전국');
        $q_zoneStr[2] = option_selected('강원도', $q_zone, '강원도');
        $q_zoneStr[3] = option_selected('경기도', $q_zone, '경기도');
        $q_zoneStr[4] = option_selected('경상도', $q_zone, '경상도');
        $q_zoneStr[5] = option_selected('서울/경기도', $q_zone, '서울/경기도');
        $q_zoneStr[6] = option_selected('서울특별시', $q_zone, '서울특별시');
        $q_zoneStr[7] = option_selected('전라도', $q_zone, '전라도');
        $q_zoneStr[8] = option_selected('제주도', $q_zone, '제주도');
        $q_zoneStr[9] = option_selected('충청도', $q_zone, '충청도');

        $stock_fieldArr = array('stock' => "", 'noti' => "");
        $stock_fieldArr['stock'] = option_selected('stock_qty', $q_stock_field, "재고수량");
        $stock_fieldArr['noti'] = option_selected('noti_qty', $q_stock_field, "통보수량");

        $price_fieldArr = array('goods' => "", 'supply' => '', 'normal' => '', 'point' => '');
        $price_fieldArr['goods'] = option_selected('goods_price', $q_price_field, "판매가격");
        $price_fieldArr['supply'] = option_selected('supply_price', $q_price_field, "공급가격");
        $price_fieldArr['normal'] = option_selected('normal_price', $q_price_field, "시중가격");
        $price_fieldArr['point'] = option_selected('gpoint', $q_price_field, "포인트");
        $q_isopenArr = array('', '', '', '', '');
        $q_isopenArr[0] = radio_checked('q_isopen', $q_isopen,  '', '전체');
        $q_isopenArr[1] = radio_checked('q_isopen', $q_isopen, '1', '진열');
        $q_isopenArr[2] = radio_checked('q_isopen', $q_isopen, '2', '품절');
        $q_isopenArr[3] = radio_checked('q_isopen', $q_isopen, '3', '단종');
        $q_isopenArr[4] = radio_checked('q_isopen', $q_isopen, '4', '중지');

        $q_optionArr = array('all' => '', 'use' => '', 'unuse' => '');
        $q_optionArr['all'] = radio_checked('q_option', $q_option,  '', '전체');
        $q_optionArr['use'] = radio_checked('q_option', $q_option, '1', '사용');
        $q_optionArr['unuse'] = radio_checked('q_option', $q_option, '0', '미사용');


        $q_notaxArr = array('all' => '', '1' => '', '0' => '');
        $q_notaxArr['all'] = radio_checked('q_notax', $q_notax,  '', '전체');
        $q_notaxArr['1'] = radio_checked('q_notax', $q_notax, '1', '과세');
        $q_notaxArr['0'] = radio_checked('q_notax', $q_notax, '0', '비과세');

        $q_supplyArr = array('all' => '', '1' => '', '0' => '');
        $q_supplyArr['all'] = radio_checked('q_supply', $q_supply,  '', '전체');
        $q_supplyArr['1'] = radio_checked('q_supply', $q_supply, '1', '사용');
        $q_supplyArr['0'] = radio_checked('q_supply', $q_supply, '0', '미사용');

        $q_openArr = array('', '');
        $q_openArr[0] = radio_checked('q_open', $q_open, '0', '오픈예정');
        $q_openArr[1] = radio_checked('q_open', $q_open, '1', '판매중');

        $data = array(
            'headTitle' => $headTitle,
            'code' => $code,
            'stx' => $stx,
            'sflOpt' => $sflOpt,
            'scaSelect' => $scaSelect,
            'q_date_fieldArr' => $q_date_fieldArr,
            'get_search_date' => $get_search_date,
            'brandStr' => $brandStr,
            'q_zoneStr' => $q_zoneStr,
            'stock_fieldArr' => $stock_fieldArr,
            'fr_stock' => $fr_stock,
            'to_stock' => $to_stock,
            'price_fieldArr' => $price_fieldArr,
            'fr_price' => $fr_price,
            'to_price' => $to_price,
            'q_isopenArr' => $q_isopenArr,
            'q_optionArr' => $q_optionArr,
            'q_notaxArr' => $q_notaxArr,
            'q_supplyArr' => $q_supplyArr,
            'q_openArr' => $q_openArr,
            'jqueryUiDatePick' => $jqueryUiDatePick
        );
        $this->load->view('admin/goods/sub_timesale', $data);
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택순위수정\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택상품복사(열린장터)\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택상품복사(함께장터)\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "AdminProcess/goods_list_excel?flag=normal&$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장(열린장터)</a>
            <a href=\"" . base_url() . "AdminProcess/goods_list_excel?flag=timesale&$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장(함께장터)</a>
            <a href=\"" . base_url() . "AdminProcess/goods_list_excel?flag=all&$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장(전체)</a>
            <a href=\"" . base_url() . "admin/goods?code=form&type=timesale\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 상품등록</a>";
        $returnJson = array(
            'q1' => $q1,
            'q2' => $q2,
            'page' => $page,
            'total_count' => $total_count,
            'page_rows' => $page_rows,
            'result' => $result,
            'btn_frmline' => $btn_frmline,
            'num' => $num,
            'total_page' => $total_page
        );
        return $returnJson;
    }

    private function goods_list($code, $headTitle, $headTitleEnd)
    {
        $returnJson = $this->goods_sub($code, $headTitle); #goods_sub가 들어가는 화면은 서브에서 먼저 headTitle이 출력이 되고, 본 함수의 끝에 headTitleEnd에 들어가야함
        $q1 = $returnJson['q1'];
        $btn_frmline = $returnJson['btn_frmline'];
        $code = $this->input->get('code');
        $page = $this->input->get('page');
        $total_count = $returnJson['total_count'];
        $page_rows = $returnJson['page_rows'];
        $q2 = $returnJson['q2'];
        $result = $returnJson['result'];
        $num = $returnJson['num'];
        $total_page = $returnJson['total_page'];

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);
        $rankTag = $this->gvcomplex_lib->subject_sort_link('rank', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $updateTimeTag = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stockTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normalPriceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supplyPriceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $goodsPriceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);
        $gPointTag = $this->gvcomplex_lib->subject_sort_link('gpoint', $q2);

        $i = 0;
        $listTable = "";
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            #var_dump((isset($gw_isopen[$row['isopen']])?$gw_isopen[$row['isopen']]:''));
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);
            $listTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['supply_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['gpoint']) . "</td>
                    <td rowspan=\"2\"><input type=\"text\" name=\"rank[{$i}]\" value=\"{$row['rank']}\" class=\"frm_input\"></td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}&type={$row['type']}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $listTable .= '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'headTitleEnd' => $headTitleEnd,
            'q1' => $q1,
            'page' => $page,
            'btn_frmline' => $btn_frmline,
            'total_count' => $total_count,
            'page_rows' => $page_rows,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'rankTag' => $rankTag,
            'mb_idTag' => $mb_idTag,
            'updateTimeTag' => $updateTimeTag,
            'stockTag' => $stockTag,
            'normalPriceTag' => $normalPriceTag,
            'supplyPriceTag' => $supplyPriceTag,
            'goodsPriceTag' => $goodsPriceTag,
            'gPointTag' => $gPointTag,
            'listTable' => $listTable,
            'pagination' => $pagination,
            'requestUrl' => $requestUrl
        );
        $this->load->view('admin/goods/list', $data);
    }
    private function goods_form($headTitle, $headTitleEnd)
    {
        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $q_date_field = $this->input->get('q_date_field');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $q_stock_field = $this->input->get('q_stock_field');
        $fr_stock = $this->input->get('fr_stock');
        $to_stock = $this->input->get('to_stock');
        $q_price_field = $this->input->get('q_price_field');
        $fr_price = $this->input->get('fr_price');
        $to_price = $this->input->get('to_price');
        $q_isopen = $this->input->get('q_isopen');
        $q_option = $this->input->get('q_option');
        $q_supply = $this->input->get('q_supply');
        $q_notax = $this->input->get('q_notax');
        $bak = $this->input->get('bak');
        $gs_id = $this->input->get('gs_id');
        $page = $this->input->get('page');
        $type = $this->input->get('type') ?? "normal";

        $config = $this->load->get_var('config');
        $default = $this->load->get_var('default');
        $qstr = $this->load->get_var('qstr');
        $w = $this->input->get('w');
        $gs = null;
        if ($w == "") {
            $gs['mb_id']        = 'admin';
            $gs['mb_name']        = $config['company_name'];
            $gs['gcode']        = time();
            $gs['sc_type']        = 0; // 배송비 유형	0:공통설정, 1:무료배송, 2:조건부 무료배송, 3:유료배송
            $gs['sc_method']    = 0; // 배송비 결제	0:선불, 1:착불, 2:사용자선택
            $gs['stock_mod']    = 0;
            $gs['noti_qty']        = 999;
            $gs['simg_type']    = 0;
            $gs['isopen']        = 1;
            $gs['notax']        = 1;
            $gs['ppay_type']    = 0;
            $gs['ppay_rate']    = 0;
            $gs['zone']            = '전국';
        } else if ($w == "u") {
            $gs = $this->global_lib->get_goods($gs_id);
            $type = $gs['type'];
            if (!$gs)
                alert("존재하지 않은 상품 입니다.");

            $readonly = " readonly style='background-color:#ddd;'";

            if (is_null_time($gs['sb_date'])) $gs['sb_date'] = '';
            if (is_null_time($gs['eb_date'])) $gs['eb_date'] = '';
        }

        if (isset($sel_ca1))            $qstr .= "&sel_ca1=$sel_ca1";
        if (isset($sel_ca2))            $qstr .= "&sel_ca2=$sel_ca2";
        if (isset($sel_ca3))            $qstr .= "&sel_ca3=$sel_ca3";
        if (isset($sel_ca4))            $qstr .= "&sel_ca4=$sel_ca4";
        if (isset($sel_ca5))            $qstr .= "&sel_ca5=$sel_ca5";
        if (isset($q_date_field))    $qstr .= "&q_date_field=$q_date_field";
        if (isset($q_brand))            $qstr .= "&q_brand=$q_brand";
        if (isset($q_zone))            $qstr .= "&q_zone=$q_zone";
        if (isset($q_stock_field))    $qstr .= "&q_stock_field=$q_stock_field";
        if (isset($fr_stock))        $qstr .= "&fr_stock=$fr_stock";
        if (isset($to_stock))        $qstr .= "&to_stock=$to_stock";
        if (isset($q_price_field))    $qstr .= "&q_price_field=$q_price_field";
        if (isset($fr_price))        $qstr .= "&fr_price=$fr_price";
        if (isset($to_price))        $qstr .= "&to_price=$to_price";
        if (isset($q_isopen))        $qstr .= "&q_isopen=$q_isopen";
        if (isset($q_option))        $qstr .= "&q_option=$q_option";
        if (isset($q_supply))        $qstr .= "&q_supply=$q_supply";
        if (isset($q_notax))            $qstr .= "&q_notax=$q_notax";

        $item_info = $this->config->item('item_info');
        $jqueryUiDatePick = $this->admin_template->datePicker();

        $frm_submit = '<div class="btn_confirm">
                <input type="submit" value="저장" class="btn_large" accesskey="s">';
        if ($w == "u" && $bak) {
            $frm_submit .= '<a href="' . base_url() . 'admin/goods?code=' . $bak . $qstr . '&page=' . $page . '" class="btn_large bx-white">목록</a>';
            $frm_submit .= '<a href="' . base_url() . 'admin/goods?code=form&type=' . $type . '" class="btn_large bx-red">추가</a>';
        }
        $frm_submit .= '</div>';

        $pg_anchor = '
            <ul class="anchor">
                <li><a href="#anc_sitfrm_cate">카테고리</a></li>
                <li><a href="#anc_sitfrm_ini">기본정보</a></li>
                <li><a href="#anc_sitfrm_option">옵션정보</a></li>
                <li><a href="#anc_sitfrm_cost">가격 및 재고</a></li>
                <li><a href="#anc_sitfrm_sendcost">배송비</a></li>
                <li><a href="#anc_sitfrm_compact">요약정보</a></li>
                <li><a href="#anc_sitfrm_relation">관련상품</a></li>
                <li><a href="#anc_sitfrm_img">상품이미지</a></li>
            </ul>';
        $sel_ca1Tag = $this->funcomplex_lib->get_category_select_1('sel_ca1', '', ' size="10" class="multiple-select"');
        $sel_ca2Tag = $this->funcomplex_lib->get_category_select_2('sel_ca2', '', ' size="10" class="multiple-select"');
        $sel_ca3Tag = $this->funcomplex_lib->get_category_select_3('sel_ca3', '', ' size="10" class="multiple-select"');
        $sel_ca4Tag = $this->funcomplex_lib->get_category_select_4('sel_ca4', '', ' size="10" class="multiple-select"');
        $sel_ca5Tag = $this->funcomplex_lib->get_category_select_5('sel_ca5', '', ' size="10" class="multiple-select"');
        $admCateOpt = "";
        $readonly = "";
        if ($w == "u") {
            if ($ca_id1 = $this->common_lib->adm_category_navi($gs['ca_id']))
                $admCateOpt .= '<option value="' . $gs['ca_id'] . '">' . $ca_id1 . '</option>';
            if ($ca_id2 = $this->common_lib->adm_category_navi($gs['ca_id2']))
                $admCateOpt .= '<option value="' . $gs['ca_id2'] . '">' . $ca_id2 . '</option>';
            if ($ca_id3 = $this->common_lib->adm_category_navi($gs['ca_id3']))
                $admCateOpt .= '<option value="' . $gs['ca_id3'] . '">' . $ca_id3 . '</option>';
        }
        $goodsRegTag = "";
        if ($w == 'u') {
            $goodsRegTag = "
                <div class=\"local_desc02 local_desc\">
                    <p>상품 등록일시 : <b>{$gs['reg_time']}</b>, 최근 수정일시 : <b>{$gs['update_time']}</b></p>
                </div>";
            $readonly = " readonly style='background-color:#ddd;'";
        }
        $preview = "";
        if ($w == "u") {
            $preview = "
                <a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\" class=\"btn_small\">미리보기</a>";
        }
        $searchHelp = help('단어와 단어 사이는 콤마 ( , ) 로 구분하여 여러개를 입력할 수 있습니다. 예시) 빨강, 노랑, 파랑');

        $sql = "select * from shop_brand where br_user_yes = 0 or (br_user_yes = 1 and mb_id = '{$gs['mb_id']}') order by br_name asc ";

        $res = $this->query_model->returnArr($sql);
        $brandStr = "";
        foreach ($res as $key => $row) {
            $row['br_id'] = isset($row['br_id']) ? $row['br_id'] : "";
            $row['br_name'] = isset($row['br_id']) ? $row['br_name'] : "";
            $gs['brand_uid'] = isset($gs['brand_uid']) ? $gs['brand_uid'] : "";
            $brandStr .= option_selected($row['br_id'], $gs['brand_uid'], $row['br_name']);
        }
        $notax = array('1' => '', '0' => '');
        $notax['1'] = radio_checked('notax', $gs['notax'], '1', '과세');
        $notax['0'] = radio_checked('notax', $gs['notax'], '0', '면세');

        $isopenArr = array('exhibit' => '', 'solidout' => '', 'extinct' => '', 'stop' => '', 'end' => '');
        $isopenArr['exhibit'] = radio_checked('isopen', $gs['isopen'], '1', '진열');
        $isopenArr['solidout'] = radio_checked('isopen', $gs['isopen'], '2', '품절');
        $isopenArr['extinct'] = radio_checked('isopen', $gs['isopen'], '3', '단종');
        $isopenArr['stop'] = radio_checked('isopen', $gs['isopen'], '4', '중지');
        $isopenArr['end'] = radio_checked('isopen', $gs['isopen'], '5', '판매종료');

        $naverHelp = help("네이버쇼핑에 입점한 경우 네이버쇼핑 상품ID를 입력하시면 네이버페이와 연동됩니다.<br>일부 쇼핑몰의 경우 네이버쇼핑 상품ID 대신 쇼핑몰 상품ID를 입력해야 하는 경우가 있습니다.<br>네이버페이 연동과정에서 이 부분에 대한 안내가 이뤄지니 안내받은 대로 값을 입력하시면 됩니다.");
        $gs['opt_subject'] = isset($gs['opt_subject']) ? $gs['opt_subject'] : "";
        $opt_subject = explode(',', $gs['opt_subject']);
        $opt_subject[1] = isset($opt_subject[1]) ? $opt_subject[1] : "";
        $opt_subject[2] = isset($opt_subject[2]) ? $opt_subject[2] : "";

        $goods_optionObj = $this->admin_template->goods_options($gs);
        $goods_option = $goods_optionObj['tag'];
        $po_run = $goods_optionObj['po_run'];
        $optJs = "";
        if (isset($gs['index_no']) && $po_run) {
            $optJs = "
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
        $gs['spl_subject'] = isset($gs['spl_subject']) ? $gs['spl_subject'] : "";
        $spl_subject = explode(',', $gs['spl_subject']);
        $spl_count = count($spl_subject);
        $i = 0;
        $optTable = "";
        do {
            $seq = $i + 1;
            $optTable .= "
                <tr>
                    <th scope=\"row\">추가{$seq}</th>
                    <td><input type=\"text\" name=\"spl_subject[]\" value=\"{$spl_subject[$i]}\" id=\"spl_subject_{$seq}\" class=\"frm_input wfull\"></td>
                    <th scope=\"row\">추가{$seq} 항목</th>
                    <td><input type=\"text\" name=\"spl[]\" id=\"spl_item_{$seq}\" value=\"\" class=\"frm_input wfull\"></td>
                    <td class=\"tac\">";
            if ($i > 0)
                $optTable .= '<button type="button" id="del_supply_row" class="btn_ssmall bx-white">삭제</button>';
            $optTable .= "
                    </td>
                </tr>";
            $i++;
        } while ($i < $spl_count);
        $goods_splObj = $this->admin_template->goods_spl($gs);
        $goods_spl = $goods_splObj['tag'];
        $ps_run = $goods_splObj['ps_run'];
        $addOptJs = "";
        if (isset($gs['index_no']) && $ps_run) {
            $addOptJs = "
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
        $stockMod = array('', '');
        $stockMod[0] = get_checked('0', $gs['stock_mod']);
        $stockMod[1] = get_checked('1', $gs['stock_mod']);
        $gs['buy_level'] = isset($gs['buy_level']) ? $gs['buy_level'] : "";
        $gs['buy_only'] = isset($gs['buy_only']) ? $gs['buy_only'] : "";
        $gs['ppay_type'] = isset($gs['ppay_type']) ? $gs['ppay_type'] : "";
        $gs['ppay_rate'] = isset($gs['ppay_rate']) ? $gs['ppay_rate'] : "";
        $gs['sc_each_use'] = isset($gs['sc_each_use']) ? $gs['sc_each_use'] : "";
        $buyLevel = $this->common_lib->get_goods_level_select('buy_level', $gs['buy_level']);
        $butOnly = get_checked('1', $gs['buy_only']);
        $ppay_typeArr = array('', '');
        $ppay_typeArr[0] = option_selected('0', $gs['ppay_type'], '공통설정');
        $ppay_typeArr[1] = option_selected('1', $gs['ppay_rate'], '설정금액(원)으로 적립');
        $ppay_rateArr = array('', '');
        $ppay_rateArr = option_selected('0', $gs['ppay_rate'], '설정비율(%)로 적립');
        $ppay_rateArr = option_selected('0', $gs['ppay_rate'], '설정비율(%)로 적립');

        $gsScType = array('', '', '', '');
        $gsScType[0] = option_selected('0', $gs['sc_type'], '공통설정');
        $gsScType[1] = option_selected('1', $gs['sc_type'], '무료배송');
        $gsScType[2] = option_selected('2', $gs['sc_type'], '조건부무료배송');
        $gsScType[3] = option_selected('3', $gs['sc_type'], '유료배송');

        $sc_method = array('', '', '');
        $sc_method[0] = option_selected('0', $gs['sc_method'], '선불');
        $sc_method[1] = option_selected('1', $gs['sc_method'], '착불');
        $sc_method[2] = option_selected('2', $gs['sc_method'], '사용자선택');

        $zoneArr = array('', '', '', '', '', '', '', '', '');
        $zoneArr[0] = option_selected('전국', $gs['zone'], '전국');
        $zoneArr[1] = option_selected('강원도', $gs['zone'], '강원도');
        $zoneArr[2] = option_selected('경기도', $gs['zone'], '경기도');
        $zoneArr[3] = option_selected('경상도', $gs['zone'], '경상도');
        $zoneArr[4] = option_selected('서울/경기도', $gs['zone'], '서울/경기도');
        $zoneArr[5] = option_selected('서울특별시', $gs['zone'], '서울특별시');
        $zoneArr[6] = option_selected('전라도', $gs['zone'], '전라도');
        $zoneArr[7] = option_selected('제주도', $gs['zone'], '제주도');
        $zoneArr[8] = option_selected('충청도', $gs['zone'], '충청도');
        $sc_each_use = get_checked('1', $gs['sc_each_use']);
        $couponOpt = "";
        if (!isset($gs['info_gubun'])) $gs['info_gubun'] = 'wear';
        foreach ($item_info as $key => $value) {
            $opt_value = $key;
            $opt_text  = $value['title'];
            $couponOpt .= '<option value="' . $opt_value . '" ' . get_selected($opt_value, $gs['info_gubun']) . '>' . $opt_text . '</option>';
        }
        $goods_info = $this->admin_template->goods_info($gs);

        $get_goods_sca_select = $this->common_lib->get_goods_sca_select('sch_relation');

        $str = array();
        $sql = "select b.index_no, b.gname, b.simg1 from shop_goods_relation a join shop_goods b ON (a.gs_id2=b.index_no) where a.gs_id = '{$gs_id}' order by ir_no asc ";
        $result = $this->query_model->returnArr($sql);
        $g = 0;
        $relatedTag = "";
        foreach ($result as $key => $row) {
            $gname = $this->funcomplex_lib->get_it_image($row['index_no'], $row['simg1'], 50, 50) . ' ' . $row['gname'];

            if ($g == 0) {
                $relatedTag .= '<ul>';
            }
            $relatedTag .= "
                <li>
                    <input type=\"hidden\" name=\"re_gs_id[]\" value=\"{$row['index_no']}\">
                    <div class=\"list_item\">{$gname}</div>
                    <div class=\"list_item_btn\"><button type=\"button\" class=\"del_item btn_small\">삭제</button></div>
                </li>";
            $str[] = $row['index_no'];
            $g++;
        }
        $str = implode(",", $str);
        if ($g > 0)
            $relatedTag .= '</ul>';
        else
            $relatedTag .= '<p>선택된 상품이 없습니다.</p>';

        $simgType = array('', '');
        $simgType[0] = get_checked('0', $gs['simg_type']);
        $simgType[1] = get_checked('1', $gs['simg_type']);

        $item_img_fld = "";
        for ($i = 1; $i <= 6; $i++) {
            if ($i == 1) {
                $item_wpx = $default['de_item_small_wpx'];
                $item_hpx = $default['de_item_small_hpx'];
            } else {
                $item_wpx = $default['de_item_medium_wpx'];
                $item_hpx = $default['de_item_medium_hpx'];
            }

            $image_str = '';
            if (in_array($i, array(1, 2))) {
                $image_str = ' <strong class="fc_red">[필수]</strong>';
            }
            $gs['simg' . $i] = isset($gs['simg' . $i]) ? $gs['simg' . $i] : "";
            $item_img_fld .= "
                <tr class=\"item_img_fld\">
                    <th scope=\"row\">이미지{$i} <span class=\"fc_197\">({$item_wpx} * {$item_hpx})</span>{$image_str}</th>
                    <td>
                        <div class=\"item_file_fld\">
                            <input type=\"file\" name=\"simg{$i}\">
                            " . get_look_ahead($gs['simg' . $i], "simg{$i}_del") . "
                        </div>
                        <div class=\"item_url_fld\">
                            <input type=\"text\" name=\"simg{$i}\" value=\"{$gs['simg' .$i]}\" class=\"frm_input\" size=\"80\" placeholder=\"http://\">
                        </div>
                    </td>
                </tr>";
        }
        $gs['memo'] = isset($gs['memo']) ? $gs['memo'] : "";
        $gs['ppay_dan'] = isset($gs['ppay_dan']) ? $gs['ppay_dan'] : "";
        $gs['gname'] = isset($gs['gname']) ? $gs['gname'] : "";
        $gs['explan'] = isset($gs['explan']) ? $gs['explan'] : "";
        $gs['keywords'] = isset($gs['keywords']) ? $gs['keywords'] : "";
        $gs['model'] = isset($gs['model']) ? $gs['model'] : "";
        $gs['origin'] = isset($gs['origin']) ? $gs['origin'] : "";
        $gs['maker'] = isset($gs['maker']) ? $gs['maker'] : "";
        $gs['ec_mall_pid'] = isset($gs['ec_mall_pid']) ? $gs['ec_mall_pid'] : "";
        $gs['normal_price'] = isset($gs['normal_price']) ? $gs['normal_price'] : 0;
        $gs['supply_price'] = isset($gs['supply_price']) ? $gs['supply_price'] : 0;
        $gs['goods_price'] = isset($gs['goods_price']) ? $gs['goods_price'] : 0;
        $gs['calculate_price'] = isset($gs['calculate_price']) ? $gs['calculate_price'] : 0;
        $gs['gpoint'] = isset($gs['gpoint']) ? $gs['gpoint'] : 0;
        $gs['price_msg'] = isset($gs['price_msg']) ? $gs['price_msg'] : '';
        $gs['stock_qty'] = isset($gs['original_stock_qty']) ? $gs['original_stock_qty'] : 0;
        // $gs['safety_stock_qty']=isset($gs['safety_stock_qty'])?$gs['safety_stock_qty']:0;
        $gs['goods_total_price'] = isset($gs['goods_total_price']) ? $gs['goods_total_price'] : 0;
        $gs['odr_min'] = isset($gs['odr_min']) ? $gs['odr_min'] : 1;
        $gs['odr_max'] = isset($gs['odr_max']) ? $gs['odr_max'] : 1;
        $gs['sb_date'] = isset($gs['sb_date']) ? $gs['sb_date'] : '';
        $gs['eb_date'] = isset($gs['eb_date']) ? $gs['eb_date'] : '';
        $gs['admin_memo'] = isset($gs['admin_memo']) ? $gs['admin_memo'] : '';
        $gs['zone_msg'] = isset($gs['zone_msg']) ? $gs['zone_msg'] : '';
        $gs['delivery_period'] = isset($gs['delivery_period']) ? $gs['delivery_period'] : '';
        $gs['sc_amt'] = isset($gs['sc_amt']) ? $gs['sc_amt'] : 0;
        $gs['sc_minimum'] = isset($gs['sc_minimum']) ? $gs['sc_minimum'] : 0;
        $gs['index_no'] = isset($gs['index_no']) ? $gs['index_no'] : '';
        $gs['mb_id'] = isset($gs['mb_id']) ? $gs['mb_id'] : 0;
        $gs['is_point'] = isset($gs['is_point']) ? $gs['is_point'] : 1;

        $memoEdit = $this->global_lib->editor_html('memo', $this->funcomplex_lib->get_text(stripcslashes($gs['memo']), 0));
        $memoEditJs = $this->global_lib->get_editor_js('memo');
        $ppay_danJs = "";
        if ($gs['ppay_dan']) {
            $ppay_danJs = "chk_ppay_dan(\"{$gs['ppay_dan']}\",\"{$gs['index_no']}\");";
        }
        $data = array(
            'w' => $w,
            'gs_id' => $gs_id,
            'qstr' => $qstr,
            'page' => $page,
            'bak' => $bak,
            'pg_anchor' => $pg_anchor,
            'frm_submit' => $frm_submit,
            'sel_ca1Tag' => $sel_ca1Tag,
            'sel_ca2Tag' => $sel_ca2Tag,
            'sel_ca3Tag' => $sel_ca3Tag,
            'sel_ca4Tag' => $sel_ca4Tag,
            'sel_ca5Tag' => $sel_ca5Tag,
            'admCateOpt' => $admCateOpt,
            'goodsRegTag' => $goodsRegTag,
            'gs' => $gs,
            'preview' => $preview,
            'searchHelp' => $searchHelp,
            'readonly' => $readonly,
            'brandStr' => $brandStr,
            'notax' => $notax,
            'isopenArr' => $isopenArr,
            'naverHelp' => $naverHelp,
            'opt_subject' => $opt_subject,
            'goods_option' => $goods_option,
            'optJs' => $optJs,
            'optTable' => $optTable,
            'goods_spl' => $goods_spl,
            'addOptJs' => $addOptJs,
            'stockMod' => $stockMod,
            'buyLevel' => $buyLevel,
            'butOnly' => $butOnly,
            'ppay_typeArr' => $ppay_typeArr,
            'ppay_typeArr' => $ppay_typeArr,
            'gsScType' => $gsScType,
            'sc_method' => $sc_method,
            'sc_each_use' => $sc_each_use,
            'zoneArr' => $zoneArr,
            'couponOpt' => $couponOpt,
            'goods_info' => $goods_info,
            'get_goods_sca_select' => $get_goods_sca_select,
            'relatedTag' => $relatedTag,
            'str' => $str,
            'simgType' => $simgType,
            'item_img_fld' => $item_img_fld,
            'memoEdit' => $memoEdit,
            'memoEditJs' => $memoEditJs,
            'ppay_danJs' => $ppay_danJs,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'type' => $type
        );
        $this->load->view('admin/goods/form', $data);
    }
    private function goods_supply($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $q_date_field = $this->input->get('q_date_field');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $q_stock_field = $this->input->get('q_stock_field');
        $fr_stock = $this->input->get('fr_stock');
        $to_stock = $this->input->get('to_stock');
        $q_price_field = $this->input->get('q_price_field');
        $fr_price = $this->input->get('fr_price');
        $to_price = $this->input->get('to_price');
        $q_isopen = $this->input->get('q_isopen');
        $q_option = $this->input->get('q_option');
        $q_supply = $this->input->get('q_supply');
        $q_notax = $this->input->get('q_notax');
        $stx = $this->input->get('stx');
        $sfl = $this->input->get('sfl');
        $sca = $this->input->get('sca');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $orderby = $this->input->get('orderby');
        $page = $this->input->get('page');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);


        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        if ($sel_ca1) $sca = $sel_ca1;
        if ($sel_ca2) $sca = $sel_ca2;
        if ($sel_ca3) $sca = $sel_ca3;
        if ($sel_ca4) $sca = $sel_ca4;
        if ($sel_ca5) $sca = $sel_ca5;

        if (isset($sel_ca1))            $qstr .= "&sel_ca1=$sel_ca1";
        if (isset($sel_ca2))            $qstr .= "&sel_ca2=$sel_ca2";
        if (isset($sel_ca3))            $qstr .= "&sel_ca3=$sel_ca3";
        if (isset($sel_ca4))            $qstr .= "&sel_ca4=$sel_ca4";
        if (isset($sel_ca5))            $qstr .= "&sel_ca5=$sel_ca5";
        if (isset($q_date_field))    $qstr .= "&q_date_field=$q_date_field";
        if (isset($q_brand))            $qstr .= "&q_brand=$q_brand";
        if (isset($q_zone))            $qstr .= "&q_zone=$q_zone";
        if (isset($q_stock_field))    $qstr .= "&q_stock_field=$q_stock_field";
        if (isset($fr_stock))        $qstr .= "&fr_stock=$fr_stock";
        if (isset($to_stock))        $qstr .= "&to_stock=$to_stock";
        if (isset($q_price_field))    $qstr .= "&q_price_field=$q_price_field";
        if (isset($fr_price))        $qstr .= "&fr_price=$fr_price";
        if (isset($to_price))        $qstr .= "&to_price=$to_price";
        if (isset($q_isopen))        $qstr .= "&q_isopen=$q_isopen";
        if (isset($q_option))        $qstr .= "&q_option=$q_option";
        if (isset($q_supply))        $qstr .= "&q_supply=$q_supply";
        if (isset($q_notax))            $qstr .= "&q_notax=$q_notax";

        $jqueryUiDatePick = $this->admin_template->datePicker();

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";
        $sql_common = " from shop_goods ";
        $sql_search = " where shop_state = '1' ";

        if ($sca) {
            $sql_search .= " and (ca_id like '$sca%' or ca_id2 like '$sca%' or ca_id3 like '$sca%') ";
        }
        // 검색어
        if ($stx) {
            switch ($sfl) {
                case "gname":
                case "explan":
                case "maker":
                case "origin":
                case "model":
                    $sql_search .= " and $sfl like '%$stx%' ";
                    break;
                default:
                    $sql_search .= " and $sfl like '$stx%' ";
                    break;
            }
        }
        // 기간검색
        if ($fr_date && $to_date)
            $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and $q_date_field between '$to_date 00:00:00' and '$to_date 23:59:59' ";
        // 브랜드
        if (isset($q_brand) && $q_brand)
            $sql_search .= " and brand_uid = '$q_brand' ";
        // 배송가능 지역
        if (isset($q_zone) && $q_zone)
            $sql_search .= " and zone = '$q_zone' ";
        // 상품재고
        if ($fr_stock && $to_stock)
            $sql_search .= " and $q_stock_field between '$fr_stock' and '$to_stock' ";

        // 상품가격
        if ($fr_price && $to_price)
            $sql_search .= " and $q_price_field between '$fr_price' and '$to_price' ";
        // 판매여부
        if (isset($q_isopen) && is_numeric($q_isopen))
            $sql_search .= " and isopen='$q_isopen' ";

        // 과세유형
        if (isset($q_notax) && is_numeric($q_notax))
            $sql_search .= " and notax = '$q_notax' ";

        // 상품 필수옵션
        if (isset($q_option) && is_numeric($q_option)) {
            if ($q_option)
                $sql_search .= " and opt_subject <> '' ";
            else
                $sql_search .= " and opt_subject = '' ";
        }
        // 상품 추가옵션
        if (isset($q_supply) && is_numeric($q_supply)) {
            if ($q_supply)
                $sql_search .= " and spl_subject <> '' ";
            else
                $sql_search .= " and spl_subject = '' ";
        }
        if (!$orderby) {
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
        if (isset($_SESSION['ss_page_rows']))
            $page_rows = $_SESSION['ss_page_rows'];
        else
            $page_rows = 30;

        $rows = $page_rows;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);
        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택승인\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택보류\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "AdminProcess/goods_supply_excel?$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장</a>
            <a href=\"" . base_url() . "admin/goods?code=form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 상품등록</a>";
        $sflArr = array('', '', '', '', '', '', '');
        $sflArr[0] = option_selected('gname', $sfl, '상품명');
        $sflArr[1] = option_selected('gcode', $sfl, '상품코드');
        $sflArr[2] = option_selected('mb_id', $sfl, '업체코드');
        $sflArr[3] = option_selected('maker', $sfl, '제조사');
        $sflArr[4] = option_selected('origin', $sfl, '원산지');
        $sflArr[5] = option_selected('model', $sfl, '모델명');
        $sflArr[6] = option_selected('explan', $sfl, '짧은설명');


        $sel_ca = array('', '', '', '', '', '');
        $sel_ca[1] = $this->funcomplex_lib->get_category_select_1('sel_ca1', $sca);
        $sel_ca[2] = $this->funcomplex_lib->get_category_select_2('sel_ca2', $sca);
        $sel_ca[3] = $this->funcomplex_lib->get_category_select_3('sel_ca3', $sca);
        $sel_ca[4] = $this->funcomplex_lib->get_category_select_4('sel_ca4', $sca);
        $sel_ca[5] = $this->funcomplex_lib->get_category_select_5('sel_ca5', $sca);

        $updateTime = option_selected('update_time', $q_date_field, "최근수정일");
        $regTime = option_selected('reg_time', $q_date_field, "최초등록일");
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $sql = "select * from shop_brand where br_user_yes='0' order by br_name asc ";
        $res = $this->query_model->returnArr($sql);
        $q_zoneStr = "";
        $q_zoneStr .= option_selected('', $q_brand, '전체');
        foreach ($res as $key => $row) {
            $q_zoneStr .= option_selected($row['br_id'], $q_brand, $row['br_name']);
        }
        $q_zoneOpt = array('', '', '', '', '', '', '', '', '', '');
        $q_zoneOpt[0] = option_selected('',  $q_zone, '전체');
        $q_zoneOpt[1] = option_selected('전국', $q_zone, '전국');
        $q_zoneOpt[2] = option_selected('강원도', $q_zone, '강원도');
        $q_zoneOpt[3] = option_selected('경기도', $q_zone, '경기도');
        $q_zoneOpt[4] = option_selected('경상도', $q_zone, '경상도');
        $q_zoneOpt[5] = option_selected('서울/경기도', $q_zone, '서울/경기도');
        $q_zoneOpt[6] = option_selected('서울특별시', $q_zone, '서울특별시');
        $q_zoneOpt[7] = option_selected('전라도', $q_zone, '전라도');
        $q_zoneOpt[8] = option_selected('제주도', $q_zone, '제주도');
        $q_zoneOpt[9] = option_selected('충청도', $q_zone, '충청도');

        $stock = array('stock' => '', 'noti' => '');
        $stock['stock'] = option_selected('stock_qty', $q_stock_field, "재고수량");
        $stock['noti'] = option_selected('noti_qty', $q_stock_field, "통보수량");

        $priceField = array('', '', '', '');

        $priceField[0] = option_selected('goods_price', $q_price_field, "판매가격");
        $priceField[1] = option_selected('supply_price', $q_price_field, "공급가격");
        $priceField[2] = option_selected('normal_price', $q_price_field, "시중가격");
        $priceField[3] = option_selected('gpoint', $q_price_field, "포인트");

        $q_isopenArr = array('', '', '', '', '');
        $q_isopenArr[0] = radio_checked('q_isopen', $q_isopen,  '', '전체');
        $q_isopenArr[1] = radio_checked('q_isopen', $q_isopen, '1', '진열');
        $q_isopenArr[2] = radio_checked('q_isopen', $q_isopen, '2', '품절');
        $q_isopenArr[3] = radio_checked('q_isopen', $q_isopen, '3', '단종');
        $q_isopenArr[4] = radio_checked('q_isopen', $q_isopen, '4', '중지');

        $q_optionArr = array('', '', '');
        $q_optionArr[0] = radio_checked('q_option', $q_option,  '', '전체');
        $q_optionArr[1] = radio_checked('q_option', $q_option, '1', '사용');
        $q_optionArr[2] = radio_checked('q_option', $q_option, '0', '미사용');

        $q_notaxArr = array('', '', '');
        $q_notaxArr[0] = radio_checked('q_notax', $q_notax,  '', '전체');
        $q_notaxArr[1] = radio_checked('q_notax', $q_notax, '1', '과세');
        $q_notaxArr[2] = radio_checked('q_notax', $q_notax, '0', '비과세');

        $q_supplyArr = array('', '', '');
        $q_supplyArr[0] = radio_checked('q_supply', $q_supply,  '', '전체');
        $q_supplyArr[1] = radio_checked('q_supply', $q_supply, '1', '사용');
        $q_supplyArr[2] = radio_checked('q_supply', $q_supply, '0', '미사용');

        $optSel = array('30' => '', '50' => '', '100' => '', '150' => '');
        $optSel['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $optSel['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $optSel['100'] = option_selected('100', $page_rows, '100줄 정렬');
        $optSel['150'] = option_selected('150', $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $update_timeTag = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stock_qtyTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normal_priceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supply_priceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $goods_priceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);

        $i = 0;
        $supplyTable = "";
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);

            $type_txt = $row['type'] == "normal" ? "열린장터 상품" : "함께장터 상품";

            $supplyTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td rowspan=\"2\">" . $type_txt . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>{$gw_isopen[$row['isopen']]}</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['supply_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $supplyTable .= '<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'btn_frmline' => $btn_frmline,
            'sflArr' => $sflArr,
            'stx' => $stx,
            'sel_ca' => $sel_ca,
            'updateTime' => $updateTime,
            'regTime' => $regTime,
            'get_search_date' => $get_search_date,
            'q_zoneStr' => $q_zoneStr,
            'q_zoneOpt' => $q_zoneOpt,
            'stock' => $stock,
            'fr_stock' => $fr_stock,
            'to_stock' => $to_stock,
            'priceField' => $priceField,
            'fr_price' => $fr_price,
            'to_price' => $to_price,
            'q_isopenArr' => $q_isopenArr,
            'q_optionArr' => $q_optionArr,
            'q_notaxArr' => $q_notaxArr,
            'q_supplyArr' => $q_supplyArr,
            'q1' => $q1,
            'page' => $page,
            'optSel' => $optSel,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'mb_idTag' => $mb_idTag,
            'update_timeTag' => $update_timeTag,
            'stock_qtyTag' => $stock_qtyTag,
            'normal_priceTag' => $normal_priceTag,
            'supply_priceTag' => $supply_priceTag,
            'goods_priceTag' => $goods_priceTag,
            'supplyTable' => $supplyTable,
            'pagination' => $pagination,
            'total_count' => $total_count,
            'requestUrl' => $requestUrl
        );
        $this->load->view('admin/goods/supply', $data);
    }
    private function goods_supply2($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $jqueryUiDatePick = $this->admin_template->datePicker();

        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $q_date_field = $this->input->get('q_date_field');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $q_stock_field = $this->input->get('q_stock_field');
        $fr_stock = $this->input->get('fr_stock');
        $to_stock = $this->input->get('to_stock');
        $q_price_field = $this->input->get('q_price_field');
        $fr_price = $this->input->get('fr_price');
        $to_price = $this->input->get('to_price');
        $q_isopen = $this->input->get('q_isopen');
        $q_option = $this->input->get('q_option');
        $q_supply = $this->input->get('q_supply');
        $q_notax = $this->input->get('q_notax');
        $stx = $this->input->get('stx');
        $sfl = $this->input->get('sfl');
        $sca = $this->input->get('sca');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $orderby = $this->input->get('orderby');
        $page = $this->input->get('page');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_goods ";
        $sql_search = " where shop_state = '2' ";

        if ($sca) {
            $sql_search .= " and (ca_id like '$sca%' or ca_id2 like '$sca%' or ca_id3 like '$sca%') ";
        }

        // 검색어
        if ($stx) {
            switch ($sfl) {
                case "gname":
                case "explan":
                case "maker":
                case "origin":
                case "model":
                    $sql_search .= " and $sfl like '%$stx%' ";
                    break;
                default:
                    $sql_search .= " and $sfl like '$stx%' ";
                    break;
            }
        }

        // 기간검색
        if ($fr_date && $to_date)
            $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and $q_date_field between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        // 브랜드
        if (isset($q_brand) && $q_brand)
            $sql_search .= " and brand_uid = '$q_brand' ";

        // 배송가능 지역
        if (isset($q_zone) && $q_zone)
            $sql_search .= " and zone = '$q_zone' ";

        // 상품재고
        if ($fr_stock && $to_stock)
            $sql_search .= " and $q_stock_field between '$fr_stock' and '$to_stock' ";

        // 상품가격
        if ($fr_price && $to_price)
            $sql_search .= " and $q_price_field between '$fr_price' and '$to_price' ";

        // 판매여부
        if (isset($q_isopen) && is_numeric($q_isopen))
            $sql_search .= " and isopen='$q_isopen' ";

        // 과세유형
        if (isset($q_notax) && is_numeric($q_notax))
            $sql_search .= " and notax = '$q_notax' ";

        // 상품 필수옵션
        if (isset($q_option) && is_numeric($q_option)) {
            if ($q_option)
                $sql_search .= " and opt_subject <> '' ";
            else
                $sql_search .= " and opt_subject = '' ";
        }

        // 상품 추가옵션
        if (isset($q_supply) && is_numeric($q_supply)) {
            if ($q_supply)
                $sql_search .= " and spl_subject <> '' ";
            else
                $sql_search .= " and spl_subject = '' ";
        }

        if (!$orderby) {
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

        if (isset($_SESSION['ss_page_rows']))
            $page_rows = $_SESSION['ss_page_rows'];
        else
            $page_rows = 30;

        $rows = $page_rows;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택승인\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택대기\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "AdminProcess/goods_supply2_excel?$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장</a>
            <a href=\"" . base_url() . "admin/goods?code=form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 상품등록</a>";

        $sflArr = array('', '', '', '', '', '', '');
        $sflArr[0] = option_selected('gname', $sfl, '상품명');
        $sflArr[1] = option_selected('gcode', $sfl, '상품코드');
        $sflArr[2] = option_selected('mb_id', $sfl, '업체코드');
        $sflArr[3] = option_selected('maker', $sfl, '제조사');
        $sflArr[4] = option_selected('origin', $sfl, '원산지');
        $sflArr[5] = option_selected('model', $sfl, '모델명');
        $sflArr[6] = option_selected('explan', $sfl, '짧은설명');

        $sel_ca = array('', '', '', '', '', '');
        $sel_ca[1] = $this->funcomplex_lib->get_category_select_1('sel_ca1', $sca);
        $sel_ca[2] = $this->funcomplex_lib->get_category_select_2('sel_ca2', $sca);
        $sel_ca[3] = $this->funcomplex_lib->get_category_select_3('sel_ca3', $sca);
        $sel_ca[4] = $this->funcomplex_lib->get_category_select_4('sel_ca4', $sca);
        $sel_ca[5] = $this->funcomplex_lib->get_category_select_5('sel_ca5', $sca);
        $update_timeTag = option_selected('update_time', $q_date_field, "최근수정일");
        $reg_timeTag = option_selected('reg_time', $q_date_field, "최초등록일");
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $qZoneArr = array('', '', '', '', '', '', '', '', '', '');
        $qZoneArr[0] = option_selected('',  $q_zone, '전체');
        $qZoneArr[1] = option_selected('전국', $q_zone, '전국');
        $qZoneArr[2] = option_selected('강원도', $q_zone, '강원도');
        $qZoneArr[3] = option_selected('경기도', $q_zone, '경기도');
        $qZoneArr[4] = option_selected('경상도', $q_zone, '경상도');
        $qZoneArr[5] = option_selected('서울/경기도', $q_zone, '서울/경기도');
        $qZoneArr[6] = option_selected('서울특별시', $q_zone, '서울특별시');
        $qZoneArr[7] = option_selected('전라도', $q_zone, '전라도');
        $qZoneArr[8] = option_selected('제주도', $q_zone, '제주도');
        $qZoneArr[9] = option_selected('충청도', $q_zone, '충청도');

        $stock = array('stock' => '', 'noti' => '');
        $stock['stock'] = option_selected('stock_qty', $q_stock_field, "재고수량");
        $stock['stock'] = option_selected('noti_qty', $q_stock_field, "통보수량");

        $price_fieldArr = array('', '', '', '');
        $price_fieldArr[0] = option_selected('goods_price', $q_price_field, "판매가격");
        $price_fieldArr[1] = option_selected('supply_price', $q_price_field, "공급가격");
        $price_fieldArr[2] = option_selected('normal_price', $q_price_field, "시중가격");
        $price_fieldArr[3] = option_selected('gpoint', $q_price_field, "포인트");

        $q_isopenArr = array('', '', '', '', '');
        $q_isopenArr[0] = radio_checked('q_isopen', $q_isopen,  '', '전체');
        $q_isopenArr[1] = radio_checked('q_isopen', $q_isopen, '1', '진열');
        $q_isopenArr[2] = radio_checked('q_isopen', $q_isopen, '2', '품절');
        $q_isopenArr[3] = radio_checked('q_isopen', $q_isopen, '3', '단종');
        $q_isopenArr[4] = radio_checked('q_isopen', $q_isopen, '4', '중지');

        $q_optionArr = array('', '', '');
        $q_optionArr[0] = radio_checked('q_option', $q_option,  '', '전체');
        $q_optionArr[1] = radio_checked('q_option', $q_option, '1', '사용');
        $q_optionArr[2] = radio_checked('q_option', $q_option, '0', '미사용');

        $q_notaxArr = array('', '', '');
        $q_notaxArr[0] = radio_checked('q_notax', $q_notax,  '', '전체');
        $q_notaxArr[1] = radio_checked('q_notax', $q_notax, '1', '과세');
        $q_notaxArr[2] = radio_checked('q_notax', $q_notax, '0', '비과세');

        $q_supplyArr = array('', '', '');
        $q_supplyArr[0] = radio_checked('q_supply', $q_supply,  '', '전체');
        $q_supplyArr[1] = radio_checked('q_supply', $q_supply, '1', '사용');
        $q_supplyArr[2] = radio_checked('q_supply', $q_supply, '0', '미사용');

        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100', $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150', $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $update_timeTagLink = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stock_qtyTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normal_priceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supply_priceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $goods_priceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);


        $supply2Table = "";
        $i = 0;
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);

            $type_txt = $row['type'] == "normal" ? "열린장터 상품" : "함께장터 상품";

            $supply2Table .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shp[/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td rowspan=\"2\">" . $type_txt . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>{$gw_isopen[$row['isopen']]}</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['supply_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $supply2Table .= '<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';


        $q_brandStr = "";
        $sql = "select * from shop_brand where br_user_yes='0' order by br_name asc ";
        $res = $this->query_model->returnArr($sql);
        $q_brandStr .= option_selected('', $q_brand, '전체');
        foreach ($res as $key => $row) {
            $q_brandStr .= option_selected($row['br_id'], $q_brand, $row['br_name']);
        }

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'sflArr' => $sflArr,
            'stx' => $stx,
            'sel_ca' => $sel_ca,
            'update_timeTag' => $update_timeTag,
            'reg_timeTag' => $reg_timeTag,
            'get_search_date' => $get_search_date,
            'qZoneArr' => $qZoneArr,
            'stock' => $stock,
            'price_fieldArr' => $price_fieldArr,
            'fr_price' => $fr_price,
            'to_price' => $to_price,
            'q_isopenArr' => $q_isopenArr,
            'q_optionArr' => $q_optionArr,
            'q_notaxArr' => $q_notaxArr,
            'q_supplyArr' => $q_supplyArr,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'q_brandStr' => $q_brandStr,
            'mb_idTag' => $mb_idTag,
            'update_timeTagLink' => $update_timeTagLink,
            'stock_qtyTag' => $stock_qtyTag,
            'normal_priceTag' => $normal_priceTag,
            'supply_priceTag' => $supply_priceTag,
            'goods_priceTag' => $goods_priceTag,
            'supply2Table' => $supply2Table,
            'pagination' => $pagination,
            'fr_stock' => $fr_stock,
            'to_stock' => $to_stock,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'requestUrl' => $requestUrl
        );
        $this->load->view('admin/goods/supply2', $data);
    }
    private function goods_userlist($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $jqueryUiDatePick = $this->admin_template->datePicker();

        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $q_date_field = $this->input->get('q_date_field');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $q_stock_field = $this->input->get('q_stock_field');
        $fr_stock = $this->input->get('fr_stock');
        $to_stock = $this->input->get('to_stock');
        $q_price_field = $this->input->get('q_price_field');
        $fr_price = $this->input->get('fr_price');
        $to_price = $this->input->get('to_price');
        $q_isopen = $this->input->get('q_isopen');
        $q_option = $this->input->get('q_option');
        $q_supply = $this->input->get('q_supply');
        $q_notax = $this->input->get('q_notax');
        $stx = $this->input->get('stx');
        $sfl = $this->input->get('sfl');
        $sca = $this->input->get('sca');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $orderby = $this->input->get('orderby');
        $page = $this->input->get('page');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_goods ";
        $sql_search = " where left(mb_id,3)='AP-' and shop_state = '2' ";

        if ($sca) {
            $sql_search .= " and (ca_id like '$sca%' or ca_id2 like '$sca%' or ca_id3 like '$sca%') ";
        }

        // 검색어
        if ($stx) {
            switch ($sfl) {
                case "gname":
                case "explan":
                case "maker":
                case "origin":
                case "model":
                    $sql_search .= " and $sfl like '%$stx%' ";
                    break;
                default:
                    $sql_search .= " and $sfl like '$stx%' ";
                    break;
            }
        }

        // 기간검색
        if ($fr_date && $to_date)
            $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and $q_date_field between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        // 브랜드
        if (isset($q_brand) && $q_brand)
            $sql_search .= " and brand_uid = '$q_brand' ";

        // 배송가능 지역
        if (isset($q_zone) && $q_zone)
            $sql_search .= " and zone = '$q_zone' ";

        // 상품재고
        if ($fr_stock && $to_stock)
            $sql_search .= " and $q_stock_field between '$fr_stock' and '$to_stock' ";

        // 상품가격
        if ($fr_price && $to_price)
            $sql_search .= " and $q_price_field between '$fr_price' and '$to_price' ";

        // 판매여부
        if (isset($q_isopen) && is_numeric($q_isopen))
            $sql_search .= " and isopen='$q_isopen' ";

        // 과세유형
        if (isset($q_notax) && is_numeric($q_notax))
            $sql_search .= " and notax = '$q_notax' ";

        // 상품 필수옵션
        if (isset($q_option) && is_numeric($q_option)) {
            if ($q_option)
                $sql_search .= " and opt_subject <> '' ";
            else
                $sql_search .= " and opt_subject = '' ";
        }

        // 상품 추가옵션
        if (isset($q_supply) && is_numeric($q_supply)) {
            if ($q_supply)
                $sql_search .= " and spl_subject <> '' ";
            else
                $sql_search .= " and spl_subject = '' ";
        }

        if (!$orderby) {
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

        if (isset($_SESSION['ss_page_rows']))
            $page_rows = $_SESSION['ss_page_rows'];
        else
            $page_rows = 30;

        $rows = $page_rows;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "AdminProcess/goods_userlist_excel?$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장</a>
            <a href=\"" . base_url() . "admin/goods?code=form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 상품등록</a>";
        $sflArr = array('', '', '', '', '', '', '');
        $sflArr[0] = option_selected('gname', $sfl, '상품명');
        $sflArr[1] = option_selected('gcode', $sfl, '상품코드');
        $sflArr[2] = option_selected('mb_id', $sfl, '가맹점ID');
        $sflArr[3] = option_selected('maker', $sfl, '제조사');
        $sflArr[4] = option_selected('origin', $sfl, '원산지');
        $sflArr[5] = option_selected('model', $sfl, '모델명');
        $sflArr[6] = option_selected('explan', $sfl, '짧은설명');

        $sel_caArr = array('', '', '', '', '', '');
        $sel_caArr[1] = $this->funcomplex_lib->get_category_select_1('sel_ca1', $sca);
        $sel_caArr[2] = $this->funcomplex_lib->get_category_select_2('sel_ca2', $sca);
        $sel_caArr[3] = $this->funcomplex_lib->get_category_select_3('sel_ca3', $sca);
        $sel_caArr[4] = $this->funcomplex_lib->get_category_select_4('sel_ca4', $sca);
        $sel_caArr[5] = $this->funcomplex_lib->get_category_select_5('sel_ca5', $sca);

        $update_timeTag = option_selected('update_time', $q_date_field, "최근수정일");
        $reg_timeTag = option_selected('reg_time', $q_date_field, "최초등록일");

        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $sql = "select * from shop_brand where br_user_yes='0' order by br_name asc ";
        $res = $this->query_model->returnArr($sql);
        $brandStr = "";
        $brandStr .= option_selected('', $q_brand, '전체');
        foreach ($res as $key => $row) {
            $brandStr .= option_selected($row['br_id'], $q_brand, $row['br_name']);
        }

        $qZoneArr = array('', '', '', '', '', '', '', '', '', '');
        $qZoneArr[0] = option_selected('',  $q_zone, '전체');
        $qZoneArr[1] = option_selected('전국', $q_zone, '전국');
        $qZoneArr[2] = option_selected('강원도', $q_zone, '강원도');
        $qZoneArr[3] = option_selected('경기도', $q_zone, '경기도');
        $qZoneArr[4] = option_selected('경상도', $q_zone, '경상도');
        $qZoneArr[5] = option_selected('서울/경기도', $q_zone, '서울/경기도');
        $qZoneArr[6] = option_selected('서울특별시', $q_zone, '서울특별시');
        $qZoneArr[7] = option_selected('전라도', $q_zone, '전라도');
        $qZoneArr[8] = option_selected('제주도', $q_zone, '제주도');
        $qZoneArr[9] = option_selected('충청도', $q_zone, '충청도');

        $stock_fieldArr = array('stock' => "", 'noti' => "");
        $stock_fieldArr['stock'] = option_selected('stock_qty', $q_stock_field, "재고수량");
        $stock_fieldArr['noti'] = option_selected('noti_qty', $q_stock_field, "통보수량");

        $price_fieldArr = array('goods' => "", 'supply' => '', 'normal' => '', 'point' => '');
        $price_fieldArr['goods'] = option_selected('goods_price', $q_price_field, "판매가격");
        $price_fieldArr['supply'] = option_selected('supply_price', $q_price_field, "공급가격");
        $price_fieldArr['normal'] = option_selected('normal_price', $q_price_field, "시중가격");
        $price_fieldArr['point'] = option_selected('gpoint', $q_price_field, "포인트");

        $q_isopenArr = array('', '', '', '', '');
        $q_isopenArr[0] = radio_checked('q_isopen', $q_isopen,  '', '전체');
        $q_isopenArr[1] = radio_checked('q_isopen', $q_isopen, '1', '진열');
        $q_isopenArr[2] = radio_checked('q_isopen', $q_isopen, '2', '품절');
        $q_isopenArr[3] = radio_checked('q_isopen', $q_isopen, '3', '단종');
        $q_isopenArr[4] = radio_checked('q_isopen', $q_isopen, '4', '중지');

        $q_optionArr = array('all' => '', 'use' => '', 'unuse' => '');
        $q_optionArr['all'] = radio_checked('q_option', $q_option,  '', '전체');
        $q_optionArr['use'] = radio_checked('q_option', $q_option, '1', '사용');
        $q_optionArr['unuse'] = radio_checked('q_option', $q_option, '0', '미사용');

        $q_notaxArr = array('', '', '');
        $q_notaxArr[0] = radio_checked('q_notax', $q_notax,  '', '전체');
        $q_notaxArr[1] = radio_checked('q_notax', $q_notax, '1', '과세');
        $q_notaxArr[2] = radio_checked('q_notax', $q_notax, '0', '비과세');

        $q_supplyArr = array('', '', '');
        $q_supplyArr[0] = radio_checked('q_supply', $q_supply,  '', '전체');
        $q_supplyArr[1] = radio_checked('q_supply', $q_supply, '1', '사용');
        $q_supplyArr[2] = radio_checked('q_supply', $q_supply, '0', '미사용');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $update_timeTagLink = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stock_qtyTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normal_priceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supply_priceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $goods_priceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);
        $gpointTag = $this->gvcomplex_lib->subject_sort_link('gpoint', $q2);


        $i = 0;
        $userlistTable = "";
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);
            $userlistTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>{$gw_isopen[$row['isopen']]}</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['supply_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['gpoint']) . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $userlistTable .= '<tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'btn_frmline' => $btn_frmline,
            'code' => $code,
            'sflArr' => $sflArr,
            'stx' => $stx,
            'sel_caArr' => $sel_caArr,
            'update_timeTag' => $update_timeTag,
            'reg_timeTag' => $reg_timeTag,
            'get_search_date' => $get_search_date,
            'brandStr' => $brandStr,
            'qZoneArr' => $qZoneArr,
            'stock_fieldArr' => $stock_fieldArr,
            'fr_stock' => $fr_stock,
            'to_stock' => $to_stock,
            'price_fieldArr' => $price_fieldArr,
            'fr_price' => $fr_price,
            'to_price' => $to_price,
            'q_isopenArr' => $q_isopenArr,
            'q_optionArr' => $q_optionArr,
            'q_notaxArr' => $q_notaxArr,
            'q_supplyArr' => $q_supplyArr,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'mb_idTag' => $mb_idTag,
            'update_timeTagLink' => $update_timeTagLink,
            'stock_qtyTag' => $stock_qtyTag,
            'normal_priceTag' => $normal_priceTag,
            'supply_priceTag' => $supply_priceTag,
            'goods_priceTag' => $goods_priceTag,
            'gpointTag' => $gpointTag,
            'userlistTable' => $userlistTable,
            'pagination' => $pagination
        );
        $this->load->view('admin/goods/userlist', $data);
    }
    private function goods_type($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $jqueryUiDatePick = $this->admin_template->datePicker();

        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');

        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $q_type1 = $this->input->get('q_type1');
        $q_type2 = $this->input->get('q_type2');
        $q_type3 = $this->input->get('q_type3');
        $q_type4 = $this->input->get('q_type4');
        $q_type5 = $this->input->get('q_type5');
        $sca = $this->input->get('sca');
        $stx = $this->input->get('stx');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');

        if (isset($sel_ca1)) $qstr .= "&sel_ca1=$sel_ca1";
        if (isset($sel_ca2)) $qstr .= "&sel_ca2=$sel_ca2";
        if (isset($sel_ca3)) $qstr .= "&sel_ca3=$sel_ca3";
        if (isset($sel_ca4)) $qstr .= "&sel_ca4=$sel_ca4";
        if (isset($sel_ca5)) $qstr .= "&sel_ca5=$sel_ca5";
        if (isset($q_type1)) $qstr .= "&q_type1=$q_type1";
        if (isset($q_type2)) $qstr .= "&q_type2=$q_type2";
        if (isset($q_type3)) $qstr .= "&q_type3=$q_type3";
        if (isset($q_type4)) $qstr .= "&q_type4=$q_type4";
        if (isset($q_type5)) $qstr .= "&q_type5=$q_type5";

        $qstr = $this->load->get_var('qstr');

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_goods a ";
        $sql_search = " where a.use_aff='0' and a.shop_state='0' ";

        // 상품 유형
        $q_type_cnt = 0;
        $sql_or = array();
        if (isset($q_type1) && $q_type1) {
            $sql_or[] = " b.it_type1 = '1' ";
            $q_type_cnt++;
        }

        if (isset($q_type2) && $q_type2) {
            $sql_or[] = " b.it_type2 = '1' ";
            $q_type_cnt++;
        }

        if (isset($q_type3) && $q_type3) {
            $sql_or[] = " b.it_type3 = '1' ";
            $q_type_cnt++;
        }

        if (isset($q_type4) && $q_type4) {
            $sql_or[] = " b.it_type4 = '1' ";
            $q_type_cnt++;
        }

        if (isset($q_type5) && $q_type5) {
            $sql_or[] = " b.it_type5 = '1' ";
            $q_type_cnt++;
        }
        if ($q_type_cnt) {
            $sql_common .= " left join shop_goods_type b on (a.index_no=b.gs_id) ";
            $sql_search .= " and b.mb_id = 'admin' ";
            if ($sql_or) {
                $sql_search .= ' and (' . implode(' or ', $sql_or) . ') ';
            }
        }
        if ($sca) {
            $sql_search .= " and (a.ca_id like '$sca%' or a.ca_id2 like '$sca%' or a.ca_id3 like '$sca%') ";
        }
        // 검색어
        if ($stx) {
            switch ($sfl) {
                case "gname":
                case "explan":
                case "maker":
                case "origin":
                case "model":
                    $sql_search .= " and a.$sfl like '%$stx%' ";
                    break;
                default:
                    $sql_search .= " and a.$sfl like '$stx%' ";
                    break;
            }
        }
        $sql_order = " group by a.index_no order by a.index_no desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql = " select count(DISTINCT a.index_no) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        if (isset($_SESSION['ss_page_rows']))
            $page_rows = $_SESSION['ss_page_rows'];
        else
            $page_rows = 30;

        $rows = $page_rows;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select a.* $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $btn_frmline = "<button type=\"submit\" class=\"btn_lsmall red\"><i class=\"fa fa-refresh fa-spin\"></i> 일괄적용</button>";


        $sflArr = array('', '', '', '', '', '', '');
        $sflArr[0] = option_selected('gname', $sfl, '상품명');
        $sflArr[1] = option_selected('gcode', $sfl, '상품코드');
        $sflArr[2] = option_selected('mb_id', $sfl, '가맹점ID');
        $sflArr[3] = option_selected('maker', $sfl, '제조사');
        $sflArr[4] = option_selected('origin', $sfl, '원산지');
        $sflArr[5] = option_selected('model', $sfl, '모델명');
        $sflArr[6] = option_selected('explan', $sfl, '짧은설명');

        $sel_caArr = array('', '', '', '', '', '');
        $sel_caArr[1] = $this->funcomplex_lib->get_category_select_1('sel_ca1', $sca);
        $sel_caArr[2] = $this->funcomplex_lib->get_category_select_2('sel_ca2', $sca);
        $sel_caArr[3] = $this->funcomplex_lib->get_category_select_3('sel_ca3', $sca);
        $sel_caArr[4] = $this->funcomplex_lib->get_category_select_4('sel_ca4', $sca);
        $sel_caArr[5] = $this->funcomplex_lib->get_category_select_5('sel_ca5', $sca);

        $typeArr = array('', '', '', '', '', '');
        $typeArr[1] = check_checked('q_type1', $q_type1, '1', '함께장터');
        $typeArr[2] = check_checked('q_type2', $q_type2, '1', '베스트셀러');
        $typeArr[3] = check_checked('q_type3', $q_type3, '1', '신상품');
        $typeArr[4] = check_checked('q_type4', $q_type4, '1', '인기상품');
        $typeArr[5] = check_checked('q_type5', $q_type5, '1', '추천상품');

        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $i = 0;
        $typeTable = "";
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];
            $href = base_url() . 'shop/view?index_no=' . $gs_id;

            $sql2 = " select * from shop_goods_type where mb_id = 'admin' and gs_id = ? ";
            $param = array($gs_id);
            $row2 = $this->query_model->returnOneArr($sql2, $param);

            if ($i == 0)
                $typeTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $typeTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        " . $num-- . "
                    </td>
                    <td><a href=\"{$href}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>" . $this->funcomplex_lib->get_text($row['gcode']) . "</td>
                    <td class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td><input type=\"checkbox\" name=\"it_type2[{$i}]\" value=\"1\"" . get_checked($row2['it_type2'], "1") . "></td>
                    <td><input type=\"checkbox\" name=\"it_type3[{$i}]\" value=\"1\"" . get_checked($row2['it_type3'], "1") . "></td>
                    <td><input type=\"checkbox\" name=\"it_type4[{$i}]\" value=\"1\"" . get_checked($row2['it_type4'], "1") . "></td>
                    <td><input type=\"checkbox\" name=\"it_type5[{$i}]\" value=\"1\"" . get_checked($row2['it_type5'], "1") . "></td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td class=\"tar\">" . number_format($row['stock_qty']) . "</td>
                    <td class=\"tar\">" . number_format($row['goods_price']) . "</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $typeTable .= '<tbody><tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'btn_frmline' => $btn_frmline,
            'code' => $code,
            'stx' => $stx,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'sflArr' => $sflArr,
            'sel_caArr' => $sel_caArr,
            'typeArr' => $typeArr,
            'page_rowsOpt' => $page_rowsOpt,
            'typeTable' => $typeTable,
            'pagination' => $pagination,
            'requestUrl' => $requestUrl
        );
        $this->load->view('admin/goods/type', $data);
    }
    private function goods_best_item($code, $headTitle, $headTitleEnd)
    {
        $default = $this->load->get_var('default');
        $list = unserialize(base64_decode($default['de_maintype_best']));
        $listTag = "";
        for ($i = 0; $i < 10; $i++) {
            $list[$i]['subj'] = isset($list[$i]['subj']) ? $list[$i]['subj'] : "";
            $list[$i]['code'] = isset($list[$i]['code']) ? $list[$i]['code'] : "";

            if (!empty($list[$i]['logo_01'])) {
                $file_01 = base_url() . 'data/logo/' . $list[$i]['logo_01'];
                $logo_01 = rpc($file_01, '', '');
                $logo_01_name = $list[$i]['logo_01'];
            } else {
                $logo_01 = "";
            }

            if (!empty($list[$i]['logo_02'])) {
                $file_02 = base_url() . 'data/logo/' . $list[$i]['logo_02'];
                $logo_02 = rpc($file_02, '', '');
                $logo_02_name = $list[$i]['logo_02'];
            } else {
                $logo_02 = "";
            }

            $listTag .= "
                <tr>
                    <td class=\"list1\">탭메뉴 " . ($i + 1) . "</td>
                    <td>
                        <input type=\"file\" name=\"maintype_logo_01[]\" class=\"frm_input wfull\">
                        <div class=\"banner_or_img\"><img src=\"{$logo_01}\"></div>";
            if (!empty($list[$i]['logo_01'])) {
                $listTag .= "<input type=\"checkbox\" name=\"del_img01[$i]\" value='{$list[$i]['logo_01']}'> 삭제";
            }
            $listTag .= "
                    </td>
                    <td>
                        <input type=\"file\" name=\"maintype_logo_02[]\" class=\"frm_input wfull\">
                        <div class=\"banner_or_img\"><img src=\"{$logo_02}\"></div>";
            if (!empty($list[$i]['logo_02'])) {
                $listTag .= "<input type=\"checkbox\" name=\"del_img02[$i]\" value='{$list[$i]['logo_02']}'> 삭제";
            }
            $listTag .= "
                    </td>
                    <td><input type=\"text\" name=\"maintype_subj[]\" value=\"{$list[$i]['subj']}\" class=\"frm_input wfull\" placeholder=\"탭메뉴명\"></td>
                    <td><input type=\"text\" name=\"maintype_code[]\" value=\"{$list[$i]['code']}\" class=\"frm_input wfull\" placeholder=\"상품코드 입력 콤마(,)로 구분하세요.\"></td>
                </tr>";
        }
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'default' => $default, 'listTag' => $listTag);
        $this->load->view('admin/goods/best_item', $data);
    }
    private function goods_brand_list($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_brand ";
        $sql_search = " where br_user_yes = '0' ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if (!$orderby) {
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
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택수정\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">";

        $sflArr = array('', '', '');
        $sflArr[0] = option_selected('br_name', $sfl, '브랜드명 (KOR)');
        $sflArr[1] = option_selected('br_name_eng', $sfl, '브랜드명 (ENG)');
        $sflArr[2] = option_selected('mb_id', $sfl, '회원ID');
        $brNameTag = $this->gvcomplex_lib->subject_sort_link('br_name', $q2);
        $brNameEngTag = $this->gvcomplex_lib->subject_sort_link('br_name_eng', $q2);
        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);

        $i = 0;
        $brandListTable = "";
        foreach ($result as $key => $row) {
            $br_id = $row['br_id'];
            $sql = "select count(*) as cnt from shop_goods where brand_uid=?";
            $param = array($row['br_id']);
            $row2 = $this->query_model->returnOneArr($sql, $param);

            $s_upd = "<a href='" . base_url() . "admin/goods?code=brand_form&w=u&br_id=$br_id$qstr&page=$page' class=\"btn_small\">수정</a>";

            if ($i == 0)
                $brandListTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $brandListTable .= "
                <tr class=\"{$bg}\">
                    <td>			
                        <input type=\"hidden\" name=\"br_id[{$i}]\" value=\"{$br_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td><input type=\"text\" name=\"br_name[{$i}]\" value=\"{$row['br_name']}\" class=\"frm_input\"></td>
                    <td><input type=\"text\" name=\"br_name_eng[{$i}]\" value=\"{$row['br_name_eng']}\" class=\"frm_input\"></td>
                    <td>{$row['mb_id']}</td>
                    <td>" . number_format($row2['cnt']) . "</td>
                    <td><a href=\"" . base_url() . "shop/brandlist?br_id={$br_id}\" target=\"_blank\" class=\"btn_small grey\">브랜드 바로가기</a></td>
                    <td>{$s_upd}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $brandListTable .= '<tbody><tr><td colspan="8" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'q1' => $q1,
            'page' => $page,
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'total_count' => $total_count,
            'sflArr' => $sflArr,
            'stx' => $stx,
            'brNameTag' => $brNameTag,
            'brNameEngTag' => $brNameEngTag,
            'mb_idTag' => $mb_idTag,
            'brandListTable' => $brandListTable,
            'pagination' => $pagination,
            'btn_frmline' => $btn_frmline
        );
        $this->load->view('admin/goods/brand_list', $data);
    }
    private function goods_brand_form($headTitle, $headTitleEnd)
    {
        $w = $this->input->get('w');
        $br_id = $this->input->get('br_id');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $page = $this->input->get('page');
        $qstr = $this->load->get_var('qstr');
        $br = array();
        if ($w == "u") {
            $sql = "select * from shop_brand where br_id=?";
            $param = array($br_id);
            $br = $this->query_model->returnOneArr($sql, $param);
            if (!$br['br_id'])
                alert("자료가 존재하지 않습니다.");
        }
        $brandLogo = "";
        $file = './data/brand/' . $br['br_logo'];
        if (is_file($file) && $br['br_logo']) {
            $file = base_url() . 'data/brand/' . $br['br_logo'];
            $br_logo = rpc($file, '', '');
            $brandLogo = "
                <input type=\"checkbox\" name=\"br_logo_del\" value=\"{$br['br_logo']}\" id=\"br_logo_del\">
                <label for=\"br_logo_del\">삭제</label>
                <div class=\"banner_or_img\"><img src=\"{$br_logo}\"></div>";
        }
        $updateTag = "";
        if (!is_null_time($br['br_updatetime'])) {
            $updateTag = "
                <tr>
                    <th scope=\"row\">최근 수정일</th>
                    <td>{$br['br_updatetime']}</td>
                </tr>";
        }

        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'br_id' => $br_id,
            'sfl' => $sfl,
            'stx' => $stx,
            'br' => $br,
            'w' => $w,
            'brandLogo' => $brandLogo,
            'updateTag' => $updateTag,
            'qstr' => $qstr,
            'page' => $page
        );
        $this->load->view('admin/goods/brand_form', $data);
    }
    private function goods_plan($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_goods_plan ";
        $sql_search = " where mb_id = 'admin' ";
        $sql_order  = " order by pl_no desc ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        // 테이블의 전체 레코드수만 얻음
        $sql = "select count(*) as cnt $sql_common $sql_search ";
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
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택수정\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "admin/goods?code=plan_form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 기획전등록</a>";
        $sflPlName = option_selected('pl_name', $sfl, '기획전명');
        $i = 0;
        $planTable = "";
        foreach ($result as $key => $row) {
            $s_upd = "<a href=\"" . base_url() . "admin/goods?code=plan_form&w=u&pl_no={$row['pl_no']}$qstr&page=$page\" class=\"btn_small\">수정</a>";

            if ($i == 0)
                $planTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $planTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"pl_no[{$i}]\" value=\"{$row['pl_no']}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td><input type=\"checkbox\" name=\"pl_use[{$i}]\" value=\"1\"" . get_checked($row['pl_use'], 1) . "></td>
                    <td><input type=\"text\" name=\"pl_name[{$i}]\" value=\"{$row['pl_name']}\" class=\"frm_input\"></td>
                    <td>{$s_upd}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $planTable .= '<tbody><tr><td colspan="5" class="empty_table">자료가 없습니다.</td></tr>';
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'code' => $code,
            'btn_frmline' => $btn_frmline,
            'stx' => $stx,
            'q1' => $q1,
            'page' => $page,
            'pagination' => $pagination,
            'sflPlName' => $sflPlName,
            'planTable' => $planTable,
            'total_count' => $total_count
        );
        $this->load->view('admin/goods/plan', $data);
    }
    private function goods_plan_form($headTitle, $headTitleEnd)
    {
        $w = $this->input->get('w');
        $pl = array();
        $page = $this->input->get('page');
        $qstr = $this->load->get_var('qstr');
        $pl_no = $this->input->get('pl_no');
        if ($w == '') {
            $pl['pl_use'] = 1;
        } else if ($w == 'u') {
            $sql = "select * from shop_goods_plan where pl_no = ?";
            $param = array($pl_no);
            $pl = $this->query_model->returnOneArr($sql, $param);
            if (!$pl['pl_no'])
                alert('자료가 존재하지 않습니다.');
        }

        $frm_submit = '<div class="btn_confirm">
                <input type="submit" value="저장" class="btn_large" accesskey="s">
                <a href="' . base_url() . 'admin/goods?code=plan' . $qstr . '&page=' . $page . '" class="btn_large bx-white">목록</a>';
        if ($w == 'u') {
            $frm_submit .= '<a href="' . base_url() . 'admin/goods?code=plan_form" class="btn_large bx-red">추가</a>';
        }
        $frm_submit .= '</div>';
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $pl_no = $this->input->get('pl_no');
        $planUrl = "";
        if ($w == 'u') {
            $planUrl = "
                <tr>
                    <th scope=\"row\">기획전 URL</th>
                    <td>
                        <input type=\"text\" value=\"/shop/planlist?pl_no={$pl_no}\" class=\"frm_input\" size=\"50\" readonly style=\"background-color:#ddd;\"> <a href=\"" . base_url() . "/shop/planlist?pl_no={$pl_no}\" target=\"_blank\" class=\"btn_small grey\">기획전 바로가기</a>
                    </td>
                </tr>";
        }
        $helpEnter = help('※ 엔터로 구분해서 입력해주세요.');
        $bimg_str = "";
        $pl['pl_limg'] = isset($pl['pl_limg']) ? $pl['pl_limg'] : "";
        $bimg = './data/plan/' . $pl['pl_limg'];
        $bimgTag = "";
        if (is_file($bimg) && $pl['pl_limg']) {
            $bimg = base_url() . 'data/plan/' . $pl['pl_limg'];
            $size = @getimagesize($bimg);
            if ($size[0] && $size[0] > 700)
                $width = 700;
            else
                $width = $size[0];

            $bimg = rpc($bimg, '', '');

            $bimgTag .= '<input type="checkbox" name="pl_limg_del" value="' . $pl['pl_limg'] . '" id="pl_limg_del"> <label for="pl_limg_del">삭제</label>';
            $bimg_str = '<img src="' . $bimg . '" width="' . $width . '">';
        }
        if ($bimg_str) {
            $bimgTag .= '<div class="banner_or_img">' . $bimg_str . '</div>';
        }
        $helpSize = help('사이즈(318픽셀 * 159픽셀)');

        $bimg_str = "";
        $upimgTag = "";
        $pl['pl_bimg'] = isset($pl['pl_bimg']) ? $pl['pl_bimg'] : "";
        $bimg = './data/plan/' . $pl['pl_bimg'];
        if (is_file($bimg) && $pl['pl_bimg']) {
            $bimg = base_url() . 'data/plan/' . $pl['pl_bimg'];
            $size = @getimagesize($bimg);
            if ($size[0] && $size[0] > 700)
                $width = 700;
            else
                $width = $size[0];
            $upimgTag .= '<input type="checkbox" name="pl_bimg_del" value="' . $pl['pl_bimg'] . '" id="pl_bimg_del"> <label for="pl_bimg_del">삭제</label>';
            $bimg_str = '<img src="' . $bimg . '" width="' . $width . '">';
        }
        if ($bimg_str) {
            $upimgTag .= '<div class="banner_or_img">' . $bimg_str . '</div>';
        }

        $bicon_str = "";
        $upiconTag = "";
        $pl['pl_icon'] = isset($pl['pl_icon']) ? $pl['pl_icon'] : "";
        $bicon = './data/plan/' . $pl['pl_icon'];
        if (is_file($bicon) && $pl['pl_icon']) {
            $bicon = base_url() . 'data/plan/' . $pl['pl_icon'];
            $size = @getimagesize($bicon);
            if ($size[0] && $size[0] > 700)
                $width = 700;
            else
                $width = $size[0];
            $upiconTag .= '<input type="checkbox" name="pl_icon_del" value="' . $pl['pl_icon'] . '" id="pl_icon_del"> <label for="pl_icon_del">삭제</label>';
            $bicon_str = '<img src="' . $bicon . '" width="' . $width . '">';
        }
        if ($bicon_str) {
            $upiconTag .= '<div class="banner_or_img">' . $bicon_str . '</div>';
        }
        $helpSize2 = help('사이즈(1000픽셀 * auto)');
        $pl['pl_name'] = isset($pl['pl_name']) ? $pl['pl_name'] : "";
        $pl['pl_it_code'] = isset($pl['pl_it_code']) ? $pl['pl_it_code'] : "";

        $helpSize3 = help('사이즈(100픽셀 * 100픽셀)');

        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'frm_submit' => $frm_submit,
            'sfl' => $sfl,
            'stx' => $stx,
            'page' => $page,
            'pl_no' => $pl_no,
            'pl' => $pl,
            'planUrl' => $planUrl,
            'helpEnter' => $helpEnter,
            'bimgTag' => $bimgTag,
            'helpSize' => $helpSize,
            'upimgTag' => $upimgTag,
            'helpSize2' => $helpSize2,
            'upiconTag' => $upiconTag,
            'helpSize3' => $helpSize3,
            'w' => $w
        );
        $this->load->view('admin/goods/plan_form', $data);
    }
    private function goods_stock($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $jqueryUiDatePick = $this->admin_template->datePicker();

        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $q_date_field = $this->input->get('q_date_field');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $q_stock_field = $this->input->get('q_stock_field');
        $fr_stock = $this->input->get('fr_stock');
        $to_stock = $this->input->get('to_stock');
        $q_price_field = $this->input->get('q_price_field');
        $fr_price = $this->input->get('fr_price');
        $to_price = $this->input->get('to_price');
        $q_isopen = $this->input->get('q_isopen');
        $q_option = $this->input->get('q_option');
        $q_supply = $this->input->get('q_supply');
        $q_notax = $this->input->get('q_notax');
        $stx = $this->input->get('stx');
        $sfl = $this->input->get('sfl');
        $sca = $this->input->get('sca');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $orderby = $this->input->get('orderby');
        $filed = $this->input->get('filed');
        $page = $this->input->get('page');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';
        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";
        $sql_common = " from shop_goods ";
        $sql_search = " where use_aff = 0 and shop_state = 0 ";
        $sql_search .= " and (stock_qty <= noti_qty and stock_mod = 1 and opt_subject = '') ";

        if ($sca) {
            $sql_search .= " and (ca_id like '$sca%' or ca_id2 like '$sca%' or ca_id3 like '$sca%') ";
        }

        // 검색어
        if ($stx) {
            switch ($sfl) {
                case "gname":
                case "explan":
                case "maker":
                case "origin":
                case "model":
                    $sql_search .= " and $sfl like '%$stx%' ";
                    break;
                default:
                    $sql_search .= " and $sfl like '$stx%' ";
                    break;
            }
        }

        // 기간검색
        if ($fr_date && $to_date)
            $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and $q_date_field between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        // 브랜드
        if (isset($q_brand) && $q_brand)
            $sql_search .= " and brand_uid = '$q_brand' ";

        // 배송가능 지역
        if (isset($q_zone) && $q_zone)
            $sql_search .= " and zone = '$q_zone' ";

        // 상품재고
        if ($fr_stock && $to_stock)
            $sql_search .= " and $q_stock_field between '$fr_stock' and '$to_stock' ";

        // 상품가격
        if ($fr_price && $to_price)
            $sql_search .= " and $q_price_field between '$fr_price' and '$to_price' ";

        // 판매여부
        if (isset($q_isopen) && is_numeric($q_isopen))
            $sql_search .= " and isopen='$q_isopen' ";

        // 과세유형
        if (isset($q_notax) && is_numeric($q_notax))
            $sql_search .= " and notax = '$q_notax' ";

        // 상품 필수옵션
        if (isset($q_option) && is_numeric($q_option)) {
            if ($q_option)
                $sql_search .= " and opt_subject <> '' ";
            else
                $sql_search .= " and opt_subject = '' ";
        }

        // 상품 추가옵션
        if (isset($q_supply) && is_numeric($q_supply)) {
            if ($q_supply)
                $sql_search .= " and spl_subject <> '' ";
            else
                $sql_search .= " and spl_subject = '' ";
        }

        if (!$orderby) {
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

        if (isset($_SESSION['ss_page_rows']))
            $page_rows = $_SESSION['ss_page_rows'];
        else
            $page_rows = 30;

        $rows = $page_rows;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "AdminProcess/goods_list_excel?flag=normal&$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장(열린장터)</a>
            <a href=\"" . base_url() . "AdminProcess/goods_list_excel?flag=timesale&$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장(함께장터)</a>
            <a href=\"" . base_url() . "AdminProcess/goods_list_excel?flag=all&$q1\" class=\"btn_lsmall bx-white\"><i class=\"fa fa-file-excel-o\"></i> 엑셀저장(전체)</a>
            <a href=\"" . base_url() . "admin/goods?code=form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 상품등록</a>";

        $sflArr = array('', '', '', '', '', '', '');
        $sflArr[0] = option_selected('gname', $sfl, '상품명');
        $sflArr[1] = option_selected('gcode', $sfl, '상품코드');
        $sflArr[2] = option_selected('mb_id', $sfl, '가맹점ID');
        $sflArr[3] = option_selected('maker', $sfl, '제조사');
        $sflArr[4] = option_selected('origin', $sfl, '원산지');
        $sflArr[5] = option_selected('model', $sfl, '모델명');
        $sflArr[6] = option_selected('explan', $sfl, '짧은설명');

        $sel_caArr = array('', '', '', '', '', '');
        $sel_caArr[1] = $this->funcomplex_lib->get_category_select_1('sel_ca1', $sca);
        $sel_caArr[2] = $this->funcomplex_lib->get_category_select_2('sel_ca2', $sca);
        $sel_caArr[3] = $this->funcomplex_lib->get_category_select_3('sel_ca3', $sca);
        $sel_caArr[4] = $this->funcomplex_lib->get_category_select_4('sel_ca4', $sca);
        $sel_caArr[5] = $this->funcomplex_lib->get_category_select_5('sel_ca5', $sca);

        $update_timeTag = option_selected('update_time', $q_date_field, "최근수정일");
        $reg_timeTag = option_selected('reg_time', $q_date_field, "최초등록일");
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);
        $sql = "select * from shop_brand where br_user_yes='0' order by br_name asc ";
        $res = $this->query_model->returnArr($sql);
        $brandStr = "";
        $brandStr .= option_selected('', $q_brand, '전체');
        foreach ($res as $key => $row) {
            $brandStr .= option_selected($row['br_id'], $q_brand, $row['br_name']);
        }
        $qZoneArr = array('', '', '', '', '', '', '', '', '', '');
        $qZoneArr[0] = option_selected('',  $q_zone, '전체');
        $qZoneArr[1] = option_selected('전국', $q_zone, '전국');
        $qZoneArr[2] = option_selected('강원도', $q_zone, '강원도');
        $qZoneArr[3] = option_selected('경기도', $q_zone, '경기도');
        $qZoneArr[4] = option_selected('경상도', $q_zone, '경상도');
        $qZoneArr[5] = option_selected('서울/경기도', $q_zone, '서울/경기도');
        $qZoneArr[6] = option_selected('서울특별시', $q_zone, '서울특별시');
        $qZoneArr[7] = option_selected('전라도', $q_zone, '전라도');
        $qZoneArr[8] = option_selected('제주도', $q_zone, '제주도');
        $qZoneArr[9] = option_selected('충청도', $q_zone, '충청도');

        $stock_fieldArr = array('stock' => "", 'noti' => "");
        $stock_fieldArr['stock'] = option_selected('stock_qty', $q_stock_field, "재고수량");
        $stock_fieldArr['noti'] = option_selected('noti_qty', $q_stock_field, "통보수량");

        $price_fieldArr = array('goods' => "", 'supply' => '', 'normal' => '', 'point' => '');
        $price_fieldArr['goods'] = option_selected('goods_price', $q_price_field, "판매가격");
        $price_fieldArr['supply'] = option_selected('supply_price', $q_price_field, "공급가격");
        $price_fieldArr['normal'] = option_selected('normal_price', $q_price_field, "시중가격");
        $price_fieldArr['point'] = option_selected('gpoint', $q_price_field, "포인트");

        $q_isopenArr = array('', '', '', '', '');
        $q_isopenArr[0] = radio_checked('q_isopen', $q_isopen,  '', '전체');
        $q_isopenArr[1] = radio_checked('q_isopen', $q_isopen, '1', '진열');
        $q_isopenArr[2] = radio_checked('q_isopen', $q_isopen, '2', '품절');
        $q_isopenArr[3] = radio_checked('q_isopen', $q_isopen, '3', '단종');
        $q_isopenArr[4] = radio_checked('q_isopen', $q_isopen, '4', '중지');

        $q_optionArr = array('all' => '', 'use' => '', 'unuse' => '');
        $q_optionArr['all'] = radio_checked('q_option', $q_option,  '', '전체');
        $q_optionArr['use'] = radio_checked('q_option', $q_option, '1', '사용');
        $q_optionArr['unuse'] = radio_checked('q_option', $q_option, '0', '미사용');

        $q_notaxArr = array('', '', '');
        $q_notaxArr[0] = radio_checked('q_notax', $q_notax,  '', '전체');
        $q_notaxArr[1] = radio_checked('q_notax', $q_notax, '1', '과세');
        $q_notaxArr[2] = radio_checked('q_notax', $q_notax, '0', '비과세');

        $q_supplyArr = array('', '', '');
        $q_supplyArr[0] = radio_checked('q_supply', $q_supply,  '', '전체');
        $q_supplyArr[1] = radio_checked('q_supply', $q_supply, '1', '사용');
        $q_supplyArr[2] = radio_checked('q_supply', $q_supply, '0', '미사용');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $update_timeTag = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stock_qtyTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normal_priceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supply_priceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $goods_priceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);
        $gpointTag = $this->gvcomplex_lib->subject_sort_link('gpoint', $q2);

        $i = 0;
        $stockTable = "";
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);
            $stockTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['supply_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['gpoint']) . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $stockTable .= '<tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';

        $data = array(
            'btn_frmline' => $btn_frmline,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'code' => $code,
            'stx' => $stx,
            'fr_stock' => $fr_stock,
            'to_stock' => $to_stock,
            'fr_price' => $fr_price,
            'to_price' => $to_price,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'pagination' => $pagination,
            'sflArr' => $sflArr,
            'sel_caArr' => $sel_caArr,
            'update_timeTag' => $update_timeTag,
            'reg_timeTag' => $reg_timeTag,
            'get_search_date' => $get_search_date,
            'brandStr' => $brandStr,
            'qZoneArr' => $qZoneArr,
            'stock_fieldArr' => $stock_fieldArr,
            'price_fieldArr' => $price_fieldArr,
            'q_isopenArr' => $q_isopenArr,
            'q_optionArr' => $q_optionArr,
            'q_notaxArr' => $q_notaxArr,
            'q_supplyArr' => $q_supplyArr,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'mb_idTag' => $mb_idTag,
            'update_timeTag' => $update_timeTag,
            'stock_qtyTag' => $stock_qtyTag,
            'normal_priceTag' => $normal_priceTag,
            'supply_priceTag' => $supply_priceTag,
            'goods_priceTag' => $goods_priceTag,
            'gpointTag' => $gpointTag,
            'stockTable' => $stockTable,
            'requestUrl' => $requestUrl
        );
        $this->load->view('admin/goods/stock', $data);
    }
    private function goods_optstock($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $jqueryUiDatePick = $this->admin_template->datePicker();

        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $q_date_field = $this->input->get('q_date_field');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $q_stock_field = $this->input->get('q_stock_field');
        $fr_stock = $this->input->get('fr_stock');
        $to_stock = $this->input->get('to_stock');
        $q_price_field = $this->input->get('q_price_field');
        $fr_price = $this->input->get('fr_price');
        $to_price = $this->input->get('to_price');
        $q_isopen = $this->input->get('q_isopen');
        $q_option = $this->input->get('q_option');
        $q_supply = $this->input->get('q_supply');
        $q_notax = $this->input->get('q_notax');
        $stx = $this->input->get('stx');
        $sfl = $this->input->get('sfl');
        $sca = $this->input->get('sca');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $orderby = $this->input->get('orderby');
        $page = $this->input->get('page');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';
        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common  = " from shop_goods_option a left join shop_goods b ON (a.gs_id=b.index_no) ";
        $sql_search  = " where b.shop_state = 0
                            and b.use_aff = 0
                            and a.io_use = 1
                            and a.io_noti_qty <> '999999999'
                            and a.io_stock_qty <= a.io_noti_qty ";

        // 카테고리
        if ($sca) {
            $sql_search .= " and (b.ca_id like '$sca%' or b.ca_id2 like '$sca%' or b.ca_id3 like '$sca%') ";
        }

        // 검색어
        if ($stx) {
            switch ($sfl) {
                case "gname":
                case "explan":
                case "maker":
                case "origin":
                case "model":
                    $sql_search .= " and b.$sfl like '%$stx%' ";
                    break;
                default:
                    $sql_search .= " and b.$sfl like '$stx%' ";
                    break;
            }
        }

        // 기간검색
        if ($fr_date && $to_date)
            $sql_search .= " and b.$q_date_field between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and b.$q_date_field between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and b.$q_date_field between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        // 브랜드
        if (isset($q_brand) && $q_brand)
            $sql_search .= " and b.brand_uid = '$q_brand' ";

        // 배송가능 지역
        if (isset($q_zone) && $q_zone)
            $sql_search .= " and b.zone = '$q_zone' ";

        // 상품재고
        if ($fr_stock && $to_stock)
            $sql_search .= " and b.$q_stock_field between '$fr_stock' and '$to_stock' ";

        // 상품가격
        if ($fr_price && $to_price)
            $sql_search .= " and b.$q_price_field between '$fr_price' and '$to_price' ";

        // 판매여부
        if (isset($q_isopen) && is_numeric($q_isopen))
            $sql_search .= " and b.isopen='$q_isopen' ";

        // 과세유형
        if (isset($q_notax) && is_numeric($q_notax))
            $sql_search .= " and b.notax = '$q_notax' ";

        // 상품 필수옵션
        if (isset($q_option) && is_numeric($q_option)) {
            if ($q_option)
                $sql_search .= " and b.opt_subject <> '' ";
            else
                $sql_search .= " and b.opt_subject = '' ";
        }

        // 상품 추가옵션
        if (isset($q_supply) && is_numeric($q_supply)) {
            if ($q_supply)
                $sql_search .= " and b.spl_subject <> '' ";
            else
                $sql_search .= " and b.spl_subject = '' ";
        }

        if (!$orderby) {
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

        if (isset($_SESSION['ss_page_rows']))
            $page_rows = $_SESSION['ss_page_rows'];
        else
            $page_rows = 30;

        $rows = $page_rows;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select a.*, b.simg1, b.gcode, b.gname, b.mb_id
                        $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $sflArr = array('', '', '', '', '', '', '');
        $sflArr[0] = option_selected('gname', $sfl, '상품명');
        $sflArr[1] = option_selected('gcode', $sfl, '상품코드');
        $sflArr[2] = option_selected('mb_id', $sfl, '가맹점ID');
        $sflArr[3] = option_selected('maker', $sfl, '제조사');
        $sflArr[4] = option_selected('origin', $sfl, '원산지');
        $sflArr[5] = option_selected('model', $sfl, '모델명');
        $sflArr[6] = option_selected('explan', $sfl, '짧은설명');

        $sel_caArr = array('', '', '', '', '', '');
        $sel_caArr[1] = $this->funcomplex_lib->get_category_select_1('sel_ca1', $sca);
        $sel_caArr[2] = $this->funcomplex_lib->get_category_select_2('sel_ca2', $sca);
        $sel_caArr[3] = $this->funcomplex_lib->get_category_select_3('sel_ca3', $sca);
        $sel_caArr[4] = $this->funcomplex_lib->get_category_select_4('sel_ca4', $sca);
        $sel_caArr[5] = $this->funcomplex_lib->get_category_select_5('sel_ca5', $sca);

        $update_timeTag = option_selected('update_time', $q_date_field, "최근수정일");
        $reg_timeTag = option_selected('reg_time', $q_date_field, "최초등록일");
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $sql = "select * from shop_brand where br_user_yes='0' order by br_name asc ";
        $res = $this->query_model->returnArr($sql);
        $brandStr = "";
        $brandStr .= option_selected('', $q_brand, '전체');
        foreach ($res as $key => $row) {
            $brandStr .= option_selected($row['br_id'], $q_brand, $row['br_name']);
        }
        $qZoneArr = array('', '', '', '', '', '', '', '', '', '');
        $qZoneArr[0] = option_selected('',  $q_zone, '전체');
        $qZoneArr[1] = option_selected('전국', $q_zone, '전국');
        $qZoneArr[2] = option_selected('강원도', $q_zone, '강원도');
        $qZoneArr[3] = option_selected('경기도', $q_zone, '경기도');
        $qZoneArr[4] = option_selected('경상도', $q_zone, '경상도');
        $qZoneArr[5] = option_selected('서울/경기도', $q_zone, '서울/경기도');
        $qZoneArr[6] = option_selected('서울특별시', $q_zone, '서울특별시');
        $qZoneArr[7] = option_selected('전라도', $q_zone, '전라도');
        $qZoneArr[8] = option_selected('제주도', $q_zone, '제주도');
        $qZoneArr[9] = option_selected('충청도', $q_zone, '충청도');

        $stock_fieldArr = array('stock' => "", 'noti' => "");
        $stock_fieldArr['stock'] = option_selected('stock_qty', $q_stock_field, "재고수량");
        $stock_fieldArr['noti'] = option_selected('noti_qty', $q_stock_field, "통보수량");

        $price_fieldArr = array('goods' => "", 'supply' => '', 'normal' => '', 'point' => '');
        $price_fieldArr['goods'] = option_selected('goods_price', $q_price_field, "판매가격");
        $price_fieldArr['supply'] = option_selected('supply_price', $q_price_field, "공급가격");
        $price_fieldArr['normal'] = option_selected('normal_price', $q_price_field, "시중가격");
        $price_fieldArr['point'] = option_selected('gpoint', $q_price_field, "포인트");

        $q_isopenArr = array('', '', '', '', '');
        $q_isopenArr[0] = radio_checked('q_isopen', $q_isopen,  '', '전체');
        $q_isopenArr[1] = radio_checked('q_isopen', $q_isopen, '1', '진열');
        $q_isopenArr[2] = radio_checked('q_isopen', $q_isopen, '2', '품절');
        $q_isopenArr[3] = radio_checked('q_isopen', $q_isopen, '3', '단종');
        $q_isopenArr[4] = radio_checked('q_isopen', $q_isopen, '4', '중지');

        $q_optionArr = array('all' => '', 'use' => '', 'unuse' => '');
        $q_optionArr['all'] = radio_checked('q_option', $q_option,  '', '전체');
        $q_optionArr['use'] = radio_checked('q_option', $q_option, '1', '사용');
        $q_optionArr['unuse'] = radio_checked('q_option', $q_option, '0', '미사용');

        $q_notaxArr = array('', '', '');
        $q_notaxArr[0] = radio_checked('q_notax', $q_notax,  '', '전체');
        $q_notaxArr[1] = radio_checked('q_notax', $q_notax, '1', '과세');
        $q_notaxArr[2] = radio_checked('q_notax', $q_notax, '0', '비과세');

        $q_supplyArr = array('', '', '');
        $q_supplyArr[0] = radio_checked('q_supply', $q_supply,  '', '전체');
        $q_supplyArr[1] = radio_checked('q_supply', $q_supply, '1', '사용');
        $q_supplyArr[2] = radio_checked('q_supply', $q_supply, '0', '미사용');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('b.gcode', $q2);
        $iotypeTag = $this->gvcomplex_lib->subject_sort_link('a.io_type', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('b.gname', $q2);
        $ioStockQtyTag = $this->gvcomplex_lib->subject_sort_link('a.io_stock_qty', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('b.mb_id', $q2);
        $io_idTag = $this->gvcomplex_lib->subject_sort_link('a.io_id', $q2);
        $io_noti_qtyTag = $this->gvcomplex_lib->subject_sort_link('a.io_noti_qty', $q2);

        $optStockTable = "";
        $i = 0;
        foreach ($result as $key => $row) {
            if ($row['io_type'])
                $io_type = '추가옵션';
            else
                $io_type = '필수옵션';

            $bg = 'list' . ($i % 2);
            $optStockTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"io_no[{$i}]\" value=\"{$row['io_no']}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$row['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($row['gs_id'], $row['simg1'], 40, 40) . "</a></td>
                    <td class=\"tal\">{$row['gcode']}</td>
                    <td rowspan=\"2\">{$io_type}</td>
                    <td class=\"tal\" colspan=\"2\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td class=\"tar\">" . number_format($row['io_stock_qty']) . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$row['gs_id']}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td>{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal fc_00f\">" . str_replace(' ', ' &gt; ', $row['io_id']) . "</td>
                    <td class=\"tar fc_00f\">" . number_format($row['io_noti_qty']) . "</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $optStockTable .= '<tr><td colspan="9" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'headTitleEnd' => $headTitleEnd,
            'stx' => $stx,
            'fr_stock' => $fr_stock,
            'to_stock' => $to_stock,
            'fr_price' => $fr_price,
            'to_price' => $to_price,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'pagination' => $pagination,
            'sflArr' => $sflArr,
            'sel_caArr' => $sel_caArr,
            'update_timeTag' => $update_timeTag,
            'r1g_timeTag' => $reg_timeTag,
            'get_search_date' => $get_search_date,
            'brandStr' => $brandStr,
            'qZoneArr' => $qZoneArr,
            'stock_fieldArr' => $stock_fieldArr,
            'price_fieldArr' => $price_fieldArr,
            'q_isopenArr' => $q_isopenArr,
            'q_optionArr' => $q_optionArr,
            'q_notaxArr' => $q_notaxArr,
            'q_supplyArr' => $q_supplyArr,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'iotypeTag' => $iotypeTag,
            'gnameTag' => $gnameTag,
            'ioStockQtyTag' => $ioStockQtyTag,
            'mb_idTag' => $mb_idTag,
            'io_idTag' => $io_idTag,
            'io_noti_qtyTag' => $io_noti_qtyTag,
            'optStockTable' => $optStockTable,
            'requestUrl' => $requestUrl
        );
        $this->load->view('admin/goods/optstock', $data);
    }
    private function goods_xls_reg($headTitle, $headTitleEnd)
    {
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/goods/xls_reg', $data);
    }
    private function goods_xls_reg_update($headTitle, $headTitleEnd)
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $dup_count = 0;
        $total_count = 0;
        $fail_count = 0;
        $succ_count = 0;
        $dup_gcode = array();
        $fail_gcode = array();
        if ($_FILES['excelfile']['tmp_name']) {
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
            for ($i = 4; $i <= $data->sheets[0]['numRows']; $i++) {
                if (trim($data->sheets[0]['cells'][$i][1]) == '')
                    continue;
                $total_count++;

                $j = 1;

                $mb_id            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매자ID
                $gcode            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 상품코드
                $ca_id            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 대표분류
                $ca_id2            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 추가분류2
                $ca_id3            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 추가분류3
                $gname            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 상품명
                $explan            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 짧은설명
                $keywords        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 검색키워드
                $model            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 모델명
                $brand_nm        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 브랜드
                $notax            = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 과세설정
                $zone            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매가능지역
                $zone_msg        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매가능지역 추가설명
                $origin            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 원산지
                $maker            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 제조사
                $isopen            = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 판매여부
                $supply_price    = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 공급가격
                $normal_price    = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 시중가격
                $goods_price    = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 판매가격
                $price_msg        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 가격대체문구
                $stock_mod        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 재고적용타입
                $stock_qty        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 재고수량
                $noti_qty        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 재고통보수량
                $odr_min        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 최소주문한도
                $odr_max        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 최대주문한도
                $gpoint            = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 포인트
                $sb_date        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매기간 시작일
                $eb_date        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매기간 종료일
                $buy_level        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 구매가능레벨
                $buy_only        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 가격공개
                $sc_type        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 배송비유형
                $sc_method        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 배송비결제
                $sc_amt            = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 기본배송비
                $sc_minimum        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 조건배송비
                $simg_type        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 이미지등록방식
                $simg1            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 소이미지
                $simg2            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지1
                $simg3            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지2
                $simg4            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지3
                $simg5            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지4
                $simg6            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지5
                $info_gubun        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 정보고시
                $info_value        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 정보고시 값
                $memo            = $data->sheets[0]['cells'][$i][$j++]; // 상세설명
                $admin_memo        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 관리자메모

                if (!$mb_id || !$gcode || !$gname) {
                    $fail_count++;
                    continue;
                }

                // 상품코드로 상품이 있는지 먼저 검사
                $sql2 = " select count(*) as cnt from shop_goods where gcode = ?";
                $param = array($gcode);
                $row2 = $this->query_model->returnOneArr($sql2, $param);
                if ($row2['cnt']) {
                    $fail_gcode[] = $gcode;
                    $dup_gcode[] = $gcode;
                    $dup_count++;
                    $fail_count++;
                    continue;
                }

                unset($value);
                $value['mb_id']            = $mb_id; // 판매자ID
                $value['gcode']            = $gcode; // 상품코드
                $value['ca_id']            = $ca_id; // 대표분류
                $value['ca_id2']        = $ca_id2; // 추가분류2
                $value['ca_id3']        = $ca_id3; // 추가분류3
                $value['gname']            = $gname; // 상품명
                $value['explan']        = $explan; // 짧은설명
                $value['keywords']        = $keywords; // 검색키워드
                $value['model']            = $model; // 모델명
                $value['brand_uid']        = $this->common_lib->get_brand_chk($brand_nm); // 브랜드주키
                $value['brand_nm']        = $brand_nm; // 브랜드명
                $value['notax']            = $notax; // 과세설정
                $value['zone']            = $zone; // 판매가능지역
                $value['zone_msg']        = $zone_msg; // 판매가능지역 추가설명
                $value['origin']        = $origin; // 원산지
                $value['maker']            = $maker; // 제조사
                $value['isopen']        = $isopen; // 판매여부
                $value['supply_price']    = $supply_price; // 공급가격
                $value['normal_price']    = $normal_price; // 시중가격
                $value['goods_price']   = $goods_price; // 판매가격
                $value['price_msg']        = $price_msg; // 가격대체문구
                $value['stock_mod']        = $stock_mod; // 재고적용타입
                $value['stock_qty']        = $stock_qty; // 재고수량
                $value['noti_qty']        = $noti_qty; // 재고통보수량
                $value['odr_min']        = $odr_min; // 최소주문한도
                $value['odr_max']        = $odr_max; // 최대주문한도
                $value['gpoint']        = $gpoint; // 포인트
                $value['sb_date']        = $sb_date; // 판매기간 시작일
                $value['eb_date']        = $eb_date; // 판매기간 종료일
                $value['buy_level']        = $buy_level; // 구매가능레벨
                $value['buy_only']        = $buy_only; // 가격공개
                $value['sc_type']        = $sc_type; // 배송비유형
                $value['sc_method']        = $sc_method; // 배송비결제
                $value['sc_amt']        = $sc_amt; // 기본배송비
                $value['sc_minimum']    = $sc_minimum; // 조건배송비
                $value['simg_type']        = $simg_type; // 이미지등록방식
                $value['simg1']            = $simg1; // 소이미지
                $value['simg2']            = $simg2; // 중이미지1
                $value['simg3']            = $simg3; // 중이미지2
                $value['simg4']            = $simg4; // 중이미지3
                $value['simg5']            = $simg5; // 중이미지4
                $value['simg6']            = $simg6; // 중이미지5
                $value['info_gubun']    = $info_gubun; // 상세설명
                $value['info_value']    = $info_value; // 상세설명
                $value['memo']            = $memo; // 상세설명
                $value['admin_memo']    = $admin_memo; // 관리자메모
                $value['reg_time']        = $OM_TIME_YMDHIS; //등록일시
                $value['update_time']    = $OM_TIME_YMDHIS; //수정일시
                $this->query_model->insertObj("shop_goods", $value);
                $succ_count++;
            }
        }
        $fail_countTag = "";
        if ($fail_count > 0) {
            $fail_countTag = "
                <tr>
                    <th scope=\"row\">실패상품코드</th>
                    <td>" . implode(', ', $fail_gcode) . "</td>
                </tr>";
        }
        $dup_countTag = "";
        if ($dup_count > 0) {
            $dup_countTag = "
                <tr>
                    <th scope=\"row\">상품코드중복건수</th>
                    <td>" . number_format($dup_count) . "</td>
                </tr>
                <tr>
                    <th scope=\"row\">중복상품코드</th>
                    <td>" . implode('<br>', $dup_gcode) . "</td>
                </tr>";
        }
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'dup_countTag' => $dup_countTag,
            'total_count' => $total_count,
            'fail_countTag' => $fail_countTag,
            'succ_count' => $succ_count,
            'fail_count' => $fail_count
        );
        $this->load->view('admin/goods/xls_reg_update', $data);
    }
    private function goods_xls_option_reg($headTitle, $headTitleEnd)
    {
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/goods/xls_option_reg', $data);
    }
    private function goods_xls_option_reg_update($headTitle, $headTitleEnd)
    {
        $fail_gcode = array();
        $update_gcode = array();
        $total_count = 0;
        $fail_count = 0;
        $succ_count = 0;
        $update_count = 0;
        if ($_FILES['excelfile']['tmp_name']) {
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
            for ($i = 3; $i <= $data->sheets[0]['numRows']; $i++) {
                if (trim($data->sheets[0]['cells'][$i][1]) == '')
                    continue;

                $total_count++;

                $j = 1;

                $gcode            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 상품코드
                $opt_subject    = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 옵션명
                $io_id            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 옵션항목
                $io_price        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 옵션가격
                $io_stock_qty    = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 재고수량
                $io_noti_qty    = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 통보수량
                $io_use            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 사용여부
                $io_type        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 옵션형식

                // 상품코드 체크
                $sql = " select COUNT(*) as cnt, index_no, opt_subject, spl_subject from shop_goods where gcode = ? and gcode <> '' ";
                $param = array($gcode);
                $gs = $this->query_model->returnOneArr($sql, $param);

                $gs_id = $gs['index_no'];

                if (!$gs['cnt'] || !$gcode || !$opt_subject || !$io_id || ($io_type != 0 && $io_type != 1)) {
                    $fail_gcode[] = $gcode;
                    $fail_count++;
                    continue;
                }

                // 상품 테이블  opt_subject 업데이트
                if ($io_type == 0 && $gs['opt_subject'] != $opt_subject) {
                    $param = array('opt_subject' => $opt_subject);
                    $this->query_model->updateObj('shop_goods', $param, "gcode='{$gcode}'");
                }

                // 상품 테이블 spl_subject 업데이트
                if ($io_type == 1 && $gs['spl_subject'] != $opt_subject) {
                    $param = array('spl_subject' => $opt_subject);
                    $this->query_model->updateObj('shop_goods', $param, "gcode='{$gcode}'");
                }

                // io_id
                $opt = array();
                $opt_arr = explode(',', $io_id);
                for ($k = 0; $k < count($opt_arr); $k++) {
                    $opt[] = $opt_arr[$k];
                }
                $io_id_chr = implode(' ', $opt);

                // gs_id, io_id, io_type 중복 - 업데이트
                $sql2 = " select io_no, count(*) as cnt from shop_goods_option where gs_id = ? and io_id = ? and io_type = ? ";
                $param = array($gs_id, $io_id_chr, $io_type);
                $row2 = $this->query_model->returnOneArr($sql2, $param);
                if ($row2['cnt']) {
                    $update_gcode[] = $gcode;
                    $update_count++;
                    $param = array(
                        'io_id' => $io_id_chr,
                        'io_type' => $io_type,
                        'gs_id' => $gs_id,
                        'io_price' => $io_price,
                        'io_stock_qty' => $io_stock_qty,
                        'io_noti_qty' => $io_noti_qty,
                        'io_use' => $io_use
                    );
                    $this->query_model->updateObj('shop_goods_option', $param, "io_no='{$row2['io_no']}'");
                } else { // 옵션 테이블 insert
                    $sql = " insert into shop_goods_option
                                    set io_id = '$io_id_chr'
                                      , io_type = '$io_type'
                                      , gs_id = '$gs_id'
                                      , io_price = '$io_price'
                                      , io_stock_qty = '$io_stock_qty'
                                      , io_noti_qty = '$io_noti_qty'
                                      , io_use = '$io_use' ";
                    $this->query_model->returnNull($sql);

                    $succ_count++;
                }
            }
        }
        $update_countTag = "";
        if ($update_count > 0) {
            $update_countTag = "
                <tr>
                    <th scope=\"row\">업데이트상품코드</th>
                    <td>" . implode(', ', array_unique($update_gcode)) . "</td>
                </tr>";
        }
        $fail_countTag = "";
        if ($fail_count > 0) {
            $fail_countTag = "
                <tr>
                    <th scope=\"row\">실패상품코드</th>
                    <td>" . implode(', ', array_unique($fail_gcode)) . "</td>
                </tr>";
        }
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'total_count' => $total_count,
            'succ_count' => $succ_count,
            'update_count' => $update_count,
            'update_countTag' => $update_countTag,
            'fail_countTag' => $fail_countTag,
            'fail_count' => $fail_count
        );
        $this->load->view('admin/goods/xls_option_reg_update', $data);
    }
    private function goods_xls_mod($headTitle, $headTitleEnd)
    {
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/goods/xls_mod', $data);
    }
    private function goods_xls_mod_update($headTitle, $headTitleEnd)
    {
        $fail_gcode = array();
        $fail_count = 0;
        $total_count = 0;
        $succ_count = 0;
        if ($_FILES['excelfile']['tmp_name']) {
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
            $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

            for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                if (trim($data->sheets[0]['cells'][$i][1]) == '')
                    continue;

                $total_count++;

                $j = 1;

                $mb_id            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매자ID
                $gcode            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 상품코드
                $ca_id            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 대표분류
                $ca_id2            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 추가분류2
                $ca_id3            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 추가분류3
                $gname            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 상품명
                $explan            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 짧은설명
                $keywords        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 검색키워드
                $model            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 모델명
                $brand_nm        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 브랜드
                $notax            = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 과세설정
                $zone            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매가능지역
                $zone_msg        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매가능지역 추가설명
                $origin            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 원산지
                $maker            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 제조사
                $isopen            = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 판매여부
                $supply_price    = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 공급가격
                $normal_price    = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 시중가격
                $goods_price    = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 판매가격
                $price_msg        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 가격대체문구
                $stock_mod        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 재고적용타입
                $stock_qty        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 재고수량
                $noti_qty        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 재고통보수량
                $odr_min        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 최소주문한도
                $odr_max        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 최대주문한도
                $gpoint            = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 포인트
                $sb_date        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매기간 시작일
                $eb_date        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 판매기간 종료일
                $buy_level        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 구매가능레벨
                $buy_only        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 가격공개
                $sc_type        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 배송비유형
                $sc_method        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 배송비결제
                $sc_amt            = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 기본배송비
                $sc_minimum        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 조건배송비
                $simg_type        = addslashes(trim(conv_number($data->sheets[0]['cells'][$i][$j++]))); // 이미지등록방식
                $simg1            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 소이미지
                $simg2            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지1
                $simg3            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지2
                $simg4            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지3
                $simg5            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지4
                $simg6            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 중이미지5
                $info_gubun        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 정보고시
                $info_value        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 정보고시 값
                $memo            = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 상세설명
                $admin_memo        = addslashes(trim($data->sheets[0]['cells'][$i][$j++])); // 관리자메모

                if (!$mb_id || !$gcode || !$gname) {
                    $fail_count++;
                    continue;
                }

                // 상품코드로 상품이 있는지 먼저 검사
                $sql2 = " select count(*) as cnt from shop_goods where gcode = '$gcode' ";
                $row2 = $this->query_model->returnOneArr($sql2);
                if (!$row2['cnt']) {
                    $fail_gcode[] = $gcode;
                    $fail_count++;
                    continue;
                }

                unset($value);
                $value['mb_id']            = $mb_id; // 판매자ID
                $value['gcode']            = $gcode; // 상품코드
                $value['ca_id']            = $ca_id; // 대표분류
                $value['ca_id2']        = $ca_id2; // 추가분류2
                $value['ca_id3']        = $ca_id3; // 추가분류3
                $value['gname']            = $gname; // 상품명
                $value['explan']        = $explan; // 짧은설명
                $value['keywords']        = $keywords; // 검색키워드
                $value['model']            = $model; // 모델명
                $value['brand_uid']        = $this->common_lib->get_brand_chk($brand_nm); // 브랜드주키
                $value['brand_nm']        = $brand_nm; // 브랜드명
                $value['notax']            = $notax; // 과세설정
                $value['zone']            = $zone; // 판매가능지역
                $value['zone_msg']        = $zone_msg; // 판매가능지역 추가설명
                $value['origin']        = $origin; // 원산지
                $value['maker']            = $maker; // 제조사
                $value['isopen']        = $isopen; // 판매여부
                $value['supply_price']    = $supply_price; // 공급가격
                $value['normal_price']    = $normal_price; // 시중가격
                $value['goods_price']    = $goods_price; // 판매가격
                $value['price_msg']        = $price_msg; // 가격대체문구
                $value['stock_mod']        = $stock_mod; // 재고적용타입
                $value['stock_qty']        = $stock_qty; // 재고수량
                $value['noti_qty']        = $noti_qty; // 재고통보수량
                $value['odr_min']        = $odr_min; // 최소주문한도
                $value['odr_max']        = $odr_max; // 최대주문한도
                $value['gpoint']        = $gpoint; // 포인트
                $value['sb_date']        = $sb_date; // 판매기간 시작일
                $value['eb_date']        = $eb_date; // 판매기간 종료일
                $value['buy_level']        = $buy_level; // 구매가능레벨
                $value['buy_only']        = $buy_only; // 가격공개
                $value['sc_type']        = $sc_type; // 배송비유형
                $value['sc_method']        = $sc_method; // 배송비결제
                $value['sc_amt']        = $sc_amt; // 기본배송비
                $value['sc_minimum']    = $sc_minimum; // 조건배송비
                $value['simg_type']        = $simg_type; // 이미지등록방식
                $value['simg1']            = $simg1; // 소이미지
                $value['simg2']            = $simg2; // 중이미지1
                $value['simg3']            = $simg3; // 중이미지2
                $value['simg4']            = $simg4; // 중이미지3
                $value['simg5']            = $simg5; // 중이미지4
                $value['simg6']            = $simg6; // 중이미지5
                $value['info_gubun']    = $info_gubun; // 상세설명
                $value['info_value']    = $info_value; // 상세설명
                $value['memo']            = $memo; // 상세설명
                $value['admin_memo']    = $admin_memo; // 관리자메모
                $value['update_time']    = $OM_TIME_YMDHISOM_TIME_YMDHIS; //수정일시
                $this->query_model->updateObj("shop_goods", $value, " gcode='$gcode'");
                $succ_count++;
            }
        }
        $fail_countTag = "";
        if ($fail_count > 0) {
            $fail_countTag = "
                <tr>
                    <th scope=\"row\">실패상품코드</th>
                    <td>" . implode(', ', $fail_gcode) . "</td>
                </tr>";
        }
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'total_count' => $total_count,
            'succ_count' => $succ_count,
            'fail_count' => $fail_count,
            'fail_countTag' => $fail_countTag
        );
        $this->load->view('admin/goods/xls_mod_update', $data);
    }
    private function goods_getprice($code, $headTitle, $headTitleEnd)
    {
        $returnJson = $this->goods_sub($code, $headTitle); #goods_sub가 들어가는 화면은 서브에서 먼저 headTitle이 출력이 되고, 본 함수의 끝에 headTitleEnd에 들어가야함
        $q1 = $returnJson['q1'];
        $btn_frmline = $returnJson['btn_frmline'];
        $code = $this->input->get('code');
        $page = $this->input->get('page');
        $total_count = $returnJson['total_count'];
        $page_rows = $returnJson['page_rows'];
        $q2 = $returnJson['q2'];
        $result = $returnJson['result'];
        $num = $returnJson['num'];
        $total_page = $returnJson['total_page'];

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);
        $rankTag = $this->gvcomplex_lib->subject_sort_link('rank', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $update_timeTag = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stock_qtyTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normal_priceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supply_priceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $goods_priceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);
        $gpointTag = $this->gvcomplex_lib->subject_sort_link('gpoint', $q2);


        $i = 0;
        $getpriceTable = "";
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);
            $getpriceTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['supply_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['gpoint']) . "</td>
                    <td rowspan=\"2\"><input type=\"text\" class=\"frm_input\" name=\"rank[{$i}]\" value=\"{$row['rank']}\"></td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $getpriceTable .= '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';


        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $data = array(
            'page_rowsOpt' => $page_rowsOpt,
            'requestUrl' => $requestUrl,
            'headTitleEnd' => $headTitleEnd,
            'q1' => $q1,
            'page' => $page,
            'btn_frmline' => $btn_frmline,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'rankTag' => $rankTag,
            'mb_idTag' => $mb_idTag,
            'update_timeTag' => $update_timeTag,
            'stock_qtyTag' => $stock_qtyTag,
            'normal_priceTag' => $normal_priceTag,
            'supply_priceTag' => $supply_priceTag,
            'goods_priceTag' => $goods_priceTag,
            'gpointTag' => $gpointTag,
            'getpriceTable' => $getpriceTable,
            'pagination' => $pagination,
            'total_count' => $total_count
        );
        $this->load->view('admin/goods/getprice', $data);
    }
    private function goods_getpoint($code, $headTitle, $headTitleEnd)
    {
        $returnJson = $this->goods_sub($code, $headTitle); #goods_sub가 들어가는 화면은 서브에서 먼저 headTitle이 출력이 되고, 본 함수의 끝에 headTitleEnd에 들어가야함
        $q1 = $returnJson['q1'];
        $btn_frmline = $returnJson['btn_frmline'];
        $code = $this->input->get('code');
        $page = $this->input->get('page');
        $total_count = $returnJson['total_count'];
        $page_rows = $returnJson['page_rows'];
        $q2 = $returnJson['q2'];
        $result = $returnJson['result'];
        $num = $returnJson['num'];
        $total_page = $returnJson['total_page'];

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);
        $rankTag = $this->gvcomplex_lib->subject_sort_link('rank', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $update_timeTag = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stock_qtyTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normal_priceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supply_priceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $goods_priceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);
        $gpointTag = $this->gvcomplex_lib->subject_sort_link('gpoint', $q2);
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');


        $getPointTable = "";
        $i = 0;
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);
            $getPointTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['supply_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['gpoint']) . "</td>
                    <td rowspan=\"2\"><input type=\"text\" class=\"frm_input\" name=\"rank[{$i}]\" value='{$row['rank']}'></td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $getPointTable .= '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';

        $data = array(
            'pagination' => $pagination,
            'headTitleEnd' => $headTitleEnd,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'rankTag' => $rankTag,
            'mb_idTag' => $mb_idTag,
            'update_timeTag' => $update_timeTag,
            'stock_qtyTag' => $stock_qtyTag,
            'normal_priceTag' => $normal_priceTag,
            'supply_priceTag' => $supply_priceTag,
            'goods_priceTag' => $goods_priceTag,
            'gpointTag' => $gpointTag,
            'q1' => $q1,
            'page' => $page,
            'getPointTable' => $getPointTable,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'requestUrl' => $requestUrl
        );
        $this->load->view('admin/goods/getpoint', $data);
    }
    private function goods_getuse($code, $headTitle, $headTitleEnd)
    {
        $returnJson = $this->goods_sub($code, $headTitle); #goods_sub가 들어가는 화면은 서브에서 먼저 headTitle이 출력이 되고, 본 함수의 끝에 headTitleEnd에 들어가야함
        $q1 = $returnJson['q1'];
        $btn_frmline = $returnJson['btn_frmline'];
        $code = $this->input->get('code');
        $page = $this->input->get('page');
        $total_count = $returnJson['total_count'];
        $page_rows = $returnJson['page_rows'];
        $q2 = $returnJson['q2'];
        $result = $returnJson['result'];
        $num = $returnJson['num'];
        $total_page = $returnJson['total_page'];

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);
        $rankTag = $this->gvcomplex_lib->subject_sort_link('rank', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $update_timeTag = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stock_qtyTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normal_priceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supply_priceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $goods_priceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);
        $gpointTag = $this->gvcomplex_lib->subject_sort_link('gpoint', $q2);
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $getUseTable = "";
        $i = 0;
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);
            $getUseTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['supply_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['gpoint']) . "</td>
                    <td rowspan=\"2\"><input type=\"text\" class=\"frm_input\" name=\"rank[{$i}]\" value=\"{$row['rank']}\"></td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $getUseTable .= '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';
        $isopenArr = array('', '', '', '', '');
        $isopenArr[1] = radio_checked('isopen', '1', '1', '진열');
        $isopenArr[2] = radio_checked('isopen', '', '2', '품절');
        $isopenArr[3] = radio_checked('isopen', '', '3', '단종');
        $isopenArr[4] = radio_checked('isopen', '', '4', '중지');
        $data = array(
            'pagination' => $pagination,
            'headTitleEnd' => $headTitleEnd,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'rankTag' => $rankTag,
            'mb_idTag' => $mb_idTag,
            'update_timeTag' => $update_timeTag,
            'stock_qtyTag' => $stock_qtyTag,
            'normal_priceTag' => $normal_priceTag,
            'supply_priceTag' => $supply_priceTag,
            'goods_priceTag' => $goods_priceTag,
            'gpointTag' => $gpointTag,
            'q1' => $q1,
            'page' => $page,
            'getUseTable' => $getUseTable,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'requestUrl' => $requestUrl,
            'isopenArr' => $isopenArr
        );
        $this->load->view('admin/goods/getuse', $data);
    }
    private function goods_getmove($code, $headTitle, $headTitleEnd)
    {
        $returnJson = $this->goods_sub($code, $headTitle); #goods_sub가 들어가는 화면은 서브에서 먼저 headTitle이 출력이 되고, 본 함수의 끝에 headTitleEnd에 들어가야함
        $q1 = $returnJson['q1'];
        $btn_frmline = $returnJson['btn_frmline'];
        $code = $this->input->get('code');
        $page = $this->input->get('page');
        $total_count = $returnJson['total_count'];
        $page_rows = $returnJson['page_rows'];
        $q2 = $returnJson['q2'];
        $result = $returnJson['result'];
        $num = $returnJson['num'];
        $total_page = $returnJson['total_page'];

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);
        $rankTag = $this->gvcomplex_lib->subject_sort_link('rank', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $update_timeTag = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stock_qtyTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normal_priceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supply_priceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $goods_priceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);
        $gpointTag = $this->gvcomplex_lib->subject_sort_link('gpoint', $q2);
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $getMoveTable = "";
        $i = 0;
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);
            $getMoveTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['supply_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['gpoint']) . "</td>
                    <td rowspan=\"2\"><input type=\"text\" class=\"frm_input\" name=\"rank[{$i}]\" value='{$row['rank']}'></td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $getMoveTable .= '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';

        $get_cate = array('', '', '', '', '', '');
        $get_cate[1] = $this->funcomplex_lib->get_category_select_1('j_ca1', '');
        $get_cate[2] = $this->funcomplex_lib->get_category_select_2('j_ca2', '');
        $get_cate[3] = $this->funcomplex_lib->get_category_select_3('j_ca3', '');
        $get_cate[4] = $this->funcomplex_lib->get_category_select_4('j_ca4', '');
        $get_cate[5] = $this->funcomplex_lib->get_category_select_5('j_ca5', '');

        $m_cate = array('', '', '', '', '', '');
        $m_cate[1] = $this->funcomplex_lib->get_category_select_1('m_ca1', '');
        $m_cate[2] = $this->funcomplex_lib->get_category_select_2('m_ca2', '');
        $m_cate[3] = $this->funcomplex_lib->get_category_select_3('m_ca3', '');
        $m_cate[4] = $this->funcomplex_lib->get_category_select_4('m_ca4', '');
        $m_cate[5] = $this->funcomplex_lib->get_category_select_5('m_ca5', '');
        $seleHelp = help("※ 분류연결시 해당 상품에 연결되어있는 분류를 포함해, 새롭게 연결되는 분류를 추가합니다. (최대 3개)");
        $moveHelp = help("※ 분류이동시 해당 상품에 연결된 분류는 모두 해제되며, 새롭게 이동되는 분류만 연결됩니다.");
        $data = array(
            'pagination' => $pagination,
            'headTitleEnd' => $headTitleEnd,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'rankTag' => $rankTag,
            'mb_idTag' => $mb_idTag,
            'update_timeTag' => $update_timeTag,
            'stock_qtyTag' => $stock_qtyTag,
            'normal_priceTag' => $normal_priceTag,
            'supply_priceTag' => $supply_priceTag,
            'goods_priceTag' => $goods_priceTag,
            'gpointTag' => $gpointTag,
            'q1' => $q1,
            'page' => $page,
            'getMoveTable' => $getMoveTable,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'requestUrl' => $requestUrl,
            'get_cate' => $get_cate,
            'seleHelp' => $seleHelp,
            'm_cate' => $m_cate,
            'moveHelp' => $moveHelp
        );
        $this->load->view('admin/goods/getmove', $data);
    }
    private function goods_getbrand($code, $headTitle, $headTitleEnd)
    {
        $returnJson = $this->goods_sub($code, $headTitle); #goods_sub가 들어가는 화면은 서브에서 먼저 headTitle이 출력이 되고, 본 함수의 끝에 headTitleEnd에 들어가야함
        $q1 = $returnJson['q1'];
        $btn_frmline = $returnJson['btn_frmline'];
        $code = $this->input->get('code');
        $page = $this->input->get('page');
        $total_count = $returnJson['total_count'];
        $page_rows = $returnJson['page_rows'];
        $q2 = $returnJson['q2'];
        $result = $returnJson['result'];
        $num = $returnJson['num'];
        $total_page = $returnJson['total_page'];

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);
        $rankTag = $this->gvcomplex_lib->subject_sort_link('rank', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $update_timeTag = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stock_qtyTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normal_priceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supply_priceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $goods_priceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);
        $gpointTag = $this->gvcomplex_lib->subject_sort_link('gpoint', $q2);
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $getBrandTable = "";
        $i = 0;
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);
            $getBrandTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['supply_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['gpoint']) . "</td>
                    <td rowspan=\"2\"><input type=\"text\" class=\"frm_input\" name=\"rank[{$i}]\" value='{$row['rank']}'></td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">{$row['brand_nm']}</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0) {
            $getBrandTable .= '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';
        }
        $brandStr = "";
        $sql = "select * from shop_brand where br_user_yes='0' order by br_name asc ";
        $res = $this->query_model->returnArr($sql);
        $brandStr .= option_selected('', '', '선택');
        foreach ($res as $key => $row) {
            $brandStr .= option_selected($row['br_id'], '', $row['br_name']);
        }
        $data = array(
            'pagination' => $pagination,
            'headTitleEnd' => $headTitleEnd,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'rankTag' => $rankTag,
            'mb_idTag' => $mb_idTag,
            'update_timeTag' => $update_timeTag,
            'stock_qtyTag' => $stock_qtyTag,
            'normal_priceTag' => $normal_priceTag,
            'supply_priceTag' => $supply_priceTag,
            'goods_priceTag' => $goods_priceTag,
            'gpointTag' => $gpointTag,
            'q1' => $q1,
            'page' => $page,
            'getBrandTable' => $getBrandTable,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'requestUrl' => $requestUrl,
            'brandStr' => $brandStr
        );
        $this->load->view('admin/goods/getbrand', $data);
    }
    private function goods_getdelivery($code, $headTitle, $headTitleEnd)
    {
        $returnJson = $this->goods_sub($code, $headTitle); #goods_sub가 들어가는 화면은 서브에서 먼저 headTitle이 출력이 되고, 본 함수의 끝에 headTitleEnd에 들어가야함
        $q1 = $returnJson['q1'];
        $btn_frmline = $returnJson['btn_frmline'];
        $code = $this->input->get('code');
        $page = $this->input->get('page');
        $total_count = $returnJson['total_count'];
        $page_rows = $returnJson['page_rows'];
        $q2 = $returnJson['q2'];
        $result = $returnJson['result'];
        $num = $returnJson['num'];
        $total_page = $returnJson['total_page'];

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);
        $rankTag = $this->gvcomplex_lib->subject_sort_link('rank', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $update_timeTag = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stock_qtyTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normal_priceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supply_priceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $goods_priceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);
        $gpointTag = $this->gvcomplex_lib->subject_sort_link('gpoint', $q2);
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $getDeliverTable = "";
        $i = 0;
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);
            $getDeliverTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['supply_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['gpoint']) . "</td>
                    <td rowspan=\"2\"><input type=\"text\" class=\"frm_input\" name=\"rank[{$i}]\" value='{$row['rank']}'></td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $getDeliverTable .= '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';

        $sc_type_arr = array('', '', '', '');
        $sc_type_arr[0] = option_selected('0', '', '공통설정');
        $sc_type_arr[1] = option_selected('1', '', '무료배송');
        $sc_type_arr[2] = option_selected('2', '', '조건부무료배송');
        $sc_type_arr[3] = option_selected('3', '', '유료배송');

        $sc_method_arr = array('', '', '');
        $sc_method_arr[0] = option_selected('0', '', '선불');
        $sc_method_arr[1] = option_selected('1', '', '착불');
        $sc_method_arr[2] = option_selected('2', '', '사용자선택');

        $qZoneArr = array('', '', '', '', '', '', '', '', '', '');
        $qZoneArr[0] = option_selected('',  '', '전체');
        $qZoneArr[1] = option_selected('전국', '', '전국');
        $qZoneArr[2] = option_selected('강원도', '', '강원도');
        $qZoneArr[3] = option_selected('경기도', '', '경기도');
        $qZoneArr[4] = option_selected('경상도', '', '경상도');
        $qZoneArr[5] = option_selected('서울/경기도', '', '서울/경기도');
        $qZoneArr[6] = option_selected('서울특별시', '', '서울특별시');
        $qZoneArr[7] = option_selected('전라도', '', '전라도');
        $qZoneArr[8] = option_selected('제주도', '', '제주도');
        $qZoneArr[9] = option_selected('충청도', '', '충청도');
        $data = array(
            'pagination' => $pagination,
            'headTitleEnd' => $headTitleEnd,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'rankTag' => $rankTag,
            'mb_idTag' => $mb_idTag,
            'update_timeTag' => $update_timeTag,
            'stock_qtyTag' => $stock_qtyTag,
            'normal_priceTag' => $normal_priceTag,
            'supply_priceTag' => $supply_priceTag,
            'goods_priceTag' => $goods_priceTag,
            'gpointTag' => $gpointTag,
            'q1' => $q1,
            'page' => $page,
            'getDeliverTable' => $getDeliverTable,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'requestUrl' => $requestUrl,
            'sc_type_arr' => $sc_type_arr,
            'sc_method_arr' => $sc_method_arr,
            'qZoneArr' => $qZoneArr
        );
        $this->load->view('admin/goods/getdelivery', $data);
    }
    private function goods_getbuylevel($code, $headTitle, $headTitleEnd)
    {
        $returnJson = $this->goods_sub($code, $headTitle); #goods_sub가 들어가는 화면은 서브에서 먼저 headTitle이 출력이 되고, 본 함수의 끝에 headTitleEnd에 들어가야함
        $q1 = $returnJson['q1'];
        $btn_frmline = $returnJson['btn_frmline'];
        $code = $this->input->get('code');
        $page = $this->input->get('page');
        $total_count = $returnJson['total_count'];
        $page_rows = $returnJson['page_rows'];
        $q2 = $returnJson['q2'];
        $result = $returnJson['result'];
        $num = $returnJson['num'];
        $total_page = $returnJson['total_page'];

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);
        $rankTag = $this->gvcomplex_lib->subject_sort_link('rank', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $update_timeTag = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stock_qtyTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normal_priceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supply_priceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $goods_priceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);
        $gpointTag = $this->gvcomplex_lib->subject_sort_link('gpoint', $q2);
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $getBuyLevelTable = "";
        $i = 0;
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);
            $getBuyLevelTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['supply_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['gpoint']) . "</td>
                    <td rowspan=\"2\"><input type=\"text\" class=\"frm_input\" name=\"rank[{$i}]\" value='{$row['rank']}'></td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $getBuyLevelTable .= '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';

        $get_goods_level_select = $this->common_lib->get_goods_level_select('buy_level', '');
        $data = array(
            'pagination' => $pagination,
            'headTitleEnd' => $headTitleEnd,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'rankTag' => $rankTag,
            'mb_idTag' => $mb_idTag,
            'update_timeTag' => $update_timeTag,
            'stock_qtyTag' => $stock_qtyTag,
            'normal_priceTag' => $normal_priceTag,
            'supply_priceTag' => $supply_priceTag,
            'goods_priceTag' => $goods_priceTag,
            'gpointTag' => $gpointTag,
            'q1' => $q1,
            'page' => $page,
            'getBuyLevelTable' => $getBuyLevelTable,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'requestUrl' => $requestUrl,
            'get_goods_level_select' => $get_goods_level_select
        );
        $this->load->view('admin/goods/getbuylevel', $data);
    }
    private function goods_qa($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $sst = $this->input->get('sst');

        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_goods_qa ";
        $sql_search = " where (left(seller_id,3)='AP-' or seller_id = 'admin') ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if ($sst) {
            $sql_search .= " and iq_ty = '$sst' ";
        }

        if (!$orderby) {
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
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $btn_frmline = '<input type="submit" name="act_button" value="선택삭제" class="btn_lsmall bx-white" onclick="document.pressed=this.value">';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $sstArr = array('', '', '', '', '');
        $sstArr[0] = get_selected($sst, '상품');
        $sstArr[1] = get_selected($sst, '배송');
        $sstArr[2] = get_selected($sst, '반품/환불/취소');
        $sstArr[3] = get_selected($sst, '교환/변경');
        $sstArr[4] = get_selected($sst, '기타');

        $sflArr = array('', '', '', '', '', '');
        $sflArr[0] = get_selected($sfl, 'iq_name');
        $sflArr[1] = get_selected($sfl, 'iq_email');
        $sflArr[2] = get_selected($sfl, 'iq_hp');
        $sflArr[3] = get_selected($sfl, 'iq_subject');
        $sflArr[4] = get_selected($sfl, 'iq_question');
        $sflArr[5] = get_selected($sfl, 'iq_answer');

        $iqTyTag = $this->gvcomplex_lib->subject_sort_link('iq_ty', $q2);
        $iqSubject = $this->gvcomplex_lib->subject_sort_link('iq_subject', $q2);
        $iqName = $this->gvcomplex_lib->subject_sort_link('iq_name', $q2);
        $iqTime = $this->gvcomplex_lib->subject_sort_link('iq_time', $q2);
        $iqReply = $this->gvcomplex_lib->subject_sort_link('iq_reply', $q2);

        $qaTable = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $iq_id = $row['iq_id'];

            $iq_name = $this->funcomplex_lib->get_sideview($row['mb_id'], $row['iq_name']);
            $iq_url = "code=qa_form&w=u&iq_id=$iq_id$qstr&page=$page";
            $iq_upd = "<a href=\"" . base_url() . "admin/goods?{$iq_url}\" class=\"btn_small\">수정</a>";
            $iq_subject = "<a href=\"" . base_url() . "admin/goods?{$iq_url}\">" . cut_str(htmlspecialchars($row['iq_subject']), 50) . "</a>";

            if ($i == 0)
                $qaTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $qaTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"iq_id[{$i}]\" value=\"{$iq_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td>{$row['iq_ty']}</td>
                    <td class=\"tal\">{$iq_subject}</td>
                    <td>{$iq_name}</a></td>
                    <td>" . substr($row['iq_time'], 0, 10) . "</td>
                    <td>" . ($row['iq_answer'] ? 'yes' : '') . "</td>
                    <td>{$iq_upd}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $qaTable .= '<tbody><tr><td colspan="8" class="empty_table">자료가 없습니다.</td></tr>';

        $data = array(
            'btn_frmline' => $btn_frmline,
            'code' => $code,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'pagination' => $pagination,
            'sstArr' => $sstArr,
            'sflArr' => $sflArr,
            'stx' => $stx,
            'iqTyTag' => $iqTyTag,
            'iqSubject' => $iqSubject,
            'iqName' => $iqName,
            'iqTime' => $iqTime,
            'iqReply' => $iqReply,
            'qaTable' => $qaTable
        );
        $this->load->view('admin/goods/qa', $data);
    }
    private function goods_qa_form($headTitle, $headTitleEnd)
    {
        $w = $this->input->get('w');
        $sst = $this->input->get('sst');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $page = $this->input->get('page');

        $qstr = $this->load->get_var('qstr');

        $iq_id = $this->input->get('iq_id');
        $sql1 = "select * from shop_goods_qa where iq_id = ?";
        $param = array($iq_id);
        $iq = $this->query_model->returnOneArr($sql1, $param);
        $sql2 = "select * from shop_goods where index_no = ?";
        $param2 = array($iq['gs_id']);
        $gs = $this->query_model->returnOneArr($sql2, $param2);

        if (!$iq['iq_id'])
            alert("자료가 존재하지 않습니다.");

        $allTag = get_selected($iq['iq_ty'], '');
        $gsTag = get_selected($iq['iq_ty'], '상품');
        $delTag = get_selected($iq['iq_ty'], '배송');
        $returnTag = get_selected($iq['iq_ty'], '반품/환불/취소');
        $changeTag = get_selected($iq['iq_ty'], '교환/변경');
        $ectTag = get_selected($iq['iq_ty'], '기타');
        $secretTag = get_checked($iq['iq_secret'], '1');
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'w' => $w,
            'sst' => $sst,
            'stx' => $stx,
            'sfl' => $sfl,
            'page' => $page,
            'iq' => $iq,
            'gs' => $gs,
            'iq_id' => $iq_id,
            'allTag' => $allTag,
            'gsTag' => $gsTag,
            'delTag' => $delTag,
            'returnTag' => $returnTag,
            'changeTag' => $changeTag,
            'ectTag' => $ectTag,
            'secretTag' => $secretTag,
            'qstr' => $qstr
        );
        $this->load->view('admin/goods/qa_form', $data);
    }
    private function goods_review($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_goods_review ";
        $sql_search = " where (1) ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if (!$orderby) {
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
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $btn_frmline = ' <input type="submit" name="act_button" value="선택삭제" class="btn_lsmall bx-white" onclick="document.pressed=this.value">';
        $mb_idTag = option_selected('mb_id', $sfl, '작성자');
        $seller_idTag = option_selected('seller_id', $sfl, '판매자');

        $seller_idTagLink = $this->gvcomplex_lib->subject_sort_link('seller_id', $q2);
        $mb_idTagLink = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $reg_timeTagLink = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $scoreTagLink = $this->gvcomplex_lib->subject_sort_link('score', $q2);

        $i = 0;
        $reviewTable = "";
        foreach ($result as $key => $row) {
            $gs = $this->global_lib->get_goods($row['gs_id'], 'simg1, gname');

            if ($i == 0)
                $reviewTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $reviewTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"index_no[{$i}]\" value=\"{$row['index_no']}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td><a href=\"" . base_url() . "shop/view?index_no={$row['gs_id']}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($row['gs_id'], $gs['simg1'], 40, 40) . "</a></td>
                    <td class=\"tal\"><a href=\"" . base_url() . "shop/view?index_no={$row['gs_id']}\" target=\"_blank\"><b>" . $this->funcomplex_lib->get_text($gs['gname']) . "</b></a><p class=\"mart5 fc_137\">" . $this->funcomplex_lib->get_text($row['memo']) . "</p></td>
                    <td>{$row['seller_id']}</td>
                    <td>{$row['mb_id']}</td>
                    <td>" . substr($row['reg_time'], 0, 10) . "</td>
                    <td><img src=\"" . base_url() . "img/sub/score_{$row['score']}.gif\"></td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $reviewTable .= '<tbody><tr><td colspan="8" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'btn_frmline' => $btn_frmline,
            'mb_idTag' => $mb_idTag,
            'seller_idTag' => $seller_idTag,
            'stx' => $stx,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'seller_idTagLink' => $seller_idTagLink,
            'mb_idTagLink' => $mb_idTagLink,
            'reg_timeTagLink' => $reg_timeTagLink,
            'scoreTagLink' => $scoreTagLink,
            'reviewTable' => $reviewTable,
            'pagination' => $pagination
        );
        $this->load->view('admin/goods/review', $data);
    }
    private function goods_gift($code, $headTitle, $headTitleEnd)
    {
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $code = $this->input->get('code');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $sst = $this->input->get('sst');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_gift_group ";
        $sql_search = " where (1) ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if (isset($sst) && is_numeric($sst))
            $sql_search .= " and gr_ty = '$sst' ";

        if ($fr_date && $to_date)
            $sql_search .= " and (gr_sdate >= '$fr_date' and gr_edate <= '$to_date') ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and (gr_sdate >= '$fr_date' and gr_sdate <= '$fr_date') ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and (gr_edate >= '$to_date' and gr_edate <= '$to_date') ";

        if (!$orderby) {
            $filed = "gr_wdate";
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
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "admin/member?code=gift_form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 쿠폰등록</a>";

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $sflArr = array('', '', '');
        $sflArr[0] = option_selected('gr_subject', $sfl, '쿠폰명');
        $sflArr[1] = option_selected('gr_explan', $sfl, '설명');
        $sflArr[2] = option_selected('gr_id', $sfl, '일련번호');
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $sstArr = array('', '', '');
        $sstArr[0] = option_selected('', $sst, '구분');
        $sstArr[1] = option_selected('0', $sst, '숫자발행');
        $sstArr[2] = option_selected('1', $sst, '영문, 숫자 혼합발행');

        $gr_idTag = $this->gvcomplex_lib->subject_sort_link('gr_id', $q2);
        $gr_sdateTag = $this->gvcomplex_lib->subject_sort_link('gr_sdate', $q2);
        $gr_edateTag = $this->gvcomplex_lib->subject_sort_link('gr_edate', $q2);
        $gr_priceTag = $this->gvcomplex_lib->subject_sort_link('gr_price', $q2);

        $i = 0;
        $giftTable = "";
        foreach ($result as $key => $row) {
            $gr_id = $row['gr_id'];

            // 발행매수
            $sql1 = " select COUNT(*) as cnt from shop_gift where gr_id=?";
            $param1 = array($gr_id);
            $row1 = $this->query_model->returnOneArr($sql1, $param1);

            // 사용매수
            $sql2 = " select COUNT(*) as cnt from shop_gift where gr_id=? and gi_use='1' ";
            $row2 = $this->query_model->returnOneArr($sql2, $param1);

            // 발행금액
            $sql3 = " select SUM(gr_price) as total from shop_gift where gr_id=?";
            $row3 = $this->query_model->returnOneArr($sql3, $param1);

            // 사용금액
            $sql4 = " select SUM(gr_price) as total from shop_gift where gr_id=? and gi_use='1' ";
            $row4 = $this->query_model->returnOneArr($sql4, $param1);

            $s_upd = "<a href='" . base_url() . "admin/member?code=gift_form&w=u&gr_id=$gr_id$qstr&page=$page' class=\"btn_small\">수정</a>";

            $s_cert = "<a href='" . base_url() . "admin/goods_gift_serial?gr_id=$gr_id' onclick=\"win_open(this,'pop_serial','700','630','yes');return false\" class=\"btn_small grey\">인증번호</a>";

            if ($i == 0)
                $giftTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $giftTable .= "
                <tr class=\"{$bg}\">
                    <td>			
                        <input type=\"hidden\" name=\"gr_id[{$i}]\" value=\"{$gr_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td>{$gr_id}</td>
                    <td>{$row['gr_sdate']}</td>
                    <td>{$row['gr_edate']}</td>
                    <td class=\"tar bold\">" . number_format($row['gr_price']) . "</td>
                    <td>" . number_format($row1['cnt']) . "</td>
                    <td>" . number_format($row2['cnt']) . "</td>
                    <td class=\"tar\">" . number_format($row3['total']) . "</td>
                    <td class=\"tar\">" . number_format($row4['total']) . "</td>
                    <td>{$s_cert}</td>
                    <td>{$s_upd}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $giftTable .= '<tbody><tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';


        $data = array(
            'code' => $code,
            'stx' => $stx,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'pagination' => $pagination,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'sflArr' => $sflArr,
            'get_search_date' => $get_search_date,
            'sstArr' => $sstArr,
            'gr_idTag' => $gr_idTag,
            'gr_sdateTag' => $gr_sdateTag,
            'gr_edateTag' => $gr_edateTag,
            'gr_priceTag' => $gr_priceTag,
            'giftTable' => $giftTable
        );
        $this->load->view('admin/goods/gift', $data);
    }
    private function goods_gift_form($headTitle, $headTitleEnd)
    {
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $w = $this->input->get('w');
        $gr_id = $this->input->get('gr_id');
        $page = $this->input->get('page');
        $qstr = $this->load->get_var('qstr');
        $group = array();
        if ($w == "") {
            $gr_id = substr(strtoupper(uniqid()), -12);
        } else if ($w == "u") {
            $sql = "select * from shop_gift_group where gr_id =?";
            $param = array($gr_id);
            $group = $this->query_model->returnOneArr($sql, $param);
            if (!$group['gr_id'])
                alert("존재하지 않은 쿠폰 입니다.");
        }


        $frm_submit = '<div class="btn_confirm">
                <input type="submit" value="저장" class="btn_large" accesskey="s">
                <a href="' . base_url() . 'admin/member?code=gift' . $qstr . '&page=' . $page . '" class="btn_large bx-white">목록</a>';
        if ($w == 'u') {
            $frm_submit .= '<a href="' . base_url() . 'admin/member?code=gift_form" class="btn_large bx-red">추가</a>';
        }
        $frm_submit .= '</div>';
        $group['gr_subject'] = isset($group['gr_subject']) ? $group['gr_subject'] : "";
        $group['gr_explan'] = isset($group['gr_explan']) ? $group['gr_explan'] : "";
        $group['gr_price'] = isset($group['gr_price']) ? $group['gr_price'] : "";
        $group['gr_quant'] = isset($group['gr_quant']) ? $group['gr_quant'] : "";
        $group['gr_sdate'] = isset($group['gr_sdate']) ? $group['gr_sdate'] : "";
        $group['gr_edate'] = isset($group['gr_edate']) ? $group['gr_edate'] : "";
        $gr_quantTag = "";
        if ($w == "") {
            $gr_quantTag = "
                <tr>
                    <th scope=\"row\">발행매수</th>
                    <td><input type=\"text\" name=\"gr_quant\" value=\"{$group['gr_quant']}\"" . (($w == "") ? 'required' : '') . " class=\"frm_input" . (($w == "") ? ' required' : '') . " w80\"> 매</td>
                </tr>";
        }
        $use_giftTag = "";
        if ($w == "") {
            $use_giftTag = "
                <tr>
                    <th scope=\"row\">발행방식</th>
                    <td class=\"td_label\">
                        <label><input type=\"radio\" name=\"use_gift\" value=\"0\" checked> 숫자로만 발행</label>
                        <label><input type=\"radio\" name=\"use_gift\" value=\"1\"> 영문, 숫자 혼합해서 발행</label>
                    </td>
                </tr>";
        }


        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'w' => $w,
            'gr_id' => $gr_id,
            'page' => $page,
            'qstr' => $qstr,
            'group' => $group,
            'gr_quantTag' => $gr_quantTag,
            'use_giftTag' => $use_giftTag,
            'frm_submit' => $frm_submit
        );
        $this->load->view('admin/goods/gift_form', $data);
    }
    private function goods_coupon($code, $headTitle, $headTitleEnd)
    {
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);


        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_coupon ";
        $sql_search = " where (1) ";

        if ($sfl && $stx) {
            $sql_search .= " and ($sfl like '%$stx%') ";
        }

        if ($fr_date && $to_date)
            $sql_search .= " and (cp_pub_sdate >= '$fr_date' and cp_pub_edate <= '$to_date') ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and (cp_pub_sdate >= '$fr_date' and cp_pub_sdate <= '$fr_date') ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and (cp_pub_edate >= '$to_date' and cp_pub_edate <= '$to_date') ";

        if (!$orderby) {
            $filed = "cp_wdate";
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
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);


        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "admin/member?code=coupon_form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 쿠폰등록</a>";

        $gw_type = array(
            "0" => "발행 날짜 지정",
            "1" => "발행 시간/요일 지정",
            "2" => "성별구분으로 발급",
            "3" => "회원 생일자 발급",
            "4" => "연령 구분으로 발급",
            "5" => "신규회원가입 발급"
        );
        $cp_subjectTag = option_selected('cp_subject', $sfl, '쿠폰명');
        $cp_explanTag = option_selected('cp_explan', $sfl, '설명');
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $cp_typeTag = $this->gvcomplex_lib->subject_sort_link('cp_type', $q2);
        $cp_useTag = $this->gvcomplex_lib->subject_sort_link('cp_use', $q2);


        $couponTable = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $cp_id = $row['cp_id'];

            $s_upd = "<a href='" . base_url() . "admin/member?code=coupon_form&w=u&cp_id=$cp_id$qstr&page=$page' class=\"btn_small\">수정</a>";

            switch ($row['cp_type']) {
                case '3':
                    $sdate = $row['cp_pub_sday'] . '일';
                    $edate = $row['cp_pub_eday'] . '일';
                    break;
                default:
                    if ($row['cp_pub_sdate'] == '9999999999')
                        $sdate = '무제한';
                    else
                        $sdate = str_replace("-", ".", $row['cp_pub_sdate']);

                    if ($row['cp_pub_edate'] == '9999999999')
                        $edate = '무제한';
                    else
                        $edate = str_replace("-", ".", $row['cp_pub_edate']);
                    break;
            }
            // 발행매수
            $sql = "select count(cp_id) as cnt from shop_coupon_log where cp_id=?";
            $param = array($cp_id);
            $log = $this->query_model->returnOneArr($sql, $param);

            if ($i == 0)
                $couponTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $couponTable .= "
                <tr class=\"{$bg}\">
                    <td>			
                        <input type=\"hidden\" name=\"cp_id[{$i}]\" value=\"{$cp_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td class=\"tal\">
                        <p class=\"fc_255\">{$gw_type[$row['cp_type']]}</p>
                        <p class=\"mart2\">" . $this->funcomplex_lib->get_text(cut_str($row['cp_subject'], 40)) . "</p>
                    </td>
                    <td>{$sdate}</td>
                    <td>{$edate}</td>
                    <td>" . number_format($log['cnt']) . "</td>
                    <td>" . number_format($row['cp_odr_cnt']) . "</td>
                    <td>" . ($row['cp_use'] ? 'yes' : '') . "</td>
                    <td>{$s_upd}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $couponTable .= '<tbody><tr><td colspan="9" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $data = array(
            'headTitle' => $headTitle,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'headTitleEnd' => $headTitleEnd,
            'code' => $code,
            'cp_subjectTag' => $cp_subjectTag,
            'cp_explanTag' => $cp_explanTag,
            'stx' => $stx,
            'get_search_date' => $get_search_date,
            'q1' => $q1,
            'page' => $page,
            'btn_frmline' => $btn_frmline,
            'total_count' => $total_count,
            'cp_typeTag' => $cp_typeTag,
            'cp_useTag' => $cp_useTag,
            'couponTable' => $couponTable,
            'pagination' => $pagination
        );
        $this->load->view('admin/goods/coupon', $data);
    }
    private function goods_coupon_form($headTitle, $headTitleEnd)
    {
        $jqueryUiDatePick = $this->admin_template->datePicker();

        $qstr = $this->load->get_var('qstr');

        $cp_id = $this->input->get('cp_id');
        $page = $this->input->get('page');
        $w = $this->input->get('w');
        $sql = "select * from shop_coupon where cp_id = ?";
        $param = array($cp_id);
        $coupon = $this->query_model->returnOneArr($sql, $param);
        $ck_join = $this->query_model->returnOneArr("select * from shop_coupon where cp_type = '5'");
        $arr_dlimit = array();
        $arr_category = array();
        $cnt_use_goods = 0;
        $ck_use_goods = "";
        $arr_dlevel = array('', '', '', '', '');

        if ($w == "") {
            $coupon['cp_use']            = 1;
            $coupon['cp_download']        = 0;
            $coupon['cp_overlap']        = 0;
            $coupon['cp_sale_type']        = 0;
            $coupon['cp_dups']            = 0;
            $coupon['cp_sale_percent']    = 0;
            $coupon['cp_sale_amt_max']    = 0;
            $coupon['cp_sale_amt']        = 0;
            $coupon['cp_inv_type']        = 0;
            $coupon['cp_low_amt']        = 0;
            $coupon['cp_pub_1_cnt']        = 0;
            $coupon['cp_pub_2_cnt']        = 0;
            $coupon['cp_pub_3_cnt']        = 0;
        } else if ($w == "u") {
            if (!$coupon['cp_id'])
                alert("존재하지 않은 쿠폰 입니다.");

            $qstr .= "&page=$page";

            // 쿠폰 (예외) 카테고리
            $arr_category = explode(',', $coupon['cp_use_category']);

            // 쿠폰 (예외) 상품
            if ($coupon['cp_use_goods']) {
                $arr_use_goods = explode(',', $coupon['cp_use_goods']);
                foreach ($arr_use_goods as $gs_id) {
                    $gs = $this->global_lib->get_goods($gs_id);
                    $gs_img = $this->funcomplex_lib->get_it_image_url($gs_id, $gs['simg1'], 40, 40);
                    $gs_name = cut_str($gs['gname'], 70);
                    $gs_price = number_format($gs['goods_price']);

                    if ($gs['index_no']) {
                        $ck_use_goods .= "<li id=\"chk_{$gs_id}\">";
                        $ck_use_goods .= "<a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\"><img src=\"{$gs_img}\" class=\"pr_img\"></a>";
                        $ck_use_goods .= "<p>{$gs_name}</p><p class=\"mart5 bold\">{$gs_price}원</p>";
                        $ck_use_goods .= "<a href=\"javascript:chk_del('{$gs_id}');\" class=\"bt_del\"><img src=\"" . base_url() . "admin/img/bt_delete.gif\"></a>";
                        $ck_use_goods .= "</li>";

                        $cnt_use_goods++;
                    }
                }
            }

            // 다운로드 (누적제한, 레벨제한)

            for ($i = 0; $i < 5; $i++) {
                if ($coupon['cp_type'] == $i) {
                    $arr_dlimit[$i] = $coupon['cp_dlimit'];
                    $arr_dlevel[$i] = $coupon['cp_dlevel'];
                    break;
                }
            }
        }
        $coupon['cp_dlimit'] = isset($coupon['cp_dlimit']) ? $coupon['cp_dlimit'] : "";
        $coupon['cp_dlevel'] = isset($coupon['cp_dlevel']) ? $coupon['cp_dlevel'] : "";
        $frm_submit = '<div class="btn_confirm">
                <input type="submit" value="저장" class="btn_large" accesskey="s">
                <a href="' . base_url() . 'admin/member?code=coupon' . $qstr . '" class="btn_large bx-white">목록</a>';
        if ($w == 'u') {
            $frm_submit .= '<a href="' . base_url() . 'admin/member?code=coupon_form" class="btn_large bx-red">추가</a>';
        }
        $frm_submit .= '</div>';
        $w = $this->input->get('w');
        $sst = $this->input->get('sst');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $page = $this->input->get('page');
        $cp_id = $this->input->get('cp_id');
        $coupontType = array('', '');
        $couponCheck = array('', '');
        $get_goods_level_select = array('', '');

        $coupontType[0] = (isset($coupon['cp_type']) && $coupon['cp_type'] == '0') ? 'list1' : 'list0';
        $couponCheck[0] = (isset($coupon['cp_type']) && $coupon['cp_type'] == '0') ? ' checked' : '';
        $get_goods_level_select[0] = $this->common_lib->get_goods_level_select('cp_dlevel0', $arr_dlevel[0]);
        $coupontType[1] = (isset($coupon['cp_type']) && $coupon['cp_type'] == '1') ? 'list1' : 'list0';
        $couponCheck[1] = (isset($coupon['cp_type']) && $coupon['cp_type'] == '1') ? ' checked' : '';
        $get_goods_level_select[1] = $this->common_lib->get_goods_level_select('cp_dlevel1', $arr_dlevel[1]);

        $coupontType[2] = (isset($coupon['cp_type']) && $coupon['cp_type'] == '2') ? 'list1' : 'list0';
        $couponCheck[2] = (isset($coupon['cp_type']) && $coupon['cp_type'] == '2') ? ' checked' : '';
        $get_goods_level_select[2] = $this->common_lib->get_goods_level_select('cp_dlevel2', $arr_dlevel[2]);

        $coupontType[3] = (isset($coupon['cp_type']) && $coupon['cp_type'] == '3') ? 'list1' : 'list0';
        $couponCheck[3] = (isset($coupon['cp_type']) && $coupon['cp_type'] == '3') ? ' checked' : '';
        $get_goods_level_select[3] = $this->common_lib->get_goods_level_select('cp_dlevel3', $arr_dlevel[3]);

        $coupontType[4] = (isset($coupon['cp_type']) && $coupon['cp_type'] == '4') ? 'list1' : 'list0';
        $couponCheck[4] = (isset($coupon['cp_type']) && $coupon['cp_type'] == '4') ? ' checked' : '';
        $get_goods_level_select[4] = $this->common_lib->get_goods_level_select('cp_dlevel4', $arr_dlevel[4]);

        $coupontType[5] = (isset($coupon['cp_type']) && $coupon['cp_type'] == '5') ? 'list1' : 'list0';
        $couponCheck[5] = (isset($coupon['cp_type']) && $coupon['cp_type'] == '5') ? ' checked' : '';

        $couponUpdateTag = "";
        if ($w == "u") {
            $couponUpdateTag .= "
                <tr>
                    <th scope=\"row\">쿠폰 생성일</th>
                    <td class=\"fc_7d6 bold\">{$coupon['cp_wdate']}</td>
                </tr>";
            if (!is_null_time($coupon['cp_udate'], 0, 1)) {
                $couponUpdateTag .= "
                    <tr>
                        <th scope=\"row\">최근 수정일</th>
                        <td class=\"fc_7d6 bold\">{$coupon['cp_udate']}</td>
                    </tr>";
            }
        }
        $cp_subjectTag = (isset($coupon['cp_subject'])) ? $this->funcomplex_lib->get_text($coupon['cp_subject']) : "";
        $cp_explanTag = (isset($coupon['cp_explan'])) ? $this->funcomplex_lib->get_text($coupon['cp_explan']) : "";
        $cp_useArr = array('', '');
        $cp_useArr[0] = ($coupon['cp_use'] == '0') ? ' checked' : '';
        $cp_useArr[1] = ($coupon['cp_use'] == '1') ? ' checked' : '';

        $cp_downArr = array('', '');
        $cp_downArr[0] = ($coupon['cp_download'] == '0') ? ' checked' : '';
        $cp_downArr[1] = ($coupon['cp_download'] == '1') ? ' checked' : '';
        $cp_downArr[2] = ($coupon['cp_download'] == '2') ? ' checked' : '';

        $cp_overlapArr = array('', '');
        $cp_overlapArr[0] = ($coupon['cp_overlap'] == '0') ? ' checked' : '';
        $cp_overlapArr[1] = ($coupon['cp_overlap'] == '1') ? ' checked' : '';

        $cp_sale_typeArr = array('', '');
        $cp_sale_typeArr[0] = ($coupon['cp_sale_type'] == '0') ? ' checked' : '';
        $cp_sale_typeArr[1] = ($coupon['cp_sale_type'] == '1') ? ' checked' : '';

        $cp_dupsArr = array('', '');
        $cp_dupsArr[0] = ($coupon['cp_dups'] == '0') ? ' checked' : '';
        $cp_dupsArr[1] = ($coupon['cp_dups'] == '1') ? ' checked' : '';

        $cp_use_sexArr = array('all' => '', 'F' => '', 'M' => '');
        $cp_use_sexArr['all'] = (isset($coupon['cp_use_sex']) && $coupon['cp_use_sex'] == '') ? ' checked' : '';
        $cp_use_sexArr['F'] = (isset($coupon['cp_use_sex']) && $coupon['cp_use_sex'] == 'F') ? ' checked' : '';
        $cp_use_sexArr['M'] = (isset($coupon['cp_use_sex']) && $coupon['cp_use_sex'] == 'M') ? ' checked' : '';
        $OM_TIME_YEAR = date("Y", time());

        $ar_week_day = isset($coupon['cp_week_day']) ? $coupon['cp_week_day'] : '';
        $cp_week_day = explode(",", $ar_week_day);
        $arr_yoil = array("일", "월", "화", "수", "목", "금", "토");
        $yoilStr = "";
        for ($i = 0; $i < 7; $i++) {
            $checked = "";
            if (in_array($arr_yoil[$i], $cp_week_day)) {
                $checked = "checked";
            }

            $yoilStr .= "<label><input type=\"checkbox\" name=\"cp_week_day[]\" value=\"{$arr_yoil[$i]}\" $checked> {$arr_yoil[$i]}요일</label>";
        }
        $cp_inv_typeArr = array('', '');
        $cp_inv_typeArr[0] = ($coupon['cp_inv_type'] == '0') ? ' checked' : '';
        $cp_inv_typeArr[1] = ($coupon['cp_inv_type'] == '1') ? ' checked' : '';

        $cp_use_partArr = array('', '', '', '', '');
        $cp_use_partArr[0] = (isset($coupon['cp_use_part']) && $coupon['cp_use_part'] == '0') ? ' checked' : '';
        $cp_use_partArr[1] = (isset($coupon['cp_use_part']) && $coupon['cp_use_part'] == '1') ? ' checked' : '';
        $cp_use_partArr[2] = (isset($coupon['cp_use_part']) && $coupon['cp_use_part'] == '2') ? ' checked' : '';
        $cp_use_partArr[3] = (isset($coupon['cp_use_part']) && $coupon['cp_use_part'] == '3') ? ' checked' : '';
        $cp_use_partArr[4] = (isset($coupon['cp_use_part']) && $coupon['cp_use_part'] == '4') ? ' checked' : '';
        $get_goods_sca_select = $this->common_lib->get_goods_sca_select('use_category');
        $categoryOpt = "";
        for ($i = 0; $i < count($arr_category); $i++) {
            if ($arr_category[$i]) {
                $categoryOpt .= "<option value='$arr_category[$i]'>" . $this->common_lib->adm_category_navi($arr_category[$i]) . " ($arr_category[$i])</option>\n";
            }
        }
        $arr_dlimit[0] = isset($arr_dlimit[0]) ? $arr_dlimit[0] : 0;
        $arr_dlimit[1] = isset($arr_dlimit[1]) ? $arr_dlimit[1] : 0;
        $arr_dlimit[2] = isset($arr_dlimit[2]) ? $arr_dlimit[2] : 0;
        $arr_dlimit[3] = isset($arr_dlimit[3]) ? $arr_dlimit[3] : 0;
        $arr_dlimit[4] = isset($arr_dlimit[4]) ? $arr_dlimit[4] : 0;
        $coupon['cp_type'] = isset($coupon['cp_type']) ? $coupon['cp_type'] : "";
        $coupon['cp_use_part'] = isset($coupon['cp_use_part']) ? $coupon['cp_use_part'] : "";
        $coupon['cp_inv_type'] = isset($coupon['cp_inv_type']) ? $coupon['cp_inv_type'] : "";

        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'frm_submit' => $frm_submit,
            'coupon' => $coupon,
            'w' => $w,
            'sst' => $sst,
            'sfl' => $sfl,
            'stx' => $stx,
            'fr_date' => $fr_date,
            'to_date' => $to_date,
            'page' => $page,
            'cp_id' => $cp_id,
            'coupontType' => $coupontType,
            'couponCheck' => $couponCheck,
            'arr_dlimit' => $arr_dlimit,
            'get_goods_level_select' => $get_goods_level_select,
            'couponUpdateTag' => $couponUpdateTag,
            'cp_subjectTag' => $cp_subjectTag,
            'cp_explanTag' => $cp_explanTag,
            'cp_useArr' => $cp_useArr,
            'cp_downArr' => $cp_downArr,
            'cp_overlapArr' => $cp_overlapArr,
            'cp_sale_typeArr' => $cp_sale_typeArr,
            'cp_dupsArr' => $cp_dupsArr,
            'cp_use_sexArr' => $cp_use_sexArr,
            'OM_TIME_YEAR' => $OM_TIME_YEAR,
            'yoilStr' => $yoilStr,
            'cp_inv_typeArr' => $cp_inv_typeArr,
            'cp_use_partArr' => $cp_use_partArr,
            'cnt_use_goods' => $cnt_use_goods,
            'ck_use_goods' => $ck_use_goods,
            'get_goods_sca_select' => $get_goods_sca_select,
            'categoryOpt' => $categoryOpt,
            'ck_join' => $ck_join,
            'cp_id' => $cp_id
        );
        $this->load->view('admin/goods/coupon_form', $data);
    }
    private function goods_price($headTitle, $headTitleEnd)
    {
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/goods/price', $data);
    }
    private function goods_popular_list($code, $headTitle, $headTitleEnd)
    {
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_popular ";
        $sql_search = " where pt_id = 'admin' and TRIM(pp_word) <> '' ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        // 기간검색
        if ($fr_date && $to_date)
            $sql_search .= " and pp_date between '$fr_date' and '$to_date' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and pp_date = '$fr_date' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and pp_date = '$to_date' ";

        if (!$orderby) {
            $filed = "pp_id";
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
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $sflArr = array('', '');
        $sflArr[0] = option_selected('pp_word', $sfl, '검색어');
        $sflArr[1] = option_selected('pp_ip', $sfl, 'IP');
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $pp_wordTag = $this->gvcomplex_lib->subject_sort_link('pp_word', $q2);
        $pp_dateTag = $this->gvcomplex_lib->subject_sort_link('pp_date', $q2);

        $i = 0;
        $popular_listTag = "";
        foreach ($result as $key => $row) {
            $word = $this->funcomplex_lib->get_text($row['pp_word']);

            if ($i == 0)
                $popular_listTag .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $popular_listTag .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"pp_id[{$i}]\" value=\"{$row['pp_id']}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td class=\"tal\">{$word}</td>
                    <td>{$row['pp_date']}</td>
                    <td class=\"tal\">{$row['pp_ip']}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $popular_listTag .= '<tbody><tr><td colspan="5" class="empty_table">자료가 없습니다.</td></tr>';


        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $btn_frmline = "<input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">";
        $data = array(
            'btn_frmline' => $btn_frmline,
            'pagination' => $pagination,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'code' => $code,
            'sflArr' => $sflArr,
            'get_search_date' => $get_search_date,
            'stx' => $stx,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'pp_wordTag' => $pp_wordTag,
            'pp_dateTag' => $pp_dateTag,
            'popular_listTag' => $popular_listTag
        );
        $this->load->view('admin/goods/popular_list', $data);
    }

    private function goods_popular_rank($code, $headTitle, $headTitleEnd)
    {
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $orderby = $this->input->get('orderby');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_popular ";
        $sql_search = " where pt_id = 'admin' and TRIM(pp_word) <> '' ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        // 기간검색
        if ($fr_date && $to_date)
            $sql_search .= " and pp_date between '$fr_date' and '$to_date' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and pp_date = '$fr_date' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and pp_date = '$to_date' ";
        $sql_group = " group by pp_word ";
        $sql_order = " order by cnt desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql_common .= $sql_search;
        $sql = " select pp_word $sql_common $sql_group ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $result = $resultObj->result_array();
        $total_count = $resultObj->num_rows();

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = "select pp_word, count(*) as cnt $sql_common $sql_group $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $sflTag = option_selected('pp_word', $sfl, '검색어');
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $i = 0;
        $popular_rankTable = "";
        foreach ($result as $key => $row) {
            $word = $this->funcomplex_lib->get_text($row['pp_word']);
            $rank = ($i + 1 + ($rows * ($page - 1)));

            if ($i == 0)
                $popular_rankTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $popular_rankTable .= "
                <tr class=\"{$bg}\">
                    <td>" . number_format($rank) . "</td>
                    <td class=\"tal\">{$word}</td>
                    <td>" . number_format($row['cnt']) . "</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $popular_rankTable .= '<tbody><tr><td colspan="3" class="empty_table">자료가 없습니다.</td></tr>';
        $data = array(
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'code' => $code,
            'stx' => $stx,
            'total_count' => $total_count,
            'pagination' => $pagination,
            'sflTag' => $sflTag,
            'get_search_date' => $get_search_date,
            'popular_rankTable' => $popular_rankTable
        );
        $this->load->view('admin/goods/popular_rank', $data);
    }

    private function goods_timesale($code, $headTitle, $headTitleEnd)
    {
        $returnJson = $this->goods_sub_timesale($code, $headTitle, "timesale"); #goods_sub가 들어가는 화면은 서브에서 먼저 headTitle이 출력이 되고, 본 함수의 끝에 headTitleEnd에 들어가야함
        $q1 = $returnJson['q1'];
        $btn_frmline = $returnJson['btn_frmline'];
        $code = $this->input->get('code');
        $page = $this->input->get('page');
        $total_count = $returnJson['total_count'];
        $page_rows = $returnJson['page_rows'];
        $q2 = $returnJson['q2'];
        $result = $returnJson['result'];
        $num = $returnJson['num'];
        $total_page = $returnJson['total_page'];

        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page_rowsOpt = array('30' => '', '50' => '', '100' => '', '150' => '');
        $page_rowsOpt['30'] = option_selected('30',  $page_rows, '30줄 정렬');
        $page_rowsOpt['50'] = option_selected('50',  $page_rows, '50줄 정렬');
        $page_rowsOpt['100'] = option_selected('100',  $page_rows, '100줄 정렬');
        $page_rowsOpt['150'] = option_selected('150',  $page_rows, '150줄 정렬');

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $reg_timeTag = $this->gvcomplex_lib->subject_sort_link('reg_time', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);
        $rankTag = $this->gvcomplex_lib->subject_sort_link('rank', $q2);

        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $updateTimeTag = $this->gvcomplex_lib->subject_sort_link('update_time', $q2);
        $stockTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $normalPriceTag = $this->gvcomplex_lib->subject_sort_link('normal_price', $q2);
        $supplyPriceTag = $this->gvcomplex_lib->subject_sort_link('supply_price', $q2);
        $calculatePriceTag = $this->gvcomplex_lib->subject_sort_link('calculate_price', $q2);
        $goodsPriceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);
        $gPointTag = $this->gvcomplex_lib->subject_sort_link('gpoint', $q2);

        $i = 0;
        $listTable = "";
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            #var_dump((isset($gw_isopen[$row['isopen']])?$gw_isopen[$row['isopen']]:''));
            $gs_id = $row['index_no'];

            if ($row['stock_mod'])
                $stockQty = number_format($row['stock_qty']);
            else
                $stockQty = '<span class="txt_false">무제한</span>';

            $bg = 'list' . ($i % 2);

            $listTable .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">
                        <input type=\"hidden\" name=\"gs_id[{$i}]\" value=\"{$gs_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">" . $num-- . "</td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td colspan=\"2\" class=\"tal\">" . $this->funcomplex_lib->get_text($row['gname']) . "</td>
                    <td>" . substr($row['reg_time'], 2, 8) . "</td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['normal_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['calculate_price']) . "</td>
                    <td rowspan=\"2\" class=\"tar\">" . number_format($row['gpoint']) . "</td>
                    <td rowspan=\"2\"><input type=\"text\" name=\"rank[{$i}]\" value=\"{$row['rank']}\" class=\"frm_input\"></td>
                    <td rowspan=\"2\"><a href=\"" . base_url() . "admin/goods?code=form&w=u&gs_id={$gs_id}{$qstr}&page={$page}&bak={$code}&type={$row['type']}\" class=\"btn_small\">수정</a></td>
                </tr>
                <tr class=\"{$bg}\">
                    <td class=\"fc_00f\">{$row['mb_id']}</td>
                    <td class=\"tal txt_succeed\">" . $this->gvcomplex_lib->get_seller_name($row['mb_id']) . "</td>
                    <td class=\"tal txt_succeed\">" . $this->funcomplex_lib->get_cgy_info($row) . "</td>
                    <td class=\"fc_00f\">" . substr($row['update_time'], 2, 8) . "</td>
                    <td>{$stockQty}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $listTable .= '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'headTitleEnd' => $headTitleEnd,
            'q1' => $q1,
            'page' => $page,
            'btn_frmline' => $btn_frmline,
            'total_count' => $total_count,
            'page_rows' => $page_rows,
            'page_rowsOpt' => $page_rowsOpt,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'reg_timeTag' => $reg_timeTag,
            'isopenTag' => $isopenTag,
            'rankTag' => $rankTag,
            'mb_idTag' => $mb_idTag,
            'updateTimeTag' => $updateTimeTag,
            'stockTag' => $stockTag,
            'normalPriceTag' => $normalPriceTag,
            'supplyPriceTag' => $supplyPriceTag,
            'calculatePriceTag' => $calculatePriceTag,
            'goodsPriceTag' => $goodsPriceTag,
            'gPointTag' => $gPointTag,
            'listTable' => $listTable,
            'pagination' => $pagination,
            'requestUrl' => $requestUrl,
            'return' => "list"
        );
        $this->load->view('admin/goods/timesale_list', $data);
    }

    public function goods_option()
    {
        $gs['index_no'] = null;
        $goods_optionObj = $this->admin_template->goods_options($gs);
        $goods_option = $goods_optionObj['tag'];
        #$po_run=$goods_optionObj['po_run'];
        echo $goods_option;
    }
    public function goods_spl()
    {
        $gs['index_no'] = null;
        $goods_splObj = $this->admin_template->goods_spl($gs);
        $goods_spl = $goods_splObj['tag'];
        #$po_run=$goods_splObj['po_run'];

        echo $goods_spl;
    }
    public function goods_info()
    {
        $gs['index_no'] = null;
        $gs['info_gubun'] = null;
        $goods_info = $this->admin_template->goods_info($gs);
        echo $goods_info;
    }
    public function goods_gift_serial()
    {
        $title = '쿠폰 (인쇄용)';
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $this->admin_template->admin_head($title);
        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        $gr_id = $this->input->get('gr_id');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $query_string = "gr_id=$gr_id$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_gift ";
        $sql_search = " where gr_id = '$gr_id' ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if (!$orderby) {
            $filed = "no";
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
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $sql2 = "select * from shop_gift_group where gr_id = ?";
        $param = array($gr_id);
        $group = $this->query_model->returnOneArr($sql, $param);
        $mb_nameTag = option_selected('mb_name', $sfl, '회원명');
        $mb_idTag = option_selected('mb_id', $sfl, '회원ID');
        $gi_numTag = option_selected('gi_num', $sfl, '인증번호');
        $gr_subjectTag = $this->funcomplex_lib->get_text($group['gr_subject']);

        $gi_useLink = $this->gvcomplex_lib->subject_sort_link('gi_use', $q2);
        $mb_idLink = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $mb_nameLink = $this->gvcomplex_lib->subject_sort_link('mb_name', $q2);
        $mb_wdateLink = $this->gvcomplex_lib->subject_sort_link('mb_wdate', $q2);

        $i = 0;
        $serialTable = "";
        foreach ($result as $key => $row) {
            if (is_null_time($row['mb_wdate'])) {
                $row['mb_wdate'] = '';
            }

            if ($i == 0)
                $serialTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $serialTable .= "
                <tr class=\"{$bg}\">
                    <td>" . $num-- . "</td>
                    <td>{$row['gi_num']}</td>
                    <td>" . ($row['gi_use'] ? 'yes' : '') . "</td>
                    <td>{$row['mb_id']}</td>
                    <td>{$row['mb_name']}</td>
                    <td>{$row['mb_wdate']}</td>
                </tr>";
        }
        if ($i == 0)
            $serialTable .= '<tbody><tr><td colspan="6" class="empty_table">자료가 없습니다.</td></tr>';


        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $data = array(
            'title' => $title,
            'mb_nameTag' => $mb_nameTag,
            'mb_idTag' => $mb_idTag,
            'gi_numTag' => $gi_numTag,
            'stx' => $stx,
            'total_count' => $total_count,
            'gr_subjectTag' => $gr_subjectTag,
            'q1' => $q1,
            'gi_useLink' => $gi_useLink,
            'mb_idLink' => $mb_idLink,
            'mb_nameLink' => $mb_nameLink,
            'mb_wdateLink' => $mb_wdateLink,
            'serialTable' => $serialTable,
            'pagination' => $pagination
        );
        $this->load->view('admin/goods/gift_serial', $data);
        $this->admin_template->admin_footer_sub();
    }
    public function goods_coupon_goods()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $title = '상품검색';
        $this->admin_template->admin_head($title);

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $sca = $this->input->get('sca');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $orderby = $this->input->get('orderby');
        $page = $this->input->get('page');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        if ($sel_ca1) $sca = $sel_ca1;
        if ($sel_ca2) $sca = $sel_ca2;
        if ($sel_ca3) $sca = $sel_ca3;
        if ($sel_ca4) $sca = $sel_ca4;
        if ($sel_ca5) $sca = $sel_ca5;

        if (isset($sel_ca1)) $qstr .= "&sel_ca1=$sel_ca1";
        if (isset($sel_ca2)) $qstr .= "&sel_ca2=$sel_ca2";
        if (isset($sel_ca3)) $qstr .= "&sel_ca3=$sel_ca3";
        if (isset($sel_ca4)) $qstr .= "&sel_ca4=$sel_ca4";
        if (isset($sel_ca5)) $qstr .= "&sel_ca5=$sel_ca5";

        $query_string = $qstr;
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_goods ";
        $sql_search = " where use_aff = 0 and shop_state = 0 ";

        if ($sca) {
            $sql_search .= " and (ca_id like '$sca%' or ca_id2 like '$sca%' or ca_id3 like '$sca%') ";
        }

        if ($sfl && $stx) {
            $sql_search .= " and ($sfl like '%$stx%') ";
        }

        if (!$orderby) {
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

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = "select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $btn_frmline = "<button type=\"button\" onclick=\"btn_check('punish');\" class=\"btn_lsmall bx-white\">선택적용</button>";

        $sflArr = array('', '', '', '', '');
        $sflArr[0] = option_selected('gname', $sfl, '상품명');
        $sflArr[1] = option_selected('gcode', $sfl, '상품코드');
        $sflArr[2] = option_selected('mb_id', $sfl, '업체코드');
        $sflArr[3] = option_selected('maker', $sfl, '제조사');
        $sflArr[4] = option_selected('origin', $sfl, '원산지');

        $sel_caArr = array('', '', '', '', '', '');
        $sel_caArr[1] = $this->funcomplex_lib->get_category_select_1('sel_ca1', $sca);
        $sel_caArr[2] = $this->funcomplex_lib->get_category_select_2('sel_ca2', $sca);
        $sel_caArr[3] = $this->funcomplex_lib->get_category_select_3('sel_ca3', $sca);
        $sel_caArr[4] = $this->funcomplex_lib->get_category_select_4('sel_ca4', $sca);
        $sel_caArr[5] = $this->funcomplex_lib->get_category_select_5('sel_ca5', $sca);

        $gcodeTag = $this->gvcomplex_lib->subject_sort_link('gcode', $q2);
        $mb_idTag = $this->gvcomplex_lib->subject_sort_link('mb_id', $q2);
        $gnameTag = $this->gvcomplex_lib->subject_sort_link('gname', $q2);
        $isopenTag = $this->gvcomplex_lib->subject_sort_link('isopen', $q2);
        $stock_qtyTag = $this->gvcomplex_lib->subject_sort_link('stock_qty', $q2);
        $goods_priceTag = $this->gvcomplex_lib->subject_sort_link('goods_price', $q2);

        $i = 0;
        $couponTable = "";
        $gw_isopen = $this->config->item('gw_isopen');
        foreach ($result as $key => $row) {
            $gs_id = $row['index_no'];
            $gs_img = $this->funcomplex_lib->get_it_image_url($gs_id, $row['simg1'], 40, 40);
            $gs_name = cut_str($row['gname'], 70);
            $gs_price = $row['goods_price'];
            $gs_type = $row['mb_id'] == "admin" ? "매입" : "위탁";

            $s_upd = "<a href=\"javascript:chk_save('$gs_id','$gs_name','$gs_img','$gs_price');\" class=\"btn_small grey\">선택</a>";

            $bg = 'list' . ($i % 2);


            if ($i == 0) {
                $couponTable .= '<tbody class="list">';
            }
            $couponTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                        <input type=\"hidden\" name=\"gs_id[]\" value=\"{$gs_id}\">
                        <input type=\"hidden\" name=\"gs_name[]\" value=\"{$gs_name}\">
                        <input type=\"hidden\" name=\"gs_img[]\" value=\"{$gs_img}\">
                        <input type=\"hidden\" name=\"gs_price[]\" value=\"{$gs_price}\">
                    </td>
                    <td><a href=\"" . base_url() . "shop/view?index_no={$gs_id}\" target=\"_blank\">" . $this->funcomplex_lib->get_it_image($gs_id, $row['simg1'], 40, 40) . "</a></td>
                    <td>{$row['gcode']}</td>
                    <td>{$gs_type}</td>
                    <td class=\"tal\">" . cut_str($row['gname'], 50) . "</td>
                    <td>" . (isset($gw_isopen[$row['isopen']]) ? $gw_isopen[$row['isopen']] : '') . "</td>
                    <td class=\"tar\">" . number_format($row['stock_qty']) . "</td>
                    <td class=\"tar\">" . number_format($row['goods_price']) . "</td>
                    <td>{$s_upd}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $couponTable .= '<tbody><tr><td colspan="9" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'btn_frmline' => $btn_frmline,
            'title' => $title,
            'sflArr' => $sflArr,
            'stx' => $stx,
            'sel_caArr' => $sel_caArr,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'gcodeTag' => $gcodeTag,
            'gnameTag' => $gnameTag,
            'isopenTag' => $isopenTag,
            'mb_idTag' => $mb_idTag,
            'stock_qtyTag' => $stock_qtyTag,
            'goods_priceTag' => $goods_priceTag,
            'couponTable' => $couponTable,
            'pagination' => $pagination
        );
        $this->load->view('admin/goods/coupon_goods', $data);
        $this->admin_template->admin_footer_sub();
    }
    #업체코드
    public function supply()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];


        $title = '업체선택';
        $this->admin_template->admin_head($title);
        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $query_string = $qstr;
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_seller ";
        $sql_search = " where state = '1' ";

        if ($sfl) {
            $sql_search .= " and seller_code = '$sfl' ";
        }

        if ($stx) {
            $sql_search .= " and company_name like '%$stx%' ";
        }

        if (!$orderby) {
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
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = "select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $company_name = $this->gvcomplex_lib->subject_sort_link('company_name', $q2);
        $company_owner = $this->gvcomplex_lib->subject_sort_link('company_owner', $q2);
        $i = 0;
        $listTable = "";
        foreach ($result as $key => $row) {
            if ($i == 0)
                $listTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $listTable .= "
                <tr class=\"{$bg}\">
                    <td>" . $num-- . "</td>
                    <td>{$row['seller_code']}</td>
                    <td class=\"tal\">{$row['company_name']}</td>
                    <td>{$row['company_owner']}</td>
                    <td><button type=\"button\" onClick=\"yes('{$row['seller_code']}')\" class=\"btn_small grey\">선택</button></td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $listTable .= '<tbody><tr><td colspan="5" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'title' => $title,
            'sfl' => $sfl,
            'stx' => $stx,
            'total_count' => $total_count,
            'company_name' => $company_name,
            'company_owner' => $company_owner,
            'listTable' => $listTable,
            'pagination' => $pagination
        );
        $this->load->view('admin/supply', $data);
        $this->admin_template->admin_footer_sub();
    }

    #상품(goods) 함수들 끝
    #통계분석(visit) 페이지
    public function visit()
    {
        $this->admin_template->admin_access();
        $this->admin_template->admin_head();
        $member = $this->load->get_var('member');
        $ADMIN_MENU7 = $this->config->item('ADMIN_MENU7');
        $ADMIN_MENU7_01 = $this->config->item('ADMIN_MENU7_01');
        $ADMIN_MENU7_02 = $this->config->item('ADMIN_MENU7_02');
        $ADMIN_MENU7_03 = $this->config->item('ADMIN_MENU7_03');
        $ADMIN_MENU7_04 = $this->config->item('ADMIN_MENU7_04');
        $ADMIN_MENU7_05 = $this->config->item('ADMIN_MENU7_05');
        $ADMIN_MENU7_06 = $this->config->item('ADMIN_MENU7_06');
        $ADMIN_MENU7_07 = $this->config->item('ADMIN_MENU7_07');
        $ADMIN_MENU7_08 = $this->config->item('ADMIN_MENU7_08');
        $ADMIN_MENU7_09 = $this->config->item('ADMIN_MENU7_09');
        $ADMIN_MENU7_10 = $this->config->item('ADMIN_MENU7_10');
        $ADMIN_MENU7_11 = $this->config->item('ADMIN_MENU7_11');
        $ADMIN_MENU7_12 = $this->config->item('ADMIN_MENU7_12');
        $ADMIN_MENU7_13 = $this->config->item('ADMIN_MENU7_13');
        $ADMIN_MENU7_14 = $this->config->item('ADMIN_MENU7_14');
        $ADMIN_MENU7_15 = $this->config->item('ADMIN_MENU7_15');
        $pg_title = $ADMIN_MENU7;
        $pg_num = 7;
        $snb_icon = "<i class=\"fa fa-bar-chart\"></i>";

        if ($member['id'] != 'admin' && !$member['auth_' . $pg_num]) {
            alert("접근권한이 없습니다.");
        }
        $pg_title2 = "";
        $code = $this->input->get('code');
        if ($code == "hour")        $pg_title2 = $ADMIN_MENU7_01;
        if ($code == "date")        $pg_title2 = $ADMIN_MENU7_02;
        if ($code == "week")        $pg_title2 = $ADMIN_MENU7_03;
        if ($code == "month")    $pg_title2 = $ADMIN_MENU7_04;
        if ($code == "year")        $pg_title2 = $ADMIN_MENU7_05;
        if ($code == "browser")    $pg_title2 = $ADMIN_MENU7_06;
        if ($code == "os")        $pg_title2 = $ADMIN_MENU7_07;
        if ($code == "domain")    $pg_title2 = $ADMIN_MENU7_08;
        if ($code == "search")    $pg_title2 = $ADMIN_MENU7_09;
        if ($code == "order1")    $pg_title2 = $ADMIN_MENU7_10;
        if ($code == "order2")    $pg_title2 = $ADMIN_MENU7_11;
        if ($code == "cancel")    $pg_title2 = $ADMIN_MENU7_12;
        if ($code == "return")    $pg_title2 = $ADMIN_MENU7_13;
        if ($code == "change")    $pg_title2 = $ADMIN_MENU7_14;
        if ($code == "refund")    $pg_title2 = $ADMIN_MENU7_15;
        $this->admin_template->admin_topmenu($pg_title, false, $pg_title2, $snb_icon);
        $this->visitSkin($code, $pg_title2);
        $this->admin_template->admin_footer();
    }
    private function visitSkin($code, $pg_title2)
    {
        $headTitle = "
            <div class=\"s_wrap\">
                <h1>{$pg_title2}</h1>";
        $headTitleEnd = "</div>";
        switch ($code) {
            case 'hour':
                $this->visit_hour($code, $headTitle, $headTitleEnd);
                break;
            case 'date':
                $this->visit_date($code, $headTitle, $headTitleEnd);
                break;
            case 'week':
                $this->visit_week($code, $headTitle, $headTitleEnd);
                break;
            case 'month':
                $this->visit_month($code, $headTitle, $headTitleEnd);
                break;
            case 'year':
                $this->visit_year($code, $headTitle, $headTitleEnd);
                break;
            case 'browser':
                $this->visit_browser($code, $headTitle, $headTitleEnd);
                break;
            case 'os':
                $this->visit_os($code, $headTitle, $headTitleEnd);
                break;
            case 'domain':
                $this->visit_domain($code, $headTitle, $headTitleEnd);
                break;
            case 'search':
                $this->visit_search($code, $headTitle, $headTitleEnd);
                break;
            case 'order1':
                $this->visit_order1($code, $headTitle, $headTitleEnd);
                break;
            case 'order2':
                $this->visit_order2($code, $headTitle, $headTitleEnd);
                break;
            case 'cancel':
                $this->visit_cancel($code, $headTitle, $headTitleEnd);
                break;
            case 'return':
                $this->visit_return($code, $headTitle, $headTitleEnd);
                break;
            case 'change':
                $this->visit_change($code, $headTitle, $headTitleEnd);
                break;
            case 'refund':
                $this->visit_refund($code, $headTitle, $headTitleEnd);
                break;
            default:
                break;
        }
    }
    private function visit_hour($code, $headTitle, $headTitleEnd)
    {
        #visit_sub쓸때 필요한 기본 세팅들
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $this->visit_sub($code, $headTitle); #vist_sub가 쓰일 때는 headTitle을 보내야 함
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = $OM_TIME_YMD;
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = $OM_TIME_YMD;
        #visit_sub쓸때 필요한 기본 세팅들 끝
        $max = 0;
        $sum_count = 0;
        $sql = " select SUBSTRING(vi_time,1,2) as vi_hour, count(vi_id) as cnt
                        from shop_visit
                        where vi_date between ? and ?
                        group by vi_hour
                        order by vi_hour ";
        $param = array($fr_date, $to_date);
        $result = $this->query_model->returnArr($sql, $param);
        $i = 0;
        $arr = array();
        foreach ($result as $key => $row) {
            $arr[$row['vi_hour']] = $row['cnt'];
            if ($row['cnt'] > $max) $max = $row['cnt'];
            $sum_count += $row['cnt'];
            $i++;
        }
        $k = 0;
        $visit_hourTable = "";
        if ($i) {
            for ($i = 0; $i < 24; $i++) {
                $hour = sprintf("%02d", $i);
                $arr[$hour] = (isset($arr[$hour])) ? $arr[$hour] : 0;
                $count = (int)$arr[$hour];

                $rate = ($count / $sum_count * 100);
                $s_rate = number_format($rate, 1);

                $bg = 'list' . ($i % 2);
                $visit_hourTable .= "
                    <tr class=\"{$bg}\">
                        <td>{$hour}</td>
                        <td class=\"tal\">
                            <div class=\"graph\">
                                <span class=\"bar\" style=\"width:{$s_rate}%\"></span>
                            </div>
                        </td>
                        <td>" . number_format($count) . "</td>
                        <td>{$s_rate}</td>
                    </tr>";
            }
        } else {
            $visit_hourTable .= '<tr><td colspan="4" class="empty_table">자료가 없습니다.</td></tr>';
        }
        $data = array('sum_count' => $sum_count, 'headTitleEnd' => $headTitleEnd, 'visit_hourTable' => $visit_hourTable);
        $this->load->view('admin/visit/visit_hour', $data);
    }
    private function visit_date($code, $headTitle, $headTitleEnd)
    {
        #visit_sub쓸때 필요한 기본 세팅들
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $this->visit_sub($code, $headTitle); #vist_sub가 쓰일 때는 headTitle을 보내야 함
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = $OM_TIME_YMD;
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = $OM_TIME_YMD;
        #visit_sub쓸때 필요한 기본 세팅들 끝
        $sum_count = 0;
        $sql = " select vs_date, sum(vs_count) as cnt
                        from shop_visit_sum
                        where vs_date between ? and ?
                        group by vs_date
                        order by vs_date desc ";
        $param = array($fr_date, $to_date);
        $result = $this->query_model->returnArr($sql, $param);
        $arr = array();
        foreach ($result as $key => $row) {
            $arr[$row['vs_date']] = $row['cnt'];
            $sum_count += $row['cnt'];
        }
        $i = 0;
        $save_count = -1;
        $tot_count = 0;
        $visit_dateTable = "";
        if (count($arr)) {
            foreach ($arr as $key => $value) {
                $count = $value;

                $rate = ($count / $sum_count * 100);
                $s_rate = number_format($rate, 1);

                $bg = 'list' . ($i % 2);
                $visit_dateTable .= "
                    <tr class=\"{$bg}\">
                        <td>{$key}</td>
                        <td class=\"tal\">
                            <div class=\"graph\">
                                <span class=\"bar\" style=\"width:{$s_rate}%\"></span>
                            </div>
                        </td>
                        <td>" . number_format($value) . "</td>
                        <td>{$s_rate}</td>
                    </tr>";
                $i++;
            }
        } else {
            $visit_dateTable .= '<tr><td colspan="4" class="empty_table">자료가 없습니다.</td></tr>';
        }
        $data = array('visit_dateTable' => $visit_dateTable, 'sum_count' => $sum_count, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/visit/visit_date', $data);
    }
    private function visit_week($code, $headTitle, $headTitleEnd)
    {
        #visit_sub쓸때 필요한 기본 세팅들
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $this->visit_sub($code, $headTitle); #vist_sub가 쓰일 때는 headTitle을 보내야 함
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = $OM_TIME_YMD;
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = $OM_TIME_YMD;
        #visit_sub쓸때 필요한 기본 세팅들 끝
        $weekday = array('월', '화', '수', '목', '금', '토', '일');
        $sum_count = 0;
        $sql = " select WEEKDAY(vs_date) as weekday_date, SUM(vs_count) as cnt
                        from shop_visit_sum
                        where vs_date between ? and ?
                        group by weekday_date
                        order by weekday_date ";
        $param = array($fr_date, $to_date);
        $result = $this->query_model->returnArr($sql, $param);
        $i = 0;
        foreach ($result as $key => $row) {
            $arr[$row['weekday_date']] = $row['cnt'];
            $sum_count += $row['cnt'];
            $i++;
        }
        $visit_weekTable = "";
        if ($i) {
            for ($i = 0; $i < 7; $i++) {
                $count = isset($arr[$i]) ? (int)$arr[$i] : 0;

                $rate = ($count / $sum_count * 100);
                $s_rate = number_format($rate, 1);

                $bg = 'list' . ($i % 2);
                $visit_weekTable .= "
                    <tr class=\"{$bg}\">
                        <td>{$weekday[$i]}</td>
                        <td class=\"tal\">
                            <div class=\"graph\">
                                <span class=\"bar\" style=\"width:{$s_rate}%\"></span>
                            </div>
                        </td>
                        <td>" . number_format($count) . "</td>
                        <td>{$s_rate}</td>
                    </tr>";
            }
        } else {
            $visit_weekTable .= '<tr><td colspan="4" class="empty_table">자료가 없습니다.</td></tr>';
        }
        $data = array('visit_weekTable' => $visit_weekTable, 'sum_count' => $sum_count, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/visit/visit_week', $data);
    }
    private function visit_month($code, $headTitle, $headTitleEnd)
    {
        #visit_sub쓸때 필요한 기본 세팅들
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $this->visit_sub($code, $headTitle); #vist_sub가 쓰일 때는 headTitle을 보내야 함
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = $OM_TIME_YMD;
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = $OM_TIME_YMD;
        #visit_sub쓸때 필요한 기본 세팅들 끝
        $sum_count = 0;
        $sql = " select SUBSTRING(vs_date,1,7) as vs_month, SUM(vs_count) as cnt
                        from shop_visit_sum
                        where vs_date between ? and ?
                        group by vs_month
                        order by vs_month desc ";
        $param = array($fr_date, $to_date);
        $result = $this->query_model->returnArr($sql, $param);
        $arr = array();
        foreach ($result as $key => $row) {
            $arr[$row['vs_month']] = $row['cnt'];
            $sum_count += $row['cnt'];
        }
        $i = 0;
        $save_count = -1;
        $tot_count = 0;
        $visit_monthTable = "";
        if (count($arr)) {
            foreach ($arr as $key => $value) {
                $count = $value;

                $rate = ($count / $sum_count * 100);
                $s_rate = number_format($rate, 1);

                $bg = 'list' . ($i % 2);
                $visit_monthTable .= "
                    <tr class=\"{$bg}\">
                        <td>{$key}</td>
                        <td class=\"tal\">
                            <div class=\"graph\">
                                <span class=\"bar\" style=\"width:{$s_rate}%\"></span>
                            </div>
                        </td>
                        <td>" . number_format($value) . "</td>
                        <td>{$s_rate}</td>
                    </tr>";
                $i++;
            }
        } else {
            $visit_monthTable .= '<tr><td colspan="4" class="empty_table">자료가 없습니다.</td></tr>';
        }
        $data = array('visit_monthTable' => $visit_monthTable, 'sum_count' => $sum_count, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/visit/visit_month', $data);
    }
    private function visit_year($code, $headTitle, $headTitleEnd)
    {
        #visit_sub쓸때 필요한 기본 세팅들
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $this->visit_sub($code, $headTitle); #vist_sub가 쓰일 때는 headTitle을 보내야 함
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = $OM_TIME_YMD;
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = $OM_TIME_YMD;
        #visit_sub쓸때 필요한 기본 세팅들 끝
        $sum_count = 0;
        $sql = " select SUBSTRING(vs_date,1,4) as vs_year, SUM(vs_count) as cnt
                        from shop_visit_sum
                        where vs_date between ? and ?
                        group by vs_year
                        order by vs_year desc ";
        $param = array($fr_date, $to_date);
        $result = $this->query_model->returnArr($sql, $param);
        $arr = array();
        foreach ($result as $key => $row) {
            $arr[$row['vs_year']] = $row['cnt'];
            $sum_count += $row['cnt'];
        }
        $i = 0;
        $save_count = -1;
        $tot_count = 0;
        $visit_yearTable = "";
        if (count($arr)) {
            foreach ($arr as $key => $value) {
                $count = $value;

                $rate = ($count / $sum_count * 100);
                $s_rate = number_format($rate, 1);

                $bg = 'list' . ($i % 2);
                $visit_yearTable .= "
                    <tr class=\"{$bg}\">
                        <td>{$key}</td>
                        <td class=\"tal\">
                            <div class=\"graph\">
                                <span class=\"bar\" style=\"width:{$s_rate}%\"></span>
                            </div>
                        </td>
                        <td>" . number_format($value) . "</td>
                        <td>{$s_rate}</td>
                    </tr>";
                $i++;
            }
        } else {
            $visit_yearTable .= '<tr><td colspan="4" class="empty_table">자료가 없습니다.</td></tr>';
        }
        $data = array('visit_yearTable' => $visit_yearTable, 'sum_count' => $sum_count, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/visit/visit_year', $data);
    }
    private function visit_browser($code, $headTitle, $headTitleEnd)
    {
        #visit_sub쓸때 필요한 기본 세팅들
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $this->visit_sub($code, $headTitle); #vist_sub가 쓰일 때는 headTitle을 보내야 함
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = $OM_TIME_YMD;
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = $OM_TIME_YMD;
        #visit_sub쓸때 필요한 기본 세팅들 끝
        $max = 0;
        $sum_count = 0;
        $sql = "select * from shop_visit where vi_date between ? and ?";
        $param = array($fr_date, $to_date);
        $result = $this->query_model->returnArr($sql, $param);
        $arr = array();
        foreach ($result as $key => $row) {
            $s = get_brow($row['vi_agent']);
            $arr[$s] = isset($arr[$s]) ? $arr[$s] : 0;
            $arr[$s]++;
            if ($arr[$s] > $max) $max = $arr[$s];
            $sum_count++;
        }
        $i = 0;
        $save_count = -1;
        $tot_count = 0;
        $visit_browserTable = "";
        if (count($arr)) {
            arsort($arr);
            foreach ($arr as $key => $value) {
                $count = $arr[$key];
                if ($save_count != $count) {
                    $i++;
                    $no = $i;
                    $save_count = $count;
                } else {
                    $no = "";
                }

                $rate = ($count / $sum_count * 100);
                $s_rate = number_format($rate, 1);

                $bg = 'list' . ($i % 2);
                $visit_browserTable .= "
                    <tr class=\"{$bg}\">
                        <td>{$no}</td>
                        <td class=\"tal\">{$key}</td>
                        <td class=\"tal\">
                            <div class=\"graph\">
                                <span class=\"bar\" style=\"width:{$s_rate}%\"></span>
                            </div>
                        </td>
                        <td>" . number_format($count) . "</td>
                        <td>{$s_rate}</td>
                    </tr>";
            }
        } else {
            $visit_browserTable .= '<tr><td colspan="5" class="empty_table">자료가 없습니다.</td></tr>';
        }
        $data = array('visit_browserTable' => $visit_browserTable, 'sum_count' => $sum_count, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/visit/visit_browser', $data);
    }
    private function visit_os($code, $headTitle, $headTitleEnd)
    {
        #visit_sub쓸때 필요한 기본 세팅들
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $this->visit_sub($code, $headTitle); #vist_sub가 쓰일 때는 headTitle을 보내야 함
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = $OM_TIME_YMD;
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = $OM_TIME_YMD;
        #visit_sub쓸때 필요한 기본 세팅들 끝
        $max = 0;
        $sum_count = 0;
        $sql = "select * from shop_visit where vi_date between ? and ?";
        $param = array($fr_date, $to_date);
        $result = $this->query_model->returnArr($sql, $param);
        $arr = array();
        foreach ($result as $key => $row) {
            $s = get_os($row['vi_agent']);
            $arr[$s] = isset($arr[$s]) ? $arr[$s] : 0;
            $arr[$s]++;
            if ($arr[$s] > $max) $max = $arr[$s];
            $sum_count++;
        }
        $i = 0;
        $save_count = -1;
        $tot_count = 0;
        $visit_osTable = "";
        if (count($arr)) {
            arsort($arr);
            foreach ($arr as $key => $value) {
                $count = $arr[$key];
                if ($save_count != $count) {
                    $i++;
                    $no = $i;
                    $save_count = $count;
                } else {
                    $no = '';
                }

                if (!$key) {
                    $key = '직접';
                }

                $rate = ($count / $sum_count * 100);
                $s_rate = number_format($rate, 1);

                $bg = 'list' . ($i % 2);
                $visit_osTable .= "
                    <tr class=\"{$bg}\">
                        <td>{$no}</td>
                        <td class=\"tal\">{$key}</td>
                        <td class=\"tal\">
                            <div class=\"graph\">
                                <span class=\"bar\" style=\"width:{$s_rate}%\"></span>
                            </div>
                        </td>
                        <td>" . number_format($count) . "</td>
                        <td>{$s_rate}</td>
                    </tr>";
            }
        } else {
            $visit_osTable .= '<tr><td colspan="5" class="empty_table">자료가 없습니다.</td></tr>';
        }
        $data = array('visit_osTable' => $visit_osTable, 'sum_count' => $sum_count, 'headTitleEnd' => $headTitleEnd);
        $this->load->view('admin/visit/visit_os', $data);
    }
    private function visit_domain($code, $headTitle, $headTitleEnd)
    {
        #visit_sub쓸때 필요한 기본 세팅들
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $this->visit_sub($code, $headTitle); #vist_sub가 쓰일 때는 headTitle을 보내야 함
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = $OM_TIME_YMD;
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = $OM_TIME_YMD;
        #visit_sub쓸때 필요한 기본 세팅들 끝
        $max = 0;
        $sum_count = 0;
        $sql = " select * from shop_visit where vi_date between ? and ?";
        $param = array($fr_date, $to_date);
        $result = $this->query_model->returnArr($sql, $param);
        $arr = array();
        foreach ($result as $key => $row) {
            $str = $row['vi_referer'];
            preg_match("/^http[s]*:\/\/([\.\-\_0-9a-zA-Z]*)\//", $str, $match);
            $s = isset($match[1]) ? $match[1] : "";
            $s = preg_replace("/^(www\.|search\.|dirsearch\.|dir\.search\.|dir\.|kr\.search\.|myhome\.)(.*)/", "\\2", $s);
            $arr[$s] = isset($arr[$s]) ? $arr[$s] : 0;
            $arr[$s]++;

            if ($arr[$s] > $max) $max = $arr[$s];

            $sum_count++;
        }
        $i = 0;
        $save_count = -1;
        $tot_count = 0;
        $visit_domainTable = "";
        if (count($arr)) {
            arsort($arr);
            foreach ($arr as $key => $value) {
                $count = $arr[$key];
                if ($save_count != $count) {
                    $i++;
                    $no = $i;
                    $save_count = $count;
                } else {
                    $no = '';
                }

                if (!$key) {
                    $key = '직접';
                }

                $rate = ($count / $sum_count * 100);
                $s_rate = number_format($rate, 1);

                $bg = 'list' . ($i % 2);
                $visit_domainTable .= "
                    <tr class=\"{$bg}\">
                        <td>{$no}</td>
                        <td class=\"tal\">{$key}</td>
                        <td class=\"tal\">
                            <div class=\"graph\">
                                <span class=\"bar\" style=\"width:{$s_rate}%\"></span>
                            </div>
                        </td>
                        <td>" . number_format($count) . "</td>
                        <td>{$s_rate}</td>
                    </tr>";
            }
        } else {
            $visit_domainTable .= '<tr><td colspan="5" class="empty_table">자료가 없습니다.</td></tr>';
        }
        $data = array('sum_count' => $sum_count, 'headTitleEnd' => $headTitleEnd, 'visit_domainTable' => $visit_domainTable);
        $this->load->view('admin/visit/visit_domain', $data);
    }
    private function visit_search($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $stx = $this->input->get('stx');
        $sfl = $this->input->get('sfl');
        $page = $this->input->get('page');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $where = array();
        $sql_search = "";

        if ($stx) {
            switch ($sfl) {
                case "vi_ip":
                case "mb_id":
                    $where[] = " $sfl like '$stx%' ";
                    break;
                default:
                    $where[] = " $sfl like '%$stx%' ";
                    break;
            }
        }

        if ($fr_date && $to_date)
            $where[] = " vi_date between '$fr_date' and '$to_date' ";
        else if ($fr_date && !$to_date)
            $where[] = " vi_date = '$fr_date' ";
        else if (!$fr_date && $to_date)
            $where[] = " vi_date = '$to_date' ";

        if ($where) {
            $sql_search = ' where ' . implode(' and ', $where);
        }

        $sql_common = " from shop_visit $sql_search ";

        // 테이블의 전체 레코드수만 얻음
        $sql = " select count(*) as cnt " . $sql_common;
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common order by vi_id desc limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $sch_sortArr = array('', '', '');
        $sch_sortArr[0] = option_selected("vi_ip", $sfl, "IP");
        $sch_sortArr[1] = option_selected("vi_referer", $sfl, "접속경로");
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date);

        $i = 0;
        $visit_searchTable = "";
        foreach ($result as $key => $row) {
            $brow = get_brow($row['vi_agent']);
            $os   = get_os($row['vi_agent']);

            $a1 = $a2 = $referer = $title = '';
            if ($row['vi_referer']) {

                $referer = $this->funcomplex_lib->get_text($row['vi_referer']);
                $referer = urldecode($referer);

                if (!is_utf8($referer)) {
                    $referer = iconv_utf8($referer);
                }

                $title = str_replace(array('<', '>', '&'), array("&lt;", "&gt;", "&amp;"), $referer);
                $a1 = '<a href="' . $row['vi_referer'] . '" class="normal" target="_blank" title="' . $title . '">';
                $a1 = str_replace('&', "&amp;", $a1);
                $a2 = '</a>';
            }

            $ip = $row['vi_ip'];

            if ($brow == '기타') {
                $brow = '<span title="' . $this->funcomplex_lib->get_text($row['vi_agent']) . '">' . $brow . '</span>';
            }
            if ($os == '기타') {
                $os = '<span title="' . $this->funcomplex_lib->get_text($row['vi_agent']) . '">' . $os . '</span>';
            }

            if ($i == 0)
                $visit_searchTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $visit_searchTable .= "
                <tr class=\"{$bg}\">
                    <td class=\"tal\">{$ip}</td>
                    <td class=\"tal\" style=\"word-break:break-all;\">";
            if ($row['vi_referer'])
                $visit_searchTable .= $a1 . $title . $a2;
            else
                $visit_searchTable .= '<span class="fc_137">주소직접입력 또는 즐겨찾기를 이용한 방문</span>';
            $visit_searchTable .= "
                    </td>
                    <td>{$brow}</td>
                    <td>{$os}</td>
                    <td>{$row['vi_date']} {$row['vi_time']}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $visit_searchTable .= '<tbody><tr><td colspan="6" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'jqueryUiDatePick' => $jqueryUiDatePick,
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'sch_sortArr' => $sch_sortArr,
            'get_search_date' => $get_search_date,
            'total_count' => $total_count,
            'visit_searchTable' => $visit_searchTable,
            'pagination' => $pagination,
            'stx' => $stx
        );

        $this->load->view('admin/visit/visit_search', $data);
    }
    private function visit_order1($code, $headTitle, $headTitleEnd)
    {
        $OM_TIME_YEAR = date("Y", time());
        $OM_TIME_MONTH = date("m", time());
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);

        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $min_year = $this->input->get('min_year');
        if (!$year)  $year  = $OM_TIME_YEAR;
        if (!$month)    $month = $OM_TIME_MONTH;

        $search_date = preg_replace("/([0-9]{4})([0-9]{2})/", "\\1-\\2", $year . $month);

        $sql = " select COUNT(*) as cnt
                    from shop_order
                    where dan IN(1,2,3,4,5,8)
                        and left(od_time,7) = ? ";
        $param = array($search_date);
        $row = $this->query_model->returnOneArr($sql, $param);
        $sum_count = (int)$row['cnt'];

        // 가장작은 년도 구하기
        $sql = " select MIN(od_time) as min_year from shop_order ";
        $row = $this->query_model->returnOneArr($sql);
        $min_year = substr($row['min_year'], 0, 4);

        if (!$min_year)
            $min_year = $OM_TIME_YEAR;
        $yearStr = "";
        for ($i = $min_year; $i <= $OM_TIME_YEAR; $i++) {
            $yearStr .= "<option value=\"{$i}\"" . get_selected($year, $i) . ">{$i}년</option>\n";
        }
        $monthStr = "";
        for ($i = 1; $i <= 12; $i++) {
            $k = sprintf('%02d', $i);
            $monthStr .= "<option value=\"{$k}\"" . get_selected($month, $k) . ">{$k}월</option>\n";
        }
        $tot_count = 0;
        $tot_price = 0;

        $visit_order1Table = "";
        $sum_countInst = $sum_count;
        if (!$sum_count) $sum_countInst = 1;


        for ($i = 1; $i <= 31; $i++) {
            $day = sprintf("%02d", $i);
            $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $year . $month . $day);

            $sql = " select COUNT(*) as cnt,
                                SUM(goods_price + baesong_price) as price
                        from shop_order
                        where dan IN(1,2,3,4,5,8)
                            and left(od_time,10) =?";
            $param = array($date);
            $sum = $this->query_model->returnOneArr($sql, $param);

            $rate = ((int)$sum['cnt'] / $sum_countInst * 100);
            $s_rate = number_format($rate, 1);

            $tot_count += (int)$sum['cnt'];
            $tot_price += (int)$sum['price'];

            $bg = 'list' . ($i % 2);
            $visit_order1Table .= "
                <tr class=\"{$bg}\">
                    <td>{$date}</td>
                    <td><div class=\"graph\"><span class=\"bar\" style=\"width:{$s_rate}%\"></span></div></td>
                    <td>{$s_rate}</td>
                    <td>" . number_format($sum['cnt']) . "</td>
                    <td class=\"tar\">" . number_format($sum['price']) . "</td>
                </tr>";
            // 오늘날짜와 같다면 중지
            if ($date == $OM_TIME_YMD) break;
        }
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'yearStr' => $yearStr,
            'monthStr' => $monthStr,
            'sum_count' => $sum_count,
            'visit_order1Table' => $visit_order1Table,
            'tot_count' => $tot_count,
            'tot_price' => $tot_price
        );
        $this->load->view('admin/visit/visit_order1', $data);
    }
    private function visit_order2($code, $headTitle, $headTitleEnd)
    {
        $OM_TIME_YEAR = date("Y", time());
        $OM_TIME_MONTH = date("m", time());
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $OM_TIME_YM = date("Y-m", time());


        $year = $this->input->get('year');
        if (!$year) $year = $OM_TIME_YEAR;
        $sql = " select COUNT(*) as cnt from shop_order where dan IN(1,2,3,4,5,8) and left(od_time,4) = ?";
        $param = array($year);
        $row = $this->query_model->returnOneArr($sql, $param);
        $sum_count = (int)$row['cnt'];

        // 가장작은 년도 구하기
        $sql = " select MIN(od_time) as min_year from shop_order ";
        $row = $this->query_model->returnOneArr($sql);
        $min_year = substr($row['min_year'], 0, 4);

        if (!$min_year)
            $min_year = $OM_TIME_YEAR;

        $yearOpt = "";
        for ($i = $min_year; $i <= $OM_TIME_YEAR; $i++) {
            $yearOpt .= "<option value=\"{$i}\"" . get_selected($year, $i) . ">{$i}년</option>\n";
        }
        $tot_count = 0;
        $tot_price = 0;
        $sum_countInst = $sum_count;
        if (!$sum_count) $sum_countInst = 1;

        $monthTag = "";
        for ($i = 1; $i <= 12; $i++) {
            $month = sprintf("%02d", $i);
            $date = preg_replace("/([0-9]{4})([0-9]{2})/", "\\1-\\2", $year . $month);

            $sql = " select COUNT(*) as cnt,SUM(goods_price + baesong_price) as price from shop_order where dan IN(1,2,3,4,5,8) and left(od_time,7) = ?";
            $param = array($date);
            $sum = $this->query_model->returnOneArr($sql, $param);

            $rate = ((int)$sum['cnt'] / $sum_countInst * 100);
            $s_rate = number_format($rate, 1);
            $tot_count += (int)$sum['cnt'];
            $tot_price += (int)$sum['price'];

            $bg = 'list' . ($i % 2);
            $monthTag .= "
                <tr class=\"{$bg}\">
                    <td>{$date}</td>
                    <td><div class=\"graph\"><span class=\"bar\" style=\"width:{$s_rate}%\"></span></div></td>
                    <td>{$s_rate}</td>
                    <td>" . number_format($sum['cnt']) . "</td>
                    <td class=\"tar\">" . number_format($sum['price']) . "</td>
                </tr>";
            // 이번달과 같다면 중지
            if ($date == $OM_TIME_YM) break;
        }


        $data = array(
            'code' => $code,
            'yearOpt' => $yearOpt,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'sum_count' => $sum_count,
            'monthTag' => $monthTag,
            'tot_count' => $tot_count,
            'tot_price' => $tot_price
        );
        $this->load->view('admin/visit/visit_order2', $data);
    }
    private function visit_cancel($code, $headTitle, $headTitleEnd)
    {
        $OM_TIME_YEAR = date("Y", time());
        $OM_TIME_MONTH = date("m", time());
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $OM_TIME_YM = date("Y-m", time());

        $year = $this->input->get('year');
        $month = $this->input->get('month');

        if (!$year)  $year  = $OM_TIME_YEAR;
        if (!$month)    $month = $OM_TIME_MONTH;

        $search_date = preg_replace("/([0-9]{4})([0-9]{2})/", "\\1-\\2", $year . $month);
        $sql = " select COUNT(*) as cnt from shop_order where dan = 6 and left(cancel_date,7) =?";
        $param = array($search_date);
        $row = $this->query_model->returnOneArr($sql, $param);
        $sum_count = (int)$row['cnt'];

        // 가장작은 년도 구하기
        $sql = "select MIN(od_time) as min_year from shop_order";
        $row = $this->query_model->returnOneArr($sql);
        $min_year = substr($row['min_year'], 0, 4);

        if (!$min_year)
            $min_year = $OM_TIME_YEAR;

        $yearOpt = "";
        for ($i = $min_year; $i <= $OM_TIME_YEAR; $i++) {
            $yearOpt .= "<option value=\"{$i}\"" . get_selected($year, $i) . ">{$i}년</option>\n";
        }
        $monthOpt = "";
        for ($i = 1; $i <= 12; $i++) {
            $k = sprintf('%02d', $i);
            $monthOpt .= "<option value=\"{$k}\"" . get_selected($month, $k) . ">{$k}월</option>\n";
        }

        $visit_cancelTable = "";
        $tot_count = 0;
        $tot_price = 0;
        $sum_countInst = $sum_count;
        if (!$sum_count) $sum_countInst = 1;
        for ($i = 1; $i <= 31; $i++) {
            $day = sprintf("%02d", $i);
            $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $year . $month . $day);

            $sql = " select COUNT(*) as cnt,
                                SUM(goods_price + baesong_price) as price
                           from shop_order
                          where dan = 6
                            and left(cancel_date,10) = ?";
            $param = array($date);
            $sum = $this->query_model->returnOneArr($sql, $param);

            $rate = ((int)$sum['cnt'] / $sum_countInst * 100);
            $s_rate = number_format($rate, 1);

            $tot_count += (int)$sum['cnt'];
            $tot_price += (int)$sum['price'];

            $bg = 'list' . ($i % 2);
            $visit_cancelTable .= "
                <tr class=\"{$bg}\">
                    <td>{$date}</td>
                    <td><div class=\"graph\"><span class=\"bar\" style=\"width:{$s_rate}%\"></span></div></td>
                    <td>{$s_rate}</td>
                    <td>" . number_format($sum['cnt']) . "</td>
                    <td class=\"tar\">" . number_format($sum['price']) . "</td>
                </tr>";
            // 오늘날짜와 같다면 중지
            if ($date == $OM_TIME_YMD) break;
        }

        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'yearOpt' => $yearOpt,
            'monthOpt' => $monthOpt,
            'sum_count' => $sum_count,
            'visit_cancelTable' => $visit_cancelTable,
            'tot_count' => $tot_count,
            'tot_price' => $tot_price
        );
        $this->load->view('admin/visit/visit_cancel', $data);
    }
    private function visit_return($code, $headTitle, $headTitleEnd)
    {
        $OM_TIME_YEAR = date("Y", time());
        $OM_TIME_MONTH = date("m", time());
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $year = $this->input->get('year');
        $month = $this->input->get('month');

        if (!$year)  $year  = $OM_TIME_YEAR;
        if (!$month)    $month = $OM_TIME_MONTH;
        $search_date = preg_replace("/([0-9]{4})([0-9]{2})/", "\\1-\\2", $year . $month);

        $sql = "select COUNT(*) as cnt from shop_order where dan = 7 and left(return_date,7) = ?";
        $param = array($search_date);
        $row = $this->query_model->returnOneArr($sql, $param);
        $sum_count = (int)$row['cnt'];
        // 가장작은 년도 구하기
        $sql = "select MIN(od_time) as min_year from shop_order";
        $row = $this->query_model->returnOneArr($sql);
        $min_year = substr($row['min_year'], 0, 4);
        if (!$min_year)
            $min_year = $OM_TIME_YEAR;
        $yearOpt = "";
        for ($i = $min_year; $i <= $OM_TIME_YEAR; $i++) {
            $yearOpt .= "<option value=\"{$i}\"" . get_selected($year, $i) . ">{$i}년</option>\n";
        }
        $monthOpt = "";
        for ($i = 1; $i <= 12; $i++) {
            $k = sprintf('%02d', $i);
            $monthOpt .= "<option value=\"{$k}\"" . get_selected($month, $k) . ">{$k}월</option>\n";
        }
        $visit_returnTable = "";
        $tot_count = 0;
        $tot_price = 0;
        $sum_countInst = $sum_count;
        if (!$sum_count) $sum_countInst = 1;
        for ($i = 1; $i <= 31; $i++) {
            $day = sprintf("%02d", $i);
            $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $year . $month . $day);

            $sql = " select COUNT(*) as cnt,
                                SUM(goods_price + baesong_price) as price
                        from shop_order
                        where dan = 7
                            and left(return_date,10) = ?";
            $param = array($date);
            $sum = $this->query_model->returnOneArr($sql, $param);

            $rate = ((int)$sum['cnt'] / $sum_countInst * 100);
            $s_rate = number_format($rate, 1);

            $tot_count += (int)$sum['cnt'];
            $tot_price += (int)$sum['price'];

            $bg = 'list' . ($i % 2);
            $visit_returnTable .= "
                <tr class=\"{$bg}\">
                    <td>{$date}</td>
                    <td><div class=\"graph\"><span class=\"bar\" style=\"width:{$s_rate}%\"></span></div></td>
                    <td>{$s_rate}</td>
                    <td>" . number_format($sum['cnt']) . "</td>
                    <td class=\"tar\">" . number_format($sum['price']) . "</td>
                </tr>";
            // 오늘날짜와 같다면 중지
            if ($date == $OM_TIME_YMD) break;
        }
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'yearOpt' => $yearOpt,
            'monthOpt' => $monthOpt,
            'sum_count' => $sum_count,
            'visit_returnTable' => $visit_returnTable,
            'tot_count' => $tot_count,
            'tot_price' => $tot_price
        );
        $this->load->view('admin/visit/visit_return', $data);
    }
    private function visit_change($code, $headTitle, $headTitleEnd)
    {
        $OM_TIME_YEAR = date("Y", time());
        $OM_TIME_MONTH = date("m", time());
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $year = $this->input->get('year');
        $month = $this->input->get('month');

        if (!$year)  $year  = $OM_TIME_YEAR;
        if (!$month)    $month = $OM_TIME_MONTH;
        $search_date = preg_replace("/([0-9]{4})([0-9]{2})/", "\\1-\\2", $year . $month);

        $sql = "select COUNT(*) as cnt from shop_order where dan = 8 and left(change_date,7) = ?";
        $param = array($search_date);
        $row = $this->query_model->returnOneArr($sql, $param);
        $sum_count = (int)$row['cnt'];
        // 가장작은 년도 구하기
        $sql = "select MIN(od_time) as min_year from shop_order";
        $row = $this->query_model->returnOneArr($sql);
        $min_year = substr($row['min_year'], 0, 4);
        if (!$min_year)
            $min_year = $OM_TIME_YEAR;
        $yearOpt = "";
        for ($i = $min_year; $i <= $OM_TIME_YEAR; $i++) {
            $yearOpt .= "<option value=\"{$i}\"" . get_selected($year, $i) . ">{$i}년</option>\n";
        }
        $monthOpt = "";
        for ($i = 1; $i <= 12; $i++) {
            $k = sprintf('%02d', $i);
            $monthOpt .= "<option value=\"{$k}\"" . get_selected($month, $k) . ">{$k}월</option>\n";
        }
        $visit_changeTable = "";
        $tot_count = 0;
        $tot_price = 0;
        $sum_countInst = $sum_count;
        if (!$sum_count) $sum_countInst = 1;
        for ($i = 1; $i <= 31; $i++) {
            $day = sprintf("%02d", $i);
            $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $year . $month . $day);

            $sql = "select COUNT(*) as cnt,SUM(goods_price + baesong_price) as price from shop_order where dan = 8 and left(change_date,10) = ?";
            $param = array($date);
            $sum = $this->query_model->returnOneArr($sql, $param);

            $rate = ((int)$sum['cnt'] / $sum_countInst * 100);
            $s_rate = number_format($rate, 1);

            $tot_count += (int)$sum['cnt'];
            $tot_price += (int)$sum['price'];

            $bg = 'list' . ($i % 2);
            $visit_changeTable .= "
                <tr class=\"{$bg}\">
                    <td>{$date}</td>
                    <td><div class=\"graph\"><span class=\"bar\" style=\"width:{$s_rate}%\"></span></div></td>
                    <td>{$s_rate}</td>
                    <td>" . number_format($sum['cnt']) . "</td>
                    <td class=\"tar\">" . number_format($sum['price']) . "</td>
                </tr>";
            // 오늘날짜와 같다면 중지
            if ($date == $OM_TIME_YMD) break;
        }
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'yearOpt' => $yearOpt,
            'monthOpt' => $monthOpt,
            'sum_count' => $sum_count,
            'visit_changeTable' => $visit_changeTable,
            'tot_count' => $tot_count,
            'tot_price' => $tot_price
        );
        $this->load->view('admin/visit/visit_change', $data);
    }
    private function visit_refund($code, $headTitle, $headTitleEnd)
    {
        $OM_TIME_YEAR = date("Y", time());
        $OM_TIME_MONTH = date("m", time());
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $year = $this->input->get('year');
        $month = $this->input->get('month');

        if (!$year)  $year  = $OM_TIME_YEAR;
        if (!$month)    $month = $OM_TIME_MONTH;
        $search_date = preg_replace("/([0-9]{4})([0-9]{2})/", "\\1-\\2", $year . $month);

        $sql = "select COUNT(*) as cnt from shop_order where dan = 9 and left(refund_date,7) = ?";
        $param = array($search_date);
        $row = $this->query_model->returnOneArr($sql, $param);
        $sum_count = (int)$row['cnt'];
        // 가장작은 년도 구하기
        $sql = "select MIN(od_time) as min_year from shop_order";
        $row = $this->query_model->returnOneArr($sql);
        $min_year = substr($row['min_year'], 0, 4);
        if (!$min_year)
            $min_year = $OM_TIME_YEAR;
        $yearOpt = "";
        for ($i = $min_year; $i <= $OM_TIME_YEAR; $i++) {
            $yearOpt .= "<option value=\"{$i}\"" . get_selected($year, $i) . ">{$i}년</option>\n";
        }
        $monthOpt = "";
        for ($i = 1; $i <= 12; $i++) {
            $k = sprintf('%02d', $i);
            $monthOpt .= "<option value=\"{$k}\"" . get_selected($month, $k) . ">{$k}월</option>\n";
        }
        $visit_refundTable = "";
        $tot_count = 0;
        $tot_price = 0;
        $sum_countInst = $sum_count;
        if (!$sum_count) $sum_countInst = 1;
        for ($i = 1; $i <= 31; $i++) {
            $day = sprintf("%02d", $i);
            $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $year . $month . $day);

            $sql = "select COUNT(*) as cnt,SUM(goods_price + baesong_price) as price from shop_order where dan = 9 and left(refund_date,10) = ?";
            $param = array($date);
            $sum = $this->query_model->returnOneArr($sql, $param);

            $rate = ((int)$sum['cnt'] / $sum_countInst * 100);
            $s_rate = number_format($rate, 1);

            $tot_count += (int)$sum['cnt'];
            $tot_price += (int)$sum['price'];

            $bg = 'list' . ($i % 2);
            $visit_refundTable .= "
                <tr class=\"{$bg}\">
                    <td>{$date}</td>
                    <td><div class=\"graph\"><span class=\"bar\" style=\"width:{$s_rate}%\"></span></div></td>
                    <td>{$s_rate}</td>
                    <td>" . number_format($sum['cnt']) . "</td>
                    <td class=\"tar\">" . number_format($sum['price']) . "</td>
                </tr>";
            // 오늘날짜와 같다면 중지
            if ($date == $OM_TIME_YMD) break;
        }
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'yearOpt' => $yearOpt,
            'monthOpt' => $monthOpt,
            'sum_count' => $sum_count,
            'visit_refundTable' => $visit_refundTable,
            'tot_count' => $tot_count,
            'tot_price' => $tot_price
        );
        $this->load->view('admin/visit/visit_refund', $data);
    }
    private function visit_sub($code, $headTitle)
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = $OM_TIME_YMD;
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = $OM_TIME_YMD;
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $get_search_date = get_search_date("fr_date", "to_date", $fr_date, $to_date, false);
        $data = array('code' => $code, 'jqueryUiDatePick' => $jqueryUiDatePick, 'get_search_date' => $get_search_date, 'headTitle' => $headTitle);
        $this->load->view('admin/visit/visit_sub', $data);
    }
    #통계페이지 끝
    #고객지원(help) 페이지
    public function help()
    {
        $this->admin_template->admin_access();
        $this->admin_template->admin_head();
        $member = $this->load->get_var('member');
        $ADMIN_MENU8 = $this->config->item('ADMIN_MENU8');
        $ADMIN_MENU8_01 = $this->config->item('ADMIN_MENU8_01');
        $ADMIN_MENU8_02 = $this->config->item('ADMIN_MENU8_02');
        $ADMIN_MENU8_03 = $this->config->item('ADMIN_MENU8_03');
        $ADMIN_MENU8_04 = $this->config->item('ADMIN_MENU8_04');
        $pg_title = $ADMIN_MENU8;
        $pg_num = 8;
        $snb_icon = "<i class=\"fa fa-comments-o\"></i>";

        if ($member['id'] != 'admin' && !$member['auth_' . $pg_num]) {
            alert("접근권한이 없습니다.");
        }
        $pg_title2 = "";
        $code = $this->input->get('code');
        if ($code == "qa")            $pg_title2 = $ADMIN_MENU8_01;
        if ($code == "qa_form")        $pg_title2 = $ADMIN_MENU8_01;
        if ($code == "leave")        $pg_title2 = $ADMIN_MENU8_02;
        if ($code == "faq_group")    $pg_title2 = $ADMIN_MENU8_03;
        if ($code == "faq")            $pg_title2 = $ADMIN_MENU8_04;
        if ($code == "faq_form")        $pg_title2 = $ADMIN_MENU8_04;
        $this->admin_template->admin_topmenu($pg_title, false, $pg_title2, $snb_icon);
        $this->helpSkin($code, $pg_title2);
        $this->admin_template->admin_footer();
    }
    private function helpSkin($code, $pg_title2)
    {
        $headTitle = "
            <div class=\"s_wrap\">
                <h1>{$pg_title2}</h1>";
        $headTitleEnd = "</div>";
        switch ($code) {
            case 'qa':
                $this->help_qa($code, $headTitle, $headTitleEnd);
                break;
            case 'qa_form':
                $this->help_qa_form($headTitle, $headTitleEnd);
                break;
            case 'leave':
                $this->help_leave($code, $headTitle, $headTitleEnd);
                break;
            case 'faq_group':
                $this->help_faq_group($code, $headTitle, $headTitleEnd);
                break;
            case 'faq':
                $this->help_faq($code, $headTitle, $headTitleEnd);
                break;
            case 'faq_form':
                $this->help_faq_form($headTitle, $headTitleEnd);
                break;
            default:
                break;
        }
    }
    private function help_qa($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_qa ";
        $sql_search = " where (1) ";
        $sql_order  = " order by index_no desc ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        // 테이블의 전체 레코드수만 얻음
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
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $btn_frmline = '<input type="submit" name="act_button" value="선택삭제" class="btn_lsmall bx-white" onclick="document.pressed=this.value">';
        $mb_idTag = option_selected('mb_id', $sfl, '작성자');
        $subjectTag = option_selected('subject', $sfl, '제목');

        $i = 0;
        $help_qaTable = "";
        foreach ($result as $key => $row) {
            $s_upd = "<a href=\"" . base_url() . "admin/help?code=qa_form&w=u&index_no={$row['index_no']}$qstr&page=$page\" class=\"btn_small\">수정</a>";

            if ($i == 0)
                $help_qaTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $help_qaTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"index_no[{$i}]\" value=\"{$row['index_no']}\">
                        <label for=\"chk_{$i}\" class=\"sound_only\">{$row['mb_id']} 님</label>
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                    </td>	
                    <td>" . $num-- . "</td>
                    <td><b>{$row['catename']}</b></td>
                    <td class=\"tal\">" . cut_str($row['subject'], 70) . "</td>
                    <td class=\"tal\">{$row['mb_id']}</td>
                    <td>" . substr($row['wdate'], 0, 10) . "</td>
                    <td>" . ($row['result_yes'] ? 'yes' : 'no') . "</td>
                    <td>{$s_upd}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $help_qaTable .= '<tbody><tr><td colspan="8" class="empty_table">자료가 없습니다.</td></tr>';


        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'btn_frmline' => $btn_frmline,
            'stx' => $stx,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'pagination' => $pagination,
            'mb_idTag' => $mb_idTag,
            'subjectTag' => $subjectTag,
            'help_qaTable' => $help_qaTable
        );
        $this->load->view('admin/help/help_qa', $data);
    }
    private function help_qa_form($headTitle, $headTitleEnd)
    {
        $w = $this->input->get('w');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $page = $this->input->get('page');
        $index_no = $this->input->get('index_no');

        $member = $this->load->get_var('member');
        $qstr = $this->load->get_var('qstr');
        $qa = array();
        if ($w == "u") {
            $sql = "select * from shop_qa where index_no=?";
            $param = array($index_no);
            $qa = $this->query_model->returnOneArr($sql, $param);
            if (!$qa['index_no'])
                alert("자료가 존재하지 않습니다.");
        }
        $qa['replyer'] = $qa['replyer'] ? $qa['replyer'] : $member['name'];
        $qa['mb_id'] = isset($qa['mb_id']) ? $qa['mb_id'] : "";
        $qa['subject'] = isset($qa['subject']) ? $qa['subject'] : "";
        $qa['replyer'] = isset($qa['replyer']) ? $qa['replyer'] : "";
        $qa['reply'] = isset($qa['reply']) ? $qa['reply'] : "";
        $resultYesTag = "";
        if ($qa['result_yes']) {
            $resultYesTag = "
                <tr>
                    <th scope=\"row\">답변일</th>
                    <td>{$qa['result_date']}</td>
                </tr>";
        }
        $data = array(
            'w' => $w,
            'sfl' => $sfl,
            'stx' => $stx,
            'page' => $page,
            'index_no' => $index_no,
            'qa' => $qa,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'resultYesTag' => $resultYesTag
        );
        $this->load->view('admin/help/help_qa_form', $data);
    }
    private function help_leave($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_member_leave ";
        $sql_search = " where 1 ";
        $sql_order  = " order by index_no desc ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        // 테이블의 전체 레코드수만 얻음
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
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $btn_frmline = '<input type="submit" name="act_button" value="선택삭제" class="btn_lsmall bx-white" onclick="document.pressed=this.value">';
        $mb_idTag = option_selected('mb_id', $sfl, '아이디');
        $mb_nameTag = option_selected('mb_name', $sfl, '회원명');

        $help_leaveTable = "";
        $i = 0;
        foreach ($result as $key => $row) {
            if ($i == 0)
                $help_leaveTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $row['mb_name'] = isset($row['mb_name']) ? $row['mb_name'] : "";
            $row['mb_id'] = isset($row['mb_id']) ? $row['mb_id'] : "";
            $row['reg_time'] = isset($row['reg_time']) ? $row['reg_time'] : "";
            $help_leaveTable .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"index_no[{$i}]\" value=\"{$row['index_no']}\">
                        <label for=\"chk_{$i}\" class=\"sound_only\">{$row['mb_id']} 님</label>
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td class=\"tal\">{$row['mb_name']}</td>
                    <td class=\"tal\">{$row['mb_id']}</td>
                    <td>{$row['reg_time']}</td>
                    <td class=\"tal\">";
            if ($row['memo'] == '기타' && $row['other'])
                $help_leaveTable .= $this->funcomplex_lib->get_text($row['other']);
            else
                $help_leaveTable .= $this->funcomplex_lib->get_text($row['memo']);
            $help_leaveTable .= "
                    </td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $help_leaveTable .= '<tbody><tr><td colspan="6" class="empty_table">자료가 없습니다.</td></tr>';

        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'btn_frmline' => $btn_frmline,
            'pagination' => $pagination,
            'mb_idTag' => $mb_idTag,
            'mb_nameTag' => $mb_nameTag,
            'stx' => $stx,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'help_leaveTable' => $help_leaveTable,
            'pagination' => $pagination
        );
        $this->load->view('admin/help/help_leave', $data);
    }
    private function help_faq_group($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $page = $this->input->get('page');

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_faq_cate ";
        $sql_search = " where (1) ";
        $sql_order  = " order by index_no desc ";

        // 테이블의 전체 레코드수만 얻음
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

        $btn_frmline = '
            <input type="submit" name="act_button" value="선택수정" class="btn_lsmall bx-white" onclick="document.pressed=this.value">
            <input type="submit" name="act_button" value="선택삭제" class="btn_lsmall bx-white" onclick="document.pressed=this.value">';
        $pagination = get_paging($config['write_pages'], $page, $total_page,  $requestUrl . '?' . $q1 . '&page=');
        $fqp_groupList = "";
        $i = 0;
        foreach ($result as $key => $row) {
            // FAQ수
            $sql2 = " select count(*) as cnt from shop_faq where cate =?";
            $param2 = array($row['index_no']);
            $row2 = $this->query_model->returnOneArr($sql2, $param2);

            if ($i == 0)
                $fqp_groupList .= '<tbody class="list">';

            $bg = 'list' . $i % 2;
            $fqp_groupList .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"index_no[{$i}]\" value=\"{$row['index_no']}\">
                        <label for=\"chk_{$i}\" class=\"sound_only\">{$row['catename']}</label>
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td class=\"tal\"><input type=\"text\" name=\"catename[{$i}]\" value=\"" . $this->funcomplex_lib->get_text($row['catename']) . "\" class=\"frm_input\"></td>
                    <td>{$row2['cnt']}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $fqp_groupList .= '<tbody><tr><td colspan="4" class="empty_table">자료가 없습니다.</td></tr>';

        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'pagination' => $pagination,
            'btn_frmline' => $btn_frmline,
            'total_count' => $total_count,
            'q1' => $q1,
            'page' => $page,
            'fqp_groupList' => $fqp_groupList
        );
        $this->load->view('admin/help/help_fap_qroup', $data);
    }
    private function help_faq($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $sca = $this->input->get('sca');
        $page = $this->input->get('page');

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_faq ";
        $sql_search = " where (1) ";
        $sql_order  = " order by index_no desc";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if ($sca) {
            $sql_search .= " and cate='$sca' ";
        }

        // 테이블의 전체 레코드수만 얻음
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

        $sql = "select * from shop_faq_cate";
        $faq_cateTag = "";
        $res = $this->query_model->returnArr($sql);
        foreach ($res as $key => $row) {
            $faq_cateTag .= option_selected($row['index_no'], $sca, $row['catename']);
        }
        $subjectTag = option_selected('subject', $sfl, '제목');
        $memoTag = option_selected('memo', $sfl, '내용');
        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "admin/help?code=faq_form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 추가하기</a>";

        $help_faqList = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $index_no = $row['index_no'];
            $sql = "select * from shop_faq_cate where index_no=?";
            $param = array($row['cate']);
            $row2 = $this->query_model->returnOneArr($sql, $param);

            $s_upd = "<a href=\"" . base_url() . "admin/help?code=faq_form&w=u&index_no=$index_no$qstr&page=$page\" class=\"btn_small\">수정</a>";

            if ($i == 0)
                $help_faqList .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $help_faqList .= "
                <tr class=\"{$bg}\">
                    <td>
                        <input type=\"hidden\" name=\"index_no[{$i}]\" value=\"{$index_no}\">
                        <label for=\"chk_{$i}\" class=\"sound_only\">{$index_no}</label>
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\" id=\"chk_{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td>{$row2['catename']}</td>
                    <td class=\"tal\"><a href=\"javascript:ubDisplay.display('{$i}')\">" . $this->funcomplex_lib->get_text($row['subject']) . "</a></td>
                    <td>" . substr($row['wdate'], 0, 10) . "</td>
                    <td>{$s_upd}</td>
                </tr>
                <tr id=\"view_{$i}\" style=\"display:none\" bgcolor='fdfbf5'>
                    <td colspan=\"3\"></td>
                    <td colspan=\"3\" class=\"tal\">
                        " . $this->thumbnail_lib->get_view_thumbnail($this->funcomplex_lib->conv_content($row['memo'], 1), 600) . "
                    </td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $help_faqList .= '<tbody><tr><td colspan="6" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'stx' => $stx,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'pagination' => $pagination,
            'faq_cateTag' => $faq_cateTag,
            'subjectTag' => $subjectTag,
            'memoTag' => $memoTag,
            'help_faqList' => $help_faqList
        );
        $this->load->view('admin/help/help_faq', $data);
    }
    private function help_faq_form($headTitle, $headTitleEnd)
    {
        $qstr = $this->load->get_var('qstr');

        $w = $this->input->get('w');
        $sca = $this->input->get('sca');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $page = $this->input->get('page');
        $index_no = $this->input->get('index_no');
        $sql = "select * from shop_faq where index_no=?";
        $param = array($index_no);
        $faq = $this->query_model->returnOneArr($sql, $param);
        $faq_cateTag = "";
        $sql = "select * from shop_faq_cate";
        $result = $this->query_model->returnArr($sql);
        foreach ($result as $key => $row) {
            $faq_cateTag .= option_selected($row['index_no'], $faq['cate'], $row['catename']);
        }
        $editor_memoJs = $this->global_lib->get_editor_js('memo');
        $editor_memo = $this->global_lib->editor_html('memo', $this->funcomplex_lib->get_text($faq['memo'], 0));
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'w' => $w,
            'sca' => $sca,
            'sfl' => $sfl,
            'stx' => $stx,
            'page' => $page,
            'index_no' => $index_no,
            'faq_cateTag' => $faq_cateTag,
            'faq' => $faq,
            'editor_memo' => $editor_memo,
            'qstr' => $qstr,
            'page' => $page,
            'editor_memoJs' => $editor_memoJs
        );
        $this->load->view('admin/help/help_faq_form', $data);
    }
    #고객관리 페이지 끝
    #디자인(design) 관리 페이지
    public function design()
    {
        $this->admin_template->admin_access();
        $this->admin_template->admin_head();
        $member = $this->load->get_var('member');
        $ADMIN_MENU9 = $this->config->item('ADMIN_MENU9');
        $ADMIN_MENU9_01 = $this->config->item('ADMIN_MENU9_01');
        $ADMIN_MENU9_02 = $this->config->item('ADMIN_MENU9_02');
        $ADMIN_MENU9_03 = $this->config->item('ADMIN_MENU9_03');
        $ADMIN_MENU9_04 = $this->config->item('ADMIN_MENU9_04');
        $ADMIN_MENU9_05 = $this->config->item('ADMIN_MENU9_05');
        // $ADMIN_MENU9_06=$this->config->item('ADMIN_MENU9_06');
        $ADMIN_MENU9_07 = $this->config->item('ADMIN_MENU9_07');
        $pg_title = $ADMIN_MENU9;
        $pg_num = 9;
        $snb_icon = "<i class=\"ionicons ion-compose\"></i>";

        if ($member['id'] != 'admin' && !$member['auth_' . $pg_num]) {
            alert("접근권한이 없습니다.");
        }
        $pg_title2 = "";
        $code = $this->input->get('code');
        if ($code == "banner_list")        $pg_title2 = $ADMIN_MENU9_01;
        if ($code == "banner_form")        $pg_title2 = $ADMIN_MENU9_01;
        if ($code == "mbanner_list")        $pg_title2 = $ADMIN_MENU9_02;
        if ($code == "mbanner_form")        $pg_title2 = $ADMIN_MENU9_02;
        if ($code == "logo")                $pg_title2 = $ADMIN_MENU9_03;
        if ($code == "contentlist")        $pg_title2 = $ADMIN_MENU9_04;
        if ($code == "contentform")        $pg_title2 = $ADMIN_MENU9_04;
        if ($code == "menu_form")        $pg_title2 = $ADMIN_MENU9_05;
        // if($code == "best_item")		$pg_title2 = $ADMIN_MENU9_06;
        if ($code == "popup_list")        $pg_title2 = $ADMIN_MENU9_07;
        if ($code == "popup_form")        $pg_title2 = $ADMIN_MENU9_07;
        $this->admin_template->admin_topmenu($pg_title, false, $pg_title2, $snb_icon);
        $this->designSkin($code, $pg_title2);
        $this->admin_template->admin_footer();
    }
    private function designSkin($code, $pg_title2)
    {
        $headTitle = "
            <div class=\"s_wrap\">
                <h1>{$pg_title2}</h1>";
        $headTitleEnd = "</div>";
        switch ($code) {
            case 'banner_list':
                $this->banner_list($code, $headTitle, $headTitleEnd);
                break;
            case 'banner_form':
                $this->banner_form($code, $headTitle, $headTitleEnd);
                break;
            case 'mbanner_list':
                $this->mbanner_list($code, $headTitle, $headTitleEnd);
                break;
            case 'mbanner_form':
                $this->mbanner_form($code, $headTitle, $headTitleEnd);
                break;
            case 'logo':
                $this->logo($code, $headTitle, $headTitleEnd);
                break;
            case 'contentlist':
                $this->contentlist($code, $headTitle, $headTitleEnd);
                break;
            case 'contentform':
                $this->contentform($headTitle, $headTitleEnd);
                break;
            case 'menu_form':
                $this->menu_form($headTitle, $headTitleEnd);
                break;
            case 'best_item':
                $this->best_item($headTitle, $headTitleEnd);
                break;
            case 'popup_list':
                $this->popup_list($code, $headTitle, $headTitleEnd);
                break;
            case 'popup_form':
                $this->popup_form($headTitle, $headTitleEnd);
                break;
            default:
                break;
        }
    }
    private function banner_list($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        // 배너코드를 얻는다.
        $gw_pbanner = $this->config->item('gw_pbanner');
        $super = $this->load->get_var('super');
        $position = $gw_pbanner[$super['theme']];
        if (!count($position)) {
            $empty = "
                <div class=\"local_desc01 local_desc\">
                    <p class=\"lh6\">
                        {$super['theme']} 스킨의 노출위치 변수가 설정되어있지 않습니다.<br>이 경우는 별도 디자인작업을 통한 변수 설정 누락건이며 담당 디자이너 및 개발자에게 문의하시기 바랍니다.<br>변수 설정파일 경로 : <b>/extend/shop.extend.php</b> 에서 설정 이후 이용 하시기 바랍니다.
                    </p>
                </div>";
            $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'empty' => $empty);
            $this->load->view('admin/design/designEmpty', $data);
            return;
        }
        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $page = $this->input->get('page');
        $sca = $this->input->get('sca');
        $sca = $this->input->get('sca');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_banner ";
        $sql_search = " where bn_device = 'pc' and bn_theme = '{$super['theme']}' and mb_id = 'admin' ";

        if (isset($sca) && is_numeric($sca)) {
            $sql_search .= " and bn_code = '$sca' ";
        }

        if (!$orderby) {
            $filed = "bn_code";
            $sod = "desc";
        } else {
            $sod = $orderby;
        }

        $sql_order = " order by $filed $sod ";

        // 테이블의 전체 레코드수만 얻음
        $sql = "select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = "select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택수정\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "admin/design?code=banner_form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 추가하기</a>";

        $scaOpt = "";
        $scaOpt .= option_selected('', $sca, '전체');
        for ($i = 0; $i < count($position); $i++) {
            $scaOpt .= option_selected($position[$i][0], $sca, $position[$i][3]);
        }
        $bn_codeTag = $this->gvcomplex_lib->subject_sort_link('bn_code', $q2);
        $bn_useTag = $this->gvcomplex_lib->subject_sort_link('bn_use', $q2);
        $bn_orderTag = $this->gvcomplex_lib->subject_sort_link('bn_order', $q2);

        $banner_listTag = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $bn_id = $row['bn_id'];

            $position = $gw_pbanner[$row['bn_theme']];
            foreach ($position as $key => $value) {
                list($pos, $wpx, $hpx, $subj) = $value;
                if ($pos == $row['bn_code']) break;
            }

            $bimg_str = '';
            $bimg = "./data/banner/{$row['bn_file']}";
            if (is_file($bimg) && $row['bn_file']) {
                $size = @getimagesize($bimg);
                if ($size[0] && $size[0] > 700)
                    $width = 700;
                else
                    $width = $size[0];
                $bimg = base_url() . "data/banner/{$row['bn_file']}";
                $bimg = rpc($bimg, '', '');
                $bimg_str = '<img src="' . $bimg . '" width="' . $width . '">';
            }

            $s_upd = "<a href='" . base_url() . "admin/design?code=banner_form&w=u&bn_id=$bn_id$qstr&page=$page' class=\"btn_small\">수정</a>";

            $bg = 'list' . ($i % 2);
            $banner_listTag .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">			
                        <input type=\"hidden\" name=\"bn_id[{$i}]\" value=\"{$bn_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">{$row['bn_code']}</td>
                    <td rowspan=\"2\"><input type=\"checkbox\" name=\"bn_use[{$i}]\" value=\"1\" " . get_checked($row['bn_use'], "1") . "></td>
                    <td rowspan=\"2\"><input type=\"text\" name=\"bn_order[{$i}]\" value=\"{$row['bn_order']}\" class=\"frm_input\"></td>
                    <td class=\"tal\">{$subj}</td>
                    <td><input type=\"text\" name=\"bn_link[{$i}]\" value=\"{$row['bn_link']}\" placeholder=\"URL\" class=\"frm_input\"></td>
                    <td>
                        <select name=\"bn_target[{$i}]\">
                            " . option_selected('_self', $row['bn_target'], "현재창") . "
                            " . option_selected('_blank', $row['bn_target'], "새창") . "
                        </select>
                    </td>	
                    <td><input type=\"text\" name=\"bn_width[{$i}]\" value=\"{$row['bn_width']}\" class=\"frm_input\"></td>
                    <td><input type=\"text\" name=\"bn_height[{$i}]\" value=\"{$row['bn_height']}\" class=\"frm_input\"></td>
                    <td>{$s_upd}</td>
                </tr>
                <tr class=\"{$bg} rows\">
                    <td class=\"td_img_view sbn_img\" colspan=\"6\">
                        <div class=\"sbn_image\">{$bimg_str}</div>
                        <button type=\"button\" class=\"btn_lsmall bx-blue sbn_img_view\">이미지보기</button>
                        <button type=\"button\" class=\"btn_lsmall bx-yellow sbn_all_view\">모두보기</button>
                        <button type=\"button\" class=\"btn_lsmall bx-yellow sbn_all_close\">모두닫기</button>
                    </td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $banner_listTag .= '<tbody><tr><td colspan="10" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'scaOpt' => $scaOpt,
            'q1' => $q1,
            'page' => $page,
            'super' => $super,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'bn_codeTag' => $bn_codeTag,
            'bn_useTag' => $bn_useTag,
            'bn_orderTag' => $bn_orderTag,
            'banner_listTag' => $banner_listTag,
            'pagination' => $pagination
        );
        $this->load->view('admin/design/banner_list', $data);
    }
    private function banner_form($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        // 배너코드를 얻는다.
        $gw_pbanner = $this->config->item('gw_pbanner');
        $super = $this->load->get_var('super');
        $qstr = $this->load->get_var('qstr');

        $position = $gw_pbanner[$super['theme']];
        if (!count($position)) {
            $empty = "
                <div class=\"local_desc01 local_desc\">
                    <p class=\"lh6\">
                        {$super['theme']} 스킨의 노출위치 변수가 설정되어있지 않습니다.<br>이 경우는 별도 디자인작업을 통한 변수 설정 누락건이며 담당 디자이너 및 개발자에게 문의하시기 바랍니다.<br>변수 설정파일 경로 : <b>/extend/shop.extend.php</b> 에서 설정 이후 이용 하시기 바랍니다.
                    </p>
                </div>";
            $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'empty' => $empty);
            $this->load->view('admin/design/designEmpty', $data);
            return;
        }
        $w = $this->input->get('w');
        $sca = $this->input->get('sca');
        $page = $this->input->get('page');
        $bn_id = $this->input->get('bn_id');
        $bn = array();
        if ($w == "") {
            $bn['bn_width']  = $position[0][1];
            $bn['bn_height'] = $position[0][2];
            $bn['bn_use']    = 1;
            $bn['bn_code']   = 0;
            $bn['bn_order']  = 0;
        } else if ($w == "u") {
            $sql = "select * from shop_banner where bn_id=?";
            $param = array($bn_id);
            $bn    = $this->query_model->returnOneArr($sql, $param);
            if (!$bn['bn_id'])
                alert("존재하지 않은 배너 입니다.");
        }

        $frm_submit = '<div class="btn_confirm">
                <input type="submit" value="저장" class="btn_large" accesskey="s">
                <a href="' . base_url() . 'admin/design?code=banner_list' . $qstr . '&page=' . $page . '" class="btn_large bx-white">목록</a>';
        if ($w == 'u')
            $frm_submit .= '<a href="' . base_url() . 'admin/design?code=banner_form" class="btn_large bx-red">추가</a>';
        $frm_submit .= '</div>';
        $positionTag = "";
        for ($i = 0; $i < count($position); $i++)
            $positionTag .= option_selected($position[$i][0], $bn['bn_code'], $position[$i][3]);
        $fixTag = help('[고정] 같은 위치에 고정되어 노출되며 2개이상 등록시 랜덤으로 노출됩니다.<br>[연속] 2개이상 등록시 세로로 연속하여 노출됩니다.<br>[롤링] 2개이상 등록시 슬라이드형식으로 롤링되며 노출됩니다.');

        $bimg_str = "";
        $bimg_strTag = "";
        $bn['bn_file'] = isset($bn['bn_file']) ? $bn['bn_file'] : "";
        $bn['bn_target'] = isset($bn['bn_target']) ? $bn['bn_target'] : "";
        $bn['bn_link'] = isset($bn['bn_link']) ? $bn['bn_link'] : "";
        $bn['bn_bg'] = isset($bn['bn_bg']) ? $bn['bn_bg'] : "";
        $bn['bn_text'] = isset($bn['bn_text']) ? $bn['bn_text'] : "";

        $bimg = './data/banner/' . $bn['bn_file'];
        if (is_file($bimg) && $bn['bn_file']) {
            $size = @getimagesize($bimg);
            if ($size[0] && $size[0] > 700)
                $width = 700;
            else
                $width = $size[0];
            $bimg = base_url() . 'data/banner/' . $bn['bn_file'];
            $bimg = rpc($bimg, '', '');

            $bimg_strTag .= '<input type="checkbox" name="bn_file_del" value="' . $bn['bn_file'] . '" id="bn_file_del"> <label for="bn_file_del">삭제</label>';
            $bimg_str = '<img src="' . $bimg . '" width="' . $width . '">';
        }
        if ($bimg_str) {
            $bimg_strTag .= '<div class="banner_or_img">' . $bimg_str . '</div>';
        }
        $selfTag = option_selected('_self', $bn['bn_target'], "현재창에서");
        $blankTag = option_selected('_blank', $bn['bn_target'], "새창으로");

        $innerHelp = help('[외부링크] http:// 를 포함해 절대경로로 입력해주시기 바랍니다.<br><span class="fc_197">절대경로 예시) http://test.com/shop/listtype?type=1</span>');
        $outerHelp = help('[내부링크] http:// 를 제외한 상대경로로 입력해주시기 바랍니다.<br><span class="fc_197">상대경로 예시) /shop/listtype?type=1</span>');
        $colorHelp = help('"#" 기호없이 색상값 6자만 입력하세요. 예) F6E3FB');
        $bannerHelp = help('특정 배너에만 문구가 노출되는 부분으로 모든 배너에 문구가 노출되지는 않습니다.');
        $rollingHelp = help('롤링 및 연속배너에만 적용됩니다. (고정배너는 의미없음)');
        $bn_checkTag = get_checked($bn['bn_use'], "1");
        $colorJs = "";
        for ($i = 0; $i < count($position); $i++) {
            $colorJs = "
                case '{$position[$i][0]}':
                    $(\"input[name=bn_width]\").val('{$position[$i][1]}');
                    $(\"input[name=bn_height]\").val('{$position[$i][2]}');
                    break;";
        }
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'frm_submit' => $frm_submit,
            'positionTag' => $positionTag,
            'w' => $w,
            'sca' => $sca,
            'page' => $page,
            'fixTag' => $fixTag,
            'bimg_strTag' => $bimg_strTag,
            'selfTag' => $selfTag,
            'blankTag' => $blankTag,
            'innerHelp' => $innerHelp,
            'outerHelp' => $outerHelp,
            'colorHelp' => $colorHelp,
            'bn' => $bn,
            'bannerHelp' => $bannerHelp,
            'rollingHelp' => $rollingHelp,
            'bn_checkTag' => $bn_checkTag,
            'bn_id' => $bn_id,
            'colorJs' => $colorJs,
            'position' => $position
        );
        $this->load->view('admin/design/banner_form', $data);
    }
    private function mbanner_list($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        // 배너코드를 얻는다.
        $gw_mbanner = $this->config->item('gw_mbanner');
        $super = $this->load->get_var('super');
        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $position = $gw_mbanner[$super['mobile_theme']];
        if (!count($position)) {
            $empty = "
                <div class=\"local_desc01 local_desc\">
                    <p class=\"lh6\">
                        {$super['theme']} 스킨의 노출위치 변수가 설정되어있지 않습니다.<br>이 경우는 별도 디자인작업을 통한 변수 설정 누락건이며 담당 디자이너 및 개발자에게 문의하시기 바랍니다.<br>변수 설정파일 경로 : <b>/extend/shop.extend.php</b> 에서 설정 이후 이용 하시기 바랍니다.
                    </p>
                </div>";
            $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'empty' => $empty);
            $this->load->view('admin/design/designEmpty', $data);
            return;
        }
        $page = $this->input->get('page');
        $sca = $this->input->get('sca');
        $sca = $this->input->get('sca');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_banner ";
        $sql_search = " where bn_device = 'mobile' and bn_theme = '{$super['mobile_theme']}' and mb_id = 'admin' ";

        if (isset($sca) && is_numeric($sca)) {
            $sql_search .= " and bn_code = '$sca' ";
        }

        if (!$orderby) {
            $filed = "bn_code";
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
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page - 1) * $rows);

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택수정\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "admin/design?code=mbanner_form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 추가하기</a>";
        $positionTag = "";
        $positionTag .= option_selected('', $sca, '전체');
        for ($i = 0; $i < count($position); $i++)
            $positionTag .= option_selected($position[$i][0], $sca, $position[$i][3]);

        $bn_codeTag = $this->gvcomplex_lib->subject_sort_link('bn_code', $q2);
        $bn_useTag = $this->gvcomplex_lib->subject_sort_link('bn_use', $q2);
        $bn_orderTag = $this->gvcomplex_lib->subject_sort_link('bn_order', $q2);
        $mBanner_listTag = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $bn_id = $row['bn_id'];

            $position = $gw_mbanner[$row['bn_theme']];
            foreach ($position as $key => $value) {
                list($pos, $wpx, $hpx, $subj) = $value;
                if ($pos == $row['bn_code']) break;
            }

            $bimg_str = '';
            $bimg = "./data/banner/{$row['bn_file']}";
            if (is_file($bimg) && $row['bn_file']) {
                $size = @getimagesize($bimg);
                if ($size[0] && $size[0] > 700)
                    $width = 700;
                else
                    $width = $size[0];

                $bimg = base_url() . "data/banner/{$row['bn_file']}";
                $bimg_str = '<img src="' . $bimg . '" width="' . $width . '">';
            }

            $s_upd = "<a href='" . base_url() . "admin/design?code=mbanner_form&w=u&bn_id=$bn_id$qstr&page=$page' class=\"btn_small\">수정</a>";

            $bg = 'list' . ($i % 2);
            $mBanner_listTag .= "
                <tr class=\"{$bg}\">
                    <td rowspan=\"2\">			
                        <input type=\"hidden\" name=\"bn_id[{$i}]\" value=\"{$bn_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td rowspan=\"2\">{$row['bn_code']}</td>
                    <td rowspan=\"2\"><input type=\"checkbox\" name=\"bn_use[{$i}]\" value=\"1\" " . get_checked($row['bn_use'], "1") . "></td>
                    <td rowspan=\"2\"><input type=\"text\" name=\"bn_order[{$i}]\" value=\"{$row['bn_order']}\" class=\"frm_input\"></td>
                    <td class=\"tal\">{$subj}</td>
                    <td><input type=\"text\" name=\"bn_link[{$i}]\" value=\"{$row['bn_link']}\" placeholder=\"URL\" class=\"frm_input\"></td>
                    <td>
                        <select name=\"bn_target[{$i}]\">
                            " . option_selected('_self', $row['bn_target'], "현재창") . "
                            " . option_selected('_blank', $row['bn_target'], "새창") . "
                        </select>
                    </td>	
                    <td><input type=\"text\" name=\"bn_width[{$i}]\" value=\"{$row['bn_width']}\" class=\"frm_input\"></td>
                    <td><input type=\"text\" name=\"bn_height[{$i}]\" value=\"{$row['bn_height']}\" class=\"frm_input\"></td>
                    <td>{$s_upd}</td>
                </tr>
                <tr class=\"{$bg} rows\">
                    <td class=\"td_img_view sbn_img\" colspan=\"6\">
                        <div class=\"sbn_image\">{$bimg_str}</div>
                        <button type=\"button\" class=\"btn_lsmall bx-blue sbn_img_view\">이미지보기</button>
                        <button type=\"button\" class=\"btn_lsmall bx-yellow sbn_all_view\">모두보기</button>
                        <button type=\"button\" class=\"btn_lsmall bx-yellow sbn_all_close\">모두닫기</button>
                    </td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $mBanner_listTag .= '<tbody><tr><td colspan="10" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'positionTag' => $positionTag,
            'q1' => $q1,
            'page' => $page,
            'super' => $super,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'bn_codeTag' => $bn_codeTag,
            'bn_useTag' => $bn_useTag,
            'bn_orderTag' => $bn_orderTag,
            'mBanner_listTag' => $mBanner_listTag,
            'pagination' => $pagination
        );
        $this->load->view('admin/design/mbanner_list', $data);
    }
    private function mbanner_form($code, $headTitle, $headTitleEnd)
    {
        // 배너코드를 얻는다.
        $gw_mbanner = $this->config->item('gw_mbanner');
        $super = $this->load->get_var('super');
        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $position = $gw_mbanner[$super['mobile_theme']];
        if (!count($position)) {
            $empty = "
                <div class=\"local_desc01 local_desc\">
                    <p class=\"lh6\">
                        {$super['theme']} 스킨의 노출위치 변수가 설정되어있지 않습니다.<br>이 경우는 별도 디자인작업을 통한 변수 설정 누락건이며 담당 디자이너 및 개발자에게 문의하시기 바랍니다.<br>변수 설정파일 경로 : <b>/extend/shop.extend.php</b> 에서 설정 이후 이용 하시기 바랍니다.
                    </p>
                </div>";
            $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'empty' => $empty);
            $this->load->view('admin/design/designEmpty', $data);
            return;
        }
        $w = $this->input->get('w');
        $sca = $this->input->get('sca');
        $page = $this->input->get('page');
        $bn_id = $this->input->get('bn_id');
        $bn = array();
        if ($w == "") {
            $bn['bn_width']  = $position[0][1];
            $bn['bn_height'] = $position[0][2];
            $bn['bn_use']    = 1;
            $bn['bn_code']   = 0;
            $bn['bn_order']  = 0;
        } else if ($w == "u") {
            $sql = "select * from shop_banner where bn_id=?";
            $param = array($bn_id);
            $bn    = $this->query_model->returnOneArr($sql, $param);
            if (!$bn['bn_id'])
                alert("존재하지 않은 배너 입니다.");
        }
        $frm_submit = '<div class="btn_confirm">
                <input type="submit" value="저장" class="btn_large" accesskey="s">
                <a href="' . base_url() . 'admin/design?code=mbanner_list' . $qstr . '&page=' . $page . '" class="btn_large bx-white">목록</a>';
        if ($w == 'u')
            $frm_submit .= '<a href="' . base_url() . 'admin/design?code=mbanner_form" class="btn_large bx-red">추가</a>';
        $frm_submit .= '</div>';
        $positionTag = "";
        for ($i = 0; $i < count($position); $i++)
            $positionTag .= option_selected($position[$i][0], $bn['bn_code'], $position[$i][3]);
        $fixHelp = help('[고정] 같은 위치에 고정되어 노출되며 2개이상 등록시 랜덤으로 노출됩니다.<br>[연속] 2개이상 등록시 세로로 연속하여 노출됩니다.<br>[롤링] 2개이상 등록시 슬라이드형식으로 롤링되며 노출됩니다.');

        $bimg_strTag = "";
        $bimg_str = "";
        $bn['bn_file'] = isset($bn['bn_file']) ? $bn['bn_file'] : "";
        $bn['bn_target'] = isset($bn['bn_target']) ? $bn['bn_target'] : "";
        $bn['bn_width'] = isset($bn['bn_width']) ? $bn['bn_width'] : "";
        $bn['bn_height'] = isset($bn['bn_height']) ? $bn['bn_height'] : "";
        $bn['bn_bg'] = isset($bn['bn_bg']) ? $bn['bn_bg'] : "";
        $bn['bn_link'] = isset($bn['bn_link']) ? $bn['bn_link'] : "";
        $bn['bn_bg'] = isset($bn['bn_bg']) ? $bn['bn_bg'] : "";
        $bn['bn_text'] = isset($bn['bn_text']) ? $bn['bn_text'] : "";

        $bimg = './data/banner/' . $bn['bn_file'];
        if (is_file($bimg) && $bn['bn_file']) {
            $size = @getimagesize($bimg);
            if ($size[0] && $size[0] > 700)
                $width = 700;
            else
                $width = $size[0];
            $bimg = base_url() . 'data/banner/' . $bn['bn_file'];
            $bimg = rpc($bimg, '', '');

            $bimg_strTag .= '<input type="checkbox" name="bn_file_del" value="' . $bn['bn_file'] . '" id="bn_file_del"> <label for="bn_file_del">삭제</label>';
            $bimg_str = '<img src="' . $bimg . '" width="' . $width . '">';
        }
        if ($bimg_str) {
            $bimg_strTag .= '<div class="banner_or_img">' . $bimg_str . '</div>';
        }

        $selfTag = option_selected('_self', $bn['bn_target'], "현재창에서");
        $blankTag = option_selected('_blank', $bn['bn_target'], "새창으로");

        $innerHelp = help('[외부링크] http:// 를 포함해 절대경로로 입력해주시기 바랍니다.<br><span class="fc_197">절대경로 예시) http://test.com/shop/listtype?type=1</span>');
        $outerHelp = help('[내부링크] http:// 를 제외한 상대경로로 입력해주시기 바랍니다.<br><span class="fc_197">상대경로 예시) /shop/listtype?type=1</span>');

        $colorHelp = help('"#" 기호없이 색상값 6자만 입력하세요. 예) F6E3FB');
        $bannerHelp = help('특정 배너에만 문구가 노출되는 부분으로 모든 배너에 문구가 노출되지는 않습니다.');
        $rollingHelp = help('롤링 및 연속배너에만 적용됩니다. (고정배너는 의미없음)');

        $get_checkedTag = get_checked($bn['bn_use'], "1");

        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'frm_submit' => $frm_submit,
            'w' => $w,
            'sca' => $sca,
            'page' => $page,
            'bn_id' => $bn_id,
            'positionTag' => $positionTag,
            'position' => $position,
            'fixHelp' => $fixHelp,
            'bimg_strTag' => $bimg_strTag,
            'selfTag' => $selfTag,
            'blankTag' => $blankTag,
            'innerHelp' => $innerHelp,
            'outerHelp' => $outerHelp,
            'bn' => $bn,
            'colorHelp' => $colorHelp,
            'bannerHelp' => $bannerHelp,
            'rollingHelp' => $rollingHelp,
            'get_checkedTag' => $get_checkedTag
        );
        $this->load->view('admin/design/mbanner_form', $data);
    }
    private function logo($code, $headTitle, $headTitleEnd)
    {
        $default = $this->load->get_var('default');

        $mb_id = $this->input->get('mb_id');
        $mb_id = $mb_id ? $mb_id : "admin";
        $sql = "select * from shop_logo where mb_id = ?";
        $param = array($mb_id);
        $lg = $this->query_model->returnOneArr($sql, $param);
        $get_level_select = $this->common_lib->get_level_select('mb_id', 1, 6, $mb_id, "onchange='user_chage(this.value)'");
        $fileTag = "";
        $file = './data/banner/' . $lg['basic_logo'];
        if (is_file($file) && $lg['basic_logo']) {
            $file = base_url() . 'data/banner/' . $lg['basic_logo'];
            $basic_logo = rpc($file, '', '');
            $fileTag = "
                <input type=\"checkbox\" name=\"basic_logo_del\" value=\"1\" id=\"basic_logo_del\">
                <label for=\"basic_logo_del\">삭제</label>
                <div class=\"banner_or_img\"><img src=\"{$basic_logo}\"></div>";
        }
        $sizeHelp = help('권장 사이즈 (' . $default['de_logo_wpx'] . 'px * ' . $default['de_logo_hpx'] . 'px)');

        $mbFileTag = "";
        $file = './data/banner/' . $lg['mobile_logo'];
        if (is_file($file) && $lg['mobile_logo']) {
            $file = base_url() . 'data/banner/' . $lg['mobile_logo'];
            $mobile_logo = rpc($file, '', '');
            $mbFileTag = "
                <input type=\"checkbox\" name=\"mobile_logo_del\" value=\"1\" id=\"mobile_logo_del\">
                <label for=\"mobile_logo_del\">삭제</label>
                <div class=\"banner_or_img\"><img src=\"{$mobile_logo}\"></div>";
        }
        $size2Help = help('권장 사이즈 (' . $default['de_mobile_logo_wpx'] . 'px * ' . $default['de_mobile_logo_hpx'] . 'px)');
        $smsFileTag = "";
        $file = './data/banner/' . $lg['sns_logo'];
        if (is_file($file) && $lg['sns_logo']) {
            $file = base_url() . 'data/banner/' . $lg['sns_logo'];
            $sns_logo = rpc($file, '', '');
            $smsFileTag = "
                <input type=\"checkbox\" name=\"sns_logo_del\" value=\"1\" id=\"sns_logo_del\">
                <label for=\"sns_logo_del\">삭제</label>
                <div class=\"banner_or_img\"><img src=\"{$sns_logo}\"></div>";
        }
        $size3Help = help('최소 사이즈 (200px * 200px)');
        $faviconTag = "";
        $file = './data/banner/' . $lg['favicon_ico'];
        if (is_file($file) && $lg['favicon_ico']) {
            $file = base_url() . 'data/banner/' . $lg['favicon_ico'];
            $favicon_ico = rpc($file, '', '');
            $faviconTag = "
                <img src=\"{$favicon_ico}\" width=\"16\" height=\"16\">
                <input type=\"checkbox\" name=\"favicon_ico_del\" value=\"1\" id=\"favicon_ico_del\">
                <label for=\"favicon_ico_del\">삭제</label>";
        }
        $size4Help = help('고정 사이즈 (16px * 16px)');

        $data = array(
            'lg' => $lg,
            'get_level_select' => $get_level_select,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'fileTag' => $fileTag,
            'sizeHelp' => $sizeHelp,
            'mbFileTag' => $mbFileTag,
            'size2Help' => $size2Help,
            'smsFileTag' => $smsFileTag,
            'size3Help' => $size3Help,
            'faviconTag' => $faviconTag,
            'size4Help' => $size4Help
        );
        $this->load->view('admin/design/logo', $data);
    }
    private function contentlist($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_content ";
        $sql_search = " where 1 ";
        $sql_order  = " order by co_id desc ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        // 테이블의 전체 레코드수만 얻음
        $sql = "select count(*) as cnt $sql_common $sql_search ";
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

        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택수정\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "admin/design?code=contentform\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 추가하기</a>";
        $contentlistTag = "";
        $i = 0;
        foreach ($result as $key => $row) {
            $co_id = $row['co_id'];

            $s_upd = "<a href='" . base_url() . "admin/design?code=contentform&w=u&co_id=$co_id$qstr&page=$page' class=\"btn_small\">수정</a>";
            $page_url = '/bbs/content?co_id=' . $co_id;

            if ($i == 0)
                $contentlistTag .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $contentlistTag .= "
                <tr class=\"{$bg}\">
                    <td>			
                        <input type=\"hidden\" name=\"co_id[{$i}]\" value=\"{$co_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td><input type=\"text\" name=\"co_subject[{$i}]\" value=\"{$row['co_subject']}\" class=\"frm_input\"></td>
                    <td><input type=\"text\" value=\"{$page_url}\" class=\"frm_input list2\" readonly></td>";
            $page_url = base_url() . 'bbs/content?co_id=' . $co_id;
            $contentlistTag .= "
                    <td><a href=\"{$page_url}\" target=\"_blank\" class=\"btn_small grey\">페이지 바로가기</a></td>
                    <td>{$s_upd}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $contentlistTag .= '<tbody><tr><td colspan="5" class="empty_table">자료가 없습니다.</td></tr>';

        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'stx' => $stx,
            'total_count' => $total_count,
            'page' => $page,
            'q1' => $q1,
            'btn_frmline' => $btn_frmline,
            'pagination' => $pagination,
            'contentlistTag' => $contentlistTag
        );
        $this->load->view('admin/design/contentlist', $data);
    }
    private function contentform($headTitle, $headTitleEnd)
    {
        $qstr = $this->load->get_var('qstr');

        $w = $this->input->get('w');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $page = $this->input->get('page');
        $co_id = $this->input->get('co_id');
        $co = array();
        if ($w == "u") {
            $sql = "select * from shop_content where co_id=?";
            $param = array($co_id);
            $co    = $this->query_model->returnOneArr($sql, $param);
            if (!$co['co_id'])
                alert('등록된 자료가 없습니다.');
        }

        $frm_submit = '<div class="btn_confirm">
                <input type="submit" value="저장" class="btn_large" accesskey="s">
                <a href="' . base_url() . 'admin/design?code=contentlist' . $qstr . '&page=' . $page . '" class="btn_large bx-white">목록</a>';
        if ($w == 'u') {
            $frm_submit .= '<a href="' . base_url() . 'admin/design?code=contentform" class="btn_large bx-red">추가</a>';
        }
        $frm_submit .= '</div>';

        $pageUrl = "";
        if ($w == 'u') {
            $pageUrl = "
                <tr>
                    <th scope=\"row\">페이지 URL</th>
                    <td><input type=\"text\" value=\"/bbs/content?co_id={$co_id}\" readonly class=\"frm_input list2\" size=\"60\"> <a href=\"" . base_url() . "bbs/content?co_id={$co_id}\" target=\"_blank\" class=\"btn_small grey\">페이지 바로가기</a></td>
                </tr>";
        }
        $co['co_mobile_content'] = isset($co['co_mobile_content']) ? $co['co_mobile_content'] : "";
        $co['co_content'] = isset($co['co_content']) ? $co['co_content'] : "";
        $co['co_subject'] = isset($co['co_subject']) ? $co['co_subject'] : "";

        $co_contentTag = $this->global_lib->editor_html('co_content', $this->funcomplex_lib->get_text($co['co_content'], 0));
        $co_mb_contentTag = $this->global_lib->editor_html('co_mobile_content', $this->funcomplex_lib->get_text($co['co_mobile_content'], 0));
        $co_contentJs = $this->global_lib->get_editor_js('co_content');
        $co_mb_contentJs = $this->global_lib->get_editor_js('co_mobile_content');
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'w' => $w,
            'sfl' => $sfl,
            'stx' => $stx,
            'page' => $page,
            'co_id' => $co_id,
            'frm_submit' => $frm_submit,
            'pageUrl' => $pageUrl,
            'co_contentTag' => $co_contentTag,
            'co_mb_contentTag' => $co_mb_contentTag,
            'co_contentJs' => $co_contentJs,
            'co_mb_contentJs' => $co_mb_contentJs,
            'co' => $co
        );
        $this->load->view('admin/design/contentform', $data);
    }
    private function menu_form($headTitle, $headTitleEnd)
    {
        $gw_menu = $this->config->item('gw_menu');
        $default = $this->load->get_var('default');
        $gw_menuTag = "";
        for ($i = 0; $i < count($gw_menu); $i++) {
            $seq = ($i + 1);
            $gw_menuTag .= "
                <tr>
                    <td class=\"list1\">메뉴{$seq}</td>
                    <td><input type=\"checkbox\" name=\"de_pname_use_{$seq}\" value=\"1\"" . ($default['de_pname_use_' . $seq] ? ' checked' : '') . "></td>
                    <td><input type=\"text\" name=\"de_pname_{$seq}\" value=\"{$default['de_pname_' .$seq]}\" class=\"frm_input\" placeholder=\"메뉴명\"></td>
                    <td class=\"tal\">{$gw_menu[$i][0]}</td>
                    <td class=\"tal\">{$gw_menu[$i][1]}</td>
                </tr>";
        }
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'gw_menuTag' => $gw_menuTag);
        $this->load->view('admin/design/menu_form', $data);
    }
    private function popup_list($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');

        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_popup ";
        $sql_search = " where mb_id = 'admin' ";
        $sql_order  = " order by index_no desc ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        // 테이블의 전체 레코드수만 얻음
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

        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택수정\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "admin/design?code=popup_form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 추가하기</a>";
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');

        $i = 0;
        $popup_listTag = "";
        foreach ($result as $key => $row) {
            $pp_id = $row['index_no'];
            $s_upd = "<a href='" . base_url() . "admin/design?code=popup_form&w=u&pp_id=$pp_id$qstr&page=$page' class=\"btn_small\">수정</a>";

            if ($i == 0)
                $popup_listTag = '<tbody class="list">';

            $bg = 'list' . $i % 2;
            $popup_listTag .= "
                <tr class=\"{$bg}\">
                    <td>			
                        <input type=\"hidden\" name=\"pp_id[{$i}]\" value=\"{$pp_id}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td>" . $num-- . "</td>
                    <td><input type=\"text\" name=\"title[{$i}]\" value=\"{$row['title']}\" class=\"frm_input\"></td>
                    <td>
                        <select name=\"device[{$i}]\">
                            " . option_selected('both', $row['device'], "모두") . "
                            " . option_selected('pc', $row['device'], "PC") . "
                            " . option_selected('mobile', $row['device'], "모바일") . "
                        </select>
                    </td>
                    <td>{$row['begin_date']}<br>{$row['end_date']}</td>
                    <td>
                        <input type=\"text\" name=\"width[{$i}]\" value=\"{$row['width']}\" class=\"frm_input w50\"> *
                        <input type=\"text\" name=\"height[{$i}]\" value=\"{$row['height']}\" class=\"frm_input w50\">
                    </td>
                    <td>
                        <input type=\"text\" name=\"lefts[{$i}]\" value=\"{$row['lefts']}\" class=\"frm_input w50\"> *
                        <input type=\"text\" name=\"top[{$i}]\" value=\"{$row['top']}\" class=\"frm_input w50\">
                    </td>
                    <td>" . (($row['state'] == '0') ? 'yes' : 'no') . "</td>
                    <td>{$s_upd}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $popup_listTag .= '<tbody><tr><td colspan="9" class="empty_table">자료가 없습니다.</td></tr>';

        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'pagination' => $pagination,
            'btn_frmline' => $btn_frmline,
            'stx' => $stx,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'popup_listTag' => $popup_listTag
        );
        $this->load->view('admin/design/popup_list', $data);
    }
    private function popup_form($headTitle, $headTitleEnd)
    {
        $jqueryUiDatePick = $this->admin_template->datePicker();
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $qstr = $this->load->get_var('qstr');
        $w = $this->input->get('w');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $page = $this->input->get('page');
        $pp_id = $this->input->get('pp_id');
        if ($w == "") {
            $nw['state'] = 0;
            $nw['begin_date'] = $OM_TIME_YMD;
            $nw['device'] = 'both';
        } else if ($w == "u") {
            $sql = "select * from shop_popup where index_no=?";
            $param = array($pp_id);
            $nw = $this->query_model->returnOneArr($sql, $param);
            if (!$nw['index_no'])
                alert("팝업이 존재하지 않습니다.");
        }
        $nw['state'] = isset($nw['state']) ? $nw['state'] : "";
        $nw['begin_date'] = isset($nw['begin_date']) ? $nw['begin_date'] : "";
        $nw['end_date'] = isset($nw['end_date']) ? $nw['end_date'] : "";
        $nw['device'] = isset($nw['device']) ? $nw['device'] : "";
        $nw['width'] = isset($nw['width']) ? $nw['width'] : "";
        $nw['top'] = isset($nw['top']) ? $nw['top'] : "";
        $nw['title'] = isset($nw['title']) ? $nw['title'] : "";
        $nw['memo'] = isset($nw['memo']) ? $nw['memo'] : "";
        $nw['height'] = isset($nw['height']) ? $nw['height'] : "";
        $nw['lefts'] = isset($nw['lefts']) ? $nw['lefts'] : "";
        $deviceArr = array('', '', '');
        $deviceArr[0] = option_selected('both',   $nw['device'], "PC와 모바일");
        $deviceArr[1] = option_selected('pc',     $nw['device'], "PC");
        $deviceArr[2] = option_selected('mobile', $nw['device'], "모바일");

        $stateArr[0] = ($nw['state'] == 0) ? " checked" : "";
        $stateArr[1] = ($nw['state'] == 1) ? " checked" : "";
        $titleTag = $this->funcomplex_lib->get_text($nw['title']);
        $editor_htmlTag = $this->global_lib->editor_html('memo', $this->funcomplex_lib->get_text($nw['memo'], 0));
        $get_editor_js = $this->global_lib->get_editor_js('memo');
        $data = array(
            'w' => $w,
            'sfl' => $sfl,
            'stx' => $stx,
            'page' => $page,
            'pp_id' => $pp_id,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'deviceArr' => $deviceArr,
            'nw' => $nw,
            'stateArr' => $stateArr,
            'titleTag' => $titleTag,
            'editor_htmlTag' => $editor_htmlTag,
            'qstr' => $qstr,
            'get_editor_js' => $get_editor_js,
            'jqueryUiDatePick' => $jqueryUiDatePick
        );
        $this->load->view('admin/design/popup_form', $data);
    }
    #디자인 관리 페이지 끝
    #환경설정(config) 관리 페이지 
    public function config()
    {
        $this->admin_template->admin_access();
        $this->admin_template->admin_head();
        $member = $this->load->get_var('member');
        $ADMIN_MENU10 = $this->config->item('ADMIN_MENU10');
        $ADMIN_MENU10_01 = $this->config->item('ADMIN_MENU10_01');
        $ADMIN_MENU10_02 = $this->config->item('ADMIN_MENU10_02');
        $ADMIN_MENU10_03 = $this->config->item('ADMIN_MENU10_03');
        $ADMIN_MENU10_04 = $this->config->item('ADMIN_MENU10_04');
        $ADMIN_MENU10_05 = $this->config->item('ADMIN_MENU10_05');
        $ADMIN_MENU10_06 = $this->config->item('ADMIN_MENU10_06');
        $ADMIN_MENU10_07 = $this->config->item('ADMIN_MENU10_07');
        $ADMIN_MENU10_08 = $this->config->item('ADMIN_MENU10_08');
        $ADMIN_MENU10_09 = $this->config->item('ADMIN_MENU10_09');
        // $ADMIN_MENU10_10=$this->config->item('ADMIN_MENU10_10');
        // $ADMIN_MENU10_11=$this->config->item('ADMIN_MENU10_11');
        $ADMIN_MENU10_12 = $this->config->item('ADMIN_MENU10_12');
        $ADMIN_MENU10_13 = $this->config->item('ADMIN_MENU10_13');
        $ADMIN_MENU10_14 = $this->config->item('ADMIN_MENU10_14');
        $ADMIN_MENU10_15 = $this->config->item('ADMIN_MENU10_15');
        $ADMIN_MENU10_16 = $this->config->item('ADMIN_MENU10_16');
        $ADMIN_MENU10_17 = $this->config->item('ADMIN_MENU10_17');
        $pg_title = $ADMIN_MENU10;
        $pg_num = 10;
        $snb_icon = "<i class=\"fa fa-cogs\"></i>";

        if ($member['id'] != 'admin' && !$member['auth_' . $pg_num]) {
            alert("접근권한이 없습니다.");
        }
        $pg_title2 = "";
        $code = $this->input->get('code');

        if ($code == "default")            $pg_title2 = $ADMIN_MENU10_01;
        if ($code == "meta")                $pg_title2 = $ADMIN_MENU10_02;
        if ($code == "sns")                $pg_title2 = $ADMIN_MENU10_03;
        if ($code == "register")            $pg_title2 = $ADMIN_MENU10_04;
        if ($code == "sendmail_test")    $pg_title2 = $ADMIN_MENU10_05;
        if ($code == "sms")                $pg_title2 = $ADMIN_MENU10_06;
        if ($code == "supply")            $pg_title2 = $ADMIN_MENU10_07;
        if ($code == "super")            $pg_title2 = $ADMIN_MENU10_08;
        if ($code == "pg")                $pg_title2 = $ADMIN_MENU10_09;
        // if($code == "kakaopay")			$pg_title2 = $ADMIN_MENU10_10;
        // if($code == "naverpay")			$pg_title2 = $ADMIN_MENU10_11;
        if ($code == "baesong")            $pg_title2 = $ADMIN_MENU10_12;
        if ($code == "islandlist")        $pg_title2 = $ADMIN_MENU10_13;
        if ($code == "nicecheck")        $pg_title2 = $ADMIN_MENU10_14;
        if ($code == "ipaccess")            $pg_title2 = $ADMIN_MENU10_15;
        if ($code == "board_group_list")    $pg_title2 = $ADMIN_MENU10_16;
        if ($code == "board_group_form")    $pg_title2 = $ADMIN_MENU10_16;
        if ($code == "board_list")        $pg_title2 = $ADMIN_MENU10_17;
        if ($code == "board_form")        $pg_title2 = $ADMIN_MENU10_17;

        $this->admin_template->admin_topmenu($pg_title, false, $pg_title2, $snb_icon);
        $this->configSkin($code, $pg_title2);
        $this->admin_template->admin_footer();
    }
    private function configSkin($code, $pg_title2)
    {
        $headTitle = "
            <div class=\"s_wrap\">
                <h1>{$pg_title2}</h1>";
        $headTitleEnd = "</div>";
        switch ($code) {
            case 'default':
                $this->defaultFun($code, $headTitle, $headTitleEnd);
                break;
            case 'meta':
                $this->meta($headTitle, $headTitleEnd);
                break;
            case 'sns':
                $this->sns($headTitle, $headTitleEnd);
                break;
            case 'register':
                $this->register($headTitle, $headTitleEnd);
                break;
            case 'sendmail_test':
                $this->sendmail_test($headTitle, $headTitleEnd);
                break;
            case 'sms':
                $this->smsSkin($headTitle, $headTitleEnd);
                break;
            case 'supply':
                $this->supplySkin($headTitle, $headTitleEnd);
                break;
            case 'super':
                $this->super($headTitle, $headTitleEnd);
                break;
            case 'pg':
                $this->pg($headTitle, $headTitleEnd);
                break;
            case 'kakaopay':
                $this->kakaopay($headTitle, $headTitleEnd);
                break;
            case 'naverpay':
                $this->naverpay($headTitle, $headTitleEnd);
                break;
            case 'baesong':
                $this->baesong($headTitle, $headTitleEnd);
                break;
            case 'islandlist':
                $this->islandlist($code, $headTitle, $headTitleEnd);
                break;
            case 'nicecheck':
                $this->nicecheck($headTitle, $headTitleEnd);
                break;
            case 'ipaccess':
                $this->ipaccess($headTitle, $headTitleEnd);
                break;
            case 'board_group_list':
                $this->board_group_list($code, $headTitle, $headTitleEnd);
                break;
            case 'board_group_form':
                $this->board_group_form($headTitle, $headTitleEnd);
                break;
            case 'board_list':
                $this->board_list($code, $headTitle, $headTitleEnd);
                break;
            case 'board_form':
                $this->board_form($headTitle, $headTitleEnd);
                break;
            default:
                break;
        }
    }
    private function defaultFun($code, $headTitle, $headTitleEnd)
    {
        $OM_TIME_YEAR = date("Y", time());
        $config = $this->load->get_var('config');
        $super = $this->load->get_var('super');
        $default = $this->load->get_var('default');
        $company_type = array('', '', '');
        $company_type[0] = radio_checked('company_type', $config['company_type'], '0', '일반과세자');
        $company_type[1] = radio_checked('company_type', $config['company_type'], '1', '간이과세자');
        $company_type[2] = radio_checked('company_type', $config['company_type'], '2', '면세사업자');

        $adminSealTag = "";
        $file = './data/common/' . $config['admin_seal'];
        if (is_file($file) && $config['admin_seal']) {
            $file = base_url() . 'data/common/' . $config['admin_seal'];
            $seal = rpc($file, '', '');
            $adminSealTag = "
                <input type=\"checkbox\" name=\"admin_seal_del\" value=\"1\" id=\"admin_seal_del\" class=\"marl5\">
                <label for=\"admin_seal_del\">삭제</label>
                <img src=\"{$seal}\" width=\"22\" height=\"22\" class=\"marr5\">
                <button type=\"button\" class=\"btn_small wimg_vs\">보기</button>";
        }
        $seal = base_url() . 'data/common/' . $config['admin_seal'];
        $help = help('사업자인감 배경은 투명으로 하셔야 합니다.', 'fc_084');
        $adminRegYesTag = ($config['admin_reg_yes']) ? " checked" : "";
        $get_theme_select = $this->funcomplex_lib->get_theme_select('theme', $super['theme']);
        $get_mobile_theme_select = $this->funcomplex_lib->get_mobile_theme_select('mobile_theme', $super['mobile_theme']);
        $shopIntroYesTag = $config['shop_intro_yes'] ? ' checked' : '';
        $certAdminYesTag = $config['cert_admin_yes'] ? ' checked' : '';
        $certPartnerYesTag = $config['cert_partner_yes'] ? " checked" : "";
        $deReviewWrUseTag = $default['de_review_wr_use'] ? ' checked' : '';
        $deBoardWrUseTag = $default['de_board_wr_use'] ? ' checked' : '';
        $usepoint_yesTag = $config['usepoint_yes'] ? ' checked' : '';
        $usemileage_yesTag = $config['usemileage_yes'] ? ' checked' : '';
        $coupon_yesTag = $config['coupon_yes'] ? ' checked' : '';
        $mouseblock_yesTag = $config['mouseblock_yes'] ? ' checked' : '';
        $gift_yesTag = $config['gift_yes'] ? ' checked' : '';
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'company_type' => $company_type,
            'config' => $config,
            'OM_TIME_YEAR' => $OM_TIME_YEAR,
            'adminSealTag' => $adminSealTag,
            'seal' => $seal,
            'help' => $help,
            'adminRegYesTag' => $adminRegYesTag,
            'get_theme_select' => $get_theme_select,
            'get_mobile_theme_select' => $get_mobile_theme_select,
            'default' => $default,
            'shopIntroYesTag' => $shopIntroYesTag,
            'certAdminYesTag' => $certAdminYesTag,
            'certPartnerYesTag' => $certPartnerYesTag,
            'deReviewWrUseTag' => $deReviewWrUseTag,
            'deBoardWrUseTag' => $deBoardWrUseTag,
            'usepoint_yesTag' => $usepoint_yesTag,
            'usemileage_yesTag' => $usemileage_yesTag,
            'coupon_yesTag' => $coupon_yesTag,
            'mouseblock_yesTag' => $mouseblock_yesTag,
            'gift_yesTag' => $gift_yesTag
        );
        $this->load->view('admin/config/default', $data);
    }
    private function meta($headTitle, $headTitleEnd)
    {
        $config = $this->load->get_var('config');
        $help = array('title' => "", 'auth' => "", 'desc' => "", 'keyword' => "", 'add_meta' => "", 'head_script' => "", 'tail_script' => "");
        $help['title'] = help('타이틀은 인터넷 브라우저 상단에 출력되는 문구이자, 검색엔진의 검색결과에서 제목부분으로 나타납니다.<br>타이틀은 간단명료하면서 페이지 내용에 대한 정보를 제공할 수 있도록작성하는 것이 좋습니다.');
        $help['auth'] = help('페이지 또는 사이트의 제작자명을 명시할 수 있습니다. 간략하게 입력하세요.');
        $help['desc'] = help('검색엔진의 검색결과에서 페이지의 요약내용을 보여주는 부분으로 1-2개의 문장이나 짧은 단락을 사용하는 것이 좋습니다.');
        $help['keyword'] = help('사용자가 많이 검색하는 검색어 및 사이트와 연관된 키워드 정보를 기입합니다. 여러개이면 콤마(,)로 구분하여 입력하시기 바랍니다.');
        $help['add_meta'] = help('추가로 사용하실 meta 태그를 입력합니다. 네이버 사이트소유확인 meta 태그 등');
        $help['head_script'] = help('HTML의 &lt;HEAD&gt; ~ &lt;/HEAD&gt; 태그 사이에 추가될 소스를 설정합니다.<br><span class="fc_red">JAVASCRIPT 태그만 허용되며 그 외 다른 태그는 입력하시면 안됩니다.</span>');
        $help['tail_script'] = help('HTML의 &lt;BODY&gt; ~ &lt;/BODY&gt; 태그 사이에 추가될 소스를 설정합니다. (BODY 후반부에 삽입될 소스 삽입)<br><span class="fc_red">JAVASCRIPT 태그만 허용되며 그 외 다른 태그는 입력하시면 안됩니다.</span>');
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'config' => $config, 'help' => $help);
        $this->load->view('admin/config/meta', $data);
    }
    private function sns($headTitle, $headTitleEnd)
    {
        $default = $this->load->get_var("default");
        $help = array('app' => "", 'kakao' => '', 'short' => "");
        $help['app'] = help('앱설정시 Callback URL에 http://도메인주소/plugin/login_oauth/login_with_naver 입력');
        $help['kakao'] = help('카카오 사이트 설정에서 플랫폼 > Redirect Path에 /plugin/login_oauth/login_with_kakao 라고 입력');
        $help['short'] = help('트위터, 페이스북과 같은 SNS가 유행하면서 제한된 문자수를 극복하기 위해 또는 너무 길어<br>지저분해 보이는 URL을 짧고 간결하게 표시하기 위한 짧은 URL(Short URL Service) 서비스입니다.<br><span class="fc_084">위 "API Key 발급받기" 버튼을 클릭 후 접속하셔서 API Key를 발급받습니다.<br>입력하지 않으면 실제 URL을 보내게 됩니다.</span><br>짧은주소 변환 후 <span class="fc_red">예시) http://goo.gl/bmjqtY</span>');
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'default' => $default, 'help' => $help);
        $this->load->view('admin/config/sns', $data);
    }
    private function register($headTitle, $headTitleEnd)
    {
        $config = $this->load->get_var('config');
        $register_use_hp = $config['register_use_hp'] ? ' checked' : '';
        $register_req_hp = $config['register_req_hp'] ? ' checked' : '';
        $register_use_tel = $config['register_use_tel'] ? ' checked' : '';
        $register_req_tel = $config['register_req_tel'] ? ' checked' : '';
        $register_use_addr = $config['register_use_addr'] ? ' checked' : '';
        $register_req_addr = $config['register_req_addr'] ? ' checked' : '';
        $help = array('word' => "", 'mail' => "");
        $help['word'] = help('입력된 단어가 포함된 내용은 회원아이디로 사용할 수 없습니다. 단어와 단어 사이는 , 로 구분합니다.');
        $help['mail'] = help('hanmail.net과 같은 메일 주소는 입력을 못합니다. 엔터로 구분합니다.');
        $shop_provision = preg_replace("/\\\/", "", $config['shop_provision']);
        $shop_private = preg_replace("/\\\/", "", $config['shop_private']);
        $shop_marketing = preg_replace("/\\\/", "", $config['shop_marketing']);
        $shop_policy = preg_replace("/\\\/", "", $config['shop_policy']);
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'config' => $config,
            'help' => $help,
            'register_use_hp' => $register_use_hp,
            'register_req_hp' => $register_req_hp,
            'register_use_tel' => $register_use_tel,
            'register_req_tel' => $register_req_tel,
            'register_use_addr' => $register_use_addr,
            'register_req_addr' => $register_req_addr,
            'shop_provision' => $shop_provision,
            'shop_private' => $shop_private,
            'shop_marketing' => $shop_marketing,
            'shop_policy' => $shop_policy
        );
        $this->load->view('admin/config/register', $data);
    }
    private function sendmail_test($headTitle, $headTitleEnd)
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');
        $super = $this->load->get_var('super');
        include_once("./lib/mailer.lib.php");
        $frm_msg = '';
        if (isset($_POST['email'])) {
            $email = explode(',', $_POST['email']);
            for ($i = 0; $i < count($email); $i++)
                mailer($config['company_name'], $super['email'], trim($email[$i]), '[메일검사] 제목', '<span style="font-size:9pt;">[메일검사] 내용<p>이 내용이 제대로 보인다면 보내는 메일 서버에는 이상이 없는것입니다.<p>' . $OM_TIME_YMDHIS . '<p>이 메일 주소로는 회신되지 않습니다.</span>', 1);

            $frm_msg .= '<h2>결과 메세지</h2>';
            $frm_msg .= '<div class="local_desc01 local_desc"><p>';
            $frm_msg .= '다음 ' . count($email) . '개의 메일 주소로 테스트 메일 발송이 완료되었습니다.';
            $frm_msg .= '</p></div>';
            $frm_msg .= '<ul class="marb10">';
            for ($i = 0; $i < count($email); $i++) {
                $frm_msg .= '<li>&#8226; ' . $email[$i] . '</li>';
            }
            $frm_msg .= '</ul>';
            $frm_msg .= '<div class="local_desc02 local_desc padb10 bb"><p>';
            $frm_msg .= '해당 주소로 테스트 메일이 도착했는지 확인해 주십시오.<br>';
            $frm_msg .= '만약, 테스트 메일이 오지 않는다면 더 다양한 계정의 메일 주소로 메일을 보내 보십시오.<br>';
            $frm_msg .= '그래도 메일이 하나도 도착하지 않는다면 메일 서버(sendmail server)의 오류일 가능성이 높으니, 웹 서버관리자에게 문의하여 주십시오.<br>';
            $frm_msg .= '</p></div>';
        }
        $helpTag = help('여러곳으로 메일을 발송하시려면 콤마(,)로 메일주소를 구분 하십시오.<br>여러곳으로 메일발송시 <em class="fc_084">예) abc@naver.com,abc@daum.net,abc@nate.com</em>');
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'helpTag' => $helpTag, 'frm_msg' => $frm_msg, 'member' => $member, 'OM_TIME_YMDHIS' => $OM_TIME_YMDHIS);
        $this->load->view('admin/config/sendmail_test', $data);
    }
    private function smsSkin($headTitle, $headTitleEnd)
    {
        $sms = $this->global_lib->get_sms('admin');
        // 문자로 사용자 정보
        $userinfo = get_moonjaro_userinfo($sms['cf_moonjaro_key']);
        $cf_moonjaro_location = "";
        if (!$sms['cf_moonjaro_location']) {
            $cf_moonjaro_location = "<script>alert('문자발송 api 주소를 db에 입력해주세요.');</script>";
        }
        $seq_sms = array(
            array("", "", "", ""),
            array(
                "회원가입",
                "이름 : <b>{이름}</b>, 아이디: <b>{아이디}</b>",
                "",
                "사이트 회원가입 즉시 문자발송"
            ),
            array(
                "주문완료",
                "이름 : <b>{이름}</b>, 주문번호: <b>{주문번호}</b>",
                "",
                "상품 주문완료시 문자발송"
            ),
            array(
                "입금확인 (무통장)",
                "이름 : <b>{이름}</b>, 주문번호: <b>{주문번호}</b>",
                "",
                "입금확인 후 상품준비중으로 변경시"
            ),
            array(
                "상품발주",
                "이름 : <b>{이름}</b>, 주문번호: <b>{주문번호}</b>",
                "업체 : <b>{업체}</b>, 송장번호: <b>{송장번호}</b>",
                "주문상품을 배송중으로 단계 변경시"
            ),
            array(
                "주문취소",
                "이름 : <b>{이름}</b>, 주문번호: <b>{주문번호}</b>",
                "",
                "주문상품을 취소할 경우"
            ),
            array(
                "배송완료",
                "이름 : <b>{이름}</b>, 주문번호: <b>{주문번호}</b>",
                "업체 : <b>{업체}</b>, 송장번호: <b>{송장번호}</b>",
                "주문상품을 배송완료 단계 변경시"
            )
        );
        $cf_sms_typeJs = "";
        if ($sms['cf_sms_type'] == 'LMS') {
            $cf_sms_typeJs = "
                    if(cnt > 90)
                        max_bytes.innerHTML = 2000;
                    else
                        max_bytes.innerHTML = 90;
                
                    if(cnt > 2000){
                        exceed = cnt - 2000;
                        alert('메시지 내용은 2000바이트를 넘을수 없습니다.\\n\\n작성하신 메세지 내용은 '+ exceed +'byte가 초과되었습니다.\\n\\n초과된 부분은 자동으로 삭제됩니다.');
                        var tcnt = 0;
                        var xcnt = 0;
                        var tmp = conts.value;
                        for(i=0; i<tmp.length; i++){
                            ch = tmp.charAt(i);
                            if(escape(ch).length > 4) {
                                tcnt += 2;
                            } else {
                                tcnt += 1;
                            }
                
                            if(tcnt > 2000) {
                                tmp = tmp.substring(0,i);
                                break;
                            } else {
                                xcnt = tcnt;
                            }
                        }
                        conts.value = tmp;
                        bytes.innerHTML = xcnt;
                        return;
                    }
                ";
        } else {
            $cf_sms_typeJs = "
                if(cnt > 80){
                    exceed = cnt - 80;
                    alert('메시지 내용은 80바이트를 넘을수 없습니다.\\n\\n작성하신 메세지 내용은 '+ exceed +'byte가 초과되었습니다.\\n\\n초과된 부분은 자동으로 삭제됩니다.');
                    var tcnt = 0;
                    var xcnt = 0;
                    var tmp = conts.value;
                    for(i=0; i<tmp.length; i++)
                    {
                        ch = tmp.charAt(i);
                        if(escape(ch).length > 4) {
                            tcnt += 2;
                        } else {
                            tcnt += 1;
                        }
            
                        if(tcnt > 80) {
                            tmp = tmp.substring(0,i);
                            break;
                        } else {
                            xcnt = tcnt;
                        }
                    }
                    conts.value = tmp;
                    bytes.innerHTML = xcnt;
                    return;
                }";
        }
        $cf_sms_useArr = array('', '');
        $cf_sms_useArr[0] = option_selected('0', $sms['cf_sms_use'], '사용안함');
        $cf_sms_useArr[1] = option_selected('1', $sms['cf_sms_use'], '사용함');

        $cf_sms_typeArr = array('', '');
        $cf_sms_typeArr[0] = option_selected('SMS', $sms['cf_sms_type'], 'SMS');
        $cf_sms_typeArr[1] = option_selected('LMS', $sms['cf_sms_type'], 'LMS');
        $priceSms = help('요금은 건당 SMS는 ' . $userinfo['price_sms'] . '원, LMS는 ' . $userinfo['price_lms'] . '원입니다.');
        $moonjaroApi = help('문자로에서 받으신 인증키를 입력합니다.');

        $cf_sms_recall = '1600-0000';
        $msg = false;
        if ($userinfo['phone'] && $cf_sms_recall != $userinfo['phone']) {
            $msg = true;
            if ($sms['cf_sms_recall'] == '1600-0000') {
                $cf_sms_recall = $userinfo['phone'];
            } else {
                $cf_sms_recall = $sms['cf_sms_recall'];
            }
        }
        $msgHelp = ($msg) ? help('메세지 발송시 발신번호로 찍히는 전화번호입니다. 발신번호는 문자로에 등록된 번호와 동일해야 합니다.') : '';
        $saveBtnMsg = ($sms['cf_sms_recall'] == '1600-0000' && $userinfo['phone']) ? '<p style="color:red">발신번호를 자동으로 입력하였습니다. 하단 저장버튼을 눌러서 저장해주세요.</p>' : '';
        $registerMsg = (isset($userinfo['phone']) && $userinfo['phone'] == '') ? '<p style="color:red">발신번호가 없습니다. 문자로에서 발신번호 등록을 해주세요.</p>' : '';
        $paymentStr = "";
        switch ($userinfo['payment']) {
            case 'A':
                $paymentStr = '선불';
                break;
            case 'C':
                $paymentStr = '후불';
                break;
            default:
                $paymentStr = '가입해주세요.';
                break;
        }
        $paymentAtype = "";
        if ($userinfo['payment'] == 'A') {
            $paymentAtype = "
                <tr>
                    <th scope=\"row\">충전 잔액</th>
                    <td>
                        " . number_format($userinfo['coin']) . " 원
                        " . help($userinfo['acc_bank'] . ' ' . substr($userinfo['acc_number'], 0, 3) . '-' . substr($userinfo['acc_number'], 3, 3) . '-' . substr($userinfo['acc_number'], 6, 8)) . "
                    </td>
                </tr>";
        }
        $smsLimitHelp = help('주의! 80 bytes 까지만 전송됩니다. (영문 한글자 : 1byte , 한글 한글자 : 2bytes , 특수문자의 경우 1 또는 2 bytes 임)');

        $msgConfigStr = "";
        for ($i = 1; $i <= 6; $i = $i + 2) {
            $k = $i + 1;
            $msgConfigStr .= "
                    <tr>
                        <td>
                            <div class=\"scf_sms_img\">
                                <textarea name=\"cf_cont{$i}\" id=\"cf_cont{$i}\" onkeyup=\"byte_check('cf_cont{$i}', 'byte{$i}', 'max_byte{$i}');\">" . $sms["cf_cont{$i}"] . "</textarea>
                                <p class=\"scf_sms_byte\"><span id=\"byte{$i}\">0</span> / <span id=\"max_byte{$i}\">" . ($sms['cf_sms_type'] == 'LMS' ? 90 : 80) . "</span> byte</p>
                            </div>
                        </td>
                        <td class=\"vat td_label\">
                            <div class=\"scf_sms_wrap\">
                                <p class=\"tit\">{$seq_sms[$i][0]}</p>
                                <p>
                                    <input type=\"checkbox\" name=\"cf_mb_use{$i}\" value=\"1\" id=\"cf_mb_use{$i}\"" . get_checked($sms["cf_mb_use{$i}"], "1") . "> <label for=\"cf_mb_use{$i}\">고객</label>
                                    <input type=\"checkbox\" name=\"cf_ad_use{$i}\" value=\"1\" id=\"cf_ad_use{$i}\"" . get_checked($sms["cf_ad_use{$i}"], "1") . "> <label for=\"cf_ad_use{$i}\">관리자</label>";
            if ($i > 1) {
                $msgConfigStr .= "<input type=\"checkbox\" name=\"cf_sr_use{$i}\" value=\"1\" id=\"cf_sr_use{$i}\"" . get_checked($sms["cf_sr_use{$i}"], "1") . "> <label for=\"cf_sr_use{$i}\">입점사</label>";
            }
            $msgConfigStr .= "
                                    </p>
                                    <p>{$seq_sms[$i][1]}</p>";
            if ($seq_sms[$i][2]) {
                $msgConfigStr .= "
                                    <p>{$seq_sms[$i][2]}</p>";
            }
            if ($seq_sms[$i][3]) {
                $msgConfigStr .= "
                                    <p>{$seq_sms[$i][3]}</p>";
            }
            $msgConfigStr .= "
                            </div>
                        </td>
                        <td>
                            <div class=\"scf_sms_img\">
                                <textarea name=\"cf_cont{$k}\" id=\"cf_cont{$k}\" onkeyup=\"byte_check('cf_cont{$k}', 'byte{$k}', 'max_byte{$k}');\">" . $sms["cf_cont{$k}"] . "</textarea>
                                <p class=\"scf_sms_byte\"><span id=\"byte{$k}\">0</span> / <span id=\"max_byte{$k}\">" . ($sms['cf_sms_type'] == 'LMS' ? 90 : 80) . "</span> byte</p>
                            </div>
                        </td>
                        <td class=\"vat td_label\">
                            <div class=\"scf_sms_wrap\">
                                <p class=\"tit\">{$seq_sms[$k][0]}</p>
                                <p>
                                    <input type=\"checkbox\" name=\"cf_mb_use{$k}\" value=\"1\" id=\"cf_mb_use{$k}\"" . get_checked($sms["cf_mb_use{$k}"], "1") . "> <label for=\"cf_mb_use{$k}\">고객</label>
                                    <input type=\"checkbox\" name=\"cf_ad_use{$k}\" value=\"1\" id=\"cf_ad_use{$k}\"" . get_checked($sms["cf_ad_use{$k}"], "1") . "> <label for=\"cf_ad_use{$k}\">관리자</label>
                                    <input type=\"checkbox\" name=\"cf_sr_use{$k}\" value=\"1\" id=\"cf_sr_use{$k}\"" . get_checked($sms["cf_sr_use{$k}"], "1") . "> <label for=\"cf_sr_use{$k}\">입점사</label>
                                </p>
                                <p>{$seq_sms[$k][1]}</p>";
            if ($seq_sms[$k][2]) {
                $msgConfigStr .= "
                                    <p>{$seq_sms[$k][2]}</p>";
            }
            if ($seq_sms[$k][3]) {
                $msgConfigStr .= "
                                    <p>{$seq_sms[$k][3]}</p>";
            }
            $msgConfigStr .= "
                            </div>
                        </td>
                    </tr>
                    <script>
                        byte_check('cf_cont{$i}','byte{$i}','max_byte{$i}');
                        byte_check('cf_cont{$k}','byte{$k}','max_byte{$k}');
                    </script>
                ";
        }

        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'cf_moonjaro_location' => $cf_moonjaro_location,
            'cf_sms_typeJs' => $cf_sms_typeJs,
            'cf_sms_useArr' => $cf_sms_useArr,
            'cf_sms_typeArr' => $cf_sms_typeArr,
            'priceSms' => $priceSms,
            'moonjaroApi' => $moonjaroApi,
            'sms' => $sms,
            'cf_sms_recall' => $cf_sms_recall,
            'msgHelp' => $msgHelp,
            'saveBtnMsg' => $saveBtnMsg,
            'registerMsg' => $registerMsg,
            'paymentStr' => $paymentStr,
            'paymentAtype' => $paymentAtype,
            'smsLimitHelp' => $smsLimitHelp,
            'msgConfigStr' => $msgConfigStr
        );
        $this->load->view('admin/config/sms', $data);
    }
    private function supplySkin($headTitle, $headTitleEnd)
    {
        $config = $this->load->get_var('config');

        $seller_reg_yesArr = array('', '');
        $seller_reg_yesArr[1] = ($config['seller_reg_yes'] == 1) ? ' checked' : '';
        $seller_reg_yesArr[0] = ($config['seller_reg_yes'] == 0) ? ' checked' : '';

        $seller_reg_autoArr = array('', '');
        $seller_reg_autoArr[1] = ($config['seller_reg_auto'] == 1) ? ' checked' : '';
        $seller_reg_autoArr[0] = ($config['seller_reg_auto'] == 0) ? ' checked' : '';

        $seller_mod_autoArr = array('', '');
        $seller_mod_autoArr[1] = ($config['seller_mod_auto'] == 1) ? ' checked' : '';
        $seller_mod_autoArr[0] = ($config['seller_mod_auto'] == 1) ? ' checked' : '';

        $seller_reg_agreeStr = preg_replace("/\\\/", "", $config['seller_reg_agree']);

        $seller_reg_guideTag = $this->global_lib->editor_html('seller_reg_guide', $this->funcomplex_lib->get_text(stripslashes($config['seller_reg_guide']), 0));
        $seller_reg_mb_guideTag = $this->global_lib->editor_html('seller_reg_mobile_guide', $this->funcomplex_lib->get_text(stripslashes($config['seller_reg_mobile_guide']), 0));

        $get_editor_js = $this->global_lib->get_editor_js('seller_reg_guide');
        $get_editor_mb_js = $this->global_lib->get_editor_js('seller_reg_mobile_guide');
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'config' => $config,
            'seller_reg_yesArr' => $seller_reg_yesArr,
            'seller_reg_autoArr' => $seller_reg_autoArr,
            'seller_mod_autoArr' => $seller_mod_autoArr,
            'seller_reg_agreeStr' => $seller_reg_agreeStr,
            'seller_reg_guideTag' => $seller_reg_guideTag,
            'seller_reg_mb_guideTag' => $seller_reg_mb_guideTag,
            'get_editor_js' => $get_editor_js,
            'get_editor_mb_js' => $get_editor_mb_js
        );
        $this->load->view('admin/config/supply', $data);
    }
    private function super($headTitle, $headTitleEnd)
    {
        $super = $this->load->get_var('super');
        $help['pwd'] = help("비밀번호는 영문자, 숫자를 포함하여 최소 6자이상 입력하세요.<br>비밀번호는 상점 관리에 매우 중요하므로 상점 관리자외 정보유출을 주의하시고 정기적으로 비밀번호를 변경하세요.");
        $help['pwd2'] = help("비밀번호를 한번 더 입력해주세요.");
        $help['mail'] = help("회원 메일발송시 사용되므로 실제 사용중인 메일주소를 입력하세요.");
        $mailser = array('y' => "", 'n' => "");
        $mailser['y'] = option_selected('Y', $super['mailser'], '수신함');
        $mailser['n'] = option_selected('N', $super['mailser'], '수신안함');

        $smsser = array('y' => "", 'n' => "");
        $smsser['y'] = option_selected('Y', $super['smsser'], '수신함');
        $smsser['n'] = option_selected('N', $super['smsser'], '수신안함');
        $lastLogin = (!is_null_time($super['today_login'])) ? $super['today_login'] : '';
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'mailser' => $mailser, 'help' => $help, 'lastLogin' => $lastLogin, 'super' => $super, 'smsser' => $smsser);
        $this->load->view('admin/config/super', $data);
    }
    private function pg($headTitle, $headTitleEnd)
    {
        $default = $this->load->get_var('default');
        $de_bank_useStr = get_checked($default['de_bank_use'], "1");
        $de_card_useStr = get_checked($default['de_card_use'], "1");
        $de_iche_useStr = get_checked($default['de_iche_use'], "1");
        $de_kakao_useStr = get_checked($default['de_kakao_use'], "1");
        $de_vbank_useStr = get_checked($default['de_vbank_use'], "1");
        $de_hp_useStr = get_checked($default['de_hp_use'], "1");
        $de_card_useStr0 = get_checked($default['de_card_test'], "0");
        $de_card_useStr1 = get_checked($default['de_card_test'], "1");
        $help['pg'] = help('PG사의 결제 테스트를 하실 경우에 체크하세요. 결제단위 최소 1,000원');

        $optArr['kcp'] = option_selected("kcp", $default['de_pg_service'], "NHN KCP");
        $optArr['lg'] = option_selected("lg",  $default['de_pg_service'], "LG유플러스");
        $optArr['inicis'] = option_selected("inicis", $default['de_pg_service'], "KG이니시스");
        $help['service'] = help('쇼핑몰에서 사용할 결제대행사를 선택합니다.');

        $de_tax_flag_use = get_checked($default['de_tax_flag_use'], "1");
        $help['tax'] = help("복합과세(과세, 비과세) 결제를 사용하려면 체크하십시오.<br>복합과세 결제를 사용하기 전 PG사에 별도로 결제 신청을 해주셔야 합니다. 사용시 PG사로 문의하여 주시기 바랍니다.");

        $de_taxsave_use = get_checked($default['de_taxsave_use'], "1");
        $help['taxsave'] = help("현금영수증 발급 취소는 PG사에서 지원하는 현금영수증 취소 기능을 사용하시기 바랍니다.");

        $de_card_noint_use = get_checked($default['de_card_noint_use'], "1");
        $help['credit'] = help("주문시 신용카드 무이자할부를 가능하게 할것인지를 설정합니다.<br>사용함으로 설정하시면 PG사 가맹점 관리자 페이지에서 설정하신 무이자할부 설정이 적용됩니다.<br>사용안함으로 설정하시면 PG사 무이자 이벤트 카드를 제외한 모든 카드의 무이자 설정이 적용되지 않습니다.");

        $de_easy_pay_use = get_checked($default['de_easy_pay_use'], "1");
        $help['easypay'] = help("주문서 작성 페이지에 PG사 간편결제(PAYCO, PAYNOW, KPAY) 버튼의 별도 사용 여부를 설정합니다.");

        $help['nhn'] = help('NHN KCP에서 발급받으신 사이트코드를 입력해 주세요.');
        $help['siteKey'] = help("25자리 영대소문자와 숫자 - 그리고 _ 로 이루어 집니다. SITE KEY 발급 NHN KCP 전화: 1544-8660<br>예) 1Q9YRV83gz6TukH8PjH0xFf__");
        $help['nhnAdmin'] = help('위 주소를 <strong><a href="http://admin.kcp.co.kr" target="_blank">NHN KCP 관리자</a> &gt; 상점정보관리 &gt; 정보변경 &gt; 공통URL 정보 &gt; 공통URL 변경</strong>후에 입력하셔야 자동으로 입금 통보됩니다.');
        $help['mertKey'] = help('상점MertKey는 LG유플러스 상점관리자 <strong>[계약정보 &gt; 상점정보관리 &gt; 시스템연동정보]</strong>에서 확인하실 수 있습니다.');
        $help['kgMid'] = help('KG이니시스로 부터 발급 받으신 상점아이디(MID)를 입력해 주십시오.');
        $help['inicisKey'] = help('KG이니시스에서 발급받은 4자리 상점 키패스워드를 입력합니다.<br>KG이니시스 상점관리자 패스워드와 관련이 없습니다.<br>키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.');
        $help['signKey'] = help('KG이니시스에서 발급받은 웹결제 사인키를 입력합니다.<br>관리자 페이지의 상점정보 > 계약정보 > 부가정보의 웹결제 signkey생성 조회 버튼 클릭, 팝업창에서 생성 버튼 클릭 후 해당 값을 입력합니다.');
        $de_samsung_pay_use = get_checked($default['de_samsung_pay_use'], "1");
        $help['samsungBtn'] = help('주문서 작성 페이지에 KG이니시스 삼성페이 버튼의 별도 사용 여부를 설정합니다.');
        $help['kgInicisAdmin'] = help('위 주소를 <strong><a href="https://iniweb.inicis.com" target="_blank">KG이니시스 관리자</a> &gt; 거래조회 &gt; 가상계좌 &gt; 입금통보방식선택 &gt; URL 수신 설정</strong>에 입력하셔야 자동으로 입금 통보됩니다.');

        $de_escrow_use0 = get_checked($default['de_escrow_use'], "0");
        $de_escrow_use1 = get_checked($default['de_escrow_use'], "1");

        $help['escrow'] = help('에스크로 결제를 사용하시려면, 반드시 결제대행사 상점 관리자 페이지에서 에스크로 서비스를 신청하신 후 사용하셔야 합니다.<br>에스크로 사용시 배송과의 연동은 되지 않으며 에스크로 결제만 지원됩니다.');
        $bank = unserialize($default['de_bank_account']);
        $limit = 5;
        $bankStr = "";
        for ($i = 0; $i < $limit; $i++) {
            $k = ($i + 1);
            $bank[$i]['name'] = isset($bank[$i]['name']) ? $bank[$i]['name'] : "";
            $bank[$i]['account'] = isset($bank[$i]['account']) ? $bank[$i]['account'] : "";
            $bank[$i]['holder'] = isset($bank[$i]['holder']) ? $bank[$i]['holder'] : "";
            $bankStr .= "
                <tr>
                    <th scope=\"row\">은행계좌번호 {$k}</th>
                    <td>
                        <input type=\"text\" name=\"bank_name[]\" value=\"{$bank[$i]['name']}\" class=\"frm_input\" size=\"15\" placeholder=\"은행명 {$k}\">
                        <input type=\"text\" name=\"bank_account[]\" value=\"{$bank[$i]['account']}\" class=\"frm_input\" size=\"30\" placeholder=\"계좌번호 {$k}\">
                        <input type=\"text\" name=\"bank_holder[]\" value=\"{$bank[$i]['holder']}\" class=\"frm_input\" size=\"15\" placeholder=\"예금주명 {$k}\">
                    </td>
                </tr>";
        }
        $de_pg_serviceJs = "";
        if ($default['de_pg_service']) {
            $de_pg_serviceJs = "$(\".{$default['de_pg_service']}_info_fld\").show();";
        } else {
            $de_pg_serviceJs = "$(\".kcp_info_fld\").show();";
        }

        $de_useJs = "";
        // 결제모듈 실행권한 체크
        if ($default['de_iche_use'] || $default['de_vbank_use'] || $default['de_hp_use'] || $default['de_card_use']) {
            // kcp의 경우 pp_cli 체크
            if ($default['de_pg_service'] == 'kcp') {
                if (!extension_loaded('openssl')) {
                    $de_useJs .= '<script>';
                    $de_useJs .= 'alert("PHP openssl 확장모듈이 설치되어 있지 않습니다.\n모바일 쇼핑몰 결제 때 사용되오니 openssl 확장 모듈을 설치하여 주십시오.");';
                    $de_useJs .= '</script>';
                }
                if (!extension_loaded('soap') || !class_exists('SOAPClient')) {
                    $de_useJs .= '<script>';
                    $de_useJs .= 'alert("PHP SOAP 확장모듈이 설치되어 있지 않습니다.\n모바일 쇼핑몰 결제 때 사용되오니 SOAP 확장 모듈을 설치하여 주십시오.");';
                    $de_useJs .= '</script>';
                }

                $is_linux = true;
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                    $is_linux = false;

                $exe = '/kcp/bin/';
                if ($is_linux) {
                    if (PHP_INT_MAX == 2147483647) // 32-bit
                        $exe .= 'pp_cli';
                    else
                        $exe .= 'pp_cli_x64';
                } else {
                    $exe .= 'pp_cli_exe.exe';
                }

                $de_useJs .= module_exec_check('./shop' . $exe, 'pp_cli');

                // shop/kcp/log 디렉토리 체크 후 있으면 경고
                if (is_dir('./shop/kcp/log') && is_writable('./shop/kcp/log')) {
                    $de_useJs .= '<script>';
                    $de_useJs .= 'alert("웹접근 가능 경로에 log 디렉토리가 있습니다.\nlog 디렉토리를 웹에서 접근 불가능한 경로로 변경해 주십시오.")';
                    $de_useJs .= '</script>';
                }
            }

            // LG의 경우 log 디렉토리 체크
            if ($default['de_pg_service'] == 'lg') {
                $log_path = './plugin/lgxpay/lgdacom/log';

                if (!is_dir($log_path)) {
                    $de_useJs .= '<script>';
                    $de_useJs .= 'alert("' . str_replace('/', '', './plugin/lgxpay') . '/lgdacom 폴더 안에 log 폴더를 생성하신 후 쓰기권한을 부여해 주십시오.\n> mkdir log\n> chmod 707 log");';
                    $de_useJs .= '</script>';
                } else {
                    if (!is_writable($log_path)) {
                        $de_useJs .= '<script>';
                        $de_useJs .= 'alert("' . str_replace('./', '', $log_path) . ' 폴더에 쓰기권한을 부여해 주십시오.\n> chmod 707 log");';
                        $de_useJs .= '</script>';
                    }
                }
            }

            // 이니시스의 경우 log 디렉토리 체크
            if ($default['de_pg_service'] == 'inicis') {
                if (!function_exists('xml_set_element_handler')) {
                    $de_useJs .=  '<script>';
                    $de_useJs .=  'alert("XML 관련 함수를 사용할 수 없습니다.\n서버 관리자에게 문의해 주십시오.");';
                    $de_useJs .=  '</script>';
                }

                if (!function_exists('openssl_get_publickey')) {
                    $de_useJs .= '<script>';
                    $de_useJs .= 'alert("OPENSSL 관련 함수를 사용할 수 없습니다.\n서버 관리자에게 문의해 주십시오.");';
                    $de_useJs .= '</script>';
                }

                if (!function_exists('socket_create')) {
                    $de_useJs .= '<script>';
                    $de_useJs .= 'alert("SOCKET 관련 함수를 사용할 수 없습니다.\n서버 관리자에게 문의해 주십시오.");';
                    $de_useJs .= '</script>';
                }

                if (!function_exists('mcrypt_module_open')) {
                    $de_useJs .= '<script>';
                    $de_useJs .= 'alert("MCRYPT 관련 함수를 사용할 수 없습니다.\n서버 관리자에게 문의해 주십시오.");';
                    $de_useJs .= '</script>';
                }

                $log_path = './shop/inicis/log';

                if (!is_dir($log_path)) {
                    $de_useJs .= '<script>';
                    $de_useJs .= 'alert("' . str_replace('/', '', './shop') . '/inicis 폴더 안에 log 폴더를 생성하신 후 쓰기권한을 부여해 주십시오.\n> mkdir log\n> chmod 707 log");';
                    $de_useJs .= '</script>';
                } else {
                    if (!is_writable($log_path)) {
                        $de_useJs .= '<script>';
                        $de_useJs .= 'alert("' . str_replace('/', '', $log_path) . ' 폴더에 쓰기권한을 부여해 주십시오.\n> chmod 707 log");';
                        $de_useJs .= '</script>';
                    }
                }
            }
        }


        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'de_bank_useStr' => $de_bank_useStr,
            'de_card_useStr' => $de_card_useStr,
            'default' => $default,
            'de_iche_useStr' => $de_iche_useStr,
            'de_vbank_useStr' => $de_vbank_useStr,
            'de_hp_useStr' => $de_hp_useStr,
            'de_card_useStr0' => $de_card_useStr0,
            'de_card_useStr1' => $de_card_useStr1,
            'help' => $help,
            'optArr' => $optArr,
            'de_tax_flag_use' => $de_tax_flag_use,
            'de_taxsave_use' => $de_taxsave_use,
            'de_card_noint_use' => $de_card_noint_use,
            'de_easy_pay_use' => $de_easy_pay_use,
            'de_samsung_pay_use' => $de_samsung_pay_use,
            'de_escrow_use0' => $de_escrow_use0,
            'de_escrow_use1' => $de_escrow_use1,
            'bankStr' => $bankStr,
            'de_pg_serviceJs' => $de_pg_serviceJs,
            'de_useJs' => $de_useJs,
            'de_kakao_useStr' => $de_kakao_useStr
        );
        $this->load->view('admin/config/pg', $data);
    }
    private function kakaopay($headTitle, $headTitleEnd)
    {
        $default = $this->load->get_var('default');
        $help = array('admin' => '', 'code' => '');
        $help['admin'] = help("카카오페이로 부터 발급 받으신 Admin키 를 입력 합니다.");
        $help['code'] = help("카카오페이로 부터 발급 받으신 가맹점 코드(10자리)를 입력합니다.");
        $default['de_kakaopay_admin_key'] = isset($default['de_kakaopay_admin_key']) ? $default['de_kakaopay_admin_key'] : "";
        $default['de_kakaopay_cid'] = isset($default['de_kakaopay_cid']) ? $default['de_kakaopay_cid'] : "";
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'help' => $help, 'default' => $default);
        $this->load->view('admin/config/kakopay', $data);
    }
    private function naverpay($headTitle, $headTitleEnd)
    {
        $default = $this->load->get_var('default');
        $help = array('test' => '', 'id' => '', 'key' => '', 'btnkey' => '', 'paytest' => '', 'xmldata' => '', 'addcost' => '');
        $help['test'] = help("네이버페이 검수 과정 중에는 <strong>테스트결제</strong>로 설정해야 하며 최종 승인 후 <strong>실결제</strong>로 설정합니다.");
        $help['id'] = help('네이버페이 가맹점 아이디를 입력합니다.<br><em class="fc_red">네이버페이는 결제대행사(PG) 심사가 끝난 후에 신청할 수 있습니다.</em>');
        $help['key'] = help("네이버페이 가맹점 인증키를 입력합니다.");
        $help['btnkey'] = help("네이버페이 버튼 인증키를 입력합니다.");
        $help['paytest'] = help("네이버페이 결제테스트를 위한 테스트 회원 아이디를 입력합니다. 네이버페이 검수 과정에서 필요합니다.");
        $help['xmldata'] = help("네이버페이에 상품정보를 XML 데이터로 제공하는 페이지입니다. 검수과정에서 아래의 URL 정보를 제공해야 합니다.");
        $help['addcost'] = help("네이버페이를 통한 결제 때 구매자에게 보여질 추가배송비 내용을 입력합니다.<br>예) 제주도 3,000원 추가, 제주도 외 도서·산간 지역 5,000원 추가");
        $payTest = array('', '');
        $payTest[0] = get_checked($default['de_naverpay_test'], "0");
        $payTest[1] = get_checked($default['de_naverpay_test'], "1");
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'help' => $help, 'default' => $default, 'payTest' => $payTest);
        $this->load->view('admin/config/naverpay', $data);
    }
    private function baesong($headTitle, $headTitleEnd)
    {
        $config = $this->load->get_var('config');
        $deliveryCompanyStr = "";
        $spl_sorts = explode(",", $config['delivery_company']);
        $spl_count = count($spl_sorts);
        $i = 0;
        do {
            $seq = explode('|', trim($spl_sorts[$i]));
            $deliveryCompanyStr .= "
                <tr>
                    <td><input type=\"text\" name=\"spl_name[]\" value=\"{$seq[0]}\" class=\"frm_input wfull\"></td>
                    <td><input type=\"text\" name=\"spl_url[]\" value=\"" . (isset($seq[1]) ? $seq[1] : '') . "\" class=\"frm_input wfull\"></td>
                    <td class=\"tac\">";
            if ($i == 0) {
                $deliveryCompanyStr .= "<button type=\"button\" id=\"add_supply_row\" class=\"btn_small\">추가</button>";
            }
            if ($i > 0) {
                $deliveryCompanyStr .= "<button type=\"button\" id=\"del_supply_row\" class=\"btn_small red\">삭제</button>";
            }
            $deliveryCompanyStr .= "
                    </td>
                </tr>";
            $i++;
        } while ($i < $spl_count);
        $deliver_method = array('', '', '', '');
        $deliver_method[1] = get_checked($config['delivery_method'], "1");
        $deliver_method[2] = get_checked($config['delivery_method'], "2");
        $deliver_method[3] = get_checked($config['delivery_method'], "3");
        $deliver_method[4] = get_checked($config['delivery_method'], "4");
        $editorPc = $this->global_lib->editor_html('baesong_cont1', $this->funcomplex_lib->get_text($config['baesong_cont1'], 0));
        $editorMb = $this->global_lib->editor_html('baesong_cont2', $this->funcomplex_lib->get_text($config['baesong_cont2'], 0));
        $baesong_cont1 = $this->global_lib->get_editor_js('baesong_cont1');
        $baesong_cont2 = $this->global_lib->get_editor_js('baesong_cont2');
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'deliveryCompanyStr' => $deliveryCompanyStr,
            'deliver_method' => $deliver_method,
            'editorPc' => $editorPc,
            'editorMb' => $editorMb,
            'baesong_cont1' => $baesong_cont1,
            'baesong_cont2' => $baesong_cont2
        );
        $this->load->view('admin/config/baesong', $data);
    }
    private function islandlist($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $qstr = $this->load->get_var('qstr');
        $config = $this->load->get_var('config');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_island ";
        $sql_search = " where (1) ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if (!$orderby) {
            $filed = "is_id";
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
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택수정\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">";
        $sflArr = array('', '', '');
        $sflArr[0] = option_selected('is_name', $sfl, '할증 지역명');
        $sflArr[1] = option_selected('is_zip1', $sfl, '우편번호 시작');
        $sflArr[2] = option_selected('is_zip2', $sfl, '우편번호 끝');
        $is_nameStr = $this->gvcomplex_lib->subject_sort_link('is_name', $q2);
        $is_zip1Str = $this->gvcomplex_lib->subject_sort_link('is_zip1', $q2);
        $is_zip2Str = $this->gvcomplex_lib->subject_sort_link('is_zip2', $q2);
        $is_priceStr = $this->gvcomplex_lib->subject_sort_link('is_price', $q2);

        $listTable = "";
        $i = 0;
        foreach ($result as $key => $row) {
            if ($i == 0)
                $listTable .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $listTable .= "
                <tr class=\"{$bg}\">
                    <td>			
                        <input type=\"hidden\" name=\"is_id[{$i}]\" value=\"{$row['is_id']}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td><input type=\"text\" name=\"is_name[{$i}]\" value=\"{$row['is_name']}\" class=\"frm_input\"></td>
                    <td><input type=\"text\" name=\"is_zip1[{$i}]\" value=\"{$row['is_zip1']}\" class=\"frm_input\"></td>
                    <td><input type=\"text\" name=\"is_zip2[{$i}]\" value=\"{$row['is_zip2']}\" class=\"frm_input\"></td>
                    <td><input type=\"text\" name=\"is_price[{$i}]\" value=\"" . number_format($row['is_price']) . "\" class=\"frm_input\" onkeyup=\"addComma(this)\"></td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $listTable .= '<tbody><tr><td colspan="5" class="empty_table">자료가 없습니다.</td></tr>';
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $zipNumberHelp = help('구 우편번호는 입력하시면 안됩니다.<br>우편번호는 00000 형식(5자)이며 숫자로만 입력하셔야 합니다.<br>예) <span class="fc_red">53321</span> 부터 <span class="fc_red">53325</span> 까지');
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'code' => $code,
            'btn_frmline' => $btn_frmline,
            'sflArr' => $sflArr,
            'stx' => $stx,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'is_nameStr' => $is_nameStr,
            'is_zip1Str' => $is_zip1Str,
            'is_zip2Str' => $is_zip2Str,
            'is_priceStr' => $is_priceStr,
            'listTable' => $listTable,
            'pagination' => $pagination,
            'zipNumberHelp' => $zipNumberHelp
        );
        $this->load->view('admin/config/islandlist', $data);
    }
    private function nicecheck($headTitle, $headTitleEnd)
    {
        $config = $this->load->get_var('config');

        $cf_cert_useArr = array('', '', '');
        $cf_cert_useArr[0] = get_checked($config['cf_cert_use'], "0");
        $cf_cert_useArr[1] = get_checked($config['cf_cert_use'], "1");
        $cf_cert_useArr[2] = get_checked($config['cf_cert_use'], "2");

        $cf_cert_ipinArr = array('nouse' => '', 'kcp' => '');
        $cf_cert_ipinArr['nouse'] = option_selected("", $config['cf_cert_ipin'], "사용안함");
        $cf_cert_ipinArr['kcp'] = option_selected("kcb", $config['cf_cert_ipin'], "코리아크레딧뷰로(KCB) 아이핀");

        $cf_cert_hpArr = array('nouse' => '', 'kpb' => '', 'kcp' => '', 'lg' => '');
        $cf_cert_hpArr['nouse'] = option_selected("",    $config['cf_cert_hp'], "사용안함");
        $cf_cert_hpArr['kpb'] = option_selected("kcb", $config['cf_cert_hp'], "코리아크레딧뷰로(KCB) 휴대폰 본인확인");
        $cf_cert_hpArr['kcp'] = option_selected("kcp", $config['cf_cert_hp'], "NHN KCP 휴대폰 본인확인");
        $cf_cert_hpArr['lg'] = option_selected("lg",  $config['cf_cert_hp'], "LG유플러스 휴대폰 본인확인");

        $help['kcpid'] = help('KCB 회원사ID를 입력해 주십시오.<br>서비스에 가입되어 있지 않다면, KCB와 계약체결 후 회원사ID를 발급 받으실 수 있습니다.<br>이용하시려는 서비스에 대한 계약을 아이핀, 휴대폰 본인확인 각각 체결해주셔야 합니다.<br>아이핀 본인확인 테스트의 경우에는 KCB 회원사ID가 필요 없으나,<br>휴대폰 본인확인 테스트의 경우 KCB 에서 따로 발급 받으셔야 합니다.');
        $help['kcpcode'] = help('NHN KCP 사이트코드를 입력해 주십시오.<br>서비스에 가입되어 있지 않다면, 본인인증 서비스 신청페이지에서 서비스 신청 후 사이트코드를 발급 받으실 수 있습니다.');
        $help['lgid'] = help('LG유플러스 상점아이디를 입력해 주십시오.<br>서비스에 가입되어 있지 않다면, 본인인증 서비스 신청페이지에서 서비스 신청 후 상점아이디를 발급 받으실 수 있습니다.<br><em class="fc_red">LG유플러스 휴대폰본인인증은 ActiveX 설치가 필요하므로 Internet Explorer 에서만 사용할 수 있습니다.</em>');
        $help['lgMertKey'] = help('LG유플러스 상점MertKey는 <strong>[계약정보 &gt; 상점정보관리 &gt; 시스템연동정보]</strong>에서 확인하실 수 있습니다.');
        $help['useLimit'] = help('하루동안 아이핀과 휴대폰 본인인증 인증 이용회수를 제한할 수 있습니다.<br>회수제한은 실서비스에서 아이핀과 휴대폰 본인인증 인증에 개별 적용됩니다.<br><em class="fc_084">0 으로 설정하시면 회수제한이 적용되지 않습니다.</em>');
        $cf_cert_reqStr = get_checked($config['cf_cert_req'], 1);
        $help['certification'] = help('회원가입시 본인인증을 필수로 할지 설정합니다.<br>필수로 설정하시면 본인인증을 하지 않은 경우 회원가입이 안됩니다.');

        $cf_cert_useJs = "";
        // 본인확인 모듈 실행권한 체크
        if ($config['cf_cert_use']) {
            // kcb일 때
            if ($config['cf_cert_ipin'] == 'kcb' || $config['cf_cert_hp'] == 'kcb') {
                // 실행모듈
                if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                    if (PHP_INT_MAX == 2147483647) // 32-bit
                        $exe = './plugin/okname/bin/okname';
                    else
                        $exe = './plugin/okname/bin/okname_x64';
                } else {
                    if (PHP_INT_MAX == 2147483647) // 32-bit
                        $exe = './plugin/okname/bin/okname.exe';
                    else
                        $exe = './plugin/okname/bin/oknamex64.exe';
                }

                $cf_cert_useJs .= module_exec_check($exe, 'okname');
            }

            // kcp일 때
            if ($config['cf_cert_hp'] == 'kcp') {
                if (PHP_INT_MAX == 2147483647) // 32-bit
                    $exe = './plugin/kcpcert/bin/ct_cli';
                else
                    $exe = './plugin/kcpcert/bin/ct_cli_x64';

                $cf_cert_useJs .= module_exec_check($exe, 'ct_cli');
            }

            // LG의 경우 log 디렉토리 체크
            if ($config['cf_cert_hp'] == 'lg') {
                $log_path = './plugin/lgxpay/lgdacom/log';

                if (!is_dir($log_path)) {
                    $cf_cert_useJs .= '<script>';
                    $cf_cert_useJs .= 'alert("' . str_replace('./', '', './plugin/lgxpay') . '/lgdacom 폴더 안에 log 폴더를 생성하신 후 쓰기권한을 부여해 주십시오.\n> mkdir log\n> chmod 707 log");';
                    $cf_cert_useJs .= '</script>';
                } else {
                    if (!is_writable($log_path)) {
                        $cf_cert_useJs .= '<script>';
                        $cf_cert_useJs .= 'alert("' . str_replace('./', '', $log_path) . ' 폴더에 쓰기권한을 부여해 주십시오.\n> chmod 707 log");';
                        $cf_cert_useJs .= '</script>';
                    }
                }
            }
        }


        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'cf_cert_useArr' => $cf_cert_useArr,
            'cf_cert_ipinArr' => $cf_cert_ipinArr,
            'cf_cert_hpArr' => $cf_cert_hpArr,
            'help' => $help,
            'config' => $config,
            'cf_cert_reqStr' => $cf_cert_reqStr,
            'cf_cert_useJs' => $cf_cert_useJs
        );
        $this->load->view('admin/config/nicecheck', $data);
    }
    private function ipaccess($headTitle, $headTitleEnd)
    {
        $config = $this->load->get_var('config');
        $help['possibleIp'] = help('입력된 IP의 컴퓨터만 접근할 수 있음. 123.123.+ 도 입력 가능. (엔터로 구분)');
        $help['interceptIp'] = help('입력된 IP의 컴퓨터는 접근할 수 없음. 123.123.+ 도 입력 가능. (엔터로 구분)');
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'config' => $config, 'help' => $help);
        $this->load->view('admin/config/ipaccess', $data);
    }
    private function board_group_list($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');
        $page = $this->input->get('page');

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_board_group ";
        $sql_search = " where (1) ";
        $sql_order  = " order by gr_id desc ";

        // 테이블의 전체 레코드수만 얻음
        $sql = " select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택수정\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "admin/config?code=board_group_form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 추가하기</a>";
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $i = 0;
        $groupList = "";
        foreach ($result as $key => $row) {
            // 게시판수
            $sql2 = " select count(*) as cnt from shop_board_conf where gr_id = ?";
            $param = array($row['gr_id']);
            $row2 = $this->query_model->returnOneArr($sql2, $param);

            $s_upd = "<a href='" . base_url() . "admin/config?code=board_group_form&w=u&gr_id=$row[gr_id]&page=$page' class=\"btn_small\">수정</a>";

            if ($i == 0)
                $groupList .= '<tbody class="list">';

            $bg = 'list' . ($i % 2);
            $groupList .= "
                <tr class=\"{$bg}\">
                    <td>			
                        <input type=\"hidden\" name=\"gr_id[{$i}]\" value=\"{$row['gr_id']}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td class=\"tal\">{$row['gr_id']}</td>
                    <td class=\"tal\"><input type=\"text\" name=\"gr_subject[{$i}]\" value=\"" . $this->funcomplex_lib->get_text($row['gr_subject']) . "\" class=\"frm_input\"></td>
                    <td>{$row2['cnt']}</td>
                    <td>{$s_upd}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $groupList .= '<tbody><tr><td colspan="5" class="empty_table">자료가 없습니다.</td></tr>';
        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'q1' => $q1,
            'page' => $page,
            'total_count' => $total_count,
            'btn_frmline' => $btn_frmline,
            'pagination' => $pagination,
            'groupList' => $groupList
        );
        $this->load->view('admin/config/board_group_list', $data);
    }
    private function board_group_form($headTitle, $headTitleEnd)
    {
        $w = $this->input->get('w');
        $gr_id = $this->input->get('gr_id');
        $page = $this->input->get('page');
        $readonly = "";
        $group = array('gr_id' => '', 'gr_subject' => "");
        if ($w == "u") {
            $sql = "select * from shop_board_group where gr_id = ?";
            $param = array($gr_id);
            $group = $this->query_model->returnOneArr($sql, $param);
            if (!$group['gr_id'])
                alert("존재하지 않은 게시판그룹 입니다.");
            $readonly = ' readonly style="background-color:#ddd"';
        }
        $gr_subjectTag = $this->funcomplex_lib->get_text($group['gr_subject']);
        $data = array('headTitle' => $headTitle, 'headTitleEnd' => $headTitleEnd, 'w' => $w, 'page' => $page, 'group' => $group, 'readonly' => $readonly, 'gr_subjectTag' => $gr_subjectTag);
        $this->load->view('admin/config/board_group_form', $data);
    }
    private function board_list($code, $headTitle, $headTitleEnd)
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');

        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');

        $query_string = "code=$code$qstr";
        $q1 = $query_string;
        $q2 = $query_string . "&page=$page";

        $sql_common = " from shop_board_conf ";
        $sql_search = " where (1) ";
        $sql_order  = " order by gr_id desc,index_no desc ";

        if ($sfl && $stx) {
            switch ($sfl) {
                case "bo_table":
                    $sql_search .= " and index_no like '$stx%' ";
                    break;
                default:
                    $sql_search .= " and $sfl like '%$stx%' ";
                    break;
            }
        }

        // 테이블의 전체 레코드수만 얻음
        $sql = " select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $btn_frmline = "
            <input type=\"submit\" name=\"act_button\" value=\"선택수정\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <input type=\"submit\" name=\"act_button\" value=\"선택삭제\" class=\"btn_lsmall bx-white\" onclick=\"document.pressed=this.value\">
            <a href=\"" . base_url() . "admin/config?code=board_form\" class=\"fr btn_lsmall red\"><i class=\"ionicons ion-android-add\"></i> 추가하기</a>";
        $pagination = get_paging($config['write_pages'], $page, $total_page, $requestUrl . '?' . $q1 . '&page=');
        $sflArr = array('', '', '');
        $sflArr[0] = option_selected('bo_table', $sfl, 'TABLE');
        $sflArr[1] = option_selected('boardname', $sfl, '제목');
        $sflArr[2] = option_selected('gr_id', $sfl, '그룹ID');

        $i = 0;
        $boardListTable = "";
        foreach ($result as $key => $row) {
            $bo_table = $row['index_no'];

            $list_priv = "비회원";
            $read_priv = "비회원";
            $write_priv = "비회원";
            if ($row['list_priv'] != 99) {
                $list_priv = $this->global_lib->get_grade($row['list_priv']);
            }
            if ($row['read_priv'] != 99) {
                $read_priv = $this->global_lib->get_grade($row['read_priv']);
            }
            if ($row['write_priv'] != 99) {
                $write_priv = $this->global_lib->get_grade($row['write_priv']);
            }
            $s_upd = "<a href='" . base_url() . "admin/config?code=board_form&w=u&bo_table=$bo_table$qstr&page=$page' class=\"btn_small\">수정</a>";
            if ($i == 0)
                $boardListTable .= '<tbody class="list">';
            $bg = 'list' . ($i % 2);
            $boardListTable .= "
                <tr class=\"{$bg}\">
                    <td>			
                        <input type=\"hidden\" name=\"bo_table[{$i}]\" value=\"{$bo_table}\">
                        <input type=\"checkbox\" name=\"chk[]\" value=\"{$i}\">
                    </td>
                    <td><a href=\"" . base_url() . "bbs/noticeList?boardid={$bo_table}\" target=\"_blank\">{$bo_table}</a></td>
                    <td><input type=\"text\" name=\"bo_subject[{$i}]\" value=\"{$row['boardname']}\" class=\"frm_input\"></td>
                    <td>" . $this->common_lib->get_group_select("gr_id[$i]", $row['gr_id']) . "</td>
                    <td>{$list_priv}</td>
                    <td>{$read_priv}</td>
                    <td>{$write_priv}</td>
                    <td>" . ($row['usereply'] == 'Y' ? 'yes' : '') . "</td>
                    <td>" . ($row['usetail'] == 'Y' ? 'yes' : '') . "</td>
                    <td>{$s_upd}</td>
                </tr>";
            $i++;
        }
        if ($i == 0)
            $boardListTable .= '<tbody><tr><td colspan="10" class="empty_table">자료가 없습니다.</td></tr>';
        $data = array(
            'code' => $code,
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'btn_frmline' => $btn_frmline,
            'stx' => $stx,
            'total_count' => $total_count,
            'q1' => $q1,
            'pagination' => $pagination,
            'sflArr' => $sflArr,
            'boardListTable' => $boardListTable,
            'page' => $page
        );
        $this->load->view('admin/config/borad_list', $data);
    }
    private function board_form($headTitle, $headTitleEnd)
    {
        $qstr = $this->load->get_var('qstr');

        $bo_table = $this->input->get('bo_table');
        $board = $this->global_lib->get_board($bo_table);
        $board_dir = "./data/board/boardimg";
        $w = $this->input->get('w');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $page = $this->input->get('page');
        if ($w == "") {
            $board['index_no']        = $this->global_lib->get_next_num('shop_board_conf');
            $board['width']            = '100';
            $board['page_num']        = '30';
            $board['list_cut']        = '40';
            $board['topfile']        = './board_head';
            $board['downfile']        = './board_tail';
            $board['read_list']        = '2';
            $board['use_secret']    = '0';
            $board['list_priv']        = '99';
            $board['read_priv']        = '99';
            $board['reply_priv']    = '1';
            $board['write_priv']    = '99';
            $board['tail_priv']        = '99';
        } else if ($w == "u") {
            if (!$board['index_no'])
                alert("존재하지 않은 게시판 입니다.");
        }
        $board['gr_id'] = isset($board['gr_id']) ? $board['gr_id'] : "";
        $board['boardname'] = isset($board['boardname']) ? $board['boardname'] : "";
        $board['usecate'] = isset($board['usecate']) ? $board['usecate'] : "";
        $board['usereply'] = isset($board['usereply']) ? $board['usereply'] : "";
        $board['usetail'] = isset($board['usetail']) ? $board['usetail'] : "";
        $board['usefile'] = isset($board['usefile']) ? $board['usefile'] : "";
        $board['fileurl1'] = isset($board['fileurl1']) ? $board['fileurl1'] : "";
        $board['fileurl2'] = isset($board['fileurl2']) ? $board['fileurl2'] : "";
        $board['use_category'] = isset($board['use_category']) ? $board['use_category'] : "";
        $board['content_head'] = isset($board['content_head']) ? $board['content_head'] : "";
        $board['content_tail'] = isset($board['content_tail']) ? $board['content_tail'] : "";
        $board['insert_content'] = isset($board['insert_content']) ? $board['insert_content'] : "";


        $get_group_select = $this->common_lib->get_group_select('gr_id', $board['gr_id'], "required itemname='그룹' onChange='chk_board_head(this.form, this.value)'");
        $updateTag = "";
        if ($w == 'u') {
            $updateTag = "<a href=\"javascript:location.href='" . base_url() . "admin/config?code=board_list&sfl=gr_id&stx='+document.fboardform.gr_id.value;\" class=\"btn_small grey\">동일그룹게시판목록</a>";
        }
        $boardTitle = $this->funcomplex_lib->get_text($board['boardname']);
        $arr = $this->global_lib->get_skin_dir();
        $skinStr = "";
        $board['skin'] = isset($board['skin']) ? $board['skin'] : "";
        for ($i = 0; $i < count($arr); $i++) {
            $skinStr .= option_selected($arr[$i], $board['skin'], $arr[$i]);
        }
        $usecateStr = $this->funcomplex_lib->get_text($board['usecate']);
        $helpMulti = help('분류와 분류 사이는 | 로 구분하세요. (예: 질문|답변) 첫자로 #은 입력하지 마세요. (예: #질문|#답변 [X])');
        $list = array('list_priv' => '', 'read_priv' => '', 'write_priv' => '', 'reply_priv' => '', 'tail_priv' => '');
        $list['list_priv'] = $this->funcomplex_lib->get_member_level_select('list_priv', 1, 9, $board['list_priv']);
        $list['read_priv'] = $this->funcomplex_lib->get_member_level_select('read_priv', 1, 9, $board['read_priv']);
        $list['write_priv'] = $this->funcomplex_lib->get_member_level_select('write_priv', 1, 9, $board['write_priv']);
        $list['reply_priv'] = $this->funcomplex_lib->get_member_level_select('reply_priv', 1, 9, $board['reply_priv']);
        $list['tail_priv'] = $this->funcomplex_lib->get_member_level_select('tail_priv', 1, 9, $board['tail_priv']);
        $usereplyStr = ($board['usereply'] == 'Y') ? ' checked' : '';
        $usetailStr = ($board['usetail'] == 'Y') ? ' checked' : '';
        $usefileStr = ($board['usefile'] == 'Y') ? ' checked' : '';

        $use_secretArr = array('', '', '');
        $use_secretArr[0] = option_selected('0', $board['use_secret'], '사용하지 않음');
        $use_secretArr[1] = option_selected('1', $board['use_secret'], '체크박스');
        $use_secretArr[2] = option_selected('2', $board['use_secret'], '무조건');

        $chkHelp = help("'체크박스'는 글작성시 비밀글 체크가 가능합니다.<br>'무조건'은 작성되는 모든글을 비밀글로 작성합니다. (관리자는 체크박스로 출력합니다.)<br>스킨에 따라 적용되지 않을 수 있습니다.");

        $read_listArr = array('', '', '', '');
        $read_listArr[1] = option_selected('1', $board['read_list'], '전체 목록 출력');
        $read_listArr[2] = option_selected('2', $board['read_list'], '이전글 다음글만 출력');
        $read_listArr[3] = option_selected('3', $board['read_list'], '사용안함');


        $fileurl1_str = "";
        $fileurl1_strTag = "";
        $fileurl1 = $board_dir . '/' . $board['fileurl1'];
        if (is_file($fileurl1) && $board['fileurl1']) {
            $size = @getimagesize($fileurl1);
            if ($size[0] && $size[0] > 700)
                $width = 700;
            else
                $width = $size[0];

            $fileurl1 = rpc($fileurl1, '', '');
            $fileurl1 = base_url() . $fileurl1;

            $fileurl1_strTag .= '<input type="checkbox" name="image_head_del" value="' . $board['fileurl1'] . '" id="image_head_del"> <label for="image_head_del">삭제</label>';
            $fileurl1_str = '<img src="' . $fileurl1 . '" width="' . $width . '">';
        }
        if ($fileurl1_str) {
            $fileurl1_strTag .= '<div class="banner_or_img">' . $fileurl1_str . '</div>';
        }

        $fileurl2_strTag = "";
        $fileurl2_str = "";
        $fileurl2 = $board_dir . '/' . $board['fileurl2'];
        if (is_file($fileurl2) && $board['fileurl2']) {
            $size = @getimagesize($fileurl2);
            if ($size[0] && $size[0] > 700)
                $width = 700;
            else
                $width = $size[0];

            $fileurl2 = rpc($fileurl2, '', '');
            $fileurl2 = base_url() . $fileurl2;

            $fileurl2_strTag .= '<input type="checkbox" name="image_tail_del" value="' . $board['fileurl2'] . '" id="image_tail_del"> <label for="image_tail_del">삭제</label>';
            $fileurl2_str = '<img src="' . $fileurl2 . '" width="' . $width . '">';
        }
        if ($fileurl2_str) {
            $fileurl2_strTag .= '<div class="banner_or_img">' . $fileurl2_str . '</div>';
        }

        $data = array(
            'headTitle' => $headTitle,
            'headTitleEnd' => $headTitleEnd,
            'w' => $w,
            'sfl' => $sfl,
            'stx' => $stx,
            'page' => $page,
            'board' => $board,
            'get_group_select' => $get_group_select,
            'updateTag' => $updateTag,
            'boardTitle' => $boardTitle,
            'skinStr' => $skinStr,
            'usecateStr' => $usecateStr,
            'helpMulti' => $helpMulti,
            'list' => $list,
            'usereplyStr' => $usereplyStr,
            'usetailStr' => $usetailStr,
            'usefileStr' => $usefileStr,
            'use_secretArr' => $use_secretArr,
            'chkHelp' => $chkHelp,
            'read_listArr' => $read_listArr,
            'fileurl1_strTag' => $fileurl1_strTag,
            'fileurl2_strTag' => $fileurl2_strTag,
            'qstr' => $qstr,
            'page' => $page
        );
        $this->load->view('admin/config/board_form', $data);
    }
    #환경설정 관리 페이지 끝

}
