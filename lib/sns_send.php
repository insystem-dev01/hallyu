<?php
include_once("./_common.php");

$title =  urlencode(str_replace('\"', '"',$_REQUEST['title']));
$short_url = google_short_url($_REQUEST['longurl']);
if(!$short_url) {
	$short_url = $_REQUEST['longurl'];
	// Naver
	if($_REQUEST['sns'] == 'naver') {
		$short_url = str_replace("&amp;", "%26", $short_url);
		$short_url = str_replace("&", "%26", $short_url);
	}
	$short_url = urlencode($short_url);
}
$title_url = $title.' : '.$short_url;

switch($_REQUEST['sns']) {
    case 'facebook' :
	    header("Location:http://www.facebook.com/sharer/sharer.php?s=100&u=".$short_url."&p=".$title);
		break;
    case 'twitter' :
        header("Location:https://twitter.com/intent/tweet?text=".$title_url);
        break;
	case 'naver' :
        header("Location:http://share.naver.com/web/shareView.nhn?url=".$short_url."&title=".$title);
        break;
	case 'naverband' :
        header("Location:http://www.band.us/plugin/share?body=".$title_url);
        break;
	case 'googleplus' :
        header("Location:https://plus.google.com/share?url=".$short_url);
        break;
	case 'kakaostory' :
        header("Location:https://story.kakao.com/share?url=".$short_url);
        break;
	case 'pinterest' :
		header("Location:https://www.pinterest.com/pin/create/button/?url=".$short_url.'&description='.$title);
        break;
    case 'tumblr' :
		header("Location:http://tumblr.com/widgets/share/tool?canonicalUrl=".$short_url); 
        break;
	default :
        echo 'Error';
}
?>