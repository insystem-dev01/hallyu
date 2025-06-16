<?=$headTitle?>
<?=$jqueryUiDatePick?>
<script src="<?php echo base_url(); ?>js/categoryform.js?ver=<?php echo time(); ?>"></script>
<form name="fregform" id="fregform" method="post" onsubmit="return fregform_submit(this)" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="gs_id" value="<?php echo $gs_id; ?>">
    <input type="hidden" name="q1" value="<?php echo $qstr; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="bak" value="<?php echo $bak; ?>">
    <input type="hidden" name="ca_id" value="">
    <input type="hidden" name="ca_id2" value="">
    <input type="hidden" name="ca_id3" value="">
    <input type="hidden" name="type" value="<?=$type?>">
    <section id="anc_sitfrm_cate">
        <h2>카테고리</h2>
        <?php echo $pg_anchor; ?>
        <div class="local_desc02 local_desc">
            <p>선택된 카테고리에 <span class="fc_084">최상위 카테고리는 대표 카테고리로 자동설정</span>되며, 최소 1개의 카테고리는 등록하셔야 합니다.</p>
        </div>
        <div class="tbl_frm02">
            <table>
                <colgroup>
                    <col class="w180">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">카테고리 선택</th>
                        <td>
                            <div class="sub_frm01">
                                <table>
                                    <tr>
                                        <td class="w20p bg1">
                                            <?php echo $sel_ca1Tag; ?>
                                        </td>
                                        <td class="w20p bg1">
                                            <?php echo $sel_ca2Tag; ?>
                                        </td>
                                        <td class="w20p bg1">
                                            <?php echo $sel_ca3Tag; ?>
                                        </td>
                                        <td class="w20p bg1">
                                            <?php echo $sel_ca4Tag; ?>
                                        </td>
                                        <td class="w20p bg1">
                                            <?php echo $sel_ca5Tag; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="btn_confirm02">
                                <button type="button" class="btn_lsmall blue" onclick="category_add();">카테고리 추가</button>
                            </div>
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
                    <tr>
                        <th scope="row">선택된 카테고리<br><span class="fc_red">(최대 3개까지만 등록)</span></th>
                        <td>
                            <select name="sel_ca_id" id="sel_ca_id" size="5" class="multiple-select">
                                <?=$admCateOpt?>
                            </select>
                            <div class="btn_confirm02">
                                <button type="button" class="btn_lsmall bx-white" onclick="category_move('sel_ca_id', 'prev');">▲ 위로</button>
                                <button type="button" class="btn_lsmall bx-white" onclick="category_move('sel_ca_id', 'next');">▼ 아래로</button>
                                <button type="button" class="btn_lsmall frm_option_del red">카테고리 삭제</button>
                            </div>
                        </td>
	                </tr>
	            </tbody>
	        </table>
        </div>
    </section>
    <?php echo $frm_submit; ?>
    <section id="anc_sitfrm_ini">
        <h2>기본정보</h2>
        <?php echo $pg_anchor; ?>
        <?=$goodsRegTag?>
        <div class="tbl_frm02">
            <table>
                <colgroup>
                    <col class="w180">
                    <col>
                </colgroup>
                <tbody>
                    <input type="hidden" name="mb_name" value="<?php echo $gs['mb_name']?>">
                    <tr>
                        <th scope="row">업체코드</th>
                        <td>
                            <input type="text" name="mb_id" value="<?php echo $gs['mb_id']; ?>" required itemname="업체코드" class="frm_input" readonly>
                            <a href="<?=base_url()?>admin/supply" onclick="win_open(this,'pop_supply','550','500','no');return false" class="btn_small">업체선택</a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">상품코드</th>
                        <td>
                            <input type="text" name="gcode" value="<?php echo $gs['gcode']; ?>" required itemname="상품코드" class="required frm_input" readonly style="background-color:#ddd;">
                            <?=$preview?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">상품명</th>
                        <td><input type="text" name="gname" value="<?php echo $gs['gname']; ?>" required itemname="상품명" class="required frm_input" size="80"></td>
                    </tr>
                    <tr>
                        <th scope="row">짧은설명</th>
                        <td><input type="text" name="explan" value="<?php echo $gs['explan']; ?>" class="frm_input" size="80"></td>
                    </tr>
                    <tr>
                        <th scope="row">검색키워드</th>
                        <td>
                            <input type="text" name="keywords" value="<?php echo $gs['keywords']; ?>" class="frm_input wfull">
                            <?php echo $searchHelp; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">브랜드</th>
                        <td>
                            <select name="brand_uid">
                                <option value="">선택</option>
                                <?=$brandStr?>
                            </select>
		                </td>
	                </tr>
                    <tr>
                        <th scope="row">모델명</th>
                        <td><input type="text" name="model" value="<?php echo $gs['model']; ?>" class="frm_input"></td>
                    </tr>
                    <tr>
                        <th scope="row">생산국(원산지)</th>
                        <td><input type="text" name="origin" value="<?php echo $gs['origin']; ?>" class="frm_input"></td>
                    </tr>
                    <tr>
                        <th scope="row">제조사</th>
                        <td><input type="text" name="maker" value="<?php echo $gs['maker']; ?>" class="frm_input"></td>
                    </tr>
                    <tr>
                        <th scope="row">과세설정</th>
                        <td class="td_label">
                            <?php echo $notax['1']; ?>
                            <?php echo $notax['0']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">수입필증 / 계약서</th>
                        <td>
                            <input type="file" name="document" class="frm_input">
                            <?php if($w == 'u' && !empty($gs['document'])) { ?>
                                <a href="<?=base_url()?>data/document/<?=$gs['document']?>" target="_blank" class="fc_197 marl5">다운로드</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">판매여부</th>
                        <td class="td_label">
                            <?php echo $isopenArr['exhibit']; ?>
                            <?php echo $isopenArr['solidout']; ?>
                            <?php echo $isopenArr['extinct']; ?>
                            <?php echo $isopenArr['stop']; ?>
                            <?php echo $isopenArr['end']; ?>
                        </td>
                    </tr>
                    <?php if(false) { ?>
                    <tr>
                        <th scope="row">네이버쇼핑 상품ID</th>
                        <td>
                            <input type="text" name="ec_mall_pid" value="<?php echo $gs['ec_mall_pid']; ?>" id="ec_mall_pid" class="frm_input">
                            <?php echo $naverHelp; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
	        </table>
        </div>
    </section>
    <?php echo $frm_submit; ?>
    <section id="anc_sitfrm_option">
        <h2>옵션정보</h2>
        <?php echo $pg_anchor; ?>
        <div class="tbl_frm02">
            <table>
                <colgroup>
                    <col class="w180">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">상품 주문옵션</th>
                        <td>
                            <p class="mart5">옵션항목은 콤마 ( , ) 로 구분하여 여러개를 입력할 수 있습니다. 예시) 빨강, 노랑, 파랑</p>
                            <table class="mart7">
                                <colgroup>
                                    <col width="60px">
                                    <col width="150px">
                                    <col width="85px">
                                    <col>
                                </colgroup>
                                <tbody>
                                    <tr>
                                        <th scope="row">옵션1</th>
                                        <td><input type="text" name="opt1_subject" value="<?php echo $opt_subject[0]; ?>" id="opt1_subject" class="frm_input wfull"></td>
                                        <th scope="row">옵션1 항목</th>
                                        <td><input type="text" name="opt1" id="opt1" value="" class="frm_input wfull"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">옵션2</th>
                                        <td><input type="text" name="opt2_subject" value="<?php echo $opt_subject[1]; ?>" id="opt2_subject" class="frm_input wfull"></td>
                                        <th scope="row">옵션2 항목</th>
                                        <td><input type="text" name="opt2" id="opt2" value="" class="frm_input wfull"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">옵션3</th>
                                        <td><input type="text" name="opt3_subject" value="<?php echo $opt_subject[2]; ?>" id="opt3_subject" class="frm_input wfull"></td>
                                        <th scope="row">옵션3 항목</th>
                                        <td><input type="text" name="opt3" id="opt3" value="" class="frm_input wfull"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="btn_confirm02"><button type="button" id="option_table_create" class="btn_lsmall red">옵션목록생성</button></p>
                            <div id="sit_option_frm">
                                <?=$goods_option; ?>
                            </div>
                            <script>
                                // 모두선택
                                function opt_chk_allFun(e){
                                    var that=$(e);
                                    if(that.is(":checked")) {
                                        $("input[name='opt_chk[]']").attr("checked", true);
                                    } else {
                                        $("input[name='opt_chk[]']").attr("checked", false);
                                    }
                                }
                                // 선택삭제
                                function sel_option_deleteFun(){
                                    var $el = $("input[name='opt_chk[]']:checked");
                                    if($el.size() < 1) {
                                        alert("삭제하려는 옵션을 하나 이상 선택해 주십시오.");
                                        return false;
                                    }

                                    $el.closest("tr").remove();
                                }
                                // 일괄적용
                                function opt_value_applyFun() {
                                    if ($(".opt_com_chk:checked").length < 1) {
                                        alert("일괄 수정할 항목을 하나 이상 체크해 주십시오.");
                                        return false;
                                    }

                                    var opt_price = $.trim($("#opt_com_price").val());
                                    var opt_stock = $.trim($("#opt_com_stock").val());
                                    var opt_noti = $.trim($("#opt_com_noti").val());
                                    var opt_use = $("#opt_com_use").val();
                                    var $checkedRows = $("input[name='opt_chk[]']:checked").closest("tr");
                                    var type = $("input[name='type']").val();
                                    var alertShown = false; 

                                    function updateOptionRow($tr) {
                                        if ($("#opt_com_price_chk").is(":checked")) {
                                            if (type !== "timesale") {
                                                $tr.find("input[name='opt_price[]']").val(opt_price);
                                            } else if (!alertShown) {
                                                alert('함께장터 상품은 옵션 추가금액을 설정할 수 없습니다.');
                                                alertShown = true;
                                            }
                                        }
                                        if ($("#opt_com_stock_chk").is(":checked")) {
                                            $tr.find("input[name='opt_stock_qty[]']").val(opt_stock);
                                        }
                                        if ($("#opt_com_noti_chk").is(":checked")) {
                                            $tr.find("input[name='opt_noti_qty[]']").val(opt_noti);
                                        }
                                        if ($("#opt_com_use_chk").is(":checked")) {
                                            $tr.find("select[name='opt_use[]']").val(opt_use);
                                        }
                                    }

                                    if ($checkedRows.length > 0) {
                                        $checkedRows.each(function () {
                                            updateOptionRow($(this));
                                        });
                                    } else {
                                        $("tr").each(function () {
                                            updateOptionRow($(this));
                                        });
                                    }

                                    stock_total(); // 재고 합산 함수 호출
                                }

                                function opt_price_chk(e) {
                                    const that = $(e);
                                    const input = that.val();
                                    
                                    var type = $("input[name='type']").val();
                                    if(type == "timesale") {
                                        alert('함께장터 상품은 옵션 추가금액을 설정할 수 없습니다.');
                                        that.val(0);
                                    }
                                }

			                    $(function() {
                                    <?=$optJs?>
                                    // 옵션목록생성
                                    $("#option_table_create").click(function() {
                                        var base_url=document.getElementById('base_url').value;
                                        var gs_id = $.trim($("input[name=gs_id]").val());
                                        var opt1_subject = $.trim($("#opt1_subject").val());
                                        var opt2_subject = $.trim($("#opt2_subject").val());
                                        var opt3_subject = $.trim($("#opt3_subject").val());
                                        var opt1 = $.trim($("#opt1").val());
                                        var opt2 = $.trim($("#opt2").val());
                                        var opt3 = $.trim($("#opt3").val());
                                        var $option_table = $("#sit_option_frm");

                                        if(!opt1_subject || !opt1) {
                                            alert("옵션명과 옵션항목을 입력해 주십시오.");
                                            return false;
                                        }

                                        $.post(
                                            base_url+"admin/goods_option",
                                            { gs_id: gs_id, w: "<?php echo $w; ?>", opt1_subject: opt1_subject, opt2_subject: opt2_subject, opt3_subject: opt3_subject, opt1: opt1, opt2: opt2, opt3: opt3 },
                                            function(data) {
                                                $option_table.empty().html(data);
                                            }
                                        );
                                    });
                                    // 모두선택
                                    $(document).on("click", "input[name=opt_chk_all]", function() {
                                        if($(this).is(":checked")) {
                                            $("input[name='opt_chk[]']").attr("checked", true);
                                        } else {
                                            $("input[name='opt_chk[]']").attr("checked", false);
                                        }
                                    });
                                    // 선택삭제
                                    $(document).on("click", "#sel_option_delete", function() {
                                        var $el = $("input[name='opt_chk[]']:checked");
                                        if($el.size() < 1) {
                                            alert("삭제하려는 옵션을 하나 이상 선택해 주십시오.");
                                            return false;
                                        }

                                        $el.closest("tr").remove();
                                    });
                                });
                            </script>
                        </td>
                    </tr>
                    <?php if(false) { ?>
                    <tr>
                        <th scope="row">상품 추가옵션</th>
                        <td>
                            <p>
                                <span class="mart7 fl">옵션항목은 콤마 ( , ) 로 구분하여 여러개를 입력할 수 있습니다. 예시) 빨강, 노랑, 파랑</span>
                                <button type="button" id="add_supply_row" class="btn_small blue marb5 fr">옵션추가</button>
                            </p>
                            <div id="sit_supply_frm">
                                <table>
                                    <colgroup>
                                        <col width="60px">
                                        <col width="150px">
                                        <col width="85px">
                                        <col>
                                        <col width="65px">
                                    </colgroup>
                                    <?=$optTable?>
                                </table>
                                <p class="btn_confirm02"><button type="button" id="supply_table_create" class="btn_lsmall red">옵션목록생성</button></p>
                            </div>
                            <div id="sit_option_addfrm">
                                <?=$goods_spl?>
                            </div>
                            <script>
                                //입력필드 제거
                                function del_supply_row(e){
                                    var that=$(e);
                                    that.closest("tr").remove();
                                    supply_sequence();
                                }
                                function spl_chk_allFun(e){
                                    var that=$(e);
                                    if(that.is(":checked")) {
                                        $("input[name='spl_chk[]']").attr("checked", true);
                                    } else {
                                        $("input[name='spl_chk[]']").attr("checked", false);
                                    }
                                }
                                function sel_supply_deleteFun(){
                                    var $el = $("input[name='spl_chk[]']:checked");
                                    if($el.size() < 1) {
                                        alert("삭제하려는 옵션을 하나 이상 선택해 주십시오.");
                                        return false;
                                    }

                                    $el.closest("tr").remove();
                                }
                                function spl_value_applyFun(){
                                    if($(".spl_com_chk:checked").size() < 1) {
                                        alert("일괄 수정할 항목을 하나이상 체크해 주십시오.");
                                        return false;
                                    }

                                    var spl_price = $.trim($("#spl_com_price").val());
                                    var spl_stock = $.trim($("#spl_com_stock").val());
                                    var spl_noti = $.trim($("#spl_com_noti").val());
                                    var spl_use = $("#spl_com_use").val();
                                    var $el = $("input[name='spl_chk[]']:checked");

                                    // 체크된 옵션이 있으면 체크된 것만 적용
                                    if($el.size() > 0) {
                                        var $tr;
                                        $el.each(function() {
                                            $tr = $(this).closest("tr");

                                            if($("#spl_com_price_chk").is(":checked"))
                                                $tr.find("input[name='spl_price[]']").val(spl_price);

                                            if($("#spl_com_stock_chk").is(":checked"))
                                                $tr.find("input[name='spl_stock_qty[]']").val(spl_stock);

                                            if($("#spl_com_noti_chk").is(":checked"))
                                                $tr.find("input[name='spl_noti_qty[]']").val(spl_noti);

                                            if($("#spl_com_use_chk").is(":checked"))
                                                $tr.find("select[name='spl_use[]']").val(spl_use);
                                        });
                                    } else {
                                        if($("#spl_com_price_chk").is(":checked"))
                                            $("input[name='spl_price[]']").val(spl_price);

                                        if($("#spl_com_stock_chk").is(":checked"))
                                            $("input[name='spl_stock_qty[]']").val(spl_stock);

                                        if($("#spl_com_noti_chk").is(":checked"))
                                            $("input[name='spl_noti_qty[]']").val(spl_noti);

                                        if($("#spl_com_use_chk").is(":checked"))
                                            $("select[name='spl_use[]']").val(spl_use);
                                    }
                                }
			                    $(function() {
                                    <?=$addOptJs?>
                                    // 입력필드추가
                                    $("#add_supply_row").click(function() {
                                        var $el = $("#sit_supply_frm tr:last");
                                        var fld = "<tr>\n";
                                        fld += "<th scope=\"row\"><label for=\"\">추가</label></th>\n";
                                        fld += "<td><input type=\"text\" name=\"spl_subject[]\" value=\"\" class=\"frm_input wfull\"></td>\n";
                                        fld += "<th scope=\"row\" class=\"ssupply_type\"><label for=\"\">추가 항목</label></th>\n";
                                        fld += "<td><input type=\"text\" name=\"spl[]\" value=\"\" class=\"frm_input wfull\"></td>\n";
                                        fld += "<td class=\"tac\"><button type=\"button\" onclick=\"del_supply_row()\" class=\"btn_ssmall bx-white\">삭제</button></td>\n";
                                        fld += "</tr>";

                                        $el.after(fld);

                                        supply_sequence();
                                    });

                                    // 입력필드삭제
                                    $(document).on("click", "#del_supply_row", function() {
                                        $(this).closest("tr").remove();

                                        supply_sequence();
                                    });

                                    // 옵션목록생성
                                    $("#supply_table_create").click(function() {
                                        var base_url=document.getElementById('base_url').value;
                                        var gs_id = $.trim($("input[name=gs_id]").val());
                                        var subject = new Array();
                                        var supply = new Array();
                                        var subj, spl;
                                        var count = 0;
                                        var $el_subj = $("input[name='spl_subject[]']");
                                        var $el_spl = $("input[name='spl[]']");
                                        var $supply_table = $("#sit_option_addfrm");

                                        $el_subj.each(function(index) {
                                            subj = $.trim($(this).val());
                                            spl = $.trim($el_spl.eq(index).val());

                                            if(subj && spl) {
                                                subject.push(subj);
                                                supply.push(spl);
                                                count++;
                                            }
                                        });

                                        if(!count) {
                                            alert("추가옵션명과 추가옵션항목을 입력해 주십시오.");
                                            return false;
                                        }

                                        $.post(
                                            base_url+"admin/goods_spl",
                                            { gs_id: gs_id, w: "<?php echo $w; ?>", 'subject[]': subject, 'supply[]': supply },
                                            function(data) {
                                                $supply_table.empty().html(data);
                                            }
                                        );
                                    });
                                    // 모두선택
                                    $(document).on("click", "input[name=spl_chk_all]", function() {
                                        if($(this).is(":checked")) {
                                            $("input[name='spl_chk[]']").attr("checked", true);
                                        } else {
                                            $("input[name='spl_chk[]']").attr("checked", false);
                                        }
                                    });
                                    // 선택삭제
                                    $(document).on("click", "#sel_supply_delete", function() {
                                        var $el = $("input[name='spl_chk[]']:checked");
                                        if($el.size() < 1) {
                                            alert("삭제하려는 옵션을 하나 이상 선택해 주십시오.");
                                            return false;
                                        }

                                        $el.closest("tr").remove();
                                    });
                                    // 일괄적용
                                    $(document).on("click", "#spl_value_apply", function() {
                                        if($(".spl_com_chk:checked").size() < 1) {
                                            alert("일괄 수정할 항목을 하나이상 체크해 주십시오.");
                                            return false;
                                        }

                                        var spl_price = $.trim($("#spl_com_price").val());
                                        var spl_stock = $.trim($("#spl_com_stock").val());
                                        var spl_noti = $.trim($("#spl_com_noti").val());
                                        var spl_use = $("#spl_com_use").val();
                                        var $el = $("input[name='spl_chk[]']:checked");

                                        // 체크된 옵션이 있으면 체크된 것만 적용
                                        if($el.size() > 0) {
                                            var $tr;
                                            $el.each(function() {
                                                $tr = $(this).closest("tr");

                                                if($("#spl_com_price_chk").is(":checked"))
                                                    $tr.find("input[name='spl_price[]']").val(spl_price);

                                                if($("#spl_com_stock_chk").is(":checked"))
                                                    $tr.find("input[name='spl_stock_qty[]']").val(spl_stock);

                                                if($("#spl_com_noti_chk").is(":checked"))
                                                    $tr.find("input[name='spl_noti_qty[]']").val(spl_noti);

                                                if($("#spl_com_use_chk").is(":checked"))
                                                    $tr.find("select[name='spl_use[]']").val(spl_use);
                                            });
                                        } else {
                                            if($("#spl_com_price_chk").is(":checked"))
                                                $("input[name='spl_price[]']").val(spl_price);

                                            if($("#spl_com_stock_chk").is(":checked"))
                                                $("input[name='spl_stock_qty[]']").val(spl_stock);

                                            if($("#spl_com_noti_chk").is(":checked"))
                                                $("input[name='spl_noti_qty[]']").val(spl_noti);

                                            if($("#spl_com_use_chk").is(":checked"))
                                                $("select[name='spl_use[]']").val(spl_use);
                                        }
                                    });
                                });
                                function supply_sequence(){
                                    var $tr = $("#sit_supply_frm tr");
                                    var seq;
                                    var th_label, td_label;

                                    $tr.each(function(index) {
                                        seq = index + 1;
                                        $(this).find("th label").attr("for", "spl_subject_"+seq).text("추가"+seq);
                                        $(this).find("td input").attr("id", "spl_subject_"+seq);
                                        $(this).find("th.ssupply_type label").attr("for", "spl_item_"+seq);
                                        $(this).find("th.ssupply_type label").text("추가"+seq+" 항목");
                                        $(this).find("td input").attr("id", "spl_item_"+seq);
                                    });
                                }
                            </script>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php echo $frm_submit; ?>
    <section id="anc_sitfrm_cost">
        <h2>가격 및 재고</h2>
        <?php echo $pg_anchor; ?>
        <div class="tbl_frm02">
            <table>
                    <colgroup>
                        <col class="w180">
                        <col>
                    </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">정가</th>
                        <td>
                            <input type="text" name="normal_price" value="<?php echo number_format($gs['normal_price']); ?>" class="frm_input w80" onkeyup="addComma(this);" onfocus="removeLeadingZero(this)" onblur="restoreZeroIfEmpty(this)"> 원
                            <span class="fc_197 marl5">시중에 판매되는 가격 <?php echo ($type == "normal") ? "(판매가보다 크지않으면 시중가 표시안함)" : "" ?></span>
                        </td>
                    </tr>
                    <?php if($type == "normal") { ?>
                    <tr>
                        <th scope="row">공급가격</th>
                        <td>
                            <input type="text" name="supply_price" value="<?php echo number_format($gs['supply_price']); ?>" class="frm_input w80" onkeyup="addComma(this);" onfocus="removeLeadingZero(this)" onblur="restoreZeroIfEmpty(this)"> 원
                            <span class="fc_197 marl5">사입처에서 공급받은 가격</span>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th scope="row"><?php echo ($type == "normal") ? "판매가" : "판매가(온라인최저가)" ?></th>
                        <td>
                            <input type="text" name="goods_price" value="<?php echo number_format($gs['goods_price']); ?>" class="frm_input w80" onkeyup="addComma(this);" onfocus="removeLeadingZero(this)" onblur="restoreZeroIfEmpty(this)"> 원
                            <span class="fc_197 marl5"><?php echo ($type == "normal") ? "실제 판매가 입력 (대표가격으로 사용)" : "구매자의 선결제금액"?></span>
                        </td>
                    </tr>
                    <?php if($type == "timesale") { ?>
                    <tr>
                        <th scope="row">실제 판매가</th>
                        <td>
                            <input type="text" name="calculate_price" value="<?php echo number_format($gs['calculate_price']); ?>" class="frm_input w80" onkeyup="addComma(this);" onfocus="removeLeadingZero(this)" onblur="restoreZeroIfEmpty(this)"> 원
                            <span class="fc_197 marl5">총 판매 금액에 계산되는 가격</span>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th scope="row">포인트 적립여부</th>
                        <td>
                            <input type="radio" name="is_point" value="1" <?php echo $gs['is_point'] == 1 ? "checked" : "checked"; ?>> 적립 O
                            <input type="radio" name="is_point" value="0" <?php echo $gs['is_point'] == 0 ? "checked" : ""; ?>> 적립 X
                        </td>
                    </tr>
                    <?php if($type == "timesale") { ?>
                    <tr>
                        <th scope="row">수량</th>
                        <td>
                            <input type="hidden" name="stock_mod" value="1">
                            <input type="text" name="stock_qty" value="<?php echo number_format($gs['stock_qty']); ?>" class="frm_input w80" onkeyup="addComma(this);"> 개
                            <?php if($type == "normal") { ?>
                                <b class="marl10">재고 통보수량</b> <input type="text" name="noti_qty" value="<?php echo number_format($gs['noti_qty']); ?>" class="frm_input w80" onkeyup="addComma(this);"> 개
                                <p class="fc_197 mart7">상품의 재고가 통보수량보다 작을 때 상품 재고관리에 '재고부족'으로 표시됩니다.<br>옵션이 있는 상품은 개별 옵션의 통보수량이 적용됩니다. 설정이 무제한이면 재고관리에 표시되지 않습니다.</p>
                            <?php } ?>
                            <!-- <?php if($type == "timesale") { ?>
                                <p class="fc_197 mart7">
                                    최소 수량을 4개 이상, 짝수로만 입력해야 계산식이 적용됩니다.
                                </p>
                            <?php } ?> -->
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">총 판매 금액</th>
                        <td>
                            <input type="text" name="goods_total_price" value="<?= number_format($gs['goods_total_price'])?>" class="frm_input" readonly>
                            <span class="fc_197 marl5">판매가격과 수량 입력시 자동으로 계산됩니다.</span>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if($type == "normal") { ?>
                    <tr>
                        <th scope="row">가격 대체문구</th>
                        <td>
                            <input type="text" name="price_msg" value="<?php echo $gs['price_msg']; ?>" class="frm_input">
                            <span class="fc_197 marl5">가격대신 보여질 문구를 노출할 때 입력, 주문불가</span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">수량</th>
                        <td>
                            <input type="radio" name="stock_mod" value="0" id="ids_stock_mode1"<?php echo $stockMod[0]; ?> onclick="chk_stock(0);">
                            <label for="ids_stock_mode1" class="marr10">무제한</label>
                            <input type="radio" name="stock_mod" value="1" id="ids_stock_mode2"<?php echo $stockMod[1]; ?> onclick="chk_stock(1);">
                            <label for="ids_stock_mode2">한정</label>
                            <input type="text" name="stock_qty" value="<?php echo number_format($gs['stock_qty']); ?>" class="frm_input w80" onkeyup="addComma(this);"> 개,
                            <b class="marl10">재고 통보수량</b> <input type="text" name="noti_qty" value="<?php echo number_format($gs['noti_qty']); ?>" class="frm_input w80" onkeyup="addComma(this);"> 개
                            <p class="fc_197 mart7">상품의 재고가 통보수량보다 작을 때 상품 재고관리에 표시됩니다.<br>옵션이 있는 상품은 개별 옵션의 통보수량이 적용됩니다. 설정이 무제한이면 재고관리에 표시되지 않습니다.</p>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th scope="row">주문한도</th>
                        <td>
                            <!-- <?php if($type == "normal") { ?> -->
                                최소 <input type="text" name="odr_min" value="<?php echo $gs['odr_min']; ?>" class="frm_input w80"> ~
                                최대 <input type="text" name="odr_max" value="<?php echo $gs['odr_max']; ?>" class="frm_input w80">
                                <span class="fc_197 marl5">미입력시 1개 고정 (무제한일 경우 0개로 설정)</span>
                            <!-- <?php } else { ?>
                                <span class="fc_197 marl5">함께장터 상품은 최소, 최대 구매수량이 1개로 자동 설정됩니다.</span>
                            <?php } ?> -->
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">판매기간 설정</th>
                        <td>
                            <label for="sb_date" class="sound_only">시작일</label>
                            <input type="text" name="sb_date" value="<?php echo $gs['sb_date']; ?>" id="sb_date" class="frm_input"> ~
                            <label for="eb_date" class="sound_only">종료일</label>
                            <input type="text" name="eb_date" value="<?php echo $gs['eb_date']; ?>" id="eb_date" class="frm_input">
                            <a href="javascript:void(0);" class="btn_small is_reset">기간초기화</a>
                            <div class="fc_197 mart7">
                                설정된 기간 동안만 판매 가능하며, 설정된 종료일 이후에는 판매되지 않습니다.<br>
                                일시 판매중지 처리하실 경우, 종료일을 현재날짜 이전의 과거 날짜를 넣어주시면 됩니다.
                            </div>
                            <script>
                            $(function(){
                                // 날짜 검색 : TODAY MAX값으로 인식 (maxDate: "+0d")를 삭제하면 MAX값 해제
                                $('#sb_date').datetimepicker({
                                    format:'Y-m-d H:i',
                                    step: 30,
                                    changeMonth: true,
                                    changeYear: true,
                                    showButtonPanel: true,
                                    onSelectDate: function(ct, $i){
                                        $("#eb_date").datetimepicker('setOptions', { minDate: ct });
                                        $("#eb_date").datetimepicker('setOptions', { minTime: ct });
                                    },
                                    onSelectTime: function(ct, $i){
                                        $("#eb_date").datetimepicker('setOptions', { minDate: ct });
                                        $("#eb_date").datetimepicker('setOptions', { minTime: ct });
                                    }
                                });

                                $('#eb_date').datetimepicker({
                                    format:'Y-m-d H:i',
                                    step: 30,
                                    changeMonth: true,
                                    changeYear: true,
                                    showButtonPanel: true,
                                    onSelectDate:function( ct, $i ){
                                        $("#sb_date").datetimepicker('setOptions', { maxDate: ct });
                                        $("#sb_date").datetimepicker('setOptions', { maxTime: ct });
                                    },
                                    onSelectTime:function( ct, $i ){
                                        $("#sb_date").datetimepicker('setOptions', { maxDate: ct });
                                        $("#sb_date").datetimepicker('setOptions', { maxTime: ct });
                                    },
                                });
                                
                                // 기간초기화
                                $(document).on("click", ".is_reset", function() {
                                    $("#sb_date, #eb_date").val("");
                                });
                            });
                            </script>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">구매가능 레벨</th>
                        <td>
                            <?php echo $buyLevel; ?>
                            <label class="marl5"><input type="checkbox" name="buy_only" value="1"<?php echo $butOnly; ?>> 현재 레벨이상 가격공개</label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    
    <?php echo $frm_submit; ?>
    <section id="anc_sitfrm_sendcost">
        <h2>배송비</h2>
        <?php echo $pg_anchor; ?>
        <div class="local_desc02 local_desc">
            <p>※ <span>참고사항) : 고객이 동일 판매자의 상품을 복수 구매시 배송비는 단 한번만 부과 됩니다. 단! 배송비는 가장 큰값을 산출하여 적용 됩니다.</span></p>
            <p>※ <span>조건부무료배송) : 고객이 동일 판매자의 상품을 복수 구매시 가장 큰 값의 (조건 배송비) 금액을 산출하여 최종배송비가 자동 적용 됩니다.</span></p>
            <p>※ <span>유료배송) : 고객이 동일 판매자의 상품을 복수 구매시 가장 큰 값의 (기본 배송비) 금액을 산출하여 최종배송비가 자동 적용 됩니다.</span></p>
        </div>
        <div class="tbl_frm02">
            <table>
                <colgroup>
                    <col class="w180">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">배송정보</th>
                        <td>
                            <select name="sc_type" onChange="chk_sc_type(this.value);">
                                <?php echo $gsScType[0]; ?>
                                <?php echo $gsScType[1]; ?>
                                <?php echo $gsScType[2]; ?>
                                <?php echo $gsScType[3]; ?>
                            </select>
                            <a href="<?=base_url()?>admin/config?code=baesong" target="_blank" class="btn_small grey">설정</a>
                            <div id="sc_method" class="mart7">
                                배송비결제
                                <select name="sc_method" class="marl10">
                                <?php echo $sc_method[0]; ?>
                                <?php echo $sc_method[1]; ?>
                                <?php echo $sc_method[2]; ?>
                                </select>
                            </div>
                            <div id="sc_amt" class="padt5">
                                기본배송비 <input type="text" name="sc_amt" value="<?php echo number_format($gs['sc_amt']); ?>" class="frm_input w80 marl10" onkeyup="addComma(this);"> 원
                                <label class="marl10"><input type="checkbox" name="sc_each_use" value="1"<?php echo $sc_each_use; ?>> 묶음배송불가</label>
                            </div>
                            <div id="sc_minimum" class="padt5">
                                조건배송비 <input type="text" name="sc_minimum" value="<?php echo number_format($gs['sc_minimum']); ?>" class="frm_input w80 marl10" onkeyup="addComma(this);"> 원 이상이면 무료배송
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">배송가능 지역</th>
                        <td>
                            <select name="zone">
                                <?php echo $zoneArr[0]; ?>
                                <?php echo $zoneArr[1]; ?>
                                <?php echo $zoneArr[2]; ?>
                                <?php echo $zoneArr[3]; ?>
                                <?php echo $zoneArr[4]; ?>
                                <?php echo $zoneArr[5]; ?>
                                <?php echo $zoneArr[6]; ?>
                                <?php echo $zoneArr[7]; ?>
                                <?php echo $zoneArr[8]; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">추가설명</th>
                        <td><input type="text" name="zone_msg" value="<?php echo $gs['zone_msg']; ?>" class="frm_input" size="50" placeholder="예 : 제주 (도서지역 제외)"></td>
                    </tr>
                    <tr>
                        <th scope="row">배송기간</th>
                        <td>
                            <select name="delivery_period">
                                <?php
                                    for($i = 1; $i <= 20; $i++) {
                                        $selected = ($gs['delivery_period'] == $i) ? "selected" : "";
                                        echo "<option value='{$i}' {$selected} >{$i}일</option>";
                                    }
                                ?>
                            </select>
                            <span class="fc_197 marl5">기본값: 1일</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <?php echo $frm_submit; ?>
    <section id="anc_sitfrm_compact">
        <h2>요약정보</h2>
        <?php echo $pg_anchor; ?>
        <div class="local_desc02 local_desc">
            <p><strong>전자상거래 등에서의 상품 등의 정보제공에 관한 고시</strong>에 따라 총 35개 상품군에 대해 상품 특성 등을 양식에 따라 입력할 수 있습니다.</p>
        </div>
        <div class="tbl_frm02">
            <table>
                <colgroup>
                    <col class="w180">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">상품군 선택</th>
                        <td>
                            <select name="info_gubun" id="info_gubun">
                                <option value="">상품군 카테고리 선택</option>
                                <?=$couponOpt?>
                            </select>
		                </td>
	                </tr>
                </tbody>
	        </table>
        </div>
        <script>
            $(function(){
                // 상품정보제공 상품군선택
                $(document).on("change", "#info_gubun", function() {
                    var base_url=document.getElementById('base_url').value;
                    var gubun = $(this).val();
                    $.post(
                        base_url+"admin/goods_info",
                        { gs_id: "<?php echo $gs['index_no']; ?>", gubun: gubun },
                        function(data) {
                            $("#sit_compact_fields").empty().html(data);
                        }
                    );
                });
            });
        </script>
        <div id="sit_compact_fields" class="tbl_frm02 mart7">
            <?=$goods_info?>
        </div>
    </section>
    <?php echo $frm_submit; ?>
    <section id="anc_sitfrm_relation">
        <h2>관련상품</h2>
        <?php echo $pg_anchor; ?>
        <div class="local_desc02 local_desc">
            <p>
                등록된 전체상품 목록에서 카테고리를 선택하면 해당 상품 리스트가 연이어 나타납니다.<br>
                상품리스트에서 관련 상품으로 추가하시면 선택된 관련상품 목록에 <strong>함께</strong> 추가됩니다.<br>
                예를 들어, A 상품에 B 상품을 관련상품으로 등록하면 B 상품에도 A 상품이 관련상품으로 자동 추가되며, <strong>저장 버튼을 누르셔야 정상 반영됩니다.</strong>
            </p>
        </div>
        <div class="srel">
            <div class="compare_wrap">
                <section class="compare_left">
                    <h3>등록된 전체상품 목록</h3>
                    <label for="sch_relation" class="sound_only">카테고리</label>
                    <span class="srel_pad">
                        <?=$get_goods_sca_select?>
                        <label for="sch_name" class="sound_only">상품명</label>
                        <input type="text" name="sch_name" id="sch_name" class="frm_input" size="15">
                        <button type="button" id="btn_search_item" class="btn_small">검색</button>
                    </span>
                    <div id="relation" class="srel_list">
                        <p>카테고리를 선택하시거나 상품명을 입력하신 후 검색하여 주십시오.</p>
                    </div>
                    <script>
                        $(function() {
                            $("#btn_search_item").click(function() {
                                var base_url=document.getElementById('base_url').value;
                                var sca = $("#sch_relation").val();
                                var gname = $.trim($("#sch_name").val());
                                var $relation = $("#relation");

                                if(sca == "" && gname == "") {
                                    $relation.html("<p>카테고리를 선택하시거나 상품명을 입력하신 후 검색하여 주십시오.</p>");
                                    return false;
                                }

                                $("#relation").load(
                                    base_url+"AdminProcess/goods_form_relation",
                                    { gs_id: "<?php echo $gs_id; ?>", mb_id: "<?php echo $gs['mb_id']; ?>", sca: sca, gname: gname }
                                );
                            });

                            $(document).on("click", "#relation .add_item", function() {
                                // 이미 등록된 상품인지 체크
                                var $li = $(this).closest("li");
                                var gs_id = $li.find("input:hidden").val();
                                var gs_id2;
                                var dup = false;
                                $("#reg_relation input[name='re_gs_id[]']").each(function() {
                                    gs_id2 = $(this).val();
                                    if(gs_id == gs_id2) {
                                        dup = true;
                                        return false;
                                    }
                                });

                                if(dup) {
                                    alert("이미 선택된 상품입니다.");
                                    return false;
                                }

                                var cont = "<li>"+$li.html().replace("add_item", "del_item").replace("추가", "삭제")+"</li>";
                                var count = $("#reg_relation li").size();

                                if(count > 0) {
                                    $("#reg_relation li:last").after(cont);
                                } else {
                                    $("#reg_relation").html("<ul>"+cont+"</ul>");
                                }

                                $li.remove();
                            });

                            $(document).on("click", "#reg_relation .del_item", function() {
                                // if(!confirm("상품을 삭제하시겠습니까?"))
                                //    return false;

                                $(this).closest("li").remove();

                                var count = $("#reg_relation li").size();
                                if(count < 1)
                                    $("#reg_relation").html("<p>선택된 상품이 없습니다.</p>");
                            });
                        });
                    </script>
                </section>
                <section class="compare_right">
                    <h3>선택된 관련상품 목록</h3>
                    <span class="srel_pad"></span>
                    <div id="reg_relation" class="srel_sel">
                        <?=$relatedTag?>
                    </div>
                    <input type="hidden" name="gs_list" value="<?php echo $str; ?>">
                </section>
            </div>
        </div>
    </section>
    <?php echo $frm_submit; ?>
    <section id="anc_sitfrm_img">
        <h2>상품이미지 및 상세정보</h2>
        <?php echo $pg_anchor; ?>
        <div class="tbl_frm02">
            <table>
                <colgroup>
                    <col class="w180">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">이미지 등록방식</th>
                        <td class="td_label">
                            <input type="radio" name="simg_type" id="simg_type_1" value="0"<?php echo $simgType[0]; ?> onclick="chk_simg_type(0);">
                            <label for="simg_type_1">직접 업로드</label>
                            <input type="radio" name="simg_type" id="simg_type_2" value="1"<?php echo $simgType[1]; ?> onclick="chk_simg_type(1);">
                            <label for="simg_type_2">URL 입력</label>
                        </td>
                    </tr>
                    <?=$item_img_fld?>
                    <tr>
                        <th scope="row">상세설명</th>
                        <td>
                            <?php echo $memoEdit; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">관리자메모</th>
                        <td><textarea name="admin_memo" class="frm_textbox"><?php echo $gs['admin_memo']; ?></textarea></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <?php echo $frm_submit; ?>
</form>
<script>
function fregform_submit(f) {
    event.preventDefault();

    var base_url = document.getElementById('base_url').value;
    var f = document.getElementById('fregform');
	var multi_caid = new Array();
	var new_caid = "";
    var type = $('input[name=type]').val();
    var w = $('input[name=w]').val();

    var brand_uid = $('select[name=brand_uid] option:selected').val();
    if(brand_uid != '') {
        var doc = $('input[name=document]').val();

        if(doc == '' && w == '') {
            alert('수입필증 / 계약서를 첨부해주세요');
            return false;
        }
    }

	$("select#sel_ca_id option").each(function() {
        new_caid = $(this).val();
        if(new_caid == "")
            return true;

        multi_caid.push(new_caid);
    });

    if(multi_caid.length > 0) {
		$("input[name=ca_id]").val(multi_caid[0]);
		$("input[name=ca_id2]").val(multi_caid[1]);
		$("input[name=ca_id3]").val(multi_caid[2]);
	}

	if(f.ca_id.value == "" && type == "normal") {
        alert("카테고리를 하나이상 선택하세요.");
        return false;
    }

	var sel_count = $("select#sel_ca_id option").size();
	if(sel_count > 3) {
		alert('카테고리는 최대 3개까지만 등록 가능합니다.');
		return false;
	}

	var item = new Array();
    var re_item = gs_id = "";

    $("#reg_relation input[name='re_gs_id[]']").each(function() {
        gs_id = $(this).val();
        if(gs_id == "")
            return true;

        item.push(gs_id);
    });

    if(item.length > 0)
        re_item = item.join();

    $("input[name=gs_list]").val(re_item);

	<?php echo $memoEditJs; ?>

    var formData = new FormData(f);

    $.ajax({
        url : base_url+'AdminProcess/goods_form_update',
        type: 'POST',
        data: formData,
        processData : false,
        contentType : false,
        async: true,
        enctype : 'multipart/form-data',
        beforeSend:function(){
            $('#ajax_loding').show();
            $('body').addClass('noscroll');
        },
        success:function(res){
            $('#ajax_loding').hide();
            $('body').removeClass('noscroll');
            var result = JSON.parse(res);
            alert(result['msg'].trim());
            if(result['res']){
                location.href=base_url+result['url'];
            }
        },error:function(a,b,c){
            console.log(a,b,c);
            $('#ajax_loding').hide();
            $('body').removeClass('noscroll');
            alert('서버에서 오류 발생');
        }
    });
}

// 배송비 설정
function chk_sc_type(type) {
	switch(type) {
		case "0" : // 공통설정
			$("#sc_amt").hide();
			$("#sc_minimum").hide();
			$("#sc_method").show();
			$("input[name=sc_amt]").attr('disabled', true);
			$("input[name=sc_minimum]").attr('disabled', true);
			$("select[name=sc_method]").attr('disabled', false);
			break;
		case "1" : // 무료배송
			$("#sc_amt").hide();
			$("#sc_minimum").hide();
			$("#sc_method").hide();
			$("input[name=sc_amt]").attr('disabled', true);
			$("input[name=sc_minimum]").attr('disabled', true);
			$("select[name=sc_method]").attr('disabled', true);
			break;
		case "2" : // 조건부무료배송
			$("#sc_amt").show();
			$("#sc_minimum").show();
			$("#sc_method").show();
			$("input[name=sc_amt]").attr('disabled', false);
			$("input[name=sc_minimum]").attr('disabled', false);
			$("select[name=sc_method]").attr('disabled', false);
			break;
		case "3" : // 유료배송
			$("#sc_amt").show();
			$("#sc_minimum").hide();
			$("#sc_method").show();
			$("input[name=sc_amt]").attr('disabled', false);
			$("input[name=sc_minimum]").attr('disabled', true);
			$("select[name=sc_method]").attr('disabled', false);
			break;
	}
}
// 이미지 등록방식
function chk_simg_type(type) {
	if(type == 0) { // 직접업로드
		$(".item_file_fld").show();
		$(".item_url_fld").hide();
	} else { // URL 입력
		$(".item_img_fld").show();
		$(".item_file_fld").hide();
		$(".item_url_fld").show();
	}
}

// 재고수량 체크
<?php if($type == "normal") { ?>
function chk_stock(type) {
	if(type == 0) {
		$("input[name=stock_qty]").attr('disabled', true);
		$("input[name=noti_qty]").attr('disabled', true);
	} else {
		$("input[name=stock_qty]").attr('disabled', false);
		$("input[name=noti_qty]").attr('disabled', false);
	}
}
<?php } ?>
// 수수료 적용
function chk_ppay_type(type) {
	if(type == 0) {
		$("input[name=ppay_dan]").attr('disabled', true);
		$("select[name=ppay_rate]").attr('disabled', true);
		$("#chk_ppay_auto").text("가맹점관리 > 가맹점 수수료정책 (기본설정 사용중)");
	} else {
		$("input[name=ppay_dan]").attr('disabled', false);
		$("select[name=ppay_rate]").attr('disabled', false);
		$("#chk_ppay_auto").text("수수료를 적용할 단계를 입력하세요");
	}
}

function chk_ppay_dan(no, index){
    var base_url=document.getElementById('base_url').value;
	$.post(
		base_url+"AdminProcess/goods_form_auto",
		{ "no": no, "index": index },
		function(data) {
			$("#chk_ppay_auto").empty().html(data);
		}
	);
}
</script>

<script>
    chk_sc_type("<?php echo $gs['sc_type']; ?>");
    chk_simg_type("<?php echo $gs['simg_type']; ?>");
    <?php if($type == "normal") { ?>
    chk_stock("<?php echo $gs['stock_mod']; ?>");
    <?php } ?>
    chk_ppay_type("<?php echo $gs['ppay_type']; ?>");
    <?=$ppay_danJs?>
</script>

<script>
    // 총 판매금액 계산
    $("input[name=calculate_price], input[name=stock_qty]").on("keyup", function(key){
        let calculate_price = $('input[name=calculate_price]').val();
        let stock_qty = $('input[name=stock_qty').val();
        let goods_total_price = 0;

        calculate_price = calculate_price.replace(/,/g, '');
        stock_qty = stock_qty.replace(/,/g, '');

        goods_total_price = calculate_price * stock_qty;

        $('input[name=goods_total_price]').val(goods_total_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
    });
</script>
<script>
    function stock_total() {
        const inputs = document.querySelectorAll('input[name="opt_stock_qty[]"]');
        let values = 0;
        // 각 input의 값을 콘솔에 출력
        inputs.forEach((input, index) => {
            values += (parseInt(input.value) > 0) ? parseInt(input.value) : 0;
        });

        console.log(values);
        $('input[name=stock_qty]').val(values);

    }
    
    function removeLeadingZero(input) {
        if (input.value == "0") {
            input.value = "";
        }
    }

    function restoreZeroIfEmpty(input) {
        if (input.value.trim() === "") {
            input.value = "0";
        }
    }

</script>
<?=$headTitleEnd?>