<?= $slideMenu ?>
<div id="wrapper">
	<div onclick="history.go(-1);" class="page_cover"><span class="sm_close"></span></div>
	<div class="top_ad"><?php echo $ban1Tag; ?></div>
	<header id="header">
		<div id="m_gnb">
			<span class="btn_sidem"></span>
			<h1 class="logo"><?php echo $mlogo; ?></h1>
			<span class="btn_search"></span>
			<a href="<?php echo base_url(); ?>m/shop/wish" class="btn_like"></a>
			<a href="<?php echo base_url(); ?>m/shop/cart" class="btn_cart"><span class="ic_num"><?php echo $cart_cnt; ?></span></a>
		</div>
		<div id="hd_sch">
			<section class="normal_board_radius">
				<form name="fsearch"  id="fsearch" method="get" action="<?php echo base_url(); ?>m/shop/search" onsubmit="return fsearch_submit(this);" autocomplete="off">
					<input type="hidden" name="hash_token" value="<?php echo $OM_HASH_TOKEN; ?>">
					<input type="search"   name="ss_tx" value="<?php echo $ss_tx; ?>" class="normal_board_radius search_inp" maxlength="20">
					<input type="submit" class="normal_board_radius" value="&#xf002;" id="sch_submit">
					<input type="hidden" name="excludeSoldOut" id="excludeSoldOutHidden" value="">
					<input type="hidden" name="onlyBid" id="onlyBidHidden" value="">
				</form>
				<script>
					function fsearch_submit(f) {
						if (!f.ss_tx.value) {
							alert('검색어를 입력하세요.');
							return false;
						}
						return true;
					}
				</script>
			</section>
			<script>
				$(function() {
					// 상단의 검색버튼 누르면 검색창 보이고 끄기
					$('.btn_search').click(function() {
						if ($("#hd_sch").css('display') == 'none') {
							$("#hd_sch").slideDown('fast');
							$(this).attr('class', 'btn_search');
						} else {
							$("#hd_sch").slideUp('fast');
							$(this).attr('class', 'btn_search');
						}
					});
				});
			</script>

			<!-- <div class="m_rkw_se" id="rkw_open">
				<?php echo $popularWd; ?>
				<button type="button" class="btn_open"></button>
			</div>
			<div class="m_rkw_se" id="rkw_close" style="display:none;">
				<h2>인기검색어 순위</h2>
				<button type="button" class="btn_close"></button>
			</div>
			<div class="m_rkw_bg" style="display:none;">
				<?php echo $popularRankWd; ?>
			</div> -->

			<script>
				// 인기검색어 펼침
				$(".m_rkw_se .btn_open").click(function() {
					$("#rkw_open").hide();
					$("#rkw_close").show();
					$(".m_rkw_bg").show();
				});

				// 인기검색어 닫음
				$(".m_rkw_se .btn_close").click(function() {
					$("#rkw_open").show();
					$("#rkw_close").hide();
					$(".m_rkw_bg").hide();
				});

				// 인기검색어 롤링
				function tick() {
					$('#ticker li:first').slideUp(function() {
						$(this).appendTo($('#ticker')).slideDown();
					});
				}
				setInterval(function() {
					tick()
				}, 4000);
			</script>
		</div>
	</header>
	<div id="container" <?= $no_index_class ?>>
		<?= $no_index_tag ?>

		<div class="swiper main_category">
			<div class="swiper-wrapper">
				<?php if (!empty($category)) { ?>
					<?php foreach ($category as $key => $row) { ?>
						<div class="swiper-slide">
							<a href="<?= base_url() ?>m/<?= $row['url'] ?>" class="tab-item"><?= $row['menu_name'] ?></a>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
		<script>
			document.addEventListener('DOMContentLoaded', () => {
				// 현재 전체 경로
				const currentFull = location.pathname + location.search;

				// GET 파라미터 읽기
				const params = new URLSearchParams(location.search);
				const boardid = params.get('boardid');

				// board_read 페이지에서만 override 할 매핑
				// key: boardid, value: 해당 탭의 pathname+search (여기선 search 없이 pathname 만)
				const overrideMap = {
					'37': '/hallyu/m/bbs/goods_request_list',
					'13': '/hallyu/m/bbs/noticeList'
					// 필요한 boardid → 탭 URL 을 더 추가
				};

				// board_read 페이지 감지 (pathname 끝이 /board_read)
				const isReadPage = /\/board_read$/i.test(location.pathname);

				// overrideUrl 값 결정
				let overrideUrl = null;
				if (isReadPage && boardid && overrideMap[boardid]) {
					overrideUrl = overrideMap[boardid];
				}

				// 탭 순회하면서 active 처리
				document.querySelectorAll('.main_category .tab-item').forEach(tab => {
					const url = new URL(tab.href);
					const tabPath = url.pathname; // 예: '/hallyu/m/goods_request_list'

					if (overrideUrl) {
						// read 페이지일 땐 overrideUrl 과 일치하는 탭만 active
						if (tabPath === overrideUrl) {
							tab.classList.add('active');
						}
					} else {
						// 그 외 페이지는 기존 currentFull 매칭
						const tabFull = tabPath + url.search;
						if (tabFull === currentFull) {
							tab.classList.add('active');
						}
					}
				});
			});
		</script>
		<?= $cs_banner ?>