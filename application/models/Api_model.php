<?php 

class Api_model extends CI_Model {
    function __construct() { 
        parent::__construct(); 
    }

    // 아이디 찾기
    public function findId($name,$cellphone) {
        $sql = "SELECT id, name, reg_time
                FROM shop_member
                WHERE name = ? AND cellphone = ?";
        
        $param = array($name, $cellphone);
        return $this->db->query($sql, $param)->row_array();
    }

    // 비밀번호 찾기
    public function findPwd($id,$name,$cellphone) {
        $sql = "SELECT id,name,reg_time
                FROM shop_member
                WHERE id = ? AND name = ? AND cellphone = ?";

        return $this->db->query($sql, array($id,$name, $cellphone))->row_array();
    }

    // 추천상품
    public function getRecommendGoods() {
        $sql = "SELECT a.index_no, a.gname
                FROM shop_goods AS a
                WHERE a.shop_state = '0' AND a.isopen IN ('1','2') AND find_in_set('admin', a.use_hide) = '0'
                ORDER BY RAND() LIMIT 6";

        return $this->db->query($sql)->result_array();
    }

    // 상품 검색
    public function searchGoods($sql_search) {
        $sql = "SELECT a.*
                FROM shop_goods a, shop_category b 
                WHERE a.ca_id = b.catecode AND a.shop_state = '0'
                AND a.isopen IN('1', '2') AND b.cateuse = '0' AND a.use_aff = '0'
                AND find_in_set('admin', a.use_hide) = '0' AND find_in_set('admin', b.catehide) = '0'
                $sql_search
                ORDER BY a.index_no DESC";

        return $this->db->query($sql)->result_array();
    }

    // 찜 확인
    public function getWishCnt($mb_id,$gs_id) {
        $sql = "SELECT count(*) AS cnt FROM shop_wish WHERE mb_id = ? AND gs_id = ?";
        $result = $this->db->query($sql,array($mb_id, $gs_id))->row_array();
        
        return $result['cnt'];
    }

    // 서브메뉴
    public function getSubMenu($sub) {
        $sql = "SELECT *
                FROM shop_category
                WHERE (upcate = ? OR catecode like ?) AND cateuse = 0 AND catecode != 999 AND find_in_set('admin', catehide) = 0
                ORDER BY index_no ASC";
                
        return $this->db->query($sql,array($sub, $sub))->result_array();
    }

    // 어플 알림개수 확인
    public function getAlarmCnt($mb_id) {
        $sql = "SELECT COUNT(*) AS cnt FROM shop_alarm WHERE alarm_to = ? ";
        $result = $this->db->query($sql, $mb_id)->row_array();
        
        return $result['cnt'];
    }

    // 알림 수신 여부 확인
    public function getIsAlarm($mb_id) {
        $sql = " SELECT is_alarm FROM shop_member WHERE id = ? ";
        $param = array('id' => $mb_id);
        $res = $this->db->query($sql, $mb_id)->row_array($sql, $param);

        return $res['is_alarm'];
    }

    // 알림 리스트
    public function getAlarmList($mb_id, $start, $limit) {
        $sql = "SELECT *
                FROM shop_alarm
                WHERE alarm_to = ?
                ORDER BY index_no DESC
                LIMIT ?, ?";

        return $this->db->query($sql, array($mb_id, $start, $limit))->result_array();
    }

    // 상품 조회수 업데이트
    public function updateGoodsCnt($index_no) {
        $sql = "UPDATE shop_goods
                SET readcount = readcount + 1
                WHERE index_no = ?";

        return $this->db->query($sql, $index_no);
    }

    public function getQnaOptions() {
        $sql = "SELECT * FROM shop_qa_cate WHERE isuse = 'Y'";

        return $this->db->query($sql)->result_array();
    }

    // 장바구니 리스트 가져오기
    public function getCartList($mb_id) {
        $sql = "SELECT a.*, b.isopen
                FROM shop_cart AS a
                RIGHT JOIN shop_goods AS b
                ON a.gs_id = b.index_no
                WHERE a.mb_id = ? AND a.ct_select = '0' AND b.isopen = '1' AND is_direct = 0
                ORDER BY a.index_no";
            
        return $this->db->query($sql, $mb_id)->result_array();
    }

    // 장바구니 합계
    public function getCartTotalPrice($mb_id) {
        $sql = "SELECT sum((ct_price + io_price) * ct_qty) AS total
                FROM shop_cart
                WHERE ct_select = '0' AND is_direct = 0 AND mb_id = ?";

        $result = $this->db->query($sql, $mb_id)->row_array();
        return $result['total'];
    }

    // 장바구니 있는지 확인 (여러개)
    public function getCartIdx($mb_id, $index_no) {
        $sql = "SELECT index_no
                FROM shop_cart
                WHERE mb_id = ? AND ct_select = '0' AND index_no IN ? AND is_direct = 0";

        return $this->db->query($sql, array($mb_id, $index_no))->result_array();
    }

    // 장바구니 있는지 확인 (1개)
    public function isCartEmpty($mb_id, $index_no) {
        $sql = "SELECT *
                FROM shop_cart
                WHERE mb_id = ? AND ct_select = '0' AND index_no = ?";

        return $this->db->query($sql, array($mb_id, $index_no))->row_array();
    }


