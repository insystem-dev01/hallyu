<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>솔루션 설치 (3/3) - DB</title>
    <style type="text/css">
        .body {
            font-family: 굴림;
            font-size: 12px;
        }

        .box {
            background-color: #FCFCFC;
            color: #B19265;
            font-family: 굴림;
            font-size: 12px;
        }

        .nobox {
            background-color: #FCFCFC;
            border-style: none;
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
            <table width="587" border="0" cellspacing="0" cellpadding="0">
                <form name=frminstall2>
                    <tr>
                        <td colspan="3">
                            <div style="width: 100%; height: 22px; background: url('<?= base_url() ?>dist/img/install/box_top.gif') center/cover;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="3"><img src="<?= base_url() ?>dist/img/install/box_left.gif" width="3" height="340"></td>
                        <td width="581" valign="top" bgcolor="#FCFCFC">
                            <br>
                            <table width="541" border="0" align="center" cellpadding="0" cellspacing="0" class="body">
                                <tr>
                                    <td>설치를 시작합니다. <font color="#CC0000">설치중 작업을 중단하지 마십시오. </font>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="status_bar" type="text" class="box" size="65" readonly></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="350" border="0" align="center" cellpadding="5" cellspacing="0" class="body">
                                            <tr>
                                                <td width="50"> </td>
                                                <td width="300"><input type=text name=job1 class=nobox size=80 readonly></td>
                                            </tr>
                                            <tr>
                                                <td width="50"> </td>
                                                <td width="300"><input type=text name=job2 class=nobox size=80 readonly></td>
                                            </tr>
                                            <tr>
                                                <td width="50"> </td>
                                                <td width="300"><input type=text name=job3 class=nobox size=80 readonly></td>
                                            </tr>
                                            <tr>
                                                <td width="50">
                                                    <div align="center"></div>
                                                </td>
                                                <td width="300"><input type=text name=job4 class=nobox size=80 readonly></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><input type=text name=job5 class=nobox size=80 readonly></td>
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
                                        <input type="button" name="btn_next" disabled value="메인화면" onclick="location.href='<?= base_url(); ?>';">
                                    </td>
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

    <?php
    flush();
    usleep(50000);

    // 테이블 생성 ------------------------------------
    $file = implode("", file(base_url()."vendor/install/sql_db.sql"));

    mysqli_set_charset($dblink, "utf8"); //한글깨짐현상 으로 추가
    $f = explode(";", $file);
    for ($i = 0; $i < count($f); $i++) {
        if (trim($f[$i]) == "") continue;
        mysqli_query($dblink, $f[$i]) or die(mysqli_error($dblink));
    }
    // 테이블 생성 ------------------------------------

    echo "<script>document.frminstall2.job1.value='전체 테이블 생성중';</script>";
    flush();
    usleep(50000);

    for ($i = 0; $i < 45; $i++) {
        echo "<script language='JavaScript'>document.frminstall2.status_bar.value += '■';</script>\n";
        flush();
        usleep(500);
    }

    echo "<script>document.frminstall2.job1.value='전체 테이블 생성 완료';</script>";
    flush();
    usleep(50000);

    //-------------------------------------------------------------------------------------------------
    // 운영자 레벨명 UPDATE
    $sql = " update shop_member_grade
            set gb_name = '$admin_name'
		  where gb_no = '1' ";
    mysqli_query($dblink, $sql);

    // 운영자 UPDATE
    $sql = " update shop_member
            set name = '$admin_name'
			  , passwd = '".hash('sha256', (hash('sha256', $admin_pass) . hash('sha256', date("Y-m-d H:i:s", time()))))."'
			  , email = '$admin_email'
			  , grade = '1'
			  , reg_time = '" . date("Y-m-d H:i:s", time()) . "'
			  , today_login = '" . date("Y-m-d H:i:s", time()) . "'
			  , login_ip = '{$_SERVER['REMOTE_ADDR']}'
			  , mb_ip = '{$_SERVER['REMOTE_ADDR']}'
		  where index_no = '1' ";
    mysqli_query($dblink, $sql);

    echo "<script>document.frminstall2.job2.value='DB설정 완료';</script>";
    flush();
    usleep(50000);
    //-------------------------------------------------------------------------------------------------

    // DB 설정 파일 생성
    $file = APPPATH . "config/database.php";
    $f = @fopen($file, "w+");
    $str = '<?php
    $active_group = \'default\';
    $query_builder = TRUE;
    
    $db[\'default\'] = array(
        \'dsn\'	=> \'\',
        \'hostname\' => \''.$mysql_host.'\',
        \'username\' => \''.$mysql_user.'\',
        \'password\' => \''.$mysql_pass.'\',
        \'database\' => \''.$mysql_db.'\',
        \'dbdriver\' => \'mysqli\',
        \'dbprefix\' => \'\',
        \'pconnect\' => FALSE,
        \'db_debug\' => (ENVIRONMENT !== \'production\'),
        \'cache_on\' => FALSE,
        \'cachedir\' => \'\',
        \'char_set\' => \'utf8\',
        \'dbcollat\' => \'utf8_general_ci\',
        \'swap_pre\' => \'\',
        \'encrypt\' => FALSE,
        \'compress\' => FALSE,
        \'stricton\' => FALSE,
        \'failover\' => array(),
        \'save_queries\' => TRUE
    );';
    fwrite($f, $str);

    fclose($f);
    @chmod($file, 0606);
    
    $file = APPPATH."./config/autoload.php";
    $f = @fopen($file, "w+");
    $str='
    <?php
        defined(\'BASEPATH\') OR exit(\'No direct script access allowed\');

        /*
        | -------------------------------------------------------------------
        | AUTO-LOADER
        | -------------------------------------------------------------------
        | This file specifies which systems should be loaded by default.
        |
        | In order to keep the framework as light-weight as possible only the
        | absolute minimal resources are loaded by default. For example,
        | the database is not connected to automatically since no assumption
        | is made regarding whether you intend to use it.  This file lets
        | you globally define which systems you would like loaded with every
        | request.
        |
        | -------------------------------------------------------------------
        | Instructions
        | -------------------------------------------------------------------
        |
        | These are the things you can load automatically:
        |
        | 1. Packages
        | 2. Libraries
        | 3. Drivers
        | 4. Helper files
        | 5. Custom config files
        | 6. Language files
        | 7. Models
        |
        */

        /*
        | -------------------------------------------------------------------
        |  Auto-load Packages
        | -------------------------------------------------------------------
        | Prototype:
        |
        |  $autoload[\'packages\'] = array(APPPATH.\'third_party\', \'/usr/local/shared\');
        |
        */
        $autoload[\'packages\'] = array();

        /*
        | -------------------------------------------------------------------
        |  Auto-load Libraries
        | -------------------------------------------------------------------
        | These are the classes located in system/libraries/ or your
        | application/libraries/ directory, with the addition of the
        | \'database\' library, which is somewhat of a special case.
        |
        | Prototype:
        |
        |	$autoload[\'libraries\'] = array(\'database\', \'email\', \'session\');
        |
        | You can also supply an alternative library name to be assigned
        | in the controller:
        |
        |	$autoload[\'libraries\'] = array(\'user_agent\' => \'ua\');
        */
        $autoload[\'libraries\'] = array(\'session\',\'database\');

        /*
        | -------------------------------------------------------------------
        |  Auto-load Drivers
        | -------------------------------------------------------------------
        | These classes are located in system/libraries/ or in your
        | application/libraries/ directory, but are also placed inside their
        | own subdirectory and they extend the CI_Driver_Library class. They
        | offer multiple interchangeable driver options.
        |
        | Prototype:
        |
        |	$autoload[\'drivers\'] = array(\'cache\');
        |
        | You can also supply an alternative property name to be assigned in
        | the controller:
        |
        |	$autoload[\'drivers\'] = array(\'cache\' => \'cch\');
        |
        */
        $autoload[\'drivers\'] = array();

        /*
        | -------------------------------------------------------------------
        |  Auto-load Helper Files
        | -------------------------------------------------------------------
        | Prototype:
        |
        |	$autoload[\'helper\'] = array(\'url\', \'file\');
        */
        $autoload[\'helper\'] = array(\'url\');

        /*
        | -------------------------------------------------------------------
        |  Auto-load Config files
        | -------------------------------------------------------------------
        | Prototype:
        |
        |	$autoload[\'config\'] = array(\'config1\', \'config2\');
        |
        | NOTE: This item is intended for use ONLY if you have created custom
        | config files.  Otherwise, leave it blank.
        |
        */
        $autoload[\'config\'] = array();

        /*
        | -------------------------------------------------------------------
        |  Auto-load Language files
        | -------------------------------------------------------------------
        | Prototype:
        |
        |	$autoload[\'language\'] = array(\'lang1\', \'lang2\');
        |
        | NOTE: Do not include the "_lang" part of your file.  For example
        | "codeigniter_lang.php" would be referenced as array(\'codeigniter\');
        |
        */
        $autoload[\'language\'] = array();

        /*
        | -------------------------------------------------------------------
        |  Auto-load Models
        | -------------------------------------------------------------------
        | Prototype:
        |
        |	$autoload[\'model\'] = array(\'first_model\', \'second_model\');
        |
        | You can also supply an alternative model name to be assigned
        | in the controller:
        |
        |	$autoload[\'model\'] = array(\'first_model\' => \'first\');
        */
        $autoload[\'model\'] = array();
    ';
    fwrite($f, $str);
    fclose($f);
    @chmod($file, 0606);

    echo "<script>document.frminstall2.job3.value='DB설정 파일 생성 완료';</script>";
    flush();
    usleep(50000);

    echo "<script language='JavaScript'>document.frminstall2.status_bar.value += '■';</script>\n";
    flush();
    sleep(1);

    echo "<script>document.frminstall2.job4.value='필요한 Table, File, 디렉토리 생성을 모두 완료 하였습니다.';</script>";
    echo "<script>document.frminstall2.job5.value='* 메인화면에서 운영자 로그인을 한 후 운영자 화면으로 이동하여 환경설정을 변경해 주십시오.';</script>";
    flush();
    usleep(50000);
    ?>
    <script>
        document.frminstall2.btn_next.disabled = false;
    </script>
    <script>
        document.frminstall2.btn_next.focus();
    </script>
</body>

</html>