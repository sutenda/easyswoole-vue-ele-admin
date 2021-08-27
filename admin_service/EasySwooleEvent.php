<?php


namespace EasySwoole\EasySwoole;


use App\Crontab\CheckLogsTableCrontab;
use App\Crontab\CheckPlayerNotLogoutCrontab;
use App\Crontab\LoadGameListCacheCrontab;
use App\Crontab\UserWinLeaderboardCrontab;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Crontab\Crontab;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\FileWatcher\FileWatcher;
use EasySwoole\FileWatcher\WatchRule;
use EasySwoole\ORM\Db\Config;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\Redis\Config\RedisConfig;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        //加载数据库
        self::loadDataBaseList();
        self::loadRedisList();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        self::hotReload();//生产环境禁用，需要注释

        // 获取 Render 配置
        $renderConfig = \EasySwoole\Template\Render::getInstance()->getConfig();
        // 设置 渲染引擎模板驱动
        $renderConfig->setRender(new \App\RenderDriver\Smarty());
        // 设置 渲染引擎进程 Socket 存放目录，默认为 getcwd()
        $renderConfig->setTempDir(EASYSWOOLE_TEMP_DIR);
        // 注册进程到 EasySwoole 主服务
        \EasySwoole\Template\Render::getInstance()->attachServer(\EasySwoole\EasySwoole\ServerManager::getInstance()->getSwooleServer());
    }

    private static function loadDataBaseList(){
        $dbConfigPath = \EasySwoole\EasySwoole\Config::getInstance()->getConf('OTHER_CONFIG');
        $environment = \EasySwoole\EasySwoole\Config::getInstance()->getConf('CONFIG_SETTING');
        $dbConfigPath.=($environment.'/db_config/mysql.json');
        var_dump('数据库配置：'.$dbConfigPath);
        $configStr = file_get_contents($dbConfigPath);
        $dbConfigs = json_decode($configStr,true);
        foreach ($dbConfigs as $key => $value)
        {
            $config = new Config();
            $config->setDatabase($value['db']);
            $config->setUser($value['username']);
            $config->setPassword($value['password']);
            $config->setHost($value['host']);
            $config->setPort($value['port']);
            $config->setTimeout(15); // 超时时间
            $config->setMinObjectNum(5); //设置最小连接池存在连接对象数量
            $config->setMaxObjectNum(100); //设置最大连接池存在连接对象数量
            $config->setReturnCollection(true);

            DbManager::getInstance()->addConnection(new Connection($config),$key);
        }

    }


    private static function loadRedisList()
    {
        $redisConfigPath = \EasySwoole\EasySwoole\Config::getInstance()->getConf('OTHER_CONFIG');
        $environment = \EasySwoole\EasySwoole\Config::getInstance()->getConf('CONFIG_SETTING');
        $redisConfigPath.=($environment.'/db_config/redis.json');
        var_dump('redis配置：'.$redisConfigPath);
        $configStr = file_get_contents($redisConfigPath);
        $redisConfigs = json_decode($configStr,true);
        $defaultRedis = $redisConfigs['default'];
        $config = new \EasySwoole\Pool\Config();
        try {
            $config = $config->setMaxObjectNum(100);
        } catch (\EasySwoole\Pool\Exception\Exception $e) {
        }
        $redisConfig = new RedisConfig(
            [
                'host'      => $defaultRedis['host'],
                'port'      => $defaultRedis['port'],
                'auth'      => $defaultRedis['password'],
                'db'        => 0,
                'serialize' => RedisConfig::SERIALIZE_NONE
            ]
        );
        try {
            \EasySwoole\Pool\Manager::getInstance()->register(new \App\Pool\RedisPool($config, $redisConfig), 'redis');
        } catch (\EasySwoole\Pool\Exception\Exception $e) {
        }
    }

    //开发过程热重载
    private static function hotReload(){
        $watcher = new FileWatcher();
        $rule = new WatchRule(EASYSWOOLE_ROOT . "/App"); // 设置监控规则和监控目录
        $watcher->addRule($rule);
        $watcher->setOnChange(function () {
            Logger::getInstance()->info('file change ,reload!!!');
            ServerManager::getInstance()->getSwooleServer()->reload();
        });
        $watcher->attachServer(ServerManager::getInstance()->getSwooleServer());
    }
}