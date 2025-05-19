<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
function empty_mb_id($reg_mb_id){
    if(trim($reg_mb_id)=='')
        return "회원아이디를 입력해 주십시오.";
    else
        return "";
}
function valid_mb_id($reg_mb_id){
    if(preg_match("/[^0-9a-z_]+/i", $reg_mb_id))
        return "회원아이디는 영문자, 숫자, _ 만 입력하세요.";
    else
        return "";
}
function valid_mb_password($reg_mb_password){
    $passwordRule = '/^[a-zA-Z0-9!@#$_%^&*]{6,20}$/';
    if(!preg_match($passwordRule, $reg_mb_password))
        return "비밀번호를 영문자, 숫자를 포함하여 6자이상 입력하세요.";
    else
        return "";
}
function count_mb_id($reg_mb_id){
    if(strlen($reg_mb_id) < 6)
        return "회원아이디는 최소 6글자 이상 입력하세요.";
}
function empty_mb_email($reg_mb_email){
    if(!trim($reg_mb_email))
        return "E-mail 주소를 입력해 주십시오.";
    else
        return "";
}
function valid_mb_email($reg_mb_email){
    if(!preg_match("/([0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)\.([0-9a-zA-Z_-]+)/", $reg_mb_email))
        return "E-mail 주소가 형식에 맞지 않습니다.";
    else
        return "";
}
function empty_mb_name($reg_mb_name){
    if(!trim($reg_mb_name))
        return "이름을 입력해 주십시오.";
    else
        return "";
}
function valid_mb_hp($reg_mb_hp){
    $reg_mb_hp = preg_replace("/[^0-9]/", "", $reg_mb_hp);
    if(!$reg_mb_hp)
        return "휴대폰번호를 입력해 주십시오.";
    else {
        if(preg_match("/^01[0-9]{8,9}$/", $reg_mb_hp))
            return "";
        else
            return "휴대폰번호를 올바르게 입력해 주십시오.";
    }
}
function empty_mb_nickname($reg_mb_nickname){
    if(trim($reg_mb_nickname)=='')
        return "닉네임을 입력해 주십시오.";
    else
        return "";
}
?>