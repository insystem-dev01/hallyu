<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Summary of Bbs
 * @property CI_Input $input
 * @property CI_Session $session
 * @property Funcomplex_lib $funcomplex_lib
 * @property Template $template
 * @property Mypage_template $mypage_template
 * @property Query_model $query_model
 * @property Gvcomplex_lib $gvcomplex_lib
 * @property Thumbnail_lib $thumbnail_lib
 * @property Global_lib $global_lib
 */
class Bbs extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('global_helper');
        $this->load->model('query_model');
        $this->load->library('gvcomplex_lib');
        $this->load->library('global_lib');
        $this->load->library('funcomplex_lib');
        $this->load->library('thumbnail_lib');
        $this->load->library('template');

        $device = $this->input->get('device');
    }
    #로그인 화면
    public function login()
    {
        $is_member = $this->load->get_var('is_member');
        $url = $this->input->get('url');
        // 이미 로그인 중이라면
        if ($is_member) {
            if ($url)
                goto_url($url);
            else
                goto_url(base_url() . 'm/main');
        }
        $tb['title'] = "로그인";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->loginSkin();
        $this->template->mb_footer();
    }
    private function loginSkin()
    {
        $default = $this->load->get_var('default');
        $url = $this->input->get('url');
        $login_url = $this->funcomplex_lib->login_url($url);

        $login_action_url = base_url() . "m/process/login_check";
        $snsLogin = "";
        $snsJs = "";
        if ($default['de_sns_login_use']) {
            $snsLogin .= "
            <p class=\"sns_btn\">";
            if ($default['de_naver_appid'] && $default['de_naver_secret']) {
                $strObj = $this->gvcomplex_lib->get_login_oauth('naver', 1);
                $snsLogin .= $strObj['str'];
                $snsJs .= $strObj['strJs'];
            }
            if ($default['de_facebook_appid'] && $default['de_facebook_secret']) {
                $strObj = $this->gvcomplex_lib->get_login_oauth('facebook', 1);
                $snsLogin .= $strObj['str'];
                $snsJs .= $strObj['strJs'];
            }
            if ($default['de_google_appid'] && $default['de_google_secret']) {
                $strObj = $this->gvcomplex_lib->get_login_oauth('google', 1);
                $snsLogin .= $strObj['str'];
                $snsJs .= $strObj['strJs'];
            }
            if ($default['de_kakao_rest_apikey']) {
                $strObj = $this->gvcomplex_lib->get_login_oauth('kakao', 1);
                $snsLogin .= $strObj['str'];
                $snsJs .= $strObj['strJs'];
            }
            $snsLogin .= "
            </p>";
        }
        $noLogin = "";
        if (preg_match("/orderform/", $url)) {
            $noLogin = "
            <section class=\"mb_login_od\">
                <h3>비회원 구매</h3>
                <p class=\"mart15\"><a href=\"" . base_url() . "m/shop/orderform\" class=\"btn_medium wfull red\">비회원으로 구매하기</a></p>
            </section>";
        } else if (preg_match("/orderinquiry$/", $url)) {
            $noLogin = "
            <form name=\"forderinquiry\" method=\"post\" action=\"" . base_url() . "m/shop/orderinquiry\" autocomplete=\"off\">
            <section class=\"mb_login_od\">
                <h3>비회원 주문조회</h3>
                <p class=\"mart15\">
                    <label for=\"od_id\" class=\"sound_only\">주문번호</label>
                    <input type=\"text\" name=\"od_id\" id=\"od_id\" placeholder=\"주문번호\">
                </p>
                <p class=\"mart3\">
                    <label for=\"od_pwd\" class=\"sound_only\">비밀번호</label>
                    <input type=\"password\" name=\"od_pwd\" id=\"od_pwd\" placeholder=\"비밀번호\">
                </p>
                <p class=\"mart10\"><button type=\"submit\" class=\"btn_medium wfull\">확인</button></p>
            </section>
            </form>";
        }
        $data = array('login_action_url' => $login_action_url, 'login_url' => $login_url, 'snsLogin' => $snsLogin, 'noLogin' => $noLogin, 'snsJs' => $snsJs);
        $this->load->view('mb/bbs/login', $data);
    }
    #회원가입화면1
    public function register()
    {
        // 회원가입 유형
        $type = $this->input->get('type');

        $is_member = $this->load->get_var('is_member');
        $pt_id = $this->load->get_var('pt_id');
        $config = $this->load->get_var('config');
        if ($is_member) {
            goto_url(base_url() . 'm/main');
        }
        // 본사쇼핑몰에서 회원가입을 받지 않을때
        $config['admin_reg_msg'] = str_replace("\r\n", "\\r\\n", $config['admin_reg_msg']);
        if ($config['admin_reg_yes'] && $pt_id == 'admin') {
            alert($config['admin_reg_msg'], base_url() . 'm/main');
        }
        // 세션을 지웁니다.
        set_session("ss_mb_reg", "");
        $tb['title'] = '약관동의';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->registerSkin($type);
        $this->template->mb_footer();
    }

    private function registerSkin($type)
    {
        $register_action_url = base_url() . "m/bbs/register_form?type={$type}";
        $default = $this->load->get_var('default');
        $config = $this->load->get_var('config');

        $data = array(
            'title' => '회원가입',
            'register_action_url' => $register_action_url,
            'type' => $type
        );

        $this->load->view('mb/bbs/register_type', $data);
    }

    #회원가입 화면 2
    public function register_form()
    {
        header("Cache-Control: no cache");
        $this->pre_register_process();

        $this->template->mb_header();
        $this->register_formSkin();
        $this->template->mb_footer();
    }

    private function register_formSkin()
    {
        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');
        $pt_id = $this->load->get_var('pt_id');

        $w = $this->input->post('w');
        $type = $this->input->get('type');

        $required = ($w == '') ? ' required' : '';
        $readonly = ($w == 'u') ? ' readonly' : '';

        // add_javascript('js 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
        $OM_POSTCODE_JS = "";
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {   //https 통신일때 daum 주소 js
            $OM_POSTCODE_JS = '<script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>';
        } else {  //http 통신일때 daum 주소 js
            $OM_POSTCODE_JS = '<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>';
        }

        if ($config['register_use_addr'])
            $this->gvcomplex_lib->add_javascript($OM_POSTCODE_JS, 0); //다음 주소 js
        $register_action_url = base_url() . 'm/process/register_form_update';

        $certifyJs = "";
        if ($config['cf_cert_use'] && ($config['cf_cert_ipin'] || $config['cf_cert_hp'])) {
            $certifyJs = "<script src=\"" . base_url() . "js/certify.js?v=" . time() . "\"></script>";
        }

        $notUTag = "";
        if ($w != "u") {
            $notUTag = "
            <tr class=\"first_line\">
                <th scope=\"row\"><label for=\"reg_mb_id\">아이디</label>
                <span id=\"mb_id_check\" class=\"check\"></span>
                </th>
                <td>
                    <input type=\"text\" name=\"mb_id\" value=\"{$member['id']}\" id=\"mb_id\" {$required} {$readonly} class=\"frm_input\" maxlength=\"20\" placeholder=\"아이디를 입력해주세요\" onkeyup=\"id_check(this); only_eng_num_text_typing(this)\">
                    <span id=\"msg_mb_id\"></span>
                    <button type=\"button\" onclick=\"reg_mb_id_check()\">중복확인</button>
                    <input type=\"hidden\" id=\"mb_id_check_confirm\">
                </td>
            </tr>";
        }
        $member['name'] = isset($member['name']) ? $member['name'] : "";
        $member['cellphone'] = isset($member['cellphone']) ? $member['cellphone'] : "";
        $memEmail = (isset($member['email']) ? $member['email'] : '');
        $mailser = ($w == '' || $member['mailser'] == 'Y') ? ' checked' : '';
        $member['zip'] = isset($member['zip']) ? $member['zip'] : "";
        $member['addr1'] = isset($member['addr1']) ? $member['addr1'] : "";
        $member['addr2'] = isset($member['addr2']) ? $member['addr2'] : "";
        $member['addr3'] = isset($member['addr3']) ? $member['addr3'] : "";
        $member['addr_jibeon'] = isset($member['addr_jibeon']) ? $member['addr_jibeon'] : "";
        $member['email'] = isset($member['email']) ? $member['email'] : "";
        $member['mb_certify'] = isset($member['mb_certify']) ? $member['mb_certify'] : "";

        $memberName = "";
        $placeholder_name = $type == "user" ? "" : "Hallyu";

        if ($w != "u") {
            $memberName = "<input type=\"text\" name=\"mb_name\" value=\"" . $this->funcomplex_lib->get_text($member['name']) . "\" id=\"mb_name\"{$required} readonly class=\"frm_input\" placeholder=\"사업자 번호 중복확인을 먼저 해주세요.\" onkeyup=\"basic_text_check('mb_name','상호명을 작성해주세요.')\">
                        <input type=\"hidden\" id=\"mb_name_check_confirm\">";
        } else {
            $memberName = "<span id=\"reg_mb_name\">" . $this->funcomplex_lib->get_text($member['name']) . "</span>";
        }

        $cert_str = '';
        if ($config['cf_cert_use']) {
            if ($config['cf_cert_ipin'])
                $cert_str .= '<button type="button" id="win_ipin_cert" class="btn_small">아이핀 본인인증</button>';
            if ($config['cf_cert_hp'])
                $cert_str .= '<button type="button" id="win_hp_cert" class="btn_small">휴대폰 본인인증</button>';

            $cert_str .= '<noscript>본인인증을 위해서는 자바스크립트 사용이 가능해야합니다.</noscript>';
        }

        $cert_strTag = "";
        if ($cert_str) $cert_strTag = '<div class="mart5">' . $cert_str . '</div>';

        $msgCertifyTag = "";
        if ($config['cf_cert_use'] && $member['mb_certify']) {
            if ($member['mb_certify'] == 'ipin')
                $mb_cert = '아이핀';
            else
                $mb_cert = '휴대폰';
            $msgCertifyTag .= "
            <div id=\"msg_certify\">
				<strong>{$mb_cert} 본인인증</strong>";
            if ($member['mb_adult']) {
                $msgCertifyTag .= "및 <strong>성인인증</strong>";
            }
            $msgCertifyTag .= "완료
			</div>";
        }

        $cf_cert_useTag = "";
        if ($config['cf_cert_use']) {
            $cf_cert_useTag = "<span class=\"frm_info\">아이핀 본인인증 후에는 이름이 자동 입력되고 휴대폰 본인인증 후에는 이름과 휴대폰번호가 자동 입력되어 수동으로 입력할수 없게 됩니다.</span>";
        }

        $register_use_telTag = "";
        if ($config['register_use_tel']) {
            $register_use_telTag = "
            <tr>
                <th scope=\"row\"><label for=\"reg_mb_tel\">전화번호</label></th>
                <td><input type=\"text\" name=\"mb_tel\" value=\"" . $this->funcomplex_lib->get_text($member['telephone']) . "\" id=\"reg_mb_tel\"" . ($config['register_req_tel'] ? ' required' : '') . " class=\"frm_input" . ($config['register_req_tel'] ? ' required' : '') . "\" size=\"20\" maxlength=\"20\"></td>
            </tr>";
        }

        $register_use_hpTag = "";
        if ($config['register_use_hp'] || $config['cf_cert_hp']) {
            $mb_hp = array('010', '', '');
            if (!empty($member['cellphone'])) {
                $mb_hp = $member['cellphone'];
                $mb_hp = explode("-", $mb_hp);
            }
            $register_use_hpTag .= "
            <tr>
                <th scope=\"row\"><label for=\"reg_mb_hp\">연락처</label>
                    <span id=\"mb_hp_check\" class=\"check\" ></span>
                </th>
                <td class=\"vertical\">
                    <div>
                        <input type=\"text\" name=\"mb_hp_01\" id=\"mb_hp_01\" value=\"{$mb_hp[0]}\" " . ($config['register_req_hp'] ? ' required' : '') . " class=\"frm_input small_box\" maxlength=\"3\" onkeyup=\"only_num_typing(this); mb_hp_check();\">
                        <span class=\"dash\">-</span>
                        <input type=\"text\" name=\"mb_hp_02\" id=\"mb_hp_02\" value=\"{$mb_hp[1]}\" " . ($config['register_req_hp'] ? ' required' : '') . " class=\"frm_input small_box\" maxlength=\"4\" onkeyup=\"only_num_typing(this); mb_hp_check();\">
                        <span class=\"dash\">-</span>
                        <input type=\"text\" name=\"mb_hp_03\" id=\"mb_hp_03\" value=\"{$mb_hp[2]}\" " . ($config['register_req_hp'] ? ' required' : '') . " class=\"frm_input small_box\" maxlength=\"4\" onkeyup=\"only_num_typing(this); mb_hp_check();\">
                    </div>
                    <span class=\"frm_info\">
                        <input type=\"checkbox\" name=\"mb_sms\" id=\"reg_mb_phone\" value=\"Y\"" . (($w == '' || $member['smsser'] == 'Y') ? ' checked' : '') . ">
                        <label for=\"reg_mb_phone\">
                            <span>휴대폰 문자메세지를 받겠습니다.</span>
                        </label>
                    </span>
                    <input type=\"hidden\" id=\"mb_hp_check_confirm\">";
            if ($config['cf_cert_use'] && $config['cf_cert_hp']) {
                $register_use_hpTag .= "<input type=\"hidden\" name=\"old_mb_hp\" value=\"" . $this->funcomplex_lib->get_text($member['cellphone']) . "\">";
            }
            $register_use_hpTag .= "
                </td>
            </tr>";
        }

        $memberEmail = isset($member['email']) ? $member['email'] : '';
        $memberMail = ($w == '' || $member['mailser'] == 'Y') ? ' checked' : '';

        $regiUseAddrTag = "";
        if ($config['register_use_addr']) {
            $regiUseAddrTag = "
            <tr>
                <th scope=\"row\"><label for=\"reg_mb_addr\">주소</label>
                <span id=\"mb_addr_check\" class=\"check\"></span>
                </th>
                <td class=\"addr_row\">
                    <div class=\"horizontal\">
                        <input type=\"text\" name=\"mb_zip\" value=\"{$member['zip']}\" id=\"reg_mb_zip\" " . ($config['register_req_addr'] ? ' required' : '') . " class=\"frm_address\" maxlength=\"5\" readonly=\"readonly\" placeholder=\"주소검색을 해주세요.\" oninput=\"mb_addr_check()\" >
                        <button type=\"button\" class=\"btn_small\" onclick=\"win_zip('fregisterform', 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');\">주소검색</button><br>
                    </div>
                    <div class=\"vertical\">
                        <input type=\"text\" name=\"mb_addr1\" value=\"" . $this->funcomplex_lib->get_text($member['addr1']) . "\" id=\"reg_mb_addr1\"" . ($config['register_req_addr'] ? ' required' : '') . " class=\"frm_address\" frm_address\" readonly=\"readonly\" placeholder=\"기본 주소\" oninput=\"mb_addr_check()\">
                        <input type=\"text\" name=\"mb_addr2\" value=\"" . $this->funcomplex_lib->get_text($member['addr2']) . "\" id=\"reg_mb_addr2\" class=\"frm_address\" placeholder=\"상세 주소 ex) 102호\" oninput=\"mb_addr_check()\">
                        <input type=\"text\" name=\"mb_addr3\" value=\"" . $this->funcomplex_lib->get_text($member['addr3']) . "\" id=\"reg_mb_addr3\" class=\"frm_address\" readonly=\"readonly\" placeholder=\"기본 주소2\" oninput=\"mb_addr_check()\">
                    </div>
                    
                    <input type=\"hidden\" id=\"mb_addr_check_confirm\">
                    <input type=\"hidden\" name=\"mb_addr_jibeon\" value=\"" . $this->funcomplex_lib->get_text($member['addr_jibeon']) . "\">
                </td>
            </tr>";
        }

        if ($w == "") {
            $btnTitle = "가입하기";
            // 추천인을 담는다.
            $member['pt_id'] = $pt_id;
        } else if ($w == 'u') {
            $btnTitle = "수정하기";
        }

        $ipinJs = "";
        if ($config['cf_cert_use'] && $config['cf_cert_ipin']) {
            $ipinJs = "// 아이핀인증
            $(\"#win_ipin_cert\").click(function() {
                if(!cert_confirm())
                    return false;
        
                var url = \"" . base_url() . "plugin/okname/ipin1\";
                certify_win_open('kcb-ipin', url);
                return;
            });";
        }
        $hpcertJs = "";
        if ($config['cf_cert_use'] && $config['cf_cert_hp']) {
            $hpcertJs = "$(\"#win_hp_cert\").click(function() {
                if(!cert_confirm())
                    return false;";
            switch ($config['cf_cert_hp']) {
                case 'kcb':
                    $cert_url = base_url() . 'plugin/okname/hpcert1';
                    $cert_type = 'kcb-hp';
                    break;
                case 'kcp':
                    $cert_url =  base_url() . 'plugin/kcpcert/kcpcert_form';
                    $cert_type = 'kcp-hp';
                    break;
                case 'lg':
                    $cert_url = base_url() . 'plugin/lgxpay/AuthOnlyReq';
                    $cert_type = 'lg-hp';
                    break;
                default:
                    $hpcertJs .= 'alert("기본환경설정에서 휴대폰 본인인증 설정을 해주십시오"); return false;';
                    break;
            }
            $hpcertJs .= "certify_win_open(\"{$cert_type}\", \"{$cert_url}\");";
        }

        $cf_cert_useJs = "";
        if ($w == '' && $config['cf_cert_use'] && $config['cf_cert_req']) {
            $cf_cert_useJs = "
            // 본인인증 체크
            if(f.cert_no.value==\"\") {
                alert(\"회원가입을 위해서는 본인인증을 해주셔야 합니다.\");
                return false;
            }";
        }

        $regis_use_hpJS = "";
        // if(($config['register_use_hp'] || $config['cf_cert_hp']) && $config['register_req_hp'] && $type == "user") {
        //     $regis_use_hpJS="
        //     // 휴대폰번호 체크
        //     var msg = reg_mb_hp_check();
        //     if(msg.trim()) {
        //         alert(msg);
        //         f.reg_mb_hp.select();
        //         return false;
        //     }";
        // }

        $data = array(
            'register_action_url' => $register_action_url,
            'OM_POSTCODE_JS' => $OM_POSTCODE_JS,
            'certifyJs' => $certifyJs,
            'w' => $w,
            'type' => $type,
            'pt_id' => $pt_id,
            'member' => $member,
            'notUTag' => $notUTag,
            'required' => $required,
            'memberName' => $memberName,
            'cert_strTag' => $cert_strTag,
            'msgCertifyTag' => $msgCertifyTag,
            'cf_cert_useTag' => $cf_cert_useTag,
            'register_use_telTag' => $register_use_telTag,
            'register_use_hpTag' => $register_use_hpTag,
            'memberEmail' => $memberEmail,
            'memberMail' => $memberMail,
            'regiUseAddrTag' => $regiUseAddrTag,
            'btnTitle' => $btnTitle,
            'ipinJs' => $ipinJs,
            'hpcertJs' => $hpcertJs,
            'cf_cert_useJs' => $cf_cert_useJs,
            'regis_use_hpJS' => $regis_use_hpJS
        );
        $this->load->view('mb/bbs/register_form', $data);
    }

    private function pre_register_process()
    {
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $config = $this->load->get_var('config');
        $pt_id = $this->load->get_var('pt_id');
        $w = $this->input->post('w');

        $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
        $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);
        $OM_HASH_TOKEN = md5(base_url() . $OM_TIME_YMD . $_SERVER['REMOTE_ADDR']);

        // 불법접근을 막도록 토큰생성
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        set_session("ss_cert_no",   "");
        set_session("ss_cert_hash", "");
        set_session("ss_cert_type", "");
        set_session("ss_hash_token", $OM_HASH_TOKEN);
        $tb['title'] = "";
        if ($w == "") {
            // 회원 로그인을 한 경우 회원가입 할 수 없다
            // 경고창이 뜨는것을 막기위해 아래의 코드로 대체
            // alert("이미 로그인중이므로 회원 가입 하실 수 없습니다.", "./");
            if ($is_member) {
                goto_url(base_url() . 'm/main');
            }

            // 본사쇼핑몰에서 회원가입을 받지 않을때
            $config['admin_reg_msg'] = str_replace("\r\n", "\\r\\n", $config['admin_reg_msg']);
            if ($config['admin_reg_yes'] && $pt_id == 'admin') {
                alert($config['admin_reg_msg'], base_url() . 'm/main');
            }

            $tb['title'] = '회원가입';

            // 추천인을 담는다.
            $member['pt_id'] = $pt_id;
        } else if ($w == 'u') {
            $mb_password = $this->input->post('mb_password');
            if (!$is_member)
                alert('로그인 후 이용하여 주십시오.', base_url() . 'm/main');

            if ($member['id'] == 'admin')
                alert('관리자의 회원정보는 관리자 화면에서 수정해 주십시오.', base_url() . 'm/main');

            if ($member['id'] != $_POST['mb_id'])
                alert('로그인된 회원과 넘어온 정보가 서로 다릅니다.');

            if ($this->input->post('mb_password')) {
                // 수정된 정보를 업데이트후 되돌아 온것이라면 비밀번호가 암호화 된채로 넘어온것임
                if ($this->input->post('is_update')) {
                    $tmp_password = $_POST['mb_password'];
                } else {
                    $tmp_password = $this->global_lib->changePassword($_POST['mb_password'], $member['id']);
                }

                if ($member['passwd'] != $tmp_password) {
                    alert('비밀번호가 틀립니다.');
                }
            }
            set_session("ss_reg_mb_name", $member['name']);
            set_session("ss_reg_mb_hp", $member['cellphone']);
            $tb['title'] = '회원정보수정';
        } else {
            alert('w 값이 제대로 넘어오지 않았습니다.');
        }
        $this->load->vars(array('tb' => $tb));
    }
    public function register_result()
    {
        $mb = "";
        if (isset($_SESSION['ss_mb_reg']))
            $mb = $this->global_lib->get_member($_SESSION['ss_mb_reg']);

        // 회원정보가 없다면 초기 페이지로 이동
        if (!$mb['id'])
            goto_url(base_url() . 'm');

        $tb['title'] = '회원가입 완료';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->register_resultSkin();
        $this->template->mb_footer();
    }
    private function register_resultSkin()
    {
        $mb = $this->global_lib->get_member($_SESSION['ss_mb_reg']);
        $mbName = $this->funcomplex_lib->get_text($mb['name']);
        $data = array('mbName' => $mbName, 'mb' => $mb);
        $this->load->view('mb/bbs/register_result', $data);
    }
    #비밀번호 찾기
    public function password_lost()
    {
        $is_member = $this->load->get_var('is_member');
        if ($is_member) {
            alert("이미 로그인중입니다.");
        }

        $tb['title'] = '회원정보 찾기';

        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->password_listSkin();
        $this->template->mb_footer();
    }
    private function password_listSkin()
    {
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);

        $form_action_url = base_url() . "m/process/password_lost2";
        $data = array('form_action_url' => $form_action_url, 'token' => $token);
        $this->load->view('mb/bbs/password_lost', $data);
    }
    # 게시판 관련된 함수
    //  public function board_list()
    // {
    //     $board = $this->load->get_var('board');
    //     $tb['title'] = $this->funcomplex_lib->get_text($board['boardname']);
    //     $this->load->vars(array('tb' => $tb));
    //     $this->template->mb_header();
    //     $this->board_listSkin();
    //     $this->template->mb_footer();
    // }
    public function noticeList()
    {
        $board = $this->load->get_var('board');
        $tb['title'] = $this->funcomplex_lib->get_text($board['boardname']);
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->board_listSkin();
        $this->template->mb_footer();
    }
    public function goods_request_list()
    {
        $board = $this->load->get_var('board');
        $tb['title'] = $this->funcomplex_lib->get_text($board['boardname']);
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->board_listSkin();
        $this->template->mb_footer();
    }
    private function board_listSkin()
    {
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $board = $this->load->get_var('board');
        $default = $this->load->get_var('default');
        $pt_id = $this->load->get_var('pt_id');

        $boardid = $this->input->get('boardid');
        $ca_name = $this->input->get('ca_name');
        $sfl = $this->input->get('sfl');
        $stx = addslashes($this->input->get('stx'));
        $page = $this->input->get('page');


        if (!$is_member) {
            $member['grade'] = 99;
        }

        if ($board['list_priv'] < 99) {
            if ($member['grade'] > $board['list_priv']) {
                alert('권한이 없습니다.');
            }
        }
        $add_search = "";
        if ($default['de_board_wr_use']) {
            $add_search = " and pt_id = '$pt_id' ";
        }
        $sql_common = " from shop_board_{$boardid} ";
        $sql_search = " where btype = '2' {$add_search} ";

        if ($ca_name)
            $sql_search .= " and ca_name = '{$ca_name}' ";
        if ($sfl && $stx)
            $sql_search .= " and ($sfl like '%$stx%') ";

        $sql_order  = " order by fid desc, thread asc ";
        $sql = " select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 15;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);

        $bo_reply_limit = 6;
        $bo_imgurl = base_url() . 'bbs/' . $board['skin']; #basic,gallery,webzine
        if ($board['skin'] == 'gallery') {
            #include_once(OM_MTHEME_PATH.'/board_gallery.skin.php');
            $this->board_gallerySkin($result, $total_page, $bo_imgurl);
        } else {
            #include_once(OM_MTHEME_PATH.'/board_list.skin.php');
            $this->boardListSkin($result, $total_page, $bo_imgurl);
        }
    }
    private function board_gallerySkin($result, $total_page, $bo_imgurl)
    {
        $bo_reply_limit = 6;
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $default = $this->load->get_var('default');
        $board = $this->load->get_var('board');
        $member = $this->load->get_var('member');
        $usecate = explode("|", $board['usecate']);
        $ca_name = $this->input->get('ca_name');
        $boardid = $this->input->get('boardid');
        $stx = $this->input->get('stx');
        $sfl = $this->input->get('sfl');
        $pt_id = $this->load->get_var('pt_id');
        $page = $this->input->get('page');

        $filurl1Tag = "";
        if ($board['fileurl1']) {
            $filurl1Tag = "<div class=\"m_bo_hd\"><img src=\"" . base_url() . "data/board/boardimg/{$board['fileurl1']}\"></div>";
        }
        $useCateTag = "";
        if ($board['use_category']) {
            $useCateTag .= "
            <select name=\"faq_type\" class=\"faq_sch\" onchange=\"location=this.value;\">
                <option value=\"" . base_url() . "m/bbs/noticeList?boardid={$boardid}\">전체보기</option>";
            for ($i = 0; $i < count($usecate); $i++) {
                $selected = "";
                if ($usecate[$i] == $ca_name) {
                    $selected = ' selected';
                }
                $useCateTag .= "
                <option value=\"" . base_url() . "m/bbs/noticeList?boardid={$boardid}&ca_name={$usecate[$i]}\" {$selected}>{$usecate[$i]}</option>";
            }
            $useCateTag .= "
            </select>";
        }
        $add_search = "";
        if ($default['de_board_wr_use']) {
            $add_search = " and pt_id = '$pt_id' ";
        }

        $li_run = 0;
        $li_str = '';
        $sql = " select * from shop_board_{$boardid} where btype = '1' {$add_search} order by fid desc ";
        $res = $this->query_model->returnArr($sql);
        $i = 0;
        foreach ($res as $key => $row) {
            $bo_href = base_url() . 'm/bbs/board_read?index_no=' . $row['index_no'] . '&boardid=' . $boardid . '&page=' . $page;
            $bo_subj = '<strong class="fc_eb7">[공지]</strong> ' . $this->funcomplex_lib->get_text($row['subject']);
            $bo_date = $this->funcomplex_lib->get_text($row['writer_s']) . "<span class='padl10'>" . date("y/m/d", $row['wdate']);

            if ((time() - $row['wdate']) < (60 * 60 * 24)) {
                $bo_subj .= " <img src='{$bo_imgurl}/img/iconY.gif' class='marl3'>";
            }
            $thumb = $this->thumbnail_lib->get_list_thumbnail($boardid, $row, 400, 0);
            if (!$thumb['src']) {
                $thumb['src'] = base_url() . 'img/noimage.gif';
            }
            $li_str .= '<li class="gall_item">';
            $li_str .= '	<a href="' . $bo_href . '">';
            $li_str .= '	<dl>';
            $li_str .= '		<dt><img src="' . $thumb['src'] . '" class="lazyload"></dt>';
            $li_str .= '		<dd class="subj">' . $bo_subj . '</dd>';
            $li_str .= '		<dd class="date">' . $bo_date . '</dd>';
            $li_str .= '	</dl>';
            $li_str .= '	</a>';
            $li_str .= '</li>';

            $li_run++;
            $i++;
        }
        foreach ($result as $key => $row) {
            $bo_href = base_url() . 'm/bbs/board_read?index_no=' . $row['index_no'] . '&boardid=' . $boardid . '&page=' . $page;
            $bo_subj = '';
            $spacer = strlen($row['thread'] != 'A');
            if ($spacer > $bo_reply_limit) {
                $spacer = $bo_reply_limit;
            }

            for ($g = 0; $g < $spacer; $g++) {
                $bo_subj = "<img src='{$bo_imgurl}/img/icon_reply.gif'> ";
            }

            $bo_subj = $bo_subj . $this->funcomplex_lib->get_text($row['subject']);
            $bo_date = $this->funcomplex_lib->get_text($row['writer_s']) . "<span class='padl10'>" . date("y/m/d", $row['wdate']);

            if ($row['issecret'] == 'Y') {
                $bo_subj .= " <img src='{$bo_imgurl}/img/icon_secret.gif'>";
            }

            if ((time() - $row['wdate']) < (60 * 60 * 24)) {
                $bo_subj .= " <img src='{$bo_imgurl}/img/iconY.gif'>";
            }
            $thumb = $this->thumbnail_lib->get_list_thumbnail($boardid, $row, 400, 0);
            if (!$thumb['src']) {
                $thumb['src'] = base_url() . 'img/noimage.gif';
            }
            $li_str .= '<li class="gall_item">';
            $li_str .= '	<a href="' . $bo_href . '">';
            $li_str .= '	<dl>';
            $li_str .= '		<dt><img src="' . $thumb['src'] . '" class="lazyload"></dt>';
            $li_str .= '		<dd class="subj">' . $bo_subj . '</dd>';
            $li_str .= '		<dd class="date">' . $bo_date . '</dd>';
            $li_str .= '	</dl>';
            $li_str .= '	</a>';
            $li_str .= '</li>';
            $li_run++;

            $i++;
        }
        $boardTb = "";
        if ($li_run > 0)
            $boardTb = "<ul id=\"m_gall_ul\">\n{$li_str}</ul>\n";
        else
            $boardTb = "<p class=\"empty_list\">게시글이 없습니다.</p>\n";
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?boardid=' . $boardid . '&page=');
        $can_write = "";
        if ($member['grade'] <= $board['write_priv']) {
            $can_write = "
            <div class=\"btn_confirm\">
                <a href=\"" . base_url() . "m/bbs/board_write?boardid={$boardid}\" class=\"btn_medium\">글쓰기</a>
            </div>";
        }
        $gw_search_value = $this->config->item('gw_search_value');
        $gw_search_text = $this->config->item('gw_search_text');
        $searchOpt = "";
        for ($i = 0; $i < sizeof($gw_search_value); $i++) {
            $searchOpt .= "<option value='{$gw_search_value[$i]}'" . get_selected($gw_search_value[$i], $sfl) . ">{$gw_search_text[$i]}</option>\n";
        }

        $data = array(
            'filurl1Tag' => $filurl1Tag,
            'useCateTag' => $useCateTag,
            'boardTb' => $boardTb,
            'pagination' => $pagination,
            'can_write' => $can_write,
            'boardid' => $boardid,
            'searchOpt' => $searchOpt,
            'stx' => $stx
        );
        $this->load->view('mb/bbs/board_gallery', $data);
    }
    private function boardListSkin($result, $total_page, $bo_imgurl)
    {
        $bo_reply_limit = 6;
        $board = $this->load->get_var('board');
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $config = $this->load->get_var('config');
        $default = $this->load->get_var('default');
        $member = $this->load->get_var('member');
        $board = $this->load->get_var('board');
        $usecate = explode("|", $board['usecate']);
        $ca_name = $this->input->get('ca_name');
        $boardid = $this->input->get('boardid');
        $stx = $this->input->get('stx');
        $page = $this->input->get('page');
        $sfl = $this->input->get('sfl');
        $pt_id = $this->load->get_var('pt_id');

        $fileurl1Tag = "";
        if ($board['fileurl1']) {
            $fileurl1Tag = "<div class=\"m_bo_hd\"><img src=\"" . base_url() . "data/board/boardimg/{$board['fileurl1']}\"></div>";
        }
        $useCateTag = "";
        if ($board['use_category']) {
            $useCateTag .= "
            <select name=\"faq_type\" class=\"faq_sch\" onchange=\"location=this.value;\">
                <option value=\"" . base_url() . "m/bbs/noticeList?boardid={$boardid}\">전체보기</option>";
            for ($i = 0; $i < count($usecate); $i++) {
                $selected = "";
                if ($usecate[$i] == $ca_name) {
                    $selected = ' selected';
                }
                $useCateTag .= "
                <option value=\"" . base_url() . "m/bbs/noticeList?boardid={$boardid}&ca_name={$usecate[$i]}\" {$selected}>{$usecate[$i]}</option>";
            }
            $useCateTag .= "
            </select>";
        }
        $li_run = 0;
        $li_str = '';
        $add_search = "";
        if ($default['de_board_wr_use']) {
            $add_search = " and pt_id = '$pt_id' ";
        }
        $sql = " select * from shop_board_{$boardid} where btype = '1' {$add_search} order by fid desc ";
        $res = $this->query_model->returnArr($sql);


        //공지사항
        foreach ($res as $key => $row) {
            $bo_href = base_url() . 'm/bbs/board_read?index_no=' . $row['index_no'] . '&boardid=' . $boardid . '&page=' . $page;
            $bo_subj = '<strong class="fc_eb7">[공지]</strong> ' . $this->funcomplex_lib->get_text($row['subject']);
            $bo_writer = $this->funcomplex_lib->get_text($row['writer_s']);
            $bo_date =  date("y/m/d", $row['wdate']);

            if ((time() - $row['wdate']) < (60 * 60 * 24)) {
                $bo_subj .= " <img src='{$bo_imgurl}/img/iconY.gif' class='marl3'>";
            }

            if ($row['fileurl1'] || $row['fileurl2'])
                $bo_file_yes = "<img src='" . $bo_imgurl . "/img/img_icon.png'>";
            else
                $bo_file_yes = "";

            $li_str .= '<li class="list">';
            $li_str .= '	<a href="' . $bo_href . '">';
            $li_str .= '	<p class="subj">' . $bo_subj . '</p>';
            $li_str .= '	<div class="info">';
            $li_str .= "    <span class=\"writer\">{$bo_writer}</span>
                            <span class=\"date\">{$bo_date}</span>";
            if ($boardid == 37) {
                $li_str .= "           
                            <span class=\"likes\">좋아요 <span class=\"good_count\">{$row['good_count']}</span></span>";
            }
            $li_str .= "<span class=\"views\">조회 {$row['readcount']}</span>
            <div class=\"icon-wrapper\">";
            if ($bo_file_yes) {
                $li_str .= "{$bo_file_yes}";
            }
            if ($row['tailcount']) {
                $li_str .= "<span class=\"fc_197\">{$row['tailcount']}</span>";
            }
            $li_str .= '</div>
                            </div>';
            $li_str .= '	</a>';
            $li_str .= '</li>';

            $li_run++;
            $tableTag = "";
            if ($li_run > 0)
                $tableTag = "<ul>\n{$li_str}</ul>\n";
            else
                $tableTag = "<p class=\"empty_list\">게시글이 없습니다.</p>\n";

            $canWrite = "";
            $canWrite = "<div class=\"btn_confirm\">
                <span class=\"total_count_text\">전체 <span class=\"total_count_count\">{$li_run}</span></span>";
            if ($member['grade'] <= $board['write_priv']) {
                $canWrite .= "
                <a href=\"" . base_url() . "m/bbs/board_write?boardid={$boardid}\" class=\"btn_medium\">글쓰기</a>
            ";
            }
            $canWrite .= "</div>";
        }
        //일반 공지
        foreach ($result as $key => $row) {
            $bo_href = base_url() . 'm/bbs/board_read?index_no=' . $row['index_no'] . '&boardid=' . $boardid . '&page=' . $page;
            $bo_subj = '';
            $spacer = strlen($row['thread'] != 'A');
            if ($spacer > $bo_reply_limit) {
                $spacer = $bo_reply_limit;
            }

            for ($g = 0; $g < $spacer; $g++) {
                $bo_subj = "<img src='{$bo_imgurl}/img/icon_reply.gif'> ";
            }

            if ($board['use_category'] == '1'  && $row['ca_name']) {
                $bo_subj .= '<strong>[' . $row['ca_name'] . ']</strong> ';
            }

            $bo_subj = $bo_subj . $this->funcomplex_lib->get_text($row['subject']);
            $bo_writer = $this->funcomplex_lib->get_text($row['writer_s']);
            $bo_date =  date("y/m/d", $row['wdate']);


            if ($row['issecret'] == 'Y') {
                $bo_subj .= " <img src='{$bo_imgurl}/img/icon_secret.gif'>";
            }

            if ((time() - $row['wdate']) < (60 * 60 * 24)) {
                $bo_subj .= " <img src='{$bo_imgurl}/img/iconY.gif'>";
            }

            if ($row['fileurl1'] || $row['fileurl2'])
                $bo_file_yes = "<img src='" . $bo_imgurl . "/img/img_icon.png'>";
            else
                $bo_file_yes = "";


            $li_str .= '<li class="list">';
            $li_str .= '	<a href="' . $bo_href . '">';
            $li_str .= '	<p class="subj">' . $bo_subj . '</p>';
            $li_str .= '	<div class="info">';
            $li_str .= "    <span class=\"writer\">{$bo_writer}</span>
                            <span class=\"date\">{$bo_date}</span>";
            if ($boardid == 37) {
                $li_str .= "           
                            <span class=\"likes\">좋아요 <span class=\"good_count\">{$row['good_count']}</span></span>";
            }
            $li_str .= "<span class=\"views\">조회 {$row['readcount']}</span>
                        <div class=\"icon-wrapper\">";
            if ($bo_file_yes) {
                $li_str .= "{$bo_file_yes}";
            }
            if ($row['tailcount']) {
                $li_str .= "<span class=\"fc_197\">{$row['tailcount']}</span>";
            }
            $li_str .= '</div>
                            </div>';
            $li_str .= '	</a>';
            $li_str .= '</li>';

            $li_run++;
        }
        $tableTag = "";
        if ($li_run > 0)
            $tableTag = "<ul>\n{$li_str}</ul>\n";
        else
            $tableTag = "<p class=\"empty_list\">게시글이 없습니다.</p>\n";

        $canWrite = "";
        $canWrite = "<div class=\"btn_confirm\">
                <span class=\"total_count_text\">전체 <span class=\"total_count_count\">{$li_run}</span></span>";
        if ($member['grade'] <= $board['write_priv']) {
            $canWrite .= "
                <a href=\"" . base_url() . "m/bbs/board_write?boardid={$boardid}\" class=\"btn_medium\">글쓰기</a>
            ";
        }
        $canWrite .= "</div>";
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?boardid=' . $boardid . '&page=');
        $gw_search_value = $this->config->item('gw_search_value');
        $gw_search_text = $this->config->item('gw_search_text');
        $searchOpt = "";
        for ($i = 0; $i < sizeof($gw_search_value); $i++) {
            $searchOpt .= "<option value='{$gw_search_value[$i]}'" . get_selected($gw_search_value[$i], $sfl) . ">{$gw_search_text[$i]}</option>\n";
        }
        $data = array(
            'fileurl1Tag' => $fileurl1Tag,
            'useCateTag' => $useCateTag,
            'tableTag' => $tableTag,
            'canWrite' => $canWrite,
            'pagination' => $pagination,
            'boardid' => $boardid,
            'searchOpt' => $searchOpt,
            'stx' => $stx
        );
        $this->load->view('mb/bbs/boardList', $data);
    }
    #게시판 읽기 모드
    public function board_read()
    {
        $board = $this->load->get_var('board');
        $tb['title'] = $this->funcomplex_lib->get_text($board['boardname']);
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->boardReadSkin();
        $this->template->mb_footer();
    }
    private function boardReadSkin()
    {
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $board = $this->load->get_var('board');
        $write = $this->load->get_var('write');
        $urlencode = $this->load->get_var('urlencode');
        $mb_no = $this->load->get_var('mb_no');
        $index_no = $this->input->get('index_no');
        $boardid = $this->input->get('boardid');
        $page = $this->input->get('page');

        $stx = $this->input->get('stx');
        $sfl = $this->input->get('sfl');
        $qstr = $this->load->get_var('qstr');
        $from_action_url = base_url() . "m/process/tail_crud";
        $bo_writer = $write['writer'];
        $bo_writer_s = $write['writer_s'];
        $bo_file1 = $write['fileurl1'];
        $bo_file2 = $write['fileurl2'];
        $bo_memo = nl2br($write['memo']);
        $accept = array("gif", "jpg", "GIF", "JPG", "PNG", "png");
        // log_message("debug", "member: {$member['index_no']}, bo_writer: {$bo_writer}");

        $qstr1 = "boardid=$boardid$qstr&page=$page";
        $qstr2 = "index_no=$index_no&boardid=$boardid$qstr&page=$page";
        $qstr3 = "boardid=$boardid$qstr";


        if (!$is_member) {
            goto_url(base_url() . "m/bbs/login?url=$urlencode");
            // alert("로그인 후 이용가능합니다");
            return false;
        }


        $prev_index_no = $this->gvcomplex_lib->get_prev_or_next_post($index_no, $boardid, 'prev');
        $next_index_no = $this->gvcomplex_lib->get_prev_or_next_post($index_no, $boardid, 'next');





        if (!$is_member) {
            $member['grade'] = 99;
        }

        if ($board['read_priv'] < 99) {
            if ($member['grade'] > $board['read_priv']) {
                alert('권한이 없습니다.');
            }
        }

        $bo_subject = $this->funcomplex_lib->get_text($write['subject']);
        $bo_wdate   = date("Y-m-d", $write['wdate']);

        if ($write['issecret'] == 'Y') {
            if ($write['writer'] != 0) {
                if (!$is_member) {
                    goto_url(base_url() . "m/bbs/login?url=$urlencode");
                }
            }

            if ($is_member) {
                if (!$this->gvcomplex_lib->is_admin()) {
                    if ($mb_no != $write['writer']) {
                        alert("비밀글은 열람하실 수 없습니다.");
                    }
                }
            } else {
                $mb_no = 0;
                if ($_GET['inpasswd'] != $write['passwd']) {
                    goto_url(base_url() . "m/bbs/board_secret?index_no=$index_no&boardid=$boardid&page=$page");
                }
            }
        }
        $read_count = $write['readcount'] + 1;
        $sql = "update shop_board_{$boardid} set readcount={$read_count} where index_no=?";
        $param = array($index_no);
        $this->query_model->returnNull($sql, $param);

        $bo_img_url = base_url() . 'bbs/' . $board['skin']; #basic,gallery,webzine
        $qstr1 = "boardid=$boardid&page=$page";
        $qstr2 = "index_no=$index_no&boardid=$boardid&page=$page";

        $bo_file1_tag = "";
        if (isset($bo_file1) && $bo_file1 != "") {
            $bo_file1_tag = "<tr><td>첨부파일1 : <a href=\"" . base_url() . "process/download?file={$bo_file1}&url=./data/board/{$boardid}/{$bo_file1}\"><b>{$bo_file1}</b></a></td></tr>";
        }
        $bo_file2_tag = "";
        if (isset($bo_file2) && $bo_file2 != "") {
            $bo_file2_tag = "<tr><td>첨부파일2 : <a href=\"" . base_url() . "process/download?file={$bo_file2}&url=./data/board/{$boardid}/{$bo_file2}\"><b>{$bo_file2}</b></a></td></tr>";
        }

        // 픽셀 (게시판에서 출력되는 이미지의 폭 크기)
        if ($board['width'] > 100) {
            $thumbnail_width = $board['width'];
        } else {
            $thumbnail_width = 730;
        }
        $width = 0;
        $height = 0;
        $bo_file1_img = "";
        if ($bo_file1 && preg_match("/\.(gif|jpg|jpeg|png)$/i", $bo_file1)) {
            $file1anal = explode(".", $bo_file1);
            if (in_array($file1anal[1], $accept)) {
                if (is_file("./data/board/" . $boardid . "/" . $bo_file1)) {
                    $imgsize1 = getimagesize("./data/board/" . $boardid . "/" . $bo_file1);
                    if ($imgsize1[0] > $thumbnail_width) {
                        $width = $thumbnail_width;
                        $height = ($imgsize1[1] / $imgsize1[0]) * $thumbnail_width;
                    } else {
                        $width = $imgsize1[0];
                        $height = $imgsize1[1];
                    }
                }
                if ($width > 320) {
                    $width = 320;
                    $height = "auto";
                }
            }
            $bo_file1_img = "<a href=\"javascript:imgview('./data/board/{$boardid}/{$bo_file1}');\"><img src=\"" . base_url() . "data/board/{$boardid}/{$bo_file1}\" width=\"{$width}\" height=\"{$height}\"></a><br><br>";
        }


        $bo_file2_img = "";

        if ($bo_file2 && preg_match("/\.(gif|jpg|jpeg|png)$/i", $bo_file2)) {
            $file2anal = explode(".", $bo_file2);
            if (in_array($file2anal[1], $accept)) {
                if (is_file("./data/board/" . $boardid . "/" . $bo_file2)) {
                    $imgsize1 = getimagesize("./data/board/" . $boardid . "/" . $bo_file2);
                    if ($imgsize1[0] > $thumbnail_width) {
                        $width = $thumbnail_width;
                        $height = ($imgsize1[1] / $imgsize1[0]) * $thumbnail_width;
                    } else {
                        $width = $imgsize1[0];
                        $height = $imgsize1[1];
                    }
                    if ($width > 320) {
                        $width = 320;
                        $height = "auto";
                    }
                }
            }
            $bo_file2_img = "<a href=\"javascript:imgview('./data/board/{$boardid}/{$bo_file2}');\"><img src=\"" . base_url() . "data/board/{$boardid}/{$bo_file2}\" width=\"{$width}\" height=\"{$height}\"></a>";
        }

        $getViewThum = $this->thumbnail_lib->get_view_thumbnail($this->funcomplex_lib->conv_content($bo_memo, 1), $thumbnail_width);


        // $memo = $this->funcomplex_lib->get_image_resize(strip_tags($write['memo'], '<img><p>'));
        $modifyTag = "";
        if (($member['index_no'] == $bo_writer) || $this->gvcomplex_lib->is_admin()) {
            $modifyTag = "<a href=\"" . base_url() . "m/bbs/board_write?{$qstr2}&w=u\" class=\"btn_lsmall\">수정</a>";
        }
        $replyTag = "";
        if ($member['index_no'] && $member['grade'] <= $board['reply_priv'] && $board['usereply'] == 'Y') {
            $replyTag = "<a href=\"" . base_url() . "m/bbs/reply?{$qstr2}\" class=\"btn_lsmall\">답글</a>";
        }

        $delTag = "";
        if (($member['index_no'] == $bo_writer) || $this->gvcomplex_lib->is_admin()) {
            $delTag = "<a href=\"" . base_url() . "m/bbs/board_delete?{$qstr2}\" class=\"btn_lsmall\">삭제</a>";
        }
        $writeTag = "";
        if ($member['grade'] <= $board['write_priv']) {
            $writeTag = "<a href=\"" . base_url() . "m/bbs/board_write?boardid={$boardid}\" class=\"btn_lsmall btn_color\">글쓰기</a>";
        }

        $good_count = '';
        $good_check = '';
        if ($boardid == "37") {

            $good_count = $this->gvcomplex_lib->get_good_count($index_no, $boardid);
            $good_check = $this->gvcomplex_lib->get_is_good_check($index_no, $boardid, $member['id']);
        }

        $comment = "";
        if ($board['usetail'] == 'Y') {
            $comment = $this->read_comment($index_no, $from_action_url, $boardid, $sfl, $stx, $page, $bo_writer_s, $qstr2, $qstr3);
        }

        $fboardformJs = "";
        if (!$is_member) {
            $fboardformJs = "
                if(!f.writer_s.value) {
                    alert('작성자명을 입력하세요.');
                    f.writer_s.focus();
                    return false;
                }
                if(!f.passwd.value) {
                    alert('비밀번호를 입력하세요.');
                    f.passwd.focus();
                    return false;
                }";
        }


        $data = array(
            'bo_subject' => $bo_subject,
            'write' => $write,
            'bo_wdate' => $bo_wdate,
            'bo_file1_img' => $bo_file1_img,
            'bo_file2_img' => $bo_file2_img,
            'bo_file1_tag' => $bo_file1_tag,
            'bo_file2_tag' => $bo_file2_tag,
            'getViewThum' => $getViewThum,
            'modifyTag' => $modifyTag,
            'replyTag' => $replyTag,
            'delTag' => $delTag,
            'writeTag' => $writeTag,
            'qstr1' => $qstr1,
            'good_count' => $good_count,
            'read_count' => $read_count,
            'boardid' => $boardid,
            'index_no' => $index_no,
            'good_check' => $good_check,
            'comment' => $comment,
            'fboardformJs' => $fboardformJs,
            'prev_index_no' => $prev_index_no,
            'next_index_no' => $next_index_no,

        );
        $this->load->view('mb/bbs/board_read', $data);
    }

    #코맨트 공통영역
    private function read_comment($index_no, $from_action_url, $boardid, $sfl, $stx, $page, $bo_writer_s, $qstr2, $qstr3)
    {
        $is_member = $this->load->get_var('is_member');
        $member    = $this->load->get_var('member');
        $board     = $this->load->get_var('board');
        $returnTag = "";

        // --- (1) 댓글 목록을 가져와서 <div class="comment-list"> 형태로 렌더링 ---
        $sql    = "SELECT * 
               FROM shop_board_{$boardid}_tail 
               WHERE board_index = ? 
               ORDER BY wdate ASC";
        $param  = array($index_no);
        $resObj = $this->query_model->returnQueryObj($sql, $param);

        // 댓글 개수 카운트 (예: "댓글 6"처럼 상단에 표시해줄 경우)
        $comment_count = $resObj->num_rows();

        if ($comment_count) {
            // 댓글 개수 출력
            $returnTag .= "<div class=\"comment-section\">\n";
            $returnTag .= "  <div class=\"comment-header\">댓글 {$comment_count}</div>\n";
            $returnTag .= "  <div class=\"comment-list\">\n";

            $res = $resObj->result_array();
            foreach ($res as $row) {
                $bo_wdate    = date("Y.m.d H:i:s", $row['wdate']);
                $writer_name = htmlspecialchars($row['writer_s'], ENT_QUOTES, 'UTF-8');
                $memo_html   = $this->funcomplex_lib->conv_content($row['memo'], 0);

                $returnTag .= "    <div class=\"comment-item\">\n";
                // 1) 작성자 + 날짜 영역
                $returnTag .= "      <div class=\"comment-meta\">\n";
                $returnTag .= "        <span class=\"comment-author\"><b>{$writer_name}</b></span>\n";
                $returnTag .= "        <span class=\"comment-date\">{$bo_wdate}</span>\n";

                // 2) 삭제 버튼: 작성자와 관리자만 보이도록
                $member_name = isset($member['name']) ? $member['name'] : '';
                if ($row['writer_s'] == $member_name || $this->gvcomplex_lib->is_admin()) {
                    $input_writer = htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8');

                    $returnTag .= "<form name=\"fboardform\" id=\"fboardform\" method=\"post\" action=\"{$from_action_url}\" onsubmit=\"return fboardform_submit(this);\">\n";
                    $returnTag .= "  <input type=\"hidden\" name=\"mode\"      value=\"d\">\n";
                    $returnTag .= "  <input type=\"hidden\" name=\"index_no\"  value=\"{$index_no}\">\n";
                    $returnTag .= "  <input type=\"hidden\" name=\"boardid\"   value=\"{$boardid}\">\n";
                    $returnTag .= "  <input type=\"hidden\" name=\"sfl\"       value=\"{$sfl}\">\n";
                    $returnTag .= "  <input type=\"hidden\" name=\"stx\"       value=\"{$stx}\">\n";
                    $returnTag .= "  <input type=\"hidden\" name=\"page\"      value=\"{$page}\">\n";
                    $returnTag .= "  <input type=\"hidden\" name=\"writer_s\"  value=\"{$input_writer}\">\n";
                    $returnTag .= "  <input type=\"hidden\" name=\"tail_index_no\"  value=\"{$row['index_no']}\">\n";
                    // 댓글 입력 영역 (textarea + 버튼)
                    // board['tail_priv']가 허용 등급인지 확인
                    if ($member['grade'] > $board['tail_priv']) {
                        $returnTag .= "    <button type=\"button\" class=\"btn btn-disabled comment-delete\" onclick=\"alert('삭제을 작성할 권한이 없습니다.');\">댓글등록</button>\n";
                    } else {
                        $returnTag .= "    <button type=\"submit\" class=\"btn btn-primary comment-delete\">삭제</button>\n";
                    }

                    $returnTag .= "</form>\n";
                }
                $returnTag .= "      </div>\n";

                // 3) 댓글 본문
                $returnTag .= "      <div class=\"comment-body\">{$memo_html}</div>\n";
                $returnTag .= "    </div>\n";
            }

            $returnTag .= "  </div> <!-- .comment-list 끝 -->\n";
            $returnTag .= "</div>   <!-- .comment-section 끝 -->\n";
        }

        // --- (2) 로그인한 사용자 여부에 따라 입력 폼 영역 출력 ---
        if ($is_member) {
            // 로그인한 사용자: 이름은 $member['name'], 권한 체크
            $input_writer = htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8');

            $returnTag .= "<form name=\"fboardform\" id=\"fboardform\" method=\"post\" action=\"{$from_action_url}\" onsubmit=\"return fboardform_submit(this);\">\n";
            $returnTag .= "  <input type=\"hidden\" name=\"mode\"      value=\"w\">\n";
            $returnTag .= "  <input type=\"hidden\" name=\"index_no\"  value=\"{$index_no}\">\n";
            $returnTag .= "  <input type=\"hidden\" name=\"boardid\"   value=\"{$boardid}\">\n";
            $returnTag .= "  <input type=\"hidden\" name=\"sfl\"       value=\"{$sfl}\">\n";
            $returnTag .= "  <input type=\"hidden\" name=\"stx\"       value=\"{$stx}\">\n";
            $returnTag .= "  <input type=\"hidden\" name=\"page\"      value=\"{$page}\">\n";
            $returnTag .= "  <input type=\"hidden\" name=\"writer_s\"  value=\"{$input_writer}\">\n";

            // 댓글 입력 영역 (textarea + 버튼)
            // board['tail_priv']가 허용 등급인지 확인
            if ($member['grade'] > $board['tail_priv']) {
                // 권한 부족 시 버튼 비활성화
                $returnTag .= "  <div class=\"comment-input-area disabled\">\n";
                $returnTag .= "    <textarea name=\"memo\" class=\"comment-input-text\" placeholder=\"댓글을 입력해주세요.\"></textarea>\n";
                $returnTag .= "    <button type=\"button\" class=\"btn btn-disabled\" onclick=\"alert('댓글을 작성할 권한이 없습니다.');\">댓글등록</button>\n";
                $returnTag .= "  </div>\n";
            } else {
                // 정상 입력 가능
                $returnTag .= "  <div class=\"comment-input-area\">\n";
                $returnTag .= "    <textarea name=\"memo\" class=\"comment-input-text\" placeholder=\"댓글 내용을 입력해주세요. 500자 이하로 작성해주세요.\"></textarea>\n";
                $returnTag .= "    <button type=\"submit\" class=\"btn btn-primary\">댓글등록</button>\n";
                $returnTag .= "  </div>\n";
            }

            $returnTag .= "</form>\n";
        } else {
            // 비로그인 사용자: tail_priv 옵션에 따라 결정
            if ($board['tail_priv'] == '99') {
                // 익명+비밀번호 입력 양식
                $returnTag .= "<form name=\"fboardform\" id=\"fboardform\" method=\"post\" action=\"{$from_action_url}\" onsubmit=\"return fboardform_submit(this);\">\n";
                $returnTag .= "  <input type=\"hidden\" name=\"mode\"     value=\"w\">\n";
                $returnTag .= "  <input type=\"hidden\" name=\"index_no\" value=\"{$index_no}\">\n";
                $returnTag .= "  <input type=\"hidden\" name=\"boardid\"  value=\"{$boardid}\">\n";
                $returnTag .= "  <input type=\"hidden\" name=\"sfl\"      value=\"{$sfl}\">\n";
                $returnTag .= "  <input type=\"hidden\" name=\"stx\"      value=\"{$stx}\">\n";
                $returnTag .= "  <input type=\"hidden\" name=\"page\"     value=\"{$page}\">\n";

                $returnTag .= "  <div class=\"comment-input-area anonymous\">\n";
                $returnTag .= "    <input type=\"text\" name=\"writer_s\" class=\"comment-input-name\" placeholder=\"작성자\">\n";
                $returnTag .= "    <input type=\"password\" name=\"passwd\" class=\"comment-input-pw\" placeholder=\"비밀번호\">\n";
                $returnTag .= "    <textarea name=\"memo\" class=\"comment-input-text\" placeholder=\"댓글을 입력해주세요. 500자 이하로 작성해주세요.\"></textarea>\n";
                $returnTag .= "    <button type=\"submit\" class=\"btn btn-primary\">댓글등록</button>\n";
                $returnTag .= "  </div>\n";
                $returnTag .= "</form>\n";
            } else {
                // 비로그인 + 로그인 필요
                $returnTag .= "<div class=\"comment-input-area disabled\">\n";
                $returnTag .= "  <textarea class=\"comment-input-text\" placeholder=\"로그인 후 댓글을 작성할 수 있습니다.\" disabled></textarea>\n";
                $returnTag .= "  <button type=\"button\" class=\"btn btn-disabled\" onclick=\"alert('로그인후 댓글을 작성 가능합니다.');\">댓글등록</button>\n";
                $returnTag .= "</div>\n";
            }
        }

        return $returnTag;
    }

    public function board_delete()
    {
        $this->template->mb_header();
        $this->board_deleteSkin();
        $this->template->mb_footer();
    }
    private function board_deleteSkin()
    {
        $index_no = $this->input->get('index_no');
        $boardid = $this->input->get('boardid');
        $page = $this->input->get('page');

        $is_member = $this->load->get_var('is_member');


        $deleteTag = "";
        if ($is_member) {
            $deleteTag = "
            <p class=\"btn_area\">
                <input type='submit' value='삭제하기' class=\"btn_medium\">
                <a href=\"javascript:history.go(-1);\" class=\"btn_medium bx-white\">뒤로가기</a>
            </p>";
        } else {
            $deleteTag = "
            <div class=\"formbox\" style=\"margin:10px 0 0 0;\">
                <input type=\"password\" name=\"passwd\" placeholder='비밀번호'>
            </div>
            <p class=\"btn_area\" style=\"margin-top:8px;\">
                <input type='submit' value='삭제하기' class=\"btn_medium\">
                <a href=\"javascript:history.go(-1);\" class=\"btn_medium bx-white\">뒤로가기</a>
            </p>";
        }
        $pwdJs = "";
        if (!$is_member) {
            $pwdJs = "
            if(!f.passwd.value){
                alert('비밀번호를 입력하세요.');
                f.passwd.focus();
                return false;
            }";
        }
        $data = array('index_no' => $index_no, 'boardid' => $boardid, 'page' => $page, 'deleteTag' => $deleteTag, 'pwdJs' => $pwdJs);
        $this->load->view('mb/bbs/board_delete', $data);
    }
    #비밀글
    public function board_secret()
    {
        $tb['title'] = '비밀번호 확인';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->board_secretSkin();
        $this->template->mb_footer();
    }
    private function board_secretSkin()
    {
        $index_no = $this->input->get('index_no');
        $boardid = $this->input->get('boardid');
        $page = $this->input->get('page');
        $data = array('index_no' => $index_no, 'boardid' => $boardid, 'page' => $page);
        $this->load->view('mb/bbs/board_secret', $data);
    }
    #게시글 작성 페이지
    public function board_write()
    {
        $board = $this->load->get_var('board');
        $tb['title'] = $this->funcomplex_lib->get_text($board['boardname']);
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->board_writeSkin();
        $this->template->mb_footer();
    }
    private function board_writeSkin()
    {
        $board = $this->load->get_var('board');
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $write = $this->load->get_var('write');
        $mb_no = $member['index_no'];
        $w = $this->input->get('w');
        $boardid = $this->input->get('boardid');
        $page = $this->input->get('page');
        $index_no = $this->input->get('index_no');
        $form_action_url = base_url() . 'm/process/board_write_update';

        if (!$is_member) {
            $member['grade'] = 99;
        }
        if ($w == "u" || $w == "r") {
            if (!$write['index_no'])
                alert("자료가 없습니다.");

            if ($w == "u") {
                if ($is_member) {
                    if (!$this->gvcomplex_lib->is_admin()) {
                        if ($write['writer'] != $mb_no) {
                            alert('글수정 권한이 없습니다.');
                        }
                    }
                }
            }

            if ($w == "r") {
                if ($board['reply_priv'] < 99) {
                    if ($member['grade'] > $board['reply_priv']) {
                        alert("댓글작성 권한이 없습니다.");
                    }
                }

                $write['writer_s'] = $member['name'];
            }
        } else {
            if ($board['write_priv'] < 99) {
                if ($member['grade'] > $board['write_priv'])
                    alert("글쓰기 권한이 없습니다.");
            }

            $write['writer_s'] = $member['name'];
        }
        $qstr1 = "boardid=$boardid&page=$page";
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);

        $is_memberTag = "";
        if ($is_member) {
            $is_memberTag .= $write['writer_s'];
            $is_memberTag .= "<input type='hidden' name='writer_s' value='$write[writer_s]'>";
        } else {
            $is_memberTag .= "<input type='text' name='writer_s' value='$write[writer_s]' style='width:150px;'>";
        }

        $pwdTag = "";
        if (!$is_member && $w != 'r') {
            $pwdTag = "
            <tr>
                <th>비밀번호</th>
                <td><input type=\"password\" name=\"passwd\" style=\"width:50vw;\"></td>
            </tr>";
        }
        $useCateTag = "";
        if ($board['use_category'] == '1') {
            $useCateTag .= "
            <tr>
                <th>분류</th>
                <td>
                    <select name=\"ca_name\" style=\"width:100%;\">
                    <option value=\"\">선택하세요</option>" . get_category_option($board['usecate']) . "
                    </select>";
            if ($w == 'u' || $w == 'r') {
                $useCateTag .= "<script>document.fwrite.ca_name.value='{$write['ca_name']}';</script>";
            }
            $useCateTag .= "
                </td>
            </tr>";
        }
        $option = "";
        $option_hidden = "";
        if ($this->gvcomplex_lib->is_admin()) {
            $checked = '';
            if (isset($write['btype']) && $write['btype'] == '1') {
                $checked = ' checked';
            }
            $option .= '<input type="checkbox" name="btype" value="1" id="ids_btype" class="css-checkbox lrg"' . $checked . '><label for="ids_btype" class="css-label padr10">공지사항</label>';

            $checked = '';
            if (isset($write['issecret']) && $write['issecret'] == 'Y') {
                $checked = ' checked';
            }
            $option .= '<input type="checkbox" name="issecret" value="Y" id="ids_issecret" class="css-checkbox lrg"' . $checked . '><label for="ids_issecret" class="css-label padr10">비밀글</label>';
        } else {
            switch ($board['use_secret']) {
                case '0':
                    $option_hidden .= '<input type="hidden" name="issecret" value="N">';
                    break;
                case '1':
                    $checked = '';
                    if ($write['issecret'] == 'Y') {
                        $checked = ' checked';
                    }
                    $option .= '<input type="checkbox" name="issecret" value="Y" id="ids_issecret" class="css-checkbox lrg"' . $checked . '> <label for="ids_issecret" class="css-label padr10">비밀글</label>';
                    break;
                case '2':
                    $option_hidden .= '<input type="hidden" name="issecret" value="Y">';
                    break;
            }
        }
        $optionTag = "";
        if ($option) {
            $optionTag = "
            <tr>
                <th>옵션</th>
                <td>{$option}</td>
            </tr>";
        }

        $btnConfirmTag = "";
        if ($w == 'u' || $w == 'r') {
            $btnConfirmTag = "<a href=\"" . base_url() . "m/bbs/noticeList?{$qstr1}\" class=\"btn_medium\">목록</a>";
        } else {
            $btnConfirmTag = "<a href=\"javascript:history.go(-1);\" class=\"btn_medium\">취소</a>";
        }
        $not_memberJs = "";
        if (!$is_member) {
            $not_memberJs = "
            if(!f.writer_s.value) {
                alert('이름을 입력하세요.');
                f.writer_s.focus();
                return false;
            }";
        }
        $pwdJs = "";
        if (!$is_member && $w != 'r') {
            $pwdJs = "
            if(!f.passwd.value) {
                alert('비밀번호를 입력하세요.');
                f.passwd.focus();
                return false;
            }";
        }
        $cateJs = "";
        if ($board['use_category'] == '1') {
            $cateJs = "
            if(!f.ca_name.value) {
                alert('분류를 선택하세요.');
                f.ca_name.focus();
                return false;
            }";
        }

        $write['subject'] = isset($write['subject']) ? $write['subject'] : "";
        $write['memo'] = isset($write['memo']) ? $write['memo'] : "";
        $write['fileurl1'] = isset($write['fileurl1']) ? $write['fileurl1'] : '';
        $write['fileurl2'] = isset($write['fileurl2']) ? $write['fileurl2'] : '';

        if ($w == "")
            $editTag = $this->global_lib->editor_html('memo', $this->funcomplex_lib->get_text($board['insert_content'], 0));
        else if ($w == "u")
            $editTag = $this->global_lib->editor_html('memo', $this->funcomplex_lib->get_text($write['memo'], 0));
        log_message("debug", "mobile editTag : {$editTag}");
        log_message("debug", "board['insert_content'] : {$board['insert_content']}");
        $usefileTag = "";
        if ($board['usefile'] == 'Y') {
            $usefileTag .= "
            <tr>
                <th scope=\"row\">파일1</th>
                <td><input type=\"file\" name=\"file1\">";
            if ($write['fileurl1']) {
                $usefileTag .= "
                            <div class=\"file_wrap\">
                            <a href=\"" . base_url() . "data/board/{$boardid}/{$write['fileurl1']}\" target=\"_blank\"><span class=\"bold fc_255\">{$write['fileurl1']}</span></a>
                            </div>
                            <div class=\"del_wrap\">
                            <input type=\"checkbox\" name=\"del_file1\" value=\"{$write['fileurl1']}\">
                            <span class=\"delete-label\">삭제</span>
                            </div>";
            }
            $usefileTag .= "</td>
            </tr>
            <tr>
                <th scope=\"row\">파일2</th>
                <td>
                    <input type=\"file\" name=\"file2\">";
            if ($write['fileurl2']) {
                $usefileTag .= "
                            <div class=\"file_wrap\">
                            <a href=\"" . base_url() . "data/board/{$boardid}/{$write['fileurl2']}\" target=\"_blank\"><span class=\"bold fc_255\">{$write['fileurl2']}</span></a>
                            </div>
                            <div class=\"del_wrap\">
                            <input type=\"checkbox\" name=\"del_file2\" value=\"{$write['fileurl2']}\"> 
                            <span class=\"delete-label\">삭제</span>
                            </div>";
            }
            $usefileTag .= "
                </td>
            </tr>";
        }
        $memoEditJs = $this->global_lib->get_editor_js('memo');
        $memoChkJs = $this->global_lib->chk_editor_js('memo');
        $data = array(
            'token' => $token,
            'form_action_url' => $form_action_url,
            'boardid' => $boardid,
            'index_no' => $index_no,
            'page' => $page,
            'w' => $w,
            'is_memberTag' => $is_memberTag,
            'pwdTag' => $pwdTag,
            'useCateTag' => $useCateTag,
            'option_hidden' => $option_hidden,
            'optionTag' => $optionTag,
            'write' => $write,
            'btnConfirmTag' => $btnConfirmTag,
            'not_memberJs' => $not_memberJs,
            'pwdJs' => $pwdJs,
            'cateJs' => $cateJs,
            'editTag' => $editTag,
            'usefileTag' => $usefileTag,
            'memoEditJs' => $memoEditJs,
            'memoChkJs' => $memoChkJs,
        );
        $this->load->view('mb/bbs/board_write', $data);
    }

    #qna_list 1대1 상담문의
    public function qna_list()
    {
        $tb['title'] = '1:1 상담문의';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->qna_listSkin();
        $this->template->mb_footer();
    }
    private function qna_listSkin()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        $page = $this->input->get('page');
        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');
        $sql_common = " from shop_qa ";
        $sql_search = " where mb_id = '$member[id]' ";
        $sql_order  = " order by wdate desc ";

        $sql = "select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];

        $rows = 15;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if ($page == "") {
            $page = 1;
        } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함

        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $i = 0;
        $qnaTableTag = "";
        foreach ($result as $key => $row) {
            $bo_date = $row['mb_id'] . "<span class='padl10'>" . substr($row['wdate'], 0, 10);
            $qnaTableTag .= "
            <li class=\"list\">
                <a href=\"" . base_url() . "m/bbs/qna_read?index_no={$row['index_no']}\">
                <p class=\"subj\"><b class=\"cate\">[ {$row['catename']} ]</b> " . cut_str($row['subject'], 60) . "</p>
                <p class=\"date\">{$bo_date}</p>
                </a>
            </li>";
            $i++;
        }
        if ($i == 0) {
            $qnaTableTag = '<li class="empty_list">자료가 없습니다.</li>';
        }
        $pagination = get_paging($config['mobile_pages'], $page, $total_page, $requestUrl . '?page=');
        $data = array('total_count' => $total_count, 'pagination' => $pagination, 'qnaTableTag' => $qnaTableTag);
        $this->load->view('mb/bbs/qna_list', $data);
    }
    #1대1 상담 읽기
    public function qna_read()
    {
        $is_member = $this->load->get_var('is_member');
        $member = $this->load->get_var('member');
        $mode = $this->input->get('mode');
        $tb['title'] = '1:1 상담문의';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->qna_readSkin();
        $this->template->mb_footer();
    }
    private function qna_readSkin()
    {
        $index_no = $this->input->get('index_no');
        $sql = "select * from shop_qa where index_no=?";
        $param = array($index_no);
        $qa = $this->query_model->returnOneArr($sql, $param);
        $resultYes = "";
        if ($qa['result_yes']) {
            $resultYes = "
            <div class=\"qna_reply\">
                <p class=\"date\"><span class=\"ic_tit\">답변</span> " . substr($qa['result_date'], 0, 10) . "</p>
                <p>" . nl2br(htmlspecialchars($qa['reply'])) . "</p>
            </div>";
        }
        $data = array('qa' => $qa, 'index_no' => $index_no, 'resultYes' => $resultYes, 'index_no' => $index_no);
        $this->load->view('mb/bbs/qna_read', $data);
    }
    #1대1 상담하기
    public function qna_write()
    {
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if (!$is_member) {
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);
        }
        $tb['title'] = '1:1 상담문의';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->qnaWriteSkin();
        $this->template->mb_footer();
    }
    private function qnaWriteSkin()
    {
        $member = $this->load->get_var('member');
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        $form_action_url = base_url() . 'm/process/qna_write';
        $sql = "select * from shop_qa_cate where isuse='Y'";
        $res = $this->query_model->returnArr($sql);
        $selectOpt = "";
        foreach ($res as $key => $row) {
            $selectOpt .= "<option value='$row[catename]'>$row[catename]</option>";
        }
        $data = array('token' => $token, 'form_action_url' => $form_action_url, 'selectOpt' => $selectOpt, 'member' => $member);
        $this->load->view('mb/bbs/qna_write', $data);
    }
    #qna_modify 상담수정하기
    public function qna_modify()
    {
        $is_member = $this->load->get_var('is_member');
        if (!$is_member) {
            alert("로그인 후 이용 가능합니다.");
        }
        $tb['title'] = '1:1 상담문의';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->qnaModifySkin();
        $this->template->mb_footer();
    }
    private function qnaModifySkin()
    {
        $member = $this->load->get_var('member');
        $index_no = $this->input->get('index_no');
        $sql = "select * from shop_qa where index_no=?";
        $param = array($index_no);
        $qa = $this->query_model->returnOneArr($sql, $param);
        if ($qa['mb_id'] != $member['id']) {
            if (!$this->gvcomplex_lib->is_admin()) {
                alert('게시글을 수정할 권한이 없습니다.');
            }
        }
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        $form_action_url = base_url() . 'm/process/qna_modify';
        $sql = "select * from shop_qa_cate where isuse='Y'";
        $res = $this->query_model->returnArr($sql);
        $selectOpt = "";
        foreach ($res as $key => $row) {
            $selectOpt .= "<option " . get_selected($qa['catename'], $row['catename']) . " value='$row[catename]'>$row[catename]</option>\n";
        }
        $emailSendYes = get_checked($qa['email_send_yes'], '1');
        $smsSendYes = get_checked($qa['sms_send_yes'], '1');
        $data = array(
            'form_action_url' => $form_action_url,
            'token' => $token,
            'index_no' => $index_no,
            'selectOpt' => $selectOpt,
            'qa' => $qa,
            'emailSendYes' => $emailSendYes,
            'smsSendYes' => $smsSendYes
        );
        $this->load->view('mb/bbs/qna_modify', $data);
    }
    #faq 자주하는 질문
    public function faq()
    {
        $tb['title'] = '자주묻는 질문';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->faqSkin();
        $this->template->mb_footer();
    }
    private function faqSkin()
    {
        $faqcate = $this->input->get('faqcate');
        $sql = "select * from shop_faq_cate order by index_no asc";
        $res = $this->query_model->returnArr($sql);
        $selectOpt = "";
        foreach ($res as $key => $row) {
            $selected = "";
            if ($row['index_no'] == $faqcate) {
                $selected = ' selected';
            }
            $selectOpt .= "<option value=\"" . base_url() . "m/bbs/faq?faqcate={$row['index_no']}\" {$selected}> {$row['catename']}</option>";
        }

        $sql = "select * from shop_faq ";
        if ($faqcate) $sql .= " where cate='$faqcate'";
        $sql .= " order by index_no asc ";
        $rst = $this->query_model->returnArr($sql);
        $i = 0;
        $faqList = "";
        foreach ($rst as $key => $row) {
            $faqList .= "
            <li class=\"faq_q\" onclick=\"js_faq('{$i}');\">
				{$row['subject']}
			</li>
			<li id=\"sod_faq_con_{$i}\" class=\"faq_a\">" . nl2br($row['memo']) . "</li>";
            $i++;
        }
        $emptyTag = "";
        if ($i == 0) {
            $emptyTag = '<div class="empty_list">자료가 없습니다.</div>';
        }


        $data = array('selectOpt' => $selectOpt, 'faqList' => $faqList, 'emptyTag' => $emptyTag);
        $this->load->view('mb/bbs/faq', $data);
    }
    #partner_reg 분양신청
    public function partner_reg()
    {
        $config = $this->load->get_var('config');
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if (!$config['partner_reg_yes']) {
            alert('서비스가 일시 중단 되었습니다.', base_url() . 'm/main');
        }
        if (!$is_member) {
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);
        }
        $tb['title'] = '쇼핑몰 분양신청';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->partnerSkin();
        $this->template->mb_footer();
    }
    private function partnerSkin()
    {
        $partner = $this->load->get_var('partner');
        $member = $this->load->get_var('member');
        if ($partner['mb_id']) {
            $recept_priceTag = "";
            if ($partner['receipt_price'] > 0)
                $recept_priceTag = display_price($partner['receipt_price']);
            else
                $recept_priceTag = '무료';
            $part_bankInfo = "";
            if ($partner['pay_settle_case'] == '1') {
                $part_bankInfo = "
                <tr>
                    <th scope=\"row\">입금계좌</th>
                    <td>{$partner['bank_acc']}</td>
                </tr>
                <tr>
                    <th scope=\"row\">입금자명</th>
                    <td>{$partner['deposit_name']}</td>
                </tr>";
            }
            $partMemo = "";
            if ($partner['memo']) {
                $partMemo = "
                <tr>
                    <th scope=\"row\">전달사항</th>
                    <td>" . $this->funcomplex_lib->conv_content($partner['memo'], 0) . "</td>
                </tr>";
            }
            $data = array('partner' => $partner, 'recept_priceTag' => $recept_priceTag, 'part_bankInfo' => $part_bankInfo, 'partMemo' => $partMemo);
            $this->load->view('mb/bbs/partner_reg_result', $data);
        } else {
            $config = $this->load->get_var('config');
            $token = md5(uniqid(rand(), true));
            set_session("ss_token", $token);
            $from_action_url = base_url() . 'm/process/partner_reg_update';
            $pf_regulationTag = "";
            if ($config['pf_regulation_subj']) {
                $pf_regulationTag = "
                <h2 class=\"mart10\">{$config['pf_regulation_subj']}</h2>
                <textarea readonly>{$config['pf_regulation']}</textarea>
                <fieldset class=\"fpartnerform_agree\">
                    <input type=\"checkbox\" name=\"agree2\" value=\"1\" id=\"agree21\" class=\"css-checkbox lrg\">
                    <label for=\"agree21\" class=\"css-label\">위 내용을 읽었으며 규정안내에 동의합니다.</label>
                </fieldset>";
            }
            $fileTag = "";
            $file = './data/common/' . $config['admin_seal'];
            if (is_file($file) && $config['admin_seal']) {
                $file = base_url() . 'data/common/' . $config['admin_seal'];
                $seal = rpc($file);
                $fileTag = '<span class="admin_seal"><img src="' . $seal . '"></span>';
            }
            $memberName = $this->funcomplex_lib->get_text($member['name']);
            $memberCell = replace_tel($member['cellphone']);
            $memberEmail = $this->funcomplex_lib->get_text($member['email']);
            $multi_settle = 0;
            $sql = " select * from shop_member_grade where gb_no between 2 and 6 and gb_name <> '' order by gb_no desc ";
            $res = $this->query_model->returnArr($sql);
            $i = 0;
            $configOptTag = "";
            foreach ($res as $key => $row) {
                $checked = '';
                if ($i == 0) $checked = ' checked="checked"';

                $reg = $row['gb_no'] . '^' . $row['gb_anew_price'];
                $configOptTag .= '<label><input type="radio" name="reg_level" value="' . $reg . '"' . $checked . '>' . $this->funcomplex_lib->get_text($row['gb_name']) . '</label>';

                $multi_settle++;
                $i++;
            }
            $configOptNoneTag = "";
            if (!$multi_settle) {
                $configOptNoneTag = '설정값이 없습니다. 운영자에게 알려주시면 감사하겠습니다.';
            }
            $multi_settleTag = array('', '');
            if ($multi_settle) {
                $multi_settleTag[0] .= "
                    <tr>
                        <th scope=\"row\">결제방법</th>
                        <td>
                            <input type=\"hidden\" name=\"anew_grade\" value=\"\">
                            <input type=\"hidden\" name=\"receipt_price\" value=\"0\">
                            <input type=\"radio\" name=\"pay_settle_case\" value=\"1\" id=\"pay_settle_case\" checked=\"checked\">
                            <label for=\"pay_settle_case\">무통장입금</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope=\"row\">결제금액</th>
                        <td><span id=\"reg_tot_price\"></span></td>
                    </tr>
                    <tr>
                        <th scope=\"row\"><label for=\"bank_acc\">입금계좌</label></th>
                        <td>" . $this->funcomplex_lib->mobile_bank_account("bank_acc") . "
                            <div class=\"padt10\">
                                <input type=\"text\" name=\"reg_hp\" value=\"" . replace_tel($member['cellphone']) . "\" id=\"reg_hp\" class=\"frm_input\" size=\"20\">
                            </div>
                            <div class=\"padt5\">
                                <button type=\"button\" class=\"btn_small btn_sms_send\">위 입금계좌 문자받기</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope=\"row\"><label for=\"deposit_name\">입금자명</label></th>
                        <td><input type=\"text\" name=\"deposit_name\" id=\"deposit_name\" class=\"frm_input\" size=\"20\" placeholder=\"실제 입금자명\"></td>
                    </tr>
                    <tr>
                        <th scope=\"row\"><label for=\"reg_memo\">전하실말씀</label></th>
                        <td><textarea name=\"memo\" id=\"reg_memo\" rows=\"10\" class=\"frm_textbox h60\"></textarea></td>
                    </tr>";
                $multi_settleTag[1] = "
                    <div class=\"btn_confirm\">
                        <input type=\"submit\" value=\"신청하기\" id=\"btn_submit\" class=\"btn_medium wset\" accesskey=\"s\">
                        <a href=\"" . base_url() . "m/main\" class=\"btn_medium bx-white\">취소</a>
                    </div>";
            }
            $multi_settleJs = "";
            if ($multi_settle) {
                $multi_settleJs = "calculate_total_price();";
            }
            $data = array(
                'token' => $token,
                'from_action_url' => $from_action_url,
                'config' => $config,
                'pf_regulationTag' => $pf_regulationTag,
                'fileTag' => $fileTag,
                'memberName' => $memberName,
                'memberCell' => $memberCell,
                'memberEmail' => $memberEmail,
                'configOptTag' => $configOptTag,
                'configOptNoneTag' => $configOptNoneTag,
                'multi_settleTag' => $multi_settleTag,
                'multi_settleJs' => $multi_settleJs
            );
            $this->load->view('mb/bbs/partner_reg', $data);
        }
    }
    #seller_reg 입양신청
    public function seller_reg()
    {
        $config = $this->load->get_var('config');
        if (!$config['seller_reg_yes']) {
            alert('서비스가 일시 중단 되었습니다.', base_url() . 'm/main');
        }
        $tb['title'] = '온라인 입점안내';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->seller_regSkin();
        $this->template->mb_footer();
    }
    private function seller_regSkin()
    {
        $config = $this->load->get_var('config');
        $imageTag = $this->funcomplex_lib->get_image_resize($config['seller_reg_mobile_guide']);
        $data = array('config' => $config, 'imageTag' => $imageTag);
        $this->load->view('mb/bbs/seller_reg', $data);
    }
    #seller_reg_from 입양신청 폼    
    public function seller_reg_form()
    {
        $config = $this->load->get_var('config');
        $member = $this->load->get_var('member');
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if (!$config['seller_reg_yes']) {
            alert('서비스가 일시 중단 되었습니다.', base_url() . 'm/main');
        }

        if (!$is_member) {
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);
        }

        if ($this->gvcomplex_lib->is_admin()) {
            alert('관리자는 신청을 하실 수 없습니다.', base_url() . 'm/main');
        }

        if ($this->gvcomplex_lib->is_seller($member['id'])) {
            alert('이미 승인 완료된 입점사입니다.', base_url() . 'm/main');
        }
        $tb['title'] = '온라인 입점신청';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->seller_reg_formSkin();
        $this->template->mb_footer();
    }
    private function seller_reg_formSkin()
    {
        $seller = $this->load->get_var('seller');
        $config = $this->load->get_var('config');
        // add_javascript('js 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
        $OM_POSTCODE_JS = "";
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {   //https 통신일때 daum 주소 js
            $OM_POSTCODE_JS = '<script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>';
        } else {  //http 통신일때 daum 주소 js
            $OM_POSTCODE_JS = '<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>';
        }
        if ($seller['mb_id'] && !$seller['state']) {
            $printAdd = $this->funcomplex_lib->print_address($seller['company_addr1'], $seller['company_addr2'], $seller['company_addr3'], $seller['company_addr_jibeon']);
            $sellerHomeage = "";
            if ($seller['company_hompage']) {
                $sellerHomeage = "
                <tr>
                    <th scope=\"row\">홈페이지</th>
                    <td>{$seller['company_hompage']}</td>
                </tr>";
            }
            $sellerMemo = "";
            if ($seller['memo']) {
                $sellerMemo = "
                <tr>
                    <th scope=\"row\">전달사항</th>
                    <td>" . $this->funcomplex_lib->conv_content($seller['memo'], 0) . "</td>
                </tr>";
            }
            $data = array('OM_POSTCODE_JS' => $OM_POSTCODE_JS, 'seller' => $seller, 'printAdd' => $printAdd, 'sellerHomeage' => $sellerHomeage, 'sellerMemo' => $sellerMemo);
            $this->load->view('mb/bbs/seller_reg_result', $data);
        } else {
            $token = md5(uniqid(rand(), true));
            set_session("ss_token", $token);

            $config['seller_reg_agree'] = preg_replace("/\\\/", "", $config['seller_reg_agree']);
            $from_action_url = base_url() . 'm/process/seller_reg_form_update';
            $data = array('OM_POSTCODE_JS' => $OM_POSTCODE_JS, 'from_action_url' => $from_action_url, 'config' => $config, 'token' => $token);
            $this->load->view('mb/bbs/seller_reg_form', $data);
        }
    }

    #회원탈퇴 화면
    public function leave_form()
    {
        $is_member = $this->load->get_var('is_member');
        $urlencode = $this->load->get_var('urlencode');
        if (!$is_member) {
            goto_url(base_url() . 'm/bbs/login?url=' . $urlencode);
        }
        $tb['title'] = "회원탈퇴";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->leave_formSkin();
        $this->template->mb_footer();
    }
    private function leave_formSkin()
    {
        $member = $this->load->get_var('member');
        $form_action_url = base_url() . 'm/process/leave_form_update';
        $memEmail = ($member['email'] ? $member['email'] : '미등록');
        $memCell = ($member['cellphone'] ? $member['cellphone'] : '미등록');
        $data = array('form_action_url' => $form_action_url, 'member' => $member, 'memEmail' => $memEmail, 'memCell' => $memCell);
        $this->load->view('mb/bbs/leave_form', $data);
    }
    #회원정보 수정
    public function member_confirm()
    {
        $is_member = $this->load->get_var('is_member');
        if (!$is_member)
            alert('로그인 한 회원만 접근하실 수 있습니다.', base_url() . 'm/bbs/login');

        $tb['title'] = '회원 비밀번호 확인';
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header();
        $this->memberConfirmSkin();
        $this->template->mb_footer();
    }
    private function memberConfirmSkin()
    {
        $member = $this->load->get_var('member');
        $url = base_url() . 'm/bbs/' . $this->input->get('url');

        $member = $this->load->get_var('member');
        $type = $member['supply'] == "Y" ? "seller" : 'user';
        $url = $url . "?type={$type}";

        // url 체크
        $this->funcomplex_lib->check_url_host($url);
        $url = $this->funcomplex_lib->get_text($url);
        $data = array('url' => $url, 'member' => $member);
        $this->load->view('mb/bbs/member_confirm', $data);
    }
    #provision 이용약관
    public function provision()
    {
        $tb['title'] = "이용약관";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header_sub();
        $this->provisionSkin($tb);
        $this->template->mb_footer_sub();
    }
    private function provisionSkin($tb)
    {
        $config = $this->load->get_var('config');
        $data = array('tb' => $tb, 'config' => $config);
        $this->load->view('mb/bbs/provision', $data);
    }
    #policy 개인정보처리방침
    public function policy()
    {
        $tb['title'] = "개인정보처리방침";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header_sub();
        $this->policySkin($tb);
        $this->template->mb_footer_sub();
    }
    private function policySkin($tb)
    {
        $config = $this->load->get_var('config');
        $data = array('tb' => $tb, 'config' => $config);
        $this->load->view('mb/bbs/policy', $data);
    }

    // 배송정책 약관동의
    public function delivery_policy()
    {
        $tb['title'] = "배송정책 약관동의";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header_sub();
        $config = $this->load->get_var('config');
        $data = array('tb' => $tb, 'config' => $config);
        $this->load->view('mb/bbs/delivery_policy', $data);
        $this->template->mb_footer_sub();
    }

    // 결제정책 약관동의
    public function charge_policy()
    {
        $tb['title'] = "결제정책 약관동의";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header_sub();
        $config = $this->load->get_var('config');
        $data = array('tb' => $tb, 'config' => $config);
        $this->load->view('mb/bbs/charge_policy', $data);
        $this->template->mb_footer_sub();
    }

    // 정산정책 약관동의
    public function calculate_policy()
    {
        $tb['title'] = "정산정책 약관동의";
        $this->load->vars(array('tb' => $tb));
        $this->template->mb_header_sub();
        $config = $this->load->get_var('config');
        $data = array('tb' => $tb, 'config' => $config);
        $this->load->view('mb/bbs/calculate_policy', $data);
        $this->template->mb_footer_sub();
    }
}
