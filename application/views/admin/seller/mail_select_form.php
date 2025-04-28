<?=$headTitle?>
<form name="fsendmailselectform" action="<?php echo base_url(); ?>admin/seller?code=mail_select_list" onsubmit="return fmailform_check(this);" method="post" autocomplete="off">
    <input type="hidden" name="token" value="">
    <h2>메일 정보</h2>
    <div class="tbl_frm01">
        <table>
        <colgroup>
            <col class="w140">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">입점사 통계</th>
            <td>전체입점사 : <?php echo number_format($tot_cnt); ?>명 , (미승인입점사수 : <?php echo number_format($finish_cnt); ?>명 , <strong>정상입점사 : <?php echo number_format($tot_cnt - $finish_cnt); ?>명</strong> 에게 메일 발송)</td>
        </tr>
        <tr>
            <th scope="row">치환 코드</th>
            <td>
                <ul class="sit_displace">
                    <li class="mlst">{아이디}</li>
                    <li class="mlst">{회사명}</li>
                    <li class="mlst">{대표자명}</li>
                    <li class="mlst">{사업자등록번호}</li>
                    <li class="mlst">{전화번호}</li>
                    <li class="mlst">{팩스번호}</li>
                    <li class="mlst">{담당자명}</li>
                </ul>
                <?php echo $help1; ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="ma_subject">메일 제목</label></th>
            <td>
                <input type="text" name="ma_subject" value="" id="ma_subject" required class="required frm_input" size="60" maxlength="255">
                <?php echo $help2; ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="ma_content">메일 내용</label></th>
            <td>
                <?php echo $editor_html; ?>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="다음" class="btn_large" accesskey="s">
    </div>
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
    <?php echo $get_editor_js; ?>
    <?php echo $chk_editor_js; ?>
    return true;
}
</script>

<?=$headTitleEnd?>
