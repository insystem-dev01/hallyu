<?php
include_once('./_common.php');
include_once(OM_LIB_PATH.'/naverpay.lib.php');

$query = $_SERVER['QUERY_STRING'];

$vars = array();

foreach(explode('&', $query) as $pair) {
    list($key, $value) = explode('=', $pair);
    $key = urldecode($key);
    $value = preg_replace("/[^A-Za-z0-9\-_]/", "", urldecode($value));
    $vars[$key][] = $value;
}

$itemIds = $vars['ITEM_ID'];

if(count($itemIds) < 1) {
    exit('ITEM_ID 는 필수입니다.');
}

header('Content-Type: application/xml;charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<response>
<?php
foreach($itemIds as $gs_id) {
    $sql = " select * from shop_goods where index_no = '$gs_id' ";
    $gs = sql_fetch($sql);
    if(!$gs['index_no'])
        continue;

    $id          = $gs['index_no'];
    $name        = $gs['gname'];
    $description = $gs['explan'];
    $price       = get_sale_price($gs_id);
    $image       = get_naverpay_item_image_url($gs_id);
    $quantity    = get_naverpay_item_stock($gs_id);
    $ca_name     = '';
    $ca_name2    = '';
    $ca_name3    = '';
    $returnInfo  = get_naverpay_return_info($gs['mb_id']);
    $option      = get_naverpay_item_option($gs_id, $gs['opt_subject']);

    if($gs['ca_id'])  $ca_name  = get_catename($gs['ca_id']);
    if($gs['ca_id2']) $ca_name2 = get_catename($gs['ca_id2']);
    if($gs['ca_id3']) $ca_name3 = get_catename($gs['ca_id3']);
?>
<item id="<?php echo $id; ?>">
<?php if($gs['ec_mall_pid']) { ?>
<name><![CDATA[<?php echo $gs['ec_mall_pid']; ?>]]></name>
<?php } ?>
<name><![CDATA[<?php echo $name; ?>]]></name>
<url><?php echo OM_SHOP_URL.'/view.php?index_no='.$gs_id; ?></url>
<description><![CDATA[<?php echo $description; ?>]]></description>
<image><?php echo $image; ?></image>
<thumb><?php echo $image; ?></thumb>
<price><?php echo $price; ?></price>
<quantity><?php echo $quantity; ?></quantity>
<category>
<first id="MJ01"><![CDATA[<?php echo $ca_name; ?>]]></first>
<second id="ML01"><![CDATA[<?php echo $ca_name2; ?>]]></second>
<third id="MN01"><![CDATA[<?php echo $ca_name3; ?>]]></third>
</category>
<?php echo $option; ?>
<?php echo $returnInfo; ?>
</item>
<?php
}
echo('</response>');
?>