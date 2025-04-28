<div class="local_ov mart30">
	총 접속자수 : <b class="fc_red"><?php echo number_format($sum_count); ?></b>건 접속
</div>

<div class="tbl_head01">
	<table>
        <colgroup>
            <col class="w100">	
            <col>
            <col class="w80">
            <col class="w80">
        </colgroup>
        <thead>
            <tr>
                <th scope="col">년-월-일</th>
                <th scope="col">그래프</th>
                <th scope="col">접속자수</th>
                <th scope="col">비율(%)</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="2"><strong>합계</strong></td>
                <td><strong><?php echo number_format($sum_count); ?></strong></td>
                <td><strong>100</strong></td>
            </tr>
        </tfoot>
	    <tbody class="list">
            <?=$visit_dateTable?>
        </tbody>
	</table>
</div>
<?=$headTitleEnd?>