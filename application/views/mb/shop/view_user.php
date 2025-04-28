<h2 class="pop_title">
	<?php echo $title; ?> <span class="fc_red">(<?php echo number_format($total_count); ?>)</span>
	<a href="javascript:cl_list();" class="btn_small bx-white">전체상품보기</a>
</h2>
<div id="sit_review">
	<table class="tbl_review">
        <colgroup>
            <col width="80px">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <td class="image"><?php echo $reviewImg; ?></td>
                <td class="gname">
                    <?php echo $gsName; ?>
                    <p class="bold mart5"><?php echo $mbPrice; ?></p>
                </td>
            </tr>
	    </tbody>
	</table>
    <?=$view_userTable?>
    <?=$pagination?>
    <div class="btn_confirm">
		<a href="javascript:window.open('<?php echo base_url(); ?>m/shop/orderreview?gs_id=<?php echo $gs_id; ?>');" class="btn_medium">구매후기쓰기</a>
		<a href="javascript:window.close();" class="btn_medium bx-white">창닫기</a>
	</div>
</div>
<script>
function cl_list(){
    var base_url=document.getElementById('base_url').value;
	opener.location.href = base_url+'m/shop/cateList?ca_id=<?php echo $gs['ca_id']; ?>';
	window.close();
}

// 삭제
$(function(){
    $(".itemqa_delete").click(function(){
        return confirm("정말 삭제 하시겠습니까?\n\n삭제후에는 되돌릴수 없습니다.");
    });
});
</script>
