<?=$headTitle?>
<form name="fsendmailselectform" action="<?php echo base_url(); ?>admin/member?code=mail_select_list" method="post" autocomplete="off">
    <input type="hidden" name="ma_id" value="<?php echo $ma_id; ?>">
    <h2>회원정보</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">회원 통계</th>
                    <td>전체회원 : <?php echo number_format($tot_cnt); ?>명 , (차단된회원수 : <?php echo number_format($finish_cnt); ?>명 , <strong>정상회원 : <?php echo number_format($tot_cnt - $finish_cnt); ?>명</strong> 중 메일 발송 대상 선택)</td>
                </tr>
                <tr>
                    <th scope="row">회원 아이디</th>
                    <td class="td_article">
                        <article>
                        <input type="radio" name="mb_id1" value="1" id="mb_id1_all" <?php echo $mb_all; ?>> <label for="mb_id1_all">전체</label>
                        <input type="radio" name="mb_id1" value="0" id="mb_id1_section" <?php echo $mb_allNo; ?>> <label for="mb_id1_section">구간</label>
                        </article>
                        <article>
                        <input type="text" name="mb_id1_from" value="<?php echo $mb_id1_from; ?>" id="mb_id1_from" title="시작구간" class="frm_input" size="10" placeholder="a" onkeyup="only_eng_num_typing(this)"> 에서
                        <input type="text" name="mb_id1_to" value="<?php echo $mb_id1_to; ?>" id="mb_id1_to" title="종료구간" class="frm_input" size="10" placeholder="z" onkeyup="only_eng_num_typing(this)"> 까지
                        </article>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="mb_email">E-mail</label></th>
                    <td>
                        <input type="text" name="mb_email" value="<?php echo $mb_email; ?>" id="mb_email" class="frm_input" size="40">
                        <?php echo $help; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="mb_mailling">메일링</label></th>
                    <td>
                        <select name="mb_mailling" id="mb_mailling">
                            <option value="Y">수신동의한 회원만
                            <option value="">전체
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">레벨</th>
                    <td>
                        <label for="mb_level_from" class="sound_only">최소레벨</label>
                        <?php echo $mb_level_from; ?> 에서
                        <label for="mb_level_to" class="sound_only">최대레벨</label>
                        <?php echo $mb_level_to; ?> 까지
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="다음" class="btn_large" accesskey="s">
        <a href="<?php echo base_url(); ?>admin/member?code=mail_list<?php echo $qstr; ?>&page=<?php echo $page; ?>" class="btn_large bx-white">목록</a>
    </div>
</form>
<div class="information">
	<h4>도움말</h4>
	<div class="content">
		<div class="desc02">
			<p>ㆍ차단된 회원과 입점사 회원을 제외한 정상 회원에게만 메일이 발송됩니다.</p>
		</div>
	</div>
</div>
<script>
$(function() {
    $("input[name=mb_id1]").on("click",function() {
        switch($(this).val()) {
            case "1":
				$("#mb_id1_from").attr("disabled",true);
				$("#mb_id1_from").css("background-color","#eee");
				$("#mb_id1_to").attr("disabled",true);
				$("#mb_id1_to").css("background-color","#eee");
                break;
			case '0':
				$("#mb_id1_from").attr("disabled",false);
				$("#mb_id1_from").css("background-color","#fff");
				$("#mb_id1_to").attr("disabled",false);
				$("#mb_id1_to").css("background-color","#fff");
				break;
        }
    });

	$("#mb_id1_all").trigger( "click" );
});
</script>
<?=$headTitleEnd?>
