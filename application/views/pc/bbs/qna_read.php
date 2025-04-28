<?=$aside_cs?>
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>고객센터<i>&gt;</i><?php echo $title; ?></p>
	</h2>
    <div class="tbl_frm01 tbl_wrap">
		<table>
            <colgroup>
                <col class="w100">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">제목</th>
                    <td><?php echo $qa['subject']; ?></td>
                </tr>
                <tr>
                    <th scope="row">내용</th>
                    <td style="height:150px;vertical-align:top;"><?php echo nl2br(htmlspecialchars($qa['memo'])); ?></td>
                </tr>
            </tbody>
		</table>
	</div>
    <?=$result_yes?>
    <div class="btn_confirm">
		<a href="<?php echo base_url(); ?>bbs/qna_write" class="btn_lsmall">상담문의하기</a>
		<a href="<?php echo base_url(); ?>bbs/qna_modify?index_no=<?php echo $index_no; ?>" class="btn_lsmall bx-white">수정</a>
		<a href="<?php echo base_url(); ?>bbs/qna_list" class="btn_lsmall bx-white">목록</a>
		<a href="javascript:del('<?php echo base_url(); ?>process/qna_del?index_no=<?php echo $index_no; ?>&mode=d');" class="btn_lsmall bx-white">삭제</a>
	</div>
</div>

<script>
function del(url) {
	answer = confirm("삭제 하시겠습니까?");
	if(answer==true) {
		location.href = url;
	}
}
</script>