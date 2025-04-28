var reg_pt_sms_bank = function() {
    var result = "";
    $.ajax({
        type: "POST",
        url: om_bbs_url+"/ajax.pt_bank.php",
        data: {
            "reg_hp": $("#reg_hp").val(),
            "reg_bank": $("select[name=bank_acc]").val(),
			"reg_price": parseInt($("input[name=receipt_price]").val())
        },
        cache: false,
        async: false,
        success: function(data) {
            result = data;
        }
    });
    return result;
}

var calculate_total_price = function() {
	var type = $("input[name=reg_level]:checked").val();				
	var info = type.split('^');
	var level = parseInt(info[0]);
	var price = parseInt(info[1]);	
	$("input[name=anew_grade]").val(level);
	$("input[name=receipt_price]").val(price);

	if(price > 0) {
		$("#reg_tot_price").empty().html('<em>'+number_format(String(price))+'</em>원 (부가세 포함)');	
	} else {
		$("#reg_tot_price").empty().html("무료");	
	}
}

var shown = true;
var sign_toggle = function() {
   if(shown) {
	   $(".blink").css("color","#0fc5b5");
	   shown = false;
   } else {
	   $(".blink").css("color","#ec0e03");
	   shown = true;
   }
}

setInterval(sign_toggle, 500);

$(function() {
	$("#sign").signature();
	$("#clear").click(function() {
		$("#sign").signature("clear");
	});
	$("#sign").signature({syncField: "#signatureJSON"});

	$(".btn_sms_send").click(function() {
		var msg = reg_pt_sms_bank();
		if(msg) {
			alert(msg);
		}
	});

	$("input[name=reg_level]").click(function() {
		calculate_total_price();
	});	
});