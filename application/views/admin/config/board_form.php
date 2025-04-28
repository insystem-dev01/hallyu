<?=$headTitle?>
<form name="fboardform" method="post" action="<?=base_url()?>AdminProcess/board_form_update" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    
    <h2>게시판기본</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">TABLE</th>
                    <td><input type="text" name="bo_table" value="<?php echo $board['index_no']; ?>" class="frm_input" readonly style="background-color:#ddd"></td>
                </tr>
                <tr>
                    <th scope="row">그룹</th>
                    <td>
                        <?= $get_group_select ?>
                        <?= $updateTag?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">게시판 제목</th>
                    <td><input type="text" name="boardname" value="<?php echo $boardTitle; ?>" required itemname="게시판 제목" class="frm_input required" size="50"></td>
                </tr>
                <tr>
                    <th scope="row">스킨 디렉토리</th>
                    <td>
                        <select name="skin" required itemname="스킨 디렉토리">
                            <?=$skinStr?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">분류</th>
                    <td>
                        <input type="text" name="usecate" value="<?php echo $usecateStr; ?>" class="frm_input" size="80">
                        <label><input type="checkbox" name="use_category" value="1"<?php echo $board['use_category']?' checked':''; ?>> 사용</label>
                        <?php echo $helpMulti; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">게시판 테이블 폭</th>
                    <td>
                        <input type="text" name="width" value="<?php echo $board['width']; ?>" required itemname="게시판 테이블 폭" class="frm_input w80"> <span class="fc_197">100 이하는 %로 작동 합니다. </span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">페이지당 목록 수</th>
                    <td>
                        <input type="text" name="page_num" value="<?php echo $board['page_num']; ?>"
                        required itemname="페이지당 목록 수" class="frm_input w80"> <span class="fc_197">목록에 출력되는 게시물 줄수를 의미합니다.</span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">제목 길이</th>
                    <td>
                        <input type="text" name="list_cut" value="<?php echo $board['list_cut']; ?>"
                        required itemname="제목 길이"  class="frm_input w80"> <span class="fc_197">게시판 목록에서 출력될 제목을 자릅니다.</span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">목록보기 권한</th>
                    <td>
                        <?php echo $list['list_priv']; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">글읽기 권한</th>
                    <td>
                        <?php echo $list['read_priv']; ?>
                </tr>
                <tr>
                    <th scope="row">글쓰기 권한</th>
                    <td>
                        <?php echo $list['write_priv']; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">글답변 권한</th>
                    <td>
                        <?php echo $list['reply_priv']; ?>
                        <label><input type="checkbox" name="usereply" value="Y"<?php echo $usereplyStr; ?>> 사용</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">코멘트쓰기 권한</th>
                    <td>
                        <?php echo $list['tail_priv'] ?>
                        <label><input type="checkbox" name="usetail" value="Y"<?php echo $usetailStr; ?>> 사용</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">파일 업로드</th>
                    <td>
                        <label><input type="checkbox" name="usefile" value="Y"<?php echo $usefileStr; ?>> 사용</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">비밀글 사용</th>
                    <td>
                        <select name="use_secret">
                            <?php echo $use_secretArr[0]; ?>
                            <?php echo $use_secretArr[1]; ?>
                            <?php echo $use_secretArr[2]; ?>
                        </select>
                        <?php echo $chkHelp; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">글내용 옵션</th>
                    <td>
                        <select name="read_list">
                            <?php echo $read_listArr[1]; ?>
                            <?php echo $read_listArr[2]; ?>
                            <?php echo $read_listArr[3]; ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <h2>디자인/양식</h2>
    <div class="tbl_frm01">
        <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <?php if(false){?>
                <tr>
                    <th scope="row">상단 파일 경로</th>
                    <td><input type="text" name="topfile" readonly="true" value="<?php echo $board['topfile']; ?>" class="frm_input" size="50"></td>
                </tr>
                <tr>
                    <th scope="row">하단 파일 경로</th>
                    <td><input type="text" name="downfile" readonly="true" value="<?php echo $board['downfile']; ?>" class="frm_input" size="50"></td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row">상단 이미지</th>
                    <td>
                        <input type="file" name="image_head" id="image_head">
                        <?=$fileurl1_strTag?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">하단 이미지</th>
                    <td>
                        <input type="file" name="image_tail" id="image_tail">
                        <?=$fileurl2_strTag?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">상단 내용</th>
                    <td><textarea name="content_head" class="frm_textbox wfull" rows="5"><?php echo $board['content_head'] ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">하단 내용</th>
                    <td><textarea name="content_tail" class="frm_textbox wfull" rows="5"><?php echo $board['content_tail'] ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">글쓰기 기본 내용</th>
                    <td><textarea name="insert_content" class="frm_textbox wfull" rows="5"><?php echo $board['insert_content'] ?></textarea></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
        <a href="<?=base_url()?>admin/config?code=board_list<?php echo $qstr; ?>&page=<?php echo $page; ?>" class="btn_large bx-white">목록</a>
    </div>
</form>
<script>
function chk_board_head(f, val){
	switch(val) {
		case 'gr_item':
			f.topfile.value  = '../mypage/board_head.php';
			f.downfile.value = '../mypage/board_tail.php';
			break;
		case 'gr_mall':
			f.topfile.value  = './board_head.php';
			f.downfile.value = './board_tail.php';
			break;
		default:
			f.topfile.value  = '';
			f.downfile.value = '';
			break;
	}
}
</script>

<?=$headTitleEnd?>