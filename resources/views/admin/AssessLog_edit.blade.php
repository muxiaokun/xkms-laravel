
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ $title }}
                @if ($batch_handle['del'])
<a class="fr fs10" href="javascript:void();" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{{ trans('common.all') }}{{ trans('common.record') }}?','{{ route('del',array('id'=>I('id'))) }}')">
                        {{ trans('common.del') }}{{ trans('common.all') }}{{ trans('common.record') }}
                    </a>
                @endif
            </div>
            <div class="panel-body">
                <h1>{$assess_info.title}</h1>
                <form method="post" class="form-horizontal" role="form">
                    <div class="form-group col-sm-12">
                        <label class="col-sm-2 control-label">
                            @if ('member' eq $assess_info['target'])
                                {{ trans('common.by') }}{{ trans('common.grade') }}{{ trans('common.member') }}
                            @elseif ('member_group' eq $assess_info['target'])
                                {{ trans('common.by') }}{{ trans('common.grade') }}{{ trans('common.member') }}{{ trans('common.group') }}
                            @endif
                        </label>
                        <div class="col-sm-6" id="re_grade_id">
                            <input type="hidden" name="re_grade_id" />
                            <script type="text/javascript" src="{{ asset('js/M_select_add.js') }}"></script>
                            <script type="text/javascript">
                                $(function(){
                                    var config = {
                                        'edit_obj':$('#re_grade_id'),
                                        'post_name':'re_grade_id',
                                        'ajax_url':'{{ route('ajax_api') }}',
                                        'field':
                                                @if ('member' eq $assess_info['target'])
                                                    'member'
                                                @elseif ('member_group' eq $assess_info['target'])
                                                    'member_group'
                                                @endif
                                    };
                                    new M_select_add(config);
                                });
                            </script>
                        </div>
                    </div>
                    <div class="cb"></div>
                    {/* 投票给分组 */}
                    <table class="table table-hover text-center">
                        {/* 评分给分组 */}
                        @if ($assess_info.result_info)
                            <tr>
                                <th>{{ trans('common.project') }}</th>
                                <th>{{ trans('common.factor') }}</th>
                                <th>{{ trans('common.grade') }}</th>
                            </tr>
                            @foreach ($assess_info['result_info'] as $result)
                                <tr>
                                    <td>{$result.p}</td><td>{$result.f}</td><td>{$result.g}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="2"></td><td>SUM: {$assess_info.all_grade}</td>
                            </tr>
                        @else
                            <tr>
                                <td>{{ trans('common.please') }}{{ trans('common.selection') }}
                                    @if ('member' eq $assess_info['target'])
                                        {{ trans('common.member') }}
                                    @elseif ('member_group' eq $assess_info['target'])
                                        {{ trans('common.member_group') }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </table>
                    <div class="row mt10">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                    {{ trans('common.select') }}
                            </button>
                            <a href="{{ route('Assess/index') }}" class="btn btn-default">
                                    {{ trans('common.goback') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>