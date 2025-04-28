<?php
/*
 * login_with_twitter.php
 *
 * @(#) $Id: login_with_twitter.php,v 1.8 2014/11/14 10:37:51 mlemos Exp $
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
	$client->server = 'Twitter';
	$client->redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].
		dirname(strtok($_SERVER['REQUEST_URI'],'?')).'/login_with_twitter.php';

	/*
	 * Uncomment the next line if you want to use
	 * the pin based authorization flow
	 */
	// $client->redirect_uri = 'oob';

	/*
	 * Was this script included defining the pin the
	 * user entered to authorize the API access?
	 */
	if(defined('OAUTH_PIN'))
		$client->pin = OAUTH_PIN;

	$client->client_id = $twitter_APIkey; $application_line = __LINE__;
	$client->client_secret = $twitter_APIsecret;

	if(strlen($client->client_id) == 0
	|| strlen($client->client_secret) == 0)
		alert_close('트위트 연동키값을 확인해 주세요.');
		/*
		die('Please go to Twitter Apps page https://dev.twitter.com/apps/new , '.
			'create an application, and in the line '.$application_line.
			' set the client_id to Consumer key and client_secret with Consumer secret. '.
			'The Callback URL must be '.$client->redirect_uri.' If you want to post to '.
			'the user timeline, make sure the application you create has write permissions');
		*/
	if(($success = $client->Initialize()))
	{
		if(($success = $client->Process()))
		{
			if(strlen($client->access_token))
			{
				$success = $client->CallAPI(
					'https://api.twitter.com/1.1/account/verify_credentials.json', 
					'GET', array(), array('FailOnAccessError'=>true), $user);

/*
				$values = array(
					'status'=>str_repeat('x', 140)
				);
				$success = $client->CallAPI(
					'https://api.twitter.com/1.1/statuses/update.json', 
					'POST', $values, array('FailOnAccessError'=>true), $update);
				if(!$success)
					error_log(print_r($update->errors[0]->code, 1));
*/

/* Tweet with an attached image
				$success = $client->CallAPI(
					"https://api.twitter.com/1.1/statuses/update_with_media.json",
					'POST', array(
						'status'=>'This is a test tweet to evaluate the PHP OAuth API support to upload image files sent at '.strftime("%Y-%m-%d %H:%M:%S"),
						'media[]'=>'php-oauth.png'
					),array(
						'FailOnAccessError'=>true,
						'Files'=>array(
							'media[]'=>array(
							)
						)
					), $upload);
*/
			}
		}
		$success = $client->Finalize($success);
	}
	if($client->exit)
		exit;
	if($success)
	{
		$client->GetAccessToken($AccessToken);

		$mb_gubun = 't_';
		$mb_id = $user->id;
		$mb_name = $user->name;
		$mb_nick = $user->name;
		$mb_email = $user->email;
		$token_value = $AccessToken['value'];
		$token_secret = $AccessToken['secret'];
		$token_refresh = '';

		//$client->ResetAccessToken();

		include_once('./oauth_check.php');
	} else {
		$error = HtmlSpecialChars($client->error);
		alert_close($error);
	}
?>
