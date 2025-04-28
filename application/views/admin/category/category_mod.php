<form name="fcgyform" method="post" action="<?=base_url()?>AdminProcess/category_mod" enctype="MULTIPART/FORM-DATA">
<input type="hidden" name="mod_type" value="u">
<input type="hidden" name="index_no" value="<?php echo $ca_no; ?>">
<input type="hidden" name="upcate" value="<?php echo $ca['catecode']; ?>">

<div class="tbl_frm02 mart10">
	<table>
        <colgroup>
            <col class="w140">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <th scope="row">카테고리 소속</th>
                <td class="bold"><?php echo $adm_category_navi; ?></td>
            </tr>
            <tr>
                <th scope="row">카테고리명</th>
                <td>
                    <input type="text" name="catename" value="<?php echo $ca['catename']; ?>" required itemname="카테고리명" class="frm_input required" size="50">
                    <input type="checkbox" name="cateuse" value="1" id="cateuse"<?php echo ($ca['cateuse'])?" checked='checked'":""; ?>> <label for="cateuse">카테고리 숨김</label>
                </td>
            </tr>
            <?=$categoryBan?>
        </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="확인" class="btn_lsmall">
        <button type="button" onClick="cancel('<?php echo $ca_no; ?>')" class="btn_lsmall bx-white">닫기</button>
    </div>
</form>
<script>
function cancel(no){
	parent.document.all['co'+no].style.display='none';
}
</script>