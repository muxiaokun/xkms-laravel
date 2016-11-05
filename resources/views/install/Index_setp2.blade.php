@extends('install.layout')
@section('body')
    {{--安装第二步界面 开始--}}
    <section class="container">
        <div class="row">
            <div class="col-sm-12">
                @if (in_array($database,$databases))
                    <div class="text-center alert alert-success">
                        @lang('common.database'){{ $database }}@lang('common.exists')
                    </div>
                @else
                    <div class="text-center alert alert-warning">
                        @lang('common.database'){{ $database }}@lang('common.dont')@lang('common.exists')@lang('install.auto_create')
                    </div>
                @endif
            </div>
            <div class="col-sm-12">
                <input type="hidden" name="setp" value="3">
                <table class="table table-hover">
                    <tr>
                        <th>
                            @lang('common.function')@lang('common.info')
                        </th>
                        <th>
                            @lang('common.table')@lang('common.info')
                            &nbsp;&nbsp;
                            <span style="color:red;">@lang('install.setp2_commont1')</span>
                        </th>
                    </tr>
                    @foreach ($install_info as $control_index => $data)
                        <tr>
                            <td>
                                [{{ $data['control_group'] }}]{{ $data['control_info'] }}
                            </td>
                            <td class="text-left">
                                @if ($data['tables'])
                                    @foreach ($data['tables'] as $table_name => $table)
                                        <div class="fl">
                                            {{ $table['table_name'] }}(
                                            @if ($table['if_exists'])
                                                <span style="color:red;">@lang('common.exists')</span>
                                            @else
                                                <span style="color:green;">@lang('common.create')</span>
                                            @endif
                                            {{ $table_name }}
                                            <span>@lang('common.table')</span>
                                            )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </div>
                                    @endforeach
                                @else
                                    @lang('common.none')@lang('common.table')@lang('common.info')
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div class="col-sm-12 text-center">
                <a class="btn btn-lg btn-primary mt20 mr80"
                   href="{{ route('Install::setp1') }}">@lang('common.previous')@lang('common.setp')</a>
                <a class="btn btn-lg btn-primary mt20" href="javascript:void(0);"
                   onclick="M_post_setp3('{{ route('Install::setp3') }}','{{ route('Install::setp4') }}',{{ config('install.setp_progress.3') }});">@lang('install.setp2')</a>
            </div>
            <div class="col-sm-12 text-center">
                <div id="show_box" class="mt20"></div>
            </div>
        </div>
    </section>
    {{--安装第二步界面 结束--}}
@endsection