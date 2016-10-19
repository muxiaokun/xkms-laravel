@extends('home.Member_layout')
@section('content')
    <script type="text/javascript">
        function answer_submit() {
            @foreach ($quests_quest_list as $quest_id => $quest)
            @if ($quest['required'])
        <
            switch name = "quest['answer_type']" >
                    <
                case value = "radio" >
                obj_str = "[name='quests[{{ $quest_id }}]']:checked"
                    ;
                </
                case>
                <
                case value = "checkbox" >
                obj_str = "[name='quests[{{ $quest_id }}][]']:checked"
                    ;
                </
                case>
                <
                case value = "textarea" >
                obj_str = "[name='quests[{{ $quest_id }}]']"
                    ;
                </
                case>
                </
                    switch
                        >
                            if (!$(obj_str).val()) {
                                window.location.hash = "quests{{ $quest_id }}";
                                return false;
                            }
                            @endif
                            @endforeach
                                    return true;
                    }
    </script>
    <div class="col-sm-12">
        {{ $quests_info['start_content'] }}
    </div>
    <div class="col-sm-12">
        <form id="answer_form" onSubmit="return answer_submit();" method="post">
            <input type="hidden" name="id" value="{{ $quests_info['id'] }}"/>
            <input type="hidden" name="access_info" value="{{ $quests_info['access_info'] }}"/>
            @foreach ($quests_quest_list as $quest_id => $quest)
                <div class="col-sm-12 form-group mt20 cb">
                    <a name="quests{{ $quest_id }}"></a>
                    <label>
                        <h4>{{ $quest['question'] }}
                            @if ($quest['required'])
                                <span class="ml20" style="color:#ff0000">(@lang('common.required'))</span>
                            @endif
                        </h4>
                    </label>
                    <span class="help-block">{{ $quest['explains'] }}</span>
                    <div>
                        <switch name="quest['answer_type']">
                            <case value="radio">
                                @foreach ($quest['answer'] as $info)
                                    <div class="col-sm-2">
                                        <label class="checkbox-inline">
                                            <input type="radio" name="quests[{{ $quest_id }}]"
                                                   value="{{ $key }}"/>{{ $info }}
                                        </label>
                                    </div>
                                @endforeach
                            </case>
                            <case value="checkbox">
                                @foreach ($quest['answer'] as $info)
                                    <div class="col-sm-2">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="quests[{{ $quest_id }}][]"
                                                   value="{{ $key }}"/>{{ $info }}
                                        </label>
                                    </div>
                                @endforeach
                            </case>
                            <case value="text">
                                <input class="form-control" type="text" name="quests[{{ $quest_id }}]"/>
                            </case>
                            <case value="textarea">
                                <textarea name="quests[{{ $quest_id }}]" style="width:100%" row="3"></textarea>
                            </case>
                        </switch>
                    </div>
                </div>
        @endforeach
        <!-- 隐藏传参 -->
            <div class="row">
                <div class="col-sm-12 text-center">
                    <button class="col-sm-offset-5 col-sm-1 btn btn-info" type="submit">@lang('common.submit')</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-sm-12">
        {{ $quests_info['end_content'] }}
    </div>
@endsection