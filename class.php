<?php
class Ask{

	//DB connection var
	public $db;

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addAsk($student, $content, $date){
		$this->db->query("INSERT INTO `ask` (`student`, `content`, `date`) VALUES ('$student', '$content', '$date') ");
	}

	public function getAsk($id){
		$getAsk = $this->db->query("SELECT * FROM `ask` WHERE `id` = '$id'");
		return $getAsk;
	}

	public function getAsks($student){
		$getAsks = $this->db->query("SELECT * FROM `ask` WHERE `student` = '$student'");
		return $getAsks;
	}

	public function deleteAsk($id){
		$this->db->query("DELETE FROM `ask` WHERE `id` = '$id'");
	}
}

class Attendance{

	//DB connection var
	public $db;
	
	//status
	// {0} - Nghi hoc
	// {1} - Di hoc 

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addAttendance($student, $date, $teacher, $status){
		$this->db->query("INSERT INTO `attendance` (`student`, `date`, `teacher`, `status`) VALUES ('$student', '$date', '$teacher', '$status') ");
	}

	public function getAttendance($id){
		$getAttendance = $this->db->query("SELECT * FROM `attendance` WHERE `id` = '$id'");
		return $getAttendance;
	}

	public function getAttendanceByDate($date){
		$getAttendanceByDate = $this->db->query("SELECT * FROM `attendance` WHERE `date` = '$date'");
		return $getAttendanceByDate;
	}

	public function getAttendanceByStudent($student){
		$getAttendanceByStudent = $this->db->query("SELECT * FROM `attendance` WHERE `student` = '$student'");
		return $getAttendanceByStudent;
	}

	public function deleteAttendance($id){
		$this->db->query("DELETE FROM `attendance` WHERE `id` = '$id'");
	}

}

class Classes{

	//DB connection var
	public $db;

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addClass($name, $school, $owner){
		$this->db->query("INSERT INTO `class` (`name`, `school`, `owner`) VALUES ('$name', '$school', '$owner') ");
	}

	public function getClass($id){
		$getClass = $this->db->query("SELECT * FROM `class` WHERE `id` = '$id'");
		return $getClass;
	}

	public function editClass($id, $name, $school, $owner){
		$this->db->query("UPDATE `class` SET `name` = '$name', `school` = '$school', `owner` = '$owner' WHERE `id` = '$id' ");
	}

	public function getClassBySchool($school){
		$getClassBySchool = $this->db->query("SELECT * FROM `class` WHERE `school` = '$school'");
		return $getClassBySchool;
	}

	public function getClassByOwner($school){
		$getClassByOwner = $this->db->query("SELECT * FROM `class` WHERE `owner` = '$owner'");
		return $getClassByOwner;
	}

	public function changeOwner($id, $teacher){
		$this->db->query("UPDATE `class` SET `owner` = '$teacher' WHERE `id` = '$id'");
	}

	public function deleteClass($id){
		$this->db->query("DELETE FROM `class` WHERE `id` = '$id'");
	}

}

class Mark{

	//DB connection var
	public $db;

	//type
	// {1} - Ktra mieng
	// {2} - Ktra 15'
	// {3} - Ktra 1 tiet
	// {4} - Ktra giua ky
	// {5} - Ktra cuoi ky

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addMark($teachet, $type, $mark, $student, $date, $test, $term){
		$this->db->query("INSERT INTO `mark` (`student`, `date`, `type`, `mark`, `teacher`, `test`, `term`) VALUES ('$student', '$date', '$type', '$mark', '$teacher', '$test', '$term') ");
	}

	public function getMark($id){
		$getMark = $this->db->query("SELECT * FROM `mark` WHERE `id` = '$id' ");
		return $getMark;
	}

	public function getMarkByStudent($student){
		$getMark = $this->db->query("SELECT * FROM `mark` WHERE `id` = '$id' ");
		return $getMark;
	}

	public function getMarkByTest($test){
		$getMark = $this->db->query("SELECT * FROM `mark` WHERE `test` = '$test' ");
		return $getMark;
	}

	public function editMark($id, $mark){
		$this->db->query("UPDATE `mark` SET `mark` = '$mark' WHERE `id` = '$id' ");
	}

