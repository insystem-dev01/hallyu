<div id="main_wrap">
	<section>
		<h2>전체 주문통계<a href="<?php echo base_url(); ?>admin/order?code=list" class="btn_small">주문내역 바로가기</a></h2>
		<div class="order_vbx">
			<dl class="od_bx1">
				<dt>전체 주문현황</dt>
				<dd>
					<p class="ddtit">총 주문건수</p>
					<p><?php echo number_format($sodrr['cnt']); ?></p>
				</dd>
				<dd class="total">
					<p class="ddtit">총 주문액</p>
					<p><?php echo number_format($sodrr['price']); ?></p>
				</dd>
			</dl>

			<dl class="od_bx2">
				<dt>주문상태 현황</dt>
				<dd>
					<p class="ddtit">입금대기</p>
					<p><?php echo number_format($sodr1['cnt']); ?></p>
				</dd>
				<dd>
					<p class="ddtit">입금완료</p>
					<p><?php echo number_format($sodr2['cnt']); ?></p>
				</dd>
				<dd>
					<p class="ddtit">배송준비</p>
					<p><?php echo number_format($sodr3['cnt']); ?></p>
				</dd>
				<dd>
					<p class="ddtit">배송중</p>
					<p><?php echo number_format($sodr4['cnt']); ?></p>
				</dd>
				<dd>
					<p class="ddtit">배송완료</p>
					<p><?php echo number_format($sodr5['cnt']); ?></p>
				</dd>
			</dl>
			<dl class="od_bx2">
				<dt>구매확정/클래임 현황</dt>
				<dd>
					<p class="ddtit">구매미확정</p>
					<p><?php echo number_format($final['cnt']); ?></p>
				</dd>
				<dd>
					<p class="ddtit">취소</p>
					<p><?php echo number_format($sodr6['cnt']); ?></p>
				</dd>
				<dd>
					<p class="ddtit">환불</p>
					<p><?php echo number_format($sodr9['cnt']); ?></p>
				</dd>
				<dd>
					<p class="ddtit">반품</p>
					<p><?php echo number_format($sodr7['cnt']); ?></p>
				</dd>
				<dd>
					<p class="ddtit">교환</p>
					<p><?php echo number_format($sodr8['cnt']); ?></p>
				</dd>
			</dl>
		</div>
	</section>

	<section class="sidx_head01">
		<h2>최근 주문내역<a href="<?php echo base_url(); ?>admin/order?code=list" class="btn_small">주문내역 바로가기</a></h2>
		<table>
		<thead>
		<tr>
			<th scope="col">주문번호</th>
			<th scope="col">주문자명</th>
			<th scope="col">수령자명</th>
			<th scope="col">전화번호</th>
			<th scope="col">결제방법</th>
			<th scope="col">총주문액</th>
			<th scope="col">주문일시</th>
		</tr>
		</thead>
		<tbody>
		<?=$recentOdrStr?>
		</tbody>
		</table>
	</section>

	<section class="sidx_head01">
		<h2>최근 회원가입<a href="<?php echo base_url(); ?>admin/member?code=list" class="btn_small">회원관리 바로가기</a></h2>
		<table>
		<thead>
		<tr>
			<th scope="col">이름</th>
			<th scope="col">아이디</th>
			<th scope="col">레벨</th>
			<th scope="col">이메일</th>
			<th scope="col">접속횟수</th>
			<th scope="col">추천인</th>
			<th scope="col">가입일시</th>
		</tr>
		</thead>
		<tbody>
		<?=$recentJoinStr?>
		</tbody>
		</table>
	</section>
</div>