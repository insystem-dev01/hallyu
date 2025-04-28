<!-- 팝업레이어 시작 { -->
<div id="hd_pop">
    <h2>팝업레이어 알림</h2>
    <?=$popTag?>
</div>
<script>
$(function() {
    $(".hd_pops_reject").click(function() {
        var id = $(this).attr('class').split(' ');
        var ck_name = id[1];
        var exp_time = parseInt(id[2]);
		var cookie_domain = '';
        $("#"+id[1]).css("display", "none");
        set_cookie(ck_name, 1, exp_time, cookie_domain);
    });
    $('.hd_pops_close').click(function() {
        var idb = $(this).attr('class').split(' ');
        $('#'+idb[1]).css('display','none');
    });
});
</script>
<!-- } 팝업레이어 끝 -->
