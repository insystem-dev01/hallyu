<?=$headTitle?>
<form name="fmailform" id="fmailform" action="<?=base_url()?>AdminProcess/member_mail_form_update" onsubmit="return fmailform_check(this);" method="post">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page"  value="<?php echo $page; ?>">
    <input type="hidden" name="ma_id" value="<?php echo $ma_id; ?>">
    <h2>메일 정보</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">치환 코드</th>
                    <td>
                        <ul class="sit_displace">
                            <li class="mlst">{이름}</li>
                            <li class="mlst">{레벨명}</li>
                            <li class="mlst">{아이디}</li>
                            <li class="mlst">{이메일}</li>
                        </ul>
                        <?=$helpArr[0]?>
                    </td>
	            </tr>
                <tr>
                    <th scope="row"><label for="ma_subject">메일 제목</label></th>
                    <td>
                        <input type="text" name="ma_subject" value="<?php echo $ma['ma_subject']; ?>" id="ma_subject" required class="required frm_input" size="60" maxlength="255">
                        <?=$helpArr[1]?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="ma_content">메일 내용</label></th>
                    <td>
                        <?php echo $editor; ?>
                    </td>
                </tr>
            </tbody>
	    </table>
    </div>
    <?=$frm_submit?>
</form>

<script>
function fmailform_check(f){
    errmsg = "";
    errfld = "";

    check_field(f.ma_subject, "제목을 입력하세요.");

    if(errmsg != "") {
        alert(errmsg);
        errfld.focus();
        return false;
    }
    <?=$getEditorJs?>
    <?=$chkEditorJs?>
    return true;
}
</script>
<?=$headTitleEnd?>