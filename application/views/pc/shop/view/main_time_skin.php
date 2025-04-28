<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    var targetDate<?=$gs_id?> = new Date(<?=$t['year']?>, <?=$t['mon'] - 1?>, <?=$t['mday']?>, <?=$t['hours']?>, <?=$t['minutes']?>, <?=$t['seconds']?>);
    var targetInMS<?=$gs_id?> = targetDate<?=$gs_id?>.getTime();

    var oneSec = 1000;
    var oneMin = 60 * oneSec;
    var oneHr = 60 * oneMin;
    var oneDay = 24 * oneHr;

    function countDown<?=$gs_id?>() {
        var nowInMS = new Date().getTime();
        var diff = targetInMS<?=$gs_id?> - nowInMS;

        if (diff < 0) {
            setImages<?=$gs_id?>(0, 0, 0, 0);
            return;
        }

        var daysLeft = Math.floor(diff / oneDay); diff %= oneDay;
        var hrsLeft = Math.floor(diff / oneHr); diff %= oneHr;
        var minsLeft = Math.floor(diff / oneMin); diff %= oneMin;
        var secsLeft = Math.floor(diff / oneSec);

        setImages<?=$gs_id?>(daysLeft, hrsLeft, minsLeft, secsLeft);
    }

    function setImages<?=$gs_id?>(days, hrs, mins, secs) {
        if (days > 999) days = 999;

        days = formatNum<?=$gs_id?>(days, 2);
        hrs = formatNum<?=$gs_id?>(hrs, 2);
        mins = formatNum<?=$gs_id?>(mins, 2);
        secs = formatNum<?=$gs_id?>(secs, 2);

        var d = document.querySelector('.days<?=$gs_id?>');
        var h = document.querySelector('.hours<?=$gs_id?>');
        var m = document.querySelector('.minutes<?=$gs_id?>');
        var s = document.querySelector('.seconds<?=$gs_id?>');

        if (d && h && m && s) {
            d.textContent = days;
            h.textContent = hrs;
            m.textContent = mins;
            s.textContent = secs;
        }
    }

    function formatNum<?=$gs_id?>(num, len) {
        var numStr = "" + num;
        while (numStr.length < len) {
            numStr = "0" + numStr;
        }
        return numStr;
    }

    countDown<?=$gs_id?>();
    setInterval(countDown<?=$gs_id?>, 1000);
});
</script>

<div class="time main <?=$is_open?>">
    <?php if($is_open == "wait"): ?>
        <span class="isOpen">오픈예정</span>
    <?php endif; ?>

    <span class="days<?=$gs_id?>">00</span><span class="day">일</span>
    <span class="hours<?=$gs_id?>">00</span><span class="day">:</span>
    <span class="minutes<?=$gs_id?>">00</span><span class="day">:</span>
    <span class="seconds<?=$gs_id?>">00</span>

    <?php if($is_open == "open"): ?>
        <span class="day">남음</span>
    <?php endif; ?>
</div>