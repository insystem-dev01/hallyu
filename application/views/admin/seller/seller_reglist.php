<h1 class="newp_tit"><?php echo $title; ?></h1>
<div class="new_win_body">
	<form name="fsearch" id="fsearch" method="get">
	<div class="guidebox tac">
		<b>아이디 : </b> <input type="text" name="sfl" value="<?php echo $sfl; ?>" class="frm_input w100 marr10">
		<b>회원명 : </b> <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input w100">
		<input type="submit" value="검색" class="btn_small">
	</div>
	</form>

	<div class="local_frm01 mart20">
		전체 : <b class="fc_197"><?php echo number_format($total_count); ?></b> 명 조회
	</div>
	<div class="tbl_head01">
		<table>
            <colgroup>
                <col class="w50">
                <col class="w100">
                <col>
                <col class="w90">
                <col class="w60">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col"><?php echo $aId; ?>아이디</a></th>
                    <th scope="col"><?php echo $aName; ?>회원명</a></th>
                    <th scope="col"><?php echo $aRegTime; ?>가입일</a></th>
                    <th scope="col">선택</td>
                </tr>
            </thead>
            <?=$sellerTable?>
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