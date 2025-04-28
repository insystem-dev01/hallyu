<?=$snbTagStart?>
<?=$admin_head_sub?>
<form name="fregform" method="post" action="<?=base_url()?>MypageProcess/seller_info_update">
    <input type="hidden" name="token" value="">
    <h2>사업자 정보</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">업체코드</th>
                    <td><?php echo $seller['seller_code']; ?></td>
                </tr>
                <tr>
                    <th scope="row">제공상품</th>
                    <td><input type="text" name="seller_item" value="<?php echo $seller['seller_item']; ?>" required itemname="제공상품" class="required frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">업체(법인)명</th>
                    <td><input type="text" name="company_name" value="<?php echo $seller['company_name']; ?>" required itemname="업체(법인)명" class="required frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">대표자명</th>
                    <td><input type="text" name="company_owner" value="<?php echo $seller['company_owner']; ?>" required itemname="대표자명" class="required frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">사업자등록번호</th>
                    <td><input type="text" name="company_saupja_no" value="<?php echo $seller['company_saupja_no']; ?>" required itemname="사업자등록번호" class="required frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">업태</th>
                    <td><input type="text" name="company_item" value="<?php echo $seller['company_item']; ?>" required itemname="업태" class="required frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">종목</th>
                    <td><input type="text" name="company_service" value="<?php echo $seller['company_service']; ?>" required itemname="종목" class="required frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">전화번호</th>
                    <td><input type="text" name="company_tel" value="<?php echo $seller['company_tel']; ?>" required itemname="전화번호" class="required frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">팩스번호</th>
                    <td><input type="text" name="company_fax" value="<?php echo $seller['company_fax']; ?>" class="frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">사업장주소</th>
                    <td>
                        <p><input type="text" name="company_zip" value="<?php echo $seller['company_zip']; ?>" class="frm_input" size="8" maxlength="5"> <a href="javascript:win_zip('fregform', 'company_zip', 'company_addr1', 'company_addr2', 'company_addr3', 'company_addr_jibeon');" class="btn_small grey">주소검색</a></p>
                        <p class="mart3"><input type="text" name="company_addr1" value="<?php echo $seller['company_addr1']; ?>" class="frm_input" size="60"> 기본주소</p>
                        <p class="mart3"><input type="text" name="company_addr2" value="<?php echo $seller['company_addr2']; ?>" class="frm_input" size="60"> 상세주소</p>
                        <p class="mart3"><input type="text" name="company_addr3" value="<?php echo $seller['company_addr3']; ?>" class="frm_input" size="60"> 참고항목
                        <input type="hidden" name="company_addr_jibeon" value="<?php echo $seller['company_addr_jibeon']; ?>"></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">홈페이지</th>
                    <td>
                        <input type="text" name="company_hompage" value="<?php echo $seller['company_hompage']; ?>" class="frm_input" size="30">
                        <?php echo $help['page']; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <h2>정산계좌 정보</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">은행명</th>
                    <td><input type="text" name="bank_name" value="<?php echo $seller['bank_name']; ?>" class="frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">계좌번호</th>
                    <td><input type="text" name="bank_account" value="<?php echo $seller['bank_account']; ?>" class="frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">예금주명</th>
                    <td><input type="text" name="bank_holder" value="<?php echo $seller['bank_holder']; ?>" class="frm_input" size="30"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <h2>담당자 정보</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">담당자명</th>
                    <td><input type="text" name="info_name" value="<?php echo $seller['info_name']; ?>" required itemname="담당자명"class="required frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">담당자 핸드폰</th>
                    <td><input type="text" name="info_tel" value="<?php echo $seller['info_tel']; ?>" required itemname="담당자 핸드폰" class="required frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">담당자 이메일</th>
                    <td><input type="text" name="info_email" value="<?php echo $seller['info_email']; ?>" required email itemname="담당자 이메일" class="required frm_input" size="30"></td>
                </tr>
            <tbody/>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>
</form>
<?=$admin_footer_sub?>
<?=$snbTagEnd?>