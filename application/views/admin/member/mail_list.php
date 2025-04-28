<?=$headTitle?>
<h2>기본검색</h2>
<form name="fsearch" id="fsearch" method="get">
    <input type="hidden" name="code" value="<?php echo $code; ?>">
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w100">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row"><label for="sfl">검색어</label></th>
                    <td>
                        <select name="sfl" id="sfl">
                            <option value="ma_subject">제목</option>
                        </select>
                        <label for="stx" class="sound_only">검색어</label>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" class="frm_input" size="30">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="검색" class="btn_medium">
        <input type="button" value="초기화" id="frmRest" class="btn_medium grey">
    </div>
</form>
<form name="fmaillist" id="fmaillist" method="post" action="<?=base_url()?>AdminProcess/member_mail_list_delete" onsubmit="return fmaillist_submit(this);">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <div class="local_ov mart30">
        총 메일수 : <b class="fc_red"><?php echo number_format($total_count); ?></b>건
    </div>
    <div class="local_frm01">
        <?php echo $btn_frmline; ?>
    </div>
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col class="w50">
                <col class="w60">
                <col>
                <col class="w130">
                <col class="w80">
                <col class="w80">
                <col class="w90">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">
                        <label for="chkall" class="sound_only">전체</label>
                        <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form);">
                    </th>
                    <th scope="col">번호</th>
                    <th scope="col">제목</th>
                    <th scope="col">작성일시</th>
                    <th scope="col">테스트</th>
                    <th scope="col">보내기</th>
                    <th scope="col">관리</th>
                </tr>
            </thead>
            <?=$mailListTag?>
            </tbody>
	    </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="desc02">
			<p>ㆍ<b>테스트</b>는 등록된 최고관리자의 이메일로 테스트 메일을 발송합니다.</p>
			<p class="fc_red">ㆍ주의) 수신자가 동의하지 않은 대량 메일 발송에는 적합하지 않습니다. 수십건 단위로 발송해 주십시오.</p>
		</div>
	</div>
</div>
<script>
function fmaillist_submit(f){
    if(!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?  \n삭제한 자료는 복구가 불가능합니다.")) {
            return false;
        }
    }

    return true;
}
</script>

<?=$headTitleEnd?>