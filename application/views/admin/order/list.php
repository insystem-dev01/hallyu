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
                            <?php echo $sel_fieldOpt['receipt_time']; ?>
                            <?php echo $sel_fieldOpt['delivery_date']; ?>
                            <?php echo $sel_fieldOpt['invoice_date']; ?>
                            <?php echo $sel_fieldOpt['user_date']; ?>
                            <?php echo $sel_fieldOpt['cancel_date']; ?>
                            <?php echo $sel_fieldOpt['refund_date']; ?>
                            <?php echo $sel_fieldOpt['return_date']; ?>
                            <?php echo $sel_fieldOpt['change_date']; ?>
                        </select>
                        <?php echo $get_search_date; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">결제방법</th>
                    <td>
                        <?php echo $payMethod['all']; ?>
                        <?php echo $payMethod['noBank']; ?>
                        <?php echo $payMethod['at']; ?>
                        <?php echo $payMethod['credit']; ?>
                        <?php echo $payMethod['point']; ?>
                        <?php echo $payMethod['mileage']; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">주문상태</th>
                    <td>
                        <?php echo $od_statusArr[0]; ?>
                        <?php echo $od_statusArr[1]; ?>
                        <?php echo $od_statusArr[2]; ?>
                        <?php echo $od_statusArr[3]; ?>
                        <?php echo $od_statusArr[4]; ?>
                        <?php echo $od_statusArr[5]; ?>
                        <?php echo $od_statusArr[6]; ?>
                        <?php echo $od_statusArr[9]; ?>
                        <?php echo $od_statusArr[7]; ?>
                        <?php echo $od_statusArr[8]; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">구매확정</th>
                    <td>
                        <?php echo $od_finalArr['all']; ?>
                        <?php echo $od_finalArr['pc']; ?>
                        <?php echo $od_finalArr['pcn']; ?>
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
                <col class="w150">
                <col class="w40">
				<?php if($member['grade'] == 99) { ?>
				<col class="w40">
				<?php } ?>
                <col>
                <col class="w60">
                <col class="w80">
                <col class="w80">
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
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col" colspan="<?=$member['grade'] == 99 ? "2" : ""?>">주문상품</th>
                    <th scope="col">수량</th>
                    <th scope="col">상품금액</th>
                    <th scope="col">배송비</th>
                    <th scope="col">주문상태</th>
                    <th scope="col">판매자</th>
                    <th scope="col">주문자</th>
                    <th scope="col">수령자</th>
                    <th scope="col">총주문액</th>
                    <th scope="col">결제방법</th>
                </tr>
            </thead>
            <tbody>
                <?=$orderlistTable?>
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