    public function getCartData($mb_id,$index_no,$option) {
        $sql = "SELECT *
                FROM shop_cart
                WHERE mb_id = ? AND ct_select = '0' AND od_id <> '0' AND io_id = ? AND gs_id = ? AND is_direct = 'n'";

        return $this->db->query($sql,array($mb_id, $option, $index_no));
    }

    public function updateCart($total_qty, $mb_id, $gs_id, $index_no) {
        $sql = "UPDATE shop_cart
                SET ct_qty = ?
                WHERE mb_id = ? AND ct_select = '0' AND gs_id = ? AND index_no = ?";

        return $this->db->query($sql,array($total_qty, $mb_id, $gs_id, $index_no));
    }

    // 주문
    public function getCartOrderData($index_no, $is_direct=null) {
        $sql_where = "";
        if(isset($is_direct)) {
            $sql_where = "AND is_direct = '{$is_direct}'";
        }        
        $sql = "SELECT *
                FROM shop_cart
                WHERE index_no IN ? AND ct_select = '0' {$sql_where}
                ORDER BY index_no";
        $param = array($index_no);

        return $this->db->query($sql,$param)->result_array();
    }

    public function getCartDataOrderAction($index_no) {
        $sql = "SELECT *
                FROM shop_cart
                WHERE index_no IN ? AND ct_select = '0'
                ORDER BY index_no ";
        $param = array($index_no);

        return $this->db->query($sql,$param)->result_array();
    }

    // 주문금액 총액
    public function getOrderTotalPrice($index_no) {
        $sql = "SELECT sum((ct_price + io_price) * ct_qty) AS total
                FROM shop_cart
                WHERE index_no IN ? AND ct_select = '0' AND is_direct = 'n'";
        $param = array($index_no);

        $result = $this->db->query($sql,$param)->row_array();
        return $result['total'];
    }

    // 주문 상세
    public function getOrderDetailList($mb_id, $od_id) {
        $where = "";
        if($mb_id == "admin") {
            $where .= "a.od_id = {$od_id}";
        } else {
            $where .= "a.mb_id = '{$mb_id}' and a.od_id = '{$od_id}'";
        }
        $sql = "SELECT a.*, b.isopen,c.io_id,c.io_type,c.io_price,c.ct_option
                FROM shop_order AS a
                LEFT JOIN shop_goods AS b
                ON a.gs_id = b.index_no
                LEFT JOIN shop_cart AS c
                on a.od_no = c.od_no
                WHERE {$where} AND b.isopen = '1'
                ORDER BY a.index_no";

        $param = array($mb_id, $od_id);
        return $this->db->query($sql, $param)->result_array();
    }

    // 주문내역
    public function getOrderList($mb_id, $search, $st_date, $ed_date) {
        $sql_where = "";

        if($search) {
            $sql_where .= "AND b.gname LIKE '%{$search}%'";
        }

        if($st_date && $ed_date) {
            $sql_where .= "AND DATE_FORMAT(od_time,'%Y-%m') BETWEEN '{$st_date}' AND '{$ed_date}'";
        }

        $sql = "SELECT a.od_id,a.od_time,a.dan
                FROM shop_order AS a
                LEFT JOIN shop_goods AS b
                ON a.gs_id = b.index_no
                WHERE a.mb_id = ? AND a.dan <> 0
                {$sql_where}
                GROUP BY a.od_id
                ORDER BY a.index_no DESC";
            
        return $this->db->query($sql, $mb_id)->result_array();
    }

    // 주문내역 총 합계
    public function getOrderAllPrice($index_no, $mb_id) {
        $sql = "SELECT sum((ct_price + io_price) * ct_qty) AS total
                FROM shop_cart
                WHERE od_id = ? AND mb_id = ?";
        $param = array($index_no, $mb_id);

        $result = $this->db->query($sql, $param)->row_array();
        return $result['total'];
    }

    public function getPaymentData($mb_id, $od_id) {
        $sql = "SELECT *
                FROM shop_order
                WHERE mb_id = ? AND od_id = ?
                GROUP BY od_id
                ORDER BY index_no DESC";
        $param = array($mb_id, $od_id);
        
        return $this->db->query($sql, $param)->result_array();
    }

    // 주문상세 배송비 값 가져오기
    public function getDeliveryValue($od_id) {
        $sql = "SELECT sum(bsprice) as bsprice
                FROM (SELECT baesong_price AS bsprice FROM shop_order WHERE od_id = ? ) AS a
                WHERE a.bsprice <> '0' ";
        $param = array($od_id);
        
        return $this->db->query($sql, $param)->row_array();
    }

    public function getOrderTotal($od_id) {
        $sql = "SELECT SUM(goods_price) AS goodsprice
                FROM shop_order
                WHERE od_id = ?";
        $param = array($od_id);

        return $this->db->query($sql, $param)->row_array();
    }
    
    public function getCartDataId($od_id) {
        $sql = "SELECT *
                FROM shop_cart
                WHERE od_id = ?
                ORDER BY io_type ASC, index_no ASC";
        $param = array($od_id);

        return $this->db->query($sql,$param)->result_array();
    }

