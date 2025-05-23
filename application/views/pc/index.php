<!-- 장바구니 -->
<script src="<?php echo base_url(); ?>js/shop.js"></script>

<!-- // 좌우 이동하는 배너 -->
<!-- <div class="bg active">
</div> -->

<!-- 마감임박 함께장터 상품 -->


<!-- 메인 배너 (Swiper) -->
<div class="main-banner">
	<div class="banner-swiper swiper-container">
		<div class="swiper-wrapper">
			<?php for ($i = 0; $i < 8; $i++) { ?>
				<div class="swiper-slide">
					<img src="<?= base_url() ?>img/banner/banner_<?= $i ?>.png" alt="배너<?= $i ?>">
				</div>
			<?php } ?>
		</div>


		<!-- 커스텀 네비게이션 -->
		<div class="swiper-button-prev"></div>
		<div class="swiper-button-next"></div>


		<!-- 페이지네이션 및 컨트롤 -->
		<div class="swiper-controls">
			<!-- <div class="swiper-pagination"></div> -->
			<div class="swiper-counter" id="swiperCounter"></div>
			<!-- <button id="swiperToggleBtn">⏸ 중지</button> -->
		</div>
	</div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.7/swiper-bundle.min.js"></script>


<div class="cont_wrap mart80">
	<div class="row">
		<div class="attention_tit marb30"><span class="timer"></span>마감임박 함께장터 상품</div>
		<div class="button" onclick="location.href='<?= base_url() ?>shop/timesale?status=active'">전체보기 ></div>
	</div>



	<div class="pr_desc">
		<ul class="timesale_slide_active">
			<div style="width: 100%; display: inline-block;">
				<div class="timesale-swiper swiper">
					<div class="swiper-wrapper">
						<?php foreach ($timesale_active_items as $item): ?>
							<div class="swiper-slide">
								<li class="timesale <?= $item['is_open'] ?>">
									<a href="<?= $item['href'] ?>">
										<dl>
											<dt><img src="<?= $item['img'] ?>" alt=""></dt>
										</dl>
									</a>
									<?= $item['tag'] ?>
									<div class="timebox">
										<div class="calendar"></div>
										<span class="date"><?= $item['open_date'] ?></span>
										<span class="time"><?= $item['open_time'] ?></span>
										<span class="dash">~</span>
										<span class="date"><?= $item['end_date'] ?></span>
										<span class="time"><?= $item['end_time'] ?></span>
									</div>
									<p class="pname"><?= $item['name'] ?></p>
									<dd class="price_box">
										<?php if ($item['normal_price']): ?>
											<p class="price"><span class="spr"><?= $item['normal_price'] ?></span></p>
										<?php endif; ?>
										<div class="price">
											<?php if ($item['sale_percent']): ?>
												<p class="sale"><?= $item['sale_percent'] ?><span class="percent">%</span></p>
											<?php endif; ?>
											<span class="mpr"><?= $item['price'] ?></span>
											<p class="ic_bx">
												<span onclick="itemlistwish('<?= $item['index_no'] ?>');" id="<?= $item['index_no'] ?>" class="<?= $item['index_no'] ?> zzim"></span>
											</p>
											<p class="ct_bx">
												<input type="hidden" name="io_type[<?= $item['index_no'] ?>][]" value="0">
												<input type="hidden" name="it_price[<?= $item['index_no'] ?>][]" value="<?= $item['goods_price'] ?>">
												<input type="hidden" name="io_id[<?= $item['index_no'] ?>][]" value="">
												<input type="hidden" name="ca_id[<?= $item['index_no'] ?>][]" value="<?= $item['ca_id'] ?>">
												<input type="hidden" name="io_value[<?= $item['index_no'] ?>][]" value="<?= $item['gname'] ?>">
												<input type="hidden" name="ct_qty[<?= $item['index_no'] ?>][]" value="1" class="inp_opt" title="수량설정" size="2">
												<input type="hidden" data-auth="<?= $item['is_soldout'] ? 'soldout' : '' ?>" class="check" name="gs_id[]" value="<?= $item['index_no'] ?>">
												<span onclick="<?= $item['onclick'] ?>"></span>
											</p>
										</div>
									</dd>
								</li>
							</div>
						<?php endforeach; ?>
					</div>
					<!-- Swiper 양옆 네비게이션 버튼 -->
					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
				</div>
			</div>
		</ul>
	</div>


	<?php if (empty($timesale_active_items)) { ?>
		<div class="goods_empty">등록된 상품이 없습니다.</div>
	<?php } else { ?>
		<div class="slide-button">
			<div class="timesale-active-button-prev">
				<span>></span>
			</div>
			<div class="timesale-active-button-next">
				<span>></span>
			</div>
		</div>
	<?php } ?>
	<div class="more_show_button" onclick="location.href='<?= base_url() ?>shop/timesale?status=active'">전체보기 ></div>

