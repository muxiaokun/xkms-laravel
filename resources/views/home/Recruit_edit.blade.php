@extends('home.layout')
@section('body')
    <section class="container">
        <div class="col-sm-12 text-center">
            <h2>{{ $recruit_info['title'] }}</h2>
        </div>
        <div class="col-sm-12 text-center mtb5">
            @lang('recruit.re_recruit')@lang('common.number')@lang('common.colon')
            {{ $recruit_info['current_portion'] }}/{{ $recruit_info['max_portion'] }}
            &nbsp;&nbsp;&nbsp;&nbsp;
            @lang('common.time')@lang('common.colon')
            {{ $recruit_info['start_time'] }}
            @lang('common.to')
            {{ $recruit_info['end_time'] }}
        </div>
        <div class="col-sm-12 text-center mtb5">
            @foreach ($recruit_info['ext_info'] as $key => $data)
                @if ($data)
                    &nbsp;&nbsp;<span class="badge">
                                    {{ $key }}@lang('common.colon'){{ $data }}
                                </span>&nbsp;&nbsp;
                @endif
            @endforeach
        </div>
        <div class="col-sm-12 mtb10">
            {!! htmlspecialchars_decode($recruit_info['explains']) !!}
        </div>
        <div class="col-sm-12 text-center mtb10">
            <a class="btn btn-default" href="{{ route('Home::Recruit::add',['id'=>$recruit_info['id']]) }}">
                @lang('common.submit')@lang('recruit.recruit')@lang('common.info')
            </a>
        </div>
    </section>
@endsection
  