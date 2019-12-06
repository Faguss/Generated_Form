<?php
/* Combination of previous examples:
- 7_selects.php
- 8_modifycontrols.php
- 9_image.php

also:
- checking permission with $_SESSION
- added "delete" section at the bottom.

This page is accessed from the 11_menu.php 

Set constants at the beginning: */
define("MAX_WORKERS"     , 3);
define("UPLOAD_DIRECTORY", "uploaded_images");




require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/header.php';
require_once $abs_us_root.$us_url_root.'users/includes/navigation.php';


if (!securePage($_SERVER['PHP_SELF']))
	die();

if (!$user->isLoggedIn())
	Redirect::to('users\login.php');

if (!Input::exists())
	Redirect::to('11_menu.php');

$uid   = $user->data()->id;
$token = $_POST["csrf"];

if (!Token::check($token))
	die("<DIV ID=\"page-wrapper\"><DIV CLASS=\"container\">Page expired. Try again from home page</DIV></DIV>");
?>


<DIV ID="page-wrapper">
<DIV CLASS="container">


<?php

$form = new Generated_Form(["form_action", "form_display", "store_id", "store_name"]);

if ($form->hidden["form_action"] == "Main Menu")
	Redirect::to('11_menu.php');

$form->title = "{$form->hidden["form_display"]} Store <B>{$form->hidden["store_name"]}</B>";





/* In the main menu I saved a list stores that user can edit.
Check if client store id matches the id on the server */
if (!in_array($form->hidden["store_id"],$_SESSION["store_id"])  &&  $form->hidden["form_display"]!="Add New")
	$form->fail("You can't handle the truth!");

/* Function fail() will:
- set all hidden variables to NULL so no further action on this page will take place
- show error message
- affect functions validate(), display(), upload_image(), keep_image() */



	


	
// 9_image.php
if (in_array($form->hidden["form_display"],["Add New","Edit"])) {
	$form->size       = 8;
	$form->label_size = 3;
	$form->input_size = 9;
	
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
	$form->add_imagefile("Logo", "Logo", "JPG file up to \$max_image_size", UPLOAD_DIRECTORY, 102400);
	
	


	if (in_array($form->hidden["form_action"],["Add New","Edit"])) {
		$data        = &$form->save_input();
		$unique_name = ["gf_stores", ["and",["Name","LIKE",$data["Name"]]]];
		
		if ($form->hidden["form_action"] == "Edit")
			$unique_name[1][] = ["id","!=",$form->hidden["store_id"]];
		
		$form->init_validation( ["max"=>100, "required"=>true] );
		$form->add_validation_rules( ["Name"]       , ["min"=>3  , "unique"     =>$unique_name                          ] );
		$form->add_validation_rules( ["Description"], ["max"=>255, "required"   =>false                                 ] );
		$form->add_validation_rules( ["Shelves"]    , [">"  =>0  , "is_int"     =>true, "display"=>"Number of shelves"  ] );
		$form->add_validation_rules( ["Location"]   , [            "in"         =>array_splice($locations, 1)           ] );
		$form->add_validation_rules( ["Created"]    , [            "is_datetime"=>true                                  ] );
		$form->add_validation_rules( ["Timezone"]   , [            "is_timezone"=>true                                  ] );

		if ($form->validate()) {
			$form->upload_image();
			
			$data["id"] = $form->hidden["store_id"];
			$result     = $db->insert("gf_stores", $data, true);

			$form->feedback(
				$result,
				"Store "     . ($form->hidden["form_action"]=="Add New" ? "added"   : "updated"), 
				"Failed to " . ($form->hidden["form_action"]=="Add New" ? "add new" : "update" ) . " store"
			);
			
			$form->keep_image($result);

			if ($result) {
				if ($form->hidden["form_action"] == "Add New") {
					$form->hidden["store_id"]          = $db->lastId();
					$form->hidden["form_display"]     = "Edit";
					
					// If added new store then include it in the "authorized" list
					$_SESSION["store_id"][] = $form->hidden["store_id"];
				}
			
				$form->hidden["store_name"]  = $data["Name"];
				$form->title = "{$form->hidden["form_display"]} Store <B>{$form->hidden["store_name"]}</B>";
			}
		}
	} 
	else
		if ($form->hidden["form_display"] == "Edit")
			if (!$form->load_record("gf_stores", $form->hidden["store_id"]))
				$form->fail("Couldn't load selected store");
	
	
	
	$form->add_button("form_action", $form->hidden["form_display"]);
}



	

	
	

	
	
