<!-- 회원 비밀번호 확인 시작 { -->
<div id="mb_confirm">
    <h1 class="tac"><?php echo $title; ?></h1>
    <p>
        <strong>비밀번호를 한번 더 입력해주세요.</strong>
        회원님의 정보를 안전하게 보호하기 위해 비밀번호를 한번 더 확인합니다.
    </p>
    <form name="fmemberconfirm" action="<?php echo $url; ?>" onsubmit="return fmemberconfirm_submit(this);" method="post">
        <input type="hidden" name="mb_id" value="<?php echo $member['id']; ?>">
        <input type="hidden" name="w" value="u">
        <fieldset>
            회원아이디
            <span id="mb_confirm_id"><?php echo $member['id']; ?></span>
            <label for="confirm_mb_password">비밀번호</label>
            <input type="password" name="mb_password" id="confirm_mb_password" required class="required frm_input" size="15" maxLength="20">
            <button type="submit" id="btn_submit" class="btn_small">확인</button>
        </fieldset>
    </form>
    <div class="btn_confirm">
        <a href="<?php echo base_url(); ?>" class="btn_medium grey">메인으로 돌아가기</a>
    </div>
</div>
<script>
function fmemberconfirm_submit(f){
    // document.getElementById("btn_submit").disabled = true;

    return true;
}
</script>
<!-- } 회원 비밀번호 확인 끝 -->
