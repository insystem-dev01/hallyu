<?=$aside_my?>
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>마이페이지<i>&gt;</i><?php echo $title; ?></p>
	</h2>
    <p class="pg_cnt">
		<em>총 <?php echo number_format($total_count); ?>건</em>의 포인트내역이 있습니다.
	</p>
    <div class="tbl_head02 tbl_wrap">
		<table>
            <colgroup>
                <col width="140">
                <col>
                <col width="100">
                <col width="100">
            </colgroup>
            <thead>
            <tr>
                <th scope="col">일시</th>
                <th scope="col">내용</th>
                <th scope="col">지급포인트</th>
                <th scope="col">사용포인트</th>
            </tr>
            </thead>
            <tbody>
                <?=$tableTag?>
            </tbody>
		    <tfoot>
                <tr>
                    <th scope="row" colspan="2">소계</th>
                    <td class="td_num fc_red"><?php echo $sum_point1; ?></td>
                    <td class="td_num fc_red"><?php echo $sum_point2; ?></td>
                </tr>
                <tr>
                    <th scope="row" colspan="2">보유포인트</th>
                    <td class="td_num fc_red" colspan="2"><?php echo number_format($sum_point3); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?=$pagination?>
</div>