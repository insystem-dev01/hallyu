<h2 class="pg_tit"><span><?php echo $title; ?></span></h2>

<div class="search_area">
	<fieldset>
		<legend>검색</legend>
		<form
			name="fsearch2"
			id="fsearch2"
			action="<?php echo base_url(); ?>shop/search"
			method="post">
			<input type="hidden" name="hash_token" value="<?php echo $OM_HASH_TOKEN; ?>">
			<input type="hidden" name="excludeSoldOut" id="excludeSoldOutHidden" value="">
			<input type="hidden" name="onlyBid" id="onlyBidHidden" value="">

			<table class="searchForm">
				<tr>
				<td class="">
					<span class="bold">" <?php echo $ss_tx; ?> "</span>
					통합검색 결과 총
					<span class="bold"><?php echo number_format($total_count); ?></span>개의 상품이 검색 되었습니다.
				</td>
				</tr>
				<tr>
				<td class="">
					<input type="text" name="ss_tx" value="<?php echo $ss_tx; ?>" size="40" class="frm_input">
					<input type="submit" value="검색" class="btn_small grey">
				</td>
				</tr>
				<tr>
				<td class="check_area">
					<input type="checkbox" id="excludeSoldOut" onchange="submitForm()" <?=(isset($excludeSoldOut) && $excludeSoldOut == 1) ? "checked=checked" : ""?>> 판매종료된 상품 제외
					<input type="checkbox" id="onlyBid" onchange="submitForm()" <?=(isset($onlyBid) && $onlyBid == 1) ? "checked=checked" : ""?>> bid 상품만 보기
				</td>
				</tr>
			</table>
		</form>
	</fieldset>
</div>
<div class="tab_sort">
    <ul>
		<?php echo $sort_str; // 탭메뉴 ?>
	</ul>
	<?php if(false) { ?>
    <select id="page_rows" onchange="location='<?php echo "{$requestUrl}?{$qstr3}";?>&page_rows='+this.value;">
		<?php echo $selectOpt[0]; ?>
		<?php echo $selectOpt[1]; ?>
		<?php echo $selectOpt[2]; ?>
	</select>
	<?php } ?>
</div>

<?=$searchTb?>
<?=$empty?>
<?=$pagination?>
<script>
	function submitForm() {
		document.getElementById('excludeSoldOutHidden').value = document.getElementById('excludeSoldOut').checked ? '1' : '';
		document.getElementById('onlyBidHidden').value = document.getElementById('onlyBid').checked ? '1' : '';
		document.getElementById('fsearch2').submit();
	}
</script>