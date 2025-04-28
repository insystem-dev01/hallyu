<?=$headTitle?>
<h2>브랜드 수정</h2>
<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/goods_brand_form_update" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="br_id" value="<?php echo $br_id; ?>">
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">브랜드명 (KOR)</th>
                    <td><input type="text" name="br_name" value="<?php echo $br['br_name']; ?>" required itemname="브랜드명 (KOR)" class="required frm_input w200"></td>
                </tr>
                <tr>
                    <th scope="row">브랜드명 (ENG)</th>
                    <td><input type="text" name="br_name_eng" value="<?php echo $br['br_name_eng']; ?>" class="frm_input w200"></td>
                </tr>
                <tr>
                    <th scope="row">브랜드 URL</th>
                    <td><input type="text" value="/shop/brandlist?br_id=<?php echo $br_id; ?>" readonly class="frm_input list1 w300"> <a href="<?=base_url()?>shop/brandlist?br_id=<?php echo $br_id; ?>" target="_blank" class="btn_small grey">브랜드 바로가기</a></td>
                </tr>
                <tr>
                    <th scope="row">브랜드로고</th>
                    <td>
                        <input type="file" name="br_logo" id="br_logo"> 사이즈(128픽셀 * 40픽셀)
                        <?=$brandLogo?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">등록일</th>
                    <td><?php echo $br['br_time']; ?></td>
                </tr>
                <?=$updateTag?>
            </tbody>
            </table>
        </div>
        <div class="btn_confirm">
            <input type="submit" value="저장" class="btn_large" accesskey="s">
            <a href="<?=base_url()?>admin/goods?code=brand_list<?php echo $qstr; ?>&page=<?php echo $page; ?>" class="btn_large bx-white">목록</a>
        </div>
</form>
<?=$headTitleEnd?>