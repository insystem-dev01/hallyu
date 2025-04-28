var admin_reg_mb_id_check = function admin_reg_mb_id_check() {
    var base_url = document.getElementById('base_url').value;
    var result = "";
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_mb_id",
        data: {
            "reg_mb_id": encodeURIComponent($("#reg_mb_id").val())
        },
        cache: false,
        async: true,
        beforeSend: function () {
            $('#ajax_loding').show();
            $('body').addClass('noscroll');
        },
        success: function success(data) {
            $('#ajax_loding').hide();
            if (data.trim()) {
                alert(data.trim());
            }
            
            return false;
        },
        error: function error(a, b, c) {
            console.log(a, b, c);
        }
    });
};

var reg_mb_id_check = function reg_mb_id_check() {
    var base_url = document.getElementById('base_url').value;
    var result = "";
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_mb_id",
        data: {
            "reg_mb_id": encodeURIComponent($("#reg_mb_id").val())
        },
        cache: false,
        async: true,
        beforeSend: function () {
            $('#ajax_loding').show();
            $('body').addClass('noscroll');
        },
        success: function success(data) {
            $('#ajax_loding').hide();
            if (data.trim()) {
                alert(data.trim());
            } else {
                alert("사용 가능한 아이디입니다.");
            }
            
            return false;
        },
        error: function error(a, b, c) {
            console.log(a, b, c);
        }
    });
};

var reg_mb_nickname_check = function reg_mb_nickname_check() {
    var base_url = document.getElementById('base_url').value;
    var result = "";
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_mb_nickname",
        data: {
            "reg_mb_nickname": $("#reg_mb_nickname").val()
        },
        cache: false,
        async: true,
        beforeSend: function () {
            $('#ajax_loding').show();
            $('body').addClass('noscroll');
        },
        success: function success(data) {
            $('#ajax_loding').hide();
            if (data.trim()) {
                alert(data.trim());
            } else {
                alert("사용 가능한 닉네임입니다.");
            }
            
            return false;
        },
        error: function error(a, b, c) {
            console.log(a, b, c);
        }
    });
};
  
  
var reg_mb_recommend_check = function reg_mb_recommend_check() {
    var base_url = document.getElementById('base_url').value;
    console.log('reg_mb_recommend_check');
    console.log({
        "reg_mb_id": encodeURIComponent($("#reg_mb_id").val())
    });
    var result = "";
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_mb_recommend",
        data: {
        "reg_mb_recommend": encodeURIComponent($("#reg_mb_recommend").val())
        },
        cache: false,
        async: false,
        success: function success(data) {
        result = data;
        },
        error: function error(a, b, c) {
        console.log(a, b, c);
        }
    });
    return result;
};

var reg_mb_email_check = function reg_mb_email_check() {
    console.log('reg_mb_email_check');
    var base_url = document.getElementById('base_url').value;
    var result = "";
    console.log({
        "reg_mb_email": $("#reg_mb_email").val(),
        "reg_mb_id": encodeURIComponent($("#reg_mb_id").val())
    });
    $.ajax({
        type : "POST",
        url: base_url + "process/ajax_mb_email",
        data: {
            "reg_mb_email": $("#reg_mb_email").val(),
            "reg_mb_id": encodeURIComponent($("#reg_mb_id").val())
        },
        cache: false,
        async: false,
        success: function success(data) {
            result = data;
        },
        error: function error(a, b, c) {
            console.log(a, b, c);
        }
    });
    return result;
};

var reg_mb_hp_check = function reg_mb_hp_check() {
    console.log('reg_mb_hp_check');
    var base_url = document.getElementById('base_url').value;
    console.log({
        "reg_mb_hp": $("#reg_mb_hp").val(),
        "reg_mb_id": encodeURIComponent($("#reg_mb_id").val())
    });
    var result = "";
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_mb_hp",
        data: {
        "reg_mb_hp": $("#reg_mb_hp").val(),
        "reg_mb_id": encodeURIComponent($("#reg_mb_id").val())
        },
        cache: false,
        async: false,
        success: function success(data) {
        result = data;
        },
        error: function error(a, b, c) {
        console.log(a, b, c);
        }
    });
    return result;
};

var reg_company_number_check = function reg_company_number_check() {
    console.log('reg_company_number');
    var base_url = document.getElementById('base_url').value;
    var company_no_01 = $("#reg_company_no_01").val();
    var company_no_02 = $("#reg_company_no_02").val();
    var company_no_03 = $("#reg_company_no_03").val();

    if(company_no_01 == "" || company_no_02 == "" || company_no_03 == "") {
        alert('사업자 등록번호를 입력해주세요');
        return false;
    }
    
    console.log({
        "company_no_01": company_no_01,
        "company_no_02": company_no_02,
        "company_no_03": company_no_03
    });
    var result = "";
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_company_number",
        data: {
        "company_no_01": company_no_01,
        "company_no_02": company_no_02,
        "company_no_03": company_no_03
        },
        cache: false,
        async: false,
        success: function success(data) {
            if(data != "") {
                alert(data);
            } else {
                alert('사용 가능한 사업자등록번호입니다.')
            }
        },
        error: function error(a, b, c) {
        console.log(a, b, c);
        }
    });
    return result;
};

