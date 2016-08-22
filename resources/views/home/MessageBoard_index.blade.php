            <section class="container">
                <div class="row">
                    <form class="form-horizontal" action="{:M_U('add')}" method="post">
                        <input type="hidden" name="id" value="{$message_board_info.id}"/>
                        <div class="col-sm-12">
                            <foreach name="message_board_info.config" item="data">
                            <div class="form-group">
                                <label class="control-label col-sm-4">{$data.msg_name}{$Think.lang.colon}</label>
                                <div class="col-sm-4">
                                <if condition="'text' eq $data['msg_type']">
                                    <input type="text" name="send_info[{$data.msg_name}]" class="form-control"/>
                                <elseif condition="'radio' eq $data['msg_type']" />
                                    <foreach name="data.msg_option" item="msg_option_data">
                                        <label class="control-label checkbox checkbox-inline">
                                            <input type="radio" name="send_info[{$data.msg_name}]" value="{$msg_option_data}"/>
                                            {$msg_option_data}
                                        </label>
                                    </foreach>
                                <elseif condition="'checkbox' eq $data['msg_type']" />
                                    <foreach name="data.msg_option" item="msg_option_data">
                                        <label class="control-label checkbox checkbox-inline">
                                            <input type="checkbox" name="send_info[{$data.msg_name}][]" value="{$msg_option_data}"/>
                                            {$msg_option_data}
                                        </label>
                                    </foreach>
                                <elseif condition="'textarea' eq $data['msg_type']" />
                                    <textarea name="send_info[{$data.msg_name}]" rows="5" class="form-control" style="resize: none;" ></textarea>
                                </if>
                                </div>
                                <div class="col-sm-4">
                                    <if condition="$data['msg_required']">{$Think.lang.required}</if>
                                    <if condition="$data['msg_length']">{$Think.lang.max}{$Think.lang.length}{$data.msg_length}</if>
                                </div>
                            </div>
                            </foreach>
                            <if condition="C('SYS_FRONTEND_VERIFY')">
                            <div class="form-group">
                                <label class="control-label col-sm-4">验证码：</label>
                                <div class="col-sm-4">
                                    <input type="text" name="verify" class="form-control" style="text-transform:uppercase;"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <img class="col-sm-offset-4 col-sm-2" src="{:M_U('Home/MessageBoard/verify_img')}" onClick="M_change_verify(this,$('input[name=verify]'))" />
                            </div>
                            </if>
                        </div>
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                    {$Think.lang.send}
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-sm-12">
                    <table class="table table-hover">
                        <foreach name="message_board_log_list" item="message_board_log">
                            <tr><td>
                            <table class="table table-hover">
                                <tr>
                                    <td width="10%">{$Think.lang.send}{$Think.lang.time}{$Think.lang.colon}</td>
                                    <td width="90%">{$message_board_log.add_time|M_date=C('SYS_DATE_DETAIL')}</td>
                                </tr>
                                <foreach name="message_board_log.send_info" key="name" item="value">
                                <tr>
                                    <td>{$name}</td>
                                    <td>{$value}</td>
                                </tr>
                                </foreach>
                                <tr>
                                    <td>{$Think.lang.reply}{$Think.lang.content}{$Think.lang.colon}</td>
                                    <td>{$message_board_log.reply_info}</td>
                                </tr>
                            </table>
                        </foreach>
                        </td></tr>
                    </table>
                    <M:Page name="message_board_log_list">
                        <table class="table"><tr><td class="text-right">
                            <config></config>
                        </td></tr></table>
                    </M:Page>
                </div>
            </section>