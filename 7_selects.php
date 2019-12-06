<?php
include("__header.php");

/* In this example I:
- get records from two tables and sort it into two multiple choice selects
- insert/remove multiple records to/from one table
- have three submit buttons */

$form = new Generated_Form(["form_action", "form_display", "store_id", "store_name"]);

if (empty($form->hidden["form_display"])) {
	$form->hidden["store_id"]     = 1;
	$form->hidden["form_display"] = "HR";
	$form->hidden["store_name"]   = "Planettaxon";
}

$form->title = "HR Management for the <B>{$form->hidden["store_name"]}</B>";








	

	
	

if ($form->hidden["form_display"] == "HR") {
	define("MAX_WORKERS", 3);
		
	$lang = array_merge($lang,array(
		"GF_DISMISS_OK" => "Dismissed %m1% worker%m2%",
		"GF_DISSMIS_FAIL" => "Failed to dissolute contracts"
	));
	
	/* This is a bit backwards because I need to make changes to the db first
	before loading any data from it 
	
	This will remove records from the table when user clicked on the "Fire" button */
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
	
	/* Here I tell feedback() to call Usesprice built-in lang() function
	to localize a string with dynamic markers in the array */


	
	
	// Get list of all people and indicate if any of them work in this store
	$sql = "
		SELECT 
			gf_ppl.*, 
			gf_storesppl.id AS EmploymentID
			
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
	
	
	
	

	
	// This will add records to the table when user clicked on the "Hire" button
	if (in_array($form->hidden["form_action"],["Hire","HireFire"])) {
		$selected_people = Input::get("hire_people");

		if (is_array($selected_people)) 
		{
			// Check if selected people were already added and if so then remove them
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

			if ($count_selected > 0) 
			{
				// Resize array to match the limit
				if ($count_existing + $count_selected > MAX_WORKERS) {
					array_splice($selected_people, MAX_WORKERS-$count_existing);
					$form->alert("Couldn't hire everybody because store is full");
				}

				$count_selected = count($selected_people);


				// Build an insert query
				$new_workers = [
					"storeID"  => [],
					"personID" => []
				];

				foreach ($selected_people as $personID) {
					$new_workers["storeID"][]  = $form->hidden["store_id"];
					$new_workers["personID"][] = $personID;
				}
				

				// Send data to the table
				$result = $db->insert("gf_storesppl", $new_workers);
				
				$lang = array_merge($lang,array(
					"GF_HIRE_OK" => "Hired %m1% worker%m2%",
					"GF_HIRE_FAIL" => "Failed to hire people"
				));
				
				$form->feedback($result, "GF_HIRE_OK", "GF_HIRE_FAIL", "lang", [$db->count(), ($db->count()!=1 ? "s" : "")]);
				
				
				// Get all records again
				if ($result) {
					if ($db->query($sql,[$form->hidden["store_id"]])->error())
						$form->fail("Failed to load list of workers");
					
					$all_people = $db->results(true);
				}
			} else
				$form->alert("These people are already working for us!");
		} else
			$form->alert("You've picked no one to enlist");
	}
	
	
	
	
	
	
	
	
	/* Format data downloaded from the table
	
	Create two new arrays:
	- one for the "Hire" select
	- second for the "Fire" select */
	
	$hire_list = [];
	$fire_list = [];
	
	foreach ($all_people as $person) 
	{
		// To which list we should put the person
		if (isset($person["EmploymentID"]))
			$fire_list[] = [$person["Name"], $person["id"]];
		else
			$hire_list[] = [$person["Name"], $person["id"]];
	}
	
	
	
	/* If store reached max capacity or there's no one left to hire
	then display appropriate information instead of select */
	$employed = count($fire_list);
	
	if ($employed>=MAX_WORKERS  ||  empty($hire_list))
		$form->add_heading(($employed>=MAX_WORKERS ? "Store is full" : "Lack of candidates"), "Available People");
	else {
		$form->add_select("hire_people", "Available People", "Pick one or more", $hire_list, "", count($hire_list));
		$form->add_button("form_action", "Hire", null, "btn-success");
	}
	
	
	// If there are people available to fire then display them
	if (!empty($fire_list)) {
		$form->add_space();
		$form->add_select("fire_people", "Current employees", "Pick one or more", $fire_list, "", MAX_WORKERS);
		$form->add_button("form_action", "Fire", null, "btn-danger");
		
		
		/* If both select controls are displayed then show third submit button
		
		I'm checking if the first select exists by searching for it */
		if ($form->find_control("hire_people") >= 0)
			$form->add_button("form_action", "HireFire", "Fire & Hire", "btn-warning");
	}
}	
	
	
	

	





echo $form->display();
include("__footer.php");
?>