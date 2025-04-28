<?=$headTitle?>
<?=$jqueryUiDatePick?>
<h2>통계검색</h2>
<form name="fsearch" id="fsearch" method="get">
    <input type="hidden" name="code" value="<?php echo $code; ?>">
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w100">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row"><label for="sch_sort">검색어</label></th>
                    <td>
                        <select name="sfl" id="sch_sort">
                            <?php echo $sch_sortArr[0]; ?>
                            <?php echo $sch_sortArr[1]; ?>
                        </select>
                        <label for="sch_word" class="sound_only">검색어</label>
                        <input type="text" name="stx" value="<?php echo stripslashes($stx); ?>" id="sch_word" class="frm_input">	
                    </td>
                </tr>
                <tr>
                    <th scope="row">기간검색</th>
                    <td>
                        <?php echo $get_search_date; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="검색" class="btn_medium">
        <input type="button" value="초기화" id="frmRest" class="btn_medium grey">
    </div>
</form>
<div class="local_ov mart30">
	총 접속자수 : <b class="fc_red"><?php echo number_format($total_count); ?></b>건
</div>
<div class="tbl_head01">
	<table>
        <colgroup>
            <col class="w120">
            <col>
            <col class="w100">
            <col class="w100">
            <col class="w130">
        </colgroup>
        <thead>
            <tr>
                <th scope="col">IP</th>
                <th scope="col">접속경로</th>
                <th scope="col">브라우저</th>
                <th scope="col">OS</th>
                <th scope="col">일시</th>	
            </tr>
        </thead>
        <?=$visit_searchTable?>
        </tbody>
	</table>
</div>
<?=$pagination?>
<script>
$(function(){
	// 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
	$("#fr_date,#to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>
<?=$headTitleEnd?>