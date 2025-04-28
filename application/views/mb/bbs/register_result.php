<!-- 회원가입결과 시작 { -->
<div id="reg_result">
    <p>
        <strong><?php echo $mbName; ?></strong>님의 회원가입을 진심으로 축하합니다.<br>
    </p>
    <div id="result_email">
        <span>아이디</span>
        <strong><?php echo $mb['id']; ?></strong><br>
        <span>E-mail</span>
        <strong><?php echo $mb['email']; ?></strong>
    </div>
    <p>
        회원님의 비밀번호는 아무도 알 수 없는 암호화 코드로 저장되므로 안심하셔도 좋습니다.<br>
        아이디, 비밀번호 분실시에는 회원가입시 입력하신 이메일 주소를 이용하여 찾을 수 있습니다.
    </p>
    <p>
        회원 탈퇴는 언제든지 가능하며 회원 탈퇴시 회원님의 정보는 영구삭제하고 있습니다.<br>
        감사합니다.
    </p>
    <p class="homebtn">
		<a href="<?php echo base_url().'m/main'; ?>"  class="btn_medium grey">메인으로 돌아가기</a>
	</p>
</div>
<!-- } 회원가입결과 끝 -->