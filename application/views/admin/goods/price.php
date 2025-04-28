<?=$headTitle?>
<div class="price_engine">
	<p class="lh6">
		가격비교사이트는 네이버 지식쇼핑, 다음 쇼핑하우 등이 있습니다.<br>
		아래 가격비교사이트 중 희망하시는 사이트에 입점합니다.<br>
		<strong>가격비교 엔진페이지 URL</strong>을 참고하여 해당 엔진페이지 URL을 입점하신 사이트에 알려주시면 됩니다.
	</p>
</div>

<h2>가격비교 엔진페이지 URL</h2>
<div class="price_engine">
	<p>사이트명을 클릭하시면 해당 사이트로 이동합니다.</p>
	<dt><a href="http://shopping.naver.com/" target="_blank">네이버 지식쇼핑</a></dt>
	<dd>
		<ul>
			<li>&#8226; 입점안내 : <a href="http://join.shopping.naver.com/join/intro.nhn" target="_blank">http://join.shopping.naver.com/join/intro.nhn</a></li>
			<li>&#8226; 전체상품 URL : <a href="<?php echo base_url(); ?>shop/price/naver" target="_blank"><?php echo base_url(); ?>shop/price/naver</a></li>
			<li>&#8226; 요약상품 URL : <a href="<?php echo base_url(); ?>shop/price/naver_summary" target="_blank"><?php echo base_url(); ?>shop/price/naver_summary</a></li>
		</ul>
	</dd>
	<dt><a href="http://shopping.daum.net/" target="_blank">다음 쇼핑하우</a></dt>
	<dd>
		<ul>
			<li>&#8226; 입점안내 : <a href="http://commerceone.biz.daum.net/join/intro.daum" target="_blank">http://commerceone.biz.daum.net/join/intro.daum</a></li>
			<li>&#8226; 전체상품 URL : <a href="<?php echo base_url(); ?>shop/price/daum" target="_blank"><?php echo base_url(); ?>shop/price/daum</a></li>
			<li>&#8226; 요약상품 URL : <a href="<?php echo base_url(); ?>shop/price/daum_summary" target="_blank"><?php echo base_url(); ?>shop/price/daum_summary</a></li>
		</ul>
	</dd>
</div>

<?=$headTitleEnd?>