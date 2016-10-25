/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_select_add Class Javascript
    Include : jQuery
    Included List(Module Controller Action)
    Admin AdminGroup addedit
    Admin MemberGroup addedit
    Admin ArticleCategory addedit
    Admin ArticleChannel addedit
    Home Assess add
*/
'use strict';

//从select元素中增加数据
function M_select_add(config)
{
    if('object' != typeof(config))
    {
        console.log('config no exists');
        return;
    }
    var _self = this;
    $.extend(_self,config);
    //检查初始化元素
    if(0 == _self.edit_obj.length)console.log('edit_obj no exists');
    if(0 == _self.post_name.length)console.log('post_name no exists');
    if(0 == _self.ajax_url.length)console.log('post_name no exists');
    if(0 == _self.edit_obj.length 
    || 0 == _self.post_name.length
    || 0 == _self.ajax_url.length)return;
    //初始化元素
    if(0 < _self.out_obj.length)
    {
        //在选择值组时需要给默认的一个Input以便提交空数据
        var null_post_name =  _self.post_name.replace(/(\[\w*?\])*$/,'');
        _self.out_obj.append('<input type="hidden" name="' + null_post_name + '" />');
    }
    _self.initialize_search(_self);
    _self.initialize_data(_self);
}

M_select_add.prototype = {
    'out_obj':'',
    'edit_obj':'',
    'post_name':'',
    'def_data':'',
    'ajax_url':'',
    'field':false,
    //插入的元素
    'insert_obj':Array(
        '<div class="fl" style="margin:2px;">',
        '<span class="label label-default"><span mtype="name"></span>',
        '<span class="glyphicon glyphicon-remove fs12 ml5" mtype="close"></span></span>',
        '<input mtype="data" type="hidden" /></div>'
    ).join(''),
    //选择select
    'select_obj':Array(
        '<select class="form-control input-sm w200 fl" mtype="select">',
        '<option value="0">' + lang.common.none + lang.common.selection + '</option>',
        '</select>'
    ).join(''),
    //搜索input
    'search_input_obj':Array(
        '<input class="form-control input-sm w200 fl" placeholder="',
        lang.common.please + lang.common.input + '"  mtype="keyword" />'
    ).join(''),
    //搜索按钮
    'search_obj':Array(
        '<button class="btn btn-default input-sm fl" type="button" mtype="search_btn">',
        lang.common.dont + lang.common.exists + lang.common.comma,
        lang.common.please + lang.common.search + '</button>'
    ).join('')
}

//初始化 搜索
M_select_add.prototype.initialize_search = function()
{
    var _self = this;
    var select_obj = _self.edit_obj.find('[mtype=select]');
    var search_btn_obj = _self.edit_obj.find('[mtype=search_btn]');
    if(0 < select_obj.length)select_obj.remove();
    if(0 < search_btn_obj.length)search_btn_obj.remove();
    var search_input_obj = $(_self.search_input_obj);
    var search_obj = $(_self.search_obj);
    if(0 == _self.out_obj.length && _self.def_data)
    {
        search_input_obj.val(_self.def_data.html);
        var input_hidden = _self.edit_obj.find('input[type=hidden][name=' + _self.post_name +']');
        if(0 < input_hidden.length)
        {
            input_hidden.val(_self.def_data.value);
        }
    }
    search_obj.html(lang.common.search);
    search_obj.on('click',function(){_self.search_get(this)});
    _self.edit_obj.append(search_obj);
    search_obj.before(search_input_obj);
}

//初始化 选择
M_select_add.prototype.initialize_select = function(data)
{
    var _self = this;
    _self.edit_obj.find('[mtype=keyword]').remove();
    _self.edit_obj.find('[mtype=search_btn]').remove();
    //创建select_obj及其事件
    var select_obj = $(_self.select_obj);
    
    select_obj.on('change',function(){
        //select 选中后初始化 insert_id insert_name
        var current_obj = $(this).find(':selected');
        var insert_id = parseInt(current_obj.attr('value'));
        var insert_name = current_obj.html().trim();
        
        // 没有输出元素 就不进行模板的生成
        if(0 < _self.out_obj.length)
        {
            _self.insert(insert_id,insert_name);
            current_obj.remove();
            return;
        }
        var input_hidden = _self.edit_obj.find('input[type=hidden][name=' + _self.post_name +']');
        if(0 < input_hidden.length)
        {
            input_hidden.val(insert_id);
        }
    });
    //初始化select_obj数据
    if(data && 0 < data.length)
    {
        $.each(data,function(k,v){
            if($.isNumeric(v.value))
            {
                var option_obj = $('<option></option>');
                option_obj.attr('value',v.value);
                option_obj.html(v.html);
                select_obj.append(option_obj);
            }
        });
    }
    _self.edit_obj.append(select_obj);
    //创建search_obj及其事件
    var search_obj = $(_self.search_obj);
    search_obj.on('click',function(){_self.initialize_search(this)});
    _self.edit_obj.append(search_obj);
}

M_select_add.prototype.initialize_data = function()
{
    var _self = this;
    if(_self.def_data)
    {
        $.each(_self.def_data,function(k,v){
            if(v.value)
            {
                _self.insert(v.value,v.html);
            }
        });
    }
}

M_select_add.prototype.insert = function(insert_id,insert_name)
{
    var _self = this;
    if(0 == _self.out_obj.length)return;
    var insert_obj = $(_self.insert_obj);
    insert_obj.find('[mtype=data]').attr('name',_self.post_name).val(insert_id);
    insert_obj.find('[mtype=name]').html(insert_name);
    insert_obj.find('[mtype=close]').on('click',function(){_self.del_insert(this)});
    _self.out_obj.append(insert_obj);
}

M_select_add.prototype.del_insert = function(obj)
{
    var _self = this;
    var obj = $(obj);
    var current_obj = obj.parent().parent();
    var insert_id = parseInt(current_obj.find('[mtype=data]').val());
    var insert_name = current_obj.find('[mtype=name]').html().trim();
    current_obj.remove();
    var option_obj = $('<option></option>');
    option_obj.attr('value',insert_id);
    option_obj.html(insert_name);
    _self.edit_obj.find('select').append(option_obj);
}

M_select_add.prototype.search_get = function(obj)
{
    var _self = this;
    var obj = $(obj);
    var is_run = obj.data('is_run');
    if(is_run)return;
    $(obj).prop('disabled',true).data('is_run',true);
    var current_obj = obj.parent();
    var keyword = current_obj.find('[mtype=keyword]').val();
    var inserted = Array();
    if(_self.out_obj)
    {
        _self.out_obj.find('input').each(function(k,v){
            inserted.push($(v).val());
        });
    }
    var post_data = {
        'type':'get_data',
        'field':_self.field,
        'data':{'keyword':keyword,'inserted':inserted}
    };
    $.ajax({
        'url':_self.ajax_url,
        'data':post_data,
        'type':'POST',
        'dataType':'JSON',
        'cache':false,
        'success':function(data)
        {
            if(data.status)
            {
                _self.initialize_select(data.info);
            }
            $(obj).prop('disabled',false).data('is_run',false);
        },
        'error':function()
        {
            console.log('M_select_add ajax error' + _self.ajax_url);
        }
    });
}