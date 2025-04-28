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
                    <select name="sca">
                        <option value="">FAQ카테고리</option>
                        <?=$faq_cateTag?>
                    </select>
			        <select name="sfl">
                        <?=$subjectTag?>
                        <?=$memoTag?>
                    </select>
                    <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
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
<form name="ffaqlist" id="ffaqlist" method="post" action="<?=base_url()?>AdminProcess/help_faq_delete" onsubmit="return ffaqlist_submit(this);">
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
                <col class="w100">
                <col>
                <col class="w80">
                <col class="w60">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col">번호</th>
                    <th scope="col">분류</th>
                    <th scope="col">제목</th>
                    <th scope="col">등록일</th>
                    <th scope="col">관리</th>
                </tr>
            </thead>
            <?=$help_faqList?>
            </tbody>
        </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<script>
function ffaqlist_submit(f){
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

var ubDisplay = {
      q_old : "",
      a_old : "",

      display : function(seq){
      var a_new = document.getElementById("view_"+seq);

      if(this.a_old != a_new){
         if(this.a_old != "")
             this.a_old.style.display = "none";
             a_new.style.display = "";
             this.a_old = a_new;
         }else{
             a_new.style.display = "none";
             this.a_old = "";
         }
       }
   };
</script>
<?=$headTitleEnd?>