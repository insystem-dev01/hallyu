<!-- 장바구니 옵션 시작 { -->
    <form name="foption" method="post" action="<?php echo base_url(); ?>process/cartupdate" onsubmit="return formcheck(this);">
    <input type="hidden" name="act" value="optionmod">
    <input type="hidden" name="gs_id[]" value="<?php echo $gs_id; ?>">
    <input type="hidden" name="ca_id[<?=$gs_id?>][]" value="<?php echo $ca_id; ?>">
    <input type="hidden" id="it_price" value="<?php echo $row2['ct_price']; ?>">
    <?=$option1_tag?>
    <?=$option2_tag?>
    <div id="option_set_list">
	    <ul id="option_set_added">
            <?=$cart_obt_list?>
        </ul>
    </div>
    <div id="sit_tot_views" class="dn">
        <span class="fl">총 합계금액</span>
        <span id="sit_tot_price" class="prdc_price fc_red"></span>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="확인" class="btn_lsmall">
        <button type="button" onclick="mod_option_close()" class="btn_lsmall bx-white">취소</button>
    </div>
</form>

<script>
function formcheck(f){
    var val, io_type, result = true;
    var sum_qty = 0;
    var min_qty = parseInt('<?php echo $it_buy_min_qty; ?>');
    var max_qty = parseInt('<?php echo $it_buy_max_qty; ?>');
    var $el_type = $("input[name^=io_type]");

    $("input[name^=ct_qty]").each(function(index) {
        val = $(this).val();

        if(val.length < 1) {
            alert("수량을 입력해 주십시오.");
            result = false;
            return false;
        }

        if(val.replace(/[0-9]/g, "").length > 0) {
            alert("수량은 숫자로 입력해 주십시오.");
            result = false;
            return false;
        }

        if(parseInt(val.replace(/[^0-9]/g, "")) < 1) {
            alert("수량은 1이상 입력해 주십시오.");
            result = false;
            return false;
        }

        io_type = $el_type.eq(index).val();
        if(io_type == "0")
            sum_qty += parseInt(val);
    });

    if(!result) {
        return false;
    }

    if(min_qty > 0 && sum_qty < min_qty) {
		alert("주문옵션 개수 총합 "+number_format(String(min_qty))+"개 이상 주문해 주십시오.");
        return false;
    }

    if(max_qty > 0 && sum_qty > max_qty) {
		alert("주문옵션 개수 총합 "+number_format(String(max_qty))+"개 이하로 주문해 주십시오.");
        return false;
    }

    return true;
}
</script>
<!-- } 장바구니 옵션 끝 -->
