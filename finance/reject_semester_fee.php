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
GET APPLICATION
=========================================*/

$app=$conn->prepare("

SELECT *
FROM applications

WHERE application_id=?

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
REJECT PAYMENT
=========================================*/

$update=$conn->prepare("

UPDATE semester_fee_payments

SET

status='Rejected',

verified_by=?,

verified_at=NOW()

WHERE id=?

");

$update->bind_param(

"ii",

$admin_id,

$payment_id

);

$update->execute();

/*=========================================
SEND NOTIFICATION
=========================================*/

$title="Semester Fee Rejected";

$message="Your semester fee payment has been rejected.

Please upload a clear bank receipt or contact the Finance Office for further assistance.";

$type="danger";

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

header("Location: semester_fee_verification.php?reject=1");

exit;

?>