// 7_selects.php
if ($form->hidden["form_display"] == "HR") {
	$form->title = "HR Management for the <B>{$form->hidden["store_name"]}</B>";

	$lang = array_merge($lang,array(
		"GF_DISMISS_OK" => "Dismissed %m1% worker%m2%",
		"GF_DISSMIS_FAIL" => "Failed to dissolute contracts"
	));

	if (in_array($form->hidden["form_action"],["Fire","HireFire"])) {
		$selected_people = Input::get("fire_people");

		if (is_array($selected_people)) {
			$result = $db->delete("gf_storesppl", ["and",["storeID","=",$form->hidden["store_id"]],["personID","IN",$selected_people]]);
			$count  = $db->count();
			$ending = $count!=1 ? "s" : "";
			
			$form->feedback(
				$result, 
				"GF_DISMISS_OK",  
				"GF_DISSMIS_FAIL",
				"lang",
				[$count, $ending]
			);
		} else
			$form->alert("You've picked no one to dismiss");
	};	


	$sql = "
		SELECT 
			gf_ppl.*, 
			gf_storesppl.id as EmploymentID
			
		FROM 
			gf_ppl LEFT JOIN gf_storesppl 
				ON gf_ppl.id            = gf_storesppl.personID  AND
				   gf_storesppl.storeID = ?
				
		ORDER BY 
			gf_ppl.Name
	";
	
	if ($db->query($sql,[$form->hidden["store_id"]])->error())
		$form->fail("Failed to load list of workers");
	
	$all_people = $db->results(true);
	

	if (in_array($form->hidden["form_action"],["Hire","HireFire"])) {
		$selected_people = Input::get("hire_people");

		if (is_array($selected_people)) {
			$already_added = [];
			
			foreach ($all_people as $person)
				if (isset($person["EmploymentID"]))
					$already_added[] = $person["id"];
				
			for ($i=0; $i<count($selected_people); $i++)
				if (array_search($selected_people[$i], $already_added) !== FALSE) {
					array_splice($selected_people, $i, 1);
					$i--;
				}
			
			$count_existing = count($already_added);
			$count_selected = count($selected_people);
			
			if ($count_selected > 0) {
				if ($count_existing + $count_selected > MAX_WORKERS) {
					array_splice($selected_people, MAX_WORKERS-$count_existing);
					$form->alert("Couldn't hire everybody because store is full");
				};
				
				$count_selected = count($selected_people);
				$new_workers    = [
					"storeID"  => [],
					"personID" => []
				];
				
				foreach ($selected_people as $personID) {
					$new_workers["storeID"][]  = $form->hidden["store_id"];
					$new_workers["personID"][] = $personID;
				};
				
				$result = $db->insert("gf_storesppl", $new_workers);
				
				$lang = array_merge($lang,array(
					"GF_HIRE_OK" => "Hired %m1% worker%m2%",
					"GF_HIRE_FAIL" => "Failed to hire people"
				));
				
				$form->feedback($result, "GF_HIRE_OK", "GF_HIRE_FAIL", "lang", [$db->count(), ($db->count()!=1 ? "s" : "")]);

				if ($result) {
					if ($db->query($sql,[$form->hidden["store_id"]])->error())
						$form->fail("Failed to load list of workers");
					
					$all_people = $db->results(true);
				}
			}
			else
				$form->alert("These people are already working for us");
		}
		else
			$form->alert("You've picked no one to enlist");
	}
	
	
	
	$hire_list = [];
	$fire_list = [];
	
	foreach ($all_people as $person)
		if (isset($person["EmploymentID"]))
			$fire_list[] = [$person["Name"], $person["id"]];
		else
			$hire_list[] = [$person["Name"], $person["id"]];
	
	$employed = count($fire_list);
	
	if ($employed>=MAX_WORKERS  ||  empty($hire_list))
		$form->add_heading(($employed>=MAX_WORKERS ? "Store is full" : "Lack of candidates"), "Available People");
	else {
		$form->add_select("hire_people", "Available People", "Pick one or more", $hire_list, "", count($hire_list));
		$form->add_button("form_action", "Hire", null, "btn-success");
	}
	
	if (!empty($fire_list)) {
		$form->add_space();
		$form->add_select("fire_people", "Current employees", "Pick one or more", $fire_list, "", MAX_WORKERS);
		$form->add_button("form_action", "Fire", null, "btn-danger");
		
		if ($form->find_control("hire_people") >= 0)
			$form->add_button("form_action", "HireFire", "Fire & Hire", "btn-warning");
	}
}	
	





	
	
	
// 8_modifycontrols.php
if ($form->hidden["form_display"] == "Review") {
	$form->title = "Performance Review at the <B>{$form->hidden["store_name"]}</B>";
	$form->add_select("personID"   , "Employee list", "Select a person"                         , []);
	$form->add_text  ("Grade"      , "New Rank"     , "Worker's position in the company (1-100)", "1");
	$form->add_button("form_action", "Review"       , "Adjust"                                  , "btn-primary");
	
	
	$sql = "
		SELECT 
			gf_ppl.*,
			gf_storesppl.id as EmploymentID,
			gf_storesppl.Grade,
			gf_storesppl.LastReview

		FROM 
			gf_ppl, 
			gf_storesppl 

		WHERE 
			gf_ppl.id            = gf_storesppl.personID  AND
			gf_storesppl.storeID = ?

		ORDER BY 
			gf_storesppl.Grade DESC
	";
	
	if ($db->query($sql,[$form->hidden["store_id"]])->error())
		$form->fail("Failed to load list of workers");
	
	$workers = $db->results(true);		

	

	
	
	if ($form->hidden["form_action"] == "Review") {
		$data = &$form->save_input();

		if (is_numeric($data["Grade"])) {
			$data["Grade"] = floatval(sprintf("%01.1f", $data["Grade"]));
			$data["Grade"] = strval($data["Grade"]);
		}	
		
		$record_id     = 0;
		$current_rank  = 0;
		$difference    = 0;
		$name          = "";
		$custom_errors = [];
		
		foreach ($workers as $worker)
			if ($data["personID"] == $worker["id"]) {
				$name         = $worker["Name"];
				$record_id    = $worker["EmploymentID"];
				$current_rank = $worker["Grade"];
				$difference   = abs($current_rank - $data["Grade"]);
				break;
			}

		if ($record_id == 0)
			$custom_errors[] = [Input::get("selected_name") . " doesn't work at {$form->hidden["store_name"]}", "personID"];
		
		$form->init_validation(["required"=>true]);
		$form->add_validation_rules(["Grade"], [">="=>1, "<="=>100, "!="=>$current_rank]);
		
		if ($form->validate($custom_errors)) {	
			$worker_record = [
				"Grade"      => $data["Grade"],
				"LastReview" => date("Y-m-d H:i:s")
			];

			$result = $db->update("gf_storesppl", $record_id, $worker_record)  &&  $db->count()>0;
			$form->feedback(
				$result,
				"$name was " . ($data["Grade"]>$current_rank ? "promoted" : "demoted") . " by $difference point" . ($difference!=1 ? "s" : ""),
				"Failed to update rank"
			);
			
			if ($result) {
				if ($db->query($sql,[$form->hidden["store_id"]])->error())
					$form->fail("Failed to load list of workers");
				
				$workers = $db->results(true);
			}
		}
		
		if ($record_id == 0)
			$data = [];
		
		unset($data["Grade"]);
	}
	
	

	$workers_select = [];
	$due_time       = [];
	$grades         = [];
	$names          = [];
	$now            = strtotime("now");
	$selected_grade = 0;
	$selected_name  = "";
	
	foreach ($workers as $worker) {
		$last_review = strtotime($worker["LastReview"]);
		$difference  = $now - $last_review;	
		$option_name = $worker["Name"] . " - " . $worker["Grade"];

		if ($difference > 0)
			$option_name .= " - (" . ($last_review>0 ? ("due $difference second". ($difference!=1 ? "s" : "")) : "never rated") . ")";
		
		$workers_select[] = [$option_name, $worker["id"]];
		$due_time[]       = $difference;
		$grades[]         = intval($worker["Grade"]);
		$names[]          = $worker["Name"];
		
		if ($form->data["personID"] == $worker["id"]) {
			$selected_grade = $worker["Grade"];
			$selected_name  = $worker["Name"];
		}
	}
	
	array_multisort($due_time, SORT_DESC, $workers_select, $grades, $names);

	if (empty($selected_name))
		$selected_name  = $names[0];
	
	if (empty($selected_grade))
		$selected_grade = $grades[0];

	$form->hidden["selected_name"] = $selected_name;	
	$form->change_control("personID", ["Options"=>$workers_select, "Property"=>"onChange=\"var index=$('#personID')[0].selectedIndex;  $('#Grade').val(grades[index]+1<=100 ? grades[index]+1 : 100);  $('#selected_name').val(names[index])\""]);
	$form->change_control("Grade"   , ["Default"=>$selected_grade+1<=100 ? $selected_grade+1 : 100]);
	$form->add_js_var(["name"=>"grades", "data"=>$grades]);
	$form->add_js_var(["name"=>"names", "data"=>$names]);
	
	if (empty($workers)) {
		$form->controls = [];
		$form->add_heading("There aren't any workers here");
	}
}	









