<?php
if(!defined('_HALLYUWEB_')) exit; // 개별 페이지 접근 불가

$adminkey = trim($default['de_kakaopay_admin_key']);       // admin 키

$cid = $default['de_kakaopay_cid'];         

$req_auth   = 'Authorization: KakaoAK '.$adminkey;
$req_cont   = 'Content-type: application/x-www-form-urlencoded;charset=utf-8';

$kakao_header = array( $req_auth, $req_cont );
?>