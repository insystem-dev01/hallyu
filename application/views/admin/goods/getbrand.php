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
                    <th scope="col">브랜드명</th>
                    <th scope="col"><?php echo $update_timeTag; ?>최근수정일</a></th>
                    <th scope="col"><?php echo $stock_qtyTag; ?>재고</a></th>
                    <th scope="col" class="th_bg"><?php echo $normal_priceTag; ?>시중가</a></th>
                    <th scope="col" class="th_bg"><?php echo $supply_priceTag; ?>공급가</a></th>
                    <th scope="col" class="th_bg"><?php echo $goods_priceTag; ?>판매가</a></th>
                    <th scope="col" class="th_bg"><?php echo $gpointTag; ?>포인트</a></th>
                </tr>
            </thead>
            <tbody>
                <?=$getBrandTable?>
            </tbody>
        </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>

<h2>브랜드연결 및 해제 일괄처리</h2>
<form name="frmbrand" id="frmbrand" method="post" autocomplete="off">
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
                    <th scope="row">연결하기</th>
                    <td>
                        <label for="new_brand" class="sound_only">브랜드</label>
                        <select name="new_brand" id="new_brand">
                            <?=$brandStr?>
                        </select>
                        <input type="button" value="일괄적용하기" onclick="js_chk_frm(this.form, 1);" class="btn_small">
                    </td>
                </tr>
                <tr>
                    <th scope="row">연결해제</th>
                    <td><input type="button" value="선택(검색)된 상품의 모든 브랜드를 연결해제" onclick="js_chk_frm(this.form, 2);" class="btn_small"></td>
                </tr>
            </tbody>
        </table>
    </div>
</form>
<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="desc02">
			<p>ㆍ일괄수정 할 상품을 검색 후 상품 브랜드를 일괄처리 하시기 바랍니다.</p>
			<p>ㆍ서버 부하등 안정적인 서비스를 위해서 수정할 상품이 많은 경우에는 "검색된 상품에 적용"은 피하시기 바랍니다.</p>
		</div>
		<div class="hd">ㆍ브랜드설정 안내</div>
		<div class="desc01">
			<p>ㆍ<em>[연결하기]</em> 기존에 연결되어있는 모든 브랜드는 삭제되며 새롭운 브랜드만 연결됩니다.</p>
			<p>ㆍ<em>[연결해제]</em> 기존에 연결되어있는 모든 브랜드가 삭제됩니다.</p>
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
function js_chk_frm(f2, act)
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

	if(act == 1) {
		if(getSelectVal(f2.new_brand) == "") {
			alert("적용할 브랜드를 선택하세요.");
			return;
		}
	}

	addHidden(f, 'gubun', getRadioVal(f2.gubun));
	addHidden(f, 'new_brand', getSelectVal(f2.new_brand));
	addHidden(f, 'act', act);

	if(confirm("일괄적용 후에는 이전상태로 복원이 되지않습니다.\n\n정말 적용하시겠습니까?")) {
		var token = get_ajax_token();
        if(!token) {
            alert("토큰 정보가 올바르지 않습니다.");
            return false;
        }
        var base_url=document.getElementById('base_url').value;
		f.token.value = token;
		f.action = base_url+"AdminProcess/goods_getbrandupdate";
		f.submit();
	}
}

$(function(){
	// 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
	$("#fr_date,#to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>
<?=$headTitleEnd?>
