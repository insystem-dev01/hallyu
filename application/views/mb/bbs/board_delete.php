<form name="frm" action="<?php echo base_url(); ?>m/process/board_delete2" method="post" onsubmit="return frm_submit(this);">
    <input type="hidden" name="w" value="d">
    <input type="hidden" name="index_no" value="<?php echo $index_no; ?>">
    <input type="hidden" name="boardid" value="<?php echo $boardid; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">

    <div class="m_bo_pop">
        <h2>게시물삭제</h2>
        <p>한번 삭제된 데이타는 복구되지 않습니다.<br>삭제 하시려면 삭제하기 버튼을 누르세요.</p>
        <?= $deleteTag ?>
    </div>
</form>

<script>
    function frm_submit(f) {
        <?= $pwdJs ?>
        if (answer == true) {
            return true;
        } else {
            return false;
        }
    }
</script>