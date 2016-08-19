<?php
// +----------------------------------------------------------------------
// | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
// +----------------------------------------------------------------------
// | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: merry M  <test20121212@qq.com>
// +----------------------------------------------------------------------
// M TagLib 自定义标签库

namespace App\Library;

use Think\Template\TagLib;

class M extends TagLib
{
    // 标签定义
    protected $tags = array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        // 已定义 详细参照Cx.class.php
        // php,volist(iterate),foreach,if,elseif,else,switch,case,default,compare,range,empty,
        // notempty,present,notpresent,defined,notdefined,import,assign,define,for,
        // 'foreach'   =>  array('attr'=>'name,item,key','level'=>3),
        'D'          => array('attr' => 'item,name,fn,fn_arg,where,order,limit,page', 'close' => 0),
        'U'          => array('attr' => 'url,vars', 'close' => 0),
        'Img'        => array('attr' => 'src,sys_src', 'close' => 0),
        'Flash'      => array('attr' => 'src,width,height', 'close' => 0),
        'Page'       => array('attr' => 'name'),
        'Kindeditor' => array('attr' => 'name', 'close' => 0),
        'Datepicker' => array('attr' => 'start,end', 'close' => 0),
        'Timepicker' => array('attr' => 'start,end', 'close' => 0),
        'Uploadfile' => array('attr' => 'id,type,dir,cb_fn', 'close' => 0),
        'CKplayer'   => array('attr' => 'id,file_url,img_url,end_run,volume,auto_play,right_close,', 'close' => 0),
    );

    //调用 方法D获取数据
    public function _D($tag)
    {
        if (!isset($tag['item']) || !isset($tag['name']) || !isset($tag['fn'])) {
            return 'missing:item,name,fn';
        }

        $tag['fn_arg'] = isset($tag['fn_arg']) ? $this->_parseCondition($tag['fn_arg']) : '';
        $tag['where']  = isset($tag['where']) ? $this->_parseCondition($tag['where']) : '';
        $tag['order']  = isset($tag['order']) ? $tag['order'] : '';
        $tag['limit']  = isset($tag['limit']) ? $tag['limit'] : '';
        $tag['page']   = isset($tag['page']) ? $tag['page'] : '';
        $cache         = C('SYS_TD_CACHE', null, 60);
        $cache         = (10 < $cache && !APP_DEBUG) ? $cache : 0;
        $pageStr       = '';
        if ($tag['page']) {
            $pageStr .= "\${$tag['item']}_count = D('{$tag['name']}')";
            $cache && $pageStr .= "->cache(true,{$cache})";
            $tag['where'] && $pageStr .= "->where({$tag['where']})";
            $tag['fn_arg'] && $pageStr .= "->where({$tag['fn_arg']})";
            $pageStr .= "->count();";
            'true' != $tag['page'] && $pageStr .= "\${$tag['item']}_max = {$tag['page']};";
            $tag['page'] = ",{$tag['page']}";
        }
        $parseStr = "<?php {$pageStr}";
        $parseStr .= "\${$tag['item']} = D('{$tag['name']}')";
        $cache && $parseStr .= "->cache(true,{$cache})";
        $tag['where'] && $parseStr .= "->where({$tag['where']})";
        $tag['order'] && $parseStr .= "->order('{$tag['order']}')";
        $tag['limit'] && $parseStr .= "->limit('{$tag['limit']}')";
        $parseStr .= "->{$tag['fn']}({$tag['fn_arg']}{$tag['page']}); ?>";

        return $parseStr;
    }

    //M_U生成的连接缓存静态化
    public function _U($tag)
    {
        $tag['url']  = isset($tag['url']) ? $tag['url'] : '';
        $tag['vars'] = isset($tag['vars']) ? $tag['vars'] : '';
        $parseStr    = M_U($tag['url'], $tag['vars']);
        return $parseStr;
    }

    //图片异步加载 需要jquery common.js
    public function _Img($tag)
    {
        $parseStr = '<img';
        foreach ($tag as $key => $value) {
            $parseStr .= ' ' . $key . '="' . $value . '"';
        }
        $parseStr .= ' />';
        return M_sync_img($parseStr);
    }

    //flash生成标记
    public function _Flash($tag)
    {
        if (!isset($tag['src'])) {
            return 'missing:src';
        }

        $tag['src']                      = __ROOT__ . '/' . $tag['src'];
        $width                           = $height                           = '';
        isset($tag['width']) && $width   = ' width="' . $tag['width'] . '"';
        isset($tag['height']) && $height = ' height="' . $tag['height'] . '"';
        $flash_attr                      = array(
            'devicefont', 'autoplay', 'loop', 'quality', 'bgcolor', 'scale', 'salign', 'base', 'menu', 'wmode', 'allowscriptaccess',
        );
        $param_str = '<param name="movie" value="' . $tag['src'] . '" />';
        $embed_str = '<embed type="application/x-shockwave-flash" ';
        $embed_str .= ' src="' . $tag['src'] . '" ' . $width . ' ' . $height;
        foreach ($flash_attr as $attr_name) {
            if (isset($tag[$attr_name])) {
                $param_str .= '<param name="' . $attr_name . '" value="' . $tag[$attr_name] . '" />';
                $embed_str .= ' ' . $attr_name . '="' . $tag[$attr_name] . '"';
            }
        }
        $embed_str .= ' >';
        $parseStr = <<<EOF
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" {$width} {$height}>
    {$param_str}
    {$embed_str}
</object>
EOF;
        return $parseStr;
    }

    //获取翻页配置 懒得自己写了 正则大仙留
    public function _Page($tag, $content)
    {
        if (!isset($tag['name'])) {
            return 'missing:name';
        }

        $cfg_count_row = "'count_row'=>\${$tag['name']}_count,";
        $cfg_max_row   = "'max_row'=>\${$tag['name']}_max,";
        $cfg_roll      = "'roll'=>\${$tag['name']}_roll,";

        $start_content = $cfg_content = $end_content = '';
        $cfg_preg_div  = $cfg_preg_a  = $cfg_preg_current  = $cfg_preg_rows  = $cfg_preg_njump  = '';

        $preg_result = array();
        if (preg_match('/(.*)<config>(.*)<\/config>(.*)/is', $content, $preg_result)) {
            $start_content = $preg_result[1];
            $cfg_content   = $preg_result[2];
            $end_content   = $preg_result[3];
            if (preg_match('/<preg_div>(.*?)<\/preg_div>/is', $cfg_content, $preg_result)) {
                $cfg_preg_div = "'preg_div'=>'" . str_replace('\"', '"', addslashes($preg_result[1])) . "',";
            }
            if (preg_match('/<preg_a>(.*?)<\/preg_a>/is', $cfg_content, $preg_result)) {
                $cfg_preg_a = "'preg_a'=>'" . str_replace('\"', '"', addslashes($preg_result[1])) . "',";
            }
            if (preg_match('/<preg_current>(.*?)<\/preg_current>/is', $cfg_content, $preg_result)) {
                $cfg_preg_current = "'preg_current'=>'" . str_replace('\"', '"', addslashes($preg_result[1])) . "',";
            }
            if (preg_match('/<preg_rows>(.*?)<\/preg_rows>/is', $cfg_content, $preg_result)) {
                $cfg_preg_rows = "'preg_rows'=>'" . str_replace('\"', '"', addslashes($preg_result[1])) . "',";
            }
            if (preg_match('/<preg_njump>(.*?)<\/preg_njump>/is', $cfg_content, $preg_result)) {
                $cfg_preg_njump = "'preg_njump'=>true,";
            }
        }

        $page_str = <<<EOF
            <?php
                \$config = array(
                    $cfg_count_row
                    $cfg_max_row
                    $cfg_roll
                    $cfg_preg_div
                    $cfg_preg_a
                    $cfg_preg_current
                    $cfg_preg_rows
                    $cfg_preg_njump
                    );
                \$page = M_page(\$config);
                if(\$page)
                {
                    echo('$start_content');
                    echo(\$page);
                    echo('$end_content');
                }
            ?>
EOF;

        return $page_str;
    }

    //创建日期选择器
    public function _Datepicker($tag)
    {
        if (!isset($tag['start'])) {
            return 'missing:start,[end]';
        }

        if (isset($tag['start']) && preg_match('/^\s*?\$/', $tag['start'])) {
            $tag['start'] = '<?php echo ' . $tag['start'] . ' ?>';
        }
        if (isset($tag['end']) && preg_match('/^\s*?\$/', $tag['end'])) {
            $tag['end'] = '<?php echo ' . $tag['end'] . ' ?>';
        }
        $dateFormat  = str_replace(array('Y', 'm', 'd'), array('yy', 'mm', 'dd'), C('SYS_DATE'));
        $date_config = 'changeYear: true,changeMonth: true,numberOfMonths: 1,showButtonPanel: true,dateFormat:"' . $dateFormat . '"';
        if (isset($tag['start']) && isset($tag['end'])) {
            $re_script = <<<EOF
<script>
$(function() {
    var datepicker_from_obj = $( "input[name={$tag['start']}_start]" );
    var datepicker_to_obj = $( "input[name={$tag['end']}_end]" );
    datepicker_from_obj.datepicker({ {$date_config},
            onClose: function( selectedDate ) { datepicker_to_obj.datepicker( "option", "minDate", selectedDate );}
    });
    datepicker_to_obj.datepicker({ {$date_config},
            onClose: function( selectedDate ) { datepicker_from_obj.datepicker( "option", "maxDate", selectedDate );}
    });
    datepicker_from_obj.datepicker( $.datepicker.regional[ "zh-CN" ] );
    datepicker_to_obj.datepicker( $.datepicker.regional[ "zh-CN" ] );
});
</script>
EOF;
        } else {
            $re_script = <<<EOF
<script>
$(function() {
    var datepicker_obj = $( "input[name={$tag['start']}]" ).datepicker({ {$date_config} });
    datepicker_obj.datepicker( $.datepicker.regional[ "zh-CN" ] );
});
</script>
EOF;
        }
        return $re_script;
    }

    //创建时间选择器
    public function _Timepicker($tag)
    {
        if (!isset($tag['start'])) {
            return 'missing:start,[end]';
        }

        if (isset($tag['start']) && preg_match('/^\s*?\$/', $tag['start'])) {
            $tag['start'] = '<?php echo ' . $tag['start'] . ' ?>';
        }
        if (isset($tag['end']) && preg_match('/^\s*?\$/', $tag['end'])) {
            $tag['end'] = '<?php echo ' . $tag['end'] . ' ?>';
        }
        //日期与时分秒必须以空格隔开
        $format_arr = explode(' ', C('SYS_DATE_DETAIL'));
        $dateFormat = '';
        $timeFormat = '';
        if (preg_match('/[Y|m|d]/', $format_arr[0])) {
            $dateFormat = $format_arr[0];
            $timeFormat = $format_arr[1];
        } else {
            $dateFormat = $format_arr[1];
            $timeFormat = $format_arr[0];
        }
        $dateFormat  = str_replace(array('Y', 'm', 'd'), array('yy', 'mm', 'dd'), $dateFormat);
        $timeFormat  = str_replace(array('H', 'i', 's'), array('HH', 'mm', 'ss'), $timeFormat);
        $date_config = 'changeYear:true,changeMonth:true,numberOfMonths:1,dateFormat:"' . $dateFormat . '",timeFormat: "' . $timeFormat . '"';
        if (isset($tag['start']) && isset($tag['end'])) {
            $re_script = <<<EOF
<script>
$(function() {
    var datepicker_from_obj = $( "input[name={$tag['start']}]" );
    var datepicker_to_obj = $( "input[name={$tag['end']}]" );
    datepicker_from_obj.datetimepicker({ {$date_config},
        onClose: function(dateText, inst) {
                if (datepicker_to_obj.val() != '') {
                        var testStartDate = datepicker_from_obj.datetimepicker('getDate');
                        var testEndDate = datepicker_to_obj.datetimepicker('getDate');
                        if (testStartDate > testEndDate)
                                datepicker_to_obj.datetimepicker('setDate', testStartDate);
                }
                else {
                        datepicker_to_obj.val(dateText);
                }
        },
        onSelect: function (selectedDateTime){
                datepicker_to_obj.datetimepicker('option', 'minDate', datepicker_from_obj.datetimepicker('getDate') );
        }
    });
    datepicker_to_obj.datetimepicker({ {$date_config},
        onClose: function(dateText, inst) {
                if (datepicker_from_obj.val() != '') {
                        var testStartDate = datepicker_from_obj.datetimepicker('getDate');
                        var testEndDate = datepicker_to_obj.datetimepicker('getDate');
                        if (testStartDate > testEndDate)
                                datepicker_from_obj.datetimepicker('setDate', testEndDate);
                }
                else {
                        datepicker_from_obj.val(dateText);
                }
        },
        onSelect: function (selectedDateTime){
                datepicker_from_obj.datetimepicker('option', 'maxDate', datepicker_to_obj.datetimepicker('getDate') );
        }
    });
});
</script>
EOF;
        } else {
            $re_script = <<<EOF
    var datepicker_obj = $( "input[name={$tag['start']}]" ).datetimepicker({ {$date_config} })';
EOF;
        }
        return $re_script;
    }

    //创建编辑器 textarea[name]
    public function _Kindeditor($tag)
    {
        isset($tag['name']) && $element = explode('|', $tag['name']);
        if (!is_array($element)) {
            return 'missing:name[|name]';
        }

        //后期修改afterSelectFile
        $UploadFileUrl = U('ManageUpload/UploadFile');
        $ManageFileUrl = U('ManageUpload/ManageFile', array('t' => 'kindeditor'));
        $editor_config = <<<EOF
uploadJson : '{$UploadFileUrl}',
fileManagerJson : '{$ManageFileUrl}',
extraFileUploadParams : {'t':'kindeditor','session_id':'{:session_id()}'},
formatUploadUrl:false,
resizeType:1,
themeType : 'simple',
urlType : 'relative',
allowFileManager : true
EOF;
        $js_global = '';
        $js_create = '';
        foreach ($element as $e) {
            $js_global .= 'var kindeditor_' . $e . ";";
            $js_create .= 'kindeditor_' . $e . ' = ' . "K.create('textarea[name=\"" . $e . "\"]', { {$editor_config} });";

        }
        /*
         * KindEditor.options.htmlTags.script = [];
         */
        $re_script = <<<EOF
<import file="kindeditor/kindeditor-all-min" />
<script type="text/javascript">
$(function(){
    {$js_global}
    KindEditor.ready(function(K){
        {$js_create}
    });
});
</script>
EOF;
        return $re_script;
    }

    //上传文件按钮 元素id 上传类型  上传地址 扩展json参数
    public function _Uploadfile($tag)
    {
        if (!isset($tag['id']) || !isset($tag['type']) || !isset($tag['cb_fn'])) {
            return "missing:id,type,cb_fn";
        }

        $button = '';
        switch ($tag['type']) {
            case 'insertfile':
                $button = <<<EOF
            editor.loadPlugin('insertfile', function() {
                editor.plugin.fileDialog({
                    fileUrl : K('#url').val(),
                    clickFn : function(url, title) {
                        {$tag['cb_fn']}(url,title)
                        editor.hideDialog();
                    }
                });
            });
EOF;
                break;
            /*
             * function  call_back 是非绑定的对象使用时才会调用的方法
             * 在多文件上传按钮后面追加的按钮id默认加_filemanage
             */
            case 'multiimage':
                $lang1      = L('selection') . L('image');
                $filemanage = <<<EOF
        $('#{$tag['id']}').after('<button id="{$tag['id']}_filemanage" type="button" class="btn btn-default ml20">{$lang1}</button>');
        K('#{$tag['id']}_filemanage').click(function() {
            editor.loadPlugin('filemanager', function() {
                editor.plugin.filemanagerDialog({
                    viewType : 'VIEW',
                    dirName : 'image',
                    clickFn : function(url, title) {
                        if('function' == typeof(M_call_back))
                        {
                            M_call_back(url,title);
                        }
                        else
                        {
                            {$tag['cb_fn']}(url,title);
                        }
                        editor.hideDialog();
                    }
                });
            });
        });
EOF;
                $button = <<<EOF
            editor.loadPlugin('multiimage', function() {
                editor.plugin.multiImageDialog({
                    clickFn : function(urlList) {
                        K.each(urlList, function(i, data) {
                            var url = data.url;
                            var title = '';
                            {$tag['cb_fn']}(url,title);
                        });
                        editor.hideDialog();
                    }
                });
            });
EOF;
                break;
            default:
                $button = <<<EOF
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    imageUrl : K('#url1').val(),
                    clickFn : function(url, title, width, height, border, align) {
                        {$tag['cb_fn']}(url, title, width, height, border, align);
                        editor.hideDialog();
                    }
                });
            });
