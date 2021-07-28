<?php
class Generated_Form {	// by Faguss (faguss@o2.pl) 28.07.21
	public

	// Attributes:
	// Arrays
	$controls            = [],                // form controls
	$data                = [],                // form controls values
	$hidden              = [],                // hidden inputs
	$alerts              = [],                // messages for the user
	$validation_rules    = [],                // validation rules
	$external_files      = [],                // external scripts to include

	// HTML/CSS
	$id                  = "myform",          // ID in the FORM tag
	$form_class          = "form-horizontal", // CSS class in the FORM tag
	$action              = "",                // form-handler script
	$title               = "",                // form title displayed on the top
	$size                = 6,                 // total form size
	$label_size          = 4,                 // form labels size
	$input_size          = 8,                 // form controls size
	$offset              = -1,                // form offset size
	$add_container       = true,              // nest FORM tag inside DIV and well
	$add_input_container = true,              // nest input inside DIV

	// Miscellaneous
	$validation          = null,              // UserSpice validation object
	$failed_state        = false,             // prevents rendering

	// File Uploading
	$image_column        = "",                // name of the table column that stores image name
	$new_image_name      = "",                // uploaded image file name
	$old_image_name      = "",                // previous image file name
	$image_dir           = "./",              // storage location for images
	$max_image_size      = 0;                 // max image upload size




	// Methods:
	// Magic
	function __construct($hidden=[], $token=NULL, $action=NULL, $add_container=true, $class="form-horizontal") {
		foreach ($hidden as $variable)
			$this->hidden[$variable] = Input::get($variable);

		$this->hidden["csrf"] = isset($token)  ? $token  : Token::generate();
		$this->action         = isset($action) ? $action : $_SERVER['PHP_SELF'];
		$this->add_container  = $add_container;
		$this->form_class     = $class;

		if ($class == "form-inline") {
			$this->add_input_container = false;
			$this->label_size          = 0;
		}
	}



	// Controls
	function add_text($tablecolumn, $label=null, $help="", $placeholder="", $default="", $rows=0, $property="") {
		if (!isset($label))
			$label = $tablecolumn;

		$this->controls[] = [
			"Type"        => ($rows==0 ? "Text" : "TextArea"), 
			"Label"       => $label, 
			"Help"        => $help, 
			"Placeholder" => $placeholder, 
			"TableColumn" => $tablecolumn, 
			"Default"     => $default, 
			"Rows"        => $rows, 
			"Property"    => $property
		];
		
		return max(array_keys($this->controls));
	}

	function add_select($tablecolumn, $label=null, $help="", $options=[], $default="", $size=0, $property="") {
		if (!isset($label))
			$label = $tablecolumn;
		
		$subtype = "default";
		
		if (is_string($size)) {
			$size = strtolower($size);
			
			if (in_array($size,["default","datalist","checkbox","radio"])) {
				$subtype = $size;
				$size    = 0;
			}
		}

		$this->controls[] = [
			"Type"           => "Select", 
			"SubType"        => $subtype, 
			"Label"          => $label, 
			"Help"           => $help, 
			"TableColumn"    => $tablecolumn, 
			"Options"        => $options, 
			"Size"           => $size, 
			"Default"        => $default, 
			"Property"       => $property, 
			"ID"             => $tablecolumn,
			"CheckboxInline" => false
		];
		
		return max(array_keys($this->controls));
	}

	function add_imagefile($tablecolumn, $label, $help, $directory, $max_image_size) {
		if (!isset($label))
			$label = $tablecolumn;

		$this->image_dir      = "./$directory/";
		$this->max_image_size = $max_image_size;
		$help                 = str_replace("\$max_image_size", $this->format_file_size($this->max_image_size), $help);

		$id                   = count($this->controls);
		$this->controls[]     = ["Type"=>"ImageFile", "Label"=>$label, "Help"=>$help, "TableColumn"=>$tablecolumn];
		$this->image_column   = &$this->controls[$id]["TableColumn"];

		$this->hidden["preserve_{$this->image_column}"] = "";
		return max(array_keys($this->controls));
	}

