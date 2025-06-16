<!-- <script src="<?php echo base_url(); ?>mb/js/shop.js?v=<?=time()?>"></script> -->

<form name="fbuyform" method="post">
    <input type="hidden" name="gs_id[]" value="<?php echo $gs_id; ?>">
    <input type="hidden" id="it_price" value="<?php echo $getSalePrice; ?>">
    <input type="hidden" name="ca_id" value="<?php echo $gs['ca_id']; ?>">
    <input type="hidden" name="referralCode" value="<?=$referralCode?>">
    <input type="hidden" name="sw_direct">
    <!-- <input type="hidden" name="is_soldout_timesale" value=<?=$is_soldout_timesale?>> -->
    <div class="sp_wrap">
        <div class="sp_sub_wrap">
            <div class="v_cont">
                <ul class="v_horiz">
                    <li><?php echo $v_horizImg; ?></li>
                </ul>
            </div>
            <a class="sp_b_a fa fa-angle-left" href="javascript:chgimg(-1)"></a>
            <a class="sp_b_a fa fa-angle-right" href="javascript:chgimg(1)"></a>
        </div>
        <div class="subject">
            <div class="subject_top">
                <span><?=$gsName?></span>
                <div onclick="javascript:itemlistwish('<?=$gs_id?>');" id="<?=$gs_id?>" class="<?=$gs_id?> <?=$zzim?>"></div>
                <div class="share" onclick="clip('')"></div>
            </div>
            <?=$gsExplain?>
        </div>
        <?php if(false) { ?>
        <div class="sp_sns">
            만족도 : <?php echo $aver_score; ?>% <span class="hline"></span>상품평 : <?php echo number_format($item_use_count); ?>건
        </div>
        <?php } ?>
        <?=$no_is_onlyTag?>
        <!-- bid 관련 태그 -->
        <?=$gsOdrPeriod?>
        <?=$gsOdrStock?>
        <?=$gsOdrSales?>

        <!-- 상품 공통 태그 -->
        <?=$gPointTag?>
        <?=$cp_btnTag?>
        <?=$gsMarkerTag?>
        <?=$gsOriginTag?>
        <?=$gsBrandNmTag?>
        <?=$gsModelTag?>
        <?=$gsOdrMin?>
        <?=$gsOdrMax?>
        <div class="<?php echo $sc_class; ?>">
            <ul>
                <li class='tlst'>배송비</li>
                <li class='trst'><?php echo $msa; ?></li>
            </ul>
        </div>
        <div class="sp_tbox">
            <ul>
                <li class='tlst'>배송지역</li>
                <li class='trst padt2'><?php echo $gs['zone']; ?> <?php echo $gs['zone_msg']; ?></li>
            </ul>
        </div>
        <div class="sp_tbox">
            <ul>
                <li class='tlst'>배송기간</li>
                <li class='trst padt2'><?php echo $gs['delivery_period']; ?>일</li>
            </ul>
        </div>
        <?=$orderOpt?>
        <?=$naverPayTag?>
        <?=$relatedgs?>

        <!-- bid 상품구매현황 -->
        <?=$timesale?>

        <div class="sp_tab">
            <nav role="navigation">
                <ul>
                    <li id='d1' class="active"> <a href="javascript:chk_tab(1);">상품정보</a> </li>
                    <li id='d2'> <a href="javascript:chk_tab(2);">구매후기</a> </li>
                    <li id='d3'> <a href="javascript:chk_tab(3);">Q&A</a> </li>
                    <li id='d4'> <a href="javascript:chk_tab(4);">반품/교환</a> </li>
                </ul>
            </nav>
        </div>
        <div class="sp_msgt">아래 상품정보는 옵션 및 사은품 정보 등 실제 상품과 차이가 있을수 있습니다</div>
        <div id="v1">
            <div class="sp_vbox_mr">
                <ul>
                    <li class='tlst'>전자상거래 등에서의 상품정보제공 고시</li>
                    <li class='trst'><a href="javascript:chk_show('extra');" id="extra">보기 <span class='im im_arr'></span></a></li>
                </ul>
            </div>
            <?=$gs_infoTag?>
            <div class="sp_vbox">
                <?php echo $gs_memoTag; ?>
            </div>
        </div>
        <div id="v2" style="display:none;">
            <?php echo $gs_reviewTag; ?>
        </div>

        <div id="v3" style="display:none;">
            <?php echo $gs_qnaTag; ?>
        </div>
        <div id="v4" style="display:none;">
            <div class="sp_vbox">
                <?php echo $gs_policy; ?>
            </div>
        </div>
    </div>
</form>


<script>
// 상품보관
function item_wish(f){
    var base_url=document.getElementById('base_url').value;
	f.action = base_url+"process/wishupdate";
	f.submit();
}

