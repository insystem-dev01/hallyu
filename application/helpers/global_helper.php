<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
// 변수 또는 배열의 이름과 값을 얻어냄. print_r() 함수의 변형
function print_r2($var){
    ob_start();
    print_r($var);
    $str = ob_get_contents();
    ob_end_clean();
    $str = str_replace(" ", "&nbsp;", $str);
    echo nl2br("<span style='font-family:Tahoma, 굴림; font-size:9pt;'>$str</span>");
}
// 전화번호 정규식 0112223333을 011-222-3333 으로 변환
function replace_tel($obj)
{
	if(!$obj) return;

	$obj = preg_replace('/[^\d\n]+/', '', $obj);

	if(substr($obj,0,1) != "0" && strlen ($obj ) > 8) $obj = "0".$obj ;
		$telnum3 = substr( $obj, -4 );

	if(in_array(substr($obj, 0, 3), array('013','050','030')))
		$telnum1 = substr($obj, 0, 4);
	else if(substr($obj, 0, 2) == "01")
		$telnum1 = substr($obj, 0, 3);
	else if(substr($obj, 0, 2) == "02")
		$telnum1 = substr($obj, 0, 2);
	else if(substr($obj, 0, 1) == "0" )
		$telnum1 = substr($obj, 0, 3);

	$telnum2 = substr($obj, strlen($telnum1), -4);
	if(!$telnum1) return $telnum2 . "-" . $telnum3 ;
	else return $telnum1 . "-" . $telnum2 . "-" . $telnum3 ;
}
// unescape nl 얻기
function conv_unescape_nl($str){
    $search = array('\\r', '\r', '\\n', '\n');
    $replace = array('', '', "\n", "\n");

    return str_replace($search, $replace, $str);
}
// 에디터 이미지 얻기
function get_editor_image($contents, $view=true){
    if(!$contents)
        return false;

    // $contents 중 img 태그 추출
    if($view)
        $pattern = "/<img([^>]*)>/iS";
    else
        $pattern = "/<img[^>]*src=[\'\"]?([^>\'\"]+[^>\'\"]+)[\'\"]?[^>]*>/i";
    preg_match_all($pattern, $contents, $matchs);

    return $matchs;
}
// 게시판 첨부파일 썸네일 삭제
function delete_board_thumbnail($boardid, $file){
    if(!$boardid || !$file)
        return;

    $fn = preg_replace("/\.[^\.]+$/i", "", basename($file));
    $files = glob('./data/board/'.$boardid.'/thumb-'.$fn.'*');
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
}
// 리뷰 사진 삭제
function delete_review_thumbnail($file){
    if(!$file)
        return;

    $fn = preg_replace("/\.[^\.]+$/i", "", basename($file));
    $files = glob("./data/review/$fn");
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
}
// 회원 사진 삭제
function delete_member_thumbnail($file){
    if(!$file)
        return;

    $fn = preg_replace("/\.[^\.]+$/i", "", basename($file));
    $files = glob("./data/member/$fn");
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
}
// 관리자 등급 사진 삭제
function delete_grade_thumbnail($file){
    if(!$file)
        return;

    $fn = preg_replace("/\.[^\.]+$/i", "", basename($file));
    $files = glob("./data/grade/$fn");
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
}
// 관리자 카테고리 사진 삭제
function delete_category_thumbnail($file){
    if(!$file)
        return;

    $fn = preg_replace("/\.[^\.]+$/i", "", basename($file));
    $files = glob("./data/category/$fn");
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
}
// 상품이미지 썸네일 삭제
function delete_item_thumbnail($dir, $file){
    if(!$dir || !$file)
        return;

    $filename = preg_replace("/\.[^\.]+$/i", "", $file); // 확장자제거

    $files = glob($dir.'/thumb-'.$filename.'*');

    if(is_array($files)) {
        foreach($files as $thumb_file) {
            @unlink($thumb_file);
        }
    }
}
// AJAX DEMO 라는 파일이 있으면 데모 화면으로 인식함
function ajax_check_demo(){
	if(file_exists("./DEMO")) {
		die("{\"error\":\"데모 화면에서는 하실(보실) 수 없는 작업입니다.\"}");
	}
}
// url에 http:// 를 붙인다
function set_http($url){
    if(!trim($url)) return;

    if(!preg_match("/^(http|https|ftp|telnet|news|mms)\:\/\//i", $url))
        $url = "http://" . $url;

    return $url;
}
// 악성태그 변환
function bad_tag_convert($code){
	//return preg_replace("/\<([\/]?)(script|iframe)([^\>]*)\>/i", "&lt;$1$2$3&gt;", $code);
	// script 나 iframe 태그를 막지 않는 경우 필터링이 되도록 수정
	return preg_replace("/\<([\/]?)(script|iframe|form)([^\>]*)\>?/i", "&lt;$1$2$3&gt;", $code);
}
// way.co.kr 의 wayboard 참고
function url_auto_link($str){
    $str = str_replace(array("&lt;", "&gt;", "&amp;", "&quot;", "&nbsp;", "&#039;"), array("\t_lt_\t", "\t_gt_\t", "&", "\"", "\t_nbsp_\t", "'"), $str);
    $str = preg_replace("/([^(href=\"?'?)|(src=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[가-힣\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,\(\)]+)/i", "\\1<A HREF=\"\\2\" TARGET=\"_blank\">\\2</A>", $str);
    $str = preg_replace("/(^|[\"'\s(])(www\.[^\"'\s()]+)/i", "\\1<A HREF=\"http://\\2\" TARGET=\"_blank\">\\2</A>", $str);
    $str = preg_replace("/[0-9a-z_-]+@[a-z0-9._-]{4,}/i", "<a href=\"mailto:\\0\">\\0</a>", $str);
    $str = str_replace(array("\t_nbsp_\t", "\t_lt_\t", "\t_gt_\t", "'"), array("&nbsp;", "&lt;", "&gt;", "&#039;"), $str);

    return $str;
}
// 3.31
// HTML SYMBOL 변환
// &nbsp; &amp; &middot; 등을 정상으로 출력
function html_symbol($str){
    return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}
