<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MypageProcess extends CI_Controller{
    private $max_size;
    private $max_width;
    private $max_height;
    private $remove_spaces;
    private $encrypt_name;
    private $allowed_types;

    public function __construct(){
        parent::__construct();
        $this->load->model('query_model');
        $this->load->helper('common_helper');
        $this->load->helper('global_helper');
        $this->load->helper('register_helper');
        $this->load->library('funcomplex_lib');
        $this->load->library('gvcomplex_lib');
        $this->load->library('global_lib');
        $this->load->library('register_lib');

        $this->allowed_types = 'jpg|jpeg|bmp|png|gif';
        $this->max_size = '2048';
        $this->max_width= '5000';
        $this->max_height= '5000';
        $this->remove_spaces = TRUE;
        $this->encrypt_name= TRUE;
    }
    public function seller_info_update(){
        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $member=$this->load->get_var('member');
        unset($value);
        $value['seller_item']			= $this->input->post('seller_item'); //제공상품
        $value['company_name']			= $this->input->post('company_name'); //업체(법인)명
        $value['company_owner']			= $this->input->post('company_owner'); //대표자명
        $value['company_saupja_no']		= $this->input->post('company_saupja_no');//사업자등록번호
        $value['company_item']			= $this->input->post('company_item'); //업태
        $value['company_service']		= $this->input->post('company_service'); //종목
        $value['company_tel']			= $this->input->post('company_tel'); //전화번호
        $value['company_fax']			= $this->input->post('company_fax'); //팩스번호
        $value['company_zip']			= $this->input->post('company_zip'); //우편번호
        $value['company_addr1']			= $this->input->post('company_addr1'); //기본주소
        $value['company_addr2']			= $this->input->post('company_addr2'); //상세주소
        $value['company_addr3']			= $this->input->post('company_addr3'); //참고항목
        $value['company_addr_jibeon']	= $this->input->post('company_addr_jibeon'); //지번
        $value['company_hompage']		= $this->input->post('company_hompage'); //홈페이지
        $value['bank_name']				= $this->input->post('bank_name'); //은행명
        $value['bank_account']			= $this->input->post('bank_account'); //계좌번호
        $value['bank_holder']			= $this->input->post('bank_holder'); //예금주명
        $value['info_name']				= $this->input->post('info_name'); //담당자명
        $value['info_tel']				= $this->input->post('info_tel'); //담당자 핸드폰
        $value['info_email']			= $this->input->post('info_email'); //담당자 이메일
        $value['update_time']			= $OM_TIME_YMDHIS;
        $this->query_model->updateObj("shop_seller",$value,"mb_id='$member[id]'");

        goto_url(base_url().'mypage/page?code=seller_info');
    }
    public function seller_baesong_update(){
        $member=$this->load->get_var('member');
        unset($value);
        $value['delivery_method']	= $this->input->post('delivery_method');
        $value['delivery_price']	= conv_number($this->input->post('delivery_price'));
        $value['delivery_price2']	= conv_number($this->input->post('delivery_price2'));
        $value['delivery_minimum']	= conv_number($this->input->post('delivery_minimum'));
        $value['baesong_cont1']		= $this->input->post('baesong_cont1');
        $value['baesong_cont2']		= $this->input->post('baesong_cont2');
        $this->query_model->updateObj("shop_seller",$value,"mb_id='$member[id]'");

        goto_url(base_url().'mypage/page?code=seller_baesong');
    }
    public function seller_goods_form_update(){
        try {
            $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
            $q1 = $this->input->post('q1');
            $page =$this->input->post('page');
            $bak = $this->input->post('bak');
            $gs_id = $this->input->post('gs_id');
            $gs_list = $this->input->post('gs_list');
            $config = $this->load->get_var('config');
            $it_option_subject = $this->input->post('it_option_subject');
            $it_supply_subject = $this->input->post('it_supply_subject');
            $w = $this->input->post('w');

            $type = $this->input->post('type') ?? "normal";
            
            $uploadPath="./data/goods";
            $configArr = array();
            $configArr['upload_path'] = $uploadPath;
    
            $configArr['allowed_types'] = $this->allowed_types;
            $configArr['max_size'] = $this->max_size;
            $configArr['max_width'] = $this->max_width;
            $configArr['max_height'] = $this->max_height;
            $configArr['remove_spaces'] = $this->remove_spaces;
            $configArr['encrypt_name'] = $this->encrypt_name;
            $this->load->library('upload', $configArr);
            $this->upload->initialize($configArr);
    
            $opt_id=$this->input->post('opt_id');
            $spl_id=$this->input->post('spl_id');

            unset($value);

            if($w == "u") {
                $gs = $this->global_lib->get_goods($gs_id);

                if($gs['shop_state'] == 0 && $type == "timesale") {
                    throw new Exception("함께장터 상품은 최종관리자 승인 이후 수정이 불가능합니다.");
                }
            }

            if($_POST['gname'] == "") {
                throw new Exception("상품명을 입력하세요.");
            }
    
            if($_POST['ca_id'] == "") {
                throw new Exception("카테고리를 하나이상 선택하세요.");
            }
    
            // 관련상품을 우선 삭제함
            $sql="DELETE FROM shop_goods_relation WHERE gs_id = ?";
            $param=array($gs_id);
            $this->query_model->returnNull($sql,$param);
    
            // 관련상품의 반대도 삭제
            $sql2="DELETE FROM shop_goods_relation WHERE gs_id2 = ?";
            $this->query_model->returnNull($sql2,$param);
    
            // 관련상품 등록
            $gs_id2 = explode(",", $gs_list);
            for($i=0; $i<count($gs_id2); $i++){
                if(trim($gs_id2[$i])){
                    $sql = "INSERT INTO shop_goods_relation
                            SET gs_id  = ?,
                                gs_id2 = ?,
                                ir_no = ?";
                    $param=array($gs_id,$gs_id2[$i],$i);
                    $this->query_model->returnNull($sql,$param);
    
                    // 관련상품의 반대로도 등록
                    $sql = "INSERT INTO shop_goods_relation
                            SET gs_id  = ?,
                                gs_id2 = ?,
                                ir_no = ? ";
                    $param2=array($gs_id2[$i],$gs_id,$i);
                    $this->query_model->returnNull($sql,$param2);
                }
            }
    
            // 기존 선택옵션삭제
            $sql3="delete from shop_goods_option where io_type = '0' and gs_id = ?";
            $this->query_model->returnNull($sql3,$param);
            $option_count = isset($opt_id)?count($opt_id):0;
            if($option_count) {
                // 옵션명
                $opt1_cnt = $opt2_cnt = $opt3_cnt = 0;
                for($i=0; $i<$option_count; $i++) {
                    $opt_val = explode(' ', $opt_id[$i]);
                    if(isset($opt_val[0])) {
                        $opt1_subject = $this->input->post('opt1_subject');
                        $opt1_cnt++;
                    } if(isset($opt_val[1])) {
                        $opt2_subject = $this->input->post('opt2_subject');
                        $opt2_cnt++;
                    } if(isset($opt_val[2])) {
                        $opt3_subject = $this->input->post('opt3_subject');
                        $opt3_cnt++;
                    }
                }

    
                if(isset($opt1_subject) && $opt1_cnt > 0) {
                    $it_option_subject = $opt1_subject;
                    if(isset($opt2_subject) && $opt2_cnt > 0)
                        $it_option_subject .= ','.$opt2_subject;
                    if(isset($opt3_subject) && $opt3_cnt > 0)
                        $it_option_subject .= ','.$opt3_subject;
                }
            }
    
            // 기존 추가옵션삭제
            $sql="delete from shop_goods_option where io_type = '1' and gs_id = ?";
            $param=array($gs_id);
            $this->query_model->returnNull($sql,$param);
    
            $supply_count = isset($spl_id)?count($spl_id):0;
            if($supply_count) {
                // 추가옵션명
                $arr_spl = array();
                for($i=0; $i<$supply_count; $i++) {
                    $spl_val = explode(' ', $_POST['spl_id'][$i]);
                    if(!in_array($spl_val[0], $arr_spl))
                        $arr_spl[] = $spl_val[0];
                }
    
                $it_supply_subject = implode(',', $arr_spl);
            }
    
            // 상품 정보제공
            $value_array = array();
            $ii_article=$this->input->post('ii_article');
            $ii_value=$this->input->post('ii_value');
            $ii_articleCnt=isset($ii_article)?count($ii_article):0;
            for($i=0; $i<$ii_articleCnt; $i++) {
                $key = $ii_article[$i];
                $val = $ii_value[$i];
                $value_array[$key] = $val;
            }
            $it_info_value = addslashes(serialize($value_array));
    
            unset($value);
            if($_POST['simg_type']) { // URL 입력
                if($w == "") {
                    if (empty($_POST['simg1']) || empty($_POST['simg2'])) {
                        throw new Exception("상품 이미지1, 이미지2를 등록해주세요.");
                    }
                }

                $value['simg1'] = $this->input->post('simg1');
                $value['simg2'] = $this->input->post('simg2');
                $value['simg3'] = $this->input->post('simg3');
                $value['simg4'] = $this->input->post('simg4');
                $value['simg5'] = $this->input->post('simg5');
                $value['simg6'] = $this->input->post('simg6');
            } else {
                if ($w == "") {
                    if (empty($_FILES['simg1']['name']) || empty($_FILES['simg2']['name'])) {
                        throw new Exception("상품 이미지1, 이미지2를 등록해주세요.");
                    }
                }
            
                for ($i = 1; $i <= 6; $i++) {
                    if ($img = $_FILES['simg' . $i]['name']) {
                        if (!preg_match("/\.(gif|jpg|png)$/i", $img)) {
                            throw new Exception("이미지 {$i}는 gif, jpg, png 파일이 아닙니다.");
                        }
                    }
            
                    if (isset($_POST['simg' . $i . '_del'])) {
                        unlink($uploadPath . '/' . $_POST['simg' . $i . '_del']);
                        $value['simg' . $i] = '';
                    }
            
                    if (!empty($_FILES['simg' . $i]['name'])) {
                        if (!$this->upload->do_upload('simg' . $i)) {
                            $error = $this->upload->display_errors();
                            // 업로드 실패 시 simg 값 설정하지 않음
                        } else {
                            $uploadFile = $this->upload->data();
                            $new_file = $uploadFile['file_name'];
                            $value['simg' . $i] = $new_file;
                        }
                    }
                }
            }

            // 수입필증 / 계약서 시작 --------
            $uploadPath="./data/document/";
            $configArr = array();
            $configArr['upload_path'] = $uploadPath;
            
            $configArr['allowed_types'] = 'jpg|jpeg|png|pdf';
            $configArr['max_size'] = $this->max_size;
            $configArr['max_width'] = $this->max_width;
            $configArr['max_height'] = $this->max_height;
            $configArr['remove_spaces'] = $this->remove_spaces;
            $configArr['encrypt_name'] = $this->encrypt_name;

            $this->load->library('upload', $configArr);
            $this->upload->initialize($configArr);

            // 파일이 있으면 파일을 업로드함
            $uploadFile = array('document'=>'');
            $error = "";

            if(isset($_FILES['document']['name']) && $_FILES['document']['name']!="") {
                if(!$this->upload->do_upload('document')){
                    $error = $this->upload->display_errors();
                }else{
                    $uploadFile['document'] = $this->upload->data();
                    $document = $uploadFile['document']['file_name'];
                    $value['document'] = $document;
                }
            }

            if($error != ""){
                throw new Exception($error);
            }
            // 수입필증 / 계약서 끝 --------

            $sc_each_use=$this->input->post('sc_each_use');
            $buy_only=$this->input->post('buy_only');

            $normal_price = conv_number($this->input->post('normal_price'));
            if($type == "timesale" && empty($normal_price)) {
                throw new Exception("정가를 입력해주세요");
            }

            $goods_price = conv_number($this->input->post('goods_price'));
            if(empty($goods_price)) {
                throw new Exception("판매가격을 입력해주세요");
            }

            $calculate_price = conv_number($this->input->post('calculate_price'));
            if($type == "timesale" && empty($calculate_price)) {
                throw new Exception("실제 판매가를 입력해주세요");
            }

            if($type == "timesale" && $goods_price > $normal_price) {
                throw new Exception("판매가격은 정가보다 작아야합니다.");
            }

            $supply_price = conv_number($this->input->post('supply_price'));
            if ($type == "normal" && $supply_price > $goods_price) {
                throw new Exception("판매가는 공급 가격보다 높아야 합니다.");
            }

            $stock_qty = conv_number($this->input->post('stock_qty'));
            if($type == "timesale" && empty($stock_qty)) {
                throw new Exception("수량을 입력해주세요");
            }

            //할인율 검증
            $this->validate_min_discount($goods_price, $calculate_price, $stock_qty);

            if($type == "timesale" && $stock_qty < 2) {
                throw new Exception("수량은 2개 이상이어야 합니다.");
            }

            if($type == "timesale" && $stock_qty % 2 != 0) {
                throw new Exception("수량은 짝수로 입력해주세요.");
            }

            if ($calculate_price <= 10000 && $stock_qty > 30) {
                throw new Exception("저가 상품은 수량 30개 이내로만 등록 가능합니다. 현재 수량: {$stock_qty}");
            }

            $goods_total_price = conv_number($this->input->post('goods_total_price')) ?? "0";
            if($type == "timesale" && empty($goods_total_price)) {
                throw new Exception("총 판매금액을 입력해주세요");
            }

            $sb_date=$this->input->post('sb_date');
            $eb_date=$this->input->post('eb_date');
            if ($type === 'timesale' && (empty($sb_date) || empty($eb_date))) {
                throw new Exception("판매기간을 설정해주세요.");
            }
            
            $memo = $this->input->post('memo');
            if(empty($memo)) {
                throw new Exception("상품 상세 설명을 입력해주세요.");
            }

            $value['type']		        = $type; // 상품 타입 (일반, bid)
            $value['use_aff']		    = 0; //본사상품으로 설정
            $value['ca_id']			    = $this->input->post('ca_id') ? $this->input->post('ca_id') : "999"; //대표카테고리
            $value['ca_id2']		    = $this->input->post('ca_id2'); //추가카테고리2
            $value['ca_id3']		    = $this->input->post('ca_id3'); //추가카테고리3
            $value['mb_id']			    = $this->input->post('mb_id'); //업체코드
            $value['mb_name']           = $this->input->post('mb_name'); // 협력사명
            $value['gname']			    = $this->input->post('gname'); //상품명
            $value['isopen']		    = $this->input->post('isopen'); //진열상태
            $value['explan']		    = $this->input->post('explan'); //짧은설명
            $value['keywords']		    = $this->input->post('keywords'); //키워드
            $value['admin_memo']	    = $this->input->post('admin_memo'); //관리자메모
            $value['memo']			    = $memo; //상품설명
            $value['goods_price']	    = $goods_price; //판매가격
            $value['supply_price']	    = $supply_price; //공급가격
            $value['normal_price']	    = $normal_price; //시중가격
            $value['calculate_price']   = $calculate_price; //실제 판매가(타임세일)
            $value['gpoint']		    = 0;
            $value['maker']			    = $this->input->post('maker'); //제조사
            $value['origin']		    = $this->input->post('origin'); //원산지
            $value['model']			    = $this->input->post('model'); //모델명
            $value['opt_subject']	    = isset($it_option_subject)?$it_option_subject:""; //상품 선택옵션
            $value['spl_subject']	    = isset($it_supply_subject)?$it_supply_subject:""; //상품 추가옵션
            $value['stock_qty']		    = $stock_qty; //재고수량
            $value['original_stock_qty'] = $stock_qty; //재고수량
            $value['goods_total_price'] = $goods_total_price;
            $value['noti_qty']		    = conv_number($this->input->post('noti_qty')); //재고 통보수량
            $value['brand_uid']		    = $this->input->post('brand_uid'); //브랜드주키
            $value['brand_nm']		    = $this->global_lib->get_brand($this->input->post('brand_uid')); //브랜드명
            $value['notax']			    = $this->input->post('notax'); //과세구분
            $value['zone']			    = $this->input->post('zone'); //판매가능지역
            $value['zone_msg']		    = $this->input->post('zone_msg'); //판매가능지역 추가설명
            $value['delivery_period']   = $this->input->post('delivery_period'); //판매가능지역 추가설명
            $value['sc_type']		    = $this->input->post('sc_type'); //배송비 유형	0:공통설정, 1:무료, 2:조건부 무료, 3:유료
            $value['sc_method']		    = $this->input->post('sc_method'); //배송비 결제	0:선불, 1:착불, 2:사용자선택
            $value['sc_amt']		    = conv_number($this->input->post('sc_amt')); //기본 배송비
            $value['sc_minimum']	    = conv_number($this->input->post('sc_minimum'));	//조건 배송비
            $value['sc_each_use']	    = isset($sc_each_use)?$sc_each_use:0; //묶음배송불가
            $value['info_gubun']	    = $this->input->post('info_gubun'); //상품정보제공 구분
            $value['info_value']	    = $it_info_value; //상품정보제공 값
            $value['price_msg']		    = $this->input->post('price_msg') ?? ""; //가격 대체문구
            $value['stock_mod']		    = ($type == "timesale") ? 1 : $this->input->post('stock_mod'); //수량형식
            $value['odr_min']	        = ($type == "timesale") ? 1 : conv_number($this->input->post('odr_min')); //최소 주문한도
            $value['odr_max']	        = ($type == "timesale") ? 1 : conv_number($this->input->post('odr_max')); //최대 주문한도
            $value['buy_level']		    = $this->input->post('buy_level'); //구매가능 레벨
            $value['buy_only']		    = isset($buy_only)?$buy_only:0; //가격공개 여부
            $value['simg_type']		    = $this->input->post('simg_type'); //이미지 등록방식
            $value['sb_date']		    = $sb_date; //판매 시작일
            $value['eb_date']		    = $eb_date; //판매 종료일
            $value['update_time']	    = $OM_TIME_YMDHIS; //수정일시
            $value['is_point']	        = $this->input->post('is_point'); // 포인트 적립여부
    
            if($w == "") {
                $value['shop_state'] = $config['seller_reg_auto']; //자동승인여부
                $value['gcode'] = $_POST['gcode']; //상품코드
                $value['reg_time'] = $OM_TIME_YMDHIS; //등록일시
                $gs_id = $this->query_model->insertObj("shop_goods", $value);
            } else if($w == "u") {
                $value['shop_state'] = $config['seller_mod_auto']; //자동승인여부
                $this->query_model->updateObj("shop_goods", $value," index_no = '$gs_id'");
            }
    
            // 선택옵션등록
            if($option_count) {
                $comma = '';
                $opt_id = $this->input->post('opt_id');
                $opt_price = $this->input->post('opt_price');
                $opt_stock_qty = $this->input->post('opt_stock_qty');
                $opt_noti_qty = $this->input->post('opt_noti_qty');
                $opt_use = $this->input->post('opt_use');

                for ($i = 0; $i < $option_count; $i++) {
                    if ((int)$opt_price[$i] < 0 && abs((int)$opt_price[$i]) > $goods_price) {
                        throw new Exception("옵션 {$opt_id[$i]}의 가격 할인폭이 상품 가격보다 큽니다.");
                    }
                }

                $sql = " insert into shop_goods_option
                                    ( `io_id`, `io_type`, `gs_id`, `io_price`, `io_stock_qty`, `io_noti_qty`, `io_use` )
                                VALUES ";

                for ($i = 0; $i < $option_count; $i++) {
                    $sql .= $comma . " ( '{$opt_id[$i]}', '0', '$gs_id', '{$opt_price[$i]}', '{$opt_stock_qty[$i]}', '{$opt_noti_qty[$i]}', '{$opt_use[$i]}' )";
                    $comma = ' , ';
                }

                $this->query_model->returnNull($sql);
            }
    
            // 추가옵션등록
            if($supply_count) {
                $comma = '';
                $spl_id = $this->input->post('spl_id');
                $spl_price = $this->input->post('spl_price');
                $spl_stock_qty = $this->input->post('spl_stock_qty');
                $spl_noti_qty = $this->input->post('spl_noti_qty');
                $spl_use = $this->input->post('spl_use');

                for ($i = 0; $i < $supply_count; $i++) {
                    if ((int)$spl_price[$i] < 0 && abs((int)$spl_price[$i]) > $goods_price) {
                        throw new Exception("옵션 {$spl_id[$i]}의 가격 할인폭이 상품 가격보다 큽니다.");
                    }
                }
                
                $sql = " insert into shop_goods_option
                                    ( `io_id`, `io_type`, `gs_id`, `io_price`, `io_stock_qty`, `io_noti_qty`, `io_use` )
                                VALUES ";

                for ($i = 0; $i < $supply_count; $i++) {
                    $sql .= $comma . " ( '{$spl_id[$i]}', '1', '$gs_id', '{$spl_price[$i]}', '{$spl_stock_qty[$i]}', '{$spl_noti_qty[$i]}', '{$spl_use[$i]}' )";
                    $comma = ' , ';
                }
                $this->query_model->returnNull($sql);
            }

            $return['res'] = true;
            $return['msg'] = "상품이 등록되었습니다.";
            if($w == "") {
                $return['url'] = "mypage/page?code=seller_goods_form&w=u&gs_id=$gs_id&type=$type";
            } else {
                $return['url'] = "mypage/page?code=seller_goods_form&w=u&gs_id=$gs_id$q1&page=$page&bak=$bak&type=$type";
            }
        } catch (Exception $e) {
            $return['res'] = false;
            $return['msg'] = $e->getMessage();
        }

        echo json_encode($return);
    }

    private function validate_min_discount($normal_price, $goods_price, $stock_qty) {
        $discount_ratio = ($normal_price - $goods_price) / $normal_price;

        if ($normal_price <= 10000) {
            $base_discount = 0.07;
            $qty_factor = 0.0012;
        } elseif ($normal_price <= 30000) {
            $base_discount = 0.10;
            $qty_factor = 0.0010;
        } elseif ($normal_price <= 100000) {
            $base_discount = 0.13;
            $qty_factor = 0.0009;
        } else {
            $base_discount = 0.15;
            $qty_factor = 0.0008;
        }

        $min_discount = min($base_discount + ($stock_qty * $qty_factor), 0.25);
        $max_goods_price = round(($normal_price * (1 - $min_discount)) / 10) * 10;

        if ($discount_ratio < $min_discount) {
            throw new Exception(
                "온라인가({$normal_price})와 딜가({$goods_price})의 차이가 너무 적습니다. " .
                "재고 {$stock_qty}개 기준으로 최소 " . ($min_discount * 100) . "% 이상의 할인율이 필요하며, " .
                "딜가는 최소 {$max_goods_price}원 이하로 설정되어야 합니다."
            );
        }
    }

    public function seller_goods_brand_form_update(){
        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        $w=$this->input->post('w');
        $qstr=$this->load->get_var('qstr');
        $seller=$this->load->get_var('seller');
        
        $uploadPath="./data/brand";
        $configArr = array();
        $configArr['upload_path'] = $uploadPath;

        $configArr['allowed_types'] = $this->allowed_types;
        $configArr['max_size'] = $this->max_size;
        $configArr['max_width'] = $this->max_width;
        $configArr['max_height'] = $this->max_height;
        $configArr['remove_spaces'] = $this->remove_spaces;
        $configArr['encrypt_name'] = $this->encrypt_name;
        $this->load->library('upload', $configArr);
        $this->upload->initialize($configArr);

        $br_logo_del=$this->input->post('br_logo_del');
        $br_name=$this->input->post('br_name');
        $br_name_eng=$this->input->post('br_name_eng');
        unset($value);
        if($br_logo_del) {
            unlink($uploadPath.'/'.$br_logo_del);
            $value['br_logo'] = '';
        }
        if(isset($_FILES['br_logo']['name'])&&$_FILES['br_logo']['name']!="") {
            if(!$this->upload->do_upload('br_logo')){
                $error = $this->upload->display_errors();
            }else{
                $uploadFile=$this->upload->data();
                $new_file=$uploadFile['file_name'];
                $value['br_logo'] = $new_file;
            }
        }

        $value['br_user_yes'] = 0;
        $value['br_name'] = $br_name;
        $value['br_name_eng'] = $br_name_eng;
        $value['br_updatetime'] = $OM_TIME_YMDHIS;

        if($w == '') {
            $value['mb_id'] = $seller['seller_code'];
            $value['br_time'] = $OM_TIME_YMDHIS;
            $this->query_model->insertObj("shop_brand", $value);
            
            goto_url(base_url()."mypage/page?$q1&page=$page");

        } else if($w == 'u') {	
            $this->query_model->updateObj("shop_brand", $value, "br_id='$br_id'");

            // 상품 정보도 동시에 수정
            $br_name=$this->input->post('br_name');
            $sql = "update shop_goods set brand_nm = ? where brand_uid = ?";
            $param=array($br_name,$br_id);
            $this->query_model->returnNull($sql,$param);

            goto_url(base_url()."mypage/page?code=seller_goods_brand_form&w=u&br_id=$br_id$qstr&page=$page");
        }
    }
    public function seller_goods_brand_list_update(){
        $seller=$this->load->get_var('seller');
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        $chk=$this->input->post('chk');
        $count = ($chk)?count($_POST['chk']):"";
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }

        if($_POST['act_button'] == "선택수정"){
            $br_name=$this->input->post('br_name');
            $br_name_eng=$this->input->post('br_name_eng');
            $br_id=$this->input->post('br_id');
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $br_id = trim($_POST['br_id'][$k]);
                $sql="select br_logo, mb_id from shop_brand where br_id = ?";
                $param=array($br_id);
                $row = $this->query_model->returnOneArr($sql,$param);
                if($row['mb_id'] != $seller['seller_code'])
                    alert("잘못된 접근입니다.");

                $param2=array();
                $param2['br_name']=$br_name[$k];
                $param2['br_name_eng']=$br_name_eng[$k];
                $this->query_model->updateObj('shop_brand',$param2,"br_id = '{$br_id[$k]}'");
                
                // 상품 정보도 동시에 수정
                $sql = "update shop_goods set brand_nm = ? where brand_uid = ?";
                $param=array($br_name[$k],$br_id[$k]);
                $this->query_model->returnNull($sql,$param);
            }
        } 
        else if($_POST['act_button'] == "선택삭제") {
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $br_id = trim($_POST['br_id'][$k]);
                $sql="select br_logo, mb_id from shop_brand where br_id = ?";
                $param=array($br_id);
                $row = $this->query_model->returnOneArr($sql,$param);
                if($row['mb_id'] != $seller['seller_code'])
                    alert("잘못된 접근입니다.");
                @unlink("./data/brand/".$row['br_logo']);
                $sql="delete from shop_brand where br_id=?";
                $param=array($br_id);
                $this->query_model->returnNull($sql,$param);
            }
        }

        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function seller_goods_qa_delete(){
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        $count = count($_POST['chk']);
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }
        $iq_id=$this->input->post('iq_id');
        for($i=0; $i<$count; $i++){
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $sql = " delete from shop_goods_qa where iq_id = ?";
            $param=array($iq_id[$k]);
            $this->query_model->returnNull($sql,$param);
        }

        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function seller_odr_excel(){

        $seller=$this->load->get_var('seller');

        $fr_date=$this->input->get('fr_date');
        $to_date=$this->input->get('to_date');
        $code=$this->input->get('code');
        $sfl=$this->input->get('sfl');
        $stx=$this->input->get('stx');
        $od_settle_case=$this->input->get('od_settle_case');
        $od_status=$this->input->get('od_status');
        $od_final=$this->input->get('od_final');
        $od_memo=$this->input->get('od_memo');
        $od_receipt_point=$this->input->get('od_receipt_point');
        $od_coupon=$this->input->get('od_coupon');
        $od_escrow=$this->input->get('od_escrow');
        
        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $sql_common = " from shop_order ";

        $where = array();
        $where[] = " seller_id = '{$seller['seller_code']}' ";

        if($code == 'seller_odr_list') // 주문리스트(전체)
            $where[] = " dan > 1 ";
        else
            $where[] = " dan = '".conv_number($code)."' ";

        if($sfl && $stx)
            $where[] = " $sfl like '%$stx%' ";

        if($od_settle_case)
            $where[] = " paymethod = '$od_settle_case' ";

        if(is_numeric($od_status))
            $where[] = " dan = '$od_status' ";

        if(is_numeric($od_final))
            $where[] = " user_ok = '$od_final' ";

        if($od_memo)
            $where[] = " memo <> '' ";

        if($od_receipt_point)
            $where[] = " use_point != 0 ";

        if($od_coupon)
            $where[] = " coupon_price != 0 ";

        if($od_escrow)
            $where[] = " od_escrow = 1 ";

        if($fr_date && $to_date)
            $where[] = " left({$sel_field},10) between '$fr_date' and '$to_date' ";
        else if($fr_date && !$to_date)
            $where[] = " left({$sel_field},10) between '$fr_date' and '$fr_date' ";
        else if(!$fr_date && $to_date)
            $where[] = " left({$sel_field},10) between '$to_date' and '$to_date' ";

        if($where) {
            $sql_search = ' where '.implode(' and ', $where);
        }

        $sql_order = " order by od_time desc, index_no asc ";

        $sql = " select * {$sql_common} {$sql_search} {$sql_order} ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result=$resultObj->result_array();
        if(!$cnt)
            alert("출력할 자료가 없습니다.");
        $this->seller_odr_excel_sub($result);
    }
    public function seller_odr_excel2(){
        $od_id=$this->input->get('od_id');
        $seller=$this->load->get_var('seller');

        if(!$od_id)
            alert("주문번호가 넘어오지 않았습니다.");

        $sql = " select *
                from shop_order
                where od_id IN ({$od_id})
                    and seller_id = ?
                order by od_time desc, index_no asc ";
        $param=array($seller['seller_code']);
        $resultObj = $this->query_model->returnQueryObj($sql,$param);
        $cnt = $resultObj->num_rows();
        $result=$resultObj->result_array();
        if(!$cnt)
            alert("출력할 자료가 없습니다.");

        // 주문서 PHPExcel 공통
        $this->seller_odr_excel_sub($result);
    }
    private function seller_odr_excel_sub($result){
        $seller=$this->load->get_var('seller');
        $gw_status=$this->config->item('gw_status');
        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++.'1', '판매자ID')
            ->setCellValue($char++.'1', '회원ID')
            ->setCellValue($char++.'1', '주문번호')
            ->setCellValue($char++.'1', '일련번호')
            ->setCellValue($char++.'1', '상품코드')
            ->setCellValue($char++.'1', '상품명')
            ->setCellValue($char++.'1', '옵션')
            ->setCellValue($char++.'1', '공급가')
            ->setCellValue($char++.'1', '판매가')
            ->setCellValue($char++.'1', '수량')
            ->setCellValue($char++.'1', '쿠폰할인')
            ->setCellValue($char++.'1', '포인트결제')
            ->setCellValue($char++.'1', '배송비')
            ->setCellValue($char++.'1', '실결제금액')
            ->setCellValue($char++.'1', '총주문금액')
            ->setCellValue($char++.'1', '결제방법')
            ->setCellValue($char++.'1', '주문상태')
            ->setCellValue($char++.'1', '주문채널')
            ->setCellValue($char++.'1', '주문자명')
            ->setCellValue($char++.'1', '수취인명')
            ->setCellValue($char++.'1', '수취인전화번호')
            ->setCellValue($char++.'1', '수취인핸드폰')
            ->setCellValue($char++.'1', '수취인우편번호')
            ->setCellValue($char++.'1', '수취인주소')
            ->setCellValue($char++.'1', '주문시요청사항')
            ->setCellValue($char++.'1', '배송회사')
            ->setCellValue($char++.'1', '운송장번호')
            ->setCellValue($char++.'1', '입금자명')
            ->setCellValue($char++.'1', '입금일시')
            ->setCellValue($char++.'1', '주문일시')
            ->setCellValue($char++.'1', '거래증빙')
            ->setCellValue($char++.'1', '세금계산서(상호명)')
            ->setCellValue($char++.'1', '세금계산서(대표자)')
            ->setCellValue($char++.'1', '세금계산서(사업자등록번호)')
            ->setCellValue($char++.'1', '세금계산서(사업장주소)')
            ->setCellValue($char++.'1', '세금계산서(업태)')
            ->setCellValue($char++.'1', '세금계산서(종목)')
            ->setCellValue($char++.'1', '현금영수증(사업자 지출증빙용)')
            ->setCellValue($char++.'1', '현금영수증(개인 소득공제용)')
            ->setCellValue($char++.'1', '관리자메모');

        $i=2;
        foreach($result as $key => $row){
            $gs = unserialize($row['od_goods']);
            $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$seller['seller_code']}' ");
            $sodr = $this->funcomplex_lib->excel_order_list($row, $amount);

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++.$i, $sodr['od_seller_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['od_mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['od_id'].$sodr['od_test'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['od_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $gs['gcode'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $gs['gname'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['it_options'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['supply_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['goods_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['sum_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['coupon_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['use_point'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['baesong_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['use_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['goods_price']+$row['baesong_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $sodr['od_paytype'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $gw_status[$row['dan']], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['od_mobile'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['b_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['b_telephone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['b_cellphone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['b_zip'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $this->funcomplex_lib->print_address($row['b_addr1'], $row['b_addr2'], $row['b_addr3'], $row['b_addr_jibeon']), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['memo'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['od_delivery_company'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['delivery_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['deposit_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['od_receipt_time'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['od_time'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['od_taxbill'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['company_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['company_owner'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['company_saupja_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['company_addr'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['company_item'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['company_service'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['tax_saupja_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['tax_hp'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['shop_memo'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }
        // Rename worksheet
        $excel->getActiveSheet()->setTitle('주문내역');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="주문내역-'.date("ymd", time()).'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function seller_odr_update(){
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        $count = isset($_POST['chk'])?count($_POST['chk']):0;
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }

        if($_POST['act_button'] == "배송준비"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k     = $_POST['chk'][$i];
                $od_no = $_POST['od_no'][$k];

                $od = $this->global_lib->get_order($od_no);
                if($od['dan'] != 2) continue;

                $this->global_lib->change_order_status_3($od_no);
            }
        }else if($_POST['act_button'] == "배송중"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k			 = $_POST['chk'][$i];
                $od_no		 = $_POST['od_no'][$k];
                $delivery	 = $_POST['delivery'][$k];
                $delivery_no = $_POST['delivery_no'][$k];

                $od = $this->global_lib->get_order($od_no);
                if($od['dan'] != 3) continue;

                $this->global_lib->change_order_status_4($od_no, $delivery, $delivery_no);

                $od_sms_baesong[$od['od_id']] = $od['cellphone'];
            }

            foreach($od_sms_baesong as $key=>$recv) {
                $q = $this->global_lib->get_order($key, 'pt_id');
                $this->funcomplex_lib->moonjaro_order_sms_send($q['pt_id'], $recv, $key, 4);
            }
        }else if($_POST['act_button'] == "배송완료"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k			 = $_POST['chk'][$i];
                $od_no		 = $_POST['od_no'][$k];
                $delivery	 = $_POST['delivery'][$k];
                $delivery_no = $_POST['delivery_no'][$k];

                $od = $this->global_lib->get_order($od_no);
                if($od['dan'] != 4) continue;

                $this->funcomplex_lib->change_order_status_5($od_no, $delivery, $delivery_no);

                $od_sms_delivered[$od['od_id']] = $od['cellphone'];
            }

            foreach($od_sms_delivered as $key=>$recv) {
                $q = $this->global_lib->get_order($key, 'pt_id');
                $this->funcomplex_lib->moonjaro_order_sms_send($q['pt_id'], $recv, $key, 6);
            }
        }else if($_POST['act_button'] == "운송장번호수정"){
            $delivery=$this->input->post('delivery');
            $delivery_no=$this->input->post('delivery_no');
            $od_no=$this->input->post('od_no');
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = " update shop_order set delivery	= ? , delivery_no = ? where od_no =?";
                $param=array($delivery[$k],$delivery_no[$k],$od_no[$k]);
                $this->query_model->returnNull($sql,$param);
            }
        } else {
            alert();
        }

        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function seller_odr_statusupdate(){
        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $od_hp=$this->input->post('od_hp');
        $od_id=$this->input->post('od_id');
        
        $chk_count = isset($_POST['chk'])?count($_POST['chk']):0;
        $pt_id=$this->load->get_var('pt_id');
        if(!$chk_count)
            alert('처리할 자료를 하나 이상 선택해 주십시오.');

        $od_sms_baesong_check	= 0;
        $od_sms_delivered_check = 0;
        $od_sms_cancel_check	= 0;
        $od_cancel_change		= 0;
        $deliveryArr=$this->input->post('delivery');
        $delivery_noArr=$this->input->post('delivery_no');
        //==============================================================================
        // 주문상태변경
        //------------------------------------------------------------------------------
        for($i=0; $i<$chk_count; $i++){
            // 실제 번호를 넘김
            $k				= $_POST['chk'][$i];
            $od_no			= $_POST['od_no'][$k];
            $change_status  = $_POST['change_status'][$k];
            $current_status = $_POST['current_status'][$k];
            $delivery		= trim($deliveryArr[$k]);
            $delivery_no	= trim($delivery_noArr[$k]);

            switch($change_status) {
                case '3': // 배송준비
                    if($current_status == 2) {
                        $this->global_lib->change_order_status_3($od_no, $delivery, $delivery_no);
                    }
                    break;
                case '4': // 배송중
                    if($current_status == 3) {
                        $this->global_lib->change_order_status_4($od_no, $delivery, $delivery_no);
                        $od_sms_baesong_check++;
                    }
                    break;
                case '5': // 배송완료
                    if(in_array($current_status, array(3,4))) {
                        $this->funcomplex_lib->change_order_status_5($od_no);
                        $od_sms_delivered_check++;
                    }
                    break;
                // case '6': // 취소
                //     if($current_status == 1) {
                //         change_order_status_6($od_no);
                //         $od_sms_cancel_check++;
                //     }
                //     break;
                case '7': // 반품
                    if($current_status == 5) {
                        $this->funcomplex_lib->change_order_status_7($od_no);
                        $od_cancel_change++;
                    }
                    break;
                case '8': // 교환
                    if($current_status == 5) {
                        $this->global_lib->change_order_status_8($od_no);
                    }
                    break;
                case '9': // 환불
                    if(in_array($current_status, array(2,3))) {
                        $this->global_lib->change_order_status_9($od_no);
                        $od_sms_cancel_check++;
                        $od_cancel_change++;
                    }
                    break;
            }
        }
        //------------------------------------------------------------------------------

        //==============================================================================
        // 문자전송
        //------------------------------------------------------------------------------
        if($od_sms_baesong_check)
            $this->funcomplex_lib->moonjaro_order_sms_send($pt_id, $od_hp, $od_id, 4); // 배송중 문자

        if($od_sms_delivered_check)
            $this->funcomplex_lib->moonjaro_order_sms_send($pt_id, $od_hp, $od_id, 6); // 배송완료 문자

        if($od_sms_cancel_check)
            $this->funcomplex_lib->moonjaro_order_sms_send($pt_id, $od_hp, $od_id, 5); // 주문취소 문자
        //------------------------------------------------------------------------------

        // 상품 모두 취소일 경우 주문상태 변경
        $mod_history = '';
        $pg_res_cd = '';
        $pg_res_msg = '';
        $pg_cancel_log = '';

        if($od_cancel_change) {
            $sql = " select COUNT(*) as od_count1,
                            SUM(IF(dan = '6' OR dan = '7' OR dan = '9', 1, 0)) as od_count2,
                            SUM(refund_price) as od_refund_price,
                            SUM(use_price) as od_receipt_price
                    from shop_order
                    where od_id = ?";
            $param=array($od_id);
            $row = $this->query_model->returnOneArr($sql,$param);

            if($row['od_count1'] == $row['od_count2']) {
                // PG 신용카드 결제 취소일 때
                $od_receipt_price = $row['od_receipt_price'];
                if($od_receipt_price > 0 && $row['od_refund_price'] == 0) {
                    $sql = " select * from shop_order where od_id =?";
                    $param=array($od_id);
                    $od = $this->query_model->returnOneArr($sql,$param);

                    if($od['od_tno'] && ($od['paymethod'] == '신용카드' || $od['paymethod'] == '간편결제' || $od['paymethod'] == 'KAKAOPAY') || ($od['od_pg'] == 'inicis' && $od['paymethod'] == '삼성페이')) {
                        // 가맹점 PG결제 정보
                        $default = $this->funcomplex_lib->set_partner_value($od['od_settle_pid']);

                        switch($od['od_pg']) {
                            case 'lg':
                                include_once('./Bill/settle_lg.inc.php');

                                $LGD_TID = $od['od_tno'];

                                $xpay = new XPay($configPath, $CST_PLATFORM);

                                // Mert Key 설정
                                $xpay->set_config_value('t'.$LGD_MID, $default['de_lg_mert_key']);
                                $xpay->set_config_value($LGD_MID, $default['de_lg_mert_key']);

                                $xpay->Init_TX($LGD_MID);

                                $xpay->Set('LGD_TXNAME', 'Cancel');
                                $xpay->Set('LGD_TID', $LGD_TID);

                                if($xpay->TX()) {
                                    $res_cd = $xpay->Response_Code();
                                    if($res_cd != '0000' && $res_cd != 'AV11') {
                                        $pg_res_cd = $res_cd;
                                        $pg_res_msg = $xpay->Response_Msg();
                                    }
                                } else {
                                    $pg_res_cd = $xpay->Response_Code();
                                    $pg_res_msg = $xpay->Response_Msg();
                                }
                                break;
                            case 'inicis':
                                include_once('./Bill/settle_inicis.inc.php');
                                $cancel_msg = iconv_euckr('쇼핑몰 운영자 승인 취소');

                                /*********************
                                 * 3. 취소 정보 설정 *
                                 *********************/
                                $inipay->SetField("type",      "cancel");                        // 고정 (절대 수정 불가)
                                $inipay->SetField("mid",       $default['de_inicis_mid']);       // 상점아이디
                                /**************************************************************************************************
                                 * admin 은 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
                                 * 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
                                 * 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
                                 * 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
                                 **************************************************************************************************/
                                $inipay->SetField("admin",     $default['de_inicis_admin_key']); // 비대칭 사용키 키패스워드
                                $inipay->SetField("tid",       $od['od_tno']);                   // 취소할 거래의 거래아이디
                                $inipay->SetField("cancelmsg", $cancel_msg);                     // 취소사유

                                /****************
                                 * 4. 취소 요청 *
                                 ****************/
                                $inipay->startAction();

                                /****************************************************************
                                 * 5. 취소 결과                                           	*
                                 *                                                        	*
                                 * 결과코드 : $inipay->getResult('ResultCode') ("00"이면 취소 성공)  	*
                                 * 결과내용 : $inipay->getResult('ResultMsg') (취소결과에 대한 설명) 	*
                                 * 취소날짜 : $inipay->getResult('CancelDate') (YYYYMMDD)          	*
                                 * 취소시각 : $inipay->getResult('CancelTime') (HHMMSS)            	*
                                 * 현금영수증 취소 승인번호 : $inipay->getResult('CSHR_CancelNum')    *
                                 * (현금영수증 발급 취소시에만 리턴됨)                          *
                                 ****************************************************************/

                                $res_cd  = $inipay->getResult('ResultCode');
                                $res_msg = $inipay->getResult('ResultMsg');

                                if($res_cd != '00') {
                                    $pg_res_cd = $res_cd;
                                    $pg_res_msg = iconv_utf8($res_msg);
                                }
                                break;
                            case 'KAKAOPAY':
                                #include_once(OM_SHOP_PATH.'/settle_kakaopay.inc.php');
                                $_REQUEST['TID']               = $od['od_tno'];
                                $_REQUEST['Amt']               = $od_receipt_price;
                                $_REQUEST['CancelMsg']         = '쇼핑몰 운영자 승인 취소';
                                $_REQUEST['PartialCancelCode'] = 0;
                                #include OM_SHOP_PATH.'/kakaopay/kakaopay_cancel.php';
                                break;
                            case 'kcp':
                                include_once('./Bill/settle_kcp.inc.php');
                                include_once('./Bill/kcp/pp_ax_hub_lib.php');

                                // locale ko_KR.euc-kr 로 설정
                                setlocale(LC_CTYPE, 'ko_KR.euc-kr');

                                $c_PayPlus = new C_PP_CLI_T;

                                $c_PayPlus->mf_clear();

                                $tno = $od['od_tno'];
                                $tran_cd = '00200000';
                                $cancel_msg = iconv_euckr('쇼핑몰 운영자 승인 취소');
                                $cust_ip = $_SERVER['REMOTE_ADDR'];
                                $bSucc_mod_type = "STSC";

                                $c_PayPlus->mf_set_modx_data( "tno",      $tno );  // KCP 원거래 거래번호
                                $c_PayPlus->mf_set_modx_data( "mod_type", $bSucc_mod_type );  // 원거래 변경 요청 종류
                                $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip );  // 변경 요청자 IP
                                $c_PayPlus->mf_set_modx_data( "mod_desc", $cancel_msg );  // 변경 사유

                                $c_PayPlus->mf_do_tx( $tno,  $g_conf_home_dir, $g_conf_site_cd,
                                                    $g_conf_site_key,  $tran_cd,    "",
                                                    $g_conf_gw_url,  $g_conf_gw_port,  "payplus_cli_slib",
                                                    $ordr_idxx, $cust_ip, "3" ,
                                                    0, 0, $g_conf_key_dir, $g_conf_log_dir);

                                $res_cd  = $c_PayPlus->m_res_cd;
                                $res_msg = $c_PayPlus->m_res_msg;

                                if($res_cd != '0000') {
                                    $pg_res_cd = $res_cd;
                                    $pg_res_msg = iconv_utf8($res_msg);
                                }

                                // locale 설정 초기화
                                setlocale(LC_CTYPE, '');
                                break;
                        }

                        // PG 취소요청 성공했으면
                        if($pg_res_cd == '') {
                            $pg_cancel_log = ' PG 신용카드 승인취소 처리';

                            // 전체취소
                            $sql = " select index_no from shop_order where od_id = ? order by index_no asc ";
                            $param=array($od_id);
                            $res = $this->query_model->returnArr($sql,$param);
                            foreach($res as $key => $row){
                                $sql = " update shop_order
                                            set refund_price = use_price
                                        where index_no = ?";
                                $param=array($row['index_no']);
                                $this->query_model->returnNull($sql,$param);
                            }
                        }
                    }
                }

                // 관리자 주문취소 로그
                $mod_history = $OM_TIME_YMDHIS.' '.$member['id'].' 주문취소 처리'.$pg_cancel_log."\n";
            }
        }

        if($mod_history) { // 주문변경 히스토리 기록
            $sql = " update shop_order
                        set od_mod_history = CONCAT(od_mod_history,?)
                    where od_id = ?";
            $param=array($mod_history,$od_id);
            $this->query_model->returnNull($sql,$param);
        }

        $url = base_url()."mypage/seller_odr_form?od_id=$od_id";

        // 신용카드 취소 때 오류가 있으면 알림
        if($pg_res_cd && $pg_res_msg) {
            alert('오류코드 : '.$pg_res_cd.' 오류내용 : '.$pg_res_msg, $url);
        }

        goto_url($url);
    }
    public function seller_goods_list_update(){
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        $count = count($_POST['chk']);
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }

        if($_POST['act_button'] == "선택삭제"){
            // _GOODS_DELETE_ 상수를 선언해야 goods_delete.inc.php 가 정상 작동함
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                // include 전에 $gs_id 값을 반드시 넘겨야 함
                $gs_id = trim($_POST['gs_id'][$k]);
                $gs = $this->global_lib->get_goods($gs_id);
                $dir_list = './data/goods/'.$gs['gcode'];
                for($g=1; $g<=6; $g++) {
                    if($gs['simg'.$g]) {
                        @unlink('./data/goods/'.$gs['simg'.$g]);
                        delete_item_thumbnail($dir_list, $gs['simg'.$g]);
                    }
                }

                // 에디터 이미지 삭제
                $this->funcomplex_lib->delete_editor_image($gs['memo']);
                // 삭제
                $sql1="delete from shop_goods where index_no=?";
                $sql2="delete from shop_goods_type where gs_id=?";
                $sql3="delete from shop_goods_review where gs_id=?";
                $sql4="delete from shop_goods_option where gs_id=?";
                $sql5="delete from shop_cart where gs_id=? and ct_select='0'";
                $sql6="delete from shop_wish where gs_id=?";
                $sql7="delete from shop_goods_qa where gs_id=?";
                $sql8="delete from shop_goods_relation where gs_id = ?";
                $sql9="delete from shop_goods_relation where gs_id2 = ?";
                $param=array($gs_id);
                $this->query_model->returnNull($sql1,$param); // 상품테이블
                $this->query_model->returnNull($sql2,$param); //진열관리
                $this->query_model->returnNull($sql3,$param); // 상품평
                $this->query_model->returnNull($sql4,$param); // 옵션
                $this->query_model->returnNull($sql5,$param); // 장바구니
                $this->query_model->returnNull($sql6,$param); // 찜목록
                $this->query_model->returnNull($sql7,$param); // 상품문의
                $this->query_model->returnNull($sql8,$param);// 관련상품
                $this->query_model->returnNull($sql9,$param);// 관련상품
            }
        }else if($_POST['act_button'] == "선택상품복사"){
            // _GOODS_COPY_ 상수를 선언해야 goods_copy.inc.php 가 정상 작동함
            $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                // include 전에 $gs_id 값을 반드시 넘겨야 함
                $gs_id = trim($_POST['gs_id'][$k]);
                $sql = " select * from shop_goods where index_no = ? limit 1 ";
                $param=array($gs_id);
                $cp = $this->query_model->returnOneArr($sql,$param);
                // 상품테이블의 필드가 추가되어도 수정하지 않도록 필드명을 추출하여 insert 퀴리를 생성한다.
                $sql_common = "";
                $fields = $this->gvcomplex_lib->sql_field_names("shop_goods");
                foreach($fields as $fld) {
                    if(in_array($fld, array('index_no', 'gcode', 'readcount', 'rank', 'm_count', 'sum_qty', 'reg_time', 'update_time'))) continue;

                    $sql_common .= " , $fld = '".addslashes($cp[$fld])."' ";
                }

                $sql = " insert into shop_goods
                            set reg_time = '".$OM_TIME_YMDHIS."',
                                update_time = '".$OM_TIME_YMDHIS."'
                                $sql_common ";
                $insert_id = $this->query_model->returnInsertId($sql);

                $sql_img = "";
                for($g=1; $g<=6; $g++) {
                    if($cp['simg'.$g] && preg_match("/^(http[s]?:\/\/)/", $cp['simg'.$g]) == false) {
                        $file = './data/goods/'.$cp['simg'.$g];
                        $dstfile = "./data/goods/{$insert_id}_".$cp['simg'.$g];
                        $filename = basename($dstfile);

                        @copy($file, $dstfile);
                        @chmod($dstfile, 0644);
                        $sql_img .= " , simg{$g} = '$filename' ";
                    }
                }

                $new_code = time() + $insert_id;

                $sql = " update shop_goods
                            set gcode = ?
                                $sql_img
                        where index_no = ?";
                $param=array($new_code,$insert_id);
                $this->query_model->returnNull($sql,$param);

                // 옵션 copy
                $opt_sql = " insert ignore into shop_goods_option
                                    ( io_id, io_type, gs_id, io_price, io_stock_qty, io_noti_qty, io_use )
                            select io_id, io_type, ?, io_price, io_stock_qty, io_noti_qty, io_use
                            from shop_goods_option
                            where gs_id = ?
                            order by io_no asc ";
                $param=array($insert_id,$gs_id);
                $this->query_model->returnNull($opt_sql,$param);
            }
        }else if($_POST['act_button'] == "선택재고수정"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $gs_id = trim($_POST['gs_id'][$k]);
                $stock_qty = conv_number($_POST['stock_qty']);

                $sql = " update shop_goods set stock_qty = ? where index_no = ?";
                $param=array($stock_qty,$gs_id);
                $this->query_model->returnNull($sql,$param);
            }
        }else if($_POST['act_button'] == "선택옵션재고수정"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $io_no = trim($_POST['io_no'][$k]);
                $io_stock_qty = conv_number($_POST['stock_qty']);

                $sql = " update shop_goods_option set io_stock_qty = ? where io_no = ?";
                $param=array($io_stock_qty,$io_no);
                $this->query_model->returnNull($sql,$param);
            }
        }else {
            alert();
        }
        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function seller_goods_list_excel(){
        $flag=$this->input->get('flag');

        $seller=$this->load->get_var('seller');
        
        $fr_date=$this->input->get('fr_date');
        $to_date=$this->input->get('to_date');
        $sel_ca1=$this->input->get('sel_ca1');
        $sel_ca2=$this->input->get('sel_ca2');
        $sel_ca3=$this->input->get('sel_ca3');
        $sel_ca4=$this->input->get('sel_ca4');
        $sel_ca5=$this->input->get('sel_ca5');
        $code=$this->input->get('code');
        $sca=$this->input->get('sca');
        $stx=$this->input->get('stx');
        $sfl=$this->input->get('sfl');
        $fr_stock=$this->input->get('fr_stock');
        $fr_price=$this->input->get('fr_price');
        $orderby=$this->input->get('orderby');

        $filed=$this->input->get('filed');
        $filed=$this->db->escape_str($filed);
        

        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        if($sel_ca1) $sca = $sel_ca1;
        if($sel_ca2) $sca = $sel_ca2;
        if($sel_ca3) $sca = $sel_ca3;
        if($sel_ca4) $sca = $sel_ca4;
        if($sel_ca5) $sca = $sel_ca5;

        $sql_type = "";
        if($flag == "normal") {
            $sql_type = "and type = 'normal'";
        } else if($flag == "timesale") {
            $sql_type = "and type = 'timesale'";
        }

        $sql_common = " from shop_goods ";
        $sql_search = " where mb_id = '{$seller['seller_code']}' {$sql_type}";

        if($code == 'seller_goods_stock') {
            $sql_search .= " and (stock_qty <= noti_qty and stock_mod = 1 and opt_subject = '') ";
        }

        if($sca) {
            $sql_search .= " and (ca_id like '$sca%' or ca_id2 like '$sca%' or ca_id3 like '$sca%') ";
        }

        // 검색어
        if($stx) {
            switch($sfl) {
                case "gname" :
                case "explan" :
                case "maker" :
                case "origin" :
                case "model" :
                    $sql_search .= " and $sfl like '%$stx%' ";
                    break;
                default :
                    $sql_search .= " and $sfl like '$stx%' ";
                    break;
            }
        }

        // 기간검색
        if($fr_date && $to_date)
            $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if($fr_date && !$to_date)
            $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if(!$fr_date && $to_date)
            $sql_search .= " and $q_date_field between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        // 브랜드
        if(isset($q_brand) && $q_brand)
            $sql_search .= " and brand_uid = '$q_brand' ";

        // 배송가능 지역
        if(isset($q_zone) && $q_zone)
            $sql_search .= " and zone = '$q_zone' ";

        // 상품재고
        if($fr_stock && $to_stock)
            $sql_search .= " and $q_stock_field between '$fr_stock' and '$to_stock' ";

        // 상품가격
        if($fr_price && $to_price)
            $sql_search .= " and $q_price_field between '$fr_price' and '$to_price' ";

        // 판매여부
        if(isset($q_isopen) && is_numeric($q_isopen))
            $sql_search .= " and isopen='$q_isopen' ";

        // 과세유형
        if(isset($q_notax) && is_numeric($q_notax))
            $sql_search .= " and notax = '$q_notax' ";

        // 상품 필수옵션
        if(isset($q_option) && is_numeric($q_option)) {
            if($q_option)
                $sql_search .= " and opt_subject <> '' ";
            else
                $sql_search .= " and opt_subject = '' ";
        }

        // 상품 추가옵션
        if(isset($q_supply) && is_numeric($q_supply)) {
            if($q_supply)
                $sql_search .= " and spl_subject <> '' ";
            else
                $sql_search .= " and spl_subject = '' ";
        }

        // 승인상태
        if(isset($q_state) && is_numeric($q_state))
            $sql_search .= " and shop_state = '$q_state' ";

        if(!$orderby) {
            $filed = "index_no";
            $sod = "desc";
        } else {
            $sod = $orderby;
        }

        $sql_order = " order by $filed $sod ";

        $sql = " select * $sql_common $sql_search $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result=$resultObj->result_array();
        if(!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++.'1', '상품코드')
            ->setCellValue($char++.'1', '대표분류')
            ->setCellValue($char++.'1', '추가분류2')
            ->setCellValue($char++.'1', '추가분류3')
            ->setCellValue($char++.'1', '상품타입')
            ->setCellValue($char++.'1', '상품명')
            ->setCellValue($char++.'1', '짧은설명')
            ->setCellValue($char++.'1', '검색키워드')
            ->setCellValue($char++.'1', '모델명')
            ->setCellValue($char++.'1', '브랜드')
            ->setCellValue($char++.'1', '과세설정')
            ->setCellValue($char++.'1', '판매가능지역')
            ->setCellValue($char++.'1', '판매가능지역 추가설명')
            ->setCellValue($char++.'1', '원산지')
            ->setCellValue($char++.'1', '제조사')
            ->setCellValue($char++.'1', '판매여부')
            ->setCellValue($char++.'1', '공급가격')
            ->setCellValue($char++.'1', '시중가격')
            ->setCellValue($char++.'1', '판매가격')
            ->setCellValue($char++.'1', '실제판매가(함께장터)')
            ->setCellValue($char++.'1', '가격대체문구')
            ->setCellValue($char++.'1', '재고적용타입')
            ->setCellValue($char++.'1', '재고수량')
            ->setCellValue($char++.'1', '재고통보수량')
            ->setCellValue($char++.'1', '최소주문한도')
            ->setCellValue($char++.'1', '최대주문한도')
            ->setCellValue($char++.'1', '포인트 적립여부')
            ->setCellValue($char++.'1', '판매기간 시작일')
            ->setCellValue($char++.'1', '판매기간 종료일')
            ->setCellValue($char++.'1', '구매가능레벨')
            ->setCellValue($char++.'1', '가격공개')
            ->setCellValue($char++.'1', '배송비유형')
            ->setCellValue($char++.'1', '배송비결제')
            ->setCellValue($char++.'1', '기본배송비')
            ->setCellValue($char++.'1', '조건배송비')
            ->setCellValue($char++.'1', '이미지등록방식')
            ->setCellValue($char++.'1', '소이미지')
            ->setCellValue($char++.'1', '중이미지1')
            ->setCellValue($char++.'1', '중이미지2')
            ->setCellValue($char++.'1', '중이미지3')
            ->setCellValue($char++.'1', '중이미지4')
            ->setCellValue($char++.'1', '중이미지5')
            ->setCellValue($char++.'1', '상세설명')
            ->setCellValue($char++.'1', '관리자메모');
        $i=2;
        foreach($result as $key => $row){
            if(is_null_time($row['sb_date'])) $row['sb_date'] = '';
            if(is_null_time($row['eb_date'])) $row['eb_date'] = '';

            $char = 'A';
            $excel->setActiveSheetIndex(0)
            ->setCellValueExplicit($char++.$i, $row['gcode'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['ca_id'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['ca_id2'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['ca_id3'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['type'] == "normal" ? "열린장터" : "함께장터", PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['gname'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['explan'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['keywords'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['model'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['brand_nm'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['notax'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['zone'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['zone_msg'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['origin'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['maker'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['isopen'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['supply_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['normal_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['goods_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['calculate_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['price_msg'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['stock_mod'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['stock_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['noti_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['odr_min'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['odr_max'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['is_point'] == "y" ? "적립" : "적립안함", PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['sb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['eb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['buy_level'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['buy_only'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['sc_type'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['sc_method'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['sc_amt'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['sc_minimum'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['simg_type'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
            ->setCellValueExplicit($char++.$i, $row['simg1'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['simg2'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['simg3'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['simg4'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['simg5'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['simg6'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['memo'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit($char++.$i, $row['admin_memo'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('상품');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        //Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="상품-'.date("ymd", time()).'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function seller_odr_baesongupdate(){
        $od_id=$this->input->post('od_id');

        $count = count($_POST['chk']);
        if(!$count) {
            alert('처리할 자료를 하나 이상 선택해 주십시오.');
        }
        $delivery=$this->input->post('delivery');
        $delivery_no=$this->input->post('delivery_no');
        $od_no=$this->input->post('od_no');
        for($i=0; $i<$count; $i++){
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            if(!isset($_POST['delivery'][$k]))
                continue;

            $sql = " update shop_order set delivery	= ? , delivery_no = ? where od_no = ? ";
            $param=array($delivery[$k],$delivery_no[$k],$od_no[$k]);
            $this->query_model->returnNull($sql,$param);
        }

        goto_url(base_url()."mypage/seller_odr_form?od_id=$od_id");
    }
    #가맹점 관련된 함수들
    public function partner_term_update(){
        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $member=$this->load->get_var('member');
        unset($value);
        $value['mb_id']			= $member['id'];
        $value['expire_date']	= $this->input->post('expire_date');
        $value['term_price']	= $this->input->post('term_price');
        $value['pay_method']	= $this->input->post('pay_method');
        $value['deposit_name']	= $this->input->post('deposit_name');
        $value['bank_account']	= $this->input->post('bank_account');
        $value['reg_time']		= $OM_TIME_YMDHIS;
        $this->query_model->insertObj("shop_partner_term", $value);

        goto_url(base_url().'mypage/page?code=partner_term');
    }
    public function partner_term_delete(){
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        $count = count($_POST['chk']);
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }
        $index_no=$this->input->post('index_no');
        for($i=0; $i<$count; $i++){
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $sql = " delete from shop_partner_term where index_no = ?";
            $param=array($index_no[$k]);
            $this->query_model->returnNull($sql,$param);
        }
        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function partner_meta_update(){
        $member=$this->load->get_var('member');
        unset($value);
        $value['head_title']		= $this->input->post('head_title'); //웹브라우져 타이틀
        $value['meta_author']		= $this->input->post('meta_author'); //Author : 메타태그 1
        $value['meta_description']	= $this->input->post('meta_description'); //description : 메타태그 2
        $value['meta_keywords']		= $this->input->post('meta_keywords'); //keywords : 메타태그 3
        $value['add_meta']			= $this->input->post('add_meta'); //추가 메타태그
        $value["head_script"]		= $this->input->post("head_script"); //<HEAD> 내부 태그
        $value["tail_script"]		= $this->input->post("tail_script"); //<BODY> 내부 태그
        $this->query_model->updateObj("shop_partner",$value,"mb_id='$member[id]'");

        goto_url(base_url().'mypage/page?code=partner_meta');
    }
    public function partner_sns_update(){
        $member=$this->load->get_var('member');
        unset($value);
        $value['de_sns_login_use']			= $this->input->post('de_sns_login_use');
        $value['de_naver_appid']			= $this->input->post('de_naver_appid');
        $value['de_naver_secret']			= $this->input->post('de_naver_secret');
        $value['de_facebook_appid']			= $this->input->post('de_facebook_appid');
        $value['de_facebook_secret']		= $this->input->post('de_facebook_secret');
        $value['de_kakao_rest_apikey']		= $this->input->post('de_kakao_rest_apikey');
        $value['de_googl_shorturl_apikey']	= $this->input->post('de_googl_shorturl_apikey');
        $value['de_insta_url']				= $this->input->post('de_insta_url');
        $value['de_insta_client_id']		= $this->input->post('de_insta_client_id');
        $value['de_insta_redirect_uri']		= $this->input->post('de_insta_redirect_uri');
        $value['de_insta_access_token']		= $this->input->post('de_insta_access_token');
        $value['de_sns_facebook']			= $this->input->post('de_sns_facebook');
        $value['de_sns_twitter']			= $this->input->post('de_sns_twitter');
        $value['de_sns_instagram']			= $this->input->post('de_sns_instagram');
        $value['de_sns_pinterest']			= $this->input->post('de_sns_pinterest');
        $value['de_sns_naverblog']			= $this->input->post('de_sns_naverblog');
        $value['de_sns_naverband']			= $this->input->post('de_sns_naverband');
        $value['de_sns_kakaotalk']			= $this->input->post('de_sns_kakaotalk');
        $value['de_sns_kakaostory']			= $this->input->post('de_sns_kakaostory');
        $this->query_model->updateObj("shop_partner",$value,"mb_id='$member[id]'");

        goto_url(base_url().'mypage/page?code=partner_sns');
    }
    public function partner_baesong_update(){
        $pf_auth_good=$this->load->get_var('pf_auth_good');
        $member=$this->load->get_var('member');
        if(!$pf_auth_good)
            alert('개별 상품판매 권한이 있어야만 이용 가능합니다.');

        unset($value);
        $value['delivery_method']	= $this->input->post('delivery_method');
        $value['delivery_price']	= conv_number($this->input->post('delivery_price'));
        $value['delivery_price2']	= conv_number($this->input->post('delivery_price2'));
        $value['delivery_minimum']	= conv_number($this->input->post('delivery_minimum'));
        $value['baesong_cont1']		= $this->input->post('baesong_cont1');
        $value['baesong_cont2']		= $this->input->post('baesong_cont2');
        $this->query_model->updateObj("shop_partner",$value,"mb_id='$member[id]'");

        goto_url(base_url().'mypage/page?code=partner_baesong');
    }
    public function partner_sms_update(){
        $config=$this->load->get_var('config');
        $member=$this->load->get_var('member');
        if(!$config['pf_auth_sms'])
            alert('개별 문자설정 권한이 있어야만 이용 가능합니다.');

        // 회신번호 체크
        if(!check_vaild_callback($_POST['cf_sms_recall']))
            alert('발신번호가 올바르지 않습니다.');

        $userinfo = get_moonjaro_userinfo($_POST['cf_moonjaro_key']);

        if(!$userinfo['code'])
            alert('문자로 아이디와 패스워드가 맞지 않습니다.');

        unset($value);
        $value['mb_id'] = $member['id'];
        $value['cf_sms_use'] = $this->input->post('cf_sms_use');
        $value['cf_sms_type'] = $this->input->post('cf_sms_type');
        $value['cf_sms_recall']	= $this->input->post('cf_sms_recall');
        $value['cf_moonjaro_key'] = $this->input->post('cf_moonjaro_key');
        $value['cf_cont1'] = $this->input->post('cf_cont1');
        $value['cf_cont2'] = $this->input->post('cf_cont2');
        $value['cf_cont3'] = $this->input->post('cf_cont3');
        $value['cf_cont4'] = $this->input->post('cf_cont4');
        $value['cf_cont5'] = $this->input->post('cf_cont5');
        $value['cf_cont6'] = $this->input->post('cf_cont6');
        $value['cf_mb_use1'] = $this->input->post('cf_mb_use1');
        $value['cf_ad_use1'] = $this->input->post('cf_ad_use1');
        $value['cf_re_use1'] = $this->input->post('cf_re_use1');
        $value['cf_sr_use1'] = $this->input->post('cf_sr_use1');
        $value['cf_mb_use2'] = $this->input->post('cf_mb_use2');
        $value['cf_ad_use2'] = $this->input->post('cf_ad_use2');
        $value['cf_re_use2'] = $this->input->post('cf_re_use2');
        $value['cf_sr_use2'] = $this->input->post('cf_sr_use2');
        $value['cf_mb_use3'] = $this->input->post('cf_mb_use3');
        $value['cf_ad_use3'] = $this->input->post('cf_ad_use3');
        $value['cf_re_use3'] = $this->input->post('cf_re_use3');
        $value['cf_sr_use3'] = $this->input->post('cf_sr_use3');
        $value['cf_mb_use4'] = $this->input->post('cf_mb_use4');
        $value['cf_ad_use4'] = $this->input->post('cf_ad_use4');
        $value['cf_re_use4'] = $this->input->post('cf_re_use4');
        $value['cf_sr_use4'] = $this->input->post('cf_sr_use4');
        $value['cf_mb_use5'] = $this->input->post('cf_mb_use5');
        $value['cf_ad_use5'] = $this->input->post('cf_ad_use5');
        $value['cf_re_use5'] = $this->input->post('cf_re_use5');
        $value['cf_sr_use5'] = $this->input->post('cf_sr_use5');
        $value['cf_mb_use6'] = $this->input->post('cf_mb_use6');
        $value['cf_ad_use6'] = $this->input->post('cf_ad_use6');
        $value['cf_re_use6'] = $this->input->post('cf_re_use6');
        $value['cf_sr_use6'] = $this->input->post('cf_sr_use6');

        $res = $this->global_lib->get_sms($member['id']);
        if (!$res)
            $this->query_model->insertObj("shop_sms", $value);
        else
            $this->query_model->updateObj("shop_sms", $value, "mb_id = '{$member['id']}'");

        goto_url(base_url().'mypage/page?code=partner_sms');
    }
    public function partner_agree_update(){
        $member=$this->load->get_var('member');
        unset($value);
        $value['shop_provision'] = $this->input->post('shop_provision'); // 회원가입약관
        $value['shop_private']	 = $this->input->post('shop_private'); // 개인정보 수집 및 이용
        $value['shop_policy']	 = $this->input->post('shop_policy'); // 개인정보처리방침
        $this->query_model->updateObj("shop_partner",$value,"mb_id='$member[id]'");

        goto_url(base_url().'mypage/page?code=partner_agree');
    }
    public function partner_pg_update(){
        $member=$this->load->get_var('member');
        $pf_auth_pg=$this->load->get_var('pf_auth_pg');
        if(!$pf_auth_pg)
            alert('개별 PG결제 사용 권한이 없습니다.');

        $adm_bank = array();
        for($i=0; $i<count($_POST['bank_name']); $i++) {
            if(!trim($_POST['bank_name'][$i])) continue;

            $adm_bank[$i]['name'] = trim($this->input->post('bank_name')[$i]);
            $adm_bank[$i]['account'] = trim($this->input->post('bank_account')[$i]);
            $adm_bank[$i]['holder'] = trim($this->input->post('bank_holder')[$i]);
        }

        unset($value);
        $value['de_bank_account']		= serialize($adm_bank);
        $value['de_bank_use']			= $this->input->post('de_bank_use'); // 무통장입금
        $value['de_card_use']			= $this->input->post('de_card_use'); // 신용카드
        $value['de_iche_use']			= $this->input->post('de_iche_use'); // 계좌이체
        $value['de_vbank_use']			= $this->input->post('de_vbank_use'); // 가상계좌
        $value['de_hp_use']				= $this->input->post('de_hp_use'); // 휴대폰
        $value['de_card_test']			= $this->input->post('de_card_test'); // 결제 테스트
        $value['de_pg_service']			= $this->input->post('de_pg_service'); // 결제대행사
        $value['de_tax_flag_use']		= $this->input->post('de_tax_flag_use'); // 복합과세 결제
        $value['de_taxsave_use']		= $this->input->post('de_taxsave_use'); // 현금영수증 발급사용
        $value['de_card_noint_use']		= $this->input->post('de_card_noint_use'); // 신용카드 무이자할부사용
        $value['de_easy_pay_use']		= $this->input->post('de_easy_pay_use'); // PG사 간편결제 버튼사용
        $value['de_escrow_use']			= $this->input->post('de_escrow_use'); // Escrow 사용여부
        $value['de_kcp_mid']			= $this->input->post('de_kcp_mid'); // NHN KCP SITE CODE
        $value['de_kcp_site_key']		= $this->input->post('de_kcp_site_key'); // NHN KCP SITE KEY
        $value['de_lg_mid']				= $this->input->post('de_lg_mid'); // LG유플러스 상점아이디
        $value['de_lg_mert_key']		= $this->input->post('de_lg_mert_key'); // LG유플러스 MertKey
        $value['de_inicis_mid']			= $this->input->post('de_inicis_mid'); // KG이니시스 상점아이디
        $value['de_inicis_admin_key']	= $this->input->post('de_inicis_admin_key'); // KG이니시스 키패스워드
        $value['de_inicis_sign_key']	= $this->input->post('de_inicis_sign_key'); // KG이니시스 웹결제 사인키
        $value['de_samsung_pay_use']	= $this->input->post('de_samsung_pay_use'); // KG이니시스 삼성페이 버튼
        $this->query_model->updateObj("shop_partner",$value,"mb_id='$member[id]'");

        goto_url(base_url().'mypage/page?code=partner_pg');
    }
    public function partner_naverpay_update(){
        $pf_auth_pg=$this->load->get_var('pf_auth_pg');
        $member=$this->load->get_var('member');
        if(!$pf_auth_pg)
            alert('���� PG���� ��� ������ �����ϴ�.');

        unset($value);
        $value['de_naverpay_mid']		 = $this->input->post('de_naverpay_mid');
        $value['de_naverpay_cert_key']	 = $this->input->post('de_naverpay_cert_key');
        $value['de_naverpay_button_key'] = $this->input->post('de_naverpay_button_key');
        $value['de_naverpay_test']		 = $this->input->post('de_naverpay_test');
        $value['de_naverpay_mb_id']		 = $this->input->post('de_naverpay_mb_id');
        $value['de_naverpay_sendcost']	 = $this->input->post('de_naverpay_sendcost');
        $this->query_model->updateObj("shop_partner",$value,"mb_id='$member[id]'");

        goto_url(base_url().'mypage/page?code=partner_naverpay');
    }
    public function partner_logo_update(){
        $member=$this->load->get_var('member');
        $basic_logo_del=$this->input->post('basic_logo_del');
        $mobile_logo_del=$this->input->post('mobile_logo_del');
        $sns_logo_del=$this->input->post('sns_logo_del');
        $favicon_ico_del=$this->input->post('favicon_ico_del');

        $uploadPath="./data/banner";
        $configArr = array();
        $configArr['upload_path'] = $uploadPath;

        $configArr['allowed_types'] = $this->allowed_types;
        $configArr['max_size'] = $this->max_size;
        $configArr['max_width'] = $this->max_width;
        $configArr['max_height'] = $this->max_height;
        $configArr['remove_spaces'] = $this->remove_spaces;
        $configArr['encrypt_name'] = $this->encrypt_name;
        $this->load->library('upload', $configArr);
        $this->upload->initialize($configArr);

        unset($value);
        if($ico = $_FILES['favicon_ico']['name']) {
            if(!preg_match("/(\.ico)$/i", $ico))
                alert("파비콘 아이콘은 ico 파일만 업로드 가능합니다.");
        }
        $sql="select * from shop_logo where mb_id = ?";
        $param=array($member['id']);
        $lg = $this->query_model->returnOneArr($sql,$param);
        if($basic_logo_del) {
            unlink($uploadPath.'/'.$lg['basic_logo']);
            $value['basic_logo'] = '';
        }
        if($mobile_logo_del) {
            unlink($uploadPath.'/'.$lg['mobile_logo']);
            $value['mobile_logo'] = '';
        }
        if($sns_logo_del) {
            unlink($uploadPath.'/'.$lg['sns_logo']);
            $value['sns_logo'] = '';
        }
        if($favicon_ico_del) {
            unlink($uploadPath.'/'.$lg['favicon_ico']);
            $value['favicon_ico'] = '';
        }

        $error="";
        if(isset($_FILES['basic_logo']['name'])&&$_FILES['basic_logo']['name']!="") {
            if(!$this->upload->do_upload('basic_logo')){
                $error = $this->upload->display_errors();
            }else{
                $uploadFile=$this->upload->data();
                $new_file2=$uploadFile['file_name'];
                $value['basic_logo']=$new_file2;
            }
        }
        if(isset($_FILES['mobile_logo']['name'])&&$_FILES['mobile_logo']['name']!="") {
            if(!$this->upload->do_upload('mobile_logo')){
                $error = $this->upload->display_errors();
            }else{
                $uploadFile=$this->upload->data();
                $new_file2=$uploadFile['file_name'];
                $value['mobile_logo']=$new_file2;
            }
        }
        if(isset($_FILES['sns_logo']['name'])&&$_FILES['sns_logo']['name']!="") {
            if(!$this->upload->do_upload('sns_logo')){
                $error = $this->upload->display_errors();
            }else{
                $uploadFile=$this->upload->data();
                $new_file2=$uploadFile['file_name'];
                $value['sns_logo']=$new_file2;
            }
        }
        if(isset($_FILES['favicon_ico']['name'])&&$_FILES['favicon_ico']['name']!="") {
            if(!$this->upload->do_upload('favicon_ico')){
                $error = $this->upload->display_errors();
            }else{
                $uploadFile=$this->upload->data();
                $new_file2=$uploadFile['file_name'];
                $value['favicon_ico']=$new_file2;
            }
        }
        if($error!=""){
            echo "<script>alert('".strip_tags($error)."')</script>";
        }

        $value['mb_id'] = $member['id'];

        if($lg['index_no']) {
            $this->query_model->updateObj("shop_logo", $value, "mb_id = '{$member['id']}'");
        } else {
            $this->query_model->insertObj("shop_logo", $value);
        }

        goto_url(base_url().'mypage/page?code=partner_logo');
    }
    public function partner_banner_list_update(){
        $member=$this->load->get_var('member');
        $mod_type=$this->input->get('mod_type');
        $bn_use=$this->input->post('bn_use');
        $bn_width=$this->input->post('bn_width');
        $bn_height=$this->input->post('bn_height');
        $bn_target=$this->input->post('bn_target');
        $bn_link=$this->input->post('bn_link');
        $bn_order=$this->input->post('bn_order');
        $bn_id=$this->input->post('bn_id');
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        if($mod_type == 'R') {
            $this->funcomplex_lib->check_banner_copy($member['id'], 'pc');
            goto_url(base_url()."mypage/page?code=partner_banner_list");
        } else {
            $count = count($_POST['chk']);
            if(!$count) {
                alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
            }

            if($_POST['act_button'] == "선택수정"){
                for($i=0; $i<$count; $i++){
                    // 실제 번호를 넘김
                    $k = $_POST['chk'][$i];
                    $param['bn_use']=$bn_use[$k];
                    $param['bn_width']=$bn_width[$k];
                    $param['bn_height']=$bn_height[$k];
                    $param['bn_target']=$bn_target[$k];
                    $param['bn_link']=$bn_link[$k];
                    $param['bn_order']=$bn_order[$k];
                    $this->query_model->updateObj('shop_banner',$param,"bn_id = '{$bn_id[$k]}'");
                }
            }else if($_POST['act_button'] == "선택삭제"){
                $upl_dir = "./data/banner";
                for($i=0; $i<$count; $i++){
                    // 실제 번호를 넘김
                    $k = $_POST['chk'][$i];

                    $bn_id = trim($_POST['bn_id'][$k]);
                    $sql="select * from shop_banner where bn_id=?";
                    $param=array($bn_id);
                    $row = $this->query_model->returnOneArr($sql,$param);

                    unlink($upl_dir.'/'.$row['bn_file']);
                    $sql="delete from shop_banner where bn_id = ?";
                    $this->query_model->returnNull($sql,$param);
                }
            }

            goto_url(base_url()."mypage/page?$q1&page=$page");
        }
    }
    public function partner_banner_form_update(){
        $member=$this->load->get_var('member');
        $qstr=$this->load->get_var('qstr');
        $w=$this->input->post('w');

        $uploadPath="./data/banner";
        $configArr = array();
        $configArr['upload_path'] = $uploadPath;
        
        $configArr['allowed_types'] = $this->allowed_types;
        $configArr['max_size'] = $this->max_size;
        $configArr['max_width'] = $this->max_width;
        $configArr['max_height'] = $this->max_height;
        $configArr['remove_spaces'] = $this->remove_spaces;
        $configArr['encrypt_name'] = $this->encrypt_name;
        $this->load->library('upload', $configArr);
        $this->upload->initialize($configArr);

        $bn_id=$this->input->post('bn_id');
        $page=$this->input->post('page');
        $bn_file_del=$this->input->post('bn_file_del');
        unset($value);
        if($bn_file_del) {
            unlink($uploadPath.'/'.$bn_file_del);
            $value['bn_file'] = '';
        }
        $error="";
        if(isset($_FILES['bn_file']['name'])&&$_FILES['bn_file']['name']!="") {
            if(!$this->upload->do_upload('bn_file')){
                $error = $this->upload->display_errors();
            }else{
                $uploadFile=$this->upload->data();
                $new_file=$uploadFile['file_name'];
                $value['bn_file'] = $new_file;
            }
        }
        if($error!=""){
            echo "<script>alert('".strip_tags($error)."')</script>";
        }

        $value['mb_id']		= $member['id'];
        $value['bn_device'] = 'pc';
        $value['bn_theme']	= $member['theme'];
        $value['bn_code']	= $this->input->post('bn_code');
        $value['bn_link']	= $this->input->post('bn_link');
        $value['bn_target'] = $this->input->post('bn_target');
        $value['bn_width']	= $this->input->post('bn_width');
        $value['bn_height'] = $this->input->post('bn_height');
        $value['bn_bg']		= preg_replace("/([^a-zA-Z0-9])/", "", $this->input->post('bn_bg'));
        $value['bn_text']	= $this->input->post('bn_text');
        $value['bn_use']	= $this->input->post('bn_use');
        $value['bn_order']	= $this->input->post('bn_order');

        if($w == "") {
            $bn_id = $this->query_model->insertObj("shop_banner", $value);
            goto_url(base_url()."mypage/page?code=partner_banner_form&w=u&bn_id=$bn_id");
        } else if($w == "u") {
            $this->query_model->updateObj("shop_banner", $value," bn_id='$bn_id'");
            goto_url(base_url()."mypage/page?code=partner_banner_form&w=u&bn_id=$bn_id$qstr&page=$page");
        }
    }
    public function partner_mbanner_list_update(){
        $member=$this->load->get_var('member');
        $mod_type=$this->input->get('mod_type');
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        if($mod_type == 'R') {
            $this->funcomplex_lib->check_banner_copy($member['id'], 'mobile');

            goto_url(base_url()."mypage/page?code=partner_mbanner_list");

        } else {
            $count = count($_POST['chk']);
            if(!$count) {
                alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
            }

            if($_POST['act_button'] == "선택수정"){
                $bn_use=$this->input->post('bn_use');
                $bn_width=$this->input->post('bn_width');
                $bn_height=$this->input->post('bn_height');
                $bn_target=$this->input->post('bn_target');
                $bn_link=$this->input->post('bn_link');
                $bn_order=$this->input->post('bn_order');
                $bn_id=$this->input->post('bn_id');
                for($i=0; $i<$count; $i++){
                    // 실제 번호를 넘김
                    $k = $_POST['chk'][$i];
                    $param['bn_use']=$bn_use[$k];
                    $param['bn_width']=$bn_width[$k];
                    $param['bn_height']=$bn_height[$k];
                    $param['bn_target']=$bn_target[$k];
                    $param['bn_link']=$bn_link[$k];
                    $param['bn_order']=$bn_order[$k];
                    $this->query_model->updateObj('shop_banner',$param,"bn_id = '{$bn_id[$k]}'");
                }
            }else if($_POST['act_button'] == "선택삭제"){
                $upl_dir = "./data/banner";

                for($i=0; $i<$count; $i++){
                    // 실제 번호를 넘김
                    $k = $_POST['chk'][$i];

                    $bn_id = trim($_POST['bn_id'][$k]);
                    $sql="select * from shop_banner where bn_id=?";
                    $param=array($bn_id);
                    $row = $this->query_model->returnOneArr($sql,$param);

                    unlink($upl_dir.'/'.$row['bn_file']);
                    $sql="delete from shop_banner where bn_id =?";
                    $param=array($bn_id);
                    $this->query_model->returnNull($sql,$param);
                }
            }
            
            goto_url(base_url()."mypage/page?$q1&page=$page");
        }
    }
    public function partner_mbanner_form_update(){
        $uploadPath="./data/banner";
        $configArr = array();
        $configArr['upload_path'] = $uploadPath;
        
        $configArr['allowed_types'] = $this->allowed_types;
        $configArr['max_size'] = $this->max_size;
        $configArr['max_width'] = $this->max_width;
        $configArr['max_height'] = $this->max_height;
        $configArr['remove_spaces'] = $this->remove_spaces;
        $configArr['encrypt_name'] = $this->encrypt_name;
        $this->load->library('upload', $configArr);
        $this->upload->initialize($configArr);

        $bn_file_del=$this->input->post('bn_file_del');
        $w=$this->input->post('w');
        $member=$this->load->get_var('member');
        $bn_id=$this->input->post('bn_id');
        $page=$this->input->post('page');
        unset($value);
        if($bn_file_del) {
            unlink($uploadPath.'/'.$bn_file_del);
            $value['bn_file'] = '';
        }
        $error="";
        if(isset($_FILES['bn_file']['name'])&&$_FILES['bn_file']['name']!="") {
            if(!$this->upload->do_upload('bn_file')){
                $error = $this->upload->display_errors();
            }else{
                $uploadFile=$this->upload->data();
                $new_file=$uploadFile['file_name'];
                $value['bn_file'] = $new_file;
            }
        }
        if($error!=""){
            echo "<script>alert('".strip_tags($error)."')</script>";
        }

        $value['mb_id']		= $member['id'];
        $value['bn_device']	= 'mobile';
        $value['bn_theme']	= $member['mobile_theme'];
        $value['bn_code']	= $this->input->post('bn_code');
        $value['bn_link']	= $this->input->post('bn_link');
        $value['bn_target'] = $this->input->post('bn_target');
        $value['bn_width']	= $this->input->post('bn_width');
        $value['bn_height']	= $this->input->post('bn_height');
        $value['bn_bg']		= preg_replace("/([^a-zA-Z0-9])/", "", $_POST['bn_bg']);
        $value['bn_text']	= $this->input->post('bn_text');
        $value['bn_use']	= $this->input->post('bn_use');
        $value['bn_order']	= $this->input->post('bn_order');

        if($w == "") {
            $bn_id = $this->query_model->insertObj("shop_banner", $value);

            goto_url(base_url()."mypage/page?code=partner_mbanner_form&w=u&bn_id=$bn_id");
        } else if($w == "u") {
            $this->query_model->updateObj("shop_banner", $value,"bn_id='$bn_id'");

            goto_url(base_url()."mypage/page?code=partner_mbanner_form&w=u&bn_id=$bn_id$qstr&page=$page");
        }
    }
    public function partner_menu_form_update(){
        $gw_menu=$this->config->item('gw_menu');
        $member=$this->load->get_var('member');
        unset($value);
        for($i=0; $i<count($gw_menu); $i++) {
            $seq = ($i+1);
            $value['de_pname_use_'.$seq] = $_POST['de_pname_use_'.$seq];
            $value['de_pname_'.$seq]	 = $_POST['de_pname_'.$seq];
        }
        $this->query_model->updateObj("shop_partner",$value,"mb_id='$member[id]'");

        goto_url(base_url().'mypage/page?code=partner_menu_form');
    }
    public function partner_best_item_update(){
        $member=$this->load->get_var('member');
        $arr_best = array();
        for($i=0; $i<count($_POST['maintype_subj']); $i++) {
            if(!trim($_POST['maintype_subj'][$i])) 
                continue;

            $arr_best[$i]['subj'] = trim($_POST['maintype_subj'][$i]);
            $arr_best[$i]['code'] = preg_replace("/\s+/", "", $_POST['maintype_code'][$i]);
        }

        $de_maintype_best = base64_encode(serialize($arr_best));

        unset($value);
        $value['de_maintype_title'] = $this->input->post('de_maintype_title');
        $value['de_maintype_best'] = $de_maintype_best;
        $this->query_model->updateObj("shop_partner",$value,"mb_id='$member[id]'");

        goto_url(base_url().'mypage/page?code=partner_best_item');
    }
    public function partner_popup_form_update(){
        $title=$this->input->post('title');
        $member=$this->load->get_var('member');
        $qstr=$this->load->get_var('qstr');
        $w=$this->input->post('w');
        $pp_id=$this->input->post('pp_id');
        $page=$this->input->post('page');
        if(!$title)
            alert("제목을 입력하세요.");

        unset($value);
        $value['device']	 = $this->input->post('device');
        $value['width']		 = $this->input->post('width');
        $value['height']	 = $this->input->post('height');
        $value['lefts']		 = $this->input->post('lefts');
        $value['top']		 = $this->input->post('top');
        $value['title']		 = $this->input->post('title');
        $value['state']		 = $this->input->post('state');
        $value['begin_date'] = $this->input->post('begin_date');
        $value['end_date']	 = $this->input->post('end_date');
        $value['memo']		 = $this->input->post('memo');

        if($w == "") {
            $value['mb_id'] = $member['id'];
            $pp_id = $this->query_model->insertObj("shop_popup", $value);

            goto_url(base_url()."mypage/page?code=partner_popup_form&w=u&pp_id=$pp_id");
        } else if($w == "u") {
            $this->query_model->updateObj("shop_popup", $value, "index_no='$pp_id'");

            goto_url(base_url()."mypage/page?code=partner_popup_form&w=u&pp_id=$pp_id$qstr&page=$page");
        }
    }
    public function partner_popup_list_update(){
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        $count = count($_POST['chk']);
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }

        if($_POST['act_button'] == "선택수정"){
            $title=$this->input->post('title');
            $device=$this->input->post('device');
            $width=$this->input->post('width');
            $height=$this->input->post('height');
            $top=$this->input->post('top');
            $lefts=$this->input->post('lefts');
            $pp_id=$this->input->post('pp_id');
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $param['title']=$title[$k];
                $param['device']=$device[$k];
                $param['width']=$width[$k];
                $param['height']=$height[$k];
                $param['top']=$top[$k];
                $param['lefts']=$lefts[$k];
                $this->query_model->updateObj('shop_popup',$param,"index_no = '{$pp_id[$k]}'");
            }
        }else if($_POST['act_button'] == "선택삭제"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $pp_id = trim($_POST['pp_id'][$k]);

                // 삭제
                $sql = "select memo from shop_popup where index_no='$pp_id' ";
                $row = $this->query_model->returnOneArr($sql);

                $this->funcomplex_lib->delete_editor_image($row['memo']);
                $sql="delete from shop_popup where index_no=?";
                $param=array($pp_id);
                $this->query_model->returnNull($sql,$param);
            }
        }

        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function member_use_app(){
        $mb = $this->global_lib->get_member($mb_id, 'use_app');
        if($mb['use_app'] == '0')
            $t_value = '1';
        else
            $t_value = '0';

        $sql = " update shop_member set use_app=? where id=?";
        $param=array($t_value,$mb_id);
        $result = $this->query_model->returnNull($sql,$param);

        if($result) 
            die("{\"error\":\"\"}"); // 정상
        else 
            die("{\"error\":\"일시적인 오류\"}");
    }
    public function partner_register_form_update(){
        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $config=$this->load->get_var('config');
        
        $mb_id=trim($this->input->post('mb_id'));
        $mb_password=trim($this->input->post('mb_password'));
        $mb_name=trim($this->input->post('mb_name'));
        $mb_email=trim($this->input->post('mb_email'));
        $mb_hp=trim($this->input->post('mb_hp'));
        $mb_tel=trim($this->input->post('mb_tel'));
        $mb_zip=trim($this->input->post('mb_zip'));
        $mb_addr1=trim($this->input->post('mb_addr1'));
        $mb_addr2=trim($this->input->post('mb_addr2'));
        $mb_addr3=trim($this->input->post('mb_addr3'));
        $mb_addr_jibeon=trim($this->input->post('mb_addr_jibeon'));
        $mb_recommend=trim($this->input->post('mb_recommend'));
        $mb_mailling=trim($this->input->post('mb_mailling'));
        $mb_sms=trim($this->input->post('mb_sms'));
        $mb_certify=trim($this->input->post('mb_certify'));
        $mb_adult=trim($this->input->post('mb_adult'));

        // 휴대폰번호 체크
        $mb_hp = hyphen_hp_number($_POST['mb_hp']);
        if($mb_hp) {
            $result = $this->funcomplex_lib->exist_mb_hp($mb_hp, $mb_id);
            if($result)
                alert($result);
        }
        $mb_certify_case=$this->input->post('mb_certify_case');
        $mb_certify=$this->input->post('mb_certify');
        // 인증정보처리
        if($mb_certify_case && $mb_certify) {
            $mb_certify = $mb_certify_case;
            $mb_adult = $this->input->post('mb_adult');
        } else {
            $mb_certify = '';
            $mb_adult = 0;
        }

        $mb = $this->global_lib->get_member($mb_id);
        if($mb['id'])
            alert('이미 존재하는 회원아이디입니다.\\nＩＤ : '.$mb['id'].'\\n이름 : '.$mb['name'].'\\n메일 : '.$mb['email']);

        // 이메일중복체크
        $mb_email=$this->input->post('mb_email');
        $sql = " select id, name, email from shop_member where email = ? and id <> ?";
        $param=array($mb_email, $mb_id);
        $row = $this->query_model->returnOneArr($sql,$param);
        if($row['id'])
            alert('이미 존재하는 이메일입니다.\\nＩＤ : '.$row['id'].'\\n이름 : '.$row['name'].'\\n메일 : '.$row['email']);

        unset($value);
        $value['id']			= $mb_id; //회원아이디
        $value['passwd']		= $mb_password; //비밀번호
        $value['name']			= $mb_name; //이름
        $value['email']			= $mb_email; //이메일
        $value['cellphone']		= $mb_hp; //핸드폰
        $value['telephone']		= $mb_tel;	 //전화번호
        $value['zip']			= $mb_zip; //우편번호
        $value['addr1']			= $mb_addr1; //주소
        $value['addr2']			= $mb_addr2; //상세주소
        $value['addr3']			= $mb_addr3; //참고항목
        $value['addr_jibeon']	= $mb_addr_jibeon; //지번주소
        $value['today_login']	= $OM_TIME_YMDHIS; //최근 로그인일시
        $value['reg_time']		= $OM_TIME_YMDHIS; //가입일시
        $value['mb_ip']			= $_SERVER['REMOTE_ADDR']; //IP
        $value['grade']			= '9'; //레벨
        $value['pt_id']			= $mb_recommend; //추천인아이디
        $value['login_ip']		= $_SERVER['REMOTE_ADDR']; //최근 로그인IP
        $value['mailser']		= $mb_mailling ? $mb_mailling : 'N'; //E-Mail을 수신
        $value['smsser']		= $mb_sms ? $mb_sms : 'N'; //SMS를 수신
        $value['mb_certify']	= $mb_certify;
        $value['mb_adult']		= $mb_adult;

        $value['use_app']="";
        // 관리자인증을 사용하지 않는다면 인증으로 간주함.
        if(!$config['cert_admin_yes'])
            $value['use_app']	= '1';

        $this->query_model->insertObj("shop_member", $value);

        alert("회원가입이 완료 되었습니다.", base_url().'mypage/page?code=partner_register_form');
    }
    public function partner_goods_admlist_update(){
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        $member=$this->load->get_var('member');
        $count = count($_POST['chk']);
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }

        if($_POST['act_button'] == "선택상품감춤"){
            $gs_id=$this->input->post('gs_id');
            $chk=$this->input->post('chk');
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $chk[$i];
                

                $gs = $this->global_lib->get_goods($_POST['gs_id'][$k], 'use_hide'); 
                $gs['use_hide'] = preg_replace('/\s+/', '', $gs['use_hide']);
                $s_value = "";

                $list_data = explode(",", $gs['use_hide']);
                $list_data = array_diff($list_data, array($member['id'])); // 적용 아이디 모두제거
                array_push($list_data, $member['id']);  // 적용아이디 배열에 추가
                $list_data = array_unique($list_data); //중복된 아이디 제거
                $list_data = array_filter($list_data); // 빈 배열 요소를 제거
                $list_data = array_values($list_data); // index 값 주기
                $s_value = implode(",", $list_data);

                $sql = " update shop_goods set use_hide = ? where index_no = ?"; 
                $param=array($s_value,$gs_id[$k]);
                $this->query_model->returnNull($sql,$param);
            }
        } else if($_POST['act_button'] == "선택상품노출") {
            $gs_id=$this->input->post('gs_id');
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $gs =$this->global_lib->get_goods($_POST['gs_id'][$k], 'use_hide'); 
                $gs['use_hide'] = preg_replace('/\s+/', '', $gs['use_hide']);
                $s_value = "";

                $list_data = explode(",", $gs['use_hide']);
                $list_data = array_diff($list_data, array($member['id'])); // 적용 아이디 모두제거
                $list_data = array_unique($list_data); //중복된 아이디 제거			
                $list_data = array_filter($list_data); // 빈 배열 요소를 제거
                $list_data = array_values($list_data); // index 값 주기
                $s_value = implode(",", $list_data);

                $sql = " update shop_goods set use_hide = ? where index_no = ?"; 
                $param=array($s_value,$gs_id[$k]);
                $this->query_model->returnNull($sql,$param);
            }
        }

        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function partner_order_admlist_excel(){
        $member=$this->load->get_var('member');

        $fr_date=$this->input->get('fr_date');
        $to_date=$this->input->get('to_date');
        $sfl=$this->input->get('sfl');
        $stx=$this->input->get('stx');
        $od_settle_case=$this->input->get('od_settle_case');
        $od_status=$this->input->get('od_status');
        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $sql_common = " from shop_order ";
        $sql_search = " where dan != 0 and pt_id = '{$member['id']}' ";

        if($sfl && $stx)
            $sql_search .= " and $sfl like '%$stx%' ";

        if($od_settle_case)
            $sql_search .= " and paymethod = '$od_settle_case' ";

        if(is_numeric($od_status))
            $sql_search .= " and dan = '$od_status' ";

        if($fr_date && $to_date)
            $sql_search .= " and left({$sel_field},10) between '$fr_date' and '$to_date' ";
        else if($fr_date && !$to_date)
            $sql_search .= " and left({$sel_field},10) between '$fr_date' and '$fr_date' ";
        else if(!$fr_date && $to_date)
            $sql_search .= " and left({$sel_field},10) between '$to_date' and '$to_date' ";

        $sql_order = " order by od_time desc, index_no asc ";

        $sql = " select * {$sql_common} {$sql_search} {$sql_order} ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        if(!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++.'1', '가맹점ID')
            ->setCellValue($char++.'1', '회원ID')
            ->setCellValue($char++.'1', '주문번호')
            ->setCellValue($char++.'1', '일련번호')
            ->setCellValue($char++.'1', '상품명')
            ->setCellValue($char++.'1', '옵션')
            ->setCellValue($char++.'1', '수량')
            ->setCellValue($char++.'1', '상품금액')
            ->setCellValue($char++.'1', '쿠폰할인')
            ->setCellValue($char++.'1', '포인트결제')
            ->setCellValue($char++.'1', '배송비')
            ->setCellValue($char++.'1', '실결제금액')
            ->setCellValue($char++.'1', '총주문금액')
            ->setCellValue($char++.'1', '판매수수료')
            ->setCellValue($char++.'1', '결제방법')
            ->setCellValue($char++.'1', '주문상태')
            ->setCellValue($char++.'1', '주문채널')
            ->setCellValue($char++.'1', '주문자명')
            ->setCellValue($char++.'1', '주문일시');

        $i=2;
        foreach($result as $key => $row){
            $gs = unserialize($row['od_goods']);
            $amount = $this->global_lib->get_order_spay($row['od_id']);
            $sodr = $this->funcomplex_lib->excel_order_list($row, $amount);

            $psql = " select pp_pay
                        from shop_partner_pay
                    where pp_rel_table = 'sale'
                        and pp_rel_id = ?
                        and pp_rel_action = ?
                        and mb_id = ?";
            $param=array($row['od_no'],$row['od_id'],$row['pt_id']);
            $psum = $this->query_model->returnOneArr($psql,$param);
            $pp_pay = (int)$psum['pp_pay'];

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++.$i, $sodr['od_pt_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['od_mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['od_id'].$sodr['od_test'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['od_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $gs['gname'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['it_options'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['sum_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['goods_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['coupon_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['use_point'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['baesong_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['use_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['goods_price']+$row['baesong_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $pp_pay, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $sodr['od_paytype'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $gw_status[$row['dan']], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['od_mobile'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['od_time'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('본사 상품판매실적');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);
        return false;
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="본사 상품판매실적-'.date("ymd", time()).'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function pt_category_update(){
        $mb = $this->global_lib->get_member($mb_id, 'id');
        $ca_no=$this->input->post('ca_no');
        $mb_id=$this->input->post('mb_id');
        if(!$mb['id']) {
            die("{\"error\":\"회원아이디가 존재하지 않습니다.\"}");
        }
        $sql="select * from shop_category where index_no = ?";
        $param=array($ca_no);
        $cat = $this->query_model->returnOneArr($sql,$param);
        $cat['catehide'] = preg_replace('/\s+/', '', $cat['catehide']);

        $count_yes = false;
        if(in_array($mb_id, explode(",", $cat['catehide']))) {
            $count_yes = true;
        }

        $s_value = "";

        if(!$count_yes) {
            $list_data = explode(",", $cat['catehide']);
            $list_data = array_diff($list_data, array($mb_id)); // 적용 아이디 모두제거
            array_push($list_data, $mb_id);  // 적용아이디 배열에 추가
            $list_data = array_unique($list_data); //중복된 아이디 제거
            $list_data = array_filter($list_data); // 빈 배열 요소를 제거
            $list_data = array_values($list_data); // index 값 주기
            $s_value = implode(",", $list_data);
        } else {
            $list_data = explode(",", $cat['catehide']);
            $list_data = array_diff($list_data, array($mb_id)); // 적용 아이디 모두제거
            $list_data = array_unique($list_data); //중복된 아이디 제거
            $list_data = array_filter($list_data); // 빈 배열 요소를 제거
            $list_data = array_values($list_data); // index 값 주기
            $s_value = implode(",", $list_data);
        }

        $len = strlen($cat['catecode']);
        $sql = "update shop_category set catehide = ? where SUBSTRING(catecode,1,$len) = ?";
        $param=array($s_value,$cat['catecode']);
        $result = $this->query_model->returnNull($sql,$param);

        if($result)
            die("{\"error\":\"\"}"); // 정상
        else
            die("{\"error\":\"일시적인 오류\"}");
    }
    public function partner_goods_type_update(){
        $member=$this->load->get_var('member');
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');

        $mb_id = $member['id'];
        $gs_id=$this->input->post('gs_id');
        for($i=0; $i<count($_POST['gs_id']); $i++){
            $sql = " select count(*) as cnt 
                        from shop_goods_type
                    where mb_id = ?
                        and gs_id = ?";
            $param=array($mb_id,$gs_id[$i]);
            $row = $this->query_model->returnOneArr($sql,$param);
            if($row['cnt']) {
                $it_type1[$i]=isset($_POST['it_type1'][$i])?$_POST['it_type1'][$i]:0;
                $it_type2[$i]=isset($_POST['it_type2'][$i])?$_POST['it_type2'][$i]:0;
                $it_type3[$i]=isset($_POST['it_type3'][$i])?$_POST['it_type3'][$i]:0;
                $it_type4[$i]=isset($_POST['it_type4'][$i])?$_POST['it_type4'][$i]:0;
                $it_type5[$i]=isset($_POST['it_type5'][$i])?$_POST['it_type5'][$i]:0;
                $gs_id=$this->input->post('gs_id');
                $sql = "update shop_goods_type set it_type1 = ?, it_type2 = ?, it_type3 = ?, it_type4 = ?, it_type5 = ? where mb_id = ? and gs_id = ? ";
                $param=array($it_type1[$i],$it_type2[$i],$it_type3[$i],$it_type4[$i],$it_type5[$i],$mb_id,$gs_id[$i]);
                $this->query_model->returnNull($sql,$param);
            } else {
                $gs_id[$i]=$this->input->post('gs_id');
                $it_type1[$i]=isset($_POST['it_type1'][$i])?$_POST['it_type1'][$i]:0;
                $it_type2[$i]=isset($_POST['it_type2'][$i])?$_POST['it_type2'][$i]:0;
                $it_type3[$i]=isset($_POST['it_type3'][$i])?$_POST['it_type3'][$i]:0;
                $it_type4[$i]=isset($_POST['it_type4'][$i])?$_POST['it_type4'][$i]:0;
                $it_type5[$i]=isset($_POST['it_type5'][$i])?$_POST['it_type5'][$i]:0;
                $sql = "insert into shop_goods_type 
                        set mb_id = ?, gs_id = ?, it_type1 = ?, it_type2 = ?, it_type3 = ?, it_type4 = ?, it_type5 = ?";
                $param=array($mb_id,$gs_id[$i],$it_type1[$i],$it_type2[$i],$it_type3[$i],$it_type4[$i],$it_type5[$i]);
                $this->query_model->returnNull($sql,$param);
            }
        }

        goto_url(base_url()."mypage/page?{$q1}&page={$page}");
    }
    public function partner_goods_plan_update(){
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');

        $count = count($_POST['chk']);
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }

        if($_POST['act_button'] == "선택수정"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];
                $pl_name[$k]=isset($_POST['pl_name'][$k])?$_POST['pl_name'][$k]:"";
                $pl_use[$k]=isset($_POST['pl_use'][$k])?$_POST['pl_use'][$k]:"";
                $pl_no[$k]=isset($_POST['pl_no'][$k])?$_POST['pl_no'][$k]:"";

                $sql = "update shop_goods_plan set pl_name = ?, pl_use = ? where pl_no = ?";
                $param=array($pl_name[$k],$pl_use[$k],$pl_no[$k]);
                $this->query_model->returnNull($sql,$param);
            }
        }else if($_POST['act_button'] == "선택삭제"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $pl_no = trim($_POST['pl_no'][$k]);

                $sql="select * from shop_goods_plan where pl_no = ?";
                $param=array($pl_no);
                $row = $this->query_model->returnOneArr($sql,$param);
                @unlink("./data/plan/".$row['pl_limg']);
                @unlink("./data/plan/".$row['pl_bimg']);
                $sql2="delete from shop_goods_plan where pl_no=?";
                $this->query_model->returnNull($sql2,$param);
            }
        }

        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function partner_goods_plan_form_update(){
        $member=$this->load->get_var('member');

        $uploadPath="./data/plan";
        $configArr = array();
        $configArr['upload_path'] = $uploadPath;
        
        $configArr['allowed_types'] = $this->allowed_types;
        $configArr['max_size'] = $this->max_size;
        $configArr['max_width'] = $this->max_width;
        $configArr['max_height'] = $this->max_height;
        $configArr['remove_spaces'] = $this->remove_spaces;
        $configArr['encrypt_name'] = $this->encrypt_name;
        $this->load->library('upload', $configArr);
        $this->upload->initialize($configArr);

        $w=$this->input->post('w');
        unset($value);
        $pl_limg_del=$this->input->post('pl_limg_del');
        $pl_bimg_del=$this->input->post('pl_bimg_del');
        if($pl_limg_del) {
            unlink($uploadPath.'/'.$pl_limg_del);
            $value['pl_limg'] = '';
        }
        if($pl_bimg_del) {
            unlink($uploadPath.'/'.$pl_bimg_del);
            $value['pl_bimg'] = '';
        }
        if(isset($_FILES['pl_limg']['name'])&&$_FILES['pl_limg']['name']!="") {
            if(!$this->upload->do_upload('pl_limg')){
                $error = $this->upload->display_errors();
            }else{
                $uploadFile=$this->upload->data();
                $new_file=$uploadFile['file_name'];
                $value['pl_limg'] = $new_file;
            }
        }
        if(isset($_FILES['pl_bimg']['name'])&&$_FILES['pl_bimg']['name']!="") {
            if(!$this->upload->do_upload('pl_bimg')){
                $error = $this->upload->display_errors();
            }else{
                $uploadFile=$this->upload->data();
                $new_file=$uploadFile['file_name'];
                $value['pl_bimg'] = $new_file;
            }
        }

        $value['mb_id']	     = $member['id'];
        $value['pl_name']	 = $_POST['pl_name'];
        $value['pl_it_code'] = $_POST['pl_it_code'];
        $value['pl_use']	 = $_POST['pl_use'];

        if($w == '') {
            $pl_no = $this->query_model->insertObj("shop_goods_plan", $value);

            goto_url(base_url()."mypage/page?code=partner_goods_plan_form&w=u&pl_no=$pl_no");

        } else if($w == 'u') {
            $pl_no=$this->input->post('pl_no');
            $page=$this->input->post('page');
            $qstr=$this->load->get_var('qstr');
            $this->query_model->updateObj("shop_goods_plan", $value, "pl_no='$pl_no'");

            goto_url(base_url()."mypage/page?code=partner_goods_plan_form&w=u&pl_no=$pl_no$qstr&page=$page");
        }
    }
    public function partner_popular_list_delete(){
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        $count = count($_POST['chk']);
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }

        if($_POST['act_button'] == "선택삭제"){
            $pp_id=$this->input->post('pp_id');
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = " delete from shop_popular where pp_id = ?";
                $param=array($pp_id[$k]);
                $this->query_model->returnNull($sql,$param);
            }
        }

        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function partner_goods_form_update(){
        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());

        $gs_id=$this->input->post('gs_id');
        $gname=$this->input->post('gname');
        $ca_id=$this->input->post('ca_id');
        $w=$this->input->post('w');
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        $bak=$this->input->post('bak');
        $gs_list=$this->input->post('gs_list');

        $pf_auth_good=$this->config->item('pf_auth_good');
        if(!$pf_auth_good)
            alert('개별 상품판매 권한이 있어야만 이용 가능합니다.');

        // input vars 체크
        $this->funcomplex_lib->check_input_vars();

        $uploadPath="./data/goods";
        $configArr = array();
        $configArr['upload_path'] = $uploadPath;
        
        $configArr['allowed_types'] = $this->allowed_types;
        $configArr['max_size'] = $this->max_size;
        $configArr['max_width'] = $this->max_width;
        $configArr['max_height'] = $this->max_height;
        $configArr['remove_spaces'] = $this->remove_spaces;
        $configArr['encrypt_name'] = $this->encrypt_name;
        $this->load->library('upload', $configArr);
        $this->upload->initialize($configArr);

        if($gname == "") {
            alert("상품명을 입력하세요.");
        }

        if($ca_id == "" ) {
            alert("카테고리를 하나이상 선택하세요.");
        }

        // 관련상품을 우선 삭제함
        $sql="delete from shop_goods_relation where gs_id = ?";
        $param=array($gs_id);
        $this->query_model->returnNull($sql,$param);

        // 관련상품의 반대도 삭제
        $sql2="delete from shop_goods_relation where gs_id2 = ?";
        $this->query_model->returnNull($sql2,$param);

        // 관련상품 등록
        $gs_id2 = explode(",", $gs_list);
        for($i=0; $i<count($gs_id2); $i++){
            if(trim($gs_id2[$i])){
                $sql = " insert into shop_goods_relation
                            set gs_id  = ?,
                                gs_id2 = ?,
                                ir_no = ? ";
                $param=array($gs_id,$gs_id2[$i],$i);
                $this->query_model->returnNull($sql,$param);

                // 관련상품의 반대로도 등록
                $sql = " insert into shop_goods_relation
                            set gs_id  = ?,gs_id2 = ?,ir_no = ?";
                $param2=array($gs_id2[$i],$gs_id,$i);
                $this->query_model->returnNull($sql,$param2);
            }
        }

        // 기존 선택옵션삭제
        $sql3=" delete from shop_goods_option where io_type = '0' and gs_id =?";
        $this->query_model->returnNull($sql3,$param);

        $opt_id=$this->input->post('opt_id');
        $option_count = ($opt_id)?count($_POST['opt_id']):0;
        if($option_count) {
            // 옵션명
            $opt1_cnt = $opt2_cnt = $opt3_cnt = 0;
            for($i=0; $i<$option_count; $i++) {
                $opt_val = explode(' ', $opt_id[$i]);
                if($opt_val[0]) {
                    $opt1_subject = $this->input->post('opt1_subject');
                    $opt1_cnt++;
                } if($opt_val[1]) {
                    $opt2_subject = $this->input->post('opt2_subject');
                    $opt2_cnt++;
                } if($opt_val[2]) {
                    $opt3_subject = $this->input->post('opt3_subject');
                    $opt3_cnt++;
                }
            }

            if(isset($opt1_subject) && $opt1_cnt > 0) {
                $it_option_subject = $opt1_subject;
                if(isset($opt2_subject) && $opt2_cnt > 0)
                    $it_option_subject .= ','.$opt2_subject;
                if(isset($opt3_subject) && $opt3_cnt > 0)
                    $it_option_subject .= ','.$opt3_subject;
            }
        }

        // 기존 추가옵션삭제
        $sql4="delete from shop_goods_option where io_type = '1' and gs_id = ?";
        $this->query_model->returnNull($sql4,$param);

        $spl_id=$this->input->post('spl_id');
        $supply_count = ($spl_id)?count($_POST['spl_id']):0;
        if($supply_count) {
            // 추가옵션명
            $arr_spl = array();
            for($i=0; $i<$supply_count; $i++) {
                $spl_val = explode(' ', $_POST['spl_id'][$i]);
                if(!in_array($spl_val[0], $arr_spl))
                    $arr_spl[] = $spl_val[0];
            }

            $it_supply_subject = implode(',', $arr_spl);
        }

        // 상품 정보제공
        $value_array = array();
        for($i=0; $i<count($_POST['ii_article']); $i++) {
            $key = $_POST['ii_article'][$i];
            $val = $_POST['ii_value'][$i];
            $value_array[$key] = $val;
        }
        $it_info_value = addslashes(serialize($value_array));

        unset($value);
        if($_POST['simg_type']) { // URL 입력
            $value['simg1'] = $_POST['simg1'];
            $value['simg2'] = $_POST['simg2'];
            $value['simg3'] = $_POST['simg3'];
            $value['simg4'] = $_POST['simg4'];
            $value['simg5'] = $_POST['simg5'];
            $value['simg6'] = $_POST['simg6'];
        } else {
            for($i=1; $i<=6; $i++) {
                if($img = $_FILES['simg'.$i]['name']) {
                    if(!preg_match("/\.(gif|jpg|png)$/i", $img)) {
                        alert("이미지가 gif, jpg, png 파일이 아닙니다.");
                    }
                }
                if(isset($_POST['simg'.$i.'_del'])) {
                    unlink($uploadPath.'/'.$_POST['simg'.$i.'_del']);
                    $value['simg'.$i] = '';
                }
                if(isset($_FILES['simg'.$i]['name'])&&$_FILES['simg'.$i]['name']!="") {
                    if(!$this->upload->do_upload('simg'.$i)){
                        $error = $this->upload->display_errors();
                    }else{
                        $uploadFile=$this->upload->data();
                        $new_file=$uploadFile['file_name'];
                        $value['simg'.$i] = $new_file;
                    }
                }
            }
        }

        $it_option_subject=$this->input->post('it_option_subject');
        $it_supply_subject=$this->input->post('it_supply_subject');
        $stock_qty=$this->input->post('stock_qty');
        $noti_qty=$this->input->post('noti_qty');
        $sc_amt=$this->input->post('sc_amt');
        $sc_minimum=$this->input->post('sc_minimum');
        $sc_each_use=$this->input->post('sc_each_use');
        $buy_only=$this->input->post('buy_only');

        $value['use_aff']		= 1; //가맹점상품으로 설정
        $value['ca_id']			= $this->input->post('ca_id'); //대표카테고리
        $value['ca_id2']		= $this->input->post('ca_id2'); //추가카테고리2
        $value['ca_id3']		= $this->input->post('ca_id3'); //추가카테고리3
        $value['mb_id']			= $this->input->post('mb_id'); //업체코드
        $value['mb_name']       = $this->input->post('mb_name'); //업체명
        $value['gname']			= $this->input->post('gname'); //상품명
        $value['isopen']		= $this->input->post('isopen'); //진열상태
        $value['explan']		= $this->input->post('explan'); //짧은설명
        $value['keywords']		= $this->input->post('keywords'); //키워드
        $value['admin_memo']	= $this->input->post('admin_memo'); //관리자메모
        $value['memo']			= $this->input->post('memo'); //상품설명
        $value['goods_price']	= conv_number($this->input->post('goods_price')); //판매가격
        $value['supply_price']	= conv_number($this->input->post('supply_price')); //공급가격
        $value['normal_price']	= conv_number($this->input->post('normal_price')); //시중가격
        $value['gpoint']		= $this->funcomplex_lib->get_gpoint($value['goods_price'],$_POST['marper'],$_POST['gpoint']);
        $value['maker']			= $_POST['maker']; //제조사
        $value['origin']		= $_POST['origin']; //원산지
        $value['model']			= $_POST['model']; //모델명
        $value['opt_subject']	= isset($it_option_subject)?$it_option_subject:""; //상품 선택옵션
        $value['spl_subject']	= isset($it_supply_subject)?$it_supply_subject:""; //상품 추가옵션
        $value['stock_qty']		= isset($stock_qty)?conv_number($stock_qty):""; //재고수량
        $value['noti_qty']		= isset($noti_qty)?conv_number($noti_qty):"";; //재고 통보수량
        $value['brand_uid']		= $_POST['brand_uid']; //브랜드주키
        $value['brand_nm']		= $this->global_lib->get_brand($_POST['brand_uid']); //브랜드명
        $value['notax']			= $_POST['notax']; //과세구분
        $value['zone']			= $_POST['zone']; //판매가능지역
        $value['zone_msg']		= $_POST['zone_msg']; //판매가능지역 추가설명
        $value['sc_type']		= $_POST['sc_type']; //배송비 유형	0:공통설정, 1:무료, 2:조건부 무료, 3:유료
        $value['sc_method']		= $_POST['sc_method']; //배송비 결제	0:선불, 1:착불, 2:사용자선택
        $value['sc_amt']		= isset($sc_amt)?conv_number($sc_amt):""; //기본 배송비
        $value['sc_minimum']	= isset($sc_minimum)?conv_number($sc_minimum):"";	//조건 배송비
        $value['sc_each_use']	= isset($sc_each_use)?$sc_each_use:""; //묶음배송불가
        $value['info_gubun']	= $_POST['info_gubun']; //상품정보제공 구분
        $value['info_value']	= $it_info_value; //상품정보제공 값
        $value['price_msg']		= $_POST['price_msg']; //가격 대체문구
        $value['stock_mod']		= $_POST['stock_mod']; //수량형식
        $value['odr_min']		= conv_number($_POST['odr_min']); //최소 주문한도
        $value['odr_max']		= conv_number($_POST['odr_max']); //최대 주문한도
        $value['buy_level']		= $_POST['buy_level']; //구매가능 레벨
        $value['buy_only']		= isset($buy_only)?$buy_only:""; //가격공개 여부
        $value['simg_type']		= $_POST['simg_type']; //이미지 등록방식
        $value['sb_date']		= $_POST['sb_date']; //판매 시작일
        $value['eb_date']		= $_POST['eb_date']; //판매 종료일
        $value['ec_mall_pid']	= $_POST['ec_mall_pid']; //네이버쇼핑 상품ID
        $value['update_time']	= $OM_TIME_YMDHIS; //수정일시

        if($w == "") {
            $value['gcode'] = $_POST['gcode']; //상품코드
            $value['reg_time'] = $OM_TIME_YMDHIS; //등록일시
            $gs_id = $this->query_model->insertObj("shop_goods", $value);
        } else if($w == "u") {
            $this->query_model->updateObj("shop_goods", $value,"index_no = '$gs_id'");
        }

        // 선택옵션등록
        if($option_count) {
            $comma = '';
            $opt_id=$this->input->post('opt_id');
            $opt_price=$this->input->post('opt_price');
            $opt_stock_qty=$this->input->post('opt_stock_qty');
            $opt_noti_qty=$this->input->post('opt_noti_qty');
            $opt_use=$this->input->post('opt_use');
            $sql = " insert into shop_goods_option
                            ( `io_id`, `io_type`, `gs_id`, `io_price`, `io_stock_qty`, `io_noti_qty`, `io_use` )
                        VALUES ";
            for($i=0; $i<$option_count; $i++) {
                $sql .= $comma . " ( '{$opt_id[$i]}', '0', '$gs_id', '{$opt_price[$i]}', '{$opt_stock_qty[$i]}', '{$opt_noti_qty[$i]}', '{$opt_use[$i]}' )";
                $comma = ' , ';
            }
            $this->query_model->returnNull($sql);
        }

        // 추가옵션등록
        if($supply_count) {
            $comma = '';
            $spl_id=$this->input->post('spl_id');
            $spl_price=$this->input->post('spl_price');
            $spl_stock_qty=$this->input->post('spl_stock_qty');
            $spl_noti_qty=$this->input->post('spl_noti_qty');
            $spl_use=$this->input->post('spl_use');
            $sql = " insert into shop_goods_option
                            ( `io_id`, `io_type`, `gs_id`, `io_price`, `io_stock_qty`, `io_noti_qty`, `io_use` )
                        VALUES ";
            for($i=0; $i<$supply_count; $i++) {
                $sql .= $comma . " ( '{$spl_id[$i]}', '1', '$gs_id', '{$spl_price[$i]}', '{$spl_stock_qty[$i]}', '{$spl_noti_qty[$i]}', '{$spl_use[$i]}' )";
                $comma = ' , ';
            }
            $this->query_model->returnNull($sql);
        }

        if($w == "")
            goto_url(base_url()."mypage/page?code=partner_goods_form&w=u&gs_id=$gs_id");
        else if($w == "u")
            goto_url(base_url()."mypage/page?code=partner_goods_form&w=u&gs_id=$gs_id$q1&page=$page&bak=$bak");
    }
    public function partner_goods_list_update(){
        $pf_auth_good=$this->load->get_var('pf_auth_good');
        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');

        if(!$pf_auth_good)
            alert('개별 상품판매 권한이 있어야만 이용 가능합니다.');

        $count = count($_POST['chk']);
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }

        if($_POST['act_button'] == "선택삭제"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                // include 전에 $gs_id 값을 반드시 넘겨야 함
                $gs_id = trim($_POST['gs_id'][$k]);
                $gs = $this->global_lib->get_goods($gs_id);
                $dir_list = './data/goods/'.$gs['gcode'];
                for($g=1; $g<=6; $g++) {
                    if($gs['simg'.$g]) {
                        @unlink('./data/goods/'.$gs['simg'.$g]);
                        delete_item_thumbnail($dir_list, $gs['simg'.$g]);
                    }
                }

                // 에디터 이미지 삭제
                $this->funcomplex_lib->delete_editor_image($gs['memo']);

                // 삭제
                $sql1="delete from shop_goods where index_no=?";
                $sql2="delete from shop_goods_type where gs_id=?";
                $sql3="delete from shop_goods_review where gs_id=?";
                $sql4="delete from shop_goods_option where gs_id=?";
                $sql5="delete from shop_cart where gs_id=? and ct_select='0'";
                $sql6="delete from shop_wish where gs_id=?";
                $sql7="delete from shop_goods_qa where gs_id=?";
                $sql8="delete from shop_goods_relation where gs_id = ?";
                $sql9="delete from shop_goods_relation where gs_id2 = ?";
                $param=array($gs_id);
                $this->query_model->returnNull($sql1,$param); // 상품테이블
                $this->query_model->returnNull($sql2,$param); //진열관리
                $this->query_model->returnNull($sql3,$param); // 상품평
                $this->query_model->returnNull($sql4,$param); // 옵션
                $this->query_model->returnNull($sql5,$param); // 장바구니
                $this->query_model->returnNull($sql6,$param); // 찜목록
                $this->query_model->returnNull($sql7,$param); // 상품문의
                $this->query_model->returnNull($sql8,$param);// 관련상품
                $this->query_model->returnNull($sql9,$param);// 관련상품
            }
        }else if($_POST['act_button'] == "선택상품복사"){
        // _GOODS_COPY_ 상수를 선언해야 goods_copy.inc.php 가 정상 작동함

            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                // include 전에 $gs_id 값을 반드시 넘겨야 함
                $gs_id = trim($_POST['gs_id'][$k]);
                $sql = " select * from shop_goods where index_no = ? limit 1 ";
                $param=array($gs_id);
                $cp = $this->query_model->returnOneArr($sql,$param);

                // 상품테이블의 필드가 추가되어도 수정하지 않도록 필드명을 추출하여 insert 퀴리를 생성한다.
                $sql_common = "";
                $fields = $this->gvcomplex_lib->sql_field_names("shop_goods");
                foreach($fields as $fld) {
                    if(in_array($fld, array('index_no', 'gcode', 'readcount', 'rank', 'm_count', 'sum_qty', 'reg_time', 'update_time'))) continue;

                    $sql_common .= " , $fld = '".addslashes($cp[$fld])."' ";
                }

                $sql = " insert into shop_goods
                            set reg_time = '".$OM_TIME_YMDHIS."',
                                update_time = '".$OM_TIME_YMDHIS."'
                                $sql_common ";
                $insert_id = $this->query_model->returnInsertId($sql);

                $sql_img = "";
                for($g=1; $g<=6; $g++) {
                    if($cp['simg'.$g] && preg_match("/^(http[s]?:\/\/)/", $cp['simg'.$g]) == false) {
                        $file = './data/goods/'.$cp['simg'.$g];
                        $dstfile = "./data/goods/{$insert_id}_".$cp['simg'.$g];
                        $filename = basename($dstfile);

                        @copy($file, $dstfile);
                        @chmod($dstfile, 0644);
                        $sql_img .= " , simg{$g} = '$filename' ";
                    }
                }

                $new_code = time() + $insert_id;

                $sql = " update shop_goods
                            set gcode = ?
                                $sql_img
                        where index_no = ? ";
                $param=array($new_code,$insert_id);
                $this->query_model->returnNull($sql,$param);

                // 옵션 copy
                $opt_sql = " insert ignore into shop_goods_option
                                    ( io_id, io_type, gs_id, io_price, io_stock_qty, io_noti_qty, io_use )
                            select io_id, io_type, ?, io_price, io_stock_qty, io_noti_qty, io_use
                            from shop_goods_option
                            where gs_id = ?
                            order by io_no asc ";
                $param=array($insert_id,$gs_id);
                $this->query_model->returnNull($opt_sql,$param);
            }
        }else if($_POST['act_button'] == "선택재고수정") {
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $gs_id = trim($_POST['gs_id'][$k]);
                $stock_qty = conv_number($_POST['stock_qty']);

                $sql = " update shop_goods set stock_qty = ? where index_no = ? ";
                $param=array($stock_qty,$gs_id);
                $this->query_model->returnNull($sql,$param);
            }
        }else if($_POST['act_button'] == "선택옵션재고수정") {
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $io_no = trim($_POST['io_no'][$k]);
                $io_stock_qty = conv_number($_POST['stock_qty']);

                $sql = " update shop_goods_option set io_stock_qty = ? where io_no = ?";
                $param=array($io_stock_qty,$io_no);
                $this->query_model->returnNull($sql,$param);
            }
        }else {
            alert();
        }

        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function partner_goods_brand_form_update(){
        $pf_auth_good=$this->load->get_var('pf_auth_good');
        $member=$this->load->get_var('member');
        $qstr=$this->load->get_var('qstr');
        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        $w=$this->input->post('w');
        $br_id=$this->input->post('br_id');

        if(!$pf_auth_good)
            alert('개별 상품판매 권한이 있어야만 이용 가능합니다.');

        $uploadPath="./data/board";
        $configArr = array();
        $configArr['upload_path'] = $uploadPath;
        
        $configArr['allowed_types'] = $this->allowed_types;
        $configArr['max_size'] = $this->max_size;
        $configArr['max_width'] = $this->max_width;
        $configArr['max_height'] = $this->max_height;
        $configArr['remove_spaces'] = $this->remove_spaces;
        $configArr['encrypt_name'] = $this->encrypt_name;
        $this->load->library('upload', $configArr);
        $this->upload->initialize($configArr);

        unset($value);
        $br_logo_del=$this->input->post('br_logo_del');
        if($br_logo_del){
            unlink($uploadPath.'/'.$br_logo_del);
            $value['br_logo'] = '';
        }
        if(isset($_FILES['br_logo']['name'])&&$_FILES['br_logo']['name']!="") {
            if(!$this->upload->do_upload('br_logo')){
                $error = $this->upload->display_errors();
            }else{
                $uploadFile=$this->upload->data();
                $new_file=$uploadFile['file_name'];
                $value['br_logo'] = $new_file;
            }
        }

        $value['br_user_yes'] = 1;
        $value['br_name'] = $_POST['br_name'];
        $value['br_name_eng'] = $_POST['br_name_eng'];
        $value['br_updatetime'] = $OM_TIME_YMDHIS;

        if($w == '') {
            $value['mb_id'] = $member['id'];
            $value['br_time'] = $OM_TIME_YMDHIS;
            $this->query_model->insertObj("shop_brand", $value);

            goto_url(base_url()."mypage/page?$q1&page=$page");

        } else if($w == 'u') {	
            $this->query_model->updateObj("shop_brand", $value, "br_id='$br_id'");
            $br_name=$this->input->post('br_name');
            // 상품 정보도 동시에 수정
            $sql = "update shop_goods set brand_nm = ? where brand_uid = ?";
            $param=array($br_name,$br_id);
            $this->query_model->returnNull($sql,$param);

            goto_url(base_url()."mypage/page?code=partner_goods_brand_form&w=u&br_id=$br_id$qstr&page=$page");
        }
    }
    public function partner_goods_brand_list_update(){
        $pf_auth_good=$this->load->get_var('pf_auth_good');
        $br_id=$this->input->post('br_id');
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        if(!$pf_auth_good)
            alert('개별 상품판매 권한이 있어야만 이용 가능합니다.');

        $chk=$this->input->post('chk');
        $count = ($chk!="")?count($chk):0;
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }

        if($_POST['act_button'] == "선택수정"){
            $br_name=$this->input->post('br_name');
            $br_name_eng=$this->input->post('br_name_eng');
            $br_id=$this->input->post('br_id');
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $chk[$i];

                $sql = "update shop_brand set br_name = ?, br_name_eng = ? where br_id = ?";
                $param=array($br_name[$k],$br_name_eng[$k],$br_id[$k]);
                $this->query_model->returnNull($sql,$param);

                // 상품 정보도 동시에 수정
                $sql = "update shop_goods 
                        set brand_nm = ? 
                        where brand_uid = ?";
                $param=array($br_name[$k],$br_id[$k]);
                $this->query_model->returnNull($sql,$param);
            }
        }else if($_POST['act_button'] == "선택삭제") {
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $br_id = trim($_POST['br_id'][$k]);
                $sql="select br_logo from shop_brand where br_id = ?";
                $param=array($br_id);
                $row = $this->query_model->returnOneArr($sql,$param);
                @unlink("./data/brand/".$row['br_logo']);
                $sql2="delete from shop_brand where br_id=?";
                $this->query_model->returnNull($sql2,$param);
            }
        }

        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function partner_goods_qa_delete(){
        $q1=$this->input->post('q1');
        $q1=$this->input->post('page');
        $pf_auth_good=$this->load->get_var('pf_auth_good');
        
        if(!$pf_auth_good)
            alert('개별 상품판매 권한이 있어야만 이용 가능합니다.');

        $count = count($_POST['chk']);
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }
        $iq_id=$this->input->post('iq_id');
        for($i=0; $i<$count; $i++){
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $sql = " delete from shop_goods_qa where iq_id = ?";
            $param=array($iq_id[$k]);
            $this->query_model->returnNull($sql,$param);
        }

        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function partner_goods_review_delete(){
        $pf_auth_good=$this->load->get_var('pf_auth_good');
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');

        if(!$pf_auth_good)
            alert('개별 상품판매 권한이 있어야만 이용 가능합니다.');

        $count = count($_POST['chk']);
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }

        for($i=0; $i<$count; $i++){
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];

            $index_no = trim($_POST['index_no'][$k]);
            $sql="delete from shop_goods_review where index_no=?";
            $param=array($index_no);
            $this->query_model->returnNull($sql,$param);
        }

        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function partner_odr_excel2(){
        $pf_auth_good=$this->load->get_var('pf_auth_good');
        $member=$this->load->get_var('member');
        $od_id=$this->input->get('od_id');

        if(!$pf_auth_good)
            alert('개별 상품판매 권한이 있어야만 이용 가능합니다.');

        if(!$od_id)
            alert("주문번호가 넘어오지 않았습니다.");

        $sql = " select *
                from shop_order
                where od_id IN ({$od_id})
                    and seller_id = ?
                order by od_time desc, index_no asc ";
        $param=array($member['id']);
        $resultObj = $this->query_model->returnQueryObj($sql,$param);
        $cnt = $resultObj->num_rows();
        if(!$cnt){
            alert("출력할 자료가 없습니다.");
        }
        $this->partner_odr_excel_sub($result);
    }
    private function partner_odr_excel_sub($result){
        
        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++.'1', '판매자ID')
            ->setCellValue($char++.'1', '회원ID')
            ->setCellValue($char++.'1', '주문번호')
            ->setCellValue($char++.'1', '일련번호')
            ->setCellValue($char++.'1', '상품코드')
            ->setCellValue($char++.'1', '상품명')
            ->setCellValue($char++.'1', '옵션')
            ->setCellValue($char++.'1', '공급가')
            ->setCellValue($char++.'1', '판매가')
            ->setCellValue($char++.'1', '수량')
            ->setCellValue($char++.'1', '쿠폰할인')
            ->setCellValue($char++.'1', '포인트결제')
            ->setCellValue($char++.'1', '배송비')
            ->setCellValue($char++.'1', '실결제금액')
            ->setCellValue($char++.'1', '총주문금액')
            ->setCellValue($char++.'1', '결제방법')
            ->setCellValue($char++.'1', '주문상태')
            ->setCellValue($char++.'1', '주문채널')
            ->setCellValue($char++.'1', '주문자명')
            ->setCellValue($char++.'1', '수취인명')
            ->setCellValue($char++.'1', '수취인전화번호')
            ->setCellValue($char++.'1', '수취인핸드폰')
            ->setCellValue($char++.'1', '수취인우편번호')
            ->setCellValue($char++.'1', '수취인주소')
            ->setCellValue($char++.'1', '주문시요청사항')
            ->setCellValue($char++.'1', '배송회사')
            ->setCellValue($char++.'1', '운송장번호')
            ->setCellValue($char++.'1', '입금자명')
            ->setCellValue($char++.'1', '입금일시')
            ->setCellValue($char++.'1', '주문일시')
            ->setCellValue($char++.'1', '거래증빙')
            ->setCellValue($char++.'1', '세금계산서(상호명)')
            ->setCellValue($char++.'1', '세금계산서(대표자)')
            ->setCellValue($char++.'1', '세금계산서(사업자등록번호)')
            ->setCellValue($char++.'1', '세금계산서(사업장주소)')
            ->setCellValue($char++.'1', '세금계산서(업태)')
            ->setCellValue($char++.'1', '세금계산서(종목)')
            ->setCellValue($char++.'1', '현금영수증(사업자 지출증빙용)')
            ->setCellValue($char++.'1', '현금영수증(개인 소득공제용)')
            ->setCellValue($char++.'1', '관리자메모');

        $i=2;
        foreach($result as $key => $row){
            $gs = unserialize($row['od_goods']);
            $amount = $this->global_lib->get_order_spay($row['od_id'], " and seller_id = '{$member['id']}' ");
            $sodr = $this->funcomplex_lib->excel_order_list($row, $amount);

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++.$i, $sodr['od_seller_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['od_mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['od_id'].$sodr['od_test'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['od_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $gs['gcode'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $gs['gname'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['it_options'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['supply_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['goods_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['sum_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['coupon_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['use_point'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['baesong_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['use_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['goods_price']+$row['baesong_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $sodr['od_paytype'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $gw_status[$row['dan']], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['od_mobile'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['b_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['b_telephone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['b_cellphone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['b_zip'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $this->funcomplex_lib->print_address($row['b_addr1'], $row['b_addr2'], $row['b_addr3'], $row['b_addr_jibeon']), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['memo'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['od_delivery_company'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['delivery_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['deposit_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['od_receipt_time'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['od_time'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $sodr['od_taxbill'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['company_name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['company_owner'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['company_saupja_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['company_addr'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['company_item'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['company_service'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['tax_saupja_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['tax_hp'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['shop_memo'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('주문내역');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="주문내역-'.date("ymd", time()).'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function partner_odr_excel(){
        $pf_auth_good=$this->load->get_var('pf_auth_good');
        $member=$this->load->get_var('member');
        $code=$this->input->get('code');

        if(!$pf_auth_good)
            alert('개별 상품판매 권한이 있어야만 이용 가능합니다.');

        $fr_date=$this->input->get('fr_date');
        $to_date=$this->input->get('to_date');
        $sfl=$this->input->get('sfl');
        $stx=$this->input->get('stx');
        $od_settle_case=$this->input->get('od_settle_case');
        $od_status=$this->input->get('od_status');
        $od_final=$this->input->get('od_final');
        $od_taxbill=$this->input->get('od_taxbill');
        $od_taxsave=$this->input->get('od_taxsave');
        $od_memo=$this->input->get('od_memo');
        $od_shop_memo=$this->input->get('od_shop_memo');
        $od_receipt_point=$this->input->get('od_receipt_point');
        $od_coupon=$this->input->get('od_coupon');
        $od_escrow=$this->input->get('od_escrow');

        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $sql_common = " from shop_order ";

        $where = array();
        $where[] = " seller_id = '{$member['id']}' ";

        if($code == 'partner_odr_list') // 주문리스트(전체)
            $where[] = " dan != 0 ";
        else
            $where[] = " dan = '".conv_number($code)."' ";

        if($sfl && $stx)
            $where[] = " $sfl like '%$stx%' ";

        if($od_settle_case)
            $where[] = " paymethod = '$od_settle_case' ";

        if(is_numeric($od_status))
            $where[] = " dan = '$od_status' ";

        if(is_numeric($od_final))
            $where[] = " user_ok = '$od_final' ";

        if($od_taxbill)
            $where[] = " taxbill_yes = 'Y' ";

        if($od_taxsave)
            $where[] = " taxsave_yes IN ('Y','S') ";

        if($od_memo)
            $where[] = " memo <> '' ";

        if($od_shop_memo)
            $where[] = " shop_memo <> '' ";

        if($od_receipt_point)
            $where[] = " use_point != 0 ";

        if($od_coupon)
            $where[] = " coupon_price != 0 ";

        if($od_escrow)
            $where[] = " od_escrow = 1 ";

        if($fr_date && $to_date)
            $where[] = " left({$sel_field},10) between '$fr_date' and '$to_date' ";
        else if($fr_date && !$to_date)
            $where[] = " left({$sel_field},10) between '$fr_date' and '$fr_date' ";
        else if(!$fr_date && $to_date)
            $where[] = " left({$sel_field},10) between '$to_date' and '$to_date' ";

        if($where) {
            $sql_search = ' where '.implode(' and ', $where);
        }

        $sql_order = " order by od_time desc, index_no asc ";

        $sql = " select * {$sql_common} {$sql_search} {$sql_order} ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result=$resultObj->result_array();
        if(!$cnt)
            alert("출력할 자료가 없습니다.");
        $this->partner_odr_excel_sub($result);
    }
    public function partner_odr_update(){
        $pf_auth_good=$this->load->get_var('pf_auth_good');
        $q1=$this->input->post('q1');
        $page=$this->input->post('page');
        if(!$pf_auth_good)
            alert('개별 상품판매 권한이 있어야만 이용 가능합니다.');

        $count = count($_POST['chk']);
        if(!$count) {
            alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
        }

        if($_POST['act_button'] == "입금완료"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k     = $_POST['chk'][$i];
                $od_id = $_POST['od_id'][$k];

                $od = $this->global_lib->get_order($od_id);
                if($od['dan'] != 1) continue;
                if(!in_array($od['paymethod'], array('무통장','가상계좌'))) continue;

                $this->global_lib->change_order_status_ipgum($od_id);

                $this->funcomplex_lib->moonjaro_order_sms_send($od['pt_id'], $od['cellphone'], $od_id, 3);
            }
        }else if($_POST['act_button'] == "주문취소"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k     = $_POST['chk'][$i];
                $od_id = $_POST['od_id'][$k];

                $od = $this->global_lib->get_order($od_id);
                if($od['dan'] != 1) continue;
                if(!in_array($od['paymethod'], array('무통장','가상계좌'))) continue;

                $sql = " select od_no from shop_order where od_id = ? order by index_no ";
                $param=array($od_id);
                $res = $this->query_model->returnArr($sql,$param);
                foreach($res as $key => $row){
                    $this->funcomplex_lib->change_order_status_6($row['od_no']);
                }

                $this->funcomplex_lib->moonjaro_order_sms_send($od['pt_id'], $od['cellphone'], $od_id, 5);
            }
        }else if($_POST['act_button'] == "배송준비"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k     = $_POST['chk'][$i];
                $od_no = $_POST['od_no'][$k];

                $od = $this->global_lib->get_order($od_no);
                if($od['dan'] != 2) continue;

                $this->global_lib->change_order_status_3($od_no);
            }
        }else if($_POST['act_button'] == "배송중"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k			 = $_POST['chk'][$i];
                $od_no		 = $_POST['od_no'][$k];
                $delivery	 = $_POST['delivery'][$k];
                $delivery_no = $_POST['delivery_no'][$k];

                $od =$this->global_lib->get_order($od_no);
                if($od['dan'] != 3) continue;

                $this->global_lib->change_order_status_4($od_no, $delivery, $delivery_no);

                $od_sms_baesong[$od['od_id']] = $od['cellphone'];
            }

            foreach($od_sms_baesong as $key=>$recv) {
                $q = $this->global_lib->get_order($key, 'pt_id');
                $this->funcomplex_lib->moonjaro_order_sms_send($q['pt_id'], $recv, $key, 4);
            }
        }else if($_POST['act_button'] == "배송완료"){
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k			 = $_POST['chk'][$i];
                $od_no		 = $_POST['od_no'][$k];
                $delivery	 = $_POST['delivery'][$k];
                $delivery_no = $_POST['delivery_no'][$k];

                $od = $this->global_lib->get_order($od_no);
                if($od['dan'] != 4) continue;

                $this->funcomplex_lib->change_order_status_5($od_no, $delivery, $delivery_no);

                $od_sms_delivered[$od['od_id']] = $od['cellphone'];
            }

            foreach($od_sms_delivered as $key=>$recv) {
                $q = $this->global_lib->get_order($key, 'pt_id');
                $this->funcomplex_lib->moonjaro_order_sms_send($q['pt_id'], $recv, $key, 6);
            }
        }else if($_POST['act_button'] == "운송장번호수정"){
            $delivery=$this->input->post('delivery');
            $delivery_no=$this->input->post('delivery_no');
            $od_no=$this->input->post('od_no');
            for($i=0; $i<$count; $i++){
                // 실제 번호를 넘김
                $k = $_POST['chk'][$i];

                $sql = " update shop_order
                            set delivery	= ?
                            , delivery_no = ?
                        where od_no = ?";
                $param=array($delivery[$k],$delivery_no[$k],$od_no[$k]);
                $this->query_model->returnNull($sql,$param);
            }
        } else {
            alert();
        }

        goto_url(base_url()."mypage/page?$q1&page=$page");
    }
    public function partner_odr_statusupdate(){
        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $member=$this->load->get_var('member');
        $od_id=$this->input->post('od_id');

        $od_sms_ipgum_check		= 0;
        $od_sms_baesong_check	= 0;
        $od_sms_delivered_check = 0;
        $od_sms_cancel_check	= 0;
        $od_cancel_change		= 0;
        
        //==============================================================================
        // 주문상태변경
        //------------------------------------------------------------------------------
        if($_POST['act_button'] == '입금완료') {
            $od = $this->global_lib->get_order($od_id);
            if($od['dan'] != 1)
                alert('입금대기 상태에서만 입금완료로 변경 가능합니다.');
        
            if(!in_array($od['paymethod'], array('무통장','가상계좌')))
                alert('무통장, 가상계좌만 입금완료로 변경 가능합니다.');
        
            $this->global_lib->change_order_status_ipgum($od_id);
            $od_sms_ipgum_check++;
        }else if($_POST['act_button'] == "주문취소"){
            $od = $this->global_lib->get_order($od_id);
            if($od['dan'] != 1)
                alert('입금대기 상태에서만 주문취소 가능합니다.');
        
            if(!in_array($od['paymethod'], array('무통장','가상계좌')))
                alert('무통장, 가상계좌만 주문취소 가능합니다.');
        
            $sql = " select od_no from shop_order where od_id = ? order by index_no ";
            $param=array($od_id);
            $res = $this->query_model->returnArr($sql,$param);
            foreach($res as $key => $row){
                $this->funcomplex_lib->change_order_status_6($row['od_no']);
                $od_sms_cancel_check++;
                $od_cancel_change++;
            }
        }else if($_POST['act_button'] == "주문상태저장"){
            $chk_count = count($_POST['chk']);
            if(!$chk_count)
                alert('처리할 자료를 하나 이상 선택해 주십시오.');
        
            for($i=0; $i<$chk_count; $i++){
                // 실제 번호를 넘김
                $k				= $_POST['chk'][$i];
                $od_no			= $_POST['od_no'][$k];
                $change_status  = $_POST['change_status'][$k];
                $current_status = $_POST['current_status'][$k];
                $delivery		= trim($_POST['delivery'][$k]);
                $delivery_no	= trim($_POST['delivery_no'][$k]);
        
                switch($change_status) {
                    case '3': // 배송준비
                        if($current_status != 2) continue 2;
                        $this->global_lib->change_order_status_3($od_no, $delivery, $delivery_no);
                        break;
                    case '4': // 배송중
                        if($current_status != 3) continue 2;
                        $this->global_lib->change_order_status_4($od_no, $delivery, $delivery_no);
                        $od_sms_baesong_check++;
                        break;
                    case '5': // 배송완료
                        if(!in_array($current_status, array(3,4))) continue 2;
                        $this->funcomplex_lib->change_order_status_5($od_no);
                        $od_sms_delivered_check++;
                        break;
                    // case '6': // 취소
                        // if($current_status != 1) continue 2;
                        // change_order_status_6($od_no);
                        // $od_sms_cancel_check++;
                        // break;
                    case '7': // 반품
                        if($current_status != 5) continue 2;
                        $this->funcomplex_lib->change_order_status_7($od_no);
                        $od_cancel_change++;
                        break;
                    case '8': // 교환
                        if($current_status != 5) continue 2;
                        $this->global_lib->change_order_status_8($od_no);
                        break;
                    case '9': // 환불
                        if(!in_array($current_status, array(2,3))) continue 2;
                        $this->funcomplex_lib->change_order_status_9($od_no);
                        $od_sms_cancel_check++;
                        $od_cancel_change++;
                        break;
                }                
            }
        }
        //------------------------------------------------------------------------------
        
        //==============================================================================
        // 문자전송
        //------------------------------------------------------------------------------
        if($od_sms_ipgum_check)
            $this->funcomplex_lib->moonjaro_order_sms_send($pt_id, $od_hp, $od_id, 3); // 입금완료 문자
        
        if($od_sms_baesong_check)
            $this->funcomplex_lib->moonjaro_order_sms_send($pt_id, $od_hp, $od_id, 4); // 배송중 문자
        
        if($od_sms_delivered_check)
            $this->funcomplex_lib->moonjaro_order_sms_send($pt_id, $od_hp, $od_id, 6); // 배송완료 문자
        
        if($od_sms_cancel_check)
            $this->funcomplex_lib->moonjaro_order_sms_send($pt_id, $od_hp, $od_id, 5); // 주문취소 문자
        //------------------------------------------------------------------------------
        
        // 상품 모두 취소일 경우 주문상태 변경
        $mod_history = '';
        $pg_res_cd = '';
        $pg_res_msg = '';
        $pg_cancel_log = '';
        
        if($od_cancel_change) {
            $sql = " select COUNT(*) as od_count1,
                            SUM(IF(dan = '6' OR dan = '7' OR dan = '9', 1, 0)) as od_count2,
                            SUM(refund_price) as od_refund_price,
                            SUM(use_price) as od_receipt_price
                       from shop_order
                      where od_id = ?";
            $param=array($od_id);
            $row = $this->query_model->returnOneArr($sql,$param);
        
            if($row['od_count1'] == $row['od_count2']) {
                // PG 신용카드 결제 취소일 때
                $od_receipt_price = $row['od_receipt_price'];
                if($od_receipt_price > 0 && $row['od_refund_price'] == 0) {
                    $sql = " select * from shop_order where od_id = ?";
                    $param=array($od_id);
                    $od = $this->query_model->returnOneArr($sql,$param);
        
                    if($od['od_tno'] && ($od['paymethod'] == '신용카드' || $od['paymethod'] == '간편결제' || $od['paymethod'] == 'KAKAOPAY') || ($od['od_pg'] == 'inicis' && $od['paymethod'] == '삼성페이')) {
                        // 가맹점 PG결제 정보
                        $default = $this->funcomplex_lib->set_partner_value($od['od_settle_pid']);
        
                        switch($od['od_pg']) {
                            case 'lg':
                                include_once('./Bill/settle_lg.inc.php');
        
                                $LGD_TID = $od['od_tno'];
        
                                $xpay = new XPay($configPath, $CST_PLATFORM);
        
                                // Mert Key 설정
                                $xpay->set_config_value('t'.$LGD_MID, $default['de_lg_mert_key']);
                                $xpay->set_config_value($LGD_MID, $default['de_lg_mert_key']);
        
                                $xpay->Init_TX($LGD_MID);
        
                                $xpay->Set('LGD_TXNAME', 'Cancel');
                                $xpay->Set('LGD_TID', $LGD_TID);
        
                                if($xpay->TX()) {
                                    $res_cd = $xpay->Response_Code();
                                    if($res_cd != '0000' && $res_cd != 'AV11') {
                                        $pg_res_cd = $res_cd;
                                        $pg_res_msg = $xpay->Response_Msg();
                                    }
                                } else {
                                    $pg_res_cd = $xpay->Response_Code();
                                    $pg_res_msg = $xpay->Response_Msg();
                                }
                                break;
                            case 'inicis':
                                include('./Bill/settle_inicis.inc.php');
                                $cancel_msg = iconv_euckr('쇼핑몰 운영자 승인 취소');
        
                                /*********************
                                 * 3. 취소 정보 설정 *
                                 *********************/
                                $inipay->SetField("type",      "cancel");                        // 고정 (절대 수정 불가)
                                $inipay->SetField("mid",       $default['de_inicis_mid']);       // 상점아이디
                                /**************************************************************************************************
                                 * admin 은 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
                                 * 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
                                 * 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
                                 * 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
                                 **************************************************************************************************/
                                $inipay->SetField("admin",     $default['de_inicis_admin_key']); // 비대칭 사용키 키패스워드
                                $inipay->SetField("tid",       $od['od_tno']);                   // 취소할 거래의 거래아이디
                                $inipay->SetField("cancelmsg", $cancel_msg);                     // 취소사유
        
                                /****************
                                 * 4. 취소 요청 *
                                 ****************/
                                $inipay->startAction();
        
                                /****************************************************************
                                 * 5. 취소 결과                                           	*
                                 *                                                        	*
                                 * 결과코드 : $inipay->getResult('ResultCode') ("00"이면 취소 성공)  	*
                                 * 결과내용 : $inipay->getResult('ResultMsg') (취소결과에 대한 설명) 	*
                                 * 취소날짜 : $inipay->getResult('CancelDate') (YYYYMMDD)          	*
                                 * 취소시각 : $inipay->getResult('CancelTime') (HHMMSS)            	*
                                 * 현금영수증 취소 승인번호 : $inipay->getResult('CSHR_CancelNum')    *
                                 * (현금영수증 발급 취소시에만 리턴됨)                          *
                                 ****************************************************************/
        
                                $res_cd  = $inipay->getResult('ResultCode');
                                $res_msg = $inipay->getResult('ResultMsg');
        
                                if($res_cd != '00') {
                                    $pg_res_cd = $res_cd;
                                    $pg_res_msg = iconv_utf8($res_msg);
                                }
                                break;
                            case 'KAKAOPAY':
                                #include_once(OM_SHOP_PATH.'/settle_kakaopay.inc.php');
                                $_REQUEST['TID']               = $od['od_tno'];
                                $_REQUEST['Amt']               = $od_receipt_price;
                                $_REQUEST['CancelMsg']         = '쇼핑몰 운영자 승인 취소';
                                $_REQUEST['PartialCancelCode'] = 0;
                                #include OM_SHOP_PATH.'/kakaopay/kakaopay_cancel.php';
                                break;
                            case 'kcp':
                                include_once('./Bill/settle_kcp.inc.php');
                                include_once('./Bill/kcp/pp_ax_hub_lib.php');
        
                                // locale ko_KR.euc-kr 로 설정
                                setlocale(LC_CTYPE, 'ko_KR.euc-kr');
        
                                $c_PayPlus = new C_PP_CLI_T;
        
                                $c_PayPlus->mf_clear();
        
                                $tno = $od['od_tno'];
                                $tran_cd = '00200000';
                                $cancel_msg = iconv_euckr('쇼핑몰 운영자 승인 취소');
                                $cust_ip = $_SERVER['REMOTE_ADDR'];
                                $bSucc_mod_type = "STSC";
        
                                $c_PayPlus->mf_set_modx_data( "tno",      $tno );  // KCP 원거래 거래번호
                                $c_PayPlus->mf_set_modx_data( "mod_type", $bSucc_mod_type );  // 원거래 변경 요청 종류
                                $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip );  // 변경 요청자 IP
                                $c_PayPlus->mf_set_modx_data( "mod_desc", $cancel_msg );  // 변경 사유
        
                                $c_PayPlus->mf_do_tx( $tno,  $g_conf_home_dir, $g_conf_site_cd,
                                                      $g_conf_site_key,  $tran_cd,    "",
                                                      $g_conf_gw_url,  $g_conf_gw_port,  "payplus_cli_slib",
                                                      $ordr_idxx, $cust_ip, "3" ,
                                                      0, 0, $g_conf_key_dir, $g_conf_log_dir);
        
                                $res_cd  = $c_PayPlus->m_res_cd;
                                $res_msg = $c_PayPlus->m_res_msg;
        
                                if($res_cd != '0000') {
                                    $pg_res_cd = $res_cd;
                                    $pg_res_msg = iconv_utf8($res_msg);
                                }
        
                                // locale 설정 초기화
                                setlocale(LC_CTYPE, '');
                                break;
                        }
        
                        // PG 취소요청 성공했으면
                        if($pg_res_cd == '') {
                            $pg_cancel_log = ' PG 신용카드 승인취소 처리';
        
                            // 전체취소
                            $sql = " select index_no from shop_order where od_id = ? order by index_no asc ";
                            $param=array($od_id);
                            $res = $this->query_model->returnArr($sql,$param);
                            foreach($res as $key => $row){
                                $sql = " update shop_order set refund_price = use_price where index_no = ?";
                                $param=array($row['index_no']);
                                $this->query_model->returnNull($sql,$param);
                            }
                        }
                    }
                }
        
                // 관리자 주문취소 로그
                $mod_history = $OM_TIME_YMDHIS.' '.$member['id'].' 주문취소 처리'.$pg_cancel_log."\n";
            }
        }
        
        if($mod_history) { // 주문변경 히스토리 기록
            $sql = " update shop_order
                        set od_mod_history = CONCAT(od_mod_history,?)
                      where od_id = ?";
            $param=array($mod_history,$od_id);
            $this->query_model->returnNull($sql,$param);
        }
        
        $url = base_url()."mypage/partner_odr_form?od_id=$od_id";
        
        // 신용카드 취소 때 오류가 있으면 알림
        if($pg_res_cd && $pg_res_msg) {
            alert('오류코드 : '.$pg_res_cd.' 오류내용 : '.$pg_res_msg, $url);
        }
        
        goto_url($url);
    }
    public function partner_odr_formupdate(){
        $od_id=$this->input->post('od_id');

        // 입금계좌
        if($_POST['mod_type'] == 'receipt') {
            unset($value);
            $value['bank']			= $_POST['bank'];
            $value['deposit_name']	= $_POST['deposit_name'];
            $this->query_model->updateObj("shop_order", $value, "od_id='$od_id'");
        }

        // 관리자메모
        if($_POST['mod_type'] == 'memo') {
            unset($value);
            $value['shop_memo']		= $_POST['shop_memo'];
            $this->query_model->updateObj("shop_order", $value, "od_id='$od_id'");
        }

        // 주문자/배송지 정보
        if($_POST['mod_type'] == 'info') {
            unset($value);
            $value['name']			= $this->input->post('name');
            $value['telephone']		= $this->input->post('telephone');
            $value['cellphone']		= $this->input->post('cellphone');
            $value['zip']			= $this->input->post('zip');
            $value['addr1']			= $this->input->post('addr1');
            $value['addr2']			= $this->input->post('addr2');
            $value['addr3']			= $this->input->post('addr3');
            $value['addr_jibeon']	= $this->input->post('addr_jibeon');
            $value['email']			= $this->input->post('email');
            $value['b_name']		= $this->input->post('b_name');
            $value['b_telephone']	= $this->input->post('b_telephone');
            $value['b_cellphone']	= $this->input->post('b_cellphone');
            $value['b_zip']			= $this->input->post('b_zip');
            $value['b_addr1']		= $this->input->post('b_addr1');
            $value['b_addr2']		= $this->input->post('b_addr2');
            $value['b_addr3']		= $this->input->post('b_addr3');
            $value['b_addr_jibeon']	= $this->input->post('b_addr_jibeon');
            $this->query_model->updateObj("shop_order", $value, "od_id='$od_id'");
        }

        goto_url(base_url()."mypage/partner_odr_form?od_id=$od_id");
    }
    public function partner_odr_baesongupdate(){
        $od_id=$this->input->post('od_id');

        $count = count($_POST['chk']);
        if(!$count) {
            alert('처리할 자료를 하나 이상 선택해 주십시오.');
        }
        $delivery=$this->input->post('delivery');
        $delivery_no=$this->input->post('delivery_no');
        $od_no=$this->input->post('od_no');
        for($i=0; $i<$count; $i++){
            // 실제 번호를 넘김
            $k = $_POST['chk'][$i];
            if(!isset($_POST['delivery'][$k]))
                continue;
            $sql = " update shop_order set delivery	= ? , delivery_no = ? where od_no = ?";
            $param=array($delivery[$k],$delivery_no[$k],$od_no[$k]);
            $this->query_model->returnNull($sql,$param);
        }

        goto_url(base_url()."mypage/partner_odr_form?od_id=$od_id");
    }
    public function partner_paylist_update(){
        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
        $OM_TIME_YMD=substr($OM_TIME_YMDHIS, 0, 10);
        $config=$this->load->get_var('config');
        $member=$this->load->get_var('member');
        // 관리비를 사용중일때 기간이 만료되었다면 출금신청 차단
        if($config['pf_expire_use'] && $config['pf_account_no']) {
            if(!is_null_time($member['term_date'])) {
                if($member['term_date'] < $OM_TIME_YMD) {
                    alert("회원님께서는 관리비 미납으로 출금신청을 하실 수 없습니다.");
                }
            }
        }

        $reg_price = conv_number($_POST['reg_price']);

        $paytax = 0;
        if($config['pf_payment_tax']) { // 세액공제
            $paytax = floor(($reg_price * $config['pf_payment_tax']) / 100);
        }

        $paynet = $reg_price - $paytax;	

        unset($value);
        $value['mb_id']			= $member['id'];
        $value['balance']		= $reg_price;
        $value['paytax']		= $paytax;
        $value['paynet']		= $paynet;
        $value['bank_name']		= $bank_name;
        $value['bank_account']	= $bank_account;
        $value['bank_holder']	= $bank_holder;
        $value['reg_time']		= $OM_TIME_YMDHIS;
        $this->query_model->insertObj("shop_partner_payrun", $value);

        goto_url(base_url().'mypage/page?code=partner_paylist');
    }
    public function partner_info_update(){
        $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());

        $member=$this->load->get_var('member');
        unset($value);
        $value['theme']			= $this->input->post('theme'); //테마스킨
        $value['mobile_theme']	= $this->input->post('mobile_theme'); //모바일테마스킨
        $this->query_model->updateObj("shop_member",$value,"id='$member[id]'");

        unset($value);
        $value['bank_name']			= $this->input->post('bank_name'); //은행명
        $value['bank_account']		= $this->input->post('bank_account'); //계좌번호
        $value['bank_holder']		= $this->input->post('bank_holder'); //예금주명
        $value['shop_name']			= $this->input->post('shop_name'); //쇼핑몰명
        $value['shop_name_us']		= $this->input->post('shop_name_us'); //쇼핑몰 영문명
        $value['saupja_yes']		= $this->input->post('saupja_yes'); //쇼핑몰 사업자노출 여부
        $value['company_type']		= $this->input->post('company_type'); //사업자유형
        $value['company_name']		= $this->input->post('company_name'); //회사명
        $value['company_saupja_no'] = $this->input->post('company_saupja_no'); //사업자등록번호
        $value['tongsin_no']		= $this->input->post('tongsin_no'); //통신판매신고번호
        $value['company_tel']		= $this->input->post('company_tel'); //대표전화
        $value['company_fax']		= $this->input->post('company_fax'); //대표팩스
        $value['company_item']		= $this->input->post('company_item'); //업태
        $value['company_service']	= $this->input->post('company_service'); //종목
        $value['company_owner']		= $this->input->post('company_owner'); //대표자명
        $value['company_zip']		= $this->input->post('company_zip'); //사업장우편번호
        $value['company_addr']		= $this->input->post('company_addr'); //사업장주소
        $value['company_hours']		= $this->input->post('company_hours'); //CS 상담가능시간
        $value['company_lunch']		= $this->input->post('company_lunch'); //CS 점심시간
        $value['company_close']		= $this->input->post('company_close'); //CS 휴무일
        $value['info_name']			= $this->input->post('info_name'); //정보책임자 이름
        $value['info_email']		= $this->input->post('info_email'); //정보책임자 e-mail
        $value['update_time']		= $OM_TIME_YMDHIS;
        $this->query_model->updateObj("shop_partner",$value,"mb_id='$member[id]'");

        goto_url(base_url().'mypage/page?code=partner_info');
    }
    public function partner_member_list_excel(){

        $member=$this->load->get_var('member');

        $fr_date=$this->input->post('fr_date');
        $to_date=$this->input->post('to_date');
        $sfl=$this->input->post('sfl');
        $stx=$this->input->post('stx');
        $sst=$this->input->post('sst');
        $orderby=$this->input->post('orderby');

        $filed=$this->input->get('filed');
        $filed=$this->db->escape_str($filed);
        
        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        $sql_common = " from shop_member ";
        $sql_search = " where pt_id = '{$member['id']}' ";

        if($sfl && $stx) {
            $sql_search .= " and $sfl like '%$stx%' ";
        }

        if($sst) {
            $sql_search .= " and grade = '$sst' ";
        }

        // 기간검색
        if($fr_date && $to_date)
            $sql_search .= " and {$spt} between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if($fr_date && !$to_date)
            $sql_search .= " and {$spt} between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if(!$fr_date && $to_date)
            $sql_search .= " and {$spt} between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        if(!$orderby) {
            $filed = "index_no";
            $sod = "desc";
        } else {
            $sod = $orderby;
        }

        $sql_order = " order by $filed $sod ";

        $sql = " select * $sql_common $sql_search $sql_order  ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $cnt = $resultObj->num_rows();
        $result=$resultObj->result_array();
        if(!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++.'1', '회원명')
            ->setCellValue($char++.'1', '아이디')
            ->setCellValue($char++.'1', '성별')
            ->setCellValue($char++.'1', '레벨')
            ->setCellValue($char++.'1', '추천인')
            ->setCellValue($char++.'1', '전화번호')
            ->setCellValue($char++.'1', '핸드폰')
            ->setCellValue($char++.'1', '우편번호')
            ->setCellValue($char++.'1', '주소')
            ->setCellValue($char++.'1', '이메일')
            ->setCellValue($char++.'1', '가입일')
            ->setCellValue($char++.'1', '로그인')
            ->setCellValue($char++.'1', '메일수신')
            ->setCellValue($char++.'1', 'SMS수신')
            ->setCellValue($char++.'1', '최후아이피')
            ->setCellValue($char++.'1', '포인트');
        $i=2;
        foreach($result as $key => $row){
            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++.$i, $row['name'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['gender'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['grade'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['pt_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['telephone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['cellphone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['zip'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $this->funcomplex_lib->print_address($row['addr1'], $row['addr2'], $row['addr3'], $row['addr_jibeon']), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['email'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['reg_time'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['login_sum'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['mailser'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['smsser'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['login_ip'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['point'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('회원목록');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="회원목록-'.date("ymd", time()).'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function partner_goods_list_excel(){
        $pf_auth_good=$this->load->get_var('pf_auth_good');
        $member=$this->load->get_var('member');

        $fr_date=$this->input->get('fr_date');
        $to_date=$this->input->get('to_date');
        $sel_ca1=$this->input->get('sel_ca1');
        $sel_ca2=$this->input->get('sel_ca2');
        $sel_ca3=$this->input->get('sel_ca3');
        $sel_ca4=$this->input->get('sel_ca4');
        $sel_ca5=$this->input->get('sel_ca5');
        $code=$this->input->get('code');
        $code=$this->input->get('code');
        $stx=$this->input->get('stx');
        $sfl=$this->input->get('sfl');
        $fr_date=$this->input->get('fr_date');
        $to_date=$this->input->get('to_date');

        $q_brand=$this->input->get('q_brand');
        $q_zone=$this->input->get('q_zone');
        $fr_stock=$this->input->get('fr_stock');
        $fr_price=$this->input->get('fr_price');
        $q_isopen=$this->input->get('q_isopen');
        $q_notax=$this->input->get('q_notax');
        $q_option=$this->input->get('q_option');
        $q_supply=$this->input->get('q_supply');
        $orderby=$this->input->get('orderby');

        $filed=$this->input->get('filed');
        $filed=$this->db->escape_str($filed);

        if(!$pf_auth_good)
            alert('개별 상품판매 권한이 있어야만 이용 가능합니다.');

        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date)) $fr_date = '';
        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date)) $to_date = '';

        if($sel_ca1) $sca = $sel_ca1;
        if($sel_ca2) $sca = $sel_ca2;
        if($sel_ca3) $sca = $sel_ca3;
        if($sel_ca4) $sca = $sel_ca4;
        if($sel_ca5) $sca = $sel_ca5;

        $sql_common = " from shop_goods ";
        $sql_search = " where use_aff = 1 and mb_id = '{$member['id']}' ";

        if($code == 'partner_goods_stock') {
            $sql_search .= " and (stock_qty <= noti_qty and stock_mod = 1 and opt_subject = '') ";
        }

        if($sca) {
            $sql_search .= " and (ca_id like '$sca%' or ca_id2 like '$sca%' or ca_id3 like '$sca%') ";
        }

        // 검색어
        if($stx) {
            switch($sfl) {
                case "gname" :
                case "explan" :
                case "maker" :
                case "origin" :
                case "model" :
                    $sql_search .= " and $sfl like '%$stx%' ";
                    break;
                default :
                    $sql_search .= " and $sfl like '$stx%' ";
                    break;
            }
        }

        // 기간검색
        if($fr_date && $to_date)
            $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
        else if($fr_date && !$to_date)
            $sql_search .= " and $q_date_field between '$fr_date 00:00:00' and '$fr_date 23:59:59' ";
        else if(!$fr_date && $to_date)
            $sql_search .= " and $q_date_field between '$to_date 00:00:00' and '$to_date 23:59:59' ";

        // 브랜드
        if(isset($q_brand) && $q_brand)
            $sql_search .= " and brand_uid = '$q_brand' ";

        // 배송가능 지역
        if(isset($q_zone) && $q_zone)
            $sql_search .= " and zone = '$q_zone' ";

        // 상품재고
        if($fr_stock && $to_stock)
            $sql_search .= " and $q_stock_field between '$fr_stock' and '$to_stock' ";

        // 상품가격
        if($fr_price && $to_price)
            $sql_search .= " and $q_price_field between '$fr_price' and '$to_price' ";

        // 판매여부
        if(isset($q_isopen) && is_numeric($q_isopen))
            $sql_search .= " and isopen='$q_isopen' ";

        // 과세유형
        if(isset($q_notax) && is_numeric($q_notax))
            $sql_search .= " and notax = '$q_notax' ";

        // 상품 필수옵션
        if(isset($q_option) && is_numeric($q_option)) {
        if($q_option)
            $sql_search .= " and opt_subject <> '' ";
        else
            $sql_search .= " and opt_subject = '' ";
        }

        // 상품 추가옵션
        if(isset($q_supply) && is_numeric($q_supply)) {
        if($q_supply)
            $sql_search .= " and spl_subject <> '' ";
        else
            $sql_search .= " and spl_subject = '' ";
        }

        if(!$orderby) {
            $filed = "index_no";
            $sod = "desc";
        } else {
            $sod = $orderby;
        }

        $sql_order = " order by $filed $sod ";

        $sql = " select * $sql_common $sql_search $sql_order ";
        $resultObj = $this->query_model->returnQueryObj($sql);
        $result=$resultObj->result_array();
        $cnt = $resultObj->num_rows();
        if(!$cnt)
            alert("출력할 자료가 없습니다.");

        /** Include PHPExcel */
        include_once('./lib/PHPExcel.php');

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Add some data
        $char = 'A';
        $excel->setActiveSheetIndex(0)
            ->setCellValue($char++.'1', '상품코드')
            ->setCellValue($char++.'1', '대표분류')
            ->setCellValue($char++.'1', '추가분류2')
            ->setCellValue($char++.'1', '추가분류3')
            ->setCellValue($char++.'1', '상품명')
            ->setCellValue($char++.'1', '짧은설명')
            ->setCellValue($char++.'1', '검색키워드')
            ->setCellValue($char++.'1', '모델명')
            ->setCellValue($char++.'1', '브랜드')
            ->setCellValue($char++.'1', '과세설정')
            ->setCellValue($char++.'1', '판매가능지역')
            ->setCellValue($char++.'1', '판매가능지역 추가설명')
            ->setCellValue($char++.'1', '원산지')
            ->setCellValue($char++.'1', '제조사')
            ->setCellValue($char++.'1', '판매여부')
            ->setCellValue($char++.'1', '공급가격')
            ->setCellValue($char++.'1', '시중가격')
            ->setCellValue($char++.'1', '판매가격')
            ->setCellValue($char++.'1', '가격대체문구')
            ->setCellValue($char++.'1', '재고적용타입')
            ->setCellValue($char++.'1', '재고수량')
            ->setCellValue($char++.'1', '재고통보수량')
            ->setCellValue($char++.'1', '최소주문한도')
            ->setCellValue($char++.'1', '최대주문한도')
            ->setCellValue($char++.'1', '포인트')
            ->setCellValue($char++.'1', '판매기간 시작일')
            ->setCellValue($char++.'1', '판매기간 종료일')
            ->setCellValue($char++.'1', '구매가능레벨')
            ->setCellValue($char++.'1', '가격공개')
            ->setCellValue($char++.'1', '배송비유형')
            ->setCellValue($char++.'1', '배송비결제')
            ->setCellValue($char++.'1', '기본배송비')
            ->setCellValue($char++.'1', '조건배송비')
            ->setCellValue($char++.'1', '이미지등록방식')
            ->setCellValue($char++.'1', '소이미지')
            ->setCellValue($char++.'1', '중이미지1')
            ->setCellValue($char++.'1', '중이미지2')
            ->setCellValue($char++.'1', '중이미지3')
            ->setCellValue($char++.'1', '중이미지4')
            ->setCellValue($char++.'1', '중이미지5')
            ->setCellValue($char++.'1', '상세설명')
            ->setCellValue($char++.'1', '관리자메모');

        $i=2;
        foreach($result as $key => $row){
            if(is_null_time($row['sb_date'])) $row['sb_date'] = '';
            if(is_null_time($row['eb_date'])) $row['eb_date'] = '';

            $char = 'A';
            $excel->setActiveSheetIndex(0)
                ->setCellValueExplicit($char++.$i, $row['gcode'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['ca_id'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['ca_id2'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['ca_id3'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['gname'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['explan'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['keywords'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['model'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['brand_nm'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['notax'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['zone'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['zone_msg'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['origin'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['maker'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['isopen'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['supply_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['normal_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['goods_price'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['price_msg'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['stock_mod'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['stock_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['noti_qty'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['odr_min'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['odr_max'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['gpoint'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['sb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['eb_date'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['buy_level'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['buy_only'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['sc_type'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['sc_method'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['sc_amt'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['sc_minimum'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['simg_type'], PHPExcel_Cell_DataType::TYPE_NUMERIC)
                ->setCellValueExplicit($char++.$i, $row['simg1'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['simg2'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['simg3'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['simg4'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['simg5'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['simg6'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['memo'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit($char++.$i, $row['admin_memo'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }

        // Rename worksheet
        $excel->getActiveSheet()->setTitle('상품');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="상품-'.date("ymd", time()).'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
}