	function add_button($name, $value, $text=null, $class="btn-primary", $id=null, $property="") {
		if (!isset($text))
			$text = $value;

		if (!isset($id))
			$id = $name;

		$this->controls[] = [
			"Type"     => "Button", 
			"Name"     => $name, 
			"Value"    => $value, 
			"Class"    => $class, 
			"Text"     => $text, 
			"ID"       => $id, 
			"Property" => $property
		];
		
		return max(array_keys($this->controls));
	}

	function add_datetime($tablecolumn, $label=null, $default="now", $display="jS F (l) Y H:i", $store="Y-m-d H:i:s") {
		if (!isset($label))
			$label = $tablecolumn;

		$locale_file = "en-gb";
		
		if (function_exists("lang"))
			switch(lang("THIS_CODE")) {
				case "ru-RU" : $locale_file="ru"; break;
				case "pl-PL" : $locale_file="pl"; break;
			}

		$this->include_file([
			"usersc/js/moment.js",
			"usersc/js/{$locale_file}.js",
			"usersc/js/transition.js",
			"usersc/js/collapse.js",
			"usersc/js/bootstrap-combobox.js",
			"usersc/js/bootstrap-datetimepicker.min.js",
			"usersc/css/bootstrap-datetimepicker.min.css"
		]);
		
		if ($default == "now")
			$default = date($store);

		$this->controls[] = [
			"Type"          => "DateTime", 
			"Label"         => $label, 
			"TableColumn"   => $tablecolumn, 
			"Default"       => $default, 
			"DisplayFormat" => $display, 
			"StoreFormat"   => $store,
			"Language"      => $locale_file
		];
		
		$this->hidden["{$tablecolumn}_datetime"] = "";
		
		return max(array_keys($this->controls));
	}

	function add_html($code) {
		$this->controls[] = ["Type"=>"Custom", "Code"=>$code];
		return max(array_keys($this->controls));
	}
	
	function add_js_var($array) {
		return $this->add_html("<script type=\"text/javascript\">var {$array["name"]} = ".json_encode($array["data"])."</script>");
	}

	function add_emptyspan($id, $group="") {
		$this->controls[] = ["Type"=>"EmptySpan", "ID"=>$id, "Group"=>$group];
		return max(array_keys($this->controls));
	}

	function add_space($amount=3) {
		$this->add_html(str_repeat("<br>",$amount));
		return max(array_keys($this->controls));
	}

	function add_heading($text, $label="", $level=4) {
		$this->controls[] = ["Type"=>"Header", "Label"=>$label, "Text"=>$text, "Level"=>$level];
		return max(array_keys($this->controls));
	}

	function find_control($value, $property="TableColumn") {
		foreach (array_keys($this->controls) as $key)
			if (isset($this->controls[$key][$property])  &&  $this->controls[$key][$property] == $value)
				return $key;

		return -1;
	}

	function change_control($list, $items_to_add) {
		$keys  = array_keys($this->controls);
		$count = count($keys);

		if (!is_array($list))
			$list = [$list];

		foreach ($list as $property=>$values) {
			$property = is_string($property) ? $property : "TableColumn";

			if (!is_array($values))
				$values = [$values];

			foreach ($values as $value) {
				$key = -1;

				if (is_int($value)) {
					if ($value < 0) {
						$value += $count;
						
						if ($value>=0  &&  $value<$count)
							$key = $keys[$value];
					} else
						if (in_array($value, $keys))
							$key = $value;
				} else
					$key = $this->find_control($value, $property);

				if ($key != -1)
					if (is_array($items_to_add))
						foreach ($items_to_add as $new_property=>$new_value)
							$this->controls[$key][$new_property] = $new_value;
					else
						if ($items_to_add == "remove")
							unset($this->controls[$key]);
			}
		}
	}

	function remove_controls_until($identification, $offset=-1) {
		list ($property, $value) = each($identification);
		$keys                    = array_reverse( array_keys($this->controls),true );

		for ($i=$offset+count($keys);  $i>=0;  $i--)
			if (!isset($this->controls[$keys[$i]][$property])  ||  $this->controls[$keys[$i]][$property] != $value)
				unset($this->controls[$keys[$i]]);
			else
				break;
	}



