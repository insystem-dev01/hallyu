<?=$headTitle?>
<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/register_update">
    <input type="hidden" name="token" value="">
    <h2>기본설정</h2>
    <div class="tbl_frm01">
        <table>
        <colgroup>
            <col class="w180">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">회원가입시 포인트</th>
            <td>
                <input type="text" name="register_point" value="<?php echo number_format($config['register_point']); ?>" class="frm_input w80" onkeyup="addComma(this);"> 원
            </td>
        </tr>
        <tr>
            <th scope="row">추천인 포인트</th>
            <td>
                <input type="text" name="partner_point" value="<?php echo number_format($config['partner_point']); ?>" class="frm_input w80" onkeyup="addComma(this);"> 원
            </td>
        </tr>
        <tr>
            <th scope="row">핸드폰 입력</th>
            <td class="td_label">
                <input id="register_use_hp" type="checkbox" name="register_use_hp" value="1"<?php echo $register_use_hp; ?>> <label for="register_use_hp">보이기</label>
                <input id="register_req_hp" type="checkbox" name="register_req_hp" value="1"<?php echo $register_req_hp; ?>> <label for="register_req_hp">필수입력</label>
            </td>
        </tr>
        <tr>
            <th scope="row">전화번호 입력</th>
            <td class="td_label">
                <input id="register_use_tel" type="checkbox" name="register_use_tel" value="1"<?php echo $register_use_tel; ?>> <label for="register_use_tel">보이기</label>
                <input id="register_req_tel" type="checkbox" name="register_req_tel" value="1"<?php echo $register_req_tel; ?>> <label for="register_req_tel">필수입력</label>
            </td>
        </tr>
        <tr>
            <th scope="row">주소 입력</th>
            <td class="td_label">
                <input id="register_use_addr" type="checkbox" name="register_use_addr" value="1"<?php echo $register_use_addr; ?>> <label for="register_use_addr">보이기</label>
                <input id="register_req_addr" type="checkbox" name="register_req_addr" value="1"<?php echo $register_req_addr; ?>> <label for="register_req_addr">필수입력</label>
            </td>
        </tr>
        <tr>
            <th scope="row">가입불가 ID</th>
            <td>
                <textarea name="prohibit_id" class="frm_textbox wfull" rows="3"><?php echo $config['prohibit_id']; ?></textarea>
                <?php echo $help['word']; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">가입불가 E-mail</th>
            <td>
                <textarea name="prohibit_email" class="frm_textbox wfull" rows="3"><?php echo $config['prohibit_email']; ?></textarea>
                <?php echo $help['mail']; ?>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <h2>약관 설정</h2>
    <div class="tbl_frm01">
        <table>
        <colgroup>
            <col class="w180">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">이용약관</th>
            <td><textarea name="shop_provision" class="frm_textbox wfull" rows="7"><?php echo $shop_provision; ?></textarea></td>
        </tr>
        <tr>
            <th scope="row">개인정보 처리방침</th>
            <td><textarea name="shop_private" class="frm_textbox wfull" rows="7"><?php echo $shop_private; ?></textarea></td>
        </tr>
        <tr>
            <th scope="row">마케팅 수신</th>
            <td><textarea name="shop_marketing" class="frm_textbox wfull" rows="7"><?php echo $shop_marketing; ?></textarea></td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>
</form>

<?=$headTitleEnd?>