<?php
require_once 'users/init.php';
/* for older userspice
require_once $abs_us_root.$us_url_root.'users/includes/header.php';
require_once $abs_us_root.$us_url_root.'users/includes/navigation.php';
*/
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';

if (!securePage($_SERVER['PHP_SELF']))
	die();

if (!$user->isLoggedIn())
	Redirect::to('users\login.php');

/*if (!Input::exists())
	Redirect::to('index.php');*/

$uid   = $user->data()->id;
$token = $_POST["csrf"];

/*if (!Token::check($token))
	die("<div id=\"page-wrapper\"><div class=\"container\">Page expired. Try again from home page</div></div>");*/
?>

<div id="page-wrapper">
<div class="container">
