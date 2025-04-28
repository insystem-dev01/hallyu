<?=$headTitle?>

<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/ipaccess_update">
    <input type="hidden" name="token" value="">
    <h2>접속 제한 / 차단 IP 설정</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">현재 접속 IP</th>
                    <td><strong><?php echo $_SERVER['REMOTE_ADDR']; ?></strong></td>
                </tr>
                <tr>
                    <th scope="row"><label for="possible_ip">접근 가능 IP</label></th>
                    <td>
                        <textarea name="possible_ip" id="possible_ip" class="frm_textbox wfull" rows="5"><?php echo $config['possible_ip']; ?></textarea>
                        <?php echo $help['possibleIp']; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="intercept_ip">접근 차단 IP</label></th>
                    <td>
                        <textarea name="intercept_ip" id="intercept_ip" class="frm_textbox wfull" rows="5"><?php echo $config['intercept_ip']; ?></textarea>
                        <?php echo $help['interceptIp']; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>
</form>

<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="desc02">
			<p>ㆍIP등록시 <em>123.+</em> 를 입력하셨다면 첫번째 항목에 해당하는 IP 주소는 접근 및 차단 됩니다. 
			예) <em>123</em>.456.789.225</p>
			<p>ㆍ테스트를 위해 접근 차단 IP 입력란에 최고관리자 IP주소를 입력하시면 절대 안됩니다.</p>
			<p>ㆍ유동적으로 변경되는 IP를 등록시 접속이 제한되실 수 있으니 참고하시기 바랍니다.</p>
		</div>
	 </div>
</div>

<?=$headTitleEnd?>