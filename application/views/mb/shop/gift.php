<!-- 쿠폰인증 시작 { -->
    <div id="sct_hdesc">
	<fieldset class="coupon_se">
		<h3>쿠폰번호 16자를 입력해주세요.</h3>
		<form name="fcouponcard" action="<?php echo $form_action_url; ?>" onsubmit="return form_check(this);" method="post" autocomplete="off">
		<input type="hidden" name="token" value="<?php echo $token; ?>">
		<div class="frm_inner">
			<input type="text" name="gi_num1" id="gi_num1" class="gi_num" maxlength="4" onkeyup="if(this.value.length==4) document.fcouponcard.gi_num2.focus();">
			<input type="text" name="gi_num2" id="gi_num2" class="gi_num" maxlength="4" onkeyup="if(this.value.length==4) document.fcouponcard.gi_num3.focus();">
			<input type="text" name="gi_num3" id="gi_num3" class="gi_num" maxlength="4" onkeyup="if(this.value.length==4) document.fcouponcard.gi_num4.focus();">
			<input type="text" name="gi_num4" id="gi_num4" class="gi_num" maxlength="4">			
		</div>	
		<div class="tac">
			<button type="submit" id="btn_submit" class="btn_large wset">쿠폰등록</button>
		</div>
		</form>

		<script>
		function form_check(f) {
			errmsg = "";
			errfld = "";
			check_field(f.gi_num1, "첫번째 쿠폰번호를 입력하십시오.");
			check_field(f.gi_num2, "두번째 쿠폰번호를 입력하십시오.");
			check_field(f.gi_num3, "세번째 쿠폰번호를 입력하십시오.");
			check_field(f.gi_num4, "네번째 쿠폰번호를 입력하십시오.");
			if(errmsg){
				alert(errmsg);
				errfld.focus();
				return false;
			}
			var gi_num = 0;
			gi_num += f.gi_num1.value.length;
			gi_num += f.gi_num2.value.length;
			gi_num += f.gi_num3.value.length;
			gi_num += f.gi_num4.value.length;
			if(gi_num < 16) {
				alert("쿠폰번호는 모두 16자 입니다. 확인 후 다시 등록 바랍니다.");
				return false;
			}
			document.getElementById("btn_submit").disabled = true;
			return true;
		}
		</script>
	</fieldset>	
</div>
<p id="page_title">
	<em>총 <?php echo number_format($total_count); ?>건</em>의 쿠폰내역이 있습니다.
</p>
<div id="scp_list">
	<ul>
        <?=$giftTable?>
    </ul>
</div>
<!-- } 쿠폰인증 끝 -->
