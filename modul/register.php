<?php 
if (isset($_POST['check'])) {
	$_SESSION['order'] = array();
	
	$validation_error = false;
	
	$pass1 			= $_POST['password'];
	$pass2 			= $_POST['password2'];
	$_SESSION['order']['pass'] = $pass1;
	$version 		= $_SESSION['order']['version'] 	= $_POST['version'];
	$subdomain 	= $_SESSION['order']['subdomain']	= $_POST['subdomain'];
	$domain 		= $_SESSION['order']['domain'] 		= $_POST['domain'];
	$admin			= $_SESSION['order']['admin'] 		= $_POST['admin'];
	$email			= $_SESSION['order']['email'] 		= $_POST['email'];
	$org				= $_SESSION['order']['org']		 		= $_POST['clan'];
	$template		= $_SESSION['order']['template']	= $_POST['template'];
	
	$domain_msg = $domain_class = "";
	$domainparts = explode(".",$subdomain);
	if(strlen($domainparts[0]) == 0){
		$domain_msg = "You have to enter a subdomain.";
		$domain_class = "error";
		$validation_error = true;
	}
	else{
		$count = mysql_num_rows(mysql_query("SELECT * FROM `users` WHERE `subdomain`='".$subdomain."'"));
		if($count > 0){
			$domain_class = 'error';
			$domain_msg = 'This subdomain already exists, please select another one.';
			$validation_error = true;
		}
		else{
			if(preg_match(SUBDOMAIN_REGEX,$subdomain) == false){
				$domain_class = 'error';
				$domain_msg = 'This subdomain is invalid. Only letters/numbers allowed, please select another one.';
				$validation_error = true;
			}
			else{
				$d_error = false;
				$query = mysql_query("SELECT `reserved` FROM `reserved`");
				while($ds = mysql_fetch_assoc($query)){
					if($ds['reserved'] == $domainparts[0]){
						$d_error = true;
						break;
					}
				}
				if($d_error){
					$domain_class = 'error';
					$domain_msg = 'This subdomain is not available for registration, please select another one.';
					$validation_error = true;
				}
				else{
					$get = mysql_query("SELECT username, email, UNIX_TIMESTAMP(`timestamp`) AS `lock_time` FROM `locks` WHERE `subdomain`='".$subdomain."'");
					if(mysql_num_rows($get) == 0){
						mysql_query("INSERT into `locks` (`subdomain`, `username`, `email`) VALUES ('".$subdomain."', '".$admin."', '".$email."')");
						$domain_class = 'ok';
						$domain_msg = 'This subdomain is available for you to register.';
					}
					else{
						$lock_data = mysql_fetch_assoc($get);
						$lock_user = $lock_data['username'];
						$lock_email = $lock_data['email'];
						$lock_time = $lock_data['lock_time'];
						if($lock_user != $admin || $lock_email != $email){
							$domain_class = 'error';
							$domain_msg = 'Another user is currently registering the same subdomain. Please go back and select another one.';
							$validation_error = true;
						}
						elseif(empty($lock_user) && empty($lock_email)){
							mysql_query("INSERT into `locks` (`subdomain`, `username`, `email`) VALUES ('".$subdomain."', '".$admin."', '".$email."')");
							$domain_class = 'ok';
							$domain_msg = 'This subdomain is available for you to register.';
						}
					}
				}
			}
		}
	}
	
	$admin_msg = $admin_class = "";
	if(empty($admin)){
		$admin_msg = 'No Admin entered. Please go back and select one.';
		$admin_class = 'error';
		$validation_error = true;
	}
	
	$password_msg = $password_class = "";
	if(strlen(trim($pass1)) == 0){
		$password_msg = 'No Password selected. Please go back and select one.';
		$password_class = 'error';
		$validation_error = true;
	}
	elseif($pass1 != $pass2){
		$password_msg = 'Passwords do not match. Check passwords.';
		$password_class = 'error';
		$validation_error = true;
	}
	
	$org_msg = $org_class = "";
	if(empty($org)){
		$org_msg = 'No Clan/Organisation selected. Please go back and select one.';
		$org_class = 'error';
		$validation_error = true;
	}
	
	$email_msg = $email_class = "";
	if(empty($email)){
		$email_msg = 'No eMail selected. Please go back and select one.';
		$email_class = 'error';
		$validation_error = true;
	}
	else{
		if(mysql_num_rows(mysql_query("SELECT * FROM `users` WHERE `email`='".$email."'"))){
			$email_msg = 'Email already registered. Please go back and select another one.';
			$email_class = 'error';
			$validation_error = true;
		}
		else{
			if(!check_email($email)){
				$email_msg = 'Invalid eMail. This eMail doesn\'t exist. Please go back and select another one.';
				$email_class = 'error';
				$validation_error = true;
			}
		}
	}
	
	$template_msg = $template_class = "";
	if(empty($template)){
		$template_msg = 'No Template selected. Please go back and select one.';
		$template_class = 'error';
		$validation_error = true;
	}
	
	$version_msg = $version_class = "";
	if(empty($version)){
		$version_msg = 'No Version selected. Please go back and select one.';
		$version_class = 'error';
		$validation_error = true;
	}
	
	if(empty($domain)){
		$domain_msg = 'No Domain selected. Please go back and select one.';
		$domain_class = 'error';
		$validation_error = true;
	}
	
	
	$captcha_msg = $captcha_class = "";
	require_once ('system/recaptchalib.php');
  $privatekey = "6LevSgUAAAAAALCdvgW09KhivVg86QwM07ULLzQ2";
	$resp = recaptcha_check_answer ($privatekey,
                                	$_SERVER["REMOTE_ADDR"],
                                	$_POST["recaptcha_challenge_field"],
                                	$_POST["recaptcha_response_field"]);
	if (!$resp->is_valid) {
		$captcha_msg = 'The captcha you entered is not correct. Please try again.';
		$captcha_class = 'error';
		$validation_error = true;
	}
	?>
	<div class="indent">
            	<h2>Registration - checking details ...</h2>
	<script type="text/javascript">
		var init = false;
		function initInstall(){
			if(init) return false;
			document.getElementById('register_form').submit();
			document.getElementById('register_button').value = '... in Progress';
			document.getElementById('register_button').disabled = true;
			init = true;
			return true;
		}
	</script>
    <?php if($validation_error == false){ ?><form action="index.php?modul=register" method="post" enctype="multipart/form-data" id="register_form" onsubmit="return initInstall();"><?php } ?>
     <table width="100%" border="0">
      <tr>
        <td class="lable">Version:</td>
	 			<td class="userinput"><?php echo $version ?></td>
				<td class="small <?=$version_class;?>"><?=$version_msg;?></td>
      </tr>
	  	<tr>
        <td class="lable">Subdomain:</td>
	 			<td class="userinput"><?php echo $subdomain; ?></td>
				<td class="small <?=$domain_class;?>"><?=$domain_msg;?></td>
      </tr>
      <tr>
        <td class="lable">Admin:</td>
        <td class="userinput"><?php echo $admin ?></td>
				<td class="small <?=$admin_class;?>"><?=$admin_msg;?></td>
      </tr>
      <tr>
        <td class="lable">Password:</td>
        <td class="userinput"><?php echo $pass1[0].str_repeat("*",strlen($pass1)-2).$pass1[strlen($pass1)-1]; ?></td>
				<td class="small <?=$password_class;?>"><?=$password_msg;?></td>
      </tr>
      <tr>
        <td class="lable">eMail:</td>
        <td class="userinput"><?php echo $email ?></td>
				<td class="small <?=$email_class;?>"><?=$email_msg;?></td>
      </tr>
      <tr>
        <td class="lable">Clan/Organisation:</td>
        <td class="userinput"><?php echo $org ?></td>
				<td class="small <?=$org_class;?>"><?=$org_msg;?></td>
      </tr>
      <tr>
        <td class="lable">Template:</td>
        <td class="userinput"><?php echo $template ?></td>
				<td class="small <?=$template_class;?>"><?=$template_msg;?></td>
      </tr>
      <tr>
				<td><input type="hidden" name="register" value="test" /></td>
				<td class="small <?=$captcha_class;?>" colspan="2"><?php echo $captcha_msg; ?></td>
			</tr>
    </table>
     <div id="form_elements" align="center">
	<?php if($validation_error == false){ ?><br /><input type="submit" name="register" id="register_button" value="Register" /><?php } ?>
    <input type="reset" name="reset" id="reset" value="Back" onclick="window.location.href='index.php?modul=register';" /><br />
	 </div>
    <?php if($validation_error == false){ ?></form> <?php } ?>
</div>
	<?php
} elseif (isset($_POST['register'])) {
	$pass 			= $_SESSION['order']['pass'];
	$version 		= $_SESSION['order']['version'];
	$subdomain 	= $_SESSION['order']['subdomain'];
	$domain 		= $_SESSION['order']['domain'];
	$admin			= $_SESSION['order']['admin'];
	$email			= $_SESSION['order']['email'];
	$org				= $_SESSION['order']['org'];
	$template		= $_SESSION['order']['template'];
	
	$password = md5($pass);
	$secret = $subdomain.$admin.$email;
	$auth = md5($secret);
	
	$mysql_get 	= mysql_fetch_assoc(mysql_query("SELECT id FROM `domains` WHERE `domain`='".$domain."'"));
	$domainID 	= $mysql_get['id'];
	$subdomain_parts = explode(".",$subdomain);
	
	$mysql_user_db_name = $domainID.'_'.$subdomain_parts[0];
	mysql_query("INSERT INTO `users` (`subdomain`, `admin`, `password`, `email`, `clan`, `template`, `version`, `domain`, `auth`) VALUES ('".$subdomain."', '".$admin."', '".$password."', '".$email."', '".$org."', '".$template."', '".$version."', '".$domain."', '".$auth."')");
	$id = mysql_insert_id();
	registerDomain($id);
	sendRegisterMail($id);
	$_SESSION['order'] = array();
	?>
	<h2> Thank you for registration</h2>
    <p>We have successfully setup your Website with the webSPELL CMS already installed.</p>
    <p>You will shortly receive an email with an activation link and your userdetails.
    You need to follow this link, otherwise your subdomain will not be active.</p>
    <p>After that you are able to login into your new webSPELL site and change the settings to your needs.<br /></p>
	<?php
} else {
?>
<div class="indent">
    <h2>Registration</h2>
    <form action="index.php?modul=register" method="post" enctype="multipart/form-data" id="register">
        <table width="100%" border="0">
        <tr>
            <td class="lable">Version:</td>
            <td class="userinput"><select name="version" id="version" size="1" onchange="loadTemplates(this,'template'); load_template_preview('');">
            <?php 
            $query = mysql_query("SELECT version, short FROM `versions`");
            while ($row = mysql_fetch_array($query, MYSQL_BOTH)) {
            	$sel = '';
            	if(isset($_SESSION['order'])){
            		if($row["short"] == $_SESSION['order']['version']) $sel = ' selected="selected"';
								else $sel = '';
            	}
              echo '<option value="'.$row["short"].'"'.$sel.'>'.$row["version"].'</option>';
            }
            ?>
            </select></td>
        </tr>
				<tr>
        	<td class="lable">Subdomain:</td>
        	<td class="userinput"><input value="<?php echo @$_SESSION['order']['subdomain'];?>" name="subdomain" type="text" id="subdomain" size="50" onchange="validateSubdomain(this.value,'subdomain_exists');" /> @ <select name="domain" id="domain" size="1" onchange="updateSubdomain(this);" ><option value="" selected="selected"></option>
        <?php 
        $query = mysql_query("SELECT domain FROM `domains`");
        while ($row = mysql_fetch_array($query, MYSQL_BOTH)) {
        	$sel = '';
        	if(isset($_SESSION['order'])){
            if($row["domain"] == $_SESSION['order']['domain']) $sel = ' selected="selected"';
						else $sel = '';
           }
           echo '<option value="'.$row["domain"].'"'.$sel.'>'.$row["domain"].'</option>';
        }
        ?>
    		</select><div id="subdomain_exists"></div></td>
				</tr>
				<tr>
    			<td class="lable">Admin:</td>
    			<td class="userinput"><input value="<?php echo @$_SESSION['order']['admin'];?>" name="admin" type="text" id="admin" size="50"/></td>
				</tr>
				<tr>
    			<td class="lable">Password:</td>
    			<td class="userinput"><input name="password" type="password" id="password" size="50" onchange="validatePasswordStrength(this.value,'password_strength');"  onblur="validatePasswordStrength(this.value,'password_strength');"/><div id="password_strength"></div></td>
				</tr>
				<tr>
    			<td class="lable">Password again:</td>
    			<td class="userinput"><input name="password2" type="password" id="password2" onchange="validatePassword(this.value,document.getElementById('password').value,'password_check');" onblur="validatePassword(this.value,document.getElementById('password').value,'password_check');" size="50"/><div id="password_check"></div></td>
				</tr>
				<tr>
					<td class="lable">eMail:</td>
    			<td class="userinput"><input name="email" value="<?php echo @$_SESSION['order']['email'];?>" type="text" id="email" onchange="validateEmail(this.value,'email_valid');" size="50"/><div id="email_valid"></div></td>
				</tr>
				<tr>
    			<td class="lable">Clan/Organisation:</td>
    			<td class="userinput"><input value="<?php echo @$_SESSION['order']['org'];?>" name="clan" type="text" id="clan" size="50" /></td>
				</tr>
				<tr>
    			<td class="lable">Template:</td>
    			<td class="userinput">
				    <select name="template" id="template" size="1" onchange="javascript:load_template_preview(this.options[this.selectedIndex].value);"><option value="" selected="selected"></option>
				    <?php
						$def = 'clan';
						if(isset($_SESSION['order'])) $def = $_SESSION['order']['version'];
						
				    $query = mysql_query("SELECT template, name FROM `templates` WHERE short='".$def."'");
				    while ($row = mysql_fetch_array($query, MYSQL_BOTH)) {
				    	$sel = '';
				    	if(isset($_SESSION['order'])){
		            if($row["template"] == $_SESSION['order']['template']) $sel = ' selected="selected"';
								else $sel = '';
		           }
				       echo '<option value="'.$row["template"].'"'.$sel.'>'.$row["name"].'</option>';
				    }
				    ?>
				    </select></td>
				</tr>
				<tr>
    		<td></td>
		    <td><img alt="" id="template_preview" src="templates/<?php echo @$_SESSION['order']['template'];?>" /></td>
			</tr>
			<tr>
    		<td class="lable">Captcha:</td>
    		<td class="userinput">
        <script type="text/javascript">
            var RecaptchaOptions = {
                theme: 'clean',
            };
        </script>
        <?php 
        require_once ('system/recaptchalib.php');
        $publickey = "6LevSgUAAAAAAER-XS46zfKMPyCJbl0bfCCToFtB";
        echo recaptcha_get_html($publickey);
        ?>
    	</td>
		</tr>
		<tr>
			<td align="left"><a href="#" class="link" onclick="document.getElementById('register').submit()"><span><strong>Send</strong></span></a></td>
			<td><input type="hidden" name="check" value="true" /></td>
		</tr>
	</table>
</form>
</div>
<?php 
}
?>