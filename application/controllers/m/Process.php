<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Summary of Process
 * 
 * @property Funcomplex_lib $funcomplex_lib
 * @property Query_model $query_model
 * @property Gvcomplex_lib $gvcomplex_lib
 * @property Global_lib $global_lib
 * @property Register_lib $register_lib
 * @property Common_lib $common_lib
 * @property CI_Image_lib $image_lib
 * @property CI_Input $input
 * @property CI_Upload $upload
 * @property CI_Security $security
 * @property External_api $external_api
 */
class Process extends CI_Controller
{
    private $max_size;
    private $max_width;
    private $max_height;
    private $remove_spaces;
    private $encrypt_name;
    private $allowed_types;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('query_model');
        $this->load->helper('common_helper');
        $this->load->helper('global_helper');
        $this->load->helper('register_helper');
        $this->load->library('funcomplex_lib');
        $this->load->library('gvcomplex_lib');
        $this->load->library('global_lib');
        $this->load->library('register_lib');
        $this->load->library('common_lib');
        $this->load->library('external_api');

        $this->allowed_types = 'jpg|jpeg|bmp|png|gif';
        $this->max_size = '8192';
        $this->max_width = '5000';
        $this->max_height = '5000';
        $this->remove_spaces = TRUE;
        $this->encrypt_name = TRUE;
    }
    #pop_coupon_update
    public function pop_coupon_update()
    {
        $member = $this->load->get_var('member');
        $token = $this->input->post('token');
        // 세션에 저장된 토큰과 폼값으로 넘어온 토큰을 비교.
        if ($token && get_session("ss_token") == $token) {
            // 맞으면 세션을 지워 다시 들어오도록 한다.
            set_session("ss_token", "");
        } else {
            alert_close("정상적인 접근이 아닙니다.");
            exit;
        }

        $cp_id = trim($_POST['cp_id']);
        $sql = "select * from shop_coupon where cp_id=?";
        $param = array($cp_id);
        $cp = $this->query_model->returnOneArr($sql, $param);

        $this->funcomplex_lib->insert_used_coupon($member['id'], $member['name'], $cp);

        alert("쿠폰발행이 정상적으로 처리 되었습니다.\\n\\n발급 된 쿠폰은 마이페이지에서 확인 가능합니다.", "close");
    }
    #orderreview_update
    public function orderreview_update()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $is_member = $this->load->get_var('is_member');
        if (!$is_member) {
            alert("로그인 후 작성 가능합니다.");
        }
        $member = $this->load->get_var('member');
        $pt_id = $this->load->get_var('pt_id');
        $w = $this->input->post('w');
        $token = $this->input->post('token');

        if ($token && get_session("ss_token") == $token) {
            // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
            set_session("ss_token", "");
        } else {
            alert("잘못된 접근 입니다.");
            exit;
        }

        $gs_id = trim(strip_tags($this->input->post('gs_id')));
        $seller_id = trim(strip_tags($this->input->post('seller_id')));
        $score = trim(strip_tags($this->input->post('score')));
        $od_id = $this->input->post('od_id');
        $od_no = $this->input->post('od_no');
        $w = $this->input->post('w');
        $memo = addslashes($this->input->post('memo'));
        if (empty($memo)) {
            alert("내용을 입력해주세요.");
        }
        $memo = nl2br(addslashes($memo));

        $del_img1 = $this->input->post('del_img1');
        $del_img2 = $this->input->post('del_img2');
        $del_img3 = $this->input->post('del_img3');
        $del_img4 = $this->input->post('del_img4');

        if (substr_count($_POST['memo'], "&#") > 50) {
            alert("내용에 올바르지 않은 코드가 다수 포함되어 있습니다.");
        }

        // 구매후기 차단 구매한사람만 쓸수있음 시작
        if ($w == "" && !$this->common_lib->find_review_flag($od_no, $member['id'])) {
            alert_close('후기는 구매완료후 등록이 가능합니다.');
            exit;
        }

        $order_review_data = $this->common_lib->get_order_review_data($gs_id, $member['id'], $od_id);

        $sql_commend = "";
        $point = "";

        // 리뷰 사진
        $uploadPath = "./data/review/";
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        $configArr = array();
        $configArr['upload_path'] = $uploadPath;

        $configArr['allowed_types'] = 'jpg|jpeg|bmp|png|gif|mp4|mov|wmv';
        $configArr['max_size'] = '6168';
        $configArr['max_width'] = $this->max_width;
        $configArr['max_height'] = $this->max_height;
        $configArr['remove_spaces'] = $this->remove_spaces;
        $configArr['encrypt_name'] = $this->encrypt_name;

        $this->load->library('upload', $configArr);
        $this->upload->initialize($configArr);

        // 파일이 있으면 파일을 업로드함
        $uploadFile = array('img1' => '', 'img2' => '', 'img3' => '', 'img4' => '');
        $error = "";

        foreach (['img1', 'img2', 'img3', 'img4'] as $img_key) {
            if (isset($_FILES[$img_key]['name']) && $_FILES[$img_key]['name'] != "") {
                if (!$this->upload->do_upload($img_key)) {
                    $error = $this->upload->display_errors();
                } else {
                    $uploadFile[$img_key] = $this->upload->data();
                    $image_data = $uploadFile[$img_key];

                    // 이미지 리사이징 설정
                    $resize_config['image_library'] = 'gd2';
                    $resize_config['source_image'] = $image_data['full_path']; // 업로드된 이미지 경로
                    $resize_config['maintain_ratio'] = TRUE; // 비율 유지 여부
                    $resize_config['width'] = 1000;

                    // 가로 1000 넘을 때만 실행
                    if ($image_data['image_width'] > 1000) {

                        $this->load->library('image_lib');
                        $this->image_lib->initialize($resize_config); // 설정 초기화

                        if (!$this->image_lib->resize()) {
                            alert($this->image_lib->display_errors());
                        }
                    }

                    $new_img = $image_data['file_name'];
                    $sql_commend .= " , $img_key = '$new_img' ";
                }
            }
        }

        if ($error != "") {
            alert(strip_tags($error));
        }

        if ($w == "") {
            $sql = "INSERT INTO shop_goods_review 
                    SET gs_id = ?, mb_id = ?, memo = ?, score = ?, reg_time = '" . $OM_TIME_YMDHIS . "', seller_id = ?, seller_name = ?, od_index = ?, pt_id = ?, od_id = ?, od_no = ? {$sql_commend}";
            $param = array(
                $gs_id,
                $member['id'],
                $memo,
                $score,
                $seller_id,
                $order_review_data['seller_name'],
                $order_review_data['index_no'],
                $pt_id,
                $od_id,
                $od_no
            );
            $this->query_model->returnNull($sql, $param);

            $sql = "UPDATE shop_goods SET m_count = m_count+1 WHERE index_no = ?";
            $param = array($gs_id);
            $this->query_model->returnNull($sql, $param);

            $grade = $member['grade'];

            $sql = "SELECT gb_review_point FROM shop_member_grade WHERE gb_no = ?";
            $param = array($grade);
            $result = $this->query_model->returnOneArr($sql, $param);

            $gs = $this->global_lib->get_goods($order_review_data['gs_id']);

            if ($gs['is_point'] > 0) {
                $gb_point = $result['gb_review_point']; // 적립률
                $goods_price = $order_review_data['goods_price']; // 구매금액  
                $point = round($goods_price * ($gb_point * (1 / 100))); // 적립 포인트
            }

            // 리뷰 작성 포인트 부여
            $this->funcomplex_lib->insert_point($member['id'], $point, "주문번호 {$od_id} 리뷰작성", '@review', $member['id'], 'review_' . $od_id . "_{$gs_id}");

            // 주문내역 flag 변경
            $this->funcomplex_lib->set_order_review_flag(0, $order_review_data['index_no'], $member['id'], $od_no);

            $txt = "등록";
        } else { // 수정
            if ($del_img1) {
                unlink($uploadPath . '/' . $del_img1);
                delete_review_thumbnail($del_img1);
                $sql_commend .= " , img1 = '' ";
            }

            if ($del_img2) {
                unlink($uploadPath . '/' . $del_img2);
                delete_review_thumbnail($del_img2);
                $sql_commend .= " , img2 = '' ";
            }

            if ($del_img3) {
                unlink($uploadPath . '/' . $del_img3);
                delete_review_thumbnail($del_img3);
                $sql_commend .= " , img3 = '' ";
            }

            if ($del_img4) {
                unlink($uploadPath . '/' . $del_img4);
                delete_review_thumbnail($del_img4);
                $sql_commend .= " , img4 = '' ";
            }

            $sql = "UPDATE shop_goods_review SET memo = ?, score = ? {$sql_commend} WHERE od_id = ?";
            $param = array(
                $memo,
                $score,
                $od_id
            );
            $this->query_model->returnNull($sql, $param);

            $txt = "수정";
        }

        alert_close("정상적으로 {$txt} 되었습니다.");
    }
    #register_form_update
    public function register_form_update()
    {
        try {
            include_once('./lib/mailer.lib.php');
            $w = $this->input->post('w');
            $type = $this->input->post('type');
            $config = $this->load->get_var('config');
            $super = $this->load->get_var('super');
            $member = $this->load->get_var('member');

            $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
            $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
            $OM_HASH_TOKEN = md5(base_url() . $OM_TIME_YMD . $_SERVER['REMOTE_ADDR']);

            $user_file_value = [
                'shop_member_id' => null,
                'company_file_original_name' => null,
                'company_file_name' => null,
                'company_file_path' => null,
                'company_file_type' => null,
                'company_file_size' => null,
            ];
            $seller_file_value = [
                'shop_seller_id' => null,
                'company_file_original_name' => null,
                'company_file_name' => null,
                'company_file_path' => null,
                'company_file_type' => null,
                'company_file_size' => null,
                'bank_file_original_name' => null,
                'bank_file_name' => null,
                'bank_file_path' => null,
                'bank_file_type' => null,
                'bank_file_size' => null,
            ];

            if (!($w == '' || $w == 'u')) {
                throw new Exception('w 값이 제대로 넘어오지 않았습니다.');
            }

            if ($_SESSION['ss_hash_token'] != $OM_HASH_TOKEN) {
                throw new Exception('잘못된 접근입니다.');
            }

            if (!$this->input->post('agree1') && $w == "" && $type == "user") {
                throw new Exception('이용약관을 동의해주세요.');
            }

            if (!$this->input->post('agree2') && $w == "") {
                throw new Exception('개인정보 수집 · 이용 동의를 동의해주세요.');
            }

            $mb_id = "";
            if ($w == 'u') {
                $mb_id = isset($_SESSION['ss_mb_id']) ? trim($_SESSION['ss_mb_id']) : '';
            } else if ($w == '') {
                $mb_id = trim($_POST['mb_id']);
            } else {
                throw new Exception('잘못된 접근입니다.');
            }

            if (!$mb_id) {
                throw new Exception('아이디를 입력해주세요.');
            }

            $mb_password    = trim($this->input->post('mb_password'));
            $mb_password_re = trim($this->input->post('mb_password_re'));
            $mb_nickname    = trim($this->input->post('mb_nickname'));
            $mb_name        = trim($this->input->post('mb_name'));

            $mb_email = "";
            $mb_email_forward = $this->input->post('mb_email');
            $mb_email_addr  = $this->input->post('mb_email_addr');
            $mb_email_ect = $this->input->post('mb_email_ect');
            if ($type === 'user') {
                if (empty($mb_email_forward) || (empty($mb_email_addr) && empty($mb_email_ect))) {
                    throw new Exception('이메일을 입력해주세요.');
                }
            }

            if (empty($mb_email_ect)) {
                $mb_email = get_email_address($mb_email_forward . "@" . $mb_email_addr);
            } else {
                $mb_email = get_email_address($mb_email_forward . "@" . $mb_email_ect);
            }

            $mb_hp_01          = $this->input->post('mb_hp_01');
            $mb_hp_02          = $this->input->post('mb_hp_02');
            $mb_hp_03          = $this->input->post('mb_hp_03');
            $mb_hp = "";
            if ($type == "user") {
                if (empty($mb_hp_01) || empty($mb_hp_02) || empty($mb_hp_03)) {
                    throw new Exception('휴대폰 번호를 입력해주세요.');
                }
                $mb_hp = $mb_hp_01 . "-" . $mb_hp_02 . "-" . $mb_hp_03;
            }

            $mb_zip            = $this->input->post('mb_zip');
            $mb_addr1       = $this->input->post('mb_addr1');
            $mb_addr2       = $this->input->post('mb_addr2');
            $mb_addr3       = $this->input->post('mb_addr3');
            $mb_addr_jibeon = $this->input->post('mb_addr_jibeon');
            $mb_recommend   = $this->input->post('mb_recommend');
            $mb_mailling    = $this->input->post('mb_mailling');
            $mb_sms         = $this->input->post('mb_sms');

            $mb_nickname    = $this->security->xss_clean($mb_nickname);
            $mb_name        = $this->security->xss_clean($mb_name);
            $mb_zip            = preg_replace('/[^0-9]/', '', $mb_zip);
            $mb_addr1       = $this->security->xss_clean($mb_addr1);
            $mb_addr2       = $this->security->xss_clean($mb_addr2);
            $mb_addr3       = $this->security->xss_clean($mb_addr3);
            $mb_addr_jibeon = preg_match("/^(N|R)$/", $mb_addr_jibeon) ? $mb_addr_jibeon : '';


            $company_no_01 = $this->input->post('company_no_01');
            $company_no_02 = $this->input->post('company_no_02');
            $company_no_03 = $this->input->post('company_no_03');

            if ((empty($company_no_01) || mb_strlen($company_no_01) < 3) || (empty($company_no_02) || mb_strlen($company_no_02) < 2) || (empty($company_no_03) || mb_strlen($company_no_03) < 5)) {
                throw new Exception('사업자 등록번호를 입력해주세요.');
            }

            $company_no = $company_no_01 . "-" . $company_no_02 . "-" . $company_no_03;

            if (empty($mb_name)) {
                throw new Exception('상호명을 입력해주세요.');
            }

            if ($type == "seller") {
                $company_owner = $this->input->post('company_owner');
                if (empty($company_owner)) {
                    throw new Exception('대표자명을 입력해주세요.');
                }

                $company_tax_email_forward = $this->input->post('company_tax_email');
                $company_tax_email_addr = $this->input->post('company_tax_email_addr');
                $company_tax_email_ect = $this->input->post('company_tax_email_ect');
                if (empty($company_tax_email_forward) || (empty($company_tax_email_addr) && empty($company_tax_email_ect))) {
                    throw new Exception('세금계산서 이메일을 입력해주세요.');
                }
                if (empty($company_tax_email_ect)) {
                    $company_tax_email = $company_tax_email_forward . "@" . $company_tax_email_addr;
                } else {
                    $company_tax_email = $company_tax_email_forward . "@" . $company_tax_email_ect;
                }

                $company_item = $this->input->post('company_item');
                if (empty($company_item)) {
                    throw new Exception('업태를 입력해주세요.');
                }

                $company_service = $this->input->post('company_service');
                if (empty($company_service)) {
                    throw new Exception('종목을 입력해주세요.');
                }

                $company_tel_01 = $this->input->post('company_tel_01');
                $company_tel_02 = $this->input->post('company_tel_02');
                $company_tel_03 = $this->input->post('company_tel_03');
                if (empty($company_tel_01) || empty($company_tel_02) || empty($company_tel_03)) {
                    throw new Exception('대표번호를 입력해주세요.');
                }
                $company_tel = $company_tel_01 . "-" . $company_tel_02 . "-" . $company_tel_03;

                $company_hompage = $this->input->post('company_hompage');
                if (empty($company_hompage)) {
                    throw new Exception('홈페이지/판매처 URL을 입력해주세요.');
                }

                $memo = $this->input->post('memo');
                if (empty($memo)) {
                    throw new Exception('회사 소개를 입력해주세요.');
                }

                $company_brand_name = $this->input->post('company_brand_name');
                if (empty($company_brand_name)) {
                    throw new Exception('브랜드명을 입력해주세요.');
                }

                $company_category = $this->input->post('company_category[]');
                if (empty($company_category)) {
                    throw new Exception('카테고리를 선택해주세요.');
                }

                $company_category = implode(",", $company_category);


                $company_item_info = $this->input->post('company_item_info');
                if (empty($company_item_info)) {
                    throw new Exception('상품소개를 입력해주세요.');
                }

                $company_sales_type = $this->input->post('company_sales_type');
                if (empty($company_sales_type)) {
                    throw new Exception('판매형태를 선택해주세요.');
                }
                $company_sales_type = implode(",", $company_sales_type);

                $company_entry = $this->input->post('company_entry');
                if (empty($company_entry)) {
                    throw new Exception('입점몰을 입력해주세요.');
                }

                $bank_name = $this->input->post('bank_name');
                if (empty($bank_name)) {
                    throw new Exception('은행명을 입력해주세요.');
                }

                $bank_account_01 = $this->input->post('bank_account_01');
                $bank_account_02 = $this->input->post('bank_account_02');
                $bank_account_03 = $this->input->post('bank_account_03');

                if (empty($bank_account_01)  || empty($bank_account_02) || empty($bank_account_03)) {
                    throw new Exception('계좌번호를 입력해주세요.');
                }

                $bank_account = $bank_account_01 . "-" . $bank_account_02 . "-" . $bank_account_03;


                $bank_holder = $this->input->post('bank_holder');
                if (empty($bank_holder)) {
                    throw new Exception('예금주명을 입력해주세요.');
                }

                $info_name = $this->input->post('info_name');
                if (empty($info_name)) {
                    throw new Exception('담당자명을 입력해주세요.');
                }

                $info_tel_01 = $this->input->post('info_tel_01');
                $info_tel_02 = $this->input->post('info_tel_02');
                $info_tel_03 = $this->input->post('info_tel_03');
                if (empty($info_tel_01) || empty($info_tel_02) || empty($info_tel_03)) {
                    throw new Exception('담당자 휴대폰 번호를 입력해주세요.');
                }
                $info_tel = $info_tel_01 . "-" . $info_tel_02 . "-" . $info_tel_03;

                if (empty($info_tel)) {
                    throw new Exception('담당자 휴대폰을 입력해주세요.');
                }

                $info_email_forward = $this->input->post('info_email');
                $info_email_addr = $this->input->post('info_email_addr');
                $info_email_ect = $this->input->post('info_email_ect');
                if (empty($info_email_forward) || (empty($info_email_addr) && empty($info_email_ect))) {
                    throw new Exception('담당자 이메일을 입력해주세요.');
                }
                if (empty($info_email_ect)) {
                    $info_email = $info_email_forward . "@" . $info_email_addr;
                } else {
                    $info_email = $info_email_forward . "@" . $info_email_ect;
                }
            }

            if ($w == '' || $w == 'u') {

                if ($msg = empty_mb_id($mb_id))    throw new Exception($msg);
                if ($msg = valid_mb_id($mb_id))    throw new Exception($msg);
                if ($msg = count_mb_id($mb_id))    throw new Exception($msg);

                // 이름에 utf-8 이외의 문자가 포함됐다면 오류
                // 서버환경에 따라 정상적으로 체크되지 않을 수 있음.
                $tmp_mb_name = iconv('UTF-8', 'UTF-8//IGNORE', $mb_name);
                if ($tmp_mb_name != $mb_name) {
                    throw new Exception('이름을 올바르게 입력해 주십시오.');
                }

                if ($w == '' && !empty($mb_password)) {
                    if ($msg = valid_mb_password($mb_password)) {
                        throw new Exception($msg);
                    }
                    if ($w == '' && $mb_password != $mb_password_re) {
                        throw new Exception('비밀번호가 일치하지 않습니다.');
                    }
                }

                // 이름에 한글명 체크를 하지 않는다.
                //if($msg = valid_mb_name($mb_name))	throw new Exception($msg);

                if ($type == "user") {
                    if ($msg = empty_mb_email($mb_email))    throw new Exception($msg);
                    if ($msg = $this->gvcomplex_lib->reserve_mb_id($mb_id))        throw new Exception($msg);
                    if ($msg = valid_mb_email($mb_email))    throw new Exception($msg);
                    if ($msg = $this->gvcomplex_lib->prohibit_mb_email($mb_email))    throw new Exception($msg);

                    // 휴대폰 필수입력일 경우 휴대폰번호 유효성 체크
                    if (($config['register_use_hp'] || $config['cf_cert_hp']) && $config['register_req_hp']) {
                        if ($msg = valid_mb_hp($mb_hp))        throw new Exception($msg);
                    }
                }


                if ($w == '') {
                    if ($msg = $this->register_lib->exist_mb_id($mb_id)) {
                        throw new Exception($msg);
                    }

                    // id 중복체크 확인
                    if (get_session('ss_check_mb_id') != $mb_id) {
                        set_session('ss_check_mb_id', '');
                        throw new Exception('아이디 중복확인을 해주세요.');
                    }

                    if ($type == "user") {
                        if ($msg = empty_mb_name($mb_name))        throw new Exception($msg);

                        // // 닉네임 중복체크 확인
                        // if (get_session('ss_check_mb_nickname') != $mb_nickname) {
                        //     set_session('ss_check_mb_nickname', '');
                        //     throw new Exception('닉네임 중복확인을 해주세요.');
                        // }

                        // 이메일 중복 확인
                        if ($msg = $this->register_lib->exist_mb_email($mb_email, $mb_id)) {
                            throw new Exception($msg);
                        }
                    }

                    if ($type == "seller" || $type == "user") {
                        // 사업자 중복체크 확인
                        if (get_session('ss_check_company_number') != $company_no) {
                            set_session('ss_check_company_number', '');
                            throw new Exception('사업자 등록번호 중복확인을 해주세요.');
                        }
                    }

                    // 본인확인 체크
                    if ($config['cf_cert_use'] && $config['cf_cert_req']) {
                        if (trim($_POST['cert_no']) != $_SESSION['ss_cert_no'] || !$_SESSION['ss_cert_no'])
                            throw new Exception("회원가입을 위해서는 본인확인을 해주셔야 합니다.");
                    }

                    if ($mb_recommend) {
                        if (!$this->register_lib->exist_mb_id($mb_recommend))
                            throw new Exception("추천인이 존재하지 않습니다.");
                    }

                    if (strtolower($mb_id) == strtolower($mb_recommend)) {
                        throw new Exception('본인을 추천할 수 없습니다.');
                    }
                } else {
                    // 자바스크립트로 정보변경이 가능한 버그 수정
                    // 회원정보의 메일을 이전 메일로 옮기고 아래에서 비교함
                    $old_email = $member['email'];
                }
            }
            //===============================================================
            //  본인확인
            //---------------------------------------------------------------
            if ($type == "user") {
                $mb_hp = hyphen_hp_number($mb_hp);
            }

            if ($config['cf_cert_use'] && $_SESSION['ss_cert_type'] && $_SESSION['ss_cert_dupinfo']) {
                // 중복체크
                $sql = " select id from shop_member where id <> ? and mb_dupinfo = ?";
                $data = array($member['id'], $_SESSION['ss_cert_dupinfo']);
                $row = $this->Query_model->returnOneArr($sql, $data);
                if ($row['id']) {
                    throw new Exception("입력하신 본인확인 정보로 가입된 내역이 존재합니다.\\n회원아이디 : " . $row['id']);
                }
            }

            $value = array();
            $md5_cert_no = $_SESSION['ss_cert_no'];
            $cert_type = $_SESSION['ss_cert_type'];
            if ($config['cf_cert_use'] && $cert_type && $md5_cert_no) {
                // 해시값이 같은 경우에만 본인확인 값을 저장한다.
                if ($_SESSION['ss_cert_hash'] == md5($mb_name . $cert_type . $_SESSION['ss_cert_birth'] . $md5_cert_no)) {
                    $value['cellphone']        = $mb_hp;
                    $value['mb_certify']    = $cert_type;
                    $value['mb_adult']        = $_SESSION['ss_cert_adult'];
                    $value['mb_birth']        = $_SESSION['ss_cert_birth'];
                    $value['gender']        = $_SESSION['ss_cert_sex'];
                    $value['mb_dupinfo']    = $_SESSION['ss_cert_dupinfo'];
                    $value['age']            = get_birth_age($_SESSION['ss_cert_birth']);
                    if ($w == 'u')
                        $value['name'] = $mb_name;
                } else {
                    $value['cellphone']        = $mb_hp;
                    $value['mb_certify']    = '';
                    $value['mb_adult']        = '0';
                    $value['mb_birth']        = '';
                    $value['gender']        = '';
                    $value['age']            = '';
                }
            } else {
                if (get_session("ss_reg_mb_name") != $mb_name || get_session("ss_reg_mb_hp") != $mb_hp) {
                    $value['cellphone']        = $mb_hp;
                    $value['mb_certify']    = '';
                    $value['mb_adult']        = '0';
                    $value['mb_birth']        = '';
                    $value['gender']        = '';
                    $value['age']            = '';
                }
            }

            // 회원 사진
            $uploadPath = "./data/member/";
            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
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
                    $value['mb_img'] = $uploadFile['mb_img']['file_name'];
                }
            }

            if ($error != "") {
                throw new Exception($error);
            }

            //===============================================================
            if ($w == '') {
                $value['id']            = $mb_id; //회원아이디
                $value['passwd']        = $this->global_lib->reg_sql_password($mb_password, $OM_TIME_YMDHIS); //비밀번호
                $value['name']            = $mb_name; //이름
                $value['nickname']        = $mb_nickname; //닉네임
                $value['email']            = $mb_email; //이메일
                $value['zip']            = $mb_zip; //우편번호
                $value['addr1']            = $mb_addr1; //주소
                $value['addr2']            = $mb_addr2; //상세주소
                $value['addr3']            = $mb_addr3; //참고항목
                $value['addr_jibeon']    = $mb_addr_jibeon; //지번주소
                $value['today_login']    = $OM_TIME_YMDHIS; //최근 로그인일시
                $value['reg_time']        = $OM_TIME_YMDHIS; //가입일시
                $value['mb_ip']            = $_SERVER['REMOTE_ADDR']; //IP
                $value['grade']            = '2'; // 일반회원 레벨
                $value['pt_id']            = $mb_recommend; //추천인아이디
                $value['login_ip']        = $_SERVER['REMOTE_ADDR']; //최근 로그인IP
                $value['mailser']        = $mb_mailling ? $mb_mailling : 'N'; //E-Mail을 수신
                $value['smsser']        = $mb_sms ? $mb_sms : 'N'; //SMS를 수신
                $value['company_name'] = $mb_name;
                $value['company_saupja_no'] = $company_no;

                // 관리자인증을 사용하지 않는다면 인증으로 간주함.
                if (!$config['cert_admin_yes']) {
                    $value['use_app']    = '1';
                }

                // 입점사의 휴대폰 번호와 이메일 주소는 사업장 번호와 사업장 이메일로
                if ($type == "seller") {
                    $value['cellphone'] = $company_tel;
                    $value['email'] = $company_tax_email;
                }

                // shop_member 테이블 입력
                $mb_no = $this->query_model->insertObj("shop_member", $value);

                if ($type == "user") {

                    $user_file_value['shop_member_id'] = $mb_id;

                    if (isset($_FILES['company_cert_file']) && $_FILES['company_cert_file']['error'] === UPLOAD_ERR_OK) {
                        $upload_path = "./data/cert/$type/";
                        $company_number = $company_no;

                        $company_result = $this->upload_file($upload_path, $company_number, 'company_cert_file', 'company', $mb_id);

                        if (!$company_result['status']) {
                            throw new Exception($company_result['message']); // 실패 메시지 처리
                        }

                        $user_file_value['company_file_original_name'] = $_FILES['company_cert_file']['name'];
                        $user_file_value['company_file_name'] = $company_result['file_name'];
                        $user_file_value['company_file_path'] = $upload_path = "./data/cert/$type/";
                        $user_file_value['company_file_type'] = $company_result['mime_type'];
                        $user_file_value['company_file_size'] = $company_result['file_size_kb'];
                    } else {
                        throw new Exception("사업자 파일 첨부 오류 발생");
                    }

                    $this->query_model->insertObj("shop_member_file", $user_file_value);
                }

                if ($type == "seller") {
                    // 입점사 회원 shop_seller 테이블 입력
                    $seller_value['seller_code']          = $this->global_lib->code_uniqid();
                    $seller_value['mb_id']                  = $mb_id;
                    $seller_value['company_name']          = $mb_name;
                    $seller_value['company_saupja_no']      = $company_no;
                    $seller_value['company_item']          = $company_item;
                    $seller_value['company_service']      = $company_service;
                    $seller_value['company_owner']          = $company_owner;
                    $seller_value['company_tax_email']      = $company_tax_email;
                    $seller_value['company_tel']          = $company_tel;
                    $seller_value['company_zip']          = $mb_zip;
                    $seller_value['company_addr1']          = $mb_addr1;
                    $seller_value['company_addr2']          = $mb_addr2;
                    $seller_value['company_addr3']          = $mb_addr3;
                    $seller_value['company_addr_jibeon']  = $mb_addr_jibeon;
                    $seller_value['company_hompage']      = $company_hompage;
                    $seller_value['memo']                 = $memo;

                    $seller_value['company_brand_name']   = $company_brand_name;
                    $seller_value['company_category']     = $company_category;
                    $seller_value['company_item_info']    = $company_item_info;
                    $seller_value['company_sales_type']   = $company_sales_type;
                    $seller_value['company_entry']        = $company_entry;

                    $seller_value['bank_name']            = $bank_name;
                    $seller_value['bank_account']         = $bank_account;
                    $seller_value['bank_holder']          = $bank_holder;

                    $seller_value['info_name']              = $info_name;
                    $seller_value['info_tel']              = $info_tel;
                    $seller_value['info_email']              = $info_email;

                    $seller_value['reg_time']              = $OM_TIME_YMDHIS;
                    $seller_value['update_time']          = $OM_TIME_YMDHIS;
                    $this->query_model->insertObj("shop_seller", $seller_value);

                    $update['supply'] = "Y";

                    // 회원정보 업데이트
                    $this->query_model->updateObj("shop_member", $update, "index_no = '$mb_no' ");

                    $seller_file_value['shop_seller_id'] = $mb_id;

                    if (isset($_FILES['company_cert_file']) && $_FILES['company_cert_file']['error'] === UPLOAD_ERR_OK) {
                        $upload_path = "./data/cert/$type/";
                        $company_number = $company_no;

                        $company_result = $this->upload_file($upload_path, $company_number, 'company_cert_file', 'company', $mb_id);

                        if (!$company_result['status']) {
                            throw new Exception($company_result['message']); // 실패 메시지 처리
                        }

                        $seller_file_value['company_file_original_name'] =  $_FILES['company_cert_file']['name'];
                        $seller_file_value['company_file_name'] = $company_result['file_name'];
                        $seller_file_value['company_file_path'] = $upload_path;
                        $seller_file_value['company_file_type'] = $company_result['mime_type'];
                        $seller_file_value['company_file_size'] = $company_result['file_size_kb'];
                    } else {
                        throw new Exception("사업자 파일 첨부 오류 발생");
                    }

                    if (isset($_FILES['bank_account_file']) && $_FILES['bank_account_file']['error'] === UPLOAD_ERR_OK) {
                        $upload_path = "./data/cert/$type/";


                        $bank_result = $this->upload_file($upload_path, $bank_account, 'bank_account_file', 'bank', $mb_id);

                        if (!$bank_result['status']) {
                            throw new Exception($bank_result['message']); // 실패 메시지 처리
                        }
                        $seller_file_value['bank_file_original_name'] = $_FILES['bank_account_file']['name'];
                        $seller_file_value['bank_file_name'] = $bank_result['file_name'];
                        $seller_file_value['bank_file_path'] = $upload_path;
                        $seller_file_value['bank_file_type'] = $bank_result['mime_type'];
                        $seller_file_value['bank_file_size'] = $bank_result['file_size_kb'];
                    } else {
                        throw new Exception("은행 파일 첨부 오류 발생");
                    }

                    //파일 db에 업로드
                    $this->query_model->insertObj("shop_seller_file", $seller_file_value);
                }

                if ($type == "user") {
                    // 회원가입 포인트 부여
                    $this->funcomplex_lib->insert_point($mb_id, $config['register_point'], '회원가입 축하 포인트 적립', '@member', $mb_id, '회원가입');

                    // 추천인에게 포인트 부여
                    $this->funcomplex_lib->insert_point($mb_recommend, $config['partner_point'], $mb_id . '의 추천인', '@member', $mb_recommend, $mb_id . ' 추천');
                }

                // 회원님께 메일 발송
                $subject = '[' . $config['company_name'] . '] 회원가입을 축하드립니다.';
                $content = $this->register_form_update_mail1($mb_name);
                mailer($config['company_name'], $super['email'], $mb_email, $subject, $content, 1);


                // 최고관리자님께 메일 발송
                $subject = '[' . $config['company_name'] . '] ' . $mb_name . '님께서 회원으로 가입하셨습니다.';
                $content = $this->register_form_update_mail2($mb_name, $mb_id, $mb_recommend);
                mailer($mb_name, $mb_email, $super['email'], $subject, $content, 1);

                // 회원가입 문자발송
                $this->funcomplex_lib->moonjaro_register_sms_send($mb_recommend, $mb_id);

                // 관리자인증을 사용하지 않는 경우에만 로그인
                if ($config['cert_admin_yes']) {
                    $msg = "회원가입이 완료 되었으며 승인 처리 이후 로그인 가능합니다";
                } else {
                    set_session('ss_mb_reg', $mb_id);
                    set_session('ss_mb_id', $mb_id);
                }
            } else if ($w == 'u') {
                $ss_mb_id = $_SESSION['ss_mb_id'];
                if (!trim($ss_mb_id)) {
                    throw new Exception('로그인 되어 있지 않습니다.');
                }

                // if(trim($_POST['mb_id']) != $mb_id)
                //     alert("로그인된 정보와 수정하려는 정보가 틀리므로 수정할 수 없습니다.\\n만약 올바르지 않은 방법을 사용하신다면 바로 중지하여 주십시오.");

                if ($mb_password) {
                    $value['passwd']    = $this->global_lib->changePassword($mb_password, $ss_mb_id); //비밀번호
                }

                $value['email']            = $mb_email; //이메일
                $value['zip']            = $mb_zip; //우편번호
                $value['addr1']            = $mb_addr1; //주소
                $value['addr2']            = $mb_addr2; //상세주소
                $value['addr3']            = $mb_addr3; //참고항목
                $value['addr_jibeon']    = $mb_addr_jibeon; //지번주소
                $value['mailser']        = $mb_mailling ? $mb_mailling : 'N'; //E-Mail을 수신
                $value['smsser']        = $mb_sms ? $mb_sms : 'N'; //SMS를 수신
                $this->query_model->updateObj("shop_member", $value, "id = '{$member['id']}' ");

                if ($type == "seller") {
                    $seller_value['company_saupja_no']      = $company_no;
                    $seller_value['company_item']          = $company_item;
                    $seller_value['company_service']      = $company_service;
                    $seller_value['company_owner']          = $company_owner;
                    $seller_value['company_tax_email']      = $company_tax_email;
                    $seller_value['company_tel']          = $company_tel;
                    $seller_value['company_zip']          = $mb_zip;
                    $seller_value['company_addr1']          = $mb_addr1;
                    $seller_value['company_addr2']          = $mb_addr2;
                    $seller_value['company_addr3']          = $mb_addr3;
                    $seller_value['company_addr_jibeon']  = $mb_addr_jibeon;
                    $seller_value['company_hompage']      = $company_hompage;
                    $seller_value['memo']                 = $memo;

                    $seller_value['company_brand_name']   = $company_brand_name;
                    $seller_value['company_category']     = $company_category;
                    $seller_value['company_item_info']    = $company_item_info;
                    $seller_value['company_sales_type']   = $company_sales_type;
                    $seller_value['company_entry']        = $company_entry;

                    $seller_value['bank_name']            = $bank_name;
                    $seller_value['bank_account']         = $bank_account;
                    $seller_value['bank_holder']          = $bank_holder;

                    $seller_value['info_name']              = $info_name;
                    $seller_value['info_tel']              = $info_tel;
                    $seller_value['info_email']              = $info_email;

                    $this->query_model->updateObj("shop_seller", $seller_value, "mb_id = '{$member['id']}' ");
                }
            }

            // 신규회원가입 쿠폰발급
            if ($w == '' && $config['coupon_yes'] && $type == "user") {
                $cp_used = false;
                $cp = $this->query_model->returnOneArr("select * from shop_coupon where cp_type = '5'");
                if ($cp['cp_id'] && $cp['cp_use']) {
                    if (($cp['cp_pub_sdate'] <= $OM_TIME_YMD || $cp['cp_pub_sdate'] == '9999999999') &&
                        ($cp['cp_pub_edate'] >= $OM_TIME_YMD || $cp['cp_pub_edate'] == '9999999999')
                    )
                        $cp_used = true;

                    if ($cp_used)
                        $this->funcomplex_lib->insert_used_coupon($mb_id, $mb_name, $cp);
                }
            }

            unset($_SESSION['ss_cert_type']);
            unset($_SESSION['ss_cert_no']);
            unset($_SESSION['ss_cert_hash']);
            unset($_SESSION['ss_cert_birth']);
            unset($_SESSION['ss_cert_adult']);
            unset($_SESSION['ss_hash_token']);

            if ($msg) {
                $return['res'] = true;
                $return['msg'] = $msg;
            }

            if ($w == '') {
                $return['res'] = true;
                $return['msg'] = $type == "user" ? "회원가입이 완료되었습니다." : "입점사 가입이 완료되었습니다. 관리자의 승인 후 이용이 가능합니다.";
            } else if ($w == 'u') {
                $return['res'] = true;
                $return['msg'] = "회원정보가 수정되었습니다.";
            }
        } catch (Exception $e) {
            $return['res'] = false;
            $return['msg'] = $e->getMessage();
        }
        echo json_encode($return);
    }
    private function register_form_update_mail1($mb_name)
    {
        $config = $this->load->get_var('config');
        $return = "
        <!doctype html>
            <html lang=\"ko\">
            <head>
            <meta charset=\"utf-8\">
            <title>회원가입 축하 메일</title>
            </head>

            <body>

            <div style=\"margin:30px auto;width:600px;border:10px solid #f7f7f7\">
                <div style=\"border:1px solid #dedede\">
                    <h1 style=\"padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em\">
                        회원가입을 축하합니다.
                    </h1>
                    <span style=\"display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right\">
                        <a href=\"" . base_url() . "\" target=\"_blank\">{$config['company_name']}</a>
                    </span>
                    <p style=\"margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee\">
                        <b>{$mb_name}</b> 님의 회원가입을 진심으로 축하합니다.<br>
                        회원님의 성원에 보답하고자 더욱 더 열심히 하겠습니다.<br>
                        감사합니다.
                    </p>

                    <a href=\"" . base_url() . "\" target=\"_blank\" style=\"display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center\">사이트바로가기</a>
                </div>
            </div>

            </body>
            </html>";
        return $return;
    }
    private function register_form_update_mail2($mb_name, $mb_id, $mb_recommend)
    {
        $config = $this->load->get_var('config');
        $return = "
        <!doctype html>
        <html lang=\"ko\">
        <head>
        <meta charset=\"utf-8\">
        <title>회원가입 알림 메일</title>
        </head>

        <body>

        <div style=\"margin:30px auto;width:600px;border:10px solid #f7f7f7\">
            <div style=\"border:1px solid #dedede\">
                <h1 style=\"padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em\">
                    회원가입 알림 메일
                </h1>
                <span style=\"display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right\">
                    <a href=\"" . base_url() . "\" target=\"_blank\">{$config['company_name']}</a>
                </span>
                <p style=\"margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee\">
                    <b>{$mb_name}</b>님께서 회원가입 하셨습니다.<br>
                    회원 아이디 : <b>{$mb_id}</b><br>
                    회원 이름 : {$mb_name}<br>
                    추천인아이디 : {$mb_recommend}
                </p>

                <a href=\"" . base_url() . "\" target=\"_blank\" style=\"display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center\">사이트바로가기</a>
            </div>
        </div>

        </body>
        </html>";
    }

    /**
     *  파일 첨부 기능만 (통장 사본, 사업자 등록증 이미지 파일)
     *    파일 이름 + '_' + 시간  형식, 8mb 이하, jpg,jpeg,png,pdf만 가능

     * *$upload_path 업로드할 파일 경로
     * *$number 파일 숫자( bank, company)
     * *$input_file 파일 이름
     * 
     */
    private function upload_file($upload_path, $number, $input_file, $file_name_type, $user_id)
    {
        date_default_timezone_set('Asia/Seoul'); // 한국 시간으로 설정

        if (!isset($_FILES[$input_file]) || $_FILES[$input_file]['error'] !== UPLOAD_ERR_OK) {
            echo "파일 업로드 실패: {$input_file}";
            return;
        }

        //해당 경로에 디렉토리가 없으면 디렉토리 생성
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png|pdf';
        $config['max_size'] = 8192; // 8mb

        $original_name = $_FILES[$input_file]['name'];
        $ext = pathinfo($original_name, PATHINFO_EXTENSION); // .jpg
        //$base = pathinfo($original_name, PATHINFO_FILENAME);


        $new_file_name =  $file_name_type . '_' . $user_id . '_' . $number . '.' . $ext;
        $full_path = $config['upload_path'] . $new_file_name;

        //    // 동일한 이름이 이미 존재하면 실패 처리
        if (file_exists($full_path)) {
            echo json_encode([
                'status' => false,
                'message' => '이미 같은 이름의 파일이 존재합니다.'
            ]);
            return;
        }

        $config['file_name'] = $new_file_name;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($input_file)) {
            $error = $this->upload->display_errors('', '');
            echo json_encode([
                'status' => false,
                'message' => '파일 업로드 실패: ' . $error
            ]);
            return;
        }

        $upload_data = $this->upload->data();

        return [
            'status' => true,
            'message' => '파일 업로드 성공',
            'file_name' => $upload_data['file_name'],
            'file_path' => $upload_data['full_path'],
            'original_name' => $upload_data['orig_name'],
            'file_size_kb' => $upload_data['file_size'],
            'mime_type' => $upload_data['file_type'],
        ];
    }


    #login_check
    public function login_check()
    {

        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $config = $this->load->get_var('config');

        $mb_id = trim($this->input->post('mb_id'));
        $mb_password = trim($this->input->post('mb_password'));
        if (!$mb_id || !$mb_password)
            alert('회원아이디나 비밀번호가 공백이면 안됩니다.');
        $mb = $this->global_lib->get_member($mb_id);
        // 가입된 회원이 아니다. 패스워드가 틀리다. 라는 메세지를 따로 보여주지 않는 이유는
        // 회원아이디를 입력해 보고 맞으면 또 패스워드를 입력해보는 경우를 방지하기 위해서입니다.
        // 불법사용자의 경우 회원아이디가 틀린지, 패스워드가 틀린지를 알기까지는 많은 시간이 소요되기 때문입니다.
        if (!$mb['id'] || !$this->funcomplex_lib->check_password($mb_password, $mb['passwd'], $mb_id)) {
            alert('가입된 회원아이디가 아니거나 비밀번호가 틀립니다.\\n비밀번호는 대소문자를 구분합니다.');
        }
        // 차단된 아이디인가?
        if ($mb['intercept_date'] && $mb['intercept_date'] <= date("Ymd", time())) {
            $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1년 \\2월 \\3일", $mb['intercept_date']);
            alert('회원님의 아이디는 접근이 금지되어 있습니다.\\n처리일 : ' . $date);
        }
        // 인트로 사용시 승인된 회원인지 체크
        if (!$this->gvcomplex_lib->is_admin($mb['grade']) && !$mb['use_app'] && $config['cert_admin_yes']) {
            alert("승인 된 회원만 로그인 가능합니다.");
        }
        // 관리비를 사용중일때 기간이 만료되었다면 로그인 차단
        if ($config['pf_expire_use'] && $config['pf_login_no']) {
            if ($this->funcomplex_lib->is_partner($mb['id']) && !is_null_time($mb['term_date'])) {
                if ($mb['term_date'] < $OM_TIME_YMD) {
                    alert("회원님의 아이디는 관리비 미납으로 접근이 금지되어 있습니다.");
                }
            }
        }
        // 회원아이디 세션 생성
        set_session('ss_mb_id', $mb['id']);

        // 인증 링크는 한번만 처리가 되게 한다.
        $sql = "update shop_member set lost_certify = '' where id = ?";
        $param = array($mb['id']);
        $this->query_model->returnNull($sql, $param);

        // FLASH XSS 공격에 대응하기 위하여 회원의 고유키를 생성해 놓는다. 관리자에서 검사함 - 110106
        set_session('ss_mb_key', md5($mb['reg_time'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));

        // 포인트 체크
        $sum_point = $this->global_lib->get_point_sum($mb['id']);

        $sql = " update shop_member set point = ? where id = ?";
        $data = array($sum_point, $mb['id']);
        $this->query_model->returnNull($sql, $data);
        // 자동로그인 : 아이디 쿠키에 한달간 저장
        $auto_login = $this->input->post('auto_login');
        if ($auto_login) {
            // 쿠키 한달간 저장
            $key = md5($_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $mb['passwd']);
            set_cookie('ck_mb_id', $mb['id'], 86400 * 31);
            set_cookie('ck_auto', $key, 86400 * 31);
        } else {
            set_cookie('ck_mb_id', '', 0);
            set_cookie('ck_auto', '', 0);
        }
        $url = $this->input->post('url');
        if ($url) {
            // url 체크
            $this->funcomplex_lib->check_url_host($url);

            $link = urldecode($url);
            // 2003-06-14 추가 (다른 변수들을 넘겨주기 위함)
            if (preg_match("/\?/", $link))
                $split = "&amp;";
            else
                $split = "?";

            // $_POST 배열변수에서 아래의 이름을 가지지 않은 것만 넘김
            $post_check_keys = array('mb_id', 'mb_password', 'x', 'y', 'url', 'slr_url');

            foreach ($_POST as $key => $value) {
                if ($key && !in_array($key, $post_check_keys)) {
                    $link .= "$split$key=$value";
                    $split = "&amp;";
                }
            }
        } else {
            $link = base_url() . 'm/main';
        }
        goto_url($link);
    }
    #비번찾기시 비번전송할때 쓰는 함수 호출
    public function password_lost2()
    {
        include_once('./lib/mailer.lib.php');
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $is_member = $this->load->get_var('is_member');
        $config = $this->load->get_var('config');
        $super = $this->load->get_var('super');

        if ($is_member) {
            alert_close("이미 로그인중입니다.");
        }
        if ($_POST["token"] && get_session("ss_token") == $_POST["token"]) {
            // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
            set_session("ss_token", "");
        } else {
            alert_close("잘못된 접근 입니다.");
            exit;
        }
        $email = trim($_POST['mb_email']);
        if (!$email)
            alert_close('메일주소 오류입니다.');

        $sql = " select count(*) as cnt from shop_member where email = ?";
        $data = array($email);
        $row = $this->query_model->returnOneArr($sql, $data);
        if ($row['cnt'] > 1)
            alert('동일한 메일주소가 2개 이상 존재합니다.\\n\\n관리자에게 문의하여 주십시오.');

        $sql = " select index_no, id, name, email from shop_member where email = ?";
        $mb = $this->query_model->returnOneArr($sql, $data);
        if (!$mb['id'])
            alert('존재하지 않는 회원입니다.');

        // 임시비밀번호 발급
        $change_password = rand(100000, 999999);
        $lost_certify = $this->global_lib->changePassword($change_password, $mb['id']);

        // 어떠한 회원정보도 포함되지 않은 일회용 난수를 생성하여 인증에 사용
        $mb_nonce = md5(pack('V*', rand(), rand(), rand(), rand()));

        $temp = $mb_nonce . $lost_certify;

        // 임시비밀번호와 난수를 lost_certify 필드에 저장
        $sql = " update shop_member set lost_certify = ? where id = ? ";
        $param = array($temp, $mb['id']);
        $this->query_model->returnNull($sql, $param);

        // 인증 링크 생성
        $href = base_url() . 'process/password_lost_certify?mb_no=' . $mb['index_no'] . '&mb_nonce=' . $mb_nonce;

        $subject = "[" . $config['company_name'] . "] 요청하신 회원정보 찾기 안내 메일입니다.";

        $content = "";

        $content .= '<div style="margin:30px auto;width:600px;border:10px solid #f7f7f7">';
        $content .= '<div style="border:1px solid #dedede">';
        $content .= '<h1 style="padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em">';
        $content .= '회원정보 찾기 안내';
        $content .= '</h1>';
        $content .= '<span style="display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right">';
        $content .= '<a href="' . base_url() . '" target="_blank">' . $config['company_name'] . '</a>';
        $content .= '</span>';
        $content .= '<p style="margin:20px 0 0;padding:30px 30px 30px;border-bottom:1px solid #eee;line-height:1.7em">';
        $content .= addslashes($mb['name']) . " 회원님은 " . $OM_TIME_YMDHIS . " 에 회원정보 찾기 요청을 하셨습니다.<br>";
        $content .= '저희 사이트는 관리자라도 회원님의 비밀번호를 알 수 없기 때문에, 비밀번호를 알려드리는 대신 새로운 비밀번호를 생성하여 안내 해드리고 있습니다.<br>';
        $content .= '아래에서 변경될 비밀번호를 확인하신 후, <span style="color:#ff3061"><strong>비밀번호 변경</strong> 링크를 클릭 하십시오.</span><br>';
        $content .= '비밀번호가 변경되었다는 인증 메세지가 출력되면 홈페이지에서 회원아이디와 변경된 비밀번호를 입력하시고 로그인 하십시오.<br>';
        $content .= '로그인 후에는 정보수정 메뉴에서 새로운 비밀번호로 변경해 주십시오.';
        $content .= '</p>';
        $content .= '<p style="margin:0;padding:30px 30px 30px;border-bottom:1px solid #eee;line-height:1.7em">';
        $content .= '<span style="display:inline-block;width:140px">회원아이디</span> ' . $mb['id'] . '<br>';
        $content .= '<span style="display:inline-block;width:140px">변경될 비밀번호</span> <strong style="color:#ff3061">' . $change_password . '</strong>';
        $content .= '</p>';
        $content .= '<a href="' . $href . '" target="_blank" style="display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center">비밀번호 변경</a>';
        $content .= '</div>';
        $content .= '</div>';

        mailer($config['company_name'], $super['email'], $mb['email'], $subject, $content, 1);

        alert($email . ' 메일로 회원아이디와 비밀번호를 인증할 수 있는 메일이 발송 되었습니다.\\n\\n메일을 확인하여 주십시오.', base_url() . 'm/bbs/login');
    }
    #로그아웃
    public function logout()
    {
        // ��ȣ��� ���� �ڵ�
        session_unset(); // ��� ���Ǻ����� �������� ������ 
        session_destroy(); // ���������� 

        // �ڵ��α��� ���� --------------------------------
        set_cookie("ck_mb_id", "", 0);
        set_cookie("ck_auto", "", 0);
        // �ڵ��α��� ���� end --------------------------------
        $url = $this->input->get('url');
        if ($url) {
            $p = parse_url($url);
            if ($p['scheme'] || $p['host']) {
                alert("url�� �������� ������ �� �����ϴ�.");
            }

            $link = $url;
        } else {
            $link = base_url() . 'm/main';
        }

        goto_url($link);
    }
    #board_write_update
    public function board_write_update()
    {
        include_once("./lib/mailer.lib.php");
        $member = $this->load->get_var('member');
        $is_member = $this->load->get_var('is_member');
        $pt_id = $this->load->get_var('pt_id');
        $board = $this->load->get_var('board');
        $super = $this->load->get_var('super');
        $page     = $this->input->post('page');
        $index_no = $this->input->post('index_no');

        $boardid = $this->input->post('boardid');
        $token = trim($this->input->post("token"));
        $memo = addslashes($this->input->post("memo"));
        log_message("debug", "memo: {$memo}");
        $subject = addslashes($this->input->post('subject'));
        $writer_s = trim($this->input->post("writer_s"));

        $havehtml = trim($this->input->post("havehtml"));
        $btype = trim($this->input->post("btype"));
        $issecret = trim($this->input->post("issecret"));
        $w = $this->input->post('w');
        $btype = $this->input->post('btype');
        $btype = ($btype == "") ? 0 : $btype;
        $ca_name = $this->input->post('ca_name');
        $issecret = $this->input->post('issecret');
        $havehtml = $this->input->post('havehtml');
        $writer_s = $this->input->post('writer_s');
        $passwd = $this->input->post('passwd');
        $average = $this->input->post('average');
        $product = $this->input->post('product');
        $qstr = $this->load->get_var('qstr');

        $del_file1 = $this->input->post('del_file1');
        $del_file2 = $this->input->post('del_file2');

        $uploadPath = "./data/board/" . $boardid;
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }



        if (!empty($index_no) && $boardid == 37)
            $redirect_url = base_url() . "m/bbs/board_read?index_no={$index_no}&boardid={$boardid}&page={$page}";
        else if (!empty($index_no) && $boardid == 13)
            $redirect_url = base_url() . "m/bbs/board_read?index_no={$index_no}&boardid={$boardid}&page={$page}";
        else if (empty($index_no) && $boardid == 37)
            $redirect_url = base_url() . "m/bbs/goods_request_list?boardid={$boardid}&page={$page}";
        else if (empty($index_no) && $boardid == 13)
            $redirect_url = base_url() . "m/bbs/noticeList?boardid={$boardid}&page={$page}";

        if (isset($token) && get_session("ss_token") == $token) {
            // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
            set_session("ss_token", "");
        } else {
            alert("잘못된 접근 입니다.");
            exit;
        }
        if (substr_count($memo, "&#") > 50) {
            alert("내용에 올바르지 않은 코드가 다수 포함되어 있습니다.");
        }
        if (!$subject)
            alert("제목을 입력하세요.");

        if (!$writer_s)
            alert("작성자명이 없습니다.");
        $upload_max_filesize = ini_get('upload_max_filesize');
        if (empty($_POST))
            alert("파일 또는 글내용의 크기가 서버에서 설정한 값을 넘어 오류가 발생하였습니다.\\n\\npost_max_size=" . ini_get('post_max_size') . " , upload_max_filesize=$upload_max_filesize\\n\\n게시판관리자 또는 서버관리자에게 문의 바랍니다.");

        $writer = 0;

        if (isset($havehtml) && $havehtml != 'Y')    $havehtml = "N";
        if (isset($btype) && $btype != '1') $btype = '2';
        if (isset($issecret) && $issecret != 'Y') $issecret = "N";
        if ($is_member) $writer = $member['index_no'];

        $fid = $this->global_lib->get_next_num("shop_board_" . $boardid);

        $uploadPath = "./data/board/" . $boardid;
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

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

        if ($w == "") {


            $sql_common = ", btype	= '{$btype}'
                            , ca_name	= '{$ca_name}'
                            , issecret	= '{$issecret}'
                            , havehtml	= '{$havehtml}'
                            , writer	= '{$writer}'
                            , writer_s	= '{$writer_s}'
                            , subject	= '{$subject}'
                            , memo		= '{$memo}'
                            , passwd	= '{$passwd}'
                            , average	= '{$average}'
                            , product	= '{$product}'
                            , pt_id	= '{$pt_id}' ";

            if (isset($del_file1)) {
                unlink($uploadPath . '/' . $del_file1);
                delete_board_thumbnail($boardid, $del_file1);
                $sql_common .= " , fileurl1 = '' ";
            }
            if (isset($del_file2)) {
                unlink($uploadPath . '/' . $del_file2);
                delete_board_thumbnail($boardid, $del_file2);
                $sql_common .= " , fileurl2 = '' ";
            }

            #파일이 있으면 파일을 업로드함
            $uploadFile = array('file1' => '', 'file2' => '');
            $error = "";
            if (isset($_FILES['file1']['name']) && $_FILES['file1']['name'] != "") {
                if (!$this->upload->do_upload('file1')) {
                    $error = $this->upload->display_errors();
                } else {
                    $uploadFile['file1'] = $this->upload->data();
                    $new_file1 = $uploadFile['file1']['file_name'];
                    $sql_common .= " , fileurl1 = '$new_file1' ";
                }
            }
            if (isset($_FILES['file2']['name']) && $_FILES['file2']['name'] != "") {
                if (!$this->upload->do_upload('file2')) {
                    $error = $this->upload->display_errors();
                } else {
                    $uploadFile['file2'] = $this->upload->data();
                    $new_file2 = $uploadFile['file2']['file_name'];
                    $sql_common .= " , fileurl2 = '$new_file2' ";
                }
            }

            if ($error != "") {
                echo "<script>alert('" . strip_tags($error) . "')</script>";
            }

            $sql = " insert into shop_board_{$boardid}
                        set fid		= ?
                        , wdate	= '" . time() . "'
                        , wip		= '{$_SERVER['REMOTE_ADDR']}'
                        , thread	= 'A'
                        {$sql_common} ";

            $param = array($fid);
            $this->query_model->returnNull($sql, $param);

            if ($member['email']) {
                $wr_subject = $this->funcomplex_lib->get_text(stripslashes($subject));
                $wr_content = $this->funcomplex_lib->conv_content(conv_unescape_nl(stripslashes($memo)), 1);
                $wr_name = $this->funcomplex_lib->get_text($member['name']);

                $subject = '[' . $board['boardname'] . '] 게시판에 새글이 등록되었습니다.';

                $content = $this->write_update_mail($wr_subject, $wr_name, $wr_content);

                mailer($member['name'], $member['email'], $super['email'], $subject, $content, 1);
            }

            goto_url($redirect_url);
        }
        //업데이트라면
        else if ($w == "u") {
            $_POST = array_map('trim', $_POST);
            $qstr = $this->load->get_var('qstr');

            $passwd = $this->input->post('passwd');

            if ($havehtml != 'Y')    $havehtml = "N";
            if ($btype != '1') $btype = '2';
            if ($issecret != 'Y') $issecret = "N";
            if ($is_member) $writer = $member['index_no'];

            $sql = "select * from shop_board_{$boardid} where index_no=?";
            $param = array($index_no);
            $row = $this->query_model->returnOneArr($sql, $param);

            $m = 2;
            if ($this->gvcomplex_lib->is_admin()) {
                $m = 1;
            } else {
                if ($row['writer'] == 0) {
                    if ($passwd != $row['passwd']) {
                        alert('비밀번호가 맞지않습니다.');
                    }
                    $m = 1;
                } else {
                    if ($row['writer'] == $mb_no) {
                        $m = 1;
                    }
                }
            }

            if ($m == 1) {
                $updateObj = array(
                    "writer_s" => $writer_s,
                    "btype" => $btype,
                    "ca_name" => $ca_name,
                    "issecret" => $issecret,
                    "havehtml" => $havehtml,
                    'subject' => $subject,
                    "memo" => $memo,
                    "average" => $average,
                    "product" => $product
                );

                if (!empty($del_file1)) {
                    unlink($uploadPath . '/' . $del_file1);
                    delete_board_thumbnail($boardid, $del_file1);
                    $updateObj['fileurl1'] = '';
                }
                if (!empty($del_file1)) {
                    unlink($uploadPath . '/' . $del_file2);
                    delete_board_thumbnail($boardid, $del_file2);
                    $updateObj['fileurl2'] = '';
                }

                #파일이 있으면 파일을 업로드함
                $uploadFile = array('file1' => '', 'file2' => '');
                $error = "";
                if (isset($_FILES['file1']['name']) && $_FILES['file1']['name'] != "") {
                    if (!$this->upload->do_upload('file1')) {
                        $error = $this->upload->display_errors();
                    } else {
                        $uploadFile['file1'] = $this->upload->data();
                        $new_file1 = $uploadFile['file1']['file_name'];
                        $updateObj['fileurl1'] = $new_file1;
                    }
                }
                if (isset($_FILES['file2']['name']) && $_FILES['file2']['name'] != "") {
                    if (!$this->upload->do_upload('file2')) {
                        $error = $this->upload->display_errors();
                    } else {
                        $uploadFile['file2'] = $this->upload->data();
                        $new_file2 = $uploadFile['file2']['file_name'];
                        $updateObj['fileurl2'] = $new_file2;
                    }
                }

                if ($error != "") {
                    echo "<script>alert('" . strip_tags($error) . "')</script>";
                }

                $this->query_model->updateObj("shop_board_{$boardid}", $updateObj, "index_no	= '$index_no'");
                goto_url($redirect_url);
            }
        } else if ($w == "r") {
            $sql = "select fid,thread,passwd from shop_board_{$boardid} where index_no=?";
            $param = array($index_no);
            $res = $this->query_model->returnArr($sql, $param);

            $fid = $res[0]['fid'];
            $thread = $res[0]['thread'];
            $passwd = $res[0]['passwd'];

            $sql = "select thread,right(thread,1)
                      from shop_board_{$boardid}
                     where fid = '$fid'
                       and length(thread) = length('$thread')+1
                       and locate('$thread',thread) = 1
                     order by thread desc limit 1 ";
            $r = $this->query_model->returnQueryObj($sql);
            if (!$r)
                alert('리플글 작성에 실패 하였습니다.');

            $rows = $r->num_rows();
            if ($rows) {
                $row = $r->result_array();
                $thread_head = substr($row[0], 0, -1);
                $thread_foot = ++$row[1];
                $new_thread = $thread_head . $thread_foot;
            } else {
                $new_thread = $thread . "A";
            }
            $sql_common = " btype		= '$btype',
                            ca_name		= '$ca_name',
                            issecret	= '$issecret',
                            havehtml	= '$havehtml',
                            writer      = '$writer',
                            writer_s	= '$writer_s',
                            subject		= '$subject',
                            memo		= '$memo',
                            passwd      = '$passwd',
                            average		= '$average',
                            product		= '$product',
                            pt_id		= '$pt_id' ";

            if (isset($del_file1)) {
                unlink($uploadPath . '/' . $del_file1);
                delete_board_thumbnail($boardid, $del_file1);
                $sql_common .= " , fileurl1 = '' ";
            }
            if (isset($del_file2)) {
                unlink($uploadPath . '/' . $del_file2);
                delete_board_thumbnail($boardid, $del_file2);
                $sql_common .= " , fileurl2 = '' ";
            }

            #파일이 있으면 파일을 업로드함
            $uploadFile = array('file1' => '', 'file2' => '');
            $error = "";
            if (isset($_FILES['file1']['name']) && $_FILES['file1']['name'] != "") {
                if (!$this->upload->do_upload('file1')) {
                    $error = $this->upload->display_errors();
                } else {
                    $uploadFile['file1'] = $this->upload->data();
                    $new_file1 = $uploadFile['file1']['file_name'];
                    $sql_common .= " , fileurl1 = '$new_file1' ";
                }
            }
            if (isset($_FILES['file2']['name']) && $_FILES['file2']['name'] != "") {
                if (!$this->upload->do_upload('file2')) {
                    $error = $this->upload->display_errors();
                } else {
                    $uploadFile['file2'] = $this->upload->data();
                    $new_file2 = $uploadFile['file2']['file_name'];
                    $sql_common .= " , fileurl2 = '$new_file2' ";
                }
            }

            if ($error != "") {
                echo "<script>alert('" . strip_tags($error) . "')</script>";
            }

            $sql = " insert into shop_board_{$boardid}
                        set fid = '$fid',
                            wdate = '" . time() . "',
                            wip = '{$_SERVER['REMOTE_ADDR']}',
                            thread = '$new_thread',
                            $sql_common ";
            $this->query_model->returnNull($sql);
            goto_url(base_url() . "m/bbs/noticeList?boardid=$boardid");
        } else {
            alert("정상적인 접근이 아닙니다.");
        }
    }
    #1대1상담 문의 삭제
    public function qna_read_delete()
    {
        $member = $this->load->get_var('member');
        $index_no = $this->input->get('index_no');
        $sql = "select * from shop_qa where index_no=?";
        $param = array($index_no);
        $row = $this->query_model->returnOneArr($sql, $param);
        if ($row['mb_id'] != $member['id']) {
            if (!$this->gvcomplex_lib->is_admin()) {
                alert("삭제 권한이 없습니다.");
            }
        }
        $sql = "delete from shop_qa where index_no=?";
        $param = array($index_no);
        $this->query_model->returnNull($sql, $param);
        goto_url(base_url() . "m/bbs/qna_list");
    }
    #상담 글작성 함수
    public function qna_write()
    {
        include_once('./lib/mailer.lib.php');
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

        $member = $this->load->get_var('member');
        $super = $this->load->get_var('super');
        $super_hp = $this->load->get_var('super_hp');

        if ($_POST["token"] && get_session("ss_token") == $_POST["token"]) {
            // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
            set_session("ss_token", "");
        } else {
            alert("잘못된 접근 입니다.");
            exit;
        }

        $subject = trim(strip_tags($_POST['subject']));

        if (substr_count($_POST['memo'], "&#") > 50) {
            alert("내용에 올바르지 않은 코드가 다수 포함되어 있습니다.");
        }

        $_POST['memo'] = addslashes($_POST['memo']);

        $memo = addslashes($this->input->post('memo'));
        $email = addslashes($this->input->post('email'));
        $cellphone = $this->input->post('cellphone');
        $cellphone = ($cellphone) ? replace_tel($cellphone) : "";

        unset($value);
        $email_send_yes = $this->input->post('email_send_yes');
        $sms_send_yes = $this->input->post('sms_send_yes');
        $value['mb_id']             = $member['id'];
        $value['subject']         = $subject;
        $value['memo']             = $memo;
        $value['catename']         = $_POST['catename'];
        $value['email']             = $email;
        $value['email_send_yes'] = isset($email_send_yes) ? $email_send_yes : "";
        $value['cellphone']         = $cellphone;
        $value['sms_send_yes']     = isset($sms_send_yes) ? $sms_send_yes : "";
        $value['ip']             = $_SERVER['REMOTE_ADDR'];
        $value['wdate']             = $OM_TIME_YMDHIS;
        $this->query_model->insertObj("shop_qa", $value);

        $wr_subject = $this->funcomplex_lib->get_text(stripslashes($subject));
        $wr_content = $this->funcomplex_lib->conv_content(conv_unescape_nl(stripslashes($_POST['memo'])), 0);
        $wr_name = $this->funcomplex_lib->get_text($member['name']);
        $subject = $wr_name . '님께서 1:1상담문의에 새글을 작성하셨습니다.';

        if ($email) {
            $content = $this->write_update_mail($wr_subject, $wr_name, $wr_content);
            mailer($wr_name, $email, $super['email'], $subject, $content, 1);
        }

        // 최고 관리자에게 문자전송
        $this->funcomplex_lib->moonjaro_direct_sms_send('admin', $super_hp, $subject);

        alert("정상적으로 접수 되었습니다. 빠른 시간안에 답변드리겠습니다", base_url() . "m/bbs/qna_list");
    }
    public function write_update()
    {
        include_once("./lib/mailer.lib.php");
        $member = $this->load->get_var('member');
        $is_member = $this->load->get_var('is_member');
        $pt_id = $this->load->get_var('pt_id');
        $board = $this->load->get_var('board');
        $super = $this->load->get_var('super');

        $boardid = $this->input->post('boardid');
        $token = trim($this->input->post("token"));
        $memo = addslashes($this->input->post("memo"));
        $subject = addslashes($this->input->post('subject'));
        $writer_s = trim($this->input->post("writer_s"));

        $havehtml = trim($this->input->post("havehtml"));
        $btype = trim($this->input->post("btype"));
        $issecret = trim($this->input->post("issecret"));
        $mode = $this->input->post('mode');
        $btype = $this->input->post('btype');
        $btype = ($btype == "") ? 0 : $btype;
        $ca_name = $this->input->post('ca_name');
        $issecret = $this->input->post('issecret');
        $havehtml = $this->input->post('havehtml');
        $writer_s = $this->input->post('writer_s');
        $passwd = $this->input->post('passwd');
        $average = $this->input->post('average');
        $product = $this->input->post('product');
        $del_file1 = $this->input->post('del_file1');
        $del_file2 = $this->input->post('del_file2');

        if ($token && get_session("ss_token") == $token) {
            // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
            set_session("ss_token", "");
        } else {
            alert("잘못된 접근 입니다.");
            exit;
        }

        if (substr_count($memo, "&#") > 50) {
            alert("내용에 올바르지 않은 코드가 다수 포함되어 있습니다.");
        }

        if (!$subject) {
            alert("제목을 입력하세요.");
        }
        if (!$writer_s) {
            alert("작성자명이 없습니다.");
        }

        /*
        $upload_max_filesize = ini_get('upload_max_filesize');
        if(empty($_POST))
            alert("파일 또는 글내용의 크기가 서버에서 설정한 값을 넘어 오류가 발생하였습니다.\\n\\npost_max_size=".ini_get('post_max_size')." , upload_max_filesize=$upload_max_filesize\\n\\n게시판관리자 또는 서버관리자에게 문의 바랍니다.");
        */
        $writer = 0;

        if (isset($havehtml) && $havehtml != 'Y')    $havehtml = "N";
        if (isset($btype) && $btype != '1') $btype = '2';
        if (isset($issecret) && $issecret != 'Y') $issecret = "N";
        if ($is_member) $writer = $member['index_no'];

        $fid = $this->global_lib->get_next_num("shop_board_" . $boardid);
        if ($mode == "w") {
            $sql_commend = ", btype	= '{$btype}'
                            , ca_name	= '{$ca_name}'
                            , issecret	= '{$issecret}'
                            , havehtml	= '{$havehtml}'
                            , writer	= '{$writer}'
                            , writer_s	= '{$writer_s}'
                            , subject	= '{$subject}'
                            , memo		= '{$memo}'
                            , passwd	= '{$passwd}'
                            , average	= '{$average}'
                            , product	= '{$product}'
                            , pt_id	= '{$pt_id}' ";
            if ($del_file1) {
                unlink($uploadPath . '/' . $del_file1);
                delete_board_thumbnail($boardid, $del_file1);
                $sql_commend .= " , fileurl1 = '' ";
            }
            if ($del_file2) {
                unlink($uploadPath . '/' . $del_file2);
                delete_board_thumbnail($boardid, $del_file2);
                $sql_commend .= " , fileurl2 = '' ";
            }

            if (isset($_FILES['file1']['name']) && $_FILES['file1']['name'] != "") {
                if (!$this->upload->do_upload('file1')) {
                    $error = $this->upload->display_errors();
                } else {
                    $uploadFile['file1'] = $this->upload->data();
                    $new_file1 = $uploadFile['file1']['file_name'];
                }
                $sql_commend .= " , fileurl1 = '$new_file1' ";
            }
            $new_file2 = "";
            if (isset($_FILES['file2']['name']) && $_FILES['file2']['name'] != "") {
                if (!$this->upload->do_upload('file2')) {
                    $error = $this->upload->display_errors();
                } else {
                    $uploadFile['file2'] = $this->upload->data();
                    $new_file2 = $uploadFile['file2']['file_name'];
                }
                $sql_commend .= " , fileurl2 = '$new_file2' ";
            }
            if ($error != "") {
                echo "<script>alert('" . strip_tags($error) . "')</script>";
            }

            $sql = " insert into shop_board_{$boardid}
                        set fid		= ?
                        , wdate	= '" . time() . "'
                        , wip		= '{$_SERVER['REMOTE_ADDR']}'
                        , thread	= 'A'
                        {$sql_commend} ";
            $oaram = array($fid);
            $this->query_model->returnNull($sql, $oaram);

            if ($member['email']) {
                $wr_subject = $this->funcomplex_lib->get_text(stripslashes($subject));
                $wr_content = $this->funcomplex_lib->conv_content(conv_unescape_nl(stripslashes($memo)), 1);
                $wr_name = $this->funcomplex_lib->get_text($member['name']);

                $subject = '[' . $board['boardname'] . '] 게시판에 새글이 등록되었습니다.';

                $content = $this->write_update_mail($wr_subject, $wr_name, $wr_content);

                mailer($member['name'], $member['email'], $super['email'], $subject, $content, 1);
            }

            goto_url(base_url() . "m/bbs/noticeList?boardid=$boardid");
        } else {
            alert("정상적인 접근이 아닙니다.");
        }
    }
    private function write_update_mail($wr_subject, $wr_name, $wr_content)
    {
        return "
            <!doctype html>
                <html lang=\"ko\">
                <head>
                <meta charset=\"utf-8\">
                <title>{$wr_subject} 메일</title>
                </head>

                <body>

                <div style=\"margin:30px auto;width:600px;border:10px solid #f7f7f7\">
                    <div style=\"border:1px solid #dedede\">
                        <h1 style=\"padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em\">
                            {$wr_subject}
                        </h1>
                        <span style=\"display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right\">
                            작성자 : {$wr_name}
                        </span>
                        <div style=\"margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee\">
                            {$wr_content}
                        </div>
                    </div>
                </div>

                </body>
            </html>";
    }
    #답장처리
    public function qna_modify()
    {
        $index_no = $this->input->post('index_no');
        if ($_POST["token"] && get_session("ss_token") == $_POST["token"]) {
            // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
            set_session("ss_token", "");
        } else {
            alert("잘못된 접근 입니다.");
            exit;
        }

        $subject = trim(strip_tags($_POST['subject']));

        if (substr_count($_POST['memo'], "&#") > 50) {
            alert("내용에 올바르지 않은 코드가 다수 포함되어 있습니다.");
        }
        $memo = "";
        if (!get_magic_quotes_gpc()) {
            $memo = addslashes($this->input->post('memo'));
        }

        unset($value);
        $email_send_yes = $this->input->post('email_send_yes');
        $sms_send_yes = $this->input->post('sms_send_yes');
        $value['subject']         = $subject;
        $value['memo']             = $memo;
        $value['catename']         = $this->input->post('catename');
        $value['email']             = $this->input->post('email');
        $value['email_send_yes'] = ($email_send_yes) ? $email_send_yes : "";
        $value['cellphone']         = replace_tel($this->input->post('cellphone'));
        $value['sms_send_yes']     = ($sms_send_yes) ? $sms_send_yes : "";
        $value['ip']             = $_SERVER['REMOTE_ADDR'];
        $this->query_model->updateObj("shop_qa", $value, "index_no='$index_no'");

        goto_url(base_url() . "m/bbs/qna_read?index_no=$index_no");
    }
    #분양신청 신청하기
    public function partner_reg_update()
    {
        include_once('./lib/mailer.lib.php');
        $is_member = $this->load->get_var('is_member');
        $partner = $this->load->get_var('partner');
        $member = $this->load->get_var('member');
        $super_hp = $this->load->get_var('super_hp');

        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

        if (!$config['partner_reg_yes']) {
            alert('서비스가 일시 중단 되었습니다.', base_url() . 'm/main');
        }

        if (!$is_member) {
            alert("로그인 후 신청 가능합니다.");
        }

        if ($this->gvcomplex_lib->is_admin()) {
            alert('관리자는 신청을 하실 수 없습니다.');
        }

        if ($partner['mb_id']) {
            alert('중복된 신청은 하실 수 없습니다.');
        }

        if ($_POST["token"] && get_session("ss_token") == $_POST["token"]) {
            // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
            set_session("ss_token", "");
        } else {
            alert("잘못된 접근 입니다.");
            exit;
        }
        $mb_id = $member['id'];
        $bank_holder = $this->input->post('bank_holder');
        $bank_account = $this->input->post('bank_account');
        $bank_name = $this->input->post('bank_name');
        $memo = $this->input->post('memo');
        $anew_grade = $this->input->post('anew_grade');
        $receipt_price = $this->input->post('receipt_price');
        $deposit_name = $this->input->post('deposit_name');
        $pay_settle_case = $this->input->post('pay_settle_case');
        $bank_acc = $this->input->post('bank_acc');
        $signatureJSON = $this->input->post('signatureJSON');
        $reg_ip = $_SERVER['REMOTE_ADDR'];
        $reg_time = $OM_TIME_YMDHIS;
        $update_time = $OM_TIME_YMDHIS;
        $value = array(
            'mb_id' => $mb_id,
            'bank_holder' => $bank_holder,
            'bank_account' => $bank_account,
            'memo' => $memo,
            'anew_grade' => $anew_grade,
            'receipt_price' => $receipt_price,
            'deposit_name' => $deposit_name,
            'pay_settle_case' => $pay_settle_case,
            'bank_acc' => $bank_acc,
            'receipt_price' => $receipt_price,
            'signatureJSON' => $signatureJSON,
            'reg_ip' => $reg_ip,
            'reg_time' => $reg_time,
            'update_time' => $update_time
        );
        $this->query_model->insertObj('shop_partner', $value);

        $wr_content = $this->funcomplex_lib->conv_content(conv_unescape_nl(stripslashes($_POST['memo'])), 0);
        $wr_name = $this->funcomplex_lib->get_text($member['name']);
        $subject = $wr_name . '님께서 분양신청을 하셨습니다.';
        if ($member['email']) {
            $content = $this->partner_reg_update_mail($wr_name, $wr_content);
            mailer($member['name'], $member['email'], $super['email'], $subject, $content, 1);
        }

        // 최고 관리자에게 문자전송
        $this->funcomplex_lib->moonjaro_direct_sms_send('admin', $super_hp, $subject);
        alert('정상적으로 신청 되었습니다.', base_url() . 'm/bbs/partner_reg');
    }
    private function partner_reg_update_mail($wr_name, $wr_content)
    {
        $config = $this->load->get_var('config');
        return "
        <!doctype html>
            <html lang=\"ko\">
            <head>
            <meta charset=\"utf-8\">
            <title>쇼핑몰 분양신청 메일</title>
            </head>

            <body>

                <div style=\"margin:30px auto;width:600px;border:10px solid #f7f7f7\">
                    <div style=\"border:1px solid #dedede\">
                        <h1 style=\"padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em\">
                            쇼핑몰 분양신청 메일
                        </h1>
                        <span style=\"display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right\">
                            <a href=\"" . base_url() . "\" target=\"_blank\">" . $config['company_name'] . "</a>
                        </span>
                        <div style=\"margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee\">
                            신청자명 <b>{$wr_name}</b><br><br>{$wr_content}
                        </div>

                        <a href=\"" . base_url() . "\" target=\"_blank\" style=\"display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center\">사이트바로가기</a>
                    </div>
                </div>
            </body>

            </html>";
    }
    #입양신청 처리하기
    #seller_reg_form_update
    public function seller_reg_form_update()
    {
        include_once('./lib/mailer.lib.php');
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $is_member = $this->load->get_var('is_member');
        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');
        $super = $this->load->get_var('super');

        if (!$config['seller_reg_yes']) {
            alert('서비스가 일시 중단 되었습니다.', base_url() . 'm/main');
        }

        if (!$is_member) {
            alert("로그인 후 신청 가능합니다.");
        }

        if ($this->gvcomplex_lib->is_admin()) {
            alert('관리자는 신청을 하실 수 없습니다.');
        }
        if ($_POST["token"] && get_session("ss_token") == $_POST["token"]) {
            // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
            set_session("ss_token", "");
        } else {
            alert("잘못된 접근 입니다.");
            exit;
        }
        $mb_id = $member['id'];
        $seller_code = $this->global_lib->code_uniqid();
        $seller_item = $this->input->post('seller_item');
        $company_name = $this->input->post('company_name');
        $company_saupja_no = $this->input->post('company_saupja_no');
        $company_item = $this->input->post('company_item');
        $company_service = $this->input->post('company_service');
        $company_owner = $this->input->post('company_owner');
        $company_tel = $this->input->post('company_tel');
        $company_fax = $this->input->post('company_fax');
        $company_zip = $this->input->post('company_zip');
        $company_addr1 = $this->input->post('company_addr1');
        $company_addr2 = $this->input->post('company_addr2');
        $company_addr3 = $this->input->post('company_addr3');
        $company_addr_jibeon = $this->input->post('company_addr_jibeon');
        $company_hompage = $this->input->post('company_hompage');
        $info_name = $this->input->post('info_name');
        $info_email = $this->input->post('info_email');
        $info_tel = $this->input->post('info_tel');
        $bank_name = $this->input->post('bank_name');
        $bank_account = $this->input->post('bank_account');
        $bank_holder = $this->input->post('bank_holder');
        $memo = $this->input->post('memo');
        $value = array(
            'mb_id' => $mb_id,
            'seller_code' => $seller_code,
            'seller_item' => $seller_item,
            'company_name' => $company_name,
            'company_saupja_no' => $company_saupja_no,
            'company_item' => $company_item,
            'company_service' => $company_service,
            'company_owner' => $company_owner,
            'company_tel' => $company_tel,
            'company_fax' => $company_fax,
            'company_zip' => $company_zip,
            'company_addr1' => $company_addr1,
            'company_addr2' => $company_addr2,
            'company_addr3' => $company_addr3,
            'company_addr_jibeon' => $company_addr_jibeon,
            'company_hompage' => $company_hompage,
            'info_name' => $info_name,
            'info_email' => $info_email,
            'info_tel' => $info_tel,
            'bank_name' => $bank_name,
            'bank_account' => $bank_account,
            'bank_holder' => $bank_holder,
            'memo' => $memo,
            'reg_time' => $OM_TIME_YMDHIS,
            'update_time' => $OM_TIME_YMDHIS
        );
        $this->query_model->insertObj('shop_seller', $value);
        $wr_content = $this->funcomplex_lib->conv_content(conv_unescape_nl(stripslashes($_POST['memo'])), 0);
        $wr_name = $this->funcomplex_lib->get_text($member['name']);
        $subject = '[' . $company_name . '] ' . $wr_name . '님께서 입점신청을 하셨습니다.';

        if ($member['email']) {
            $content = $this->seller_reg_form_update_mail($wr_name, $company_name, $wr_content);
            mailer($member['name'], $member['email'], $super['email'], $subject, $content, 1);
        }
        // 최고 관리자에게 문자전송
        $super_hp = $this->load->get_var('super_hp');
        $this->funcomplex_lib->moonjaro_direct_sms_send('admin', $super_hp, $subject);

        alert('정상적으로 신청 되었습니다.', base_url() . 'm/bbs/seller_reg_form');
    }
    private function seller_reg_form_update_mail($wr_name, $company_name, $wr_content)
    {
        $config = $this->load->get_var('config');
        return "
            <!doctype html>
            <html lang=\"ko\">
                <head>
                <meta charset=\"utf-8\">
                <title>쇼핑몰 입점신청 메일</title>
                </head>

                <body>

                <div style=\"margin:30px auto;width:600px;border:10px solid #f7f7f7\">
                    <div style=\"border:1px solid #dedede\">
                        <h1 style=\"padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em\">
                            쇼핑몰 입점신청 메일
                        </h1>
                        <span style=\"display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right\">
                            <a href=\"" . base_url() . "m/main\" target=\"_blank\">{$config['company_name']}</a>
                        </span>
                        <div style=\"margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee\">
                            신청자명 : <b>{$wr_name}</b><br>
                            업체명 : <b>{$company_name}</b><br><br>
                            {$wr_content}
                        </div>

                        <a href=\"" . base_url() . "m/main\" target=\"_blank\" style=\"display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center\">사이트바로가기</a>
                    </div>
                </div>

                </body>
            </html>";
    }
    #leave_form_update 회원탈퇴 처리
    public function leave_form_update()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');

        $memo = $this->input->post('memo');
        $other = $this->input->post('other');

        if (!$is_member) alert('회원만 접근하실 수 있습니다.');

        $mb_password = trim($this->input->post('mb_password'));
        if (!($mb_password && $this->funcomplex_lib->check_password($mb_password, $member['passwd'], $member['id'])))
            alert('비밀번호가 틀립니다.');

        if ($member['id'] == 'admin')
            alert('최고 관리자는 탈퇴하실 수 없습니다.', base_url() . 'm/main');

        // 회원삭제
        $this->funcomplex_lib->member_delete($member['id']);

        $sql = "insert into shop_member_leave
                   set mb_id = ?
                     , mb_name = ?
                     , memo = ?
                     , other = ?
                     , reg_time = ?";
        $param = array($member['id'], $member['name'], $memo, $other, $OM_TIME_YMDHIS);
        $this->query_model->returnNull($sql, $param);
        $super_hp = $this->load->get_var('super_hp');
        // 최고 관리자에게 문자전송
        $this->funcomplex_lib->moonjaro_direct_sms_send('admin', $super_hp, $member['name'] . '님께서 회원에서 탈퇴 하셨습니다.');

        // 3.09 수정 (로그아웃)
        unset($_SESSION['ss_mb_id']);

        alert($member['name'] . '님께서는 ' . date("Y년 m월 d일") . '에 회원에서 탈퇴 하셨습니다.', base_url() . 'm/main');
    }
    #orderinquirycancel 주문취소했을때
    public function orderinquirycancel()
    {
        $default = $this->load->get_var('default');
        $member = $this->load->get_var('member');
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $token = $this->input->post('token');
        $od_id = $this->input->post('od_id');
        $member = $this->load->get_var('member');

        if ($token && get_session("ss_token") == $token) {
            // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
            set_session("ss_token", "");
        } else {
            set_session("ss_token", "");
            alert("토큰 에러", base_url());
        }

        $sql = "SELECT * FROM shop_order WHERE od_id = ? AND mb_id = ?";
        $param = array($od_id, $member['id']);
        $od = $this->query_model->returnOneArr($sql, $param);
        if (!$od['od_id']) {
            alert("존재하는 주문이 아닙니다.");
        }

        // 주문취소 가능여부 체크
        $od_count1 = $od_count2 = $od_cancel_price = 0;

        $sql = "SELECT dan, goods_price, baesong_price, cancel_price, gs_id FROM shop_order WHERE od_id = ? ORDER BY index_no ASC ";
        $param = array($od_id);
        $res = $this->query_model->returnArr($sql, $param);

        $amount = 0; // 결제(무통장, 카드, 계좌이체) 환불 금액

        // bid 환불 관련
        $amount_mileage = 0; // 예치금 환불 금액
        $amount_point = 0; // 포인트 환불 금액
        $cancel_amount = 0;

        foreach ($res as $key => $row) {
            $od_count1++;

            // 'dan' 값이 '1', '2', '3'일 경우에만 처리
            if (in_array($row['dan'], array('1', '2', '3'))) {
                $gs = $this->global_lib->get_goods($row['gs_id'], 'type, isopen');

                // 타임세일 상품이 종료되었을 경우
                if ($gs['type'] == "timesale" && $gs['isopen'] == 5) {
                    alert("판매가 종료된 상품으로 취소가 불가능합니다.");
                }

                // 타임세일 상품일 경우
                if ($gs['type'] == "timesale") {
                    // 타임세일 상품은 수수료 10% 제외 환불
                    $cancel_amount += ((int)$row['goods_price'] * 0.9) + (int)$row['baesong_price'];

                    // use_price가 있으면 환불 금액에 포함
                    if ($row['use_price'] > 0) {
                        $amount += $row['use_price'];
                        $cancel_amount -= $row['use_price'];
                    }
                } else {
                    // 타임세일이 아닌 경우
                    $amount += (int)$row['goods_price'] + (int)$row['baesong_price'];
                }

                $od_count2++;

                // 취소된 금액에 대한 가격 합산
                $od_cancel_price += (int)$row['cancel_price'];
            }
        }

        foreach ($res as $key => $row) {
            if (in_array($row['dan'], array('1', '2', '3'))) {
                $gs = $this->global_lib->get_goods($row['gs_id'], 'type, isopen');

                // 타임세일 상품일 경우
                if ($gs['type'] == "timesale") {
                    // Mileage 환불 처리
                    if ($row['use_mileage'] > 0 && $cancel_amount > 0) {
                        $mileage_to_refund = min($cancel_amount, $row['use_mileage']);
                        $amount_mileage += $mileage_to_refund;
                        $cancel_amount -= $mileage_to_refund;
                    }

                    // Point 환불 처리
                    if ($row['use_point'] > 0 && $cancel_amount > 0) {
                        $point_to_refund = min($cancel_amount, $row['use_point']);
                        $amount_point += $point_to_refund;
                        $cancel_amount -= $point_to_refund;
                    }
                }
            }
        }

        $uid = md5($od['od_id'] . $od['od_time'] . $od['od_ip']);
        if ($od_cancel_price > 0 || $od_count1 != $od_count2) {
            alert("취소할 수 있는 주문이 아닙니다.", base_url() . "shop/orderinquiryview?od_id=$od_id&uid=$uid");
        }

        // 주문 취소 사유
        $cancel_memo = $this->input->post('cancel_memo');
        $cancel_memo = addslashes(strip_tags($cancel_memo));

        $sql = "SELECT * FROM shop_order WHERE od_id = ? ORDER BY index_no ASC";
        $param = array($od_id);
        $res = $this->query_model->returnOneArr($sql, $param);

        // PG 결제 취소
        #3. 주문취소
        if ($res['paymethod'] != '무통장') {
            $imp_uid = $res['imp_uid'];
            $merchant_uid = $res['merchant_uid'];

            include_once('./Bill/iamport.php');
            $result = $iamport->cancel(array(
                'imp_uid'        => "$imp_uid",         //merchant_uid에 우선한다
                'merchant_uid'    => "$merchant_uid",     //imp_uid 또는 merchant_uid가 지정되어야 함
                'reason'        => "$cancel_memo",                //취소사유
                'amount'        => "$amount"
            ));

            if (!$result->success) {
                alert($result->error['message']);
                return false;
            }
        }

        $sql = "SELECT od_no FROM shop_order WHERE od_id = '$od_id' ORDER BY index_no ASC";
        $result = $this->query_model->returnArr($sql);
        foreach ($result as $key => $row) {
            $this->funcomplex_lib->change_order_status_6($row['od_no'], $amount);
        }

        // 메모남김
        $sql = " update shop_order set shop_memo = CONCAT(shop_memo,\"\\n주문자 본인 직접 취소 - " . $OM_TIME_YMDHIS . " (취소이유 : {$cancel_memo})\") where od_id = ?";
        $param = array($od_id);
        $this->query_model->returnNull($sql, $param);

        // 주문취소 문자전송
        $this->funcomplex_lib->moonjaro_order_sms_send($od['pt_id'], $od['cellphone'], $od_id, 5);

        goto_url(base_url() . "m/shop/orderinquiryview?od_id=$od_id&uid=$uid");
    }
    #찜하기 삭제
    public function wishupdate()
    {
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $w = $this->input->get('w');
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $gs_id = "";
        if (!$is_member) {
            alert('회원 전용 서비스 입니다.');
        }

        if ($w == "d") {
            $wi_id = trim($this->input->get('wi_id'));
            $sql = " delete from shop_wish where wi_id = ? and mb_id = ?";
            $data = array($wi_id, $member['id']);
            $this->query_model->returnNull($sql, $data);
        } else {
            $gs_id = $this->input->post('gs_id')[0];
            $sql = " select wi_id from shop_wish where mb_id = ? and gs_id = ?";
            $data = array($member['id'], $gs_id);
            $row = $this->query_model->returnOneArr($sql, $data);
            if ($row['wi_id']) { // 이미 있다면 삭제함
                $sql = "delete from shop_wish where wi_id = ?";
                $param = array($row['wi_id']);
                $this->query_model->returnNull($sql, $param);
            }

            $sql = "insert shop_wish set mb_id = ?,gs_id = ?,wi_time = ?,wi_ip = ?";
            $param = array($member['id'], $gs_id, $OM_TIME_YMDHIS, $_SERVER['REMOTE_ADDR']);
            $this->query_model->returnNull($sql, $param);
        }
        goto_url(base_url() . "m/shop/wish");
    }

    #cartupdate 상품카트에 담기
    public function cartupdate()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $act = $this->input->post('act');
        $is_member = $this->load->get_var('is_member');
        if (!$is_member) {
            alert('로그인 후 이용해주세요.', base_url() . "m/bbs/login");
        }

        $ca_id = $this->input->post('ca_id');
        $member = $this->load->get_var('member');
        $set_mb_id = $member['id'];

        $referralCode = $this->input->post('referralCode') ?? ""; // 리뷰 공유 코드
        $key = "share";
        $referralCode_decode = openssl_decrypt(base64_decode($referralCode), "AES-256-CBC", $key, true, str_repeat(chr(0), 16));

        $sw_direct = $this->input->post('sw_direct') ?? 0;
        set_session("sw_direct", $sw_direct);

        if ($act == "buy") {
            if (!count($this->input->post('ct_chk'))) {
                alert("주문하실 상품을 하나이상 선택해 주십시오.");
            }

            $comma = "";
            $ss_cart_id = "";
            set_session('ss_cart_id', '');

            $fldcnt = count($_POST['gs_id']);
            $allowed_types = []; // 허용된 타입을 저장할 배열
            $bid_count = 0; // bid 타입 상품 개수

            for ($i = 0; $i < $fldcnt; $i++) {
                $ct_chk = isset($_POST['ct_chk'][$i]) ? $_POST['ct_chk'][$i] : "";

                if ($ct_chk) {
                    $gs_id = $_POST['gs_id'][$i];
                    $index_no = $_POST['index_no'][$i];   // 장바구니 번호
                    $type = $this->global_lib->get_goods($gs_id, 'type');
                    $type = $type['type'];

                    // bid 타입이면 개수 증가
                    if ($type === 'timesale') {
                        $bid_count++;
                    }

                    // 타입을 배열에 추가
                    if (!in_array($type, $allowed_types)) {
                        $allowed_types[] = $type;
                    }

                    // 타입이 섞이면 중단
                    if (count($allowed_types) > 1) {
                        alert("함께장터 상품은 일반 상품과 함께 구입이 불가능합니다.");
                    }

                    // bid 상품이 2개 이상인 경우
                    if ($bid_count > 1) {
                        alert("함께장터 상품은 한 번에 하나만 구매 가능합니다.");
                    }

                    $sql = "SELECT * FROM shop_cart WHERE gs_id = ? AND ct_select='0' AND mb_id = ? AND index_no = ?";
                    $data = array($gs_id, $set_mb_id, $index_no);
                    $res = $this->query_model->returnQueryObj($sql, $data);

                    if ($res->num_rows() > 0) {
                        $row = $res->row_array();
                        $ss_cart_id .= $comma . $row['index_no'];
                        $comma = ",";
                    }
                }
            }

            set_session('ss_cart_id', $ss_cart_id);
        } else if ($act == "alldelete") { // 모두 삭제이면
            $sql = "SELECT * FROM shop_cart WHERE ct_select='0' AND mb_id = ?";
            $data = array($set_mb_id);
            $res = $this->query_model->returnArr($sql, $data);
            $set_cart_id = get_cookie('ck_guest_cart_id');
            foreach ($res as $key => $row) {
                $sql = "DELETE FROM shop_order WHERE od_id = ? AND od_no = ? AND gs_id = ? AND dan = '0' ";
                $param = array($row['od_id'], $row['od_no'], $row['gs_id']);
                $this->query_model->returnNull($sql, $param);
            }

            $sql = "DELETE FROM shop_cart WHERE ct_select='0' AND mb_id = ?";
            $param = array($set_cart_id);
            $this->query_model->returnNull($sql, $param);
        } else if ($act == "seldelete") { // 선택삭제
            if (!count($this->input->post('ct_chk'))) {
                alert("삭제하실 상품을 하나이상 선택해 주십시오.");
            }

            $fldcnt = count($this->input->post('gs_id'));

            for ($i = 0; $i < $fldcnt; $i++) {
                if (!isset($this->input->post('ct_chk')[$i])) {
                    continue;
                }

                $ct_chk = $this->input->post('ct_chk')[$i];
                $gs_id = $this->input->post('gs_id')[$i];
                $index_no = $this->input->post('index_no')[$i];

                $sql = "SELECT * FROM shop_cart WHERE gs_id=? AND ct_select='0'  AND mb_id = ? AND index_no = ?";
                $data = array($gs_id, $set_mb_id, $index_no);
                $res = $this->query_model->returnArr($sql, $data);
                if (!$res) {
                    alert("삭제할 상품이 없습니다.");
                }

                $sql = "DELETE FROM shop_cart WHERE gs_id=? AND ct_select='0' AND mb_id = ? AND index_no = ?";
                $this->query_model->returnNull($sql, $data);
            }
        } else if ($act == "optionDelete") { // 옵션 삭제
            $gs_id = $this->input->post('gs_id');
            $index_no = $this->input->post('index_no');

            $sql = "SELECT * FROM shop_cart WHERE gs_id=? AND ct_select='0'  AND mb_id = ? AND index_no = ?";
            $data = array($gs_id, $set_mb_id, $index_no);
            $res = $this->query_model->returnArr($sql, $data);
            if (!$res) {
                echo json_encode(array(
                    'res' => 400,
                    'msg' => "삭제할 상품이 없습니다."
                ));
                exit;
            }

            $sql = "DELETE FROM shop_cart WHERE gs_id=? AND ct_select='0' AND mb_id = ? AND index_no = ?";
            $this->query_model->returnNull($sql, $data);

            echo json_encode(array(
                'res' => 200
            ));
            exit;
        } else { // 장바구니에 담기
            $count = count($this->input->post('gs_id'));
            if ($count < 1) {
                alert('장바구니에 담을 상품을 선택하여 주십시오.');
            }

            $comma = "";
            $ss_cart_id = "";
            set_session('ss_cart_id', '');

            for ($i = 0; $i < $count; $i++) {
                // 보관함의 상품을 담을 때 체크되지 않은 상품 건너뜀
                if ($act == 'multi' && !isset($this->input->post('chk_gs_id')[$i]))
                    continue;

                $gs_id = $this->input->post('gs_id')[$i];
                $opt_count = count($this->input->post('io_id')[$gs_id]);

                if ($opt_count && $_POST['io_type'][$gs_id][0] != 0)
                    alert('상품의 주문옵션을 선택해 주십시오.');

                for ($k = 0; $k < $opt_count; $k++) {
                    if ($_POST['ct_qty'][$gs_id][$k] < 1)
                        alert('수량은 1 이상 입력해 주십시오.');
                }

                // 상품정보
                $gs = $this->global_lib->get_goods($gs_id);
                if ($gs['type'] == "timesale") {
                    $gs['goods_price'] = $this->gvcomplex_lib->get_timesale_goods_price($gs_id);
                } else {
                    $gs['goods_price'] = $this->gvcomplex_lib->get_sale_price($gs_id);
                }

                // 구매내역 있는지 확인 (bid)
                if ($gs['type'] == "timesale") {
                    $sql = "SELECT * FROM shop_order WHERE mb_id = ? AND gs_id = ? AND (dan IN ('2','3','4','5'))";
                    $data = array($set_mb_id, $gs['index_no']);
                    $result = $this->query_model->returnOneArr($sql, $data);
                    if (!empty($result)) {
                        alert('이미 구매한 상품입니다. 함께장터 상품은 1개만 구매 가능합니다.');
                    }
                }

                // 옵션정보를 얻어서 배열에 저장
                $opt_list = array();
                $sql = "SELECT * FROM shop_goods_option WHERE gs_id = ? ORDER BY io_no ASC";
                $param = array($gs_id);
                $result = $this->query_model->returnArr($sql, $param);
                $lst_count = 0;
                foreach ($result as $key => $row) {
                    $opt_list[$row['io_type']][$row['io_id']]['id'] = $row['io_id'];
                    $opt_list[$row['io_type']][$row['io_id']]['use'] = $row['io_use'];
                    $opt_list[$row['io_type']][$row['io_id']]['price'] = $row['io_price'];
                    $opt_list[$row['io_type']][$row['io_id']]['stock'] = $row['io_stock_qty'];

                    // 주문옵션 개수
                    if (!$row['io_type'])
                        $lst_count++;
                }
                //--------------------------------------------------------
                //  재고 검사
                //--------------------------------------------------------
                // 이미 장바구니에 있는 같은 상품의 수량합계를 구한다.
                $OM_OPTION_ID_FILTER = '/[\'\"\\\'\\\"]/';
                for ($k = 0; $k < $opt_count; $k++) {
                    $io_id = preg_replace($OM_OPTION_ID_FILTER, '', $_POST['io_id'][$gs_id][$k]);
                    $io_type = preg_replace('#[^01]#', '', $_POST['io_type'][$gs_id][$k]);
                    $io_value = $_POST['io_value'][$gs_id][$k];

                    // 재고 구함
                    $ct_qty = $_POST['ct_qty'][$gs_id][$k];
                    if (!$io_id) {
                        $it_stock_qty = $this->global_lib->get_it_stock_qty($gs_id);
                    } else {
                        $it_stock_qty = $this->global_lib->get_option_stock_qty($gs_id, $io_id, $io_type);
                    }

                    $is_soldout = $this->funcomplex_lib->is_soldout($gs_id);
                    if ($is_soldout) {
                        alert($io_value . " 의 재고수량이 부족합니다.\\n\\n현재 재고수량 : " . number_format($it_stock_qty) . " 개");
                    }
                }
                //--------------------------------------------------------

                // 기존 장바구니 자료를 먼저 삭제
                $sql = "SELECT * FROM shop_cart WHERE gs_id=? AND ct_select='0' AND mb_id = ? AND is_direct = 0";
                $data = array($gs_id, $set_mb_id);
                $res = $this->query_model->returnArr($sql, $data);
                foreach ($res as $key => $row) {
                    $sql = "DELETE FROM shop_order
                            WHERE od_id = ? AND od_no = ? AND gs_id = ? AND dan = '0'";
                    $param = array($row['od_id'], $row['od_no'], $row['gs_id']);
                    $this->query_model->returnNull($sql, $param);
                }

                // 장바구니에 Insert
                for ($k = 0; $k < $opt_count; $k++) {
                    $io_id = preg_replace($OM_OPTION_ID_FILTER, '', $_POST['io_id'][$gs_id][$k]);
                    $io_type = preg_replace('#[^01]#', '', $_POST['io_type'][$gs_id][$k]);
                    $io_value = $_POST['io_value'][$gs_id][$k];

                    // 주문옵션정보가 존재하는데 선택된 옵션이 없으면 건너뜀
                    if ($lst_count && $io_id == '')
                        continue;

                    // 구매할 수 없는 옵션은 건너뜀
                    if ($io_id && !$opt_list[$io_type][$io_id]['use'])
                        continue;

                    $io_price = isset($opt_list[$io_type][$io_id]['price']) ? $opt_list[$io_type][$io_id]['price'] : 0;
                    $ct_qty = $_POST['ct_qty'][$gs_id][$k];

                    // 포인트 계산
                    $point = 0;
                    $grade = $member['grade'];

                    $sql = "SELECT gb_buy_point FROM shop_member_grade WHERE gb_no = ?";
                    $param = array($grade);
                    $result = $this->query_model->returnOneArr($sql, $param);

                    if ($gs['is_point'] > 0) {
                        $gb_point = $result['gb_buy_point']; // 적립률
                        $point = (int) round(($gs['goods_price'] * $ct_qty) * ($gb_point * (1 / 100))); // 적립 포인트
                    }

                    // 0 장바구니
                    // 1 바로구매
                    $od_id  = "";
                    if ($sw_direct == 1) {
                        $od_id = $this->global_lib->get_uniqid();

                        set_session("ss_order_id", $od_id);
                    }

                    // 동일옵션의 상품이 있으면 수량 더함
                    $sql2 = "SELECT index_no
                            FROM shop_cart
                            WHERE gs_id = ? AND mb_id = ? AND ct_select = '0' AND io_id = ? AND is_direct = 0";
                    $data = array($gs_id, $set_mb_id, $io_id);
                    $row2 = $this->query_model->returnOneArr($sql2, $data);

                    if ($gs['type'] == "timesale" && !empty($row2)) {
                        alert("이미 장바구니에 담겨있는 상품입니다.");
                    }

                    if ($sw_direct != 1 && !empty($row2['index_no'])) {
                        if ($act == "optionmod") {
                            $sql3 = "UPDATE shop_cart SET ct_qty = ? WHERE index_no = ?";
                        } else {
                            $sql3 = "UPDATE shop_cart SET ct_qty = ct_qty+? WHERE index_no = ?";
                        }
                        $data3 = array($ct_qty, $row2['index_no']);
                        $this->query_model->returnNull($sql3, $data3);
                        continue;
                    }

                    // 중복되지 않는 유일키를 생성
                    $od_no = $this->global_lib->cart_uniqid();
                    $ct_send_cost = isset($ct_send_cost) ? $ct_send_cost : 0;
                    $ca_id = isset($ca_id[$gs_id][$k]) ? $ca_id[$gs_id][$k] : "";

                    $value = array(
                        'ca_id' => $ca_id,
                        'mb_id' => $member['id'],
                        'gs_id' => $gs['index_no'],
                        'ct_time' => $OM_TIME_YMDHIS,
                        'ct_price' => $gs['goods_price'],
                        'ct_supply_price' => $gs['supply_price'],
                        'ct_qty' => $ct_qty,
                        'ct_point' => $point,
                        'io_id' => $io_id,
                        'io_type' => $io_type,
                        'io_price' => $io_price,
                        'ct_option' => $io_value,
                        'ct_send_cost' => $ct_send_cost,
                        'od_no' => $od_no,
                        'ct_ip' => $_SERVER['REMOTE_ADDR'],
                        'is_direct' => $sw_direct,
                        'referral_code' => $referralCode,
                        'referral_decode' => $referralCode_decode
                    );
                    $insertId = $this->query_model->insertObj('shop_cart', $value);
                    $ss_cart_id .= $comma . $insertId;
                    $comma = ",";
                }
            }

            set_session('ss_cart_id', $ss_cart_id);
        }

        // 바로 구매일 경우
        if ($sw_direct == 1 || $act == "buy") {
            goto_url(base_url() . "m/shop/orderform");
        } else {
            goto_url(base_url() . "m/shop/cart");
        }
    }
    #상품 주문하기 함수 화요일에 와서 하기
    public function orderformupdate()
    {
        include_once('./lib/mailer.lib.php');
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');
        $default = $this->load->get_var('default');
        $super = $this->load->get_var('super');
        $pt_settle_pid = $this->load->get_var('pt_settle_pid');

        $set_cart_id = get_cookie('ck_guest_cart_id');
        $cart_select = "";

        $gs_id = $this->input->post('gs_id');
        $type = $this->input->post('type');
        $cart_id = $this->input->post('cart_id');
        $gs_price = $this->input->post('gs_price');
        $gs_notax = $this->input->post('gs_notax');
        $gs_notax = $this->input->post('gs_notax');
        $seller_real_id = $this->input->post('seller_real_id');
        $seller_real_id = $this->input->post('seller_real_id');
        $seller_id = $this->input->post('seller_id');
        $seller_name = $this->input->post('seller_name');
        $supply_price = $this->input->post('supply_price');
        $sum_point = $this->input->post('sum_point');
        $sum_qty = $this->input->post('sum_qty');
        $referralCode = $this->input->post('referral_code');

        // 삼성페이 요청으로 왔다면 현재 삼성페이는 이니시스 밖에 없으므로
        $paymethod = $this->input->post('paymethod');
        if ($paymethod == '삼성페이' && $default['de_pg_service'] != 'inicis') {
            alert("이니시스를 사용중일때만 삼성페이 결제가 가능합니다.", base_url() . "m/shop/cart");
        }
        // 장바구니 상품 재고 검사
        $error = "";
        $ss_cart_id = $this->input->post('ss_cart_id');
        $sql = " select * from shop_cart where index_no IN ({$ss_cart_id}) and ct_select = '0' ";
        $result = $this->query_model->returnArr($sql);
        $i = 0;
        foreach ($result as $key => $row) {
            // 상품에 대한 현재고수량
            if ($row['io_id']) {
                $it_stock_qty = (int)$this->global_lib->get_option_stock_qty($row['gs_id'], $row['io_id'], $row['io_type']);
            } else {
                $it_stock_qty = (int)$this->global_lib->get_it_stock_qty($row['gs_id']);
            }
            // 장바구니 수량이 재고수량보다 많다면 오류
            $is_soldout = $this->funcomplex_lib->is_soldout($row['gs_id']);
            if ($is_soldout) {
                $error .= "{$row['ct_option']} 의 재고수량이 부족합니다. 현재고수량 : $it_stock_qty 개\\n\\n";
            }
            $i++;
        }
        if ($i == 0)
            alert('장바구니가 비어 있습니다.\\n\\n이미 주문하셨거나 장바구니에 담긴 상품이 없는 경우입니다.', base_url() . 'shop/cart');
        if ($error != "") {
            $error .= "다른 고객님께서 {$name}님 보다 먼저 주문하신 경우입니다. 불편을 끼쳐 죄송합니다.";
            alert($error);
        }
        // 주문번호를 얻는다.
        $od_id = get_session('ss_order_id');

        if (!$od_id) {
            alert("주문번호가 없습니다.", base_url() . 'm');
        }
        // 이전에 주문하다 멈춘 내역이 있을경우 삭제한다
        $sql = "select * from shop_cart where ct_select='0' and ct_direct=?";
        $param = array($set_cart_id);
        $res = $this->query_model->returnArr($sql, $param);
        $comma = "";
        foreach ($res as $key => $row) {
            $sql = " delete from shop_order where od_id = ? and od_no = ? and gs_id = ?  and dan = '0' ";
            $param = array($row['od_id'], $row['od_no'], $row['gs_id']);
            $this->query_model->returnNull($sql, $param);

            $ss_cart_id .= $comma . $row['index_no'];
            $comma = ",";
        }
        $dan = 0;
        if ($paymethod == '무통장')
            $dan = 1; // 주문접수 단계로 적용

        if ((int)$_POST['tot_price'] == 0) { // 총 결제금액이 0 이면
            $dan = 2; // 입금확인 단계로 적용

            // 포인트로 전액 결제시는 포인트결제로 값을 바꾼다.
            if ($paymethod != '포인트' && (int)$_POST['org_price'] == (int)$_POST['use_point']) {
                $paymethod = '포인트';
            }

            // 예치금으로 전액 결제시는 예치금결제로 값을 바꾼다.
            if ($_POST['paymethod'] != '예치금' && (int)$_POST['org_price'] == (int)$_POST['use_mileage']) {
                $_POST['paymethod'] = '예치금';
            }
        }
        set_session('tot_price', (int)$_POST['tot_price']);
        set_session('use_point', (int)$_POST['use_point']);
        set_session('use_mileage', (int)$_POST['use_mileage']);
        $baesong_price    = explode("|", $_POST['baesong_price']); // 상품별 배송비
        $coupon_price    = explode("|", $_POST['coupon_price']); // 상품별 할인가
        $coupon_lo_id    = explode("|", $_POST['coupon_lo_id']); // 상품별 쿠폰 shop_coupon_log (필드:lo_id)
        $coupon_cp_id    = explode("|", $_POST['coupon_cp_id']); // 상품별 쿠폰 shop_coupon_log (필드:cp_id)
        $ss_cart_id        = explode(",", $_POST['ss_cart_id']); // 장바구니 idx

        $use_point = (int)$_POST['use_point']; // 포인트결제
        $use_mileage = (int)$_POST['use_mileage']; // 포인트결제
        $baesong_price2 = (int)$_POST['baesong_price2']; // 추가배송비

        if ($is_member)
            $od_pwd = $member['passwd'];
        else {
            $od_pwd = $this->input->post('od_pwd');
            $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
            $od_pwd = $this->global_lib->reg_sql_password($od_pwd, $OM_TIME_YMDHIS);
        }
        for ($i = 0; $i < count($gs_id); $i++) {
            // 주문 일련번호
            $od_no = $cart_id[$i];

            if ($i == 0) {
                $t_point = $use_point;  // 포인트 결제금액
                $t_mileage = $use_mileage;  // 예치금 결제금액

                for ($k = 0; $k < count($gs_id); $k++) {
                    // 추가 배송비 처리
                    if ($k == 0 && $baesong_price2 > 0) {
                        $baesong_price[$k] = (int)$baesong_price[$k] + $baesong_price2;
                    }

                    // 기본값 처리
                    $baesong_price[$k] = $baesong_price[$k] ?? 0;
                    $coupon_price[$k] = $coupon_price[$k] ?? 0;
                    $gs_price[$k] = $gs_price[$k] ?? 0;

                    $t_baesong = (int)$baesong_price[$k]; // 배송비 결제금액
                    $t_price = (int)$gs_price[$k] - (int)$coupon_price[$k]; // 상품 가격 - 쿠폰 할인가

                    // 포인트 처리
                    if ($t_point > 0) {
                        if (($t_price + $t_baesong) >= $t_point) {
                            $i_use_point[$k] = $t_point;
                            $i_use_price[$k] = ($t_price + $t_baesong) - $t_point;
                            $t_point = 0;

                            // 마일리지 처리
                            if ($i_use_price[$k] > $t_mileage) {
                                $i_use_mileage[$k] = $t_mileage;
                                $i_use_price[$k] -= $t_mileage;
                                $t_mileage = 0;
                            } else {
                                $i_use_mileage[$k] = $i_use_price[$k];
                                $t_mileage -= $i_use_price[$k];
                                $i_use_price[$k] = 0;
                            }
                        } else {
                            $i_use_point[$k] = $t_price + $t_baesong;
                            $t_point -= $i_use_point[$k];
                            $i_use_price[$k] = 0;
                        }
                    }

                    // 마일리지 처리
                    if ($t_mileage > 0) {
                        if (($t_price + $t_baesong) > $t_mileage) {
                            $i_use_mileage[$k] = $t_mileage;
                            $i_use_price[$k] = ($t_price + $t_baesong) - $t_mileage;
                            $t_mileage = 0;

                            // 포인트 처리
                            if ($i_use_price[$k] > $t_point) {
                                $i_use_point[$k] = $t_point;
                                $i_use_price[$k] -= $t_point;
                                $t_point = 0;
                            } else {
                                $i_use_point[$k] = $i_use_price[$k];
                                $t_point -= $i_use_price[$k];
                                $i_use_price[$k] = 0;
                            }
                        } else {
                            $i_use_mileage[$k] = $t_price + $t_baesong;
                            $t_mileage -= $i_use_mileage[$k];
                            $i_use_price[$k] = 0;
                        }
                    } else {
                        // 포인트와 마일리지가 없을 경우
                        $t_point = 0;
                        $t_mileage = 0;
                        $i_use_point[$k] = 0;
                        $i_use_mileage[$k] = 0;
                        $i_use_price[$k] = $t_price + $t_baesong;
                    }
                }
            } else {
                $baesong_price2 = 0;
            }

            $coupon_lo_id[$i] = isset($coupon_lo_id[$i]) ? $coupon_lo_id[$i] : "";
            $coupon_cp_id[$i] = isset($coupon_cp_id[$i]) ? $coupon_cp_id[$i] : "";

            // 상품 타입 확인
            $gs_type = $this->global_lib->get_goods($gs_id[$i], 'type');
            $gs_type = $gs_type['type'];

            // 리뷰 공유 코드
            $key = "share";
            $referral_code = $referralCode[$i];
            $referral_decode = openssl_decrypt(base64_decode($referralCode[$i]), "AES-256-CBC", $key, true, str_repeat(chr(0), 16));

            $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
            $data = array(
                'od_id' => $od_id,
                'od_no' => $od_no,
                'mb_id' => $member['id'],
                'name' => $this->input->post('name'),
                'cellphone' => $this->input->post('cellphone'),
                'email' => $this->input->post('email'),
                'zip' => $this->input->post('zip'),
                'addr1' => $this->input->post('addr1'),
                'addr2' => $this->input->post('addr2'),
                'addr3' => $this->input->post('addr3'),
                'addr_jibeon' => $this->input->post('addr_jibeon'),
                'b_name' => $this->input->post('b_name'),
                'b_cellphone' => $this->input->post('b_cellphone'),
                'b_zip' => $this->input->post('b_zip'),
                'b_addr1' => $this->input->post('b_addr1'),
                'b_addr2' => $this->input->post('b_addr2'),
                'b_addr3' => $this->input->post('b_addr3'),
                'b_addr_jibeon' => $this->input->post('b_addr_jibeon'),
                'gs_id' => $gs_id[$i],
                'gs_notax' => $gs_notax[$i],
                'seller_id' => $seller_id[$i],
                'seller_real_id' => $seller_real_id[$i],
                'seller_name' => $seller_name[$i],
                'goods_price' => $gs_price[$i],
                'supply_price' => $supply_price[$i],
                'sum_point' => $sum_point[$i],
                'sum_qty' => $sum_qty[$i],
                'coupon_price' => $coupon_price[$i],
                'coupon_lo_id' => $coupon_lo_id[$i],
                'coupon_cp_id' => $coupon_cp_id[$i],
                'use_price' => $i_use_price[$i],
                'use_point' => $i_use_point[$i],
                'use_mileage' => $i_use_mileage[$i],
                'baesong_price' => $baesong_price[$i],
                'baesong_price2' => $baesong_price2,
                'paymethod' => $this->input->post('paymethod'),
                'bank' => $this->input->post('bank'),
                'deposit_name' => $this->input->post('deposit_name'),
                'dan' => $dan,
                'memo' => $this->input->post('memo'),
                'taxsave_yes' => $this->input->post('taxsave_yes'),
                'taxbill_yes' => $this->input->post('taxbill_yes'),
                'company_saupja_no' => $this->input->post('company_saupja_no'),
                'company_name' => $this->input->post('company_name'),
                'company_owner' => $this->input->post('company_owner'),
                'company_addr' => $this->input->post('company_addr'),
                'company_item' => $this->input->post('company_item'),
                'company_service' => $this->input->post('company_service'),
                'tax_hp' => $this->input->post('tax_hp'),
                'tax_saupja_no' => $this->input->post('tax_saupja_no'),
                'od_time' => $OM_TIME_YMDHIS,
                'od_pwd' => $od_pwd,
                'od_ip' => $_SERVER['REMOTE_ADDR'],
                'od_type' => $gs_type,
                'od_test' => $default['de_card_test'],
                'od_tax_flag' => $default['de_tax_flag_use'],
                'od_settle_pid' => $pt_settle_pid,
                'pt_id' => $this->input->post('pt_id'),
                'shop_id' => $this->input->post('shop_id'),
                'referral_code' => $referral_code,
                'referral_decode' => $referral_decode
            );
            $table = "shop_order";
            $insert_id = $this->query_model->insertObj($table, $data);

            // 고객이 주문/배송조회를 위해 보관해 둔다.
            $this->funcomplex_lib->save_goods_data($gs_id[$i], $insert_id, $od_id);
            // 쿠폰 사용함으로 변경 (무통장, 포인트결제일 경우만)
            if ($coupon_lo_id[$i] && $is_member && in_array($_POST['paymethod'], array('무통장', '포인트', '예치금'))) {
                $sql = "update shop_coupon_log set mb_use='1',od_no=?,cp_udate=? where lo_id=?";
                $param = array($od_no, $OM_TIME_YMDHIS, $coupon_lo_id[$i]);
                $this->query_model->returnNull($sql, $param);
            }

            // 쿠폰 주문건수 증가
            if ($coupon_cp_id[$i] && $is_member) {
                $sql = "update shop_coupon set cp_odr_cnt=(cp_odr_cnt + 1) where cp_id=?";
                $param = array($coupon_cp_id[$i]);
                $this->query_model->returnNull($sql, $param);
            }

            // 주문완료 후 쿠폰발행
            $gs = $this->global_lib->get_goods($gs_id[$i], 'use_aff');
            if (!$gs['use_aff'] && $config['coupon_yes'] && $is_member) {
                $cp_used = $this->funcomplex_lib->is_used_coupon('1', $gs_id[$i], $member['id']);
                if ($cp_used) {
                    $cp_id = explode(",", $cp_used);
                    for ($g = 0; $g < count($cp_id); $g++) {
                        if ($cp_id[$g]) {
                            $sql = "select * from shop_coupon where cp_id=?";
                            $param = array($cp_id[$g]);
                            $cp = $this->query_model->returnOneArr($sql, $param);
                            $this->funcomplex_lib->insert_used_coupon($member['id'], $member['name'], $cp);
                        }
                    }
                }
            }
        }

        $od_pg = $default['de_pg_service'];
        if ($_POST['paymethod'] == 'KAKAOPAY')
            $od_pg = 'KAKAOPAY';

        // 복합과세 금액
        if ($default['de_tax_flag_use']) {
            $info = $this->global_lib->comm_tax_flag($od_id);
            $od_tax_mny  = $info['comm_tax_mny'];
            $od_vat_mny  = $info['comm_vat_mny'];
            $od_free_mny = $info['comm_free_mny'];
        } else {
            $od_tax_mny  = round($_POST['tot_price'] / 1.1);
            $od_vat_mny  = $_POST['tot_price'] - $od_tax_mny;
            $od_free_mny = 0;
        }

        // 주문서에 UPDATE
        $sql = " update shop_order set od_pg = ? , od_tax_mny= ? , od_vat_mny= ? , od_free_mny	 = ? where od_id = ?";
        $param = array($od_pg, $od_tax_mny, $od_vat_mny, $od_free_mny, $od_id);
        $this->query_model->returnNull($sql, $param);

        if (in_array($this->input->post('paymethod'), array('무통장', '포인트'))) {
            $cart_select = " , ct_select = '1' ";
        }

        // 장바구니 주문완료 처리 (무통장, 포인트결제)
        $sql = " update shop_cart set od_id = ? {$cart_select} where index_no IN ({$this->input->post('ss_cart_id')})";
        $param = array($od_id);
        $this->query_model->returnNull($sql, $param);

        // 재고수량 감소
        for ($i = 0; $i < count($ss_cart_id); $i++) {
            $ct = $this->global_lib->get_cart_id($ss_cart_id[$i]);

            if ($ct['io_id']) { // 옵션 : 재고수량 감소
                $sql = "update shop_goods_option set io_stock_qty = io_stock_qty - ? where io_id = ? and gs_id = ? and io_type = ? 
                        and io_stock_qty <> '999999999' ";
                $param = array($ct['ct_qty'], $ct['io_id'], $ct['gs_id'], $ct['io_type']);
                $this->query_model->returnNull($sql, $param);
            } else { // 상품 : 재고수량 감소
                $sql = " update shop_goods
                            set stock_qty = GREATEST(stock_qty - ?, 0)
                        where index_no = ?
                            and stock_mod = '1' ";
                $param = array($ct['ct_qty'], $ct['gs_id']);
                $this->query_model->returnNull($sql, $param);
            }
        }

        if (in_array($_POST['paymethod'], array('무통장', '포인트', '예치금'))) {
            // 회원이면서 포인트를 사용했다면 테이블에 사용을 추가
            if ($is_member && $use_point > 0) {
                $this->funcomplex_lib->insert_point($member['id'], (-1) * $use_point, "주문번호 $od_id 결제");
            }

            if ($is_member && $use_mileage > 0) {
                $this->funcomplex_lib->insert_mileage($member['id'], (-1) * $use_mileage, "주문번호 $od_id 결제 (예치금)");
            }

            // 쿠폰사용내역기록
            if ($is_member) {
                $sql = "select * from shop_order where od_id=?";
                $param = array($od_id);
                $res = $this->query_model->returnArr($sql, $param);
                foreach ($res as $key => $row) {
                    if ($row['coupon_price']) {
                        $sql = "update shop_coupon_log set mb_use = '1', od_no = ?, cp_udate	= '" . $OM_TIME_YMDHIS . "' where lo_id = ?";
                        $param = array($row['od_no'], $row['coupon_lo_id']);
                        $this->query_model->returnNull($sql, $param);
                    }
                }
            }
            $sql = "select * from shop_order where od_id=?";
            $param = array($od_id);
            $od = $this->query_model->returnOneArr($sql, $param);

            // 주문완료 문자전송
            $this->funcomplex_lib->moonjaro_order_sms_send($od['pt_id'], $od['cellphone'], $od_id, 2);

            // 무통장 입금 때 고객에게 계좌정보 보냄
            if ($_POST['paymethod'] == '무통장' && (int)$_POST['tot_price'] > 0) {
                $sms_content = $od['name'] . "님의 입금계좌입니다.\n금액:" . number_format($_POST['tot_price']) . "원\n계좌:" . $od['bank'] . "\n" . $config['company_name'];
                $this->funcomplex_lib->moonjaro_direct_sms_send($od['pt_id'], $od['cellphone'], $sms_content);
            }

            // 메일발송
            if ($od['email']) {
                $subject1 = $this->funcomplex_lib->get_text($od['name']) . "님 주문이 정상적으로 처리되었습니다.";
                $subject2 = $this->funcomplex_lib->get_text($od['name']) . " 고객님께서 신규주문을 신청하셨습니다.";

                $content = $this->orderformupdate_mail($od);


                // 주문자에게 메일발송
                mailer($config['company_name'], $super['email'], $od['email'], $subject1, $content, 1);

                // 관리자에게 메일발송
                if ($super['email'] != $od['email']) {
                    mailer($od['name'], $od['email'], $super['email'], $subject2, $content, 1);
                }
            }
        }
        // 주문번호제거
        set_session('ss_order_id', '');

        // 장바구니 session 삭제
        set_session('ss_cart_id', '');
        // orderinquiryview 에서 사용하기 위해 session에 넣고
        $uid = md5($od_id . $OM_TIME_YMDHIS . $_SERVER['REMOTE_ADDR']);
        set_session('ss_orderview_uid', $uid);

        if (in_array($_POST['paymethod'], array('무통장', '포인트', '예치금'))) {
            for ($i = 0; $i < count($gs_id); $i++) {
                if (in_array("timesale", $type)) {
                    // bid 상품 결제 로그 추가
                    $data2 = array(
                        'mb_id' => $member['id'],
                        'mb_name' => $member['name'],
                        'gs_id' => $gs_id[$i],
                        'od_id' => $od_id,
                        'flag' => "buy",
                        'purchase_date' => date("Y-m-d H:i:s", time())
                    );

                    $table = "shop_order_timesale";
                    $insert_id = $this->query_model->insertObj($table, $data2);
                }
            }

            goto_url(base_url() . 'm/shop/orderinquiryview?od_id=' . $od_id . '&uid=' . $uid);
        } else if ($_POST['paymethod'] == 'KAKAOPAY') {
            goto_url(base_url() . 'm/shop/orderkakaopay?od_id=' . $od_id);
        } else if ($_POST['paymethod'] == '삼성페이') {
            goto_url(base_url() . 'm/shop/orderinicis?od_id=' . $od_id);
        } else {
            if ($default['de_pg_service'] == 'inicis')
                goto_url(base_url() . 'm/shop/orderinicis?od_id=' . $od_id);;
        }
    }
    private function orderformupdate_mail($od)
    {
        $config = $this->load->get_var('config');
        $sql = " select *  from shop_cart where od_id = ? group by gs_id order by index_no ";
        $param = array($od['od_id']);
        $res = $this->query_model->returnArr($sql, $param);
        $tableTag = "";
        foreach ($res as $key => $row2) {
            $rw = $this->global_lib->get_order($row2['od_no']);
            $gs = $this->global_lib->get_goods($row2['gs_id'], 'gname');

            $it_name = stripslashes($gs['gname']);
            $it_options = $this->funcomplex_lib->print_complete_options2($row2['gs_id'], $od['od_id']);
            if ($it_options && $row2['io_id']) {
                $it_name .= '<div style="margin:5px 0 0 0;padding:0">' . $it_options . '</div>';
            }
            $tableTag = "
            <tr>
                <td style=\"padding:5px;border-top:1px solid #d5d5d5;border-right:1px solid #d5d5d5;\" align=\"left\">{$it_name}</td>
                <td style=\"padding:5px;border-top:1px solid #d5d5d5;border-right:1px solid #d5d5d5;\" align=\"right\">" . number_format($rw['goods_price']) . "원</td>
                <td style=\"padding:5px;border-top:1px solid #d5d5d5;border-right:1px solid #d5d5d5;\" align=\"center\">" . number_format($rw['sum_qty']) . "개</td>			
                <td style=\"padding:5px;border-top:1px solid #d5d5d5;\" align=\"right\">" . number_format($rw['use_price']) . "원</td>
            </tr>";
        }
        $cont_st = 'font-size:12px;font-family:Gulim;line-height:15px;border:1px solid #d5d5d5;border-right:0;border-bottom:0';
        $th_st = 'padding:13px 10px 10px 10px;font-weight:normal;border-bottom:1px solid #d5d5d5;border-right:1px solid #d5d5d5;color:#80878d;background-color:#f5f6f5;text-align:left;';
        $td_st = 'padding:13px 10px 10px 10px;border-bottom:1px solid #d5d5d5;border-right:1px solid #d5d5d5;color:#393939;text-align:left;';
        $tot_price = number_format(get_session('tot_price'));
        $address = $this->funcomplex_lib->get_text(sprintf("(%s)", $od['b_zip']) . ' ' . $this->funcomplex_lib->print_address($od['b_addr1'], $od['b_addr2'], $od['b_addr3'], $od['b_addr_jibeon']));
        $data = array('config' => $config, 'od' => $od, 'tableTag' => $tableTag, 'cont_st' => $cont_st, 'th_st' => $th_st, 'td_st' => $td_st, 'tot_price' => $tot_price, 'address' => $address);
        return $this->load->view('pc/shop/order/orderformupdate_mail', $data, TRUE);
    }
    #게시글 삭제하기
    public function board_delete2()
    {
        $w = $this->input->post('w');
        $boardid = $this->input->post('boardid');
        $index_no = $this->input->post('index_no');
        $page = $this->input->post('page');
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $mb_no = $member['index_no'];
        $write = $this->load->get_var('write');
        if ($w == 'd') {

            if ($write['writer'] != 0) {
                if (!$is_member) {
                    goto_url(base_url() . "m/bbs/login");
                }

                if (!$this->gvcomplex_lib->is_admin()) {
                    if ($write['writer'] != $mb_no) {
                        alert('삭제 권한이 없습니다.');
                    }
                }
            } else {
                if (!$this->gvcomplex_lib->is_admin()) {
                    $passwd = $this->input->post('passwd');
                    if ($passwd != $write['passwd']) {
                        alert('비밀번호가 일치하지 않습니다.');
                    }
                }
            }

            $file1_dir = './data/board/' . $boardid . '/' . $write['fileurl1'];
            if (is_file($file1_dir) && $write['fileurl1']) {
                @unlink($file1_dir);
                delete_board_thumbnail($boardid, $write['fileurl1']);
            }

            $file2_dir = './data/board/' . $boardid . '/' . $write['fileurl2'];
            if (is_file($file2_dir) && $write['fileurl2']) {
                @unlink($file2_dir);
                delete_board_thumbnail($boardid, $write['fileurl2']);
            }

            $this->funcomplex_lib->delete_editor_image($write['memo']);

            $sql = " delete from shop_board_{$boardid} where index_no=?";
            $param = array($index_no);
            $this->query_model->returnNull($sql, $param);

            $sql = " delete from shop_board_{$boardid}_tail where board_index=? ";
            $this->query_model->returnNull($sql, $param);

            if ($boardid == 37)
                $redirect_url = base_url() . "m/bbs/goods_request_list?boardid={$boardid}&page={$page}";
            else if ($boardid == 13)
                $redirect_url = base_url() . "m/bbs/noticeList?boardid={$boardid}&page={$page}";
            goto_url($redirect_url);
        }
    }
    #상담문의 작성하기
    public function qaform_update()
    {
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $super = $this->load->get_var('super');
        $super_hp = $this->load->get_var('super_hp');

        $gs_id = $this->input->post('gs_id');
        $mb_id = $this->input->post('mb_id');
        $seller_id = $this->input->post('seller_id');
        $iq_ty = $this->input->post('iq_ty');
        $iq_secret = $this->input->post('iq_secret');
        $iq_secret = isset($iq_secret) ? $iq_secret : 0;
        $iq_name = addslashes($this->input->post('iq_name'));
        $iq_email = addslashes($this->input->post('iq_email'));
        $iq_hp = addslashes($this->input->post('iq_hp'));
        $iq_subject = addslashes($this->input->post('iq_subject'));
        $iq_question = addslashes($this->input->post('iq_question'));

        $w = $this->input->get('w');
        if (!isset($w)) {
            $w = $this->input->post('w');
        }

        include_once("./lib/mailer.lib.php");

        if (!$is_member) {
            alert_close("로그인 후 작성 가능합니다.");
        }

        if ($w == "" || $w == "u") {
            $token = $this->input->post('token');

            if ($token && get_session("ss_token") == $token) {
                // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
                set_session("ss_token", "");
            } else {
                alert_close("잘못된 접근 입니다.");
                exit;
            }

            if (substr_count($_POST['iq_question'], "&#") > 50) {
                alert("내용에 올바르지 않은 코드가 다수 포함되어 있습니다.");
            }

            if (!$_POST['iq_subject'])
                alert("제목을 입력하여 주십시오.");

            if (!$_POST['iq_question'])
                alert("질문을 입력하여 주십시오.");
        }

        if ($w == "") {
            $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
            $sql = "insert into shop_goods_qa
                        set gs_id= ?,mb_id= ?,seller_id	= ?,iq_ty= ?,iq_secret= ?,iq_name= ?,iq_email= ?,iq_hp= ?,iq_subject= ?,iq_question = ?,iq_time= ?,
                            iq_ip= ?";
            $param = array($gs_id, $mb_id, $seller_id, $iq_ty, $iq_secret, $iq_name, $iq_email, $iq_hp, $iq_subject, $iq_question, $OM_TIME_YMDHIS, $_SERVER['REMOTE_ADDR']);
            $this->query_model->returnNull($sql, $param);

            if ($_POST['gs_use_aff']) { // 가맹점상품인가?
                $mb = $this->global_lib->get_member($seller_id);
                $super_hp = $mb['cellphone'];
                $super['email'] = $mb['email'];
            }

            $wr_subject = $this->funcomplex_lib->get_text(stripslashes($iq_subject));
            $wr_content = $this->funcomplex_lib->conv_content(conv_unescape_nl(stripslashes($_POST['iq_question'])), 0);
            $wr_name = $this->funcomplex_lib->get_text($iq_name);
            $subject = $iq_name . '님께서 상품문의에 새글을 작성하셨습니다.';

            if ($iq_email) {
                $content = $this->write_update_mail($wr_subject, $wr_name, $wr_content);
                mailer($iq_name, $iq_email, $super['email'], $subject, $content, 1);
            }

            // 최고 관리자에게 문자전송
            $this->funcomplex_lib->moonjaro_direct_sms_send('admin', $super_hp, $subject);

            alert("정상적으로 등록 되었습니다.", "replace");
        } else if ($w == "u") {
            $iq_id = $this->input->post('iq_id');

            $sql = " update shop_goods_qa
                        set iq_ty= ?,iq_secret= ?,iq_name= ?,iq_email= ?,iq_hp= ?,iq_subject= ?,iq_question = ?
                        where iq_id	= ?";
            $param = array($iq_ty, $iq_secret, $iq_name, $iq_email, $iq_hp, $iq_subject, $iq_question, $iq_id);
            $this->query_model->returnNull($sql, $param);
            alert("정상적으로 수정 되었습니다.", "replace");
        } else if ($w == "d") {
            $iq_id = $this->input->get('iq_id');
            $hash = $this->input->get('hash');
            $gs_id = $this->input->get('gs_id');
            if (!$this->gvcomplex_lib->is_admin()) {
                $sql = " select iq_answer from shop_goods_qa where mb_id = ? and iq_id = ?";
                $param = array($member['id'], $iq_id);
                $row = $this->query_model->returnOneArr($sql, $param);
                if (!$row)
                    alert("자신의 상품문의만 삭제하실 수 있습니다.");

                if ($row['iq_answer'])
                    alert("답변이 있는 상품문의는 삭제하실 수 없습니다.");
            }

            $sql = "delete from shop_goods_qa where iq_id = ? and md5(concat(iq_id,iq_time,iq_ip)) = ?";
            $param = array($iq_id, $hash);
            $this->query_model->returnNull($sql, $param);

            alert("정상적으로 삭제 되었습니다", base_url() . "m/shop/view?index_no=$gs_id");
        }
    }
    #쿠폰등록하기
    public function gift_update()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);

        $config = $this->load->get_var('config');
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');

        $token = $this->input->post('token');

        if (!$config['gift_yes']) {
            alert("쿠폰사용이 중지 되었습니다.");
        }

        if (!$is_member) {
            alert("로그인 후 이용 가능합니다.");
        }

        // 세션에 저장된 토큰과 폼값으로 넘어온 토큰을 비교.
        if ($token && get_session("ss_token") == $token) {
            // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
            set_session("ss_token", "");
        } else {
            alert("잘못된 접근 입니다.");
            exit;
        }

        $str  = trim($_POST['gi_num1']);
        $str .= trim($_POST['gi_num2']);
        $str .= trim($_POST['gi_num3']);
        $str .= trim($_POST['gi_num4']);

        $gi_num = preg_replace("/([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})/", "\\1-\\2-\\3-\\4", $str);

        $sql = "select * from shop_gift where gi_num =?";
        $param = array($gi_num);
        $cp = $this->query_model->returnOneArr($sql, $param);

        if (!$cp['no'])
            alert("쿠폰번호가 존재하지 않습니다. 확인 후 다시 등록 바랍니다.");

        if ($cp['gr_edate'] < $OM_TIME_YMD)
            alert("현재 쿠폰은 사용기간이 만료 되었습니다. \\n\\n만료날짜 : " . $cp['gr_edate']);

        if ($cp['gr_sdate'] > $OM_TIME_YMD)
            alert("현재 쿠폰은 " . $cp['gr_sdate'] . "일 이후부터 사용 가능하십니다.");

        if ($cp['gi_use'])
            alert("현재 쿠폰은 이미 등록 된 상태입니다.");

        unset($value);
        $value['mb_id']        = $member['id'];
        $value['mb_name']    = $member['name'];
        $value['mb_wdate']    = $OM_TIME_YMDHIS;
        $value['gi_use']    = 1;
        $this->query_model->updateObj("shop_gift", $value, "gi_num = '$gi_num' ");

        // 포인트적립
        $this->funcomplex_lib->insert_point($member['id'], $cp['gr_price'], $cp['gr_subject'] . " 쿠폰", "@gift", $member['id'], $gi_num);

        alert("정상적으로 처리 되었습니다.", base_url() . "m/shop/gift");
    }
    #최근에 본 상품 삭제
    public function today()
    {
        $pr_idx = get_session("ss_pr_idx");
        $gs_id = $this->input->get('gs_id');
        set_session("ss_pr_idx", "");
        $tmp_idx = explode(",", $pr_idx);
        $comma = "";
        $tmp_saved = "";
        for ($i = 0; $i < count($tmp_idx); $i++) {
            if ($tmp_idx[$i] != $gs_id) {
                $tmp_saved .= $comma . $tmp_idx[$i];
                $comma = ',';
            }
        }
        set_session("ss_pr_idx", $tmp_saved);
        goto_url(base_url() . "m/shop/today");
    }
    #아임포트 처리 함수
    #아임포트에서 모바일로 리다이렉트 보낼수 작동할 함수
    // 아임포트(포트원) 토큰 생성
    private function imp_get_token()
    {
        $imp_secret = "P84FHcHJo90l8UsjSE22SrcccZ5SlvY2goKRdq54SSLmVbbHqqbeXPJgsOYbfmgdKDapzYCmnjiCWqGw";
        $imp_key = "4482313508533864";
        $data = array(
            'imp_key' => $imp_key,
            'imp_secret' => $imp_secret,
        );
        $data = json_encode($data);
        $url = 'https://api.iamport.kr/users/getToken';

        $ch = curl_init();                                 //curl 초기화
        curl_setopt($ch, CURLOPT_URL, $url);               //URL 지정하기
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);      //connection timeout 60초 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
        curl_setopt($ch, CURLOPT_POST, true);              //true시 post 전송 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);          //data post 전송 

        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res);
        return $res->response->access_token;
    }

    // 포트원 단건 결제 조회하기
    private function imp_get_payment_data($imp_uid)
    {
        $token = $this->imp_get_token();

        $url = "https://api.iamport.kr/payments/" . $imp_uid;
        $headers = array(
            "Authorization:{$token}",
            "Content-Type: application/json"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function order_import()
    {
        $imp_uid = $this->input->get('imp_uid');
        $merchant_uid = $this->input->get('merchant_uid');
        include_once('./Bill/iamport.php');

        $imp_paid_result = $this->imp_get_payment_data($imp_uid);
        $imp_paid_result = json_decode($imp_paid_result, true);
        if (empty($imp_paid_result)) {
            alert("결제 내역이 존재하지 않습니다.");
        }

        $imp_paid_result = $imp_paid_result['response'];

        //성공적으로 결제가 되면 처리할 과정
        $amount = $imp_paid_result['amount'];
        $bank_name = $imp_paid_result['bank_name'];
        $pay_method = $imp_paid_result['pay_method'];
        $buyer_name = $imp_paid_result['buyer_name'];

        $this->orderformresult($amount, $pay_method, $imp_uid, $bank_name, $buyer_name, $merchant_uid);
    }
    public function orderformresult($amount, $pay_method, $imp_uid, $bank_name, $buyer_name, $merchant_uid)
    {
        try {
            include_once('./Bill/iamport.php');
            //code...
            $is_member = $this->load->get_var('is_member');
            $member = $this->load->get_var('member');
            $config = $this->load->get_var('config');
            $super = $this->load->get_var('super');
            $default = $this->load->get_var('default');

            $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

            $imp_paid_result = $this->imp_get_payment_data($imp_uid);
            $imp_paid_result = json_decode($imp_paid_result, true);
            if (empty($imp_paid_result)) {
                alert("결제 내역이 존재하지 않습니다.");
            }
            $imp_paid_result = $imp_paid_result['response'];

            include_once('./lib/mailer.lib.php');
            $sql = "select * from shop_order where merchant_uid=?";
            $param = array($merchant_uid);
            $od = $this->query_model->returnOneArr($sql, $param);
            $od_id = $od['od_id'];
            if (!$od['od_id']) {
                throw new Exception("결제할 주문서가 없습니다.");
            }
            if (!$merchant_uid) {
                throw new Exception("결제중 오류가 발생했습니다.");
            }
            if (!$imp_uid) {
                throw new Exception("결제중 오류가 발생했습니다.");
            }


            $stotal =  $this->global_lib->get_order_spay($od_id); // 총계

            $i_price = (int)$stotal['useprice']; // 결제금액
            $i_usepoint = (int)$stotal['usepoint']; // 포인트결제액

            if (!$i_price) {
                throw new Exception("결제할 금액이 없습니다.");
            }

            $od_tno = '';
            $od_status = 0;
            $od_bank_account = "";
            $od_deposit_name = "";

            // if($pay_method=="card" || $pay_method=="trans" || $pay_method=="phone" ){
            //     $od_status=2;
            //     $od_bank_account    = $bank_name;
            // }
            if ($pay_method == "trans") {
                $od_status = 1;
                $od_deposit_name    = $od['name'];
            } else {
                $od_status = 2;
                $od_bank_account    = $bank_name;
            }

            if (!$amount) {
                throw new Exception('결제할 금액이 없습니다');
            }
            $pg_price = $amount;
            $app_time = date("Y-m-d H:i:s", time());
            $od_app_no = "";
            $od_receipt_time = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);

            $od_pg = $default['de_pg_service'];
            if ($pay_method == 'kakaopay')
                $od_pg = 'KAKAOPAY';

            // 주문금액과 결제금액이 일치하는지 체크 환불 체크하기 i am port 환불
            $tno = $od['od_tno'];
            if ((int)$i_price !== (int)$pg_price) {
                $cancel_msg = '결제금액 불일치';
                #환불이 필요한 상황
                #3. 주문취소
                $result = $iamport->cancel(array(
                    'imp_uid'        => $imp_uid,         //merchant_uid에 우선한다
                    'merchant_uid'    => $merchant_uid,     //imp_uid 또는 merchant_uid가 지정되어야 함
                    'reason'        => $cancel_msg,                //취소사유
                ));
                // 주문서에 UPDATE
                $sql = "update shop_order set dan= 0,shop_memo=? where od_id = ?";
                $param2 = array($cancel_msg, $od_id);
                $this->query_model->returnNull($sql, $param2);

                die("Receipt Amount Error");
            }
            $od_escrow = 0;
            // 주문서에 UPDATE
            $sql = " update shop_order
                        set deposit_name = ? , receipt_time = ? , bank= ? , dan= ? ,od_pg = ?, od_tno= ?
                        , od_app_no	 = ?, od_escrow	 = ?,
                        imp_uid=?
                        where od_id = ?";
            $param2 = array(
                $od_deposit_name,
                $od_receipt_time,
                $od_bank_account,
                $od_status,
                $od_pg,
                $od_tno,
                $od_app_no,
                $od_escrow,
                $imp_uid,
                $od_id
            );
            $result = $this->query_model->returnNull($sql, $param2);

            // 포트원 결제로그 등록
            if ($pay_method == "card" || $pay_method == "trans" || $pay_method == "phone") {
                $payLog['od_id'] = $od_id;
                $payLog['mb_id'] = $member['id'];
                $payLog['name']         = $imp_paid_result['buyer_name'];
                $payLog['tel']          = $imp_paid_result['buyer_tel'];
                $payLog['addr']         = $imp_paid_result['buyer_addr'];
                $payLog['email']        = $imp_paid_result['buyer_email'];
                $payLog['amount']       = $imp_paid_result['amount'];
                $payLog['paymethod']    = $imp_paid_result['pay_method'];
                $payLog['imp_uid']      = $imp_paid_result['imp_uid'];
                $payLog['merchant_uid'] = $imp_paid_result['merchant_uid'];
                $payLog['bank_name']    = isset($imp_paid_result['bank_name']) ? $imp_paid_result['bank_name'] : "";
                $payLog['card_name']    = isset($imp_paid_result['card_name']) ? $imp_paid_result['card_name'] : "";
                $payLog['card_number']  = isset($imp_paid_result['card_number']) ? $imp_paid_result['card_number'] : "";
                $payLog['apply_num']    = isset($imp_paid_result['apply_num']) ? $imp_paid_result['apply_num'] : "";
                $payLog['receipt_url']  = isset($imp_paid_result['receipt_url']) ? $imp_paid_result['receipt_url'] : "";
                $payLog['cancelled_amount'] = isset($imp_paid_result['cancel_amount']) ? $imp_paid_result['cancel_amount'] : "";
                $payLog['cancelled_reason'] = isset($imp_paid_result['cancelled_reason']) ? $imp_paid_result['cancelled_reason'] : "";
                $payLog['cancelled_time'] = isset($imp_paid_result['cancelled_time']) ? date("Y-m-d H:i:s", $imp_paid_result['cancelled_time']) : "";
                $payLog['order_time'] = date("Y-m-d H:i:s", $imp_paid_result['paid_at']);

                $this->query_model->insertObj("shop_order_log", $payLog);
            }

            if ($result) {
                // 장바구니 상태변경
                $sql = " update shop_cart set ct_select = '1' where od_id = ?";
                $param = array($od_id);
                $this->query_model->returnNull($sql, $param);
            } else {
                // 주문정보 UPDATE 오류시 결제 취소
                $cancel_msg = '주문상태 변경 오류';
                $result = $iamport->cancel(array(
                    'imp_uid'        => $imp_uid,         //merchant_uid에 우선한다
                    'merchant_uid'    => $merchant_uid,     //imp_uid 또는 merchant_uid가 지정되어야 함
                    'reason'        => $cancel_msg,                //취소사유
                ));
                $sql = "update shop_order set dan= 0,shop_memo=? where od_id = ?";
                $param2 = array($cancel_msg, $od_id);
                $this->query_model->returnNull($sql, $param2);
                die('<p>고객님의 주문 정보를 처리하는 중 오류가 발생해서 주문이 완료되지 않았습니다.</p><p>' . strtoupper($od_pg) . '를 이용한 전자결제(신용카드, 계좌이체, 가상계좌 등)은 자동 취소되었습니다.');
            }
            // 회원이면서 포인트를 사용했다면 테이블에 사용을 추가
            if ($is_member && $i_usepoint) {
                $this->funcomplex_lib->insert_point($member['id'], (-1) * $i_usepoint, "주문번호 $od_id 결제");
            }
            // 쿠폰사용내역기록
            if ($is_member) {
                $sql = "select * from shop_order where od_id=?";
                $param = array($od_id);
                $res = $this->query_model->returnArr($sql, $param);
                foreach ($res as $key => $row) {
                    if ($row['coupon_price']) {
                        $sql = "update shop_coupon_log set mb_use = '1', od_no = ?, cp_udate= '" . $OM_TIME_YMDHIS . "' where lo_id = ? ";
                        $param = array($row['od_no'], $row['coupon_lo_id']);
                        $this->query_model->returnNull($sql, $param);
                    }
                }
            }
            // 주문완료 문자전송
            $this->funcomplex_lib->moonjaro_order_sms_send($od['pt_id'], $od['cellphone'], $od_id, 2);
            // 메일발송
            if ($od['email']) {
                $subject1 = $this->funcomplex_lib->get_text($od['name']) . "님 주문이 정상적으로 처리되었습니다.";
                $subject2 = $this->funcomplex_lib->get_text($od['name']) . " 고객님께서 신규주문을 신청하셨습니다.";

                $content = $this->orderformupdate_mail($od);
                // 주문자에게 메일발송
                mailer($config['company_name'], $super['email'], $od['email'], $subject1, $content, 1);

                // 관리자에게 메일발송
                if ($super['email'] != $od['email']) {
                    mailer($od['name'], $od['email'], $super['email'], $subject2, $content, 1);
                }
            }
            // 주문 정보 임시 데이터 삭제
            if ($od_pg == 'inicis') {
                $sql = " delete from shop_order_data where od_id = ? and dt_pg = ? ";
                $param = array($od_id, $od_pg);
                $this->query_model->returnNull($sql, $param);
            }
            // 주문번호제거
            set_session('ss_order_id', '');

            // 장바구니 session 삭제
            set_session('ss_cart_id', '');

            // orderinquiryview 에서 사용하기 위해 session에 넣고
            $uid = md5($od_id . $od['od_time'] . $_SERVER['REMOTE_ADDR']);
            set_session('ss_orderview_uid', $uid);
            $result = array();
            $result['res'] = true;
            $result['od_id'] = $od_id;
            $result['uid'] = $uid;
            goto_url(base_url() . 'm/shop/orderinquiryview?od_id=' . $od_id . '&uid=' . $uid);
        } catch (Exception $e) {
            alert($e->getMessage(), base_url());
        }
    }
    private function import_cancel($imp_uid, $merchant_uid, $cancel_memo)
    {
        include_once('./Bill/iamport.php');

        $result = $iamport->cancel(array(
            'imp_uid'        => "$imp_uid",         //merchant_uid에 우선한다
            'merchant_uid'    => "$merchant_uid",     //imp_uid 또는 merchant_uid가 지정되어야 함
            'reason'        => "$cancel_memo",                //취소사유
        ));
        if (!$result->success) {
            alert($result->error['message'], base_url());
        }
    }
    #아임포트 모바일 페이지로 리다이렉트하기전에 사전에 미리 값들을 넣어놓기
    public function preimport()
    {
        try {
            //code...
            $is_member = $this->load->get_var('is_member');
            $member = $this->load->get_var('member');
            $config = $this->load->get_var('config');
            $super = $this->load->get_var('super');
            $default = $this->load->get_var('default');
            $od_pg = $default['de_pg_service'];
            $merchant_uid = $this->input->post('merchant_uid');

            $od_id = $this->input->post('od_id');
            $sql = "select * from shop_order where od_id=?";
            $param = array($od_id);
            $od = $this->query_model->returnOneArr($sql, $param);
            if (!$od['od_id']) {
                throw new Exception("결제할 주문서가 없습니다.");
            }
            if (!$merchant_uid) {
                throw new Exception("고유값이 없습니다.");
            }
            $stotal =   $this->global_lib->get_order_spay($od_id); // 총계

            $i_price = (int)$stotal['useprice']; // 결제금액
            $i_usepoint = (int)$stotal['usepoint']; // 포인트결제액

            if (!$i_price) {
                throw new Exception("결제할 금액이 없습니다.");
            }
            $od_settle_case = $this->input->post('od_settle_case');

            if (in_array($od_settle_case, array('무통장', '포인트'))) {
                throw new Exception("올바른 방법으로 이용해 주십시오.");
            }

            if ($od_settle_case != 'KAKAOPAY' && $default['de_pg_service'] == 'lg' && !$_POST['LGD_PAYKEY'])
                throw new Exception('결제등록 요청 후 주문해 주십시오.');
            $od_tno = '';
            $od_status = 0;
            $od_bank_account = "";
            $od_deposit_name = "";

            $od_tax_mny = round($i_price / 1.1);
            $od_vat_mny = $i_price - $od_tax_mny;
            $od_free_mny = 0;
            if ($default['de_tax_flag_use']) {
                $od_tax_mny = (int)$this->input->post('comm_tax_mny');
                $od_vat_mny = (int)$this->input->post('comm_vat_mny');
                $od_free_mny = (int)$this->input->post('comm_free_mny');
            }

            // 주문서에 UPDATE

            $sql = " update shop_order set merchant_uid=?,od_tax_mny = ?, od_vat_mny=?, od_free_mny= ? where od_id = ?";
            $param2 = array($merchant_uid, $od_tax_mny, $od_vat_mny, $od_free_mny, $od_id);

            $result = $this->query_model->returnNull($sql, $param2);
            if ($result) {
                // 장바구니 상태변경
                $sql = " update shop_cart set ct_select = '1' where od_id = ?";
                $param = array($od_id);
                $this->query_model->returnNull($sql, $param);
            } else {
                // 주문정보 UPDATE 오류시 결제 취소
                die('<p>고객님의 주문 정보를 처리하는 중 오류가 발생해서 주문이 완료되지 않았습니다.</p><p>' . strtoupper($od_pg) . '를 이용한 전자결제(신용카드, 계좌이체, 가상계좌 등)은 자동 취소되었습니다.');
            }
            $result = array();
            $result['res'] = 'true';
            $result['od_id'] = $od_id;
            $result['uid'] = $merchant_uid;
            #goto_url(base_url().'shop/orderinquiryview?od_id='.$od_id.'&uid='.$uid);
        } catch (Exception $e) {
            $result['res'] = 'false';
            $result['msg'] = $e->getMessage();
        }
        echo json_encode($result);
    }
    public function view_option()
    {
        $gs_id = $this->input->post('gs_id');
        $opt_id = $this->input->post('opt_id');
        $idx = $this->input->post('idx');
        $sel_count = $this->input->post('sel_count');

        $gs = $this->global_lib->get_goods($gs_id, 'type');
        $type = $gs['type'];

        if ($type == "timesale") {
            $amt = $this->gvcomplex_lib->get_timesale_goods_price($gs_id);
        } else {
            $amt = $this->gvcomplex_lib->get_sale_price($gs_id);
        }

        $sql = " select * from shop_goods_option where io_type = '0' and gs_id = ? and io_use = '1' and io_id like '$opt_id%' order by io_no asc";
        $param = array($gs_id);
        $result = $this->query_model->returnArr($sql, $param);

        $str = '<option value="">(필수) 선택하세요</option>';
        $opt = array();

        foreach ($result as $key => $row) {
            $val = explode(' ', $row['io_id']);
            $key = $idx + 1;

            if (!$val[$key])
                continue;

            if (in_array($val[$key], $opt))
                continue;

            $opt[] = $val[$key];

            if ($key + 1 < $sel_count) {
                $str .= PHP_EOL . '<option value="' . $val[$key] . '">' . $val[$key] . '</option>';
            } else {
                if ($row['io_price'] >= 0)
                    $price = '&nbsp;&nbsp;+ ' . display_price($row['io_price']);
                else
                    $price = '&nbsp;&nbsp; ' . display_price($row['io_price']);

                $io_stock_qty = $this->global_lib->get_option_stock_qty($gs_id, $row['io_id'], $row['io_type']);

                if ($io_stock_qty < 1)
                    $soldout = '&nbsp;&nbsp;[품절]';
                else
                    $soldout = '';

                $str .= '<option value="' . $val[$key] . ',' . $row['io_price'] . ',' . $io_stock_qty . ',' . $amt . '">' . $val[$key] . $price . $soldout . '</otpion>';
            }
        }
        echo $str;
    }

    #답글 처리 함수
    public function tail_crud()
    {
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $qstr = $this->load->get_var('qstr');

        $_POST = array_map('trim', $_POST);
        $memo = $this->input->post('memo');
        $page = $this->input->post('page');
        $boardid = $this->input->post('boardid');
        $index_no = $this->input->post('index_no');
        $writer_s = $this->input->post('writer_s');
        $passwd = $this->input->post('passwd');
        $passwd = (isset($passwd) && $passwd != null) ? $passwd : "";
        $mode = $this->input->post('mode');


        if (substr_count($memo, "&#") > 50) {
            alert("내용에 올바르지 않은 코드가 다수 포함되어 있습니다.");
            exit;
        }

        if (!$_POST['writer_s']) {
            alert("작성자명은 필수 입니다.");
        }

        if ($is_member)
            $writer = $member['index_no'];
        else
            $writer = 0;

        if ($mode == 'w') {
            $sql = "insert into shop_board_{$boardid}_tail set board_index = ?, writer= ?, writer_s= ?, memo= ?, wdate= ?, wip= ?, passwd= ?";
            $param = array($index_no, $writer, $writer_s, $memo, time(), $_SERVER['REMOTE_ADDR'], $passwd);
            $this->query_model->returnNull($sql, $param);

            $sql2 = "select count(board_index) as tail_count from shop_board_{$boardid}_tail where board_index = ?";
            $data = [
                $index_no,
            ];
            $result = $this->query_model->returnOneArr($sql2, $data);
            $tail_count = $result['tail_count'];
            log_message('debug', "tail_count: " . $tail_count);

            $sql = "update shop_board_{$boardid} set tailcount={$tail_count} where index_no=?";
            $param = array($index_no);
            $this->query_model->returnNull($sql, $param);
        } else if ($mode == 'd') {
            $tail_index_no = $this->input->post('tail_index_no');

            // 1) 해당 댓글 삭제
            $where = [
                'index_no' => $tail_index_no,
            ];
            $this->query_model->delete("shop_board_{$boardid}_tail", $where);
            // 댓글 갯수 업데이트
            $sql2 = "select count(board_index) as tail_count from shop_board_{$boardid}_tail where board_index = ?";
            $data = [
                $index_no,
            ];
            $result = $this->query_model->returnOneArr($sql2);
            $tail_count = $result['tail_count'];

            $sql = "update shop_board_{$boardid} set tailcount={$tail_count} where index_no=?";
            $param = array($index_no);
            $this->query_model->returnNull($sql, $param);
        }

        goto_url(base_url() . "m/bbs/board_read?index_no=$index_no&boardid=$boardid$qstr&page=$page");
    }



    //좋아요 증가 감소
    public function ajax_goodupdate()
    {
        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $index_no = $this->input->post('index_no');
        $index_no = trim($index_no);
        $board_id = 37;

        if (!$is_member) {
            die('회원 전용 서비스입니다.');
        }

        if (!$index_no) {
            die('잘못된 접근입니다.');
        }

        $sql = "select count(*) as cnt from shop_request_goods_good where mb_id = ? and board_{$board_id}_index_no = ?";
        $param = array($member['id'], $index_no);
        $row = $this->query_model->returnOneArr($sql, $param);

        if ($row['cnt']) {
            $sql = " delete from shop_request_goods_good where mb_id = ? and board_{$board_id}_index_no = ?";
            $param = array($member['id'], $index_no);
            $this->query_model->returnNull($sql, $param);
            $count = $this->gvcomplex_lib->get_good_count($index_no, $board_id);

            echo json_encode([
                'status' => 'DELETE',
                'count' => $count,
            ]);
        } else {
            $sql = "insert shop_request_goods_good set mb_id = ?, board_{$board_id}_index_no = ?, wi_time = '" . $OM_TIME_YMDHIS . "', wi_ip = '{$_SERVER['REMOTE_ADDR']}' ";
            $param = array($member['id'], $index_no);
            $this->query_model->returnNull($sql, $param);
            $count = $this->gvcomplex_lib->get_good_count($index_no, $board_id);

            echo json_encode([
                'status' => 'INSERT',
                'count' => $count,
            ]);
        }
    }
}
