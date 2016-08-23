{__NOLAYOUT__}
        <if condition="IS_AJAX AND C('COMMENT_SWITCH')">{//下一行的div只是ajax获取元素时的外壳}
            <div>
                <div class="col-sm-12">
                    <M:Page name="comment_list" ><config></config></M:Page>
                </div>
                <div class="col-sm-12">
                    <table class="table">
                        <foreach name="comment_list" item="data">
                        <tr>
                            <td>{$data.member_name}{{ trans('common.grade') }}{{ trans('common.colon') }}{$data.level}[{$data.add_time|M_date=C('SYS_DATE_DETAIL')}]</td>
                        </tr>
                        <tr>
                            <td>{$data.content}</td>
                        </tr>
                        </foreach>
                    </table>
                </div>
            </div>
        <elseif condiction=" C('COMMENT_SWITCH')">
            <div class="col-sm-12">
            <hr />
                <import file="js/M_comment_editor" />
                <script type="text/javascript">
                    var M_comment_editor;
                    $(function(){
                        var config = {
                            'main_obj':$('#comment_index'),
                            'ajax_url':'{:M_U('Comment/ajax_api')}',
                            'controller':'[controller]',
                            'item':'[item]'
                        };
                        M_comment_editor = new M_comment_editor(config);
                    });
                </script>
                <form onsubmit="M_comment_editor.put_data(this);return false;">
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{ trans('common.grade') }}</label>
                        <div class="radio">
                            <for start="1" end="6">
                                <label class="mr100">
                                    <input type="radio" name="comment_level" value="{{ $i }}" <if condition="$i eq 5">checked="checked"</if> />{{ $i }}
                                </label>
                            </for>
                        </div>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">{{ trans('common.comment') }}{{ trans('common.content') }}</label>
                      <textarea name="comment_content" class="form-control" style="resize:none;"></textarea>
                    </div>
                    <button type="submit">{{ trans('common.submit') }}</button><span class="mlr20" style="color:red;"></span>
                </form>
            </div>
            <div id="comment_index" class="row"></div>
        </if>