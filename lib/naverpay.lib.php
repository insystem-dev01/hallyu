<?php
if(!defined('_OMNIWEB_')) exit;

class naverpay_register
{
    public $options;
    public $keys;
    public $send_cost;
    public $total_price;

    function __construct($options, $send_cost)
    {
        $this->options = $options;
        $this->send_cost = $send_cost;
    }

    function get_sendcost()
    {
        global $config;

        $options = $this->options;
        $send_cost = $this->send_cost;
        $keys = $this->keys;

        $data = array();

        if($send_cost == 1)
            return array('type' => 'ONDELIVERY', 'cost' => 0);

        $cost = 0;
        $cnt  = 0;
        $total_price = 0;

        foreach($keys as $gs_id) {
            $gs = sql_fetch(" select * from shop_goods where index_no = '$gs_id' ");
            if(!$gs['index_no'])
                continue;

			if($gs['sc_method'] == 1) { // 착불
				$cnt++;
				continue;
			}

            $qty = 0;
            $price = 0;
            $opts = $options[$gs_id];
            $uprice = get_sale_price($gs_id);

            foreach($opts as $opt) {
                if($opt['type'])
                    $price += ((int)$opt['price'] * (int)$opt['qty']);
                else
                    $price += (((int)$uprice + (int)$opt['price']) * (int)$opt['qty']);

                $qty += $opt['qty'];
            }

            $cost += get_sendcost_amt2($gs_id, $price);
        }

        // 모두 착불상품이면
        if(count($keys) == $cnt && $cnt > 0)
            return array('type' => 'ONDELIVERY', 'cost' => 0);

        if($cost > 0)
            $data = array('type' => 'PAYED', 'cost' => $cost);
        else
            $data = array('type' => 'FREE', 'cost' => 0);

        return $data;
    }

    function query()
    {
        global $default;

        $keys = array();
        $opts = array();

        $item     = '';
        $query    = '';
        $total    = 0;
        $shipping = '';

        $keys = array_unique(array_keys($this->options));
        $this->keys = $keys;

        foreach($keys as $gs_id) {
            $sql = " select * from shop_goods where index_no = '$gs_id' and isopen = '1' and shop_state = '0' and price_msg = '' ";
            $gs = sql_fetch($sql);
            if(!$gs['index_no'])
                continue;

            $opts = $this->options[$gs_id];

            if(empty($opts) || !is_array($opts))
                continue;

            $gname = $gs['gname'];
            $uprice  = get_sale_price($gs_id);
            $tprice  = 0;

            foreach($opts as $opt) {
                if($opt['type'])
                    $tprice = ((int)$opt['price'] * (int)$opt['qty']);
                else
                    $tprice = (((int)$uprice + (int)$opt['price']) * (int)$opt['qty']);

                $item .= '&ITEM_ID='.urlencode($gs_id);
                if($gs['ec_mall_pid'])
                    $item .= '&EC_MALL_PID='.urlencode($gs['ec_mall_pid']);
                $item .= '&ITEM_NAME='.urlencode($gname);
                $item .= '&ITEM_COUNT='.$opt['qty'];
                $item .= '&ITEM_OPTION='.urlencode($opt['option']);
                $item .= '&ITEM_TPRICE='.$tprice;
                $item .= '&ITEM_UPRICE='.$uprice;

                $total += $tprice;
            }
        }

        $sendcost = $this->get_sendcost();

        if($sendcost['cost'] > 0)
            $total += $sendcost['cost'];

        $this->total_price = $total;

        $shipping .= '&SHIPPING_TYPE='.$sendcost['type'];
        $shipping .= '&SHIPPING_PRICE='.$sendcost['cost'];
        if(defined('SHIPPING_ADDITIONAL_PRICE') && SHIPPING_ADDITIONAL_PRICE)
            $shipping .= '&SHIPPING_ADDITIONAL_PRICE='.urlencode(SHIPPING_ADDITIONAL_PRICE);

        if($item) {
            $query .= 'SHOP_ID='.urlencode($default['de_naverpay_mid']);
            $query .= '&CERTI_KEY='.urlencode($default['de_naverpay_cert_key']);
            $query .= $shipping;
            $query .= '&BACK_URL='.urlencode(NAVERPAY_BACK_URL);
            $query .= '&NAVER_INFLOW_CODE='.urlencode($_COOKIE['NA_CO']);
            $query .= $item;
            $query .= '&TOTAL_PRICE='.$total;
        }

        return $query;
    }
}

