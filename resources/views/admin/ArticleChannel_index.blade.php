
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.channel') }}{{ trans('common.name') }}</th>
                        <th>{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.show') }}</th>
                        <th>{{ trans('common.channel') }}{{ trans('common.template') }}</th>
                        <td class="col-sm-2 nowrap">
                            <if condition="$batch_handle['add']">
                                <a class="btn btn-xs btn-success" href="{{ route('add') }}">{{ trans('common.add') }}{{ trans('common.channel') }}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="article_channel_list" item="article_channel">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$article_channel.id}"/>
                                &nbsp;{$article_channel.id}
                            </td>
                            <td>
                                {$article_channel.name}
                            </td>
                            <td>
                                <if condition="$article_channel['if_show']">{{ trans('common.show') }}<else />{{ trans('common.hidden') }}</if>
                            </td>
                            <td>
                                <if condition="$article_channel['template']">{$article_channel.template}<else />{{ trans('common.default') }}</if>
                            </td>
                            <td class="nowrap">
                                <a class="btn btn-xs btn-primary" target="_blank" href="{{ route('Home/Article/channel',array('channel_id'=>$article_channel['id'])) }}">
                                    {{ trans('common.look') }}
                                </a>
                                <if condition="$batch_handle['edit']">
                                    &nbsp;|&nbsp;
                                    <a class="btn btn-xs btn-primary" href="{{ route('edit',array('id'=>$article_channel['id'])) }}">
                                        {{ trans('common.edit') }}
                                    </a>
                                </if>
                                <if condition="$batch_handle['del']">
                                    &nbsp;|&nbsp;
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{$article_channel.name}?','{{ route('del',array('id'=>$article_channel['id'])) }}')" >
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
                                    config.type_data.push({'name':$Think.lang.show,'post_link':'{{ route('edit') }}','post_data':{'if_show':'1'} });
                                    config.type_data.push({'name':$Think.lang.hidden,'post_link':'{{ route('edit') }}','post_data':{'if_show':'0'} });
                                </if>
                                <if condition="$batch_handle['del']">
                                    config.type_data.push({'name':$Think.lang.del,'post_link':'{{ route('del') }}' });
                                </if>
                                new M_batch_handle(config);
                            });
                        </script>
                        </if>
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="article_channel_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>