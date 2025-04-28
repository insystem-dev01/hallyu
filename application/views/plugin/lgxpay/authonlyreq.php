<!doctype html>
<html lang="ko">
    <head>
        <meta charset="utf-8">
        1<title>LG유플러스 전자결제 본인확인서비스</title>
        <!-- 고객사 사이트가 https인 경우는 아래 http://을 https:// 으로 변경하시면 됩니다. -->
        <link rel="stylesheet" href="<?php echo base_url();?>css/default.css">
        <script language="javascript" src="<?=$httpsScript?>  type="text/javascript"></script>

        <script type="text/javascript">

            /*
            * 수정불가.
            */
            var LGD_window_type = "<?php echo $LGD_WINDOW_TYPE;?>";
            var lgd_form = "LGD_PAYINFO";
            /*
            * 수정불가.
            */
            function launchCrossPlatform(){

                <?=$lgwinjs ?>

            }

            /*
            * FORM 명만  수정 가능
            */
            function getFormObject() {
                    return document.getElementById(lgd_form);
            }

            function  payment_return() {

                var fDoc = lgdwin.contentWindow || lgdwin.contentDocument;

                if (fDoc.document.getElementById('LGD_RESPCODE').value == "0000") {
                    document.getElementById("LGD_AUTHONLYKEY").value = fDoc.document.getElementById('LGD_AUTHONLYKEY').value;
                    document.getElementById("LGD_PAYTYPE").value = fDoc.document.getElementById('LGD_PAYTYPE').value;

                    document.getElementById(lgd_form).target = "_self";
                    document.getElementById("LGD_PAYINFO").action = "AuthOnlyRes.php";
                    document.getElementById(lgd_form).submit();
                } else {
                    alert("LGD_RESPCODE (결과코드2) : " + fDoc.document.getElementById('LGD_RESPCODE').value + "\n" + "LGD_RESPMSG (결과메시지): " + fDoc.document.getElementById('LGD_RESPMSG').value);
                    closeIframe();
                    window.close();
                }//end if
            }//end payment_return
        </script>

        <style>
        #uplus_win {}
        .up_cmt {text-align:center; font-size:14px;}
        .up_cmt img {display:block; margin:0 auto 20px}
        .up_info {background:#eee;padding:13px;margin:28px 25px 20px;}
        .up_info a {float:left; margin-right:10px}
        .up_info p {padding:10px 0 0; line-height:18px;}
        .up_info:after {clear:both; display:block; content:"";}
        .win_btn {clear:both;}
        </style>
    </head>
    <body>
        <form method="post" name ="LGD_PAYINFO" id="LGD_PAYINFO" action="<?php echo base_url();?>plugin/AuthOnlyRes">
        <input type="hidden" name="LGD_ENCODING" value="UTF-8"/>
        <?=$inputTags?>

        </form>

        <div id="uplus_win" class="new_win mbskin">
            <h1 id="win_title">휴대폰 본인확인</h1>
            <p class="up_cmt"><img src="<?php echo base_url();?>plugin/lgxpay/img/upluslogo.jpg" alt="">LG유플러스에 휴대폰 본인확인 요청 중입니다.</p>
            <div class="up_info">
                <a href="http://pgweb.uplus.co.kr:8080/pg/wmp/Home2009/skill/payment_error_center01.jsp" target="_blank"><img src="<?php echo base_url();?>plugin/lgxpay/img/btn_gouplus.jpg" alt="U+ 오류 해결방법 바로가기"></a>
                <p>본인확인이 진행되지 않는다면<br /> 왼쪽의 링크로 이동하여보세요.</p>
                <!--[If lte IE 7]><span style="clear:both; display:block; content:'';"></span><![endif]-->
            </div>
            <div class="win_btn">
                <button type="button" onclick="window.close();" class="btn_medium">창닫기</button>
            </div>
        </div>
        <script>
        setTimeout("launchCrossPlatform();", 1);
        </script>
    </body>
</html>