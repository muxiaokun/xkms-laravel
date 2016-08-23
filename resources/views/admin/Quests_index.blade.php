
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.title') }}</th>
                        <th>{{ trans('common.portion') }}</th>
                        <th>{{ trans('common.start') }}{{ trans('common.time') }}</th>
                        <th>{{ trans('common.end') }}{{ trans('common.time') }}</th>
                        <th>{{ trans('common.access') }}{{ trans('common.pass') }}</th>
                        <td class="nowrap">
                            <if condition="$batch_handle['add']">
                                <a class="btn btn-xs btn-success" href="{{ route('add') }}">{{ trans('common.add') }}{{ trans('common.quests') }}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="quests_list" item="quests">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$quests.id}"/>
                                &nbsp;{$quests.id}
                            </td>
                            <td>
                                {$quests.title}
                            </td>
                            <td>
                                {$quests.current_portion}/{$quests.max_portion}
                            </td>
                            <td>
                                {$quests.start_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$quests.end_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$quests.access_info}
                            </td>
                            <td class="nowrap">
                                <if condition="$batch_handle['answer_index']">
                                    <a class="btn btn-xs btn-primary" href="{{ route('QuestsAnswer/index',array('quests_id'=>$quests['id'])) }}">
                                        {{ trans('common.answer') }}{{ trans('common.list') }}
                                    </a>
                                </if>
                                <if condition="$batch_handle['answer_index'] AND $batch_handle['answer_edit']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['answer_edit']">
                                    <a class="btn btn-xs btn-primary" href="{{ route('QuestsAnswer/edit',array('quests_id'=>$quests['id'])) }}">
                                        {{ trans('common.statistics') }}{{ trans('common.quests') }}
                                    </a>
                                </if>
                                <if condition="$batch_handle['answer_edit'] AND $batch_handle['edit']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['edit']">
                                    <a class="btn btn-xs btn-primary" href="{{ route('edit',array('id'=>$quests['id'])) }}">
                                        {{ trans('common.edit') }}
                                    </a>
                                </if>
                                <if condition="$batch_handle['edit'] AND $batch_handle['del']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['del']">
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.clear') }}{$quests.title}{{ trans('common.answer') }}?',
'{{ route('del',array('id'=>$quests['id'],'clear'=>1)) }}')" >
                                        {{ trans('common.clear') }}{{ trans('common.answer') }}
                                    </a>
                                    &nbsp;|&nbsp;
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{$quests.title}?','{{ route('del',array('id'=>$quests['id'])) }}')" >
                                        {{ trans('common.del') }}
                                    </a>
                                </if>
                            </td>
                        </tr>
                    </foreach>
                </table>
                <div class="row">
                    <div id="batch_handle"  class="col-sm-4 pagination">
                        <if condition="$batch_handle['del']">
                        <import file="js/M_batch_handle" />
                        <script type="text/javascript">
                            $(function(){
                                var config = {
                                    'out_obj':$('#batch_handle'),
                                    'post_obj':'input[name="id"]',
                                    'type_data':Array()
                                };
                                <if condition="$batch_handle['del']">
                                    config.type_data.push({'name':$Think.lang.del,'post_link':'{{ route('del') }}' });
                                </if>
                                new M_batch_handle(config);
                            });
                        </script>
                        </if>
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="quests_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
