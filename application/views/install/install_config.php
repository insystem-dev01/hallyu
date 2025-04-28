<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>솔루션 설치 (2/3) - 설정</title>
    <style type="text/css">
        .body {
            font-family: 굴림;
            font-size: 12px;
        }

        .box {
            font-family: 굴림;
            font-size: 12px;
        }

        .box2 {
            font-family: 굴림;
            background-color: #D6D3CE;
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
            <table width="587" border="0" cellspacing="0" cellpadding="0">
                <form name=frm method=post action="javascript:frm_submit(document.frm)" autocomplete="off">
                    <tr>
                        <td colspan="3">
                            <div style="width: 100%; height: 22px; background: url('<?= base_url() ?>dist/img/install/box_top.gif') center/cover;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="3"><img src="<?= base_url() ?>dist/img/install/box_left.gif" width="3" height="340"></td>
                        <td width="581" valign="top" bgcolor="#FCFCFC">
                            <br>
                            <table width="540" border="0" align="center" cellpadding="0" cellspacing="0" class="body">
                                <tr>
                                    <td width="270" height="16">
                                        <strong>MySQL 정보입력 </strong><br><br>
                                        <table width="270" border="0" cellpadding="0" cellspacing="0" class="body">
                                            <tr>
                                                <td width="80" align="right" height=30>Host :&nbsp;</td>
                                                <td><input name="mysql_host" type="text" class="box" value="localhost"></td>
                                            </tr>
                                            <tr>
                                                <td width="80" align="right" height=30>User :&nbsp;</td>
                                                <td><input name="mysql_user" type="text" class="box"></td>
                                            </tr>
                                            <tr>
                                                <td width="80" align="right" height=30>Password :&nbsp;</td>
                                                <td><input name="mysql_pass" type="text" class="box"></td>
                                            </tr>
                                            <tr>
                                                <td width="80" align="right" height=30>DB :&nbsp;</td>
                                                <td><input name="mysql_db" type="text" class="box"></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <strong>최고관리자 정보입력</strong><br><br>
                                        <table width="270" border="0" cellpadding="0" cellspacing="0" class="body">
                                            <tr>
                                                <td width="80" align="right" height=30>ID :&nbsp;</td>
                                                <td><input name="admin_id" type="text" class="box2" value="admin" readonly></td>
                                            </tr>
                                            <tr>
                                                <td width="80" align="right" height=30>Password :&nbsp;</td>
                                                <td><input name="admin_pass" type="text" class="box"></td>
                                            </tr>
                                            <tr>
                                                <td width="80" align="right" height=30>Name :&nbsp;</td>
                                                <td><input name="admin_name" type="text" class="box" value="관리자"></td>
                                            </tr>
                                            <tr>
                                                <td width="80" align="right" height=30>E-mail :&nbsp;</td>
                                                <td><input name="admin_email" type="text" class="box" value="admin@domain.com"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table width="562" border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td height=15><img src="<?= base_url() ?>dist/img/install/box_line.gif" width="562" height="2"></td>
                                </tr>
                                <tr>
                                    <td class=body align=right height=35>
                                        <font color=crimson>이미 솔루션이 설치되어있다면 DB 자료가 망실되므로 주의하십시오.</font>
                                    </td>
                                </tr>
                                <tr>
                                    <td height=15><img src="<?= base_url() ?>dist/img/install/box_line.gif" width="562" height="2"></td>
                                </tr>
                            </table>
                            <br>

                            <table width="551" border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="right"><input type="submit" name="Submit2" value=" 다   음 "></td>
                                </tr>
                            </table>
                        </td>
                        <td width="3"><img src="<?= base_url() ?>dist/img/install/box_right.gif" width="3" height="340"></td>
                    </tr>
                    <tr>
                        <td colspan="3"><img src="<?= base_url() ?>dist/img/install/box_bottom.gif" width="587" height="3"></td>
                    </tr>
                </form>
            </table>
        </div>
    </div>

    <script language="JavaScript">
        function frm_submit(f) {
            if (f.mysql_host.value == "") {
                alert("MySQL Host 를 입력하십시오.");
                f.mysql_host.focus();
                return;
            } else if (f.mysql_user.value == "") {
                alert("MySQL User 를 입력하십시오.");
                f.mysql_user.focus();
                return;
            } else if (f.mysql_db.value == "") {
                alert("MySQL DB 를 입력하십시오.");
                f.mysql_db.focus();
                return;
            } else if (f.admin_id.value == "") {
                alert("최고관리자 ID 를 입력하십시오.");
                f.admin_id.focus();
                return;
            } else if (f.admin_pass.value == "") {
                alert("최고관리자 패스워드를 입력하십시오.");
                f.admin_pass.focus();
                return;
            } else if (f.admin_name.value == "") {
                alert("최고관리자 이름을 입력하십시오.");
                f.admin_name.focus();
                return;
            } else if (f.admin_email.value == "") {
                alert("최고관리자 E-mail 을 입력하십시오.");
                f.admin_email.focus();
                return;
            }


            if (/[^a-zA-Z]/g.test(f.admin_id.value)) {
                alert("최고관리자 ID 가 영문자가 아닙니다.");
                f.admin_id.focus();
            }

            f.action = "<?= base_url() ?>install/install_db";
            f.submit();

            return true;
        }

        document.frm.mysql_user.focus();
    </script>
</body>

</html>