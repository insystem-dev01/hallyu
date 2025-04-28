<?=$headTitle?>
<?=$jqueryUiDatePick?>
<h2>기본검색</h2>
<form name="fsearch" id="fsearch" method="get">
    <input type="hidden" name="code" value="<?php echo $code; ?>">
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w100">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">검색어</th>
                    <td>
                        <select name="sfl">
                            <?php echo $sflArr['id']; ?>
                            <?php echo $sflArr['name']; ?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">주문일</th>
                    <td>
                        <?php echo $get_search_date; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">레벨검색</th>
                    <td>
                        <?php echo $get_search_level; ?>
                    </td>
                </tr>
            </tbody>
	    </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="검색" class="btn_medium">
        <input type="button" value="초기화" id="frmRest" class="btn_medium grey">
    </div>
</form>
<div class="local_ov mart30">
	전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 명 조회
	<strong class="ov_a">총 주문액 : <?php echo number_format($sum_price); ?>원</strong>
	<strong class="ov_a">총 판매상품수 : <?php echo number_format($sum_count); ?>건</strong>
</div>
<div class="local_frm01">
	<?php echo $btn_frmline; ?>
</div>
<div class="tbl_head02">
	<table id="partner_list">
        <colgroup>
            <col class="w50">
            <col class="w130">
            <col class="w130">
            <col class="w130">
            <col>
            <col class="w100">
            <col class="w100">
            <col class="w100">
            <col class="w100">
            <col class="w100">
            <col class="w100">
            <col class="w100">
        </colgroup>
        <thead>
            <tr>
                <th scope="col" rowspan="2">번호</th>
                <th scope="col" rowspan="2"><?php echo $name; ?>회원명</a></th>
                <th scope="col" rowspan="2"><?php echo $id; ?>아이디</a></th>
                <th scope="col" rowspan="2"><?php echo $grade; ?>레벨</a></th>
                <th scope="col" rowspan="2">그래프 (주문액)</th>
                <th scope="col" colspan="2">검색집계</th>
                <th scope="col" colspan="5">최근집계 (오늘날짜 기준)</th>
            </tr>
            <tr class="rows">
                <th scope="col" class="th_bg2">판매상품수</th>
                <th scope="col" class="th_bg2">주문액</th>
                <th scope="col" class="th_bg">오늘</th>
                <th scope="col" class="th_bg">어제</th>
                <th scope="col" class="th_bg">일주일</th>
                <th scope="col" class="th_bg">지난달</th>
                <th scope="col" class="th_bg">3개월</th>
            </tr>
        </thead>
        <?=$order1Table?>
        </tbody>
	</table>
</div>
<div class="local_frm02">
	<?php echo $btn_frmline; ?>
</div>
<?=$pagination?>
<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="desc02">
			<p>ㆍ가맹점 주문통계는 <strong>(취소/환불/반품)</strong> 내역은 제외 입니다.</p>
			<p>ㆍ최근집계 항목은 검색결과에 반영되지 않으며 오늘날짜를 기준으로 고정되어 노출됩니다.</p>
		</div>
	 </div>
</div>

<script>
$(function(){
	// 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99" });
});
</script>
<?=$headTitleEnd?>