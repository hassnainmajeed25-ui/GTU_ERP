<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Finance Office"){
    header("Location: ../admins/login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: semester_fee_verification.php");
    exit;
}

$payment_id=(int)$_GET['id'];

$admin_id=$_SESSION['admin_id'];

/*=========================================
GET PAYMENT
=========================================*/

$stmt=$conn->prepare("

SELECT *

FROM semester_fee_payments

WHERE id=?

LIMIT 1

");

$stmt->bind_param("i",$payment_id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

die("Payment Record Not Found.");

}

$payment=$result->fetch_assoc();

/*=========================================
GET APPLICATION + PROGRAM + SESSION
=========================================*/

$app=$conn->prepare("

SELECT

applications.*,

students.cnic,

programs.program_name,
programs.program_code,

admission_sessions.session_name

FROM applications

INNER JOIN students
ON students.id = applications.student_id

INNER JOIN programs

ON programs.id=applications.program_id

INNER JOIN admission_sessions

ON admission_sessions.id=applications.session_id

WHERE applications.application_id=?

LIMIT 1

");

$app->bind_param("s",$payment['application_id']);

$app->execute();

$appResult=$app->get_result();

if($appResult->num_rows==0){

die("Application Not Found.");

}

$application=$appResult->fetch_assoc();

/*=========================================
GET PAID SEMESTER
=========================================*/

$paidSemester=(int)$payment['semester'];

if($paidSemester<1){

    $paidSemester=1;

}

/*=========================================
FIRST SEMESTER CHECK
=========================================*/

$isFirstSemester=false;

if($paidSemester==1){

    $isFirstSemester=true;

}

/*=========================================
PROGRAM CODE
=========================================*/

$programCode=strtoupper(trim($application['program_code']));

/*=========================================
SESSION CODE
=========================================*/

$sessionName=strtolower(trim($application['session_name']));

preg_match('/20(\d{2})/', $sessionName, $yearMatch);

$yearCode = isset($yearMatch[1]) ? $yearMatch[1] : date("y");

$sessionCode="F";

/*=========================================
SHIFT CODE
=========================================*/

$shift=strtolower(trim($application['shift']));

switch($shift){

    case "morning":
        $shiftCode="M";
        break;

    case "evening":
        $shiftCode="E";
        break;

    case "replica":
        $shiftCode="R";
        break;

    default:
        $shiftCode="X";
        break;

}

/*=========================================
REGISTRATION NUMBER
=========================================*/

$registration_no=$application['registration_no'];

if($isFirstSemester && empty($registration_no)){

    $regQuery=$conn->query("

    SELECT registration_no

    FROM applications

    WHERE registration_no IS NOT NULL

    ORDER BY id DESC

    LIMIT 1

    ");

    if($regQuery && $regQuery->num_rows>0){

        $regRow=$regQuery->fetch_assoc();

        preg_match('/(\d+)$/',$regRow['registration_no'],$match);

        $regNo=(int)$match[1];

        $regNo++;

    }else{

        $regNo=1;

    }

    $registration_no="REG-".date("Y")."-".str_pad($regNo,5,"0",STR_PAD_LEFT);

}

/*=========================================
ROLL PREFIX

Example:
GTU-BSCS-F26-M
=========================================*/

$prefix="GTU-".$programCode."-".$sessionCode.$yearCode."-".$shiftCode;

/*=========================================
LAST ROLL NUMBER OF SAME
PROGRAM + SESSION + SHIFT
=========================================*/

$rollQuery=$conn->prepare("

SELECT roll_no

FROM applications

WHERE roll_no LIKE CONCAT(?, '-%')

ORDER BY id DESC

LIMIT 1

");

$rollQuery->bind_param("s",$prefix);

$rollQuery->execute();

$rollResult=$rollQuery->get_result();

if($rollResult->num_rows>0){

    $rollRow=$rollResult->fetch_assoc();

    preg_match('/(\d+)$/',$rollRow['roll_no'],$match);

    $rollNumber=(int)$match[1];

    $rollNumber++;

}else{

    $rollNumber=1;

}

/*=========================================
FINAL ROLL NUMBER
=========================================*/

$roll_no=$application['roll_no'];

if($isFirstSemester && empty($roll_no)){

    $roll_no=$prefix."-".str_pad($rollNumber,2,"0",STR_PAD_LEFT);

}

/*=========================================
APPROVE PAYMENT
=========================================*/

$updatePayment=$conn->prepare("

UPDATE semester_fee_payments

SET

status='Approved',
verified_by=?,
verified_at=NOW()

WHERE id=?

");

$updatePayment->bind_param(

"ii",

$admin_id,
$payment_id

);

$updatePayment->execute();


/*=========================================
SAVE REGISTRATION & ROLL NUMBER
=========================================*/

$updateApplication=$conn->prepare("

UPDATE applications

SET

registration_no=?,
roll_no=?

WHERE id=?

");

$updateApplication->bind_param(

"ssi",

$registration_no,
$roll_no,
$application['id']

);

$updateApplication->execute();

/*=========================================
GENERATE LOGIN PASSWORD FROM CNIC
=========================================*/

$studentPassword = password_hash($application['cnic'], PASSWORD_DEFAULT);

/*=========================================
UPDATE STUDENT TABLE
=========================================*/

/*=========================================
CURRENT SEMESTER
=========================================*/

$semester=$paidSemester;

$program = $application['program_name'];

$updateStudent = $conn->prepare("

UPDATE students

SET

registration_no=?,

roll_no=?,

program_id=?,

semester=?,

shift=?,

program=?,

password=?

WHERE id=?

");
$updateStudent->bind_param(

"ssiisssi",

$registration_no,

$roll_no,

$application['program_id'],

$semester,

$application['shift'],

$program,

$studentPassword,

$application['student_id']

);

$updateStudent->execute();
/*=========================================
SEND NOTIFICATION TO STUDENT
=========================================*/

$title="Semester Fee Approved";

$message="Congratulations!

Your semester fee has been approved successfully.

Registration Number : ".$registration_no."

Roll Number : ".$roll_no."

You can now log in to your student dashboard and continue your academic activities.";

$type="success";

$notification=$conn->prepare("

INSERT INTO notifications

(

student_id,

title,

message,

type

)

VALUES

(?,?,?,?)

");

$notification->bind_param(

"isss",

$application['student_id'],

$title,

$message,

$type

);

$notification->execute();

/*=========================================
REDIRECT
=========================================*/

header("Location: semester_fee_verification.php?approve=1");

exit;

?>