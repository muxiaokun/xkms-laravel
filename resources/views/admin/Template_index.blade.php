<section class="container mt10">
    <div class="panel panel-default">
        <div class="panel-heading">{{ $title }}</div>
        <div class="panel-body">
            <form id="form_valid" class="form-horizontal" role="form" action="" method="post">
                <div class="form-group">
                    <div class="col-sm-8 text-center">
                        @if ($theme_list)
                            <label class="col-sm-4 control-label">@lang('common.selection')@lang('common.current')@lang('common.theme')</label>
                            <div class="col-sm-6">
                                <select class="form-control" onchange="window.location.href = $(this).val()">
                                    <option value="{{ route('index',array('default_theme'=>'empty')) }}">@lang('common.please')@lang('common.selection')@lang('common.or')@lang('common.empty')</option>
                                    @foreach ($theme_list as $theme)
                                        <option value="{{ route('index',array('default_theme'=>$theme)) }}"
                                                @if ($default_theme == $theme)selected="selected"@endif >{{ $theme }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="col-sm-4 text-center">
                        <button type="submit" class="btn btn-info">
                            @lang('common.save')
                        </button>
                        <a href="{{ route('Admin::Index::main') }}" class="btn btn-default">
                            @lang('common.goback')
                        </a>
                    </div>
                </div>
                <table class="table table-condensed table-hover">
                    <tr>
                        <th>@lang('common.template')@lang('common.file')@lang('common.name')</th>
                        <th>@lang('common.template')@lang('common.name')</th>
                        <th>@lang('common.template')@lang('common.info')</th>
                        <td class="nowrap">
                            @if ($batch_handle['add'])
                                <a class="btn btn-xs btn-success"
                                   href="{{ route('add') }}">@lang('common.add')@lang('common.template')</a>&nbsp;|
                                &nbsp;
                            @endif
                            <a class="btn btn-xs btn-success"
                               href="{{ route('index',array('refresh'=>1)) }}">@lang('common.refresh')@lang('common.template')@lang('common.list')</a>
                        </td>
                    </tr>
                    @foreach ($theme_info_list as $file_md5 => $template)
                        <tr>
                            <td>
                                {{ $template['file_name'] }}
                            </td>
                            <td>
                                <input class="form-control" type="text" name="{{ $file_md5 }}[name]"
                                       value="{{ $template['name'] }}"/>
                            </td>
                            <td>
                                <input class="form-control" type="text" name="{{ $file_md5 }}[info]"
                                       value="{{ $template['info'] }}"/>
                            </td>
                            <td class="nowrap">
                                @if ($batch_handle['edit'])
                                    <a class="btn btn-xs btn-primary" href="{{ route('edit',array('id'=>$file_md5)) }}">
                                        @lang('common.edit')
                                    </a>
                                @endif
                                @if ($batch_handle['edit'] AND $batch_handle['del'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['del'])
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del'){{ $template['file_name'] }}?','{{ route('del',array('id'=>$file_md5)) }}')">
                                        @lang('common.del')
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </form>
        </div>
    </div>
</section>
