<form name="fboardform" id="fboardform" method="post" action="<?php echo $from_action_url; ?>" onsubmit="return fboardform_submit(this);" autocomplete="off" enctype="MULTIPART/FORM-DATA">
<input type="hidden" name="mode" value="w">
<input type="hidden" name="boardid" value="<?php echo $boardid; ?>">
<input type="hidden" name="token" value="<?php echo $token; ?>">
	
<div class="tbl_frm01 tbl_wrap">
	<table>
        <colgroup>
            <col class="w100">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <th scope="row">이름</th>
                <td>
                    <?=$is_memeberTag?>
                </td>
            </tr>
            <?=$passTag?>
            <?=$use_category?>
            <?=$option_hidden?>
            <?=$optionTag?>
            <tr>
                <th scope="row">제목</th>
                <td><input type="text" name="subject" class="frm_input wfull"></td>
            </tr>
            <tr>
                <th scope="row">내용</th>
                <td>
                    <?php echo $editTag; ?>
                </td>
            </tr>
            <?=$usefileTag?>
        </tbody>
	</table>
</div>
<div class="btn_confirm">
	<input type="submit" value="글쓰기" class="btn_lsmall">
	<a href="javascript:history.go(-1);" class="btn_lsmall bx-white">취소</a>
</div>
</form>

<script>
function fboardform_submit(f) {
	<?=$not_is_memberJs ?>

	<?=$useCateJs?>

	if(!f.subject.value) {
		alert('제목을 입력하세요.');
		f.subject.focus();
		return false;
	}
    <?=$memoEditJs?>
    <?=$memoChkJs?>
    
    return true;
}
</script>
