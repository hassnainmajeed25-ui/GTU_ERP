<?php

session_start();


require_once '../config/config.php';
require_once '../config/database.php';



/*=========================================
LOGIN SECURITY
=========================================*/


if(!isset($_SESSION['admin_id'])){

    header("Location: ../admins/login.php");
    exit;

}



$admin_id=$_SESSION['admin_id'];



/*=========================================
CHECK REQUEST
=========================================*/


if($_SERVER['REQUEST_METHOD']!="POST"){


    header("Location: student_inbox.php");

    exit;

}



/*=========================================
GET DATA
=========================================*/


$message_id = intval($_POST['message_id']);

$reply = trim($_POST['reply']);




/*=========================================
VALIDATION
=========================================*/


if($message_id<=0 || empty($reply)){


    $_SESSION['error']="Reply message required.";

    header("Location: student_inbox.php");

    exit;

}



/*=========================================
CHECK MESSAGE OWNER
=========================================*/


$check=$conn->prepare("

SELECT id

FROM student_messages

WHERE id=?

AND admin_id=?

LIMIT 1

");



$check->bind_param(

"ii",

$message_id,

$admin_id

);



$check->execute();



$result=$check->get_result();



if($result->num_rows==0){


    $_SESSION['error']="Invalid Message.";

    header("Location: student_inbox.php");

    exit;

}

/*=========================================
UPDATE REPLY
=========================================*/


$update=$conn->prepare("

UPDATE student_messages

SET

reply=?,

status='Replied'

WHERE

id=?

AND

admin_id=?

");



$update->bind_param(

"sii",

$reply,

$message_id,

$admin_id

);




if($update->execute()){


    $_SESSION['success']="Reply Sent Successfully.";


}else{


    $_SESSION['error']="Unable to Send Reply.";


}




/*=========================================
REDIRECT
=========================================*/


header("Location: student_inbox.php");

exit;



?>