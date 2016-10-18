@include('admin.Public_header')
<body>
<script type="text/javascript"
        src="{{ route('Minify',['type'=>'js','files'=>'M_history','lang'=>'common']) }}"></script>
<script>
    $(function () {
        if (M_history) {
            var config = {
                'out_obj': $("#left_nav_menu")
            };
            new M_history(config);
        }
        else {
            var left_nav_menu = $("#left_nav_menu");
            left_nav_menu.accordion({
                heightStyle: "content"
            });
        }
    });
</script>
<section class="left_nav">
    <div id="left_nav_menu" class="accordion">
        @if (count($installMenu) > 0)
            @foreach ($installMenu as $group_name => $actions)
                <h3>{{ $group_name }}</h3>
                <ul class="nav text-center" role="tablist">
                    @foreach ($actions as $link => $name)
                        <li role="presentation">
                            <a class="fs12" href="{{ route($link) }}" target="main">{{ $name }}</a>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        @else
            <h3>@lang('common.none')@lang('common.privilege')</h3>
            <ul class="nav text-center" role="tablist">
                <li role="presentation">
                    <a class="fs12" href="javascript:void(0);">@lang('common.not_action_privilege')</a>
                </li>
            </ul>
        @endif
        <h3>技术支持</h3>
        <ul class="nav text-center" role="tablist">
            <li role="presentation">
                <a class="fs12" href="javascript:void(0);">电话：0991-5819279</a>
            </li>
            <li role="presentation">
                <a class="fs12" href="javascript:void(0);">电话：0991-5819271</a>
            </li>
            <li role="presentation">
                <a class="fs12" href="http://www.xjhywh.cn" target="_blank">官网：www.xjhywh.cn</a>
            </li>
        </ul>
    </div>
</section>
</body>
</html>