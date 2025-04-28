<?=$headTitle?>
<?=$jqueryUiDatePick?>
<h2>기본검색</h2>
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
                    <th scope="row">검색어</th>
                    <td>
                        <select name="sfl">
                            <?php echo $sflArr['id']; ?>
                            <?php echo $sflArr['name']; ?>
                            <?php echo $sflArr['old_id']; ?>
                            <?php echo $sflArr['new_id']; ?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">적용일</th>
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
	전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 건 조회
</div>
<div class="tbl_head01">
	<table>
        <colgroup>
            <col class="w50">
            <col class="w130">
            <col class="w130">
            <col class="w130">
            <col class="w130">
            <col class="w130">
            <col class="w130">
            <col>
        </colgroup>
        <thead>
            <tr>
                <th scope="col">번호</th>
                <th scope="col"><?php echo $bName; ?>회원명</a></th>
                <th scope="col"><?php echo $aMb_id; ?>아이디</a></th>
                <th scope="col"><?php echo $bGrade; ?>레벨</a></th>
                <th scope="col"><?php echo $aReg_time; ?>적용일시</a></th>
                <th scope="col" class="th_bg"><?php echo $aOld_id; ?>변경 전 추천인</a></th>
                <th scope="col" class="th_bg"><?php echo $aNew_id; ?>변경 후 추천인</a></th>
                <th scope="col">메모</th>
            </tr>
        </thead>
        <?=$leaveTable?>
        </tbody>
	</table>
</div>
<?=$pagination?>
<script>
$(function(){
	// 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>

<?=$headTitleEnd?>