<?=$headTitle?>
<h2>기본검색</h2>
<form name="fsearch" id="fsearch" method="get">
    <input type="hidden" name="code" value="<?php echo $code; ?>">
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
                            <?=$sflArr[0]?>
                            <?=$sflArr[1]?>
                            <?=$sflArr[2]?>
                            <?=$sflArr[3]?>
                            <?=$sflArr[4]?>
                            <?=$sflArr[5]?>
                            <?=$sflArr[6]?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">카테고리</th>
                    <td colspan="3">
                        <?php echo $sel_caArr[0]; ?>
                        <?php echo $sel_caArr[1]; ?>
                        <?php echo $sel_caArr[2]; ?>
                        <?php echo $sel_caArr[3]; ?>
                        <?php echo $sel_caArr[4]; ?>
                        <?php echo $sel_caArr[5]; ?>
                        <script>
                        $(function() {
                            var base_url=document.getElementById('base_url').value;
                            $("#sel_ca1").multi_select_box("#sel_ca",5,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                            $("#sel_ca2").multi_select_box("#sel_ca",5,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                            $("#sel_ca3").multi_select_box("#sel_ca",5,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                            $("#sel_ca4").multi_select_box("#sel_ca",5,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                            $("#sel_ca5").multi_select_box("#sel_ca",5,"","=카테고리선택=");
                        });
                        </script>
                    </td>
                </tr>
                <tr>
                    <th scope="row">진열영역</th>
                    <td>
                        <?php echo $typeArr[1]; ?>
                        <?php echo $typeArr[2]; ?>
                        <?php echo $typeArr[3]; ?>
                        <?php echo $typeArr[4]; ?>
                        <?php echo $typeArr[5]; ?>
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
<form name="fgoodslist" method="post" action="<?=base_url()?>AdminProcess/goods_type_update">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <div class="local_ov mart30">
        전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 건 조회
        <span class="ov_a">
            <select id="page_rows" onchange="location='<?php echo "{$requestUrl}?{$q1}&page=1"; ?>&page_rows='+this.value;">
                <?php echo $page_rowsOpt['30']; ?>
                <?php echo $page_rowsOpt['50']; ?>
                <?php echo $page_rowsOpt['100']; ?>
                <?php echo $page_rowsOpt['150']; ?>
            </select>
        </span>
    </div>
    <div class="local_frm01">
        <?php echo $btn_frmline; ?>
    </div>
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col class="w60">
                <col class="w60">
                <col class="w80">
                <col>
                <col class="w100">
                <col class="w80">
                <col class="w80">
                <col class="w80">
                <col class="w80">
                <col class="w80">
                <col class="w80">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col" rowspan="2">번호</th>
                    <th scope="col" rowspan="2">이미지</th>
                    <th scope="col" rowspan="2">상품코드</th>
                    <th scope="col" rowspan="2">상품명</th>
                    <th scope="col" class="th_bg">베스트셀러</th>
                    <th scope="col" class="th_bg">신상품</th>
                    <th scope="col" class="th_bg">인기상품</th>
                    <th scope="col" class="th_bg">추천상품</th>
                    <th scope="col" rowspan="2">진열</th>
                    <th scope="col" rowspan="2">재고</th>
                    <th scope="col" rowspan="2">판매가</th>
                </tr>
                <tr class="rows">
                    <th scope="col" class="th_bg"><input type="checkbox" name="chkall2" value="1" onclick="check_all2(this.form);"></th>
                    <th scope="col" class="th_bg"><input type="checkbox" name="chkall3" value="1" onclick="check_all3(this.form);"></th>
                    <th scope="col" class="th_bg"><input type="checkbox" name="chkall4" value="1" onclick="check_all4(this.form);"></th>
                    <th scope="col" class="th_bg"><input type="checkbox" name="chkall5" value="1" onclick="check_all5(this.form);"></th>
                </tr>
            </thead>
            <?=$typeTable?>
            </tbody>
        </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<script>
function check_all1(f) {
    var chk = $("input[name^=it_type1]");

    for(i=0; i<chk.length; i++)
        chk[i].checked = f.chkall1.checked;
}
function check_all2(f) {
    var chk = $("input[name^=it_type2]");

    for(i=0; i<chk.length; i++)
        chk[i].checked = f.chkall2.checked;
}
function check_all3(f) {
    var chk = $("input[name^=it_type3]");

    for(i=0; i<chk.length; i++)
        chk[i].checked = f.chkall3.checked;
}
function check_all4(f) {
    var chk = $("input[name^=it_type4]");

    for(i=0; i<chk.length; i++)
        chk[i].checked = f.chkall4.checked;
}
function check_all5(f) {
    var chk = $("input[name^=it_type5]");

    for(i=0; i<chk.length; i++)
        chk[i].checked = f.chkall5.checked;
}
</script>

<?=$headTitleEnd?>