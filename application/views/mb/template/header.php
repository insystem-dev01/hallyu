<?=$slideMenu?>
<div id="wrapper">
	<div onclick="history.go(-1);" class="page_cover"><span class="sm_close"></span></div>
    <div class="top_ad"><?php echo $ban1Tag; ?></div>
    <header id="header">
		<div id="m_gnb">
			<span class="btn_sidem"></span>
            <h1 class="logo"><?php echo $mlogo; ?></h1>
			<span class="btn_search"></span>
			<a href="<?php echo base_url(); ?>m/shop/cart" class="btn_cart"><span class="ic_num"><?php echo $cart_cnt; ?></span></a>
		</div>
        <div id="hd_sch">
			<section>
				<form name="fsearch" id="fsearch" method="get" action="<?php echo base_url(); ?>m/shop/search" onsubmit="return fsearch_submit(this);" autocomplete="off">
				<input type="hidden" name="hash_token" value="<?php echo $OM_HASH_TOKEN; ?>">
				<input type="search" name="ss_tx" value="<?php echo $ss_tx; ?>" class="search_inp" maxlength="20">
				<input type="submit" value="&#xf002;" id="sch_submit">
				<input type="hidden" name="excludeSoldOut" id="excludeSoldOutHidden" value="">
				<input type="hidden" name="onlyBid" id="onlyBidHidden" value="">
				</form>
				<script>
				function fsearch_submit(f){
					if(!f.ss_tx.value){
						alert('검색어를 입력하세요.');
						return false;
					}
					return true;
				}
				</script>
			</section>
			<script>
			$(function(){
				// 상단의 검색버튼 누르면 검색창 보이고 끄기
				$('.btn_search').click(function(){
					if($("#hd_sch").css('display') == 'none'){
						$("#hd_sch").slideDown('fast');
						$(this).attr('class','btn_search');
					} else {
						$("#hd_sch").slideUp('fast');
						$(this).attr('class','btn_search');
					}
				});
			});
			</script>

			<div class="m_rkw_se" id="rkw_open">
				<?php echo $popularWd; ?>
				<button type="button" class="btn_open"></button>
			</div>
			<div class="m_rkw_se" id="rkw_close" style="display:none;">
				<h2>인기검색어 순위</h2>
				<button type="button" class="btn_close"></button>
			</div>
			<div class="m_rkw_bg" style="display:none;">
				<?php echo $popularRankWd; ?>
			</div>

			<script>
			// 인기검색어 펼침
			$(".m_rkw_se .btn_open").click(function(){
				$("#rkw_open").hide();
				$("#rkw_close").show();
				$(".m_rkw_bg").show();
			});

			// 인기검색어 닫음
			$(".m_rkw_se .btn_close").click(function(){
				$("#rkw_open").show();
				$("#rkw_close").hide();
				$(".m_rkw_bg").hide();
			});

			// 인기검색어 롤링
			function tick(){
				$('#ticker li:first').slideUp( function () {
					$(this).appendTo($('#ticker')).slideDown();
				});
			}
			setInterval(function(){ tick () }, 4000);
			</script>
		</div>
    </header>
    <div id="container" <?=$no_index_class?>>
        <?=$no_index_tag?>

		<div class="swiper main_category">
			<div class="swiper-wrapper">
				<?php if(!empty($category)) { ?>
				<?php foreach($category as $row) { ?>
					<div class="swiper-slide">
						<a href="<?=base_url()?>m/<?=$row['url']?>"><?=$row['menu_name']?></a>
					</div>
				<?php } ?>
				<?php } ?>
			</div>
		</div>