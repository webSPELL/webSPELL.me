<?php 
$auth = $_GET['id'];
$get = mysql_query("SELECT * FROM `users` WHERE `auth`='".$auth."'");
if(mysql_num_rows($get) == 1){
	$data = mysql_fetch_assoc($get);
	$user = $data['admin'];
	$subdomain = $data['subdomain'];
	$activated = $data['activated'];
	$version = $data['version'];
	$domain = $data['domain'];
	if($activated == 1){
		?>
		<h2> Account already activated</h2>
		<p>Your account has already been activated.<br />
    	To go straight to your new webSPELL page, click the following link:<br /><br />
    	<a href="http://<?php echo $subdomain;?>">http://<?php echo $subdomain;?></a><br /><br />
    	Use the login details you provided during the registration.</p>
		<?php
	}
	else{
		mysql_query("UPDATE `users` SET `activated`='1' WHERE `auth`='".$auth."'");
		$subdomain2 = explode('.', $subdomain);
		system("sudo ".ACTIVATION_SCRIPT_1." $subdomain2[0] $domain $version");
		exec("sudo ".ACTIVATION_SCRIPT_2);
		?>
		<h2> Thank you for registration</h2>
    <p>Thank you <?php echo $user;?><br/><br />
    Your account is now activated and ready for use.<br />
    To go straight to your new webSPELL page, click on the following link:<br /><br />
    <a href="http://<?php echo $subdomain;?>">http://<?php echo $subdomain;?></a><br /><br />
    Use the login details you provided during the registration.</p>
		<?php
	}
}
else{
	?>
	<h2> There was a problem with the auth code</h2>
		<p>We are sorry, but there was a problem with the auth code<br />
		Please make sure that there are no line breaks in the email if you click on the link.<br />
		Try to copy & paste the link into a browser window.<br />
		If it is still not working, please contact the webSPELL.me Team for support.</p>
	<?php
}
?>