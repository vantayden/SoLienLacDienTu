<?php

class Models{

	//DB connection var
	public $db;

	public function __construct($conn){
		$this->db = $conn;
	}

	//Asks

		public function addAsk($student, $content, $date){			
			$date = date_create($date);
			$date = date_format($date, "Y-m-d");
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

		public function checkAsk($student){
			$timenow = date("Y-m-d");
			$checkAsk = $this->db->query("SELECT * FROM `ask` WHERE `student` = '$student' AND `date` = '$timenow'");
			if($checkAsk->num_rows == 0)
				return false;
			else 
				return true;
		}

		public function getAskToday($student){
			$timenow = date("Y-m-d");
			$getAskToday = $this->db->query("SELECT * FROM `ask` WHERE `student` = '$student' AND `date` = '$timenow'");
			return $getAskToday;
		}

		public function deleteAsk($id){
			$this->db->query("DELETE FROM `ask` WHERE `id` = '$id'");
		}

	//Attendances

	//status
	// {0} - Nghi hoc
	// {1} - Di hoc 
	// {2} - Co phep

		public function addAttendance($student, $date, $teacher, $status){
			$this->db->query("INSERT INTO `attendance` (`student`, `date`, `teacher`, `status`) VALUES ('$student', '$date', '$teacher', '$status') ");
		}

		public function getAttendance($id){
			$getAttendance = $this->db->query("SELECT * FROM `attendance` WHERE `id` = '$id'");
			return $getAttendance;
		}

		public function getAttendanceByDate($date, $class){
			$date = date_create($date)->format('Y-d-m');
			$teacher = $this->getFirstTeacher($date, $class);
			$getAttendanceByDate = $this->db->query("SELECT * FROM `attendance` WHERE `date` = '$date' AND 'teacher' = '$teacher'");
			return $getAttendanceByDate;
		}

		public function getAttendanceByStudent($student){
			$student = $this->getStudent($student)->fetch_array();
			$term = $this->getCurrentTermByClass($student['class']);
			$getAttendanceByStudent = $this->db->query("SELECT * FROM `attendance` WHERE `student` = '".$student['id']." AND `term` = '$term'");
			return $getAttendanceByStudent;
		}

		public function deleteAttendance($id){
			$this->db->query("DELETE FROM `attendance` WHERE `id` = '$id'");
		}

	//Classes

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

		public function getClassByOwner($owner){
			$getClassByOwner = $this->db->query("SELECT * FROM `class` WHERE `owner` = '$owner'");
			return $getClassByOwner;
		}

		public function changeClassOwner($id, $teacher){
			$this->db->query("UPDATE `class` SET `owner` = '$teacher' WHERE `id` = '$id'");
		}

		public function deleteClass($id){
			$this->db->query("DELETE FROM `class` WHERE `id` = '$id'");
		}

	//Marks

	//type
	// {1} - Ktra mieng
	// {2} - Ktra 15'
	// {3} - Ktra 1 tiet
	// {4} - Ktra giua ky
	// {5} - Ktra cuoi ky

		public function addMark($teacher, $type, $mark, $student, $date, $test = null){
			$student = $this->getStudent($student)->fetch_array();
			$term = $this->getCurrentTermByClass($student['class']);
			$this->db->query("INSERT INTO `mark` (`student`, `date`, `type`, `mark`, `teacher`, `test`, `term`) VALUES ('".$student['id']."', '$date', '$type', '$mark', '$teacher', '$test', '$term') ");
		}

		public function getMark($id){
			$getMark = $this->db->query("SELECT * FROM `mark` WHERE `id` = '$id' ");
			return $getMark;
		}

		public function getMarkByStudent($student){
			$student = $this->getStudent($student)->fetch_array();
			$term = $this->getCurrentTermByClass($student['class']);
			$getMark = $this->db->query("SELECT * FROM `mark` WHERE `student` = '".$student['id']."' AND `term` = '$term'");
			return $getMark;
		}

		public function getMarkByTest($test){
			$getMark = $this->db->query("SELECT * FROM `mark` WHERE `test` = '$test' ");
			return $getMark;
		}

		public function getMarkByStudentAndSubject($student, $subject){
			$student = $this->getStudent($student)->fetch_array();
			$term = $this->getCurrentTermByClass($student['class']);
			$getMark = $this->db->query("SELECT * FROM `mark` WHERE `student` = '".$student['id']."' AND `teacher` = '$subject' AND `term` = '$term'");
			return $getMark;
		}

		public function editMark($id, $mark){
			$this->db->query("UPDATE `mark` SET `mark` = '$mark' WHERE `id` = '$id' ");
		}

		public function deleteMark($id){
			$this->db->query("DELETE FROM `mark` WHERE `id` = '$id' ");
		}

	//Notifications
	//Status
	// {0} - Unread
	// {1} - Read

		public function addNotification($content, $status, $teacher, $student){
			$student = $this->getStudent($student)->fetch_array();
			$term = $this->getCurrentTermByClass($student['class']);
			$this->db->query("INSERT INTO `notification` (`content`, `status`, `teacher`, `student`, `term`) VALUES ('$content', '$status', '$teacher', '".$student['id']."', '$term') ");
		}

		public function getNotification($id){
			$getNotification = $this->db->query("SELECT * FROM `notification` WHERE `id` = '$id'");
			return $getNotification;
		}

		public function getNotificationByStudent($student){
			$student = $this->getStudent($student)->fetch_array();
			$term = $this->getCurrentTermByClass($student['class']);
			$getNotificationByStudent = $this->db->query("SELECT * FROM `notification` WHERE `student` = '".$student['id']."' AND `term` = '$term' ORDER BY `date` DESC");
			return $getNotificationByStudent;
		}

		public function getNotificationByStudentUnread($student){
			$student = $this->getStudent($student)->fetch_array();
			$term = $this->getCurrentTermByClass($student['class']);
			$getNotificationByStudentUnread = $this->db->query("SELECT * FROM `notification` WHERE `student` = '".$student['id']."' AND `status` = '0' AND `term` = '$term'");
			return $getNotificationByStudentUnread;
		}

		public function deleteNotification($id){
			$this->db->query("DELETE FROM `notification` WHERE `id` = '$id'");
		}
	
	//Parents{

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
			$student = $this->getStudent($student);
			$row = $student->fetch_array();
			$mom = $row['mom'];
			$dad = $row['dad'];
			$getParentByStudent = $this->db->query("SELECT * FROM `parent` WHERE `id` = '$mom' OR `id` = '$dad' ");
			return $getParentByStudent;
		}

