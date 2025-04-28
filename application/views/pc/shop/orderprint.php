<div style="margin:10px">
	<div class="tbl_head02 tbl_wrap">
		<table>
            <colgroup>
                <col>
                <col class="w100">
                <col class="w60">
                <col class="w80">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">상품정보</th>
                    <th scope="col">결제금액</th>
                    <th scope="col">수량</th>
                    <th scope="col">배송비</th>
                </tr>
            </thead>
            <tbody>
                <?=$printTb?>
            </tbody>
		</table>
	</div>
    <dl id="sod_ws_tot">
		<dt class="bt_nolne">주문총액</dt>
		<dd class="bt_nolne"><strong><?php echo $stotal_price;?></strong></dd>
        <?=$stotal_coupon?>
        <?=$stotal_usepoint?>
        <?=$stotal_usemileage?>
        <?=$stotal_baesong?>
        <dt class="ws_price">총계</dt>
		<dd class="ws_price"><strong><?php echo $stotal_useprice; ?></strong></dd>
		<dt class="bt_nolne">포인트적립</dt>
		<dd class="bt_nolne"><strong><?php echo $stotal_point; ?></strong></dd>
	</dl>
    <section id="sod_fin_orderer">
		<h2 class="anc_tit">주문하신 분</h2>
		<div class="tbl_frm01 tbl_wrap">
			<table>
                <colgroup>
                    <col class="w120">
                    <col>
                </colgroup>
                <tr>
                    <th scope="row">이 름</th>
                    <td><?php echo $od_name; ?></td>
                </tr>
                <tr>
                    <th scope="row">전화번호</th>
                    <td><?php echo $od_tele; ?></td>
                </tr>
                <tr>
                    <th scope="row">핸드폰</th>
                    <td><?php echo $od_cell; ?></td>
                </tr>
                <tr>
                    <th scope="row">주 소</th>
                    <td><?php echo $od_addr; ?></td>
                </tr>
                <tr>
                    <th scope="row">E-mail</th>
                    <td><?php echo $od_email; ?></td>
                </tr>
            </table>
		</div>
	</section>
    <section id="sod_fin_receiver">
		<h2 class="anc_tit">받으시는 분</h2>
		<div class="tbl_frm01 tbl_wrap">
			<table>
			<colgroup>
				<col class="w120">
				<col>
			</colgroup>
			<tr>
                <th scope="row">이 름</th>
				<td><?php echo $od_Bname;?></td>
			</tr>
            <tr>
				<th scope="row">전화번호</th>
				<td><?php echo $od_Btele; ?></td>
			</tr>
            <tr>
				<th scope="row">핸드폰</th>
				<td><?php echo $od_Bcell; ?></td>
			</tr>
            <tr>
				<th scope="row">주 소</th>
				<td><?php echo $od_Baddr; ?></td>
			</tr>
            <?=$od_memo?>
            </table>
		</div>
	</section>
    <div class="btn_confirm marb50">
		<a href='javascript:od_print();' class="btn_medium">인쇄</a>
		<a href='javascript:self.close();' class="btn_medium bx-white">닫기</a>
	</div>
</div>
<script>
function od_print(){
	print();
}
</script>