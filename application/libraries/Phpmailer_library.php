<?php
class Phpmailer_library
{
    public function __construct()
    {
        log_message('Debug', 'PHPMailer class is loaded.');
    }

    public function load()
    {
        require_once(APPPATH."libraries/phpmailer/PHPMailerAutoload.php");
        $objMail = new PHPMailer;
        return $objMail;
    }
}
?>