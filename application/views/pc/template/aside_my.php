<!-- 좌측메뉴 시작 { -->
<aside id="aside">
	<div class="aside_hd">
		<p class="eng">MY PAGE</p>
		<p class="kor">마이페이지</p>
	</div>
	<div class="aside_name"><?php echo $name; ?></div>
	<!-- <ul class="aside_bx">
		<li>포인트 <span><a href="<?php echo base_url(); ?>shop/point"><?php echo number_format($member['point']); ?></a>P</span></li>
		<li>예치금 <span><a href="<?php echo base_url(); ?>shop/mileage"><?php echo number_format($member['mileage']); ?></a>P</span></li>
	</ul> -->
	<dl class="aside_my">
		<dt>주문현황</dt>
		<dd><a href="<?php echo base_url(); ?>shop/orderinquiry">주문/배송조회</a></dd>
		<dd><a href="<?php echo base_url(); ?>shop/review">리뷰</a></dd>
		<!-- <dt>쇼핑통장</dt> -->
		<!-- <dd><a href="<?php echo base_url(); ?>shop/point">포인트조회</a></dd>
		<?=$giftYes?>
		<?= $couponYes ?> -->
		<dt>관심상품</dt>
		<dd><a href="<?php echo base_url(); ?>shop/cart">장바구니</a></dd>
		<dd><a href="<?php echo base_url(); ?>shop/wish">내가 찜한상품</a></dd>
		<dt>회원정보</dt>
		<dd><a href="<?php echo base_url(); ?>bbs/member_confirm?url=<?=base_url()?>bbs/register_form">회원정보수정</a></dd>
		<dd class="marb5"><a href="<?php echo base_url(); ?>bbs/leave_form">회원탈퇴</a></dd>
	</dl>
</aside>
<!-- } 좌측메뉴 끝 -->
