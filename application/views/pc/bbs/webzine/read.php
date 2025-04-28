<?=$b_content_head?>
<div class="tbl_frm01 tbl_wrap">
	<table>
        <tbody>
            <tr>
                <td class="list1 fs14"><b><?php echo $bo_subject; ?></b></td>
            </tr>
            <tr>
                <td class="list1 tal"><b><?php echo $bo_writer_s; ?></b> <?=$bo_writer_id?>, <b>작성일</b> : <?php echo $bo_wdate; ?>, <b>조회수</b> : <?php echo $bo_hit; ?></td>
            </tr>
            <?=$bo_file1Tag?>
            <?=$bo_file2Tag?>
            <tr>
		        <td class="memo" style="height:200px;vertical-align:top;">
                <?=$bo_file1Str?>
                <?=$bo_file2Str?>
                <?=$thumb?>
                </td>
            </tr>
        </tbody>
	</table>
</div>
<div class="page_wrap">
	<div class="lbt_box">
		<a href="<?=base_url()?>bbs/noticeList?<?php echo $qstr1; ?>" class="btn_lsmall bx-white">목록</a>
	</div>
    <div class="rbt_box">
		<?=$webModify?>
		<?=$webReply?>
		<?=$webDel?>
		<?=$webWrite?>
	</div>
</div>
<?=$webComment?>
<script>
$(".memo").css({ 'pointer-events': 'none' });
function fboardform_submit(f){
    <?=$fboardformJs?>
    if(!f.memo.value) {
		alert('댓글을 작성하지 않았습니다!');
		f.memo.focus();
		return false;
	}

	return true;
}
function imgview(img) {
    var base_url=document.getElementById('base_url').value;
    window.open(base_url+"bbs/imgviewer?img="+img,"img",'width=150,height=150,status=no,top=0,left=0,scrollbars=yes');
}
</script>
<?=$read_list?>
<?=$b_content_tail?>