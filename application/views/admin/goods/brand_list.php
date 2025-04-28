<?=$headTitle?>

<h2>브랜드 등록</h2>
<form name="fbrandlist2" id="fbrandlist2" action="<?=base_url()?>AdminProcess/goods_brand_form_update" method="post" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w100">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">브랜드명 (KOR)</th>
                    <td>
                        <input type="text" name="br_name" required itemname="브랜드명 (KOR)" class="required frm_input w200">
                        <span class="fc_197">예시) 아르마니 익스체인지</span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">브랜드명 (ENG)</th>
                    <td>
                        <input type="text" name="br_name_eng" class="frm_input w200">
                        <span class="fc_197">예시) Armani Exchange</span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">브랜드로고</th>
                    <td>
                        <input type="file" name="br_logo" id="br_logo">
                        <span class="fc_197">사이즈(128픽셀 * 40픽셀)</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="추가" class="btn_medium red">
    </div>
</form>

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
                            <?php echo $sflArr[0]; ?>
                            <?php echo $sflArr[1]; ?>
                            <?php echo $sflArr[2]; ?>
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
<form name="fbrandlist" id="fbrandlist" method="post" action="<?=base_url()?>AdminProcess/goods_brand_list_update" onsubmit="return fbrandlist_submit(this);">
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
                <col>
                <col>
                <col class="w130">
                <col class="w80">
                <col class="w110">
                <col class="w60">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col">번호</th>
                    <th scope="col"><?php echo $brNameTag; ?>브랜드명 (KOR)</a></th>
                    <th scope="col"><?php echo $brNameEngTag; ?>브랜드명 (ENG)</a></th>
                    <th scope="col"><?php echo $mb_idTag; ?>회원ID</a></th>
                    <th scope="col">상품수</th>
                    <th scope="col">바로가기</th>
                    <th scope="col">관리</th>
                </tr>
            </thead>
            <?=$brandListTable?>
            </tbody>
        </table>
    </div>
    <div class="local_frm02">
        <?php echo $btn_frmline; ?>
    </div>
</form>
<?=$pagination?>
<script>
function fbrandlist_submit(f){
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
<?=$headTitleEnd?>