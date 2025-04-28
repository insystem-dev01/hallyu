<h2 class="pg_tit">
	<span><?php echo $default['de_pname_6']; ?></span>
	<p class="pg_nav">HOME<i>&gt;</i><?php echo $default['de_pname_6']; ?><i>&gt;</i><?php echo $title; ?></p>
</h2>
<h2 class="br_view_tit">
	<span class="tit_txt"><?php echo $title; ?></span>
	<span class="tit_logo"><img src="<?php echo $br_logo; ?>" title="<?php echo $title; ?>"></span>
</h2>
<div class="br_search list">
	<form name="frmbrandsearch">
		<dl class="sch_inner">
			<dt><label for="ssch_q">SEARCH</label></dt>
			<dd><input type="text" name="qstx" value="<?= $qstx ?>" id="ssch_q" maxlength="30"><button type="submit" class="btn_submit fa fa-search"></button></dd>
			<input type="hidden" name="br_id" value="<?= $br_id ?>">
		</dl>
	</form>
</div>
<div class="tab_sort">
	<span class="total">전체상품 <b class="fc_90" id="total"><?php echo number_format($total_count); ?></b>개</span>
	<ul>
		<?php echo $sort_str; // 탭메뉴 ?>
	</ul>
</div>
<div class="pr_desc wli4">
	<ul>
        <?=$tableTag?>
    </ul>
</div>
<?=$empty?>
<?=$pagination?>