<?php 
if (!isset($_POST['reg_check'])) {
header("Location: http://www.webspell.me/");
exit;
}
//if (!isset($_POST['reg_check'])) die('You are not allowed to access this file directly.');

include("include/func.php");
include("include/mysql.php");
$domaincheck = $_POST['domain'];
$domain = $_POST['domain'];
$version = $_POST['version'];
$subdomain = $_POST['subdomain'];
$admin = $_POST['admin'];
$password = $_POST['password'];
$email = $_POST['email'];
$clan = $_POST['clan'];
$template = $_POST['template'];
$url = "http://".$subdomain;
$datetime = time();
$password_raw = $password;
$password = md5($password);
$secret = $subdomain.$admin.$email;
$auth = md5($secret);

//echo 'password: '.$password.'<br />';
//echo 'password_raw: '.$password_raw.'<br />';
//die();

$query = mysql_query("SELECT id FROM `domains` WHERE `domain`='".$domain."'");
	 while($row =  mysql_fetch_array($query, MYSQL_BOTH)) {
	 	$domainid = $row["id"];
	 }

$count = mysql_num_rows(mysql_query("SELECT * FROM `users` WHERE `subdomain`='".$subdomain."'"));
//	if (!$count) {
//    		echo 'Could not run query: ' . mysql_error($count);
//    		exit;
//	}
if($count > 0) {
	$register_report =& regfail("subdomain_exists");
}else{
if (empty($subdomain) || empty($admin) || empty($password) || empty($clan) || empty($template)) {
	$register_report =& regfail("required_not_available");
}else{
if (!preg_match('/^[A-z0-9._-]+$/', $subdomain)){
	$register_report =& regfail("subdomain_invalid");
}else{
	   	$match = array();
		$query = mysql_query("SELECT `reserved` FROM `reserved`");
		while ($row = mysql_fetch_array($query))
		{
    		$match[] = $row[0];
		}
		if(array_intersect($match, explode('.', $subdomain))) {
	$register_report =& regfail("subdomain_notallowed");
}
}
}
if (empty($email)) {
	$register_report =& regfail("required_not_available");
	}else{

	if (!check_email($email)) {
		$register_report =& regfail("email_invalid");
	}

}

//----------------------------------

$lock = mysql_num_rows(mysql_query("SELECT * FROM `locks` WHERE `subdomain`='".$subdomain."'"));
//	if (!$lock) {
//    		echo 'Could not run query: ' . mysql_error($lock);
//    		exit;
//	}
//var_dump($lock);
if($lock == 0) {

	$query = mysql_query("INSERT into `locks` (`subdomain`, `username`, `email`) VALUES ('".$subdomain."', '".$admin."', '".$email."')");
//	if (!$query) {
//    		echo 'Could not run query: ' . mysql_error($query);
//    		exit;
//	}
	//echo 'locking subdomain<br />';
//	echo $subdomain.'<br />';
//	echo $template.'<br />';
//	echo $admin.'<br />';
//	echo $password.'<br />';
//	echo $password_raw.'<br />';
//	echo $email.'<br />';;
//	echo $clan.'<br />';
//	echo $auth.'<br />';
//	echo $datetime.'<br />';
//	echo $version.'<br />';
//	echo $domain.'<br />';
//	echo $domainid.'<br />';
//	die();

	register_subdomain($subdomain, $template, $admin, $password, $email, $clan, $auth, $datetime, $version, $domain, $domainid);
	send_email($subdomain, $admin, $password_raw, $email, $clan, $template, $auth, $version);
	//echo 'register domain for user<br />';
	$query = mysql_query("DELETE from `locks` WHERE `subdomain`='".$subdomain."'");
//	if (!$query) {
//    		echo 'Could not run query: ' . mysql_error($query);
//    		exit;
//	}

	//echo 'remove lock<br />';
	$register_report =& regdone($subdomain);

}else{

	$lock2 = mysql_num_rows(mysql_query("SELECT * FROM `locks` WHERE `subdomain`='".$subdomain."' AND `username`='".$admin."' AND `email`='".$email."'"));
//	if (!$lock2) {
//    		echo 'Could not run query: ' . mysql_error($lock2);
//    		exit;
//	}
	//var_dump($lock2);
	if($lock2 == 1) {
	
	$now = date("Y-m-d H:i:s");
	$query = mysql_query("UPDATE `locks` SET `timestamp`='".$now."' WHERE `subdomain`='".$subdomain."' AND `username`='".$admin."' AND `email`='".$email."'");
//	if (!$query) {
//    		echo 'Could not run query: ' . mysql_error($query);
//    		exit;
//	}

	//echo 'update lock<br />';
//	echo $subdomain.'<br />';
//	echo $template.'<br />';
//	echo $admin.'<br />';
//	echo $password.'<br />';
//	echo $password_raw.'<br />';
//	echo $email.'<br />';;
//	echo $clan.'<br />';
//	echo $auth.'<br />';
//	echo $datetime.'<br />';
//	echo $version.'<br />';
//	echo $domain.'<br />';
//	echo $domainid.'<br />';
//	die();

	register_subdomain($subdomain, $template, $admin, $password, $email, $clan, $auth, $datetime, $version, $domain, $domainid);
	send_email($subdomain, $admin, $password_raw, $email, $clan, $template, $auth, $version);
	//echo 'register domain for user<br />';
	$query = mysql_query("DELETE from `locks` WHERE `subdomain`='".$subdomain."'");
//	if (!$query) {
//    		echo 'Could not run query: ' . mysql_error($query);
//    		exit;
//	}

	//echo 'remove lock<br />';
	$register_report =& regdone($subdomain);

	}else{
	
	$query = mysql_query("SELECT UNIX_TIMESTAMP(`timestamp`) FROM `locks` WHERE `subdomain`='".$subdomain."'");
//	if (!$query) {
//    		echo 'Could not run query: ' . mysql_error($query);
//    		exit;
//	}
	$row = mysql_fetch_row($query);
	$regtime = $row[0];
	//var_dump($query);
	$datetime = time();
	$now = date("Y-m-d H:i:s"); 
	$difference = $datetime - $regtime;
	//echo 'get timestamp info<br />';
	//echo 'difference: '.$difference.'<br />';
	if($difference > 60) {
		
		$query = mysql_query("UPDATE `locks` SET `timestamp`='".$now."', `username`='".$admin."', `email`='".$email."' WHERE `subdomain`='".$subdomain."'");
//			if (!$query) {
//   				echo 'Could not run query: ' . mysql_error($query);
//    			exit;
//			}

		//echo 'reset lock after 120 sec.<br />';
//	echo $subdomain.'<br />';
//	echo $template.'<br />';
//	echo $admin.'<br />';
//	echo $password.'<br />';
//	echo $password_raw.'<br />';
//	echo $email.'<br />';;
//	echo $clan.'<br />';
//	echo $auth.'<br />';
//	echo $datetime.'<br />';
//	echo $version.'<br />';
//	echo $domain.'<br />';
//	echo $domainid.'<br />';
//	die();
	
		register_subdomain($subdomain, $template, $admin, $password, $email, $clan, $auth, $datetime, $version, $domain, $domainid);
		send_email($subdomain, $admin, $password_raw, $email, $clan, $template, $auth, $version);
		//echo 'register domain for user<br />';
		$query = mysql_query("DELETE from `locks` WHERE `subdomain`='".$subdomain."'");
//			if (!$query) {
//    				echo 'Could not run query: ' . mysql_error($query);
//    			exit;
//			}

		//echo 'remove lock<br />';
		$register_report =& regdone($subdomain);

	}else{
		$register_report =& regfail(subdomain_inuse);
		//echo 'subdomain currently in progress by another user<br />';
	}

	}
}	

// -------------------------------------

}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>webSPELL.me - Your All-in-one Clan Hosting Solutions</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="layout.css" rel="stylesheet" type="text/css" />

