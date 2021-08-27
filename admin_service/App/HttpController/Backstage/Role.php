<?php


namespace App\HttpController\Backstage;

use App\Helper\Backstage\BackstageErrorCode;
use App\Models\Backstage\AdminRole;

class Role extends AdminBase
{
    public function add()
    {
        $param = $this->json();
        $param['rules'] = implode(',', $param['rules']); // 将数组转化为字符串，以','分割
        $result = AdminRole::create()->addRole($param);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddRoleError,'新增失败');
        }
        $this->writeJsonSuccess($result);
    }

    public function update()
    {
        $param = $this->json();
        $param['rules'] = implode(',', $param['rules']); // 将数组转化为字符串，以','分割
        $id = $param['id'];
        unset($param['id']);
        unset($param['create_time']);
        unset($param['update_time']);
        var_dump($param);
        $result = AdminRole::create()->updateRole($id,$param);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddRoleError,'更新失败');
        }
        $this->writeJsonSuccess($result);
    }


    public function getRoles()
    {
        $result = AdminRole::create()->roleWithRoutes();
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddRoleError,'查询失败');
        }
        $this->writeJsonSuccess($result);
    }

    public function delete()
    {
        $param = $this->request()->getRequestParam();
        $id = $param['id'];
        $result = AdminRole::create()->deleteRole($id);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddAdminUserError,'删除失败');
        }
        $this->writeJsonSuccess($result);
    }

    public function multiDelete()
    {
        $param = $this->json();
        $result = AdminRole::create()->multiDelete($param);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddAdminUserError,'删除失败');
        }
        $this->writeJsonSuccess($result);
    }


    public function getAll()
    {
        $result = AdminRole::create()->list();
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddAdminUserError,'获取列表');
        }
        $this->writeJsonSuccess($result);
    }
}