	// Data Import
	function load_record($sql, $arguments=[]) {
		$db  = DB::getInstance();
		$sql = ltrim($sql);

		if (is_array($arguments)) {
			if (strcasecmp(substr($sql,0,6),"select") == 0)
				$db->query($sql, $arguments);
			else
				$db->get($sql, $arguments);
		} else
			$db->findById($arguments, $sql);

		$this->data = $db->first(true);

		return !empty($this->data);
	}

	function &save_input() {
		foreach ($this->controls as $control)
			if (isset($control["TableColumn"])  &&  $control["TableColumn"] != "") {
				$input       = Input::get($control["TableColumn"]);
				$input_array = $control["Type"]=="Select"  &&  ($control["SubType"]=="checkbox" ||  $control["Size"]>0);

				// if variable was meant to be an array then turn it into empty array
				if (!is_array($input)  &&  $input_array)
					$input = [];

				// convert date format
				if ($control["Type"] == "DateTime") {
					$iso8601 = Input::get($control["TableColumn"]."_datetime");
					$dot     = strpos($iso8601,".");
					
					if ($dot !== FALSE)
						$iso8601 = substr($iso8601, 0, $dot) . substr($iso8601, $dot+4);
					
					$object = DateTime::createFromFormat(DateTime::ATOM, $iso8601);
					
					if ($object  &&  DateTime::getLastErrors()["warning_count"]==0  &&  DateTime::getLastErrors()["error_count"]==0)
						$input = $object->format($control["StoreFormat"]);
				}

				// get image name from hidden input
				if ($control["TableColumn"]==$this->image_column  &&  $input=="")
					if (Input::get("remove_{$this->image_column}") != "1")
						$input = Input::get("preserve_{$this->image_column}");
				
				$this->data[$control["TableColumn"]] = $input;
			}

		return $this->data;
	}



