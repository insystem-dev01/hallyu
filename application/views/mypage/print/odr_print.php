<div id="sodr_print_pop" class="new_win">
    <h1>주문서출력</h1>
    <?=$printStr?>
    <div id="sodr_print_pop_total">
        <span>
            총 수량 <strong><?php echo number_format($tot_tot_qty); ?></strong>개,
            총 주문액 <strong><?php echo number_format($tot_tot_price); ?></strong>원,
			총 결제액 <strong><?php echo number_format($tot_tot_price2); ?></strong>원,
			총 배송비 <strong><?php echo number_format($tot_tot_baesong); ?></strong>원
        </span>
        &lt;출력 끝&gt;
    </div>
</div>
<script>print();</script>

</body>
</html>