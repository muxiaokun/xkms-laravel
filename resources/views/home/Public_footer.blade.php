<section class="container">
    <div class="row">
        {{-- 友情链接示例 开始 --}}
        @php
            $flinks = App\Model\Itlink::mFindData('test');
        @endphp
        <div class="col-sm-12 mb20">
            <div class="list_title">
                友情链接<span>Link</span>
            </div>
            <div class="list_link">
                @if(isset($flinks))
                @foreach ($flinks as $data)
                        <a class="label label-default list_link" href="{{ $data['itl_link'] }}"
                           target="{{ $data['itl_target'] }}">
                            {{ $data['itl_text'] }}
                    </a>
                @endforeach
                @endif
            </div>
        </div>
        {{-- 友情链接 结束 --}}
    </div>
</section>
<footer class="container">
    <div class="col-sm-2 lh150 hidden-xs">
        <img class="w150" src="{{ mExists('css/fimages/sitelogo.png') }}"/>
    </div>
    <div class="col-sm-7 pt30 h150 lh30" style="color:#FFF;">
        @if (config('system.site_company')){{ config('website.site_company') }}&nbsp;@endif
        @if (config('system.site_phone'))@lang('common.phone')@lang('common.colon'){{ config('website.site_phone') }}
        &nbsp;@endif
        @if (config('system.site_telphone'))@lang('common.telphone')@lang('common.colon'){{ config('website.site_telphone') }}
        &nbsp;
        <br/>@endif
        @if (config('system.site_other')){{ config('website.site_other') }}&nbsp;<br/>@endif
        @lang('common.version')@lang('common.colon') Home Module 1.0.0
    </div>
    <div class="col-sm-3 lh150 hidden-xs">
        <img class="w120" src="{{ mExists('css/fimages/siteqrcode.png') }}"/>
    </div>
</footer>
@stack('scripts')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
</body>
</html>