		public function deleteParent($id){
			$this->db->query("DELETE FROM `parent` WHERE `id` = '$id'");
		}

		public function deleteParentByStudent($student){
			$student = $this->getStudent($student);
			$row = $student->fetch_array();
			$this->deleteParent($row['mom']);
			$this->deleteParent($row['dad']);
		}
	
	//Schedules

	//Day
	// {2} - Monday
	// {3} - Tuesday
	// ...
	// {8} - Sunday

	//Period
	// {1} -> {10} - period 1->10

		public function addSchedule($class, $day, $period, $teacher){
			$term = $this->getCurrentTermByClass($class);
			$this->db->query("INSERT INTO `schedule` (`class`, `day`, `period`, `teacher`, `term`) VALUES ('$class', '$day', '$period', '$teacher', '$term') ");
		}

		public function editSchedule($id, $class, $day, $period, $teacher){
			$this->db->query("UPDATE `schedule` SET `class` = '$class', `day` = '$day', `period` = '$period', `teacher` = '$teacher' WHERE `id` = '$id' ");
		}

		public function getSchedule($id){
			$getSchedule = $this->db->query("SELECT * FROM `schedule` WHERE `id` = '$id'");
			return $getSchedule;
		}

		public function getScheduleClassByTeacher($teacher){
			$teacher = $this->getTeacher($teacher)->fetch_array();
			$term = $this->getCurrentTermBySchool($teacher['school']);
			$getScheduleClassByTeacher = $this->db->query("SELECT DISTINCT `class` FROM `schedule` WHERE `teacher` = '".$teacher['id']."' AND `term` = '$term'");
			return $getScheduleClassByTeacher;
		}

