        <section class="container">
            <div class="row">
                {/*友情链接示例 开始*/}
                <M:D item="flinks" name="Itlink" fn="m_find_data" fn_arg="flinks" />
                <div class="col-sm-12 mb20">
                    <div class="list_title">
                        友情链接<span>Link</span>
                    </div>
                    <div class="list_link">
                        @foreach ($flinks as $data)
                        <a class="label label-default list_link" href="{$data.link}" target="{$data.link_type}">
                            {$data.link_name}
                        </a>
                        @endforeach
                    </div>
                </div>
                {/*友情链接 结束*/}
            </div>
        </section>
        <footer class="container">
            <div class="col-sm-2 lh150 hidden-xs">
                <img class="w150" src="{:M_exists('Uploads/attached/image/index/sitelogo.png')}" />
            </div>
            <div class="col-sm-7 pt30 h150 lh30" style="color:#FFF;">
                @if (C('SITE_COMPANY')){{ config('SITE_COMPANY') }}&nbsp;@endif
                @if (C('SITE_PHONE')){{ trans('common.phone') }}{{ trans('common.colon') }}{{ config('SITE_PHONE') }}&nbsp;@endif
                @if (C('SITE_TELPHONE')){{ trans('common.telphone') }}{{ trans('common.colon') }}{{ config('SITE_TELPHONE') }}&nbsp;<br />@endif
                @if (C('SITE_OTHER')){{ config('SITE_OTHER') }}&nbsp;<br />@endif
                {{ trans('common.version') }}{{ trans('common.colon') }} Home Module 1.0.0
            </div>
            <div class="col-sm-3 lh150 hidden-xs">
                <img class="w120" src="{:M_exists('Uploads/attached/image/index/siteqrcode.png')}" />
            </div>
        </footer>
    </body>
</html>