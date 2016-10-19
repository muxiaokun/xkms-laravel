@if ($where_info)
    <div class="mb10 text-right">
        <form class="form-inline" role="form" method="get">
            @if (URL_COMMON == config('system.url_model'))
                <input type="hidden" name="m" value="{{ $Think['const']['MODULE_NAME'] }}"/>
                <input type="hidden" name="c" value="{{ $Think['const']['CONTROLLER_NAME'] }}"/>
                <input type="hidden" name="a" value="{{ $Think['const']['ACTION_NAME'] }}"/>
            @endif
            @foreach ($where_info as $input_name => $data)
                <div class="form-group mr10">
                    <label>{{ $data['name'] }}</label>
                    @if ('time' == $data['type'])
                        <input type="text" name="{{ $input_name }}_start" class="form-control w100"
                               value="{:I($input_name.'_start')}" onClick="$(this).val('')"/>
                        @lang('common.to')
                        <input type="text" name="{{ $input_name }}_end" class="form-control w100"
                               value="{:I($input_name.'_end')}" onClick="$(this).val('')"/>
                        <M:Datepicker start="$input_name" end="$input_name"/>
                    @elseif ('input' == $data['type'])
                        <input type="text" name="{{ $input_name }}" class="form-control w100" value="{:I($input_name)}"
                               onClick="$(this).val('')"/>
                    @elseif ('select' == $data['type'])
                        <select type="text" name="{{ $input_name }}" class="form-control w100">
                            <option value="">@lang('common.please')@lang('common.selection')</option>
                            @foreach ($data['value'] as $value => $name)
                                <option value="{{ $value }}"
                                        @if ($value == I($input_name))selected="selected"@endif >{{ $name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endforeach
            <input class="btn btn-default" type="submit" value="@lang('common.select')"/>
        </form>
    </div>
@endif