// 코드 : http://in2.php.net/manual/en/function.mb-check-encoding.php#95289
function is_utf8($str){
    $len = strlen($str);
    for($i = 0; $i < $len; $i++) {
        $c = ord($str[$i]);
        if ($c > 128) {
            if (($c > 247)) return false;
            elseif ($c > 239) $bytes = 4;
            elseif ($c > 223) $bytes = 3;
            elseif ($c > 191) $bytes = 2;
            else return false;
            if (($i + $bytes) > $len) return false;
            while ($bytes > 1) {
                $i++;
                $b = ord($str[$i]);
                if ($b < 128 || $b > 191) return false;
                $bytes--;
            }
        }
    }
    return true;
}
// 한글 요일
function get_yoil($date, $full=0){
	$arr_yoil = array ('일', '월', '화', '수', '목', '금', '토');

	$yoil = date("w", strtotime($date));
	$str = $arr_yoil[$yoil];
	if($full) {
		$str .= '요일';
	}
	return $str;
}
function get_checked($field, $value){
	return ($field==$value) ? ' checked="checked"' : '';
}
// 임시주문 데이터로 주문 필드 생성
function make_order_field($data, $exclude){
    $field = '';
    foreach($data as $key=>$value) {
        if(!empty($exclude) && in_array($key, $exclude))
            continue;

        if(is_array($value)) {
            foreach($value as $k=>$v) {
                $field .= '<input type="hidden" name="'.$key.'['.$k.']" value="'.$v.'">';
            }
        } else {
            $field .= '<input type="hidden" name="'.$key.'" value="'.$value.'">';
        }
    }
    return $field;
}
function escape_trim($field){
    $str = call_user_func('addslashes', $field);
    return $str;
}
// date 형식 변환
function conv_date_format($format, $date, $add=''){
    if($add)
        $timestamp = strtotime($add, strtotime($date));
    else
        $timestamp = strtotime($date);

    return date($format, $timestamp);
}
// 쿠폰 : 문자열 검색
function get_substr_count($haystrack, $needle){
	$count = 0;
	if($haystrack && $needle) {
		$arr_needle = explode(",", $needle);
		for($i=0; $i<count($arr_needle); $i++) {
			if(substr_count($haystrack, trim($arr_needle[$i])) > 0 ) {
				$count++;
			}
		}
	}

	return (int)$count;
}
// get_browser() 함수는 이미 있음
function get_brow($agent){
    $agent = strtolower($agent);

    //echo $agent; echo "<br/>";
    if(preg_match("/msie ([1-9][0-9]\.[0-9]+)/", $agent, $m)) { $s = 'MSIE '.$m[1]; }
    else if(preg_match("/firefox/", $agent))            { $s = "FireFox"; }
    else if(preg_match("/chrome/", $agent))             { $s = "Chrome"; }
    else if(preg_match("/x11/", $agent))                { $s = "Netscape"; }
    else if(preg_match("/opera/", $agent))              { $s = "Opera"; }
    else if(preg_match("/gec/", $agent))                { $s = "Gecko"; }
    else if(preg_match("/bot|slurp/", $agent))          { $s = "Robot"; }
    else if(preg_match("/internet explorer/", $agent))  { $s = "IE"; }
    else if(preg_match("/mozilla/", $agent))            { $s = "Mozilla"; }
    else { $s = "기타"; }

    return $s;
}
function get_os($agent){
    $agent = strtolower($agent);

    //echo $agent; echo "<br/>";

    if(preg_match("/windows 98/", $agent))                 { $s = "98"; }
    else if(preg_match("/windows 95/", $agent))             { $s = "95"; }
    else if(preg_match("/windows nt 4\.[0-9]*/", $agent))   { $s = "NT"; }
    else if(preg_match("/windows nt 5\.0/", $agent))        { $s = "2000"; }
    else if(preg_match("/windows nt 5\.1/", $agent))        { $s = "XP"; }
    else if(preg_match("/windows nt 5\.2/", $agent))        { $s = "2003"; }
    else if(preg_match("/windows nt 6\.0/", $agent))        { $s = "Vista"; }
    else if(preg_match("/windows nt 6\.1/", $agent))        { $s = "Windows7"; }
    else if(preg_match("/windows nt 6\.2/", $agent))        { $s = "Windows8"; }
    else if(preg_match("/windows 9x/", $agent))             { $s = "ME"; }
    else if(preg_match("/windows ce/", $agent))             { $s = "CE"; }
    else if(preg_match("/mac/", $agent))                    { $s = "MAC"; }
    else if(preg_match("/linux/", $agent))                  { $s = "Linux"; }
    else if(preg_match("/sunos/", $agent))                  { $s = "sunOS"; }
    else if(preg_match("/irix/", $agent))                   { $s = "IRIX"; }
    else if(preg_match("/phone/", $agent))                  { $s = "Phone"; }
    else if(preg_match("/bot|slurp/", $agent))              { $s = "Robot"; }
    else if(preg_match("/internet explorer/", $agent))      { $s = "IE"; }
    else if(preg_match("/mozilla/", $agent))                { $s = "Mozilla"; }
    else { $s = "기타"; }

    return $s;
}
// 발신번호 유효성 체크
function check_vaild_callback($callback){
    $_callback = preg_replace('/[^0-9]/','', $callback);
 
    /**
    * 1588 로시작하면 총8자리인데 7자리라 차단
    * 02 로시작하면 총9자리 또는 10자리인데 11자리라차단
    * 1366은 그자체가 원번호이기에 다른게 붙으면 차단
    * 030으로 시작하면 총10자리 또는 11자리인데 9자리라차단
    */
 
    if( substr($_callback,0,4) == '1588') if( strlen($_callback) != 8) return false;
    if( substr($_callback,0,2) == '02')   if( strlen($_callback) != 9  && strlen($_callback) != 10 ) return false;
    if( substr($_callback,0,3) == '030')  if( strlen($_callback) != 10 && strlen($_callback) != 11 ) return false;
 
    if( !preg_match("/^(02|0[3-6]\d|01(0|1|3|5|6|7|8|9)|070|080|007)\-?\d{3,4}\-?\d{4,5}$/",$_callback) &&
        !preg_match("/^(15|16|18)\d{2}\-?\d{4,5}$/",$_callback) ){
              return false;
    } else if( preg_match("/^(02|0[3-6]\d|01(0|1|3|5|6|7|8|9)|070|080)\-?0{3,4}\-?\d{4}$/",$_callback )) {
              return false;
    } else {
              return true;
    }
}
function get_sock($url){
    // host 와 uri 를 분리
    //if(ereg("http://([a-zA-Z0-9_\-\.]+)([^<]*)", $url, $res))
    if(preg_match("/http:\/\/([a-zA-Z0-9_\-\.]+)([^<]*)/", $url, $res)){
        $host = $res[1];
        $get  = $res[2];
    }

    // 80번 포트로 소캣접속 시도
    $fp = fsockopen ($host, 80, $errno, $errstr, 30);
    if(!$fp){
        die("$errstr ($errno)\n");
    }else{
        fputs($fp, "GET $get HTTP/1.0\r\n");
        fputs($fp, "Host: $host\r\n");
        fputs($fp, "\r\n");

        // header 와 content 를 분리한다.
        while(trim($buffer = fgets($fp,1024)) != "")
        {
            $header .= $buffer;
        }
        while(!feof($fp))
        {
            $buffer .= fgets($fp,1024);
        }
    }
    fclose($fp);

    // content 만 return 한다.
    return $buffer;
}
// 인증, 결제 모듈 실행 체크
function module_exec_check($exe, $type){
    $error = '';
    $is_linux = false;
    if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
        $is_linux = true;

    // 모듈 파일 존재하는지 체크
    if(!is_file($exe)) {
        $error = $exe.' 파일이 존재하지 않습니다.';
    } else {
        // 실행권한 체크
        if(!is_executable($exe)) {
            if($is_linux)
                $error = $exe.'\n파일의 실행권한이 없습니다.\n\nchmod 755 '.basename($exe).' 과 같이 실행권한을 부여해 주십시오.';
            else
                $error = $exe.'\n파일의 실행권한이 없습니다.\n\n'.basename($exe).' 파일에 실행권한을 부여해 주십시오.';
        } else {
            // 바이너리 파일인지
            if($is_linux) {
                $search = false;
                $isbinary = true;
                $executable = true;

                switch($type) {
                    case 'ct_cli':
                        exec($exe.' -h 2>&1', $out, $return_var);

                        if($return_var == 139) {
                            $isbinary = false;
                            break;
                        }

                        for($i=0; $i<count($out); $i++) {
                            if(strpos($out[$i], 'KCP ENC') !== false) {
                                $search = true;
                                break;
                            }
                        }
                        break;
                    case 'pp_cli':
                        exec($exe.' -h 2>&1', $out, $return_var);

                        if($return_var == 139) {
                            $isbinary = false;
                            break;
                        }

                        for($i=0; $i<count($out); $i++) {
                            if(strpos($out[$i], 'CLIENT') !== false) {
                                $search = true;
                                break;
                            }
                        }
                        break;
                    case 'okname':
                        exec($exe.' D 2>&1', $out, $return_var);

                        if($return_var == 139) {
                            $isbinary = false;
                            break;
                        }

                        for($i=0; $i<count($out); $i++) {
                            if(strpos(strtolower($out[$i]), 'ret code') !== false) {
                                $search = true;
                                break;
                            }
                        }
                        break;
                }

                if(!$isbinary || !$search) {
                    $error = $exe.'\n파일을 바이너리 타입으로 다시 업로드하여 주십시오.';
                }
            }
        }
    }

    if($error) {
        $error = '<script>alert("'.$error.'");</script>';
    }

    return $error;
}
//*******************************************
// SMS 발송 로직 수정 190705 ilhoon
//*******************************************
// 문자로 사용자정보
function get_moonjaro_userinfo($key){
    $url = "http://oapi.moonjaro.co.kr/auth/api_conn_check";
    $curlData = array('api_key' => $key);
    $ch = curl_init();                                 //curl 초기화
    curl_setopt($ch, CURLOPT_URL, $url);               //URL 지정하기
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);      //connection timeout 10초 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($curlData));          //data post 전송 
    curl_setopt($ch, CURLOPT_POST, true);              //true시 post 전송 
     
    $userinfo = curl_exec($ch);
    curl_close($ch);
    //return $userinfo;
    return json_decode($userinfo, true);
}
// 생년월일을 기준으로 연령대 뽑기
function get_birth_age($birth){
	if(!$birth) return '';

	$birth = substr($birth,0,4);
	$age = substr(date("Y")-$birth,0,1).'0';

	return $age;
}
function get_mobile_theme_dir(){
    $result_array = array();

    $dirname = '/m/theme/';
    if(!is_dir($dirname))
        return;

    $handle = opendir($dirname);
    while($file = readdir($handle)) {
        if($file == '.'||$file == '..') continue;

        if(is_dir($dirname.$file)) $result_array[] = $file;
    }
    closedir($handle);
    sort($result_array);

    return $result_array;
}
if(false){
    /*
    // 테마 path
    function get_theme_path($skin){
        $skin_path = OM_PATH.'/theme/'.$skin;
        return $skin_path;
    }
    // 테마 url
    function get_theme_url($skin){
        $skin_url = OM_URL.'/theme/'.$skin;
        return $skin_url;
    }
    // pc 테마 스킨경로를 얻는다
    function get_theme_dir(){
        $result_array = array();

        $dirname = OM_PATH.'/theme/';
        if(!is_dir($dirname))
            return;

        $handle = opendir($dirname);
        while($file = readdir($handle)) {
            if($file == '.'||$file == '..') continue;

            if(is_dir($dirname.$file)) $result_array[] = $file;
        }
        closedir($handle);
        sort($result_array);

        return $result_array;
    }
    // mobile 테마 path
    function get_mobile_theme_path($skin){
        $skin_path = OM_PATH.'/m/theme/'.$skin;

        return $skin_path;
    }
    // mobile 테마 url
    function get_mobile_theme_url($skin){
        $skin_url = OM_URL.'/m/theme/'.$skin;

        return $skin_url;
    }
    // mobile 테마 스킨경로를 얻는다
    
    */
}
// 원격지에 파일이 존재하는지 확인
// $filepath = "http://원격지 URL/파일명.png";
function remoteFileExist($filepath){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$filepath);
	curl_setopt($ch, CURLOPT_NOBODY, 1);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if(curl_exec($ch)!==false) {
		return true;
    } else {
        return false;
    }
}
function radio_checked($field, $checked, $value, $text=''){
    if(!$text) $text = $value;

	$str = '<label><input type="radio" name="'.$field.'" value="'.$value.'"';
	if($value == $checked) $str.= ' checked="checked"';
    $str.= '> '.$text.'</label>';

	return $str;
}
function check_checked($field, $checked, $value, $text=''){
    if(!$text) $text = $value;

	$str = '<label><input type="checkbox" name="'.$field.'" value="'.$value.'"';
	if($value == $checked) $str.= ' checked="checked"';
    $str.= '> '.$text.'</label>';

	return $str;
}
// 외부이미지 서버에 저장(방법,1)
function get_remote_image($url, $dir){
    $OM_FILE_PERMISSION=0644;
    $filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec ($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($http_code == 200)
	{
        $filename = basename($url);
        if(preg_match("/\.(gif|jpg|jpeg|png)$/i", $filename))		{
			$pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";

			$filename = preg_replace("/\s+/", "", $filename);
			$filename = preg_replace($pattern, "", $filename);

			$filename = preg_replace_callback(
								  "/[가-힣]+/",
								  create_function('$matches', 'return base64_encode($matches[0]);'),
								  $filename);

			$filename = preg_replace($pattern, "", $filename);

            // 파일 다운로드
            $path = $dir.'/'.$filename;
            $fp = fopen ($path, 'w+');

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
            curl_setopt( $ch, CURLOPT_FILE, $fp );
            curl_exec( $ch );
            curl_close( $ch );

            fclose( $fp );

            // 다운로드 파일이 이미지인지 체크
            if(is_file($path)) {
                $size = @getimagesize($path);
                if($size[2] < 1 || $size[2] > 3) {
                    @unlink($path);
                    $filename = '';
                } else {
                    @rename($path, $dir.'/'.$filename);
                    @chmod($dir.'/'.$filename, $OM_FILE_PERMISSION);
                }
            }
        }
    }

    return $filename;
}
// 외부이미지 서버에 저장(방법,2)
function get_remote_image2($url, $dir){
	if(strpos($url,"http://") !== false){
		$name_exchane = array('?','%');

		$newname = str_replace($name_exchane,'_',basename($url));

		$path = pathinfo($newname); //파일에 대한 정보를 얻음
		$ext = strtolower($path['extension']); //확장자를 연관배열에서 가져

		if(!is_dir($dir)){
			$oldmask = umask(0);
			@mkdir($dir, 0777);
			umask($oldmask);
		}

		$img_file = file_get_contents($url);
		$file_handler = fopen($dir."/".$newname,'wb');
		if(fwrite($file_handler,$img_file)==false){
			echo 'error';
		}
		fclose($file_handler);

		return $newname;
	}
}
// 이메일 주소 추출
function get_email_address($email){
    preg_match("/[0-9a-z._-]+@[a-z0-9._-]{4,}/i", $email, $matches);
    return isset($matches[0])?$matches[0]:"";
}
// 별
function get_star($score){
    $star = round($score);
    if($star > 5) $star = 5;
    else if($star < 0) $star = 0;

    return $star;
}
// 은행정보 : select 형태로 얻음
function get_bank_select($name, $opt=''){
	$str = "<select name=\"{$name}\" id=\"{$name}\"";
    if($opt) $str .= " $opt";
    $str .= ">\n";
	$str.= "<option value=''>선택</option>\n";
	$str.= "<option value='경남은행'>경남은행</option>\n";
	$str.= "<option value='광주은행'>광주은행</option>\n";
	$str.= "<option value='국민은행'>국민은행</option>\n";
	$str.= "<option value='기업은행'>기업은행</option>\n";
	$str.= "<option value='농협'>농협</option>\n";
	$str.= "<option value='대구은행'>대구은행</option>\n";
	$str.= "<option value='도이치뱅크'>도이치뱅크</option>\n";
	$str.= "<option value='부산은행'>부산은행</option>\n";
	$str.= "<option value='산업은행'>산업은행</option>\n";
	$str.= "<option value='상호저축은행'>상호저축은행</option>\n";
	$str.= "<option value='새마을금고'>새마을금고</option>\n";
	$str.= "<option value='수협중앙회'>수협중앙회</option>\n";
	$str.= "<option value='신용협동조합'>신용협동조합	</option>\n";
	$str.= "<option value='신한은행'>신한은행</option>\n";
	$str.= "<option value='외환은행'>외환은행</option>\n";
	$str.= "<option value='우리은행'>우리은행</option>\n";
	$str.= "<option value='우체국'>우체국</option>\n";
	$str.= "<option value='전북은행'>전북은행</option>\n";
	$str.= "<option value='제주은행'>제주은행</option>\n";
	$str.= "<option value='하나은행'>하나은행</option>\n";
	$str.= "<option value='한국시티은행'>한국시티은행</option>\n";
	$str.= "<option value='HSBC'>HSBC</option>\n";
	$str.= "<option value='SC제일은행'>SC제일은행</option>\n";
	$str.= "</select>";

	return $str;
}
// 취소/반품/교환 : select 형태로 얻음
function get_cancel_select($name, $opt=''){
	$str = "<select name=\"{$name}\"";
    if($opt) $str .= " $opt";
    $str .= ">\n";
	$str.= "<option value=''>선택</option>\n";
	$str.= "<option value='고객변심(스타일)'>고객변심(스타일)</option>\n";
	$str.= "<option value='출하전 취소(주문서변경)'>출하전 취소(주문서변경)</option>\n";
	$str.= "<option value='화면과 다름(퀄리티)'>화면과 다름(퀄리티)</option>\n";
	$str.= "<option value='퀄리티 불만'>퀄리티 불만</option>\n";
	$str.= "<option value='중복주문'>중복주문</option>\n";
	$str.= "<option value='A/S관련'>A/S관련</option>\n";
	$str.= "<option value='재결제'>재결제</option>\n";
	$str.= "<option value='품절'>품절</option>\n";
	$str.= "<option value='상품불량'>상품불량</option>\n";
	$str.= "<option value='결제 오류'>결제 오류</option>\n";
	$str.= "<option value='시스템오류'>시스템오류</option>\n";
	$str.= "<option value='오배송'>오배송</option>\n";
	$str.= "<option value='출하전 취소(재주문)'>출하전 취소(재주문)</option>\n";
	$str.= "<option value='출하전 취소(변심환불)'>출하전 취소(변심환불)</option>\n";
	$str.= "<option value='배송중분실'>배송중분실</option>\n";
	$str.= "<option value='기타'>기타</option>\n";
	$str.= "<option value='고객센터 불만족'>고객센터 불만족</option>\n";
	$str.= "<option value='업무 처리 지연'>업무 처리 지연</option>\n";
	$str.= "<option value='교환제품 품절'>교환제품 품절</option>\n";
	$str.= "<option value='사이즈 맞지 않음(단순)'>사이즈 맞지 않음(단순)</option>\n";
	$str.= "<option value='화면과 다름(색상)'>화면과 다름(색상)</option>\n";
	$str.= "<option value='화면과 다름(디자인)'>화면과 다름(디자인)</option>\n";
	$str.= "<option value='화면과 다름(재질)'>화면과 다름(재질)</option>\n";
	$str.= "<option value='상세 실측 오류'>상세 실측 오류</option>\n";
	$str.= "<option value='고객오류'>고객오류</option>\n";
	$str.= "<option value='배송지연'>배송지연</option>\n";
	$str.= "</select>";

	return $str;
}
// 분류 옵션을 얻음
function get_category_option($usecate){
	$arr = explode("|", $usecate); // 구분자가 | 로 되어 있음
	$str = "";
	for($i=0; $i<count($arr); $i++)
		if(trim($arr[$i]))
			$str .= "<option value='$arr[$i]'>$arr[$i]</option>\n";

	return $str;
}

// 배열을 comma 로 구분하여 연결
function gnd_implode($str, $comma=","){
	$arr = is_array($str) ? $str : array($str);
	return implode($comma, $arr);
}
if(false){
    /*
    // 스킨 style sheet 파일 얻기
    function get_skin_stylesheet($skin_path, $dir=''){
        if(!$skin_path)
            return "";

        $str = "";
        $files = array();

        if($dir)
            $skin_path .= '/'.$dir;

        $skin_url = OM_URL.str_replace("\\", "/", str_replace(OM_PATH, "", $skin_path));
        if(is_dir($skin_path)) {
            if($dh = opendir($skin_path)) {
                while(($file = readdir($dh)) !== false) {
                    if($file == "." || $file == "..")
                        continue;

                    if(is_dir($skin_path.'/'.$file))
                        continue;

                    if(preg_match("/\.(css)$/i", $file))
                        $files[] = $file;
                }
                closedir($dh);
            }
        }
        if(!empty($files)) {
            sort($files);

            foreach($files as $file) {
                $str .= '<link rel="stylesheet" href="'.$skin_url.'/'.$file.'?='.date("md").'">'."\n";
            }
        }
        return $str;
        
        // glob 를 이용한 코드
        // if(!$skin_path) return '';
        // $skin_path .= $dir ? '/'.$dir : '';

        // $str = '';
        // $skin_url = OM_URL.str_replace('\\', '/', str_replace(OM_PATH, '', $skin_path));

        // foreach (glob($skin_path.'/*.css') as $filepath) {
        //     $file = str_replace($skin_path, '', $filepath);
        //     $str .= '<link rel="stylesheet" href="'.$skin_url.'/'.$file.'?='.date('md').'">'."\n";
        // }
        // return $str;
        
    }
    // 스킨 javascript 파일 얻기
    function get_skin_javascript($skin_path, $dir=''){
        if(!$skin_path)
            return "";

        $str = "";
        $files = array();
        if($dir)
            $skin_path .= '/'.$dir;

        $skin_url = OM_URL.str_replace("\\", "/", str_replace(OM_PATH, "", $skin_path));
        if(is_dir($skin_path)) {
            if($dh = opendir($skin_path)) {
                while(($file = readdir($dh)) !== false) {
                    if($file == "." || $file == "..")
                        continue;

                    if(is_dir($skin_path.'/'.$file))
                        continue;

                    if(preg_match("/\.(js)$/i", $file))
                        $files[] = $file;
                }
                closedir($dh);
            }
        }
        if(!empty($files)) {
            sort($files);
            foreach($files as $file) {
                $str .= '<script src="'.$skin_url.'/'.$file.'"></script>'."\n";
            }
        }
        return $str;
    }*/
}
// 휴대폰번호의 숫자만 취한 후 중간에 하이픈(-)을 넣는다.
function hyphen_hp_number($hp){
    $hp = preg_replace("/[^0-9]/", "", $hp);
    return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $hp);
}
// CHARSET 변경 : utf-8 -> euc-kr
function iconv_euckr($str){
	return iconv('utf-8', 'euc-kr', $str);
}
// CHARSET 변경 : euc-kr -> utf-8
function iconv_utf8($str){
	return iconv('euc-kr', 'utf-8', $str);
}
// 쿼리에 맞게 콤마로 구분
function mb_comma($list){
	$mid = $comma = '';
	foreach($list as $id) {
		$id = trim($id);
		$mid .= $comma."'{$id}'";
		$comma = ',';
	}

	return $mid;
}

