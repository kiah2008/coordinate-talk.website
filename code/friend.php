<?php
/**
 * UCenter 应用程序开发 Example
 *
 * 列出好友的 Example 代码
 * 使用到的接口函数：
 * uc_friend_totalnum()	必须，返回好友总数
 * uc_friend_ls()	必须，返回好友列表
 * uc_friend_delete()	必须，删除好友
 * uc_friend_add()	必须，添加好友
 */
require_once './config.inc.php';
include './cta_client/client.php';
include './include/cookie.php';

if(empty($_POST['fun'])) {
	switch($_POST['fun']){
		case 'add':
			if($_POST['newfriend'] && $friendid = uc_get_user($_POST['newfriend'])) {
				uc_friend_add($Cta_uid, $friendid[0], $_POST['newcomment']);
			}
			break;
		case 'del':
			if(!empty($_POST['delete']) && is_array($_POST['delete'])) {
				uc_friend_delete($Cta_uid, $_POST['delete']);
			}
			break;
		case 'frtn':
			$num = uc_friend_totalnum($Cta_uid);
			break;
		case 'frls':
			$friendlist = uc_friend_ls($Cta_uid, 1, 999, $num);
			break;
	}
}

?>