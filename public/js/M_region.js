/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_region Class Javascript
    Include : jQuery
    Included List(Module Controller Action)
*/
'use strict';

//从select元素中增加数据
function M_region(config)
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
    if(0 == _self.post_name.length)console.log('post_name no exists');
    if(0 == _self.ajax_url.length)console.log('ajax_url no exists');
    if(0 == _self.out_obj.length
    || 0 == _self.post_name.length
    || 0 == _self.ajax_url.length)return;
    //初始化元素
    _self.initialize();
}

M_region.prototype = {
    'out_obj':'',
    'edit_obj':'',
    'post_name':'',
    'ajax_url':''
}

//初始化
M_region.prototype.initialize = function()
{
    var _self = this;
    
    var button = _self.out_obj.find(_self.edit_obj);
    if(0 < button.length)
    {
        button.on('click',function(){
            _self.out_obj.html('');
            _self.initialize_select();
        });
    }
    else
    {
        _self.initialize_select();
    }
}

//初始化 选择框
M_region.prototype.initialize_select = function()
{
    var _self = this;
    var cb_fn = function(data)
    {
        //新建地址回调函数
        if(!data || 1 > data.length)return;
        var select_obj = $('<select name="' + _self.post_name + '[]"></select>');
        select_obj.append('<option>' + $Think.lang.please + $Think.lang.selection + '</option>');
        $.each(data,function(k,v){
            var option_obj = $('<option></option>');
            option_obj.attr('select_id',v.id);
            option_obj.attr('value',v.region_name);
            option_obj.html(v.region_name);
            select_obj.append(option_obj);
        });
        select_obj.on('change',function(){
            //删除下级地址
            var current_obj = $(this);
            current_obj.nextAll().remove();
            //新建地址
            var id = current_obj.find(':selected').attr('select_id');
            _self.get_data(id,cb_fn);
        });
        _self.out_obj.append(select_obj);
    }
    _self.get_data(0,cb_fn);
}

M_region.prototype.get_data = function(id,cb_fn)
{
    var _self = this;
    var post_data = {
        'type':'get_data',
        'field':'parent_id',
        'data':{'id':id}
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
                cb_fn(data.info);
            }
        },
        'error':function()
        {
            console.log('M_region ajax error' + _self.ajax_url);
        }
    });
}