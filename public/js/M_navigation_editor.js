/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_navigation_editor Class Javascript
    Include : jQuery jQueryUi.sortable()
    Included List(Module Controller Action)
    Admin Navigation add,edit
*/
'use strict';

function M_navigation_editor(config)
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
    if(0 == _self.edit_obj.length)console.log('edit_obj no exists');
    if(0 == _self.post_name.length)console.log('post_name no exists');
    if(0 >= _self.max_level)console.log('max_level must > 0');
    if(0 == _self.out_obj.length 
    || 0 == _self.edit_obj.length 
    || 0 == _self.post_name.length 
    || 0 >= _self.max_level)return;
    _self.initialize();
}

M_navigation_editor.prototype = 
{
    'out_obj':'',
    'edit_obj':'',
    'max_level':'',
    'def_data':'',
    'auto_increment':0,
    'post_name':'ext_info',
    'level_obj': Array(
        '<div class="col-sm-3">',
        '<button type="button" class="btn btn-default mb5 disabled" style="width:100%">',
        $Think.lang.add + $Think.lang.navigation + '</button></div>'
        ).join(''),
    'sortable_obj': Array(
        '<div class="list-group"></div>'
        ).join(''),
    'a_obj':Array(
        '<a class="list-group-item list-group-default mb5" style="min-height:42px;"><span mtype="name"></span>',
        '<span class="glyphicon glyphicon-remove fr" mtype="close" style="cursor:pointer;"></span>',
        '<input type="hidden" mtype="nav_data"></a>'
        ).join('')
}

M_navigation_editor.prototype.initialize = function()
{
    var _self = this;
    if(1 > _self.max_level)return;
    _self.edit_obj.find('[name=nav_text]').prop('disabled',true);
    _self.edit_obj.find('[name=nav_target]').prop('disabled',true);
    _self.edit_obj.find('[name=nav_link]').prop('disabled',true);
    for(var i = 0;i<_self.max_level;i++)
    {
        //初始化基本参数 level_obj
        var level_text = i + 1;
        var level_obj = $(_self.level_obj);
        level_obj.attr('mlevel',i);
        var level_obj_button = level_obj.find('button');
        level_obj_button.html('Level:'+ level_text + level_obj.find('button').html());
        if(i==0)
        {
            level_obj_button.on('click',function(){_self.add_nav(this,0)});
            level_obj_button.removeClass('disabled');
        }
        _self.out_obj.append(level_obj);
    }
    //初始化数据
    if(_self.def_data)
    {
        _self.initialize_data(_self.def_data,0,0);
    }
}

M_navigation_editor.prototype.initialize_data = function(data,level,pid)
{
    var _self = this;
    //不得超出设置的级别
    if(_self.max_level < level)return;
    var level_obj = _self.out_obj.find('[mlevel="' + level + '"]');
    $.each(data,function(k,v){
        //初始化 sortable 框
        var sortable_obj = level_obj.find('[mpid=' + pid + ']');
        if(0 == sortable_obj.length)
        {
            sortable_obj = $(_self.sortable_obj);
            sortable_obj.attr('mpid',pid);
        }
        //初始化 a_obj 导航
        var a_obj = $(_self.a_obj);
        var a_obj_id = _self.get_id();
        a_obj.find('[mtype="name"]').html(v.nav_text);
        a_obj.on('click',function(){_self.select_nav(this)});
        a_obj.find('[mtype="close"]').on('click',function(){_self.remove_nav(this)});
        a_obj.attr('mid',a_obj_id);
        var post_data = {};
        post_data.nav_id = a_obj_id;
        post_data.nav_pid = v.nav_pid;
        post_data.nav_text = v.nav_text;
        post_data.nav_target = v.nav_target;
        post_data.nav_link = v.nav_link;
        var post_data_str = JSON.stringify(post_data);
        //初始化 Input
        var input_hidden_obj = a_obj.find('[mtype=nav_data]');
        input_hidden_obj.val(post_data_str);
        input_hidden_obj.attr('name',_self.post_name + '[' + pid + '][]');
        //插入 Element
        sortable_obj.append(a_obj);
        level_obj.append(sortable_obj);
        level_obj.find('[mid=' + a_obj_id + ']').click();
        //回调继续添加
        if(null != v.nav_child)
        {
            _self.initialize_data(JSON.parse(v.nav_child),level+1,a_obj_id);
        }
    });
    var sortable_objs = _self.out_obj.find('.list-group');
    sortable_objs.sortable();
}

