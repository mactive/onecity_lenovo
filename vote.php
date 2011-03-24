<?php

/**
 * SINEMALL 调查程序 * $Author: testyang $
 * $Id: vote.php 14481 2008-04-18 11:23:01Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');

if (!isset($_REQUEST['vote']) || !isset($_REQUEST['options']) || !isset($_REQUEST['type']))
{
    ecs_header("Location: ./\n");
    exit;
}

$res        = array('error' => 0, 'message' => '', 'content' => '');

$vote_id    = intval($_REQUEST['vote']);
$options    = trim($_REQUEST['options']);
$type       = intval($_REQUEST['type']);
$ip_address = "0.0.0.".$_SESSION['user_id'];//real_ip();

if (vote_already_submited($vote_id, $ip_address))
{
    $res['error']   = 1;
    $res['message'] = $_LANG['vote_ip_same'];
}
else
{
    save_vote($vote_id, $ip_address, $options);
	log_account_change($_SESSION['user_id'], GOLD_30, 0, 0, 0, $_LANG['MONEY_GOLD_30']);
	
    $vote = get_vote($vote_id);
    if (!empty($vote))
    {
        $smarty->assign('vote_id', $vote['id']);
        $smarty->assign('vote',    $vote['content']);
    }

    $str = $smarty->fetch("library/vote_page.lbi");

    $pattern = '/(?:<(\w+)[^>]*> .*?)?<div\s+id="ECS_VOTE">(.*)<\/div>(?:.*?<\/\1>)?/is';

    if (preg_match($pattern, $str, $match))
    {
        $res['content'] = $match[2];
    }
    $res['message'] = $_LANG['vote_success'];
}

$json = new JSON;
//echo $res['message'];
echo $json->encode($res);
//echo $res;

/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 检查是否已经提交过投票
 *
 * @access  private
 * @param   integer     $vote_id
 * @param   string      $ip_address
 * @return  boolean
 */
function vote_already_submited($vote_id, $ip_address)
{
    $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('vote_log')." ".
           "WHERE ip_address = '$ip_address' AND vote_id = '$vote_id' ";

    return ($GLOBALS['db']->GetOne($sql) > 0);
}

/**
 * 保存投票结果信息
 *
 * @access  public
 * @param   integer     $vote_id
 * @param   string      $ip_address
 * @param   string      $option_id
 * @return  void
 */
function save_vote($vote_id, $ip_address, $option_id)
{
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('vote_log') . " (vote_id, ip_address, vote_time) " .
           "VALUES ('$vote_id', '$ip_address', " . gmtime() .")";
    $res = $GLOBALS['db']->query($sql);

    /* 更新投票主题的数量 */
    $sql = "UPDATE " .$GLOBALS['ecs']->table('vote'). " SET ".
           "vote_count = vote_count + 1 ".
           "WHERE vote_id = '$vote_id'";
    $GLOBALS['db']->query($sql);

    /* 更新投票选项的数量 */
    $sql = "UPDATE " . $GLOBALS['ecs']->table('vote_option') . " SET " .
           "option_count = option_count + 1 " .
           "WHERE " . db_create_in($option_id, 'option_id');
    $GLOBALS['db']->query($sql);
}

?>