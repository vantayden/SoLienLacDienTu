<?php
class Term extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Core');
    }

    function getCurrentTermBySchool($school){
        $getCurrentTerm = $this->Core->get('term', array('school' => $school, 'current' => 1))->result();
        return $getCurrentTerm[0]->id;
    }

    function getCurrentTermByClass($class){
        $class = $this->Core->get('class', array('id' => $class))->result();
        return $this->getCurrentTermBySchool($class[0]->school);
    }
}
?>