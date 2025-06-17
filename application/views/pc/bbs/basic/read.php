<?= $b_content_head ?>
<div class="tbl_frm01 tbl_wrap">
    <table>
        <tbody>
            <tr>
                <td class="list1 fs14"><b><?php echo $bo_subject; ?></b></td>
            </tr>
            <tr>
                <td class="list1 tal">
                    <b>이름: </b> <?= $bo_writer_s; ?> <b>|</b> 
                    <b>작성일:</b> <?php echo $bo_wdate; ?> <b>|</b>
                    <?= $good ?> <b>|</b>
                     <b>조회수:</b> <?php echo $bo_hit; ?> 
                     
                </td>
            </tr>
            <?= $bo_file1_tag ?>
            <?= $bo_file2_tag ?>
            <tr>
                <td class="memo remove_line" style="height:200px;vertical-align:top;">
                    <?= $bo_file1_img ?>
                    <?= $bo_file2_img ?>
                    <?= $getViewThum ?>

                </td>
            </tr>
            <?= $good_button ?>
        </tbody>

    </table>

</div>

<div class="page_wrap">
    <div class="lbt_box">
        <?php if ($prev_index_no): ?>
            <a href="<?= base_url() ?>bbs/read?index_no=<?= $prev_index_no ?>&boardid=<?= $boardid ?>" class="btn_lsmall bx-white">
                &lt; 이전글</a>

                <?php else: ?>
                    <a href="#" class="btn_lsmall bx-white" onclick="alert('이전 글이 없습니다.');">
                        &lt; 이전글
                            </a>
                        <?php endif; ?>

                        <a href="<?= base_url() ?>bbs/noticeList?<?= $qstr1 ?>" class="btn_lsmall bx-white">목록</a>

                        <?php if ($next_index_no): ?>
                            <a href="<?= base_url() ?>bbs/read?index_no=<?= $next_index_no ?>&boardid=<?= $boardid ?>" class="btn_lsmall bx-white">
                                다음글 &gt;</a>

                        <?php else: ?>
                            <a href="#" class="btn_lsmall bx-white"
                                onclick="alert('다음 글이 없습니다.');">
                                다음글 &gt;
                            </a>
                        <?php endif; ?>


    </div>
    <div class="rbt_box">

        <?= $modifyTag ?>
        <?= $replyTag ?>
        <?= $delTag ?>
        <?= $writeTag ?>
    </div>
</div>
<?= $comment ?>
<script>
    $(".memo").css({
        'pointer-events': 'none'
    });

    function fboardform_submit(f) {
        <?= $fboardformJs ?>
        if (!f.memo.value) {
            alert('댓글을 작성하지 않았습니다!');
            f.memo.focus();
            return false;
        }
        return true;
    }

    function imgview(img) {
        var base_url = document.getElementById('base_url').value;
        window.open(base_url + "bbs/imgviewer?img=" + img, "img", 'width=150,height=150,status=no,top=0,left=0,scrollbars=yes');
    }
</script>
<?= $read_list ?>
<?= $b_content_tail ?>