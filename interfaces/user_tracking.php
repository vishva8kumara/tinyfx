<?php
if (!isset($user_id)){
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if (isset($_SERVER['REMOTE_ADDR']))
		$ip = $_SERVER['REMOTE_ADDR'];
	else if (isset($_SERVER['HTTP_CLIENT_IP']))
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	$ip_address = $ip;
	//	$ip_address may be used in several other places, so DO NOT move this block to inside the following else block..

	if (isset($_COOKIE['user_id'])){
		$user_id = $_COOKIE['user_id'];
	}
	else{
		$ip = explode(".", $ip);
		$ip = sha1(microtime() * ($ip[3] + ($ip[2]*4) + ($ip[1]*8) + ($ip[0]*16)));
		$user_id = strtoupper($ip);
		setcookie('user_id', $user_id, time()+(3600*24*365*5));
		//setcookie('user_id', 0, time() - 3600, '/', 'public.kaputa.lk/');
	}
}
/*$db = connect_database();
$login = $db->query('SELECT user_id, active FROM login WHERE browser_id = \''.$user_id.'\'');
if (isset($user)){
	$login = mysql_fetch_array($login, MYSQL_ASSOC);
	if ($login['active'] == '0'){
		unset($_SESSION['user']);
		unset($user);
		flash_message('Your session is terminated', 'warning');
	}
}
else{
	if ($login = mysql_fetch_array($login, MYSQL_ASSOC)){
		if ($login['active'] == 1){
			$login = $db->query('SELECT id, auth_level, full_name, username, password, email, approved FROM user WHERE id = '.$login['user_id']);
			if ($login = mysql_fetch_array($login, MYSQL_ASSOC))
				$_SESSION['user'] = $login;
				$user = $login;
				$login = $db->query('UPDATE login SET last_login = \''.date('Y-m-d H:i:s').'\' WHERE browser_id = \''.$user_id.'\'');
				if (isset($_SESSION['REDIRECT_AFTER_SIGNIN']))
					header('location:'.$_SESSION['REDIRECT_AFTER_SIGNIN']);
		}
	}
}*/
?>
