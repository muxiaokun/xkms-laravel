@extends('home.Member_layout')
@section('content')
    <script type="text/javascript">
        function answer_submit() {
            @foreach ($quests_quest_list as $quest_id => $quest)
                    @if ($quest['required'])
                    @if ('radio' == $quest['answer_type'])
                obj_str = "[name='quests[{{ $quest_id }}]']:checked";
            @elseif ('checkbox' == $quest['answer_type'])
                obj_str = "[name='quests[{{ $quest_id }}][]']:checked"
            @elseif ('text' == $quest['answer_type'])
                    @elseif ('textarea' == $quest['answer_type'])
                obj_str = "[name='quests[{{ $quest_id }}]']"
            @endif
            if (!$(obj_str).val()) {
                window.location.hash = "quests{{ $quest_id }}";
                return false;
            }
            @endif
            @endforeach
                return true;
        }
    </script>
    <div class="col-sm-12">        {{ $quests_info['start_content'] }}    </div>
    <div class="col-sm-12">
        <form id="answer_form" onSubmit="return answer_submit();" method="post">            {{ csrf_field() }} <input
                    type="hidden" name="id" value="{{ $quests_info['id'] }}"/>
            <input type="hidden" name="access_info" value="{{ $quests_info['access_info'] }}"/>
            @if ($quests_quest_list)
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
                            @if ('radio' == $quest['answer_type'])
                                @foreach ($quest['answer'] as $key => $info)
                                    <div class="col-sm-2">
                                        <label class="checkbox-inline">
                                            <input type="radio" name="quests[{{ $quest_id }}]"
                                                   value="{{ $key }}"/>{{ $info }}
                                        </label>
                                    </div>
                                @endforeach
                            @elseif ('checkbox' == $quest['answer_type'])
                                @foreach ($quest['answer'] as$key =>  $info)
                                    <div class="col-sm-2">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="quests[{{ $quest_id }}][]"
                                                   value="{{ $key }}"/>{{ $info }}
                                        </label>
                                    </div>
                                @endforeach
                            @elseif ('text' == $quest['answer_type'])
                                <input class="form-control" type="text" name="quests[{{ $quest_id }}]"/>
                            @elseif ('textarea' == $quest['answer_type'])
                                <textarea name="quests[{{ $quest_id }}]" style="width:100%" row="3"></textarea>
                            @endif
                        </div>
                    </div>
                @endforeach
            <!-- 隐藏传参 -->
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button class="col-sm-offset-5 col-sm-1 btn btn-info"
                                type="submit">@lang('common.submit')</button>
                    </div>
                </div>
            @else

                <div class="row">
                    <div class="col-sm-12 text-center">
                        @lang('quests.quests')@lang('common.question')@lang('common.dont')@lang('common.exists')
                    </div>
                </div>
            @endif
        </form>
    </div>
    <div class="col-sm-12">
        {{ $quests_info['end_content'] }}
    </div>
@endsection