<?=$headTitle?>
<form name="fbasicform" class="w964" method="post" action="<?=base_url()?>AdminProcess/pt_pbasicupdate">
    <input type="hidden" name="token" value="">
    <h2>등급별 기본설정</h2>
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col class="w60">
                <col>
                <col class="w130">
                <col class="w130">
                <col class="w130">
                <col class="w150">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">레벨</th>
                    <th scope="col">레벨명</th>	
                    <th scope="col">가맹점개설비</th>
                    <th scope="col">관리비</th>
                    <th scope="col">접속수수료(CPC)</th>			
                    <th scope="col">자동레벨업(누적수익)</th>
                </tr>
            </thead>
            <tbody class="list">
                <?=$pbasicTable?>
            </tbody>
	    </table>
    </div>
    <div class="local_cmd02">
        <i class="fa fa-exclamation-circle fs11 fc_084"></i>
        <strong>가맹점 등급안내</strong> : 6 ~ 2번 순으로 숫자가 작을수록 레벨이 높습니다.
        <button type="button" id="del_partner_basic_row" class="btn_small red">설정초기화</button>
    </div>
<script>
    $(function() {
        // 입력필드초기화
        $(document).on("click", "#del_partner_basic_row", function() {
            $("input[name^=gb_anew_price]").val(0);
            $("input[name^=gb_term_price]").val(0);
            $("input[name^=gb_visit_pay]").val(0);
            $("input[name^=gb_promotion]").val(0);
        });

        // 레벨명입력시 하위테이블 자동노출
        $(document).on("keyup", ".pb-name-cell", function() {
            var grade, seq;
            var $el_name = $("input[name^=gb_name]");
            var $el_no = $("input[name^=gb_no]");
            $el_name.each(function(index) {
                grade = $.trim($(this).val());
                seq = $.trim($el_no.eq(index).val());
                $(".grade_fld_"+seq).empty().text(grade);
            });
        });
    });
</script>
<?php echo $frm_submit; ?>
<h2>판매수수료 인센티브</h2>
<div class="tbl_frm01">
	<table>
        <colgroup>
            <col class="w140">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <th scope="row">적립조건</th>
                <td>   
                    <label for="pf_sale_benefit_type" class="sound_only">적립유형</label>
                    <select name="pf_sale_benefit_type" id="pf_sale_benefit_type">
                        <option value="0"<?=$typeArr[0]?>>[대상]이 판매시 수수료를 설정비율(%)로</option>
                        <option value="1"<?=$typeArr[1]?>>[대상]이 판매시 수수료를 설정금액(원)으로</option>
                    </select>	
                    <label for="pf_sale_benefit_dan" class="sound_only">적립단계</label>
                    <input type="text" name="pf_sale_benefit_dan" value="<?php echo $config['pf_sale_benefit_dan']; ?>" id="pf_sale_benefit_dan" class="frm_input" size="5"> UP 까지 적립
                </td>
            </tr>
            <tr>
                <th scope="row">인센티브</th>
                <td>                
                    <div class="sub_frm01">
                        <table class="tablef">
                            <colgroup>
                                <col class="w70">
                            </colgroup>
                            <thead>
                                <tr class="tr_alignc">
                                    <th scope="col">대상</th>
                                    <?=$incentiveStr?>
                                </tr>
                            </thead>
                            <tbody class="pf_sale_benefit_fld">
                                <?=$sale_danStr?>
                            </tbody>
                        </table>
                    </td>
                </td>
            </tr>
	    </tbody>
	</table>
</div>
<div class="local_cmd02">
	<i class="fa fa-exclamation-circle fs11 fc_084"></i>
	<strong>1UP 이란?</strong> : 고객이 상품구매시 판매수수료를 받는 직가맹점이 {1UP} 이며, 직가맹점의 추천인부터 {2UP} {3UP}....{좌동} 식으로 적립됩니다.
	<button type="button" id="del_sale_benefit_row" class="btn_small red">설정초기화</button>
</div>
<script>
$(function(){
    var base_url=document.getElementById('base_url').value;
	// 입력필드초기화
	$(document).on("click", "#del_sale_benefit_row", function() {
		$(".pf_sale_benefit_fld input[name^=pf_sale_benefit]").val(0);
	});

	$(document).on("keyup", "#pf_sale_benefit_dan", function() {
		var dan = $(this).val().replace(/[^0-9]/g,"");	
		$.post(
			base_url+"AdminProcess/ajax_sale_benefit",
			{ "dan": dan },
			function(data) {
				$(".pf_sale_benefit_fld").empty().html(data);
			}
		);
	});
});
</script>
<h2>추천수수료 인센티브</h2>
<div class="tbl_frm01">
	<table>
        <colgroup>
            <col class="w140">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <th scope="row">적립조건</th>
                <td> 
                    <label for="pf_anew_benefit_type" class="sound_only">적립유형</label>
                    <select name="pf_anew_benefit_type" id="pf_anew_benefit_type">
                        <option value="0"<?=$benefitType[0]?>>[대상]을 유치시 수수료를 설정비율(%)로</option>
                        <option value="1"<?=$benefitType[1]?>>[대상]을 유치시 수수료를 설정금액(원)으로</option>
                    </select>	
                    <label for="pf_anew_benefit_dan" class="sound_only">적립단계</label>
                    <input type="text" name="pf_anew_benefit_dan" value="<?php echo $config['pf_anew_benefit_dan']; ?>" id="pf_anew_benefit_dan" class="frm_input" size="5"> UP 까지 적립
                </td>
            </tr>
            <tr>
                <th scope="row">인센티브</th>
                <td>                
                    <div class="sub_frm01">
                        <table class="tablef">
                            <colgroup>
                                <col class="w70">
                            </colgroup>
                            <thead>
                                <tr class="tr_alignc">
                                    <th scope="col">대상</th>
                                    <?=$recomIncentiveStr?>
                                </tr>
                            </thead>
                            <tbody class="pf_anew_benefit_fld">
                                <?=$anew_danStr?>
                            </tbody>
				        </table>
                    </div>
		        </td>
        	</tr>	
        </tbody>
	</table>
</div>
<div class="local_cmd02 lh6">
	<i class="fa fa-exclamation-circle fs11 fc_084"></i>
	<strong>1UP 이란?</strong> : 가맹점 신규개설시 추천수수료를 받는 직가맹점이 {1UP} 이며, 직가맹점의 추천인부터 {2UP} {3UP}....{좌동} 식으로 적립됩니다.<br>
	<i class="fa fa-exclamation-circle fs11 fc_red"></i>
	<strong>주의하세요</strong> : 추천수수료는 별도 허가된 법적 라이센스없이 절대 사용해서는 안됩니다. 반드시 법적자문을 받으신 후 사용하셔야 합니다.
	<button type="button" id="del_anew_benefit_row" class="btn_small red">설정초기화</button>
</div>
<script>
$(function(){
    var base_url=document.getElementById('base_url').value;
	// 입력필드초기화
	$(document).on("click", "#del_anew_benefit_row", function() {
		$(".pf_anew_benefit_fld input[name^=pf_anew_benefit]").val(0);
	});

	$(document).on("keyup", "#pf_anew_benefit_dan", function() {
		var dan = $(this).val().replace(/[^0-9]/g,"");	
		$.post(
			base_url+"AdminProcess/ajax_anew_benefit",
			{ "dan": dan },
			function(data) {
				$(".pf_anew_benefit_fld").empty().html(data);
			}
		);
	});
});
</script>

<?php echo $frm_submit; ?>
<?=$headTitleEnd?>