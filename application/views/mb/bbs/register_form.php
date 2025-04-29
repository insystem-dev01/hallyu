<?=$OM_POSTCODE_JS?>
<!-- 회원정보 입력/수정 시작 { -->
<!-- 모바일은 입점사 회원가입 안 씀-->
<h2 class="pg_tit_join"><?php echo ($type == "user") ? "회원가입" : "입점사 가입"?></h2>

<script src="<?php echo base_url(); ?>js/jquery.register_form.js"></script>
<?=$certifyJs?>
<form name="fregisterform" id="fregisterform" action="<?php echo $register_action_url; ?>" onsubmit="return fregisterform_submit(this);" method="post" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="mb_recommend" id="reg_mb_recommend" value="<?php echo $member['pt_id']; ?>">
    <input type="hidden" name="cert_type" value="<?php echo $member['mb_certify']; ?>">
    <input type="hidden" name="cert_no" value="">
    <input type="hidden" name="type" value="<?=$type?>">

    <table>
        <colgroup>
            <col class="w100">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <th colspan="2" class="title">사이트 이용정보</th>
            </tr>
            <?=$notUTag?>
            <tr>
                <th scope="row"><label for="reg_mb_password">비밀번호</label></th>
                <td style="flex-direction: column;">
                    <input type="password" name="mb_password" id="reg_mb_password"<?php echo $required; ?> class="frm_input" maxlength="20" placeholder="비밀번호를 입력해주세요" onkeyup="pwd_check(this)">
                    <div id="pwdInfo" style="margin-top: 10px;"></div>
                    <input type="hidden" id="pwdDigitConfirm">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="reg_mb_password_re">비밀번호 확인</label></th>
                <td style="flex-direction: column;">
                    <input type="password" name="mb_password_re" id="reg_mb_password_re"<?php echo $required; ?> class="frm_input" maxlength="20" placeholder="비밀번호를 한번 더 입력해주세요" onkeyup="pwd_dup_check(this)">
                    <div id="pwdDupInfo" style="margin-top: 10px;"></div>
                    <input type="hidden" id="pwdDupConfirm">
                </td>
            </tr>
            <?php if($type == "user") { ?>
            <tr>
                <th colspan="2" class="title">개인정보</th>
            </tr>
            <tr class="first_line">
                <th scope="row"><label for="reg_mb_nickname">닉네임</label></th>
                <td>
                    <input type="text" name="mb_nickname" id="reg_mb_nickname" <?php echo $required; ?> class="frm_input" value="<?php echo isset($member['nickname']) ? $member['nickname'] : ""?>">
                    <button type="button" onclick="reg_mb_nickname_check()">중복확인</button>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="reg_mb_img">이미지</label></th>
                <td>
                    <input type="file" name="mb_img" id="reg_mb_img">
                    <label for="reg_mb_img">이미지선택</label>
                    <span class="frm_info">400*400 이하 사이즈만 업로드 가능합니다.</span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="reg_mb_name">이름</label></th>
                <td>
                    <?=$memberName?>
                    <?=$cert_strTag?>
                    <?=$msgCertifyTag?>
                    <?=$cf_cert_useTag?>
                </td>
            </tr>
            <?php } ?>
            <?php if($type == "seller" && false) { ?>
                <tr>
                    <th colspan="2" class="title">사업자 정보</th>
                </tr>
                <tr class="first_line">
                    <th scope="row"><label for="reg_mb_name">이름</label></th>
                    <td>
                        <?=$memberName?>
                        <?=$cert_strTag?>
                        <?=$msgCertifyTag?>
                        <?=$cf_cert_useTag?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_no">사업자등록번호</label></th>
                    <?php 
                        $company_saupja_no = "";
                        if(!empty($seller['company_saupja_no'])) {
                            $company_saupja_no = $seller['company_saupja_no'];
                            $company_saupja_no = explode("-",$company_saupja_no);
                        }
                    ?>
                    <td>
                        <input type="text" name="company_no_01" id="reg_company_no_01"<?php echo $required; ?> class="frm_input small_box" maxlength="3" onkeyup="only_num_typing(this)" value="<?php echo isset($seller['company_saupja_no']) ? $company_saupja_no[0] : ""?>" placeholder="123">
                        <span class="dash">-</span>
                        <input type="text" name="company_no_02" id="reg_company_no_02"<?php echo $required; ?> class="frm_input small_box" maxlength="2" onkeyup="only_num_typing(this)" value="<?php echo isset($seller['company_saupja_no']) ? $company_saupja_no[1] : ""?>" placeholder="45">
                        <span class="dash">-</span>
                        <input type="text" name="company_no_03" id="reg_company_no_03"<?php echo $required; ?> class="frm_input small_box" maxlength="5" onkeyup="only_num_typing(this)" value="<?php echo isset($seller['company_saupja_no']) ? $company_saupja_no[2] : ""?>" placeholder="56789">
                        <button type="button" onclick="reg_company_number_check()">중복확인</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_owner">대표자명</label></th>
                    <td>
                        <input type="text" name="company_owner" id="reg_company_owner"<?php echo $required; ?> class="frm_input" placeholder="홍길동" value="<?php echo isset($seller['company_owner']) ? $seller['company_owner'] : ""?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_tax_email">세금계산서 이메일</label></th>
                    <?php 
                        $company_tax_email = array('','');
                        if(!empty($seller['company_tax_email'])) {
                            $company_tax_email = $seller['company_tax_email'];
                            $company_tax_email = explode("@",$company_tax_email);
                        }
                    ?>
                    <td class="vertical">
                        <div>
                            <input type="text" name="company_tax_email" id="reg_company_tax_email"<?php echo $required; ?> class="frm_input mideum_box" placeholder="example" value="<?php echo isset($company_tax_email[0]) ? $company_tax_email[0] : ""?>">
                            <span class="dash">@</span>
                            <select name="company_tax_email_addr" class="frm_input mideum_box" onchange="email_addr_change(this)" data-type="company_tax_email_ect">
                                <option value="naver.com"   <?= $company_tax_email[1] == "naver.com" ? "selected" : "" ?>>naver.com</option>
                                <option value="daum.net"    <?= $company_tax_email[1] == "daum.net" ? "selected" : "" ?>>daum.net</option>
                                <option value="gmail.com"   <?= $company_tax_email[1] == "gmail.com" ? "selected" : "" ?>>gmail.com</option>
                                <option value="nate.com"    <?= $company_tax_email[1] == "nate.com" ? "selected" : "" ?>>nate.com</option>
                                <option value="hanmail.net" <?= $company_tax_email[1] == "hanmail.net" ? "selected" : "" ?>>hanmail.net</option>
                                <option value="ect"         <?= $company_tax_email[1] != "" && $company_tax_email[1] != "naver.com" &&  $company_tax_email[1] != "daum.net" && $company_tax_email[1] != "gmail.com" && $company_tax_email[1] != "nate.com" && $company_tax_email[1] != "hanmail.net" ? "selected" : "" ?>>직접입력</option>
                            </select>
                        </div>
                        <input type="text" name="company_tax_email_ect" id="company_tax_email_ect" class="email_ect" value="<?=$company_tax_email[1]?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_item">업태</label></th>
                    <td>
                        <input type="text" name="company_item" id="reg_company_item"<?php echo $required; ?> class="frm_input" value="<?php echo isset($seller['company_item']) ? $seller['company_item'] : ""?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_service">종목</label></th>
                    <td>
                        <input type="text" name="company_service" id="reg_company_service"<?php echo $required; ?> class="frm_input" value="<?php echo isset($seller['company_service']) ? $seller['company_service'] : ""?>">
                    </td>
                </tr>
            <?php } ?>
            <?php if($type == "user") { ?>
            <?=$register_use_hpTag?>
            <tr>
                <th scope="row"><label for="reg_mb_email">E-mail</label></th>
                <?php 
                    $mb_email = array('','');
                    if(!empty($member['email'])) {
                        $mb_email = $member['email'];
                        $mb_email = explode("@",$mb_email);
                    }
                ?>
                <td class="vertical">
                    <input type="hidden" name="old_email" value="<?php echo $member['email']; ?>">
                    <div>
                        <input type="text" name="mb_email" id="reg_mb_email"<?php echo $required; ?> class="frm_input mideum_box" placeholder="example" value="<?php echo isset($mb_email[0]) ? $mb_email[0] : ""?>">
                        <span class="dash">@</span>
                        <select name="mb_email_addr" class="frm_input mideum_box" onchange="email_addr_change(this)" data-type="mb_email_ect">
                            <option value="naver.com"   <?= $mb_email[1] == "naver.com" ? "selected" : "" ?>>naver.com</option>
                            <option value="daum.net"    <?= $mb_email[1] == "daum.net" ? "selected" : "" ?>>daum.net</option>
                            <option value="gmail.com"   <?= $mb_email[1] == "gmail.com" ? "selected" : "" ?>>gmail.com</option>
                            <option value="nate.com"    <?= $mb_email[1] == "nate.com" ? "selected" : "" ?>>nate.com</option>
                            <option value="hanmail.net" <?= $mb_email[1] == "hanmail.net" ? "selected" : "" ?>>hanmail.net</option>
                            <option value="ect"         <?= $mb_email[1] != "" && $mb_email[1] != "naver.com" &&  $mb_email[1] != "daum.net" && $mb_email[1] != "gmail.com" && $mb_email[1] != "nate.com" && $mb_email[1] != "hanmail.net" ? "selected" : "" ?>>직접입력</option>
                        </select>
                    </div>
                    <input type="text" name="mb_email_ect" id="mb_email_ect" class="email_ect" value="<?=$mb_email[1]?>">
                    <span class="frm_info">
                        <input type="checkbox" name="mb_mailling" value="Y" id="reg_mb_mailling"<?php echo $memberMail;?>>
                        <label for="reg_mb_mailling">
                            <span>정보 메일을 받겠습니다.</span>
                        </label>
                    </span>
                </td>
            </tr>
            <?php } ?> 
            <?php if($type == "seller" && false) { ?>
                <tr>
                    <th scope="row"><label for="reg_company_tel">대표번호</label></th>
                    <td>
                        <input type="text" name="company_tel" id="reg_company_tel"<?php echo $required; ?> class="frm_input" placeholder="하이픈(-)을 포함하여 입력해주세요" value="<?php echo isset($seller['company_tel']) ? $seller['company_tel'] : ""?>" onkeyup="only_phone_text_typing(this)">
                    </td>
                </tr>
            <?php } ?>
            <?=$regiUseAddrTag?>
            <?php if($type == "seller" && false) { ?>
                <tr>
                    <th scope="row"><label for="reg_company_hompage">홈페이지/판매처 URL</label></th>
                    <td>
                        <input type="text" name="company_hompage" id="reg_company_hompage"<?php echo $required; ?> class="frm_input" value="<?php echo isset($seller['company_hompage']) ? $seller['company_hompage'] : ""?>" placeholder="https://hallyu.kr">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_memo">회사소개</label></th>
                    <td>
                        <textarea name="memo" id="reg_memo"><?php echo isset($seller['memo']) ? $seller['memo'] : ""?></textarea>
                    </td>
                </tr>

                <tr>
                    <th colspan="2" class="title">상품정보</th>
                </tr>
                <tr class="first_line">
                    <th scope="row"><label for="reg_company_brand_name">브랜드명</label></th>
                    <td>
                        <input type="text" name="company_brand_name" id="reg_company_brand_name"<?php echo $required; ?> class="frm_input" value="<?php echo isset($seller['info_name']) ? $seller['info_name'] : ""?>" placeholder="예) 캘빈클라인">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_category">카테고리</label></th>
                    <?php 
                        $company_category = "";
                        if(!empty($seller['company_category'])) {
                            $company_category = $seller['company_category'];
                            $company_category = explode(",",$company_category);
                        }
                    ?>
                    <td class="category">
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="1" <?php echo (isset($seller['company_category']) && in_array("1", $company_category)) ? 'checked' : ''; ?>> <span class="dash">의류</span></div>
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="2" <?php echo (isset($seller['company_category']) && in_array("2", $company_category)) ? 'checked' : ''; ?>> <span class="dash">생활용품</span></div>
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="3" <?php echo (isset($seller['company_category']) && in_array("3", $company_category)) ? 'checked' : ''; ?>> <span class="dash">주방용품</span></div>
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="4" <?php echo (isset($seller['company_category']) && in_array("4", $company_category)) ? 'checked' : ''; ?>> <span class="dash">욕실용품</span></div>
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="5" <?php echo (isset($seller['company_category']) && in_array("5", $company_category)) ? 'checked' : ''; ?>> <span class="dash">반려동물용품</span></div>
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="6" <?php echo (isset($seller['company_category']) && in_array("6", $company_category)) ? 'checked' : ''; ?>> <span class="dash">가구</span></div>
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="7" <?php echo (isset($seller['company_category']) && in_array("7", $company_category)) ? 'checked' : ''; ?>> <span class="dash">식품</span></div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_item_info">상품소개</label></th>
                    <td>
                        <textarea name="company_item_info" id="reg_company_item_info"><?php echo isset($seller['company_item_info']) ? $seller['company_item_info'] : ""?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_sales_type">판매형태</label></th>
                    <?php 
                        $company_sales_type = "";
                        if(!empty($seller['company_sales_type'])) {
                            $company_sales_type = $seller['company_sales_type'];
                            $company_sales_type = explode(",",$company_sales_type);
                        }
                    ?>
                    <td>
                        <input type="checkbox" name="company_sales_type[]" value="1" <?php echo (isset($seller['company_sales_type']) && in_array("1", $company_sales_type)) ? 'checked' : ''; ?>> <span class="dash">수입</span>
                        <input type="checkbox" name="company_sales_type[]" value="2" <?php echo (isset($seller['company_sales_type']) && in_array("2", $company_sales_type)) ? 'checked' : ''; ?>> <span class="dash">제조</span>
                        <input type="checkbox" name="company_sales_type[]" value="3" <?php echo (isset($seller['company_sales_type']) && in_array("3", $company_sales_type)) ? 'checked' : ''; ?>> <span class="dash">유통</span>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_entry">입점몰</label></th>
                    <td>
                        <input type="text" name="company_entry" id="reg_company_entry"<?php echo $required; ?> class="frm_input" value="<?php echo isset($seller['info_name']) ? $seller['info_name'] : ""?>" placeholder="예) 쿠팡, 11번가 등 현재 입점몰을 입력해주세요.">
                    </td>
                </tr>

                <tr>
                    <th colspan="2" class="title">정산 계좌 정보</th>
                </tr>
                <tr class="first_line">
                    <th scope="row"><label for="reg_bank_name">은행명</label></th>
                    <td>
                        <input type="text" name="bank_name" id="reg_bank_name"<?php echo $required; ?> class="frm_input" value="<?php echo isset($seller['bank_name']) ? $seller['bank_name'] : ""?>" placeholder="">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_bank_account">계좌번호</label></th>
                    <td>
                        <input type="text" name="bank_account" id="reg_bank_account"<?php echo $required; ?> class="frm_input" value="<?php echo isset($seller['bank_account']) ? $seller['bank_account'] : ""?>" placeholder="" onkeyup="only_phone_text_typing(this)">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_bank_holder">예금주명</label></th>
                    <td>
                        <input type="text" name="bank_holder" id="reg_bank_holder"<?php echo $required; ?> class="frm_input" value="<?php echo isset($seller['bank_holder']) ? $seller['bank_holder'] : ""?>" placeholder="김베리">
                    </td>
                </tr>

                <tr>
                    <th colspan="2" class="title">담당자 정보</th>
                </tr>
                <tr class="first_line">
                    <th scope="row"><label for="reg_info_name">담당자명</label></th>
                    <td>
                        <input type="text" name="info_name" id="reg_info_name"<?php echo $required; ?> class="frm_input" value="<?php echo isset($seller['info_name']) ? $seller['info_name'] : ""?>" placeholder="김베리">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_info_tel">담당자휴대폰</label></th>
                    <?php 
                        $info_tel = "";
                        if(!empty($seller['info_tel'])) {
                            $info_tel = $seller['info_tel'];
                            $info_tel = explode("-",$info_tel);
                        }
                    ?>
                    <td>
                        <input type="text" name="info_tel_01" <?php echo $required; ?> class="frm_input small_box" value="<?php echo isset($seller['info_tel']) ? $info_tel[0] : "010"?>"  maxlength="3" onkeyup="only_num_typing(this)">
                        <span class="dash">-</span>
                        <input type="text" name="info_tel_02" <?php echo $required; ?> class="frm_input small_box" value="<?php echo isset($seller['info_tel']) ? $info_tel[1] : ""?>" maxlength="4" onkeyup="only_num_typing(this)" placeholder="1234">
                        <span class="dash">-</span>
                        <input type="text" name="info_tel_03" <?php echo $required; ?> class="frm_input small_box" value="<?php echo isset($seller['info_tel']) ? $info_tel[2] : ""?>" maxlength="4" onkeyup="only_num_typing(this)" placeholder="5678">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_info_email">담당자이메일</label></th>
                    <?php 
                        $info_email = array('','');
                        if(!empty($seller['info_email'])) {
                            $info_email = $seller['info_email'];
                            $info_email = explode("@",$info_email);
                        }
                    ?>
                    <td class="vertical">
                        <div>
                            <input type="text" name="info_email" id="reg_info_email"<?php echo $required; ?> class="frm_input mideum_box" placeholder="example" value="<?php echo isset($info_email[0]) ? $info_email[0] : ""?>">
                            <span class="dash">@</span>
                            <select name="info_email_addr" class="frm_input mideum_box" onchange="email_addr_change(this)" data-type="info_email_ect">
                                <option value="naver.com"   <?= $info_email[1] == "naver.com" ? "selected" : "" ?>>naver.com</option>
                                <option value="daum.net"    <?= $info_email[1] == "daum.net" ? "selected" : "" ?>>daum.net</option>
                                <option value="gmail.com"   <?= $info_email[1] == "gmail.com" ? "selected" : "" ?>>gmail.com</option>
                                <option value="nate.com"    <?= $info_email[1] == "nate.com" ? "selected" : "" ?>>nate.com</option>
                                <option value="hanmail.net" <?= $info_email[1] == "hanmail.net" ? "selected" : "" ?>>hanmail.net</option>
                                <option value="ect"         <?= $info_email[1] != "" && $info_email[1] != "naver.com" &&  $info_email[1] != "daum.net" && $info_email[1] != "gmail.com" && $info_email[1] != "nate.com" && $info_email[1] != "hanmail.net" ? "selected" : "" ?>>직접입력</option>
                            </select>
                        </div>
                        <input type="text" name="info_email_ect" id="info_email_ect" class="email_ect" value="<?=$info_email[1]?>">
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php if($w == "") { ?>
    <div class="line"></div>
    <table class="terms">
        <tbody>
            <tr>
                <th><label for="reg_mb_terms">이용약관동의</label></th>
                <td>
                    <div class="vertical terms">
                        <div class="terms_box title">
                            <input type="checkbox" name="chk_all" value="1" id="chk_all">
                            <label for="chk_all">
                                <span>전체 동의합니다.</span>
                            </label>
                        </div>
                        <?php if($type == "user") { ?>
                        <div class="terms_box">
                            <input type="checkbox" name="agree" value="1" id="agree1">
                            <label for="agree1">
                                <span>이용약관 동의<span class="req">(필수)</span></span>
                            </label>
                            <span class="terms" onclick="open_term(1);">약관 보기 ></span>
                        </div>
                        <div id="term1" class="text_box"><?php echo $config['shop_provision']; ?></div>
                        <?php } ?>
                        <div class="terms_box">
                            <input type="checkbox" name="agree2" value="1" id="agree2">
                            <label for="agree2">
                                <span>개인정보 수집 · 이용 동의<span class="req">(필수)</span></span>
                            </label>
                            <span class="terms" onclick="open_term(2);">약관 보기 ></span>
                        </div>
                        <div id="term2" class="text_box">
                            <?php if($type == "user") {
                                echo $config['shop_private']; 
                            } else { ?>
                                신규입점 문의를 위한 정보수집 및 이용동의 <br> <br>
                                주식회사 그로(이하 "회사"라 함)는 개인정보보호법, 정보통신망 이용촉진 및 정보보호 등에 관한 법률 등 관련 법령상의
                                개인 정보보호 규정을 준수하며, 파트너의 개인정보 보호에 최선을 다하고 있습니다. <br> <br> <br>
                                1. 개인정보 수집 및 이용주체 <br>
                                　입점 신청을 통해 제공하신 정보는 "회사"가 직접 접수하고 관리합니다. <br>
                                2. 동의를 거부할 권리 및 동의 거부에 따른 불이익 <br>
                                　신청자는 개인정보제공 등에 관해 동의하지 않을 권리가 있습니다. (이 경우 입점신청이 불가능합니다.) <br>
                                3. 수집하는 개인정보 항목 <br>
                                　필수항목: 담당자명, 담당자 이메일 주소, 담당자 휴대전화 번호 <br>
                                4. 수집 및 이용목적 <br>
                                　입점 검토, 입점 관리시스템의 운용, 공지사항의 전달 등 <br>
                                5. 보유기간 및 이용기간 <br>
                                　수집된 정보는 <span style="text-decoration: underline; font-family: 'Pretendard-Bd';">등록 시점으로 부터 1년간</span> 보관됩니다.
                                
                            <?php } ?>
                        </div>
                        <?php if($type == "user") { ?>
                        <div class="terms_box">
                            <input type="checkbox" name="agree3" value="1" id="agree3">
                            <label for="agree3">
                                <span>마케팅 수신 동의<span class="req">(선택)</span></span>
                            </label>
                            <span class="terms" onclick="open_term(3);">약관 보기 ></span>
                        </div>
                        <div id="term3" class="text_box"><?php echo $config['shop_marketing']; ?></div>
                        <?php } ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <?php } ?>
    <div class="btn_confirm">
        <input type="submit" value="<?=$btnTitle?>" id="btn_submit" accesskey="s">
    </div>
</form>
<script>
$(function() {
    <?=$ipinJs?>
    <?=$hpcertJs?>
});

function open_term(num) {
    let term = document.getElementById('term'+num);
    if(term.className.includes("show")) {
        document.getElementById('term'+num).classList.remove('show');
    } else {
        document.getElementById('term'+num).classList.add('show');
    }
}

function email_addr_change(e){
    var that    = $(e);
    var value   = that.val();
    var type = that.data('type');
    console.log(type);
    if(value == 'ect'){
        $('#'+type).val('');
        $('#'+type).show();
        $('#'+type).focus();
    }else{
        $('#'+type).hide();
        $('#'+type).val('');
    }
}

jQuery(function($){
	// 모두선택
	$("input[name=chk_all]").click(function() {
		if ($(this).prop('checked')) {
			$("input[name^=agree]").prop('checked', true);
		} else {
			$("input[name^=agree]").prop("checked", false);
		}
	});

	$("input[name^=agree]").click(function() {
		$("input[name=chk_all]").prop("checked", false);
	});

    if($("select[name=mb_email_addr]").val() == "ect") {
        $('#mb_email_ect').show();
    }

    if($("select[name=company_tax_email_addr]").val() == "ect") {
        $('#company_tax_email_ect').show();
    }

    if($("select[name=info_email_addr]").val() == "ect") {
        $('#info_email_ect').show();
    }
});

function fregisterform_submit(f) {
	event.preventDefault();

    var base_url = document.getElementById('base_url').value;
    var f = document.getElementById('fregisterform');
    var formData = new FormData(f);

    var type = f.type.value;

    var pwdDigitConfirm = $('#pwdDigitConfirm').val();
    var pwdDupConfirm   = $('#pwdDupConfirm').val();

    if(pwdDigitConfirm != "ok") {
        alert('비밀번호를 형식에 맞게 입력해주세요.');
        return false;
    }

    if(pwdDupConfirm != "ok") {
        alert('비밀번호가 같지 않습니다.');
        return false;
    }

    // 영문자, 숫자, 특수문자를 포함하여 최소 8자 이상
    if(f.w.value == '' || (f.w.value == 'u' && f.mb_password.value != '')) {
        if(f.mb_password.value.length > 7 && /[a-zA-Z]/.test(f.mb_password.value) && /[0-9]/.test(f.mb_password.value) && /[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/.test(f.mb_password.value)){
        } else {
            alert("비밀번호를 영문자, 숫자, 특수문자를 포함하여 8자 이상 입력하세요.");
            f.mb_password.focus();
            return false;
        }
        
        // 비밀번호 확인
        if(f.mb_password.value != f.mb_password_re.value) {
            alert("비밀번호가 같지 않습니다.");
            f.mb_password_re.focus();
            return false;
        }
    }

	// 이름 검사
	if(f.w.value=="") {
		if(f.mb_name.value.length < 1) {
			alert("이름을 입력하십시오.");
			f.mb_name.focus();
			return false;
		}
	}

	<?=$cf_cert_useJs?>

	// E-mail 검사
    // if (type == "user" && ((f.w.value == "") || (f.w.value == "u" && f.mb_email.defaultValue != f.mb_email.value))) {
	// 	var msg = reg_mb_email_check();
	// 	if(msg.trim()) {
	// 		alert(msg);
	// 		f.reg_mb_email.select();
	// 		return false;
	// 	}
	// }

	<?=$regis_use_hpJS?>

	if(typeof(f.mb_recommend) != "undefined" && f.mb_recommend.value && f.w.value == "") {
		if(f.mb_id.value == f.mb_recommend.value) {
			alert("본인을 추천할 수 없습니다.");
			f.mb_recommend.focus();
			return false;
		}

		var msg = reg_mb_recommend_check();
		if(msg.trim()) {
			alert(msg);
			f.mb_recommend.select();
			return false;
		}
	}

    $.ajax({
        url : base_url+'m/process/register_form_update',
        type: 'POST',
        data: formData,
        processData : false,
        contentType : false,
        async: true,
        enctype : 'multipart/form-data',
        beforeSend:function(){
            $('#ajax_loding').show();
            $('body').addClass('noscroll');
        },
        success:function(res){
            $('#ajax_loding').hide();
            $('body').removeClass('noscroll');
            var result = JSON.parse(res);
            alert(result['msg'].trim());
            if(result['res']){
                if(f.w.value == "") {
                    location.href=base_url+'m/bbs/register_result';
                } else {
                    location.reload();
                    if(result['pwd']) {
                        $('input[name=mb_password]').val(result['pwd']);
                        $('input[name=is_update]').val(1);
                    } else {
                    }
                }
            }
        },error:function(a,b,c){
            console.log(a,b,c);
            $('#ajax_loding').hide();
            $('body').removeClass('noscroll');
            alert('서버에서 오류 발생');
        }
    });
}
</script>
<!-- } 회원정보 입력/수정 끝 -->