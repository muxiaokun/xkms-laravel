/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
   Common Module Javascript
   关于该文件的注意事项 所有的第三方方法都会通过本文件的方法 在执行}
   相关 js 如果不在js文件中 请查看 Common/Common/function.php}
   语言包自动引入自动引入system_js的功能 U('Common/Common/system_js') root lang
*/
'use strict';

var M_test_val;

$(function(){
    //异步加载图片默认js
    M_img_sync_load();
    $(window).bind('scroll',function(){
        M_img_sync_load();
    });
    //UI优化鼠标移动到input[type=text],textarea赋予焦点并全选内容
    $('input[type=text],textarea').on('mouseover',function(){
        $(this).focus().select();
    });
});

//异步加载图片 <M:img src="" />
//必须包含$.base64
function M_img_sync_load()
{
    if(!$.base64)
    {
        console.log('please include jquery-ui:$.base64');
        return;
    }
    $('img[M_img]').each(function(k,v){
        var obj = $(v);
        var sync_img = new Image();
        var sync_obj = $($.base64.atob(obj.attr('M_img')));
        var sync_src = obj.attr('M_img_src');
        var obj_start = $(obj).offset()['top'];
        var obj_end = obj_start + $(obj).height();
        var window_start = $(window).scrollTop();
        var window_end = window_start + $(window).height();
        if(obj.data('is_sync') == true
        || window_start > obj_end 
        || window_end < obj_start
        )return;
        obj.data('is_sync',true);
        sync_img.onload = function()
        {
            obj.fadeOut(500,'',function(){
                sync_obj.hide();
                obj.before(sync_obj);
                obj.remove();
                sync_obj.attr('src',sync_src).fadeIn(500);
                obj.data('is_sync',false);
            });
        };
        sync_img.src = sync_src;
    });
}

function M_jqueryui_tooltip(obj)
{
    var obj = $(obj);
    if(!obj.tooltip)
    {
        console.log('please include jquery-ui:obj.tooltip');
        return;
    }
    obj.tooltip({ 
        track: true
    });
}

function M_change_verify(obj,input_obj)
{
    var img_obj = $(obj);
    if(input_obj && 0 < input_obj.length)input_obj.val('');
    if(img_obj.data('is_sync'))return;
    var old_src = img_obj.attr('src');
    var rand_int = parseInt(Math.random()*1000);
    var new_src = '';
    var preg = /(^(.*)((\?\d{3})|(\&\d{3}))$)|(^([^\&\?]*)$|^(.*)$)/;
    var verify_src = preg.exec(old_src);
    if(!verify_src)return;
    var str = '';
    (verify_src[8] || verify_src[5]) && (str = '&');
    (verify_src[7] || verify_src[4]) && (str = '?');
    (verify_src[8] || verify_src[7]) && (new_src = verify_src[6] + str + rand_int);
    (verify_src[5] || verify_src[4]) && (new_src = verify_src[2] + str + rand_int);
    img_obj.data('is_sync', true);
    var sync_img = new Image();
    sync_img.onload = function ()
    {
        img_obj.fadeOut(500,'',function(){
            img_obj.attr('src',new_src).fadeIn(500);
            img_obj.data('is_sync', false);
        });
    };
    sync_img.src = new_src;
}

function M_confirm(str,go_link,go_back)
{
    var obj = $('#alert_confirm');
    if(!obj.length)
    {
        obj = $('<div id="alert_confirm" title="' + lang.common.system + lang.common.info + '" >' + str + '</div>');
    }
    else
    {
        obj.html(str)
    }
    if(!obj.dialog)
    {
        console.log('please include jquery-ui:obj.dialog');
        return;
    }
    var buttons_fn = {};
    buttons_fn[lang.common.confirm] = function () {
        window.location.href = go_link;
        $( this ).dialog( "close" );
    }
    buttons_fn[lang.common.cancel] = function () {
        if(true == go_back)
        {
            history.go(-1);
        }
        $( this ).dialog( "close" );
    }
    obj.dialog({
        resizable: false,
        buttons:buttons_fn
    });
    return false;
}

function M_allselect_par(obj,parent_str)
{
    var objs = $(obj).parents(parent_str).find('input[type=checkbox]');
    objs.prop('checked',$(obj).prop('checked'));
}

