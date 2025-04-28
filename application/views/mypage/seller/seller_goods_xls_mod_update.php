<?=$snbTagStart?>
<?=$admin_head_sub?>
<h2>총 건수</h2>
<div class="tbl_frm02">
	<table>
        <colgroup>
            <col class="w180">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <th scope="row">총상품수</th>
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
	<a href="<?=base_url()?>mypage/page?code=seller_goods_xls_mod" class="btn_large">확인</a>
</div>

<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="desc02">
			<p>ㆍ엑셀자료는 1회 업로드당 최대 1,000건까지 이므로 1,000건씩 나누어 업로드 하시기 바랍니다.</p>
			<p>ㆍ엑셀파일을 저장하실 때는 <strong>Excel 97 - 2003 통합문서 (*.xls)</strong>로 저장하셔야 합니다.</p>
			<p>ㆍ상품관리에서 엑셀다운로드 하시고 수정 후 그대로 업로드 하시면 됩니다.</p>
			<p>ㆍ상품관리에서 다운로드 받으신 엑셀데이터는 2번째 라인부터 저장되므로 타이틀은 지우시면 안됩니다.</p>
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
<?=$admin_footer_sub?>
<?=$snbTagEnd?>