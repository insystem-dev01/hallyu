<div id="sit_use_write" class="new_win">
	<h1 id="win_title"><?php echo $title; ?></h1>
    <form name="forderreview" method="post" action="<?php echo $form_action_url; ?>" onsubmit="return forderreview_submit(this);" enctype="multipart/form-data">
        <input type="hidden" name="gs_id" value="<?php echo $gs_id; ?>">
        <input type="hidden" name="seller_id" value="<?php echo $gs['mb_id']; ?>">
        <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
        <input type="hidden" name="od_no" value="<?php echo $od_no; ?>">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <input type="hidden" name="od_index" value="<?php echo $od_index; ?>">
        <input type="hidden" name="w" value="<?php echo $w; ?>">
        <div class="tbl_frm01 tbl_wrap review_write">
            <table>
                <colgroup>
                    <col class="w90">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">상품명</th>
                        <td><?php echo $gs_name; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">주문번호</th>
                        <td><?php echo $od_id; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">이름</th>
                        <td><?php echo $member['name']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">평점</th>
                        <td>
                            <input type="radio" name="score" value="5" <?php echo (!empty($review_data) && $review_data['score'] == "5" ? "checked" : "checked")?>>
                            <img src="<?php echo base_url(); ?>img/sub/score_5.gif">
                            <input type="radio" name="score" value="4" <?php echo (!empty($review_data) && $review_data['score'] == "4" ? "checked" : "")?>>
                            <img src="<?php echo base_url(); ?>img/sub/score_4.gif">
                            <input type="radio" name="score" value="3" <?php echo (!empty($review_data) && $review_data['score'] == "3" ? "checked" : "")?>>
                            <img src="<?php echo base_url(); ?>img/sub/score_3.gif">
                            <input type="radio" name="score" value="2" <?php echo (!empty($review_data) && $review_data['score'] == "2" ? "checked" : "")?>>
                            <img src="<?php echo base_url(); ?>img/sub/score_2.gif">
                            <input type="radio" name="score" value="1" <?php echo (!empty($review_data) && $review_data['score'] == "1" ? "checked" : "")?>>
                            <img src="<?php echo base_url(); ?>img/sub/score_1.gif">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">내용</th>
                        <td>
                            <textarea name="memo" class="frm_textbox wufll"><?php echo (!empty($review_data) ? strip_tags($review_data['memo']) : "") ?></textarea>
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
        <div class="win_btn">
            <input type="submit" value="작성완료" class="btn_lsmall">
            <a href="javascript:window.close();" class="btn_lsmall bx-white">창닫기</a>
        </div>
	</form>
</div>
<script>
function forderreview_submit(f) {
	if(!f.memo.value) {
		alert('내용을 입력하세요.');
		f.memo.focus();
		return false;
	}

	if(confirm("등록 하시겠습니까?") == false)
		return false;

    return true;
}
</script>