	// File Uploading
	function upload_image() {
		$id = $this->image_column;

		if ($this->new_image_name!=""  ||  $this->failed_state)
			return false;

		if (!isset($_FILES[$id]["error"])  ||  is_array($_FILES[$id]["error"])) {
			$this->alert("Invalid parameters");
			return false;
		}

		if ($_FILES[$id]["size"] >= $this->max_image_size) {
			$this->alert("Exceeded filesize limit");
			return false;
		}

		$ok = false;

		switch ($_FILES[$id]["error"]) {
			case UPLOAD_ERR_OK         : $ok=true;                                       break;
			case UPLOAD_ERR_FORM_SIZE  : 
			case UPLOAD_ERR_INI_SIZE   : $error_msg="File too large";                    break;
			case UPLOAD_ERR_PARTIAL    : $error_msg="File uploaded partially";           break;
			case UPLOAD_ERR_NO_FILE    :                                                 break;
			case UPLOAD_ERR_NO_TMP_DIR : $error_msg="Missing a temporary folder";        break;
			case UPLOAD_ERR_CANT_WRITE : $error_msg="Failed to write file to disk";      break;
			case UPLOAD_ERR_EXTENSION  : $error_msg="PHP Extension stopped the upload";  break;
			default                    : $error_msg="Unknown error";                     break;
		}

		if (!$ok) { 
			if (isset($error_msg)  &&  $error_msg != "")
				$this->alert($error_msg);

			return false;
		}

		if (!is_uploaded_file($_FILES[$id]["tmp_name"])) {
			$this->alert("Illegal file {$_FILES[$id]["name"]}");
			return false;
		}

		if (filesize($_FILES[$id]["tmp_name"]) >= $this->max_image_size) {
			$this->alert("Exceeded filesize limit");
			return false;
		}

		$finfo = new finfo(FILEINFO_MIME_TYPE);

		if (!$finfo) {
			$this->alert("Not able to process file");
			return false;
		}

		$jpg = array_search($finfo->file($_FILES[$id]["tmp_name"]), ["jpg"=>"image/jpeg"], true);
		$paa = array_search($finfo->file($_FILES[$id]["tmp_name"]), ["paa"=>"application/octet-stream"], true);
		$ext = strtolower(pathinfo($_FILES[$id]["name"], PATHINFO_EXTENSION));
		unset($finfo);

		if ($jpg !== false) {
			$img = @imagecreatefromjpeg($_FILES[$id]["tmp_name"]);

			if (!$img) {
				$this->alert("Failed to format image");
				return false;
			}

			$size = getimagesize($_FILES[$id]["tmp_name"]);

			function IsPowerOfTwo($x) {
				return ($x & ($x - 1)) == 0;
			}

			// Scale image if necessary
			if (!IsPowerOfTwo($size[0]) || !IsPowerOfTwo($size[1]))
			{
				$new_size = $size[0]<$size[1] ? $size[0] : $size[1];

				//http://www.techiedelight.com/round-previous-power-2/
				while ($new_size & $new_size - 1)
					$new_size = $new_size & $new_size - 1;

				if ($new_size < 2)
					$new_size = 2;

				$img_scale = @imagescale($img, $new_size, $new_size);

				imagedestroy($img);

				if (!$img_scale) {
					$this->alert("Failed to process image");
					return false;
				}

				$img = &$img_scale;
			};

			// Assign random name
			do {
				$this->new_image_name = substr(Hash::unique(), rand(0,58), 6);
			} while (file_exists($this->new_image_name));

			$this->old_image_name = $this->data[$id];
			$this->data[$id]      = $this->new_image_name.".jpg";

			imagejpeg($img, "{$this->image_dir}{$this->new_image_name}.jpg", 90);
			imagedestroy($img);
		} else
		if ($ext=="paa"  &&  $paa!==false) {
			$handle = fopen($_FILES[$id]["tmp_name"], "rb");
			$data   = fread ($handle, 6);
			$header = unpack ("v1type/A4signature", $data);
			fclose($handle);
			
			if ($header["signature"]!="GGAT"  ||  ($header["type"]!=0xFF01  &&  $header["type"]!=0x8080  &&  $header["type"]!=0x4444)) {
				$this->alert("Invalid texture format");
				return false;
			};
			
			do {
				$this->new_image_name = substr(Hash::unique(), rand(0,58), 6);
			} while (file_exists($this->new_image_name));

			$this->old_image_name = $this->data[$id];
			$this->data[$id]      = $this->new_image_name.".paa";
			move_uploaded_file($_FILES[$id]["tmp_name"], "{$this->image_dir}{$this->new_image_name}.paa");
		} else {
			$this->alert("Invalid image type");
			return false;
		}

		return true;
	}

	function keep_image($yes) {
		if ($this->failed_state)
			$yes = false;

		if (Input::get("remove_{$this->image_column}") == "1")
			@unlink($this->image_dir . Input::get("preserve_{$this->image_column}"));

		if ($this->new_image_name != "") {
			$to_delete = $yes ? $this->old_image_name : $this->new_image_name;

			if ($to_delete != "")
				@unlink("{$this->image_dir}{$to_delete}");
		}
	}

	function format_file_size($bytes) {
		if ($bytes > 1048600) {
			$bytes /= 1048600;
			$bytes = sprintf("%01.1f MB", $bytes);
		} else 
			if ($bytes > 1024) {
				$bytes = intval($bytes/1024);
				$bytes = "$bytes KB";
			} else
				$bytes = "$bytes B";

		return $bytes;
	}



	// Validation
	function init_validation($rules=[], $exclude=[]) {
		$this->validation = new Validate();

		foreach ($this->controls as $control)
			if (isset($control["TableColumn"])  &&  $control["TableColumn"]!=""  &&  isset($control["Type"])  &&  $control["Type"]!="ImageFile")
				if (array_search($control["TableColumn"], $exclude) === FALSE)
					$this->validation_rules[$control["TableColumn"]] = array_merge(["display"=>$control["Label"]!="" ? $control["Label"] : $control["TableColumn"]], $rules);
	}

	function add_validation_rules($columns, $rules) {
		if (!is_array($columns))
			$columns = [$columns];

		foreach ($columns as $column)
			foreach ($rules as $rule=>$value)
				if (isset($this->validation_rules[$column]))
					$this->validation_rules[$column][$rule] = $value;
	}

