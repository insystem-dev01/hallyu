<?=$headTitle?>
<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/sns_update">
    <input type="hidden" name="token" value="">
    <h2>소셜네트워크서비스(SNS : Social Network Service)</h2>
    <div class="tbl_frm01">
        <table>
        <colgroup>
            <col class="w180">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">소셜네트워크 로그인</th>
            <td><label><input type="checkbox" name="de_sns_login_use" value="1"<?php echo $default['de_sns_login_use']?' checked':''; ?>> 사용함</label></td>
        </tr>
        <tr>
            <th scope="row">네이버 Client ID</th>
            <td>
                <input type="text" name="de_naver_appid" value="<?php echo $default['de_naver_appid']; ?>" class="frm_input" size="50">
                <a href="https://developers.naver.com/products/login/api/" target="_blank" class="btn_small grey">앱 등록하기</a>
                <?php echo $help['app']; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">네이버 Client Secret</th>
            <td><input type="text" name="de_naver_secret" value="<?php echo $default['de_naver_secret']; ?>" class="frm_input" size="50"></td>
        </tr>
        <tr>
            <th scope="row">페이스북 앱 ID</th>
            <td>
                <input type="text" name="de_facebook_appid" value="<?php echo $default['de_facebook_appid']; ?>" class="frm_input" size="50">
                <a href="https://developers.facebook.com" target="_blank" class="btn_small grey">앱 등록하기</a>
            </td>
        </tr>
        <tr>
            <th scope="row">페이스북 앱 Secret</th>
            <td><input type="text" name="de_facebook_secret" value="<?php echo $default['de_facebook_secret']; ?>" class="frm_input" size="50"></td>
        </tr>
        <tr>
            <th scope="row">구글 앱 ID</th>
            <td>
                <input type="text" name="de_google_appid" value="<?php echo $default['de_google_appid']; ?>" class="frm_input" size="50">
            </td>
        </tr>
        <tr>
            <th scope="row">구글 앱 Secret</th>
            <td><input type="text" name="de_google_secret" value="<?php echo $default['de_google_secret']; ?>" class="frm_input" size="50"></td>
        </tr>
        <tr>
            <th scope="row">카카오 REST API Key</th>
            <td>
                <input type="text" name="de_kakao_rest_apikey" value="<?php echo $default['de_kakao_rest_apikey']; ?>" class="frm_input" size="50">
                <a href="https://developers.kakao.com/" target="_blank" class="btn_small grey">앱 등록하기</a>
                <?php echo $help['kakao']; ?>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <h2>goo.gl 짧은주소 만들기</h2>
    <div class="tbl_frm01">
        <table>
        <colgroup>
            <col class="w180">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">구글 짧은주소 API Key</th>
            <td>
                <input type="text" name="de_googl_shorturl_apikey" value="<?php echo $default['de_googl_shorturl_apikey']; ?>" class="frm_input" size="50">
                <a href="http://code.google.com/apis/console/" target="_blank" class="btn_small grey">API Key 발급받기</a>
                <?php echo $help['short']; ?>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <h2>인스타그램 연동</h2>
    <div class="local_cmd01">
        <p>※ 인스타그램 설정 완료시 쇼핑몰 하단부분에 노출됩니다.</p>
    </div>
    <div class="tbl_frm01">
        <table>
        <colgroup>
            <col class="w180">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">INSTAGRAM URL</th>
            <td>
                <span>https://www.instagram.com/</span>
                <input type="text" name="de_insta_url" value="<?php echo $default['de_insta_url']; ?>" class="frm_input">
            </td>
        </tr>
        <tr>
            <th scope="row">CLIENT ID</th>
            <td>
                <input type="text" name="de_insta_client_id" value="<?php echo $default['de_insta_client_id']; ?>" class="frm_input" size="50">
                <a href="https://www.instagram.com/developer/register" target="_blank" class="btn_small grey">INSTAGRAM 시작</a>
            </td>
        </tr>
        <tr>
            <th scope="row">Valid redirect URIs</th>
            <td><input type="text" name="de_insta_redirect_uri" value="<?php echo $default['de_insta_redirect_uri']; ?>" class="frm_input" size="50"></td>
        </tr>
        <tr>
            <th scope="row">ACCESS_TOKEN</th>
            <td>
                <input type="text" name="de_insta_access_token" value="<?php echo $default['de_insta_access_token']; ?>" class="frm_input" size="50">
                <a href="javascript:createAccessToken();" class="btn_small grey">ACCESS_TOKEN 생성하기</a>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <h2>SNS URL 설정</h2>
    <div class="local_cmd01">
        <p>※ SNS URL 설정 완료시 쇼핑몰 하단부분에 아이콘이 노출됩니다.</p>
    </div>
    <div class="tbl_frm01">
        <table class="tablef">
        <colgroup>
            <col class="w180">
            <col>
            <col class="w180">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">FACEBOOK</th>
            <td><input type="text" name="de_sns_facebook" value="<?php echo $default['de_sns_facebook']; ?>" class="frm_input wfull"></td>
            <th scope="row">TWITTER</th>
            <td><input type="text" name="de_sns_twitter" value="<?php echo $default['de_sns_twitter']; ?>" class="frm_input wfull"></td>
        </tr>
        <tr>
            <th scope="row">INSTAGRAM</th>
            <td><input type="text" name="de_sns_instagram" value="<?php echo $default['de_sns_instagram']; ?>" class="frm_input wfull"></td>
            <th scope="row">PINTEREST</th>
            <td><input type="text" name="de_sns_pinterest" value="<?php echo $default['de_sns_pinterest']; ?>" class="frm_input wfull"></td>
        </tr>
        <tr>
            <th scope="row">NAVER BLOG</th>
            <td><input type="text" name="de_sns_naverblog" value="<?php echo $default['de_sns_naverblog']; ?>" class="frm_input wfull"></td>
            <th scope="row">NAVER BAND</th>
            <td><input type="text" name="de_sns_naverband" value="<?php echo $default['de_sns_naverband']; ?>" class="frm_input wfull"></td>
        </tr>
        <tr>
            <th scope="row">KAKAOTALK</th>
            <td><input type="text" name="de_sns_kakaotalk" value="<?php echo $default['de_sns_kakaotalk']; ?>" class="frm_input wfull"></td>
            <th scope="row">KAKAOSTORY</th>
            <td><input type="text" name="de_sns_kakaostory" value="<?php echo $default['de_sns_kakaostory']; ?>" class="frm_input wfull"></td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>
</form>

<script>
function createAccessToken() {
	var client_id = $('input[name=de_insta_client_id]');
	var redirect_uri = $('input[name=de_insta_redirect_uri]');

	if(client_id.val() == "") {
		alert("CLIENT ID 값을 입력해 주세요.");
		client_id.focus();
		return false;
	}
	if(redirect_uri.val() == "") {
		alert("Valid redirect URIs 값을 입력해 주세요.");
		redirect_uri.focus();
		return false;
	}
	var url = "https://www.instagram.com/oauth/authorize/?client_id=";
	url += client_id.val();
	url += "&redirect_uri=";
	url += redirect_uri.val();
	url += "&response_type=token";

	window.open(url, '_blank');
}
</script>

<?=$headTitleEnd?>