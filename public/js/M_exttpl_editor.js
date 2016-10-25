/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_exttpl_editor Class Javascript
    Include : jQuery jQueryUi.sortable()
    Included List(Module Controller Action)
    Admin Admin add,edit
    Admin AdminGroup add,edit
    Admin Member add,edit
    Admin MemberGroup add,edit
    Admin Article add,edit
    Admin ArticleCategory add,edit
*/
'use strict';

function M_exttpl_editor(config)
{
    if('object' != typeof(config))
    {
        console.log('config no exists');
        return;
    }
    var _self = this;
    $.extend(_self,config);
    //检查初始化元素
    if('add' != _self.run_type && 'edit' != _self.run_type && 'add_edit' != _self.run_type)
    {
        console.log('run_type error');
        return;
    }
    if(0 == _self.out_obj.length)console.log('out_obj no exists');
    if(0 == _self.edit_obj.length)console.log('edit_obj no exists');
    if(0 == _self.run_type.length)console.log('run_type no exists');
    if(0 == _self.post_name.length)console.log('post_name no exists');
    if(0 == _self.out_obj.length
    || 0 == _self.edit_obj.length
    || 0 == _self.run_type.length
    || 0 == _self.post_name.length
    )return;
    _self.initialize();
}

M_exttpl_editor.prototype = 
{
    'run_type':'',
    'out_obj':'',
    'edit_obj':'',
    'def_data':'',
    'post_name':'',
    'ajax_url':'',
    'add_type_edit_obj':Array(
        '<input mtype="in_val" class="form-control input-sm w200 fl"/>',
        '<button mtype="in_add" type="button" class="btn btn-default input-sm" >' + lang.common.add + '</button>'
    ).join(''),
    'add_type_obj':Array(
        '<li class="list-group-item input-sm mb5" style="padding: 5px 10px;">',
        '<input mtype="exttpl_data" type="hidden" /><span mtype="in_name"></span>',
        '<span mtype="remove_btn" class="glyphicon glyphicon-remove fr" style="margin:2px 0px"></span></li>'
    ).join(''),
    'edit_type_obj':Array(
        '<div id="ext_info" class="col-sm-6"><div class="form-group">',
        '<label class="col-sm-4 control-label" mtype="exttpl_title"></label>',
        '<div class="col-sm-6 "><input type="text" class="form-control" mtype="exttpl_data"/>',
        '</div></div></div>'
    ).join('')
}

M_exttpl_editor.prototype.initialize = function()
{
    var _self = this;
    switch(_self.run_type)
    {
        //添加key
        case 'add':
            var add_type_edit_obj = $(_self.add_type_edit_obj);
            _self.edit_obj.append(add_type_edit_obj);
            _self.edit_obj.find('[mtype="in_add"]').on('click',function(){_self.add_exttpl()});
            _self.out_obj.sortable();
            break;
        //在已有key时的编辑value
        case 'edit':
            _self.edit_obj.on('change',function(){
                _self.change_exttpl(this);
            });
            break;
        //添加编辑key value
        case 'add_edit':
            _self.edit_obj.find('[mtype="in_add"]').on('click',function(){_self.add_edit_exttpl()});
            _self.out_obj.sortable();
            break;
    }
    _self.initialize_data(_self.def_data);
}

M_exttpl_editor.prototype.initialize_data = function(data)
{
    var _self = this;
    if(0 >= _self.edit_obj.length 
    || 'object' != typeof(_self.def_data))return;
    switch(_self.run_type)
    {
        case 'add':
            $.each(data,function(k,v){
                if(v)
                {
                    _self.add_exttpl_obj(v);
                }
            });
            break;
        case 'edit':
            _self.out_obj.html('');
            $.each(data,function(k,v){
                if(k)
                {
                    _self.change_exttpl_obj(k,v);
                }
            });
            break;
        case 'add_edit':
            _self.out_obj.html('');
            $.each(data,function(k,v){
                if(k)
                {
                    _self.add_edit_exttpl_obj(k,v);
                }
            });
            break;
    }
}

//添加新的扩展信息 事件
M_exttpl_editor.prototype.add_exttpl = function()
{
    var _self = this;
    var in_val_obj = _self.edit_obj.find('[mtype=in_val]');
    var in_val = in_val_obj.val();
    if('' != in_val)
    {
        in_val_obj.val('');
        _self.add_exttpl_obj(in_val);
    }
}

