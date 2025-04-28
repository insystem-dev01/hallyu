// 선택된 카테고리가 있는지
var is_option_check = function()
{
	var chk = true;
	$("select[name^=sel_ca] option:selected").each(function() {
		chk = false;
		return false;
    });

    if(chk)
        alert('카테고리를 하나이상 선택하세요.');

    return chk;
}

// 동일 카테고리가 있는지
var same_option_check = function(val)
{
	var chk = false;
	$("select#sel_ca_id option").each(function() {
        if(val == $(this).val()) {
            chk = true;
            return false;
        }
    });

    if(chk)
        alert('이미 선택하신 카테고리 입니다.');

    return chk;
}

// 카테고리 추가
var category_add = function()
{
	if(is_option_check())
		return;
	
	var sel_count = $("select#sel_ca_id option").size();	
	if(sel_count >= 3) {
		alert('카테고리는 최대 3개까지만 등록 가능합니다.');
		return;
	}

	var sel_text = "";
	var sel_value = "";
	var gubun = "";
	for(var i=1; i<=5; i++) 
	{		
		$this = $("select#sel_ca"+i+" option:selected");
		if($this.val()) {
			sel_text += gubun + $this.text();		
			sel_value = $this.val();	
			gubun = " > ";
		}			
	}

    if(sel_value) {
        if(same_option_check(sel_value))
            return;

		$("select#sel_ca_id").append("<option value=\""+sel_value+"\">"+sel_text+"</option>");
    }	
}

// 카테고리 순서변경
var category_move = function(sel_id, type)
{
	var $el = $("select#"+sel_id+" option:selected");
	if($el.size() > 0) {
		if(type == 'prev')
			$el.insertBefore($el.prev());
		else
			$el.insertAfter($el.next());
	} else {
		alert('이동할 항목을 선택해 주세요.');
		return;
	}
}

$(function(){
	// 삭제
	$("button.frm_option_del").click(function() {
		var $el = $("select#sel_ca_id option:selected");
		if($el.size() > 0) {
			$el.remove();
		} else {
			alert('삭제할 항목을 선택해 주세요.');
			return;
		}
	});
});