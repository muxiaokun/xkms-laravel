<section class="container">
    <div class="row">
        <div class="col-sm-8">
            <div class="clearfix">
                <div class="list_title">
                    系统功能<span>Function</span>
                </div>
                <div class="list_images">
                    <M:D name="Article" fn="m_select" fn_arg="cate_id between 1|is_audit > 0" limit="8"
                         item="function_article"/>
                    @foreach ($function_article as $data)
                        <div class="col-sm-3">
                            <div class="thumbnail">
                                <a href="{:M_U('article',$data['id'])}">
                                    <M:Img src="{{ $data['thumb']|M_exists }}"/>
                                </a>
                                <div class="caption">
                                    <a href="{:M_U('article',$data['id'])}">
                                        <h4>{{ mSubstr($data['title'],6)}}</h4>
                                    </a>
                                    <a href="{:M_U('article',$data['id'])}">
                                        <p>{{ mSubstr($data['description'],20)}}</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="clearfix">
                <div class="list_title">
                    应用案例<span>Case</span>
                </div>
                <div class="list_images">
                    <M:D name="Article" fn="m_select" fn_arg="cate_id == 3|is_audit > 0" limit="8"
                         item="case_article"/>
                    @foreach ($case_article as $data)
                        <div class="col-sm-3">
                            <div class="thumbnail">
                                <a href="{:M_U('article',$data['id'])}">
                                    <M:Img src="{{ $data['thumb']|M_exists }}"/>
                                </a>
                                <div class="caption">
                                    <a href="{:M_U('article',$data['id'])}">
                                        <h4>{{ mSubstr($data['title'],6)}}</h4>
                                    </a>
                                    <a href="{:M_U('article',$data['id'])}">
                                        <p>{{ mSubstr($data['description'],20)}}</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            @include('Public:right')
        </div>
    </div>
</section>