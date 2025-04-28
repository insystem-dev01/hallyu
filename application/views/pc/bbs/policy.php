<h2 class="pg_tit">
	<span><?php echo $title; ?></span>
	<p class="pg_nav">HOME<i>&gt;</i><?php echo $title; ?></p>
</h2>
<?=$shop_policy?>

<button
    class="btn btn-default"
    type="button"
    id="BTN_10000028_20250329_000001"
    tabindex="10"
    title="KT vs 롯데 2025.03.29 (토) 17:00 경기"
    onclick="javascript:programClick('500049','1001','110020','10000028',
	'롯데자이언츠 VS KT 위즈','사직야구장',
	'20250329', '000001', '1700', '',
	'1회차', '035', '1' ); return false;">선택</button>

<button
    class="btn btn-default"
    type="button"
    id="BTN_10000028_20250328_000001"
    tabindex="8"
    title="KT vs 롯데 2025.03.28 (금) 18:30 경기"
    onclick="javascript:programClick('500049','1001','110020','10000028',
	'롯데자이언츠 VS KT 위즈','사직야구장',
	'20250405', '000001', '1700', '',
	'1회차', '035', '1' ); return false;">선택</button>

<button
	class="btn btn-default"
	type="button"
	id="BTN_10000028_20250405_000001"
	tabindex="13"
	title="두산 vs 롯데 2025.04.05 (토) 17:00 경기"
	onclick="javascript:programClick('500049','1001','110020','10000028',
	'롯데자이언츠 VS 두산 베어스','사직야구장',
	'20250405', '000001', '1700', '',
	'1회차', '035', '5' ); return false;">선택</button>
<button
	class="btn btn-default"
	type="button"
	id="BTN_10000028_20250406_000001"
	tabindex="14"
	title="두산 vs 롯데 2025.04.06 (일) 14:00 경기"
	onclick="javascript:programClick('500049','1001','110020','10000028',
	'롯데자이언츠 VS 두산 베어스','사직야구장',
	'20250406', '000001', '1400', '',
	'1회차', '035', '5' ); return false;">선택</button>
	
<script>

	

   
function onstart(){}

function popNotice(){
	
	var popObj = "pop_Notice";
	var popObjINChile = "pop_Notice .in";
	
	$(this).closest(".pop_w").css("visibility","hidden");
	$("#"+popObj).show();
	var modalH2 = $("#"+popObjINChile).outerHeight();
	//$("#"+popObjINChile).css({"margin-top":(modalH2/2)});
	//$("#"+popObjINChile).css({"margin-top":"0px"});
}	

function popNoticeClose(){
	var popObj = "pop_Notice";
	$("#"+popObj).hide();
};



// 데이터 초기화
function init_data_info(){
	$('#PLAY_COMPANY_CD').val("");
	$('#PLACE_CD').val("");
	$('#REG_COMPANY_CD').val("");
	$('#PROGRAM_CD').val("");
	$('#PROGRAM_NAME').val("");
	$('#PLACE_NAME').val("");
	$('#PLAY_DATE').val("");	
	$('#PLAY_SEQ_CD').val("");	
	$('#PLAY_ST_TIME').val("");
	
	$('#PLAY_ED_TIME').val("");
	$('#PLAY_SEQ_NM').val("");
	$('#PHYMAP_NBR').val("");
	$('#REMAIN_SEAT_CNT').val("");
	
}

//공연선택 
function programClick( play_company_cd, place_cd, reg_company_cd, program_cd, program_name, place_name, 
		                   play_date, play_seq_cd, play_st_time, play_ed_time, play_seq_nm, phymap_nbr, remain_seat_cnt )
{
 
	init_data_info();
	$('#PLAY_COMPANY_CD').val(play_company_cd);
	$('#PLACE_CD').val(place_cd);
	$('#REG_COMPANY_CD').val(reg_company_cd);
	$('#PROGRAM_CD').val(program_cd);
	$('#PROGRAM_NAME').val(program_name);
	$('#PLACE_NAME').val(place_name);
	$('#PLAY_DATE').val(play_date);
	
	$('#PLAY_SEQ_CD').val(play_seq_cd);	
	$('#PLAY_ST_TIME').val(play_st_time);
	$('#PLAY_ED_TIME').val(play_ed_time);
	$('#PLAY_SEQ_NM').val(play_seq_nm);
	$('#PHYMAP_NBR').val(phymap_nbr);
	$('#REMAIN_SEAT_CNT').val(remain_seat_cnt);
	
	var btn_class ="btn-default";
	$("."+btn_class).css("background-color", "#0e1b4d");
	$("."+btn_class).css("border","1px solid");
	$("."+btn_class).html("선택");
	 
	var btn_id="BTN_"+program_cd+"_"+ play_date+"_"+ play_seq_cd ;
	$("#"+btn_id).css("background-color","#dd0330");
	$("#"+btn_id).css("border","1px dotted");
	$("#"+btn_id).html("선택됨");
	
	selectProgramInfo();
}
 

 

