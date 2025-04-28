<?=$aside_my?>
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>마이페이지<i>&gt;</i><?php echo $title; ?></p>
	</h2>
    <p class="pg_cnt">
		<em>총 <?php echo number_format($wish_count); ?>개</em>의 상품이 보관되어 있습니다.
	</p>
    <form name="fwishlist" id="fwishlist" method="post">
        <input type="hidden" name="act" value="multi">
        <input type="hidden" name="sw_direct">

        <div class="tbl_head02 tbl_wrap">
            <table>
                <colgroup>
                    <col width="50">
                    <col width="80">
                    <col>
                    <col width="100">
                    <col width="60">
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">
							<label for="wish_all" class="sound_only">상품전체</label>
							<input type="checkbox" name="wish_all" value="1" id="wish_all">
						</th>
                        <th scope="col">이미지</th>
                        <th scope="col">상품정보</th>
                        <th scope="col">가격</th>
                        <th scope="col">삭제</th>
                    </tr>
                </thead>
                <tbody>
                    <?=$wishTable?>
                </tbody>
		    </table>
	    </div>
		<div id="sod_bsk_btn">
            <div class="palt"><button type="button" onclick="return fwishlist_check(document.fwishlist, 'seldelete');" class="btn_lsmall bx-red">선택상품 삭제</button></div>
        </div>
        <div class="btn_confirm">		
            <!-- <button type="submit" class="btn_large wset" onclick="return fwishlist_check(document.fwishlist,'direct_buy');">주문하기</button> -->
            <button type="submit" class="btn_large bx-white" onclick="return fwishlist_check(document.fwishlist,'');">장바구니 담기</button>
        </div>
	</form>
</div>

<script>
<!--
function out_cd_check(fld, out_cd){
	if(out_cd == 'no'){
		alert("옵션이 있는 상품입니다.\n\n상품을 클릭하여 상품페이지에서 옵션을 선택한 후 주문하십시오.");
		fld.checked = false;
		return;
	}

	if(out_cd == 'price_msg'){
		alert("이 상품은 전화로 문의해 주십시오.\n\n장바구니에 담아 구입하실 수 없습니다.");
		fld.checked = false;
		return;
	}
}

function fwishlist_check(f, act){
    var base_url=document.getElementById('base_url').value;
	var k = 0;
	var length = f.elements.length;

	for(i=0; i<length; i++) {
		if(f.elements[i].checked) {
			k++;
		}
	}
	
	if(k == 0){
		alert("상품을 하나 이상 체크 하십시오");
		return false;
	}
	
	if(act == "direct_buy"){
		f.action = base_url+"process/cartupdate";
		f.sw_direct.value = 1;
	}else if(act == "seldelete"){
		if($("input[name^=chk_gs_id]:checked").size() < 1) {
			alert("삭제하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }
        f.act.value = act;
		f.action = base_url+"process/wishdelete";

    }else{
		f.action = base_url+"process/cartupdate";
		f.sw_direct.value = 0;
	}

	f.submit();
}

    // 모두선택
	$(function() {
		// 모두 선택 / 해제
		$("input[name=wish_all]").on("click", function() {
			const checked = $(this).is(":checked");
			$("input[name^='chk_gs_id']").prop("checked", checked);
		});

		// 개별 체크 상태에 따라 전체선택 자동 체크/해제
		$("input[name^='chk_gs_id']").on("click", function() {
			const total = $("input[name^='chk_gs_id']").length;
			const checked = $("input[name^='chk_gs_id']:checked").length;
			$("input[name=wish_all]").prop("checked", total === checked);
		});
	});

//-->
</script>