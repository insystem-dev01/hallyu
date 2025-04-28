<?=$lgTxt?>
<?=$aside_my?>
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>마이페이지<i>&gt;</i><?php echo $title; ?></p>
	</h2>
    <p class="pg_cnt">주문번호 <em><?php echo $od_id; ?></em> 의 상세내역입니다.</p>
    <div class="tbl_head02 tbl_wrap">
		<table>
            <colgroup>
                <col class="w90">
                <col>
                <col class="w100">
                <col class="w140">
            </colgroup>
            <thead>
            <tr>
                <th scope="col">주문일자</th>
                <th scope="col">상품정보</th>
                <th scope="col">결제금액</th>
                <th scope="col">상태</th>
            </tr>
            </thead>
            <tbody>
                <?=$returnTb?>
            </tbody>
        </table>
    </div>
    <dl id="sod_ws_tot">
		<dt class="ws_price">주문총액</dt>
		<dd class="ws_price"><strong><?php echo $totprice; ?></strong></dd>
		<dt>상품금액</dt>
		<dd><strong><?php echo $goodsPrice; ?></strong></dd>
        <?=$baesong?>
        <?=$couDis?>
        <?=$usepoint?>
        <?=$usemileage?>
		<?php if($gs['type'] == "timesale") { ?>
			<?php if($cancel_price > 0) { ?>
				<dt>환불액</dt>
				<dd><strong><?=number_format($cancel_price)?>원</strong></dd>
			<?php } else if($gs['isopen'] == 5){ ?>
				<dt>예치금 적립</dt>
				<dd><strong><?php echo $refund_mileage_price; ?></strong></dd>
			<?php } ?>
		<?php } ?>
		<dt>포인트 적립</dt>
		<dd><strong><?php echo $totpoint;?></strong></dd>
    </dl>
    <section id="sod_fin_pay">
		<h2 class="anc_tit">결제정보</h2>
		<div class="tbl_frm01 tbl_wrap">
			<table>
			<colgroup>
				<col class="w140">
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
				<td><?php echo $paymethod; ?></td>
			</tr>
            <tr>
				<th scope="row">결제금액</th>
				<td><?php echo $pay_price; ?></td>
			</tr>
            <?=$receipt_time?>
            <?=$app_noTag?>
            <?=$disp_bankTag?>
            <?=$disp_receiptTag?>
            <?=$de_taxsave_useTag?>
            </tbody>
			</table>
		</div>
	</section>
    <section id="sod_fin_orderer">
		<h2 class="anc_tit">주문하신 분</h2>
		<div class="tbl_frm01 tbl_wrap">
			<table>
			<colgroup>
				<col class="w140">
				<col>
			</colgroup>
			<tr>
				<th scope="row">이 름</th>
                <td><?php echo $odName; ?></td>
			</tr>
			<?php if(false) { ?>
            <tr>
				<th scope="row">전화번호</th>
				<td><?php echo $odTelepone;?></td>
			</tr>
			<?php } ?>
			<tr>
				<th scope="row">핸드폰</th>
				<td><?php echo $odCellepone;?></td>
			</tr>
			<tr>
				<th scope="row">주 소</th>
				<td><?php echo $odAddress; ?></td>
			</tr>
			<tr>
				<th scope="row">E-mail</th>
				<td><?php echo $odEmail; ?></td>
			</tr>
			</table>
		</div>
	</section>
    <section id="sod_fin_receiver">
		<h2 class="anc_tit">받으시는 분</h2>
		<div class="tbl_frm01 tbl_wrap">
			<table>
			<colgroup>
				<col class="w140">
				<col>
			</colgroup>
			<tr>
				<th scope="row">이 름</th>
				<td><?php echo $odBName; ?></td>
			</tr>
			<?php if(false) { ?>
			<tr>
				<th scope="row">전화번호</th>
				<td><?php echo $odBTelepone; ?></td>
			</tr>
			<?php } ?>
			<tr>
				<th scope="row">핸드폰</th>
				<td><?php echo $odBCellepone; ?></td>
			</tr>
			<tr>
				<th scope="row">주 소</th>
                
				<td><?php echo $odBAddress; ?></td>
			</tr>
			<?=$odBMemo?>
			</table>
		</div>
	</section>
    <?=$noCancelHistory?>
</div>
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
