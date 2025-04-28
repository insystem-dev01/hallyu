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
                        <?php echo $sflOpt[3]; ?>
                        <?php echo $sflOpt[4]; ?>
                        <?php echo $sflOpt[5]; ?>
                        <?php echo $sflOpt[6]; ?>
                        <?php echo $sflOpt[7]; ?>
                        <?php echo $sflOpt[8]; ?>
                        <?php echo $sflOpt[9]; ?>
                    </select>
                    <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                </td>
            </tr>
            <tr>
                <th scope="row">주문일</th>
                <td>
                    <?php echo $get_search_date; ?>
                </td>
            </tr>
            <tr>
            <th scope="row">주문상태</th>
                <td>
                    <?php echo $scaArr[0]; ?>
                    <?php echo $scaArr[1]; ?>
                    <?php echo $scaArr[2]; ?>
                    <?php echo $scaArr[3]; ?>
                    <?php echo $scaArr[4]; ?>
                    <?php echo $scaArr[5]; ?>
                    <?php echo $scaArr[6]; ?>
                    <?php echo $scaArr[7]; ?>
                    <?php echo $scaArr[8]; ?>
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
<form name="fmemolist" id="fmemolist" action="<?=base_url()?>AdminProcess/order_memo_delete" onsubmit="return fmemolist_submit(this);" method="post">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    
    <div class="local_frm01">
        <?php echo $btn_frmline; ?>
    </div>
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col class="w50">
                <col class="w50">
                <col class="w100">
                <col class="w150">
                <col class="w90">
                <col class="w90">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col">번호</th>
                    <th scope="col">주문일시</th>
                    <th scope="col">주문번호</th>
                    <th scope="col">주문자</th>
                    <th scope="col">주문상태</th>
                    <th scope="col">메모</th>
                </tr>
            </thead>
            <?=$orderMemoTable?>
            </tbody>
	    </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});

function fmemolist_submit(f){
    if(!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까? \n삭제한 자료는 복구가 불가능합니다.")) {
            return false;
        }
    }

    return true;
}
</script>

<?=$headTitleEnd?>