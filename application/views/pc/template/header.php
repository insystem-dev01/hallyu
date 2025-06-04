<?=$popup?>
<div id="wrapper">
	<div id="header">
        <?=$hd_banTag?>
        <!-- <div id="tnb">
		</div> -->
        <div id="hd">
			<!-- 상단부 영역 시작 { -->
			<div id="hd_inner">
                <div class="hd_logo">
					<?php echo $hd_logo; ?>
				</div>
                <div id="hd_sch">
					<fieldset class="sch_frm">
						<legend>사이트 내 전체검색</legend>
						<form name="fsearch" id="fsearch" method="post" action="<?php echo base_url(); ?>shop/search" onsubmit="return fsearch_submit(this);" autocomplete="off">
						<input type="hidden" name="hash_token" value="<?php echo $OM_HASH_TOKEN; ?>">
						<input type="text" name="ss_tx" class="sch_stx" maxlength="20" placeholder="검색어를 입력해주세요">
						<button type="submit" class="sch_submit" value="검색"></button>
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
					</fieldset>
				</div>
				<a class="hd_zzim" href="<?=base_url()?>shop/wish">
				</a>
				<?=$cart_data?>

			</div>
            <div id="gnb">
				<div id="gnb_inner">
					<div class="all_cate">
						<span class="allc_bt">
							<div class="menu_icon"></div>
							전체 카테고리
						</span>
						<div class="con_bx">
							<ul>
                                <?=$cateTag?>
                            </ul>
                        </div>
						<script>
						$(function(){
							$('.all_cate .allc_bt').click(function(){
								if($('.all_cate .con_bx').css('display') == 'none'){
									$('.all_cate .con_bx').show();
									$(this).html('<i class="ionicons ion-ios-close-empty"></i> 전체 카테고리');
								} else {
									$('.all_cate .con_bx').hide();
									$(this).html('<div class="menu_icon"></div> 전체 카테고리');
								}
							});
						});
						</script>
					</div>
                    <div class="gnb_li">
						<ul>
                            <?=$gw_menu?>
                        </ul>
                    </div>
					<div id="tnb_inner">
						<ul class="fr">
							<?=$tnb_str?>
						</ul>
					</div>
				</div>
			</div>
            <!-- } 상단부 영역 끝 -->
            <script>
			$(function(){
				// 상단메뉴 따라다니기
				var elem1 = $("#hd_banner").height() + $("#tnb").height() + $("#hd_inner").height();
				var elem2 = $("#hd_banner").height() + $("#tnb").height() + $("#hd").height();
				var elem3 = $("#gnb").height();
				$(window).scroll(function () {
					if($(this).scrollTop() > elem1) {
						$("#gnb").addClass('gnd_fixed');
						$("#hd").css({'padding-bottom':elem3})
					} else if($(this).scrollTop() < elem2) {
						$("#gnb").removeClass('gnd_fixed');
						$("#hd").css({'padding-bottom':'0'})
					}
				});
			});
			</script>
		</div>
    </div>
    <div id="container">
    <?= $quickMenu ?>
	<?= $cs_banner ?>
    <?= $no_index ?>