// 전역 변수
var errmsg = "";
var errfld = null;

// 필드 검사
function check_field(fld, msg)
{
    if ((fld.value = trim(fld.value)) == "")
        error_field(fld, msg);
    else
        clear_field(fld);
    return;
}

// 필드 오류 표시
function error_field(fld, msg)
{
    if (msg != "")
        errmsg += msg + "\n";
    if (!errfld) errfld = fld;
    fld.style.background = "#FFE4E1";
}

// 필드를 깨끗하게
function clear_field(fld)
{
    fld.style.background = "#FFFFFF";
}

// 양쪽 공백 없애기
function trim(sVal)
{
    var pattern = /(^\s*)|(\s*$)/g; // \s 문자열 시작부분과 문자열 끝나는 부분의 공백, 탭을 모두 찾는다.
    sReturnVal = sVal.replace(pattern, "");
    return sReturnVal;
}

// 숫자 확인
function checkNum(value, isDec) {
	var RegExp;

	if (!isDec) isDec = false;
	RegExp = (isDec) ? /^-?[\d\.]*$/ : /^-?[\d]*$/;

	return RegExp.test(value)? true : false;
}

// 자바스크립트로 PHP의 number_format 흉내를 냄
// 숫자에 , 를 출력
function number_format(num, pos) {
	if (!pos) pos = 0;  //소숫점 이하 자리수
	var re = /(-?\d+)(\d{3}[,.])/;

	var strNum = no_comma(num.toString());
	var arrNum = strNum.split(".");

	arrNum[0] += ".";

    while (re.test(arrNum[0])) {
        arrNum[0] = arrNum[0].replace(re, "$1,$2");
    }

	if (arrNum.length > 1) {
		if (arrNum[1].length > pos) {
			arrNum[1] = arrNum[1].substr(0, pos);
		}
		return arrNum.join("");
	}
	else {
		return arrNum[0].split(".")[0];
	}
}

// , 를 없앤다.
function no_comma(data)
{
    var tmp = '';
    var comma = ',';
    var i;

    for (i=0; i<data.length; i++)
    {
        if (data.charAt(i) != comma)
            tmp += data.charAt(i);
    }
    return tmp;
}

// 쿠키 입력
function set_cookie(name, value, expirehours, domain)
{
    var today = new Date();
    today.setTime(today.getTime() + (60*60*1000*expirehours));
    document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + today.toGMTString() + ";";
    if (domain) {
        document.cookie += "domain=" + domain + ";";
    }
}

// 쿠키 얻음
function get_cookie(name)
{
    var find_sw = false;
    var start, end;
    var i = 0;

    for (i=0; i<= document.cookie.length; i++)
    {
        start = i;
        end = start + name.length;

        if(document.cookie.substring(start, end) == name)
        {
            find_sw = true
            break
        }
    }

    if (find_sw == true)
    {
        start = end + 1;
        end = document.cookie.indexOf(";", start);

        if(end < start)
            end = document.cookie.length;

        return unescape(document.cookie.substring(start, end));
    }
    return "";
}

// 쿠키를 호출 함수
function getCookieVal(offset)
{
	var endstr = document.cookie.indexOf (";", offset);
	if (endstr == -1) endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}


// 쿠키에 등록된 값중 해당 이름의 쿠키값을 찾는 함수
function GetCookie(name)
{
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;
	while (i < clen) //while open
	{
		var j = i + alen;
		if (document.cookie.substring(i, j) == arg)
			return getCookieVal (j);
		i = document.cookie.indexOf(" ", i) + 1;
		if (i == 0) break;
	} //while close
	return null;
}

// 쿠키를 저장 하는 함수
function SetCookie(name, value)
{
	var argv = SetCookie.arguments;
	var argc = SetCookie.arguments.length;
	var expires = (2 < argc) ? argv[2] : null;
	var path = (3 < argc) ? argv[3] : null;
	var domain = (4 < argc) ? argv[4] : null;
	var secure = (5 < argc) ? argv[5] : false;
	document.cookie = name + "=" + escape (value) +
		((expires == null) ? "" :
		("; expires=" + expires.toGMTString())) +
		((path == null) ? "" : ("; path=" + path)) +
		((domain == null) ? "" : ("; domain=" + domain)) +
		((secure == true) ? "; secure" : "");
}

// a 태그에서 onclick 이벤트를 사용하지 않기 위해
function win_open(url, name)
{
	var popup = window.open(url, name);
	popup.focus();
}

/**
 * 우편번호 창
 **/
