<div id="smb_my">
	<section id="smb_my_ov">
        <h2>회원정보 개요</h2>
        <ul>
            <li>보유쿠폰<a href="<?php echo base_url(); ?>m/shop/coupon"><?php echo $cp_countTag; ?></a></li>
            <li>보유포인트<a href="<?php echo base_url(); ?>m/shop/point"><?php echo $mbPoint; ?></a></li>
            <li>보유예치금<a href="<?php echo base_url(); ?>m/shop/mileage"><?php echo $mbmileage; ?></a></li>
        </ul>
        <dl>
            <dt>연락처</dt>
            <dd><?php echo $mbTel; ?></dd>
            <dt>E-Mail</dt>
            <dd><?php echo $mbEmail; ?></dd>
            <dt>최종접속일시</dt>
            <dd><?php echo $member['today_login']; ?></dd>
            <dt>회원가입일시</dt>
            <dd><?php echo $member['reg_time']; ?></dd>
            <dt class="ov_addr">주소</dt>
            <dd class="ov_addr"><?php echo $mbAddr; ?></dd>
        </dl>
    </section>
    <section id="smb_my_od">
        <h2 class="anc_tit">최근 주문내역<span class="fr"><a href="<?php echo base_url(); ?>m/shop/orderinquiry" class="btn_txt">더보기<i class="fa fa-angle-right"></i></a></span></h2>
		<ul id="sod_inquiry">
            <?=$mypageTable?>
        </ul>
    </section>
    <section id="smb_my_wish">
        <h2 class="anc_tit">최근 위시리스트<span class="fr"><a href="<?php echo base_url(); ?>m/shop/wish" class="btn_txt">더보기<i class="fa fa-angle-right"></i></a></span></h2>
        <ul>
            <?=$wishList?>
        </ul>
    </section>
</div>