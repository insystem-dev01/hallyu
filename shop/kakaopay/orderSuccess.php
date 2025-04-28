<?php    
include_once('./_common.php');
include_once('./incKakaopayCommon.php');

$pg_token = $_GET["pg_token"];
$kakao_params = array(
    'cid'               => $cid,                            // 가맹점코드 10자
    'tid'               => get_cookie('kakao_tid'),         // 결제 고유번호. 결제준비 API의 응답에서 얻을 수 있음
    'partner_order_id'  => get_cookie('kakao_order_id'),    // 가맹점 주문번호. 결제준비 API에서 요청한 값과 일치해야 함
    'partner_user_id'   => $member['id'],           		// 가맹점 회원 id. 결제준비 API에서 요청한 값과 일치해야 함
    'pg_token'          => $pg_token    					// 결제승인 요청을 인증하는 토큰. 사용자가 결제수단 선택 완료시 approval_url로 redirection해줄 때 pg_token을 query string으로 넘겨줌
);

$url = "https://kapi.kakao.com/v1/payment/approve";  // 신청 url
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

curl_close($ch);

$data['tid'] = $response_res->tid;
$data['aid'] = $response_res->aid;
$data['approved_at'] = $response_res->approved_at;
$data['total'] = $response_res->amount->total;

//print_r($data);

// echo $od_tno;

$data = json_encode($data);
echo "<script>";
echo "top.opener.setChildValue('".$data."');";
echo "top.window.close();";
echo "</script>";
?>