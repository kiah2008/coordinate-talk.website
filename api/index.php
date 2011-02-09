<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>guohai.org android api</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>

<?php
require_once '../config.php';
require_once '../include/db_class.php';
$DB = new MySqlClass(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

    function AddNewMessage($DB,$Note,$SendAccount,$Latitude,$Longitude,$Altitude,$AddressZH,$AddressEN)
    {
        $sql = "INSERT INTO cta_message (send_account,note,latitude,longitude,altitude,address_zh,address_en)VALUES('$SendAccount','$Note','$Latitude','$Longitude','$Altitude','$AddressZH','$AddressEN')";
        $result=$DB->ExeSql($sql);
        return "succeed";
    }
    function GetAddress($url)
	{
		$file = fopen($url,"r");
		while(!feof($file))
		{
			$line .= fgets($file,1024);
			
		}
		fclose($file);
		$address=json_decode($line);
		return $address->results[0]->formatted_address;
	}

    
$Note = $_POST['Note'];
$SendAccount = $_POST['SendAccount'];
$Latitude = $_POST['Latitude'];
$Longitude = $_POST['Longitude'];
$Altitude = $_POST['Altitude'];
$AddressZH=GetAddress("http://maps.google.com/maps/api/geocode/json?latlng=".$Latitude.",".$Longitude."&sensor=true");
$AddressEN=GetAddress("http://maps.google.cn/maps/api/geocode/json?latlng=".$Latitude.",".$Longitude."&sensor=true");;
AddNewMessage($DB,$Note,$SendAccount,$Latitude,$Longitude,$AddressZH,$AddressEN);
?>

</body>
</html>
