<h1 class="newp_tit"><?php echo $title; ?></h1>
<div class="new_win_body">
	<form name="fsearch" id="fsearch" method="get">
	<div class="guidebox tac">
		<b>업체코드 : </b> <input type="text" name="sfl" value="<?php echo $sfl; ?>" class="frm_input marr10">
		<b>입점사명 : </b> <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input">
		<input type="submit" value="검색" class="btn_small">
	</div>
	</form>

	<div class="local_ov mart20">
		전체 : <b class="fc_197"><?php echo number_format($total_count); ?></b> 건 조회
	</div>
	<div class="tbl_head01">
		<table>
		<colgroup>
			<col class="w50">
			<col class="w80">
			<col>
			<col class="w80">
			<col class="w60">
		</colgroup>
		<thead>
		<tr>
			<th scope="col">번호</th>
			<th scope="col">업체코드</th>
			<th scope="col"><?php echo $company_name; ?>입점사명</a></th>
			<th scope="col"><?php echo $company_owner; ?>대표자명</a></th>
			<th scope="col">선택</th>
		</tr>
		</thead>
            <?=$listTable?>
            </tbody>
		</table>
	</div>
    <?=$pagination?>
</div>
<script>
function yes(mb_id){
	opener.document.fregform.mb_id.value = mb_id;
	self.close();
}
</script>