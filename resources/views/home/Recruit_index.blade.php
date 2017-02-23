<section class="container">
    <div class="col-sm-12 text-right mb10">
        <form class="form-inline" role="form" method="get">
            @lang('common.recruit')@lang('common.keywords')
            <input type="text" name="keyword" class="form-control w100 mlr10" value="{{ request('keyword') }}"
                   onClick="$(this).val('')"/>
            <button class="btn btn-default" type="submit">
                @lang('common.search')@lang('common.recruit')
            </button>
        </form>
    </div>
    <div class="col-sm-12">
        <table class="table table-condensed table-hover">
            <tr>
                <th>@lang('common.recruit')@lang('common.name')</th>
                <th>@lang('common.re_recruit')@lang('common.number')</th>
                <th>@lang('common.recruit')@lang('common.time')</th>
                <th>@lang('common.description')</th>
                <th></th>
            </tr>
            @foreach ($recruit_list as $recruit)
                <tr>
                    <td>
                        {{ $recruit['title'] }}
                    </td>
                    <td>
                        {{ $recruit['current_portion'] }}/{{ $recruit['max_portion'] }}
                    </td>
                    <td>
                        {{ mDate($recruit['start_time']) }}
                        @lang('common.to')
                        {{ mDate($recruit['end_time']) }}
                    </td>
                    <td>
                        {{ mSubstr($recruit['explains']|strip_tags,30)}}
                    </td>
                    <td>
                        <a class="btn btn-primary btn-xs"
                           href="{{ route('Home::Recruit::edit',['id'=>$recruit['id']]) }}">
                            @lang('common.look')@lang('common.recruit')@lang('common.info')
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
        <table class="table">
            <tr>
                <td class="text-right">
                    {{ $recruit_list->links('home.pagination') }}
                </td>
            </tr>
        </table>
</section>
  