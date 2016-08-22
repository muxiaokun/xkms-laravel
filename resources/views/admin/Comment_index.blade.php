
    <import file="js/M_alert_log" />
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.comment') }}{{ trans('common.member') }}</th>
                        <th>{{ trans('common.comment') }}{{ trans('common.time') }}</th>
                        <th>{{ trans('common.audit') }}{{ trans('common.admin') }}</th>
                        <th>{{ trans('common.controller') }}</th>
                        <th>{{ trans('common.id') }}</th>
                        <th>{{ trans('common.comment') }} IP</th>
                        <td class="nowrap">
                            <if condition="$batch_handle['add']">
                                <a class="btn btn-xs btn-success" href="{:U('add')}">{{ trans('common.config') }}{{ trans('common.comment') }}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="comment_list" item="comment">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$comment.id}"/>
                                &nbsp;{$comment.id}
                            </td>
                            <td>
                                {$comment.member_name}
                            </td>
                            <td>
                                {$comment.add_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$comment.audit_name}
                            </td>
                            <td>
                                {$comment.controller}
                            </td>
                            <td>
                                {$comment.item}
                            </td>
                            <td>
                                {$comment.aip}
                            </td>
                            <td class="nowrap">
<a id="M_alert_log_{$comment.id}" class="btn btn-xs btn-primary" href="javascript:void(0);">{{ trans('common.look') }}</a>
                                <script>
                                    $(function(){
                                        var config = {
                                            'bind_obj':$('#M_alert_log_{$comment.id}'),
                                            'title':'{{ trans('common.comment') }}{{ trans('common.content') }}',
                                            'message':"{$comment.content}"
                                        }
                                        new M_alert_log(config);
                                    });
                                </script>
                                <if condition="$batch_handle['edit']">
                                    &nbsp;|&nbsp;
<a class="btn btn-xs btn-primary" href="{:U('edit',array('id'=>$comment['id']))}">
                                        {{ trans('common.audit') }}
                                    </a>
                                </if>
                                <if condition="$batch_handle['del']">
                                    &nbsp;|&nbsp;
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{{ trans('common.comment') }}?','{:U('del',array('id'=>$comment['id']))}')" >
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
                                <if condition="$batch_handle['edit']">
                                    config.type_data.push({'name':$Think.lang.audit,'post_link':'{:U('edit')}'});
                                </if>
                                <if condition="$batch_handle['del']">
                                    config.type_data.push({'name':$Think.lang.del,'post_link':'{:U('del')}' });
                                </if>
                                new M_batch_handle(config);
                            });
                        </script>
                        </if>
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="comment_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>