<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: user.php 12180 2008-01-17 05:56:43Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class control extends adminbase {

	function control() {
		$this->adminbase();
		$this->load('user');
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadminbadword']) {
			$this->message('no_permission_for_this_module');
		}
	}

	//note public 内部接口
	function onls() {

		//include_once UC_ROOT.'view/default/admin.lang.php';
		/**
		 * note 状态:
		 * 	0:	未知状态
		 * 	1:	添加成功
		 * 	-1:	已经为管理员
		 * 	-2:	插入失败
		 * 	-3:	无此用户
		 * 	-4:	修改Founder账号时，输入的原密码有误
		 */
		$status = 0;
		if(!empty($_POST['addname']) && $this->submitcheck()) {
			$addname = getgpc('addname', 'P');
			$this->view->assign('addname', $addname);
			$uid = $this->db->result_first("SELECT uid FROM ".UC_DBTABLEPRE."members WHERE username='$addname'");
			if($uid) {
				//note 判断是否已经为管理员了
				$adminuid = $this->db->result_first("SELECT uid FROM ".UC_DBTABLEPRE."admins WHERE username='$addname'");
				if($adminuid) {
					$status = -1;//note 已经为管理员
				} else {
					$allowadminsetting = getgpc('allowadminsetting', 'P');
					$allowadminapp = getgpc('allowadminapp', 'P');
					$allowadminuser = getgpc('allowadminuser', 'P');
					$allowadminbadword = getgpc('allowadminbadword', 'P');
					$allowadmincredits = getgpc('allowadmincredits', 'P');
					$allowadmintag = getgpc('allowadmintag', 'P');
					$allowadminpm = getgpc('allowadminpm', 'P');
					$allowadmindomain = getgpc('allowadmindomain', 'P');
					$allowadmindb = getgpc('allowadmindb', 'P');
					$allowadminnote = getgpc('allowadminnote', 'P');
					$allowadmincache = getgpc('allowadmincache', 'P');
					$allowadminlog = getgpc('allowadminlog', 'P');
					$this->db->query("INSERT INTO ".UC_DBTABLEPRE."admins SET
						uid='$uid',
						username='$addname',
						allowadminsetting='$allowadminsetting',
						allowadminapp='$allowadminapp',
						allowadminuser='$allowadminuser',
						allowadminbadword='$allowadminbadword',
						allowadmincredits='$allowadmincredits',
						allowadmintag='$allowadmintag',
						allowadminpm='$allowadminpm',
						allowadmindomain='$allowadmindomain',
						allowadmindb='$allowadmindb',
						allowadminnote='$allowadminnote',
						allowadmincache='$allowadmincache',
						allowadminlog='$allowadminlog'");
					$insertid = $this->db->insert_id();
					if($insertid) {
						$this->writelog('admin_add', 'username='.htmlspecialchars($addname));
						$status = 1;
					} else {
						$status = -2;//note 插入失败
					}
				}
			} else {
				$status = -3;//note 无此用户
			}
		}

		if(!empty($_POST['editpwsubmit']) && $this->submitcheck()) {
			$oldpw = getgpc('oldpw', 'P');
			$newpw = getgpc('newpw', 'P');
			$newpw2 = getgpc('newpw2', 'P');
			if(UC_FOUNDERPW == md5(md5($oldpw).UC_FOUNDERSALT)) {
				//note 修改 config.inc.php
				$configfile = UC_ROOT.'./data/config.inc.php';
				if(!is_writable($configfile)) {
					$status = -4;//note 配置文件不可写
				} else {
					if($newpw != $newpw2) {
						$status = -6;//note 两次输入的密码不一致
					} else {
						$config = file_get_contents($configfile);
						$salt = substr(uniqid(rand()), 0, 6);
						$md5newpw = md5(md5($newpw).$salt);
						$config = preg_replace("/define\('UC_FOUNDERSALT',\s*'.*?'\);/i", "define('UC_FOUNDERSALT', '$salt');", $config);
						$config = preg_replace("/define\('UC_FOUNDERPW',\s*'.*?'\);/i", "define('UC_FOUNDERPW', '$md5newpw');", $config);
						$fp = @fopen($configfile, 'w');
						@fwrite($fp, $config);
						@fclose($fp);
						$status = 2;
						$this->writelog('admin_pw_edit');
					}
				}
			} else {
				$status = -5;//note 创始人密码错误
			}
		}

		$this->view->assign('status', $status);

		if(!empty($_POST['delete'])) {
			$uids = $this->implode(getgpc('delete', 'P'));
			$this->db->query("DELETE FROM ".UC_DBTABLEPRE."admins WHERE uid IN ($uids)");
		}

		$page = max(1, getgpc('page'));
		$ppp  = 15;
		$totalnum = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."admins");
		$start = $this->page_get_start($page, $ppp, $totalnum);
		$userlist = $this->db->fetch_all("SELECT a.*,m.* FROM ".UC_DBTABLEPRE."admins a LEFT JOIN ".UC_DBTABLEPRE."members m USING(uid) LIMIT $start, $ppp");
		$multipage = $this->page($totalnum, $ppp, $page, 'admin.php?m=admin&a=admin');
		if($userlist) {
			foreach($userlist as $key=>$user) {
				$user['regdate'] = $this->date($user['regdate']);
				$userlist[$key] = $user;
			}
		}

		$a = getgpc('a');
		$this->view->assign('a', $a);
		$this->view->assign('multipage', $multipage);
		$this->view->assign('userlist', $userlist);
		$this->view->display('admin_admin');

	}

	function onedit() {
		$uid = getgpc('uid');
		$status = 0;
		if($this->submitcheck()) {
			$allowadminsetting = getgpc('allowadminsetting', 'P');
			$allowadminapp = getgpc('allowadminapp', 'P');
			$allowadminuser = getgpc('allowadminuser', 'P');
			$allowadminbadword = getgpc('allowadminbadword', 'P');
			$allowadmintag = getgpc('allowadmintag', 'P');
			$allowadminpm = getgpc('allowadminpm', 'P');
			$allowadmincredits = getgpc('allowadmincredits', 'P');
			$allowadmindomain = getgpc('allowadmindomain', 'P');
			$allowadmindb = getgpc('allowadmindb', 'P');
			$allowadminnote = getgpc('allowadminnote', 'P');
			$allowadmincache = getgpc('allowadmincache', 'P');
			$allowadminlog = getgpc('allowadminlog', 'P');
			$this->db->query("UPDATE ".UC_DBTABLEPRE."admins SET
				allowadminsetting='$allowadminsetting',
				allowadminapp='$allowadminapp',
				allowadminuser='$allowadminuser',
				allowadminbadword='$allowadminbadword',
				allowadmincredits='$allowadmincredits',
				allowadmintag='$allowadmintag',
				allowadminpm='$allowadminpm',
				allowadmindomain='$allowadmindomain',
				allowadmindb='$allowadmindb',
				allowadminnote='$allowadminnote',
				allowadmincache='$allowadmincache',
				allowadminlog='$allowadminlog'
				WHERE uid='$uid'");
			$status = $this->db->errno() ? -1 : 1;
			$this->writelog('admin_priv_edit', 'username='.htmlspecialchars($admin));
		}
		$admin = $this->db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."admins WHERE uid='$uid'");
		$this->view->assign('uid', $uid);
		$this->view->assign('admin', $admin);
		$this->view->assign('status', $status);
		$this->view->display('admin_admin');
	}

}

?>