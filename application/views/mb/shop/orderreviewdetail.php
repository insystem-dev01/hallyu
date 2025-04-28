<section id="review_detail">
    <div class="top_area">
        <div class="title">
            <span class="text">포토&동영상</span>
            <span class="current"><?=$current?></span>
            <span class="slash">/</span>
            <span class="total"><?=$item_use_count?></span>
        </div>
        <a href="javascript:window.close();" class="close_btn"></a>
    </div>
    <?=$result?>
</section>

<script>
    function open_review_modal(index_no, gs_id) {
        // 페이지 열기
        var base_url = document.getElementById('base_url').value;
        location.href = `${base_url}m/shop/reviewDetail?index_no=${index_no}&gs_id=${gs_id}`;
	}
</script>