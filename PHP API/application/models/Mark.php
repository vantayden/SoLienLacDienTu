<?php
class Mark extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Core');
    }


    function getMarkOfStudent($student){
        $this->load->model('Teacher');

        $student = $this->Core->get('student', array('id'=>$student))->result();
        $teachers = $this->Teacher->getTeacherOfClass($student[0]->class);

        $mark = array();
        foreach($teachers as $teacher){
            $tea = $this->Core->get('teacher', array('id'=>$teacher->teacher))->result();
            $subject = $this->Core->get('subject', array('id' => $tea[0]->subject))->result();
            $ma = array();
            $ma['name'] = $subject[0]->name;
            $ma['hs1'] = '';
            $ma['hs2'] = '';
            $ma['hs3'] = '';

            $marks = $this->getMarkByStudentAndTeacher($tea[0]->id, $student[0]->id);
            if($marks->num_rows() > 0){
                $marks = $marks->result();
                foreach($marks as $m){
                    $ma['hs'.$m->type] .= $m->mark.' ';
                }
            }
            $mark[] = $ma;
        }

        return $mark;
    }

    function getMarkByStudentAndTeacher($teacher, $student){
        $this->load->model('Term');

        $term = $this->Term->getCurrentTermByClass($student);
        return $this->Core->get('mark', array('student' => $student, 'teacher' => $teacher, 'term' => $term));
    }

}
?>