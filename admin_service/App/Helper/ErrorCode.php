<?php


namespace App\Helper;


class ErrorCode
{
    const UnknownError = 400;
    const TokenError = 500;
    const changeBalanceError = 600;
    const UserNotLoginError = 999;
    const ApiRSPError = 777;
    const SameRequestError = 998;
    const UserAlreadyLogoutError = 986;
    const UserAlreadyLoginGameError = 972;
    const UserSupplierNotSameError = 916;
    const ParamError = 10000;
    const UserInfoError = 10001;
    const EmailCodeError = 10002;
    const RegisterError = 10003;
    const UserExistsError = 10004;
    const UserEmptyError = 10005;
    const UserPwdError = 10006;
    const UserBalanceError = 10007;
    const GameUrlEmptyError = 10008;
    const UserIDEmptyError = 10009;
    const UpperLimitError = 10010;
    const UserNotExistsError = 10011;
    const ResetCodeInvalidError = 10012;
    const ResetPasswordError = 10013;
    const ImageUploadError = 10014;
    const UserAgentEnterGameError = 10015;
    const UserNotAgentError = 10016;
}