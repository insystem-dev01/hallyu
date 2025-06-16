<?php
// get_pc_timesaleTag() 에서 $end_datetime, $is_open, $gs_id를 넘겨줬다고 가정
$timerClass = 'timer-' . $gs_id;
?>

<div class="time goods <?= $is_open ?> <?= $timerClass ?>"
    data-endtime="<?= htmlspecialchars($end_datetime, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($is_open === 'wait'): ?>
        <span class="status">오픈예정</span>
    <?php endif; ?>

    <span class="days">00</span><span class="unit">일</span>
    <span class="hours">00</span><span class="unit">:</span>
    <span class="minutes">00</span><span class="unit">:</span>
    <span class="seconds">00</span>
    <span class="unit"> 남음</span>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ONE_SEC = 1000,
            ONE_MIN = 60 * ONE_SEC,
            ONE_HR = 60 * ONE_MIN,
            ONE_DAY = 24 * ONE_HR;

        // .time.goods 요소 전부에 대해 카운트다운 초기화
        document.querySelectorAll(".time.goods.<?= $timerClass ?>").forEach(container => {
            // data-endtime: "YYYY-MM-DD hh:mm:ss"
            const raw = container.dataset.endtime;
            const iso = raw.replace(" ", "T"); // "YYYY-MM-DDThh:mm:ss"
            const endMS = new Date(iso).getTime();

            function update() {
                let diff = endMS - Date.now();
                if (diff <= 0) {
                    container.innerHTML = '<span class="unit">마감</span>';
                    return clearInterval(timer);
                }
                const days = Math.floor(diff / ONE_DAY);
                diff %= ONE_DAY;
                const hrs = Math.floor(diff / ONE_HR);
                diff %= ONE_HR;
                const mins = Math.floor(diff / ONE_MIN);
                diff %= ONE_MIN;
                const secs = Math.floor(diff / ONE_SEC);

                container.querySelector(".days").textContent = String(days).padStart(2, "0");
                container.querySelector(".hours").textContent = String(hrs).padStart(2, "0");
                container.querySelector(".minutes").textContent = String(mins).padStart(2, "0");
                container.querySelector(".seconds").textContent = String(secs).padStart(2, "0");
            }

            update();
            const timer = setInterval(update, 1000);
        });
    });
</script>