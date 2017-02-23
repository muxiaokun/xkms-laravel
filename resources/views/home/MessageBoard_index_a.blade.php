<section class="container">
    <div class="row">
        <form class="form-horizontal" action="{{ route('Home::MessageBoard::add') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $message_board_info['id'] }}"/>
            <div class="col-sm-12">
                @foreach ($message_board_info['config'] as $data)
                    <div class="form-group">
                        <label class="control-label col-sm-4">{{ $data['msg_name'] }}@lang('common.colon')</label>
                        <div class="col-sm-4">
                            @if ('text' == $data['msg_type'])
                                <input type="text" name="send_info[{{ $data['msg_name'] }}]" class="form-control"/>
                            @elseif ('radio' == $data['msg_type'])
                                @foreach ($data['msg_option'] as $msg_option_data)
                                    <label class="control-label checkbox checkbox-inline">
                                        <input type="radio" name="send_info[{{ $data['msg_name'] }}]"
                                               value="{{ $msg_option_data }}"/>
                                        {{ $msg_option_data }}
                                    </label>
                                @endforeach
                            @elseif ('checkbox' == $data['msg_type'])
                                @foreach ($data['msg_option'] as $msg_option_data)
                                    <label class="control-label checkbox checkbox-inline">
                                        <input type="checkbox" name="send_info[{{ $data['msg_name'] }}][]"
                                               value="{{ $msg_option_data }}"/>
                                        {{ $msg_option_data }}
                                    </label>
                                @endforeach
                            @elseif ('textarea' == $data['msg_type'])
                                <textarea name="send_info[{{ $data['msg_name'] }}]" rows="5" class="form-control"
                                          style="resize: none;"></textarea>
                            @endif
                        </div>
                        <div class="col-sm-4">
                            @if ($data['msg_required'])@lang('common.required')@endif
                            @if ($data['msg_length'])@lang('common.max')@lang('common.length'){{ $data['msg_length'] }}@endif
                        </div>
                    </div>
                @endforeach
                @if (config('system.sys_frontend_verify'))
                    <div class="form-group">
                        <label class="control-label col-sm-4">验证码：</label>
                        <div class="col-sm-4">
                            <input type="text" name="verify" class="form-control" style="text-transform:uppercase;"/>
                        </div>
                    </div>
                    <div class="form-group">
                        @asyncImg(<img class="col-sm-4 col-sm-offset-2"
                             src="{{ route('VerificationCode') }}"
                             onClick="M_change_verify(this,$('input[name=verify]'))"/>)
                    </div>
                @endif
            </div>
            <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-info">
                    @lang('common.send')
                </button>
            </div>
        </form>
    </div>
    <div class="col-sm-12">
        <table class="table table-hover">
            @foreach ($message_board_log_list as $message_board_log)
                <tr>
                    <td>
                        <table class="table table-hover">
                            <tr>
                                <td width="10%">@lang('common.send')@lang('common.time')@lang('common.colon')</td>
                                <td width="90%">{{ mDate($message_board_log['created_at']) }}</td>
                            </tr>
                            @foreach ($message_board_log['send_info'] as $name => $value)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>@lang('common.reply')@lang('common.content')@lang('common.colon')</td>
                                <td>{{ $message_board_log['reply_info'] }}</td>
                            </tr>
                        </table>
                        @endforeach
                    </td>
                </tr>
        </table>
        <table class="table">
            <tr>
                <td class="text-right">
                    {{ $message_board_log_list->links('home.pagination') }}
                </td>
            </tr>
        </table>
    </div>
</section>