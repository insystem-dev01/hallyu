<form name="frm_<?php echo $sel_id; ?>" method="post" target="hiddenframe">
    <input type="hidden" name="W" value="<?php echo $sel_id; ?>">
    <select multiple name="<?php echo $sel_id; ?>[]" id="<?php echo $sel_id; ?>" class="multiple-select2">
        <?=$optList?>
    </select>
    <div class="btn_confirm03">
        <button type="button" class="btn_small bx-white" onclick="category_move('<?php echo $sel_id; ?>','prev')">▲ 위로</button>
        <button type="button" class="btn_small bx-white" onclick="category_move('<?php echo $sel_id; ?>','next')">▼ 아래로</button>
        <button type="button" class="btn_small sel_submit">저장</button>
    </div>
</form>

<script>
$(function(){
	var opt = "";
	opt += "<select multiple class=\"multiple-select2\"></select>\n";
	opt += "<div class=\"btn_confirm03\">\n";
	opt += "<button type=\"button\" class=\"btn_small bx-white\">▲ 위로</button>\n";
	opt += "<button type=\"button\" class=\"btn_small bx-white\">▼ 아래로</button>\n";
	opt += "<button type=\"button\" class=\"btn_small\">저장</button>\n";
	opt += "</div>";

	// 2~5차 분류는 초기값으로 세팅
	$(".sit_selbox_info").empty().html(opt);

	// 분류 클릭에 따른 적용값
	$("#sel_ca1, #sel_ca2, #sel_ca3, #sel_ca4").on("change", function() {
        var base_url=document.getElementById('base_url').value;
		var opt_id = $(this).attr("id");
		var opt_nm = opt_id.replace(/[^0-9]/g, "");
		var opt_st = opt_id.replace(/[^a-z_]/g, "");
		var sel_id = opt_st + (parseInt(opt_nm) + 1);
		var catecode = $("#"+opt_id+" option:selected").val();

		var obj = { load_sel_ca2:2, load_sel_ca3:3, load_sel_ca4:4, load_sel_ca5:5 };
		jQuery.each(obj, function(sel, idx) {
			if(idx > parseInt(opt_nm)) {
				$("#"+sel).empty().html(opt);
			}
		});

		$.post(
            base_url+"admin/category_view_select",
			{ catecode: catecode, sel_id: sel_id },
			function(data) {
				$("#load_"+sel_id).empty().html(data);
			}
		);
	});

	// 저장 (FRAME 방식)
	$("button.sel_submit").on("click", function() {
		var $sel_id = $(this).closest("form").attr("select","id");
		var bchk = false;

		$sel_id.find('option').each(function() {
			$(this).attr("selected", "selected");
			bchk = true;
		});

		if(!bchk) {
			alert("처리할 항목이 없습니다");
			return;
		} else {
			var base_url=document.getElementById('base_url').value;
			$(this).closest("form").attr('action', base_url+'AdminProcess/category_view_update').submit();
		}
	});
});
</script>