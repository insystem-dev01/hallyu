<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_lib {
    protected $ci;
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('Query_model');
        // Do something with $params
    }
	// 카테고리 페이지경로
	function get_move($ca_id){
		$str = "";
		$len = strlen($ca_id);
		for($i=1;$i<=($len/3);$i++) {
			$cut_id = substr($ca_id,0,($i*3));
            $sql="select * from shop_category where catecode=?";
            $data=array($cut_id);
            $row=$this->ci->Query_model->returnOneArr($sql,$data);
			$href = base_url().'shop/cateList?ca_id='.$row['catecode'];

			$str = $str." <i class=\"ionicons ion-ios-arrow-right\"></i> "."<a href='{$href}'>{$row['catename']}</a>";
		}

		return $str;
	}
	// 회원 총 주문수
	function shop_count($mb_id){
		if(!$mb_id) return 0;

		$sql="select count(*) as cnt from shop_order where mb_id = ? and dan IN(1,2,3,4,5,8) ";
        $data=array($mb_id);
		$row=$this->ci->Query_model->returnOneArr($sql,$data);
		return (int)$row['cnt'];
	}
	// 회원 총 주문액
	function shop_price($mb_id){
		if(!$mb_id) return 0;

		$sql = "select SUM(goods_price + baesong_price) as price from shop_order where mb_id = ? and dan IN(1,2,3,4,5,8) ";
        $data=array($mb_id);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);

		return (int)$row['price'];
	}
	// 승인완료 검사
	function admRequest($table, $add_query=''){
		$filed="";
		if($table == 'shop_goods1') {
			$table = "shop_goods";
			$filed = "shop_state";
			$value = '1';
		} else if($table == 'shop_goods2') {
			$table = "shop_goods";
			$filed = "shop_state";
			$value = '2';
		} else if($table == 'shop_goods_qa') {
			$filed = "iq_reply";
			$value = '0';
		} else {
			$filed = "state";
			$value = '0';
		}

		$sql = "select count(*) as cnt from {$table} where {$filed} =  ? {$add_query}";
        $data=array($value);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);

		return (int)$row['cnt'];
	}
	function sel_count($table, $where){
        $sql="select count(*) as cnt from {$table} {$where}";
        $row=$this->ci->Query_model->returnOneArr($sql);
		return (int)$row['cnt'];
	}
	// 주문상태에 따른 합계 금액
	function admin_order_status_sum($where){
		$sql = " select od_id from shop_order {$where} group by od_id ";
        $res=$this->ci->Query_model->returnQueryObj($sql);
		
		$od_count = $res->num_rows();

		$sql = " select SUM(goods_price + baesong_price) as price from shop_order {$where} ";
        $row=$this->ci->Query_model->returnOneArr($sql);
		$od_price = (int)$row['price'];

		$info = array();
		$info['cnt']   = $od_count;
		$info['price'] = $od_price;

		return $info;
	}
	// 가맹점 주문합계
	function partner_order_status_sum($pt_id, $sql_search=''){
		$sql = " select od_id from shop_order where pt_id = ? and dan IN(1,2,3,4,5,8) {$sql_search} group by od_id ";
        $data=array($pt_id);
        $res=$this->ci->Query_model->returnQueryObj($sql,$data);
		$od_count=$res->num_rows();

		$sql = "select SUM(goods_price + baesong_price) as price from shop_order where pt_id = ? and dan IN(1,2,3,4,5,8) {$sql_search}";
		$row=$this->ci->Query_model->returnOneArr($sql,$data);
		$od_price = (int)$row['price'];

		$info = array();
		$info['cnt']   = $od_count;
		$info['price'] = $od_price;

		return $info;
	}
	// 총 재고부족 상품
	function admin_gs_jaego_bujog($add_query=''){
		$sql = " select count(*) as cnt from shop_goods where stock_qty <= noti_qty and stock_mod = 1 and opt_subject = '' {$add_query} ";
		$row=$this->ci->Query_model->returnOneArr($sql);
		return (int)$row['cnt'];
	}
	// 총 옵션재고부족 상품
	function admin_io_jaego_bujog($add_query=''){
		$sql=" select count(*) as cnt from shop_goods_option a left join shop_goods b on (a.gs_id=b.index_no) where a.io_use = 1 and a.io_noti_qty <> '999999999' 
				and a.io_stock_qty <= a.io_noti_qty {$add_query}";
        $row=$this->ci->Query_model->returnOneArr($sql);
		return (int)$row['cnt'];
	}
	// 총 주문 관리자메모
	function admin_order_memo($add_query=''){
		$sql = " select od_id from shop_order where shop_memo <> '' {$add_query} group by od_id ";
        $res=$this->ci->Query_model->returnQueryObj($sql);
		return $res->num_rows();
	}
	// 총 상품평점 수
	function admin_goods_review($add_query=''){
		$sql="select count(*) as cnt from shop_goods_review where 1 {$add_query}";
        $row=$this->ci->Query_model->returnOneArr($sql);
		return (int)$row['cnt'];
	}
	// 상품 브랜드명 정보의 배열을 리턴
	function get_brand_chk($br_name, $mb_id=''){
		$sql_search  = " and ( br_user_yes = '0' ";
		if($mb_id) $sql_search .= " or (br_user_yes='1' and mb_id=TRIM('$mb_id')) ";
		$sql_search .= " ) ";

        $sql="select br_id from shop_brand where br_name=TRIM(?) {$sql_search}";
        $data=array($br_name);
		$row=$this->ci->Query_model->returnOneArr($sql,$data);
		if($row['br_id'])
			return $row['br_id'];
		else
			return '';
	}
	// 분류를 navigation 형식으로 얻음
	function adm_category_navi($ca_id){
		if(!$ca_id) return '';

		$str = '';
		for($i=1; $i<=(strlen($ca_id)/3); $i++) {
			$cut_id = substr($ca_id,0,($i*3));
            $sql="select catename from shop_category where catecode=?";
            $data=array($cut_id);
            $row=$this->ci->Query_model->returnOneArr($sql,$data);
			if($row['catename']) {
				$arr[] = $row['catename'];
			}
		}

		if(isset($arr)&&is_array($arr)) {
			$str = implode(" &gt; ", $arr);
		}

		return $str;
	}
	// 카테고리를 SELECT 형식으로 얻음 (본사, 입점사 공통)
	function get_goods_sca_select($name, $selected='', $event=''){
		$str = "<select id=\"{$name}\" name=\"{$name}\"";
		if($event) $str .= " $event";
		$str .= ">\n";
		$str .= "<option value=''>선택</option>\n";

		$sql_common = " from shop_category ";
		$sql_order  = " order by caterank, catecode ";
        $sql="select * {$sql_common} where upcate='' and catecode != 999 {$sql_order} ";
		$r = $this->ci->Query_model->returnArr($sql);
        foreach($r as $key => $row){
            $str .= "<option value='$row[catecode]'";
			if($row['catecode'] == $selected)
				$str .= " selected";
			$str .= ">[1]$row[catename]</option>\n";
            $sql2="select * {$sql_common} where upcate=? {$sql_order}";
            $data2=array($row['catecode']);
            $r2=$this->ci->Query_model->returnArr($sql2,$data2);
            foreach($r2 as $key2 => $row2){
                $len = strlen($row2['catecode']) / 3 - 1;
				$nbsp = "";
				for($i=0; $i<$len; $i++) {
					$nbsp .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				$str .= "<option value='$row2[catecode]'";
				if($row2['catecode'] == $selected)
					$str .= " selected";
				$str .= ">{$nbsp}[2]$row2[catename]</option>\n";
                $sql3="select * {$sql_common} where upcate=? {$sql_order}";
                $data3=array($row2['catecode']);
                $r3=$this->ci->Query_model->returnArr($sql3,$data3);
                foreach($r3 as $key3 => $row3){
                    $len = strlen($row3['catecode']) / 3 - 1;
					$nbsp = "";
					for($i=0; $i<$len; $i++) {
						$nbsp .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}
					$str .= "<option value='$row3[catecode]'";
					if($row3['catecode'] == $selected)
						$str .= " selected";
					$str .= ">{$nbsp}[3]$row3[catename]</option>\n";
                    $sql4="select * {$sql_common} where upcate=? {$sql_order}";
                    $data4=array($row3['catecode']);
                    $r4 = $this->ci->Query_model->returnArr($sql4,$data4);
                    foreach($r4 as $key4 => $row4){
                        $len = strlen($row4['catecode']) / 3 - 1;
						$nbsp = "";
						for($i=0; $i<$len; $i++) {
							$nbsp .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						}
						$str .= "<option value='$row4[catecode]'";
						if($row4['catecode'] == $selected)
							$str .= " selected";
						$str .= ">{$nbsp}[4]$row4[catename]</option>\n";
                        $sql5 = "select * {$sql_common} where upcate=? {$sql_order}";
                        $data5=array($row4['catecode']);
                        $r5=$this->ci->Query_model->returnArr($sql5,$data5);
                        foreach($r5 as $key5 => $row5){
                            $len = strlen($row5['catecode']) / 3 - 1;
							$nbsp = "";
							for($i=0; $i<$len; $i++) {
								$nbsp .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							}
							$str .= "<option value='$row5[catecode]'";
							if($row5['catecode'] == $selected)
								$str .= " selected";
							$str .= ">{$nbsp}[5]$row5[catename]</option>\n";
                        }//5
                    }//4
                }//3
            }//2
        }//1
		$str .= "</select>\n";
		return $str;
	}
	// 회원권한을 SELECT 형식으로 얻음
	function get_level_select($name, $start_id=1, $end_id=9, $selected='', $event=''){
		$str  = "<select id=\"{$name}\" name=\"{$name}\"";
		if($event) $str .= " $event";
		$str .= ">\n";

		$sql= "select * from shop_member where (grade>='$start_id' and grade<='$end_id') order by name";
        $data=array($start_id,$end_id);
        $result=$this->ci->Query_model->returnArr($sql,$data);
		foreach($result as $key => $row){
            $str .= "<option value='{$row['id']}'";
			if($row['id'] == $selected)
				$str .= " selected";
			$str .= ">{$row['name']} ({$row['id']})</option>\n";
        }
		$str .= "</select>\n";

		return $str;
	}
	// 회원권한을 SELECT 형식으로 얻음
	function get_member_select($name, $selected='', $event=''){
		$str  = "<select id=\"{$name}\" name=\"{$name}\"";
		if($event) $str .= " $event";
		$str .= ">\n";

		$sql= "select * from shop_member_grade where gb_name <> '' order by gb_no desc";
		$result = $this->ci->Query_model->returnArr($sql);
        foreach($result as $key => $row){
            $str .= "<option value='{$row['gb_no']}'";
			if($row['gb_no'] == $selected)
				$str .= " selected";
			$str .= ">[{$row['gb_no']}] {$row['gb_name']}</option>\n";
        }

		$str .= "</select>\n";

		return $str;
	}
	// 회원권한을 SELECT 형식으로 얻음
	function get_goods_level_select($name, $selected='', $event=''){
		$str  = "<select id=\"{$name}\" name=\"{$name}\"";
		if($event) $str .= " $event";
		$str .= ">\n";
		$str .= "<option value='99'>제한없음</option>\n";

		$sql= "select * from shop_member_grade where gb_name <> '' and gb_no > 1 order by gb_no desc ";
		$result = $this->ci->Query_model->returnArr($sql);
        foreach($result as $key => $row){
            $str .= "<option value='{$row['gb_no']}'";
			if($row['gb_no'] == $selected)
				$str .= " selected";
			$str .= ">[{$row['gb_no']}] {$row['gb_name']}</option>\n";
        }

		$str .= "</select>\n";

		return $str;
	}
	// 게시판 그룹을 SELECT 형식으로 얻음
	function get_group_select($name, $selected='', $event=''){
		$str  = "<select id=\"{$name}\" name=\"{$name}\"";
		if($event) $str .= " $event";
		$str .= ">\n";

		$sql = "select gr_id, gr_subject from shop_board_group order by gr_id desc ";
		$result = $this->ci->Query_model->returnArr($sql);
        foreach($result as $key => $row){
            $str .= "<option value='{$row['gr_id']}'";
			if($row['gr_id'] == $selected) $str .= " selected";
			$str .= ">{$row['gr_subject']}</option>\n";
        }
		$str .= "</select>\n";

		return $str;
	}
	// 카테고리번호 생성
	function get_ca_depth($tablename, $upcate){
		$sql_fld = " MAX(catecode) as max_caid ";

        $sql="select {$sql_fld} from {$tablename} where upcate = ? ";
        $data=array($upcate);
        $ca=$this->ci->Query_model->returnOneArr($sql,$data);
		$max_caid = $ca['max_caid'] + 1;

		if(strlen($max_caid)%3 == 1)
			$new_code = '00'.$max_caid;
		else if(strlen($max_caid)%3 == 2)
			$new_code = '0'.$max_caid;
		else 
			$new_code = $max_caid;
		
		$new_code = substr($new_code,-3);
		$new_code = $upcate.$new_code;

		return $new_code;
	}
	// 구매후기 작성가능 여부 확인
	function find_review_flag($od_no, $member_id){
		$sql = "select * from shop_order where dan = 5 and user_ok = 1 and review_flag = 1 and od_no = ? and mb_id = ?";
        $data=array($od_no,$member_id);
        $result=$this->ci->Query_model->returnQueryObj($sql,$data);
		$count=$result->num_rows();
		if ($count != 0)	return true;
		else return false;
	}
    //구매후기 작성필요 데이터
    function get_order_review_data($index_no, $member_id, $od_id){
        $sql = "SELECT index_no, od_id, gs_id, seller_name, review_flag, goods_price FROM shop_order WHERE dan = 5 AND review_flag = 1 AND gs_id = ? AND mb_id = ? AND od_id = ?";
        $data=array($index_no,$member_id,$od_id);
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }
###쿼리문 들어가 있는 함수 끝
}