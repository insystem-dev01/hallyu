<?=$jqueryUiDatePick?>

<div id="memberpoint_pop" class="new_win">
	<h1><?php echo $title; ?></h1>

	<section class="new_win_desc marb50">
        <?php echo $mb_pg_anchor; ?>
        <h3 class="anc_tit">기본검색</h3>
        <form name="fsearch" id="fsearch" method="get">
            <input type="hidden" name="mb_id" value="<?php echo $mb_id; ?>">
            <div class="tbl_frm01">
                <table>
                    <colgroup>
                        <col class="w100">
                        <col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <th scope="row">기간검색</th>
                            <td>
                                <?=$get_search_date?>
			                </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="btn_confirm">
                <input type="submit" value="검색" class="btn_medium">
                <input type="button" value="초기화" id="frmRest" class="btn_medium grey">
            </div>
        </form>
        <div class="local_ov mart30">
            전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 건 조회
            <strong class="ov_a">포인트 합계 : <?php echo number_format($sum_point); ?>원</strong>
        </div>
        <div class="tbl_head01">
            <table>
                <colgroup>
                    <col class="w50">
                    <col>
                    <col class="w130">
                    <col class="w90">
                    <col class="w90">
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">번호</th>
                        <th scope="col">포인트내용</th>
                        <th scope="col">일시</th>
                        <th scope="col">포인트</th>
                        <th scope="col">포인트합</th>
                    </tr>
                </thead>
                <?=$pointList?>
                </tbody>
            </table>
        </div>
        <?=$pagination?>
    </section>
</div>
<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>