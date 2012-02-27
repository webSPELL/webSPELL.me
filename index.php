<?php
	include("system/core.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>webSPELL.me - Your All-in-one Clan Hosting Solutions</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="ajax.js"></script>    
<script type="text/javascript" src="scripts.js"></script>    
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="layout.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!--<script type="text/javascript" src="https://www.startssl.com/seal.js"></script> -->    
	<!--header -->
    <div id="header">
   	  <div class="main">
        	<div class="top">
            	<ul class="menu">
                	<li><a href="index.php">Home</a></li>
                  <li><a href="index.php?modul=register">Register</a></li>
                  <li><a href="#">Coming soon</a></li>
                  <li class="last"><a href="index.php?modul=contact">Contact</a></li>
                </ul>
                <div class="logo"><big>webSPELL</big><small>.me</small><br/>YOUR ALL-IN-ONE HOSTING SOLUTION </div>
            </div>
            <div class="container">
            	<div class="banners">
            		<div id="grey_banner">CLAN WEBSITE WITH CMS<br/><big>SUBDOMAIN FREE</big></div>
								<div id="orange_banner"><big>GET YOUR OWN WEBSITE FOR YOU AND YOUR CLAN</big><br/><br/>with the webSPELL.me Clan package. <a href="index.php?modul=register">Sign up</a> now and enjoy a fast and easy to setup Clan website.</div>
              </div>
                <div class="header-text">
                	<h3>Registration easy as 1-2-3</h3>
                    You wanto to use the webSPELL CMS for your Clan website, but don't know how to install it, or how to get a new design for it?
                    <ul>
                    	<li style="background-image:url(images/list_a.png);">SUBDOMAIN INCLUDED</li>
                      <li style="background-image:url(images/list_b.png);">No HTML / PHP SKILLS REQUIRED</li>
                      <li style="background-image:url(images/list_c.png);">NO INSTALLATION NEEDED</li>
                    </ul>
                    <div class="buttons" id="info_button"><a href="index.php?modul=register" class="even">SIGN UP NOW!</a><a class="odd" href="index.php?modul=register">Your own subdomain free</a><br style="clear:both"/></div>
                </div>
            </div>
        </div>
    </div>
    <!--header end-->
    <div id="content">
        <div class="main">
            	<?php
								include("./modul/".$_page.".php");
							?>    
        </div>
    </div>
    <!--footer -->
    <div id="footer">
        <div class="main">
        	<div class="text">webSPELL.me &copy; 2009 | <a href="index.php?modul=tos">TOS</a> | <a href="index.php?modul=imprint">Imprint</a></div>
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