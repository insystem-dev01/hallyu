<?php
include_once('./_common.php');
include_once('./incKakaopayCommon.php');

$approval_url   = OM_HTTPS_SHOP_URL."/kakaopay/orderSuccess.php";
$cancel_url     = OM_HTTPS_SHOP_URL."/kakaopay/orderCancle.php";
$fail_url       = OM_HTTPS_SHOP_URL."/kakaopay/orderCancle.php";

$kakao_params = array(
        'cid'               => $cid,             // 가맹점코드 10자
        'partner_order_id'  => $partner_order_id,           // 주문번호
        'partner_user_id'   => $partner_user_id,    // 유저 id
        'item_name'         => $item_name,        // 상품명
        'quantity'          => $quantity,                // 상품 수량
        'total_amount'      => $total_amount,             // 상품 총액
        'tax_free_amount'   => $tax_free_amount,                // 상품 비과세 금액
        'approval_url'      => $approval_url,    // 결제성공시 콜백url 최대 255자
        'cancel_url'        => $cancel_url,
        'fail_url'          => $fail_url,
    );

$url = "https://kapi.kakao.com/v1/payment/ready";  // 신청 url
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($kakao_params));
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $kakao_header);
$response = curl_exec($ch);

$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);

$response_res = json_decode($body);

// print_r($response_res);

$tid = $response_res->tid;
$link_pc = $response_res->next_redirect_pc_url;
$link_mobile = $response_res->next_redirect_mobile_url;
$link_app =  $response_res->android_app_scheme;

// 성공시 넘겨줄 값을 쿠키에 담는다
set_cookie("kakao_tid", $tid, time() + 180);
set_cookie("kakao_order_id", $partner_order_id, time() + 180);
$return['tid'] = $tid;
$return['kakao_order_id'] = $partner_order_id;

if(!is_mobile()){
    $return['pc'] = $link_pc;
    $return['mobile'] = "";
}else {
    $return['pc'] = "";
    $return['mobile'] = $link_mobile;
}
$error = $response_res->code;

if( isset($error)) {
    $return['error_code'] = $error;
    $return['msg'] = "에러입니다. 관리자에게 문의하세요.";
}

echo json_encode($return);
?>