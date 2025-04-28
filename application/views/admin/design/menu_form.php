<?=$headTitle?>

<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/menu_form_update">
    <input type="hidden" name="token" value="">

    <h2>메뉴 설정</h2>
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col width="80px">
                <col width="50px">
                <col width="200px">
                <col width="200px">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th>구분</th>
                    <th>노출</th>
                    <th>메뉴</th>
                    <th>기본</th>
                    <th>URL</th>
                </tr>
            </thead>
            <tbody>
            <?=$gw_menuTag?>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>
</form>
<?=$headTitleEnd?>