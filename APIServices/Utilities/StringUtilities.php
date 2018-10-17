<?php

namespace APIServices\Utilities;


class StringUtilities
{
    static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL)
            && preg_match('/@.+\./', $email);
    }

    static function isValidPhoneNumber($string)
    {
        return filter_var($string, FILTER_VALIDATE_INT)
            && preg_match('/^[+]?\d{8,}?$/', $string);
    }

    static function RandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}