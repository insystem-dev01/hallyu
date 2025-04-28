<?=$headTitle?>

<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/kakaopay_update">
	<input type="hidden" name="token" value="">

	<h2>카카오페이 서비스</h2>
	<div class="tbl_frm01">
		<table>
			<colgroup>
				<col class="w180">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th scope="row"><label for="de_kakaopay_admin_key">카카오페이 Admin키</label></th>
					<td>                
						<input type="text" name="de_kakaopay_admin_key" value="<?php echo $default['de_kakaopay_admin_key']; ?>" id="de_kakaopay_admin_key" class="frm_input" size="20">
						<a href="https://developers.kakao.com/" target="_blank" class="btn_small grey">카카오페이 서비스신청하기</a>
						<?php echo $help['admin']; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="de_kakaopay_cid">카카오페이 CID키</label></th>
					<td>                
						<input type="text" name="de_kakaopay_cid" value="<?php echo $default['de_kakaopay_cid']; ?>" id="de_kakaopay_cid" class="frm_input" size="50">
						<?php echo $help['code'];?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="btn_confirm">
		<input type="submit" value="저장" class="btn_large" accesskey="s">
	</div>
</form>

<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="hd">ㆍ카카오페이란?</div>
		<div class="desc01">
			<p>ㆍ국내 최초 모든기기, 모든 OS 모든 브라우저에서 지원이 가능하여, 고객이 언제 어디서나 손쉽게 실제 서비스를 이용할 수 있습니다.</p>
			<p>ㆍ모바일/PC 동시 지원 뿐만 아니라 국내 대부분의 쇼핑몰이 지원하지 않는 Mac, Linux에서도 결제가 가능합니다.</p>
			<p>ㆍActive-X, 공인인증서 등 복잡한 절차에 구애 받지 않아 모든 브라우저 환경에서 편리하게 결제 서비스를 이용할 수 있습니다.</p>
		</div>
	<!-- 	<div class="hd">ㆍ서류 접수처</div>
		<div class="desc01">
			<p>ㆍ(07320) 서울특별시 영등포구 여의대로 24 FKI타워 28층 스마트페이먼트팀 카카오페이 신규계약 담당자앞</p>
		</div> -->
		<div class="hd">ㆍ문의 및 상담</div>
		<div class="desc01">
			<p>ㆍ상담시간 : 오전 09:00~오후6:00 <strong class="fc_red">주말 및 공휴일 휴무</strong></p>
			<p>ㆍ고객센터 : 1:1 문의 1644-7405 / https://pay-cs-web.kakao.com/inquiry</p>
			<p>ㆍ홈페이지 : <a href="https://pg.kakao.com" target="_blank">https://pg.kakao.com</a></p>
		</div>
	</div>
</div>

<?=$headTitleEnd?>