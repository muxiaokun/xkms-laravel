@include('Public:header')
    <section class="container">
        <div class="row">
            <div class="col-sm-8">
                @section('content')@endsection
            </div>
            <div class="col-sm-4">
                @include('Public:right')
            </div>
        </div>
    </section>
@include('Public:footer')