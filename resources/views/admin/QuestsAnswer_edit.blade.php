    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ $title }}<a href="javascript:window.print()">打印</a>
                <a class="fr fs10" href="{{ route('Quests/index',array('id'=>I('id'))) }}">{{ trans('common.goback') }}</a>
            </div>
            <div class="panel-body">
                <foreach name="quests_quest_list" item="quest">
                    <div class="form-group">
                        <label class="col-sm-12">
                            {$quest.question}
                            <switch name="quest['answer_type']">
                                <case value="radio">
                                    {{ trans('common.radio') }}
                                </case>
                                <case value="checkbox">
                                    {{ trans('common.checkbox') }}
                                </case>
                                <case value="text">
                                    {{ trans('common.textarea') }}
                                </case>
                                <case value="textarea">
                                    {{ trans('common.textarea') }}
                                </case>
                            </switch>
                        </label>
                        <div class="col-sm-12">
                            <foreach name="quest.answer" item="answer">
                                <div class="col-sm-2">{:L('sf_answer_conut',array('name'=>$answer['name'],'count'=>$answer['count']))}</div>
                            </foreach>
                            <div class="cb">{:L('sf_answer_all_conut',array('count'=>$quest['max_count']))}</div>
                        </div>
                    </div>
                    <div class="cb h20"></div>
                </foreach>
            </div>
        </div>
    </section>