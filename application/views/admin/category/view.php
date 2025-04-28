<?=$headTitle?>
<script src="<?php echo base_url(); ?>js/categoryform.js?ver=<?php echo time(); ?>"></script>
<h2>카테고리 순위 설정</h2>
<div class="tbl_frm01">
	<table>
        <thead>
            <tr>
                <th scope="col" class="tac">1차 분류</th>
                <th scope="col" class="tac">2차 분류</th>
                <th scope="col" class="tac">3차 분류</th>
                <th scope="col" class="tac">4차 분류</th>
                <th scope="col" class="tac">5차 분류</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="w20p">
                    <form name="frm_sel_ca1" method="post" target="hiddenframe">
                        <input type="hidden" name="W" value="sel_ca1">
                        <select multiple name="sel_ca1[]" id="sel_ca1" class="multiple-select2">
                            <?=$selOpt?>
                        </select>
                        <div class="btn_confirm03">
                            <button type="button" class="btn_small bx-white" onclick="category_move('sel_ca1', 'prev')">▲ 위로</button>
                            <button type="button" class="btn_small bx-white" onclick="category_move('sel_ca1', 'next')">▼ 아래로</button>
                            <button type="button" class="btn_small sel_submit">저장</button>
                        </div>
                    </form>
                </td>
                <?=$selboxInfo?>
            </tr>
	    </tbody>
	</table>
</div>
<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="hd">1, 카테고리 순위변경 안내</div>
		<div class="desc01">
			<p>ㆍ1차 카테고리명을 먼저 클릭하시면 2차~5차까지 순차적으로 항목이 노출 됩니다.</p>
		</div>
		<div class="hd">2, 버튼 사용방법 안내</div>
		<div class="desc01">
			<p>ㆍ<em>[위　로]</em> 버튼을 클릭시 상위로 한칸씩 이동되며 우선순위로 노출 됩니다.</p>
			<p>ㆍ<em>[아래로]</em> 버튼을 클릭시 하위로 한칸씩 이동됩니다.</p>
			<p>ㆍ<em>[저　장]</em> 버튼을 클릭시 해당 카테고리 항목만 처리되며 그외 카테고리는 처리되지 않습니다.</p>
		</div>
	 </div>
</div>

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

<iframe width="0" height="0" name="hiddenframe"></iframe>
<?=$headTitleEnd?>