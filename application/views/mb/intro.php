<form name="flogin" action="<?php echo base_url(); ?>m/bbs/login_check" onsubmit="return flogin_submit(this);" method="post">
<div class="mb_login">
	<section class="login_fs">
		<h2 class="log_tit">MEMBER <strong>LOGIN</strong></h2>
		<p class="mart15">
			<label for="login_id" class="sound_only">회원아이디</label>
			<input type="text" name="mb_id" id="login_id" maxLength="20" placeholder="아이디">
		</p>
		<p class="mart3">
			<label for="login_pw" class="sound_only">비밀번호</label>
			<input type="password" name="mb_password" id="login_pw" maxLength="20" placeholder="비밀번호">
		</p>
		<p class="mart10 tal">
			<input type="checkbox" name="auto_login" id="login_auto_login" class="css-checkbox lrg">
			<label for="login_auto_login" class="css-label">자동로그인</label>
		</p>
		<p class="mart10"><input type="submit" value="로그인" class="btn_large wset wfull"></p>
		<p class="mart3"><a href="<?php echo base_url(); ?>m/bbs/register?type=user" class="btn_medium bx-white wfull">회원가입</a></p>
		<p class="mart7 tar"><span><a href="<?php echo base_url(); ?>m/bbs/password_lost">아이디/비밀번호 찾기</a></span></p>
	</section>
</div>
</form>
<script>
$(function(){
    $("#login_auto_login").click(function(){
        if (this.checked) {
            this.checked = confirm("자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?");
        }
    });
});

function flogin_submit(f)
{
	if(!f.mb_id.value){
		alert('아이디를 입력하세요.');
		f.mb_id.focus();
		return false;
	}
	if(!f.mb_password.value){
		alert('비밀번호를 입력하세요.');
		f.mb_password.focus();
		return false;
	}

	return true;
}
</script>