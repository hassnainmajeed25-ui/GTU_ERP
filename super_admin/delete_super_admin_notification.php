<?php

session_start();


require_once '../config/config.php';
require_once '../config/database.php';



/*=========================================
SUPER ADMIN SECURITY
=========================================*/


if(!isset($_SESSION['super_admin_id'])){


    header("Location: login.php");

    exit;


}




/*=========================================
CHECK ID
=========================================*/


if(!isset($_GET['id'])){


    header("Location: super_admin_notifications.php");

    exit;


}



$id=intval($_GET['id']);





/*=========================================
DELETE NOTIFICATION
=========================================*/


$delete=$conn->prepare("


UPDATE super_admin_notifications

SET

status='Deleted'

WHERE id=?


");



$delete->bind_param(

"i",

$id

);




if($delete->execute()){


    $_SESSION['success']="Notification Deleted Successfully.";



}else{


    $_SESSION['error']="Unable to Delete Notification.";


}





header("Location: super_admin_notifications.php");

exit;



?>