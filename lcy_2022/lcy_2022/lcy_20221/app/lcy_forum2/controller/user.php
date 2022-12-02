<?php

namespace app\lcy_forum2\controller;

use think\facade\View;
use think\facade\Db;
use app\BaseController;

use liliuwei\think\Jump;
use think\facade\Route;
use think\facade\Session;

class user extends BaseController
{
    use \liliuwei\think\Jump;

    public function reg()
    {
        return View::fetch();
    }

    public function login()
    {

        return View::fetch();
    }
    public function logOut()
    {
        $this->check();
        Session::delete('unick');
        $this->success('注销成功', 'user/login');
    }
    // 查询帖子基本信息和用户头像
    public function view()
    {
        $res = Db::view('mes', 'mtitle,munick,mcreateat')
            ->view('user', 'uimg', 'mes.munick=user.unick')
            ->where('msid', 1)
            ->select();
        dump($res);
    }
    // 查询帖子详细信息和用户头像
    public function detail()
    {
        $res = Db::view('mes', 'mtitle,mcontent,munick,mcreateat')
            ->view('user', 'uimg', 'mes.munick=user.unick')
            ->view('section', 'sname', 'section.sid = mes.msid')
            ->where('mid', 3)
            ->find();
        dump($res);
    }
    // 注册账号和密码
    public function doReg()
    {

        $username = $this->request->param('username', '', 'trim,htmlspecialchars');
        $password = $this->request->param('password', '', 'trim,md5');
        $uemail = $this->request->param('uemail', '', 'trim,htmlspecialchars');
        $utel = $this->request->param('utel', '', 'trim');
        $user = Db::name('user')
            ->field('unick,utel,uemail')
            ->whereOr('unick', $username)
            ->whereOr('uemail', $uemail)
            ->whereOr('utel', $utel)
            ->find();
        if ($user !== null) {
            if ($username == $user['unick']) {
                $this->error('名字已经被注册', 'user/reg');
            }
            if ($uemail == $user['uemail']) {
                $this->error('邮箱已经被注册', 'user/reg');
            }
            if ($utel == $user['utel']) {
                $this->error('号码已经被注册', 'user/reg');
            }
        }
        $data = [
            'unick' => $username,
            'upa' => $password,
            'uimg' => 'me.png',
            'utel' => $utel,
            'uemail' => $uemail
        ];

        $res =  Db::name('user')->insert($data);

        if ($res == 1) {
            $this->success('注册成功', 'user/login');
        } else {
            $this->error('注册失败', 'user/reg');
        }
    }
    public function forgetPage()
    {

        return view();
    }
}