</head>

<body id="page6">
    <!--header -->
    <div id="header">
   	  <div class="main">
        	<div class="top">
            	<ul class="menu">
                	<li><a href="index.html" class="active">Home</a></li>
                    <li><a href="register.html">Register</a></li>
                    <li><a href="#">Coming soon</a></li>
                    <li class="last"><a href="contact.html">Contact</a></li>
                </ul>
                <div class="logo"><a href="index.html"><img src="images/logo.jpg" alt="" /></a></div>
            </div>
            <div class="container">
            	<div class="banners">
               	  <a href="register.html"><img src="images/banner1.jpg" alt="" /></a><br />
                    <a href="register.html"><img src="images/banner2.jpg" alt="" /></a>
                </div>
                <div class="header-text">
                	<h3>Registration easy as 1-2-3</h3>
                    You wanto to use the webSPELL CMS for your Clan website, but don't know how to install it, or how to get a new design for it?
                    <ul>
                    	<li><a href="register.html"><img src="images/header-list-item1.gif" alt="" /></a></li>
                        <li><a href="register.html"><img src="images/header-list-item2.gif" alt="" /></a></li>
                        <li><a href="register.html"><img src="images/header-list-item3.gif" alt="" /></a></li>
                    </ul>
                    <div class="buttons">
                    	<a href="register.html"><img src="images/header-button1.jpg" alt="" /></a><a href="register.html"><img src="images/header-button2.jpg" alt="" /></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--header end-->
    <div id="content">
        <div class="main">
            <div class="indent">
            	<?php echo $register_report; ?>
</div>       
        </div>
    </div>
    <!--footer -->
    <div id="footer">
        <div class="main">
        	<div class="text">webSPELL.me &copy; 2009 | <a href="tos.html">TOS</a> | <a href="imprint.html">Imprint</a></div>
        </div>
    </div>
    <!--footer end-->
	<!-- Piwik -->
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://derchris.eu/piwik/" : "http://derchris.eu/piwik/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://derchris.eu/piwik/piwik.php?idsite=1" style="border:0" alt=""/></p></noscript>
<!-- End Piwik Tag -->
</body>

</html>
<?php exec("sudo /var/www/webspell.me/restartws"); ?>
