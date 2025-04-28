<table class="mart20">
    <colgroup>
        <col class="w40">
        <col>
        <col class="w80">
        <col class="w70">
        <col class="w70">
        <col class="w90">
    </colgroup>
    <thead>
        <tr>
            <th scope="col" class="tac"><input type="checkbox" name="opt_chk_all" onclick="opt_chk_allFun(this)" value="1" id="opt_chk_all"></th>
            <th scope="col" class="tac">옵션</th>
            <th scope="col" class="tac">추가금액</th>
            <th scope="col" class="tac">재고수량</th>
            <th scope="col" class="tac">통보수량</th>
            <th scope="col" class="tac">사용여부</th>
        </tr>
    </thead>
    <tbody>
        <?=$tableOne?>
    </tbody>
</table>
<div class="mart5">
	<button type="button" id="sel_option_delete" onclick="sel_option_deleteFun()" class="btn_small bx-white">선택삭제</button>
</div>
<div class="mart15">
	<b>옵션 일괄 적용</b> <span class="fs11 fc_197">전체 옵션의 추가금액, 재고/통보수량 및 사용여부를 일괄 적용할 수 있습니다. 단, 체크된 항목만 적용됨!</span>
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
                <label for="opt_com_price">추가금액</label>
                <label for="opt_com_price_chk" class="sound_only">추가금액일괄수정</label>
            </th>
            <td>
                <input type="checkbox" name="opt_com_price_chk" value="1" id="opt_com_price_chk" class="opt_com_chk">
                <input type="text" name="opt_com_price" value="0" id="opt_com_price" class="frm_input w80">
            </td>
            <th scope="row">
                <label for="opt_com_stock">재고수량</label>
                <label for="opt_com_stock_chk" class="sound_only">재고수량일괄수정</label>
            </th>
            <td>
                <input type="checkbox" name="opt_com_stock_chk" value="1" id="opt_com_stock_chk" class="opt_com_chk">
                <input type="text" name="opt_com_stock" value="0" id="opt_com_stock" class="frm_input w80">
                <a href="javascript:chk_obj('opt_com_stock', '999999999');" class="btn_small bx-white">무제한</a>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="opt_com_use">사용여부</label>
                <label for="opt_com_use_chk" class="sound_only">사용여부일괄수정</label>
            </th>
            <td>
                <input type="checkbox" name="opt_com_use_chk" value="1" id="opt_com_use_chk" class="opt_com_chk">
                <select name="opt_com_use" id="opt_com_use">
                    <option value="1">사용함</option>
                    <option value="0">미사용</option>
                </select>
            </td>
            <th scope="row">
                <label for="opt_com_noti">통보수량</label>
                <label for="opt_com_noti_chk" class="sound_only">통보수량일괄수정</label>
            </th>
            <td>
                <input type="checkbox" name="opt_com_noti_chk" value="1" id="opt_com_noti_chk" class="opt_com_chk">
                <input type="text" name="opt_com_noti" value="0" id="opt_com_noti" class="frm_input w80">
                <a href="javascript:chk_obj('opt_com_noti', '999999999');" class="btn_small bx-white">무제한</a>
            </td>
        </tr>
    </tbody>
</table>
<div class="btn_confirm02">
	<button type="button" id="opt_value_apply" class="btn_small grey" onclick="opt_value_applyFun(this)">옵션일괄적용</button>
</div>
<script language="javascript">
function chk_obj(fld, num){
	document.getElementById(fld).value = num;
}
</script>