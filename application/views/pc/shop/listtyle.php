<h2 class="pg_tit">
	<span><?php echo $title; ?></span>
	<p class="pg_nav">HOME<i>&gt;</i><?php echo $title; ?></p>
</h2>
<div class="tab_sort">
	<span class="total">전체상품 <b class="fc_90" id="total"><?php echo number_format($total_count); ?></b>개</span>
	<ul>
		<?php echo $sort_str; // 탭메뉴 ?>
	</ul>
    <!-- <select id="page_rows" onchange="location='<?=$changeSel?>&page_rows='+this.value;">
		<?=$selectOpt?>
	</select> -->
</div>
<?=$listTag?>
<?=$empty?>
<?=$pagination?>