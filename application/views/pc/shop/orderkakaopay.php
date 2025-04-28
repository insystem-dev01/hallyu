<?=$kakaoJs?>
<!-- 카카오페이결제 시작 { -->
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
            <col class="w90">
            <col class="w90">
        </colgroup>
        <thead>
        <tr>
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
            <?=$kakaoTableTag?>
        </tbody>
        <tfoot>
        <tr>
            <td class="tar" colspan="7">
                (상품금액 : <strong><?php echo $stotal_price; ?></strong> +
                배송비 : <strong><?php echo $stotal_baesong; ?></strong>) -
                (쿠폰할인 : <strong><?php echo $stotal_coupon; ?></strong> +
                포인트결제 : <strong><?php echo $stotal_usepoint; ?></strong>) =
                총계 : <strong class="fc_red"><?php echo $stotal_useprice; ?></strong>
            </td>
        </tr>
        </tfoot>
	</table>
</div>
<form name="forderform" id="forderform" method="post" action="<?php echo $order_action_url; ?>" autocomplete="off">
    <?=$kakaoOrderForm2?>
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
                <th scope="row">전화번호</th>
                <td><?php echo $od['telephone']; ?></td>
            </tr>
            <tr>
                <th scope="row">핸드폰</th>
                <td><?php echo $od['cellphone']; ?></td>
            </tr>
            <tr>
                <th scope="row">주소</th>
                <td><?php echo $odAdd;?></td>
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
                <th scope="row">전화번호</th>
                <td><?php echo $od['b_telephone']; ?></td>
            </tr>
            <tr>
                <th scope="row">핸드폰</th>
                <td><?php echo $od['b_cellphone']; ?></td>
            </tr>
            <tr>
                <th scope="row">주소</th>
                <td><?php echo $odAdd; ?></td>
            </tr>
            <?=$odMemo?>
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
                <td class="fs14 bold"><?php echo $dis_tot_price; ?></td>
            </tr>
            </table>
        </div>
    </section>
    <?=$kakaoPayTag?>
</form>
<script type="text/javascript" src="https://cdn.iamport.kr/js/iamport.payment-1.2.0.js"></script>
<script>
var IMP = window.IMP; // 생략 가능
IMP.init('sample'); // 예: imp00000000
var od_id="<?=$od_id?>";
function forderform_check(f){
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
function paybtn(f) {
    var pay_method=f.od_settle_case.value;
    var buyer_tel=f.od_hp.value;
    var name=f.item_name.value;
    var amount=f.price.value;
    var buyer_email=f.buyeremail.value;
    var buyer_addr=f.od_addr1.value;
    var buyer_name=f.od_name.value;
    var buyer_postcode=f.od_zip.value;
    var merchant_uid = 'omni_' + new Date().getTime();

    <?=$de_tax_flagJs2?>
    var pg="";
    if(pay_method=="KAKAOPAY"){
        pg="kakaopay";
    }
    $.ajax({
        url:base_url+"process/preimport",
        data:{
            'merchant_uid':merchant_uid,
            'od_id':od_id,
            <?=$de_tax_flagJs3?>
        },
        type:"POST",
        success:function(res){
            var result=JSON.parse(res);
            if(result['res']=='true'){
                importProcess(pg,merchant_uid,name,amount,buyer_email,buyer_name,buyer_tel,buyer_addr,buyer_postcode);
            }else{
                alert('다시 실행해주세요.');
            }
        },error:function(a,b,c){
            alert('다시 실행해주세요.');
            history.back();
        }
    })
    function importProcess(pg,merchant_uid,name,amount,buyer_email,buyer_name,buyer_tel,buyer_addr,buyer_postcode){
        IMP.request_pay({
            pg : pg,
            merchant_uid: merchant_uid,
            name: name,
            amount: amount,
            buyer_email: buyer_email,
            buyer_name: buyer_name,
            buyer_tel: buyer_tel,
            buyer_addr: buyer_addr,
            buyer_postcode: buyer_postcode,
            m_redirect_url: base_url + 'm/process/order_import', // 모바일에서 리디렉션 될 주소
        }, function (rsp) { // callback
            console.log(rsp);
            if (rsp.success) {
                var bank_name=rsp.card_name;
                var pay_method=rsp.pay_method;
                var imp_uid=rsp.imp_uid;
                var merchant_uid=rsp.merchant_uid;
                var amount=rsp.paid_amount;
                $.ajax({
                    url:base_url+'process/orderformresult',
                    data:{
                        'od_id':od_id,
                        'bank_name':bank_name,
                        'amount':rsp.paid_amount,
                        'pay_method':pay_method,
                        'merchant_uid':merchant_uid,
                        'imp_uid':imp_uid,
                        'buyer_name':buyer_name,
                    },
                    type:'POST',
                    success:function(e){
                        console.log(e);
                        $('.import_loading').hide();
                        var res=JSON.parse(e);
                        if(res['res']){
                            var od_id=res['od_id'];
                            var uid=res['uid'];
                            location.href=base_url+'shop/orderinquiryview?od_id='+od_id+'&uid='+uid;
                        }else{
                            alert(res['msg']);
                            location.href=base_url;
                        }
                    },error:function(a,b,c){
                        console.log(a,b,c);
                    }, beforeSend: function () {
                        $('.import_loading').show();      
                    }
                })

            } else {
                // 결제 실패 시 로직,
                var msg = "결제에 실패하였습니다.실패사유: ";
                msg += rsp.error_msg;
                alert(msg);
            }
        });
    }
    return false;
}
</script>
<!-- } 카카오페이결제 끝 -->
