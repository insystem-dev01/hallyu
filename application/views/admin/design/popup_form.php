<?=$jqueryUiDatePick?>
<?=$headTitle?>
<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/popup_form_update" onsubmit="return fregform_submit(this);">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="pp_id" value="<?php echo $pp_id; ?>">
    
    <div class="tbl_frm02">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">접속기기</th>
                    <td>				
                        <select name="device">
                            <?php echo $deviceArr[0]; ?>
                            <?php echo $deviceArr[1]; ?>
                            <?php echo $deviceArr[2]; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">팝업크기(pixel)</th>
                    <td>
                        <input type="text" name="width" value="<?php echo $nw['width']; ?>" required numeric itemname="팝업크기" class="required frm_input w80"> X <input type="text" name="height" value="<?php echo $nw['height']; ?>" required numeric itemname="팝업크기" class="required frm_input w80"></td>
                    </td>
                </tr>
                <tr>
                    <th scope="row">팝업위치(pixel)</th>
                    <td>
                        <input type="text" name="top" value="<?php echo $nw['top']; ?>" required numeric itemname="팝업위치" class="required frm_input w80"> X <input type="text" name="lefts" value="<?php echo $nw['lefts']; ?>" required numeric itemname="팝업위치" class="required frm_input w80"></td>
                    </td>
                </tr>
                <tr>
                    <th scope="row">실행기간</th>
                    <td>
                        <input type="text" autocomplete="off" name="begin_date" value="<?php echo $nw['begin_date']; ?>" id="begin_date" required itemname="실행기간" class="required frm_input w80"> ~
                        <input type="text" autocomplete="off" name="end_date" value="<?php echo $nw['end_date']; ?>" id="end_date" required itemname="실행기간" class="required frm_input w80">
                    </td>
                </tr>
                <tr>
                    <th scope="row">노출여부</th>
                    <td class="td_label">
                        <input type="radio" name="state" value="0" id="state_yes"<?php echo $stateArr[0]; ?>> <label for="state_yes">노출함</label>
                        <input type="radio" name="state" value="1" id="state_no"<?php echo $stateArr[1]; ?>> <label for="state_no">노출안함</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">팝업제목</th>
                    <td><input type="text" name="title" value="<?php echo $titleTag; ?>" required itemname="팝업 제목" class="required frm_input" size="80"></td>
                </tr>
                <tr>
                    <th scope="row">팝업내용</th>
                    <td>
                        <?php echo $editor_htmlTag; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
        <a href="<?=base_url()?>admin/design?code=popup_list<?php echo $qstr; ?>&page=<?php echo $page; ?>" class="btn_large bx-white">목록</a>
    </div>
</form>
<script>
function fregform_submit(f) {
	<?=$get_editor_js?>
    return true;
}

$(function(){
	// 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
	$("#begin_date,#end_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99"});
});
</script>
<?=$headTitleEnd?>