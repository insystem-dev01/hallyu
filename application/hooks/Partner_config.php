<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Partner_config  {
    protected $ci;
    public $super_hp;
    protected $OM_TIME_YMDHIS;
    protected $OM_TIME_YMD;
    protected $OM_TIME_HIS;
    function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('Query_model');
        $this->ci->load->library('global_lib');
        $this->ci->load->library('common_lib');
        $this->ci->load->library('partner_lib');
        $this->ci->load->library('gvcomplex_lib');
        $this->ci->load->library('funcomplex_lib');
        $this->ci->load->helper('url');
        $this->ci->load->helper('global_helper');
        $this->OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $this->OM_TIME_YMD=substr($this->OM_TIME_YMDHIS,0,10);
        $this->OM_TIME_HIS=substr($this->OM_TIME_YMDHIS,11,8);
    }
    public function common(){
        $pt_id="";
        $config=$this->ci->load->get_var('config');
        $default=$this->ci->load->get_var('default');
        $super=$this->ci->load->get_var('super');
        $is_member=$this->ci->load->get_var('is_member');
        $OM_TIME_YMD=$this->OM_TIME_YMD;

        $mk = array();
        $pt = array();
        // 개별도메인을 검사 후 있으면 세션을 바꾼다
        $sql = "select id from shop_member where homepage = ?";
        $data=array($_SERVER['HTTP_HOST']);
        $row = $this->ci->Query_model->returnOneArr($sql,$data);
        if(isset($row['id']) && $row['id']!="") {
            $temp_shopid = $row['id'].'.';
        } else {
            $temp_domain = $this->ci->gvcomplex_lib->get_basedomain($_SERVER['HTTP_HOST']);
            $temp_shopid = preg_replace("/{$temp_domain}/", "", $_SERVER['HTTP_HOST']);
        }
        // 접속도메인이 (아이디.domain) 형태인 경우
        if(substr_count($temp_shopid, ".") == 1) {
            $fr_shopid = explode('.', $temp_shopid);
            $pt_id = trim($fr_shopid[0]);

            // 가맹점인가?
            if($this->ci->funcomplex_lib->is_partner($pt_id)) {
                // 관리비를 사용중일때 기간이 만료되었다면 pt_id 를 비움
                if($config['pf_expire_use'] && $config['pf_session_no']) {
                    $row = $this->ci->global_lib->get_member($pt_id, 'term_date');
                    if(!is_null_time($row['term_date'])) {
                        if($row['term_date'] < $OM_TIME_YMD) {
                            $pt_id="";
                        }
                    }
                }
            }
        }
        // 가맹점이 아니면 최고관리자로 변경
        if(!$this->ci->funcomplex_lib->is_partner($pt_id)) {
            $pt_id = 'admin';
        }
        // 가맹점아이디를 세션에담는다.
        set_session('pt_id', $pt_id);
        $mk = $this->ci->global_lib->get_member($pt_id);
        $pt = $this->ci->partner_lib->get_partner($pt_id);
        // 방문자수의 접속을 남김
        $this->visit_insert_inc($pt_id);
        $auth_good = false;
        $auth_pg = false;

        if($pt_id != 'admin') {
            // 개별 상품판매
            if($config['pf_auth_good'] == 2 || ($config['pf_auth_good'] == 3 && $mk['use_good']))
                $auth_good = true;

            // 개별 PG결제
            if($config['pf_auth_pg'] == 2 || ($config['pf_auth_pg'] == 3 && $mk['use_pg']))
                $auth_pg = true;
        }

        // 인트로사용시 로그인페이지로 이동을 제외함.
        $intro_run = 0;
        if(!$is_member && $config['shop_intro_yes']) {
            if(preg_match("/index.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/register.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/register_form.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/register_form_update.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/register_result.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/email_stop.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/password_lost.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/password_lost2.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/password_lost_certify.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/login_check.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/provision.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/policy.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/ajax.mb_email.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/ajax.mb_hp.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/ajax.mb_id.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/ajax.mb_recommend.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/hpcert1.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/hpcert2.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/ipin1.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/ipin2.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/kcpcert_form.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/kcpcert_result.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/AuthOnlyReq.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/AuthOnlyRes.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/returnurl.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/login_with_facebook.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/login_with_google.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/login_with_kakao.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/login_with_naver.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/login_with_twitter.php/", $_SERVER['PHP_SELF'])) $intro_run++;
            if(preg_match("/oauth_check.php/", $_SERVER['PHP_SELF'])) $intro_run++;

            if(!$intro_run) {
                if($this->ci->agent->is_mobile()) // 모바일 접속인가?
                    goto_url(base_url().'m');
                else
                    goto_url(base_url());
            }
        }

        // 개별 전자결제(PG)
        if($auth_pg) {
            $pt_settle_pid = $pt_id;
            $default = $this->ci->funcomplex_lib->set_partner_value($pt_id);
        } else {
            $pt_settle_pid = 'admin';
        }

        // 본사접속일 아닐때는 가맹점 설정정보를 불러옴
        if($pt_id != 'admin') {
            $default = $this->ci->funcomplex_lib->set_default_value($pt_id);
            $config  = $this->ci->funcomplex_lib->set_config_value($pt_id);

            if($pt['saupja_yes']) {
                $super['email'] = $mk['email'];
            }
        } 

        // 역슬래시가 생기는 현상을 방지
        $config['shop_provision'] = preg_replace("/\\\/", "", $config['shop_provision']);
        $config['shop_private']   = preg_replace("/\\\/", "", $config['shop_private']);
        $config['shop_policy']    = preg_replace("/\\\/", "", $config['shop_policy']);
        // 매출전표 url 설정
        $OM_BILL_RECEIPT_URL="";
        $OM_CASH_RECEIPT_URL="";
        if($default['de_card_test']) {
            $OM_BILL_RECEIPT_URL='https://testadmin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=';
            $OM_CASH_RECEIPT_URL= 'https://testadmin8.kcp.co.kr/Modules/Service/Cash/Cash_Bill_Common_View.jsp?term_id=PGNW';
        } else {
            $OM_BILL_RECEIPT_URL= 'https://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=';
            $OM_CASH_RECEIPT_URL= 'https://admin.kcp.co.kr/Modules/Service/Cash/Cash_Bill_Common_View.jsp?term_id=PGNW';
        }
        $arr=array('config'=>$config,'pt_id'=>$pt_id,'mk'=>$mk,'OM_BILL_RECEIPT_URL'=>$OM_BILL_RECEIPT_URL,'OM_CASH_RECEIPT_URL'=>$OM_CASH_RECEIPT_URL,
                   'pt_settle_pid'=>$pt_settle_pid);

        $this->ci->load->vars($arr);
    }
    protected function visit_insert_inc($pt_id){
        $OM_TIME_YMD=$this->OM_TIME_YMD;
        $OM_TIME_HIS=$this->OM_TIME_HIS;
        // 컴퓨터의 아이피와 쿠키에 저장된 아이피가 다르다면 테이블에 반영함
        if(get_cookie('ck_visit_ip') != $_SERVER['REMOTE_ADDR']){
            set_cookie('ck_visit_ip', $_SERVER['REMOTE_ADDR'], 86400); // 하루동안 저장

            $tmp_row = $this->ci->Query_model->returnOneArr(" select max(vi_id) as max_vi_id from shop_visit ");
            $vi_id = $tmp_row['max_vi_id'] + 1;	

            // $_SERVER 배열변수 값의 변조를 이용한 SQL Injection 공격을 막는 코드입니다. 110810
            $remote_addr = $this->ci->db->escape_str(trim($_SERVER['REMOTE_ADDR']));
            $referer = "";
            if(isset($_SERVER['HTTP_REFERER']))
                $referer = $this->ci->db->escape_str($this->ci->security->xss_clean($_SERVER['HTTP_REFERER']));
            $user_agent  = $this->ci->db->escape_str($this->ci->security->xss_clean($_SERVER['HTTP_USER_AGENT']));
            $sql = " insert ignore shop_visit (vi_id,mb_id,vi_ip,vi_date,vi_time,vi_referer,vi_agent) values (?,?,?,?,?,?,?)";
            $data=array($vi_id,$pt_id,$remote_addr,$OM_TIME_YMD,$OM_TIME_HIS,$referer,$user_agent);
            $result = $this->ci->Query_model->returnQueryObj($sql,$data);
            // 정상으로 INSERT 되었다면 방문자 합계에 반영
            if($result) {
                $sql = " select vs_count as cnt 
                        from shop_visit_sum 
                        where vs_date = ?
                            and mb_id = ?";
                $data=array($OM_TIME_YMD,$pt_id);
                $row = $this->ci->Query_model->returnOneArr($sql,$data);
                $tmp_cnt = (int)$row['cnt'];
                if($tmp_cnt) {
                    $sql = " update shop_visit_sum 
                                set vs_count = vs_count + 1 
                                where vs_date =?
                                and mb_id = ?";
                    $this->ci->Query_model->returnNull($sql,$data);
                } else {
                    $sql = " insert ignore shop_visit_sum (vs_count, vs_date, mb_id) values (1,?,?)";
                    $this->ci->Query_model->returnNull($sql,$data);
                }

                // INSERT, UPDATE 된건이 있다면 기본환경설정 테이블에 저장
                // 방문객 접속시마다 따로 쿼리를 하지 않기 위함 (엄청난 쿼리를 줄임 ^^)
                
                // 접속수수료 지급
                $this->ci->funcomplex_lib->insert_visit_pay($pt_id, $remote_addr, $referer, $user_agent);

                // 오늘
                $sql = " select vs_count as cnt 
                        from shop_visit_sum 
                        where vs_date = ?
                            and mb_id = ?";
                $data2=array($OM_TIME_YMD,$pt_id);
                $row = $this->ci->Query_model->returnOneArr($sql,$data2);
                $vi_today = (int)$row['cnt'];

                // 어제
                $sql = " select vs_count as cnt 
                        from shop_visit_sum
                        where vs_date = DATE_SUB(?, INTERVAL 1 DAY)
                            and mb_id = ?";
                $row = $this->ci->Query_model->returnOneArr($sql,$data2);
                $vi_yesterday = isset($row['cnt'])?(int)$row['cnt']:0;

                // 최대
                $sql = " select max(vs_count) as cnt 
                        from shop_visit_sum 
                        where mb_id = ?";
                $data3=array($pt_id);
                $row = $this->ci->Query_model->returnOneArr($sql,$data3);
                $vi_max = (int)$row['cnt'];

                // 전체
                $sql = " select sum(vs_count) as total from shop_visit_sum where mb_id =?";
                $row = $this->ci->Query_model->returnOneArr($sql,$data3);
                $vi_sum = (int)$row['total'];

                $visit = '오늘:'.$vi_today.', 어제:'.$vi_yesterday.', 최대:'.$vi_max.', 전체:'.$vi_sum;		

                // 기본설정 테이블에 방문자수를 기록한 후
                // 방문자수 테이블을 읽지 않고 출력한다.
                // 쿼리의 수를 상당부분 줄임
                $sql = " update shop_member set vi_today = ?,vi_yesterday = ?,vi_max = ?,vi_sum = ?,vi_history = ? where id = ?";
                $data4=array($vi_today,$vi_yesterday,$vi_max,$vi_sum,$visit,$pt_id);
                $this->ci->Query_model->returnNull($sql,$data4);
            }
        }
    }

}
?>