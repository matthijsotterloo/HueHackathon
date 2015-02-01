<?php
require_once('../auth.inc.php');

$db = new PDO('mysql:host=localhost;dbname=hue;charset=utf8', 'hue', 'P@$$w0rd', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$auth = new Auth();

$check = $auth->logincheck();

if($check != true) {
	header('Location: ../login.php?message=You%20have%20to%20log%20in%20first.');
	die();
}

if(empty($_GET['join']) && empty($_GET['create'])) {
	header('Location: ./');
}

if(isset($_GET['create'])) {
	$players = implode(';', $_GET['players']);
	$stmt = $db->prepare('INSERT INTO games (users) VALUES (?)');
	$stmt->execute(array($players));
	$gameid = $db->lastInsertId();
}

$userid = $_COOKIE['userid'];
?>
<html>
<body>
<h1>Game</h1>
<h2>Je gameid is <?php echo($gameid); ?></h2>
</body>
</html>