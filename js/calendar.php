<?php

/**
 * SINEMALL 调用日历 JS * $Author: testyang $
 * $Id: calendar.php 14481 2008-04-18 11:23:01Z testyang $
*/

$lang = (!empty($_GET['lang'])) ? trim($_GET['lang']) : 'zh_cn';

if (!file_exists('../languages/' . $lang . '/calendar.php'))
{
    $lang = 'zh_cn';
}

require(dirname(dirname(__FILE__)) . '/data/config.php');
header('Content-type: application/x-javascript; charset=' . EC_CHARSET);

include_once('../languages/' . $lang . '/calendar.php');

foreach ($_LANG['calendar_lang'] AS $cal_key => $cal_data)
{
    echo 'var ' . $cal_key . " = \"" . $cal_data . "\";\r\n";
}

include_once('./calendar/calendar.js');

?>