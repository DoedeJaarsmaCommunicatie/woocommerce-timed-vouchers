<?php

namespace WooCommerceTimedVouchers\Helpers;

class Rand
{
    const CHARACTERS_ALPHA_NUM = '0123456789abcdefghjklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVW';

    public static function generateRandomString($length = 6): string
    {
        $random_string = '';
        for ($i = 0; $i < $length; $i++) {
            $random_string .= static::generateRandom();
        }

        return strtoupper($random_string);
    }

    public static function generateRandom($length = 1): string
    {
        return substr(
            str_shuffle(
                md5(time())
            ),
            0,
            $length
        );
    }
}
