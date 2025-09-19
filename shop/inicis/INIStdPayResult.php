<?php
if(!defined('_HALLYUWEB_')) exit; // 개별 페이지 접근 불가

$tb['title'] = 'KG 이니시스 결제';
$tb['body_script'] = ' onload="setPAYResult();"';
include_once(OM_PATH.'/head.sub.php');

$exclude = array();

echo '<form name="forderform" method="post" action="'.$order_action_url.'" autocomplete="off">'.PHP_EOL;

echo make_order_field($data, $exclude);

echo '</form>'.PHP_EOL;
?>

<div id="pay_working">
    <span style="display:block; text-align:center;margin-top:120px"><img src="<?php echo OM_IMG_URL; ?>/loading2.gif" alt=""></span>
    <span style="display:block; text-align:center;margin-top:10px; font-size:14px">주문완료 중입니다. 잠시만 기다려 주십시오.</span>
</div>

<script type="text/javascript">
function setPAYResult() {
    setTimeout( function() {
        document.forderform.submit();
    }, 300);
}
</script>