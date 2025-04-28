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
                            <?php echo $sflArr['id']; ?>
                            <?php echo $sflArr['name']; ?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">기간검색</th>
                    <td>
                        <?php echo $get_search_date; ?>
                    </td>				
                </tr>
                <tr>
                    <th scope="row">구분</th>
                    <td>
                        <?php echo $rel_fieldArr['all']; ?>
                        <?php echo $rel_fieldArr['sale']; ?>
                        <?php echo $rel_fieldArr['anew']; ?>
                        <?php echo $rel_fieldArr['visit']; ?>
                        <?php echo $rel_fieldArr['passive']; ?>
                        <?php echo $rel_fieldArr['payment']; ?>
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
<form name="fpayhistory" id="fpayhistory" method="post" action="<?=base_url()?>AdminProcess/pt_payhistorydelete" onsubmit="return fpayhistory_submit(this);">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <div class="local_ov mart30">
        전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 건 조회
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
                <col>			
                <col class="w130">		
                <col class="w90">
                <col class="w90">
                <col class="w90">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col">번호</th>
                    <th scope="col"><?php echo $bName;?>회원명</a></th>
                    <th scope="col"><?php echo $aMb_id; ?>아이디</a></th>
                    <th scope="col"><?php echo $bGrade; ?>레벨</a></th>
                    <th scope="col"><?php echo $aPp_content; ?>수수료내용</a></th>
                    <th scope="col"><?php echo $aPp_datetime; ?>일시</a></th>
                    <th scope="col" class="th_bg"><?php echo $aPp_rel_table; ?>구분</a></th>
                    <th scope="col" class="th_bg"><?php echo $aPp_pay; ?>수수료</a></th>
                    <th scope="col"><?php echo $aPp_balance; ?>수수료합</a></th>
                </tr>
            </thead>
            <?=$historyTable?>
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
			<p>ㆍ수수료가 음수인 내역을 삭제할 경우 수수료가 다시 복원처리됩니다.</p>
			<p>ㆍ수수료가 양수인 내역을 삭제할 경우 수수료가 다시 차감처리됩니다.</p>
		</div>
	</div>
</div>
<script>
function fpayhistory_submit(f){
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