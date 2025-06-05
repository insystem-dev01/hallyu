<!-- 장바구니 -->
<script src="<?php echo base_url(); ?>mb/js/shop.js"></script>

<div class="bg active">
	<div class="cont_wrap">
		<div class="cont_box">
			<div class="title">함께장터</div>
			<div class="row">
				<div class="text">
					최저가보다 더 최저가로 <br>
					구매하는 신개념 입찰 쇼핑 함께장터
				</div>
				<div class="button" onclick="location.href='<?=base_url()?>m/shop/timesale?status=active'">전체보기 ></div>
			</div>
		</div>
		<?php echo $timesale_active ?>
		<?php if(empty($timesale_active)) { ?>
			<div class="goods_empty">등록된 상품이 없습니다.</div>
		<?php } ?>
	</div>
</div>

<div class="bg pending">
	<div class="cont_wrap">
		<div class="cont_box pending">
			<div class="title">함께장터 오픈예정</div>
			<div class="row">
				<div class="button" onclick="location.href='<?=base_url()?>m/shop/timesale?status=pending'">전체보기 ></div>
			</div>
		</div>
		<?php echo $timesale_pending ?>
		<?php if(empty($timesale_pending)) { ?>
			<div class="goods_empty">등록된 상품이 없습니다.</div>
		<?php } ?>
	</div>
</div>

<div class="cont_wrap">
	<?=$mainBan?>
</div>

<div class="cont_wrap">
	<?php echo $plan ?>
	<?php if(empty($plan)) { ?>
		<div class="goods_empty">등록된 상품이 없습니다.</div>
	<?php } ?>
</div>

<div class="cont_wrap">
	<div class="attention_tit marb30"></div>
	<?php echo $attention ?>
	<?php if(empty($attention)) { ?>
		<div class="goods_empty">등록된 상품이 없습니다.</div>
	<?php } else { ?>
		<div class="slide-button attention">
			<div class="attention-button-prev"></div>
			<div class="attention-button-next"></div>
		</div>
	<?php } ?>
</div>

<div class="cont_wrap">
	<div class="miniBan">
		<?=$miniBan?>
	</div>
</div>

<div class="bg brand">
	<div class="brand_tit">BRAND 브랜드관</div>
</div>
<div class="cont_wrap">
	<div class="cont_box brand">
		<div class="attention_tit"></div>
		<div class="button" onclick="location.href='<?=base_url()?>m/shop/brand'">전체보기 ></div>
	</div>
	<?php echo $brand ?>
	<?php if(empty($brand)) { ?>
		<div class="goods_empty">등록된 상품이 없습니다.</div>
	<?php } ?>
</div>

<div class="bg substance">
	<div class="substance_tit">SUBSTANCE 실속관</div>
</div>
<div class="cont_wrap">
	<div class="cont_box substance">
		<div class="attention_tit"></div>
		<div class="button" onclick="location.href='<?=base_url()?>m/shop/cateList'">전체보기 ></div>
	</div>
	<?php echo $substance ?>
	<?php if(empty($substance)) { ?>
		<div class="goods_empty">등록된 상품이 없습니다.</div>
	<?php } ?>
</div>