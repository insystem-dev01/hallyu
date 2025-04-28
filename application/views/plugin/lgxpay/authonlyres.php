<script>
jQuery(function($) {

    var $opener = window.opener;
    var is_mobile = false;

    if (typeof om_is_mobile != "undefined" && om_is_mobile ) {
        $opener = window.parent;
        is_mobile = true;
    } else {
        $opener = window.opener;
    }

    // 인증정보
    $opener.$("input[name=cert_type]").val("<?php echo $cert_type; ?>");
    $opener.$("input[name=mb_name]").val("<?php echo $user_name; ?>").attr("readonly", true);
    $opener.$("input[name=mb_hp]").val("<?php echo $phone_no; ?>").attr("readonly", true);
    $opener.$("input[name=cert_no]").val("<?php echo $md5_cert_no; ?>");

    if(is_mobile) {
        $opener.$("#cert_info").css("display", "");
        $opener.$("#lgu_cert" ).css("display", "none");
    }

    alert("본인의 휴대폰번호로 확인 되었습니다.");
    window.close();
});
</script>