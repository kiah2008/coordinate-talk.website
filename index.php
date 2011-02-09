<?php 
require_once './config.inc.php';
require_once './include/db_class.php';
include './include/db_mysql.class.php';
$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);

include './cta_client/client.php';
/**
 * 获取当前用户的 UID 和 用户名
 * Cookie 解密直接用 uc_authcode 函数，用户使用自己的函数
 */
 
if(!empty($_COOKIE['Cta_auth'])) {
	list($Cta_uid, $Cta_username,$Cta_imei) = explode("\t", uc_authcode($_COOKIE['Cta_auth'], 'DECODE'));
} else {
	$Cta_uid = $Cta_username = $Cta_imei = '';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Coordinate Talk Home</title>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="./js/jquery-1.5.min.js"></script>
<script type="text/javascript">
function initialize(){
	var myOptions = {
			zoom:9,
			center:new google.maps.LatLng(39.9056266666667,116.38993),
		    mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	var map = new google.maps.Map(document.getElementById("mainMap"),myOptions);
	setMarkers(map,beaches);
}
<?php 
	$DB = new MySqlClass(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	$sql = "SELECT a.code, c.username, note, latitude, longitude, create_date, altitude, address_zh FROM `cta_message` a JOIN cta_members_imei b ON a.send_account = b.imei JOIN cta_members c ON b.uid = c.uid ORDER BY `code` DESC LIMIT 0 , 30 ";
	$result=$DB->ExeSql($sql);
	echo "var beaches = [";
	while($row=$DB->getRowResult($result))
	{
		    $code=$row['code'];
            $username=$row['username'];
            $note = $row['note'];
            $latitude = $row['latitude'];
            $longitude = $row['longitude'];
            $altitude = $row['altitude'];
            $create_date = $row['create_date'];
            $address=$row['address_zh'];
            echo "['$username:$note<br/>$address', $latitude, $longitude, 4],\n";
	}
	echo "['Maroubra Beach', -33.950198, 151.259302, 1]];";
?>

function setMarkers(map, locations) {
	// Add markers to the map    
	// Marker sizes are expressed as a Size of X,Y   
	// where the origin of the image (0,0) is located
	// in the top left of the image.
	// Origins, anchor positions and coordinates of the marker   
	// increase in the X direction to the right and in   
	// the Y direction down.   
	var image = new google.maps.MarkerImage('images/beachflag.png',       
			// This marker is 20 pixels wide by 32 pixels tall.       
			new google.maps.Size(27, 32),       
			// The origin for this image is 0,0.       
			new google.maps.Point(0,0),       
			// The anchor for this image is the base of the flagpole at 0,32.       
			new google.maps.Point(0, 32));   
	var shadow = new google.maps.MarkerImage('images/beachflag_shadow.png',       
			// The shadow image is larger in the horizontal dimension       
			// while the position and offset are the same as for the main image.       
			new google.maps.Size(37, 32),       
			new google.maps.Point(0,0),       
			new google.maps.Point(0, 32));       
	// Shapes define the clickable region of the icon.       
	// The type defines an HTML <area> element 'poly' which       
	// traces out a polygon as a series of X,Y points. The final       
	// coordinate closes the poly by connecting to the first       
	// coordinate.   
	var shape = {       
			coord: [1, 1, 1, 20, 18, 20, 18 , 1],       
			type: 'poly'   };   
	for (var i = 0; i < locations.length; i++) {     
		var beach = locations[i];     
		var myLatLng = new google.maps.LatLng(beach[1], beach[2]);     
		var marker = new google.maps.Marker({         
			position: myLatLng,         
			map: map,         
			shadow: shadow,         
			icon: image,         
			shape: shape,         
			title: beach[0],         
			zIndex: beach[3]     
		});   
	} 
}

function smallMap(divid,latitude,longitude) {
    var myLatlng = new google.maps.LatLng(latitude,longitude);
    var myOptions = {
      zoom: 17,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.HYBRID
    }
    var map = new google.maps.Map(document.getElementById(divid), myOptions);
    
    var marker = new google.maps.Marker({
        position: myLatlng, 
        map: map,
        title:""
    });
}
</script>
<style type="text/css">
body{
	font-size:12px;
}
table{
	font-size:12px;
}
#mainMap{
	height:500px;
	width:680px;
	float: left;
}
#login{
	height:300px;
	width:250px;
	float: left;
	margin:20px 20px 20px 20px;
}
</style>
</head>
<body onload="initialize()">
    <div id="mainMap"></div>
    <div id="login">
     
    <?php 
    if(!$Cta_username){
		//登录表单
		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?fun=login">';
		echo '登录:';
		echo '<dl><dt>用户名</dt><dd><input name="username" size="20"/></dd>';
		echo '<dt>密　码</dt><dd><input name="password" type="password" size="20"/></dd></dl>';
		echo '<input type="button" value="登陆"/><input type="button" value="注册"/>';
		echo '</form>';
    }else{
    	
    }
    
    
    ?>
    </div>
    <?php
	function GetMessageList($DB)
    {
        $sql = "SELECT a.code, c.username, note, latitude, longitude, create_date, altitude, address_zh FROM `cta_message` a JOIN cta_members_imei b ON a.send_account = b.imei JOIN cta_members c ON b.uid = c.uid ORDER BY `code` DESC LIMIT 0 , 30 ";
        $result=$DB->ExeSql($sql);
        $html = "";
        while($row=$DB->getRowResult($result))
        {
            $code=$row['code'];
            $username=$row['username'];
            $note = $row['note'];
            $latitude = $row['latitude'];
            $longitude = $row['longitude'];
            $altitude = $row['altitude'];
            $create_date = $row['create_date'];
            $address=$row['address_zh'];
            $html = "$html<tr><td>$code</td><td>$username</td><td>$note</td><td>$latitude<br/>$longitude<br/>$altitude</td><td>$address</td><td>$create_date</td><td><div id=\"map$code\"  style=\"width:300px; height:200px\"></div><script>smallMap(\"map$code\",$latitude,$longitude);</script></td></tr>\n";
        }
        return $html;
    }
    echo "<table>\n<tr><td>编号</td><td>账号</td><td>信息内容</td><td>经度，维度,海拔</td><td>可能地址</td><td>时间</td><td>地图</td></tr>\n";
	echo GetMessageList($DB);
	echo "</table>";
	?>
</body>
</html>