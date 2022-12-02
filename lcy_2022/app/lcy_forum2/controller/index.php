<?php

namespace app\lcy_forum2\controller;

use think\facade\View;
use think\facade\Db;
use app\BaseController;
use think\facade\Session;

use think\facade\Config;
use think\facade\Cookie;
use think\facade\Route;

class index extends BaseController
{
    use \liliuwei\think\Jump;


    // 首页
    public function index()
    {

        $this->check();
        $sec = $this->Section();
        $uimg = Db::name('user')
            ->field('uimg')
            ->where('unick', Session::get('unick'))
            ->find();

        dump($sec);
        $res = Db::view('mes', 'mid,mtitle,munick,mcreateat,mcontent')
            ->view('user', 'uimg', 'mes.munick=user.unick')
            ->order('mcreateat', 'desc')
            ->select();
        dump($res);

        session('user',  $uimg);

        return view('', ['mes' => $res, 'sec' => $sec]);
    }


    //登录
    public function dologin()
    {

        $username = trim($this->request->param('unicks'));
        $password = md5(trim($this->request->param('upass')));
        // dump($password);
        // die;
        $res = Db::table('lcy_user')
            ->where('unick', $username)
            ->where('upa', $password)
            ->value('uimg');
        if ($res == null) {
            $this->error('登录失败', 'user/login');
        } else {
            session('unick', $username);
            Cookie::set('uimg', $res);
            $this->success('欢迎您登录  ' . $username, 'index/index');
        }
    }
    // 帖子信息

    public function doRes()
    {
        $this->check();
        return view();
    }
    // 发表帖子
    public function dopost()
    {
        $this->check();
        $mtitle = $this->request->param('mtitle', '', 'trim,htmlspecialchars');
        $mcontent = trim($this->request->param('mcontent', '', 'trim'));
        $section = $this->request->param('section');

        $data = [
            'mtitle' =>  $mtitle,
            'mcontent' => $mcontent,
            'munick' => Session::get('unick'),
            'msid' => $section,
        ];
        $post = Db::name('mes')->insert($data);
        if ($post == 1) {
            $this->success('发表成功', 'index/index');
        } else {
            $this->error('发表失败');
        }
        return view();
    }

    // 更新密码
    public function updatepass()
    {
        $this->check();
        $update = Db::name('user')->where('unick', Session::get('unick'))->update(['upa' => md5(1)]);
        dump($update);
        if ($update == 1) {
            $this->success('更改密码成功', 'user/login');
        } else {
            $this->error('更改失败', 'index/index');
        }
    }
    // 更新密码
    public function updatePassword()
    {
        $upPass = $this->request->param('password', '', 'trim,md5');
        $user = $this->request->param('username', '', 'trim');
        $Perviouspassword = md5(trim($this->request->param('Perviouspassword')));
        $res = Db::name('user')
            ->where('unick', $user)
            ->where('upa', $Perviouspassword)
            ->find();
        dump($res);
        if ($res !== null) {
            $update = Db::name('user')
                ->where('unick', $user)
                ->update(['upa' => $upPass]);
            if ($update !== 0) {
                $this->success('修改成功！', 'user/login');
            } else {
                $this->error('修改密码失败', 'user/forgetPage');
            }
        } else {
            $this->error('原先密码输入错误！', 'user/forgetPage');
        }
    }

    public function post()
    {
        $this->check();
        return view();
    }
    // 帖子详细页面
    public function detail()
    {

        $this->check();
        $mid = $this->request->param('mid');
        $mes = Db::view('mes', 'mtitle,mcontent,munick,mcreateat')
            ->view('user', 'uimg', 'mes.munick=user.unick')
            ->view('section', 'sname', 'mes.msid=section.sid')
            ->where('mid', $mid)

            ->select();
        $sName = $mes[0]['sname'];
        $res = Db::view('res', 'rcontent,runick,rcreateat')
            ->view('mes', 'mid', 'mes.mid=res.rmid')
            ->view('user', 'uimg', 'user.unick=res.runick')
            ->where('rmid', $mid)
            ->select();
        dump($res);

        return view('detail', ['mes' => $mes, 'res' => $res, 'sName' => $sName]);
    }
    public function me()
    {
        $this->check();
        return view();
    }
    // 上传头像
    public function upme()
    {
        $this->check();
        $file = request()->file('uimg');
        $path = $this->app->getRootPath();
        $savename = \think\facade\Filesystem::disk('public')->putFile('', $file);
        $filename = $path . 'public/static/lcy_forum2/upload/' . $savename;


        if (file_exists($filename)) {
            $res = Db::name('user')
                ->where('unick', Session::get('unick'))
                ->update(['uimg' => $savename]);

            if ($res == 1) {
                // 如果存在上传头像 替换掉
                if (strlen(Cookie::get('uimg')) > 7) {
                    unlink($path . 'public/static/lcy_forum2/upload/' . Cookie::get('uimg'));
                }
                // 重新设置cookie
                Cookie::set('uimg', $savename);
                $this->success('上传成功', 'index/index');
            } else {
                unlink($filename);
                $this->error('上传失败');
            }
        } else {
            $this->error('上传失败');
        }
    }
    // 我的帖子
    public function mypost()
    {
        $this->check();
        $res =
            Db::name('mes')
            ->field('mid,mtitle,mcontent,mcreateat,munick')
            ->where('munick', Session::get('unick'))
            ->order('mid', 'desc')

            ->select();
        dump($res);
        return view('mypost', [
            'list' => $res
        ]);
    }
    // 板块详细
    public function block($sid = 0)
    {

        $sec = $this->Section();
        $sid  = $this->request->param('sid');
        if ($sid == 0) {
            $sName = '全部帖子';
            $mes = Db::view('mes', 'mid,mtitle,munick,mcreateat')
                ->view('user', 'uimg', 'mes.munick = user.unick')
                ->order('mcreateat', 'desc')
                ->select('');
        } else {
            foreach ($sec as $key => $value) {
                if ($value['sid'] == $sid) {
                    $sName = $value['sname'];
                    break;
                }
            }
            $mes = Db::view('mes', 'mid,mtitle,munick,mcreateat,mcontent')
                ->view('user', 'uimg', 'mes.munick=user.unick')
                ->where('msid', $sid)
                ->order('mcreateat', 'desc')
                ->select();
            dump($mes);
        }


        return view('', [
            'list' => $mes, 'sName' => $sName
        ]);
    }
    public function reply()
    {

        $rcontent = $this->request->param('content');
        $runick = Session::get('unick');
        $rmid = $this->request->param('mid');

        $data = ['runick' => $runick, 'rcontent' => $rcontent, 'rmid' => $rmid];

        $res = Db::name('res')
            ->insert($data);

        if ($res == 1) {
            $this->success('回复成功', url('index/detail', ['mid' => $rmid]));
        } else {
            $this->error('回复失败', '');
        }
    }
}