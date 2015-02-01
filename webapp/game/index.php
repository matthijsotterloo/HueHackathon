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
<form action="play.php" method="GET">
Bestaande game joinen: <input type="text" autocomplete="off" name="join"><br>
<input type="submit" value="Join">
</form>
<form action="play.php" method="GET">
<input type="hidden" name="create" value="1"><br>
<input type="submit" value="Nieuwe game aanmaken">
</form>
<?php
echo($userid);
?>
</body>
</html>