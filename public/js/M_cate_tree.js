/* +----------------------------------------------------------------------
   | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
   +----------------------------------------------------------------------
   | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved. 
   +----------------------------------------------------------------------
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
   +----------------------------------------------------------------------
   | Author: merry M  <test20121212@qq.com>
   +----------------------------------------------------------------------
    M_cate_tree Class Javascript
    Include : jQuery
    Included List(Module Controller Action)
    Admin ArticleCategory index
    Admin ArticleChannel add,edit
    PS:不做js类，会有大量的html封装进入js
*/
'use strict';

function M_cate_tree(obj,call_back_fn)
{
    var root = $(obj).parents('tr');
    var cate_id = root.attr('cate_id');
    var parent_id = root.attr('parent_id');
    if('' == parent_id)return;
    var has_child = root.attr('has_child');
    var cate_level = root.attr('cate_level');
    cate_level = (cate_level)?cate_level:0;
    var status = root.data('status');
    status = (status)?status:'close';
    if('close' == status && 0 < has_child)
    {
        root.find('.glyphicon').removeClass('glyphicon-plus');
        root.find('.glyphicon').addClass('glyphicon-minus');
        root.attr('status','lock');
        $.ajax({
            'data':{'parent_id':cate_id},
            type:'post',
            dataType:'json',
            'success':function(data){
                cate_level++;
                var offset = 10 + cate_level * 30;
                var previous_id = 0;
                $.each(data,function(k,v){
                    var html = call_back_fn(v,cate_level,offset);
                    if(previous_id)
                    {
                        $('tr[cate_id="'+previous_id+'"]').after(html);
                    }
                    else
                    {
                        root.after(html);
                    }
                    M_cate_tree('input[name="category_list[]"][value="'+v.id+'"]',call_back_fn);
                    previous_id = v.id;
                });
                root.data('status','open');
            },
            'error':function(){window.console.log('M_cate_tree ajax error');}
        });
    }
    else if('open' == status)
    {
        root.find('.glyphicon').removeClass('glyphicon-minus');
        root.find('.glyphicon').addClass('glyphicon-plus');
        root.data('status','close');
        var remove_all_obj = function(cate_id)
        {
            var remove_obj = root.siblings('tr[parent_id="'+cate_id+'"]');
            remove_obj.each(function(k,v){
                var v_cate_id = $(v).attr('cate_id');
                if(0 < v_cate_id)remove_all_obj(v_cate_id);
            });
            remove_obj.remove();
        }
        remove_all_obj(cate_id);
    }
}

//分类的选择 用于频道
function M_cate_checkbox(obj,data)
{
    var current_obj = $(obj);
    var root = $(obj).parents('tr');
    var cate_id = root.attr('cate_id');
    var parent_id = root.attr('parent_id');
    if(!data || cate_id == data.parent_id)
    {
        if(0 == root.parent().find('tr[parent_id="'+parent_id+'"] input[type="checkbox"]:checked').length || current_obj.prop('checked'))
        {
            root.parent().find('tr[cate_id="'+parent_id+'"] input[type="checkbox"]').prop('checked',current_obj.prop('checked'));
        }
        root.parent().find('tr[cate_id="'+parent_id+'"] input[type="checkbox"]').each(function(k,v){
            M_cate_checkbox(v,{'cate_id':cate_id,'parent_id':parent_id});
        });
    }
    if(!data || parent_id == data.cate_id)
    {
        root.parent().find('tr[parent_id="'+cate_id+'"] input[type="checkbox"]').prop('checked',current_obj.prop('checked'));
        root.parent().find('tr[parent_id="'+cate_id+'"] input[type="checkbox"]').each(function(k,v){
            M_cate_checkbox(v,{'cate_id':cate_id,'parent_id':parent_id});
        });
    }
}

//call back function area

function article_category_cb(v,cate_level,offset)
{
    //初始化元素
    var html = '';
    var button_class = 'glyphicon mlr10';
    button_class = (0 < v.has_child)?button_class+' glyphicon-plus':button_class+' glyphicon-minus';
    var button = '<span class="'+button_class+'" onclick="M_cate_tree(this,article_category_cb);" style="margin-left:'+offset+'px !important;"></span>';
    var attribute = 'cate_id="'+v.id+'" parent_id="'+v.parent_id+'" has_child="'+v.has_child+'" cate_level='+cate_level;
    var html = Array(
            '<tr '+attribute+'><td>'+button+v.name+'(ID:'+v.id+')</td>',
            '<td onClick="M_line_edit(this);" field_id="'+v.id+'" field="sort" link="'+v.ajax_api_link+'">'+v.sort+'</td>',
            '<td>'+v.show+'</td>',
        '<td><a class="btn btn-xs btn-primary" target="_blank" href="' + v.look_link + '"> ' + lang.common.look + ' </a> &nbsp;|&nbsp; ',
        '<a class="btn btn-xs btn-primary" href="' + v.edit_link + '"> ' + lang.common.edit + ' </a> &nbsp;|&nbsp; ',
            '<a href="javascript:void(0);" class="btn btn-xs btn-danger" ',
        'onClick="return M_confirm(\'' + lang.common.confirm + lang.common.del + '?\',\'' + v.del_link + '\')">' + lang.common.del,
        '</a> &nbsp;|&nbsp; <a class="btn btn-xs btn-primary" href="' + v.add_link + '">' + lang.common.add + lang.common.article + '</td></tr>'
    ).join('');
    return html;
}

function article_channel_cb(v,cate_level,offset)
{
    var s_limit = $('#s_limit').clone();
    var template_list = $('#template_list').clone();
    var list_template_list = $('#list_template_list').clone();
    var article_template_list = $('#article_template_list').clone();
    var checked = (v.checked)?'checked="checked"':'';
    var html = '';
    var button_class = 'glyphicon mlr10';
    button_class = (0 < v.has_child)?button_class+' glyphicon-plus':button_class+' glyphicon-minus';
    var button = '<span class="'+button_class+'" onclick="M_cate_tree(this,article_channel_cb);" style="margin-left:'+offset+'px !important;"></span>';
    var attribute = 'cate_id="'+v.id+'" parent_id="'+v.parent_id+'" has_child="'+v.has_child+'" cate_level='+cate_level;

    s_limit.find('input').attr('name','s_limit['+v.id+']');
    template_list.find('select').attr('name','template_list['+v.id+']');
    list_template_list.find('select').attr('name','list_template_list['+v.id+']');
    article_template_list.find('select').attr('name','article_template_list['+v.id+']');
    s_limit.find('input').attr('value',v.s_limit);
    template_list.find('select').find('option[value="'+v.template+'"]').attr('selected','selected');
    list_template_list.find('select').find('option[value="'+v.list_template+'"]').attr('selected','selected');
    article_template_list.find('select').find('option[value="'+v.article_template+'"]').attr('selected','selected');
    html = Array(
        '<tr '+attribute+'><td>'+button+'<input type="checkbox" name="category_list[]" value="',
        v.id + '" ' + checked + ' onClick="M_cate_checkbox(this)" />' + v.name+'(ID:' + v.id + ')</td>',
        '<td>' + s_limit.html() + '</td>',
        '<td>' + template_list.html() + '</td>',
        '<td>' + list_template_list.html() + '</td>',
        '<td>' + article_template_list.html() + '</td></tr>'
        ).join('');
    return html;
}