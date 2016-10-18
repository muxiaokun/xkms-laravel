<div class="col-sm-12 list_title">
    系统新闻<span>News</span>
</div>
<div class="col-sm-12 m_select">
    <ul class="list-unstyled lh30">
        <M:D name="Article" fn="m_select" fn_arg="cate_id == 2|is_audit > 0" limit="10" item="news_article"/>
        @foreach ($news_article as $data)
            <li class="title">
                <a href="{:M_U('article',$data['id'])}">
                    {{ mDate($data['created_at'],"m-d") }}&nbsp;&nbsp;{{ $data['title']|M_substr=15 }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
<div class="col-sm-12 list_title mt20">
    使用手册<span>Manual</span>
</div>
<div class="col-sm-12 m_select">
    <ul class="list-unstyled lh30">
        <M:D name="Article" fn="m_select" fn_arg="cate_id == 4|is_audit > 0" limit="10" item="manual_article"/>
        @foreach ($manual_article as $data)
            <li class="title">
                <a href="{:M_U('article',$data['id'])}">
                    {{ mDate($data['created_at'],"m-d") }}&nbsp;&nbsp;{{ $data['title']|M_substr=15 }}
                </a>
            </li>
        @endforeach
    </ul>
</div>