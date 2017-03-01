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
    Home Comment Index
*/
'use strict';

function M_comment_editor(config)
{
    if('object' != typeof(config))
    {
        console.log('config no exists');
        return;
    }
    var _self = this;
    $.extend(_self,config);
    //检查初始化元素
    if(0 == _self.main_obj.length)console.log('main_obj no exists');
    if(0 == _self.ajax_url.length)console.log('ajax_url no exists');
    if (0 == _self.route.length) console.log('route no exists');
    if(0 == _self.item.length)console.log('item no exists');
    if(0 == _self.main_obj.length
    || 0 == _self.ajax_url.length
        || 0 == _self.route.length
    || 0 == _self.item)return;
    _self.initialize();
}

M_comment_editor.prototype = 
{
    'main_obj':'',
    'ajax_url':'',
    'route': '',
    'put_lock':false,
    'get_lock':false,
    'item':''
}

//初始化 提交表单
M_comment_editor.prototype.initialize = function()
{
    var _self = this;
    
    _self.get_data();
}

//推送评论的数据
M_comment_editor.prototype.put_data = function(form_obj)
{
    var _self = this;
    if(_self.put_lock)return;
    _self.put_lock = true;
    var form_obj = $(form_obj);
    var level = form_obj.find('[name=comment_level]:checked').val();
    var content_obj =  form_obj.find('[name=comment_content]');
    var print_info_obj =  form_obj.find('span');
    print_info_obj.html('');
    var content =  content_obj.val();
    content_obj.prop('disabled',true);
    content_obj.val(lang.common.send + lang.common.comment + '...');
    
    var data = {
        'type':'get_data',
        'field':'put_data',
        'data':{
            'route': _self.route,
            'item':_self.item,
            'level':level,
            'content':content
        }
    }
    
    $.ajax({
        'url':_self.ajax_url,
        'data':data,
        'type':'POST',
        'dataType':'JSON',
        'cache':false,
        'success':function(data)
        {
            _self.put_lock = false;
            content_obj.prop('disabled',false);
            content_obj.val('');
            if(data && data.status)
            {
                print_info_obj.css('color','green');
                _self.get_data();
            }
            else
            {
                content_obj.val(content);
                print_info_obj.css('color','red');
            }
            print_info_obj.html(data.info);
        },
        'error':function()
        {
            console.log('M_comment_editor.put_data ajax error');
        }
    });
    return false;
}


//获取评论的数据和分页 html
M_comment_editor.prototype.get_data = function(p)
{
    var _self = this;
    if(_self.get_lock)return;
    _self.get_lock = true;
    
    var data = {
        'type':'get_data',
        'field':'get_data',
        'data': {'route': _self.route, 'item': _self.item},
        'p':p
    }
    
    $.ajax({
        'url':_self.ajax_url,
        'data':data,
        'type': 'POST',
        'dataType':'HTML',
        'cache':false,
        'success':function(data)
        {
            _self.get_lock = false;
            var result = $(data);
            var form_str = 'form';
            var page_str = '.num,.prev,.next';
            var form_obj = result.find(form_str);
            form_obj.attr('action','');
            form_obj.attr('onSubmit','return false');
            var page_obj = result.find(page_str);
            page_obj.each(function(k,v){
                $(this).attr('p',$(this).attr('href').match(/p\/(\d*)/)[1]);
            });
            page_obj.attr('href','javascript:void(0);');
            
            _self.main_obj.html(result.html());
            _self.main_obj.find(form_str).on('submit',function(){
                    var p = parseInt($(this).find('[name=p]').val());
                    if('' != p)_self.get_data(p);
                });
            _self.main_obj.find(page_str).on('click',function(){
                    var p = parseInt($(this).attr('p'));
                    if('' != p)_self.get_data(p);
                });
        },
        'error':function()
        {
            console.log('M_comment_editor.get_data ajax error');
        }
    });
}
