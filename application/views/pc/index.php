<!-- 장바구니 -->
<script src="<?php echo base_url(); ?>js/shop.js"></script>

<div class="bg active">

</div>

<!-- 메인 배너 (Swiper) -->
    <div class="main-banner">
        <div class="banner-swiper swiper-container">
            <div class="swiper-wrapper">
				<?php for($i= 0; $i< 3; $i++) { ?>
                <div class="swiper-slide">
                    <img src="<?=base_url() ?>img/banner/s_banner_<?=$i?>.png" alt="배너<?=$i?>">
                    <div class="slide-navigation">
                        <div class="swiper-button-prev">
							 <img src="/img/swiper/arrow_L.png" alt="이전">
						</div>
                        <div class="swiper-button-next">
							<img src="/img/swiper/arrow_R.png" alt="다음">
						</div>
                    </div>
                </div>
				<?php } ?>
            </div>
            <!-- 페이지네이션 -->
            <div class="swiper-controls">
                <div class="swiper-pagination"></div>
                <button id="swiperToggleBtn">⏸ 중지</button>
            </div>
        </div>
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.7/swiper-bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const swiper = new Swiper('.banner-swiper', {
            loop: true,
            slidesPerView: 'auto',
            centeredSlides: true,
            effect: 'coverflow',
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            coverflowEffect: {
                rotate: 0,
                stretch: 0,
                depth: 150,
                modifier: 1,
                slideShadows: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    });

    let isPlaying = true;

    document.getElementById('swiperToggleBtn').addEventListener('click', function() {
        if (isPlaying) {
            swiper.autoplay.stop();
            this.textContent = '▶ 재생';
        } else {
            swiper.autoplay.start();
            this.textContent = '⏸ 중지';
        }
        isPlaying = !isPlaying;
    });
    </script>

<div class="bg pending ">
	<div class="cont_wrap">
		<div class="cont_box">
			<div class="title">이벤트 행사</div>
			<div class="row">
				<div class="text">
					놓치지 마세요! <br>
					기간한정 특별 판매 상품
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
	<!-- <div class="cont_wrap">
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
	</div> -->
</div>

<div class="bg popular ">
	<div class="cont_wrap">
		<div class="cont_box">
			<div class="title">인기상품</div>
			<div class="row">
				<div class="text">
					지금 가장 HOT한 <br>
					인기 상품들을 만나보세요
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

	<!-- <div class="cont_wrap">
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
	</div> -->
</div>


<?= $mainBan ?>

<!-- 작은 아이콘 나오는 부분 -->
<div class="cont_wrap mart80">
	<?php echo $plan ?>
	<?php if (empty($plan)) { ?>
		<div class="goods_empty">등록된 상품이 없습니다.</div>
	<?php } ?>
</div>

<div class="cont_wrap mart80">
	<div class="row">
		<div class="attention_tit marb30">이번에 새로 들어온 상품이에요</div>
		<div class="button" onclick="location.href='<?= base_url() ?>shop/timesale?status=attention'">전체보기 ></div>
	</div>

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

<!-- <div class="bg brand mart80">
	<div class="brand_tit">test1</div>
	<div class="cont_wrap brand">
		<div class="cont_box brand">
			<div class="attention_tit">test2</div>
			<div class="button" onclick="location.href='<?= base_url() ?>shop/brand'">전체보기 ></div>
		</div>

		<?php echo $brand ?>
		<?php if (empty($brand)) { ?>
			<div class="goods_empty">등록된 상품이 없습니다.</div>
		<?php } else { ?>
			<div class="slide-button brand">
				<div class="brand-button-prev"><span>
					</span>
				</div>
				<div class="brand-button-next"><span>></span></div>
			</div>
		<?php } ?>

	</div>
</div> -->
<!-- 
<div class="bg substance mart80">
	<div class="substance_tit">SUBSTANCE 실속관1</div>
	<div class="cont_wrap substance">
		<div class="cont_box substance">
			<div class="attention_tit">SUBSTANCE 실속관2</div>
			<div class="button" onclick="location.href='<?= base_url() ?>shop/cateList'">전체보기 ></div>
		</div>

		<?php echo $substance ?>
		<?php if (empty($substance)) { ?>
			<div class="goods_empty">등록된 상품이 없습니다.</div>
		<?php } else { ?>
			<div class="slide-button substance">
				<div class="substance-button-prev"><span>
					</span>
				</div>
				<div class="substance-button-next"><span>></span></div>
			</div>
		<?php } ?>

	</div>
</div> -->

<div class="cont_wrap mart80">
	<div class="row">
		<div class="attention_tit marb30">지금 가장 인기 있는 추천 상품</div>
		<div class="button" onclick="location.href='<?= base_url() ?>shop/timesale?status=active'">전체보기 ></div>
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