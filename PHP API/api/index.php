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
Flight::set('api', $API_KEY);
Flight::map('checkParams', function($require){
   	$error = true;
	foreach ($require as $key){
		if(!isset(Flight::request()->data->$key) || strlen(Flight::request()->data->$key) == 0)
			$error = false;
	}
	return $error;
});
Flight::map('sendFCM', function($token, $title, $message, $type){
	//Getting registration token we have to make it as array 
	$reg_token = array($token);
	
	
	//Creating a message array 
	$msg = array
	(
		'message' 	=> $message,
		'title'		=> $title,
		'subtitle'	=> 'Android Push Notification using GCM Demo',
		'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
		'vibrate'	=> 1,
		'sound'		=> 1,
		'largeIcon'	=> 'large_icon',
		'smallIcon'	=> 'small_icon',
		'type'=> $type
	);
	
	//Creating a new array fileds and adding the msg array and registration token array here 
	$fields = array
	(
		'registration_ids' 	=> $reg_token,
		'data'			=> $msg
	);
	
	//Adding the api key in one more array header 
	$headers = array
	(
		'Authorization: key=' . Flight::get('api'),
		'Content-Type: application/json'
	); 
	
	//Using curl to perform http request 
	$ch = curl_init();
	curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
	curl_setopt( $ch,CURLOPT_POST, true );
	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	
	//Getting the result 
	$result = curl_exec($ch );
	curl_close( $ch );
	
	//Decoding json from result 
	$res = json_decode($result);

	
	//Getting value from success 
	$flag = $res->success;
	
	//if success is 1 means message is sent 
	if($flag == 1){
		//Redirecting back to our form with a request success 
		return true;
	}else{
		//Redirecting back to our form with a request failure 
		return false;
	}
});

Flight::map('checkToken', function($token){
	$user = Flight::get('models');
	return $user->checkToken($token);
});

Flight::map('getUser', function($token){
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
	    		$callback = array('code' => '0', 'message' => 'Sai Số điện thoại hoặc Mật khẩu');
	    	} else {
	    		$token = $user->addSession(Flight::request()->data->username);
	    		$info = $user->getUserByUsername(Flight::request()->data->username); $info = $info->fetch_array();
	    		$callback = array('code' => '1', 'message' => 'Login successful', 'token' => $token, 'type' => $info['type'], 'id'=>$info['user']);
	    	}
	    Flight::json($callback);
	}
});

