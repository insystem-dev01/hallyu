<h1 class="psms_tit"><?php echo $title; ?></h1>
<div class="psms_wrap">
    <form name="fsend" method="post" onsubmit="return fsend_submit(this)">
    <input type="hidden" name="ph" value="<?php echo $ph; ?>">
        <div class="tbl_frm02">
            <table>
                <colgroup>
                    <col width="75px">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <td colspan="2">
                            <p class="marb7">※ 한글 45자까지 전송가능합니다.</p>
                            <textarea name="sms_content" id="sms_content" class="frm_textbox wfull" onkeyup="byte_check('sms_content', 'byte', 'max_byte');" rows="6"></textarea>
                            <p class="mart7 tar"><span id="byte">0</span> / <span id="max_byte"><?php echo $cf_sms_typeNum; ?></span> byte</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">발신번호</th>
                        <td><?php echo $cf_sms_recalStr; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">수신번호</th>
                        <td><input name="recv_number" type="text" value="<?php echo $ph; ?>" class="frm_input wfull" maxlength="20"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="btn_confirm">
            <input type="submit" class="btn_lsmall" value="메세지보내기">
            <a href="javascript:self.close()" class="btn_lsmall bx-white">닫기</a>
        </div>
    </form>
</div>

<script>
function fsend_submit(f) {
    var base_url=document.getElementById('base_url').value;
	if(f.sms_content.value == '') {
		alert('메세지 내용을 입력하세요.');
		f.sms_content.focus();
		return false;
	}

	f.action = base_url+"AdminProcess/sms_user_update";
    return true;
}

function byte_check(el_message, el_byte, el_max_byte){
    var conts = document.getElementById(el_message);
    var bytes = document.getElementById(el_byte);
    var max_bytes = document.getElementById(el_max_byte);

    var i = 0;
    var cnt = 0;
    var exceed = 0;
    var ch = '';

    for(i=0; i<conts.value.length; i++)
    {
        ch = conts.value.charAt(i);
        if(escape(ch).length > 4) {
            cnt += 2;
        } else {
            cnt += 1;
        }
    }

    bytes.innerHTML = cnt;
    <?=$cf_sms_typeJs?>
}
</script>