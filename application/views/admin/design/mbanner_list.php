<?=$headTitle?>

<h2>코드검색</h2>
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
                    <th scope="row">노출위치</th>
                    <td>
                        <select name="sca">
                            <?=$positionTag?>
                        </select>
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
<form name="fmbannerlist" id="fmbannerlist" method="post" action="<?=base_url()?>AdminProcess/mbanner_list_update" onsubmit="return fmbannerlist_submit(this);">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    
    <h2>모바일스킨 (<?php echo $super['mobile_theme']; ?>) 배너목록</h2>
    <div class="local_ov">
        전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 건 조회
        <span class="ov_a fc_red">순서는 숫자가 작을수록 우선 순위로 노출되며 롤링 및 연속배너에만 적용됩니다. 고정배너는 의미없음</span>
    </div>
    <div class="local_frm01">
        <?php echo $btn_frmline; ?>
    </div>
    <div class="tbl_head01">
        <table class="tablef">
            <colgroup>
                <col class="w50">
                <col class="w50">
                <col class="w50">
                <col class="w60">
                <col>
                <col>
                <col class="w80">
                <col class="w80">
                <col class="w80">
                <col class="w60">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col" rowspan="2"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col" rowspan="2"><?php echo $bn_codeTag; ?>코드</a></th>
                    <th scope="col" rowspan="2"><?php echo $bn_useTag; ?>노출</a></th>
                    <th scope="col" rowspan="2"><?php echo $bn_orderTag; ?>순서</a></th>
                    <th scope="col">노출위치</th>
                    <th scope="col">링크주소</th>	
                    <th scope="col">TARGET</th>	
                    <th scope="col">가로사이즈</th>	
                    <th scope="col">세로사이즈</th>
                    <th scope="col">관리</th>
                </tr>
                <tr class="rows">
                    <th scope="col" colspan="6">이미지</th>
                </tr>
            </thead>
            <tbody>
            <?=$mBanner_listTag?>
            </tbody>
        </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<script>
function fmbannerlist_submit(f){
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
    $(".sbn_img_view").click(function(){
        var $con = $(this).closest(".td_img_view").find(".sbn_image");
        if($con.is(":visible")) {
            $con.slideUp("fast");
            $(this).text("이미지보기");
        } else {
            $con.slideDown("fast");
            $(this).text("이미지닫기");
        }
    });

	// 모두보기
    $(".sbn_all_view").click(function(){
        $(".sbn_image").slideDown("fast");
		$(".sbn_img_view").text("이미지닫기");
    });
	
	// 모두닫기
    $(".sbn_all_close").click(function(){
        $(".sbn_image").slideUp("fast");
		$(".sbn_img_view").text("이미지보기");
    });
});
</script>

<?=$headTitleEnd?>