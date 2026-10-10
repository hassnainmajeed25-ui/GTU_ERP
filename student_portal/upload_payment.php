<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['student_id'])){

    header("Location: login.php");
    exit;

}

$student_id = $_SESSION['student_id'];

if($_SERVER['REQUEST_METHOD']!="POST"){

    header("Location: fee_status.php");
    exit;

}

$fee_structure_id = (int)$_POST['fee_structure_id'];

$uploadDir = "../uploads/fee_receipts/";

if(!file_exists($uploadDir)){

    mkdir($uploadDir,0777,true);

}

/*=========================================
FILE CHECK
=========================================*/

if(!isset($_FILES['receipt'])){

    die("Receipt not found.");

}

$fileName = $_FILES['receipt']['name'];
$tmpName  = $_FILES['receipt']['tmp_name'];
$fileSize = $_FILES['receipt']['size'];
$error    = $_FILES['receipt']['error'];

if($error != 0){

    die("File Upload Error.");

}

/*=========================================
ALLOWED FILE TYPES
=========================================*/

$allowed = array(

"jpg",
"jpeg",
"png",
"pdf"

);

$extension = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));

if(!in_array($extension,$allowed)){

    die("Only JPG, JPEG, PNG and PDF files are allowed.");

}

/*=========================================
FILE SIZE
=========================================*/

if($fileSize > 5*1024*1024){

    die("Maximum file size is 5 MB.");

}

/*=========================================
NEW FILE NAME
=========================================*/

$newFileName =

"REC_".
time().
"_".
$student_id.
".".
$extension;

$destination =

$uploadDir.$newFileName;

if(!move_uploaded_file($tmpName,$destination)){

    die("Unable to upload receipt.");

}

/*=========================================
CHECK DUPLICATE PAYMENT
=========================================*/

$check = $conn->prepare("

SELECT id

FROM fee_payments

WHERE

student_id=?
AND fee_structure_id=?
AND payment_status IN ('Pending','Verified')

LIMIT 1

");

$check->bind_param(

"ii",

$student_id,

$fee_structure_id

);

$check->execute();

$exists = $check->get_result();

if($exists->num_rows > 0){

    die("You have already submitted this fee.");

}

/*=========================================
SAVE PAYMENT
=========================================*/

$insert = $conn->prepare("

INSERT INTO fee_payments(

student_id,
fee_structure_id,
receipt_path,
payment_status,
submitted_at

)

VALUES(

?,
?,
?,
'Pending',
NOW()

)

");

$insert->bind_param(

"iis",

$student_id,

$fee_structure_id,

$newFileName

);

if($insert->execute()){

    echo "

<script>

alert('Receipt uploaded successfully. Your payment is now Pending for Finance Office verification.');

window.location='fee_status.php';

</script>

";

}else{

    echo "

<script>

alert('Unable to save payment.');

history.back();

</script>

";

}