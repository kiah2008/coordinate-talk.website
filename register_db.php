<?php
/**
 * UCenter 应用程序开发 Example
 *
 * 应用程序有自己的用户表，用户注册、激活的 Example 代码
 * 使用到的接口函数：
 * uc_get_user()	必须，获取用户的信息
 * uc_user_register()	必须，注册用户数据
 * uc_authcode()	可选，借用用户中心的函数加解密激活字串和 Cookie
 */
include './config.inc.php';
include './include/db_mysql.class.php';
$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
include './cta_client/client.php';

function check_imei($imei){
	if(!ctype_digit($imei)){
		return false;//如果不是数字退出
	}
	$len = strlen($imei);
    if($len != 15) return false;//检查长度
    
    for($ii=1, $sum=0 ; $ii < $len ; $ii++){
		if($ii % 2 == 0) $prod = 2;
        else $prod = 1;
        $num = $prod * $imei[$ii-1];//奇数位*1，偶数位*2
        if($num > 9){//大于9时十位加个位
          $numstr = strval($num);
          $sum += $numstr[0] + $numstr[1]; 
        }else $sum += $num;
    }
	//得到总合
    $sumlast = intval(10*(($sum/10)-floor($sum/10))); //The last digit of $sum
    $dif = (10-$sumlast);
    $diflast = intval(10*(($dif/10)-floor($dif/10))); //The last digit of $dif
    $CD = intval($imei[$len-1]); //check digit

    if($diflast == $CD) return true;

    return false;
}
//校验邮件地址 
function check_email_address($email) {
    // First, we check that there's one @ symbol, and that the lengths are right
    if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
        // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
        return false;
    }
    // Split it into sections to make life easier
    $email_array = explode("@", $email);
    $local_array = explode(".", $email_array[0]);
    for ($i = 0; $i < sizeof($local_array); $i++) {
         if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
            return false;
        }
    }    
    if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
        $domain_array = explode(".", $email_array[1]);
        if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
        }
        for ($i = 0; $i < sizeof($domain_array); $i++) {
            if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
                return false;
            }
        }
    }
    return true;
}
if(!empty($_GET['submit'])) {
	//在UCenter注册用户信息
	$username = '';
	$imei = $_POST['txtImei'];
	if(!check_imei($imei)){
		echo '手机串号错误';
		exit;
	}

	$email = $_POST['txtEmail'];
	if(!check_email_address($email)){
		echo '邮件地址错误';
		exit;
	}
	
	$password = $_POST['txtPass'];
	if($password != $_POST['txtRPass']){
		echo '两次输入密码不一样';
		exit;
	}

	$uid = uc_user_register($_POST['txtNickName'], $password, $email);
	if($uid <= 0) {
		if($uid == -1) {
			echo '用户名不合法';
		} elseif($uid == -2) {
			echo '包含要允许注册的词语';
		} elseif($uid == -3) {
			echo '用户名已经存在';
		} elseif($uid == -4) {
			echo 'Email 格式有误';
		} elseif($uid == -5) {
			echo 'Email 不允许注册';
		} elseif($uid == -6) {
			echo '该 Email 已经被注册';
		} else {
			echo '未定义';
		}
	} else {
		$username = $_POST['txtNickName'];
	}

	if($username) {
		$db->query("INSERT INTO {$tablepre}members (uid,username,admin) VALUES ('$uid','$username','0')");
		$db->query("INSERT INTO {$tablepre}members_imei (uid,imei) VALUES ('$uid','$imei')");
		//注册成功，设置 Cookie，加密直接用 uc_authcode 函数，用户使用自己的函数
		setcookie('Cta_auth', uc_authcode($uid."\t".$username."\t".$imei, 'ENCODE'));
		echo 'succeed';
		exit;
	}
}

?>