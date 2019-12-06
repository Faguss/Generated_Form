<?php
include("__header.php");

if (Input::exists())
	dump($_POST);

/* In this example I'll describe other aspects of the class 

Class Generated_Form automatically creates UserSpice security token. 
If you want to have more than one form instance then create the token manually and then pass it to the construct function

Create token before any other forms */
$csrf = Token::generate();

// Pass it to the form
$form1 = new Generated_Form([], $csrf);


//------------------------------------------------------------------------------------------------------------------------


/* Default form ID is "myform".
With multiple forms you might want to change it */

$form1->id    = "myform1";
$form1->title = $form1->id;

$form1->add_text("Token");
$form1->add_html("<SCRIPT TYPE=\"text/javascript\">$(':input','#{$form1->id}').val('{$form1->hidden["csrf"]}');</SCRIPT>");
$form1->add_button("","Display");

echo $form1->display();


//------------------------------------------------------------------------------------------------------------------------


/* By default form is wrapped in two containers:
- div col
- well

You can change size of the first one by accessing "size" attribute 
Values range from 1 to 12 */

$form2        = new Generated_Form([], $csrf);
$form2->size  = 12;
$form2->title = "Size: {$form2->size}";

// You may change width of the label and input as well

$form2->label_size = 1;
$form2->input_size = 11;
$form2->add_text("Short Label",null,"","Long Input");
echo $form2->display();

// div col can also be offset

$form3 = new Generated_Form([], $csrf);
$form3->offset = 6;
$form3->title  = "Offset: {$form3->offset} &nbsp; &nbsp; Size: {$form3->size}";
echo $form3->display();


//------------------------------------------------------------------------------------------------------------------------


// Set "add_container" attribute to false to disable default form wrapping
$form4 = new Generated_Form([], $csrf);
$form4->add_container = false;
$form4->title         = "Custom wrap";
$form4->add_text("Text");

echo "
<DIV CLASS=\"col-lg-6\" 
STYLE=\"border:2px solid #6da221; border-radius:2em; margin:1em; background:linear-gradient(to bottom, rgba(220, 246, 218, 0.5) 0%, rgba(183, 227, 188, 0.5) 100%); background-repeat:repeat-x;\">" 
. $form4->display() . "</DIV>";


//------------------------------------------------------------------------------------------------------------------------


// Form class is "form-horizontal" by default but with a little hackery it can be changed to "form-inline"
$form5 = new Generated_Form([], $csrf);
$form5->form_class          = "form-inline";
$form5->add_input_container = false;
$form5->label_size          = 0;

/* Attributes:
form_class          - determines CSS class in the FORM tag
add_input_container - changing it to false will cancel DIV wrapping for controls 
label_size          - changing it to invalid number will cancel "col" wrapping for labels

Here's a recreation of the example from the Bootstrap page: */

$form5->add_text("Name",null,"","Jane Doe");
$form5->add_text("Email",null,"","jane.doe@example.com");
$form5->add_button("","Send invitation",null,"btn-default",null,"TYPE='button'");

$form5->title = $form5->form_class;
$form5->size  = 8;

echo $form5->display();


//------------------------------------------------------------------------------------------------------------------------


/* Default form action is: $_SERVER[PHP_SELF]
so it's submitting data to the same page

This can be changed by using construct function: */

$form6 = new Generated_Form([], $csrf, "1_minimal.php");

// or by modifying "action" attribute:

$form6->action     = "2_edit.php";
$form6->title      = "Submit to:";
$form6->size       = 2;
$form6->label_size = 0;

$form6->add_button("", $form6->action, null, "btn-link");
echo $form6->display();


//------------------------------------------------------------------------------------------------------------------------


/* Other properties that controls may have are:
Inline - will put controls in the same line */

$form7 = new Generated_Form([], $csrf);
$form7->title = "Other Options for Controls";

