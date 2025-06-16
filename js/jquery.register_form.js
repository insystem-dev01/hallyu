var admin_reg_mb_id_check = function admin_reg_mb_id_check() {
    var base_url = document.getElementById('base_url').value;
    var result = "";
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_mb_id",
        data: {
            "mb_id": encodeURIComponent($("#mb_id").val())
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

//아이디 중복확인 
var reg_mb_id_check = function reg_mb_id_check() {
    var base_url = document.getElementById('base_url').value;
    var mb_id = $("#mb_id").val().trim();

    if (mb_id === '') {
        $('#mb_id_check')
            .text("아이디를 입력해주세요.")
            .css('color', 'black');
        return;
    }
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_mb_id",
        data: {
            "mb_id": encodeURIComponent($("#mb_id").val())
        },
        cache: false,
        async: true,
        beforeSend: function () {
            $('#ajax_loding').show();
            $('body').addClass('noscroll');
        },
        success: function success(data) {
            $('#ajax_loding').hide();
            $('body').removeClass('noscroll');

            if (data.trim()) {
                $('#mb_id_check')
                    .text(data.trim())
                    .css('color', 'red');
                $('#mb_id_check_confirm').val("");
            } else {
                $('#mb_id_check')
                    .text("사용 가능한 아이디입니다.")
                    .css('color', 'green');
                $('#mb_id_check_confirm').val("ok");
            }

            return false;
        },
        error: function error(a, b, c) {
            console.log(a, b, c);
            $('#mb_id_check')
                .text("서버 오류가 발생했습니다.")
                .css('color', 'red');

        }
    });
};

//닉네임 중복확인
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

// 추천인 중복확인
var reg_mb_recommend_check = function reg_mb_recommend_check() {
    var base_url = document.getElementById('base_url').value;
    console.log('reg_mb_recommend_check');
    console.log({
        "mb_id": encodeURIComponent($("#mb_id").val())
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
        "mb_id": encodeURIComponent($("#mb_id").val())
    });
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_mb_email",
        data: {
            "reg_mb_email": $("#reg_mb_email").val(),
            "mb_id": encodeURIComponent($("#mb_id").val())
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
        "mb_id": encodeURIComponent($("#mb_id").val())
    });
    var result = "";
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_mb_hp",
        data: {
            "reg_mb_hp": $("#reg_mb_hp").val(),
            "mb_id": encodeURIComponent($("#mb_id").val())
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

//사업자 중복확인 
var reg_company_number_check = function reg_company_number_check() {
    console.log('reg_company_number');
    var base_url = document.getElementById('base_url').value;
    var company_no_01 = $("#reg_company_no_01").val();
    var company_no_02 = $("#reg_company_no_02").val();
    var company_no_03 = $("#reg_company_no_03").val();

    if (company_no_01.length == 3 || company_no_02.length == 2 || company_no_03.length == 5) {
        console.log({
            "company_no_01": company_no_01,
            "company_no_02": company_no_02,
            "company_no_03": company_no_03
        });

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
            beforeSend: function () {
                $('#ajax_loding').show();
                $('body').addClass('noscroll');
            },
            success: function success(data) {
                $('#ajax_loding').hide();
                $('body').removeClass('noscroll');


                let response = JSON.parse(data);
                // status는 true or false
                if (!response) {
                    alert("서버 오류 발생");
                }

                if (response.status) {
                    $('#company_no_check')
                        .text(response.message)
                        .css('color', 'green');
                    $('#company_no_check_confirm').val("ok");

                    $('#active_company_cert').removeAttr('disabled');
                }
                else {
                    $('#company_no_check')
                        .text(response.message)
                        .css('color', 'red');
                    $('#company_no_check_confirm').val("");

                    $('#active_company_cert').attr('disabled', true);
                }

            },
            error: function error(a, b, c) {
                console.log(a, b, c);
                alert("사업자 등록번호 검증 중 오류 발생");
            }
        });


    }
    else {
        $('#company_no_check')
            .text('사업자 등록번호를 입력해주세요')
            .css('color', 'red');
        $('#company_no_check_confirm').val("");
        return false;
    }
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

    if (company_no_01 == "" || company_no_02 == "" || company_no_03 == "") {
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
            let response = JSON.parse(data);
            // status는 true or false
            if (!response) {
                alert("서버 오류 발생");
            }

            if (response.status) {
                alert(response.message);
                $('#active_company_cert').removeAttr('disabled');
            }
            else {
                alert(response.message);
                $('#active_company_cert').attr('disabled', true);
            }

        },
        error: function error(a, b, c) {
            console.log(a, b, c);
            alert("사업자 등록번호 검증 중 오류 발생");
        }
    });
    return result;
};

