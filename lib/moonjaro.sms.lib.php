<?php
#if(!defined('_OMNIWEB_')) exit;
// 문자로에서 제공하는 함수

///////////////////////////////////////////////////////////////////////////////////////////
// 이 부분은 건드릴 필요가 없습니다.

function spacing($text,$size) {
	for($i=0; $i<$size; $i++) $text.=" ";
	$text = substr($text,0,$size);
	return $text;
}

function cut_char($word, $cut) {
//	$word=trim(stripslashes($word));
	$word=substr($word,0,$cut);						// 필요한 길이만큼 취함.
	for($k=$cut-1; $k>1; $k--) {
		if(ord(substr($word,$k,1))<128) break;		// 한글값은 160 이상.
	}
	$word=substr($word,0,$cut-($cut-$k+1)%2);
	return $word;
}

function CheckCommonType($to, $rsvTime) {
	//$to=eregi_replace("[^0-9]","",$to);
	$to=preg_replace("/[^0-9]/i","",$to);
	if(strlen($to)<10 || strlen($to)>11) return "휴대폰 번호가 틀렸습니다";
	$CID=substr($to,0,3);
	//if( eregi("[^0-9]",$CID) || ($CID!='010' && $CID!='011' && $CID!='016' && $CID!='017' && $CID!='018' && $CID!='019') ) return "휴대폰 앞자리 번호가 잘못되었습니다";
	if( preg_match("/[^0-9]/i",$CID) || ($CID!='010' && $CID!='011' && $CID!='016' && $CID!='017' && $CID!='018' && $CID!='019') ) return "휴대폰 앞자리 번호가 잘못되었습니다";
	//$rsvTime=eregi_replace("[^0-9]","",$rsvTime);
	$rsvTime=preg_replace("/[^0-9]/i","",$rsvTime);
	if($rsvTime) {
		if(!checkdate(substr($rsvTime,4,2),substr($rsvTime,6,2),substr($rsvTime,0,4))) return "예약날짜가 잘못되었습니다";
		if(substr($rsvTime,8,2)>23 || substr($rsvTime,10,2)>59) return "예약시간이 잘못되었습니다";
	}
}

class SMS {
	var $API_KEY;
	var $SMS_Server;
	var $Data = array();
	var $Result = array();

	function SMS_con($sms_server,$api_key) {
		$this->API_KEY=$api_key;		// 계약 후 지정
		$this->SMS_Server=$sms_server;
	}

	function Init() {
		$this->Data = "";
		$this->Result = "";
	}

	// 단독으로 발송
	function Add($to, $from, $subject, $text) {
		// 내용 검사 1
		// rsvTime : 예약시간
		$rsvTime = '';
		$Error = CheckCommonType($to, $rsvTime);
		if($Error) return $Error;
		// 내용 검사 2
		//if( eregi("[^0-9]",$from) ) return "회신 전화번호가 잘못되었습니다";
		if( preg_match("/[^0-9]/i",$from) ) return "회신 전화번호가 잘못되었습니다";

        $text=cut_char($text,2000); // 2000byte 제한
        $subject = cut_char($subject,40); // 40byte 제한

		// 보낼 내용을 배열에 집어넣기
		//$rsvTime = spacing($rsvTime,12);
		$this->Data[] = array('api_key'=>$this->API_KEY ,'to'=>$to, 'from'=>$from, 'subject'=>$subject, 'text'=>$text);
		return "";
	}

	// 그룹으로 동일문자내용 발송
	function AddGroup($to, $from, $subject, $text){
		// 내용 검사 1
		// rsvTime : 예약시간
		// to :  01011112222, 01022223333, ...
		// 내용 검사 2
		//if( eregi("[^0-9]",$from) ) return "회신 전화번호가 잘못되었습니다";
		if( preg_match("/[^0-9]/i",$from) ) return "회신 전화번호가 잘못되었습니다";

        $text=cut_char($text,2000); // 2000byte 제한
        $subject = cut_char($subject,40); // 40byte 제한

		// 보낼 내용을 배열에 집어넣기
		//$rsvTime = spacing($rsvTime,12);
		$this->Data[] = array('api_key'=>$this->API_KEY, 'to'=>$to, 'from'=>$from, 'subject'=>$subject, 'text'=>$text);
		return "";
	}

	function Send () {
		// 발송로직
		// $success_count = 0;
		// var_dump($this->Data);
		foreach ($this->Data as $puts) {
			$postData = $puts;
			$url = $this->SMS_Server;
 
			$ch = curl_init();                                 //curl 초기화
			curl_setopt($ch, CURLOPT_URL, $url);               //URL 지정하기
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환 
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);      //connection timeout 60초 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
			curl_setopt($ch, CURLOPT_POST, true);              //true시 post 전송 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);   //data post 전송 

			$response = curl_exec($ch);
			curl_close($ch);

			// $response = json_decode($response, true);
			// $success_count += (int)$response['success_count'];
		}
		$this->Data = '';
		return true;
	}
}
?>