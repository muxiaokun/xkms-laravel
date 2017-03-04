/* +----------------------------------------------------------------------
 | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
 +----------------------------------------------------------------------
 | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved.
 +----------------------------------------------------------------------
 | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 +----------------------------------------------------------------------
 | Author: merry M  <test20121212@qq.com>
 +----------------------------------------------------------------------
 M_goods_attribute Class Javascript
 Include : jQuery jQueryUi.dialog()
 Included List(Module Controller Action)
 Admin Goods addedit
 */
'use strict';

function M_attribute_editor(config) {
    if ('object' != typeof(config)) {
        console.log('config no exists');
        return;
    }
    var _self = this;
    $.extend(_self, config);
    //检查初始化元素
    if (0 == _self.run_type.length) console.log('run_type no exists');
    if (0 == _self.out_obj.length) console.log('out_obj no exists');
    if (0 == _self.post_name.length) console.log('post_name no exists');
    if (0 == _self.out_obj.length
        || 0 == _self.post_name.length
    )return;
    _self.initialize();
}

M_attribute_editor.prototype =
    {
        'run_type': '',
        'ajax_url': '',
        'editor_name_obj': $(Array(
            '<div class="col-sm-6 text-center">',
            '<button type="button" class="btn btn-sm btn-default">',
            lang.common.add,
            lang.common.attribute,
            lang.common.name,
            '</button>',
            '</div>'
        ).join('')),
        'editor_value_obj': $(Array(
            '<div class="col-sm-6 text-center">',
            '<button type="button" class="btn btn-sm btn-default">',
            lang.common.add,
            lang.common.attribute,
            lang.common.value,
            '</button>',
            '</div>'
        ).join('')),
        'select_html': Array(
            '<div class="col-sm-6"><div class="form-group">',
            '<label class="col-sm-4 control-label"></label>',
            '<div class="col-sm-6">',
            '<select class="form-control">',
            '<option value="">' + lang.common.none + lang.common.attribute + '</option></select>',
            '</div></div></div>'
        ).join(''),
        'editor_input_obj': Array(
            '<div class="col-sm-12 mtb5"><div class="col-sm-10">',
            '<input class="form-control input-sm" type="text" /></div>',
            '<span class="glyphicon glyphicon-remove close"></span></div>'
        ).join(''),
        'auto_increment': 0,
        'out_obj': '',
        'select_obj': '',
        'post_name': '',
        'def_data': '',
        'def_selected': ''
    }

M_attribute_editor.prototype.initialize = function () {
    var _self = this;

    switch (_self.run_type) {
        case 'select':
            if (0 == _self.select_obj.length) {
                console.log('select_obj no exists');
                return;
            }
            if (0 == _self.ajax_url.length) {
                console.log('ajax_url no exists');
                return;
            }
            _self.init_select();
            break;
        case 'edit':
            _self.out_obj.append(_self.editor_name_obj);
            _self.editor_name_obj.find('button').on('click', function () {
                _self.init_name_input();
            });
            _self.out_obj.append(_self.editor_value_obj);
            _self.editor_value_obj.find('button').prop('disabled', true);
            //初始化数据1 开始 因为get_id()的存在
            if (_self.def_data) {
                for (var name in _self.def_data) {
                    _self.init_name_input(name, _self.def_data[name]);
                }
            }
        //初始化数据1 结束
    }
}

