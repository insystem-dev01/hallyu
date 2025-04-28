<div class="s_wrap">
	<h2><?php echo $title; ?></h2>
	<form name="fsearch" id="fsearch" method="get">
        <div class="tbl_frm01 mart7">
            <table class="tablef">
                <colgroup>
                    <col class="w50">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">검색</th>
                        <td>
                            <select name="sfl">
                                <?php echo $sflArr[0]; ?>
                                <?php echo $sflArr[1]; ?>
                                <?php echo $sflArr[2]; ?>
                                <?php echo $sflArr[3]; ?>
                                <?php echo $sflArr[4]; ?>
                            </select>
                            <input type="text" name="stx" value="<?php echo $stx; ?>" class="frm_input" size="30">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">분류</th>
                        <td>
                            <?php echo $sel_caArr[1]; ?>
                            <?php echo $sel_caArr[2]; ?>
                            <?php echo $sel_caArr[3]; ?>
                            <?php echo $sel_caArr[4]; ?>
                            <?php echo $sel_caArr[5]; ?>
                            <script>
                            $(function() {
                                var base_url=document.getElementById('base_url').value;
                                $("#sel_ca1").multi_select_box("#sel_ca",5,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                                $("#sel_ca2").multi_select_box("#sel_ca",5,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                                $("#sel_ca3").multi_select_box("#sel_ca",5,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                                $("#sel_ca4").multi_select_box("#sel_ca",5,base_url+"AdminProcess/ajax_category_select_json","=카테고리선택=");
                                $("#sel_ca5").multi_select_box("#sel_ca",5,"","=카테고리선택=");
                            });
                            </script>
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
    <form name="fgoodslist" method="post">
        <input type="hidden" name="q1" value="<?php echo $q1; ?>">
        <input type="hidden" name="page" value="<?php echo $page; ?>">

        <div class="local_ov mart30">
            전체 : <b class="fc_red"><?php echo number_format($total_count); ?></b> 건 조회
        </div>
        <div class="local_frm01">
            <?php echo $btn_frmline; ?>
        </div>
        <div class="tbl_head01">
            <table>
                <colgroup>
                    <col class="w50">
                    <col class="w60">
                    <col class="w80">
                    <col class="w50">
                    <col>
                    <col class="w70">
                    <col class="w70">
                    <col class="w80">
                    <col class="w60">
                </colgroup>
                <thead>
                    <tr>
                        <th><input type="checkbox" name="chkall" value="1" onclick="check_all(this.form);"></th>
                        <th>이미지</th>
                        <th><?php echo $gcodeTag; ?>상품코드</a></th>
                        <th><?php echo $mb_idTag; ?>구분</a></th>
                        <th><?php echo $gnameTag; ?>상품명</a></th>
                        <th><?php echo $isopenTag; ?>진열</a></th>
                        <th><?php echo $stock_qtyTag; ?>재고</a></th>
                        <th><?php echo $goods_priceTag; ?>판매가</a></th>
                        <th>관리</th>
                    </tr>
                </thead>
                <?=$couponTable?>
                </tbody>
            </table>
        </div>
        <div class="local_frm02">
            <?php echo $btn_frmline; ?>
        </div>
    </form>
    <?=$pagination?>
</div>

<script>
function chk_save(gs_id, gs_name, gs_img, gs_price){
	var ck_use_goods = opener.document.getElementById('ck_use_goods');
	var cp_use_goods = opener.document.getElementById('cp_use_goods');
	var comma = '';

	var this_good = 'chk_'+gs_id;
	var ck_use_goods_info = ck_use_goods.outerHTML;

	var str = ck_use_goods.innerHTML;
	if(ck_use_goods_info.indexOf(this_good)==-1){
		str += "<li id=\"chk_"+gs_id+"\">";
		str += "<img src=\""+gs_img+"\" class=\"pr_img\">";
		str += "<p>"+gs_name+"</p><p class=\"mart5 bold\">"+commaStr(gs_price)+"원</p>";
		str += "<a href=\"javascript:chk_del('"+gs_id+"');\" class=\"bt_del\"><img src=\"<?php echo base_url(); ?>admin/img/bt_delete.gif\"></a>";
		str += "</li>";
		ck_use_goods.innerHTML = str;

		if(cp_use_goods.value)
			comma = ',';

		cp_use_goods.value += comma + gs_id;
	}
}

function check_all(f)
{
    var chk = document.getElementsByName("chk[]");

    for (i=0; i<chk.length; i++)
        chk[i].checked = f.chkall.checked;
}

function btn_check(act)
{
	var f = document.fgoodslist;

    if(act == "punish")
        str = "적용";
    else
        return;

    var chk		 = document.getElementsByName("chk[]");
	var gs_id	 = document.getElementsByName("gs_id[]");
	var gs_name  = document.getElementsByName("gs_name[]");
	var gs_img   = document.getElementsByName("gs_img[]");
	var gs_price = document.getElementsByName("gs_price[]");
    var bchk = false;

	var cnt = 0;
    for(i=0; i<chk.length; i++)
    {
        if(chk[i].checked) {
            bchk = true;
			cnt++;

			chk_save(gs_id[i].value, gs_name[i].value, gs_img[i].value, gs_price[i].value);
		}
    }

    if(!bchk)
    {
        alert(str + "할 자료를 하나 이상 선택하세요.");
        return;
    } else {
		alert(cnt + "건이 적용 되었습니다.");
	}
}
</script>