<?=$headTitle?>
<?=$jqueryUiDatePick?>
<form name="fregform" method="post" onsubmit="return fregform_submit(this);" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="sst" value="<?php echo $sst; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="fr_date" value="<?php echo $fr_date; ?>">
    <input type="hidden" name="to_date" value="<?php echo $to_date; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="cp_id" value="<?php echo $cp_id; ?>">
    <input type="hidden" name="cp_dlimit" value="<?php echo $coupon['cp_dlimit']; ?>">
    <input type="hidden" name="cp_dlevel" value="<?php echo $coupon['cp_dlevel']; ?>">
    <h2>쿠폰유형선택</h2>
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col>
                <col width="200px">
                <col width="120px">
                <col width="80px">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">쿠폰발급 유형</th>
                    <th scope="col">누적 다운로드 제한</th>
                    <th scope="col">레벨 다운로드 제한</th>
                    <th scope="col">발급주기</th>
                </tr>
            </thead>
            <tbody>
                <tr class="<?php echo $coupontType[0]; ?>">
                    <td class="tal">
                        <label><input type="radio" name="cp_type" value="0"<?php echo $couponCheck[0]; ?> onclick="chk_show('0');"> <b>발행 날짜 지정</b></label>
                        <span class="vam fc_197">(Ex : 11월 20일에만 5% 할인)</span>
                    </td>
                    <td>최대 <input type="text" name="cp_dlimit0" value="<?php echo $arr_dlimit[0]; ?>" class="frm_input w80"> 건 <a href="javascript:chk_obj('cp_dlimit0', '99999999999');" class="btn_small grey">무제한</a></td>
                    <td>
                        <?php echo $get_goods_level_select[0]; ?>
                    </td>
                    <td>자유</td>
                </tr>
                <tr class="<?php echo $coupontType[1]; ?>">
                    <td class="tal">
                        <label><input type="radio" name="cp_type" value="1"<?php echo $couponCheck[1]; ?> onclick="chk_show('1');"> <b>발행 시간/요일 지정</b></label>
                        <span class="vam fc_197">(Ex : 11월 한달 동안 매일/지정)</span>
                    </td>
                    <td>최대 <input type="text" name="cp_dlimit1" value="<?php echo $arr_dlimit[1]; ?>" class="frm_input w80"> 건 <a href="javascript:chk_obj('cp_dlimit1', '99999999999');" class="btn_small grey">무제한</a></td>
                    <td>
                        <?php echo $get_goods_level_select[1]; ?>
                    </td>
                    <td>자유</td>
                </tr>
                <tr class="<?php echo $coupontType[2];?>">
                    <td class="tal">
                        <label><input type="radio" name="cp_type" value="2"<?php echo $couponCheck[2]; ?> onclick="chk_show('2');"> <b>성별구분으로 발급</b></label>
                        <span class="vam fc_197">(Ex : 11월 20일에 여성고객에게)</span>
                    </td>
                    <td>최대 <input type="text" name="cp_dlimit2" value="<?php echo $arr_dlimit[2]; ?>" class="frm_input w80"> 건 <a href="javascript:chk_obj('cp_dlimit2', '99999999999');" class="btn_small grey">무제한</a></td>
                    <td>
                        <?php echo $get_goods_level_select[2]; ?>
                    </td>
                    <td>자유</td>
                </tr>
                <tr class="<?php echo $coupontType[3]; ?>">
                    <td class="tal">
                        <label><input type="radio" name="cp_type" value="3"<?php echo $couponCheck[3]; ?> onclick="chk_show('3');"> <b>회원 생일자 발급</b></label>
                        <span class="vam fc_197">(Ex : 행복하고 즐거운 생일쿠폰)</span>
                    </td>
                    <td>최대 <input type="text" name="cp_dlimit3" value="<?php echo $arr_dlimit[3]; ?>" class="frm_input w80"> 건 <a href="javascript:chk_obj('cp_dlimit3', '99999999999');" class="btn_small grey">무제한</a></td>
                    <td>
                        <?php echo $get_goods_level_select[3]; ?>
                    </td>
                    <td>1년에 한번</td>
                </tr>
                <tr class="<?php echo $coupontType[4]; ?>">
                    <td class="tal">
                        <label><input type="radio" name="cp_type" value="4"<?php echo $couponCheck[4]; ?> onclick="chk_show('4');"> <b>연령 구분으로 발급</b></label>
                        <span class="vam fc_197">(Ex : 11월 20일에 80년생부터)</span>
                    </td>
                    <td>최대 <input type="text" name="cp_dlimit4" value="<?php echo $arr_dlimit[4]; ?>" class="frm_input w80"> 건 <a href="javascript:chk_obj('cp_dlimit4', '99999999999');" class="btn_small grey">무제한</a></td>
                    <td>
                        <?php echo $get_goods_level_select[4]; ?>
                    </td>
                    <td>자유</td>
                </tr>
                <tr class="<?php echo $coupontType[5];?>">
                    <td class="tal">
                        <label><input type="radio" name="cp_type" value="5"<?php echo $couponCheck[5]; ?> onclick="chk_show('5');"> <b>신규회원가입 발급</b></label>
                        <span class="vam fc_197">(Ex : 11월 회원가입시 3,000원)</span>
                    </td>
                    <td colspan="2">신규회원 가입시 자동발급 (다운로드 쿠폰 아님)</td>
                    <td>최초 1회</td>
                </tr>
            </tbody>
	    </table>
    </div>
    
    <h2>필수입력사항</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <?=$couponUpdateTag?>
                <tr>
                    <th scope="row">쿠폰명</th>
                    <td>
                        <input type="text" name="cp_subject" value="<?php echo $cp_subjectTag; ?>" required itemname="쿠폰명" class="required frm_input w300">
                        <span class="fc_197 marl5">(Ex : 오픈기념쿠폰, 추석할인쿠폰)</span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">설명</th>
                    <td>
                        <input type="text" name="cp_explan" value="<?php echo $cp_explanTag; ?>" class="frm_input w300">
                        <span class="fc_197 marl5">(Ex : 특가이벤트! 여름상품 10% 할인쿠폰)</span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">사용여부</th>
                    <td class="td_label">
                        <label><input type="radio" name="cp_use" value="1"<?php echo $cp_useArr[1]; ?>> 사용</label>
                        <label><input type="radio" name="cp_use" value="0"<?php echo $cp_useArr[0]; ?>> 중지</label>
                    </td>
                </tr>
                <tr id="ids_download">
                    <th scope="row">쿠폰발급 시점</th>
                    <td>
                        <p>
                            <label><input type="radio" name="cp_download" value="0"<?php echo $cp_downArr[0];?>> 회원직접 다운로드</label>
                            <span class="fc_197 marl5">(상품상세정보에서 회원이 직접 쿠폰을 다운로드받습니다)</span>
                        </p>
                        <p class="mart3">
                            <label><input type="radio" name="cp_download" value="1"<?php echo $cp_downArr[1];?>> 주문완료 후 자동발급</label>
                            <span class="fc_197 marl5">(주문완료 즉시 쿠폰을 발급합니다. 입금대기 상태에서도 고객이 쿠폰을 받을 수 있습니다)</span>
                        </p>
                        <p class="mart3">
                            <label><input type="radio" name="cp_download" value="2"<?php echo $cp_downArr[2];?>> 주문완료 후 배송완료시에 자동발급</label>
                            <span class="fc_197 marl5">(주문상태가 배송완료로 변경되는 시점에 쿠폰을 발급합니다)</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">중복발급 여부</th>
                    <td>
                        <p>
                            <label><input type="radio" name="cp_overlap" value="0"<?php echo $cp_overlapArr[0]; ?>> 중복발급 안함</label>
                            <span class="fc_197 marl5">(한 고객에게 무조건 1회만 쿠폰을 발급합니다)</span>
                        </p>
                        <p class="mart3">
                            <label><input type="radio" name="cp_overlap" value="1"<?php echo $cp_overlapArr[1]; ?>> 중복발급 허용</label>
                            <span class="fc_197 marl5">(쿠폰을 사용한 후 다음번 주문시에도 같은 상품의 쿠폰다운로드를 허용합니다)</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">해택</th>
                    <td>
                        <p>
                            <input type="radio" name="cp_sale_type" value="0"<?php echo $cp_sale_typeArr[0]; ?>>
                            (상품 판매가 x 수량) 의
                            <input type="text" name="cp_sale_percent" value="<?php echo $coupon['cp_sale_percent']; ?>" numeric itemname="할인률" class="frm_input w80">
                            % 할인, 최대
                            <input type="text" name="cp_sale_amt_max" value="<?php echo $coupon['cp_sale_amt_max']; ?>" numeric itemname="최대 할인금액" class="frm_input w80"> 원
                        </p>
                        <p class="mart3">
                            <input type="radio" name="cp_sale_type" value="1"<?php echo $cp_sale_typeArr[1]; ?>>
                            <input type="text" name="cp_sale_amt" value="<?php echo $coupon['cp_sale_amt']; ?>" numeric itemname="할인금액" class="frm_input w80"> 원 할인
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">중복사용 여부</th>
                    <td>
                        <p>
                            <label><input type="radio" name="cp_dups" value="0"<?php echo $cp_dupsArr[0]; ?>> 이 쿠폰은 동일한 주문건에 중복해서 동시에 사용할 수 있습니다.</label>
                        </p>
                        <p class="mart3">
                            <label><input type="radio" name="cp_dups" value="1"<?php echo $cp_dupsArr[1]; ?>> 이 쿠폰은 동일한 주문건에 중복해서 동시에 사용할 수 없습니다.</label>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="ids_show">
        <h2>세부입력사항</h2>
        <div class="tbl_frm01">
            <table>
                <colgroup>
                    <col class="w180">
                    <col>
                </colgroup>
                <tbody>
                    <tr id="ids_date">
                        <th scope="row">쿠폰발행 기간</th>
                        <td>
                            <input type="text" name="cp_pub_sdate" value="<?php echo isset($coupon['cp_pub_sdate']) ? $coupon['cp_pub_sdate'] : ""; ?>" id="pub_sdate" class="frm_input w80"> <a href="javascript:chk_obj('cp_pub_sdate', '9999999999');" class="btn_small grey">무제한</a> ~
                            <input type="text" name="cp_pub_edate" value="<?php echo isset($coupon['cp_pub_edate']) ? $coupon['cp_pub_edate'] : ""; ?>" id="pub_edate" class="frm_input w80"> <a href="javascript:chk_obj('cp_pub_edate', '9999999999');" class="btn_small grey">무제한</a>
                        </td>
                    </tr>
                    <tr id="ids_birthday">
                        <th scope="row">쿠폰발행 기간 (생일)</th>
                        <td>
                            <input type="text" name="cp_pub_sday" value="<?php echo isset($coupon['cp_pub_sday']) ? $coupon['cp_pub_sday'] : ""; ?>" numeric itemname="쿠폰발행 기간 (생일)" class="frm_input w60"> 일 전부터 ~
                            <input type="text" name="cp_pub_eday" value="<?php echo isset($coupon['cp_pub_eday']) ? $coupon['cp_pub_eday'] : ""; ?>" numeric itemname="쿠폰발행 기간 (생일)" class="frm_input w60"> 일 이후까지
                            <span class="fc_197 marl5">(Ex : 3일 전부터 5일 후까지)</span>
                        </td>
                    </tr>
                    <tr id="ids_sex">
                        <th scope="row">성별</th>
                        <td class="td_label">
                            <label><input type="radio" name="cp_use_sex" <?php echo $cp_use_sexArr['all']; ?> value=""> 모든고객</label>
                            <label><input type="radio" name="cp_use_sex" <?php echo $cp_use_sexArr['F']; ?> value="F"> 여성고객</label>
                            <label><input type="radio" name="cp_use_sex" <?php echo $cp_use_sexArr['M']; ?> value="M"> 남성고객</label>
                        </td>
                    </tr>
                    <tr id="ids_age">
                        <th scope="row">연령대</th>
                        <td>
                            <input type="text" name="cp_use_sage" value="<?php echo isset($coupon['cp_use_sage']) ? $coupon['cp_use_sage'] : ""; ?>" maxlength="4" numeric itemname="연령대" class="frm_input w60"> 년생부터 ~
                            <input type="text" name="cp_use_eage" value="<?php echo isset($coupon['cp_use_eage']) ? $coupon['cp_use_eage'] : ""; ?>" maxlength="4" numeric itemname="연령대" class="frm_input w60"> 년생까지
                            <span class="fc_197 marl5">생년월일 앞 4자리만 입력 (Ex : <?php echo $OM_TIME_YEAR-5; ?>년생부터 <?php echo $OM_TIME_YEAR; ?>년생까지)</span>
                        </td>
                    </tr>
                    <tr id="ids_day">
                        <th scope="row">쿠폰발행 요일</th>
                        <td class="td_label">
                            <?=$yoilStr?>
                        </td>
                    </tr>
                    <tr id="ids_hour1">
                        <th scope="row" rowspan="3">발행시간 / 매수</th>
                        <td>
                            <input type="checkbox" name="cp_pub_1_use" value="1"<?php echo (isset($coupon['cp_pub_1_use'])) ? ' checked' : ''; ?>>
                            <input type="text" name="cp_pub_shour1" value="<?php echo isset($coupon['cp_pub_shour1']) ? $coupon['cp_pub_shour1'] : ""; ?>" maxlength="2" numeric itemname="발행시간" class="frm_input w30">
                            <a href="javascript:chk_obj('cp_pub_shour1', '99');" class="btn_small grey">무제한</a> 시 부터 ~
                            <input type="text" name="cp_pub_ehour1" value="<?php echo isset($coupon['cp_pub_ehour1']) ? $coupon['cp_pub_ehour1'] : ""; ?>" maxlength="2" numeric itemname="발행시간" class="frm_input w30">
                            <a href="javascript:chk_obj('cp_pub_ehour1', '99');" class="btn_small grey">무제한</a> 시 까지 /
                            <input type="text" name="cp_pub_1_cnt" value="<?php echo isset($coupon['cp_pub_1_cnt']) ? $coupon['cp_pub_1_cnt'] : ""; ?>" numeric itemname="매수" class="frm_input w70"> 매
                            <span class="fc_197 marl5">(Ex : 08시 ~ 12시까지 / 선착순 : 100매)</span>
                        </td>
                    </tr>
                    <tr id="ids_hour2">
                        <td>
                            <input type="checkbox" name="cp_pub_2_use" value="1"<?php echo (isset($coupon['cp_pub_2_use'])) ? ' checked' : ''; ?>>
                            <input type="text" name="cp_pub_shour2" value="<?php echo isset($coupon['cp_pub_shour2']) ? $coupon['cp_pub_shour2'] : ""; ?>" maxlength="2" numeric itemname="발행시간" class="frm_input w30">
                            <a href="javascript:chk_obj('cp_pub_shour2', '99');" class="btn_small grey">무제한</a> 시 부터 ~
                            <input type="text" name="cp_pub_ehour2" value="<?php echo isset($coupon['cp_pub_ehour2']) ? $coupon['cp_pub_ehour2'] : ""; ?>" maxlength="2" numeric itemname="발행시간" class="frm_input w30">
                            <a href="javascript:chk_obj('cp_pub_ehour2', '99');" class="btn_small grey">무제한</a> 시 까지 /
                            <input type="text" name="cp_pub_2_cnt" value="<?php echo isset($coupon['cp_pub_2_cnt']) ? $coupon['cp_pub_2_cnt'] : ""; ?>" numeric itemname="매수" class="frm_input w70"> 매
                            <span class="fc_197 marl5">(Ex : 14시 ~ 16시까지 / 선착순 : 100매)</span>
                        </td>
                    </tr>
                    <tr id="ids_hour3">
                        <td>
                            <input type="checkbox" name="cp_pub_3_use" value="1"<?php echo (isset($coupon['cp_pub_3_use'])) ? ' checked' : ''; ?>>
                            <input type="text" name="cp_pub_shour3" value="<?php echo isset($coupon['cp_pub_shour3']) ? $coupon['cp_pub_shour3'] : ""; ?>" maxlength="2" numeric itemname="발행시간" class="frm_input w30">
                            <a href="javascript:chk_obj('cp_pub_shour3', '99');" class="btn_small grey">무제한</a> 시 부터 ~
                            <input type="text" name="cp_pub_ehour3" value="<?php echo isset($coupon['cp_pub_ehour3']) ? $coupon['cp_pub_ehour3'] : ""; ?>" maxlength="2" numeric itemname="발행시간" class="frm_input w30">
                            <a href="javascript:chk_obj('cp_pub_ehour3', '99');" class="btn_small grey">무제한</a>  시 까지 /
                            <input type="text" name="cp_pub_3_cnt" value="<?php echo isset($coupon['cp_pub_3_cnt']) ? $coupon['cp_pub_3_cnt'] : ""; ?>" numeric itemname="매수" class="frm_input w70"> 매
                            <span class="fc_197 marl5">(Ex : 18시 ~ 20시까지 / 선착순 : 100매)</span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">쿠폰유효 기간</th>
                        <td class="td_label">
                            <div>
                                <label><input type="radio" name="cp_inv_type" value="0"<?php echo $cp_inv_typeArr[0]; ?> onclick="chk_inv_show('0');"> 시작일, 종료일 선택</label>
                                <label><input type="radio" name="cp_inv_type" value="1"<?php echo $cp_inv_typeArr[1]; ?> onclick="chk_inv_show('1');"> 발급일로부터 기간 제한</label>
                            </div>
                            <div id="ids_inv_type0" class="mart7">
                                <input type="text" name="cp_inv_sdate" value="<?php echo isset($coupon['cp_inv_sdate']) ? $coupon['cp_inv_sdate'] : ""; ?>" id="inv_sdate" class="frm_input w80"> <a href="javascript:chk_obj('cp_inv_sdate', '9999999999');" class="btn_small grey">무제한</a> ~
                                <input type="text" name="cp_inv_edate" value="<?php echo isset($coupon['cp_inv_edate']) ? $coupon['cp_inv_edate'] : "" ; ?>" id="inv_edate"  class="frm_input w80"> <a href="javascript:chk_obj('cp_inv_edate', '9999999999');" class="btn_small grey">무제한</a>

                                <input type="text" name="cp_inv_shour1" value="<?php echo isset($coupon['cp_inv_shour1']) ? $coupon['cp_inv_shour1'] : ""; ?>" maxlength="2" numeric itemname="유효시간" class="frm_input w30 marl10">
                                <a href="javascript:chk_obj('cp_inv_shour1', '99');" class="btn_small grey">무제한</a> 시 부터 ~
                                <input type="text" name="cp_inv_shour2" value="<?php echo isset($coupon['cp_inv_shour2']) ? $coupon['cp_inv_shour2'] : ""; ?>" maxlength="2" numeric itemname="유효시간" class="frm_input w30">
                                <a href="javascript:chk_obj('cp_inv_shour2', '99');" class="btn_small grey">무제한</a> 시 까지
                            </div>
                            <div id="ids_inv_type1" class="mart7">
                                쿠폰발급일로부터 <input type="text" name="cp_inv_day" value="<?php echo isset($coupon['cp_inv_day']) ? $coupon['cp_inv_day'] : ""; ?>" numeric itemname="발급일" class="frm_input w50"> 일까지 사용기간을 제한
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">금액제한</th>
                        <td>
                            {상품 판매가 x 수량}+{좌동}+{좌동}…이 <input type="text" name="cp_low_amt" value="<?php echo isset($coupon['cp_low_amt']) ? $coupon['cp_low_amt'] : ""; ?>" numeric itemname="금액제한" class="frm_input w80"> 원 이상이면 사용가능.<span class="fc_197 marl5">(Ex : 2만원이상 구매시)</span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">사용가능대상</th>
                        <td>
                            <p>
                                <label><input type="radio" name="cp_use_part" value="0"<?php echo $cp_use_partArr[0]; ?> onclick="chk_use_show('0');"> 전체상품에 쿠폰사용 가능</label>
                            </p>
                            <p class="mart3">
                                <label><input type="radio" name="cp_use_part" value="1"<?php echo $cp_use_partArr[1]; ?> onclick="chk_use_show('1');"> 일부 상품만 쿠폰사용 가능</label>
                            </p>
                            <p class="mart3">
                                <label><input type="radio" name="cp_use_part" value="2"<?php echo $cp_use_partArr[2]; ?> onclick="chk_use_show('2');"> 일부 카테고리만 쿠폰사용 가능</label>
                            </p>
                            <p class="mart3">
                                <label><input type="radio" name="cp_use_part" value="3"<?php echo $cp_use_partArr[3]; ?> onclick="chk_use_show('3');"> 일부 상품은 쿠폰사용 불가</label>
                            </p>
                            <p class="mart3">
                                <label><input type="radio" name="cp_use_part" value="4"<?php echo $cp_use_partArr[4]; ?> onclick="chk_use_show('4');"> 일부 카테고리는 쿠폰사용 불가</label>
                            </p>
                        </td>
                    </tr>
                    <tr id="ids_gpart">
                        <th scope="row">쿠폰<span id="g_title"></span> 상품</th>
                        <td>
                            <input type="hidden" name="cp_use_goods" id="cp_use_goods" value="<?php echo isset($coupon['cp_use_goods']) ? $coupon['cp_use_goods'] : ""; ?>">
                            <input type="hidden" name="cp_use_category" value="">
                            <p>
                                전체 : <b><?php echo number_format($cnt_use_goods); ?></b> 건
                                <a href="<?php echo base_url(); ?>admin/goods_coupon_goods" onclick="win_open(this,'cp_goods','850','800','yes'); return false;" class="btn_small red marl10">상품등록</a>
                            </p>
                            <ul id="ck_use_goods" class="chk_opli">
                                <?php echo $ck_use_goods; ?>
                            </ul>
                        </td>
                    </tr>
                    <tr id="ids_cpart">
                        <th scope="row">쿠폰<span id="c_title"></span> 카테고리</th>
                        <td>
                            <p>
                                <?php echo $get_goods_sca_select; ?>
                                <a href="javascript:chk_cate_add();" class="btn_small bx-white">저장</a>
                            </p>
                            <p class="mart5">
                                <select name="use_category_list" size="10" class="frm_input wfull h100 list1">
                                    <?=$categoryOpt?>
                                </select>
                            </p>
                            <p class="mart5">
                                <a href="javascript:chk_cate_move(document.fregform.use_category_list.selectedIndex, -1, document.fregform.use_category_list);" class="btn_small bx-white">위로</a>
                                <a href="javascript:chk_cate_move(document.fregform.use_category_list.selectedIndex, +1, document.fregform.use_category_list);" class="btn_small bx-white">아래로</a>
                                <a href="javascript:chk_cate_del();" class="btn_small bx-red">삭제</a>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo $frm_submit; ?>