</div>

<div class="bg pending ">
	<div class="cont_wrap">
		<div class="cont_box">
			<div class="title">함께장터</div>
			<div class="row">
				<div class="text">
					놓치지 마세요! <br>
					기간한정 특별 판매 상품
				</div>

			</div>
		</div>

		<div class="category-filter">
			<button class="category-btn active">청과</button>
			<button class="category-btn">농산물</button>
			<button class="category-btn">축산물</button>
			<button class="category-btn">야채</button>
			<button class="category-btn">가공식품</button>
			<button class="category-btn">주방용품</button>
			<button class="category-btn">생활/위생용품</button>
		</div>

		<div class="pr_desc">
			<ul class="timesale_slide_active">
				<div style="width: 100%; display: inline-block;">
					<div class="timesale-swiper swiper">
						<div class="swiper-wrapper">
							<?php foreach ($timesale_active_items as $item): ?>
								<div class="swiper-slide">
									<li class="timesale <?= $item['is_open'] ?>">
										<a href="<?= $item['href'] ?>">
											<dl>
												<dt><img src="<?= $item['img'] ?>" alt=""></dt>
											</dl>
										</a>
										<?= $item['tag'] ?>
										<div class="timebox">
											<div class="calendar"></div>
											<span class="date"><?= $item['open_date'] ?></span>
											<span class="time"><?= $item['open_time'] ?></span>
											<span class="dash">~</span>
											<span class="date"><?= $item['end_date'] ?></span>
											<span class="time"><?= $item['end_time'] ?></span>
										</div>
										<p class="pname"><?= $item['name'] ?></p>
										<dd class="price_box">
											<?php if ($item['normal_price']): ?>
												<p class="price"><span class="spr"><?= $item['normal_price'] ?></span></p>
											<?php endif; ?>
											<div class="price">
												<?php if ($item['sale_percent']): ?>
													<p class="sale"><?= $item['sale_percent'] ?><span class="percent">%</span></p>
												<?php endif; ?>
												<span class="mpr"><?= $item['price'] ?></span>
												<p class="ic_bx">
													<span onclick="itemlistwish('<?= $item['index_no'] ?>');" id="<?= $item['index_no'] ?>" class="<?= $item['index_no'] ?> zzim"></span>
												</p>
												<p class="ct_bx">
													<input type="hidden" name="io_type[<?= $item['index_no'] ?>][]" value="0">
													<input type="hidden" name="it_price[<?= $item['index_no'] ?>][]" value="<?= $item['goods_price'] ?>">
													<input type="hidden" name="io_id[<?= $item['index_no'] ?>][]" value="">
													<input type="hidden" name="ca_id[<?= $item['index_no'] ?>][]" value="<?= $item['ca_id'] ?>">
													<input type="hidden" name="io_value[<?= $item['index_no'] ?>][]" value="<?= $item['gname'] ?>">
													<input type="hidden" name="ct_qty[<?= $item['index_no'] ?>][]" value="1" class="inp_opt" title="수량설정" size="2">
													<input type="hidden" data-auth="<?= $item['is_soldout'] ? 'soldout' : '' ?>" class="check" name="gs_id[]" value="<?= $item['index_no'] ?>">
													<span onclick="<?= $item['onclick'] ?>"></span>
												</p>
											</div>
										</dd>
									</li>
								</div>
							<?php endforeach; ?>
						</div>
						<!-- Swiper 양옆 네비게이션 버튼 -->
						<div class="swiper-button-prev"></div>
						<div class="swiper-button-next"></div>
					</div>
				</div>
			</ul>
		</div>

		<?php if (empty($timesale_active)) { ?>
			<div class="goods_empty">등록된 상품이 없습니다.</div>
		<?php } else { ?>
			<div class="slide-button">
				<div class="timesale-active-button-prev"></div>
				<div class="timesale-active-button-next"></div>
			</div>
		<?php } ?>
	</div>
	<div class="more_show_button" onclick="location.href='<?= base_url() ?>shop/timesale?status=active'">전체보기 ></div>
