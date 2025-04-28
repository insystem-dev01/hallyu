<?=$echoScript?>
<form name="form_auth" method="post">
    <?php echo $sbParam; ?>
</form>

<script>
$(function() {
    var $opener;
    var is_mobile = false;

    if( ( navigator.userAgent.indexOf("Android") > - 1 || navigator.userAgent.indexOf("iPhone") > - 1 ) ) { // 스마트폰인 경우
        $opener = window.parent;
        is_mobile = true;
    } else {
        $opener = window.opener;
    }

    // up_hash 검증
    if( document.form_auth.up_hash.value != $opener.$("input[name=veri_up_hash]").val() ) {
        alert("up_hash 변조 위험있음");
    }

    // 인증정보
    $opener.$("input[name=cert_type]").val("<?php echo $cert_type; ?>");
    $opener.$("input[name=mb_name]").val("<?php echo $user_name; ?>").attr("readonly", true);
    $opener.$("input[name=mb_hp]").val("<?php echo $phone_no; ?>").attr("readonly", true);
    $opener.$("input[name=cert_no]").val("<?php echo $md5_cert_no; ?>");

    if(is_mobile) {
        $opener.$("#cert_info").css("display", "");
        $opener.$("#kcp_cert" ).css("display", "none");
    }

    alert("본인의 휴대폰번호로 확인 되었습니다.");

    window.close();
});
</script>
