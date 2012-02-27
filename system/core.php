<?php 
include (dirname(__FILE__)."/settings.php");
include (dirname(__FILE__)."/functions.php");
$mysql_link = mysql_connect(MYSQL_HOST, MYSQL_ROOT_USER, MYSQL_ROOT_PASS);
define("MYSQL_LINK",$mysql_link);
mysql_select_db(MYSQL_ROOT_DATABASE,MYSQL_LINK);
function security_slashes(&$array) {
    foreach ($array as $key=>$value) {
        if (is_array($array[$key])) {
            security_slashes($array[$key]);
        } else {
            if (get_magic_quotes_gpc()) {
                $tmp = stripslashes($value);
            } else {
                $tmp = $value;
            }
            if (function_exists("mysql_real_escape_string")) {
                $array[$key] = mysql_real_escape_string($tmp);
            } else {
                $array[$key] = addslashes($tmp);
            }
            unset($tmp);
        }
    }
}

session_name("session");
session_start();

security_slashes($_POST);
security_slashes($_COOKIE);
security_slashes($_GET);

if (!function_exists('checkdnsrr')) {
    function checkdnsrr($host, $type = '') {
        if (! empty($host)) {
            if ($type == '')
                $type = "MX";
            @exec("nslookup -type=$type $host", $output);
            while (list($k, $line) = each($output)) {
                if (stristr($line, $host)) {
                    return true;
                }
            }
            return false;
        }
    }
}

function check_email($email) {
    if ((preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $email)) 
		|| (preg_match('/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/', $email))) {
        $host = explode('@', $email);
        
        if (checkdnsrr($host[1].'.', 'MX'))
            return true;
        if (checkdnsrr($host[1].'.', 'A'))
            return true;
        if (checkdnsrr($host[1].'.', 'CNAME'))
            return true;            
    }
    return false;
}

mysql_query("DELETE * FROM `locks` WHERE (NOW() - UNIX_TIMESTAMP(`timestamp`)) > ".LOCK_DURATION,MYSQL_LINK);

if (defined("AJAX") && AJAX == true) {
    $_page = false;
    $modules = array("validation","communication");
} else {
    $_page = "home";
    $modules = array("home", "register", "contact", "imprint", "tos","auth");
}
if (isset($_GET['modul']) && in_array($_GET['modul'], $modules)) {
    $_page = $_GET['modul'];
}
?>