<div id="smb_my">
	<p id="sod_fin_no">
		<strong><?php echo $member['name']; ?></strong>님의 쿠폰내역
	</p>

	<table class="navbar">
	<colgroup>
		<col width="50%">
		<col width="50%">
	</colgroup>
	<tbody>
	<tr>
		<td <?php echo $selected1; ?>><a href="<?php echo base_url(); ?>m/shop/coupon">사용가능쿠폰</a></td>
		<td <?php echo $selected2; ?>><a href="<?php echo base_url(); ?>m/shop/coupon?sca=1">사용/기한만료쿠폰</a></td>
	</tr>
	</tbody>
	</table>
    <?=$couponTb?>
    <?=$pagination?>
</div>