<!-- 장바구니 -->
<script src="<?php echo base_url(); ?>js/shop.js"></script>

<div class="bg active">
	<div class="cont_wrap">
		<div class="cont_box">
			<div class="title">Bid</div>
			<div class="row">
				<div class="text">
					최저가보다 더 최저가로 <br>
					구매하는 신개념 입찰 쇼핑 bid
				</div>
				<div class="button" onclick="location.href='<?= base_url() ?>shop/timesale?status=active'">전체보기 ></div>
			</div>
		</div>
		<?php echo $timesale_active ?>
		<?php if (empty($timesale_active)) { ?>
			<div class="goods_empty">등록된 상품이 없습니다.</div>
		<?php } else { ?>
			<div class="slide-button">
				<div class="timesale-active-button-prev"><span>
						<< /span>
				</div>
				<div class="timesale-active-button-next"><span>></span></div>
			</div>
		<?php } ?>
	</div>
</div>

<div class="bg pending mart80">
	<div class="cont_wrap">
		<div class="cont_box">
			<div class="title">Bid 오픈예정</div>
			<div class="row">
				<div class="button" onclick="location.href='<?= base_url() ?>shop/timesale?status=pending'">전체보기 ></div>
			</div>
		</div>
		<?php echo $timesale_pending ?>
		<?php if (empty($timesale_pending)) { ?>
			<div class="goods_empty">등록된 상품이 없습니다.</div>
		<?php } else { ?>
			<div class="slide-button">
				<div class="timesale-pending-button-prev"></div>
				<div class="timesale-pending-button-next"></div>
			</div>
		<?php } ?>
	</div>
</div>

<?= $mainBan ?>

<div class="cont_wrap mart80">
	<?php echo $plan ?>
	<?php if (empty($plan)) { ?>
		<div class="goods_empty">등록된 상품이 없습니다.</div>
	<?php } ?>
</div>

<div class="cont_wrap mart80">
	<div class="attention_tit marb30"></div>
	<?php echo $attention ?>
	<?php if (empty($attention)) { ?>
		<div class="goods_empty">등록된 상품이 없습니다.</div>
	<?php } else { ?>
		<div class="slide-button attention">
			<div class="attention-button-prev"></div>
			<div class="attention-button-next"></div>
		</div>
	<?php } ?>
</div>

<div class="cont_wrap">
	<div class="wide_bn mart40"><?= $miniBan ?></div>
</div>

<div class="bg brand mart80">
	<div class="brand_tit">BRAND 브랜드관</div>
	<div class="cont_wrap brand">
		<div class="cont_box brand">
			<div class="attention_tit"></div>
			<div class="button" onclick="location.href='<?= base_url() ?>shop/brand'">전체보기 ></div>
		</div>

		<?php echo $brand ?>
		<?php if (empty($brand)) { ?>
			<div class="goods_empty">등록된 상품이 없습니다.</div>
		<?php } else { ?>
			<div class="slide-button brand">
				<div class="brand-button-prev"><span>
						<< /span>
				</div>
				<div class="brand-button-next"><span>></span></div>
			</div>
		<?php } ?>
	</div>
</div>

<div class="bg substance mart80">
	<div class="substance_tit">SUBSTANCE 실속관</div>
	<div class="cont_wrap substance">
		<div class="cont_box substance">
			<div class="attention_tit"></div>
			<div class="button" onclick="location.href='<?= base_url() ?>shop/cateList'">전체보기 ></div>
		</div>

		<?php echo $substance ?>
		<?php if (empty($substance)) { ?>
			<div class="goods_empty">등록된 상품이 없습니다.</div>
		<?php } else { ?>
			<div class="slide-button substance">
				<div class="substance-button-prev"><span>
						<< /span>
				</div>
				<div class="substance-button-next"><span>></span></div>
			</div>
		<?php } ?>
	</div>
</div>