</form>

<script language="javascript">
function fregform_submit(f) {
	var cp_type = false;
	var cp_use_part = false;
	var cp_week_day = false;
	var ck_id = "<?php echo $ck_join['cp_id']; ?>";
	var cp_id = "<?php echo $cp_id; ?>";
	var backcolor = '#FFE4E1';

	if(f.cp_type[5].checked == true && (ck_id && ck_id != cp_id)) {
		alert('신규회원가입 발급은 중복해서 등록하실 수 없습니다!');
		return false;
	}

	for(var i=0; i<f.elements.length; i++){
		if(f.elements[i].name == "cp_type" && f.elements[i].checked==true){
			cp_type = true;
		}

		if(f.elements[i].name == "cp_use_part" && f.elements[i].checked==true){
			cp_use_part = true;
		}

		if(f.elements[i].name == "cp_week_day[]" && f.elements[i].checked==true){
			cp_week_day = true;
		}
	}

	if(cp_type == false) {
		alert('쿠폰발급 유형을 선택하세요!');
		return false;
	}

	for(var i=0; i<f.cp_type.length - 1; i++){
		if(f.cp_type[i].checked==true && !eval("f.cp_dlimit"+i).value) {
			alert('누적제한 : 항목은 필수 입력입니다.');
			eval("f.cp_dlimit"+i).style.backgroundColor = backcolor;
			eval("f.cp_dlimit"+i).focus();
			return false;
		}
	}

	// 쿠폰발행 기간
	if(f.cp_type[3].checked == false){
		if(!f.cp_pub_sdate.value){
			alert('쿠폰발행 기간 : 항목은 필수 입력입니다.');
			f.cp_pub_sdate.style.backgroundColor = backcolor;
			f.cp_pub_sdate.focus();
			return false;
		}
		if(!f.cp_pub_edate.value){
			alert('쿠폰발행 기간 : 항목은 필수 입력입니다.');
			f.cp_pub_edate.style.backgroundColor = backcolor;
			f.cp_pub_edate.focus();
			return false;
		}
	} else {
		if(!f.cp_pub_sday.value){
			alert('쿠폰발행 기간 (생일) : 항목은 필수 입력입니다.');
			f.cp_pub_sday.style.backgroundColor = backcolor;
			f.cp_pub_sday.focus();
			return false;
		}
		if(!f.cp_pub_eday.value){
			alert('쿠폰발행 기간 (생일) : 항목은 필수 입력입니다.');
			f.cp_pub_eday.style.backgroundColor = backcolor;
			f.cp_pub_eday.focus();
			return false;
		}
	}

	// 발행 시간/요일 지정
	if(f.cp_type[4].checked == true) {
		if(!f.cp_use_sage.value){
			alert('연령대 : 항목은 필수 입력입니다.');
			f.cp_use_sage.style.backgroundColor = backcolor;
			f.cp_use_sage.focus();
			return false;
		}
		if(!f.cp_use_eage.value){
			alert('연령대 : 항목은 필수 입력입니다.');
			f.cp_use_eage.style.backgroundColor = backcolor;
			f.cp_use_eage.focus();
			return false;
		}
	}

	// 발행 시간/요일 지정
	if(f.cp_type[1].checked == true) {
		if(cp_week_day == false) {
			alert('쿠폰발행 요일 :  하나이상은 체크 하셔야 합니다.');
			return false;
		}

		if(f.cp_pub_1_use.checked == false && f.cp_pub_2_use.checked == false && f.cp_pub_3_use.checked == false){
			alert('발행시간 / 매수 : 하나이상은 체크 하셔야 합니다.');
			f.cp_pub_1_use.focus();
			return false;
		}

		for(var i=1; i<=3; i++) {
			if(eval("f.cp_pub_"+i+"_use").checked == true) {
				if(!eval("f.cp_pub_shour"+i).value){
					alert('발행시간 : 항목은 필수 입력입니다.');
					eval("f.cp_pub_shour"+i).style.backgroundColor = backcolor;
					eval("f.cp_pub_shour"+i).focus();
					return false;
				}
				if(!eval("f.cp_pub_ehour"+i).value){
					alert('발행시간 : 항목은 필수 입력입니다.');
					eval("f.cp_pub_ehour"+i).style.backgroundColor = backcolor;
					eval("f.cp_pub_ehour"+i).focus();
					return false;
				}
			}
		}
	}

	// 쿠폰유효 기간 (시작일, 종료일 선택)
	if(f.cp_inv_type[0].checked == true){
		if(!f.cp_inv_sdate.value){
			alert('쿠폰유효 기간 : 항목은 필수 입력입니다.');
			f.cp_inv_sdate.style.backgroundColor = backcolor;
			f.cp_inv_sdate.focus();
			return false;
		}
		if(!f.cp_inv_edate.value){
			alert('쿠폰유효 기간 : 항목은 필수 입력입니다.');
			f.cp_inv_edate.style.backgroundColor = backcolor;
			f.cp_inv_edate.focus();
			return false;
		}
		if(!f.cp_inv_shour1.value){
			alert('쿠폰유효 시간 : 항목은 필수 입력입니다.');
			f.cp_inv_shour1.style.backgroundColor = backcolor;
			f.cp_inv_shour1.focus();
			return false;
		}
		if(!f.cp_inv_shour2.value){
			alert('쿠폰유효 시간 : 항목은 필수 입력입니다.');
			f.cp_inv_shour2.style.backgroundColor = backcolor;
			f.cp_inv_shour2.focus();
			return false;
		}
	}

	// 쿠폰유효 기간 (발급일로부터 기간 제한)
	if(f.cp_inv_type[1].checked == true){
		if(!f.cp_inv_day.value){
			alert('쿠폰유효 기간 : 항목은 필수 입력입니다.');
			f.cp_inv_day.style.backgroundColor = backcolor;
			f.cp_inv_day.focus();
			return false;
		}
	}

	if(cp_use_part == false) {
		alert('사용가능대상을 선택하세요!');
		return false;
	}

	var tmp_str = '';
	var tmp_comma = '';
	for(var i=0;i<f.use_category_list.length;i++)
	{
		tmp_str += tmp_comma + f.use_category_list.options[i].value;
		tmp_comma = ',';
	}

	f.cp_use_category.value = tmp_str;

	//누적제한 , 레벨제한 최종 체크
	for(var i=0; i<f.cp_type.length - 1; i++){
		if(f.cp_type[i].checked==true && eval("f.cp_dlimit"+i).value) {
			f.cp_dlimit.value = eval("f.cp_dlimit"+i).value;
			f.cp_dlevel.value = eval("f.cp_dlevel"+i).value;
			break;
		}
	}
    var base_url=document.getElementById('base_url').value;
	f.action = base_url+"AdminProcess/goods_coupon_form_update";
    return true;
}

