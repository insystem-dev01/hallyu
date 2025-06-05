<!-- <div class="tbl_head01 tbl_wrap mart30">
	<table>
        <colgroup>
            <col width="50">
            <col>
            <col width="90">
            <col width="50">
            <col width="50">
            <col width="90">
        </colgroup>
        <thead>
            <tr>
                <th scope="col">번호</th>
                <th scope="col">제목</th>
                <th scope="col">작성자</th>
                <th scope="col">파일</th>
                <th scope="col">조회</th>
                <th scope="col">등록일</th>
            </tr>
        </thead>
	    <tbody>
            <?=$read_list_tag?>
        </tbody>
	</table>
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
<?=$pagination?> -->
