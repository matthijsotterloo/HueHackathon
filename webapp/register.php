<?php
require_once('auth.inc.php');
if(!empty($_POST['username']) && !empty($_POST['password'])) {
	$auth = new Auth();
	$auth->register($_POST['username'], $_POST['password']);
	header('Location: /login.php?message=You%20can%20now%20log%20in.');
	die();
}
?>
<html>
<body>
<h1>Register</h1>
<form action="register.php" method="POST">
Username: <input type="text" autocomplete="off" name="username"><br>
Password: <input type="password" autocomplete="off" name="password">
<input type="submit">
</form>
</body>
</html>