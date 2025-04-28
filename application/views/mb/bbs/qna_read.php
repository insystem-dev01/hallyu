<div class="m_bo_bg">
	<div class="title"><?php echo $qa['subject']; ?></div>
	<div class="wr_name">
		<?php echo $qa['mb_id']; ?><span class="wr_day"><?php echo substr($qa['wdate'],0,10); ?></span>
	</div>
	<div class="wr_txt">
		<?php echo nl2br(htmlspecialchars($qa['memo'])); ?>
	</div>
    <?=$resultYes?>
    <div class="btn_confirm">
		<a href="<?php echo base_url(); ?>m/bbs/qna_write" class="btn_medium">상담문의</a>		
		<a href="<?php echo base_url(); ?>m/bbs/qna_modify?index_no=<?php echo $index_no; ?>" class="btn_medium bx-white">수정</a>
		<a href="<?php echo base_url(); ?>m/bbs/qna_list" class="btn_medium bx-white">목록</a>
        <a href="javascript:del('<?php echo base_url(); ?>m/process/qna_read_delete?index_no=<?php echo $index_no; ?>&mode=d');" class="btn_medium bx-white">삭제</a>
	</div>
</div>
<script>
function del(url) {
	answer = confirm('삭제 하시겠습니까?');
	if(answer==true) { 
		location.href = url; 
	}
}
</script>