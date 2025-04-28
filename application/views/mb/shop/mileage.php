<div id="point">
	<p id="sod_fin_no">
		총 <b class="fc_red"><?php echo number_format($total_count); ?></b>건의 예치금 내역이 있습니다.
	</p>
    <ul id="point_ul">
        <?=$pointTb?>
    </ul>
    <div id="point_sum">
        <div class="sum_row">
            <span class="sum_tit">지급</span>
            <b class="sum_val"><?php echo $sum_point1; ?></b>
        </div>
        <div class="sum_row">
            <span class="sum_tit">사용</span>
            <b class="sum_val"><?php echo $sum_point2; ?></b>
        </div>
        <div class="sum_row">
            <span class="sum_tit">보유</span>
            <b class="sum_val"><?php echo number_format($sum_point3); ?></b>
        </div>
    </div>
    <?=$pagination?>
</div>