<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function login($username, $password) {
	if(empty($username) || empty($password)) {
		return false;
	}
	global $mysqli;
	//Logt in, true indien ingelogd, false bij fout password
	if ($stmt = $mysqli->prepare('SELECT userid,password FROM auth_users WHERE username=?')) {
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->bind_result($userid, $dbpass);
		$stmt->fetch();
		$stmt->close();
	}
	
	if(md5($password) == $dbpass) {
		$done = false;
		while($done == false) {
			$sessionid = rand(100000000, 999999999);
			if($result = $mysqli->query('SELECT * FROM auth_sessions WHERE sessionid=' . $sessionid)) {
				if($result->num_rows == 0) {
					$done = true;
				}
				$result->close();
			}
		}
		//$mysqli->query('DELETE FROM sessions WHERE userid=' . $userid); //Logt huidige sessie uit.
		logout($userid);
		if(session_status() != PHP_SESSION_ACTIVE) {
			session_start();
		}
		$mysqli->query('INSERT INTO auth_sessions (userid, sessionid) VALUES (' . $userid . ', ' . $sessionid . ')'); //Logt in
		$_SESSION['userid'] = $userid;
		$_SESSION['sessionid'] = $sessionid;
		return true;
	} else {
		sleep(1);
		return false;
	}
}

function logincheck() {
	if(session_status() != PHP_SESSION_ACTIVE) {
		session_start();
	}
	if(!isset($_SESSION['userid']) || !isset($_SESSION['userid'])) {
		return false;
	}
	global $mysqli;
	$userid = $_SESSION['userid'];
	$sessionid = $_SESSION['sessionid'];
	if($result = $mysqli->query('SELECT * FROM auth_sessions WHERE userid=' . $userid . ' AND sessionid=' . $sessionid)) {
		if($result->num_rows > 0) {
			return $userid;
		} else {
			return false;
		}
		$result->close();
	}
}

function getusername($userid) {
	global $mysqli;
	if ($stmt = $mysqli->prepare('SELECT username FROM auth_users WHERE userid=?')) {
		$stmt->bind_param("i", $userid);
		$stmt->execute();
		$stmt->bind_result($username);
		$stmt->fetch();
		$stmt->close();
	}
	if(!empty($username)) {
		return $username;
	} else {
		return false;
	}
}

function logout($userid = null) {
	//Logt uit
	if(session_status() != PHP_SESSION_ACTIVE) {
		session_start();
	}
	if(empty($userid)) {
		if(!isset($_SESSION['userid'])) {
			return false;
		}
		$userid = $_SESSION['userid'];
	}
	global $mysqli;
	if($mysqli->query('DELETE FROM sessions WHERE userid=' . $userid)) {
		if(!empty($_SESSION['userid']) && !empty($_SESSION['sessionid'])) {
			unset($_SESSION['userid']);
			unset($_SESSION['sessionid']);
		}
		return true;
	} else {
		return false;
	}
}

function register($username, $password, $email) {
	//Registreert een gebruikersnaam. Op het moment geen verificatie.
	global $mysqli;
	if ($stmt = $mysqli->prepare('SELECT userid FROM auth_users WHERE username=?')) {
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->bind_result($userid);
		$stmt->fetch();
		$stmt->close();
	}
	if(!empty($userid)) { //Username ingenomen
		return false;
	}
	
	$done = false;
	while($done == false) { //Blijft loopen totdat een geldige userid is gevonden.
		$userid = rand(100000000, 999999999);
		if($result = $mysqli->query('SELECT * FROM auth_users WHERE userid=' . $userid)) {
			if($result->num_rows == 0) {
				$done = true;
			}
			$result->close();
		}
	}
	
	$hash = md5($password);
	
	if ($stmt = $mysqli->prepare('INSERT INTO auth_users (userid, username, password, salt) VALUES (?, ?, ?, ?)')) {
		$stmt->bind_param('isss', $userid, $username, $hash, $email);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
	}
}

function deleteuser($userid = null) {
	//Deletet het ingelogde account.
	global $mysqli;
	if(session_status() != PHP_SESSION_ACTIVE) {
		session_start();
	}
	if(empty($userid)) {
		$logincheck = logincheck();
		if(!empty($logincheck)) {
			$userid = $logincheck;
		} else {
			return false;
		}
	}
	if(!usercheck($userid)) {
		return false;
	}
	
	logout($userid);
	
	$mysqli->query('DELETE FROM auth_users WHERE userid=' . $userid);
	
	return true;
}

function usercheck($userid) {
	global $mysqli;
	if ($stmt = $mysqli->prepare('SELECT userid FROM auth_users WHERE id=?')) {
		$stmt->bind_param('i', $userid);
		$stmt->execute();
		$stmt->bind_result($result);
		$stmt->fetch();
		$stmt->close();
	}
	if(!empty($result)) {
		return true;
	} else {
		return false;
	}
}

function facebookcheck($username) {
	global $mysqli;
	if ($stmt = $mysqli->prepare('SELECT userid FROM auth_users WHERE username=?')) {
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->bind_result($result);
		$stmt->fetch();
		$stmt->close();
	}
	if(!empty($result)) {
		return true;
	} else {
		return false;
	}
}
?>
