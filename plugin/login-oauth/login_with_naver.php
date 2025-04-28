<?php
/*
 * login_with_naver.php
 *
 * @(#) $Id: login_with_facebook.php,v 1.3 2013/07/31 11:48:04 mlemos Exp $
 * @(#) $Id: login_with_naver,v 1.0 2014/12/30 dosa
 *
 */

	/*
	 *  Get the http.php file from http://www.phpclasses.org/httpclient
	 */
	include_once('./_common.php');
	require('http.php');
	require('oauth_client.php');

	$client = new oauth_client_class;
	$client->debug = false;
	$client->debug_http = true;
	$client->server = 'Naver';
	$client->redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].
		dirname(strtok($_SERVER['REQUEST_URI'],'?')).'/login_with_naver.php';

	$client->client_id = $naver_ClientID; $application_line = __LINE__;
	$client->client_secret = $naver_ClientSecret;

	if(strlen($client->client_id) == 0
	|| strlen($client->client_secret) == 0)
		alert_close('네이버 연동키값을 확인해 주세요.');

	if($login=='Y') {
		unset($_SESSION['OAUTH_STATE']);
		$client->ResetAccessToken();
	}

	/* API permissions
	 */
	if(($success = $client->Initialize()))
	{
		if(($success = $client->Process()))
		{
			if(strlen($client->access_token))
			{
				$success = $client->CallAPI(
					'https://apis.naver.com/nidlogin/nid/getUserProfile.xml',
					'POST', array('mode'=>'userinfo'), array('FailOnAccessError'=>true), $user);
			}
		}
		$success = $client->Finalize($success);
	}
	if($client->exit)
		exit;
	if($success)
	{
		$xml = simplexml_load_string($user);
		if($xml->result->resultcode == '00') {
			$client->GetAccessToken($AccessToken);

			$mb_gubun = 'n_';
			$mb_id = $xml->response->id; //동일인 식별정보
			$mb_name = $xml->response->name; //사용자 이름
			$mb_nick = $xml->response->nickname; //사용자 별명
			$mb_email = $xml->response->email; //사용자 메일주소
			$mb_gender = $xml->response->gender; //F:여성, M:남성, U:확인불가
			$mb_age = $xml->response->age; //사용자 연령대
			$mb_birthday = $xml->response->birthday; //사용자 생일(MM-DD 형식)
			$mb_profile_image = $xml->response->profile_image; //사용자 프로필 사진 URL
			$token_value = $AccessToken['value'];
			$token_refresh = $AccessToken['refresh'];
			$token_secret = '';

			//$client->ResetAccessToken();

			include_once('./oauth_check.php');
		} else {
			$error = HtmlSpecialChars($xml->result->resultcode);
			alert_close($error);
		}
	} else {
		$error = HtmlSpecialChars($client->error);
		alert_close($error);
	}
?>