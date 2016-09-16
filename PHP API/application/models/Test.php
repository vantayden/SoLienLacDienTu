<?php
class Test extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Core');
    }

    function getTestFromToDate($from, $to, $class){
        return $this->Core->get('test', array(
            'class' => $class,
            'date > ' => $from,
            'date < ' => $to
        ));
    }

    function getTestFromToDateOfTeacher($from, $to, $teacher){
        return $this->Core->get('test', array(
            'teacher' => $teacher,
            'date > ' => $from,
            'date < ' => $to
        ));
    }
}
?>