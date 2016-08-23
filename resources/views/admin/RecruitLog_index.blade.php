
    <import file="js/M_alert_log" />
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.re_recruit') }}</th>
                        <th>{{ trans('common.recruit_name') }}</th>
                        <th>{{ trans('common.recruit') }}{{ trans('common.time') }}</th>
                        <th>{{ trans('common.recruit_birthday') }}</th>
                        <th>{{ trans('common.recruit_sex') }}</th>
                        <th>{{ trans('common.recruit_certificate') }}</th>
                        <th>{{ trans('common.handle') }}</th>
                    </tr>
                    <foreach name="recruit_log_list" item="recruit_log">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$recruit_log.id}"/>
                                &nbsp;{$recruit_log.id}
                            </td>
                            <td>
                                {$recruit_log.recruit_title}
                            </td>
                            <td>
                                {$recruit_log.name}
                            </td>
                            <td>
                                {$recruit_log.add_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$recruit_log.birthday|M_date=C('SYS_DATE')}
                            </td>
                            <td>
                                {$recruit_log.sex}
                            </td>
                            <td>
                                {$recruit_log.certificate}
                            </td>
                            <td class="nowrap">
<a id="M_alert_log_{$recruit_log.id}" class="btn btn-xs btn-primary" href="javascript:void(0);">{{ trans('common.look') }}</a>
                                <script>
                                    $(function(){
                                        var config = {
                                            'bind_obj':$('#M_alert_log_{$recruit_log.id}'),
                                            'title':'{{ trans('common.recruit_log') }}',
                                            'message':{$recruit_log.ext_info|json_encode}
                                        }
                                        new M_alert_log(config);
                                    });
                                </script>
                                <if condition="$batch_handle['del']">
                                    &nbsp;|&nbsp;
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{$recruit_log.name}?','{{ route('del',array('id'=>$recruit_log['id'])) }}')" >
                                        {{ trans('common.del') }}
                                    </a>
                                </if>
                            </td>
                        </tr>
                    </foreach>
                </table>
                <div class="row">
                    <div id="batch_handle"  class="col-sm-4 pagination">
                        <if condition="$batch_handle['edit'] OR $batch_handle['del']">
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
                        <M:Page name="recruit_log_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>