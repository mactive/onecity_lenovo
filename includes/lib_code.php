<?php

/**
 * SINEMALL 加密解密类 * $Author: testyang $
 * $Id: lib_code.php 14481 2008-04-18 11:23:01Z testyang $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 定义当前使用的加密串
 */
define('AUTH_KEY', 'this is a key');

/**
 * 定义更改之前使用的加密串，如果没有必要，请保持为空值
 */
define('OLD_AUTH_KEY', '');

/**
 * 加密函数
 * @param   string  $str    加密前的字符串
 * @param   string  $key    密钥
 * @return  string  加密后的字符串
 */
function encrypt($str, $key = AUTH_KEY)
{
    $coded = '';
    $keylength = strlen($key);

    for ($i = 0, $count = strlen($str); $i < $count; $i += $keylength)
    {
        $coded .= substr($str, $i, $keylength) ^ $key;
    }

    return str_replace('=', '', base64_encode($coded));
}

/**
 * 解密函数
 * @param   string  $str    加密后的字符串
 * @param   string  $key    密钥
 * @return  string  加密前的字符串
 */
function decrypt($str, $key = AUTH_KEY)
{
    $coded = '';
    $keylength = strlen($key);
    $str = base64_decode($str);

    for ($i = 0, $count = strlen($str); $i < $count; $i += $keylength)
    {
        $coded .= substr($str, $i, $keylength) ^ $key;
    }

    return $coded;
}

?>