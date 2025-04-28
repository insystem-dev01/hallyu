<script>
    alert("<?php echo $od['paymethod']; ?> 부분취소 처리됐습니다.");
    opener.document.location.reload();
    self.close();
</script>