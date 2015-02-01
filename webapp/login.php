<?php
if(!empty($_POST['username']) && !empty($_POST['password'])) {
header('Location: dashboard.php');
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