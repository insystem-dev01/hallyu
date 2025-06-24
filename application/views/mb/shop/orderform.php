<?=$OM_POSTCODE_JS?>
<div id="sod_frm">
	<form name="buyform" id="buyform" method="post" action="<?php echo $order_action_url; ?>" onsubmit="return fbuyform_submit(this);" autocomplete="off">
        <input type="hidden" name="ss_cart_id" value="<?php echo $ss_cart_id; ?>">
        <input type="hidden" name="mb_point" value="<?php echo $member['point']; ?>">
        <input type="hidden" name="mb_mileage" value="<?php echo $member['mileage']; ?>">
        <input type="hidden" name="pt_id" value="<?php echo $mb_recommend; ?>">
        <input type="hidden" name="shop_id" value="<?php echo $pt_id; ?>">
        <input type="hidden" name="coupon_total" value="0">
        <input type="hidden" name="coupon_price" value="">
        <input type="hidden" name="coupon_lo_id" value="">
        <input type="hidden" name="coupon_cp_id" value="">	
        <input type="hidden" name="baesong_price" value="<?php echo $baesong_price; ?>">
        <input type="hidden" name="baesong_price2" value="0">
        <input type="hidden" name="org_price" value="<?php echo $tot_price; ?>">
        <?=$usePointTag?>
        <?=$content?>
        <section id="sod_frm_orderer">
            <h2 class="anc_tit">주문하시는 분</h2>
            <div class="odf_tbl">
                <table>
                    <tbody>
                        <?=$isNotMember?>
                        <tr>
                            <th scope="row">이름</th>
                            <td><input type="text" name="name" value="<?php echo $member['name']; ?>" required class="frm_input required" maxlength="20"></td>
                        </tr>
                        <tr>
                            <th scope="row">연락처</th>
                            <td><input type="text" name="cellphone" value="<?php echo $member['cellphone']; ?>" required class="frm_input required" maxlength="20"></td>
                        </tr>
                        <tr>
                            <th scope="row">주소</th>
                            <td>
                                <input type="text" name="zip" value="<?php echo $member['zip']; ?>" required class="frm_input required" size="5" maxlength="5">
                                <button type="button" onclick="win_zip('buyform', 'zip', 'addr1', 'addr2', 'addr3', 'addr_jibeon');" class="btn_small grey">주소검색</button><br>
                                <input type="text" name="addr1" value="<?php echo $member['addr1']; ?>" required class="frm_input frm_address required"><br>   
                                <input type="text" name="addr2" value="<?php echo $member['addr2']; ?>" class="frm_input frm_address"><br>
                                <input type="text" name="addr3" value="<?php echo $member['addr3']; ?>" class="frm_input frm_address" readonly><br>
                                <input type="hidden" name="addr_jibeon" value="<?php echo $member['addr_jibeon']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">E-mail</th>
                            <td><input type="text" name="email" value="<?php echo $member['email']; ?>" required class="frm_input required wfull"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="sod_frm_taker">
            <h2 class="anc_tit">받으시는 분</h2>
            <div class="odf_tbl">
                <table>
                    <tbody>
                        <tr>
                            <th scope="row">배송지선택</th>
                            <td>
                                <input type="radio" name="ad_sel_addr" value="1" id="sel_addr1" class="css-checkbox lrg">
                                <label for="sel_addr1" class="css-label padr5">주문자와 동일</label><br>
                                <input type="radio" name="ad_sel_addr" value="2" id="sel_addr2" class="css-checkbox lrg">
                                <label for="sel_addr2" class="css-label">신규배송지</label>
                                <?=$deliverList?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">이름</th>
                            <td><input type="text" name="b_name" required class="frm_input required"></td>
                        </tr>
                        <tr>
                            <th scope="row">연락처</th>
                            <td><input type="text" name="b_cellphone" required class="frm_input required"></td>
                        </tr>
                        <tr>
                            <th scope="row">주소</th>
                            <td>
                                <input type="text" name="b_zip" required class="frm_input required" size="5" maxlength="5">
                                <button type="button" onclick="win_zip('buyform', 'b_zip', 'b_addr1', 'b_addr2', 'b_addr3', 'b_addr_jibeon');" class="btn_small grey">주소검색</button><br>
                                <input type="text" name="b_addr1" required class="frm_input frm_address required"><br>   
                                <input type="text" name="b_addr2" class="frm_input frm_address"><br>
                                <input type="text" name="b_addr3" class="frm_input frm_address" readonly><br>
                                <input type="hidden" name="b_addr_jibeon" value="">				
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">전하실말씀</th>
                            <td>
                                <select name="sel_memo" class="wfull">
                                    <option value="">요청사항 선택하기</option>
                                    <option value="부재 시 경비실에 맡겨주세요.">부재 시 경비실에 맡겨주세요.</option>
                                    <option value="집 앞에 놔주세요.">집 앞에 놔주세요.</option>
                                    <option value="택배함에 놔주세요.">택배함에 놔주세요.</option>
                                    <option value="배송 전에 연락 바랍니다.">배송 전에 연락 바랍니다.</option>
                                </select>
                                <div class="padt5">
                                    <textarea name="memo" id="memo" class="frm_textbox"></textarea>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="sod_frm_pay">
            <h2 class="anc_tit">결제정보 입력</h2>
            <div class="odf_tbl">
                <table>
                    <tbody>
                        <tr>
                            <th scope="row">결제방법</th>
                            <td>
                                <select name="paymethod" onchange="calculate_paymethod(this.value);" class="wfull">
                                    <option value="">선택하기</option>
                                    <?php echo $multi_settle; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">합계</th>
                            <td><strong><?php echo $totPrice; ?></strong></td>
                        </tr>
                        <tr>
                            <th scope="row">추가배송비</th>
                            <td>
                                <strong><span id="send_cost2">0</span>원</strong>
                                <span class="fc_999">(지역에 따라 추가되는 배송비)</span>
                            </td>
                        </tr>
                        <?=$couponTag?>
                        <?=$pointUseTag?>
                        <?=$mileageUseTag?>
                        <tr>
                            <th scope="row">총 결제금액</th>
                            <td>
                                <input type="text" name="tot_price" value="<?php echo number_format($tot_price); ?>" class="frm_input w100" readonly style="background:#f1f1f1;color:red;font-weight:bold;"> 원
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <section id="bank_section" style="display:none;">
            <h2 class="anc_tit">입금하실 계좌</h2>
            <div class="odf_tbl">
                <table>
                    <tbody>
                        <tr>
                            <th scope="row">무통장계좌</th>
                            <td>
                                <?=$bankTag?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">입금자명</th>
                            <td><input type="text" name="deposit_name" value="<?php echo $member['name']; ?>" class="frm_input w100"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="taxsave_section" style="display:none;">
            <h2 class="anc_tit">증빙서류 발급</h2>
            <div class="odf_tbl">
                <table>
                <tbody>
                <tr>
                    <th scope="row">현금영수증</th>
                    <td>
                        <select name="taxsave_yes" onchange="tax_save(this.value);" class="wfull">
                            <option value="N">발행안함</option>
                            <option value="Y">개인 소득공제용</option>
                            <option value="S">사업자 지출증빙용</option>
                        </select>
                        <div id="taxsave_fld_1" style="display:none;">
                            <input type="text" name="tax_hp" class="frm_input frm_address" placeholder="핸드폰번호">
                        </div>
                        <div id="taxsave_fld_2" style="display:none;">
                            <input type="text" name="tax_saupja_no" class="frm_input frm_address" placeholder="사업자등록번호">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">세금계산서</th>
                    <td>
                        <select name="taxbill_yes" onchange="tax_bill(this.value);" class="wfull">
                            <option value="N">발행안함</option>
                            <option value="Y">발행요청</option>
                        </select>
                        <div id="taxbill_section" style="display:none;">
                            <input type="text" name="company_saupja_no" class="frm_input frm_address" placeholder="사업자등록번호"><br>
                            <input type="text" name="company_name" class="frm_input frm_address" placeholder="상호(법인명)"><br>
                            <input type="text" name="company_owner" class="frm_input frm_address" placeholder="대표자명"><br>
                            <input type="text" name="company_addr" class="frm_input frm_address" placeholder="사업장주소"><br>
                            <input type="text" name="company_item" class="frm_input frm_address" placeholder="업태"><br>
                            <input type="text" name="company_service" class="frm_input frm_address" placeholder="종목">
                        </div>
                    </td>
                </tr>
                </tbody>
                </table>
            </div>
        </section>
        <?=$noMemberTag?>
        <div class="btn_confirm">		
            <input type="submit" value="주문하기" class="btn_medium wset">
            <a href="<?php echo base_url(); ?>m/shop/cart" class="btn_medium bx-white">주문취소</a>
        </div>
    </form>
