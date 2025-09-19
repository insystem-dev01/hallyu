<?php
if(!defined('_HALLYUWEB_')) exit; // 개별 페이지 접근 불가

if($is_kakaopay_use) { ?>

    <script type="text/javascript">

        function getTxnId(frm) {
            // console.log(frm);
            $.ajax({
                url: om_url+"/shop/kakaopay/getTxnId.php",
                type: "POST",
                data: $("#kakaopay_request input").serialize(),
                dataType: "json",
                async: false,
                cache: false,
                success: function(data) {
                    // console.log(data);

                    if(data.pc != ''){
                        popup_kakao(data.pc);
                    }else if(data.mobile != ''){
                        popup_kakao(data.mobile);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        // pc에서 결제할 경우.
        function popup_kakao(link){
            var win = window.open('','','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=540,height=700,left=100,top=100');
            win.document.write('<iframe width=100%, height=650 src='+link+' frameborder=0 allowfullscreen></iframe><script>var meta = document.createElement(\'meta\'); meta.name = "viewport"; meta.content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width"; \ndocument.head.append(meta);<\/script>');
        }

        function setChildValue(name){
            name = JSON.parse(name);
            var tid = $("<input type='hidden' value='"+name.tid+"' name = 'tid' >");
            var aid = $("<input type='hidden' value='"+name.aid+"' name = 'aid' >");
            var time = $("<input type='hidden' value='"+name.approved_at+"' name = 'app_time' >");
            var total = $("<input type='hidden' value='"+name.total+"' name='amount' >");

            $("#forderform").append(tid);
            $("#forderform").append(aid);
            $("#forderform").append(time);
            $("#forderform").append(total);
            
            $("#forderform").submit();
        }
    </script>
<?php } 
?>