function get_selected($field, $value){
	return ($field==$value) ? ' selected="selected"' : '';
}
// 시간이 비어 있는지 검사
function is_null_time($datetime){
    // 공란 0 : - 제거
    $datetime = preg_replace("/[ 0:-]/", "", $datetime);
    if ($datetime == "")
        return true;
    else
        return false;
}
// 배송조회버튼 생성
function get_delivery_inquiry($company, $invoice, $class=''){
	if(!$company || !$invoice)
		return '';

	// 배송정보 (예:배송회사|배송추적URL)
	list($com, $url) = explode('|', $company);

	$str = '';
	if($com && $url) {
		$str .= '<a href="'.$url.$invoice.'" target="_blank"';
		if($class) $str .= ' class="'.$class.'"';
		$str .='>배송조회</a>';
	}

	return $str;
}
// sns 공유하기
function get_sns_share_link($sns, $url, $title, $image_url){
    if(!$sns)
        return '';

	$sns_url = $url;
	$sns_msg = str_replace('\"', '"', strip_tags($title));
	$sns_msg = str_replace('\'', '', $sns_msg);
	#$sns_send = OM_LIB_URL.'/sns_send.php?longurl='.urlencode($sns_url).'&amp;title='.urlencode($sns_msg);
    $sns_send = base_url().'process/sns_send?longurl='.urlencode($sns_url).'&amp;title='.urlencode($sns_msg);

    switch($sns) {
		case 'facebook':
			$facebook_url = $sns_send.'&amp;sns=facebook';
			$str = 'share_sns(\'facebook\', \''.$facebook_url.'\'); return false;';
			$str = '<a href="'.$facebook_url.'" onclick="'.$str.'" target="_blank"><img src="'.$image_url.'"></a>';
            break;
        case 'twitter':
			$twitter_url = $sns_send.'&amp;sns=twitter';
			$str = 'share_sns(\'twitter\', \''.$twitter_url.'\'); return false;';
			$str = '<a href="'.$twitter_url.'" onclick="'.$str.'" target="_blank"><img src="'.$image_url.'"></a>';
			break;
        case 'naver':
			$naver_url = $sns_send.'&amp;sns=naver';
			$str = 'share_sns(\'naver\',\''.$naver_url.'\'); return false;';
			$str = '<a href="'.$naver_url.'" onclick="'.$str.'" target="_blank"><img src="'.$image_url.'" alt="Naver"></a>';
            break;
		case 'googleplus':
			$gplus_url = $sns_send.'&amp;sns=googleplus';
            $str = 'share_sns(\'googleplus\',\''.$gplus_url.'\'); return false;';
			$str = '<a href="'.$gplus_url.'" onclick="'.$str.'" target="_blank"><img src="'.$image_url.'"></a>';
            break;
		case 'kakaostory':
			$kakaostory_url = $sns_send . '&amp;sns=kakaostory';
            $str = 'share_sns(\'kakaostory\',\'' . $kakaostory_url . '\'); return false;';
			$str = '<a href="'.$kakaostory_url.'" onclick="'.$str.'" target="_blank"><img src="'.$image_url.'"></a>';
            break;
		case 'naverband':
			$naverband_url = $sns_send . '&amp;sns=naverband';
            $str = 'share_sns(\'naverband\',\'' . $naverband_url . '\'); return false;';
			$str = '<a href="'.$naverband_url.'" onclick="'.$str.'" target="_blank"><img src="'.$image_url.'"></a>';
            break;
		case 'pinterest':
			$pinterest_url = $sns_send . '&amp;sns=pinterest';
            $str = 'share_sns(\'pinterest\',\'' . $pinterest_url . '\'); return false;';
			$str = '<a href="'.$pinterest_url.'" onclick="'.$str.'" target="_blank"><img src="'.$image_url.'"></a>';
            break;
		case 'tumblr':
			$tumblr_url = $sns_send.'&amp;sns=tumblr';
			$str = 'share_sns(\'tumblr\',\''.$tumblr_url.'\'); return false;';
			$str = '<a href="'.$tumblr_url.'" onclick="'.$str.'" target="_blank"><img src="'.$image_url.'"></a>';
            break;
    }

    return $str;
}
// 세션변수 생성
function set_session($session_name, $value){
    static $check_cookie = null;

    if( $check_cookie === null ){
        $cookie_session_name = session_name();
        if( ! ($cookie_session_name && isset($_COOKIE[$cookie_session_name]) && $_COOKIE[$cookie_session_name]) && ! headers_sent() ){
            @session_regenerate_id(false);
        }

        $check_cookie = 1;
    }

    if (PHP_VERSION < '5.3.0')
        session_register($session_name);
    // PHP 버전별 차이를 없애기 위한 방법
    $session_name = $_SESSION["$session_name"] = $value;
}
// 수량 표시
function display_qty($price){
	return number_format($price, 0).'개';
}

