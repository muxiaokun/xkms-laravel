<if condition="$where_info">
    <div class="mb10 text-right">
        <form class="form-inline" role="form" method="get">
            <if condition="URL_COMMON eq C('URL_MODEL')">
                <input type="hidden" name="m" value="{$Think.const.MODULE_NAME}" />
                <input type="hidden" name="c" value="{$Think.const.CONTROLLER_NAME}" />
                <input type="hidden" name="a" value="{$Think.const.ACTION_NAME}" />
            </if>
            <foreach name="where_info" key="input_name" item="data">
                <div class="form-group mr10">
                    <label>{$data.name}</label>
                    <if condition="'time' eq $data['type']">
                        <input type="text" name="{{ $input_name }}_start" class="form-control w100" value="{:I($input_name.'_start')}" onClick="$(this).val('')" />
                        {{ trans('common.to') }}
                        <input type="text" name="{{ $input_name }}_end" class="form-control w100" value="{:I($input_name.'_end')}" onClick="$(this).val('')" />
                        <M:Datepicker start="$input_name" end="$input_name" />
                    <elseif condition="'input' eq $data['type']"/>
                        <input type="text" name="{{ $input_name }}" class="form-control w100" value="{:I($input_name)}" onClick="$(this).val('')" />
                    <elseif condition="'select' eq $data['type']"/>
                        <select type="text" name="{{ $input_name }}" class="form-control w100">
                            <option value="">{{ trans('common.please') }}{{ trans('common.selection') }}</option>
                            <foreach name="data.value" key="value" item="name">
<option value="{{ $value }}" <if condition="$value eq I($input_name)">selected="selected"</if> >{{ $name }}</option>
                            </foreach>
                        </select>
                    </if>
                </div>
            </foreach>
            <input class="btn btn-default" type="submit" value="{{ trans('common.select') }}" />
        </form>
    </div>
</if>