</div>

<div class="banner-wrapper">
	<img src="./data/banner/banner_04.png" alt="5월엔 4고" class="banner-img">
	<img src="./data/banner/banner_05.png" alt="함께장터 특가" class="banner-img">
</div>

<div class="cont_wrap mart80">
	<div class="row">
		<div class="attention_tit">지금 가장 인기있는 함께장터 상품이에요</div>
		<div class="button" onclick="location.href='<?= base_url() ?>shop/timesale?status=active'">전체보기 ></div>
	</div>

	<div class="category-filter">
		<button class="category-btn active">청과</button>
		<button class="category-btn">농산물</button>
		<button class="category-btn">축산물</button>
		<button class="category-btn">야채</button>
		<button class="category-btn">가공식품</button>
		<button class="category-btn">주방용품</button>
		<button class="category-btn">생활/위생용품</button>
	</div>

	<div class="pr_desc">
		<ul class="timesale_slide_active">
			<div style="width: 100%; display: inline-block;">
				<div class="timesale-swiper swiper">
					<div class="swiper-wrapper">
						<?php foreach ($timesale_active_items as $item): ?>
							<div class="swiper-slide">
								<li class="timesale <?= $item['is_open'] ?>">
									<a href="<?= $item['href'] ?>">
										<dl>
											<dt><img src="<?= $item['img'] ?>" alt=""></dt>
										</dl>
									</a>
									<?= $item['tag'] ?>
									<div class="timebox">
										<div class="calendar"></div>
										<span class="date"><?= $item['open_date'] ?></span>
										<span class="time"><?= $item['open_time'] ?></span>
										<span class="dash">~</span>
										<span class="date"><?= $item['end_date'] ?></span>
										<span class="time"><?= $item['end_time'] ?></span>
									</div>
									<p class="pname"><?= $item['name'] ?></p>
									<dd class="price_box">
										<?php if ($item['normal_price']): ?>
											<p class="price"><span class="spr"><?= $item['normal_price'] ?></span></p>
										<?php endif; ?>
										<div class="price">
											<?php if ($item['sale_percent']): ?>
												<p class="sale"><?= $item['sale_percent'] ?><span class="percent">%</span></p>
											<?php endif; ?>
											<span class="mpr"><?= $item['price'] ?></span>
											<p class="ic_bx">
												<span onclick="itemlistwish('<?= $item['index_no'] ?>');" id="<?= $item['index_no'] ?>" class="<?= $item['index_no'] ?> zzim"></span>
											</p>
											<p class="ct_bx">
												<input type="hidden" name="io_type[<?= $item['index_no'] ?>][]" value="0">
												<input type="hidden" name="it_price[<?= $item['index_no'] ?>][]" value="<?= $item['goods_price'] ?>">
												<input type="hidden" name="io_id[<?= $item['index_no'] ?>][]" value="">
												<input type="hidden" name="ca_id[<?= $item['index_no'] ?>][]" value="<?= $item['ca_id'] ?>">
												<input type="hidden" name="io_value[<?= $item['index_no'] ?>][]" value="<?= $item['gname'] ?>">
												<input type="hidden" name="ct_qty[<?= $item['index_no'] ?>][]" value="1" class="inp_opt" title="수량설정" size="2">
												<input type="hidden" data-auth="<?= $item['is_soldout'] ? 'soldout' : '' ?>" class="check" name="gs_id[]" value="<?= $item['index_no'] ?>">
												<span onclick="<?= $item['onclick'] ?>"></span>
											</p>
										</div>
									</dd>
								</li>
							</div>
						<?php endforeach; ?>
					</div>
					<!-- Swiper 양옆 네비게이션 버튼 -->
					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
				</div>
			</div>
		</ul>
	</div>


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
	<div class="more_show_button" onclick="location.href='<?= base_url() ?>shop/timesale?status=active'">전체보기 ></div>
</div>

