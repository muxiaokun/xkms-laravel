        <script type="text/javascript">
            $(function(){
                if(parent && parent.move_progress)
                {
                    parent.move_progress({:C('setp_progress.1')});
                }
            });
        </script>
        {/*<!--安装第一步界面 开始-->*/}
        <section class="container">
            <div class="row">
                <div class="col-sm-12">
                <form id="mysql_config" class="form-horizontal" action="">
                    <div class="form-group">
                        <label class="col-sm-1 control-label">{{ trans('common.host') }}{{ trans('common.colon') }}</label>
                        <div class="col-sm-5"><input type="text" class="form-control"  placeholder="{{ trans('common.host') }}" value="{$default_config.DB_HOST}" name="host"></div>
                        <label class="col-sm-1 control-label">{{ trans('common.database') }}{{ trans('common.colon') }}</label>
                        <div class="col-sm-3"><input type="text" class="form-control"  placeholder="{{ trans('common.database') }}" value="{$default_config.DB_NAME}" name="name"></div>
                        <div class="btn-group col-sm-2">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                {{ trans('common.selection') }}{{ trans('common.exists') }}{{ trans('common.database') }}<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <foreach name="database_list" item="data">
                                    <li class="text-left"><a href="#" onclick="$('input[name=name]').val($(this).html())">{{ $data }}</a></li>
                                </foreach>
                            </ul>
                          </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-1 control-label">{{ trans('common.user') }}{{ trans('common.colon') }}</label>
                        <div class="col-sm-5"><input type="text" class="form-control"  placeholder="{{ trans('common.user') }}" value="{$default_config.DB_USER}" name="user"></div>
                        <label class="col-sm-1 control-label">{{ trans('common.pass') }}{{ trans('common.colon') }}</label>
                        <div class="col-sm-5"><input type="password" class="form-control"  placeholder="{{ trans('common.pass') }}" value="{$default_config.DB_PWD}" name="pass"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-1 control-label">{{ trans('common.port') }}{{ trans('common.colon') }}</label>
                        <div class="col-sm-5"><input type="text" class="form-control"  placeholder="{{ trans('common.port') }}" value="{$default_config.DB_PORT}" name="port"></div>
                        <label class="col-sm-1 control-label">{{ trans('common.prefix') }}{{ trans('common.colon') }}</label>
                        <div class="col-sm-5"><input type="text" class="form-control"  placeholder="{{ trans('common.prefix') }}" value="{$default_config.DB_PREFIX}" name="prefix"></div>
                    </div>
                </form>
                </div>
                <div class="col-sm-12 text-center">
                    <a class="btn btn-lg btn-primary mt20 mr80" href="{:U('')}">{{ trans('common.previous') }}{{ trans('common.setp') }}</a>
                    <a id="mysql_config_btn" class="btn btn-lg btn-primary mt20" href="javascript:void(0);">{{ trans('common.setp1') }}</a>
                    <script type="text/javascript">
                        var config = {
                            'out_obj':'#mysql_config_btn',
                            'edit_obj':'#mysql_config',
                            'next_link':'{:U('setp2')}',
                            'ajax_url':'{:U('ajax_api')}'
                        }
                        new M_check_mysql(config);
                    </script>
                </div>
                <div class="col-sm-12 text-center"><div id="show_box" class="mt20"></div></div>
                <script type="text/javascript">
                    <foreach name="note" item="data">
                    show_install_message("#show_box","{{ $data }}{{ trans('common.extend') }}{{ trans('common.none') }}{{ trans('common.loading') }}","warning")
                    </foreach>
                </script>
            </div>
        </section>
        {/*<!--安装第一步界面 结束-->*/}