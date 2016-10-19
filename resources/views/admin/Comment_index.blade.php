@extends('admin.layout')
@section('body')
    <script type="text/javascript" src="{{ asset('js/M_alert_log.js') }}"></script>
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('admin.public_whereInfo')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')"/>&nbsp;@lang('common.id')
                        </th>
                        <th>@lang('common.comment')@lang('common.member')</th>
                        <th>@lang('common.comment')@lang('common.time')</th>
                        <th>@lang('common.audit')@lang('common.admin')</th>
                        <th>@lang('common.controller')</th>
                        <th>@lang('common.id')</th>
                        <th>@lang('common.comment') IP</th>
                        <td class="nowrap">
                            @if ($batch_handle['add'])
                                <a class="btn btn-xs btn-success"
                                   href="{{ route('add') }}">@lang('common.config')@lang('common.comment')</a>
                            @endif
                        </td>
                    </tr>
                    @foreach ($comment_list as $comment)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $comment['id'] }}"/>
                                &nbsp;{{ $comment['id'] }}
                            </td>
                            <td>
                                {{ $comment['member_name'] }}
                            </td>
                            <td>
                                {{ mDate($comment['created_at']) }}
                            </td>
                            <td>
                                {{ $comment['audit_name'] }}
                            </td>
                            <td>
                                {{ $comment['controller'] }}
                            </td>
                            <td>
                                {{ $comment['item'] }}
                            </td>
                            <td>
                                {{ $comment['aip'] }}
                            </td>
                            <td class="nowrap">
                                <a id="M_alert_log_{{ $comment['id'] }}" class="btn btn-xs btn-primary"
                                   href="javascript:void(0);">@lang('common.look')</a>
                                <script>
                                    $(function () {
                                        var config = {
                                            'bind_obj': $('#M_alert_log_{{ $comment['id'] }}'),
                                            'title': '@lang('common.comment')@lang('common.content')',
                                            'message': "{{ $comment['content'] }}"
                                        }
                                        new M_alert_log(config);
                                    });
                                </script>
                                @if ($batch_handle['edit'])
                                    &nbsp;|&nbsp;
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('edit',array('id'=>$comment['id'])) }}">
                                        @lang('common.audit')
                                    </a>
                                @endif
                                @if ($batch_handle['del'])
                                    &nbsp;|&nbsp;
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del')@lang('common.comment')?','{{ route('del',array('id'=>$comment['id'])) }}')">
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
                                        'name': $Think.lang.audit,
                                        'post_link': '{{ route('edit') }}'
                                    });
                                    @endif
                                    @if ($batch_handle['del'])
                                        config.type_data.push({
                                        'name': $Think.lang.del,
                                        'post_link': '{{ route('del') }}'
                                    });
                                    @endif
                                            new M_batch_handle(config);
                                });
                            </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="comment_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection