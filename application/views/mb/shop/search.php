<!-- 상품 정렬 선택 시작 { -->
<div id="sct_sort">
	<div class="count">전체 <strong><?php echo number_format($total_count); ?></strong>개</div>
	<span id="btn_sort"><?php echo $sort_name; ?></span>
	<div class="check_area">
		<input type="checkbox" id="excludeSoldOut" onchange="submitForm()" <?=(isset($excludeSoldOut) && $excludeSoldOut == 1) ? "checked=checked" : ""?>> 판매종료된 상품 제외
		<input type="checkbox" id="onlyBid" onchange="submitForm()" <?=(isset($onlyBid) && $onlyBid == 1) ? "checked=checked" : ""?>> bid 상품만 보기
	</div>
</div>
<div id="sort_li">
	<h2>상품 정렬</h2>
	<ul>
		<?php echo $sort_str; // 탭메뉴 ?>
	</ul>
	<span id="sort_close" class="ionicons ion-ios-close-empty"></span>
</div>
<div id="sort_bg"></div>
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

function submitForm() {
	document.getElementById('excludeSoldOutHidden').value = document.getElementById('excludeSoldOut').checked ? '1' : '';
	document.getElementById('onlyBidHidden').value = document.getElementById('onlyBid').checked ? '1' : '';
	document.getElementById('fsearch').submit();
}
</script>
<!-- } 상품 정렬 선택 끝 -->
<div>
    <?=$searchTb?>
    <?=$pagination?>
</div>