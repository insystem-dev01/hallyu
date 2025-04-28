<?=$headTitle?>

<script>
function byte_check(el_message, el_byte, el_max_byte){
    var conts = document.getElementById(el_message);
    var bytes = document.getElementById(el_byte);
    var max_bytes = document.getElementById(el_max_byte);
    var i = 0;
    var cnt = 0;
    var exceed = 0;
    var ch = '';
    for(i=0; i<conts.value.length; i++){
        ch = conts.value.charAt(i);
        if(escape(ch).length > 4) {
            cnt += 2;
        } else {
            cnt += 1;
        }
    }
    bytes.innerHTML = cnt;
    <?=$cf_sms_typeJs?>
}
</script>
<form name="fconfig" id="fconfig" action="<?=base_url()?>AdminProcess/sms_update" method="post">
    <h2>기본설정</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">SMS 사용</th>
                    <td>
                        <select name="cf_sms_use" id="cf_sms_use">
                            <?php echo $cf_sms_useArr[0]; ?>
                            <?php echo $cf_sms_useArr[1]; ?>
                        </select>
                        <a href="http://moonjaro.co.kr" target="_blank" class="btn_small grey">문자로 바로가기</a>
                    </td>
                </tr>
                <tr>
                    <th scope="row">SMS 전송유형</th>
                    <td>
                        <input type="hidden" name="cf_sms_type" value="LMS">
                        <!-- <select name="cf_sms_type" id="cf_sms_type">
                            <?php echo $cf_sms_typeArrp[0]; ?>
                            <?php echo $cf_sms_typeArrp[1]; ?>
                        </select> -->
                        <div class="">90바이트 이하는 SMS로, 그 이상은 2000바이트까지 LMS로 전송됩니다.</div>
                        <?php echo $priceSms; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">문자로 인증키</th>
                    <td>
                        <input type="text" name="cf_moonjaro_key" value="<?php echo $sms['cf_moonjaro_key']; ?>" id="cf_moonjaro_key" required itemname="문자로 인증키" class="frm_input required" style="width:500px;">
                        <?php echo $moonjaroApi; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">발신 전화번호</th>
                    <td>
                        <input type="text" name="cf_sms_recall" value="<?php echo $cf_sms_recall; ?>" id="cf_sms_recall" required itemname="발신 전화번호" class="frm_input required">
                        <a href="http://moonjaro.co.kr/calling/addCallingNumber" target="_blank" class="btn_small grey">발신번호 등록</a>
                        <?=$msgHelp?>
                        <?=$saveBtnMsg?>
                        <?=$registerMsg?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">요금제</th>
                    <td>
                        <?=$paymentStr?>
                    </td>
                </tr>
                <?=$paymentAtype?>
            </tbody>
        </table>
    </div>
    <div id="scf_sms_pre">
        <h2>사전에 정의된 SMS프리셋</h2>
        <div class="local_desc01 local_desc">
            <dl>
                <dt>회원가입시</dt>
                <dd>{이름} {아이디}</dd>
                <dt>주문완료시</dt>
                <dd>{이름} {주문번호}</dd>
                <dt>입금확인시</dt>
                <dd>{이름} {주문번호}</dd>
                <dt>상품발주시</dt>
                <dd>{이름} {주문번호} {업체} {송장번호}</dd>
                <dt>주문취소시</dt>
                <dd>{이름} {주문번호}</dd>
                <dt>배송완료시</dt>
                <dd>{이름} {주문번호} {업체} {송장번호}</dd>
            </dl>
            <p><?php echo $smsLimitHelp; ?></p>
        </div>
    </div>
    <h2>메세지설정</h2>
    <table class="wfull">
        <colgroup>
            <col width="175px">
            <col>
            <col width="175px">
            <col>
        </colgroup>
        <tbody>
            <?=$msgConfigStr?>
        </tbody>
    </table>
    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>
</form>

<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="desc02">
			<p class="fc_red">ㆍ 주의! 영문 한글자당 : 1 byte , 한글 한글자당 : 2 byte , 특수문자의 경우 1 또는 2 byte</p>
			<p>ㆍ 충전잔액이 있어야 발송이 가능합니다.</p>
			<p>ㆍ SMS 자동 발송은 문자 수신여부와 상관없이 발송됩니다.</p>
			<p>ㆍ 네트워크 상태에 따라 약 1~2분정도 늦게 발송될 수 있습니다.</p>
		</div>
	 </div>
</div>
<?=$headTitleEnd?>