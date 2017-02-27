@extends('home.Member_layout')
@section('content')
    <table class="table table-condensed table-hover">
        <tr>
            <td>@lang('common.account')@lang('common.name')</td>
            <td>{{ session('frontend_info.member_name') }}</td>
        </tr>
        <tr>
            <td>@lang('common.register')@lang('common.time')</td>
            <td>{{ session('frontend_info.created_at') }}</td>
        </tr>
        <tr>
            <td>@lang('common.login')@lang('common.time')</td>
            <td>{{ session('frontend_info.last_time') }}</td>
        </tr>
        <tr>
            <td>@lang('common.last')@lang('common.login')IP</td>
            <td>{{ session('frontend_info.login_ip') }}</td>
        </tr>
    </table>
@endsection