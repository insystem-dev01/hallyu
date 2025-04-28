<form name="fsellerform" id="fsellerform" action="<?=base_url()?>AdminProcess/pop_sellerformupdate" method="post">
    <input type="hidden" name="mb_id" value="<?php echo $mb_id; ?>">
    <input type="hidden" name="seller_code" value="<?php echo $sr['seller_code']; ?>">

    <div id="sellerform_pop" class="new_win">
        <h1><?php echo $title; ?></h1>
        <section class="new_win_desc marb50">
            <?php echo $mb_pg_anchor; ?>
            <h3 class="anc_tit">사업자정보</h3>
            <div class="tbl_frm01">
                <table class="tablef">
                    <colgroup>
                        <col class="w130">
                        <col>
                        <col class="w130">
                        <col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <th scope="row">입점 승인상태</th>
                            <td class="td_label">
                                <?=$stataArr[1]?>
                                <?=$stataArr[0]?>
                            </td>
                            <th scope="row">전체 상품진열</th>
                            <td class="td_label">
                                <?=$seller_openArr[1]?>
                                <?=$seller_openArr[2]?>
                                <?=$seller_openArr[3]?>
                                <?=$seller_openArr[4]?>
                                </td>
                        </tr>
                        <tr>
                            <th scope="row">입점사명</th>
                            <td><input type="text" name="company_name" value="<?php echo $sr['company_name']; ?>" class="frm_input" size="30"></td>
                            <!-- <th scope="row">제공상품</th>
                            <td><input type="text" name="seller_item" value="<?php echo $sr['seller_item']; ?>" class="frm_input" size="30"></td> -->
                        </tr>
                        <tr>
                            <th scope="row">대표자명</th>
                            <td><input type="text" name="company_owner" value="<?php echo $sr['company_owner']; ?>" class="frm_input" size="30"></td>
                            <th scope="row">사업자등록번호</th>
                            <td><input type="text" name="company_saupja_no" value="<?php echo $sr['company_saupja_no']; ?>" class="frm_input" size="30"></td>
                        </tr>
                        <tr>
                            <th scope="row">업태</th>
                            <td><input type="text" name="company_item" value="<?php echo $sr['company_item']; ?>" class="frm_input" size="30"></td>
                            <th scope="row">종목</th>
                            <td><input type="text" name="company_service" value="<?php echo $sr['company_service']; ?>" class="frm_input" size="30"></td>
                        </tr>
                        <tr>
                            <th scope="row">전화번호</th>
                            <td><input type="text" name="company_tel" value="<?php echo $sr['company_tel']; ?>" class="frm_input" size="30"></td>
                            <th scope="row">팩스번호</th>
                            <td><input type="text" name="company_fax" value="<?php echo $sr['company_fax']; ?>" class="frm_input" size="30"></td>
                        </tr>
                        <tr>
                            <th scope="row">사업자등록증</th>
                            <td colspan="3">
                                <?php if(!empty($sr['company_cert'])) { ?>
                                    <a target="_blank" href="<?=base_url()?>data/certificate/<?=$sr['company_cert']?>">사업자등록증 확인</a>
                                <?php } else { ?>
                                    <span>업로드 된 사업자등록증이 없습니다.</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">세금계산서 발행 이메일</th>
                            <td><input type="text" name="company_tax_email" value="<?php echo $sr['company_tax_email']; ?>" class="frm_input" size="30"></td>
                        </tr>
                        <tr>
                            <th scope="row">사업장주소</th>
                            <td colspan="3">
                                <input type="text" name="company_zip" value="<?php echo $sr['company_zip']; ?>" class="frm_input" size="8" maxlength="5"> <a href="javascript:win_zip('fsellerform', 'company_zip', 'company_addr1', 'company_addr2', 'company_addr3', 'company_addr_jibeon');" class="btn_small grey">주소검색</a>
                                <p class="mart5"><input type="text" name="company_addr1" value="<?php echo $sr['company_addr1']; ?>" class="frm_input" size="60"> 기본주소</p>
                                <p class="mart5"><input type="text" name="company_addr2" value="<?php echo $sr['company_addr2']; ?>" class="frm_input" size="60"> 상세주소</p>
                                <p class="mart5"><input type="text" name="company_addr3" value="<?php echo $sr['company_addr3']; ?>" class="frm_input" size="60"> 참고항목
                                <input type="hidden" name="company_addr_jibeon" value="<?php echo $sr['company_addr_jibeon']; ?>"></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">홈페이지</th>
                            <td colspan="3"><input type="text" name="company_hompage" value="<?php echo $sr['company_hompage']; ?>" class="frm_input" size="40" placeholder="http://"></td>
                        </tr>
                        <tr>
                            <th scope="row">회사소개</th>
                            <td colspan="3"><textarea name="memo" class="frm_textbox wfull"><?php echo $sr['memo']; ?></textarea></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h3 class="anc_tit mart30">상품 정보</h3>
            <div class="tbl_frm01">
                <table class="tablef">
                    <colgroup>
                        <col class="w130">
                        <col>
                        <col class="w130">
                        <col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <th scope="row">브랜드명</th>
                            <td><input type="text" name="company_brand_name" value="<?php echo $sr['company_brand_name']; ?>" class="frm_input" size="30"></td>
                            <th scope="row">카테고리</th>
                            <?php 
                                $company_category = $sr['company_category'];
                                $company_category = explode(",",$company_category);
                            ?>
                            <td>
                                <input type="checkbox" name="company_category[]" value="1" <?php echo in_array("1", $company_category) ? 'checked' : ''; ?>> 의류
                                <input type="checkbox" name="company_category[]" value="2" <?php echo in_array("2", $company_category) ? 'checked' : ''; ?>> 생활용품
                                <input type="checkbox" name="company_category[]" value="3" <?php echo in_array("3", $company_category) ? 'checked' : ''; ?>> 주방용품
                                <input type="checkbox" name="company_category[]" value="4" <?php echo in_array("4", $company_category) ? 'checked' : ''; ?>> 욕실용품
                                <input type="checkbox" name="company_category[]" value="5" <?php echo in_array("5", $company_category) ? 'checked' : ''; ?>> 반려동물용품
                                <input type="checkbox" name="company_category[]" value="6" <?php echo in_array("6", $company_category) ? 'checked' : ''; ?>> 가구
                                <input type="checkbox" name="company_category[]" value="7" <?php echo in_array("7", $company_category) ? 'checked' : ''; ?>> 식품
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">상품소개</th>
                            <td colspan="3"><textarea name="company_item_info" class="frm_textbox wfull"><?php echo $sr['company_item_info']; ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row">판매형태</th>
                            <?php 
                                $company_sales_type = $sr['company_sales_type'];
                                $company_sales_type = explode(",",$company_sales_type);
                            ?>
                            <td>
                                <input type="checkbox" name="company_sales_type[]" value="1" <?php echo in_array("1", $company_sales_type) ? 'checked' : ''; ?>> 수입
                                <input type="checkbox" name="company_sales_type[]" value="2" <?php echo in_array("2", $company_sales_type) ? 'checked' : ''; ?>> 제조
                                <input type="checkbox" name="company_sales_type[]" value="3" <?php echo in_array("3", $company_sales_type) ? 'checked' : ''; ?>> 유통
                            </td>
                            <th scope="row">입점몰</th>
                            <td><input type="text" name="company_entry" value="<?php echo $sr['company_entry']; ?>" class="frm_input" size="30"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h3 class="anc_tit mart30">정산계좌 정보</h3>
            <div class="tbl_frm01">
                <table class="tablef">
                    <colgroup>
                        <col class="w130">
                        <col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <th scope="row">은행명</th>
                            <td><input type="text" name="bank_name" value="<?php echo $sr['bank_name']; ?>" class="frm_input" size="30"></td>
                        </tr>
                        <tr>
                            <th scope="row">계좌번호</th>
                            <td><input type="text" name="bank_account" value="<?php echo $sr['bank_account']; ?>" class="frm_input" size="30"></td>
                        </tr>
                        <tr>
                            <th scope="row">예금주명</th>
                            <td><input type="text" name="bank_holder" value="<?php echo $sr['bank_holder']; ?>" class="frm_input" size="30"></td>
                        </tr>
                        <tr>
                            <th scope="row">통장사본</th>
                            <td colspan="3">
                                <?php if(!empty($sr['bank_cert'])) { ?>
                                    <a target="_blank" href="<?=base_url()?>data/certificate/<?=$sr['bank_cert']?>">통장사본 확인</a>
                                <?php } else { ?>
                                    <span>업로드 된 통장사본이 없습니다.</span>
                                <?php } ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h3 class="anc_tit mart30">담당자 정보</h3>
            <div class="tbl_frm01">
                <table class="tablef">
                    <colgroup>
                        <col class="w130">
                        <col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <th scope="row">담당자명</th>
                            <td><input type="text" name="info_name" value="<?php echo $sr['info_name']; ?>" class="frm_input" size="30"></td>
                        </tr>
                        <tr>
                            <th scope="row">담당자 핸드폰</th>
                            <td><input type="text" name="info_tel" value="<?php echo $sr['info_tel']; ?>" class="frm_input" size="30"></td>
                        </tr>
                        <tr>
                            <th scope="row">담당자 이메일</th>
                            <td><input type="text" name="info_email" value="<?php echo $sr['info_email']; ?>" class="frm_input" size="30"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="btn_confirm">
                <input type="submit" value="저장" class="btn_medium" accesskey="s">
                <button type="button" class="btn_medium bx-white" onclick="window.close();">닫기</button>
            </div>
        </section>
    </div>
</form>