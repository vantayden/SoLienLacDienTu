<?php
class Core extends CI_Model{

    function __construct(){
        parent::__construct();
        $this->load->database();
    }

    //Core add, update, get, delete Objects

    function add($object, $data){
        if(isset($data['date'])){
            $date = new DateTime($data['date']);
            $data['date'] = date_format($date, "Y-m-d");
        }
        $this->db->insert($object, $data);
    }

    function update($object, $data, $where){
        $this->db->where($where);
        $this->db->update($object, $data);
    }

    function get($object, $where, $order = null, $select = null){
        $this->db->distinct();
        $this->db->where($where);
        if($order == null)
            $this->db->order_by('id', 'asc');
        else
            $this->db->order_by($order[0], $order[1]);
        if($select != null)
            $this->db->select($select);
        return $this->db->get($object);
    }

    function delete($object, $where){
        $this->db->where($where);
        $this->db->delete($object);
    }

    //Login, check permission, sessions function

    function check_user($usr, $pwd){
        $where = array('username' => $usr);
        $user = $this->db->get('user', $where);
        if($user->num_rows() == 0)
            return false;
        else {
            $user = $user->result();
            if(md5($pwd) != $user[0]->password)
                return false;
            else
                return true;
        }
    }

    function login($usr){
        $token = $this->generateRandomString();

        //Get user info
        $user = $this->get('user', array('username' => $usr))->result();

        //Delete old session
        $this->delete('session', array('user' => $user[0]->id));

        $data = array(
            'token' => $token,
            'user' => $user[0]->id
        );

        $this->add('session', $data);
        return $token;
    }

    function checkToken($token){
        $token = $this->get('session', array('token' => $token));
        return $token->num_rows();
    }

    function getUserFromToken($token){
        $user = $this->get('session', array('token' => $token))->result();
        $user = $this->get('user', array('id'=>$user[0]->user))->result();
        return $user[0];
    }

    //Special return

    function getStudentInfo($token, $FCMToken){
        $this->load->model('Mark');
        $this->load->model('Schedule');
        $this->load->model('School');
        $this->lang->load('api');

        $user = $this->getUserFromToken($token);
		$this->addFCMToken($user->user, $FCMToken);

        $student = $this->get('student', array('id' => $user->user))->result();
        $school = $this->get('class', array('id'=>$student[0]->class))->result();
		$student[0]->className = 'Lớp '.$school[0]->name;
        $student[0]->school = $this->School->getNameOfSchool($school[0]->school);
        $dad = $this->get('parent', array('id' => $student[0]->dad))->result();
        $mom = $this->get('parent', array('id' => $student[0]->mom))->result();
        //$schedule = $this->Schedule->getScheduleByClass($student[0]->class);
        $schedule = $this->Schedule->getNewScheduleByClass($student[0]->class);
        $mark = $this->Mark->getMarkOfStudent($student[0]->id);
        $callback = array(
            'status' => true,
            'message' => sprintf($this->lang->line('got_successful'), $this->lang->line('student')),
            'student' => $student[0],
            'dad' => $dad[0],
            'mom' => $mom[0],
            'schedule' => $schedule,
            'marks' => $mark
        );
        return $callback;
    }

    function getStudentNotification($token){
        $this->load->model('Teacher');
        $this->load->model('Subject');
        $this->lang->load('api');

        $user = $this->getUserFromToken($token);

        $notification = $this->get('notification', array('student' => $user->user), array('date', 'desc'));
        $notifications = $notification->result();
        for($c = 0; $c < $notification->num_rows(); $c++){
            $date = new DateTime($notifications[$c]->date);
            $notifications[$c]->date = $date->format('d/m/Y');
            $teacher = $this->get('teacher', array('id'=>$notifications[$c]->teacher))->result();
            if($teacher[0]->type == 1)
                $teacher[0]->type = 'Giáo viên bộ môn';
            else {
                $class = $this->get('class', array('owner' => $teacher[0]->id))->result();
                $teacher[0]->type = 'Giáo viên chủ nhiệm lớp '.$class[0]->name;
            }
            $teacher[0]->subject = $this->Subject->getNameOfSubject($teacher[0]->subject);
            $notifications[$c]->teacher = $teacher[0];
        }
        $callback = array(
            'status' => true,
            'message' => sprintf($this->lang->line('got_successful'), $this->lang->line('notification')),
            'total' => $notification->num_rows(),
            'notifications' => $notifications
        );
        return $callback;
    }

