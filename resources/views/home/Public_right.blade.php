<div class="col-sm-12 list_title">
    系统新闻<span>News</span>
</div>
<div class="col-sm-12 m_select">
    <ul class="list-unstyled lh30">
        @php
            $ids = App\Model\ArticleCategory::mFindCateChildIds(2)->toArray();
            $news_article = App\Model\Article::colWhere($ids,'cate_id')
            ->take(10)
            ->where('is_audit','>',0)
            ->get();
        @endphp
        @if (isset($news_article))
            @foreach ($news_article as $data)
            <li class="title">
                <a href="{{ route('Home::Article::article',['id'=>$data['id']]) }}">
                    {{ $data['created_at'] }}&nbsp;&nbsp;{{ mSubstr($data['title'],15) }}
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
        @php
            $ids = App\Model\ArticleCategory::mFindCateChildIds(4,'cate_id')->toArray();
            $manual_article = App\Model\Article::colWhere($ids)
            ->take(10)
            ->where('is_audit','>',0)
            ->get();
        @endphp
        @if (isset($manual_article))
        @foreach ($manual_article as $data)
            <li class="title">
                <a href="{{ route('Home::Article::article',['id'=>$data['id']]) }}">
                    {{ mDate($data['created_at'],"m-d") }}&nbsp;&nbsp;{{ mSubstr($data['title'],15) }}
                </a>
            </li>
        @endforeach
        @endif
    </ul>
</div>