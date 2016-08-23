
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form id="form_valid" class="form-horizontal" role="form" action="" method="post" >
                    <input type="hidden" name="id" value="{{ $id }}"/>
                    <div class="row">
                        <div class="col-sm-12">
                            @if ('add' eq ACTION_NAME)
                            <div class="form-group">
                                <label class="col-sm-4 control-label">
                                    {{ trans('common.template') }}{{ trans('common.file') }}{{ trans('common.name') }}
                                    (@if ('/' eq C('TMPL_FILE_DEPR'))
                                        controller/file_name{{ config('TMPL_TEMPLATE_SUFFIX') }}
                                    @else
                                        file_name{{ config('TMPL_TEMPLATE_SUFFIX') }}
                                    @endif)
                                </label>
                                <div class="col-sm-4">
                                    <input class="form-control" type="text" name="file_name" value="{{ $edit_info['file_name'] }}" />
                                </div>
                            </div>
                            @endif
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <textarea rows="15" class="col-sm-12" name="content">{{ $edit_info['content'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                             <button type="submit" class="btn btn-info">
                                {{ trans('common.save') }}
                             </button>
                            <a href="{{ route('Index/main') }}" class="btn btn-default">
                                {{ trans('common.goback') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
