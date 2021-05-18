<?php

namespace app\index\controller;

use think\Db;
use think\Request;

class Index
{
    public function homePageInit()
    {
        $list = [];

        $user = db('user')
            ->alias('a')
            ->where('a.id', 1)
            ->select();
        if (!$user) {
            return json('用户不存在');
        } else {
            $listRes = db('user')
                ->alias('a')
                ->where('a.id', 1)
                ->join('blog_class_list b', 'a.id=b.user_id', 'LEFT')
                ->field('b.id as class_id,b.user_id,b.class_name')
                ->order('b.id')
                ->select();
            foreach ($listRes as $x) {
                $contents = db('blog_content')
                    ->where(['user_id' => $x['user_id'], 'class_id' => $x['class_id']])
                    ->limit(5)
                    ->select();
                $x['contents'] = $contents;
                array_push($list, $x);
            }
            $loopList = db('blog_content')
                ->where('user_id', 1)
                ->limit(5)
                ->select();

            return json(['blog_user' => $user[0], 'list' => $list, 'loopList' => $loopList]);
        }
    }
    public function getClassItem()
    {
        $req = file_get_contents('php://input');
        $req = json_decode($req);
        $_classId = $req->class_id;
        $pageSize = (int)($req->pageSize);
        $currentPage = (int)($req->currentPage);

        $current = db('blog_class_list')
            ->where(['id' => (int)($_classId), 'user_id' => 1])
            ->select();
        $list = db('blog_content')
            ->where(['class_id' => (int)($_classId), 'user_id' => 1])
            ->page($currentPage, $pageSize)
            ->select();
        $total = db('blog_content')
            ->where(['class_id' => (int)($_classId), 'user_id' => 1])
            ->count();
        return json(['total' => $total, 'currentPage' => $currentPage, 'pageSize' => $pageSize, 'currentClassItem' => $current[0], 'list' => $list]);
    }
    public function getArticle()
    {
        $req = file_get_contents('php://input');
        $req = json_decode($req);
        $content_id = (int)($req->id);

        $article = db('blog_content')
            ->where('id', $content_id)
            ->select();
        $class_id = $article[0]['class_id'];
        $user_id = $article[0]['user_id'];

        $prev_data = Db::table('blog_content')
            ->where("id<$content_id AND class_id=$class_id AND user_id=$user_id")
            ->field('id,title')
            ->order('id desc')
            ->limit(1)
            ->select();
        $prev_data = empty($prev_data)?(object)([]):$prev_data[0];
        $next_data = Db::table('blog_content')
            ->where("id>$content_id AND class_id=$class_id AND user_id=$user_id")
            ->field('id,title')
            ->order('id')
            ->limit(1)
            ->select();
        $next_data = empty($next_data)?(object)([]):$next_data[0];
        return json(['article' => $article[0], 'prev' =>$prev_data , 'next' => $next_data]);
    }
}
