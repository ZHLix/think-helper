<?php


namespace zhlix\helper\base;


use think\Controller;
use think\Loader;

class BaseController extends Controller
{
    use Base;

    protected function assets()
    {
        $res = [];
        $path_root = implode('/', [
            $this->request->module(),
            Loader::parseName($this->request->controller())
        ]);
        $path = implode('/', [
            $path_root,
            $this->request->action()
        ]);

        // js
        if (file_exists("./assets/$path.js")) {
            // vendor
            if (file_exists("./assets/$path_root/vendor.js")) {
                $res[] = asset("$path_root/vendor.js");
            }
            // manifest
            if (file_exists("./assets/$path_root/manifest.js")) {
                $res[] = asset("$path_root/manifest.js");
            }
            $res[] = asset("$path.js");
        }

        // css
        if (file_exists("./assets/$path.css")) {
            $res[] = asset("$path.css");
        }

        $this->assign('assets', implode('', $res));
    }
}