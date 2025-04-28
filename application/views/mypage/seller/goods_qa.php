<?=$snbTagStart?>
<?=$admin_head_sub?>
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
                    <select name="sst">
                        <option value="">구분</option>
                        <option value="상품"<?php echo $sstArr['goods']; ?>>상품</option>
                        <option value="배송"<?php echo $sstArr['deliver']; ?>>배송</option>
                        <option value="반품/환불/취소"<?php echo $sstArr['return']; ?>>반품/환불/취소</option>
                        <option value="교환/변경"<?php echo $sstArr['change']; ?>>교환/변경</option>
                        <option value="기타"<?php echo $sstArr['etc']; ?>>기타</option>
                    </select>
                    <select name="sfl">
                        <option value="iq_name"<?php echo $sflArr[0]; ?>>작성자명</option>
                        <option value="iq_email"<?php echo $sflArr[1]; ?>>작성자 이메일</option>
                        <option value="iq_hp"<?php echo $sflArr[2]; ?>>작성자 핸드폰</option>
                        <option value="iq_subject"<?php echo $sflArr[3]; ?>>제목</option>
                        <option value="iq_question"<?php echo $sflArr[4]; ?>>질문내용</option>
                        <option value="iq_answer"<?php echo $sflArr[5]; ?>>답변내용</option>
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
<form name="fqalist" id="fqalist" method="post" action="<?=base_url()?>MypageProcess/seller_goods_qa_delete" onsubmit="return fqalist_submit(this);">
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
                <col>
                <col class="w130">
                <col class="w80">
                <col class="w60">
                <col class="w60">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col">번호</th>
                    <th scope="col"><?php echo $iqTyTag; ?>구분</a></th>
                    <th scope="col"><?php echo $iqSubject; ?>제목</a></th>
                    <th scope="col"><?php echo $iqName; ?>작성자</a></th>
                    <th scope="col"><?php echo $iqTime; ?>작성일</a></th>
                    <th scope="col"><?php echo $iqReply; ?>답변</a></th>
                    <th scope="col">관리</th>
                </tr>
            </thead>
            <?=$goods_qaTable?>
            </tbody>
	    </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<script>
function fqalist_submit(f){
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
</script>
<?=$admin_footer_sub?>
<?=$snbTagEnd?>