<?php


namespace App\HttpController\Backstage;


use App\Helper\Backstage\BackstageErrorCode;
use App\Helper\RedisHelper;
use App\Models\Backstage\AdminRole;
use App\Models\Backstage\AdminUser;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Jwt\Jwt;

class Login extends Controller
{
    public function index()
    {
        $this->writeJson(0,['aaa'=>11],"Success");
    }

    private function writeJsonSuccess($result = null, $msg = 'Success',$statusCode=0)
    {
        if (!$this->response()->isEndResponse()) {
            $data = Array(
                "code" => $statusCode,
                "data" => $result,
                "msg" => $msg
            );
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        } else {
            return false;
        }
    }

    public function login()
    {
        $getData = $this->json();
        $username = $getData['username'];
        $password = $getData['password'];
        if(empty($username)||empty($password)){
            $this->writeJson(BackstageErrorCode::ParamError,$getData,'参数为空');
            return;
        }
        $info = AdminUser::create()->infoByName($username);
        if(empty($info))
        {
            $this->writeJson(BackstageErrorCode::UserEmptyError,$getData,'用户不存在');
            return;
        }
        $roleInfo =  AdminRole::create()->infoById($info['role_id']);
        $role_key = $roleInfo['key'];
        $token = $this->makeUserJwt($info['id'],$role_key);
        RedisHelper::getInstance()->redisSet('admin_login_status:'.$info['id'],md5($token),60*60*12);
        $this->writeJsonSuccess(['token' => $token]);
    }


    /**
     * 生成token
     * @return mixed
     */
    private function makeUserJwt($uid,$key)
    {
        $jwtObject = Jwt::getInstance()
            ->setSecretKey('pubGamingAdminSecretKey') // 秘钥
            ->publish();

        $jwtObject->setAlg('HMACSHA256'); // 加密方式
        $jwtObject->setAud('pubGamingAdminUser'); // 用户
        $jwtObject->setExp(time()+3600*24); // 过期时间
        $jwtObject->setIat(time()); // 发布时间
        $jwtObject->setIss('pubgamingAdmin'); // 发行人
        $jwtObject->setJti(md5(time())); // jwt id 用于标识该jwt
        $jwtObject->setNbf(time()); // 在此之前不可用
        $jwtObject->setSub('userTokenAdmin'); // 主题

        // 自定义数据
        $jwtObject->setData([
            'uid'=>$uid,
            'role'=>$key
        ]);

        // 最终生成的token
        return $jwtObject->__toString();
    }


    /**
     * 退出登录
     */
    public function logout() {
        $getData = $this->json();
        $id = $getData['id'];
        RedisHelper::getInstance()->redisDel('admin_login_status:'.$id);
        $this->writeJsonSuccess([]);
    }
}