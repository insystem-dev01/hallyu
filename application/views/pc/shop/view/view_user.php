<a name="it_comment"></a>
<div class="review_danger">
	전체 <b><?php echo $item_use_count; ?></b>건의 상품평이 있습니다. 상품평 이외에 다른 목적이나 불건전한 내용을 올리실 경우 삭제 처리될 수 있습니다.
</div>
<table class="review">
    <?=$tableTag?>
</table>

<script>
function fuserform_submit(f){
	if(!f.memo.value){
		alert('내용을 입력하세요.');
		f.memo.focus();
		return false;
	}

	if(confirm("등록 하시겠습니까?") == false)
		return false;
}

function tdel(url){
	if(confirm('삭제 하시겠습니까?')){
		location.href = url;
	}
}

function tguest(){
    var base_url=document.getElementById('base_url').value;
	answer = confirm('로그인 하셔야 상품평 작성이 가능합니다. 로그인 하시겠습니까?');
	if(answer==true) {
		location.href = base_url+"bbs/login?url=<?php echo $urlencode; ?>";
	}
}
</script>
