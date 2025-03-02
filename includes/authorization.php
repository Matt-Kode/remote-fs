<?php
require_once ('config.php');
Class Authorization {
    public static function baseUser(): bool {

        if (!self::checkHeader()) {
            return false;
        }

        $token = explode(' ', $_SERVER['HTTP_AUTHORIZATION'])[1];

        list($api_key, $permission, $signature) = explode(".", $token);

        $calculated_signature = hash_hmac('sha256', $api_key . '.' . $permission, SECRET_KEY);

        if (hash_equals($signature, $calculated_signature) && $api_key == API_KEY) {
            return true;
        }
        return false;
    }

    private static function checkHeader(): bool
    {
        return isset($_SERVER['HTTP_AUTHORIZATION']);
    }
}