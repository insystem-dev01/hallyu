<?=$headTitle?>
<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/goods_qa_form_update" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="sst" value="<?php echo $sst; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="iq_id" value="<?php echo $iq_id; ?>">
    <div class="tbl_frm02">
        <table>
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row">상품명</th>
                    <td><a href="<?php echo base_url(); ?>shop/view?index_no=<?php echo $iq['gs_id']; ?>" target="_blank"><?php echo $gs['gname']; ?></a></td>
                </tr>
                <tr>
                    <th scope="row">품목코드</th>
                    <td><?php echo $gs['gcode']; ?></td>
                </tr>
                <tr>
                    <th scope="row">판매자</th>
                    <td><?php echo $gs['mb_id']; ?></td>
                </tr>
                <tr>
                    <th scope="row">옵션</th>
                    <td>
                        <select name="iq_ty">
                            <option value=""<?php echo $allTag; ?>>문의유형(선택)</option>
                            <option value="상품"<?php echo $gsTag; ?>>상품</option>
                            <option value="배송"<?php echo $delTag; ?>>배송</option>
                            <option value="반품/환불/취소"<?php echo $returnTag; ?>>반품/환불/취소</option>
                            <option value="교환/변경"<?php echo $changeTag; ?>>교환/변경</option>
                            <option value="기타"<?php echo $ectTag; ?>>기타</option>
                        </select>
                        <input type="checkbox" name="iq_secret" value="1" id="iq_secret"<?php echo $secretTag; ?>> <label for="iq_secret">비밀글</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">성명</th>
                    <td><input type="text" name="iq_name" value="<?php echo $iq['iq_name']; ?>" class="frm_input" size="20"></td>
                </tr>
                <tr>
                    <th scope="row">이메일</th>
                    <td><input type="text" name="iq_email" value="<?php echo $iq['iq_email']; ?>" class="frm_input" size="30"></td>
                </tr>
                <tr>
                    <th scope="row">핸드폰</th>
                    <td><input type="text" name="iq_hp" value="<?php echo $iq['iq_hp']; ?>" class="frm_input" size="20"></td>
                </tr>
                <tr>
                    <th scope="row">제목</th>
                    <td><input type="text" name="iq_subject" value="<?php echo htmlspecialchars($iq['iq_subject']); ?>" class="frm_input" size="60"></td>
                </tr>
                <tr>
                    <th scope="row">질문</th>
                    <td><textarea name="iq_question" class="frm_textbox"><?php echo $iq['iq_question']; ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">답변</th>
                    <td><textarea name="iq_answer" class="frm_textbox"><?php echo $iq['iq_answer']; ?></textarea></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
        <a href="<?=base_url()?>admin/goods?code=qa<?php echo $qstr; ?>&page=<?php echo $page; ?>" class="btn_large bx-white">목록</a>
    </div>
</form>
<?=$headTitleEnd?>