// 분류 추가
function chk_cate_add() {
	var f = document.fregform;
	if(!f.use_category.value) {
		alert('카테고리를 선택하세요.');
		f.use_category.focus();
		return;
	}

	var n = f.use_category_list.length;
	var k = f.use_category.options.selectedIndex;
	f.use_category_list.options[n] = new Option();
	f.use_category_list.options[n].text  = f.use_category.options[k].text;
	f.use_category_list.options[n].value = f.use_category.options[k].value;
}

// 분류 삭제
function chk_cate_del() {
	var f = document.fregform;
	var chk  = 0;

	for(var i = 0; i < f.use_category_list.length; i++) {
		if(f.use_category_list.options[i].selected == true) {
			f.use_category_list.options[i] = null;
			chk++;
			break;
		}
	}

	if(!chk) {
		alert('제외시킬 항목을 선택해 주세요.');
		return;
	}
}

// 뷴류 이동
function chk_cate_move(index,to,list) {
	var f = document.fregform;
	var total = list.options.length-1;
	var chk = 0;

	for(var i = 0; i < list.length; i++) {
		if(list.options[i].selected == true) {
			chk = 1;
			break;
		}
	}

	if(!chk) {
		alert('이동할 항목을 선택해 주세요.');
		return;
	}

	if(index == -1) return false;
	if(to == +1 && index == total) return false;
	if(to == -1 && index == 0) return false;

	var items = new Array;
	var values = new Array;

	for(i = total; i >= 0; i--)
	{
		items[i] = list.options[i].text;
		values[i] = list.options[i].value;
	}

	for(i = total; i >= 0; i--) {
		if(index == i) {
			list.options[i + to] = new Option(items[i],values[i], 0, 1);
			list.options[i] = new Option(items[i + to], values[i + to]);
			i--;
		} else {
			list.options[i] = new Option(items[i], values[i]);
		}
	}
}

