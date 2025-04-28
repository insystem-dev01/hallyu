<form name="forderaddress" id="forderaddress" method="post" class="new_win">
    <h1 id="win_title"><?php echo $title; ?></h1>
    <div class="tbl_head02 tbl_wrap">
        <table>
            <colgroup>
                <col class="w50">
                <col>
                <col class="w50">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col">주소</th>
                    <th scope="col">선택</th>
                </tr>
            </thead>
            <tbody>
                <?=$addressList?>
            </tbody>
	    </table>
    </div>
    <div class="win_btn">
        <a href="javascript:window.close()" class="btn_lsmall bx-white">창닫기</a>
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
