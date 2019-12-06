<?php
include("__header.php");



// At the beginning I create form instance
$form = new Generated_Form();

// At the end I show the form
echo $form->display();



/* Class Generated_Form consists of:
- 6 arrays that hold information about the form
- 27 methods mostly for modifying these arrays
- 17 miscellaneous attributes */



include("__footer.php");
?>