/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_navigation_tree Class Javascript
    Include : jQuery jQueryUi.sortable()
    Included List(Module Controller Action)
    Admin Assess add,edit
*/
'use strict';

function M_assess_editor(config)
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
    if(0 == _self.out_obj.length
    || 0 == _self.edit_obj.length
    || 0 == _self.post_name.length
    )return;
    _self.initialize();
}

M_assess_editor.prototype = 
{
    'out_obj':'',
    'edit_obj':'',
    'source_obj':'',
    'def_data':'',
    'post_name':'ext_info',
    'tr_obj':Array(
        '<tr><td mtd="p">&nbsp;</td><td mtd="f"></td><td mtd="mg"></td>',
        '<td class="text-center" mtype="close"><a class="btn btn-default btn-sm">' + lang.common.del + '</a></td>',
        '<input mtype="assess_data" type="hidden"></tr>'
        ).join('')
}

M_assess_editor.prototype.initialize = function()
{
    var _self = this;
    _self.edit_obj.find('input').prop('disabled',true);
    //初始化editor add button
    _self.edit_obj.find('[mtype=add_assess]').on('click',function(){_self.add_assess(_self)});
    _self.out_obj.sortable();
    if(_self.def_data)
    {
        _self.initialize_data(_self.def_data);
    }
}

M_assess_editor.prototype.initialize_data = function(data)
{
    var _self = this;
    if(0 < _self.out_obj.find('tr').length)return;
    $.each(data,function(k,v){
        var post_data = v;

        //初始化 a_obj 考核
        var tr_obj = $(_self.tr_obj);
        tr_obj.find('[mtd="p"]').html(post_data.p);
        tr_obj.find('[mtd="f"]').html(post_data.f);
        tr_obj.find('[mtd="mg"]').html(post_data.mg);
        tr_obj.on('click',function(){_self.select_assess(this)});
        tr_obj.find('[mtype="close"]').on('click',function(){_self.remove_assess(this)});

        //初始化 Input
        var post_data_str = JSON.stringify(post_data);
        var input_hidden_obj = tr_obj.find('[mtype=assess_data]');
        input_hidden_obj.val(post_data_str);
        input_hidden_obj.attr('name',_self.post_name + '[]');

        _self.out_obj.append(tr_obj);
    });
}

//添加新的考核
M_assess_editor.prototype.add_assess = function()
{
    var _self = this;
    //初始化 a_obj 考核
    var tr_obj = $(_self.tr_obj);
    tr_obj.find('[mtype=assess_data]').attr('name',_self.post_name + '[]');
    tr_obj.on('click',function(){_self.select_assess(this)});
    tr_obj.find('[mtype="close"]').on('click',function(){_self.remove_assess(this)});

    //插入 Element
    _self.out_obj.append(tr_obj);
    //添加后默认点击一下
    tr_obj.click();
}

//删除考核条目
M_assess_editor.prototype.remove_assess = function(obj)
{
    var _self = this;
    obj = $(obj);

    _self.edit_obj.find('[mtype="p"]').val('');
    _self.edit_obj.find('[mtype="f"]').val('');
    _self.edit_obj.find('[mtype="mg"]').val('');
    _self.edit_obj.find('input').prop('disabled',true);

    var assess_obj = obj.parent();
    assess_obj.remove();
}

//编辑后保存
M_assess_editor.prototype.save_assess = function(obj)
{
    var _self = this;
    obj = $(obj);
    if(0 == obj.length)console.log('M_assess_editor.save_assess obj error');
    var post_data = {};
    post_data.p = _self.edit_obj.find('[mtype=p]').val();
    post_data.f = _self.edit_obj.find('[mtype=f]').val();
    post_data.mg = _self.edit_obj.find('[mtype=mg]').val();
    var post_data_str = JSON.stringify(post_data);
    obj.find('[mtd="p"]').html(post_data.p + '&nbsp;');
    obj.find('[mtd="f"]').html(post_data.f);
    obj.find('[mtd="mg"]').html(post_data.mg);

    //提交时的数组结构
    obj.find('[mtype=assess_data]').val(post_data_str);
}

//选中后编辑
M_assess_editor.prototype.select_assess = function(obj)
{
    var _self = this;
    obj = $(obj);
    if(0 == obj.length)console.log('M_assess_editor.select_assess obj error');
    var post_data_str = obj.find('[mtype=assess_data]').val();
    var post_data = {};
    if(post_data_str)
    {
        post_data = JSON.parse(post_data_str);
    }

    _self.edit_obj.find('[mtype="p"]').val(post_data.p);
    _self.edit_obj.find('[mtype="f"]').val(post_data.f);
    _self.edit_obj.find('[mtype="mg"]').val(post_data.mg);

    _self.edit_obj.find('input').prop('disabled',false);

    //编辑保存事件
    //on.click
    //_self.edit_obj.find('a').off('click').on('click',function(){_self.save_assess(obj)});
    //on.change
    _self.edit_obj.find('[mtype=p],[mtype=f],[mtype=mg]'
        ).off('change keyup').on('change keyup',function(){_self.save_assess(obj)});
    //button active
    _self.out_obj.find('.info').removeClass('info');
    obj.addClass('info');
}
