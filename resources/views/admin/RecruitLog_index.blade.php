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
                        <th>@lang('recruit.re_recruit')</th>
                        <th>@lang('recruit.recruit_name')</th>
                        <th>@lang('recruit.recruit')@lang('common.time')</th>
                        <th>@lang('recruit.recruit_birthday')</th>
                        <th>@lang('recruit.recruit_sex')</th>
                        <th>@lang('recruit.recruit_certificate')</th>
                        <th>@lang('common.handle')</th>
                    </tr>
                    @foreach ($recruit_log_list as $recruit_log)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $recruit_log['id'] }}"/>
                                &nbsp;{{ $recruit_log['id'] }}
                            </td>
                            <td>
                                {{ $recruit_log['recruit_title'] }}
                            </td>
                            <td>
                                {{ $recruit_log['name'] }}
                            </td>
                            <td>
                                {{ mDate($recruit_log['created_at']) }}
                            </td>
                            <td>
                                {{ mDate($recruit_log['birthday']) }}
                            </td>
                            <td>
                                {{ $recruit_log['sex'] }}
                            </td>
                            <td>
                                {{ $recruit_log['certificate'] }}
                            </td>
                            <td class="nowrap">
                                <a id="M_alert_log_{{ $recruit_log['id'] }}" class="btn btn-xs btn-primary"
                                   href="javascript:void(0);">@lang('common.look')</a>
                                <script>
                                    $(function () {
                                        var config = {
                                            'bind_obj': $('#M_alert_log_{{ $recruit_log['id'] }}'),
                                            'title': '@lang('common.recruit_log')',
                                            'message':{{ $recruit_log['ext_info']|json_encode }}
                                        }
                                        new M_alert_log(config);
                                    });
                                </script>
                                @if ($batch_handle['del'])
                                    &nbsp;|&nbsp;
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del'){{ $recruit_log['name'] }}?','{{ route('Admin::RecruitLog::del',array('id'=>$recruit_log['id'])) }}')">
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
                                        'post_link': '{{ route('Admin::RecruitLog::del') }}'
                                    });
                                    @endif
                                            new M_batch_handle(config);
                                });
                            </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        {{ $recruit_log_list->links('admin.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection