<?php
defined('BASEPATH') or exit('No direct script access allowed');

define('DATE_TIME', date("Y-m-d H:i:s", time()));

class Api extends CI_Controller {   
    public function __construct(){
        parent::__construct();
    
        $this->load->model('query_model');
        $this->load->model('api_model');

        $this->load->helper('register_helper');
        $this->load->helper('global_helper');
        $this->load->library('common_lib');
        $this->load->library('register_lib');
        $this->load->library('gvcomplex_lib');
        $this->load->library('funcomplex_lib');
        $this->load->library('global_lib');
    }

// -------------------- 회원관련 시작
    // 로그인
    public function login() { 
        # 로그인
        $result = new stdClass();

        try {
			$token = $this->input->post('token'); // 토큰
            if(!$token){
				$token = '';
			}

            $mb_id = $this->input->post('mb_id'); // 아이디
            if(!$mb_id) {
                throw new Exception('아이디를 입력해주세요.');
            }

            $password = $this->input->post('password');
            if(!$mb_id) {
                throw new Exception('비밀번호를 입력해주세요.');
            }

            $auto_login = $this->input->post('auto_login') ?? "n"; // 자동로그인 여부

            $member_data = $this->global_lib->get_member($mb_id);
            if(!isset($member_data)|| !$this->funcomplex_lib->check_password($password, $member_data['passwd'], $mb_id)){
				throw new Exception('아이디 또는 비밀번호가 일치하지 않습니다.');
			}

            if($member_data['intercept_date'] && $member_data['intercept_date'] <= date("Ymd", time())) {
                $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1년 \\2월 \\3일", $member_data['intercept_date']);
                throw new Exception('접근이 금지된 아이디입니다. \\n 처리일 : '.$date);
            }

            $table = 'shop_member';
            $today_login = DATE_TIME;
            $data = array(
                'today_login' => $today_login,
                'token' => $token,
                'auto_login' => $auto_login
            );

            $this->query_model->updateObj($table, $data, array('id' => $member_data['id']));

            $result->status = 200;
            $result->message = "success";
            $result->mb_id = $mb_id;

		} catch (Exception $e){
			$result->status  = 400;
            $result->message = $e->getMessage();
		}

		echo json_encode($result);
    }

    // 아이디 찾기
    public function findId() {
        # 아이디 찾기
        $result = new StdClass();

        try {
            $name = $this->input->post('name');
            if(!$name) {
                throw new Exception('이름을 입력해주세요.');
            }

            $phone = $this->input->post('phone');
            if(!$phone) {
                throw new Exception('휴대폰 번호를 입력해주세요.');
            }

            if($msg = valid_mb_hp($phone)) {
                throw new Exception($msg);
            }

            $member_data = $this->api_model->findId($name,$phone);

            if(empty($member_data)) {
                throw new Exception("일치하는 회원 정보가 없습니다."); 
            }

            $result->status = 200;
            $result->message = "success";
            $result->member_data = $member_data;

		} catch (Exception $e){
			$result->status  = 400;
            $result->message = $e->getMessage();
		}

		echo json_encode($result);
    }

    // 비밀번호 찾기
    public function findPwd() {
        # 비밀번호 찾기
        $result = new StdClass();

        try {
            $mb_id = $this->input->post('mb_id');
            if(!$mb_id) {
                throw new Exception('아이디를 입력해주세요.');
            }

            $name = $this->input->post('name');
            if(!$name) {
                throw new Exception('이름을 입력해주세요.');
            }

            $phone = $this->input->post('phone');
            if(!$phone) {
                throw new Exception('휴대폰 번호를 입력해주세요.');
            }

            if($msg = valid_mb_hp($phone)) {
                throw new Exception($msg);
            }

            $member_data = $this->api_model->findPwd($mb_id,$name,$phone);
            if(empty($member_data)) {
                throw new Exception("일치하는 회원 정보가 없습니다."); 
            }

            $result->status = 200;
            $result->message = "success";
            $result->member_data = $member_data;

        } catch (Exception $e) {
			$result->status  = 400;
            $result->message = $e->getMessage();
		}

		echo json_encode($result);
    }

    // 비밀번호 재설정
    public function resetPwd() {
        # 비밀번호 재설정
        $result = new StdClass();

        try {
            $mb_id = $this->input->post('mb_id');
            if(!$mb_id) {
                throw new Exception('아이디를 입력해주세요.');
            }

            $password = $this->input->post('password');
            if(!$password) {
                throw new Exception('비밀번호를 입력해주세요.');
            }

            if ($msg = valid_mb_password($password)) {
                throw new Exception($msg);
            }

            $password_re = $this->input->post('password_re');
            if(!$password_re) {
                throw new Exception('비밀번호를 다시 한 번 입력해주세요.');
            }

            if ($password != $password_re) {
                throw new Exception("비밀번호가 일치하지 않습니다.");
            }

            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception("일치하는 회원 정보가 없습니다.");
            }
            $password = $this->global_lib->reg_sql_password($password,$member_data['reg_time']);
            
            $table = "shop_member";
            $where = "id='{$mb_id}'";
            if(!$this->query_model->updateObj($table,array('passwd'=>$password),$where)) {
                throw new Exception("비밀번호 변경 중 문제가 발생하였습니다.");
            }

            $result->status = 200;
            $result->message = "success";

        } catch (Exception $e) {
			$result->status  = 400;
            $result->message = $e->getMessage();
		}

		echo json_encode($result);
    }

    // 회원가입
    public function register() {
        # 회원가입
        $result = new StdClass();

        try {
            $mb_id = $this->input->post('mb_id');
            if(!$mb_id) {
                throw new Exception('아이디를 입력해주세요.');
            }

            if($msg = $this->register_lib->exist_mb_id($mb_id)) {
                throw new Exception($msg);
            }

            $id_check = $this->input->post('id_check');
            if(!$id_check && $id_check != "y") {
                throw new Exception('아이디 중복확인을 진행해주세요.');
            }

            $password = $this->input->post('password');
            if(!$password) {
                throw new Exception('비밀번호를 입력해주세요.');
            }

            if ($msg = valid_mb_password($password)) {
                throw new Exception($msg);
            }
            
            $password_re = $this->input->post('password_re');
            if(!$password_re) {
                throw new Exception('비밀번호를 다시 한 번 입력해주세요.');
            }

            if ($password != $password_re) {
                throw new Exception("비밀번호가 일치하지 않습니다.");
            }

            $password = $this->global_lib->reg_sql_password($password, DATE_TIME);

            $name = $this->input->post('name');
            if(!$name) {
                throw new Exception('이름을 입력해주세요.');
            }
            $name = $this->security->xss_clean($name);

            $phone = $this->input->post('phone');
            if(!$phone) {
                throw new Exception('휴대폰 번호를 입력해주세요.');
            }

            if($msg = $this->funcomplex_lib->exist_mb_hp($phone, $mb_id)) {
                throw new Exception($msg);
            }
            
            if($msg = valid_mb_hp($phone)) {
                throw new Exception($msg);
            }

            $email = $this->input->post('email');
            if(!$email) {
                throw new Exception('이메일을 입력해주세요.');
            }

            $email_addr = $this->input->post('email_addr');
            if(!$email_addr) {
                throw new Exception('이메일을 입력해주세요.');
            }
            
            $email = $email."@".$email_addr;
            if(!$email) {
                throw new Exception("이메일을 입력해주세요.");
            }

            $email = get_email_address($email);
            if($msg = empty_mb_email($email)) {
                throw new Exception($msg);
            }

            if($msg = valid_mb_email($email)) {
                throw new Exception($msg);
            }

            if($msg = $this->gvcomplex_lib->prohibit_mb_email($email)) {
                throw new Exception($msg);
            }

            if($msg = $this->register_lib->exist_mb_email($email, $mb_id)) {
                throw new Exception($msg);
            }

            $zip = $this->input->post('zip');
            if(!$zip) {
                throw new Exception('우편번호 찾기를 진행해주세요.');
            }

            $addr1 = $this->input->post('addr1');
            if(!$addr1) {
                throw new Exception('우편번호 찾기를 진행해주세요.');
            }

            $addr2 = $this->input->post('addr2');
            if(!$addr2) {
                throw new Exception('우편번호 찾기를 진행해주세요.');
            }

            $data = [
                'id' => $mb_id,
                'pt_id' => 'admin',
                'passwd' => $password,
                'name' => $name,
                'gender' => '',
                'email' => $email,
                'cellphone' => $phone,
                'zip' => preg_replace('/[^0-9]/', '', $zip),
                'addr1' => $this->security->xss_clean($addr1),
                'addr2' => $this->security->xss_clean($addr2),
                'today_login' => DATE_TIME,
                'reg_time' => DATE_TIME,
                'mb_ip' => $_SERVER['REMOTE_ADDR'],
                'grade' => '2', //레벨
                'use_app' => '1',
                'login_ip' => $_SERVER['REMOTE_ADDR'],
                'mailser' => $this->input->post('mailling') ? $this->input->post('mailling') : 'N',
                'smsser' => $this->input->post('sms') ? $this->input->post('sms') : 'N'
            ];

            $this->query_model->insertObj("shop_member", $data);

            $result->status = 200;
            $result->message = "success";

        } catch (Exception $e) {
			$result->status  = 400;
            $result->message = $e->getMessage();
		}
		echo json_encode($result);
    }

    // 아이디 중복확인
    public function idCheck() {
        # 아이디 중복확인
        $result = new StdClass();

        try {

            $mb_id = $this->input->post('mb_id');
            if(!$mb_id) {
                throw new Exception('아이디를 입력해주세요.');
            }

            if($msg = $this->register_lib->exist_mb_id($mb_id)) {
                throw new Exception($msg);
            }

            if ($msg = empty_mb_id($mb_id)) {
                throw new Exception($msg);
            }

            if ($msg = valid_mb_id($mb_id)) {
                throw new Exception($msg);
            }

            if ($msg = count_mb_id($mb_id)) {
                throw new Exception($msg);
            }

            if ($msg = $this->gvcomplex_lib->reserve_mb_id($mb_id)) {
                throw new Exception($msg);
            }
            
            $result->status = 200;
            $result->message = "success";
            
        } catch (Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
		}

		echo json_encode($result);
    }

// -------------------- 회원관련 끝

