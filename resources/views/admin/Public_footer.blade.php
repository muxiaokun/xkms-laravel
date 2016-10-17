@stack('scripts')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<footer class="container text-center mt20">
    {:L('pfcopyright',array('app_name'=>APP_NAME))}<br/>
    @lang('common.version')@lang('common.colon') Admin Group 1.8.0
</footer>
</body>
</html>
