<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use cmf\controller\AdminBaseController;
use app\admin\model\SlideItemModel;

class SlideItemController extends AdminBaseController
{
    /**
     * 幻灯片页面列表
     * @adminMenu(
     *     'name'   => '幻灯片页面列表',
     *     'parent' => 'admin/Slide/index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '幻灯片页面列表',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $content = hook_one('admin_slide_item_index_view');

        if (!empty($content)) {
            return $content;
        }

        $id      = $this->request->param('slide_id', 0, 'intval');
        $slideId = !empty($id) ? $id : 1;
        $result  = Db::name('slideItem')->where('slide_id', $slideId)->select();

        $this->assign('slide_id', $id);
        $this->assign('result', $result);
        return $this->fetch();
    }

    /**
     * 幻灯片页面添加
     * @adminMenu(
     *     'name'   => '幻灯片页面添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '幻灯片页面添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $content = hook_one('admin_slide_item_add_view');

        if (!empty($content)) {
            return $content;
        }

        $slideId = $this->request->param('slide_id');
        $this->assign('slide_id', $slideId);
        return $this->fetch();
    }

    /**
     * 幻灯片页面添加提交
     * @adminMenu(
     *     'name'   => '幻灯片页面添加提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '幻灯片页面添加提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data = $this->request->param();
        Db::name('slideItem')->insert($data['post']);
        $this->success($Think.\lang('ADD_SUCCESS'), url("slideItem/index", ['slide_id' => $data['post']['slide_id']]));
    }

    /**
     * 幻灯片页面编辑
     * @adminMenu(
     *     'name'   => '幻灯片页面编辑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '幻灯片页面编辑',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $content = hook_one('admin_slide_item_edit_view');

        if (!empty($content)) {
            return $content;
        }

        $id     = $this->request->param('id', 0, 'intval');
        $result = Db::name('slideItem')->where('id', $id)->find();

        $this->assign('result', $result);
        $this->assign('slide_id', $result['slide_id']);
        return $this->fetch();
    }

    /**
     * 幻灯片页面编辑
     * @adminMenu(
     *     'name'   => '幻灯片页面编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '幻灯片页面编辑提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();

        $data['post']['image'] = cmf_asset_relative_url($data['post']['image']);

        Db::name('slideItem')->update($data['post']);

        $this->success($Think.\lang('EDIT_SUCCESS'), url("SlideItem/index", ['slide_id' => $data['post']['slide_id']]));

    }

    /**
     * 幻灯片页面删除
     * @adminMenu(
     *     'name'   => '幻灯片页面删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '幻灯片页面删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');

        $slideItem = Db::name('slideItem')->find($id);

        $result = Db::name('slideItem')->delete($id);
        if ($result) {
            //删除图片。
//            if (file_exists("./upload/".$slideItem['image'])){
//            }
            $this->success($Think.\lang('DELETE_SUCCESS'), url("SlideItem/index", ["slide_id" => $slideItem['slide_id']]));
        } else {
            $this->error($Think.\lang('DELETE_FAILED'));
        }

    }

    /**
     * 幻灯片页面隐藏
     * @adminMenu(
     *     'name'   => '幻灯片页面隐藏',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '幻灯片页面隐藏',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id) {
            $rst = Db::name('slideItem')->where('id', $id)->update(['status' => 0]);
            if ($rst) {
                $this->success($Think.\lang('SLIDE_HIDING_SUCCEEDED'));
            } else {
                $this->error($Think.\lang('SLIDE_HIDING_FAILED'));
            }
        } else {
            $this->error($Think.\lang('DATA_TRANSFER_IN_FAILED'));
        }
    }

    /**
     * 幻灯片页面显示
     * @adminMenu(
     *     'name'   => '幻灯片页面显示',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '幻灯片页面显示',
     *     'param'  => ''
     * )
     */
    public function cancelBan()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id) {
            $result = Db::name('slideItem')->where('id', $id)->update(['status' => 1]);
            if ($result) {
                $this->success($Think.\lang('SLIDE_ENABLED_SUCCESSFULLY'));
            } else {
                $this->error($Think.\lang('SLIDE_ENABLE_FAILED'));
            }
        } else {
            $this->error($Think.\lang('DATA_TRANSFER_IN_FAILED'));
        }
    }

    /**
     * 幻灯片页面排序
     * @adminMenu(
     *     'name'   => '幻灯片页面排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '幻灯片页面排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $slideItemModel = new  SlideItemModel();
        parent::listOrders($slideItemModel);
        $this->success($Think.\lang('SORTING_UPDATE_SUCCEEDED'));
    }
}