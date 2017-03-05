@if (request()->ajax() AND config('system.comment_switch')){{-- 下一行的div只是ajax获取元素时的外壳 --}}
<div>
    <div class="col-sm-12">
        {{ $comment_list->links('home.pagination') }}
    </div>
    <div class="col-sm-12">
        <table class="table">
            @foreach ($comment_list as $data)
                <tr>
                    <td>{{ $data['member_name'] }}@lang('common.grade')@lang('common.colon'){{ $data['level'] }}
                        [{{ $data['created_at'] }}]
                    </td>
                </tr>
                <tr>
                    <td>{{ $data['content'] }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@elseif ( config('system.comment_switch') && session('frontend_info.id'))
    <div class="col-sm-12">
        <hr/>
        <script type="text/javascript" src="{{ asset('js/M_comment_editor.js') }}"></script>
        <script type="text/javascript">
            var M_comment_editor;
            $(function () {
                var config = {
                    'main_obj': $('#comment_index'),
                    'ajax_url': '{{ route('Home::Comment::ajax_api') }}', 'route': '{{ $route }}', 'item': '{{ $item }}'
                };
                M_comment_editor = new M_comment_editor(config);
            });
        </script>
        <form onsubmit="M_comment_editor.put_data(this);return false;">
            <div class="form-group">
                <label for="exampleInputEmail1">@lang('common.grade')</label>
                <div class="radio">
                    @for($i = 0;$i < 6 ;$i++)
                        <label class="mr100">
                            <input type="radio" name="comment_level" value="{{ $i }}"
                                   @if ($i == 5)checked="checked"@endif />{{ $i }}
                        </label>
                    @endfor
                </div>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">@lang('common.comment')@lang('common.content')</label>
                <textarea name="comment_content" class="form-control" style="resize:none;"></textarea>
            </div>
            <button type="submit">@lang('common.submit')</button>
            <span class="mlr20" style="color:red;"></span>
        </form>
    </div>
    <div id="comment_index" class="row"></div>
@endif