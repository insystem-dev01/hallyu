<?=$orderformJs?>
<!-- 이니시스 결제 시작 { -->
<p><img src="<?php echo base_url(); ?>img/orderform.gif"></p>

<p class="pg_cnt mart20">
	※ 주문하실 상품 내역에 <em>수량 및 주문금액</em>이 틀리지 않는지 반드시 확인하시기 바랍니다.
</p>

<div class="tbl_head02 tbl_wrap">
	<table>
        <colgroup>
            <col class="w120">
            <col>
            <col class="w60">
            <col class="w90">
            <col class="w90">
            <!-- <col class="w90"> -->
            <col class="w90">
        </colgroup>
        <thead>
            <tr>
                <th scope="col">이미지</th>
                <th scope="col">상품/옵션정보</th>
                <th scope="col">수량</th>
                <th scope="col">상품금액</th>
                <th scope="col">소계</th>
                <!-- <th scope="col">포인트</th> -->
                <th scope="col">배송비</th>
            </tr>
        </thead>
        <tbody>
            <?=$inicisTable?>
        </tbody>
        <tfoot>
            <tr>
                <td class="tar" colspan="7">
                    (상품금액 : <strong><?php echo $stot_price; ?></strong> +
                    배송비 : <strong><?php echo $stot_baesong; ?></strong>) -
                    (쿠폰할인 : <strong><?php echo $stot_coupon; ?></strong> +
                    포인트결제 : <strong><?php echo $stot_usepoint; ?></strong>) =
                    총계 : <strong class="fc_red"><?php echo $stot_useprice; ?></strong>
                </td>
            </tr>
        </tfoot>
	</table>
</div>

<form name="forderform" id="forderform" method="post" action="<?php echo $order_action_url; ?>" autocomplete="off">
    <?php
    // 결제대행사별 코드 include (결제대행사 정보 필드)
    echo $orderform2;
    ?>
    <section id="sod_fin_orderer">
        <h2 class="anc_tit">주문하시는 분</h2>
        <div class="tbl_frm01 tbl_wrap">
            <table>
            <colgroup>
                <col width="140">
                <col>
            </colgroup>
            <tr>
                <th scope="row">이름</th>
                <td><?php echo $od['name']; ?></td>
            </tr>
            <tr>
                <th scope="row">연락처</th>
                <td><?php echo $od['cellphone']; ?></td>
            </tr>
            <tr>
                <th scope="row">주소</th>
                <td><?php echo $od_addr; ?></td>
            </tr>
            <tr>
                <th scope="row">E-mail</th>
                <td><?php echo $od['email']; ?></td>
            </tr>
            </table>
        </div>
    </section>
    <section id="sod_fin_receiver">
        <h2 class="anc_tit">받으시는 분</h2>
        <div class="tbl_frm01 tbl_wrap">
            <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tr>
                <th scope="row">이름</th>
                <td><?php echo $od['b_name']; ?></td>
            </tr>
            <tr>
                <th scope="row">연락처</th>
                <td><?php echo $od['b_cellphone']; ?></td>
            </tr>
            <tr>
                <th scope="row">주소</th>
                <td><?php echo $od_baddr; ?></td>
            </tr>
            <?=$sendMsg?>
            </table>
        </div>
    </section>
    <section id="sod_fin_pay">
        <h2 class="anc_tit">결제정보</h2>
        <div class="tbl_frm01 tbl_wrap">
            <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tr>
                <th scope="row">결제방법</th>
                <td><?php echo $od['paymethod']; ?></td>
            </tr>
            <tr>
                <th scope="row">결제금액</th>
                <td class="fs14 bold"><?php echo $payPrice; ?></td>
            </tr>
            </table>
        </div>
    </section>
    <div id="display_pay_button" class="btn_confirm">
        <input type="button" value="결제하기" onclick="forderform_check(this.form);" class="btn_large wset">
        <a href="<?php echo base_url(); ?>" class="btn_large bx-white">취소</a>
    </div>
    <div id="display_pay_process" style="display:none">
        <img src="<?php echo base_url(); ?>img/ajax-loader.gif" alt="">
        <span>주문완료 중입니다. 잠시만 기다려 주십시오.</span>
    </div>
</form>
<div class="import_loading" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background-color:rgba(0,0,0,0.5);z-index:999;">
    <div class="img" style="width:60px;height:auto;float:left;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);">
        <img style="width:100%;height:auto;float:left;" src="<?=base_url()?>img/ajax-loader.gif">
    </div>
</div>
<script>
var form_action_url = "<?php echo $order_action_url; ?>";

function forderform_check(f){
    // 금액체크
    if(!payment_check(f))
        return false;

    if( f.action != form_action_url ){
        f.action = form_action_url;
        f.removeAttribute("target");
        f.removeAttribute("accept-charset");
    }
    switch(f.od_settle_case.value){
        case "계좌이체":
            f.gopaymethod.value = "DirectBank";
            break;
        case "가상계좌":
            f.gopaymethod.value = "VBank";
            break;
        case "휴대폰":
            f.gopaymethod.value = "HPP";
            break;
        case "신용카드":
            f.gopaymethod.value = "Card";
            f.acceptmethod.value = f.acceptmethod.value.replace(":useescrow", "");
            break;
        case "간편결제":
            f.gopaymethod.value = "Kpay";
            break;
    }

    f.price.value       = f.good_mny.value;
    <?=$de_tax_flagJs?>
    f.buyername.value   = f.od_name.value;
    f.buyeremail.value  = f.od_email.value;
    f.buyertel.value    = f.od_hp.value ? f.od_hp.value : f.od_tel.value;
    f.recvname.value    = f.od_b_name.value;
    f.recvtel.value     = f.od_b_hp.value ? f.od_b_hp.value : f.od_b_tel.value;
    f.recvpostnum.value = f.od_b_zip.value;
    f.recvaddr.value    = f.od_b_addr1.value + " " +f.od_b_addr2.value;

	// 주문정보 임시저장
	var order_data = $(f).serialize();
	var save_result = "";
    var base_url=document.getElementById('base_url').value;
	$.ajax({
        type: "POST",
		data: order_data,
		url: base_url+"process/orderdatasaveAjax",
		cache: false,
		async: false,
		success: function(data) {
            save_result = data.trim();
		},error:function(a,b,c){
            console.log(a,b,c);
        }
	});
	if(save_result) {
		alert(save_result);
		return false;
	}
	paybtn(f);
}
// 결제체크
function payment_check(f){
    var tot_price = parseInt(f.good_mny.value);

	if(f.od_settle_case.value == '계좌이체') {
		if(tot_price < 150) {
			alert("계좌이체는 150원 이상 결제가 가능합니다.");
			return false;
		}
	}

    if(f.od_settle_case.value == '신용카드') {
		if(tot_price < 1000) {
            alert("신용카드는 1000원 이상 결제가 가능합니다.");
            return false;
        }
    }

	if(f.od_settle_case.value == '휴대폰') {
		if(tot_price < 350) {
			alert("휴대폰은 350원 이상 결제가 가능합니다.");
			return false;
		}
    }

    return true;
}
</script>
<!-- } 이니시스 결제 끝 -->
