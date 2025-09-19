<?php
#if(!defined('_HALLYUWEB_')) exit;
include_once('./plugin/PHPMailer/PHPMailerAutoload.php');

// 메일 보내기 (파일 여러개 첨부 가능)
// type : text=0, html=1, text+html=2
function mailer($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc=""){
    if($type != 1)
        $content = nl2br($content);

    $mail = new PHPMailer(); // defaults to using php "mail()"
    $OM_SMTP='127.0.0.1';
    $OM_SMTP_PORT='25';
    if($OM_SMTP) {
        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->Host = $OM_SMTP; // SMTP server
        if(defined('OM_SMTP_PORT') && $OM_SMTP_PORT)
            $mail->Port = $OM_SMTP_PORT;
    }
    $mail->CharSet = 'UTF-8';
    $mail->From = $fmail;
    $mail->FromName = $fname;
    $mail->Subject = $subject;
    $mail->AltBody = ""; // optional, comment out and test
    $mail->msgHTML($content);
    $mail->addAddress($to);
    if($cc)
        $mail->addCC($cc);
    if($bcc)
        $mail->addBCC($bcc);
    //print_r2($file); exit;
    if($file != "") {
        foreach ($file as $f) {
            $mail->addAttachment($f['path'], $f['name']);
        }
    }
    return $mail->send();
}

// 파일을 첨부함
function attach_file($filename, $tmp_name)
{
    // 서버에 업로드 되는 파일은 확장자를 주지 않는다. (보안 취약점)
    $dest_file = './data/tmp/'.str_replace('/', '_', $tmp_name);
    move_uploaded_file($tmp_name, $dest_file);
    $tmpfile = array("name" => $filename, "path" => $dest_file);
    return $tmpfile;
}
?>