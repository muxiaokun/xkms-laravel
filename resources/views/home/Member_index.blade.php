@extends('Member:base')
@section('content')
    <table class="table table-condensed table-hover">
        <tr>
            <td>{{ trans('common.account') }}{{ trans('common.name') }}</td>
            <td>{$Think.session.frontend_info.member_name}</td>
        </tr>
        <tr>
            <td>{{ trans('common.register') }}{{ trans('common.time') }}</td>
            <td>{$Think.session.frontend_info.register_time|M_date=C('SYS_DATE_DETAIL')}</td>
        </tr>
        <tr>
            <td>{{ trans('common.login') }}{{ trans('common.time') }}</td>
            <td>{$Think.session.frontend_info.last_time|M_date=C('SYS_DATE_DETAIL')}</td>
        </tr>
        <tr>
            <td>{{ trans('common.last') }}{{ trans('common.login') }}IP</td>
            <td>{$Think.session.frontend_info.aip}</td>
        </tr>
    </table>
@endsection