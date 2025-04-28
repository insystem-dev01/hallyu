<?php
include_once('./_common.php');
// include_once(OM_SHOP_PATH.'/settle_inicis.inc.php');
include_once(OM_SHOP_PATH.'/inicis/libs/HttpClient.php');
include_once(OM_SHOP_PATH.'/inicis/libs/json_lib.php');

$request_mid = isset($_POST['mid']) ? clean_xss_tags($_POST['mid']) : '';

if( ($request_mid != $default['de_inicis_mid']) ){
	alert("요청된 mid 와 설정된 mid 가 틀립니다.");
}

$orderNumber = isset($_POST['orderNumber']) ? preg_replace("/[ #\&\+%@=\/\\\:;,\.'\"\^`~|\!\?\*$#<>()\[\]\{\}]/i", "", strip_tags($_POST['orderNumber'])) : 0;
$session_order_num = get_session('ss_order_inicis_id');

if( !$orderNumber ){
	alert("주문번호가 없습니다.");
}

$sql = " select * from shop_order_data where od_id = '$orderNumber' ";
$row = sql_fetch($sql);

if( empty($row) ){
	alert("임시 주문정보가 저장되지 않았습니다.");
}

$data = unserialize(base64_decode($row['dt_data']));

$params = array();
$var_datas = array();

foreach($data as $key=>$value) {
	if(is_array($value)) {
		foreach($value as $k=>$v) {
			$_POST[$key][$k] = $params[$key][$k] = clean_xss_tags(strip_tags($v));
		}
	} else {
		$_POST[$key] = $params[$key] = clean_xss_tags(strip_tags($value));
	}
}

foreach ($params as $key=>$value) {
	$var_datas[$key] = $value;

	$$key = $value;
}

include_once(OM_SHOP_PATH.'/orderformresult.php');
?>