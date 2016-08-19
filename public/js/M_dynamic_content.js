/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_dynamic_content Class Javascript
    Include : jQuery
*/
'use strict';

function M_dynamic_content(config)
{
    var _self = this;
    if('object' != typeof(config))
    {
        console.log('M_dynamic_content config no exists('+_self.main_obj.selector+')');
        return;
    }
    //输出的外围元素
    _self.main_obj = $(config.main);
    _self.mask_obj = _self.main_obj.find(config.mask);
    _self.run_obj = _self.main_obj.find(config.run);
    
    if(0 == _self.main_obj.length)console.log('M_dynamic_content main_obj no exists('+_self.main_obj.selector+')');
    if(0 == _self.mask_obj.length)console.log('M_dynamic_content mask_obj no exists('+_self.main_obj.selector+')');
    if(0 == _self.run_obj.length)console.log('M_dynamic_content run_obj no exists('+_self.main_obj.selector+')');
    if(0 == _self.main_obj.length
    || 0 == _self.mask_obj.length
    || 0 == _self.run_obj.length
    )return;
    
    if(config.previous)_self.previous_obj = _self.main_obj.find(config.previous);
    if(config.next)_self.next_obj = _self.main_obj.find(config.next);
    if(config.index)_self.index_obj = _self.main_obj.find(config.index);
    if(config.run_type)_self.run_type = config.run_type;
    if(config.active_css)_self.active_css = config.active_css;
    if(-1 == config.direction || 1 == config.direction)_self.direction = config.direction;
    
    //检查初始化元素
    _self.initialize();
}

M_dynamic_content.prototype = {
    'main_obj':'',
    'mask_obj':'',
    'run_obj':'',
    'previous_obj':'',
    'next_obj':'',
    'index_obj':'',
    'run_type':'lr',// lr tb io lr_as tb_as
    'active_css':'active',
    'run_time':5000,
    'direction':1,
    'all_width':0,
    'all_height':0,
    'mask_width':0,
    'mask_height':0,
    'child_width':0,
    'child_height':0,
    'lock':false,
    'run_fns':{},
    'run_fn':'',
    'run_init_fn':'',
    'auto_run_fn':'',
    'interval_obj':''
}

M_dynamic_content.prototype.initialize = function()
{
    var _self = this;
    //初始化动画方法 开始
    if('' == _self.run_type
    || 'function' != typeof(_self.run_fns[_self.run_type])
    ||'function' != typeof(_self.run_fns[_self.run_type + '_init'])
    )
    {
        console.log('M_dynamic_content run_fns error('+_self.main_obj.selector+')');
        return;
    }
    _self.run_fn = _self.run_fns[_self.run_type];
    _self.run_init_fn = _self.run_fns[_self.run_type + '_init'];
    var run_css = {
        'position':'relative',
        'left':'0px',
        'top':'0px'
    }
    _self.run_obj.css(run_css);
    _self.run_obj.data('index',0);
    _self.mask_width = _self.mask_obj.width();
    _self.mask_height = _self.mask_obj.height();
    if(!_self.run_init_fn(_self))
    {
        console.log('M_dynamic_content init error('+_self.main_obj.selector+')');
        return;
    }
    //初始化动画方法 结束
    //初始化按钮时间 开始
    if(0 < _self.previous_obj.length)_self.previous_obj.on('click',_self.button(_self.direction * -1));
    if(0 < _self.next_obj.length)_self.next_obj.on('click',_self.button(_self.direction));
    if(0 < _self.index_obj.length)
    {
        _self.index_obj.children().each(function(k,v){
            $(v).on('click',_self.button(_self.direction,$(this).index() + 1));
        });
    }
    //初始化按钮时间 结束
    _self.auto_run_fn = _self.button(_self.direction);
    _self.interval_obj = setTimeout(_self.auto_run_fn,_self.run_time);
    return;
}

M_dynamic_content.prototype.button = function(direction,index)
{
    var _self = this;
    return function()
    {
        if(_self.lock)return;
        _self.lock = true;
        clearInterval(_self.interval_obj);
        _self.run_fn(_self,direction,index)();
        _self.interval_obj = setInterval(_self.auto_run_fn,_self.run_time);
    }
}