EOF;
        }
        $UploadFileUrl = U('ManageUpload/UploadFile');
        $ManageFileUrl = U('ManageUpload/ManageFile', array('t' => $tag['dir']));
        $re_script     = <<<EOF
<import file="kindeditor/kindeditor-all-min" />
<script type="text/javascript">
$(function(){
    KindEditor.create();
    KindEditor.ready(function(K) {
        var editor = K.editor({
            extraFileUploadParams : {'t':'{$tag['dir']}','session_id':'{:session_id()}'},
            uploadJson : '{$UploadFileUrl}',
            fileManagerJson : '{$ManageFileUrl}',
            themeType : 'simple',
            urlType : 'relative',
            allowFileManager : true
        });
        {$filemanage}
        K('#{$tag['id']}').click(function() {
            {$button}
        });
    });
});
</script>
EOF;
        return $re_script;
    }

    //创建CKplayer播放器
    public function _CKplayer($tag)
    {
        return M_ckplayer($tag);
    }

    //格式化数组 -> 字符串
    private function _parseCondition($str)
    {
        if ('' == $str) {
            return false;
        }

        //匹配模板变量
        $pattern_var = '/^\$|array\(|true|false|null|\w\(.*?\)/i';
        //匹配数组 where 条件
        $pattern_condition = '/(.*?)\s(eq|neq|gt|egt|lt|elt|like|(not\s)?between|(not\s)?in)\s(.*)/i';

        //分割参数
        $strs          = explode('||', $str);
        $condition_str = '';
        foreach ($strs as $condition) {
            if ('' == $condition) {
                continue;
            }
            $condition_str && $condition_str .= ',';
            //分割条件
            $args     = explode('|', $condition);
            $is_array = 1 < count($args);
            $re_str   = $is_array ? 'array(' : '';
            foreach ($args as $arg) {
                if ('' == $arg) {
                    continue;
                }
                //模板中不支持=>需要一次转换
                $arg = str_replace('=', '=>', $arg);
                if (preg_match($pattern_condition, $arg, $matches)) {
                    $re_str .= ('eq' != $matches[2]) ? "'{$matches[1]}'=>array('{$matches[2]}'," : "'{$matches[1]}'=>";
                    $re_str .= (preg_match($pattern_var, $matches[5])) ? $matches[5] : "'{$matches[5]}'";
                    $re_str .= ('eq' != $matches[2]) ? '),' : ',';
                } else {
                    $re_str .= (preg_match($pattern_var, $arg)) ? $arg : "'{$arg}'";
                    $is_array && $re_str .= ',';
                }
            }
            $is_array && $re_str .= ')';
            $condition_str .= $re_str;
        }
        return $condition_str;
    }
}
