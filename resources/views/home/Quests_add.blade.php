<extend name="Member:base" />
<block name="content">
    <script type="text/javascript">
        function answer_submit()
        {
            <foreach name="quests_quest_list" key="quest_id" item="quest">
            <if condition="$quest['required']">
                <switch name="quest['answer_type']">
                    <case value="radio">
                        obj_str="[name='quests[{$quest_id}]']:checked";
                    </case>
                    <case value="checkbox">
                        obj_str="[name='quests[{$quest_id}][]']:checked";
                    </case>
                    <case value="textarea">
                        obj_str="[name='quests[{$quest_id}]']";
                    </case>
                </switch>
                if(!$(obj_str).val())
                {
                    window.location.hash = "quests{$quest_id}";
                    return false;
                }
            </if>
            </foreach>
            return true;
        }
    </script>
    <div class="col-sm-12">
        {$quests_info.start_content}
    </div>
    <div class="col-sm-12">
        <form id="answer_form" onSubmit="return answer_submit();" method="post">
            <input type="hidden" name="id" value="{$quests_info.id}"/>
            <input type="hidden" name="access_info" value="{$quests_info.access_info}"/>
            <foreach name="quests_quest_list" key="quest_id" item="quest">
                <div class="col-sm-12 form-group mt20 cb">
                    <a name="quests{$quest_id}"></a>
                    <label >
                        <h4>{$quest.question}
                        <if condition="$quest['required']">
                            <span class="ml20" style="color:#ff0000">({$Think.lang.required})</span>
                        </if>
                        </h4>
                    </label>
                    <span class="help-block">{$quest.explains}</span>
                    <div>
                        <switch name="quest['answer_type']">
                            <case value="radio">
                                <foreach name="quest['answer']" item="info">
                                    <div class="col-sm-2">
                                        <label class="checkbox-inline">
                                            <input type="radio" name="quests[{$quest_id}]" value="{$key}" />{$info}
                                        </label>
                                    </div>
                                </foreach>
                            </case>
                            <case value="checkbox">
                                <foreach name="quest['answer']" item="info">
                                    <div class="col-sm-2">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="quests[{$quest_id}][]" value="{$key}" />{$info}
                                        </label>
                                    </div>
                                </foreach>
                            </case>
                            <case value="text">
                                <input class="form-control" type="text" name="quests[{$quest_id}]" />
                            </case>
                            <case value="textarea">
                                <textarea name="quests[{$quest_id}]" style="width:100%" row="3"></textarea>
                            </case>
                        </switch>
                    </div>
                </div>
            </foreach>
            <!-- 隐藏传参 -->
            <div class="row">
                <div class="col-sm-12 text-center">
                    <button class="col-sm-offset-5 col-sm-1 btn btn-info" type="submit">{$Think.lang.submit}</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-sm-12">
        {$quests_info.end_content}
    </div>
</block>