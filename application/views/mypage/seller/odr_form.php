<div id="sodr_pop" class="new_win">
	<h1><?php echo $title; ?></h1>

	<section id="anc_sodr_list">
		<h4 class="anc_tit">주문상품 목록</h4>
		<?php echo $pg_anchor; ?>
		<div class="local_desc02 local_desc">
			<p>
				주문일시 <strong><?php echo substr($od['od_time'],0,16); ?> (<?php echo $yoil_time; ?>)</strong> <span class="fc_214">|</span>
				주문총액 <strong><?php echo number_format($amount['buyprice']); ?></strong>원
				<a href="javascript:win_open('<?php echo base_url(); ?>mypage/seller_odr_print?od_id=<?php echo $od_id; ?>','order_print','670','600','yes');" class="btn_small blue fr"><i class="fa fa-print"></i> 주문서출력</a>
			</p>
		</div>

		<form name="frmorderform" method="post" action="<?=base_url()?>MypageProcess/seller_odr_statusupdate" onsubmit="return form_submit(this);">
            <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
            <input type="hidden" name="od_hp" value="<?php echo $od['cellphone']; ?>">
            <input type="hidden" name="od_email" value="<?php echo $od['email']; ?>">
            <input type="hidden" name="mb_id" value="<?php echo $od['mb_id']; ?>">
            <input type="hidden" name="pt_id" value="<?php echo $od['pt_id']; ?>">
            <input type="hidden" name="pg_cancel" value="0">

            <div class="tbl_head01">
                <table id="sodr_list">
                    <colgroup>
                        <col class="w40">
                        <col class="w60">
                        <col>
                        <col class="w90">
                        <col class="w60">
                        <col class="w70">
                        <col class="w70">
                        <col class="w70">
                        <col class="w70">
                        <col class="w70">
                        <col class="w70">
                    </colgroup>
                    <thead>
                    <tr>
                        <th scope="col">
                            <label for="chkall" class="sound_only">주문 전체</label>
                            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form);">
                        </th>
                        <th scope="col">이미지</th>
                        <th scope="col">주문상품</th>
                        <th scope="col">주문상태</th>
                        <th scope="col">수량</th>
                        <th scope="col">공급가격</th>
                        <th scope="col">상품금액</th>
                        <th scope="col">배송비</th>
                        <th scope="col">쿠폰할인</th>
                        <th scope="col">포인트결제</th>
                        <th scope="col">실결제액</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                        <?=$odr_formTable?>
                    </tbody>
                </table>
            </div>
            <div class="local_frm02">
                <i class="ionicons ion-alert-circled fc_red"></i> 환불, 반품완료 후 <b>"PG부분취소"</b>를 통해 신용카드 및 계좌이체 결제취소를 해주셔야 <b>"PG 신용카드 승인취소 처리"</b>가 완료됩니다.
            </div>
            <?=$normalOrder?>
        </form>
        <?=$odTestTag?>
        <?=$od_mod_historyTag?>
    </section>
    <section id="anc_sodr_pay" class="new_win_desc mart30">
		<h3 class="anc_tit">주문결제 내역</h3>
		<?php echo $pg_anchor; ?>
		<form name="frmorderreceiptform" action="./seller_odr_formupdate.php" method="post" autocomplete="off">
            <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
            <input type="hidden" name="mod_type" value="receipt">

            <div class="compare_wrap">
                <section id="anc_sodr_chk" class="compare_left">
                    <h3>결제상세정보 확인</h3>

                    <div class="tbl_frm01">
                        <table>
                            <colgroup>
                                <col class="w150">
                                <col>
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th scope="row">총 주문금액</th>
                                    <td class="td_price"><?php echo $buypriceStr; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">총 상품금액</th>
                                    <td class="td_price bg0"><?php echo $amountPriceStr; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">예치금 환불</th>
                                    <td class="td_price"><?php echo $refundMileagePriceStr; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">총 배송비</th>
                                    <td class="td_price fc_197">(+) <?php echo $amountBaeStr; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">총 쿠폰할인</th>
                                    <td class="td_price fc_red">(-) <?php echo $amountCouStr; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">포인트결제</th>
                                    <td class="td_price fc_red">(-) <?php echo $amountUsePointStr; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">실 결제금액</th>
                                    <td class="td_price bg0"><?php echo $amountUsePriceStr; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row" class="fc_red">환불액(PG승인취소)</th>
                                    <td class="td_price bg1 fc_red">(-) <?php echo $amountRefundStr; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">포인트적립</th>
                                    <td class="tar"><?php echo $sumPointStr; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                <section id="anc_sodr_paymo" class="compare_right">
                    <h3>결제상세정보 수정</h3>

                    <div class="tbl_frm01">
                        <table>
                            <colgroup>
                                <col class="w150">
                                <col>
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th scope="row">주문번호</th>
                                    <td><?php echo $od['od_id']; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">주문일시</th>
                                    <td><?php echo $od['od_time']; ?> (<?php echo $yoil_time; ?>)</td>
                                </tr>
                                <tr>
                                    <th scope="row">주문채널</th>
                                    <td><?php echo $od_device; ?> 쇼핑몰에서 주문</td>
                                </tr>
                                <tr>
                                    <th scope="row">결제방법</th>
                                    <td><?php echo $s_receipt_way; ?></td>
                                </tr>
                                <?=$payMethodBank?>
                                <?=$payMethodPhone?>
                                <?=$payMethodCredit?>
                                <?=$payMethodKakao?>
                                <?=$payMethodEasy?>
                                <?=$taxsaveSY?>
                                <?=$taxbillYes?>
                            </tbody>
					    </table>
				    </div>
			    </section>
		    </div>
            <div class="btn_confirm">
                <input type="submit" value="결제정보 수정" class="btn_medium">
                <a href="javascript:window.close();" class="btn_medium bx-white">닫기</a>
            </div>
		</form>
	</section>
    <section id="anc_sodr_memo">
		<h3 class="anc_tit">관리자메모</h3>
		<?php echo $pg_anchor; ?>
		<div class="local_desc02 local_desc">
			<p>현재 열람 중인 주문에 대한 내용을 메모하는곳입니다.</p>
		</div>

		<form name="frmorderform3" action="./seller_odr_formupdate.php" method="post">
            <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
            <input type="hidden" name="mod_type" value="memo">

            <label for="shop_memo" class="sound_only">관리자메모</label>
            <textarea name="shop_memo" id="shop_memo" rows="8" class="frm_textbox"><?php echo stripslashes($od['shop_memo']); ?></textarea>

            <div class="btn_confirm">
                <input type="submit" value="관리자메모 수정" class="btn_medium">
                <a href="javascript:window.close();" class="btn_medium bx-white">닫기</a>
            </div>
		</form>
	</section>
    <section id="anc_sodr_addr">
		<h3 class="anc_tit">주문자/배송지 정보</h3>
		<?php echo $pg_anchor; ?>

		<form name="frmorderform2" action="./seller_odr_formupdate.php" method="post">
            <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
            <input type="hidden" name="mod_type" value="info">

            <div class="compare_wrap">
                <section id="anc_sodr_orderer" class="compare_left">
                    <h3>주문하신 분</h3>

                    <div class="tbl_frm01">
                        <table>
                            <colgroup>
                                <col class="w100">
                                <col>
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th scope="row"><label for="od_name">이름</label></th>
                                    <td><input type="text" name="name" value="<?php echo $odName; ?>" id="od_name" required class="frm_input required"></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="telephone">전화번호</label></th>
                                    <td><input type="text" name="telephone" value="<?php echo $odTele; ?>" id="telephone" class="frm_input"></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="cellphone">핸드폰</label></th>
                                    <td><input type="text" name="cellphone" value="<?php echo $odPhone; ?>" id="cellphone" required class="frm_input required"></td>
                                </tr>
                                <tr>
                                    <th scope="row">주소</th>
                                    <td>
                                        <label for="zip" class="sound_only">우편번호</label>
                                        <input type="text" name="zip" value="<?php echo $od['zip']; ?>" id="zip" required class="frm_input required" size="5" maxlength="5">
                                        <button type="button" class="btn_small grey" onclick="win_zip('frmorderform2', 'zip', 'addr1', 'addr2', 'addr3', 'addr_jibeon');">주소검색</button><br>
                                        <span id="od_win_zip" style="display:block"></span>
                                        <input type="text" name="addr1" value="<?php echo $odAddr1; ?>" id="addr1" required class="frm_input required" size="35">
                                        <label for="addr1">기본주소</label><br>
                                        <input type="text" name="addr2" value="<?php echo $odAddr2; ?>" id="addr2" class="frm_input" size="35">
                                        <label for="addr2">상세주소</label><br>
                                        <input type="text" name="addr3" value="<?php echo $odAddr3; ?>" id="addr3" class="frm_input" size="35">
                                        <label for="addr3">참고항목</label><br>
                                        <input type="hidden" name="addr_jibeon" value="<?php echo $odAddrJibeon; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="od_email">E-mail</label></th>
                                    <td><input type="text" name="email" value="<?php echo $od['email']; ?>" id="od_email" required class="frm_input required" size="30"></td>
                                </tr>
                                <tr>
                                    <th scope="row"><span class="sound_only">주문하신 분 </span>IP Address</th>
                                    <td><?php echo $od['od_ip']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section id="anc_sodr_taker" class="compare_right">
                    <h3>받으시는 분</h3>

                    <div class="tbl_frm01">
                        <table>
                            <colgroup>
                                <col class="w100">
                                <col>
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th scope="row"><label for="b_name">이름</label></th>
                                    <td><input type="text" name="b_name" value="<?php echo $odBName; ?>" id="b_name" required class="frm_input required"></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="b_telephone">전화번호</label></th>
                                    <td><input type="text" name="b_telephone" value="<?php echo $odBTele; ?>" id="b_telephone" class="frm_input"></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="b_cellphone">핸드폰</label></th>
                                    <td><input type="text" name="b_cellphone" value="<?php echo $odBPhone; ?>" id="b_cellphone" required class="frm_input required"></td>
                                </tr>
                                <tr>
                                    <th scope="row">주소</th>
                                    <td>
                                        <label for="b_zip" class="sound_only">우편번호</label>
                                        <input type="text" name="b_zip" value="<?php echo $od['b_zip']; ?>" id="b_zip" required class="frm_input required" size="5" maxlength="5">
                                        <button type="button" class="btn_small grey" onclick="win_zip('frmorderform2', 'b_zip', 'b_addr1', 'b_addr2', 'b_addr3', 'b_addr_jibeon');">주소검색</button><br>
                                        <input type="text" name="b_addr1" value="<?php echo $odBAddr1; ?>" id="b_addr1" required class="frm_input required" size="35">
                                        <label for="b_addr1">기본주소</label><br>
                                        <input type="text" name="b_addr2" value="<?php echo $odBAddr2; ?>" id="b_addr2" class="frm_input" size="35">
                                        <label for="b_addr2">상세주소</label><br>
                                        <input type="text" name="b_addr3" value="<?php echo $odBAddr3; ?>" id="b_addr3" class="frm_input" size="35">
                                        <label for="b_addr3">참고항목</label><br>
                                        <input type="hidden" name="b_addr_jibeon" value="<?php echo $odBAddrJibeon; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">전달 메세지</th>
                                    <td><?= $odMemo?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>

            <div class="btn_confirm">
                <input type="submit" value="주문자/배송지 정보 수정" class="btn_medium">
                <a href="javascript:window.close();" class="btn_medium bx-white">닫기</a>
            </div>
		</form>
	</section>
</div>
<script>
$(function() {
	// 부분취소창
	$(".orderpartcancel").on("click", function() {
		var href = this.href;
		window.open(href, "partcancelwin", "left=100, top=100, width=600, height=350, scrollbars=yes");
		return false;
	});
});
function form_submit(f){
	var status = document.pressed;

	if(!is_checked("chk[]")) {
		alert("처리할 자료를 하나 이상 선택해 주십시오.");
		return false;
	}

	if(status == "운송장번호수정") {
		f.action = "./seller_odr_baesongupdate.php";
		return true;
	}

	if(confirm("주문상태를 변경하시겠습니까?")) {
		return true;
	} else {
		return false;
	}
}
</script>