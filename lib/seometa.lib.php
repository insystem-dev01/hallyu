<?php
if(!defined('_OMNIWEB_')) exit; // 개별 페이지 접근 불가

$seometa = array();

if($is_seometa == 'it') { // 상품
	$seometa['author'] = '';
	$seometa['keywords'] = $gs['keywords'];
	$seometa['title'] = $gs['gname'];
	$seometa['description'] = strip_tags($gs['explan']);
	$seometa['type'] = 'product';
	$seometa['url'] = OM_SHOP_URL.'/view.php?index_no='.$gs['index_no'];
	$seometa['img'] = get_it_image_url($gs['index_no'], $gs['simg2'], 300, 300);
}

$seometa['author'] = ($seometa['author']) ? $seometa['author'] : $config['meta_author'];
$seometa['keywords'] = ($seometa['keywords']) ? $seometa['keywords'] : $config['meta_keywords'];
$seometa['title'] = ($seometa['title']) ? $seometa['title'] : $tb['title'];
$seometa['description'] = ($seometa['description']) ? $seometa['description'] : $config['meta_description'];
$seometa['type'] = ($seometa['type']) ? $seometa['type'] : 'website';
$seometa['url'] = ($seometa['url']) ? $seometa['url'] : OM_URL;
$seometa['img'] = ($seometa['img']) ? $seometa['img'] : display_logo_url('sns_logo');

$seometa['title'] = str_replace("\"", "'", $seometa['title']);
$seometa['description'] = str_replace("\"", "'", $seometa['description']);
$seometa['site_name'] = str_replace("\"", "'", $config['shop_name']);
$seometa['url'] = preg_replace("/[\<\>\'\"\\\'\\\"\(\)\^\*]/", "", $seometa['url']);

if($seometa['img'] && preg_match("/(\.(jpg|jpeg|gif|png))$/i", $seometa['img'])) {
	$seometa['img'] = $seometa['img'].'?v='.OM_TIME_YHS;
}
?>
<meta name="author" content="<?php echo $seometa['author']; ?>">
<meta name="description" content="<?php echo $seometa['description']; ?>">
<meta name="keywords" content="<?php echo $seometa['keywords']; ?>">
<!-- Open Graph -->
<meta property="og:type" content="<?php echo $seometa['type']; ?>">
<meta property="og:title" content="<?php echo $seometa['title']; ?>">
<meta property="og:description" content="<?php echo $seometa['description']; ?>">
<meta property="og:url" content="<?php echo $seometa['url']; ?>">
<meta property="og:site_name" content="<?php echo $seometa['site_name']; ?>">
<meta property="og:image" content="<?php echo $seometa['img']; ?>">
<meta property="og:locale" content="ko_KR">
<meta name="robots" content="index,follow">
