<?=$headTitle?>
<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/default_update" enctype="MULTIPART/FORM-DATA">
<input type="hidden" name="token" value="">

<h2>사업자정보</h2>
<div class="tbl_frm01">
	<table>
	<colgroup>
		<col class="w180">
		<col>
	</colgroup>
	<tbody>
	<tr>
		<th scope="row"><label for="shop_name">쇼핑몰명</label></th>
		<td>
			<input type="text" name="shop_name" value="<?php echo $config['shop_name']; ?>" id="shop_name" class="frm_input" size="30">
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="shop_name_us">쇼핑몰 영문명</label></th>
		<td>
			<input type="text" name="shop_name_us" value="<?php echo $config['shop_name_us']; ?>" id="shop_name_us" class="frm_input" size="30">
		</td>
	</tr>
	<tr>
		<th scope="row">사업자유형</th>
		<td>
			<?php echo $company_type[0] ?>
			<?php echo $company_type[1] ?>
			<?php echo $company_type[2] ?>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="company_name">회사명</label></th>
		<td>
			<input type="text" name="company_name" value="<?php echo $config['company_name']; ?>" id="company_name" class="frm_input" size="30">
			<em>세무서에 등록되어 있는 회사명 입력</em>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="company_owner">대표자명</label></th>
		<td>
			<input type="text" name="company_owner" value="<?php echo $config['company_owner']; ?>" id="company_owner" class="frm_input" size="30">
			<em>예) 홍길동</em>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="company_saupja_no">사업자등록번호</label></th>
		<td>
			<input type="text" name="company_saupja_no" value="<?php echo $config['company_saupja_no']; ?>" id="company_saupja_no" class="frm_input" size="30">
			<em>예) 000-00-00000</em>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="company_item">업태</label></th>
		<td>
			<input type="text" name="company_item" value="<?php echo $config['company_item']; ?>" id="company_item" class="frm_input" size="30">
			<em>예) 소매업</em>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="company_service">종목</label></th>
		<td>
			<input type="text" name="company_service" value="<?php echo $config['company_service']; ?>" id="company_service" class="frm_input" size="30">
			<em>예) 전자상거래업</em>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="company_zip">사업장우편번호</label></th>
		<td>
			<input type="text" name="company_zip" maxlength="5" value="<?php echo $config['company_zip']; ?>" id="company_zip" class="frm_input" size="5">
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="company_addr">사업장주소</label></th>
		<td>
			<input type="text" name="company_addr" value="<?php echo $config['company_addr']; ?>" id="company_addr" class="frm_input" size="60">
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="tongsin_no">통신판매업신고번호</label></th>
		<td>
			<input type="text" name="tongsin_no" value="<?php echo $config['tongsin_no']; ?>" id="tongsin_no" class="frm_input" size="30">
			<em>예) <?php echo $OM_TIME_YEAR.'-서울강남-0000호'; ?></em>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="company_tel">대표전화번호</label></th>
		<td>
			<input type="text" name="company_tel" value="<?php echo $config['company_tel']; ?>" id="company_tel" class="frm_input" size="30">
			<em>예) 1544-0000, 070-0000-0000</em>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="company_fax">팩스번호</label></th>
		<td>
			<input type="text" name="company_fax" value="<?php echo $config['company_fax']; ?>" id="company_fax" class="frm_input" size="30">
			<em>예) 02-0000-0000</em>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="info_name">정보책임자 이름</label></th>
		<td>
			<input type="text" name="info_name" value="<?php echo $config['info_name']; ?>" id="info_name" class="frm_input" size="30">
			<em>예) 홍길동</em>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="info_email">정보책임자 e-mail</label></th>
		<td>
			<input type="text" name="info_email" value="<?php echo $config['info_email']; ?>" id="info_email" class="email frm_input" size="30">
			<em>예) help@domain.com</em>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="admin_seal">사업자인감</label></th>
		<td>
			<input type="file" name="admin_seal" id="admin_seal">
			<?=$adminSealTag?>
			<em>사이즈 (80px * 80px)</em>
			<div id="vimg" class="banner_or_img dn">
				<img src="<?php echo $seal; ?>">
			</div>
			<?php echo $help; ?>
		</td>
	</tr>
	</tbody>
	</table>
</div>

<script>
$(function(){
	$(".wimg_vs").bind("click", function() {
		var $img_display = $("#vimg");

		$img_display.toggle();

		if($img_display.is(":visible")) {
			$(this).text($(this).text().replace("보기", "닫기"));
		} else {
			$(this).text($(this).text().replace("닫기", "보기"));
		}
	});
});
</script>

