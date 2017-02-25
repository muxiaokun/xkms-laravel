@include('home.Public_header')
<section class="container">
    <div class="row">
        <div class="col-sm-8">
            @section('content')
            @show
        </div>
        <div class="col-sm-4">
            @include('home.Public_right')
        </div>
    </div>
</section>
@include('home.Public_footer')