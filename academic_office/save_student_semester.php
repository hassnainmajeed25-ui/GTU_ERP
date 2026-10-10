<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

    header("Location: ../admin/login.php");
    exit;

}

/*=========================================
CHECK REQUEST
=========================================*/

if($_SERVER['REQUEST_METHOD']!="POST"){

    header("Location: assign_student_semester.php");
    exit;

}

if(

!isset($_POST['students'])

||

count($_POST['students'])==0

){

    $_SESSION['error']="Please Select Students.";

    header("Location: assign_student_semester.php");
    exit;

}

$program_id = intval($_POST['program_id']);

$semester  = intval($_POST['semester']);

$shift      = trim($_POST['shift']);

$students = $_POST['students'];

/*=========================================
GET DEFAULT SESSION
=========================================*/

$session_id = 1;

/*=========================================
PREPARE INSERT
=========================================*/

$insert = $conn->prepare("

INSERT INTO student_semesters(

student_id,
program_id,
semester,
shift,
session_id,
status

)

VALUES(

?,?,?,?,?,'Active'

)

");

$check = $conn->prepare("

SELECT id

FROM student_semesters

WHERE

student_id=?

AND semester=?

AND status='Active'

LIMIT 1

");

$totalAssigned = 0;
/*=========================================
PART 2
SAVE STUDENTS
=========================================*/

foreach($students as $student_id){

    $student_id = intval($student_id);

    /*=========================================
    CHECK ALREADY ASSIGNED
    =========================================*/

    $check->bind_param(

    "ii",

    $student_id,

    $semester

    );

    $check->execute();

    $exists = $check->get_result();

    if($exists->num_rows > 0){

        continue;

    }

    /*=========================================
    INSERT STUDENT
    =========================================*/

    $insert->bind_param(

    "iiiis",

    $student_id,

    $program_id,

    $semester,

    $shift,

    $session_id

    );

    if($insert->execute()){

        $totalAssigned++;

    }

}

$insert->close();

$check->close();

/*=========================================
PART 3
SUCCESS MESSAGE
=========================================*/

$_SESSION['success'] =

$totalAssigned .

" Student(s) Assigned Successfully.";

header("Location: assign_student_semester.php");

exit;

?>