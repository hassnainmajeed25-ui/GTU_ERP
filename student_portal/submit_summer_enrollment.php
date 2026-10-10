<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){

    header("Location: login.php");
    exit;

}

$student_id = $_SESSION['student_id'];

/*=========================================
ONLY POST REQUEST
=========================================*/

if($_SERVER['REQUEST_METHOD']!="POST"){

    header("Location: summer_enrollment.php");
    exit;

}

/*=========================================
CHECK STUDENT
=========================================*/

$studentQuery = $conn->prepare("

SELECT

id,
program_id,
semester,
shift

FROM students

WHERE id=?

LIMIT 1

");

$studentQuery->bind_param("i",$student_id);

$studentQuery->execute();

$student = $studentQuery->get_result()->fetch_assoc();

if(!$student){

    die("Student not found.");

}

/*=========================================
CHECK PREVIOUS APPLICATION
=========================================*/

$check = $conn->prepare("

SELECT id

FROM summer_enrollments

WHERE

student_id=?

AND status IN('Pending','Approved')

LIMIT 1

");

$check->bind_param("i",$student_id);

$check->execute();

if($check->get_result()->num_rows>0){

echo "

<script>

alert('You have already submitted a Summer Enrollment application.');

window.location='summer_enrollments.php';

</script>

";

exit;

}

/*=========================================
GET SUBJECTS
=========================================*/

$mandatory_subjects = $_POST['mandatory_subjects'] ?? [];

$optional_subjects = $_POST['optional_subjects'] ?? [];

/*=========================================
MERGE SUBJECTS
=========================================*/

$allSubjects = array_merge(

$mandatory_subjects,

$optional_subjects

);

$allSubjects = array_unique($allSubjects);

$totalSubjects = count($allSubjects);

/*=========================================
VALIDATION
=========================================*/

if($totalSubjects==0){

echo "

<script>

alert('Please select at least one subject.');

window.location='summer_enrollment.php';

</script>

";

exit;

}

if($totalSubjects>3){

echo "

<script>

alert('Maximum 3 subjects are allowed.');

window.location='summer_enrollment.php';

</script>

";

exit;

}

/*=========================================
CREATE SUMMER ENROLLMENT APPLICATION
=========================================*/

$summer_session = date("Y")." Summer";

$insert = $conn->prepare("

INSERT INTO summer_enrollments

(

student_id,
summer_session,
total_subjects,
status

)

VALUES

(

?,
?,
?,
'Pending'

)

");

$insert->bind_param(

"isi",

$student_id,

$summer_session,

$totalSubjects

);

if(!$insert->execute()){

die("Unable to create Summer Enrollment.");

}

$enrollment_id = $conn->insert_id;


/*=========================================
PREPARE SUBJECT INSERT
=========================================*/

$subjectInsert = $conn->prepare("

INSERT INTO summer_enrollment_subjects

(

enrollment_id,
subject_id,
grade,
repeat_type,
subject_status

)

VALUES

(

?,
?,
?,
?,
'Selected'

)

");

/*=========================================
SAVE MANDATORY SUBJECTS (F GRADE)
=========================================*/

foreach($mandatory_subjects as $subject_id){

$grade = "F";

$repeat_type = "Mandatory";

$subjectInsert->bind_param(

"iiss",

$enrollment_id,

$subject_id,

$grade,

$repeat_type

);

$subjectInsert->execute();

}


/*=========================================
SAVE OPTIONAL SUBJECTS (D GRADE)
=========================================*/

foreach($optional_subjects as $subject_id){

$grade = "D";

$repeat_type = "Optional";

$subjectInsert->bind_param(

"iiss",

$enrollment_id,

$subject_id,

$grade,

$repeat_type

);

$subjectInsert->execute();

}

/*=========================================
SUCCESS MESSAGE
=========================================*/

echo "

<script>

alert('Summer Enrollment Application Submitted Successfully.');

window.location='summer_enrollment.php';

</script>

";

exit;

?>