// 상품 삭제
function chk_del(gs_id){
	var cp_use_goods = document.getElementById('cp_use_goods');  // 실제 입력 값
	var ck_use_table = document.getElementById('chk_'+gs_id);

	ck_use_table.style.display ='none';
	cp_use_goods.value = cp_use_goods.value.replace(gs_id,'');

	var tmp_str = '';
	var tmp_comma = '';
	var tmp_use_goods = cp_use_goods.value.split(',');
	for(var i=0;i<tmp_use_goods.length;i++)
	{
		if(tmp_use_goods[i]){
			tmp_str += tmp_comma + tmp_use_goods[i];
			tmp_comma = ',';
		}
	}

	cp_use_goods.value = tmp_str;
}

// 필드별로 무제한설정
function chk_obj(f_obj, num){
	var f = document.fregform;
	eval("f."+f_obj).value = num;
}

// 쿠폰발급 유형
function chk_show(n) {
	var f = document.fregform;
	switch(n){
		case '0': // 발행 날짜 지정
			eval("ids_show").style.display ='';
			eval("ids_download").style.display ='';
			eval("ids_sex").style.display ='none';
			eval("ids_age").style.display ='none';
			eval("ids_day").style.display ='none';
			eval("ids_hour1").style.display ='none';
			eval("ids_hour2").style.display ='none';
			eval("ids_hour3").style.display ='none';
			eval("ids_date").style.display ='';
			eval("ids_birthday").style.display ='none';
			break;
		case '1': // 발행 시간/요일 지정
			eval("ids_show").style.display ='';
			eval("ids_download").style.display ='';
			eval("ids_sex").style.display ='none';
			eval("ids_age").style.display ='none';
			eval("ids_day").style.display ='';
			eval("ids_hour1").style.display ='';
			eval("ids_hour2").style.display ='';
			eval("ids_hour3").style.display ='';
			eval("ids_date").style.display ='';
			eval("ids_birthday").style.display ='none';
			break;
		case '2': // 성별구분으로 발급
			eval("ids_show").style.display ='';
			eval("ids_download").style.display ='';
			eval("ids_sex").style.display ='';
			eval("ids_age").style.display ='none';
			eval("ids_day").style.display ='none';
			eval("ids_hour1").style.display ='none';
			eval("ids_hour2").style.display ='none';
			eval("ids_hour3").style.display ='none';
			eval("ids_date").style.display ='';
			eval("ids_birthday").style.display ='none';
			break;
		case '3': // 회원 생일자 발급
			eval("ids_show").style.display ='';
			eval("ids_download").style.display ='';
			eval("ids_sex").style.display ='none';
			eval("ids_age").style.display ='none';
			eval("ids_day").style.display ='none';
			eval("ids_hour1").style.display ='none';
			eval("ids_hour2").style.display ='none';
			eval("ids_hour3").style.display ='none';
			eval("ids_date").style.display ='none';
			eval("ids_birthday").style.display ='';
			break;
		case '4': // 연령 구분으로 발급
			eval("ids_show").style.display ='';
			eval("ids_download").style.display ='';
			eval("ids_sex").style.display ='none';
			eval("ids_age").style.display ='';
			eval("ids_day").style.display ='none';
			eval("ids_hour1").style.display ='none';
			eval("ids_hour2").style.display ='none';
			eval("ids_hour3").style.display ='none';
			eval("ids_date").style.display ='';
			eval("ids_birthday").style.display ='none';
			break;
		case '5': // 신규회원가입 발급
			eval("ids_show").style.display ='';
			eval("ids_download").style.display ='none';
			eval("ids_sex").style.display ='none';
			eval("ids_age").style.display ='none';
			eval("ids_day").style.display ='none';
			eval("ids_hour1").style.display ='none';
			eval("ids_hour2").style.display ='none';
			eval("ids_hour3").style.display ='none';
			eval("ids_date").style.display ='';
			eval("ids_birthday").style.display ='none';
			break;
		default:
			eval("ids_show").style.display ='none';
			break;
	}
}

