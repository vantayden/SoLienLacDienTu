<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require(APPPATH.'\libraries\REST_Controller.php');

class API extends REST_Controller {
    function index_get(){
        echo "hello, world";
    }

    function login_post(){
        $this->load->model('Core');
        $this->lang->load('api');

        $check_user = $this->Core->check_user($this->post('username'), $this->post('password'));
        if($check_user == false){
            $callback = array(
                'status' => false,
                'error' => $this->lang->line('wrong_username_or_password')
            );
        } else {
            $token = $this->Core->login($this->post('username'));
            $user = $this->Core->getUserFromToken($token);
            $callback = array(
                'status' => true,
                'message' => $this->lang->line('login_successful'),
                'token' => $token,
                'type' => $user->type,
                'id' => $user->id
            );
        }

        $this->response($callback);
    }

    function logout_post(){
        $this->load->model('Core');
        $this->lang->load('api');

        $token = $this->post('token');
        if(!$this->Core->checkToken($token)){
            $callback = array(
                'status' => false,
                'message' => $this->lang->line('wrong_token')
            );
        } else {
            $this->Core->delete('session', array('token'=>$this->post('token')));
            $callback = array(
                'status' => true,
                'message' => $this->lang->line('logout_successful')
            );
        }
        $this->response($callback);
    }

    function add_post(){
        $this->load->model('Core');
        $this->load->model('Term');
        $this->lang->load('api');

        $token = $this->post('token');
        if(!$this->Core->checkToken($token)){
            $callback = array(
                'status' => false,
                'message' => $this->lang->line('wrong_token')
            );
        } else {
            $object = $this->post('object');
            $data = $this->post('data');
            switch($object){
                case 'ask':
                    $data['student'] = $this->Core->getUserFromToken($this->post('token'))->user;
                    break;
                case 'attendance':
                    $teacher = $this->Core->getUserFromToken($this->post('token'));
                    $data['teacher'] = $teacher->user;
                    break;
                case 'mark':
                    $data['date'] = date('Y-m-d');
                case 'notification':
                    $teacher = $this->Core->get('teacher', array('id'=> $this->Core->getUserFromToken($this->post('token'))->user))->result();
                    $data['teacher'] = $teacher[0]->id;
                    $data['term'] = $this->Term->getCurrentTermBySchool($teacher[0]->school);
					$FCMToken = $this->Core->getFCMToken($data['student']);
					$this->Core->sendFCM($FCMToken, 'Thông báo từ giáo viên', $data['content'], 1);
                    break;
                default:
                    break;
            }
            $this->Core->add($object, $data);
            $callback = array(
                'status' => true,
                'message' => sprintf($this->lang->line('added_successful'), $this->lang->line($this->post('object')))
            );
        }
        $this->response($callback);
    }

    function get_post(){
        $this->load->model('Core');
        $this->lang->load('api');

        $token = $this->post('token');

        if(!$this->Core->checkToken($token)){
            $callback = array(
                'status' => false,
                'message' => $this->lang->line('wrong_token')
            );
        } else {
            if($this->get('student') == 'info')
                $callback = $this->Core->getStudentInfo($token, $this->post('FCMToken'));
            else if($this->get('student') == 'notification')
                $callback = $this->Core->getStudentNotification($token);
            else if($this->get('teacher') == 'info')
                $callback = $this->Core->getTeacherInfo($token);
            else if($this->get('teacher') == 'class')
                $callback = $this->Core->getTeacherClass($token);
            else if($this->get('teacher') == 'attendanceClass')
                $callback = $this->Core->getAttendanceClass($token);
            else if(is_numeric($this->get('teacher')))
                $callback = $this->Core->getTeacher($this->get('teacher'));
            else
                $callback = array(
                    'status' => false,
                    'message' => $this->lang->line('unknown_api')
                );
        }

        $this->response($callback);
    }

    /*
    function update_post(){
        $this->load->model('Core');
        $this->lang->load('api');
        if(!$this->Core->checkToken($this->post('token'))){
            $callback = array(
                'status' => false,
                'message' => $this->lang->line('wrong_token')
            );
        } else {
            $this->Core->update($this->post('object'), $this->post('data'), array);
            $callback = array(
                'status' => true,
                'message' => sprintf($this->lang->line('updated_successful'), $this->lang->line($this->post('object')))
            );
        }
        $this->response($callback);
    }
    */
}