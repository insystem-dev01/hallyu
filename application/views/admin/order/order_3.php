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
                    <th scope="row">기간검색</th>
                    <td>
                        <select name="sel_field">
                            <?php echo $sel_fieldOpt[0]; ?>
                            <?php echo $sel_fieldOpt[1]; ?>
                        </select>
                        <?php echo $get_search_date; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">결제방법</th>
                    <td>
                        <?php echo $payMethod[0]; ?>
                        <?php echo $payMethod[1]; ?>
                        <?php echo $payMethod[2]; ?>
                        <?php echo $payMethod[3]; ?>
                        <?php echo $payMethod[4]; ?>
                        <?php echo $payMethod[5]; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">기타선택</th>
                    <td>
                        <?php echo $ectSelectChk[0]; ?>
                        <?php echo $ectSelectChk[1]; ?>
                        <?php echo $ectSelectChk[2]; ?>
                        <?php echo $ectSelectChk[3]; ?>
                        <?php echo $ectSelectChk[4]; ?>
                        <?php echo $ectSelectChk[5]; ?>
                        <?php echo $ectSelectChk[6]; ?>
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
<form name="forderlist" id="forderlist" action="<?=base_url()?>AdminProcess/order_update" onsubmit="return forderlist_submit(this);" method="post">
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
                <col class="w30">
                <col class="w30">
                <?php if($member['grade'] == 99) { ?>
                <col class="w40">
                <?php } ?>
                <col class="w400">
                <col class="w90">
                <col>
                <col>
                <col class="w90">
                <col class="w90">
                <col class="w90">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col">주문일시</th>
                    <th scope="col">주문번호</th>
                    <th scope="col"><input type="checkbox" id="sit_select_all"></th>
					<th scope="col" colspan="<?=$member['grade'] == 99 ? "3" : "2"?>">주문상품</th>
                    <th scope="col">판매자</th>
                    <th scope="col">배송회사</th>
                    <th scope="col">운송장번호</th>
                    <th scope="col">주문자</th>
                    <th scope="col">수령자</th>
                    <th scope="col">총주문액</th>
                </tr>
            </thead>
            <tbody>
                <?=$order3Table?>
            </tbody>
	    </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
    
    <h2>주문 일괄처리</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w100">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row" rowspan="2">선택한 주문을</th>
                    <td>
                        <?php echo $deliver2Tag; ?>
                        <input type="text" name="delivery_no2" class="frm_input" placeholder="일괄 운송장번호">
                        <?php echo $helpTag; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="act_button" value="배송중" class="btn_medium red" onclick="document.pressed=this.value">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form>
<?=$pagination?>
<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="hd">ㆍ주문상태 변경에 제한이 있나요?</div>
		<div class="desc01 accent">
			<p>ㆍ주문리스트 내 선택된 주문의 상태를 <em>"입금완료 &gt; 배송준비 &gt; 배송중 &gt; 배송완료 &gt; 구매확정"</em> 순으로 변경됩니다.</p>
			<p>ㆍ배송준비 상태의 주문은 "배송중" 상태로만 변경할 수 있으며, 주문의 일부 상품만 부분적으로 "배송중" 상태로 변경 가능합니다.</p>
		</div>
		<div class="hd">ㆍ출고전 취소할 수 있나요?</div>
		<div class="desc01 accent">
			<p>ㆍ주문리스트에서 배송준비 상태의 주문을 취소/환불 처리가 불가능합니다.</p>
			<p>ㆍ<em>취소/환불/반품/교환</em> 등의 주문상태로 변경은 해당 주문의 <strong>"주문상세정보"</strong> 페이지에서 처리 가능합니다.</p>
		</div>
	 </div>
</div>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

	// 전체 상품선택
    $("#sit_select_all").click(function() {
        if($(this).is(":checked")) {
            $("input[name='it_sel[]']").attr("checked", true);
            $("input[name^=chk]").attr("checked", true);
        } else {
            $("input[name='it_sel[]']").attr("checked", false);
            $("input[name^=chk]").attr("checked", false);
        }
    });

    // 주문의 상품선택
    $("input[name='it_sel[]']").click(function() {
        var cls = $(this).attr("id").replace("sit_", "sct_");
        var $chk = $("input[name^=chk]."+cls);
        if($(this).is(":checked"))
            $chk.attr("checked", true);
        else
            $chk.attr("checked", false);
    });

	// 주문서출력
	$("#frmOrderPrint, #frmOrderExcel").on("click", function() {
        var base_url=document.getElementById('base_url').value;
		var type = $(this).attr("id");
		var od_chk = new Array();
		var od_id = "";
		var $el_chk = $("input[name='it_sel[]']");

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
				this.href = base_url+"AdminProccess/order_excel2?od_id="+od_id;
				return true;
			}
		}
	});
});
</script>

<script>
function forderlist_submit(f)
{
    if(!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

	if(document.pressed == "배송중") {
		if(f.delivery2.value && f.delivery_no2.value) {
			var $el_chk = $("input[name='chk[]']");
			$el_chk.each(function(index) {
				if($(this).is(":checked")) {
					$("select[name='delivery["+index+"]']").val(f.delivery2.value);
					$("input[name='delivery_no["+index+"]']").val(f.delivery_no2.value);
				}
			});
		}

		if(!confirm("선택하신 주문서의 주문상태를 '"+document.pressed+"'상태로 변경하시겠습니까?"))
			return false;
	}

	return true;
}
</script>

<?=$headTitleEnd?>