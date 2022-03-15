<?php

namespace app\user\behavior;

class UserLog
{

    public function run(&$params)
    {
        if (request()->isPost()) {
            \app\common\model\user\Log::record();
        }
    }

}