var win_zip = function(frm_name, frm_zip, frm_addr1, frm_addr2, frm_addr3, frm_jibeon) {
    if(typeof daum === 'undefined'){
        alert("다음 우편번호 postcode.v2.js 파일이 로드되지 않았습니다.");
        return false;
    }

    var zip_case = 2;   //0이면 레이어, 1이면 페이지에 끼워 넣기, 2이면 새창

	var complete_fn = function(data){
        // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

        // 각 주소의 노출 규칙에 따라 주소를 조합한다.
        // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
        var fullAddr = ''; // 최종 주소 변수
        var extraAddr = ''; // 조합형 주소 변수

        // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
        if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
            fullAddr = data.roadAddress;

        } else { // 사용자가 지번 주소를 선택했을 경우(J)
            fullAddr = data.jibunAddress;
        }

        // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
        if(data.userSelectedType === 'R'){
            //법정동명이 있을 경우 추가한다.
            if(data.bname !== ''){
                extraAddr += data.bname;
            }
            // 건물명이 있을 경우 추가한다.
            if(data.buildingName !== ''){
                extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
            }
            // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
            extraAddr = (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
        }

        // 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
        var of = document[frm_name];

        of[frm_zip].value = data.zonecode;

        of[frm_addr1].value = fullAddr;
        of[frm_addr3].value = extraAddr;

        if(of[frm_jibeon] !== undefined){
            of[frm_jibeon].value = data.userSelectedType;
        }

        of[frm_addr2].focus();
    };

    switch(zip_case) {
        case 1 :    //iframe을 이용하여 페이지에 끼워 넣기
            var daum_pape_id = 'daum_juso_page'+frm_zip,
                element_wrap = document.getElementById(daum_pape_id),
                currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
            if (element_wrap == null) {
                element_wrap = document.createElement("div");
                element_wrap.setAttribute("id", daum_pape_id);
                element_wrap.style.cssText = 'display:none;border:1px solid;left:0;width:100%;height:300px;margin:5px 0;position:relative;-webkit-overflow-scrolling:touch;';
                element_wrap.innerHTML = '<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;right:0px;top:-21px;z-index:1" class="close_daum_juso" alt="접기 버튼">';
                jQuery('form[name="'+frm_name+'"]').find('input[name="'+frm_addr1+'"]').before(element_wrap);
                jQuery("#"+daum_pape_id).off("click", ".close_daum_juso").on("click", ".close_daum_juso", function(e){
                    e.preventDefault();
                    jQuery(this).parent().hide();
                });
            }

            new daum.Postcode({
                oncomplete: function(data) {
                    complete_fn(data);
                    // iframe을 넣은 element를 안보이게 한다.
                    element_wrap.style.display = 'none';
                    // 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
                    document.body.scrollTop = currentScroll;
                },
                // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분.
                // iframe을 넣은 element의 높이값을 조정한다.
                onresize : function(size) {
                    element_wrap.style.height = size.height + "px";
                },
                width : '100%',
                height : '100%'
            }).embed(element_wrap);

            // iframe을 넣은 element를 보이게 한다.
            element_wrap.style.display = 'block';
            break;
        case 2 :    //새창으로 띄우기
            new daum.Postcode({
                oncomplete: function(data) {
                    complete_fn(data);
                }
            }).open();
            break;
        default :   //iframe을 이용하여 레이어 띄우기
            var rayer_id = 'daum_juso_rayer'+frm_zip,
                element_layer = document.getElementById(rayer_id);
            if (element_layer == null) {
                element_layer = document.createElement("div");
                element_layer.setAttribute("id", rayer_id);
                element_layer.style.cssText = 'display:none;border:5px solid;position:fixed;width:300px;height:460px;left:50%;margin-left:-155px;top:50%;margin-top:-235px;overflow:hidden;-webkit-overflow-scrolling:touch;z-index:10000';
                element_layer.innerHTML = '<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" class="close_daum_juso" alt="닫기 버튼">';
                document.body.appendChild(element_layer);
                jQuery("#"+rayer_id).off("click", ".close_daum_juso").on("click", ".close_daum_juso", function(e){
                    e.preventDefault();
                    jQuery(this).parent().hide();
                });
            }

            new daum.Postcode({
                oncomplete: function(data) {
                    complete_fn(data);
                    // iframe을 넣은 element를 안보이게 한다.
                    element_layer.style.display = 'none';
                },
                width : '100%',
                height : '100%'
            }).embed(element_layer);

            // iframe을 넣은 element를 보이게 한다.
            element_layer.style.display = 'block';
    }
}

// Select 설정값 가져오기 ##################################################
function getSelectVal(obj) {
	var value = "";
	var idx = obj.selectedIndex;

	if (idx >= 0){
		value = obj.options[idx].value;
	}

	return value;
}

// Radio(CheckBox) 설정값 가져오기 ##################################################
function getRadioVal(obj) {
	var i, value = "";

	if (obj) {
		if (typeof(obj.length) == "undefined") {
			if (obj.checked) {
				value = obj.value;
			}
		}
		else {
			for (i=0; i<obj.length; i++) {
				if (obj[i].checked) {
					value = obj[i].value;
					break;
				}
			}
		}
	}
	return value;
}

// Sub ID		: getRadioValue
// Description	: 체크된 라디오버튼 값
// Param		: obj	- RadioButton object
// Return		: 체크된 라디오버튼 값
function getRadioValue(obj){
	if(obj){
		if(obj.length){
			for(i=0;i<obj.length;i++){
				if(obj[i].checked == true){
					return obj[i].value;
				}
			}

		}else{
			return obj.value;
		}
	} else { return false; }
}

