<div id="find_info" class="new_win">
	<h1 id="win_title"><?php echo $title; ?></h1>
    <form name="fpasswordlost" action="<?php echo $form_action_url; ?>" method="post" autocomplete="off">
	<input type="hidden" name="token" value="<?php echo $token; ?>">
	<fieldset id="info_fs">
		<p>
			회원가입 시 등록하신 이메일 주소를 입력해 주세요.<br>
			해당 이메일로 아이디와 비밀번호 정보를 보내드립니다.
		</p>
		<div class="info_form">
			<label for="mb_email">E-mail 주소<strong class="sound_only">필수</strong></label>
			<input type="text" name="mb_email" id="mb_email" required email itemname="E-mail 주소" class="required frm_input" size="30">
		</div>
	</fieldset>

	<div class="win_btn">
		<input type="submit" class="btn_lsmall" value="확인">
		<button type="button" class="btn_lsmall bx-white" onclick="window.close();">창닫기</button>
	</div>	
	</form>
</div>
