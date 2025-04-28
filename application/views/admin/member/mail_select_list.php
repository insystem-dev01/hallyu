<?=$headTitle?>
<form name="fmailselectlist" id="fmailselectlist" method="post" action="<?php echo base_url(); ?>admin/member?code=mail_select_update">
    <input type="hidden" name="ma_id" value="<?php echo $ma_id; ?>">

    <div class="local_desc02 local_desc">
        <p>
            선택된 회원수 : <strong><?php echo number_format($cnt); ?></strong>명
        </p>
    </div>
    <div class="tbl_head01" id="sit_select_tbl">
        <table>
            <colgroup>
                <col width="50px">
                <col width="150px">
                <col width="150px">
                <col width="150px">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col">아이디</th>
                    <th scope="col">회원명</th>
                    <th scope="col">레벨</th>
                    <th scope="col">E-mail</th>
                </tr>
            </thead>
            <tbody>
                <?=$listTable?>
            </tbody>
	    </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="메일전송" class="btn_large" accesskey="s">
        <a href="javascript:history.go(-1);" class="btn_large bx-white">취소</a>
    </div>
</form>

<?=$headTitleEnd?>
