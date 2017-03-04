@if (isset($where_info) && is_array($where_info))
    <form class="form-inline" role="form" method="get">
        <div class="col-sm-12 mb10 text-right">
            @foreach ($where_info as $input_name => $data)
                @if ('time' == $data['type'])
                    <div class="form-group mr10">
                        <label>{{ $data['name'] }}</label>
                        <input type="text" name="{{ $input_name }}_start" class="form-control w100"
                               value="{{ request($input_name.'_start') }}" onClick="$(this).val('')"/>
                        @lang('common.to')
                        <input type="text" name="{{ $input_name }}_end" class="form-control w100"
                               value="{{ request($input_name.'_end') }}" onClick="$(this).val('')"/>
                        @datepicker($input_name,$input_name)
                    </div>
                @elseif ('input' == $data['type'])
                    <div class="form-group mr10">
                        <label>{{ $data['name'] }}</label>
                        <input type="text" name="{{ $input_name }}" class="form-control w100"
                               value="{{ request($input_name) }}"
                               onClick="$(this).val('')"/>
                    </div>
                @elseif ('select' == $data['type'])
                    <div class="form-group mr10">
                        <label>{{ $data['name'] }}</label>
                        <select name="{{ $input_name }}" class="form-control w100">
                            <option value="">@lang('common.please')@lang('common.selection')</option>
                            @foreach ($data['value'] as $value => $name)
                                <option value="{{ $value }}"
                                        @if ($value == request($input_name))selected="selected"@endif >{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            @endforeach
            <input class="btn btn-default" type="submit" value="@lang('common.select')"/>
        </div>
        @foreach ($where_info as $input_name => $data)
            @if ('multilevel_selection' == $data['type'])
                <div class="col-sm-12 mb10 text-right">
                    <div id="multilevel_selection_{{ $input_name }}" class="form-group mr10">
                        <label>{{ $data['name'] }}</label>
                        @if (isset($data['value']) && !$data['value']->isEmpty())
                            <input type="hidden" name="cate_id" value="{{ request('cate_id') }}"/>
                            @foreach($data['value'] as $categorys)
                                <select class="form-control w100">
                                    <option value="">@lang('common.please')@lang('common.selection')</option>
                                    @foreach($categorys['category_list'] as $category)
                                        <option @if ($categorys['id'] == $category['id'])selected="selected" @endif
                                        value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                    @endforeach
                                </select>
                            @endforeach
                        @endif
                    </div>
                    <script type="text/javascript" src="{{ asset('js/M_multilevel_selection.js') }}"></script>
                    <script type="text/javascript">
                        var M_multilevel_selection_obj;
                        $(function () {
                            var config = {
                                'out_obj': $('#multilevel_selection_{{ $input_name }}'),
                                'submit_type': 'id',
                                'edit_obj': $('<select class="form-control w100"></select>'),
                                'post_name': '{{ $input_name }}',
                                'ajax_url': '{{ $data['ajax_url'] }}'
                            };
                            M_multilevel_selection_obj = new M_multilevel_selection(config);
                        });
                    </script>
                    <div class="fr w60">&nbsp;</div>
                </div>
            @endif
        @endforeach
    </form>
@endif