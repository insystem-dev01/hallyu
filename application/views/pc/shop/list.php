<!-- <h2 class="pg_tit">
	<span><?php echo $ca['catename']; ?></span>
	<p class="pg_nav">HOME<?php echo $getMove;?></p>
</h2> -->
<?=$heading?>
<h2 class="pg_title">
	<div class="inner">
		<dl class="txt_bx">
			<dt>열린장터</dt>
			<dd>시간제한 없이 누릴 수 있는 열린장터 상품을 만나보세요</dd>
		</dl>
	</div>
</h2>
<?=$treeCate?>
<div class="tab_sort">
	<span class="total">전체상품 <b class="fc_90" id="total"><?php echo number_format($total_count); ?></b>개</span>
	<ul>
		<?php echo $sort_str; // 탭메뉴 ?>
	</ul>
	<!-- <select id="page_rows" onchange="location='<?=$pageRowchange?>&page_rows='+this.value;">
		<?php echo $selOpt[0]; ?>
		<?php echo $selOpt[1]; ?>
		<?php echo $selOpt[2]; ?>
	</select> -->
</div>
<?=$listTb?>
<?=$empty?>
<?=$pagination?>