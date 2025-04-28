<?=$snbTagStart?>
<?=$admin_head_sub?>
<form name="fregform" method="post" action="<?=base_url()?>MypageProcess/seller_baesong_update" onsubmit="return fregform_submit(this);">
    <input type="hidden" name="token" value="">

    <h2>배송정책 설정</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <?php if(false) { ?>
                <tr>
                    <th scope="row">배송업체</th>
                    <td>
                        <div id="sit_supply_frm" class="tbl_frm02">
                            <table>
                                <colgroup>
                                    <col class="w180">
                                    <col>
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col" class="tac">배송업체명</th>
                                        <th scope="col" class="tac">배송추적 링크주소</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?=$deliverLinkstr?>
                                </tbody>
                            </table>
                            <?php echo $help['deliver']; ?>
                        </div>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row">기본 배송정책</th>
                    <td>
                        <div class="tbl_frm02">
                            <table>
                                <colgroup>
                                    <col class="w180">
                                    <col>
                                </colgroup>
                                <tbody>
                                    <tr>
                                        <th scope="row">
                                            <input type="radio" name="delivery_method" value="1" id="delivery_method1"<?php echo $deliverChk[1]; ?>>
                                            <label for="delivery_method1">무료배송</label>
                                        </th>
                                        <td>배송비가 부과되지 않습니다</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <input type="radio" name="delivery_method" value="2" id="delivery_method2"<?php echo $deliverChk[2]; ?>>
                                            <label for="delivery_method2">착불배송</label>
                                        </th>
                                        <td>주문시 또는 장바구니에 배송비가 <b>[착불]</b> 이라는 글이 출력되며 배송비는 부과되지 않습니다</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <input type="radio" name="delivery_method" value="3" id="delivery_method3"<?php echo $deliverChk[3]; ?>>
                                            <label for="delivery_method3">유료배송</label>
                                        </th>
                                        <td><input type="text" name="delivery_price" value="<?php echo number_format($seller['delivery_price']); ?>" class="frm_input" size="10" onkeyup="addComma(this);"> 원을 주문금액 또는 수량에 상관없이 동일 주문건에 배송비를 한번만 부과됩니다
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <input type="radio" name="delivery_method" value="4" id="delivery_method4"<?php echo $deliverChk[4]; ?>>
                                            <label for="delivery_method4">조건부무료배송</label>
                                        </th>
                                        <td>
                                            <input type="text" name="delivery_price2" value="<?php echo number_format($seller['delivery_price2']); ?>" class="frm_input" size="10" onkeyup="addComma(this);"> 원의 배송비를 부과하며 단! 주문금액이
                                            <input type="text" name="delivery_minimum" value="<?php echo number_format($seller["delivery_minimum"]); ?>" class="frm_input" size="10" onkeyup="addComma(this);"> 원 이상이면 무료배송 처리됩니다
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <div/>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <h2>쇼핑몰 배송/교환/반품안내</h2>
    <div>
        <?php echo $baseEdit;?>
    </div>
    <h2>모바일 배송/교환/반품안내</h2>
    <div>
        <?php echo $baseMbEdit;?>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>
</form>
<script>
function fregform_submit(f) {
	<?php echo $bassonCont1Js;?>
	<?php echo $bassonCont2Js;?>
    return true;
}
</script>
<?=$admin_footer_sub?>
<?=$snbTagEnd?>