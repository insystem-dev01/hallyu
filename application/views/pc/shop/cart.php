<!-- 장바구니 시작 { -->
<!-- <script src="<?php echo base_url(); ?>js/shop.js"></script> -->
<p><img src="<?php echo base_url(); ?>img/tit_cart.gif"></p>
<p class="pg_cnt mart30">
	<em>총 <?php echo number_format($cart_count); ?>개</em>의 상품이 장바구니에 있습니다.
</p>
<form name="frmcartlist" id="sod_bsk_list" method="post" action="<?php echo $cart_action_url; ?>">
<div class="tbl_head02 tbl_wrap">
	<table>
        <colgroup>
            <col class="w50">
            <col class="w120">
            <col>
            <col class="w60">
            <col class="w90">
            <col class="w90">
            <col class="w90">
            <col class="w90">
    	</colgroup>
        <thead>
            <tr>
                <th scope="col">
                    <label for="ct_all" class="sound_only">상품전체</label>
                    <input type="checkbox" name="ct_all" value="1" id="ct_all" checked="checked">
                </th>
                <th scope="col">이미지</th>
                <th scope="col">상품/옵션정보</th>
                <th scope="col">수량</th>
                <th scope="col">상품금액</th>
                <th scope="col">소계</th>
                <th scope="col">포인트</th>
                <th scope="col">배송비</th>
            </tr>
	    </thead>
        <tbody>
            <?=$cartTb?>
        </tbody>
	</table>
</div>
<?=$cartInTag?>
    <div class="btn_confirm">
        <?=$btnConfirmTag?>
    </div>
</form>

<script>
var close_btn_idx;
function mod_option_close(){
    $("#mod_option_frm").remove();
    $(".mod_options").eq(close_btn_idx).focus();
}
$(function() {
    var base_url=document.getElementById('base_url').value;
	// 선택사항수정
	$(".mod_options").click(function() {
		var gs_id = $(this).closest("tr").find("input[name^=gs_id]").val();
		var $this = $(this);
		close_btn_idx = $(".mod_options").index($(this));
        
        //window.open(base_url+"shop/cartoption?gs_id="+gs_id,'_blank')
        var obj={'gs_id':gs_id};
        
        $.ajax({
            url:base_url+"shop/cartoption",
            data:obj,
            type:'POST',
            error:function(a,b,c){
                console.log(a,b,c);
            },
            success:function(data){
                console.log(data);
                $("#mod_option_frm").remove();
				$this.after("<div id=\"mod_option_frm\"></div>");
				$("#mod_option_frm").html(data);
				price_calculate();
            }
        })
	});

    // 모두선택
    $("input[name=ct_all]").click(function() {
        if($(this).is(":checked"))
            $("input[name^=ct_chk]").attr("checked", true);
        else
            $("input[name^=ct_chk]").attr("checked", false);
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
        $(".mod_options").eq(close_btn_idx).focus();
    });

    $("#win_mask").click(function () {
        $("#mod_option_frm").remove();
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
    }
    else if(act == "seldelete"){
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