
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" action="" method="post" >
                    <input type="hidden" name="id" value="{$edit_info.id}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.title') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.title') }}" name="title" value="{$edit_info.title}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.author') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.author') }}" name="author" value="{$edit_info.author}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.description') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.description') }}" name="description" value="{$edit_info.description}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.sort') }}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" placeholder="{{ trans('common.sort') }}" name="sort" onKeyup="M_in_int_range(this,1,100);"
       value="<if condition="$edit_info['sort']">{$edit_info.sort}<else />100</if>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.channel') }}</label>
                                <div class="col-sm-6">
                                    <select name="channel_id" class="form-control input-sm" >
                                        <option value="">{{ trans('common.default') }}{{ trans('common.dont') }}{{ trans('common.pertain') }}{{ trans('common.channel') }}</option>
                                        <foreach name="channel_list" item="channel">
                                            <option value="{$channel.id}" <if condition="$channel['id'] eq $edit_info['channel_id']">selected="selected"</if> >{$channel.name}</option>
                                        </foreach>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.category') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control input-sm" name="cate_id">
                                        <option value="">{{ trans('common.default') }}{{ trans('common.dont') }}{{ trans('common.pertain') }}{{ trans('common.category') }}</option>
                                        <foreach name="category_list" item="category">
<option value="{$category.id}" <if condition="$category['id'] eq $edit_info['cate_id'] or $category['id'] eq I('cate_id')">selected="selected" mtype="def_data"</if> >{$category.name}</option>
                                        </foreach>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="attribute" class="row">
                        <import file="js/M_attribute_editor" />
                        <script type="text/javascript">
                            $(function(){
                                var config = {
                                    <if condition="$edit_info['attribute_tpl']">'def_data':{$edit_info.attribute_tpl|json_encode},</if>
                                    <if condition="$edit_info['attribute']">'def_selected':{$edit_info.attribute|json_encode},</if>
                                    'run_type':'select',
                                    'select_obj':$('select[name=cate_id]'),
                                    'out_obj':$('#attribute'),
                                    'ajax_url':'{{ route('ajax_api') }}',
                                    'post_name':'attribute'
                                };
                                new M_attribute_editor(config);
                            });
                        </script>
                    </div>
                    <div id="extend_list" class="row">
                        <import file="js/M_exttpl_editor" />
                        <script type="text/javascript">
                            $(function(){
                                var config = {
                                    <if condition="$edit_info['extend']">'def_data':{$edit_info.extend|json_encode},</if>
                                    'run_type':'edit',
                                    'out_obj':$('#extend_list'),
                                    'edit_obj':$('select[name=cate_id]'),
                                    'post_name':'extend',
                                    'ajax_url':'{{ route('ajax_api') }}'
                                };
                                new M_exttpl_editor(config);
                            });
                        </script>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.stick') }}</label>
                                <div class="col-sm-4">
                                    <label class="radio-inline">
<input type="radio" name="is_stick" value="1" <if condition="'1' heq $edit_info['is_stick']">checked="checked"</if> />{{ trans('common.yes') }}
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="is_stick" value="0" <if condition="'0' heq $edit_info['is_stick'] or !isset($edit_info['is_stick'])">checked="checked"</if> />{{ trans('common.no') }}
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.audit') }}</label>
                                <div class="col-sm-4">
                                    <label class="radio-inline">
<input type="radio" name="is_audit" value="1" <if condition="'1' heq $edit_info['is_audit'] or !isset($edit_info['is_audit'])">checked="checked"</if> />{{ trans('common.yes') }}
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="is_audit" value="0" <if condition="'0' heq $edit_info['is_audit']">checked="checked"</if> />{{ trans('common.no') }}
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.show') }}</label>
                                <div class="col-sm-4">
                                    <label class="radio-inline">
