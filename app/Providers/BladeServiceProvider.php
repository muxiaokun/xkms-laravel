<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
        Blade::directive('asyncImg', function ($expression) {
            return mAsyncImg($expression);
        });
        Blade::directive('flash', function () {
            if (!isset($tag['src'])) {
                return 'missing:src';
            }

            $tag['src'] = asset($tag['src']);
            $width      = $height = '';
            isset($tag['width']) && $width = ' width="' . $tag['width'] . '"';
            isset($tag['height']) && $height = ' height="' . $tag['height'] . '"';
            $flash_attr = [
                'devicefont',
                'autoplay',
                'loop',
                'quality',
                'bgcolor',
                'scale',
                'salign',
                'base',
                'menu',
                'wmode',
                'allowscriptaccess',
            ];
            $param_str  = '<param name="movie" value="' . $tag['src'] . '" />';
            $embed_str  = '<embed type="application/x-shockwave-flash" ';
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
        });
        Blade::directive('datepicker', function ($expression) {
            if (!isset($expression)) {
                return 'missing:start[,end]';
            }
            $end_input = '';
            $inputs    = explode(',', $expression);
            if (1 < count($inputs)) {
                list($start_input, $end_input) = $inputs;
            } else {
                $start_input = $inputs[0];
            }

            if ($start_input) {
                $start_input = '<?php echo "' . $start_input . '" ?>';
            }
            if ($end_input) {
                $end_input = '<?php echo "' . $end_input . '" ?>';
            }

            $dateFormat  = str_replace(['Y', 'm', 'd'], ['yy', 'mm', 'dd'], config('system.sys_date'));
            $date_config = 'changeYear: true,changeMonth: true,numberOfMonths: 1,showButtonPanel: true,dateFormat:"' . $dateFormat . '"';
            if ($start_input && $end_input) {
                $re_script = <<<EOF
<script>
$(function() {
    var datepicker_from_obj = $( "input[name={$start_input}_start]" );
    var datepicker_to_obj = $( "input[name={$end_input}_end]" );
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
    var datepicker_obj = $( "input[name={$start_input}]" ).datepicker({ {$date_config} });
    datepicker_obj.datepicker( $.datepicker.regional[ "zh-CN" ] );
});
</script>
EOF;
            }
            return $re_script;
        });
        Blade::directive('timepicker', function ($expression) {
            if (!isset($expression)) {
                return 'missing:start,[end]';
            }

            $end_input = '';
            $inputs    = explode(',', $expression);
            if (1 < count($inputs)) {
                list($start_input, $end_input) = $inputs;
            } else {
                $start_input = $inputs[0];
            }

            if ($start_input) {
                $start_input = '<?php echo "' . $start_input . '" ?>';
            }
            if ($end_input) {
                $end_input = '<?php echo "' . $end_input . '" ?>';
            }

            //日期与时分秒必须以空格隔开
            $format_arr = explode(' ', config('system.sys_date_detail'));
            if (preg_match('/[Y|m|d]/', $format_arr[0])) {
                $dateFormat = $format_arr[0];
                $timeFormat = $format_arr[1];
            } else {
                $dateFormat = $format_arr[1];
                $timeFormat = $format_arr[0];
            }
            $dateFormat  = str_replace(['Y', 'm', 'd'], ['yy', 'mm', 'dd'], $dateFormat);
            $timeFormat  = str_replace(['H', 'i', 's'], ['HH', 'mm', 'ss'], $timeFormat);
            $date_config = 'changeYear:true,changeMonth:true,numberOfMonths:1,dateFormat:"' . $dateFormat . '",timeFormat: "' . $timeFormat . '"';
            if ($start_input && $end_input) {
                $re_script = <<<EOF
$(function() {
    var datepicker_from_obj = $( "input[name={$start_input}]" );
    var datepicker_to_obj = $( "input[name={$end_input}]" );
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
EOF;
            } else {
                $re_script = <<<EOF
    var datepicker_obj = $( "input[name={$start_input}]" ).datetimepicker({ {$date_config} });
EOF;
            }
            return '<script type="text/javascript">' . $re_script . '</script>';
        });
        Blade::directive('kindeditor', function ($expression) {
            isset($expression) && $element = explode('|', $expression);
            if (!is_array($element)) {
                return 'missing:name[|name]';
            }

            //后期修改afterSelectFile
            $UploadFileUrl = route('UploadFile');
            $ManageFileUrl = route('ManageFile', ['t' => 'kindeditor']);
            $editor_config = json_encode([
                'uploadJson'            => $UploadFileUrl,
                'fileManagerJson'       => $ManageFileUrl,
                'extraFileUploadParams' => [
                    't'         => 'kindeditor',
                    'user_type' => Route::is("Admin::*"),
                    '_token'    => csrf_token(),
                ],
                'resizeType'            => 1,
                'themeType'             => 'simple',
                'allowFileManager'      => true,
            ]);
            $js_global = '';
            $js_create = '';
            foreach ($element as $e) {
                $js_global .= 'var kindeditor_' . $e . ";";
                $js_create .= 'kindeditor_' . $e . ' = ' . "K.create('textarea[name=\"" . $e . "\"]',{$editor_config});";

            }
            /*
             * KindEditor.options.htmlTags.script = [];
             */
            $kindeditor_js_src = asset('kindeditor/kindeditor-all-min.js');
            $re_script         = <<<EOF
<script type="text/javascript" src="{$kindeditor_js_src}"></script>
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
        });
        Blade::directive('uploadfile', function ($expression) {
            list($id, $type, $dir, $cb_fn) = explode(',', $expression);
            if (!$id || !$type || !$dir || !$cb_fn) {
                return "missing:id,type,cb_fn,dir";
            }

            $filemanage = '';
            switch ($type) {
                case 'insertfile':
                    $button = <<<EOF
            editor.loadPlugin('insertfile', function() {
                editor.plugin.fileDialog({
                    fileUrl : K('#url').val(),
                    clickFn : function(url, title) {
                        {$cb_fn}(url,title)
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
                    $lang1      = trans('common.selection') . trans('common.image');
                    $filemanage = <<<EOF
        $('#{$id}').after('<button id="{$id}_filemanage" type="button" class="btn btn-default ml20">{$lang1}</button>');
        K('#{$id}_filemanage').click(function() {
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
                            {$cb_fn}(url,title);
                        }
                        editor.hideDialog();
                    }
                });
            });
        });
EOF;
                    $button     = <<<EOF
            editor.loadPlugin('multiimage', function() {
                editor.plugin.multiImageDialog({
                    clickFn : function(urlList) {
                        K.each(urlList, function(i, data) {
                            var url = data.url;
                            var title = '';
                            {$cb_fn}(url,title);
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
                        {$cb_fn}(url, title, width, height, border, align);
                        editor.hideDialog();
                    }
                });
            });
EOF;
            }
            $UploadFileUrl = route('UploadFile');
            $ManageFileUrl = route('ManageFile', ['t' => $dir]);
            $editor_config = json_encode([
                'uploadJson'            => $UploadFileUrl,
                'fileManagerJson'       => $ManageFileUrl,
                'extraFileUploadParams' => [
                    't'         => 'kindeditor',
                    'user_type' => Route::is("Admin::*") ? 1 : 2,
                    '_token'    => csrf_token(),
                ],
                'resizeType'            => 1,
                'themeType'             => 'simple',
                'allowFileManager'      => true,
            ]);

            $re_script = <<<EOF
<import file="kindeditor/kindeditor-all-min" />
<script type="text/javascript">
$(function(){
    KindEditor.create();
    KindEditor.ready(function(K) {
        var editor = K.editor({$editor_config});
        {$filemanage}
        K('#{$id}').click(function() {
            {$button}
        });
    });
});
</script>
EOF;
            return $re_script;
        });
        Blade::directive('ckplayer', function ($expression) {
            $argv   = explode(',', $expression);
            $config = [];
            foreach ($argv as $value) {
                list($k, $v) = explode('=', $value);
                $config[$k] = $v;
            }
            return mCkplayer($expression);
        });
    }

    /**
     * Register the application services.
     * @return void
     */
    public function register()
    {
        //
    }
}
