<?=$closeDiv?>
</div>
<!-- 카피라이터 시작 { -->
    <div id="ft">
        <div class="fgnb">
            <ul>
				<li><a href="<?php echo base_url(); ?>bbs/provision">이용약관</a></li>
				<li><a href="<?php echo base_url(); ?>bbs/policy">개인정보처리방침</a></li>
				<li><a href="javascript:void(0);" onclick="company_check();">사업자정보확인</a></li>
				<li class="last"><a href="<?php echo base_url(); ?>bbs/noticeList?boardid=13">공지사항</a></li>
				<!-- <li><a href="<?php echo base_url(); ?>bbs/register?type=seller">입점사 가입</a></li> -->
				<script type="text/javascript">
					function company_check() {
						window.open("http://www.ftc.go.kr/bizCommPop.do?wrkr_no=6058636234", "bizCommPop", "width=750, height=700;");
					}
				</script>
            </ul>
		</div>
        <div class="company">
			<ul>
				<li>
					<?php echo $config['company_name']; ?>
					<span class="g_hl"></span> 대표자 : <?php echo $config['company_owner']; ?>
					<span class="g_hl"></span> <?php echo $config['company_addr']; ?><br>
					사업자등록번호 : <?php echo $config['company_saupja_no']; ?>
					<a  href="javascript:saupjaonopen('<?php echo conv_number($config['company_saupja_no']); ?>');" class="company_btn">사업자정보확인</a>
					<span class="g_hl"></span> 통신판매업신고 : <?php echo $config['tongsin_no']; ?><br>
					고객센터 : <?php echo $config['company_tel']; ?>
					<span class="g_hl"></span> FAX : <?php echo $config['company_fax']; ?>
					<span class="g_hl"></span> Email : <?php echo $config['info_email']; ?><br>
					개인정보보호책임자 : <?php echo $config['info_name']; ?> (<?php echo $config['info_email']; ?>) <br>
					<p class="copy">
						주식회사 그로는 통신판매중개자이며, 통신판매의 당사자가 아닙니다.
						상품, 상품정보, 거래에 관한 의무와 책임은 입점 판매자에게 있습니다. <br>
						ⓒ hallyu.kr ALL RIGHTS RESERVED
					</p>
				</li>
				<div class="logo"></div>
			</ul>
		</div>
    </div>
    <script>
	function escrow_foot_check(){
        <?=$escrow_js?>
    }
	</script>
    <!-- } 카피라이터 끝 -->
</div>
<?=$mobile_but?>
<?=$tailScript?>

<script>
	$('.timesale_slide_active').slick({
		slide: 'div',
		dots: true,
		arrows: true,
		rows: 2,
		prevArrow: $('.timesale-active-button-prev'), 
		nextArrow: $('.timesale-active-button-next'), 
		infinite: false,
		customPaging : function(slider, index) { 
			var num = index + 1;
			return '<span class="dot">'+ num +'</span>';
		}
	});

	$('.timesale_slide_pending').slick({
		slide: 'div',
		dots: true,
		arrows: true,
		rows: 2,
		prevArrow: $('.timesale-pending-button-prev'), 
		nextArrow: $('.timesale-pending-button-next'), 
		infinite: false,
		customPaging : function(slider, index) { 
			var num = index + 1;
			return '<span class="dot">'+ num +'</span>';
		}
	});

	$('.plan_slide').slick({
		slide: 'div',
		variableWidth: true,
		dots: false,
		arrows: true,
		slidesToShow: 6,
		slidesToScroll: 6,
		prevArrow: $('.attention-button-prev'), 
		nextArrow: $('.attention-button-next'), 
		infinite: false,
	});

	$('.attention_slide').slick({
		slide: 'li',
		variableWidth: true,
		dots: false,
		arrows: true,
		slidesToShow: 6,
		slidesToScroll: 6,
		prevArrow: $('.attention-button-prev'), 
		nextArrow: $('.attention-button-next'), 
		infinite: false,
	});

	$('.brand_slide').slick({
		slide: 'div',
		variableWidth: true,
		dots: true,
		arrows: true,
		rows: 2,
		prevArrow: $('.brand-button-prev'), 
		nextArrow: $('.brand-button-next'), 
		infinite: false,
		customPaging : function(slider, index) { 
			var num = index + 1;
			return '<span class="dot">'+ num +'</span>';
		}
	});

	$('.substance_slide').slick({
		slide: 'div',
		variableWidth: true,
		dots: true,
		arrows: true,
		rows: 2,
		prevArrow: $('.substance-button-prev'), 
		nextArrow: $('.substance-button-next'), 
		infinite: false,
		customPaging : function(slider, index) { 
			var num = index + 1;
			return '<span class="dot">'+ num +'</span>';
		}
	});
</script>