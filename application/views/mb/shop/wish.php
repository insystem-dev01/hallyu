<div id="sod_ws">
	<p id="sod_fin_no">
		<strong>총 <?php echo number_format($wish_count); ?>개</strong>의 상품이 보관되어 있습니다.
	</p>
	<form name="fwishlist" id="fwishlist" method="post">
        <input type="hidden" name="act" value="multi">
        <input type="hidden" name="sw_direct">
        <?=$wishTable?>
    </form>
</div>
<script>
<!--
function out_cd_check(fld, out_cd){
	if(out_cd == 'no'){
		alert("옵션이 있는 상품입니다.\n\n상품을 클릭하여 상품페이지에서 옵션을 선택한 후 주문하십시오.");
		fld.checked = false;
		return;
	}

	if(out_cd == 'price_msg'){
		alert("이 상품은 전화로 문의해 주십시오.\n\n장바구니에 담아 구입하실 수 없습니다.");
		fld.checked = false;
		return;
	}
}

function fwishlist_check(f, act){
	var base_url=document.getElementById('base_url').value;
	var k = 0;
	var length = f.elements.length;

	for(i=0; i<length; i++) {
		if(f.elements[i].checked) {
			k++;
		}
	}

	if(k == 0){
		alert("상품을 하나 이상 체크 하십시오");
		return false;
	}

	if(act == "direct_buy"){
		f.sw_direct.value = 1;
	}
	else{
		f.sw_direct.value = 0;
	}

	f.action = base_url+"m/process/cartupdate";
	f.submit();
}
//-->
</script>
