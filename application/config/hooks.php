<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/
$base_url=str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

$request_uri = $_SERVER['REQUEST_URI'];
$url = $request_uri;
$obj=explode('/',$url);
$last=count($obj)-1;
$segment=$obj[$last];

$classNa=$segment;
$classNa2=$obj[$last-1];

if(!file_exists(APPPATH."./config/database.php")){
    if($classNa=="install" || $classNa2=='install'){
        return false;
    }
    die('설치를 먼저 해주세요.');
}

$hook['post_controller_constructor'][] = array(
    'class'    => 'Common',
    'function' => 'common',
    'filename' => 'Common.php',
    'filepath' => 'hooks',
);
$hook['post_controller_constructor'][] = array(
    'class'    => 'Db_table_optimize',
    'function' => 'common',
    'filename' => 'Db_table_optimize.php',
    'filepath' => 'hooks',
);
$hook['post_controller_constructor'][] = array(
    'class'    => 'Partner_config',
    'function' => 'common',
    'filename' => 'Partner_config.php',
    'filepath' => 'hooks',
);
$naverUsePage=explode('?',$segment)[0];
if($naverUsePage=='view'||$naverUsePage=='cart') {
    $hook['post_controller_constructor'][] = array(
        'class'    => 'Settle_naverpay',
        'function' => 'index',
        'filename' => 'Settle_naverpay.php',
        'filepath' => 'hooks',
    );
}
if($naverUsePage=='orderform' || $naverUsePage=='orderkakaopay'){
    $hook['post_controller_constructor'][] = array(
        'class'    => 'Settle_kakaopay',
        'function' => 'index',
        'filename' => 'Settle_kakaopay.php',
        'filepath' => 'hooks',
    );
}



