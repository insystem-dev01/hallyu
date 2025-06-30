<!-- <script src="<?php echo base_url(); ?>js/shop.js"></script> -->

<form name="fbuyform" method="post">
	<input type="hidden" name="gs_id[]" value="<?php echo $index_no; ?>">
	<input type="hidden" id="it_price" value="<?php echo $price; ?>">
	<input type="hidden" name="ca_id" value="<?php echo $gs['ca_id']; ?>">
	<input type="hidden" name="referralCode" value="<?= $referralCode ?>">
	<input type="hidden" name="sw_direct">
	<input type="hidden" name="type" value="<?= $gs['type'] ?>">
	<?php if (false) { ?>
		<p class="tit_navi marb15"><?php echo $navi; ?></p>
	<?php } ?>
	<div class="vi_info">
		<div class="vi_img_bx <?= $is_open ?>" style="width:<?php echo $default['de_item_medium_wpx']; ?>px">
			<div class="bimg">
				<?= $bimg ?>
			</div>
			<div class="simg_li">
				<ul>
					<?= $simg_li ?>
				</ul>
			</div>
		</div>
		<div class="vi_txt_bx">
			<div class="tit">
				<div class="tit_top">
					<span><?= $gs['gname'] ?></span>
					<div onclick="javascript:itemlistwish('<?= $index_no ?>');" id="<?= $index_no ?>" class="<?= $index_no ?> <?= $zzim ?>"></div>
					<div class="share" onclick="clip('')"></div>
				</div>
				<?= $adminEdit ?>
				<?= $explan ?>
			</div>
			<?= $priceTag ?>
			<div class="vi_txt_li">
				<!-- bid 관련 태그 -->
				<?= $odr_periodTag ?>
				<?= $odr_stockTag ?>
				<?= $odr_salesTag ?>

				<!-- 상품 공통 태그 -->
				<?= $pointTag ?>
				<?= $couponTag ?>
				<?= $maker ?>
				<?= $origin ?>
				<?= $brand ?>
				<?= $model ?>

				<dl>
					<dt>배송비</dt>
					<dd><?php echo $sendcost; ?></dd>
				</dl>
				<dl>
					<dt>배송지역</dt>
					<dd><?php echo $gs['zone']; ?> <?php echo $gs['zone_msg']; ?></dd>
				</dl>
				<dl>
					<dt>배송기간</dt>
					<dd><?php echo $gs['delivery_period']; ?>일</dd>
				</dl>
				<?php if (false) { ?>
					<dl>
						<dt>상품평</dt>
						<dd>상품평 : <?php echo $item_use_count; ?>건 | 평점 : <img src="<?php echo base_url(); ?>img/sub/view_score_<?php echo $star_score; ?>.gif"></dd>
					</dl>
				<?php } ?>
				<?= $odr_minTag ?>
				<?= $odr_maxTag ?>
			</div>
			<?= $optionTag ?>
			<?= $buyBtn ?>

			<!-- bid 상품구매현황 -->
			<?= $timesale ?>
		</div>
	</div>
</form>
<?= $related ?>
<section class="mart50">
	<a name="tab1"></a>
	<div class="vi_tab">
		<ul>
			<li onclick="javascript:pg_anchor('tab1')" class="on">상품정보</li>
			<li onclick="javascript:pg_anchor('tab2')">상품평</li>
			<li onclick="javascript:pg_anchor('tab3')">상품문의</li>
			<li onclick="javascript:pg_anchor('tab4')">배송/교환/반품안내</li>
		</ul>
	</div>
	<div class="ofh tac padt10 padb10">
		<?php echo nl2br($thumbTag); ?>
	</div>
	<?= $info_value_tag ?>
</section>
<section class="mart50">
	<a name="tab2"></a>
	<div class="vi_tab">
		<ul>
			<li onclick="javascript:pg_anchor('tab1')">상품정보</li>
			<li onclick="javascript:pg_anchor('tab2')" class="on">상품평</li>
			<li onclick="javascript:pg_anchor('tab3')">상품문의</li>
			<li onclick="javascript:pg_anchor('tab4')">배송/교환/반품안내</li>
		</ul>
	</div>
	<div class="mart15">
		<?= $view_user ?>
	</div>
</section>

<section class="mart50">
	<a name="tab3"></a>
	<div class="vi_tab">
		<ul>
			<li onclick="javascript:pg_anchor('tab1')">상품정보</li>
			<li onclick="javascript:pg_anchor('tab2')">상품평</li>
			<li onclick="javascript:pg_anchor('tab3')" class="on">상품문의</li>
			<li onclick="javascript:pg_anchor('tab4')">배송/교환/반품안내</li>
		</ul>
	</div>
	<div class="mart15 vi_qa">
		<?= $view_qa ?>
	</div>
