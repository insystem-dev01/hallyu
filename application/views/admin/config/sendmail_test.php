<?=$headTitle?>
<div class="local_desc02 local_desc">
	<p>사이트에서 메일을 발송했음에도 불구하고 메일을 수신 하지 못하는경우</p>
	<p>프로그램상의 문제이기 보다는 대부분 메일서버 혹은 받는 메일서버 중 문제가 발생했을 가능성이 있습니다.</p>
	<p>또는 메일이 정상적으로 발송이 되었으나 스팸처리로 이동되어 있을수도 있습니다.</p>
	<p class="mart10 fc_red">따라서 보다 정확한 테스트를 원하신다면 여러 곳으로 테스트 메일을 발송하시기 바랍니다.</p>
	<p>메일이 정상적으로 발송된 경우 아래내용과 같이 수신됩니다.</p>
	<p>-------------------------------------------------------------------------------</p>
	<p>[메일검사] 내용</p>
	<p>이 내용이 제대로 보인다면 보내는 메일 서버에는 이상이 없는것입니다.</p>
	<p><?php echo $OM_TIME_YMDHIS; ?></p>
	<p>이 메일 주소로는 회신되지 않습니다.</p>
	<p>-------------------------------------------------------------------------------</p>        
</div>

<form name="fsendmailtest" method="post">
    <div class="tbl_frm01">
        <table>
        <colgroup>
            <col class="w180">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="email">받는 메일주소</label></th>
            <td>				
                <input type="text" name="email" value="<?php echo $member['email']; ?>" id="email" required class="required frm_input" size="60">
                <?php echo $helpTag; ?>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
    <?php echo $frm_msg; ?>
    <div class="btn_confirm">
        <input type="submit" value="발송" class="btn_large" accesskey="s">
    </div>
</form>

<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="hd">ㆍE-mail 수신이 안되시나요?</div>
		<div class="desc01">
			<p>ㆍ메일수신이 안되는경우 프로그램상에서 처리해드릴 수 있는 부분이 없습니다.</p>
			<p class="fc_red">ㆍ호스팅회사나 서버관리자에게 메일발송테스트 한 결과를 설명하고 해결요청을 하시기 바랍니다.</p>
			<p class="mart13">ㆍ호스팅회사나 서버관리자가 화이트 도메인 등록이 필요하다고 요청한경우 <strong>한국정보보호진흥원 KISA</strong> <a href="http://www.kisarbl.or.kr" target="_blank">(http://www.kisarbl.or.kr)</a> 에서 등록합니다.</p>
			<p>ㆍ등록후 SPF Record 레코드값을 호스팅 회사나 서버관리자에게 전달하고 적용을 요청합니다.</p>
		</div>
	 </div>
</div>

<?=$headTitleEnd?>