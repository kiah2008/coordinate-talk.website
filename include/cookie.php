<?php
if(!empty($_COOKIE['Cta_auth'])) {
	list($Cta_uid, $Cta_username,$Cta_imei) = explode("\t", uc_authcode($_COOKIE['Cta_auth'], 'DECODE'));
} else {
	$Cta_uid = $Cta_username = $Cta_imei = '';
}