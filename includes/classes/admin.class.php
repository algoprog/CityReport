<?php

class admin{
	
	public function is_logged(){
		if(!empty($_SESSION['admin'])) return 1;
		else return 0;
	}
	
	public function login($user, $pass){
		if($user==ADMIN_USER && $pass==ADMIN_PASS){
			$_SESSION['admin'] = '1';
			return 1;
		}else{
			return 0;
		}
	}
	
	public function logout(){
		unset($_SESSION['admin']);
		header("Location: admin");
	}
	
}

?>