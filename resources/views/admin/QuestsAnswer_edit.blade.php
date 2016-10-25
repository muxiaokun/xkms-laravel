@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ $title }}<a href="javascript:window.print()">打印</a>
                <a class="fr fs10"
                   href="{{ route('Admin::Quests::index',array('id'=>request('id'))) }}">@lang('common.goback')</a>
            </div>
            <div class="panel-body">
                @foreach ($quests_quest_list as $quest)
                    <div class="form-group">
                        <label class="col-sm-12">
                            {{ $quest['question'] }}
                            <switch name="quest['answer_type']">
                                <case value="radio">
                                    @lang('common.radio')
                                </case>
                                <case value="checkbox">
                                    @lang('common.checkbox')
                                </case>
                                <case value="text">
                                    @lang('common.textarea')
                                </case>
                                <case value="textarea">
                                    @lang('common.textarea')
                                </case>
                            </switch>
                        </label>
                        <div class="col-sm-12">
                            @foreach ($quest['answer'] as $answer)
                                <div class="col-sm-2">
                                    {:L('sf_answer_conut',array('name'=>$answer['name'],'count'=>$answer['count']))}
                                </div>
                            @endforeach
                            <div class="cb">{:L('sf_answer_all_conut',array('count'=>$quest['max_count']))}</div>
                        </div>
                    </div>
                    <div class="cb h20"></div>
                @endforeach
            </div>
        </div>
    </section>
@endsection