</div>
<script>
$(function() {
    var zipcode = "";

    $("input[name=b_addr2]").focus(function() {
        var zip = $("input[name=b_zip]").val().replace(/[^0-9]/g, "");
        if(zip == "")
            return false;

        var code = String(zip);

        if(zipcode == code)
            return false;

        zipcode = code;
        calculate_sendcost(code);
    });

	// 배송지선택
	$("input[name=ad_sel_addr]").on("click", function() {
		var addr = $(this).val();

		if(addr == "1") {
			gumae2baesong(true);
		} else if(addr == "2") {
			gumae2baesong(false);
		} else {
			win_open('./orderaddress','win_address');
		}
	});

    $("select[name=sel_memo]").change(function() {
         $("textarea[name=memo]").val($(this).val());
    });
});

// 도서/산간 배송비 검사
function calculate_sendcost(code) {
    var base_url=document.getElementById('base_url').value;
    $.post(
        base_url+"process/ordersendcost",
        { zipcode: code },
        function(data) {
            $("input[name=baesong_price2]").val(data);
            $("#send_cost2").text(number_format(String(data)));

            calculate_order_price();
        }
    );
}

function calculate_order_price() {
    var sell_price = parseInt($("input[name=org_price]").val()); // 합계금액
	var send_cost2 = parseInt($("input[name=baesong_price2]").val()); // 추가배송비
	var mb_coupon  = parseInt($("input[name=coupon_total]").val()); // 쿠폰할인
	var mb_point   = parseInt($("input[name=use_point]").val().replace(/[^0-9]/g, "")); //포인트결제
	// var mb_mileage = parseInt($("input[name=use_mileage]").val().replace(/[^0-9]/g, "")); //예치금
    // var tot_price  = sell_price + send_cost2 - (mb_coupon + mb_point + mb_mileage);
    var tot_price  = sell_price + send_cost2 - (mb_coupon + mb_point);

	$("input[name=tot_price]").val(number_format(String(tot_price)));
}

