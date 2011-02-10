<?php
require_once '../config.inc.php';
require_once '../include/db_class.php';
$DB = new MySqlClass(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

	//增加一条用户消息
    function AddNewMessage($DB,$Note,$SendAccount,$Latitude,$Longitude,$Altitude,$AddressZH,$AddressEN)
    {
        $sql = "INSERT INTO cta_message (send_account,note,latitude,longitude,altitude,address_zh,address_en)VALUES('$SendAccount','$Note','$Latitude','$Longitude','$Altitude','$AddressZH','$AddressEN')";
        $result=$DB->ExeSql($sql);
        return "succeed";
    }
    
    //记录反向解释日志
    function ParseGPS($DB,$address,$imei)
    {
    	$sql = "INSERT INTO cta_parse_gps_log (imei,address)values('$imei','$address')";
    	$result=$DB->ExeSql($sql);
        return "succeed";
    }
    
   	//记录GMS日志
    function GsmLog($DB,$cellId,$locationAreaCode,$mobileCountryCode,$mobileNetworkCode,$imei,$Latitude,$Longitude,$address)
    {
    	$sql="INSERT INTO `cta_gsm_station_log` (`imei` ,`cell_id` ,`location_area_code` ,`mobile_country_code` ,`mobile_network_code` ,`latitude` ,`longitude`,`address`)VALUES ( '$imei', '$cellId', '$locationAreaCode', '$mobileCountryCode', '$mobileNetworkCode', '$Latitude', '$Longitude','$address');";
    	$result=$DB->ExeSql($sql);
        return "succeed";
    }
    
    //通过坐标取物理地址
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
	
	function FromGSMGetAddress($cellId,$locationAreaCode,$mobileCountryCode,$mobileNetworkCode)
	{
		$curlPost = '{"version": "1.1.0" ,"host": "maps.google.com","home_mobile_country_code": '.$mobileCountryCode.',"home_mobile_network_code":'.$mobileNetworkCode.',"address_language": "zh_CN","radio_type": "gsm","request_address": true ,"cell_towers":[{"cell_id":'.$cellId.',"location_area_code":'.$locationAreaCode.',"mobile_country_code":'.$mobileCountryCode.',"mobile_network_code":'.$mobileNetworkCode.',"timing_advance":5555}]}';
		$fp = fsockopen("www.google.com",80,$errno,$errstr,30);
		if(!$fp){
			echo "0,0";
			exit(1);
		}else{
			$out = "POST /loc/json HTTP/1.1\r\n";
			$out .= "Host: www.google.com\r\n";
			$out .= "Content-Type: application/jsonrequest\r\n";
			$out .= "Content-Length: ". strlen($curlPost) ."\r\n";
			$out .= "Connection: Close\r\n\r\n";
			$out .=  $curlPost;
			fwrite($fp, $out);
			do{
				$line = fgets($fp,128);
			}while($line!=="\r\n");
			$data = fread($fp, 8192);
			fclose($fp);
			return $data;
		}
	}
	
switch(@$_GET['fun'])
{
	case 'parse':
		$SendAccount = $_POST['SendAccount'];
		$Latitude = $_POST['Latitude'];
		$Longitude = $_POST['Longitude'];
		$AddressZH=GetAddress("http://maps.google.com/maps/api/geocode/json?latlng=".$Latitude.",".$Longitude."&sensor=true");
		ParseGPS($DB,$AddressZH,$SendAccount);
		echo $AddressZH;
		break;
	case 'add':
		$Note = $_POST['Note'];
		$SendAccount = $_POST['SendAccount'];
		$Latitude = $_POST['Latitude'];
		$Longitude = $_POST['Longitude'];
		$Altitude = $_POST['Altitude'];
		$AddressZH=GetAddress("http://maps.google.com/maps/api/geocode/json?latlng=".$Latitude.",".$Longitude."&sensor=true");
		$AddressEN=GetAddress("http://maps.google.cn/maps/api/geocode/json?latlng=".$Latitude.",".$Longitude."&sensor=true");;
		AddNewMessage($DB,$Note,$SendAccount,$Latitude,$Longitude,$AddressZH,$AddressEN);
		break;
	case 'gsm':
		$cellId = $_POST['cid'];
		$locationAreaCode = $_POST['lac'];
		$mobileCountryCode = $_POST['mcc'];
		$mobileNetworkCode = $_POST['mnc'];
		$imei = $_POST['imei'];
		$jsonString =FromGSMGetAddress($cellId,$locationAreaCode,$mobileCountryCode,$mobileNetworkCode);
		$arr = json_decode($jsonString,true);
		$latitude = empty($arr["location"]["latitude"])?0.0:$arr["location"]["latitude"];
		$longitude = empty($arr["location"]["longitude"])?0.0:$arr["location"]["longitude"];
		echo $latitude.",".$longitude;
		$address = GetAddress("http://maps.google.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&sensor=true");
		GsmLog($DB,$cellId,$locationAreaCode,$mobileCountryCode,$mobileNetworkCode,$imei,$latitude,$longitude,$address);
		break;
}
    

?>