	public function deleteMark($id){
		$this->db->query("DELETE FROM `mark` WHERE `id` = '$id' ");
	}
}

class Notification{

	//DB connection var
	public $db;

	//Status
	// {0} - Unread
	// {1} - Read

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addNotification($date, $content, $status, $teacher, $student){
		$this->db->query("INSERT INTO `` (`date`, `content`, `status`, `teacher`, `student`) VALUES ('$date', '$content', '$status', '$teacher', '$student') ");
	}

	public function getNotification($id){
		$getNotification = $this->db->query("SELECT * FROM `notification` WHERE `id` = '$id'");
		return $getNotification;
	}

	public function getNotificationByStudent($student){
		$getNotificationByStudent = $this->db->query("SELECT * FROM `notification` WHERE `student` = '$student'");
		return $getNotificationByStudent;
	}

	public function getNotificationByStudentUnread($student){
		$getNotificationByStudentUnread = $this->db->query("SELECT * FROM `notification` WHERE `student` = '$student' AND `status` = '0' ");
		return $getNotificationByStudentUnread;
	}

	public function deleteNotification($id){
		$this->db->query("DELETE FROM `notification` WHERE `id` = '$id'");
	}
}

class Parents{

	//DB connection var
	public $db;

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addParent($name, $phone){
		$this->db->query("INSERT INTO `parent` (`name`, `phone`) VALUES ('$name', '$phone') ");
	}

	public function editParent($id, $name, $phone){
		$this->db->query("UPDATE `parent` SET `name` = '$name', `phone` = '$phone' WHERE `id` = '$id' ");
	}

	public function getParent($id){
		$getParent = $this->db->query("SELECT * FROM `parent` WHERE `id` = '$id'");
		return $getParent;
	}

	public function getParentByStudent($student){
		$getParentByStudent = $this->db->query("SELECT * FROM `parent` WHERE `student` = '$student'");
		return $getParentByStudent;
	}

	public function deleteParent($id){
		$this->db->query("DELETE FROM `parent` WHERE `id` = '$id'");
	}

	public function deleteParentByStudent($student){
		$this->db->query("DELETE FROM `parent` WHERE `student` = '$student'");
	}
}

class Schedule{

	//DB connection var
	public $db;

	//Day
	// {2} - Monday
	// {3} - Tuesday
	// ...
	// {8} - Sunday

	//Period
	// {1} -> {10} - period 1->10

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addSchedule($class, $day, $period, $teacher, $term){
		$this->db->query("INSERT INTO `schedule` (`class`, `day`, `period`, `teacher`, `term`) VALUES ('$class', '$day', '$period', '$teacher', '$term') ");
	}

	public function editSchedule($id, $class, $day, $period, $teacher, $term){
		$this->db->query("UPDATE `schedule` SET `class` = '$class', `day` = '$day', `period` = '$period', `teacher` = '$teacher', `term` = '$term' WHERE `id` = '$id' ");
	}

	public function getSchedule($id){
		$getSchedule = $this->db->query("SELECT * FROM `schedule` WHERE `id` = '$id'");
		return $getSchedule;
	}

	public function getScheduleByClass($class, $term){
		$getSchedule = $this->db->query("SELECT * FROM `schedule` WHERE `class` = '$class' AND `term` = '$term' ");
		return $getScheduleByClass;
	}

	public function getScheduleByTeacher($teacher, $term){
		$getScheduleByTeacher = $this->db->query("SELECT * FROM `schedule` WHERE `teacher` = '$teacher' AND `term` = '$term' ");
		return $getScheduleByTeacher;
	}

	public function deleteSchedule($id){
		$this->db->query("DELETE FROM `schedule` WHERE `id` = '$id'");
	}
}

class School{

	//DB connection var
	public $db;

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addSchool($name, $address){
		$this->db->query("INSERT INTO `school` (`name`, `address`) VALUES ('$name', '$address') ");
	}

	public function editSchool($id, $name, $address){
		$this->db->query("UPDATE `school` SET `name` = '$name', `address` = '$address' WHERE `id` = '$id' "); 
	}

