<?=$aside_my?>
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>마이페이지<i>&gt;</i><?php echo $title; ?></p>
	</h2>

	<nav id="tab_cate">
		<ul id="tab_cate_ul">
			<li <?php echo $selected1; ?>><a href="<?php echo base_url(); ?>shop/coupon">사용가능한 쿠폰</a></li>
			<li <?php echo $selected2; ?>><a href="<?php echo base_url(); ?>shop/coupon?sca=1">사용완료 및 기한만료 쿠폰</a></li>
		</ul>
	</nav>
    <p class="pg_cnt">
		<em>총 <?php echo number_format($total_count); ?>건</em>의 쿠폰내역이 있습니다.
	</p>
    <div class="tbl_head02 tbl_wrap">
		<table>
            <colgroup>
                <col class="w50">
                <col>
                <col>
                <col>
                <col>
                <col class="w140">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col">할인쿠폰</th>
                    <th scope="col">할인금액(율)</th>
                    <th scope="col">사용가능대상</th>
                    <th scope="col">사용기한</th>
                    <th scope="col">취득일자</th>
                </tr>
            </thead>
            <tbody>
                <?=$couponTable?>
            </tbody>
		</table>
	</div>
    <?=$pagination?>
</div>