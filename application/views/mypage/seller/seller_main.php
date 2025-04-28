<?=$snbTagStart?>
<?=$admin_head_sub?>
<div id="main_wrap">
	<section>
		<h2>전체 주문통계<a href="<?php echo base_url(); ?>mypage/page?code=seller_odr_list" class="btn_small">주문내역 바로가기</a></h2>
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
				<dd>
					<p class="ddtit">구매확정</p>
					<p><?php echo number_format($final1['cnt']); ?></p>
				</dd>
			</dl>
			<dl class="od_bx2">
				<dt>구매확정/클래임 현황</dt>
				<dd>
					<p class="ddtit">구매미확정</p>
					<p><?php echo number_format($final2['cnt']); ?></p>
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
		<h2>최근 주문내역<a href="<?php echo base_url(); ?>mypage/page?code=seller_odr_list" class="btn_small">주문내역 바로가기</a></h2>
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
                <?=$recentOdr?>
            </tbody>
		</table>
	</section>
    <section>
		<table class="wfull">
            <tr>
                <td width="49.5%" valign="top" class="sidx_head01">
                    <h2>공지사항<a href="<?php echo base_url(); ?>bbs/noticeList?boardid=20" class="btn_small">바로가기</a></h2>
                    <table>
                        <colgroup>
                            <col width="20%">
                            <col width="80%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col">등록일</th>
                                <th scope="col">제목</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?=$noticeStr?>
                        </tbody>
                    </table>
			    </td>
                <td width="1%"></td>
                <td width="49.5%" valign="top" class="sidx_head01">
                    <h2>질문과답변<a href="<?php echo base_url(); ?>bbs/noticeList?boardid=21" class="btn_small">바로가기</a></h2>
                    <table>
                        <colgroup>
                            <col width="20%">
                            <col width="80%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col">등록일</th>
                                <th scope="col">제목</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?=$qnaStr?>
                        </tbody>
                    </table>
			    </td>
		    </tr>
		</table>
	</section>
</div>
<?=$admin_footer_sub?>
<?=$snbTagEnd?>