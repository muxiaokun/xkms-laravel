/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_fontsize Class Javascript
    Include : jQuery
    Included List(Module Controller Action)
    EVERYWHERE
*/
'use strict';

function M_fontsize(config)
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
    if(0 == _self.big_obj.length)console.log('big_obj no exists');
    if(0 == _self.small_obj.length)console.log('small_obj no exists');
    if(0 == _self.out_obj.length
    || 0 == _self.big_obj.length
    || 0 == _self.small_obj.length
    )return;
    _self.initialize();
}

M_fontsize.prototype = 
{
    'out_obj':'',
    'big_obj':'',
    'small_obj':'',
    'setp':2
}

M_fontsize.prototype.initialize = function()
{
    var _self = this;
    _self.big_obj.on('click',function(){
        _self.tobtos(1);
    });
    _self.small_obj.on('click',function(){
        _self.tobtos(-1);
    });
}

M_fontsize.prototype.tobtos = function(tobtos)
{
    var _self = this;
    _self.out_obj.find('*').each(function(k,v){
        var obj = $(v);
        var font_size = parseInt(obj.css('font-size'));
        if(0 < font_size)
        {
            obj.css('font-size',(font_size + _self.setp * tobtos) + 'px');
        }
    });
}