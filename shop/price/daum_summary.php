<?php
include_once('./_common.php');

ob_start();

header("Content-Type: text/html; charset=utf-8");

/*
구분 태그명 내용    설명    크기
<<<begin>>>     시작    상품시작 알림   필수
<<<mapid>>>     상품ID  해당사 상품 ID  필수
<<<lprice>>>    원판매가(할인전가격)    선택적필수
<<<price>>>     할인적용가  할인후가격  필수
<<<mprice>>>    모바일 할인적용가  할인후가격  선택적필수
<<<pname>>>     상품명  상품명  필수,varchar(500)
<<<pgurl>>>     상품링크    해당 상품으로 갈 상품URL    필수
<<<igurl>>>     이미지링크  상품이미지 링크
                (상품이미지 중 제일 큰이미지링크)   필수,varchar(255)
<<<cate1>>>     카테고리명  대분류명 필수
<<<caid1>>>     카테고리 ID(대분류)  필수
<<<cate2>>>     카테고리명  중분류명
<<<caid2>>>     카테고리 ID(중분류)
<<<cate3>>>     카테고리명  소분류명
<<<caid3>>>     카테고리 ID(소분류)
<<<cate4>>>     카테고리명  세분류명
<<<caid4>>>     카테고리 ID(세분류)

<<<model>>>     모델명
<<<brand>>>     브랜드명
<<<maker>>>     제조사

<<<coupo>>>     쿠폰/제휴쿠폰
<<<mcoupo>>>    모바일 쿠폰/제휴쿠폰
<<<pcard>>>     무이자할부
<<<point>>>     적립금/포인트
<<<deliv>>>     배송비  무료일 때는 0, 유료일 때는 배송금액, 착불은 -1
<<<event>>>     이벤트
<<<weight>>>    가중치값

<<<selid>>>     셀러 ID   선택
<<<insco>>>     별도설치비
<<<ftend>>>     끝알림 필수
*/

$lt = "<<<";
$gt = ">>>";
$shop_url = OM_SHOP_URL;
$data_url = OM_DATA_URL;

$sql_search = " and SUBSTRING(update_time, 1, 10) = '".OM_TIME_YMD."' ";
$sql_common = sql_goods_list($sql_search);
$sql_order  = " order by index_no desc ";

$sql = " select * $sql_common $sql_order ";
$result = sql_query($sql);

for($i=0; $row=sql_fetch_array($result); $i++)
{
    $cate1 = $cate2 = $cate3 = '';
    $caid1 = $caid2 = $caid3 = '';

    if(strlen($row['ca_id']) >= 9) {
        $caid3 = substr($row['ca_id'],0,9);
        $row2 = sql_fetch(" select catename from shop_category where catecode = '$caid3' ");
        $cate3 = $row2['catename'];
    }
    if(strlen($row['ca_id']) >= 6) {
        $caid2 = substr($row['ca_id'],0,6);
        $row2 = sql_fetch(" select catename from shop_category where catecode = '$caid2' ");
        $cate2 = $row2['catename'];
    }
    if(strlen($row['ca_id']) >= 3) {
        $caid1 = substr($row['ca_id'],0,3);
        $row2 = sql_fetch(" select catename from shop_category where catecode = '$caid1' ");
        $cate1 = $row2['catename'];
    }

    $point = (int)$row['gpoint'];
    if( $point ){
        $point .= '원';
    }

    // 배송비계산
	$deliv = get_sendcost_amt2($row['index_no'], $row['goods_price']);

    // 상품이미지
	$img_url = get_it_image_url($row['index_no'], $row['simg2'], 400, 400);

    // 상태
    $class = 'U';
    $stock_qty = get_it_stock_qty($row['index_no']);

    if(substr($row['reg_time'], 0, 10) == OM_TIME_YMD && $row['update_time'] >= $row['reg_time'])
        $class = 'I';

    if($row['isopen'] > 1 || $stock_qty < 0)
        $class = 'D';

    // 수정시간
    $utime = str_replace(array('-', ' ', ':'), '', $row['update_time']);

    $str = "{$lt}begin{$gt}".PHP_EOL;
    $str.= "{$lt}mapid{$gt}{$row['index_no']}".PHP_EOL;
    $str.= "{$lt}price{$gt}{$row['goods_price']}".PHP_EOL;
    $str.= "{$lt}class{$gt}$class".PHP_EOL;
    $str.= "{$lt}utime{$gt}$utime".PHP_EOL;
    $str.= "{$lt}pname{$gt}{$row['gname']}".PHP_EOL;
    $str.= "{$lt}pgurl{$gt}$shop_url/view.php?index_no={$row['index_no']}".PHP_EOL;
    $str.= "{$lt}igurl{$gt}$img_url".PHP_EOL;
    $str.= "{$lt}cate1{$gt}$cate1".PHP_EOL;
    $str.= "{$lt}caid1{$gt}$caid1".PHP_EOL;
    if( $cate2 ){
        $str .= "{$lt}cate2{$gt}$cate2".PHP_EOL;
    }
    if( $caid2 ){
        $str .= "{$lt}caid2{$gt}$caid2".PHP_EOL;
    }
    if( $cate3 ){
        $str .= "{$lt}cate3{$gt}$cate3".PHP_EOL;
    }
    if( $caid3 ){
        $str .= "{$lt}caid3{$gt}$caid3".PHP_EOL;
    }
    if( $row['model'] ){
        $str .= "{$lt}model{$gt}{$row['model']}".PHP_EOL;
    }
    if( $row['brand_nm'] ){
        $str .= "{$lt}brand{$gt}{$row['brand_nm']}".PHP_EOL;
    }
    if( $row['maker'] ){
        $str .= "{$lt}maker{$gt}{$row['maker']}".PHP_EOL;
    }
    $str .= "{$lt}point{$gt}$point".PHP_EOL;
    $str .= "{$lt}deliv{$gt}$deliv".PHP_EOL;
    $str .= "{$lt}ftend{$gt}".PHP_EOL;

	echo iconv('utf-8', 'euc-kr', $str);
}

$content = ob_get_contents();
ob_end_clean();

echo $content;
?>