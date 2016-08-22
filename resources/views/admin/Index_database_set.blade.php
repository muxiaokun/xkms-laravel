
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
            <form method="post" class="form-horizontal"  role="form" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.host}</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_HOST" value="{:C('DB_HOST')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.database}</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_NAME" value="{:C('DB_NAME')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.user}</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_USER" value="{:C('DB_USER')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.pass}</label>
                            <div class="col-sm-5">
                                <input type="password" name="DB_PWD" class="form-control">
                            </div>
                            <if condition="0 lt strlen(C('DB_PWD'))">
                                <div class="col-sm-4" style="color:green;">{$Think.lang.exists}{$Think.lang.pass}</div>
                            </if>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.port}</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_PORT" value="{:C('DB_PORT')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.prefix}</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_PREFIX" value="{:C('DB_PREFIX')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.backup}{$Think.lang.database}</label>
                            <div class="col-sm-7">
<a class="btn btn-sm btn-success" href="{:U('database_set',array('backup'=>'1'))}" target="_blank">{$Think.lang.download}{$Think.lang.database}{$Think.lang.backup}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.restore}{$Think.lang.database}</label>
                            <div class="col-sm-7">
                                <input type="file" name="restore_file" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-offset-5">
                        <button type="submit" class="btn btn-info">{$Think.lang.save}</button>
                        <input class="btn btn-default" type="reset" value="{$Think.lang.reset}">
                        <a href="{:U('main')}" class="btn btn-default">
                            {$Think.lang.goback}
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </section>