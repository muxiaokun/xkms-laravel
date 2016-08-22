
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <import file="js/M_valid" />
                <script>
                    $(function(){
                        var config = {
                            'form_obj':$('#form_valid'),
                            'check_list':{
                                'password':Array('password'),
                                'password_again':Array('password','password_again')
                            },
                            'ajax_url':"{:U('ajax_api')}",
                        };
                        new M_valid(config);
                    });
                </script>
                <form id="form_valid" onSubmit="return false;" class="form-horizontal" role="form" action="" method="post" >
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label">{$Think.lang.current}{$Think.lang.pass}</label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" placeholder="{$Think.lang.current}{$Think.lang.pass}" name="cur_password" value="{$edit_info.admin_name}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label">{$Think.lang.new}{$Think.lang.pass}</label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" placeholder="{$Think.lang.new}{$Think.lang.pass}" name="password" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label">{$Think.lang.again}{$Think.lang.input}{$Think.lang.pass}</label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" placeholder="{$Think.lang.again}{$Think.lang.input}{$Think.lang.pass}" name="password_again" />
                            <if condition="$edit_info">
                                <span class="help-block">{$Think.lang.not_input_pass}</span>
                            </if>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                {$Think.lang.save}
                            </button>
                            <a href="{:U('Index/main')}" class="btn btn-default">
                                {$Think.lang.goback}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