Flight::route('POST /logout', function(){
	if(!Flight::checkParams(array('token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->logout(Flight::request()->data->token);
	   		$callback = array('code' => '1', 'message' => 'Done!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});


Flight::route('POST /ask/add', function(){
	if(!Flight::checkParams(array('content', 'date', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->addAsk(Flight::getUser(Flight::request()->data->token), Flight::request()->data->content, Flight::request()->data->date);
	   		$callback = array('code' => '1', 'message' => 'Gửi xin phép nghỉ thành công');
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
	if(!Flight::checkParams(array('student', 'status', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$id = Flight::getUser(Flight::request()->data->token);
	   		$user->addAttendance(Flight::request()->data->student, date("Y-m-d"), $id, Flight::request()->data->status);
	   		if(Flight::request()->data->status == 0){
	   			$user->addNotification("Nghỉ học không phép ngày " . date("Y-m-d"), 1, $id, Flight::request()->data->student);
	   			$result = Flight::sendFCM($user->getFCMToken(Flight::request()->data->student), "Điểm danh hằng ngày", "Học sinh nghỉ học không phép ngày " . date("Y-m-d"), "2");
	   		}
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

Flight::route('POST /attendance/getClass', function(){
	if(!Flight::checkParams(array('token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			$id = Flight::getUser(Flight::request()->data->token);
		   	$user = Flight::get('models');
		   	$class = array();
		   	$listClass = $user->getScheduleClassByTeacher($id);
		   	while($row = $listClass->fetch_assoc()){
		   		$cl = $user->getClass($row['class'])->fetch_array();
		   		$cla = array("id"=>$cl['id'], "name" => $cl['name']);
		   		$student = $user->getStudentByClass($row['class']);
		   		while($row2 = $student->fetch_assoc()){
		   			if($user->checkAsk($row2['id'])){
		   				$ask = $user->getAskToday($row2['id'])->fetch_array();
		   				$row2['type'] = '2';
		   				$row2['reason'] = $ask['content'];
		   			} else {
		   				$row2['type'] = '1';
		   				$row2['reason'] = $ask['content'];
		   			}
		   			$cla['student'][] = $row2;
		   		}
		   		$class[] = $cla;
		   	}
		   	$callback = array('code' => '1', 'message' => 'Get classes successful', 'class' => $class);
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
	if(!Flight::checkParams(array('type', 'mark', 'student', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error1');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->addMark(Flight::getUser(Flight::request()->data->token), Flight::request()->data->type, Flight::request()->data->mark, Flight::request()->data->student, date("Y-m-d"), Flight::request()->data->test);
	   		$subject = $user->getSubjectByTeacher(Flight::getUser(Flight::request()->data->token));
	   		$result = Flight::sendFCM($user->getFCMToken(Flight::request()->data->student), "Điểm mới", "Học sinh vừa được 1 điểm ".Flight::request()->data->mark." môn ".$subject, "1");
	   		$callback = array('code' => '1', 'message' => 'Đã thêm điểm!');
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
	if(!Flight::checkParams(array('content', 'status', 'teacher', 'student', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->addNotification(Flight::request()->data->content, Flight::request()->data->status, Flight::request()->data->teacher, Flight::request()->data->student);
	   		$callback = array('code' => '1', 'message' => 'Notification added!');
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /notification/get', function(){
	if(!Flight::checkParams(array('token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
	   		$id = Flight::getUser(Flight::request()->data->token);
			$noti = $user->getNotificationByStudent($id);
			$callback = array('code'=>'1', 'total'=>$noti->num_rows);
			$i=0;
	        while($row = $noti->fetch_assoc()){
	        	$teacher = $user->getTeacher($row['teacher'])->fetch_array();
	        	$row['teacher'] = $teacher['name'];
	            $callback['notification'][$i] = $row;
	            $i++;
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

Flight::route('POST /student/getInfo', function(){
	if(!Flight::checkParams(array('token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->addFCMToken(Flight::request()->data->token, Flight::request()->data->FCMToken);
	   		$callback = array();
	   		$id = Flight::getUser(Flight::request()->data->token);
			$student = $user->getStudent($id)->fetch_array();
	        $dad = $user->getParent($student['dad'])->fetch_array();
	        $mom = $user->getParent($student['mom'])->fetch_array();
	        $class = $user->getClass($student['class'])->fetch_array();
	        $school = $user->getSchool($class['school'])->fetch_array();
	        $schedule = $user->getScheduleByClass($student['class']);
	        $student['class'] = $class['name'];
	        $student['school'] = $school['name'];
	        $mark = array();
	        $subjects = $user->getSubjects();
	        $i=0;
	        while ($subject = $subjects->fetch_assoc()){
	        	$mos = $user->getMarkByStudentAndSubject($id, $subject['id']);
	        	$hs1 = "";
	        	$hs2 = "";
	        	$hs3 = "";
	        	if($mos->num_rows > 0)
	        		while($m = $mos->fetch_assoc()){
	        			switch ($m['type']){
	        				case 1:
	        					$hs1 .= $m['mark']." "; break;
	        				case 2: 
	        					$hs2 .= $m['mark']." "; break;
	        				case 3:
	        					$hs3 .= $m['mark']." "; break;
	        			}
	        		}
	        	$mark[$i]['name'] = $subject['name'];
	        	$mark[$i]['hs1'] = $hs1;
	        	$mark[$i]['hs2'] = $hs2;
	        	$mark[$i]['hs3'] = $hs3;
	        	$i++;
	        }

	        $callback = array('code' => '1', 'message'=>'Info get successful');
	        $callback['student'] = $student;
	        $callback['dad'] = $dad;
	        $callback['mom'] = $mom;
	        $callback['schedule'] = $schedule;
	        $callback['mark'] = $mark;
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


Flight::route('POST /teacher/getInfo', function(){
	if(!Flight::checkParams(array('token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$callback = array();
	   		$id = Flight::getUser(Flight::request()->data->token);
			$teacher = $user->getTeacher($id)->fetch_array();
	        $school = $user->getSchool($teacher['school'])->fetch_array();
	        $schedule = $user->getScheduleByTeacher($id);
	        $subject = $user->getSubjectByTeacher($id);
	        $teacher['subject'] = $subject;
	        $teacher['school'] = $school['name'];
	        if($teacher['type'] == 1)
	        	$teacher['type'] = "Giáo viên bộ môn";
	        else {
	        	$class = $user->getClassByOwner($id)->fetch_array();
	        	$teacher['type'] = "Giáo viên chủ nhiệm lớp " . $class['name'];
	        }
		   	
	        $callback = array('code' => '1', 'message'=>'Info get successful');
	        $callback['teacher'] = $teacher;
	        $callback['schedule'] = $schedule;
	        $class = array();
		   	$listClass = $user->getScheduleClassByTeacher($id);
		   	while($row = $listClass->fetch_assoc()){
		   		$cl = $user->getClass($row['class'])->fetch_array();
		   		$cla = array("id"=>$cl['id'], "name" => $cl['name']);
		   		$student = $user->getStudentByClass($row['class']);
		   		$class[] = $cla;
		   	}
		   	$callback['myClass'] = $class;
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});


Flight::route('POST /teacher/getMyClass', function(){
	if(!Flight::checkParams(array('token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
			$id = Flight::getUser(Flight::request()->data->token);
		   	$user = Flight::get('models');
		   	$class = array();
		   	$listClass = $user->getScheduleClassByTeacher($id);
		   	while($row = $listClass->fetch_assoc()){
		   		$cl = $user->getClass($row['class'])->fetch_array();
		   		$cla = array("id"=>$cl['id'], "name" => $cl['name']);
		   		$student = $user->getStudentByClass($row['class']);
		   		while($row2 = $student->fetch_assoc())
		   			$cla['student'][] = $row2;
		   		$class[] = $cla;
		   	}
		   	$callback = array('code' => '1', 'message' => 'Get classes successful', 'class' => $class);
		} else
			$callback = array('code' => '2', 'message' => 'Wrong token access!');
	    Flight::json($callback);
	}
});

Flight::route('POST /teacher/addNotification', function(){
	if(!Flight::checkParams(array('content', 'student', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->addNotification(Flight::request()->data->content, '1', Flight::getUser(Flight::request()->data->token), Flight::request()->data->student);
	   		$teacher = $user->getTeacher(Flight::getUser(Flight::request()->data->token))->fetch_array();
	   		$result = Flight::sendFCM($user->getFCMToken(Flight::request()->data->student), "Thông báo từ giáo viên ".$teacher['name'],Flight::request()->data->content, "0");
	   		$callback = array('code' => '1', 'message' => "Gửi thông báo thành công!");
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
	if(!Flight::checkParams(array('date', 'class', 'teacher', 'type', 'token'))){
		$callback = array('code' => '0', 'message' => 'Error');
    	Flight::json($callback);
	} else {
		if(Flight::checkToken(Flight::request()->data->token)){
	   		$user = Flight::get('models');
	   		$user->addTest(Flight::request()->data->date, Flight::request()->data->class, Flight::request()->data->teacher, Flight::request()->data->type);
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
$db->mysqli_close();

?>
