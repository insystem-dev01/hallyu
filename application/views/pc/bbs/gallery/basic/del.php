<?=$b_content_head?>
<form name="fboardform" action="<?=base_url()?>process/del" method="post" onsubmit="return fboardform_submit(this);">
    <input type='hidden' name="index_no" value="<?php echo $index_no; ?>">
    <input type="hidden" name="boardid" value="<?php echo $boardid; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="mode" value="d">
    <?=$delMsg?>
</form>
<script>
function fboardform_submit(){
	<?=$pwdJs?>
	if(answer==true){	
        return true;	
    }else{	
        return false;	
    }
}
</script>
<?=$b_content_tail?>