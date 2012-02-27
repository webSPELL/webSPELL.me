<?php
function registerDomain($ID){
	$get = mysql_query("SELECT * FROM `users` WHERE id='".$ID."'",MYSQL_LINK);
	$data = mysql_fetch_assoc($get);
	$subdomain = $data['subdomain'];
	$subdomain_parts = explode(".",$subdomain);
	
	$template = $data['template'];
	$version = $data['version'];
	$domain = $data['domain'];
	$org = $data['clan'];
	$admin = $data['admin'];
	$email = $data['email'];
	$password = $data['password'];
	
	$mysql_get 	= mysql_fetch_assoc(mysql_query("SELECT id FROM `domains` WHERE `domain`='".$domain."'",MYSQL_LINK));
	$domainID 	= $mysql_get['id'];
	
	$mysql_user_db_name = $domainID.'_'.$subdomain_parts[0];
	
	system("sudo ".INSTALLATIONS_SCRIPT." ".$subdomain_parts[0]." $template $version $domain $domainID");
	
	$mysql_user_db = mysql_connect(MYSQL_HOST,MYSQL_ROOT_USER, MYSQL_ROOT_PASS,true);
	mysql_select_db($mysql_user_db_name,$mysql_user_db);
	
	mysql_query("UPDATE `webs_settings` SET `hpurl`='".$subdomain."', `clanname`='".$org."', `adminname`='".$admin."', `adminemail`='".$email."'",$mysql_user_db);
	mysql_query("UPDATE `webs_contact` SET `email`='".$email."' WHERE contactID='1'",$mysql_user_db);
	mysql_query("UPDATE `webs_user` SET `registerdate`='".time()."', `lastlogin`='".time()."', `password`='".$password."', `username`='".$admin."', `nickname`='".$admin."', `email`='".$email."'",$mysql_user_db);
	mysql_query("ALTER DATABASE `$mysql_user_db_name` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci",$mysql_user_db);
	
	mysql_query("DELETE FROM `locks` WHERE `subdomain`='".$subdomain."'");
	mysql_close($mysql_user_db);
}

function sendRegisterMail($id) {
	$get = mysql_query("SELECT * FROM `users` WHERE id='".$id."'",MYSQL_LINK);
	$data = mysql_fetch_assoc($get);
	$template = $data['template'];
	$version = $data['version'];
	$domain = $data['domain'];
	$org = $data['clan'];
	$admin = $data['admin'];
	$email = $data['email'];
	$subdomain = $data['subdomain'];
	$auth = $data['auth'];
	
	$query = mysql_query("SELECT name, version FROM `templates` WHERE `template`='".$template."'",MYSQL_LINK);
	 while($row =  mysql_fetch_array($query, MYSQL_BOTH)) {
	 	$template = $row["name"];
		$version = $row["version"];
	 }

// subject
$subject = 'webSPELL.me - Registration';

// message
$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>webSPELL.me - Registration</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<style type="text/css">
<!--
body { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; color: #666666; background-color: #FFFFFF; border: 0px; margin: 5px; }
h3 {font-size: 16px; color: #515151; padding: 0px; margin-top: 25px; text-align: ; }
img { border: none; }
.center { margin-left: auto; margin-right: auto; }
#main { width: 650px; }
#footer { color: #8C8C8C; }
hr { height: 1px; background-color: #cdcdcd; color: #cdcdcd; border: none; margin: 6px 0px; }
a { color: #0066FF; text-decoration: none; }
a:hover { text-decoration: underline; }
-->
</style>
<!--[if lte IE 7]>
<style type="text/css">
hr { margin: 0px; }
</style>
<![endif]-->
	</head>
	<body>
		<div id="main" class="center">
		
			<h3>Thank you for creating an account with webSPELL.me</h3>
			<span>Here are the details you gave us during the registration:<br />
<br /><br />
  <table>
    <tr>
      <td>Subdomain:</td>
      <td><b><a href="'.$subdomain.'" target="_blank">'.$subdomain.'</a></b></td>
    </tr>
    <tr>
      <td>Admin account:</td>
      <td><b>'.$admin.'</b></td>
    </tr>
    <tr>
      <td>eMail:</td>
      <td><b>'.$email.'</b></td>
    </tr>
    <tr>
      <td>Clan/Organisation Name:</td>
      <td><b>'.$org.'</b></td>
    </tr>
    <tr>
      <td>Version:</td>
      <td><b>'.$version.'</b></td>
    </tr>	
    <tr>
      <td>Template:</td>
      <td><b>'.$template.'</b></td>
    </tr>
  </table><br />
<br /><br />
   Please keep the username/password somewhere safe. They will be required to login into your new webSPELL site, and you will be able to use the same details on the webSPELL.me site, soon<br />
   In order to activate your account, you need to follow this link:<br /><br /><br />
  <a href="http://www.webspell.me/index.php?modul=auth&id='.$auth.'" target="_blank">http://www.webspell.me/index.php?modul=auth&id='.$auth.'</a><br /><br />
<br />
The webSPELL.me Team</span>
			<hr />
		</div>
	</body>
</html>';

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
$headers .= 'Return-Path: <noreply@webspell.me>' . "\r\n";
$headers .= 'From: webSPELL.me <noreply@webspell.me>' . "\r\n";
return mail($email, $subject, $message, $headers);
}
?>