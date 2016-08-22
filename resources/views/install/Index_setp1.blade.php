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
                        <label class="col-sm-1 control-label">{$Think.lang.host}{$Think.lang.colon}</label>
                        <div class="col-sm-5"><input type="text" class="form-control"  placeholder="{$Think.lang.host}" value="{$default_config.DB_HOST}" name="host"></div>
                        <label class="col-sm-1 control-label">{$Think.lang.database}{$Think.lang.colon}</label>
                        <div class="col-sm-3"><input type="text" class="form-control"  placeholder="{$Think.lang.database}" value="{$default_config.DB_NAME}" name="name"></div>
                        <div class="btn-group col-sm-2">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                {$Think.lang.selection}{$Think.lang.exists}{$Think.lang.database}<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <foreach name="database_list" item="data">
                                    <li class="text-left"><a href="#" onclick="$('input[name=name]').val($(this).html())">{$data}</a></li>
                                </foreach>
                            </ul>
                          </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-1 control-label">{$Think.lang.user}{$Think.lang.colon}</label>
                        <div class="col-sm-5"><input type="text" class="form-control"  placeholder="{$Think.lang.user}" value="{$default_config.DB_USER}" name="user"></div>
                        <label class="col-sm-1 control-label">{$Think.lang.pass}{$Think.lang.colon}</label>
                        <div class="col-sm-5"><input type="password" class="form-control"  placeholder="{$Think.lang.pass}" value="{$default_config.DB_PWD}" name="pass"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-1 control-label">{$Think.lang.port}{$Think.lang.colon}</label>
                        <div class="col-sm-5"><input type="text" class="form-control"  placeholder="{$Think.lang.port}" value="{$default_config.DB_PORT}" name="port"></div>
                        <label class="col-sm-1 control-label">{$Think.lang.prefix}{$Think.lang.colon}</label>
                        <div class="col-sm-5"><input type="text" class="form-control"  placeholder="{$Think.lang.prefix}" value="{$default_config.DB_PREFIX}" name="prefix"></div>
                    </div>
                </form>
                </div>
                <div class="col-sm-12 text-center">
                    <a class="btn btn-lg btn-primary mt20 mr80" href="{:U('')}">{$Think.lang.previous}{$Think.lang.setp}</a>
                    <a id="mysql_config_btn" class="btn btn-lg btn-primary mt20" href="javascript:void(0);">{$Think.lang.setp1}</a>
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
                    show_install_message("#show_box","{$data}{$Think.lang.extend}{$Think.lang.none}{$Think.lang.loading}","warning")
                    </foreach>
                </script>
            </div>
        </section>
        {/*<!--安装第一步界面 结束-->*/}