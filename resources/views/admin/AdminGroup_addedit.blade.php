@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <script type="text/javascript" src="{{ asset('js/M_valid.js') }}"></script>
                <script>
                    $(function () {
                        var config = {
                            'form_obj': $('#form_valid'),
                            'check_list': {
                                'name': Array('name', 'id')
                            },
                            'ajax_url': "{{ route('Admin::AdminGroup::ajax_api') }}",
                        };
                        new M_valid(config);
                    });
                </script>
                <form id="form_valid" onSubmit="return false;" class="form-horizontal" role="form" action=""
                      method="post">
                    <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                    <input type="hidden" name="is_pwd" value="@if ($Think.const.ACTION_NAME == 'add')1@else0@endif"/>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">@lang('common.management')@lang('common.group')@lang('common.name')</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.management')@lang('common.group')@lang('common.name')"
                                           name="name" value="{{ $edit_info['name'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.management')@lang('common.group')@lang('common.explains')</label>
                                <div class="col-sm-6">
                                <textarea name="explains" class="form-control"
                                          style="resize:none;">{{ $edit_info['explains'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.management')@lang('common.group')@lang('common.yes')@lang('common.no')@lang('common.enable')</label>
                                <div class="col-sm-6">
                                    <label class="radio-inline">
                                        <input type="radio" name="is_enable" value="1"
                                               @if ('1' === $edit_info['is_enable'] or !isset($edit_info['is_enable']))checked="checked"@endif />@lang('common.enable')
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="is_enable" value="0"
                                               @if ('0' === $edit_info['is_enable'])checked="checked"@endif />@lang('common.disable')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.group')@lang('common.admin')</label>
                                <div class="col-sm-6"><h4 id="manage_id_list" style="margin:2px 0px 0px 0px;"></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6" id="admin_user_list">
                            <script type="text/javascript" src="{{ asset('js/M_select_add.js') }}"></script>
                            <script type="text/javascript">
                                $(function () {
                                    var config = {
                                        @if ($edit_info['manage_id'])'def_data':{{ $edit_info['manage_id'] }}, @endif
                                        'out_obj': $('#manage_id_list'),
                                        'edit_obj': $('#admin_user_list'),
                                        'post_name': 'manage_id[]',
                                        'ajax_url': '{{ route('Admin::AdminGroup::ajax_api') }}',
                                        'field': 'manage_id'
                                    };
                                    new M_select_add(config);
                                });
                            </script>
                        </div>
                    </div>
                    <div class="row mt10">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                @if ($Think.const.ACTION_NAME == 'add')
                                    @lang('common.add')
                                @elseif ($Think.const.ACTION_NAME == 'edit')
                                    @lang('common.edit')
                                @endif
                            </button>
                            <a href="{{ route('Admin::AdminGroup::index') }}" class="btn btn-default">
                                @lang('common.goback')
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.management')@lang('common.group')@lang('common.default')@lang('common.privilege')</label>
                                <div class="col-sm-8 mb10">
                                    <label class="checkbox-inline"><input type="checkbox"
                                                                          onClick="M_allselect_par(this,'.row')"/>@lang('common.allselect')
                                    </label>
                                    <input type="hidden" value="" name="privilege"/>
                                </div>
                            </div>
                        </div>
                        @foreach ($privilege as $controller => $privs)
                            <div class="col-sm-12">
                                <ul class="list-group">
                                    <li class="list-group-item list-group-item-info">
                                        <label class="checkbox-inline"><input type="checkbox"
                                                                              onClick="M_allselect_par(this,'ul')"/>@lang('common.allselect'){{ $controller }}
                                        </label>
                                    </li>
                                    @foreach ($privs as $controller_name => $actions)
                                        <li class="list-group-item">
                                            @foreach ($actions as $action_name => $action_value)
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="privilege[]"
                                                           value="{{ $controller_name }}_{{ $action_name }}"
                                                           @if ('all' == $edit_info['privilege'] or (is_array($edit_info['privilege']) AND in_array($controller_name.'_'.$action_name,$edit_info['privilege'])))
                                                           checked="checked"
                                                            @endif
                                                    />
                                                    {{ $action_value }}
                                                </label>
                                            @endforeach
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection