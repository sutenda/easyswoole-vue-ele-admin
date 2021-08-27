<?php


namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Utility\AnnotationDoc;

class Index extends AnnotationController
{

    public function index()
    {
        $file = EASYSWOOLE_ROOT.'/App/View/Index/index.html';
        $this->response()->write(file_get_contents($file));
    }

    function test()
    {
        $this->response()->write('this is 3311');
    }

    protected function actionNotFound(?string $action)
    {
        $file = EASYSWOOLE_ROOT.'/App/View/Index/index.html';
        $this->response()->write(file_get_contents($file));
    }

    function version(){
        $setting = \EasySwoole\EasySwoole\Config::getInstance()->getConf('CONFIG_SETTING');
        $this->writeJson(0,$setting,'success');
    }

    function doc()
    {
        $doc = new AnnotationDoc();
        $string = $doc->scan2Html(EASYSWOOLE_ROOT.'/App/HttpController');
        $this->response()->withAddedHeader('Content-type',"text/html;charset=utf-8");
        $this->response()->write($string);
    }
}