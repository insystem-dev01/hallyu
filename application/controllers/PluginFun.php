<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PluginFun extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->helper('global_helper');
        $this->load->model('query_model');
        $this->load->library('gvcomplex_lib');
        $this->load->library('global_lib');
        $this->load->library('funcomplex_lib');
        $this->load->library('template');
    }
    public function okname(){
        $segment=$this->uri->segment(3);
        switch ($segment) {
            case 'ipin1':
                $this->ipin1();
                break;
            case 'ipin2':
                $this->ipin2();
                break;
            case 'hpcert1':
                $this->hpcert1();
                break;
            case 'hpcert2':
                $this->hpcert2();
                break;
            default:
                # code...
                break;
        }
    }
    public function kcpcert(){
        $segment=$this->uri->segment(3);
        switch ($segment) {
            case 'kcpcert_form':
                $this->kcpcert_form();
                break;
           
            default:
                # code...
                break;
        }
    }
    public function lgxpay(){
        $segment=$this->uri->segment(3);
        switch ($segment) {
            case 'AuthOnlyReq':
                $this->AuthOnlyReq();
                break;
            case 'returnurl':
                $this->returnurl();
                break;
            default:
                # code...
                break;
        }
    }
    private function ipin1(){
        $config=$this->load->get_var('config');
        $member=$this->load->get_var('member');
        // 금일 인증시도 회수 체크
        $this->funcomplex_lib->certify_count_check($member['id'], 'ipin');
        // KISA 취약점 내용(KVE-2018-0291) hpcert1.php의 $cmd 함수에 대한 인자 값은 hpcert_config.php 파일에서 설정되나, 이를 다른 페이지에서 포함한 뒤 호출할 시 임의 값 설정 가능
        // 이에 include_once 를 include 로 수정함 
        include('.plugin/ipin.config.php');

        $option = "C";// Option

        // 명령어
        $cmd = "$exe $keypath $memid \"{$reserved1}\" \"{$reserved2}\" $EndPointURL $logpath $option";

        // 실행
        exec($cmd, $out, $ret);

        if($ret == 127) {
            alert_close('모듈실행 파일이 존재하지 않습니다.\\n\\n'.basename($exe).' 파일이 '.'./pluginFun/okname/bin 안에 있어야 합니다.');
        }

        if($ret == 126) {
            alert_close('모듈실행 파일의 실행권한이 없습니다.\\n\\nchmod 755 '.basename($exe).' 과 같이 실행권한을 부여해 주십시오.');
        }

        if($ret == -1) {
            alert_close('모듈실행 파일의 실행권한이 없습니다.\\n\\ncmd.exe의 IUSER 실행권한이 있는지 확인하여 주십시오.');
        }

        $pubkey = "";
        $sig = "";
        $curtime = "";

        $pubkey=$out[0];
        $sig=$out[1];
        $curtime=$out[2];

        $tb['title'] = 'KCB 아이핀 본인확인';
        $this->load->vars(array('tb'=>$tb));
        $this->template->header_sub();
        $data=array('kcbForm_action'=>$kcbForm_action,'idpCode'=>$idpCode,'idpUrl'=>$idpUrl,'cpCode'=>$cpCode,'curtime'=>$curtime,'returnUrl'=>$returnUrl,
                    'pubkey'=>$pubkey,'sig'=>$sig);
        $this->load->view('plugin/okanme/ipin_config',$data);
        $this->template->footer_sub();
    }
    private function ipin2(){
        $member=$this->load->get_var('member');
        $config=$this->load->get_var('config');
        include('.plugin/ipin.config.php');

        //아이핀팝업에서 조회한 PERSONALINFO이다.
        @$encPsnlInfo = $this->input->post("encPsnlInfo");
        if(preg_match('~[^0-9a-zA-Z+/=]~', $encPsnlInfo, $match)) {echo "입력 값 확인이 필요합니다"; exit;}

        //KCB서버 공개키
        @$WEBPUBKEY = trim($_POST["WEBPUBKEY"]);
        if(preg_match('~[^0-9a-zA-Z+/=]~', $WEBPUBKEY, $match)) {echo "입력 값 확인이 필요합니다"; exit;}

        //KCB서버 서명값
        @$WEBSIGNATURE = trim($_POST["WEBSIGNATURE"]);
        if(preg_match('~[^0-9a-zA-Z+/=]~', $WEBSIGNATURE, $match)) {echo "입력 값 확인이 필요합니다"; exit;}

        //아이핀 서버와 통신을 위한 키파일 생성
        // 파라미터 정의
        $cpubkey = $WEBPUBKEY;    //server publickey
        $csig = $WEBSIGNATURE;    //server signature
        $encdata = $encPsnlInfo;  //PERSONALINFO
        $option = "SU";

        // 명령어
        $cmd = "$exe $keypath $memid $EndPointURL $cpubkey $csig $encdata $logpath $option";

        // 실행
        exec($cmd, $out, $ret);

        // 인증내역기록
        $this->global_lib->insert_cert_history($member['id'], 'kcb', 'ipin');

        if($ret != 0) {
            if($ret <=200)
                $resultCd=sprintf("B%03d", $ret);
            else
                $resultCd=sprintf("S%03d", $ret);

            alert_close('아이핀 본인확인 중 오류가 발생했습니다. 오류코드 : '.$resultCd.'\\n\\n문의는 코리아크레딧뷰로 고객센터 02-708-1000 로 해주십시오.');
        }

        // 결과라인에서 값을 추출
        foreach($out as $a => $b) {
            if($a < 13) {
                $field[$a] = $b;
            }
        }

        /*
        $field_name_IPIN_DEC = array(
            "dupInfo        ",	// 0
            "coinfo1        ",	// 1
            "coinfo2        ",	// 2
            "ciupdate       ",	// 3
            "virtualNo      ",	// 4
            "cpCode         ",	// 5
            "realName       ",	// 6
            "cpRequestNumber",	// 7
            "age            ",	// 8
            "sex            ",	// 9
            "nationalInfo   ",	// 10
            "birthDate      ",	// 11
            "authInfo       ",	// 12
        );
        */

        $mb_name = $field[6];
        $req_num = $field[7];
        $mb_birth = $field[11];
        $mb_dupinfo = $field[0];

        // 중복정보 체크
        $sql = " select id from shop_member where id <> ? and mb_dupinfo = ?";
        $param=array($member['id'],$mb_dupinfo);
        $row = $this->query_model->returnOneArr($sql,$param);
        if($row['id']) {
            alert_close("입력하신 본인확인 정보로 가입된 내역이 존재합니다.\\n회원아이디 : ".$row['id']);
        }

        // hash 데이터
        $cert_type = 'ipin';
        $md5_cert_no = md5($req_num);
        $hash_data   = md5($mb_name.$cert_type.$mb_birth.$md5_cert_no);

        // 성인인증결과
        $adult = $field[8] > 5 ? 1 : 0;

        set_session('ss_cert_type',    $cert_type);
        set_session('ss_cert_no',      $md5_cert_no);
        set_session('ss_cert_hash',    $hash_data);
        set_session('ss_cert_adult',   $adult);
        set_session('ss_cert_birth',   $mb_birth);
        set_session('ss_cert_sex',     ($field[9] == 1 ? 'M' : 'F'));
        set_session('ss_cert_dupinfo', $mb_dupinfo);

        $tb['title'] = 'KCB 아이핀 본인확인';
        $this->load->vars(array('tb'=>$tb));
        $this->template->header_sub();
        $data=array('cert_type'=>$cert_type,'mb_name'=>$mb_name,'md5_cert_no'=>$md5_cert_no);
        $this->load->view('plugin/okname/ipin2',$data);
        $this->template->footer_sub();
    }
    private function hpcert1(){
        $member=$this->load->get_var('member');
        // 금일 인증시도 회수 체크
        $this->funcomplex_lib->certify_count_check($member['id'], 'hp');

        //include_once('./hpcert.config.php');

        // KISA 취약점 내용(KVE-2018-0291) hpcert1.php의 $cmd 함수에 대한 인자 값은 hpcert_config.php 파일에서 설정되나, 이를 다른 페이지에서 포함한 뒤 호출할 시 임의 값 설정 가능
        // 이에 include_once 를 include 로 수정함
        include('./plugin/okname/hpcert.config.php');
        /**************************************************************************
        okname 실행
        **************************************************************************/
        $option = "Q";

        $cmd = "$exe $svcTxSeqno \"$name\" $birthday $gender $ntvFrnrTpCd $mblTelCmmCd $mbphnNo $rsv1 $rsv2 $rsv3 \"$returnMsg\" $returnUrl $inTpBit $hsCertMsrCd $hsCertRqstCausCd $memId $clientIp $clientDomain $endPointURL $logPath $option";

        //cmd 실행
        exec($cmd, $out, $ret);

        if($ret == 127) {
            alert_close('모듈실행 파일이 존재하지 않습니다.\\n\\n'.basename($exe).' 파일이 '.OM_PLUGIN_DIR.'/'.OM_OKNAME_DIR.'/bin 안에 있어야 합니다.');
        }

        if($ret == 126) {
            alert_close('모듈실행 파일의 실행권한이 없습니다.\\n\\nchmod 755 '.basename($exe).' 과 같이 실행권한을 부여해 주십시오.');
        }

        if($ret == -1) {
            alert_close('모듈실행 파일의 실행권한이 없습니다.\\n\\ncmd.exe의 IUSER 실행권한이 있는지 확인하여 주십시오.');
        }

        /**************************************************************************
        okname 응답 정보
        **************************************************************************/
        $retcode = "";										// 결과코드
        $retmsg = "";										// 결과메시지
        $e_rqstData = "";									// 암호화된요청데이터

        if($ret == 0) {//성공일 경우 변수를 결과에서 얻음
            $retcode = $out[0];
            $retmsg  = $out[1];
            $e_rqstData = $out[2];
        }
        else {
            if($ret <=200)
                $retcode=sprintf("B%03d", $ret);
            else
                $retcode=sprintf("S%03d", $ret);
        }

        $tb['title'] = 'KCB 휴대폰 본인확인';
        $this->load->vars(array('tb'=>$tb));
        $this->template->header_sub();
        $retcodeTag="";
        if($retcode == "B000") {
            //인증요청
            $retcodeTag= ("<script>request();</script>");
        } else {
            //요청 실패 페이지로 리턴
            $retcodeTag= ("<script>alert(\"$retcode\"); self.close();</script>");
        }
        $data=array('commonSvlUrl'=>$commonSvlUrl,'e_rqstData'=>$e_rqstData,'targetId'=>$targetId,'retcodeTag'=>$retcodeTag);
        $this->load->view('plugin/okname/hpcert1',$data);
        $this->template->footer_sub();
    }
    private function hpcert2(){
        $config=$this->load->get_var('config');
        $member=$this->load->get_var('member');
        //include_once('./hpcert.config.php');
        // KISA 취약점 내용(KVE-2018-0291) hpcert1.php의 $cmd 함수에 대한 인자 값은 hpcert_config.php 파일에서 설정되나, 이를 다른 페이지에서 포함한 뒤 호출할 시 임의 값 설정 가능
        // 이에 include_once 를 include 로 수정함
        include('./plugin/okname/hpcert.config.php');
        /**************************************************************************
            파일명 : safe_hs_cert3.php

            생년월일 본인 확인서비스 결과 화면(return url)
        **************************************************************************/

        /* 공통 리턴 항목 */
        //$idcfMbrComCd			=   $_POST['idcf_mbr_com_cd'];      // 고객사코드
        $idcfMbrComCd           =   $memId;
        $hsCertSvcTxSeqno       =   $this->input->post('hs_cert_svc_tx_seqno'); // 거래번호
        $rqstSiteNm             =   $this->input->post('rqst_site_nm');         // 접속도메인
        $hsCertRqstCausCd       =   $this->input->post('hs_cert_rqst_caus_cd'); // 인증요청사유코드 2byte  (00:회원가입, 01:성인인증, 02:회원정보수정, 03:비밀번호찾기, 04:상품구매, 99:기타)

        $resultCd               =   $this->input->post('result_cd');            // 결과코드
        $resultMsg              =   $this->input->post('result_msg');           // 결과메세지
        $certDtTm               =   $this->input->post('cert_dt_tm');           // 인증일시

        if($resultCd != 'B000') {
            alert_close('휴대폰 본인확인 중 오류가 발생했습니다. 오류코드 : '.$resultCd.'\\n\\n문의는 코리아크레딧뷰로 고객센터 02-708-1000 로 해주십시오.');
        }

        /**************************************************************************
         * 모듈 호출    ; 생년월일 본인 확인서비스 결과 데이터를 복호화한다.
         **************************************************************************/
        $encInfo = $this->input->post('encInfo');
        if(preg_match('~[^0-9a-zA-Z+/=]~', $encInfo, $match)) {echo "입력 값 확인이 필요합니다"; exit;}

        //KCB서버 공개키
        $WEBPUBKEY = trim($this->input->post('WEBPUBKEY'));
        if(preg_match('~[^0-9a-zA-Z+/=]~', $WEBPUBKEY, $match)) {echo "입력 값 확인이 필요합니다"; exit;}

        //KCB서버 서명값
        $WEBSIGNATURE = trim($this->input->post('WEBSIGNATURE'));
        if(preg_match('~[^0-9a-zA-Z+/=]~', $WEBSIGNATURE, $match)) {echo "입력 값 확인이 필요합니다"; exit;}

        // ########################################################################
        // # 암호화키 파일 설정 (절대경로) - 파일은 주어진 파일명으로 자동 생성 됨
        // ########################################################################
        $keypath = './plugin/okname/key/safecert_'.$idcfMbrComCd.'.key';

        $cpubkey = $WEBPUBKEY;    //server publickey
        $csig = $WEBSIGNATURE;    //server signature

        // ########################################################################
        // # 로그 경로 지정 및 권한 부여 (절대경로)
        // # 옵션값에 'L'을 추가하는 경우에만 로그가 생성됨.
        // ########################################################################
        $option = 'SU';

        // 명령어
        $cmd = "$exe $keypath $idcfMbrComCd $endPointUrl $WEBPUBKEY $WEBSIGNATURE $encInfo $logPath $option";

        // 실행
        exec($cmd, $out, $ret);

        // 인증내역기록
        $this->global_lib->insert_cert_history($member['id'], 'kcb', 'hp');

        if($ret == 0) {
            // 결과라인에서 값을 추출
            foreach($out as $a => $b) {
                if($a < 17) {
                    $field[$a] = $b;
                }
            }
            $resultCd = $field[0];
        }
        else {
            if($ret <=200)
                $resultCd=sprintf("B%03d", $ret);
            else
                $resultCd=sprintf("S%03d", $ret);
        }

        /*
        echo "처리결과코드      :$resultCd  <br/>";
        echo "처리결과메시지    :$field[1]  <br/>";
        echo "거래일련번호      :$field[2]  <br/>";
        echo "인증일시          :$field[3]  <br/>";
        echo "DI                :$field[4]  <br/>";
        echo "CI                :$field[5]  <br/>";
        echo "성명              :$field[7]  <br/>";
        echo "생년월일          :$field[8]  <br/>";
        echo "성별              :$field[9]  <br/>";
        echo "내외국인구분      :$field[10] <br/>";
        echo "통신사코드        :$field[11] <br/>";
        echo "휴대폰번호        :$field[12] <br/>";
        echo "리턴메시지        :$field[16] <br/>";
        */

        // 인증결과처리
        $mb_name = $field[7];
        $req_num = $field[12];
        $mb_birth = $field[8];
        $mb_dupinfo = $field[4];
        $phone_no = hyphen_hp_number($req_num);

        // 중복정보 체크
        $sql = " select id from shop_member where id <> ? and mb_dupinfo = ?";
        $param=array($member['id'],$mb_dupinfo);
        $row = $this->query_model->returnOneArr($sql,$param);
        if($row['id']) {
            alert_close("입력하신 본인학인 정보로 가입된 내역이 존재합니다.\\n회원아이디 : ".$row['id']);
        }

        // hash 데이터
        $cert_type = 'hp';
        $md5_cert_no = md5($req_num);
        $hash_data   = md5($mb_name.$cert_type.$mb_birth.$md5_cert_no);

        // 성인인증결과
        $adult_day = date("Ymd", strtotime("-19 years", time()));
        $adult = ((int)$mb_birth <= (int)$adult_day) ? 1 : 0;

        set_session('ss_cert_type',    $cert_type);
        set_session('ss_cert_no',      $md5_cert_no);
        set_session('ss_cert_hash',    $hash_data);
        set_session('ss_cert_adult',   $adult);
        set_session('ss_cert_birth',   $mb_birth);
        set_session('ss_cert_sex',     ($field[9] == 1 ? 'M' : 'F'));
        set_session('ss_cert_dupinfo', $mb_dupinfo);

        $tb['title'] = 'KCB 휴대폰 본인확인';
        $this->load->vars(array('tb'=>$tb));
        $this->template->header_sub();
        $data=array('cert_type'=>$cert_type,'mb_name'=>$mb_name,'phone_no'=>$phone_no,'md5_cert_no'=>$md5_cert_no);
        $this->load->view('plugin/okanme/hecert2',$data);
        $this->template->footer_sub();
    }
    private function kcpcert_form(){
        $member=$this->load->get_var('member');
        $config=$this->load->get_var('config');
        // 금일 인증시도 회수 체크
        $this->funcomplex_lib->certify_count_check($member['id'], 'hp');

        setlocale(LC_CTYPE, 'ko_KR.euc-kr');

        // kcp 휴대폰인증파일
        include_once('./plugin/kcpcert/kcpcert_config.php');

        $ordr_idxx = get_session('ss_uniqid');
        if(!$ordr_idxx)
            $ordr_idxx = get_uniqid();

        $ct_cert = new C_CT_CLI;
        $ct_cert->mf_clear();

        $year          = "00";
        $month         = "00";
        $day           = "00";
        $user_name     = "";
        $sex_code      = "";
        $local_code    = "";

        // !!up_hash 데이터 생성시 주의 사항
        // year , month , day 가 비어 있는 경우 "00" , "00" , "00" 으로 설정이 됩니다
        // 그외의 값은 없을 경우 ""(null) 로 세팅하시면 됩니다.
        // up_hash 데이터 생성시 site_cd 와 ordr_idxx 는 필수 값입니다.
        $hash_data = $site_cd   .
                    $ordr_idxx .
                    $user_name .
                    $year      .
                    $month     .
                    $day       .
                    $sex_code  .
                    $local_code;

        $up_hash = $ct_cert->make_hash_data( $home_dir, $hash_data );

        $ct_cert->mf_clear();
        $metaTag="";
        $inputTag="";
        if($this->agent->is_mobile()) {
            $metaTag="<meta name=\"viewport\" content=\"user-scalable=yes, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width, target-densitydpi=medium-dpi\" >";
            $inputTag="
            <!-- cert_able_yn input 비활성화 설정 -->
            <input type=\"hidden\" name=\"cert_able_yn\" value=\"\"/>";
        }
        $data=array('cert_url'=>$cert_url,'ordr_idxx'=>$ordr_idxx,'site_cd'=>$site_cd,'metaTag'=>$metaTag);
        $this->load->view('plugin/kcpcert_form',$data);
    }
    public function kcpcert_result(){
        include_once('./plugin/kcpcert/kcpcert_config.php');
        $site_cd       = "";
        $ordr_idxx     = "";

        $cert_no       = "";
        $cert_enc_use  = "";
        $enc_info      = "";
        $enc_data      = "";
        $req_tx        = "";

        $enc_cert_data = "";
        $cert_info     = "";

        $tran_cd       = "";
        $res_cd        = "";
        $res_msg       = "";

        $dn_hash       = "";

        /*------------------------------------------------------------------------*/
        /*  :: 전체 파라미터 남기기                                               */
        /*------------------------------------------------------------------------*/

        // request 로 넘어온 값 처리
        $key = array_keys($_POST);
        $sbParam ="";

        for($i=0; $i<count($key); $i++){
            $nmParam = $key[$i];
            $valParam = $_POST[$nmParam];

            if( $nmParam == "site_cd" ){
                $site_cd = f_get_parm_str( $valParam );
            }

            if( $nmParam == "ordr_idxx" ){
                $ordr_idxx = f_get_parm_str( $valParam );
            }

            if( $nmParam == "res_cd" ){
                $res_cd = f_get_parm_str ( $valParam );
            }

            if( $nmParam == "cert_enc_use" ){
                $cert_enc_use = f_get_parm_str ( $valParam );
            }

            if( $nmParam == "req_tx" ){
                $req_tx = f_get_parm_str ( $valParam );
            }

            if( $nmParam == "cert_no" ){
                $cert_no = f_get_parm_str ( $valParam );
            }

            if( $nmParam == "enc_cert_data" ){
                $enc_cert_data = f_get_parm_str ( $valParam );
            }

            if( $nmParam == "dn_hash" ){
                $dn_hash = f_get_parm_str ( $valParam );
            }

            // 부모창으로 넘기는 form 데이터 생성 필드
            $sbParam .= "<input type='hidden' name='" . $nmParam . "' value='" . f_get_parm_str( $valParam ) . "'/>";
        }

        $ct_cert = new C_CT_CLI;
        $ct_cert->mf_clear();


        $tb['title'] = '휴대폰인증 결과';
        $this->load->vars(array('tb'=>$tb));
        $this->template->header_sub();

        // 결과 처리
        $echoScript="";
        if( $cert_enc_use == "Y" ){
            // 인증내역기록
            $this->global_lib->insert_cert_history($member['id'], 'kcp', 'hp');

            if( $res_cd == "0000" ){
                // dn_hash 검증
                // KCP 가 리턴해 드리는 dn_hash 와 사이트 코드, 주문번호 , 인증번호를 검증하여
                // 해당 데이터의 위변조를 방지합니다
                $veri_str = $site_cd.$ordr_idxx.$cert_no; // 사이트 코드 + 주문번호 + 인증거래번호

                if( $ct_cert->check_valid_hash ( $home_dir , $dn_hash , $veri_str ) != "1" ){
                    // 검증 실패시 처리 영역
                    if(PHP_INT_MAX == 2147483647) // 32-bit
                        $bin_exe = '/bin/ct_cli';
                    else
                        $bin_exe = '/bin/ct_cli_x64';

                    $echoScript.= "dn_hash 변조 위험있음 (./plugin/kcperpt{$bin_exe} 파일에 실행권한이 있는지 확인하세요.)";
                    exit;
                    // 오류 처리 ( dn_hash 변조 위험있음)
                }

                // 가맹점 DB 처리 페이지 영역

                // 인증데이터 복호화 함수
                // 해당 함수는 암호화된 enc_cert_data 를
                // site_cd 와 cert_no 를 가지고 복화화 하는 함수 입니다.
                // 정상적으로 복호화 된경우에만 인증데이터를 가져올수 있습니다.
                $opt = "1" ; // 복호화 인코딩 옵션 ( UTF - 8 사용시 "1" )
                $ct_cert->decrypt_enc_cert( $home_dir , $site_cd , $cert_no , $enc_cert_data , $opt );

                $comm_id        = $ct_cert->mf_get_key_value("comm_id"    );                // 이동통신사 코드
                $phone_no       = $ct_cert->mf_get_key_value("phone_no"   );                // 전화번호
                $user_name      = $ct_cert->mf_get_key_value("user_name"  );                // 이름
                $birth_day      = $ct_cert->mf_get_key_value("birth_day"  );                // 생년월일
                $sex_code       = $ct_cert->mf_get_key_value("sex_code"   );                // 성별코드
                $local_code     = $ct_cert->mf_get_key_value("local_code" );                // 내/외국인 정보
                $ci             = $ct_cert->mf_get_key_value("ci"         );                // CI
                $di             = $ct_cert->mf_get_key_value("di"         );                // DI 중복가입 확인값
                $ci_url         = urldecode( $ct_cert->mf_get_key_value("ci"         ) );   // CI
                $di_url         = urldecode( $ct_cert->mf_get_key_value("di"         ) );   // DI 중복가입 확인값
                $dec_res_cd     = $ct_cert->mf_get_key_value("res_cd"     );                // 암호화된 결과코드
                $dec_mes_msg    = $ct_cert->mf_get_key_value("res_msg"    );                // 암호화된 결과메시지

                // 정상인증인지 체크
                if(!$phone_no)
                    alert_close("정상적인 인증이 아닙니다. 올바른 방법으로 이용해 주세요.");

                $phone_no = hyphen_hp_number($phone_no);
                $mb_dupinfo = $di;

                $sql = " select id from shop_member where id <> ? and mb_dupinfo = ?";
                $param=array($member['id'],$mb_dupinfo);
                $row = $this->query_model->returnOneArr($sql,$param);
                if($row['id']) {
                    alert_close("입력하신 본인확인 정보로 가입된 내역이 존재합니다.\\n회원아이디 : ".$row['id']);
                }

                // hash 데이터
                $cert_type = 'hp';
                $md5_cert_no = md5($cert_no);
                $hash_data   = md5($user_name.$cert_type.$birth_day.$md5_cert_no);

                // 성인인증결과
                $adult_day = date("Ymd", strtotime("-19 years", time()));
                $adult = ((int)$birth_day <= (int)$adult_day) ? 1 : 0;

                set_session("ss_cert_type",    $cert_type);
                set_session("ss_cert_no",      $md5_cert_no);
                set_session("ss_cert_hash",    $hash_data);
                set_session("ss_cert_adult",   $adult);
                set_session("ss_cert_birth",   $birth_day);
                set_session("ss_cert_sex",     ($sex_code=="01"?"M":"F"));
                set_session('ss_cert_dupinfo', $mb_dupinfo);
            }else if( $res_cd != "0000" ){
                // 인증실패
                alert_close('코드 : '.$_POST['res_cd'].'  '.urldecode($_POST['res_msg']));
                exit;
            }
        }else if( $cert_enc_use != "Y" ){
            // 암호화 인증 안함
            if( $this->agent->is_mobile() ){
                $echoScript.= '<script>';
                $echoScript.= 'window.parent.$("#cert_info").css("display", "");';
                $echoScript.= 'window.parent.$("#kcp_cert" ).css("display", "none");';
                $echoScript.= '</script>';
            } else {
                alert_close("휴대폰 본인확인을 취소 하셨습니다.");
            }
            exit;
        }

        $ct_cert->mf_clear();
        $data=array('echoScript'=>$echoScript,'sbParam'=>$sbParam,'cert_type'=>$cert_type,'user_name'=>$user_name,'phone_no'=>$phone_no,
                    'md5_cert_no'=>$md5_cert_no);
        $this->load->view('plugin/kcpcert/kcpcert_result',$data);
        $this->template->footer_sub();
    }
    private function AuthOnlyReq(){
        $member=$this->load->get_var('member');
        $config=$this->load->get_var('config');
        // 금일 인증시도 회수 체크
        $this->funcomplex_lib->certify_count_check($member['id'], 'hp');

        /*
        * [본인확인 요청페이지]
        *
        * 샘플페이지에서는 기본 파라미터만 예시되어 있으며, 별도로 필요하신 파라미터는 연동메뉴얼을 참고하시어 추가 하시기 바랍니다.
        */

        //LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
        if($config['cf_cert_use'] == 2)
            $CST_PLATFORM = 'service';
        else
            $CST_PLATFORM = 'test';
        $CST_MID                    = $config['cf_lg_mid'];				// 상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
                                                                        //테스트 아이디는 't'를 반드시 제외하고 입력하세요.
        $LGD_MID                    = (('test' == $CST_PLATFORM) ? 't':'').$CST_MID;  //상점아이디(자동생성)
        $LGD_BUYER                  = '홍길동';                         // 성명 (보안을 위해 DB난 세션에서 가져오세요)
        $LGD_BUYERSSN               = '000000';							// 주민등록번호 (보안을 위해 DB나 세션에서 가져오세요)
                                                                        // 휴대폰 본인인증을 사용할 경우 주민번호는 '0' 13자리를 넘기세요. 예)0000000000000
                                                                        // 기타 인증도 사용할 경우 실 주민등록번호 (보안을 위해 DB나 세션에 저장처리 권장)
        $LGD_MOBILE_SUBAUTH_SITECD  = '123456789abc';                               // 신용평가사에서 부여받은 회원사 고유 코드
                                                                        //  (CI값만 필요한 경우 옵션, DI값도 필요한 경우 필수)
        $LGD_TIMESTAMP              = date('YmdHis');                   // 타임스탬프 (YYYYMMDDhhmmss)
        $LGD_CUSTOM_SKIN            = 'red';                            // 상점정의 인증창 스킨 (red, blue, cyan, green, yellow)

        /*
        *************************************************
        * 2. MD5 해쉬암호화 (수정하지 마세요) - BEGIN
        *
        * MD5 해쉬암호화는 거래 위변조를 막기위한 방법입니다.
        *************************************************
        *
        * 해쉬 암호화 적용( LGD_MID + LGD_BUYERSSN + LGD_TIMESTAMP + LGD_MERTKEY )
        * LGD_MID          : 상점아이디
        * LGD_BUYERSSN     : 주민등록번호
        * LGD_TIMESTAMP    : 타임스탬프
        * LGD_MERTKEY      : 상점MertKey (mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
        *
        * MD5 해쉬데이터 암호화 검증을 위해
        * LG유플러스에서 발급한 상점키(MertKey)를 환경설정 파일(lgdacom/conf/mall.conf)에 반드시 입력하여 주시기 바랍니다.
        */

        $LGD_MERTKEY    = $config['cf_lg_mert_key'];
        $LGD_HASHDATA   = md5($LGD_MID.$LGD_BUYERSSN.$LGD_TIMESTAMP.$LGD_MERTKEY);
        $LGD_RETURNURL  = base_url().'plugin/lgxpay/returnurl';
        if( $this->agent->is_mobile() ){
            $LGD_WINDOW_TYPE = 'submit';
        } else {
            $LGD_WINDOW_TYPE = 'iframe';
        }

        $LGD_NAMECHECKYN = 'N';
        $LGD_HOLDCHECKYN = 'Y';
        $LGD_CUSTOM_USABLEPAY = 'ASC007';

        $payReqMap = array();

        $payReqMap['CST_PLATFORM']              = $CST_PLATFORM;           				// 테스트, 서비스 구분
        $payReqMap['CST_MID']                   = $CST_MID;                				// 상점아이디
        $payReqMap['LGD_MID']                   = $LGD_MID;                				// 상점아이디
        $payReqMap['LGD_HASHDATA'] 				= $LGD_HASHDATA;      	           		// MD5 해쉬암호값
        $payReqMap['LGD_BUYER']              	= $LGD_BUYER;							// 요청자 성명
        $payReqMap['LGD_BUYERSSN']              = $LGD_BUYERSSN;           				// 요청자 생년월일 / 사업자번호

        $payReqMap['LGD_NAMECHECKYN']           = $LGD_NAMECHECKYN;           			// 계좌실명확인여부
        $payReqMap['LGD_HOLDCHECKYN']           = $LGD_HOLDCHECKYN;           			// 휴대폰본인확인 SMS발송 여부
        $payReqMap['LGD_MOBILE_SUBAUTH_SITECD'] = $LGD_MOBILE_SUBAUTH_SITECD;           // 신용평가사에서 부여받은 회원사 고유 코드

        $payReqMap['LGD_CUSTOM_SKIN'] 			= $LGD_CUSTOM_SKIN;                		// 본인확인창 SKIN
        $payReqMap['LGD_TIMESTAMP'] 			= $LGD_TIMESTAMP;                  		// 타임스탬프
        $payReqMap['LGD_CUSTOM_USABLEPAY']      = $LGD_CUSTOM_USABLEPAY;        		// [반드시 설정]상점정의 이용가능 인증수단으로 한 개의 값만 설정 (예:"ASC007")
        $payReqMap['LGD_WINDOW_TYPE']           = $LGD_WINDOW_TYPE;        				// 호출방식 (수정불가)
        $payReqMap['LGD_RETURNURL'] 			= $LGD_RETURNURL;      			   		// 응답수신페이지
        $payReqMap['LGD_VERSION'] 				= "PHP_Non-ActiveX_AuthOnly";			// 사용타입 정보(수정 및 삭제 금지): 이 정보를 근거로 어떤 서비스를 사용하는지 판단할 수 있습니다.


        /*Return URL에서 인증 결과 수신 시 셋팅될 파라미터 입니다.*/
        $payReqMap['LGD_RESPCODE'] 				= "";
        $payReqMap['LGD_RESPMSG'] 				= "";
        $payReqMap['LGD_AUTHONLYKEY'] 			= "";
        $payReqMap['LGD_PAYTYPE'] 				= "";

        $_SESSION['lgd_certify'] = $payReqMap;

        /*
        *************************************************
        * 2. MD5 해쉬암호화 (수정하지 마세요) - END
        *************************************************
        */
        $httpsScript=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 'https' : 'http://xpay.uplus.co.kr/xpay/js/xpay_crossplatform.js';
        $lgwinjs="";
        if( $this->agent->is_mobile() ){   //모바일이면 
            $lgwinjs="lgdwin = open_paymentwindow(document.getElementById(lgd_form), '{$CST_PLATFORM}', LGD_window_type);";
        } else {  //PC 이면 
            $lgwinjs="lgdwin = openAuthonly( document.getElementById(lgd_form), \"{$CST_PLATFORM}\", LGD_window_type, null );";
        }
        $inputTags="";
        foreach ($payReqMap as $key => $value) {
            $key = htmlspecialchars(strip_tags($key), ENT_QUOTES);
            $value = htmlspecialchars(strip_tags($value), ENT_QUOTES);
            $inputTags.="<input type='hidden' name='$key' id='$key' value='$value'/>";
        }
        $data=array('LGD_WINDOW_TYPE'=>$LGD_WINDOW_TYPE,'httpsScript'=>$httpsScript,'lgwinjs'=>$lgwinjs,'inputTags'=>$inputTags);
        $this->load->view('plugin/lgxpay/authonlyreq',$data);
    }
    private function returnurl(){
        /*
        payreq_crossplatform 에서 세션에 저장했던 파라미터 값이 유효한지 체크
        세션 유지 시간(로그인 유지시간)을 적당히 유지 하거나 세션을 사용하지 않는 경우 DB처리 하시기 바랍니다.
        */
        if(!isset($_SESSION['lgd_certify'])){
            echo '세션이 만료 되었거나 유효하지 않은 요청 입니다.';
            return;
        }

        $payReqMap = $_SESSION['lgd_certify'];//결제 요청시, Session에 저장했던 파라미터 MAP
        $is_mobileJs="";
        if ( $this->agent->is_mobile() ) {  // 모바일이면 
            $is_mobileJs.="
            var lgd_form = document.getElementById('LGD_RETURNINFO');

            lgd_form.action = \"".base_url()."plguin/lgxpay/AuthOnlyRes\";
            lgd_form.submit();";
        } else {  // PC 이면
            $is_mobileJs.="parent.payment_return();";
        }
        $LGD_RESPCODEJs="";
        $LGD_RESPCODE = $_POST['LGD_RESPCODE'];
        $LGD_RESPMSG 	= iconv("EUC-KR","UTF-8",$_POST['LGD_RESPMSG']);
        $LGD_AUTHONLYKEY		= "";
        $LGD_PAYTYPE			= "";

        $payReqMap['LGD_RESPCODE'] = $LGD_RESPCODE;
        $payReqMap['LGD_RESPMSG']	 = $LGD_RESPMSG;

        if($LGD_RESPCODE == "0000"){
            $payReqMap['LGD_AUTHONLYKEY'] = isset($_POST['LGD_AUTHONLYKEY']) ? $_POST['LGD_AUTHONLYKEY'] : '';
            $payReqMap['LGD_PAYTYPE'] 	= isset($_POST['LGD_PAYTYPE']) ? $_POST['LGD_PAYTYPE'] : '';
        }
        else{
            $LGD_RESPCODEJs.="LGD_RESPCODE:" + $LGD_RESPCODE + " ,LGD_RESPMSG:" + $LGD_RESPMSG; //인증 실패에 대한 처리 로직 추가
        }
        $inputTags="";
        foreach ($payReqMap as $key => $value) {
            $key = htmlspecialchars(strip_tags($key));
            $value = htmlspecialchars(strip_tags($value));
            $inputTags.="<input type='hidden' name='$key' id='$key' value='$value'>";
        }
        $data=array('is_mobileJs'=>$is_mobileJs,'LGD_RESPCODEJs'=>$LGD_RESPCODEJs,'inputTags'=>$inputTags);
        $this->load->view('plugin/lgxpay/returnurl',$data);
    }
    public function AuthOnlyRes(){
        $config=$this->load->get_var('config');
        $_POST = array_map_deep('conv_unescape_nl', $_POST);

        /*
        * [본인확인 처리 페이지]
        *
        * LG유플러스으로 부터 내려받은 LGD_AUTHONLYKEY(인증Key)를 가지고 최종 인증요청.(파라미터 전달시 POST를 사용하세요)
        */

        /*
        *************************************************
        * 1.최종인증 요청 - BEGIN
        *************************************************
        */

        //LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
        if($config['cf_cert_use'] == 2)
            $CST_PLATFORM = 'service';
        else
            $CST_PLATFORM = 'test';
        $CST_MID                = $config['cf_lg_mid'];								//상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
                                                                                    //테스트 아이디는 't'를 반드시 제외하고 입력하세요.
        $LGD_MID                = (('test' == $CST_PLATFORM) ? 't' : '').$CST_MID;  //상점아이디(자동생성)
        $LGD_AUTHONLYKEY        = $_POST['LGD_AUTHONLYKEY'];			            //LG유플러스으로부터 부여받은 인증키
        $LGD_PAYTYPE  			= $_POST['LGD_PAYTYPE'];				            //인증요청타입 (신용카드:ASC001, 휴대폰:ASC002, 계좌:ASC004)

        require_once('./plugin/lgxpay/lgdacom/XPayClient.php');

        // mall.conf 설정 추가를 위한 XPayClient 확장
        class XPay extends XPayClient
        {
            public function set_config_value($key, $val)
            {
                $this->config[$key] = $val;
            }
        }

        $configPath = './plugin/lgxpay/lgdacom'; //LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf,/conf/mall.conf") 위치 지정.

        $xpay = new XPay($configPath, $CST_PLATFORM);

        // Mert Key 설정
        $xpay->set_config_value('t'.$LGD_MID, $config['cf_lg_mert_key']);
        $xpay->set_config_value($LGD_MID, $config['cf_lg_mert_key']);

        $xpay->Init_TX($LGD_MID);
        $xpay->Set("LGD_TXNAME", "AuthOnlyByKey");
        $xpay->Set("LGD_AUTHONLYKEY", $LGD_AUTHONLYKEY);
        $xpay->Set("LGD_PAYTYPE", $LGD_PAYTYPE);

        $tb['title'] = '휴대폰인증 결과';
        $this->load->vars(array('tb'=>$tb));
        $this->template->header_sub();
        /*
        *************************************************
        * 1.최종인증 요청(수정하지 마세요) - END
        *************************************************
        */

        /*
        * 2. 최종인증 요청 결과처리
        *
        * 최종 인증요청 결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
        */
        $xpayJs="";
        if ($xpay->TX()) {
            //1)인증결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)

            /*
            echo "인증요청이 완료되었습니다.  <br>";
            echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
            echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";

            $keys = $xpay->Response_Names();
            foreach($keys as $name) {
                echo $name . " = " . $xpay->Response($name, 0) . "<br>";
            }

            echo "</p>";
            */

            if( "0000" == $xpay->Response_Code() ) {
                //인증요청 결과 성공 DB처리
                //echo "인증요청 결과 성공 DB처리하시기 바랍니다.<br>";

                $cert_no        = $xpay->Response('LGD_TID', 0);                      // LG 인증처리번호
                $comm_id        = $xpay->Response('LGD_FINANCECODE', 0);              // 이동통신사 코드
                $phone_no       = $xpay->Response('LGD_MOBILENUM', 0);                // 전화번호
                $user_name      = $xpay->Response('LGD_MOBILE_SUBAUTH_NAME', 0);      // 이름
                $birth_day      = $xpay->Response('LGD_MOBILE_SUBAUTH_BIRTH', 0);     // 생년월일
                $sex_code       = $xpay->Response('LGD_MOBILE_SUBAUTH_SEX', 0);       // 성별코드
                $ci             = $xpay->Response('LGD_AUTHSUB_CI', 0);               // CI
                $di             = $xpay->Response('LGD_AUTHSUB_DI', 0);               // DI 중복가입 확인값

                // 내/외국인
                if($sex_code > 4)
                    $local_code = 2; // 외국인
                else
                    $local_code = 1; // 내국인

                // 남/여구분
                if($sex_code % 2 == 0)
                    $mb_sex = 'F';
                else
                    $mb_sex = 'M';

                // 생년월일
                if($sex_code < 5) {
                    if($sex_code <= 2)
                        $birth_prefix = '19';
                    else
                        $birth_prefix = '20';
                } else {
                    if($sex_code <= 6)
                        $birth_prefix = '19';
                    else
                        $birth_prefix = '20';
                }
                $birth_day = $birth_prefix.$birth_day;

                // 정상인증인지 체크
                if(!$phone_no)
                    alert_close("정상적인 인증이 아닙니다. 올바른 방법으로 이용해 주세요.");

                $phone_no = hyphen_hp_number($phone_no);
                $mb_dupinfo = $di;

                if($mb_dupinfo) {
                    $sql = " select id from shop_member where id <> ? and mb_dupinfo = ?";
                    $param=array($member['id'],$mb_dupinfo);
                    $row = $this->query_model->returnOneArr($sql,$param);
                    if ($row['id']) {
                        alert_close("입력하신 본인확인 정보로 가입된 내역이 존재합니다.\\n회원아이디 : ".$row['id']);
                    }
                }

                // hash 데이터
                $cert_type = 'hp';
                $md5_cert_no = md5($cert_no);
                $hash_data   = md5($user_name.$cert_type.$birth_day.$md5_cert_no);

                // 성인인증결과
                $adult_day = date("Ymd", strtotime("-19 years", time()));
                $adult = ((int)$birth_day <= (int)$adult_day) ? 1 : 0;

                set_session("ss_cert_type",    $cert_type);
                set_session("ss_cert_no",      $md5_cert_no);
                set_session("ss_cert_hash",    $hash_data);
                set_session("ss_cert_adult",   $adult);
                set_session("ss_cert_birth",   $birth_day);
                set_session("ss_cert_sex",     $mb_sex);
                set_session('ss_cert_dupinfo', $mb_dupinfo);

                // 인증내역기록
                $this->global_lib->insert_cert_history($member['id'], 'lg', 'hp');

            } else {
                //인증요청 결과 실패 DB처리
                //echo "인증요청 결과 실패 DB처리하시기 바랍니다.<br>";

                if( $this->agent->is_mobile() ){
                    $xpayJs.='<script>';
                    $xpayJs.= 'window.parent.$("#cert_info").css("display", "");';
                    $xpayJs.= 'window.parent.$("#lgu_cert" ).css("display", "none");';
                    $xpayJs.= 'alert("인증요청이 취소 또는 실패하였습니다.\\n\\n코드 : '.$xpay->Response_Code().'  '.$xpay->Response_Msg().'")';
                    $xpayJs.= '</script>';
                } else {
                    alert_close('인증요청이 취소 또는 실패하였습니다.\\n\\n코드 : '.$xpay->Response_Code().'  '.$xpay->Response_Msg());
                }
                exit;
            }
        } else {
            //2)API 요청실패 화면처리
            /*
            echo "인증요청이 실패하였습니다.  <br>";
            echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
            echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";

            //인증요청 결과 실패 DB처리
            echo "인증요청 결과 실패 DB처리하시기 바랍니다.<br>";
            */

            if( $this->agent->is_mobile() ){
                $xpayJs.= '<script>';
                $xpayJs.= 'window.parent.$("#cert_info").css("display", "");';
                $xpayJs.= 'window.parent.$("#lgu_cert" ).css("display", "none");';
                $xpayJs.= 'alert("인증요청이 실패하였습니다.\\n\\n코드 : '.$xpay->Response_Code().'  '.$xpay->Response_Msg().'")';
                $xpayJs.= '</script>';
            } else {
                alert_close('인증요청이 실패하였습니다.\\n\\n코드 : '.$xpay->Response_Code().'  '.$xpay->Response_Msg());
            }
            exit;
        }
        $data=array('xpayJs'=>$xpayJs,'cert_type'=>$cert_type,'user_name'=>$user_name,'phone_no'=>$phone_no,'md5_cert_no'=>$md5_cert_no);
        $this->load->view('plugin/lgxpay/authonlyres',$data);
        $this->template->footer_sub();
    }
}