//添加新的导航
M_navigation_editor.prototype.add_nav = function(obj,pid)
{
    var _self = this;
    obj = $(obj);
    var level_obj = obj.parent();
    //var level = parseInt(level_obj.attr('mlevel'));
    //初始化 sortable 框
    var sortable_obj = level_obj.find('[mpid=' + pid + ']');
    if(0 == sortable_obj.length)
    {
        sortable_obj = $(_self.sortable_obj);
        sortable_obj.attr('mpid',pid);
    }
    //初始化 a_obj 导航
    var a_obj = $(_self.a_obj);
    var a_obj_id = _self.get_id();
    a_obj.attr('mid',a_obj_id);
    a_obj.find('[mtype="name"]').html($Think.lang.click+$Think.lang.edit);
    a_obj.find('[mtype=nav_data]').attr('name',_self.post_name + '[' + pid + '][]');
    a_obj.on('click',function(){_self.select_nav(this)});
    a_obj.find('[mtype="close"]').on('click',function(){_self.remove_nav(this)});
    //插入 Element
    sortable_obj.append(a_obj);
    sortable_obj.sortable();
    level_obj.append(sortable_obj);
    //添加后默认点击一下
    level_obj.find('[mid=' + a_obj_id + ']').click();
}

//删除导航条目
M_navigation_editor.prototype.remove_nav = function(obj)
{
    var _self = this;
    obj = $(obj);
    var nav_obj = obj.parent();
    var id = parseInt(nav_obj.attr('mid'));
    var next_level = parseInt(nav_obj.parent().parent().attr('mlevel')) + 1;
    var remove_child_obj = _self.out_obj.find('[mpid=' + id + ']');
    var remove_child_objs = remove_child_obj.find('[mtype="close"]');
    if(0 < remove_child_objs.length)
    {
        remove_child_objs.each(function(k,v){
            v.click();
        });
    }
    remove_child_obj.remove();
    nav_obj.remove();
    for(i = next_level;i < _self.max_level;i++)
    {
        var level_button = _self.out_obj.find('[mlevel=' + i + '] > button');
        level_button.off('click');
        level_button.addClass('disabled');
    }

    _self.edit_obj.find('[name=nav_text]').val('').prop('disabled',true);
    _self.edit_obj.find('[name=nav_target]').prop('selected',false).prop('disabled',true);
    _self.edit_obj.find('[name=nav_link]').val('').prop('disabled',true);
}

//编辑后保存
M_navigation_editor.prototype.save_nav = function(obj)
{
    var _self = this;
    obj = $(obj);
    var id = parseInt(obj.attr('mid'));
    var pid = parseInt(obj.parent().attr('mpid'));
    var post_data = {};
    post_data.nav_id = id;
    //post_data.nav_pid = pid;
    post_data.nav_text = _self.edit_obj.find('[name=nav_text]').val();
    post_data.nav_target = _self.edit_obj.find('[name=nav_target]').val();
    post_data.nav_link = _self.edit_obj.find('[name=nav_link]').val();
    var post_data_str = JSON.stringify(post_data);
    obj.find('[mtype=name]').html(post_data.nav_text + '&nbsp;');
    //提交时的数组结构
    obj.find('[mtype=nav_data]').val(post_data_str);
}

//选中后编辑
M_navigation_editor.prototype.select_nav = function(obj)
{
    var _self = this;
    obj = $(obj);
    var id = parseInt(obj.attr('mid'));

    //edit event
    var post_data_str = obj.find('[mtype=nav_data]').val();
    var post_data = {};
    if(post_data_str)
    {
        post_data = JSON.parse(post_data_str);
    }
    var edit_nav_text_obj = _self.edit_obj.find('[name=nav_text]');
    var edit_nav_link_obj = _self.edit_obj.find('[name=nav_link]');
    edit_nav_text_obj.val(post_data.nav_text);
    _self.edit_obj.find('[name=nav_target] > :selected').prop('selected',false);
    _self.edit_obj.find('[name=nav_target] > [value=' + post_data.nav_target + ']').prop('selected',true);
    edit_nav_link_obj.val(post_data.nav_link);
    edit_nav_text_obj.prop('disabled',false);
    _self.edit_obj.find('[name=nav_target]').prop('disabled',false);
    edit_nav_link_obj.prop('disabled',false);
    //编辑保存事件
    //on.click
    //_self.edit_obj.find('a').off('click').on('click',function(){_self.save_nav(obj)});
    //on.change
    _self.edit_obj.find('[name=nav_text],[name=nav_target],[name=nav_link]'
        ).off('change keyup').on('change keyup',function(){_self.save_nav(obj)});

    //button disabled
    var level_obj = obj.parent().parent();
    var next_level = parseInt(level_obj.attr('mlevel')) + 1;
    for(var i = 0;i < _self.max_level;i++)
    {
        //处理按钮样式
        var level_button = _self.out_obj.find('[mlevel=' + i + '] > button');
        var next_level_obj = level_button.parent();
        if(i <= next_level)
        {
            //处理添加按钮的事件 和 sortable的显示
            if(i == next_level)
            {
                next_level_obj.find('.list-group').addClass('hidden');
                next_level_obj.find('.list-group[mpid=' + id + ']').removeClass('hidden');
                level_button.off('click').on('click',function(){_self.add_nav(this,id)});
            }
            level_button.removeClass('disabled');
        }
        else
        {
            next_level_obj.find('.list-group').addClass('hidden');
            level_button.addClass('disabled');
        }
    }
    //button active
    _self.out_obj.find('.active').removeClass('active');
    obj.addClass('active');
}

M_navigation_editor.prototype.get_id = function()
{
    var _self = this;
    return ++_self.auto_increment;
}