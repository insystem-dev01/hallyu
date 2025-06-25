<?= $OM_POSTCODE_JS ?>
<!-- 회원정보 입력/수정 시작 { -->
<!-- 모바일은 입점사 회원가입 안 씀-->
<h2 class="pg_tit_join">
    <?= ($type == "user") ? $title : ("입점사 " . $title) ?>
</h2>

<script src="<?= base_url(); ?>js/jquery.register_form.js?v=<?= time() ?>"></script>
<?= $certifyJs ?>
<form name="fregisterform" id="fregisterform" action="<?= $register_action_url; ?>" onsubmit="return fregisterform_submit(this);" method="post" autocomplete="off" enctype="multipart/form-data">
    <input type="hidden" name="w" value="<?= $w; ?>">
    <input type="hidden" name="mb_recommend" id="reg_mb_recommend" value="<?= $member['pt_id']; ?>">
    <input type="hidden" name="cert_type" value="<?= $member['mb_certify']; ?>">
    <input type="hidden" name="cert_no" value="">
    <input type="hidden" name="type" value="<?= $type ?>">

    <div class="req">
        <span>*</span> 필수입력사항
    </div>
    <table>
        <!-- <colgroup>
            <col class="w100">
            <col>
        </colgroup> -->
        <tbody>

            <!-- 일반회원 시작 -->
            <?php if ($type == "user") { ?>
                <tr>
                    <th colspan="2" class="title">사이트 이용정보</th>
                </tr>
                <?= $notUTag ?>
                <tr>
                    <th scope="row"><label for="reg_mb_password">비밀번호</label>
                        <span id="pwdInfo" class="check"></span>
                    </th>
                    <td style="flex-direction: column;">
                        <input type="password" name="mb_password" id="reg_mb_password" <?= $required; ?> class="frm_input" maxlength="20" placeholder="비밀번호를 입력해주세요" onkeyup="pwd_check(this)">
                        <input type="hidden" id="pwdDigitConfirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_mb_password_re">비밀번호 확인</label>
                        <span id="pwdDupInfo" class="check"></span>
                    </th>
                    <td style="flex-direction: column;">
                        <input type="password" name="mb_password_re" id="reg_mb_password_re" <?= $required; ?> class="frm_input" maxlength="20" placeholder="비밀번호를 한번 더 입력해주세요" onkeyup="pwd_dup_check(this)">
                        <input type="hidden" id="pwdDupConfirm">
                    </td>
                </tr>
                <tr>
                    <th colspan="2" class="title">사업자 정보</th>
                </tr>
                <tr class="first_line">
                    <th scope="row"><label for="reg_mb_name">상호명</label>
                        <span id="mb_name_check" class="check"></span>
                    </th>
                    <td>
                        <?= $memberName ?>
                        <?= $cert_strTag ?>
                        <?= $msgCertifyTag ?>
                        <?= $cf_cert_useTag ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_no">사업자등록번호</label>
                        <span id="company_no_check" class="check"></span>
                    </th>
                    <?php
                    $company_saupja_no = "";
                    if (!empty($seller['company_saupja_no'])) {
                        $company_saupja_no = $seller['company_saupja_no'];
                        $company_saupja_no = explode("-", $company_saupja_no);
                    }
                    ?>
                    <td>
                        <input type="text" name="company_no_01" id="reg_company_no_01" <?= $required; ?> <?= $readonly; ?> class="frm_input small_box" maxlength="3" onkeyup="only_num_typing(this); company_no_check(this);" value="<?= isset($seller['company_saupja_no']) ? $company_saupja_no[0] : "" ?>" placeholder="123">
                        <span class="dash">-</span>
                        <input type="text" name="company_no_02" id="reg_company_no_02" <?= $required; ?> <?= $readonly; ?> class="frm_input small_box" maxlength="2" onkeyup="only_num_typing(this); company_no_check(this);" value="<?= isset($seller['company_saupja_no']) ? $company_saupja_no[1] : "" ?>" placeholder="45">
                        <span class="dash">-</span>
                        <input type="text" name="company_no_03" id="reg_company_no_03" <?= $required; ?> <?= $readonly; ?> class="frm_input small_box" maxlength="5" onkeyup="only_num_typing(this); company_no_check(this);" value="<?= isset($seller['company_saupja_no']) ? $company_saupja_no[2] : "" ?>" placeholder="56789">
                        <button type="button" onclick="reg_company_number_check()" <?= $disabled; ?>>중복확인</button>
                        <input type="hidden" id="company_no_check_confirm">
                    </td>
                </tr>

                <?php if ($w != 'u'): ?>
                    <tr>
                        <th scope="row"><label for="reg_company_certification">사업자등록증</label>
                            <span id="company_cert_file_check" class="check"></span>
                        </th>
                        <td>
                            <!-- 숨겨진 파일 input -->
                            <input type="file" id="company_cert_file" name="company_cert_file" accept=".jpg,.png,.pdf,.jpeg" onchange="preview_file('company_cert_file', 'company_file_name_display', 'company_cert_file_check', 'company_cert_file_check_confirm')">

                            <!-- 선택된 파일명 표시 -->
                            <input type="text" id="company_file_name_display" readonly placeholder="사업자등록번호 중복확인부터 해주세요.">
                            <!-- 첨부 버튼 -->
                            <button type="button" class="reg_cert_btn" id="active_company_cert" onclick="$('#company_cert_file').click();" disabled>파일첨부</button>
                            <input type="hidden" id="company_cert_file_check_confirm">
                        </td>
                    </tr>
                <?php endif; ?>
                <?= $register_use_hpTag ?>
                <tr>
                    <th scope="row"><label for="reg_mb_email">E-mail</label>
                        <span id="reg_mb_email_check" class="check"></span>
                    </th>
                    <?php
                    $mb_email = array('', '');
                    if (!empty($member['email'])) {
                        $mb_email = $member['email'];
                        $mb_email = explode("@", $mb_email);
                    }
                    ?>
                    <td class="vertical">
                        <input type="hidden" name="old_email" value="<?= $member['email']; ?>">
                        <div>
                            <input type="text" name="mb_email" id="reg_mb_email" <?= $required; ?> class="frm_input mideum_box" placeholder="example" value="<?= isset($mb_email[0]) ? $mb_email[0] : "" ?>" onkeyup="basic_text_check('reg_mb_email','이메일을 작성해주세요.')">
                            <span class="dash">@</span>
                            <select name="mb_email_addr" class="frm_input mideum_box" onchange="email_addr_change(this)" data-type="mb_email_ect">
                                <option value="naver.com" <?= $mb_email[1] == "naver.com" ? "selected" : "" ?>>naver.com</option>
                                <option value="daum.net" <?= $mb_email[1] == "daum.net" ? "selected" : "" ?>>daum.net</option>
                                <option value="gmail.com" <?= $mb_email[1] == "gmail.com" ? "selected" : "" ?>>gmail.com</option>
                                <option value="nate.com" <?= $mb_email[1] == "nate.com" ? "selected" : "" ?>>nate.com</option>
                                <option value="hanmail.net" <?= $mb_email[1] == "hanmail.net" ? "selected" : "" ?>>hanmail.net</option>
                                <option value="ect" <?= $mb_email[1] != "" && $mb_email[1] != "naver.com" &&  $mb_email[1] != "daum.net" && $mb_email[1] != "gmail.com" && $mb_email[1] != "nate.com" && $mb_email[1] != "hanmail.net" ? "selected" : "" ?>>직접입력</option>
                            </select>
                        </div>
                        <input type="text" name="mb_email_ect" id="mb_email_ect" class="email_ect" value="<?= $mb_email[1] ?>">
                        <span class="frm_info">
                            <input type="checkbox" name="mb_mailling" value="Y" id="reg_mb_mailling" <?= $memberMail; ?>>
                            <label for="reg_mb_mailling">
                                <span>정보 메일을 받겠습니다.</span>
                            </label>
                        </span>
                        <input type="hidden" id="reg_mb_email_check_confirm">
                    </td>
                </tr>
                <?= $regiUseAddrTag ?>
            <?php } ?>
            <!-- 일반회원 끝-->

            <!-- 사업자회원 시작-->
            <?php if ($type == "seller") { ?>
                <tr>
                    <th colspan="2" class="title">사이트 이용정보</th>
                </tr>
                <?= $notUTag ?>
                <tr>
                    <th scope="row"><label for="reg_mb_password">비밀번호</label>
                        <span id="pwdInfo" class="check"></span>
                    </th>
                    <td style="flex-direction: column;">
                        <input type="password" name="mb_password" id="reg_mb_password" <?= $required; ?> class="frm_input" maxlength="20" placeholder="비밀번호를 입력해주세요" onkeyup="pwd_check(this)">
                        <input type="hidden" id="pwdDigitConfirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_mb_password_re">비밀번호 확인</label>
                        <span id="pwdDupInfo" class="check"></span>
                    </th>
                    <td style="flex-direction: column;">
                        <input type="password" name="mb_password_re" id="reg_mb_password_re" <?= $required; ?> class="frm_input" maxlength="20" placeholder="비밀번호를 한번 더 입력해주세요" onkeyup="pwd_dup_check(this)">
                        <input type="hidden" id="pwdDupConfirm">
                    </td>
                </tr>
                <tr>
                    <th colspan="2" class="title">사업자 정보</th>

                </tr>
                <tr>
                    <th scope="row"><label for="company_owner">대표자명</label>
                        <span id="company_owner_check" class="check"></span>
                    </th>
                    <td>
                        <input type="text" name="company_owner" id="company_owner" <?= $required; ?> class="frm_input" placeholder="ex) 홍길동" value="<?= isset($seller['company_owner']) ? $seller['company_owner'] : "" ?>" onkeyup="basic_text_check('company_owner','대표자명을 작성해주세요.')">

                        <input type="hidden" id="company_owner_check_confirm">
                    </td>
                </tr>
                <tr class="first_line">
                    <th scope="row"><label for="reg_mb_name">상호명</label>
                        <span id="mb_name_check" class="check"></span>
                    </th>
                    <td>
                        <?= $memberName ?>
                        <?= $cert_strTag ?>
                        <?= $msgCertifyTag ?>
                        <?= $cf_cert_useTag ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_no">사업자등록번호</label>
                        <span id="company_no_check" class="check"></span>
                    </th>
                    <?php
                    $company_saupja_no = "";
                    if (!empty($seller['company_saupja_no'])) {
                        $company_saupja_no = $seller['company_saupja_no'];
                        $company_saupja_no = explode("-", $company_saupja_no);
                    }
                    ?>
                    <td>
                        <input type="text" name="company_no_01" id="reg_company_no_01" <?= $required; ?> <?= $readonly; ?> class="frm_input small_box" maxlength="3" onkeyup="only_num_typing(this); company_no_check(this);" value="<?= isset($seller['company_saupja_no']) ? $company_saupja_no[0] : "" ?>" placeholder="123">
                        <span class="dash">-</span>
                        <input type="text" name="company_no_02" id="reg_company_no_02" <?= $required; ?> <?= $readonly; ?> class="frm_input small_box" maxlength="2" onkeyup="only_num_typing(this); company_no_check(this);" value="<?= isset($seller['company_saupja_no']) ? $company_saupja_no[1] : "" ?>" placeholder="45">
                        <span class="dash">-</span>
                        <input type="text" name="company_no_03" id="reg_company_no_03" <?= $required; ?> <?= $readonly; ?> class="frm_input small_box" maxlength="5" onkeyup="only_num_typing(this); company_no_check(this);" value="<?= isset($seller['company_saupja_no']) ? $company_saupja_no[2] : "" ?>" placeholder="56789">
                        <button type="button" onclick="reg_company_number_check()" <?= $disabled; ?>>중복확인</button>

                        <input type="hidden" id="company_no_check_confirm">
                    </td>
                </tr>
                <?php if ($w != 'u'): ?>
                    <tr>
                        <th scope="row"><label for="reg_company_certification">사업자등록증</label>
                            <span id="company_cert_file_check" class="check"></span>
                        </th>
                        <td>
                            <!-- 숨겨진 파일 input -->
                            <input type="file" id="company_cert_file" name="company_cert_file" accept=".jpg,.png,.pdf,.jpeg" onchange="preview_file('company_cert_file', 'company_file_name_display', 'company_cert_file_check', 'company_cert_file_check_confirm')">

                            <!-- 선택된 파일명 표시 -->
                            <input type="text" id="company_file_name_display" readonly placeholder="사업자등록번호 중복확인부터 해주세요.">
                            <!-- 첨부 버튼 -->
                            <button type="button" class="reg_cert_btn" id="active_company_cert" onclick="$('#company_cert_file').click();" disabled>파일첨부</button>

                            <input type="hidden" id="company_cert_file_check_confirm">
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th scope="row"><label for="reg_company_tax_email">세금계산서 이메일</label>
                        <span id="company_tax_email_check" class="check"></span>
                    </th>
                    <?php
                    $company_tax_email = array('', '');
                    if (!empty($seller['company_tax_email'])) {
                        $company_tax_email = $seller['company_tax_email'];
                        $company_tax_email = explode("@", $company_tax_email);
                    }
                    ?>
                    <td class="vertical">
                        <div>
                            <input type="text" name="company_tax_email" id="reg_company_tax_email" <?= $required; ?> class="frm_input small_box2" placeholder="example" value="<?= isset($company_tax_email[0]) ? $company_tax_email[0] : "" ?>" onkeyup="company_tax_email_check(this)">
                            <span class="dash">@</span>
                            <select name="company_tax_email_addr" class="frm_input small_box2" onchange="email_addr_change(this)" data-type="company_tax_email_ect">
                                <option value="naver.com" <?= $company_tax_email[1] == "naver.com" ? "selected" : "" ?>>naver.com</option>
                                <option value="daum.net" <?= $company_tax_email[1] == "daum.net" ? "selected" : "" ?>>daum.net</option>
                                <option value="gmail.com" <?= $company_tax_email[1] == "gmail.com" ? "selected" : "" ?>>gmail.com</option>
                                <option value="nate.com" <?= $company_tax_email[1] == "nate.com" ? "selected" : "" ?>>nate.com</option>
                                <option value="hanmail.net" <?= $company_tax_email[1] == "hanmail.net" ? "selected" : "" ?>>hanmail.net</option>
                                <option value="ect" <?= $company_tax_email[1] != "" && $company_tax_email[1] != "naver.com" &&  $company_tax_email[1] != "daum.net" && $company_tax_email[1] != "gmail.com" && $company_tax_email[1] != "nate.com" && $company_tax_email[1] != "hanmail.net" ? "selected" : "" ?>>직접입력</option>
                            </select>
                            <button type="button" onclick="reg_mb_company_tax_email_check()">중복확인</button>
                        </div>

                        <input type="text" name="company_tax_email_ect" id="company_tax_email_ect" class="email_ect" value="<?= $company_tax_email[1] ?>">
                        <input type="hidden" id="company_tax_email_check_confirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="company_item">업태</label>
                        <span id="company_item_check" class="check"></span>
                    </th>
                    <td>
                        <input type="text" name="company_item" id="company_item" <?= $required; ?> class="frm_input" value="<?= isset($seller['company_item']) ? $seller['company_item'] : "" ?>" onkeyup="basic_text_check('company_item','업태를 작성해주세요.')">

                        <input type="hidden" id="company_item_check_confirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_service">종목</label>
                        <span id="company_service_check" class="check"></span>
                    </th>
                    <td>
                        <input type="text" name="company_service" id="company_service" <?= $required; ?> class="frm_input" value="<?= isset($seller['company_service']) ? $seller['company_service'] : "" ?>" onkeyup="basic_text_check('company_service','종목을 작성해주세요.')">

                        <input type="hidden" id="company_service_check_confirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_tel">대표번호</label>
                        <span id="company_tel_check" class="check"></span>
                    </th>
                    <?php
                    $company_tel = "";
                    if (!empty($seller['company_tel'])) {
                        $company_tel = $seller['company_tel'];
                        $company_tel = explode("-", $company_tel);
                    }
                    ?>
                    <td>
                        <input type="text" name="company_tel_01" id="company_tel_01" <?= $required; ?> class="frm_input small_box2" value="<?= isset($seller['company_tel']) ? $company_tel[0] : "" ?>" maxlength="3" onkeyup="only_num_typing(this); company_tel_check();" placeholder="">
                        <span class="dash">-</span>
                        <input type="text" name="company_tel_02" id="company_tel_02" <?= $required; ?> class="frm_input small_box2" value="<?= isset($seller['company_tel']) ? $company_tel[1] : "" ?>" maxlength="4" onkeyup="only_num_typing(this); company_tel_check();" placeholder="">
                        <span class="dash">-</span>
                        <input type="text" name="company_tel_03" id="company_tel_03" <?= $required; ?> class="frm_input small_box2" value="<?= isset($seller['company_tel']) ? $company_tel[2] : "" ?>" maxlength="4" onkeyup="only_num_typing(this); company_tel_check();" placeholder="">

                        <input type="hidden" id="company_tel_check_confirm">
                    </td>
                </tr>
                <?= $regiUseAddrTag ?>
                <tr>
                    <th scope="row"><label for="company_hompage">홈페이지/판매처 URL</label>
                        <span id="company_hompage_check" class="check"></span>
                    </th>
                    <td>
                        <input type="text" name="company_hompage" id="company_hompage" <?= $required; ?> class="frm_input" value="<?= isset($seller['company_hompage']) ? $seller['company_hompage'] : "" ?>" placeholder="https://hallyu.kr" onkeyup="basic_text_check('company_hompage','홈페이지를 작성해주세요.')">
                        <input type="hidden" id="company_hompage_check_confirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="memo">회사소개</label>
                        <span id="memo_check" class="check"></span>
                    </th>
                    <td>
                        <textarea name="memo" id="memo" onkeyup="basic_text_check('memo','회사소개를 작성해주세요.')" placeholder="회사소개를 작성해주세요."><?= isset($seller['memo']) ? $seller['memo'] : "" ?></textarea>

                        <input type="hidden" id="memo_check_confirm">
                    </td>
                </tr>

                <tr>
                    <th colspan="2" class="title">상품정보</th>
                </tr>
                <tr class="first_line">
                    <th scope="row"><label for="company_brand_name">브랜드명</label>
                        <span id="company_brand_name_check" class="check"></span>
                    </th>
                    <td>
                        <input type="text" name="company_brand_name" id="company_brand_name" <?= $required; ?> class="frm_input" value="<?= isset($seller['info_name']) ? $seller['info_name'] : "" ?>" placeholder="예) 캘빈클라인" onkeyup="basic_text_check('company_brand_name','브랜드명를 작성해주세요.')">

                        <input type="hidden" id="company_brand_name_check_confirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_category">카테고리</label>
                        <span id="company_category_check" class="check"></span>
                    </th>
                    <?php
                    $company_category = "";
                    if (!empty($seller['company_category'])) {
                        $company_category = $seller['company_category'];
                        $company_category = explode(",", $company_category);
                    }
                    ?>
                    <td class="category">
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="1" <?= (isset($seller['company_category']) && in_array("1", $company_category)) ? 'checked' : ''; ?> onclick="company_category_check()"> <span class="dash">의류</span></div>
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="2" <?= (isset($seller['company_category']) && in_array("2", $company_category)) ? 'checked' : ''; ?> onclick="company_category_check()"> <span class="dash">생활용품</span></div>
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="3" <?= (isset($seller['company_category']) && in_array("3", $company_category)) ? 'checked' : ''; ?> onclick="company_category_check()"> <span class="dash">주방용품</span></div>
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="4" <?= (isset($seller['company_category']) && in_array("4", $company_category)) ? 'checked' : ''; ?> onclick="company_category_check()"> <span class="dash">욕실용품</span></div>
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="5" <?= (isset($seller['company_category']) && in_array("5", $company_category)) ? 'checked' : ''; ?> onclick="company_category_check()"> <span class="dash">반려동물용품</span></div>
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="6" <?= (isset($seller['company_category']) && in_array("6", $company_category)) ? 'checked' : ''; ?> onclick="company_category_check()"> <span class="dash">가구</span></div>
                        <div class="chk_box"><input type="checkbox" name="company_category[]" value="7" <?= (isset($seller['company_category']) && in_array("7", $company_category)) ? 'checked' : ''; ?> onclick="company_category_check()"> <span class="dash">식품</span></div>

                        <input type="hidden" id="company_category_check_confirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="company_item_info">상품소개</label>
                        <span id="company_item_info_check" class="check"></span>
                    </th>
                    <td>
                        <textarea name="company_item_info" id="company_item_info" onkeyup="basic_text_check('company_item_info','상품소개를 작성해주세요.')" placeholder="상품소개를 작성해주세요."><?= isset($seller['company_item_info']) ? $seller['company_item_info'] : "" ?></textarea>

                        <input type="hidden" id="company_item_info_check_confirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_company_sales_type">판매형태</label>
                        <span id="company_sales_type_check" class="check"></span>
                    </th>
                    <?php
                    $company_sales_type = "";
                    if (!empty($seller['company_sales_type'])) {
                        $company_sales_type = $seller['company_sales_type'];
                        $company_sales_type = explode(",", $company_sales_type);
                    }
                    ?>
                    <td>
                        <input type="checkbox" name="company_sales_type[]" value="1" onclick="company_sales_type_check()" <?= (isset($seller['company_sales_type']) && in_array("1", $company_sales_type)) ? 'checked' : ''; ?>> <span class="dash">수입</span>
                        <input type="checkbox" name="company_sales_type[]" value="2" onclick="company_sales_type_check()" <?= (isset($seller['company_sales_type']) && in_array("2", $company_sales_type)) ? 'checked' : ''; ?>> <span class="dash">제조</span>
                        <input type="checkbox" name="company_sales_type[]" value="3" onclick="company_sales_type_check()" <?= (isset($seller['company_sales_type']) && in_array("3", $company_sales_type)) ? 'checked' : ''; ?>> <span class="dash">유통</span>

                        <input type="hidden" id="company_sales_type_check_confirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="company_entry">입점몰</label>
                        <span id="company_entry_check" class="check"></span>
                    </th>
                    <td>
                        <input type="text" name="company_entry" id="company_entry" <?= $required; ?> class="frm_input" value="<?= isset($seller['info_name']) ? $seller['info_name'] : "" ?>" placeholder="예) 쿠팡, 11번가 등 현재 입점몰을 입력해주세요." onkeyup="basic_text_check('company_entry','입점몰을 작성해주세요.')">
                        <input type="hidden" id="company_entry_check_confirm">
                    </td>
                </tr>

                <tr>
                    <th colspan="2" class="title">정산 계좌 정보</th>
                </tr>
                <tr class="first_line">
                    <th scope="row"><label for="bank_name">은행명</label>
                        <span id="bank_name_check" class="check"></span>
                    </th>
                    <td>
                        <input type="text" name="bank_name" id="bank_name" <?= $required; ?> <?= $readonly; ?> class="frm_input" value="<?= isset($seller['bank_name']) ? $seller['bank_name'] : "" ?>" placeholder="예) 기업은행" onkeyup="basic_text_check('bank_name','은행명을 작성해주세요.')">

                        <input type="hidden" id="bank_name_check_confirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bank_account">계좌번호</label>
                        <span id="bank_account_check" class="check"></span>
                    </th>
                    <?php
                    $bank_account = "";
                    if (!empty($seller['bank_account'])) {
                        $bank_account = $seller['bank_account'];
                        $bank_account = explode("-", $bank_account);
                    }
                    ?>
                    <td>

                        <input type="text" name="bank_account_01" id="bank_account_01" <?= $required; ?> <?= $readonly; ?> class="frm_input small_box2" value="<?= isset($seller['bank_account']) ? $bank_account[0] : "" ?>" maxlength="10" onkeyup="only_num_typing(this); bank_account_check();" placeholder="">
                        <span class="dash">-</span>
                        <input type="text" name="bank_account_02" id="bank_account_02" <?= $required; ?> <?= $readonly; ?> class="frm_input small_box2" value="<?= isset($seller['bank_account']) ? $bank_account[1] : "" ?>" maxlength="10" onkeyup="only_num_typing(this); bank_account_check();" placeholder="">
                        <span class="dash">-</span>
                        <input type="text" name="bank_account_03" id="bank_account_03" <?= $required; ?> <?= $readonly; ?> class="frm_input small_box2" value="<?= isset($seller['bank_account']) ? $bank_account[2] : "" ?>" maxlength="10" onkeyup="only_num_typing(this); bank_account_check();" placeholder="">

                        <input type="hidden" id="bank_account_check_confirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bank_holder">예금주명</label>
                        <span id="bank_holder_check" class="check"></span>
                    </th>
                    <td>
                        <input type="text" name="bank_holder" id="bank_holder" <?= $required; ?> <?= $readonly; ?> class="frm_input" value="<?= isset($seller['bank_holder']) ? $seller['bank_holder'] : "" ?>" placeholder="김베리" onkeyup="basic_text_check('bank_holder','예금주명을 작성해주세요.')">

                        <input type="hidden" id="bank_holder_check_confirm">
                    </td>
                </tr>

                <?php if ($w != "u"): ?>
                    <tr>
                        <th scope="row"><label for="reg_account_certification">통장사본</label>
                            <span id="bank_account_file_check" class="check"></span>
                        </th>
                        <td>
                            <!-- 숨겨진 파일 input -->
                            <input type="file" id="bank_account_file" name="bank_account_file" accept=".jpg,.png,.pdf,.jpeg" onchange="preview_file('bank_account_file', 'bank_file_name_display', 'bank_account_file_check', 'bank_account_file_check_confirm')">

                            <!-- 선택된 파일명 표시 -->
                            <input type="text" id="bank_file_name_display" readonly placeholder="통장사본 입력 후 파일첨부 해주세요.">
                            <!-- 첨부 버튼 -->
                            <button type="button" class="reg_cert_btn" id="active_bank_cert" onclick="$('#bank_account_file').click();">파일첨부</button>
                            <input type="hidden" id="bank_account_file_check_confirm">
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th colspan="2" class="title">담당자 정보</th>
                </tr>
                <tr class="first_line">
                    <th scope="row"><label for="reg_info_name">담당자명</label>
                        <span id="info_name_check" class="check"></span>
                    </th>
                    <td>
                        <input type="text" name="info_name" id="info_name" <?= $required; ?> class="frm_input" value="<?= isset($seller['info_name']) ? $seller['info_name'] : "" ?>" placeholder="김베리" onkeyup="basic_text_check('info_name','담당자명을 작성해주세요.')">

                        <input type="hidden" id="info_name_check_confirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_info_tel">담당자 휴대폰</label>
                        <span id="info_tel_check" class="check"></span>
                    </th>
                    <?php
                    $info_tel = "";
                    if (!empty($seller['info_tel'])) {
                        $info_tel = $seller['info_tel'];
                        $info_tel = explode("-", $info_tel);
                    }
                    ?>
                    <td>
                        <input type="text" name="info_tel_01" id="info_tel_01" <?= $required; ?> class="frm_input small_box" value="<?= isset($seller['info_tel']) ? $info_tel[0] : "010" ?>" maxlength="3" onkeyup="only_num_typing(this); info_tel_check();" placeholder="010">
                        <span class="dash">-</span>
                        <input type="text" name="info_tel_02" id="info_tel_02" <?= $required; ?> class="frm_input small_box" value="<?= isset($seller['info_tel']) ? $info_tel[1] : "" ?>" maxlength="4" onkeyup="only_num_typing(this); info_tel_check();" placeholder="1234">
                        <span class="dash">-</span>
                        <input type="text" name="info_tel_03" id="info_tel_03" <?= $required; ?> class="frm_input small_box" value="<?= isset($seller['info_tel']) ? $info_tel[2] : "" ?>" maxlength="4" onkeyup="only_num_typing(this); info_tel_check();" placeholder="5678">

                        <input type="hidden" id="info_tel_check_confirm">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="info_email">담당자 이메일</label>
                        <span id="info_email_check" class="check"></span>
                    </th>
                    <?php
                    $info_email = array('', '');
                    if (!empty($seller['info_email'])) {
                        $info_email = $seller['info_email'];
                        $info_email = explode("@", $info_email);
                    }
                    ?>
                    <td class="vertical">
                        <div>
                            <input type="text" name="info_email" id="info_email" <?= $required; ?> class="frm_input mideum_box" placeholder="example" value="<?= isset($info_email[0]) ? $info_email[0] : "" ?>" onkeyup="basic_text_check('info_email','담당자 이메일을 작성해주세요.')">
                            <span class="dash">@</span>
                            <select name="info_email_addr" class="frm_input mideum_box" onchange="email_addr_change(this)" data-type="info_email_ect">
                                <option value="naver.com" <?= $info_email[1] == "naver.com" ? "selected" : "" ?>>naver.com</option>
                                <option value="daum.net" <?= $info_email[1] == "daum.net" ? "selected" : "" ?>>daum.net</option>
                                <option value="gmail.com" <?= $info_email[1] == "gmail.com" ? "selected" : "" ?>>gmail.com</option>
                                <option value="nate.com" <?= $info_email[1] == "nate.com" ? "selected" : "" ?>>nate.com</option>
                                <option value="hanmail.net" <?= $info_email[1] == "hanmail.net" ? "selected" : "" ?>>hanmail.net</option>
                                <option value="ect" <?= $info_email[1] != "" && $info_email[1] != "naver.com" &&  $info_email[1] != "daum.net" && $info_email[1] != "gmail.com" && $info_email[1] != "nate.com" && $info_email[1] != "hanmail.net" ? "selected" : "" ?>>직접입력</option>
                            </select>
                        </div>
                        <input type="text" name="info_email_ect" id="info_email_ect" class="email_ect" value="<?= $info_email[1] ?>">
                        <input type="hidden" id="info_email_check_confirm">
                    </td>
                </tr>
            <?php } ?>
            <!-- 사업자회원 끝-->


            <!-- <?php if ($type == "user") { ?>
                <tr>
                    <th colspan="2" class="title">개인정보</th>
                </tr>
                <tr class="first_line">
                    <th scope="row"><label for="reg_mb_nickname">닉네임</label></th>
                    <td>
                        <input type="text" name="mb_nickname" id="reg_mb_nickname" <?= $required; ?> class="frm_input" value="<?= isset($member['nickname']) ? $member['nickname'] : "" ?>">
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
                        <?= $memberName ?>
                        <?= $cert_strTag ?>
                        <?= $msgCertifyTag ?>
                        <?= $cf_cert_useTag ?>
                    </td>
                </tr>
            <?php } ?> -->


            <?php if ($w == "") { ?>
                <tr>
                    <th colspan="2" class="title">이용약관</th>
                </tr>
                <tr class="first_line">
                    <table class="terms">
                        <tbody>
                            <tr>
                                <th><label for="reg_mb_terms">이용약관동의</label></th>
                                <td>
                                    <div class="vertical terms">
                                        <!-- <div class="terms_box title">
                                <input type="checkbox" name="chk_all" value="1" id="chk_all">
                                <label for="chk_all">
                                    <span>전체 동의합니다.</span>
                                </label>
                            </div> -->
                                        <?php if ($type == "user") { ?>
                                            <div class="terms_box">
                                                <input type="checkbox" name="agree1" value="1" id="agree1">
                                                <label for="agree1">
                                                    <span>이용약관 동의<span class="req">(필수)</span></span>
                                                </label>
                                                <span class="terms" onclick="open_term(1);">약관 보기 ></span>
                                            </div>
                                            <div id="term1" class="text_box"><?= $config['shop_provision']; ?></div>
                                        <?php } ?>
                                        <div class="terms_box">
                                            <input type="checkbox" name="agree2" value="1" id="agree2">
                                            <label for="agree2">
                                                <span>개인정보 수집 · 이용 동의<span class="req">(필수)</span></span>
                                            </label>
                                            <span class="terms" onclick="open_term(2);">약관 보기 ></span>
                                        </div>
                                        <div id="term2" class="text_box">
                                            <?php if ($type == "user") {
                                                echo $config['shop_private'];
                                            } else { ?>
                                                신규입점 문의를 위한 정보수집 및 이용동의 <br> <br>
                                                주식회사 더한류(이하 "회사"라 함)는 개인정보보호법, 정보통신망 이용촉진 및 정보보호 등에 관한 법률 등 관련 법령상의
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

                                    </div>
                                </td>
                            </tr>

                            <?php if ($type == "seller") { ?>
                                <tr>
                                    <th colspan="2" class="title">정책사항 약관</th>
                                </tr>
                                <tr class="first_line">
                                    <th><label for="reg_mb_terms">정책사항 약관 동의</label></th>
                                    <td>
                                        <div class="vertical terms">
                                            <!-- 배송정책 약관동의 -->
                                            <div class="terms_box">
                                                <input type="checkbox" name="agree4" value="1" id="agree4" onclick="return checkTermBeforeAgree(4);">
                                                <label for="agree4">
                                                    <span>배송정책 약관동의 <span class="req">(필수)</span></span>
                                                </label>
                                                <span class="terms" onclick="open_term(4);">약관 보기 ></span>
                                            </div>
                                            <span id="agree_check4" class="warn_msg"></span>
                                            <div id="term4" class="text_box"><?= nl2br($config['shop_delivery_policy']); ?></div>

                                            <!-- 결제정책 약관동의 -->
                                            <div class="terms_box">
                                                <input type="checkbox" name="agree5" value="1" id="agree5" onclick="return checkTermBeforeAgree(5);">
                                                <label for="agree5">
                                                    <span>결제정책 약관동의<span class="req">(필수)</span>
                                                    </span>
                                                </label>
                                                <span class="terms" onclick="open_term(5);">약관 보기 ></span>
                                            </div>
                                            <span id="agree_check5" class="warn_msg"></span>
                                            <div id="term5" class="text_box"><?= nl2br($config['shop_charge_policy']); ?></div>

                                            <!-- 정산정책 약관동의 -->
                                            <div class="terms_box">
                                                <input type="checkbox" name="agree6" value="1" id="agree6" onclick="return checkTermBeforeAgree(6);">
                                                <label for="agree6">
                                                    <span>정산정책 약관동의<span class="req">(필수)</span>

                                                    </span>
                                                </label>
                                                <span class="terms" onclick="open_term(6);">약관 보기 ></span>
                                            </div>
                                            <span id="agree_check6" class="warn_msg"></span>
                                            <div id="term6" class="text_box"><?= nl2br($config['shop_calculate_policy']); ?></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>

        </tbody>
    </table>


    <div class="btn_confirm">
        <button type="button" id="btn_cancel" onclick="history.back();" style="margin-left:10px;">
            뒤로가기
        </button>
        <input type="submit" value="<?= $btnTitle ?>" id="btn_submit" accesskey="s">

    </div>
</form>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const agreements = [{
                contentId: "term4",
                checkboxId: "agree4",
                warnId: "agree_check4"
            },
            {
                contentId: "term5",
                checkboxId: "agree5",
                warnId: "agree_check5"
            },
            {
                contentId: "term6",
                checkboxId: "agree6",
                warnId: "agree_check6"
            },
        ];

        agreements.forEach(({
            contentId,
            checkboxId,
            warnId
        }) => {
            const contentBox = document.getElementById(contentId);
            const checkbox = document.getElementById(checkboxId);
            const warnMsg = document.getElementById(warnId);

            // 스크롤 감지 후 자동으로 체크 허용
            contentBox.addEventListener("scroll", function() {
                const atBottom =
                    contentBox.scrollTop + contentBox.clientHeight >= contentBox.scrollHeight - 2;
                if (atBottom) {
                    checkbox.dataset.scrolled = "true"; // custom flag로 체크 허용
                    warnMsg.textContent = ""; // 경고 제거
                }
            });
        });
    });

    function open_term(num) {
        const term = document.getElementById("term" + num);
        term.classList.toggle("show");
    }

    // 체크박스 클릭 시 검사
    function checkTermBeforeAgree(num) {
        const termBox = document.getElementById("term" + num);
        const checkbox = document.getElementById("agree" + num);
        const warnMsg = document.getElementById("agree_check" + num);

        const hasScrolled = checkbox.dataset.scrolled === "true";

        if (!hasScrolled) {
            checkbox.checked = false;
            warnMsg.textContent = "약관을 다 읽고 체크해주세요..";
            termBox.classList.add("show");
            // termBox.scrollIntoView({ behavior: "auto" });
            return false;
        }

        warnMsg.textContent = "";
        return true;
    }

    //연락처 체크
    function mb_hp_check() {
        tel_check_by_id({
            ids: ["mb_hp_01", "mb_hp_02", "mb_hp_03"],
            lengths: [3, 4, 4],
            is_only_phone: true,
            check_id: "mb_hp_check",
            confirm_id: "mb_hp_check_confirm",
            checked_mesage: "✔",
            unchecked_message: "연락처를 작성해주세요.",
        })
    }

    //대표번호 체크
    function company_tel_check() {
        tel_check_by_id({
            ids: ["company_tel_01", "company_tel_02", "company_tel_03"],
            lengths: [2, 3, 4],
            is_only_phone: false,
            check_id: "company_tel_check",
            confirm_id: "company_tel_check_confirm",
            checked_mesage: "✔",
            unchecked_message: "대표번호를 작성해주세요.",
        })
    }
    //담당자 번호 체크
    function info_tel_check() {
        tel_check_by_id({
            ids: ["info_tel_01", "info_tel_02", "info_tel_03"],
            lengths: [3, 4, 4],
            is_only_phone: true,
            check_id: "info_tel_check",
            confirm_id: "info_tel_check_confirm",
            checked_mesage: "✔",
            unchecked_message: "담당자 번호를 작성해주세요.",
        })
    }

    //계좌번호 체크
    function bank_account_check() {
        tel_check_by_id({
            ids: ["bank_account_01", "bank_account_02", "bank_account_03"],
            lengths: [2, 2, 2],
            is_only_phone: false,
            check_id: "bank_account_check",
            confirm_id: "bank_account_check_confirm",
            checked_mesage: "✔",
            unchecked_message: "계좌번호를 작성해주세요.",
        })
    }

    //아이디 체크
    function id_check(input) {
        show_duplicate_check_notice({
            input: input,
            check_id: "mb_id_check",
            confirm_id: "mb_id_check_confirm",
            empty_message: "아이디를 입력해주세요. 영문 소문자와 숫자만 입력해주세요."
        })
    }

    //사업자 등록 번호 체크
    function company_no_check(input) {
        show_duplicate_check_notice({
            input: input,
            check_id: "company_no_check",
            confirm_id: "company_no_check_confirm",
            empty_message: "사업자 번호를 입력해주세요."
        })
    }

    //세금계산서 이메일 체크
    function company_tax_email_check(input) {
        show_duplicate_check_notice({
            input: input,
            check_id: "company_tax_email_check",
            confirm_id: "company_tax_email_check_confirm",
            empty_message: "세금계산서 이메일을 입력해주세요.",
        })
    }
    //판매형태 체크
    function company_sales_type_check() {
        checkbox_group_check({
            name: "company_sales_type",
            check_id: "company_sales_type_check",
            confirm_id: "company_sales_type_check_confirm",
            checked_mesage: "✔",
            unchecked_message: "판매형태를 선택해주세요.",
        })
    }
    //카테고리 체크
    function company_category_check() {
        checkbox_group_check({
            name: "company_category",
            check_id: "company_category_check",
            confirm_id: "company_category_check_confirm",
            checked_mesage: "✔",
            unchecked_message: "카테고리를 선택해주세요.",
        })
    }
    $(function() {
        <?= $ipinJs ?>
        <?= $hpcertJs ?>
    });



    function email_addr_change(e) {
        var that = $(e);
        var value = that.val();
        var type = that.data('type');
        console.log(type);
        if (value == 'ect') {
            $('#' + type).val('');
            $('#' + type).show();
            $('#' + type).focus();
        } else {
            $('#' + type).hide();
            $('#' + type).val('');
        }
    }

    jQuery(function($) {
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

        if ($("select[name=mb_email_addr]").val() == "ect") {
            $('#mb_email_ect').show();
        }

        if ($("select[name=company_tax_email_addr]").val() == "ect") {
            $('#company_tax_email_ect').show();
        }

        if ($("select[name=info_email_addr]").val() == "ect") {
            $('#info_email_ect').show();
        }
    });

    function fregisterform_submit(f) {
        event.preventDefault();

        console.log("회원가입 정보 전송");

        const base_url = document.getElementById('base_url').value;
        const type = document.getElementById('fregisterform').type.value;
        const formData = new FormData(f);

        const validations = [
            //공통
            {
                selector: "#mb_id_check_confirm",
                message: "아이디 중복확인을 진행해주세요.",
                target: "all"
            },
            {
                selector: "#pwdDigitConfirm",
                message: "비밀번호를 형식에 맞게 입력해주세요.",
                target: "all"
            },
            {
                selector: "#pwdDupConfirm",
                message: "비밀번호가 같지 않습니다.",
                target: "all"
            },
            {
                selector: "#mb_name_check_confirm",
                message: "상호명을 작성해주세요.",
                target: "all"
            },
            {
                selector: "#mb_addr_check_confirm",
                message: "주소검색을 해주세요.",
                target: "all"
            },
            {
                selector: "#company_no_check_confirm",
                message: "사업자등록번호 중복확인을 진행해주세요.",
                target: "all"
            },
            {
                selector: "#company_cert_file_check_confirm",
                message: "사업자등록증을 첨부해주세요.",
                target: "all"
            },

            //user
            {
                selector: "#reg_mb_email_check_confirm",
                message: "이메일을 작성해주세요.",
                target: "user",
            },
            {
                selector: "#mb_hp_check_confirm",
                message: "연락처를 작성해주세요.",
                target: "user",
            },

            //seller
            {
                selector: "#company_tax_email_check_confirm",
                message: "세금계산서 이메일을 중복확인 해주세요.",
                target: "seller"
            },
            {
                selector: '#company_owner_check_confirm',
                message: '대표자명을 작성해주세요.',
                target: 'seller'
            },
            {
                selector: '#company_item_check_confirm',
                message: '업태를 작성해주세요.',
                target: 'seller'
            },
            {
                selector: '#company_service_check_confirm',
                message: '종목을 작성해주세요.',
                target: 'seller'
            },
            {
                selector: '#company_tel_check_confirm',
                message: '대표번호를 작성해주세요.',
                target: 'seller'
            },
            {
                selector: '#company_hompage_check_confirm',
                message: '홈페이지를 작성해주세요.',
                target: 'seller'
            },
            {
                selector: '#memo_check_confirm',
                message: '회사소개를 작성해주세요.',
                target: 'seller'
            },
            {
                selector: "#company_brand_name_check_confirm",
                message: "브랜드명을 작성해주세요.",
                target: "seller",
            },
            {
                selector: '#company_category_check_confirm',
                message: '카테고리를 체크해주세요.',
                target: 'seller'
            },
            {
                selector: '#company_item_info_check_confirm',
                message: '상품소개를 작성해주세요.',
                target: 'seller'
            },
            {
                selector: '#company_sales_type_check_confirm',
                message: '판매형태를 체크해주세요.',
                target: 'seller'
            },
            {
                selector: '#company_entry_check_confirm',
                message: '입점몰을 작성해주세요.',
                target: 'seller'
            },
            {
                selector: '#bank_name_check_confirm',
                message: '은행명을 작성해주세요.',
                target: 'seller'
            },
            {
                selector: '#bank_account_check_confirm',
                message: '계좌번호를 작성해주세요.',
                target: 'seller'
            },
            {
                selector: '#bank_holder_check_confirm',
                message: '예금주명을 작성해주세요.',
                target: 'seller'
            },
            {
                selector: "#bank_account_file_check_confirm",
                message: "통장사본 파일을 첨부해주세요.",
                target: "seller"
            },
            {
                selector: '#info_name_check_confirm',
                message: '담당자명을 작성해주세요.',
                target: 'seller'
            },
            {
                selector: '#info_tel_check_confirm',
                message: '담당자 번호를 작성해주세요.',
                target: 'seller'
            },
            {
                selector: '#info_email_check_confirm',
                message: '담당자 이메일을 작성해주세요.',
                target: 'seller'
            },
        ];

        for (let item of validations) {
            let {
                selector,
                message,
                target
            } = item;

            if ((target === 'all' || target === type) && $(selector).val() !== 'ok') {
                alert(message);
                return false;
            }
        }

        if (f.w.value === "") {
            const agree1 = document.getElementById('agree1');
            const agree2 = document.getElementById('agree2');

            if (agree1 && !agree1.checked && type === "user") {
                alert("이용약관에 동의해주세요.");
                agree1.focus();
                return false;
            }

            if (agree2 && !agree2.checked) {
                alert("개인정보 수집 및 이용에 동의해주세요.");
                agree2.focus();
                return false;
            }

            if (agree4 && !agree4.checked) {
                alert("배송정책 약관에 동의해주세요.");
                agree4.focus();
                return false;
            }
            if (agree5 && !agree5.checked) {
                alert("결제정책 약관에 동의해주세요.");
                agree5.focus();
                return false;
            }
            if (agree6 && !agree6.checked) {
                alert("정산정책 약관에 동의해주세요.");
                agree6.focus();
                return false;
            }
        }




        // // 이름 검사
        // if (f.w.value == "") {
        //     if (f.mb_name.value.length < script 1) {
        //         alert("이름을 입력하십시오.");
        //         f.mb_name.focus();
        //         return false;
        //     }
        // }

        <?= $cf_cert_useJs ?>

        // E-mail 검사
        // if (type == "user" && ((f.w.value == "") || (f.w.value == "u" && f.mb_email.defaultValue != f.mb_email.value))) {
        // 	var msg = reg_mb_email_check();
        // 	if(msg.trim()) {
        // 		alert(msg);
        // 		f.reg_mb_email.select();
        // 		return false;
        // 	}
        // }

        <?= $regis_use_hpJS ?>

        // if (typeof(f.mb_recommend) != "undefined" && f.mb_recommend.value && f.w.value == "") {
        //     if (f.mb_id.value == f.mb_recommend.value) {
        //         alert("본인을 추천할 수 없습니다.");
        //         f.mb_recommend.focus();
        //         return false;
        //     }

        //     var msg = reg_mb_recommend_check();
        //     if (msg.trim()) {
        //         alert(msg);
        //         f.mb_recommend.select();
        //         return false;
        //     }
        // }

        $.ajax({
            url: base_url + 'm/process/register_form_update',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            async: true,
            enctype: 'multipart/form-data',
            beforeSend: function() {
                $('#ajax_loding').show();
                $('body').addClass('noscroll');
            },
            success: function(res) {
                $('#ajax_loding').hide();
                $('body').removeClass('noscroll');


                let result;
                try {
                    result = JSON.parse(res);
                } catch (e) {
                    console.error("서버 응답이 JSON이 아닙니다:", res);
                    alert("서버에서 잘못된 응답이 왔습니다.");
                    return;
                }
                alert(result['msg'].trim());

                if (result['res']) {
                    if (f.w.value == "") {
                        location.href = base_url + 'm/bbs/register_result';
                    } else {
                        location.reload();
                        // if (result['pwd']) {
                        //     $('input[name=mb_password]').val(result['pwd']);
                        //     $('input[name=is_update]').val(1);
                        // } else {}
                    }
                }
            },
            error: function(a, b, c) {
                console.log("에러1:", a);
                console.log("에러2:", b);
                console.log("에러3:", c);
                $('#ajax_loding').hide();
                $('body').removeClass('noscroll');
                alert('서버에서 오류 발생');
            }
        });
    }
</script>
<!-- } 회원정보 입력/수정 끝 -->