	function validate($custom_errors=[], $string="Incorrect form data") {
		if ($this->failed_state)
			return false;

		$this->validation->check($this->data, $this->validation_rules, false);

		if (!empty($custom_errors))
			$this->validation->_errors = array_merge($this->validation->_errors, $custom_errors);

		$result = empty($this->validation->_errors);

		if (!$result)
			$this->alert("$string: <br><br>" . $this->validation->display_errors());

		return $result;
	}



	// Alerts
	function alert_type($class, $message) {
		if (!is_string($class))
			$class = $class ? "success" : "danger";

		$this->alerts[] = ["Class"=>$class, "Message"=>$message];
		return count($this->alerts) - 1;
	}

	function alert($message) {
		return $this->alert_type(false, $message);
	}

	function feedback($result, $ok_message, $fail_message, $function=null, $function_arg=null) {
		$alert_arg = [false, $fail_message];
		$count     = 0;

		if (is_object($result))
			$result = !$result->error();

		if ($result) {
			$db        = DB::getInstance();
			$count     = $db->count();
			$alert_arg = [true, $ok_message];
		}	

		if (isset($function))
			$alert_arg[1] = call_user_func($function, $alert_arg[1], isset($function_arg) ? $function_arg : [$count]);

		$this->alert_type($alert_arg[0], $alert_arg[1]);
		return $alert_arg[0];
	}



	// Miscellaneous
	function include_file($list) {
		if (!is_array($list))
			$list = [$list];

		foreach ($list as $file)
			if (array_search($file, $this->external_files) === FALSE)
				$this->external_files[] = $file;
	}

	function fail($message) {
		foreach ($this->hidden as $name=>$value)
			$this->hidden[$name] = NULL;

		$this->alert($message);
		$this->failed_state = true;
	}

