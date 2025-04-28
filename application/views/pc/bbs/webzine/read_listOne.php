<div class="webzine">
    <?=$read_list_tag?>
</div>
<div class="page_wrap">
	<div class="lbt_box">
		<a href="<?=base_url()?>bbs/noticeList?<?php echo $qstr1; ?>" class="btn_lsmall bx-white">목록</a>
	</div>
    <?=$writeTag?>
</div>
<form name="searchform" method="get">
    <input type="hidden" name="boardid" value="<?php echo $boardid; ?>">
    <div class="bottom_sch">
        <select name="sfl">
            <?=$gw_search_opt?>
        </select>
        <input type="text" name="stx" class="frm_input" value="<?php echo $stx; ?>">
        <input type="submit" value="검색" class="btn_lsmall grey">
    </div>
</form>
<?=$pagination?>