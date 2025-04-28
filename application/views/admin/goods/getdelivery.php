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
                <?=$getDeliverTable?>
            </tbody>
	    </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<h2>배송비 일괄처리</h2>
<form name="frmdelivery" id="frmdelivery" method="post" autocomplete="off">
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
            <th scope="row">배송정보</th>
            <td>
                <select name="sc_type" onChange="chk_sc_type(this.value);">
                    <?php echo $sc_type_arr[0]; ?>
                    <?php echo $sc_type_arr[1]; ?>
                    <?php echo $sc_type_arr[2]; ?>
                    <?php echo $sc_type_arr[3]; ?>
                </select>
                <div id="sc_method" class="mart7">
                    배송비결제
                    <select name="sc_method" class="marl10">
                        <?php echo $$sc_method_arr[0]; ?>
                        <?php echo $$sc_method_arr[1]; ?>
                        <?php echo $$sc_method_arr[2]; ?>
                    </select>
                </div>
                <div id="sc_amt" class="padt5">
                    기본배송비 <input type="text" name="sc_amt" value="0" class="frm_input w80 marl10"> 원
                    <label class="marl10"><input type="checkbox" name="sc_each_use" value="1"> 묶음배송불가</label>
                </div>
                <div id="sc_minimum" class="padt5">
                    조건배송비 <input type="text" name="sc_minimum" value="0" class="frm_input w80 marl10"> 원 이상이면 무료배송
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="zone">배송가능 지역</label></th>
            <td>
                <select name="zone" id="zone">
                <?php echo $qZoneArr[0]; ?>
                <?php echo $qZoneArr[1]; ?>
                <?php echo $qZoneArr[2]; ?>
                <?php echo $qZoneArr[3]; ?>
                <?php echo $qZoneArr[4]; ?>
                <?php echo $qZoneArr[5]; ?>
                <?php echo $qZoneArr[6]; ?>
                <?php echo $qZoneArr[7]; ?>
                <?php echo $qZoneArr[8]; ?>
                <?php echo $qZoneArr[9]; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="zone_msg">추가설명</label></th>
            <td><input type="text" name="zone_msg" id="zone_msg" class="frm_input" size="60" placeholder="예 : 제주 (도서지역 제외)"></td>
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
			<p>ㆍ<em>[기본설정]</em>  <a href="<?php echo base_url(); ?>admin/config?code=baesong">환경설정 > 배송/교환/반품 설정</a>에서 적용된값으로 부과됩니다.</p>
			<p>ㆍ서버 부하등 안정적인 서비스를 위해서 수정할 상품이 많은 경우에는 "검색된 상품에 적용"은 피하시기 바랍니다.</p>
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
function js_chk_frm(f2)
{
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

	addHidden(f, 'gubun', getRadioVal(f2.gubun));
	addHidden(f, 'sc_type', getSelectVal(f2.sc_type));
	addHidden(f, 'sc_method', getSelectVal(f2.sc_method));
	addHidden(f, 'sc_amt', f2.sc_amt.value);
	addHidden(f, 'sc_each_use', f2.sc_each_use.value);
	addHidden(f, 'sc_minimum', f2.sc_minimum.value);
	addHidden(f, 'zone', getSelectVal(f2.zone));
	addHidden(f, 'zone_msg', f2.zone_msg.value);

	if(confirm("일괄적용 후에는 이전상태로 복원이 되지않습니다.\n\n정말 적용하시겠습니까?")) {
		var token = get_ajax_token();
        if(!token) {
            alert("토큰 정보가 올바르지 않습니다.");
            return false;
        }
        var base_url=document.getElementById('base_url').value;
		f.token.value = token;
		f.action = base_url+"AdminProcess/goods_getdeliveryupdate";
		f.submit();
	}
}

// 배송비 설정
function chk_sc_type(type) {
	switch(type) {
		case "0" : // 공통설정
			$("#sc_amt").hide();
			$("#sc_minimum").hide();
			$("#sc_method").show();
			$("input[name=sc_amt]").attr('disabled', true);
			$("input[name=sc_minimum]").attr('disabled', true);
			$("select[name=sc_method]").attr('disabled', false);
			break;
		case "1" : // 무료배송
			$("#sc_amt").hide();
			$("#sc_minimum").hide();
			$("#sc_method").hide();
			$("input[name=sc_amt]").attr('disabled', true);
			$("input[name=sc_minimum]").attr('disabled', true);
			$("select[name=sc_method]").attr('disabled', true);
			break;
		case "2" : // 조건부무료배송
			$("#sc_amt").show();
			$("#sc_minimum").show();
			$("#sc_method").show();
			$("input[name=sc_amt]").attr('disabled', false);
			$("input[name=sc_minimum]").attr('disabled', false);
			$("select[name=sc_method]").attr('disabled', false);
			break;
		case "3" : // 유료배송
			$("#sc_amt").show();
			$("#sc_minimum").hide();
			$("#sc_method").show();
			$("input[name=sc_amt]").attr('disabled', false);
			$("input[name=sc_minimum]").attr('disabled', true);
			$("select[name=sc_method]").attr('disabled', false);
			break;
	}
}

chk_sc_type('0');

$(function(){
	// 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
	$("#fr_date,#to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>
<?=$headTitleEnd?>