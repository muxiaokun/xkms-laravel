/* +----------------------------------------------------------------------
 | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
 +----------------------------------------------------------------------
 | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved.
 +----------------------------------------------------------------------
 | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 +----------------------------------------------------------------------
 | Author: merry M  <test20121212@qq.com>
 +----------------------------------------------------------------------
 M_multilevel_selection Class Javascript
 Include : jQuery
 Included List(Module Controller Action)
 */
'use strict';

//从select元素中增加数据
function M_multilevel_selection(config) {
    if ('object' != typeof(config)) {
        console.log('config no exists');
        return;
    }
    var _self = this;
    $.extend(_self, config);
    //检查初始化元素
    if (0 == _self.out_obj.length) console.log('out_obj no exists');
    if (0 == _self.post_name.length) console.log('post_name no exists');
    if (0 == _self.ajax_url.length) console.log('ajax_url no exists');
    if (0 == _self.out_obj.length
        || 0 == _self.post_name.length
        || 0 == _self.ajax_url.length)return;
    //初始化元素
    _self.initialize();
}

M_multilevel_selection.prototype = {
    'out_obj': '',
    'edit_obj': '',
    'submit_type': '',//id or empyt(name)
    'submit_obj': $('<input type="hidden" value="0" />'),
    'post_name': '',
    'ajax_url': ''
}

//初始化
M_multilevel_selection.prototype.initialize = function () {
    var _self = this;

    var def_select_objs = _self.out_obj.find('select');
    if (0 < def_select_objs.length) {
        _self.submit_obj = _self.out_obj.find('input[name=' + _self.post_name + ']');
        def_select_objs.each(function (k, v) {
            $(v).on('change', function () {
                _self.select_obj_change($(this));
            });
        });
        _self.get_data(_self.out_obj.find('select:last').val(), function (data) {
            _self.initialize_select(data);
        });
    } else {
        _self.submit_obj.attr('name', _self.post_name);
        _self.out_obj.append(_self.submit_obj);
        _self.get_data(0, function (data) {
            _self.initialize_select(data);
        });
    }


}

//初始化 选择框
M_multilevel_selection.prototype.initialize_select = function (data) {
    var _self = this;
    //新建地址回调函数
    if (!data || 1 > data.length)return;
    var select_obj = '';
    if (_self.edit_obj) {
        select_obj = _self.edit_obj.clone();
    } else {
        select_obj = $('<select></select>');
    }
    select_obj.append('<option value="">' + lang.common.please + lang.common.selection + '</option>');
    $.each(data, function (k, v) {
        var option_obj = $('<option></option>');
        option_obj.attr('value', v.id);
        option_obj.html(v.name);
        select_obj.append(option_obj);
    });
    select_obj.on('change', function () {
        _self.select_obj_change($(this));
    });
    _self.out_obj.append(select_obj);
}

M_multilevel_selection.prototype.select_obj_change = function (current_obj) {
    var _self = this;
    //删除下级地址
    current_obj.nextAll().remove();
    //新建地址
    var id = current_obj.find(':selected').val();
    var submit_val = '';

    _self.out_obj.find('select').each(function (k, v) {
        var option_val = '';
        if ('id' == _self.submit_type) {
            option_val = $(v).find('option:selected').val();
        } else {
            option_val = $(v).find('option:selected').html();
        }
        if (0 < option_val.length) {
            if ('id' == _self.submit_type) {
                submit_val = option_val;
            } else {
                if (submit_val) submit_val += ' ';
                submit_val += option_val;
            }
        }
    });
    if (submit_val) {
        _self.submit_obj.val(submit_val);
    } else {
        if ('id' == _self.submit_type) {
            _self.submit_obj.val(0);
        }
    }
    if (_self.submit_obj.change) {
        _self.submit_obj.change();
    }

    _self.get_data(id, function (data) {
        _self.initialize_select(data);
    });
}

M_multilevel_selection.prototype.get_data = function (id, cb_fn) {
    var _self = this;
    if (0 !== id && !id) {
        return;
    }
    var post_data = {
        'type': 'get_data',
        'field': 'parent_id',
        'data': {'id': id}
    };
    $.ajax({
        'url': _self.ajax_url,
        'data': post_data,
        'type': 'POST',
        'dataType': 'JSON',
        'cache': false,
        'success': function (data) {
            if (data.status) {
                cb_fn(data.info);
            }
        },
        'error': function () {
            console.log('M_multilevel_selection ajax error' + _self.ajax_url);
        }
    });
}