	public function getSchool($id){
		$getSchool = $this->db->query("SELECT * FROM `school` WHERE `id` = '$id'");
		return $getSchool;
	}

	public function deleteSchool($id){
		$this->db->query("DELETE FROM `school` WHERE `id` = '$id'");
	}
}

class Session{

	//DB connection var
	public $db;

	public function __construct($conn){
		$this->db = $conn;
	}

	public function clearSession(){
		$getSession = $this->db->query("SELECT * FROM `session` WHERE 1");
		while($row = $getSession->fetch_asscoc()){
			$timeNow = date("Y-m-d H:i:s");
			$diff = $row['date']->diff($timeNow);
			if($diff->d >= 1){
				$this->deleteSession($row['id']);
			}
		}
	}

	public function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	public function addSession($user){
		$randomString = $this->generateRandomString();
		$timeNow = date("Y-m-d H:i:s");
		$this->db->query("INSERT INTO `session` (`token`, `date`, `user`) VALUES ('".md5($randomString)."', '$timeNow', '$user') ");
	}

	public function refreshSession($id){
		$timeNow = date("Y-m-d H:i:s");
		$this->db->query("UPDATE `session` SET `date` = '$timeNow' WHERE `id` = '$id'");
	}

	public function checkToken($token){
		$checkToken = $this->db->query("SELECT * FROM `session` WHERE `token` = '$token'");
		if($checkToken->num_rows == 0){
			return false;
		} else {
			$timeNow = date("Y-m-d H:i:s");
			$row = $checkToken->fetch_array();
			$diff = $row['date']->diff($timeNow);
			if($diff->d >= 1){
				$this->deleteSession($row['id']);
				return false;
			} else {
				$this->refreshSession($row['id']);
				return true;
			}
		}
	}

	public function getSessionUser($token){
		$getSessionUser = $this->db->query("SELECT * FROM `session` WHERE `token` = '$token'");
		$row = $getSessionUser->fetch_array();
		return $row['user'];
	}

	public function deleteSession($id){
		$this->db->query("DELETE FROM `session` WHERE `id` = '$id'");
	}
}

class Student{

	//DB connection var
	public $db;

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addStudent($name, $address, $class, $dad, $mom){
		$this->db->query("INSERT INTO `student` (`name`, `address`, `class`, `dad`, `mom`) VALUES ('$name', '$address', '$class', '$dad', '$mom') ");
	}

	public function editStudent($id, $name, $address, $class, $dad, $mom){
		$this->db->query("UPDATE `student` SET `name` = '$name', `address` = '$address', `class` = '$class', `dad` = '$dad', `mom` = '$mom' WHERE `id` = '$id'");
	}

	public function getStudent($id){
		$getStudent = $this->db->query("SELECT * FROM `student` WHERE `id` = '$id'");
		return $getStudent;
	}

	public function getStudentByClass($class){
		$getStudentByClass = $this->db->query("SELECT * FROM `student` WHERE `class` = '$class'");
		return $getStudentByClass;
	}

	public function deleteStudent($id){
		$this->db->query("DELETE FROM `student` WHERE `id` = '$id'");
	}
}

class Subject{

	//DB connection var
	public $db;

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addSubject($name){
		$this->db->query("INSERT INTO `subject` (`name`) VALUES ('$name') ");
	}

	public function edit($id, $name){
		$this->db->query("UPDATE `subject` SET `name` = '$name' WHERE `id` = '$id'");
	}

	public function getSubject($id){
		$getSubject = $this->db->query("SELECT * FROM `subject` WHERE `id` = '$id'");
		$row = $getSubject->fetch_array();
		return $row['name'];
	}

	public function delete($id){
		$this->db->query("DELETE FROM `subject` WHERE `id` = '$id'");
	}
}

class Teacher{

	//DB connection var
	public $db;

	//Type
	// {1} - GV bo mon
	// {2} - GV chu nhiem

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addTeacher($name, $address, $phone, $type, $subject, $school){
		$this->db->query("INSERT INTO `teacher` (`name`, `address`, `phone`, `type`, `subject`, `school`) VALUES ('$name', '$address', '$phone', '$type', '$subject', '$school') ");
	}

