<?=$headTitle?>
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
                            <?php echo $sflArr[0]; ?>
                            <?php echo $sflArr[1]; ?>
                            <?php echo $sflArr[2]; ?>
                            <?php echo $sflArr[3]; ?>
                            <?php echo $sflArr[4]; ?>
                            <?php echo $sflArr[5]; ?>
                            <?php echo $sflArr[6]; ?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">카테고리</th>
                    <td colspan="3">
                        <?php echo $sel_ca[1]; ?>
                        <?php echo $sel_ca[2]; ?>
                        <?php echo $sel_ca[3]; ?>
                        <?php echo $sel_ca[4]; ?>
                        <?php echo $sel_ca[5]; ?>
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
                        <select name="q_date_field" id="q_date_field">
                            <?php echo $updateTime; ?>
                            <?php echo $regTime; ?>
                        </select>
                        <?php echo $get_search_date; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">브랜드</th>
                    <td>
                        <select name="q_brand">
                            <?=$q_zoneStr?>
                        </select>
                    </td>
                    <th scope="row">배송가능 지역</th>
                    <td>
                        <select name="q_zone">
                            <?php echo $q_zoneOpt[0]; ?>
                            <?php echo $q_zoneOpt[1]; ?>
                            <?php echo $q_zoneOpt[2]; ?>
                            <?php echo $q_zoneOpt[3]; ?>
                            <?php echo $q_zoneOpt[4]; ?>
                            <?php echo $q_zoneOpt[5]; ?>
                            <?php echo $q_zoneOpt[6]; ?>
                            <?php echo $q_zoneOpt[7]; ?>
                            <?php echo $q_zoneOpt[8]; ?>
                            <?php echo $q_zoneOpt[9]; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">상품재고</th>
                    <td>
                        <select name="q_stock_field" id="q_stock_field">
                            <?php echo $stock['stock'] ?>
                            <?php echo $stock['noti'] ?>
                        </select>
                        <label for="fr_stock" class="sound_only">재고수량 시작</label>
                        <input type="text" name="fr_stock" value="<?php echo $fr_stock; ?>" id="fr_stock" class="frm_input" size="6"> 개 이상 ~
                        <label for="to_stock" class="sound_only">재고수량 끝</label>
                        <input type="text" name="to_stock" value="<?php echo $to_stock; ?>" id="to_stock" class="frm_input" size="6"> 개 이하
                    </td>
                    <th scope="row">상품가격</th>
                    <td>
                        <select name="q_price_field" id="q_price_field">
                            <?php echo $priceField[0]; ?>
                            <?php echo $priceField[1]; ?>
                            <?php echo $priceField[2]; ?>
                            <?php echo $priceField[3]; ?>
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
                        <?php echo $q_optionArr[0]; ?>
                        <?php echo $q_optionArr[1]; ?>
                        <?php echo $q_optionArr[2]; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">과세유형</th>
                    <td>
                        <?php echo $q_notaxArr[0]; ?>
                        <?php echo $q_notaxArr[1]; ?>
                        <?php echo $q_notaxArr[2]; ?>
                    </td>
                    <th scope="row">추가옵션</th>
                    <td>
                        <?php echo $q_supplyArr[0]; ?>
                        <?php echo $q_supplyArr[1]; ?>
                        <?php echo $q_supplyArr[2]; ?>
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
<form name="fgoodslist" id="fgoodslist" method="post" action="<?=base_url()?>AdminProcess/goods_list_update" onsubmit="return fgoodslist_submit(this);">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <div class="local_ov mart30">
        전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 건 조회
        <span class="ov_a">
            <select id="page_rows" onchange="location='<?php echo "{$requestUrl}?{$q1}&page=1"; ?>&page_rows='+this.value;">
                <?php echo $optSel['30']; ?>
                <?php echo $optSel['50']; ?>
                <?php echo $optSel['100']; ?>
                <?php echo $optSel['150']; ?>
            </select>
        </span>
    </div>
    <div class="local_frm01">
        <?php echo $btn_frmline; ?>
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
                <col class="w80">
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
                    <th scope="col"><?php echo $gcodeTag; ?>상품코드</a></th>
                    <th scope="col" colspan="2"><?php echo $gnameTag; ?>상품명</a></th>
                    <th scope="col" rowspan="2">상품구분</th>
                    <th scope="col"><?php echo $reg_timeTag; ?>최초등록일</a></th>
                    <th scope="col"><?php echo $isopenTag; ?>진열</a></th>
                    <th scope="col" colspan="3" class="th_bg">가격정보</th>
                    <th scope="col" rowspan="2">관리</th>
                </tr>
                <tr class="rows">
                    <th scope="col"><?php echo $mb_idTag; ?>업체코드</a></th>
                    <th scope="col">입점사명</th>
                    <th scope="col">카테고리</th>
                    <th scope="col"><?php echo $update_timeTag; ?>최근수정일</a></th>
                    <th scope="col"><?php echo $stock_qtyTag; ?>재고</a></th>
                    <th scope="col" class="th_bg"><?php echo $normal_priceTag; ?>시중가</a></th>
                    <th scope="col" class="th_bg"><?php echo $supply_priceTag; ?>공급가</a></th>
                    <th scope="col" class="th_bg"><?php echo $goods_priceTag; ?>판매가</a></th>
                </tr>
            </thead>
	        <tbody>
                <?=$supplyTable?>
            </tbody>
	    </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<script>
function fgoodslist_submit(f){
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
	$("#fr_date,#to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>
<?=$headTitleEnd?>