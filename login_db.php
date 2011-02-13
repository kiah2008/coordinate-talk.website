<?php
/**
 * UCenter 应用程序开发 Example
 *
 * 应用程序有自己的用户表，用户登录的 Example 代码
 * 使用到的接口函数：
 * uc_user_login()	必须，判断登录用户的有效性
 * uc_authcode()	可选，借用用户中心的函数加解密激活字串和 Cookie
 * uc_user_synlogin()	可选，生成同步登录的代码
 */
include './config.inc.php';
include './include/db_mysql.class.php';
$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
include './cta_client/client.php';
if(!empty($_GET['submit'])) {
	if(stripos($_POST['username'],"@")){
		list($uid, $username, $password, $email) = uc_user_login($_POST['username'], $_POST['password'],2);
	}
	else{
		//通过接口判断登录帐号的正确性，返回值为数组
		list($uid, $username, $password, $email) = uc_user_login($_POST['username'], $_POST['password']);
	}
	setcookie('Cta_auth', '', -86400);
	if($uid > 0) {
		if(!$db->result_first("SELECT count(*) FROM {$tablepre}members WHERE uid='$uid'")) {
			//判断用户是否存在于用户表，不存在则跳转到激活页面
			$auth = rawurlencode(uc_authcode("$username\t".time(), 'ENCODE'));
			echo '您需要需要激活该帐号，才能进入本应用程序<br><a href="'.$_SERVER['PHP_SELF'].'?fun=register&action=activation&auth='.$auth.'">继续</a>';
			exit;
		}
		$imei = $db->result_first("SELECT imei FROM {$tablepre}members_imei WHERE uid='$uid'");
		//用户登陆成功，设置 Cookie，加密直接用 uc_authcode 函数，用户使用自己的函数
		//
		setcookie('Cta_auth', uc_authcode($uid."\t".$username."\t".$imei, 'ENCODE'));
		//生成同步登录的代码
		$ucsynlogin = uc_user_synlogin($uid);
		echo 'succeed';
		exit;
	} elseif($uid == -1) {
		echo '用户不存在,或者被删除';
	} elseif($uid == -2) {
		echo '密码错';
	} else {
		echo '未定义';
	}
}

?>