//添加新的扩展信息 对象
M_exttpl_editor.prototype.add_exttpl_obj = function(in_val)
{
    var _self = this;
    //防止 添加的数据列名称重复 开始 PS:导致形成数组时键值重复
    var old_val = _self.out_obj.find('input[value=' + in_val + ']').val();
    if(old_val == in_val)
    {
        _self.edit_obj.find('[mtype=in_val]').val(lang.common.exists + ':' + in_val).prop('disabled', true);
        _self.edit_obj.find('[mtype=in_add]').html(lang.common.confirm);
        _self.edit_obj.find('[mtype=in_add]').off('click').on('click',function(){
            _self.edit_obj.find('[mtype=in_val]').val('').prop('disabled',false);
            _self.edit_obj.find('[mtype=in_add]').html(lang.common.add);
            _self.edit_obj.find('[mtype="in_add"]').off('click').on('click',function(){_self.add_exttpl()});
        });
        return;
    }
    //防止 添加的数据列名称重复 结束
    var add_type_obj = $(_self.add_type_obj);
    add_type_obj.find('[mtype="in_name"]').html(in_val);
    add_type_obj.find('[mtype="exttpl_data"]').attr('name',_self.post_name).val(in_val);
    add_type_obj.find('[mtype="remove_btn"]').on('click',function(){
        add_type_obj.remove();
    });
    _self.out_obj.append(add_type_obj);
}

//选择新的扩展信息 事件
M_exttpl_editor.prototype.change_exttpl = function(obj)
{
    var _self = this;
    var obj = $(obj);
    var current_id = obj.val();
    var def_id = obj.find('option[mtype="def_data"]').val();
    if(current_id == def_id)
    {
        _self.out_obj.html('');
        _self.initialize_data(_self.def_data);
        return;
    }
    var post_data = {
        'type':'get_data',
        'field':'exttpl_id',
        'data':{'id':current_id}
    };
    $.ajax({
        'url':_self.ajax_url,
        'data':post_data,
        'type':'POST',
        'dataType':'JSON',
        'cache':false,
        'success':function(data)
        {
            if(data && data.status)
            {
                _self.out_obj.html('');
                $.each(data.info,function(k,v){
                    if(k)
                    {
                        _self.change_exttpl_obj(k,v);
                    }
                });
            }
        },
        'error':function()
        {
            console.log('M_exttpl_editor ajax error' + _self.ajax_url);
        }
    });
}

//选择新的扩展信息 对象
M_exttpl_editor.prototype.change_exttpl_obj = function(in_name,in_val)
{
    var _self = this;
    var edit_type_obj = $(_self.edit_type_obj);
    edit_type_obj.find('[mtype="exttpl_title"]').html(in_name);
    edit_type_obj.find('[mtype="exttpl_data"]').attr('name',_self.post_name + '[' + in_name + ']');
    edit_type_obj.find('[mtype="exttpl_data"]').val(in_val);
    _self.out_obj.append(edit_type_obj);
}


//添加新的扩展信息 事件
M_exttpl_editor.prototype.add_edit_exttpl = function()
{
    var _self = this;
    var in_val_obj = _self.edit_obj.find('[mtype=in_val]');
    var in_name = in_val_obj.val();
    if('' != in_name)
    {
        in_val_obj.val('');
        _self.add_edit_exttpl_obj(in_name);
    }
}

//添加新的扩展信息 对象
M_exttpl_editor.prototype.add_edit_exttpl_obj = function(in_name,in_val)
{
    var _self = this;
    //防止 添加的数据列名称重复 开始 PS:导致形成数组时键值重复
    var old_val = _self.out_obj.find('input[name="' + _self.post_name + '[' + in_name + ']' + '"]');
    if(0 < old_val.length)
    {
        _self.edit_obj.find('[mtype=in_val]').val(lang.common.exists + ':' + in_name).prop('disabled', true);
        _self.edit_obj.find('[mtype=in_add]').html(lang.common.confirm);
        _self.edit_obj.find('[mtype=in_add]').off('click').on('click',function(){
            _self.edit_obj.find('[mtype=in_val]').val('').prop('disabled',false);
            _self.edit_obj.find('[mtype=in_add]').html(lang.common.add + lang.common.extend + lang.common.info);
            _self.edit_obj.find('[mtype="in_add"]').off('click').on('click',function(){_self.add_edit_exttpl()});
        });
        return;
    }
    //防止 添加的数据列名称重复 结束
    var edit_type_obj = $(_self.edit_type_obj);
    var exttpl_title_obj = edit_type_obj.find('[mtype="exttpl_title"]');
    var exttpl_data_obj = edit_type_obj.find('[mtype="exttpl_data"]');
    exttpl_title_obj.html(in_name + '<span mtype="remove_btn" class="glyphicon glyphicon-remove fr" style="margin:2px 5px"></span>');
    var remove_obj = exttpl_title_obj.find('[mtype="remove_btn"]');
    remove_obj.on('click',function(){
        remove_obj.parents('#ext_info').remove();
    });
    exttpl_data_obj.attr('name',_self.post_name + '[' + in_name + ']');
    exttpl_data_obj.val(in_val);
    _self.out_obj.append(edit_type_obj);
}