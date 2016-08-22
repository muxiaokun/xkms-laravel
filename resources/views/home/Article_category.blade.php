<extend name="Article:base" />
<block name="content">
                <div class="col-sm-12 text-center">
                    <h2>{$category_info.name}</h2>
                </div>
                <div class="col-sm-12 text-center">
                    <button id="big_obj" class="btn btn-sm btn-default" >{{ trans('common.tobig') }}{{ trans('common.font') }}</button>
                    <button id="small_obj" class="btn btn-sm btn-default">{{ trans('common.tosmall') }}{{ trans('common.font') }}</button>
                </div>
                <div id="content" class="col-sm-12 mt20">
                    {$category_info.content}
                </div>
                <import file="js/M_fontsize" />
                <script type="text/javascript">
                    $(function(){
                        var config = {
                            'out_obj':$('#content'),
                            'big_obj':$('#big_obj'),
                            'small_obj':$('#small_obj')
                        };
                        new M_fontsize(config);
                    });
                </script>
</block>