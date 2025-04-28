<?=$snsJs?>
<div class="mb_login">
	<form name="flogin" action="<?php echo $login_action_url; ?>" onsubmit="return flogin_submit(this);" method="post">
        <input type="hidden" name="url" value="<?php echo $login_url; ?>">
        <section class="login_fs">
            <h2>로그인</h2>
            <div class="login">
                <p>
                    <label for="login_id" class="sound_only">회원아이디</label>
                    <input type="text" name="mb_id" id="login_id" maxLength="20" placeholder="아이디">
                </p>
                <p>
                    <label for="login_pw" class="sound_only">비밀번호</label>
                    <input type="password" name="mb_password" id="login_pw" maxLength="20" placeholder="비밀번호">		
                </p>
            </div>
            <p class="tal">
                <input type="checkbox" name="auto_login" id="login_auto_login" class="css-checkbox lrg">
                <label for="login_auto_login" class="css-label">자동로그인</label>
            </p>
            <p><button type="submit" class="btn_login">로그인</button></p>
            <p><a href="<?php echo base_url(); ?>m/bbs/register?type=user" class="btn_join">회원가입</a></p>
            <p class="find"><span><a href="<?php echo base_url(); ?>m/bbs/password_lost">아이디 찾기 | 비밀번호 찾기</a></span></p>
        </section>
        <?=$snsLogin?>
    </form>
    <?=$noLogin?>
</div>
<script>
$(function(){
    $("#login_auto_login").click(function(){
        if (this.checked) {
            this.checked = confirm("자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?");
        }
    });
});
function flogin_submit(f){
	if(!f.mb_id.value) {
		alert('아이디를 입력하세요.');
		f.mb_id.focus();
		return false;
	}
	if(!f.mb_password.value) {
		alert('비밀번호를 입력하세요.');
		f.mb_password.focus();
		return false;
	}

    return true;
}
function fguest_submit(f){
	if(!f.od_id.value) {
		alert('주문번호를 입력하세요.');
		f.od_id.focus();
		return false;
	}
	if(!f.od_pwd.value) {
		alert('비밀번호를 입력해주세요.');
		f.od_pwd.focus();
		return false;
	}

    return true;
}
</script>
