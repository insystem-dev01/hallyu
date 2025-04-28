<?=$headTitle?>
<form name="fpointselectlist" id="fpointselectlist" method="post" action="<?php echo base_url(); ?>AdminProcess/member_point_select_update">
    <input type="hidden" name="po_point" value="<?php echo $po_point; ?>">
    <input type="hidden" name="po_content" value="<?php echo $po_content; ?>">
    <input type="hidden" name="token" value="">

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
                <col>
                <col width="100px">
                <col width="100px">
                <col width="100px">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col">아이디</th>
                    <th scope="col">회원명</th>
                    <th scope="col">레벨</th>
                    <th scope="col">포인트잔액</th>
                    <th scope="col">적용포인트</th>
                    <th scope="col">비고</th>
                </tr>
            </thead>
            <tbody>
                <?=$listTable?>
            </tbody>
        </table>
    </div>
    <div class="mart10 fc_red">※ [주의] 포인트 차감금액이 현재 잔액보다 클경우 차감되지 않습니다.</div>

    <div class="btn_confirm">
        <input type="submit" value="포인트적용" class="btn_large" accesskey="s">
        <a href="<?php echo base_url(); ?>admin/member?code=point_select_form" class="btn_large bx-white">취소</a>
    </div>
</form>
<?=$headTitleEnd?>