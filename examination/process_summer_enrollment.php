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


if($_SESSION['department']!="Examination Office"){

    header("Location: ../admins/login.php");
    exit;

}


/*=========================================
ONLY POST REQUEST
=========================================*/

if($_SERVER['REQUEST_METHOD']!="POST"){

    header("Location: summer_enrollment.php");
    exit;

}


/*=========================================
GET DATA
=========================================*/


$enrollment_id = $_POST['enrollment_id'] ?? 0;

$action = $_POST['action'] ?? '';

$remarks = $_POST['remarks'] ?? '';



if(empty($enrollment_id) || empty($action)){


    echo "

    <script>

    alert('Invalid Request.');

    window.location='summer_enrollment.php';

    </script>

    ";

    exit;

}



/*=========================================
VALID STATUS
=========================================*/


if($action!="Approved" && $action!="Rejected"){


    echo "

    <script>

    alert('Invalid Action.');

    window.location='summer_enrollment.php';

    </script>

    ";

    exit;


}

/*=========================================
CHECK APPLICATION
=========================================*/


$check = $conn->prepare("

SELECT id,status

FROM summer_enrollments

WHERE id=?

LIMIT 1

");


$check->bind_param(

"i",

$enrollment_id

);


$check->execute();


$result = $check->get_result();



if($result->num_rows==0){


echo "

<script>

alert('Summer Enrollment Application Not Found.');

window.location='summer_enrollment.php';

</script>

";


exit;


}



$application = $result->fetch_assoc();



/*=========================================
ALREADY DECIDED
=========================================*/


if($application['status']!="Pending"){


echo "

<script>

alert('Application decision already completed.');

window.location='summer_enrollment.php';

</script>

";


exit;


}



/*=========================================
UPDATE STATUS
=========================================*/


$update = $conn->prepare("

UPDATE summer_enrollments

SET

status=?,

remarks=?

WHERE id=?

");


$update->bind_param(

"ssi",

$action,

$remarks,

$enrollment_id

);



if(!$update->execute()){


die("Status Update Failed : ".$conn->error);


}
/*=========================================
SUCCESS MESSAGE
=========================================*/


if($action=="Approved"){


$message = "Summer Enrollment Approved Successfully.";


}else{


$message = "Summer Enrollment Rejected Successfully.";


}



echo "

<script>

alert('$message');

window.location='summer_enrollment.php';

</script>

";


exit;


?>