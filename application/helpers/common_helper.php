<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
//  등록된 상품이미지 미리보기
function get_look_ahead($it_img, $it_img_del){
	if(!trim($it_img)) return;

	if(preg_match("/^(http[s]?:\/\/)/", $it_img) == true)
		$file_url = $it_img;
	else
		$file_url = base_url()."data/goods/".$it_img;

	$str  = "<a href='{$file_url}' target='_blank' class='btn_small bx-white marr7'>미리보기</a> <label class='marr7'><input type='checkbox' name='{$it_img_del}' value='{$it_img}'>삭제</label>";

	return $str;
}
function get_pagecode($code){
	$value_code	= is_array($code) ? $code : array($code);
	$value_code	= implode(",", $value_code);

	return $value_code;
}
// 쿠폰번호 생성함수
function get_coupon_id($reg_type='1'){
    $len = 16;
	if($reg_type)
		$chars = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789";
	else
		$chars = "1234567890";
    srand((double)microtime()*1000000);
    $i = 0;
    $str = '';
    while($i < $len) {
        $num = rand() % strlen($chars);
        $tmp = substr($chars, $num, 1);
        $str .= $tmp;
        $i++;
    }
    $str = preg_replace("/([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})/", "\\1-\\2-\\3-\\4", $str);
    return $str;
}
// 날짜를 select 박스 형식으로 얻는다
function date_select($date, $name="", $date_y, $date_m, $date_d){
	$s = "";
	if(substr($date, 0, 4) == "0000") {
		#$date = OM_TIME_YMDHIS;
		$date=date("Y-m-d H:i:s", time());
	}
	preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $date, $m);

	// 년
	$s .= "<select name='{$name}_y'>";
	$s .= "<option value='0000'>선택";
	for($i=$m[0]-3; $i<=$m[0]+3; $i++) {
		$s .= "<option value='$i'";
		if($date_y == $i) {
			$s .= " selected";
		}
		$s .= ">$i";
	}
	$s .= "</select>년 \n";

	// 월
	$s .= "<select name='{$name}_m'>";
	$s .= "<option value='00'>선택";
	for($i=1; $i<=12; $i++) {
		$ms = sprintf('%02d',$i);
		$s .= "<option value='$ms'";
		if($date_m == $ms) {
			$s .= " selected";
		}
		$s .= ">$ms";
	}
	$s .= "</select>월 \n";

	// 일
	$s .= "<select name='{$name}_d'>";
	$s .= "<option value='00'>선택";
	for($i=1; $i<=31; $i++) {
		$ds = sprintf('%02d',$i);
		$s .= "<option value='$ds'";
		if($date_d == $ds) {
			$s .= " selected";
		}
		$s .= ">$ds";
	}
	$s .= "</select>일 \n";

	return $s;
}
// 입력 폼 안내문
function help($help="", $addclass='fc_125'){
	$help = str_replace("\n", "<br>", $help);

	if($addclass == 1) {
		$str = '<span class="tooltip"><i class="fa fa-question-circle"></i><span class="tooltiptext">'.$help.'</span></span>';
	} else {
		$str = '<span class="frm_info';
		if($addclass) $str.= ' '.$addclass;
		$str.= '">'.$help.'</span>';
	}

    return $str;
}
// 날짜검색
function get_search_date($fr_date, $to_date, $fr_val, $to_val, $is_last=true){
	$input_end = ' class="frm_input w80" maxlength="10">';
	$js = " onclick=\"search_date('{$fr_date}','{$to_date}',this.value);\"";

	$frm = array();
	$frm[] = '<label for="'.$fr_date.'" class="sound_only">시작일</label>';
	$frm[] = '<input type="text" name="'.$fr_date.'" value="'.$fr_val.'" id="'.$fr_date.'" autocomplete="off" '.$input_end;
	$frm[] = ' ~ ';
	$frm[] = '<label for="'.$to_date.'" class="sound_only">종료일</label>';
	$frm[] = '<input type="text" name="'.$to_date.'" value="'.$to_val.'" id="'.$to_date.'" autocomplete="off" '.$input_end;
	$frm[] = '<span class="btn_group">';
	$frm[] = '<input type="button"'.$js.' class="btn_small white" value="오늘">';
	$frm[] = '<input type="button"'.$js.' class="btn_small white" value="어제">';
	$frm[] = '<input type="button"'.$js.' class="btn_small white" value="일주일">';
	$frm[] = '<input type="button"'.$js.' class="btn_small white" value="지난달">';
	$frm[] = '<input type="button"'.$js.' class="btn_small white" value="1개월">';
	$frm[] = '<input type="button"'.$js.' class="btn_small white" value="3개월">';
	if($is_last) $frm[] = '<input type="button"'.$js.' class="btn_small white" value="전체">';
	$frm[] = '</span>';

	return implode('', $frm);
}
function return_error2json($str, $fld='error'){
	$data = array();
	$data[$fld] = trim($str);

	die(json_encode($data));
}
// alert 메세지 출력
function alert($msg, $move='back', $myname=''){
	if(!$msg) $msg = '올바른 방법으로 이용해 주십시오.';
	switch($move){
		case "back" :
			$url = "history.go(-1);void(1);";
			break;
		case "close" :
			$url = "window.close();";
			break;
		case "reload" :
			$url = "location.reload();";
			break;
		case "parent" :
			$url = "parent.document.location.reload();";
			break;
		case "replace" :
			$url = "opener.document.location.reload();window.close();";
			break;
		case "no" :
			$url = "";
			break;
		case "shash" :
			$url = "location.hash='{$myname}';";
			break;
		case "thash" :
			$url  = "opener.document.location.reload();";
			$url .= "opener.document.location.hash='{$myname}';";
			$url .= "window.close();";
			break;
		default :
			$url = "location.href='{$move}'";
			break;
	}
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">";
	echo "<script type=\"text/javascript\">alert(\"{$msg}\");{$url}</script>";
	exit;
}
?>