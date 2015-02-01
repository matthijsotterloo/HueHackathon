<?php
/*
Hue auth API
(c) 2015 Lukas van den Dijssel. Alle rechten voorbehouden.
*/

error_reporting(E_ALL);

class Auth {
	//Settings
	const DB_DATABASE = 'hue';
	const DB_USERNAME = 'hue';
	const DB_PASSWORD = 'P@$$w0rd';
	const DB_HOSTNAME = 'localhost';
	const DB_PREFIX = 'auth_';
	
	//echo('debug 1<br>');
	
	//Defines
	var $db;
	
	//Functions
	function __construct() {
		//$this->mysqli = new mysqli(self::DB_HOSTNAME, self::DB_USERNAME, self::DB_PASSWORD, self::DB_DATABASE);
		$this->db = new PDO('mysql:host=' . self::DB_HOSTNAME . ';dbname=' . self::DB_DATABASE . ';charset=utf8', self::DB_USERNAME, self::DB_PASSWORD, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	}
	
	function register($username, $password) { //Register a new username
		if(empty($username) || strlen($username) > 50 || empty($password)) { //Credentials not entered
			return('CREDENTIALS_INVALID'); //Too long or empty
		}
		
		//Normal username (e.g. Sydcul)
		$usercheck_stmt = $this->db->prepare('SELECT * FROM ' . self::DB_PREFIX . 'users WHERE username=?');
		$usercheck_stmt->execute(array($username));
		$usercheck_rows = $usercheck_stmt->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($usercheck_rows)) { //Taken
			return('USERNAME_TAKEN');
		}
		
		//SECURITY STUFF
		$salt = substr(str_shuffle(md5(time())), 0, 10);
		$hash = hash('sha512', $password . $salt);
		
		//REGISTRATION
		$registration_stmt = $this->db->prepare('INSERT INTO ' . self::DB_PREFIX . 'users (username, password, salt) VALUES (?, ?, ?)');
		$registration_stmt->execute(array($username, $hash, $salt));
		
		//Yay, it worked!
		return('SUCCESS');
	}
	
	function credentialscheck($username, $password) { //Check if the credentials are correct
		if(empty($username) || empty($password)) { //Credentials not entered
			return('INVALID'); //Too empty
		}
		
		//Retrieves the password/salt from the DB
		$password_stmt = $this->db->prepare('SELECT password,salt FROM ' . self::DB_PREFIX . 'users WHERE username=?');
		$password_stmt->execute(array($username));
		$password_rows = $password_stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if(empty($password_rows)) { //Unknown username.
			return('INCORRECT');
		}
		
		//Stores some stuff in some variables.
		$salt = $password_rows[0]['salt'];
		$correctpassword = $password_rows[0]['password'];
		
		//Hashes the user input password and checks it.
		$userpassword = hash('sha512', $password . $salt);
		
		if($userpassword != $correctpassword) { //Wrong password
			return('INCORRECT');
			//return($password_rows);
		}
		
		return('CORRECT');
	}
	
	function getuserid($username) {
		$usercheck_stmt = $this->db->prepare('SELECT userid FROM ' . self::DB_PREFIX . 'users WHERE username=?');
		$usercheck_stmt->execute(array($username));
		$usercheck_rows = $usercheck_stmt->fetchAll(PDO::FETCH_ASSOC);
		if(empty($usercheck_rows)) { //Unavailable
			return(false);
		}
		return $usercheck_rows[0]['userid'];
	}
	
	function login($username, $password) { //Logs in the user
		$check = $this->credentialscheck($username, $password);
		
		if($check != 'CORRECT') { //Incorrect user/pass combo
			return('CREDENTIALS_' . $check);
			//return($check);
		}
		
		$userid = $this->getuserid($username); //Retrieves userid
		
		//Removes any old sessions
		//$delete_stmt = $this->db->prepare('DELETE FROM ' . self::DB_PREFIX . 'sessions WHERE userid=?');
		//$delete_stmt->execute(array($userid));
		
		//Creates session
		
		$created = date('Y-m-d H:i:s', time());
		$expires = date('Y-m-d H:i:s', time()+86400);
		
		$session_stmt = $this->db->prepare('INSERT INTO ' . self::DB_PREFIX . 'sessions (userid, created, expires) VALUES (?, ?, ?)');
		$session_stmt->execute(array($userid, $created, $expires));
		
		//Retrieves auto-increment session ID
		$id_stmt = $this->db->prepare('SELECT sessionid FROM ' . self::DB_PREFIX . 'sessions WHERE userid=?');
		$id_stmt->execute(array($userid));
		$id_rows = $id_stmt->fetchAll(PDO::FETCH_ASSOC);
		$sessionid = $id_rows[0]['sessionid'];
		
		//Stores session cookies
		setcookie('userid', $userid);
		setcookie('sessionid', $sessionid);
		
		//return($userid . ';' . $sessionid);
	}
	
	function cleanup() { //Get rid of any expired sessions
		$current = date('Y-m-d H:i:s', time());
		$delete_stmt = $this->db->prepare('DELETE FROM ' . self::DB_PREFIX . 'sessions WHERE expires<?');
		$delete_stmt->execute(array($current));
		return('SUCCESS');
	}
	
	function logincheck() { //Check if the user is logged in.
		$this->cleanup();
		
		$session_stmt = $this->db->prepare('SELECT * FROM ' . self::DB_PREFIX . 'sessions WHERE userid=?');
		$session_stmt->execute(array($_COOKIE['userid']));
		$session_rows = $session_stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$valid = false;
		
		foreach ($session_rows as $session) {
			if($session['userid'] == $_COOKIE['userid'] && $session['sessionid'] == $_COOKIE['sessionid']) { //User IS logged in.
				$valid = true;
			}
		}
		
		return($valid);
	}
}
?>			