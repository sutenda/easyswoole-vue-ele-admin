<?php
namespace App\Helper;


use EasySwoole\Pool\Exception\Exception;
use EasySwoole\Pool\Manager;

class RedisHelper
{
    private static $logDir = "";
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance) {
            return self::$instance;
        }
        self::$logDir = \EasySwoole\EasySwoole\Config::getInstance()->getConf('MY_LOG_PATH').'redis';
        return self::$instance = new RedisHelper();
    }

    public function redisGet($key)
    {
        $redis= Manager::getInstance()->get('redis')->getObj();
        //var_dump(\EasySwoole\Pool\Manager::getInstance()->get('redis')->status());
        if(empty($redis)){
            var_dump('-------------redis error -------------');
            var_dump(Manager::getInstance()->get('redis')->status());
            var_dump('-------------redis error end -------------');
        }
        try {
            $res=$redis->get($key);
        }catch (Exception $e){

        } finally {
            Manager::getInstance()->get('redis')->recycleObj($redis);
        }

        return $res;
    }

    public function redisSet($key,$value,$ex=600){
        $redis= Manager::getInstance()->get('redis')->getObj();

        try {
            $redis->set($key,$value,$ex);
        }catch (Exception $e){

        } finally {
            Manager::getInstance()->get('redis')->recycleObj($redis);
        }
        //\EasySwoole\Pool\Manager::getInstance()->get('redis')->recycleObj($redis);
        return true;
    }

    public function redisDel($key){
        $redis= Manager::getInstance()->get('redis')->getObj();
        try {
            $redis->del($key);
        }catch (Exception $e){

        } finally {
            Manager::getInstance()->get('redis')->recycleObj($redis);
        }

        return true;
    }

    public function redisGetKeys($key)
    {
        $redis= Manager::getInstance()->get('redis')->getObj();
        //var_dump(\EasySwoole\Pool\Manager::getInstance()->get('redis')->status());
        if(empty($redis)){
            var_dump('-------------redis error -------------');
            var_dump(Manager::getInstance()->get('redis')->status());
            var_dump('-------------redis error end -------------');
        }
        try {
            $res=$redis->keys($key.'*');
        }catch (Exception $e){

        } finally {
            Manager::getInstance()->get('redis')->recycleObj($redis);
        }

        return $res;
    }

    /**
     * 重复请求
     * @param $key
     * @param $type
     * @return array|false[]
     * @throws \Throwable
     */
    public function lockRequest($key,$type){
        $redis= Manager::getInstance()->get('redis')->getObj();
        try {
            $result = $redis->get($key);
            if(empty($result)){
                $token = mt_rand();
                $lockKey = 'lock:slot:load:'.$type;
                $lockExpire = 5;
                $this->log_slot('加锁开始:'.$key.' type:'.$type,'add-lock.log');
                //加锁，只有一个请求往下，其余的请求过滤
                if ($this->lock($redis, $lockKey, $token, $lockExpire)) {
                    $this->log_slot('加锁成功:'.$key.' type:'.$type,'add-lock.log');
                    return ['next'=>true,'data'=>['lockKey'=>$lockKey,'token'=>$token]];
                    //RedisHelper::unlock($redis, $lockKey, $token);
                } else {
                    $this->log_slot('加锁失败:'.$key.' type:'.$type,'add-lock.log');
                    return ['next'=>false];
                }
            }
            Manager::getInstance()->get('redis')->recycleObj($redis);
        }catch (Exception $e){

        } finally {
            Manager::getInstance()->get('redis')->recycleObj($redis);
        }
        return ['next'=>false];
    }

    /**
     * 解锁重复请求
     * @param $data
     * @throws \Throwable
     */
    public function unlockRequest($data){
        $lockKey = $data['lockKey'];
        $token = $data['token'];
        $redis= Manager::getInstance()->get('redis')->getObj();
        try {
            $this->unlock($redis, $lockKey, $token);
        }catch (Exception $e){

        } finally {
            Manager::getInstance()->get('redis')->recycleObj($redis);
        }
    }

    /**
     * redis加锁
     * @param $redisClient \Redis
     * @param $lockKey
     * @param $token
     * @param $expire
     * @return bool
     */
    private function lock($redisClient, $lockKey, $token, $expire): bool
    {
        try {
            $ret = $redisClient->set($lockKey, $token, ['NX', 'EX' => $expire]);
        }catch (Exception $e){
            var_dump("----------lock---------");
            var_dump($e);
            $ret = false;
        } finally {
            Manager::getInstance()->get('redis')->recycleObj($redisClient);
        }
        return $ret;
    }

    /**
     * redis释放锁
     * @param $redisClient \Redis
     * @param $lockKey
     * @param $token
     * @return mixed
     */
    private function unlock($redisClient, $lockKey, $token)
    {
        $script = "if redis.call('get',KEYS[1]) == ARGV[1] 
        then return redis.call('del',KEYS[1]) 
        else return 0 
        end";
        try {
            $ret = $redisClient->rawCommand(['EVAL',$script,'1',$lockKey,$token]);
        }catch (Exception $e){
            var_dump("----------unlock---------");
            var_dump($e);
            $ret = false;
        } finally {
            Manager::getInstance()->get('redis')->recycleObj($redisClient);
        }
        return  $ret;
    }


    /**
     * 加锁
     * @param $key
     * @return bool
     * @throws \Throwable
     */
    public function lockSame($key){
        $ret = false;
        $redis= Manager::getInstance()->get('redis')->getObj();
        $token = mt_rand();
        $lockKey = $key;
        $lockExpire = 5;
        try {
            if($this->lock($redis, $lockKey, $token, $lockExpire)){
                $ret = true;
            }
        }catch (Exception $e){
            var_dump("----------lockSame---------");
            var_dump($e);
            $ret = false;
        } finally {
            Manager::getInstance()->get('redis')->recycleObj($redis);
        }
        return $ret;
    }


    /**
     * 记录日志文件
     * @param $msg
     * @param string $file
     * @param false $isTry
     */
    private function log_slot($msg, $file = '',$isTry=false)
    {
        $filePrefix = '';
        if($isTry){
            $filePrefix = 'try_';
        }
        ini_set('date.timezone', 'PRC');
        $date = date('Y-m-d',time());
        if(!empty($file)){
            $file = self::$logDir.$filePrefix.$file."-$date.log";
        }
        else{
            $file  = self::$logDir.$filePrefix."slot-$date.log";
        }
        @file_put_contents($file, date("[Y-m-d H:i:s] ").$msg."\n", FILE_APPEND);
    }


    /**
     * 赢取历史记录列表
     * @param $key
     * @param $value
     * @return bool
     * @throws \Throwable
     */
    public function redisListForWinHistory($key,$value){
        $redis= Manager::getInstance()->get('redis')->getObj();
        try {
            $redis->lpush($key,$value);
            if($redis->lLen($key)>20)
            {
                $redis->rPop($key);
            }
        }catch (Exception $e){

        } finally {
            Manager::getInstance()->get('redis')->recycleObj($redis);
        }

        return true;
    }


    public function redisGetListForWinHistory($key){
        $redis= Manager::getInstance()->get('redis')->getObj();
        try {
            return $redis->lrange($key,0,-1);
        }catch (Exception $e){

        } finally {
            Manager::getInstance()->get('redis')->recycleObj($redis);
        }

        return true;
    }


    public function redisCallBack($func)
    {
        $redis= Manager::getInstance()->get('redis')->getObj();
        if(empty($redis)){
            var_dump('-------------redis error -------------');
            var_dump(Manager::getInstance()->get('redis')->status());
            var_dump('-------------redis error end -------------');
        }
        try {
            call_user_func($func,$redis);
        }catch (Exception $e){
            return false;
        } finally {
            Manager::getInstance()->get('redis')->recycleObj($redis);
        }
        return true;
    }
}