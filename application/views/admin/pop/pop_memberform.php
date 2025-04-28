<form name="fmemberform" id="fmemberform" action="<?=base_url()?>AdminProcess/pop_memberformupdate" method="post" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="mb_id" value="<?php echo $mb_id; ?>">

    <div id="memberform_pop" class="new_win">
        <h1><?php echo $title; ?></h1>
        <section class="new_win_desc marb50">
            <?php echo $mb_pg_anchor; ?>
            <h3 class="anc_tit">기본정보</h3>
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
                            <th scope="row">프로필</th>
                            <td colspan="2">
                                <input type="file" name="mb_img">
                                <?php if(!empty($mb['mb_img'])) { ?>
                                    <img src="<?=base_url()?>data/member/<?=$mb['mb_img']?>" alt="회원 사진" class="member_img">
                                    <input type="checkbox" name="del_img" value="<?=$mb['mb_img']?>"> 삭제
                                    <span class="fc_125">(기본 이미지로 변경됩니다)</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">회원명</th>
                            <td>
                                <input type="text" name="name" value="<?php echo $mb['name']; ?>" required itemname="회원명" class="frm_input required">
                                <?=$intercept_dateStr?>
                            </td>
                            <th scope="row">아이디</th>
                            <td><?php echo $mb['id']; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">비밀번호</th>
                            <td><input type="text" name="passwd" value="" class="frm_input"></td>
                            <th scope="row">추천인아이디</th>
                            <td><input type="text" name="pt_id" value="<?php echo $mb['pt_id']; ?>" itemname="추천인아이디" class="frm_input"></td>
                        </tr>
                        <tr>
                            <th scope="row">E-Mail</th>
                            <td><input type="text" name="email" value="<?php echo $mb['email']; ?>" email itemname="E-Mail" class="frm_input" size="30"></td>
                            <th scope="row">휴대전화</th>
                            <td><input type="text" name="cellphone" value="<?php echo $mb['cellphone']; ?>" class="frm_input" onkeyup="phone_typing(this)"></td>
                        </tr>
                        <tr>
                            <th scope="row">주소</th>
                            <td colspan="3">
                                <input type="text" name="zip" value="<?php echo $mb['zip']; ?>" class="frm_input" size="8" maxlength="5">
                                <a href="javascript:win_zip('fmemberform', 'zip', 'addr1', 'addr2', 'addr3', 'addr_jibeon');" class="btn_small grey">주소검색</a>
                                <p class="mart5"><input type="text" name="addr1" value="<?php echo $mb['addr1']; ?>" class="frm_input" size="60"> 기본주소</p>
                                <p class="mart5"><input type="text" name="addr2" value="<?php echo $mb['addr2']; ?>" class="frm_input" size="60"> 상세주소</p>
                                <p class="mart5"><input type="text" name="addr3" value="<?php echo $mb['addr3']; ?>" class="frm_input" size="60"> 참고항목
                                <input type="hidden" name="addr_jibeon" value="<?php echo $mb['addr_jibeon']; ?>"></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">본인확인방법</th>
                            <td>
                                <input type="radio" name="mb_certify_case" value="ipin" id="mb_certify_ipin" <?=$mb_certify_ipin?>>
                                <label for="mb_certify_ipin">아이핀</label>
                                <input type="radio" name="mb_certify_case" value="hp" id="mb_certify_hp" <?=$mb_certify_hp?>>
                                <label for="mb_certify_hp">휴대폰</label>
                            </td>
                            <?php if(false) { ?>
                            <th scope="row">성별</th>
                            <td>
                                <input type="radio" name="gender" value="M" id="gender1"<?php echo $mb_genderArr['m']; ?>>
                                <label for="gender1">남자</label>
                                <input type="radio" name="gender" value="F" id="gender2"<?php echo $mb_genderArr['f']; ?>>
                                <label for="gender2">여자</label>
                            </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th scope="row">본인확인</th>
                            <td>
                                <input type="radio" name="mb_certify" value="1" id="mb_certify_yes" <?php echo $mb_certify_yes; ?>>
                                <label for="mb_certify_yes">예</label>
                                <input type="radio" name="mb_certify" value="" id="mb_certify_no" <?php echo $mb_certify_no; ?>>
                                <label for="mb_certify_no">아니오</label>
                            </td>
                            <th scope="row">성인인증</th>
                            <td>
                                <input type="radio" name="mb_adult" value="1" id="mb_adult_yes" <?php echo $mb_adult_yes; ?>>
                                <label for="mb_adult_yes">예</label>
                                <input type="radio" name="mb_adult" value="0" id="mb_adult_no" <?php echo $mb_adult_no; ?>>
                                <label for="mb_adult_no">아니오</label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">레벨</th>
                            <td>
                                <?php echo $mb_gradeStr; ?>
                            </td>
                            <th scope="row">포인트</th>
                            <td>
                                <b><?php echo number_format($mb['point']); ?></b> Point
                                <a href="<?php echo base_url(); ?>admin/member_point_req?mb_id=<?php echo $mb_id; ?>" onclick="win_open(this,'pop_point_req','600','500','yes');return false;" class="btn_small grey marl10">강제적립</a>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">예치금</th>
                            <td>
                                <b><?php echo number_format($mb['mileage']); ?></b> 원
                                <a href="<?php echo base_url(); ?>admin/member_mileage_req?mb_id=<?php echo $mb_id; ?>" onclick="win_open(this,'pop_mileage_req','600','500','yes');return false;" class="btn_small grey marl10">강제적립</a>
                            </td>
                        </tr>
                        <tr class="mb_adm_fld">
                            <th scope="row">부운영자 접근허용</th>
                            <td colspan="3">
                                <div class="sub_frm02">
                                    <table>
                                        <tr>
                                            <?=$sub_authStr?>
                                        </tr>
                                        <tr>
                                            <?=$sub_authStr2?>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr class="pt_pay_fld">
                            <th scope="row" class="th_bg fc_00f">PC 쇼핑몰스킨</th>
                            <td>
                                <?php echo $themePc; ?>
                            </td>
                            <th scope="row" class="th_bg fc_00f">모바일 쇼핑몰스킨</th>
                            <td>
                                <?php echo $themeMb; ?>
                            </td>
                        </tr>
                        <tr class="pt_pay_fld">
                            <th scope="row" class="th_bg fc_00f">추가 판매수수료</th>
                            <td>
                                <input type="text" name="payment" value="<?php echo $mb['payment']; ?>" class="frm_input" size="10">
                                <select name="payflag">
                                    <?php echo $payflagArr[0]; ?>
                                    <?php echo $payflagArr[1]; ?>
                                </select>
                                <?php echo $helpArr[0]; ?>
                            </td>
                            <th scope="row" class="th_bg fc_00f">개별 도메인</th>
                            <td>
                                <span class="sitecode">www.</span><input type="text" name="homepage" value="<?php echo $mb['homepage']; ?>" class="frm_input" placeholder="Ex) sample.com">
                                <?php echo $helpArr[1]; ?>
                            </td>
                        </tr>
                        <tr class="pt_pay_fld">
                            <th scope="row" class="th_bg fc_00f">입금계좌</th>
                            <td colspan="3">
                                <input type="text" name="bank_name" value="<?php echo $pt['bank_name']; ?>" class="frm_input" placeholder="은행명">
                                <input type="text" name="bank_account" value="<?php echo $pt['bank_account']; ?>" class="frm_input" placeholder="계좌번호" size="30">
                                <input type="text" name="bank_holder" value="<?php echo $pt['bank_holder']; ?>" class="frm_input" placeholder="예금주명">
                                <?php echo $helpArr[2]; ?>
                            </td>
                        </tr>
                        <tr class="pt_pay_fld">
                            <th scope="row" class="th_bg fc_00f">본사지정 권한</th>
                            <td colspan="3">
                                <input type="checkbox" name="use_pg" value="1" id="use_pg"<?php echo $mb_use_pg; ?>> <label for="use_pg">개별 PG결제 허용</label>
                                <input type="checkbox" name="use_good" value="1" id="use_good"<?php echo $mb_use_good; ?>> <label for="use_good">개별 상품판매 허용</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h3 class="anc_tit mart30">기타정보</h3>
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
                            <th scope="row">메일수신</th>
                            <td>
                                <input type="radio" name="mailser" value="Y" id="mb_mailling_yes"<?php echo $mailser['y']; ?>>
                                <label for="mb_mailling_yes">예</label>
                                <input type="radio" name="mailser" value="N" id="mb_mailling_no"<?php echo $mailser['n']; ?>>
                                <label for="mb_mailling_no">아니오</label>
                            </td>
                            <th scope="row">문자수신</th>
                            <td>
                                <input type="radio" name="smsser" value="Y" id="mb_sms_yes"<?php echo $smsser['y']; ?>>
                                <label for="mb_sms_yes">예</label>
                                <input type="radio" name="smsser" value="N" id="mb_sms_no"<?php echo $smsser['n']; ?>>
                                <label for="mb_sms_no">아니오</label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">회원가입일</th>
                            <td><?php echo $mb['reg_time']; ?></td>
                            <th scope="row">최근아이피</th>
                            <td><?php echo $mb['login_ip']; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">로그인횟수</th>
                            <td><?php echo number_format($mb['login_sum']); ?> 회</td>
                            <th scope="row">최근접속일</th>
                            <td><?php echo $recentLogin; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">구매횟수</th>
                            <td><?php echo $buyCnt; ?> 회</td>
                            <th scope="row">총구매금액</th>
                            <td><?php echo $totalCnt; ?> 원</td>
                        </tr>
                        <tr>
                            <th scope="row">접근차단일자</th>
                            <td>
                                <input type="text" name="intercept_date" value="<?php echo $mb['intercept_date']; ?>" id="intercept_date" class="frm_input" size="10" maxlength="8">
                                <input type="checkbox" value="<?php echo date("Ymd"); ?>" id="mb_intercept_date_set_today" onclick="if(this.form.intercept_date.value==this.form.intercept_date.defaultValue) { this.form.intercept_date.value=this.value; } else { this.form.intercept_date.value=this.form.intercept_date.defaultValue; }">
                                <label for="mb_intercept_date_set_today">접근차단일을 오늘로 지정</label>
                            </td>
                            <th scope="row">IP</th>
                            <td><?php echo $mb['mb_ip']; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">관리자메모</th>
                            <td colspan="3"><textarea name="memo" class="frm_textbox" rows="3"><?php echo $mb['memo']; ?></textarea></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="btn_confirm">
                <input type="submit" value="저장" class="btn_medium" accesskey="s">
                <button type="button" class="btn_medium bx-red" onclick="member_leave();">탈퇴</button>
                <button type="button" class="btn_medium bx-white" onclick="window.close();">닫기</button>
            </div>
        </section>
    </div>
</form>

<script>
function member_leave() {
    var base_url=document.getElementById('base_url').value;
    if(confirm("영구 탈퇴처리 하시겠습니까?\n한번 삭제된 데이터는 복구 불가능합니다.")) {
        var token = get_ajax_token();
        if(!token) {
            alert("토큰 정보가 올바르지 않습니다.");
            return false;
        }
        location.href = base_url+"AdminProcess/member_delete?mb_id=<?php echo $mb_id; ?>&token="+token;
        return true;
    } else {
        return false;
    }
}

$(function() {
    $(".pt_pay_fld").hide();
	$(".mb_adm_fld").hide();
	<?=$is_partnerJs?>
	<?=$mb_gradeOne?>
	$("#mb_grade").on("change", function() {
		$(".pt_pay_fld:visible").hide();
		$(".mb_adm_fld:visible").hide();
        var level = $(this).val();
		if(level >= 2 && level <= 6) {
			$(".pt_pay_fld").show();
		} else if(level == 1) {
			$(".mb_adm_fld").show();
		}
    });
});
</script>