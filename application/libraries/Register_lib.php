<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Register_lib {
    protected $ci;
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('Query_model');
        // Do something with $params
    }

    function exist_mb_id($reg_mb_id){
        $reg_mb_id = trim($reg_mb_id);
        if($reg_mb_id == "") return "";
        $sql = "SELECT count(*) AS cnt FROM shop_member WHERE id = ?";
        $data = array($reg_mb_id);
        $row = $this->ci->Query_model->returnOneArr($sql,$data);
        if($row['cnt'])
            return "이미 사용중인 회원 아이디 입니다.";
        else
            return "";
    }

    function exist_mb_email($reg_mb_email, $reg_mb_id){
        $sql = "SELECT count(*) AS cnt FROM shop_member WHERE email = ? AND id <> ?";
        $data = array($reg_mb_email,$reg_mb_id);
        $row = $this->ci->Query_model->returnOneArr($sql,$data);
        if($row['cnt'])
            return "이미 사용중인 E-mail 주소입니다.";
        else
            return "";
    }

    // 사업자번호 중복체크
    function exist_company_number($company_saupja_no) {
        $sql = "SELECT count(*) AS cnt FROM shop_seller WHERE company_saupja_no = ?";
        $param = array($company_saupja_no);
        $row = $this->ci->query_model->returnOneArr($sql, $param);

        return $row['cnt'] >= 1 ? false : true;
    }

    function valid_company_number($company_number) {
        if(!preg_match("/^\d{3}-\d{2}-\d{5}$/", $company_number))
            return false;
        else
            return true;
    }

    function exist_mb_nickname($reg_mb_nickname){
        $reg_mb_nickname = trim($reg_mb_nickname);
        if($reg_mb_nickname == "") return "";
        $sql = "SELECT count(*) AS cnt FROM shop_member WHERE nickname = ?";
        $data = array($reg_mb_nickname);
        $row = $this->ci->Query_model->returnOneArr($sql,$data);
        if($row['cnt'])
            return "이미 사용중인 닉네임 입니다.";
        else
            return "";
    }

    function exist_company_tax_email($reg_mb_email, $reg_mb_id){
        $sql = "SELECT count(*) AS cnt FROM shop_seller WHERE company_tax_email = ? AND mb_id <> ?";
        $data = array($reg_mb_email,$reg_mb_id);
        $row = $this->ci->Query_model->returnOneArr($sql,$data);
        if($row['cnt'])
            return "이미 사용중인 E-mail 주소입니다.";
        else
            return "";
    }
}
?>