// New section for deleting a record from the table
if ($form->hidden["form_display"] == "Delete") 
{
	// Erase the store, logo file and its employment records
	if ($form->hidden["form_action"] == "Delete") 
	{
		// Get image name
		$image = $db->cell("gf_stores.Logo",$form->hidden["store_id"]);
		
		// Remove record
		$result = $form->feedback(
			$db->delete("gf_stores", ["id","=",$form->hidden["store_id"]]),
			"Store removed", 
			"Failed to remove the store"
		);

		// Remove associated records and image file
		if ($result) {
			$db->delete("gf_storesppl", ["storeID","=",$form->hidden["store_id"]]);
			
			if (isset($image))
				@unlink(UPLOAD_DIRECTORY . "/$image.jpg");
		}
	} 
	else 
	{
		// Show warning how many people you'll boot out onto the street
		$employees = $db->cell("gf_storesppl.COUNT(*)",["storeID","=",$form->hidden["store_id"]]);
		
		if (!empty($employees))
			$form->add_html("This store is employing <B>$employees worker" . ($employees==1 ? "" : "s") . "</B>. Are you sure?<BR /><BR /><BR />");
	
		$form->add_button("form_action", $form->hidden["form_display"], null, "btn-danger btn-sm");
	}
}
	
	
	

	




$form->add_space();
$form->add_button("form_action", "Main Menu", null, "btn-success btn-lg");
echo $form->display();
include("__footer.php");
?>