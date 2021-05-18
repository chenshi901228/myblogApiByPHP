<?php

namespace app\admin\controller;

class Index
{
    public function index()
    {
        $res = db('user')->where(['userName'=>'小黑','password'=>123])->select();
        return json(['data' => $res]);
    }
}
