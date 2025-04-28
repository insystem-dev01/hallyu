<script>
    function request(){
        //window.name = "<?php echo $targetId; ?>";

        document.form1.action = "<?php echo $commonSvlUrl; ?>";
        document.form1.method = "post";

        document.form1.submit();
    }
</script>

<form name="form1">
    <!-- 인증 요청 정보 -->
    <!--// 필수 항목 -->
    <input type="hidden" name="tc" value="kcb.oknm.online.safehscert.popup.cmd.P901_CertChoiceCmd"> <!-- 변경불가-->
    <input type="hidden" name="rqst_data"				value="<?php echo $e_rqstData; ?>">		    <!-- 요청데이터 -->
    <input type="hidden" name="target_id"				value="<?php echo $targetId; ?>">		    <!-- 타겟ID -->
    <!-- 필수 항목 //-->
</form>
<?=$retcodeTag?>