// -------------------- 메인 시작
    // 메인
    public function main() {
        # 메인
        $result = new stdClass();

        try {
            // 타임특가
            $timesaleList = $this->getMainGoods("timesale");
            
            // 추천상품
            $recommendList = $this->getMainGoods("recommend");
        
            // 카테고리 인기상품
            $catebestList = $this->getMainGoods("cateBest");

            // 카테고리 메뉴
            $category = $this->global_lib->get_menu();

            $categoryList = array();
            foreach($category as $row) {
                $data = array(
                    'index_no' => $row['index_no'],
                    'menu_name' => $row['menu_name']
                );

                array_push($categoryList, $data);
            }            

            $result->status = 200;
            $result->message = "success";
            $result->timesaleList = $timesaleList;
            $result->recommendList = $recommendList;
            $result->catebestList = $catebestList;
            $result->categoryList = $categoryList;

        } catch (Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
		}

		echo json_encode($result);

    }

    // main() 연관 함수
    private function getMainGoods($type) {
        $pt_id = $this->load->get_var('pt_id');
        $default = $this->load->get_var('default');
    
        $goodsList = array();
    
        // 타입에 따른 데이터 처리
        if ($type == "timesale") {
            $resultObj = $this->global_lib->display_timesale($pt_id, "8", "");
        } elseif ($type == "recommend") {
            $resultObj = $this->global_lib->display_itemtype($pt_id, "5", "8");
        } elseif ($type == "cateBest" && $default['de_maintype_best']) {
            $resultObj = unserialize(base64_decode($default['de_maintype_best']));
        }
    
        // 'cateBest' 타입의 상품 목록 처리
        if ($type == "cateBest" && $default['de_maintype_best']) {
            $goodsList = $this->getBestCategoryGoods($resultObj);
        }
    
        // 'timesale' 또는 'recommend' 타입의 상품 목록 처리
        if ($type == "timesale" || $type == "recommend") {
            $result = $resultObj->result_array();
            foreach($result as $row) {
                $goodsList[] = $this->getGoodsInfo($row);
            }
        }
    
        return $goodsList;
    }
    
    // Best 카테고리 상품 처리 함수
    private function getBestCategoryGoods($resultObj) {
        $goodsList = [];
    
        foreach ($resultObj as $key => $row) {
            if (!empty($row['subj'])) {
                $list_code = explode(",", $row['code']);
                $list_code = array_unique($list_code);
                $list_code = array_filter($list_code);
                $goodsList[$key]['subj'] = $row['subj'];
    
                foreach ($list_code as $code) {
                    $goods_data = $this->global_lib->get_goods_gcode($code);
                    if ($goods_data) {
                        $goodsList[$key][] = $this->getGoodsInfo($goods_data);
                    }
                }
            }
        }
    
        return $goodsList;
    }

    // 상품 상세 정보 생성 함수
    private function getGoodsInfo($row) {
        $it_amount = ($row['type'] == "timesale") 
            ? $this->gvcomplex_lib->get_timesale_goods_price($row['index_no']) 
            : $this->gvcomplex_lib->get_sale_price($row['index_no']);
        
        $sale = ($row['normal_price'] > $it_amount)
            ? number_format(($row['normal_price'] - $it_amount) / $row['normal_price'] * 100, 0)
            : 0;
    
        // 품절 상태 처리
        switch ($row['isopen']) {
            case 1: $display_txt = "판매"; break;
            case 2: $display_txt = "품절"; break;
            case 3: $display_txt = "단종"; break;
            case 4: $display_txt = "중지"; break;
            case 5: $display_txt = "판매종료"; break;
            default: $display_txt = "";
        }
    
        return [
            'index_no' => $row['index_no'],
            'ca_id' => $row['ca_id'],
            'ca_id2' => $row['ca_id2'],
            'ca_id3' => $row['ca_id3'],
            'gcode' => $row['gcode'],
            'gname' => $row['gname'],
            'normal_price' => $row['normal_price'],
            'goods_price' => $row['goods_price'],
            'sale' => $sale,
            'simg1' => $this->funcomplex_lib->get_it_image_url($row['index_no'], $row['simg1'], 275, 352),
            'isopen' => $row['isopen'],
            'display_txt' => $display_txt,
            'type' => $row['type'],
            'sb_date' => $row['sb_date'] ?? null, // 타임세일 날짜
            'eb_date' => $row['eb_date'] ?? null, // 타임세일 종료 날짜
        ];
    }

    // 1차 카테고리 메뉴
    public function firstMenu(){
        # 1차 카테고리 메뉴
        $result = new StdClass();
        
        try {
            $sub = trim($this->input->get('sub'));
            $category_data = $this->api_model->getSubMenu($sub);

            $categoryData = array();
            foreach ($category_data as $res){
                $catecode   = $res['catecode'];
                $upcate     = $res['upcate'] != null ? $res['upcate'] : '';
                $catename   = $res['catename'];

                $category_array = array(
                    'catecode'  => $catecode,
                    'upcate'    => $upcate,
                    'catename'  => $catename
                );
                
                $categoryData[] = $category_array;
            }


            $result->status = 200;
            $result->message = "success";
            $result->category = $categoryData;

        } catch(Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }
        echo json_encode($result);
    }

    // 1차 + 2차 카테고리 메뉴
    public function secondMenu() {
        # 1차 + 2차 카테고리 메뉴
        $result = new StdClass();

        try {
            $all_category = $this->gvcomplex_lib->sql_query_cgy("all");
            if(!$all_category) {
                throw new Exception('등록 된 카테고리가 없습니다.');
            }
            
            $category = array();
            $category_data = array();

            foreach($all_category as $key => $row) {
                $sub_category = $this->gvcomplex_lib->sql_query_cgy($all_category[$key]['catecode']);

                $category = array(
                    'index_no'  => $all_category[$key]['index_no'],
                    'catecode'  => $all_category[$key]['catecode'],
                    'upcate'    => $all_category[$key]['upcate'],
                    'catename'  => $all_category[$key]['catename'],
                    'cateimg1'  => $all_category[$key]['cateimg1'],
                    'cateimg2'  => $all_category[$key]['cateimg2'],
                    'headimg'   => $all_category[$key]['headimg'],
                    'headimgurl'=> $all_category[$key]['headimgurl'],
                    'caterank'  => $all_category[$key]['caterank'],
                    'cateuse'   => $all_category[$key]['cateuse'],
                    'catehide'  => $all_category[$key]['catehide'],
                    'suvmenu'   => $sub_category
                );

                array_push($category_data,$category);
            }

            $result->status = 200;
            $result->message = "success";
            $result->category = $category_data;

        } catch (Exception $e) {
			$result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 알림 개수
    public function alarmCnt() {
        # 알림 개수 확인
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            
            $total_cnt = "";
            if(!empty($mb_id)) {
                $total_cnt = $this->api_model->getAlarmCnt($mb_id);
            }

            $result->status   = 200;
            $result->message  = "success";
            $result->cnt      = $total_cnt;

        } catch (Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 메인 메뉴 회원 이름 가져오기
    public function getName() {
        # 회원 이름 가져오기
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');

            $member_info = $this->global_lib->get_member($mb_id, 'name');
            $name = $member_info['name'];

            $result->status = 200;
            $result->message = "success";
            $result->name = $name;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

// -------------------- 메인 끝

// -------------------- 푸터 시작
    // 푸터 정보
    public function footerInfo() {
        # 사업자 정보 확인 (푸터)
        $result = new stdClass();

        try {
            $config = $this->load->get_var('config');

            $data = array(
                'company_owner' => $config['company_owner'],
                'company_addr' => $config['company_addr'],
                'company_saupja_no' => $config['company_saupja_no'],
                'tongsin_no' => $config['tongsin_no'],
                'company_tel' => $config['company_tel'],
                'tongsin_no' => $config['tongsin_no'],
                'company_fax' => $config['company_fax'],
                'company_fax' => $config['company_fax'],
                'email' => $config['info_email'],
                'info_name' => $config['info_name'],
                'info_email' => $config['info_email'],
            );

            $result->status  = 200;
            $result->message = "success";
            $result->data = $data;

        } catch(Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }
// -------------------- 푸터 끝

// -------------------- 상품관련 시작

    // 상품 검색화면 추천 상품
    public function recommendGoods() {
        # 상품 검색 추천상품
        $result = new stdClass();

        try {
            $goods_data = $this->api_model->getRecommendGoods();

            $goodsList = array();
            foreach($goods_data as $row) {
                $data = array(
                    'gs_id' => $row['index_no'],
                    'gname' => $row['gname']
                );

                array_push($goodsList,$data);
            }

            $result->status   = 200;
            $result->message  = "success";
            $result->recommendList  = $goodsList;

        } catch(Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 상품 검색
    public function searchGoods() {
        # 상품 검색 화면
        $result = new stdClass();

        try {
            $word = $this->input->get('word');
            if(!$word) {
                throw new Exception('검색어를 입력해주세요.');
            }

            $concat = ["a.gname", "a.explan", "a.gcode"];
            $concat_fields = "CONCAT(".implode(",' ',", $concat).")";
            $sql_search = "AND ($concat_fields LIKE '%$word%' OR find_in_set('$word', a.keywords) >= 1 ".") ";

            $search_data = $this->api_model->searchGoods($sql_search);
            if(!$search_data) {
                throw new Exception('검색된 상품이 없습니다.');
            }
            $goodsList = array_map([$this, 'getGoodsInfo'], $search_data);

            $result->status   = 200;
            $result->message  = "success";
            $result->word  = $word;
            $result->goodsList = $goodsList;
        } catch(Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 메인 카테고리 클릭 상품 리스트 (확인필요)
    public function mainGoodsList() {
        # 메인 상단 카테고리 클릭시 나오는 상품 리스트
        $result = new stdClass();
    
        try {
            $type = $this->input->get('type');
            if (!$type) {
                throw new Exception('타입 정보가 없습니다.');
            }

            $page = $this->input->get('page');
            $pt_id = $this->load->get_var('pt_id');

            // 카테고리 메뉴
            $category = $this->global_lib->get_menu_info($type);
            
            // bid, 기획전은 따로
            // listtype 인 경우 뒤에 타입만 따로 빼기
            $cate_arr = explode('/', $category['url']);

            if(strpos($cate_arr[1], 'listtype') !== false) {
                $type_arr = explode('=', $cate_arr[1]);
                $type = $type_arr[1];
            } else {
                echo "미포함";
            }
            // 기본 쿼리 구성
            $sql_search = "";
            $sql_order = "ORDER BY a.index_no DESC";
            $resultObj = $this->global_lib->query_itemtype($pt_id, $type, $sql_search, $sql_order);
    
            // 전체 아이템 수
            $total_count = $resultObj->num_rows();
    
            // 페이지 처리
            $page_res = $this->getLimit($page, 10, $total_count);
            $sql_order .= " LIMIT {$page_res['from_record']}, {$page_res['rows']}";

            switch($category['menu_name']) {
                case '함께장터':
                    break;
                case '기획전':
                    break;
                case '베스트셀러':
                case '신상품':
                case '인기상품':
                case '추천상품':
                    $resultObj = $this->global_lib->query_itemtype($pt_id, $type, $sql_search, $sql_order);
                    $goodsList = array_map([$this, 'getGoodsInfo'], $resultObj->result_array());

                    break;
                default:
                    break;
            }

            if($type == "함께장터") {
                $sql_search = " and type='timesale' ";
            }
    
            // 페이지에 맞는 상품 리스트 재조회
            // $resultObj = $this->global_lib->query_itemtype($pt_id, $type, "", $sql_order);

    
            // 결과 반환
            $result->status = 200;
            $result->message = "success";
            $result->goodsList = $goodsList;
    
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }
    
        echo json_encode($result);
    }
    

    // 일반 카테고리 클릭 상품 리스트
    public function goodsList() {
        # 메뉴 클릭 후 나오는 카테고리 선택후 나오는 상품리스트
        $result = new stdClass();
        
        try {
            $ca_id = $this->input->get('ca_id');
            if(!$ca_id) {
                throw new Exception('카테고리를 선택해주세요.');
            }

            $page = $this->input->get('page') ?? 1;

            $sql = "SELECT * FROM shop_category WHERE catecode = ? AND cateuse = '0'";
            $param = array($ca_id);
            $category = $this->query_model->returnOneArr($sql,$param);
    
            if(!$category['catecode']) {
                throw new Exception('등록된 카테고리가 없습니다.');
            }

            $sql_search = " and (ca_id like '$ca_id%' or ca_id2 like '$ca_id%' or ca_id3 like '$ca_id%') ";
            $sql_common = $this->gvcomplex_lib->sql_goods_list($sql_search);

            // 테이블의 전체 레코드수만 얻음
            $sql = "SELECT count(*) AS cnt $sql_common $sql_search";
            $row = $this->query_model->returnOneArr($sql);

            $page_res = $this->getLimit($page, 10, $row['cnt']);
            if(isset($page_res['msg'])) {
                throw new Exception($page_res['msg']);
            }

            // 상품 정렬
            $sql_order = " order by `rank` desc, index_no desc ";

            $sql = "SELECT * $sql_common $sql_order LIMIT {$page_res['from_record']}, {$page_res['rows']}";
            $goods_data = $this->query_model->returnArr($sql);
            $goodsList = array_map([$this, 'getGoodsInfo'], $goods_data);

            $result->status = 200;
            $result->message = "success";
            $result->page = $page;
            $result->goodsList = $goodsList;

        } catch (Exception $e) {
			$result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    public function timesale() {
        # bid (타임세일)
        $result = new stdClass();

        try {
            $page = $this->input->get('page');

            $sql = "select * from shop_category where catecode = ? and cateuse = '0' and find_in_set('$pt_id', catehide) = '0' ";
            $param = array($ca_id);
            $ca = $this->query_model->returnOneArr($sql,$param);
    
            $title = $default['de_pname_8'];
            $sql_search = " and (ca_id like '$ca_id%' or ca_id2 like '$ca_id%' or ca_id3 like '$ca_id%') ";
            $sql_search .= " and type='timesale' ";
            $sql_common = $this->gvcomplex_lib->sql_goods_list($sql_search);

            // 테이블의 전체 레코드수만 얻음
            $sql = " SELECT COUNT(*) AS cnt $sql_common ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            $page_res = $this->getLimit($page, 10, $total_count);

            $sql = "SELECT * $sql_common LIMIT {$page_res['from_record']}, {$page_res['rows']} ";

            $result = $this->query_model->returnArr($sql);

            print_r($result);
            exit;

            $result->status = 200;
            $result->message = "success";
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 기획전 리스트
    public function plan() {
        # 기획전 리스트
        $result = new stdClass();

        try {
            $pt_id = $this->load->get_var('pt_id');
            $sql = "SELECT * from shop_goods_plan where mb_id IN('admin','{$pt_id}') and pl_use = '1' ";
            $res = $this->query_model->returnArr($sql);

            $planList = array();
            foreach($res as $row) {
                $pl_img = (!empty($row['pl_limg'])) ? base_url()."data/plan/{$row['pl_limg']}" : "";
                $data = array(
                    'pl_no' => $row['pl_no'],
                    'pl_name' => $row['pl_name'],
                    'pl_limg' => $pl_img
                );

                array_push($planList, $data);
            }

            $result->status = 200;
            $result->message = "success";
            $result->planList = $planList;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 기획전 상세 리스트
    public function planList() {
        # 기획전 상품 리스트
        $result = new stdClass();

        try {
            $pl_no = $this->input->get('pl_no');
            if(!$pl_no) {
                throw new Exception('기획전을 선택해주세요.');
            }

            $page = $this->input->get('page') ?? 1;

            $sql = "SELECT * FROM shop_goods_plan WHERE pl_no = ?";
            $res = $this->query_model->returnOneArr($sql, array($pl_no));

            $plan_img = base_url()."data/plan/{$res['pl_limg']}";

            $pl_it_code = explode("\n", $res['pl_it_code']);
            $pl_it = mb_comma($pl_it_code);

            $sql_search = " and gcode IN({$pl_it}) ";
            $sql_common = $this->gvcomplex_lib->sql_goods_list($sql_search);

            // 테이블의 전체 레코드수만 얻음
            $sql = " SELECT COUNT(*) AS cnt $sql_common ";
            $row = $this->query_model->returnOneArr($sql);
            $total_count = $row['cnt'];

            $page_res = $this->getLimit($page, 10, $total_count);

            $sql = "SELECT * $sql_common LIMIT {$page_res['from_record']}, {$page_res['rows']} ";
            $event_data = $this->query_model->returnArr($sql);
            $goodsList = array_map([$this, 'getGoodsInfo'], $event_data);
    
            $result->status = 200;
            $result->message = "success";
            $result->planImg = $plan_img;
            $result->goodsList = $goodsList;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 상품정보
    public function goodsInfo() {
        # 상품 상세보기
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $gs_id = $this->input->get('gs_id');
            
            // 상품 정보
            $goods_info = $this->global_lib->get_goods($gs_id);
            if (!$goods_info) {
                throw new Exception('상품 정보가 존재하지 않습니다.');
            }

            // 상품조회 카운터하기
            $this->api_model->updateGoodsCnt($gs_id);
        
            // 배송비
            $this->load->vars(array('gs'=>$goods_info));
            $sendcost = $this->funcomplex_lib->get_sendcost_amt();

            // 필수 옵션
            $option_item = $this->funcomplex_lib->get_item_options_api($gs_id, $goods_info['opt_subject']);
            
            // 추가 옵션
            $supply_item = $this->funcomplex_lib->get_item_supply_api($gs_id, $goods_info['spl_subject']);

            // 품절 상태 처리
            $display_txt = "";
            switch ($goods_info['isopen']) {
                case "1": $display_txt = "판매"; break;
                case "2": $display_txt = "품절"; break;
                case "3": $display_txt = "단종"; break;
                case "4": $display_txt = "중지"; break;
                case "5": $display_txt = "판매종료"; break;
                default: $display_txt = "";
            }

            // 찜체크 시 데이터 
            $zzim = "N";
            if(isset($mb_id)) {
                // 로그인 상태 일 경우
                $with_cnt = $this->api_model->getWishCnt($mb_id, $goods_info['index_no']);

                $zzim = $with_cnt == 0 ? 'N' : 'Y'; 
            }

            $is_soldout = $this->funcomplex_lib->is_soldout($gs_id);
            // $is_soldout_timesale = $this->funcomplex_lib->is_soldout_timesale($gs_id);
            $is_sale_end = $this->funcomplex_lib->is_sale_end($gs_id);

            if($is_soldout) {
                $display_txt = "품절";
            }
            if($is_sale_end) {
                $display_txt = "판매종료";
            }

            $stock_qty = 0;
            $order_qty = 0;
            if($goods_info['type'] == "timesale") {
                $stock_qty = $this->global_lib->get_it_timesale_stock_qty($gs_id);
                $stock_qty = $stock_qty < 0 ? 0 : $stock_qty;
    
                $order_qty = $this->global_lib->get_order_cnt($gs_id);
                $order_qty = ($goods_info['original_stock_qty'] < $order_qty) ? $goods_info['original_stock_qty'] : $order_qty;
            }

            // 상품 썸네일
            $thumbTag = $this->thumbnail_lib->get_view_thumbnail($this->funcomplex_lib->conv_content($goods_info['memo'], 1), 1000);
            
            // 상품정보고시
            $info_data = unserialize(stripslashes(
                isset($goods_info['info_value']) != false ? $goods_info['info_value'] : isset($goods_info['info_value'])
            ));

            $it_amount = $this->gvcomplex_lib->get_sale_price($goods_info['index_no']);
            $is_uncase = $this->funcomplex_lib->is_uncase($goods_info['index_no']);

            // (시중가 - 할인판매가) / 시중가 X 100 = 할인률%
            $sale = "";
            if($goods_info['normal_price'] > $it_amount && !$is_uncase) {
                $sale = number_format(($goods_info['normal_price'] - $it_amount) / $goods_info['normal_price'] * 100,0);
            }

            $info = array(
                'gs_id'         => $goods_info['index_no'],
                'ca_id'         => $goods_info['ca_id'],
                'ca_id2'        => $goods_info['ca_id2'],
                'ca_id3'        => $goods_info['ca_id3'],
                'gname'         => $goods_info['gname'],
                'explan'        => $goods_info['explan'],
                'goods_price'   => $goods_info['goods_price'],
                'normal_price'  => $goods_info['normal_price'],
                'sale'          => $sale,
                'price_msg'     => $goods_info['price_msg'],
                'origin'        => $goods_info['origin'],
                'gcode'         => $goods_info['gcode'],
                'isopen'        => $goods_info['isopen'],
                "display_txt"   => $display_txt,
                "zzim"          => $zzim,
                "sendcost"      => $sendcost,
                "option"        => $goods_info['opt_subject'],
                "option_data"   => empty($option_item) ? array() : $option_item,
                "spl_option"    => $goods_info['spl_subject'],
                "spl_option_data" => empty($supply_item) ? array() : $supply_item,
                'thumbTag'      => $thumbTag,
                'info_data'     => $info_data,
                'sb_date'       => $goods_info['sb_date'],
                'eb_date'       => $goods_info['eb_date'],
                'stock_qty'     => $stock_qty,
                'order_qty'     => $order_qty,
                'type'          => $goods_info['type']
            );

            // 상품 이미지
            $imgArr = ['simg1', 'simg2', 'simg3', 'simg4', 'simg5', 'simg6'];

            foreach($imgArr as $row) {
                if(!empty($goods_info[$row])) {
                    $info[$row] = $this->funcomplex_lib->get_it_image_url($goods_info['index_no'], $goods_info[$row], 640, 640);
                }
            }

            $result->status = 200;
            $result->message = "success";
            $result->info = $info;
            $result->mb_id = $mb_id;
            $result->status = 200;

            if($goods_info['type'] == "timesale") {
                $timesale_list = $this->global_lib->get_timesale_list2($gs_id);
                $timesaleList = array();
                foreach($timesale_list as $key => $row) {
                    $num = $key+1;

                    $length = strlen($row['mb_id']);
                    $start = substr($row['mb_id'],0,3);
                    $masking = "";
                
                    for($i = 0;$i < ($length - 3);$i++) {
                        $masking .= "*";
                    }
                
                    $hiddenId = $start.$masking;
                    $price = empty($row['gts_no']) ? $row['normal_price'] : $row['real_price'];

                    $data = array(
                        'num' => $num,
                        'mb_id' => $hiddenId,
                        'purchase_date' => $row['receipt_time'],
                        'price' => $price
                    );

                    array_push($timesaleList, $data);
                }
                $result->timesaleList = $timesaleList;
            }
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 찜하기
    public function wishCheck() {
        # 찜하기
        $result = new StdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }
            
            $gs_id = $this->input->get('gs_id');
            if(!$gs_id) {
                throw new Exception("상품 정보가 존재하지 않습니다.");
            }

            $with_cnt = $this->api_model->getWishCnt($member_data['id'],$gs_id);
            
            if($with_cnt) {
                $where = array(
                    'mb_id' => $member_data['id'],
                    'gs_id' => $gs_id
                );
                $this->query_model->delete("shop_wish",$where);
                $text = "삭제";
            } else {
                $data = array(
                    'mb_id' => $member_data['id'],
                    'gs_id' => $gs_id,
                    'wi_time' => DATE_TIME,
                    'wi_ip' => $_SERVER['REMOTE_ADDR']
                );
                $this->query_model->insertObj("shop_wish",$data);
                $text = "추가";
            }

            $result->status = 200;
            $result->message = "success";
            $result->text = $text;

        } catch (Exception $e) {
			$result->status  = 400;
            $result->message = $e->getMessage();
		}

		echo json_encode($result);
    }

    // 구매후기
    public function goodsReview() {
        # 상품 구매후기
        $result = new stdClass();

        try {
            $gs_id = $this->input->get('gs_id');
            if(!$gs_id) {
                throw new Exception('상품 정보가 없습니다.');
            }

            $review_data = $this->funcomplex_lib->getReviewData($gs_id);

            $img_arr = ['img1', 'img2', 'img3', 'img4'];
            $reviewData = array();
            foreach($review_data['result'] as $row) {
                $member_data = $this->global_lib->get_member($row['mb_id'], 'mb_img');

                $data = array(
                    'index_no' => $row['index_no'],
                    'mb_img' => $this->funcomplex_lib->get_it_mb_image_url($row['mb_id'], $member_data['mb_img'], 90, 90),
                    'mb_id' => $row['mb_id'],
                    'score' => $row['score'],
                    'reg_time' => $row['reg_time'],
                    'memo' => $row['memo']
                );

                foreach($img_arr as $row2) {
                    if(!empty($row[$row2])) {
                        $media_type = $this->funcomplex_lib->getMediaType($row[$row2]);
                        $data2 = $this->funcomplex_lib->get_it_review_image_url($row[$row2], 90, 90);
    
                        if($media_type == "video") {
                            $data[$row2] = "동영상";
                        } else {
                            $data[$row2] = $data2;
                        }
                    }
                }

                array_push($reviewData, $data);
            }

            $result->status = 200;
            $result->message = "success";
            $result->reviewData = $reviewData;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // Q&A
    public function goodsQna() {
        # 상품 QNA
        $result = new stdClass();

        try {
            $gs_id = $this->input->get('gs_id');
            if(empty($gs_id)) {
                throw new Exception('상품 정보가 없습니다.');
            }

            $is_myQna = $this->input->get('is_myQna'); // 내 qna

            $is_answer = $this->input->get('is_answer'); // 답변상태
            
            $where_sql = "";
            if($is_myQna == "y") {
                $mb_id = $this->input->get('mb_id');
                $member_data = $this->global_lib->get_member($mb_id);
                if(!$member_data) {
                    throw new Exception('회원 정보가 존재하지 않습니다.');
                }
                $where_sql .= " and mb_id = '{$mb_id}'";
            }

            if($is_answer == "y") { // 답변완료
                $where_sql .= " and iq_reply = 1";
            } else if($is_answer == "n") { // 답변대기
                $where_sql .= " and iq_reply = 0";
            }

            $page = $this->input->get('page') ?? 1;
            
            $total_count = $this->api_model->getQnaCnt($gs_id);
            $page_res = $this->getLimit($page, 10, $total_count);
            if(isset($page_res['msg'])) {
                throw new Exception($page_res['msg']);
            }

            $res = $this->api_model->getQnaList($gs_id, $page_res['from_record'], $page_res['rows'], $where_sql);

            $qnaList = array();
            foreach($res as $row) {
                $data = array(
                    'subject' => $row['iq_subject'],
                    'question' => $row['iq_question'],
                    'regdate' => $row['iq_time'],
                    'answer' => $row['iq_answer'],
                    'answer_date' => $row['iq_answer_date'],
                    'mb_id' => $row['mb_id'],
                    'regdate' => $row['iq_time'],
                    'is_answer' => $row['iq_reply'] == 1 ? "답변완료" : "답변대기",
                );

                array_push($qnaList, $data);
            }

            $result->status = 200;
            $result->message = "success";
            $result->qnaList = $qnaList;
            $result->total_count = $total_count;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // Q&A 작성 화면
    public function goodsQnaForm() {
        # 상품 Q&A 작성 화면
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $gs_id = $this->input->get('gs_id');
            $goods_info = $this->global_lib->get_goods($gs_id);
            if(empty($goods_info)) {
                throw new Exception('상품 정보가 존재하지 않습니다.');
            }

            $options = $this->api_model->getQnaOptions();

            $optionArr = array();
            foreach($options as $row) {
                $data = array(
                    'index_no' => $row['index_no'],
                    'option_name' => $row['catename']
                );

                array_push($optionArr, $data);
            }

            $result->status = 200;
            $result->message = "message";
            $result->mb_id = $mb_id;
            $result->goods_name = $goods_info['gname'];
            $result->optionArr = $optionArr;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // Q&A 작성
    public function goodsQnaWrite() {
        # 상품 Q&A 작성
        $result = new stdClass();

        try {
            $mb_id = $this->input->post('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $gs_id = $this->input->post('gs_id');
            $goods_info = $this->global_lib->get_goods($gs_id);
            if(empty($goods_info)) {
                throw new Exception('상품 정보가 존재하지 않습니다.');
            }

            $option = $this->input->post('option');
            if(!$option) {
                throw new Exception('질문 유형을 선택해주세요.');
            }

            $is_secret = $this->input->post('is_secret');
            if(!$is_secret) {
                throw new Exception('비밀글 여부를 선택해주세요.');
            }

            $subject = $this->input->post('subject');
            if(!$subject) {
                throw new Exception('제목을 입력해주세요.');
            }

            $question = $this->input->post('question');
            if(!$question) {
                throw new Exception('내용을 입력해주세요.');
            }

            $param = array(
                'gs_id' => $gs_id,
                'mb_id' => $mb_id,
                'iq_ty' => $option,
                'iq_secret' => ($is_secret == "y") ? 1 : 0,
                'iq_subject' => $subject,
                'iq_question' => $question,
                'iq_time' => DATE_TIME,
                'iq_ip' => $_SERVER['REMOTE_ADDR']
            );

            $this->query_model->insertObj("shop_goods_qa", $param);

            $result->status = 200;
            $result->message = "success";
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 배송/반품/교환
    public function goodsEtc() {
        # 배송/반품/교환
        $result = new stdClass();

        try {
            $gs_id = $this->input->get('gs_id');
            $goods_info = $this->global_lib->get_goods($gs_id);
            if(empty($goods_info)) {
                throw new Exception('상품 정보가 존재하지 않습니다.');
            }

            // 배송/반품/교환 정보
            $baesongInfo = $this->thumbnail_lib->get_view_thumbnail($this->funcomplex_lib->conv_content($this->funcomplex_lib->get_policy_content($gs_id), 1), 1000);

            $result->status = 200;
            $result->message = "success";
            $result->baesongInfo = $baesongInfo;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

// -------------------- 상품관련 끝

// -------------------- 주문관련 시작

    // 장바구니 개수 확인
    public function cartCnt() {
        # 장바구니 개수 확인

        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');

            $cartCnt = $this->gvcomplex_lib->get_cart_count($mb_id);

            $result->status = 200;
            $result->message = "success";
            $result->cartCnt = $cartCnt;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 장바구니 담기
    public function cartUpdate() {
        # 장바구니 담기
        $result = new StdClass();

        try {
            $mb_id = $this->input->post('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            // 상품ID
            $gs_id = $this->input->post('gs_id');
            $gs_id_arr = explode('|',$gs_id);

            // 수량 초기화
            $ct_qty = $this->input->post('ct_qty') ?? 1;
            $ct_qty_arr = explode('|',$ct_qty);

            // 사용자가 선택한 옵션 받아오기
            $option_type = $this->input->post('option_type'); // 0: 필수 / 1: 선택
            $option_type_arr = explode('|',$option_type);
            
            $option = $this->input->post('option'); // io_id (옵션항목)
            $option_arr = explode('|',$option);
            
            $option_value = $this->input->post('option_value'); // ct_option (옵션명)
            $option_value_arr = explode('|',$option_value);

            foreach($gs_id_arr as $num => $idx) {
                if(!isset($option_type_arr[$num])) {
                    $option_type_arr[$num] = "";
                }

                if(!isset($option_arr[$num])) {
                    $option_arr[$num] = "";
                }

                if(!isset($option_value_arr[$num])) {
                    $option_value_arr[$num] = "";
                }
            }

            $od_id_arr = array();
            $cart_idx_arr = array();
            $od_id = $this->global_lib->get_uniqid();

            foreach($gs_id_arr as $key => $row) {
                $goods_info = $this->global_lib->get_goods($row);
                if(!$goods_info) {
                    throw new Exception('상품 정보가 존재하지 않습니다.');
                }

                // if($goods_info['type'] == "timesale") {
                //     $sql = "SELECT * FROM shop_order WHERE mb_id = ? AND gs_id = ? AND (dan IN ('2','3','4','5'))";
                //     $data = array($mb_id, $gs_id);
                //     $is_alreay = $this->query_model->returnOneArr($sql,$data);
                //     if(!empty($is_alreay)) {
                //         throw new Exception('이미 구매한 상품입니다. bid 상품은 1개만 구매 가능합니다.');
                //     }
                // }

                $this->load->vars(array('member'=>$member_data));

                if($goods_info['type'] == "timesale") {
                    $goods_info['goods_price'] = $this->gvcomplex_lib->get_timesale_goods_price($gs_id);                
                } else {
                    $goods_info['goods_price'] = $this->gvcomplex_lib->get_sale_price($gs_id);                
                }

                if(!$goods_info) {
                    throw new Exception('상품 정보가 존재하지 않습니다.');
                }

                if (!isset($ct_qty_arr[$key]) || empty($ct_qty_arr[$key])) {
                    $ct_qty_arr[$key] = 1;
                }

                // 옵션정보를 얻어서 배열에 저장
                $opt_list = array();
                $sql = "SELECT * FROM shop_goods_option WHERE gs_id = ? ORDER BY io_no ASC ";
                $data = array($row);
                $option_data = $this->query_model->returnArr($sql,$data);
                $lst_count = 0;
                
                foreach($option_data as $key2 => $row2){
                    $opt_list[$row2['io_type']][$row2['io_id']]['id'] = $row2['io_id'];
                    $opt_list[$row2['io_type']][$row2['io_id']]['use'] = $row2['io_use'];
                    $opt_list[$row2['io_type']][$row2['io_id']]['price'] = $row2['io_price'];
                    $opt_list[$row2['io_type']][$row2['io_id']]['stock'] = $row2['io_stock_qty'];
        
                    // 주문옵션 개수
                    if(!$row2['io_type']) {
                        $lst_count++;
                    }
                }
                
                if($opt_list && !$option_arr[$key] && !$option_value_arr[$key]) {
                    throw new Exception('상품의 옵션을 선택해주세요.');
                }

                $io_price = isset($opt_list[$option_type_arr[$key]][$option_arr[$key]]['price']) ? $opt_list[$option_type_arr[$key]][$option_arr[$key]]['price'] : 0;

                // 재고 구허가
                if(!$option_arr[$key]) {
                    $it_stock_qty = $this->global_lib->get_it_stock_qty($row);
                } else {
                    $it_stock_qty = $this->global_lib->get_option_stock_qty($row, $option_arr[$key], $option_type_arr[$key]);
                }

                if($ct_qty_arr[$key] > $it_stock_qty) {
                    throw new Exception($option_arr[$key]." 의 재고수량이 부족합니다.\\n\\n현재 재고수량 : " . number_format($it_stock_qty) . " 개");
                }

                $insert_id = "";

                // 바로 구매인지 확인    
                $is_direct = $this->input->post('is_direct');
                if(!$is_direct) {
                    throw new Exception('바로 구매 정보가 넘어오지 않았습니다.');
                }

                // 장바구니 존재 여부
                $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
                $od_no = $this->global_lib->cart_uniqid();
            
                // 포인트 계산
                $point = 0;
                $grade = $member_data['grade'];

                $sql = "SELECT gb_buy_point FROM shop_member_grade WHERE gb_no = ?";
                $param = array($grade);
                $res = $this->query_model->returnOneArr($sql,$param);

                if($goods_info['is_point'] > 0) {
                    $gb_point = $res['gb_buy_point']; // 적립률
                    $point = (int) round((($goods_info['goods_price'] + $io_price) * $ct_qty) * ($gb_point * (1/100))); // 적립 포인트
                }

                // 장바구니
                if($is_direct == "n") {

                    $resObj = $this->api_model->getCartData($member_data['id'], $goods_info['index_no'], $option_arr[$key]);            

                    // 동일한 상품이 장바구니에 존재할 경우
                    if ($resObj->num_rows() >= 1) { 
                        // $io_type = 1; 
                        $sameObj = $resObj->row_array();

                        $total_qty = 0; // 동일한 상품의 수량을 합친다.

                        if($sameObj['gs_id'] == $row) {
                            $total_qty = (int)$ct_qty_arr[$key] + (int)$sameObj['ct_qty'];
                        }

                        $index_no = $sameObj['index_no'];
                        $this->api_model->updateCart($total_qty, $member_data['id'],$row,$index_no);

                        $msg = "기존 장바구니 추가";
                        $cart_idx_arr[$key] = intval($index_no);

                    // 동일한 상품 존재하지 않음
                    } else {
                        
                        // 동일한 상품이 아닐 경우
                        $cartadd = array(
                            "ca_id"     => $goods_info['ca_id'],
                            'od_id'     => "",
                            'mb_id'     => $member_data['id'],
                            'gs_id'     => $goods_info['index_no'],
                            'ct_time'   => $OM_TIME_YMDHIS,
                            'ct_price'  => $goods_info['goods_price'],
                            "ct_supply_price" => $goods_info['supply_price'],
                            "ct_qty"    => $ct_qty_arr[$key],
                            "ct_point"  => $point,
                            "io_id"     => $option_arr[$key],
                            "io_type"   => $option_type_arr[$key],
                            "io_price"  => isset($opt_list[$option_type_arr[$key]][$option_arr[$key]]['price']) ? $opt_list[$option_type_arr[$key]][$option_arr[$key]]['price'] : 0,
                            "ct_option" => $option_value_arr[$key],
                            "ct_send_cost" => 0,
                            "od_no"     => $od_no,
                            "ct_ip"     => $_SERVER['REMOTE_ADDR'],
                            "is_direct" => $is_direct
                        );

                        $insert_id = $this->query_model->insertObj('shop_cart', $cartadd);
                        if(!$insert_id) {
                            throw new Exception('장바구니 저장 중 오류가 발생했습니다.');
                        }

                        $msg = "새 장바구니 추가";
                        $cart_idx_arr[$key] = $insert_id;
                    }

                // 바로구매 할 경우
                } else {

                    $cartAdd = array(
                        "ca_id"     => $goods_info['ca_id'],
                        'od_id'     => "",
                        'mb_id'     => $member_data['id'],
                        'gs_id'     => $goods_info['index_no'],
                        'ct_time'   => $OM_TIME_YMDHIS,
                        'ct_price'  => $goods_info['goods_price'],
                        "ct_supply_price" => $goods_info['supply_price'],
                        "ct_qty"    => $ct_qty_arr[$key],
                        "ct_point"  => $point,
                        "io_id"     => $option_arr[$key],
                        "io_type"   => $option_type_arr[$key],
                        "io_price"  => isset($opt_list[$option_type_arr[$key]][$option_arr[$key]]['price'])?$opt_list[$option_type_arr[$key]][$option_arr[$key]]['price']:0,
                        "ct_option" => $option_value_arr[$key],
                        "ct_send_cost" => 0,
                        "od_no"     => $od_no,
                        "ct_ip"     => $_SERVER['REMOTE_ADDR'],
                        "is_direct" => ($is_direct == "n") ? 0 : 1
                    );

                    $insert_id = $this->query_model->insertObj('shop_cart', $cartAdd);
                    if(!$insert_id) {
                        throw new Exception('장바구니 저장 중 오류가 발생했습니다.');
                    }

                    $msg = "바로구매";
                    $od_id_arr[$key] = $od_id;
                    $cart_idx_arr[$key] = $insert_id;
                }

            }

            $result->status = 200;
            $result->message = "success";
            $result->od_id = $od_id_arr;
            $result->cart_idx = $cart_idx_arr;
            $result->msg = $msg;

        } catch(Exception $e){
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 장바구니 리스트
    public function cartList() {
        # 장바구니 리스트
        $result = new StdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            // 장바구니 데이터 가져오기
            $cart_list = $this->api_model->getCartList($member_data['id']);

            // 장바구니 총 상품 금액 합계
            $cart_total_price = $this->api_model->getCartTotalPrice($mb_id);
            
            $cartList = array();
            $i = 0;
            $totalSum = 0; // 상품 + 배송비 총 가격
            $goodsSum = 0; // 상품 총가격

            foreach ($cart_list as $key => $row) {
                // 상품 정보의 배열을 리턴한다.
                $goods_info = $this->global_lib->get_goods($row['gs_id']);
                $this->load->vars(array('gs'=>$goods_info));

                // 장바구니의 저장된 합계 금액을 계산한다.
                $total_sql = "SELECT SUM(IF(io_type = 1, (io_price * ct_qty),((io_price + ct_price) * ct_qty))) as price,
                                    SUM(IF(io_type = 1, (0),(ct_point * ct_qty))) as point,
                                    SUM(IF(io_type = 1, (0),(ct_qty))) as qty,
                                    SUM(io_price * ct_qty) as opt_price
                            FROM  shop_cart
                            WHERE index_no = ? AND mb_id = ? AND ct_select = '0' AND is_direct = 0";

                $param = array($row['index_no'], $member_data['id']);
                $sum  = $this->query_model->returnOneArr($total_sql, $param);

                $goodsSum = $goodsSum + $sum['price'];
                $sell_price = $sum['price'];

                // 배송비 구함
                $info = $this->funcomplex_lib->get_item_sendcost($row, $sell_price, $cart_total_price);
                if (!$info) {
                    throw new Exception('배송비 정보 호출 실패');
                }

                $item_sendcost[] = $info['pattern'];
                $info['price'] = isset($info['price']) ? $info['price'] : 0;

                $temp = array(
                    'cart_idx'      => $row['index_no'],
                    'od_id'         => $row['od_id'],
                    'on_no'         => $row['od_no'],
                    'gs_id'         => $row['gs_id'],
                    'ct_get_img'    => $this->funcomplex_lib->get_it_image_url($goods_info['index_no'], $goods_info['simg1'], 100, 100),
                    'ct_qty'        => $row['ct_qty'],
                    'ct_price'      => $row['ct_price'],
                    'goods_name'    => $goods_info['gname'],
                    'ct_option_type'=> $row['io_type'],
                    'ct_option'     => $row['ct_option'],
                    'ct_option_text'=> $row['io_id'],
                    'ct_option_price' => $row['io_price'],
                    'delivery'      => $info['price'],
                    'mb_id'         => $row['mb_id'],
                    'is_direct'     => $row['is_direct'],
                );
                
                $idx = array_push($cartList, $temp);
                if(!$idx) {
                    throw new Exception('장바구니 리스트 생성에 실패했습니다.');
                }

                $i++;  // 장바구니 담긴 상품 
            }
        
            if (count($cartList) == 0) {
                throw new Exception('장바구니에 담긴 상품이 없습니다.');
            }

            // 배송비 검사
            $send_cost = 0;
            $com_send_cost = 0;
            $sep_send_cost = 0;
            $max_send_cost = 0;
            
            // 최종배송비
            $tot_send_cost = 0;
            
            if ($i > 0) {
                $k = 0;
                $condition = array();
                
                foreach($item_sendcost as $key) {
                    list($userid, $bundle, $price) = explode('|', $key);
                    $condition[$userid][$bundle][$k] = ($price!="") ? $price : 0;
                    $k++;
                }

                $com_array = array();
                $val_array = array();

                foreach($condition as $key=>$value) {
                    if(isset($condition[$key]['묶음'])) {
                        $com_send_cost += array_sum($condition[$key]['묶음']); // 묶음배송 합산
                        $max_send_cost += max($condition[$key]['묶음']); // 가장 큰 배송비 합산
                        $com_array[] = max(array_keys($condition[$key]['묶음'])); // max key
                        $val_array[] = max(array_values($condition[$key]['묶음']));// max value
                    }
                    if(isset($condition[$key]['개별'])) {
                        $sep_send_cost += array_sum($condition[$key]['개별']); // 묶음배송불가 합산
                        $com_array[] = array_keys($condition[$key]['개별']); // 모든 배열 key
                        $val_array[] = array_values($condition[$key]['개별']); // 모든 배열 value
                    }
                }

                $send_cost = $com_send_cost + $sep_send_cost; // 총 배송비합계
                $tot_send_cost = $max_send_cost + $sep_send_cost; // 최종배송비
                $tot_final_sum = $send_cost - $tot_send_cost; // 배송비할인
            }
            
            $totalSum = $goodsSum + $tot_send_cost;

            $result->status      = 200;
            $result->message     = "success";
            $result->cartList    = $cartList;
            $result->price       = $goodsSum;
            $result->delivery_total = $send_cost;
            $result->discount    = $tot_final_sum;
            $result->delivery    = $tot_send_cost;
            $result->total_price = $totalSum;

        } catch(Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 장바구니 삭제
    public function cartDelete() {
        # 장바구니 삭제
        $result = new StdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $ct_idx = $this->input->get('ct_idx');
            if (!$ct_idx) {
                throw new Exception('상품 정보가 넘어오지 않았습니다.');
            }

            $ct_idx_arr = explode(",", $ct_idx);
            $cart_data = $this->api_model->getCartIdx($mb_id, $ct_idx_arr);
            if (!$cart_data || (count($cart_data) != count($ct_idx_arr))) {
                throw new Exception('장바구니에 등록된 상품이 없습니다.'); 
            }

            foreach($ct_idx_arr as $row) {
                $param = array(
                    'mb_id' => $mb_id,
                    'index_no' => $row
                );
                $this->query_model->delete("shop_cart", $param);
            }

            $result->status  = 200;
            $result->message = "success";

        } catch(Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 장바구니 옵션변경 클릭
    public function cartOption() {
        # 장바구니 옵션변경 클릭
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $ct_idx = $this->input->get('ct_idx');
            $cart_data = $this->api_model->isCartEmpty($mb_id, $ct_idx);
            if(!$cart_data) {
                throw new Exception('장바구니 정보가 존재하지 않습니다.');
            }

            $gs_id = $cart_data['gs_id'];
            $goods_info = $this->global_lib->get_goods($gs_id);
            
            // 필수 옵션
            $option_item = $this->funcomplex_lib->get_item_options_api($gs_id, $goods_info['opt_subject']);

            // 추가 옵션
            $supply_item = $this->funcomplex_lib->get_item_supply_api($gs_id, $goods_info['spl_subject']);

            if(!$cart_data['io_type'])
                $io_price = ($cart_data['io_price'] + $goods_info['goods_price']) * $cart_data['ct_qty'];
            else
                $io_price = $cart_data['io_price'];

            $optionData = array(
                "option"        => $goods_info['opt_subject'],
                "option_data"   => $option_item,
                "spl_option"    => $goods_info['spl_subject'],
                "spl_option_data" => $supply_item
            );

            $result->status = 200;
            $result->message = "success";
            $result->goods_price  = $goods_info['goods_price'];
            $result->option_name  = $cart_data['ct_option'];
            $result->option_qty   = $cart_data['ct_qty'];
            $result->option_price = $io_price;
            $result->option_type  = $cart_data['io_type'];
            $result->optionData   = $optionData;
            
        } catch(Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);

    }

    // 장바구니 수량 업데이트
    public function cartQtyUpdate() {
        # 장바구니 수량 업데이트
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $ct_idx = $this->input->get('ct_idx');
            $cart_data = $this->api_model->getCartQtyData($mb_id,$ct_idx);
            if(!$cart_data) {
                throw new Exception('장바구니 정보가 존재하지 않습니다.');
            }

            $ct_qty = $this->input->get('ct_qty');
            if(!$ct_qty) {
                throw new Exception('수량이 넘어오지 않았습니다.');
            }

            $update_result = $this->api_model->updateCartQty($ct_qty,$mb_id,$ct_idx);
            if($update_result) {
                $result->status = 200;
                $result->message = "success";
            } else {
                throw new Exception('수량 업데이트 중 문제가 발생하였습니다.');
            }

        } catch(Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 장바구니 옵션 업데이트
    public function cartOptionUpdate() {
        # 장바구니 옵션 업데이트
        $result = new StdClass();

        try {
            $mb_id = $this->input->post('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            // 상품ID
            $gs_id = $this->input->post('gs_id');
            if(!$gs_id) {
                throw new Exception('상품 정보가 넘어오지 않았습니다.');
            }
            $gs_id_arr = explode('|', $gs_id);

            // 수량 초기화
            $ct_qty = $this->input->post('ct_qty');
            if(!$ct_qty) {
                throw new Exception('수량 정보가 넘어오지 않았습니다.');
            }
            $ct_qty_arr = explode('|', $ct_qty);

            // 사용자가 선택한 옵션 받아오기
            $option_type = $this->input->post('option_type'); // 0: 필수 / 1: 선택
            if($option_type == "") {
                throw new Exception('옵션 타입 정보가 넘어오지 않았습니다.');
            }
            $option_type_arr = explode('|', $option_type);
            
            $option = $this->input->post('option'); // io_id (옵션항목)
            if(!$option) {
                throw new Exception('옵션 항목 정보가 넘어오지 않았습니다.');
            }
            $option_arr = explode('|', $option);
            
            $option_value = $this->input->post('option_value'); // ct_option (옵션명)
            if(!$option_value) {
                throw new Exception('옵션명 정보가 넘어오지 않았습니다.');
            }
            $option_value_arr = explode('|', $option_value);

            $cart_idx_arr = array();
            $msg_arr = array();

            foreach($gs_id_arr as $key => $row) {
                $goods_info = $this->global_lib->get_goods($row);
                if(!$goods_info) {
                    throw new Exception('상품 정보가 존재하지 않습니다.');
                }

                if($goods_info['type'] == "timesale") {
                    $sql = "SELECT * FROM shop_order WHERE mb_id = ? AND gs_id = ? AND (dan IN ('2','3','4','5'))";
                    $data = array($mb_id, $gs_id);
                    $is_alreay = $this->query_model->returnOneArr($sql,$data);
                    if(!empty($is_alreay)) {
                        throw new Exception('이미 구매한 상품입니다. 함께장터 상품은 1개만 구매 가능합니다.');
                    }
                }

                $this->load->vars(array('member'=>$member_data));

                if($goods_info['type'] == "timesale") {
                    $goods_info['goods_price'] = $this->gvcomplex_lib->get_timesale_goods_price($gs_id);                
                } else {
                    $goods_info['goods_price'] = $this->gvcomplex_lib->get_sale_price($gs_id);                
                }

                if (!isset($ct_qty_arr[$key]) || empty($ct_qty_arr[$key])) {
                    $ct_qty_arr[$key] = 1;
                }

                if(!isset($option_arr[$key])) {
                    throw new Exception('상품의 옵션을 선택해주세요.');
                }

                // 옵션정보를 얻어서 배열에 저장
                $opt_list = array();
                $sql = "SELECT * FROM shop_goods_option WHERE gs_id = ? ORDER BY io_no ASC ";
                $data = array($row);
                $option_data = $this->query_model->returnArr($sql,$data);
                $lst_count = 0;
                
                foreach($option_data as $key2 => $row2){
                    $opt_list[$row2['io_type']][$row2['io_id']]['id'] = $row2['io_id'];
                    $opt_list[$row2['io_type']][$row2['io_id']]['use'] = $row2['io_use'];
                    $opt_list[$row2['io_type']][$row2['io_id']]['price'] = $row2['io_price'];
                    $opt_list[$row2['io_type']][$row2['io_id']]['stock'] = $row2['io_stock_qty'];
        
                    // 주문옵션 개수
                    if(!$row2['io_type']) {
                        $lst_count++;
                    }
                }

                if($opt_list && !$option_arr[$key] && !$option_value_arr[$key]) {
                    throw new Exception('상품의 옵션을 선택해주세요.');
                }

                $io_price = isset($opt_list[$option_type_arr[$key]][$option_arr[$key]]['price']) ? $opt_list[$option_type_arr[$key]][$option_arr[$key]]['price'] : 0;

                // 재고 구허가
                if(!$option_arr[$key]) {
                    $it_stock_qty = $this->global_lib->get_it_stock_qty($row);
                } else {
                    $it_stock_qty = $this->global_lib->get_option_stock_qty($row, $option_arr[$key], $option_type_arr[$key]);
                }

                if($ct_qty_arr[$key] > $it_stock_qty) {
                    throw new Exception($option_arr[$key]." 의 재고수량이 부족합니다.\\n\\n현재 재고수량 : " . number_format($it_stock_qty) . " 개");
                }

                $insert_id = "";

                // 장바구니 존재 여부
                $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());
                $od_no = $this->global_lib->cart_uniqid();

                $resObj = $this->api_model->getCartData($member_data['id'],$goods_info['index_no'],$option_arr[$key]);            
                
                // 동일한 상품이 장바구니에 존재할 경우
                if ($resObj->num_rows() >= 1) { 
                    // $io_type = 1; 
                    $sameObj = $resObj->row_array();

                    $total_qty = 0; // 동일한 상품의 수량을 합친다.

                    if($sameObj['gs_id'] == $row) {
                        $total_qty = $ct_qty_arr[$key];
                    }

                    $index_no = $sameObj['index_no'];
                    $this->api_model->updateCart($total_qty, $member_data['id'], $row,$index_no);

                    $cart_idx_arr[$key] = $index_no;
                    $msg_arr[$key] = "업데이트";

                // 동일한 상품 존재하지 않음
                } else {

                    // 포인트 계산
                    $point = 0;
                    $grade = $member_data['grade'];

                    $sql = "SELECT gb_buy_point FROM shop_member_grade WHERE gb_no = ?";
                    $param = array($grade);
                    $res = $this->query_model->returnOneArr($sql,$param);

                    if($goods_info['is_point'] > 0) {
                        $gb_point = $res['gb_buy_point']; // 적립률
                        $point = (int) round((($goods_info['goods_price'] + $io_price) * $ct_qty) * ($gb_point * (1/100))); // 적립 포인트
                    }

                    // 동일한 상품이 아닐 경우
                    $cartAdd = array(
                        "ca_id"     => $goods_info['ca_id'],
                        'od_id'     => "",
                        'mb_id'     => $member_data['id'],
                        'gs_id'     => $goods_info['index_no'],
                        'ct_time'   => $OM_TIME_YMDHIS,
                        'ct_price'  => $goods_info['goods_price'],
                        "ct_supply_price" => $goods_info['supply_price'],
                        "ct_qty"    => $ct_qty_arr[$key],
                        "ct_point"  => $point,
                        "io_id"     => $option_arr[$key],
                        "io_type"   => $option_type_arr[$key],
                        "io_price"  => isset($opt_list[$option_type_arr[$key]][$option_arr[$key]]['price']) ? $opt_list[$option_type_arr[$key]][$option_arr[$key]]['price'] : 0,
                        "ct_option" => $option_value_arr[$key],
                        "ct_send_cost" => 0,
                        "od_no"     => $od_no,
                        "ct_ip"     => $_SERVER['REMOTE_ADDR'],
                        "is_direct" => "n"
                    );

                    $insert_id = $this->query_model->insertObj('shop_cart', $cartAdd);
                    if(!$insert_id) {
                        throw new Exception('장바구니 저장 중 오류가 발생했습니다.');
                    }

                    $cart_idx_arr[$key] = $insert_id;
                    $msg_arr[$key] = "추가";
                }
            }

            $result->status = 200;
            $result->message = "success";
            $result->cart_idx = $cart_idx_arr;
            $result->msg = $msg_arr;

        } catch(Exception $e){
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 주문하기
    public function order() {
        # 주문하기
        $result = new StdClass();

        try {
            $default = $this->load->get_var('default');
            $config = $this->load->get_var('config');
            
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            // 바로구매인지 장바구닉 구매인지 확인
            $is_direct = $this->input->get('is_direct');
            if(!$is_direct) {
                throw new Exception('구매 유형 정보가 없습니다.');
            }

            // 장바구니 번호 넘어옴
            $ct_idx = $this->input->get('ct_idx');

            // 장바구니 조회
            $ct_idx_arr = explode(",",$ct_idx);
            $cart_data = $this->api_model->getCartOrderData($ct_idx_arr, $is_direct);
            if(!$cart_data || (count($ct_idx_arr) != count($cart_data))) {
                throw new Exception('장바구니 정보가 없습니다.');
            }

            // 장바구니 총 상품 금액 합계
            $totalOrderPrice = $this->api_model->getOrderTotalPrice($ct_idx_arr);

            $buySql = "";
            $totalSum = 0;
            $goodsSum = 0;
            $goodsData = array();
            $i = 0;
            
            // 상품 주문 정보
            $allowed_types = [];
            foreach($cart_data as $key => $row) {
                $goods_info = $this->global_lib->get_goods($row['gs_id']);

                $this->load->vars(array('gs'=>$goods_info));

                // 타입을 배열에 추가
                if (!in_array($goods_info['type'], $allowed_types)) {
                    $allowed_types[] = $goods_info['type'];
                }
        
                // 타입이 섞이면 중단
                if (count($allowed_types) > 1) {
                    throw new Exception("함께장터 상품은 일반 상품과 함께 구입이 불가능합니다.");
                }

                // 합계금액 계산
                $sql = "SELECT  SUM(IF(io_type = 1, (io_price * ct_qty), ((io_price + ct_price) * ct_qty))) as price,
                                SUM(IF(io_type = 1, (io_price * ct_qty), ((io_price + ct_supply_price) * ct_qty))) as supply_price,
                                SUM(IF(io_type = 1, (0),(ct_point * ct_qty))) as point,
                                SUM(IF(io_type = 1, (0),(ct_qty))) as qty,
                                SUM(io_price * ct_qty) as opt_price
                        FROM shop_cart
                        WHERE index_no IN ({$row['index_no']})
                        AND ct_select = '0'
                        AND is_direct='{$is_direct}'";
                $sql .= $buySql;
                $sum = $this->query_model->returnOneArr($sql);

                $goodsSum = $goodsSum + $sum['price'];

                $sell_price = $sum['price'];

                // 배송비 구함
                $info = $this->funcomplex_lib->get_item_sendcost($row,$sell_price,$totalOrderPrice);
                if (!$info) {
                    throw new Exception('배송비 정보 호출 실패');
                }

                $item_sendcost[] = $info['pattern'];
                $info['price'] = isset($info['price']) ? $info['price'] : 0;

                $array = array(
                    'goods_name'    => $goods_info['gname'],
                    'option_id'     => $row['io_id'],
                    'option_value'  => $row['ct_option'],
                    'qty'           => (int) $sum['qty'],
                    'price'         => (int) $sum['price'],
                    'supply_price'  => (int) $sum['supply_price'],
                    'option_price'  => (int) $sum['opt_price'],
                    'get_it_image'  => $this->funcomplex_lib->get_it_image_url($row['gs_id'], $goods_info['simg1'], 400, 400),
                    'type'          => $goods_info['type']
                );

                array_push($goodsData, $array);

                $i++;  // 장바구니 담긴 상품 
            }

            // 배송비 검사
            $send_cost = 0;
            $com_send_cost = 0;
            $sep_send_cost = 0;
            $max_send_cost = 0;
            
            // 최종배송비
            $tot_send_cost = 0;
            
            if ($i > 0 ) {
                $k = 0;
                $condition = array();
                
                foreach($item_sendcost as $key) {
                    list($userid, $bundle, $price) = explode('|', $key);
                    $condition[$userid][$bundle][$k] = ($price != "") ? $price : 0;
                    $k++;
                }

                $com_array = array();
                $val_array = array();

                foreach($condition as $key=>$value) {
                    if(isset($condition[$key]['묶음'])) {
                        $com_send_cost += array_sum($condition[$key]['묶음']); // 묶음배송 합산
                        $max_send_cost += max($condition[$key]['묶음']); // 가장 큰 배송비 합산
                        $com_array[] = max(array_keys($condition[$key]['묶음'])); // max key
                        $val_array[] = max(array_values($condition[$key]['묶음']));// max value
                    }
                    if(isset($condition[$key]['개별'])) {
                        $sep_send_cost += array_sum($condition[$key]['개별']); // 묶음배송불가 합산
                        $com_array[] = array_keys($condition[$key]['개별']); // 모든 배열 key
                        $val_array[] = array_values($condition[$key]['개별']); // 모든 배열 value
                    }
                }

                $send_cost = $com_send_cost + $sep_send_cost; // 총 배송비합계
                $tot_send_cost = $max_send_cost + $sep_send_cost; // 최종배송비
                $tot_final_sum = $send_cost - $tot_send_cost; // 배송비할인
            }

            // 쿠폰 정보
            $useCoupon = "";
            if($config['coupon_yes']) { // 보유쿠폰
                $cp_count = $this->global_lib->get_cp_precompose($member_data['id']);
                $useCoupon = "사용 가능 쿠폰 {$cp_count[3]}장";
            }

            // 포인트 정보
            $usePoint = "";
            $usePointTxt = "";
            if($config['usepoint_yes']) {
                $usePoint = (int) $member_data['point'];
                if($config['usepoint'] >= 0) {
                    $usePointTxt = "(".display_point($config['usepoint'])." 부터 사용가능)";
                }
            }

            // 예치금 정보
            $useMileage = "";
            $useMileageTxt =  "";
            if($config['usemileage_yes']) {
                $useMileage = (int) $member_data['mileage'];
                if($config['usepoint'] >= 0) {
                    $useMileageTxt = "(".display_price($config['usemileage'])." 부터 사용가능)";
                }
            }

            // 총 합계
            $totalSum = $goodsSum + $tot_send_cost;

            // 계좌정보
            $bankData = "";
            $bankInfo = $this->funcomplex_lib->get_order_bank_account($default); 
            if(count($bankInfo) > 0) {
                for($j = 0; $j < count($bankInfo); $j++) {   
                    $bankData = $bankInfo[$j]['name'] . " " . $bankInfo[$j]['account'] . " " . $bankInfo[$j]['holde'];
                }
            }

            // 구매자 정보
            $buyerData = array(
                'name'  => $member_data['name'],
                'phone' => $member_data['cellphone'],
                'zip'   => $member_data['zip'],
                'addr1' => $member_data['addr1'],
                'addr2' => $member_data['addr2'],
                'addr3' => $member_data['addr3'],
            );

            $merchant_uid = 'hallyu'.round(microtime(true) * 1000);

            $result->status         = 200;
            $result->message        = "success";
            $result->ct_idx         = $ct_idx;
            $result->goodsData      = $goodsData;
            $result->useCoupon      = $useCoupon;
            $result->usePoint       = $usePoint;
            $result->usePointTxt    = $usePointTxt;
            $result->useMileage     = $useMileage;
            $result->useMileageTxt  = $useMileageTxt;
            $result->bankData       = $bankData;
            $result->buyerData      = $buyerData;
            $result->price          = $goodsSum;
            $result->delivery_total = $send_cost;
            $result->discount       = $tot_final_sum;
            $result->delivery       = $tot_send_cost;
            $result->total_price    = $totalSum;
            $result->merchant_uid   = $merchant_uid;

        } catch (Exception $e){
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 쿠폰 - 상품리스트
    public function couponGoodsList() {
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $ca_id = $this->input->get('ca_id');
            if(!$ca_id) {
                throw new Exception('장바구니 번호가 넘어오지 않았습니다.');
            }

            $sql2 = "SELECT * FROM shop_cart WHERE index_no IN ({$ca_id}) GROUP BY gs_id ORDER BY index_no";
            $result2Obj = $this->query_model->returnQueryObj($sql2);
            $result2 = $result2Obj->result_array();

            $goodsList = [];

            foreach($result2 as $key => $row){
                $goods_info = $this->global_lib->get_goods($row['gs_id']);
                
                $goodsList[] = array(
                    'ca_id' => $row['index_no'],
                    'gs_id' => $goods_info['index_no'],
                    'gname' => $goods_info['gname'],
                    'qty' => (int) $row['ct_qty'],
                    'price' => (int) $row['ct_price'],
                    'get_it_image' => $this->funcomplex_lib->get_it_image_url($goods_info['index_no'], $goods_info['simg1'], 100, 100),
                );
            }

            $result->status = 200;
            $result->message = "success";
            $result->goodsList = $goodsList;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 쿠폰 - 상품 적용가능 쿠폰 리스트
    public function couponList() {
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $ca_id = $this->input->get('ca_id');
            if(!$ca_id) {
                throw new Exception('장바구니 번호가 넘어오지 않았습니다.');
            }

            $query = $this->global_lib->get_cp_precompose($member_data['id']);
            $sql_common  = $query[0];
            $sql_search  = $query[1];
            $sql_order	 = $query[2];
            $total_count = $query[3];

            $sql = "SELECT * $sql_common $sql_search $sql_order";
            $res = $this->query_model->returnArr($sql);

            $sql2 = "SELECT * FROM shop_cart WHERE index_no IN ({$ca_id}) GROUP BY gs_id ORDER BY index_no";
            $result2 = $this->query_model->returnOneArr($sql2);

            // 상품 정보 및 합계금액 계산
            $cp_tmp = [];
            $goods_info = $this->global_lib->get_goods($result2['gs_id']);
            
            // 합계금액 계산 쿼리
            $sql = "SELECT SUM(IF(io_type = 1, (io_price * ct_qty),((io_price + ct_price) * ct_qty))) as price,
                            SUM(IF(io_type = 1, 0, (ct_point * ct_qty))) as point,
                            SUM(ct_qty) as qty,
                            SUM(io_price * ct_qty) as opt_price
                    FROM shop_cart
                    WHERE index_no = ? AND mb_id = ? AND ct_select = 0";
            $data = [$ca_id, $member_data['id']];
            $sum = $this->query_model->returnOneArr($sql, $data);
        
            // 상품별 가격 및 카테고리 정보
            $price = $sum['price'];
            $ca_list = $this->funcomplex_lib->get_extract($result2['gs_id']);
            $cp_tmp[] = "{$price}|{$result2['gs_id']}|{$ca_list}|{$goods_info['use_aff']}";

            $couponList = [];
            foreach ($res as $row) {
                $lo_id = $row['lo_id'];
                $str = $this->funcomplex_lib->get_cp_contents($row);
            
                foreach ($cp_tmp as $cp_data) {
                    $is_gubun = explode("|", $cp_data);
                    $is_coupon = false;
            
                    // 쿠폰 사용 조건 체크
                    switch ($row['cp_use_part']) {
                        case '0':
                            $is_coupon = true;
                            break;
                        case '1':
                            if ($row['cp_use_goods'] && strpos($is_gubun[1], $row['cp_use_goods']) !== false) {
                                $is_coupon = true;
                            }
                            break;
                        case '2':
                            if ($row['cp_use_category'] && strpos($is_gubun[2], $row['cp_use_category']) !== false) {
                                $is_coupon = true;
                            }
                            break;
                        case '3':
                            if ($row['cp_use_goods'] && strpos($is_gubun[1], $row['cp_use_goods']) === false) {
                                $is_coupon = true;
                            }
                            break;
                        case '4':
                            if ($row['cp_use_category'] && strpos($is_gubun[2], $row['cp_use_category']) === false) {
                                $is_coupon = true;
                            }
                            break;
                    }
                    
                    // 쿠폰 적용여부 && 가맹점상품제외 && 최대금액 <= 상품금액
                    if ($is_coupon && !$is_gubun[3] && $row['cp_low_amt'] <= (int) $is_gubun[0]) {
                        // 할인 금액 계산
                        $amt = $this->funcomplex_lib->get_cp_sale_amt($row, (int) $is_gubun[0]);
                        $goods_info = $this->global_lib->get_goods($is_gubun[1]);
            
                        // 결과 배열에 쿠폰 정보 추가
                        $couponList[] = [
                            'gs_id' => $is_gubun[1],
                            'get_it_image' => $this->funcomplex_lib->get_it_image_url($is_gubun[1], $goods_info['simg1'], 100, 100),
                            'gname' => $goods_info['gname'],
                            'cp_id' => $lo_id,
                            'cp_no' => $row['cp_id'],
                            'is_dups' => $row['cp_dups'],
                            'discount_percent' => $amt[1],
                            'discount' => round($amt[0]),
                            'name' => $row['cp_subject']
                        ];
                    }
                }
            }

            $result->status = 200;
            $result->message = "success";
            $result->couponList = $couponList;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 주문 진행
    public function orderAction() {
        # 주문 진행
        $result = new StdClass();

        try {
            $default = $this->load->get_var('default');

            $mb_id = $this->input->post('mb_id'); // 회원아이디
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            // 주문번호
            $ct_idx = $this->input->post('ct_idx');
            if(!$ct_idx) {
                throw new Exception('장바구니 idx가 넘어오지 않았습니다.');
            }
            
            $ct_idx_arr = explode(",", $ct_idx);
            $cart_data = $this->api_model->getCartOrderData($ct_idx_arr);

            if(!$cart_data || count($cart_data) != count($ct_idx_arr)) {
                throw new Exception('구매할 상품이 없습니다');
            }

            // 장바구니 총 상품 금액 합계
            $totalOrderPrice = $this->api_model->getOrderTotalPrice($ct_idx_arr);
            
            // 이름
            $name = $this->input->post('name');
            if(!$name) {
                throw new Exception('주문자 이름을 입력해주세요.');
            }
            
            // 연락처
            $phone = $this->input->post('phone');
            if(!$phone) {
                throw new Exception('주문자 연락처를 입력해주세요.');
            }

            // 주소
            $zip = $this->input->post('zip');
            $addr1 = $this->input->post('addr1');
            $addr2 = $this->input->post('addr2');
            $addr3 = $this->input->post('addr3') ?? "";
            if(!$zip || !$addr1 || !$addr2) {
                throw new Exception('배송지 정보를 입력해주세요.');
            }

            // 배송 요청사항
            $delivery_memo = $this->input->post('delivery_memo');
            if(!$delivery_memo) {
                throw new Exception('배송요청사항을 입력해주세요.');
            }

            // 지불 방법
            $paymethod = $this->input->post('paymethod');
            if(!$paymethod) {
                throw new Exception('결제 방법을 선택해주세요.');
            }

            // 무통장 입금 정보
            $bank = "";
            $deposit_name = "";
            $deposit_phone = "";
            $taxbill_yes = "N";
            $taxsave_yes = "N";
            $tax_hp = "";
            $tax_saupja_no = "";
            $company_saupja_no = "";
            $company_name = "";
            $company_owner = "";
            $company_addr = "";
            $company_item = "";
            $company_service = "";

            // 무통장입금 일때만
            if($paymethod == "무통장") {
                // 은행 정보
                $bank = $this->input->post('bank');
                if(!$bank) {
                    throw new Exception('은행정보가 없습니다.');
                }

                // 입금자
                $deposit_name = $this->input->post('deposit_name');
                if(!$deposit_name) {
                    throw new Exception('입금자를 입력해주세요.');
                }
                
                // 연락처
                $deposit_tel = $this->input->post('deposit_tel');
                if(!$deposit_tel) {
                    throw new Exception('입금자 연락처를 입력해주세요.');
                }

                // 현금영수증 발행
                $taxsave_yes = $this->input->post('taxsave_yes');
                if(!$taxsave_yes) {
                    throw new Exception('현금영수증 발행 여부를 선택해주세요.');
                }

                if($taxsave_yes == "Y") {
                    $tax_hp = $this->input->post('tax_hp');
                    if(!$tax_hp) {
                        throw new Exception('현금영수증 발행 연락처를 입력해주세요.');
                    }
                } else if($taxsave_yes == "S") {
                    $tax_saupja_no = $this->input->post('tax_saupja_no');
                    if(!$tax_saupja_no) {
                        throw new Exception('현금영수증 발행 사업자등록번호를 입력해주세요.');
                    }
                }

                // 세금계산서 발행
                $taxbill_yes = $this->input->post('taxbill_yes');
                if(!$taxbill_yes) {
                    throw new Exception('세금계산서 발행 여부를 선택해주세요.');
                }

                if($taxbill_yes == "Y") {
                    $company_saupja_no = $this->input->post('company_saupja_no');
                    if(!$company_saupja_no) {
                        throw new Exception('세금계산서 발행 사업자등록번호를 입력해주세요.');
                    }

                    $company_name = $this->input->post('company_name');
                    if(!$company_name) {
                        throw new Exception('세금계산서 발행 상호명을 입력해주세요.');
                    }

                    $company_owner = $this->input->post('company_owner');
                    if(!$company_owner) {
                        throw new Exception('세금계산서 발행 대표자를 입력해주세요.');
                    }

                    $company_addr = $this->input->post('company_addr');
                    if(!$company_addr) {
                        throw new Exception('세금계산서 발행 사업장 주소를 입력해주세요.');
                    }

                    $company_item = $this->input->post('company_item');
                    if(!$company_item) {
                        throw new Exception('세금계산서 발행 업태를 입력해주세요.');
                    }

                    $company_service = $this->input->post('company_service');
                    if(!$company_service) {
                        throw new Exception('세금계산서 발행 종목을 입력해주세요.');
                    }
                }
            }

            // 포트원 신용카드 & 계좌이체 결제
            $imp_uid = "";
            $merchant_uid = "";

            if($paymethod != "무통장") {
                $imp_uid = $this->input->post('imp_uid');
                if(!$imp_uid) {
                    throw new Exception('포트원 결제정보가 없습니다.');
                }

                $merchant_uid = $this->input->post('merchant_uid');
                if(!$merchant_uid) {
                    throw new Exception('포트원 결제정보가 없습니다.');
                }

                $imp_paid_result = $this->imp_get_payment_data($imp_uid);
                $imp_paid_result = json_decode($imp_paid_result, true);
                if(empty($imp_paid_result)) {
                    throw new Exception("결제 내역이 존재하지 않습니다.");
                }

                $imp_paid_result = $imp_paid_result['response'];

                // 결제 실패
                if(isset($imp_paid_result['fail_reason'])) {
                    throw new Exception($imp_paid_result['fail_reason']);
                }
            
                $amount = $imp_paid_result['amount'];
                $bank = $imp_paid_result['bank_name'];
                $bank = empty($bank) ? "" : $bank;
                $pay_method = $imp_paid_result['pay_method'];

                if($pay_method == 'card') {
                    $paymethod = "신용카드";
                } else if($pay_method == 'trans') {
                    $paymethod = "계좌이체";
                }

                $pg_price = $amount;
            }

            $tot_sell_price = 0;
            $tot_opt_price  = 0;
            $tot_sell_qty   = 0;
            $tot_sell_amt   = 0;

            $i = 0;   // 상품의 수
            $sell_good = array();
            $totalSum = 0;
            $goodsSum = 0;

            $buySql = "";
            foreach($cart_data as $key => $row) {

                $goods_info = $this->global_lib->get_goods($row['gs_id']);
                $this->load->vars(array('gs'=>$goods_info));

                $totalSql = "SELECT SUM(IF(io_type = 1, (io_price * ct_qty), ((io_price + ct_price) * ct_qty ))) AS price,
                                    SUM(IF(io_type = 1, (io_price * ct_qty), ((io_price + ct_price) * ct_qty ))) AS use_price, 
                                    SUM(IF(io_type = 1, (0), (ct_point * ct_qty))) as point,
                                    SUM(IF(io_type = 1, (0), (ct_qty))) as qty,
                                    SUM(io_price * ct_qty) as opt_price
                            FROM    shop_cart 
                            WHERE   index_no = ?
                            AND     mb_id = ?
                            AND     ct_select = '0'";

                $totalSql .= $buySql;
                $param = array($row['index_no'], $member_data['id']);
                $sum = $this->query_model->returnOneArr($totalSql,$param);
                
                $use_price      = $sum['use_price'];
                $sell_price     = $sum['price'];
                $sell_opt_price = $sum['opt_price'];
                $sell_qty       = $sum['qty'];
                $sell_amt       = $sum['price'] - $sum['opt_price'];

                $goodsSum = $goodsSum + $sum['price'];

                $info = $this->funcomplex_lib->get_item_sendcost($row,$sell_price,$totalOrderPrice);
                if (!$info) {
                    throw new Exception('배송비 정보 호출 실패');
                }
                
                $item_sendcost[] = $info['pattern'];
                $info['price'] = isset($info['price']) ? $info['price'] : 0;

                // 상품 초기화
                $notax = (int)$goods_info['notax'];
                $goods_price = (int)$goods_info['goods_price'];

                $tot_sell_price += (int)$sell_price;
                $tot_opt_price += (int)$sell_opt_price;
                $tot_sell_qty += (int)$sell_qty;
                $tot_sell_amt += (int)$sell_amt;

                $sell_good[$i]['cart_id']         = $row['index_no'];
                $sell_good[$i]['od_no']           = $row['od_no'];
                $sell_good[$i]['gs_id']           = $row['gs_id'];
                $sell_good[$i]['gs_notax']        = $notax;
                $sell_good[$i]['goods_price']     = $goods_price;
                $sell_good[$i]['goods_tot']       = $sell_price;
                $sell_good[$i]['seller_id']       = $member_data['pt_id'];
                $sell_good[$i]['seller_real_id']  = $member_data['pt_id'];
                $sell_good[$i]['use_price']       = $use_price;
                $sell_good[$i]['sum_qty']         = $sell_qty;
                $sell_good[$i]['get_it_image']    = $this->funcomplex_lib->get_it_image_url($row['gs_id'], $goods_info['simg1'], 400, 400);

                $i++;
            }

            // 배송비 요금 정산
            $com_send_cost = 0;
            $sep_send_cost = 0;
            $max_send_cost = 0;

            if($i > 0 ) {
                $k = 0;
                $condition = array();
                
                foreach($item_sendcost as $key) {
                    list($userid, $bundle, $price) = explode('|', $key);
                    $price=($price=="")?0:$price;
                    $condition[$userid][$bundle][$k] = ($price != "") ? $price : 0;
                    $k++;
                }

                $com_array = array();
                $val_array = array();
                foreach($condition as $key=>$value) {
                    if(isset($condition[$key]['묶음'])) {
                        $com_send_cost += array_sum($condition[$key]['묶음']); // 묶음배송 합산
                        $max_send_cost += max($condition[$key]['묶음']); // 가장 큰 배송비 합산
                        $com_array[] = max(array_keys($condition[$key]['묶음'])); // max key
                        $val_array[] = max(array_values($condition[$key]['묶음']));// max value
                    }
                    if(isset($condition[$key]['개별'])) {
                        $sep_send_cost += array_sum($condition[$key]['개별']); // 묶음배송불가 합산
                        $com_array[] = array_keys($condition[$key]['개별']); // 모든 배열 key
                        $val_array[] = array_values($condition[$key]['개별']); // 모든 배열 value
                    }
                }

                $baesong_price_txt = $this->gvcomplex_lib->get_tune_sendcost($com_array, $val_array, $item_sendcost);
            
                $tot_send_cost = $max_send_cost + $sep_send_cost; // 최종배송비
            }

            $totalSum = $goodsSum + $tot_send_cost;

            // 신용카드 & 계좌이체 결제 결제금액 일치하는지 확인
            if($paymethod != "무통장" && ((int) $totalSum !== (int) $pg_price)) {
                $cancel_msg = '결제금액 불일치';

                include_once('./Bill/iamport.php');
                $result = $iamport->cancel(array(
                    'imp_uid'		=> $imp_uid, 		//merchant_uid에 우선한다
                    'merchant_uid'	=> $merchant_uid, 	//imp_uid 또는 merchant_uid가 지정되어야 함
                    'reason'		=> $cancel_msg,				//취소사유
                ));

                throw new Exception('결제금액이 불일치하여 주문이 취소되었습니다.');
            }

            // 구매자 정보
            $buyer = array();
            $buyer['name']  = isset($member_data['name']) ? $member_data['name'] : "";
            $buyer['phone'] = isset($member_data['cellphone']) ? $member_data['cellphone'] : "";
            $buyer['zip']   = isset($member_data['zip']) ? $member_data['zip'] : "";
            $buyer['addr1'] = isset($member_data['addr1']) ? $member_data['addr1'] : "";
            $buyer['addr2'] = isset($member_data['addr1']) ? $member_data['addr2'] : "";
            $buyer['addr3'] = isset($member_data['addr1']) ? $member_data['addr3'] : "";
            $buyer['addr_jibeon'] = isset($member_data['addr_jibeon']) ? $member_data['addr_jibeon'] : "";
            $buyer['email'] = isset($member_data['email']) ? $member_data['email'] : "";

            // 바로구매일 경우 => 주문코드를 동일하게 사용
            $od_id = "";
            if(!$od_id) {
                $od_id = $this->global_lib->get_uniqid();
            } else {
                $od_id = $direct_od_id; 
            }
                        
            // 배송비 값
            $baesong_price = explode("|" , $baesong_price_txt);
                        
            // 주문서 등록 날짜
            $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());

            $cnt = count($sell_good);
            for($i=0; $i < $cnt; $i++) {
                $dan = ($paymethod == "무통장") ? 1 : 2;

                // 주문서 등록
                $creditData = array(
                    'od_id'     => $od_id,
                    'od_no'     => $sell_good[$i]['od_no'],
                    'mb_id'     => $mb_id,
                    'name'      => $buyer['name'],
                    'cellphone' => $buyer['phone'],
                    'email'     => $buyer['email'],
                    'zip'       => $buyer['zip'],
                    'addr1'     => $buyer['addr1'],
                    'addr2'     => $buyer['addr2'],
                    'addr3'     => $buyer['addr3'],
                    'addr_jibeon' => "R",

                    // 결제자정보
                    'b_name'    => $name,
                    'b_cellphone' => $phone,
                    
                    // 배송지 정보
                    'b_zip'     => $zip,
                    'b_addr1'   => $addr1,
                    'b_addr2'   => $addr2,
                    'b_addr3'   => $addr3,
                    'b_addr_jibeon' => "R",

                    // 상품정보
                    'gs_id'          => $sell_good[$i]['gs_id'],
                    'gs_notax'       => $sell_good[$i]['gs_notax'],
                    'goods_price'    => $sell_good[$i]['goods_tot'],
                    'supply_price'   => 0,
                    'use_price'      => $sell_good[$i]['use_price']+$baesong_price[$i],
                    'sum_qty'        => $sell_good[$i]['sum_qty'],
                    'use_point'      => 0,
                    'baesong_price'  => $baesong_price[$i],
                    'baesong_price2' => 0,
                    'paymethod'      => $paymethod,
                    'bank'           => $bank,
                    'deposit_name'   => $deposit_name,
                    'deposit_tel'    => $deposit_tel,
                    'dan'            => $dan,
                    'memo'           => $delivery_memo,
                    'taxsave_yes'    => $taxsave_yes,
                    'tax_hp'         => $tax_hp,
                    'tax_saupja_no'  => $tax_saupja_no,
                    'taxbill_yes'    => $taxbill_yes,
                    'company_saupja_no' => $company_saupja_no,
                    'company_name'   => $company_name,
                    'company_owner'  => $company_owner,
                    'company_addr'   => $company_addr,
                    'company_item'   => $company_item,
                    'company_service' => $company_service,
                    'seller_id'      => "admin",
                    'seller_real_id' => "admin",
                    'seller_name'    => "",
                    'od_time'        => $OM_TIME_YMDHIS,
                    'od_pwd'         => "",
                    'od_ip'          => $_SERVER['REMOTE_ADDR'],
                    'od_test'        => $default['de_card_test'],
                    'od_tax_flag'    => $default['de_tax_flag_use'],
                    'od_settle_pid'  => "",
                    'pt_id'          => "admin",
                    'shop_id'        => "admin",
                    'imp_uid'        => $imp_uid,
                    'merchant_uid'   => $merchant_uid,
                );
                
                $table="shop_order";
                $insert_id = $this->query_model->insertObj($table, $creditData);     
                if(!$insert_id) {
                    throw new Exception('주문서 생성 실패');
                }

                // 고객 주문 / 배송조회 위해 보관(주문 요청을 위해서);
                $this->funcomplex_lib->save_goods_data($sell_good[$i]['gs_id'], $insert_id, $od_id);
            }

            if($insert_id > 0 ) {
                $alarm_idx = "";
                // // 관리자 알림
                // $this->funcomplex_lib->admin_app_push_send($dan, $od_id);

                // // 푸시 알림
                // if($member_data['is_alarm'] == "y") {
                //     $alarm_data = $this->funcomplex_lib->app_push_send($member_data['token'], $member_data['id'], "admin", $dan, $od_id);
                //     if($alarm_data['res']) {
                //         $alarm_idx = $alarm_data['alarmId'];
                            
                //         $table2 = "shop_alarm";
                //         $OM_TIME_YMDHIS = date("Y-m-d H:i:s", time());// 발송날짜
    
                //         $data2 = array("dispatch_date" => $OM_TIME_YMDHIS);
                //         $where2 = array("index_no" => $alarm_idx);
                //         $this->Query_model->updateObj($table2, $data2, $where2);
                //     }
                // }
            }

            // 장바구니 주문완료 처리
            $this->funcomplex_lib->cart_order_submit($od_id, $ct_idx);

            // 포트원 결제로그 등록
            if($paymethod != "무통장") {
                $cardLog = array(
                    'od_id'        => $od_id,
                    'mb_id'        => $mb_id, 
                    'name'         => $imp_paid_result['buyer_name'],
                    'tel'          => $imp_paid_result['buyer_tel'],
                    'addr'         => $imp_paid_result['buyer_addr'],
                    'email'        => $imp_paid_result['buyer_email'],
                    'amount'       => $imp_paid_result['amount'],
                    'paymethod'    => $imp_paid_result['pay_method'],
                    'imp_uid'      => $imp_paid_result['imp_uid'],
                    'merchant_uid' => $imp_paid_result['merchant_uid'],
                    'bank_name'    => isset($imp_paid_result['bank_name']) ? $imp_paid_result['bank_name'] : "",
                    'card_name'    => isset($imp_paid_result['card_name']) ? $imp_paid_result['card_name'] : "",
                    'card_number'  => isset($imp_paid_result['card_number'])? $imp_paid_result['card_number'] : "",        
                    'apply_num'    => isset($imp_paid_result['apply_num'])? $imp_paid_result['apply_num'] : "",
                    'receipt_url'  => isset($imp_paid_result['receipt_url'])? $imp_paid_result['receipt_url'] : "",
                    'cancelled_amount' => isset($imp_paid_result['cancel_amount']) ? $imp_paid_result['cancel_amount'] : "",
                    'cancelled_reason' => isset($imp_paid_result['cancelled_reason']) ? $imp_paid_result['cancelled_reason'] : "",
                    'cancelled_time' =>  isset($imp_paid_result['cancelled_time']) ? date("Y-m-d H:i:s", $imp_paid_result['cancelled_time']) : "",
                    'order_time'   => date("Y-m-d H:i:s", $imp_paid_result['paid_at'])
                );

                $this->query_model->insertObj("shop_order_log",$cardLog);
            }

            // 문자 발송
            $result->status = 200;
            $result->message = "success";
            $result->mb_id = $mb_id;
            $result->od_id = $od_id;

        } catch(Exception $e){
            $result->status = 400;
            $result->msg    = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 결제 완료
    public function orderFinish() {
        $result = new StdClass();

        try {
            // 회원정보
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }
            
            // 주문번호
            $od_id = $this->input->get('od_id');
            if(!$od_id) {
                throw new Exception('주문번호가 넘어오지 않았습니다.');
            }
        
            $orderItem = array();
        
            $delivery  = "";
            $tot       = "";   // 상품 합계 정보
            $tot_price = "";   // 상품 합계 + 배송비 합계 정보
            $orderInfo = ""; 

            // 주문정보
            $order_data = $this->api_model->getPaymentData($mb_id,$od_id);
            if(!$order_data) {
                throw new Exception('주문정보가 없습니다');
            }

            // 주문정보
            foreach($order_data as $row) {   
                if(!$orderInfo) {
                    $delivery_arr = "";

                    $delivery_arr = $this->api_model->getDeliveryValue($od_id);
                    $delivery     = $delivery_arr['bsprice'];   // 배송비 정보 넣기
                    $tot          = $this->api_model->getOrderTotal($od_id);
                    $tot_price    = (int)$tot['goodsprice'] + (int)$delivery;

                    $orderInfo = array(
                        "od_id"     => $row['od_id'],
                        // "tot_price" => $tot_price,
                        "od_date"   => $row['od_time'],
                        "receipt_date" => $row['receipt_time']
                    );
                }                   

                // 주문자 정보
                $orderPerson['mb_id']     = $row['mb_id'];
                $orderPerson['name']      = $row['name'];
                $orderPerson['cellphone'] = $row['cellphone'];

                // 배송지 정보
                $orderDelivery['zip']    = $row['zip'];
                $orderDelivery['addr1']  = $row['addr1'];
                $orderDelivery['addr2']  = $row['addr2'];
                $orderDelivery['addr3']  = $row['addr3'];
                $orderDelivery['memo']   = $row['memo'];

                // 결제정보
                $orderPayMethod['paymethod']     = ($row['paymethod'] == '') ? "정보없음" :  $row['paymethod'];
                $orderPayMethod['deposit_name']  = $row['deposit_name'];
                $orderPayMethod['bank']          = $row['bank'];
                $orderPayMethod['cellphone']     = $row['cellphone'];
                $orderPayMethod['taxbill_yes']   = $row['taxbill_yes'];
                $orderPayMethod['taxsave_yes']   = $row['taxsave_yes'];

            }

            // 주문목록
            $cart_data = $this->api_model->getCartDataId($od_id);
            if(!$cart_data) {
                throw new Exception('구매한 상품의 정보를 찾을 수 없습니다.');
            }

            foreach($cart_data as $row) {
                // 상품의 정보
                $goods_info = $this->global_lib->get_goods($row['gs_id']);

                // 상품의 가격 정보
                $return_data = array(
                    "gname" => $goods_info['gname'],
                    "od_id" => $row['od_id'],
                    "ct_price" => $row['ct_price'],
                    "ct_option" => $row['ct_option'],
                    "ct_qty" => $row["ct_qty"],
                    "get_img" => $this->funcomplex_lib->get_it_image_url($goods_info['index_no'], $goods_info['simg1'], 100, 100),
                );

                array_push($orderItem, $return_data);
            }

            // 상품의 합계 정보
            $tot_product = $tot['goodsprice'] == 0 ? (int)$tot['beforesprice'] : (int)$tot['goodsprice'];
        
            $stotal = $this->global_lib->get_order_spay($od_id); // 총계

            $totprice = (int) $stotal['useprice']; #총계
            $totpoint = (int) $stotal['point'];  #포인트적립
            $cancel_price = (int) $stotal['cancel'];
            $refund_mileage_price = (int) $stotal['refund_mileage_price'];

            $orderView = array(
                "orderInfo" => $orderInfo,
                "orderItem" => $orderItem,
                "tot_product" => $tot_product,
                "delivery" => $delivery,
                "tot_price" => $tot_price,
                "totalPrice" => $totprice,
                "tot_point" => $totpoint,
                "cancel_price" => $cancel_price,
                "refund_mileage_price" => $refund_mileage_price,
                "orderPerson" => $orderPerson,
                "orderDelivery" => $orderDelivery,
                "orderPayMethod" => $orderPayMethod
            );

            $result->status     = 200;
            $result->message    = "success";
            $result->data       = $orderView;

        } catch (Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 주문리스트
    public function orderList() {
        # 주문내역 리스트
        $result = new StdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            // 조회 기간
            $search = $this->input->get('search');
            $start_date = $this->input->get('start_date');
            $end_date = $this->input->get('end_date');

            if($start_date && !$end_date) {
                throw new Exception('종료년월을 입력해주세요.');
            }

            if(!$start_date && $end_date) {
                throw new Exception('시작년월을 입력해주세요.');
            }

            // 총 주문 리스트 가져오기 (od_id group by)
            $orderList = array();
            $cart_list = $this->api_model->getOrderList($member_data['id'], $search, $start_date, $end_date);

            foreach($cart_list as $key => $row) {
                $detail_list = $this->api_model->getOrderDetailList($member_data['id'], $row['od_id']);
                $temp = array();

                $i = 0;
                $totalSum = 0;
                $goodsSum = 0;
                $item_sendcost = array();

                // 장바구니 총 상품 금액 합계
                $totalOrderPrice = $this->api_model->getOrderAllPrice($row['od_id'], $mb_id);

                foreach($detail_list as $row2) {
                    // 상품 정보의 배열을 리턴한다.
                    $goods_info = $this->global_lib->get_goods($row2['gs_id']);
                    $this->load->vars(array('gs'=>$goods_info));

                    // 장바구니의 저장된 합계 금액을 계산한다.
                    $totalSql = "SELECT SUM(IF(io_type = 1, (io_price * ct_qty),((io_price + ct_price) * ct_qty))) as price,
                                        SUM(IF(io_type = 1, (0),(ct_point * ct_qty))) as point,
                                        SUM(IF(io_type = 1, (0),(ct_qty))) as qty,
                                        SUM(io_price * ct_qty) as opt_price
                                FROM  shop_cart
                                WHERE od_no = ?
                                AND   mb_id = ?";

                    $param = array($row2['od_no'], $member_data['id']);
                    $sum  = $this->query_model->returnOneArr($totalSql, $param);

                    $goodsSum = $goodsSum + $sum['price'];
                    $sell_price = $sum['price'];

                    // 배송비 구함
                    $info = $this->funcomplex_lib->get_item_sendcost($row2,$sell_price, $totalOrderPrice);
                    if(!$info) {
                        throw new Exception('배송비 정보 호출 실패');
                    }

                    $item_sendcost[] = $info['pattern'];
                    $info['price'] = isset($info['price']) ? $info['price'] : 0;

                    $data = array(
                        'goods_name' => $goods_info['gname'],
                        'gs_id' => $row2['gs_id'],
                        'od_id' => $row2['od_id'],
                        'on_no' => $row2['od_no'],
                        'qty' => $row2['sum_qty'],
                        'goods_price' => intval($row2['goods_price']),
                        'ct_get_img' => $this->funcomplex_lib->get_it_image_url($goods_info['index_no'], $goods_info['simg1'], 100, 100),
                        'ct_option_type' => $row2['io_type'],
                        'ct_option' => $row2['ct_option'],
                        'ct_option_text' => $row2['io_id'],
                        'ct_option_price' => $row2['io_price'],
                        // 'delivery' => $info['price'],
                    );
                    $i++;  // 장바구니 담긴 상품 
                    array_push($temp, $data);
                }

                // 배송비 검사
                $send_cost = 0;
                $com_send_cost = 0;
                $sep_send_cost = 0;
                $max_send_cost = 0;
                
                // 최종배송비
                $tot_send_cost = 0;

                $tot_final_sum = 0;
                
                if ($i > 0) {
                    $k = 0;
                    $condition = array();
                    
                    foreach($item_sendcost as $key) {
                        list($userid, $bundle, $price) = explode('|', $key);
                        $condition[$userid][$bundle][$k] = ($price!="") ? $price : 0;
                        $k++;
                    }

                    $com_array = array();
                    $val_array = array();

                    foreach($condition as $key=>$value) {
                        if(isset($condition[$key]['묶음'])) {
                            $com_send_cost += array_sum($condition[$key]['묶음']); // 묶음배송 합산
                            $max_send_cost += max($condition[$key]['묶음']); // 가장 큰 배송비 합산
                            $com_array[] = max(array_keys($condition[$key]['묶음'])); // max key
                            $val_array[] = max(array_values($condition[$key]['묶음']));// max value
                        }
                        if(isset($condition[$key]['개별'])) {
                            $sep_send_cost += array_sum($condition[$key]['개별']); // 묶음배송불가 합산
                            $com_array[] = array_keys($condition[$key]['개별']); // 모든 배열 key
                            $val_array[] = array_values($condition[$key]['개별']); // 모든 배열 value
                        }
                    }

                    $send_cost = $com_send_cost + $sep_send_cost; // 총 배송비합계
                    $tot_send_cost = $max_send_cost + $sep_send_cost; // 최종배송비
                    $tot_final_sum = $send_cost - $tot_send_cost; // 배송비할인
                }
                
                $totalSum = $goodsSum + $tot_send_cost;

                $dan_arr = [
                    '1' => "입금대기",
                    '2' => "입금완료",
                    '3' => "배송준비",
                    '4' => "배송중",
                    '5' => "배송완료",
                    '6' => "취소",
                    '7' => "반품",
                    '8' => "교환",
                    '9' => "환불",
                ];

                $dan = $row['dan'];
                $order_data = array(
                    'od_id'     => $row['od_id'],
                    'od_time'   => $row['od_time'],
                    'dan'       => $dan_arr[$dan],
                    'orderData' => $temp,
                    'goodsSum'  => $goodsSum,
                    'totalSum'  => $totalSum,
                    'send_cost' => $send_cost,
                    'tot_final_sum' => $tot_final_sum,
                    'tot_send_cost' => $tot_send_cost,
                );

                array_push($orderList,$order_data);
            }            

            $result->status      = 200;
            $result->message     = "success";
            $result->list        = $orderList;

        } catch (Exception $e) {
            $result->status = 400;
            $result->msg    = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 주문 상세
    public function orderDetail() {
        # 주문 상세 페이지
        $result = new stdClass();

        try {
            // 회원정보
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }
            
            // 주문번호
            $od_id = $this->input->get('od_id');
            if(!$od_id) {
                throw new Exception('주문번호가 넘어오지 않았습니다.');
            }
        
            $orderItem = array();
        
            $delivery  = "";
            $tot       = "";   // 상품 합계 정보
            $tot_price = "";   // 상품 합계 + 배송비 합계 정보
            $orderInfo = ""; 

            // 주문정보
            $order_data = $this->api_model->getPaymentData($mb_id, $od_id);
            if(!$order_data) {
                throw new Exception('주문정보가 없습니다');
            }

            // 주문정보
            foreach($order_data as $order) {   
    
                if(!$orderInfo) {
                    $orderstate        = "";
                    $od_date           = "";
                    $delivery_arr      = "";

                    $od_id        =  $order['od_id'];
                    $delivery_arr = $this->api_model->getDeliveryValue($od_id);
                    $delivery     = $delivery_arr['bsprice'];   // 배송비 정보 넣기
                    $tot          = $this->api_model->getOrderTotal($od_id);
                    $tot_price    = (int)$tot['goodsprice'] + (int)$delivery;
                    $orderstate   = $order['dan'];
                    $od_date      = $order['od_time'];
                    $receipt_date = $order['receipt_time'];

                    $dan_arr = [
                        '1' => "입금대기",
                        '2' => "입금완료",
                        '3' => "배송준비",
                        '4' => "배송중",
                        '5' => "배송완료",
                        '6' => "취소",
                        '7' => "반품",
                        '8' => "교환",
                        '9' => "환불",
                    ];
    
                    $orderInfo = array(
                        "od_id" => $od_id,
                        "ordersate" => $dan_arr[$orderstate],
                        "receipt_date" => $receipt_date,
                        "od_date" => $od_date,
                    );
                }                   

                // 주문자 정보
                $orderPerson['mb_id']      = $order['mb_id'];
                $orderPerson['name']       = $order['name'];
                $orderPerson['cellphone']  = $order['cellphone'];

                // 배송지 정보
                $orderDelivery['zip']    = $order['zip'];
                $orderDelivery['addr1']  = $order['addr1'];
                $orderDelivery['addr2']  = $order['addr2'];
                $orderDelivery['addr3']  = $order['addr3'];
                $orderDelivery['memo']   = $order['memo'];

                // 결제정보
                $orderPayMethod['paymethod']     = ($order['paymethod'] == '') ? "정보없음" :  $order['paymethod'];
                $orderPayMethod['deposit_name']  = $order['deposit_name'];
                $orderPayMethod['bank']          = $order['bank'];
                $orderPayMethod['cellphone']     = $order['cellphone'];
                $orderPayMethod['taxbill_yes']   = $order['taxbill_yes'];
                $orderPayMethod['taxsave_yes']   = $order['taxsave_yes'];

            }

            // 주문목록
            $sql = "SELECT * FROM shop_cart WHERE od_id = ? ORDER BY io_type ASC, index_no ASC";
            $param = array($od_id);
            $cart_data = $this->query_model->returnArr($sql,$param);

            if(!$cart_data) {
                throw new Exception('구매한 상품의 정보를 찾을 수 없습니다.');
            }

            foreach($cart_data as $row) {
                // 상품의 정보
                $goods_info = $this->global_lib->get_goods($row['gs_id']);

                // 상품의 가격 정보
                $return_data = array(
                    "od_id" => $row['od_id'],
                    "goods_name" => $goods_info['gname'],
                    "ct_price" => intVal($row['ct_price']),
                    "ct_option" => $row['ct_option'],
                    "ct_qty" => $row["ct_qty"],
                    "get_img" => $this->funcomplex_lib->get_it_image_url($goods_info['index_no'],$goods_info['simg1'],100,100),
                );

                array_push($orderItem, $return_data);
            }

            // 상품의 합계 정보
            $tot_product = $tot['goodsprice'] == 0 ? (int)$tot['beforesprice'] : (int)$tot['goodsprice'];

            $stotal = $this->global_lib->get_order_spay($od_id); // 총계

            $totprice = (int) $stotal['useprice']; #총계
            $totpoint = (int) $stotal['point'];  #포인트적립
            $cancel_price = (int) $stotal['cancel'];
            $refund_mileage_price = (int) $stotal['refund_mileage_price'];
            
            $orderView = array(
                "orderInfo" => $orderInfo,
                "orderItem" => $orderItem,
                "tot_product" => $tot_product,
                "tot_price" => $tot_price,
                "totalPrice" => $totprice,
                "tot_point" => $totpoint,
                "cancel_price" => $cancel_price,
                "refund_mileage_price" => $refund_mileage_price,
                "delivery" => $delivery,
                "orderPerson" => $orderPerson,
                "orderDelivery" => $orderDelivery,
                "orderPayMethod" => $orderPayMethod
            );

            $result->status = 200;
            $result->message = "success";
            $result->data = $orderView;

        } catch (Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

// -------------------- 주문관련 끝

// -------------------- 게시판 관련 시작

    // 공지사항
    public function noticeList() {
        # 공지사항 리스트
        $result = new StdClass();

        try {
            // 공지사항 게시판 번호
            $boardid = 13;
            $page = $this->input->get('page') ?? 1;
            
            $total_count = $this->api_model->getBoardCnt($boardid);
            $page_res = $this->getLimit($page, 10, $total_count);
            if(isset($page_res['msg'])) {
                throw new Exception($page_res['msg']);
            }

            $board_list = $this->api_model->getBoardList($boardid, $page_res['from_record'], $page_res['rows']);
            if(!$board_list) {
                throw new Exception('등록된 게시물이 없습니다');
            }

            $noticeList = array();
            foreach($board_list as $key => $list) {
                $noticeList[$key]['index_no'] = $list['index_no'];
                $noticeList[$key]['subject'] = $list['subject'];
                $noticeList[$key]['wdate'] = date("Y-m-d H:i:s", $list['wdate']);
            }

            $result->status = 200;
            $result->message = "success";
            $result->noticeList = $noticeList;
            $result->page = $page;

        } catch(Exception $e) {
			$result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }
    
    // 공지 상세 내역 
    public function noticeDetail() {
        # 공지사항 상세보기
        $result = new StdClass();

        try {
            // 공지사항 게시판 번호
            $boardid = 13;

            $index_no = $this->input->get('index_no');

            $board_data = $this->api_model->getBoardDetail($boardid,$index_no);
            if(!$board_data) {
                throw new Exception('게시글이 존재하지 않습니다.'); 
            }

            $view = array();
            $view['index_no'] = $board_data['index_no'];
            $view['subject'] = $board_data['subject'];
            $view['content'] = strip_tags($board_data['memo']);
            $view['wdate']   = date("Y-m-d H:i:s" ,$board_data['wdate']);

            $result->status = 200;
            $result->message = "success";
            $result->view = $view;

        } catch (Exception $e) {
			$result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // faq
    public function fqaList() {
        # FAQ 리스트
        $result = new stdClass();

        try {
            $cate = $this->input->get('cate');
            if(!$cate) {
                $cate = 1;
            }

            $sql = "SELECT * FROM shop_faq_cate ORDER BY index_no ASC";
            $faqCate = $this->query_model->returnArr($sql);

            $sql = "SELECT * FROM shop_faq WHERE cate = ? ORDER BY index_no ASC";
            $faqList = $this->query_model->returnArr($sql, $cate);

            $result->status = 200;
            $result->message = "success";
            $result->cate = $cate;
            $result->faqCate = $faqCate;
            $result->faqList = $faqList;

        } catch (Exception $e) {
			$result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 알림 리스트
    public function alarmList() {
        # 푸시알림 리스트
        $result = new StdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $page = $this->input->get('page') ?? 1;
            $total_count = $this->api_model->getAlarmCnt($member_data['id']);

            $page_res = $this->getLimit($page, 10, $total_count);
            if(isset($page_res['msg'])) {
                throw new Exception($page_res['msg']);
            }
        
            $alarmList = array();
            $alarm_list_data = $this->api_model->getAlarmList($member_data['id'], $page_res['from_record'], $page_res['rows']);
            if (empty($alarm_list_data)) {
                throw new Exception('리스트가 존재하지 않습니다');
            }

            foreach($alarm_list_data as $row) {
                $order_info = $this->api_model->getOrderDetailList($mb_id, $row['od_id']);
                $goods_arr = array();

                foreach($order_info as $row2) {
                    $goods_info = $this->global_lib->get_goods($row2['gs_id'], 'gname');
                    $data = array(
                        'gname' => $goods_info['gname'],
                        'qty' => $row2['sum_qty']
                    );
                    array_push($goods_arr, $data);
                }

                $return_list = array(
                    "index_no"     => $row['index_no'],
                    "od_id"        => $row['od_id'],
                    "alarm_to"     => $row['alarm_to'],
                    "alarm_title"  => $row['alarm_title'],
                    "alarm_read"   => $row['alarm_read'],
                    'goods_info'   => $goods_arr
                );
                array_push($alarmList, $return_list);
            }

            $result->status = 200;
            $result->message = "success";
            $result->list = $alarmList;

        } catch (Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 푸시알람 여부
    public function alarmSetting() {
        # 푸시알림 여부 설정
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $is_alarm = $this->input->get('is_alarm');
            if(!$is_alarm) {
                throw new Exception('알람 여부 데이터가 넘어오지 않았습니다.');
            }

            $update_data = $this->api_model->alarmUpdate($mb_id,$is_alarm);
            if(!$update_data) {
                throw new Exception('알람 여부 업데이트 중 문제가 발생하였습니다.');
            }

            $result->status = 200;
            $result->message = "success";

        } catch (Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }
// -------------------- 게시판 관련 끝

// -------------------- 마이페이지 시작

    // 마이페이지 접속
    public function mypage() {
        # 마이페이지 접속
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $member_data = $this->global_lib->get_member($mb_id, 'name, mb_img');
            $mb_name = $member_data['name'];
            $mb_img = $this->funcomplex_lib->get_it_mb_image_url($mb_id, $member_data['mb_img'], 90, 90);

            $result->status = 200;
            $result->message = "success";
            $result->mb_name = $mb_name;
            $result->mb_img = $mb_img;

        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }
    
    // 비밀번호 재확인
    public function pwdCheck() {
        # 비밀번호 재확인
        $result = new stdClass();

        try {
            $mb_id = $this->input->post('mb_id');
            if(!$mb_id) {
                throw new Exception('아이디가 넘어오지 않았습니다.');
            }
            
            $password = $this->input->post('password');
            if(!$password) {
                throw new Exception('비밀번호를 입력해주세요.');
            }

            $member_data = $this->global_lib->get_member($mb_id, 'passwd');
            $tmp_password = $this->global_lib->changePassword($password, $mb_id);

            if($tmp_password != $member_data['passwd']) {
                throw new Exception('비밀번호가 틀립니다.');
            }

            $result->status = 200;
            $result->messgae = "success";
        } catch (Exception $e) {
            $result->status = 400;
            $result->messgae = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 내정보
    public function myInfo() {
        # 내정보
        $result = new stdClass();

        try {

            $mb_id = $this->input->post('mb_id');
            if(!$mb_id) {
                throw new Exception('아이디 값이 넘어오지 않았습니다.');
            }

            $member_data = $this->global_lib->get_member($mb_id);
            $memberInfo = array(
                'mb_id' => $member_data['id'],
                'name' => $member_data['name'],
                'phone' => $member_data['cellphone'],
                'email' => $member_data['email'],
                'zip' => $member_data['zip'],
                'addr1' => $member_data['addr1'],
                'addr2' => $member_data['addr2']
            );

            $result->status = 200;
            $result->message = "success";
            $result->memberInfo = $memberInfo;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 내정보 업데이트
    public function myInfoUpdate() {
        # 내정보 업데이트
        $result = new stdClass();

        try {
            $mb_id = $this->input->post('mb_id');
            if(!$mb_id) {
                throw new Exception('아이디 값이 넘어오지 않았습니다');
            }
            
            // 비밀번호 있을 경우에만 비밀번호 변경
            $password = $this->input->post('password');
            $password_re = $this->input->post('password_re');
            if($password != "" || $password_re != "") {
                if(!$password || !$password_re) {
                    throw new Exception("비밀번호를 입력해주세요.");
                }

                if($password != $password_re) {
                    throw new Exception("비밀번호가 일치하지 않습니다.");
                }

                $data['passwd'] = $this->global_lib->changePassword($password, $mb_id);
            }

            $email = $this->input->post('email');
            $email_addr = $this->input->post('email_addr');
            if(!$email || !$email_addr) {
                throw new Exception("이메일을 입력해주세요.");
            }

            $email = $email."@".$email_addr;
            if($msg = $this->register_lib->exist_mb_email($email, $mb_id)) {
                throw new Exception($msg);
            }

            $phone = $this->input->post('phone');
            if(!$phone) {
                throw new Exception("휴대폰 번호를 입력해주세요.");
            }
            if($msg = valid_mb_hp($phone)) {
                throw new Exception($msg);
            }

            $zip = $this->input->post('zip');
            if(!$zip) {
                throw new Exception("우편번호 찾기를 진행해주세요.");
            }
            $zip = preg_replace('/[^0-9]/', '', $zip);

            $addr1 = $this->input->post('addr1');
            if(!$addr1) {
                throw new Exception("우편번호 찾기를 진행해주세요.");
            }
            $addr1 = $this->security->xss_clean($addr1);

            $addr2 = $this->input->post('addr2');
            if(!$addr2) {
                throw new Exception("상세주소를 입력해주세요.");
            }
            $addr2 = $this->security->xss_clean($addr2);

            $addr3 = $this->input->post('addr3');
            if($addr3) {
                $addr3 = $this->security->xss_clean($addr3);
            }

            $data['email'] = $email;
            $data['cellphone'] = $phone;
            $data['zip'] = $zip;
            $data['addr1'] = $addr1;
            $data['addr2'] = $addr2;
            $data['addr3'] = $addr3;

            $table = "shop_member";
            $where = array(
                "id" => $mb_id
            );
            $this->query_model->updateObj($table, $data, $where);

            $result->status = 200;
            $result->message = "success";

        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 회원 탈퇴
    public function leaveMember() {
        # 회원 탈퇴
        $result = new StdClass();

        try {
            $mb_id = $this->input->post('mb_id');
            if(!$mb_id) {
                throw new Exception('아이디를 입력해주세요.');
            }

            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception("회원정보가 존재하지 않습니다.");
            }
            
            if($mb_id != $member_data['id']) {
                throw new Exception("회원정보가 일치하지 않습니다.");
            }

            // 회원삭제
            $this->funcomplex_lib->member_delete($member_data['id']);

            $memo = "회원['".$member_data['name']."']님께서 탈퇴하셨습니다.";

            // 회원탈퇴 테이블
            $table = "shop_member_leave";
            $leave_array = array(
                "mb_id"     => $member_data['id'],
                "mb_name"   => $member_data['name'],
                "memo"      => $memo,
                "reg_time"  => DATE_TIME
            );

            $leave_idx = $this->query_model->insertObj($table, $leave_array);
            if(!$leave_idx) {
                throw new Exception("탈퇴 도중 문제가 발생하였습니다."); 
            }

            $result->status = 200;
            $result->message = "success";

        } catch (Exception $e) {
            $result->status  = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 상품 qna 리스트 => 클릭시 상품페이지 내 qna 리스트로 이동
    public function qnaList() {
        # 마이페이지 상품 QNA 리스트
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $page = $this->input->get('page') ?? 1;

            $total_count = $this->api_model->getMyQnaCnt($mb_id);
            $page_res = $this->getLimit($page, 10, $total_count);
            if(isset($page_res['msg'])) {
                throw new Exception($page_res['msg']);
            }

            $res = $this->api_model->getMyQnaList($mb_id, $page_res['from_record'], $page_res['rows']);

            $qnaList = array();
            foreach($res as $row) {
                $goods_info = $this->global_lib->get_goods($row['gs_id']);

                $data = array(
                    'iq_id' => $row['iq_id'],
                    'gname' => $goods_info['gname'],
                    "get_img" => $this->funcomplex_lib->get_it_image_url($goods_info['index_no'], $goods_info['simg1'], 100, 100),
                    'regdate' => $row['iq_time'],
                );

                array_push($qnaList, $data);
            }

            $result->status = 200;
            $result->message = "message";
            $result->qnaList = $qnaList;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 최근본 상품

    // 포인트 조회
    public function pointList() {
        # 마이페이지 포인트 조회
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $page = $this->input->get('page');

            $OM_SERVER_TIME = time();
            $OM_TIME_YMDHIS = date('Y-m-d H:i:s',$OM_SERVER_TIME);
            $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);

            $start_date = $this->input->get('start_date');
            $end_date = $this->input->get('end_date');

            if($start_date && !$end_date) {
                throw new Exception('종료년월을 입력해주세요.');
            }

            if(!$start_date && $end_date) {
                throw new Exception('시작년월을 입력해주세요.');
            }

            // 기간 검색
            $search = $this->input->get('search');
            if(!empty($search)) {
                $start_date = date('Y-m-d', strtotime($OM_TIME_YMD."-{$search} month"));
                $end_date = date('Y-m-d', strtotime($OM_TIME_YMD));
    
                $start_date = $start_date ." 00:00:00";
                $end_date = $end_date ." 23:59:59";
            }

            $sql_where = "";
            if(!empty($search) || ($start_date && $end_date)) {
                $sql_where .= "AND DATE_FORMAT(po_datetime, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}'";
            }
            
            $param = array($mb_id);
            // 테이블의 전체 레코드수만 얻음
            $sql = "SELECT count(*) AS cnt, SUM(po_point) AS totalPoint FROM shop_point WHERE mb_id = ?";
            $row = $this->query_model->returnOneArr($sql, $param);
            $totalPoint = $row['totalPoint'];

            $page_res = $this->getLimit($page, 10, $row['cnt']);
            if(isset($page_res['msg'])) {
                throw new Exception($page_res['msg']);
            }

            $sql = "SELECT * FROM shop_point WHERE mb_id = ? {$sql_where} ORDER BY po_id DESC LIMIT {$page_res['from_record']}, {$page_res['rows']}";
            $res = $this->query_model->returnArr($sql, $param);

            $pointList = array();
            foreach($res as $row) {
                $data = array(
                    'regdate' => $row['po_datetime'],
                    'point' => $row['po_point'], // 포인트
                    'use_point' => $row['po_use_point'], // 원래 포인트
                    'subject' => $row['po_content']
                );

                array_push($pointList, $data);
            }

            $result->status = 200;
            $result->message = "success";
            $result->totalPoint = $totalPoint;
            $result->pointList = $pointList;
            $result->page = $page;

        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 예치금 조회
    public function mileageList() {
        # 마이페이지 예치금 조회
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $page = $this->input->get('page');

            $OM_SERVER_TIME = time();
            $OM_TIME_YMDHIS = date('Y-m-d H:i:s',$OM_SERVER_TIME);
            $OM_TIME_YMD = substr($OM_TIME_YMDHIS, 0, 10);

            $start_date = $this->input->get('start_date');
            $end_date = $this->input->get('end_date');

            if($start_date && !$end_date) {
                throw new Exception('종료년월을 입력해주세요.');
            }

            if(!$start_date && $end_date) {
                throw new Exception('시작년월을 입력해주세요.');
            }

            // 기간 검색
            $search = $this->input->get('search');
            if(!empty($search)) {
                $start_date = date('Y-m-d', strtotime($OM_TIME_YMD."-{$search} month"));
                $end_date = date('Y-m-d',strtotime($OM_TIME_YMD));
    
                $start_date = $start_date ." 00:00:00";
                $end_date = $end_date ." 23:59:59";
            }

            $sql_where = "";
            if(!empty($search) || ($start_date && $end_date)) {
                $sql_where .= "AND DATE_FORMAT(po_datetime, '%Y-%m') BETWEEN '{$start_date}' AND '{$end_date}'";
            }
            
            $param = array($mb_id);
            // 테이블의 전체 레코드수만 얻음
            $sql = "SELECT count(*) AS cnt, SUM(po_point) AS totalPoint FROM shop_mileage WHERE mb_id = ?";
            $row = $this->query_model->returnOneArr($sql, $param);
            $totalPoint = $row['totalPoint'];

            $page_res = $this->getLimit($page, 10, $row['cnt']);
            if(isset($page_res['msg'])) {
                throw new Exception($page_res['msg']);
            }

            $sql = "SELECT * FROM shop_mileage WHERE mb_id = ? {$sql_where} ORDER BY po_id DESC LIMIT {$page_res['from_record']}, {$page_res['rows']}";
            $res = $this->query_model->returnArr($sql, $param);

            $pointList = array();
            foreach($res as $row) {
                $data = array(
                    'regdate' => $row['po_datetime'],
                    'point' => $row['po_point'], // 포인트
                    'use_point' => $row['po_use_point'], // 원래 포인트
                    'subject' => $row['po_content']
                );

                array_push($pointList, $data);
            }

            $result->status = 200;
            $result->message = "success";
            $result->totalPoint = $totalPoint;
            $result->pointList = $pointList;
            $result->page = $page;

        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 찜한 상품
    public function zzimList() {
        # 마이페이지 찜한 상품
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $zzim = $this->api_model->getZzim($mb_id);
            $goodsList = array_map([$this, 'getGoodsInfo'], $zzim);

            $result->status = 200;
            $result->message = "success";
            $result->goodsList = $goodsList;

        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 구매 후기
    public function reviewList() {
        # 마이페이지 구매후기 리스트 (작성 가능한 후기, 작성한 후기)
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $flag = $this->input->get('flag');
            if(!$flag) {
                throw new Exception('탭을 선택해주세요.');
            }

            $reviewsMap = [
                'complete' => 'getCompelteReview',
                'possible' => 'getPossibleReview',
            ];
            
            // $flag에 해당하는 메소드가 있으면 호출
            if (isset($reviewsMap[$flag])) {
                $review = $this->api_model->{$reviewsMap[$flag]}($mb_id);
            }

            $reviewList = array();
            foreach($review as $row) {
                $data = array(
                    'purchase_data' => $row['receipt_time'],
                    'gname' => $row['gname'],
                    'use_price' => $row['use_price'],
                    'index_no' => $row['index_no'],
                    'od_no' => $row['od_no']
                );

                array_push($reviewList, $data);
            }

            $result->status = 200;
            $result->message = "success";
            $result->reviewList = $reviewList;
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 구매후기 작성
    public function reviewWrite() {
        # 마이페이지 구매후기 작성
        $result = new stdClass();

        try {
            $pt_id = $this->load->get_var('pt_id');

            $mb_id = $this->input->post('mb_id');
            if(!$mb_id) {
                throw new Exception('아이디 값이 넘어오지 않았습니다.');
            }

            $od_no = $this->input->post('od_no');
            if(!$od_no) {
                throw new Exception('주문번호가 넘어오지 않았습니다.');
            }

            $order_data = $this->global_lib->get_order($od_no);
            if(empty($order_data)) {
                throw new Exception('주문내역이 없습니다.');
            }

            $score = $this->input->post('score');
            if(!$score) {
                throw new Exception('별점을 선택해주세요.');
            }

            $memo = $this->input->post('memo');
            if(!$memo) {
                throw new Exception('내용을 입력해주세요.');
            }

            // 이미 리뷰 작성한 후기인지 확인
            $already_review_data = $this->api_model->alreadyReview($od_no);

            if($already_review_data['cnt'] > 0) {
                throw new Exception('이미 구매후기를 작성하셨습니다.');
            }

            // 구매후기 차단 구매한사람만 쓸수있음 시작
            if(!$this->common_lib->find_review_flag($od_no , $mb_id)){
                throw new Exception('후기는 구매완료후 등록이 가능합니다.');
            }

            $data = array(
                'od_index' => $order_data['index_no'],
                'gs_id' => $order_data['gs_id'],
                'mb_id' => $mb_id,
                'memo' => $memo,
                'score' => $score,
                'reg_time' => DATE_TIME,
                'seller_id' => $order_data['seller_id'],
                'seller_name' => $order_data['seller_name'],
                'pt_id' => $pt_id,
                'od_id' => $order_data['od_id'],
                'od_no' => $od_no
            );

            $insert_id = $this->query_model->insertObj("shop_goods_review", $data);

            // 사진 저장
            $path="./data/review/";
            $imgArr = ['img1', 'img2', 'img3', 'img4'];

            $imgData = [];
            foreach($imgArr as $row) {
                $fileData = $this->uploadFile($row, $path);
                if(is_array($fileData)) {
                    $imgData[$row] = $fileData['file_name'];
                }
            }

            if(!empty($imgData)) {
                $this->query_model->updateObj("shop_goods_review", $imgData, "index_no = {$insert_id}");
            }

            // 주문내역 flag 변경
            $this->funcomplex_lib->set_order_review_flag(0, $order_data['index_no'], $mb_id, $od_no);

            $result->status = 200;
            $result->message = "success";

        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 구매후기 불러오기
    public function reviewRead() {
        # 마이페이지 구매후기 불러오기
        $result = new stdClass();

        try {
            $mb_id = $this->input->get('mb_id');
            $member_data = $this->global_lib->get_member($mb_id);
            if(!$member_data) {
                throw new Exception('회원 정보가 존재하지 않습니다.');
            }

            $index_no = $this->input->get('index_no');
            $review_data = $this->api_model->getReview($mb_id, $index_no);
            if(empty($review_data)) {
                throw new Exception('글 정보가 없습니다.');
            }

            $reviewData = array(
                'index_no' => $review_data['index_no'],
                'thumb' => $this->funcomplex_lib->get_it_image_url($review_data['gs_id'], $review_data['simg1'], 90, 90),
                'score' => $review_data['score'],
                'memo' => $review_data['memo'],
                'gname' => $review_data['gname'],
                'use_price' => $review_data['use_price']
            );

            $img_arr = ['img1', 'img2', 'img3', 'img4'];

            foreach($img_arr as $row) {
                if(!empty($review_data[$row])) {
                    $media_type = $this->funcomplex_lib->getMediaType($review_data[$row]);
                    $data = $this->funcomplex_lib->get_it_review_image_url($review_data[$row], 90, 90);

                    if($media_type == "video") {
                        $reviewData[$row] = "동영상";
                    } else {
                        $reviewData[$row] = $data;
                    }
                }
            }

            $result->status = 200;
            $result->message = "success";
            $result->reviewData = $reviewData;

        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }

    // 구매후기 수정
    public function reviewUpdate() {
        # 구매후기 수정
        $result = new stdClass();

        try {
            $mb_id = $this->input->post('mb_id');
            if(!$mb_id) {
                throw new Exception('아이디 값이 넘어오지 않았습니다.');
            }

            $index_no = $this->input->post('index_no');
            if(!$index_no) {
                throw new Exception('글 번호가 넘어오지 않았습니다.');
            }

            $review_data = $this->api_model->getReview($mb_id, $index_no);
            if(empty($review_data)) {
                throw new Exception('존재하지 않는 리뷰입니다.');
            }

            $score = $this->input->post('score');
            if(!$score) {
                throw new Exception('별점을 선택해주세요.');
            }
            $update_data['score'] = $score;

            $memo = $this->input->post('memo');
            if(!$memo) {
                throw new Exception('내용을 입력해주세요.');
            }
            $update_data['memo'] = $memo;
            
            // 파일 삭제
            $del_imgArr = ['img1', 'img2', 'img3', 'img4'];
            $uploadPath = "./data/review/";
             if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
            foreach($del_imgArr as $row) {
                $img = $this->input->post('del_'.$row);

                if(!empty($img)) {
                    // 이미지 삭제가 있으면 해당 이미지 파일명 가지고오기
                    $sql = "SELECT {$row} FROM shop_goods_review WHERE index_no = ? ";
                    $res = $this->query_model->returnOneArr($sql, array($index_no));

                    if(!empty($res[$row])) {
                        unlink($uploadPath.'/'.$res[$row]);
                        delete_review_thumbnail($res[$row]);
                    }

                    $update_data[$row] = '';
                }
            }

            $this->query_model->updateObj("shop_goods_review", $update_data, "index_no = '{$index_no}'");

            $result->status = 200;
            $result->message = "success";
        } catch (Exception $e) {
            $result->status = 400;
            $result->message = $e->getMessage();
        }

        echo json_encode($result);
    }


// -------------------- 마이페이지 끝


// 게시글 조회 limit 데이터 생성
private function getLimit($page, $rows, $total_count) {
    $page = empty($page) ? 1 : $page; // 페이지가 없으면 첫 페이지 (1 페이지)
    $total_page = ceil($total_count / $rows); // 전체 페이지 계산
    $from_record = ($page - 1) * $rows; // 시작 열을 구함

    if($page > $total_page) {
        $result['msg'] = "마지막 페이지 입니다.";
    }

    $result['from_record'] = $from_record;
    $result['rows'] = $rows;

    return $result;
}

// 파일 저장
private function uploadFile($name, $path) {
    $config['upload_path'] 		= $path;
    $config['allowed_types'] 	= '*';
    $config['max_size'] 		= '300000';
    $config['encrypt_name'] 	= TRUE;

    $this->load->library('upload',$config);
    $this->upload->do_upload($name);

    $fileInfo	= $this->upload->data();
    $fileObj	= array();
    if($this->upload->display_errors()!=""){
        return strip_tags($this->upload->display_errors());
    }

    $data = array(
        'file_path' => $path.$fileInfo['file_name'],
        'file_name' => $fileInfo['file_name'],
        'file_size' => $fileInfo['file_size'],
        'file_type' => $fileInfo['file_type'],
    );

    return $data;
}

}