//세금 계산서 이메일 중복 체크
var reg_mb_company_tax_email_check = function reg_mb_company_tax_email_check() {
    console.log('reg_mb_company_tax_email_check');
    var base_url = document.getElementById('base_url').value;

    var company_tax_email = $("input[name='company_tax_email']").val();
    var company_tax_email_addr = $("select[name=company_tax_email_addr]").val();
    var company_tax_email_ect = $("input[name='company_tax_email_ect']").val();

    if (company_tax_email == "" || company_tax_email_addr == "") {
        alert('세금계산서 이메일을 입력해주세요.');
        return false;
    }

    console.log({
        "company_tax_email": company_tax_email,
        "company_tax_email_addr": company_tax_email_addr,
        "company_tax_email_ect": company_tax_email_ect,
        "mb_id": encodeURIComponent($("#mb_id").val())
    });
    var result = "";
    $.ajax({
        type: "POST",
        url: base_url + "process/ajax_company_tax_email",
        data: {
            "company_tax_email": company_tax_email,
            "company_tax_email_addr": company_tax_email_addr,
            "company_tax_email_ect": company_tax_email_ect,
            "mb_id": encodeURIComponent($("#mb_id").val())
        },
        cache: false,
        async: false,
        success: function success(data) {
            let response = JSON.parse(data);
            // status는 true or false
            if (!response) {
                alert("서버 오류 발생");
            }

            if (response.status) {

                $('#company_tax_email_check')
                    .text(response.message)
                    .css('color', 'green');
                $('#company_tax_email_check_confirm').val("ok");

            }
            else {

                $('#company_tax_email_check')
                    .text(response.message)
                    .css('color', 'red');
                $('#company_tax_email_check_confirm').val("");


            }
        },
        error: function error(a, b, c) {
            console.log(a, b, c);
            alert("세금 계산서 이메일 검증 중 오류 발생");
        }
    });
    return result;
};



