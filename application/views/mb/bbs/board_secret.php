<form name="frm" action="<?php echo base_url(); ?>m/bbs/board_read" method="get" onsubmit="return frm_submit(this);">
    <input type="hidden" name="index_no" value="<?php echo $index_no; ?>">
    <input type="hidden" name="boardid" value="<?php echo $boardid; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">

    <div class="m_bo_pop">
        <h2>비밀번호 확인</h2>
        <p>이글은 비밀글 입니다.<br>글작성시 입력한 비밀번호를 입력해주세요.</p>
        <div class="formbox" style="margin:10px 0 0 0;">
            <input type="password" name="inpasswd" placeholder="비밀번호">
        </div>
        <p class="btn_area" style="margin-top:8px;">
            <input type='submit' value='확인' class="btn_medium">
            <a href="javascript:history.go(-1);" class="btn_medium bx-white">뒤로가기</a>
        </p>
    </div>
</form>
<script>
function frm_submit(f){
	if(!f.inpasswd.value){
		alert('비밀번호를 입력하세요.');
		f.inpasswd.focus();
		return false;
	}

	if(answer==true){
        return true;  
    }else{
        return false; 
    }
}
</script>