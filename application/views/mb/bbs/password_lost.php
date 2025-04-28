<form name="fpasswordlost" action="<?php echo $form_action_url; ?>" method="post" autocomplete="off">
<input type="hidden" name="token" value="<?php echo $token; ?>">

<div class="m_bo_pop">
	<p>
		회원가입 시 등록하신 이메일 주소를 입력해 주세요.<br>
		해당 이메일로 아이디와 비밀번호 정보를 보내드립니다.
	</p>
	<div class="formbox" style="margin:10px 0 0 0;">
		<input type="text" name="mb_email" id="mb_email" required email itemname="E-mail 주소" placeholder="E-mail 주소">
	</div>
	<p class="btn_confirm">
		<input type="submit" value="확인" class="btn_medium">
		<a href="javascript:history.go(-1);" class="btn_medium bx-white">취소</a>
	</p>
</div>
</form>