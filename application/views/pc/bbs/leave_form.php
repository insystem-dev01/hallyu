<?=$aside_my?>
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>마이페이지<i>&gt;</i><?php echo $title; ?></p>
	</h2>
    <form name="fleaveform" id="fleaveform" method="post" action="<?php echo $form_action_url; ?>" onsubmit="return fleaveform_submit(this);" autocomplete="off">
        <div class="tbl_frm01 tbl_wrap">
            <table>
                <colgroup>
                    <col width="140">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">고객명(ID)</th>
                        <td><b><?php echo $member['name']; ?></b> (<?php echo $member['id']; ?>)</td>
                    </tr>
                    <tr>
                        <th scope="row">보유포인트</th>
                        <td><b><?php echo number_format($member['point']); ?>P</b> <span class="fc_red marl10">※ 탈퇴이후 포인트는 모두 소멸됩니다.</span></td>
                    </tr>
                    <tr>
                        <th scope="row">E-Mail</th>
                        <td><?php echo $email?></td>
                    </tr>
                    <tr>
                        <th scope="row">핸드폰</th>
                        <td><?php echo $cellphone; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">현재비밀번호</th>
                        <td><input type="password" name="mb_password" required itemname="현재비밀번호" class="frm_input required" size="20" minlength="4" maxlength="20"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <section>
            <h2 class="anc_tit">탈퇴 이유에 대해 고객님의 소중한 의견 남겨주시면 보다 나은 서비스를 위해 노력하겠습니다.</h2>
            <ul>
                <li>
                    <input type="radio" name="memo" id="memo1" value="다른 ID로 변경">
                    <label for="memo1">다른 ID로 변경</label>
                </li>
                <li>
                    <input type="radio" name="memo" id="memo2" value="회원가입의 혜택이 적음">
                    <label for="memo2">회원가입의 혜택이 적음</label>
                </li>
                <li>
                    <input type="radio" name="memo" id="memo3" value="개인정보(통신 및 신용정보)의 노출 우려">
                    <label for="memo3">개인정보(통신 및 신용정보)의 노출 우려</label>
                </li>
                <li>
                    <input type="radio" name="memo" id="memo4" value="시스템장애 (속도저조,잦은에러등)">
                    <label for="memo4">시스템장애 (속도저조,잦은에러등)</label>
                </li>
                <li>
                    <input type="radio" name="memo" id="memo5" value="서비스의 불만 (늦은배송, 가격불만족, 복잡한 절차등)">
                    <label for="memo5">서비스의 불만 (늦은배송, 가격불만족, 복잡한 절차등)</label>
                </li>
                <li>
                    <input type="radio" name="memo" id="memo6" value="장시간의부재">
                    <label for="memo6">장시간의부재</label>
                </li>
                <li>
                    <input type="radio" name="memo" id="memo7" value="기타" onclick="showDiv('other');">
                    <label for="memo7">기타</label> <input type="text" class="frm_input marl10" size="60" name="other" style="visibility:hidden">
                </li>
            </ul>
        </section>
        <div class="btn_confirm">
            <input type="submit" value="회원탈퇴" class="btn_large wset">
            <a href="javascript:history.go(-1);" class="btn_large bx-white">취소</a>
        </div>
	</form>
</div>
<script>
function fleaveform_submit(f) {
	if(confirm("정말 회원탈퇴 하시겠습니까?") == false)
		return false;

    return true;
}

function showDiv( id ) {
    document.all.other.style.visibility = 'hidden';
    document.all.other.value = '';
    document.all[ id ].style.visibility = 'visible';
    document.all[ id ].focus();
}
</script>