<h2>CS 운영시간</h2>
<div class="tbl_frm01">
	<table>
	<colgroup>
		<col class="w180">
		<col>
	</colgroup>
	<tbody>
	<tr>
		<th scope="row"><label for="company_hours">상담가능시간</label></th>
		<td>
			<input type="text" name="company_hours" value="<?php echo $config['company_hours']; ?>" id="company_hours" class="frm_input" size="60">
			<em>예) 오전9시~오후6시</em>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="company_lunch">점심시간</label></th>
		<td>
			<input type="text" name="company_lunch" value="<?php echo $config['company_lunch']; ?>" id="company_lunch" class="frm_input" size="60">
			<em>예) 오후12시~오후1시</em>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="company_close">휴무일</label></th>
		<td>
			<input type="text" name="company_close" value="<?php echo $config['company_close']; ?>" id="company_close" class="frm_input" size="60">
			<em>예) 토요일,공휴일 휴무</em>
		</td>
	</tr>
	</tbody>
	</table>
</div>

<?php if($member['grade'] == "99") { ?>
<h2>도메인 설정</h2>
<div class="tbl_frm01">
	<table>
	<colgroup>
		<col class="w180">
		<col>
	</colgroup>
	<tbody>
	<tr>
		<th scope="row"><label for="admin_shop_url">대표도메인</label></th>
		<td>
			<span class="sitecode">www.</span><input type="text" name="admin_shop_url" value="<?php echo $config['admin_shop_url']; ?>" id="admin_shop_url" class="frm_input" size="40">
			<div class="padt5">
				<input type="checkbox" name="admin_reg_yes" value="1" id="admin_reg_yes"<?php echo $adminRegYesTag; ?>>
				<label for="admin_reg_yes">본사에서 회원가입을 받지 않음 (분양몰에서만 회원가입 허용)</label>
			</div>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="admin_reg_msg">본사에서 회원가입 거부시<br>출력 할 경고메세지</label></th>
		<td>
			<textarea name="admin_reg_msg" id="admin_reg_msg" class="frm_textbox wfull" rows="3"><?php echo $config['admin_reg_msg']; ?></textarea>
		</td>
	</tr>
	</tbody>
	</table>
</div>

<h2>이미지사이즈 설정</h2>
<div class="local_cmd01">
	<p>※ 아래 이미지사이즈 설정은 관리자페이지에서 노출용으로 사용되므로 실제 사이즈가 반경되지는 않습니다.</p>
</div>
<div class="tbl_frm01">
	<table class="tablef">
	<colgroup>
		<col class="w180">
		<col>
		<col class="w180">
		<col>
	</colgroup>
	<tbody>
	<tr>
		<th scope="row">PC 쇼핑몰로고</th>
		<td class="td_label">
			<label>가로 : <input type="text" name="de_logo_wpx" value="<?php echo $default['de_logo_wpx']; ?>" class="frm_input w50"> 픽셀</label>
			<label>세로 : <input type="text" name="de_logo_hpx" value="<?php echo $default['de_logo_hpx']; ?>" class="frm_input w50"> 픽셀</label>
		</td>
		<th scope="row">모바일 쇼핑몰로고</th>
		<td class="td_label">
			<label>가로 : <input type="text" name="de_mobile_logo_wpx" value="<?php echo $default['de_mobile_logo_wpx']; ?>" class="frm_input w50"> 픽셀</label>
			<label>세로 : <input type="text" name="de_mobile_logo_hpx" value="<?php echo $default['de_mobile_logo_hpx']; ?>" class="frm_input w50"> 픽셀</label>
		</td>
	</tr>
	<tr>
		<th scope="row">PC 메인배너</th>
		<td class="td_label">
			<label>가로 : <input type="text" name="de_slider_wpx" value="<?php echo $default['de_slider_wpx']; ?>" class="frm_input w50"> 픽셀</label>
			<label>세로 : <input type="text" name="de_slider_hpx" value="<?php echo $default['de_slider_hpx']; ?>" class="frm_input w50"> 픽셀</label>
		</td>
		<th scope="row">모바일 메인배너</th>
		<td class="td_label">
			<label>가로 : <input type="text" name="de_mobile_slider_wpx" value="<?php echo $default['de_mobile_slider_wpx']; ?>" class="frm_input w50"> 픽셀</label>
			<label>세로 : <input type="text" name="de_mobile_slider_hpx" value="<?php echo $default['de_mobile_slider_hpx']; ?>" class="frm_input w50"> 픽셀</label>
		</td>
	</tr>
	<tr>
		<th scope="row">상품 소이미지</th>
		<td class="td_label">
			<label>가로 : <input type="text" name="de_item_small_wpx" value="<?php echo $default['de_item_small_wpx']; ?>" class="frm_input w50"> 픽셀</label>
			<label>세로 : <input type="text" name="de_item_small_hpx" value="<?php echo $default['de_item_small_hpx']; ?>" class="frm_input w50"> 픽셀</label>
		</td>
		<th scope="row">상품 중이미지</th>
		<td class="td_label">
			<label>가로 : <input type="text" name="de_item_medium_wpx" value="<?php echo $default['de_item_medium_wpx']; ?>" class="frm_input w50"> 픽셀</label>
			<label>세로 : <input type="text" name="de_item_medium_hpx" value="<?php echo $default['de_item_medium_hpx']; ?>" class="frm_input w50"> 픽셀</label>
		</td>
	</tr>
	</tbody>
	</table>
</div>

<h2>폐쇄몰설정</h2>
<div class="tbl_frm01">
	<table>
	<colgroup>
		<col class="w180">
		<col>
	</colgroup>
	<tbody>
	<tr>
		<th scope="row">폐쇄몰 사용여부</th>
		<td><label><input type="checkbox" name="shop_intro_yes" value="1"<?php echo $shopIntroYesTag; ?>> 사용함</label></td>
	</tr>
	<tr>
		<th scope="row">폐쇄몰 회원인증</th>
		<td>
			<label><input type="checkbox" name="cert_admin_yes" value="1"<?php echo $certAdminYesTag; ?>> 관리자 인증 후 회원가입 <span class="fc_197 fs11 vam">(체크안하면 회원가입 완료후 바로 쇼핑몰 이용이 가능합니다)</span></label>
		</td>
	</tr>
	<tr>
		<th scope="row">폐쇄몰 가입인증 권한</th>
		<td>
			<label><input type="checkbox" name='cert_partner_yes' value="1"<?php echo $certPartnerYesTag; ?>> 가맹점도 인증권한 부여 <span class="fc_197">(각 가맹점에 소속 된 회원일경우 직접 인증할 수 있습니다)</span></label>
		</td>
	</tr>
	</tbody>
	</table>
</div>

<h2>정보노출설정</h2>
<div class="tbl_frm01">
	<table>
	<colgroup>
		<col class="w180">
		<col>
	</colgroup>
	<tbody>
	<tr>
		<th scope="row">구매후기</th>
		<td><label><input type="checkbox" name="de_review_wr_use" value="1"<?php echo $deReviewWrUseTag; ?>> 작성된 분양몰에서만 노출 <span class="fc_197">(체크안하면 통합노출)</span></label></td>
	</tr>
	<tr>
		<th scope="row">게시판글</th>
		<td><label><input type="checkbox" name="de_board_wr_use" value="1"<?php echo $deBoardWrUseTag; ?>> 작성된 분양몰에서만 노출 <span class="fc_197">(체크안하면 통합노출)</span></label></td>
	</tr>
	</tbody>
	</table>
</div>
<?php } ?>

