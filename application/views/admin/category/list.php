<?=$headTitle?>
<h2>카테고리 등록</h2>
<form name="fcategoryform" method="post" action="<?=base_url()?>AdminProcess/category_update" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="token" value="">
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">카테고리 소속</th>
                    <td>
                        <?php echo $sel_ca[0]; ?>
                        <?php echo $sel_ca[1]; ?>
                        <?php echo $sel_ca[2]; ?>
                        <?php echo $sel_ca[3]; ?>
                        <script>
                        $(function() {
                            var base_url=document.getElementById('base_url').value;
                            $("#sel_ca1").multi_select_box("#sel_ca",4,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                            $("#sel_ca2").multi_select_box("#sel_ca",4,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                            $("#sel_ca3").multi_select_box("#sel_ca",4,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                            $("#sel_ca4").multi_select_box("#sel_ca",4,"","=카테고리선택=");
                        });
                        </script>
                    </td>
                </tr>
                <tr>
                    <th scope="row">카테고리명</th>
                    <td><input type="text" name="catename" required itemname="카테고리명" class="frm_input required" size="50"></td>
                </tr>
                <?php if($member['grade'] == "99") { ?>
                <tr>
                    <th scope="row">카테고리 상단배너</th>
                    <td><input type="file" name="headimg"></td>
                </tr>
                <tr>
                    <th scope="row">카테고리 상단배너 링크</th>
                    <td>
                        <input type="text" name="headimgurl" class="frm_input" size="50">
                        <?php echo $help1; ?>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" class="btn_large" value="저장">
    </div>
</form>
<div class="sho_cate_bx mart30">
	<div class="local_frm02">
		<a href="<?php echo base_url(); ?>AdminProcess/category_excel" class="btn_lsmall bx-white"><i class="fa fa-file-excel-o"></i> 카테고리 엑셀저장</a>
	</div>
	<ul>
        <?=$listTable?>
    </ul>
</div>
<script>
// POST 방식으로 삭제
function post_delete(action_url, val){
	var f = document.fpost;

	if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
        f.ca_no.value = val;
		f.action = action_url;
		f.submit();
	}
}

function modok(no){
    var base_url=document.getElementById('base_url').value;
	document.all['cos'+no].src = base_url+"admin/category_mod?index_no="+no;
	document.all['co'+no].style.display = "";
}
</script>

<form name="fpost" method="post">
    <input type="hidden" name="q1" value="<?php echo $q1; ?>">
    <input type="hidden" name="ca_no" value="">
</form>
<?=$headTitleEnd?>