<?php
require_once('auth.inc.php');
$auth = new Auth();

$check = $auth->logincheck();

if($check == true) {
	$userid = $_COOKIE['userid'];
	echo($userid);
} else {
	header('Location: login.php?message=You%20have%20to%20log%20in%20first.');
}
?>