<?=$aside_cs?>
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>고객센터<i>&gt;</i><?php echo $title; ?></p>
	</h2>

	<div class="tar marb5">
		<a href="<?php echo base_url();?>bbs/qna_write" class="btn_small wset">상담문의하기</a>
	</div>
    <div class="tbl_head02 tbl_wrap">
		<table>
            <colgroup>
                <col class="w50">
                <col class="w100">
                <col>
                <col class="w100">
                <col class="w100">
                <col class="w80">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col">분류</th>
                    <th scope="col">제목</th>
                    <th scope="col">작성자</th>
                    <th scope="col">날짜</th>
                    <th scope="col">상태</th>
                </tr>
            </thead>
            <tbody>
                <?=$qna_table?>
            </tbody>
        </table>
	</div>
    <?=$pagination?>
</div>
<script>
function js_qna(id){
	var $con = $("#sod_qa_con_"+id);
	if($con.is(":visible")) {
		$con.hide();
	} else {
		$(".sod_qa_con:visible").hide();
		$con.show();
	}
}
</script>