<input type="radio" name="if_show" value="1" <if condition="'1' heq $edit_info['if_show'] or !isset($edit_info['if_show'])">checked="checked"</if> />{{ trans('common.show') }}
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="if_show" value="0" <if condition="'0' heq $edit_info['if_show']">checked="checked"</if> />{{ trans('common.hidden') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.thumb') }}</label>
                                <div class="col-sm-5">
                                    <div id="uploadbutton" title="{{ trans('common.selection') }}{{ trans('common.or') }}{{ trans('common.upload') }}" class="default_image">
                                        <M:Img id="thumb_src" src="{:M_exists($edit_info['thumb'])}" />
                                    </div>
                                    <input id="thumb_val" type="hidden" name="thumb" value="{$edit_info.thumb}" />
                                    <script>
                                        function M_article_uploadbutton(url,title)
                                        {
                                            {//必须替换掉__ROOT__/ 原因伪静态目录结构}
                                            $('#thumb_val').val(url.replace(RegExp('^'+$Think.root),''));
                                            $('#thumb_src').attr('src',url);
                                        }
                                        M_jqueryui_tooltip('#uploadbutton');
                                    </script>
                                    <M:Uploadfile id="uploadbutton" type="image" dir="kindeditor" cb_fn="M_article_uploadbutton" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-sm-6 control-label">{{ trans('common.add') }}{{ trans('common.time') }}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" disabled="disabled" placeholder="{{ trans('common.add') }}{{ trans('common.time') }}" name="add_time" value="{$edit_info.add_time|M_date=C('SYS_DATE_DETAIL')}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-sm-6 control-label">{{ trans('common.update') }}{{ trans('common.time') }}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" disabled="disabled" placeholder="{{ trans('common.update') }}{{ trans('common.time') }}" name="update_time" value="{$edit_info.update_time|M_date=C('SYS_DATE_DETAIL')}"/>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            function change_time(obj)
                            {
                                var time_obj = $(obj).parent().parent().find('[name=add_time],[name=update_time]');
                                var status = $(obj).prop('checked');
                                if(status)
                                {
                                    time_obj.prop('disabled',false);
                                }
                                else
                                {
                                    time_obj.prop('disabled',true);
                                }
                            }
                        </script>
                        <M:Timepicker start="add_time" end="update_time" />
                        <label class="checkbox-inline">
                            <input type="checkbox" onclick="change_time(this)" />{{ trans('common.change') }}{{ trans('common.time') }}
                        </label>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.access') }}{{ trans('common.member') }}{{ trans('common.group') }}</label>
                                <div class="col-sm-6"><h4 id="access_group_id_list" style="margin:2px 0px 0px 0px;"></h4></div>
                            </div>
                        </div>
                        <div class="col-sm-6" id="access_group_list">
                            <import file="js/M_select_add" />
                            <script type="text/javascript">
                                $(function(){
                                    var config = {
                                        <if condition="$edit_info['access_group_id']">'def_data':{$edit_info.access_group_id},</if>
                                        'out_obj':$('#access_group_id_list'),
                                        'edit_obj':$('#access_group_list'),
                                        'post_name':'access_group_id[]',
                                        'ajax_url':'{{ route('ajax_api') }}',
                                        'field':'access_group_id'
                                    };
                                    new M_select_add(config);
                                });
                            </script>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-1 control-label">{{ trans('common.character') }}{{ trans('common.content') }}</label>
                                <div class="col-sm-10">
                                    <textarea rows="15" class="col-sm-12" name="content">{$edit_info.content}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-1 control-label">{{ trans('common.image') }}{{ trans('common.content') }}</label>
                                <div class="col-sm-10">
                                    <button id="uploadsbutton" type="button" class="btn btn-default">
                                        {{ trans('common.upload') }}{{ trans('common.image') }}{{ trans('common.group') }}
                                    </button>
                                    <script>
                                        function M_article_uploadsbutton(url,title,description)
                                        {
                                            var image_box = $(Array(
                                                '<div class="fl mr20 mb20" style="width:195px;height:120px;">',
                                                '<div class="default_image">',
                                                    '<img />',
                                                    '<input type="hidden" name="album[]" />',
                                                '</div>',
                                                '<div class="fr glyphicon glyphicon-remove" style="top:-115px;right:5px;cursor:pointer;" ></div>',
                                                '</div>'
                                            ).join(''));
                                            {//必须替换掉__ROOT__/ 原因伪静态目录结构}
                                            if('object' == typeof(url))
                                            {
                                                console.log(url);
                                                title = url.title;
                                                description = url.description;
                                                url = url.src;
                                            }
                                            var img_data = {
                                                'src':url.replace(RegExp('^'+$Think.root),''),
                                                'title':title,
                                                'description':description
                                            }
                                            image_box.find('input').val(JSON.stringify(img_data));
                                            image_box.find('img').attr('src',$Think.root + url.replace(RegExp('^'+$Think.root),''));
                                            
                                            var image_title = $('#image_title');
                                            var image_description =$('#image_description');
                                            var iamge_editor = $('#image_title,#image_description');
                                            image_box.on('click',function(){
                                                var old_data = JSON.parse(image_box.find('input').val());
                                                image_title.val(old_data.title);
                                                image_description.val(old_data.description);
                                                iamge_editor.prop('disabled',false).off('change keyup');
                                                iamge_editor.on('change keyup',function(){
                                                    old_data.title = image_title.val();
                                                    old_data.description = image_description.val();
                                                    image_box.find('input').val(JSON.stringify(old_data));
                                                });
                                            });
                                            image_box.find('.glyphicon-remove').on('click',function(){
                                                $(this).parent().remove();
                                                image_title.val('');
                                                image_description.val('');
                                                iamge_editor.prop('disabled',true);
                                            });
                                            var div = $('#image_box');
                                            div.append(image_box).sortable();
                                        }
                                        <if condition="$edit_info['album']">
                                        $(function(){
                                            <foreach name="edit_info.album" item="data">
                                                 <if condition="'null' neq $data">M_article_uploadsbutton({{ $data }});</if>
                                             </foreach>
                                        });
                                        </if>
                                    </script>
                                    <M:Uploadfile id="uploadsbutton" type="multiimage" dir="kindeditor" cb_fn="M_article_uploadsbutton" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-1 control-label">{{ trans('common.image') }}{{ trans('common.title') }}</label>
                                <div class="col-sm-10">
                                    <input id="image_title" disabled="disabled" type="text" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-1 control-label">{{ trans('common.image') }}{{ trans('common.description') }}</label>
                                <div class="col-sm-10">
                                    <input id="image_description" disabled="disabled" type="text" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-1 control-label"></label>
                                <div id="image_box" class="col-sm-10">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cb"></div>
                    <div class="row mt10">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                <if condition="$Think.const.ACTION_NAME eq 'add'">
                                    {{ trans('common.add') }}
                                <elseif condition="$Think.const.ACTION_NAME eq 'edit'" />
                                    {{ trans('common.edit') }}
                                </if>
                            </button>
                            <a href="{{ route('index') }}" class="btn btn-default">
                                    {{ trans('common.goback') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <M:Kindeditor name="content" />