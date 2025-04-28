<?=$aside_my?>
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>고객센터<i>&gt;</i><?php echo $title; ?></p>
	</h2>
    <p class="pg_cnt">
		<em>총 <?php echo number_format($total_count); ?>건</em>의 상품평이 있습니다.
	</p>
    <div class="tbl_head02 tbl_wrap">
		<table>
            <colgroup>
                <col width="50">
                <col width="70">
                <col>
                <col width="90">
                <col width="90">
                <col width="90">
                <col width="90">
            </colgroup>
            <thead>
            <tr>
                <th scope="col">번호</th>
                <th scope="col">이미지</th>
                <th scope="col">상품평</th>
                <th scope="col">작성자</th>
                <th scope="col">작성일</th>
                <th scope="col">평점</th>
                <th scope="col">수정</th>
            </tr>
            </thead>
            <tbody>
                <?=$reviewTb?>
            </tbody>
		</table>
	</div>
    <?=$pagination?>
</div>