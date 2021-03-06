@extends('admin.layout')
@section('body')
    <script type="text/javascript" src="{{ asset('js/M_alert_log.js') }}"></script>
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('admin.Public_whereInfo')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')"/>&nbsp;@lang('common.id')
                        </th>
                        <th>@lang('common.upload')@lang('common.person')</th>
                        <th>@lang('common.file')@lang('common.name')</th>
                        <th>@lang('common.add')@lang('common.time')</th>
                        <th>@lang('common.size')</th>
                        <th>@lang('common.suffix')</th>
                        <th>@lang('common.bind')@lang('common.info')</th>
                        <td class="nowrap">
                            @if ($batch_handle['edit'])
                                <a class="btn btn-xs btn-success"
                                   href="{{ route('Admin::ManageUpload::edit') }}">@lang('common.clear')@lang('common.none')@lang('common.bind')</a>
                            @endif
                        </td>
                    </tr>
                    @foreach ($manage_upload_list as $manage_upload)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $manage_upload['id'] }}"/>
                                &nbsp;{{ $manage_upload['id'] }}
                            </td>
                            <td>
                                {{ $manage_upload['user_name'] }}
                                [
                                @if (1 == $manage_upload['user_type'])@lang('common.backend')
                                @elseif (2 == $manage_upload['user_type'])@lang('common.frontend')
                                @endif
                                ]
                            </td>
                            <td>
                                {{ $manage_upload['name'] }}
                            </td>
                            <td>
                                {{ $manage_upload['created_at'] }}
                            </td>
                            <td>
                                {{ $manage_upload['size'] }}
                            </td>
                            <td>
                                {{ $manage_upload['suffix'] }}
                            </td>
                            <td class="nowrap">
                                @if (!$manage_upload['bind_info']->isEmpty())
                                    <a id="M_alert_log_{{ $manage_upload['id'] }}" class="btn btn-xs btn-primary"
                                       href="javascript:void(0);">@lang('common.look')</a>
                                    <script>
                                        $(function () {
                                            var config = {
                                                'bind_obj': $('#M_alert_log_{{ $manage_upload['id'] }}'),
                                                'title': '@lang('common.file')@lang('common.bind')@lang('common.info')',
                                                'message': {!! json_encode($manage_upload['bind_info']) !!}
                                            }
                                            new M_alert_log(config);
                                        });
                                    </script>
                                @else
                                    @lang('common.empty')
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="javascript:void(0);"
                                   id="copy_obj{{ $manage_upload['id'] }}"
                                   data-clipboard-text="{{ $manage_upload['path'] }}">
                                    <script type="text/javascript"
                                            charset="utf-8">M_ZeroClipboard('copy_obj{{ $manage_upload['id'] }}');</script>
                                    @lang('common.copy')@lang('common.path')
                                </a>
                                @if ($batch_handle['del'])
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del'){{ $manage_upload['name'] }}?','{{ route('Admin::ManageUpload::del',array('id'=>$manage_upload['id'])) }}')">
                                        @lang('common.del')
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="row">
                    <div id="batch_handle" class="col-sm-4 pagination">
                        @if ($batch_handle['del'])
                            <script type="text/javascript" src="{{ asset('js/M_batch_handle.js') }}"></script>
                            <script type="text/javascript">
                                $(function () {
                                    var config = {
                                        'out_obj': $('#batch_handle'),
                                        'post_obj': 'input[name="id"]',
                                        'type_data': Array()
                                    };
                                    @if ($batch_handle['del'])
                                        config.type_data.push({
                                        'name': lang.common.del,
                                        'post_link': '{{ route('Admin::ManageUpload::del') }}'
                                    });
                                    @endif
                                        new M_batch_handle(config);
                                });
                            </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        {{ $manage_upload_list->links('admin.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection