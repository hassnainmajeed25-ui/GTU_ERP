<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Finance Office"){
    header("Location: ../admins/login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: verify_processing_fee.php");
    exit;
}

$payment_id=(int)$_GET['id'];

$admin_id=$_SESSION['admin_id'];
/*=========================================
GET PAYMENT RECORD
=========================================*/

$get = $conn->prepare("

SELECT

application_payments.application_id,
applications.student_id

FROM application_payments

INNER JOIN applications
ON applications.id=application_payments.application_id

WHERE application_payments.id=?

LIMIT 1

");

$get->bind_param("i",$payment_id);
$get->execute();

$result=$get->get_result();

if($result->num_rows==0){

header("Location: verify_processing_fee.php");
exit;

}

$data=$result->fetch_assoc();

$application_id=$data['application_id'];
$student_id=$data['student_id'];

/*=========================================
VERIFY PAYMENT
=========================================*/

$verify=$conn->prepare("

UPDATE application_payments

SET

verification_status='Verified',
payment_status='Paid',
verified_by=?,
verified_at=NOW()

WHERE id=?

");

$verify->bind_param("ii",$admin_id,$payment_id);
$verify->execute();

/*=========================================
UPDATE APPLICATION STATUS
=========================================*/

$app=$conn->prepare("

UPDATE applications

SET application_status='Under Review'

WHERE id=?

");

$app->bind_param("i",$application_id);
$app->execute();

/*=========================================
STUDENT NOTIFICATION
=========================================*/

$title="Processing Fee Verified";

$message="Congratulations! Your Processing Fee has been verified successfully. Your admission application has been forwarded to the Admission Office.";

$notify=$conn->prepare("

INSERT INTO student_notifications

(

student_id,
title,
message,
is_read

)

VALUES

(?,?,?,0)

");

$notify->bind_param(

"iss",

$student_id,
$title,
$message

);

$notify->execute();

/*=========================================
REDIRECT
=========================================*/

header("Location: verify_processing_fee.php?success=1");
exit;

?>