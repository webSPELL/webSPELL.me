<?php
    if(isset($_GET['typ'])) $type = $_GET['typ'];
		else $type = null;
	
		header('Content-Type: text/xml');
  	header('Pragma: no-cache');
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		
		switch($type){
			case 'loadTemplates':
				$get = mysql_query("SELECT template, name FROM `templates` WHERE short='".$_GET['short']."'");
				echo "<xml>";
				while($ds = mysql_fetch_assoc($get)){
					echo '<result><template>'.$ds['template'].'</template><name>'.$ds['name'].'</name></result>'."\n";
				}
				echo "</xml>";
				break;
		}
