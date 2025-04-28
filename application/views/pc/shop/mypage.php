<?=$aside_my?>
<!-- 마이페이지 시작 { -->
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>마이페이지</p>
	</h2>
    <!-- 회원정보 개요 시작 { -->
	<div id="smb_my_ov">
		<h2>회원정보 개요</h2>
		<div id="smb_my_act">
			<ul>
				<li><a href="<?php echo base_url(); ?>bbs/member_confirm?url=<?=base_url()?>bbs/register_form" class="btn_lsmall">회원정보수정</a></li>
				<li><a href="<?php echo base_url(); ?>bbs/leave_form" class="btn_lsmall grey">회원탈퇴</a></li>
			</ul>
		</div>
        <table id="smb_my_tbl">
            <tbody>
            <tr>
                <th scope="row">보유포인트</th>
                <td><a href="<?php echo base_url(); ?>shop/point"><b><?php echo $memberPoint;?></b></a></td>
                <th scope="row">보유쿠폰</th>
                <td><a href="<?php echo base_url(); ?>shop/coupon"><b><?php echo $cpCount;?></b></a></td>
            </tr>
            <tr>
                <th scope="row">휴대폰번호</th>
                <td><?php echo $mem_cell; ?></td>
                <th scope="row">E-Mail</th>
                <td><?php echo $mem_email; ?></td>
            </tr>
            <tr>
                <th scope="row">최종접속일시</th>
                <td><?php echo $member['today_login']; ?></td>
                <th scope="row">회원가입일시</th>
                <td><?php echo $member['reg_time']; ?></td>
            </tr>
            <tr>
                <th scope="row">주소</th>
                <td colspan="3"><?php echo $mem_address; ?></td>
            </tr>
            </tbody>
		</table>
	</div>
    <!-- } 회원정보 개요 끝 -->

    <!-- 최근 주문내역 시작 { -->
    <div id="smb_my_od">
		<h2 class="anc_tit">최근 주문내역</h2>
		<div class="tbl_head02 tbl_wrap">
			<table>
                <colgroup>
                    <col class="w90">
                    <col>
                    <col class="w100">
                    <col class="w140">
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">주문일자</th>
                        <th scope="col">상품정보</th>
                        <th scope="col">결제금액</th>
                        <th scope="col">상태</th>
                    </tr>
                </thead>
                <tbody>
                    <?=$orderOrderTable?>
                </tbody>
			</table>
		</div>
        <div class="smb_my_more">
			<a href="<?php echo base_url(); ?>shop/orderinquiry" class="btn_lsmall bx-white">주문내역 더보기</a>
		</div>
	</div>
    <!-- } 최근 주문내역 끝 -->

	<!-- 최근 찜한상품 시작 { -->
    <div id="smb_my_wish">
		<h2 class="anc_tit">최근 찜한상품</h2>
		<div class="tbl_head02 tbl_wrap">
			<table>
			<colgroup>
				<col class="w90">
				<col>
				<col class="w100">
				<col class="w140">
			</colgroup>
			<thead>
                <tr>
                    <th scope="col">이미지</th>
                    <th scope="col">상품명</th>
                    <th scope="col">판매가</th>
                    <th scope="col">보관일시</th>
                </tr>
			</thead>
			<tbody>
                <?=$orderZzimTable?>
            </tbody>
			</table>
		</div>

		<div class="smb_my_more">
			<a href="<?php echo base_url();?>shop/wish" class="btn_lsmall bx-white">찜한상품 더보기</a>
		</div>
	</div>
	<!-- } 최근 찜한상품 끝 -->
</div>
<!-- } 마이페이지 끝 -->