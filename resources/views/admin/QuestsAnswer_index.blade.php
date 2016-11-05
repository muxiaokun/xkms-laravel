@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ $title }}
                <a class="fr fs10"
                   href="{{ route('Admin::Quests::index',array('id'=>request('id'))) }}">@lang('common.goback')</a>
            </div>
            <div class="panel-body">
                @include('admin.Public_whereInfo')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')"/>&nbsp;@lang('common.id')
                        </th>
                        <th>@lang('common.title')</th>
                        <th>@lang('common.add')@lang('common.time')</th>
                        <th>@lang('common.handle')</th>
                    </tr>
                    @foreach ($quests_answer_list as $quests_answer)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $quests_answer['id'] }}"/>
                                &nbsp;{{ $quests_answer['id'] }}
                            </td>
                            <td>
                                {{ $quests_answer['member_name'] }}
                            </td>
                            <td>
                                {{ mDate($quests_answer['created_at']) }}
                            </td>
                            <td class="nowrap">
                                @if ($batch_handle['add'])
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('Admin::QuestsAnswer::add',array('id'=>$quests_answer['id'])) }}">
                                        @lang('common.look')
                                    </a>
                                @endif
                                @if ($batch_handle['add'] AND $batch_handle['del'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['del'])
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del')?','{{ route('Admin::QuestsAnswer::del',array('id'=>$quests_answer['id'])) }}')">
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
                                        'name': lang.commondel,
                                        'post_link': '{{ route('Admin::QuestsAnswer::del') }}'
                                    });
                                    @endif
                                            new M_batch_handle(config);
                                });
                            </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="quests_answer_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection