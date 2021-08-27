<?php


namespace App\Models\Backstage;


use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\Db\ClientInterface;
use EasySwoole\ORM\DbManager;

class AdminUser extends AbstractModel
{
    protected $tableName = 'pg_admin_user';

    protected $connectionName = 'admin';


    public function infoByName($userName)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use ($userName){
            try {
                return AdminUser::invoke($client)->where(['username'=>$userName])->get()->toArray();
            }catch (\Throwable $e){
                return false;
            }
        }, $this->connectionName);
    }


    public function infoById($id)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use ($id){
            try {
                return AdminUser::invoke($client)->where(['id'=>$id])->get()->toArray();
            }catch (\Throwable $e){
                return false;
            }
        }, $this->connectionName);
    }

    public function getWithRole()
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client){
            try {
                $list = AdminUser::invoke($client)->all(null)->toArray();
                foreach ($list as &$item){
                    $role = AdminRole::invoke($client)->infoById($item['role_id']);
                    $item['role_name'] = $role['name'];
                }
                return $list;
            }catch (\Throwable $e){
                return false;
            }
        }, $this->connectionName);
    }

    public function addUser($data)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use($data){
            try {

                return AdminUser::invoke($client)->data($data)->save();
            }catch (\Throwable $e){
                return false;
            }
        }, $this->connectionName);
    }

    public function updateUser($id,$data)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use($id,$data){
            try {

                return AdminUser::invoke($client)->update($data,['id'=>$id]);
            }catch (\Throwable $e){
                return false;
            }
        }, $this->connectionName);
    }

    public function deleteUser($id)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use($id){
            try {

                return AdminUser::invoke($client)->destroy(['id'=>$id]);
            }catch (\Throwable $e){
                return false;
            }
        }, $this->connectionName);
    }

    public function multiDelete($ids)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use($ids){
            try {

                return AdminUser::invoke($client)->where('id',$ids,'IN')->destroy();
            }catch (\Throwable $e){
                return false;
            }
        }, $this->connectionName);
    }
}