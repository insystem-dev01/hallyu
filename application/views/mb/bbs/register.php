<form  name="fregister" id="fregister" action="<?php echo $register_action_url; ?>" onsubmit="return fregister_submit(this);" method="POST" autocomplete="off">
    <div class="s_cont">
        <?=$snsBox?>
        <div class="fregister_agree">
            <h3 class="fr_tit">
                회원가입 약관 (필수)
                <a href="<?php echo base_url();?>m/bbs/provision" target="_blank" class="btn_small bx-white">전문보기</a>
            </h3>
            <div class="agree_txt"><?php echo nl2br($config['shop_provision']); ?></div>
            <p class="agree_chk">
                <input name="agree" type="checkbox" value="1" id="agree11" class="css-checkbox">
                <label for="agree11" class="css-label">회원가입 약관의 내용에 동의합니다</label>
            </p>
        </div>
        <div class="fregister_agree">
            <h3 class="fr_tit">
                개인정보 수집 및 이용 (필수)
                <a href="<?php echo base_url(); ?>m/bbs/policy" target="_blank" class="btn_small bx-white">전문보기</a>
            </h3>
            <div class="agree_txt"><?php echo nl2br($config['shop_private']); ?></div>
            <p class="agree_chk">
                <input name="agree2" type="checkbox" value="1" id="agree21" class="css-checkbox">
                <label for="agree21" class="css-label">개인정보 수집 및 이용 내용에 동의합니다.</label>
            </p>
        </div>
        <div class="fregister_agree2">
            <input type="checkbox" name="chk_all" value="1" id="chk_all" class="css-checkbox">
            <label for="chk_all" class="css-label">모든 약관을 확인하고 전체 동의합니다.</label>
        </div>

        <div class="btn_confirm">
            <input type="submit" value="확인" class="btn_medium wset">
            <a href="<?php echo base_url();?>m/main" class="btn_medium bx-white">취소</a>
        </div>
    </div>
</form>
<script>
function fregister_submit(f){
	if(!f.agree.checked) {
		alert("회원가입 약관 내용에 동의하셔야 회원가입 하실 수 있습니다.");
		f.agree.focus();
		return false;
	}

	if(!f.agree2.checked) {
		alert("개인정보 수집 및 이용 내용에 동의하셔야 회원가입 하실 수 있습니다.");
		f.agree2.focus();
		return false;
	}

	return true;
}
jQuery(function($){
	// 모두선택
	$("input[name=chk_all]").click(function() {
		if ($(this).prop('checked')) {
			$("input[name^=agree]").prop('checked', true);
		} else {
			$("input[name^=agree]").prop("checked", false);
		}
	});

	$("input[name^=agree]").click(function() {
		$("input[name=chk_all]").prop("checked", false);
	});
});
</script>