// 세션변수값 얻음
function get_session($session_name){
	return isset($_SESSION[$session_name])?$_SESSION[$session_name]:"";
}

// 포인트 표시
function display_point($price){
	return number_format($price, 0).'P';
}
// 금액 표시
function display_price($price){
	return number_format($price, 0).'원';
}
// 금액 표시
function display_price2($price){
	return '<span class="spr">'.number_format($price).'<span>원</span></span>';
}
// 한페이지에 보여줄 행, 현재페이지, 총페이지수, URL
function get_paging($write_pages, $cur_page, $total_page, $url, $add=""){
	if(!$cur_page) $cur_page = 1;
	if(!$total_page) $total_page = 1;
	if($total_page < 2) return '';

	$url = preg_replace('#&page=[0-9]*#', '', $url) . '&page=';

    $str = '';
	if($cur_page < 2) {
		$str .= '<span class="pg_start">처음</span>';
	} else {
		$str .= '<a href="'.$url.'1'.$add.'" class="pg_page pg_start">처음</a>';
	}

	$start_page = (((int)(($cur_page - 1 ) / $write_pages)) * $write_pages) + 1;
	$end_page = $start_page + $write_pages - 1;

	if($end_page >= $total_page) $end_page = $total_page;

	if($start_page > 1) {
		$str .= '<a href="'.$url.($start_page-1).$add.'" class="pg_page pg_prev">이전</a>';
	} else {
		$str .= '<span class="pg_prev">이전</span>';
	}

    if($total_page > 1) {
        for($k=$start_page;$k<=$end_page;$k++) {
            if($cur_page != $k) {
                $str .= '<a href="'.$url.$k.$add.'" class="pg_page">'.$k.'<span class="sound_only">페이지</span></a>';
            } else {
                $str .= '<span class="sound_only">열린</span><strong class="pg_current">'.$k.'</strong><span class="sound_only">페이지</span>';
			}
        }
    }

	if($total_page > $end_page) {
		$str .= '<a href="'.$url.($end_page+1).$add.'" class="pg_page pg_next">다음</a>';
	} else {
		$str .= '<span class="pg_next">다음</span>';
	}

	if($cur_page < $total_page) {
		$str .= '<a href="'.$url.$total_page.$add.'" class="pg_page pg_end">맨끝</a>';
	} else {
		$str .= '<span class="pg_end">맨끝</span>';
	}

    return "<nav class=\"pg_wrap\"><span class=\"pg\">{$str}</span></nav>";
}
// UTF-8 문자열 자르기
// 출처 : https://www.google.co.kr/search?q=utf8_strcut&aq=f&oq=utf8_strcut&aqs=chrome.0.57j0l3.826j0&sourceid=chrome&ie=UTF-8
function utf8_strcut( $str, $size, $suffix='...' ){
	$substr = substr( $str, 0, $size * 2 );
	$multi_size = preg_match_all( '/[\x80-\xff]/', $substr, $multi_chars );

	if( $multi_size > 0 )
		$size = $size + intval( $multi_size / 3 ) - 1;

	if( strlen( $str ) > $size ) {
		$str = substr( $str, 0, $size );
		$str = preg_replace( '/(([\x80-\xff]{3})*?)([\x80-\xff]{0,2})$/', '$1', $str );
		$str .= $suffix;
	}

	return $str;
}
// 검색어 특수문자 제거
function get_search_string($stx){
    $stx_pattern = array();
    $stx_pattern[] = '#\.*/+#';
    $stx_pattern[] = '#\\\*#';
    $stx_pattern[] = '#\.{2,}#';
    $stx_pattern[] = '#[/\'\"%=*\#\(\)\|\+\&\!\$~\{\}\[\]`;:\?\^\,]+#';

    $stx_replace = array();
    $stx_replace[] = '';
    $stx_replace[] = '';
    $stx_replace[] = '.';
    $stx_replace[] = '';

    $stx = preg_replace($stx_pattern, $stx_replace, $stx);

    return $stx;
}
// 문자열이 한글, 영문, 숫자, 특수문자로 구성되어 있는지 검사
function check_string($str, $options){
    $OM_ALPHAUPPER=1;
    $OM_ALPHALOWER=2;
    $OM_ALPHABETIC=4;
    $OM_NUMERIC=8;
    $OM_HANGUL=16;
    $OM_SPACE=32;
    $OM_SPECIAL=64;
    $s = '';
    for($i=0;$i<strlen($str);$i++) {
        $c = $str[$i];
        $oc = ord($c);

        // 한글
        if($oc >= 0xA0 && $oc <= 0xFF) {
            if($options & $OM_HANGUL) {
                $s .= $c . $str[$i+1] . $str[$i+2];
            }
            $i+=2;
        }
        // 숫자
        else if($oc >= 0x30 && $oc <= 0x39) {
            if($options & $OM_NUMERIC) {
                $s .= $c;
            }
        }
        // 영대문자
        else if($oc >= 0x41 && $oc <= 0x5A) {
            if(($options & $OM_ALPHABETIC) || ($options & $OM_ALPHAUPPER)) {
                $s .= $c;
            }
        }
        // 영소문자
        else if($oc >= 0x61 && $oc <= 0x7A) {
            if(($options & $OM_ALPHABETIC) || ($options & $OM_ALPHALOWER)) {
                $s .= $c;
            }
        }
        // 공백
        else if($oc == 0x20) {
            if($options & $OM_SPACE) {
                $s .= $c;
            }
        }
        else {
            if($options & $OM_SPECIAL) {
                $s .= $c;
            }
        }
    }

    // 넘어온 값과 비교하여 같으면 참, 틀리면 거짓
    return ($str == $s);
}
// 경고메세지 출력후 창을 닫음
function alert_close($msg){
    if(!$msg) $msg = '올바른 방법으로 이용해 주십시오.';
 
    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">";
    echo "<script type='text/javascript'>alert(\"{$msg}\");window.close();</script>";exit;
 }
