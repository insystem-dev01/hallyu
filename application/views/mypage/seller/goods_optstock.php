<?=$snbTagStart?>
<?=$admin_head_sub?>
<?=$jqueryUiDatePick?>
<h2>기본검색</h2>
<form name="fsearch" id="fsearch" method="get">
    <input type="hidden" name="code" value="<?php echo $code; ?>">
    <div class="tbl_frm01">
        <table class="tablef">
            <colgroup>
                <col class="w100">
                <col>
                <col class="w100">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">검색어</th>
                    <td colspan="3">
                        <select name="sfl">
                            <?=$sflArr[0]?>
                            <?=$sflArr[1]?>
                            <?=$sflArr[2]?>
                            <?=$sflArr[3]?>
                            <?=$sflArr[4]?>
                            <?=$sflArr[5]?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">카테고리</th>
                    <td colspan="3">
                        <?php echo $scaSelect[0]; ?>
                        <?php echo $scaSelect[1]; ?>
                        <?php echo $scaSelect[2]; ?>
                        <?php echo $scaSelect[3]; ?>
                        <?php echo $scaSelect[4]; ?>
                        <script>
                        $(function() {
                            var base_url=document.getElementById('base_url').value;
                            $("#sel_ca1").multi_select_box("#sel_ca",5,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                            $("#sel_ca2").multi_select_box("#sel_ca",5,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                            $("#sel_ca3").multi_select_box("#sel_ca",5,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                            $("#sel_ca4").multi_select_box("#sel_ca",5,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                            $("#sel_ca5").multi_select_box("#sel_ca",5,"","=카테고리선택=");
                        });
                        </script>
                    </td>
                </tr>
                <tr>
                    <th scope="row">기간검색</th>
                    <td colspan="3">
                        <select name1="q_date_field" id="q_date_field">
                            <?php echo $updateTimeTag; ?>
                            <?php echo $regTimeTag; ?>
                        </select>
                        <?php echo $get_search_date; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">브랜드</th>
                    <td>
                        <select name="q_brand">
                            <?=$brandStr?>
                        </select>
                    </td>
                    <th scope="row">배송가능 지역</th>
                    <td>
                        <select name="q_zone">
                            <?php echo $q_zoneStr[0];?>
                            <?php echo $q_zoneStr[1];?>
                            <?php echo $q_zoneStr[2];?>
                            <?php echo $q_zoneStr[3];?>
                            <?php echo $q_zoneStr[4];?>
                            <?php echo $q_zoneStr[5];?>
                            <?php echo $q_zoneStr[6];?>
                            <?php echo $q_zoneStr[7];?>
                            <?php echo $q_zoneStr[8];?>
                            <?php echo $q_zoneStr[9];?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">상품재고</th>
                    <td>
                        <select name="q_stock_field" id="q_stock_field">
                            <?php echo $stock_qtyTag; ?>
                            <?php echo $noti_qtyTag; ?>
                        </select>
                        <label for="fr_stock" class="sound_only">재고수량 시작</label>
                        <input type="text" name="fr_stock" value="<?php echo $fr_stock; ?>" id="fr_stock" class="frm_input" size="6"> 개 이상 ~
                        <label for="to_stock" class="sound_only">재고수량 끝</label>
                        <input type="text" name="to_stock" value="<?php echo $to_stock; ?>" id="to_stock" class="frm_input" size="6"> 개 이하
                    </td>
                    <th scope="row">상품가격</th>
                    <td>
                        <select name="q_price_field" id="q_price_field">
                            <?php echo $price_fieldArr[0]; ?>
                            <?php echo $price_fieldArr[1]; ?>
                            <?php echo $price_fieldArr[2]; ?>
                            <?php echo $price_fieldArr[3]; ?>
                        </select>
                        <label for="fr_price" class="sound_only">상품가격 시작</label>
                        <input type="text" name="fr_price" value="<?php echo $fr_price; ?>" id="fr_price" class="frm_input" size="6"> 원 이상 ~
                        <label for="to_price" class="sound_only">상품가격 끝</label>
                        <input type="text" name="to_price" value="<?php echo $to_price; ?>" id="to_price" class="frm_input" size="6"> 원 이하
                    </td>
                </tr>
                <tr>
                    <th scope="row">판매여부</th>
                    <td>
                        <?php echo $q_isopenArr[0]; ?>
                        <?php echo $q_isopenArr[1]; ?>
                        <?php echo $q_isopenArr[2]; ?>
                        <?php echo $q_isopenArr[3]; ?>
                        <?php echo $q_isopenArr[4]; ?>
                    </td>
                    <th scope="row">필수옵션</th>
                    <td>
                        <?php echo $q_optionArr['all']; ?>
                        <?php echo $q_optionArr['use']; ?>
                        <?php echo $q_optionArr['unuse']; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">과세유형</th>
                    <td>
                        <?php echo $q_notaxArr['all']; ?>
                        <?php echo $q_notaxArr['1']; ?>
                        <?php echo $q_notaxArr['0']; ?>
                    </td>
                    <th scope="row">추가옵션</th>
                    <td>
                        <?php echo $q_supplyArr['all']; ?>
                        <?php echo $q_supplyArr['1']; ?>
                        <?php echo $q_supplyArr['0']; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">승인상태</th>
                    <td colspan="3">
                        <?php echo $q_stateArr['all']; ?>
                        <?php echo $q_stateArr['0']; ?>
                        <?php echo $q_stateArr['1']; ?>
                        <?php echo $q_stateArr['2']; ?>
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
<h2>재고설정</h2>
<form name="fregform" id="fregform" method="post" autocomplete="off">
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w100">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">일괄</th>
                    <td>
                        재고수량 : <input type="text" name="stock_qty" class="frm_input w80"> 개
                        <button type="button" onclick="js_chk_frm(this.form);" class="btn_small blue marl5">적용하기</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form>

<form name="fgoodslist" id="fgoodslist" method="post" action="<?=base_url()?>MypageProcess/seller_goods_list_update" onsubmit="return fgoodslist_submit(this);">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">

    <div class="local_ov mart30">
        전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 건 조회
        <span class="ov_a">
            <select id="page_rows" onchange="location='<?php echo "{$requertUrl}?{$q1}&page=1"; ?>&page_rows='+this.value;">
                <?php echo $page_rowsOpt['30']; ?>
                <?php echo $page_rowsOpt['50']; ?>
                <?php echo $page_rowsOpt['100']; ?>
                <?php echo $page_rowsOpt['150']; ?>
            </select>
        </span>
    </div>
    <div class="tbl_head02">
        <table id="sodr_list" class="tablef">
            <colgroup>
                <col class="w50">
                <col class="w50">
                <col class="w60">
                <col class="w120">
                <col>
                <col>
                <col class="w80">
                <col class="w80">
                <col class="w90">
                <col class="w90">
                <col class="w90">
                <col class="w90">
                <col class="w60">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col" rowspan="2"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col" rowspan="2">번호</th>
                    <th scope="col" rowspan="2">이미지</th>
                    <th scope="col"><?php echo $gcodeStr; ?>상품코드</a></th>
                    <th scope="col" colspan="2"><?php echo $gnameStr; ?>상품명</a></th>
                    <th scope="col"><?php echo $regTimeStr; ?>최초등록일</a></th>
                    <th scope="col"><?php echo $isopenStr; ?>진열</a></th>
                    <th scope="col" colspan="4" class="th_bg">가격정보</th>
                    <th scope="col" rowspan="2">관리</th>
                </tr>
                <tr class="rows">
                    <th scope="col"><?php echo $shop_stateStr; ?>승인상태</a></th>
                    <th scope="col">업체명</th>
                    <th scope="col">카테고리</th>
                    <th scope="col"><?php echo $updateTimeStr; ?>최근수정일</a></th>
                    <th scope="col"><?php echo $stockQtyStr; ?>재고</a></th>
                    <th scope="col" class="th_bg"><?php echo $normalPriceStr; ?>시중가</a></th>
                    <th scope="col" class="th_bg"><?php echo $supplyPriceStr; ?>공급가</a></th>
                    <th scope="col" class="th_bg"><?php echo $goodPriceStr; ?>판매가</a></th>
                    <th scope="col" class="th_bg"><?php echo $gpointStr; ?>포인트</a></th>
                </tr>
            </thead>
	        <tbody>
                <?=$goods_optStockTable?>
            </tbody>
	    </table>
    </div>
</form>
<?=$pagination?>

<script>
function js_chk_frm(f2){
	var f = document.foptionlist;

    if(!is_checked("chk[]")) {
        alert("적용하실 항목을 하나 이상 선택하세요.");
        return;
    }

	if(!f2.stock_qty.value) {
		alert('적용할 재고수량을 입력하세요.');
		f2.stock_qty.focus();
		return;
	}

	var token = get_ajax_token();
	if(!token) {
		alert("토큰 정보가 올바르지 않습니다.");
		return;
	}

	addHidden(f, 'act_button', '선택옵션재고수정');
	addHidden(f, 'stock_qty', f2.stock_qty.value);
	addHidden(f, 'token', token);
    f.submit();
}

$(function(){
	// 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
	$("#fr_date,#to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>
<?=$admin_footer_sub?>
<?=$snbTagEnd?>