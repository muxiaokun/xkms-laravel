
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form method="post">
                    <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                    <div class="col-sm-12 text-center"><h3>{{ trans('common.system') }}{{ trans('common.info') }}</h3></div>
                    <table class="table table-condensed table-hover col-sm-12">
                        <tr>
                            <td>{{ trans('common.audit') }}{{ trans('common.admin') }}</td><td>{{ $edit_info['admin_name'] }}</td>
                            <td>{{ trans('common.send') }}{{ trans('common.user') }}</td><td>{{ $edit_info['member_name'] }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans('common.send') }}{{ trans('common.time') }}</td><td>{{ $edit_info['add_time']|M_date=C('SYS_DATE_DETAIL') }}</td>
                            <td>{{ trans('common.send') }} IP</td><td>{{ $edit_info['aip'] }}</td>
                        </tr>
                    </table>
                    <div class="col-sm-12 text-center"><h3>{{ trans('common.extend') }}{{ trans('common.info') }}</h3></div>
                    <table class="table table-condensed table-hover col-sm-12">
                        @foreach ($edit_info['send_info'] as $name => $value)
                        <tr>
                            <td class="col-sm-3">{{ $name }}</td>
                            <td>{{ $value }}</td>
                        </tr>
                        @endforeach
                    </table>
                    <div class="col-sm-12 text-center">{{ trans('common.reply') }}{{ trans('common.content') }}</div>
                    <div class="col-sm-12">
                        <textarea rows="5" class="col-sm-12" name="reply_info">{{ $edit_info['reply_info'] }}</textarea>
                    </div>
                    <div class="col-sm-12 text-center mt10">
                        <button type="submit" class="btn btn-info">
                            {{ trans('common.audit') }}
                        </button>
                        <a href="{{ route('index') }}" class="btn btn-default">
                            {{ trans('common.goback') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
