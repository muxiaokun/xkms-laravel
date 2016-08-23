@extends('Member:base')
@section('content')
    <table class="table table-condensed table-hover">
        <tr>
            <th>{{ trans('common.id') }}</th>
            <th>{{ trans('common.title') }}</th>
            <th>{{ trans('common.target') }}</th>
            <th>{{ trans('common.start') }}{{ trans('common.time') }}</th>
            <th>{{ trans('common.end') }}{{ trans('common.time') }}</th>
            <th></th>
        </tr>
        @foreach ($assess_list as $assess)
            <tr>
                <td>
                    {$assess.id}
                </td>
                <td>
                    {$assess.title}
                </td>
                <td>
                    {$assess.target_name}
                </td>
                <td>
                    {$assess.start_time|M_date=C('SYS_DATE_DETAIL')}
                </td>
                <td>
                    {$assess.end_time|M_date=C('SYS_DATE_DETAIL')}
                </td>
                <td>
                    <a href="{:M_U('Assess/add',array('id'=>$assess[id]))}">
                        {{ trans('common.grade') }}
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
    <M:Page name="assess_list">
        <table class="table"><tr><td class="text-right">
            <config></config>
        </td></tr></table>
    </M:Page>
@endsection