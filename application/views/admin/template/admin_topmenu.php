<header id="hd">
	<div id="hd_wrap">
		<h1><?php echo $config['shop_name']; ?></h1>
		<div id="logo"><a href="<?php echo base_url();?>admin/index"><img src="<?php echo base_url(); ?>dist/img/admin/logo.png" alt="<?php echo $config['shop_name']; ?> 관리자"></a></div>
		<div id="tnb">
			<ul>
				<li><?php echo $OM_VERSION; ?></li>
				<li><?php echo $super['vi_history']; ?></li>
				<li><a href="<?php echo base_url();?>admin/config?code=super">관리자정보</a></li>
				<li><a href="<?php echo base_url();?>admin/index">관리자홈</a></li>
				<li><a href="<?php echo base_url();?>" target="_blank">쇼핑몰</a></li>
				<li id="tnb_logout"><a href="<?php echo base_url();?>process/logout">로그아웃</a></li>
			</ul>
		</div>
        <nav id="gnb">
			<h2>관리자 주메뉴</h2>
			<ul id="gnb_1dul">
				<li class="gnb_1dli <?=$ADMIN_MENU1?>">
					<a href="<?php echo base_url(); ?>admin/member?code=list" class="gnb_1da"><?=$ADMIN_MENU_Text['ADMIN_MENU1']?></a>
				</li>
				<li class="gnb_1dli <?=$ADMIN_MENU3?>">
					<a href="<?php echo base_url(); ?>admin/seller?code=list" class="gnb_1da"><?=$ADMIN_MENU_Text['ADMIN_MENU3']?></a>
				</li>
				<li class="gnb_1dli <?=$ADMIN_MENU4?>">
					<a href="<?php echo base_url(); ?>admin/category?code=list" class="gnb_1da"><?=$ADMIN_MENU_Text['ADMIN_MENU4']?></a>
				</li>
				<li class="gnb_1dli <?=$ADMIN_MENU5?>">
					<a href="<?php echo base_url(); ?>admin/goods?code=list" class="gnb_1da"><?=$ADMIN_MENU_Text['ADMIN_MENU5']?></a>
				</li>
				<li class="gnb_1dli <?=$ADMIN_MENU6?>">
					<a href="<?php echo base_url(); ?>admin/order?code=list" class="gnb_1da"><?=$ADMIN_MENU_Text['ADMIN_MENU6']?></a>
				</li>
				<li class="gnb_1dli <?=$ADMIN_MENU7?>">
					<a href="<?php echo base_url(); ?>admin/visit?code=hour" class="gnb_1da"><?=$ADMIN_MENU_Text['ADMIN_MENU7']?></a>
				</li>
				<li class="gnb_1dli <?=$ADMIN_MENU8?>">
					<a href="<?php echo base_url(); ?>admin/help?code=qa" class="gnb_1da"><?=$ADMIN_MENU_Text['ADMIN_MENU8']?></a>
				</li>
				<li class="gnb_1dli <?=$ADMIN_MENU9?>">
					<a href="<?php echo base_url(); ?>admin/design?code=banner_list" class="gnb_1da"><?=$ADMIN_MENU_Text['ADMIN_MENU9']?></a>
				</li>
				<li class="gnb_1dli <?=$ADMIN_MENU10?>">
					<a href="<?php echo base_url(); ?>admin/config?code=default" class="gnb_1da"><?=$ADMIN_MENU_Text['ADMIN_MENU10']?></a>
				</li>
			</ul>
		</nav>
	</div>
</header>
<div id="wrapper">
	<?=$adminSnb?>
	<?=$content?>