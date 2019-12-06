# Generated_Form

A class for [UserSpice](https://userspice.com/) PHP framework 4.3 or newer. Makes it easier to handle HTML forms.

Made by [Faguss](https://ofp-faguss.com/). See [forum](https://userspice.com/forums/showthread.php?tid=644&highlight=generated_form).

**Includes:**
* [Datetimepicker for Bootstrap 3](https://github.com/Eonasdan/bootstrap-datetimepicker/) by eonasdan
* [transition.js](https://github.com/smnh/TransitionJs) by smnh
* [collapse.js](https://getbootstrap.com/docs/3.4/javascript/#collapse)
* [moment.js](https://momentjs.com/) by Tim Wood, Iskren Chernev, Moment.js contributors
* [bootstrap-combobox.js](https://github.com/danielfarrell/bootstrap-combobox) by Daniel Farrell

## Installation:

* Copy files to your Userspice installation folder
* Run query from "gf_example_tables.sql"
* Add permissions for 1-12.php example files in the Admin Dashboard
* Instructions on how to use the Generated_Form class you'll find in the examples. Start with 1_minimal.php (open it in a text editor)
* Example scripts are compatible with UserSpice 4.4. For other versions you'll have to edit header and footer includes


## Version history:

### 05.12.19

* add_text(), add_select(), add_imagefile(), add_button(), add_datetime() - changed order of arguments: "tablecolumn" is now first, "label" is now second and optional
* add_datetime() - now handles localization; changed "close" icon in datepicker
* add_emptyspan() - added optional argument $group
* add_imagefile() - added argument $tablecolumn
* add_select() - now handles "optgroup"
* feedback() - removed macros; instead added optional argument for calling a custom function
* upload_image() - can upload paa image file
* validate() - added optional arugment for a custom failure message
* added function add_js_var()
* added files: "bootstrap-combobox.js", "ru.js
* updated "moment.js" from 2.18.1 to 2.24.0
* bugfix - checking if array keys exist before using them


### 07.09.17

* fixed typo in code for displaying radios
* added "CheckboxInline" property for placing checkbox and radios inline
* added "Addons" property for placing add-ons in text inputs

### 25.08.17

* added optional $property argument to add_text()
* add_select() can now create radios
* text type can be changed by adding passing "type='email'" as $property argument in add_text()
* optional CSS classes can be added by modifying "Class" and "GroupClass" property with change_control()
* added static control type
* fixed incorrect date validation code when displaying datetime control
* class Validate - function passed() would return incorrect value - fixed

### 18.08.17

* optionally add_validation_rules() argument can be a string instead of an array with strings

### 21.07.17

* datetime conversion to moment.js format is done in display() rather than in add_datetime()
* button type can be changed by adding passing "type='button'" as $property argument in add_button()
* square brackets are automatically added to select multiple names in display()
* reverted class Validate display_errors() change from previous version

### 18.07.17

* removed methods "add_timezone" and "add_checkboxes", instead you use "add_select" where $size is "datalist" or "checkbox"
* checkboxes now have HTML ID property so they can be marked red in validation
* method "add_select" - adds ID property to the array which is "TableColumn" name without square brackets
* method "add_datetime" - added "display" and "store" optional arguments for a custom date & time format
* methods "save_input" and "display" automatically convert date time
* class Validate removes "[]" from ID when adding error message so JS can be used
* class Validate - is_datetime - true value is equal to timestamp

### 15.07.17

* methods that add controls return proper item key
* method change_control() - fixed numeric key handling
* changed ID for TimeZone control

### 14.07.17

* First release
