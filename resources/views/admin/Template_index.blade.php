
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form id="form_valid" class="form-horizontal" role="form" action="" method="post" >
                    <div class="form-group">
                        <div class="col-sm-8 text-center">
                            <if condition="$theme_list">
                            <label class="col-sm-4 control-label">{{ trans('common.selection') }}{{ trans('common.current') }}{{ trans('common.theme') }}</label>
                            <div class="col-sm-6">
                                <select class="form-control" onchange="window.location.href = $(this).val()">
                                    <option value="{{ route('index',array('default_theme'=>'empty')) }}">{{ trans('common.please') }}{{ trans('common.selection') }}{{ trans('common.or') }}{{ trans('common.empty') }}</option>
                                    <foreach name="theme_list" item="theme" >
                                        <option value="{{ route('index',array('default_theme'=>$theme)) }}" <if condition="$default_theme eq $theme">selected="selected"</if> >{{ $theme }}</option>
                                    </foreach>
                                </select>
                            </div>
                            </if>
                        </div>
                        <div class="col-sm-4 text-center">
                            <button type="submit" class="btn btn-info">
                                {{ trans('common.save') }}
                            </button>
                            <a href="{{ route('Index/main') }}" class="btn btn-default">
                                {{ trans('common.goback') }}
                            </a>
                        </div>
                    </div>
                    <table class="table table-condensed table-hover">
                        <tr>
                            <th>{{ trans('common.template') }}{{ trans('common.file') }}{{ trans('common.name') }}</th>
                            <th>{{ trans('common.template') }}{{ trans('common.name') }}</th>
                            <th>{{ trans('common.template') }}{{ trans('common.info') }}</th>
                            <td class="nowrap">
                                <if condition="$batch_handle['add']">
                                    <a class="btn btn-xs btn-success" href="{{ route('add') }}">{{ trans('common.add') }}{{ trans('common.template') }}</a>&nbsp;|&nbsp;
                                </if>
                                <a class="btn btn-xs btn-success" href="{{ route('index',array('refresh'=>1)) }}">{{ trans('common.refresh') }}{{ trans('common.template') }}{{ trans('common.list') }}</a>
                            </td>
                        </tr>
                        <foreach name="theme_info_list" key="file_md5" item="template">
                            <tr>
                                <td>
                                    {$template.file_name}
                                </td>
                                <td>
                                    <input class="form-control" type="text" name="{{ $file_md5 }}[name]" value="{$template.name}" />
                                </td>
                                <td>
                                    <input class="form-control" type="text" name="{{ $file_md5 }}[info]" value="{$template.info}" />
                                </td>
                                <td class="nowrap">
                                    <if condition="$batch_handle['edit']">
                                        <a class="btn btn-xs btn-primary" href="{{ route('edit',array('id'=>$file_md5)) }}">
                                            {{ trans('common.edit') }}
                                        </a>
                                    </if>
                                    <if condition="$batch_handle['edit'] AND $batch_handle['del']">&nbsp;|&nbsp;</if>
                                    <if condition="$batch_handle['del']">
    <a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{$template.file_name}?','{{ route('del',array('id'=>$file_md5)) }}')" >
                                            {{ trans('common.del') }}
                                        </a>
                                    </if>
                                </td>
                            </tr>
                        </foreach>
                    </table>
                </form>
            </div>
        </div>
    </section>
