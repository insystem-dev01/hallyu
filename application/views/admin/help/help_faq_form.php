<?=$headTitle?>
<form name="faqform" method="post" onsubmit="return faqform_submit(this);">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="sca" value="<?php echo $sca; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="index_no" value="<?php echo $index_no; ?>">
    
    <div class="tbl_frm02">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">분류</th>
                    <td>
                        <select name="faq_cate">
                            <?=$faq_cateTag?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">제목</th>
                    <td>
                        <input type="text" name="subject" value="<?php echo $faq['subject']; ?>" required itemname="제목" class="frm_input required" size="60">
                    </td>
                </tr>
                <tr>
                    <th scope="row">내용</th>
                    <td>
                        <?php echo $editor_memo; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
        <a href="<?=base_url()?>admin/help?code=faq<?php echo $qstr; ?>&page=<?php echo $page; ?>" class="btn_large bx-white">목록</a>
    </div>
</form>
<script>
function faqform_submit(f) {
    var base_url=document.getElementById('base_url').value;
	<?php echo $editor_memoJs; ?>

	f.action = base_url+"AdminProcess/help_faq_form_update";
    return true;
}
</script>
<?=$headTitleEnd?>
