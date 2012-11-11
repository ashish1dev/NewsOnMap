Contact-Pop jQuery Plugin
Author  : Jon Raasch
Website : http://jonraasch.com/blog/
Email   : jr@jonraasch.com

Copyright (c)2009 Jon Raasch



##############################
       FreeBSD License     
##############################
##############################

Copyright (c)2009 Jon Raasch. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

	1.	Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
	2.	Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
	
THIS SOFTWARE IS PROVIDED BY JON RAASCH ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL JON RAASCH OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those of the authors and should not be interpreted as representing official policies, either expressed or implied, of Jon Raasch, who is the man.


    
##############################
      Basic Instructions   
##############################
##############################

Basic use of Contact-Pop couldn't be easier.  First upload the Contact-Pop directory to your web server, preferably at the root of your site.  Next, make sure to include the contact-pop.js and contact-pop.css files, as well as the jQuery library:

<script type="text/javascript" src="/Contact-Pop/js/jquery.js"></script>
<script type="text/javascript" src="/Contact-Pop/js/contact-pop.js"></script>

<link rel="stylesheet" type="text/css" href="/Contact-Pop/css/contact-pop.css" />

The paths above should work if you've uploaded Contact-Pop to your site's root, otherwise be sure to modify them as needed.

Next we will need to flag our contact links.  Open up contact-pop.js and look for the config section.  When modifying this section be careful to keep any trailing commas after the variable definitions, or the script will throw JS errors.

First change the replaceHref variable to the path to your contact page.  Please note that this pulls in the href attribute from your various <a> tags, so 'contact.php', '/contact.php' and '../contact.php' are all different even if they point to the same page.  

Next open up contact-pop.php and head to the config section.  Make sure to change the $siteEmail variable, since this is where the results of the form will get sent (when a message is posted, it will be emailed to you).

var $siteEmail = 'email@youremail.com';

And that's it, you're all set to go!


##############################
   Unobtrusive Contact-Pop
##############################
##############################

By replacing links to your static contact page, Contact-Pop ensures that even users without Javascript enabled can access the form.  But we can take this a step further--let's use the same backend script for the AJAX Contact-Pop form as well as the static contact page.  That way the same process runs with the popup and the normal links.

First replace your current contact page with contact-pop.php by renaming and moving contact-pop.php accordingly.  (If you don't have a contact page, name it whatever you want).

Next open up contact-pop.js and find this part of the config section:

formPhpLocation : '/Contact-Pop/contact-pop.php', // relative path to the backend contact form

Modify this according to the location of your contact page, which should be a relative path from the browser's perspective.  Just to double-check, the formPhpLocation should match the replaceHref in most cases (remember that our goal is to use the same page for the AJAX and static form).

Finally, you should use your site's frame on the static contact page.  Open up the newly renamed contact-pop.php file, and look to the bottom for a large block of HTML.  Modifying this should be pretty straightforward.

Now regardless of how users reach your contact form, the same backend script will be used to both serve and process the form.


##############################
   Customizing Contact-Pop
##############################
##############################

Although its basic setup is simple, Contact-Pop is also extremely customizable.   Mainly, the easy-to-modify config section of contact-pop.js controls many of the plugins options.  

First you can adjust the fade speed using overlayFadeIn and overlayFadeOut.  If these fade speed controls aren't enough, you can even incorporate the jQuery easing plugin (http://gsgd.co.uk/sandbox/jquery/easing/) by simply passing whichever easing string you want to use in overlayEasing (and including the plugin Javascript).

Additionally, the text and color of the popup header can be modified using the contactHeadline and headerBgColor variables.

Contact-Pop's config section also provides a number of options for the selectors it uses.  First, if you want to use the popup for more than one href, you can pass an array of hrefs in replaceHref, for instance:

replaceHref : array('/contact.php', '/Contact'),

Alternately, you can flag links for the Contact-Pop overlay using a jQuery selector; simply pass whichever selector string you want to use into openButtonSelector.  If you want to only use the jQuery selector and not replace any links based on their href value, just set replaceHref to null or an empty string.

Additionally you can flag a custom close button using closeButtonSelector.  By default Contact-Pop appends its own close button so in most cases you can leave this alone.

Finally the Javascript file allows you to control a few other options.  For instance, by default Contact-Pop preserves any changes made to the contact form when it is closed and reopened.  To cause it to reset the form each time, set resetFormEachTime to 1.  

Additionally, Contact-Pop doesn't fade the overlay in IE7 because of a bug with IE7's implementation of png-24s.  This bug causes the overlay's translucent gray png-24 to flash black before fading out.  However, if you want to use a totally opaque image for your overlay image, set fadeOverlayIE to 1.

Besides the contact-pop.js config section, you can modify any other styling options through contact-pop.css, which should be fairly straightforward if you know CSS.

Finally, there are a few options provided in the contact-pop.php file.  You can change the title of the emails that the form sends, as well as the text of the server response when the form is processed.