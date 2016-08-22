                <section class="container">
                    <div class="col-sm-12 text-center">
                        <h2>{$recruit_info.title}</h2>
                    </div>
                    <div class="col-sm-12 text-center mtb5">
                        {$Think.lang.re_recruit}{$Think.lang.number}{$Think.lang.colon}
                        {$recruit_info.current_portion}/{$recruit_info.max_portion}
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        {$Think.lang.time}{$Think.lang.colon}
                        {$recruit_info.start_time|M_date=C('SYS_DATE_DETAIL')}
                        {$Think.lang.to}
                        {$recruit_info.end_time|M_date=C('SYS_DATE_DETAIL')}
                    </div>
                    <div class="col-sm-12 text-center mtb5">
                        <foreach name="recruit_info.ext_info" item="data">
                            <if condition="$data">
                                &nbsp;&nbsp;<span class="badge">
                                    {$key}{$Think.lang.colon}{$data}
                                </span>&nbsp;&nbsp;
                            </if>
                        </foreach>
                    </div>
                    <div class="col-sm-12 mtb10">
                        {$recruit_info.explains}
                    </div>
                    <div class="col-sm-12 text-center mtb10">
                        <a class="btn btn-default" href="{:M_U('Recruit/add',array('id'=>$recruit_info['id']))}">
                            {$Think.lang.submit}{$Think.lang.recruit}{$Think.lang.info}
                        </a>
                    </div>
                </section>
  