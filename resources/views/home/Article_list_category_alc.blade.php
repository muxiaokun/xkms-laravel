@extends('Article:base')
@section('content')
    <link rel="stylesheet" href="{{ asset('prettyPhoto/prettyPhoto.css') }}"/>
    <script type="text/javascript" src="{{ asset('prettyPhoto/jquery.prettyPhoto.js') }}"></script>
    <script type="text/javascript">
        $("#prettyPhoto").prettyPhoto({'social_tools': '', 'theme': 'facebook'});
    </script>
    <div id="prettyPhoto">
        <div class="col-sm-12">
            @foreach ($t=M_attribute_arr($category_info['attribute']) as $attrs)
                <div class="col-sm-12 btn-group btn-group-sm mb10" role="group" aria-label="...">
                    @foreach ($attrs as $attr)
                        <a class="btn btn-default @if ($attr['checked']">btn-info@endif" href="{{ $attr['link'] }})
                        {{ $attr['name'] }}
                                </a>
                                @endforeach
                                </div>
                            @endforeach
                                </div>
                                @foreach ($article_list as $index => $data)
                                <div class=" col-sm-12 pb10">
                        <div class="col-sm-2 pt10">
                            <a id="prettyPhoto{{ $index }}" href="{:M_U('article',$data['id'])}">
                                <M:Img class="img-thumbnail" src="{{ $data['thumb']|M_exists }}"/>
                            </a>
                        </div>
                        <a href="{:M_U('article',$data['id'])}">
                            <h3>{{ $data['title']|M_substr=15 }}</h3>
                        </a>
                        <span>{{ $data['description']|M_substr=100 }}</span>
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
                                    api_gallery.push($Think.root + data.src.replace(RegExp('^' + $Think.root), ''));
                                    api_titles.push(data.title);
                                    api_descriptions.push(data.description);
                                }
                                @foreach ($data['ext_info']['images_info'] as $data)
                                    prettyPhoto_push({{ $data|json_encode }});
                                @endforeach
                                $("#prettyPhoto{{ $index }}").on('click', function () {
                                    $.prettyPhoto.open(api_gallery, api_titles, api_descriptions);
                                    return false;
                                });
                            });
                        </script>
                </div>
            @endforeach
        </div>
        <M:Page name="article_list">
            <div class="col-sm-12">
                <config></config>
            </div>
        </M:Page>
@endsection