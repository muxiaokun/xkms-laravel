@include('home.Public_header')
<section class="container">
    <div class="row">
        <div class="col-sm-2 text-center">
            <ul class="nav nav-pills nav-stacked" role="tablist">
                @if(isset($left_nav))
                    @foreach ($left_nav as $data)
                        <li role="presentation"><a href="{{ $data['link'] }}">{{ $data['name'] }}</a></li>
                    @endforeach
                @endif
            </ul>
        </div>
        <div class="col-sm-10">
            @if(isset($position))
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        @foreach ($position as $data)
                            @if ($data['link'])
                                <li><a href="{{ $data['link'] }}">{{ $data['name'] }}</a></li>
                            @else
                                <li class="active">{{ $data['name'] }}</li>
                            @endif
                        @endforeach
                    </ol>
                </div>
            @endif
            <div class="col-sm-12">
                @section('content')
                @show
            </div>
        </div>
    </div>
</section>
@include('home.Public_footer')