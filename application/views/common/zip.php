<?= $js; ?>
<style>
#daum_juso_wrap{position:absolute;left:0;top:0;width:100%;height:100%}
</style>

<div id="daum_juso_wrap" class="daum_juso_wrap"></div>

<script>
function put_data5(zip, addr1, addr2, addr3, jibeon){
    var of = window.opener.document.<?php echo ($frm_name); ?>;

    of.<?=$frm_zip ?>.value = zip;
    of.<?=$frm_addr1 ?>.value = addr1;
    of.<?=$frm_addr2 ?>.value = addr2;
    of.<?=$frm_addr3 ?>.value = addr3;

    if( jibeon ){
		if(of.<?=$frm_jibeon ?> !== undefined){
			of.<?=$frm_jibeon ?>.value = jibeon;
		}
    }
    of.<?=$frm_addr2 ?>.focus();
    window.close();
}
</script>