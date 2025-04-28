<?=$headTitle?>
<form name="fregform" method="post" action="<?=base_url()?>AdminProcess/best_item_update" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="token" value="">

    <h2><?php echo $default['de_maintype_title']; ?></h2>
    <div class="tbl_head01">
        <table>
            <colgroup>
                <col width="80px">
                <col width="230px">
                <col width="230px">
                <col width="230px">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">구분</th>
                    <th scope="col">아이콘(1)</th>
                    <th scope="col">아이콘(2)</th>
                    <th scope="col">카테고리명</th>
                    <th scope="col">상품코드</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="list1">타이틀명</td>
                    <td><input type="text" name="de_maintype_title" value="<?php echo $default['de_maintype_title']; ?>" required itemname="최상위 타이틀명" class="required frm_input wfull" placeholder="최상위 타이틀명"></td>
                    <td></td>
                </tr>
                <?=$listTag?>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm">
        <input type="submit" value="저장" class="btn_large" accesskey="s">
    </div>
</form>
<?=$headTitleEnd?>
