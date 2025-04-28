<?=$jqueryUiDatePick?>
<?=$headTitle?>
<h2>기본검색</h2>
<form name="fsearch" id="fsearch" method="get">
    <input type="hidden" name="code" value="<?php echo $code; ?>">
    <input type="hidden" name="gs_id" value="<?php echo $gs_id; ?>">
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
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
            </tbody>
        </table>
        <table>
            <colgroup>
                <col class="w100">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">기간검색</th>
                    <td>
                        <?=$sflArr2?>
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
    <input type="hidden" name="gs_id" value="<?=$gs_id?>">

    <div class="tbl_head01 mart80">
        <table>
            <colgroup>
                <col class="w150">
                <col class="w200">
                <col>
                <col class="w150">
                <col class="w150">
                <col class="w150">
                <col class="w150">
                <col class="w150">
                <col class="w150">
            </colgroup>
            <thead>
                <tr>
                    <th>이미지</th>
                    <th>상품명</th>
                    <th>판매기간</th>
                    <th>정가</th>
                    <th>판매가(온라인최저가)</th>
                    <th>실제 판매가</th>
                    <th>수량</th>
                    <th>구매</th>
                    <th>재고</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $imgTag ?></td>
                    <td><?= $gs['gname'] ?></td>
                    <td><?php echo $gs['sb_date']." ~ ".$gs['eb_date'];?></td>
                    <td><?= number_format($gs['normal_price']) ?></td>
                    <td><?= number_format($gs['goods_price']) ?></td>
                    <td><?= number_format($gs['calculate_price']) ?></td>
                    <td><?= $gs['original_stock_qty'] ?></td>
                    <td><?= $order_cnt ?></td>
                    <td><?= $stock_qty?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <a href="<?=base_url()?>AdminProcess/order_timesale_excel?gs_id=<?=$gs_id?>" class="btn_lsmall white mart30"><i class="fa fa-file-excel-o"></i>엑셀저장</a>
    <div class="tbl_head01 mart10">
        <table>
            <colgroup>
                <col class="w150">
                <col>
                <col class="w150">
                <col class="w150">
                <col class="w150">
                <col class="w150">
                <col class="w150">
            </colgroup>
            <thead>
                <tr>
                    <th>구매순서</th>
                    <th>주문일시</th>
                    <th>주문번호</th>
                    <th>구매자</th>
                    <th>결제금액</th>
                    <th>실제 구매금액</th>
                    <th>예치금(차액)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($result)) { ?>
                    <?php foreach($result as $key => $row) { ?>
                    <tr>
                        <td><?=$key+1?></td>
                        <td><?=$row['purchase_date']?></td>
                        <td>
                            <a href="<?=base_url()?>admin/pop_orderform?od_id=<?=$row['od_id']?>" onclick="win_open(this,'pop_orderform','1200','800','yes');return false;" class="fc_197"><?=$row['od_id']?></a>
                        </td>
                        <td>
                            <p>
                                <span class="sv_wrap">
                                        <a href="javascript:void(0);" class="sv_member"><?=$row['mb_name']?></a>
                                        <span class="sv">
                                            <a href="<?=base_url()?>admin/pop_memberform?mb_id=test1234" onclick="win_open(this,'win_member','1200','600','yes');return false;">회원정보수정</a>
                                            <a href="<?=base_url()?>admin/formmail?mb_id=test1234&amp;name=%ED%85%8C%EC%8A%A4%ED%8A%B8&amp;email=2cmnpaSsyNetZsmfnQ==" onclick="win_open(this,'win_email','650','580','no'); return false;">메일보내기</a>
                                            <a href="<?=base_url()?>admin/sms/sms_user?ph=01012345678" onclick="win_open(this,'win_sms','300','360','no'); return false;">SMS보내기</a>
                                            <a href="<?=base_url()?>AdminProcess/admin_ss_login?mb_id=test1234" target="_blank">쇼핑몰로그인</a>
                                        </span>

                                    <noscript class="sv_nojs">
                                        <span class="sv">
                                            <a href="<?=base_url()?>admin/pop_memberform?mb_id=test1234" onclick="win_open(this,'win_member','1200','600','yes');return false;">회원정보수정</a>
                                            <a href="<?=base_url()?>admin/formmail?mb_id=test1234&name=%ED%85%8C%EC%8A%A4%ED%8A%B8&email=2cmnpaSsyNetZsmfnQ==" onclick="win_open(this,'win_email','650','580','no'); return false;">메일보내기</a>
                                            <a href="<?=base_url()?>admin/sms/sms_user?ph=01012345678" onclick="win_open(this,'win_sms','300','360','no'); return false;">SMS보내기</a>
                                            <a href="<?=base_url()?>AdminProcess/admin_ss_login?mb_id=test1234" target="_blank">쇼핑몰로그인</a>
                                        </span>
                                    </noscript>
                                </span>
                            </p>
                            <p>
                                <span class="list_mb_id"><?php echo (!empty($row['mb_id'])) ? "(".$row['mb_id'].")" : "" ?></span>
                            </p>
                        </td>
                        <td><?=number_format($row['normal_price'])?></td>
                        <td><?=number_format($row['real_price'])?></td>
                        <td><?=number_format($row['difference'])?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
                <tr>
                    <td colspan="4">합계</td>
                    <td><?=number_format($sum_data['sum_normal_price'])?></td>
                    <td><?=number_format($sum_data['sum_real_price'])?></td>
                    <td><?=number_format($sum_data['sum_difference'])?></td>
                </tr>
            </tbody>
        </table>
    </div>
</form>
<?=$headTitleEnd?>
<script>
    $(function(){
        // 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
        $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99"});
    });
</script>