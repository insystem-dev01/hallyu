<?=$headTitle?>
<h2>세부설정</h2>
<form name="frmlist" method="post" action="<?=base_url()?>AdminProcess/member_level_form_update" enctype="MULTIPART/FORM-DATA">
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col class="w70">
                <col class="w170">
                <col class="w130">
                <col class="w130">
                <col class="w130">
                <col class="w130">
                <col class="w130">
                <col class="w130">
                <col class="w130">
                <col class="w200">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">레벨</th>
                    <th scope="col">레벨명</th>
                    <th scope="col">할인률</th>
                    <th scope="col">절삭(할인률)</th>
                    <th scope="col">적립률(구매)</th>
                    <th scope="col">적립률(리뷰)</th>
                    <th scope="col">적립률(리뷰구매)</th>
                    <th scope="col">최소주문금액</th>
                    <th scope="col">최대주문금액</th>
                    <th scope="col" colspan="2">등급이미지</th>
                    <th scope="col">등급설명</th>
                </tr>
            </thead>
            <tbody class="list">
                <?=$levelList?>
            </tbody>
	    </table>
    </div>

    <h2>기간설정</h2>
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">계산 기간</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        최근
                        <select name="gb_period">
                            <option value="1" <?php echo ($config['gb_period'] == 1) ? "selected" : "selected"?>>1개월</option>
                            <option value="3" <?php echo ($config['gb_period'] == 3) ? "selected" : ""?>>3개월</option>
                            <option value="6" <?php echo ($config['gb_period'] == 6) ? "selected" : ""?>>6개월</option>
                            <option value="12" <?php echo ($config['gb_period'] == 12) ? "selected" : ""?>>12개월</option>
                        </select>
                        의 구매금액을 기준으로 등급 적용
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large">
    </div>
</form>
<?=$headTitleEnd?>
