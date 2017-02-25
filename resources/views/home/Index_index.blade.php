@extends('home.layout')
@section('body')
    <section class="container">
        <div class="row">
            <div class="col-sm-8">
                <div class="clearfix">
                    <div class="list_title">
                        系统功能<span>Function</span>
                    </div>
                    <div class="list_images">
                        @php
                            $ids = App\Model\ArticleCategory::mFindCateChildIds(1)->toArray();
                            $function_article = App\Model\Article::colWhere($ids,'cate_id')
                            ->take(8)
                            ->where('is_audit','>',0)
                            ->get();
                        @endphp
                        @if (isset($function_article))
                            @foreach ($function_article as $data)
                                <div class="col-sm-3">
                                    <div class="thumbnail">
                                        <a href="{{ route('Home::Article::article',['id'=>$data['id']]) }}">
                                            @asyncImg(<img src="{{ mExists($data['thumb']) }}"/>)
                                        </a>
                                        <div class="caption">
                                            <a href="{{ route('Home::Article::article',['id'=>$data['id']]) }}">
                                                <h4>{{ mSubstr($data['title'],6) }}</h4>
                                            </a>
                                            <a href="{{ route('Home::Article::article',['id'=>$data['id']]) }}">
                                                <p>{{ mSubstr($data['description'],20) }}</p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="clearfix">
                    <div class="list_title">
                        应用案例<span>Case</span>
                    </div>
                    <div class="list_images">
                        @php
                            $ids = App\Model\ArticleCategory::mFindCateChildIds(3)->toArray();
                            $case_article = App\Model\Article::colWhere($ids,'cate_id')
                            ->take(8)
                            ->where('is_audit','>',0)
                            ->get();
                        @endphp
                        @if (isset($case_article))
                            @foreach ($case_article as $data)
                                <div class="col-sm-3">
                                    <div class="thumbnail">
                                        <a href="{{ route('Home::Article::article',['id'=>$data['id']]) }}">
                                            @asyncImg(<img src="{{ mExists($data['thumb']) }}"/>)
                                        </a>
                                        <div class="caption">
                                            <a href="{{ route('Home::Article::article',['id'=>$data['id']]) }}">
                                                <h4>{{ mSubstr($data['title'],6)}}</h4>
                                            </a>
                                            <a href="{{ route('Home::Article::article',['id'=>$data['id']]) }}">
                                                <p>{{ mSubstr($data['description'],20)}}</p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                @include('home.Public_right')
            </div>
        </div>
    </section>
@endsection