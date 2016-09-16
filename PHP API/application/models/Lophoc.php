<?php
class Lophoc extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Core');
    }

    function getClassName($class){
        $class = $this->Core->get('class', array(
            'id' => $class
        ))->result();
        return $class[0]->name;
    }

    function getTeacherClass($teacher){
        $this->Core->db->distinct();
        $this->Core->db->select('class');
        $class = $this->Core->db->get_where('schedule', array('teacher' => $teacher))->result();
        $classes = array();
        foreach($class as $cl){
            $cla = $this->Core->get('class', array('id' => $cl->class))->result();
            $student = $this->Core->get('student', array('class' => $cla[0]->id), array('fname', 'asc'), array('id', 'name'))->result();

            $clas = array('id' => $cla[0]->id, 'name' => $cla[0]->name);
            $clas['student'] = $student;

            $classes[] = $clas;
        }

        return $classes;
    }

    function getAttendanceClass($teacher){
        $this->Core->db->distinct();
        $this->Core->db->select('class');
        $class = $this->Core->db->get_where('schedule', array('teacher' => $teacher))->result();
        $classes = array();
        foreach($class as $cl){
            $cla = $this->Core->get('class', array('id' => $cl->class))->result();
            $students = $this->Core->get('student', array('class' => $cla[0]->id), array('fname', 'asc'), array('id', 'name'))->result();
            $student = array();
            foreach($students as $stu){
                $today = date('Y-m-d');
                $stude = $this->Core->get('ask', array('student' => $stu->id, 'date' => $today));
                if($stude->num_rows() == 0){
                    $stu->type = '1';
                    $stu->reason = null;
                } else {
                    $stude = $stude->result();
                    $stu->type = '2';
                    $stu->reason = $stude[0]->content;
                }
                unset($stu->fname); unset($stu->class); unset($stu->image); unset($stu->address); unset($stu->dad); unset($stu->mom);
                $student[] = $stu;
            }

            $clas = array('id' => $cla[0]->id, 'name' => $cla[0]->name);
            $clas['student'] = $student;
            $classes[] = $clas;
        }
        return $classes;
    }
}
?>