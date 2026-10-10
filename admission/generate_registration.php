<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=====================================
LOGIN SECURITY
=====================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Admission Office"){
    header("Location: ../admins/login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: selected_students.php");
    exit;
}

$id=(int)$_GET['id'];

/*=====================================
GET APPLICATION
=====================================*/

$stmt=$conn->prepare("

SELECT

application_id,
registration_no,
student_id

FROM applications

WHERE id=?

LIMIT 1

");

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

header("Location:selected_students.php");

exit;

}

$app=$result->fetch_assoc();
/*=====================================
GENERATE REGISTRATION NUMBER
=====================================*/

// Agar pehle se registration number hai
if(!empty($app['registration_no'])){

header("Location:selected_students.php");

exit;

}

// Current Year
$year=date("Y");

// Last Registration Number
$q=$conn->query("

SELECT registration_no

FROM applications

WHERE registration_no IS NOT NULL

ORDER BY id DESC

LIMIT 1

");

if($q && $q->num_rows>0){

$r=$q->fetch_assoc();

$last=$r['registration_no'];

/*

GTU-2026-0007

*/

$parts=explode("-",$last);

$number=(int)$parts[2];

$number++;

}else{

$number=1;

}

// Registration Number Format

$registration_no="GTU-".$year."-".str_pad($number,4,"0",STR_PAD_LEFT);

/*=====================================
SAVE REGISTRATION NUMBER
=====================================*/

$update=$conn->prepare("

UPDATE applications

SET registration_no=?

WHERE id=?

");

$update->bind_param("si",$registration_no,$id);

if($update->execute()){

    /*=====================================
    SAVE REGISTRATION NUMBER IN STUDENTS TABLE
    =====================================*/

    $student_id = $app['student_id'];

    $updateStudent = $conn->prepare("

    UPDATE students

    SET registration_no=?

    WHERE id=?

    ");

    $updateStudent->bind_param("si",$registration_no,$student_id);

    $updateStudent->execute();

    header("Location:selected_students.php?reg_success=1");

    exit;

}else{

    header("Location:selected_students.php?reg_error=1");

    exit;

}

?>
