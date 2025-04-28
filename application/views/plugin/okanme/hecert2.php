<script>
$(function() {
    var $opener = window.opener;

    $opener.$("input[name=cert_type]").val("<?php echo $cert_type; ?>");
    $opener.$("input[name=mb_name]").val("<?php echo $mb_name; ?>").attr("readonly", true);
    $opener.$("input[name=mb_hp]").val("<?php echo $phone_no; ?>").attr("readonly", true);
    $opener.$("input[name=cert_no]").val("<?php echo $md5_cert_no; ?>");

    alert("본인의 휴대폰번호로 확인 되었습니다.");
    window.close();
});
</script>