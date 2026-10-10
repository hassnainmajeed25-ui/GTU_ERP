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

$student_id = $_SESSION['student_id'];

/*=========================================
ONLY POST REQUEST
=========================================*/

if($_SERVER['REQUEST_METHOD']!="POST"){

    header("Location: summer_fee.php");
    exit;

}

/*=========================================
GET FORM DATA
=========================================*/

$enrollment_id = intval($_POST['enrollment_id']);
$total_fee     = floatval($_POST['total_fee']);
$transaction_id = trim($_POST['transaction_id']);

/*=========================================
VALIDATION
=========================================*/

if(empty($transaction_id)){

    die("Transaction ID is required.");

}

if(!isset($_FILES['payment_slip'])){

    die("Payment Slip is required.");

}

$file = $_FILES['payment_slip'];

if($file['error']!=0){

    die("Payment Slip upload failed.");

}

/*=========================================
UPLOAD DIRECTORY
=========================================*/

$upload_dir = "../uploads/summer_fee_slips/";

if(!is_dir($upload_dir)){

    mkdir($upload_dir,0777,true);

}

/*=========================================
FILE EXTENSION
=========================================*/

$file_name = $file['name'];

$file_tmp  = $file['tmp_name'];

$file_size = $file['size'];

$file_ext  = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));

/*=========================================
ALLOWED FILE TYPES
=========================================*/

$allowed = array(

"jpg",

"jpeg",

"png",

"pdf"

);

if(!in_array($file_ext,$allowed)){

die("Only JPG, JPEG, PNG and PDF files are allowed.");

}

/*=========================================
MAX FILE SIZE
=========================================*/

if($file_size > 5*1024*1024){

die("Maximum file size is 5 MB.");

}

/*=========================================
UNIQUE FILE NAME
=========================================*/

$new_file_name =

"SUMMER_"

.$student_id."_"

.time()."."

.$file_ext;

$destination =

$upload_dir.$new_file_name;

/*=========================================
MOVE FILE
=========================================*/

if(!move_uploaded_file($file_tmp,$destination)){

die("Failed to upload payment slip.");

}

/*=========================================
SAVE PAYMENT RECORD
=========================================*/

$insert = $conn->prepare("

INSERT INTO summer_fee_payments
(

student_id,
enrollment_id,
transaction_id,
amount,
payment_slip,
payment_status,
submitted_at

)

VALUES
(

?,
?,
?,
?,
?,
'Pending',
NOW()

)

");

$insert->bind_param(

"iisds",

$student_id,

$enrollment_id,

$transaction_id,

$total_fee,

$new_file_name

);

/*=========================================
EXECUTE
=========================================*/

if($insert->execute()){

echo "

<script>

alert('Summer Fee Submitted Successfully.\n\nStatus : Pending Verification');

window.location='summer_fee.php';

</script>

";

}else{

echo "

<script>

alert('Failed to submit payment.');

history.back();

</script>

";

}

exit;

?>