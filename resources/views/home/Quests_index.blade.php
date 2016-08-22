<extend name="Member:base" />
<block name="content">
    <table class="table table-condensed table-hover">
        <tr>
            <th>{$Think.lang.id}</th>
            <th>{$Think.lang.title}</th>
            <th>{$Think.lang.start}{$Think.lang.time}</th>
            <th>{$Think.lang.end}{$Think.lang.time}</th>
            <th>{$Think.lang.access}{$Think.lang.info}</th>
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
                            <button class="btn btn-default btn-sm">{$Think.lang.confirm}{$Think.lang.pass}</button>
                        </form>
                    <else/>
                        <a href="{:M_U('Quests/add',array('id'=>$quests['id']))}">{$Think.lang.public}{$Think.lang.access}</a>
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