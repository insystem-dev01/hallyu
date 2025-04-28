<div class="s_cont">
	<select name="faq_type" class="faq_sch" onchange="location=this.value;">
		<option value="<?php echo base_url(); ?>m/bbs/faq">전체보기</option>
        <?=$selectOpt?>
    </select>
    <div class="faq_li">
		<ul>
            <?=$faqList?>
        </ul>
	</div>
    <?=$emptyTag?>
</div>
<script>
function js_faq(id){
	var $con = $("#sod_faq_con_"+id);
	if($con.is(":visible")) {
		$con.slideUp("fast");
		$(".faq_q").removeClass("active");
	} else {
		$(".faq_a:visible").slideUp("fast");
		$con.slideDown("fast");
		$(".faq_q").removeClass("active");
		$con.prev().addClass("active");
	}
}
</script>
