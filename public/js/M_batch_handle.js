/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_alert_log Class Javascript
    Include : jQuery $.dialog
    Included List(Module Controller Action)
    Admin Admin Index
*/
'use strict';

function M_batch_handle(config)
{
    if('object' != typeof(config))
    {
        console.log('config no exists');
        return;
    }
    var _self = this;
    $.extend(_self,config);
    //检查初始化元素
    if(0 == _self.out_obj.length)console.log('out_obj no exists');
    if(0 == _self.type_data)console.log('type_data no exists');
    if(0 == _self.post_field)console.log('post_field no exists');
    if(0 == _self.out_obj.length
    || 0 == _self.post_field.length
    || 0 == _self.type_data)return;
    _self.initialize();
}

M_batch_handle.prototype = 
{
    'out_obj':{},
    'form_obj':Array(
        '<form method="post" onSubmit="return false"><div class="col-sm-6">',
        '<select class="form-control"></select></div>',
        '<button type="submit" class="btn btn-default col-sm-2">',
        lang.submit + '</button>',
        '</form>'
        ).join(''),
    'option_obj':'<option></option>',
    'hidden_input_obj':'<input type="hidden" />',
    'post_field':'id[]',
    'type_data':{}
}

//初始化 提交表单
M_batch_handle.prototype.initialize = function()
{
    var _self = this;
    var form_obj = $(_self.form_obj);
    
    //初始化选择框
    var select_obj = form_obj.find('select');
    var option_obj = $(_self.option_obj);
    option_obj.val(-1);
    option_obj.html(lang.selection + lang.batch + lang.handle);
    select_obj.append(option_obj);
    $.each(_self.type_data,function(k,v){
        option_obj = $(_self.option_obj);
        option_obj.data(v);
        option_obj.html(v.name);
        option_obj.val(k);
        select_obj.append(option_obj);
    });
    _self.out_obj.append(form_obj);
    
    //绑定提交事件
    form_obj.on('submit',function(){return _self.form_submit()});
    form_obj.attr('onSubmit','');
}


//提交后 修改提交表单数据
M_batch_handle.prototype.form_submit = function()
{
    var _self = this;
    
    //设置需要传的ext_data
    var select_obj =  _self.out_obj.find('select');
    if(-1 == select_obj.val())
    {
        _self.dialog(lang.please + lang.selection + lang.handle);
        return false;
    }
    var option_obj = _self.out_obj.find('option[value="' + select_obj.val() + '"]');
    var type_data = option_obj.data();
    var out_form_obj = _self.out_obj.find('form');
    out_form_obj.attr('action',type_data.post_link);
    out_form_obj.find('input[type=hidden][mtype=ext_data]').remove();
    
    var hidden_input_obj = $(_self.hidden_input_obj);
    if(type_data.post_data)
    {
        $.each(type_data.post_data,function(k,v){
            hidden_input_obj.attr('mtype','ext_data');
            hidden_input_obj.attr('name',k);
            hidden_input_obj.val(v);
            out_form_obj.append(hidden_input_obj);
        });
    }
    
    //设置需要传的field值
    var post_fields = $('input[name="' + _self.post_field + '"][type=checkbox][value]:checked');
    if(0 == post_fields.length)
    {
        _self.dialog(lang.please + lang.selection + lang.id);
        return false;
    }
    else
    {
        var out_form_obj = _self.out_obj.find('form');
        out_form_obj.find('input[type=hidden][mtype=field]').remove();
        $.each(post_fields,function(k,v){
            var hidden_input_obj = $(_self.hidden_input_obj);
            hidden_input_obj.attr('mtype','field');
            hidden_input_obj.attr('name',_self.post_field);
            hidden_input_obj.val($(v).val());
            out_form_obj.append(hidden_input_obj);
        });
        return true;
    }
}

//提示窗口
M_batch_handle.prototype.dialog = function(msg)
{
    var dialog_div = $('<div style="text-align:center;"></div>')
    dialog_div.attr('title', lang.system + lang.info);
    dialog_div.html(msg);
    var buttons_fn = {};
    buttons_fn[lang.close] = function () {
        $(this).dialog( "close" );
    }
    dialog_div.dialog({
        resizable: false,
        buttons: buttons_fn
    });
}