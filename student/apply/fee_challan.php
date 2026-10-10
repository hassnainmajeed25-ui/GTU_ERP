<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

$sql = $conn->prepare("

SELECT

a.application_id,
a.registration_no,
a.application_status,

s.first_name,
s.last_name,
s.email,

p.program_name,
f.faculty_name,

pay.challan_no,
pay.processing_fee,
pay.payment_status,
pay.created_at

FROM applications a

INNER JOIN students s
ON a.student_id=s.id

LEFT JOIN programs p
ON a.program_id=p.id

LEFT JOIN faculties f
ON a.faculty_id=f.id

LEFT JOIN application_payments pay
ON pay.application_id=a.id

WHERE a.student_id=?

LIMIT 1

");

$sql->bind_param("i",$student_id);

$sql->execute();

$result=$sql->get_result();

if($result->num_rows==0){

die("Application not found.");

}

$data=$result->fetch_assoc();

$due_date=date("d M Y",strtotime("+7 days"));

/*=====================================
UPLOAD PAYMENT RECEIPT
=====================================*/

$message = "";

if(isset($_POST['submit_receipt'])){

    $transaction_id = trim($_POST['transaction_id']);

    $receipt = "";

    if(isset($_FILES['receipt']) && $_FILES['receipt']['error']==0){

        $folder="../../uploads/receipts/";

        if(!is_dir($folder)){
            mkdir($folder,0777,true);
        }

        $extension=pathinfo($_FILES['receipt']['name'],PATHINFO_EXTENSION);

        $receipt="REC_".time().".".$extension;

        move_uploaded_file(
            $_FILES['receipt']['tmp_name'],
            $folder.$receipt
        );

    }

    $status="Verification Pending";

    $update=$conn->prepare("

    UPDATE application_payments

    SET

    transaction_id=?,

    receipt_image=?,

    submitted_at=NOW(),

    payment_status=?

    WHERE challan_no=?

    ");

    $update->bind_param(

    "ssss",

    $transaction_id,

    $receipt,

    $status,

    $data['challan_no']

    );

    if($update->execute()){

        $message="Payment receipt submitted successfully.";

    }

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>GTU Admission Processing Fee</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{
background:#edf2f7;
font-family:Arial,Helvetica,sans-serif;
}

.card{

margin-top:30px;

border:none;

border-radius:15px;

box-shadow:0 5px 25px rgba(0,0,0,.10);

}

.card-header{

background:#0B1F52;

color:white;

font-size:26px;

font-weight:bold;

padding:20px;

}

.table th{

width:35%;

background:#f8f9fa;

}

.payment-box{

background:#eef5ff;

padding:20px;

border-radius:12px;

margin-top:25px;

}

.btn-pay{

background:#198754;

color:white;

font-weight:bold;

}

.btn-print{

background:#FDB913;

font-weight:bold;

}

</style>

</head>

<body>

<div class="container">

<?php if($message!=""){ ?>

<div class="alert alert-success mt-4">

<h5>

<i class="fa-solid fa-circle-check"></i>

Success

</h5>

<?php echo $message; ?>

<br><br>

Your receipt has been sent to the Finance Office.

<br>

Payment Status:

<strong>

Verification Pending

</strong>

</div>

<?php } ?>

<div class="card">

<div class="card-header">

<div class="d-flex align-items-center justify-content-center">

<img src="../../assets/images/logo.png"
alt="GTU Logo"
style="height:80px;width:80px;margin-right:20px;">

<div>

<h2 class="mb-1 text-white">

GLOBAL TECH UNIVERSITY

</h2>

<p class="mb-0 text-white">

Admission Processing Fee Challan

</p>

</div>

</div>

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th>Student Name</th>

<td>

<?php echo $data['first_name']." ".$data['last_name']; ?>

</td>

</tr>

<tr>

<th>Application ID</th>

<td>

<?php echo $data['application_id']; ?>

</td>

</tr>

<tr>

<th>Faculty</th>

<td>

<?php echo $data['faculty_name']; ?>

</td>

</tr>

<tr>

<th>Program</th>

<td>

<?php echo $data['program_name']; ?>

</td>

</tr>

<tr>

<th>Challan Number</th>

<td>

<?php echo $data['challan_no']; ?>

</td>

</tr>

<tr>

<th>Processing Fee</th>

<td>

<b>

Rs. <?php echo number_format($data['processing_fee'],2); ?>

</b>

</td>

</tr>

<tr>

<th>Payment Status</th>

<td>

<?php echo $data['payment_status']; ?>

</td>

</tr>

<tr>

<th>Due Date</th>

<td>

<?php echo $due_date; ?>

</td>

</tr>

</table>

<div class="payment-box">

<h4 class="mb-4">

<i class="fa-solid fa-building-columns"></i>

University Bank Details

</h4>

<div class="alert alert-primary">

<table class="table table-bordered">

<tr>

<th width="35%">

Bank Name

</th>

<td>

Meezan Bnak Limited

</td>

</tr>

<tr>

<th>

Account Title

</th>

<td>

Ali Hamza 

</td>

</tr>

<tr>

<th>

Account Number

</th>

<td>

98690111864042

</td>

</tr>

<tr>

<th>

Branch Code

</th>

<td>

0101

</td>

</tr>

<tr>

<th>

Processing Fee

</th>

<td>

<strong>

Rs. 1,050

</strong>

</td>

</tr>

</table>

<div class="alert alert-warning mb-0">

<strong>Instructions:</strong>

<br>

1. Print this challan.

<br>

2. Deposit the fee in the above bank account.

<br>

3. Upload the payment receipt below.

</div>

</div>

<!-- Print Challan -->

<hr>

<h4 class="mb-3">

<i class="fa-solid fa-upload"></i>

Upload Payment Receipt

</h4>

<form action="" method="POST" enctype="multipart/form-data">

<div class="mb-3">

<label class="form-label">

Transaction ID / Deposit Slip No.

</label>

<input
type="text"
name="transaction_id"
class="form-control"
placeholder="Enter Transaction ID">

</div>

<div class="mb-3">

<label class="form-label">

Upload Receipt

</label>

<input
type="file"
name="receipt"
class="form-control"
accept=".jpg,.jpeg,.png,.pdf"
required>

</div>

<div class="d-flex justify-content-between">

<button
type="button"
onclick="window.print()"
class="btn btn-warning">

<i class="fa-solid fa-print"></i>

Print Challan

</button>

<button
type="submit"
name="submit_receipt"
class="btn btn-success">

<i class="fa-solid fa-paper-plane"></i>

Submit Receipt

</button>

</div>

</form>

<script>

document.querySelector("form").addEventListener("submit",function(e){

let file=document.querySelector("input[name='receipt']").value;

if(file==""){

alert("Please upload payment receipt.");

e.preventDefault();

}

});

</script>
<hr>

<div class="text-center mt-4">

<a href="../dashboard.php"
class="btn btn-secondary">

<i class="fa-solid fa-arrow-left"></i>

Back to Dashboard

</a>

</div>

</div>

</div>

</div>

<script>

const online=document.getElementById('online');

const challan=document.getElementById('challan');

const onlineBox=document.getElementById('onlinePayment');

const challanBox=document.getElementById('challanBox');

online.addEventListener('change',function(){

onlineBox.style.display='block';

challanBox.style.display='none';

});

challan.addEventListener('change',function(){

onlineBox.style.display='none';

challanBox.style.display='block';

});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>