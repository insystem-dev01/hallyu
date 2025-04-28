<script src="<?php echo base_url();?>js/jquery.register_form.js"></script>
<?=$headTitle?>
<form name="fregisterform" id="fregisterform" action="<?=base_url()?>AdminProcess/member_register_form_update" onsubmit="return fregisterform_submit(this);" method="post" autocomplete="off" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="token" value="">
    <h2>사이트 이용정보 입력</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row"><label for="reg_mb_id">아이디</label></th>
                    <td>
                        <input type="text" name="mb_id" id="reg_mb_id"  class="frm_input " maxlength="20" onkeyup="only_eng_num_text_typing(this)" style="text-transform: lowercase;">
                        <span id="msg_mb_id"></span>
                        <button type="button" class="btn_small" onclick="reg_mb_id_check()">중복확인</button>
                        <?php echo $help; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_mb_nickname">닉네임</label></th>
                    <td>
                        <input type="text" name="mb_nickname" id="reg_mb_nickname"  class="frm_input " maxlength="20" onkeyup="only_eng_num_kor_typing(this)" style="text-transform: lowercase;">
                        <span id="msg_mb_id"></span>
                        <span class="frm_info fc_125">한글 6자, 영문 & 숫자 10자 이내</span>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_mb_password">비밀번호</label></th>
                    <td>
                        <input type="password" name="mb_password" id="reg_mb_password"  class="frm_input " maxlength="20">
                        <button type="button" class="btn_small" onclick="pwd_look(this)">비밀번호 확인</button>
                        <span class="frm_info fc_125">숫자, 영문자, 특수문자 8자 이상</span>
                    </td>   
                </tr>
                <tr>
                    <th scope="row"><label for="reg_mb_password_re">비밀번호 확인</label></th>
                    <td>
                        <input type="password" name="mb_password_re" id="reg_mb_password_re"  class="frm_input " maxlength="20">
                        <button type="button" class="btn_small" onclick="pwd_look(this)">비밀번호 확인</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_mb_img">프로필</label></th>
                    <td>
                        <input type="file" name="mb_img" id="reg_mb_img">
                        <span id="msg_mb_img"></span>
                        <span class="frm_info fc_125">400*400 사이 이미지만 업로드 가능합니다. 업로드 하지 않았을 경우 기본 이미지가 등록됩니다.</span>
                    </td>
                </tr>
            </tbody>
    	</table>
    </div>
    <h2 class="mart30">개인정보 입력</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row"><label for="reg_mb_name">이름(실명)</label></th>
                    <td><input type="text" name="mb_name" id="reg_mb_name"  class="frm_input " onkeyup="only_eng_kor_typing(this)"></td>
                </tr>
                <?php if($config['register_use_tel'] > 0) { ?>
                <tr>
                    <th scope="row"><label for="reg_mb_tel">전화번호</label></th>
                    <td><input type="text" name="mb_tel" id="reg_mb_tel" class="frm_input" maxlength="20"></td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row"><label for="reg_mb_hp">연락처</label></th>
                    <td>
                        <input type="text" name="mb_hp" id="reg_mb_hp" class="frm_input" size="20" maxlength="13" onkeyup="phone_typing(this)">
                        <span class="frm_info">
                            <label><input type="checkbox" name="mb_sms" value="Y" checked="checked"> 휴대폰 문자메세지를 받겠습니다.</label>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_mb_email">E-mail</label></th>
                    <td>
                        <input type="text" name="mb_email" id="reg_mb_email"  class="frm_input " size="40" maxlength="100">
                        <span class="frm_info">
                            <label><input type="checkbox" name="mb_mailling" value="Y" id="reg_mb_mailling" checked="checked"> 정보 메일을 받겠습니다.</label>
                        </span>
                    </td>
                </tr>
                <?php if(false) { ?>
                <tr>
                    <th scope="row">본인확인방법</th>
                    <td>
                        <input type="radio" name="mb_certify_case" value="ipin" id="mb_certify_ipin">
                        <label for="mb_certify_ipin">아이핀</label>
                        <input type="radio" name="mb_certify_case" value="hp" id="mb_certify_hp">
                        <label for="mb_certify_hp">휴대폰</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">본인확인</th>
                    <td>
                        <input type="radio" name="mb_certify" value="1" id="mb_certify_yes">
                        <label for="mb_certify_yes">예</label>
                        <input type="radio" name="mb_certify" value="" id="mb_certify_no" checked="checked">
                        <label for="mb_certify_no">아니오</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">성인인증</th>
                    <td>
                        <input type="radio" name="mb_adult" value="1" id="mb_adult_yes">
                        <label for="mb_adult_yes">예</label>
                        <input type="radio" name="mb_adult" value="0" id="mb_adult_no" checked="checked">
                        <label for="mb_adult_no">아니오</label>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row">주소</th>
                    <td>
                        <label for="reg_mb_zip" class="sound_only">우편번호</label>
                        <input type="text" name="mb_zip" id="reg_mb_zip" class="frm_input" size="8" maxlength="5" readonly>
                        <button type="button" class="btn_small" onclick="win_zip('fregisterform', 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">주소검색</button><br>
                        <input type="text" name="mb_addr1" id="reg_mb_addr1" class="frm_input frm_address" size="60" readonly>
                        <label for="reg_mb_addr1">기본주소</label><br>
                        <input type="text" name="mb_addr2" id="reg_mb_addr2" class="frm_input frm_address" size="60" readonly>
                        <label for="reg_mb_addr2">상세주소</label><br>
                        <input type="text" name="mb_addr3" id="reg_mb_addr3" class="frm_input frm_address" size="60" readonly="readonly">
                        <label for="reg_mb_addr3">참고항목</label>
                        <input type="hidden" name="mb_addr_jibeon" value="">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="reg_mb_recommend">추천인</label></th>
                    <td><input type="text" name="mb_recommend" value="admin" id="reg_mb_recommend"  class="frm_input "></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="저장" id="btn_submit" class="btn_large" accesskey="s">
    </div>
</form>
<?=$headTitleEnd?>
<script>
function fregisterform_submit(f){
    event.preventDefault();

    const base_url = document.getElementById('base_url').value;
    let formData = new FormData(f);

	// 회원아이디 검사
	var msg = admin_reg_mb_id_check();
	if(msg && msg.trim()) {
		alert(msg);
		f.mb_id.select();
		return false;
	}

    if(f.mb_nickname.value == "") {
		alert("닉네임을 입력해주세요.");
		f.mb_nickname.focus();
		return false;
	}

    if(f.mb_password.value.length > 7 && /[a-zA-Z]/.test(f.mb_password.value) && /[0-9]/.test(f.mb_password.value) && /[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/.test(f.mb_password.value)) {
    } else {
        alert("비밀번호를 영문자, 숫자, 특수문자를 포함하여 8자 이상 입력해주세요.");
        f.mb_password.focus();
        return false;
    }

	if(f.mb_password.value != f.mb_password_re.value) {
		alert("비밀번호가 같지 않습니다.");
		f.mb_password_re.focus();
		return false;
	}

	// 이름 검사
	if(f.mb_name.value.length < 1) {
		alert("이름을 입력해주세요.");
		f.mb_name.focus();
		return false;
	}

	/*
	var pattern = /([^가-힣\x20])/i;
	if(pattern.test(f.mb_name.value)) {
		alert("이름은 한글로 입력하십시오.");
		f.mb_name.select();
		return false;
	}
	*/

    // 휴대폰 번호
    if(f.mb_hp.value.length < 1) {
		alert("연락처를 입력해주세요.");
		f.mb_hp.focus();
		return false;
	}

	// E-mail 검사
	var mail_msg = reg_mb_email_check();
	if(mail_msg && mail_msg.trim()) {
		alert(mail_msg);
		f.reg_mb_email.select();
		return false;
	}

	if(typeof(f.mb_recommend) != "undefined" && f.mb_recommend.value) {
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

    if(f.mb_zip.value.length < 1 || f.mb_addr1.value.length < 1) {
		alert("주소를 입력해주세요.");
		f.mb_zip.focus();
		return false;
	}

    $.ajax({
        url : base_url+'AdminProcess/member_register_form_update',
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
            location.reload();
        },error:function(a,b,c){
            console.log(a,b,c);
            $('#ajax_loding').hide();
            $('body').removeClass('noscroll');
            alert('서버에서 오류 발생');
        }
    });
}
</script>