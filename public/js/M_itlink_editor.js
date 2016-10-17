/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_itlink_editor Class Javascript
    Include : jQuery jQueryUi.sortable() M_jqueryui_tooltip
    Included List(Module Controller Action)
    Admin Itlink add,edit
*/
'use strict';

function M_itlink_editor(config)
{
    if('object' != typeof(config))
    {
        console.log('config no exists');
        return;
    }
    var _self = this;
    $.extend(_self,config);
    //检查初始化元素
    if(0 == _self.global_var.length)console.log('global_var no exists');
    if(0 == _self.out_obj.length)console.log('out_obj no exists');
    if(0 == _self.upload_btn.length)console.log('upload_btn no exists');
    if(0 == _self.callback_fn.length)console.log('callback_fn no exists');
    if(0 == _self.post_name.length)console.log('post_name no exists');
    if(0 == _self.global_var.length
    || 0 == _self.out_obj.length
    || 0 == _self.upload_btn.length
    || 0 == _self.callback_fn.length
    || 0 == _self.post_name.length
    )return;
    
    //全局添加模式 回调函数
    _self.add_callback_fn_str = Array(
        'function(url,title){',
            'var data = {"itl_image":url};',
            'if(url || title){',
                _self.global_var + '.add_itlink(data);',
            '}',
        '}'
    ).join('');
    _self.initialize();
}

M_itlink_editor.prototype = 
{
    'global_var':'',
    'out_obj':'',
    'upload_btn':'',
    'callback_fn':'',
    'post_name':'',
    'def_image':'',
    'def_data':'',
    'add_callback_fn_str':'',
    'add_btn_obj':Array(
        '<button type="button" class="btn btn-default ml20">',
        lang.add + '(' + lang.none + lang.image + ')</button>'
    ).join(''),
    'itlink_obj':Array(
        '<table class="table table-condensed table-hover">',
        '<tbody></tbody></table>'
    ).join(''),
    'itlink_tr_obj':Array(
            '<tr mtype="row">',
                '<td class="col-sm-2 text-center">',
        '<div mtype="itl_image" class="default_image" title="' + lang.click + lang.edit + '">',
                        '<img />',
                        '<input type="hidden" name="ext_info[image][]" value="" />',
                    '</div>',
                '</td>',
                '<td class="col-sm-8">',
                    '<div class="form-group">',
                        '<label class="col-sm-2 control-label">',
        lang.link + '(' + lang.name + '/' + lang.attribute + ')',
                        '</label>',
                        '<div class="col-sm-6">',
        '<input mtype="itl_text" class="form-control" placeholder="' + lang.link + lang.name + '"  />',
                        '</div>',
                        '<div class="col-sm-2">',
                            '<select class="form-control" mtype="itl_target">',
                                '<option value="_self">_self</option>',
                                '<option value="_blank">_blank</option>',
                                '<option value="_top">_top</option>',
                                '<option value="_parent">_parent</option>',
                            '</select>',
                        '</div>',
                        '<div class="col-sm-2">',
        '<button mtype="remove_btn" class="btn btn-default" type="button" >' + lang.del + '</button>',
                        '</div>',
                    '</div>',
                    '<div class="form-group">',
        '<label class="col-sm-2 control-label">' + lang.link + '</label>',
                        '<div class="col-sm-10">',
        '<input mtype="itl_link" class="form-control" placeholder="' + lang.link + '" />',
                        '</div>',
                    '</div>',
                    '<p class="help-block col-sm-offset-2">',
        lang.insite + lang.colon + 'M/C/A?arg1=argv1,arg2=argv2',
        lang.outsite + lang.colon + 'http:// | https:// | ftp://',
                     '</p>',
                '</td>',
            '</tr>'
    ).join(''),
    //初始化kind上传按钮事件
    'initialize_upload_btn':false,
    'auto_increment':0
}

