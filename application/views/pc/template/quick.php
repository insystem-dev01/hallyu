<!-- 퀵메뉴 좌측날개 시작 { -->
<?php if(false) { ?>
<div id="qcl">
	<?=$banRows?>
</div>
<?php } ?>
<!-- } 퀵메뉴 좌측날개 끝 -->

<!-- 퀵메뉴 우측날개 시작 { -->
<?php if(false) { ?>
<div id="qcr">
	<ul>
		<li class="tit">최근 본 상품</li>
		<li>
			<?=$quickTag?>
		</li>
		<?php if($pr_tot_count > 0){ ?>
		<li class="stv_wrap">
			<img src="<?php echo base_url();?>img/bt_qcr_prev.gif" id="up">
			<span id="stv_pg"></span>
			<img src="<?php echo base_url();?>img/bt_qcr_next.gif" id="down">
		</li>
		<?php } ?>
	</ul>
</div>
<?php } ?>
<!-- } 퀵메뉴 우측날개 끝 -->

<div class="qbtn_bx">
	<button type="button" id="anc_up">TOP</button>
	<button type="button" id="anc_dw">DOWN</button>
</div>

<script>
$(function() {
	var itemQty = <?php echo $pr_tot_count; ?>; // 총 아이템 수량
	var itemShow = <?php echo $mod; ?>; // 한번에 보여줄 아이템 수량
	var Flag = 1; // 페이지
	var EOFlag = parseInt(itemQty/itemShow); // 전체 리스트를 나눠 페이지 최댓값을 구하고
	var itemRest = parseInt(itemQty%itemShow); // 나머지 값을 구한 후
	if(itemRest > 0) // 나머지 값이 있다면
	{
		EOFlag++; // 페이지 최댓값을 1 증가시킨다.
	}
	$('.c'+Flag).css('display','block');
	$('#stv_pg').text(Flag+'/'+EOFlag); // 페이지 초기 출력값
	$('#up').click(function() {
		if(Flag == 1)
		{
			alert('목록의 처음입니다.');
		} else {
			Flag--;
			$('.c'+Flag).css('display','block');
			$('.c'+(Flag+1)).css('display','none');
		}
		$('#stv_pg').text(Flag+'/'+EOFlag); // 페이지 값 재설정
	})
	$('#down').click(function() {
		if(Flag == EOFlag)
		{
			alert('더 이상 목록이 없습니다.');
		} else {
			Flag++;
			$('.c'+Flag).css('display','block');
			$('.c'+(Flag-1)).css('display','none');
		}
		$('#stv_pg').text(Flag+'/'+EOFlag); // 페이지 값 재설정
	});

	$(window).scroll(function () {
		var pos = $("#ft").offset().top - $(window).height();
		if($(this).scrollTop() > 0) {
			$(".qbtn_bx").fadeIn(300);
			if($(this).scrollTop() > pos) {
				$(".qbtn_bx").addClass('active');
			}else{
				$(".qbtn_bx").removeClass('active');
			}
		} else {
			$(".qbtn_bx").fadeOut(300);
		}
	});

	// 퀵메뉴 상위로이동
    $("#anc_up").click(function(){
        $("html, body").animate({ scrollTop: 0 }, 400);
    });

	// 하위로이동
    $("#anc_dw").click(function(){
		$("html, body").animate({ scrollTop: $(document).height() }, 400);
    });

	// 좌/우 퀵메뉴 높이 자동조절
	<?php if($index) { ?>
	var Theight = $("#header").height() - $("#gnb").height();
	var ptop = 30;
	<?php } else { ?>
	var Theight = $("#header").height() - $("#gnb").height();
	var ptop = 20;
	<?php } ?>
	$("#qcr, #qcl").css({'top':ptop + 'px'});

	$(window).scroll(function () {
		if($(this).scrollTop() > Theight) {
			$("#qcr, #qcl").css({'position':'fixed','top':'67px','z-index':'999'});
		} else {
			$("#qcr, #qcl").css({'position':'absolute','top':ptop + 'px'});
		}
	});
});
</script>
<!-- } 우측 퀵메뉴 끝 -->