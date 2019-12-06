<?php
require_once 'users/init.php';
/* for older userspice
require_once $abs_us_root.$us_url_root.'users/includes/header.php';
require_once $abs_us_root.$us_url_root.'users/includes/navigation.php';
*/
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';

if (!$user->isLoggedIn()) 
	Redirect::to('users\login.php');
?>

<DIV ID="page-wrapper">
<DIV CLASS="container">
		
<?php

// In this example I create main menu that links to other PHP scripts
$csrf = Token::generate();

// I'm going to use session variable as a security measure
$_SESSION["store_id"] = [];

// Open custom container
$html = "
<DIV CLASS=\"col-lg-4\">
	<DIV CLASS=\"page-header\">
		<H2>Stores:</H2>
	</DIV>
	<DIV CLASS=\"panel panel-default\">
		<DIV CLASS=\"panel-body\" STYLE=\"background:linear-gradient(to bottom, rgba(233,246,253,0.5) 0%,rgba(211,238,251,0.5) 100%); background-repeat:repeat-x;\">";

		

// Get all the stores
$stores = $db->query("SELECT id, Name FROM gf_stores")->results(true);
		
foreach ($stores as $store) 
{
	// One record - one form
	$form = new Generated_Form([], $csrf, "12_final.php", false);
	
	/* Construct function takes the following arguments: 
	 
		__construct($hidden=[], $token=NULL, $action=NULL, $add_container=true, $class="form-horizontal")
		
	$hidden        - list of hidden inputs to add to the "hidden" array
	$token         - UserSpice security token, if NULL then it generates a new one
	$action        - form-handler script, if NULL then it's set to $_SERVER[PHP_SELF]
	$add_container - if true then FORM tag will be wrapped in "div col" and "well"
	$class         - CSS class of the FORM tag
					 if passed "form-inline" then attributes "add_input_container" and "label_size" will be set to zero */
	
	
	// Pass store id and name to the form-handling script
	$form->hidden["store_id"]   = $store["id"];
	$form->hidden["store_name"] = $store["Name"];
	
	
	// Create a list of stores that user can edit
	$_SESSION["store_id"][] = $store["id"];
		

	// Add buttons that will determine which form to display in the form-handling script
	foreach (["Edit","HR","Review","Delete"] as $section)
		$form->add_button("form_display", $section, null, "btn-primary btn-xs");

	
	// Inlining buttons
	$first = true;
	
	foreach ($form->controls as &$control) {
		$control["Inline"] = -1;

		if ($first) {
			$control["Label"]      = $store["Name"];
			$control["LabelClass"] = "";
			$first                 = false;
		}
	}

	// Render form to a string
	$html .= $form->display();
}




// Add a button to create new record
$form = new Generated_Form([], $csrf, "12_final.php", false, "form-inline");
$form->add_button("form_display", "Add New", null, "btn-success");
$html .= $form->display();



// Display the entire thing
$html .= "
		</DIV><!-- end panel body -->
	</DIV><!-- end panel -->
</DIV><!-- end column -->\n";

echo $html;
?>









<BR />
<BR />
<?php include("__footer.php"); ?>