<div class="bg popular ">
	<div class="cont_wrap">
		<div class="cont_box">
			<div class="title">열린장터</div>
			<div class="row">
				<div class="text">
					지금 가장 HOT한 <br>
					인기 상품들을 만나보세요
				</div>
			</div>
		</div>

		<div class="category-filter">
			<button class="category-btn active">청과</button>
			<button class="category-btn">농산물</button>
			<button class="category-btn">축산물</button>
			<button class="category-btn">야채</button>
			<button class="category-btn">가공식품</button>
			<button class="category-btn">주방용품</button>
			<button class="category-btn">생활/위생용품</button>
		</div>


		<div class="pr_desc">
			<ul class="timesale_slide_active">
				<div style="width: 100%; display: inline-block;">
					<div class="open_market swiper">
						<div class="swiper-wrapper">
							<?php foreach ($timesale_active_items as $item): ?>
								<div class="swiper-slide">
									<li class="timesale <?= $item['is_open'] ?>">
										<a href="<?= $item['href'] ?>">
											<dl>
												<dt><img src="<?= $item['img'] ?>" alt=""></dt>
											</dl>
										</a>
										<?= $item['tag'] ?>
										<div class="timebox">
											<div class="calendar"></div>
											<span class="date"><?= $item['open_date'] ?></span>
											<span class="time"><?= $item['open_time'] ?></span>
											<span class="dash">~</span>
											<span class="date"><?= $item['end_date'] ?></span>
											<span class="time"><?= $item['end_time'] ?></span>
										</div>
										<p class="pname"><?= $item['name'] ?></p>
										<dd class="price_box">
											<?php if ($item['normal_price']): ?>
												<p class="price"><span class="spr"><?= $item['normal_price'] ?></span></p>
											<?php endif; ?>
											<div class="price">
												<?php if ($item['sale_percent']): ?>
													<p class="sale"><?= $item['sale_percent'] ?><span class="percent">%</span></p>
												<?php endif; ?>
												<span class="mpr"><?= $item['price'] ?></span>
												<p class="ic_bx">
													<span onclick="itemlistwish('<?= $item['index_no'] ?>');" id="<?= $item['index_no'] ?>" class="<?= $item['index_no'] ?> zzim"></span>
												</p>
												<p class="ct_bx">
													<input type="hidden" name="io_type[<?= $item['index_no'] ?>][]" value="0">
													<input type="hidden" name="it_price[<?= $item['index_no'] ?>][]" value="<?= $item['goods_price'] ?>">
													<input type="hidden" name="io_id[<?= $item['index_no'] ?>][]" value="">
													<input type="hidden" name="ca_id[<?= $item['index_no'] ?>][]" value="<?= $item['ca_id'] ?>">
													<input type="hidden" name="io_value[<?= $item['index_no'] ?>][]" value="<?= $item['gname'] ?>">
													<input type="hidden" name="ct_qty[<?= $item['index_no'] ?>][]" value="1" class="inp_opt" title="수량설정" size="2">
													<input type="hidden" data-auth="<?= $item['is_soldout'] ? 'soldout' : '' ?>" class="check" name="gs_id[]" value="<?= $item['index_no'] ?>">
													<span onclick="<?= $item['onclick'] ?>"></span>
												</p>
											</div>
										</dd>
									</li>
								</div>
							<?php endforeach; ?>
						</div>
						<!-- Swiper 양옆 네비게이션 버튼 -->
						<div class="swiper-button-prev"></div>
						<div class="swiper-button-next"></div>
					</div>
				</div>
			</ul>
		</div>

		<?php if (empty($timesale_active)) { ?>
			<div class="goods_empty">등록된 상품이 없습니다.</div>
		<?php } else { ?>
			<div class="slide-button">
				<div class="timesale-active-button-prev"><span>
					</span>
				</div>
				<div class="timesale-active-button-next"><span>></span></div>
			</div>
		<?php } ?>
		<div class="more_show_button" onclick="location.href='<?= base_url() ?>shop/timesale?status=active'">전체보기 ></div>
	</div>

	<div class="footer-top">
		<!-- 고객센터 -->
		<div class="footer-box">
			<h3 class="footer-title">고객센터</h3>
			<p class="footer-phone">1522-8920</p>
			<div class="footer-timebox">
				<p class="footer-time-label">상담시간 : 오전 10시 ~ 오후 06시</p>
				<p class="footer-time">점심시간 : 오전 11시 30분 ~ 오후 12시 30분</p>
			</div>
		</div>

		<!-- 공지사항 -->
		<div class="footer-box">
			<div class="footer-title-row">
				<h3 class="footer-title">공지사항</h3>
				<a href="#" class="footer-more">더보기 <span class="plus">+</span></a>
			</div>
			<ul class="footer-list">
				<li>
					<a href="#" class="footer-post-title">[공지] 2025년 05월 16일 공지사항입니다...ssssssssssssssssssssssssssssssssssssssssssss</a>
					<span class="footer-date">2025.05.16</span>
				</li>
				<li>
					<a href="#" class="footer-post-title">[이벤트] 2025년 05월 16일 이벤트입니다...ssssssssssssssssssssssssssssssssssssssssssss</a>
					<span class="footer-date">2025.05.16</span>
				</li>
				<li>
					<a href="#" class="footer-post-title">[공지] 2025년 05월 16일 공지사항입니다...ssssssssssssssssssssssssssssssssssssssssssss</a>
					<span class="footer-date">2025.05.16</span>
				</li>
				<li>
					<a href="#" class="footer-post-title">[이벤트] 2025년 05월 16일 이벤트입니다...ssssssssssssssssssssssssssssssssssssssssssss</a>
					<span class="footer-date">2025.05.16</span>
				</li>
			</ul>
		</div>

		<!-- 상품요청 -->
		<div class="footer-box">
			<div class="footer-title-row">
				<h3 class="footer-title">상품요청게시판</h3>
				<a href="#" class="footer-more">더보기 <span class="plus">+</span></a>
			</div>
			<ul class="footer-list">
				<li>

					<a href="#" class="footer-post-title">청포도 3kg 대량 입고 요청합니다...</a>

					<span class="footer-date">2025.05.16</span>
				</li>
				<li>
					<a href="#" class="footer-post-title">30개입 2MOQ로 요청드려요...</a>
					<span class="footer-date">2025.05.16</span>
				</li>
			</ul>
		</div>
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


