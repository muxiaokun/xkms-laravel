
    <import file="js/M_cate_tree" />
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th></th>
                        <th class="col-sm-1" >{{ trans('common.sort') }}</th>
                        <th class="col-sm-1" >{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.show') }}</th>
                        <td class="col-sm-3  nowrap" >
                            <if condition="$batch_handle['add']">
                                <a class="btn btn-xs btn-success" href="{:U('add')}">{{ trans('common.add') }}{{ trans('common.category') }}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="article_category_list" item="article_category">
                        <tr cate_id="{$article_category.id}" parent_id="{$article_category.parent_id}" has_child="{$article_category.has_child}" >
                            <td>
<span class="glyphicon <if condition="0 lt $article_category['has_child']">glyphicon-plus<else/>glyphicon-minus</if> mlr10" onclick="M_cate_tree(this,article_category_cb);" ></span>
                                {$article_category.name}(ID:{$article_category.id})
                            </td>
                            <td onClick="M_line_edit(this);" field_id="{$article_category.id}" field="sort" link="{:U('ajax_api')}">
                                {$article_category.sort}
                            </td>
                            <td>
                                {$article_category.show}
                            </td>
                            <td class="nowrap">
                                <a class="btn btn-xs btn-primary" target="_blank" href="{:U('Home/Article/category',array('cate_id'=>$article_category['id']))}">
                                    {{ trans('common.look') }}
                                </a>
                                <if condition="$batch_handle['edit']">
                                    &nbsp;|&nbsp;
                                    <a class="btn btn-xs btn-primary" href="{:U('edit',array('id'=>$article_category['id']))}">
                                        {{ trans('common.edit') }}
                                    </a>
                                </if>
                                <if condition="$batch_handle['del']">
                                    &nbsp;|&nbsp;
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{$article_category.name}?','{:U('del',array('id'=>$article_category['id']))}')" >
                                        {{ trans('common.del') }}
                                    </a>
                                </if>
                                &nbsp;|&nbsp;
                                <a class="btn btn-xs btn-primary" href="{:U('Article/add',array('cate_id'=>$article_category['id']))}">
                                    {{ trans('common.add') }}{{ trans('common.article') }}
                                </a>
                            </td>
                        </tr>
                    </foreach>
                </table>
            </div>
        </div>
    </section>