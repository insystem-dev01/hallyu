<!-- 이니시스 결제 시작 { -->
    <div id="sod_fin">
	<section id="sod_fin_list">
        <h2>주문하실 상품</h2>
        <ul id="sod_list_inq" class="sod_list">
            <?=$ulList?>
            </ul>
        <dl id="sod_bsk_tot">
            <dt class="sod_bsk_dvr"><span>주문총액</span></dt>
            <dd class="sod_bsk_dvr"><strong><?php echo $totPrice; ?></strong></dd>
            <?=$couponTag?>
            <?=$usePointTag?>
            <?=$baesongTag?>
            <dt class="sod_bsk_cnt"><span>총계</span></dt>
            <dd class="sod_bsk_cnt"><strong><?=$stotalUsePrice?></strong></dd>

            <!-- <dt class="sod_bsk_point"><span>포인트적립</span></dt>
            <dd class="sod_bsk_point"><strong><?=$stotalPrice?></strong></dd> -->
        </dl>
    </section>
    <?=$orderform1Tag?>
    <form id="forderform" name="forderform" method="post" action="<?php echo $order_action_url; ?>" autocomplete="off">
        <section id="sod_fin_orderer">
            <h3 class="anc_tit">주문하시는 분</h3>
            <div  class="odf_tbl">
                <table>
                    <colgroup>
                        <col class="w70">
                        <col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <th scope="row">이 름</th>
                            <td><?php echo $od_name; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">연락처</th>
                            <td><?php echo $od_cel; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">주 소</th>
                            <td><?php echo $od_add; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">E-mail</th>
                            <td><?php echo $od_email; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <section id="sod_fin_receiver">
            <h3 class="anc_tit">받으시는 분</h3>
            <div  class="odf_tbl">
                <table>
                    <colgroup>
                        <col class="w70">
                        <col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <th scope="row">이 름</th>
                            <td><?php echo $od_bname; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">연락처</th>
                            <td><?php echo $od_bcel; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">주 소</th>
                            <td><?php echo $od_baddr; ?></td>
                        </tr>
                        <?=$od_memo?>
                    </tbody>
                </table>
            </div>
        </section>
        <?=$Inicisorderform2?>
        <div id="show_progress" style="display:none;">
            <img src="<?php echo base_url(); ?>mb/img/loading.gif" alt="">
            <span>주문완료 중입니다. 잠시만 기다려 주십시오.</span>
        </div>
	</form>
</div>
<script type="text/javascript" src="https://cdn.iamport.kr/v1/iamport.js"></script>
<script language=javascript>
var base_url=document.getElementById('base_url').value;
var IMP = window.IMP; // 생략 가능
var od_id='<?=$od_id?>';
IMP.init('imp52034655'); // 예: imp00000000

    
function paybtn(f) {
    var pay_method=f.od_settle_case.value;
    var buyer_tel=f.od_hp.value;
    var name=f.goodname.value;
    var amount=f.price.value;
    var buyer_email=f.buyeremail.value;
    var buyer_addr=f.od_addr1.value;
    var buyer_name=f.od_name.value;
    var buyer_postcode=f.od_zip.value;
    var merchant_uid = 'omni' + new Date().getTime();

    <?=$payBtnJs?>
    
    if(pay_method=='신용카드'){
        pay_method='card';
    }else if(pay_method=='휴대폰'){
        pay_method='phone';
    }else if(pay_method=='계좌이체'){
        pay_method='trans';
    }else if(pay_method=="삼성페이"){
        pay_method="samsung";
    }
    $.ajax({
        url:base_url+"m/process/preimport",
        data:{
            'merchant_uid':merchant_uid,
            'od_id':od_id,
            <?=$preimportJs?>
        },
        type:"POST",
        success:function(res){
            var result=JSON.parse(res);
            if(result['res']=='true'){
                importProcess(pay_method,merchant_uid,name,amount,buyer_email,buyer_name,buyer_tel,buyer_addr,buyer_postcode);
            }else{
                alert('다시 실행해주세요.');
            }
        },error:function(a,b,c){
            alert('다시 실행해주세요.');
            history.back();
        }
    })
    function importProcess(pay_method,merchant_uid,name,amount,buyer_email,buyer_name,buyer_tel,buyer_addr,buyer_postcode){
        IMP.request_pay({ // param
            pg: "smartro_v2",
            pay_method: pay_method,
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
            if (rsp.error_code != null) {
                // 결제 실패 시 로직,
                var msg = "결제에 실패하였습니다.실패사유: ";
                msg += rsp.error_msg;
                history.back();
                alert(msg);
                console.log(rsp);
            } else {
                var imp_uid=rsp.imp_uid;
                var merchant_uid=rsp.merchant_uid;
                
                $.ajax({
                    url:base_url+'process/orderformresult',
                    data:{
                        'od_id':od_id,
                        'merchant_uid':merchant_uid,
                        'imp_uid':imp_uid,
                    },
                    type:'POST',
                    success:function(e){
                        console.log(e);
                        $('.import_loading').hide();
                        var res=JSON.parse(e);
                        if(res['res']){
                            alert('결제가 완료되었습니다.');
                            var od_id=res['od_id'];
                            var uid=res['uid'];
                            location.href=base_url+'m/shop/orderinquiryview?od_id='+od_id+'&uid='+uid;
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
            }
        });
    }
    return false;
}
/* 결제방법에 따른 처리 후 결제등록요청 실행 */
function forderform_check(f){

    // 금액체크
    if(!payment_check(f))
        return false;


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
    <?=$flag_useJs?>
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