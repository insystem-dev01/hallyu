<?=$headTitle?>
<?=$jqueryUiDatePick?>
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
                            <?php echo $selectOpt['id']; ?>
                            <?php echo $selectOpt['name']; ?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">만료일</th>
                    <td>
                        <?php echo $deadlineTime; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">레벨검색</th>
                    <td>
                        <?php echo $levelSearch; ?>
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
<form name="fplist" id="fplist" method="post" action="<?=base_url()?>AdminProcess/pt_plistupdate" onsubmit="return fplist_submit(this);">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <div class="local_ov mart30">
        전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 명 조회
        <strong class="ov_a">총 수수료잔액 : <?php echo number_format($stotal_pay); ?>원 </strong>
    </div>
    <?=$pf_expire_use?>
    <div class="tbl_head02">
        <table id="partner_list">
            <colgroup>
                <col class="w50">
                <col class="w130">
                <col class="w130">
                <col class="w130">
                <col class="w80">
                <col>
                <col class="w100">
                <col class="w100">
                <col class="w100">
                <col class="w60">
                <col class="w60">
                <col class="w60">
                <col class="w60">
                <col class="w80">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col" rowspan="2"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col" rowspan="2"><?php echo $name; ?>회원명</a></th>
                    <th scope="col" rowspan="2"><?php echo $id; ?>아이디</a></th>
                    <th scope="col" rowspan="2"><?php echo $grade; ?>레벨</a></th>
                    <th scope="col" rowspan="2"><?php echo $term_date; ?>만료일</a></th>
                    <th scope="col" rowspan="2">개별도메인</th>
                    <th scope="col" colspan="3">수수료집계</th>
                    <th scope="col" colspan="4">접속자집계</th>
                    <th scope="col" rowspan="2">카테고리</th>
                </tr>
                <tr class="rows">
                    <th scope="col" class="th_bg2"><?php echo $pay;?>현재잔액</a></th>
                    <th scope="col" class="th_bg2">총적립액</th>
                    <th scope="col" class="th_bg2">총차감액</th>
                    <th scope="col" class="th_bg"><?php echo $vi_today; ?>오늘</a></th>
                    <th scope="col" class="th_bg"><?php echo $vi_yesterday; ?>어제</a></th>
                    <th scope="col" class="th_bg"><?php echo $vi_max; ?>최대</a></th>
                    <th scope="col" class="th_bg"><?php echo $vi_sum; ?>전체</a></th>
                </tr>
            </thead>
            <?=$plistTable?>
            </tbody>
	    </table>
    </div>
</form>
<?=$pagination?>
<h2>개별회원 수수료 증감 설정</h2>
<form name="fplist2" id="fplist2" method="post" action="<?=base_url()?>AdminProcess/pt_ppayupdate" autocomplete="off">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="token" value="">
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w120">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row"><label for="mb_id">회원아이디</label></th>
                    <td><input type="text" name="mb_id" value="<?php echo $mb_id; ?>" id="mb_id" required class="required frm_input"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="pp_content">수수료내용</label></th>
                    <td><input type="text" name="pp_content" id="pp_content" required class="required frm_input" size="60"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="pp_pay">수수료금액</label></th>
                    <td><input type="text" name="pp_pay" id="pp_pay" required class="required frm_input" size="10"> 원</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="수수료적용" class="btn_large red">
    </div>
</form>
<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="desc02">
			<p>ㆍ수수료를 적립할 경우 양수만 입력하시기 바랍니다. 예) 3000</p>
			<p>ㆍ수수료를 차감할 경우 음수도 포함해 입력하시기 바랍니다. 예) -3000</p>
			<p class="fc_red">ㆍ수수료 차감액이 현재 잔액보다 클경우 차감되지 않습니다.</p>
		</div>
	</div>
</div>

<script>
function fplist_submit(f){
    if(!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "기간연장") {
        if(f.expire_date.value == 0) {
			alert('연장하실 기간을 선택하세요.');
			f.expire_date.focus();
			return false;
		}

        if(!confirm("선택한 자료를 기간연장 하시겠습니까?")) {
            return false;
        }
    }

    return true;
}

$(function(){
	// 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99" });
});
</script>
<?=$headTitleEnd?>
