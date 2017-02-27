<footer class="container text-center mt20">
    @lang('common.pfcopyright',['app_name'=>trans('common.app_name')]) <br/>
    @lang('common.version')@lang('common.colon') Backend 3.0.0
</footer>@stack('scripts')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
</body>
</html>
