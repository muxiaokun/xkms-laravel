@extends('home.Member_layout')
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
                    {{ $quests['start_time'] }}
                </td>
                <td>
                    {{ $quests['end_time'] }}
                </td>
                <td>
                    @if ($quests['access_info'])
                        <form action="{{ route('Home::Quests::add',['id'=>$quests['id']]) }}" method="get">
                            <input type="hidden" name="id" value="{{ $quests['id'] }}"/>
                            <input type="text" name="access_info"/>
                            <button class="btn btn-default btn-sm">@lang('common.confirm')@lang('common.pass')</button>
                        </form>
                    @else
                        <a href="{{ route('Home::Quests::add',['id'=>$quests['id']]) }}">@lang('common.public')@lang('common.access')</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
        <table class="table">
            <tr>
                <td class="text-right">
                    {{ $quests_list->links('home.pagination') }}
                </td>
            </tr>
        </table>
@endsection