<!-- <?= $mainBan ?> -->

<!-- 작은 아이콘 나오는 부분 -->
<!-- <div class="cont_wrap mart80">
	<?php echo $plan ?>
	<?php if (empty($plan)) { ?>
		<div class="goods_empty">등록된 상품이 없습니다.</div>
	<?php } ?>
</div> -->

<!-- <div class="cont_wrap mart80">
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

</div> -->

<!-- <div class="cont_wrap">
	<div class="wide_bn mart40"><?= $miniBan ?></div>
</div> -->

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



<!-- <script>
	$('.timesale_slide_active').slick({
		slidesToShow: 4,
		slidesToScroll: 1,
		infinite: false,
		dots: true,
		arrows: true
	});
</script> -->

<script>
	document.querySelectorAll('.category-btn').forEach(btn => {
		btn.addEventListener('click', function() {
			document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
			this.classList.add('active');
		});
	});

	document.addEventListener("DOMContentLoaded", function() {

		const timesaleSwiper = new Swiper(".timesale-swiper", {
			loop: true,
			slidesPerView: 4,
			spaceBetween: 30,
			navigation: {
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
			},
		});

		const open_market = new Swiper(".open_market", {
			loop: true,
			slidesPerView: 8,
			spaceBetween: 30,
			navigation: {
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
			},
		});

		const swiper = new Swiper(".banner-swiper", {
			loop: true,
			slidesPerView: "auto",
			centeredSlides: true,
			effect: "coverflow",
			autoplay: {
				delay: 5000,
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
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
			},
			// pagination: {
			// 	el: ".swiper-pagination",
			// 	clickable: true,
			// },
			on: {
				init: function() {
					updateCounter(this);
				},
				slideChange: function() {
					updateCounter(this);
				},
			},
		});

		// let isPlaying = true;
		// document.getElementById("swiperToggleBtn").addEventListener("click", function() {
		// 	if (isPlaying) {
		// 		swiper.autoplay.stop();
		// 		this.textContent = "▶ 재생";
		// 	} else {
		// 		swiper.autoplay.start();
		// 		this.textContent = "⏸ 중지";
		// 	}
		// 	isPlaying = !isPlaying;
		// });

		function updateCounter(swiperInstance) {
			const totalSlides = swiperInstance.slides.length - (swiperInstance.loopedSlides * 2); // loop일 경우 실제 슬라이드 수 보정
			const currentIndex = (swiperInstance.realIndex || 0) + 1;
			document.getElementById("swiperCounter").textContent = `${currentIndex} / ${totalSlides}`;
		}
	});

	// const timesaleSwiper = new Swiper('.timesale-swiper', {
	// 	loop: false, // 반복 안 할 경우 false
	// 	slidesPerView: 4,
	// 	slidesPerGroup: 1,
	// 	spaceBetween: 20,
	// 	navigation: {
	// 		nextEl: '.swiper-button-next',
	// 		prevEl: '.swiper-button-prev'
	// 	}
	// });
</script>