function fsubmit_check(f){
    // 판매가격이 0 보다 작다면
    if (document.getElementById("it_price").value < 0) {
        alert("전화로 문의해 주시면 감사하겠습니다.");
        return false;
    }

	if($(".sit_opt_list").size() < 1) {
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

        if(val.length < 1) {
            alert("수량을 입력해 주십시오.");
            result = false;
            return false;
        }

        if(val.replace(/[0-9]/g, "").length > 0) {
            alert("수량은 숫자로 입력해 주십시오.");
            result = false;
            return false;
        }

        if(parseInt(val.replace(/[^0-9]/g, "")) < 1) {
            alert("수량은 1이상 입력해 주십시오.");
            result = false;
            return false;
        }

        io_type = $el_type.eq(index).val();
        if(io_type == "0")
            sum_qty += parseInt(val);
    });

    if(!result) {
        return false;
    }

    if(min_qty > 0 && sum_qty < min_qty) {
		alert("주문옵션 개수 총합 "+number_format(String(min_qty))+"개 이상 주문해 주세요.");
        return false;
    }

    if(max_qty > 0 && sum_qty > max_qty) {
		alert("주문옵션 개수 총합 "+number_format(String(max_qty))+"개 이하로 주문해 주세요.");
        return false;
    }

    return true;
}

// 바로구매, 장바구니 폼 전송
function fbuyform_submit(sw_direct){
    var base_url=document.getElementById('base_url').value;

	var f = document.fbuyform;
	f.sw_direct.value = sw_direct;
    
    // var is_soldout_timesale = f.is_soldout_timesale.value;

    // if(is_soldout_timesale) {
	// 	alert('현재 구매는 예비 입찰으로 기존 입찰자의 취소가 없을 경우 환불될 수 있습니다');
	// }
    
	if(sw_direct == "cart") {
        if(!confirm("해당 상품을 장바구니에 담으시겠습니까?")) return false;
		f.sw_direct.value = 0;
	} else { // 바로구매
        if(!confirm("해당 상품을 구매하시겠습니까?")) return false;
		f.sw_direct.value = 1;
	}

	if($(".sit_opt_list").size() < 1) {
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

		if(val.length < 1) {
			alert("수량을 입력해 주세요.");
			result = false;
			return;
		}

		if(val.replace(/[0-9]/g, "").length > 0) {
			alert("수량은 숫자로 입력해 주세요.");
			result = false;
			return;
		}

		if(parseInt(val.replace(/[^0-9]/g, "")) < 1) {
			alert("수량은 1이상 입력해 주세요.");
			result = false;
			return;
		}

		io_type = $el_type.eq(index).val();
		if(io_type == "0")
			sum_qty += parseInt(val);
	});

	if(!result) {
		return;
	}

	if(min_qty > 0 && sum_qty < min_qty) {
		alert("주문옵션 개수 총합 "+number_format(String(min_qty))+"개 이상 주문해 주세요.");
		return;
	}

	if(max_qty > 0 && sum_qty > max_qty) {
		alert("주문옵션 개수 총합 "+number_format(String(max_qty))+"개 이하로 주문해 주세요.");
		return;
	}

	f.action = base_url+"m/process/cartupdate";
	f.submit();
}

// 전자상거래 등에서의 상품정보제공 고시
var old = '';
function chk_show(name) {
	submenu=eval("ids_"+name+".style");

	if(old!=submenu) {
		if(old) { old.display='none'; }

		submenu.display='';
		eval("extra").innerHTML = "닫기";
		old = submenu;

	} else {
		submenu.display='none';
		eval("extra").innerHTML = "보기";
		old = '';
	}
}

// 상품문의
var qa_old = '';
function qna(name){
	qa_submenu = eval("qna"+name+".style");

	if(qa_old!=qa_submenu) {
		if(qa_old) { qa_old.display='none'; }

		qa_submenu.display='block';
		qa_old=qa_submenu;

	} else {
		qa_submenu.display='none';
		qa_old='';
	}
}

// 상품문의 삭제
$(function(){
    $(".itemqa_delete").click(function(){
        return confirm("정말 삭제 하시겠습니까?\n\n삭제후에는 되돌릴수 없습니다.");
    });
});

// 탭메뉴 컨트롤
function chk_tab(n) {
	for(var i=1; i<=4; i++) {
		if(eval("d"+i).className == "" && i == n) {
			eval("d"+i).className = "active";
			eval("v"+i).style.display = "";
		} else {

			if(i != n) {
				eval("d"+i).className = "";
				eval("v"+i).style.display = "none";
			}
		}
	}
}

// 미리보기 이미지
var num = 0;
var img_url = '<?php echo $slide_url; ?>';
var img_max = '<?php echo $slide_cnt; ?>';
var img_arr = img_url.split('|');
var slide   = new Array;
for(var i=0 ;i<parseInt(img_max);i++) {
	slide[i] = img_arr[i];
}

var cnt = slide.length-1;

function chgimg(ergfun) {
	if(document.images) {
		num = num + ergfun;
		if(num > cnt) { num = 0; }
		if(num < 0) { num = cnt; }

		document.slideshow.src = slide[num];
	}
}
</script>
<script>
	function clip(token){
		const base_url = $('#base_url').val();
		const gs_id = <?= $gs_id ?>;

		let textarea = document.createElement("textarea");
		let href = `${base_url}m/shop/view?index_no=${gs_id}`;
		if(token != "") {
			href += "&referralCode="+token;
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

		$('.table_box').css('overflow','auto');
		$('.table_box').css('height','auto');
		$('.table_box').css('max-height','41.67vw');
		$('.more_btn').css('display','none');

	}

    function open_review_modal(index_no, gs_id) {
        var base_url = document.getElementById('base_url').value;
        link = `${base_url}m/shop/reviewDetail?index_no=${index_no}&gs_id=${gs_id}`;

        window.open(link);
	}
</script>