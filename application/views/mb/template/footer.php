</div>
	<?php if(false) { ?>
	<span class="btn_top fa fa-chevron-up"></span>
	<span class="btn_bottom fa fa-chevron-down"></span>
	<?php } ?>
    <footer id="ft">
		<ul class="ft_info">
			<li><a href="<?php echo base_url(); ?>m/bbs/provision">이용약관</a></li>
			<li><a href="<?php echo base_url(); ?>m/bbs/policy">개인정보 처리방침</a></li>
			<li><a href="javascript:void(0);" onclick="company_check();">사업자정보확인</a></li>
			<li class="last"><a href="<?php echo base_url(); ?>m/bbs/board_list?boardid=13">공지사항</a></li>
			<script type="text/javascript">
				function company_check() {
					window.open("http://www.ftc.go.kr/bizCommPop.do?wrkr_no=6058636234", "bizCommPop", "width=750, height=700;");
				}
			</script>
		</ul>
        <ul class="ft_address">
			<li>
				<span>대표자 : <?php echo $config['company_owner']; ?></span>
				<span><?php echo $config['company_addr']; ?></span>
			</li>
			<li>
				<span>사업자등록번호 : <?php echo $config['company_saupja_no']; ?></span>
				<a href="javascript:void(0);" onclick="company_check();">사업자정보확인</a>
			</li>
			<script type="text/javascript">
				function company_check() {
					window.open("http://www.ftc.go.kr/bizCommPop.do?wrkr_no=6058636234", "bizCommPop", "width=750, height=700;");
				}
			</script>
			<li>
				<span>통신판매업신고 : <?php echo $config['tongsin_no']; ?></span>
				<span class="marl15">|　고객센터 : <?php echo $config['company_tel']; ?></span>
			</li>
			<li>
				<span>FAX : <?php echo $config['company_fax']; ?></span>
				<span class="marl15">|　E-mail : <?php echo $super['email']; ?></span>
			</li>
		</ul>
		<ul class="ft_address pad">
			<li>
				주식회사 그로는 통신판매중개자이며, 통신판매의 당사자가 아닙니다. <br>
				상품, 상품정보, 거래에 관한 의무와 책임은 입점 판매자에게 있습니다.
			</li>
		</ul>
		<p class="ft_crt">
			ⓒ hallyu.kr ALL RIGHTS RESERVED
		</p>
		<ul class="ft_logo">
			<img src="<?=base_url()?>dist/img/mb/footer_logo.png" alt="logo">
		</ul>
    </footer>
</div>

<script>
$(function() {
	// 상위로이동
	$(".btn_top").click(function(){
		$("html, body").animate({ scrollTop: 0 }, 300);
	});
	// 하위로이동
    $(".btn_bottom").click(function(){
		$("html, body").animate({ scrollTop: $(document).height() }, 300);
    });

	$(window).scroll(function () {
		if($(this).scrollTop() > 0) {
			$(".btn_top, .btn_bottom").fadeIn(300);
		} else {
			$(".btn_top, .btn_bottom").fadeOut(300);
		}
	});

	// 상단메뉴 스크롤시 fixed
	var adheight = $(".top_ad").height() + $("#gnb").height();
	$(window).scroll(function () {
		if($(this).scrollTop() > adheight) {
			$("#header").addClass('active');
			$("#container").addClass('padt45');
		} else {
			$("#header").removeClass('active');
			$("#container").removeClass('padt45');
		}
	});
});
</script>
<script>
	$('.timesale_slide_active').slick({
		rows: 2,
		dots: false,
		arrows: false,
		infinite: false,
		variableWidth: true,
		// autoplay: true,
		// speed: 3000,
	});

	$('.timesale_slide_pending').slick({
		rows: 2,
		dots: false,
		arrows: false,
		infinite: false,
		variableWidth: true,
		// autoplay: true,
		// speed: 3000,
	});

	$('.plan_slide').slick({
		slide: 'div',
		dots: false,
		arrows: false,
		prevArrow: $('.attention-button-prev'), 
		nextArrow: $('.attention-button-next'), 
		infinite: false,
	});

	$('.attention_slide').slick({
		slide: 'li',
		dots: false,
		arrows: true,
		infinite: false,
		variableWidth: true,
		prevArrow: $('.attention-button-prev'), 
		nextArrow: $('.attention-button-next'), 
	});

	$('.brand_slide').slick({
		rows: 2,
		dots: false,
		arrows: false,
		infinite: false,
		variableWidth: true,
		// autoplay: true,
		// speed: 3000,
	});

	$('.substance_slide').slick({
		rows: 2,
		dots: false,
		arrows: false,
		infinite: false,
		variableWidth: true,
		// autoplay: true,
		// speed: 3000,
	});

</script>