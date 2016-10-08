@extends('install.layout')
@section('body')
        @push('scripts')
        <script type="text/javascript">
            $(function(){
                if(parent && parent.move_progress)
                {
                    parent.move_progress({{ config('setp_progress.2') }});
                }
            });
        </script>
        @endpush
        {{--安装第二步界面 开始--}}
        <section class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="text-center alert @if ($compare_database_info['if_exists'] eq 1">alert-success@elsealert-warning@endif)
                        @if ($compare_database_info['if_exists'] eq 1)
                            @lang('common.database'){{ $compare_database_info['name'] }}@lang('common.exists')
                        @else
                            @lang('common.database'){{ $compare_database_info['name'] }}@lang('common.dont')@lang('common.exists')@lang('common.auto_create')
                        @endif
                    </div>
                </div>
                <form action="{{ route('setp3') }}" method="post">
                <div class="col-sm-12">
                    <input type="hidden" name="setp" value="3">
                    <table class="table table-hover">
                        <tr>
                            <th>
                                @lang('common.controller')@lang('common.info')
                            </th>
                            <th>
                                <label class="checkbox-inline">
                                    <input type="checkbox" onClick="allselect('[mtype=install]',this);" />
                                    @lang('common.allselect')
                                </label>
                            </th>
                            <th>
                                @lang('common.table')@lang('common.info')
                                &nbsp;&nbsp;
                                <label class="checkbox-inline">
                                    <input type="checkbox" onClick="allselect('[mtype=reset]',this);" />
                                    <span style="color:red;">@lang('common.setp2_commont1')</span>
                                </label>
                            </th>
                        </tr>
                        @foreach ($compare_tables_info as $control_index => $data)
                        <tr>
                            <td>
                                [{{ $data['control_group'] }}]{{ $data['control_info'] }}
                            </td>
                            <td>
                                @if (1 lt $data['category'])
                                    <input type="checkbox" name="install_control[install][]" value="{{ $control_index }}"  mtype="install"/>
                                @else
                                    <input type="checkbox" disabled="disabled" checked="checked"/>
                                @endif
                            </td>
                            <td class="text-left">
                                @if ($data['tables'])
                                    @foreach ($data['tables'] as $table_name => $table)
                                        <div class="fl">
                                        {{ $table['table_info'] }}{{ $table_name }}(
                                        @if ($table['if_exists'] eq 1)
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="install_control[reset][]" value="{{ $table_name }}"  mtype="reset"/>
                                                <span style="color:red;">@lang('common.exists')</span>
                                            </label>
                                        @else
                                            <span style="color:green;">@lang('common.create')</span>
                                        @endif
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
                    <a class="btn btn-lg btn-primary mt20 mr80" href="{{ route('setp1') }}">@lang('common.previous')@lang('common.setp')</a>
                    <button id="switch_href" class="btn btn-lg btn-primary mt20" type="submit">@lang('common.setp2')</button>
                </div>
                </form>
            </div>
        </section>
        {{--安装第二步界面 结束--}}
@endsection