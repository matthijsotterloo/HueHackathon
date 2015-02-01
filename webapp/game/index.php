<?php
require_once('auth.inc.php');
$auth = new Auth();

$check = $auth->logincheck();

if($check != true) {
	header('Location: login.php?message=You%20have%20to%20log%20in%20first.');
	die();
}
$userid = $_COOKIE['userid'];
?>
<html>
<body>
<h1>Game</h1>
<?php
echo($userid);
?>
</body>
</html>