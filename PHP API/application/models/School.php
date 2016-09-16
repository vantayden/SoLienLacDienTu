<?php
class School extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Core');
    }

    function getNameOfSchool($school){
        $school = $this->Core->get('school', array('id' => $school))->result();
        return $school[0]->name;
    }

}
?>