</section>

<section class="mart50">
	<a name="tab4"></a>
	<div class="vi_tab">
		<ul>
			<li onclick="javascript:pg_anchor('tab1')">상품정보</li>
			<li onclick="javascript:pg_anchor('tab2')">상품평</li>
			<li onclick="javascript:pg_anchor('tab3')">상품문의</li>
			<li onclick="javascript:pg_anchor('tab4')" class="on">배송/교환/반품안내</li>
		</ul>
	</div>
	<div class="mart15">
		<?php echo $thumbTag2; ?>
	</div>
</section>

<div class="review_modal" id="review_modal">
	<div class="top_area">
		<div class="info_box">
			<span class="title">포토 & 동영상</span>
			<span class="current">1</span>
			<span class="slash">/</span>
			<span class="total"><?= $item_use_count ?></span>
			<div class="back" id="modal_close_btn"></div>
		</div>
	</div>
</div>

<script>
	var base_url = '';
	window.onload = function() {
		base_url = document.getElementById('base_url').value;
	}
	// 상품보관
	function item_wish(f) {
		f.action = base_url + "process/wishupdate";
		f.submit();
	}

	function fsubmit_check(f) {
		// 판매가격이 0 보다 작다면
		if (document.getElementById("it_price").value < 0) {
			alert("전화로 문의해 주시면 감사하겠습니다.");
			return false;
		}

		if ($(".sit_opt_list").size() < 1) {
			alert("주문옵션을 선택해주시기 바랍니다.");
			return false;
		}

		var val, io_type, result = true;
		var sum_qty = 0;
		var min_qty = parseInt('<?php echo $odr_min; ?>');
		var max_qty = parseInt('<?php echo $odr_max; ?>');
		var $el_type = $("input[name^=io_type]");

		$("input[name^=ct_qty]").each(function(index) {
			val = $(this).val();

			if (val.length < 1) {
				alert("수량을 입력해 주십시오.");
				result = false;
				return false;
			}

			if (val.replace(/[0-9]/g, "").length > 0) {
				alert("수량은 숫자로 입력해 주십시오.");
				result = false;
				return false;
			}

			if (parseInt(val.replace(/[^0-9]/g, "")) < 1) {
				alert("수량은 1이상 입력해 주십시오.");
				result = false;
				return false;
			}

			io_type = $el_type.eq(index).val();
			if (io_type == "0")
				sum_qty += parseInt(val);
		});

		if (!result) {
			return false;
		}

		if (min_qty > 0 && sum_qty < min_qty) {
			alert("주문옵션 개수 총합 " + number_format(String(min_qty)) + "개 이상 주문해 주세요.");
			return false;
		}

		if (max_qty > 0 && sum_qty > max_qty) {
			alert("주문옵션 개수 총합 " + number_format(String(max_qty)) + "개 이하로 주문해 주세요.");
			return false;
		}

		return true;
	}

	// 바로구매, 장바구니 폼 전송
	function fbuyform_submit(sw_direct) {
		var f = document.fbuyform;
		f.sw_direct.value = sw_direct;
		// var is_soldout_timesale = f.is_soldout_timesale.value;

		// if(is_soldout_timesale) {
		// 	alert('현재 구매는 예비 입찰으로 기존 입찰자의 취소가 없을 경우 환불될 수 있습니다');
		// }

		if (sw_direct == "cart") {
			if (!confirm("해당 상품을 장바구니에 담으시겠습니까?")) return false;
			f.sw_direct.value = 0;
		} else { // 바로구매
			if (!confirm("해당 상품을 구매하시겠습니까?")) return false;
			f.sw_direct.value = 1;
		}


		if ($(".sit_opt_list").size() < 1) {
			alert("주문옵션을 선택해주시기 바랍니다.");
			return;
		}

		var val, io_type, result = true;
		var sum_qty = 0;
		var min_qty = parseInt('<?php echo $odr_min; ?>');
		var max_qty = parseInt('<?php echo $odr_max; ?>');
		var $el_type = $("input[name^=io_type]");

		$("input[name^=ct_qty]").each(function(index) {
			val = $(this).val();

			if (val.length < 1) {
				alert("수량을 입력해 주세요.");
				result = false;
				return;
			}

			if (val.replace(/[0-9]/g, "").length > 0) {
				alert("수량은 숫자로 입력해 주세요.");
				result = false;
				return;
			}

			if (parseInt(val.replace(/[^0-9]/g, "")) < 1) {
				alert("수량은 1이상 입력해 주세요.");
				result = false;
				return;
			}

			io_type = $el_type.eq(index).val();
			if (io_type == "0")
				sum_qty += parseInt(val);
		});

		if (!result) {
			return;
		}

		if (min_qty > 0 && sum_qty < min_qty) {
			alert("주문옵션 개수 총합 " + number_format(String(min_qty)) + "개 이상 주문해 주세요.");
			return;
		}
		0
		if (max_qty > 0 && sum_qty > max_qty) {
			alert("주문옵션 개수 총합 " + number_format(String(max_qty)) + "개 이하로 주문해 주세요.");
			return;
		}
		f.action = base_url + "process/cartupdate";
		f.submit();
	}
