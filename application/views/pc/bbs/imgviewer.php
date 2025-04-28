<div id="sit_pvi_nw">
    <table width="<?php echo $size[0]; ?>" height="<?php echo $size[1]; ?>">
    <tr>
        <td width="<?php echo $size[0]; ?>" height="<?php echo $size[1]; ?>"><a href="javascript:window.close();"><img src="<?php echo $img; ?>"></a></td>
    </tr>
    </table>
</div>

<script>
$(function(){
    // 창 사이즈 조절
    $(window).on("load", function() {
        var w = <?php echo $size[0]; ?> + 50;
        var h = $("#sit_pvi_nw").outerHeight(true) + $("#sit_pvi_nw h1").outerHeight(true);
        window.resizeTo(w, h);
    });
});
</script>