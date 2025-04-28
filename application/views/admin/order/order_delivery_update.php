<?=$headTitle?>

<h2>총 건수</h2>
<div class="tbl_frm02">
	<table>
        <colgroup>
            <col class="w180">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <th scope="row">총배송건수</th>
                <td><?php echo number_format($total_count); ?>건</td>
            </tr>
            <tr>
                <th scope="row">완료건수</th>
                <td><?php echo number_format($succ_count); ?>건</td>
            </tr>
            <tr>
                <th scope="row">실패건수</th>
                <td><?php echo number_format($fail_count); ?>건</td>
            </tr>
            <?=$fail_countTag?>
        </tbody>
	</table>
</div>

<div class="btn_confirm">
	<a href="<?php echo base_url(); ?>admin/order?code=delivery" class="btn_large">확인</a>
</div>

<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="desc02">
			<p>ㆍ엑셀자료는 1회 업로드당 최대 1,000건까지 이므로 1,000건씩 나누어 업로드 하시기 바랍니다.</p>
			<p>ㆍ형식은 <strong>배송처리용 엑셀파일</strong>을 다운로드하여 배송 정보를 입력하시면 됩니다.</p>
			<p>ㆍ수정 완료 후 엑셀파일을 업로드하시면 배송정보가 일괄등록됩니다.</p>
			<p>ㆍ엑셀파일을 저장하실 때는 <strong>Excel 97 - 2003 통합문서 (*.xls)</strong> 로 저장하셔야 합니다.</p>
			<p>ㆍ주문상태가 배송준비인 주문에 한해 엑셀파일이 생성됩니다.</p>
			<p>ㆍ배송회사명은 <a href="<?php echo base_url(); ?>admin/config?code=baesong"><strong>환경설정 > 배송/교환/반품 설정</strong></a>에 등록된 배송업체명과 반드시 일치해야 합니다.</p>
			<p>ㆍ엑셀데이터는 2번째 라인부터 저장되므로 타이틀은 지우시면 안됩니다.</p>
		</div>
	 </div>
</div>

<script>
$(function() {
	// 새로고침(F5) 막기
	$(document).keydown(function (e) {		
		if(e.which === 116) {
			if(typeof event == "object") {
				event.keyCode = 0;
			}
			return false;
		} else if(e.which === 82 && e.ctrlKey) {
			return false;
		}
	});
});
</script>
<?=$headTitleEnd?>