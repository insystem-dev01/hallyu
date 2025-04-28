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
                            <?php echo $sflOpt['od_id']; ?>
                            <?php echo $sflOpt['od_no']; ?>
                            <?php echo $sflOpt['mb_id']; ?>
                            <?php echo $sflOpt['name']; ?>
                            <?php echo $sflOpt['deposit_name']; ?>
                            <?php echo $sflOpt['bank']; ?>
                            <?php echo $sflOpt['b_name']; ?>
                            <?php echo $sflOpt['b_telephone']; ?>
                            <?php echo $sflOpt['b_cellphone']; ?>
                            <?php echo $sflOpt['delivery_no']; ?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">기간검색</th>
                    <td>
                        <select name="sel_field">
                            <?php echo $sel_fieldOpt['od_time']; ?>
                            <?php echo $sel_fieldOpt['refund_date']; ?>
                        </select>
                        <?php echo $get_search_date; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">결제방법</th>
                    <td>
                        <?php echo $od_settleArr['all']; ?>
                        <?php echo $od_settleArr['noBank']; ?>
                        <?php echo $od_settleArr['va']; ?>
                        <?php echo $od_settleArr['credit']; ?>
                        <?php echo $od_settleArr['point']; ?>
                        <?php echo $od_settleArr['mileage']; ?>
                    </td>
                </tr>
                <tr>
                <th scope="row">기타선택</th>
                    <td>
                        <?php echo $ectSelectChk['od_taxbill']; ?>
                        <?php echo $ectSelectChk['od_taxsave']; ?>
                        <?php echo $ectSelectChk['od_memo']; ?>
                        <?php echo $ectSelectChk['od_shop_memo']; ?>
                        <?php echo $ectSelectChk['od_receipt_point']; ?>
                        <?php echo $ectSelectChk['od_coupon']; ?>
                        <?php echo $ectSelectChk['od_escrow']; ?>
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
	<select id="page_rows" onchange="location='<?php echo "{$requestUrl}?{$q1}&page=1"; ?>&page_rows='+this.value;" class="marl5">
		<?php echo $page_rowsOpt['30']; ?>
		<?php echo $page_rowsOpt['50']; ?>
		<?php echo $page_rowsOpt['100']; ?>
		<?php echo $page_rowsOpt['150']; ?>
	</select>
	<strong class="ov_a">총주문액 : <?php echo number_format($tot_orderprice); ?>원</strong>
</div>
<form name="forderlist" id="forderlist" method="post">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    
    <div class="local_frm01">
        <?php echo $btn_frmline; ?>
    </div>
    <div class="tbl_head01">
        <table id="sodr_list">
            <colgroup>
                <col class="w50">
                <col class="w100">
                <col class="w100">
                <col class="w150">
                <col class="w40">
				<?php if($member['grade'] == 99) { ?>
				<col class="w40">
				<?php } ?>
                <col>
                <col class="w90">
                <col class="w90">
                <col class="w90">
                <col class="w90">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col">주문일시</th>
                    <th scope="col">환불일시</th>
                    <th scope="col">주문번호</th>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
					<th scope="col" colspan="<?=$member['grade'] == 99 ? "2" : ""?>">주문상품</th>
                    <th scope="col">판매자</th>
                    <th scope="col">주문자</th>
                    <th scope="col">총주문액</th>
                    <th scope="col">결제방법</th>
                </tr>
            </thead>
            <tbody>
                <?=$order9Table?>
            </tbody>
	    </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="hd">ㆍ주문상태를 변경할 수 있나요?</div>
		<div class="desc01 accent">
			<p>ㆍ환불 리스트 내 주문은 상태변경 및 원복이 불가능하며, 삭제도 하실 수 없습니다.</p>
		</div>
	 </div>
</div>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

	// 주문서출력
	$("#frmOrderPrint, #frmOrderExcel").on("click", function() {
        var base_url=document.getElementById('base_url').value;
		var type = $(this).attr("id");
		var od_chk = new Array();
		var od_id = "";
		var $el_chk = $("input[name='chk[]']");

		$el_chk.each(function(index) {
			if($(this).is(":checked")) {
				od_chk.push($("input[name='od_id["+index+"]']").val());
			}
		});

		if(od_chk.length > 0) {
			od_id = od_chk.join();
		}

		if(od_id == "") {
			alert("처리할 자료를 하나 이상 선택해 주십시오.");
			return false;
		} else {
			if(type == 'frmOrderPrint') {
				var url = base_url+"admin/order_print?od_id="+od_id;
				window.open(url, "frmOrderPrint", "left=100, top=100, width=670, height=600, scrollbars=yes");
				return false;
			} else {
				this.href = base_url+"AdminProcess/order_excel2?od_id="+od_id;
				return true;
			}
		}
	});
});
</script>

<?=$headTitleEnd?>