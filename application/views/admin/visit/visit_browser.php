<div class="local_ov mart30">
	총 접속자수 : <b class="fc_red"><?php echo number_format($sum_count); ?></b>건 접속
</div>

<div class="tbl_head01">
	<table>
        <colgroup>
            <col class="w60">
            <col class="w200">
            <col>
            <col class="w80">
            <col class="w80">	
        </colgroup>
        <thead>
        <tr>
            <th scope="col">순위</th>
            <th scope="col">브라우저</th>
            <th scope="col">그래프</th>
            <th scope="col">접속자수</th>
            <th scope="col">비율(%)</th>
        </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="3"><strong>합계</strong></td>
                <td><strong><?php echo number_format($sum_count); ?></strong></td>
                <td><strong>100</strong></td>
            </tr>
        </tfoot>
        <tbody class="list">
            <?=$visit_browserTable?>
        </tbody>
	</table>
</div>
<?=$headTitleEnd?>