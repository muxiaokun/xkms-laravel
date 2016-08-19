/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_valid Class Javascript
    Include : jQuery jQueryUi.dialog()
    Included List(Module Controller Action)
    Admin AdminGroup addedit
    Admin Admin addedit
    Admin Index edit my pass
    Admin MemberGroup addedit
    Admin Member addedit
    Admin Wechat addedit
    Home Assess add
*/
'use strict';

//ajax验证
function M_valid(config)
{
    if('object' != typeof(config))
    {
        console.log('config no exists');
        return;
    }
    var _self = this;
    $.extend(_self,config);
    //检查初始化元素
    if(0 == _self.form_obj.length 
    || 0 == _self.check_list.length
    || 0 == _self.ajax_url.length)
    {
        console.log('M_valid create error');
        return;
    }
    _self.initialize();
}

M_valid.prototype = 
{
    'check_list':Array(),
    'form_obj':Object(),
    //下面的每一个成员 0 未检查 1检测中 2检测成功 
    'checked':Object(),
    'ajax_url':''
}

M_valid.prototype.initialize = function()
{
    var _self = this;
    for(var field in _self.check_list)
    {
        _self.form_obj.find('input[name="'+ field +'"]').on('change',function(){
            _self.check($(this).attr('name'))
        });
        _self.checked[field] = 0;
    }
    _self.form_obj.attr('onSubmit','');
    _self.form_obj.submit(function(){return _self.submit()});
    
}

M_valid.prototype.submit = function()
{
    var _self = this;
    for(var field in _self.check_list)
    {
        //循环需要检查的Input 如果没有检查成功 则进行检查
       if(0 == _self.checked[field])_self.check(field,true);
    }

    var fiel_count = 0;
    var fiel_c_count = 0;
    for(var field in _self.check_list)
    {
        fiel_count++;
        //循环需要检查的Input 如果没有检查成功 则进行检查
        if(2 == _self.checked[field])fiel_c_count++;
    }
    if(fiel_count == fiel_c_count)
    {
        return true;
    }
    return false;
}

M_valid.prototype.check = function(field,is_cb)
{
    var _self = this;
    if('object' == typeof(field))
    {
        field = field.data;
    }
    //检查的时候先对checked下的对象标记 检查中
    _self.checked[field] = 1;
    var data = {'type':'validform','field':field,'data':{}};
    for(var i = 0;i < _self.check_list[field].length;i++)
    {
        eval('data.data.' + _self.check_list[field][i] + ' = "' + _self.form_obj.find('input[name="'+ _self.check_list[field][i] +'"]').val() + '"')
    }
    $.ajax({
        'dataType':'json',
        'type':'post',
        'cache':false,
        'url':_self.ajax_url,
        'data':data,
        'success':function(data)
        {
            var input_group = _self.form_obj.find('input[name="'+ field +'"]').parent().parent();
            var msg_obj = input_group.find('div[mtype="msg"]');
            if(0 == msg_obj.length)
            {
                input_group.append('<div class="" mtype="msg"></div>');
                msg_obj = input_group.find('div[mtype="msg"]');
            }
            if(true == data.status)
            {
                _self.checked[field] = 2;
                msg_obj.html('<span class="glyphicon glyphicon-ok fs12 mt10" style="color:green;"></span>');
                if(is_cb)_self.form_obj.submit();
            }
            else
            {
                _self.checked[field] = 0;
                msg_obj.html('<span class="glyphicon glyphicon-remove fs12 mt5 mr10 fl" style="color:red;"></span><span class="help-block">' + data.info + '</span>');
            }
        },
        'error':function()
        {
            window.console.log('M_valid ajax error' + _self.ajax_url);
        }
    });
}