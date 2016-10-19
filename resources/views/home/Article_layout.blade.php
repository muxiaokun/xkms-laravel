@include('home.public_header')
<section class="container">
    <div class="row">
        <div class="col-sm-8">
            @section('content')@endsection
        </div>
        <div class="col-sm-4">
            @include('home.Public_right')
        </div>
    </div>
</section>
@include('home.public_footer')