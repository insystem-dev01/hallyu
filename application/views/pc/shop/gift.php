<?=$aside_my?>
<div id="con_lf">
	<h2 class="pg_tit">
		<span><?php echo $title; ?></span>
		<p class="pg_nav">HOME<i>&gt;</i>마이페이지<i>&gt;</i><?php echo $title; ?></p>
	</h2>
    <form name="fcoupon" id="fcoupon" method="post" action="<?php echo $form_action_url; ?>" onsubmit="return fcoupon_submit(this);" autocomplete="off">
	    <input type="hidden" name="token" value="<?php echo $token; ?>">
        <p class="fc_e06 marb7">
            ※ 쿠폰번호 인증 완료 후 포인트가 실시간 적립되며 바로 사용하실 수 있습니다.
        </p>
        <p class="cp_txt_bx bt">
            1. 쿠폰은 현금으로 교환 및 환불이 불가능 합니다.<br>
            2. 쿠폰번호는 대/소문자를 구분할 수 있으니 받은 번호 그대로 입력해 주세요.
        </p>
        <p class="cp_txt_bx tac">
            <input type="text" name="gi_num1" required itemname="쿠폰번호" maxlength="4" class="frm_cp required" onkeyup="if(this.value.length==4) document.fcoupon.gi_num2.focus();">
            <span>-</span>
            <input type="text" name="gi_num2" required itemname="쿠폰번호" maxlength="4" class="frm_cp required" onkeyup="if(this.value.length==4) document.fcoupon.gi_num3.focus();">
            <span>-</span>
            <input type="text" name="gi_num3" required itemname="쿠폰번호" maxlength="4" class="frm_cp required" onkeyup="if(this.value.length==4) document.fcoupon.gi_num4.focus();">
            <span>-</span>
            <input type="text" name="gi_num4" required itemname="쿠폰번호" maxlength="4" class="frm_cp required">
            <input type="submit" value="인증하기" class="btn_lsmall wset">
        </p>
    </form>
    <script>
	function fcoupon_submit(f) {
		if(confirm("인증 하시려면 '확인'버튼을 클릭하세요!") == false)
			return false;

		return true;
	}
	</script>
    <div class="top_sch mart20">
		<form name="fsearch2" id="fsearch2" method="post">
		<p class="fl padt10">총 <b class="fc_255"><?php echo number_format($total_count); ?></b>건의 쿠폰내역이 있습니다.</p>
		<p class="fr">
			<input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" placeholder="쿠폰번호">
			<input type="submit" value="검색" class="btn_small grey">
		</p>
		</form>
	</div>
    <div class="tbl_head02 tbl_wrap">
		<table>
            <colgroup>
                <col width="6%">
                <col>
                <col width="12%">
                <col width="11%">
                <col width="11%">
                <col width="8%">
                <col width="18%">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col">쿠폰번호</th>
                    <th scope="col">금액</th>
                    <th scope="col">시작일</th>
                    <th scope="col">종료일</th>
                    <th scope="col">인증상태</th>
                    <th scope="col">등록일</th>
                </tr>
            </thead>
            <tbody>
                <?=$giftTable?>
            </tbody>
		</table>
	</div>
    <?=$pagination?>
</div>