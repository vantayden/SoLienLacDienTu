<?php
class Schedule extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Core');
        $this->load->model('Lophoc');
        $this->load->model('Subject');
        $this->load->model('Term');
        $this->load->model('Test');
    }

    function getScheduleByClass($class){
        $schedule = array();
        $term = $this->Term->getCurrentTermByClass($class);

        for($i=0; $i<6; $i++){
            $schedule[$i]['name'] = "Thứ ".($i+2);
            $sang = "";
            $chieu = "";
            $note = "";

            for($j=1; $j<6; $j++){
                $getTeacher = $this->Core->get('schedule', array(
                    'class' => $class,
                    'day' => ($i+2),
                    'period' => $j,
                    'term' => $term
                ));
                if($getTeacher->num_rows() == 0)
                    $sang .= "Trống\n";
                else {
                    $getTeacher = $getTeacher->result();
                    $subject = $this->Subject->getSubjectByTeacher($getTeacher[0]->teacher);
                    $sang .= $subject->name."\n";
                }
            }

            for($j=6; $j<11; $j++){
                $getTeacher = $this->Core->get('schedule', array(
                    'class' => $class,
                    'day' => ($i+2),
                    'period' => $j,
                    'term' => $term
                ));
                if($getTeacher->num_rows() == 0)
                    $chieu .= "Trống\n";
                else {
                    $getTeacher = $getTeacher->result();
                    $subject = $this->Subject->getSubjectByTeacher($getTeacher[0]->teacher);
                    $chieu .= $subject->name."\n";
                }
            }

            $from = date("Y-m-d", strtotime("sunday previous week"));
            $to = date("Y-m-d", strtotime("sunday next week"));
            $test = $this->Test->getTestFromToDate($from, $to, $class);

            if($test->num_rows() != 0){
                $exam = $test->result();
                for($c = 0; $c < $test->num_rows(); $c++){
                    if(date_create($exam[$c]->date)->format('w') == $i + 1){
                        $subject = $this->Subject->getSubjectByTeacher($exam[$c]->teacher);
                        $note.="Kiểm tra lớp ".$subject->name." ngày ".$exam[$c]->date."\n";
                    }
                }
            }
            if($note == '')
                $note = "Không có";
            $schedule[$i]['Sáng'] = $sang;
            $schedule[$i]['Chiều'] = $chieu;
            $schedule[$i]['Note'] = $note;
        }
        return $schedule;
    }

    function getNewScheduleByClass($class){
        $schedule = array();
        $term = $this->Term->getCurrentTermByClass($class);

        for($i=0; $i<6; $i++){
            $schedule[$i]['name'] = "Thứ ".($i+2);
            $periods = array();
            $period = array();


            $from = date("Y-m-d", strtotime("sunday previous week"));
            $to = date("Y-m-d", strtotime("sunday next week"));

            for($j=0; $j<10; $j++){
                $getTeacher = $this->Core->get('schedule', array(
                    'class' => $class,
                    'day' => ($i+2),
                    'period' => $j+1,
                    'term' => $term
                ));

                if($getTeacher->num_rows() == 0){
                    $period['type'] = "1";
                    $period['teacher'] = null;
                    $period['name'] = null;
                    $period['exam'] = null;
                } else {
                    $getTeacher = $getTeacher->result();
                    $subject = $this->Subject->getSubjectByTeacher($getTeacher[0]->teacher);
                    $period['name'] = $subject->sort_name;
                    $period['teacher'] = $getTeacher[0]->teacher;

                    $test = $this->Core->get('test', array('class' => $class, 'teacher' => $getTeacher[0]->teacher, 'date >' => $from, 'date <' => $to));
                    if($test->num_rows() == 0){
                        $period['type'] = "2";
                        $period['exam'] = null;
                    } else {
                        $test = $test->result();
                        $exam_date = new DateTime($test[0]->date);
                        if($exam_date->format('w') != $i+1){
                            $period['type'] = "2";
                            $period[$j]['exam'] = null;
                        } else {
                            $period['type'] = "3";
                            $period['exam'][] = $test[0]->date;
                        }
                    }

                }

                $periods[] = $period;
            }
            $schedule[$i]['periods'] = $periods;
        }
        return $schedule;
    }

    function getScheduleByTeacher($teacher){
        $schedule = array();

        $teacher = $this->Core->get('teacher', array('id'=>$teacher))->result();
        $term = $this->Term->getCurrentTermBySchool($teacher[0]->school);

        for($i=0; $i<6; $i++){
            $schedule[$i]['name'] = "Thứ ".($i+2);
            $sang = "";
            $chieu = "";
            $note = "";

            for($j=1; $j<6; $j++){
                $getTeacher = $this->Core->get('schedule', array(
                    'teacher' => $teacher[0]->id,
                    'day' => ($i+2),
                    'period' => $j,
                    'term' => $term
                ));
                if($getTeacher->num_rows() == 0)
                    $sang .= "Trống\n";
                else {
                    $getTeacher = $getTeacher->result();
                    $subject = $this->Subject->getSubjectByTeacher($getTeacher[0]->teacher);
                    $sang .= $subject->name."\n";
                }
            }

            for($j=6; $j<11; $j++){
                $getTeacher = $this->Core->get('schedule', array(
                    'teacher' => $teacher[0]->id,
                    'day' => ($i+2),
                    'period' => $j,
                    'term' => $term
                ));
                if($getTeacher->num_rows() == 0)
                    $chieu .= "Trống\n";
                else {
                    $getTeacher = $getTeacher->result();
                    $subject = $this->Subject->getSubjectByTeacher($getTeacher[0]->teacher);
                    $chieu .= $subject->name."\n";
                }
            }

            $from = date("Y-m-d", strtotime("sunday previous week"));
            $to = date("Y-m-d", strtotime("sunday next week"));
            $test = $this->Test->getTestFromToDateofTeacher($from, $to, $teacher[0]->id);
            if($test->num_rows() != 0){
                $exam = $test->result();
                for($c = 0; $c < $test->num_rows(); $c++){
                    if(date_create($exam[$c]->date)->format('w') == $i + 1){
                        $note.="Kiểm tra ".$this->Lophoc->getClassName($exam[$c]->class)." ngày ".$exam[$c]->date."\n";
                    }
                }
            }
            if($note == '')
                $note = "Không có";
            $schedule[$i]['Sáng'] = $sang;
            $schedule[$i]['Chiều'] = $chieu;
            $schedule[$i]['Note'] = $note;
        }
        return $schedule;
    }

    function getNewScheduleByTeacher($teacher){
        $schedule = array();

        $school = $this->Core->get('teacher', array('id' => $teacher))->result();
        $term = $this->Term->getCurrentTermBySchool($school[0]->school);


        for($i=0; $i<6; $i++){
            $schedule[$i]['name'] = "Thứ ".($i+2);
            $periods = array();
            $period = array();

            $from = date("Y-m-d", strtotime("sunday previous week"));
            $to = date("Y-m-d", strtotime("sunday next week"));
            for($j=1; $j<11; $j++){
                $getTeacher = $this->Core->get('schedule', array(
                    'teacher' => $teacher,
                    'day' => ($i+2),
                    'period' => $j,
                    'term' => $term
                ));

                if($getTeacher->num_rows() == 0){
                    $period['type'] = "1";
                    $period['name'] = null;
                    $period['exam'] = null;
                } else {
                    $getTeacher = $getTeacher->result();
                    $period['name'] = $this->Lophoc->getClassName($getTeacher[0]->class);

                    $test = $this->Core->get('test', array('teacher' => $teacher, 'class' => $getTeacher[0]->class, 'date >' => $from, 'date <' => $to));
                    if($test->num_rows() == 0 ){
                        $period['type'] = "2";
                        $period['exam'] = null;
                    } else {
                        $test = $test->result();
                        $exam_date = new DateTime($test[0]->date);
                        if($exam_date->format('w') != $i+1){
                            $period['type'] = "2";
                            $period[$j]['exam'] = null;
                        } else {
                            $period['type'] = "3";
                            $period['exam'][] = $test[0]->date;
                        }
                    }

                }
                $periods[] = $period;
            }
            $schedule[$i]['periods'] = $periods;
        }
        return $schedule;
    }
}
?>