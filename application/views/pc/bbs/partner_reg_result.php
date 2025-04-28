<?=$aside_cs?>
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>고객센터<i>&gt;</i><?php echo $title; ?></p>
	</h2>
    <h3 class="anc_tit">입금받으실 계좌</h3>
	<div class="tbl_frm01 tbl_wrap">
		<table>
            <colgroup>
                <col class="w140">
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
    <h3 class="anc_tit mart30">결제정보</h3>
	<div class="tbl_frm01 tbl_wrap">
		<table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tr>
                <th scope="row">신청일시</th>
                <td><?php echo $partner['reg_time']; ?></td>
            </tr>
            <tr>
                <th scope="row">결제방법</th>
                <td><?php echo $card_case; ?></td>
            </tr>
            <tr>
                <th scope="row">결제금액</th>
                <td>
                    <?=$receipt_price?>
                </td>
            </tr>
            <?=$inputBank?>
            <?=$partMemo?>
        </table>
	</div>
    <div class="btn_confirm">
		<a href="<?php echo base_url(); ?>" class="btn_large wset">확인</a>
	</div>
</div>