//사업자 등록증 파일 첨부
var admin_reg_company_number_check = function admin_reg_company_number_check() {
    console.log('admin_reg_company_number');
    var base_url = document.getElementById('base_url').value;

    var company_no = $("input[name='company_saupja_no']").val();

    var company_arr = company_no.split('-');

    var company_no_01 = company_arr[0];
    var company_no_02 = company_arr[1];
    var company_no_03 = company_arr[2];

    if(company_no_01 == "" || company_no_02 == "" || company_no_03 == "") {
        alert('사업자 등록번호를 입력해주세요');
        return false;
    }
    
    console.log({
        "company_no_01": company_no_01,
        "company_no_02": company_no_02,
        "company_no_03": company_no_03
    });
    var result = "";
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_company_number",
        data: {
        "company_no_01": company_no_01,
        "company_no_02": company_no_02,
        "company_no_03": company_no_03
        },
        cache: false,
        async: false,
        success: function success(data) {
            if(data != "") {
                alert(data);
            } else {
                alert('사용 가능한 사업자등록번호입니다.')
            }
        },
        error: function error(a, b, c) {
        console.log(a, b, c);
        }
    });
    return result;
};

var reg_mb_company_tax_email_check = function reg_mb_company_tax_email_check() {
    console.log('reg_mb_company_tax_email_check');
    var base_url = document.getElementById('base_url').value;

    var company_tax_email = $("input[name='company_tax_email']").val();
    var company_tax_email_addr = $("select[name=company_tax_email_addr]").val();
    var company_tax_email_ect = $("input[name='company_tax_email_ect']").val();

    if(company_tax_email == "" || company_tax_email_addr == "") {
        alert('세금계산서 이메일을 입력해주세요.');
        return false;
    }
    
    console.log({
        "company_tax_email": company_tax_email,
        "company_tax_email_addr": company_tax_email_addr,
        "company_tax_email_ect": company_tax_email_ect,
        "mb_id" : encodeURIComponent($("#reg_mb_id").val())
    });
    var result = "";
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_company_tax_email",
        data: {
        "company_tax_email": company_tax_email,
        "company_tax_email_addr": company_tax_email_addr,
        "company_tax_email_ect": company_tax_email_ect,
        "mb_id" : encodeURIComponent($("#reg_mb_id").val())
        },
        cache: false,
        async: false,
        success: function success(data) {
            if(data != "") {
                alert(data);
            } else {
                alert('사용 가능한 세금계산서 이메일 주소입니다.')
            }
        },
        error: function error(a, b, c) {
        console.log(a, b, c);
        }
    });
    return result;
};

// 비밀번호 확인
function pwd_check(e) {
    var that=$(e);
    var value=that.val();

    if (value.length > 7 && /[a-zA-Z]/.test(value) && /[0-9]/.test(value) && /[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/.test(value)) {
        $('#pwdDigitConfirm').val('ok');
        $("#pwdInfo").text("");
        $("#pwdInfo").hide();
        
    } else {
        $('#pwdDigitConfirm').val('');
        $("#pwdInfo")
            .text("영문자, 숫자, 특수문자 3가지를 조합하여 8자 이상 입력해주세요.")
            .css('color', 'red')
            .css('font-size', '17px');
        $("#pwdInfo").show();
    }
}

// 비밀번호 중복 확인
function pwd_dup_check(e) {
    let that         = $(e);
    let dup_password = that.val();
    let password     = $("input[name=mb_password]").val();

    if (password == '' && dup_password == '') {
        $("#pwdDupInfo").text("");
        $("#pwdDupInfo").hide();
        return;
    }
    if (password == dup_password) {
        $('#pwdDupConfirm').val('ok');
        $("#pwdDupInfo")
            .text("비밀번호가 일치합니다")
            .css('color', '#000')
            .css('font-size', '17px');
        $("#pwdDupInfo").show();
        return;
    }
    if (password != dup_password) {
        $('#pwdDupConfirm').val('');
        $("#pwdDupInfo")
            .text("비밀번호가 일치하지 않습니다.")
            .css('color', 'red')
            .css('font-size', '17px');
        $("#pwdDupInfo").show();
        return;
    }
}

function pwd_look(btn) {
    var $btn = $(btn);
    var $input = $btn.siblings('input[type="password"], input[type="text"]'); // 옆의 input 찾기
    var currentType = $input.attr('type');
    var newType = currentType === 'password' ? 'text' : 'password';

    // clone 방식으로 type 변경
    var $clone = $input.clone();
    $clone.attr('type', newType);
    $input.replaceWith($clone);

    // 버튼 텍스트 변경
    if (newType === 'text') {
        $btn.text('비밀번호 숨김');
    } else {
        $btn.text('비밀번호 확인');
    }
}
