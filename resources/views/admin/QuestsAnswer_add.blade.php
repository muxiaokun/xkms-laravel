    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ $title }}<a href="javascript:window.print()">打印</a>
                <a class="fr fs10" href="{{ route('index',array('quests_id'=>$quests_info['id'])) }}">{{ trans('common.goback') }}</a>
            </div>
            <div class="panel-body">
                <foreach name="quests_quest_list" key="quest_id" item="quest">
                    <div class="col-sm-12">
                        <div class="form-group mt20 cb">
                            <a name="quests{{ $quest_id }}"></a>
                            <label >
                                <h4>{$quest.question}
                                <if condition="$quest['required']">
                                    <span class="ml20" style="color:#ff0000">({{ trans('common.required') }})</span>
                                </if>
                                </h4>
                            </label>
                            <span class="mt10 help-block">{$quest.explains}</span>
                            <div>
                                <switch name="quest['answer_type']">
                                    <case value="radio">
                                        <foreach name="quest['answer']" item="info">
                                            <div class="col-sm-2">
<input type="radio" name="quests[{{ $quest_id }}]" value="{{ $key }}" <if condition="in_array($key,$quests_answer_list[$quest_id])" >checked="checked"</if> disabled="disabled" />
                                                {{ $info }}
                                            </div>
                                        </foreach>
                                    </case>
                                    <case value="checkbox">
                                        <foreach name="quest['answer']" item="info">
                                            <div class="col-sm-2">
<input type="checkbox" name="quests[{{ $quest_id }}][]" value="{{ $key }}" <if condition="in_array($key,$quests_answer_list[$quest_id])" >checked="checked"</if> disabled="disabled" />
                                                {{ $info }}
                                            </div>
                                        </foreach>
                                    </case>
                                    <case value="text">
                                        {$quests_answer_list[$quest_id][0]}
                                    </case>
                                    <case value="textarea">
                                        {$quests_answer_list[$quest_id][0]}
                                    </case>
                                </switch>
                            </div>
                        </div>
                    </div>
                </foreach>
            </div>
        </div>
    </section>