		public function getScheduleByClass($class){
			$schedule = array();
			$term = $this->getCurrentTermByClass($class);
			for($i=0; $i<6; $i++){
				$schedule[$i]['name'] = "Thứ ".($i+2);
				$sang = "";
				$chieu = "";
				$note = "";

				$from = date("Y-m-d", strtotime("sunday previous week"));
				$to = date("Y-m-d", strtotime("sunday next week"));
				$test = $this->getTestFromToDate($from, $to, $class);

				for($j=1; $j<6; $j++){
					$key = $i.$j;
					$getTeacher =  $this->db->query("SELECT `teacher` FROM `schedule` WHERE `class` = '$class' AND `day` = '".($i+2)."' AND `period` = '$j' AND `term` = '$term'");
					if($getTeacher->num_rows == 0)
						$sang .= "Trống\n";
					else {
						$getTeacher = $getTeacher->fetch_array();
						$sang .= $this->getSubjectByTeacher($getTeacher['teacher'])."\n";
					}
				}

				for($j=6; $j<11; $j++){
					$key = $i.$j;
					$getTeacher =  $this->db->query("SELECT `teacher` FROM `schedule` WHERE `class` = '$class' AND `day` = '$i' AND `period` = '$j'  AND `term` = '$term'");
					if($getTeacher->num_rows == 0)
						$chieu .= "Trống\n";
					else {
						$getTeacher = $getTeacher->fetch_array();
						$chieu .= $this->getSubjectByTeacher($getTeacher['teacher'])."\n";
					}
				}
				if($test->num_rows != 0)
					while($row = $test->fetch_assoc()){
						if(date_create($row['date'])->format('w') == $i + 1)
							$note.="Kiểm tra ".$this->getNameSubject($this->getTeacherSubject($row['teacher']))." ngày ".$row['date']."\n";
					}
				if($note == '')
					$note = "Không có";
				$schedule[$i]['sang'] = $sang;
				$schedule[$i]['chieu'] = $chieu;
				$schedule[$i]['note'] = $note;
			}
			return $schedule;
		}

		public function getScheduleByTeacher($teacher){
			$teacher = $this->getTeacher($teacher)->fetch_array();
			$schedule = array();
			$term = $this->getCurrentTermBySchool($teacher['school']);
			for($i=0; $i<6; $i++){
				$schedule[$i]['name'] = "Thứ ".($i+2);
				$sang = "";
				$chieu = "";
				$note = "";

				$from = date("Y-m-d", strtotime("sunday previous week"));
				$to = date("Y-m-d", strtotime("sunday next week"));
				$test = $this->getTestFromToDateOfTeacher($from, $to, $teacher['id']);

				for($j=1; $j<6; $j++){
					$key = $i.$j;
					$getClass =  $this->db->query("SELECT * FROM `schedule` WHERE `teacher` = '".$teacher['id']."' AND `day` = '".($i+2)."' AND `period` = '$j' AND `term` = '$term'");
					if($getClass->num_rows == 0)
						$sang .= "Trống\n";
					else {
						$getClass = $getClass->fetch_array();
						$class = $this->getClass($getClass['class'])->fetch_array();
						$sang .= $class['name']."\n";
					}
				}

				for($j=6; $j<11; $j++){
					$key = $i.$j;
					$getClass =  $this->db->query("SELECT * FROM `schedule` WHERE `teacher` = '".$teacher['id']."' AND `day` = '$i' AND `period` = '$j'  AND `term` = '$term'");
					if($getClass->num_rows == 0)
						$chieu .= "Trống\n";
					else {
						$getClass = $getClass->fetch_array();
						$class = $this->getClass($getClass['class'])->fetch_array();
						$chieu .= $class['name']."\n";
					}
				}
				if($test->num_rows != 0)
					while($row = $test->fetch_assoc()){
						if(date_create($row['date'])->format('w') == $i + 1)
							$note.="Kiểm tra ".$this->getNameSubject($this->getTeacherSubject($row['teacher']))." ngày ".$row['date']."\n";
					}
				if($note == '')
					$note = "Không có";
				$schedule[$i]['sang'] = $sang;
				$schedule[$i]['chieu'] = $chieu;
				$schedule[$i]['note'] = $note;
			}
			return $schedule;
		}

		public function getFirstTeacher($date, $class){
			$getFirstTeacher = $this->db->query("SELECT * FROM `schedule` WHERE `date` = '$date' AND `class` = '$class' AND `period` = '1' ");
			$row = $getFirstTeacher->fetch_array();
			return $row['teacher'];
		}

		public function deleteSchedule($id){
			$this->db->query("DELETE FROM `schedule` WHERE `id` = '$id'");
		}
	//Schools

		public function addSchool($name, $address){
			$this->db->query("INSERT INTO `school` (`name`, `address`) VALUES ('$name', '$address') ");
		}

