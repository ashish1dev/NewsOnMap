<?php 

/* ContactPop jQuery Plugin
 *
 * By Jon Raasch
 * http://jonraasch.com
 *
 * Copyright (c)2009 Jon Raasch. All rights reserved.
 * Released under FreeBSD License, see readme.txt
 * Do not remove the above copyright notice or text.
 *
 * For more information please visit: 
 * http://jonraasch.com/blog/contact-pop-jquery-plugin
*/


class ContactPop {
    /****** config *********/
    
    // the email to which you want to send the info from the contact form
    var $siteEmail = 'contact@jonraasch.com';
    
    // the title of the emails that the form sends
    var $emailTitle = 'Contact-Pop Form mail';
    
    var $thankYouMessage = "Thank you for contacting us, we'll get back to you shortly.";
    
    /******* end config ********/
    
    
    var $error = '';
    
    function getFormHtml($ajax = 0) {
        
        $postedName = $_POST['name'];
        $postedEmail = $_POST['email'];
        $postedMessage = $_POST['message'];
        
        $formHtml = '';
        
        // send congratulations message
        if ( isset($_POST['httpReferer']) && !$this->error ) {
            $out = '<p id="contact-pop-error" class="formItem">' . $this->thankYouMessage . '</p>';
            
            if ( $ajax ) $out .= '<a href="#" class="close-overlay">Close</a>';
            
            return $out;
        }
        
        if ( $this->error ) $formHtml .= '<p id="contact-pop-error" class="formItem">' . $this->error . '</p>';

        
        $httprefi = $_SERVER["HTTP_REFERER"];
        
        $cancelLink = $ajax ? '<a href="#" class="close-overlay">Cancel</a>' : '';
        
        $formHtml .= <<<EOT
        
        <input type="hidden" name="httpReferer" value="$httprefi" />
        
        <div class="formItem">
            <label>Name:</label>
            <input type="text" name="name" class="inputText" value="$postedName" size="35" />
        </div>
        
        <div class="formItem">
            <label>Email:</label>
            <input type="text" name="email" class="inputText" value="$postedEmail" size="35" />
        </div>
        
        <div class="formItem">
            <label>Message:</label>
            <textarea name="message" class="textarea" rows="7" cols="38">$postedMessage</textarea>
        </div>
        
        <div class="formItem">
            <input type="submit" value="Send Message" class="submit" /> $cancelLink
        </div>
        
EOT;
        
        return $formHtml;
    }
    
    function checkEmail($emailAddress) {
        if (preg_match('/[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+(?:\.[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+)*\@[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+(?:\.[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+)+/i', $emailAddress)){
            $emailArray = explode("@",$emailAddress);
            if (checkdnsrr($emailArray[1])){
                return TRUE;
            }
        }
        return false;
    }
    
    function processForm() {
        
        // check data
        if ( !$_POST['name'] ) $this->error .= 'Please enter your name<br />';
        if ( !$this->checkEmail( $_POST['email'] ) ) $this->error .= 'Please enter a valid email address<br />'; 
        if ( !$_POST['message'] ) $this->error .= 'Please enter a message<br />';
        
        if ( !$this->error ) $this->sendFormEmail();
    }
    
    function sendFormEmail() {
        $message = "Name: " . stripslashes($_POST['name']) . 
            "\nEmail: " . $_POST['email'] . 
            "\n\nMessage: " . stripslashes($_POST['message']);
        
        if ( $_POST['ajaxForm'] ) $message .= "\n\nFrom a Contact-Pop Form on page: " . $_SERVER["HTTP_REFERER"];
        else $message .= "\n\nReferrer: " . $_POST['httpReferer'];
        
        $message .= "\n\nBrowser Info: " . $_SERVER["HTTP_USER_AGENT"] .
            "\nIP: " . $_SERVER["REMOTE_ADDR"] .
            "\n\nDate: " . date("Y-m-d h:i:s");
        
        mail($this->siteEmail, $this->emailTitle, $message, 'From: ' . $_POST['name'] . ' <' . $_POST['email'] . '>');
    }
}

$contactPop = new ContactPop();

if (isset($_POST['httpReferer'])) $contactPop->processForm();

// echo the ajax version of the form
if ( isset($_REQUEST['ajaxForm']) && $_REQUEST['ajaxForm']) {
    echo $contactPop->getFormHtml(1);
}
// or echo the full page version of the form
else {

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>Contact Us</title>

</head>

<body>

<div id="contactForm">
    <h1> Contact Us </h1>
    
    <form method="post" action="<?=$_SERVER['REQUEST_URI']; ?>">
    
    <?=$contactPop->getFormHtml(); ?>
    
    </form>
</div>

</body>
</html>

<?php
}
?>