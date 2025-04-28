<?=$aside_my?>
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>마이페이지<i>&gt;</i><?php echo $title; ?></p>
	</h2>
    <h2 class="anc_tit">상세보기 버튼을 클릭하시면 주문상세내역을 조회하실 수 있습니다.</h2>
	<div class="tbl_head02 tbl_wrap">
		<table>
		<colgroup>
			<col class="w90">
			<col>
			<col class="w100">
			<col class="w140">
		</colgroup>
		<thead>
            <tr>
                <th scope="col">주문일자</th>
                <th scope="col">상품정보</th>
                <th scope="col">결제금액</th>
                <th scope="col">상태</th>
            </tr>
		</thead>
		<tbody>
            <?=$tableTag?>
        </tbody>
        </table>
	</div>
    <?=$pagination?>
</div>