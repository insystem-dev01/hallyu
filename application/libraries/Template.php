<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template {
    protected $ci;
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('Query_model');
        $this->ci->load->helper('global_helper');
        $this->ci->load->model('query_model');
        $this->ci->load->library('gvcomplex_lib');
        $this->ci->load->library('global_lib');
        $this->ci->load->library('funcomplex_lib');
        // Do something with $params
    }
    public function aside_my(){
        $member=$this->ci->load->get_var('member');
        $config=$this->ci->load->get_var('config');
        $member['name']=isset($member['name'])?$member['name']:"";
        $member['point']=isset($member['point'])?$member['point']:0;

        $name=addslashes($this->ci->funcomplex_lib->get_text($member['name']));
        $giftYes="";
        if($config['gift_yes']) {
            $giftYes="<dd><a href=\"".base_url()."shop/gift\">쿠폰인증</a></dd>";
        } 
        $couponYes="";
        if($config['coupon_yes']) {
            $couponYes="<dd><a href=\"".base_url()."shop/coupon\">쿠폰관리</a></dd>";
        }
        $aside=array('name'=>$name,'giftYes'=>$giftYes,"couponYes"=>$couponYes,'member'=>$member);
        return $this->ci->load->view('pc/template/aside_my',$aside,TRUE);
    }
    #pc부분
        //헤더부분
        public function header($index=false){
            $pt_id=$this->ci->load->get_var('pt_id');
            $config=$this->ci->load->get_var('config');
            $is_admin=$this->ci->load->get_var('is_admin');
            $is_member=$this->ci->load->get_var('is_member');
            $member=$this->ci->load->get_var('member');
            $urlencode=$this->ci->load->get_var('urlencode');
            $default=$this->ci->load->get_var('default');

            $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
            $OM_TIME_YMD=substr($OM_TIME_YMDHIS,0,10);
            $gw_menu=$this->ci->global_lib->get_menu();
            $this->header_sub();
            $data2=array();
            $popupTag="";
            if($index==true){
                $popupTag=$this->popInc();
            }
            $data2['popup']=$popupTag;
            $hd_banTag="";
            if(!get_cookie("ck_hd_banner")) { // 상단 큰배너
                $hd_banTag.="<div id=\"hd_banner\">";
                    if($banner1 = $this->ci->funcomplex_lib->display_banner_bg(1, $pt_id)) { // 배너가 있나? 
                        $hd_banTag.=$banner1;
                        $hd_banTag.="<img src=\"".base_url()."img/bt_close.gif\" id=\"hd_close\">";
                    } // banner end
                $hd_banTag.="</div>";
            } // cookie end 
            $data2['hd_banTag']=$hd_banTag;
            #tnd
            $tnb_str="";
            if($is_admin)
                $tnb_str .= '<li><a href="'.$is_admin.'" target="_blank" class="admin">관리자</a></li>';
            if($is_member) {
                $tnb_str .= '<li><a href="'.base_url().'process/logout">로그아웃</a></li>';
            } else {
                $tnb_str .= '<li><a href="'.base_url().'bbs/login?url='.$urlencode.'">로그인</a></li>';
                $tnb_str .= '<li><a href="'.base_url().'bbs/register?type=user">회원가입</a></li>';
            }
            $tnb_str .= '<li><a href="'.base_url().'shop/mypage">마이페이지</a></li>';
            $tnb_str .= '<li><a href="'.base_url().'shop/orderinquiry">주문조회</a></li>';
            $tnb_str .= '<li><a href="'.base_url().'bbs/faq?faqcate=1">고객센터</a></li>';
            $data2['tnb_str']=$tnb_str;

            #상단부 영역 시작
            $hd_logo=$this->ci->funcomplex_lib->display_logo();
            $data2['hd_logo']=$hd_logo;
            $data2['OM_HASH_TOKEN']=md5(base_url().$OM_TIME_YMD.$_SERVER['REMOTE_ADDR']);

            #전체카테고리 작업
            $cateTag=$this->category();
            $data2['cateTag']=$cateTag;

            #gw_menu태그
            $gw_menu_tag="";
            foreach($gw_menu as $key => $row) {
                $page_url = base_url().$row['url'];
                $gw_menu_tag.='<li><a href="'.$page_url.'">'.$row['menu_name'].'</a></li>';
            }
            $data2['gw_menu']=$gw_menu_tag;

            #인덱스일때만 나타나는 메인 슬라이드
            $mainBan="";
            if($index==true){
                $mainBan=$this->mainSlider();
            }
            $data2['mainBan']=$mainBan;
            #모바일이 아닐때 나타나는 퀵메뉴
            $quickMenu="";
            if(!$this->ci->agent->is_mobile()){
                $quickMenu=$this->quickMenu($index);
            }
            $data2['quickMenu']=$quickMenu;
            #인덱스파일이 아닐때 나타나는 것
            $data2['no_index']=($index!=true)?"<div class='cont_inner'>":"";

            // 장바구니 태그
            $data2['cart_data'] = '<a class="hd_cart" href="'.base_url().'shop/cart">
                                        <span class="ic_num">'.$this->ci->gvcomplex_lib->get_cart_count($member['id']).'</span>
                                    </a>';
            $this->ci->load->view('pc/template/header',$data2);
        }
        public function header_sub(){
            $OM_TIME_YHS=date("YmdHis", time());
            $pt_id=$this->ci->load->get_var('pt_id');
            $config=$this->ci->load->get_var('config');
            $is_seometa=$this->ci->load->get_var('is_seometa');
            $gs=$this->ci->load->get_var('gs');
            
            $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
            $OM_TIME_YMD=substr($OM_TIME_YMDHIS,0,10);
            $gw_menu=$this->ci->config->item('gw_menu');
            
            $add_meta=($config['add_meta'])?$config['add_meta']:"";
            $tb=$this->ci->load->get_var('tb');
            #타이틀 지정 처리 함수
                $om_head_title="";
                if(!isset($tb['title'])) {
                    $tb['title'] = $this->ci->gvcomplex_lib->get_head_title('head_title', $pt_id);
                    $om_head_title = $tb['title'];
                }
                else {
                    $om_head_title = $tb['title']; // 상태바에 표시될 제목
                    $om_head_title .= " | ".$this->ci->gvcomplex_lib->get_head_title('head_title', $pt_id);
                }
            #파이콘 태그
                $ico=$this->ci->funcomplex_lib->display_logo_url('favicon_ico');
                $faviconTag="";
                if($ico){
                    $faviconTag='<link rel="shortcut icon" href="'.$ico.'" type="image/x-icon">';
                }
            #마우스 우클릭 방지
                $mouseRight="";
                if($config['mouseblock_yes']){
                    $mouseRight='<script>
                    $(document).ready(function(){
                        $(document).bind("contextmenu", function(e) {
                            return false;
                        });
                    });
                    $(document).bind(\'selectstart\',function() {return false;});
                    $(document).bind(\'dragstart\',function(){return false;});
                    </script>';
                }
            #head 내부태그
                $headInner=($config['head_script'])?$config['head_script']:"";
            #body script
                $bodyScript=isset($tb['body_script'])?$tb['body_script']:"";
            #seometa.lib.php파일
                $seometa = array();

                if($is_seometa == 'it') { // 상품
                    $seometa['author'] = '';
                    $seometa['keywords'] = $gs['keywords'];
                    $seometa['title'] = $gs['gname'];
                    $seometa['description'] = strip_tags($gs['explan']);
                    $seometa['type'] = 'product';
                    $seometa['url'] = base_url().'shop/view?index_no='.$gs['index_no'];
                    $seometa['img'] = $this->ci->funcomplex_lib->get_it_image_url($gs['index_no'], $gs['simg2'], 300, 300);
                }
                
                $seometa['author'] = isset($seometa['author']) ? $seometa['author'] : $config['meta_author'];
                $seometa['keywords'] = isset($seometa['keywords']) ? $seometa['keywords'] : $config['meta_keywords'];
                $seometa['title'] = isset($seometa['title']) ? $seometa['title'] : $tb['title'];
                $seometa['description'] = isset($seometa['description']) ? $seometa['description'] : $config['meta_description'];
                $seometa['type'] = isset($seometa['type']) ? $seometa['type'] : 'website';
                $seometa['url'] = isset($seometa['url']) ? $seometa['url'] : base_url();
                $seometa['img'] = isset($seometa['img']) ? $seometa['img'] : $this->ci->funcomplex_lib->display_logo_url('sns_logo');
                
                $seometa['title'] = str_replace("\"", "'", $seometa['title']);
                $seometa['description'] = str_replace("\"", "'", $seometa['description']);
                $seometa['site_name'] = str_replace("\"", "'", $config['shop_name']);
                $seometa['url'] = preg_replace("/[\<\>\'\"\\\'\\\"\(\)\^\*]/", "", $seometa['url']);
                
                if($seometa['img'] && preg_match("/(\.(jpg|jpeg|gif|png))$/i", $seometa['img'])) {
                    $seometa['img'] = $seometa['img'].'?v='.$OM_TIME_YHS;
                }
            #load header
            // 현재 접속자
            // 게시판 제목에 ' 포함되면 오류 발생
            $tb['lo_location'] = addslashes($tb['title']);
            if(!$tb['lo_location'])
                $tb['lo_location'] = addslashes($this->ci->security->xss_clean($_SERVER['REQUEST_URI']));
            $tb['lo_url'] = addslashes($this->ci->security->xss_clean($_SERVER['REQUEST_URI']));
            if(strstr($tb['lo_url'], '/'.base_url().'admin/') || $this->ci->gvcomplex_lib->is_admin()) $tb['lo_url'] = '';
            $arr=array('tb'=>$tb);
            $this->ci->load->vars($arr);
            $data=array('seometa'=>$seometa,'om_head_title'=>$om_head_title,'add_meta'=>$add_meta,'favicon'=>$faviconTag,'mouseRight'=>$mouseRight,'headInner'=>$headInner,
                        'bodyScript'=>$bodyScript);
            $this->ci->load->view('pc/template/header_sub',$data);
        }
        #board_head부분
        public function board_header(){
            $board=$this->ci->load->get_var('board');
            $aside_cs=$this->aside_cs();
            $fileulr1Tag="";
            if($board['fileurl1']) {
                $fileulr1Tag="<p class=\"marb10\"><img src=\"".base_url()."data/board/boardimg/{$board['fileurl1']}\"></p>";
            } 
            $data=array('fileulr1Tag'=>$fileulr1Tag,'board'=>$board,'aside_cs'=>$aside_cs);
            $this->ci->load->view('pc/template/board_head',$data);
        }
        private function popInc(){
            $pt_id=$this->ci->load->get_var('pt_id');
            $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
            $OM_TIME_YMD=substr($OM_TIME_YMDHIS,0,10);
            $sql = " select * from shop_popup where ? between begin_date and end_date and device IN ( 'both', 'pc' ) 
                    and state = '0' and mb_id = ? order by index_no asc ";
            $data=array($OM_TIME_YMD,$pt_id);
            $res=$this->ci->Query_model->returnArr($sql,$data);
            $pop="";
            $i=0;
            foreach($res as $key => $nw){
                // 이미 체크 되었다면 Continue
                if(isset($_COOKIE["hd_pops_{$nw['index_no']}"]))
                    continue;
                $pop.="<div id=\"hd_pops_{$nw['index_no']}\" class=\"hd_pops\" style=\"top:{$nw['top']}px;left:{$nw['lefts']}px\">";
                $pop.="  <div class=\"hd_pops_con\" style=\"width:{$nw['width']}px;height:{$nw['height']}px\">";
                $pop.=$this->ci->funcomplex_lib->conv_content($nw['memo'], 1);
                $pop.="  </div>";
                $pop.="  <div class=\"hd_pops_footer\">";
                $pop.="      <button class=\"hd_pops_reject hd_pops_{$nw['index_no']} 24\"><strong>24</strong>시간 동안 다시 열지않음.</button>";
                $pop.="      <button class=\"hd_pops_close hd_pops_{$nw['index_no']}\">닫기</button>";
                $pop.="  </div>";
                $pop.="</div>";
                $i++;
            }
            if($i == 0) $pop= '<span class="sound_only">팝업레이어 알림이 없습니다.</span>';
            $popData=array('pop'=>$pop);
            $popTag=$this->ci->load->view('pc/template/popInc',$popData,true);
            return $popTag;
        }
        private function category(){
            $cateTag="";
            $mod = 5;
            $res = $this->ci->gvcomplex_lib->sql_query_cgy('all');
            $i=0;
            foreach($res as $key => $row){
                $href = base_url().'shop/cateList?ca_id='.$row['catecode'];
                if($i && $i%$mod == 0) $cateTag.="</ul>\n<ul>\n";
                $cateTag.="<li class=\"c_box\">";
                $cateTag.="<a href=\"{$href}\" class=\"cate_tit\">{$row['catename']}</a>";
                    $r = $this->ci->gvcomplex_lib->sql_query_cgy($row['catecode'], 'COUNT');
                    if($r['cnt'] > 0) {
                        $cateTag.="<ul>";
                        $res2 = $this->ci->gvcomplex_lib->sql_query_cgy($row['catecode']);
                        foreach($res2 as $key => $row2){
                            $href2 = base_url().'shop/cateList?ca_id='.$row2['catecode'];
                            $cateTag.="<li><a href=\"{$href2}\">{$row2['catename']}</a></li>";
                        }
                        $cateTag.="</ul>";
                    }
                $cateTag.="</li>";
                $i++;
            }
            $li_cnt = ($i%$mod);
            if($li_cnt) { // 나머지 li
                for($i=$li_cnt; $i<$mod; $i++)
                $cateTag.="<li></li>";
            }
            return $cateTag;
        }
        public function mainSlider(){
            $mainBanTag="";
            $pt_id=$this->ci->load->get_var('pt_id');
            $sql = $this->ci->gvcomplex_lib->sql_banner_rows(0, $pt_id);
            $res=$this->ci->Query_model->returnArr($sql);
            $NumRes=$this->ci->Query_model->returnQueryObj($sql);
            $mbn_rows = $NumRes->num_rows();
            $txt_arr = array();

            if($mbn_rows){
                $mainBanTag.="<div id=\"mbn_wrap\" class=\"mart160\">";
                $txt_w = (100 / $mbn_rows);
                foreach($res as $key => $row){
                    if($row['bn_text'])
                        $txt_arr[] = $row['bn_text'];
                    $a1 = $a2 = $bg = '';
                    $file = './data/banner/'.$row['bn_file'];
                    if(is_file($file) && $row['bn_file']) {
                        if($row['bn_link']) {
                            $a1 = "<a href=\"{$row['bn_link']}\" target=\"{$row['bn_target']}\">";
                            $a2 = "</a>";
                        }

                        $row['bn_bg'] = preg_replace("/([^a-zA-Z0-9])/", "", $row['bn_bg']);
                        if($row['bn_bg']) $bg = "#{$row['bn_bg']} ";

                        $file = rpc($file);
                        $mainBanTag .= 
                            "<div class=\"mbn_img\" style=\"background:{$bg}url('{$file}') no-repeat top center;\">
                                {$a1}{$a2}
                            </div>\n";
                    }
                }
                $mainBanTag.="</div>";
            }
            $mainBanTag.="<script>$(document).on('ready', function() {";
            if(count($txt_arr)>0){
                $mainBanTag .= "var txt_arr = ".json_encode($txt_arr).";";
                $mainBanTag .= 
                "$('#mbn_wrap').slick({
                    autoplay: true,
                    autoplaySpeed: 4000,
                    dots: false,
                    fade: true,
                    // customPaging: function(slider, i) {
                    //     return \"<span>\"+txt_arr[i]+\"</span>\";
                    // }
                });
                // $('#mbn_wrap .slick-dots li').css('width', '{$txt_w}%');
                // document.querySelector('#mbn_wrap > .slick-dots').addEventListener('mouseover', (event) => {
                //     if (event.target.tagName.toLowerCase() === 'li') {
                //         event.target.click();
                //     }
                // });
                ";
            }else{
                $mainBanTag.="$('#mbn_wrap').slick({
                    autoplay: true,
                    autoplaySpeed: 4000,
                    dots: false,
                    fade: true
                });";
            }
            $mainBanTag.="});</script>";
            
            return $mainBanTag;
        }
        private function quickMenu($index=false){
            $pt_id=$this->ci->load->get_var('pt_id');
            $quickTag="";
            $banRows=$this->ci->funcomplex_lib->display_banner_rows(90, $pt_id);

            $pr_tmp = get_cookie('ss_pr_idx');
            $pr_idx = explode('|',$pr_tmp);
            $pr_tot_count = 0;
            $k = 0;
            $mod = 3;
            foreach($pr_idx as $idx){
                $rowx = $this->ci->global_lib->get_goods($idx, 'index_no, simg1');
                if(!isset($rowx['index_no']))
                    continue;

                $href = base_url().'shop/view?index_no='.$idx;

                if($pr_tot_count % $mod == 0) $k++;

                $pr_tot_count++;
                $quickTag.="<p class=\"dn c{$k}\">";
                $quickTag.="    <a href=\"$href\">".$this->ci->funcomplex_lib->get_it_image($idx, $rowx['simg1'], 60, 60)."</a>";
                $quickTag.="</p>";
            }
            if(!$pr_tot_count)
                $quickTag= '<p class="no_item">없음</p>';
            
            $data=array('banRows'=>$banRows,'quickTag'=>$quickTag,'pr_tot_count'=>$pr_tot_count,'mod'=>$mod,'index'=>$index);
            $quickTag=$this->ci->load->view('pc/template/quick',$data,true);
            return $quickTag;
        }
        #board_head부분
       
        public function aside_cs(){
            $asideCsList="";
            $sql = " select * from shop_board_conf where gr_id='gr_mall' order by index_no asc ";
		    $res = $this->ci->Query_model->returnArr($sql);
            foreach($res as $key => $row){
                $bo_href = base_url().'bbs/noticeList?boardid='.$row['index_no'];
                $asideCsList.='<dt><a href="'.$bo_href.'">'.$row['boardname'].'</a></dt>';
            }
            // FAQ MASTER
            $i=0;
            $fm_sql = "select * from shop_faq_cate order by index_no asc";
            $fm_result = $this->ci->Query_model->returnArr($fm_sql);
            $fagList="";
            foreach($fm_result as $key => $row){
                if($i==0) $fagList.="<dd>\n<ul>\n";
                $fm_href = base_url().'bbs/faq?faqcate='.$row['index_no'];
                $fagList.='<li><a href="'.$fm_href.'">'.$row['catename'].'</a></li>';
                $i++;
            }
            if($i==0){
                $fagList.="</ul>\n</dd>\n";
            }

            $data=array('asideCsList'=>$asideCsList,'fagList'=>$fagList);
            return $this->ci->load->view('pc/template/aside_cs',$data,TRUE);
        }
        //풋터부분
        public function footer($index=false){
            $default=$this->ci->load->get_var('default');
            $config=$this->ci->load->get_var('config');
            $pt_id=$this->ci->load->get_var('pt_id');
            #index가 아니면 실행
            $closeDiv="";
            if(!$index){
                $closeDiv="</div>";
            }
            $insta_tag=$this->insta();
            $linkTag=$this->snsLink();
            $bank = unserialize($default['de_bank_account']);
            $board_latest=$this->ci->funcomplex_lib->board_latest(13, 100, 4, $pt_id);
            $escrow_js=$this->escrow_js();
            $mobile_but=$this->mobile_but();
            $tailScript="";
            if($config['tail_script']){
                $tailScript=$config['tail_script'];
            }
            $data2=array('closeDiv'=>$closeDiv,'insta_tag'=>$insta_tag,"linkTag"=>$linkTag,"config"=>$config,'bank'=>$bank,"board_latest"=>$board_latest,
                        'escrow_js'=>$escrow_js,"mobile_but"=>$mobile_but,'tailScript'=>$tailScript);
            $this->ci->load->view('pc/template/footer',$data2);
            $this->footer_sub();
        }
        public function footer_sub(){
            $this->ci->load->view('pc/template/footer_sub');
        }
        private function insta(){
            $default=$this->ci->load->get_var('default');
            $insta_tag="";
            if($default['de_insta_access_token']){
                $userId = explode(".", $default['de_insta_access_token']);
                $insta_tag="\
                <script src=\"".base_url()."js/instafeed.min.js\"></script>
                <script>
                    var userFeed = new Instafeed({
                        get: 'user',
                        userId: \"{$userId[0]}\",
                        limit: 8,
                        template: '<li class=\"ins_li\"><a href=\"{{link}}\" target=\"_blank\"><img src=\"{{image}}\" /></a></li>',
                        accessToken: \"{$default['de_insta_access_token']}\"
                    });
                    userFeed.run();
                </script>
                <div class=\"insta\">
                    <h2 class=\"tac\"><i class=\"fa fa-instagram\"></i> INSTAGRAM<a href=\"https://www.instagram.com/{$default['de_insta_url']}\" target=\"_blank\">@ {$default['de_insta_url']}</a></h2>
                    <ul id=\"instafeed\">
                    </ul>
                </div>";
            }
            return $insta_tag;
        }
        private function snsLink(){
            $default=$this->ci->load->get_var('default');
            $linkTag="";
            if($default['de_sns_facebook']) { $linkTag.="<a href=\"{$default['de_sns_facebook']}\" target=\"_blank\" class=\"sns_fa\"><img src=\"".base_url()."theme/img/sns_fa.png\" title=\"facebook\"></a>";} 
            if($default['de_sns_twitter']) { $linkTag.="<a href=\"{$default['de_sns_twitter']}\" target=\"_blank\" class=\"sns_tw\"><img src=\"".base_url()."theme/img/sns_tw.png\" title=\"twitter\"></a>";}
            if($default['de_sns_instagram']) { $linkTag.="<a href=\"{$default['de_sns_instagram']}\" target=\"_blank\" class=\"sns_in\"><img src=\"".base_url()."theme/img/sns_in.png\" title=\"instagram\"></a>";}
            if($default['de_sns_pinterest']) { $linkTag.="<a href=\"{$default['de_sns_pinterest']}\" target=\"_blank\" class=\"sns_pi\"><img src=\"".base_url()."theme/img/sns_pi.png\" title=\"pinterest\"></a>"; } 
            if($default['de_sns_naverblog']) { $linkTag.="<a href=\"{$default['de_sns_naverblog']}\" target=\"_blank\" class=\"sns_bl\"><img src=\"".base_url()."theme/img/sns_bl.png\" title=\"naverblog\"></a>"; } 
            if($default['de_sns_naverband']) { $linkTag.="<a href=\"{$default['de_sns_naverband']}\" target=\"_blank\" class=\"sns_ba\"><img src=\"".base_url()."theme/img/sns_ba.png\" title=\"naverband\"></a>"; } 
            if($default['de_sns_kakaotalk']) { $linkTag.="<a href=\"{$default['de_sns_kakaotalk']}\" target=\"_blank\" class=\"sns_kt\"><img src=\"".base_url()."theme/img/sns_kt.png\" title=\"kakaotalk\"></a>"; }
            if($default['de_sns_kakaostory']) { $linkTag.="<a href=\"{$default['de_sns_kakaostory']}\" target=\"_blank\" class=\"sns_ks\"><img src=\"".base_url()."theme/img/sns_ks.png\" title=\"kakaostory\"></a>";}
            return $linkTag;
        }
        
        private function escrow_js(){
            $default=$this->ci->load->get_var('default');
            $return="";
            if($default['de_pg_service'] == 'inicis') {
                $return.="window.open(\"https://mark.inicis.com/mark/escrow_popup.php?mid={$default['de_inicis_mid']}\", \"escrow_foot_pop\",\"scrollbars=yes,width=565,height=683,top=10,left=10\");";
            }
            return $return;
        }
        private function mobile_but(){
            $return="";
            if($this->ci->agent->is_mobile()){
                $return="<a href=\"".base_url()."?device=mobile\" id=\"device_change\">모바일 버전으로 보기</a>";
            }
            return $return;
        }
        #board_footer 부분
        public function board_footer(){
            $board=$this->ci->load->get_var('board');
            $fileulr2Tag="";
            if($board['fileurl2']) {
                $fileulr2Tag="<p class=\"mart10\"><img src=\"".base_url()."data/board/boardimg/{$board['fileurl2']}\"></p>";
            }
            $data=array('fileulr2Tag'=>$fileulr2Tag);
            $this->ci->load->view('pc/template/board_footer',$data);
        }
    #mb부분
        //헤더부분
        public function mb_header($index=false){
            $pt_id=$this->ci->load->get_var('pt_id');
            $config=$this->ci->load->get_var('config');
            $is_admin=$this->ci->load->get_var('is_admin');
            $is_member=$this->ci->load->get_var('is_member');
            $urlencode=$this->ci->load->get_var('urlencode');
            $default=$this->ci->load->get_var('default');
            $member=$this->ci->load->get_var('member');
            $tb=$this->ci->load->get_var('tb');
            $pg=$this->ci->load->get_var('pg');

            $category = $this->ci->global_lib->get_menu();

            #타이틀 지정 처리 함수
            $om_head_title="";
            if(!isset($tb['title'])) {
                $tb['title'] = $this->ci->gvcomplex_lib->get_head_title('head_title', $pt_id);
                $om_head_title = $tb['title'];
            }else {
                $om_head_title = $tb['title']; // 상태바에 표시될 제목
                $om_head_title .= " | ".$this->ci->gvcomplex_lib->get_head_title('head_title', $pt_id);
            }

            $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
            $OM_TIME_YMD=substr($OM_TIME_YMDHIS,0,10);
            $this->mb_header_sub();
            $slideMenu=$this->slideMenu();
            $ban1Tag="";
            $banner1=$this->ci->funcomplex_lib->mobile_banner(1, $pt_id);
            if($banner1){
                $ban1Tag=$banner1;
            }
            $mlogo=$this->ci->funcomplex_lib->mobile_display_logo();
            $cart_cnt=$this->ci->gvcomplex_lib->get_cart_count($member['id']);
            $OM_HASH_TOKEN=md5(base_url().$OM_TIME_YMD.$_SERVER['REMOTE_ADDR']);
            $ss_tx=$this->ci->input->get('ss_tx');

            #인기검색어
            $popularWd=$this->ci->funcomplex_lib->mobile_popular("인기검색어", 6, $pt_id);

            #인기 순위
            $popularRankWd=$this->ci->funcomplex_lib->mobile_popular_rank(10, $pt_id);
            $no_index_class="";
            if(!$index==true){
                $no_index_class="class=\"sub_wrap\"";
            }

            $gw_menu_tag="";
            foreach($category as $index => $row) {
				$page_url = base_url().'m/'.$row['url'];
                $gw_menu_tag.='<li><a href="'.$page_url.'">'.$row['menu_name'].'</a></li>';
            }

            $no_index_tag="";
            // if(!$index==true){
            //     $no_index_tag="
            //     <div id=\"content_title\">
            //         <span>".((isset($pg['pagename']) ? $pg['pagename'] : $tb['title']))."</span>
            //     </div>";
            // }

            $data2 = array(
                'slideMenu' => $slideMenu,
                'category' => $category,
                'ban1Tag' => $banner1,
                'mlogo' => $mlogo,
                'cart_cnt' => $cart_cnt,
                'OM_HASH_TOKEN' => $OM_HASH_TOKEN,
                'ss_tx' => $ss_tx,
                'popularWd' => $popularWd,
                'popularRankWd' => $popularRankWd,
                'no_index_class' => $no_index_class,
                'gw_menu_tag' => $gw_menu_tag,
                'no_index_tag' => $no_index_tag
            );

            $this->ci->load->view('mb/template/header', $data2);
        }
        public function mb_header_sub(){
            $OM_TIME_YHS=date("YmdHis", time());
            $pt_id=$this->ci->load->get_var('pt_id');
            $config=$this->ci->load->get_var('config');
            $is_seometa=$this->ci->load->get_var('is_seometa');
            $gs=$this->ci->load->get_var('gs');
            $tb=$this->ci->load->get_var('tb');
            
            $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
            $OM_TIME_YMD=substr($OM_TIME_YMDHIS,0,10);
            $gw_menu=$this->ci->config->item('gw_menu');
            
            $add_meta=($config['add_meta'])?$config['add_meta']:"";
            #타이틀 지정 처리 함수
                $om_head_title="";
                if(!isset($tb['title'])) {
                    $tb['title'] = $this->ci->gvcomplex_lib->get_head_title('head_title', $pt_id);
                    $om_head_title = $tb['title'];
                }
                else {
                    $om_head_title = $tb['title']; // 상태바에 표시될 제목
                    $om_head_title .= " | ".$this->ci->gvcomplex_lib->get_head_title('head_title', $pt_id);
                }
            #파이콘 태그
                $ico=$this->ci->funcomplex_lib->display_logo_url('favicon_ico');
                $faviconTag="";
                if($ico){
                    $faviconTag='<link rel="shortcut icon" href="'.$ico.'" type="image/x-icon">';
                }
            #마우스 우클릭 방지
                $mouseRight="";
                if($config['mouseblock_yes']){
                    $mouseRight='<script>
                    $(document).ready(function(){
                        $(document).bind("contextmenu", function(e) {
                            return false;
                        });
                    });
                    $(document).bind(\'selectstart\',function() {return false;});
                    $(document).bind(\'dragstart\',function(){return false;});
                    </script>';
                }
            #head 내부태그
                $headInner=($config['head_script'])?$config['head_script']:"";
            #body script
                $bodyScript=isset($tb['body_script'])?$tb['body_script']:"";
            #seometa.lib.php파일
                $seometa = array();

                if($is_seometa == 'it') { // 상품
                    $seometa['author'] = '';
                    $seometa['keywords'] = $gs['keywords'];
                    $seometa['title'] = $gs['gname'];
                    $seometa['description'] = strip_tags($gs['explan']);
                    $seometa['type'] = 'product';
                    $seometa['url'] = base_url().'shop/view?index_no='.$gs['index_no'];
                    $seometa['img'] = $this->ci->funcomplex_lib->get_it_image_url($gs['index_no'], $gs['simg2'], 300, 300);
                }
                
                $seometa['author'] = isset($seometa['author']) ? $seometa['author'] : $config['meta_author'];
                $seometa['keywords'] = isset($seometa['keywords']) ? $seometa['keywords'] : $config['meta_keywords'];
                $seometa['title'] = isset($seometa['title']) ? $seometa['title'] : $tb['title'];
                $seometa['description'] = isset($seometa['description']) ? $seometa['description'] : $config['meta_description'];
                $seometa['type'] = isset($seometa['type']) ? $seometa['type'] : 'website';
                $seometa['url'] = isset($seometa['url']) ? $seometa['url'] : base_url();
                $seometa['img'] = isset($seometa['img']) ? $seometa['img'] : $this->ci->funcomplex_lib->display_logo_url('sns_logo');
                
                $seometa['title'] = str_replace("\"", "'", $seometa['title']);
                $seometa['description'] = str_replace("\"", "'", $seometa['description']);
                $seometa['site_name'] = str_replace("\"", "'", $config['shop_name']);
                $seometa['url'] = preg_replace("/[\<\>\'\"\\\'\\\"\(\)\^\*]/", "", $seometa['url']);
                
                if($seometa['img'] && preg_match("/(\.(jpg|jpeg|gif|png))$/i", $seometa['img'])) {
                    $seometa['img'] = $seometa['img'].'?v='.$OM_TIME_YHS;
                }
            #load header
            // 현재 접속자
            // 게시판 제목에 ' 포함되면 오류 발생
            $tb['lo_location'] = addslashes($tb['title']);
            if(!$tb['lo_location'])
                $tb['lo_location'] = addslashes($this->ci->security->xss_clean($_SERVER['REQUEST_URI']));
            $tb['lo_url'] = addslashes($this->ci->security->xss_clean($_SERVER['REQUEST_URI']));
            if(strstr($tb['lo_url'], '/'.base_url().'admin/') || $this->ci->gvcomplex_lib->is_admin()) $tb['lo_url'] = '';
            $arr=array('tb'=>$tb);
            $this->ci->load->vars($arr);
            $data=array('seometa'=>$seometa,'om_head_title'=>$om_head_title,'add_meta'=>$add_meta,'favicon'=>$faviconTag,'mouseRight'=>$mouseRight,'headInner'=>$headInner,
                        'bodyScript'=>$bodyScript);
            $this->ci->load->view('mb/template/header_sub',$data);
        }
        protected function slideMenu(){
            $is_member=$this->ci->load->get_var('is_member');
            $urlencode=$this->ci->load->get_var('urlencode');
            $config=$this->ci->load->get_var('config');
            $default=$this->ci->load->get_var('default');
            $logTag="";
            if($is_member){
                $logTag.="<a href=\"".base_url()."m/process/logout\" class=\"btn_medium\">로그아웃</a>";
            }else{
                $logTag.="<a href=\"".base_url()."m/bbs/login?url={$urlencode}\" class=\"btn_medium\">로그인</a>";
            }
            $joinTag="";
            if($is_member){
                $joinTag.="<a href=\"".base_url()."m/bbs/member_confirm?url=register_form\" class=\"btn_medium bx-white\">정보수정</a>";
            }else{
                $joinTag.="<a href=\"".base_url()."m/bbs/register?type=user\" class=\"btn_medium bx-white\">회원가입</a>";
            }
            $r = $this->ci->gvcomplex_lib->sql_query_cgy('all', 'COUNT');
            #카테고리에 관련된 메뉴
            $cateTag="";
            if($r['cnt'] > 0){
                $cateTag.="<ul>";
                $res =$this->ci->gvcomplex_lib->sql_query_cgy('all');
                foreach($res as $key => $row){
                    $href = base_url().'m/shop/cateList?ca_id='.$row['catecode'];
                    $cateTag.="
                    <li class=\"bm\">{$row['catename']}</li>
                    <li class=\"subm\">
                        <ul>
                            <li><a href=\"{$href}\">전체</a></li>";
                            $res2 =$this->ci->gvcomplex_lib->sql_query_cgy($row['catecode']);
                            foreach($res2 as $key => $row2){
                                $href2 = base_url().'m/shop/cateList?ca_id='.$row2['catecode'];
                                $cateTag.="<li><a href=\"{$href2}\">{$row2['catename']}</a></li>";
                            }
                        $cateTag.="</ul>";
                    $cateTag.="</li>";
                }
                $cateTag.="</ul>";
            }else{
                $cateTag="<p class=\"sct_noitem\">등록된 분류가 없습니다.</p>";
            }
            #custom에 관련된 메뉴
            $customTag="";
            $sql = " select * from shop_board_conf where gr_id='gr_mall' order by index_no asc ";
			$res = $this->ci->Query_model->returnArr($sql);
            foreach($res as $key => $row){
                $customTag.="<li><a href=\"".base_url()."m/bbs/board_list?boardid={$row['index_no']}\">{$row['boardname']}</a></li>";
            }
            #mypage에 관련된 메뉴
            $gift="";
            if($config['gift_yes']){
                $gift="<li><a href=\"".base_url()."m/shop/gift\">쿠폰인증</a></li>";
            }
            $coupon="";
            if($config['coupon_yes']){
                $coupon="<li><a href=\"".base_url()."m/shop/coupon\">쿠폰관리</a></li>";
            }
            $exitTag="";
            if($is_member){
                $exitTag="<li><a href=\"".base_url()."m/bbs/leave_form\">회원탈퇴</a></li>";
            }
            $bank = unserialize($default['de_bank_account']);

            $data=array('logTag'=>$logTag,'joinTag'=>$joinTag,'cateTag'=>$cateTag,'customTag'=>$customTag,'giftTag'=>$gift,"couponTag"=>$coupon,"exitTag"=>$exitTag,
                        'bank'=>$bank);
            $returnTag=$this->ci->load->view('mb/template/slideMenu',$data,true);
            return $returnTag;
        }
        public function mb_pop_inc(){
            $pt_id=$this->ci->load->get_var('pt_id');
            $OM_TIME_YMDHIS=date("Y-m-d H:i:s", time());
            $OM_TIME_YMD=substr($OM_TIME_YMDHIS,0,10);

            $sql = " select * from shop_popup where ? between begin_date and end_date and device IN ( 'both', 'mobile' )
            and state = '0' and mb_id = ? order by index_no asc ";
            $data=array($OM_TIME_YMD,$pt_id);
            $res=$this->ci->Query_model->returnArr($sql,$data);
            $popTag="";
            $i=0;
            foreach($res as $key => $nw){
                // 이미 체크 되었다면 Continue
                if(isset($_COOKIE["hd_pops_{$nw['index_no']}"]))
                    continue;
                $popTag.="<div id=\"hd_pops_{$nw['index_no']}\" class=\"hd_pops\">
                    <div class=\"hd_pops_con\">";
                $popTag.=$this->ci->funcomplex_lib->conv_content($nw['memo'], 1);
                $popTag.="</div>
                    <div class=\"hd_pops_footer\">
                        <button class=\"hd_pops_reject hd_pops_{$nw['index_no']} 24\">오늘하루 열지않음.</button>
                        <button class=\"hd_pops_close hd_pops_{$nw['index_no']}\">닫기</button>
                    </div>
                </div>";
                $i++;
            }
            $popTag.="
            <script>
            function funLoad(){
                $('.hd_pops').css({'width':'90%'});
                $('.hd_pops_con img').css({'width':'100%'});
            }
            window.onload = funLoad;
            window.onresize = funLoad;
            </script>";
            if($i==0){
                $popTag='<span class="sound_only">팝업레이어 알림이 없습니다.</span>';
            }
            $data2=array('popTag'=>$popTag);
            $this->ci->load->view('mb/template/popInc',$data2);
        }
        public function mb_footer($index=false){
            $config=$this->ci->load->get_var('config');
            $default=$this->ci->load->get_var('default');
            $super=$this->ci->load->get_var('super');
            $insta_tag=$this->mb_insta();
            $sns_tag=$this->mb_sns_link();
            $goPc="";
            if($this->ci->agent->is_mobile() || $this->ci->load->get_var('is_mobile')){
                $goPc="<li><a href=\"".base_url()."?device=pc\">PC버전</a></li>";
            }
            $part_yes="";
            if($config['partner_reg_yes']){
                $part_yes="<li><a href=\"".base_url()."m/bbs/partner_reg\">쇼핑몰분양신청</a></li>";
            }
            $seller_yes="";
            if($config['seller_reg_yes']){
                $seller_yes="<li><a href=\"".base_url()."m/bbs/seller_reg\">온라인입점신청</a></li>";
            }
            $saupjaTag="<li><a href=\"javascript:saupjaonopen('".(conv_number($config['company_saupja_no']))."');\">사업자정보확인</a></li>";
            $bank = unserialize($default['de_bank_account']);

            $data=array('insta_tag'=>$insta_tag,'sns_tag'=>$sns_tag,'goPc'=>$goPc,'part_yes'=>$part_yes,'seller_yes'=>$seller_yes,'saupjaTag'=>$saupjaTag,'config'=>$config,
                        'bank'=>$bank,'super'=>$super);
            $this->ci->load->view('mb/template/footer',$data);
            $this->mb_footer_sub();

        }
        protected function mb_insta(){
            $default=$this->ci->load->get_var('default');
            $insta_tag="";
            if($default['de_insta_access_token']) { // 인스타그램
                $userId = explode(".", $default['de_insta_access_token']);
                $insta_tag.="<script src=\"".base_url()."js/instafeed.min.js\"></script>
                <script>
                    var userFeed = new Instafeed({
                        get: 'user',
                        userId: \"{$userId[0]}\",
                        limit: 6,
                        template: '<li class=\"ins_li\"><a href=\"{{link}}\" target=\"_blank\"><img src=\"{{image}}\" /></a></li>',
                        accessToken: \"{$default['de_insta_access_token']}\"
                    });
                    userFeed.run();
                </script>

                <div class=\"insta\">
                    <h2 class=\"tac\"><i class=\"fa fa-instagram\"></i> INSTAGRAM<a href=\"https://www.instagram.com/{$default['de_insta_url']}\" target=\"_blank\">@ {$default['de_insta_url']}</a></h2>
                    <ul id=\"instafeed\">
                    </ul>
                </div>";
                return $insta_tag;
            }
        }
        protected function mb_sns_link(){
            $default=$this->ci->load->get_var('default');
            $mb_sns="";
            if($default['de_sns_facebook']) {
                $mb_sns.="<a href=\"{$default['de_sns_facebook']}\" target=\"_blank\" class=\"sns_fa\"><img src=\"".base_url()."theme/img/sns_fa.png\" title=\"facebook\"></a>";
            }
            if($default['de_sns_twitter']) { 
                $mb_sns.="<a href=\"{$default['de_sns_twitter']}\" target=\"_blank\" class=\"sns_tw\"><img src=\"".base_url()."theme/img/sns_tw.png\" title=\"twitter\"></a>";
            }
            if($default['de_sns_instagram']) { 
                $mb_sns.="<a href=\"{$default['de_sns_instagram']}\" target=\"_blank\" class=\"sns_in\"><img src=\"".base_url()."theme/img/sns_in.png\" title=\"instagram\"></a>";
            }
            if($default['de_sns_pinterest']) { 
                $mb_sns.="<a href=\"{$default['de_sns_pinterest']}\" target=\"_blank\" class=\"sns_pi\"><img src=\"".base_url()."theme/img/sns_pi.png\" title=\"pinterest\"></a>";
            }
            if($default['de_sns_naverblog']) { 
                $mb_sns.="<a href=\"{$default['de_sns_naverblog']}\" target=\"_blank\" class=\"sns_bl\"><img src=\"".base_url()."theme/img/sns_bl.png\" title=\"naverblog\"></a>";
            }
            if($default['de_sns_naverband']) {
                $mb_sns.="<a href=\"{$default['de_sns_naverband']}\" target=\"_blank\" class=\"sns_ba\"><img src=\"".base_url()."theme/img/sns_ba.png\" title=\"naverband\"></a>";
            }
            if($default['de_sns_kakaotalk']) { 
                $mb_sns.="<a href=\"{$default['de_sns_kakaotalk']}\" target=\"_blank\" class=\"sns_kt\"><img src=\"".base_url()."theme/img/sns_kt.png\" title=\"kakaotalk\"></a>";
            }
            if($default['de_sns_kakaostory']) { 
                $mb_sns.="<a href=\"{$default['de_sns_kakaostory']}\" target=\"_blank\" class=\"sns_ks\"><img src=\"".base_url()."theme/img/sns_ks.png\" title=\"kakaostory\"></a>";
            }
            return $mb_sns;
        }
        public function mb_footer_sub(){
            $this->ci->load->view('mb/template/footer_sub');
        }
}