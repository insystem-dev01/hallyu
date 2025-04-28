<?=$headTitle?>

<form name="fregform" method="post" onsubmit="return fregform_submit(this);">
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
                <th scope="row">입점서비스 사용</th>
                <td class="td_label">
                    <label><input type="radio" name="seller_reg_yes" value="1"<?php echo $seller_reg_yesArr[1]; ?>> 사용함</label>
                    <label><input type="radio" name="seller_reg_yes" value="0"<?php echo $seller_reg_yesArr[0]; ?>> 사용안함</label>
                </td>
            </tr>
            <tr>
                <th scope="row">신규상품등록 진열</th>
                <td class="td_label">			
                    <label><input type="radio" name="seller_reg_auto" value="1"<?php echo $seller_reg_autoArr[1];?>> 관리자 승인</label>
                    <label><input type="radio" name="seller_reg_auto" value="0"<?php echo $seller_reg_autoArr[0]; ?>> 등록시 바로 승인</label>
                </td>
            </tr>
            <tr>
                <th scope="row">기존상품수정 진열</th>
                <td class="td_label">			
                    <label><input type="radio" name="seller_mod_auto" value="1"<?php echo $seller_mod_autoArr[1]; ?>> 관리자 승인</label>
                    <label><input type="radio" name="seller_mod_auto" value="0"<?php echo $seller_mod_autoArr[0]; ?>> 수정시 바로 승인</label>
                </td>
            </tr>	
            </tbody>
        </table>
    </div>

    <h2>가입설정</h2>
    <div class="tbl_frm01">
        <table class="tablef">
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">입점 가입약관</th>
                    <td><textarea name="seller_reg_agree" class="frm_textbox wfull" rows="7"><?php echo $seller_reg_agreeStr; ?></textarea></td>
                </tr>	
                <tr>
                    <th scope="row">PC 입점 이용안내</th>
                    <td>
                        <?php echo $seller_reg_guideTag; ?>	
                    </td>
                </tr>
                <tr>
                    <th scope="row">모바일 입점 이용안내</th>
                    <td>
                        <?php echo $seller_reg_mb_guideTag; ?>	
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>
</form>

<script>
function fregform_submit(f) {
    var base_url=document.getElementById('base_url').value;
	<?php echo $get_editor_js; ?>
	<?php echo $get_editor_mb_js; ?>

	f.action = base_url+"AdminProcess/supply_update";
    return true;
}
</script>
<?=$headTitleEnd?>