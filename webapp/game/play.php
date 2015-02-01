<?php
require_once('../auth.inc.php');
$auth = new Auth();

$check = $auth->logincheck();

if($check != true) {
	header('Location: /login.php?message=You%20have%20to%20log%20in%20first.');
	die();
}

if(empty($_GET['join']) && empty($_GET['create'])) {
	header('Location: ./');
}

$userid = $_COOKIE['userid'];
?>
<html>
<body>
<h1>Game</h1>
</body>
</html>