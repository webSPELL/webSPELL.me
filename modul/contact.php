<?php 
$report = '';
if (isset($_POST['contact_send'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subdomain = $_POST['subdomain'];
    $msg = $_POST['msg'];
    if (! empty($name) and ! empty($email) and ! empty($subdomain) and ! empty($msg)) {
        $subject = 'webSPELL.me - Contact';
        $message = '<html>
<head>
  <title>webSPELL.me - Contact</title>
</head>
<body>
  <table>
    <tr>
      <td>Subdomain:</td>
      <td><b>'.$subdomain.'</b>.webspell.me</td>
    </tr>
    <tr>
      <td>Name:</td>
      <td><b>'.$name.'</b></td>
    </tr>
    <tr>
      <td>eMail:</td>
      <td><b>'.$email.'</b></td>
    </tr>
    <tr>
      <td>Msg:</td>
      <td><b>'.$msg.'</b></td>
    </tr>
  </table>
 </body>
</html>';
        
        // To send HTML mail, the Content-type header must be set
        $headers = 'MIME-Version: 1.0'."\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
        $headers .= 'Return-Path: <contact@webspell.me>'."\r\n";
        $headers .= 'From: webSPELL.me <contact@webspell.me>'."\r\n";
        $headers .= 'To: webSPELL.me <contact@webspell.me>'."\r\n";
        mail($to, $subject, $message, $headers);
        $report = "<div class='ok'>eMail has been sent.</div>";
    } else {
        $report = "<div class='error'>Some or all required fields are  missing.</div>";
    }
}
?>
<div class="container">
    <div class="column-1">
        <div class="indent">
        	<?php echo $report; ?>
            <h2>Contact form</h2>
            <strong>Please use this form to contact us.</strong>
            <div class="padding1">
                You can use this form if you have any kind of querys about the website, like feedback, problems, errors. Give us your name, eMail and subdomain, and we will try to get back you as soon as possible.
            </div>
            <form action="" method="post" enctype="multipart/form-data" id="ContactForm">
                <div>
                    <strong>Enter your name:</strong>
                    <label>
                        <input type="text" name="name" value="" />
                    </label><strong>Enter your eMail:</strong>
                    <label>
                        <input type="text" name="email" value="" />
                    </label><strong>Enter your subdomain:</strong>
                    <label>
                        <input type="text" name="subdomain" value="" />
                    </label><strong>Enter your message:</strong>
                    <div>
                        <textarea cols="30" rows="10" name="msg">
                        </textarea>
                    </div>
                    <input type="hidden" name="contact_send" value="" />
                </div>
                <div class="container padding3">
                    <a href="#" class="link" onclick="document.getElementById('ContactForm').submit()"><span><strong>Send</strong></span></a>
                    <a href="#" class="link margin3" onclick="document.getElementById('ContactForm').reset()"><span><strong>Clear</strong></span></a>
                </div>
            </form>
        </div>
    </div>
    <div class="column-2">
        <div class="indent">
            <h2>Contact information</h2>
            <strong>You can also reach us direct by eMail.</strong>
            <br/>
            <p>
                Please send any querys you may have to <strong>beta {AT} webspell.me</strong>
                while we are doing the beta testing. This eMail will only be active during the beta testing, and will be removed once this is done.
            </p>
            <p>
                For general querys you can still use <strong>contact {AT} webspell.me</strong>
            </p>
        </div>
    </div>
</div>
