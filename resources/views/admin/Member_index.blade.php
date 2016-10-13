<section class="container mt10">
    <div class="panel panel-default">
        <div class="panel-heading">{{ $title }}</div>
        <div class="panel-body">
            @include('Public:where_info')
            <table class="table table-condensed table-hover">
                <tr>
                    <th><input type="checkbox" onClick="M_allselect_par(this,'table')"/>&nbsp;@lang('common.id')</th>
                    <th>@lang('common.member')@lang('common.name')</th>
                    <th>@lang('common.member')@lang('common.group')</th>
                    <th>@lang('common.register')@lang('common.time')</th>
                    <th>@lang('common.last')@lang('common.login')@lang('common.time')</th>
                    <th>@lang('common.login') IP</th>
                    <th>@lang('common.yes')@lang('common.no')@lang('common.enable')</th>
                    <td class="nowrap">
                        @if ($batch_handle['add'])
                            <a class="btn btn-xs btn-success"
                               href="{{ route('add') }}">@lang('common.add')@lang('common.member')</a>
                        @endif
                    </td>
                </tr>
                @foreach ($member_list as $member)
                    <tr>
                        <td>
                            <input name="id[]" type="checkbox" value="{{ $member['id'] }}"/>
                            &nbsp;{{ $member['id'] }}
                        </td>
                        <td>
                            {{ $member['member_name'] }}
                        </td>
                        <td>
                            {{ $member['group_name'] }}
                        </td>
                        <td>
                            {{ $member['register_time']|M_date=config('system.sys_date_detail') }}
                        </td>
                        <td>
                            {{ $member['last_time']|M_date=config('system.sys_date_detail') }}
                        </td>
                        <td>
                            {{ $member['aip'] }}
                        </td>
                        <td>
                            @if ($member['is_enable'])@lang('common.enable')@else@lang('common.disable')@endif
                        </td>
                        <td class="nowrap">
                            @if ($batch_handle['edit'])
                                <a class="btn btn-xs btn-primary" href="{{ route('edit',array('id'=>$member['id'])) }}">
                                    @lang('common.edit')
                                </a>
                            @endif
                            @if ($batch_handle['edit'] AND $batch_handle['del'])&nbsp;|&nbsp;@endif
                            @if ($batch_handle['del'])
                                <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                   onClick="return M_confirm('@lang('common.confirm')@lang('common.del'){{ $member['member_name'] }}?','{{ route('del',array('id'=>$member['id'])) }}')">
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
                                    'name': $Think.lang.enable,
                                    'post_link': '{{ route('edit') }}',
                                    'post_data': {'is_enable': '1'}
                                });
                                config.type_data.push({
                                    'name': $Think.lang.disable,
                                    'post_link': '{{ route('edit') }}',
                                    'post_data': {'is_enable': '0'}
                                });
                                @endif
                                @if ($batch_handle['del'])
                                    config.type_data.push({'name': $Think.lang.del, 'post_link': '{{ route('del') }}'});
                                @endif
                                        new M_batch_handle(config);
                            });
                        </script>
                    @endif
                </div>
                <div class="col-sm-8 text-right">
                    <M:Page name="member_list">
                        <config></config>
                    </M:Page>
                </div>
            </div>
        </div>
    </div>
</section>