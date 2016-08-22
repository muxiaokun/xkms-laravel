
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
            <form method="post" class="form-horizontal"  role="form">
                <input type="hidden" name="id" value="{$edit_info.id}"/>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.send}{$Think.lang.to}</label>
                            <div class="col-sm-7">
                                <input type="text" value="{$edit_info.member_name}" class="form-control" disabled="disabled">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.start}{$Think.lang.content}</label>
                            <div class="col-sm-7">
                                <input type="text" name="start_content" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.start}{$Think.lang.content}{$Think.lang.color}</label>
                            <div class="col-sm-7">
                                <input type="text" name="start_content_color" value="#000000" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.end}{$Think.lang.content}</label>
                            <div class="col-sm-7">
                                <input type="text" name="end_content" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.end}{$Think.lang.content}{$Think.lang.color}</label>
                            <div class="col-sm-7">
                                <input type="text" name="end_content_color" value="#000000" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.content}1</label>
                            <div class="col-sm-7">
                                <input type="text" name="content1" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.content}1{$Think.lang.color}</label>
                            <div class="col-sm-7">
                                <input type="text" name="content1_color" value="#000000" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.content}2</label>
                            <div class="col-sm-7">
                                <input type="text" name="content2" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.content}2{$Think.lang.color}</label>
                            <div class="col-sm-7">
                                <input type="text" name="content2_color" value="#000000" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-info">{$Think.lang.send}</button>
                        <input class="btn btn-default" type="reset" value="{$Think.lang.reset}">
                        <a href="{:U('index')}" class="btn btn-default">
                                {$Think.lang.goback}
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </section>