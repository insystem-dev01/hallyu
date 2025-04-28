<table class="mart20">
    <colgroup>
        <col class="w40">
        <col>
        <col>
        <col class="w80">
        <col class="w70">
        <col class="w70">
        <col class="w90">
    </colgroup>
    <thead>
        <tr>
            <th scope="col" class="tac"><input type="checkbox" name="spl_chk_all" value="1" onclick="spl_chk_allFun(this)"></th>
            <th scope="col" class="tac">옵션명</th>
            <th scope="col" class="tac">옵션항목</th>
            <th scope="col" class="tac">상품금액</th>
            <th scope="col" class="tac">재고수량</th>
            <th scope="col" class="tac">통보수량</th>
            <th scope="col" class="tac">사용여부</th>
        </tr>
    </thead>
    <tbody>
        <?=$tableTag?>
    </tbody>
</table>
<div class="mart5">
	<button type="button" id="sel_supply_delete" onclick="sel_supply_deleteFun()" class="btn_small bx-white">선택삭제</button>
</div>
<div class="mart15">
	<b>옵션 일괄 적용</b> <span class="fs11 fc_197">전체 추가 옵션의 상품금액, 재고/통보수량 및 사용여부를 일괄 적용할 수 있습니다. 단, 체크된 항목만 적용됨!</span>
</div>

<table class="mart5 tablef">
    <colgroup>
        <col class="w100">
        <col>
        <col class="w100">
        <col>
    </colgroup>
    <tbody>
        <tr>
            <th scope="row">
                <label for="spl_com_price">추가금액</label>
                <label for="spl_com_price_chk" class="sound_only">추가금액일괄수정</label>
            </th>
            <td>
                <input type="checkbox" name="spl_com_price_chk" value="1" id="spl_com_price_chk" class="spl_com_chk">
                <input type="text" name="spl_com_price" value="0" id="spl_com_price" class="frm_input w80">
            </td>
            <th scope="row">
                <label for="spl_com_stock">재고수량</label>
                <label for="spl_com_stock_chk" class="sound_only">재고수량일괄수정</label>
            </th>
            <td>
                <input type="checkbox" name="spl_com_stock_chk" value="1" id="spl_com_stock_chk" class="spl_com_chk">
                <input type="text" name="spl_com_stock" value="0" id="spl_com_stock" class="frm_input w80">
                <a href="javascript:chk_obj('spl_com_stock', '999999999');" class="btn_small bx-white">무제한</a>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="spl_com_use">사용여부</label>
                <label for="spl_com_use_chk" class="sound_only">사용여부일괄수정</label>
            </th>
            <td>
                <input type="checkbox" name="spl_com_use_chk" value="1" id="spl_com_use_chk" class="spl_com_chk">
                <select name="spl_com_use" id="spl_com_use">
                    <option value="1">사용함</option>
                    <option value="0">미사용</option>
                </select>
            </td>
            <th scope="row">
                <label for="spl_com_noti">통보수량</label>
                <label for="spl_com_noti_chk" class="sound_only">통보수량일괄수정</label>
            </th>
            <td>
                <input type="checkbox" name="spl_com_noti_chk" value="1" id="spl_com_noti_chk" class="spl_com_chk">
                <input type="text" name="spl_com_noti" value="0" id="spl_com_noti" class="frm_input w80">
                <a href="javascript:chk_obj('spl_com_noti', '999999999');" class="btn_small bx-white">무제한</a>
            </td>
        </tr>
    </tbody>
</table>
<div class="btn_confirm02">
	<button type="button" id="spl_value_apply" onclick="spl_value_applyFun()" class="btn_small grey">옵션일괄적용</button>
</div>
<script language="javascript">
function chk_obj(fld, num){
	document.getElementById(fld).value = num;
}
</script>