<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use App\Admin\Extensions\Form\CKEditor;
Form::extend('ckeditor', CKEditor::class);

use App\Model\AgoodsModel;

class GoodsController extends Controller
{
    use HasResourceActions;
    public function index(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('商品列表')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new AgoodsModel());

        $grid->model()->orderBy('goods_id','desc');     //倒序排序

        $grid->goods_id('商品ID');
        $grid->goods_name('商品名称');
        $grid->store('库存');
        $grid->price('价格');
        $grid->add_time('添加时间')->display(function($time){
            return date('Y-m-d H:i:s',$time);
        });

        return $grid;
    }
    protected function form()
    {
        $form = new Form(new AgoodsModel());

        $form->display('goods_id', '商品ID');
        $form->text('goods_name', '商品名称');
        $form->number('store', '库存');
        $form->currency('price', '价格')->symbol('¥');
        $form->ckeditor('content');

        return $form;
    }
    //编辑
    public function edit($id, Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }
    //添加
    public function create(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('添加')
            ->body($this->form());
    }
    public function show($id,Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('展示')
            ->body($this->form()->show($id));
    }
}
