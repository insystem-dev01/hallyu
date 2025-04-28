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
                            <?php echo $sflOpt[0]; ?>
                            <?php echo $sflOpt[1]; ?>
                            <?php echo $sflOpt[2]; ?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
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
<form name="forderlist" id="forderlist" method="post" action="<?=base_url()?>AdminProcess/seller_pay_delete" onsubmit="return forderlist_submit(this);">
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
                <col class="w80">
                <col>
                <col class="w130">
                <col class="w60">
                <col class="w90">
                <col class="w90">
                <col class="w90">
                <col class="w90">
                <col class="w90">
                <col class="w90">
                <col class="w90">
                <col class="w90">
                <col class="w90">
                <col class="w60">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col">번호</th>
                    <th scope="col">업체코드</th>
                    <th scope="col">입점사명</th>
                    <th scope="col">일시</th>
                    <th scope="col" class="th_bg">총건수</th>
                    <th scope="col" class="th_bg">주문금액</th>
                    <th scope="col" class="th_bg">포인트결제</th>
                    <th scope="col" class="th_bg">예치금결제</th>
                    <th scope="col" class="th_bg">쿠폰할인</th>
                    <th scope="col" class="th_bg">배송비</th>
                    <th scope="col" class="th_bg">공급가총액</th>
                    <th scope="col" class="th_bg">실정산액</th>
                    <th scope="col">가맹점수수료</th>
                    <th scope="col">본사마진</th>
                    <th scope="col">내역</th>
                </tr>
            </thead>
            <tbody>
                <?=$pay_historyTag?>
            </tbody>
        </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<script>
function forderlist_submit(f){
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