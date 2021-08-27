<?php


namespace App\HttpController\Backstage;


use App\Helper\Backstage\BackstageErrorCode;
use App\Helper\ErrorCode;
use App\Helper\RedisHelper;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Jwt\Jwt;

abstract class AdminBase extends Controller
{
    public $admin_token_uid = '';
    public $admin_token_key = '';

    protected function onRequest(string $action): ?bool
    {
        $header = $this->request()->getHeaders();
        $this->user_ip = $header['x-real-ip'][0]??'';
        if(isset($header['x-token']))
        {
            try {
                $isLogin = true;
                $token = $header['x-token'][0];
                $jwtObject = Jwt::getInstance()->setSecretKey('pubGamingAdminSecretKey')->decode($token);
                $status = $jwtObject->getStatus();
                switch ($status)
                {
                    case  1:
                        $jwtObject->getExp() > time() ?:$isLogin = false;
                        $jwtObject->getAud() == 'pubGamingAdminUser'?:$isLogin = false;
                        $data = $jwtObject->getData();
                        $this->admin_token_uid = $data['uid'];
                        $this->admin_token_key = $data['role'];
                        $jwtObject->getIss() == 'pubgamingAdmin'?:$isLogin = false;
                        $jwtObject->getSub() == 'userTokenAdmin'?:$isLogin = false;
                        $cacheJwt = RedisHelper::getInstance()->redisGet('admin_login_status:'.$this->admin_token_uid);
                        ($cacheJwt == md5($token))?:$isLogin = false;
                        if(!$isLogin)
                        {
                            $this->writeJson(BackstageErrorCode::TokenError,[],'Token Error');
                        }
                        break;
                    case  -1:
                        $isLogin = false;
                        $this->writeJson(BackstageErrorCode::TokenError,[],'Token Error');
                        break;
                    case  -2:
                        $isLogin = false;
                        $this->writeJson(BackstageErrorCode::TokenError,[],'Token expired');
                        break;
                }
                return $isLogin;
            } catch (\EasySwoole\Jwt\Exception $e) {
                $this->writeJson(BackstageErrorCode::UnknownError,[],$e->getMessage());
                return false;
            }
        }
        $this->writeJson(BackstageErrorCode::UserNotLoginError,[],'User not login');
        return false;
    }

    protected function writeJsonError($statusCode = 200, $msg = '', $result = [])
    {
        parent::writeJson($statusCode, $result, $msg);
        parent::response()->end();
        return true;
    }

    protected function writeJsonSuccess($result = null, $msg = '成功',$statusCode=0)
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

    protected function onException(\Throwable $throwable): void
    {
        $this->writeJson(ErrorCode::UnknownError,[],$throwable->getMessage());
    }
}