//左右走类型
M_dynamic_content.prototype.run_fns = {
    //Left Right
    'lr':function(_self,direction,index)
        {
            return function()
            {
                var children_length = _self.run_obj.children().length / 2 - 1;
                var current_index = _self.run_obj.data('index');
                var next_key = (index)?index - 1 : current_index + 1 * direction;
                
                var target_left = _self.child_width * next_key * direction;
                if(0 < target_left)target_left = target_left * -1;
                var run_range = _self.all_width / 2 * -1;
                //run_obj <- target_left < run_range
                //run_obj -> 0 < target_left
                if(next_key > children_length)
                {
                    _self.run_obj.animate({'left':target_left},1000,'',function(){
                        _self.run_obj.css('left',0+'px');
                        _self.lock = false;
                    });
                }else if(next_key < 0)
                {
                    _self.run_obj.css('left',run_range+'px');
                    _self.run_obj.animate({'left':run_range + _self.child_width},1000,'',function(){
                        _self.lock = false;
                    });
                }
                else
                {
                    _self.run_obj.animate({'left':target_left},1000,'',function(){
                        _self.lock = false;
                    });
                }
                
                if(next_key > children_length)next_key = 0;
                if(next_key < 0)next_key = children_length;
                _self.run_obj.data('index',next_key);
                if(0 < _self.index_obj.length)
                {
                    var index_children = $(_self.index_obj.children().get(next_key));
                    index_children.addClass(_self.active_css).siblings().removeClass(_self.active_css);
                }
            }
        },
    'lr_init':function(_self)
    {
        var children_obj = _self.run_obj.children();
        if(0 == children_obj.length)
        {
            console.log('M_dynamic_content children_obj no exists('+_self.main_obj.selector+')');
            return false;
        }
        _self.child_width = $(children_obj[0]).width();
        _self.child_height = $(children_obj[0]).width();
        children_obj.each(function(k,v){
            _self.run_obj.append($(v).clone());
        });
        _self.all_width = _self.child_width * children_obj.length * 2;
        if(_self.mask_width > _self.all_width/2)
        {
            console.log('M_dynamic_content mask_width > all_width('+_self.main_obj.selector+')');
            return false;
        }
        _self.run_obj.css('width',_self.all_width + 'px');
        return true;
    },
    
    //Top Buttom
    'tb':function(_self,direction,index)
        {
            return function()
            {
                
                var children_length = _self.run_obj.children().length / 2 - 1;
                var current_index = _self.run_obj.data('index');
                var next_key = (index)?index - 1 : current_index + 1 * direction;
                
                var target_top = _self.child_height * next_key * direction;
                if(0 < target_top)target_top = target_top * -1;
                var run_range = _self.all_height / 2 * -1;
                //run_obj <- target_top < run_range
                //run_obj -> 0 < target_top
                if(next_key > children_length)
                {
                    _self.run_obj.animate({'top':target_top},1000,'',function(){
                        _self.run_obj.css('top',0+'px');
                        _self.lock = false;
                    });
                }else if(next_key < 0)
                {
                    _self.run_obj.css('top',run_range+'px');
                    _self.run_obj.animate({'top':run_range + _self.child_height},1000,'',function(){
                        _self.lock = false;
                    });
                }
                else
                {
                    _self.run_obj.animate({'top':target_top},1000,'',function(){
                        _self.lock = false;
                    });
                }
                
                if(next_key > children_length)next_key = 0;
                if(next_key < 0)next_key = children_length;
                _self.run_obj.data('index',next_key);
                if(0 < _self.index_obj.length)
                {
                    var index_children = $(_self.index_obj.children().get(next_key));
                    index_children.addClass(_self.active_css).siblings().removeClass(_self.active_css);
                }
            }
        },
    'tb_init':function(_self)
    {
        var children_obj = _self.run_obj.children();
        if(0 == children_obj.length)
        {
            console.log('M_dynamic_content children_obj no exists('+_self.main_obj.selector+')');
            return false;
        }
        _self.child_height = $(children_obj[0]).height();
        _self.child_height = $(children_obj[0]).height();
        children_obj.each(function(k,v){
            _self.run_obj.append($(v).clone());
        });
        _self.all_height = _self.child_height * children_obj.length * 2;
        if(_self.mask_height > _self.all_height/2)
        {
            console.log('M_dynamic_content mask_height > all_height('+_self.main_obj.selector+')');
            return false;
        }
        _self.run_obj.css('height',_self.all_height + 'px');
        return true;
    },
    
    //fadeIn fadeOut
    'io':function(_self,direction,index)
        {
            return function()
            {
                var children_obj = _self.run_obj.children();
                var current_index = _self.run_obj.data('index');
                var next_key = (index)?index-1:current_index + 1 * direction;
                if(next_key > children_obj.length - 1)next_key = 0;
                if(next_key < 0)next_key = children_obj.length - 1;
                _self.run_obj.data('index',next_key);
                if(0 < _self.index_obj.length)
                {
                    var index_children = $(_self.index_obj.children().get(next_key));
                    index_children.addClass(_self.active_css).siblings().removeClass(_self.active_css);
                }
                if(current_index == next_key)
                {
                    _self.lock = false;
                    return;
                }
                $.when(
                    $(children_obj.get(current_index)).fadeOut(1000),
                    $(children_obj.get(next_key)).fadeIn(1000)
                ).done(function(){
                    _self.lock = false;
                });
            }
        },
    'io_init':function(_self)
    {
        var children_obj = _self.run_obj.children();
        if(2 > children_obj.length)
        {
            console.log('M_dynamic_content2 > children_obj.length('+_self.main_obj.selector+')');
            return false;
        }
        _self.run_obj.css('width',_self.mask_width + 'px');
        _self.run_obj.css('height',_self.mask_height + 'px');
        var children_css = {
            'position':'absolute',
            'top':'0px',
            'left':'0px'
        }
        children_obj.each(function(k,v){
            var children = $(v);
            children.css(children_css);
            if(0 == k)
            {
                children_css.display = 'none';
            }
        });
        return true;
    },
    
    //Left Right Auto Stop
    'lr_as':function(_self,direction,index)
        {
            return function()
            {
                var target_left = parseInt(_self.run_obj.css('left')) + direction * -1;
                if(_self.all_width/2 * direction * -1 > target_left)target_left = 0;
                _self.run_obj.css({'left':target_left});
                _self.lock = false;
            }
        },
    'lr_as_init':function(_self)
    {
        var children_obj = _self.run_obj.children();
        if(0 == children_obj.length)
        {
            console.log('M_dynamic_content children_obj no exists('+_self.main_obj.selector+')');
            return false;
        }
        _self.child_width = $(children_obj[0]).width();
        _self.child_height = $(children_obj[0]).height();
        children_obj.each(function(k,v){
            _self.run_obj.append($(v).clone());
        });
        _self.all_width = _self.child_width * children_obj.length * 2;
        if(_self.mask_width > _self.all_width)
        {
            console.log('M_dynamic_content mask_width > all_width('+_self.main_obj.selector+')');
            return false;
        }
        _self.run_obj.css('width',_self.all_width + 'px');
        
        _self.main_obj.on('mouseover',function(){
            _self.lock = true;
        }).on('mouseout',function(){
            _self.lock = false;
        });
        _self.previous_obj = '';
        _self.next_obj = '';
        _self.index_obj = '';
        _self.run_time = 100;
        return true;
    },
    
    //Top Buttom Auto Stop
    'tb_as':function(_self,direction,index)
        {
            return function()
            {
                var target_top = parseInt(_self.run_obj.css('top')) + direction * -1;
                if(_self.all_height/2 * direction * -1 > target_top)target_top = 0;
                _self.run_obj.css({'top':target_top});
                _self.lock = false;
            }
        },
    'tb_as_init':function(_self)
    {
        var children_obj = _self.run_obj.children();
        if(0 == children_obj.length)
        {
            console.log('M_dynamic_content children_obj no exists('+_self.main_obj.selector+')');
            return false;
        }
        _self.child_width = $(children_obj[0]).width();
        _self.child_height = $(children_obj[0]).height();
        children_obj.each(function(k,v){
            _self.run_obj.append($(v).clone());
        });
        _self.all_height = _self.child_height * children_obj.length * 2;
        if(_self.mask_height > _self.all_height)
        {
            console.log('M_dynamic_content mask_height > all_height('+_self.main_obj.selector+')');
            return false;
        }
        _self.run_obj.css('height',_self.all_height + 'px');
        
        _self.main_obj.on('mouseover',function(){
            _self.lock = true;
        }).on('mouseout',function(){
            _self.lock = false;
        });
        _self.previous_obj = '';
        _self.next_obj = '';
        _self.index_obj = '';
        _self.run_time = 100;
        return true;
    }
}
