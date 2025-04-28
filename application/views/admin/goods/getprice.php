<form name="fgoodslist" id="fgoodslist" method="post" action="<?=base_url()?>AdminProcess/goods_list_update" onsubmit="return fgoodslist_submit(this);">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="token" value="">

    <div class="local_ov mart30">
        전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 건 조회
        <span class="ov_a">
            <select id="page_rows" onchange="location='<?php echo "{$requestUrl}?{$q1}&page=1"; ?>&page_rows='+this.value;">
                <?php echo $page_rowsOpt['30']; ?>
                <?php echo $page_rowsOpt['50']; ?>
                <?php echo $page_rowsOpt['100']; ?>
                <?php echo $page_rowsOpt['150']; ?>
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
                <col class="w90">
                <col class="w90">
                <col class="w90">
                <col class="w90">
                <col class="w60">
                <col class="w60">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col" rowspan="2"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col" rowspan="2">번호</th>
                    <th scope="col" rowspan="2">이미지</th>
                    <th scope="col"><?php echo $gcodeTag; ?>상품코드</a></th>
                    <th scope="col" colspan="2"><?php echo $gnameTag; ?>상품명</a></th>
                    <th scope="col"><?php echo $reg_timeTag; ?>최초등록일</a></th>
                    <th scope="col"><?php echo $isopenTag; ?>진열</a></th>
                    <th scope="col" colspan="4" class="th_bg">가격정보</th>
                    <th scope="col" rowspan="2"><?php echo $rankTag; ?>순위</a></th>
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
                    <th scope="col" class="th_bg"><?php echo $gpointTag; ?>포인트</a></th>
                </tr>
            </thead>
	        <tbody>
                <?=$getpriceTable?>
            </tbody>
	    </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<h2>판매가 및 시중가 일괄처리</h2>
<form name="frmprice" id="frmprice" method="post" autocomplete="off">
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w100">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th scope="row">적용조건</th>
                <td>
                    <input type="radio" name="gubun" value="1" id="gubun_1" checked="checked">
                    <label for="gubun_1">선택한 상품만 적용</label>
                    <input type="radio" name="gubun" value="2" id="gubun_2">
                    <label for="gubun_2">검색된 상품 전체(<?php echo number_format($total_count); ?>개)를 적용</label>
                </td>
            </tr>
            <tr>
                <th scope="row">가격조건</th>
                <td>
                    <label for="price_field" class="sound_only">적용기준</label>
                    <select name="price_field" id="price_field">
                        <option value="goods_price">판매가에서</option>
                        <option value="normal_price">시중가에서</option>
                        <option value="supply_price">공급가에서</option>
                    </select>
                    <label for="price" class="sound_only">적용금액(율)</label>
                    <input type="text" name="price" id="price" class="frm_input" size="7">
                    <label for="price_type" class="sound_only">적용타입</label>
                    <select name="price_type" id="price_type">
                        <option value="$">(원)을</option>
                        <option value="%">(%)를</option>
                    </select>
                    <label for="price_both" class="sound_only">가격 (할인/할증)</label>
                    <select name="price_both" id="price_both">
                        <option value="down">할인된 가격으로</option>
                        <option value="up">할증된 가격으로</option>
                    </select>
                    <label for="price_target" class="sound_only">적용대상</label>
                    <select name="price_target" id="price_target">
                        <option value="goods_price">판매가를</option>
                        <option value="normal_price">시중가를</option>
                        <option value="supply_price">공급가를</option>
                    </select>
                    <label for="price_unit" class="sound_only">적용단위</label>
                    <select name="price_unit" id="price_unit">
                        <option value="1">1원 단위로</option>
                        <option value="10">10원 단위로</option>
                        <option value="100">100원 단위로</option>
                        <option value="1000">1000원 단위로</option>
                    </select>
                    <label for="price_cut" class="sound_only">가격절사</label>
                    <select name="price_cut" id="price_cut">
                        <option value="floor">내림</option>
                        <option value="round">반올림</option>
                        <option value="ceil">올림</option>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="button" value="일괄적용하기" onclick="js_chk_frm(this.form);" class="btn_large">
    </div>
</form>

<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="desc02">
			<p>ㆍ일괄수정 할 상품을 검색 후 상품 가격을 일괄처리 조건에 맞춰 적용합니다.</p>
			<p>ㆍ일괄수정 이후에는 이전상태로 복원이 안되므로 신중하게 변경하시기 바랍니다.</p>
			<p>ㆍ서버 부하등 안정적인 서비스를 위해서 수정할 상품이 많은 경우에는 "검색된 상품에 적용"은 피하시기 바랍니다.</p>
			<p>ㆍ할인/할증율(%) : 소수점 첫째자리까지 입력하실 수 있습니다.</p>
			<p>ㆍ<em>[예] 5.5% 할인(가능), 5.5% 할증(가능), 5.55% 할인(불가능)</em></p>
		</div>
		<div class="hd">ㆍ가격수정 예제</div>
		<div class="desc01">
			<p>ㆍ판매가의 5.5% 할인된 가격으로 판매가를 일괄적으로 수정하고, 가격 단위는 100원 단위로 내림하여 수정한다면,</p>
			<p>ㆍ판매가 10,000원인 상품의 계산식은 다음과 같습니다.</p>
			<p class="fc_red">ㆍ10,000 × (1 - (5.5 / 100)) = 9,450원이며,</p>
			<p class="fc_red">ㆍ100원 단위 내림하면 9,400원 으로 최종 가격수정이 됩니다.</p>
		</div>
	</div>
</div>

<script>
function fgoodslist_submit(f)
{
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

// 일괄적용하기
function js_chk_frm(f2){
    var base_url=document.getElementById('base_url').value;
	var f = document.fgoodslist;
	if(getRadioVal(f2.gubun) == 1) {
		var chk = document.getElementsByName("chk[]");
		var bchk = false;
		for(i=0; i<chk.length; i++)
		{
			if(chk[i].checked)
				bchk = true;
		}

		if(!bchk)
		{
			alert("적용할 자료를 하나 이상 선택하세요.");
			return;
		}
	}

	if(!f2.price.value) {
		alert("적용할 숫자를 입력하세요.");
		f2.price.focus();
		return;
	}

    if(isNaN(f2.price.value)) {
		alert("숫자만 입력하세요.");
		f2.price.focus();
        return;
	}

	addHidden(f, 'gubun', getRadioVal(f2.gubun));
	addHidden(f, 'price', setFloor(f2.price.value,1));
	addHidden(f, 'price_field', getSelectVal(f2.price_field));
	addHidden(f, 'price_type', getSelectVal(f2.price_type));
	addHidden(f, 'price_both', getSelectVal(f2.price_both));
	addHidden(f, 'price_target', getSelectVal(f2.price_target));
	addHidden(f, 'price_unit', getSelectVal(f2.price_unit));
	addHidden(f, 'price_cut', getSelectVal(f2.price_cut));

	if(confirm("일괄적용 후에는 이전상태로 복원이 되지않습니다.\n\n정말 적용하시겠습니까?")) {
		var token = get_ajax_token();
        if(!token) {
            alert("토큰 정보가 올바르지 않습니다.");
            return false;
        }

		f.token.value = token;
		f.action = base_url+'AdminProcess/goods_getprice_update';
		f.submit();
	}
}

$(function(){
	// 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
	$("#fr_date,#to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>
<?=$headTitleEnd?>