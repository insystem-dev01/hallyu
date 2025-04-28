<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Zip extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }
    public function zip(){
        $js="";
        $js.="<script type=\"text/JavaScript\" src=\"".base_url()."/js/jquery-1.8.3.min.js\"></script>";
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {   //https 통신
            $js.="<script src=\"https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js\"></script>";
        } else {  //http 통신
            $js.="<script src=\"http://dmaps.daum.net/map_js_init/postcode.v2.js\"></script>";
        }
        $js.="<script src=\"".base_url()."/js/zip.js\"></script>";
        $frm_name=$this->input->get('frm_name');
        $frm_zip=$this->input->get('frm_zip');
        $frm_addr1=$this->input->get('frm_addr1');
        $frm_addr2=$this->input->get('frm_addr2');
        $frm_addr3=$this->input->get('frm_addr3');
        $frm_jibeon=$this->input->get('frm_jibeon');
        $data=array("js"=>$js,'frm_name'=>$frm_name,'frm_zip'=>$frm_zip,'frm_addr1'=>$frm_addr1,'frm_addr2'=>$frm_addr2,'frm_addr3'=>$frm_addr3,'frm_jibeon'=>$frm_jibeon);
        $this->load->view('common/zip',$data);
    }
}