M_itlink_editor.prototype.initialize = function()
{
    var _self = this;
    var itlink_obj = $(_self.itlink_obj);
    _self.out_obj.append(itlink_obj);
    _self.out_obj = _self.out_obj.find('tbody');
    _self.out_obj.sortable();

    //初始化添加按钮
    var add_btn_obj = $(_self.add_btn_obj);
    add_btn_obj.on('click',function(){
        //注册全局函数 每一次添加时都要重新注册
        eval('window.' + _self.callback_fn + ' = ' + _self.add_callback_fn_str + ';');
        _self.add_itlink();
    });
    //注册全局函数
    eval('window.' + _self.callback_fn + ' = ' + _self.add_callback_fn_str + ';');

    $(_self.upload_btn + ',' + _self.upload_btn + '_filemanage').on('click',function(){
        eval('window.' + _self.callback_fn + ' = ' + _self.add_callback_fn_str + ';');
    });
    $(_self.upload_btn).parent().append(add_btn_obj);

    _self.initialize_data(_self.def_data);
}

M_itlink_editor.prototype.initialize_data = function(data)
{
    var _self = this;
    if('object' != typeof(data))return;
    $.each(data,function(k,v){
        _self.add_itlink(v);
    });
}

//添加新的图文链接 事件
M_itlink_editor.prototype.add_itlink = function(data)
{
    var _self = this;
    //初始化 数据
    var auto_id = _self.get_id();
    var itlink_tr_obj = $(_self.itlink_tr_obj);
    itlink_tr_obj.find('[mtype=itl_image]').attr('id',_self.callback_fn + auto_id );
    var itl_image_img_obj = itlink_tr_obj.find('[mtype=itl_image] > img');
    var itl_image_in_obj = itlink_tr_obj.find('[mtype=itl_image] > input');
    var itl_text_obj = itlink_tr_obj.find('[mtype=itl_text]');
    var itl_target_obj = itlink_tr_obj.find('[mtype=itl_target]');
    var itl_link_obj = itlink_tr_obj.find('[mtype=itl_link]');
    itl_image_img_obj.attr('src',$Think.root + _self.def_image);
    itl_image_in_obj.attr('name',_self.post_name + '[itl_image][' + auto_id + ']');
    itl_text_obj.attr('name',_self.post_name + '[itl_text][' + auto_id + ']');
    itl_target_obj.attr('name',_self.post_name + '[itl_target][' + auto_id + ']');
    itl_link_obj.attr('name',_self.post_name + '[itl_link][' + auto_id + ']');
    if('object' == typeof(data))
    {
        //支持伪静态路径
        if(data.itl_image)
        {
            itl_image_img_obj.attr('src',$Think.root + data.itl_image.replace(RegExp('^'+$Think.root),''));
            itl_image_in_obj.val(data.itl_image.replace(RegExp('^'+$Think.root),''));
        }
        itl_text_obj.val(data.itl_text);
        itl_target_obj.find('[value=' + data.itl_target + ']').prop('selected',true);
        itl_link_obj.val(data.itl_link);
    }

    //初始化 事件
    itlink_tr_obj.find('[mtype=itl_image]').on('click',function(){
        _self.edit_itlink_img(auto_id);
    });

    M_jqueryui_tooltip(itlink_tr_obj.find('[mtype=itl_image]'));
    itlink_tr_obj.find('[mtype=remove_btn]').on('click',function(){
        $(this).parents('tr').remove();
    });
    _self.out_obj.append(itlink_tr_obj);
}

//编辑图片 事件
M_itlink_editor.prototype.edit_itlink_img = function(auto_id)
{
    var _self = this;
    var _filemanage = $(_self.upload_btn + '_filemanage');
    if(_filemanage.click)_filemanage.click();
    //编辑回调函数
    var callback_fn_str = Array(
        'function(url,title){',
            'var edit_obj = $("#'+ _self.callback_fn + auto_id + '");',
            'if(url || title){',
                'edit_obj.find("img").attr("src",url);',
                'edit_obj.find("input").val(url.replace(RegExp(\'^\'+$Think.root + \'\'),""));',
            '}',
        '}'
    ).join('');
    eval('window.' + _self.callback_fn + ' = ' + callback_fn_str + ';');

    //保证对kind的上传按钮只加一次事件
    if(false === _self.initialize_upload_btn)
    {
        $(_self.upload_btn + ',' + _self.upload_btn + '_filemanage').on('click',function(){
            eval('window.' + _self.callback_fn + ' = ' + _self.add_callback_fn_str + ';');
        });
        _self.initialize_upload_btn = true;
    }
}

M_itlink_editor.prototype.get_id = function()
{
    var _self = this;
    return ++_self.auto_increment;
}