function fbuyform_submit(f) {
    errmsg = "";
    errfld = "";

    var min_point	 = parseInt("<?php echo $config['usepoint']; ?>");
    var min_mileage	 = parseInt("<?php echo $config['usemileage']; ?>");
    var temp_point   = parseInt(no_comma(f.use_point.value));
    var temp_mileage = parseInt(no_comma(f.use_mileage.value));
    var sell_price   = parseInt(f.org_price.value);
    var send_cost2   = parseInt(f.baesong_price2.value);
    var mb_coupon    = parseInt(f.coupon_total.value);
    var mb_point     = parseInt(f.mb_point.value);
    var mb_mileage	= parseInt(f.mb_mileage.value);
    var tot_price    = sell_price + send_cost2 - mb_coupon;

    if(f.use_point.value == '') {
        alert('포인트사용 금액을 입력하세요. 사용을 원치 않을경우 0을 입력하세요.');
        f.use_point.value = 0;
        f.use_point.focus();
        return false;
    }

    if(temp_point > mb_point) {
        alert('포인트사용 금액은 현재 보유포인트 보다 클수 없습니다.');
        f.tot_price.value = number_format(String(tot_price));
        f.use_point.value = 0;
        f.use_point.focus();
        return false;
    }

    if(temp_point > tot_price) {
        alert('포인트사용 금액은 최종결제금액 보다 클수 없습니다.');
        f.tot_price.value = number_format(String(tot_price));
        f.use_point.value = 0;
        f.use_point.focus();
        return false;
    }

    if(temp_point > 0 && (mb_point < min_point)) {
        alert('포인트사용 금액은 '+number_format(String(min_point))+'원 부터 사용가능 합니다.');
        f.tot_price.value = number_format(String(tot_price));
        f.use_point.value = 0;
        f.use_point.focus();
        return false;
    }

    if(f.use_mileage.value == '') {
        alert('예치금사용 금액을 입력하세요. 사용을 원치 않을경우 0을 입력하세요.');
        f.use_mileage.value = 0;
        f.use_mileage.focus();
        return false;
    }

    if(temp_mileage > mb_mileage) {
        alert('예치금사용 금액은 현재 보유예치금 보다 클수 없습니다.');
        f.tot_price.value = number_format(String(tot_price));
        f.use_mileage.value = 0;
        f.use_mileage.focus();
        return false;
    }

    if(temp_mileage > tot_price) {
        alert('예치금사용 금액은 최종결제금액 보다 클수 없습니다.');
        f.tot_price.value = number_format(String(tot_price));
        f.use_mileage.value = 0;
        f.use_mileage.focus();
        return false;
    }

    if(temp_mileage > 0 && (mb_mileage < min_mileage)) {
        alert('예치금사용 금액은 '+number_format(String(min_mileage))+'원 부터 사용가능 합니다.');
        f.tot_mileage.value = number_format(String(tot_price));
        f.use_mileage.value = 0;
        f.use_mileage.focus();
        return false;
    }

    if(getSelectVal(f["paymethod"]) == ''){
        alert("결제방법을 선택하세요.");
        f.paymethod.focus();
        return false;
    }

    if(typeof(f.od_pwd) != 'undefined') {
        clear_field(f.od_pwd);
        if( (f.od_pwd.value.length<3) || (f.od_pwd.value.search(/([^A-Za-z0-9]+)/)!=-1) )
            error_field(f.od_pwd, "회원이 아니신 경우 주문서 조회시 필요한 비밀번호를 3자리 이상 입력해 주십시오.");
    }

    if(getSelectVal(f["paymethod"]) == '무통장'){
        check_field(f.bank, "입금계좌를 선택하세요");
        check_field(f.deposit_name, "입금자명을 입력하세요");
    }

    <?= $company_typeNotJs?>

    if(errmsg)
    {
        alert(errmsg);
        errfld.focus();
        return false;
    }

    if(getSelectVal(f["paymethod"]) == '계좌이체') {
        if(tot_price < 150) {
            alert("계좌이체는 150원 이상 결제가 가능합니다.");
            return false;
        }
    }

    if(getSelectVal(f["paymethod"]) == '신용카드') {
        if(tot_price < 1000) {
            alert("신용카드는 1000원 이상 결제가 가능합니다.");
            return false;
        }
    }

    if(getSelectVal(f["paymethod"]) == '휴대폰') {
        if(tot_price < 350) {
            alert("휴대폰은 350원 이상 결제가 가능합니다.");
            return false;
        }
    }

    if(document.getElementById('agree')) {
        if(!document.getElementById('agree').checked) {
            alert("개인정보 수집 및 이용 내용을 읽고 이에 동의하셔야 합니다.");
            return false;
        }
    }

    if(!confirm("주문내역이 정확하며, 주문 하시겠습니까?"))
        return false;

    f.use_point.value = no_comma(f.use_point.value);
    f.use_mileage.value = no_comma(f.use_mileage.value);
    f.tot_price.value = no_comma(f.tot_price.value);

    return true;
}

