<?php
require_once('auth.inc.php');
if(!empty($_POST['username']) && !empty($_POST['password'])) {
	$auth = new Auth();
	$status = $auth->login($_POST['username'], $_POST['password']);
	if($status == 'SUCCESS') {
		header('Location: dashboard.php');
	}
}
?>
<html>
<body>
<h1>Login</h1>
<form action="login.php" method="POST">
Username: <input type="text" autocomplete="off" name="username"><br>
Password: <input type="password" autocomplete="off" name="password">
<input type="submit">
</form>
</body>
</html>