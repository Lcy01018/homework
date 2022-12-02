<?php

namespace app\lcy_forum2\controller;

use think\facade\Config;
use think\facade\View;

class Test 
{

    public function ShowConfig()
    {
        $getData = Config::get('database');
        dump($getData);
    }

   public function test(){
       return view('index/cc',[
            'data' => [
                'name' => 'asdsd'
            ]
        ]
       );
   }
     
}