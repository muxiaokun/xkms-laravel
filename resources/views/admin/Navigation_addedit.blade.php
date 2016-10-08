
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <script type="text/javascript" src="{{ asset('js/M_valid.js') }}"></script>
                <script>
                    $(function(){
                        var config = {
                            'form_obj':$('#form_valid'),
                            'check_list':{
                                'short_name':Array('short_name','id')
                            },
                            'ajax_url':"{{ route('ajax_api') }}",
                        };
                        new M_valid(config);
                    });
                </script>
                <form id="form_valid" onSubmit="return false;"  class="form-horizontal" role="form" action="" method="post" >
                    <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.navigation')@lang('common.name')</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" placeholder="@lang('common.navigation')@lang('common.name')" name="name" value="{{ $edit_info['name'] }}" onchange="M_zh2py(this,'input[name=short_name]')"  link="{{ route('ajax_api') }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.short')@lang('common.name')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="@lang('common.short')@lang('common.name')" name="short_name" value="{{ $edit_info['short_name'] }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.yes')@lang('common.no')@lang('common.enable')</label>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
<input type="radio" name="is_enable" value="1" @if ('1' heq $edit_info['is_enable'] or !isset($edit_info['is_enable']))checked="checked"@endif />@lang('common.enable')
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="is_enable" value="0" @if ('0' heq $edit_info['is_enable'])checked="checked"@endif />@lang('common.disable')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <table id="edit_window" class="table table-condensed table-hover">
                            <tr>
                                <th class="col-sm-3">@lang('common.navigation')@lang('common.button')@lang('common.name')</th>
                                <th class="col-sm-1">@lang('common.target')@lang('common.type')</th>
                                <th class="col-sm-1">URL</th>
                                <th class="col-sm-7">
                                    @lang('common.insite')@lang('common.colon')M/C/A?arg1=argv1,arg2=argv2<br />
                                    @lang('common.outsite')@lang('common.colon')http:// | https:// | ftp://
                                </th>
                            </tr>
                            <tr>
                                <td><input class="form-control" type="text" name="nav_text" value="" style="width:100%" /></td>
                                <td>
                                    <select class="form-control" name="nav_target">
                                        <option value="_self">_self</option>
                                        <option value="_blank">_blank</option>
                                        <option value="_top">_top</option>
                                        <option value="_parent">_parent</option>
                                    </select>
                                </td>
                                <td colspan="2"><input class="form-control" type="text" name="nav_link" value="" style="width:100%" /></td>
                            </tr>
                        </table>
                        <div id="navigation_out" class="col-sm-12">
                            <script type="text/javascript" src="{{ asset('js/M_navigation_editor.js') }}"></script>
                            <script>
                                $(function(){
                                    new M_navigation_editor({
                                            @if ($edit_info['ext_info'])'def_data':{{ $edit_info['ext_info'] }},@endif
                                            'out_obj':$('#navigation_out'),
                                            'edit_obj':$('#edit_window'),
                                            'post_name':'{{ $navigation_config['post_name'] }}',
                                            'max_level':{{ $navigation_config['navigation_level'] }},
                                        });
                                });
                            </script>
                        </div>
                    </div>
                    <div class="row mt10">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                @if ($Think.const.ACTION_NAME eq 'add')
                                    @lang('common.add')
                                @elseif ($Think.const.ACTION_NAME eq 'edit')
                                    @lang('common.edit')
                                @endif
                            </button>
                            <a href="{{ route('index') }}" class="btn btn-default">
                                    @lang('common.goback')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>