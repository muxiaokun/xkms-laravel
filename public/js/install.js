/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
   Install Module Javascript
*/
'use strict';

function show_install_message(obj_str,msg,type)
{
    switch(type)
    {
        case 'success':type='success';break;
        case 'warning':type='warning';break;
        case 'danger':type='danger';break;
        default :type='info';
    }
    $(obj_str).prepend('<div class="alert alert-'+type+'">'+msg+'</div>');
}

function check_checkBoxVal(obj_str,msg)
{
    if($(obj_str+':checked').val())
    {
        return true;
    }
    else
    {
        alert(msg);
        return false;
    }
}

function allselect(obj_area,obj)
{
    var objs = $(obj_area);
    objs.prop('checked',$(obj).prop('checked'));
}

function M_check_mysql(config)
{
    if('object' != typeof(config))
    {
        console.log('M_check_mysql config no exists');
        return;
    }
    
    var _self = M_check_mysql.prototype
    _self.edit_obj = $(config.edit_obj);
    _self.out_obj = $(config.out_obj);
    _self.next_link = config.next_link;
    _self.ajax_url = config.ajax_url;
    
    if(0 == _self.out_obj.length)console.log('out_obj no exists');
    if(0 == _self.edit_obj.length)console.log('edit_obj no exists');
    if(0 == _self.next_link.length)console.log('next_link no exists');
    if(0 == _self.ajax_url.length)console.log('ajax_url no exists');
    if(0 == _self.out_obj.length 
    || 0 == _self.edit_obj.length 
    || 0 == _self.next_link.length
    || 0 == _self.ajax_url.length)return;
    
    _self.out_obj.on('click',function(){_self.check()});
}

M_check_mysql.prototype = {
    'edit_obj':'',
    'out_obj':'',
    'next_link':'',
    'ajax_url':'',
    'running':0
}

M_check_mysql.prototype.check = function(){
    var _self = M_check_mysql.prototype
    if(_self.running)
    {
        show_install_message('#show_box','ajax running!','danger');
        return false;
    }
    _self.running = 1;
    var db_host = _self.edit_obj.find('input[name=db_host]').val();
    var db_port = _self.edit_obj.find('input[name=db_port]').val();
    var db_database = _self.edit_obj.find('input[name=db_database]').val();
    var db_username = _self.edit_obj.find('input[name=db_username]').val();
    var db_password = _self.edit_obj.find('input[name=db_password]').val();
    var db_prefix = _self.edit_obj.find('input[name=db_prefix]').val();
    var post_value = {
        'type':'get_data',
        'field':'check_mysql',
        'data':{
            'db_host':db_host,
            'db_port':db_port,
            'db_database':db_database,
            'db_username':db_username,
            'db_password':db_password,
            'db_prefix':db_prefix
        }
    };
    $.ajax({
        'url':_self.ajax_url,
        'type':'POST',
        'dataType':'json',
        'data':post_value,
        'cache':false,
        'success':function(data){
            if(!data || !data.info)return;
            switch(data.info.type)
            {
                case 1:
                    show_install_message('#show_box',data.info.msg,'danger');
                    break;
                case 2:
                    var btn_str = Array('<a class="btn btn-info ml50" href="' + _self.next_link + '" >',
                        data.info.msg + '</a>'
                        ).join('');
                    show_install_message('#show_box', btn_str, 'info');
                    break;
                case 3:
                    show_install_message('#show_box',data.info.msg,'success');
                    setTimeout('window.location.href="'+_self.next_link+'"',3000);
                    break;
            }
            _self.running = 0;
        },
        'error':function(){
            window.console.log('connect error! install.js M_check_mysql')
            _self.running=0;
        }
    });
    return false;
}