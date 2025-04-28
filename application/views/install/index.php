<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>솔루션 설치 (1/3) - 라이센스(License)</title>
    <style type="text/css">
        .body {
            font-family: 굴림;
            font-size: 12px;
        }

        .box {
            background-color: #D6D3CE;
            font-family: 굴림;
            font-size: 12px;
        }

        .mw {
            position: fixed;
            _position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9000;
            text-align: center;
        }

        .mw .bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .mw .fg {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 587px;
            height: 365px;
            margin: -183px 0 0 -294px;
            background: #fff
        }
    </style>
</head>

<body background="<?= base_url() ?>dist/img/install/all_bg.gif" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <div class="mw">
        <div class="bg"></div>
        <div class="fg">
            <table width="587" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                    <td colspan="3">
                        <div style="width: 100%; height: 22px; background: url('<?= base_url() ?>dist/img/install/box_top.gif') center/cover;"></div>
                    </td>
                </tr>
                <tr>
                    <td width="3"><img src="<?= base_url() ?>dist/img/install/box_left.gif" width="3" height="340"></td>
                    <td width="581" valign="top" bgcolor="#FCFCFC">
                        <table width="541" border="0" align="center" cellpadding="0" cellspacing="0" class="body">
                            <tr>
                                <td height="10"></td>
                            </tr>
                            <tr>
                                <td>라이센스(License) 내용을 반드시 확인하십시오.</td>
                            </tr>
                            <tr>
                                <td height="10"></td>
                            </tr>
                            <tr>
                                <td align="center"><textarea name="textarea" style='width:99%' rows="9" class="box" readonly><?php echo implode("", file(base_url() . "vendor/install/LICENSE.txt")); ?></textarea></td>
                            </tr>
                            <tr>
                                <td height=10></td>
                            </tr>
                            <tr>
                                <td>설치를 원하시면 위 내용에 동의하셔야 합니다.<br>
                                    동의를 원하시면 &lt;예, 동의합니다&gt; 버튼을 클릭해 주세요.</td>
                            </tr>
                        </table>

                        <table width="562" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td height=20><img src="<?= base_url() ?>dist/img/install/box_line.gif" width="562" height="2"></td>
                            </tr>
                        </table>

                        <table width="551" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="right">
                                    <form name=frm method=post onsubmit="return frm_submit(document.frm);">
                                        <input type="hidden" name="agree" value="동의함">
                                        <input type="submit" name="btn_submit" value="예, 동의합니다 ">
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="3"><img src="<?= base_url() ?>dist/img/install/box_right.gif" width="3" height="340"></td>
                </tr>
                <tr>
                    <td colspan="3"><img src="<?= base_url() ?>dist/img/install/box_bottom.gif" width="587" height="3"></td>
                </tr>
            </table>
        </div>
    </div>

    <script language="JavaScript">
        function frm_submit(f) {
            f.action = "<?= base_url() ?>install/install_config";
            f.submit();
        }

        document.frm.btn_submit.focus();
    </script>
</body>

</html>