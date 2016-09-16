<?php
class Teacher extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Core');
    }


    function getNameOfTeacher($teacher){
        $teacher = $this->Core->get('teacher', array('id' => $teacher))->result();
        return $teacher[0]->name;
    }

    function getTeacherOfClass($class){
        $this->Core->db->distinct();
        $this->Core->db->select('teacher');
        return $this->Core->db->get_where('schedule', array('class' => $class))->result();
    }
}
?>