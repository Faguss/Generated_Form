<?php
include("__header.php");

/* In the previous example I didn't use save_input() and defined form controls at the end

This time I want to call save_input() 
but that requires me to declare form controls before I have any data to fill them with.

To deal with this problem I'm going to set up form at the beginning and then modify controls later */


$form = new Generated_Form(["form_action", "form_display", "store_id", "store_name"]);

if (empty($form->hidden["form_display"])) {
	$form->hidden["store_id"]     = 1;
	$form->hidden["form_display"] = "Review";
	$form->hidden["store_name"]   = "Planettaxon";
}

if ($form->hidden["form_action"] == "Go to HR")
	Redirect::to("7_selects.php");

$form->title = "Performance Review at the <B>{$form->hidden["store_name"]}</B>";








	

	
	

if ($form->hidden["form_display"] == "Review") {
	$form->add_button("form_action", "Go to HR", "Go back to the hiring page", "btn-info btn-sm");
	$form->add_space();

	$form->add_select("personID"   , "Employee list", "Select a person"                         , []);
	$form->add_text  ("Grade"      , "New Rank"     , "Worker's position in the company (1-100)", "1");
	$form->add_button("form_action", "Review"       , "Adjust"                                  , "btn-primary");
	
	/* "TableColumn" are now different from "Label"
	so $_POST is going to look like this:
	[
		"personID" => 1,
		"Grade"    => 1
	] */

	
	
	
	
	// Get list of all people working for this store
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

		// Format rank number - cut remainder and remove trailing zeros
		if (is_numeric($data["Grade"])) {
			$data["Grade"] = floatval(sprintf("%01.1f", $data["Grade"]));
			$data["Grade"] = strval($data["Grade"]);
		}	
		
		/* Check if selected worker is in the query results
		If so then save information about him */
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

		// If couldn't find worker
		if ($record_id == 0)
			$custom_errors[] = [Input::get("selected_name") . " doesn't work at {$form->hidden["store_name"]}", "personID"];
		
		

		$form->init_validation(["is_numeric"=>true, "required"=>true]);
		$form->add_validation_rules(["Grade"], [">="=>1, "<="=>100, "!="=>$current_rank]);
		
		// Optional argument for validate() method is an array to merge with the validation object _errors array
		if ($form->validate($custom_errors)) {	
			$worker_record = [
				"Grade"      => $data["Grade"],
				"LastReview" => date("Y-m-d H:i:s")
			];
			
			/* Success is when any of the rows were modified
			Because UPDATE returns no error if the record id didn't match */
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


		// If invalid worker then clear form inputs
		if ($record_id == 0)
			$data = [];

		// Clear text input in order to let the default value kick in
		unset($data["Grade"]);
	}







	/* Format data downloaded from the table

	I'm going to:
	- create a list of options for the select control
	- create an array of time differences which I'll use to sort the list of options
	- create separate arrays with names and grades for use with JavaScript
	- determine default value for controls */
	$workers_select = [];
	$due_time       = [];
	$grades         = [];
	$names          = [];
	$now            = strtotime("now");
	$selected_grade = 0;
	$selected_name  = "";

	foreach ($workers as $worker) 
	{
		// How long since this person was reviewed
		$last_review = strtotime($worker["LastReview"]);
		$difference  = $now - $last_review;	
		
		/* I want select option names to be:
		Name - Grade - (Due Time) */
		$option_name = $worker["Name"] . " - " . $worker["Grade"];

		// If the person was reviewed just now then the difference is zero - in that case don't display time
		if ($difference > 0) {
			$option_name .= " - (";
			$plural       = $difference!=1 ? "s" : "";

			// If the person was never reviewed then display "never" instead of time since 1970
			$option_name .= $last_review>0 ? "due $difference second$plural" : "never rated";
			
			$option_name .= ")";
		}
		
		$workers_select[]  = [$option_name, $worker["id"]];
		
		
		// Fill remaining arrays
		$due_time[] = $difference;
		$grades[]   = intval($worker["Grade"]);
		$names[]    = $worker["Name"];
		
		
		/* If this person is currently selected	then remember details about him
		so we can use them as default values for controls */
		if ($worker["id"] == $form->data["personID"]) {
			$selected_grade = $worker["Grade"];
			$selected_name  = $worker["Name"];
		}
	}
	
	
	// Sort arrays by how long it has been since the last performance review
	array_multisort($due_time, SORT_DESC, $workers_select, $grades, $names);

	
	/* If there's no user selection (aka user loaded this page for the first time)
	then pick first value from the list */
	if (empty($selected_name))
		$selected_name  = $names[0];
	
	if (empty($selected_grade))
		$selected_grade = $grades[0];

	
	/* $_POST contains only selected option value
	but I also want to pass option name.
	
	The reason for that is to have a fancy error message.
	If the selected person isn't in the company then display:
		Jim doesn't work here
	instead of
		This person doesn't work here
		
	That's why I'm adding hidden input that will save select option name */
	$form->hidden["selected_name"] = $selected_name;
	
	
	
	
	
	
	/* Now it's time to update form controls
	I have change_control() method for that
	
	First argument is a list of controls to change
	Second argument is a list of properties to add */
	
	$form->change_control("personID", ["Options"=>$workers_select, "Property"=>"onChange=\"var index=$('#personID')[0].selectedIndex;  $('#Grade').val(grades[index]+1<=100 ? grades[index]+1 : 100);  $('#selected_name').val(names[index])\""]);
	$form->change_control("Grade"   , ["Default"=>$selected_grade+1<=100 ? $selected_grade+1 : 100]);
	
	/* So:
	- find control that have "personID" as "TableColumn" and then change its "Options" and "Property" values.
	- find control that has  "Grade"    as "TableColumn" and then change its "Default" value
	
	Function change_control also accepts:
	- an array
		change_control(["personID", "Grade"], [])
		
	- different properties:
		change_control(["TableColumn"=>"personID", "Value"=>"Review"], [])
		
	- sub arrays:
		change_control(["TableColumn"=>["personID","Grade"], "Value"=>["Review","Go to HR"]], [])
		
	- numeric keys:
		change_control(0, [])
		
	- negative values for controls relative from the end:
		change_control([-1,2], [])
	
		

	
	
	
	I've added a bit of JavaScript to the workers select.
	When user selects a different person from the list:
	- change rank input
	- change hidden input containing select option name	
	
	I need to convert PHP arrays to JavaScript arrays
	in order to make the onChange event work: */
	
	$form->add_js_var(["name"=>"grades", "data"=>$grades]);
	$form->add_js_var(["name"=>"names", "data"=>$names]);
	
	


	
	
	if (empty($workers)) 
	{
		/* If there aren't any records then I don't want to have controls on this page	
		The simplest way would be to clean array:

			$form->controls = [];
			
		But there's a button here that I want to keep so instead I'm going to call method: */
		
			$form->remove_controls_until(["Value"=>"Go to HR"]);
		
		/* This is going to remove all controls 
		until it encounters control with property "Value" equal to "Go to HR"
		which is the button that I want to preserve.
		
		Optional argument for this function is a starting position:
			remove_controls_until($form->hidden["store_id"]entification, $offset=-1)
		
			
		
		Alternatively I could use change_control() for removing only specific controls: */
		
			#$form->change_control(["personID", "Grade", "Value"=>"Review"], "remove");
			
		/* This is going to find control:
		- that has "personID" as "TableColumn"  and remove it
		- that has "Grade"    as "TableColumn"  and remove it
		- that has "Review"   as "Value"        and remove it
	
		
		
		Lastly display info for the user why controls were removed */
		$form->add_heading("There aren't any workers here");
	}
}	
	
	
	

	





echo $form->display();
include("__footer.php");
?>