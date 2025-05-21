<h2 class="pop_title">
	<?php echo $tb['title']; ?>
	<a href="javascript:history.back();" class="btn_small bx-white">창닫기</a>
</h2>
<div class="m_agree">
	<?php echo nl2br($config['shop_private']); ?>
</div>
<div class="btn_confirm">
	<button type="button" onclick="safeClose();" class="btn_medium bx-white">창닫기</button>
</div>

<script>
function safeClose() {
    if (window.opener) {
        // 새창이나 팝업으로 열렸을 경우
        window.close();
    } else {
        // 일반 페이지로 열렸을 경우
        history.back();
    }
}
</script>


