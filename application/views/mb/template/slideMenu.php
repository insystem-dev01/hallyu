<div id="slideMenu">
	<dl class="top_btn">
		<dd>
			<?=$logTag?>
		</dd>
		<dd>
			<?=$joinTag?>
		</dd>
	</dl>
    <ul class="sm_icbt">
		<li><a href="<?php echo base_url(); ?>m/shop/cart"><i class="ionicons ion-android-cart"></i> 장바구니</a></li>
		<li><a href="<?php echo base_url(); ?>m/shop/orderinquiry"><i class="ionicons ion-ios-list-outline"></i> 주문/배송</a></li>
		<li><a href="<?php echo base_url(); ?>m/shop/review"><i class="ionicons ion-android-camera"></i> 구매후기</a></li>
		<li><a href="<?php echo base_url(); ?>m/shop/mypage"><i class="ionicons ion-android-contact"></i> 마이페이지</a></li>
	</ul>
    <ul class="smtab">
		<li data-tab="shop_cate">카테고리</li>
		<li data-tab="custom">고객센터</li>
		<li data-tab="mypage">마이페이지</li>
	</ul>
    <div id="shop_cate" class="sm_body">
        <?=$cateTag?>
    </div>
    <div id="custom" class="sm_body">
		<ul>
            <?=$customTag?>
			<li><a href="<?php echo base_url(); ?>m/bbs/qna_list">1:1 상담문의</a></li>
			<li><a href="<?php echo base_url(); ?>m/bbs/faq">자주묻는 질문</a></li>
        </ul>
	</div>
    <div id="mypage" class="sm_body">
		<ul>
            <li><a href="<?php echo base_url(); ?>m/shop/orderinquiry">주문/배송조회</a></li>
			<li><a href="<?php echo base_url(); ?>m/shop/review">구매후기</a></li>
			<!-- <li><a href="<?php echo base_url(); ?>m/shop/point">포인트조회</a></li> -->
            <!-- <?=$giftTag?> -->
            <!-- <?=$couponTag?> -->
            <li><a href="<?php echo base_url(); ?>m/shop/wish">찜한상품</a></li>
			<li><a href="<?php echo base_url(); ?>m/shop/today">최근 본 상품</a></li>
            <?=$exitTag?>
        </ul>
	</div>
	<dl class="sm_cs">
		<dt>고객센터</dt>
		<dd class="cs_tel"><?php echo $config['company_tel']; ?></dd>
		<dd>상담 : <?php echo $config['company_hours']; ?> (<?php echo $config['company_close']; ?>)</dd>
		<dd>점심 : <?php echo $config['company_lunch']; ?></dd>
	</dl>
    <!-- <dl class="sm_cs">
		<dt>입금계좌안내</dt>
		<dd><?php echo $bank[0]['name']; ?> <b><?php echo $bank[0]['account']; ?></b></dd>
		<dd>예금주명 : <?php echo $bank[0]['holder']; ?></dd>
	</dl> -->
    <p class="mart20"><a href="tel:<?php echo $config['company_tel']; ?>" class="btn_medium grey wfull">고객센터 전화연결</a></p>
</div>
<script>
$(function(){
	// 왼쪽 슬라이드메뉴의 서브메뉴 동작
	$('#slideMenu .subm').hide();
	$('#slideMenu .bm').click(function(){
		if($(this).hasClass('active')){
			$(this).next().slideUp(250);
			$(this).removeClass('active');
		} else {
			$('#slideMenu .bm').removeClass('active');
			$('#slideMenu .subm').slideUp(250);
			$(this).addClass('active');
			$(this).next().slideDown(250);
		}
	});

	// 상단 메뉴버튼 클릭시 메뉴페이지 슬라이드
	$(".btn_sidem").click(function () {
		$("#slideMenu, #wrapper, .page_cover, html").addClass("m_open");
		window.location.hash = "#Menu";
		$("#wrapper, html").css({
			height: $(window).height()
		});
	});
	window.onhashchange = function () {
		if(location.hash != "#Menu") {
			$("#slideMenu, #wrapper, .page_cover, html").removeClass("m_open");
			$("#wrapper, html").css({
				height:'100%'
			});
		}
	};

	//탭기능
	$(document).ready(function(){
		$(".smtab>li:eq(0)").addClass('active');
		$("#shop_cate").addClass('active');

		$(".smtab>li").click(function() {
			var activeTab = $(this).attr('data-tab');
			$(".smtab>li").removeClass('active');
			$(".sm_body").removeClass('active');
			$(this).addClass('active');
			$("#"+activeTab).addClass('active');
		});
	});
});
</script>