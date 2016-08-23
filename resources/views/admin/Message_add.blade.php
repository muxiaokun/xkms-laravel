
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" action="" method="post">
                    <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.receive') }}{{ trans('common.member') }}</label>
                                <div class="col-sm-8" id="receive_member_list">
                                    <input type="hidden" name="receive_id" />
                                    <script type="text/javascript" src="{{ asset('js/M_select_add.js') }}"></script>
                                    <script type="text/javascript">
                                        $(function(){
                                            var config = {
                                                @if ($receive_info['id'])'def_data':{'value':'{{ $receive_info['id'] }}','html':'{{ $receive_info['member_name'] }}'},@endif
                                                'edit_obj':$('#receive_member_list'),
                                                'post_name':'receive_id',
                                                'ajax_url':'{{ route('ajax_api') }}',
                                                'field':'receive_id'
                                            };
                                            new M_select_add(config);
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">{{ trans('common.send') }}{{ trans('common.content') }}</div>
                        <div class="col-sm-12">
                            <textarea rows="5" class="col-sm-12" name="content">{{ $edit_info['reply_info'] }}</textarea>
                        </div>
                    </div>
                    <div class="row mt10">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                    {{ trans('common.send') }}
                            </button>
                            <a href="{{ route('index') }}" class="btn btn-default">
                                    {{ trans('common.goback') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
