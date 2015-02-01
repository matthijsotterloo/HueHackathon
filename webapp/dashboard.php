<?php
require_once('auth.inc.php');
$auth = new Auth();

$check = $auth->logincheck();

if($check != true) {
	header('Location: login.php?message=You%20have%20to%20log%20in%20first.');
	die();
	$userid = $_COOKIE['userid'];
	echo($userid);
} else {
	
}
?>
<html>
<body>
<h1>
</body>
</html>