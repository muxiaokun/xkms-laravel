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
                        @lang('common.database'){{ $database }}@lang('common.dont')@lang('common.exists')@lang('common.auto_create')
                    </div>
                @endif
            </div>
            <form action="{{ route('Install::setp3') }}" method="post">
                <div class="col-sm-12">
                    <input type="hidden" name="setp" value="3">
                    <table class="table table-hover">
                        <tr>
                            <th>
                                @lang('common.controller')@lang('common.info')
                            </th>
                            <th>
                                <label class="checkbox-inline">
                                    <input type="checkbox" onClick="allselect('[mtype=install]',this);"/>
                                    @lang('common.allselect')
                                </label>
                            </th>
                            <th>
                                @lang('common.table')@lang('common.info')
                                &nbsp;&nbsp;
                                <label class="checkbox-inline">
                                    <input type="checkbox" onClick="allselect('[mtype=reset]',this);"/>
                                    <span style="color:red;">@lang('install.setp2_commont1')</span>
                                </label>
                            </th>
                        </tr>
                        @foreach ($tables as $control_index => $data)
                            <tr>
                                <td>
                                    [{{ $data['control_group'] }}]{{ $data['control_info'] }}
                                </td>
                                <td>
                                    @if (1 < $data['category'])
                                        <input type="checkbox" name="install_control[install][]"
                                               value="{{ $control_index }}" mtype="install"/>
                                    @else
                                        <input type="checkbox" disabled="disabled" checked="checked"/>
                                    @endif
                                </td>
                                <td class="text-left">
                                    @if ($data['tables'])
                                        @foreach ($data['tables'] as $table_name => $table)
                                            <div class="fl">
                                                {{ $table['table_info'] }}{{ $table_name }}(
                                                @if ($table['if_exists'] == 1)
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" name="install_control[reset][]"
                                                               value="{{ $table_name }}" mtype="reset"/>
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
                    <a class="btn btn-lg btn-primary mt20 mr80"
                       href="{{ route('Install::setp1') }}">@lang('common.previous')@lang('common.setp')</a>
                    <button id="switch_href" class="btn btn-lg btn-primary mt20"
                            type="submit">@lang('install.setp2')</button>
                </div>
            </form>
        </div>
    </section>
    {{--安装第二步界面 结束--}}
@endsection

@push('scripts')
<script type="text/javascript">
    $(function () {
        if (parent && parent.move_progress) {
            parent.move_progress({{ config('setp_progress.2') }});
        }
    });
</script>
@endpush