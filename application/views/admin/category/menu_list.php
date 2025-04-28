<?=$headTitle?>
<h2>메뉴 관리</h2>
<form name="fcategoryform" method="post" action="<?=base_url()?>AdminProcess/menu_update">
    <input type="hidden" name="token" value="">
    <input type="hidden" name="type" value="insert">
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w140">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">메뉴명</th>
                    <td><input type="text" name="menu_name" required itemname="메뉴명" class="frm_input required" size="50"></td>
                </tr>
                <tr>
                    <th scope="row">연결 URL</th>
                    <td><input type="text" name="url" required class="frm_input required" size="50"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" class="btn_large" value="저장">
    </div>
</form>
<form method="post" action="<?=base_url()?>AdminProcess/menu_update">
    <input type="hidden" name="token" value="">
    <input type="hidden" name="type" value="update">
    <div class="local_frm01 mart80">
            <input type="submit" name="act_button" value="선택삭제" class="btn_lsmall bx-white" onclick="document.pressed=this.value">
            <input type="submit" name="act_button" value="선택수정" class="btn_lsmall bx-white" onclick="document.pressed=this.value">
    </div>
    <div class="tbl_head01">
        <table class="tablef">
            <colgroup>
                <col class="w50">
                <col class="w100">
                <col>
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                    <th scope="col">순서</th>
                    <th scope="col">메뉴명</th>	
                    <th scope="col">연결 URL</th>	
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($res)) {
                    foreach($res as $key => $row) { ?>
                        <tr class="list">
                            <td>			
                                <input type="hidden" name="menu_id[]" value="<?=$row['index_no']?>">
                                <input type="checkbox" name="chk[]" value="<?=$key?>" id="chk_<?=$key?>">
                            </td>
                            <td><input type="text" name="rank[]" class="frm_input" value="<?=$row['rank']?>"></td>
                            <td><input type="text" name="menu_name[]" class="frm_input" value="<?=$row['menu_name']?>"></td>
                            <td><input type="text" name="url[]" class="frm_input" value="<?=$row['url']?>"></td>
                        </tr>  
                    <?php } 
                } ?>
            </tbody>
        </table>
    </div>
</form>
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
</script>

<?=$headTitleEnd?>