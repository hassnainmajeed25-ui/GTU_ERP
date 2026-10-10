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

if($_SESSION['department']!="Finance Office"){

    header("Location: ../admins/login.php");
    exit;

}

/*=========================================
ONLY POST REQUEST
=========================================*/

if($_SERVER['REQUEST_METHOD']!="POST"){

    header("Location: summer_fee_verification.php");
    exit;

}

/*=========================================
GET FORM DATA
=========================================*/

$payment_id = (int)$_POST['payment_id'];

$action = trim($_POST['action']);

$remarks = trim($_POST['finance_remarks']);

/*=========================================
VALIDATION
=========================================*/

if($payment_id<=0){

    die("Invalid Payment ID.");

}

if($action!="Approved" && $action!="Rejected"){

    die("Invalid Action.");

}

/*=========================================
LOAD PAYMENT RECORD
=========================================*/

$payment = $conn->prepare("

SELECT

id,
student_id,
summer_enrollment_id,
payment_status

FROM summer_fee_payments

WHERE id=?

LIMIT 1

");

$payment->bind_param("i",$payment_id);

$payment->execute();

$paymentResult = $payment->get_result();

if($paymentResult->num_rows==0){

die("Payment Record Not Found.");

}

$paymentData = $paymentResult->fetch_assoc();

/*=========================================
CHECK ALREADY APPROVED
=========================================*/

if($paymentData['payment_status']=="Approved"){

echo "

<script>

alert('This payment has already been approved.');

window.location='summer_fee_verification.php';

</script>

";

exit;

}

/*=========================================
UPDATE PAYMENT STATUS
=========================================*/

$update = $conn->prepare("

UPDATE summer_fee_payments

SET

payment_status=?,
finance_remarks=?

WHERE id=?

");

$update->bind_param(

"ssi",

$action,
$remarks,
$payment_id

);

if(!$update->execute()){

die("Failed to update payment.");

}

/*=========================================
IF APPROVED
UPDATE SUMMER ENROLLMENT
=========================================*/

if($action=="Approved"){

$approveEnrollment = $conn->prepare("

UPDATE summer_enrollments

SET status='Approved'

WHERE id=?

");

$approveEnrollment->bind_param(

"i",

$paymentData['summer_enrollment_id']

);

$approveEnrollment->execute();

}

/*=========================================
IF REJECTED
UPDATE SUMMER ENROLLMENT
=========================================*/

if($action=="Rejected"){

$rejectEnrollment = $conn->prepare("

UPDATE summer_enrollments

SET status='Rejected'

WHERE id=?

");

$rejectEnrollment->bind_param(

"i",

$paymentData['summer_enrollment_id']

);

$rejectEnrollment->execute();

}

/*=========================================
SUCCESS MESSAGE
=========================================*/

if($action=="Approved"){

echo "

<script>

alert('Summer Fee has been Approved Successfully.');

window.location='summer_fee_verification.php';

</script>

";

}else{

echo "

<script>

alert('Summer Fee has been Rejected.');

window.location='summer_fee_verification.php';

</script>

";

}

exit;

?>