function option_selected($value, $selected, $text=''){
    if(!$text) $text = $value;
    if($value == $selected)
        return "<option value=\"{$value}\" selected=\"selected\">{$text}</option>\n";
    else
        return "<option value=\"{$value}\">{$text}</option>\n";
}
// 글자수를 자루는 함수.
function cut_str($str, $len, $suffix="…"){
    $arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    $str_len = count($arr_str);

    if($str_len >= $len) {
        $slice_str = array_slice($arr_str, 0, $len);
        $str = join("", $slice_str);

        return $str . ($str_len > $len ? $suffix : '');
    } else {
        $str = join("", $arr_str);
        return $str;
    }
}
function goto_url($url){
    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">";
    echo "<script type='text/javascript'>location.replace('{$url}');</script>";
}
// 문자열중 숫자만 추출
function conv_number($str){
	return preg_replace("/[^0-9]*/s", "", $str);
}
// 마이크로 타임을 얻어 계산 형식으로 만듦
function get_microtime(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}
// 쿠키변수값 얻음
function get_cookie($cookie_name){
    if(isset($_COOKIE[md5($cookie_name)])){
        return base64_decode($_COOKIE[md5($cookie_name)]);
    }
    return null;
}
// str_replace
function rpc($str, $kind=",", $conv=""){
	return str_replace($kind, $conv, $str);
}

// 쿠키변수 생성
function set_cookie($cookie_name, $value, $expire){
	setcookie(md5($cookie_name), base64_encode($value), time() + $expire, '/', $_SERVER['HTTP_HOST']);
}

?>