<?php


namespace App\HttpController\Backstage;


use App\Helper\Backstage\BackstageErrorCode;
use App\Models\Backstage\AdminRoutes;

class Routes extends AdminBase
{
    public function index()
    {
        $param = $this->json();
        $uid = $param['uid']??'';
        if(empty($uid)){
            $uid = $this->admin_token_uid;
        }
        $result = AdminRoutes::create()->routesByTree($uid);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::RoutesError,'获取列表失败');
            return;
        }
        $this->writeJsonSuccess($result);
    }

    public function delete()
    {
        $param = $this->request()->getRequestParam();
        $id = $param['id'];
        $result = AdminRoutes::create()->deleteRoutes($id);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddAdminUserError,'删除失败');
            return;
        }
        $this->writeJsonSuccess($result);
    }


    public function multiDelete()
    {
        $param = $this->json();
        $result = AdminRoutes::create()->multiDelete($param);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddAdminUserError,'删除失败');
            return;
        }
        $this->writeJsonSuccess($result);
    }

    public function add()
    {
        //$param = $this->request()->getRequestParam();
        $param = $this->json();
        $result = AdminRoutes::create()->addRoutes($param);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddAdminUserError,'新增失败');
            return;
        }
        $this->writeJsonSuccess($result);
    }


    public function update()
    {
        $param = $this->json();
        $uid = $param['id'];
        unset($param['id']);
        $result = AdminRoutes::create()->updateRoutes($uid,$param);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddAdminUserError,'更新失败');
            return;
        }
        $this->writeJsonSuccess([]);
    }
}