<?php


namespace App\RenderDriver;

use EasySwoole\Template\RenderInterface;

class Smarty implements RenderInterface
{
    private $smarty;

    function __construct()
    {
        $temp = sys_get_temp_dir();
        $this->smarty = new \Smarty();
        $this->smarty->setTemplateDir(EASYSWOOLE_ROOT . '/App/View/');
        $this->smarty->setCacheDir("{$temp}/smarty/cache/");
        $this->smarty->setCompileDir("{$temp}/smarty/compile/");
    }

    public function render(string $template, ?array $data = null, ?array $options = null): ?string
    {
        foreach ($data as $key => $item) {
            $this->smarty->assign($key, $item);
        }
        return $this->smarty->fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false,
            $merge_tpl_vars = true, $no_output_filter = false);
    }

    public function onException(\Throwable $throwable, $arg): string
    {
        $msg = "{$throwable->getMessage()} at file:{$throwable->getFile()} line:{$throwable->getLine()}";
        trigger_error($msg);
        return $msg;
    }
}