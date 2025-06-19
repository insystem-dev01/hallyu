<form name="fwrite" id="fwrite" method="post" action="<?php echo $form_action_url; ?>" onsubmit="return fwrite_submit(this);" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="boardid" value="<?php echo $boardid; ?>">
    <input type="hidden" name="index_no" value="<?php echo $index_no; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <div class="m_bo_bg mart10">
        <div class="m_bo_wrap">
            <table class="tbl03">
                <colgroup>
                    <col style="width:12vw">
                    <col style="width:auto">
                </colgroup>
                <tbody>
                    <tr>
                        <th>이름</th>
                        <td>
                            <?=$is_memberTag?>
                        </td>
                    </tr>
                    <?=$pwdTag?>
                    <?=$useCateTag?>
                    <?=$option_hidden?>
                    <?=$optionTag?>
                    <tr>
                        <th>제목</th>
                        <td><input type="text" name="subject" value="<?php echo htmlspecialchars($write['subject']); ?>" class="wfull"></td>
                    </tr>
                    <tr style="border-bottom:none">
                        <th>내용</th>
                        <td>
                            <?php echo $editTag; ?>
                        </td>
                    </tr>
                    <?=$usefileTag?>
                </tbody>
		    </table>
	    </div>
        <div class="btn_confirm">
            <?=$btnConfirmTag?>
            <input type="submit" value="확인" class="btn_medium">
        </div>
    </div>
</form>
<script>
function fwrite_submit(f) {
    <?=$not_memberJs?>
    <?=$pwdJs?>
    <?=$cateJs?>
	if(!f.subject.value) {
		alert('제목을 입력하세요.');
		f.subject.focus();
		return false;
	}

    return true;
}
</script>