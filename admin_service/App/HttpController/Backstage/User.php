<?php


namespace App\HttpController\Backstage;


use App\Helper\Backstage\BackstageCommonHelper;
use App\Helper\Backstage\BackstageErrorCode;
use App\Models\Backstage\AdminUser;

class User extends AdminBase
{
    public function getAll()
    {
        $result = AdminUser::create()->getWithRole();
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::ListError,'获取列表失败');
            return;
        }
        $this->writeJsonSuccess($result);
    }


    public function add()
    {
        $param = $this->json();
        $param['avatar'] = 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif';
        $param['salt'] = BackstageCommonHelper::alnum();  // 调用公共助手函数中alnum()方法，随机生成密码盐，默认6个字符
        $param['password'] = md5(md5($param['password']) . $param['salt']); // 先对原始密码md5加密，加上密码盐后再次md5加密
        $result = AdminUser::create()->addUser($param);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddAdminUserError,'新增失败');
            return;
        }
        $this->writeJsonSuccess();
    }

    public function update()
    {
        $param = $this->json();
        $uid = $param['id'];
        unset($param['id']);
        $result = AdminUser::create()->updateUser($uid,$param);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddAdminUserError,'更新失败');
            return;
        }
        $this->writeJsonSuccess([]);
    }

    public function userInfo()
    {
        $param = $this->json();
        $uid = $this->admin_token_uid;
        $role = $this->admin_token_key;
        $info = AdminUser::create()->infoById($uid);
        if(!$info){
            $this->writeJsonError(BackstageErrorCode::AddAdminUserError,'获取用户信息失败');
            return;
        }
        $info['roles'] = [$role];
        $this->writeJsonSuccess($info);
    }

    public function delete()
    {
        $param = $this->request()->getRequestParam();
        $id = $param['id'];
        $result = AdminUser::create()->deleteUser($id);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddAdminUserError,'删除失败');
            return;
        }
        $this->writeJsonSuccess([]);
    }


    public function multiDelete()
    {
        $param = $this->json();
        $result = AdminUser::create()->multiDelete($param);
        if(!$result)
        {
            $this->writeJsonError(BackstageErrorCode::AddAdminUserError,'删除失败');
            return;
        }
        $this->writeJsonSuccess([]);
    }

}