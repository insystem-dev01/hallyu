<h2 class="pg_tit">
	<span><?php echo $tb['title']; ?></span>
	<p class="pg_nav">HOME<i>&gt;</i><?php echo $default['de_pname_7']; ?></p>
</h2>
<?=$bming_urlTag?>
<div class="tab_sort">
	<span class="total">전체상품 <b class="fc_90" id="total"><?php echo number_format($total_count); ?></b>개</span>
	<ul>
		<?php echo $sort_str; // 탭메뉴 ?>
	</ul>
</div>

<div class="pr_desc">
	<ul>
        <?=$planlistTag?>
    </ul>
</div>
<?=$pagination?>