    function getTeacher($teacherID){
        $this->load->model('Teacher');
        $this->load->model('Subject');
        $this->lang->load('api');
        $teacher = $this->get('teacher', array('id' => $teacherID));
        if($teacher->num_rows() == 0){
            $callback = array(
                'status' => false,
                'message' => "Không tìm thấy Giáo viên!"
            );
        } else {
            $teacher = $teacher->result();
            $class = ($teacher[0]->type == 2) ? $this->get('class', array('owner' => $teacher[0]->id))->result() : null;
            if($teacher[0]->type == 1)
                $teacher[0]->type = 'Giáo viên bộ môn';
            else {
                $cla = $this->get('class', array('owner' => $teacher[0]->id))->result();
                $teacher[0]->type = 'Giáo viên chủ nhiệm lớp '.$cla[0]->name;
            }
            $teacher[0]->subject = $this->Subject->getNameOfSubject($teacher[0]->subject);
            $callback = array(
                'status' => true,
                'message' => sprintf($this->lang->line('got_successful'), $this->lang->line('teacher')),
                'teacher' => $teacher[0]
            );
        }
        return $callback;
    }

    function getTeacherInfo($token){
        $this->load->model('Schedule');
        $this->load->model('School');
        $this->lang->load('api');

        $user = $this->getUserFromToken($token);

        $teacher = $this->get('teacher', array('id' => $user->user))->result();
        $class = ($teacher[0]->type == 2) ? $this->get('class', array('owner' => $teacher[0]->id))->result() : null;
        if($teacher[0]->type == 1)
            $teacher[0]->type = 'Giáo viên bộ môn';
        else {
            $cla = $this->get('class', array('owner' => $teacher[0]->id))->result();
            $teacher[0]->type = 'Giáo viên chủ nhiệm lớp '.$cla[0]->name;
        }
        $teacher[0]->subject = $this->Subject->getNameOfSubject($teacher[0]->subject);
        $teacher[0]->school = $this->School->getNameOfSchool($teacher[0]->school);
        //$schedule = $this->Schedule->getScheduleByTeacher($teacher[0]->id);
        $schedule = $this->Schedule->getNewScheduleByTeacher($teacher[0]->id);
        $callback = array(
            'status' => true,
            'message' => sprintf($this->lang->line('got_successful'), $this->lang->line('teacher')),
            'teacher' => $teacher[0],
            'schedule' => $schedule,
            'myClass' => $class
        );
        return $callback;
    }

    function getTeacherClass($token){
        $this->load->model('Lophoc');

        $user = $this->getUserFromToken($token);
        $classes = $this->Lophoc->getTeacherClass($user->user);
        $callback = array(
            'status' => true,
            'message' => sprintf($this->lang->line('got_successful'), $this->lang->line('class')),
            'class' => $classes
        );
        return $callback;
    }

    function getAttendanceClass($token){
        $this->load->model('Lophoc');
        $user = $this->getUserFromToken($token);
        $classes = $this->Lophoc->getAttendanceClass($user->user);
        $callback = array(
            'status' => true,
            'message' => sprintf($this->lang->line('got_successful'), $this->lang->line('class')),
            'class' => $classes
        );
        return $callback;
    }


    //Extra functions
    function generateRandomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
	
	function addFCMToken($student, $FCMToken){
		$user = $this->get('user', array('user' => $student, 'type' => '2'))->result();
        $this->update('session', array('FCMToken' => $FCMToken), array('user' => $user[0]->id));
    }

    function getFCMToken($student){
        $user = $this->get('user', array('user' => $student, 'type' => '2'))->result();
        $session = $this->get('session', array('user' => $user[0]->id));
        if($session->num_rows() == 0)
            return false;
        else {
            $session = $session->result();
            return $session[0]->FCMToken;
        }
    }

    function sendFCM($token, $title, $message, $type){
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
            'Authorization: key=AIzaSyC7Cu1KcIBxvMnHAoc0YlJDwbXxg8njY9U',
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
    }
}
?>