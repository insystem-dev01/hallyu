<?=$headTitle?>
<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/goods_plan_form_update" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="pl_no" value="<?php echo $pl_no; ?>">
    <div class="tbl_frm02">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">기획전명</th>
                    <td><input type="text" name="pl_name" value="<?php echo $pl['pl_name']; ?>" required itemname="기획전명" class="frm_input required" size="50"></td>
                </tr>
                <?=$planUrl?>
                <tr>
                    <th scope="row">노출여부</th>
                    <td><input type="checkbox" name="pl_use" value="1" id="pl_use"<?php echo get_checked($pl['pl_use'], "1"); ?>> <label for="pl_use">노출함</label></td>
                </tr>
                <tr>
                    <th scope="row">관련상품코드</th>
                    <td>
                        <textarea name="pl_it_code" class="frm_input wfull" style="height:350px;resize:none;"><?php echo $pl['pl_it_code']; ?></textarea>
                        <?php echo $helpEnter; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">목록이미지</th>
                    <td>
                        <input type="file" name="pl_limg" id="pl_limg">
                        <?=$bimgTag ?>
                        <?=$helpSize?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">상단이미지</th>
                    <td>
                        <input type="file" name="pl_bimg" id="pl_bimg">
                        <?=$upimgTag?>
                        <?=$helpSize2?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">아이콘</th>
                    <td>
                        <input type="file" name="pl_icon" id="pl_icon">
                        <?=$upiconTag?>
                        <?=$helpSize3?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php echo $frm_submit; ?>
</form>
<?=$headTitleEnd?>