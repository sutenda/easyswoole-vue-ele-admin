<?php


namespace App\Helper\Backstage;


class BackstageErrorCode
{
    const UnknownError = 400;
    const ParamError = 10000;
    const UserEmptyError = 10001;
    const TokenError = 500;
    const UserNotLoginError = 999;
    const AddRoleError = 10002;
    const RoutesError = 10003;
    const ListError = 10003;
    const AddAdminUserError = 10004;
    const SendTokenError = 10005;
    const ModifyError = 10006;

}