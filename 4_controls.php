<?php
include("__header.php");

/* In this example let's explore all the controls */

if (Input::exists())
	dump($_POST);

$form        = new Generated_Form();
$form->title = "Control List";







$form->add_text("Text"     , "Name", "Help Text", "Placeholder", "Default Value", 0);
$form->add_text("Text Area", "Name", "Help Text", "Placeholder", "Default Value", 1);
$form->add_text("Password" , "Name", "Help Text", "Placeholder", "Default Value", 0, "type='password'");

/*
	add_text($tablecolumn, $label=null, $help="", $placeholder="", $default="", $rows=0, $property="")
	
	$tablecolumn - identifier used when copying the data
	$label       - text on the left side describing control; by default it's the same as $tablecolumn
	$help        - text below the control
	$placeholder - text when there's no value
	$default     - value displayed when there's no value in the "data" array for this control
	$rows        - number larger than zero creates a TextArea instead (with given size)
	$property    - custom HTML property, write "TYPE='email'" here to change the text input type (default is "text")
*/




$form->add_button("Name", "Value", "Button", "btn-info btn-lg", "ButtonID", "STYLE=\"margin-bottom:1em;\"");
/*
	add_button($name, $value, $text=null, $class="btn-primary", $id="", $property="")
	
	$name     - name HTML property
	$value    - value HTML property
	$text     - text on the button; by default it's the same as $value
	$class    - CSS class
	$id       - HTML ID property, by default it's the same as $name
	$property - custom HTML property, write "TYPE='button'" here to change the button type (default is "submit")
*/




$form->add_select("Number_select"  , "Select"         , "Help Text", [1,2,3], 2, 0         , "STYLE=\"background-color:#f6ffe6;\"");
$form->add_select("Number_multiple", "Select Multiple", "Help Text", [1,2,3], 2, 3         , "STYLE=\"background-color:#f6ffe6;\"");
$form->add_select("Number_datalist", "Datalist"       , "Help Text", [1,2,3], 2, "datalist", "STYLE=\"background-color:#f6ffe6;\"");
$form->add_select("Number_checkbox", "Checkboxes"     , "Help Text", [1,2,3], 2, "checkbox", "onClick=\"alert('You\'ve clicked on '+this.value);\"");
$form->add_select("Number_radio"   , "Radios"         , "Help Text", [1,2,3], 2, "radio");
/*
	add_select($tablecolumn, $label=null, $help="", $options=[], $default="", $size=0, $property="")
	
	$tablecolumn - identifier used when copying the data
	$label       - text on the left side describing control; by default it's the same as $tablecolumn
	$help        - text below the control
	$options     - list of values to select
	$default     - value selected when there's no value in the "data" array for this control
	$size        - number larger than zero creates a multiple choice select (with given size)
	$property    - custom HTML property

	To create a datalist pass "datalist" as $size
	To create checkboxes pass "checkbox" as $size
	To create radios     pass "radio"    as $size
	
==
	
	Brackets are automatically added to the names of select multiple and checkboxes.
	In this example:
		Number_multiple[]
		Number_checkbox[]

	So they will be arrays in $_POST.
	If nothing was selected then they will be empty arrays in the "data" array.
	Always check if the array is not empty before changing database.

==
	
	$options can be a little more complex.
	If it's one dimensional like [1,2,3] then option name and value will be identical
	
	Alternatively it may contain sub-arrays for separate option name and value
	[["one",1], ["two",2], ["three",3]]
	
	Additionally third item in the sub-array is an HTML property
	[["one",1,"disabled"], ["two",2], ["three",3,"selected"]]
	
	Keep in mind that "selected" may conflict with the default value
	
	Alternatively it can be an option group:
	[["Numbers",0,"optgroup"], ["one",1], ["two",2], ["three",3]]
*/




$form->add_datetime("date", "Date and Time");
/*
	add_datetime($tablecolumn, $label=null, $default="now", $display="jS F (l) Y H:i", $store="Y-m-d H:i:s")
	
	$tablecolumn   - identifier used when copying the data
	$label         - text on the left side describing control; by default it's the same as $tablecolumn
	$default       - default value in output format ($store); if passed "now" then it creates current date
	$display       - input date and time format, default is: 18th July (Tuesday) 2017 18:12
	$store         - output date and time format, default is: 2017-07-18 18:12:00
	
	This is a date and time picker for Bootstrap https://eonasdan.github.io/bootstrap-datetimepicker/
	Its settings are locked inside the class but feel free to change that
	
	This function adds a hidden input {TableColumn}_datetime which holds selected date in ISO8601 format
*/




$form->add_imagefile("Image", null, "JPG file up to \$max_image_size", "uploaded_images", 1024*1024*1.5);
/*
	add_imagefile($tablecolumn, $label, $help, $directory, $max_image_size)
	
	$tablecolumn    - identifier used when copying the data
	$label          - text on the left side describing control; by default it's the same as $tablecolumn
	$help           - text below the control
	$directory      - file destination
	$max_image_size - file size limit in bytes
	
	This is a file select for uploading an image.
	Its usage instructions you'll find in the 9_image.php
*/




$form->add_space(4);
/*
	add_space($amount=3)
	
	$amount - how many new lines
	
	This will add <BR />
*/




$form->add_heading("Title", "Heading", 3);
/*
	add_heading($text, $label="", $level=4)
	
	$text  - text displayed on the right
	$label - text displayed on the left
	$level - heading 1-6
	
	This will add HTML heading wrapped in a form-group
*/




$form->add_emptyspan("custom_field", "custom_field_group");
/*
	add_emptyspan($id, $group="")

	$id    - HTML ID property
	$group - HTML ID property for an entire control group

	This will add <SPAN> wrapped in a form-group. For use with JS
*/




$form->add_html("<SCRIPT TYPE=\"text/javascript\">document.getElementById('custom_field').innerHTML='Custom span'</SCRIPT>");
/*
	add_html($code)
	
	$code - HTML script

	This will add custom HTML code
*/



$list = ["name"=>"Example", "data"=>[1,2,3]];
$form->add_js_var($list);
/*
	add_js_var($array)
	
	$array - array with "name" and "data" keys

	This will convert PHP array to JavaScript
*/

echo $form->display();
include("__footer.php");
?>