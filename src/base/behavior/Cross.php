<?php

namespace zhlix\helper\base\behavior;


class Cross
{
    public function run()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token, Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE');
        header('Access-Control-Allow-Credentials: true');

        if (request()->isOptions()) {
            exit();
        }
    }
}
