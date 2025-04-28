<?=$headTitle?>
<h2>통계검색</h2>
<form name="fsearch" id="fsearch" method="get">
    <input type="hidden" name="code" value="<?php echo $code; ?>">
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w120">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">기간검색</th>
                    <td>
                        <select name="year">
                            <?=$yearList?>
                        </select>
                        <select name="month">
                            <?=$monthList?>
                        </select>
                        <input type="submit" value="검색" class="btn_small">
                    </td>
                </tr>
	        </tbody>
	    </table>
    </div>
</form>
<div class="local_ov mart20">
	총 회원수 : <b><?php echo number_format($tot_count1); ?></b>명
	<span class="ov_a">
		일반회원 : <b><?php echo number_format($tot_count2); ?></b>명
	</span>
</div>
<div class="tbl_head01">
	<table>
        <colgroup>
            <col class="w100">
            <col>
            <col class="w60">
            <col class="w80">
            <col class="w80">
        </colgroup>
        <thead>
            <tr>
                <th scope="col">날짜</th>
                <th scope="col">그래프</th>
                <th scope="col">비율%</th>
                <th scope="col">전체</th>
                <th scope="col">일반</th>
            </tr>
        </thead>
        <tbody class="list">
            <?=$dayList?>
        </tbody>
	</table>
</div>
<?=$headTitleEnd?>
