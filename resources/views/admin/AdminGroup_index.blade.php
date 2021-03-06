@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('admin.Public_whereInfo')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')"/>&nbsp;@lang('common.id')
                        </th>
                        <th>@lang('common.management')@lang('common.group')@lang('common.name')</th>
                        <th>@lang('common.management')@lang('common.group')@lang('common.explains')</th>
                        <th>@lang('common.yes')@lang('common.no')@lang('common.enable')</th>
                        <td class="nowrap">
                            @if ($batch_handle['add'])
                                <a class="btn btn-xs btn-success"
                                   href="{{ route('Admin::AdminGroup::add') }}">@lang('common.add')@lang('common.management')@lang('common.group')</a>
                            @endif
                        </td>
                    </tr>
                    @foreach ($admin_group_list as $admin_group)
                        <tr>
                            <td>
                                @if ($admin_group['id'] != 1)
                                    <input name="id[]" type="checkbox" value="{{ $admin_group['id'] }}"/>
                                @else
                                    &nbsp;&nbsp;&nbsp;
                                @endif
                                &nbsp;{{ $admin_group['id'] }}
                            </td>
                            <td>
                                {{ $admin_group['name'] }}
                            </td>
                            <td>
                                {{ $admin_group['explains'] }}
                            </td>
                            <td>
                                @if ($admin_group['is_enable'])@lang('common.enable')@else @lang('common.disable')@endif
                            </td>
                            <td class="nowrap">
                                @if ($batch_handle['edit'])
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('Admin::AdminGroup::edit',array('id'=>$admin_group['id'])) }}">
                                        @lang('common.edit')
                                    </a>
                                @endif
                                @if ($batch_handle['edit'] AND $batch_handle['del'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['del'])
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del'){{ $admin_group['name'] }}?','{{ route('Admin::AdminGroup::del',array('id'=>$admin_group['id'])) }}')">
                                        @lang('common.del')
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="row">
                    <div id="batch_handle" class="col-sm-4 pagination">
                        @if ($batch_handle['edit'] OR $batch_handle['del'])
                            <script type="text/javascript" src="{{ asset('js/M_batch_handle.js') }}"></script>
                            <script type="text/javascript">
                                $(function () {
                                    var config = {
                                        'out_obj': $('#batch_handle'),
                                        'post_obj': 'input[name="id"]',
                                        'type_data': Array()
                                    };
                                    @if ($batch_handle['edit'])
                                        config.type_data.push({
                                        'name': lang.common.enable,
                                        'post_link': '{{ route('Admin::AdminGroup::edit') }}',
                                        'post_data': {'is_enable': '1'}
                                    });
                                    config.type_data.push({
                                        'name': lang.common.disable,
                                        'post_link': '{{ route('Admin::AdminGroup::edit') }}',
                                        'post_data': {'is_enable': '0'}
                                    });
                                    @endif
                                    @if ($batch_handle['del'])
                                        config.type_data.push({
                                        'name': lang.common.del,
                                        'post_link': '{{ route('Admin::AdminGroup::del') }}'
                                    });
                                    @endif
                                            new M_batch_handle(config);
                                });
                            </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        {{ $admin_group_list->links('admin.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection