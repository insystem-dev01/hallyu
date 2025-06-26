<h2 class="pop_title">
	<?php echo $title; ?>
	<a href="javascript:window.close();" class="btn_small bx-white">창닫기</a>
</h2>

<form name="forderreview" id="sit_review" method="post" action="<?php echo $form_action_url; ?>" onsubmit="return forderreview_submit(this);" enctype="multipart/form-data">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="gs_id" value="<?php echo $gs_id; ?>">
    <input type="hidden" name="seller_id" value="<?php echo $gs['mb_id']; ?>">
    <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
    <input type="hidden" name="od_no" value="<?php echo $od_no; ?>">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <table class="tbl_review">
        <colgroup>
            <col width="80px">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <td class="image"><?php echo $imageTag; ?></td>
                <td class="gname">
                    <?php echo $gsName; ?>
                    <p class="bold mart5"><?php echo $mbPrice; ?></p>
                </td>
            </tr>
        </tbody>
    </table>
    <h2 class="anc_tit mart20">구매후기 쓰기</h2>
    <div class="tbl_frm01 tbl_wrap review_write">
        <table>
            <colgroup>
                <col width="80px">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row"><label for="memo">내용</label></th>
                    <td><textarea name="memo" id="memo" class="frm_textbox wufll"><?php echo (!empty($review_data) ? strip_tags($review_data['memo']) : "") ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">평점</th>
                    <td>
                        <input type="radio" name="score" value="5" <?php echo (!empty($review_data) && $review_data['score'] == "5" ? "checked" : "checked")?>>
                        <img src="<?php echo base_url(); ?>img/sub/score_5.gif">
                        <br>
                        <input type="radio" name="score" value="4" <?php echo (!empty($review_data) && $review_data['score'] == "4" ? "checked" : "")?>>
                        <img src="<?php echo base_url(); ?>img/sub/score_4.gif">
                        <br>
                        <input type="radio" name="score" value="3" <?php echo (!empty($review_data) && $review_data['score'] == "3" ? "checked" : "")?>>
                        <img src="<?php echo base_url(); ?>img/sub/score_3.gif">
                        <br>
                        <input type="radio" name="score" value="2" <?php echo (!empty($review_data) && $review_data['score'] == "2" ? "checked" : "")?>>
                        <img src="<?php echo base_url(); ?>img/sub/score_2.gif">
                        <br>
                        <input type="radio" name="score" value="1" <?php echo (!empty($review_data) && $review_data['score'] == "1" ? "checked" : "")?>>
                        <img src="<?php echo base_url(); ?>img/sub/score_1.gif">
                    </td>
	            </tr>
                <tr>
                    <th scope="row">이미지/동영상</th>
                    <td>
                        <input type="file" name="img1">
                        <?php if(!empty($review_data['img1'])) { ?>
                            <img src="<?=base_url()?>data/review/<?=$review_data['img1']?>" class="review_img">
                            <input type="checkbox" name="del_img1" value="<?=$review_data['img1']?>"> 삭제
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">이미지/동영상</th>
                    <td>
                        <input type="file" name="img2">
                        <?php if(!empty($review_data['img2'])) { ?>
                            <img src="<?=base_url()?>data/review/<?=$review_data['img2']?>" class="review_img">
                            <input type="checkbox" name="del_img2" value="<?=$review_data['img2']?>"> 삭제
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">이미지/동영상</th>
                    <td>
                        <input type="file" name="img3">
                        <?php if(!empty($review_data['img3'])) { ?>
                            <img src="<?=base_url()?>data/review/<?=$review_data['img3']?>" class="review_img">
                            <input type="checkbox" name="del_img3" value="<?=$review_data['img3']?>"> 삭제
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">이미지/동영상</th>
                    <td>
                        <input type="file" name="img4">
                        <?php if(!empty($review_data['img4'])) { ?>
                            <img src="<?=base_url()?>data/review/<?=$review_data['img4']?>" class="review_img">
                            <input type="checkbox" name="del_img4" value="<?=$review_data['img4']?>"> 삭제
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
	    </table>
    </div>
    <div class="btn_confirm">
        <button type="submit" class="btn_medium">확인</button>
        <button type="button" onclick="window.close();" class="btn_medium bx-white">취소</button>
    </div>
</form>
<script>
function forderreview_submit(f) {
	if(!f.wr_content.value) {
		alert('내용을 입력하세요.');
		f.wr_content.focus();
		return false;
	}

	if(!getSelectVal(f["wr_score"])){
		alert('평점을 선택하세요.');
		f.wr_score.focus();
		return false;
	}

	if(confirm("등록 하시겠습니까?") == false)
		return false;

    return true;
}
</script>
