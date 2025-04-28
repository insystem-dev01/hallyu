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
                            <?php echo $opt['id']; ?>
                            <?php echo $opt['name']; ?>
                            <?php echo $opt['pt_id']; ?>
                            <?php echo $opt['deposit_name']; ?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">기간검색</th>
                    <td>
                        <select name="spt">						
                            <?php echo $opt['reg_time']; ?>
                            <?php echo $opt['anew_date'] ?>
                        </select>
                        <?php echo $duringStr; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">신청등급</th>
                    <td>				
                        <?php echo $searchLevel; ?>
                    </td>				
                </tr>
                <tr>
                    <th scope="row">승인상태</th>
                    <td>
                        <?php echo $radioChk['all']; ?>
                        <?php echo $radioChk['wait']; ?>
                        <?php echo $radioChk['complete']; ?>
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
<form name="fanewlist" id="fanewlist" method="post" action="<?=base_url()?>AdminProcess/pt_anewlistupdate" onsubmit="return fanewlist_submit(this);">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <div class="local_ov mart30">
        전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 명 조회
        <strong class="ov_a">총 결제금액 <?php echo number_format($stotal_price); ?>원 </strong>
    </div>
    <div class="local_frm01">
        <?php echo $btn_frmline; ?>
    </div>
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col class="w50">
                <col class="w50">
                <col class="w130">
                <col class="w130">
                <col class="w130">
                <col class="w130">
                <col class="w100">
                <col class="w130">
                <col class="w80">
                <col>		
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col" class="th_bg"><?php echo $aState; ?>승인</a></th>
                    <th scope="col"><?php echo $bName; ?>회원명</a></th>
                    <th scope="col"><?php echo $aMb_id; ?>아이디</a></th>			
                    <th scope="col"><?php echo $aPt_id; ?>추천인</a></th>
                    <th scope="col" class="th_bg"><?php echo $aAnew_grade; ?>신청등급</a></th>
                    <th scope="col" class="th_bg"><?php echo $aReceipt_price; ?>결제금액</a></th>
                    <th scope="col"><?php echo $aReg_time; ?>신청일시</a></th>
                    <th scope="col"><?php echo $bAnew_grade; ?>등업일</a></th>
                    <th scope="col">본사 입금계좌 및 전달사항</th>	
                </tr>
            </thead>
            <?=$anewlistTable?>
            </tbody>
	    </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>

<script>
function fanewlist_submit(f){
    if(!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
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
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>
<?=$headTitleEnd?>