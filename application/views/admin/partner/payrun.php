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
                            <?php echo $sflArr['id']; ?>
                            <?php echo $sflArr['name']; ?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">기간검색</th>
                    <td>
                        <select name="spt">					
                            <?php echo $sptArr['time']; ?>
                            <?php echo $sptArr['term']; ?>
                        </select>
                        <?php echo $get_search_date; ?>
                    </td>				
                </tr>
                <tr>
                    <th scope="row">레벨검색</th>
                    <td>				
                        <?php echo $get_search_level; ?>
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
<form name="fpayrun" id="fpayrun" method="post" action="<?=base_url()?>AdminProcess/pt_payrunupdate" onsubmit="return fpayrun_submit(this);">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <div class="local_ov mart30">
        전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 건 조회
        <strong class="ov_a">총 출금요청액 <?php echo number_format($stotal_pay); ?>원 </strong>
    </div>
    <div class="local_frm01">
        <?php echo $btn_frmline; ?>
    </div>
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col class="w50">
                <col class="w130">
                <col class="w130">
                <col class="w130">
                <col class="w130">
                <col class="w80">
                <col class="w100">
                <col class="w100">
                <col class="w100">
                <col class="w100">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col"><?php echo $bName; ?>회원명</a></th>
                    <th scope="col"><?php echo $aMb_id; ?>아이디</a></th>
                    <th scope="col"><?php echo $bGrade; ?>레벨</a></th>
                    <th scope="col"><?php echo $aReg_time; ?>신청일시</a></th>
                    <th scope="col"><?php echo $bTerm_date; ?>만료일</a></th>
                    <th scope="col" class="th_bg"><?php echo $bPay; ?>현재잔액</a></th>
                    <th scope="col" class="th_bg"><?php echo $aBalance; ?>출금요청</a></th>
                    <th scope="col" class="th_bg"><?php echo $aPaytax; ?>세액공제</a></th>
                    <th scope="col" class="th_bg"><?php echo $aPaynet; ?>실수령액</a></th>
                    <th scope="col">회원입금계좌</th>
                </tr>
            </thead>
            <?=$payrunTable?>
            </tbody>
	    </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="desc02">
			<p>ㆍ정산완료를 하실 경우 DB상에 데이터값만 변경되므로 실제 본사 은행계좌에서 인출되지는 않습니다.</p>
			<p>ㆍ엑셀저장 후 인터넷뱅킹을 통한 대량 이체가 가능하므로 이체 완료 후 정산완료 하시면 됩니다.</p>
			<p class="fc_red">ㆍ정산완료를 실수로 처리하셨다면 가맹점 수수료내역에서 "선택삭제"를 통한 복원이 가능합니다.</p>
		</div>
	</div>
</div>
<script>
function fpayrun_submit(f){
    if(!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택정산") {
        if(!confirm("선택한 자료를 정산하시겠습니까?")) {
            return false;
        }
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?  \n삭제한 자료는 복구가 불가능합니다.")) {
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