@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                    <input type="hidden" name="audit_id" value="1"/>
                    <div class="col-sm-12 text-center"><h3>@lang('common.system')@lang('common.info')</h3></div>
                    <table class="table table-condensed table-hover col-sm-12">
                        <tr>
                            <td>@lang('common.audit')@lang('common.admin')</td>
                            <td>{{ $edit_info['admin_name'] }}</td>
                            <td>@lang('common.send')@lang('common.user')</td>
                            <td>{{ $edit_info['member_name'] }}</td>
                        </tr>
                        <tr>
                            <td>@lang('common.send')@lang('common.time')</td>
                            <td>{{ $edit_info['created_at'] }}</td>
                            <td>@lang('common.send') IP</td>
                            <td>{{ $edit_info['add_ip'] }}</td>
                        </tr>
                    </table>
                    <div class="col-sm-12 text-center"><h3>@lang('common.extend')@lang('common.info')</h3></div>
                    <table class="table table-condensed table-hover col-sm-12">
                        @foreach ($edit_info['send_info'] as $name => $value)
                            <tr>
                                <td class="col-sm-3">{{ $name }}</td>
                                <td>{{ $value }}</td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="col-sm-12 text-center">@lang('common.reply')@lang('common.content')</div>
                    <div class="col-sm-12">
                        <textarea rows="5" class="col-sm-12" name="reply_info">{{ $edit_info['reply_info'] }}</textarea>
                    </div>
                    <div class="col-sm-12 text-center mt10">
                        <button type="submit" class="btn btn-info">
                            @lang('common.audit')
                        </button>
                        <a href="{{ route('Admin::MessageBoardLog::index') }}" class="btn btn-default">
                            @lang('common.goback')
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection