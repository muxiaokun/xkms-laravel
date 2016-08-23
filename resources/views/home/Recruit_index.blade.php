                <section class="container">
                    <div class="col-sm-12 text-right mb10">
                        <form class="form-inline" role="form" method="get">
                            {{ trans('common.recruit') }}{{ trans('common.keywords') }}
                            <input type="text" name="keyword" class="form-control w100 mlr10" value="{:I('keyword')}" onClick="$(this).val('')" />
                            <button class="btn btn-default" type="submit">
                                {{ trans('common.search') }}{{ trans('common.recruit') }}
                            </button>
                        </form>
                    </div>
                    <div class="col-sm-12">
                        <table class="table table-condensed table-hover">
                            <tr>
                                <th>{{ trans('common.recruit') }}{{ trans('common.name') }}</th>
                                <th>{{ trans('common.re_recruit') }}{{ trans('common.number') }}</th>
                                <th>{{ trans('common.recruit') }}{{ trans('common.time') }}</th>
                                <th>{{ trans('common.description') }}</th>
                                <th></th>
                            </tr>
                            @foreach ($recruit_list as $recruit)
                                <tr>
                                    <td>
                                        {{ $recruit['title'] }}
                                    </td>
                                    <td>
                                        {{ $recruit['current_portion'] }}/{{ $recruit['max_portion'] }}
                                    </td>
                                    <td>
                                        {{ $recruit['start_time']|M_date=C('SYS_DATE_DETAIL') }}
                                        {{ trans('common.to') }}
                                        {{ $recruit['end_time']|M_date=C('SYS_DATE_DETAIL') }}
                                    </td>
                                    <td>
                                        {{ $recruit['explains']|strip_tags|M_substr=30 }}
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btn-xs" href="{:M_U('Recruit/edit',array('id'=>$recruit['id']))}">
                                            {{ trans('common.look') }}{{ trans('common.recruit') }}{{ trans('common.info') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <M:Page name="recruit_list">
                        <table class="table"><tr><td class="text-right">
                            <config></config>
                        </td></tr></table>
                    </M:Page>
                </section>
  