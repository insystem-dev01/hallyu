<form name="fpointform" method="post" action="<?=base_url()?>AdminProcess/member_mileage_req_update">
    <input type="hidden" name="mb_id" value="<?php echo $mb_id; ?>">
    <input type="hidden" name="token" value="">

    <h2 class="newp_tit"><?php echo $title; ?></h2>
    <div class="newp_wrap">
        <div class="tbl_frm01">
            <table>
            <colgroup>
                <col class="w100">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th scope="row">아이디</th>
                <td><?php echo $mb['id']; ?></td>
            </tr>
            <tr>
                <th scope="row">회원명</th>
                <td><?php echo $mb['name']; ?></td>
            </tr>
            <tr>
                <th scope="row">예치금잔액</th>
                <td><?php echo number_format($mb['mileage']); ?> 원</td>
            </tr>
            <tr>
                <th scope="row"><label for="po_content">예치금내용</label></th>
                <td><input type="text" name="po_content" id="po_content" required class="required frm_input wfull"></td>
            </tr>		
            <tr>
                <th scope="row"><label for="po_point">예치금</label></th>
                <td><input type="text" name="po_point" id="po_point" required class="required frm_input" size="10"> 원 (음수 입력시 예치금차감)</td>	
            </tr>
            </tbody>
            </table>
        </div>
        <div class="btn_confirm">
            <input type="submit" value="예치금적용" class="btn_medium" accesskey="s">
            <button type="button" onclick="self.close();" class="btn_medium bx-white">닫기</button>
        </div>
    </div>
</form>