function goBooking_seat()  
{
	if( $('#PLAY_COMPANY_CD').val() == "" ||
			$('#PLACE_CD').val() == "" || 
	    $('#REG_COMPANY_CD').val() == "" ||
	    $('#PROGRAM_CD').val() == "" )
	{
		   alert("경기를 선택 해주세요.");
		   return;
	}
	    
	if( $('#PLAY_DATE').val() == "" )
	{
		   alert("경기를 선택 해주세요.");
		   return;
	}
	
	if( $('#PLAY_SEQ_CD').val() == "" ||
	    $('#PLAY_ST_TIME').val() == "" )
	{
		   alert("경기를 선택 해주세요.");
		   return;
	}
	
	if( $('#REMAIN_SEAT_CNT').val() == 0 )
	{
			   alert("잔여 좌석이 없습니다.");
			   return;
	}
	
	document.ReserveForm.PLAY_SEQ_NM_ENC.value = encodespecialChar( document.ReserveForm.PLAY_SEQ_NM.value );
	var frm = document.ReserveForm;
	   
	if( $('#PLAY_DATE').val().substring(4,8) == "1231" ){

		$('*[data-show3]').modal('show');
		   return ;
		   
	} else if( $('#PLAY_DATE').val().substring(4,8) == "1230" ){

		$('*[data-show4]').modal('show');
		   return ;
		
	} else {
			if ( document.getElementById("HandicapChk").checked == true){
				frm.action = 'booking_step2_Handicap.do';
			} else {
				frm.action = 'booking_step2.do';
			}
			frm.method = 'post';
			frm.submit();
	}
	
}


function goMypage()
{
	var link = "/mypage_list.do?TAB_NO=R&PAGENUM=1&SORT_DATE_TYPE=SORT_TRAN";
	var CheckObj = window.open(link, "callMyBook", "resizable=yes, status=no, scrollbars=yes, toolbar=no, menubar=no, width=1200, height=929");
	if(CheckObj == null){ alert("팝업 차단을 해제해주세요."); }
}


function goGuide1()
{ 
	var link = "http://ticket.giantsclub.com/guide01_01.do";
	var CheckObj = window.open(link, "contentsView", "resizable=yes, status=no, scrollbars=yes, toolbar=no, menubar=no, width=1230, height=929");
	if(CheckObj == null){ alert("팝업 차단을 해제해주세요."); }
}



//   
function selectProgramInfo() {
	
  var params = $("form[name=ReserveForm]").serialize();
	
	$.ajax({
		type :'post',
		url :'getProgramNotice.do',
		data: params,
		dataType :'html',
		success:function(data){
			console.log("data:"+ data.trim() );
			if ( data != ""){
					programNotice( data.trim() );
			}
		}
	});
}


function programNotice(html){
	var htmlText;
	
	htmlText  = "";
	htmlText += "<b>" ;
	htmlText += html.ReplaceAll("\n", "<br />"); ;
	htmlText += "</b>";
	 
	$('#program_Info').empty();
	$('#program_Info').append(htmlText); 
	$('*[data-show2]').modal('show');
   
}	

function programNoticeClose(){
	var popObj = "notice_modal2"; // "programNotice";
	$("#"+popObj).hide();
};
  

function btnSeasonConfirm(){
	var frm = document.ReserveForm; 
	if ( document.getElementById("HandicapChk").checked == true){
		frm.action = 'booking_step2_Handicap.do';
	} else {
		frm.action = 'booking_step2.do';
	}
	frm.method = 'post';
	frm.submit();
}

function btnMembershipConfirm(){
	var frm = document.ReserveForm; 
	if ( document.getElementById("HandicapChk").checked == true){
		frm.action = 'booking_step2_Handicap.do';
	} else {
		frm.action = 'booking_step2.do';
	}
	frm.method = 'post';
	frm.submit();
}


function btnSeasonClose(){
	var popObj = "notice_modal3";
	$("#"+popObj).hide();
} 
 
function btnMembershipClose(){
	var popObj = "notice_modal4";
	$("#"+popObj).hide();
} 
 
 
 
</script>