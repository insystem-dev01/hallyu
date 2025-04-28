<a name="it_qa"></a>
<form name="f_search" method="post">
    <table class="wfull marb5">
        <tr>
            <td class="tal">총 <b class="fc_red"><?php echo $total_count; ?></b>개의 문의가 있습니다.</td>
            <td class="tar">
                <a href="<?php echo base_url(); ?>shop/qaform?gs_id=<?php echo $index_no; ?>" onclick="win_open(this,'qaform','600','600','yes');return false" class="btn_lsmall grey">상품Q&A작성</a>
                <a href="<?php echo base_url(); ?>bbs/qna_list" target="_blank" class="btn_lsmall grey">고객센터 문의하기</a>
            </td>
        </tr>
    </table>
</form>
<div class="tbl_head01 tbl_wrap">
	<table>
        <colgroup>
            <col width="8%">
            <col width="14%">
            <col width="6%">
            <col width="2%">
            <col>
            <col width="12%">
            <col width="14%">
        </colgroup>
        <thead>
            <tr>
                <th scope="col">번호</th>
                <th scope="col">문의유형</th>
                <th scope="col" colspan="3">문의/답변</th>
                <th scope="col">작성자</th>
                <th scope="col">작성일</th>
            </tr>
        </thead>
	</table>
    <?=$tableTag?>
</div>
<?=$pagination?>
<script>
function js_qna(id){
	var $con = $("#sod_qa_con_"+id);
	if($con.is(":visible")) {
		$con.hide(200);
	} else {
		$(".sod_qa_con:visible").hide();
		$con.show(200);
	}
}

$(function(){
    $(".itemqa_delete").click(function(){
        return confirm("정말 삭제 하시겠습니까?\n\n삭제후에는 되돌릴수 없습니다.");
    });
});
</script>