function get_naverpay_item_image_url($gs_id)
{
	global $default;

	$sql = " select index_no, simg1, simg2, simg3, simg4, simg5 from shop_goods where index_no = '$gs_id' ";
	$row = sql_fetch($sql);

	if(!$row['index_no'])
		return '';

	$url = '';

	for($i=1; $i<=6; $i++) {
		$it_imageurl = get_it_image_url($row['index_no'], $row['simg'.$i], $default['de_item_medium_wpx'], $default['de_item_medium_hpx']);

		if($it_imageurl) {
			$url = $it_imageurl;

			if( isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ){
				$url = preg_replace('#^https:#', '', $url);
				
				$port_str = ':'.$_SERVER['SERVER_PORT'];
				
				if( strpos($url, $port_str) !== false ){
					$url = str_replace($port_str, '', $url);
				}
			}
			
			//TLS(SSL/HTTPS) 프로토콜 사용 시 네이버페이/네이버 쇼핑 서버가 해당 경로로 접근하여 데이터를 취득할 수 없으므로, 반드시 http 를 사용해야 함
			$url = (preg_match('#^http:#', $url) ? '' : 'http:').$url;

			break;
		}
	}

	return $url;
}

function get_naverpay_item_stock($gs_id)
{
	$sql = " select isopen, stock_qty, stock_mod from shop_goods where index_no = '$gs_id' ";
	$gs = sql_fetch($sql);

	if(($gs['stock_mod'] && $gs['stock_qty']==0) || $gs['isopen'] > 1)
		return 0;

	$jaego = get_it_stock_qty($gs_id);

	// 옵션체크
	$sql = " select count(io_no) as cnt, sum(io_stock_qty) as qty 
			   from shop_goods_option 
			  where gs_id = '$gs_id' 
				and io_type = '0' 
				and io_use = '1' ";
	$row = sql_fetch($sql);

	if($row['cnt'] > 0)
		return $row['qty'];
	else
		return $jaego;
}

function get_naverpay_item_option($gs_id, $subject)
{
	if(!$gs_id || !$subject)
		return '';

	$sql = " select * from shop_goods_option where io_type = '0' and gs_id = '$gs_id' and io_use = '1' order by io_no asc ";
	$result = sql_query($sql);
	if(!sql_num_rows($result))
		return '';

	$str = '';
	$subj = explode(',', $subject);
	$subj_count = count($subj);

	$option = '';

	if($subj_count > 1) {
		$options = array();

		// 옵션항목 배열에 저장
		for($i=0; $row=sql_fetch_array($result); $i++) {
			$osl_id = explode(chr(30), $row['io_id']);

			for($k=0; $k<$subj_count; $k++) {
				if(!is_array($options[$k]))
					$options[$k] = array();

				if($osl_id[$k] && !in_array($osl_id[$k], $options[$k]))
					$options[$k][] = $osl_id[$k];
			}
		}

		// 옵션선택목록 만들기
		for($i=0; $i<$subj_count; $i++) {
			$opt = $options[$i];
			$osl_count = count($opt);
			if($osl_count) {
				$option .= '<option name="'.get_text($subj[$i]).'">'.PHP_EOL;
				for($k=0; $k<$osl_count; $k++) {
					$osl_val = $opt[$k];
					if(strlen($osl_val)) {
						$option .= '<select><![CDATA['.$osl_val.']]></select>'.PHP_EOL;
					}
				}
				$option .= '</option>'.PHP_EOL;
			}
		}
	} else {
		$option .= '<option name="'.get_text($subj[0]).'">'.PHP_EOL;
		for($i=0; $row=sql_fetch_array($result); $i++) {
			$option .= '<select><![CDATA['.$row['io_id'].']]></select>'.PHP_EOL;
		}
		$option .= '</option>'.PHP_EOL;
	}

	return '<options>'.$option.'</options>';
}

function get_naverpay_return_info($mb_id)
{
	global $config;

	$data = '';
	$address1 = trim($config['company_addr']);
	$address2 = ' ';

	$data .= '<returnInfo>';
	$data .= '<zipcode><![CDATA['.$config['company_zip'].']]></zipcode>';
	$data .= '<address1><![CDATA['.$address1.']]></address1>';
	$data .= '<address2><![CDATA['.$address2.']]></address2>';
	$data .= '<sellername><![CDATA['.$config['company_name'].']]></sellername>';
	$data .= '<contact1><![CDATA['.$config['company_tel'].']]></contact1>';
	$data .= '</returnInfo>';

	return $data;
}

function return_error2json($str, $fld='error')
{
	$data = array();
	$data[$fld] = trim($str);

	die(json_encode($data));
}
?>