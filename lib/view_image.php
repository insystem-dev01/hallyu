<?php
include_once('./_common.php');

$tb['title'] = '이미지 크게보기';
include_once(OM_PATH.'/head.sub.php');

$filename = $_GET['fn'];

if(strpos($filename, 'data/editor')) {
    $editor_file = strstr($filename, 'editor');
    $filepath = OM_DATA_PATH.'/'.$editor_file;
}

if(is_file($filepath)) {
    $size = @getimagesize($filepath);
    if(empty($size))
        alert_close('이미지 파일이 아닙니다.');

    $width = $size[0];
    $height = $size[1];

    if($editor_file)
        $fileurl = OM_DATA_URL.'/'.$editor_file;

    $img = '<img src="'.$fileurl.'" alt="" width="'.$width.'" height="'.$height.'" class="draggable" style="position:relative;top:0;left:0;cursor:move;">';
} else {
    alert('파일이 존재하지 않습니다.','close');
}
?>

<div><?php echo $img ?></div>

<script>
var win_w = <?php echo $width ?>;
var win_h = <?php echo $height ?> + 70;
var win_l = (screen.width - win_w) / 2;
var win_t = (screen.height - win_h) / 2;

if(win_w > screen.width) {
    win_l = 0;
    win_w = screen.width - 20;

    if(win_h > screen.height) {
        win_t = 0;
        win_h = screen.height - 40;
    }
}

if(win_h > screen.height) {
    win_t = 0;
    win_h = screen.height - 40;

    if(win_w > screen.width) {
        win_w = screen.width - 20;
        win_l = 0;
    }
}

window.moveTo(win_l, win_t);
window.resizeTo(win_w, win_h);

$(function() {
    var is_draggable = false;
    var x = y = 0;
    var pos_x = pos_y = 0;

    $(".draggable").mousemove(function(e) {
        if(is_draggable) {
            x = parseInt($(this).css("left")) - (pos_x - e.pageX);
            y = parseInt($(this).css("top")) - (pos_y - e.pageY);

            pos_x = e.pageX;
            pos_y = e.pageY;

            $(this).css({ "left" : x, "top" : y });
        }

        return false;
    });

    $(".draggable").mousedown(function(e) {
        pos_x = e.pageX;
        pos_y = e.pageY;
        is_draggable = true;
        return false;
    });

    $(".draggable").mouseup(function() {
        is_draggable = false;
        return false;
    });

    $(".draggable").dblclick(function() {
        window.close();
    });
});
</script>

<?php
include_once(OM_PATH.'/tail.sub.php');
?>