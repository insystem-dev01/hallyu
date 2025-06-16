<?php
$timerClass = 'timer-' . $gs_id;
?>

<div class="time main <?= $is_open ?> <?= $timerClass ?>"
    data-endtime="<?= htmlspecialchars($end_datetime, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($is_open == "wait"): ?>
        <span class="isOpen">오픈예정</span>
    <?php endif; ?>

    <span class="days">00</span><span class="day">일</span>
    <span class="hours">00</span><span class="day">:</span>
    <span class="minutes">00</span><span class="day">:</span>
    <span class="seconds">00</span>

    <?php if ($is_open == "open"): ?>
        <span class="day">남음</span>
    <?php endif; ?>
</div>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        const ONE_SEC = 1000,
            ONE_MIN = 60 * ONE_SEC,
            ONE_HR = 60 * ONE_MIN,
            ONE_DAY = 24 * ONE_HR;

        document.querySelectorAll(".time.<?= $timerClass ?>").forEach(el => {
            const raw = el.dataset.endtime;
            const iso = raw.replace(" ", "T");
            const endMS = new Date(iso).getTime();

            function update() {
                let diff = endMS - Date.now();
                if (diff <= 0) {
                    el.innerHTML = '<span class="unit">마감</span>';
                    return clearInterval(timer);
                }
                const days = Math.floor(diff / ONE_DAY);
                diff %= ONE_DAY;
                const hrs = Math.floor(diff / ONE_HR);
                diff %= ONE_HR;
                const mins = Math.floor(diff / ONE_MIN);
                diff %= ONE_MIN;
                const secs = Math.floor(diff / ONE_SEC);

                el.querySelector(".days").textContent = String(days).padStart(2, "0");
                el.querySelector(".hours").textContent = String(hrs).padStart(2, "0");
                el.querySelector(".minutes").textContent = String(mins).padStart(2, "0");
                el.querySelector(".seconds").textContent = String(secs).padStart(2, "0");
            }

            update();
            const timer = setInterval(update, 1000);
        });
    });
</script>