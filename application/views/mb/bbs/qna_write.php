<form name="fqaform" id="fqaform" method="post" action="<?php echo $form_action_url; ?>" onsubmit="return fqaform_submit(this);" autocomplete="off">
    <input type="hidden" name="mode" value="w">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <div class="m_bo_bg">
        <div class="m_bo_wrap">
            <table class="tbl03">
                <colgroup>
                    <col style="width:70px">
                    <col style="width:auto">
                </colgroup>
                <tbody>
                    <tr>
                        <th>질문유형</th>
                        <td>
                            <select name="catename" required itemname="질문유형" class="wfull">
                                <option value="">문의하실 유형을 선택하세요</option>
                                <?=$selectOpt?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>제목</th>
                        <td><input type="text" name="subject" required itemname="제목"></td>
                    </tr>
                    <tr>
                        <th>질문내용</th>
                        <td><textarea name="memo" required rows="7" itemname="질문내용"></textarea></td>
                    </tr>
                    <tr>
                        <th>이메일</th>
                        <td>
                            <input type="text" name="email" value="<?php echo $member['email'];?>">
                            <p class="mart5 fs12">
                                <span class="marr10">답변 내용을 메일로 받아보시겠습니까?</span>
                                <input type="checkbox" name="email_send_yes" value="1" id="email_send_yes" class="css-checkbox lrg"> <label for="email_send_yes" class="css-label">예</label>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>휴대폰</th>
                        <td>
                            <input type="text" name="cellphone" value="<?php echo $member['cellphone']; ?>">
                            <p class="mart5 fs12">
                                <span class="marr10">답변 여부를 문자로 받아보시겠습니까?</span>
                                <input type="checkbox" name="sms_send_yes" value="1" id="sms_send_yes" class="css-checkbox lrg"> <label for="sms_send_yes" class="css-label">예</label>
                            </p>
                        </td>
                    </tr>
                </tbody>
		    </table>
	    </div>
        <div class="btn_confirm">
            <input type="submit" value="글쓰기" class="btn_medium">
            <a href="javascript:history.go(-1);" class="btn_medium bx-white">취소</a>
        </div>
    </div>
</form>

<script>
function fqaform_submit(f) {
	if(confirm("등록 하시겠습니까?") == false)
		return false;

	return true;
}
</script>