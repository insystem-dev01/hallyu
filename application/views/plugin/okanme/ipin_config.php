<form name="kcbInForm" method="post" action="<?php echo $kcbForm_action; ?>">
    <input type="hidden" name="IDPCODE" value="<?php echo $idpCode; ?>" />
    <input type="hidden" name="IDPURL" value="<?php echo $idpUrl; ?>" />
    <input type="hidden" name="CPCODE" value="<?php echo $cpCode; ?>" />
    <input type="hidden" name="CPREQUESTNUM" value="<?php echo $curtime; ?>" />
    <input type="hidden" name="RETURNURL" value="<?php echo $returnUrl; ?>" />
    <input type="hidden" name="WEBPUBKEY" value="<?php echo $pubkey; ?>" />
    <input type="hidden" name="WEBSIGNATURE" value="<?php echo $sig; ?>" />
</form>

<script>
document.kcbInForm.submit();
</script>