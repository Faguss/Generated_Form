<?php
include("__header.php");
$form = new Generated_Form();


/*	Class Generated_Form has an array "alerts" that stores messages.
They will be displayed above the form.

For adding messages there are two methods:
- alert() 
- alert_type()

For example: */

$form->alert("Houston, we've had a problem");

/* Now "alerts" array looks like this:
[
	0 => [
		"Class"   => "danger", 
		"Message" => "Houston, we've had a problem"
	]
]


For other alert types use: */

$form->alert_type("success", "It's alive! It's alive!");
$form->alert_type("info"   , "Keep your friends close, but your enemies closer");
$form->alert_type("warning", "The owls are not what they seem");



/* Other functions that may add messages are:
- fail()
- feedback()
- validate()
- upload_image() */


echo $form->display();
include("__footer.php");
?>