	public function editTeacher($id, $name, $address, $phone, $type, $subject, $school){
		$this->db->query("UPDATE `teacher` SET `name` = '$name', `address` = '$address', `phone` = '$phone', `type` = '$type', `subject` = '$subject', `school` = '$school' WHERE `id` = '$id'");
	}

	public function getTeacher($id){
		$getTeacher = $this->db->query("SELECT * FROM teacher`` WHERE `id` = '$id'");
		return $getTeacher;
	}

	public function getTeacherBySchool($school){
		$getTeacherBySchool = $this->db->query("SELECT * FROM `teacher` WHERE `school` = '$school'");
		return $getTeacherBySchool;
	}

	public function delete($id){
		$this->db->query("DELETE FROM `` WHERE `id` = '$id'");
	}
}

class Term{

	//DB connection var
	public $db;

	//Current
	// {0} - Not current term
	// {1} - Current term

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addTerm($name, $year, $school, $current){
		$this->db->query("INSERT INTO `term` (`name`, `year`, `school`, `current`) VALUES ('$name', '$year', '$school', '$current') ");
	}

	public function getTerm($id){
		$getTerm = $this->db->query("SELECT * FROM `term` WHERE `id` = '$id'");
		return $getTerm;
	}

	public function getTermBySchool($school){
		$getTermBySchool = $this->db->query("SELECT * FROM `term` WHERE `school` = '$school'");
		return $getTermBySchool;
	}

	public function getCurrentTerm($school){
		$getCurrentTerm = $this->db->query("SELECT * FROM `term` WHERE `school` = '$school' AND `current` = '1' ");
		return $getCurrentTerm;
	}

	public function setCurrentTerm($school, $id){
		$this->db->query("UPDATE `term` SET `current` = '0' WHERE `school` = '$school'");
		$this->db->query("UPDATE `term` SET `current` = '1' WHERE `id` = '$id'");
	}

	public function deleteTerm($id){
		$this->db->query("DELETE FROM `term` WHERE `id` = '$id'");
	}
}

class Test{

	//DB connection var
	public $db;

	//type
	// {2} - Ktra 15'
	// {3} - Ktra 1 tiet
	// {4} - Ktra giua ky
	// {5} - Ktra cuoi ky

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addTest($date, $class, $teacher, $type, $term){
		$this->db->query("INSERT INTO `test` (`date`, `class`, `teacher`, `type`, `term`) VALUES ('$date', '$class', '$teacher', '$type', '$term') ");
	}

	public function editTest($id, $date, $class, $teacher, $type, $term){
		$this->db->query("UPDATE `test` SET `date` = '$date', `class` =  '$class', `teacher` = '$teacher', `type` = '$type', `term` = '$term' WHERE `id` = '$id'");
	}

	public function getTest($id){
		$getTest = $this->db->query("SELECT * FROM `test` WHERE `id` = '$id'");
		return $getTest;
	}

	public function deleteTest($id){
		$this->db->query("DELETE FROM `test` WHERE `id` = '$id'");
	}
}

class User{

	//DB connection var
	public $db;

	//type
	// {1} - Teacher
	// {2} - Parent
	// {3} - Admin

	public function __construct($conn){
		$this->db = $conn;
	}

	public function addUser($username, $password, $type, $user){
		$this->db->query("INSERT INTO `user` (`username`, `password`, `type`, `user`) VALUES ('$username', '$password', '$type', '$user') ");
	}

	public function editPassword($id, $password){
		$this->db->query("UPDATE `user` SET `password` = '$password' WHERE `id` = '$id'");
	}

	public function getUser($id){
		$getUser = $this->db->query("SELECT * FROM `user` WHERE `id` = '$id'");
		return $getUser;
	}

	public function getUserByUsername($username){
		$getUserByUsername = $this->db->query("SELECT * FROM `user` WHERE `username` = '$username'");
		return $getUserByUsername;
	}

	public function checkPassword($username, $password){
		$user = $this->getUserByUsername($username);
		$row = $user->fetch_array();
		if($password == $row['password'])
			return true;
		else
			return false; 
	}

	public function deleteUser($id){
		$this->db->query("DELETE FROM `user` WHERE `id` = '$id'");
	}
}

?>