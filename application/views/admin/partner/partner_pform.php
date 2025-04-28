<?=$headTitle?>
<form name="pform" id="pform" method="post" action="<?=base_url()?>AdminProcess/pt_pformupdate">
    <input type="hidden" name="token" value="">
    <section id="anc_pbasic">
        <h2>기본설정</h2>
        <?php echo $pg_anchor; ?>
        <div class="tbl_frm01">
            <table>
                <colgroup>
                    <col class="w180">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row" rowspan="2">상품 판매수수료</th>
                        <td>
                            <input type="checkbox" name="pf_sale_use" value="1" id="pf_sale_use"
                            <?php echo $saleUse; ?>>
                            <label for="pf_sale_use">사용함</label>
                            <?php echo $help?>
                        </td>
                    </tr>
                    <tr>
                        <td class="bo_label">
                            <label><input type="radio" name="pf_sale_flag" value="0"<?php echo $boLabelArr[0]; ?>> <strong class="fc_red">결제액 - 배송비 - 쿠폰 - 포인트결제액 = 순수결제액</strong> 에서 판매수수료를 배분</label><br>
                            <label><input type="radio" name="pf_sale_flag" value="1"<?php echo $boLabelArr[1]; ?>> <strong class="fc_red">판매가 - 공급가 - 쿠폰 - 포인트결제액 = 마진</strong> 에서 판매수수료를 배분<span>(마진이 없으면 적립되지 않음)</span></label><br>
                            <label><input type="radio" name="pf_sale_flag" value="2"<?php echo $boLabelArr[2]; ?>> <strong class="fc_red">판매가 - 공급가 = 마진</strong> 에서 판매수수료를 배분<span>(쿠폰 및 포인트 사용액은 무시하고 무조건 적립)</span></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">분양 추천수수료</th>
                        <td>
                            <input type="checkbox" name="pf_anew_use" value="1" id="pf_anew_use"
                            <?php echo $anerUse; ?>>
                            <label for="pf_anew_use">사용함</label>
                            <?php echo $help2;?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">상점 접속수수료</th>
                        <td>
                            <input type="checkbox" name="pf_visit_use" value="1" id="pf_visit_use"
                            <?php echo $visitUse; ?>>
                            <label for="pf_visit_use">사용함</label>
                            <?php echo $help3; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="th_bg"><label for="pf_payment_type">수수료 정산방법</label></th>
                        <td>
                            <select name="pf_payment_type" id="pf_payment_type">
                                <?php echo $paymentType[0]; ?>
                                <?php echo $paymentType[1]; ?>
                            </select>
                            <?php echo $help4; ?>
                            <script>
                            $(function() {
                                $("#pf_payment_type").on("change", function() {
                                    $(".use_payment_fld").hide();
                                    var type = $(this).val();
                                    if(type == '1') {
                                        $(".use_payment_fld").show();
                                    }
                                })
                                .change();
                            });
                            </script>
                        </td>
                    </tr>
                    <tr class="use_payment_fld">
                        <th scope="row" class="th_bg"><label for="pf_payment">수수료 출금조건</label></th>
                        <td>
                            <input type="text" name="pf_payment" value="<?php echo $config['pf_payment']; ?>" id="pf_payment" class="frm_input" size="8"> 원부터
                            <label for="pf_payment_unit" class="sound_only">수수료 출금단위</label>
                            <select name="pf_payment_unit" id="pf_payment_unit">
                                <?php echo $unitArr[0]; ?>
                                <?php echo $unitArr[1]; ?>
                                <?php echo $unitArr[2]; ?>
                                <?php echo $unitArr[3]; ?>
                                <?php echo $unitArr[4]; ?>
                            </select>
                            원 단위로 출금신청 가능
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="th_bg"><label for="pf_payment_tax">수수료 세액공제</label></th>
                        <td>
                            <input type="text" name="pf_payment_tax" value="<?php echo $config['pf_payment_tax']; ?>" id="pf_payment_tax" class="frm_input" size="8"> % 를 공제 후 수수료 정산
                            <?php echo $help5; ?>
                        </td>
                    </tr>
                </tbody>
		    </table>
	    </div>
    </section>
    <?php echo $frm_submit; ?>
    <section id="anc_panew">
        <h2>가입설정</h2>
        <?php echo $pg_anchor; ?>
        <div class="tbl_frm01">
            <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th scope="row">가맹점 모집</th>
                <td>
                    <input type="checkbox" name="partner_reg_yes" value="1" id="partner_reg_yes"
                    <?php echo $parterYes; ?>>
                    <label for="partner_reg_yes">사용함</label>
                    <?php echo $help6; ?>
                </td>
            </tr>
            <tr>
                <th scope="row">판매수수료 노출여부</th>
                <td>
                    <input type="checkbox" name="pf_payment_yes" value="1" id="pf_payment_yes"
                    <?php echo $parterPayYes; ?>>
                    <label for="pf_payment_yes">사용함</label>
                    <?php echo $help7; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pf_stipulation">가맹점 이용약관</label></th>
                <td>
                    <label for="pf_stipulation_subj" class="sound_only">가맹점 이용약관 제목</label>
                    <input type="text" name="pf_stipulation_subj" value="<?php echo $config['pf_stipulation_subj']; ?>" id="pf_stipulation_subj" required class="required frm_input" size="70" placeholder="예) 소프트웨어(쇼핑몰 솔루션) 사용권 계약서/이용약관(EULA)"> 제목
                    <div class="mart5"><textarea id="pf_stipulation" name="pf_stipulation" class="frm_textbox" placeholder="해당 쇼핑몰에 맞는 가맹점 이용약관을 입력합니다."><?php echo $config['pf_stipulation']; ?></textarea></div>
                    <?php echo $help8; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pf_regulation">가맹점 규정안내</label></th>
                <td>
                    <label for="pf_regulation_subj" class="sound_only">가맹점 규정안내 제목</label>
                    <input type="text" name="pf_regulation_subj" value="<?php echo $config['pf_regulation_subj']; ?>" id="pf_regulation_subj" class="frm_input" size="70" placeholder="예) 불법홍보 금지 및 규정안내"> 제목
                    <div class="mart5"><textarea id="pf_regulation" name="pf_regulation" class="frm_textbox" placeholder="해당 쇼핑몰에 맞는 가맹점 규정안내를 입력합니다."><?php echo $config['pf_regulation']; ?></textarea></div>
                    <?php echo $help9; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pf_basedomain">도메인 종류</label></th>
                <td>
                    <input type="text" name="pf_basedomain" value="<?php echo $config['pf_basedomain']; ?>" id="pf_basedomain" class="frm_input" size="70">
                    <?php echo $help10; ?>
                </td>
            </tr>
            </tbody>
            </table>
        </div>
    </section>
    <?php echo $frm_submit; ?>
    <section id="anc_pauth">
        <h2>권한설정</h2>
        <?php echo $pg_anchor; ?>
        <div class="tbl_frm01">
            <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th scope="row"><label for="pf_auth_good">상품판매 권한</label></th>
                <td>
                    <select name="pf_auth_good" id="pf_auth_good">
                        <?php echo $pf_auth_good[0]; ?>
                        <?php echo $pf_auth_good[1]; ?>
                        <?php echo $pf_auth_good[2]; ?>
                    </select>
                    <?php echo $help11; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pf_auth_pg">PG결제연동 권한</label></th>
                <td>
                    <select name="pf_auth_pg" id="pf_auth_pg">
                        <?php echo $pf_auth_pg[0]; ?>
                        <?php echo $pf_auth_pg[1]; ?>
                        <?php echo $pf_auth_pg[2]; ?>
                    </select>
                    <?php echo $help12; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pf_auth_sms">SMS 문자설정 권한</label></th>
                <td>
                    <select name="pf_auth_sms" id="pf_auth_sms">
                        <?php echo $pf_auth_sms[0]; ?>
                        <?php echo $pf_auth_sms[1]; ?>
                    </select>
                    <?php echo $help13; ?>
                </td>
            </tr>
            </tbody>
            </table>
        </div>
    </section>
    <?php echo $frm_submit; ?>
    <section id="anc_pterm">
        <h2>관리비설정</h2>
        <?php echo $pg_anchor; ?>
        <div class="tbl_frm01">
            <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th scope="row">관리비 사용여부</th>
                <td>
                    <input type="checkbox" name="pf_expire_use" value="1" id="pf_expire_use"<?php echo $pf_expire_use; ?>>
                    <label for="pf_expire_use">사용함</label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pf_expire_term">관리비 연장기간</label></th>
                <td>
                    <input type="text" name="pf_expire_term" value="<?php echo $config['pf_expire_term']; ?>" id="pf_expire_term" class="frm_input" size="8"> 개월 단위
                </td>
            </tr>
            <tr>
                <th scope="row">관리비 미납시</th>
                <td>
                    <input type="checkbox" name="pf_login_no" value="1" id="pf_login_no"<?php echo $pf_login_no; ?>>
                    <label for="pf_login_no">회원로그인 차단</label>
                    <input type="checkbox" name="pf_account_no" value="1" id="pf_account_no"<?php echo $pf_account_no; ?>>
                    <label for="pf_account_no">수수료 출금신청 차단</label>
                    <input type="checkbox" name="pf_session_no" value="1" id="pf_session_no"<?php echo $pf_session_no; ?>>
                    <label for="pf_session_no">운영권한을 본사로 귀속</label>
                </td>
            </tr>
            </tbody>
            </table>
        </div>
    </section>
    <?php echo $frm_submit; ?>
</form>
<?=$headTitleEnd?>