<h2>주문시 포인트결제 설정</h2>
<div class="tbl_frm01">
	<table class="tablef">
	<colgroup>
		<col class="w180">
		<col>
	</colgroup>
	<tbody>
	<tr>
		<th scope="row">포인트결제 사용</th>
		<td>
			<label><input type="checkbox" name="usepoint_yes" value="1"<?php echo $usepoint_yesTag; ?>> 사용함</label>
			<?php echo help('체크하지 않으면 주문시 포인트결제가 되지 않습니다.'); ?>
		</td>
	</tr>
	<tr>
		<th scope="row">최소 결제포인트</th>
		<td>
			<input type="text" name="usepoint" value="<?php echo number_format($config['usepoint']); ?>" class="frm_input w80" onkeyup="addComma(this)"> 원
			<?php echo help('회원의 포인트가 설정값 이상일 경우만 주문시 결제에 사용할 수 있습니다.'); ?>
		</td>
	</tr>
	</tbody>
	</table>
</div>

<h2>주문시 예치금결제 설정</h2>
<div class="tbl_frm01">
	<table class="tablef">
	<colgroup>
		<col class="w180">
		<col>
	</colgroup>
	<tbody>
	<tr>
		<th scope="row">예치금결제 사용</th>
		<td>
			<label><input type="checkbox" name="usemileage_yes" value="1"<?php echo $usemileage_yesTag; ?>> 사용함</label>
			<?php echo help('체크하지 않으면 주문시 예치금결제가 되지 않습니다.'); ?>
		</td>
	</tr>
	<tr>
		<th scope="row">최소 결제예치금</th>
		<td>
			<input type="text" name="usemileage" value="<?php echo number_format($config['usemileage']); ?>" class="frm_input w80" onkeyup="addComma(this)"> 원
			<?php echo help('회원의 예치금이 설정값 이상일 경우만 주문시 결제에 사용할 수 있습니다.'); ?>
		</td>
	</tr>
	</tbody>
	</table>
