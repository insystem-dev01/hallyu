<!-- 장바구니 시작 { -->
    <!-- <script src="<?php echo base_url(); ?>mb/js/shop.js"></script> -->

<div class="stit_txt">
	※ 총 <?php echo number_format($cart_count); ?>개의 상품이 담겨 있습니다.
</div>
<div id="sod_bsk">
	<form name="frmcartlist" id="sod_bsk_list" method="post" action="<?php echo $cart_action_url; ?>">
        <?=$notEmpty?>
        <ul class="sod_list">
            <?=$cartTable?>
        </ul>
        <?=$deliveryPay?>
        <div id="sod_bsk_act" class="btn_confirm">
            <?=$shopKeep?>
        </div>
    </form>
</div>

<script>
$(function() {
    var close_btn_idx;

    // 선택사항수정
    $(".mod_options").click(function() {
        var base_url=document.getElementById('base_url').value;
        var gs_id = $(this).attr("id").replace("mod_opt_", "");
        var $this = $(this);
        close_btn_idx = $(".mod_options").index($(this));
        $.post(
            base_url+"m/shop/cartoption",
            { gs_id: gs_id },
            function(data) {
                $("#mod_option_frm").remove();
                $this.after("<div id=\"mod_option_frm\"></div>");
                $("#mod_option_frm").html(data);
                price_calculate();
            }
        );
    });

    // 모두선택
    $("input[name=ct_all]").click(function() {
        if($(this).is(":checked"))
            $("input[name^=ct_chk]").prop("checked", true);
        else
            $("input[name^=ct_chk]").prop("checked", false);
    });
    $('input[name^=ct_chk').click(function(){
        var total=$('input[name^=ct_chk').length;
        var perCnt=0;
        $('input[name^=ct_chk').each(function(index,item){
            if($(item).is(":checked")){
                perCnt++;
            }
        });
        if(total==perCnt){
            $("input[name=ct_all]").prop('checked',true);
        }else{
            $("input[name=ct_all]").prop('checked',false);
        }
    });

    // 옵션수정 닫기
    $(document).on("click", "#mod_option_close", function() {
        $("#mod_option_frm").remove();
        $("#win_mask, .window").hide();
        $(".mod_options").eq(close_btn_idx).focus();
    });
    $("#win_mask").click(function () {
        $("#mod_option_frm").remove();
        $("#win_mask").hide();
        $(".mod_options").eq(close_btn_idx).focus();
    });

});

function fsubmit_check(f) {
    if($("input[name^=ct_chk]:checked").size() < 1) {
        alert("구매하실 상품을 하나이상 선택해 주십시오.");
        return false;
    }

    return true;
}

function form_check(act) {
    var f = document.frmcartlist;
    var cnt = f.records.value;

    if(act == "buy"){
		if($("input[name^=ct_chk]:checked").size() < 1) {
			alert("주문하실 상품을 하나이상 선택해 주십시오.");
			return false;
		}

        f.act.value = act;
        f.submit();
    }
    else if(act == "alldelete"){
        f.act.value = act;
        f.submit();
    }else if(act == "seldelete"){
        if($("input[name^=ct_chk]:checked").size() < 1) {
            alert("삭제하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }

        f.act.value = act;
        f.submit();
    }

    return true;
}
</script>
<!-- } 장바구니 끝 -->
