<h1 class="newp_tit"><?php echo $title; ?></h1>
<div class="new_win_body">
	<div class="sho_cate_bx">
		<div class="local_frm02">
			<a href="<?=base_url()?>admin/pt_category?mb_id=<?php echo $mb_id; ?>" class="btn_lsmall bx-blue">처음으로</a>
		</div>
        <ul>
            <?=$categoryTable?>
        </ul>
	</div>
	<div class="btn_confirm">
		<button type="button" onclick="self.close();" class="btn_medium bx-white">닫기</button>
	</div>
</div>

<script>
function check_sub(ca_no, mb_id) {
    var base_url=document.getElementById('base_url').value;
	var error = "";
	var token = get_ajax_token();
	if(!token) {
		alert("토큰 정보가 올바르지 않습니다.");
		return false;
	}
	$.ajax({
		url: base_url+"AdminProcess/pt_category_update",
		type: "POST",
		data: { "mb_id": mb_id, "ca_no": ca_no, "token": token },
		dataType: "json",
		async: false,
		cache: false,
		success: function(data, textStatus) {
			error = data.error;
		}
	});
	if(error) {
		alert(error);
	}
	location.reload();
}
</script>