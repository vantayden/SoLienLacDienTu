<?php
class Subject extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Core');
    }


    function getNameOfSubject($subject){
        $subject = $this->Core->get('subject', array('id' => $subject))->result();
        return $subject[0]->name;
    }

    function getSubjectByTeacher($teacher){
        $teacher = $this->Core->get('teacher', array('id' => $teacher))->result();
        $subject = $this->Core->get('subject', array('id' => $teacher[0]->subject))->result();
        return $subject[0];
    }
	
	function getSubjects(){
		return $this->Core->get('subject', array())->result();
	}
}
?>