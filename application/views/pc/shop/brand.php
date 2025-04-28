<h2 class="pg_title">
	<div class="inner">
		<dl class="txt_bx">
			<dt><?php echo $title; ?></dt>
			<dd>각 <?php echo $title; ?>의 상품들을 모아모아서 쇼핑하기!</dd>
		</dl>
	</div>
</h2>
<!-- 브랜드 검색 { -->
<div class="br_search">
	<form name="frmbrandsearch">
        <input type="hidden" name="qsort" id="qsort" value="<?=$qsort?>">
        <input type="hidden" name="qorder" id="qorder" value="<?=$qorder?>">
        <input type="hidden" name="qword" id="qword" value="<?=$qword?>">
        <dl class="sch_inner">
            <dt><label for="ssch_q">SEARCH</label></dt>
            <dd><input type="text" name="qstx" value="<?php echo $qstx; ?>" id="ssch_q" placeholder="<?=$title?>명을 입력해주세요." maxlength="30"><button type="submit" class="btn_submit fa fa-search"></button></dd>
        </dl>
        <div id="br_sch">
            <ul class="sch_tab">
                <li<?php if($qsort == 'br_name') echo ' class="active"'; ?>><a href="javascript:set_sort('br_name', 'asc');">가나다순</a></li>
                <li<?php if($qsort == 'br_name_eng') echo ' class="active"'; ?>><a href="javascript:set_sort('br_name_eng', 'asc');">알파벳순</a></li>
            </ul>
            <div class="sch_tab_con">
                <ul>
                    <li class="w35 <?=$all?>" onclick="set_word('');">전체</li>
                    <?=$charAtStr?>
                    <li class="w35 <?=$ect?>" onclick="set_word('etc');">기타</li>
	    		</ul>
		</div>
	</div>
	</form>
</div>
<script>
function set_sort(qsort, qorder)
{
    var f = document.frmbrandsearch;
    f.qsort.value = qsort;
    f.qorder.value = qorder;
	f.qword.value = '';
    f.submit();
}

function set_word(qword)
{
    var f = document.frmbrandsearch;
    f.qword.value = qword;
    f.submit();
}
</script>
<!-- } 브랜드 검색 -->
<?=$brand_str?>