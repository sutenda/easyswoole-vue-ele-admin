<?php


namespace App\Models\Backstage;


use App\Helper\Backstage\BackstageCommonHelper;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\Db\ClientInterface;
use EasySwoole\ORM\DbManager;

class AdminRoutes  extends AbstractModel
{
    protected $tableName = 'pg_routes';

    protected $connectionName = 'admin';

    public function routesByRules($rules)
    {
        $ids = [];
        if ($rules != '*') {
            $ids = explode(',', $rules);
        }
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use ($ids){
            try {
                if(empty($ids)){
                    return AdminRoutes::invoke($client)->all(null)->toArray();
                }
                return AdminRoutes::invoke($client)->where('id',$ids,'IN')->all(null);
            }catch (\Throwable $e){
                return false;
            }
        }, $this->connectionName);
    }

    public function routesByTree($uid)
    {
        $user = AdminUser::create()->infoById($uid);
        $role_id = $user['role_id'];
        $role = AdminRole::create()->infoById($role_id);
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use ($role){
            try {
                if($role['rules'] == '*'){
                    $result = AdminRoutes::invoke($client)->order('sort','DESC')->all(null)->toArray();
                    return BackstageCommonHelper::tree($result);
                }
                $ids = explode(',', $role['rules']);
                $result = AdminRoutes::invoke($client)->where('id',$ids,'IN')->order('sort','DESC')->all(null)->toArray();
                return BackstageCommonHelper::tree($result);
            }catch (\Throwable $e){
                var_dump($e->getMessage());
                return false;
            }
        }, $this->connectionName);

    }


    public function deleteRoutes($id)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use($id){
            try {
                return AdminRoutes::invoke($client)->destroy(['id'=>$id]);
            }catch (\Throwable $e){
                return false;
            }
        }, $this->connectionName);
    }

    public function multiDelete($ids)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use($ids){
            try {

                return AdminRoutes::invoke($client)->where('id',$ids,'IN')->destroy();
            }catch (\Throwable $e){
                return false;
            }
        }, $this->connectionName);
    }

    public function addRoutes($data)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use($data){
            try {

                return AdminRoutes::invoke($client)->data($data)->save();
            }catch (\Throwable $e){
                return false;
            }
        }, $this->connectionName);
    }


    public function updateRoutes($id,$data)
    {
        return DbManager::getInstance()->invoke(function (ClientInterface $client) use($id,$data){
            try {

                return AdminRoutes::invoke($client)->update($data,['id'=>$id]);
            }catch (\Throwable $e){
                return false;
            }
        }, $this->connectionName);
    }
}