<form name="forderaddress" method="post">
    <div id="sod_addr" class="new_win">
        <h1 id="win_title">배송지 목록</h1>

        <div class="win_desc">
            <ul>
                <?=$addrList?>
            </ul>
        </div>
        <div class="win_btn">
            <button type="button" class="btn_medium bx-white" onclick="self.close();">창닫기</button>
        </div>
    </div>
</form>

<script>
$(function() {
    $(".sel_address").on("click", function() {
        var addr = $(this).siblings("input").val().split(String.fromCharCode(30));

        var f = window.opener.buyform;
		f.b_name.value			= addr[0];
		f.b_cellphone.value		= addr[1];
		f.b_telephone.value		= addr[2];
		f.b_zip.value			= addr[3];
		f.b_addr1.value			= addr[4];
		f.b_addr2.value			= addr[5];
		f.b_addr3.value			= addr[6];
		f.b_addr_jibeon.value	= addr[7];

        var zip = addr[3].replace(/[^0-9]/g, "");
        if(zip != "") {
            var code = String(zip);
			window.opener.calculate_sendcost(code);
        }

        window.close();
    });
});
</script>