// 비밀번호 확인
function pwd_check(input) {
    var value = $(input).val().trim();

    if (value.length > 7 && /[a-zA-Z]/.test(value) && /[0-9]/.test(value) && /[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/.test(value)) {
        $('#pwdDigitConfirm').val('ok');
        $("#pwdInfo")
            .text("사용가능한 비밀번호입니다.")
            .css("color", "green")
            .show();
    }
    else if (value.length == 0) {
        $('#pwdDigitConfirm').val('');
        $("#pwdInfo")
            .text("비밀번호를 입력해주세요.")
            .css('color', 'red')
            .show();
    }
    else {
        $('#pwdDigitConfirm').val('');
        $("#pwdInfo")
            .text("영문 소문자, 숫자, 특수문자 3가지를 조합하여 8자 이상 입력해주세요.")
            .css('color', 'red')
            .show();
    }
}

// 비밀번호 2차 중복 확인
function pwd_dup_check(input) {

    let dup_password = $(input).val().trim();
    let password = $("input[name=mb_password]").val();

    if (password == '' && dup_password == '') {
        $("#pwdDupInfo")
            .text("비밀번호를 입력해주세요")
            .css("color", "red")
            .show();

    }
    if (password == dup_password) {
        $('#pwdDupConfirm').val('ok');
        $("#pwdDupInfo")
            .text("비밀번호가 일치합니다")
            .css('color', 'green')
            .show();
    }
    if (password != dup_password) {
        $('#pwdDupConfirm').val('');
        $("#pwdDupInfo")
            .text("비밀번호가 일치하지 않습니다.")
            .css('color', 'red')
            .show();

    }
}

//주소검색 체크
function mb_addr_check() {
    const addr1 = $('input[name="mb_addr1"]').val().trim();
    const addr3 = $('input[name="mb_addr3"]').val().trim();
    const addr2 = $('input[name="mb_addr2"]').val().trim();
    const zip = $('input[name="mb_zip"]').val().trim();

    const isValid = addr1 && addr3 && addr2 && zip;

    if (isValid) {
        $('#mb_addr_check')
            .text("✔")
            .css('color', 'green')
            .show();

        $('#mb_addr_check_confirm').val("ok");
    } else if (addr1 && addr3 && zip && addr2.length === 0) {
        $('#mb_addr_check')
            .text("상세 주소를 입력해주세요")
            .css('color', 'red')
            .show();

        $('#mb_addr_check_confirm').val("");
    }
    else {
        $('#mb_addr_check').hide().text("");
        $('#mb_addr_check_confirm').val("");
    }

}




//일반 텍스트 기본 체크
function basic_text_check(id, message) {
    var value = $(`#${id}`).val().trim();
    const check_div = $(`#${id}_check`);
    const confirm_input = $(`#${id}_check_confirm`);

    if (value.length === 0) {
        check_div.text(message).css('color', 'red').show();
        confirm_input.val("");
    }
    else {
        check_div.text("✔").css('color', 'green').show();
        confirm_input.val("ok");
    }
}

//휴대폰 및 집전화 체크
function tel_check_by_id({ ids, lengths, is_only_phone, check_id, confirm_id, checked_mesage, unchecked_message }) {
    const values = ids.map(id => $(`#${id}`).val().trim());
    let is_valid;

    if (is_only_phone) { // 010-1234-5678 고정적
        is_valid = values.every((val, idx) => val.length == lengths[idx]);
    }
    //집번호 형식인 경우 02-514-1234 051
    else {
        is_valid = values.every((val, idx) => val.length >= lengths[idx]);
    }

    if (is_valid) {
        $(`#${check_id}`)
            .text(checked_mesage)
            .css('color', 'green')
            .show();
        $(`#${confirm_id}`).val("ok");
    }
    else {
        $(`#${check_id}`)
            .text(unchecked_message)
            .css('color', 'red')
            .show();
        $(`#${confirm_id}`).val("");
    }
}

// 중복확인 버튼 있는 것들 체크
function show_duplicate_check_notice({ input, check_id, confirm_id, empty_message = "입력해주세요" }) {
    const value = $(input).val().trim();

    if (value.length > 0) {
        $(`#${check_id}`)
            .text("중복확인을 진행해주세요.")
            .css('color', 'black');

        $(`#${confirm_id}`).val("");
    }
    else {
        $(`#${check_id}`)
            .text(empty_message)
            .css('color', 'red');
        $(`#${confirm_id}`).val("");

    }
}


//체크 박스있는 것들 체크
function checkbox_group_check({ name, check_id, confirm_id, checked_mesage, unchecked_message }) {

    const checked = $(`input[name="${name}[]"]:checked`).length;

    if (checked === 0) {
        $(`#${check_id}`).text(unchecked_message).css('color', 'red').show();
        $(`#${confirm_id}`).val("");
    }
    else {
        $(`#${check_id}`).text(checked_mesage).css('color', 'green').show();
        $(`#${confirm_id}`).val("ok");
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

//파일 첨부시 첨부했다는 것을 보여주기 위해서
function preview_file(file_id, show_id, check_id, confirm_id) {
    var file_input = document.getElementById(file_id);
    var file = file_input.files[0];

    if (!file) {
        alert("파일을 선택해주세요.");
        return;
    }

    var allow_extensions = /(\.jpg|\.jpeg|\.png|\.pdf)$/i;

    const checkDiv = document.getElementById(check_id);
    if (!allow_extensions.exec(file.name)) {
        alert("jpg, jpeg, png, pdf 형식의 파일만 첨부 가능합니다.");
        file_input.value = '';
        document.getElementById(show_id).value = '';
        checkDiv.style.display = "none";
        document.getElementById(confirm_id).value = "";
        return;
    }

    document.getElementById(show_id).value = file.name;
    checkDiv.textContent = "✔"; // ✅ ✔ 문자 출력
    checkDiv.style.color = "green";
    checkDiv.style.display = "inline";

    document.getElementById(confirm_id).value = "ok";
}
