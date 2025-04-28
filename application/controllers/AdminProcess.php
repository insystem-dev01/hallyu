<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AdminProcess extends CI_Controller{
    private $max_size;
    private $max_width;
    private $max_height;
    private $remove_spaces;
    private $encrypt_name;
    private $allowed_types;

    public function __construct(){
        parent::__construct();
        $this->load->model('query_model');
        $this->load->helper('common_helper');
        $this->load->helper('global_helper');
        $this->load->helper('register_helper');
        $this->load->library('funcomplex_lib');
        $this->load->library('gvcomplex_lib');
        $this->load->library('global_lib');
        $this->load->library('register_lib');

        $this->allowed_types = 'jpg|jpeg|bmp|png|gif';
        $this->max_size = '2048';
        $this->max_width = '5000';
        $this->max_height = '5000';
        $this->remove_spaces = TRUE;
        $this->encrypt_name = TRUE;
    }
    #기타 등등
    public function formmail_send()
    {
        $is_member = $this->load->get_var('is_member');
        $fnick = $this->input->post('fnick');
        $to = $this->input->post('to');
        $fmail = $this->input->post('fmail');
        $subject = $this->input->post('subject');
        $attach = $this->input->post('attach');
        $content = $this->input->post("content");
        $type = $this->input->post('type');

        include_once('./lib/mailer.lib.php');
        if ($_POST["token"] && get_session("ss_token") == $_POST["token"]) {
            // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
            set_session("ss_token", "");
        } else {
            alert("잘못된 접근 입니다.");
            exit;
        }

        if (!$is_member)
            alert_close('회원만 이용하실 수 있습니다.');

        $email_enc = new str_encrypt();
        $to = $email_enc->decrypt($to);

        if (substr_count($to, "@") > 1)
            alert_close('한번에 한사람에게만 메일을 발송할 수 있습니다.');

        $file = array();
        for ($i = 1; $i <= $attach; $i++) {
            if ($_FILES['file' . $i]['name'])
                $file[] = attach_file($_FILES['file' . $i]['name'], $_FILES['file' . $i]['tmp_name']);
        }

        $content = stripslashes($content);
        if ($type == 2) {
            $type = 1;
            $content = str_replace("\n", "<br>", $content);
        }

        // html 이면
        if ($type) {
            $current_url = base_url();
            $mail_content = '<!doctype html><html lang="ko"><head><meta charset="utf-8"><title>메일보내기</title><link rel="stylesheet" href="' . $current_url . '/style.css"></head><body>' . $content . '</body></html>';
        } else
            $mail_content = $content;

        mailer($fnick, $fmail, $to, $subject, $mail_content, $type, $file);

        // 임시 첨부파일 삭제
        if (!empty($file)) {
            foreach ($file as $f) {
                @unlink($f['path']);
            }
        }

        $html_title = '메일 발송중';
        alert_close('메일을 정상적으로 발송하였습니다.');
    }
    public function sms_user_update()
    {
        $ph = $this->input->post('ph');
        $sms = $this->global_lib->get_sms('admin');
        $recv_number = $this->input->post('recv_number');
        $sms_content = $this->input->post('sms_content');
        if (!$sms['cf_sms_use']) {
            alert_close('문자서비스를 사용가능한 설정 상태가 아닙니다.');
        }
        $this->funcomplex_lib->moonjaro_direct_sms_send('admin', $recv_number, $sms_content, 1);

        alert("전송되었습니다.", base_url() . "admin/sms/sms_user?ph=$ph");
    }
    public function admin_ss_login()
    {
        // 관리자 세션 변수에 등록
        if ($this->gvcomplex_lib->is_admin()) {
            set_session('admin_ss_mb_id', get_session('ss_mb_id'));
        }

        // 세션 변수에 등록
        set_session('ss_mb_id', $_GET['mb_id']);
        $lg_type = $this->input->get('lg_type');
        if ($lg_type == 'S')
            goto_url(base_url() . 'mypage/page?code=seller_main');
        else if ($lg_type == 'P')
            goto_url(base_url() . 'mypage/page?code=partner_info');
        else
            goto_url(base_url());
    }
    public function ajax_token()
    {
        set_session('ss_admin_token', '');

        $error = $this->funcomplex_lib->admin_referer_check(true);
        if ($error)
            die(json_encode(array('error' => $error, 'url' => base_url())));

        $token = $this->funcomplex_lib->get_admin_token();

        die(json_encode(array('error' => '', 'token' => $token, 'url' => '')));
    }
    public function board_list_update()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');

        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "선택수정") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $gr_id = $this->input->post('gr_id');
                $bo_subject = $this->input->post('bo_subject');
                $bo_table = $this->input->post('bo_table');
                $param = array('gr_id' => $gr_id[$k], 'boardname' => $bo_subject[$k]);
                $this->query_model->updateObj('shop_board_conf', $param, "index_no='{$bo_table[$k]}'");
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            $board_dir = "./data/board/boardimg";
            $board_def = array(36, 22, 21, 20, 13);

            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $bo_table = trim($_POST['bo_table'][$k]);

                //기본게시판의 삭제를 막는다.
                if (in_array($_POST['bo_table'][$k], $board_def)) {
                    alert('기본게시판은 삭제 할 수 없습니다.');
                }

                $tmpData = $this->query_model->returnArr("select * from shop_board_{$bo_table}");
                foreach ($tmpData as $key => $board) {
                    if ($board['fileurl1']) @unlink("./data/board/" . $bo_table . "/" . $board['fileurl1']);
                    if ($board['fileurl2']) @unlink("./data/board/" . $bo_table . "/" . $board['fileurl2']);
                    $this->funcomplex_lib->delete_editor_image($board['memo']);
                }

                if (is_dir("./data/board/" . $bo_table))
                    $this->zRmDir("./data/board/" . $bo_table);

                $tmpData1 = $this->query_model->returnArr("select * from shop_board_conf where index_no='$bo_table'");
                foreach ($tmpData1 as $key => $conf) {
                    if ($conf['fileurl1']) @unlink($board_dir . "/" . $conf['fileurl1']);
                    if ($conf['fileurl2']) @unlink($board_dir . "/" . $conf['fileurl2']);
                }

                $this->query_model->returnNull("drop table shop_board_{$bo_table}");
                $this->query_model->returnNull("drop table shop_board_{$bo_table}_tail");
                $sql = "delete from shop_board_conf where index_no=?";
                $param = array($bo_table);
                $this->query_model->returnNull($sql, $param);
            }
        }
        goto_url(base_url() . "admin/config?$q1&page=$page");
    }
    private function zRmDir($path)
    {
        $directory = dir($path);
        while ($entry = $directory->read()) {
            if ($entry != "." && $entry != "..") {
                if (Is_Dir($path . "/" . $entry)) {
                    zRmDir($path . "/" . $entry);
                } else {
                    @UnLink($path . "/" . $entry);
                }
            }
        }

        $directory->close();
        @RmDir($path);
    }
    public function ajax_menu()
    {
        $type = $this->input->post('type');
        $gubun = $this->input->post('gubun');
        set_cookie('ck_' . $type, $gubun, 86400 * 365);
    }
    public function email_stop()
    {
        $mb_id = $this->input->get('mb_id');
        $mb_md5 = $this->input->get('mb_md5');
        $sql = " select id, email, reg_time from shop_member where id = ?";
        $param = array($mb_id);
        $row = $this->query_model->returnOneArr($sql, $param);
        if (!$row['id'])
            alert('존재하는 회원이 아닙니다.', base_url());

        if ($mb_md5) {
            $tmp_md5 = md5($row['id'] . $row['email'] . $row['reg_time']);
            if ($mb_md5 == $tmp_md5) {
                $this->query_model->returnNull(" update shop_member set mailser = 'N' where id = '{$mb_id}' ");

                alert('정보메일을 보내지 않도록 수신거부 하였습니다.', base_url());
            }
        }

        alert('제대로 된 값이 넘어오지 않았습니다.', base_url());
    }
    #멤버 관련된 함수들 시작
    public function member_list_excel()
    {
        $fr_date = $this->input->post('fr_date');
        $to_date = $this->input->post('to_date');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $sst = $this->input->get('sst');
        $spt = $this->input->get('spt');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';
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
        $sql_order = " order by $filed $sod ";
        $sql = " select * $sql_common $sql_search $sql_order  ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();
        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '회원명')
            ->setCellValue($char++ . '1', '아이디')
            ->setCellValue($char++ . '1', '성별')
            ->setCellValue($char++ . '1', '레벨')
            ->setCellValue($char++ . '1', '추천인')
            ->setCellValue($char++ . '1', '전화번호')
            ->setCellValue($char++ . '1', '핸드폰')
            ->setCellValue($char++ . '1', '우편번호')
            ->setCellValue($char++ . '1', '주소')
            ->setCellValue($char++ . '1', '이메일')
            ->setCellValue($char++ . '1', '회원가입일')
            ->setCellValue($char++ . '1', '로그인횟수')
            ->setCellValue($char++ . '1', '메일수신')
            ->setCellValue($char++ . '1', 'SMS수신')
            ->setCellValue($char++ . '1', '최근아이피')
            ->setCellValue($char++ . '1', 'IP')
            ->setCellValue($char++ . '1', '포인트');
        $i = 2;
        foreach ($result as $key => $row) {
            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gender'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['grade'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['pt_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['telephone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['cellphone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['zip'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $this->funcomplex_lib->print_address($row['addr1'], $row['addr2'], $row['addr3'], $row['addr_jibeon']), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['email'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['reg_time'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['login_sum'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['mailser'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['smsser'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['login_ip'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['mb_ip'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['point'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $i++;
        }
        // Rename worksheet
        $excel->getActiveSheet()->setTitle('회원목록');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="회원목록-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function sms_member_update()
    {
        $sms = $this->global_lib->get_sms('admin');
        if (!$sms['cf_sms_use']) {
            alert_close('문자서비스를 사용가능한 설정 상태가 아닙니다.');
        }

        $send_type = trim($_POST['send_type']);
        $sms_content = trim($_POST['sms_content']);

        if ($send_type == 'all') {
            $sql = " select cellphone from shop_member where smsser = 'Y' and id <> 'admin' and supply <> 'Y' ";
            $result = $this->query_model->returnArr($sql);
        } else {
            $sql = " select cellphone from shop_member where smsser = 'Y' and id <> 'admin' and supply <> 'Y' and grade =?";
            $param = array($send_type);
            $result = $this->query_model->returnArr($sql, $param);
        }

        $recv_number = '';
        $count = 0;
        foreach ($result as $key => $row) {
            $temp = $row['cellphone'];
            $recv_number .= $temp . ",";
            $count++;
        }
        $this->funcomplex_lib->moonjaro_direct_sms_send('admin', $recv_number, $sms_content, $count);
        alert("전송되었습니다.", base_url() . "admin/sms/sms_member");
    }

    public function member_register_form_update()
    {
        try {
            $config = $this->load->get_var('config');
            $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

            $mb_id = trim($this->input->post('mb_id'));

            // 휴대폰번호 체크
            $mb_hp = hyphen_hp_number($this->input->post('mb_hp'));
            if ($mb_hp) {
                $result = $this->funcomplex_lib->exist_mb_hp($mb_hp, $mb_id);
                if ($result) {
                    throw new Exception($result);
                }
            }
            $mb_certify = "";
            $mb_adult = "";

            // 인증정보처리
            $mb_certify_case = $this->input->post('mb_certify_case');
            $mb_certify = $this->input->post('mb_certify');
            if ($mb_certify_case && $mb_certify) {
                $mb_certify = $this->input->post('mb_certify_case');
                $mb_adult = $this->input->post('mb_adult');
            } else {
                $mb_certify = '';
                $mb_adult = 0;
            }

            $mb = $this->global_lib->get_member($mb_id);
            if ($mb['id']) {
                throw new Exception('이미 존재하는 회원아이디입니다.\\nＩＤ : ' . $mb['id'] . '\\n이름 : ' . $mb['name'] . '\\n메일 : ' . $mb['email']);
            }

            // 이메일중복체크
            $sql = "SELECT id, name, email FROM shop_member WHERE email = ? and id <> ? ";
            $mb_email = $this->input->post('mb_email');
            $param = array($mb_email, $mb_id);
            $row = $this->query_model->returnOneArr($sql, $param);
            if ($row['id']) {
                throw new Exception('이미 존재하는 이메일입니다.\\nＩＤ : ' . $row['id'] . '\\n이름 : ' . $row['name'] . '\\n메일 : ' . $row['email']);
            }
            unset($value);

            $mb_mailling = $this->input->post('mb_mailling');
            $mb_sms = $this->input->post('mb_sms');

            // 회원 사진
            $uploadPath = "./data/member/";
            $configArr = array();
            $configArr['upload_path'] = $uploadPath;

            $configArr['allowed_types'] = $this->allowed_types;
            $configArr['max_size'] = $this->max_size;
            $configArr['max_width'] = "400";
            $configArr['max_height'] = "400";
            $configArr['remove_spaces'] = $this->remove_spaces;
            $configArr['encrypt_name'] = $this->encrypt_name;

            $this->load->library('upload', $configArr);
            $this->upload->initialize($configArr);

            // 파일이 있으면 파일을 업로드함
            $uploadFile = array('mb_img' => '');
            $error = "";

            if (isset($_FILES['mb_img']['name']) && $_FILES['mb_img']['name'] != "") {
                if (!$this->upload->do_upload('mb_img')) {
                    $error = $this->upload->display_errors();
                } else {
                    $uploadFile['mb_img'] = $this->upload->data();
                    $new_mb_img = $uploadFile['mb_img']['file_name'];
                }
            }

            if ($error != "") {
                throw new Exception($error);
            }

            $value['id']            = $mb_id; //회원아이디
            $value['nickname']        = trim($this->input->post('mb_nickname')); //회원아이디
            $value['passwd']        = $this->global_lib->reg_sql_password($this->input->post('mb_password'), $OM_TIME_YMDHIS); //비밀번호
            $value['name']            = $this->input->post('mb_name'); //이름
            $value['email']            = $this->input->post('mb_email'); //이메일
            $value['cellphone']        = $mb_hp; //핸드폰
            // $value['telephone']		= $this->input->post('mb_tel');	 //전화번호
            $value['zip']            = $this->input->post('mb_zip'); //우편번호
            $value['addr1']            = $this->input->post('mb_addr1'); //주소
            $value['addr2']            = $this->input->post('mb_addr2'); //상세주소
            $value['addr3']            = $this->input->post('mb_addr3'); //참고항목
            $value['addr_jibeon']    = $this->input->post('mb_addr_jibeon'); //지번주소
            $value['today_login']    = $OM_TIME_YMDHIS; //최근 로그인일시
            $value['reg_time']        = $OM_TIME_YMDHIS; //가입일시
            $value['mb_ip']            = $_SERVER['REMOTE_ADDR']; //IP
            $value['grade']            = '2'; // 일반회원 레벨
            $value['pt_id']            = $this->input->post('mb_recommend'); //추천인아이디
            $value['login_ip']        = $_SERVER['REMOTE_ADDR']; //최근 로그인IP
            $value['mailser']        = $mb_mailling ? $mb_mailling : 'N'; //E-Mail을 수신
            $value['smsser']        = $mb_sms ? $mb_sms : 'N'; //SMS를 수신
            $value['mb_certify']    = $mb_certify;
            $value['mb_adult']        = $mb_adult;

            // 관리자인증을 사용하지 않는다면 인증으로 간주함.
            if (!$config['cert_admin_yes']) {
                $value['use_app']    = '1';
            }
            $insert_id = $this->query_model->insertObj("shop_member", $value);

            // 사진이 있을 경우 사진 이름 업데이트
            if (isset($_FILES['mb_img']['name']) && $_FILES['mb_img']['name'] != "") {
                $sql = "UPDATE shop_member SET mb_img = ? WHERE index_no = ?";
                $param = array($new_mb_img, $insert_id);
                $this->query_model->returnNull($sql, $param);
            }

            $return['res'] = true;
            $return['msg'] = "회원가입이 완료되었습니다.";
        } catch (Exception $e) {
            $return['res'] = false;
            $return['msg'] = $e->getMessage();
        }

        echo json_encode($return);
    }

    public function member_level_form_update()
    {
        $chk = $this->input->post('chk');
        $gb_name = $this->input->post('gb_name');
        $gb_sale = $this->input->post('gb_sale');
        $gb_sale_unit = $this->input->post('gb_sale_unit');
        $gb_sale_rate = $this->input->post('gb_sale_rate');
        $gb_buy_point = $this->input->post('gb_buy_point');
        $gb_review_point = $this->input->post('gb_review_point');
        $gb_payback_point = $this->input->post('gb_payback_point');
        $gb_minimum = $this->input->post('gb_minimum');
        $gb_maximum = $this->input->post('gb_maximum');
        $gb_info = $this->input->post('gb_info');
        $gb_no = $this->input->post('gb_no');
        $gb_period = $this->input->post('gb_period');
        $value['gb_period'] = $gb_period;

        $del_arr = [];
        $del_img = $this->input->post('del_img') ?? []; // 체크된 항목
        for ($i = 0; $i < count($chk); $i++) {
            if (isset($del_img[$i])) {
                $del_arr[$i] = $del_img[$i]; // 체크된 경우
            } else {
                $del_arr[$i] = 0; // 체크되지 않은 경우
            }
        }

        $this->load->library('upload');
        $uploaded_files = $_FILES;

        for ($i = 0; $i < count($chk); $i++) {
            // 실제 번호를 넘김
            $k = $chk[$i];

            if (!empty($del_arr[$k])) {
                // 파일 삭제
                delete_grade_thumbnail($del_arr[$k]);
                $param = array('gb_img' => "");
                $this->query_model->updateObj('shop_member_grade', $param, "gb_no = '{$gb_no[$k]}'");
            }

            if ($uploaded_files['gb_img']['name'][$k] != null) {
                $_FILES['gb_img']['name']     = $uploaded_files['gb_img']['name'][$k];
                $_FILES['gb_img']['type']     = $uploaded_files['gb_img']['type'][$k];
                $_FILES['gb_img']['tmp_name'] = $uploaded_files['gb_img']['tmp_name'][$k];
                $_FILES['gb_img']['error']    = $uploaded_files['gb_img']['error'][$k];
                $_FILES['gb_img']['size']     = $uploaded_files['gb_img']['size'][$k];

                $upload_config = array(
                    'upload_path' => './data/grade',
                    'allowed_types' => 'gif|jpg|jpeg|png',
                    'max_size' => '10240'
                );

                $this->upload->initialize($upload_config);
                if (! $this->upload->do_upload('gb_img')) {
                    echo $this->upload->display_errors();
                }

                $param = array('gb_img' => $uploaded_files['gb_img']['name'][$k]);
                $this->query_model->updateObj('shop_member_grade', $param, "gb_no = '{$gb_no[$k]}'");
            }

            $param = array(
                'gb_name' => $gb_name[$k],
                'gb_sale' => $gb_sale[$k],
                'gb_sale_unit' => $gb_sale_unit[$k],
                'gb_sale_rate' => $gb_sale_rate[$k],
                'gb_buy_point' => $gb_buy_point[$k],
                'gb_review_point' => $gb_review_point[$k],
                'gb_payback_point' => $gb_payback_point[$k],
                'gb_minimum' => conv_number($gb_minimum[$k]),
                'gb_maximum' => conv_number($gb_maximum[$k]),
                'gb_info' => $gb_info[$k],
            );

            $this->query_model->updateObj('shop_member_grade', $param, "gb_no = '{$gb_no[$k]}'");
        }

        $this->query_model->updateObj("shop_config", $value);

        goto_url(base_url() . "admin/member?code=level_form");
    }

    public function member_mail_form_update()
    {
        $qstr = $this->load->get_var('qstr');
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $w = $this->input->post('w');
        $_POST = array_map('trim', $_POST);
        $ma_subject = $this->input->post('ma_subject');
        $ma_content = $this->input->post('ma_content');
        $page = $this->input->post('page');

        if ($w == "") {
            $sql = " insert into shop_mail set ma_subject = ? ,ma_content = ?, ma_time= ?, ma_ip = ?";
            $param = array($ma_subject, $ma_content, $OM_TIME_YMDHIS, $_SERVER['REMOTE_ADDR']);
            $ma_id = $this->query_model->returnInsertId($sql, $param);
        } else if ($w == "u") {
            $ma_id = $this->input->post('ma_id');
            $sql = " update shop_mail set ma_subject = ? ,ma_content = ?, ma_time= ?, ma_ip = ? where ma_id = ?";
            $param = array($ma_subject, $ma_content, $OM_TIME_YMDHIS, $_SERVER['REMOTE_ADDR'], $ma_id);
            $this->query_model->returnNull($sql, $param);
        }

        if ($w == "" || $w == "u")
            goto_url(base_url() . "admin/member?code=mail_form&w=u&ma_id=$ma_id$qstr&page=$page");
        else
            goto_url(base_url() . "admin/member?code=mail_list");
    }

    public function member_point_req_update()
    {
        $member = $this->load->get_var('member');
        $_POST = array_map('trim', $_POST);
        $po_point   = $this->input->post('po_point');
        $po_content = $this->input->post('po_content');

        $mb    = $this->global_lib->get_member($_POST['mb_id'], 'id, point');

        if (!$mb['id'])
            alert("존재하는 회원아이디가 아닙니다.");

        if (($po_point < 0) && ($po_point * (-1) > $mb['point']))
            alert("포인트를 차감하는 경우 현재 포인트보다 작으면 안됩니다.");

        $this->funcomplex_lib->insert_point($mb['id'], $po_point, $po_content, '@passive', $mb['id'], $member['id'] . '-' . uniqid(''));

        alert('정상적으로 처리 되었습니다.', 'replace');
    }
    public function member_mileage_req_update()
    {
        $member = $this->load->get_var('member');
        $_POST = array_map('trim', $_POST);
        $po_point   = $this->input->post('po_point');
        $po_content = $this->input->post('po_content');

        $mb    = $this->global_lib->get_member($_POST['mb_id'], 'id, mileage');

        if (!$mb['id'])
            alert("존재하는 회원아이디가 아닙니다.");

        if (($po_point < 0) && ($po_point * (-1) > $mb['mileage']))
            alert("예치금을 차감하는 경우 현재 예치금보다 작으면 안됩니다.");

        $this->funcomplex_lib->insert_mileage($mb['id'], $po_point, $po_content, '@passive', $mb['id'], $member['id'] . '-' . uniqid(''));

        alert('정상적으로 처리 되었습니다.', 'replace');
    }
    public function member_delete()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $mb_id = $this->input->get('mb_id');
        $mb = $this->global_lib->get_member($mb_id);
        if (!$mb['id'])
            alert('존재하지 않는 회원입니다.');

        if ($mb_id == 'admin')
            alert('관리자는 탈퇴하실 수 없습니다.');

        // 회원삭제
        $this->funcomplex_lib->member_delete($mb_id);

        unset($value);
        $value['mb_id']       = $mb['id'];
        $value['mb_name']  = $mb['name'];
        $value['memo']       = '관리자 강제 영구탈퇴처리';
        $value['reg_time'] = $OM_TIME_YMDHIS;
        $this->query_model->insertObj("shop_member_leave", $value);

        alert_close('정상적으로 탈퇴 되었습니다.');
    }
    public function pop_memberformupdate()
    {
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');

        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);

        $mb_id = trim($_POST['mb_id']);
        $mb_grade = $this->input->post('mb_grade');

        // 프로필 사진 등록
        $uploadPath = "./data/member/";
        $configArr = array();
        $configArr['upload_path'] = $uploadPath;

        $configArr['allowed_types'] = $this->allowed_types;
        $configArr['max_size'] = $this->max_size;
        $configArr['max_width'] = "400";
        $configArr['max_height'] = "400";
        $configArr['remove_spaces'] = $this->remove_spaces;
        $configArr['encrypt_name'] = $this->encrypt_name;

        $this->load->library('upload', $configArr);
        $this->upload->initialize($configArr);

        // 파일이 있으면 파일을 업로드함
        $uploadFile = array('mb_img' => '');
        $error = "";

        if (isset($_FILES['mb_img']['name']) && $_FILES['mb_img']['name'] != "") {
            if (!$this->upload->do_upload('mb_img')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile['mb_img'] = $this->upload->data();
                $new_img1 = $uploadFile['mb_img']['file_name'];

                $sql = "UPDATE shop_member SET mb_img = ? WHERE id = ?";
                $param = array($new_img1, $mb_id);
                $this->query_model->returnNull($sql, $param);
            }
        }

        if ($error != "") {
            echo "<script>alert('" . strip_tags($error) . "')</script>";
        }

        // 프로필 사진 삭제
        $del_img = $this->input->post('del_img');
        if (!empty($del_img)) {
            delete_grade_thumbnail($del_img);

            $sql = "UPDATE shop_member SET mb_img = ? WHERE id = ?";
            $param = array("basic.png", $mb_id);
            $this->query_model->returnNull($sql, $param);
        }

        $mb = $this->global_lib->get_member($mb_id);
        if (!$mb['id']) {
            alert('존재하지 않는 회원자료입니다.');
        }

        if ($mb_id == 'admin') {
            alert('최고관리자는 수정하실 수 없습니다.');
        }

        if ($member['id'] != 'admin' && $mb['grade'] >= $member['grade']) {
            alert('자신보다 레벨이 높거나 같은 회원은 수정할 수 없습니다.');
        }

        if ($mb_id == $member['id'] && $mb_grade != $mb['grade']) {
            alert($mb_id . ' : 로그인 중인 관리자 레벨은 수정 할 수 없습니다.');
        }

        // 이메일중복체크
        $sql = "SELECT id, name, email FROM shop_member WHERE email = ? AND id <> ? ";
        $email = $this->input->post('email');
        $param = array($email, $mb_id);
        $row = $this->query_model->returnOneArr($sql, $param);
        if ($row['id']) {
            alert('이미 존재하는 이메일입니다.\\nＩＤ : ' . $row['id'] . '\\n이름 : ' . $row['name'] . '\\n메일 : ' . $row['email']);
        }

        // 휴대폰번호 체크
        $mb_hp = hyphen_hp_number($_POST['cellphone']);
        if ($mb_hp) {
            $result = $this->funcomplex_lib->exist_mb_hp($mb_hp, $mb_id);
            if ($result)
                alert($result);
        }

        // 인증정보처리
        $mb_certify_case = $this->input->post('mb_certify_case');
        $mb_certify = $this->input->post('mb_certify');
        if ($mb_certify_case && $mb_certify) {
            $mb_certify = $mb_certify_case;
            $mb_adult = $this->input->post('mb_adult');;
        } else {
            $mb_certify = '';
            $mb_adult = 0;
        }

        unset($mfrm);
        $mfrm['name']            = $this->input->post('name'); // 회원명
        $mfrm['pt_id']            = $this->input->post('pt_id'); // 추천인
        $mfrm['gender']            = strtoupper($this->input->post('gender')); // 성별
        $mfrm['mb_birth']        = $this->input->post('mb_birth'); // 생년월일
        $mfrm['age']            = get_birth_age($this->input->post('mb_birth')); // 연령대
        $mfrm['email']            = $this->input->post('email'); // 이메일
        $mfrm['grade']            = $mb_grade; // 레벨
        $mfrm['cellphone']        = $this->input->post('cellphone'); //핸드폰
        $mfrm['zip']            = $this->input->post('zip'); // 우편번호
        $mfrm['addr1']            = $this->input->post('addr1'); // 주소
        $mfrm['addr2']            = $this->input->post('addr2'); // 상세주소
        $mfrm['addr3']            = $this->input->post('addr3'); // 참고항목
        $mfrm['addr_jibeon']    = $this->input->post('addr_jibeon'); // 지번주소
        $mfrm['use_good']        = $this->input->post('use_good'); // 개별상품판매
        $mfrm['use_pg']            = $this->input->post('use_pg'); // 개인결제
        $mfrm['payment']        = $this->input->post('payment'); // 추가 판매수수료
        $mfrm['payflag']        = $this->input->post('payflag'); // 추가 판매수수료 유형
        $mfrm['homepage']        = $this->input->post('homepage'); // 도메인
        $mfrm['theme']            = $this->input->post('theme'); //테마스킨
        $mfrm['mobile_theme']    = $this->input->post('mobile_theme'); //모바일테마스킨
        $mfrm['memo']            = $this->input->post('memo'); // 메모
        $mfrm['intercept_date'] = $this->input->post('intercept_date'); // 접근차단일자
        $mfrm['mailser']        = $this->input->post('mailser'); // 메일수신
        $mfrm['smsser']            = $this->input->post('smsser'); // SMS수신
        $mfrm['mb_certify']        = $mb_certify; // 본인확인 인증
        $mfrm['mb_adult']        = $mb_adult; // 성인인증
        $mfrm['auth_1']            = $this->input->post('auth_1');
        $mfrm['auth_2']            = $this->input->post('auth_2');
        $mfrm['auth_3']            = $this->input->post('auth_3');
        $mfrm['auth_4']            = $this->input->post('auth_4');
        $mfrm['auth_5']            = $this->input->post('auth_5');
        $mfrm['auth_6']            = $this->input->post('auth_6');
        $mfrm['auth_7']            = $this->input->post('auth_7');
        $mfrm['auth_8']            = $this->input->post('auth_8');
        $mfrm['auth_9']            = $this->input->post('auth_9');
        $mfrm['auth_10']        = $this->input->post('auth_10');
        $passwd = $this->input->post('passwd');
        if ($passwd) {
            $passwdSql = $this->global_lib->changePassword($passwd, $mb_id);
            $mfrm['passwd'] = $passwdSql; // 패스워드
        }
        $this->query_model->updateObj("shop_member", $mfrm, "id='$mb_id'");

        $mb = $this->global_lib->get_member($mb_id);
        $pt = $this->partner_lib->get_partner($mb_id);

        // if($pt['mb_id']) {
        //     $bank_name=$this->input->post('bank_name');
        //     $param=array('bank_name'=>$bank_name);
        //     $this->query_model->updateObj('shop_partner',$param,"mb_id='{$mb_id}'");
        // }

        if (in_array($mb_grade, array(9, 8, 7, 6, 5, 4, 3, 2))) {
            $param = array('term_date' => '0000-00-00', 'anew_date' => '0000-00-00');
            $this->query_model->updateObj('shop_member', $param, "id = '$mb_id'");
            $sql = "delete from shop_partner where mb_id=?";
            $param = array($mb_id);
            $this->query_model->returnNull($sql, $param);
        }

        // if(in_array($mb_grade, array(6,5,4,3,2))) {
        //     if(is_null_time($mb['term_date'])) { // 만료일
        //         $expire_date = $this->gvcomplex_lib->get_term_date($config['pf_expire_term']);
        //         $param=array('term_date'=>$expire_date);
        //         $this->query_model->updateObj('shop_member',$param,"id = '$mb_id'");
        //     }
        //     if(is_null_time($mb['anew_date'])) { // 등업일
        //         $param=array('anew_date'=>$OM_TIME_YMD);
        //         $this->query_model->updateObj('shop_member',$param,"id = '$mb_id'");
        //     }

        //     // 회원 아이디가 존재하지 않을 경우만 실행
        //     if(!$pt['mb_id']) {
        //         $pb = $this->partner_lib->get_partner_basic($mb_grade);

        //         unset($pfrm);
        //         $pfrm['mb_id']			 = $mb_id; //아이디
        //         $pfrm['bank_name']		 = $_POST['bank_name']; //은행명
        //         $pfrm['bank_account']	 = $_POST['bank_account']; //계좌번호
        //         $pfrm['bank_holder']	 = $_POST['bank_holder']; //예금주
        //         $pfrm['anew_grade']		 = $mb_grade; //레벨 인덱스번호
        //         $pfrm['receipt_price']	 = isset($pb['gb_anew_price'])?$pb['gb_anew_price']:0; //분양개설비
        //         $pfrm['deposit_name']	 = $mb['name']; //입금자명
        //         $pfrm['pay_settle_case'] = 1; //결제방식 1은 무통장, 2는 신용카드결제
        //         $pfrm['memo']			 = '관리자에 의해 승인처리 되었습니다.'; //메모
        //         $pfrm['state']			 = 1; //처리결과 1은 완료, 0은 대기
        //         $pfrm['reg_ip']			 = $_SERVER['REMOTE_ADDR'];
        //         $pfrm['reg_time']		 = $OM_TIME_YMDHIS;
        //         $pfrm['update_time']	 = $OM_TIME_YMDHIS;
        //         $this->query_model->insertObj("shop_partner", $pfrm);

        //         $this->funcomplex_lib->insert_anew_pay($mb_id); // 추천수수료

        //     } else {
        //         // 신청내역이 이미 있으나 승인처리가 되지 않았을경우
        //         if(!$pt['state']) {
        //             $sql = " update shop_partner
        //                         set state = '1'
        //                         , anew_grade = ?
        //                     where mb_id = ?";
        //             $param=array($mb_grade,$mb_id);
        //             $this->query_model->returnNull($sql,$param);

        //             $this->funcomplex_lib->insert_anew_pay($mb_id); // 추천수수료
        //         }
        //     }
        // }

        goto_url(base_url() . 'admin/pop_memberform?mb_id=' . $mb_id);
    }
    public function member_mail_list_delete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count)
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");

        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $ma_id = trim($_POST['ma_id'][$k]);

            $sql = " select ma_content from shop_mail where ma_id =? ";
            $param = array($ma_id);
            $row = $this->query_model->returnOneArr($sql, $param);

            // 에디터 이미지 삭제
            $this->funcomplex_lib->delete_editor_image($row['ma_content']);

            // 삭제
            $sql = "delete from shop_mail where ma_id = ?";
            $param = array($ma_id);
            $this->query_model->returnNull($sql, $param);
        }

        goto_url(base_url() . "admin/member?$q1&page=$page");
    }
    public function member_point_delete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }
        $po_id = $this->input->post('po_id');
        $mb_id = $this->input->post('mb_id');
        $po_id = $this->input->post('po_id');
        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            // 포인트 내역정보
            $sql = " select * from shop_point where po_id = ?";
            $param = array($po_id[$k]);
            $row = $this->query_model->returnOneArr($sql, $param);

            if (!$row['po_id'])
                continue;

            if ($row['po_point'] < 0) {
                $mb_id = $row['mb_id'];
                $po_point = abs($row['po_point']);

                $this->global_lib->delete_use_point($mb_id, $po_point);
            } else {
                if ($row['po_use_point'] > 0) {
                    $this->global_lib->insert_use_point($row['mb_id'], $row['po_use_point'], $row['po_id']);
                }
            }

            // 포인트 내역삭제
            $sql = " delete from shop_point where po_id = ?";
            $this->query_model->returnNull($sql, $param);

            // po_mb_point에 반영
            $sql = " update shop_point
                            set po_mb_point = po_mb_point - ?
                        where mb_id = ?
                            and po_id > ?";
            $param = array($row['po_point'], $mb_id[$k], $po_id[$k]);
            $this->query_model->returnNull($sql, $param);

            // 포인트 UPDATE
            $sum_point = $this->global_lib->get_point_sum($_POST['mb_id'][$k]);
            $sql = " update shop_member set point = ? where id = ?";
            $param = array($sum_point, $mb_id[$k]);
            $this->query_model->returnNull($sql, $param);
        }

        goto_url(base_url() . "admin/member?$q1&page=$page");
    }
    public function member_mileage_delete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }
        $po_id = $this->input->post('po_id');
        $mb_id = $this->input->post('mb_id');
        $po_id = $this->input->post('po_id');
        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            // 포인트 내역정보
            $sql = " select * from shop_mileage where po_id = ?";
            $param = array($po_id[$k]);
            $row = $this->query_model->returnOneArr($sql, $param);

            if (!$row['po_id'])
                continue;

            if ($row['po_point'] < 0) {
                $mb_id = $row['mb_id'];
                $po_point = abs($row['po_point']);

                $this->global_lib->delete_use_point($mb_id, $po_point);
            } else {
                if ($row['po_use_point'] > 0) {
                    $this->global_lib->insert_use_point($row['mb_id'], $row['po_use_point'], $row['po_id']);
                }
            }

            // 포인트 내역삭제
            $sql = " delete from shop_mileage where po_id = ?";
            $this->query_model->returnNull($sql, $param);

            // po_mb_point에 반영
            $sql = " update shop_mileage
                            set po_mb_point = po_mb_point - ?
                        where mb_id = ?
                            and po_id > ?";
            $param = array($row['po_point'], $mb_id[$k], $po_id[$k]);
            $this->query_model->returnNull($sql, $param);

            // 포인트 UPDATE
            $sum_point = $this->global_lib->get_point_sum($_POST['mb_id'][$k]);
            $sql = " update shop_member set point = ? where id = ?";
            $param = array($sum_point, $mb_id[$k]);
            $this->query_model->returnNull($sql, $param);
        }

        goto_url(base_url() . "admin/member?$q1&page=$page");
    }
    public function member_point_select_update()
    {
        $member = $this->load->get_var('member');

        $po_point = trim($this->input->post('po_point'));
        $po_content = trim($this->input->post('po_content'));

        $po_list = explode(",", trim($this->input->post('point_mb_id')));
        $mb_cnt1 = count($po_list);
        $mb_list = array();
        for ($i = 0; $i < $mb_cnt1; $i++) {
            $row = $this->global_lib->get_member($po_list[$i], "id, point");
            $mb_list['id'][] = $row['id'];
            $mb_list['point'][] = $row['point'];
        }

        $mb_cnt2 = count($mb_list['id']);
        for ($i = 0; $i < $mb_cnt2; $i++) {
            $mb_id = $mb_list['id'][$i];
            $mb_point = $mb_list['point'][$i];

            $isNum = false;
            if (($po_point < 0) && ($po_point * (-1) > $mb_point))
                $isNum = true;

            if (!$isNum)
                $this->funcomplex_lib->insert_point($mb_id, $po_point, $po_content, '@passive', $mb_id, $member['id'] . '-' . uniqid(''));
        }

        alert("정상적으로 처리 되었습니다.", base_url() . 'admin/member?code=point_select_form');
    }
    #멤버 관련된 함수들 끝
    #파트너 관련된 함수들 시작
    public function pt_pformupdate()
    {
        $_POST = array_map('trim', $_POST);

        $pf_stipulation = substr($_POST['pf_stipulation'], 0, 65536);
        $pf_stipulation = preg_replace("#[\\\]+$#", "", $pf_stipulation);

        $pf_regulation = substr($_POST['pf_regulation'], 0, 65536);
        $pf_regulation = preg_replace("#[\\\]+$#", "", $pf_regulation);

        $pf_basedomain = preg_replace("/\s+/", "", $_POST['pf_basedomain']);

        unset($value);
        $value['partner_reg_yes']        = $this->input->post('partner_reg_yes');
        $value['pf_payment_yes']        = $this->input->post('pf_payment_yes');
        $value['pf_sale_use']            = $this->input->post('pf_sale_use');
        $value['pf_sale_flag']            = $this->input->post('pf_sale_flag');
        $value['pf_anew_use']            = $this->input->post('pf_anew_use');
        $value['pf_visit_use']            = $this->input->post('pf_visit_use');
        $value['pf_payment_type']        = $this->input->post('pf_payment_type');
        $value['pf_payment']            = conv_number($_POST['pf_payment']);
        $value['pf_payment_unit']        = $this->input->post('pf_payment_unit');
        $value['pf_payment_tax']        = $this->input->post('pf_payment_tax');
        $value['pf_stipulation_subj']    = $this->input->post('pf_stipulation_subj');
        $value['pf_stipulation']        = $pf_stipulation;
        $value['pf_regulation_subj']    = $this->input->post('pf_regulation_subj');
        $value['pf_regulation']            = $pf_regulation;
        $value['pf_basedomain']            = $this->input->post('pf_basedomain');
        $value['pf_auth_good']            = $this->input->post('pf_auth_good');
        $value['pf_auth_pg']            = $this->input->post('pf_auth_pg');
        $value['pf_auth_sms']            = $this->input->post('pf_auth_sms');
        $value['pf_expire_use']            = $this->input->post('pf_expire_use');
        $value['pf_expire_term']        = $this->input->post('pf_expire_term');
        $value['pf_login_no']            = $this->input->post('pf_login_no');
        $value['pf_account_no']            = $this->input->post('pf_account_no');
        $value['pf_session_no']            = $this->input->post('pf_session_no');
        $this->query_model->updateObj("shop_config", $value);

        goto_url(base_url() . "admin/partner?code=pform");
    }
    public function pt_pbasicupdate()
    {
        if (!count($_POST['chk'])) {
            alert();
        }
        $gb_no = $this->input->post('gb_no');
        for ($i = 0; $i < count($_POST['chk']); $i++) {
            // 실제 번호를 넘김
            $k = $chk[$i];
            $param['gb_name'] = $_POST['gb_name'][$k];
            $param['gb_anew_price'] = $_POST['gb_anew_price'][$k];
            $param['gb_term_price'] = $_POST['gb_term_price'][$k];
            $param['gb_visit_pay'] = $_POST['gb_visit_pay'][$k];
            $param['gb_promotion'] = $_POST['gb_promotion'][$k];
            $param['gb_name'] = $_POST['gb_name'][$k];
            $this->query_model->updateObj('shop_member_grade', $param, "gb_no = '$gb_no[$k]'");
        }
        $pf_sale_benefit_2 = $this->input->post('pf_sale_benefit_2');
        $pf_sale_benefit_3 = $this->input->post('pf_sale_benefit_3');
        $pf_sale_benefit_4 = $this->input->post('pf_sale_benefit_4');
        $pf_sale_benefit_5 = $this->input->post('pf_sale_benefit_5');
        $pf_sale_benefit_6 = $this->input->post('pf_sale_benefit_6');
        $pf_anew_benefit_2 = $this->input->post('pf_anew_benefit_2');
        $pf_anew_benefit_3 = $this->input->post('pf_anew_benefit_3');
        $pf_anew_benefit_4 = $this->input->post('pf_anew_benefit_4');
        $pf_anew_benefit_5 = $this->input->post('pf_anew_benefit_5');
        $pf_anew_benefit_6 = $this->input->post('pf_anew_benefit_6');

        $pf_sale_benefit_2 = is_array($pf_sale_benefit_2) ? implode(' ', $pf_sale_benefit_2) : '';
        $pf_sale_benefit_3 = is_array($pf_sale_benefit_3) ? implode(' ', $pf_sale_benefit_3) : '';
        $pf_sale_benefit_4 = is_array($pf_sale_benefit_4) ? implode(' ', $pf_sale_benefit_4) : '';
        $pf_sale_benefit_5 = is_array($pf_sale_benefit_5) ? implode(' ', $pf_sale_benefit_5) : '';
        $pf_sale_benefit_6 = is_array($pf_sale_benefit_6) ? implode(' ', $pf_sale_benefit_6) : '';
        $pf_anew_benefit_2 = is_array($pf_anew_benefit_2) ? implode(' ', $pf_anew_benefit_2) : '';
        $pf_anew_benefit_3 = is_array($pf_anew_benefit_3) ? implode(' ', $pf_anew_benefit_3) : '';
        $pf_anew_benefit_4 = is_array($pf_anew_benefit_4) ? implode(' ', $pf_anew_benefit_4) : '';
        $pf_anew_benefit_5 = is_array($pf_anew_benefit_5) ? implode(' ', $pf_anew_benefit_5) : '';
        $pf_anew_benefit_6 = is_array($pf_anew_benefit_6) ? implode(' ', $pf_anew_benefit_6) : '';

        $sql = " update shop_config
                set pf_sale_benefit_dan = '{$pf_sale_benefit_dan}'
                , pf_sale_benefit_type = '{$pf_sale_benefit_type}'
                , pf_sale_benefit_2 = '{$pf_sale_benefit_2}'
                , pf_sale_benefit_3 = '{$pf_sale_benefit_3}'
                , pf_sale_benefit_4 = '{$pf_sale_benefit_4}'
                , pf_sale_benefit_5 = '{$pf_sale_benefit_5}'
                , pf_sale_benefit_6 = '{$pf_sale_benefit_6}'
                , pf_anew_benefit_dan = '{$pf_anew_benefit_dan}'
                , pf_anew_benefit_type = '{$pf_anew_benefit_type}'
                , pf_anew_benefit_2 = '{$pf_anew_benefit_2}'
                , pf_anew_benefit_3 = '{$pf_anew_benefit_3}'
                , pf_anew_benefit_4 = '{$pf_anew_benefit_4}'
                , pf_anew_benefit_5 = '{$pf_anew_benefit_5}'
                , pf_anew_benefit_6 = '{$pf_anew_benefit_6}' ";
        $this->query_model->returnNull($sql);
        goto_url(base_url() . 'admin/partner?code=pbasic');
    }
    public function pt_ppayupdate()
    {
        $member = $this->load->get_var('member');
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $_POST = array_map('trim', $_POST);
        $mb_id = $_POST['mb_id'];
        $pp_pay = $_POST['pp_pay'];
        $pp_content = $_POST['pp_content'];

        $mb = $this->global_lib->get_member($mb_id, 'id, pay');
        if (!$mb['id'])
            alert("존재하는 회원아이디가 아닙니다.");

        if (($pp_pay < 0) && ($pp_pay * (-1) > $mb['pay']))
            alert("수수료를 차감하는 경우 현재 수수료보다 작으면 안됩니다.");

        $this->funcomplex_lib->insert_pay($mb_id, $pp_pay, $pp_content, 'passive', $mb_id, $member['id'] . '-' . uniqid(''));
        goto_url(base_url() . "admin/partner?$q1&page=$page");
    }
    public function pt_anewlistupdate()
    {
        $config = $this->load->get_var('config');
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $count = count($_POST['chk']);
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }
        $chk = array_reverse($_POST['chk']);
        if ($_POST['act_button'] == "선택승인") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $chk[$i];

                $mb_id = trim($_POST['mb_id'][$k]);

                $pt = $this->partner_lib->get_partner($mb_id, 'state, anew_grade');

                // 이미승인된 회원이면 건너뜀
                if ($pt['state'])
                    continue;

                // 승인
                $sql = " update shop_partner set state = '1' where mb_id = ?";
                $param = array($mb_id);
                $this->query_model->returnNull($sql, $param);

                $term_date = $this->gvcomplex_lib->get_term_date($config['pf_expire_term']); // 만료일

                $param2['grade'] = $pt['anew_grade'];
                $param2['anew_date'] = $OM_TIME_YMD;
                $param2['term_date'] = $term_date;
                $this->query_model->updateObj('shop_member', $param2, "id='$mb_id'");

                // 추천 소개수수료
                $this->funcomplex_lib->insert_anew_pay($mb_id);
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $chk[$i];

                $mb_id = trim($_POST['mb_id'][$k]);

                // 삭제
                $sql = " delete from shop_partner where mb_id = ?";
                $param = array($mb_id);
                $this->query_model->returnNull($sql, $param);

                $sql = " update shop_member
                                set grade = '9'
                                , anew_date = '0000-00-00'
                                , term_date = '0000-00-00'
                            where id = ?";
                $param = array($mb_id);
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/partner?$q1&page=$page");
    }
    public function pt_plistupdate()
    {
        $count = count($_POST['chk']);
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "기간연장") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $mb_id = trim($_POST['mb_id'][$k]);

                $mb = $this->global_lib->get_member($mb_id, 'term_date');

                if (is_null_time($mb['term_date'])) // 시간이 비어있는가?
                    $new_date = date("Y-m-d", strtotime("+{$_POST['expire_date']} month", time()));
                else
                    $new_date = date("Y-m-d", strtotime("+{$_POST['expire_date']} month", strtotime($mb['term_date'])));

                // 기간연장을 한다.
                $sql = "update shop_member set term_date = ? where id = ?";
                $param = array($new_date, $mb_id);
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/partner?$q1&page=$page");
    }
    public function pt_category_update()
    {
        $mb_id = $this->input->post('mb_id');
        $ca_no = $this->input->post('ca_no');
        $token = $this->input->post('token');

        $mb = $this->global_lib->get_member($mb_id, 'id');
        if (!$mb['id']) {
            die("{\"error\":\"회원아이디가 존재하지 않습니다.\"}");
        }
        $sql = "select * from shop_category where index_no = ?";
        $param = array($ca_no);
        $cat = $this->query_model->returnOneArr($sql, $param);
        $cat['catehide'] = preg_replace('/\s+/', '', $cat['catehide']);

        $count_yes = false;
        if (in_array($mb_id, explode(",", $cat['catehide']))) {
            $count_yes = true;
        }
        $s_value = "";
        if (!$count_yes) {
            $list_data = explode(",", $cat['catehide']);
            $list_data = array_diff($list_data, array($mb_id)); // 적용 아이디 모두제거
            array_push($list_data, $mb_id);  // 적용아이디 배열에 추가
            $list_data = array_unique($list_data); //중복된 아이디 제거
            $list_data = array_filter($list_data); // 빈 배열 요소를 제거
            $list_data = array_values($list_data); // index 값 주기
            $s_value = implode(",", $list_data);
        } else {
            $list_data = explode(",", $cat['catehide']);
            $list_data = array_diff($list_data, array($mb_id)); // 적용 아이디 모두제거
            $list_data = array_unique($list_data); //중복된 아이디 제거
            $list_data = array_filter($list_data); // 빈 배열 요소를 제거
            $list_data = array_values($list_data); // index 값 주기
            $s_value = implode(",", $list_data);
        }
        $len = strlen($cat['catecode']);
        $sql = "update shop_category set catehide = ? where SUBSTRING(catecode,1,$len) = ?";
        $param = array($s_value, $cat['catecode']);
        $result = $this->query_model->returnNull($sql, $param);
        if ($result)
            die("{\"error\":\"\"}"); // 정상
        else
            die("{\"error\":\"일시적인 오류\"}");
    }
    public function pt_termlistupdate()
    {
        $count = count($_POST['chk']);
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "선택승인") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $index_no = trim($_POST['index_no'][$k]);
                $mb_id    = trim($_POST['mb_id'][$k]);

                $row = $this->partner_lib->get_partner_term($index_no);

                // 이미승인된건이면 건너뜀
                if ($row['state'])
                    continue;

                $mb = $this->global_lib->get_member($mb_id, 'term_date');

                if (is_null_time($mb['term_date'])) // 시간이 비어있는가?
                    $new_date = date("Y-m-d", strtotime("+{$row['expire_date']} month", time()));
                else
                    $new_date = date("Y-m-d", strtotime("+{$row['expire_date']} month", strtotime($mb['term_date'])));

                // 기간연장을 한다.	
                $sql1 = "update shop_member set term_date = ? where id = ?";
                $param1 = array($new_date, $mb_id);
                $this->query_model->returnNull($sql1, $para1);
                $sql2 = "update shop_partner_term set state = '1' where index_no = ?";
                $param2 = array($index_no);
                $this->query_model->returnNull($sql2, $param2);
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $index_no = trim($_POST['index_no'][$k]);
                $sql = "delete from shop_partner_term where index_no = ?";
                $param = array($index_no);
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/partner?$q1&page=$page");
    }
    public function pt_payrunexcel()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $sst = $this->input->get('sst');
        $spt = $this->input->get('spt');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $sql_common = " from shop_partner_payrun a, shop_member b ";
        $sql_search = " where a.mb_id = b.id and a.state = '0' ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if (isset($sst) && is_numeric($sst))
            $sql_search .= " and b.grade = '$sst' ";

        if ($fr_date && $to_date)
            $sql_search .= " and left({$spt},10) between '$fr_date' and '$to_date' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and left({$spt},10) between '$fr_date' and '$fr_date' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and left({$spt},10) between '$to_date' and '$to_date' ";

        if (!$orderby) {
            $filed = "a.index_no";
            $sod = "desc";
        } else {
            $sod = $orderby;
        }

        $sql_order = " order by $filed $sod ";

        $sql = " select a.*, b.name, b.grade, b.term_date, b.pay $sql_common $sql_search $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $result = $resultObj->result_array();
        $cnt = $resultObj->num_rows();
        if (!$cnt)
            alert("출력할 내역이 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '회원명')
            ->setCellValue($char++ . '1', '아이디')
            ->setCellValue($char++ . '1', '레벨')
            ->setCellValue($char++ . '1', '신청일시')
            ->setCellValue($char++ . '1', '만료일')
            ->setCellValue($char++ . '1', '현재잔액')
            ->setCellValue($char++ . '1', '출금요청')
            ->setCellValue($char++ . '1', '세액공제')
            ->setCellValue($char++ . '1', '실수령액')
            ->setCellValue($char++ . '1', '은행명')
            ->setCellValue($char++ . '1', '계좌번호')
            ->setCellValue($char++ . '1', '예금주명');

        $i = 2;
        foreach ($result as $key => $row) {
            $expire_date = '무제한';

            // 관리비를 사용중인가?
            if ($config['pf_expire_use']) {
                if ($row['term_date'] < $OM_TIME_YMD)
                    $expire_date = '만료' . substr(conv_number($row['term_date']), 2);
                else
                    $expire_date = $row['term_date'];
            }

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $this->global_lib->get_grade($row['grade']), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['reg_time'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $expire_date, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['pay'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['balance'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['paytax'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['paynet'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['bank_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['bank_account'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['bank_holder'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('가맹점 수수료정산');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="가맹점 수수료정산-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function pt_payhistoryexcel()
    {
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $sst = $this->input->get('sst');
        $rel_field = $this->input->get('rel_field');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $sql_common = " from shop_partner_pay a, shop_member b ";
        $sql_search = " where a.mb_id = b.id ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if (isset($sst) && is_numeric($sst))
            $sql_search .= " and b.grade = '$sst' ";

        if ($rel_field)
            $sql_search .= " and a.pp_rel_table = '{$rel_field}' ";

        if ($fr_date && $to_date)
            $sql_search .= " and a.pp_datetime between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and a.pp_datetime between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and a.pp_datetime between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        if (!$orderby) {
            $filed = "a.pp_id";
            $sod = "desc";
        } else {
            $sod = $orderby;
        }

        $sql_order = " order by {$filed} {$sod} ";

        $sql = " select a.*, b.name, b.grade {$sql_common} {$sql_search} {$sql_order} ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $result = $resultObj->result_array();
        $cnt = $resultObj->num_rows();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '회원명')
            ->setCellValue($char++ . '1', '아이디')
            ->setCellValue($char++ . '1', '레벨')
            ->setCellValue($char++ . '1', '수수료내용')
            ->setCellValue($char++ . '1', '일시')
            ->setCellValue($char++ . '1', '구분')
            ->setCellValue($char++ . '1', '수수료')
            ->setCellValue($char++ . '1', '수수료합');

        $i = 2;
        foreach ($result as $key => $row) {
            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $this->global_lib->get_grade($row['grade']), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['pp_content'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['pp_datetime'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $gw_ptype[$row['pp_rel_table']], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['pp_pay'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['pp_balance'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('가맹점 수수료내역');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="가맹점 수수료내역-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function pt_order1excel()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $sst = $this->input->get('sst');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $sql_common = " from shop_member ";
        $sql_search = " where grade between 2 and 6 ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if (isset($sst) && is_numeric($sst))
            $sql_search .= " and grade = '$sst' ";

        if (!$orderby) {
            $filed = "index_no";
            $sod = "desc";
        } else {
            $sod = $orderby;
        }

        $sql_order = " order by $filed $sod ";

        $sql = " select * $sql_common $sql_search $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 내역이 없습니다.");

        $sql_time = "";
        if ($fr_date && $to_date)
            $sql_time .= " and left(od_time,10) between '$fr_date' and '$to_date' ";
        else if ($fr_date && !$to_date)
            $sql_time .= " and left(od_time,10) between '$fr_date' and '$fr_date' ";
        else if (!$fr_date && $to_date)
            $sql_time .= " and left(od_time,10) between '$to_date' and '$to_date' ";

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '회원명')
            ->setCellValue($char++ . '1', '아이디')
            ->setCellValue($char++ . '1', '레벨')
            ->setCellValue($char++ . '1', '판매상품수')
            ->setCellValue($char++ . '1', '주문액')
            ->setCellValue($char++ . '1', '오늘')
            ->setCellValue($char++ . '1', '어제')
            ->setCellValue($char++ . '1', '일주일')
            ->setCellValue($char++ . '1', '지난달')
            ->setCellValue($char++ . '1', '3개월');
        $i = 2;
        foreach ($result as $key => $row) {
            $info = $this->common_lib->partner_order_status_sum($row['id'], $sql_time);

            // 오늘
            $and1 = " and left(od_time,10) between '" . $OM_TIME_YMD . "' and '" . $OM_TIME_YMD . "' ";
            $row1 = $this->common_lib->partner_order_status_sum($row['id'], $and1);

            // 어제
            $yesterday = date("Y-m-d", strtotime("-1 day"));
            $and2 = " and left(od_time,10) between '" . $yesterday . "' and '" . $OM_TIME_YMD . "' ";
            $row2 = $this->common_lib->partner_order_status_sum($row['id'], $and2);

            // 일주일
            $weekday = date("Y-m-d", strtotime("-7 day"));
            $and3 = " and left(od_time,10) between '" . $weekday . "' and '" . $OM_TIME_YMD . "' ";
            $row3 = $this->common_lib->partner_order_status_sum($row['id'], $and3);

            // 지난달
            $month1 = date("Y-m", strtotime("-1 month"));
            $and4 = " and left(od_time,7) = '$month1' ";
            $row4 = $this->common_lib->partner_order_status_sum($row['id'], $and4);

            // 3개월
            $month3 = date("Y-m-d", strtotime("-3 month"));
            $and5 = " and left(od_time,10) between '" . $month3 . "' and '" . $OM_TIME_YMD . "' ";
            $row5 = $this->common_lib->partner_order_status_sum($row['id'], $and5);

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $this->global_lib->get_grade($row['grade']), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $info['cnt'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $info['price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row1['price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row2['price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row3['price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row4['price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row5['price'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('가맹점 주문통계');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="가맹점 주문통계-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function ajax_sale_benefit()
    {
        $config = $this->load->get_var('config');
        $dan = trim($_POST['dan']);
        $sale_benefit = array();
        $echoTag = "";
        for ($g = 6; $g > 1; $g--) {
            $sale_benefit[$g] = explode(' ', $config['pf_sale_benefit_' . $g]);
        }

        for ($i = 0; $i < (int)$dan; $i++) {
            $echoTag .=
                "<tr class=\"tr_alignc\">
                    <td class=\"bg3\">" . ($i + 1) . "UP</td>";
            for ($g = 6; $g > 1; $g--) {
                $amount = (int)trim($sale_benefit[$g][$i]);
                $echoTag .= "
                        <td>                
                            <label for=\"pf_sale_benefit_{$g}_{$i}\" class=\"sound_only\">" . ($i + 1) . "UP {$g}레벨 인센티브</label>
                            <input type=\"text\" name=\"pf_sale_benefit_{$g}[{$i}]\" value=\"{$amount}\" id=\"pf_sale_benefit_{$g}_{$i}\" class=\"frm_input\" size=\"8\">
                        </td>";
            }
            $echoTag .= "
                </tr>";
        }
        if ($i == 0) {
            $echoTag .= '<tr class="tr_alignc"><td colspan="6" class="empty_table">추가 인센티브 설정값이 없습니다.</td></tr>';
        }
        echo $echoTag;
    }
    public function ajax_anew_benefit()
    {
        $dan = trim($_POST['dan']);
        $anew_benefit = array();
        $echoTag = "";
        for ($g = 6; $g > 1; $g--) {
            $anew_benefit[$g] = explode(' ', $config['pf_anew_benefit_' . $g]);
        }

        for ($i = 0; $i < (int)$dan; $i++) {
            $echoTag .= "
                <tr class=\"tr_alignc\">
                    <td class=\"bg3\">" . ($i + 1) . "UP</td>";
            for ($g = 6; $g > 1; $g--) {
                $amount = (int)trim($anew_benefit[$g][$i]);
                $echoTag .= "
                    <td>                
                        <label for=\"pf_anew_benefit_{$g}_{$i}\" class=\"sound_only\">" . ($i + 1) . "UP {$g}레벨 인센티브</label>
                        <input type=\"text\" name=\"pf_anew_benefit_{$g}[{$i}]\" value=\"{$amount}\" id=\"pf_anew_benefit_{$g}_{$i}\" class=\"frm_input\" size=\"8\"> 
                    </td>";
            }
            $echoTag .= "
                </tr>";
        }
        if ($i == 0) {
            $echoTag .= '<tr class="tr_alignc"><td colspan="6" class="empty_table">추가 인센티브 설정값이 없습니다.</td></tr>';
        }
        echo $echoTag;
    }
    public function pt_payhistorydelete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }
        $pp_id = $this->input->post('pp_id');
        $mb_id = $this->input->post('mb_id');
        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            // 수수료 내역정보
            $sql = " select * from shop_partner_pay where pp_id = ?";
            $param = array($pp_id[$k]);
            $row = $this->query_model->returnOneArr($sql, $param);

            if (!$row['pp_id'])
                continue;

            if ($row['pp_pay'] < 0) {
                $mb_id = $row['mb_id'];
                $pp_pay = abs($row['pp_pay']);

                $this->partner_lib->delete_use_pay($mb_id, $pp_pay);
            } else {
                if ($row['pp_use_pay'] > 0) {
                    $this->partner_lib->insert_use_pay($row['mb_id'], $row['pp_use_pay'], $row['pp_id']);
                }
            }

            // 수수료 내역삭제
            $sql = " delete from shop_partner_pay where pp_id = ? ";
            $this->query_model->returnNull($sql, $param);

            // pp_balance에 반영
            $sql = " update shop_partner_pay
                            set pp_balance = pp_balance - ?
                        where mb_id = ?
                            and pp_id > ?";
            $param2 = array($row['pp_pay'], $mb_id[$k], $pp_id[$k]);
            $this->query_model->returnNull($sql, $param2);

            // 수수료 UPDATE
            $sum_pay = $this->partner_lib->get_pay_sum($mb_id[$k]);
            $sql = " update shop_member set pay = ? where id = ? ";
            $param3 = array($sum_pay, $mb_id[$k]);
            $this->query_model->returnNull($sql, $param3);
        }

        goto_url(base_url() . "admin/partner?$q1&page=$page");
    }
    public function pt_paylistexcel()
    {
        $config = $this->load->get_var('config');

        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);

        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $sst = $this->input->get('sst');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $sql_common = " from shop_member ";
        $sql_search = " where grade between 2 and 6 ";

        if ($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if (isset($sst) && is_numeric($sst))
            $sql_search .= " and grade = '$sst' ";

        if ($fr_date && $to_date)
            $sql_search .= " and term_date between '$fr_date' and '$to_date' ";
        else if ($fr_date && !$to_date)
            $sql_search .= " and term_date between '$fr_date' and '$fr_date' ";
        else if (!$fr_date && $to_date)
            $sql_search .= " and term_date between '$to_date' and '$to_date' ";

        if (!$orderby) {
            $filed = "index_no";
            $sod = "desc";
        } else {
            $sod = $orderby;
        }

        $sql_order = " order by $filed $sod ";

        $sql = " select * $sql_common $sql_search $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '회원명')
            ->setCellValue($char++ . '1', '아이디')
            ->setCellValue($char++ . '1', '레벨')
            ->setCellValue($char++ . '1', '만료일')
            ->setCellValue($char++ . '1', '현재잔액')
            ->setCellValue($char++ . '1', '총적립액')
            ->setCellValue($char++ . '1', '총차감액')
            ->setCellValue($char++ . '1', '판매')
            ->setCellValue($char++ . '1', '추천')
            ->setCellValue($char++ . '1', '접속')
            ->setCellValue($char++ . '1', '본사');
        $i = 2;
        foreach ($result as $key => $row) {
            $expire_date = '무제한';

            // 관리비를 사용중인가?
            if ($config['pf_expire_use']) {
                if ($row['term_date'] < $OM_TIME_YMD)
                    $expire_date = '만료' . substr(conv_number($row['term_date']), 2);
                else
                    $expire_date = $row['term_date'];
            }

            $info  = $this->partner_lib->get_pay_sheet($row['id']); // 누적
            $sale  = $this->partner_lib->get_pay_status($row['id'], 'sale'); // 판매
            $anew  = $this->partner_lib->get_pay_status($row['id'], 'anew'); // 추천
            $visit = $this->partner_lib->get_pay_status($row['id'], 'visit'); // 접속
            $admin = $this->partner_lib->get_pay_status($row['id'], 'passive'); // 본사

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $this->global_lib->get_grade($row['grade']), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $expire_date, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['pay'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $info['pay'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $info['usepay'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $sale['pay'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $anew['pay'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $visit['pay'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $admin['pay'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('가맹점수수료');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="가맹점수수료-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function pt_payrunupdate()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);

        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');

        $member = $this->load->get_var('member');

        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "선택정산") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $index_no = trim($_POST['index_no'][$k]);
                $mb_id    = trim($_POST['mb_id'][$k]);

                $row = $this->partner_lib->get_partner_payrun($index_no);

                // 출금요청액
                $pp_pay = $row['balance'];

                // 이미승인된건이면 건너뜀
                if ($row['state'])
                    continue;

                if ($pp_pay < 1)
                    continue;

                $mb = $this->global_lib->get_member($mb_id, 'id, pay');
                if (!$mb['id'])
                    alert($mb_id . ' 님은 존재하는 회원아이디가 아닙니다.');

                if ($pp_pay > $mb['pay'])
                    alert($mb_id . ' 님의 지급수수료가 현재수수료잔액을 초과하였습니다.');

                $this->funcomplex_lib->insert_pay($mb_id, $pp_pay * (-1), $OM_TIME_YMD . ' 수수료 정산완료', 'payment', $mb_id, $member['id'] . '-' . uniqid(''));

                $sql = " update shop_partner_payrun set state = '1' where index_no = ?";
                $param = array($index_no);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $index_no = trim($_POST['index_no'][$k]);

                $sql = " delete from shop_partner_payrun where index_no = ?";
                $param = array($index_no);
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/partner?$q1&page=$page");
    }
    #파트너 관련된 함수들 끝
    #공급자 관련된 함수들 시작
    public function seller_list_excel()
    {
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $sql_common = " from shop_seller a, shop_member b ";
        $sql_search = " where a.mb_id = b.id ";

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

        if (!$orderby) {
            $filed = "a.state";
            $sod = "asc, a.index_no desc";
        } else {
            $sod = $orderby;
        }

        $sql_order = " order by $filed $sod ";

        $sql = " select a.* $sql_common $sql_search $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '아이디')
            ->setCellValue($char++ . '1', '업체코드')
            ->setCellValue($char++ . '1', '제공상품')
            ->setCellValue($char++ . '1', '입점사명')
            ->setCellValue($char++ . '1', '사업자등록번호')
            ->setCellValue($char++ . '1', '전화번호')
            ->setCellValue($char++ . '1', '팩스번호')
            ->setCellValue($char++ . '1', '우편번호')
            ->setCellValue($char++ . '1', '사업장주소')
            ->setCellValue($char++ . '1', '업태')
            ->setCellValue($char++ . '1', '종목')
            ->setCellValue($char++ . '1', '대표자명')
            ->setCellValue($char++ . '1', '홈페이지')
            ->setCellValue($char++ . '1', '전달사항')
            ->setCellValue($char++ . '1', '은행명')
            ->setCellValue($char++ . '1', '계좌번호')
            ->setCellValue($char++ . '1', '예금주명')
            ->setCellValue($char++ . '1', '담당자명')
            ->setCellValue($char++ . '1', '담당자이메일')
            ->setCellValue($char++ . '1', '담당자전화번호');
        $i = 2;
        foreach ($result as $key => $row) {
            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['seller_code'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['seller_item'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_saupja_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_tel'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_fax'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_zip'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $this->funcomplex_lib->print_address($row['company_addr1'], $row['company_addr2'], $row['company_addr3'], $row['company_addr_jibeon'], PHPExcel_Cell_DataType::TYPE_STRING))
                ->setCellValueExplicit($char++ . $i, $row['company_item'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_service'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_owner'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, set_http($row['company_hompage'], PHPExcel_Cell_DataType::TYPE_STRING))
                ->setCellValueExplicit($char++ . $i, $row['memo'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['bank_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['bank_account'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['bank_holder'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['info_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['info_email'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['info_tel'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('공급업체');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="공급업체-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function seller_register_update()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $memo = $this->input->post('memo');
        unset($value);
        $value['seller_code']          = $this->global_lib->code_uniqid();
        $value['mb_id']                  = $this->input->post('mb_id');
        $value['seller_item']          = $this->input->post('seller_item');
        $value['company_name']          = $this->input->post('company_name');
        $value['company_saupja_no']      = $this->input->post('company_saupja_no');
        $value['company_item']          = $this->input->post('company_item');
        $value['company_service']      = $this->input->post('company_service');
        $value['company_owner']          = $this->input->post('company_owner');
        $value['company_tel']          = $this->input->post('company_tel');
        $value['company_tax_email']      = $this->input->post('company_tax_email');
        $value['company_fax']          = $this->input->post('company_fax');
        $value['company_zip']          = $this->input->post('company_zip');
        $value['company_addr1']          = $this->input->post('company_addr1');
        $value['company_addr2']          = $this->input->post('company_addr2');
        $value['company_addr3']          = $this->input->post('company_addr3');
        $value['company_addr_jibeon'] = $this->input->post('company_addr_jibeon');
        $value['company_hompage']      = $this->input->post('company_hompage');
        $value['info_name']              = $this->input->post('info_name');
        $value['info_tel']              = $this->input->post('info_tel');
        $value['info_email']          = $this->input->post('info_email');
        $value['bank_name']              = $this->input->post('bank_name');
        $value['bank_account']          = $this->input->post('bank_account');
        $value['bank_holder']          = $this->input->post('bank_holder');
        $value['memo']                  = ($memo == "") ? "" : $memo;
        $value['reg_time']              = $OM_TIME_YMDHIS;
        $value['update_time']          = $OM_TIME_YMDHIS;
        $this->query_model->insertObj("shop_seller", $value);

        goto_url(base_url() . 'admin/seller?code=register');
    }
    public function seller_mail_select_update()
    {
        include_once("./lib/mailer.lib.php");
        $config = $this->load->get_var('config');
        $countgap  = 10; // 몇건씩 보낼지 설정
        $maxscreen = 500; // 몇건씩 화면에 보여줄건지?
        $sleepsec  = 200; // 천분의 몇초간 쉴지 설정

        echo '
            <div class="local_desc02 local_desc">
                <p>
                    메일 발송중 ...<br>
                    처리완료까지 다소 시간이 걸릴 수 있습니다.<br>
                    <strong class="fc_red">[끝]</strong> 이라는 단어가 나오기 전에는 중간에 중지하지 마세요.
                </p>
            </div>

            <div class="local_desc01 local_desc">
                <span id="cont"></span>
            </div>

            <div class="btn_confirm">
                <a href="' . base_url() . 'admin/seller?code=mail_select_form" class="btn_large">확인</a>
            </div>';
        $ma_subject = trim($this->input->post('ma_subject'));
        $ma_content = trim($this->input->post('ma_content'));
        $select_seller_list = trim($this->input->post('ma_list'));
        //print_r2($_POST); EXIT;
        $seller_list = explode("\n", conv_unescape_nl($select_seller_list));

        $cnt = 0;
        for ($i = 0; $i < count($seller_list); $i++) {
            list($to_email, $mb_id, $name, $owner, $saupja_no, $tel, $fax, $dam) = explode("||", trim($seller_list[$i]));

            $sw = preg_match("/[0-9a-zA-Z_]+(\.[0-9a-zA-Z_]+)*@[0-9a-zA-Z_]+(\.[0-9a-zA-Z_]+)*/", $to_email);
            // 올바른 메일 주소만
            if ($sw == true) {
                $cnt++;

                $content = $ma_content;
                $content = preg_replace("/{아이디}/", $mb_id, $content);
                $content = preg_replace("/{회사명}/", $name, $content);
                $content = preg_replace("/{대표자명}/", $owner, $content);
                $content = preg_replace("/{사업자등록번호}/", $saupja_no, $content);
                $content = preg_replace("/{전화번호}/", $tel, $content);
                $content = preg_replace("/{팩스번호}/", $fax, $content);
                $content = preg_replace("/{담당자명}/", $dam, $content);
                mailer($config['company_name'], $member['email'], $to_email, $ma_subject, $content, 1);
                echo "<script> document.all.cont.innerHTML += '$cnt. $to_email ($mb_id : $name)<br>'; </script>\n";
                //echo "+";
                flush();
                ob_flush();
                ob_end_flush();
                usleep($sleepsec);
                if ($cnt % $countgap == 0) {
                    echo "<script> document.all.cont.innerHTML += '<br>'; document.body.scrollTop += 1000; </script>\n";
                }

                // 화면을 지운다... 부하를 줄임
                if ($cnt % $maxscreen == 0)
                    echo "<script> document.all.cont.innerHTML = ''; document.body.scrollTop += 1000; </script>\n";
            }
        }
        echo '<script> document.all.cont.innerHTML += "<br>총 <?php echo number_format($cnt); ?>건 발송<br><br><font color=crimson><b>[끝]</b></font>"; document.body.scrollTop += 1000; </script>';
    }
    public function seller_pay_excel()
    {
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

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

        $sql = " select a.*, b.mb_id, b.company_name, b.bank_name, b.bank_account, b.bank_holder
                        $sql_common
                        $sql_search
                        $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '업체코드')
            ->setCellValue($char++ . '1', '입점사명')
            ->setCellValue($char++ . '1', '아이디')
            ->setCellValue($char++ . '1', '총건수')
            ->setCellValue($char++ . '1', '주문금액')
            ->setCellValue($char++ . '1', '포인트결제')
            ->setCellValue($char++ . '1', '예치금결제')
            ->setCellValue($char++ . '1', '쿠폰할인')
            ->setCellValue($char++ . '1', '배송비')
            ->setCellValue($char++ . '1', '공급가총액')
            ->setCellValue($char++ . '1', '실정산액')
            ->setCellValue($char++ . '1', '가맹점수수료')
            ->setCellValue($char++ . '1', '본사마진')
            ->setCellValue($char++ . '1', '은행명')
            ->setCellValue($char++ . '1', '계좌번호')
            ->setCellValue($char++ . '1', '예금주명');

        $i = 2;
        foreach ($result as $key => $row) {
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

            $sql2 = " select * from shop_order where seller_id = ? and dan IN(5,8) and sellerpay_yes = '0' and user_ok = '1' ";
            $param2 = array($row['seller_id']);
            if ($fr_date && $to_date) {
                $sql2 .= " and left(od_time,10) between '$fr_date' and '$to_date' ";
            }
            $res2 = $this->query_model->returnArr($sql2, $param2);
            foreach ($res2 as $key => $row2) {
                $psql = " select SUM(pp_pay) as sum_pay
                                from shop_partner_pay
                            where pp_rel_table = 'sale'
                                and pp_rel_id = ?
                                and pp_rel_action = ?";
                $param = array($row2['od_no'], $row2['od_id']);
                $psum = $this->query_model->returnOneArr($psql, $param);

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

            // 정산액 = (공급가합 + 배송비)
            $tot_seller = ($tot_supply + $tot_baesong);

            // 본사마진 = (판매가 - 공급가 - 가맹점수수료 - 포인트결제 - 쿠폰할인)
            $tot_admin = ($tot_price - $tot_supply - $tot_partner - $tot_point - $tot_coupon);

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['seller_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, count($order_idx), PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $tot_price, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $tot_point, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $tot_mileage, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $tot_coupon, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $tot_baesong, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $tot_supply, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $tot_seller, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $tot_partner, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $tot_admin, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['bank_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['bank_account'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['bank_holder'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('공급업체정산');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="공급업체정산-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function seller_pay_history_excel()
    {
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

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

        $sql = " select a.*, b.seller_code, b.company_name
                        $sql_common
                        $sql_search
                        $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '업체코드')
            ->setCellValue($char++ . '1', '입점사명')
            ->setCellValue($char++ . '1', '아이디')
            ->setCellValue($char++ . '1', '총건수')
            ->setCellValue($char++ . '1', '주문금액')
            ->setCellValue($char++ . '1', '포인트결제')
            ->setCellValue($char++ . '1', '예치금결제')
            ->setCellValue($char++ . '1', '쿠폰할인')
            ->setCellValue($char++ . '1', '배송비')
            ->setCellValue($char++ . '1', '공급가총액')
            ->setCellValue($char++ . '1', '실정산액')
            ->setCellValue($char++ . '1', '가맹점수수료')
            ->setCellValue($char++ . '1', '본사마진')
            ->setCellValue($char++ . '1', '일시');

        $i = 2;
        foreach ($result as $key => $row) {
            $order_idx = explode(',', $row['order_idx']);

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['seller_code'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, count($order_idx), PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['tot_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['tot_point'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['tot_mileage'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['tot_coupon'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['tot_baesong'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['tot_supply'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['tot_seller'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['tot_partner'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['tot_admin'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['reg_time'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('공급업체정산내역');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="공급업체정산내역-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function pop_orderformupdate()
    {
        $od_id = $this->input->post('od_id');
        // 입금계좌
        if ($_POST['mod_type'] == 'receipt') {
            unset($value);
            $value['bank']            = $this->input->post('bank');
            $value['pt_id']            = $this->input->post('pt_id');
            $value['deposit_name']    = $this->input->post('deposit_name');
            $this->query_model->updateObj("shop_order", $value, "od_id='$od_id'");
        }

        // 관리자메모
        if ($_POST['mod_type'] == 'memo') {
            unset($value);
            $value['shop_memo']        = $this->input->post('shop_memo');
            $this->query_model->updateObj("shop_order", $value, "od_id='$od_id'");
        }

        // 주문자/배송지 정보
        if ($_POST['mod_type'] == 'info') {
            unset($value);
            $value['name']            = $this->input->post('name');
            $value['telephone']        = $this->input->post('telephone');
            $value['cellphone']        = $this->input->post('cellphone');
            $value['zip']            = $this->input->post('zip');
            $value['addr1']            = $this->input->post('addr1');
            $value['addr2']            = $this->input->post('addr2');
            $value['addr3']            = $this->input->post('addr3');
            $value['addr_jibeon']    = $this->input->post('addr_jibeon');
            $value['email']            = $this->input->post('email');
            $value['b_name']        = $this->input->post('b_name');
            $value['b_telephone']    = $this->input->post('b_telephone');
            $value['b_cellphone']    = $this->input->post('b_cellphone');
            $value['b_zip']            = $this->input->post('b_zip');
            $value['b_addr1']        = $this->input->post('b_addr1');
            $value['b_addr2']        = $this->input->post('b_addr2');
            $value['b_addr3']        = $this->input->post('b_addr3');
            $value['b_addr_jibeon']    = $this->input->post('b_addr_jibeon');
            $this->query_model->updateObj("shop_order", $value, "od_id='$od_id'");
        }

        goto_url(base_url() . "admin/pop_orderform?od_id=$od_id");
    }
    public function pop_orderbaesongupdate()
    {
        $od_id = $this->input->post('od_id');
        $count = count($_POST['chk']);
        if (!$count) {
            alert('처리할 자료를 하나 이상 선택해 주십시오.');
        }
        $delivery = $this->input->post('delivery');
        $delivery_no = $this->input->post('delivery_no');
        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            if (!isset($_POST['delivery'][$k]))
                continue;
            $param['delivery'] = $delivery[$k];
            $param['delivery_no'] = $delivery_no[$k];
            $this->query_model->updateObj('shop_order', $param, "od_no = '{$_POST['od_no'][$k]}'");
        }

        goto_url(base_url() . "admin/pop_orderform?od_id=$od_id");
    }
    public function pop_orderstatusupdate()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $member = $this->load->get_var('member');
        $chk_count = count($_POST['chk']);
        $od_id = $this->input->post('od_id');
        $pt_id = $this->input->post('pt_id');
        $od_hp = $this->input->post('od_hp');

        if (!$chk_count)
            alert('처리할 자료를 하나 이상 선택해 주십시오.');

        $od_sms_ipgum_check        = 0;
        $od_sms_baesong_check    = 0;
        $od_sms_delivered_check = 0;
        $od_sms_cancel_check    = 0;
        $od_cancel_change        = 0;

        //==============================================================================
        // 주문상태변경
        //------------------------------------------------------------------------------
        for ($i = 0; $i < $chk_count; $i++) {
            // 실제 번호를 넘김
            $k                = $_POST['chk'][$i];
            $_POST['change_status'][$k] = isset($_POST['change_status'][$k]) ? $_POST['change_status'][$k] : "";
            $od_no            = $_POST['od_no'][$k];
            $change_status  = $_POST['change_status'][$k];
            $current_status = $_POST['current_status'][$k];
            $_POST['delivery'][$k] = isset($_POST['delivery'][$k]) ? $_POST['delivery'][$k] : "";
            $_POST['delivery_no'][$k] = isset($_POST['delivery_no'][$k]) ? $_POST['delivery_no'][$k] : "";
            $delivery        = trim($_POST['delivery'][$k]);
            $delivery_no    = trim($_POST['delivery_no'][$k]);

            if ($_POST['act_button'] == '입금대기') $change_status = 1;
            if ($_POST['act_button'] == '입금완료') $change_status = 2;
            if ($_POST['act_button'] == '주문취소') $change_status = 6;
            if ($_POST['act_button'] == '전체반품') $change_status = 7;
            if ($_POST['act_button'] == '전체환불') $change_status = 9;

            switch ($change_status) {
                case '1': // 입금대기
                    if ($current_status == 2) {
                        $this->global_lib->change_order_status_1($od_no);
                    }
                    break;
                case '2': // 입금완료
                    if ($current_status == 1) {
                        $this->global_lib->change_order_status_2($od_no);
                    }
                    $od_sms_ipgum_check++;
                    break;
                case '3': // 배송준비
                    if ($current_status == 2) {
                        $this->global_lib->change_order_status_3($od_no, $delivery, $delivery_no);
                    }
                    break;
                case '4': // 배송중
                    if ($current_status == 3) {
                        $this->global_lib->change_order_status_4($od_no, $delivery, $delivery_no);
                    }
                    $od_sms_baesong_check++;
                    break;
                case '5': // 배송완료
                    if (in_array($current_status, array(3, 4))) {
                        $this->funcomplex_lib->change_order_status_5($od_no);
                    }
                    $od_sms_delivered_check++;
                    break;
                case '6': // 취소
                    if ($current_status == 1) {
                        $this->funcomplex_lib->change_order_status_6($od_no);
                        $od_sms_cancel_check++;
                        $od_cancel_change++;
                    }
                    break;
                case '7': // 반품
                    if ($current_status == 5) {
                        $this->funcomplex_lib->change_order_status_7($od_no);
                        $od_cancel_change++;
                    }
                    break;
                case '8': // 교환
                    if ($current_status == 5) {
                        $this->global_lib->change_order_status_8($od_no);
                    }
                    break;
                case '9': // 환불
                    if (in_array($current_status, array(2, 3))) {
                        $this->funcomplex_lib->change_order_status_9($od_no);
                        $od_sms_cancel_check++;
                        $od_cancel_change++;
                    }
                    break;
                case 'end': // 구매확정
                    if ($current_status == 5) {
                        $this->funcomplex_lib->change_order_status_end($od_no);
                    }
                    break;
            }
        }
        //------------------------------------------------------------------------------

        //==============================================================================
        // 문자전송
        //------------------------------------------------------------------------------
        if ($od_sms_ipgum_check)
            $this->funcomplex_lib->moonjaro_order_sms_send($pt_id, $od_hp, $od_id, 3); // 입금완료 문자

        if ($od_sms_baesong_check)
            $this->funcomplex_lib->moonjaro_order_sms_send($pt_id, $od_hp, $od_id, 4); // 배송중 문자

        if ($od_sms_delivered_check)
            $this->funcomplex_lib->moonjaro_order_sms_send($pt_id, $od_hp, $od_id, 6); // 배송완료 문자

        if ($od_sms_cancel_check)
            $this->funcomplex_lib->moonjaro_order_sms_send($pt_id, $od_hp, $od_id, 5); // 주문취소 문자
        //------------------------------------------------------------------------------

        // 상품 모두 취소일 경우 주문상태 변경
        $mod_history = '';
        $pg_res_cd = '';
        $pg_res_msg = '';
        $pg_cancel_log = '';

        if ($od_cancel_change) {
            $sql = " select COUNT(*) as od_count1,
                                SUM(IF(dan = '6' OR dan = '7' OR dan = '9', 1, 0)) as od_count2,
                                SUM(refund_price) as od_refund_price,
                                SUM(use_price) as od_receipt_price
                        from shop_order
                        where od_id = ?";
            $param = array($od_id);
            $row = $this->query_model->returnOneArr($sql, $param);

            if ($row['od_count1'] == $row['od_count2']) {
                // PG 신용카드 결제 취소일 때
                $od_receipt_price = $row['od_receipt_price'];
                if ($od_receipt_price > 0 && $row['od_refund_price'] == 0) {
                    $sql = " select * from shop_order where od_id = ?";
                    $param = array($od_id);
                    $od = $this->query_model->returnOneArr($sql, $param);
                    #3. 주문취소
                    if ($od['paymethod'] != '무통장') {
                        $imp_uid = $od['imp_uid'];
                        $merchant_uid = $od['merchant_uid'];
                        $amount = (int)$od['cancel_price'];

                        include_once('./Bill/iamport.php');
                        $result = $iamport->cancel(array(
                            'imp_uid'        => "$imp_uid",         //merchant_uid에 우선한다
                            'merchant_uid'    => "$merchant_uid",     //imp_uid 또는 merchant_uid가 지정되어야 함
                            'reason'        => "환불",                //취소사유
                        ));
                        if (!$result->success) {
                            alert($result->error['message']);
                            return false;
                        }
                    }
                }

                // 관리자 주문취소 로그
                $mod_history = $OM_TIME_YMDHIS . ' ' . $member['id'] . ' 주문취소 처리' . $pg_cancel_log . "\n";
            }
        }

        if ($mod_history) { // 주문변경 히스토리 기록
            $sql = " update shop_order
                            set od_mod_history = CONCAT(od_mod_history,'$mod_history')
                        where od_id = ?";
            $param = array($od_id);
            $this->query_model->returnNull($sql, $param);
        }

        $url = base_url() . "admin/pop_orderform?od_id=$od_id";

        // 신용카드 취소 때 오류가 있으면 알림
        if ($pg_res_cd && $pg_res_msg) {
            alert('오류코드 : ' . $pg_res_cd . ' 오류내용 : ' . $pg_res_msg, $url);
        }

        goto_url($url);
    }
    public function seller_list_update()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $chk = $this->input->post('chk');
        $count = "";
        if ($chk) {
            $count = count($_POST['chk']);
        }
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "선택승인") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $mb_id = trim($_POST['mb_id'][$k]);
                $seller_code = trim($_POST['seller_code'][$k]);
                $sql = "update shop_seller set state='1', seller_open='1' where mb_id=?";
                $sql2 = "update shop_member set supply='Y' where id=?";
                $sql3 = "update shop_goods set isopen='1' where mb_id=?";
                $param = array($mb_id);
                $param2 = array($seller_code);
                $this->query_model->returnNull($sql, $param);
                $this->query_model->returnNull($sql2, $param);
                $this->query_model->returnNull($sql3, $param2);
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            $mb_id = $this->input->post('mb_id');
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = " delete from shop_seller where mb_id = ?";
                $param = array($mb_id[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/seller?$q1&page=$page");
    }
    public function pop_sellerformupdate()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $mb_id = $this->input->post('mb_id');

        unset($value);
        $value['seller_open']          = $this->input->post('seller_open');
        $value['seller_item']          = $this->input->post('seller_item');
        $value['company_name']          = $this->input->post('company_name');
        $value['company_saupja_no']      = $this->input->post('company_saupja_no');
        $value['company_tel']          = $this->input->post('company_tel');
        $value['company_tax_email']          = $this->input->post('company_tax_email');
        $value['company_fax']          = $this->input->post('company_fax');
        $value['company_zip']          = $this->input->post('company_zip');
        $value['company_addr1']          = $this->input->post('company_addr1');
        $value['company_addr2']          = $this->input->post('company_addr2');
        $value['company_addr3']          = $this->input->post('company_addr3');
        $value['company_addr_jibeon'] = $this->input->post('company_addr_jibeon');
        $value['memo']                  = $this->input->post('memo');
        $value['company_service']      = $this->input->post('company_service');
        $value['company_item']          = $this->input->post('company_item');
        $value['company_owner']          = $this->input->post('company_owner');
        $value['company_hompage']      = $this->input->post('company_hompage');

        $value['company_brand_name']      = $this->input->post('company_brand_name');

        $company_category                 = $this->input->post('company_category');
        $company_category                 = implode(",", $company_category);
        $value['company_category']          = $company_category;
        $value['company_item_info']          = $this->input->post('company_item_info');

        $company_sales_type               = $this->input->post('company_sales_type');
        $company_sales_type               = implode(",", $company_sales_type);
        $value['company_sales_type']      = $company_sales_type;
        $value['company_entry']              = $this->input->post('company_entry');

        $value['info_name']              = $this->input->post('info_name');
        $value['info_tel']              = $this->input->post('info_tel');
        $value['info_email']          = $this->input->post('info_email');
        $value['bank_name']              = $this->input->post('bank_name');
        $value['bank_account']          = $this->input->post('bank_account');
        $value['bank_holder']          = $this->input->post('bank_holder');
        $value['state']                  = $this->input->post('state');
        $value['update_time']          = $OM_TIME_YMDHIS;
        $this->query_model->updateObj("shop_seller", $value, "mb_id='$mb_id'");

        unset($value);
        $value['isopen'] = $_POST['seller_open'];
        $this->query_model->updateObj("shop_goods", $value, " mb_id='{$_POST['seller_code']}' ");

        goto_url(base_url() . 'admin/pop_sellerform?mb_id=' . $mb_id);
    }
    public function seller_pay_delete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');

        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }
        $index_no = $this->input->post('index_no');
        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $sql = " update shop_order
                            set sellerpay_yes = '0'
                        where index_no IN ({$_POST['order_idx'][$k]})
                            and sellerpay_yes = '1' ";
            $this->query_model->returnNull($sql);

            $sql = " delete from shop_seller_cal where index_no = ?";
            $param = array($index_no[$k]);
            $this->query_model->returnNull($sql, $param);
        }

        goto_url(base_url() . "admin/seller?$q1&page=$page");
    }
    public function seller_pay_update()
    {
        $count = count($_POST['chk']);
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }
        $mb_id = $this->input->post('mb_id');
        $order_idx = $this->input->post('order_idx');
        $tot_price = $this->input->post('tot_price');
        $tot_point = $this->input->post('tot_point');
        $tot_mileage = $this->input->post('tot_mileage');
        $tot_coupon = $this->input->post('tot_coupon');
        $tot_baesong = $this->input->post('tot_baesong');
        $tot_supply = $this->input->post('tot_supply');
        $tot_seller = $this->input->post('tot_seller');
        $tot_partner = $this->input->post('tot_partner');
        $tot_admin = $this->input->post('tot_admin');
        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $sql = "insert into shop_seller_cal
                        set mb_id = ?
                            , order_idx = ?
                            , tot_price = ?
                            , tot_point = ?
                            , tot_mileage = ?
                            , tot_coupon = ?
                            , tot_baesong = ?
                            , tot_supply = ?
                            , tot_seller = ?
                            , tot_partner = ?
                            , tot_admin = ?
                            , reg_time = ?";
            $param = array(
                $mb_id[$k],
                $order_idx[$k],
                $tot_price[$k],
                $tot_point[$k],
                $tot_mileage[$k],
                $tot_coupon[$k],
                $tot_baesong[$k],
                $tot_supply[$k],
                $tot_seller[$k],
                $tot_partner[$k],
                $tot_admin[$k],
                $OM_TIME_YMDHIS
            );
            $this->query_model->returnNull($sql, $param);

            $sql = " update shop_order
                            set sellerpay_yes = '1'
                        where index_no IN ({$_POST['order_idx'][$k]})
                            and sellerpay_yes = '0' ";
            $this->query_model->returnNull($sql);
        }

        goto_url(base_url() . "admin/seller?$q1&page=$page");
    }
    public function seller_goods_review_delete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];
            $index_no = trim($_POST['index_no'][$k]);

            // 실제 번호에서 리뷰 인덱스를 검색한다
            $review_data = $this->funcomplex_lib->get_review_data($index_no, $member['id']);
            $review_od_index = $review_data['od_index'];
            // 검색한 리뷰 인덱스를 회복시킨다.
            $this->funcomplex_lib->set_order_review_flag(1, $review_od_index, $member['id']);

            // 상품평 삭제시 상품테이블에 상품평 카운터를 감소한다
            $sql = "update shop_goods set m_count=m_count-1 where index_no=?";
            $param = array($index_no);
            $this->query_model->returnNull($sql, $param);
            $sql2 = "delete from shop_goods_review where index_no=?";
            $this->query_model->returnNull($sql2, $param);
        }

        goto_url(base_url() . "admin/page?$q1&page=$page");
    }
    public function seller_odr_formupdate()
    {
        $od_id = $this->input->post('od_id');
        // 입금계좌
        if ($_POST['mod_type'] == 'receipt') {
            unset($value);
            $value['bank']            = $this->input->post('bank');
            $value['deposit_name']    = $this->input->post('deposit_name');
            $this->query_model->updateObj("shop_order", $value, "od_id='$od_id'");
        }

        // 관리자메모
        if ($_POST['mod_type'] == 'memo') {
            unset($value);
            $value['shop_memo']        = $this->input->post('shop_memo');
            $this->query_model->updateObj("shop_order", $value, "od_id='$od_id'");
        }

        // 주문자/배송지 정보
        if ($_POST['mod_type'] == 'info') {
            unset($value);
            $value['name']            = $this->input->post('name');
            $value['telephone']        = $this->input->post('telephone');
            $value['cellphone']        = $this->input->post('cellphone');
            $value['zip']            = $this->input->post('zip');
            $value['addr1']            = $this->input->post('addr1');
            $value['addr2']            = $this->input->post('addr2');
            $value['addr3']            = $this->input->post('addr3');
            $value['addr_jibeon']    = $this->input->post('addr_jibeon');
            $value['email']            = $this->input->post('email');
            $value['b_name']        = $this->input->post('b_name');
            $value['b_telephone']    = $this->input->post('b_telephone');
            $value['b_cellphone']    = $this->input->post('b_cellphone');
            $value['b_zip']            = $this->input->post('b_zip');
            $value['b_addr1']        = $this->input->post('b_addr1');
            $value['b_addr2']        = $this->input->post('b_addr2');
            $value['b_addr3']        = $this->input->post('b_addr3');
            $value['b_addr_jibeon']    = $this->input->post('b_addr_jibeon');
            $this->query_model->updateObj("shop_order", $value, "od_id='$od_id'");
        }

        goto_url(base_url() . "admin/seller_odr_form?od_id=$od_id");
    }
    #공급자 관련된 함수들 끝
    #카테고리 관련된 함수들 시작
    public function category_update()
    {
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

        $srcfile = $this->input->post('srcfile');
        $q1 = $this->input->post('q1');

        if (!is_dir($srcfile)) {
            @mkdir($srcfile, 0707);
            @chmod($srcfile, 0707);
        }
        $sel_ca1 = $this->input->post('sel_ca1');
        $sel_ca2 = $this->input->post('sel_ca2');
        $sel_ca3 = $this->input->post('sel_ca3');
        $sel_ca4 = $this->input->post('sel_ca4');
        $upcate = "";
        if ($sel_ca1) $upcate = $sel_ca1;
        if ($sel_ca2) $upcate = $sel_ca2;
        if ($sel_ca3) $upcate = $sel_ca3;
        if ($sel_ca4) $upcate = $sel_ca4;

        unset($value);
        if (isset($_FILES['headimg']['name'])) {
            if (!$this->upload->do_upload('headimg')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['headimg'] = $new_file;
            }
        }

        $new_code = $this->common_lib->get_ca_depth("shop_category", $upcate);
        $new_next = $this->global_lib->get_next_wr_num("shop_category", "caterank", " where upcate='$upcate' ");

        $value['catecode']        = $new_code;
        $value['upcate']        = $upcate;
        $value['caterank']        = $new_next;
        $value['catename']        = $_POST['catename'];
        $value['headimgurl']    = isset($_POST['headimgurl']) ?? "";
        $this->query_model->insertObj("shop_category", $value);

        goto_url(base_url() . "admin/category?$q1");
    }
    public function category_excel()
    {
        $sql_order = " order by caterank, catecode ";
        $sql = " select * from shop_category where length(catecode)='3' {$sql_order} ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");
        /** Include PHPExcel */
        include('./lib/PHPExcel.php');
        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '1차분류')
            ->setCellValue($char++ . '1', '2차분류')
            ->setCellValue($char++ . '1', '3차분류')
            ->setCellValue($char++ . '1', '4차분류')
            ->setCellValue($char++ . '1', '5차분류')
            ->setCellValue($char++ . '1', '분류코드');

        $i = 2;
        foreach ($result as $key => $row1) { // 1차 분류
            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValue($char++ . $i, $row1['catename'])
                ->setCellValue($char++ . $i, '')
                ->setCellValue($char++ . $i, '')
                ->setCellValue($char++ . $i, '')
                ->setCellValue($char++ . $i, '')
                ->setCellValue($char++ . $i, $row1['catecode']);
            $i++;

            $sql2 = " select * from shop_category where upcate=? {$sql_order} ";
            $param2 = array($row1['catecode']);
            $result2 = $this->query_model->returnArr($sql2, $param2);
            foreach ($result2 as $key => $row2) { // 2차 분류
                $char = 'A';
                $excel->setActiveSheetIndex(0)
                    ->setCellValue($char++ . $i, $row1['catename'])
                    ->setCellValue($char++ . $i, $row2['catename'])
                    ->setCellValue($char++ . $i, '')
                    ->setCellValue($char++ . $i, '')
                    ->setCellValue($char++ . $i, '')
                    ->setCellValue($char++ . $i, $row2['catecode']);
                $i++;

                $sql3 = " select * from shop_category where upcate=? {$sql_order} ";
                $param3 = array($row2['catecode']);
                $result3 = $this->query_model->returnArr($sql3, $param3);
                foreach ($result3 as $key => $row3) { // 3차 분류
                    $char = 'A';
                    $excel->setActiveSheetIndex(0)
                        ->setCellValue($char++ . $i, $row1['catename'])
                        ->setCellValue($char++ . $i, $row2['catename'])
                        ->setCellValue($char++ . $i, $row3['catename'])
                        ->setCellValue($char++ . $i, '')
                        ->setCellValue($char++ . $i, '')
                        ->setCellValue($char++ . $i, $row3['catecode']);
                    $i++;

                    $sql4 = " select * from shop_category where upcate=? {$sql_order} ";
                    $param4 = array($row3['catecode']);
                    $result4 = $this->query_model->returnArr($sql4, $param4);
                    foreach ($result4 as $key => $row4) { // 4차 분류
                        $char = 'A';
                        $excel->setActiveSheetIndex(0)
                            ->setCellValue($char++ . $i, $row1['catename'])
                            ->setCellValue($char++ . $i, $row2['catename'])
                            ->setCellValue($char++ . $i, $row3['catename'])
                            ->setCellValue($char++ . $i, $row4['catename'])
                            ->setCellValue($char++ . $i, '')
                            ->setCellValue($char++ . $i, $row4['catecode']);
                        $i++;

                        $sql5 = " select * from shop_category where upcate=? {$sql_order} ";
                        $param5 = array($row4['catecode']);
                        $result5 = $this->query_model->returnArr($sql5, $param5);
                        foreach ($result5 as $key => $row5) { // 5차 분류
                            $char = 'A';
                            $excel->setActiveSheetIndex(0)
                                ->setCellValue($char++ . $i, $row1['catename'])
                                ->setCellValue($char++ . $i, $row2['catename'])
                                ->setCellValue($char++ . $i, $row3['catename'])
                                ->setCellValue($char++ . $i, $row4['catename'])
                                ->setCellValue($char++ . $i, $row5['catename'])
                                ->setCellValue($char++ . $i, $row5['catecode']);
                            $i++;
                        }
                    }
                }
            }
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('분류');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="분류-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function category_delete()
    {
        $ca_no = trim($_POST['ca_no']);
        $q1 = $this->input->post('q1');
        if (!$ca_no) {
            alert('올바른 방법으로 이용해 주십시오.');
        }

        $srcfile = './data/category';
        $sql = "select * from shop_category where index_no=?";
        $param = array($ca_no);
        $ca = $this->query_model->returnOneArr($sql, $param);
        $len = strlen($ca['catecode']);
        if ($len > 0) {
            $sql = "select * from shop_category where left(upcate,$len)=?";
            $param = array($ca['catecode']);
            $res = $this->query_model->returnArr($sql, $param);
            foreach ($res as $key => $row) {
                if ($row['index_no']) { // 대상 하위 삭제
                    @unlink($srcfile . "/" . $row['cateimg1']);
                    @unlink($srcfile . "/" . $row['cateimg2']);
                    @unlink($srcfile . "/" . $row['headimg']);

                    $sql = "delete from shop_category where index_no=?";
                    $param = array($row['index_no']);
                    $this->query_model->returnNull($sql, $param);
                }
            }

            @unlink($srcfile . "/" . $ca['cateimg1']);
            @unlink($srcfile . "/" . $ca['cateimg2']);
            @unlink($srcfile . "/" . $ca['headimg']);
            $sql = "delete from shop_category where index_no=?";
            $param = array($ca_no);
            $this->query_model->returnNull($sql, $param);  // 삭제대상
        }

        goto_url(base_url() . "admin/category?$q1");
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
        $error = $this->input->post('error');

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
        $ca_no = $this->input->post('index_no');
        $sql = "select * from shop_category where index_no=?";
        $param = array($ca_no);
        $ca = $this->query_model->returnOneArr($sql, $param);

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
    public function category_view_update()
    {
        $W = $this->input->post('W');
        $count = 0;
        $code = "";
        switch ($W) {
            case "sel_ca1":
                $count = count($_POST['sel_ca1']);
                $code = $this->input->post('sel_ca1');
                break;
            case "sel_ca2":
                $count = count($_POST['sel_ca2']);
                $code = $this->input->post('sel_ca2');
                break;
            case "sel_ca3":
                $count = count($_POST['sel_ca3']);
                $code = $_POST['sel_ca3'];
                break;
            case "sel_ca4":
                $count = count($_POST['sel_ca4']);
                $code = $this->input->post('sel_ca4');
                break;
            case "sel_ca5":
                $count = count($_POST['sel_ca5']);
                $code = $this->input->post('sel_ca5');
                break;
        }

        if ($count) {
            for ($i = 0; $i <= $count; $i++) {
                $k = ($i + 1);

                $sql = " update shop_category set caterank = ? where catecode = ?";
                $param = array($k, $code[$i]);
                $this->query_model->returnNull($sql, $param);
            }

            $msg = "정상적으로 처리 되었습니다";
        } else {
            $msg = "처리할 항목이 없습니다";
        }

        echo "<meta charset='utf-8'>";
        echo "<script>alert('" . $msg . "');</script>";
    }
    public function menu_update()
    {
        $type = $this->input->post('type');

        if ($type == "insert") {
            $menu_name = $this->input->post('menu_name');
            if (empty($menu_name)) {
                alert("메뉴명을 입력해주세요.");
            }
            $url = $this->input->post('url');
            if (empty($url)) {
                alert("연결 URL을 입력해주세요.");
            }
            $value['menu_name'] = $menu_name;
            $value['url'] = $url;
            $value['regdate'] = date("Y-m-d H:i:s", time());

            $this->query_model->insertObj("shop_menu", $value);
        } else if ($type == "update") {
            $act_button = $this->input->post('act_button');
            $menu_id = $this->input->post('menu_id');
            $chk = $this->input->post('chk');
            $rank = $this->input->post('rank');
            $menu_name = $this->input->post('menu_name');
            $url = $this->input->post('url');

            if (empty($chk)) {
                alert($act_button . " 하실 항목을 하나 이상 체크하세요.");
            }
            $count = count($chk);

            if ($act_button == "선택삭제") {
                for ($i = 0; $i < $count; $i++) {
                    // 실제 번호를 넘김
                    $k = $chk[$i];

                    $sql = "delete from shop_menu where index_no = ?";
                    $param = array($menu_id[$k]);
                    $this->query_model->returnNull($sql, $param);
                }
            } else if ($act_button == "선택수정") {
                for ($i = 0; $i < $count; $i++) {
                    // 실제 번호를 넘김
                    $k = $chk[$i];

                    $sql = "update shop_menu set `rank` = ?, menu_name = ?, url = ? where index_no = ?";
                    $param = array($rank[$k], $menu_name[$k], $url[$k], $menu_id[$k]);
                    $this->query_model->returnNull($sql, $param);
                }
            }
        }

        goto_url(base_url() . "admin/category?code=menu_list");
    }
    #카테고리 관련된 함수들 끝
    #상품 관련된 함수들 시작
    public function goods_list_excel()
    {
        $flag = $this->input->get('flag');

        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');

        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');

        $code = $this->input->get('code');
        $sca = $this->input->get('sca');
        $stx = $this->input->get('stx');
        $sfl = $this->input->get('sfl');

        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $q_isopen = $this->input->get('q_isopen');
        $q_option = $this->input->get('q_option');
        $q_notax = $this->input->get('q_notax');
        $q_supply = $this->input->get('q_supply');
        $orderby = $this->input->get('orderby');
        $fr_stock = $this->input->get('fr_stock');
        $fr_price = $this->input->get('fr_price');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        if ($sel_ca1) $sca = $sel_ca1;
        if ($sel_ca2) $sca = $sel_ca2;
        if ($sel_ca3) $sca = $sel_ca3;
        if ($sel_ca4) $sca = $sel_ca4;
        if ($sel_ca5) $sca = $sel_ca5;

        $sql_type = "";
        if ($flag == "normal") {
            $sql_type = "and type = 'normal'";
        } else if ($flag == "timesale") {
            $sql_type = "and type = 'timesale'";
        }

        $sql_common = " from shop_goods ";
        $sql_search = " where use_aff = 0 and shop_state = 0 {$sql_type}";

        if ($code == 'stock') {
            $sql_search .= " and (stock_qty <= noti_qty and stock_mod = 1 and opt_subject = '') ";
        }

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

        $sql = " select * $sql_common $sql_search $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '업체코드')
            ->setCellValue($char++ . '1', '상품코드')
            ->setCellValue($char++ . '1', '대표분류')
            ->setCellValue($char++ . '1', '추가분류2')
            ->setCellValue($char++ . '1', '추가분류3')
            ->setCellValue($char++ . '1', '상품타입')
            ->setCellValue($char++ . '1', '상품명')
            ->setCellValue($char++ . '1', '짧은설명')
            ->setCellValue($char++ . '1', '검색키워드')
            ->setCellValue($char++ . '1', '모델명')
            ->setCellValue($char++ . '1', '브랜드')
            ->setCellValue($char++ . '1', '과세설정')
            ->setCellValue($char++ . '1', '판매가능지역')
            ->setCellValue($char++ . '1', '판매가능지역 추가설명')
            ->setCellValue($char++ . '1', '원산지')
            ->setCellValue($char++ . '1', '제조사')
            ->setCellValue($char++ . '1', '판매여부')
            ->setCellValue($char++ . '1', '공급가격')
            ->setCellValue($char++ . '1', '시중가격')
            ->setCellValue($char++ . '1', '판매가격')
            ->setCellValue($char++ . '1', '실제판매가(bid)')
            ->setCellValue($char++ . '1', '가격대체문구')
            ->setCellValue($char++ . '1', '재고적용타입')
            ->setCellValue($char++ . '1', '재고수량')
            ->setCellValue($char++ . '1', '재고통보수량')
            ->setCellValue($char++ . '1', '최소주문한도')
            ->setCellValue($char++ . '1', '최대주문한도')
            ->setCellValue($char++ . '1', '포인트 적립여부')
            ->setCellValue($char++ . '1', '판매기간 시작일')
            ->setCellValue($char++ . '1', '판매기간 종료일')
            ->setCellValue($char++ . '1', '구매가능레벨')
            ->setCellValue($char++ . '1', '가격공개')
            ->setCellValue($char++ . '1', '배송비유형')
            ->setCellValue($char++ . '1', '배송비결제')
            ->setCellValue($char++ . '1', '기본배송비')
            ->setCellValue($char++ . '1', '조건배송비')
            ->setCellValue($char++ . '1', '이미지등록방식')
            ->setCellValue($char++ . '1', '소이미지')
            ->setCellValue($char++ . '1', '중이미지1')
            ->setCellValue($char++ . '1', '중이미지2')
            ->setCellValue($char++ . '1', '중이미지3')
            ->setCellValue($char++ . '1', '중이미지4')
            ->setCellValue($char++ . '1', '중이미지5')
            ->setCellValue($char++ . '1', '상세설명')
            ->setCellValue($char++ . '1', '관리자메모');

        $i = 2;
        foreach ($result as $key => $row) {

            if (is_null_time($row['sb_date'])) $row['sb_date'] = '';
            if (is_null_time($row['eb_date'])) $row['eb_date'] = '';

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gcode'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['ca_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['ca_id2'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['ca_id3'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['type'] == "normal" ? "일반" : "bid", PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gname'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['explan'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['keywords'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['model'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['brand_nm'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['notax'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['zone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['zone_msg'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['origin'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['maker'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['isopen'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['supply_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['normal_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['goods_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['calculate_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['price_msg'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['stock_mod'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['stock_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['noti_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['odr_min'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['odr_max'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['is_point'] == "y" ? "적립" : "적립안함", PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['eb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['buy_level'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['buy_only'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_type'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_method'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_amt'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_minimum'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['simg_type'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['simg1'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg2'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg3'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg4'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg5'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg6'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['memo'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['admin_memo'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('상품');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="상품-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function goods_form_relation()
    {
        $gname = $this->input->post('gname');
        $sca = $this->input->post('sca');
        $mb_id = $this->input->post('mb_id');
        $gs_id = $this->input->post('gs_id');

        $gname = trim(strip_tags($gname));
        $sca = trim($sca);
        $mb_id = trim($mb_id);

        if (!$sca && !$gname)
            die('<p>카테고리를 선택하시거나 상품명을 입력하신 후 검색하여 주십시오.</p>');

        if (!$mb_id)
            die('<p>판매자 아이디가 값이 넘어오지 않았습니다.</p>');

        $sql_common = " from shop_goods ";
        $sql_search = " where shop_state = 0 and index_no <> '$gs_id' and (use_aff = 0 or (use_aff = 1 and mb_id = '$mb_id')) ";

        if ($sca) {
            $sql_search .= " and (ca_id like '$sca%' or ca_id2 like '$sca%' or ca_id3 like '$sca%') ";
        }

        if ($gname) {
            $sql_search .= " and (gname like '%$gname%') ";
        }

        $sql_order = " order by index_no desc ";

        $list = '';

        $sql = " select index_no, gname, simg1 $sql_common $sql_search $sql_order ";
        $result = $this->query_model->returnArr($sql);
        $i = 0;
        foreach ($result as $key => $row) {
            $sql2 = " select count(*) as cnt
                            from shop_goods_relation
                        where gs_id = ?
                            and gs_id2 = ?";
            $param = array($gs_id, $row['index_no']);
            $row2 = $this->query_model->returnOneArr($sql2, $param);
            if ($row2['cnt'])
                continue;

            $gname = $this->funcomplex_lib->get_it_image($row['index_no'], $row['simg1'], 50, 50) . ' ' . $row['gname'];

            $list .= '<li class="list_res">';
            $list .= '<input type="hidden" name="re_gs_id[]" value="' . $row['index_no'] . '">';
            $list .= '<div class="list_item">' . $gname . '</div>';
            $list .= '<div class="list_item_btn"><button type="button" class="add_item btn_small">추가</button></div>';
            $list .= '</li>';
            $i++;
        }

        if ($list)
            $list = '<ul>' . $list . '</ul>';
        else
            $list = '<p>등록된 상품이 없습니다.';

        echo $list;
    }

    // 상품 등록
    public function goods_form_update() {
        try {
            $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
            $q1 = $this->input->post('q1');
            $page = $this->input->post('page');
            $bak = $this->input->post('bak');
            $gs_id = $this->input->post('gs_id');
            $gs_list = $this->input->post('gs_list');
            $config = $this->load->get_var('config');
            $it_option_subject = '';
            $it_supply_subject = '';
            $w = $this->input->post('w');

            $type = $this->input->post('type') ?? "normal";

            $uploadPath = "./data/goods";
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

            if ($w == "u") {
                $gs = $this->global_lib->get_goods($gs_id);

                if ($gs['shop_state'] == 0 && $type == "timesale") {
                    throw new Exception("bid 상품은 최종관리자 승인 이후 수정이 불가능합니다.");
                }
            }

            if ($_POST['gname'] == "") {
                throw new Exception("상품명을 입력하세요.");
            }

            if ($_POST['ca_id'] == "") {
                throw new Exception("카테고리를 하나이상 선택하세요.");
            }

            // 관련상품을 우선 삭제함
            $sql = "DELETE FROM shop_goods_relation WHERE gs_id = ?";
            $param = array($gs_id);
            $this->query_model->returnNull($sql, $param);

            // 관련상품의 반대도 삭제
            $sql2 = "DELETE FROM shop_goods_relation WHERE gs_id2 = ?";
            $this->query_model->returnNull($sql2, $param);

            // 관련상품 등록
            $gs_id2 = explode(",", $gs_list);
            for ($i = 0; $i < count($gs_id2); $i++) {
                if (trim($gs_id2[$i])) {
                    $sql = "INSERT INTO shop_goods_relation
                                    SET gs_id  = ?,
                                        gs_id2 = ?,
                                        ir_no = ?";
                    $param = array($gs_id, $gs_id2[$i], $i);
                    $this->query_model->returnNull($sql, $param);

                    // 관련상품의 반대로도 등록
                    $sql = "INSERT INTO shop_goods_relation
                                    SET gs_id  = ?,
                                        gs_id2 = ?,
                                        ir_no = ? ";
                    $param = array($gs_id2[$i], $gs_id, $i);
                    $this->query_model->returnNull($sql, $param);
                }
            }

            // 기존 선택옵션삭제
            $sql = "DELETE FROM shop_goods_option WHERE io_type = '0' AND gs_id = ?";
            $param = array($gs_id);
            $this->query_model->returnNull($sql, $param);
            $opt_id = $this->input->post('opt_id');
            $option_count = ($opt_id != "") ? count($opt_id) : 0;
            if ($option_count) {
                // 옵션명
                $opt1_cnt = $opt2_cnt = $opt3_cnt = 0;
                for ($i = 0; $i < $option_count; $i++) {
                    $opt_val = explode(' ', $_POST['opt_id'][$i]);
                    if (isset($opt_val[0])) {
                        $opt1_subject = $this->input->post('opt1_subject');
                        $opt1_cnt++;
                    }
                    if (isset($opt_val[1])) {
                        $opt2_subject = $this->input->post('opt2_subject');
                        $opt2_cnt++;
                    }
                    if (isset($opt_val[2])) {
                        $opt3_subject = $this->input->post('opt3_subject');
                        $opt3_cnt++;
                    }
                }

                if (isset($opt1_subject) && $opt1_cnt > 0) {
                    $it_option_subject = $opt1_subject;
                    if (isset($opt2_subject) && $opt2_cnt > 0)
                        $it_option_subject .= ',' . $opt2_subject;
                    if (isset($opt3_subject) && $opt3_cnt > 0)
                        $it_option_subject .= ',' . $opt3_subject;
                }
            }

            // 기존 추가옵션삭제
            $sql = "DELETE FROM shop_goods_option WHERE io_type = '1' AND gs_id = ?";
            $param = array($gs_id);
            $this->query_model->returnNull($sql, $param);
            $spl_id = $this->input->post('spl_id');
            $supply_count =  ($spl_id != "") ? count($spl_id) : 0;
            if ($supply_count) {
                // 추가옵션명
                $arr_spl = array();
                for ($i = 0; $i < $supply_count; $i++) {
                    $spl_val = explode(' ', $_POST['spl_id'][$i]);
                    if (!in_array($spl_val[0], $arr_spl))
                        $arr_spl[] = $spl_val[0];
                }

                $it_supply_subject = implode(',', $arr_spl);
            }

            // 상품 정보제공
            $value_array = array();
            $ii_article = $this->input->post('ii_article');
            if ($ii_article) {
                for ($i = 0; $i < count($_POST['ii_article']); $i++) {
                    $key = $_POST['ii_article'][$i];
                    $val = $_POST['ii_value'][$i];
                    $value_array[$key] = $val;
                }
            }
            $it_info_value = addslashes(serialize($value_array));
            
            unset($value);
            if ($_POST['simg_type']) { // URL 입력
                if($w == "") {
                    if (empty($_POST['simg1']) || empty($_POST['simg2'])) {
                        throw new Exception("상품 이미지1, 이미지2를 등록해주세요.");
                    }
                }

                $value['simg1'] = $_POST['simg1'];
                $value['simg2'] = $_POST['simg2'];
                $value['simg3'] = $_POST['simg3'];
                $value['simg4'] = $_POST['simg4'];
                $value['simg5'] = $_POST['simg5'];
                $value['simg6'] = $_POST['simg6'];
            } else {
                if($w == "") {
                    if (empty($_FILES['simg1']['name']) || empty($_FILES['simg2']['name'])) {
                        throw new Exception("상품 이미지1, 이미지2를 등록해주세요.");
                    }
                }
            
                for ($i = 1; $i <= 6; $i++) {
                    if ($img = $_FILES['simg' . $i]['name']) {
                        if (!preg_match("/\.(gif|jpg|png)$/i", $img)) {
                            throw new Exception("이미지 {$i}는 gif, jpg, png 파일이 아닙니다.");
                        }
                    }
            
                    if (isset($_POST['simg' . $i . '_del'])) {
                        unlink($uploadPath . '/' . $_POST['simg' . $i . '_del']);
                        $value['simg' . $i] = '';
                    }
            
                    if (!empty($_FILES['simg' . $i]['name'])) {
                        if (!$this->upload->do_upload('simg' . $i)) {
                            $error = $this->upload->display_errors();
                            // 업로드 실패 시 simg 값 설정하지 않음
                        } else {
                            $uploadFile = $this->upload->data();
                            $new_file = $uploadFile['file_name'];
                            $value['simg' . $i] = $new_file;
                        }
                    }
                }
            }            

            $ppay_type = $this->input->post('ppay_type');
            $ca_id = $this->input->post('ca_id');
            $ca_id2 = $this->input->post('ca_id2');
            $ca_id3 = $this->input->post('ca_id3');
            $mb_id = $this->input->post('mb_id');
            $mb_name = $this->input->post('mb_name');
            $gname = $this->input->post('gname');
            $isopen = $this->input->post('isopen');
            $explan = $this->input->post('explan');
            $keywords = $this->input->post('keywords');
            $admin_memo = $this->input->post('admin_memo');
            $memo = $this->input->post('memo');
            $maker = $this->input->post('maker');
            $origin = $this->input->post('origin');
            $model = $this->input->post('model');
            $brand_uid = $this->input->post('brand_uid');
            $notax = $this->input->post('notax');
            $zone = $this->input->post('zone');
            $zone_msg = $this->input->post('zone_msg');
            $delivery_period = $this->input->post('delivery_period');
            $sc_type = $this->input->post('sc_type');
            $sc_method = $this->input->post('sc_method');
            $info_gubun = $this->input->post('info_gubun');
            $buy_level = $this->input->post('buy_level');
            $simg_type = $this->input->post('simg_type');

            // 수입필증 / 계약서 시작 --------
            $uploadPath = "./data/document/";
            $configArr = array();
            $configArr['upload_path'] = $uploadPath;

            $configArr['allowed_types'] = 'jpg|jpeg|png|pdf';
            $configArr['max_size'] = $this->max_size;
            $configArr['max_width'] = $this->max_width;
            $configArr['max_height'] = $this->max_height;
            $configArr['remove_spaces'] = $this->remove_spaces;
            $configArr['encrypt_name'] = $this->encrypt_name;

            $this->load->library('upload', $configArr);
            $this->upload->initialize($configArr);

            // 파일이 있으면 파일을 업로드함
            $uploadFile = array('document' => '');
            $error = "";

            if (isset($_FILES['document']['name']) && $_FILES['document']['name'] != "") {
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                } else {
                    $uploadFile['document'] = $this->upload->data();
                    $document = $uploadFile['document']['file_name'];
                    $value['document'] = $document;
                }
            }

            if ($error != "") {
                throw new Exception($error);
            }
            // 수입필증 / 계약서 끝 --------

            $normal_price = conv_number($this->input->post('normal_price'));
            if ($type == "timesale" && empty($normal_price)) {
                throw new Exception("정가를 입력해주세요");
            }

            $goods_price = conv_number($this->input->post('goods_price'));
            if (empty($goods_price)) {
                throw new Exception("판매가격을 입력해주세요");
            }

            $calculate_price = conv_number($this->input->post('calculate_price'));
            if ($type == "timesale" && empty($calculate_price)) {
                throw new Exception("실제 판매가를 입력해주세요");
            }

            if ($type == "timesale" && $goods_price > $normal_price) {
                throw new Exception("판매가격은 정가보다 작아야합니다.");
            }

            $supply_price = conv_number($this->input->post('supply_price'));
            if ($type == "normal" && $supply_price > $goods_price) {
                throw new Exception("판매가는 공급 가격보다 높아야 합니다.");
            }

            $stock_qty = conv_number($this->input->post('stock_qty'));
            if ($type == "timesale" && empty($stock_qty)) {
                throw new Exception("수량을 입력해주세요");
            }

            //할인율 검증
            $this->validate_min_discount($goods_price, $calculate_price, $stock_qty);

            if ($type == "timesale" && $stock_qty < 2) {
                throw new Exception("수량은 2개 이상이어야 합니다.");
            }

            if ($type == "timesale" && $stock_qty % 2 != 0) {
                throw new Exception("수량은 짝수로 입력해주세요.");
            }

            if ($calculate_price <= 10000 && $stock_qty > 30) {
                throw new Exception("저가 상품은 수량 30개 이내로만 등록 가능합니다. 현재 수량: {$stock_qty}");
            }

            $goods_total_price = conv_number($this->input->post('goods_total_price')) ?? "0";
            if ($type == "timesale" && empty($goods_total_price)) {
                throw new Exception("총 판매금액을 입력해주세요");
            }

            $sb_date = $this->input->post('sb_date');
            $eb_date = $this->input->post('eb_date');
            if ($type == "timesale" && (empty($sb_date) || empty($eb_date))) {
                throw new Exception("판매기간을 설정해주세요.");
            }

            if(empty($memo)) {
                throw new Exception("상품 상세 설명을 입력해주세요.");
            }

            $ec_mall_pid = $this->input->post('ec_mall_pid');
            $value['type']              = isset($type) ? $type : "normal"; //상품 타입(일반, bid)
            $value['ca_id']             = isset($ca_id) ? $ca_id : ""; //대표카테고리
            $value['ca_id2']            = isset($ca_id2) ? $ca_id2 : ""; //추가카테고리2
            $value['ca_id3']            = isset($ca_id3) ? $ca_id3 : ""; //추가카테고리3
            $value['mb_id']             = isset($mb_id) ? $mb_id : ""; //업체코드
            $value['mb_name']           = isset($mb_name) ? $mb_name : ""; //업체명
            $value['gname']             = isset($gname) ? $gname : ""; //상품명
            $value['isopen']            = isset($isopen) ? $isopen : "1"; //진열상태
            $value['explan']            = isset($explan) ? $explan : "1";  //짧은설명
            $value['keywords']          = isset($keywords) ? $keywords : "";  //키워드
            $value['admin_memo']        = isset($admin_memo) ? $admin_memo : ""; //관리자메모
            $value['memo']              = isset($memo) ? $memo : "";  //상품설명
            $value['goods_price']       = $goods_price; //판매가격
            $value['supply_price']      = $supply_price; //공급가격
            $value['normal_price']      = $normal_price; //시중가격
            $value['calculate_price']   = $calculate_price; //실제 판매가(타임세일)
            $value['stock_qty']         = $stock_qty; //재고수량
            $value['original_stock_qty']= $stock_qty; //재고수량
            $value['goods_total_price'] = $goods_total_price;
            $value['gpoint']            = 0;
            $value['maker']             = isset($maker) ? $maker : ""; //제조사
            $value['origin']            = isset($origin) ? $origin : "";  //원산지
            $value['model']             = isset($model) ? $model : "";   //모델명
            $value['opt_subject']       = $it_option_subject; //상품 선택옵션
            $value['spl_subject']       = $it_supply_subject; //상품 추가옵션
            $value['ppay_type']         = isset($ppay_type) ?: ""; //수수료적용타입
            $ppay_rate                  = $this->input->post('ppay_rate');
            $value['ppay_rate']         = ($ppay_rate != null) ? $ppay_rate : 0; //수수료구분
            $value['ppay_fee']          = is_array($this->input->post('ppay_fee')) ? implode(' ', $this->input->post('ppay_fee')) : '';
            $ppay_dan                   = $this->input->post('ppay_dan');
            $value['ppay_dan']          = ($ppay_dan != null) ? $ppay_dan : 0;
            $value['noti_qty']          = conv_number($this->input->post('noti_qty')); //재고 통보수량
            $value['brand_uid']         = $this->input->post('brand_uid'); //브랜드주키
            $value['brand_nm']          = $this->global_lib->get_brand($this->input->post('brand_uid')); //브랜드명
            $value['notax']             = isset($notax) ? $notax : "0"; //과세구분
            $value['zone']              = isset($zone) ? $zone : ""; //판매가능지역
            $value['zone_msg']          = isset($zone_msg) ? $zone_msg : ""; //판매가능지역 추가설명
            $value['delivery_period']   = isset($delivery_period) ? $delivery_period : ""; //판매가능지역 추가설명
            $value['sc_type']           = isset($sc_type) ? $sc_type : "0"; //배송비 유형	0:공통설정, 1:무료, 2:조건부 무료, 3:유료
            $value['sc_method']         = isset($sc_method) ? $sc_method : "0"; //배송비 결제	0:선불, 1:착불, 2:사용자선택
            $value['sc_amt']            = conv_number($this->input->post('sc_amt')); //기본 배송비
            $value['sc_minimum']        = conv_number($this->input->post('sc_minimum'));    //조건 배송비
            $sc_each_use                = $this->input->post('sc_each_use');
            $value['sc_each_use']       = ($sc_each_use != null) ? $sc_each_use : 0;  //묶음배송불가
            $value['info_gubun']        = isset($info_gubun) ? $info_gubun : ""; //상품정보제공 구분
            $value['info_value']        = $it_info_value; //상품정보제공 값
            $value['price_msg']         = $this->input->post('price_msg') ?? ""; //가격 대체문구
            $value['stock_mod']         = ($type == "timesale") ? 1 : $this->input->post('stock_mod'); //수량형식
            $value['odr_min']           = ($type == "timesale") ? 1 : conv_number($this->input->post('odr_min')); //최소 주문한도
            $value['odr_max']           = ($type == "timesale") ? 1 : conv_number($this->input->post('odr_max')); //최대 주문한도
            $value['buy_level']         = isset($buy_level) ? $buy_level : "10"; //구매가능 레벨
            $buy_only                   = $this->input->post('buy_only');
            $value['buy_only']          = ($buy_only != null) ? $buy_only : 0; //가격공개 여부
            $value['simg_type']         = $this->input->post('simg_type'); //이미지 등록방식
            $value['sb_date']           = $sb_date; //판매 시작일
            $value['eb_date']           = $eb_date; //판매 종료일
            $value['ec_mall_pid']       = $this->input->post('ec_mall_pid'); //네이버쇼핑 상품ID
            $value['update_time']       = $OM_TIME_YMDHIS; //수정일시
            $value['is_point']          = $this->input->post('is_point'); // 포인트 적립여부

            if ($w == "") {
                $value['shop_state'] = $config['seller_reg_auto']; //자동승인여부
                $value['gcode'] = $_POST['gcode']; //상품코드
                $value['reg_time'] = $OM_TIME_YMDHIS; //등록일시
                $gs_id = $this->query_model->insertObj("shop_goods", $value);
            } else if ($w == "u") {
                $this->query_model->updateObj("shop_goods", $value, "index_no = '$gs_id'");
            }
            // 선택옵션등록
            if ($option_count) {
                $comma = '';
                $opt_id = $this->input->post('opt_id');
                $opt_price = $this->input->post('opt_price');
                $opt_stock_qty = $this->input->post('opt_stock_qty');
                $opt_noti_qty = $this->input->post('opt_noti_qty');
                $opt_use = $this->input->post('opt_use');

                for ($i = 0; $i < $option_count; $i++) {
                    if ((int)$opt_price[$i] < 0 && abs((int)$opt_price[$i]) > $goods_price) {
                        throw new Exception("옵션 {$opt_id[$i]}의 가격 할인폭이 상품 가격보다 큽니다.");
                    }
                }

                $sql = " insert into shop_goods_option
                                    ( `io_id`, `io_type`, `gs_id`, `io_price`, `io_stock_qty`, `io_noti_qty`, `io_use` )
                                VALUES ";

                for ($i = 0; $i < $option_count; $i++) {
                    $sql .= $comma . " ( '{$opt_id[$i]}', '0', '$gs_id', '{$opt_price[$i]}', '{$opt_stock_qty[$i]}', '{$opt_noti_qty[$i]}', '{$opt_use[$i]}' )";
                    $comma = ' , ';
                }

                $this->query_model->returnNull($sql);
            }

            // 추가옵션등록
            if ($supply_count) {
                $comma = '';
                $spl_id = $this->input->post('spl_id');
                $spl_price = $this->input->post('spl_price');
                $spl_stock_qty = $this->input->post('spl_stock_qty');
                $spl_noti_qty = $this->input->post('spl_noti_qty');
                $spl_use = $this->input->post('spl_use');

                for ($i = 0; $i < $supply_count; $i++) {
                    if ((int)$spl_price[$i] < 0 && abs((int)$spl_price[$i]) > $goods_price) {
                        throw new Exception("옵션 {$spl_id[$i]}의 가격 할인폭이 상품 가격보다 큽니다.");
                    }
                }
                
                $sql = " insert into shop_goods_option
                                    ( `io_id`, `io_type`, `gs_id`, `io_price`, `io_stock_qty`, `io_noti_qty`, `io_use` )
                                VALUES ";

                for ($i = 0; $i < $supply_count; $i++) {
                    $sql .= $comma . " ( '{$spl_id[$i]}', '1', '$gs_id', '{$spl_price[$i]}', '{$spl_stock_qty[$i]}', '{$spl_noti_qty[$i]}', '{$spl_use[$i]}' )";
                    $comma = ' , ';
                }
                $this->query_model->returnNull($sql);
            }

            $txt = ($w == "") ? "등록" : "수정";
            $return['res'] = true;
            $return['msg'] = "상품이 {$txt}되었습니다.";
            if ($w == "") {
                $return['url'] = "admin/goods?code=form&w=u&gs_id=$gs_id&type=$type";
            } else {
                $return['url'] = "admin/goods?code=form&w=u&gs_id=$gs_id$q1&page=$page&bak=$bak&type=$type";
            }
        } catch (Exception $e) {
            $return['res'] = false;
            $return['msg'] = $e->getMessage();
        }

        echo json_encode($return);
    }

    private function validate_min_discount($normal_price, $goods_price, $stock_qty) {
        $discount_ratio = ($normal_price - $goods_price) / $normal_price;

        if ($normal_price <= 10000) {
            $base_discount = 0.07;
            $qty_factor = 0.0012;
        } elseif ($normal_price <= 30000) {
            $base_discount = 0.10;
            $qty_factor = 0.0010;
        } elseif ($normal_price <= 100000) {
            $base_discount = 0.13;
            $qty_factor = 0.0009;
        } else {
            $base_discount = 0.15;
            $qty_factor = 0.0008;
        }

        $min_discount = min($base_discount + ($stock_qty * $qty_factor), 0.25);
        $max_goods_price = round(($normal_price * (1 - $min_discount)) / 10) * 10;

        if ($discount_ratio < $min_discount) {
            throw new Exception(
                "온라인가({$normal_price})와 딜가({$goods_price})의 차이가 너무 적습니다. " .
                "재고 {$stock_qty}개 기준으로 최소 " . ($min_discount * 100) . "% 이상의 할인율이 필요하며, " .
                "딜가는 최소 {$max_goods_price}원 이하로 설정되어야 합니다."
            );
        }
    }
    
    public function insert_timesale_goods($gs_id, $normal_price, $goods_price, $stock_qty) {
        $this->validate_min_discount($normal_price, $goods_price, $stock_qty);
    
        $online_price = $normal_price;
        $deal_price = $goods_price;
        $Q = $stock_qty;
        $round_unit = 10;
        $steps = 3;
    
        $min_price = $this->get_valid_min_price($deal_price, $online_price, $Q, $round_unit);
        $result = $this->bid_distribution($online_price, $deal_price, $Q, $min_price, $round_unit, $steps);
    
        if (empty($result['prices_arr'])) {
            alert('데이터 저장 중 오류가 발생하였습니다.');
        }
    
        $real_prices = array_map(function($row) use ($gs_id, $normal_price, $goods_price) {
            return [
                'gs_id' => $gs_id,
                'normal_price' => $normal_price,
                'goods_price' => $goods_price,
                'real_price' => $row['price'],
                'difference' => $normal_price - $row['price'],
                'percentage' => $row['percent'],
                'mb_id' => "",
                'regdate' => date("Y-m-d H:i:s")
            ];
        }, $result['prices_arr']);
    
        $this->db->insert_batch('shop_goods_timesale', $real_prices);
        return true;
    }
    
    private function get_valid_min_price($deal_price, $normal_price, $stock_qty, $round_unit = 10) {
        if ($deal_price <= 10000) {
            $base_discount = 0.07;
            $qty_factor = 0.0012;
        } elseif ($deal_price <= 30000) {
            $base_discount = 0.10;
            $qty_factor = 0.0010;
        } elseif ($deal_price <= 100000) {
            $base_discount = 0.13;
            $qty_factor = 0.0009;
        } else {
            $base_discount = 0.15;
            $qty_factor = 0.0008;
        }
    
        $discount_rate = min($base_discount + ($stock_qty * $qty_factor), 0.25);
        $min_price = floor($deal_price * (1 - $discount_rate));
    
        $price_range = $normal_price - $min_price;
        $available_steps = floor($price_range / $round_unit);
    
        if ($available_steps < $stock_qty) {
            $required_range = $stock_qty * $round_unit;
    
            throw new Exception(
                "중복 없는 가격 분포가 불가능합니다.\n" .
                "수량 {$stock_qty}개를 {$round_unit}원 단위로 분포하려면 최소 {$required_range}원의 가격폭이 필요합니다.\n" .
                "현재 가격폭은 {$price_range}원입니다. min_price를 더 낮추거나 수량을 줄이세요.\n" .
                "(현재 min_price 계산 기준 할인율: " . round($discount_rate * 100, 1) . "%)"
            );
        }
    
        return $min_price;
    }

    private function has_duplicates($array) {
        return count($array) !== count(array_unique($array));
    }

    private function bid_distribution($online_price, $deal_price, $Q, $min_price, $round_unit, $steps) {
        $find_min_max_k = function($online_price, $deal_price, $Q, $min_price, $round_unit, $steps, $epsilon = 1) {
            $k_min = 1;
            $k_max = 10.0;
            $valid_k_min = null;
            $valid_k_max = null;
    
            for ($k = $k_min; $k <= $k_max; $k += 0.1) {
                $result = $this->bid_distribution_with_k($online_price, $deal_price, $Q, $min_price, $round_unit, $steps, $k);
                if (abs($result["diff"]) < $epsilon && !$this->has_duplicates($result["prices"])) {
                    $valid_k_min = $k;
                    break;
                }
            }
    
            for ($k = $k_max; $k >= $k_min; $k -= 0.1) {
                $result = $this->bid_distribution_with_k($online_price, $deal_price, $Q, $min_price, $round_unit, $steps, $k);
                if (abs($result["diff"]) < $epsilon && !$this->has_duplicates($result["prices"])) {
                    $valid_k_max = $k;
                    break;
                }
            }
    
            return [$valid_k_min, $valid_k_max];
        };
    
        $find_optimal_k = function($k_min, $k_max, $online_price, $deal_price, $Q, $min_price, $round_unit, $steps, $epsilon = 1) {
            $best_k = $k_min;
            $best_diff = PHP_INT_MAX;
    
            while ($k_min <= $k_max) {
                $k_mid = ($k_min + $k_max) / 2;
                $result = $this->bid_distribution_with_k($online_price, $deal_price, $Q, $min_price, $round_unit, $steps, $k_mid);
                $diff = abs($result["diff"]);
    
                if ($diff < $epsilon && !$this->has_duplicates($result["prices"])) {
                    return $k_mid;
                }
    
                if ($result["diff"] > 0) {
                    $k_min = $k_mid + 0.01;
                } else {
                    $k_max = $k_mid - 0.01;
                }
    
                if ($diff < $best_diff && !$this->has_duplicates($result["prices"])) {
                    $best_diff = $diff;
                    $best_k = $k_mid;
                }
            }
    
            return $best_k;
        };
    
        list($k_min, $k_max) = $find_min_max_k($online_price, $deal_price, $Q, $min_price, $round_unit, $steps);
        if ($k_min === null || $k_max === null) {
            throw new Exception("적절한 k 값을 찾을 수 없습니다. 온라인 최저가와 실제 판매가의 차이가 너무 적습니다.");
        }
    
        $optimal_k = $find_optimal_k($k_min, $k_max, $online_price, $deal_price, $Q, $min_price, $round_unit, $steps);
        return $this->bid_distribution_with_k($online_price, $deal_price, $Q, $min_price, $round_unit, $steps, $optimal_k);
    }
    
    private function bid_distribution_with_k($online_price, $deal_price, $Q, $min_price, $round_unit, $steps, $k) {
        $T = $deal_price * $Q;
        $prices = [];
    
        if ($Q <= 1) {
            return ["prices" => [$T], "final_sum" => $T, "diff" => 0];
        }
    
        for ($i = 1; $i <= $Q; $i++) {
            $u = 0.1 + (0.9 - 0.1) * ($i - 1) / ($Q - 1);
            $s = 1.0 / (1.0 + exp(-$k * ($u - 0.5)));
            $p = $min_price + $s * ($online_price - $min_price);
            $prices[] = min($online_price, $p);
        }
    
        for ($step = 0; $step <= $steps; $step++) {
            $sum_now = array_sum($prices);
            $scale = $sum_now > 0 ? $T / $sum_now : 1;
            foreach ($prices as &$price) {
                $price = min($online_price, $price * $scale);
            }
        }
    
        foreach ($prices as &$price) {
            $price = min($online_price, round($price / $round_unit) * $round_unit);
        }
    
        $final_sum = array_sum($prices);
        $diff = $T - $final_sum;
        if (abs($diff) > 0) {
            $last_index = count($prices) - 1;
            $prices[$last_index] += $diff;
            $prices[$last_index] = min($online_price, round($prices[$last_index] / $round_unit) * $round_unit);
        }
    
        $prices_arr = array_map(function($price) use ($online_price) {
            return [
                'price' => $price,
                'percent' => round($price / $online_price, 2)
            ];
        }, $prices);
    
        return [
            'prices_arr' => $prices_arr,
            'prices' => $prices,
            'final_sum' => array_sum($prices),
            'diff' => $T - array_sum($prices)
        ];
    }    

    public function goods_form_auto()
    {
        $index = $this->input->post('index');
        $no = $this->input->post('no');
        $gs = $this->global_lib->get_goods($index, 'ppay_fee');
        $formAuto = "";
        for ($i = 0; $i < (int)$no; $i++) {
            $arr = explode(' ', trim($gs['ppay_fee']));
            if ($arr[$i] == "")
                $ppay = 0;
            else
                $ppay = trim($arr[$i]);
            $formAuto .= "<input type=\"text\" name=\"ppay_fee[]\" value=\"{$ppay}\" class=\"frm_input w80\">";
        }
        echo $formAuto;
    }
    public function ajax_category_select_json()
    {
        $mod_type = $this->input->post('mod_type');
        $ca_id = $this->input->post('ca_id');
        if (!$mod_type) die('Error');
        switch ($mod_type):
            case "2":
                $len = 6;
                break;
            case "3":
                $len = 9;
                break;
            case "4":
                $len = 12;
                break;
            case "5":
                $len = 15;
                break;
        endswitch;

        $data = array();

        if ($ca_id) {
            $sql = " select catecode, catename
                        from shop_category
                        where length(catecode) = '$len'
                            and catecode like '{$ca_id}%'
                        order by caterank, catecode ";
            $result = $this->query_model->returnArr($sql);
            $i = 0;
            foreach ($result as $key => $row) {
                $data[$i]['optionValue'] = $row['catecode'];
                $data[$i]['optionText']  = $row['catename'];
                $i++;
            }
        }

        die(json_encode($data));
    }
    public function goods_supply_excel()
    {
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $sca = $this->input->get('sca');
        $stx = $this->input->get('stx');
        $sfl = $this->input->get('sfl');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $fr_stock = $this->input->get('fr_stock');
        $to_stock = $this->input->get('to_stock');
        $fr_price = $this->input->get('fr_price');
        $to_price = $this->input->get('to_price');
        $q_isopen = $this->input->get('q_isopen');
        $q_notax = $this->input->get('q_notax');
        $q_option = $this->input->get('q_option');
        $q_supply = $this->input->get('q_supply');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        if ($sel_ca1) $sca = $sel_ca1;
        if ($sel_ca2) $sca = $sel_ca2;
        if ($sel_ca3) $sca = $sel_ca3;
        if ($sel_ca4) $sca = $sel_ca4;
        if ($sel_ca5) $sca = $sel_ca5;

        $sql_common = " from shop_goods ";
        $sql_search = " where left(mb_id,3)='AP-' and shop_state = '1' ";

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

        $sql = " select * $sql_common $sql_search $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '판매자ID')
            ->setCellValue($char++ . '1', '상품코드')
            ->setCellValue($char++ . '1', '대표분류')
            ->setCellValue($char++ . '1', '추가분류2')
            ->setCellValue($char++ . '1', '추가분류3')
            ->setCellValue($char++ . '1', '상품타입')
            ->setCellValue($char++ . '1', '상품명')
            ->setCellValue($char++ . '1', '짧은설명')
            ->setCellValue($char++ . '1', '검색키워드')
            ->setCellValue($char++ . '1', '모델명')
            ->setCellValue($char++ . '1', '브랜드')
            ->setCellValue($char++ . '1', '과세설정')
            ->setCellValue($char++ . '1', '판매가능지역')
            ->setCellValue($char++ . '1', '판매가능지역 추가설명')
            ->setCellValue($char++ . '1', '원산지')
            ->setCellValue($char++ . '1', '제조사')
            ->setCellValue($char++ . '1', '판매여부')
            ->setCellValue($char++ . '1', '공급가격')
            ->setCellValue($char++ . '1', '시중가격')
            ->setCellValue($char++ . '1', '판매가격')
            ->setCellValue($char++ . '1', '실제판매가(bid)')
            ->setCellValue($char++ . '1', '가격대체문구')
            ->setCellValue($char++ . '1', '재고적용타입')
            ->setCellValue($char++ . '1', '재고수량')
            ->setCellValue($char++ . '1', '재고통보수량')
            ->setCellValue($char++ . '1', '최소주문한도')
            ->setCellValue($char++ . '1', '최대주문한도')
            ->setCellValue($char++ . '1', '포인트 적립여부')
            ->setCellValue($char++ . '1', '판매기간 시작일')
            ->setCellValue($char++ . '1', '판매기간 종료일')
            ->setCellValue($char++ . '1', '구매가능레벨')
            ->setCellValue($char++ . '1', '가격공개')
            ->setCellValue($char++ . '1', '배송비유형')
            ->setCellValue($char++ . '1', '배송비결제')
            ->setCellValue($char++ . '1', '기본배송비')
            ->setCellValue($char++ . '1', '조건배송비')
            ->setCellValue($char++ . '1', '이미지등록방식')
            ->setCellValue($char++ . '1', '소이미지')
            ->setCellValue($char++ . '1', '중이미지1')
            ->setCellValue($char++ . '1', '중이미지2')
            ->setCellValue($char++ . '1', '중이미지3')
            ->setCellValue($char++ . '1', '중이미지4')
            ->setCellValue($char++ . '1', '중이미지5')
            ->setCellValue($char++ . '1', '상세설명')
            ->setCellValue($char++ . '1', '관리자메모');
        $i = 2;
        foreach ($result as $key => $row) {
            if (is_null_time($row['sb_date'])) $row['sb_date'] = '';
            if (is_null_time($row['eb_date'])) $row['eb_date'] = '';

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gcode'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['ca_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['ca_id2'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['ca_id3'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['type'] == "normal" ? "일반" : "bid", PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gname'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['explan'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['keywords'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['model'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['brand_nm'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['notax'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['zone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['zone_msg'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['origin'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['maker'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['isopen'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['supply_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['normal_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['goods_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['calculate_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['price_msg'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['stock_mod'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['stock_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['noti_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['odr_min'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['odr_max'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['is_point'] == "y" ? "적립" : "적립안함", PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['gpoint'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['eb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['buy_level'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['buy_only'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_type'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_method'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_amt'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_minimum'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['simg_type'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['simg1'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg2'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg3'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg4'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg5'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg6'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['memo'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['admin_memo'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('공급업체 대기상품');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="공급업체 대기상품-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function goods_supply2_excel()
    {
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $sca = $this->input->get('sca');
        $stx = $this->input->get('stx');
        $sfl = $this->input->get('sfl');
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $fr_stock = $this->input->get('fr_stock');
        $to_stock = $this->input->get('to_stock');
        $fr_price = $this->input->get('fr_price');
        $to_price = $this->input->get('to_price');
        $q_isopen = $this->input->get('q_isopen');
        $q_notax = $this->input->get('q_notax');
        $q_option = $this->input->get('q_option');
        $q_supply = $this->input->get('q_supply');
        $orderby = $this->input->get('orderby');
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        if ($sel_ca1) $sca = $sel_ca1;
        if ($sel_ca2) $sca = $sel_ca2;
        if ($sel_ca3) $sca = $sel_ca3;
        if ($sel_ca4) $sca = $sel_ca4;
        if ($sel_ca5) $sca = $sel_ca5;

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

        $sql = " select * $sql_common $sql_search $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '판매자ID')
            ->setCellValue($char++ . '1', '상품코드')
            ->setCellValue($char++ . '1', '대표분류')
            ->setCellValue($char++ . '1', '추가분류2')
            ->setCellValue($char++ . '1', '추가분류3')
            ->setCellValue($char++ . '1', '상품타입')
            ->setCellValue($char++ . '1', '상품명')
            ->setCellValue($char++ . '1', '짧은설명')
            ->setCellValue($char++ . '1', '검색키워드')
            ->setCellValue($char++ . '1', '모델명')
            ->setCellValue($char++ . '1', '브랜드')
            ->setCellValue($char++ . '1', '과세설정')
            ->setCellValue($char++ . '1', '판매가능지역')
            ->setCellValue($char++ . '1', '판매가능지역 추가설명')
            ->setCellValue($char++ . '1', '원산지')
            ->setCellValue($char++ . '1', '제조사')
            ->setCellValue($char++ . '1', '판매여부')
            ->setCellValue($char++ . '1', '공급가격')
            ->setCellValue($char++ . '1', '시중가격')
            ->setCellValue($char++ . '1', '판매가격')
            ->setCellValue($char++ . '1', '실제판매가(bid)')
            ->setCellValue($char++ . '1', '가격대체문구')
            ->setCellValue($char++ . '1', '재고적용타입')
            ->setCellValue($char++ . '1', '재고수량')
            ->setCellValue($char++ . '1', '재고통보수량')
            ->setCellValue($char++ . '1', '최소주문한도')
            ->setCellValue($char++ . '1', '최대주문한도')
            ->setCellValue($char++ . '1', '포인트 적립여부')
            ->setCellValue($char++ . '1', '판매기간 시작일')
            ->setCellValue($char++ . '1', '판매기간 종료일')
            ->setCellValue($char++ . '1', '구매가능레벨')
            ->setCellValue($char++ . '1', '가격공개')
            ->setCellValue($char++ . '1', '배송비유형')
            ->setCellValue($char++ . '1', '배송비결제')
            ->setCellValue($char++ . '1', '기본배송비')
            ->setCellValue($char++ . '1', '조건배송비')
            ->setCellValue($char++ . '1', '이미지등록방식')
            ->setCellValue($char++ . '1', '소이미지')
            ->setCellValue($char++ . '1', '중이미지1')
            ->setCellValue($char++ . '1', '중이미지2')
            ->setCellValue($char++ . '1', '중이미지3')
            ->setCellValue($char++ . '1', '중이미지4')
            ->setCellValue($char++ . '1', '중이미지5')
            ->setCellValue($char++ . '1', '상세설명')
            ->setCellValue($char++ . '1', '관리자메모');

        $i = 2;
        foreach ($result as $key => $row) {
            if (is_null_time($row['sb_date'])) $row['sb_date'] = '';
            if (is_null_time($row['eb_date'])) $row['eb_date'] = '';

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gcode'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['ca_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['ca_id2'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['ca_id3'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['type'] == "normal" ? "일반" : "bid", PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gname'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['explan'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['keywords'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['model'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['brand_nm'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['notax'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['zone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['zone_msg'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['origin'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['maker'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['isopen'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['supply_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['normal_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['goods_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['calculate_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['price_msg'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['stock_mod'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['stock_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['noti_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['odr_min'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['odr_max'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['is_point'] == "y" ? "적립" : "적립안함", PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['eb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['buy_level'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['buy_only'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_type'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_method'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_amt'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_minimum'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['simg_type'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['simg1'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg2'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg3'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg4'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg5'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg6'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['memo'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['admin_memo'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('공급업체 보류상품');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="공급업체 보류상품-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function goods_list_update()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $return_page = $this->input->post('return');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }
        if ($_POST['act_button'] == "선택삭제") {
            $gs = null;
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                // include 전에 $gs_id 값을 반드시 넘겨야 함
                $gs_id = trim($_POST['gs_id'][$k]);
                $gs = $this->global_lib->get_goods($gs_id);
                $dir_list = './data/goods/' . $gs['gcode'];
                for ($g = 1; $g <= 6; $g++) {
                    if ($gs['simg' . $g]) {
                        @unlink('./data/goods/' . $gs['simg' . $g]);
                        delete_item_thumbnail($dir_list, $gs['simg' . $g]);
                    }
                }
                // 에디터 이미지 삭제
                $this->funcomplex_lib->delete_editor_image($gs['memo']);

                // 삭제
                $sql1 = "delete from shop_goods where index_no=?";
                $sql2 = "delete from shop_goods_type where gs_id=?";
                $sql3 = "delete from shop_goods_review where gs_id=?";
                $sql4 = "delete from shop_goods_option where gs_id=?";
                $sql5 = "delete from shop_cart where gs_id=? and ct_select='0'";
                $sql6 = "delete from shop_wish where gs_id=?";
                $sql7 = "delete from shop_goods_qa where gs_id=?";
                $sql8 = "delete from shop_goods_relation where gs_id =?";
                $sql9 = "delete from shop_goods_relation where gs_id2 =?";
                $sql10 = "delete from shop_goods_timesale where gs_id =?";

                $param = array($gs_id);

                $this->query_model->returnNull($sql1, $param); // 상품테이블
                $this->query_model->returnNull($sql2, $param); //진열관리
                $this->query_model->returnNull($sql3, $param); // 상품평
                $this->query_model->returnNull($sql4, $param); // 옵션
                $this->query_model->returnNull($sql5, $param); // 장바구니
                $this->query_model->returnNull($sql6, $param); // 찜목록
                $this->query_model->returnNull($sql7, $param); // 상품문의
                $this->query_model->returnNull($sql8, $param); // 관련상품
                $this->query_model->returnNull($sql8, $param); // 관련상품
                $this->query_model->returnNull($sql10, $param); // 타임세일
            }
            $dir = "./data/goods/" . $gs['gcode'];
            $k = -2;
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        $k++;
                    }
                    closedir($dh);
                }
            }
            if ($k == 0) {
                @rmdir("./data/goods/" . $gs['gcode']);
            }
        } else if ($_POST['act_button'] == "선택순위수정") {
            $rank = $this->input->post('rank');
            $gs_id = $this->input->post('gs_id');
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = "update shop_goods set shop_goods.rank = ? where index_no = ?";
                $param = array($rank[$k], $gs_id[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($_POST['act_button'] == "선택상품복사(일반)") {
            // _GOODS_COPY_ 상수를 선언해야 goods_copy.inc.php 가 정상 작동함
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                // include 전에 $gs_id 값을 반드시 넘겨야 함
                $gs_id = trim($_POST['gs_id'][$k]);
                $sql = " select * from shop_goods where index_no = ? limit 1 ";
                $param = array($gs_id);
                $cp = $this->query_model->returnOneArr($sql, $param);

                // 상품테이블의 필드가 추가되어도 수정하지 않도록 필드명을 추출하여 insert 퀴리를 생성한다.
                $sql_common = "";
                $fields = $this->gvcomplex_lib->sql_field_names("shop_goods");
                foreach ($fields as $fld) {
                    if (in_array($fld, array(
                        'index_no',
                        'ca_id',
                        'gcode',
                        'shop_state',
                        'isopen',
                        'readcount',
                        'rank',
                        'm_count',
                        'sum_qty',
                        'reg_time',
                        'sb_date',
                        'eb_date',
                        'update_time',
                        'odr_max',
                        'odr_min',
                        'is_copy',
                        'copy_id',
                        'type'
                    )))
                        continue;

                    $sql_common .= " , $fld = '" . addslashes($cp[$fld]) . "' ";
                }

                $sql = " insert into shop_goods
                                set shop_state = '1',
                                    isopen = '1',
                                    reg_time = '" . $OM_TIME_YMDHIS . "',
                                    update_time = '" . $OM_TIME_YMDHIS . "',
                                    is_copy = 'y',
                                    copy_id = '{$gs_id}',
                                    odr_max = '1',
                                    odr_min = '1',
                                    type = 'normal'
                                    $sql_common ";
                $insert_id = $this->query_model->returnInsertId($sql);

                $sql_img = "";
                for ($g = 1; $g <= 6; $g++) {
                    if ($cp['simg' . $g] && preg_match("/^(http[s]?:\/\/)/", $cp['simg' . $g]) == false) {
                        $file = './data/goods/' . $cp['simg' . $g];
                        $dstfile = "./data/goods/{$insert_id}_" . $cp['simg' . $g];
                        $filename = basename($dstfile);

                        @copy($file, $dstfile);
                        @chmod($dstfile, 0644);
                        $sql_img .= " , simg{$g} = '$filename' ";
                    }
                }

                $new_code = time() + $insert_id;

                $sql = " update shop_goods
                                set gcode = ?
                                    $sql_img
                            where index_no = ? ";
                $param = array($new_code, $insert_id);
                $this->query_model->returnNull($sql, $param);

                // 옵션 copy
                $opt_sql = " insert ignore into shop_goods_option
                                        ( io_id, io_type, gs_id, io_price, io_stock_qty, io_noti_qty, io_use )
                                select io_id, io_type, '{$insert_id}', io_price, io_stock_qty, io_noti_qty, io_use
                                from shop_goods_option
                                where gs_id = ?
                                order by io_no asc ";
                $param = array($gs_id);

                // 상품평 카피
                $this->query_model->returnNull($opt_sql, $param);
            }
        } else if ($_POST['act_button'] == "선택상품복사(bid)") {
            // _GOODS_COPY_ 상수를 선언해야 goods_copy.inc.php 가 정상 작동함
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                // include 전에 $gs_id 값을 반드시 넘겨야 함
                $gs_id = trim($_POST['gs_id'][$k]);
                $sql = " select * from shop_goods where index_no = ? limit 1 ";
                $param = array($gs_id);
                $cp = $this->query_model->returnOneArr($sql, $param);

                // 상품평 카피
                $sql = "select * from shop_goods_review where gs_id = ?";
                $param = array($gs_id);
                $result = $this->query_model->returnArr($sql, $param);

                // 상품테이블의 필드가 추가되어도 수정하지 않도록 필드명을 추출하여 insert 퀴리를 생성한다.
                $sql_common = "";
                $fields = $this->gvcomplex_lib->sql_field_names("shop_goods");
                foreach ($fields as $fld) {
                    if (in_array($fld, array(
                        'index_no',
                        'gcode',
                        'shop_state',
                        'isopen',
                        'readcount',
                        'rank',
                        'm_count',
                        'sum_qty',
                        'reg_time',
                        'update_time',
                        'odr_max',
                        'odr_min',
                        'type',
                        'is_copy',
                        'copy_id'
                    )))
                        continue;

                    $sql_common .= " , $fld = '" . addslashes($cp[$fld]) . "' ";
                }

                $sql = " insert into shop_goods
                                set shop_state = '1',
                                    isopen = '1',
                                    reg_time = '" . $OM_TIME_YMDHIS . "',
                                    update_time = '" . $OM_TIME_YMDHIS . "',
                                    type = 'timesale',
                                    is_copy = 'y',
                                    odr_max = '1',
                                    odr_min = '1',
                                    copy_id = '{$gs_id}'
                                    $sql_common ";
                $insert_id = $this->query_model->returnInsertId($sql);

                $sql_img = "";
                for ($g = 1; $g <= 6; $g++) {
                    if ($cp['simg' . $g] && preg_match("/^(http[s]?:\/\/)/", $cp['simg' . $g]) == false) {
                        $file = './data/goods/' . $cp['simg' . $g];
                        $dstfile = "./data/goods/{$insert_id}_" . $cp['simg' . $g];
                        $filename = basename($dstfile);

                        @copy($file, $dstfile);
                        @chmod($dstfile, 0644);
                        $sql_img .= " , simg{$g} = '$filename' ";
                    }
                }

                $new_code = time() + $insert_id;

                $sql = " update shop_goods
                                set gcode = ?
                                    $sql_img
                            where index_no = ? ";
                $param = array($new_code, $insert_id);
                $this->query_model->returnNull($sql, $param);

                // 옵션 copy
                $opt_sql = " insert ignore into shop_goods_option
                                        ( io_id, io_type, gs_id, io_price, io_stock_qty, io_noti_qty, io_use )
                                select io_id, io_type, '{$insert_id}', io_price, io_stock_qty, io_noti_qty, io_use
                                from shop_goods_option
                                where gs_id = ?
                                order by io_no asc ";
                $param = array($gs_id);
                $this->query_model->returnNull($opt_sql, $param);
            }
        } else if ($_POST['act_button'] == "선택재고수정") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $gs_id = trim($_POST['gs_id'][$k]);
                $stock_qty = conv_number($_POST['stock_qty']);

                $sql = " update shop_goods set stock_qty = ? where index_no = ?";
                $param = array($stock_qty, $gs_id);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($_POST['act_button'] == "선택옵션재고수정") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $io_no = trim($_POST['io_no'][$k]);
                $io_stock_qty = conv_number($_POST['stock_qty']);

                $sql = " update shop_goods_option set io_stock_qty = ? where io_no = ?";
                $param = array($io_stock_qty, $io_no);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($_POST['act_button'] == "선택승인") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $gs_id = trim($_POST['gs_id'][$k]);
                $gs = $this->global_lib->get_goods($gs_id);

                // bid 가격 데이터 입력
                if ($gs['type'] == "timesale" && $this->global_lib->get_timesale_cnt($gs_id) <= 0) {
                    $this->insert_timesale_goods($gs_id, $gs['goods_price'], $gs['calculate_price'], $gs['stock_qty']);
                }

                $sql = " update shop_goods set shop_state = '0', approval_time = ? where index_no = ?";
                $param = array($OM_TIME_YMDHIS, $gs_id);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($_POST['act_button'] == "선택보류") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $gs_id = trim($_POST['gs_id'][$k]);

                $sql = " update shop_goods set shop_state = '2' where index_no = ?";
                $param = array($gs_id);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($_POST['act_button'] == "선택대기") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $gs_id = trim($_POST['gs_id'][$k]);

                $sql = " update shop_goods set shop_state = '1' where index_no = ?";
                $param = array($gs_id);
                $this->query_model->returnNull($sql, $param);
            }
        } else {
            alert('');
        }

        $messages = [
            "선택상품복사(일반)" => '복사가 완료되었습니다. 카테고리를 등록해주세요.',
            "선택상품복사(bid)" => '복사가 완료되었습니다. 카테고리와 bid 정보를 등록해주세요.'
        ];

        $redirectUrl = ($return_page == "timesale_state_list")
            ? base_url() . "admin/order?$q1&page=$page"
            : base_url() . "admin/goods?$q1&page=$page";

        if (isset($messages[$_POST['act_button']])) {
            alert($messages[$_POST['act_button']], $redirectUrl);
        }

        goto_url($redirectUrl);
    }
    public function goods_type_update()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $gs_id = $this->input->post('gs_id');
        for ($i = 0; $i < count($_POST['gs_id']); $i++) {
            $sql = " select count(*) as cnt
                            from shop_goods_type
                        where mb_id = 'admin'
                            and gs_id = ?";
            $param = array($gs_id[$i]);
            $row = $this->query_model->returnOneArr($sql, $param);

            $it_type1[$i] = isset($_POST['it_type1'][$i]) ? $_POST['it_type1'][$i] : "";
            $it_type2[$i] = isset($_POST['it_type2'][$i]) ? $_POST['it_type2'][$i] : "";
            $it_type3[$i] = isset($_POST['it_type3'][$i]) ? $_POST['it_type3'][$i] : "";
            $it_type4[$i] = isset($_POST['it_type4'][$i]) ? $_POST['it_type4'][$i] : "";
            $it_type5[$i] = isset($_POST['it_type5'][$i]) ? $_POST['it_type5'][$i] : "";
            if ($row['cnt']) {
                $sql = "update shop_goods_type
                            set it_type1 = ?,
                                it_type2 = ?,
                                it_type3 = ?,
                                it_type4 = ?,
                                it_type5 = ?
                            where mb_id = 'admin'
                            and gs_id =?";
                $param = array($it_type1[$i], $it_type2[$i], $it_type3[$i], $it_type4[$i], $it_type5[$i], $gs_id[$i]);
                $this->query_model->returnNull($sql, $param);
            } else {
                $sql = "insert into shop_goods_type
                            set mb_id = 'admin',
                                gs_id = ?,
                                it_type1 = ?,
                                it_type2 = ?,
                                it_type3 = ?,
                                it_type4 = ?,
                                it_type5 = ?";
                $param = array($gs_id[$i], $it_type1[$i], $it_type2[$i], $it_type3[$i], $it_type4[$i], $it_type5[$i]);
                $this->query_model->returnNull($sql, $param);
            }
        }
        goto_url(base_url() . "admin/goods?{$q1}&page={$page}");
    }
    public function goods_brand_form_update()
    {
        $uploadPath = "./data/brand";
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

        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $w = $this->input->post('w');
        $qstr = $this->load->get_var('qstr');
        $br_id = $this->input->post('br_id');

        unset($value);
        $br_logo_del = $this->input->post('br_logo_del');
        if ($br_logo_del) {
            unlink($uploadPath . '/' . $br_logo_del);
            $value['br_logo'] = '';
        }
        $error = "";
        if (isset($_FILES['br_logo']['name']) && $_FILES['br_logo']['name'] != "") {
            if (!$this->upload->do_upload('br_logo')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['br_logo'] = $new_file;
            }
        }

        $value['br_name'] = $_POST['br_name'];
        $value['br_name_eng'] = $_POST['br_name_eng'];
        $value['br_updatetime'] = $OM_TIME_YMDHIS;

        if ($w == '') {
            $value['mb_id'] = 'admin';
            $value['br_time'] = $OM_TIME_YMDHIS;
            $this->query_model->insertObj("shop_brand", $value);

            goto_url(base_url() . "admin/goods?$q1&page=$page");
        } else if ($w == 'u') {
            $this->query_model->updateObj("shop_brand", $value, "br_id='$br_id'");

            // 상품 정보도 동시에 수정
            $br_name = $this->input->post('br_name');
            $sql = "update shop_goods set brand_nm = ? where brand_uid = ?";
            $param = array($br_name, $br_id);
            $this->query_model->returnNull($sql, $param);

            goto_url(base_url() . "admin/goods?code=brand_form&w=u&br_id={$br_id}{$qstr}&page={$page}");
        }
    }
    public function goods_brand_list_update()
    {
        $page = $this->input->post('page');
        $q1 = $this->input->post('q1');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "선택수정") {
            $br_name = $this->input->post('br_name');
            $br_name_eng = $this->input->post('br_name_eng');
            $br_id = $this->input->post('br_id');
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = "update shop_brand 
                            set br_name = ?,
                                br_name_eng = ?
                            where br_id = ?";
                $param = array($br_name[$k], $br_name_eng[$k], $br_id[$k]);
                $this->query_model->returnNull($sql, $param);

                // 상품 정보도 동시에 수정
                $sql = "update shop_goods 
                            set brand_nm = ?
                            where brand_uid = ?";
                $param = array($br_name[$k], $br_id[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            $sql = "select br_logo from shop_brand where br_id = ?";
            $sql2 = "delete from shop_brand where br_id=?";
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $br_id = trim($_POST['br_id'][$k]);
                $param = array($br_id);
                $row = $this->query_model->returnOneArr($sql, $param);
                @unlink("./data/brand/" . $row['br_logo']);

                $this->query_model->returnNull($sql2, $param);
            }
        }

        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_plan_update()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "선택수정") {
            $pl_name = $this->input->post('pl_name');
            $pl_use = $this->input->post('pl_use');
            $pl_no = $this->input->post('pl_no');
            $sql = "update shop_goods_plan set pl_name = ?, pl_use = ? where pl_no = ?";
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $param = array($pl_name[$k], $pl_use[$k], $pl_no[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            $sql = "select * from shop_goods_plan where pl_no = ?";
            $sql2 = "delete from shop_goods_plan where pl_no=?";
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $pl_no = trim($_POST['pl_no'][$k]);

                $param = array($pl_no);
                $row = $this->query_model->returnOneArr($sql, $param);
                @unlink("./data/plan/" . $row['pl_limg']);
                @unlink("./data/plan/" . $row['pl_bimg']);

                $this->query_model->returnNull($sql2, $param);
            }
        }
        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_getprice_update()
    {
        $gubun = trim($this->input->post('gubun'));
        $price = trim($this->input->post('price'));
        $price_field = trim($this->input->post('price_field'));
        $price_type = trim($this->input->post('price_type'));
        $price_both = trim($this->input->post('price_both'));
        $price_target = trim($this->input->post('price_target'));
        $price_unit = trim($this->input->post('price_unit'));
        $price_cut = trim($this->input->post('price_cut'));
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');

        if (!$price)
            alert("적용할 숫자가 0 이거나 값이 없습니다.");

        if ($gubun == '1') // 선택한 상품만 적용
        {
            $sql = " select goods_price, normal_price, supply_price
                            from shop_goods
                            where index_no = ?";
            $gs_id = $this->input->post('gs_id');
            for ($i = 0; $i < count($_POST['chk']); $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $gs_id = $gs_id[$k];

                $param = array($gs_id);
                $gs = $this->query_model->returnOneArr($sql, $param);

                $goods_price = 0;
                if ($price_type == '%') // (%)를
                    $goods_price = ($gs[$price_field] / 100) * $price;
                else // (원)을
                    $goods_price = $price;

                if ($price_both == 'up') // 할증된 가격으로
                    $goods_price = $gs[$price_field] + $goods_price;
                else // 할인된 가격으로
                    $goods_price = $gs[$price_field] - $goods_price;

                if ($price_cut == 'floor') // 내림
                    $goods_price = floor($goods_price / $price_unit) * $price_unit;
                else if ($price_cut == 'round') // 반올림
                    $goods_price = round($goods_price / $price_unit) * $price_unit;
                else if ($price_cut == 'ceil') // 올림
                    $goods_price = ceil($goods_price / $price_unit) * $price_unit;

                $sql = " update shop_goods
                                set $price_target = ?
                            where index_no = ? ";
                $param = array($goods_price, $gs_id);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($gubun == '2') // 검색된 상품에 적용
        {
            foreach (explode("&", $q1) as $arr_param) {
                $param = explode("=", $arr_param);
                $$param[0] = $param[1];
            }

            if ($sel_ca1) $sca = $sel_ca1;
            if ($sel_ca2) $sca = $sel_ca2;
            if ($sel_ca3) $sca = $sel_ca3;
            if ($sel_ca4) $sca = $sel_ca4;
            if ($sel_ca5) $sca = $sel_ca5;

            $sql_common = " from shop_goods ";
            $sql_search = " where use_aff = 0 and shop_state = 0 ";
            $sql_order  = " order by index_no desc ";

            include('./admin/good/goods_query.inc.php');

            $sql = " select index_no,goods_price,normal_price,supply_price $sql_common $sql_search $sql_order ";
            $result = $this->query_model->returnArr($sql);
            $i = 0;
            foreach ($result as $key => $row) {
                $goods_price = 0;
                if ($price_type == '%') // (%)를
                    $goods_price = ($row[$price_field] / 100) * $price;
                else // (원)을
                    $goods_price = $price;

                if ($price_both == 'up') // 할증된 가격으로
                    $goods_price = $row[$price_field] + $goods_price;
                else // 할인된 가격으로
                    $goods_price = $row[$price_field] - $goods_price;

                if ($price_cut == 'floor') // 내림
                    $goods_price = floor($goods_price / $price_unit) * $price_unit;
                else if ($price_cut == 'round') // 반올림
                    $goods_price = round($goods_price / $price_unit) * $price_unit;
                else if ($price_cut == 'ceil') // 올림
                    $goods_price = ceil($goods_price / $price_unit) * $price_unit;

                $sql = " update shop_goods
                                set $price_target = ?
                            where index_no = ?";
                $param = array($goods_price, $row['index_no']);
                $this->query_model->returnNull($sql, $param);
                $i++;
            }
        }

        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_getpointupdate()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');

        $gubun = trim($this->input->post('gubun'));
        $point = trim($this->input->post('point'));

        // 포인트 비율 값 체크
        if ($point > 99)
            alert("포인트 비율을 0과 99 사이의 값으로 입력해 주십시오.");

        if ($gubun == '1') // 선택한 상품만 적용
        {
            for ($i = 0; $i < count($_POST['chk']); $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $gs_id = $_POST['gs_id'][$k];


                $sql = " select goods_price from shop_goods where index_no = ?";
                $param = array($gs_id);
                $gs = $this->query_model->returnOneArr($sql, $param);

                $gpoint = round($gs['goods_price'] * $point / 100);

                $sql = " update shop_goods
                                set gpoint = ?
                            where index_no = ?";
                $param = array($gpoint, $gs_id);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($gubun == '2') // 검색된 상품에 적용
        {
            foreach (explode("&", $q1) as $arr_param) {
                $param = explode("=", $arr_param);
                $$param[0] = $param[1];
            }

            if ($sel_ca1) $sca = $sel_ca1;
            if ($sel_ca2) $sca = $sel_ca2;
            if ($sel_ca3) $sca = $sel_ca3;
            if ($sel_ca4) $sca = $sel_ca4;
            if ($sel_ca5) $sca = $sel_ca5;

            $sql_common = " from shop_goods ";
            $sql_search = " where use_aff = 0 and shop_state = 0 ";
            $sql_order  = " order by index_no desc ";

            include('./admin/good/goods_query.inc.php');

            $sql = " select index_no, goods_price $sql_common $sql_search $sql_order ";
            $result = $this->query_model->returnArr($sql);
            foreach ($result as $key => $row) {
                $gpoint = round($row['goods_price'] * $point / 100);

                $sql = " update shop_goods
                                set gpoint = ?
                            where index_no = ?";
                $param = array($gpoint, $row['index_no']);
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_getuseupdate()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $gubun = trim($_POST['gubun']);
        $isopen = trim($_POST['isopen']);

        if ($gubun == '1') // 선택한 상품만 적용
        {
            $gs_id = $this->input->post('gs_id');
            for ($i = 0; $i < count($_POST['chk']); $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = " update shop_goods
                                set isopen = ?
                            where index_no =?";
                $param = array($isopen, $gs_id[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($gubun == '2') // 검색된 상품에 적용
        {
            foreach (explode("&", $q1) as $arr_param) {
                $param = explode("=", $arr_param);
                $$param[0] = $param[1];
            }

            if ($sel_ca1) $sca = $sel_ca1;
            if ($sel_ca2) $sca = $sel_ca2;
            if ($sel_ca3) $sca = $sel_ca3;
            if ($sel_ca4) $sca = $sel_ca4;
            if ($sel_ca5) $sca = $sel_ca5;

            $sql_common = " from shop_goods ";
            $sql_search = " where use_aff = 0 and shop_state = 0 ";
            $sql_order  = " order by index_no desc ";

            include('./admin/good/goods_query.inc.php');


            $sql = " select index_no $sql_common $sql_search $sql_order ";
            $result = $this->query_model->returnArr($sql);
            foreach ($result as $key => $row) {
                $sql = "update shop_goods
                                set isopen = ?
                            where index_no = ?";
                $param = array($isopen, $row['index_no']);
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_getmoveupdate()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $gubun = trim($_POST['gubun']);
        $new_ca_id = trim($_POST['new_ca_id']);
        $act = trim($_POST['act']);

        if ($gubun == '1') // 선택한 상품만 적용
        {
            for ($i = 0; $i < count($_POST['chk']); $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $gs_id = $_POST['gs_id'][$k];

                $sql_fld = "";

                if ($act == 1) { // 분류연결
                    $gs = $this->global_lib->get_goods($gs_id, 'ca_id, ca_id2, ca_id3');

                    if (!$gs['ca_id'])
                        $sql_fld = " ca_id  = '$new_ca_id' ";
                    else if (!$gs['ca_id2'])
                        $sql_fld = " ca_id2 = '$new_ca_id' ";
                    else if (!$gs['ca_id3'])
                        $sql_fld = " ca_id3 = '$new_ca_id' ";
                } else if ($act == 2) // 분류이동
                    $sql_fld = " ca_id = '$new_ca_id', ca_id2 = '', ca_id3 = '' ";
                else if ($act == 3) // 모든 분류를 연결해제
                    $sql_fld = " ca_id = '', ca_id2 = '', ca_id3 = '' ";
                else if ($act == 4) // 추가 분류만 연결해제
                    $sql_fld = " ca_id2 = '', ca_id3 = '' ";

                if ($sql_fld) {
                    $sql = "update shop_goods set $sql_fld where index_no =?";
                    $param = array($gs_id);
                    $this->query_model->returnNull($sql, $param);
                }
            }
        } else if ($gubun == '2') // 검색된 상품에 적용
        {
            foreach (explode("&", $q1) as $arr_param) {
                $param = explode("=", $arr_param);
                $$param[0] = $param[1];
            }

            if ($sel_ca1) $sca = $sel_ca1;
            if ($sel_ca2) $sca = $sel_ca2;
            if ($sel_ca3) $sca = $sel_ca3;
            if ($sel_ca4) $sca = $sel_ca4;
            if ($sel_ca5) $sca = $sel_ca5;

            $sql_common = " from shop_goods ";
            $sql_search = " where use_aff = 0 and shop_state = 0 ";
            $sql_order  = " order by index_no desc ";

            include('./admin/good/goods_query.inc.php');

            $sql = " select index_no, ca_id, ca_id2, ca_id3 $sql_common $sql_search $sql_order ";
            $result = $this->query_model->returnArr($sql);
            foreach ($result as $key => $row) {
                $gs_id = $row['index_no'];

                $sql_fld = "";

                if ($act == 1) { // 분류연결
                    if (!$row['ca_id'])
                        $sql_fld = " ca_id  = '$new_ca_id' ";
                    else if (!$row['ca_id2'])
                        $sql_fld = " ca_id2 = '$new_ca_id' ";
                    else if (!$row['ca_id3'])
                        $sql_fld = " ca_id3 = '$new_ca_id' ";
                } else if ($act == 2) // 분류이동
                    $sql_fld = " ca_id = '$new_ca_id', ca_id2 = '', ca_id3 = '' ";
                else if ($act == 3) // 모든 분류를 연결해제
                    $sql_fld = " ca_id = '', ca_id2 = '', ca_id3 = '' ";
                else if ($act == 4) // 추가 분류만 연결해제
                    $sql_fld = " ca_id2 = '', ca_id3 = '' ";

                if ($sql_fld) {
                    $sql = "update shop_goods set $sql_fld where index_no =?";
                    $param = array($gs_id);
                    $this->query_model->returnNull($sql, $param);
                }
            }
        }
        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_getbrandupdate()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $gubun = trim($_POST['gubun']);
        $new_brand = trim($_POST['new_brand']);
        $act = trim($_POST['act']);

        if ($gubun == '1') // 선택한 상품만 적용
        {
            $gs_id = $this->input->post('gs_id');
            for ($i = 0; $i < count($_POST['chk']); $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                if ($act == 1) {
                    $brand_uid = $new_brand;
                    $brand_nm = $this->global_lib->get_brand($new_brand); //브랜드명
                } else {
                    $brand_uid = "";
                    $brand_nm = "";
                }

                $sql = " update shop_goods
                                set brand_uid = ?,
                                    brand_nm = ?
                            where index_no = ? ";
                $param = array($brand_uid, $brand_nm, $gs_id[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($gubun == '2') // 검색된 상품에 적용
        {
            foreach (explode("&", $q1) as $arr_param) {
                $param = explode("=", $arr_param);
                $$param[0] = $param[1];
            }

            if ($sel_ca1) $sca = $sel_ca1;
            if ($sel_ca2) $sca = $sel_ca2;
            if ($sel_ca3) $sca = $sel_ca3;
            if ($sel_ca4) $sca = $sel_ca4;
            if ($sel_ca5) $sca = $sel_ca5;

            $sql_common = " from shop_goods ";
            $sql_search = " where use_aff = 0 and shop_state = 0 ";
            $sql_order  = " order by index_no desc ";

            include('./admin/good/goods_query.inc.php');

            $sql = " select index_no $sql_common $sql_search $sql_order ";
            $result = $this->query_model->returnArr($sql);
            foreach ($result as $key => $row) {
                if ($act == 1) {
                    $brand_uid = $new_brand;
                    $brand_nm = $this->global_lib->get_brand($new_brand); //브랜드명
                } else {
                    $brand_uid = "";
                    $brand_nm = "";
                }

                $sql = " update shop_goods
                                set brand_uid = ?,
                                    brand_nm = ?
                            where index_no = ?";
                $param = array($brand_uid, $brand_nm, $row['index_no']);
                $this->query_model->returnNull($sql, $param);
            }
        }
        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_getdeliveryupdate()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $gubun = trim($_POST['gubun']);
        $sc_type = trim($_POST['sc_type']);
        $sc_method = trim($_POST['sc_method']);
        $sc_amt = trim($_POST['sc_amt']);
        $sc_each_use = trim($_POST['sc_each_use']);
        $sc_minimum    = trim($_POST['sc_minimum']);
        $zone = trim($_POST['zone']);
        $zone_msg = trim($_POST['zone_msg']);

        if ($gubun == '1') // 선택한 상품만 적용
        {
            $gs_id = $this->input->post('gs_id');
            for ($i = 0; $i < count($_POST['chk']); $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $param['sc_type'] = $sc_type;
                $param['sc_method'] = $sc_method;
                $param['sc_amt'] = $sc_amt;
                $param['sc_each_use'] = $sc_each_use;
                $param['sc_minimum'] = $sc_minimum;
                $param['zone'] = $zone;
                $param['zone_msg'] = $zone_msg;
                $this->query_model->updateObj('shop_goods', $param, "index_no = '{$gs_id[$k]}'");
            }
        } else if ($gubun == '2') // 검색된 상품에 적용
        {
            foreach (explode("&", $q1) as $arr_param) {
                $param = explode("=", $arr_param);
                $$param[0] = $param[1];
            }

            if ($sel_ca1) $sca = $sel_ca1;
            if ($sel_ca2) $sca = $sel_ca2;
            if ($sel_ca3) $sca = $sel_ca3;
            if ($sel_ca4) $sca = $sel_ca4;
            if ($sel_ca5) $sca = $sel_ca5;

            $sql_common = " from shop_goods ";
            $sql_search = " where use_aff = 0 and shop_state = 0 ";
            $sql_order  = " order by index_no desc ";

            include('./admin/good/goods_query.inc.php');

            $sql = " select index_no $sql_common $sql_search $sql_order ";
            $result = $this->query_model->returnArr($sql);
            foreach ($result as $key => $row) {
                $param['sc_type'] = $sc_type;
                $param['sc_method'] = $sc_method;
                $param['sc_amt'] = $sc_amt;
                $param['sc_each_use'] = $sc_each_use;
                $param['sc_minimum'] = $sc_minimum;
                $param['zone'] = $zone;
                $param['zone_msg'] = $zone_msg;
                $this->query_model->updateObj('shop_goods', $param, "index_no = '{$row['index_no']}'");
            }
        }
        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_getbuylevelupdate()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');

        $gubun = trim($_POST['gubun']);
        $buy_level = trim($_POST['buy_level']);
        $buy_only = trim($_POST['buy_only']);

        if ($gubun == '1') // 선택한 상품만 적용
        {
            $gs_id = $this->input->post('gs_id');
            for ($i = 0; $i < count($_POST['chk']); $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = " update shop_goods
                                set buy_level = ?,
                                    buy_only = ?
                            where index_no = ?";
                $param = array($buy_level, $buy_only, $gs_id[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($gubun == '2') // 검색된 상품에 적용
        {
            foreach (explode("&", $q1) as $arr_param) {
                $param = explode("=", $arr_param);
                $$param[0] = $param[1];
            }

            if ($sel_ca1) $sca = $sel_ca1;
            if ($sel_ca2) $sca = $sel_ca2;
            if ($sel_ca3) $sca = $sel_ca3;
            if ($sel_ca4) $sca = $sel_ca4;
            if ($sel_ca5) $sca = $sel_ca5;

            $sql_common = " from shop_goods ";
            $sql_search = " where use_aff = 0 and shop_state = 0 ";
            $sql_order  = " order by index_no desc ";

            include('./admin/good/goods_query.inc.php');

            $sql = " select index_no $sql_common $sql_search $sql_order ";
            $result = $this->query_model->returnArr($sql);
            foreach ($result as $key => $row) {
                $sql = " update shop_goods
                                set buy_level = ?,
                                    buy_only = ?
                            where index_no = ?";
                $param = array($buy_level, $buy_only, $row['index_no']);
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_gift_delete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $gr_id = trim($_POST['gr_id'][$k]);

            // 삭제
            $param = array($gr_id);
            $sql = "delete from shop_gift_group where gr_id=?";
            $sql2 = "delete from shop_gift where gr_id=?";
            $this->query_model->returnNull($sql, $param);
            $this->query_model->returnNull($sql2, $param);
        }

        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_coupon_delete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }
        $cp_id = $this->input->post('cp_id');
        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $sql = " delete from shop_coupon where cp_id=?";
            $param = array($cp_id[$k]);
            $this->query_model->returnNull($sql, $param);
        }

        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_coupon_form_update()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $w = $this->input->post('w');
        $cp_id = $this->input->post('cp_id') ?? "";

        unset($value);
        $value['cp_type']            = $this->input->post('cp_type') ?? "";
        $value['cp_dlimit']            = $this->input->post('cp_dlimit') ?? "";
        $value['cp_dlevel']            = $this->input->post('cp_dlevel') ?? "";
        $value['cp_subject']        = $this->input->post('cp_subject') ?? "";
        $value['cp_explan']            = $this->input->post('cp_explan') ?? "";
        $value['cp_use']            = $this->input->post('cp_use') ?? "";
        $value['cp_download']        = $this->input->post('cp_download') ?? "";
        $value['cp_overlap']        = $this->input->post('cp_overlap') ?? "";
        $value['cp_sale_type']        = $this->input->post('cp_sale_type') ?? "";
        $value['cp_sale_percent']    = $this->input->post('cp_sale_percent') ?? "";
        $value['cp_sale_amt_max']    = $this->input->post('cp_sale_amt_max') ?? "";
        $value['cp_sale_amt']        = $this->input->post('cp_sale_amt') ?? "";
        $value['cp_dups']            = $this->input->post('cp_dups') ?? "";
        $value['cp_pub_sdate']        = $this->input->post('cp_pub_sdate') ?? "";
        $value['cp_pub_edate']        = $this->input->post('cp_pub_edate') ?? "";
        $value['cp_pub_sday']        = $this->input->post('cp_pub_sday') ?? "";
        $value['cp_pub_eday']        = $this->input->post('cp_pub_eday') ?? "";
        $value['cp_use_sex']        = $this->input->post('cp_use_sex') ?? "";
        $value['cp_use_sage']        = $this->input->post('cp_use_sage') ?? "";
        $value['cp_use_eage']        = $this->input->post('cp_use_eage') ?? "";
        $value['cp_week_day']        = get_pagecode($this->input->post('cp_week_day'));
        $value['cp_pub_1_use']        = $this->input->post('cp_pub_1_use') ?? "";
        $value['cp_pub_shour1']        = $this->input->post('cp_pub_shour1') ? sprintf('%02d', $this->input->post('cp_pub_shour1')) : '';
        $value['cp_pub_ehour1']        = $_POST['cp_pub_ehour1'] ? sprintf('%02d', $this->input->post('cp_pub_ehour1')) : '';
        $value['cp_pub_1_cnt']        = $this->input->post('cp_pub_1_cnt') ?? "";
        $value['cp_pub_2_use']        = $this->input->post('cp_pub_2_use') ?? "";
        $value['cp_pub_shour2']        = $this->input->post('cp_pub_shour2') ? sprintf('%02d', $this->input->post('cp_pub_shour2')) : '';
        $value['cp_pub_ehour2']        = $this->input->post('cp_pub_ehour2') ? sprintf('%02d', $this->input->post('cp_pub_ehour2')) : '';
        $value['cp_pub_2_cnt']        = $this->input->post('cp_pub_2_cnt') ?? "";
        $value['cp_pub_3_use']        = $this->input->post('cp_pub_3_use') ?? "";
        $value['cp_pub_shour3']        = $this->input->post('cp_pub_shour3') ? sprintf('%02d', $this->input->post('cp_pub_shour3')) : '';
        $value['cp_pub_ehour3']        = $this->input->post('cp_pub_ehour3') ? sprintf('%02d', $this->input->post('cp_pub_ehour3')) : '';
        $value['cp_pub_3_cnt']        = $this->input->post('cp_pub_3_cnt') ?? "";
        $value['cp_inv_type']        = $this->input->post('cp_inv_type') ?? "";
        $value['cp_inv_sdate']        = $this->input->post('cp_inv_sdate') ?? "";
        $value['cp_inv_edate']        = $this->input->post('cp_inv_edate') ?? "";
        $value['cp_inv_shour1']        = sprintf('%02d', $this->input->post('cp_inv_shour1'));
        $value['cp_inv_shour2']        = sprintf('%02d', $this->input->post('cp_inv_shour2'));
        $value['cp_inv_day']        = $this->input->post('cp_inv_day') ?? "";
        $value['cp_low_amt']        = $this->input->post('cp_low_amt') ?? "";
        $value['cp_use_part']        = $this->input->post('cp_use_part') ?? "";
        $value['cp_use_goods']        = $this->input->post('cp_use_goods') ?? "";
        $value['cp_use_category']    = $this->input->post('cp_use_category') ?? "";
        $value['cp_udate'] = $OM_TIME_YMDHIS;

        if ($w == "") {
            $value['cp_wdate'] = $OM_TIME_YMDHIS;
            $cp_id = $this->query_model->insertObj("shop_coupon", $value);
            goto_url(base_url() . "admin/member?code=coupon_form&w=u&cp_id=$cp_id");
        } else if ($w == "u") {
            $this->query_model->updateObj("shop_coupon", $value, "cp_id='$cp_id'");
            $qstr = $this->load->get_var('qstr');
            $page = $this->input->post('page');
            $cp_id = $this->input->post('cp_id');
            goto_url(base_url() . "admin/member?code=coupon_form&w=u&cp_id=$cp_id$qstr&page=$page");
        }
    }
    public function goods_gift_form_update()
    {
        $w = $this->input->post('w');
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $gr_id = $this->input->post('gr_id');
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $gr_subject = $this->input->post('gr_subject');
        $gr_price = $this->input->post('gr_price');
        $gr_sdate = $this->input->post('gr_sdate');
        $gr_edate = $this->input->post('gr_edate');
        $gr_explan = $this->input->post('gr_explan');
        $use_gift = $this->input->post('use_gift');
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        if ($w == "") {
            unset($value);
            $value['gr_id']         = $gr_id;
            $value['gr_subject'] = $gr_subject;
            $value['gr_explan']     = $gr_explan;
            $value['gr_price']     = $gr_price;
            $value['gr_sdate']     = $gr_sdate;
            $value['gr_edate']     = $gr_edate;
            $value['use_gift']     = $use_gift;
            $value['gr_wdate']     = $OM_TIME_YMD;
            $this->query_model->insertObj("shop_gift_group", $value);

            for ($i = 0; $i < (int)$_POST['gr_quant']; $i++) {
                $j = 0;
                do {
                    $gi_num = get_coupon_id($_POST['use_gift']);

                    $sql = " select count(*) as cnt from shop_gift where gi_num = ?";
                    $param = array($gi_num);
                    $row = $this->query_model->returnOneArr($sql, $param);

                    if (!$row['cnt'])
                        break;
                    else {
                        if ($j > 20)
                            die('Coupon ID Error');
                    }
                } while (1);

                $sql = "insert into shop_gift set gr_id = ? , gr_subject = ? , gr_price = ? , gr_sdate = ? , gr_edate = ? , gi_num = ? ";
                $param = array($gr_id, $gr_subject, $gr_price, $gr_sdate, $gr_edate, $gi_num);
                $this->query_model->returnNull($sql, $param);
            }

            goto_url(base_url() . "admin/member?code=gift_form&w=u&gr_id=$gr_id");
        } else if ($w == "u") {
            unset($value);
            $value['gr_subject']    = $_POST['gr_subject'];
            $value['gr_explan']        = $_POST['gr_explan'];
            $value['gr_price']        = $_POST['gr_price'];
            $value['gr_sdate']        = $_POST['gr_sdate'];
            $value['gr_edate']        = $_POST['gr_edate'];
            $this->query_model->updateObj("shop_gift_group", $value, "gr_id='$gr_id'");

            unset($value);
            $value['gr_subject']    = $_POST['gr_subject'];
            $value['gr_price']        = $_POST['gr_price'];
            $value['gr_sdate']        = $_POST['gr_sdate'];
            $value['gr_edate']        = $_POST['gr_edate'];
            $this->query_model->updateObj("shop_gift", $value, "gr_id='$gr_id'");

            goto_url(base_url() . "admin/member?code=gift_form&w=u&gr_id=$gr_id$q1&page=$page");
        }
    }
    public function goods_gift_excel()
    {
        $gr_id = $this->input->get('gr_id');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

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

        $sql = " select * $sql_common $sql_search $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '일련번호')
            ->setCellValue($char++ . '1', '쿠폰명')
            ->setCellValue($char++ . '1', '설명')
            ->setCellValue($char++ . '1', '발행금액')
            ->setCellValue($char++ . '1', '시작일')
            ->setCellValue($char++ . '1', '종료일')
            ->setCellValue($char++ . '1', '등록일')
            ->setCellValue($char++ . '1', '인증번호')
            ->setCellValue($char++ . '1', '사용')
            ->setCellValue($char++ . '1', '사용자아이디')
            ->setCellValue($char++ . '1', '사용자이름')
            ->setCellValue($char++ . '1', '최종사용일');

        $i = 2;
        foreach ($result as $key => $row) {
            $sql = "select * from shop_gift_group where gr_id =?";
            $param = array($row['gr_id']);
            $grp = $this->query_model->returnOneArr($sql, $param);

            if (is_null_time($row['mb_wdate'])) $row['mb_wdate'] = '';
            $row['gi_use'] = $row['gi_use'] ? 'yes' : 'no';

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['gr_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gr_subject'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $grp['gr_explan'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gr_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['gr_sdate'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gr_edate'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $grp['gr_wdate'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gi_num'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gi_use'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['mb_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['mb_wdate'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('쿠폰(인쇄용)');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="쿠폰(인쇄용)-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function goods_popular_list_delete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "선택삭제") {
            $pp_id = $this->input->post('pp_id');
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = " delete from shop_popular where pp_id = ? ";
                $param = array($pp_id[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_userlist_excel()
    {
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $sel_ca1 = $this->input->get('sel_ca1');
        $sel_ca2 = $this->input->get('sel_ca2');
        $sel_ca3 = $this->input->get('sel_ca3');
        $sel_ca4 = $this->input->get('sel_ca4');
        $sel_ca5 = $this->input->get('sel_ca5');
        $stx = $this->input->get('stx');
        $sfl = $this->input->get('sfl');
        $q_brand = $this->input->get('q_brand');
        $q_zone = $this->input->get('q_zone');
        $fr_stock = $this->input->get('fr_stock');
        $to_stock = $this->input->get('to_stock');
        $fr_price = $this->input->get('fr_price');
        $to_price = $this->input->get('to_price');
        $q_isopen = $this->input->get('q_isopen');
        $q_notax = $this->input->get('q_notax');
        $q_option = $this->input->get('q_option');
        $q_supply = $this->input->get('q_supply');
        $orderby = $this->input->get('orderby');

        $filed = $this->input->get('filed');
        $filed = $this->db->escape_str($filed);

        $sca = "";

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        if ($sel_ca1) $sca = $sel_ca1;
        if ($sel_ca2) $sca = $sel_ca2;
        if ($sel_ca3) $sca = $sel_ca3;
        if ($sel_ca4) $sca = $sel_ca4;
        if ($sel_ca5) $sca = $sel_ca5;

        $sql_common = " from shop_goods ";
        $sql_search = " where use_aff = 1 ";

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

        $sql = " select * $sql_common $sql_search $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '가맹점ID')
            ->setCellValue($char++ . '1', '상품코드')
            ->setCellValue($char++ . '1', '대표분류')
            ->setCellValue($char++ . '1', '추가분류2')
            ->setCellValue($char++ . '1', '추가분류3')
            ->setCellValue($char++ . '1', '상품타입')
            ->setCellValue($char++ . '1', '상품명')
            ->setCellValue($char++ . '1', '짧은설명')
            ->setCellValue($char++ . '1', '검색키워드')
            ->setCellValue($char++ . '1', '모델명')
            ->setCellValue($char++ . '1', '브랜드')
            ->setCellValue($char++ . '1', '과세설정')
            ->setCellValue($char++ . '1', '판매가능지역')
            ->setCellValue($char++ . '1', '판매가능지역 추가설명')
            ->setCellValue($char++ . '1', '원산지')
            ->setCellValue($char++ . '1', '제조사')
            ->setCellValue($char++ . '1', '판매여부')
            ->setCellValue($char++ . '1', '공급가격')
            ->setCellValue($char++ . '1', '시중가격')
            ->setCellValue($char++ . '1', '판매가격')
            ->setCellValue($char++ . '1', '실제판매가(bid)')
            ->setCellValue($char++ . '1', '가격대체문구')
            ->setCellValue($char++ . '1', '재고적용타입')
            ->setCellValue($char++ . '1', '재고수량')
            ->setCellValue($char++ . '1', '재고통보수량')
            ->setCellValue($char++ . '1', '최소주문한도')
            ->setCellValue($char++ . '1', '최대주문한도')
            ->setCellValue($char++ . '1', '포인트 적립여부')
            ->setCellValue($char++ . '1', '판매기간 시작일')
            ->setCellValue($char++ . '1', '판매기간 종료일')
            ->setCellValue($char++ . '1', '구매가능레벨')
            ->setCellValue($char++ . '1', '가격공개')
            ->setCellValue($char++ . '1', '배송비유형')
            ->setCellValue($char++ . '1', '배송비결제')
            ->setCellValue($char++ . '1', '기본배송비')
            ->setCellValue($char++ . '1', '조건배송비')
            ->setCellValue($char++ . '1', '이미지등록방식')
            ->setCellValue($char++ . '1', '소이미지')
            ->setCellValue($char++ . '1', '중이미지1')
            ->setCellValue($char++ . '1', '중이미지2')
            ->setCellValue($char++ . '1', '중이미지3')
            ->setCellValue($char++ . '1', '중이미지4')
            ->setCellValue($char++ . '1', '중이미지5')
            ->setCellValue($char++ . '1', '상세설명')
            ->setCellValue($char++ . '1', '관리자메모');

        $i = 2;
        foreach ($result as $key => $row) {
            if (is_null_time($row['sb_date'])) $row['sb_date'] = '';
            if (is_null_time($row['eb_date'])) $row['eb_date'] = '';

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gcode'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['ca_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['ca_id2'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['ca_id3'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['type'] == "normal" ? "일반" : "bid", PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gname'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['explan'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['keywords'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['model'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['brand_nm'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['notax'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['zone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['zone_msg'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['origin'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['maker'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['isopen'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['supply_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['normal_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['goods_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['calculate_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['price_msg'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['stock_mod'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['stock_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['noti_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['odr_min'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['odr_max'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['is_point'] == "y" ? "적립" : "적립안함", PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['eb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['buy_level'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['buy_only'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_type'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_method'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_amt'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sc_minimum'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['simg_type'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['simg1'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg2'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg3'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg4'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg5'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['simg6'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['memo'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['admin_memo'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('가맹점 상품');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="가맹점 상품-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function goods_qa_form_update()
    {
        $w = $this->input->post('w');
        $iq_id = $this->input->post('iq_id');
        $qstr = $this->load->get_var('qstr');
        $page = $this->input->post('page');
        if ($w == "u") {
            unset($value);
            $value['iq_ty']            = $this->input->post('iq_ty');
            $value['iq_secret']        = $this->input->post('iq_secret');
            $value['iq_name']        = $this->input->post('iq_name');
            $value['iq_email']        = $this->input->post('iq_email');
            $value['iq_hp']            = $this->input->post('iq_hp');
            $value['iq_subject']    = $this->input->post('iq_subject');
            $value['iq_question']    = $this->input->post('iq_question');
            $value['iq_answer']        = $this->input->post('iq_answer');
            $iq_answer = $this->input->post('iq_answer');
            if (!$iq_answer)
                $value['iq_reply'] = 0;
            else
                $value['iq_answer_date'] = date("Y-m-d H:i:s", time());
            $value['iq_reply'] = 1;

            $this->query_model->updateObj("shop_goods_qa", $value, "iq_id='$iq_id'");

            goto_url(base_url() . "admin/goods?code=qa_form&w=u&iq_id=$iq_id$qstr&page=$page");
        }
    }
    public function goods_qa_delete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }
        $iq_id = $this->input->post('iq_id');
        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $sql = " delete from shop_goods_qa where iq_id = ?";
            $param = array($iq_id[$k]);
            $this->query_model->returnNull($sql, $param);
        }

        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    public function goods_review_delete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $index_no = trim($_POST['index_no'][$k]);
            $sql = "delete from shop_goods_review where index_no=?";
            $param = array($index_no);
            $this->query_model->returnNull($sql, $param);
        }

        goto_url(base_url() . "admin/goods?$q1&page=$page");
    }
    #상품 관련된 함수들 끝
    #주문관리
    public function order_excel2()
    {
        $od_id = $this->input->get('od_id');
        if (!$od_id)
            alert("주문번호가 넘어오지 않았습니다.");

        $sql = " select * from shop_order where od_id IN ({$od_id}) order by od_time desc, index_no asc ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $result = $resultObj->result_array();
        $cnt = $resultObj->num_rows();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");
        $this->order_excel_sub($result);
    }
    private function order_excel_sub($result)
    {
        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '가맹점ID')
            ->setCellValue($char++ . '1', '판매자ID')
            ->setCellValue($char++ . '1', '회원ID')
            ->setCellValue($char++ . '1', '주문번호')
            ->setCellValue($char++ . '1', '일련번호')
            ->setCellValue($char++ . '1', '상품코드')
            ->setCellValue($char++ . '1', '상품명')
            ->setCellValue($char++ . '1', '옵션')
            ->setCellValue($char++ . '1', '공급가')
            ->setCellValue($char++ . '1', '판매가')
            ->setCellValue($char++ . '1', '수량')
            ->setCellValue($char++ . '1', '쿠폰할인')
            ->setCellValue($char++ . '1', '포인트결제')
            ->setCellValue($char++ . '1', '예치금결제')
            ->setCellValue($char++ . '1', '배송비')
            ->setCellValue($char++ . '1', '실결제금액')
            ->setCellValue($char++ . '1', '총주문금액')
            ->setCellValue($char++ . '1', '결제방법')
            ->setCellValue($char++ . '1', '주문상태')
            ->setCellValue($char++ . '1', '주문채널')
            ->setCellValue($char++ . '1', '주문자명')
            ->setCellValue($char++ . '1', '수취인명')
            ->setCellValue($char++ . '1', '수취인전화번호')
            ->setCellValue($char++ . '1', '수취인핸드폰')
            ->setCellValue($char++ . '1', '수취인우편번호')
            ->setCellValue($char++ . '1', '수취인주소')
            ->setCellValue($char++ . '1', '주문시요청사항')
            ->setCellValue($char++ . '1', '배송회사')
            ->setCellValue($char++ . '1', '운송장번호')
            ->setCellValue($char++ . '1', '입금자명')
            ->setCellValue($char++ . '1', '입금일시')
            ->setCellValue($char++ . '1', '주문일시')
            ->setCellValue($char++ . '1', '거래증빙')
            ->setCellValue($char++ . '1', '세금계산서(상호명)')
            ->setCellValue($char++ . '1', '세금계산서(대표자)')
            ->setCellValue($char++ . '1', '세금계산서(사업자등록번호)')
            ->setCellValue($char++ . '1', '세금계산서(사업장주소)')
            ->setCellValue($char++ . '1', '세금계산서(업태)')
            ->setCellValue($char++ . '1', '세금계산서(종목)')
            ->setCellValue($char++ . '1', '현금영수증(사업자 지출증빙용)')
            ->setCellValue($char++ . '1', '현금영수증(개인 소득공제용)')
            ->setCellValue($char++ . '1', '관리자메모');
        $i = 2;
        $gw_status = $this->config->item('gw_status');
        foreach ($result as $key => $row) {
            $gs = unserialize($row['od_goods']);
            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->excel_order_list($row, $amount);

            $char = 'A';
            $gs['gcode'] = isset($gs['gcode']) ? $gs['gcode'] : "";
            $gs['gname'] = isset($gs['gname']) ? $gs['gname'] : "";
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $sodr['od_pt_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $sodr['od_seller_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $sodr['od_mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['od_id'] . $sodr['od_test'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['od_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $gs['gcode'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $gs['gname'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $sodr['it_options'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, ($row['supply_price'] / $row['sum_qty']), PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, ($row['goods_price'] / $row['sum_qty']), PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['sum_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['coupon_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['use_point'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['use_mileage'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['baesong_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['use_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['goods_price'] + $row['baesong_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $sodr['od_paytype'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $gw_status[$row['dan']], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $sodr['od_mobile'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['b_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['b_telephone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['b_cellphone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['b_zip'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $this->funcomplex_lib->print_address($row['b_addr1'], $row['b_addr2'], $row['b_addr3'], $row['b_addr_jibeon']), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['memo'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $sodr['od_delivery_company'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['delivery_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['deposit_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $sodr['od_receipt_time'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['od_time'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $sodr['od_taxbill'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_owner'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_saupja_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_addr'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_item'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['company_service'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['tax_saupja_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['tax_hp'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['shop_memo'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('주문내역');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="주문내역-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function order_excel()
    {
        $fr_date = $this->input->get('fr_date');
        $to_date = $this->input->get('to_date');
        $code = $this->input->get('code');
        $sfl = $this->input->get('sfl');
        $stx = $this->input->get('stx');
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
        $sel_field = $this->input->get('sel_field');

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $sql_common = " from shop_order ";

        $where = array();

        if ($code == 'list') // 전체주문내역
            $where[] = " dan != '0' ";
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

        $sql_order = " order by od_time desc, index_no asc ";

        $sql = " select * {$sql_common} {$sql_search} {$sql_order} ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");
        $this->order_excel_sub($result);
    }
    public function order_update()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "입금완료") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k     = $_POST['chk'][$i];
                $od_id = $_POST['od_id'][$k];

                $od = $this->global_lib->get_order($od_id);
                if ($od['dan'] != 1) continue;
                if (!in_array($od['paymethod'], array('무통장', '가상계좌'))) continue;

                $this->global_lib->change_order_status_ipgum($od_id);

                $this->funcomplex_lib->moonjaro_order_sms_send($od['pt_id'], $od['cellphone'], $od_id, 3);
            }
        } else if ($_POST['act_button'] == "주문취소") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k     = $_POST['chk'][$i];
                $od_id = $_POST['od_id'][$k];

                $od = $this->global_lib->get_order($od_id);
                if ($od['dan'] != 1) continue;
                if (!in_array($od['paymethod'], array('무통장', '가상계좌'))) continue;

                $sql = " select od_no from shop_order where od_id = ? order by index_no ";
                $param = array($od_id);
                $res = $this->query_model->returnArr($sql, $param);
                foreach ($res as $key => $row) {
                    $this->funcomplex_lib->change_order_status_6($row['od_no']);
                }
                $this->funcomplex_lib->moonjaro_order_sms_send($od['pt_id'], $od['cellphone'], $od_id, 5);
            }
        } else if ($_POST['act_button'] == "배송준비") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k     = $_POST['chk'][$i];
                $od_no = $_POST['od_no'][$k];

                $od = $this->global_lib->get_order($od_no);
                if ($od['dan'] != 2) continue;

                $this->global_lib->change_order_status_3($od_no);
            }
        } else if ($_POST['act_button'] == "배송중") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k             = $_POST['chk'][$i];
                $od_no         = $_POST['od_no'][$k];
                $delivery     = $_POST['delivery'][$k];
                $delivery_no = $_POST['delivery_no'][$k];

                $od = $this->global_lib->get_order($od_no);
                if ($od['dan'] != 3) continue;

                $this->global_lib->change_order_status_4($od_no, $delivery, $delivery_no);

                $od_sms_baesong[$od['od_id']] = $od['cellphone'];
            }

            foreach ($od_sms_baesong as $key => $recv) {
                $q = $this->global_lib->get_order($key, 'pt_id');
                $this->funcomplex_lib->moonjaro_order_sms_send($q['pt_id'], $recv, $key, 4);
            }
        } else if ($_POST['act_button'] == "배송완료") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k             = $_POST['chk'][$i];
                $od_no         = $_POST['od_no'][$k];
                $delivery     = $_POST['delivery'][$k];
                $delivery_no = $_POST['delivery_no'][$k];

                $od = $this->global_lib->get_order($od_no);
                if ($od['dan'] != 4) continue;

                $this->funcomplex_lib->change_order_status_5($od_no, $delivery, $delivery_no);

                $od_sms_delivered[$od['od_id']] = $od['cellphone'];
            }

            foreach ($od_sms_delivered as $key => $recv) {
                $q = $this->global_lib->get_order($key, 'pt_id');
                $this->funcomplex_lib->moonjaro_order_sms_send($q['pt_id'], $recv, $key, 6);
            }
        } else if ($_POST['act_button'] == "구매확정") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k     = $_POST['chk'][$i];
                $od_no = $_POST['od_no'][$k];

                $this->global_lib->change_status_final($od_no);
            }
        } else if ($_POST['act_button'] == "구매확정취소") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k     = $_POST['chk'][$i];
                $od_no = $_POST['od_no'][$k];

                $this->global_lib->change_status_final_cancel($od_no);
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k     = $_POST['chk'][$i];
                $od_id = $_POST['od_id'][$k];

                $od = $this->global_lib->get_order($od_id);
                if (!in_array($od['dan'], array(1, 6)))
                    alert('입금대기, 주문취소 상태의 상품만 삭제 가능합니다.');

                $sql = " select od_no from shop_order where od_id = ? order by index_no ";
                $param = array($od_id);
                $res = $this->query_model->returnArr($sql, $param);
                foreach ($res as $key => $row) {
                    $this->funcomplex_lib->order_delete($row['od_no'], $od_id); // 주문서 삭제
                }
            }
        } else if ($_POST['act_button'] == "운송장번호수정") {
            $delivery = $this->input->post('delivery');
            $delivery_no = $this->input->post('delivery_no');
            $od_no = $this->input->post('od_no');
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $param['delivery'] = $delivery[$k];
                $param['delivery_no'] = $delivery_no[$k];
                $this->query_model->updateObj('shop_order', $param, "od_no = '{$od_no[$k]}'");
            }
        } else {
            alert();
        }

        goto_url(base_url() . "admin/order?$q1&page=$page");
    }
    public function order_timesale_excel()
    {

        $gs_id = $this->input->get('gs_id');

        $result = $this->global_lib->get_timesale_list_excel($gs_id);
        // print_r($result);
        // exit;

        if (empty($result)) {
            alert("출력할 자료가 없습니다.");
        }

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '구매순서')
            ->setCellValue($char++ . '1', '상품명')
            ->setCellValue($char++ . '1', '상품코드')
            ->setCellValue($char++ . '1', '판매시작일자')
            ->setCellValue($char++ . '1', '판매종료일자')
            ->setCellValue($char++ . '1', '주문번호')
            ->setCellValue($char++ . '1', '주문일시')
            ->setCellValue($char++ . '1', '아이디')
            ->setCellValue($char++ . '1', '이름')
            ->setCellValue($char++ . '1', '결제금액')
            ->setCellValue($char++ . '1', '판매가(온라인최저가)')
            ->setCellValue($char++ . '1', '실제 구매금액')
            ->setCellValue($char++ . '1', '예치금(차액)');
        $i = 2;

        foreach ($result as $key => $row) {
            $char = 'A';

            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $key + 1, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gname'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['gcode'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['sb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['eb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['od_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['receipt_time'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['mb_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['normal_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['goods_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['real_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++ . $i, $row['difference'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('주문내역');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="bid 현황-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function order_delivery_excel()
    {

        $sql = " select * from shop_order where dan = '3' order by od_time desc, index_no asc ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result = $resultObj->result_array();
        if (!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++ . '1', '판매자ID')
            ->setCellValue($char++ . '1', '주문번호')
            ->setCellValue($char++ . '1', '일련번호')
            ->setCellValue($char++ . '1', '상품명')
            ->setCellValue($char++ . '1', '주문자')
            ->setCellValue($char++ . '1', '주문자전화1')
            ->setCellValue($char++ . '1', '주문자전화2')
            ->setCellValue($char++ . '1', '수령자')
            ->setCellValue($char++ . '1', '수령지전화1')
            ->setCellValue($char++ . '1', '수령지전화2')
            ->setCellValue($char++ . '1', '수령지우편번호')
            ->setCellValue($char++ . '1', '수령지주소')
            ->setCellValue($char++ . '1', '배송회사')
            ->setCellValue($char++ . '1', '운송장번호');

        $i = 2;
        foreach ($result as $key => $row) {
            $gs = unserialize($row['od_goods']);
            $gs['gname'] = isset($gs['gname']) ? $gs['gname'] : "";
            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++ . $i, $row['seller_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['od_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['od_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $gs['gname'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['telephone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['cellphone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['b_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['b_telephone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['b_cellphone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $row['b_zip'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, $this->funcomplex_lib->print_address($row['b_addr1'], $row['b_addr2'], $row['b_addr3'], $row['b_addr_jibeon']), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, '', PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++ . $i, '', PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('엑셀배송처리');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="엑셀배송처리-' . date("ymd", time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function order_memo_delete()
    {
        $chk = $this->input->post('chk');
        $od_id = $this->input->post('od_id');
        $page = $this->input->post('page');
        $act_button = $this->input->post('act_button');
        $count = isset($chk) ? count($chk) : 0;
        $q1 = $this->input->post("q1");
        if (!$count) {
            alert($act_button . " 하실 항목을 하나 이상 체크하세요.");
        }

        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $chk[$i];
            $sql = " update shop_order set shop_memo = '' where od_id =?";
            $param = array($od_id[$k]);
            $this->query_model->returnNull($sql, $param);
        }

        goto_url(base_url() . "admin/order?$q1&page=$page");
    }
    #sms 함수 시작
    public function sms_seller_update()
    {
        $sms = $this->global_lib->get_sms('admin');
        if (!$sms['cf_sms_use']) {
            alert_close('문자서비스를 사용가능한 설정 상태가 아닙니다.');
        }

        $sms_content = trim($_POST['sms_content']);

        $sql = "select info_tel from shop_seller where state='1'";
        $result = $this->query_model->returnArr($sql);

        $recv_number = '';
        $count = 0;
        foreach ($result as $key => $row) {
            $recv_number .= $row['cellphone'] . ',';
            $count++;
        }
        $this->funcomplex_lib->moonjaro_direct_sms_send('admin', $recv_number, $sms_content, $count);

        alert("전송되었습니다.", "./sms_seller.php");
    }
    public function sms_update()
    {
        // 회신번호 체크
        if (!check_vaild_callback($_POST['cf_sms_recall']))
            alert('발신번호가 올바르지 않습니다.');

        $userinfo = get_moonjaro_userinfo($_POST['cf_moonjaro_key']);

        if (!$userinfo['code'])
            alert('문자로 아이디와 패스워드가 맞지 않습니다.');


        $cf_sms_use = $this->input->post('cf_sms_use');
        $cf_sms_type = $this->input->post('cf_sms_type');
        $cf_sms_recall = $this->input->post('cf_sms_recall');
        $cf_moonjaro_key = $this->input->post('cf_moonjaro_key');
        $cf_cont1 = $this->input->post('cf_cont1');
        $cf_cont2 = $this->input->post('cf_cont2');
        $cf_cont3 = $this->input->post('cf_cont3');
        $cf_cont4 = $this->input->post('cf_cont4');
        $cf_cont5 = $this->input->post('cf_cont5');
        $cf_cont6 = $this->input->post('cf_cont6');
        $cf_mb_use1 = $this->input->post('cf_mb_use1');
        $cf_ad_use1 = $this->input->post('cf_ad_use1');
        $cf_re_use1 = $this->input->post('cf_re_use1');
        $cf_sr_use1 = $this->input->post('cf_sr_use1');
        $cf_mb_use2 = $this->input->post('cf_mb_use2');
        $cf_ad_use2 = $this->input->post('cf_ad_use2');
        $cf_re_use2 = $this->input->post('cf_re_use2');
        $cf_sr_use2 = $this->input->post('cf_sr_use2');
        $cf_mb_use3 = $this->input->post('cf_mb_use3');
        $cf_ad_use3 = $this->input->post('cf_ad_use3');
        $cf_re_use3 = $this->input->post('cf_re_use3');
        $cf_sr_use3 = $this->input->post('cf_sr_use3');
        $cf_mb_use4 = $this->input->post('cf_mb_use4');
        $cf_ad_use4 = $this->input->post('cf_ad_use4');
        $cf_re_use4 = $this->input->post('cf_re_use4');
        $cf_sr_use4 = $this->input->post('cf_sr_use4');
        $cf_mb_use5 = $this->input->post('cf_mb_use5');
        $cf_ad_use5 = $this->input->post('cf_ad_use5');
        $cf_re_use5 = $this->input->post('cf_re_use5');
        $cf_sr_use5 = $this->input->post('cf_sr_use5');
        $cf_mb_use6 = $this->input->post('cf_mb_use6');
        $cf_ad_use6 = $this->input->post('cf_ad_use6');
        $cf_re_use6 = $this->input->post('cf_re_use6');
        $cf_sr_use6 = $this->input->post('cf_sr_use6');

        unset($value);
        $value['mb_id'] = 'admin';
        $value['cf_sms_use'] = isset($cf_sms_use) ? $cf_sms_use : "0";
        $value['cf_sms_type'] = isset($cf_sms_type) ? $cf_sms_type : "";
        $value['cf_sms_recall']    = isset($cf_sms_recall) ? $cf_sms_recall : "";
        $value['cf_moonjaro_key'] = isset($cf_moonjaro_key) ? $cf_moonjaro_key : "";
        $value['cf_cont1'] = isset($cf_cont1) ? $cf_cont1 : "";
        $value['cf_cont2'] = isset($cf_cont2) ? $cf_cont2 : "";
        $value['cf_cont3'] = isset($cf_cont3) ? $cf_cont3 : "";
        $value['cf_cont4'] = isset($cf_cont4) ? $cf_cont4 : "";
        $value['cf_cont5'] = isset($cf_cont5) ? $cf_cont5 : "";
        $value['cf_cont6'] = isset($cf_cont6) ? $cf_cont6 : "";
        $value['cf_mb_use1'] = isset($cf_mb_use1) ? $cf_mb_use1 : "0";
        $value['cf_ad_use1'] = isset($cf_ad_use1) ? $cf_ad_use1 : "0";
        $value['cf_re_use1'] = isset($cf_re_use1) ? $cf_re_use1 : "0";
        $value['cf_sr_use1'] = isset($cf_sr_use1) ? $cf_sr_use1 : "0";
        $value['cf_mb_use2'] = isset($cf_mb_use2) ? $cf_mb_use2 : "0";
        $value['cf_ad_use2'] = isset($cf_ad_use2) ? $cf_ad_use2 : "0";
        $value['cf_re_use2'] = isset($cf_re_use2) ? $cf_re_use2 : "0";
        $value['cf_sr_use2'] = isset($cf_sr_use2) ? $cf_sr_use2 : "0";
        $value['cf_mb_use3'] = isset($cf_mb_use3) ? $cf_mb_use3 : "0";
        $value['cf_ad_use3'] = isset($cf_ad_use3) ? $cf_ad_use3 : "0";
        $value['cf_re_use3'] = isset($cf_re_use3) ? $cf_re_use3 : "0";
        $value['cf_sr_use3'] = isset($cf_sr_use3) ? $cf_sr_use3 : "0";
        $value['cf_mb_use4'] = isset($cf_mb_use4) ? $cf_mb_use4 : "0";
        $value['cf_ad_use4'] = isset($cf_ad_use4) ? $cf_ad_use4 : "0";
        $value['cf_re_use4'] = isset($cf_re_use4) ? $cf_re_use4 : "0";
        $value['cf_sr_use4'] = isset($cf_sr_use4) ? $cf_sr_use4 : "0";
        $value['cf_mb_use5'] = isset($cf_mb_use5) ? $cf_mb_use5 : "0";
        $value['cf_ad_use5'] = isset($cf_ad_use5) ? $cf_ad_use5 : "0";
        $value['cf_re_use5'] = isset($cf_re_use5) ? $cf_re_use5 : "0";
        $value['cf_sr_use5'] = isset($cf_sr_use5) ? $cf_sr_use5 : "0";
        $value['cf_mb_use6'] = isset($cf_mb_use6) ? $cf_mb_use6 : "0";
        $value['cf_ad_use6'] = isset($cf_ad_use6) ? $cf_ad_use6 : "0";
        $value['cf_re_use6'] = isset($cf_re_use6) ? $cf_re_use6 : "0";
        $value['cf_sr_use6'] = isset($cf_sr_use6) ? $cf_sr_use6 : "0";

        $res = $this->global_lib->get_sms('admin');
        if (!$res)
            $this->query_model->insertObj("shop_sms", $value);
        else
            $this->query_model->updateObj("shop_sms", $value, "mb_id = 'admin'");

        goto_url(base_url() . 'admin/config?code=sms');
    }
    #sms 함수 끝
    #고객관리 함수 시작
    public function help_qa_delete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $index_no = trim($_POST['index_no'][$k]);

            // 삭제
            $sql = "delete from shop_qa where index_no =?";
            $param = array($index_no);
            $this->query_model->returnNull($sql, $param);
        }

        goto_url(base_url() . "admin/help?$q1&page=$page");
    }
    public function help_qa_form_update()
    {
        include_once("./lib/mailer.lib.php");
        $w = $this->input->post('w');
        $index_no = $this->input->post('index_no');
        $page = $this->input->post('page');

        $config = $this->load->get_var('config');
        $qstr = $this->load->get_var('qstr');

        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

        if ($w == "u") {
            $sql = "select * from shop_qa where index_no=?";
            $param = array($index_no);
            $qa = $this->query_model->returnOneArr($sql, $param);
            if (!$qa['result_yes']) { // 답변전
                if ($qa['email_send_yes'] && $_POST['reply']) {

                    $subject = '[1:1문의 답변 알림 메일] ' . $config['company_name'];
                    mailer($config['company_name'], $super['email'], $qa['email'], $subject, $_POST['reply'], 1);
                }

                if ($qa['sms_send_yes'] && $qa['cellphone']) {
                    $content = '1:1문의에 답변이 등록되었습니다. ' . $config['company_name'];
                    $this->funcomplex_lib->moonjaro_direct_sms_send('admin', $qa['cellphone'], $content);
                }
            }

            unset($value);
            $value['reply'] = $this->input->post('reply');
            $value['replyer'] = $this->input->post('replyer');
            $value['result_yes'] = 1;
            $value['result_date'] = $OM_TIME_YMDHIS;
            $this->query_model->updateObj("shop_qa", $value, "index_no='$index_no'");

            goto_url(base_url() . "admin/help?code=qa_form&w=u&index_no=$index_no$qstr&page=$page");
        }
    }
    public function help_faq_group_update()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = isset($_POST['chk']) ? count($_POST['chk']) : 0;
        if ($_POST['act_button'] != "추가" && !$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "추가") {
            $catename = $this->input->post('catename');
            $sql = " insert shop_faq_cate set catename = ? ";
            $param = array($catename);
            $this->query_model->returnNull($sql, $param);
        } else if ($_POST['act_button'] == "선택수정") {
            $catename = $this->input->post('catename');
            $index_no = $this->input->post('index_no');
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = " update shop_faq_cate 
                                set catename = ?
                              where index_no = ?";
                $param = array($catename[$k], $index_no[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            $index_no = $this->input->post('index_no');
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = " delete from shop_faq_cate where index_no = ?";
                $param = array($index_no[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/help?$q1&page=$page");
    }
    public function help_faq_form_update()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $w = $this->input->post('w');
        $index_no = $this->input->post('index_no');
        $page = $this->input->post('page');

        $qstr = $this->load->get_var('qstr');
        unset($value);
        $value['cate']      = $_POST['faq_cate'];
        $value['subject'] = $_POST['subject'];
        $value['memo']      = $_POST['memo'];

        if ($w == "") {
            $value['wdate'] = $OM_TIME_YMDHIS;
            $index_no = $this->query_model->insertObj("shop_faq", $value);
            goto_url(base_url() . "admin/help?code=faq_form&w=u&index_no=$index_no");
        } else if ($w == "u") {
            $this->query_model->updateObj("shop_faq", $value, "index_no='$index_no'");

            goto_url(base_url() . "admin/help?code=faq_form&w=u&index_no=$index_no$qstr&page=$page");
        }
    }
    public function help_faq_delete()
    {
        $count = count($_POST['chk']);
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $index_no = trim($_POST['index_no'][$k]);

            // 삭제
            $sql = "select memo from shop_faq where index_no=?";
            $param = array($index_no);
            $faq = $this->query_model->returnOneArr($sql, $param);
            $this->funcomplex_lib->delete_editor_image($faq['memo']);

            $sql = "delete from shop_faq where index_no = ?";
            $param = array($index_no);
            $this->query_model->returnNull($sql, $param);
        }

        goto_url(base_url() . "admin/help?$q1&page=$page");
    }
    public function help_leave_delete()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        for ($i = 0; $i < $count; $i++) {
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $index_no = trim($_POST['index_no'][$k]);

            // 삭제
            $sql = "delete from shop_member_leave where index_no = ?";
            $param = array($index_no);
            $this->query_model->returnNull($sql, $param);
        }
        goto_url(base_url() . "admin/help?$q1&page=$page");
    }
    #고객관리 함수 끝
    #디자인 관리 홤수 시작
    public function banner_form_update()
    {
        $uploadPath = "./data/banner";
        $super = $this->load->get_var('super');
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

        $w = $this->input->post('w');
        $bn_id = $this->input->post('bn_id');
        $qstr = $this->load->get_var('qstr');
        $page = $this->input->post('page');
        unset($value);
        $bn_file_del = $this->input->post('bn_file_del');
        if ($bn_file_del) {
            if (is_file($uploadPath . '/' . $bn_file_del)) {
                unlink($uploadPath . '/' . $bn_file_del);
            }
            $value['bn_file'] = '';
        }
        $error = "";
        if (isset($_FILES['bn_file']['name']) && $_FILES['bn_file']['name'] != "") {
            if (!$this->upload->do_upload('bn_file')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['bn_file'] = $new_file;
            }
        }
        if ($error != "") {
            echo "<script>alert('" . $error . "');</script>";
        }

        $value['mb_id']        = 'admin';
        $value['bn_device'] = 'pc';
        $value['bn_theme']    = $super['theme'];
        $value['bn_code']    = $_POST['bn_code'];
        $value['bn_link']    = $_POST['bn_link'];
        $value['bn_target'] = $_POST['bn_target'];
        $value['bn_width']    = $_POST['bn_width'];
        $value['bn_height'] = $_POST['bn_height'];
        $value['bn_bg']        = preg_replace("/([^a-zA-Z0-9])/", "", $_POST['bn_bg']);
        $value['bn_text']    = $_POST['bn_text'] ?? "";
        $value['bn_use']    = $_POST['bn_use'];
        $value['bn_order']    = $_POST['bn_order'];
        if ($w == "") {
            $bn_id = $this->query_model->insertObj("shop_banner", $value);
            goto_url(base_url() . "admin/design?code=banner_form&w=u&bn_id=$bn_id");
        } else if ($w == "u") {
            $this->query_model->updateObj("shop_banner", $value, "bn_id='$bn_id'");

            goto_url(base_url() . "admin/design?code=banner_form&w=u&bn_id=$bn_id$qstr&page=$page");
        }
    }
    public function banner_list_update()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');

        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "선택수정") {
            $bn_use = $this->input->post('bn_use');
            $bn_width = $this->input->post('bn_width');
            $bn_height = $this->input->post('bn_height');
            $bn_target = $this->input->post('bn_target');
            $bn_link = $this->input->post('bn_link');
            $bn_order = $this->input->post('bn_order');
            $bn_id = $this->input->post('bn_id');
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $param['bn_use'] = isset($bn_use[$k]) ? $bn_use[$k] : "";
                $param['bn_width'] = isset($bn_width[$k]) ? $bn_width[$k] : "";
                $param['bn_height'] = isset($bn_height[$k]) ? $bn_height[$k] : "";
                $param['bn_target'] = isset($bn_target[$k]) ? $bn_target[$k] : "";
                $param['bn_link'] = isset($bn_link[$k]) ? $bn_link[$k] : "";
                $param['bn_order'] = isset($bn_order[$k]) ? $bn_order[$k] : "";
                $this->query_model->updateObj('shop_banner', $param, "bn_id = '{$bn_id[$k]}'");
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            $upl_dir = "./data/banner";
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $bn_id = trim($_POST['bn_id'][$k]);
                $sql = "select * from shop_banner where bn_id=?";
                $param = array($bn_id);
                $row = $this->query_model->returnOneArr($sql, $param);

                unlink($upl_dir . '/' . $row['bn_file']);
                $sql2 = "delete from shop_banner where bn_id =?";
                $this->query_model->returnNull($sql2, $param);
            }
        }
        goto_url(base_url() . "admin/design?$q1&page=$page");
    }
    public function mbanner_list_update()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "선택수정") {
            $bn_use = $this->input->post('bn_use');
            $bn_width = $this->input->post('bn_width');
            $bn_height = $this->input->post('bn_height');
            $bn_target = $this->input->post('bn_target');
            $bn_link = $this->input->post('bn_link');
            $bn_order = $this->input->post('bn_order');
            $bn_id = $this->input->post('bn_id');
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $param['bn_use'] = isset($bn_use[$k]) ? $bn_use[$k] : "";
                $param['bn_width'] = isset($bn_width[$k]) ? $bn_width[$k] : "";
                $param['bn_height'] = isset($bn_height[$k]) ? $bn_height[$k] : "";
                $param['bn_target'] = isset($bn_target[$k]) ? $bn_target[$k] : "";
                $param['bn_link'] = isset($bn_link[$k]) ? $bn_link[$k] : "";
                $param['bn_order'] = isset($bn_order[$k]) ? $bn_order[$k] : "";
                $this->query_model->updateObj("shop_banner", $param, "bn_id = '{$bn_id[$k]}'");
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            $upl_dir = "./data/banner";

            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $bn_id = trim($_POST['bn_id'][$k]);
                $sql = "select * from shop_banner where bn_id=?";
                $param = array($bn_id);
                $row = $this->query_model->returnOneArr($sql, $param);

                unlink($upl_dir . '/' . $row['bn_file']);
                $sql = "delete from shop_banner where bn_id = ?";
                $param = array($bn_id);
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/design?$q1&page=$page");
    }
    public function mbanner_form_update()
    {

        $uploadPath = "./data/banner";
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

        $bn_id = $this->input->post('bn_id');
        $qstr = $this->load->get_var('qstr');
        $super = $this->load->get_var('super');
        $page = $this->input->post('page');
        $w = $this->input->post('w');
        $bn_file_del = $this->input->post('bn_file_del');

        unset($value);
        if (!empty($bn_file_del)) {
            unlink($uploadPath . '/' . $bn_file_del);
            $value['bn_file'] = '';
        }
        $error = "";
        if (isset($_FILES['bn_file']['name']) && $_FILES['bn_file']['name'] != "") {
            if (!$this->upload->do_upload('bn_file')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['bn_file'] = $new_file;
            }
        }
        if ($error != "") {
            echo "<script>alert('" . strip_tags($error) . "')</script>";
        }

        $value['mb_id']        = 'admin';
        $value['bn_device']    = 'mobile';
        $value['bn_theme']    = $super['mobile_theme'];
        $value['bn_code']    = $_POST['bn_code'];
        $value['bn_link']    = $_POST['bn_link'];
        $value['bn_target'] = $_POST['bn_target'];
        $value['bn_width']    = $_POST['bn_width'];
        $value['bn_height']    = $_POST['bn_height'];
        $value['bn_bg']        = preg_replace("/([^a-zA-Z0-9])/", "", $_POST['bn_bg']);
        $value['bn_text']    = $_POST['bn_text'] ?? "";
        $value['bn_use']    = $_POST['bn_use'];
        $value['bn_order']    = $_POST['bn_order'];

        if ($w == "") {
            $bn_id = $this->query_model->insertObj("shop_banner", $value);
            goto_url(base_url() . "admin/design?code=mbanner_form&w=u&bn_id=$bn_id");
        } else if ($w == "u") {
            $this->query_model->updateObj("shop_banner", $value, "bn_id='$bn_id'");

            goto_url(base_url() . "admin/design?code=mbanner_form&w=u&bn_id=$bn_id$qstr&page=$page");
        }
    }
    public function logo_update()
    {
        $mb_id = $this->input->post('mb_id');
        $basic_logo_del = $this->input->post('basic_logo_del');
        $mobile_logo_del = $this->input->post('mobile_logo_del');
        $sns_logo_del = $this->input->post('sns_logo_del');
        $favicon_ico_del = $this->input->post('favicon_ico_del');

        $uploadPath = "./data/banner";
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

        unset($value);
        if ($ico = $_FILES['favicon_ico']['name']) {
            if (!preg_match("/(\.ico)$/i", $ico))
                alert("파비콘 아이콘은 ico 파일만 업로드 가능합니다.");
        }
        $sql = "select * from shop_logo where mb_id = ?";
        $param = array($mb_id);
        $lg = $this->query_model->returnOneArr($sql, $param);

        if ($basic_logo_del) {
            unlink($uploadPath . '/' . $lg['basic_logo']);
            $value['basic_logo'] = '';
        }
        if ($mobile_logo_del) {
            unlink($uploadPath . '/' . $lg['mobile_logo']);
            $value['mobile_logo'] = '';
        }
        if ($sns_logo_del) {
            unlink($uploadPath . '/' . $lg['sns_logo']);
            $value['sns_logo'] = '';
        }
        if ($favicon_ico_del) {
            unlink($uploadPath . '/' . $lg['favicon_ico']);
            $value['favicon_ico'] = '';
        }

        $error = "";
        if (isset($_FILES['basic_logo']['name']) && $_FILES['basic_logo']['name'] != "") {
            if (!$this->upload->do_upload('basic_logo')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['basic_logo'] = $new_file;
            }
        }
        if (isset($_FILES['mobile_logo']['name']) && $_FILES['mobile_logo']['name'] != "") {
            if (!$this->upload->do_upload('mobile_logo')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['mobile_logo'] = $new_file;
            }
        }
        if (isset($_FILES['sns_logo']['name']) && $_FILES['sns_logo']['name'] != "") {
            if (!$this->upload->do_upload('sns_logo')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['sns_logo'] = $new_file;
            }
        }
        if (isset($_FILES['favicon_ico']['name']) && $_FILES['favicon_ico']['name'] != "") {
            if (!$this->upload->do_upload('favicon_ico')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['favicon_ico'] = $new_file;
            }
        }

        $value['mb_id'] = $_POST['mb_id'];

        if ($lg['index_no']) {
            $this->query_model->updateObj("shop_logo", $value, "mb_id = '$mb_id'");
        } else {
            $this->query_model->insertObj("shop_logo", $value);
        }

        goto_url(base_url() . "admin/design?code=logo&mb_id={$mb_id}");
    }
    public function contentformupdate()
    {
        unset($value);
        $w = $this->input->post('w');
        $qstr = $this->load->get_var('qstr');
        $page = $this->input->post('page');
        $co_id = $this->input->post('co_id');

        $value['co_subject'] = $_POST['co_subject'];
        $value['co_content'] = $_POST['co_content'];
        $value['co_mobile_content'] = $_POST['co_mobile_content'];

        if ($w == "") {
            $co_id = $this->query_model->insertObj("shop_content", $value);
            goto_url(base_url() . "admin/design?code=contentform&w=u&co_id=$co_id");
        } else if ($w == "u") {
            $this->query_model->updateObj("shop_content", $value, "co_id='$co_id'");
            goto_url(base_url() . "admin/design?code=contentform&w=u&co_id=$co_id$qstr&page=$page");
        }
    }
    public function menu_form_update()
    {
        $gw_menu = $this->config->item('gw_menu');
        unset($value);
        for ($i = 0; $i < count($gw_menu); $i++) {
            $seq = ($i + 1);
            $_POST['de_pname_use_' . $seq] = isset($_POST['de_pname_use_' . $seq]) ? $_POST['de_pname_use_' . $seq] : "";
            $value['de_pname_use_' . $seq] = $_POST['de_pname_use_' . $seq];
            $value['de_pname_' . $seq]     = $_POST['de_pname_' . $seq];
        }
        $this->query_model->updateObj('shop_default', $value);

        goto_url(base_url() . 'admin/design?code=menu_form');
    }
    public function best_item_update()
    {
        $arr_best = array();
        $maintype_subj_arr = $this->input->post('maintype_subj');
        $maintype_code_arr = $this->input->post('maintype_code');
        $de_maintype_title = $this->input->post('de_maintype_title');
        $del_img01 = $this->input->post('del_img01');
        $del_img02 = $this->input->post('del_img02');

        $default = $this->load->get_var('default');
        $list = unserialize(base64_decode($default['de_maintype_best']));

        for ($i = 0; $i < count($maintype_subj_arr); $i++) {
            $list[$i]['subj'] = trim($maintype_subj_arr[$i]);
            $list[$i]['code'] = preg_replace("/\s+/", "", $maintype_code_arr[$i]);

            if (isset($del_img01[$i])) {
                delete_category_thumbnail($del_img01[$i]);
                $list[$i]['logo_01'] = "";
            }

            if (isset($del_img02[$i])) {
                delete_category_thumbnail($del_img02[$i]);
                $list[$i]['logo_02'] = "";
            }
        }

        $this->load->library('upload');
        $uploaded_files = $_FILES;
        $uploaded_file_count = count($_FILES['maintype_logo_01']['name']);
        $uploaded_file_count2 = count($_FILES['maintype_logo_02']['name']);

        for ($i = 0; $i < $uploaded_file_count; $i++) {
            if ($uploaded_files['maintype_logo_01']['name'][$i] == null) continue;

            unset($_FILES);

            $_FILES['maintype_logo_01']['name'] = $uploaded_files['maintype_logo_01']['name'][$i];
            $_FILES['maintype_logo_01']['type'] = $uploaded_files['maintype_logo_01']['type'][$i];
            $_FILES['maintype_logo_01']['tmp_name'] = $uploaded_files['maintype_logo_01']['tmp_name'][$i];
            $_FILES['maintype_logo_01']['error'] = $uploaded_files['maintype_logo_01']['error'][$i];
            $_FILES['maintype_logo_01']['size'] = $uploaded_files['maintype_logo_01']['size'][$i];

            $list[$i]['logo_01'] = $uploaded_files['maintype_logo_01']['name'][$i];

            $upload_config = array(
                'upload_path' => './data/logo',
                'allowed_types' => 'gif|jpg|jpeg|png',
                'max_size' => '10240'
            );

            $this->upload->initialize($upload_config);
            if (! $this->upload->do_upload('maintype_logo_01')) {
                echo $this->upload->display_errors();
            }
        }

        for ($i = 0; $i < $uploaded_file_count2; $i++) {
            if ($uploaded_files['maintype_logo_02']['name'][$i] == null) continue;

            unset($_FILES);

            $_FILES['maintype_logo_02']['name'] = $uploaded_files['maintype_logo_02']['name'][$i];
            $_FILES['maintype_logo_02']['type'] = $uploaded_files['maintype_logo_02']['type'][$i];
            $_FILES['maintype_logo_02']['tmp_name'] = $uploaded_files['maintype_logo_02']['tmp_name'][$i];
            $_FILES['maintype_logo_02']['error'] = $uploaded_files['maintype_logo_02']['error'][$i];
            $_FILES['maintype_logo_02']['size'] = $uploaded_files['maintype_logo_02']['size'][$i];

            $list[$i]['logo_02'] = $uploaded_files['maintype_logo_02']['name'][$i];

            $upload_config = array(
                'upload_path' => './data/logo',
                'allowed_types' => 'gif|jpg|jpeg|png',
                'max_size' => '10240'
            );

            $this->upload->initialize($upload_config);
            if (! $this->upload->do_upload('maintype_logo_02')) {
                echo $this->upload->display_errors();
            }
        }

        $de_maintype_best = base64_encode(serialize($list));

        unset($value);
        $value['de_maintype_title'] = $de_maintype_title;
        $value['de_maintype_best'] = $de_maintype_best;
        $this->query_model->updateObj('shop_default', $value);

        goto_url(base_url() . "admin/goods?code=best_item");
    }

    public function popup_list_update()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "선택수정") {
            $title = $this->input->post('title');
            $device = $this->input->post('device');
            $width = $this->input->post('width');
            $height = $this->input->post('height');
            $top = $this->input->post('top');
            $lefts = $this->input->post('lefts');
            $pp_id = $this->input->post('pp_id');
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $param['title'] = $title[$k];
                $param['device'] = $device[$k];
                $param['width'] = $width[$k];
                $param['height'] = $height[$k];
                $param['top'] = $top[$k];
                $param['lefts'] = $lefts[$k];
                $this->query_model->updateObj('shop_popup', $param, "index_no = '{$pp_id[$k]}'");
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $pp_id = trim($_POST['pp_id'][$k]);

                // 삭제
                $sql = "select memo from shop_popup where index_no=?";
                $param = array($pp_id);
                $row = $this->query_model->returnOneArr($sql, $param);

                $this->funcomplex_lib->delete_editor_image($row['memo']);
                $sql2 = "delete from shop_popup where index_no=?";
                $this->query_model->returnNull($sql2, $param);
            }
        }

        goto_url(base_url() . "admin/design?$q1&page=$page");
    }
    public function popup_form_update()
    {
        $qstr = $this->load->get_var('qstr');
        $page = $this->input->post('page');
        $pp_id = $this->input->post('pp_id');
        $w = $this->input->post('w');
        if (!$_POST['title'])
            alert("제목을 입력하세요.");

        unset($value);
        $value['device']     = $_POST['device'];
        $value['width']         = $_POST['width'];
        $value['height']     = $_POST['height'];
        $value['lefts']         = $_POST['lefts'];
        $value['top']         = $_POST['top'];
        $value['title']         = $_POST['title'];
        $value['state']         = $_POST['state'];
        $value['begin_date'] = $_POST['begin_date'];
        $value['end_date']     = $_POST['end_date'];
        $value['memo']         = $_POST['memo'];

        if ($w == "") {
            $value['mb_id'] = 'admin';
            $pp_id = $this->query_model->insertObj("shop_popup", $value);
            goto_url(base_url() . "admin/design?code=popup_form&w=u&pp_id=$pp_id");
        } else if ($w == "u") {
            $this->query_model->updateObj("shop_popup", $value, "index_no='$pp_id'");
            goto_url(base_url() . "admin/design?code=popup_form&w=u&pp_id=$pp_id$qstr&page=$page");
        }
    }
    public function contentlistupdate()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');

        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }

        if ($_POST['act_button'] == "선택수정") {
            $co_subject = $this->input->post('co_subject');
            $co_id = $this->input->post('co_id');
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = " update shop_content set co_subject = ? where co_id = ?";
                $param = array($co_subject[$k], $co_id[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            $co_id = $this->input->post('co_id');
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = " delete from shop_content where co_id = ?";
                $param = array($co_id[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/design?$q1&page=$page");
    }
    #디자인 관리 함수 끝
    #환경설정 관리 함수 시작
    public function default_update()
    {
        $OM_DIR_PERMISSION = 0707;
        $member = $this->load->get_var('member');

        $theme = $this->input->post('theme');
        $mobile_theme = $this->input->post('mobile_theme');
        $de_wish_keep_term = $this->input->post('de_wish_keep_term');
        $de_cart_keep_term = $this->input->post('de_cart_keep_term');
        $de_misu_keep_term = $this->input->post('de_misu_keep_term');
        $de_final_keep_term = $this->input->post('de_final_keep_term');

        if ($member['grade'] == 99) {
            $de_review_wr_use = $this->input->post('de_review_wr_use');
            $de_board_wr_use = $this->input->post('de_board_wr_use');

            $de_logo_wpx = $this->input->post('de_logo_wpx');
            $de_logo_hpx = $this->input->post('de_logo_hpx');
            $de_mobile_logo_wpx = $this->input->post('de_mobile_logo_wpx');
            $de_mobile_logo_hpx = $this->input->post('de_mobile_logo_hpx');
            $de_slider_wpx = $this->input->post('de_slider_wpx');
            $de_slider_hpx = $this->input->post('de_slider_hpx');
            $de_mobile_slider_wpx = $this->input->post('de_mobile_slider_wpx');
            $de_mobile_slider_hpx = $this->input->post('de_mobile_slider_hpx');
            $de_item_small_wpx = $this->input->post('de_item_small_wpx');
            $de_item_small_hpx = $this->input->post('de_item_small_hpx');
            $de_item_medium_wpx = $this->input->post('de_item_medium_wpx');
            $de_item_medium_hpx = $this->input->post('de_item_medium_hpx');

            $admin_shop_url = $this->input->post('admin_shop_url');
            $admin_reg_yes = $this->input->post('admin_reg_yes');
            $admin_reg_msg = $this->input->post('admin_reg_msg');
        }

        $login_point = $this->input->post('login_point');
        $usepoint = $this->input->post('usepoint');
        $usepoint_yes = $this->input->post('usepoint_yes');
        $usemileage = $this->input->post('usemileage');
        $usemileage_yes = $this->input->post('usemileage_yes');
        $mouseblock_yes = $this->input->post('mouseblock_yes');
        $shop_name = $this->input->post('shop_name');
        $shop_name_us = $this->input->post('shop_name_us');
        $company_type = $this->input->post('company_type');
        $company_name = $this->input->post('company_name');
        $company_saupja_no = $this->input->post('company_saupja_no');
        $tongsin_no = $this->input->post('tongsin_no');
        $company_tel = $this->input->post('company_tel');
        $company_fax = $this->input->post('company_fax');
        $company_item = $this->input->post('company_item');
        $company_service = $this->input->post('company_service');
        $company_owner = $this->input->post('company_owner');
        $company_zip = $this->input->post('company_zip');
        $company_addr = $this->input->post('company_addr');
        $company_hours = $this->input->post('company_hours');
        $company_lunch = $this->input->post('company_lunch');
        $company_close = $this->input->post('company_close');
        $info_name = $this->input->post('info_name');
        $info_email = $this->input->post('info_email');
        $shop_intro_yes = $this->input->post('shop_intro_yes');
        $cert_admin_yes = $this->input->post('cert_admin_yes');
        $cert_partner_yes = $this->input->post('cert_partner_yes');
        $coupon_yes = $this->input->post('coupon_yes');
        $gift_yes = $this->input->post('gift_yes');
        $write_pages = $this->input->post('write_pages');
        $mobile_pages = $this->input->post('mobile_pages');
        $admin_seal_del = $this->input->post('admin_seal_del');
        unset($value);
        $value['theme']                    = isset($theme) ? $theme : "basic"; //테마스킨
        $value['mobile_theme']            = isset($mobile_theme) ? $mobile_theme : "basic"; //모바일테마스킨
        $this->query_model->updateObj("shop_member", $value, "id='admin'");

        unset($value);
        $value['de_wish_keep_term']        = $de_wish_keep_term; //찜목록 보관일수
        $value['de_cart_keep_term']        = $de_cart_keep_term; //장바구니 보관일수
        $value['de_misu_keep_term']        = $de_misu_keep_term; //미입금 주문내역
        $value['de_final_keep_term']    = $de_final_keep_term; //자동구매확정일
        if ($member['grade'] == 99) {
            $value['de_review_wr_use']        = $de_review_wr_use; //구매후기 노출
            $value['de_board_wr_use']        = $de_board_wr_use; //게시판글 노출

            $value['de_logo_wpx']            = $de_logo_wpx; //PC 쇼핑몰로고(가로)
            $value['de_logo_hpx']            = $de_logo_hpx; //PC 쇼핑몰로고(세로)
            $value['de_mobile_logo_wpx']    = $de_mobile_logo_wpx; //모바일 쇼핑몰로고(가로)
            $value['de_mobile_logo_hpx']    = $de_mobile_logo_hpx; //모바일 쇼핑몰로고(세로)
            $value['de_slider_wpx']            = $de_slider_wpx; //PC 메인배너(가로)
            $value['de_slider_hpx']            = $de_slider_hpx; //PC 메인배너(세로)
            $value['de_mobile_slider_wpx']    = $de_mobile_slider_wpx; //모바일 메인배너(가로)
            $value['de_mobile_slider_hpx']    = $de_mobile_slider_hpx; //모바일 메인배너(세로)
            $value['de_item_small_wpx']        = $de_item_small_wpx; //상품 소이미지(가로)
            $value['de_item_small_hpx']        = $de_item_small_hpx; //상품 소이미지(세로)
            $value['de_item_medium_wpx']    = $de_item_medium_wpx; //상품 중이미지(가로)
            $value['de_item_medium_hpx']    = $de_item_medium_hpx; //상품 중이미지(세로)
        }
        $this->query_model->updateObj("shop_default", $value);

        $uploadPath = "./data/common";
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

        unset($value);
        if ($admin_seal_del) {
            unlink($uploadPath . '/' . $config['admin_seal']);
            $value['admin_seal'] = '';
        }
        $error = "";
        if (isset($_FILES['admin_seal']['name']) && $_FILES['admin_seal']['name'] != "") {
            if (!$this->upload->do_upload('admin_seal')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['admin_seal'] = $new_file;
            }
        }
        if ($error != "") {
            echo "<script>alert('" . strip_tags($error) . "')</script>";
        }
        $value['login_point']            = conv_number($login_point); //로그인 포인트
        $value['usepoint']                = conv_number($usepoint); //구매시포인트
        $value['usepoint_yes']            = $usepoint_yes; //포인트결제 사용
        $value['usemileage']            = conv_number($usemileage); //예치금
        $value['usemileage_yes']        = $usemileage_yes; //예치금결제 사용

        if ($member['grade'] == 99) {
            $value['admin_shop_url']        = $admin_shop_url; //대표도메인
            $value['admin_reg_yes']            = $admin_reg_yes; //본사몰 회원가입 여부
            $value['admin_reg_msg']            = $admin_reg_msg; //본사몰 회원가입 거부시 출력 메시지
        }

        $value['mouseblock_yes']        = $mouseblock_yes; //마우스차단여부
        $value['shop_name']                = $shop_name; //쇼핑몰명
        $value['shop_name_us']            = $shop_name_us; //쇼핑몰 영문명
        $value['company_type']            = $company_type; //사업자유형
        $value['company_name']            = $company_name; //회사명
        $value['company_saupja_no']        = $company_saupja_no; //사업자등록번호
        $value['tongsin_no']            = $tongsin_no; //통신판매신고번호
        $value['company_tel']            = $company_tel; //대표전화
        $value['company_fax']            = $company_fax; //대표팩스
        $value['company_item']            = $company_item; //업태
        $value['company_service']        = $company_service; //종목
        $value['company_owner']            = $company_owner; //대표자명
        $value['company_zip']            = $company_zip; //사업장우편번호
        $value['company_addr']            = $company_addr; //사업장주소
        $value['company_hours']            = $company_hours; //CS 상담가능시간
        $value['company_lunch']            = $company_lunch; //CS 점심시간
        $value['company_close']            = $company_close; //CS 휴무일
        $value['info_name']                = $info_name; //정보책임자 이름
        $value['info_email']            = $info_email; //정보책임자 e-mail
        $value['shop_intro_yes']        = $shop_intro_yes; // 메인인트로 적용
        $value['cert_admin_yes']        = $cert_admin_yes; //승인 후 로그인
        $value['cert_partner_yes']        = $cert_partner_yes; //회원 승인권한
        $value['coupon_yes']            = $coupon_yes; //쿠폰 (온라인) 사용여부
        $value['gift_yes']                = $gift_yes; //쿠폰 (인쇄용) 사용여부
        $value['write_pages']            = $write_pages; // 페이지 표시 수
        $value['mobile_pages']            = $mobile_pages; // 모바일 페이지 표시 수
        $this->query_model->updateObj("shop_config", $value);


        $goods_dir  = "./data/goods";
        $banner_dir = "./data/banner";
        $brand_dir  = "./data/brand";
        $order_dir    = "./data/order";
        $plan_dir    = "./data/plan";

        // 회원초기화
        if (isset($_POST['clear_1']) && $_POST['clear_1']) {

            // 회원 로고
            $sql = "select * from shop_logo where mb_id!='admin' ";
            $res = $this->query_model->returnArr($sql);
            foreach ($res as $key => $row) {
                if ($row['basic_logo']) {
                    @unlink($banner_dir . "/" . $row['basic_logo']);
                }
                if ($row['mobile_logo']) {
                    @unlink($banner_dir . "/" . $row['mobile_logo']);
                }
                if ($row['sns_logo']) {
                    @unlink($banner_dir . "/" . $row['sns_logo']);
                }
                if ($row['favicon_ico']) {
                    @unlink($banner_dir . "/" . $row['favicon_ico']);
                }
            }
            $this->query_model->returnNull("delete from shop_logo where mb_id!='admin' ");

            // 회원 배너
            $sql = "select * from shop_banner where mb_id!='admin' ";
            $res = $this->query_model->returnArr($sql);
            foreach ($res as $key => $row) {
                if ($row['bn_file']) {
                    @unlink($banner_dir . "/" . $row['bn_file']);
                }
            }
            $this->query_model->returnNull("delete from shop_banner where mb_id!='admin' ");

            // 기획전
            $sql = "select * from shop_goods_plan where mb_id!='admin' ";
            $res = $this->query_model->returnArr($sql);
            foreach ($res as $key => $row) {
                if ($row['pl_limg']) @unlink($plan_dir . '/' . $row['pl_limg']);
                if ($row['pl_bimg']) @unlink($plan_dir . '/' . $row['pl_bimg']);
            }
            $this->query_model->returnNull("delete from shop_goods_plan where mb_id!='admin'");

            $this->query_model->returnNull("delete from shop_member where id !='admin'"); //회원정보 내역
            $this->query_model->returnNull("ALTER TABLE shop_member AUTO_INCREMENT=2");
            $this->query_model->returnNull("delete from shop_member_leave"); //탈퇴내역
            $this->query_model->returnNull("ALTER TABLE shop_member_leave AUTO_INCREMENT=1");

            // 가맹점정보
            $sql = "select * from shop_partner ";
            $res = $this->query_model->returnArr($sql);
            foreach ($res as $key => $row) {
                $this->funcomplex_lib->delete_editor_image($row['baesong_cont1']);
                $this->funcomplex_lib->delete_editor_image($row['baesong_cont2']);
            }

            $this->query_model->returnNull("delete from shop_partner");
            $this->query_model->returnNull("ALTER TABLE shop_partner AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_partner_pay"); //가맹점 수수료 적립내역
            $this->query_model->returnNull("ALTER TABLE shop_partner_pay AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_partner_payrun"); //실시간 출금요청
            $this->query_model->returnNull("ALTER TABLE shop_partner_payrun AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_partner_term"); //관리비연장 신청내역
            $this->query_model->returnNull("ALTER TABLE shop_partner_term AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_point"); //포인트내역
            $this->query_model->returnNull("ALTER TABLE shop_point AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_leave_log"); //추천인변경 로그
            $this->query_model->returnNull("ALTER TABLE shop_leave_log AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_cert_history"); //본인인증
            $this->query_model->returnNull("ALTER TABLE shop_cert_history AUTO_INCREMENT=1");

            // 입점사 정보
            $sql = "select * from shop_seller ";
            $res = $this->query_model->returnArr($sql);
            foreach ($res as $key => $row) {
                $this->funcomplex_lib->delete_editor_image($row['baesong_cont1']);
                $this->funcomplex_lib->delete_editor_image($row['baesong_cont2']);
            }
            $this->query_model->returnNull("delete from shop_seller");
            $this->query_model->returnNull("ALTER TABLE shop_seller AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_seller_cal"); //공급업체 정산내역
            $this->query_model->returnNull("ALTER TABLE shop_seller_cal AUTO_INCREMENT=1");

            // 상품정보
            $sql = "select * from shop_goods where mb_id!='admin' ";
            $res = $this->query_model->returnArr($sql);
            foreach ($res as $key => $row) {
                $dir_list = $goods_dir . '/' . $row['gcode'];

                for ($g = 1; $g <= 6; $g++) {
                    if ($row['simg' . $g]) {
                        @unlink($goods_dir . '/' . $row['simg' . $g]);
                        delete_item_thumbnail($dir_list, $row['simg' . $g]);
                    }
                }

                // 에디터 이미지 삭제
                $this->funcomplex_lib->delete_editor_image($row['memo']);
                $sql1 = "delete from shop_goods_relation where gs_id = ?";
                $sql2 = "delete from shop_goods_relation where gs_id2 = ?";
                $param = array($row['index_no']);
                $this->query_model->returnNull($sql1, $param); // 관련상품
                $this->query_model->returnNull($sql2, $param); // 관련상품
            }
            $this->query_model->returnNull("delete from shop_goods where mb_id!='admin'");
            $this->query_model->returnNull("delete from shop_goods_type where mb_id!='admin'");

            // 팝업삭제
            $sql = "select * from shop_popup where mb_id!='admin' ";
            $res = $this->query_model->returnArr($sql);
            foreach ($res as $key => $row) {
                $this->funcomplex_lib->delete_editor_image($row['memo']);
            }
            $this->query_model->returnNull("delete from shop_popup where mb_id!='admin'");

            $this->query_model->returnNull("delete from shop_goods_qa"); // 상품문의 테이블
            $this->query_model->returnNull("ALTER TABLE shop_goods_qa AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_brand where mb_id!='admin'"); // 브랜드정보
            $this->query_model->returnNull("delete from shop_popular where pt_id!='admin'"); // 검색 키워드
            $this->query_model->returnNull("delete from shop_visit where mb_id!='admin'"); // 접속자집계
            $this->query_model->returnNull("delete from shop_visit_sum where mb_id!='admin'"); // 접속자집계
            $this->query_model->returnNull("delete from shop_sms where mb_id!='admin'"); // 문자설정
        }

        // 주문초기화
        if (isset($_POST['clear_2']) && $_POST['clear_2']) {
            $this->query_model->returnNull("delete from shop_cart"); //장바구니 테이블
            $this->query_model->returnNull("ALTER TABLE shop_cart AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_order"); //주문 테이블
            $this->query_model->returnNull("ALTER TABLE shop_order AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_order_data"); //임시 주문정보테이블
            $this->query_model->returnNull("delete from shop_inicis_log"); //이니시스 결제정보테이블

            $this->funcomplex_lib->rm_rf($order_dir);
            if (!is_dir($order_dir)) {
                @mkdir($order_dir, $OM_DIR_PERMISSION);
                @chmod($order_dir, $OM_DIR_PERMISSION);
            }
        }

        // 가맹점 수수료 초기화
        if (isset($_POST['clear_3']) && $_POST['clear_3']) {
            $this->query_model->returnNull("delete from shop_partner_pay"); //가맹점 수수료 적립내역
            $this->query_model->returnNull("delete from shop_partner_payrun"); //실시간 출금요청
            $this->query_model->returnNull("update shop_member set pay='0'"); //모든회원 및 가맹점 수수료 초기화
            $this->query_model->returnNull("delete from shop_partner_term"); //관리비연장 신청내역

            $this->query_model->returnNull("ALTER TABLE shop_partner_pay AUTO_INCREMENT=1");
            $this->query_model->returnNull("ALTER TABLE shop_partner_payrun AUTO_INCREMENT=1");
            $this->query_model->returnNull("ALTER TABLE shop_partner_term AUTO_INCREMENT=1");
        }

        // 상품초기화
        if (isset($_POST['clear_4']) && $_POST['clear_4']) {
            $this->query_model->returnNull("delete from shop_goods"); //상품 테이블
            $this->query_model->returnNull("ALTER TABLE shop_goods AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_goods_type"); //상품 진열관리
            $this->query_model->returnNull("ALTER TABLE shop_goods_type AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_goods_qa"); // 상품문의 테이블
            $this->query_model->returnNull("ALTER TABLE shop_goods_qa AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_goods_review"); //상품평관리
            $this->query_model->returnNull("ALTER TABLE shop_goods_review AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_cart where ct_select='0'"); //장바구니
            $this->query_model->returnNull("delete from shop_wish"); //찜목록
            $this->query_model->returnNull("ALTER TABLE shop_wish AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_goods_option"); //옵션
            $this->query_model->returnNull("ALTER TABLE shop_goods_option AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_goods_relation"); // 관련상품

            $this->funcomplex_lib->rm_rf($goods_dir);
            if (!is_dir($goods_dir)) {
                @mkdir($goods_dir, $OM_DIR_PERMISSION);
                @chmod($goods_dir, $OM_DIR_PERMISSION);
            }
        }

        // 회원포인트 초기화
        if (isset($_POST['clear_5']) && $_POST['clear_5']) {
            $this->query_model->returnNull("update shop_member set point='0'"); //회원 전체 포인트
            $this->query_model->returnNull("delete from shop_point"); //포인트 내역
            $this->query_model->returnNull("ALTER TABLE shop_point AUTO_INCREMENT=1");
        }

        // 접속통계 초기화
        if (isset($_POST['clear_6']) && $_POST['clear_6']) {
            $this->query_model->returnNull("delete from shop_visit");
            $this->query_model->returnNull("ALTER TABLE shop_visit AUTO_INCREMENT=1");
            $this->query_model->returnNull("delete from shop_visit_sum");
            $this->query_model->returnNull("ALTER TABLE shop_visit_sum AUTO_INCREMENT=1");
        }

        // 검색키워드 초기화
        if (isset($_POST['clear_7']) && $_POST['clear_7']) {
            $this->query_model->returnNull("delete from shop_popular");
            $this->query_model->returnNull("ALTER TABLE shop_popular AUTO_INCREMENT=1");
        }

        // 브랜드 초기화
        if (isset($_POST['clear_8']) && $_POST['clear_8']) {
            $this->query_model->returnNull("delete from shop_brand");
            $this->query_model->returnNull("ALTER TABLE shop_brand AUTO_INCREMENT=1");

            $this->funcomplex_lib->rm_rf($brand_dir);
            if (!is_dir($brand_dir)) {
                @mkdir($brand_dir, $OM_DIR_PERMISSION);
                @chmod($brand_dir, $OM_DIR_PERMISSION);
            }
        }

        goto_url(base_url() . 'admin/config?code=default');
    }
    public function meta_update()
    {
        unset($value);
        $value['head_title']        = $this->input->post('head_title'); //웹브라우져 타이틀
        $value['meta_author']        = $this->input->post('meta_author'); //Author : 메타태그 1
        $value['meta_description']    = $this->input->post('meta_description'); //description : 메타태그 2
        $value['meta_keywords']        = $this->input->post('meta_keywords'); //keywords : 메타태그 3
        $value['add_meta']            = $this->input->post('add_meta'); //추가 메타태그
        $value["head_script"]        = $this->input->post("head_script"); //<HEAD> 내부 태그
        $value["tail_script"]        = $this->input->post("tail_script"); //<BODY> 내부 태그
        $this->query_model->updateObj("shop_config", $value);

        goto_url(base_url() . 'admin/config?code=meta');
    }
    public function register_update()
    {
        unset($value);
        $value['partner_point']      = conv_number($this->input->post('partner_point'));  //추천인 포인트
        $value['register_point']     = conv_number($this->input->post('register_point'));  //회원가입 포인트
        $value['register_use_hp']     = $this->input->post('register_use_hp'); //핸드폰 입력
        $value['register_req_hp']     = $this->input->post('register_req_hp'); //핸드폰 필수입력
        $value['register_use_tel']   = $this->input->post('register_use_tel'); //전화번호 입력
        $value['register_req_tel']   = $this->input->post('register_req_tel'); //전화번호 필수입력
        $value['register_use_addr']  = $this->input->post('register_use_addr'); //주소 입력
        $value['register_req_addr']  = $this->input->post('register_req_addr'); //주소 필수입력
        $value['prohibit_id']         = $this->input->post('prohibit_id'); //아이디 금지단어
        $value['prohibit_email']     = $this->input->post('prohibit_email'); //입력 금지 메일
        $value['shop_provision']     = $this->input->post('shop_provision'); // 회원가입약관
        $value['shop_private']         = $this->input->post('shop_private'); // 개인정보 수집 및 이용
        $value['shop_marketing']     = $this->input->post('shop_marketing'); // 마케팅
        $value['shop_policy']         = $this->input->post('shop_policy'); // 개인정보처리방침
        $this->query_model->updateObj("shop_config", $value);

        goto_url(base_url() . 'admin/config?code=register');
    }
    public function supply_update()
    {
        unset($value);
        $value['seller_reg_yes']          = $this->input->post('seller_reg_yes');
        $value['seller_reg_auto']          = $this->input->post('seller_reg_auto');
        $value['seller_mod_auto']          = $this->input->post('seller_mod_auto');
        $value['seller_reg_agree']          = $this->input->post('seller_reg_agree');
        $value['seller_reg_guide']          = $this->input->post('seller_reg_guide');
        $value['seller_reg_mobile_guide'] = $this->input->post('seller_reg_mobile_guide');
        $this->query_model->updateObj("shop_config", $value);

        goto_url(base_url() . 'admin/config?code=supply');
    }
    public function super_update()
    {
        unset($value);
        $value['name']            = $this->input->post('name');
        $value['email']            = $this->input->post('email');
        $value['cellphone']        = replace_tel($this->input->post('cellphone'));
        $value['zip']            = $this->input->post('zip');
        $value['addr1']            = $this->input->post('addr1');
        $value['addr2']            = $this->input->post('addr2');
        $value['addr3']            = $this->input->post('addr3');
        $value['addr_jibeon']    = $this->input->post('addr_jibeon');
        $value['mailser']        = $this->input->post('mailser');
        $value['smsser']        = $this->input->post('smsser');

        if ($_POST['passwd']) {
            $passwd = trim($_POST['passwd']);
            $passwd_re = trim($_POST['passwd_re']);
            if ($msg = valid_mb_password($passwd)) {
                alert($msg);
            }
            if ($passwd != $passwd_re) {
                alert('비밀번호가 일치하지 않습니다.');
            }
            $value['passwd'] = $passwd;
        }

        $this->query_model->updateObj("shop_member", $value, "id='admin'");

        goto_url(base_url() . 'admin/config?code=super');
    }
    public function pg_update()
    {
        $adm_bank = array();
        for ($i = 0; $i < count($_POST['bank_name']); $i++) {
            if (!trim($_POST['bank_name'][$i])) continue;

            $adm_bank[$i]['name'] = trim($_POST['bank_name'][$i]);
            $adm_bank[$i]['account'] = trim($_POST['bank_account'][$i]);
            $adm_bank[$i]['holder'] = trim($_POST['bank_holder'][$i]);
        }

        unset($value);
        $value['de_bank_account']        = serialize($adm_bank);
        $value['de_bank_use']            = $this->input->post('de_bank_use'); // 무통장입금
        $value['de_card_use']            = $this->input->post('de_card_use'); // 신용카드
        $value['de_iche_use']            = $this->input->post('de_iche_use'); // 계좌이체
        $value['de_vbank_use']            = $this->input->post('de_vbank_use'); // 가상계좌
        $value['de_kakao_use']            = $this->input->post('de_kakao_use'); // 카카오결제
        $value['de_hp_use']                = $this->input->post('de_hp_use'); // 휴대폰
        $value['de_card_test']            = $this->input->post('de_card_test'); // 결제 테스트
        $value['de_pg_service']            = $this->input->post('de_pg_service'); // 결제대행사
        $value['de_tax_flag_use']        = $this->input->post('de_tax_flag_use'); // 복합과세 결제
        $value['de_taxsave_use']        = $this->input->post('de_taxsave_use'); // 현금영수증 발급사용
        $value['de_card_noint_use']        = $this->input->post('de_card_noint_use'); // 신용카드 무이자할부사용
        $value['de_easy_pay_use']        = $this->input->post('de_easy_pay_use'); // PG사 간편결제 버튼사용
        $value['de_escrow_use']            = $this->input->post('de_escrow_use'); // Escrow 사용여부
        $value['de_kcp_mid']            = $this->input->post('de_kcp_mid'); // NHN KCP SITE CODE
        $value['de_kcp_site_key']        = $this->input->post('de_kcp_site_key'); // NHN KCP SITE KEY
        $value['de_lg_mid']                = $this->input->post('de_lg_mid'); // LG유플러스 상점아이디
        $value['de_lg_mert_key']        = $this->input->post('de_lg_mert_key'); // LG유플러스 MertKey
        $value['de_inicis_mid']            = $this->input->post('de_inicis_mid'); // KG이니시스 상점아이디
        $value['de_inicis_admin_key']    = $this->input->post('de_inicis_admin_key'); // KG이니시스 키패스워드
        $value['de_inicis_sign_key']    = $this->input->post('de_inicis_sign_key'); // KG이니시스 웹결제 사인키
        $value['de_samsung_pay_use']    = $this->input->post('de_samsung_pay_use'); // KG이니시스 삼성페이 버튼
        $this->query_model->updateObj("shop_default", $value);
        goto_url(base_url() . 'admin/config?code=pg');
    }
    public function kakaopay_update()
    {
        $pf_auth_pg = $this->input->post('pf_auth_pg');
        if (!$pf_auth_pg)
            alert('개별 PG결제 사용 권한이 없습니다.');

        unset($value);
        $value['de_kakaopay_admin_key']        = $this->input->post('de_kakaopay_admin_key'); // 카카오페이 Admin키
        $value['de_kakaopay_cid']        = $this->input->post('de_kakaopay_cid'); // 카카오페이 CID
        $this->query_model->updateObj("shop_partner", $value, "mb_id='$member[id]'");

        goto_url(base_url() . 'mypage/page?code=partner_kakaopay');
    }
    public function naverpay_update()
    {
        unset($value);
        $value['de_naverpay_mid']         = $this->input->post('de_naverpay_mid');
        $value['de_naverpay_cert_key']     = $this->input->post('de_naverpay_cert_key');
        $value['de_naverpay_button_key'] = $this->input->post('de_naverpay_button_key');
        $value['de_naverpay_test']         = $this->input->post('de_naverpay_test');
        $value['de_naverpay_mb_id']         = $this->input->post('de_naverpay_mb_id');
        $value['de_naverpay_sendcost']     = $this->input->post('de_naverpay_sendcost');
        $this->query_model->updateObj("shop_default", $value);

        goto_url(base_url() . 'admin/config?code=naverpay');
    }
    public function baesong_update() {
        $delivery_company = '';
        $spl_name = isset($_POST['spl_name']) ? $_POST['spl_name'] : array();
        $spl_url = isset($_POST['spl_url']) ? $_POST['spl_url'] : array();
        $delivery_count = count($spl_name);
    
        if ($delivery_count) {
            $arr_spl = array();
            for ($i = 0; $i < $delivery_count; $i++) {
                $spl_val = trim($spl_name[$i]) . '|' . trim($spl_url[$i]);
                $arr_spl[] = $spl_val;
            }
            $delivery_company = implode(',', $arr_spl);
        }
    
        $value = array();
        $value['delivery_company']   = $delivery_company;
        $value['delivery_method']    = $this->input->post('delivery_method');
        $value['delivery_price']     = conv_number($this->input->post('delivery_price'));
        $value['delivery_price2']    = conv_number($this->input->post('delivery_price2'));
        $value['delivery_minimum']   = conv_number($this->input->post('delivery_minimum'));
        $value['baesong_cont1']      = $this->input->post('baesong_cont1');
        $value['baesong_cont2']      = $this->input->post('baesong_cont2');
    
        $this->query_model->updateObj("shop_config", $value);
    
        goto_url(base_url() . 'admin/config?code=baesong');
    }
    
    public function islandlistupdate()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');
        $chk = $this->input->post('chk');
        $count = "";
        if ($chk) {
            $count = count($_POST['chk']);
        }
        if ($_POST['act_button'] != "추가" && !$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }
        if ($_POST['act_button'] == "추가") {
            $is_name  = trim($this->input->post('is_name'));
            $is_zip1  = conv_number($this->input->post('is_zip1'));
            $is_zip2  = conv_number($this->input->post('is_zip2'));
            $is_price = conv_number($this->input->post('is_price'));

            if (!$is_name)
                alert('지역명을 입력해 주십시오.');
            if (!$is_zip1)
                alert('우편번호 시작을 입력해 주십시오.');
            if (!$is_zip2)
                alert('우편번호 끝을 입력해 주십시오.');
            if (!$is_price)
                alert('추가배송비를 입력해 주십시오.');
            $param['is_name'] = $is_name;
            $param['is_zip1'] = $is_zip1;
            $param['is_zip2'] = $is_zip2;
            $param['is_price'] = $is_price;
            $this->query_model->insertObj('shop_island', $param);
        } else if ($_POST['act_button'] == "선택수정") {
            $is_name  = $this->input->post('is_name');
            $is_zip1  = conv_number($this->input->post('is_zip1'));
            $is_zip2  = conv_number($this->input->post('is_zip2'));
            $is_price = conv_number($this->input->post('is_price'));
            $is_id = $this->input->post('is_id');
            for ($i = 0; $i < $count; $i++) {
                $k = $_POST['chk'][$i];
                $param['is_name'] = $is_name[$k];
                $param['is_zip1'] = $is_zip1[$k];
                $param['is_zip2'] = $is_zip2[$k];
                $param['is_price'] = $is_price[$k];
                $this->query_model->updateObj('shop_island', $param, "is_id = '{$is_id[$k]}'");
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            $is_id = $this->input->post('is_id');
            for ($i = 0; $i < $count; $i++) {
                $k = $_POST['chk'][$i];

                $sql = " delete from shop_island where is_id = ? ";
                $param = array($is_id[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        } else {
            alert();
        }

        goto_url(base_url() . "admin/config?$q1&page=$page");
    }
    public function nicecheck_update()
    {
        // 본인확인을 사용할 경우 아이핀, 휴대폰인증 중 하나는 선택되어야 함
        if ($_POST['cf_cert_use'] && !$_POST['cf_cert_ipin'] && !$_POST['cf_cert_hp'])
            alert('본인확인을 위해 아이핀 또는 휴대폰 본인학인 서비스를 하나이상 선택해 주십시오');

        if (!$_POST['cf_cert_use']) {
            $_POST['cf_cert_ipin'] = '';
            $_POST['cf_cert_hp'] = '';
        }

        unset($value);
        $value['cf_cert_use']        = $this->input->post('cf_cert_use');
        $value['cf_cert_ipin']        = $this->input->post('cf_cert_ipin');
        $value['cf_cert_hp']        = $this->input->post('cf_cert_hp');
        $value['cf_cert_kcb_cd']    = $this->input->post('cf_cert_kcb_cd');
        $value['cf_cert_kcp_cd']    = $this->input->post('cf_cert_kcp_cd');
        $value['cf_lg_mid']            = $this->input->post('cf_lg_mid');
        $value['cf_lg_mert_key']    = $this->input->post('cf_lg_mert_key');
        $value['cf_cert_limit']        = $this->input->post('cf_cert_limit');
        $value['cf_cert_req']        = $this->input->post('cf_cert_req');
        $this->query_model->updateObj("shop_config", $value);

        goto_url(base_url() . 'admin/config?code=nicecheck');
    }
    public function ipaccess_update()
    {
        unset($value);
        $value['possible_ip']  = $this->input->post('possible_ip'); //접근가능 IP
        $value['intercept_ip'] = $this->input->post('intercept_ip'); //접근차단 IP
        $this->query_model->updateObj("shop_config", $value);

        goto_url(base_url() . 'admin/config?code=ipaccess');
    }
    public function board_group_form_update()
    {
        $gr_id = $this->input->post('gr_id');
        $gr_subject = $this->input->post('gr_subject');
        $w = $this->input->post('w');
        $page = $this->input->post('page');
        if (!preg_match("/^([A-Za-z0-9_]{1,10})$/", $gr_id))
            alert("그룹 ID는 공백없이 영문자, 숫자, _ 만 사용 가능합니다. (10자 이내)");

        if (!$gr_subject) alert("그룹 제목을 입력하세요.");

        if ($w == "") {
            $gr_id = $this->input->post('gr_id');
            $gr_subject = $this->input->post('gr_subject');
            $sql = " select count(*) as cnt from shop_board_group where gr_id = ?";
            $param = array($gr_id);
            $row = $this->query_model->returnOneArr($sql, $param);
            if ($row['cnt'])
                alert("이미 존재하는 그룹 ID 입니다.");

            $sql = " insert into shop_board_group set gr_id = ?, gr_subject = ?";
            $param = array($gr_id, $gr_subject);
            $this->query_model->returnNull($sql, $param);
        } else if ($w == "u") {
            $gr_id = $this->input->post('gr_id');
            $gr_subject = $this->input->post('gr_subject');
            $sql = " update shop_board_group set gr_subject = ? where gr_id = ? ";
            $param = array($gr_id, $gr_subject);
            $this->query_model->returnNull($sql, $param);
        } else
            alert("제대로 된 값이 넘어오지 않았습니다.");

        goto_url(base_url() . "admin/config?code=board_group_form&w=u&gr_id=$gr_id&page=$page");
    }
    public function board_form_update()
    {
        $qstr = $this->load->get_var('qstr');
        $w = $this->input->post('w');
        $page = $this->input->post('page');

        $bo_table = $this->input->post('bo_table');
        $board = $this->global_lib->get_board($bo_table);

        $uploadPath = "./data/board/boardimg";
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

        if (!$_POST['boardname']) {
            alert("게시판 제목을 입력하세요.");
        }
        $topfile = $board['topfile'];
        $downfile = $board['downfile'];
        if ($topfile) {
            if (!preg_match("/\.(php|htm[l]?)$/i", $topfile)) {
                alert("상단 파일 경로가 php, html 파일이 아닙니다.");
            }
        }
        if ($downfile) {
            if (!preg_match("/\.(php|htm[l]?)$/i", $downfile)) {
                alert("하단 파일 경로가 php, html 파일이 아닙니다.");
            }
        }
        $image_head_del = $this->input->post('image_head_del');
        $image_tail_del = $this->input->post('image_tail_del');
        if ($image_head_del) {
            unlink($uploadPath . '/' . $image_head_del);
            $value['fileurl1'] = '';
        }
        if ($image_tail_del) {
            unlink($uploadPath . '/' . $image_tail_del);
            $value['fileurl2'] = '';
        }

        $error = "";
        if (isset($_FILES['image_head']['name']) && $_FILES['image_head']['name'] != "") {
            if (!$this->upload->do_upload('image_head')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['fileurl1'] = $new_file;
            }
        }
        if (isset($_FILES['image_tail']['name']) && $_FILES['image_tail']['name'] != "") {
            if (!$this->upload->do_upload('image_tail')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['fileurl2'] = $new_file;
            }
        }
        if ($error != "") {
            echo "<script>alert('" . strip_tags($error) . "')</script>";
        }

        $use_category = $this->input->post('use_category');
        $use_category = (isset($use_category) && $use_category != "") ? $use_category : "";

        $usefile = $this->input->post('usefile');
        $usefile = (isset($usefile) && $usefile != "") ? $usefile : "";

        $usereply = $this->input->post('usereply');
        $usereply = (isset($usereply) && $usereply != "") ? $usereply : "";

        $usetail = $this->input->post('usetail');
        $usetail = (isset($usetail) && $usetail != "") ? $usetail : "";

        if ($w == "") {
            $value['gr_id']                = $this->input->post('gr_id');
            $value['boardname']            = $this->input->post('boardname');
            $value['skin']                = $this->input->post('skin');
            $value['list_skin']            = $this->input->post('skin');
            $value['list_priv']            = $this->input->post('list_priv');
            $value['read_priv']            = $this->input->post('read_priv');
            $value['write_priv']        = $this->input->post('write_priv');
            $value['reply_priv']        = $this->input->post('reply_priv');
            $value['tail_priv']            = $this->input->post('tail_priv');
            $value['topfile']            = $topfile;
            $value['downfile']            = $downfile;
            $value['use_secret']        = $this->input->post('use_secret');
            $value['use_category']        = $use_category;
            $value['usecate']            = $this->input->post('usecate');
            $value['usefile']            = $usefile;
            $value['usereply']            = $usereply;
            $value['usetail']            = $usetail;
            $value['width']                = $this->input->post('width');
            $value['page_num']            = $this->input->post('page_num');
            $value['read_list']            = $this->input->post('read_list');
            $value['list_cut']            = $this->input->post('list_cut');
            $value['content_head']        = $this->input->post('content_head');
            $value['content_tail']        = $this->input->post('content_tail');
            $value['insert_content']    = $this->input->post('insert_content');
            $bo_table = $this->query_model->insertObj("shop_board_conf", $value);

            $this->board_sql($bo_table);
            $path = "./data/board/" . $bo_table;
            exec("mkdir  $path");
            exec("chmod 707 $path");
            goto_url(base_url() . "admin/config?code=board_form&w=u&bo_table=$bo_table");
        } else if ($w == "u") {
            $value['gr_id']                = $this->input->post('gr_id');
            $value['boardname']            = $this->input->post('boardname');
            $value['skin']                = $this->input->post('skin');
            $value['list_skin']            = $this->input->post('skin');
            $value['list_priv']            = $this->input->post('list_priv');
            $value['read_priv']            = $this->input->post('read_priv');
            $value['write_priv']        = $this->input->post('write_priv');
            $value['reply_priv']        = $this->input->post('reply_priv');
            $value['tail_priv']            = $this->input->post('tail_priv');
            $value['topfile']            = $topfile;
            $value['downfile']            = $downfile;
            $value['use_secret']        = $this->input->post('use_secret');
            $value['use_category']        = $this->input->post('use_category');
            $value['usecate']            = $this->input->post('usecate');
            $value['usefile']            = $this->input->post('usefile');
            $value['usereply']            = $this->input->post('usereply');
            $value['usetail']            = $this->input->post('usetail');
            $value['width']                = $this->input->post('width');
            $value['page_num']            = $this->input->post('page_num');
            $value['read_list']            = $this->input->post('read_list');
            $value['list_cut']            = $this->input->post('list_cut');
            $value['content_head']        = $this->input->post('content_head');
            $value['content_tail']        = $this->input->post('content_tail');
            $value['insert_content']    = $this->input->post('insert_content');
            $bo_table = $this->input->post('bo_table');
            $this->query_model->updateObj("shop_board_conf", $value, "index_no='$bo_table'");

            goto_url(base_url() . "admin/config?code=board_form&w=u&bo_table=$bo_table$qstr&page=$page");
        }
    }
    private function board_sql($bo_table)
    {
        $board_table =
            "CREATE TABLE IF NOT EXISTS `shop_board_{$bo_table}` (
                `index_no` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '주키',
                `btype` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '형식(1:공지,2:일반)',
                `fid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글등록 순서',
                `ca_name` varchar(100) NOT NULL COMMENT '분류',
                `issecret` char(1) NOT NULL COMMENT '비밀글(Y:비밀글,N:공개)',
                `havehtml` char(1) NOT NULL COMMENT 'HTML 사용여부(미사용)',
                `writer` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '회원주키',
                `writer_s` varchar(50) NOT NULL COMMENT '작성자명',
                `subject` varchar(200) NOT NULL COMMENT '글제목',
                `memo` text NOT NULL COMMENT '글내용',
                `fileurl1` varchar(50) NOT NULL COMMENT '파일첨부1',
                `fileurl2` varchar(50) NOT NULL COMMENT '파일첨부2',
                `readcount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '조회수',
                `tailcount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '댓글수',
                `wdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '등록일',
                `wip` varchar(20) NOT NULL COMMENT '작성자IP',
                `thread` varchar(255) NOT NULL COMMENT '답글체크',
                `passwd` varchar(20) NOT NULL COMMENT '패스워드',
                `average` char(1) NOT NULL COMMENT '미사용',
                `product` varchar(50) NOT NULL COMMENT '미사용',
                `pt_id` varchar(20) NOT NULL COMMENT '가맹점ID',
                PRIMARY KEY (`index_no`),
                KEY `btype` (`btype`,`fid`,`wdate`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='공지사항' AUTO_INCREMENT=1;";


        $board_tail_table =
            "CREATE TABLE IF NOT EXISTS `shop_board_{$bo_table}_tail` (
                `index_no` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '주키',
                `board_index` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '게시판주키',
                `writer` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '회원주키',
                `writer_s` varchar(30) NOT NULL COMMENT '작성자명',
                `memo` text NOT NULL COMMENT '글내용',
                `wdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '작성일',
                `wip` varchar(20) NOT NULL COMMENT '작성자IP',
                `passwd` varchar(20) NOT NULL COMMENT '패스워드',
                PRIMARY KEY (`index_no`),
                KEY `board_index` (`board_index`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
        @$this->query_model->returnNull($board_table);
        @$this->query_model->returnNull($board_tail_table);
    }
    public function goods_plan_form_update()
    {
        $uploadPath = "./data/plan";
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

        $w = $this->input->post('w');

        unset($value);
        $pl_limg_del = $this->input->post('pl_limg_del');
        if ($pl_limg_del) {
            unlink($uploadPath . '/' . $pl_limg_del);
            $value['pl_limg'] = '';
        }
        $pl_bimg_del = $this->input->post('pl_bimg_del');
        if ($pl_bimg_del) {
            unlink($uploadPath . '/' . $pl_bimg_del);
            $value['pl_bimg'] = '';
        }
        $pl_icon_del = $this->input->post('pl_icon_del');
        if ($pl_icon_del) {
            unlink($uploadPath . '/' . $pl_icon_del);
            $value['pl_icon'] = '';
        }
        if (isset($_FILES['pl_limg']['name']) && $_FILES['pl_limg']['name'] != "") {
            if (!$this->upload->do_upload('pl_limg')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['pl_limg'] = $new_file;
            }
        }
        if (isset($_FILES['pl_bimg']['name']) && $_FILES['pl_bimg']['name'] != "") {
            if (!$this->upload->do_upload('pl_bimg')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['pl_bimg'] = $new_file;
            }
        }
        if (isset($_FILES['pl_icon']['name']) && $_FILES['pl_icon']['name'] != "") {
            if (!$this->upload->do_upload('pl_icon')) {
                $error = $this->upload->display_errors();
            } else {
                $uploadFile = $this->upload->data();
                $new_file = $uploadFile['file_name'];
                $value['pl_icon'] = $new_file;
            }
        }

        $value['mb_id']         = 'admin';
        $value['pl_name']     = $this->input->post('pl_name');
        $value['pl_it_code'] = $this->input->post('pl_it_code');
        $value['pl_use']     = $this->input->post('pl_use');

        if ($w == '') {
            $pl_no = $this->query_model->insertObj("shop_goods_plan", $value);
            goto_url(base_url() . "admin/goods?code=plan_form&w=u&pl_no=$pl_no");
        } else if ($w == 'u') {
            $pl_no = $this->input->post('pl_no');
            $this->query_model->updateObj("shop_goods_plan", $value, "pl_no='$pl_no'");

            goto_url(base_url() . "admin/goods?code=plan_form&w=u&pl_no=$pl_no");
        }
    }
    public function board_group_list_update()
    {
        $q1 = $this->input->post('q1');
        $page = $this->input->post('page');

        $count = count($_POST['chk']);
        if (!$count) {
            alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
        }
        $gr_subject = $this->input->post('gr_subject');
        $gr_id = $this->input->post('gr_id');
        $chk = $this->input->post('chk');
        if ($_POST['act_button'] == "선택수정") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = " update shop_board_group set gr_subject = ? where gr_id = ?";
                $param = array($gr_subject[$k], $gr_id[$k]);
                $this->query_model->returnNull($sql, $param);
            }
        } else if ($_POST['act_button'] == "선택삭제") {
            for ($i = 0; $i < $count; $i++) {
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $gr_id = trim($_POST['gr_id'][$k]);
                $sql = "select count(*) as cnt from shop_board_conf where gr_id = ?";
                $param = array($gr_id);
                $row = $this->query_model->returnOneArr($sql, $param);
                if ($row['cnt'])
                    alert("이 그룹에 속한 게시판이 존재하여 게시판 그룹을 삭제할 수 없습니다.\\n\\n이 그룹에 속한 게시판을 먼저 삭제하여 주십시오.", base_url() . "admin/config?code=board_list&sfl=gr_id&stx=$gr_id");

                // 그룹 삭제
                $sql = "delete from shop_board_group where gr_id =?";
                $this->query_model->returnNull($sql, $param);
            }
        }

        goto_url(base_url() . "admin/config?$q1&page=$page");
    }
    public function sns_update()
    {
        $de_sns_login_use = $this->input->post('de_sns_login_use');
        $de_naver_appid = $this->input->post('de_naver_appid');
        $de_naver_secret = $this->input->post('de_naver_secret');
        $de_facebook_appid = $this->input->post('de_facebook_appid');
        $de_facebook_secret = $this->input->post('de_facebook_secret');
        $de_kakao_rest_apikey = $this->input->post('de_kakao_rest_apikey');
        $de_google_appid = $this->input->post('de_google_appid');
        $de_google_secret = $this->input->post('de_google_secret');
        $de_googl_shorturl_apikey = $this->input->post('de_googl_shorturl_apikey');
        $de_insta_url = $this->input->post('de_insta_url');
        $de_insta_client_id = $this->input->post('de_insta_client_id');
        $de_insta_redirect_uri = $this->input->post('de_insta_redirect_uri');
        $de_insta_access_token = $this->input->post('de_insta_access_token');
        $de_sns_facebook = $this->input->post('de_sns_facebook');
        $de_sns_twitter = $this->input->post('de_sns_twitter');
        $de_sns_instagram = $this->input->post('de_sns_instagram');
        $de_sns_pinterest = $this->input->post('de_sns_pinterest');
        $de_sns_naverblog = $this->input->post('de_sns_naverblog');
        $de_sns_naverband = $this->input->post('de_sns_naverband');
        $de_sns_kakaotalk = $this->input->post('de_sns_kakaotalk');
        $de_sns_kakaostory = $this->input->post('de_sns_kakaostory');
        unset($value);
        $value['de_sns_login_use']            = $de_sns_login_use;
        $value['de_naver_appid']            = $de_naver_appid;
        $value['de_naver_secret']            = $de_naver_secret;
        $value['de_facebook_appid']            = $de_facebook_appid;
        $value['de_facebook_secret']        = $de_facebook_secret;
        $value['de_kakao_rest_apikey']        = $de_kakao_rest_apikey;
        $value['de_google_appid']            = $de_google_appid;
        $value['de_google_secret']            = $de_google_secret;
        $value['de_googl_shorturl_apikey']    = $de_googl_shorturl_apikey;
        $value['de_insta_url']                = $de_insta_url;
        $value['de_insta_client_id']        = $de_insta_client_id;
        $value['de_insta_redirect_uri']        = $de_insta_redirect_uri;
        $value['de_insta_access_token']        = $de_insta_access_token;
        $value['de_sns_facebook']            = $de_sns_facebook;
        $value['de_sns_twitter']            = $de_sns_twitter;
        $value['de_sns_instagram']            = $de_sns_instagram;
        $value['de_sns_pinterest']            = $de_sns_pinterest;
        $value['de_sns_naverblog']            = $de_sns_naverblog;
        $value['de_sns_naverband']            = $de_sns_naverband;
        $value['de_sns_kakaotalk']            = $de_sns_kakaotalk;
        $value['de_sns_kakaostory']            = $de_sns_kakaostory;
        $this->query_model->updateObj("shop_default", $value);

        goto_url(base_url() . 'admin/config?code=sns');
    }

    #환경설정 관리 함수 끝
}
