<!-- 회원 비밀번호 확인 시작 { -->
<div id="mb_confirm">
    <p><strong>비밀번호를 한번 더 입력해주세요.</strong></p>
	<p>정보를 안전하게 보호하기 위해 비밀번호를 한번 더 확인합니다.</p>

    <form name="fmemberconfirm" action="<?php echo $url; ?>" onsubmit="return fmemberconfirm_submit(this);" method="post">
    <input type="hidden" name="mb_id" value="<?php echo $member['id']; ?>">
    <input type="hidden" name="w" value="u">

    <fieldset>
        <span id="mb_confirm_id">회원아이디 <b><?php echo $member['id'] ?></b></span>
        <label for="confirm_mb_password">비밀번호<strong class="sound_only">필수</strong></label>
        <input type="password" name="mb_password" id="confirm_mb_password" required class="required frm_input" size="15" maxLength="20">
        <input type="submit" value="확인" id="btn_submit" class="btn_small">
    </fieldset>

    </form>

    <p class="homebtn">
		<a href="<?php echo base_url().'m/main'; ?>" class="btn_medium grey">메인으로 돌아가기</a>
	</p>

</div>

<script>
function fmemberconfirm_submit(f){
    document.getElementById("btn_submit").disabled = true;
    return true;
}
</script>
<!-- } 회원 비밀번호 확인 끝 -->
