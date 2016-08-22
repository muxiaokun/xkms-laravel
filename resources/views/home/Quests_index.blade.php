<extend name="Member:base" />
<block name="content">
    <table class="table table-condensed table-hover">
        <tr>
            <th>{{ trans('common.id') }}</th>
            <th>{{ trans('common.title') }}</th>
            <th>{{ trans('common.start') }}{{ trans('common.time') }}</th>
            <th>{{ trans('common.end') }}{{ trans('common.time') }}</th>
            <th>{{ trans('common.access') }}{{ trans('common.info') }}</th>
        </tr>
        <foreach name="quests_list" item="quests">
            <tr>
                <td>
                    {$quests.id}
                </td>
                <td>
                    {$quests.title}
                </td>
                <td>
                    {$quests.start_time|M_date=C('SYS_DATE_DETAIL')}
                </td>
                <td>
                    {$quests.end_time|M_date=C('SYS_DATE_DETAIL')}
                </td>
                <td>
                    <if condition="$quests['access_info']">
                        <form action="{:M_U('Quests/add',array('id'=>$quests['id']))}" method="get">
                            <input type="text" name="access_info" />
                            <button class="btn btn-default btn-sm">{{ trans('common.confirm') }}{{ trans('common.pass') }}</button>
                        </form>
                    <else/>
                        <a href="{:M_U('Quests/add',array('id'=>$quests['id']))}">{{ trans('common.public') }}{{ trans('common.access') }}</a>
                    </if>
                </td>
            </tr>
        </foreach>
    </table>
    <M:Page name="quests_list">
        <table class="table"><tr><td class="text-right">
            <config></config>
        </td></tr></table>
    </M:Page>
</block>