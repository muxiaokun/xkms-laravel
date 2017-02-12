@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" action="" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.region_name')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="@lang('common.region_name')"
                                           name="region_name" value="{{ $edit_info['region_name'] }}"
                                           onchange="M_zh2py(this,'input[name=short_name]')"
                                           link="{{ route('Admin::Region::ajax_api') }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.short_name')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="@lang('common.short_name')"
                                           name="short_name" value="{{ $edit_info['short_name'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.all_spell')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="@lang('common.all_spell')"
                                           name="all_spell" value="{{ $edit_info['all_spell'] }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.short_spell')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="@lang('common.short_spell')"
                                           name="short_spell" value="{{ $edit_info['short_spell'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.areacode')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="@lang('common.areacode')"
                                           name="areacode" value="{{ $edit_info['areacode'] }}"
                                           onKeyup="M_in_int(this);"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.postcode')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="@lang('common.postcode')"
                                           name="postcode" value="{{ $edit_info['postcode'] }}"
                                           onKeyup="M_in_int(this);"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.parent_level')</label>
                                <div class="col-sm-6" id="region_list">
                                    <input type="hidden" name="parent_id"/>
                                    <script type="text/javascript" src="{{ asset('js/M_select_add.js') }}"></script>
                                    <script type="text/javascript">
                                        $(function () {
                                            var config = {
                                                @if ($edit_info['parent_id'])'def_data': {
                                                    'value':{{ $edit_info['parent_id'] }},
                                                    'html': '{{ $edit_info['parent_name'] }}'
                                                }, @endif
                                                'edit_obj': $('#region_list'),
                                                'post_name': 'parent_id',
                                                'ajax_url': '{{ route('Admin::Region::ajax_api') }}',
                                                'field': 'parent_id'
                                            };
                                            new M_select_add(config);
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.yes')@lang('common.no')@lang('common.show')</label>
                                <div class="col-sm-4">
                                    <label class="radio-inline">
                                        <input type="radio" name="if_show" value="1"
                                               @if (1 === $edit_info['if_show'])checked="checked"@endif />@lang('common.show')
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="if_show" value="0"
                                               @if (0 === $edit_info['if_show'] or '' === $edit_info['if_show'])checked="checked"@endif />@lang('common.hidden')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt10">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                @if (Route::is('*::add'))
                                    @lang('common.add')
                                @elseif (Route::is('*::edit'))
                                    @lang('common.edit')
                                @endif
                            </button>
                            <a href="{{ route('Admin::Region::index') }}" class="btn btn-default">
                                @lang('common.goback')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection