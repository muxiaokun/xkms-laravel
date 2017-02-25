<div class="col-sm-12 list_title">
    系统新闻<span>News</span>
</div>
<div class="col-sm-12 m_select">
    <ul class="list-unstyled lh30">
        <M:D name="Article" fn="m_select" fn_arg="cate_id == 2|is_audit > 0" limit="10" item="news_article"/>
        @if (isset($news_article))
            @foreach ($news_article as $data)
            <li class="title">
                <a href="{{ route('Home::Article::article',['id'=>$data['id']]) }}">
                    {{ mSubstr(mDate($data['created_at'],"m-d")) }}&nbsp;&nbsp;{{ mSubstr($data['title'],15) }}
                </a>
            </li>
        @endforeach
        @endif
    </ul>
</div>
<div class="col-sm-12 list_title mt20">
    使用手册<span>Manual</span>
</div>
<div class="col-sm-12 m_select">
    <ul class="list-unstyled lh30">
        <M:D name="Article" fn="m_select" fn_arg="cate_id == 4|is_audit > 0" limit="10" item="manual_article"/>
        @if (isset($manual_article))
        @foreach ($manual_article as $data)
            <li class="title">
                <a href="{{ route('Home::Article::article',['id'=>$data['id']]) }}">
                    {{ mSubstr(mDate($data['created_at'],"m-d")) }}&nbsp;&nbsp;{{ mSubstr($data['title'],15) }}
                </a>
            </li>
        @endforeach
        @endif
    </ul>
</div>