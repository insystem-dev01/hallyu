<h2 class="pop_title">
	<?php echo $title ?>
	<a href="javascript:window.close();" class="btn_small bx-white">창닫기</a>
</h2>
<div id="sit_coupon">
	<table class="tbl_cp">
        <colgroup>
            <col width="60px">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <td class="scope" colspan='2'>
                    이 상품 구매시, 사용하실 수 있는 할인쿠폰입니다.<br>
                    다운로드 받은 후 주문시 사용하세요!<br>
                    발행된 쿠폰은 마이페이지에서 확인 할 수 있습니다.
                </td>
            </tr>
            <tr>
                <td class="image"><?php echo $imageTag; ?></td>
                <td class="gname">
                    <?php echo $gsName; ?>
                    <p class="bold mart5"><?php echo $gsPrice; ?></p>
                </td>
            </tr>
        </tbody>
	</table>
    <?=$popTable?>
    <?=$pagination?>
    <div class="btn_confirm">
		<button type="button" onclick="window.close();" class="btn_medium bx-white">창닫기</button>
	</div>
</div>
<script>
function post_update(action_url, val) {
	var f = document.fpost;
	f.cp_id.value = val;
	f.action = action_url;
	f.submit();
}
</script>

<form name="fpost" method="post">
    <input type="hidden" name="gs_id" value="<?php echo $gs_id; ?>">
    <input type="hidden" name="page"  value="<?php echo $page; ?>">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <input type="hidden" name="cp_id">
</form>
