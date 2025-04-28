<?=$headTitle?>
<script src="<?php echo base_url(); ?>js/colorpicker.js"></script>

<form name="fbanner" id="fbanner" action="<?=base_url()?>AdminProcess/banner_form_update" method="post" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="w" value="<?php echo $w; ?>">	
    <input type="hidden" name="sca"  value="<?php echo $sca; ?>">
    <input type="hidden" name="page"  value="<?php echo $page; ?>">
    <input type="hidden" name="bn_id" value="<?php echo $bn_id; ?>">
    <input type="hidden" name="token" value="">
    <div class="tbl_frm02">
        <table>
            <colgroup>
                <col class="w140">
                <col> 
            </colgroup>
            <tbody>
            <tr>
                <th scope="row">노출위치</th>
                <td>
                    <select name="bn_code" onchange="chk_js_position(this.value);">
                        <?=$positionTag?>
                    </select>
                    <?=$fixTag?>
                </td>
	        </tr>
	        <tr>
                <th scope="row">배너파일</th>
                    <td>
                        <input type="file" name="bn_file" id="bn_file">
                        <?=$bimg_strTag?>
                    </td>
	            </tr>
                <tr>
                    <th scope="row">링크주소</th>
                    <td>
                        <input type="text" name="bn_link" value="<?php echo $bn['bn_link']; ?>" class="frm_input" size="40">
                        <select name="bn_target">
                            <?php echo $selfTag; ?>
                            <?php echo $blankTag; ?>
                        </select>
                        <?php echo $innerHelp; ?>
                        <?php echo $outerHelp; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">가로사이즈</th>
                    <td><input type="text" name="bn_width" value="<?php echo $bn['bn_width']; ?>" class="frm_input" size="7"> px</td>
                </tr>
                <tr>
                    <th scope="row">세로사이즈</th>
                    <td><input type="text" name="bn_height" value="<?php echo $bn['bn_height']; ?>" class="frm_input" size="7"> px</td>
                </tr>
                <tr>
                    <th scope="row">백그라운드 색상</th>
                    <td>
                        <input type="text" name="bn_bg" value="<?php echo $bn['bn_bg']; ?>" id="bn_bg" class="frm_input" size="7" maxlength="6"> 
                        <?=$colorHelp?>
                        <script>
                        $(function() {
                            $('#bn_bg').ColorPicker({
                                onSubmit: function(hsb, hex, rgb, el) {
                                    $(el).val(hex);
                                    $(el).ColorPickerHide();
                                },
                                onBeforeShow: function () {
                                    $(this).ColorPickerSetColor(this.value);
                                }
                            })
                            .bind('keyup', function(){
                                $(this).ColorPickerSetColor(this.value);
                            });
                        });
                        </script>
                    </td>
                </tr>
                <tr>
                    <th scope="row">배너문구</th>
                    <td>
                        <input type="text" name="bn_text" value="<?php echo $bn['bn_text']; ?>" class="frm_input" size="40">
                        <?php echo $bannerHelp; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">순서</th>
                    <td>
                        <input type="text" name="bn_order" value="<?php echo $bn['bn_order']; ?>" class="frm_input" size="7"> 숫자가 작을수록 우선 순위로 노출 됩니다.
                        <?php echo $rollingHelp; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">노출여부</th>
                    <td>
                        <input type="checkbox" name="bn_use" value="1" id="bn_use_yes" <?php echo $bn_checkTag; ?>> <label for="bn_use_yes">노출함</label>
                    </td>
                </tr>
            </tbody>
	    </table>
    </div>
    <?php echo $frm_submit; ?>
</form>

<script>
function chk_js_position(val) { 
    switch(val){
		<?=$colorJs ?>
		default:
			$("input[name=bn_width]").val('<?php echo $position[0][1]; ?>');
			$("input[name=bn_height]").val('<?php echo $position[0][2]; ?>');
			break;
	}
}
</script>
<?=$headTitleEnd?>