		public function editSchool($id, $name, $address){
			$this->db->query("UPDATE `school` SET `name` = '$name', `address` = '$address' WHERE `id` = '$id' "); 
		}

		public function getSchool($school){
			$getSchool = $this->db->query("SELECT * FROM `school` WHERE `id` = '$school' ");
			return $getSchool;
		}

		public function getSchools(){
			$getSchools = $this->db->query("SELECT * FROM `school`");
			return $getSchools;
		}

		public function deleteSchool($id){
			$this->db->query("DELETE FROM `school` WHERE `id` = '$id'");
		}
	
	//Sessions
		public function clearSession(){
			$getSession = $this->db->query("SELECT * FROM `session` WHERE 1");
			while($row = $getSession->fetch_assoc()){
				$timeNow = new DateTime();
				$date = new DateTime($row['date']);
				$diff = $date->diff($timeNow);
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

		public function addSession($username){
			$randomString = $this->generateRandomString();
			$timeNow = date("Y-m-d H:i:s");
			$id = $this->getUserByUsername($username)->fetch_array();
			$this->db->query("DELETE * FROM `session` WHERE `user` = '".$id['id']."'");
			$this->db->query("INSERT INTO `session` (`token`, `date`, `user`) VALUES ('".md5($randomString)."', '$timeNow', '".$id['id']."') ");
			return md5($randomString);
		}


		public function addFCMToken($token, $FCMToken){
			$this->db->query("UPDATE `session` SET `FCMToken` = '$FCMToken' WHERE `token` = '$token'");
		}

		public function refreshSession($id){
			$timeNow = date("Y-m-d H:i:s");
			$this->db->query("UPDATE `session` SET `date` = '$timeNow' WHERE `id` = '$id'");
		}

		/*
		public function checkToken($token){
			$checkToken = $this->db->query("SELECT * FROM `session` WHERE `token` = '$token'");
			if($checkToken->num_rows == 0){
				return false;
			} else {
				$timeNow = new DateTime();
				$row = $checkToken->fetch_array();
				$date = date_create($row['date']);
				$diff = $date->diff($timeNow);
				if($diff->d >= 1){
					$this->deleteSession($row['id']);
					return false;
				} else {
					$this->refreshSession($row['id']);
					return true;
				}
			}
		}
		*/
		public function checkToken($token){
			$checkToken = $this->db->query("SELECT * FROM `session` WHERE `token` = '$token'");
			if($checkToken->num_rows == 0)
				return false;
			else
				return true;
		}

		public function getSessionUser($token){
			$getSessionUser = $this->db->query("SELECT * FROM `session` WHERE `token` = '$token'")->fetch_array();
			$row = $this->getUser($getSessionUser['user'])->fetch_array();
			return $row['user'];
		}

		public function getFCMToken($student){
			$getUser = $this->db->query("SELECT * FROM `user` WHERE `user` = '$student' AND `type` = '2'")->fetch_array();
			$getSessionUser = $this->db->query("SELECT * FROM `session` WHERE `user` = '".$getUser['id']."'")->fetch_array();
			return $getSessionUser['FCMToken'];
		}

		public function sessionIsAdmin($token){
			$user = $this->getUser($this->getSessionUser($token));
			$user = $user->fetch_array();
			if($user['type'] == 3)
				return true;
			else
				return false;
		}

		public function deleteSession($id){
			$this->db->query("DELETE FROM `session` WHERE `id` = '$id'");
		}
		public function logout($token){
			$this->db->query("DELETE FROM `session` WHERE `token` = '$token'");
		}

	//Students

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
	//Subjects

		public function addSubject($name){
			$this->db->query("INSERT INTO `subject` (`name`) VALUES ('$name') ");
		}

		public function editSubject($id, $name){
			$this->db->query("UPDATE `subject` SET `name` = '$name' WHERE `id` = '$id'");
		}

		public function getNameSubject($subject){
			$subject = $this->db->query("SELECT `name` FROM `subject` WHERE `id` = '$subject'")->fetch_array();
			return $subject['name'];
		}

		public function getSubjectByTeacher($teacher){
			$getTeacher = $this->getTeacher($teacher)->fetch_array();
			$subject = $this->db->query("SELECT `name` FROM `subject` WHERE `id` = '".$getTeacher['subject']."'")->fetch_array();
			return $subject['name'];
		}

		public function getSubjects(){
			$getSubjects = $this->db->query("SELECT * FROM `subject`");
			return $getSubjects;
		}

		public function deleteSubject($id){
			$this->db->query("DELETE FROM `subject` WHERE `id` = '$id'");
		}
	//Teachers
	//Type
	// {1} - GV bo mon
	// {2} - GV chu nhiem

		public function addTeacher($name, $address, $phone, $type, $subject, $school){
			$this->db->query("INSERT INTO `teacher` (`name`, `address`, `phone`, `type`, `subject`, `school`) VALUES ('$name', '$address', '$phone', '$type', '$subject', '$school') ");
		}

		public function editTeacher($id, $name, $address, $phone, $type, $subject, $school){
			$this->db->query("UPDATE `teacher` SET `name` = '$name', `address` = '$address', `phone` = '$phone', `type` = '$type', `subject` = '$subject', `school` = '$school' WHERE `id` = '$id'");
		}

		public function getTeacher($id){
			$getTeacher = $this->db->query("SELECT * FROM `teacher` WHERE `id` = '$id'");
			return $getTeacher;
		}

		public function getTeacherSubject($id){
			$getTeacher = $this->db->query("SELECT * FROM `teacher` WHERE `id` = '$id'")->fetch_array();
			return $getTeacher['subject'];
		}

		public function getTeacherBySchool($school){
			$getTeacherBySchool = $this->db->query("SELECT * FROM `teacher` WHERE `school` = '$school'");
			return $getTeacherBySchool;
		}

		public function deleteTeacher($id){
			$this->db->query("DELETE FROM `teacher` WHERE `id` = '$id'");
		}

	//Terms
	//Current
	// {0} - Not current term
	// {1} - Current term

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

		public function getCurrentTermBySchool($school){
			$getCurrentTerm = $this->db->query("SELECT * FROM `term` WHERE `school` = '$school' AND `current` = '1' ")->fetch_array();
			return $getCurrentTerm['id'];
		}

		public function getCurrentTermByClass($class){
			$getClass = $this->getClass($class)->fetch_array();
			return $this->getCurrentTermBySchool($getClass['school']);
		}

		public function setCurrentTerm($school, $id){
			$this->db->query("UPDATE `term` SET `current` = '0' WHERE `school` = '$school'");
			$this->db->query("UPDATE `term` SET `current` = '1' WHERE `id` = '$id'");
		}

		public function deleteTerm($id){
			$this->db->query("DELETE FROM `term` WHERE `id` = '$id'");
		}
	
	//Tests

	//type
	// {2} - Ktra 15'
	// {3} - Ktra 1 tiet
	// {4} - Ktra giua ky
	// {5} - Ktra cuoi ky

		public function addTest($date, $class, $teacher, $type){
			$term = $this->getCurrentTermByClass($class);
			$this->db->query("INSERT INTO `test` (`date`, `class`, `teacher`, `type`, `term`) VALUES ('$date', '$class', '$teacher', '$type', '$term') ");
		}

		public function editTest($id, $date, $class, $teacher, $type){
			$this->db->query("UPDATE `test` SET `date` = '$date', `class` =  '$class', `teacher` = '$teacher', `type` = '$type' WHERE `id` = '$id'");
		}

		public function getTest($id){
			$getTest = $this->db->query("SELECT * FROM `test` WHERE `id` = '$id'");
			return $getTest;
		}

		public function getTestFromToDate($from, $to, $class){
			return $this->db->query("SELECT * FROM `test` WHERE `class` = '$class' AND `date` > '$from' AND `date` < '$to'");
		}

		public function getTestFromToDateOfTeacher($from, $to, $teacher){
			return $this->db->query("SELECT * FROM `test` WHERE `teacher` = '$teacher' AND `date` > '$from' AND `date` < '$to'");
		}

		public function deleteTest($id){
			$this->db->query("DELETE FROM `test` WHERE `id` = '$id'");
		}

	//Users
	//type
	// {1} - Teacher
	// {2} - Parent
	// {3} - Admin

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
			if($user->num_rows == 0)
				return false;
			else {
				$row = $user->fetch_array();
				if($password == $row['password'])
					return true;
				else
					return false; 
			}
		}

		public function getUserType($username){
			$user = $this->getUserByUsername($username);
			$user = $user->fetch_array();
			return $user['type'];
		}

		public function deleteUser($id){
			$this->db->query("DELETE FROM `user` WHERE `id` = '$id'");
		}

}

?>