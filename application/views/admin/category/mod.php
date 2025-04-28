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
                    <td class="bold"><?php echo $catecode; ?></td>
                </tr>
                <tr>
                    <th scope="row">카테고리명</th>
                    <td>
                        <input type="text" name="catename" value="<?php echo $ca['catename']; ?>" required itemname="카테고리명" class="frm_input required" size="50">
                        <input type="checkbox" name="cateuse" value="1" id="cateuse"<?php echo $checked; ?>> <label for="cateuse">카테고리 숨김</label>
                    </td>
                </tr>
                <?php /* ?>
                <tr>
                    <th scope="row">카테고리 아이콘</th>
                    <td>
                        <input type="file" name="cateimg1">
                        <?php
                        $mimg_str = "";
                        $mimg = $srcfile.'/'.$ca['cateimg1'];
                        if(is_file($mimg) && $ca['cateimg1']) {
                            $size = @getimagesize($mimg);
                            if($size[0] && $size[0] > 300)
                                $width = 300;
                            else
                                $width = $size[0];

                            $mimg = rpc($mimg, OM_PATH, OM_URL);

                            echo '<input type="checkbox" name="cateimg1_del" value="'.$ca['cateimg1'].'" id="cateimg1_del"> <label for="cateimg1_del">삭제</label>';
                            $mimg_str = '<img src="'.$mimg.'" width="'.$width.'">';
                        }
                        if($mimg_str) {
                            echo '<div class="banner_or_img">'.$mimg_str.'</div>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">카테고리 아이콘 (ON)</th>
                    <td>
                        <input type="file" name="cateimg2">
                        <?php
                        $timg_str = "";
                        $timg = $srcfile.'/'.$ca['cateimg2'];
                        if(is_file($timg) && $ca['cateimg2']) {
                            $size = @getimagesize($timg);
                            if($size[0] && $size[0] > 300)
                                $width = 300;
                            else
                                $width = $size[0];

                            $timg = rpc($timg, OM_PATH, OM_URL);

                            echo '<input type="checkbox" name="cateimg2_del" value="'.$ca['cateimg2'].'" id="cateimg2_del"> <label for="cateimg2_del">삭제</label>';
                            $timg_str = '<img src="'.$timg.'" width="'.$width.'">';
                        }
                        if($timg_str) {
                            echo '<div class="banner_or_img">'.$timg_str.'</div>';
                        }
                        ?>
                    </td>
                </tr>
                <?php */ ?>
                <?=$catecodeThree?>
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