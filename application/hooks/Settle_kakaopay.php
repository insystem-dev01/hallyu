<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Settle_kakaopay  {
    protected $ci;
    function __construct(){
        $this->ci =& get_instance();
    }
    function index(){
        $default=$this->ci->load->get_var('default');
        $is_kakaopay_use = false;
        if(isset($default['de_kakaopay_admin_key']) && isset($default['de_kakaopay_cid'])) {
            $is_kakaopay_use = true;
            $adminkey = trim($default['de_kakaopay_admin_key']);       // admin 키
            $cid = $default['de_kakaopay_cid'];
            $req_auth   = 'Authorization: KakaoAK '.$adminkey;
            $req_cont   = 'Content-type: application/x-www-form-urlencoded;charset=utf-8';
            $kakao_header = array( $req_auth, $req_cont );
        }
        $data=array('is_kakaopay_use'=>$is_kakaopay_use);
        $this->ci->load->vars($data);
    }
}?>