<?=$snsJs?>
<h2 class="stit">로그인</h2>

<form name="flogin" action="<?php echo $login_action_url; ?>" onsubmit="return flogin_submit(this);" method="post">
    <input type="hidden" name="url" value="<?php echo $login_url; ?>">
    <div class="login_wrap" id="login_fld">		
		<dl class="log_inner">
			<dd>
				<label for="login_id" class="sound_only">회원아이디</label>
				<input type="text" name="mb_id" id="login_id" class="frm_input" maxLength="20" placeholder="아이디를 입력해주세요">	
			</dd>
			<dd>
				<label for="login_pw" class="sound_only">비밀번호</label>
				<input type="password" name="mb_password" id="login_pw" class="frm_input" maxLength="20" placeholder="비밀번호를 입력해주세요">		
			</dd>
			<dd class="log_op">
				<!-- <span><input type="checkbox" name="auto_login" id="login_auto_login"> <label for="login_auto_login">자동로그인</label></span>	 -->
				<span class="fr"><a href="<?php echo base_url(); ?>bbs/password_lost" onclick="win_open(this,'pop_password_lost','500','400','no');return false;">아이디 / 비밀번호 찾기</a></span>
			</dd>
			<dd class="log_btn">
				<button type="submit" class="btn_login">로그인</button>
				<a href="<?php echo base_url(); ?>bbs/register?type=user" class="btn_join">회원가입</a>
			</dd>
			<?=$notLogoOrd?>
		</dl>
		<?=$snsLogin?>
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
$(document).ready(function(){
	$(".login_tab>li:eq(0)").addClass('active');
	$("#login_fld").addClass('active');

	$(".login_tab>li").click(function() {
		var activeTab = $(this).attr('data-tab');
		$(".login_tab>li").removeClass('active');
		$(".login_wrap").removeClass('active');
		$(this).addClass('active');
		$("#"+activeTab).addClass('active');
	});
});
</script>