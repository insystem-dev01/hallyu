<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mypage_template {
    protected $ci;
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('Query_model');
        
        $this->ci->load->helper('global_helper');
        
        $this->ci->load->library('gvcomplex_lib');
        $this->ci->load->library('global_lib');
        $this->ci->load->library('funcomplex_lib');
        $this->ci->load->library('common_lib');
        
        $this->ci->load->config('admin_menu');
        // Do something with $params
    }
    public function mypage_admin_head(){
        $admin_partner = false;
        $admin_seller = false;
        $code=$this->ci->input->get('code');
        $admin_snb_file="";
        $admin_partner="";
        $boardid=$this->ci->input->get('boardid');
        if(in_array($code, array(
            'seller_main',
            'seller_info',
            'seller_baesong',
            'seller_goods_list',
            'seller_goods_form',
            'seller_goods_stock',
            'seller_goods_optstock',
            'seller_goods_xls_reg',
            'seller_goods_xls_reg_update',
            'seller_goods_xls_mod',
            'seller_goods_xls_mod_update',
            'seller_goods_brand_list',
            'seller_goods_brand_form',
            'seller_goods_qa',
            'seller_goods_qa_form',
            'seller_goods_review',
            'seller_odr_2',
            'seller_odr_3',
            'seller_odr_4',
            'seller_odr_5',
            'seller_odr_6',
            'seller_odr_7',
            'seller_odr_8',
            'seller_odr_9',
            'seller_odr_list',
            'seller_odr_stats',
            'seller_odr_balance',
            'seller_odr_account'))) {
            $admin_snb_file = 'admin_snb1';
            $admin_seller = true;
        } else if(in_array($boardid, array('20','21'))) {
            $admin_snb_file = 'admin_snb1';
            $admin_seller = true;
        } else if(in_array($code, array(
            'partner_info',
            'partner_term',
            'partner_meta',
            'partner_sns',
            'partner_agree',
            'partner_baesong',
            'partner_sms',
            'partner_popular_list',
            'partner_popular_rank',
            'partner_payhistory',
            'partner_paylist',
            'partner_pg',
            'partner_kakaopay',
            'partner_naverpay',
            'partner_logo',
            'partner_banner_list',
            'partner_banner_form',
            'partner_mbanner_list',
            'partner_mbanner_form',
            'partner_menu_form',
            'partner_best_item',
            'partner_popup_list',
            'partner_popup_form',
            'partner_member_list',
            'partner_register_form',
            'partner_tree',
            'partner_stats_day',
            'partner_stats_month',
            'partner_visit',
            'partner_goods_admlist',
            'partner_order_admlist',
            'partner_goods_type',
            'partner_goods_plan',
            'partner_goods_plan_form',
            'partner_category_list',
            'partner_goods_brand_list',
            'partner_goods_brand_form',
            'partner_goods_form',
            'partner_goods_list',
            'partner_goods_stock',
            'partner_goods_optstock',
            'partner_goods_review',
            'partner_goods_xls_reg',
            'partner_goods_xls_reg_update',
            'partner_goods_xls_mod',
            'partner_goods_xls_mod_update',
            'partner_goods_qa',
            'partner_goods_qa_form',
            'partner_odr_1',
            'partner_odr_2',
            'partner_odr_3',
            'partner_odr_4',
            'partner_odr_5',
            'partner_odr_6',
            'partner_odr_7',
            'partner_odr_8',
            'partner_odr_9',
            'partner_odr_list'))) {
            $admin_snb_file = 'admin_snb2';
            $admin_partner = true;
        } else if(in_array($boardid, array('22','36'))) {
            $admin_snb_file = 'admin_snb2';
            $admin_partner = true;
        }
        $this->ci->load->vars(array('admin_snb_file'=>$admin_snb_file));

        $boardid=$this->ci->input->get('boardid');
        $is_member=$this->ci->load->get_var('is_member');
        $member=$this->ci->load->get_var('member');
        $config=$this->ci->load->get_var('config');

        if($this->ci->gvcomplex_lib->is_admin() && !$boardid) {
            alert('현재 관리자로 접속중입니다.', base_url());
        }
        if(!$is_member) {
            alert('로그인 후 이용하세요.', base_url().'bbs/login');
        }
        if(!$this->ci->gvcomplex_lib->is_admin() && !$this->ci->gvcomplex_lib->is_seller($member['id'])){
            alert('잘못된 접근입니다.', base_url());
        }
        $admin_shop_url="";
        $partner_term="";
        if($this->ci->funcomplex_lib->is_partner($member['id'])) {
            if($member['homepage'])
                $admin_shop_url = set_http($member['homepage']);
            else
                $admin_shop_url = set_http($member['id'].'.'.$config['admin_shop_url']);
        
            // 월관리비를 사용중인가?
            if($config['pf_expire_use'] && !is_null_time($member['term_date'])) {
                $partner_term = '<em>(가맹점 만료일: '.$member['term_date'].')</em>';
            }
        } else {
            $admin_shop_url = set_http($config['admin_shop_url']);
        }
        $faviconTag="";
        if($ico = $this->ci->funcomplex_lib->display_logo_url('favicon_ico')) { // 파비콘 
            $faviconTag="<link rel=\"shortcut icon\" href=\"{$ico}\" type=\"image/x-icon\">";
        }
        $supplyClass="";
        if($admin_seller) { 
            $supplyClass="class=\"supply\"";
        } 
        $admin_partnerTag="";
        if($admin_partner) {
            $admin_partnerTag="<h1><a href=\"".base_url()."mypage/page?code=partner_info\">가맹점 관리자</a></h1>";
        }
        $admin_sellerTag="";
        if($admin_seller) {
            $admin_sellerTag="<h1><a href=\"".base_url()."mypage/page?code=seller_main\">입점사 관리자</a></h1>";
        } 
        $partnerAdminTag="";
        if($this->ci->funcomplex_lib->is_partner($member['id'])) {
			$partnerAdminTag="<li><a href=\"".base_url()."mypage/page?code=partner_info\">가맹점 관리</a></li>";
		}
        $sellerAdminTag="";
        if($this->ci->gvcomplex_lib->is_seller($member['id'])) {
			$sellerAdminTag="<li><a href=\"".base_url()."mypage/page?code=seller_main\">입점사 관리</a></li>";
		}
        $memberName=$this->ci->funcomplex_lib->get_text($member['name']);
        $data=array('faviconTag'=>$faviconTag,'supplyClass'=>$supplyClass,'admin_partnerTag'=>$admin_partnerTag,'admin_sellerTag'=>$admin_sellerTag,
                    'admin_shop_url'=>$admin_shop_url,'partnerAdminTag'=>$partnerAdminTag,'sellerAdminTag'=>$sellerAdminTag,'memberName'=>$memberName,
                    'config'=>$config,'partner_term'=>$partner_term);
        $this->ci->load->view('mypage/template/header',$data);
        $this->ci->load->vars(array('admin_shop_url'=>$admin_shop_url));
    }
    public function board_header(){
        $default=$this->ci->load->get_var('default');
        $board=$this->ci->load->get_var('board');
        $default['de_board_wr_use'] = 0;
        $pg_title = $board['boardname'];
        $admin_snb_file=$this->ci->load->get_var('admin_snb_file');
        $admin_snb="";
        if($admin_snb_file=="admin_snb1"){
            $admin_snb=$this->admin_snb1();
        }else if($admin_snb_file=="admin_snb2"){
            $admin_snb=$this->admin_snb2();
        }
        $admin_head_sub=$this->admin_head_sub($pg_title);
        $fileTag="";
        $file = './data/board/boardimg/'.$board['fileurl1'];
		if(is_file($file) && $board['fileurl1']) {
            $file = base_url().'data/board/boardimg/'.$board['fileurl1'];
			$file = rpc($file, '', '');
			$fileTag='<p><img src="'.$file.'"></p>';
		}
        $data=array('fileTag'=>$fileTag,'admin_snb'=>$admin_snb,'admin_head_sub'=>$admin_head_sub);
        $this->ci->load->view('mypage/board_head',$data);
    }
    public function admin_head_sub($pg_title){
        $pg_navi=$this->ci->load->get_var('pg_navi');
        $data=array('pg_title'=>$pg_title,'pg_navi'=>$pg_navi);
        return $this->ci->load->view('mypage/template/head_sub',$data,TRUE);
    }
    public function admin_head($title=""){
        if(!$title)
            $title = '관리자 페이지';
        $favTag="";
        if($ico = $this->ci->funcomplex_lib->display_logo_url('favicon_ico')) { // 파비콘
            $favTag="<link rel=\"shortcut icon\" href=\"{$ico}\" type=\"image/x-icon\">";
        }
        $data=array('title'=>$title,'favTag'=>$favTag);
        return $this->ci->load->view('admin/template/admin_head',$data,TRUE);
    }
    public function admin_footer_sub(){
        return '</div>';
    }
    public function admin_footer_end(){
        $this->ci->load->view('mypage/template/divEnd');
    }
    public function admin_snb1(){
        $pg_navi = '입점사 관리자';
        $this->ci->load->vars(array('pg_navi'=>$pg_navi));
        $asideUrl= base_url().'mypage/page';
        $boardUrl= base_url().'bbs/noticeList';
        $seller=$this->ci->load->get_var('seller');
        $jaego1  = $this->ci->common_lib->admin_gs_jaego_bujog("and mb_id = '{$seller['seller_code']}'"); // 재고부족
        $jaego2  = $this->ci->common_lib->admin_io_jaego_bujog("and b.mb_id = '{$seller['seller_code']}'"); // 옵션재고부족
        $starCnt = $this->ci->common_lib->admin_goods_review("and seller_id = '{$seller['seller_code']}'"); // 총 상품평점
        $qaCnt   = $this->ci->common_lib->admRequest("shop_goods_qa", "and seller_id = '{$seller['seller_code']}'"); // 총 상품문의

        $sql_where = " where seller_id = '{$seller['seller_code']}' ";
        $sodrr = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan > 1 "); // 총 주문내역
        $sodr2 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 2 "); // 총 입금완료
        $sodr3 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 3 "); // 총 배송준비
        $sodr4 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 4 "); // 총 배송중
        $sodr5 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 5 "); // 총 배송완료
        $sodr6 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 6 "); // 총 입금전 취소
        $sodr7 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 7 "); // 총 배송후 반품
        $sodr8 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 8 "); // 총 배송후 교환
        $sodr9 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 9 "); // 총 배송전 환불
        return 
        "<dl>".
            $this->admin_snb1_printMenu1(1, '정보관리').
            $this->admin_snb1_printMenu2(1, '업체 정보관리', $asideUrl.'?code=seller_info').
            $this->admin_snb1_printMenu2(1, '업체 배송정책', $asideUrl.'?code=seller_baesong').
            $this->admin_snb1_printMenu1(2, '상품관리').
            $this->admin_snb1_printMenu2(2, '전체 상품관리', $asideUrl.'?code=seller_goods_list').
            // $this->admin_snb1_printMenu2(2, '상품 엑셀일괄등록', $asideUrl.'?code=seller_goods_xls_reg').
            // $this->admin_snb1_printMenu2(2, '상품 엑셀일괄수정', $asideUrl.'?code=seller_goods_xls_mod').
            $this->admin_snb1_printMenu2(2, '상품 재고관리', $asideUrl.'?code=seller_goods_stock', $jaego1).
            $this->admin_snb1_printMenu2(2, '상품 옵션재고관리', $asideUrl.'?code=seller_goods_optstock', $jaego2).
            $this->admin_snb1_printMenu2(2, '브랜드관리', $asideUrl.'?code=seller_goods_brand_list').
            $this->admin_snb1_printMenu2(2, '상품 문의관리', $asideUrl.'?code=seller_goods_qa', $qaCnt).
            $this->admin_snb1_printMenu2(2, '상품 평점관리', $asideUrl.'?code=seller_goods_review', $starCnt).
            $this->admin_snb1_printMenu1(3, '주문관리').
            $this->admin_snb1_printMenu2(3, '주문리스트(전체)', $asideUrl.'?code=seller_odr_list', $sodrr['cnt']).
            $this->admin_snb1_printMenu2(3, '입금완료(배송요청)', $asideUrl.'?code=seller_odr_2', $sodr2['cnt']).
            $this->admin_snb1_printMenu2(3, '배송준비', $asideUrl.'?code=seller_odr_3', $sodr3['cnt']).
            $this->admin_snb1_printMenu2(3, '배송중', $asideUrl.'?code=seller_odr_4', $sodr4['cnt']).
            $this->admin_snb1_printMenu2(3, '배송완료', $asideUrl.'?code=seller_odr_5', $sodr5['cnt']).
            $this->admin_snb1_printMenu1(4, '취소/교환/반품/환불 관리').
            $this->admin_snb1_printMenu2(4, '입금전 취소', $asideUrl.'?code=seller_odr_6', $sodr6['cnt']).
            $this->admin_snb1_printMenu2(4, '배송전 환불', $asideUrl.'?code=seller_odr_9', $sodr9['cnt']).
            $this->admin_snb1_printMenu2(4, '배송후 반품', $asideUrl.'?code=seller_odr_7', $sodr7['cnt']).
            $this->admin_snb1_printMenu2(4, '배송후 교환', $asideUrl.'?code=seller_odr_8', $sodr8['cnt']).
            $this->admin_snb1_printMenu1(5, '기타/정산관리').
            $this->admin_snb1_printMenu2(5, '주문통계분석', $asideUrl.'?code=seller_odr_stats').
            $this->admin_snb1_printMenu2(5, '정산대기목록', $asideUrl.'?code=seller_odr_balance').
            $this->admin_snb1_printMenu2(5, '정산완료목록', $asideUrl.'?code=seller_odr_account').
            $this->admin_snb1_printMenu2(5, '공지사항', $boardUrl.'?boardid=20').
            // $this->admin_snb1_printMenu2(5, '질문과답변', $boardUrl.'?boardid=21').
        "</dl>";
    }
    public function admin_snb2(){
        $pg_navi = '가맹점 관리자';
        $member=$this->ci->load->get_var('member');
        $config=$this->ci->load->get_var('config');
        $pf_auth_good=$this->ci->load->get_var('pf_auth_good');
        $pf_auth_pg=$this->ci->load->get_var('pf_auth_pg');
        
        $this->ci->load->vars(array('pg_navi'=>$pg_navi));
        $asideUrl= base_url().'mypage/page';
        $boardUrl= base_url().'bbs/noticeList';

        $jaego1  = $this->ci->common_lib->admin_gs_jaego_bujog("and mb_id = '{$member['id']}'"); // 재고부족
        $jaego2  = $this->ci->common_lib->admin_io_jaego_bujog("and b.mb_id = '{$member['id']}'"); // 옵션재고부족
        $starCnt = $this->ci->common_lib->admin_goods_review("and seller_id = '{$member['id']}'"); // 총 상품평점
        $qaCnt   = $this->ci->common_lib->admRequest("shop_goods_qa", "and seller_id = '{$member['id']}'"); // 총 상품문의

        $sql_where = " where seller_id = '{$member['id']}' ";
        $sodrr = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan > 0 "); // 총 주문내역
        $sodr1 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 1 "); // 총 입금대기
        $sodr2 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 2 "); // 총 입금완료
        $sodr3 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 3 "); // 총 배송준비
        $sodr4 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 4 "); // 총 배송중
        $sodr5 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 5 "); // 총 배송완료
        $sodr6 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 6 "); // 총 입금전 취소
        $sodr7 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 7 "); // 총 배송후 반품
        $sodr8 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 8 "); // 총 배송후 교환
        $sodr9 = $this->ci->common_lib->admin_order_status_sum("{$sql_where} and dan = 9 "); // 총 배송전 환불
        $returnStr="
        <dl>".
            $this->admin_snb2_printMenu1(1, '기본환경 설정').
            $this->admin_snb2_printMenu2(1, '기본정보 관리', $asideUrl.'?code=partner_info');
            if($config['pf_expire_use']) { // 
                $returnStr.=$this->admin_snb2_printMenu2(1, '가맹점 연장신청', $asideUrl.'?code=partner_term');
            }
            $returnStr.=
            $this->admin_snb2_printMenu2(1, '검색엔진 최적화(SEO) 설정', $asideUrl.'?code=partner_meta').
            $this->admin_snb2_printMenu2(1, '소셜 네트워크 설정', $asideUrl.'?code=partner_sns');
            if($pf_auth_good) { // 상품판매 권한이있나?
                $returnStr.=$this->admin_snb2_printMenu2(1, '배송/교환/반품 설정', $asideUrl.'?code=partner_baesong');
            }
            if($config['pf_auth_sms']) { // 개별 문자설정 사용중인가?
                $returnStr.=$this->admin_snb2_printMenu2(1, 'SMS 기본설정', $asideUrl.'?code=partner_sms');
            }
            $returnStr.=$this->admin_snb2_printMenu2(1, '쇼핑몰 약관 설정', $asideUrl.'?code=partner_agree');
            if($pf_auth_pg) { // 개별 PG결제 권한이있나?
                $returnStr.=$this->admin_snb2_printMenu1(2, '결제관리').
                $this->admin_snb2_printMenu2(2, '전자결제 (PG) 설정', $asideUrl.'?code=partner_pg').
                //echo printMenu2(2, '카카오페이 설정', asideUrl.'?code=partner_kakaopay');
                $this->admin_snb2_printMenu2(2, '네이버페이 설정', $asideUrl.'?code=partner_naverpay');
            }
            $returnStr.=
            $this->admin_snb2_printMenu1(3, '디자인관리').
            $this->admin_snb2_printMenu2(3, '로고 관리', $asideUrl.'?code=partner_logo').
            $this->admin_snb2_printMenu2(3, '통합 배너관리', $asideUrl.'?code=partner_banner_list').
            $this->admin_snb2_printMenu2(3, '통합 배너관리(모바일)', $asideUrl.'?code=partner_mbanner_list').
            $this->admin_snb2_printMenu2(3, '메뉴 관리', $asideUrl.'?code=partner_menu_form').
            $this->admin_snb2_printMenu2(3, '메인 진열관리', $asideUrl.'?code=partner_best_item').
            $this->admin_snb2_printMenu2(3, '팝업 관리', $asideUrl.'?code=partner_popup_list').
            $this->admin_snb2_printMenu1(4, '회원관리').
            $this->admin_snb2_printMenu2(4, '회원목록', $asideUrl.'?code=partner_member_list').
            $this->admin_snb2_printMenu2(4, '신규 회원등록', $asideUrl.'?code=partner_register_form').
            $this->admin_snb2_printMenu2(4, '트리 회원조회', $asideUrl.'?code=partner_tree').
            $this->admin_snb2_printMenu2(4, '일별 가입통계분석', $asideUrl.'?code=partner_stats_day').
            $this->admin_snb2_printMenu2(4, '월별 가입통계분석', $asideUrl.'?code=partner_stats_month').
            $this->admin_snb2_printMenu2(4, '접속자검색', $asideUrl.'?code=partner_visit').
            $this->admin_snb2_printMenu1(5, '상품관리(본사)').
            $this->admin_snb2_printMenu2(5, '본사 상품목록', $asideUrl.'?code=partner_goods_admlist').
            $this->admin_snb2_printMenu2(5, '본사 상품판매실적', $asideUrl.'?code=partner_order_admlist').
            $this->admin_snb2_printMenu2(5, '카테고리 관리', $asideUrl.'?code=partner_category_list').
            $this->admin_snb2_printMenu2(5, '상품 진열관리', $asideUrl.'?code=partner_goods_type').
            $this->admin_snb2_printMenu2(5, '기획전 관리', $asideUrl.'?code=partner_goods_plan').
            $this->admin_snb2_printMenu2(5, '상품 검색어관리', $asideUrl.'?code=partner_popular_list').
            $this->admin_snb2_printMenu2(5, '상품 검색어순위', $asideUrl.'?code=partner_popular_rank');
            if($pf_auth_good) { // 상품판매 권한이있나?
                $returnStr.=
                $this->admin_snb2_printMenu1(6, '상품관리').
                $this->admin_snb2_printMenu2(6, '전체 상품관리', $asideUrl.'?code=partner_goods_list').
                // $this->admin_snb2_printMenu2(6, '상품 엑셀일괄등록', $asideUrl.'?code=partner_goods_xls_reg').
                // $this->admin_snb2_printMenu2(6, '상품 엑셀일괄수정', $asideUrl.'?code=partner_goods_xls_mod').
                $this->admin_snb2_printMenu2(6, '상품 재고관리', $asideUrl.'?code=partner_goods_stock', $jaego1).
                $this->admin_snb2_printMenu2(6, '상품 옵션재고관리', $asideUrl.'?code=partner_goods_optstock', $jaego2).
                $this->admin_snb2_printMenu2(6, '브랜드관리', $asideUrl.'?code=partner_goods_brand_list').
                $this->admin_snb2_printMenu2(6, '상품 문의관리', $asideUrl.'?code=partner_goods_qa', $qaCnt).
                $this->admin_snb2_printMenu2(6, '상품 평점관리', $asideUrl.'?code=partner_goods_review', $starCnt).
                $this->admin_snb2_printMenu1(7, '주문관리').
                $this->admin_snb2_printMenu2(7, '주문리스트(전체)', $asideUrl.'?code=partner_odr_list', $sodrr['cnt']).
                $this->admin_snb2_printMenu2(7, '입금대기', $asideUrl.'?code=partner_odr_1', $sodr1['cnt']).
                $this->admin_snb2_printMenu2(7, '입금완료', $asideUrl.'?code=partner_odr_2', $sodr2['cnt']).
                $this->admin_snb2_printMenu2(7, '배송준비', $asideUrl.'?code=partner_odr_3', $sodr3['cnt']).
                $this->admin_snb2_printMenu2(7, '배송중', $asideUrl.'?code=partner_odr_4', $sodr4['cnt']).
                $this->admin_snb2_printMenu2(7, '배송완료', $asideUrl.'?code=partner_odr_5', $sodr5['cnt']).
                $this->admin_snb2_printMenu1(8, '취소/교환/반품/환불 관리').
                $this->admin_snb2_printMenu2(8, '입금전 취소', $asideUrl.'?code=partner_odr_6', $sodr6['cnt']).
                $this->admin_snb2_printMenu2(8, '배송전 환불', $asideUrl.'?code=partner_odr_9', $sodr9['cnt']).
                $this->admin_snb2_printMenu2(8, '배송후 반품', $asideUrl.'?code=partner_odr_7', $sodr7['cnt']).
                $this->admin_snb2_printMenu2(8, '배송후 교환', $asideUrl.'?code=partner_odr_8', $sodr8['cnt']);
            }
            $returnStr.=
            $this->admin_snb2_printMenu1(9, '수수료 리포트').
            $this->admin_snb2_printMenu2(9, '수수료 정산', $asideUrl.'?code=partner_paylist').
            $this->admin_snb2_printMenu2(9, '수수료 건별내역', $asideUrl.'?code=partner_payhistory').
            $this->admin_snb2_printMenu2(9, '공지사항', $boardUrl.'?boardid=22').
            $this->admin_snb2_printMenu2(9, '질문과답변', $boardUrl.'?boardid=36').
        "</dl>";
        return $returnStr;
    }

    public function mypage_admin_footer(){
        $config=$this->ci->load->get_var('config');
        $boardid=$this->ci->input->get('boardid');
        $boardidScript="";
        if(!$boardid) { // �Խ����ϰ�� admin.js ������ �����ϸ� �ȵ�
            $boardidScript="<script src=\"".base_url()."admin/js/admin.js?ver=".time()."\"></script>";
        }
        $data=array('config'=>$config,'boardidScript'=>$boardidScript);
        $this->ci->load->view('mypage/template/footer',$data);
    }
    private function admin_snb1_printMenu1($key, $subject){
        $svc_class = 'smenu'.$key;
        if(get_cookie("ck_{$svc_class}")) {
            $svc_class .= ' menu_close';
        }
    
        return '<dt class="'.$svc_class.' menu_toggle">'.$subject.'</dt>';
    }
    private function admin_snb1_printMenu2($key, $subject, $url, $menu_cnt=''){
        $svc_class = 'smenu'.$key;
        if(get_cookie("ck_{$svc_class}")) {
            $svc_class .= ' menu_close';
        }
    
        $current_class = '';
        $count_class = '';
        if(is_numeric($menu_cnt)) {
            if($menu_cnt > 0)
                $current_class = ' class="snb_air"';
            $count_class = '<em'.$current_class.'>'.$menu_cnt.'</em>';
        }
        $urlObj=explode('=',$url)[1];
        $active=($urlObj==$this->ci->input->get('code'))?'active':"";
    
        return '<dd class="'.$svc_class.' '.$active.'"><a href="'.$url.'">'.$subject.$count_class.'</a></dd>';
    }

    private function admin_snb2_printMenu1($key, $subject){
        $svc_class = 'pmenu'.$key;
        if(get_cookie("ck_{$svc_class}")) {
            $svc_class .= ' menu_close';
        }
    
        return '<dt class="'.$svc_class.' menu_toggle">'.$subject.'</dt>';
    }
    
    private function admin_snb2_printMenu2($key, $subject, $url, $menu_cnt=''){
        $svc_class = 'pmenu'.$key;
        if(get_cookie("ck_{$svc_class}")) {
            $svc_class .= ' menu_close';
        }
    
        $current_class = '';
        $count_class = '';
        if(is_numeric($menu_cnt)) {
            if($menu_cnt > 0)
                $current_class = ' class="snb_air"';
            $count_class = '<em'.$current_class.'>'.$menu_cnt.'</em>';
        }

        $urlObj=explode('=',$url)[1];
        $active=($urlObj==$this->ci->input->get('code'))?'active':"";

        return '<dd class="'.$svc_class.' '.$active.'"><a href="'.$url.'">'.$subject.$count_class.'</a></dd>';
    }
}