</script>
<script>
	function clip(token) {
		const base_url = $('#base_url').val();
		const gs_id = <?= $index_no ?>;

		let textarea = document.createElement("textarea");
		let href = `${base_url}shop/view?index_no=${gs_id}`;
		if (token != "") {
			href += "&referralCode=" + token;
		}

		document.body.appendChild(textarea);
		textarea.value = href;
		textarea.select();
		document.execCommand("copy");
		document.body.removeChild(textarea);

		alert("링크가 복사되었습니다.");
	};

	function order_more() {
		event.preventDefault();

		$('.table_box').css('overflow', 'auto');
		$('.table_box').css('height', 'auto');
		$('.table_box').css('max-height', '350px');
		$('.more_btn').css('display', 'none');
	}

	// 모달창 열기
	function modal(id) {
		var zIndex = 9999;
		var modal = $("#" + id);

		// 백그라운드
		var background = $("<div class='background'>").css({
			position: 'fixed',
			zIndex: zIndex,
			left: '0px',
			top: '0px',
			width: '100%',
			height: '100%',
			overflow: 'auto',
			// 레이어 색갈은 여기서 바꾸면 됨
			backgroundColor: 'rgba(0,0,0,0.4)'
		}).appendTo('body');
		modal.css({
				position: 'fixed',

				// 시꺼먼 레이어 보다 한칸 위에 보이기
				zIndex: zIndex + 1,

				// div center 정렬
				top: '50%',
				left: '50%',
				transform: 'translate(-50%, -50%)',
				msTransform: 'translate(-50%, -50%)',
				webkitTransform: 'translate(-50%, -50%)'
			}).show() // 닫기 버튼 처리, 시꺼먼 레이어와 모달 div 지우기
			.find('#modal_close_btn')
			.on('click', function() {
				background.remove();
				modal.hide();
			});
	}

	function open_review_modal(index_no, gs_id) {
		$('.background').remove();
		$('.content_area').remove();

		// 비동기로 리뷰 정보 가져오기
		$.ajax({
			type: "POST",
			url: base_url + "Process/get_review_data",
			data: {
				index_no: index_no,
				gs_id: gs_id
			},
			cache: false,
			async: true,
			dataType: "json",
			success: function(res) {
				if (res.res) {
					$('.current').text(res.current);
					$('#review_modal').append(res.data);
				}
			}
		});

		// const currentScrollY = window.scrollY;
		// document.body.style.position = 'fixed';
		// document.body.style.width = '100%';
		// document.body.style.top = `-${currentScrollY}px`; // 현재 스크롤 위치
		// document.body.style.overflowY = 'scroll';

		modal('review_modal');
	}

	function change_photo(type, url, event) {
		var clickedElement = event.target;
		parentElement = clickedElement.closest('.disable');

		$(".thumb").find('.disable').each(function(index, item) {
			if (item.classList.contains('active')) {
				item.classList.remove('active');
			}
		});

		parentElement.classList.add('active');

		var tag = "";
		if (type == "img") {
			tag = "<img src='" + url + "'>";
		} else {
			tag = "<video controls src='" + url + "'></video>";
		}
		$('.photo_box').empty();
		$('.photo_box').append(tag);
	}


	document.addEventListener("DOMContentLoaded", function() {
		const bigImg = document.getElementById("bigImg");
		const thumbs = document.querySelectorAll(".simg_li .thumb-item");

		thumbs.forEach(li => {
			li.addEventListener("mouseenter", function() {
				const src = this.getAttribute("data-src");
				if (src && bigImg.src !== src) {
					bigImg.src = src;
				}
			});
			// (선택) 마우스가 나가면 원래 1번 이미지로 돌아가기
			li.addEventListener("mouseleave", function() {
				// bigImg.src = thumbs[0].getAttribute("data-src");
			});
		});
	});
</script>