$form7->add_text("","Inline 1","","","Text");
$form7->add_select("","","",[1,2,3],2);

$form7->change_control([-2,-1], ["Inline"=>6]);
$form7->change_control(-1     , ["CloseInline"=>true]);

$form7->add_text("control1","Inline 2","","","txeT");
$form7->add_select("control2","","",[4,5,6],5);

$form7->change_control(["control1","control2"], ["Inline"=>6]);
$form7->change_control("control2"             , ["CloseInline"=>true]);

/* Inline is closed when:
- next control doesn't have Inline property
- current control has CloseInline property
- or it's the last control in the array

Control is wrapped in additional column and Inline number is its width 
Alternatively use negative value (for the first item) to disable this wrapping */

$form7->add_button("", "Yes", null, "btn-primary btn-success", null, "TYPE='button'");
$form7->add_button("", "Nah", null, "btn-primary btn-danger", null, "TYPE='button'");

$form7->change_control(["Value"=>["Yes","Nah"]], ["Inline"=>-1]);

$form7->add_space();


// Group - custom HTML property for the DIV containing label and control

$i = $form7->add_text("Group",null,"","Click on the link to hide/show entire group");
$form7->controls[$i]["Group"] = "ID=\"ShowHideText\"";
$form7->add_html("<A onClick=\"$('#ShowHideText').toggle();\">Toggle</A>");


/* ID - HTML ID property, by default "TableColumn" is used as an ID
but this can be overriden by adding "ID" property */

$form7->controls[$i]["ID"] = "NiceText";
$form7->add_html("<A STYLE=\"float:right\" onClick=\"$('#NiceText').val('The quick brown fox jumps over the lazy dog');\">Fill</A><BR /><BR />");


// Property - custom HTML property for the control

$form7->controls[
	$form7->add_text("Property",null,"","Readonly input here…") 
]["Property"] = "READONLY";


// LabelClass - replaces CSS class of the label tag

$form7->controls[$form7->add_text("","","","Label class now includes glyphicon")]["LabelClass"] = "control-label glyphicon glyphicon-hand-right";


/* By default add_button() function doesn't add:
Label - text on the left
Help  - help text below the control 

but they could be included manually: */

$form7->controls[] = [
	"Label"    => "Label", 
	"Help"     => "Help",
	"Type"     => "Button", 
	"Class"    => "btn-primary", 
	"Text"     => "Button with label and help",
	"Property" => "TYPE='button'"
];


// For static control change "Type" property to "Static"

$form7->add_text("static", "Static Control", "", "", "Plain Text");
$form7->change_control(-1, ["Type"=>"Static"]);


// To add a CSS class to control change its "class" property

$form7->add_text("Small Input", null, "", ".input-sm");
$form7->change_control(-1, ["Class"=>"input-sm"]);


// To add a CSS class to control group change its "groupclass" property

$form7->add_text("Large Label", null, "", "Large input");
$form7->change_control(-1, ["GroupClass"=>"form-group-lg"]);


// To add input group change its "Addons" property

$form7->add_text("Addons");
$form7->change_control(-1, ["Addons"=>[["$"],[".00"]]]);

echo $form7->display();


//------------------------------------------------------------------------------------------------------------------------


/* Class Generated_Form has an "external_files" array
which holds list of scripts to be included when the form is displayed 

Function add_datetime() will include a bunch of files in order to make the datetime picker work.
I also use it to include custom JS scripts.

Method include_file() will add files to the array (duplicates excluded).
Its argument is a file name or an array with file names. 
They have to be CSS or JS scripts */

$form8 = new Generated_Form([], $csrf);
$form8->include_file(["usersc/js/custom.js","usersc/css/custom.css"]);

$form8->add_html(implode("<BR />",$form8->external_files));
$form8->title = "Included files";
echo $form8->display();

// With multiple forms the same files could be included multiple times. No fix for this.

include("__footer.php");
?>