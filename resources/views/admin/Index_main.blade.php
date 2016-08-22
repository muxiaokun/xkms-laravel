    <section class="container">
        <div class="row">
            <div class="col-sm-12 page-header">
                <h1>{{ trans('common.welcome') }}{{ trans('common.use') }}{$Think.const.APP_NAME}</h1>
                <a href="{:U('Index/clean_cache')}" class="hidden" >{{ trans('common.clean') }}{{ trans('common.cache') }}</a>
            </div>
            <div class="col-sm-12 mb20 text-center quick_ico">
                <if condition="$site_info['ico']['ico1']">
                    <a href="{:U('Article/add')}" ><span class="mr20 ico1" title="{{ trans('common.add') }}{{ trans('common.article') }}"></span></a>
                </if>
                <if condition="$site_info['ico']['ico2']">
                    <a href="{:U('ArticleCategory/add')}" ><span class="mr20 ico2" title="{{ trans('common.add') }}{{ trans('common.article') }}{{ trans('common.category') }}"></span></a>
                </if>
                <if condition="$site_info['ico']['ico6']">
                    <a href="{:U('Member/add')}" ><span class="mr20 ico6" title="{{ trans('common.add') }}{{ trans('common.member') }}"></span></a>
                </if>
                <if condition="$site_info['ico']['ico7']">
                    <a href="{:U('MemberGroup/add')}" ><span class="mr20 ico7" title="{{ trans('common.add') }}{{ trans('common.member') }}{{ trans('common.group') }}"></span></a>
                </if>
                <if condition="$site_info['ico']['ico8']">
                    <a href="{:U('ManageUpload/index')}" ><span class="mr20 ico8" title="{{ trans('common.management') }}{{ trans('common.upload') }}{{ trans('common.file') }}"></span></a>
                </if>
                <if condition="$site_info['ico']['ico9']">
                    <a href="{:U('Index/clean_log')}" ><span class="mr20 ico9" title="{{ trans('common.clean') }}{{ trans('common.log') }}"></span></a>
                </if>
                <if condition="$site_info['ico']['ico10']">
                    <a href="{:U('Index/clean_cache')}" ><span class="mr20 ico10" title="{{ trans('common.clean') }}{{ trans('common.cache') }}"></span></a>
                </if>
                <if condition="$site_info['ico']['ico12']">
                    <a href="{:U('Index/database_set')}" ><span class="mr20 ico12" title="{{ trans('common.database') }}{{ trans('common.config') }}"></span></a>
                </if>
                <script type="text/javascript">M_jqueryui_tooltip('.quick_ico span')</script>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">{$Think.const.APP_NAME}动态</div>
                    <div class="panel-body">
                        <table class="table table-condensed table-hover">
                            <tr id="news_row" style="display:none">
                                <td><a href="javascript:void(0);" target="_blank">
                                        <span class="col-sm-9" mtype="title" style="overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"></span>
                                        <span class="col-sm-3" mtype="date"></span>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <if condition="!APP_DEBUG">
                    <script type="text/javascript">
                        //加载站异步的新闻
                        $(function(){
                            $.getJSON('http://www.xjhywh.cn/news_api.php?callback=?',function(data){
                                if(!data)return;
                                var t_obj = $('#news_row');
                                var tp_obj = t_obj.parent();
                                var max = 4;
                                $.each(data,function(k,v){
                                    if(k >= max)return;
                                    var new_obj = t_obj.clone();
                                    new_obj.find('[mtype=title]').html(v.title);
                                    new_obj.find('[mtype=date]').html(v.date);
                                    new_obj.find('a').attr('href',v.link);
                                    new_obj.show();
                                    tp_obj.append(new_obj);
                                });
                            });
                        });
                    </script>
                    </if>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('common.current') }}{{ trans('common.account') }}{{ trans('common.info') }}</div>
                    <div class="panel-body">
                        <table class="table table-condensed table-hover">
                            <tr>
                                <td>{{ trans('common.account') }}{{ trans('common.name') }}</td>
                                <td>{$Think.session.backend_info.admin_name}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('common.register') }}{{ trans('common.time') }}</td>
                                <td>{$Think.session.backend_info.add_time|M_date=C('SYS_DATE_DETAIL')}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('common.login') }}{{ trans('common.time') }}</td>
                                <td>{$Think.session.backend_info.last_time|M_date=C('SYS_DATE_DETAIL')}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('common.login') }}IP</td>
                                <td>{$Think.session.backend_info.aip|M_iptoadd}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('common.current') }}{{ trans('common.system') }}{{ trans('common.info') }}</div>
                    <div class="panel-body">
                        <table class="table table-condensed table-hover">
                            <tr>
                                <td class="col-sm-2">{{ trans('common.system') }}{{ trans('common.version') }}</td>
                                <td class="col-sm-4">{$site_info.sys_version}</td>
                                <td class="col-sm-2">{{ trans('common.system') }}{{ trans('common.timezone') }}</td>
                                <td class="col-sm-4">{$site_info.sys_timezone}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('common.server') }}IP</td>
                                <td>{$site_info.server_ip}</td>
                                <td>{{ trans('common.max') }}{{ trans('common.upload') }}{{ trans('common.limit') }}</td>
                                <td>{$site_info.max_upload_size}</td>
                            </tr>
                            <tr>
                                <td>PHP{{ trans('common.version') }}</td>
                                <td>{$site_info.php_version}</td>
                                <td>MySql{{ trans('common.version') }}</td>
                                <td>{$site_info.mysql_version}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('common.system') }}{{ trans('common.encode') }}</td>
                                <td>{$site_info.sys_encode}</td>
                                <td>MySql{{ trans('common.encode') }}</td>
                                <td>{$site_info.mysql_encode}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>