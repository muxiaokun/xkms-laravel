/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_history Class Javascript
    Include : jQuery
    Include : jQuery UI $.cookie
    Included List(Module Controller Action)
    Admin Index left
*/
'use strict';

function M_history(config)
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
    if(0 == _self.out_obj.length
    )return;
    _self.initialize();
}

M_history.prototype = 
{
    'out_obj':'',
    'menu_obj':'',
    'data':{},
    'cookie_name':'M_history',
    'type':'cookie' //cookie = $.cookie | '' = _self.data
}

M_history.prototype.initialize = function()
{
    var _self = this;
    if(_self.type == 'cookie' && !$.cookie)_self.type = '';
    _self.menu_obj = $('<ul class="nav text-center" role="tablist"></ul>');
    _self.out_obj.prepend(_self.menu_obj);
    _self.out_obj.prepend('<h3>' + lang.common.history + lang.common.handle + '</h3>');
    _self.out_obj.find('a').each(function(k,v){
        var a_obj = $(v);
        if (!a_obj.attr('href').match(/javascript:/)) {
            a_obj.on('click', function () {
                _self.a_click(a_obj)
            });
        }
    });
    _self.initialize_data();
    _self.out_obj.accordion({
        heightStyle: "content"
    });
}

M_history.prototype.initialize_data = function()
{
    var _self = this;
    var history = _self.get_history();
    if(history)
    {
        _self.menu_obj.html('');
        $.each(history,function(k,v){
            _self.menu_obj.append('<li role="presentation">' + v['outerHTML'] + '</li>');
        });
    }
    _self.out_obj.accordion({
        heightStyle: "content"
    }); 
}


//点击连接事件
M_history.prototype.a_click = function(a_obj)
{
    var _self = this;
    var outerHTML = a_obj.prop('outerHTML');
    var old_history = _self.get_history();
    var new_history = {};
    if(old_history)
    {
        var add_index = 0;
        var do_add = true;
        $.each(old_history,function(k,v){
            if(outerHTML == v['outerHTML'])
            {
                old_history[k]['hits'] += 2;
                do_add = false;
            }
            add_index = k + 1;
        });
        if(do_add)
        {
            old_history[add_index] = {
            'hits':2,
            'outerHTML':outerHTML,
            };
        }

        //排序
        var remove_index = 0;
        var hist = 0;
        for(var k = 1;k < 6;k++)
        {
            remove_index = 0;
            new_history[k] = {
              'hits':0,
              'outerHTML':'',
            };
            $.each(old_history,function(kk,v){
                if(new_history[k]['hits'] < v['hits'])
                {
                    remove_index = kk;
                    hist = v['hits'];
                    new_history[k]['hits'] = 6 - k;
                    new_history[k]['outerHTML'] = v['outerHTML'];
                }
            });
            if(remove_index)old_history[remove_index]['hits'] = -1;
        }
    }
    else
    {
        new_history[0] = {
            'hits':2,
            'outerHTML':outerHTML,
        };
    }
    _self.put_history(new_history);
    _self.initialize_data();
}


//读取历史数据
M_history.prototype.get_history = function()
{
    var _self = this;
    var data = {};
    switch(_self.type)
    {
        case 'cookie':
            var cookie_data = $.cookie(_self.cookie_name);
            if(cookie_data)
            {
                data = JSON.parse(cookie_data);
            }
        break;
        default:
            data = _self.data;
    }
    return data;
}

//存入历史数据
M_history.prototype.put_history = function(data)
{
    var _self = this;
    switch(_self.type)
    {
        case 'cookie':
            $.cookie(_self.cookie_name,JSON.stringify(data));
        break;
        default:
            _self.data = data;
    }
    return true;
}