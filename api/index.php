<?php
require '../vendor/autoload.php';
require '../class.php';
require '../config.php';
//code
//{1} - ok
//{0} - error
//{2} - permission

Flight::set('db', new mysqli($mysql_server, $mysql_user, $mysql_pass, $mysql_database));

Flight::map('checkParams', function($require){
   	$error = 0;
	foreach ($require as $key){
		if(!isset($_REQUEST[$key]) || empty($_REQUEST[$key]))
			$error++;
	}
	if($error == 0)
		return true;
	else
		return false;
});

Flight::route('POST /login', function(){
    echo 'I received a POST request.';
});

Flight::route('POST /user/add', function(){
	if(!Flight::checkParams(array('username', 'password', 'type'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
	    $user = new User(Flight::get('db'));
	    if($_POST['type'] == 3)
	    	$user->addUser($_POST['username'], $_POST['password'], $_POST['type'],  0);
	    else
	    	$user->addUser($_POST['username'], $_POST['password'], $_POST['type'],  $_POST['user']);
	    $callback = array('code' => '1', 'message' => 'User added!');
	    Flight::json($callback);
	}
});

Flight::start();
?>