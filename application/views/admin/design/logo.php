<?=$headTitle?>
<form name="flogo" method="post" action="<?=base_url()?>AdminProcess/logo_update" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="token" value="">

    <h2>쇼핑몰 로고</h2>
    <div class="tbl_frm01">
        <table>
                <colgroup>
                    <col class="w180">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row">적용대상</th>
                    <td>
                        <?php echo $get_level_select; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">대표 로고</th>
                    <td>
                        <input type="file" name="basic_logo" id="basic_logo">
                        <?=$fileTag?>
                        <?=$sizeHelp?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">모바일 로고</th>
                    <td>
                        <input type="file" name="mobile_logo" id="mobile_logo">
                        <?=$mbFileTag?>
                        <?=$size2Help?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">SNS 기본 로고</th>
                    <td>
                        <input type="file" name="sns_logo" id="sns_logo">
                        <?=$smsFileTag?>
                        <?php echo $size3Help; ?>
                    </td>
                </tr>
            </tbody>
	    </table>
    </div>
    
    <h2>파비콘 (favicon) 설정</h2>
    <div class="tbl_frm01">
        <table class="tablef">
            <colgroup>
                <col class="w180">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row" rowspan="2">파비콘 아이콘 (ico파일)</th>
                    <td>
                        <input type="file" name="favicon_ico" id="favicon_ico">
                        <?=$faviconTag?>
                        <?=$size4Help ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>파비콘(favicon) 이란?</strong>
                        <p class="padt5">브라우저의 타이틀 옆에 표시되거나 즐겨찾기시 설명 옆에 표시되는 사이트의 아이콘을 말합니다.<br>크롬, 사파리, 오페라등 익스플로러 외 다른 OS이거나 브라우저 버전에 따라 출력이 되지 않을 수 있습니다.<br>파비콘(favicon)은 크기 16x16픽셀, 최대 용량 150KB의 (*.ico) 파일만 사용하실 수 있습니다.</p>
                        <p class="padt5"><img src="<?php echo base_url(); ?>img/visual_favicon.jpg"></p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>
</form>
<script>
function user_chage(mb_id){
    var base_url=document.getElementById('base_url').value;
	location.href = base_url+'admin/design?code=logo&mb_id='+mb_id;
}
</script>
<?=$headTitleEnd?>