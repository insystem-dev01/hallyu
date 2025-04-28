<script>
document.addEventListener("DOMContentLoaded", function () {
    function initCountdown(targetDate, container) {
        var targetInMS = targetDate.getTime();

        function countDown() {
            var now = new Date().getTime();
            var diff = targetInMS - now;

            if (diff < 0) {
                updateDisplay(0, 0, 0, 0);
                clearInterval(timer);
                return;
            }

            var oneSec = 1000, oneMin = 60000, oneHr = 3600000, oneDay = 86400000;
            var days = Math.floor(diff / oneDay); diff %= oneDay;
            var hrs = Math.floor(diff / oneHr); diff %= oneHr;
            var mins = Math.floor(diff / oneMin); diff %= oneMin;
            var secs = Math.floor(diff / oneSec);

            updateDisplay(days, hrs, mins, secs);
        }

        function updateDisplay(days, hrs, mins, secs) {
            container.querySelector('.days').textContent = String(days).padStart(2, '0');
            container.querySelector('.hours').textContent = String(hrs).padStart(2, '0');
            container.querySelector('.minutes').textContent = String(mins).padStart(2, '0');
            container.querySelector('.seconds').textContent = String(secs).padStart(2, '0');
        }

        countDown();
        var timer = setInterval(countDown, 1000);
    }

    // ↓ 이 부분에 PHP로 넘긴 날짜값을 자바스크립트에 삽입
    var targetDate = new Date(<?= $t['year'] ?>, <?= $t['mon'] - 1 ?>, <?= $t['mday'] ?>, <?= $t['hours'] ?>, <?= $t['minutes'] ?>, <?= $t['seconds'] ?>);

    // ↓ 해당 상품 ID가 들어간 박스만 선택해서 타이머 적용
    document.querySelectorAll('[data-timer="<?=$gs_id?>"]').forEach(function(el) {
        initCountdown(targetDate, el);
    });
});
</script>


<div class="time goods <?=$is_open?>" data-timer="<?=$gs_id?>">
    <span class="days">00</span><span class="text">일 </span>
    <span class="hours">00</span><span class="text"> : </span>
    <span class="minutes">00</span><span class="text"> : </span>
    <span class="seconds">00</span><span class="text"> 남음</span>
</div>