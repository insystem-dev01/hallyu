<script type="text/javascript">
function CountDownTimer(dt, id){
	var end = new Date(dt);

	var _second = 1000;
	var _minute = _second * 60;
	var _hour = _minute * 60;
	var _day = _hour * 24;
	var timer;

	function showRemaining() {
		var now = new Date();
		var distance = end - now;
		if (distance < 0) {
			clearInterval(timer);
			document.getElementById(id).innerHTML = '<span class="expired">종료됨</span>';
			return;
		}

		var days = Math.floor(distance / _day);
		var hours = Math.floor((distance % _day) / _hour);
		var minutes = Math.floor((distance % _hour) / _minute);
		var seconds = Math.floor((distance % _minute) / _second);

		var str = "";
		str += '<span class="label">남은시간 </span>';
		str += '<span class="num">'+ pad(days, 2) + '</span><span class="unit">일</span> ';
		str += '<span class="num">'+ pad(hours, 2) + '</span><span class="unit">:</span>';
		str += '<span class="num">'+ pad(minutes, 2) + '</span><span class="unit">:</span>';
		str += '<span class="num">'+ pad(seconds, 2) + '</span><span class="unit"> 남음</span>';

		document.getElementById(id).innerHTML = str;
	}

	timer = setInterval(showRemaining, 1000);
	showRemaining(); // 처음 로드될 때 바로 보여주기
}

function pad(n, width) {
	n = n + '';
	return n.length >= width ? n : new Array(width - n.length + 1).join('0') + n;
}
</script>

<h2 class="pg_title">
	<div class="inner">
		<dl class="txt_bx">
			<dt>함께장터</dt>
			<dd>한정된 시간동안만 누릴 수 있는 함께장터 상품을 만나보세요</dd>
		</dl>
	</div>
</h2>
<?=$treeCate?>
<div class="tab_sort">
	<span class="total">전체상품 <b class="fc_90" id="total"><?php echo number_format($total_count); ?></b>개</span>
	<ul>
		<?php echo $sort_str; // 탭메뉴 ?>
	</ul>
</div>
<div class="timesale">
	<ul>
        <?=$tiemList?>
    </ul>
</div>
<?=$empty?>
<?=$pagination?>
