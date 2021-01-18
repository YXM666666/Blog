<?php

namespace app\system\controller;

use app\index\controller\BaseController;
use app\system\validate\User;
use think\Db;

/**
 * Class Blog
 * @package app\system\controller
 */
class Blog extends BaseController
{

    /**
     *博文表内容增加接口
     */
    public function increase()
    {
        $data = $this->request->post();

        $valdate = new User();
        if (!$valdate->scene('increase')->check($data)) {
            return $this->resFail($valdate->getError());
        }

        try {
            $user = Db::name('tp_blog')
                ->where('title', $data['title'])
                ->find();

            if (!empty($user)) {
                return $this->resFail('标题已存在', 1);
            }

            Db::name('tp_blog')
                ->insert($data);

            $result = [
                'id' => Db::name('tp_user')->getLastInsID(),
                'title' => $data['title'],
                'content' => $data['content'],
                'picture' => $data['picture'],
                'author' => $data['author'],
                'volume' => $data['volume'],
                'category' => $data['category'],
                'create_time' => date('Y-m-d H:i:s')];

            return $this->resSuccess($result, '增加成功');

        } catch (\Exception $e) {
            return $this->resFail('数据异常' . $e->getMessage());
        }
    }

    /**
     * 更改博文内容
     * 有修改数据修改成功则返回相应数据，没有修改数据则返回空列表
     */
    public function change()
    {
        $data = $this->request->post();

        $valdate = new User();
        if (!$valdate->scene('change')->check($data)) {
            return $this->resFail($valdate->getError());
        }

        $updata = [];

        if (!empty($data['title'])) {
            $updata['title'] = $data['title'];
        }

        if (!empty($data['content'])) {
            $updata['content'] = $data['content'];
        }

        if (!empty($data['picture'])) {
            $updata['picture'] = $data['picture'];
        }

        if (!empty($data['author'])) {
            $updata['author'] = $data['author'];
        }

        if (!empty($data['volume'])) {
            $updata['volume'] = $data['volume'];
        }

        if (!empty($data['category'])) {
            $updata['category'] = $data['category'];
        }

        try {

            $res = Db::name('tp_blog')
                ->where('id', $data['id'])
                ->where('is_del', '=', 0)
                ->find();

            if (empty($res)) {
                return $this->resSuccess([], '修改成功');
            }

            $result = $res = Db::name('tp_blog')
                ->where('title', $data['title'])
                ->where('is_del', '=', 0)
                ->find();

            if (!empty($result)) {
                return $this->resFail('标题已存在', 1);
            }

            Db::name('tp_blog')
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
     * 这是一个删除博文内容接口,删除的数据is_del = 1
     */
    public function delete()
    {
        $data = $this->request->post();

        $valdate = new User();
        if (!$valdate->scene('delete')->check($data)) {
            return $this->resFail($valdate->getError());
        }

        try {
            Db::name('tp_blog')
                ->where('id', $data['id'])
                ->update(['is_del' => '1']);
            return $this->resSuccess([], '删除成功');

        } catch (\Exception $e) {
            return $this->resFail('数据异常' . $e->getMessage());
        }
    }

    /**
     * 这是一个查询博文内容接口
     * 内容和标题模糊查询，作者和所属标题精准查询
     */
    public function select()
    {
        $data = $this->request->post();

        $valdate = new User();
        if (!$valdate->scene('select')->check($data)) {
            return $this->resFail($valdate->getError());
        }

        $condition = [];

        if (!empty($data['title'])) {
            $condition[] = ['title', 'like', "%{$data['title']}%"];
        }

        if (!empty($data['content'])) {
            $condition[] = ['content', 'like', "%{$data['content']}%"];
        }

        if (!empty($data['author'])) {
            $condition[] = ['author', '=', $data['author']];
        }

        if (!empty($data['category'])) {
            $condition[] = ['category', '=', $data['category']];
        }

        try {
            $res = Db::name('tp_blog')
                ->where($condition)
                ->order('volume', 'desc')
                ->where('is_del','=',0)
                ->select();
            return $this->resSuccess($res, '查询成功');

        } catch (\Exception $e) {
            return $this->resFail('数据异常' . $e->getMessage());
        }
    }
}