<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    var targetDate = new Date(<?=$t['year'];?>, <?=$t['mon'] - 1;?>, <?=$t['mday'];?>, <?=$t['hours'];?>, <?=$t['minutes'];?>, <?=$t['seconds'];?>);
    var targetInMS = targetDate.getTime();

    var oneSec = 1000;
    var oneMin = 60 * oneSec;
    var oneHr = 60 * oneMin;
    var oneDay = 24 * oneHr;

    function countDown() {
        var nowInMS = new Date().getTime();
        var diff = targetInMS - nowInMS;

        if (diff < 0) {
            setImages(0, 0, 0, 0);
            return;
        }

        var daysLeft = Math.floor(diff / oneDay); diff %= oneDay;
        var hrsLeft = Math.floor(diff / oneHr); diff %= oneHr;
        var minsLeft = Math.floor(diff / oneMin); diff %= oneMin;
        var secsLeft = Math.floor(diff / oneSec);

        setImages(daysLeft, hrsLeft, minsLeft, secsLeft);
    }

    function setImages(days, hrs, mins, secs) {
        $('.days').html(formatNum(days, 2));
        $('.hours').html(formatNum(hrs, 2));
        $('.minutes').html(formatNum(mins, 2));
        $('.seconds').html(formatNum(secs, 2));
    }

    function formatNum(num, len) {
        var numStr = "" + num;
        while (numStr.length < len) {
            numStr = "0" + numStr;
        }
        return numStr;
    }

    countDown();
    setInterval(countDown, 1000);
});
</script>

<div class="time goods <?=$is_open?>">
    <span class="days">00</span><span class="day">일</span>
    <span class="hours">00</span><span class="day">:</span>
    <span class="minutes">00</span><span class="day">:</span>
    <span class="seconds">00</span>
</div>