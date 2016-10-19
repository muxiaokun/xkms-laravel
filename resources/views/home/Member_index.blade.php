@extends('home.Member_layout')
@section('content')
    <table class="table table-condensed table-hover">
        <tr>
            <td>@lang('common.account')@lang('common.name')</td>
            <td>{{ $Think['session']['frontend_info']['member_name'] }}</td>
        </tr>
        <tr>
            <td>@lang('common.register')@lang('common.time')</td>
            <td>{{ mDate($Think['session']['frontend_info']['register_time']) }}</td>
        </tr>
        <tr>
            <td>@lang('common.login')@lang('common.time')</td>
            <td>{{ mDate($Think['session']['frontend_info']['last_time']) }}</td>
        </tr>
        <tr>
            <td>@lang('common.last')@lang('common.login')IP</td>
            <td>{{ $Think['session']['frontend_info']['aip'] }}</td>
        </tr>
    </table>
@endsection