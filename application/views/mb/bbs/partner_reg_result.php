<div id="fpartner_result">
	<h3 class="anc_tit">입금받으실 계좌</h3>
	<div class="tbl_frm01 tbl_wrap">
		<table>
		<colgroup>
			<col class="w80">
			<col>
		</colgroup>
		<tbody>
		<tr>
			<th scope="row">은행명</th>
			<td><?php echo $partner['bank_name']; ?></td>
		</tr>
		<tr>
			<th scope="row">계좌번호</th>
			<td><?php echo $partner['bank_account']; ?></td>
		</tr>
		<tr>
			<th scope="row">예금주명</th>
			<td><?php echo $partner['bank_holder']; ?></td>
		</tr>
		</tbody>
		</table>
	</div>
    <h3 class="anc_tit">결제정보</h3>
	<div class="tbl_frm01 tbl_wrap">
		<table>
            <colgroup>
                <col class="w80">
                <col>
            </colgroup>
            <tr>
                <th scope="row">신청일시</th>
                <td><?php echo $partner['reg_time']; ?></td>
            </tr>
            <tr>
                <th scope="row">결제방법</th>
                <td><?php echo ($partner['pay_settle_case']=='1')?"무통장입금":"신용카드"; ?></td>
            </tr>
            <tr>
                <th scope="row">결제금액</th>
                <td>
                    <?=$recept_priceTag?>
                </td>
		    </tr>
            <?=$part_bankInfo?>
            <?=$partMemo?>
        </table>
	</div>
	<div class="btn_confirm">
		<a href="<?php echo base_url().'m/main'; ?>" class="btn_medium wset">확인</a>
	</div>
</div>
