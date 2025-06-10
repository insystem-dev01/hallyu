<!-- 장바구니 -->
<script src="<?php echo base_url(); ?>mb/js/shop.js"></script>

<!-- 메인 배너 (Swiper) -->
<div class="main-banner">
	<div class="banner-swiper swiper-container">
		<div class="swiper-wrapper">
			<?php for ($i = 0; $i < 5; $i++) { ?>
				<div class="swiper-slide">
					<img src="<?= base_url() ?>img/banner/banner_<?= $i ?>_m.png" alt="배너<?= $i ?>">
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


<div class="cont_wrap mart30">
	<div class="row marb10">
		<div class="attention_tit "><span class="timer"></span>마감임박 함께장터 상품</div>
		<div class="button" onclick="location.href='<?= base_url() ?>m/shop/timesale?status=active'">전체보기 ></div>
	</div>



	<div class="pr_desc">
		<ul class="timesale_slide_active">
			<div style="width: 100%; display: block;">
				<div class="timesale-swiper swiper" id="limited-market">
					<div class="swiper-wrapper">
						<?php if (! empty($limited_timesale_active_items)): ?>
							<?php foreach ($limited_timesale_active_items as $item): ?>
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
						<?php else: ?>
							<!-- 아이템이 아무것도 없을 때 -->
							<div class="goods_empty">등록된 상품이 없습니다.</div>
						<?php endif; ?>
					</div>
					<!-- Swiper 양옆 네비게이션 버튼 -->
					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
				</div>
			</div>
		</ul>
	</div>

	<div class="more_show_button" onclick="location.href='<?= base_url() ?>m/shop/timesale?status=active'">전체보기 ></div>

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

		<!-- 함께장터 카테고리 -->
		<div class="together_category_filter" id="together_market_filter">
			<?php foreach ($category_list as $index => $category): ?>
				<button class="together_category_btn <?= ($index === 0) ? 'active' : '' ?>"
					id="together_category:<?= htmlspecialchars($category['catecode'], ENT_QUOTES, 'UTF-8') ?>">
					<?= htmlspecialchars($category['catename'], ENT_QUOTES, 'UTF-8') ?>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="pr_desc">
			<ul class="timesale_slide_active">
				<div style="width: 100%; display: inline-block;">
					<div class="timesale-swiper swiper" id="together-market">
						<div class="swiper-wrapper">
							<?php foreach ($normal_timesale_active_items as $item): ?>
								<div class="swiper-slide"
									id="together_items:<?= htmlspecialchars($item['ca_id'], ENT_QUOTES, 'UTF-8') ?>">
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

		<?php if (empty($normal_timesale_active_items)) { ?>
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

<!-- //서브 배너 수정해야함 -->
<div class="main-banner">
	<div class="banner-swiper swiper-container">
		<div class="swiper-wrapper">
			<?php for ($i = 0; $i < 5; $i++) { ?>
				<div class="swiper-slide">
					<img src="<?= base_url() ?>img/banner/banner_<?= $i ?>_m.png" alt="배너<?= $i ?>">
				</div>
			<?php } ?>
		</div>


		<!-- 커스텀 네비게이션 -->
		<div class="swiper-button-prev"></div>
		<div class="swiper-button-next"></div>


		<!-- 페이지네이션 및 컨트롤 -->
		<div class="swiper-controls">
			<!-- <div class="swiper-pagination"></div> -->
			<div class="swiper-counter" id="swiperCounter2"></div>
			<!-- <button id="swiperToggleBtn">⏸ 중지</button> -->
		</div>
	</div>
</div>

<!-- 인기상품 -->
<div class="cont_wrap mart30">
	<div class="row">
		<div class="attention_tit">지금 가장 인기있는 함께장터 상품이에요</div>
		<div class="button" onclick="location.href='<?= base_url() ?>m/shop/timesale?status=active'">전체보기 ></div>
	</div>

	<!-- 인기상품 함께장터 -->
	<div class="hottest_category_filter" id="hottest_market_filter">
		<?php foreach ($category_list as $index => $category): ?>
			<button class="hottest_category_btn <?= ($index == 0) ? 'active' : '' ?>"
				id="hottest_category:<?= htmlspecialchars($category['catecode'], ENT_QUOTES, 'UTF-8') ?>">
				<?= htmlspecialchars($category['catename'], ENT_QUOTES, 'UTF-8') ?>

			</button>
		<?php endforeach; ?>
	</div>

	<div class="pr_desc">
		<ul class="timesale_slide_active">
			<div style="width: 100%; display: inline-block;">
				<div class="timesale-swiper swiper" id="hottest-market">
					<div class="swiper-wrapper">
						<?php foreach ($hottest_timesale_active_items as $item): ?>
							<div class="swiper-slide"
								id="hottest_items:<?= htmlspecialchars($item['ca_id'], ENT_QUOTES, 'UTF-8') ?>">
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


	<?php if (empty($hottest_timesale_active_items)) { ?>
		<div class="goods_empty">등록된 상품이 없습니다.</div>
	<?php } else { ?>
		<div class="slide-button">
			<div class="timesale-active-button-prev"><span>
					<< /span>
			</div>
			<div class="timesale-active-button-next"><span>></span></div>
		</div>
	<?php } ?>
	<div class="more_show_button" onclick="location.href='<?= base_url() ?>m/shop/timesale?status=active'">전체보기 ></div>
</div>

<!-- 열린장터 -->
<div class="bg substance ">
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

		<div class="open_category_filter" id="open_market_filter">
			<?php foreach ($category_list as $index => $category): ?>
				<button class="open_category_btn <?= ($index === 0) ? 'active' : '' ?>"
					id="open_category:<?= htmlspecialchars($category['catecode'], ENT_QUOTES, 'UTF-8') ?>">
					<?= htmlspecialchars($category['catename'], ENT_QUOTES, 'UTF-8') ?>
				</button>
			<?php endforeach; ?>
		</div>


		<div class="pr_desc">
			<ul class="timesale_slide_active">
				<div style="width: 100%; display: inline-block;">
					<div class="open_market swiper">
						<div class="swiper-wrapper">
							<?php if (! empty($substance_items)): ?>
								<?php foreach ($substance_items as $item): ?>
									<div class="swiper-slide"
										id="open_items:<?= htmlspecialchars($item['ca_id'], ENT_QUOTES, 'UTF-8') ?>">
										<!--
                                    클래스 이름을 "timesale" 에서 "substance" 로 바꿔도 되고,
                                    기존 CSS를 재활용하려면 timesale 클래스를 그대로 두셔도 됩니다.
                                    여기서는 “substance_item” 이라는 클래스를 추가한 예시입니다.
                                -->
										<li class="timesale <?= ($item['is_soldout'] ? 'soldout' : '') ?>">
											<!-- 상품 상세 페이지 링크 -->
											<a href="<?= $item['href'] ?>">
												<dl>
													<dt>
														<img src="<?= $item['img'] ?>" alt="<?= htmlspecialchars($item['name']) ?>">
													</dt>
												</dl>
											</a>

											<!-- (선택 사항) 품절 처리 표시 -->
											<?php if ($item['is_soldout']): ?>
												<div class="soldout-badge">품절</div>
											<?php endif; ?>

											<!-- 상품명 -->
											<p class="pname"><?= htmlspecialchars($item['name']) ?></p>

											<!-- 가격 박스 -->
											<dd class="price_box">
												<!-- 취소선으로 표시할 정상가가 있으면 출력 -->
												<?php if (! empty($item['normal_price'])): ?>
													<p class="price"><span class="spr"><?= $item['normal_price'] ?></span></p>
												<?php endif; ?>

												<div class="price">
													<!-- 할인율이 있으면 % 표시 -->
													<?php if (! empty($item['sale_percent'])): ?>
														<p class="sale"><?= $item['sale_percent'] ?><span class="percent">%</span></p>
													<?php endif; ?>

													<!-- 실제 판매가 -->
													<span class="mpr"><?= $item['price'] ?></span>

													<!-- 찜 버튼 -->
													<p class="ic_bx">
														<span
															onclick="itemlistwish('<?= $item['index_no'] ?>');"
															id="<?= $item['index_no'] ?>"
															class="<?= $item['index_no'] ?> zzim">
														</span>
													</p>

													<!-- 장바구니 / 바로구매 버튼 -->
													<p class="ct_bx">
														<input type="hidden" name="io_type[<?= $item['index_no'] ?>][]" value="0">
														<input type="hidden" name="it_price[<?= $item['index_no'] ?>][]" value="<?= $item['goods_price'] ?>">
														<input type="hidden" name="io_id[<?= $item['index_no'] ?>][]" value="">
														<input type="hidden" name="ca_id[<?= $item['index_no'] ?>][]" value="<?= $item['ca_id'] ?>">
														<input type="hidden" name="io_value[<?= $item['index_no'] ?>][]" value="<?= htmlspecialchars($item['gname']) ?>">
														<input type="hidden" name="ct_qty[<?= $item['index_no'] ?>][]" value="1" class="inp_opt" title="수량설정" size="2">
														<input
															type="hidden"
															data-auth="<?= ($item['is_soldout'] ? 'soldout' : '') ?>"
															class="check"
															name="gs_id[]"
															value="<?= $item['index_no'] ?>">
														<!-- 클릭 시 실행할 JS 함수 or 상세페이지로 이동 -->
														<span onclick="<?= $item['onclick'] ?>"></span>
													</p>
												</div>
											</dd>
										</li>
									</div>
								<?php endforeach; ?>
							<?php else: ?>
								<!-- 아이템이 아무것도 없을 때 -->
								<div class="goods_empty">등록된 상품이 없습니다.</div>
							<?php endif; ?>
						</div>
						<!-- Swiper 양옆 네비게이션 버튼 -->
						<div class="swiper-button-prev"></div>
						<div class="swiper-button-next"></div>
					</div>
				</div>
			</ul>
		</div>

		<?php if (empty($substance_items)) { ?>
			<div class="goods_empty">등록된 상품이 없습니다.</div>
		<?php } else { ?>
			<div class="slide-button">
				<div class="timesale-active-button-prev">
					<span>
						<< /span>
				</div>
				<div class="timesale-active-button-next"><span>></span></div>
			</div>
		<?php } ?>


		<div class="more_show_button" onclick="location.href='<?= base_url() ?>m/shop/normal'">전체보기 ></div>
	</div>
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
			<a href="<?php echo base_url(); ?>m/bbs/noticeList?boardid=13" class="footer-more">더보기 <span class="plus">+</span></a>
		</div>
		<ul class="footer-list">
			<?php if (!empty($bottom_notice_list)): ?>
				<?php foreach ($bottom_notice_list as $item): ?>
					<li>
						<a href="<?= $item['url']; ?>" class="footer-post-title">
							<?= htmlspecialchars($item['title']); ?>
						</a>
						<span class="footer-date"><?= $item['date']; ?></span>
					</li>
				<?php endforeach; ?>
			<?php else: ?>
				<li>공지사항이 없습니다.</li>
			<?php endif; ?>
		</ul>
	</div>

	<!-- 상품요청 -->
	<div class="footer-box">
		<div class="footer-title-row">
			<h3 class="footer-title">상품요청게시판</h3>
			<a href="<?php echo base_url(); ?>m/bbs/goods_request_list?boardid=37" class="footer-more">더보기 <span class="plus">+</span></a>
		</div>
		<ul class="footer-list">
			<?php if (!empty($bottom_request_goods_list)): ?>
				<?php foreach ($bottom_request_goods_list as $item): ?>
					<li>
						<a href="<?= $item['url']; ?>" class="footer-post-title">
							<?= htmlspecialchars($item['title']); ?>
						</a>
						<span class="footer-date"><?= $item['date']; ?></span>
					</li>
				<?php endforeach; ?>
			<?php else: ?>
				<li>상품요청이 없습니다.</li>
			<?php endif; ?>
		</ul>
	</div>
</div>

<script>
	// 1) '함께장터' 그룹 (.together_category_btn)
	document.querySelectorAll('.together_category_btn').forEach(btn => {
		btn.addEventListener('click', function() {
			// 같은 그룹(.together_category_btn) 내 모든 active 제거
			document.querySelectorAll('.together_category_btn').forEach(b => b.classList.remove('active'));
			// 클릭된 버튼에만 active 추가
			this.classList.add('active');
		});
	});

	// 2) '인기상품' 그룹 (.hottest_category_btn)
	document.querySelectorAll('.hottest_category_btn').forEach(btn => {
		btn.addEventListener('click', function() {
			document.querySelectorAll('.hottest_category_btn').forEach(b => b.classList.remove('active'));
			this.classList.add('active');
		});
	});

	// 3) '열린장터' 그룹 (.open_category_btn)
	document.querySelectorAll('.open_category_btn').forEach(btn => {
		btn.addEventListener('click', function() {
			document.querySelectorAll('.open_category_btn').forEach(b => b.classList.remove('active'));
			this.classList.add('active');
		});
	});

	document.addEventListener("DOMContentLoaded", function() {



		const limited_market = new Swiper("#limited-market", {
			loop: false,
			slidesPerView: 2,
			spaceBetween: 10,
			navigation: {
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
			},

		});

		const together_market = new Swiper("#together-market", {
			loop: false,
			slidesPerView: 2,
			spaceBetween: 5,
			navigation: {
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
			},
		});

		const hottest_market = new Swiper("#hottest-market", {
			loop: false,
			slidesPerView: 2,
			spaceBetween: 5,
			navigation: {
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
			},
		});

		let open_market = new Swiper(".open_market", {
			loop: false,
			slidesPerView: 2,
			spaceBetween: 5,
			// grid: {
			// 	rows: 2,
			// 	fill: "row",
			// },
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


		function updateCounter(swiperInstance) {
			const totalSlides = swiperInstance.slides.length - (swiperInstance.loopedSlides * 2); // loop일 경우 실제 슬라이드 수 보정
			const currentIndex = (swiperInstance.realIndex || 0) + 1;
			document.getElementById("swiperCounter").textContent = `${currentIndex} / ${totalSlides}`;
		}

		function updateCounter2(swiperInstance) {
			const totalSlides = swiperInstance.slides.length - (swiperInstance.loopedSlides * 2); // loop일 경우 실제 슬라이드 수 보정
			const currentIndex = (swiperInstance.realIndex || 0) + 1;
			document.getElementById("swiperCounter2").textContent = `${currentIndex} / ${totalSlides}`;
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

	$(function() {
		// 클릭 시 active 토글 후 필터링
		$('.open_category_btn').on('click', function() {
			$('.open_category_btn').removeClass('active');
			$(this).addClass('active');
			openfilterItems();
		});

		$('.hottest_category_btn').on('click', function() {
			$('.hottest_category_btn').removeClass('active');
			$(this).addClass('active');
			hottestfilterItems();
		});

		$('.together_category_btn').on('click', function() {
			$('.together_category_btn').removeClass('active');
			$(this).addClass('active');
			togetherfilterItems();
		});

		function openfilterItems() {
			const activeBtn = document.querySelector('.open_category_btn.active');
			if (!activeBtn) return;

			// 클릭된 버튼의 카테고리 코드 (예: "003")
			const categoryCode = activeBtn.id.split(':')[1];

			document.querySelectorAll('[id^="open_items:"]').forEach(itemEl => {
				// 예: itemEl.id === "open_items:004005"
				const fullItemCode = itemEl.id.split(':')[1]; // "004005"

				// 끝에서 3자리만 뽑아낸다
				const first_Three = fullItemCode.slice(0, 3); // "005"

				// 만약 슬라이드의 끝 3자리 == 카테고리 코드라면 보이고, 아니면 숨긴다
				if (first_Three === categoryCode) {
					itemEl.style.display = '';
				} else {
					itemEl.style.display = 'none';
				}
			});
		}

		function hottestfilterItems() {
			const activeBtn = document.querySelector('.hottest_category_btn.active');
			if (!activeBtn) return;

			// 클릭된 버튼의 카테고리 코드 (예: "003")
			const categoryCode = activeBtn.id.split(':')[1];

			document.querySelectorAll('[id^="hottest_items:"]').forEach(itemEl => {
				// 예: itemEl.id === "open_items:004005"
				const fullItemCode = itemEl.id.split(':')[1]; // "004005"

				// 끝에서 3자리만 뽑아낸다
				const first_Three = fullItemCode.slice(0, 3); // "005"

				// 만약 슬라이드의 끝 3자리 == 카테고리 코드라면 보이고, 아니면 숨긴다
				if (first_Three === categoryCode) {
					itemEl.style.display = '';
				} else {
					itemEl.style.display = 'none';
				}
			});
		}

		function togetherfilterItems() {
			const activeBtn = document.querySelector('.together_category_btn.active');
			if (!activeBtn) return;

			// 클릭된 버튼의 카테고리 코드 (예: "003")
			const categoryCode = activeBtn.id.split(':')[1];

			document.querySelectorAll('[id^="together_items:"]').forEach(itemEl => {
				// 예: itemEl.id === "open_items:004005"
				const fullItemCode = itemEl.id.split(':')[1]; // "004005"

				// 끝에서 3자리만 뽑아낸다
				const first_Three = fullItemCode.slice(0, 3); // "005"

				// 만약 슬라이드의 끝 3자리 == 카테고리 코드라면 보이고, 아니면 숨긴다
				if (first_Three === categoryCode) {
					itemEl.style.display = '';
				} else {
					itemEl.style.display = 'none';
				}
			});
		}


		// 최초 로드 시에도 필터링 한 번
		openfilterItems();
		togetherfilterItems();
		hottestfilterItems();
	});

	function load_category_items(category_code) {

		var base_url = document.getElementById('base_url').value;
		$.ajax({
			url: base_url + 'main/ajax_load_category_items',
			type: "POST",
			data: {
				cate: category_code
			},
			async: true,
			success: function(res) {
				$(".open_market .swiper-wrapper").html(res);
				openMarketSwiper.update(); // 슬라이드 다시 계산
			},
			error: function() {
				alert("불러오기 실패");
			}
		});
	}
</script>