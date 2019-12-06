<?php
include("__header.php");

// In this example I upload a picture

$form = new Generated_Form(["form_action", "form_display", "store_id", "store_name"]);

if (empty($form->hidden["form_display"])) {
	$form->hidden["store_id"]     = 1;
	$form->hidden["form_display"] = "Edit";
	$form->hidden["store_name"]   = "Planettaxon";
}








	

	
	

if (in_array($form->hidden["form_display"],["Add New","Edit"])) {
	$locations = [
		["Pick one","","SELECTED DISABLED"], 
		"Africa", 
		"Asia", 
		"Australia", 
		"Europe", 
		"North America", 
		"South America"
	];
	
	$form->add_text    ("Name");
	$form->add_text    ("Description", null, "What this shop sells?", "Computer hardware", "", "", 3);
	$form->add_text    ("Shelves"    , null, "How many shelves do they have?");
	$form->add_select  ("Location"   , null, "Where is the store?", $locations);
	$form->add_datetime("Created"    , "Established");
	$form->add_select  ("Timezone"   , "Time zone", "", DateTimeZone::listIdentifiers(DateTimeZone::ALL), "Europe/Warsaw", "datalist");

	$form->add_imagefile("Logo", "Logo", "JPG file up to \$max_image_size", "uploaded_images", 102400);
	
	/* Class Generated_Form allows for upload of one JPG image
	It will be resized so that its dimensions are a power of 2.
	This is specifically what I needed but feel free to change it in the source code.
	
		add_imagefile($tablecolumn, $label, $help, $directory, $max_image_size)
		
	$tablecolumn    - where to store information in the table
	$label          - determines "Label" and "TableColumn"
	$help           - text below the control, string "\$max_image_size" will be replaced with size number
	$directory      - where to store uploaded image, use . for current directory
	$max_image_size - max file size in bytes
	
	If image already exists then it will be displayed along with a checkbox to remove it
	
	Also hidden variables will be added:
	- preserve_{TableColumn} - for keeping the same image when record is modified
	- remove_{TableColumn}   - indicates if checkbox was checked */

	
	


	if (in_array($form->hidden["form_action"],["Add New","Edit"])) {
		$data = &$form->save_input();

		/* save_input() needs to be called here.
		Image upload control is automatically excluded from validation */

		
		$unique_name = ["gf_stores", ["and",["Name","LIKE",$data["Name"]]]];
		
		if ($form->hidden["form_action"] == "Edit")
			$unique_name[1][] = ["id","!=",$form->hidden["store_id"]];
		
		$form->init_validation( ["max"=>100, "required"=>true] );
		$form->add_validation_rules( ["Name"]       , ["min"=>3  , "unique"     =>$unique_name                        ] );
		$form->add_validation_rules( ["Description"], ["max"=>255, "required"   =>false                               ] );
		$form->add_validation_rules( ["Shelves"]    , [">"  =>0  , "is_int"     =>true, "display"=>"Number of shelves"] );
		$form->add_validation_rules( ["Location"]   , [            "in"         =>array_splice($locations, 1)         ] );
		$form->add_validation_rules( ["Created"]    , [            "is_datetime"=>true                                ] );
		$form->add_validation_rules( ["Timezone"]   , [            "is_timezone"=>true                                ] );

		if ($form->validate()) 
		{
			/* Before inserting data to the table I call upload_image() method.
			It will:
			- validate the file
			- copy it to the destination directory 
			- change "data" array to contain file name */
			$form->upload_image();
			
			
			$data["id"] = $form->hidden["store_id"];
			$result     = $db->insert("gf_stores", $data, true);

			$form->feedback(
				$result,
				"Store "     . ($form->hidden["form_action"]=="Add New" ? "added"   : "updated"), 
				"Failed to " . ($form->hidden["form_action"]=="Add New" ? "add new" : "update" ) . " store"
			);
			
			if ($result) {
				if ($form->hidden["form_action"] == "Add New") {
					$form->hidden["store_id"]     = $db->lastId();
					$form->hidden["form_display"] = "Edit";
				}

				$form->hidden["store_name"] = $data["Name"];
			}
			
			
			// Delete old or new file based on the query result
			$form->keep_image($result);
		}
	} 
	else
		/* When I load this page for the first time and I want to edit existing record
		then I don't have any data yet - fetch it from the database */
		if ($form->hidden["form_display"] == "Edit")
			if (!$form->load_record("gf_stores", $form->hidden["store_id"]))
				$form->fail("Couldn't load selected store");
	
	
	
	$form->add_button("form_action",$form->hidden["form_display"]);
}	
	
	
	

	



$form->title = "{$form->hidden["form_display"]} Store <B>{$form->hidden["store_name"]}</B>";

// display() will carry over hidden variables so that the image will be kept
echo $form->display();

/* To sum up you MUST call these four methods:
$form->save_input();
$form->upload_image();
$form->keep_image($result);
$form->display(); 

Function fail() will render upload_image() unusable
and make keep_image() delete newly uploaded file */

include("__footer.php");
?>