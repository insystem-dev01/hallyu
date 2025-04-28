<?php
// 이 파일은 새로운 파일 생성시 반드시 포함되어야 함

if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

if($sca) {
	$sql_search .= " and (ca_id like '$sca%' or ca_id2 like '$sca%' or ca_id3 like '$sca%') ";
}

// 검색어
if($stx) {
    switch($sfl) {
        case "gname" :
		case "explan" :
		case "maker" :
		case "origin" :
		case "model" :
            $sql_search .= " and $sfl like '%$stx%' ";
            break;
		case "seller_name" :
			$sql_search .= " and mb_id = (select seller_code from shop_seller where company_name like '%$stx%') ";
            break;
        default :
            $sql_search .= " and $sfl like '$stx%' ";
            break;
    }
}

// 기간검색
if($fr_date && $to_date) {
	if($q_date_field == "sale_date") {
		$sql_search .= " and sb_date >= '$fr_date 00:00:00' and eb_date <= '$to_date 23:59:59' ";
	} else {
		$sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
	}
} else if($fr_date && !$to_date) {
	$sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
} else if(!$fr_date && $to_date) {
	$sql_search .= " and $q_date_field between '$to_date 00:00:00' and '$to_date 23:59:59' ";
}

// 브랜드
if(isset($q_brand) && $q_brand)
	$sql_search .= " and brand_uid = '$q_brand' ";

// 배송가능 지역
if(isset($q_zone) && $q_zone)
	$sql_search .= " and zone = '$q_zone' ";

// 상품재고
if($fr_stock && $to_stock)
	$sql_search .= " and $q_stock_field between '$fr_stock' and '$to_stock' ";

// 상품가격
if($fr_price && $to_price)
	$sql_search .= " and $q_price_field between '$fr_price' and '$to_price' ";

// 판매여부
if(isset($q_isopen) && is_numeric($q_isopen))
	$sql_search .= " and isopen='$q_isopen' ";

// 과세유형
if(isset($q_notax) && is_numeric($q_notax))
	$sql_search .= " and notax = '$q_notax' ";

// 상품 필수옵션
if(isset($q_option) && is_numeric($q_option)) {
	if($q_option)
		$sql_search .= " and opt_subject <> '' ";
	else
		$sql_search .= " and opt_subject = '' ";
}

// 상품 추가옵션
if(isset($q_supply) && is_numeric($q_supply)) {
	if($q_supply)
		$sql_search .= " and spl_subject <> '' ";
	else
		$sql_search .= " and spl_subject = '' ";
}

// 오픈상태
if(isset($q_open) && is_numeric($q_open)) {
	if($q_open == '0') {
		$sql_search .= " and sb_date > now()";
	} else {
		$sql_search .= " and sb_date < now() and eb_date > now()";
	}
}
?>