//只能输入整数
//onKeyup="M_in_int(this);"
function M_in_int(obj)
{
    obj = $(obj);
    var input_val = obj.val();
    if('' != input_val)
    {
        input_val = parseInt(input_val);
        if('' === input_val)input_val = '';
    }
    obj.val(input_val);
    if(!obj.data('onkeypress'))
    {
        obj.attr('onKeypress',Array(
            'var keyCode = event.charCode;',
            'if(47 < keyCode && 58 > keyCode || 0 == keyCode)return true;',
            'return false;'
        ).join('')
        );
        obj.data('onkeypress',1);
    }
}

//只能输入浮点数 DECIMAL
//onKeyup="M_in_decimal(this);"
function M_in_decimal(obj,max_decimal)
{
    obj = $(obj);
    if(!parseInt(max_decimal))max_decimal = 2;
    var preg = RegExp('0?(\\d+(\\.\\d{0,'+ max_decimal +'})?)');
    var input_val = obj.val();
    if(!input_val)return;
    var new_input_val = input_val.match(preg);
    input_val = new_input_val[0] == input_val?new_input_val[1]:0;
    obj.val(input_val);
    if(!obj.data('onkeypress'))
    {
        obj.attr('onKeypress',Array(
            'var keyCode = event.charCode;',
            'if(47 < keyCode && 58 > keyCode || 0 == keyCode || 46 == keyCode)return true;',
            'return false;'
        ).join('')
        );
        obj.data('onkeypress',1);
    }
}

//输入整数范围
//onKeyup="M_in_int_range(this,1,100);"
function M_in_int_range(obj,start,end)
{
    M_in_int(obj);
    obj = $(obj);
    var number = obj.val();
    if(number < start && number != '')
    {
        obj.val(start);
    }
    if(number > end)
    {
        obj.val(end);
    }
}

//复制插件 传入id
function M_ZeroClipboard(obj_id)
{
    if(!ZeroClipboard)
    {
        console.log('please include jquery-ui:ZeroClipboard');
        return;
    }
    $(function(){
        ZeroClipboard.config({swfPath: 'asset(ZeroClipboard.swf)'});
        var obj = $('#'+obj_id);
        var clip = new ZeroClipboard(obj,{cacheBust: true});
        var dialog_div = $('<div style="text-align:center;"></div>')
        dialog_div.attr('title', lang.common.copy + lang.common.info);
        var buttons_fn = {};
        buttons_fn[lang.common.close] = function () {
            $(this).dialog( "close" );
        }
        clip.on("ready", function() {
            //clip.setData("text/plain", obj.attr('copy_content'));
            this.on("aftercopy", function(event) {
                dialog_div.html(lang.common.copy + lang.common.success);
                dialog_div.dialog({
                    resizable: false,
                    buttons: buttons_fn
                });
            });
        });
        clip.on("error", function(event) {
            dialog_div.html(lang.common.copy + lang.common.initialize + lang.common.error);
            dialog_div.dialog({
                resizable: false,
                buttons:buttons_fn
            });
            clip.destroy();
        });
    });
}

//行编辑器
function M_line_edit(obj)
{
    var _self = $(obj);
    var status = _self.data('status');
    if('ready' != status)
    {
        _self.data('status','ready');
        if('error' != status)
        {
            var value = _self.html().trim();
            _self.attr('value',value);
        }
        else
        {
            var value = _self.attr('value');
        }
        var input_obj = $('<input value="'+value+'" onKeyup="M_in_int(this);" style="width:100%"/>');
        _self.html(input_obj);
        input_obj.focus();
        input_obj.blur(function(){
            var id = _self.attr('field_id');
            var field = _self.attr('field');
            var link = _self.attr('link');
            var sub_value = input_obj.val();
            if(sub_value == value)
            {
                _self.data('status','error');
                _self.html(value);
                return;
            }
            $.ajax({
                url:link,
                type:'post',
                dataType:'json',
                cache:false,
                data:{'type':'line_edit','field':field,'data':{'id':id,'value':sub_value}},
                success:function(data)
                {
                    if(data.status)
                    {
                        _self.data('status','success');
                    }
                    else
                    {
                        _self.data('status','error');
                    }
                    _self.html(data.info);
                }
            });
            _self.html(lang.common.loading + '...');
        });
    }
}

//中文转拼音
function M_zh2py(obj,to)
{
    var _self = $(obj);
    _self.prop('disabled',true);
    var link = _self.attr('link');
    $.ajax({
        url:link,
        type:'post',
        dataType:'json',
        cache:false,
        data:{'type':'zh2py','data':_self.val()},
        success:function(data)
        {
            if(data.status)
            {
                $(to).val(data.info);
            }
            _self.prop('disabled',false);
        }
    });
}