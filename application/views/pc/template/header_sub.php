<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
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
<?=$add_meta?>
<title><?=$om_head_title?></title>
<link rel="stylesheet" href="<?php echo base_url(); ?>css/default.css?ver=<?=time()?>">
<link rel="stylesheet" href="<?php echo base_url(); ?>dist/css/common.css?ver=<?=time();?>">
<?=$favicon?>
<script src="<?php echo base_url(); ?>js/jquery-1.8.3.min.js"></script>
<script src="<?php echo base_url(); ?>js/shop.js"></script>
<script src="<?php echo base_url(); ?>js/jquery-ui-1.10.3.custom.js"></script>
<script src="<?php echo base_url(); ?>js/common.js?ver=<?=time()?>"></script>
<script src="<?php echo base_url(); ?>js/slick.js"></script>
<script src="<?=base_url()?>js/swiper-bundle.min.js?v=1"></script>
<link rel="stylesheet" href="<?=base_url()?>dist/js/swiper-bundle.min.css"/>
<?=$mouseRight?>
<?=$headInner?>
<body<?=$bodyScript?>>
    <input type="hidden" id="base_url" value="<?=base_url()?>">