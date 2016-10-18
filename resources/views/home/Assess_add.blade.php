@extends('Member:base')
@section('content')
    <script type="text/javascript" src="{{ asset('js/M_valid.js') }}"></script>
    <script>
        $(function () {
            var config = {
                'form_obj': $('#form_valid'),
                'check_list': {
                    're_grade_id': Array('re_grade_id')
                },
                'ajax_url': "{:M_U('ajax_api')}",
            };
            new M_valid(config);
        });
    </script>
    <form id="form_valid" onSubmit="return false;" method="post" class="form-horizontal" role="form">
        <div class="col-sm-12 mb20">
            <small>{{ $assess_info['explains'] }}</small>
        </div>
        <div class="col-sm-12">
            <div class="form-group col-sm-12">
                <label class="col-sm-2 control-label">
                    @if ('member' == $assess_info['target'])
                        @lang('common.by')@lang('common.grade')@lang('common.member')
                    @elseif ('member_group' == $assess_info['target'])
                        @lang('common.by')@lang('common.grade')@lang('common.member')@lang('common.group')
                    @endif
                    {{ mDate($assess_info['start_time']) }}
                    {{ mDate($assess_info['end_time']) }}
                </label>
                <div class="col-sm-6" id="re_grade_id">
                    <input type="hidden" name="re_grade_id"/>
                    <script type="text/javascript" src="{{ asset('js/M_select_add.js') }}"></script>
                    <script type="text/javascript">
                        $(function () {
                            var config = {
                                'edit_obj': $('#re_grade_id'),
                                'post_name': 're_grade_id',
                                'ajax_url': '{:M_U('ajax_api')}',
                                'field': @if ('member' == $assess_info['target'])
                                        'member'
                                @elseif ('member_group' == $assess_info['target'])
                                'member_group'
                                @endif
                            };
                            new M_select_add(config);
                        });
                    </script>
                </div>
            </div>
            <table class="table table-hover">
                <tr>
                    <th>@lang('common.project')</th>
                    <th>@lang('common.factor')</th>
                    <th>@lang('common.grade')</th>
                </tr>
                @foreach ($assess_info['ext_info'] as $row)
                    <tr>
                        <th>{{ $row['p'] }}</th>
                        <th>{{ $row['f'] }}</th>
                        <th><input class="w50" type="text" onKeyup="M_in_int_range(this,1,{{ $row['mg'] }});"
                                   name="score[]"/></th>
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="col-sm-12 text-center">
            <button type="submit" class="btn btn-info">
                @lang('common.submit')
            </button>
            <a href="{:M_U('index')}" class="btn btn-default">
                @lang('common.goback')
            </a>
        </div>
    </form>
@endsection