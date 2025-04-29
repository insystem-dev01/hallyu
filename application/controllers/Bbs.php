<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bbs extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('query_model');
        $this->load->helper('common_helper');
        $this->load->helper('global_helper');
        $this->load->library('template');
        $this->load->library('mypage_template');
        $this->load->library('funcomplex_lib');
        $this->load->library('gvcomplex_lib');
        $this->load->library('thumbnail_lib');
        $this->load->library('global_lib');
        $device=$this->input->get('device');
        if(isset($device)){
            $_SESSION['device']=$device;
        }
    }
    #회사소개
    public function content(){
        if((!isset($_SESSION['device']) || $_SESSION['device']!="pc") && $this->agent->is_mobile()){
            redirect('./m/bbs/contents?co_id='.$co_id);
        }
        $this->template->header();
        $this->contentskin();
        $this->template->footer();
    }
    private function contentskin(){
        $co_id=$this->input->get('co_id');
        $tb=$this->load->get_var('tb');
        $sql="select * from shop_content where co_id = ?";
        $arr=array($co_id);
        $co	= $this->query_model->returnOneArr($sql,$arr);
        if(!$co["co_id"]){
            alert('�ڷᰡ �����ϴ�.', base_url());
        }
        $tb['title'] = isset($co['co_subject'])?$co['co_subject']:$tb['title'];
        $thumb=$this->thumbnail_lib->get_view_thumbnail($this->funcomplex_lib->conv_content($co["co_content"], 1, 0), 1000);
        $data=array('title'=>$tb['title'],'thumb'=>$thumb);
        $this->load->view('pc/bbs/content',$data);
    }
    #이용약관
    public function provision(){
        $this->template->header();
        $this->provisionskin();
        $this->template->footer();
    }
    private function provisionskin(){
        $tb['title'] = '이용약관';
        $config=$this->load->get_var('config');
        $shop_provision=nl2br($config['shop_provision']);
        $data=array('title'=>$tb['title'],'shop_provision'=>$shop_provision);
        $this->load->view('pc/bbs/proviosion',$data);
    }
    #개인정보처리방침
    public function policy(){
        $this->template->header();
        $this->policyskin();
        $this->template->footer();
    }
    private  function policyskin(){
        $tb['title'] = '개인정보처리방침';
        $config=$this->load->get_var('config');
        $shop_policy=nl2br($config['shop_private']);
        $data=array('title'=>$tb['title'],'shop_policy'=>$shop_policy);
        $this->load->view('pc/bbs/policy',$data);
    }

    private function register_process($process = 1) {
        $step1 = "";
        $step2 = "";
        $step3 = "";
        $step4 = "";

        if($process == 1) {
            $step1 = "step_on";
        }else if($process == 2) {
            $step2 = "step_on";
        }else if($process == 3) {
            $step3 = "step_on";
        }else if($process == 4) {
            $step4 = "step_on";
        }

        $memberType="
            <div class=\"register_process\">
                <div class=\"process_in\">
                    <div class=\"process_step\">
                        <div class=\"step_box step_1 {$step1}\">
                            <div class=\"step_box_txt\">
                                <p class=\"step\">STEP 1</p>
                                <p class=\"line\"></p>
                                <p>회원유형 선택</p>
                            </div>
                        </div>
                        <div class=\"step_box step_2 {$step2} \">
                            <div class=\"step_box_txt\">
                                <p class=\"step\">STEP 2</p>
                                <p class=\"line\"></p>
                                <p>약관동의</p>
                            </div>
                        </div>
                        <div class=\"step_box step_3 {$step3}\">
                            <div class=\"step_box_txt\">
                                <p class=\"step\">STEP 3</p>
                                <p class=\"line\"></p>
                                <p>정보입력</p>
                            </div>
                        </div>
                        <div class=\"step_box stpe_4 {$step4}\">
                            <div class=\"step_box_txt\">
                                <p class=\"step\">STEP 4</p>
                                <p class=\"line\"></p>
                                <p>가입완료</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ";

        return $memberType;
    }

    
    #회원가입하기 1차화면
    public function register(){
        header("Cache-Control: no cache");

        // 회원가입 유형
        $type = $this->input->get('type');
        
        $is_member=$this->load->get_var('is_member');
        $config=$this->load->get_var('config');
        $pt_id=$this->load->get_var('pt_id');

        if((!isset($_SESSION['device']) || $_SESSION['device']!="pc") && $this->agent->is_mobile()) {
            goto_url(base_url()."m/bbs/register?type={$type}");
        }
        if($is_member) {
            goto_url(base_url());
        }
        // 본사쇼핑몰에서 회원가입을 받지 않을때
        $config['admin_reg_msg'] = str_replace("\r\n", "\\r\\n", $config['admin_reg_msg']);
        if($config['admin_reg_yes'] && $pt_id == 'admin') {
            alert($config['admin_reg_msg'], base_url());
        }
        // 세션을 지웁니다.
        set_session("ss_mb_reg", "");
        
        $this->template->header();
        $this->registerskin($type);
        $this->template->footer();
    }

    private function registerskin($type){
        $register_action_url = base_url()."bbs/register_form?type={$type}";
        $default=$this->load->get_var('default');
        $config=$this->load->get_var('config');

        $data = array(
            'title' => '회원가입',
            'register_action_url' => $register_action_url,
            'registerProcess' => $this->register_process(1),
            'type' => $type
        );
        $this->load->view('pc/bbs/register_type',$data);
    }

    #회원가입하기 2차화면
    public function register_form(){
        header("Cache-Control: no cache");
        $this->pre_register_process();
        $this->template->header();
        $this->register_form_skin();
        $this->template->footer();
    }

    private function register_form_skin(){
        $w=$this->input->post('w');
        $member = $this->load->get_var('member');
        if(!empty($member) && !empty($this->session->userdata('w'))) {
            $w = $this->session->userdata('w');
        }

        $type=$this->input->get('type');
        $member=$this->load->get_var('member');
        $pt_id=$this->load->get_var('pt_id');
        $config=$this->load->get_var('config');
        $title="";
        $butTxt="";
        if($w == "") {
            $title = '회원가입';
            $butTxt = "회원가입";
            // 추천인을 담는다.
            $member['pt_id'] = $pt_id;
        } else if($w == 'u') {
            $title = '회원정보수정';
            $butTxt = "정보수정";
        }
        $required = ($w=='') ? ' required' : '';
        $readonly = ($w=='u') ? ' readonly' : '';
        $agree=$this->input->post('agree');
        $agree2=$this->input->post('agree2');
        $agree  = preg_replace('#[^0-9]#', '', $agree);
        $agree2 = preg_replace('#[^0-9]#', '', $agree2);
        $register_action_url = base_url().'process/register_form_update';
        $certify="";
        if($config['cf_cert_use'] && ($config['cf_cert_ipin'] || $config['cf_cert_hp'])) {
            $certify="<script src=\"".base_url()."js/certify.js?v=".time()."></script>";
        }
        $notWu="";
        $nowWname="";
        $placeholder_name = $type == "user" ? "" : "Hallyu";

        $member['name']=isset($member['name'])?$member['name']:"";
        $member['cellphone']=isset($member['cellphone'])?$member['cellphone']:"";
        if($w!="u"){
            $notWu="
            <tr class=\"first_line\">
                <th scope=\"row\"><label for=\"reg_mb_id\">아이디</label></th>
                <td>
                    <input type=\"text\" name=\"mb_id\" value=\"{$member['id']}\" id=\"reg_mb_id\" {$required} {$readonly} class=\"frm_input\" maxlength=\"20\" placeholder=\"아이디를 입력해주세요\" onkeyup=\"only_eng_num_text_typing(this)\">
                    <span id=\"msg_mb_id\"></span>
                    <button type=\"button\" onclick=\"reg_mb_id_check()\">중복확인</button>
                </td>
            </tr>";
            $nowWname="<input type=\"text\" name=\"mb_name\" value=\"".$this->funcomplex_lib->get_text($member['name'])."\" id=\"reg_mb_name\" {$required} {$readonly} class=\"frm_input\" size=\"20\" placeholder=\"{$placeholder_name}\">";
        }else{
            $nowWname="<span id=\"reg_mb_name\">".$this->funcomplex_lib->get_text($member['name'])."</span>";
        }
        
        $cf_cert_use="";
        if($config['cf_cert_use']){
            if($config['cf_cert_ipin'])
                $cf_cert_use.= '<button type="button" id="win_ipin_cert" class="btn_small">아이핀 본인인증</button>';
				if($config['cf_cert_hp'])
                    $cf_cert_use.= '<button type="button" id="win_hp_cert" class="btn_small">휴대폰 본인인증</button>';
                $cf_cert_use.= '<noscript>본인인증을 위해서는 자바스크립트 사용이 가능해야합니다.</noscript>';
        }
        
        $mb_certTag="";
        $member['mb_certify']=isset($member['mb_certify'])?$member['mb_certify']:"";
        if($config['cf_cert_use'] && $member['mb_certify']) {
            $mb_cert="";
            if($member['mb_certify'] == 'ipin')
                $mb_cert = '아이핀';
            else
                $mb_cert = '휴대폰';
            $mb_adult="";
            if($member['mb_adult']) {
                $mb_adult="및 <strong>성인인증</strong>";
            }
            $mb_certTag="<div id=\"msg_certify\">
				<strong>{$mb_cert} 본인인증</strong>{$mb_adult}완료
			</div>";
        }

        $cf_cert_use2="";
        if($config['cf_cert_use']){
            $cf_cert_use2="<span class=\"frm_info\">아이핀 본인인증 후에는 이름이 자동 입력되고 휴대폰 본인인증 후에는 이름과 휴대폰번호가 자동 입력되어 수동으로 입력할수 없게 됩니다.</span>";
        }

        $regis_hp="";
        if($config['register_use_hp'] || $config['cf_cert_hp']) {
            $mb_hp = array('010','','');
            if(!empty($member['cellphone'])) {
                $mb_hp = $member['cellphone'];
                $mb_hp = explode("-",$mb_hp);
            }
            $regis_hp="
            <tr>
                <th scope=\"row\"><label for=\"reg_mb_hp\">연락처</label></th>
                <td>
                    <input type=\"text\" name=\"mb_hp_01\" value=\"{$mb_hp[0]}\" ".($config['register_req_hp']?' required':'')." class=\"frm_input small_box\" maxlength=\"3\" onkeyup=\"only_num_typing(this)\">
                    <span class=\"dash\">-</span>
                    <input type=\"text\" name=\"mb_hp_02\" value=\"{$mb_hp[1]}\" ".($config['register_req_hp']?' required':'')." class=\"frm_input small_box\" maxlength=\"4\" onkeyup=\"only_num_typing(this)\">
                    <span class=\"dash\">-</span>
                    <input type=\"text\" name=\"mb_hp_03\" value=\"{$mb_hp[2]}\" ".($config['register_req_hp']?' required':'')." class=\"frm_input small_box\" maxlength=\"4\" onkeyup=\"only_num_typing(this)\">
                    <span class=\"frm_info\">
                        <input type=\"checkbox\" name=\"mb_sms\" id=\"reg_mb_phone\" value=\"Y\"".(($w=='' || $member['smsser'] == 'Y')?' checked':'').">
                        <label for=\"reg_mb_phone\">
                            <span>휴대폰 문자메세지를 받겠습니다.</span>
                        </label>
                    </span>";
            if($config['cf_cert_use'] && $config['cf_cert_hp']) {
                $regis_hp.="<input type=\"hidden\" name=\"old_mb_hp\" value=\"".$this->funcomplex_lib->get_text($member['cellphone'])."\">";
            }
            $regis_hp.="</td>
            </tr>";
        }

        $memEmail=(isset($member['email'])?$member['email']:'');
        $mailser=($w=='' || $member['mailser'] == 'Y')?' checked':'';
        $member['zip']=isset($member['zip'])?$member['zip']:"";
        $member['addr1']=isset($member['addr1'])?$member['addr1']:"";
        $member['addr2']=isset($member['addr2'])?$member['addr2']:"";
        $member['addr3']=isset($member['addr3'])?$member['addr3']:"";
        $member['addr_jibeon']=isset($member['addr_jibeon'])?$member['addr_jibeon']:"";
        $member['email']=isset($member['email'])?$member['email']:"";

        $register_use_addr="";
        if($config['register_use_addr']) {
            $register_use_addr="
            <tr>
                <th scope=\"row\"><label for=\"reg_mb_addr\">주소</label></th>
                <td class=\"addr_row\">
                    <div class=\"vertical\">
                        <input type=\"text\" name=\"mb_addr1\" value=\"".$this->funcomplex_lib->get_text($member['addr1'])."\" id=\"reg_mb_addr1\"".($config['register_req_addr']?' required':'')." class=\"frm_address\" frm_address\" readonly=\"readonly\" placeholder=\"주소검색을 해주세요.\">
                        <input type=\"text\" name=\"mb_addr3\" value=\"".$this->funcomplex_lib->get_text($member['addr3'])."\" id=\"reg_mb_addr3\" class=\"frm_address\" readonly=\"readonly\" placeholder=\"기본 주소\" >
                        <input type=\"text\" name=\"mb_addr2\" value=\"".$this->funcomplex_lib->get_text($member['addr2'])."\" id=\"reg_mb_addr2\" class=\"frm_address\" placeholder=\"상세 주소\">
                        <input type=\"text\" name=\"mb_zip\" value=\"{$member['zip']}\" id=\"reg_mb_zip\" ".($config['register_req_addr']?' required':'')." class=\"frm_address\" maxlength=\"5\" readonly=\"readonly\" placeholder=\"우편 번호\" >
                    </div>
                    <button type=\"button\" class=\"btn_small\" onclick=\"win_zip('fregisterform', 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');\">주소검색</button><br>
                    <input type=\"hidden\" name=\"mb_addr_jibeon\" value=\"".$this->funcomplex_lib->get_text($member['addr_jibeon'])."\">
                </td>
            </tr>";
        }

        $ipinJs="";
        if($config['cf_cert_use'] && $config['cf_cert_ipin']) {
            $ipinJs="// 아이핀인증
            $(\"#win_ipin_cert\").click(function() {
                if(!cert_confirm())
                    return false;
        
                var url = \"".base_url()."pluginFun/okname/ipin1\";
                certify_win_open('kcb-ipin', url);
                return;
            });";
        }

        $hpcertJs="";
        if($config['cf_cert_use'] && $config['cf_cert_hp']) {
            $hpcertJs="$(\"#win_hp_cert\").click(function() {
                if(!cert_confirm())
                    return false;";
            switch($config['cf_cert_hp']) {
                case 'kcb':
                    $cert_url = base_url().'pluginFun/okname/hpcert1';
                    $cert_type = 'kcb-hp';
                    break;
                case 'kcp':
                    $cert_url =  base_url().'pluginFun/kcpcert/kcpcert_form';
                    $cert_type = 'kcp-hp';
                    break;
                case 'lg':
                    $cert_url = base_url().'pluginFun/lgxpay/AuthOnlyReq';
                    $cert_type = 'lg-hp';
                    break;
                default:
                    $hpcertJs.='alert("기본환경설정에서 휴대폰 본인인증 설정을 해주십시오"); return false;';
                    break;
            }
            $hpcertJs.="certify_win_open(\"{$cert_type}\", \"{$cert_url}\");";
        }

        $cf_cert_useJs="";
        if($w == '' && $config['cf_cert_use'] && $config['cf_cert_req']) {
            $cf_cert_useJs.="// 본인인증 체크";
            $cf_cert_useJs.="if(f.cert_no.value==\"\") {alert(\"회원가입을 위해서는 본인인증을 해주셔야 합니다.\");return false;}";
        }

        $phoneCheckJs="";
        // if(($config['register_use_hp'] || $config['cf_cert_hp']) && $config['register_req_hp'] && $type == "user") {
        //     $phoneCheckJs="
        //     // 휴대폰번호 체크
        //     var msg = reg_mb_hp_check();
        //     if(msg.trim()) {
        //         alert(msg);
        //         f.reg_mb_hp.select();
        //         return false;
        //     }";
        // }
        
        $data = array(
            'title' => $title,
            'member' => $member,
            'required' => $required,
            'readonly' => $readonly,
            'register_action_url' => $register_action_url,
            'certifyjs' => $certify,
            'w' => $w,
            'type' => $type,
            'agree' => $agree,
            'agree2' => $agree2,
            "notWu" => $notWu,
            "nowWname" => $nowWname,
            'cf_cert_use' => $cf_cert_use,
            'mb_certTag' => $mb_certTag,
            "cf_cert_use2" => $cf_cert_use2,
            'regis_hp' => $regis_hp,
            'memEmail' => $memEmail,
            'mailser' => $mailser,
            'register_use_addr' => $register_use_addr,
            "butTxt" => $butTxt,
            'ipinJs' => $ipinJs,
            'hpcertJs' => $hpcertJs,
            'cf_cert_useJs' => $cf_cert_useJs,
            'phoneCheckJs' => $phoneCheckJs,
            'registerProcess' => $this->register_process(3)
        );
        $this->load->view('pc/bbs/register_form',$data);
    }

    private function pre_register_process(){
        $is_member=$this->load->get_var('is_member');
        $config=$this->load->get_var('config');
        $pt_id=$this->load->get_var('pt_id');
        $member=$this->load->get_var('member');

        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $OM_TIME_YMD=substr($OM_TIME_YMDHIS,0,10);  
        $OM_HASH_TOKEN=md5(base_url().$OM_TIME_YMD.$_SERVER['REMOTE_ADDR']);
        // 불법접근을 막도록 토큰생성
        $token = md5(uniqid(rand(), true));
        $w = $this->input->post('w');
        $mb_id = $this->input->post('mb_id');

        if(!empty($member) && !empty($this->session->userdata('w'))) {
            $w = $this->session->userdata('w');
            $mb_id = $member['id'];
        }

        set_session("ss_token", $token);
        set_session("ss_cert_no",   "");
        set_session("ss_cert_hash", "");
        set_session("ss_cert_type", "");
        set_session("ss_hash_token", $OM_HASH_TOKEN);
        if($w == "") {
            // 회원 로그인을 한 경우 회원가입 할 수 없다
            // 경고창이 뜨는것을 막기위해 아래의 코드로 대체
            // alert("이미 로그인중이므로 회원 가입 하실 수 없습니다.", "./");
            if($is_member)
                goto_url(base_url());

            // 본사쇼핑몰에서 회원가입을 받지 않을때
            $config['admin_reg_msg'] = str_replace("\r\n", "\\r\\n", $config['admin_reg_msg']);
            if($config['admin_reg_yes'] && $pt_id == 'admin') {
                alert($config['admin_reg_msg'], base_url());
            }
        } else if($w == 'u') {
            if(!$is_member)
                alert('로그인 후 이용하여 주십시오.', base_url());
        
            if($member['id'] == 'admin')
                alert('관리자의 회원정보는 관리자 화면에서 수정해 주십시오.', base_url());
        
            if($member['id'] != $mb_id)
                alert('로그인된 회원과 넘어온 정보가 서로 다릅니다.');
            $mb_password=$this->input->post('mb_password');
                    
            if($mb_password) {
                // 수정된 정보를 업데이트후 되돌아 온것이라면 비밀번호가 암호화 된채로 넘어온것임
                if($this->input->post('is_update')) {
                    $tmp_password = $mb_password;
                } else {
                    $tmp_password = $this->global_lib->changePassword($mb_password,$member['id']);
                }

                if($member['passwd'] != $tmp_password) {
                    alert('비밀번호가 틀립니다.');
                }
            }
            $tb['title'] = '회원정보수정';
            $this->load->vars(array('tb'=>$tb));
            $member['name']=isset($member['name'])?$member['name']:"";
            $member['cellphone']=isset($member['cellphone'])?$member['cellphone']:"";
            set_session("ss_reg_mb_name", $member['name']);
            set_session("ss_reg_mb_hp", $member['cellphone']);
        } else {
            alert('w 값이 제대로 넘어오지 않았습니다.');
        }
    }

    #register_result 회원가입 결과 화면
    public function register_result(){
        if(isset($_SESSION['ss_mb_reg']))
            $mb = $this->global_lib->get_member($_SESSION['ss_mb_reg']);

        // 회원정보가 없다면 초기 페이지로 이동
        if(!$mb['id'])
            goto_url(base_url());
        $this->template->header();
        $this->register_resultSkin();
        $this->template->footer();
    }
    private function register_resultSkin(){
        $mb="";
        if(isset($_SESSION['ss_mb_reg']))
            $mb = $this->global_lib->get_member($_SESSION['ss_mb_reg']);
        $title="회원가입 완료";
        $name=$this->funcomplex_lib->get_text($mb['name']);
        $data=array('title'=>$title,'name'=>$name,'mb'=>$mb);
        $this->load->view('pc/bbs/register_result',$data);
    }
    #로그인하기
    public function login(){
        $url = $this->input->get('url');
        // 이미 로그인 중이라면
        $is_member=$this->load->get_var('is_member');
        if($is_member){
            if($url)
                goto_url($url);
            else
                goto_url(base_url());
        }
        $this->template->header();
        $this->loginSkin();
        $this->template->footer();
    }
    private function loginSkin(){
        $default=$this->load->get_var('default');
        $url = $this->input->get('url');
        $title="로그인";
        $login_url        = $this->funcomplex_lib->login_url($url);
        $login_action_url = base_url()."process/login_check";
        $notLogoOrd="";
        if(preg_match("/orderform/", $url)) {
            $notLogoOrd="<dd><a href=\"".base_url()."shop/orderform\" class=\"btn_large red wfull\">비회원으로 구매하기</a></dd>";
        }
        $snsLogin="";
        $snsJs="";
        if($default['de_sns_login_use']) {
            $snsLogin.="<div class=\"sns_btn\">
                <h3>SNS 계정 로그인</h3>";
            if($default['de_naver_appid'] && $default['de_naver_secret']) {
                $strObj=$this->gvcomplex_lib->get_login_oauth('naver', 1);
                $snsLogin.=$strObj['str'];
                $snsJs.=$strObj['strJs'];
            }
            if($default['de_facebook_appid'] && $default['de_facebook_secret']) {
                $strObj=$this->gvcomplex_lib->get_login_oauth('facebook', 1);
                $snsLogin.=$strObj['str'];
                $snsJs.=$strObj['strJs'];
            }
            if($default['de_google_appid'] && $default['de_google_secret']) {
                $strObj=$this->gvcomplex_lib->get_login_oauth('google', 1);
                $snsLogin.=$strObj['str'];
                $snsJs.=$strObj['strJs'];
            }
            if($default['de_kakao_rest_apikey']) {
                $strObj=$this->gvcomplex_lib->get_login_oauth('kakao', 1);
                $snsLogin.=$strObj['str'];
                $snsJs.=$strObj['strJs'];
            }
            $snsLogin.="</div>";
        }
        $data = array(
            'title' => $title,
            'login_url' => $login_url,
            'login_action_url' => $login_action_url,
            "notLogoOrd" => $notLogoOrd,
            'snsLogin' => $snsLogin,
            'snsJs' => $snsJs
        );
        $this->load->view('pc/bbs/login',$data);
    }
    #비번찾기
    public function password_lost(){
        $is_member=$this->load->get_var('is_member');
        if($is_member) {
            alert_close("이미 로그인중입니다.");
        }
        $this->template->header_sub();
        $this->password_lost_skin();
        $this->template->footer_sub();
    }
    private function password_lost_skin(){
        $title="회원정보찾기";
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        $form_action_url = base_url()."process/password_lost2";
        $data=array('title'=>$title,'form_action_url'=>$form_action_url,'token'=>$token);
        $this->load->view('pc/bbs/paswword_lost',$data);
    }   
    #회원정보수정 화면
    public function member_confirm(){
        $is_member=$this->load->get_var('is_member');
        if(!$is_member)
            alert('로그인 한 회원만 접근하실 수 있습니다.', base_url().'bbs/login');

        // sns 회원이면 수정 페이지로 리다이렉트
        $member = $this->load->get_var('member');
        if(!empty($member['sns_id'])) {
            $this->session->set_flashdata(['w' => 'u']);
            redirect('bbs/register_form?type=user');
        }

        $this->template->header();
        $this->member_confirmSkin();
        $this->template->footer();
    }
    private function member_confirmSkin(){
        $title="회원 비밀번호 확인";
        $url = $this->input->get('url');

        $member=$this->load->get_var('member');
        $type = $member['supply'] == "Y" ? "seller" : 'user';
        $url = $url."?type={$type}";

        // url 체크
        $this->funcomplex_lib->check_url_host($url);
        $url = $this->funcomplex_lib->get_text($url);
        $data=array('title'=>$title,'url'=>$url,'member'=>$member);
        $this->load->view('pc/bbs/member_confirm',$data);
    }
    #회원탈퇴 화면
    public function leave_form(){
        $is_member=$this->load->get_var('is_member');
        $urlencode=$this->load->get_var('urlencode');
        if((!isset($_SESSION['device']) || $_SESSION['device']!="pc") && $this->agent->is_mobile()) {
            goto_url(base_url().'m/bbs/leave_form');
        }
        if(!$is_member) {
            goto_url(base_url().'bbs/login?url='.$urlencode);
        }
        $this->template->header();
        $this->leave_formSkin();
        $this->template->footer();
    }
    private function leave_formSkin(){
        $member=$this->load->get_var('member');
        $title="회원탈퇴";
        $form_action_url = base_url().'process/leave_form_update';
        $aside_my=$this->template->aside_my();
        $email=$member['email'] ? $member['email'] : '미등록';
        $cellphone=$member['cellphone'] ? $member['cellphone'] : '미등록';
        $data=array('title'=>$title,'aside_my'=>$aside_my,'form_action_url'=>$form_action_url,'member'=>$member,'email'=>$email,'cellphone'=>$cellphone);
        $this->load->view('pc/bbs/leave_form',$data);
    }
    #공지사항 리스트
    public function noticeList(){
        $boardid=$this->input->get('boardid');
        $is_member=$this->load->get_var('is_member');
        $member=$this->load->get_var('member');
        $board=$this->load->get_var('board');
        $sfl=$this->input->get('sfl');
        $stx=$this->input->get('stx');
        if((!isset($_SESSION['device']) || $_SESSION['device']!="pc") && $this->agent->is_mobile()) {
            goto_url(base_url().'m/bbs/board_list?boardid='.$boardid);
        }
        if(!$is_member) { $member['grade'] = 99; }

        if($board['list_priv'] < 99) {
            if($member['grade'] > $board['list_priv']) {
                alert('권한이 없습니다.');
            }
        }
        if($board['topfile']) {	
            if($sfl=='writer') {	
                if(!$stx){	
                    $stx = $member['index_no'];	
                }	
            } else {
                if(strpos($board['topfile'],'mypage')){
                    $this->mypage_template->mypage_admin_head();
                    $this->mypage_template->board_header();
                }else{
                    $this->template->header();
                    $this->template->board_header();
                }
            }
        }

        $this->listSkin($board);

        if($board['downfile']) {
            if(strpos($board['downfile'],'mypage')){
                $this->mypage_template->admin_footer_end();
                $this->mypage_template->mypage_admin_footer();
            }else{
                $this->template->board_footer();
                $this->template->footer();
            }
            
        }
    }
    private function listSkin($board){
        $b_content_head=isset($board['content_head'])?$board['content_head']:"";
        $b_content_tail=isset($board['content_tail'])?$board['content_tail']:"";
        $board['width'] =($board['width'] <= 100)?$board['width'] ."%":$board['width'];
        $bo_img_url = base_url().'bbs/'.$board['skin'];#basic,gallery,webzine
        $data=array('board'=>$board,'b_content_head'=>$b_content_head,'b_content_tail'=>$b_content_tail,'bo_img_url'=>$bo_img_url);
        #$this->load->view("bbs/{$board['skin']}/list",$data);
        $this->gateway($board['skin'],$data,'list'); #중간에 하나를 거쳐서 skin이 basic,gallery,webzine에 따라서 함수 호출하기
        #list,modify,read_list는 그 basic,gallery,webzine 함수명이 호출된 이후에 분리
    }
    public function del(){
        $mode=$this->input->post('mode');
        $boardid=$this->input->post('boardid');
        $page=$this->input->post('page');
        $sfl=$this->load->get_var('sfl');

        $urlencode=$this->load->get_var('urlencode');
        $write=$this->load->get_var('write');
        $member=$this->load->get_var('member');
        $qstr=$this->load->get_var('qstr');
        $is_member=$this->load->get_var('is_member');
        $board=$this->load->get_var('board');
        
        if($write['writer']) {
            if(!$is_member) {
                goto_url(base_url().'bbs/login?url='.$urlencode);
            }

            if(!$this->gvcomplex_lib->is_admin()) {
                if($write['writer'] != $member['index_no']) {
                    alert('권한이 없습니다.');
                }
            }
        }

        if($board['topfile']) {	
            if($sfl=='writer') {	
                if(!$stx){	
                    $stx = $member['index_no'];	
                }	
            } else {
                if(strpos($board['topfile'],'mypage')){
                    $this->mypage_template->mypage_admin_head();
                    $this->mypage_template->board_header();
                }else{
                    $this->template->header();
                    $this->template->board_header();
                }
            }
        }

        if($board['width'] <= 100) {	
            $board['width'] = $board['width'] ."%";	
        }

        $this->delSkin($board);
        if($board['downfile']) {	
            if(strpos($board['downfile'],'mypage')){
                $this->mypage_template->admin_footer_end();
                $this->mypage_template->mypage_admin_footer();
            }else{
                $this->template->board_footer();
                $this->template->footer();
            }
        }
    }
    private function delSkin($board){
        $b_content_head=isset($board['content_head'])?$board['content_head']:"";
        $b_content_tail=isset($board['content_tail'])?$board['content_tail']:"";
        $board['width'] =($board['width'] <= 100)?$board['width'] ."%":$board['width'];
        $bo_img_url = base_url().'bbs/'.$board['skin'];#basic,gallery,webzine
        $data=array('board'=>$board,'b_content_head'=>$b_content_head,'b_content_tail'=>$b_content_tail,'bo_img_url'=>$bo_img_url);
        #$this->load->view("bbs/{$board['skin']}/list",$data);
        $this->gateway($board['skin'],$data,'del'); #중간에 하나를 거쳐서 skin이 basic,gallery,webzine에 따라서 함수 호출하기
        #list,modify,read_list는 그 basic,gallery,webzine 함수명이 호출된 이후에 분리
    }
    public function modify(){
        $is_member=$this->load->get_var('is_member');
        $member=$this->load->get_var('member');
        $write=$this->load->get_var('write');
        $board=$this->load->get_var('board');
        $sfl=$this->input->get('sfl');
        if(!$is_member) { $member['grade'] = 99; }

        if($is_member) {
            if(!$this->gvcomplex_lib->is_admin()) {
                if($write['writer']!=$member['index_no']) {
                    alert('권한이 없습니다.');
                }
            }
        }
        
        if($board['topfile']) {	
            if($sfl=='writer') {	
                if(!$stx){	
                    $stx = $member['index_no'];	
                }	
            } else {
                if(strpos($board['topfile'],'mypage')){
                    $this->mypage_template->mypage_admin_head();
                    $this->mypage_template->board_header();
                }else{
                    $this->template->header();
                    $this->template->board_header();
                }
            }
        }
        
        if($board['width'] <= 100) {	
            $board['width'] = $board['width'] ."%";	
        }
        
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
               
        $this->modifySkin($board);
        
        if($board['downfile']) {	
            if(strpos($board['downfile'],'mypage')){
                $this->mypage_template->admin_footer_end();
                $this->mypage_template->mypage_admin_footer();
            }else{
                $this->template->board_footer();
                $this->template->footer();
            }
        }
    }
    private function modifySkin($board){
        $b_content_head=isset($board['content_head'])?$board['content_head']:"";
        $b_content_tail=isset($board['content_tail'])?$board['content_tail']:"";
        $board['width'] =($board['width'] <= 100)?$board['width'] ."%":$board['width'];
        $bo_img_url = base_url().'bbs/'.$board['skin'];#basic,gallery,webzine
        $from_action_url = base_url()."process/modify_update";
        $data=array('board'=>$board,'b_content_head'=>$b_content_head,'b_content_tail'=>$b_content_tail,'bo_img_url'=>$bo_img_url,
                    'from_action_url'=>$from_action_url);
        #$this->load->view("bbs/{$board['skin']}/list",$data);
        $this->gateway($board['skin'],$data,'modify'); #중간에 하나를 거쳐서 skin이 basic,gallery,webzine에 따라서 함수 호출하기
        #list,modify,read_list는 그 basic,gallery,webzine 함수명이 호출된 이후에 분리
    }
    #답글달아주기
    public function reply(){
        $is_member=$this->load->get_var('is_member');
        $member=$this->load->get_var('member');
        $write=$this->load->get_var('write');
        $board=$this->load->get_var('board');
        $sfl=$this->input->get('sfl');
        if(!$is_member) { $member['grade'] = 99; }

        if($board['reply_priv'] < 99) {
            if($member['grade'] > $board['reply_priv'])	{
                alert('권한이 없습니다.');
            }
        }
        
        if($board['topfile']) {	
            if($sfl=='writer') {	
                if(!$stx){	
                    $stx = $member['index_no'];	
                }	
            } else {
                if(strpos($board['topfile'],'mypage')){
                    $this->mypage_template->mypage_admin_head();
                    $this->mypage_template->board_header();
                }else{
                    $this->template->header();
                    $this->template->board_header();
                }
            }
        }
        
        if($board['width'] <= 100) {	
            $board['width'] = $board['width'] ."%";	
        }

        $this->replySkin($board);
        
        if($board['downfile']) {	
            if(strpos($board['downfile'],'mypage')){
                $this->mypage_template->admin_footer_end();
                $this->mypage_template->mypage_admin_footer();
            }else{
                $this->template->board_footer();
                $this->template->footer();
            }
        }
    }
    private function replySkin($board){
        $index_no=$this->input->get('index_no');
        $sfl=$this->input->get('sfl');
        $stx=$this->input->get('stx');
        $page=$this->input->get('stx');

        $write=$this->load->get_var('writer');

        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);

        $boardid=$this->input->get('boardid');
        $is_member=$this->load->get_var('is_member');
        $member=$this->load->get_var('member');
        $from_action_url = base_url()."process/reply_update";

        $is_memeberTag="";
        $passTag="";
        if($is_member) {
            $is_memeberTag.=$member['name'];
            $is_memeberTag.="<input type=\"hidden\" name=\"writer_s\" value=\"{$member['name']}\">";
        }else {
            $is_memeberTag.="<input type=\"text\" name=\"writer_s\" class=\"frm_input\">";
            $passTag="
                <tr>
                    <th scope=\"row\">비밀번호</td>
                    <td><input name=\"passwd\" type=\"password\" class=\"frm_input\"></td>
                </tr>";
        }
        $use_category="";
        if($board['use_category'] == '1') {
            $use_category.="
            <tr>
                <th scope=\"row\">분류</th>
                <td>
                    <select name=\"ca_name\">
                    <option value=\"\">선택하세요</option>";
                    $use_category.=get_category_option($board['usecate']);
                    $use_category.="
                    </select>
                    <script>document.fboardform.ca_name.value='".$write['ca_name']."';</script>
                </td>
            </tr>";
        }

        $option = "";
        $option_hidden = "";
        if($this->gvcomplex_lib->is_admin()) {
            $checked="";
            if($write['btype']=='1') { $checked = 'checked'; }
            $option .= "<input type=\"checkbox\" name=\"btype\" value=\"1\" $checked> 공지사항";

            $checked="";
            if($write['issecret']=='Y') { $checked = 'checked'; }
            $option .= "<input type=\"checkbox\" name=\"issecret\" value=\"Y\" class=\"marl15\" $checked> 비밀글";
        } else {
            switch($board['use_secret']){
                case '0':
                    $option_hidden .= "<input type=\"hidden\" value=\"N\" name=\"issecret\">";
                    break;
                case '1':
                    if($write['issecret']=='Y') { $checked = 'checked'; }
                    $option .= "<input type=\"checkbox\" value=\"Y\" name=\"issecret\" $checked> 비밀글";
                    break;
                case '2':
                    $option_hidden .= "<input type=\"hidden\" value=\"Y\" name=\"issecret\">";
                    break;
            }
        }
        $optionTag="";
        if($option) {
            $optionTag="
            <tr>
                <th scope=\"row\">옵션</th>
                <td>{$option}</td>
            </tr>";
        }
        $editTag=$this->global_lib->editor_html('memo', $this->funcomplex_lib->get_text($write['memo'], 0));
        $usefileTag="";
        if($board['usefile']=='Y') {
            $usefileTag="
            <tr>
                <th scope=\"row\">첨부파일1</th>
                <td><input type=\"file\" name=\"file1\"></td>
            </tr>
            <tr>
                <th scope=\"row\">첨부파일2</th>
                <td><input type=\"file\" name=\"file2\"></td>
            </tr>";
        }
        $not_is_memberJs="";
        if(!$is_member) {
            $not_is_memberJs="
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
        $useCateJs="";
        if($board['use_category'] == '1') {
            $useCateJs="
            if(!f.ca_name.value) {
                alert('분류를 선택하세요.');
                f.ca_name.focus();
                return false;
            }";
        } 
        $memoEditJs=$this->global_lib->get_editor_js('memo');
        $memoChkJs=$this->global_lib->chk_editor_js('memo');

        $data=array('from_action_url'=>$from_action_url,'token'=>$token,'board'=>$board,'boardid'=>$boardid,'is_memeberTag'=>$is_memeberTag,
                    "passTag"=>$passTag,'use_category'=>$use_category,'option_hidden'=>$option_hidden,'optionTag'=>$optionTag,'editTag'=>$editTag,
                'usefileTag'=>$usefileTag,'not_is_memberJs'=>$not_is_memberJs,'useCateJs'=>$useCateJs,'memoEditJs'=>$memoEditJs,'memoChkJs'=>$memoChkJs,
                'index_no'=>$index_no,'sfl'=>$sfl,'stx'=>$stx,'page'=>$page);
        $this->load->view('pc/bbs/reply',$data);
    }
    #공지사항 글작성페이지
    public function write(){
        $boardid=$this->input->get('boardid');
        $is_member=$this->load->get_var('is_member');
        $member=$this->load->get_var('member');
        $board=$this->load->get_var('board');
        $sfl=$this->input->get('sfl');
        $stx=$this->input->get('stx');
        if(!$is_member) { $member['grade'] = 99; }
        if($board['write_priv'] < 99) {
            if($member['grade'] > $board['write_priv']) {
                alert("권한이 없습니다.", base_url()."bbs/noticeList?boardid=$boardid");
            }
        }
        if($board['topfile']) {	
            if($sfl=='writer') {	
                if(!$stx){	
                    $stx = $member['index_no'];	
                }	
            } else {
                if(strpos($board['topfile'],'mypage')){
                    $this->mypage_template->mypage_admin_head();
                    $this->mypage_template->board_header();
                }else{
                    $this->template->header();
                    $this->template->board_header();
                }
            }
        }

        $this->writeSkin($board);

        if($board['downfile']) {	
            if(strpos($board['downfile'],'mypage')){
                $this->mypage_template->admin_footer_end();
                $this->mypage_template->mypage_admin_footer();
            }else{
                $this->template->board_footer();
                $this->template->footer();
            }
        }
    }
    private function writeSkin($board){
        $boardid=$this->input->get('boardid');
        $is_member=$this->load->get_var('is_member');
        $member=$this->load->get_var('member');
        
        $b_content_head=isset($board['content_head'])?$board['content_head']:"";
        $b_content_tail=isset($board['content_tail'])?$board['content_tail']:"";

        if($board['width'] <= 100) {
            $board['width'] = $board['width'] ."%";
        }
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        $bo_img_url = base_url().'bbs/'.$board['skin'];#basic,gallery,webzine
        $from_action_url = base_url()."process/write_update";
        $is_memeberTag="";
        $passTag="";
        if($is_member) {
            $is_memeberTag.=$member['name'];
            $is_memeberTag.="<input type=\"hidden\" name=\"writer_s\" value=\"$member[name]\">";
        }else {
            $is_memeberTag.="<input type=\"text\" name=\"writer_s\" class=\"frm_input\">";
            $passTag="
                <tr>
                    <th scope=\"row\">비밀번호</td>
                    <td><input name=\"passwd\" type=\"password\" class=\"frm_input\"></td>
                </tr>";
        }
        $use_category="";
        if($board['use_category'] == '1') {
            $use_category.="
            <tr>
                <th scope=\"row\">분류</th>
                <td>
                    <select name=\"ca_name\">
                    <option value=\"\">선택하세요</option>";
                    $use_category.=get_category_option($board['usecate']);
                    $use_category.="
                    </select>
                </td>
            </tr>";
        }

        $option = "";
        $option_hidden = "";
        if($this->gvcomplex_lib->is_admin()) {
            $option .= "<input type=\"checkbox\" name=\"btype\" value=\"1\"> 공지사항";
            $option .= "<input type=\"checkbox\" name=\"issecret\" value=\"Y\" class=\"marl15\"> 비밀글";
        } else {
            switch($board['use_secret']){
                case '0':
                    $option_hidden .= "<input type=\"hidden\" value=\"N\" name=\"issecret\">";
                    break;
                case '1':
                    $option .= "<input type=\"checkbox\" value=\"Y\" name=\"issecret\"> 비밀글";
                    break;
                case '2':
                    $option_hidden .= "<input type=\"hidden\" value=\"Y\" name=\"issecret\">";
                    break;
            }
        }
        $optionTag="";
        if($option) {
            $optionTag="
            <tr>
                <th scope=\"row\">옵션</th>
                <td>{$option}</td>
            </tr>";
        }
        $editTag=$this->global_lib->editor_html('memo', $this->funcomplex_lib->get_text($board['insert_content'], 0));

        $usefileTag="";
        if($board['usefile']=='Y') {
            $usefileTag="
            <tr>
                <th scope=\"row\">첨부파일1</th>
                <td><input type=\"file\" name=\"file1\"></td>
            </tr>
            <tr>
                <th scope=\"row\">첨부파일2</th>
                <td><input type=\"file\" name=\"file2\"></td>
            </tr>";
        }
        $not_is_memberJs="";
        if(!$is_member) {
            $not_is_memberJs="
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
        $useCateJs="";
        if($board['use_category'] == '1') {
            $useCateJs="
            if(!f.ca_name.value) {
                alert('분류를 선택하세요.');
                f.ca_name.focus();
                return false;
            }";
        } 
        $memoEditJs=$this->global_lib->get_editor_js('memo');
        $memoChkJs=$this->global_lib->chk_editor_js('memo');
        $data=array('bo_img_url'=>$bo_img_url,'from_action_url'=>$from_action_url,'token'=>$token,'board'=>$board,'boardid'=>$boardid,'is_memeberTag'=>$is_memeberTag,
                    "passTag"=>$passTag,'use_category'=>$use_category,'option_hidden'=>$option_hidden,'optionTag'=>$optionTag,'editTag'=>$editTag,
                'usefileTag'=>$usefileTag,'not_is_memberJs'=>$not_is_memberJs,'useCateJs'=>$useCateJs,'memoEditJs'=>$memoEditJs,'memoChkJs'=>$memoChkJs);
        $this->load->view('pc/bbs/write',$data);
    }
    #공지사항 글읽기 화면
    public function read(){
        $boardid=$this->input->get('boardid');
        $is_member=$this->load->get_var('is_member');
        $member=$this->load->get_var('member');
        $board=$this->load->get_var('board');
        $index_no=$this->input->get('index_no');
        $qstr=$this->load->get_var('qstr');

        $page=$this->input->get('page');
        $sfl=$this->input->get('sfl');

        if((!isset($_SESSION['device']) || $_SESSION['device']!="pc") && $this->agent->is_mobile()) {
            goto_url(base_url().'m/bbs/read?boardid='.$boardid.'&index_no='.$index_no);
        }
        if($board['read_priv'] < 99) {
            if($member['grade'] > $board['read_priv']) {
                alert('권한이 없습니다.');
                exit();
            }
        }
        if($board['topfile']) {	
            if($sfl=='writer') {	
                if(!$stx){	
                    $stx = $member['index_no'];	
                }	
            } else {
                if(strpos($board['topfile'],'mypage')){
                    $this->mypage_template->mypage_admin_head();
                    $this->mypage_template->board_header();
                }else{
                    $this->template->header();
                    $this->template->board_header();
                }
            }
        }
        $qstr1 = "boardid=$boardid$qstr&page=$page";
        $qstr2 = "index_no=$index_no&boardid=$boardid$qstr&page=$page";
        $qstr3 = "boardid=$boardid$qstr";
        $queryString=array('qstr1'=>$qstr1,'qstr2'=>$qstr2,'qstr3'=>$qstr3);
        $this->readSkin($board,$queryString);

        if($board['downfile']) {	
            if(strpos($board['downfile'],'mypage')){
                $this->mypage_template->admin_footer_end();
                $this->mypage_template->mypage_admin_footer();
            }else{
                $this->template->board_footer();
                $this->template->footer();
            }
        }
    }
    private function readSkin($board,$queryString){
        $is_member=$this->load->get_var('is_member');
        $member=$this->load->get_var('member');
        $board=$this->load->get_var('board');
        $write=$this->load->get_var('write');
        $qstr=$this->load->get_var('qstr');

        $index_no=$this->input->get('index_no');
        $boardid=$this->input->get('boardid');
        $sfl=$this->input->get('sfl');
        $stx=$this->input->get('stx');
        $page=$this->input->get('page');

        if(!$is_member) { $member['grade'] = 99; }
        $bo_wdate		= date("Y-m-d",$write['wdate']);
        $bo_writer		= $write['writer'];
        $bo_writer_s	= $write['writer_s'];
        $bo_issecret	= $write['issecret'];
        $bo_subject		= $write['subject'];
        $bo_memo		= nl2br($write['memo']);
        $bo_file1		= $write['fileurl1'];
        $bo_file2		= $write['fileurl2'];
        $bo_hit			= $write['readcount'] + 1;
        $bo_passwd		= $write['passwd'];
        $qstr1 = $queryString['qstr1'];
        $qstr2 = $queryString['qstr2'];
        $qstr3 = $queryString['qstr3'];
        $sql="select id from shop_member where index_no = ?";
        $param=array($bo_writer);
        $mb = $this->query_model->returnOneArr($sql,$param);
        $bo_writer_id = isset($mb['id'])?$mb['id']:"";
        $sql2="update shop_board_{$boardid} set readcount=? where index_no=?";
        $param2=array($bo_hit,$index_no);
        $this->query_model->returnNull($sql2,$param2);

        $accept = array("gif","jpg","GIF","JPG","PNG","png");
        $bo_subject = "<b>".$bo_subject."</b>";
        if($bo_issecret=='Y'){
            if($bo_writer) {
                if(!$is_member) {
                    goto_url(base_url().'bbs/login?url='.$urlencode);
                }
            }

            if(!$this->gvcomplex_lib->is_admin()){
                if($is_member){
                    //관리자가 답변을 달면 본인이 글을 볼수가 없었던문제 버그수정 jck
                    $sql="select fid from shop_board_{$boardid} where index_no = ?";
                    $param=array($index_no);
                    $sb_sqlObj = $this->query_model->returnQueryObj($sql,$param);
                    if( $sb_sqlObj->num_rows() > 0 ) {
                        $sql= "select fid from shop_board_{$boardid} where index_no = ?";
                        $param=array($index_no);
                        $sb_row =  $this->query_model->returnOneArr($sql,$param);
                        $sql2="select writer from shop_board_{$boardid} where fid = ? and thread = 'A' ";
                        $param2=array($sb_row['fid']);
                        $wr_row = $this->query_model->returnOneArr($sql2,$param2);
                        $bo_writer = $wr_row['writer'];
                    }

                    if($member['index_no'] != $bo_writer) {
                        alert("비밀글은 열람하실 수 없습니다.");
                    }
                }else{
                    $inpasswd = $_GET['inpasswd'];
                    $member['index_no'] = 0;

                    if($inpasswd != $bo_passwd) {
                        goto_url(base_url()."bbs/secret?index_no=$index_no&$qstr1");
                    }else{
                        //관리자가 답변을 달면 본인이 글을 볼수가 없었던문제 버그수정 jck
                        $sql="select fid from shop_board_{$boardid} where index_no = ?";
                        $param=array($index_no);
                        $sb_sql = $this->query_model->returnQueryObj($sql,$param);
                        if( $sb_sql->num_rows() > 0 ) {
                            $sql="select fid from shop_board_{$boardid} where index_no = ?";
                            $param=array($index_no);
                            $sb_row = $this->query_model->returnOneArr($sql,$param);
                            $sql2="select writer from shop_board_{$boardid} where fid = ? and thread = 'A' ";
                            $param2=array($sb_row['fid']);
                            $wr_row = $this->query_model->returnOneArr($sql2,$param2);
                            $bo_writer = $wr_row['writer'];
                        }
                    }
                }
            }
        }
        $b_content_head=isset($board['content_head'])?$board['content_head']:"";
        $b_content_tail=isset($board['content_tail'])?$board['content_tail']:"";
        $board['width'] =($board['width'] <= 100)?$board['width'] ."%":$board['width'];
        $bo_img_url = base_url().'bbs/'.$board['skin'];#basic,gallery,webzine
        $from_action_url = base_url()."process/tail_write";

        $data=array('from_action_url'=>$from_action_url,'bo_img_url'=>$bo_img_url,'b_content_head'=>$b_content_head,'b_content_tail'=>$b_content_tail,
                    'bo_subject'=>$bo_subject,
                    'bo_writer_id'=>$bo_writer_id,'bo_file1'=>$bo_file1,'bo_file2'=>$bo_file2,'board'=>$board,'sfl'=>$sfl,'stx'=>$stx,'bo_wdate'=>$bo_wdate,
                    'bo_hit'=>$bo_hit,'queryString'=>$queryString,'accept'=>$accept,
                    'qstr2'=>$qstr2,'qstr3'=>$qstr3);

        $this->gateway($board['skin'],$data,'read'); #중간에 하나를 거쳐서 skin이 basic,gallery,webzine에 따라서 함수 호출하기
    }
    private function gateway($skin,$data,$theme){
        if($skin=="basic")
            $this->basic($data,$theme);
        else if($skin=="gallery")
            $this->gallery($data,$theme);
        else if($skin=="webzine")
            $this->webzine($data,$theme);
            
    }
    #basic(공지사항)
    public function basic($data,$theme){
        $board=$this->load->get_var('board');
        $default=$this->load->get_var('default');
        $pt_id=$this->load->get_var('pt_id');
        $member=$this->load->get_var('member');
        $is_member=$this->load->get_var('is_member');
        $member['index_no']=isset($member['index_no'])?$member['index_no']:"";
        $config=$this->load->get_var('config');
        $write=$this->load->get_var('write');
        $bo_img_url=$data['bo_img_url'];
        
        if($theme=="list"){
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $qstr=$this->load->get_var('qstr');
            $usecate = explode("|", $board['usecate']);
            $ca_name=$this->input->get('ca_name');
            $gw_search_value=$this->config->item('gw_search_value');
            $gw_search_text=$this->config->item('gw_search_text');
            $sfl=$this->input->get('sfl');
            
            $stx=addslashes($this->input->get('stx'));
            $page=$this->input->get('page');
            $boardid=$this->input->get('boardid');
            $sql_search2 = "";
            if($default['de_board_wr_use']) {
                $sql_search2 = " and pt_id = '$pt_id' ";
            }
            $sql_common = " from shop_board_{$boardid} ";
            $sql_search = " where btype = '2' {$sql_search2} ";
            if($ca_name)
                $sql_search .= " and ca_name='{$ca_name}' ";

            if($sfl && $stx) {
                $sql_search .= " and ($sfl like '%$stx%') ";
            }
            $sql_order  = " order by fid desc, thread asc ";

            $sql = " select count(*) as cnt $sql_common $sql_search ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            $rows = $board['page_num'];
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);

            $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
            $result = $this->query_model->returnArr($sql);

            $reply_limit = 6;
            $run = 0;

            $colspan = 6;
            if($this->gvcomplex_lib->is_admin()) $colspan++;

            $qstr1 = "boardid=$boardid$qstr&page=$page";
            $qstr2 = "boardid=$boardid$qstr";

            $data['boardid']=$boardid;
            $data['sfl']=$sfl;
            $data['stx']=$stx;
            $data['page']=$page;
            $bocateTag="";
            if($board['use_category']) { 
                $bocateTag.="
                <ul class=\"bo_cate\">
                    <li ".((!$ca_name)? 'class="active"':'')."><a href=\"".base_url()."bbs/noticeList{$qstr1}\">전체</a></li>";
                    for($i=0; $i<count($usecate); $i++) {
                        $active = '';
                        if($ca_name == $usecate[$i])
                            $active = ' class="active"';

                            $bocateTag.="<li{$active}><a href=\"".base_url()."bbs/noticeList{$qstr1}&ca_name={$usecate[$i]}\">{$usecate[$i]}</a></li>";
                    }
                $bocateTag.="</ul>";
            }
            $data['bocateTag']=$bocateTag;
            $data['total_count']=$total_count;
            $adminTb=array('','','');
            if($this->gvcomplex_lib->is_admin()){
                $adminTb[0]="<col width=\"50\">";
                $adminTb[1]="<th scope=\"col\"><input type=\"checkbox\" name=\"chkall\" value=\"1\" onclick=\"check_all(this.form);\"></th>";
            }
            $sql = " select * from shop_board_{$boardid} where btype = '1' {$sql_search2} order by fid desc ";
	        $rst = $this->query_model->returnArr($sql);
            $listTb="";
            foreach($rst as $key => $row){
                $bo_subject	= cut_str($row['subject'], $board['list_cut']);
                $bo_wdate	= date("Y-m-d", $row['wdate']);

                if($row['fileurl1'] || $row['fileurl2'])
                    $bo_file_yes ="<img src='".$bo_img_url."/img/file_on.gif'>";
                else
                    $bo_file_yes ="<img src='".$bo_img_url."/img/file_off.gif'>";

                if((time()-$row['wdate']) < (60*60*24))
                    $bo_newicon = "&nbsp;<img src='".$bo_img_url."/img/iconY.gif'>";
                else
                    $bo_newicon = "";

                $bo_href = base_url().'bbs/read?index_no='.$row['index_no'].'&'.$qstr1; #차후분기처리해야함
                $listTb.="
                <tr>
                    <td class=\"tac\"><img src=\"{$bo_img_url}/img/notice.gif\"></td>";
                    if($this->gvcomplex_lib->is_admin()) {
                        $listTb.="<td class=\"tac\"><input type=\"checkbox\" name=\"OrderNum[]\" value=\"{$row['index_no']}\"></td>";
                    }
                    $listTb.="
                    <td class=\"tal\">
                        <a href=\"{$bo_href}\"><b>{$bo_subject}</b></a>";
                        if($row['issecret'] == 'Y') { 
                            $listTb.="&nbsp;<img src='{$bo_img_url}/img/icon_secret.gif'>";
                        }
                        if($row['tailcount']) { 
                            $listTb.="&nbsp;<img src='{$bo_img_url}/img/dot_cnum.gif'>&nbsp;<span class=\"fc_197\">({$row['tailcount']})</span>";
                        }
                        $listTb.=$bo_newicon;
                    $listTb.="
                    </td>
                    <td class=\"tac\">{$row['writer_s']}</td>
                    <td class=\"tac\">{$bo_file_yes}</td>
                    <td class=\"tac\">{$row['readcount']}</td>
                    <td class=\"tac\">{$bo_wdate}</td>
                </tr>";
                $run++;
            }
            foreach($result as $key => $row){
                $bo_wdate = date("Y-m-d", $row['wdate']);
                if($row['fileurl1'] || $row['fileurl2'])
                    $bo_file_yes ="<img src='".$bo_img_url."/img/file_on.gif'>";
                else
                    $bo_file_yes ="<img src='".$bo_img_url."/img/file_off.gif'>";

                $spacer = strlen($row['thread'] != 'A');
                if($spacer > $reply_limit) {
                    $spacer = $reply_limit;
                }

                $bo_subject = "";
                for($g=0; $g<$spacer; $g++) {
                    $bo_subject .= "<img src='".$bo_img_url."/img/icon_reply.gif'>&nbsp;";
                }

                if($board['use_category'] == '1'  && $row['ca_name']) {
                    $bo_subject .= '<strong>['.$row['ca_name'].']</strong>&nbsp;';
                }

                $bo_subject .= cut_str($row['subject'], $board['list_cut']);

                if((time()-$row['wdate']) < (60*60*24))
                    $bo_newicon = "&nbsp;<img src='".$bo_img_url."/img/iconY.gif'>";
                else
                    $bo_newicon = "";

                $bo_href = base_url().'bbs/read?index_no='.$row['index_no'].'&'.$qstr1;
                $listTb.="
                <tr>
                    <td class=\"tac\">".($num--)."</td>";
                    if($this->gvcomplex_lib->is_admin()) {
                        $listTb.="<td class=\"tac\"><input type=\"checkbox\" name=\"OrderNum[]\" value=\"{$row['index_no']}\"></td>";
                    }
                    $listTb.="
                    <td class=\"tal\">
                        <a href=\"{$bo_href}\">{$bo_subject}</a>";
                        if($row['issecret'] == 'Y') { 
                            $listTb.="&nbsp;<img src=\"{$bo_img_url}/img/icon_secret.gif\">";
                        } 
                        $listTb.="&nbsp;";
                        if($row['tailcount']) {
                            $listTb.="<img src='{$bo_img_url}/img/dot_cnum.gif'>&nbsp;<span class=\"fc_197\">({$row['tailcount']})</span>";
                        }
                        $listTb.=$bo_newicon;
                    $listTb.="
                    </td>
                    <td class=\"tac\">{$row['writer_s']}</td>
                    <td class=\"tac\">{$bo_file_yes}</td>
                    <td class=\"tac\">{$row['readcount']}</td>
                    <td class=\"tac\">{$bo_wdate}</td>
                </tr>";
                $run++;
            }
            if(!$run){
                $listTb.="<tr><td colspan=\"{$colspan}\" class=\"empty_table\">게시물이 없습니다.</td></tr>";
            }
            $data['listTb']=$listTb;
            if($this->gvcomplex_lib->is_admin()) {
                $adminTb[2].="
                <div class=\"lbt_box\">
                    <input type=\"submit\" value=\"삭제\" class=\"btn_lsmall bx-white\">
                </div>";
            }
            $data['adminTb']=$adminTb;

            $writeTag="";
            if($member['grade'] <= $board['write_priv']) {
                $writeTag="
                <div class=\"rbt_box\">
                    <a href=\"".base_url()."bbs/write?boardid={$boardid}\" class=\"btn_lsmall\">글쓰기</a>
                </div>"; #차후분기처리해야함
            }
            $data['writeTag']=$writeTag;
            $gw_opt="";
            for($i=0;$i<sizeof($gw_search_value);$i++) {
                $gw_opt.="<option value='{$gw_search_value[$i]}'".get_selected($gw_search_value[$i], $sfl).">{$gw_search_text[$i]}</option>\n";
            }
            $data['gw_opt']=$gw_opt;
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$qstr2.'&page=');
            $data['pagination']=$pagination; 

            $this->load->view('pc/bbs/basic/list',$data);
        }else if($theme=="read"){
            $accept=$data['accept'];
            $queryString=$data['queryString'];
            $qstr1=$queryString['qstr1'];
            $qstr2=$queryString['qstr2'];
            $from_action_url=$data['from_action_url'];
            $bo_file1=$data['bo_file1'];
            $bo_file2=$data['bo_file2'];
            $board=$data['board'];
            $bo_memo = nl2br($write['memo']);
            $bo_writer = $write['writer'];
            $bo_writer_s = $write['writer_s'];

            $boardid=$this->input->get('boardid');
            $index_no=$this->input->get('index_no');
            $page=$this->input->get('page');
            $stx=$this->input->get('stx');
            $sfl=$this->input->get('sfl');

            $bo_file1_tag="";
            if(isset($bo_file1) && $bo_file1!=""){
                $bo_file1_tag="<tr><td>첨부파일1 : <a href=\"".base_url()."process/download?file={$bo_file1}&url=./data/board/{$boardid}/{$bo_file1}\"><b>{$bo_file1}</b></a></td></tr>";
            }
            $bo_file2_tag="";
            if(isset($bo_file2) && $bo_file2!=""){
                $bo_file2_tag="<tr><td>첨부파일2 : <a href=\"".base_url()."process/download?file={$bo_file2}&url=./data/board/{$boardid}/{$bo_file2}\"><b>{$bo_file2}</b></a></td></tr>";
            }
            $data['bo_file1_tag']=$bo_file1_tag;
            $data['bo_file2_tag']=$bo_file2_tag;
            // 픽셀 (게시판에서 출력되는 이미지의 폭 크기)
            if($board['width'] > 100) {
                $thumbnail_width = $board['width'];
            } else {
                $thumbnail_width = 730;
            }
            $width=0;
            $height=0;
            $bo_file1_img="";
            if($bo_file1 && preg_match("/\.(gif|jpg|jpeg|png)$/i", $bo_file1)){
                $file1anal = explode(".",$bo_file1);
                if(in_array($file1anal[1],$accept)){
                    if(is_file("./data/board/".$boardid."/".$bo_file1)){
                        $imgsize1 = getimagesize("./data/board/".$boardid."/".$bo_file1);
                        if($imgsize1[0] > $thumbnail_width) {
                            $width = $thumbnail_width;
                            $height = ($imgsize1[1] / $imgsize1[0]) * $thumbnail_width;
                        } else {
                            $width = $imgsize1[0];
                            $height = $imgsize1[1];
                        }
                    }
                }
                $bo_file1_img="<a href=\"javascript:imgview('./data/board/{$boardid}/{$bo_file1}');\"><img src=\"".base_url()."data/board/{$boardid}/{$bo_file1}\" width=\"{$width}\" height=\"{$height}\"></a><br><br>";
            }
            

            $bo_file2_img="";
            
            if($bo_file2 && preg_match("/\.(gif|jpg|jpeg|png)$/i", $bo_file2)){
                $file2anal = explode(".",$bo_file2);
                if(in_array($file2anal[1],$accept)){
                    if(is_file("./data/board/".$boardid."/".$bo_file2)){
                        $imgsize1 = getimagesize("./data/board/".$boardid."/".$bo_file2);
                        if($imgsize1[0] > $thumbnail_width) {
                            $width = $thumbnail_width;
                            $height = ($imgsize1[1] / $imgsize1[0]) * $thumbnail_width;
                        } else {
                            $width = $imgsize1[0];
                            $height = $imgsize1[1];
                        }       
                    }
                }
                $bo_file2_img="<a href=\"javascript:imgview('./data/board/{$boardid}/{$bo_file2}');\"><img src=\"".base_url()."data/board/{$boardid}/{$bo_file2}\" width=\"{$width}\" height=\"{$height}\"></a>";
            }
            
            $data['bo_file1_img']=$bo_file1_img;
            $data['bo_file2_img']=$bo_file2_img;
            $getViewThum=$this->thumbnail_lib->get_view_thumbnail($this->funcomplex_lib->conv_content($bo_memo, 1), $thumbnail_width);
            $data['getViewThum']=$getViewThum;
            $modifyTag="";
            if(($member['index_no'] == $bo_writer) || $this->gvcomplex_lib->is_admin()) {
                $modifyTag="<a href=\"".base_url()."bbs/modify?{$qstr2}\" class=\"btn_lsmall bx-white\">수정</a>";
            }
            $replyTag="";
            if($member['index_no'] && $member['grade']<=$board['reply_priv'] && $board['usereply']=='Y') {
                $replyTag="<a href=\"".base_url()."bbs/reply?{$qstr2}\" class=\"btn_lsmall bx-white\">답글</a>";
            }

            $delTag="";
            if(($member['index_no'] == $bo_writer) || $this->gvcomplex_lib->is_admin()) {
                $delTag="<a href=\"".base_url()."bbs/del?{$qstr2}\" class=\"btn_lsmall bx-white\">삭제</a>";
            }
            $writeTag="";
            if($member['grade'] <= $board['write_priv']){
                $writeTag="<a href=\"".base_url()."bbs/write?boardid={$boardid}\" class=\"btn_lsmall\">글쓰기</a>";
            }
            $data['modifyTag']=$modifyTag;
            $data['replyTag']=$replyTag;
            $data['delTag']=$delTag;
            $data['writeTag']=$writeTag;

            $comment="";
            if($board['usetail']=='Y') {
                $stx=$data['stx'];
                $sfl=$data['sfl'];
                $qstr2=$data['qstr2'];
                $qstr3=$data['qstr3'];
                $comment=$this->read_comment($index_no,$from_action_url,$boardid,$sfl,$stx,$page,$bo_writer_s,$qstr2,$qstr3);
            }
            $data['comment']=$comment;
            $fboardformJs="";
            if(!$is_member) {
                $fboardformJs="
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
            $data['fboardformJs']=$fboardformJs;
            $listData=array('boardid'=>$boardid,'stx'=>$stx,'sfl'=>$sfl,'page'=>$page,'bo_img_url'=>$bo_img_url,'queryString'=>$queryString);
            $read_list=$this->read_list('basic',$listData);
            $data['read_list']=$read_list;
            $data['bo_writer_s']=$bo_writer_s;
            $data['qstr1']=$qstr1;
            $this->load->view('pc/bbs/basic/read',$data);
        }else if($theme=="del"){
            $sfl=$this->input->get('sfl');
            $stx=$this->input->get('stx');
            $page=$this->input->get('page');
            $boardid=$this->input->get('boardid');
            $index_no=$this->input->get('index_no');

            $delMsg="";
            if($is_member){
                $delMsg.="
                <div class=\"del_box\">
                    한번 삭제된 데이타는 복구되지 않습니다.<br>삭제 하시려면 삭제하기 버튼을 누르세요.
                    <p class=\"mart15\">
                        <input type=\"submit\" value=\"삭제\" class=\"btn_small\">
                        <a href=\"javascript:history.go(-1);\" class=\"btn_small bx-white\">취소</a>
                    </p>
                </div>";
            }else{
                $delMsg.="
                <div class=\"del_box\">
                    한번 삭제된 데이타는 복구되지 않습니다.<br>아래 비밀번호를 입력하여 주십시오.
                    <p class=\"mart15\">
                        <input name=\"passwd\" type=\"password\" class=\"frm_input\">
                        <input type=\"submit\" value=\"삭제\" class=\"btn_small\">
                    </p>
                </div>";
            }
            $pwdJs="";
            if(!$is_member) {
                $pwdJs="
                if(!f.passwd.value) {
                    alert('비밀번호를 입력하세요.');
                    f.passwd.focus();
                    return false;
                }";
            } 
            $data['index_no']=$index_no;
            $data['boardid']=$boardid;
            $data['sfl']=$sfl;
            $data['stx']=$stx;
            $data['page']=$page;
            $data['delMsg']=$delMsg;
            $data['pwdJs']=$pwdJs;
            $this->load->view('pc/bbs/basic/del',$data);
        }else if($theme=="modify"){
            $index_no=$this->input->get('index_no');
            $boardid=$this->input->get('boardid');
            $sfl=$this->input->get('sfl');
            $stx=$this->input->get('stx');
            $page=$this->input->get('page');
            $token = get_session('ss_token');
            $nameTag="";
            if($is_member) {
				$nameTag.=$write['writer_s'];
				$nameTag.="<input type=\"hidden\" name=\"writer_s\" value=\"$write[writer_s]\">";
			} else {
				$nameTag.="<input type=\"text\" name=\"writer_s\" value=\"$write[writer_s]\" class=\"frm_input\">";
			}
            $pwdTag="";
            if(!$is_member) {
                $pwdTag="
                <tr>
                    <th scope=\"row\">비밀번호</th>
                    <td><input name=\"passwd\" type=\"password\" class='frm_input'></td>
                </tr>";
            }
            $use_categoryOne="";
            if($board['use_category'] == '1') {
                $use_categoryOne="
                <tr>
                    <th scope=\"row\">분류</th>
                    <td>
                        <select name=\"ca_name\">
                        <option value=\"\">선택하세요</option>
                        ".get_category_option($board['usecate'])."
                        </select>
                        <script>document.fboardform.ca_name.value='{$write['ca_name']}';</script>
                    </td>
                </tr>";
            }
            $option = "";
            $option_hidden = "";
            if($this->gvcomplex_lib->is_admin()) {
                $checked="";
                if($write['btype']=='1') { 
                    $checked = 'checked'; 
                }
                    $option .= "<input type=\"checkbox\" name=\"btype\" value=\"1\" $checked> 공지사항&nbsp;&nbsp;";

                $checked="";
                if($write['issecret']=='Y') { 
                    $checked = 'checked'; 
                }
                $option .= "<input type=\"checkbox\" name=\"issecret\" value=\"Y\" $checked> 비밀글";
            } else {

                switch($board['use_secret']){
                    case '0':
                        $option_hidden .= "<input type=\"hidden\" value=\"N\" name=\"issecret\">";
                        break;
                    case '1':
                        unset($checked);
                        if($write['issecret']=='Y') { $checked = 'checked'; }
                        $option .= "<input type=\"checkbox\" value=\"Y\" name=\"issecret\" $checked> 비밀글";
                        break;
                    case '2':
                        $option_hidden .= "<input type=\"hidden\" value=\"Y\" name=\"issecret\">";
                    break;
                }
            }
            $optionTag="";
            if($option) {
                $optionTag="
                <tr>
                    <th scope=\"row\">옵션</th>
                    <td>{$option}</td>
                </tr>";
            }
            $editor_memo=$this->global_lib->editor_html('memo', $this->funcomplex_lib->get_text($write['memo'], 0));

            $usefileY="";
            if($board['usefile']=='Y' ) {
                $usefileY.="
                <tr>
                    <th scope=\"row\">첨부파일1</th>
                    <td>
                        <input type=\"file\" name=\"file1\">";
                        if($write['fileurl1']) {
                            $usefileY.="
                            <a href=\"".base_url()."data/board/{$boardid}/{$write['fileurl1']}\" target=\"_blank\"><span class=\"bold fc_255\">{$write['fileurl1']}</span></a>
                            <input type=\"checkbox\" name=\"del_file1\" value=\"{$write['fileurl1']}\"> 삭제";
                        }
                    $usefileY.="
                    </td>
                </tr>
                <tr>
                    <th scope=\"row\">첨부파일2</th>
                    <td>
                        <input type=\"file\" name=\"file2\">";
                        if($write['fileurl2']) {
                            $usefileY.="
                            <a href=\"".base_url()."data/board/{$boardid}/{$write['fileurl2']}\" target=\"_blank\"><span class=\"bold fc_255\">{$write['fileurl2']}</span></a>
                            <input type=\"checkbox\" name=\"del_file2\" value=\"{$write['fileurl2']}\"> 삭제";
                        }
                    $usefileY.="
                    </td>
                </tr>";
            } 
            $isnotMemberJs="";
            if(!$is_member) {
                $isnotMemberJs="
                if(!f.writer_s.value){
                    alert('작성자명을 입력하세요.');
                    f.writer_s.focus();
                    return false;
                }
            
                if(!f.passwd.value){
                    alert('비밀번호를 입력하세요.');
                    f.passwd.focus();
                    return false;
                }";
            } 
            $use_categoryOneJs="";
            if($board['use_category'] == '1') {
                $use_categoryOneJs="
                if(!f.ca_name.value) {
                    alert('분류를 선택하세요.');
                    f.ca_name.focus();
                    return false;
                }";
            }
            $memoGetJs=$this->global_lib->get_editor_js('memo');
            $memoChkJs=$this->global_lib->chk_editor_js('memo');

            $data['index_no']=$index_no;
            $data['boardid']=$boardid;
            $data['sfl']=$sfl;
            $data['stx']=$stx;
            $data['page']=$page;
            $data['token']=$token;
            $data['nameTag']=$nameTag;
            $data['pwdTag']=$pwdTag;
            $data['use_categoryOne']=$use_categoryOne;
            $data['option_hidden']=$option_hidden;
            $data['optionTag']=$optionTag;
            $data['write']=$write;
            $data['editor_memo']=$editor_memo;
            $data['usefileY']=$usefileY;
            $data['isnotMemberJs']=$isnotMemberJs;
            $data['memoGetJs']=$memoGetJs;
            $data['memoChkJs']=$memoChkJs;
            $data['use_categoryOneJs']=$use_categoryOneJs;
            
            $this->load->view('pc/bbs/basic/modify',$data);
        }
    }
    #코맨트 공통영역
    private function read_comment($index_no,$from_action_url,$boardid,$sfl,$stx,$page,$bo_writer_s,$qstr2,$qstr3){
        $is_member=$this->load->get_var('is_member');
        $member=$this->load->get_var('member');
        $board=$this->load->get_var('board');
        $returnTag="";
        $returnTag.="
        <form name=\"fboardform\" id=\"fboardform\" method=\"post\" action=\"{$from_action_url}\" onsubmit=\"return fboardform_submit(this);\">
            <input type=\"hidden\" name=\"mode\" value=\"w\">
            <input type=\"hidden\" name=\"index_no\" value=\"{$index_no}\">
            <input type=\"hidden\" name=\"boardid\" value=\"{$boardid}\">
            <input type=\"hidden\" name=\"sfl\" value=\"{$sfl}\">
            <input type=\"hidden\" name=\"stx\" value=\"{$stx}\">
            <input type=\"hidden\" name=\"page\" value=\"{$page}\">";
        $sql = "select * from shop_board_{$boardid}_tail where board_index=? order by wdate asc";
        $param=array($index_no);
        $resObj = $this->query_model->returnQueryObj($sql,$param);
        if($resObj->num_rows()){
            $returnTag.="
            <div class=\"tbl_frm02 tbl_wrap marb10\">
                <table>
                    <tbody>";
            $res=$resObj->result_array();
            foreach($res as $key => $row){
                $bo_wdate = date("Y-m-d H:i:s",$row['wdate']);
                $returnTag.="
                <tr class=\"list1\">
                    <td>작성자 : <b>{$row['writer_s']}</b> ({$bo_wdate})";
                if($row['writer_s'] == $member['name'] || $this->gvcomplex_lib->is_admin()){
                    $returnTag.="<a href=\"".base_url()."bbs/tail_del?tailindex={$row['index_no']}&{$qstr2}\" class=\"btn_ssmall bx-white\">삭제</a>";
                }
                $returnTag.="
                    </td>
                </tr>";
                $returnTag.="
                <tr>
                    <td>".$this->funcomplex_lib->conv_content($row['memo'], 0)."</td>
                </tr>";
            }
            $returnTag.="
                    </tbody>
                </table>
            </div>";
        }
        if($is_member) {
            $returnTag.="
            <table class=\"wfull bd\">
                <tr height=\"80\" class=\"list1\">
                    <td width=\"9%\" class=\"tac bold\">{$member['name']}</td>
                    <td width=\"81%\"><textarea name=\"memo\" class=\"frm_textbox h60\"></textarea></td>
                    <td class=\"tar padr10 padl10\">";
                    if($member['grade'] > $board['tail_priv']) {
                        $returnTag.="<input type=\"button\" onclick=\"alert('댓글을 작성할 권한이 없습니다.');\" value=\"댓글입력\" class=\"btn_medium grey h60\">";
                    } else { 
                        $returnTag.="
                        <input type=\"hidden\" name=\"writer_s\" value=\"{$member['name']}\">
                        <input type=\"submit\" value=\"댓글입력\" class=\"btn_medium grey h60\">";
                    }
                    $returnTag.="
                    </td>
                </tr>
            </table>";
        }else{
            if($board['tail_priv'] == '99') {
                $returnTag.="
                <div class=\"tbl_frm01 tbl_wrap\">
                    <table>
                    <tr>
                        <td colspan=\"2\">
                            작성자 : <input type=\"text\" name=\"writer_s\" class=\"frm_input marr15\" size=\"20\">
                            비밀번호 : <input type=\"password\" name=\"passwd\" class=\"frm_input\" size=\"20\">
                        </td>
                    </tr>
                    <tr class=\"list1\">
                        <td width=\"90%\"style=\"padding:10px 0 10px 10px\"><textarea name=\"memo\" class=\"frm_textbox h60\"></textarea></td>
                        <td class=\"tar padr10 padl10\"><input type=\"submit\" value=\"댓글입력\" class=\"btn_medium grey h60\"></td>
                    </tr>
                    </table>
                </div>";
            }else{
                $returnTag.="
                <table class=\"wfull bd\">
                <tr height=\"80\" class=\"list1\">
                    <td width=\"9%\" class=\"tac bold\">{$bo_writer_s}</td>
                    <td width=\"81%\"><textarea name=\"memo\" class=\"frm_textbox h60\"></textarea></td>
                    <td class=\"tar padr10 padl10\"><input type=\"button\" onclick=\"alert('로그인후 댓글을 작성 가능합니다.');\" value=\"댓글입력\" class=\"btn_medium grey h60\"></td>
                </tr>
                </table>";
            }
        }
        $returnTag.="</form>";

        return $returnTag;
    }
    private function read_list($theme,$listData){
        $default=$this->load->get_var('default');
        $pt_id=$this->load->get_var('pt_id');
        
        $member=$this->load->get_var('member');
        $write=$this->load->get_var('write');
        $board=$this->load->get_var('board');
        $config=$this->load->get_var('config');

        $gw_search_value=$this->config->item('gw_search_value');
        $gw_search_text=$this->config->item('gw_search_text');
        $queryString=$listData['queryString'];
        $qstr1=$queryString['qstr1'];
        $qstr3=$queryString['qstr3'];
        
        $stx=addslashes($listData['stx']);
        $sfl=$listData['sfl'];
        $page=$listData['page'];
        $boardid=$listData['boardid'];
        $bo_img_url = $listData['bo_img_url'];

        $read_list_tag="";
        if($theme=="basic"){
            if($board['read_list']=='1') {
                $sql_search2 = "";
                if($default['de_board_wr_use']) {
                    $sql_search2 = " and pt_id = '$pt_id' ";
                }
            
                $sql_common = " from shop_board_{$boardid} ";
                $sql_search = " where btype = '2' {$sql_search2} ";
            
                if($sfl && $stx) {
                    $sql_search .= " and ($sfl like '%$stx%') ";
                }
            
                $sql_order = " order by fid desc, thread asc ";
            
                $sql = " select count(*) as cnt $sql_common $sql_search ";
                $row = $this->query_model->returnOneArr($sql);
                $total_count = $row['cnt'];
            
                $rows = $board['page_num'];
                $total_page = ceil($total_count / $rows); // 전체 페이지 계산
                if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
                $from_record = ($page - 1) * $rows; // 시작 열을 구함
                $num = $total_count - (($page-1)*$rows);
            
                $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
                $result = $this->query_model->returnArr($sql);
            
                $reply_limit = 6;
                $run = 0;

                $sql = " select * from shop_board_{$boardid} where btype = '1' {$sql_search2} order by fid desc ";
                $rst = $this->query_model->returnArr($sql);
                foreach($rst as $key => $row){
                    $bo_subject	= cut_str($row['subject'], $board['list_cut']);
                    $bo_wdate	= date("Y-m-d", $row['wdate']);

                    if($row['fileurl1'] || $row['fileurl2'])
                        $bo_file_yes ="<img src='".$bo_img_url."/img/file_on.gif'>";
                    else
                        $bo_file_yes ="<img src='".$bo_img_url."/img/file_off.gif'>";

                    if((time()-$row['wdate']) < (60*60*24))
                        $bo_newicon = "&nbsp;<img src='".$bo_img_url."/img/iconY.gif'>";
                    else
                        $bo_newicon = "";

                    $bo_href = base_url().'bbs/read?index_no='.$row['index_no'].'&'.$qstr1;
                    $read_list_tag.="
                    <tr>
                        <td class=\"tac\"><img src=\"{$bo_img_url}/img/notice.gif\"></td>
                        <td class=\"tal\">
                            <a href=\"{$bo_href}\"><b>{$bo_subject}</b></a>";
                            if($row['issecret'] == 'Y') { 
                                $read_list_tag.="&nbsp;<img src=\"{$bo_img_url}/img/icon_secret.gif\">";
                            }
                            if($row['tailcount']) { 
                                $read_list_tag.="&nbsp;<img src=\"{$bo_img_url}/img/dot_cnum.gif\">&nbsp;<span class=\"fc_197\">({$row['tailcount']})</span>";
                            }
                            $read_list_tag.=$bo_newicon;
                        $read_list_tag.="
                        </td>
                        <td class=\"tac\">{$row['writer_s']}</td>
                        <td class=\"tac\">{$bo_file_yes}</td>
                        <td class=\"tac\">{$row['readcount']}</td>
                        <td class=\"tac\">{$bo_wdate}</td>
                    </tr>";
                    $run++;
                }
                foreach($result as $key => $row){
                    $bo_wdate = date("Y-m-d", $row['wdate']);
                    if($row['fileurl1'] || $row['fileurl2'])
                        $bo_file_yes ="<img src='".$bo_img_url."/img/file_on.gif'>";
                    else
                        $bo_file_yes ="<img src='".$bo_img_url."/img/file_off.gif'>";
                    $spacer = strlen($row['thread'] != 'A');
                    if($spacer > $reply_limit) {
                        $spacer = $reply_limit;
                    }
                    $bo_subject = "";
                    for($g=0; $g<$spacer; $g++) {
                        $bo_subject .= "<img src='".$bo_img_url."/img/icon_reply.gif'>&nbsp;";
                    }
                    if($board['use_category'] == '1'  && $row['ca_name']) {
                        $bo_subject .= '<strong>['.$row['ca_name'].']</strong>&nbsp;';
                    }
                    $bo_subject .= cut_str($row['subject'], $board['list_cut']);
                    if((time()-$row['wdate']) < (60*60*24))
                        $bo_newicon = "&nbsp;<img src='".$bo_img_url."/img/iconY.gif'>";
                    else
                        $bo_newicon = "";

                    $bo_href = base_url().'bbs/read?index_no='.$row['index_no'].'&'.$qstr1;
                    $read_list_tag.="
                    <tr>
                        <td class=\"tac\">".($num--)."</td>
                        <td class=\"tal\">
                            <a href=\"{$bo_href}\">{$bo_subject}</a>";
                            if($row['issecret'] == 'Y') { 
                                $read_list_tag.="&nbsp;<img src=\"{$bo_img_url}/img/icon_secret.gif\">";
                            }
                            $read_list_tag.="&nbsp;";
                            if($row['tailcount']) {
                                $read_list_tag.="<img src=\"{$bo_img_url}/img/dot_cnum.gif\">&nbsp;<span class=\"fc_197\">({$row['tailcount']})</span>";
                            }
                            $read_list_tag.=$bo_newicon;
                            $read_list_tag.="
                        </td>
                        <td class=\"tac\">{$row['writer_s']}</td>
                        <td class=\"tac\">{$bo_file_yes}</td>
                        <td class=\"tac\">{$row['readcount']}</td>
                        <td class=\"tac\">{$bo_wdate}</td>
                    </tr>";
                    $run++;
                }
                if(!$run) {
                    $read_list_tag="<tr><td colspan=\"6\" class=\"empty_table\">게시물이 없습니다.</td></tr>";
                }
                $writeTag="";
                if($member['grade'] <= $board['write_priv']) {
                    $writeTag="
                    <div class=\"rbt_box\">
                        <a href=\"".base_url()."bbs/write?boardid={$boardid}\" class=\"btn_lsmall\">글쓰기</a>
                    </div>";
                }
                $gw_search_opt="";
                for($i=0;$i<sizeof($gw_search_value);$i++) {
                    $gw_search_opt.="<option value='{$gw_search_value[$i]}'".get_selected($gw_search_value[$i], $sfl).">{$gw_search_text[$i]}</option>\n";
                }
                $pagination=get_paging($config['write_pages'], $page, $total_page, base_url().'bbs/noticeList?'.$qstr3.'&page=');
                $data=array('read_list_tag'=>$read_list_tag,'qstr1'=>$qstr1,'writeTag'=>$writeTag,'boardid'=>$boardid,'gw_search_opt'=>$gw_search_opt,'stx'=>$stx,
                            'pagination'=>$pagination);
                return $this->load->view('pc/bbs/basic/read_listOne',$data,TRUE);
            }else if($board['read_list']=='2'){
                $sql = "select * from shop_board_{$boardid} where wdate > '{$write['wdate']}'";
                if($sfl && $stx)
                    $sql .= " and $sfl like '%$stx%'";
                $sql .= " order by wdate asc limit 0,1";
                $resObj = $this->query_model->returnQueryObj($sql);
                $preveTag="";
                if($resObj->num_rows()){
                    $row=$this->query_model->returnOneArr($sql);
                    $prev_no = $row['index_no'];
                    $prev_subject = $row['subject'];
                    $prev_tailcount = $row['tailcount'];
                    $prev_href = base_url()."bbs/read?index_no=$prev_no&boardid=$boardid$qstr&page=$page";
                    $prevTag="
                    <tr>
                        <th width=\"15%\">▲ 이전 글</th>
                        <td width=\"85%\"><a href=\"{$prev_href}\">{$prev_subject}[{$prev_tailcount}]</a></td>
                    </tr>";
                }
                $sql = "select * from shop_board_{$boardid} where wdate < '{$write['wdate']}'";
                if($sfl && $stx)
                    $sql .= " and $sfl like '%$stx%'";
                $sql .= " order by wdate asc limit 0,1";
                $nextTag="";
                $res = $this->query_model->returnQueryObj($sql);
                if($res->num_rows()){
                    $row = $this->query_model->returnOneArr($sql);
                    $next_no = $row['index_no'];
                    $next_subject = $row['subject'];
                    $next_tailcount = $row['tailcount'];
                    $next_href = base_url()."bbs/read?index_no=$next_no&boardid=$boardid$qstr&page=$page";
                    $nextTag.="
                    <tr>
                        <th width=\"15%\">▼ 다음 글</th>
                        <td width=\"85%\"><a href=\"{$next_href}\">{$next_subject}[{$next_tailcount}]</a></td>
                    </tr>";
                }

                $data=array('prevTag'=>$prevTag,'nextTag'=>$nextTag);
                return $this->load->view('pc/template/read_listTwo',$data,TRUE);
            }
        }else if($theme=="gallery"){
            if($board['read_list']=='1') {
                $sql_search2 = "";
                if($default['de_board_wr_use']) { 
                    $sql_search2 = " and pt_id = '$pt_id' ";
                }
            
                $sql_common = " from shop_board_{$boardid} ";
                $sql_search = " where btype = '2' {$sql_search2} ";
            
                if($sfl && $stx) {
                    $sql_search .= " and ($sfl like '%$stx%') ";
                }
            
                $sql_order  = " order by fid desc, thread asc ";
            
                $sql = " select count(*) as cnt $sql_common $sql_search ";
                $row = $this->query_model->returnOneArr($sql);
                $total_count = $row['cnt'];
            
                $rows = $board['page_num'];
                $total_page = ceil($total_count / $rows); // 전체 페이지 계산
                if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
                $from_record = ($page - 1) * $rows; // 시작 열을 구함
                $num = $total_count - (($page-1)*$rows);
            
                $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
                $result = $this->query_model->returnArr($sql);
            
                $reply_limit = 6;
                $run = 0;
                $sql = " select * from shop_board_{$boardid} where btype = '1' {$sql_search2} order by fid desc ";
                $rst = $this->query_model->returnArr($sql);
                foreach($rst as $key => $row){
                    $bo_subject	= cut_str($row['subject'], $board['list_cut']);
                    $bo_wdate	= date("Y-m-d", $row['wdate']);

                    if((time()-$row['wdate']) < (60*60*24))
                        $bo_newicon = "&nbsp;<img src='".$bo_img_url."/img/iconY.gif'>";
                    else
                        $bo_newicon = "";

                    $bo_href = base_url().'bbs/read?index_no='.$row['index_no'].'&'.$qstr1;

                    $thumb = $this->thumbnail_lib->get_list_thumbnail($boardid, $row, 120, 0);
                    if(!$thumb['src']) {
                        $thumb['src'] = base_url().'img/noimage.gif';
                    }
                    if(!$thumb['alt']) {
                        $thumb['alt'] = "썸네일";
                    }
                    $read_list_tag.="
                    <dl>
                        <a href=\"{$bo_href}\">
                        <dt><img src=\"{$thumb['src']}\" alt=\"{$thumb['alt']}\"></dt>
                        <dd class=\"bo_tit\">
                            <p class=\"bo_notice\">공지</p> {$bo_subject}";
                            if($row['issecret'] == 'Y') { 
                                $read_list_tag.="&nbsp;<img src=\"{$bo_img_url}/img/icon_secret.gif\">";
                            }
                            $read_list_tag.=$bo_newicon;
                            $read_list_tag.="
                        </dd>
                        <dd>{$row['writer_s']}<span>{$bo_wdate}</span><span>조회 : {$row['readcount']}</span></dd>
                        </a>
                    </dl>";
            		$run++;
                }
                foreach($result as $key => $row){
                    $bo_wdate = date("Y-m-d", $row['wdate']);
                    $spacer = strlen($row['thread'] != 'A');
                    if($spacer > $reply_limit) {
                        $spacer = $reply_limit;
                    }

                    $bo_subject = "";
                    for($g=0; $g<$spacer; $g++) {
                        $bo_subject .= "<img src='".$bo_img_url."/img/icon_reply.gif'>&nbsp;";
                    }

                    if($board['use_category'] == '1'  && $row['ca_name']) {
                        $bo_subject .= '<strong>['.$row['ca_name'].']</strong>&nbsp;';
                    }

                    $bo_subject .= cut_str($row['subject'], $board['list_cut']);

                    if((time()-$row['wdate']) < (60*60*24))
                        $bo_newicon = "&nbsp;<img src='".$bo_img_url."/img/iconY.gif'>";
                    else
                        $bo_newicon = "";

                    $bo_href = base_url().'bbs/read?index_no='.$row['index_no'].'&'.$qstr1;

                    $thumb = $this->thumbnail_lib->get_list_thumbnail($boardid, $row, 120, 0);
                    if(!isset($thumb['src'])) {
                        $thumb['src'] = base_url().'img/noimage.gif';
                    }
                    if(!isset($thumb['alt'])) {
                        $thumb['alt'] = "썸네일";
                    }
                    $read_list_tag.="
                    <dl>
                        <a href=\"{$bo_href}\">
                        <dt><p><img src=\"{$thumb['src']}\" alt=\"{$thumb['alt']}\"></p></dt>
                        <dd class=\"bo_tit\">";
                        $read_list_tag.=$bo_subject;
                            if($row['issecret'] == 'Y') {
                                $read_list_tag.="&nbsp;<img src=\"{$bo_img_url}/img/icon_secret.gif\">";
                            }
                        $read_list_tag.= $bo_newicon;
                        $read_list_tag.="
                        </dd>
                        <dd>{$row['writer_s']}<span>{$bo_wdate}</span><span>조회 : {$row['readcount']}</span></dd>
                        </a>
                    </dl>";
                    $run++;
                }
                if(!$run){
                    $read_list_tag='<p class="empty_list">게시물이 없습니다.</p>';
                }
                $writeTag="";
                if($member['grade'] <= $board['write_priv']) {
                    $writeTag="
                    <div class=\"rbt_box\">		
                        <a href=\"".base_url()."bbs/write?boardid={$boardid}\" class=\"btn_lsmall\">글쓰기</a>		
                    </div>";
                }
                $gw_search_opt="";
                for($i=0;$i<sizeof($gw_search_value);$i++) {
                    $gw_search_opt.="<option value='{$gw_search_value[$i]}'".get_selected($gw_search_value[$i], $sfl).">{$gw_search_text[$i]}</option>\n";
                }

                $pagination=get_paging($config['write_pages'], $page, $total_page, base_url().'bbs/noticeList?'.$qstr3.'&page=');
                
                $data=array('read_list_tag'=>$read_list_tag,'qstr1'=>$qstr1,'writeTag'=>$writeTag,'boardid'=>$boardid,'gw_search_opt'=>$gw_search_opt,'stx'=>$stx,
                            'pagination'=>$pagination);
                return $this->load->view('pc/bbs/gallery/read_listOne',$data,TRUE);
            }else if($board['read_list']=='2'){
                $sql = "select * from shop_board_{$boardid} where wdate > '{$write['wdate']}' {$sql_search2}";
                if($sfl && $stx)
                    $sql .= " and $sfl like '%$stx%'";
                $sql .= " order by wdate asc limit 0,1";
                $res = $this->query_model->returnQueryObj($sql);
                $prevTag="";
                if($res->num_rows()) {
                    $row = $res->result_array();
                    $prev_no = $row['index_no'];
                    $prev_subject = $row['subject'];
                    $prev_tailcount = $row['tailcount'];
                    $prev_href = base_url()."bbs/read?index_no=$prev_no&boardid=$boardid$qstr&page=$page";
                    $prevTag="
                    <tr>
                        <th width=\"15%\">▲ 이전 글</th>
                        <td width=\"85%\"><a href=\"{$prev_href}\">{$prev_subject} [{$prev_tailcount}]</a></td>
                    </tr>";
                }

                $sql = "select * from shop_board_{$boardid} where wdate < '{$write['wdate']}' {$sql_search2}";
                if($sfl && $stx)
                    $sql .= " and $sfl like '%$stx%'";
                $sql .= " order by wdate asc limit 0,1";
                $res = $this->query_model->returnQueryObj($sql);
                $nextTag="";
                if($res->num_rows()) {
                    $row = $res->result_array();
                    $next_no = $row['index_no'];
                    $next_subject = $row['subject'];
                    $next_tailcount = $row['tailcount'];
                    $next_href = base_url()."bbs/read?index_no=$next_no&boardid=$boardid$qstr&page=$page";
                    $nextTag="
                    <tr>
                        <th width=\"15%\">▼ 다음 글</th>
                        <td width=\"85%\"><a href=\"{$next_href}\">{$next_subject} [{$next_tailcount}]</a></td>
                    </tr>";
                }

                $data=array('prevTag'=>$prevTag,'nextTag'=>$nextTag);
                return $this->load->view('pc/template/read_listTwo',$data,TRUE);
            }
        }else if($theme=="webzine"){
            if($board['read_list']=='1') {
                $sql_search2 = "";
                if($default['de_board_wr_use']) { 
                    $sql_search2 = " and pt_id = '$pt_id' ";
                }
            
                $sql_common = " from shop_board_{$boardid} ";
                $sql_search = " where btype = '2' {$sql_search2} ";
            
                if($sfl && $stx) {
                    $sql_search .= " and ($sfl like '%$stx%') ";
                }
            
                $sql_order  = " order by fid desc, thread asc ";
            
                $sql = " select count(*) as cnt $sql_common $sql_search ";
                $row = $this->query_model->returnOneArr($sql);
                $total_count = $row['cnt'];
            
                $rows = $board['page_num'];
                $total_page = ceil($total_count / $rows); // 전체 페이지 계산
                if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
                $from_record = ($page - 1) * $rows; // 시작 열을 구함
                $num = $total_count - (($page-1)*$rows);
            
                $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
                $result = $this->query_model->returnArr($sql);
            
                $reply_limit = 6;
                $run = 0;
                $sql = " select * from shop_board_{$boardid} where btype = '1' {$sql_search2} order by fid desc ";
                $rst = $this->query_model->returnArr($sql);
                foreach($rst as $key => $row){
                    $bo_subject	= cut_str($row['subject'], $board['list_cut']);
                    $bo_wdate	= date("Y-m-d", $row['wdate']);

                    if((time()-$row['wdate']) < (60*60*24))
                        $bo_newicon = "&nbsp;<img src='".$bo_img_url."/img/iconY.gif'>";
                    else
                        $bo_newicon = "";

                    $bo_href = base_url().'bbs/read?index_no='.$row['index_no'].'&'.$qstr1;

                    $thumb = $this->thumbnail_lib->get_list_thumbnail($boardid, $row, 120, 0);
                    if(!$thumb['src']) {
                        $thumb['src'] = base_url().'img/noimage.gif';
                    }
                    if(!$thumb['alt']) {
                        $thumb['alt'] = "썸네일";
                    }
                    $read_list_tag.="
                    <dl>
                        <a href=\"{$bo_href}\">
                        <dt><p><img src=\"{$thumb['src']}\" alt=\"{$thumb['alt']}\"></p></dt>
                        <dd class=\"bo_tit\">
                            <p class=\"bo_notice\">공지</p> {$bo_subject}";
                            if($row['issecret'] == 'Y') { 
                                $read_list_tag.="&nbsp;<img src=\"{$bo_img_url}/img/icon_secret.gif\">";
                            }
                            $read_list_tag.=$bo_newicon;
                            $read_list_tag.="
                        </dd>
                        <dd>{$row['writer_s']}<span>{$bo_wdate}</span><span>조회 : {$row['readcount']}</span></dd>
                        <dd class=\"bo_txt\">".strip_tags($row['memo'])."</dd>
                        </a>
                    </dl>";
            		$run++;
                }
                foreach($result as $key => $row){
                    $bo_wdate = date("Y-m-d", $row['wdate']);
                    $spacer = strlen($row['thread'] != 'A');
                    if($spacer > $reply_limit) {
                        $spacer = $reply_limit;
                    }

                    $bo_subject = "";
                    for($g=0; $g<$spacer; $g++) {
                        $bo_subject .= "<img src='".$bo_img_url."/img/icon_reply.gif'>&nbsp;";
                    }

                    if($board['use_category'] == '1'  && $row['ca_name']) {
                        $bo_subject .= '<strong>['.$row['ca_name'].']</strong>&nbsp;';
                    }

                    $bo_subject .= cut_str($row['subject'], $board['list_cut']);

                    if((time()-$row['wdate']) < (60*60*24))
                        $bo_newicon = "&nbsp;<img src='".$bo_img_url."/img/iconY.gif'>";
                    else
                        $bo_newicon = "";

                    $bo_href = base_url().'bbs/read?index_no='.$row['index_no'].'&'.$qstr1;

                    $thumb = $this->thumbnail_lib->get_list_thumbnail($boardid, $row, 120, 0);
                    if(!isset($thumb['src'])) {
                        $thumb['src'] = base_url().'img/noimage.gif';
                    }
                    if(!isset($thumb['alt'])) {
                        $thumb['alt'] = "썸네일";
                    }
                    $read_list_tag.="
                    <dl>
                        <a href=\"{$bo_href}\">
                        <dt><p><img src=\"{$thumb['src']}\" alt=\"{$thumb['alt']}\"></p></dt>
                        <dd class=\"bo_tit\">";
                        $read_list_tag.=$bo_subject;
                            if($row['issecret'] == 'Y') {
                                $read_list_tag.="&nbsp;<img src=\"{$bo_img_url}/img/icon_secret.gif\">";
                            }
                        $read_list_tag.= $bo_newicon;
                        $read_list_tag.="
                        </dd>
                        <dd>{$row['writer_s']}<span>{$bo_wdate}</span><span>조회 : {$row['readcount']}</span></dd>
                        <dd class=\"bo_txt\">".strip_tags($row['memo'])."</dd>
                        </a>
                    </dl>";
                    $run++;
                }
                if(!$run){
                    $read_list_tag='<p class="empty_list">게시물이 없습니다.</p>';
                }
                $writeTag="";
                if($member['grade'] <= $board['write_priv']) {
                    $writeTag="
                    <div class=\"rbt_box\">		
                        <a href=\"".base_url()."bbs/write?boardid={$boardid}\" class=\"btn_lsmall\">글쓰기</a>		
                    </div>";
                }
                $gw_search_opt="";
                for($i=0;$i<sizeof($gw_search_value);$i++) {
                    $gw_search_opt.="<option value='{$gw_search_value[$i]}'".get_selected($gw_search_value[$i], $sfl).">{$gw_search_text[$i]}</option>\n";
                }

                $pagination=get_paging($config['write_pages'], $page, $total_page, base_url().'bbs/noticeList?'.$qstr3.'&page=');
                
                $data=array('read_list_tag'=>$read_list_tag,'qstr1'=>$qstr1,'writeTag'=>$writeTag,'boardid'=>$boardid,'gw_search_opt'=>$gw_search_opt,'stx'=>$stx,
                            'pagination'=>$pagination);
                return $this->load->view('pc/bbs/webzine/read_listOne',$data,TRUE);
            }else if($board['read_list']=='2'){
                $sql = "select * from shop_board_{$boardid} where wdate > '{$write['wdate']}' {$sql_search2}";
                if($sfl && $stx)
                    $sql .= " and $sfl like '%$stx%'";
                $sql .= " order by wdate asc limit 0,1";
                $res = $this->query_model->returnQueryObj($sql);
                $prevTag="";
                if($res->num_rows()) {
                    $row = $res->result_array();
                    $prev_no = $row['index_no'];
                    $prev_subject = $row['subject'];
                    $prev_tailcount = $row['tailcount'];
                    $prev_href = base_url()."bbs/read?index_no=$prev_no&boardid=$boardid$qstr&page=$page";
                    $prevTag="
                    <tr>
                        <th width=\"15%\">▲ 이전 글</th>
                        <td width=\"85%\"><a href=\"{$prev_href}\">{$prev_subject} [{$prev_tailcount}]</a></td>
                    </tr>";
                }

                $sql = "select * from shop_board_{$boardid} where wdate < '{$write['wdate']}' {$sql_search2}";
                if($sfl && $stx)
                    $sql .= " and $sfl like '%$stx%'";
                $sql .= " order by wdate asc limit 0,1";
                $res = $this->query_model->returnQueryObj($sql);
                $nextTag="";
                if($res->num_rows()) {
                    $row = $res->result_array();
                    $next_no = $row['index_no'];
                    $next_subject = $row['subject'];
                    $next_tailcount = $row['tailcount'];
                    $next_href = base_url()."bbs/read?index_no=$next_no&boardid=$boardid$qstr&page=$page";
                    $nextTag="
                    <tr>
                        <th width=\"15%\">▼ 다음 글</th>
                        <td width=\"85%\"><a href=\"{$next_href}\">{$next_subject} [{$next_tailcount}]</a></td>
                    </tr>";
                }

                $data=array('prevTag'=>$prevTag,'nextTag'=>$nextTag);
                return $this->load->view('pc/template/read_listTwo',$data,TRUE);
            }
        }
    }
    public function gallery($data,$theme){
        $board=$this->load->get_var('board');
        $default=$this->load->get_var('default');
        $pt_id=$this->load->get_var('pt_id');
        $member=$this->load->get_var('member');
        $is_member=$this->load->get_var('is_member');
        $member['index_no']=isset($member['index_no'])?$member['index_no']:"";
        $config=$this->load->get_var('config');
        $write=$this->load->get_var('write');
        $bo_img_url=$data['bo_img_url'];
        $page=$this->input->get('page');
        if($theme=="list"){
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $usecate = explode("|", $board['usecate']);
            $gw_search_value=$this->config->item('gw_search_value');
            $gw_search_text=$this->config->item('gw_search_text');
            
            $qstr=$this->load->get_var('qstr');

            $ca_name=$this->input->get('ca_name');
            $sfl=$this->input->get('sfl');
            $stx=addslashes($this->input->get('stx'));
            $page=$this->input->get('page');
            $boardid=$this->input->get('boardid');

            
            $usecate = explode("|", $board['usecate']);
            $sql_search2 = "";
            if($default['de_board_wr_use']) {
                $sql_search2 = " and pt_id = '$pt_id' ";
            }

            $sql_common = " from shop_board_{$boardid} ";
            $sql_search = " where btype = '2' {$sql_search2} ";
            if($ca_name)
                $sql_search .= " and ca_name='{$ca_name}' ";

            if($sfl && $stx) {
                $sql_search .= " and ($sfl like '%$stx%') ";
            }

            $sql_order  = " order by fid desc, thread asc ";

            $sql = " select count(*) as cnt $sql_common $sql_search ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            $rows = $board['page_num'];
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);

            $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
            $result = $this->query_model->returnQueryObj($sql);

            $reply_limit = 6;
            $run = 0;

            $qstr1 = "boardid=$boardid$qstr&page=$page";
            $qstr2 = "boardid=$boardid$qstr";

            $useCate="";
            if($board['use_category']) {
                $useCate.="
                <ul class=\"bo_cate\">
                    <li".((!$ca_name)?'class="active"':'')."><a href=\"".base_url()."bbs/noticeList?{$qstr1}\">전체</a></li>";
                    for($i=0; $i<count($usecate); $i++) {
                        $active = '';
                        if($ca_name == $usecate[$i])
                            $active = ' class="active"';
                        $useCate.="<li{$active}><a href=\"".base_url()."bbs/noticeList?{$qstr1}&ca_name={$usecate[$i]}\">{$usecate[$i]}</a></li>";
                    }
                $useCate.="
                </ul>";
            }
            $is_admin=array('','');
            if($this->gvcomplex_lib->is_admin()) {
                $is_admin[0]="
                <input type=\"checkbox\" name=\"chkall\" value=\"1\" id=\"chkall\" onclick=\"check_all(this.form);\">
                <label for=\"chkall\" class=\"marr10 bold\">전체선택</label>";
                $is_admin[1]="	
                <div class=\"lbt_box\">
                    <input type=\"submit\" value=\"삭제\" class=\"btn_lsmall bx-white\">
                </div>";
            }
            #galleryTb 겔러리 테이블
            $sql = " select * from shop_board_{$boardid} where btype = '1' {$sql_search2} order by fid desc ";
	        $rst = $this->query_model->returnArr($sql);
            $galleryTb="";
            foreach($rst as $key => $row){
                $bo_subject	= cut_str($row['subject'], $board['list_cut']);
                $bo_wdate	= date("Y-m-d", $row['wdate']);
        
                if((time()-$row['wdate']) < (60*60*24))
                    $bo_newicon = "&nbsp;<img src='".$bo_img_url."/img/iconY.gif'>";
                else
                    $bo_newicon = "";
        
                $bo_href = base_url().'bbs/read?index_no='.$row['index_no'].'&'.$qstr1;
        
                $thumb = $this->thumbnail_lib->get_list_thumbnail($boardid, $row, 252, 0);
                if(!$thumb['src']) {
                    $thumb['src'] = base_url().'img/noimage.gif';
                }
                if(!$thumb['alt']) {
                    $thumb['alt'] = "썸네일";
                }
                $galleryTb.="
                <dl>
                    <a href=\"{$bo_href}\">
                    <dt><img src=\"{$thumb['src']}\" alt=\"{$thumb['alt']}\"></dt>
                    <dd class=\"bo_tit\">
                        <p class=\"bo_notice\">공지</p> {$bo_subject}";
                        if($row['issecret'] == 'Y') { 
                            $galleryTb.="&nbsp;<img src=\"{$bo_img_url}/img/icon_secret.gif\">";
                        }
                        $galleryTb.=$bo_newicon;
                    $galleryTb.="
                    </dd>
                    <dd>{$row['writer_s']}<span>{$bo_wdate}</span><span>조회 : {$row['readcount']}</span></dd>
                    </a>";
                    if($this->gvcomplex_lib->is_admin()) { 
                        $galleryTb.="<dd class=\"bo_chk\"><input type=\"checkbox\" name=\"OrderNum[]\" value=\"{$row['index_no']}\"></dd>";
                    }
                $galleryTb.="
                </dl>";
                $run++;
            }
            foreach($result as $key => $row){
                $bo_wdate = date("Y-m-d", $row['wdate']);
                $spacer = strlen($row['thread'] != 'A');
                if($spacer > $reply_limit) {
                    $spacer = $reply_limit;
                }
                $bo_subject = "";
                for($g=0; $g<$spacer; $g++) {
                    $bo_subject .= "<img src='".$bo_img_url."/img/icon_reply.gif'>&nbsp;";
                }

                if($board['use_category'] == '1'  && $row['ca_name']) {
                    $bo_subject .= '<strong>['.$row['ca_name'].']</strong>&nbsp;';
                }

                $bo_subject .= cut_str($row['subject'], $board['list_cut']);

                if((time()-$row['wdate']) < (60*60*24))
                    $bo_newicon = "&nbsp;<img src='".$bo_img_url."/img/iconY.gif'>";
                else
                    $bo_newicon = "";

                $bo_href = base_url().'bbs/read?index_no='.$row['index_no'].'&'.$qstr1;

                $thumb =$this->thumbnail_lib->get_list_thumbnail($boardid, $row, 252, 0);
                if(!$thumb['src']) {
                    $thumb['src'] = base_url().'img/noimage.gif';
		        }
                if(!$thumb['alt']) {
                    $thumb['alt'] = "썸네일";
		        }
                $galleryTb.="
                <dl>
                    <a href=\"{$bo_href}\">
                    <dt><img src=\"{$thumb['src']}\" alt=\"{$thumb['alt']}\"></dt>
                    <dd class=\"bo_tit\">{$bo_subject}";
                    if($row['issecret'] == 'Y') { 
                        $galleryTb.="&nbsp;<img src=\"{$bo_img_url}/img/icon_secret.gif\">";
                    }
                    $galleryTb.=$bo_newicon; 
                    $galleryTb.="
                    </dd>
                    <dd>{$row['writer_s']}<span>{$bo_wdate}</span><span>조회 : {$row['readcount']}</span></dd>
                    </a>";
                    if($this->gvcomplex_lib->is_admin()) {
                        $galleryTb.="<dd class=\"bo_chk\"><input type=\"checkbox\" name=\"OrderNum[]\" value=\"{$row['index_no']}\"></dd>";
                    }
                $galleryTb.="
                </dl>";
                $run++;
            }
            if(!$run){
                $galleryTb='<p class="empty_list">게시물이 없습니다.</p>';
            }
            $writeTag="";
            if($member['grade'] <= $board['write_priv']) {
                $writeTag="
                <div class=\"rbt_box\">
                    <a href=\"".base_url()."bbs/write?boardid={$boardid}\" class=\"btn_lsmall\">글쓰기</a>
                </div>";
            }
            $gw_search_opt="";
            for($i=0;$i<sizeof($gw_search_value);$i++) {
                $gw_search_opt.="<option value='{$gw_search_value[$i]}'".get_selected($gw_search_value[$i], $sfl).">{$gw_search_text[$i]}</option>\n";
            }
            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$qstr2.'&page=');

            $data=array('page'=>$page,'boardid'=>$boardid,'sfl'=>$sfl,'stx'=>$stx,'pagination'=>$pagination,'useCate'=>$useCate,'is_admin'=>$is_admin,'total_count'=>$total_count,
                        'galleryTb'=>$galleryTb,'writeTag'=>$writeTag,'gw_search_opt'=>$gw_search_opt);
            $this->load->view('pc/bbs/gallery/list',$data);
        }else if($theme=="read"){
            $accept=$data['accept'];
            $queryString=$data['queryString'];
            $qstr1=$queryString['qstr1'];
            $qstr2=$queryString['qstr2'];
            $from_action_url=$data['from_action_url'];
            $bo_file1=$data['bo_file1'];
            $bo_file2=$data['bo_file2'];
            $board=$data['board'];
            $bo_memo = nl2br($write['memo']);
            $bo_writer = $write['writer'];
            $bo_writer_s = $write['writer_s'];

            $boardid=$this->input->get('boardid');
            $index_no=$this->input->get('index_no');

            $bo_file1Tag="";
            $data['qstr1']=$qstr1;
            if(isset($bo_file1) && $bo_file1!="") {
                $bo_file1Tag="
                <tr>
                    <td>첨부파일1 : <a href=\"".base_url()."process/download?file={$bo_file1}&url=./data/board/{$boardid}/{$bo_file1}\"><b>{$bo_file1}</b></a></td>
                </tr>";
            }

            $bo_file2Tag="";
            if(isset($bo_file2) && $bo_file2!="") {
                $bo_file2Tag="
                <tr>
                    <td>첨부파일1 : <a href=\"".base_url()."process/download?file={$bo_file2}&url=./data/board/{$boardid}/{$bo_file2}\"><b>{$bo_file2}</b></a></td>
                </tr>";
            }
            $data['bo_file1Tag']=$bo_file1Tag;
            $data['bo_file2Tag']=$bo_file2Tag;
            // 픽셀 (게시판에서 출력되는 이미지의 폭 크기)
            if($board['width'] > 100) {
                $thumbnail_width = $board['width'];
            } else {
                $thumbnail_width = 730;
            }
            $width=0;
            $height=0;
            $bo_file1Tag="";
            if($bo_file1 && preg_match("/\.(gif|jpg|jpeg|png)$/i", $bo_file1)){
                $file1anal = explode(".",$bo_file1);
                if(in_array($file1anal[1],$accept)){
                    if(is_file("./data/board/".$boardid."/".$bo_file1)){
                        $imgsize1 = getimagesize("./data/board/".$boardid."/".$bo_file1);
                        if($imgsize1[0] > $thumbnail_width) {
                            $width = $thumbnail_width;
                            $height = ($imgsize1[1] / $imgsize1[0]) * $thumbnail_width;
                        } else {
                            $width = $imgsize1[0];
                            $height = $imgsize1[1];
                        }
                    }
                    $bo_file1Tag="<a href=\"javascript:imgview('./data/board/{$boardid}/{$bo_file1}; ?>');\"><img src=\"".base_url()."data/board/{$boardid}/{$bo_file1}\" width=\"{$width}\" height=\"{$height}\"></a><br><br>";
                }
            }
            $width=0;
            $height=0;
            $bo_file2Tag="";
            if($bo_file2 && preg_match("/\.(gif|jpg|jpeg|png)$/i", $bo_file2)){
                $file1anal = explode(".",$bo_file2);
                if(in_array($file1anal[1],$accept)){
                    if(is_file("./data/board/".$boardid."/".$bo_file1)){
                        $imgsize1 = getimagesize("./data/board/".$boardid."/".$bo_file2);
                        if($imgsize1[0] > $thumbnail_width) {
                            $width = $thumbnail_width;
                            $height = ($imgsize1[1] / $imgsize1[0]) * $thumbnail_width;
                        } else {
                            $width = $imgsize1[0];
                            $height = $imgsize1[1];
                        }
                    }
                    $bo_file2Tag="<a href=\"javascript:imgview('./data/board/{$boardid}/{$bo_file2}; ?>');\"><img src=\"".base_url()."data/board/{$boardid}/{$bo_file2}\" width=\"{$width}\" height=\"{$height}\"></a><br><br>";
                }
            }
            $data['bo_file1Tag']=$bo_file1Tag;
            $data['bo_file2Tag']=$bo_file2Tag;
            $thumb=$this->thumbnail_lib->get_view_thumbnail($this->funcomplex_lib->conv_content($bo_memo, 1), $thumbnail_width);
            $data['thumb']=$thumb;
            $webModify="";
            if(($member['index_no'] == $bo_writer) || $this->gvcomplex_lib->is_admin()) {
                $webModify="<a href=\"".base_url()."bbs/modify?{$qstr2}\" class=\"btn_lsmall bx-white\">수정</a>";
            }
            $data['webModify']=$webModify;

            $webReply="";
            if($member['index_no'] && $member['grade']<=$board['reply_priv'] && $board['usereply']=='Y') {  
                $webReply="<a href=\"".base_url()."bbs/reply?{$qstr2}\" class=\"btn_lsmall bx-white\">답글</a>";
            }
            $data['webReply']=$webReply;

            $webDel="";
            if(($member['index_no'] == $bo_writer) || $this->gvcomplex_lib->is_admin()) {
                $webDel="<a href=\"".base_url()."bbs/del?{$qstr2}\" class=\"btn_lsmall bx-white\">삭제</a>";
            }
            $data['webDel']=$webDel;

            $webWrite="";
            if($member['grade'] <= $board['write_priv']){
                $webWrite="<a href=\"".base_url()."bbs/write?boardid={$boardid}\" class=\"btn_lsmall\">글쓰기</a>";
            } 
            $data['webWrite']=$webWrite;

            $webComment="";
            if($board['usetail']=='Y') {
                $stx=addslashes($data['stx']);
                $sfl=$data['sfl'];
                $qstr2=$data['qstr2'];
                $qstr3=$data['qstr3'];
                $webComment=$this->read_comment($index_no,$from_action_url,$boardid,$sfl,$stx,$page,$bo_writer_s,$qstr2,$qstr3);
            }
            $data['webComment']=$webComment;
            $fboardformJs="";
            if(!$is_member) {
                $fboardformJs="
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
            $data['fboardformJs']=$fboardformJs;
            $listData=array('boardid'=>$boardid,'stx'=>$stx,'sfl'=>$sfl,'page'=>$page,'bo_img_url'=>$bo_img_url,'queryString'=>$queryString);
            $read_list=$this->read_list('gallery',$listData);
            $data['read_list']=$read_list;
            $data['bo_writer_s']=$bo_writer_s;
            $data['qstr1']=$qstr1;

            $this->load->view('pc/bbs/gallery/read',$data);
        }else if($theme=="del"){
            $sfl=$this->input->get('sfl');
            $stx=$this->input->get('stx');
            $page=$this->input->get('page');
            $boardid=$this->input->get('boardid');
            $index_no=$this->input->get('index_no');

            $delMsg="";
            if($is_member){
                $delMsg.="
                <div class=\"del_box\">
                    한번 삭제된 데이타는 복구되지 않습니다.<br>삭제 하시려면 삭제하기 버튼을 누르세요.
                    <p class=\"mart15\">
                        <input type=\"submit\" value=\"삭제\" class=\"btn_small\">
                        <a href=\"javascript:history.go(-1);\" class=\"btn_small bx-white\">취소</a>
                    </p>
                </div>";
            }else{
                $delMsg.="
                <div class=\"del_box\">
                    한번 삭제된 데이타는 복구되지 않습니다.<br>아래 비밀번호를 입력하여 주십시오.
                    <p class=\"mart15\">
                        <input name=\"passwd\" type=\"password\" class=\"frm_input\">
                        <input type=\"submit\" value=\"삭제\" class=\"btn_small\">
                    </p>
                </div>";
            }
            $pwdJs="";
            if(!$is_member) {
                $pwdJs="
                if(!f.passwd.value) {
                    alert('비밀번호를 입력하세요.');
                    f.passwd.focus();
                    return false;
                }";
            } 
            $data['index_no']=$index_no;
            $data['boardid']=$boardid;
            $data['sfl']=$sfl;
            $data['stx']=$stx;
            $data['page']=$page;
            $data['delMsg']=$delMsg;
            $data['pwdJs']=$pwdJs;
            $this->load->view('pc/bbs/gallery/basic/del',$data);
        }else if($theme=="modify"){
            $index_no=$this->input->get('index_no');
            $boardid=$this->input->get('boardid');
            $sfl=$this->input->get('sfl');
            $stx=$this->input->get('stx');
            $page=$this->input->get('page');
            $token = get_session('ss_token');
            $nameTag="";
            if($is_member) {
				$nameTag.=$write['writer_s'];
				$nameTag.="<input type=\"hidden\" name=\"writer_s\" value=\"$write[writer_s]\">";
			} else {
				$nameTag.="<input type=\"text\" name=\"writer_s\" value=\"$write[writer_s]\" class=\"frm_input\">";
			}
            $pwdTag="";
            if(!$is_member) {
                $pwdTag="
                <tr>
                    <th scope=\"row\">비밀번호</th>
                    <td><input name=\"passwd\" type=\"password\" class='frm_input'></td>
                </tr>";
            }
            $use_categoryOne="";
            if($board['use_category'] == '1') {
                $use_categoryOne="
                <tr>
                    <th scope=\"row\">분류</th>
                    <td>
                        <select name=\"ca_name\">
                        <option value=\"\">선택하세요</option>
                        ".get_category_option($board['usecate'])."
                        </select>
                        <script>document.fboardform.ca_name.value='{$write['ca_name']}';</script>
                    </td>
                </tr>";
            }
            $option = "";
            $option_hidden = "";
            if($this->gvcomplex_lib->is_admin()) {
                $checked="";
                if($write['btype']=='1') { 
                    $checked = 'checked'; 
                }
                    $option .= "<input type=\"checkbox\" name=\"btype\" value=\"1\" $checked> 공지사항&nbsp;&nbsp;";

                $checked="";
                if($write['issecret']=='Y') { 
                    $checked = 'checked'; 
                }
                $option .= "<input type=\"checkbox\" name=\"issecret\" value=\"Y\" $checked> 비밀글";
            } else {

                switch($board['use_secret']){
                    case '0':
                        $option_hidden .= "<input type=\"hidden\" value=\"N\" name=\"issecret\">";
                        break;
                    case '1':
                        unset($checked);
                        if($write['issecret']=='Y') { $checked = 'checked'; }
                        $option .= "<input type=\"checkbox\" value=\"Y\" name=\"issecret\" $checked> 비밀글";
                        break;
                    case '2':
                        $option_hidden .= "<input type=\"hidden\" value=\"Y\" name=\"issecret\">";
                    break;
                }
            }
            $optionTag="";
            if($option) {
                $optionTag="
                <tr>
                    <th scope=\"row\">옵션</th>
                    <td>{$option}</td>
                </tr>";
            }
            $editor_memo=$this->global_lib->editor_html('memo', $this->funcomplex_lib->get_text($write['memo'], 0));

            $usefileY="";
            if($board['usefile']=='Y' ) {
                $usefileY.="
                <tr>
                    <th scope=\"row\">첨부파일1</th>
                    <td>
                        <input type=\"file\" name=\"file1\">";
                        if($write['fileurl1']) {
                            $usefileY.="
                            <a href=\"".base_url()."data/board/{$boardid}/{$write['fileurl1']}\" target=\"_blank\"><span class=\"bold fc_255\">{$write['fileurl1']}</span></a>
                            <input type=\"checkbox\" name=\"del_file1\" value=\"{$write['fileurl1']}\"> 삭제";
                        }
                    $usefileY.="
                    </td>
                </tr>
                <tr>
                    <th scope=\"row\">첨부파일2</th>
                    <td>
                        <input type=\"file\" name=\"file2\">";
                        if($write['fileurl2']) {
                            $usefileY.="
                            <a href=\"".base_url()."data/board/{$boardid}/{$write['fileurl2']}\" target=\"_blank\"><span class=\"bold fc_255\">{$write['fileurl2']}</span></a>
                            <input type=\"checkbox\" name=\"del_file2\" value=\"{$write['fileurl2']}\"> 삭제";
                        }
                    $usefileY.="
                    </td>
                </tr>";
            } 
            $isnotMemberJs="";
            if(!$is_member) {
                $isnotMemberJs="
                if(!f.writer_s.value){
                    alert('작성자명을 입력하세요.');
                    f.writer_s.focus();
                    return false;
                }
            
                if(!f.passwd.value){
                    alert('비밀번호를 입력하세요.');
                    f.passwd.focus();
                    return false;
                }";
            } 
            $use_categoryOneJs="";
            if($board['use_category'] == '1') {
                $use_categoryOneJs="
                if(!f.ca_name.value) {
                    alert('분류를 선택하세요.');
                    f.ca_name.focus();
                    return false;
                }";
            }
            $memoGetJs=$this->global_lib->get_editor_js('memo');
            $memoChkJs=$this->global_lib->chk_editor_js('memo');

            $data['index_no']=$index_no;
            $data['boardid']=$boardid;
            $data['sfl']=$sfl;
            $data['stx']=$stx;
            $data['page']=$page;
            $data['token']=$token;
            $data['nameTag']=$nameTag;
            $data['pwdTag']=$pwdTag;
            $data['use_categoryOne']=$use_categoryOne;
            $data['option_hidden']=$option_hidden;
            $data['optionTag']=$optionTag;
            $data['write']=$write;
            $data['editor_memo']=$editor_memo;
            $data['usefileY']=$usefileY;
            $data['isnotMemberJs']=$isnotMemberJs;
            $data['memoGetJs']=$memoGetJs;
            $data['memoChkJs']=$memoChkJs;
            $data['use_categoryOneJs']=$use_categoryOneJs;
            
            $this->load->view('pc/bbs/gallery/modify',$data);
        }
    }
    #webzine페이지(갤러리 게시판)
    public function webzine($data,$theme){
        $board=$this->load->get_var('board');
        $default=$this->load->get_var('default');
        $pt_id=$this->load->get_var('pt_id');
        $member=$this->load->get_var('member');
        $is_member=$this->load->get_var('is_member');
        $member['index_no']=isset($member['index_no'])?$member['index_no']:"";
        $config=$this->load->get_var('config');
        $write=$this->load->get_var('write');
        $bo_img_url=$data['bo_img_url'];
        $page=$this->input->get('page');
        if($theme=="list"){
            $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
            $usecate = explode("|", $board['usecate']);
            $gw_search_value=$this->config->item('gw_search_value');
            $gw_search_text=$this->config->item('gw_search_text');
            
            $qstr=$this->load->get_var('qstr');

            $ca_name=$this->input->get('ca_name');
            $sfl=$this->input->get('sfl');
            $stx= addslashes($this->input->get('stx'));
            $page=$this->input->get('page');
            $boardid=$this->input->get('boardid');

            
            $usecate = explode("|", $board['usecate']);
            $sql_search2 = "";
            if($default['de_board_wr_use']) {
                $sql_search2 = " and pt_id = '$pt_id' ";
            }

            $sql_common = " from shop_board_{$boardid} ";
            $sql_search = " where btype = '2' {$sql_search2} ";
            if($ca_name)
                $sql_search .= " and ca_name='{$ca_name}' ";

            if($sfl && $stx) {
                $sql_search .= " and ($sfl like '%$stx%') ";
            }

            $sql_order  = " order by fid desc, thread asc ";

            $sql = " select count(*) as cnt $sql_common $sql_search ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            $rows = $board['page_num'];
            $total_page = ceil($total_count / $rows); // 전체 페이지 계산
            if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
            $from_record = ($page - 1) * $rows; // 시작 열을 구함
            $num = $total_count - (($page-1)*$rows);

            $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
            $result = $this->query_model->returnQueryObj($sql);

            $reply_limit = 6;
            $run = 0;

            $qstr1 = "boardid=$boardid$qstr&page=$page";
            $qstr2 = "boardid=$boardid$qstr";

            $useCate="";
            if($board['use_category']) {
                $useCate.="
                <ul class=\"bo_cate\">
                    <li".((!$ca_name)?'class="active"':'')."><a href=\"".base_url()."bbs/noticeList?{$qstr1}\">전체</a></li>";
                    for($i=0; $i<count($usecate); $i++) {
                        $active = '';
                        if($ca_name == $usecate[$i])
                            $active = ' class="active"';
                        $useCate.="<li{$active}><a href=\"".base_url()."bbs/noticeList?{$qstr1}&ca_name={$usecate[$i]}\">{$usecate[$i]}</a></li>";
                    }
                $useCate.="
                </ul>";
            }
            $is_admin=array('','');
            if($this->gvcomplex_lib->is_admin()) {
                $is_admin[0]="
                <input type=\"checkbox\" name=\"chkall\" value=\"1\" id=\"chkall\" onclick=\"check_all(this.form);\">
                <label for=\"chkall\" class=\"marr10 bold\">전체선택</label>";
                $is_admin[1]="	
                <div class=\"lbt_box\">
                    <input type=\"submit\" value=\"삭제\" class=\"btn_lsmall bx-white\">
                </div>";
            }

            #webzine테이블만들기
            $webzineTb="";
            $sql = " select * from shop_board_{$boardid} where btype = '1' {$sql_search2} order by fid desc ";
            $rst = $this->query_model->returnArr($sql);
            foreach($rst as $key => $row){
                $bo_subject	= cut_str($row['subject'], $board['list_cut']);
                $bo_wdate	= date("Y-m-d", $row['wdate']);

                if((time()-$row['wdate']) < (60*60*24))
                    $bo_newicon = "&nbsp;<img src='".$bo_img_url."/img/iconY.gif'>";
                else
                    $bo_newicon = "";

                $bo_href = base_url().'bbs/read?index_no='.$row['index_no'].'&'.$qstr1;

                $thumb = $this->thumbnail_lib->get_list_thumbnail($boardid, $row, 120, 0);
                if(!$thumb['src']) {
                    $thumb['src'] = base_url().'img/noimage.gif';
                }
                $webzineTb.="
                <dl>
                    <a href=\"{$bo_href}\">
                        <dt><p><img src=\"{$thumb['src']}\" alt=\"{$thumb['alt']}\"></p></dt>
                        <dd class=\"bo_tit\">
                            <p class=\"bo_notice\">공지</p> {$bo_subject}";
                            if($row['issecret'] == 'Y') { 
                                $webzineTb.="&nbsp;<img src=\"{$bo_img_url}/img/icon_secret.gif\">";
                            }
                            $webzineTb.=$bo_newicon;
                            $webzineTb.="
                        </dd>
                        <dd>{$row['writer_s']}<span>{$bo_wdate}</span><span>조회 : {$row['readcount']}</span></dd>
                        <dd class=\"bo_txt\">".strip_tags($row['memo'])."</dd>
                    </a>";
                    if($this->gvcomplex_lib->is_admin()) {
                        $webzineTb.="
                        <dd class=\"bo_chk\"><input type=\"checkbox\" name=\"OrderNum[]\" value=\"{$row['index_no']}\"></dd>";
                    }
                    $webzineTb.="
                </dl>";
                $run++;
            }
            $result=$result->result_array();
            foreach($result as $key => $row){
                $bo_wdate = date("Y-m-d", $row['wdate']);

                $spacer = strlen($row['thread'] != 'A');
                if($spacer > $reply_limit) {
                    $spacer = $reply_limit;
                }
        
                $bo_subject = "";
                for($g=0; $g<$spacer; $g++) {
                    $bo_subject .= "<img src='".$bo_img_url."/img/icon_reply.gif'>&nbsp;";
                }
        
                if($board['use_category'] == '1'  && $row['ca_name']) {
                    $bo_subject .= '<strong>['.$row['ca_name'].']</strong>&nbsp;';
                }
        
                $bo_subject .= cut_str($row['subject'], $board['list_cut']);
        
                if((time()-$row['wdate']) < (60*60*24))
                    $bo_newicon = "&nbsp;<img src='".$bo_img_url."/img/iconY.gif'>";
                else
                    $bo_newicon = "";
        
                $bo_href = base_url().'bbs/read?index_no='.$row['index_no'].'&'.$qstr1;
        
                $thumb = $this->thumbnail_lib->get_list_thumbnail($boardid, $row, 120, 0);
                if(!$thumb['src']) {
                    $thumb['src'] = base_url().'img/noimage.gif';
                }
                if(!isset($thumb['alt'])) {
                    $thumb['alt'] = '썸네일';
                }
                
                $webzineTb.="
                <dl>
                    <a href=\"{$bo_href}\">
                        <dt><p><img src=\"{$thumb['src']}\" alt=\"{$thumb['alt']}\"></p></dt>
                        <dd class=\"bo_tit\">
                            {$bo_subject}";
                            if($row['issecret'] == 'Y') { 
                                $webzineTb.="&nbsp;<img src=\"{$bo_img_url}/img/icon_secret.gif\">";
                            }
                            $webzineTb.=$bo_newicon;
                            $webzineTb.="
                        </dd>
                        <dd>{$row['writer_s']}<span>{$bo_wdate}</span><span>조회 : {$row['readcount']}</span></dd>
                        <dd class=\"bo_txt\">".strip_tags($row['memo'])."</dd>
                    </a>";
                    if($this->gvcomplex_lib->is_admin()) {
                        $webzineTb.="
                        <dd class=\"bo_chk\"><input type=\"checkbox\" name=\"OrderNum[]\" value=\"{$row['index_no']}\"></dd>";
                    }
                $webzineTb.="
                </dl>";
                $run++;
            }
            if(!$run){
                $webzineTb='<p class="empty_list">게시물이 없습니다.</p>';
            }
            $writeTag="";
            if($member['grade'] <= $board['write_priv']) {
                $writeTag="
                <div class=\"rbt_box\">
                    <a href=\"".base_url()."bbs/write?boardid={$boardid}\" class=\"btn_lsmall\">글쓰기</a>
                </div>";
            }
            $gw_search_opt="";
            for($i=0;$i<sizeof($gw_search_value);$i++) {
                $gw_search_opt.="<option value='{$gw_search_value[$i]}'".get_selected($gw_search_value[$i], $sfl).">{$gw_search_text[$i]}</option>\n";
            }


            $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?'.$qstr2.'&page=');


            $data=array('page'=>$page,'boardid'=>$boardid,'sfl'=>$sfl,'stx'=>$stx,'pagination'=>$pagination,'useCate'=>$useCate,'is_admin'=>$is_admin,'total_count'=>$total_count,
                        'webzineTb'=>$webzineTb,'writeTag'=>$writeTag,'gw_search_opt'=>$gw_search_opt);
            $this->load->view('pc/bbs/webzine/list',$data);
        }else if($theme=="read"){
            $accept=$data['accept'];
            $queryString=$data['queryString'];
            $qstr1=$queryString['qstr1'];
            $qstr2=$queryString['qstr2'];
            $from_action_url=$data['from_action_url'];
            $bo_file1=$data['bo_file1'];
            $bo_file2=$data['bo_file2'];
            $board=$data['board'];
            $bo_memo = nl2br($write['memo']);
            $bo_writer = $write['writer'];
            $bo_writer_s = $write['writer_s'];

            $boardid=$this->input->get('boardid');
            $index_no=$this->input->get('index_no');

            $bo_file1Tag="";
            $data['qstr1']=$qstr1;
            if(isset($bo_file1) && $bo_file1!="") {
                $bo_file1Tag="
                <tr>
                    <td>첨부파일1 : <a href=\"".base_url()."process/download?file={$bo_file1}&url=./data/board/{$boardid}/{$bo_file1}\"><b>{$bo_file1}</b></a></td>
                </tr>";
            }

            $bo_file2Tag="";
            if(isset($bo_file2) && $bo_file2!="") {
                $bo_file2Tag="
                <tr>
                    <td>첨부파일1 : <a href=\"".base_url()."process/download?file={$bo_file2}&url=./data/board/{$boardid}/{$bo_file2}\"><b>{$bo_file2}</b></a></td>
                </tr>";
            }
            $data['bo_file1Tag']=$bo_file1Tag;
            $data['bo_file2Tag']=$bo_file2Tag;
            // 픽셀 (게시판에서 출력되는 이미지의 폭 크기)
            if($board['width'] > 100) {
                $thumbnail_width = $board['width'];
            } else {
                $thumbnail_width = 730;
            }
            $width=0;
            $height=0;
            $bo_file1Str="";
            if($bo_file1 && preg_match("/\.(gif|jpg|jpeg|png)$/i", $bo_file1)){
                $file1anal = explode(".",$bo_file1);
                if(in_array($file1anal[1],$accept)){
                    if(is_file("./data/board/".$boardid."/".$bo_file1)){
                        $imgsize1 = getimagesize("./data/board/".$boardid."/".$bo_file1);
                        if($imgsize1[0] > $thumbnail_width) {
                            $width = $thumbnail_width;
                            $height = ($imgsize1[1] / $imgsize1[0]) * $thumbnail_width;
                        } else {
                            $width = $imgsize1[0];
                            $height = $imgsize1[1];
                        }
                    }
                    $bo_file1Str="<a href=\"javascript:imgview('./data/board/{$boardid}/{$bo_file1}; ?>');\"><img src=\"".base_url()."data/board/{$boardid}/{$bo_file1}\" width=\"{$width}\" height=\"{$height}\"></a><br><br>";
                }
            }
            $width=0;
            $height=0;
            $bo_file2Str="";
            if($bo_file2 && preg_match("/\.(gif|jpg|jpeg|png)$/i", $bo_file2)){
                $file1anal = explode(".",$bo_file2);
                if(in_array($file1anal[1],$accept)){
                    if(is_file("./data/board/".$boardid."/".$bo_file1)){
                        $imgsize1 = getimagesize("./data/board/".$boardid."/".$bo_file2);
                        if($imgsize1[0] > $thumbnail_width) {
                            $width = $thumbnail_width;
                            $height = ($imgsize1[1] / $imgsize1[0]) * $thumbnail_width;
                        } else {
                            $width = $imgsize1[0];
                            $height = $imgsize1[1];
                        }
                    }
                    $bo_file2Str="<a href=\"javascript:imgview('./data/board/{$boardid}/{$bo_file2}; ?>');\"><img src=\"".base_url()."data/board/{$boardid}/{$bo_file2}\" width=\"{$width}\" height=\"{$height}\"></a><br><br>";
                }
            }
            $data['bo_file1Str']=$bo_file1Str;
            $data['bo_file2Str']=$bo_file2Str;
            $thumb=$this->thumbnail_lib->get_view_thumbnail($this->funcomplex_lib->conv_content($bo_memo, 1), $thumbnail_width);
            $data['thumb']=$thumb;
            $webModify="";
            if(($member['index_no'] == $bo_writer) || $this->gvcomplex_lib->is_admin()) {
                $webModify="<a href=\"".base_url()."bbs/modify?{$qstr2}\" class=\"btn_lsmall bx-white\">수정</a>";
            }
            $data['webModify']=$webModify;

            $webReply="";
            if($member['index_no'] && $member['grade']<=$board['reply_priv'] && $board['usereply']=='Y') {  
                $webReply="<a href=\"".base_url()."bbs/reply?{$qstr2}\" class=\"btn_lsmall bx-white\">답글</a>";
            }
            $data['webReply']=$webReply;

            $webDel="";
            if(($member['index_no'] == $bo_writer) || $this->gvcomplex_lib->is_admin()) {
                $webDel="<a href=\"".base_url()."bbs/del?{$qstr2}\" class=\"btn_lsmall bx-white\">삭제</a>";
            }
            $data['webDel']=$webDel;

            $webWrite="";
            if($member['grade'] <= $board['write_priv']){
                $webWrite="<a href=\"".base_url()."bbs/write?boardid={$boardid}\" class=\"btn_lsmall\">글쓰기</a>";
            } 
            $data['webWrite']=$webWrite;

            $webComment="";
            if($board['usetail']=='Y') {
                $stx=$data['stx'];
                $sfl=$data['sfl'];

                $qstr2=$data['qstr2'];
                $qstr3=$data['qstr3'];

                $webComment=$this->read_comment($index_no,$from_action_url,$boardid,$sfl,$stx,$page,$bo_writer_s,$qstr2,$qstr3);
            }
            $data['webComment']=$webComment;
            $fboardformJs="";
            if(!$is_member) {
                $fboardformJs="
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
            $data['fboardformJs']=$fboardformJs;
            $listData=array('boardid'=>$boardid,'stx'=>$stx,'sfl'=>$sfl,'page'=>$page,'bo_img_url'=>$bo_img_url,'queryString'=>$queryString);
            $read_list=$this->read_list('webzine',$listData);
            $data['read_list']=$read_list;
            $data['bo_writer_s']=$bo_writer_s;
            $data['qstr1']=$qstr1;
            $this->load->view('pc/bbs/webzine/read',$data);
        }else if($theme=="del"){
            $sfl=$this->input->get('sfl');
            $stx=$this->input->get('stx');
            $page=$this->input->get('page');
            $boardid=$this->input->get('boardid');
            $index_no=$this->input->get('index_no');

            $delMsg="";
            if($is_member){
                $delMsg.="
                <div class=\"del_box\">
                    한번 삭제된 데이타는 복구되지 않습니다.<br>삭제 하시려면 삭제하기 버튼을 누르세요.
                    <p class=\"mart15\">
                        <input type=\"submit\" value=\"삭제\" class=\"btn_small\">
                        <a href=\"javascript:history.go(-1);\" class=\"btn_small bx-white\">취소</a>
                    </p>
                </div>";
            }else{
                $delMsg.="
                <div class=\"del_box\">
                    한번 삭제된 데이타는 복구되지 않습니다.<br>아래 비밀번호를 입력하여 주십시오.
                    <p class=\"mart15\">
                        <input name=\"passwd\" type=\"password\" class=\"frm_input\">
                        <input type=\"submit\" value=\"삭제\" class=\"btn_small\">
                    </p>
                </div>";
            }
            $pwdJs="";
            if(!$is_member) {
                $pwdJs="
                if(!f.passwd.value) {
                    alert('비밀번호를 입력하세요.');
                    f.passwd.focus();
                    return false;
                }";
            } 
            $data['index_no']=$index_no;
            $data['boardid']=$boardid;
            $data['sfl']=$sfl;
            $data['stx']=$stx;
            $data['page']=$page;
            $data['delMsg']=$delMsg;
            $data['pwdJs']=$pwdJs;
            $this->load->view('pc/bbs/webzine/basic/del',$data);
        }else if($theme=="modify"){
            $index_no=$this->input->get('index_no');
            $boardid=$this->input->get('boardid');
            $sfl=$this->input->get('sfl');
            $stx=$this->input->get('stx');
            $page=$this->input->get('page');
            $token = get_session('ss_token');
            $nameTag="";
            if($is_member) {
				$nameTag.=$write['writer_s'];
				$nameTag.="<input type=\"hidden\" name=\"writer_s\" value=\"$write[writer_s]\">";
			} else {
				$nameTag.="<input type=\"text\" name=\"writer_s\" value=\"$write[writer_s]\" class=\"frm_input\">";
			}
            $pwdTag="";
            if(!$is_member) {
                $pwdTag="
                <tr>
                    <th scope=\"row\">비밀번호</th>
                    <td><input name=\"passwd\" type=\"password\" class='frm_input'></td>
                </tr>";
            }
            $use_categoryOne="";
            if($board['use_category'] == '1') {
                $use_categoryOne="
                <tr>
                    <th scope=\"row\">분류</th>
                    <td>
                        <select name=\"ca_name\">
                        <option value=\"\">선택하세요</option>
                        ".get_category_option($board['usecate'])."
                        </select>
                        <script>document.fboardform.ca_name.value='{$write['ca_name']}';</script>
                    </td>
                </tr>";
            }
            $option = "";
            $option_hidden = "";
            if($this->gvcomplex_lib->is_admin()) {
                $checked="";
                if($write['btype']=='1') { 
                    $checked = 'checked'; 
                }
                    $option .= "<input type=\"checkbox\" name=\"btype\" value=\"1\" $checked> 공지사항&nbsp;&nbsp;";

                $checked="";
                if($write['issecret']=='Y') { 
                    $checked = 'checked'; 
                }
                $option .= "<input type=\"checkbox\" name=\"issecret\" value=\"Y\" $checked> 비밀글";
            } else {

                switch($board['use_secret']){
                    case '0':
                        $option_hidden .= "<input type=\"hidden\" value=\"N\" name=\"issecret\">";
                        break;
                    case '1':
                        unset($checked);
                        if($write['issecret']=='Y') { $checked = 'checked'; }
                        $option .= "<input type=\"checkbox\" value=\"Y\" name=\"issecret\" $checked> 비밀글";
                        break;
                    case '2':
                        $option_hidden .= "<input type=\"hidden\" value=\"Y\" name=\"issecret\">";
                    break;
                }
            }
            $optionTag="";
            if($option) {
                $optionTag="
                <tr>
                    <th scope=\"row\">옵션</th>
                    <td>{$option}</td>
                </tr>";
            }
            $editor_memo=$this->global_lib->editor_html('memo', $this->funcomplex_lib->get_text($write['memo'], 0));

            $usefileY="";
            if($board['usefile']=='Y' ) {
                $usefileY.="
                <tr>
                    <th scope=\"row\">첨부파일1</th>
                    <td>
                        <input type=\"file\" name=\"file1\">";
                        if($write['fileurl1']) {
                            $usefileY.="
                            <a href=\"".base_url()."data/board/{$boardid}/{$write['fileurl1']}\" target=\"_blank\"><span class=\"bold fc_255\">{$write['fileurl1']}</span></a>
                            <input type=\"checkbox\" name=\"del_file1\" value=\"{$write['fileurl1']}\"> 삭제";
                        }
                    $usefileY.="
                    </td>
                </tr>
                <tr>
                    <th scope=\"row\">첨부파일2</th>
                    <td>
                        <input type=\"file\" name=\"file2\">";
                        if($write['fileurl2']) {
                            $usefileY.="
                            <a href=\"".base_url()."data/board/{$boardid}/{$write['fileurl2']}\" target=\"_blank\"><span class=\"bold fc_255\">{$write['fileurl2']}</span></a>
                            <input type=\"checkbox\" name=\"del_file2\" value=\"{$write['fileurl2']}\"> 삭제";
                        }
                    $usefileY.="
                    </td>
                </tr>";
            } 
            $isnotMemberJs="";
            if(!$is_member) {
                $isnotMemberJs="
                if(!f.writer_s.value){
                    alert('작성자명을 입력하세요.');
                    f.writer_s.focus();
                    return false;
                }
            
                if(!f.passwd.value){
                    alert('비밀번호를 입력하세요.');
                    f.passwd.focus();
                    return false;
                }";
            } 
            $use_categoryOneJs="";
            if($board['use_category'] == '1') {
                $use_categoryOneJs="
                if(!f.ca_name.value) {
                    alert('분류를 선택하세요.');
                    f.ca_name.focus();
                    return false;
                }";
            }
            $memoGetJs=$this->global_lib->get_editor_js('memo');
            $memoChkJs=$this->global_lib->chk_editor_js('memo');

            $data['index_no']=$index_no;
            $data['boardid']=$boardid;
            $data['sfl']=$sfl;
            $data['stx']=$stx;
            $data['page']=$page;
            $data['token']=$token;
            $data['nameTag']=$nameTag;
            $data['pwdTag']=$pwdTag;
            $data['use_categoryOne']=$use_categoryOne;
            $data['option_hidden']=$option_hidden;
            $data['optionTag']=$optionTag;
            $data['write']=$write;
            $data['editor_memo']=$editor_memo;
            $data['usefileY']=$usefileY;
            $data['isnotMemberJs']=$isnotMemberJs;
            $data['memoGetJs']=$memoGetJs;
            $data['memoChkJs']=$memoChkJs;
            $data['use_categoryOneJs']=$use_categoryOneJs;
            
            $this->load->view('pc/bbs/webzine/modify',$data);
        }

    }
    #qna_list 질응답
    public function qna_list(){
        if((!isset($_SESSION['device']) || $_SESSION['device']!="pc") && $this->agent->is_mobile()) {
            goto_url(base_url().'m/bbs/qna_list');
        }
        $this->template->header();
        $this->qna_listSkin();
        $this->template->footer();
    }
    private function qna_listSkin(){
        $requestUrl=explode('?',$_SERVER['REQUEST_URI'])[0];
        
        $config=$this->load->get_var('config');
        $member=$this->load->get_var('member');

        $page=$this->input->get('page');

        $title="1:1 상담문의";
        $sql_common = " from shop_qa ";
        $sql_search = " where mb_id = '$member[id]' ";
        $sql_order  = " order by wdate desc ";
        
        $sql = " select count(*) as cnt $sql_common $sql_search ";
        $row = $this->query_model->returnOneArr($sql);
        $total_count = $row['cnt'];
        
        $rows = 30;
        $total_page = ceil($total_count / $rows); // 전체 페이지 계산
        if($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
        $from_record = ($page - 1) * $rows; // 시작 열을 구함
        $num = $total_count - (($page-1)*$rows);
        
        $sql = " select * $sql_common $sql_search $sql_order limit $from_record, $rows ";
        $result = $this->query_model->returnArr($sql);
        $aside_cs=$this->template->aside_cs();
        $qna_table=$this->qna_table($result,$num);

        $pagination=get_paging($config['write_pages'], $page, $total_page, $requestUrl.'?page=');

        $data=array('title'=>$title,'aside_cs'=>$aside_cs,'qna_table'=>$qna_table,'pagination'=>$pagination);
        $this->load->view('pc/bbs/qna_list',$data);
    }
    private function qna_table($result,$num){
        $returnTb="";
        $i=0;
        foreach($result as $key => $row){
            $bg = 'list'.($i%2);
            $returnTb.="<tr class=\"{$bg}\">
                <td class=\"tac\">".($num--)."</td>
                <td class=\"tac\">{$row['catename']}</td>
                <td><a href=\"".base_url()."bbs/qna_read?index_no={$row['index_no']}\">".cut_str($row['subject'],60)."</a></td>
                <td>{$row['mb_id']}</td>
                <td class=\"tac\">".substr($row['wdate'],0,10)."</td>
                <td class=\"tac\">";
                if($row['result_yes']) {
                    $returnTb.="<a href=\"javascript:js_qna('{$i}');\" class=\"fc_197 tu\">답변보기</a>";
                } else {
                    $returnTb.="답변대기";
                }
                $returnTb.="
                </td>
            </tr>
            <tr id=\"sod_qa_con_{$i}\" class=\"sod_qa_con\" style=\"display:none;\">
                <td class=\"tal\" colspan=\"6\">".nl2br(htmlspecialchars($row['reply']))."</td>
            </tr>";
            $i++;
        }
        if($i==0){
            $returnTb='<tr><td colspan="6" class="empty_list">내역이 없습니다.</td></tr>';
        }
        return $returnTb;
    }
    #qna_write 상담문의 작성하기
    public function qna_write(){
        $is_member=$this->load->get_var('is_member');
        $urlencode=$this->load->get_var('urlencode');
        if(!$is_member) {
            goto_url(base_url().'bbs/login?url='.$urlencode);
        }
        $this->template->header();
        $this->qna_writeSkin();
        $this->template->footer();
    }
    private function qna_writeSkin(){
        $member=$this->load->get_var('member');
        $title="1:1 상담문의";
        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        $aside_cs=$this->template->aside_cs();

        $form_action_url = base_url().'process/qna_write';

        $sql = "select * from shop_qa_cate where isuse='Y'";
        $res = $this->query_model->returnArr($sql);
        $selectOpt="";
        foreach($res as $key => $row){
            $selectOpt.="<option value='$row[catename]'>$row[catename]</option>\n";
        }
        
        $data=array('title'=>$title,'form_action_url'=>$form_action_url,'aside_cs'=>$aside_cs,'token'=>$token,'selectOpt'=>$selectOpt,'member'=>$member);
        $this->load->view('pc/bbs/qna_write',$data);
    }
    #qna_read 상담문의 답변읽기 화면
    public function qna_read(){
        $is_member=$this->load->get_var('is_member');
        $member=$this->load->get_var('member');
        $index_no=$this->input->get('index_no');

        if(!$is_member) {
            alert("로그인 후 이용 가능합니다.");
        }
        $sql="select * from shop_qa where index_no=?";
        $preData=array($index_no);
        $row = $this->query_model->returnOneArr($sql,$preData);
        if(!$this->gvcomplex_lib->is_admin() && $row['mb_id'] != $member['id'])
            alert("잘못된 접근입니다.");
        $this->template->header();
        $this->qna_readSkin();
        $this->template->footer();
    }
    private function qna_readSkin(){
        $index_no=$this->input->get('index_no');
        $title="1:1 상담문의";

        $sql="select * from shop_qa where index_no=?";
        $preData=array($index_no);
        $qa=$this->query_model->returnOneArr($sql,$preData);
        $aside_cs=$this->template->aside_cs();
        $result_yes="";
        if($qa['result_yes']) {
            $result_yes="
            <h3 class=\"anc_tit mart30\">답변내용</h3>
            <div class=\"tbl_frm01 tbl_wrap\">
                <table>
                <colgroup>
                    <col class=\"w100\">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope=\"row\">답변일</th>
                    <td>".substr($qa['result_date'],0,10)."</td>
                </tr>
                <tr>
                    <th scope=\"row\">답변내용</th>
                    <td style=\"height:150px;vertical-align:top;\">".nl2br(htmlspecialchars($qa['reply']))."</td>
                </tr>
                </tbody>
                </table>
            </div>";
        }

        $data=array('title'=>$title,'aside_cs'=>$aside_cs,'qa'=>$qa,'result_yes'=>$result_yes,'index_no'=>$index_no);
        $this->load->view('pc/bbs/qna_read',$data);
    }
    #qna_modify 상담 수정하기 화면
    public function qna_modify(){
        $is_member=$this->load->get_var('is_member');
        if(!$is_member) {
            alert("로그인 후 이용 가능합니다.");
        }
        $this->template->header();
        $this->qna_modifySkin();
        $this->template->footer();
    }
    private function qna_modifySkin(){
        $member=$this->load->get_var('member');

        $index_no=$this->input->get('index_no');

        $title="1:1 상담문의";
        $sql="select * from shop_qa where index_no=?";
        $preData=array($index_no);
        $qa=$this->query_model->returnOneArr($sql,$preData);
        if($qa['mb_id'] != $member['id']) {
            if(!$this->gvcomplex_lib->is_admin()) {
                alert('게시글을 수정할 권한이 없습니다.');
            }
        }

        $token = md5(uniqid(rand(), true));
        set_session("ss_token", $token);
        $form_action_url = base_url().'process/qna_modify?index_no='.$index_no;
        $aside_cs=$this->template->aside_cs();
        $sql = "select * from shop_qa_cate where isuse='Y'";
        $res = $this->query_model->returnArr($sql);
        $selectOpt="";
        foreach($res as $key => $row){
            $selectOpt.="<option ".get_selected($qa['catename'],$row['catename'])." value='$row[catename]'>$row[catename]</option>\n";
        }
        $emailYes=get_checked($qa['email_send_yes'],'1');
        $emailNo=get_checked($qa['email_send_yes'],'0');

        $smsYes=get_checked($qa['sms_send_yes'],'1');
        $smsNo=get_checked($qa['sms_send_yes'],'0');
        $data=array('title'=>$title,'token'=>$token,'form_action_url'=>$form_action_url,'aside_cs'=>$aside_cs,'index_no'=>$index_no,'selectOpt'=>$selectOpt,
                    'qa'=>$qa,'emailYes'=>$emailYes,'emailNo'=>$emailNo,'smsYes'=>$smsYes,'smsNo'=>$smsNo);

        $this->load->view('pc/bbs/qna_modify',$data);
    }
    #faq 자주하는 질문들
    public function faq(){
        if((!isset($_SESSION['device']) || $_SESSION['device']!="pc") && $this->agent->is_mobile()) {
            goto_url(base_url().'m/bbs/faq');
        }
        $this->template->header();
        $this->faqSkin();
        $this->template->footer();
    }
    private function faqSkin(){
        $faqcate=$this->input->get('faqcate');
        $title="자주묻는 질문";
        $aside_cs=$this->template->aside_cs();

        if(!$faqcate) { $faqcate = 1; }
        $sql = "select * from shop_faq_cate order by index_no asc";
        $res = $this->query_model->returnArr($sql);
        $faqCate="";
        foreach($res as $key => $row){
            $category_option = "";
            if($row['index_no'] == $faqcate) {
                $category_option = ' class="active"';
            }
            $faqCate.="<li {$category_option}><a href=\"".base_url()."bbs/faq?faqcate={$row['index_no']}\">{$row['catename']}</a></li>";
        }

        $faqTb="";
        $sql = "select * from shop_faq where cate=? order by index_no asc";
        $preData=array($faqcate);
		$res = $this->query_model->returnArr($sql,$preData);
        $i=0;
        foreach($res as $key => $row){
            $faqTb.="
            <li class=\"faq_q\" onclick=\"js_faq('{$i}');\">{$row['subject']}</li>
            <li id=\"sod_faq_con_{$i}\" class=\"faq_a\">".$this->thumbnail_lib->get_view_thumbnail($this->funcomplex_lib->conv_content($row['memo'], 1), 700)."</li>";
            $i++;
        }
        $faqTbEmpty="";
        if($i==0){
            $faqTbEmpty='<div class="empty_list">등록된 FAQ가 없습니다.</div>';
        }

        $data=array('title'=>$title,'aside_cs'=>$aside_cs,'faqCate'=>$faqCate,'faqTb'=>$faqTb,'faqTbEmpty'=>$faqTbEmpty);

        $this->load->view('pc/bbs/faq',$data);
    }
    #partner_reg 분양신청
    public function partner_reg(){
        $config=$this->load->get_var('config');
        $is_member=$this->load->get_var('is_member');
        $urlencode=$this->load->get_var('urlencode');
        
        if((!isset($_SESSION['device']) || $_SESSION['device']!="pc") && $this->agent->is_mobile()) {
            goto_url(base_url().'m/bbs/partner_reg');
        }
        if(!$config['partner_reg_yes']) {
            alert('서비스가 일시 중단 되었습니다.', base_url());
        }
        if(!$is_member) {
            goto_url(base_url().'bbs/login?url='.$urlencode);
        }
        $this->template->header();
        $this->partner_regSkin();
        $this->template->footer();
    }
    private function partner_regSkin(){
        $member=$this->load->get_var('member');
        $partner=$this->load->get_var('partner');
        $config=$this->load->get_var('config');
        $title="쇼핑몰 분양신청";
        $aside_cs=$this->template->aside_cs();
        if($partner['mb_id']){
            $card_case=($partner['pay_settle_case']=='1')?"무통장입금":"신용카드";
            $receipt_price="";
            if($partner['receipt_price'] > 0) 
                $receipt_price=display_price($partner['receipt_price']);
            else
                $receipt_price='무료';

            $inputBank="";
            if($partner['pay_settle_case']=='1') {
                $inputBank="
                <tr>
                    <th scope=\"row\">입금계좌</th>
                    <td>{$partner['bank_acc']}</td>
                </tr>
                <tr>
                    <th scope=\"row\">입금자명</th>
                    <td>{$partner['deposit_name']}</td>
                </tr>";
            } 
            $partMemo="";
            if($partner['memo']) {
                $partMemo="
                <tr>
                    <th scope=\"row\">전달사항</th>
                    <td>".conv_content($partner['memo'], 0)."</td>
                </tr>";
            }
            $data=array('aside_cs'=>$aside_cs,'title'=>$title,'partner'=>$partner,'card_case'=>$card_case,'receipt_price'=>$receipt_price,'partner'=>$partner,
                        'inputBank'=>$inputBank,'partMemo'=>$partMemo);
            $this->load->view('pc/bbs/partner_reg_result',$data);
        }else{
            $token = md5(uniqid(rand(), true));
            set_session("ss_token", $token);
            $from_action_url = base_url().'process/partner_reg_update';
            $pf_requlate="";
            if($config['pf_regulation_subj']) {
                $pf_requlate="
                <h2 class=\"mart20\">{$config['pf_regulation_subj']}</h2>
                <textarea readonly>{$config['pf_regulation']}</textarea>
                <fieldset class=\"fpartnerform_agree\">
                    <input type=\"checkbox\" name=\"agree2\" value=\"1\" id=\"agree21\">
                    <label for=\"agree21\">위 내용을 읽었으며 규정안내에 동의합니다.</label>
                </fieldset>";
            }
            $is_file="";
            $file = './data/common/'.$config['admin_seal'];
			if(is_file($file) && $config['admin_seal']) {
                $file = base_url().'data/common/'.$config['admin_seal'];
				$seal = rpc($file);
				$is_file='<span class="admin_seal"><img src="'.$seal.'"></span>';
			}
            $infoName=$this->funcomplex_lib->get_text($member['name']);
            $infoCell=replace_tel($member['cellphone']);
            $infoEmail=$this->funcomplex_lib->get_text($member['email']);


            $multi_settle = 0;
            $sql = "select * from shop_member_grade where gb_no between 2 and 6 and gb_name <> '' order by gb_no desc ";
            $res = $this->query_model->returnArr($sql);
            $serviceTb="";
            $i=0;
            foreach($res as $key => $row){
                $checked = '';
                if($i==0) $checked = ' checked="checked"';

                $reg = $row['gb_no'].'^'.$row['gb_anew_price'];
                $serviceTb.='<label><input type="radio" name="reg_level" value="'.$reg.'"'.$checked.'> '.$this->funcomplex_lib->get_text($row['gb_name']).'</label>';

                $multi_settle++;
                $i++;
            }
            if(!$multi_settle)
                $serviceTb='설정값이 없습니다. 운영자에게 알려주시면 감사하겠습니다.';
            $payMethod="";
            $btnConfrimBtn="";
            $calculate_total_price="";
            if($multi_settle) {
                $payMethod="
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
                    <td>
                        ".$this->funcomplex_lib->get_bank_account("bank_acc")."
                        <div class=\"padt10\">
                            <input type=\"text\" name=\"reg_hp\" value=\"".replace_tel($member['cellphone'])."\" id=\"reg_hp\" class=\"frm_input\" size=\"20\">
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
                    <td><textarea name=\"memo\" id=\"reg_memo\" rows=\"10\" class=\"frm_textbox wfull h60\"></textarea></td>
                </tr>";
                $btnConfrimBtn="
                <div class=\"btn_confirm\">
                    <input type=\"submit\" value=\"신청하기\" id=\"btn_submit\" class=\"btn_large wset\" accesskey=\"s\">
                    <a href=\"".base_url()."\" class=\"btn_large bx-white\">취소</a>
                </div>";
                $calculate_total_price='calculate_total_price();';
            }
            


            $data=array('token'=>$token,'from_action_url'=>$from_action_url,'aside_cs'=>$aside_cs,'title'=>$title,
                        'pf_requlate'=>$pf_requlate,'is_file'=>$is_file,'infoName'=>$infoName,'infoCell'=>$infoCell,
                        'infoEmail'=>$infoEmail,'serviceTb'=>$serviceTb,'payMethod'=>$payMethod,'btnConfrimBtn'=>$btnConfrimBtn,
                        'calculate_total_price'=>$calculate_total_price);
            $this->load->view('pc/bbs/partner_reg',$data);
        }
    }
    #seller_reg 입양신청
    public function seller_reg(){
        $config=$this->load->get_var('config');
        if((!isset($_SESSION['device']) || $_SESSION['device']!="pc") && $this->agent->is_mobile()) {
            goto_url(base_url().'m/bbs/seller_reg');
        }
        if(!$config['seller_reg_yes']) {
            alert('서비스가 일시 중단 되었습니다.', base_url());
        }
        $this->template->header();
        $this->seller_regSkin();
        $this->template->footer();
    }
    private function seller_regSkin(){
        $config=$this->load->get_var('config');

        $thumb=$this->thumbnail_lib->get_view_thumbnail($this->funcomplex_lib->conv_content($config['seller_reg_guide'], 1), 1000);
        $data=array('thumb'=>$thumb);
        $this->load->view('pc/bbs/seller_reg',$data);
    }
    #입양신청 등록페이지
    public function seller_reg_form(){
        $config=$this->load->get_var('config');
        $is_member=$this->load->get_var('is_member');
        $urlencode=$this->load->get_var('urlencode');
        $member=$this->load->get_var('member');

        if(!$config['seller_reg_yes']) {
            alert('서비스가 일시 중단 되었습니다.', base_url());
        }
        
        if(!$is_member) {
            goto_url(base_url().'bbs/login?url='.$urlencode);
        }
        
        if($this->gvcomplex_lib->is_admin()) {
            alert('관리자는 신청을 하실 수 없습니다.');
        }
        
        if($this->gvcomplex_lib->is_seller($member['id'])) {
            goto_url(base_url().'mypage/page?code=seller_main');
        }
        $this->template->header();
        $this->seller_reg_formSkin();
        $this->template->footer();
    }
    private function seller_reg_formSkin(){
        $seller=$this->load->get_var('seller');
        $config=$this->load->get_var('config');
        $title="온라인 입점신청";
        if($seller['mb_id'] && !$seller['state']) {
            $selAddres=$this->funcomplex_lib->print_address($seller['company_addr1'], $seller['company_addr2'], $seller['company_addr3'], $seller['company_addr_jibeon']);
            $seller_homepage="";
            if($seller['company_hompage']) {
                $seller_homepage="
                <tr>
                    <th scope=\"row\">홈페이지</th>
                    <td>{$seller['company_hompage']}</td>
                </tr>";
            }

            $seller_memo="";
            if($seller['memo']) {
                $seller_memo="
                <tr>
                    <th scope=\"row\">전달사항</th>
                    <td>".$this->funcomplex_lib->conv_content($seller['memo'], 0)."</td>
                </tr>";
            }

            $data=array('seller'=>$seller,'selAddres'=>$selAddres,'seller_homepage'=>$seller_homepage,'seller_memo'=>$seller_memo);
            $this->load->view('pc/bbs/seller_reg_result',$data);
        } else {
            $token = md5(uniqid(rand(), true));
            set_session("ss_token", $token);
            $config['seller_reg_agree'] = preg_replace("/\\\/", "", $config['seller_reg_agree']);
            $from_action_url = base_url().'process/seller_reg_form_update';
            $data=array('title'=>$title,'from_action_url'=>$from_action_url,'token'=>$token,'config'=>$config);
            $this->load->view('pc/bbs/seller_reg_form',$data);
        }
    }
    #미리보기
    public function imgviewer(){
        $tb['title'] = '이미지 뷰어';
        $this->load->vars(array('tb'=>$tb));
        $this->template->header_sub();
        $img=$this->input->get('img');
        $size = getimagesize($img);
        if($size[0] > 980) {	
            $size[1] = $size[1] / $size[0] * 980;
        }

        $width = $size[0];
        $height = $size[1];

        $winwidth = $size[0]+30;
        $winheight = $size[1]+110;
        $data=array('size'=>$size,'img'=>$img);
        $this->load->view('pc/bbs/imgviewer',$data);
        $this->template->footer_sub();
    }
}