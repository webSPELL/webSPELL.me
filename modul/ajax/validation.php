<?php
	if(isset($_GET['typ'])) $type = $_GET['typ'];
	else $type = null;
	
	header('Content-Type: text/xml');
  header('Pragma: no-cache');
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	
	switch($type){
		case 'subdomain':
			$parts = explode(".",$_GET['domain']);
			if(strlen($parts[0]) == 0){
					$class = 'error';
					$message = 'You have to enter a subdomain.';
			}
			else{
				$count = mysql_num_rows(mysql_query("SELECT * FROM `users` WHERE `subdomain`='".$_GET['domain']."'"));
				if($count > 0){
					$class = 'error';
					$message = 'This subdomain already exists, please select another one.';
				}
				else{
					if(preg_match(SUBDOMAIN_REGEX,$_GET['domain']) == false){
						$class = 'error';
						$message = 'This subdomain is invalid. Only letters/numbers allowed, please select another one.';
					}
					else{
						$error = false;
						$query = mysql_query("SELECT `reserved` FROM `reserved`");
						while($ds = mysql_fetch_assoc($query)){
							if($ds['reserved'] == $parts[0]){
								$error = true;
								break;
							}
						}
						if($error){
							$class = 'error';
							$message = 'This subdomain is not available for registration, please select another one.';
						}
						else{
							$class = 'ok';
							$message = 'This subdomain is available for you to register.';
						}
					}
				}
			}
			echo '<result>';
				echo '<class>'.$class.'</class>';
				echo '<message>'.$message.'</message>';
			echo '</result>';
			break;
		
		case 'email':
			echo '<result>';
			if(mysql_num_rows(mysql_query("SELECT * FROM `users` WHERE `email`='".$_GET['email']."'"))){
				echo '<class>error</class>';
				echo '<message>This email already exists, please select another one.</message>';
			}
			else{
				if(check_email($_GET['email']) == false){
					echo '<class>error</class>';
					echo '<message>This eMail is invalid, please select another one.</message>';
				}
				else{
					echo '<class>ok</class>';
					echo '<message>This eMail is valid.</message>';
				}
			}
			echo '</result>';
			break;
		
		case 'password_strength':
			$strength = 0;
			$strength += preg_match("/([a-z]+)/", $_GET['password']);
			$strength += preg_match("/([A-Z]+)/", $_GET['password']);
			$strength += preg_match("/([0-9]+)/", $_GET['password']);
			$strength += preg_match("/([!�$%^&*]+)/", $_GET['password']);
			echo '<result>';
			switch($strength){
				case 4:
					echo "<class>progress_bar</class>";
					echo "<special>100%</special>";
					echo "<message>Very Strong</message>";
					break;
				case 3:
					echo "<class>progress_bar</class>";
					echo "<special>75%</special>";
					echo "<message>Strong</message>";
					break;
				case 2:
					echo "<class>progress_bar</class>";
					echo "<special>50%</special>";
					echo "<message>Weak</message>";
					break;
				case 1:
					echo "<class>progress_bar</class>";
					echo "<special>25%</special>";
					echo "<message>Very Weak</message>";
					break;
			}
			echo '</result>';
			break;
		case 'password':
			echo '<result>';
			if($_GET['pass1'] == $_GET['pass2']){
				echo "<class>ok</class>";
				echo "<message>Passwords match.</message>";
			}
			else{
				echo "<class>error</class>";
				echo "<message>Passwords do not match. Check passwords.</message>";
			}
			echo '</result>';
			break;
	}
?>