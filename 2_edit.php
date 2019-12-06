<?php
include("__header.php");



// Create form instance
// Optional array argument is a list of variables passed by the hidden text inputs
$form = new Generated_Form(["form_action"]);

/* Class Generated_Form has an array "hidden" that stores hidden text inputs.
Right now it looks like this:
[
  "form_action" => ""
]

Variable "form_action" is set when user clicks on the submit button.
Otherwise it's empty
*/


// Set form title
$form->title = "Edit Store";











// Now to create form controls
$form->add_text("Name");

// Added text input with "Name" as table column in the database



$form->add_text("Description", null, "What this shop sells?", "Computer hardware", "", "", 3);

/* Added text input with:
"Description"   as name of the column in the table in the database
null            as label (if null it will be the same as table column)
"What this ..." as help text displayed below the control
"Computer..."   as suggested input that shows only when the field is empty
""              as default input
3               as number of lines for a TextArea (0 creates normal text input) */



$form->add_button("form_action", "Edit");

/* Added button with:
"form_action" as name
"Edit"        as value



Class Generated_Form has an array "controls" that stores information about the controls.
Functions I've just used filled this array. Now it looks like this:
[
	0 => [
		"Type"        => "Text", 
		"Label"       => "Name", 
		"Help"        => "",
		"Placeholder" => "",
		"TableColumn" => "Name",
		"Default"     => "",
		"Rows"        => 0
	],
	
	1 => [
		"Type"        => "TextArea", 
		"Label"       => "Description", 
		"Help"        => "Write here what the shop is about",
		"Placeholder" => "Computer hardware",
		"TableColumn" => "Description",
		"Default"     => "",
		"Rows"        => 3
	],
	
	2 => [
		"Type"     => "Button", 
		"Name"     => "form_action", 
		"Value"    => "Edit", 
		"Class"    => "btn-primary", 
		"Text"     => "Edit", 
		"Property" => ""
  ]
] */


	
	
	

	
	
/* Resolving submitted data

When user submits form, variable "form_action" is set to "Edit".
Class Generated_Form is using the same PHP page to handle submission (form action is set to "self") */
if ($form->hidden["form_action"] == "Edit") 
{
	// This function copies $_POST vars to an internal array
	$data = &$form->save_input();
	
	/* Class Generated_Form has an array called "data" that stores controls inputs.
	Property "TableColumn" is used as an identifier.
	After using save_input() "data" array now looks like this:
	
	[
	  "Name"        => "Planettaxon",
	  "Description" => "Retail telescopes &amp; binoculars"
	] 
	
	
	For convenience save_input() returns reference to the array	so that I can easily access the data
	
	
	
	I make a query and its result determines which message to display */
	$form->feedback(
		$db->update("gf_stores", 1, $data),
		"Store updated",
		"Failed to update the store"
	);
}
else
	/* If there's no submission that means it's the first load and we don't have any data yet.
	So get them from the database	*/
	if (!$form->load_record("gf_stores", 1))
		$form->fail("Couldn't load store");
	
	/* Similar to the save_input() this will fill "data" array but not from the $_POST
	instead take a record from the table "gf_stores" with id equal to 1
	
	Function fail() will block the form and display error message */









/* Render controls
Values for them will be taken from the "data" array */
echo $form->display();



include("__footer.php");
?>