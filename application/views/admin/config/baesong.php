<?=$headTitle?>
<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/baesong_update" onsubmit="return fregform_submit(this);">
    <input type="hidden" name="token" value="">

    <h2>배송정책 설정</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <?php if($member['grade'] == "99") { ?>
                <tr>
                    <th scope="row">배송업체</th>
                    <td>
                        <div id="sit_supply_frm" class="tbl_frm02">
                            <table>
                            <colgroup>
                                <col class="w180">
                                <col>
                                <col class="w70">
                            </colgroup>
                            <thead>
                            <tr>
                                <th scope="col" class="tac">배송업체명</th>
                                <th scope="col" class="tac">배송추적 링크주소</th>
                                <th scope="col" class="tac">삭제</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?=$deliveryCompanyStr?>
                            </tbody>
                            </table>
                        </div>

                        <script>
                        $(function() {
                            // 입력필드추가
                            $("#add_supply_row").click(function() {
                                var $el = $("#sit_supply_frm tbody tr:last");
                                var fld = "<tr>\n";
                                fld += "<td><input type=\"text\" name=\"spl_name[]\" value=\"\" class=\"frm_input wfull\"></td>\n";
                                fld += "<td><input type=\"text\" name=\"spl_url[]\" value=\"\" class=\"frm_input wfull\"></td>\n";
                                fld += "<td class=\"tac\"><button type=\"button\" id=\"del_supply_row\" class=\"btn_small red\">삭제</button></td>\n";
                                fld += "</tr>";

                                $el.after(fld);
                            });

                            // 입력필드삭제
                            $("#del_supply_row").on("click", function() {
                                $(this).closest("tr").remove();
                            });
                        });
                        </script>
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
                                        <input type="radio" name="delivery_method" value="1" id="delivery_method1"<?php echo $deliver_method[1]; ?>>
                                        <label for="delivery_method1">무료배송</label>
                                    </th>
                                    <td>배송비가 부과되지 않습니다</td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <input type="radio" name="delivery_method" value="2" id="delivery_method2"<?php echo $deliver_method[2]; ?>>
                                        <label for="delivery_method2">착불배송</label>
                                    </th>
                                    <td>주문시 또는 장바구니에 배송비가 <b>[착불]</b> 이라는 글이 출력되며 배송비는 부과되지 않습니다</td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <input type="radio" name="delivery_method" value="3" id="delivery_method3"<?php echo $deliver_method[3]; ?>>
                                        <label for="delivery_method3">유료배송</label>
                                    </th>
                                    <td><input type="text" name="delivery_price" value="<?php echo number_format($config['delivery_price']); ?>" class="frm_input" size="10" onkeyup="addComma(this);"> 원을 주문금액 또는 수량에 상관없이 동일 주문건에 배송비를 한번만 부과됩니다
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <input type="radio" name="delivery_method" value="4" id="delivery_method4"<?php echo $deliver_method[4]; ?>>
                                        <label for="delivery_method4">조건부무료배송</label>
                                    </th>
                                    <td>
                                        <input type="text" name="delivery_price2" value="<?php echo number_format($config['delivery_price2']); ?>" class="frm_input" size="10" onkeyup="addComma(this);"> 원의 배송비를 부과하며 단! 주문금액이
                                        <input type="text" name="delivery_minimum" value="<?php echo number_format($config["delivery_minimum"]); ?>" class="frm_input" size="10" onkeyup="addComma(this);"> 원 이상이면 무료배송 처리됩니다
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <h2>배송/교환/반품안내</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">PC 내용</th>
                    <td>
                        <?php echo $editorPc; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">모바일 내용</th>
                    <td>
                        <?php echo $editorMb; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>

</form>

<?php if($member['grade'] == 99) { ?>
<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="hd">ㆍ꼭! 알아두기</div>
		<div class="desc01 accent">
			<p>ㆍ정책 안내1) 본사, 가맹점, 공급업체는 배송정책을 각각 설정할 수 있고, 주문시 판매자의 배송정책에 따라 배송비가 개별 부과됩니다.</p>
			<p>ㆍ정책 안내2) <b>배송업체</b> 등록은 본사에서만 등록 가능합니다. 가맹점과 공급업체에서 등록요청시 추가등록 해주셔야 합니다.</p>
			<p>ㆍ정책 안내3) 주문이 발생되면 판매자의 상품에 대해서만 분기하여 개별적으로 직배송 처리 됩니다.</p>
		</div>
	 </div>
</div>
<?php } ?>

<script>
function fregform_submit(f) {
	<?php echo $baesong_cont1; ?>
	<?php echo $baesong_cont2; ?>

    return true;
}
</script>
<?=$headTitleEnd?>