// 쿠폰유효 기간 유형
function chk_inv_show(n) {
	var f = document.fregform;
	switch(n){
		case '0':
			eval("ids_inv_type0").style.display ='';
			eval("ids_inv_type1").style.display ='none';
			break;
		case '1':
			eval("ids_inv_type0").style.display ='none';
			eval("ids_inv_type1").style.display ='';
			break;
	}
}

// 쿠폰유효 기간 유형
function chk_use_show(n) {
	var f = document.fregform;
	switch(n){
		case '1':
			eval("ids_gpart").style.display ='';
			eval("ids_cpart").style.display ='none';
			eval("g_title").innerHTML ='&nbsp;적용';
			eval("c_title").innerHTML ='&nbsp;적용';
			break;
		case '2':
			eval("ids_gpart").style.display ='none';
			eval("ids_cpart").style.display ='';
			eval("g_title").innerHTML ='&nbsp;적용';
			eval("c_title").innerHTML ='&nbsp;적용';
			break;
		case '3':
			eval("ids_gpart").style.display ='';
			eval("ids_cpart").style.display ='none';
			eval("g_title").innerHTML ='&nbsp;예외';
			eval("c_title").innerHTML ='&nbsp;예외';
			break;
		case '4':
			eval("ids_gpart").style.display ='none';
			eval("ids_cpart").style.display ='';
			eval("g_title").innerHTML ='&nbsp;예외';
			eval("c_title").innerHTML ='&nbsp;예외';
			break;
		default:
			eval("ids_gpart").style.display ='none';
			eval("ids_cpart").style.display ='none';
			break;
	}
}

$(function(){
	// 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
	$("#pub_sdate,#pub_edate,#inv_sdate,#inv_edate").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99"});
});

chk_show("<?php echo $coupon['cp_type']; ?>");
chk_inv_show("<?php echo $coupon['cp_inv_type']; ?>");
chk_use_show("<?php echo $coupon['cp_use_part']; ?>");
</script>
<?=$headTitleEnd?>