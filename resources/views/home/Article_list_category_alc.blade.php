@extends('home.Article_layout')
@section('content')
    <link rel="stylesheet" href="{{ asset('prettyPhoto/prettyPhoto.css') }}"/>
    <script type="text/javascript" src="{{ asset('prettyPhoto/jquery.prettyPhoto.js') }}"></script>
    <script type="text/javascript">
        $("#prettyPhoto").prettyPhoto({'social_tools': '', 'theme': 'facebook'});
    </script>
    <div id="prettyPhoto">
        @if(isset($category_info['attribute']))
            <div class="col-sm-12">
                @foreach (mAttributeArr($category_info['attribute']) as $attrs)
                    <div class="col-sm-12 btn-group btn-group-sm mb10" role="group" aria-label="...">
                        @foreach ($attrs as $attr)
                            <a class="btn btn-default @if ($attr['checked'])btn-info @endif" href="{{ $attr['link'] }}">
                                {{ $attr['name'] }}
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
        @foreach ($article_list as $index => $data)
            <div class=" col-sm-12 pb10">
                <div class="col-sm-2 pt10">
                    <a id="prettyPhoto{{ $index }}"
                       href="{{ route('Home::Article::article',['id'=>$data['id']]) }}">
                        @asyncImg(<img class="img-thumbnail" src="{{ mExists($data['thumb']) }}"/>)
                    </a>
                </div>
                <a href="{{ route('Home::Article::article',['id'=>$data['id']]) }}">
                    <h3>{{ mSubstr($data['title'],15)}}</h3>
                </a>
                <span>{{ mSubstr($data['description'],100)}}</span>
                @if(isset($data['album']) && is_array($data['album']))
                    <script type="text/javascript">
                        $(function () {
                            var api_gallery = Array();
                            var api_titles = Array();
                            var api_descriptions = Array();
                            var prettyPhoto_push = function (data) {
                                if (!data) {
                                    console.log('prettyPhoto_push data not exists!');
                                    return;
                                }
                                api_gallery.push(data.src);
                                api_titles.push(data.title);
                                api_descriptions.push(data.description);
                            }
                            @foreach ($data['album'] as $data)
                                prettyPhoto_push({!! json_encode($data) !!});
                            @endforeach
                            $("#prettyPhoto{{ $index }}").on('click', function () {
                                $.prettyPhoto.open(api_gallery, api_titles, api_descriptions);
                                return false;
                            });
                        });
                    </script>
                @endif
            </div>
        @endforeach
    </div>
    <div class="col-sm-12">
        {{ $article_list->links('home.pagination') }}
    </div>
@endsection