<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>마이페이지</title>
<link rel="stylesheet" href="<?php echo base_url();?>dist/css/mypage.css?ver=<?php echo time(); ?>">
<?=$faviconTag?>

<script src="<?php echo base_url(); ?>js/jquery-1.8.3.min.js"></script>
<script src="<?php echo base_url(); ?>js/shop.js"></script>
<script src="<?php echo base_url(); ?>js/jquery-ui-1.10.3.custom.js"></script>
<script src="<?php echo base_url(); ?>js/common.js?ver=<?php echo time(); ?>"></script>
<script src="<?php echo base_url(); ?>js/categorylist.js?ver=<?php echo time(); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>js/datetimepicker/jquery.datetimepicker.css">
<script src="<?=base_url()?>js/datetimepicker/build/jquery.datetimepicker.full.min.js"></script>
</head>
<body>
    <input type="hidden" id="base_url" value="<?=base_url()?>">
<div id="header"<?=$supplyClass?>>
	<?=$admin_partnerTag?>
	<?=$admin_sellerTag?>
	<div id="tnb">
		<ul>
			<li><?php echo $memberName; ?>님! 접속중..<?php echo $partner_term; ?></li>
			<li>고객센터 : <?php echo $config['company_tel']; ?></li>
			<li><a href="<?php echo $admin_shop_url; ?>">쇼핑몰</a></li>
			<?=$partnerAdminTag?>
			<?=$sellerAdminTag?>
			<li id="tnb_logout"><a href="<?php echo base_url(); ?>process/logout">로그아웃</a></li>
		</ul>
	</div>
</div>
