<?php
require_once '../config.inc.php';
require_once '../include/db_class.php';

$colorArray = array("black", "brown", "green", "purple", "yellow", "blue", "gray", "orange", "red", "white");
$DB = new MySqlClass(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if(!$_GET['uid']){//无此用户ID时显示所有信息
	$sql="";
}else{
	$uid=$_GET['uid'];
	echo "document.write('<div style=\"text-align:center;border-style:solid;border-width:1px;margin:0px;padding:0px;width:315px;font:12px/1.6em Verdana,Helvetica,Arial,sans-serif;\"><div style=\"text-align:left;border-bottom-style:solid;border-bottom-width:1px;background-color:#e0ecff;width:303px;padding:5px;\"><img src=\"http://guohai.org/ucenter/avatar.php?uid=$uid&size=small\" /></div><div style=\"width:100%;padding:5px;text-align:left;\">');";
	$sql="SELECT * FROM `cta_message` a JOIN `cta_members_imei` b on a.send_account=b.imei WHERE b.uid=$uid ORDER BY `a`.`code` DESC LIMIT 0 , 8 ";
	$result=$DB->ExeSql($sql);
	$imgurl = "http://maps.google.com/maps/api/staticmap?&size=305x305&maptype=roadmap";
	$listnote="";
	$i=1;
	while($row=$DB->getRowResult($result)){
		$note = $row['note'];
		$latitude = $row['latitude'];
		$longitude = $row['longitude'];
        $create_date = $row['create_date'];
		$imgurl .="&markers=color:".$colorArray[$i-1]."|label:$i|$latitude,$longitude";
		$listnote .="<li>$note|$create_date</li>";
		$i +=1;
	}
	$imgurl.="&sensor=false";
	echo "document.write('<img src=\"$imgurl\"><ol>$listnote</ol>');";
}
?>
document.write('</div></div>');