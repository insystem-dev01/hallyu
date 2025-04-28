<div id="sit_qa_write" class="new_win">
    <h1 id="win_title"><?php echo $title; ?></h1>

	<form name="fitemqa" id="fitemqa" method="post" action="<?php echo $form_action_url; ?>" onsubmit="return fitemqa_submit(this);">
        <input type="hidden" name="w" value="<?php echo $w; ?>">
        <input type="hidden" name="mb_id" value="<?php echo $member['id']; ?>">
        <input type="hidden" name="gs_id" value="<?php echo $gs_id; ?>">	
        <input type="hidden" name="gs_use_aff" value="<?php echo $gs['use_aff']; ?>">
        <input type="hidden" name="seller_id" value="<?php echo $gs['mb_id']; ?>">	
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <input type="hidden" name="iq_id" value="<?php echo $iq_id; ?>">
        <div class="tbl_frm01 tbl_wrap">
            <table>
                <colgroup>
                    <col class="w90">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">상품명</th>
                        <td><?php echo $gs['gname']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">옵션</th>
                        <td>
                            <select name="iq_ty" required itemname="문의유형">
                                <option value=""<?php echo $selectoptTheme; ?>>문의유형(선택)</option>
                                <option value="상품"<?php echo $selectoptGs; ?>>상품</option>
                                <option value="배송"<?php echo $selectoptDeliver; ?>>배송</option>
                                <option value="반품/환불/취소"<?php echo $selectoptCancel; ?>>반품/환불/취소</option>
                                <option value="교환/변경"<?php echo $selectoptChange; ?>>교환/변경</option>
                                <option value="기타"<?php echo $selectoptEct; ?>>기타</option>
                            </select>
                            <input id="iq_secret" type="checkbox" name="iq_secret" value="1"
                            <?php echo $secretTag; ?> class="marl7">
                            <label for="iq_secret">비밀글</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">성명</th>
                        <td><input type="text" name="iq_name" value="<?php echo $iq_name; ?>" required itemname="성명" class="frm_input required" size="20"></td>
                    </tr>
                    <tr>
                        <th scope="row">이메일</th>
                        <td><input type="text" name="iq_email" value="<?php echo $iq_email; ?>" required email itemname="이메일" class="frm_input required" size="30"></td>
                    </tr>
                    <tr>
                        <th scope="row">핸드폰</th>
                        <td><input type="text" name="iq_hp" value="<?php echo $iq_hp; ?>" required itemname="핸드폰" class="frm_input required" size="20"></td>
                    </tr>
                    <tr>
                        <th scope="row">제목</th>
                        <td><input type="text" name="iq_subject" value="<?php echo $iq_subject; ?>" required itemname="제목" class="frm_input wfull required"></td>
                    </tr>
                    <tr>
                        <th scope="row">질문</th>
                        <td><textarea name="iq_question" rows="10" required itemname="질문" class="frm_textbox wufll required"><?php echo $iq_question; ?></textarea></td>
                    </tr>
                </tbody>
            </table>
	    </div>
        <div class="win_btn">
            <input type="submit" value="작성완료" class="btn_lsmall">
            <a href="javascript:window.close();" class="btn_lsmall bx-white">창닫기</a>
        </div>
	</form>
</div>
<script>
function fitemqa_submit(f) {
	if(confirm("등록 하시겠습니까?") == false)
		return false;

    return true;
}
</script>