</div>

<h2>기타설정</h2>
<div class="tbl_frm01">
	<table class="tablef">
	<colgroup>
		<col class="w180">
		<col>
		<col class="w180">
		<col>
	</colgroup>
	<tbody>
	<tr>
		<th scope="row">찜 보관일수</th>
		<td><input type="text" name="de_wish_keep_term" value="<?php echo $default['de_wish_keep_term']; ?>" class="frm_input w50"> 일 이후 자동삭제</td>
		<th scope="row">장바구니 보관일수</th>
		<td><input type="text" name="de_cart_keep_term" value="<?php echo $default['de_cart_keep_term']; ?>" class="frm_input w50"> 일 이후 자동삭제</td>
	</tr>
	<tr>
		<th scope="row">미입금 주문내역</th>
		<td><input type="text" name="de_misu_keep_term" value="<?php echo $default['de_misu_keep_term']; ?>" class="frm_input w50"> 일 이후 자동취소</td>
		<th scope="row">로그인 포인트</th>
		<td><input type="text" name="login_point" value="<?php echo number_format($config['login_point']); ?>" class="frm_input w80" onkeyup="addComma(this)"> P</td>
	</tr>
	<tr>
		<th scope="row">구매확정 강제승인</th>
		<td><input type="text" name="de_final_keep_term" value="<?php echo $default['de_final_keep_term']; ?>" class="frm_input w50"> 일 이후 자동승인</td>
		<th scope="row">온라인쿠폰</th>
		<td><label><input type="checkbox" name="coupon_yes" value="1"<?php echo $coupon_yesTag; ?>> 사용함</label></td>
	</tr>
	<tr>
		<th scope="row">이미지 도용방지</th>
		<td><label><input type="checkbox" name="mouseblock_yes" value="1"<?php echo $mouseblock_yesTag; ?>> 드레그, 마우스 우클릭 차단</label></td>
		<th scope="row">인쇄용쿠폰</th>
		<td><label><input type="checkbox" name="gift_yes" value="1"<?php echo $gift_yesTag; ?>> 사용함</label></td>
	</tr>
	<tr>
		<th scope="row">페이지 표시 수</th>
		<td><input type="text" name="write_pages" value="<?php echo $config['write_pages']; ?>" class="frm_input w50"> 페이지씩 표시</td>
		<th scope="row">모바일 페이지 표시 수</th>
		<td><input type="text" name="mobile_pages" value="<?php echo $config['mobile_pages']; ?>" class="frm_input w50"> 페이지씩 표시</td>
	</tr>
	</tbody>
	</table>
</div>

<?php if($member['grade'] == "99") { ?>
<h2>부분초기화</h2>
<div class="tbl_frm01">
	<table>
	<colgroup>
		<col class="w180">
		<col>
	</colgroup>
	<tbody>
	<tr>
		<th scope="row">DB 부분초기화</th>
		<td class="td_label">
			<label><input type="checkbox" id="motorCheckbox" value="1"> <b>초기화 체크박스 활성</b></label>
			<p class="clear_fld mart7 dn">
				<label><input type="checkbox" name="clear_1" value="1"> 전체회원</label>
				<label><input type="checkbox" name="clear_2" value="1"> 주문내역</label>
				<label><input type="checkbox" name="clear_3" value="1"> </label>
				<label><input type="checkbox" name="clear_4" value="1"> 전체상품</label>
				<label><input type="checkbox" name="clear_5" value="1"> 회원포인트</label>
				<label><input type="checkbox" name="clear_6" value="1"> 통계분석</label>
				<label><input type="checkbox" name="clear_7" value="1"> 검색키워드</label>
				<label><input type="checkbox" name="clear_8" value="1"> 상품브랜드</label>
			</p>
		</td>
	</tr>
	</tbody>
	</table>
</div>
<?php } ?>

<div class="btn_confirm">
	<input type="submit" value="저장" class="btn_large" accesskey="s">
</div>
</form>

<script>
$(function() {
	$("#motorCheckbox").on("click", function() {
		if($(this).is(":checked"))
			$(".clear_fld").show();
		else
			$(".clear_fld").hide();

    });
});
</script>
<?=$headTitleEnd?>