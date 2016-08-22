
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" action="" method="post">
                    <input type="hidden" name="id" value="{$edit_info.id}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.receive}{$Think.lang.member}</label>
                                <div class="col-sm-8" id="receive_member_list">
                                    <input type="hidden" name="receive_id" />
                                    <import file="js/M_select_add" />
                                    <script type="text/javascript">
                                        $(function(){
                                            var config = {
                                                <if condition="$receive_info['id']">'def_data':{'value':'{$receive_info.id}','html':'{$receive_info.member_name}'},</if>
                                                'edit_obj':$('#receive_member_list'),
                                                'post_name':'receive_id',
                                                'ajax_url':'{:U('ajax_api')}',
                                                'field':'receive_id'
                                            };
                                            new M_select_add(config);
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">{$Think.lang.send}{$Think.lang.content}</div>
                        <div class="col-sm-12">
                            <textarea rows="5" class="col-sm-12" name="content">{$edit_info.reply_info}</textarea>
                        </div>
                    </div>
                    <div class="row mt10">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                    {$Think.lang.send}
                            </button>
                            <a href="{:U('index')}" class="btn btn-default">
                                    {$Think.lang.goback}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
