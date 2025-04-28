<div class="new_win">
    <h1 id="win_title"><?php echo $title; ?></h1>
    <div class="win_desc marb10">
		<p class="bx-danger">
			* 이 상품 구매시, 사용하실 수 있는 할인쿠폰입니다. 다운로드 받은 후 주문시 사용하세요!<br>
			* 발행된 쿠폰은 <b>[마이페이지 > 쿠폰관리]</b> 에서 확인 할 수 있습니다.
		</p>
	</div>
    <div class="tbl_head01 tbl_wrap bt_nolne">
		<table>
		<colgroup>
			<col class="w80">
			<col>
			<col class="w70">
		</colgroup>
		<tbody>
		<tr>
			<td class="tac"><?php echo $itImage; ?></td>
			<td class="td_name"><?php echo $gsName; ?></td>
			<td class="tac"><?php echo $gsPrice;?></td>
		</tr>
		</tbody>
		</table>
	</div>	
    <div class="win_desc mart20">
		<p class="pg_cnt">
			<em>총 <?php echo number_format($total_count); ?>개</em> 조회
		</p>
	</div>
    <div class="tbl_head01 tbl_wrap">
		<table>
            <colgroup>
                <col>
                <col class="w90">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">할인쿠폰명</th>
                    <th scope="col">쿠폰받기</th>
                </tr>
            </thead>
            <tbody>
                <?=$popTable?>
            </tbody>
		</table>
	</div>
    <?=$pagination?>
    <div class="win_btn">
		<a href="javascript:window.close();" class="btn_lsmall bx-white">창닫기</a>
    </div>
</div>

<script>
function post_update(action_url, val) {
	var f = document.fpost;
	f.cp_id.value = val;
	f.action = action_url;
	f.submit();
}
</script>

<form name="fpost" method="post">
    <input type="hidden" name="gs_id" value="<?php echo $gs_id; ?>">
    <input type="hidden" name="page"  value="<?php echo $page; ?>">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <input type="hidden" name="cp_id" value="">
</form>
