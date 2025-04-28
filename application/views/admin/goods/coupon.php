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
                        <?php echo $cp_subjectTag; ?>
                        <?php echo $cp_explanTag; ?>
                    </select>
                    <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                </td>
            </tr>
            <tr>
                <th scope="row">사용기간</th>
                <td>
                    <?php echo $get_search_date;?>
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
<form name="fcouponlist" id="fcouponlist" method="post" action="<?=base_url()?>AdminProcess/goods_coupon_delete" onsubmit="return fcouponlist_submit(this);">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <div class="local_ov mart30">
        전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 건 조회
    </div>
    <div class="local_frm01">
        <?php echo $btn_frmline; ?>
    </div>
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col class="w50">
                <col class="w50">
                <col>
                <col class="w80">
                <col class="w80">
                <col class="w80">
                <col class="w80">
                <col class="w50">
                <col class="w60">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col">번호</th>
                    <th scope="col"><?php echo $cp_typeTag; ?>쿠폰유형</a> (쿠폰명)</th>
                    <th scope="col">사용시작</th>
                    <th scope="col">사용종료</th>
                    <th scope="col">다운로드</th>
                    <th scope="col">주문건수</th>
                    <th scope="col"><?php echo $cp_useTag; ?>사용</a></th>
                    <th scope="col">관리</th>
                </tr>
            </thead>
            <?=$couponTable?>
            </tbody>
	    </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>

<script>
function fcouponlist_submit(f){
    if(!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?  \n삭제한 자료는 복구가 불가능합니다.")) {
            return false;
        }
    }

    return true;
}

$(function(){
	// 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>


<?=$headTitleEnd?>