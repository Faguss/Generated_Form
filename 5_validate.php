<?php
include("__header.php");

// In this example I'll use UserSpice Validate class to verify form inputs

$form = new Generated_Form(["form_action", "form_display", "store_id", "store_name"]);

if (empty($form->hidden["form_display"]))
	$form->hidden["form_display"] = "Add New";










	

	
	

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
	$form->add_text    ("Description", null, "What this shop sells?"         , "Computer hardware", "", "", 3                             );
	$form->add_text    ("Shelves"    , null, "How many shelves do they have?"                                                             );
	$form->add_select  ("Location"   , null, "Where is the store?"           , $locations                                                 );
	$form->add_datetime("Created"    , "Established");
	$form->add_select  ("Timezone"   , "Time zone",  "", DateTimeZone::listIdentifiers(DateTimeZone::ALL), "Europe/Warsaw", "datalist");
	
	


	if (in_array($form->hidden["form_action"],["Add New","Edit"])) {
		$data = &$form->save_input();
		
		// save_input() and display() automatically convert datetime format so I don't have to do it manually

		
		// This is a query I'll use in validation to check if the store name is unique
		$unique_name = ["gf_stores", ["and",["Name","LIKE",$data["Name"]]] ];
		
		if ($form->hidden["form_action"] == "Edit")
			$unique_name[1][] = ["id", "!=", $form->hidden["store_id"]];
		

			
		
		// First initialize validation
		$form->init_validation( ["max"=>100, "required"=>true] );
		
		/*	Class Generated_Form has an array "validation_rules" that stores rules for use with the Validate class
		
		Function init_validation() creates sub-arrays for all controls that have "TableColumn" defined.
		"Label" is used as a value for the "display" rule
		
		After using this function "validation_rules" now looks like this:
		[
			"Name" => [
				"display"  => "Name",
				"max"      => 100,
				"required" => true
			],
			
			"Description" => [
				"display"  => "Description",
				"max"      => 100,
				"required" => true
			],
			
			"Shelves" => [
				"display"  => "Shelves",
				"max"      => 100,
				"required" => true
			],
			
			"Location" => [
				"display"  => "Location",
				"max"      => 100,
				"required" => true
			]
		]
		
		
		This method has two optional arguments:
		
			init_validation($rules=[], $exclude=[])
			
		$rules   - list of rules that will be added to all controls
		$exclude - list of controls that will be skipped 
		
		
		
		Function add_validation_rules() will add more/change existing rules */
		
		$form->add_validation_rules( "Name"       , ["min"=>3  , "unique"     =>$unique_name                        ] );
		$form->add_validation_rules( "Description", ["max"=>255, "required"   =>false                               ] );
		$form->add_validation_rules( "Shelves"    , [">"  =>0  , "is_int"     =>true, "display"=>"Number of shelves"] );
		$form->add_validation_rules( "Location"   , [            "in"         =>array_splice($locations, 1)         ] );
		$form->add_validation_rules( "Created"    , [            "is_datetime"=>true                                ] );
		$form->add_validation_rules( "Timezone"   , [            "is_timezone"=>true                                ] );
		
		/*
			add_validation_rules($columns, $rules)
			
		First argument is the list of controls for which I want to add rules.
		It can be more than one. For example:
		
			add_validation_rules( ["Name","Description"], ["min"=>3] );
			
		Second argument is the list of rules that I want to add.
		If the control doesn't exist in the "validation_rules" array then nothing will be added
		
		
		
		
		
		
		
		
		Finally use validate() to check data.
		It returns a bool value	and shows error message if there are any discrepancies */
		
		if ($form->validate()) {
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
		}
	}
	
	
	
	$form->add_button("form_action", $form->hidden["form_display"]);
}	
	
	
	

	




$form->title = "{$form->hidden["form_display"]} Store <b>{$form->hidden["store_name"]}</b>";
echo $form->display();
include("__footer.php");
?>