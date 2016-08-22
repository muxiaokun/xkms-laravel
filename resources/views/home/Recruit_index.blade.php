                <section class="container">
                    <div class="col-sm-12 text-right mb10">
                        <form class="form-inline" role="form" method="get">
                            {$Think.lang.recruit}{$Think.lang.keywords}
                            <input type="text" name="keyword" class="form-control w100 mlr10" value="{:I('keyword')}" onClick="$(this).val('')" />
                            <button class="btn btn-default" type="submit">
                                {$Think.lang.search}{$Think.lang.recruit}
                            </button>
                        </form>
                    </div>
                    <div class="col-sm-12">
                        <table class="table table-condensed table-hover">
                            <tr>
                                <th>{$Think.lang.recruit}{$Think.lang.name}</th>
                                <th>{$Think.lang.re_recruit}{$Think.lang.number}</th>
                                <th>{$Think.lang.recruit}{$Think.lang.time}</th>
                                <th>{$Think.lang.description}</th>
                                <th></th>
                            </tr>
                            <foreach name="recruit_list" item="recruit">
                                <tr>
                                    <td>
                                        {$recruit.title}
                                    </td>
                                    <td>
                                        {$recruit.current_portion}/{$recruit.max_portion}
                                    </td>
                                    <td>
                                        {$recruit.start_time|M_date=C('SYS_DATE_DETAIL')}
                                        {$Think.lang.to}
                                        {$recruit.end_time|M_date=C('SYS_DATE_DETAIL')}
                                    </td>
                                    <td>
                                        {$recruit.explains|strip_tags|M_substr=30}
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btn-xs" href="{:M_U('Recruit/edit',array('id'=>$recruit['id']))}">
                                            {$Think.lang.look}{$Think.lang.recruit}{$Think.lang.info}
                                        </a>
                                    </td>
                                </tr>
                            </foreach>
                        </table>
                    </div>
                    <M:Page name="recruit_list">
                        <table class="table"><tr><td class="text-right">
                            <config></config>
                        </td></tr></table>
                    </M:Page>
                </section>
  