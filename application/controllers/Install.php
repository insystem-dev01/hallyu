<?php defined('BASEPATH') or exit('No direct script access allowed');

class Install extends CI_Controller
{
    public function __construct(){
        parent::__construct();
    }

    // db 파일 체크
    public function check_database(){
        if (file_exists(APPPATH."./config/database.php")) { #임시로 파일 설치후 생성할 파일
            header("Content-Type: text/html; charset=utf-8");
            die("설치하실 수 없습니다.");
        }
    }

    public function index(){
        // 파일이 존재한다면 설치할 수 없다.
        $this->check_database();

        // 루트 디렉토리에 파일 생성 가능한지 검사.
        if (!is_writeable(APPPATH)) {
            header("Content-Type: text/html; charset=utf-8");
            die("루트 디렉토리의 퍼미션을 707로 변경하여 주십시오.");
        }

        // data 디렉토리에 파일 생성 가능한지 검사.
        if (!is_writeable(APPPATH."config")) {
            header("Content-Type: text/html; charset=utf-8");
            die("data 디렉토리 및 하위 디렉토리모두 퍼미션을 707로 변경하여 주십시오.");
        }

        $this->load->view('install/index');
    }

    function install_config(){
        // 파일이 존재한다면 설치할 수 없다.
        $this->check_database();

        $gmnow = gmdate("D, d M Y H:i:s") . " GMT";
        header("Expires: 0"); // rfc2616 - Section 14.21
        header("Last-Modified: " . $gmnow);
        header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
        header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
        header("Pragma: no-cache"); // HTTP/1.0

        $agree = $this->input->post('agree');
        
        if ($agree != "동의함") {
            die("라이센스(License) 내용에 동의하셔야 설치를 계속하실 수 있습니다.");
        }

        $this->load->view('install/install_config');
    }

    function install_db(){
        // 파일이 존재한다면 설치할 수 없다.
        $this->check_database();

        $gmnow = gmdate("D, d M Y H:i:s") . " GMT";
        header("Expires: 0"); // rfc2616 - Section 14.21
        header("Last-Modified: " . $gmnow);
        header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
        header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
        header("Pragma: no-cache"); // HTTP/1.0

        $mysql_host  = $this->input->post('mysql_host');
        $mysql_user  = $this->input->post('mysql_user');
        $mysql_pass  = $this->input->post('mysql_pass');
        $mysql_db    = $this->input->post('mysql_db');
        $admin_id    = $this->input->post('admin_id');
        $admin_pass  = $this->input->post('admin_pass');
        $admin_name  = $this->input->post('admin_name');
        $admin_email = $this->input->post('admin_email');

        $dblink = @mysqli_connect($mysql_host, $mysql_user, $mysql_pass);
        if (!$dblink) {
            die("MySQL Host, User, Password 를 확인해 주십시오.");
        }

        
        $select_db = @mysqli_select_db($dblink, $mysql_db);
        if ($select_db){
            die("MySQL 이미 존재한 DB명입니다.");
        }
        #데이터베이스 생성
        $sql="create schema {$mysql_db}";
        mysqli_query($dblink, $sql) or die(mysqli_error($dblink));        
        @mysqli_query($dblink, "SET SESSION sql_mode = ''");

        @mysqli_select_db($dblink, $mysql_db);

        $data = array(
            "mysql_host" => $mysql_host,
            "mysql_user" => $mysql_user,
            "mysql_pass" => $mysql_pass,
            "mysql_db" => $mysql_db,
            "admin_id" => $admin_id,
            "admin_pass" => $admin_pass,
            "admin_name" => $admin_name,
            "admin_email" => $admin_email,
            "dblink" => $dblink
        );

        $this->load->view('install/install_db', $data);
    }
}
