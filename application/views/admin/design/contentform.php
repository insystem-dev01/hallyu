<?=$headTitle?>

<form name="frmcontentform" method="post" action="<?=base_url()?>AdminProcess/contentformupdate" onsubmit="return frmcontentform_check(this);">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page"  value="<?php echo $page; ?>">
    <input type="hidden" name="co_id" value="<?php echo $co_id; ?>">

    <div class="tbl_frm02">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">제목</th>
                    <td><input type="text" name="co_subject" value="<?php echo $co['co_subject']; ?>" required itemname="제목" class="required frm_input" size="60"></td>
                </tr>
                <?=$pageUrl?>
                <tr>
                    <th scope="row">PC 내용</th>
                    <td>
                        <?php echo $co_contentTag; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">모바일 내용</th>
                    <td>
                        <?php echo $co_mb_contentTag; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php echo $frm_submit; ?>
</form>
<script>
function frmcontentform_check(f) {
	<?php echo $co_contentJs; ?>
	<?php echo $co_mb_contentJs; ?>
    return true;
}
</script>
<?=$headTitleEnd?>