<?php

namespace app\system\controller;

use app\index\controller\BaseController;
use app\system\validate\Check;
use think\Db;

/**
 * Class Blog
 * @package app\system\controller
 */
class Category extends BaseController
{

    /**
     *博文类别表内容增加接口
     */
    public function increase()
    {
        $data = $this->request->post();

        $valdate = new Check();
        if (!$valdate->scene('increase')->check($data)) {
            return $this->resFail($valdate->getError());
        }

        try {
            $user = Db::name('tp_blog_category')
                ->where('name', $data['name'])
                ->find();

            if (!empty($user)) {
                return $this->resFail('类别名称已存在', 1);
            }

            Db::name('tp_blog_category')
                ->insert($data);

            $result = [
                'id' => Db::name('tp_user_category')->getLastInsID(),
                'name' => $data['name'],
                'create_time' => date('Y-m-d H:i:s')];

            return $this->resSuccess($result, '增加成功');

        } catch (\Exception $e) {
            return $this->resFail('数据异常' . $e->getMessage());
        }
    }

    /**
     * 更改博文类别
     * 有修改数据修改成功则返回相应数据，没有修改数据则返回空列表
     */
    public function change()
    {
        $data = $this->request->post();

        $valdate = new Check();
        if (!$valdate->scene('change')->check($data)) {
            return $this->resFail($valdate->getError());
        }

        $updata = [];

        if (!empty($data['name'])) {
            $updata['name'] = $data['name'];
        }

        try {

            $res = Db::name('tp_blog_category')
                ->where('id', $data['id'])
                ->where('is_del', '=', 0)
                ->find();

            if (empty($res)) {
                return $this->resSuccess([], '修改成功');
            }

            $result = $res = Db::name('tp_blog_category')
                ->where('name','<>', $data['name'])
                ->where('is_del', '=', 0)
                ->find();

            if (!empty($result)) {
                return $this->resFail('类别名称已存在', 1);
            }

            Db::name('tp_blog_category')
                ->where('id', $data['id'])
                ->where('is_del', '=', 0)
                ->update($updata);

            $data['create_time'] = date('Y-m-d H:i:s');
            return $this->resSuccess($data, '修改成功');

        } catch (\Exception $e) {
            return $this->resFail('数据异常' . $e->getMessage());
        }
    }

    /**
     * 这是一个删除博文类别接口,删除的数据is_del = 1
     */
    public function delete()
    {
        $data = $this->request->post();

        $valdate = new Check();
        if (!$valdate->scene('delete')->check($data)) {
            return $this->resFail($valdate->getError());
        }

        try {
            Db::name('tp_blog_category')
                ->where('id', $data['id'])
                ->update(['is_del' => '1']);
            return $this->resSuccess([], '删除成功');

        } catch (\Exception $e) {
            return $this->resFail('数据异常' . $e->getMessage());
        }
    }

    /**
     * 这是一个查询博文类别接口
     * 内容和标题模糊查询，
     */
    public function select()
    {
        $data = $this->request->post();

        $condition = [];

        if (!empty($data['name'])) {
            $condition[] = ['name', 'like', "%{$data['name']}%"];
        }

        try {
            $res = Db::name('tp_blog_category')
                ->where($condition)
                ->order('create_time', 'desc')
                ->where('is_del','=',0)
                ->select();
            
            return $this->resSuccess($res, '查询成功');

        } catch (\Exception $e) {
            return $this->resFail('数据异常' . $e->getMessage());
        }
    }
}