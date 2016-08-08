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
	   		$callback = array('code' => '1', 'message' => 'Attendance deleted!');
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
		   		$user->addClass(Flight::request()->data->name, Flight::request()->data->school, Flight::request()->data->owner);
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
		   		$user->editClass(Flight::request()->data->id, Flight::request()->data->name, Flight::request()->data->school, Flight::request()->data->owner);
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
		        $callback = $school->fetch_array();
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
	   		$callback = array('code' => '1', 'message' => 'Mark edited!');
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
			$callback = array('code' => '1', 'message' => 'Mark deleted!');
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
		   		$user->addSchedule(Flight::request()->data->class, Flight::request()->data->day, Flight::request()->data->period, Flight::request()->data->teacher, Flight::request()->data->term);
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
		   		$user->editSchedule(Flight::request()->data->id, Flight::request()->data->class, Flight::request()->data->day, Flight::request()->data->period, Flight::request()->data->teacher, Flight::request()->data->term);
		   		$callback = array('code' => '1', 'message' => 'Schedule edited!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /schedule/getbyclass', function(){
	if(!Flight::checkParams(array('class', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$schedule = $user->getScheduleByClass(Flight::request()->data->class);
	        while($row = $mark->fetch_assoc()){
	            $callback[] = $row;
	        }
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /schedule/getbyteacher', function(){
	if(!Flight::checkParams(array('teacher', 'token'))){
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
	if(!Flight::checkParams(array('token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$school = $user->getSchools();
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

Flight::route('POST /school/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->deleteSchool(Flight::request()->data->id);
		   		$callback = array('code' => '1', 'message' => 'School deleted!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /student/add', function(){
	if(!Flight::checkParams(array('name', 'address', 'class', 'dad', 'mom', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->addStudent(Flight::request()->data->name, Flight::request()->data->address, Flight::request()->data->class, Flight::request()->data->dad, Flight::request()->data->mom);
		   		$callback = array('code' => '1', 'message' => 'Student added!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /student/edit', function(){
	if(!Flight::checkParams(array('id', 'name', 'address', 'class', 'dad', 'mom', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->editStudent(Flight::request()->data->id, Flight::request()->data->name, Flight::request()->data->address, Flight::request()->data->class, Flight::request()->data->dad, Flight::request()->data->mom);
		   		$callback = array('code' => '1', 'message' => 'Student edited!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /student/get', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$student = $user->getStudent(Flight::request()->data->id);
	        $callback = $student->fetch_array();
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /student/getbyclass', function(){
	if(!Flight::checkParams(array('class', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
			$student = $user->getStudentByClass(Flight::request()->data->class);
	        while($row = $student->fetch_assoc()){
	            $callback[] = $row;
	        }
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /student/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->deleteStudent(Flight::request()->data->id);
		   		$callback = array('code' => '1', 'message' => 'Student deleted!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /subject/add', function(){
	if(!Flight::checkParams(array('name', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->addSubject(Flight::request()->data->name);
		   		$callback = array('code' => '1', 'message' => 'Subject added!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /subject/edit', function(){
	if(!Flight::checkParams(array('id', 'name', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->editSubject(Flight::request()->data->id, Flight::request()->data->name);
		   		$callback = array('code' => '1', 'message' => 'Subject edited!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /subject/get', function(){
	if(!Flight::checkParams(array('token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$subjects = $user->getSubjects();
		   		$callback = array();
		   		while($row = $subjects->fetch_assoc()){
		   			$callback[] = $row;
		   		}
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /subject/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->deleteSubject(Flight::request()->data->id);
		   		$callback = array('code' => '1', 'message' => 'Subject deleted!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /teacher/add', function(){
	if(!Flight::checkParams(array('name', 'address', 'phone', 'type', 'subject', 'school', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->addTeacher(Flight::request()->data->name, Flight::request()->data->address, Flight::request()->data->phone, Flight::request()->data->type, Flight::request()->data->subject, Flight::request()->data->school);
		   		$callback = array('code' => '1', 'message' => 'Teacher added!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /teacher/edit', function(){
	if(!Flight::checkParams(array('id', 'name', 'address', 'phone', 'type', 'subject', 'school', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->editTeacher(Flight::request()->data->id, Flight::request()->data->name, Flight::request()->data->address, Flight::request()->data->phone, Flight::request()->data->type, Flight::request()->data->subject, Flight::request()->data->school);
		   		$callback = array('code' => '1', 'message' => 'Teacher edited!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /teacher/get', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$teacher = $user->getTeacher(Flight::request()->data->id);
		   		$callback = $teacher->fetch_array();
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /teacher/getbyschool', function(){
	if(!Flight::checkParams(array('school', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$teacher = $user->getTeacherBySchool(Flight::request()->data->school);
		   		$callback = array();
		   		while($row = $teacher->fetch_assoc()){
		   			$callback[] = $row;
		   		}
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /teacher/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->deleteTeacher(Flight::request()->data->id);
		   		$callback = array('code' => '1', 'message' => 'Teacher deleted!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /term/add', function(){
	if(!Flight::checkParams(array('name', 'year', 'school', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->addTerm(Flight::request()->data->name, Flight::request()->data->year, Flight::request()->data->school, '0');
		   		$callback = array('code' => '1', 'message' => 'Term added!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /term/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->deleteTerm(Flight::request()->data->id);
		   		$callback = array('code' => '1', 'message' => 'Term deleted!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /test/add', function(){
	if(!Flight::checkParams(array('date', 'class', 'teacher', 'type', 'term', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->addTest(Flight::request()->data->date, Flight::request()->data->class, Flight::request()->data->teacher, Flight::request()->data->type, Flight::request()->data->term);
	   		$callback = array('code' => '1', 'message' => 'Test added!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /test/edit', function(){
	if(!Flight::checkParams(array('id', 'date', 'class', 'teacher', 'type', 'term', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->editTest(Flight::request()->data->id, Flight::request()->data->date, Flight::request()->data->class, Flight::request()->data->teacher, Flight::request()->data->type, Flight::request()->data->term);
	   		$callback = array('code' => '1', 'message' => 'Test edited!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /test/get', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$test = $user->getTest(Flight::request()->data->id);
		   		$callback = $test->fetch_array();
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /test/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
		   		$user->deleteTest(Flight::request()->data->id);
		   		$callback = array('code' => '1', 'message' => 'Test deleted!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /user/add', function(){
	if(!Flight::checkParams(array('username', 'password', 'type', 'user', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
			    if(Flight::request()->data->type == 3)
			    	$user->addUser(Flight::request()->data->username, Flight::request()->data->password, Flight::request()->data->type,  0);
			    else
			    	$user->addUser(Flight::request()->data->username, Flight::request()->data->password, Flight::request()->data->type,  Flight::request()->data->user);
		   		$callback = array('code' => '1', 'message' => 'User added!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /user/changepassword', function(){
	if(!Flight::checkParams(array('id', 'password', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
			    $user->editPassword(Flight::request()->data->id, Flight::request()->data->password);
		   		$callback = array('code' => '1', 'message' => 'Password changed!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /user/delete', function(){
	if(!Flight::checkParams(array('id', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			if(Flight::checkPermission(Flight::request()->data->token)){
		   		$user = Flight::get('models');
			    $user->deleteUser(Flight::request()->data->id);
		   		$callback = array('code' => '1', 'message' => 'User deleted!');
			} else
				$callback = array('code' => '2', 'message' => 'Admin permission require!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});
Flight::start();
?>