	function display() {
		$html = "";

		// Open container
		if ($this->add_container)
			$html .= "<div class=\"col-lg-{$this->size}". ($this->offset>=0 ? " col-lg-offset-{$this->offset}" : "") ."\">";

		// Output alerts
		foreach ($this->alerts as $alert) {
			$class = $alert["Class"];
			$glyph = "asterisk";

			switch ($class) {
				case "success" : $glyph="ok"       ; break;
				case "danger"  : $glyph="remove"   ; break;
				case "info"    : $glyph="info-sign"; break;
				case "warning" : $glyph="alert"    ; break;
			}

			$html .= "
			<div class=\"alert alert-{$class}\" role=\"alert\">
				<span class=\"glyphicon glyphicon-{$glyph}\" style=\"font-size:16px;\" aria-hidden=\"true\"></SPAN>
				&nbsp;&nbsp;{$alert["Message"]}
			</div>";
		};


		// Close if error
		if ($this->failed_state)
			return "$html</div>";


		// Include files
		foreach ($this->external_files as $file) {
			$code = "";
			$ext  = substr( strrchr($file,'.'), 1 );

			if ($ext == "js")
				$code = "<script type=\"text/javascript\" src=\"{$file}\"></script>";
			else
				if ($ext == "css")
					$code = "<link rel=\"stylesheet\" href=\"{$file}\" />";

			echo $code;
		}


		// Second container
		if ($this->add_container)
			$html .= "<div class=\"well bs-component\">";

		// Open form
		$html .= "<form id=\"{$this->id}\" class=\"{$this->form_class}\" enctype=\"multipart/form-data\" method=\"post\" action=\"{$this->action}\"><fieldset>";

		if ($this->title != "")
			$html .= "<legend>{$this->title}</legend>";


		// Generate hidden variables
		if ($this->image_column != "") {
			$this->hidden["MAX_FILE_SIZE"] = $this->max_image_size;
			
			if (isset($this->data[$this->image_column]))
				$this->hidden["preserve_{$this->image_column}"] = $this->data[$this->image_column];
		}

		foreach ($this->hidden as $variable=>$value)
			$html .= "<input type=\"hidden\" id=\"$variable\" name=\"$variable\" value=\"$value\" />";



		// Generate controls
		$inline_enabled = false;
		$inline_wrap    = true;
		$i              = -1;
		$last           = count($this->controls) - 1;
		$keys           = array_keys($this->controls);

		foreach ($this->controls as $control) {
			$undefined_indexes = ["Default", "GroupClass", "Group", "Help", "CloseInline", "TableColumn", "Share", "Label", "Class", "Property", "DivInline"];
			
			foreach ($undefined_indexes as $index)
				if (!isset($control[$index]))
					$control[$index] = "";
			
			$i++;
			$value   = isset($this->data[$control["TableColumn"]]) ? $this->data[$control["TableColumn"]] : $control["Default"];
			$ID      = isset($control["ID"]) ? $control["ID"] : $control["TableColumn"];
			$content = "";
			$wrap    = "
			<div class=\"form-group {$control["GroupClass"]}\" {$control["Group"]}>
				<label for=\"{$ID}\" class=\"col-lg-{$this->label_size} ".(isset($control["LabelClass"]) ? $control["LabelClass"] : "control-label")."\">{$control["Label"]}</label>";
				
			if ($this->add_input_container)
				$wrap .= "<div class=\"col-lg-{$this->input_size}\">";


			// Controls in a single line ------------------------------------------------------------------
			if (isset($control["Inline"])) {
				if (!$inline_enabled) {
					$inline_enabled = true;
					$inline_wrap    = $control["Inline"] > 0;

					if ($inline_wrap)
						$wrap .= "<div class=\"form-group row\" style=\"margin-top:0; margin-bottom:0;\">";
				} else
					$wrap = "";
			}//--------------------------------------------------------------------------------------------
			
			
			// Input addons -------------------------------------------------------------------------------
			if (isset($control["Addons"])) {
				$content .= "<div class=\"input-group\">";
				
				foreach ($control["Addons"][0] as $addon)
					$content .= "<div class=\"input-group-addon\">$addon</div>";
			}//--------------------------------------------------------------------------------------------


			switch ($control["Type"])
			{
				case "Text" : 
				$type     = stripos($control["Property"], "type=")===false ? "TYPE=\"text\"" : "";
				$content .= "<input {$type} class=\"form-control {$control["Class"]}\" id=\"{$ID}\" name=\"{$control["TableColumn"]}\" value=\"{$value}\" placeholder=\"{$control["Placeholder"]}\" {$control["Property"]}>";
				break;


				case "TextArea" : 
				$rows = $control["Rows"];

				if ($rows < 0) {
					$rows = substr_count($value, "\n");
					if ($rows == 0)
						$rows = 1;
					$rows++;
				}

				$content .= "<textarea class=\"form-control {$control["Class"]}\" rows=\"{$rows}\" id=\"{$ID}\" name=\"{$control["TableColumn"]}\" placeholder=\"{$control["Placeholder"]}\" {$control["Property"]}>{$value}</textarea>";
				break;


				case "Static" : 
				$content .= "<p class=\"form-control-static {$control["Class"]}\" id=\"{$ID}\" name=\"{$control["TableColumn"]}\" value=\"{$value}\" {$control["Property"]}>" . (isset($control["Text"]) ? $control["Text"] : $value) . "</p>";
				break;


				case "Select" : 
				switch($control["SubType"]) {
					case "datalist" :
						$content .= "<input class=\"form-control {$control["Class"]}\" name=\"{$control["TableColumn"]}\" value=\"{$value}\" id=\"{$ID}\" {$control["Property"]} list=\"{$ID}_datalist\"><datalist id=\"{$ID}_datalist\">";	
						break;

					case "checkbox" : 
					case "radio"    : 
						$content .= "<div id=\"{$ID}\">";
						break;

					default : 
						$content .= "<select ". ($control["Size"]>0 ? "multiple size={$control["Size"]}" : "") . " class=\"form-control {$control["Class"]}\" name=\"{$control["TableColumn"]}". ($control["Size"]>0 ? "[]" : "") . "\" id=\"{$ID}\" {$control["Property"]}>\n";
						break;
				}

				$number = 0;
				$optgroup_opened = false;

				foreach ($control["Options"] as $option) {
					$option_name  = $option;
					$option_value = $option;
					$option_extra = "";
					$isSelected   = false;

					if (is_array($option)) {
						$count = count($option);

						if ($count == 0)
							continue;

						$option_name  = $option[0];
						$option_value = $count > 1 ? $option[1] : $option_name;
						$option_extra = $count > 2 ? $option[2] : "";
					}

					if (is_array($value))
						$isSelected = in_array($option_value, $value);
					else
						if (strlen($value) > 0)
							$isSelected = $option_value == $value;

					if (!in_array($control["SubType"],["checkbox","radio"])) {
						if ($option_extra == "optgroup") {
							if ($optgroup_opened)
								$content .= "</optgroup>";
							
							$content .= "<optgroup label=\"{$option_name}\">";
							$optgroup_opened = true;
						} else
							$content .= "<option {$option_extra} value=\"{$option_value}\" " . ($isSelected ? "selected" : "") . ">{$option_name}</option>\n";
						
					} else {
						if (!$control["CheckboxInline"])
							$content .= "<div class=\"{$control["SubType"]}\" id=\"{$ID}_{$number}\">";
						
						$content .= "
							<label " . ($control["CheckboxInline"] ? "class=\"{$control["SubType"]}-inline\"" : "") . ">
								<input type=\"{$control["SubType"]}\" id=\"{$ID}_{$number}_input\" name=\"{$control["TableColumn"]}". ($control["SubType"]=="checkbox" ? "[]" : "") . "\" value=\"{$option_value}\" {$option_extra} " . ($isSelected ? "checked" : "") . " {$control["Property"]}>
								{$option_name}
							</label>";
							
						if (!$control["CheckboxInline"])
							$content .= "</div>";
					}

					$number++;
				}
				
				if ($optgroup_opened) 
					$content .= "</optgroup>";

				switch($control["SubType"]) {
					case "datalist" : $content .= "</datalist>\n"; break;
					case "radio"    : 
					case "checkbox" : $content .= "</div>"       ; break;
					default         : $content .= "</select>\n"  ; break;
				}
				break;


				case "ImageFile" : 				
				if (isset($this->data[$control["TableColumn"]])  &&  $this->data[$control["TableColumn"]] != "")
					$value = $this->data[$control["TableColumn"]];
				else
					if ($this->hidden["preserve_{$this->image_column}"] != "")
						$value = $this->hidden["preserve_{$this->image_column}"];
					else
						if ($control["Default"] != "")
							$value = $control["Default"];

				if ($value != "") {
					$extension = substr($value, -3);
					$src       = "{$this->image_dir}{$value}";
					$alt       = "No image";

					if ($extension == "paa") {
						$src = "";
						$alt = "PAA. No preview";
					}

					$content .= "
					<img src=\"$src\" class=\"img-thumbnail\" alt=\"$alt\">
					<div class=\"checkbox\">
						<label>
							<input type=\"checkbox\" name=\"remove_{$this->image_column}\" value=\"1\"> Remove
						</label>
					</div>
					<br>";
				}

				$content .= "<input type=\"file\" id=\"{$ID}\" name=\"{$control["TableColumn"]}\" {$control["Property"]} hidden>";
				break;


				case "Button" : 
				$type     = stripos($control["Property"], "type=")===false ? "TYPE=\"submit\"" : "";
				$content .= "<button {$type} class=\"btn {$control["Class"]}\" id=\"{$ID}\" name=\"{$control["Name"]}\" value=\"{$control["Value"]}\" {$control["Property"]}>{$control["Text"]}</button>";
				break;


				case "DateTime" : 
				$object = DateTime::createFromFormat($control["StoreFormat"], $value);

				if ($object  &&  DateTime::getLastErrors()["warning_count"]==0  &&  DateTime::getLastErrors()["error_count"]==0)
					$value = $object->format('r');
				
				//https://stackoverflow.com/questions/30186611/php-dateformat-to-moment-js-format#30192680
				$replacements = [
					'd' => 'DD',
					'D' => 'ddd',
					'j' => 'D',
					'l' => 'dddd',
					'N' => 'E',
					'S' => 'o',
					'w' => 'e',
					'z' => 'DDD',
					'W' => 'W',
					'F' => 'MMMM',
					'm' => 'MM',
					'M' => 'MMM',
					'n' => 'M',
					't' => '', // no equivalent
					'L' => '', // no equivalent
					'o' => 'YYYY',
					'Y' => 'YYYY',
					'y' => 'YY',
					'a' => 'a',
					'A' => 'A',
					'B' => '', // no equivalent
					'g' => 'h',
					'G' => 'H',
					'h' => 'hh',
					'H' => 'HH',
					'i' => 'mm',
					's' => 'ss',
					'u' => 'SSS',
					'e' => 'zz', // deprecated since version 1.6.0 of moment.js
					'I' => '', // no equivalent
					'O' => '', // no equivalent
					'P' => '', // no equivalent
					'T' => '', // no equivalent
					'Z' => '', // no equivalent
					'c' => '', // no equivalent
					'r' => '', // no equivalent
					'U' => 'X',
				];
				
				$moment   = strtr($control["DisplayFormat"], $replacements);
				$content .= "
				<div class=\"input-group date\" id=\"{$ID}\">
					<input type=\"text\" class=\"form-control\" id=\"{$ID}_input\" name=\"{$control["TableColumn"]}\" {$control["Property"]}>
					<span class=\"input-group-addon\">
						<span class=\"fa fa-calendar\"></span>
						&nbsp;
						<span class=\"glyphicon glyphicon-time\"></span>
					</span>
				</div>
				<script type=\"text/javascript\">
					moment.locale('{$control["Language"]}');
					$(function () {
						$('#{$ID}').datetimepicker({
							defaultDate: moment('{$value}'),
							format: '{$moment}',
							locale: '{$control["Language"]}',
							sideBySide: true,
							allowInputToggle: true,
							toolbarPlacement: 'bottom',
							showTodayButton: true,
							showClose: true,
							icons: {
								today: 'fa fa-undo',
								close: 'fa fa-check'
							}
						})
						.on('dp.change', function(e) {
							$('#{$ID}_datetime').attr('value',moment(e.date).toISOString(true));
						});
					});	
					$('#{$ID}_input').attr('placeholder', moment('{$value}').format('{$moment}'));
					$('#{$ID}_datetime').attr('value',moment('{$value}').toISOString(true));
				</script>";
				break;


				case "Custom" :
				$wrap     = "";
				$content .= $control["Code"];
				break;


				case "EmptySpan" : 
				$content .= "<span id=\"{$ID}\"></span>";
				break;


				case "Header" : 
				$content .= "<h{$control["Level"]} id=\"{$ID}\">{$control["Text"]}</h{$control["Level"]}>";
				break;
			}
			
			
			// Close addons -------------------------------------------------------------------------------
			if (isset($control["Addons"])) {
				foreach ($control["Addons"][1] as $addon)
					$content .= "<div class=\"input-group-addon\">$addon</div>";
				
				$content .= "</div>";
			}//--------------------------------------------------------------------------------------------


			if (strlen($control["Help"]) > 0)
				$content .= "<span class=\"help-block\">{$control["Help"]}</span>";


			// Extra wrap for inline elements------------------------------------------------------------------
			if ($inline_enabled  &&  $inline_wrap)
				$content = "<div class=\"col-lg-{$control["Inline"]}\" {$control["DivInline"]}>$content</div>";
			//-------------------------------------------------------------------------------------------------


			$html .= $wrap . $content . "\n";

			
			// Close inline----------------------------------------------------------------------------------------------------------------
			if ($inline_enabled  &&  ($control["CloseInline"]===TRUE  ||  $i==$last  ||  !isset($this->controls[$keys[$i+1]]["Inline"]))) {
				$inline_enabled = false;
				$wrap           = " ";

				if ($inline_wrap)
					$html .= "</div>";
			}//----------------------------------------------------------------------------------------------------------------------------


			// Close wrap
			if ($wrap!=""  &&  !$inline_enabled) {
				if ($this->add_input_container)
					$html .= "</div>";

				$html .= "</div>";
			}
		}

		$html .= "</fieldset></form>";

		if ($this->add_container)
			$html .= "</div></div>";

		return $html;
	}
}
?>