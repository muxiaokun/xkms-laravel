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
    Include : jQuery jQueryUi.dialog()
    Included List(Module Controller Action)
    Admin AdminLog index
    Admin Comment index
    Admin Message index
    Admin RecruitLog index
    Home Message index
*/
'use strict';

function M_alert_log(config)
{
    if('object' != typeof(config))
    {
        console.log('config no exists');
        return;
    }
    var _self = this;
    $.extend(_self,config);
    var msg_str = '';
    if(typeof(_self.message) == 'object')
    {
        msg_str = '<table class="table table-hover">';
        for (var name in _self.message)
        {
            msg_str += "<tr><td>" + name + "</td><td>" + _self.message[name] + "</td></tr>";
        }
        msg_str += '</table>';
    }
    else
    {
        msg_str = _self.message;
    }
    _self.bind_obj.on('click',function(){
        if($.isFunction(_self.cb_fn))_self.cb_fn();
        var obj = $('#M_alert_log');
        if(!obj.length)
        {
            obj = $(Array('<div id="M_alert_log" title="' + _self.title + '" >' + msg_str + '</div>'
                ).join('')
            );
        }
        else
        {
            obj.html(msg_str);
        }
        var buttons_fn = {};
        buttons_fn[lang.common.close] = function () {
            $( this ).dialog( "close" );
        }
        obj.dialog({
            resizable: false,
            height:300,
            width:500,
            buttons: buttons_fn
        });
    });
}

M_alert_log.prototype =
{
    'cb_fn_isrun':false
}

function M_alert_log_Message(obj,id,url)
{
    return function()
    {
        var a_obj = $(obj);
        var is_run = a_obj.data('is_run');
        if(is_run)return;
        a_obj.data('is_run',true);
        var data = {'type':'get_data','field':'read_message','data':{'id':id}};
        $.ajax({
            'url':url,
            'type':'POST',
            'dataType':'JSON',
            'data':data,
            'cache':true,
            'success':function(data){
                if(data && data.status)
                {
                    var change_obj = $(a_obj.parents('tr').find('td').get(2));
                    M_test_val = a_obj;
                    change_obj.html(change_obj.html().replace(/.*?\]/,data.info + ']'));
                }
            },
            'error':function()
            {
                console.log('M_alert_log_Message ajax error');
            }
        });
    }
}