@extends('Member:base')
@section('content')
    <table class="table table-condensed table-hover">
        <tr>
            <th>@lang('common.id')</th>
            <th>@lang('common.title')</th>
            <th>@lang('common.start')@lang('common.time')</th>
            <th>@lang('common.end')@lang('common.time')</th>
            <th>@lang('common.access')@lang('common.info')</th>
        </tr>
        @foreach ($quests_list as $quests)
            <tr>
                <td>
                    {{ $quests['id'] }}
                </td>
                <td>
                    {{ $quests['title'] }}
                </td>
                <td>
                    {{ $quests['start_time']|M_date=config('system.sys_date_detail') }}
                </td>
                <td>
                    {{ $quests['end_time']|M_date=config('system.sys_date_detail') }}
                </td>
                <td>
                    @if ($quests['access_info'])
                        <form action="{:M_U('Quests/add',array('id'=>$quests['id']))}" method="get">
                            <input type="text" name="access_info"/>
                            <button class="btn btn-default btn-sm">@lang('common.confirm')@lang('common.pass')</button>
                        </form>
                    @else
                        <a href="{:M_U('Quests/add',array('id'=>$quests['id']))}">@lang('common.public')@lang('common.access')</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    <M:Page name="quests_list">
        <table class="table">
            <tr>
                <td class="text-right">
                    <config></config>
                </td>
            </tr>
        </table>
    </M:Page>
@endsection