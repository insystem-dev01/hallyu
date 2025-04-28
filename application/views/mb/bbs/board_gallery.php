<script src="<?php echo base_url(); ?>mb/js/masonry.pkgd.js"></script>
<script src="<?php echo base_url(); ?>mb/js/imagesloaded.pkgd.min.js"></script>
<?=$filurl1Tag?>
<div class="m_gall">
    <?=$useCateTag?>
    <?=$boardTb?>
    <?=$pagination?>
    <?=$can_write?>
    <form name="searchform" method="get">
        <input type="hidden" name="boardid" value="<?php echo $boardid; ?>">
        <div class="bottom_sch">
            <select name="sfl">
                <?=$searchOpt?>
            </select>
            <input type="text" name="stx" class="frm_input" value="<?php echo $stx; ?>">
            <input type="submit" value="검색" class="btn_small grey">
        </div>
	</form>
</div>
<script>
/* Mobile 브라우져 체크 함수 */
function mobile_chk(){
	var user_device = navigator.userAgent.toLowerCase();
	var mobile_device = new Array('iphone','ipad', 'firefox', 'android');
	for(var i=0;i<mobile_device.length;i++){
		if(user_device.indexOf(mobile_device[i]) != -1)	return true;
	}
	return false;
}

var $masgall = $('#m_gall_ul').masonry({
	itemSelector: '.gall_item',
	percentPosition: true,
	columnWidth: '.gall_item'
});
// layout Masonry after each image loads
$masgall.imagesLoaded().progress( function() {
	$masgall.masonry();
});
</script>
