                <section class="container">
                    <div class="col-sm-12 text-center">
                        <h2>{$recruit_info.title}</h2>
                    </div>
                    <div class="col-sm-12 text-center mtb5">
                        {{ trans('common.re_recruit') }}{{ trans('common.number') }}{{ trans('common.colon') }}
                        {$recruit_info.current_portion}/{$recruit_info.max_portion}
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        {{ trans('common.time') }}{{ trans('common.colon') }}
                        {$recruit_info.start_time|M_date=C('SYS_DATE_DETAIL')}
                        {{ trans('common.to') }}
                        {$recruit_info.end_time|M_date=C('SYS_DATE_DETAIL')}
                    </div>
                    <div class="col-sm-12 text-center mtb5">
                        @foreach ($recruit_info['ext_info'] as $data)
                            @if ($data)
                                &nbsp;&nbsp;<span class="badge">
                                    {{ $key }}{{ trans('common.colon') }}{{ $data }}
                                </span>&nbsp;&nbsp;
                            @endif
                        @endforeach
                    </div>
                    <div class="col-sm-12 mtb10">
                        {$recruit_info.explains}
                    </div>
                    <div class="col-sm-12 text-center mtb10">
                        <a class="btn btn-default" href="{:M_U('Recruit/add',array('id'=>$recruit_info['id']))}">
                            {{ trans('common.submit') }}{{ trans('common.recruit') }}{{ trans('common.info') }}
                        </a>
                    </div>
                </section>
  