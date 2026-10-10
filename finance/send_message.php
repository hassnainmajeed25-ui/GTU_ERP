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

$sender_id = $_SESSION['admin_id'];

if($_SERVER['REQUEST_METHOD']!="POST"){
    header("Location: compose_message.php");
    exit;
}

/*==========================
GET DATA
==========================*/

$receiver_role = $_POST['receiver_role'];

$subject = trim($_POST['subject']);
$message = trim($_POST['message']);

if(empty($receiver_role) || empty($subject) || empty($message)){
    die("Please fill all fields.");
}

/*==========================
GET SUPER ADMIN ID
==========================*/

$receiver_id = 0;

if($receiver_role=="Super Admin"){

    $q = $conn->query("
    SELECT id
    FROM admins
    WHERE role='Super Admin'
    LIMIT 1
    ");

    if($q->num_rows==0){
        die("Super Admin not found.");
    }

    $data = $q->fetch_assoc();

    $receiver_id = $data['id'];
}

/*==========================
SAVE MESSAGE
==========================*/

$stmt = $conn->prepare("

INSERT INTO messages(

sender_role,
sender_id,
receiver_role,
receiver_id,
subject,
message,
is_read

)

VALUES(

'Finance Office',
?,
?,
?,
?,
?,
0

)

");

$stmt->bind_param(
"isis",
$sender_id,
$receiver_role,
$receiver_id,
$subject,
$message
);

if($stmt->execute()){

    header("Location: compose_message.php?success=1");
    exit;

}else{

    echo $conn->error;

}

?>