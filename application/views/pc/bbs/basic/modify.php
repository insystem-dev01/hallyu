<?=$b_content_head?>
<form name="fboardform" id="fboardform" method="post" action="<?php echo $from_action_url; ?>" onsubmit="return fboardform_submit(this);" autocomplete="off" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="index_no" value="<?php echo $index_no; ?>">
    <input type="hidden" name="boardid" value="<?php echo $boardid; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
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
                        <?=$nameTag?>
                    </td>
	            </tr>
                <?=$pwdTag?>
                <?=$use_categoryOne?>
                <?=$option_hidden?>
                <?=$optionTag?>
                <tr>
                    <th scope="row">제목</th>
                    <td><input type="text" name="subject" value="<?php echo $write['subject']; ?>" class="frm_input wfull"></td>
                </tr>
                <tr>
                    <th scope="row">내용</th>
                    <td>
                        <?php echo $editor_memo; ?>
                    </td>
                </tr>
                <?=$usefileY?>
            </tbody>
	    </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="글쓰기" class="btn_lsmall">
        <a href="javascript:history.go(-1);" class="btn_lsmall bx-white">취소</a>
    </div>
</form>
<script>
function fboardform_submit(f){
    <?=$isnotMemberJs?>
    <?=$use_categoryOneJs?>
    if(!f.subject.value){
		alert('제목을 입력하세요.');
		f.subject.focus();
		return false;
	}
    <?=$memoGetJs?>
    <?=$memoChkJs?>
    return true;
}
</script>
<?=$b_content_tail?>