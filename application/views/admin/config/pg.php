<?=$headTitle?>
<form name="fregform" id="fregform" action="<?=base_url()?>AdminProcess/pg_update" method="post">
    <input type="hidden" name="token" value="">
    <h2>전자결제 (PG) 설정</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">결제수단</th>
                    <td class="td_label">
                        <input type="checkbox" name="de_bank_use" value="1" id="de_bank_use"<?php echo $de_bank_useStr; ?>>
                        <label for="de_bank_use">무통장</label>
                        <input type="checkbox" name="de_card_use" value="1" id="de_card_use"<?php echo $de_card_useStr; ?>>
                        <label for="de_card_use">신용카드</label>
                        <input type="checkbox" name="de_iche_use" value="1" id="de_iche_use"<?php echo $de_iche_useStr; ?>>
                        <label for="de_iche_use">실시간계좌이체</label>
                        <?php if(false){#가상계좌 취소?>
                            <input type="checkbox" name="de_kakao_use" value="1" id="de_kakao_use"<?php echo $de_kakao_useStr; ?>>
                            <label for="de_kakao_use">카카오결제</label>
                            <input type="checkbox" name="de_vbank_use" value="1" id="de_vbank_use"<?php echo $de_vbank_useStr; ?>>
                            <label for="de_vbank_use">가상계좌</label>
                            <input type="checkbox" name="de_hp_use" value="1" id="de_hp_use"<?php echo $de_hp_useStr; ?>>
                            <label for="de_hp_use">휴대폰결제</label>
                        <?php } ?>
                    </td>
                </tr>
                <?php if(false){?>
                <tr>
                    <th scope="row">결제 테스트</th>
                    <td class="td_label">
                        <input type="radio" name="de_card_test" value="0" id="de_card_test_1" 
                        <?php echo $de_card_useStr0; ?>>
                        <label for="de_card_test_1">실결제</label>
                        <input type="radio" name="de_card_test" value="1" id="de_card_test_2" 
                        <?php echo $de_card_useStr1; ?>>
                        <label for="de_card_test_2">테스트결제</label>
                        <?php echo $help['pg']; ?>
                    </td>
                </tr>
                <?php } ?>

                <tr>
                    <th scope="row"><label for="de_pg_service">결제대행사</label></th>
                    <td> 
                        <select name="de_pg_service" id="de_pg_service">
                            <?php echo $optArr['inicis'];?>
                        </select> 
                        <?php echo $help['service']; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">복합과세 결제</th>
                    <td>
                        <input type="checkbox" name="de_tax_flag_use" value="1" id="de_tax_flag_use" 
                        <?php echo $de_tax_flag_use; ?>>
                        <label for="de_tax_flag_use">사용함</label>
                        <?php echo $help['tax']; ?>
                    </td>
                </tr>
                <?php if(false){?>
                <tr>
                    <th scope="row">현금영수증 발급사용</th>
                    <td>
                        <input type="checkbox" name="de_taxsave_use" value="1" id="de_taxsave_use" 
                        <?php echo $de_taxsave_use; ?>>
                        <label for="de_taxsave_use">사용함</label>
                        <?php echo $help['taxsave']; ?>
                    </td>
                </tr>
                <?php } ?>
                <?php if(false){ ?>
                <tr>
                    <th scope="row">신용카드 무이자할부사용</th>
                    <td>
                        <input type="checkbox" name="de_card_noint_use" value="1" id="de_card_noint_use" 
                        <?php echo $de_card_noint_use;?>>
                        <label for="de_card_noint_use">사용함</label>
                        <?php echo $help['credit']; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">PG사 간편결제 버튼사용</th>
                    <td>
                        <input type="checkbox" name="de_easy_pay_use" value="1" id="de_easy_pay_use" 
                        <?php echo $de_easy_pay_use; ?>>
                        <label for="de_easy_pay_use">사용함</label>
                        <?php echo $help['easypay']; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if(false){?>
    <div class="pg_info_fld kcp_info_fld">
        <h2>NHN KCP 계약정보</h2>
        <div class="tbl_frm01">
            <table>
                <colgroup>
                    <col class="w180">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row"><label for="de_kcp_mid">NHN KCP SITE CODE</label></th>
                        <td>
                            <input type="text" name="de_kcp_mid" value="<?php echo $default['de_kcp_mid']; ?>" id="de_kcp_mid" class="frm_input" size="10">					
                            <a href="http://www.kcp.co.kr/main.do" target="_blank" class="btn_small blue">NHN KCP서비스신청하기</a>
                            <?php echo $help['nhn']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="de_kcp_site_key">NHN KCP SITE KEY</label></th>
                        <td>
                            <input type="text" name="de_kcp_site_key" value="<?php echo $default['de_kcp_site_key']; ?>" id="de_kcp_site_key" class="frm_input" size="40">
                            <?php echo $help['siteKey']; ?>
                        </td>
                    </tr>			
                    <tr>
                        <th scope="row">가상계좌 입금통보 URL</th>
                        <td>
                            <?php echo base_url(); ?>shop/settle_kcp_common
                            <?php echo $help['nhnAdmin']; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="pg_info_fld lg_info_fld">
        <h2>LG유플러스 계약정보</h2>
        <div class="tbl_frm01">
            <table>
                <colgroup>
                    <col class="w180">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row"><label for="de_lg_mid">LG유플러스 상점아이디</label></th>
                        <td>
                            <input type="text" name="de_lg_mid" value="<?php echo $default['de_lg_mid']; ?>" id="de_lg_mid" class="frm_input" size="10" maxlength="20">
                            <a href="http://ecredit.uplus.co.kr/" target="_blank" class="btn_small blue">LG유플러스 서비스신청하기</a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="de_lg_mert_key">LG유플러스 MertKey</label></th>
                        <td>
                            <input type="text" name="de_lg_mert_key" value="<?php echo $default['de_lg_mert_key']; ?>" id="de_lg_mert_key" class="frm_input" size="40" maxlength="50">
                            <em>예) 95160cce09854ef44d2edb2bfb05f9f3</em>
                            <?php echo $help['mertKey']; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>

    <?php if($member['grade'] == "99") { ?>
    <div class="pg_info_fld inicis_info_fld">
        <h2>KG이니시스 계약정보</h2>
        <div class="tbl_frm01">
            <table>
                <colgroup>
                    <col class="w180">
                    <col>
                </colgroup>
                <tbody>
                    <?php if(false){?>
                    <tr>
                        <th scope="row"><label for="de_inicis_mid">KG이니시스 상점아이디</label></th>
                        <td>
                            <input type="text" name="de_inicis_mid" value="<?php echo $default['de_inicis_mid']; ?>" id="de_inicis_mid" class="frm_input" size="10">
                            <a href="https://www.inicis.com/" target="_blank" class="btn_small blue">KG이니시스 서비스신청하기</a>
                            <?php echo $help['kgMid']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="de_inicis_admin_key">KG이니시스 키패스워드</label></th>
                        <td>
                            <input type="text" name="de_inicis_admin_key" value="<?php echo $default['de_inicis_admin_key']; ?>" id="de_inicis_admin_key" class="frm_input" size="5" maxlength="4">
                            <?php echo $help['inicisKey']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="de_inicis_sign_key">KG이니시스 웹결제 사인키</label></th>
                        <td>					
                            <input type="text" name="de_inicis_sign_key" value="<?php echo $default['de_inicis_sign_key']; ?>" id="de_inicis_sign_key" class="frm_input" size="40" maxlength="50">
                            <?php echo $help['signKey']; ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th scope="row">KG이니시스 삼성페이 버튼</th>
                        <td>
                            <input type="checkbox" name="de_samsung_pay_use" value="1" id="de_samsung_pay_use"<?php echo $de_samsung_pay_use; ?>>
                            <label for="de_samsung_pay_use">사용함</label>
                            <?php echo $help['samsungBtn']; ?>
                        </td>
                    </tr>
                    <?php if(false){?>
                    <tr>
                        <th scope="row">가상계좌 입금통보 URL</th>
                        <td>
                            <?php echo base_url(); ?>process/settle_inicis_common
                            <?php echo $help['kgInicisAdmin']; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <h2>에스크로 설정</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">에스크로 사용</th>
                    <td> 
                        <input type="radio" name="de_escrow_use" value="0" id="de_escrow_use_no"<?php echo $de_escrow_use0; ?>>
                        <label for="de_escrow_use_no">일반결제 사용</label>
                        <input type="radio" name="de_escrow_use" value="1" id="de_escrow_use_yes"<?php echo $de_escrow_use1; ?>>
                        <label for="de_escrow_use_yes"> 에스크로결제 사용</label>
                        <?php echo $help['escrow']; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php } ?>

    <h2>무통장입금계좌</h2>
    <div class="local_cmd01">
        <p>※ 무통장입금계좌는 최대 5개까지 등록 가능합니다.</p>
    </div>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <?=$bankStr?>
            </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>
</form>

<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="desc02">
			<p>ㆍPG사와 계약을 맺은 이후에는 메일로 받으신 실제 PG 계약정보를 입력하시면 됩니다.</p>
			<p>ㆍPG사의 결제정보 설정후 고객님께서 카드결제 테스트를 꼭 해보시기 바랍니다.</p>
			<p>ㆍ간혹 PG사를 통해 카드승인된 값을 받지못하여 주문관리페이지에서 입금확인으로 자동변경되지 않을수 있습니다.</p>
			<p>ㆍ반드시 주문관리페이지의 주문상태와 PG사에서 제공하는 관리자화면내의 카드승인내역도 동시에 확인해 주십시요.</p>
		</div>
	 </div>
</div>

<script>
$(function() { 
    $(".pg_info_fld").hide();
    <?=$de_pg_serviceJs ?>
	$("#de_pg_service").on("change", function() {
		$(".pg_info_fld:visible").hide();
        var pg = $(this).val();
		$("."+pg+"_info_fld").show();
    });
});
</script>
<?=$de_useJs?>
<?=$headTitleEnd?>