<div class="m_bo_bg mart10">
	<div class="title"><?php echo $bo_subject; ?></div>
	<div class="wr_name"><?php echo $write['writer_s']; ?><span class="wr_day"><?php echo $bo_wdate; ?></span></div>
	<div class="wr_txt">
        <?=$file1Tag?>
        <?=$file2Tag?>
        <p><?=$memo?></p>
    </div>
</div>
<div class="btn_confirm">
	<a href="<?php echo base_url(); ?>m/bbs/board_list?<?php echo $qstr1; ?>" class="btn_medium bx-white">목록</a>
	<?=$replyTag?>
    <?=$adminTag?>
</div>