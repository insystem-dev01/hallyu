<html>
    <head>
        <script type="text/javascript">

            function setLGDResult() {
                <?=$is_mobileJs ?>
                try {
                } catch (e) {
                    alert(e.message);
                }
            }

        </script>
    </head>
    <body onload="setLGDResult()">
        <?=$LGD_RESPCODEJs?>
        <form method="post" name="LGD_RETURNINFO" id="LGD_RETURNINFO">
        <?=$inputTags?>
        </form>
    </body>
</html>