// SNS
function share_sns(id, url) {
	switch(id) {
		case 'facebook' : 
			window.open(url, "win_facebook", "menubar=0,resizable=1,width=600,height=400"); 
			break;
		case 'twitter' : 
			window.open(url, "win_twitter", "menubar=0,resizable=1,width=600,height=400"); 
			break;
		case 'googleplus' : 
			window.open(url, "win_googleplus", "menubar=0,resizable=1,width=600,height=600"); 
			break;
		case 'naverband' :
			window.open(url, "win_naverband", "menubar=0,resizable=1,width=410,height=540"); 
			break;
		case 'naver' : 
			window.open(url, "win_naver", "menubar=0,resizable=1,width=450,height=540"); 
			break;
		case 'kakaostory' :
			window.open(url, "win_kakaostory", "menubar=0,resizable=1,width=500,height=500"); 
			break;
		case 'pinterest' :
			window.open(url, "win_tumblr", "menubar=0,resizable=1,width=600,height=400"); 
			break;
		case 'tumblr' :
			window.open(url, "win_tumblr", "menubar=0,resizable=1,width=540,height=600"); 
			break;
	}
    return false;
}

// 주문 구매확정
function final_confirm(hash){
	var base_url=document.getElementById('base_url').value;
	if(confirm("구매확정 하시겠습니까?")) {
		var save_result = "";
		$.ajax({
			type: "POST",
			data: { "hash": hash },
			url: base_url+"process/ajax_orderfinalsave",
			cache: false,
			async: false,
			success: function(data) {
				save_result = data;
                location.reload();
			}
		});

		if(save_result) {
			alert(save_result);
			return false;
		}

		location.reload();
    } else {
        return false;
    }
}

function itemlistwish(gs_id) {
	var base_url=document.getElementById('base_url').value;

	$.post(
        base_url+"process/ajax_wishupdate",
        { gs_id: gs_id },
        function(data) {
            data=data.trim();
			if(data == 'INSERT') {
				$("."+gs_id).attr('class',gs_id+' zzim on');
			} else if(data == 'DELETE') {
				$("."+gs_id).attr('class',gs_id+' zzim');
			} else {
				alert(data);
			}
        }
    );
}

function saupjaonopen(saupjano) {
	var url = "http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no="+saupjano;
	window.open(url, "communicationViewPopup");
}

// 이미지의 크기를 1:1로
$(document).on('ready', function() {
    // bid
    var pr_width = $('.slick-slide').children('a').children('dt').width();
    $('.slick-slide').children('a').children('dt').height(pr_width);
    // 카테고리별 베스트
    $('.pimg').height('33vw');
    // 나머지 상품
    var width = $('a').children('dl').children('dt').width();
    $('a').children('dl').children('dt').height(width);
});


// 휴대폰 번호 입력
function phone_typing(e) {
    const that = $(e);
    const input = that.val();
    let formatted = '';

	formatted = input.replace(/[^0-9]/g, '').replace(/^(\d{2,3})(\d{3,4})(\d{4})$/, `$1-$2-$3`);

    that.val(formatted);
}

// 숫자 입력
function only_num_typing(e) {
    const that  = $(e);
    const value = that.val();
    that.val(value.replace(/[^0-9]/gi, ''));
}

// 휴대폰 번호 입력
function only_phone_text_typing(e) {
    const that  = $(e);
    const value = that.val();
    that.val(value.replace(/[^0-9-]/gi, ''));
}

// 영어 + 숫자 입력
function only_eng_num_typing(e) {
    const that  = $(e);
    const value = that.val();
    that.val(value.replace(/[^a-z0-9]/gi, ''));
}

// 영어 + 숫자 + 특문 입력
function only_eng_num_text_typing(e) {
    const that  = $(e);
    const value = that.val();
    that.val(value.replace(/[^a-z0-9_]/gi, ''));
}

// 영어 + 숫자 + 한글 입력
function only_eng_num_kor_typing(e) {
    const that  = $(e);
    const value = that.val();
    that.val(value.replace(/[^a-z0-9가-힣]/gi, ''));
}

// 영어 + 한글 입력
function only_eng_kor_typing(e) {
    const that  = $(e);
    const value = that.val();
    that.val(value.replace(/[^a-z가-힣]/gi, ''));
}

// 사업자 등록번호 입력
function bizNoFormatter(e) {

	var that = $(e);
	var input = that.val();
	var digits = input.replace(/\D/g, ''); // 숫자만 추출
	var formatted = '';

	if (digits.length > 3) {
		formatted += digits.substring(0, 3) + '-';
		digits = digits.substring(3);
	}
	if (digits.length > 4) {
		formatted += digits.substring(0, 2) + '-';
		digits = digits.substring(2);
	}
	formatted += digits;

	that.val(formatted);
}