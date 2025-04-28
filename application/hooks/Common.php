<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Common  {
    protected $ci;
    public $super_hp;
    function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('Query_model');
        $this->ci->load->library('global_lib');
        $this->ci->load->library('common_lib');
        $this->ci->load->library('partner_lib');
        $this->ci->load->library('funcomplex_lib');
        $this->ci->load->helper('url');
        $this->ci->load->helper('global_helper');
    }

    public function common(){
        $sql="SET SESSION sql_mode = ''";
        $this->ci->Query_model->returnNull($sql);
        $device=$this->ci->input->get('device');
        $is_mobile=false;
        if($device=="mb" || $device=="mobile" || $this->ci->uri->segment(1)=='m'){
            $is_mobile=true;
        }
        $this->ci->load->vars(array('is_mobile'=>$is_mobile));
        
        // 4.00.03 : [보안관련] PHPSESSID 가 틀리면 로그아웃한다.
        if(isset($_REQUEST['PHPSESSID']) && $_REQUEST['PHPSESSID'] != session_id())
            goto_url(base_url().'main/limit');

        $config = $this->ci->Query_model->returnOneArr("select * from shop_config");
        $default = $this->ci->Query_model->returnOneArr("select * from shop_default");
        $super =$this->ci->global_lib->get_member('admin');
        $super_hp = $super['cellphone'];
        $this->super_hp=$super_hp;
        $initalM=$this->autoLogin();
        $qstr=$this->queryString();
        $urObj=$this->urlencode();
        $urlencode=$urObj['urlencode'];
        $url=$urObj['url'];
        $writeObj=$this->write();
        $board=$writeObj['board'];
        $write=$writeObj['write'];
        $memberObj=$this->compare($initalM);
        #$member=$memberObj['member'];
        $is_member=$memberObj['is_member'];
        $is_admin=$memberObj['is_admin'];
        $mb_no=$memberObj['mb_no'];
        $seller=$memberObj['seller'];
        $partner=$memberObj['partner'];
        $filed=trim($this->ci->input->get('filed'));
        $orderby=trim($this->ci->input->get('orderby'));
        $this->notLogin();
        $set_cart_id=get_cookie("ck_guest_cart_id");
        #shop.extend.php에 있던 변수
        // 관리자페이지에서 사용
        $page_rows=$this->ci->input->get('page_rows');
        if(isset($page_rows) && $page_rows) {
            set_session('ss_page_rows', $page_rows);
        }
        $pf_auth_good = false;
        $pf_auth_pg   = false;
        // 개별 상품판매
        if($config['pf_auth_good'] == 2 || ($config['pf_auth_good'] == 3 && $member['use_good']))
            $pf_auth_good = true;
        // 개별 결제연동
        if($config['pf_auth_pg'] == 2 || ($config['pf_auth_pg'] == 3 && $member['use_pg']))
            $pf_auth_pg = true;

        $arr = array(
            'config' => $config,
            'default' => $default,
            'super' => $super,
            'super_hp' => $super_hp,
            "qstr" => $qstr,
            "write" => $write,
            'board' => $board,
            "is_member" => $is_member,
            "is_admin" => $is_admin,
            "mb_no" => $mb_no,
            "seller" => $seller,
            "partner" => $partner,
            "filed" => $filed,
            'orderby' => $orderby,
            'set_cart_id' => $set_cart_id,
            'pf_auth_good' => $pf_auth_good,
            'pf_auth_pg' => $pf_auth_pg,
            'urlencode' => $urlencode,
            'url' => $url
        );
        $this->ci->load->vars($arr);
        $this->session_start_samesite();
    }

    private function session_start_samesite($options = array()) {   
        $res = @session_start($options);    
        // IE 브라우저 또는 엣지브라우저 일때는 secure; SameSite=None 을 설정하지 않습니다. 
        if( preg_match('/Edge/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || preg_match('~Trident/7.0(; Touch)?; rv:11.0~',$_SERVER['HTTP_USER_AGENT']) ){ 
            return $res;    
        }   
        $headers = headers_list();  
        krsort($headers);   
        foreach ($headers as $header) { 
            if (!preg_match('~^Set-Cookie: PHPSESSID=~', $header)) continue;    
            $header = preg_replace('~; secure(; HttpOnly)?$~', '', $header) . '; secure; SameSite=None';    
            header($header, false); 
            break;  
        }   
        return $res;    
    }  

    private function autoLogin(){
        $OM_TIME_YMDHIS=date('y-m-d H:i:s',time());
        $OM_TIME_YMD=substr($OM_TIME_YMDHIS,0,10);
        $member = array();
        $config = $this->ci->Query_model->returnOneArr("select * from shop_config");

        // 자동로그인 부분에서 첫로그인에 포인트 부여하던것을 로그인중일때로 변경하면서 코드도 대폭 수정하였습니다.
        if(isset($_SESSION['ss_mb_id'])) { // 로그인중이라면
            $member = $this->ci->global_lib->get_member($_SESSION['ss_mb_id']);

            // 차단된 회원이면 ss_mb_id 초기화
            if($member['intercept_date'] && $member['intercept_date'] <= date("Ymd", time())) {
                if(!get_session('admin_ss_mb_id')) { // 관리자 강제접속이 아닐때만.
                    set_session('ss_mb_id', '');
                    $member = array();
                }
            } else {
                // 오늘 처음 로그인 이라면
                if(substr($member['today_login'], 0, 10) != $OM_TIME_YMD) {
                    // 첫 로그인 포인트 지급
                    #아직 미완성된 함수
                    $this->ci->funcomplex_lib->insert_point($member['id'], $config['login_point'], $OM_TIME_YMD.'첫로그인', '@login', $member['id'], $OM_TIME_YMD);

                    // 오늘의 로그인이 될 수도 있으며 마지막 로그인일 수도 있음
                    // 해당 회원의 접근일시와 IP 를 저장
                    $sql = " update shop_member set login_sum = login_sum + 1, today_login = ?, login_ip = ? where id = ?";
                    $param=array($OM_TIME_YMDHIS,$_SERVER['REMOTE_ADDR'],$member['id']);
                    $this->ci->Query_model->returnNull($sql,$param);
                }
            }
        } else {
            // 자동로그인 ---------------------------------------
            // 회원아이디가 쿠키에 저장되어 있다면 (3.27)
            if($tmp_mb_id = get_cookie('ck_mb_id')) {
                $tmp_mb_id = substr(preg_replace("/[^a-zA-Z0-9_]*/", "", $tmp_mb_id), 0, 20);
                // 최고관리자는 자동로그인 금지
                if(strtolower($tmp_mb_id) != 'admin') {
                    $sql = " select passwd, intercept_date from shop_member where id = ?";
                    $param=array($tmp_mb_id);
                    $row = $this->ci->Query_model->returnOneArr($sql,$param);
                    $key = md5($_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $row['passwd']);
                    // 쿠키에 저장된 키와 같다면
                    $tmp_key = get_cookie('ck_auto');
                    if($tmp_key === $key && $tmp_key) {
                        // 차단, 인트로 사용이 아니라면
                        if($row['intercept_date'] == '' && !$config['shop_intro_yes'] ) {
                            // 세션에 회원아이디를 저장하여 로그인으로 간주
                            set_session('ss_mb_id', $tmp_mb_id);

                            // 페이지를 재실행
                            echo "<script type='text/javascript'> window.location.reload(); </script>";
                            exit;
                        }
                    }
                    // $row 배열변수 해제
                    unset($row);
                }
            }
            // 자동로그인 end ---------------------------------------
        }
        $member['telephone']=isset($member['telephone'])?$member['telephone']:"";
        $this->ci->load->vars(array("member"=>$member));
        return $member;
    }
    
    private function queryString(){
        // QUERY_STRING
        $qstr = '';
        $set=trim($this->ci->input->get('set'));
        $qstr .= '&set=' . urlencode($set);
        $sca=trim($this->ci->input->get('sca'));
        $qstr .= '&sca=' . urlencode($sca); 
        $sfl=preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*\s]/", "",trim($this->ci->input->get('sfl')));
        $qstr .= '&sfl=' . urlencode($sfl);
        $stx=trim($this->ci->input->get('stx'));
        $qstr .= '&stx=' . urlencode($stx);
        $sst=trim($this->ci->input->get('sst'));
        $qstr .= '&sst=' . urlencode($sst);
        $sod=trim($this->ci->input->get('sod'));
        $qstr .= '&sod=' . urlencode($sod);
        $sop=trim($this->ci->input->get('sop'));
        $qstr .= '&sop=' . urlencode($sop);
        $spt=trim($this->ci->input->get('spt'));
        $qstr .= '&spt=' . urlencode($spt);
        $ca_id=trim($this->ci->input->get('ca_id'));
        $qstr .= '&ca_id=' . urlencode($ca_id);

        $fr_date=trim($this->ci->input->get('fr_date'));
        $qstr .= '&fr_date=' . urlencode($fr_date);

        $to_date=trim($this->ci->input->get('to_date'));
        $qstr .= '&to_date=' . urlencode($to_date);

        $filed=trim($this->ci->input->get('filed'));
        $qstr .= '&filed=' . urlencode($filed);

        $orderby=trim($this->ci->input->get('orderby'));
        $qstr .= '&orderby=' . urlencode($orderby);
        return $qstr;
    }
    private function urlencode(){
        // URL ENCODING
        if(isset($this->ci->input->get['url'])) {
            $url = strip_tags(trim($this->ci->input->get['url']));
            $urlencode = urlencode($url);
        } else {
            $url = '';
            $urlencode = urlencode($_SERVER['REQUEST_URI']);
            if('') {
                $p = @parse_url('');
                $urlencode = urldecode(preg_replace("/^".urlencode($p['path'])."/", "", $urlencode));
            }
        }
        
        return array('url'=>$url,'urlencode'=>$urlencode);
    }
    private function write(){
        $boardid=$this->ci->input->get('boardid');
        $index_no=$this->ci->input->get('index_no');
        $write=null;
        $board =null;
        if($boardid) {
            $sql="select * from shop_board_conf where index_no=?";
            $data=array($boardid);
            $board = $this->ci->Query_model->returnOneArr($sql,$data);
            if($board['index_no']) {
                $write_table = 'shop_board_'.$boardid; // 게시판 테이블 전체이름
                if(isset($index_no) && $index_no){
                    $sql="select * from {$write_table} where index_no = ? ";
                    $data=array($index_no);
                    $write = $this->ci->Query_model->returnOneArr($sql,$data);
                }
            }
        }
        $returnObj=array('board'=>$board,'write'=>$write);
        return $returnObj;
    }
    private function notLogin(){
        // 비회원구매를 위해 쿠키를 1년간 저장
        if(empty(get_cookie("ck_guest_cart_id")) || get_cookie("ck_guest_cart_id")==null )
            set_cookie("ck_guest_cart_id", time(), 86400 * 365);
        #$set_cart_id = get_cookie('ck_guest_cart_id');
        #return $set_cart_id;
    }
    private function compare($member){
        #compare 하기전 autoLogin할때 이미 $member라는 객체 변수는 전역변수로 등록이 되어있어야 get_admin과 gvcomplex_lib를 호출시 $member를 가져올수 있음
        #비회원일때 처리를 하기위해서 여기서 한번더 $member라는 객체 변수를 전역변수로 등록하기
        // 회원, 비회원 구분
        $is_admin = $mb_no = $seller= $partner= '';
        if(isset($member['id'])){
            $is_member = 1;
            $is_admin = $this->ci->funcomplex_lib->get_admin($member['id']);
            #함수안에 함수가 있는 것은 아직 작업 안함
            #여기서 입점사 관리자 들어갈수 있어야 함
            $partner = $this->ci->partner_lib->get_partner($member['id']);
            $seller = $this->ci->global_lib->get_seller($member['id']);
            $mb_no = $member['index_no'];
        }else{
            $is_member = 0;
            $member['id'] = '';
            $member['grade'] = 10; // 비회원의 경우 회원레벨을 가장 낮게 설정
        }
        $this->ci->load->vars(array("member"=>$member));

        $return=array('member'=>$member,"is_member"=>$is_member,"is_admin"=>$is_admin,"mb_no"=>$mb_no,'seller'=>$seller,'partner'=>$partner);
        return $return;
    }
}
?>