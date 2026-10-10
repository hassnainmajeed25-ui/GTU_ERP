<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
EXAMINATION LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

/*=========================================
CHECK PARAMETERS
=========================================*/

if(
    !isset($_GET['session_id']) ||
    !isset($_GET['student_id'])
){
    $_SESSION['error']="Invalid Request";
    header("Location: verify_marks.php");
    exit;
}

$session_id=(int)$_GET['session_id'];
$student_id=(int)$_GET['student_id'];

/*=========================================
GET STUDENT MARKS
=========================================*/

$sql=$conn->prepare("

SELECT

md.*,

ms.subject_id,
ms.program_id,
ms.semester,
ms.shift,

s.credit_hours

FROM marks_details md

INNER JOIN mark_sessions ms
ON md.mark_session_id=ms.id

INNER JOIN subjects s
ON ms.subject_id=s.id

WHERE

md.mark_session_id=?
AND md.student_id=?

LIMIT 1

");

$sql->bind_param(
"ii",
$session_id,
$student_id
);

$sql->execute();

$result=$sql->get_result();

if($result->num_rows==0){

    $_SESSION['error']="Marks Record Not Found";

    header("Location: verify_marks.php");

    exit;

}

$row=$result->fetch_assoc();

/*=========================================
CALCULATE TOTAL
=========================================*/

$total_marks = $row['quiz_marks']
             + $row['assignment_marks']
             + $row['presentation_marks']
             + $row['practical_marks']
             + $row['mid_marks']
             + $row['final_marks'];

$obtained_marks = $total_marks;

/*=========================================
TOTAL MARKS
=========================================*/

if($row['practical_marks'] > 0){

    $maximum_marks = 100;

}else{

    $maximum_marks = 95;

}

/*=========================================
PERCENTAGE
=========================================*/

$percentage = ($obtained_marks / $maximum_marks) * 100;

/*=========================================
GRADE + GRADE POINT
=========================================*/

$grade = "";
$grade_point = 0.00;
$result_status = "Fail";

if($percentage >= 85){

    $grade = "A+";
    $grade_point = 4.00;
    $result_status = "Pass";

}elseif($percentage >= 80){

    $grade = "A";
    $grade_point = 3.70;
    $result_status = "Pass";

}elseif($percentage >= 75){

    $grade = "B+";
    $grade_point = 3.30;
    $result_status = "Pass";

}elseif($percentage >= 70){

    $grade = "B";
    $grade_point = 3.00;
    $result_status = "Pass";

}elseif($percentage >= 65){

    $grade = "C+";
    $grade_point = 2.70;
    $result_status = "Pass";

}elseif($percentage >= 60){

    $grade = "C";
    $grade_point = 2.30;
    $result_status = "Pass";

}elseif($percentage >= 55){

    $grade = "D";
    $grade_point = 2.00;
    $result_status = "Pass";

}elseif($percentage >= 50){

    $grade = "E";
    $grade_point = 1.00;
    $result_status = "Pass";

}else{

    $grade = "F";
    $grade_point = 0.00;
    $result_status = "Fail";

}

/*=========================================
VERIFY MARK SESSION
=========================================*/

$verify = $conn->prepare("

UPDATE mark_sessions

SET

status='Verified'

WHERE id=?

");

$verify->bind_param(

"i",

$session_id

);

$verify->execute();

/*=========================================
UPDATE MARK DETAILS
=========================================*/

$updateMarks = $conn->prepare("

UPDATE marks_details

SET

obtained_marks=?,
remarks='Verified By Examination Office'

WHERE

mark_session_id=?
AND student_id=?

");

$updateMarks->bind_param(

"dii",

$obtained_marks,
$session_id,
$student_id

);

$updateMarks->execute();

/*=========================================
CHECK SEMESTER RESULT
=========================================*/

$check = $conn->prepare("

SELECT id

FROM semester_results

WHERE

mark_session_id=?
AND student_id=?

LIMIT 1

");

$check->bind_param(

"ii",

$session_id,
$student_id

);

$check->execute();

$exists = $check->get_result();

/*=========================================
INSERT OR UPDATE SEMESTER RESULT
=========================================*/

if($exists->num_rows > 0){

    $old = $exists->fetch_assoc();

    $update = $conn->prepare("

    UPDATE semester_results

    SET

    subject_id=?,
    program_id=?,
    semester=?,
    credit_hours=?,
    total_marks=?,
    obtained_marks=?,
    percentage=?,
    grade=?,
    grade_point=?,
    status=?,
    result_status='Generated'

    WHERE id=?

    ");

    $update->bind_param(

    "iiiidddsssi",

    $row['subject_id'],
    $row['program_id'],
    $row['semester'],
    $row['credit_hours'],
    $maximum_marks,
    $obtained_marks,
    $percentage,
    $grade,
    $grade_point,
    $result_status,
    $old['id']

    );

    $update->execute();

}else{

    $insert = $conn->prepare("

    INSERT INTO semester_results
    (
        mark_session_id,
        student_id,
        subject_id,
        program_id,
        semester,
        credit_hours,
        total_marks,
        obtained_marks,
        percentage,
        grade,
        grade_point,
        status,
        result_status
    )

    VALUES
    (
        ?,?,?,?,?,?,?,?,?,?,?,?,
        'Generated'
    )

    ");

    $insert->bind_param(

    "iiiiiidddsss",

    $session_id,
    $student_id,
    $row['subject_id'],
    $row['program_id'],
    $row['semester'],
    $row['credit_hours'],
    $maximum_marks,
    $obtained_marks,
    $percentage,
    $grade,
    $grade_point,
    $result_status

    );

    $insert->execute();

}

/*=========================================
CALCULATE STUDENT GPA
=========================================*/

$gpaQuery = $conn->prepare("

SELECT

SUM(grade_point * credit_hours) AS total_points,
SUM(credit_hours) AS total_ch

FROM semester_results

WHERE

student_id=?
AND semester=?

");

$gpaQuery->bind_param(

"ii",

$student_id,
$row['semester']

);

$gpaQuery->execute();

$gpaResult = $gpaQuery->get_result()->fetch_assoc();

$gpa = 0;

if($gpaResult['total_ch'] > 0){

    $gpa = round(
        $gpaResult['total_points'] / $gpaResult['total_ch'],
        2
    );

}

/*=========================================
SAVE STUDENT GPA RECORD
=========================================*/

$subjectNameQuery = $conn->prepare("

SELECT subject_name

FROM subjects

WHERE id=?

LIMIT 1

");

$subjectNameQuery->bind_param(
"i",
$row['subject_id']
);

$subjectNameQuery->execute();

$subjectData = $subjectNameQuery->get_result()->fetch_assoc();


$checkGPA = $conn->prepare("

SELECT id

FROM student_gpa

WHERE

student_id=?
AND semester=?
AND subject_name=?

LIMIT 1

");


$checkGPA->bind_param(

"iis",

$student_id,
$row['semester'],
$subjectData['subject_name']

);


$checkGPA->execute();


$gpaExists = $checkGPA->get_result();



if($gpaExists->num_rows > 0){


    $oldGPA = $gpaExists->fetch_assoc();


    $updateGPA = $conn->prepare("

    UPDATE student_gpa

    SET

    program_id=?,
    shift=?,
    credit_hours=?,
    grade=?,
    grade_points=?,
    gpa=?

    WHERE id=?

    ");


    $updateGPA->bind_param(

    "isisddi",

    $row['program_id'],
    $row['shift'],
    $row['credit_hours'],
    $grade,
    $grade_point,
    $gpa,
    $oldGPA['id']

    );


    $updateGPA->execute();



}else{


    $insertGPA = $conn->prepare("

    INSERT INTO student_gpa

    (

    student_id,
    program_id,
    semester,
    shift,
    subject_name,
    credit_hours,
    grade,
    grade_points,
    gpa

    )

    VALUES

    (?,?,?,?,?,?,?,?,?)

    ");


    $insertGPA->bind_param(

    "iiissisdd",

    $student_id,
    $row['program_id'],
    $row['semester'],
    $row['shift'],
    $subjectData['subject_name'],
    $row['credit_hours'],
    $grade,
    $grade_point,
    $gpa

    );


    $insertGPA->execute();


}

/*=========================================
CALCULATE OVERALL CGPA
=========================================*/

$cgpaQuery = $conn->prepare("

SELECT
SUM(grade_point * credit_hours) AS total_points,
SUM(credit_hours) AS total_ch

FROM semester_results

WHERE student_id=?

");

$cgpaQuery->bind_param(
"i",
$student_id
);

$cgpaQuery->execute();

$cgpaData = $cgpaQuery->get_result()->fetch_assoc();

$cgpa = 0;

if($cgpaData['total_ch'] > 0){

    $cgpa = round(
        $cgpaData['total_points'] / $cgpaData['total_ch'],
        2
    );

}

/*=========================================
SAVE / UPDATE RESULTS TABLE
=========================================*/

$checkResult = $conn->prepare("

SELECT id

FROM results

WHERE

student_id=?
AND semester=?

LIMIT 1

");

$checkResult->bind_param(
"ii",
$student_id,
$row['semester']
);

$checkResult->execute();

$checkData = $checkResult->get_result();

if($checkData->num_rows>0){

    $old = $checkData->fetch_assoc();

    $updateResult = $conn->prepare("

    UPDATE results

    SET

    total_marks=?,
    obtained_marks=?,
    percentage=?,
    cgpa=?,
    result_status=?

    WHERE id=?

    ");

    $updateResult->bind_param(

    "ddddsi",

    $maximum_marks,
    $obtained_marks,
    $percentage,
    $cgpa,
    $result_status,
    $old['id']

    );

    $updateResult->execute();

}else{

    $insertResult = $conn->prepare("

    INSERT INTO results
    (
        student_id,
        semester,
        total_marks,
        obtained_marks,
        percentage,
        cgpa,
        result_status
    )

    VALUES
    (
        ?,?,?,?,?,?,?
    )

    ");

    $insertResult->bind_param(

    "iidddds",

    $student_id,
    $row['semester'],
    $maximum_marks,
    $obtained_marks,
    $percentage,
    $cgpa,
    $result_status

    );

    $insertResult->execute();

}

/*=========================================
NOTIFICATION
=========================================*/

$title="Marks Verified";

$message="Student marks verified successfully by Examination Office.";

$notify=$conn->prepare("

INSERT INTO admin_notifications
(
user_role,
user_id,
title,
message
)

VALUES
(
'Examination',
?,
?,
?
)

");

$notify->bind_param(

"iss",

$_SESSION['admin_id'],
$title,
$message

);

$notify->execute();

/*=========================================
SUCCESS
=========================================*/

$_SESSION['success']="Student Marks Verified Successfully.";

header("Location: verify_marks.php");

exit;

?>