function removeLeadingZero(input) {
        if (input.value == "0") {
            input.value = "";
        }
    }

function restoreZeroIfEmpty(input) {
    if (input.value.trim() === "") {
        input.value = "0";
    }
}

function calculate_temp_point(val) {
    calculate_tot_price();
}

function calculate_temp_mileage(val) {
    calculate_tot_price();
}

function calculate_tot_price() {
    var f = document.buyform;
    var org_price   = parseInt(f.org_price.value) || 0;
    var send_cost2  = parseInt(f.baesong_price2.value) || 0;
    var mb_coupon   = parseInt(f.coupon_total.value) || 0;

    var use_point   = parseInt(no_comma(f.use_point.value)) || 0;
    var use_mileage = parseInt(no_comma(f.use_mileage.value)) || 0;

    var mb_point    = parseInt(f.mb_point.value) || 0;
    var mb_mileage  = parseInt(f.mb_mileage.value) || 0;

    // 유효성 검사
    if (!checkNum(no_comma(f.use_point.value))) {
        alert('포인트 사용액은 숫자이어야 합니다.');
        f.use_point.value = 0;
        use_point = 0;
    }
    if (!checkNum(no_comma(f.use_mileage.value))) {
        alert('예치금 사용액은 숫자이어야 합니다.');
        f.use_mileage.value = 0;
        use_mileage = 0;
    }

    if (use_point > mb_point) {
        alert('보유한 포인트보다 많이 사용할 수 없습니다.');
        f.use_point.value = 0;
        use_point = 0;
    }

    if (use_mileage > mb_mileage) {
        alert('보유한 예치금보다 많이 사용할 수 없습니다.');
        f.use_mileage.value = 0;
        use_mileage = 0;
    }

    // 최종 계산
    var tot_price = org_price + send_cost2 - mb_coupon - use_point - use_mileage;
    f.tot_price.value = number_format(String(tot_price));
}

