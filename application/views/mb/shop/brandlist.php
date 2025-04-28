<h2 class="br_view_tit">
	<span class="tit_txt"><?php echo $br['br_name'];?></span>
	<span class="tit_logo"><img src="<?php echo $br_logo;?>" title="<?php echo $br['br_name']; ?>"></span>
</h2>
<div class="br_search marb10">
	<form name="frmbrandsearch">
		<dl class="sch_inner">
			<dt><label for="ssch_q">SEARCH</label></dt>
			<dd><input type="text" name="qstx" value="" id="ssch_q" maxlength="30"><button type="submit" class="btn_submit fa fa-search"></button></dd>
			<input type="hidden" name="br_id" value="<?= $br_id ?>">
		</dl>
	</form>
</div>
<!-- 상품 정렬 선택 시작 { -->
<div id="sct_sort">
	<div class="count">전체 <strong><?php echo number_format($total_count); ?></strong>개</div>
	<span id="btn_sort"><?php echo $sort_name; ?></span>
</div>
<div id="sort_li">
	<h2>상품 정렬</h2>
	<ul>
		<?php echo $sort_str; // 탭메뉴 ?>
	</ul>
	<span id="sort_close" class="ionicons ion-ios-close-empty"></span>
</div>
<script>
$(function() {
	var mbheight = $(window).height();

	$('#btn_sort').click(function(){
		$('#sort_bg').fadeIn(300);
		$('#sort_li').slideDown('fast');
		$('html').css({'height':mbheight+'px', 'overflow':'hidden'});
	});

	$('#sort_bg, #sort_close').click(function(){
		$('#sort_bg').fadeOut(300);
		$('#sort_li').slideUp('fast');
		$('html').css({'height':'100%', 'overflow':'scroll'});
	});
});
</script>
<!-- } 상품 정렬 선택 끝 -->
<div>
    <?=$tableTag?>
</div>