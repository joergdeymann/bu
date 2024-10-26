<?php
class cookielogin {
	/* 
		Erster Login
		-> Passwort muss geändert werden
		Funktion nach dem Login Formular 
		$user = user aus der eingabe / datei
		$pw   = Verschlüsseltes PW aus der Datei
		
	*/
	
	public function check(&$user,&$pw) {
		return password_verify($user, $pw);
	}
	public function setCookie() {
		setcookie("username",$_POST['user'],time()+(3600*24)); // 24h
	}

	/* 
		Normaler Login
	*/	
	public function login(&$pw) {		
		if (password_verify($pw, $this->row['pw']))  {
			$_SESSION['username']=$_POST['name'];

			/* 
				nach erfolgreichen Login Cookie verlängern
			*/
			setcookie("username",$_POST['name'],time()+(3600*24)); // 24h
			
			return true;
		}
		return false;
	}


	/*
		setCookie() ginge auch
	*/
	public function login_cookie() {
		if (isset($_COOKIE['username'])) {
			$_SESSION['username']=$_COOKIE['username'];
			setcookie("username",$_COOKIE['username'],time()+(3600*24)); // 24h
			return true;
		} else {
			return false;
		}
	}
	
	/* 
		clear and logout
	*/
	public function clear() {
		if (isset($_COOKIE['username'])) {
			// setcookie("username", "", time() – 3600);			
			// setcookie("username",$_COOKIE['username'],time()-(3600*24)); // 24h
			setcookie("username","",time()-3600); // 24h
			unset($_COOKIE['username']);
			// echo "cookie cleared<br>";
		}
		if(session_status() == PHP_SESSION_ACTIVE) {
			session_destroy();
		}
	}
	
	
	/*
	Abfrage im Programm:
	if (login_cookie()){
		vorbereitungen normal
	} else 
	if (login_firsttime()) {
		Neues Passwort und email eingeben
	} else {
		Login Formular
	}
	*/	
		
}

?>
