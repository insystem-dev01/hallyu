<?=$LgJs?>
<div id="sod_fin">
	<div id="sod_fin_no">주문번호 <strong><?php echo $od_id; ?></strong></div>

	<section id="sod_fin_list">
        <h2>주문하신 상품</h2>
        <ul id="sod_list_inq" class="sod_list">
            <?=$orderTb?>
        </ul>
		<dl id="sod_bsk_tot">
            <dt class="sod_bsk_cnt"><span>주문총액</span></dt>
            <dd class="sod_bsk_cnt"><strong><?php echo $stoalPrice; ?></strong></dd>
            <dt><span>상품금액</span></dt>
            <dd><strong><?=$stoalGoodsPrice?></strong></dd>
            <?=$stotalBaesong?>
            <?=$stotalCoupon?>
            <?=$stotalUsept?>
            <?=$stotalUseMile?>
            <?php if($gs['type'] == "timesale") { ?>
                <?php if($stotalCancelPrice > 0) { ?>
                    <dt><span>환불액</span></dt>
                    <dd><strong><?=number_format($stotalCancelPrice)?>원</strong></dd>
                <?php } else if($gs['isopen'] == 5){ ?>
                    <dt><span>예치금 적립</span></dt>
                    <dd><strong><?php echo $refund_mileage_price; ?></strong></dd>
                <?php } ?>
            <?php } ?>
            <dt class="sod_bsk_point"><span>포인트 적립</span></dt>
            <dd class="sod_bsk_point"><strong><?php echo $stoalPoint;?></strong></dd>
        </dl>
    </section>
    <section id="sod_fin_pay">
		<h3 class="anc_tit">결제정보</h3>
		<div class="odf_tbl">
			<table>
                <colgroup>
                    <col class="w70">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">주문번호</th>
                        <td><?php echo $od_id; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">주문일시</th>
                        <td><?php echo $od['od_time']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">결제방식</th>
                        <td><?php echo $payMethod; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">결제금액</th>
                        <td><?php echo $stotalUsePrice; ?></td>
                    </tr>
                    <?=$payDate?>
                    <?=$app_no_subjTag?>
                    <?=$disp_bankTag?>
                    <?=$disp_receiptTag?>
                    <?=$taxsaveTag?>
                </tbody>
			</table>
		</div>
	</section>

    <section id="sod_fin_orderer">
		<h3 class="anc_tit">주문하신 분</h3>
		<div class="odf_tbl">
			<table>
                <colgroup>
                    <col class="w70">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">이 름</th>
                        <td><?php echo $odName; ?></td>
			        </tr>
                    <tr>
                        <th scope="row">연락처</th>
                        <td><?php echo $odCell; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">주 소</th>
                        <td><?php echo $odAddr; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">E-mail</th>
                        <td><?php echo $odEmail; ?></td>
                    </tr>
                </tbody>
			</table>
		</div>
	</section>
    <section id="sod_fin_receiver">
		<h3 class="anc_tit">받으시는 분</h3>
		<div class="odf_tbl">
			<table>
                <colgroup>
                    <col class="w70">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">이 름</th>
                        <td><?php echo $odBName; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">연락처</th>
                        <td><?php echo $odBCell; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">주 소</th>
                        <td><?php echo $odBAddr; ?></td>
                    </tr>
                    <?=$odMemo?>
                </tbody>
			</table>
		</div>
	</section>
    <?=$not_cancelTag?>
<script>
function fcancel_check(f){
	var type = f.type.value;

	if(type == "timesale") {
		if(!confirm("해당 주문은 특가 상품으로 취소시 10%의 수수료가 부과됩니다. 주문을 정말 취소하시겠습니까?")) {
			return false;
		}
	} else {
		if(!confirm("주문을 정말 취소하시겠습니까?")) {
			return false;
		}
	}

    var memo = f.cancel_memo.value;
    if(memo == "") {
        alert("취소사유를 입력해 주십시오.");
        return false;
    }

    return true;
}
</script>
</div>