/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_quests_editor Class Javascript
    Include : jQuery jQueryUi.sortable()
    Included List(Module Controller Action)
    Admin Quest add,edit
*/
'use strict';

function M_quest_editor(config)
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

M_quest_editor.prototype = 
{
    'out_obj':'',
    'edit_obj':'',
    'def_data':'',
    'post_name':'ext_info',
    'a_obj':Array(
        '<a class="list-group-item list-group-default mb5" style="min-height:42px;"><span mtype="question_name"></span>',
        '<span class="glyphicon glyphicon-remove fr" mtype="close" style="cursor:pointer;"></span>',
        '<input type="hidden" mtype="question_data"></a>'
        ).join('')
}

M_quest_editor.prototype.initialize = function()
{
    var _self = this;
    
    _self.edit_obj.find('[mtype=question]').prop('disabled',true);
    _self.edit_obj.find('[mtype=explains]').prop('disabled',true);
    _self.edit_obj.find('[mtype=answer]').prop('disabled',true);
    _self.edit_obj.find('[mtype=required]').prop('disabled',true);
    _self.edit_obj.find('[name=answer_type]').prop('disabled',true);

    //初始化editor add button
    var add_question_obj = _self.edit_obj.find('[mtype=add_question]');
    add_question_obj.on('click',function(){_self.add_question()});
    _self.out_obj.sortable();

    if(_self.def_data)
    {
        _self.initialize_data(_self.def_data);
    }
}

M_quest_editor.prototype.initialize_data = function(data)
{
    var _self = this;
    
    $.each(data,function(k,v){
        var post_data = v;

        //初始化 a_obj 问题
        var a_obj = $(_self.a_obj);
        var question_title = Array(
                post_data.question + '&nbsp;',
            '[' + lang.common.answer + lang.common.type + ':' + post_data.answer_type + ']',
            '[' + lang.common.yes + lang.common.no + lang.common.required + ':' + post_data.required + ']'
            ).join('');
        a_obj.find('[mtype="question_name"]').html(question_title);
        a_obj.on('click',function(){_self.select_question(this)});
        a_obj.find('[mtype="close"]').on('click',function(){_self.remove_question(this)});

        //初始化 Input
        var post_data_str = JSON.stringify(post_data);
        var input_hidden_obj = a_obj.find('[mtype=question_data]');
        input_hidden_obj.val(post_data_str);
        input_hidden_obj.attr('name',_self.post_name + '[]');

        _self.out_obj.append(a_obj);
    });
}

//添加新的问题
M_quest_editor.prototype.add_question = function()
{
    var _self = this;
    
    //初始化 a_obj 问题
    var a_obj = $(_self.a_obj);
    a_obj.find('[mtype="question"]').html(lang.common.click + lang.common.edit);
    a_obj.find('[mtype=question_data]').attr('name',_self.post_name + '[]');
    a_obj.on('click',function(){_self.select_question(this)});
    a_obj.find('[mtype="close"]').on('click',function(){_self.remove_question(this)});
    //插入 Element
    _self.out_obj.append(a_obj);
    //添加后默认点击一下
    a_obj.click();
}

//删除问题条目
M_quest_editor.prototype.remove_question = function(obj)
{
    var _self = this;
    obj = $(obj);

    _self.edit_obj.find('[mtype=question]').val('').prop('disabled',true);
    _self.edit_obj.find('[mtype=explains]').val('').prop('disabled',true);
    _self.edit_obj.find('[mtype=answer]').val('').prop('disabled',true);
    _self.edit_obj.find('[mtype=required]').prop('checked',false).prop('disabled',true);
    _self.edit_obj.find('[name=answer_type]:checked').prop('checked',false);
    _self.edit_obj.find('[name=answer_type]:first').prop('checked',true);
    _self.edit_obj.find('[name=answer_type]').prop('disabled',true);

    var question_obj = obj.parent();
    question_obj.remove();
}

//编辑后保存
M_quest_editor.prototype.save_question = function(obj)
{
    var _self = this;
    obj = $(obj);
    var post_data = {};
    post_data.question = _self.edit_obj.find('[mtype=question]').val();
    post_data.explains = _self.edit_obj.find('[mtype=explains]').val();
    post_data.answer = _self.edit_obj.find('[mtype=answer]').val();
    post_data.required = _self.edit_obj.find('[mtype=required]').prop('checked');
    post_data.answer_type = _self.edit_obj.find('[name=answer_type]:checked').val();
    var post_data_str = JSON.stringify(post_data);
    var question_title = Array(
            post_data.question + '&nbsp;',
        '[' + lang.common.answer + lang.common.type + ':' + post_data.answer_type + ']',
        '[' + lang.common.yes + lang.common.no + lang.common.required + ':' + post_data.required + ']'
        ).join('');
    obj.find('[mtype=question_name]').html(question_title);
    //提交时的数组结构
    obj.find('[mtype=question_data]').val(post_data_str);
}

//选中后编辑
M_quest_editor.prototype.select_question = function(obj)
{
    var _self = this;
    obj = $(obj);
    var post_data_str = obj.find('[mtype=question_data]').val();
    var post_data = {};
    if(post_data_str)
    {
        post_data = JSON.parse(post_data_str);
    }
    _self.edit_obj.find('[mtype=question]').val(post_data.question).prop('disabled',false);
    _self.edit_obj.find('[mtype=explains]').val(post_data.explains).prop('disabled',false);
    _self.edit_obj.find('[mtype=answer]').val(post_data.answer).prop('disabled',false);
    _self.edit_obj.find('[mtype=required]').prop('checked',post_data.required).prop('disabled',false);
    if(post_data.answer_type)
    {
        _self.edit_obj.find('[name=answer_type]:checked').prop('checked',false);
        _self.edit_obj.find('[name=answer_type][value=' + post_data.answer_type + ']').prop('checked',true).prop('disabled',false);

    }
    _self.edit_obj.find('[name=answer_type]').prop('disabled',false);

    //编辑保存事件
    //on.click
    //_self.edit_obj.find('a').off('click').on('click',function(){_self.save_question(obj)});
    //on.change
    _self.edit_obj.find('[mtype=question],[mtype=explains],[mtype=answer],[mtype=required],[name=answer_type]'
        ).off('change keyup').on('change keyup',function(){_self.save_question(obj)});

    //button active
    _self.out_obj.find('.active').removeClass('active');
    obj.addClass('active');
}