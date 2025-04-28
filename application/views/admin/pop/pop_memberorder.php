<?=$jqueryUiDatePick?>
<div id="memberorder_pop" class="new_win">
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
                            <th scope="row">검색어</th>
                            <td>
                                <select name="sfl">
                                    <?=$sflOpt[0]?>
                                    <?=$sflOpt[1]?>
                                    <?=$sflOpt[2]?>
                                    <?=$sflOpt[3]?>
                                    <?=$sflOpt[4]?>
                                    <?=$sflOpt[5]?>
                                    <?=$sflOpt[6]?>
                                    <?=$sflOpt[7]?>
                                    <?=$sflOpt[8]?>
                                    <?=$sflOpt[9]?>
                                    <?=$sflOpt[10]?>
                                </select>
                                <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">기간검색</th>
                            <td>
                                <select name="sel_field">
                                    <?=$sel_fieldOpt[0]?>
                                    <?=$sel_fieldOpt[1]?>
                                    <?=$sel_fieldOpt[2]?>
                                    <?=$sel_fieldOpt[3]?>
                                    <?=$sel_fieldOpt[4]?>
                                    <?=$sel_fieldOpt[5]?>
                                    <?=$sel_fieldOpt[6]?>
                                    <?=$sel_fieldOpt[7]?>
                                    <?=$sel_fieldOpt[8]?>
                                </select>
                                <?php echo $get_search_date; ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">결제방법</th>
                            <td>
                                <?=$payMethod[0]?>
                                <?=$payMethod[1]?>
                                <?=$payMethod[2]?>
                                <?=$payMethod[3]?>
                                <?=$payMethod[4]?>
                                <?=$payMethod[5]?>
                                <?=$payMethod[6]?>
                                <?=$payMethod[7]?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">주문상태</th>
                            <td>
                                <?=$od_statusArr[0]?>
                                <?=$od_statusArr[1]?>
                                <?=$od_statusArr[2]?>
                                <?=$od_statusArr[3]?>
                                <?=$od_statusArr[4]?>
                                <?=$od_statusArr[5]?>
                                <?=$od_statusArr[6]?>
                                <?=$od_statusArr[7]?>
                                <?=$od_statusArr[8]?>
                                <?=$od_statusArr[9]?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">구매확정</th>
                            <td>
                                <?=$od_finalArr[0]?>
                                <?=$od_finalArr[1]?>
                                <?=$od_finalArr[2]?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">기타선택</th>
                            <td>
                                <?=$ectSelectChk[0]?>
                                <?=$ectSelectChk[1]?>
                                <?=$ectSelectChk[2]?>
                                <?=$ectSelectChk[3]?>
                                <?=$ectSelectChk[4]?>
                                <?=$ectSelectChk[5]?>
                                <?=$ectSelectChk[6]?>
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
            <strong class="ov_a">총주문액 : <?php echo number_format($tot_orderprice); ?>원</strong>
        </div>
        <div class="tbl_head01">
            <table id="sodr_list">
                <colgroup>
                    <col class="w50">
                    <col class="w150">
                    <col class="w40">
                    <col>
                    <col class="w90">
                    <col class="w90">
                    <col class="w90">
                    <col class="w90">
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">번호</th>
                        <th scope="col">주문번호</th>
                        <th scope="col" colspan="2">주문상품</th>
                        <th scope="col">주문상태</th>
                        <th scope="col">총주문액</th>
                        <th scope="col">결제방법</th>
                        <th scope="col">주문자명</th>
                    </tr>
                </thead>
                <tbody>
                    <?=$popList?>
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