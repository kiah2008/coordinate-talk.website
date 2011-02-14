<?php 
require_once './config.inc.php';

include './cta_client/client.php';
include './include/cookie.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
<script language="javascript" type="text/javascript">
<?php 
if(!$Cta_username){
	echo "alert(\"请先登陆\");";
	echo "window.location.href=\"/\";";
}
?>
</script>
<style type="text/css">
@import "style/global.css";
.wrap{
	width:960px;
	background-color:#dad6c5;
}
.main{
	width:100%;
	float:left;
	overflow:hidden;
}
.side{
	width:180px;
	float:right;
	padding-bottom:20px;
}
.sideinner {
	PADDING-BOTTOM: 0px;
	PADDING-LEFT: 25px;
	PADDING-RIGHT: 25px;
	PADDING-TOP: 0px
}
</style>
</head>
<body>
<div id="header"><a href="/">返回首页</a></div>
<div class="wrap" id="wrap">
	<div class="main">
	<?php 
	switch(@$_GET['typeid']) {
		case '2':
	?>
		<form action="?action=profile" method="post">
			<table class="formtable">
				<tr><th>个人签名</th><td><input type="text" name="qm"></td></tr>
				<tr><th>手机串号</th><td><input type="text" name="imei"></td></tr>
				<tr><th></th><td><input type="submit" value="提交"></td></tr>
				
			</table>
		</form>
	<?php 
			break;
		case '3':
			echo '
			<img src="'.UC_API.'/avatar.php?uid='.$Cta_uid.'&size=big">
			<img src="'.UC_API.'/avatar.php?uid='.$Cta_uid.'&size=middle">
			<img src="'.UC_API.'/avatar.php?uid='.$Cta_uid.'&size=small">
			<br><br>'.uc_avatar($Cta_uid);
			break;
	}
	?>

		
	</div>
	<div class="side">
		<div class="sideinner">
			<ul class="tabs">
				<li class="current"><a href="?typeid=2">个人资料</a></li>
				<li class="current"><a href="?typeid=3">修改头像</a></li>
			</ul>
		</div>
	</div>
</div>
</body>
</html>