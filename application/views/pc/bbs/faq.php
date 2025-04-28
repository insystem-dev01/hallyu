<?=$aside_cs?>
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>고객센터<i>&gt;</i><?php echo $title; ?></p>
	</h2>
    <nav id="tab_cate">
		<h2>자주묻는질문 분류</h2>
		<ul id="tab_cate_ul">
            <?=$faqCate?>
        </ul>
	</nav>
    <ul class="faq_li">
        <?=$faqTb?>
    </ul>
    <?=$faqTbEmpty?>
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
</div>