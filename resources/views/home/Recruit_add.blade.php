<section class="container">
    <form method="post" class="form-horizontal" role="form">
        <input type="hidden" name="id" value="{{ request('id') }}"/>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-4 control-label">@lang('common.recruit_name')</label>
                    <div class="col-sm-7">
                        <input type="text" name="name" class="form-control">
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-4 control-label">@lang('common.recruit_birthday')</label>
                    <M:Datepicker start="birthday"/>
                    <div class="col-sm-7">
                        <input type="text" name="birthday" value="{{ $start_year }}" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-4 control-label">@lang('common.recruit_sex')</label>
                    <div class="col-sm-7">
                        <select name="sex" class="form-control">
                            @foreach ($Think['lang']['recruit_sex_data'] as $data)
                                <option value="{{ $key }}">{{ $data }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-4 control-label">@lang('common.recruit_certificate')</label>
                    <div class="col-sm-7">
                        <select name="certificate" class="form-control">
                            @foreach ($Think['lang']['recruit_certificate_data'] as $data)
                                <option value="{{ $key }}">{{ $data }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-4 control-label">@lang('common.phone')</label>
                    <div class="col-sm-7">
                        <input type="text" name="ext_info[@lang('common.phone')]" class="form-control">
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-4 control-label">E-mail</label>
                    <div class="col-sm-7">
                        <input type="text" name="ext_info[E-mail]" value="" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt10">
            <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-info">
                    @lang('common.submit')
                </button>
                <a href="{:M_U('index')}" class="btn btn-default">
                    @lang('common.goback')
                </a>
            </div>
        </div>
    </form>
</section>
  