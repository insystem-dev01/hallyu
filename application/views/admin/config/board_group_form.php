<?=$headTitle?>
<form name="fboardgroup" method="post" action="<?=base_url()?>AdminProcess/board_group_form_update" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">

    <div class="tbl_frm02">
        <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">그룹 ID</th>
                    <td><input type="text" name="gr_id" value="<?php echo $group['gr_id']; ?>" required alphanumericunderline itemname="그룹 아이디" class="frm_input required"<?php echo $readonly; ?>> 영문자, 숫자, _ 만 가능 (공백없이)</td>
                </tr>
                <tr>
                    <th scope="row">그룹 제목</th>
                    <td><input type="text" name="gr_subject" value="<?php echo $gr_subjectTag; ?>" required itemname="그룹 제목" class="frm_input required" size="50"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
        <a href="<?=base_url()?>admin/config?code=board_group_list&page=<?php echo $page; ?>" class="btn_large bx-white">목록</a>
    </div>
</form>
<?=$headTitleEnd?>