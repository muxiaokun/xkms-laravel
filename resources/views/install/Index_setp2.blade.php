        <script type="text/javascript">
            $(function(){
                if(parent && parent.move_progress)
                {
                    parent.move_progress({{ config('setp_progress.2') }});
                }
            });
        </script>
        {/*<!--安装第二步界面 开始-->*/}
        <section class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="text-center alert <if condition="$compare_database_info['if_exists'] eq 1">alert-success<else />alert-warning</if>">
                        <if condition="$compare_database_info['if_exists'] eq 1">
                            {{ trans('common.database') }}{$compare_database_info.name}{{ trans('common.exists') }}
                        <else />
                            {{ trans('common.database') }}{$compare_database_info.name}{{ trans('common.dont') }}{{ trans('common.exists') }}{{ trans('common.auto_create') }}
                        </if>
                    </div>
                </div>
                <form action="{{ route('setp3') }}" method="post">
                <div class="col-sm-12">
                    <input type="hidden" name="setp" value="3">
                    <table class="table table-hover">
                        <tr>
                            <th>
                                {{ trans('common.controller') }}{{ trans('common.info') }}
                            </th>
                            <th>
                                <label class="checkbox-inline">
                                    <input type="checkbox" onClick="allselect('[mtype=install]',this);" />
                                    {{ trans('common.allselect') }}
                                </label>
                            </th>
                            <th>
                                {{ trans('common.table') }}{{ trans('common.info') }}
                                &nbsp;&nbsp;
                                <label class="checkbox-inline">
                                    <input type="checkbox" onClick="allselect('[mtype=reset]',this);" />
                                    <span style="color:red;">{{ trans('common.setp2_commont1') }}</span>
                                </label>
                            </th>
                        </tr>
                        <foreach name="compare_tables_info" key="control_index" item="data">
                        <tr>
                            <td>
                                [{$data.control_group}]{$data.control_info}
                            </td>
                            <td>
                                <if condition="1 lt $data['category']">
                                    <input type="checkbox" name="install_control[install][]" value="{{ $control_index }}"  mtype="install"/>
                                <else />
                                    <input type="checkbox" disabled="disabled" checked="checked"/>
                                </if>
                            </td>
                            <td class="text-left">
                                <if condition="$data['tables']">
                                    <foreach name="data.tables" key="table_name" item="table">
                                        <div class="fl">
                                        {$table.table_info}{{ $table_name }}(
                                        <if condition="$table['if_exists'] eq 1">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="install_control[reset][]" value="{{ $table_name }}"  mtype="reset"/>
                                                <span style="color:red;">{{ trans('common.exists') }}</span>
                                            </label>
                                        <else />
                                            <span style="color:green;">{{ trans('common.create') }}</span>
                                        </if>
                                        )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </div>
                                    </foreach>
                                <else />
                                    {{ trans('common.none') }}{{ trans('common.table') }}{{ trans('common.info') }}
                                </if>
                            </td>
                        </tr>
                        </foreach>
                    </table>
                </div>
                <div class="col-sm-12 text-center">
                    <a class="btn btn-lg btn-primary mt20 mr80" href="{{ route('setp1') }}">{{ trans('common.previous') }}{{ trans('common.setp') }}</a>
                    <button id="switch_href" class="btn btn-lg btn-primary mt20" type="submit">{{ trans('common.setp2') }}</button>
                </div>
                </form>
            </div>
        </section>
        {/*<!--安装第二步界面 结束-->*/}