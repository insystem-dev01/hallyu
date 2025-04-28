<form name="fboardlist" method="post" action="<?=base_url()?>process/del_all" onsubmit="return Check_Select(this);">
    <input type="hidden" name="boardid" value="<?php echo $boardid; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <?=$useCate?>
    <p class="marb7 tal">
        <?=$is_admin[0]?>
        총 <b class="fc_red"><?php echo $total_count; ?></b>개의 게시물이 있습니다.
    </p>
    <div class="webzine">
        <?=$webzineTb?>
    </div>
    <div class="page_wrap">
        <?=$is_admin[1]?>
        <?=$writeTag?>
    </div>
</form>
<form name="searchform" method="get">
    <input type="hidden" name="boardid" value="<?php echo $boardid; ?>">
    <div class="bottom_sch">
        <select name="sfl">
            <?=$gw_search_opt?>
        </select>
        <input type="text" name="stx" class="frm_input" value="<?php echo $stx; ?>">
        <input type="submit" value="검색" class="btn_lsmall grey">
    </div>
</form>
<?=$pagination?>
<script>
function Check_Select(form) {
	var check_nums = document.fboardlist.elements.length;
	for(var i=0; i<check_nums; i++) {
		var checkbox_obj = eval("document.fboardlist.elements[" + i + "]");
		if(checkbox_obj.checked == true) {
			break;
		}
	}

	if(i == check_nums) {
		alert ("삭제할 게시물을 하나 이상 선택하세요!");
			return false;
	} else {
		if(!confirm("한번 삭제한 자료는 복구할 수 없습니다.\n\n선택한 항목을 정말 삭제 하시겠습니까?"))
			return false;

		document.fboardlist.submit();
	}
}

function check_all(f){
    var chk = document.getElementsByName("OrderNum[]");

    for(i=0; i<chk.length; i++)
        chk[i].checked = f.chkall.checked;
}
</script>