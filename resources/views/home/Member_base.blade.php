@include('Public:header')
<section class="container">
    <div class="row">
        <div class="col-sm-2 text-center">
            <ul class="nav nav-pills nav-stacked" role="tablist">
                @foreach ($left_nav as $data)
                    <li role="presentation"><a href="{{ $data['link'] }}">{{ $data['name'] }}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="col-sm-10">
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
            <div class="col-sm-12">
                @section('content')@endsection
            </div>
        </div>
    </div>
</section>
@include('Public:footer')