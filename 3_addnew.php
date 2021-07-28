<?php
include("__header.php");



/* In this example I'm going to display a form that adds a new record
and after the submission changes into a record edit form */

$form = new Generated_Form(["form_action", "form_display", "store_id", "store_name"]);

/* New hidden variables:
- form_display - determines whether to show "add new" or "edit"
- store_id     - record id number 
- store_name   - record name displayed in the title

Ordinarily "form_display" should be set from the main menu
but this is a self-contained example so I'll make a default value */

if (empty($form->hidden["form_display"]))
	$form->hidden["form_display"] = "Add New";















	

if (in_array($form->hidden["form_display"],["Add New","Edit"])) {
	$form->add_text("Name");
	$form->add_text("Description", null, "What this shop sells?", "Computer hardware", "", "", 3);

	
	
	if (in_array($form->hidden["form_action"],["Add New","Edit"])) {
		$data = &$form->save_input();
		
		// Adding a new field to the data for the db query
		$data["id"] = $form->hidden["store_id"];
		$result     = $db->insert("gf_stores", $data, true);

		$form->feedback(
			$result,
			"Store "     . ($form->hidden["form_action"]=="Add New" ? "added"   : "updated"), 
			"Failed to " . ($form->hidden["form_action"]=="Add New" ? "add new" : "update" ) . " store"
		);
		
		if ($result) {
			// Update hidden variables in order to turn this page into "Edit"
			if ($form->hidden["form_action"] == "Add New") {
				$form->hidden["store_id"]     = $db->lastId();
				$form->hidden["form_display"] = "Edit";
			}
			
			// I want to update store name in the title only when the query was successful
			$form->hidden["store_name"] = $data["Name"];
		}		
	}
	
	
	
	
	// I'm adding button at the end because its value can change
	$form->add_button("form_action", $form->hidden["form_display"]);
}






// Title can change as well
$form->title = "{$form->hidden["form_display"]} Store <b>{$form->hidden["store_name"]}</b>";

echo $form->display();



include("__footer.php");
?>