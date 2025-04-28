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

        $('.days<?=$gs_id?>').html(formatNum<?=$gs_id?>(days, 2));
        $('.hours<?=$gs_id?>').html(formatNum<?=$gs_id?>(hrs, 2));
        $('.minutes<?=$gs_id?>').html(formatNum<?=$gs_id?>(mins, 2));
        $('.seconds<?=$gs_id?>').html(formatNum<?=$gs_id?>(secs, 2));
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
    <span class="days<?=$gs_id?>">00</span><span class="day">일</span>
    <span class="hours<?=$gs_id?>">00</span><span class="day">:</span>
    <span class="minutes<?=$gs_id?>">00</span><span class="day">:</span>
    <span class="seconds<?=$gs_id?>">00</span>
</div>