    // 알림 여부 확인
    public function alarmUpdate($mb_id, $is_alarm) {
        $sql = "UPDATE shop_member
                SET is_alarm = ?
                WHERE id = ?";

        return $this->db->query($sql, array($is_alarm, $mb_id));
    }

    // 공지사항 글 개수
    public function getBoardCnt($boardid) {
        $sql = "SELECT count(*) AS cnt FROM shop_board_{$boardid}";
        $result = $this->db->query($sql)->row_array();

        return $result['cnt'];
    }

    // 공지 리스트
    public function getBoardList($boardid, $start, $limit) {
        $sql = "SELECT *
                FROM shop_board_{$boardid}
                ORDER BY fid DESC, thread ASC
                LIMIT ?, ?";

        return $this->db->query($sql, array($start, $limit))->result_array();
    }

    // 공지 상세
    public function getBoardDetail($boardid, $index_no) {
        $sql = "SELECT * FROM shop_board_{$boardid} WHERE index_no = ?";

        return $this->db->query($sql, $index_no)->row_array();
    }

    // 상품 qna cnt
    public function getQnaCnt($gs_id) {
        $sql = "SELECT count(*) AS cnt FROM shop_goods_qa WHERE gs_id = ?";
        $result = $this->db->query($sql, array($gs_id))->row_array();

        return $result['cnt'];
    }

    // 마이페이지 상품 qna cnt
    public function getMyQnaCnt($mb_id) {
        $sql = "SELECT count(*) AS cnt FROM shop_goods_qa WHERE mb_id = ?";
        $result = $this->db->query($sql, array($mb_id))->row_array();

        return $result['cnt'];
    }

    // 상품 qna 리스트
    public function getQnaList($gs_id, $start, $limit, $where_sql = null) {
        $sql = "SELECT *
                FROM shop_goods_qa
                WHERE gs_id = ? {$where_sql}
                ORDER BY iq_time DESC
                LIMIT ?, ?";

        return $this->db->query($sql, array($gs_id, $start, $limit))->result_array();
    }

    // 마이페이지 상품 qna 리스트
    public function getMyQnaList($mb_id, $start, $limit) {
        $sql = "SELECT *
                FROM shop_goods_qa
                WHERE mb_id = ?
                ORDER BY iq_time DESC
                LIMIT ?, ?";

        return $this->db->query($sql, array($mb_id, $start, $limit))->result_array();
    }

    public function getMyQnaInfo($mb_id, $index_no) {
        $sql = "SELECT *
                FROM shop_goods_qa
                WHERE mb_id = ? AND iq_id = ?
                ORDER BY iq_time DESC
                LIMIT ?, ?";

        return $this->db->query($sql, array($mb_id, $index_no))->result_array();
    }

    // 찜한 상품
    public function getZzim($mb_id) {        
        $sql = "SELECT a.wi_id, a.wi_time, a.gs_id, b.* 
                FROM shop_wish a LEFT JOIN shop_goods b ON ( a.gs_id = b.index_no )
                WHERE a.mb_id = ?
                ORDER BY a.wi_id DESC";

        return $this->db->query($sql, array($mb_id))->result_array();
    }

    // 작성한 리뷰
    public function getCompelteReview($mb_id) {
        $sql = "SELECT a.index_no, b.gname, c.receipt_time, c.use_price, c.od_no
                FROM shop_goods_review AS a
                LEFT JOIN shop_goods AS b
                ON a.gs_id = b.index_no
                LEFT JOIN shop_order AS c
                ON a.od_index = c.index_no
                WHERE a.mb_id = ?
                ORDER BY a.index_no DESC";

        return $this->db->query($sql, array($mb_id))->result_array();
    }

    // 작성 가능한 리뷰
    public function getPossibleReview($mb_id) {
        $sql = "SELECT a.receipt_time, a.use_price, a.index_no, a.od_no, b.gname
                FROM shop_order AS a
                LEFT JOIN shop_goods AS b
                ON a.gs_id = b.index_no
                WHERE a.mb_id = ?
                and a.user_ok = 1 AND a.review_flag = 1";

        return $this->db->query($sql, array($mb_id))->result_array();
    }

    public function alreadyReview($od_no) {
        $sql = "SELECT count(*) AS cnt
                FROM shop_order
                WHERE user_ok = 1 AND review_flag = 0 AND od_no = ?";

        return $this->db->query($sql, array($od_no))->row_array();
    }

    // 리뷰 보기
    public function getReview($mb_id, $index_no) {
        $sql = "SELECT a.index_no, a.score, a.memo, a.gs_id, a.img1, a.img2, a.img3, a.img4, b.gname, b.simg1, c.use_price
                FROM shop_goods_review AS a
                LEFT JOIN shop_goods AS b
                ON a.gs_id = b.index_no
                LEFT JOIN shop_order AS c
                ON a.od_no = c.od_no
                WHERE a.mb_id = ? AND a.index_no = ?";

        return $this->db->query($sql, array($mb_id, $index_no))->row_array();
    }

}