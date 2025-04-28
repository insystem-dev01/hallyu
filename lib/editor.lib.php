<?php
if(!defined('_OMNIWEB_')) exit;

/*************************************************************************
**
**  에디터 관련 함수 모음
**
*************************************************************************/
function editor_html($id, $content, $is_dhtml_editor=true)
{
    static $js = true;

    $editor_url = OM_EDITOR_URL.'/smarteditor2';

    $html = "";
    $html .= "<span class=\"sound_only\">웹에디터 시작</span>";
    if($is_dhtml_editor && $js) {
        $html .= "\n".'<script src="'.$editor_url.'/js/HuskyEZCreator.js"></script>';
        $html .= "\n".'<script>var tw_editor_url = "'.$editor_url.'", oEditors = [];</script>';
        $html .= "\n".'<script src="'.$editor_url.'/config.js"></script>';
        $js = false;
    }

    $smarteditor_class = $is_dhtml_editor ? "smarteditor2" : "";
    $html .= "\n<textarea id=\"$id\" name=\"$id\" class=\"$smarteditor_class\" maxlength=\"65536\" style=\"width:100%\">$content</textarea>";
    $html .= "\n<span class=\"sound_only\">웹 에디터 끝</span>";
    return $html;
}

// textarea 로 값을 넘긴다. javascript 반드시 필요
function get_editor_js($id, $is_dhtml_editor=true)
{
    if($is_dhtml_editor) {
        return "var {$id}_editor_data = oEditors.getById['{$id}'].getIR();\noEditors.getById['{$id}'].exec('UPDATE_CONTENTS_FIELD', []);\nif(jQuery.inArray(document.getElementById('{$id}').value.toLowerCase().replace(/^\s*|\s*$/g, ''), ['&nbsp;','<p>&nbsp;</p>','<p><br></p>','<div><br></div>','<p></p>','<br>','']) != -1){document.getElementById('{$id}').value='';}\n";
    } else {
        return "var {$id}_editor = document.getElementById('{$id}');\n";
    }
}

//  textarea 의 값이 비어 있는지 검사
function chk_editor_js($id, $is_dhtml_editor=true)
{
    if($is_dhtml_editor) {
        return "if(!{$id}_editor_data || jQuery.inArray({$id}_editor_data.toLowerCase(), ['&nbsp;','<p>&nbsp;</p>','<p><br></p>','<p></p>','<br>']) != -1) { alert(\"내용을 입력해 주십시오.\"); oEditors.getById['{$id}'].exec('FOCUS'); return false; }\n";
    } else {
        return "if(!{$id}_editor.value) { alert(\"내용을 입력해 주십시오.\"); {$id}_editor.focus(); return false; }\n";
    }
}
?>