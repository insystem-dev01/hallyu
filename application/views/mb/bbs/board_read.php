<div class="m_bo_bg mart10">
    <div class="title"><?php echo $bo_subject; ?></div>
    <div class="wr_info">
        <?php echo $write['writer_s']; ?>
        <b>|</b>
        <?php echo $bo_wdate; ?>

        <?php if ($boardid == "37"): ?>
            <b>|</b>
            좋아요
            <span class="count_margin good_count" id="good_count_<?= $index_no ?>"><?= $good_count ?></span>
        <?php endif; ?>
        <b>|</b>
        조회수
        <span class="count_margin"><?= $read_count ?></span>
    </div>
    <div class="wr_txt">
        <div class="file_area">
            <?= $bo_file1_tag ?>
            <?= $bo_file2_tag ?>
        </div>
        <div class="memo_area memo" style="vertical-align:top;">
            <?= $bo_file1_img ?>
            <?= $bo_file2_img ?>
            <?= $getViewThum ?>
        </div>
        <!-- 상품 요청 게시판일 때만 보이게 -->
        <?php if ($boardid == "37"): ?>
            <div class="goods_button" onclick="request_goods_good('<?= $index_no ?>');">
                <span id="<?= $index_no ?>" class="<?= $index_no ?> <?= $good_check ?>"></span>
                <span class="good_label">좋아요 <em id='good_count2_<?= $index_no ?>'><?= $good_count ?></em></span>
            </div>
        <?php endif; ?>

    </div>
    <div class="rbt_box">

        <?= $modifyTag ?>
        <?= $replyTag ?>
        <?= $delTag ?>
        <?= $writeTag ?>
    </div>
    <div class="comment_area">
        <?= $comment ?>
    </div>
</div>
<div class="page_wrap">
    <div class="lbt_box">
        <?php if ($prev_index_no): ?>
            <a href="<?= base_url() ?>m/bbs/board_read?index_no=<?= $prev_index_no ?>&boardid=<?= $boardid ?>" class="btn_lsmall">
                &lt; 이전글</a>

        <?php else: ?>
            <a href="#" class="btn_lsmall" onclick="alert('이전 글이 없습니다.');">
                &lt; 이전글
            </a>
        <?php endif; ?>
        <!-- 상품요청게시판 -->
        <?php if ($boardid == "37"): ?>
            <a href="<?= base_url() ?>m/bbs/goods_request_list?<?= $qstr1 ?>" class="btn_lsmall">목록</a>
            <!-- 공지사항 -->
        <?php elseif ($boardid == "13"): ?>
            <a href="<?= base_url() ?>m/bbs/noticeList?<?= $qstr1 ?>" class="btn_lsmall ">목록</a>
        <?php endif; ?>

        <?php if ($next_index_no): ?>
            <a href="<?= base_url() ?>m/bbs/board_read?index_no=<?= $next_index_no ?>&boardid=<?= $boardid ?>" class="btn_lsmall ">
                다음글 &gt;</a>

        <?php else: ?>
            <a href="#" class="btn_lsmall "
                onclick="alert('다음 글이 없습니다.');">
                다음글 &gt;
            </a>
        <?php endif; ?>


    </div>

</div>

<script>
    $(".memo").css({
        'pointer-events': 'none'
    });

    function fboardform_submit(f) {
        <?= $fboardformJs ?>

        if (f.mode.value === 'd') {
            return true;
        }

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