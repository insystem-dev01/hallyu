
<h1 class="newp_tit"><?php echo $title; ?></h1>
<div class="new_win_body">
	<form name="fsearch" id="fsearch" method="get">
        <div class="tbl_frm01">
            <table>
            <colgroup>
                <col class="w120">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th scope="row">검색어</th>
                <td>
                    <select name="sfl">
                        <?php echo $mb_nameTag; ?>
                        <?php echo $mb_idTag; ?>
                        <?php echo $gi_numTag; ?>
                    </select>
                    <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input">
                </td>
            </tr>
            </tbody>
            </table>
        </div>
        <div class="btn_confirm">
            <input type="submit" value="검색" class="btn_lsmall">
            <input type="button" value="초기화" id="frmRest" class="btn_lsmall grey">
        </div>
	</form>

	<div class="local_ov mart30">
		전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 건 조회
		<span class="ov_a">쿠폰명 : <?php echo $gr_subjectTag; ?></span>
	</div>
	<div class="local_frm01">
		<a href="<?=base_url()?>AdminProcess/goods_gift_excel?<?php echo $q1; ?>" class="btn_small bx-white"><i class="fa fa-file-excel-o"></i> 엑셀저장</a>
	</div>
	<div class="tbl_head01">
		<table>
            <colgroup>
                <col class="w50">
                <col class="w170">
                <col class="w50">
                <col>
                <col>
                <col class="w130">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col">인증번호</th>
                    <th scope="col"><?php echo $gi_useLink; ?>사용</a></th>
                    <th scope="col"><?php echo $mb_idLink; ?>회원ID</a></th>
                    <th scope="col"><?php echo $mb_nameLink; ?>회원명</a></th>
                    <th scope="col"><?php echo $mb_wdateLink; ?>최종사용일</a></th>
                </tr>
            </thead>
            <?=$serialTable?>
            </tbody>
		</table>
	</div>
    <?=$pagination?>
</div>