// 결제방법
function calculate_paymethod(type) {
    var sell_price = parseInt($("input[name=org_price]").val()); // 합계금액
	var send_cost2 = parseInt($("input[name=baesong_price2]").val()); // 추가배송비
	var mb_coupon  = parseInt($("input[name=coupon_total]").val()); // 쿠폰할인
	var mb_point   = parseInt($("input[name=mb_point]").val()); // 보유포인트
    var mb_mileage = parseInt($("input[name=mb_mileage]").val()); // 보유예치금
	var tot_price  = sell_price + send_cost2 - mb_coupon;

	// 포인트잔액이 부족한가?
	if( type == '포인트' && mb_point < tot_price ) {
		alert('포인트 잔액이 부족합니다.');

		$("select[name=paymethod]").val('무통장');
		$("#bank_section").show();
		$("input[name=use_point]").val(0);
		$("input[name=use_point]").attr("readonly", false); 
		calculate_order_price();
		<?=$taxJs?>
		return;
	}
    // 예치금
    if( type == '예치금' && mb_mileage < tot_price ) {
        alert('예치금 잔액이 부족합니다.');

        $("#paymethod_bank").attr("checked", true);
        $("#bank_section").show();
        $("input[name=use_mileage]").val(0);
        $("input[name=use_mileage]").attr("readonly", false);
        calculate_order_price();
		<?=$taxJs?>
        return;
    }
    switch(type) {
		case '무통장':
			$("#bank_section").show();
			$("input[name=use_point]").val(0);
            $("input[name=use_mileage]").val(0);
			$("input[name=use_point]").attr("readonly", false); 
			calculate_order_price();
            <?=$taxJs?>
			break;
		case '포인트':
			$("#bank_section").hide();
            $("input[name=use_mileage]").val(0);
			$("input[name=use_point]").val(number_format(String(tot_price)));
			$("input[name=use_point]").attr("readonly", true);
			calculate_order_price();
			<?=$taxHideJs?>
			break;
        case '예치금':
            $("#bank_section").hide();
            $("input[name=use_point]").val(0);
            $("input[name=use_mileage]").val(number_format(String(tot_price)));
            $("input[name=use_mileage]").attr("readonly", true);
            calculate_order_price();
            <?=$taxHideJs?>
            break;
        default: // 그외 결제수단
            $("#bank_section").hide();
            $("input[name=use_point]").val(0);
            $("input[name=use_point]").attr("readonly", false);
            $("input[name=use_mileage]").val(0);
            $("input[name=use_mileage]").attr("readonly", false);
            calculate_order_price();
            <?=$taxHideJs?>
            break;
	}
}
// 현금영수증
function tax_save(val) {
	switch(val) {
		case 'Y': // 개인 소득공제용
			$("#taxsave_fld_1").show();
			$("#taxsave_fld_2").hide();
			$("#taxbill_section").hide();
			$("select[name=taxbill_yes]").val('N');
			break;
		case 'S': // 지출증빙용
			$("#taxsave_fld_1").hide();
			$("#taxsave_fld_2").show();
			$("#taxbill_section").hide();
			$("select[name=taxbill_yes]").val('N');
			break;
		default: // 발행안함
			$("#taxsave_fld_1").hide();
			$("#taxsave_fld_2").hide();
			break;
	}
}
// 세금계산서
function tax_bill(val) {
	switch(val) {
		case 'Y':  // 발행함
			$("#taxsave_fld_1").hide();
			$("#taxsave_fld_2").hide();
			$("select[name=taxsave_yes]").val('N');
			$("#taxbill_section").show();
			break;
		case 'N': //미발행
			$("#taxbill_section").hide();
			break;
	}
}
// 할인쿠폰 삭제
function coupon_cancel() {
	var f = document.buyform;
	var sell_price = parseInt(no_comma(f.tot_price.value)); // 최종 결제금액
	var mb_coupon  = parseInt(f.coupon_total.value); // 쿠폰할인
	var tot_price  = sell_price + mb_coupon;

	$("#dc_amt").text(0);
	$("#dc_cancel").hide();
	$("#dc_coupon").show();

	$("input[name=tot_price]").val(number_format(String(tot_price)));
	$("input[name=coupon_total]").val(0);
	$("input[name=coupon_price]").val("");
	$("input[name=coupon_lo_id]").val("");
	$("input[name=coupon_cp_id]").val("");
}


// 구매자 정보와 동일합니다.
function gumae2baesong(checked) {
    var f = document.buyform;

    if(checked == true) {
		f.b_name.value			= f.name.value;
		f.b_cellphone.value		= f.cellphone.value;
		// f.b_telephone.value		= f.telephone.value;
		f.b_zip.value			= f.zip.value;
		f.b_addr1.value			= f.addr1.value;
		f.b_addr2.value			= f.addr2.value;
		f.b_addr3.value			= f.addr3.value;
		f.b_addr_jibeon.value	= f.addr_jibeon.value;

        calculate_sendcost(String(f.b_zip.value));
    } else {
		f.b_name.value			= '';
		f.b_cellphone.value		= '';
		// f.b_telephone.value		= '';
		f.b_zip.value			= '';
		f.b_addr1.value			= '';
		f.b_addr2.value			= '';
		f.b_addr3.value			= '';
		f.b_addr_jibeon.value	= '';

		calculate_sendcost('');
    }
}

gumae2baesong(true);
</script>
<!-- } 주문서작성 끝 -->