//initialize input (保持有一个空的存在删除其他为空的)
M_attribute_editor.prototype.init_name_input = function (name, values) {
    var _self = this;
    var attr_id = _self.get_id();
    //初始化 input_name_obj 开始
    var input_name_obj = $(_self.editor_input_obj);
    var input_name_obj_input = input_name_obj.find('input[type=text]');
    input_name_obj.attr('attr_id', attr_id);
    input_name_obj_input.attr('name', _self.post_name + '[' + attr_id + '][name]');
    input_name_obj.find('.close').on('click', function () {
        _self.init_value_input(attr_id, true);
        input_name_obj.remove();
    });
    //初始化数据2 开始 因为get_id()的存在
    if (name) input_name_obj_input.val(name);
    if (values) {
        for (var key in values) {
            _self.add_value_input(attr_id, values[key]);
        }
    }
    ;
    //初始化数据2 结束
    //初始化 input_name_obj 结束

    var editor_value_obj_button = _self.editor_value_obj.find('button');
    //添加值 开始
    input_name_obj_input.on('focus', function () {
        _self.init_value_input(attr_id, false);
        editor_value_obj_button.off('click').on('click', function () {
            _self.add_value_input(attr_id);
        });
        //添加值 结束
    }).on('focus keyup', function () {
        var input_name_val = input_name_obj_input.val();
        var editor_value_button_html = '';
        if (input_name_val != '') {
            editor_value_button_html = Array(
                lang.common.add,
                '[',
                input_name_val,
                ']',
                lang.common.value
            ).join('');
            //检测重复的值 开始
            var input_repaet = 0;
            _self.editor_name_obj.find('input[type=text]').each(function (k, v) {
                if ($(v).val() == input_name_val) input_repaet++;
            });
            if (1 < input_repaet) {
                editor_value_obj_button.css({'color': 'red'});
                editor_value_button_html = Array(
                    lang.common.attribute,
                    lang.common.name,
                    lang.common.repeat
                ).join('');
                editor_value_obj_button.prop('disabled', true);
            }
            else {
                editor_value_obj_button.css({'color': ''});
                editor_value_obj_button.prop('disabled', false);
            }
            //检测重复的值 结束
        }
        else {
            editor_value_obj_button.css({'color': 'red'});
            editor_value_button_html = Array(
                lang.common.attribute,
                lang.common.name,
                lang.common.not,
                lang.common.empty
            ).join('');
            editor_value_obj_button.prop('disabled', true);
        }
        input_name_obj_input.val(input_name_val);
        editor_value_obj_button.html(editor_value_button_html);
    });
    _self.editor_name_obj.append(input_name_obj);
}

//initialize value input
M_attribute_editor.prototype.init_value_input = function (attr_id, is_del) {
    var _self = this;
    //处理 值 input 开始
    _self.editor_value_obj.find('[attr_pid]').hide();
    var current_input_objs = _self.editor_value_obj.find('[attr_pid="' + attr_id + '"]');
    if (is_del) {
        current_input_objs.remove();
    }
    else {
        current_input_objs.show();
    }
}

//add value input
M_attribute_editor.prototype.add_value_input = function (attr_id, value) {
    var _self = this;
    var input_value_obj = $(_self.editor_input_obj);
    var input_value_obj_input = input_value_obj.find('input[type=text]');
    input_value_obj_input.attr('name', _self.post_name + '[' + attr_id + '][value][]');
    input_value_obj.attr('attr_pid', attr_id);
    input_value_obj.find('.close').on('click', function () {
        input_value_obj.remove();
    });
    if (value) {
        input_value_obj.hide();
        input_value_obj_input.val(value);
    }
    _self.editor_value_obj.append(input_value_obj);
}

//initialize select
M_attribute_editor.prototype.init_select = function () {
    var _self = this;
    //输出def_data
    if (_self.def_data) _self.init_select_value(_self.def_data);
    _self.select_obj.on('change', function () {
        _self.out_obj.html('');
        var post_data = {};
        post_data['type'] = 'get_data';
        post_data['field'] = 'attribute';
        post_data['data'] = {'id': _self.select_obj.val()};
        if (!post_data.data.id)return;
        //绑定ajax
        $.ajax({
            'url': _self.ajax_url,
            'data': post_data,
            'type': 'POST',
            'dataType': 'JSON',
            'cache': false,
            'success': function (data) {
                if (data && data.status) {
                    _self.init_select_value(data.info);
                }
            },
            'error': function () {
                console.log('ajax error');
            }
        });
    });
}

//initialize select
M_attribute_editor.prototype.init_select_value = function (def_data, def_selected) {
    var _self = this;
    //初始化数据1 开始 因为get_id()的存在
    if (def_data) {
        $.each(def_data, function (k, v) {
            var select_out_obj = $(_self.select_html);
            select_out_obj.find('label').html(k);
            var select_out_obj_select = select_out_obj.find('select');
            select_out_obj_select.attr('name', _self.post_name + '[' + k + ']')
            $.each(v, function (kk, vv) {
                var select_out_obj_option = $('<option value=' + vv + '>' + vv + '</option>');
                if (_self.def_selected && _self.def_selected[k] == vv) select_out_obj_option.prop('selected', true);
                select_out_obj_select.append(select_out_obj_option);
            });
            _self.out_obj.append(select_out_obj);
        });
    }
    //初始化数据1 结束
}

M_attribute_editor.prototype.get_id = function () {
    var _self = this;
    return ++_self.auto_increment;
}