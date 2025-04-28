$(function() {
    var el_id = document.getElementById("daum_juso_wrap");
    new daum.Postcode({
        oncomplete: function(data) {
            var address1 = "", 
                address2 = "";
            // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
            if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                address1 = data.roadAddress;

                //법정동명이 있을 경우 추가한다.
                if(data.bname !== ''){
                    address2 += data.bname;
                }
                // 건물명이 있을 경우 추가한다.
                if(data.buildingName !== ''){
                    address2 += (address2 !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                address2 = (address2 !== '' ? ' ('+ address2 +')' : '');
            } else { // 사용자가 지번 주소를 선택했을 경우(J)
                address1 = data.jibunAddress;
            }

            put_data5(data.zonecode, address1, "", address2, data.addressType);
        },
        width : "100%",
        height : "100%"
    }).embed(el_id);
});