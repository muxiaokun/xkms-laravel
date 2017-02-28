@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ $title }}<a href="javascript:window.print()">打印</a>
                <a class="fr fs10"
                   href="{{ route('Admin::QuestsAnswer::index',array('quests_id'=>$quests_info['id'])) }}">@lang('common.goback')</a>
            </div>
            <div class="panel-body">
                @foreach ($quests_quest_list as $quest_id => $quest)
                    <div class="col-sm-12">
                        <div class="form-group mt20 cb">
                            <a name="quests{{ $quest_id }}"></a>
                            <label>
                                <h4>{{ $quest['question'] }}
                                    @if ($quest['required'])
                                        <span class="ml20" style="color:#ff0000">(@lang('common.required'))</span>
                                    @endif
                                </h4>
                            </label>
                            <span class="mt10 help-block">{{ $quest['explains'] }}</span>
                            <div>
                                @if ('radio' == $quest['answer_type'])
                                    @foreach ($quest['answer'] as $key=> $info)
                                        <div class="col-sm-2">
                                            <input type="radio" name="quests[{{ $quest_id }}]" value="{{ $key }}"
                                                   @if (in_array($key,$quests_answer_list[$quest_id]))checked="checked"
                                                   @endif disabled="disabled"/>
                                            {{ $info }}
                                        </div>
                                    @endforeach
                                @elseif ('checkbox' == $quest['answer_type'])
                                    @foreach ($quest['answer'] as $key=> $info)
                                        <div class="col-sm-2">
                                            <input type="checkbox" name="quests[{{ $quest_id }}][]"
                                                   value="{{ $key }}"
                                                   @if (in_array($key,$quests_answer_list[$quest_id]))checked="checked"
                                                   @endif disabled="disabled"/>
                                            {{ $info }}
                                        </div>
                                    @endforeach
                                @elseif ('text' == $quest['answer_type'])
                                    {{ $quests_answer_list[$quest_id][0] }}
                                @elseif ('textarea' == $quest['answer_type'])
                                    {{ $quests_answer_list[$quest_id][0] }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection