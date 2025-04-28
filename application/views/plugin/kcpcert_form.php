<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <?= $metaTag ?>
    </head>
    <body oncontextmenu="return false;" ondragstart="return false;" onselectstart="return false;">
    <form name="form_auth" method="post" action="<?php echo $cert_url ?>">
        <!-- 유저네임 -->
        <input type="hidden" name="user_name"    value="" />
        <!-- 주문번호 -->
        <input type="hidden" name="ordr_idxx"    value="<?php echo $ordr_idxx ?>">
        <!-- 요청종류 -->
        <input type="hidden" name="req_tx"       value="cert"/>
        <!-- 인증종류 -->
        <input type="hidden" name="cert_type"    value="01"/>
        <!-- 웹사이트아이디 -->
        <input type="hidden" name="web_siteid"   value=""/>
        <!-- 노출 통신사 default 처리시 아래의 주석을 해제하고 사용하십시요
            SKT : SKT , KT : KTF , LGU+ : LGT
        <input type="hidden" name="fix_commid"      value="KTF"/>
        -->
        <!-- 사이트코드 -->
        <input type="hidden" name="site_cd"      value="<?php echo $site_cd; ?>" />
        <!-- Ret_URL : 인증결과 리턴 페이지 ( 가맹점 URL 로 설정해 주셔야 합니다. ) -->
        <input type="hidden" name="Ret_URL"      value="<?php echo base_url(); ?>pluginFun/kcpcert_result" />
        <!-- cert_otp_use 필수 ( 메뉴얼 참고)
            Y : 실명 확인 + OTP 점유 확인 , N : 실명 확인 only
        -->
        <input type="hidden" name="cert_otp_use" value="Y"/>
        <!-- cert_enc_use 필수 (고정값 : 메뉴얼 참고) -->
        <input type="hidden" name="cert_enc_use" value="Y"/>

        <?=$inputTag  ?>

        <input type="hidden" name="res_cd"       value=""/>
        <input type="hidden" name="res_msg"      value=""/>

        <input type="hidden" name="up_hash" value="<?php echo $up_hash; ?>"/>

        <!-- up_hash 검증 을 위한 필드 -->
        <input type="hidden" name="veri_up_hash" value=""/>

        <!-- 가맹점 사용 필드 (인증완료시 리턴)-->
        <input type="hidden" name="param_opt_1"  value="opt1"/>
        <input type="hidden" name="param_opt_2"  value="opt2"/>
        <input type="hidden" name="param_opt_3"  value="opt3"/>
    </form>

    <script>
    window.onload = function() {
        cert_page();
    }

    // 인증 요청 시 호출 함수
    function cert_page(){
        var frm = document.form_auth;

        if( ( frm.req_tx.value == "auth" || frm.req_tx.value == "otp_auth" ) ){
            frm.action="./kcpcert_result.php";

        // MOBILE
            if( ( navigator.userAgent.indexOf("Android") > - 1 || navigator.userAgent.indexOf("iPhone") > - 1 ) ){
                self.name="kcp_cert";
            }
            // PC
            else{
                frm.target="kcp_cert";
            }

            frm.submit();

            window.close();
        }else if( frm.req_tx.value == "cert" ){
            if( ( navigator.userAgent.indexOf("Android") > - 1 || navigator.userAgent.indexOf("iPhone") > - 1 ) ) // 스마트폰인 경우
            {
                window.parent.$("input[name=veri_up_hash]").val(frm.up_hash.value); // up_hash 데이터 검증을 위한 필드
                self.name="auth_popup";
            }
            else // 스마트폰 아닐때
            {
                window.opener.$("input[name=veri_up_hash]").val(frm.up_hash.value); // up_hash 데이터 검증을 위한 필드
                frm.target = "auth_popup";
            }

            frm.action="<?php echo $cert_url; ?>";
            frm.submit();
        }
    }
    </script>

    </body>
</html>