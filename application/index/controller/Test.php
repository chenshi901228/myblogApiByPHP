<?php

namespace app\index\controller;

use \think\Request;

class Test
{
    public function read()
    {
        $data = ['name' => 'thinkphp', 'url' => 'thinkphp.cn'];
        return json(['data' => $data, 'code' => 1, 'message' => '操作完成']);
    }
    public function login()
    {
        $userInfo = json_decode(file_get_contents('php://input'));
        $res = db('user')->where((array)($userInfo))->count();
        return json(['code' => $res]);
    }
    public function getSomething()
    {
        // $info = Request::instance()->header();
        if (!Request::instance()->isOptions()) {
            $res_count = db('user')->count();
            $res = db('user')->page(2, 3)->select();
            // var_dump($info['authorization']);
            return json(['data' => $res, 'count' => $res_count]);
        }
    }
    public function addUser()
    {
        $res = null;
        $addMD5 = function ($item) {
            $item->_id = md5(uniqid());
            return (array)($item);
        };
        if (!Request::instance()->isOptions()) {
            $data = json_decode(file_get_contents('php://input'));
            $list = $data->list;
            $new_list = array_map($addMD5, $list);
            $res = db('user')->insertAll($new_list);
        }
        if ($res != 0) {
            return json(['code' => $res, 'message' => '操作成功']);
        } else {
            return json(['code' => $res, 'message' => '操作失败',]);
        }
    }
    public function deleteUser()
    {
    }
}
