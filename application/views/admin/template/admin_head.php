<!doctype html>
    <html lang="ko">
        <head>
            <meta charset="utf-8">
            <title><?=$title ?></title>
            <link rel="stylesheet" href="<?php echo base_url(); ?>/dist/css/admin.css?ver=<?=time()?>">
            <?=$favTag?>
            <script src="<?php echo base_url(); ?>js/jquery-1.8.3.min.js"></script>
            
            <script src="<?php echo base_url(); ?>js/jquery-ui-1.10.3.custom.js"></script>
            <script src="<?php echo base_url(); ?>js/shop.js"></script>
            <script src="<?php echo base_url(); ?>js/common.js?ver=<?=time()?>"></script>
            <script src="<?php echo base_url(); ?>js/categorylist.js?ver=<?=time()?>"></script>
            <script src="<?php echo base_url(); ?>js/categorylist.js?ver=<?=time()?>"></script>
            <link rel="stylesheet" type="text/css" href="<?=base_url()?>js/datetimepicker/jquery.datetimepicker.css"/ >
            <script src="<?=base_url()?>js/datetimepicker/build/jquery.datetimepicker.full.min.js"></script>
        </head>
    <body>
        <input type="hidden" id="base_url" value="<?=base_url()?>">