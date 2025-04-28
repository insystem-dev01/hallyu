<?=$jqueryUiDatePick?>
<?=$headTitle?>
    <h2>기본검색</h2>
    <form name="fsearch" id="fsearch" method="get">
        <input type="hidden" name="code" value="<?php echo $code; ?>">
        <div class="tbl_frm01">
            <table>
                <colgroup>
                    <col class="w100">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">검색어</th>
                        <td>
                            <select name="sfl">
                                <?=$selectOpt[0]?>
                                <?=$selectOpt[1]?>
                                <?=$selectOpt[2]?>
                                <?=$selectOpt[3]?>
                                <?=$selectOpt[4]?>
                            </select>
                            <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">기간검색</th>
                        <td>
                            <select name="spt">
                                <?=$sptOpt[0]?>
                                <?=$sptOpt[1]?>
                            </select>
                            <?=$timeSearch?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">레벨검색</th>
                        <td>
                            <?=$levelSearch?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="btn_confirm">
            <input type="submit" value="검색" class="btn_medium">
            <input type="button" value="초기화" id="frmRest" class="btn_medium grey">
        </div>
    </form>
    <div class="local_ov mart30">
        총 회원수 : <b class="fc_red"><?php echo number_format($total_count); ?></b>명
    </div>
    <div class="local_frm01">
        <?php echo $btn_frmline; ?>
    </div>
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col class="w50">
                <col class="w130">
                <col class="w150">
                <col class="w90">
                <col class="w150">
                <col class="w150">
                <col class="w150">
                <col class="w130">
                <col class="w100">
                <col class="w130">
                <col class="w60">
                <col class="w60">
                <col class="w60">
                <?=$tableIsIntro[0]?>
                <col class="w90">
                <col class="w90">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col"><?php echo $name;?>회원명</a></th>
                    <th scope="col"><?php echo $id; ?>아이디</a></th>
                    <th scope="col">포로필</th>
                    <th scope="col"><?php echo $grade; ?>레벨</a></th>
                    <th scope="col"><?php echo $business_cert_image; ?>사업자등록증</a></th>
                    <th scope="col"><?php echo $bank_account_image; ?>통장사본</a></th>
                    <th scope="col"><?php echo $recom_id; ?>추천인</a></th>
                    <th scope="col">핸드폰</th>
                    <th scope="col"><?php echo $reg_time; ?>가입일시</a></th>
                    <th scope="col">구매수</th>
                    <th scope="col"><?php echo $login_sum; ?>로그인</a></th>
                    <th scope="col"><?php echo $intercept_date; ?>접근차단</a></th>
                    <?=$tableIsIntro[1]?>
                    <th scope="col"><?php echo $point; ?>포인트</a></th>
                    <th scope="col"><?php echo $mileage; ?>예치금</a></th>
                </tr>
            </thead>
            <?=$mebmerList?>
            </tbody>
        </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
    <?=$pagination?>
<?=$headTitleEnd?>
<script>
function chk_use_app(mb_id) {
    var base_url=document.getElementById('base_url').value;
	var error = "";
	var token = get_ajax_token();
	if(!token) {
		alert("토큰 정보가 올바르지 않습니다.");
		return false;
	}

	$.ajax({
		url: base_url+"MypageProcess/member_use_app",
		type: "POST",
		data: {"mb_id": mb_id, "token": token },
		dataType: "json",
		async: false,
		cache: false,
		success: function(data, textStatus) {
			error = data.error;
		}
	});

	if(error) {
		alert(error);
		return false;
	}
}

$(function(){
	// 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>
