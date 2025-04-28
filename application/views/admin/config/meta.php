<?=$headTitle?>
<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/meta_update">
    <input type="hidden" name="token" value="">
    <h2>메타태그 설정</h2>
    <div class="tbl_frm01">
        <table>
        <colgroup>
            <col class="w180">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">브라우저 타이틀</th>
            <td>
                <input type="text" name="head_title" value="<?php echo $config['head_title']; ?>" class="frm_input" size="60">
                <?php echo $help['title'];?>
            </td>
        </tr>
        <tr>
            <th scope="row">Author : 메타태그 1</th>
            <td>
                <input type="text" name="meta_author" value="<?php echo $config['meta_author']; ?>" class="frm_input" size="60">
                <?php echo $help['auth']; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">description : 메타태그 2</th>
            <td>
                <input type="text" name="meta_description" value="<?php echo $config['meta_description']; ?>" class="frm_input" size="60">
                <?php echo $help['desc']; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">keywords : 메타태그 3</th>
            <td>
                <textarea name="meta_keywords" class="frm_textbox wfull" rows="5"><?php echo $config['meta_keywords']; ?></textarea>
                <?php echo $help['keyword']; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">추가 메타태그</th>
            <td>
                <textarea name="add_meta" class="frm_textbox wfull" rows="5"><?php echo $config['add_meta']; ?></textarea>
                <?php echo $help['add_meta']; ?>
                <p class="mart5"><a href="http://webmastertool.naver.com/" target="_blank" class="btn_small grey">네이버 웹마스터도구</a></p>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <h2>HEAD, BODY - JAVASCRIPT 추가입력</h2>
    <div class="tbl_frm01">
        <table>
        <colgroup>
            <col class="w180">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">&lt;HEAD&gt; 내부 태그</th>
            <td>
                <textarea name="head_script" class="frm_textbox wfull" rows="5"><?php echo $config['head_script']; ?></textarea>
                <?php echo $help['head_script']; ?>
                <p class="mart5"><a href="http://analytics.naver.com/" target="_blank" class="btn_small grey">네이버 애널리틱스 (웹로그분석)</a> <a href="https://www.google.com/intl/ko_KR/analytics/" target="_blank" class="btn_small grey">구글 애널리틱스 (웹로그분석)</a></p>
            </td>
        </tr>
        <tr>
            <th scope="row">&lt;BODY&gt; 내부 태그</th>
            <td>
                <textarea name="tail_script" class="frm_textbox wfull" rows="5"><?php echo $config['tail_script']; ?></textarea>
                <?php echo $help['tail_script']; ?>
                <p class="mart5"><a href="https://tocplus.co.kr/" target="_blank" class="btn_small grey">톡플러스 실시간 상담</a></p>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>
</form>

<?=$headTitleEnd?>