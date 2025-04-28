<?=$snbTagStart?>
<?=$admin_head_sub?>
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
                    <th scope="row">주문일</th>
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
	<table id="sodr_list">
        <colgroup>
            <col class="w50">
            <col class="w100">
            <col class="w150">
            <col class="w90">
            <col class="w40">
            <col>
            <col class="w60">
            <col class="w90">
            <col class="w90">
            <col class="w90">
            <col class="w90">
            <col class="w90">
            <col class="w90">
        </colgroup>
        <thead>
            <tr>
                <th scope="col">번호</th>
                <th scope="col">주문일시</th>
                <th scope="col">주문번호</th>
                <th scope="col">주문자</th>
                <th scope="col" colspan="2">주문상품</th>
                <th scope="col">수량</th>
                <th scope="col">판매가</th>
                <th scope="col" class="th_bg">공급가</th>
                <th scope="col" class="th_bg">배송비</th>
                <th scope="col" class="th_bg">정산액</th>
                <th scope="col">주문총액</th>
                <th scope="col">결제방법</th>
            </tr>
        </thead>
        <?=$balanceTable?>
        </tbody>
	</table>
</div>
<?=$pagination?>
<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="hd">ㆍ정산대기목록에서 주문건은 언제 노출되나요?</div>
		<div class="desc01 accent">
			<p>ㆍ배송완료 후 "구매확정"이 되어야만 정산대기목록에 노출됩니다. 정산처리가 완료되면 "정산완료목록"에서 확인하실 수 있습니다.</p>
		</div>
	 </div>
</div>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>
<?=$admin_footer_sub?>
<?=$snbTagEnd?>