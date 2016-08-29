<?php
require '../vendor/autoload.php';
require '../class.php';
require '../config.php';


//code
//{1} - ok
//{0} - error
//{2} - permission

$db = new mysqli($mysql_server, $mysql_user, $mysql_pass, $mysql_database);
Flight::set('models', new Models($db));

Flight::map('checkParams', function($require){
   	$error = true;
	foreach ($require as $key){
		if(!isset(Flight::request()->data->$key) || strlen(Flight::request()->data->$key) == 0)
			$error = false;
	}
	return $error;
});

Flight::map('checkToken', function($token){
	$user = Flight::get('models');
	return $user->checkToken($token);
});

Flight::map('getStudent', function($token){
	$user = Flight::get('models');
	return $user->getSessionUser($token);
});

Flight::map('checkPermission', function($token){
	$user = Flight::get('models');
	return $user->sessionIsAdmin($token);
});

Flight::route('POST /login', function(){
    if(!Flight::checkParams(array('username', 'password'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
	    $user = Flight::get('models');
	    	if(!$user->checkPassword(Flight::request()->data->username, md5(Flight::request()->data->password))){
	    		$callback = array('code' => '0', 'message' => 'Wrong username or password');
	    	} else {
	    		$token = $user->addSession(Flight::request()->data->username);
	    		$info = $user->getUserByUsername(Flight::request()->data->username); $info = $info->fetch_array();
	    		$callback = array('code' => '1', 'message' => 'Login successful', 'token' => $token, 'type' => $info['type']);
	    	}
	    Flight::json($callback);
	}
});

Flight::route('POST /add/@object', function($object){
    if(!Flight::checkParams(array('token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		//$user->addAsk(Flight::request()->data->student, Flight::request()->data->content, Flight::request()->data->date);
	   		$callback = array('code' => '1', 'message' => 'Ask added!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});
Flight::start();
$db->mysqli_close();
?>
