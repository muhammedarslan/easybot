<?php

class Account
{

    private $DatabaseConntection;

    public function setDb($db)
    {
        $this->DatabaseConntection = $db;
    }

    public function Login($UserID, $Channel, $IsAutoLogin = false): void
    {
        $db = $this->DatabaseConntection;
        StaticFunctions::new_session();

        $UserQuery = $db->query("SELECT * from users WHERE id='{$UserID}' and status=1")->fetch(PDO::FETCH_ASSOC);
        if (!$UserQuery) {
            echo StaticFunctions::JsonOutput([
                'status' => 'failed',
                'label' => 'danger',
                'message' => StaticFunctions::lang('Giriş reddedildi.')
            ]);
            exit;
        }

        if (!$IsAutoLogin) {
            $this->DeleteRememberTokens($UserQuery['id']);
            $this->AddLoginLog($UserQuery['id'], $Channel);
        } else {
            $this->AddLoginLog($UserQuery['id'], 'AutoLogin');
        }

        $this->LastLoginUpdate($UserQuery['id'], $Channel);
        $this->SetLoginSessions($UserQuery);
        $this->SetSecurityHash($UserQuery['id']);
        $this->AccountSecurity($UserQuery, $Channel, $IsAutoLogin);
        session_regenerate_id();
    }

    private function AccountSecurity($UserQuery, $Channel, $IsAutoLogin): void
    {
        StaticFunctions::new_session();
        $IsSecureLevel = false;
        $UserPrefences = json_decode($UserQuery['user_prefences'], true)['AuthorizedProfiles'];

        if (!$IsSecureLevel) {
            if ($UserPrefences[$Channel] != true) {
                $_SESSION['SecureLevel_Auth'] = true;
                $_SESSION['SecureLevel_AuthChannel'] = $Channel;
                $IsSecureLevel = true;
            }
        }

        $AutoLoginVerified = false;
        if ($IsAutoLogin) {
            $RememberToken = (isset($_COOKIE['RMB']) && $_COOKIE['RMB'] != '') ? StaticFunctions::clear($_COOKIE['RMB']) : null;
            $Browser       = md5($_SERVER['HTTP_USER_AGENT']);
            $Now           = time();
            if ($RememberToken != null) {
                $CheckToken = $this->DatabaseConntection->query("SELECT id,is_verified FROM remember_me WHERE remember_token = '{$RememberToken}' and user_browser = '$Browser' and expired_time > $Now and user_id='{$UserQuery['id']}' and is_verified=1 ")->fetch(PDO::FETCH_ASSOC);
                if ($CheckToken) {
                    $AutoLoginVerified = true;
                }
            }
        }

        if (!$IsSecureLevel) {
            if ($UserQuery['2step_verification'] == 1) {
                if (!$AutoLoginVerified) {
                    $_SESSION['SecureLevel_2Factor'] = true;
                    $IsSecureLevel = true;
                }
            }
        }

        if (!$IsSecureLevel) {
            if ($UserQuery['failed_login'] > 2) {
                $_SESSION['SecureLevel_FailedLogin'] = true;
                $IsSecureLevel = true;
            }
        }
    }

    private function LastLoginUpdate($UserID, $Channel): void
    {

        $LastLoginJson = json_encode([
            'u' => time(),
            'i' => StaticFunctions::get_ip(),
            't' => $Channel
        ]);

        $LastLoginUpdate = $this->DatabaseConntection->prepare("UPDATE users SET
        last_login   = :iki
        WHERE id = :dort");
        $update = $LastLoginUpdate->execute(array(
            'iki' => $LastLoginJson,
            'dort' => $UserID
        ));
    }

    private function SetLoginSessions($UserQuery): void
    {
        StaticFunctions::new_session();
        $LastLoginDecode = json_decode($UserQuery['last_login'], true);
        $_SESSION['CheckSession'] = 'active';
        $_SESSION['UserSession']    = (object) [
            'id' => $UserQuery['id'],
            'phone_code' => $UserQuery['phone_code'],
            'phone_number' => $UserQuery['phone_number'],
            'email' => $UserQuery['email'],
            'email_verify' => $UserQuery['email_verify'],
            'phone_verify' => $UserQuery['phone_verify'],
            'real_name' => $UserQuery['real_name'],
            'avatar' => $UserQuery['avatar'],
            'created_time' => $UserQuery['created_time'],
            'last_login' => $LastLoginDecode['u'],
            'last_ip' => $LastLoginDecode['i'],
            'last_type' => $LastLoginDecode['t'],
            'token' => $UserQuery['token']
        ];
        $_SESSION['UserID'] = $UserQuery['id'];
    }

    private function AddLoginLog($UserID, $Channel): void
    {
        StaticFunctions::AddLog(['Login' => [
            'UserId' => $UserID,
            'UserIp' => StaticFunctions::get_ip(),
            'UserBrowser' => StaticFunctions::getBrowser(),
            'Type' => $Channel
        ]], $UserID);
    }

    private function DeleteRememberTokens($UserID): void
    {
        $DeleteTokens = $this->DatabaseConntection->exec("DELETE FROM remember_me WHERE user_id = '$UserID' ");
    }

    private function SetSecurityHash($UserID): void
    {
        StaticFunctions::new_session();
        $payload = array(
            'UserId' => $UserID,
            'UserIp' => StaticFunctions::get_ip(),
            'UserBrowser' => md5($_SERVER['HTTP_USER_AGENT'])
        );
        $jwt = \Firebase\JWT\JWT::encode($payload, StaticFunctions::JwtKey());
        $_SESSION['SecurityHash'] = $jwt;
    }
}