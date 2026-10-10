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

$student_id=$_SESSION['student_id'];

/*=========================================
CHECK REQUEST
=========================================*/

if($_SERVER['REQUEST_METHOD']!="POST"){

    header("Location: compose_message.php");
    exit;

}

/*=========================================
GET FORM DATA
=========================================*/

$admin_id=isset($_POST['receiver_id'])
? intval($_POST['receiver_id'])
:0;

$subject=trim($_POST['subject']);

$message=trim($_POST['message']);

/*=========================================
VALIDATION
=========================================*/

if(

$admin_id<=0 ||

empty($subject) ||

empty($message)

){

    $_SESSION['error']="Please fill all required fields.";

    header("Location: compose_message.php");

    exit;

}

/*=========================================
CHECK ADMIN
=========================================*/

$check=$conn->prepare("

SELECT

id,
full_name,
department

FROM admins

WHERE

id=?

AND

status='Active'

LIMIT 1

");

$check->bind_param(

"i",

$admin_id

);

$check->execute();

$result=$check->get_result();

if($result->num_rows==0){

    $_SESSION['error']="Invalid Department Selected.";

    header("Location: compose_message.php");

    exit;

}

$admin=$result->fetch_assoc();

/*=========================================
INSERT MESSAGE
=========================================*/

$insert=$conn->prepare("

INSERT INTO student_messages

(

student_id,
admin_id,
subject,
message

)

VALUES

(

?,?,?,?

)

");

$insert->bind_param(

"iiss",

$student_id,
$admin_id,
$subject,
$message

);

/*=========================================
SAVE MESSAGE
=========================================*/

if($insert->execute()){

    $_SESSION['success']="Your message has been sent successfully.";

}else{

    $_SESSION['error']="Unable to send message. Please try again.";

}

/*=========================================
REDIRECT
=========================================*/

header("Location: sent_messages.php");

exit;

?>