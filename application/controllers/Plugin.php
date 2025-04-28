<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Plugin extends CI_Controller{
    public function __construct(){
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
    }
    public function login_oauth(){
        $segStr=$this->uri->segment(3);
        $segArr=explode('_',$segStr);
        $lastNum=count($segArr)-1;
        $seg=$segArr[$lastNum];
        switch ($seg) {
            case 'naver':
                $this->naver();
                break;
            case 'facebook':
                $this->facebook();
                break;
            case 'kakao':
                $this->kakao();
                break;
            case 'google':
                $this->google();
                break;
            default:
                # code...
                break;
        }
    }
    private function naver(){
        $default=$this->load->get_var('default');
        $naver_ClientID = $default['de_naver_appid']; 
        $naver_ClientSecret = $default['de_naver_secret'];

        include_once('./Bill/plugin/http.php');
        include_once('./Bill/plugin/oauth_client.php');
    
        $client = new oauth_client_class;
        $client->debug = false;
        $client->debug_http = true;
        $client->server = 'Naver';
        $client->redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].
            dirname(strtok($_SERVER['REQUEST_URI'],'?')).'/login_with_naver';
    
        $client->client_id = $naver_ClientID; 
        $application_line = __LINE__;
        $client->client_secret = $naver_ClientSecret;
        $login=$this->input->post('login');
        if(strlen($client->client_id) == 0 || strlen($client->client_secret) == 0)
            alert_close('네이버 연동키값을 확인해 주세요.');
    
        if($login=='Y') {
            unset($_SESSION['OAUTH_STATE']);
            $client->ResetAccessToken();
        }
    
        /* API permissions
         */
        if(($success = $client->Initialize())){
            if(($success = $client->Process())){
                if(strlen($client->access_token)){
                    $success = $client->CallAPI(
                        'https://apis.naver.com/nidlogin/nid/getUserProfile.xml',
                        'POST', array('mode'=>'userinfo'), array('FailOnAccessError'=>true), $user
                    );
                }
            }
            $success = $client->Finalize($success);
        }
        if($client->exit)
            exit;
        if($success){
            $xml = simplexml_load_string($user);
            if($xml->result->resultcode == '00') {
                $client->GetAccessToken($AccessToken);
    
                $mb_gubun = 'n_';
                $mb_id = $xml->response->id; //동일인 식별정보
                $mb_name = $xml->response->name; //사용자 이름
                $mb_nick = $xml->response->nickname; //사용자 별명
                $mb_email = $xml->response->email; //사용자 메일주소
                $mb_gender = $xml->response->gender; //F:여성, M:남성, U:확인불가
                $mb_age = $xml->response->age; //사용자 연령대
                $mb_birthday = $xml->response->birthday; //사용자 생일(MM-DD 형식)
                $mb_profile_image = $xml->response->profile_image; //사용자 프로필 사진 URL
                $token_value = $AccessToken['value'];
                $token_refresh = $AccessToken['refresh'];
                $token_secret = '';
    
                //$client->ResetAccessToken();
                $this -> oauth_check(
                    $mb_gubun,
                    $mb_id,
                    $mb_name,
                    $mb_nick,
                    $mb_email,
                    $mb_gender,
                    $mb_age,
                    $mb_birthday,
                    $mb_profile_image,
                    $token_value,
                    $token_refresh,
                    $token_secret
                );
            } else {
                $error = HtmlSpecialChars($xml->result->resultcode);
                alert_close($error);
            }
        } else {
            $error = HtmlSpecialChars($client->error);
            alert_close($error);
        } 
    }
    private function facebook(){
        $default=$this->load->get_var('default');

        // 페이스북 설정 - 키발급은 https://developers.facebook.com
        $facebook_APPid = $default['de_facebook_appid'];
        $facebook_APPsecret = $default['de_facebook_secret'];

        include_once('./Bill/plugin/http.php');
        include_once('./Bill/plugin/oauth_client.php');

        $client = new oauth_client_class;
        $client->debug = false;
        $client->debug_http = true;
        $client->server = 'Facebook';
        $client->redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].
            dirname(strtok($_SERVER['REQUEST_URI'],'?')).'/login_with_facebook';

        $client->client_id = $facebook_APPid; $application_line = __LINE__;
        $client->client_secret = $facebook_APPsecret;

        if(strlen($client->client_id) == 0
        || strlen($client->client_secret) == 0)
            alert_close('페이스북 연동키값을 확인해 주세요.');
            /*
            die('Please go to Facebook Apps page https://developers.facebook.com/apps , '.
                'create an application, and in the line '.$application_line.
                ' set the client_id to App ID/API Key and client_secret with App Secret');
            */	
        /* API permissions
        */
        $client->scope = 'email,publish_actions,user_website';
        if(($success = $client->Initialize())){
            if(($success = $client->Process())){
                if(strlen($client->access_token)){
                    $success = $client->CallAPI(
                        'https://graph.facebook.com/v2.3/me', 
                        'GET', array(), array('FailOnAccessError'=>true), $user);
                }
            }
            $success = $client->Finalize($success);
        }
        if($client->exit)
            exit;
        if($success){
            $client->GetAccessToken($AccessToken);

            $mb_gubun = 'f_';
            $mb_id = $user->id;
            $mb_name = $user->name;
            $mb_nick = $user->name;
            $mb_email = $user->email;
            $mb_homepage = $user->website;

            $token_value = $AccessToken['value'];
            $token_secret = '';
            $token_refresh = '';

            //$client->ResetAccessToken();

            $this -> oauth_check(
                $mb_gubun,
                $mb_id,
                $mb_name,
                $mb_nick,
                $mb_email,
                $mb_gender,
                $mb_age,
                $mb_birthday,
                $mb_profile_image,
                $token_value,
                $token_refresh,
                $token_secret
            );
        } else {
            $error = HtmlSpecialChars($client->error);
            alert_close($error);
        }
    }
    private function google(){
        $default=$this->load->get_var('default');

        $google_APPid = $default['de_google_appid'];
        $google_APPsecret = $default['de_google_secret'];

        include_once('./Bill/plugin/http.php');
        include_once('./Bill/plugin/oauth_client.php');

        $client = new oauth_client_class;
        $client->debug = false;
        $client->debug_http = true;
        $client->server = 'Google';
        $client->redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].
            dirname(strtok($_SERVER['REQUEST_URI'],'?')).'/login_with_google';

        $client->client_id = $google_APPid; $application_line = __LINE__;
        $client->client_secret = $google_APPsecret;

        if(strlen($client->client_id) == 0
        || strlen($client->client_secret) == 0)
            alert_close('구글 연동키값을 확인해 주세요.');
            /*
            die('Please go to Google APIs console page '.
                'http://code.google.com/apis/console in the API access tab, '.
                'create a new client ID, and in the line '.$application_line.
                ' set the client_id to Client ID and client_secret with Client Secret. '.
                'The callback URL must be '.$client->redirect_uri.' but make sure '.
                'the domain is valid and can be resolved by a public DNS.');
            */
        /* API permissions
         */
        $client->scope = 'https://www.googleapis.com/auth/userinfo.email '.
            'https://www.googleapis.com/auth/userinfo.profile';
        if(($success = $client->Initialize()))
        {
            if(($success = $client->Process()))
            {
                if(strlen($client->authorization_error))
                {
                    $client->error = $client->authorization_error;
                    $success = false;
                }
                elseif(strlen($client->access_token))
                {
                    $success = $client->CallAPI(
                        'https://www.googleapis.com/oauth2/v2/userinfo',
                        'GET', array(), array('FailOnAccessError'=>true), $user);
                }
            }
            $success = $client->Finalize($success);
        }
        if($client->exit)
            exit;
        if($success)
        {
            $client->GetAccessToken($AccessToken);

            $mb_gubun = 'g_';
            $mb_id = $user->id;
            $mb_name = $user->name;
            $mb_nick = $user->name;
            $mb_email = $user->email;

            $token_value = $AccessToken['value'];

            //$client->ResetAccessToken();

            $this -> oauth_check(
                $mb_gubun,
                $mb_id,
                $mb_name,
                $mb_nick,
                $mb_email,
                "",
                "",
                null,
                null,
                null,
                $token_value
            );
        } else {
            $error = HtmlSpecialChars($client->error);
            alert_close($error);
        }
    }

    private function kakao(){
        $default=$this->load->get_var('default');

        // 카카오 설정 
        // https://developers.kakao.com/apps/new 에서 키발급 후 발급된 키값 중 REST API키값 입력
        // 카카오 사이트 설정에서 플랫폼 > Redirect Path에 /plugin/login-oauth/login_with_kakao 라고 입력
        $kakao_REST_APIkey = $default['de_kakao_rest_apikey'];

        include_once('./Bill/plugin/http.php');
        include_once('./Bill/plugin/oauth_client.php');

        $client = new oauth_client_class;
        $client->debug = false;
        $client->debug_http = true;
        $client->server = 'Kakao';
        $client->redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].
            dirname(strtok($_SERVER['REQUEST_URI'],'?')).'/login_with_kakao';

        $client->client_id = $kakao_REST_APIkey;
        $client->client_secret = "";

        if(strlen($client->client_id) == 0)
            alert_close('카카오 연동키값을 확인해 주세요.');
        
        /* API permissions
        */
        $client->scope = 'email,publish_actions,user_website';
        if(($success = $client->Initialize())){
            if(($success = $client->Process())){
                if(strlen($client->access_token)){
                    $success = $client->CallAPI(
                        'https://kapi.kakao.com/v2/user/me', 
                        'GET', array(), array('FailOnAccessError'=>true), $user);
                }
            }
            $success = $client->Finalize($success);
        }
        if($client->exit)
            exit;
        if($success){
            $client->GetAccessToken($AccessToken);
            
            $mb_gubun = 'k_';
            $mb_id = $user->id;
            $mb_name = $user->kakao_account->name;
            $mb_nick = $user->kakao_account->profile->nickname;
            $mb_email = "";
            if($user->kakao_account->has_email == 1) {
                $mb_email = $user->kakao_account->email;
            }
            $mb_gender = "";
            if($user->kakao_account->has_gender == 1) {
                $mb_gender = ($user->kakao_account->has_gender == "male") ? "M" : "F";
            }
            $mb_phone_arr = explode(" ", $user->kakao_account->phone_number);
            $mb_phone = "0{$mb_phone_arr[1]}";

            $token_value = $AccessToken['value'];
            $token_secret = '';
            $token_refresh = '';

            //$client->ResetAccessToken();

            $this -> oauth_check(
                        $mb_gubun,
                        $mb_id,
                        $mb_name,
                        $mb_nick,
                        $mb_email,
                        $mb_phone,
                        $mb_gender,
                        null,
                        null,
                        null,
                        $token_value,
                        $token_refresh,
                        $token_secret
                    );
        } else {
            $error = HtmlSpecialChars($client->error);
            alert_close($error);
        }
    }
    private function oauth_check($mb_gubun, $mb_id, $mb_name, $mb_nick, $mb_email, $mb_phone="", $mb_gender="", $mb_age="", $mb_birthday="", $mb_profile_image="", $token_value){
        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $config=$this->load->get_var('config');
        $super=$this->load->get_var('super');
        $mb_recommend=$this->input->post('mb_recommend');
        if(!trim($mb_id) || !trim($token_value)) {
            alert_close("정보가 제대로 넘어오지 않아 오류가 발생했습니다.");
        }

        //소셜아이디
        $sns_id = $mb_id;

        // 소셜아이디(sns_id) 체크
        $sql="show columns from shop_member";
        $res=$this->query_model->returnArr($sql);
        $is_sns_id=false;
        foreach($res as $key => $row){
            if($row['Field']=="sns_id"){
                $is_sns_id=true;
            }
        }
        if(!$is_sns_id){
            $this->query_model->returnNull("ALTER TABLE `shop_member` ADD `sns_id` varchar(255) NOT NULL COMMENT '소셜아이디(sns_id) 체크' AFTER `id` ");
        }

        $sql="select * from shop_member where sns_id = ?";
        $param=array($sns_id);
        $mb = $this->query_model->returnOneArr($sql,$param);
        
        // 소셜아이디가 없으면 기존방식으로 회원가입여부 체크
        if(!$mb['sns_id']) {
            //기존에 풀인증번호로 변경하신 분은 여기를 수정해 주셔야 합니다.
            $mb_id = substr($mb_id,0,18); //최대 20자 = 구분자(2자) + 아이디값(18자)
            $mb_id = $mb_gubun.''.$mb_id;
            $mb = $this->global_lib->get_member($mb_id);
        }

        $register_script = '';
        if($mb['id']) { // 가입된 회원이면

            // 소셜아이디 업데이트
            if(!$mb['sns_id']) {
                $mb['sns_id'] = $sns_id;
                $this->query_model->returnNull("update shop_member set sns_id = '{$sns_id}' where id = '{$mb['id']}' ");
            }

            // 세션 생성
            set_session('ss_mb_id', $mb['id']);

            // FLASH XSS 공격에 대응하기 위하여 회원의 고유키를 생성해 놓는다. 관리자에서 검사함 - 110106
            set_session('ss_mb_key', md5($mb['reg_time'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));

            // 포인트 체크
            $sum_point = $this->global_lib->get_point_sum($mb['id']);

            $sql="update shop_member set point = ? where id = ?";
            $param=array($sum_point,$mb['id']);
            $this->query_model->returnNull($sql,$param);

            set_cookie('ck_mb_id', '', 0);
            set_cookie('ck_auto', '', 0);

        } else {
            //회원아이디 처리 - 16자리 임의 아이디 발급
            //$mb_id = $mb_gubun . (get_microtime() * 100);
            $arr_id = str_split('abcdefghijklmnopqrstuvwxyz012345678901234567890123456789');
            for($i=0; $i<999; $i++) {
                shuffle($arr_id);
                $tmp_id = $mb_gubun . implode('',array_slice($arr_id,0,16));
                $sql = " select count(*) as cnt from shop_member where id = ?";
                $param=array($tmp_id);
                $row = $this->query_model->returnOneArr($sql,$param);
                if(!$row['cnt'])
                    break;
            }

            $mb_id = $tmp_id;

            //이름
            $mb_name = $this->security->xss_clean($mb_name);
            if(!$mb_name) {
                $mb_name = $mb_nick;
            }

            // 스크립트 알림
            $msg_alert = '회원가입을 축하드립니다. 마이페이지에서 회원 정보를 등록해주셔야 정상적인 이용이 가능합니다.';
            if($mb_email) {
                if(!preg_match("/([0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)\.([0-9a-zA-Z_-]+)/", $mb_email)) {
                    $mb_email = '';
                }
            }

            //임시비밀번호 생성 - 10자리 임의 발급
            $arr_pw = str_split('abcdefghijklmnopqrstuvwxyz012345678901234567890123456789');
            shuffle($arr_pw);
            $tmp_pw = implode('',array_slice($arr_pw,0,10));
            $pt_id=$this->load->get_var('pt_id');
            unset($value);
            $value['id']			= $mb_id; // 회원아이디
            $value['sns_id']		= $sns_id; // sns_id
            $value['passwd']		= $tmp_pw; // 비밀번호
            $value['name']			= $mb_name; // 이름
            $value['email']			= $mb_email; // 이메일
            $value['cellphone']		= $mb_phone; // 휴대전화
            $value['gender']		= $mb_gender; // 성별
            $value['today_login']	= $OM_TIME_YMDHIS; // 최근 로그인일시
            $value['reg_time']		= $OM_TIME_YMDHIS; // 가입일시
            $value['mb_ip']			= $_SERVER['REMOTE_ADDR']; // IP
            $value['grade']			= '2'; // 레벨
            $value['pt_id']			= $pt_id; // 추천인아이디
            $value['login_ip']		= $_SERVER['REMOTE_ADDR']; // 최근 로그인IP
            $value['mailser']		= 'N'; // E-Mail을 수신
            $value['smsser']		= 'N'; // SMS를 수신

            // 관리자인증을 사용하지 않는다면 인증으로 간주함.
            if(!$config['cert_admin_yes'])
                $value['use_app']	= '1';

            $this->query_model->insertObj("shop_member", $value);

            // 회원가입 포인트 부여
            $this->funcomplex_lib->insert_point($mb_id, $config['register_point'], '회원가입 축하 포인트 적립', '@member', $mb_id, '회원가입');

            // 추천인에게 포인트 부여
            $this->funcomplex_lib->insert_point($pt_id, $config['partner_point'], $mb_id.'의 추천인', '@member', $pt_id, $mb_id.' 추천');

            // 신규회원가입 쿠폰발급
            if($config['coupon_yes']) {
                $cp_used = false;
                $cp = $this->query_model->returnOneArr("select * from shop_coupon where cp_type = '5'");
                if($cp['cp_id'] && $cp['cp_use']) {
                    if(($cp['cp_pub_sdate'] <= $OM_TIME_YMD || $cp['cp_pub_sdate'] == '9999999999') &&
                    ($cp['cp_pub_edate'] >= $OM_TIME_YMD || $cp['cp_pub_edate'] == '9999999999'))
                        $cp_used = true;

                    if($cp_used) {
                        $this->funcomplex_lib->insert_used_coupon($mb_id, $mb_name, $cp);
                    }
                }
            }

            // 회원가입 문자발송
            $this->funcomplex_lib->moonjaro_register_sms_send($pt_id, $mb_id);

            // 회원가입 메일발송
            include_once("./lib/mailer.lib.php");

            // 회원님께 메일 발송
            $subject = '['.$config['company_name'].'] 회원가입을 축하드립니다.';
            $content=$this->register_form_update_mail1($mb_name);

            mailer($config['company_name'], $super['email'], $mb_email, $subject, $content, 1);

            // 최고관리자님께 메일 발송
            $subject = '['.$config['company_name'].'] '.$mb_name .'님께서 회원으로 가입하셨습니다.';
            $content =$this->register_form_update_mail2($mb_name,$mb_id,$mb_recommend);

            mailer($mb_name, $mb_email, $super['email'], $subject, $content, 1);

            // 가입완료 알림
            $register_script = 'alert("'.$msg_alert.'");';

            // 세션 생성
            set_session('ss_mb_id', $mb_id);

            set_cookie('ck_mb_id', '', 0);
            set_cookie('ck_auto', '', 0);
        }

        // 메타태그 사용안함
        $is_no_meta = true;

        $tb['title'] = 'SNS LOGIN';
        $this->load->vars(array('tb'=>$tb));
        $this->template->header_sub();
        
        $js="
        <script>
            var slr = opener.document.getElementsByName(\"slr_url\").length;
            var url = \"\";
            {$register_script}
            if(slr) {
                url = opener.document.getElementsByName(\"slr_url\")[0].value;
            }

            if(url) {
                opener.location.href = decodeURIComponent(url);
            } else {
                opener.location.reload();
            }

            window.close();
        </script>";
        $data=array('js'=>$js);
        $this->load->view('plugin/oauth_check',$data);
        $this->template->footer_sub();
    }
    private function register_form_update_mail1($mb_name){
        $config=$this->load->get_var('config');
        $return="
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
                        <a href=\"".base_url()."\" target=\"_blank\">{$config['company_name']}</a>
                    </span>
                    <p style=\"margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee\">
                        <b>{$mb_name}</b> 님의 회원가입을 진심으로 축하합니다.<br>
                        회원님의 성원에 보답하고자 더욱 더 열심히 하겠습니다.<br>
                        감사합니다.
                    </p>

                    <a href=\"".base_url()."\" target=\"_blank\" style=\"display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center\">사이트바로가기</a>
                </div>
            </div>

            </body>
            </html>";
        return $return;
    }
    private function register_form_update_mail2($mb_name,$mb_id,$mb_recommend){
        $config=$this->load->get_var('config');
        $return="
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
                    <a href=\"".base_url()."\" target=\"_blank\">{$config['company_name']}</a>
                </span>
                <p style=\"margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee\">
                    <b>{$mb_name}</b>님께서 회원가입 하셨습니다.<br>
                    회원 아이디 : <b>{$mb_id}</b><br>
                    회원 이름 : {$mb_name}<br>
                    추천인아이디 : {$mb_recommend}
                </p>

                <a href=\"".base_url()."\" target=\"_blank\" style=\"display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center\">사이트바로가기</a>
            </div>
        </div>

        </body>
        </html>";

    }
}
?>