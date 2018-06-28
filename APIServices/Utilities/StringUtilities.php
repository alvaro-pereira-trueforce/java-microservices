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
}