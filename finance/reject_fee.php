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
GET PAYMENT
=========================================*/

$get=$conn->prepare("

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

$student_id=$data['student_id'];

/*=========================================
REJECT PAYMENT
=========================================*/

$reject=$conn->prepare("

UPDATE application_payments

SET

verification_status='Rejected',
rejected_by=?,
rejected_at=NOW()

WHERE id=?

");
$reject->bind_param(

"ii",

$admin_id,
$payment_id

);

$reject->execute();

/*=========================================
STUDENT NOTIFICATION
=========================================*/

$title="Processing Fee Rejected";

$message="Your Processing Fee has been rejected. Please upload a valid bank receipt or contact the Finance Office.";

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

header("Location: verify_processing_fee.php?rejected=1");
exit;

?>