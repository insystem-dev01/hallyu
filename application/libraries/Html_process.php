<?php
class Html_process {
    protected $css;
    protected $js;
    protected $OM_TIME_YMDHIS;
    protected $OM_TIME_YMD;
    function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('Query_model');
        $this->css=array();
        $this->js=array();
        $this->OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $this->OM_TIME_YMD=substr($this->OM_TIME_YMDHIS,0,10);
    }
   

    function merge_stylesheet($stylesheet, $order){
        $links = $this->css;
        $is_merge = true;

        foreach($links as $link) {
            if($link[1] == $stylesheet) {
                $is_merge = false;
                break;
            }
        }

        if($is_merge)
            $this->css[] = array($order, $stylesheet);
    }

    function merge_javascript($javascript, $order){
        $scripts = $this->js;
        $is_merge = true;

        foreach($scripts as $script) {
            if($script[1] == $javascript) {
                $is_merge = false;
                break;
            }
        }

        if($is_merge)
            $this->js[] = array($order, $javascript);
    }

    function run(){
        $member=$this->ci->load->get_var('member');
        $tb=$this->ci->load->get_var('tb');
        $OM_TIME_YMDHIS=$this->OM_TIME_YMDHIS;
        $OM_SERVER_TIME=time();

        // 현재접속자 처리
        $tmp_sql = " select count(*) as cnt from shop_login where lo_ip = '{$_SERVER['REMOTE_ADDR']}' ";
        $tmp_row = $this->ci->Query_model->returnOneArr($tmp_sql);

        if($tmp_row['cnt']) {
            $tmp_sql = " update shop_login set mb_id = '{$member['id']}', lo_datetime = '".$OM_TIME_YMDHIS."', lo_location = '{$tb['lo_location']}', lo_url = '{$tb['lo_url']}' where lo_ip = '{$_SERVER['REMOTE_ADDR']}' ";
            $this->ci->Query_model->returnNull($tmp_sql);
        } else {
            $tmp_sql = " insert into shop_login ( lo_ip, mb_id, lo_datetime, lo_location, lo_url ) values ( '{$_SERVER['REMOTE_ADDR']}', '{$member['id']}', '".$OM_TIME_YMDHIS."', '{$tb['lo_location']}',  '{$tb['lo_url']}' ) ";
            $this->ci->Query_model->returnNull($tmp_sql);

            // 시간이 지난 접속은 삭제한다
            $this->ci->Query_model->returnNull(" delete from shop_login where lo_datetime < '".date("Y-m-d H:i:s", $OM_SERVER_TIME - (60 * 10))."' ");

            // 부담(overhead)이 있다면 테이블 최적화
            //$row = sql_fetch(" SHOW TABLE STATUS FROM `$mysql_db` LIKE '$tb['login_table']' ");
            //if($row['Data_free'] > 0) sql_query(" OPTIMIZE TABLE $tb['login_table'] ");
        }

        $buffer = ob_get_contents();
        ob_end_clean();

        $stylesheet = '';
        $links = $this->css;

        if(!empty($links)) {
            foreach ($links as $key => $row) {
                $order[$key] = $row[0];
                $index[$key] = $key;
                $style[$key] = $row[1];
            }

            array_multisort($order, SORT_ASC, $index, SORT_ASC, $links);

            foreach($links as $link) {
                if(!trim($link[1]))
                    continue;

                $link[1] = preg_replace('#\.css([\'\"]?>)$#i', '.css?ver='.time().'$1', $link[1]);

                $stylesheet .= $link[1];
            }
        }

        $javascript = '';
        $scripts = $this->js;
        $php_eol = '';

        unset($order);
        unset($index);

        if(!empty($scripts)) {
            foreach ($scripts as $key => $row) {
                $order[$key] = $row[0];
                $index[$key] = $key;
                $script[$key] = $row[1];
            }

            array_multisort($order, SORT_ASC, $index, SORT_ASC, $scripts);

            foreach($scripts as $js) {
                if(!trim($js[1]))
                    continue;

                $js[1] = preg_replace('#\.js([\'\"]?>)$#i', '.js?ver='.time().'$1', $js[1]);

                $javascript .= $php_eol.$js[1];
            }
        }

        /*
        </title>
        <link rel="stylesheet" href="default.css">
        밑으로 스킨의 스타일시트가 위치하도록 하게 한다.
        */
        $buffer = preg_replace('#(</title>[^<]*<link[^>]+>)#', "$1$stylesheet", $buffer);

        /*
        </head>
        <body>
        전에 스킨의 자바스크립트가 위치하도록 하게 한다.
        */
        $nl = '';
        if($javascript)
            $nl = "\n";
        $buffer = preg_replace('#(</head>[^<]*<body[^>]*>)#', "$javascript{$nl}$1", $buffer);

        return $buffer;
    }
}

?>