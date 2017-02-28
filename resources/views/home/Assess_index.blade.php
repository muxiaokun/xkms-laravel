@extends('home.Member_layout')
@section('content')
    <table class="table table-condensed table-hover">
        <tr>
            <th>@lang('common.id')</th>
            <th>@lang('common.title')</th>
            <th>@lang('common.target')</th>
            <th>@lang('common.start')@lang('common.time')</th>
            <th>@lang('common.end')@lang('common.time')</th>
            <th></th>
        </tr>
        @foreach ($assess_list as $assess)
            <tr>
                <td>
                    {{ $assess['id'] }}
                </td>
                <td>
                    {{ $assess['title'] }}
                </td>
                <td>
                    {{ $assess['target_name'] }}
                </td>
                <td>
                    {{ $assess['start_time'] }}
                </td>
                <td>
                    {{ $assess['end_time'] }}
                </td>
                <td>
                    <a class="btn btn-xs btn-primary" href="{{ route('Home::Assess::add',['id'=>$assess['id']]) }}">
                        @lang('common.grade')
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
    <table class="table">
        <tr>
            <td class="text-right">
                {{ $assess_list->links('home.pagination') }}
            </td>
        </tr>
    </table>
@endsection