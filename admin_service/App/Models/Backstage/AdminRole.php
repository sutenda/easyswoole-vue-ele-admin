<?php


namespace App\Models\Backstage;


use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\Db\ClientInterface;
use EasySwoole\ORM\DbManager;

class AdminRole extends AbstractModel
{
    protected $tableName = 'pg_role';

    protected $connectionName = 'admin';

    public function infoById($id)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use ($id){
            try {
                return AdminRole::invoke($client)->where(['id'=>$id])->get()->toArray();
            }catch (\Throwable $e){
                var_dump($e->getMessage());
                return false;
            }
        }, $this->connectionName);
    }

    public function addRole($data)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use ($data){
            try {
                return AdminRole::invoke($client)->data($data)->save();
            }catch (\Throwable $e){
                var_dump($e->getMessage());
                return false;
            }
        }, $this->connectionName);
    }

    public function updateRole($id,$data)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use ($id,$data){
            try {
                return AdminRole::invoke($client)->update($data,['id'=>$id]);
            }catch (\Throwable $e){
                var_dump($e->getMessage());
                return false;
            }
        }, $this->connectionName);
    }

    public function roleWithRoutes()
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client){
            try {
                $list = AdminRole::invoke($client)->all(null)->toArray();
                foreach ($list as &$item) {
                    $routes = AdminRoutes::invoke($client)->routesByRules($item['rules']);// 查询每个角色组方法
                    $item['routes'] = $routes;
                }
                return $list;
            }catch (\Throwable $e){
                var_dump($e->getMessage());
                return false;
            }
        }, $this->connectionName);
    }

    public function deleteRole($id)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use($id){
            try {
                return AdminRole::invoke($client)->destroy(['id'=>$id]);
            }catch (\Throwable $e){
                var_dump($e->getMessage());
                return false;
            }
        }, $this->connectionName);
    }


    public function multiDelete($ids)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use($ids){
            try {
                return AdminRole::invoke($client)->where('id',$ids,'IN')->destroy();
            }catch (\Throwable $e){
                var_dump($e->getMessage());
                return false;
            }
        }, $this->connectionName);
    }

    public function list()
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client){
            try {
                return AdminRole::invoke($client)->all(null)->toArray();
            }catch (\Throwable $e){
                var_dump($e->getMessage());
                return false;
            }
        }, $this->connectionName);
    }
}