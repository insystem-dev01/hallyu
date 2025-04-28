<?=$headTitle?>
<?=$jqueryUiDatePick?>
<h2>기본검색</h2>
<form name="fsearch" id="fsearch" method="get">
    <input type="hidden" name="code" value="<?php echo $code; ?>">
    <div class="tbl_frm01">
        <table class="tablef">
            <colgroup>
                <col class="w100">
                <col>
                <col class="w100">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">검색어</th>
                    <td colspan="3">
                        <select name="sfl">
                            <?php echo $sflOpt[0]; ?>
                            <?php echo $sflOpt[1]; ?>
                            <?php echo $sflOpt[2]; ?>
                            <?php echo $sflOpt[3]; ?>
                            <?php echo $sflOpt[4]; ?>
                            <?php echo $sflOpt[5]; ?>
                            <?php echo $sflOpt[6]; ?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">카테고리</th>
                    <td colspan="3">
                        <?php echo $scaSelect[0]; ?>
                        <?php echo $scaSelect[1]; ?>
                        <?php echo $scaSelect[2]; ?>
                        <?php echo $scaSelect[3]; ?>
                        <?php echo $scaSelect[4]; ?>
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
                    <th scope="row">기간검색</th>
                    <td colspan="3">
                        <select name="q_date_field" id="q_date_field">
                            <?php echo $q_date_fieldArr[0]; ?>
                            <?php echo $q_date_fieldArr[1]; ?>
                            <?php echo $q_date_fieldArr[2]; ?>
                        </select>
                        <?php echo $get_search_date; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">브랜드</th>
                    <td>
                        <select name="q_brand">
                            <?=$brandStr?>
                        </select>
                    </td>
                    <th scope="row">배송가능 지역</th>
                    <td>
                        <select name="q_zone">
                            <?php echo $q_zoneStr[0]; ?>
                            <?php echo $q_zoneStr[1]; ?>
                            <?php echo $q_zoneStr[2]; ?>
                            <?php echo $q_zoneStr[3]; ?>
                            <?php echo $q_zoneStr[4]; ?>
                            <?php echo $q_zoneStr[5]; ?>
                            <?php echo $q_zoneStr[6]; ?>
                            <?php echo $q_zoneStr[7]; ?>
                            <?php echo $q_zoneStr[8]; ?>
                            <?php echo $q_zoneStr[9]; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                <th scope="row">상품재고</th>
                    <td>
                        <select name="q_stock_field" id="q_stock_field">
                            <?php echo $stock_fieldArr['stock']; ?>
                            <?php echo $stock_fieldArr['noti']; ?>
                        </select>
                        <label for="fr_stock" class="sound_only">재고수량 시작</label>
                        <input type="text" name="fr_stock" value="<?php echo $fr_stock; ?>" id="fr_stock" class="frm_input" size="6"> 개 이상 ~
                        <label for="to_stock" class="sound_only">재고수량 끝</label>
                        <input type="text" name="to_stock" value="<?php echo $to_stock; ?>" id="to_stock" class="frm_input" size="6"> 개 이하
                    </td>
                    <th scope="row">상품가격</th>
                    <td>
                        <select name="q_price_field" id="q_price_field">
                            <?php echo $price_fieldArr['goods']; ?>
                            <?php echo $price_fieldArr['supply']; ?>
                            <?php echo $price_fieldArr['normal']; ?>
                            <?php echo $price_fieldArr['point']; ?>
                        </select>
                        <label for="fr_price" class="sound_only">상품가격 시작</label>
                        <input type="text" name="fr_price" value="<?php echo $fr_price; ?>" id="fr_price" class="frm_input" size="6"> 원 이상 ~
                        <label for="to_price" class="sound_only">상품가격 끝</label>
                        <input type="text" name="to_price" value="<?php echo $to_price; ?>" id="to_price" class="frm_input" size="6"> 원 이하
                    </td>
                </tr>
                <tr>
                    <th scope="row">판매여부</th>
                    <td>
                        <?php echo $q_isopenArr[0]; ?>
                        <?php echo $q_isopenArr[1]; ?>
                        <?php echo $q_isopenArr[2]; ?>
                        <?php echo $q_isopenArr[3]; ?>
                        <?php echo $q_isopenArr[4]; ?>
                    </td>
                    <th scope="row">필수옵션</th>
                    <td>
                        <?php echo $q_optionArr['all']; ?>
                        <?php echo $q_optionArr['use']; ?>
                        <?php echo $q_optionArr['unuse']; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">과세유형</th>
                    <td>
                        <?php echo $q_notaxArr['all']; ?>
                        <?php echo $q_notaxArr['1']; ?>
                        <?php echo $q_notaxArr['0']; ?>
                    </td>
                    <th scope="row">추가옵션</th>
                    <td>
                        <?php echo $q_supplyArr['all']; ?>
                        <?php echo $q_supplyArr['1']; ?>
                        <?php echo $q_supplyArr['0']; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">오픈상태</th>
                    <td>
                        <?php echo $q_openArr[0]; ?>
                        <?php echo $q_openArr[1]; ?>
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