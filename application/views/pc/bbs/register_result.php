<!-- 회원가입결과 시작 { -->
<div id="reg_result">
	<div class="bx-danger">
		<h4 class="fs18"><strong><?php echo $name; ?></strong>님의 회원가입을 진심으로 축하합니다.</h4>

		<p id="result_email">
			<span>아이디</span>
			<strong><?php echo $mb['id']; ?></strong><br>
			<span>이메일 주소</span>
			<strong><?php echo $mb['email']; ?></strong>
		</p>

		<p>회원님의 비밀번호는 아무도 알 수 없는 암호화 코드로 저장되므로 안심하셔도 좋습니다.</p>
		<p>아이디, 비밀번호 분실시에는 회원가입시 입력하신 이메일 주소를 이용하여 찾을 수 있습니다.</p>
		<p>회원 탈퇴는 언제든지 가능하며 회원 탈퇴시 회원님의 정보는 영구삭제하고 있습니다.</p>
		<p>감사합니다.</p>
	</div>

    <div class="btn_confirm">
        <a href="<?php echo base_url(); ?>" class="btn_medium">메인으로</a>
    </div>

</div>
<!-- } 회원가입결과 끝 -->