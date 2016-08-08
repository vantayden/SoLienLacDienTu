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
		if(!isset(Flight::request()->data->$key) || empty(Flight::request()->data->$key))
			$error = false;
	}
	return $error;
});

Flight::map('checkToken', function($token){
	$user = Flight::get('models');
	return $user->checkToken($token);
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
	    		$callback = array('code' => '1', 'message' => 'Login successful', 'token' => $token);
	    	}
	    Flight::json($callback);
	}
});

//Admin Functions

Flight::route('POST /user/add', function(){
	if(!Flight::checkParams(array('username', 'password', 'type'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
	    $user = Flight::get('models');
	    if(Flight::request()->data->type == 3)
	    	$user->addUser(Flight::request()->data->username, Flight::request()->data->password, Flight::request()->data->type,  0);
	    else
	    	$user->addUser(Flight::request()->data->username, Flight::request()->data->password, Flight::request()->data->type,  Flight::request()->data->user);
	    $callback = array('code' => '1', 'message' => 'User added!');
	    Flight::json($callback);
	}
});

Flight::route('POST /ask/add', function(){
	if(!Flight::checkParams(array('student', 'content', 'date', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->addAsk(Flight::request()->data->student, Flight::request()->data->content, Flight::request()->data->date);
	   		$callback = array('code' => '1', 'message' => 'Ask added!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /ask/get', function(){
	if(!Flight::checkParams(array('student', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$asks = $user->getAsks(Flight::request()->data->student);
	        while($row=$asks->fetch_assoc()){
	                $callback[]=$row;
	        }
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /ask/check', function(){
	if(!Flight::checkParams(array('student', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		if($user->checkAsk(Flight::request()->data->student))
	   			$callback = array('code' => '1', 'message' => 'Student is absent today!', 'status' => '1');
	   		else
	   			$callback = array('code' => '1', 'message' => 'Student is present today!', 'status' => '0');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /ask/today', function(){
	if(!Flight::checkParams(array('student', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$today = $user->getAskToday(Flight::request()->data->student);
	   		$row = $today->fetch_array();
	   		$callback = array('code' => '1', 'message' => 'Student is absent today', "content"=>$row['content']);
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /ask/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$today = $user->deleteAsk(Flight::request()->data->id);
	   		$callback = array('code' => '1', 'message' => 'Ask deleted!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /attendance/add', function(){
	if(!Flight::checkParams(array('student', 'date', 'teacher', 'status', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->addAttendance(Flight::request()->data->student, Flight::request()->data->date, Flight::request()->data->teacher, Flight::request()->data->status);
	   		$callback = array('code' => '1', 'message' => 'Attendance added!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /attendance/get', function(){
	if(!Flight::checkParams(array('date', 'class', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$attendance = $user->getAttendanceByDate(Flight::request()->data->date, Flight::request()->data->class);
	   		while($row = $attendance->fetch_assoc()){
	   			$callback[] = $row;
	   		}
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /attendance/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$today = $user->deleteAttendance(Flight::request()->data->id);
	   		$callback = array('code' => '1', 'message' => 'deleteAttendance deleted!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /class/add', function(){
	if(!Flight::checkParams(array('name', 'school', 'owner', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->addClass(Flight::request()->data->name, Flight::request()->data->school, Flight::request()->data->, Flight::request()->data->owner);
		   		$callback = array('code' => '1', 'message' => 'Class added!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /class/edit', function(){
	if(!Flight::checkParams(array('id', 'name', 'school', 'owner', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->editClass(Flight::request()->data->id, Flight::request()->data->name, Flight::request()->data->school, Flight::request()->data->, Flight::request()->data->owner);
		   		$callback = array('code' => '1', 'message' => 'Class edited!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /class/getbyschool', function(){
	if(!Flight::checkParams(array('school', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$callback = array();
				$school = $user->getClassBySchool(Flight::request()->data->school);
		        while($row = $school->fetch_assoc()){
		            $callback[] = $row;
		        }
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /class/getbyowner', function(){
	if(!Flight::checkParams(array('teacher', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$callback = array();
				$school = $user->getClassByOwner(Flight::request()->data->teacher);
		        $row = $school->fetch_array();
		        $callback[] = $row;
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /class/changeowner', function(){
	if(!Flight::checkParams(array('id', 'teacher', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$callback = array();
				$user->changeClassOwner(Flight::request()->data->id, Flight::request()->data->teacher);
				$callback = array('code' => '1', 'message' => 'Owner changed!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /class/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$callback = array();
				$user->deleteClass(Flight::request()->data->id);
				$callback = array('code' => '1', 'message' => 'Class deleted!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /mark/add', function(){
	if(!Flight::checkParams(array('teacher', 'type', 'mark', 'student', 'date', 'test', 'term', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->addMark(Flight::request()->data->teacher, Flight::request()->data->type, Flight::request()->data->mark, Flight::request()->data->student, Flight::request()->data->date, Flight::request()->data->test, Flight::request()->data->term);
	   		$callback = array('code' => '1', 'message' => 'Mark added!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /mark/edit', function(){
	if(!Flight::checkParams(array('id', 'mark', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->editMark(Flight::request()->data->id, Flight::request()->data->mark);
	   		$callback = array('code' => '1', 'message' => 'Mark editted!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /mark/getbystudent', function(){
	if(!Flight::checkParams(array('student', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$mark = $user->getMarkByStudent(Flight::request()->data->student);
	        while($row = $mark->fetch_assoc()){
	            $callback[] = $row;
	        }
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /mark/getbytest', function(){
	if(!Flight::checkParams(array('test', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$mark = $user->getMarkByTest(Flight::request()->data->test);
	        while($row = $mark->fetch_assoc()){
	            $callback[] = $row;
	        }
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /mark/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$user->deleteMark(Flight::request()->data->id);
			$callback = array('code' => '1', 'message' => 'Class deleted!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /notification/add', function(){
	if(!Flight::checkParams(array('date', 'content', 'status', 'teacher', 'student', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->addNotification(Flight::request()->data->date, Flight::request()->data->content, Flight::request()->data->status, Flight::request()->data->teacher, Flight::request()->data->student);
	   		$callback = array('code' => '1', 'message' => 'Notification added!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /notification/get', function(){
	if(!Flight::checkParams(array('student', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$noti = $user->getNotificationByStudent(Flight::request()->data->student);
	        while($row = $noti->fetch_assoc()){
	            $callback[] = $row;
	        }
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /notification/unread', function(){
	if(!Flight::checkParams(array('student', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$noti = $user->getNotificationByStudentUnread(Flight::request()->data->student);
	        while($row = $noti->fetch_assoc()){
	            $callback[] = $row;
	        }
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /notification/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$user->deleteNotification(Flight::request()->data->id);
			$callback = array('code' => '1', 'message' => 'Notification deleted!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /parent/add', function(){
	if(!Flight::checkParams(array('name', 'phone', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->addParent(Flight::request()->data->name, Flight::request()->data->phone);
		   		$callback = array('code' => '1', 'message' => 'Parent added!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /parent/edit', function(){
	if(!Flight::checkParams(array('id', 'name', 'phone', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->editParent(Flight::request()->data->id, Flight::request()->data->name, Flight::request()->data->phone);
		   		$callback = array('code' => '1', 'message' => 'Parent edited!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /parent/get', function(){
	if(!Flight::checkParams(array('student', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$parent = $user->getParentByStudent(Flight::request()->data->student);
	        while($row = $parent->fetch_assoc()){
	            $callback[] = $row;
	        }
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /parent/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$user->deleteParent(Flight::request()->data->id);
			$callback = array('code' => '1', 'message' => 'Parent deleted!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /schedule/add', function(){
	if(!Flight::checkParams(array('class', 'day', 'period', 'teacher', 'term', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->addSchedule(Flight::request()->data->class, Flight::request()->data->day, Flight::request()->data->period, Flight::request()->data->phone, Flight::request()->data->phone);
		   		$callback = array('code' => '1', 'message' => 'Schedule added!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /schedule/edit', function(){
	if(!Flight::checkParams(array('id', 'class', 'day', 'period', 'teacher', 'term', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->editSchedule(Flight::request()->data->id, Flight::request()->data->class, Flight::request()->data->day, Flight::request()->data->period, Flight::request()->data->phone, Flight::request()->data->phone);
		   		$callback = array('code' => '1', 'message' => 'Schedule edited!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /mark/getbyclass', function(){
	if(!Flight::checkParams(array('class', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$mark = $user->getScheduleByClass(Flight::request()->data->class);
	        while($row = $mark->fetch_assoc()){
	            $callback[] = $row;
	        }
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /mark/getbyteacher', function(){
	if(!Flight::checkParams(array('class', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$mark = $user->getScheduleByTeacher(Flight::request()->data->teacher);
	        while($row = $mark->fetch_assoc()){
	            $callback[] = $row;
	        }
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /school/add', function(){
	if(!Flight::checkParams(array('name', 'address', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->addSchool(Flight::request()->data->name, Flight::request()->data->address);
		   		$callback = array('code' => '1', 'message' => 'School added!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /school/edit', function(){
	if(!Flight::checkParams(array('id', 'name', 'address', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->editSchool(Flight::request()->data->id, Flight::request()->data->name, Flight::request()->data->address);
		   		$callback = array('code' => '1', 'message' => 'School edited!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /school/get', function(){
	if(!Flight::checkParams(array('id', 'name', 'address', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->editSchool(Flight::request()->data->id, Flight::request()->data->name, Flight::request()->data->address);
		   		while($row = $noti->fetch_assoc()){
	            	$callback[] = $row;
	        	}
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::start();
?>
