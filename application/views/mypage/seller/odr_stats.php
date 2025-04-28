<?=$snbTagStart?>
<?=$admin_head_sub?>
<h2>통계검색</h2>
<form name="fsearch" id="fsearch" method="get">
    <input type="hidden" name="code" value="<?php echo $code; ?>">
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w120">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">기간검색</th>
                    <td>
                        <select name="year">
                            <?=$yearOpt?>
                        </select>
                        <select name="month">
                            <?=$monthOpt?>
                        </select>
                        <input type="submit" value="검색" class="btn_small">
                    </td>
                </tr>
	        </tbody>
	    </table>
    </div>
</form>
<div class="local_ov mart20">
	<strong>총 판매상품수 : <?php echo number_format($sum_count); ?>건</strong>
	<span class="fc_red">(취소/환불/반품 내역은 제외)</span>
</div>
<div class="tbl_head01">
	<table>
        <colgroup>
            <col class="w100">
            <col>
            <col class="w60">
            <col class="w90">
            <col class="w90">
            <col class="w90">
            <col class="w90">
            <col class="w90">
        </colgroup>
        <thead>
            <tr>
                <th scope="col">날짜</th>
                <th scope="col">그래프</th>
                <th scope="col">비율%</th>
                <th scope="col">판매상품수</th>
                <th scope="col">판매가격</th>
                <th scope="col" class="th_bg">공급가격</th>
                <th scope="col" class="th_bg">배송비</th>
                <th scope="col" class="th_bg">소계</th>
            </tr>
        </thead>
        <tbody class="list">
            <?=$statsTable?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">합계</td>
                <td>100</td>
                <td><?php echo number_format($tot_count); ?></td>
                <td class="tar"><?php echo number_format($tot_price); ?></td>
                <td class="tar"><?php echo number_format($tot_supply); ?></td>
                <td class="tar"><?php echo number_format($tot_baesong); ?></td>
                <td class="tar"><?php echo number_format($tot_subprice); ?></td>
            </tr>
        </tfoot>
	</table>
</div>
<?=$admin_footer_sub?>
<?=$snbTagEnd?>