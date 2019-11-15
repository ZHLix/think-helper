<?php


namespace zhlix\helper\base;


use think\Controller;
use think\Loader;
use think\Request;

class BaseController extends Controller
{
    use Base;

    protected function assets()
    {
        $res = [];
        $path = implode('/', [
            $this->request->module(),
            Loader::parseName($this->request->controller()),
            $this->request->action()
        ]);

        // js
        if (file_exists("./assets/$path.js")) {
            $res[] = "<script src='" . asset("$path.js") . "'></script>";
        }

        // css
        if (file_exists("./assets/$path.css")) {
            $res[] = "<link rel='stylesheet' href='" . asset("$path.css") . "'>";
        }

        $this->assign('assets', implode('', $res));
    }
}