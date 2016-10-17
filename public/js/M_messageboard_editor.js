/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_messageboard_editor Class Javascript
    Include : jQuery jQueryUi.sortable()
    Included List(Module Controller Action)
    Admin MessageBoard add,edit
*/
'use strict';

function M_messageboard_editor(config)
{
    if('object' != typeof(config))
    {
        console.log('config no exists');
        return;
    }
    var _self = this;
    $.extend(_self,config);
    //检查初始化元素 SYS_MESSAGE_CONFIG
    if(0 == _self.out_obj.length)console.log('out_obj no exists');
    if(0 == _self.edit_obj.length)console.log('edit_obj no exists');
    if(0 == _self.btn_obj.length)console.log('btn_obj no exists');
    if(0 == _self.post_name.length)console.log('post_name no exists');
    if(0 == _self.out_obj.length
    || 0 == _self.edit_obj.length
    || 0 == _self.btn_obj.length
    || 0 == _self.post_name.length
    )return;
    _self.initialize();
}

M_messageboard_editor.prototype = 
{
    'out_obj':'',
    'edit_obj':'',
    'btn_obj':'',
    'post_name':'',
    'def_data':'',
}

M_messageboard_editor.prototype.initialize = function()
{
    var _self = this;
    
    //init add_button
    var add_button_btn = $('<a class="btn btn-xs btn-success" href="javascript:void(0);">' +
        lang.add + lang.option + '</a>');
    add_button_btn.on('click',function(){_self.add_msg()});
    _self.btn_obj.append(add_button_btn);
    
    //init default data
    if(_self.def_data)
    {
        $.each(_self.def_data,function(k,v){
            _self.add_msg(v);
        });
    }
    _self.save_msg();
}

//添加新的导航
M_messageboard_editor.prototype.add_msg = function(data)
{
    var _self = this;
    //init edit_obj
    var new_option = _self.edit_obj.clone().attr('id','');
    //init data
    if(data)
    {
        var ms_option = (data.msg_option)?':' + data.msg_option.join(','):'';
        new_option.find('input[mtype="msg_name"]').val(data.msg_name+ms_option);
        new_option.find('select[mtype="msg_type"] > option[value="' + data.msg_type + '"]').prop('selected',true);
        new_option.find('input[mtype="msg_required"]').prop('checked',data.msg_required);
        new_option.find('input[mtype="msg_length"]').val(data.msg_length);
        new_option.find('a.btn-danger').on('click',function(){
            new_option.fadeOut('',function(){
                new_option.remove()
            });
        });
    }
    new_option.find('input,select').on('change click',function(){_self.save_msg()});
    _self.out_obj.append(new_option);
    new_option.fadeIn();
    (_self.out_obj.find('tbody'))?_self.out_obj.find('tbody').sortable():_self.out_obj.sortable();
}

//编辑后保存
M_messageboard_editor.prototype.save_msg = function()
{
    var _self = this;
    var post_data = {};
    var post_obj = _self.out_obj.find('input[type=hidden][name="' + _self.post_name + '"]');
    if(0 == post_obj.length)
    {
        post_obj = $('<input type="hidden" name="' + _self.post_name + '" />');
        _self.out_obj.append(post_obj);
    }
    var data_source = (_self.out_obj.find('tbody'))?_self.out_obj.find('tbody').children():_self.out_obj.children();
    $.each(data_source,function(k,v){
        var option = $(v);
        var msg_name = option.find('input[mtype="msg_name"]').val();
        var msg_name_arr = msg_name.split(':');
        if(2 == msg_name_arr.length)
        {
            msg_name = msg_name_arr[0];
            post_data[msg_name] = {};
            post_data[msg_name]['msg_option'] = msg_name_arr[1].split(',');
        }
        else
        {
            post_data[msg_name] = {};    
        }
        post_data[msg_name]['msg_name'] = msg_name;
        post_data[msg_name]['msg_type'] = option.find('select[mtype="msg_type"]').val();
        post_data[msg_name]['msg_required'] = option.find('input[mtype="msg_required"]').prop('checked');
        post_data[msg_name]['msg_length'] = option.find('input[mtype="msg_length"]').val();
    });
    post_obj.val(JSON.stringify(post_data));
}