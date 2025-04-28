<?=$fileurl1Tag?>
<div class="m_bo_bg">
    <?=$useCateTag?>
    <?=$tableTag?>
    <?=$canWrite?>
    <?=$pagination?>
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
