<?php 
require_once '../config.inc.php';
require_once '../include/db_class.php';
$colorArray = array("black", "brown", "green", "purple", "yellow", "blue", "gray", "orange", "red", "white");
$DB = new MySqlClass(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if(!$_GET['uid']){//无此用户ID时显示所有信息
	$sql="";
}else{
	$uid=$_GET['uid'];
	
	$sql="SELECT * FROM `cta_message` a JOIN `cta_members_imei` b on a.send_account=b.imei WHERE b.uid=$uid ORDER BY `a`.`code` DESC LIMIT 0 , 8 ";
	$result=$DB->ExeSql($sql);
	$imgurl = "http://maps.google.com/maps/api/staticmap?&size=305x305&maptype=roadmap";
	$listnote="";
	$bigMap="";
	$i=1;
	while($row=$DB->getRowResult($result)){
		$note = $row['note'];
		$latitude = $row['latitude'];
		$longitude = $row['longitude'];
        $create_date = $row['create_date'];
		$imgurl .="&markers=color:".$colorArray[$i-1]."|label:$i|$latitude,$longitude";
		$listnote .="<li>$note|$create_date</li>";
		$bigMap .="['$note', $latitude, $longitude, 4],";
		$i +=1;
	}
	$imgurl.="&sensor=false";
	

}
switch(@$_GET['fun'])
{
	case 'big':

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Coordinate Talk Home</title>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
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
function setMarkers(map, locations) {  
	var image = new google.maps.MarkerImage('http://android.guohai.org/images/beachflag.png',      
			new google.maps.Size(27, 32),             
			new google.maps.Point(0,0),            
			new google.maps.Point(0, 32));   
	var shadow = new google.maps.MarkerImage('http://android.guohai.org/images/beachflag_shadow.png',       
      
			new google.maps.Size(37, 32),       
			new google.maps.Point(0,0),       
			new google.maps.Point(0, 32));       
   
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
<?php 
echo "var beaches = [".$bigMap."['测试新版本程序，使用了json传送数据。', 40.0811934471119, 116.347553730008, 4]];";
?>
</script>
<style type="text/css">
body{
	margin:0px;
	padding:0px;
}
</style>
</head>
<body onload="initialize()">
<div id="mainMap" style="width:100%;height:300px;margin:0px;padding:0px;"></div>
111
</body>
</html>
<?php 
		break;
	case 'small':
?>
function creatediv(){
	var DIV = document.createElement("div");
	DIV.style.position = "absolute";
	DIV.style.width="500px";
	DIV.style.height="600px"
	DIV.style.left=(document.body.offsetWidth-parseInt(DIV.style.width))/2+"px";
	DIV.style.top=(document.body.offsetHeight-parseInt(DIV.style.height))/2+"px";
	DIV.style.border= "1px solid #000";
	DIV.style.background="#fff";
	DIV.innerHTML="<iframe src=\"http://android.guohai.org/api/display.php?fun=big&uid=<?php echo $_GET['uid'];?>\" width=\"100%\" height=\"100%\" frameborder=\"0\"></iframe>";
	document.body.appendChild(DIV);
}

<?php
echo "document.write('<div style=\"text-align:center;border-style:solid;border-width:1px;margin:0px;padding:0px;width:315px;font:12px/1.6em Verdana,Helvetica,Arial,sans-serif;\"><div style=\"text-align:left;border-bottom-style:solid;border-bottom-width:1px;background-color:#e0ecff;width:303px;padding:5px;\"><img src=\"http://guohai.org/ucenter/avatar.php?uid=$uid&size=small\" /></div><div style=\"width:100%;padding:5px;text-align:left;\">');";
	echo "document.write('<img style=\"cursor:hand;\" onclick=\"creatediv();\" src=\"$imgurl\"><ol>$listnote</ol>');";
	echo "document.write('</div></div>');";
		break;
	
}
?>
