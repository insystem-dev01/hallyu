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
                            <?php echo $company_name; ?>
                            <?php echo $seller_code; ?>
                            <?php echo $info_name; ?>
                            <?php echo $company_owner; ?>
                            <?php echo $mb_id; ?>
                            <?php echo $name; ?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">신청일</th>
                    <td>
                        <?php echo $get_search_date; ?>
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
<form name="fsellerlist" id="fsellerlist" method="post" action="<?=base_url()?>AdminProcess/seller_list_update" onsubmit="return fsellerlist_submit(this);">
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
                <col class="w50">
                <col class="w130">
                <col class="w130">
                <col class="w80">
                <col>
                <col class="w130"> <!-- 사업자등록증 -->
                <col class="w130">
                <col class="w100">
                <col class="w130">
                <col class="w130">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col"><?php echo $aState;?>승인</a></th>
                    <th scope="col"><?php echo $aSeller_open; ?>상품</a></th>
                    <th scope="col"><?php echo $bName; ?>회원명</a></th>
                    <th scope="col"><?php echo $aMb_id; ?>아이디</a></th>
                    <th scope="col"><?php echo $aSeller_code; ?>업체코드</a></th>
                    <th scope="col"><?php echo $aCompany_name; ?>입점사명</a></th>
                    <th scope="col">사업자등록증</th> <!-- 새로 추가 -->
                    <th scope="col">통장사본</th> <!-- 새로 추가 -->
                    <th scope="col">전화번호</th>
                    <th scope="col"><?php echo $aReg_time; ?>등록일시</a></th>
                    <th scope="col"><?php echo $aReg_time; ?>최근접속일시</a></th>
                </tr>
            </thead>
            <?=$sellerListTable?>
            </tbody>
        </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<script>
function fsellerlist_submit(f){
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