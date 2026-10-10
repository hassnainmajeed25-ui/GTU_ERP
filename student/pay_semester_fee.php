<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

$message = "";
$type = "";

/*=========================================
GET STUDENT
=========================================*/

$student = $conn->prepare("

SELECT

id,
first_name,
last_name,
email

FROM students

WHERE id=?

LIMIT 1

");

$student->bind_param("i", $student_id);
$student->execute();

$studentResult = $student->get_result();
$studentData = $studentResult->fetch_assoc();

/*=========================================
GET APPLICATION
=========================================*/

$app = $conn->prepare("

SELECT *

FROM applications

WHERE

student_id=?

AND application_status='Selected'

LIMIT 1

");

$app->bind_param("i", $student_id);
$app->execute();

$appResult = $app->get_result();

if ($appResult->num_rows == 0) {

    die("You are not selected yet.");

}

$application = $appResult->fetch_assoc();

/*=========================================
CHECK SEMESTER FEE PAYMENT
=========================================*/

$alreadyPaid = false;
$paymentData = [];

$payment = $conn->prepare("

SELECT *

FROM semester_fee_payments

WHERE student_id=?

LIMIT 1

");

$payment->bind_param("i", $student_id);
$payment->execute();

$paymentResult = $payment->get_result();

if ($paymentResult->num_rows > 0) {

    $alreadyPaid = true;
    $paymentData = $paymentResult->fetch_assoc();

}

/*=========================================
GET FEE STRUCTURE
=========================================*/

$fee = $conn->prepare("

SELECT *

FROM fee_structure

WHERE

faculty_id=?

AND

program_id=?

AND

session_id=?

AND

shift=?

AND

status='Active'

LIMIT 1

");

$fee->bind_param(

    "iiis",

    $application['faculty_id'],

    $application['program_id'],

    $application['session_id'],

    $application['shift']

);

$fee->execute();

$feeResult = $fee->get_result();

if($feeResult->num_rows==0){

    /*=========================================
    IF STUDENT ALREADY PAID
    =========================================*/

    if($alreadyPaid){

        if($paymentData['status']=="Approved"){

?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Semester Fee</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

<div class="modal fade"

id="alreadyPaidModal"

tabindex="-1"

data-bs-backdrop="static"

data-bs-keyboard="false">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content">

<div class="modal-header bg-success text-white">

<h4 class="modal-title">

Semester Fee Already Paid

</h4>

</div>

<div class="modal-body text-center">

<h3 class="text-success">

Congratulations 🎉

</h3>

<hr>

<p>

You have already paid your Semester Fee.

</p>

<p>

Your payment has been verified successfully.

</p>

<p>

Roll Number

</p>

<h4 class="text-primary">

<?php echo $application['roll_no']; ?>

</h4>

<p class="text-danger">

You cannot pay the Semester Fee again.

</p>

</div>

<div class="modal-footer">

<a href="dashboard.php"

class="btn btn-success w-100">

OK

</a>

</div>

</div>

</div>

</div>

<script>

var modal=new bootstrap.Modal(

document.getElementById('alreadyPaidModal')

);

modal.show();

</script>

</body>

</html>

<?php

exit;

        }

    }

    echo "<h2>Fee Structure Not Found</h2>";
    echo "<hr>";
    echo "Faculty ID : ".$application['faculty_id']."<br>";
    echo "Program ID : ".$application['program_id']."<br>";
    echo "Session ID : ".$application['session_id']."<br>";
    echo "Shift : ".$application['shift']."<br>";

    exit;

}

$feeData = $feeResult->fetch_assoc();

/*=========================================
TOTAL FEE
=========================================*/

$totalFee =

$feeData['admission_fee'] +

$feeData['tuition_fee'] +

$feeData['exam_fee'] +

$feeData['library_fee'] +

$feeData['lab_fee'] +

$feeData['other_fee'];

if(isset($_GET['success'])){

    $message="Semester Fee submitted successfully. Please wait for Finance Office verification.";

    $type="success";

}

/*=========================================
UPLOAD SEMESTER FEE
=========================================*/

if(isset($_POST['submit_fee'])){

    if($alreadyPaid){

        if($paymentData['status']=="Approved"){

            $message="You have already paid your Semester Fee. You cannot pay again.";
            $type="warning";

        }else{

            $message="Your Semester Fee is already submitted and is under verification.";
            $type="warning";

        }

    }else{

        $bank_name = trim($_POST['bank_name']);

        $transaction_id = trim($_POST['transaction_id']);

        $payment_date = $_POST['payment_date'];

        $slip = "";

        /*=============================
        UPLOAD SLIP
        =============================*/

        if(isset($_FILES['slip']) && $_FILES['slip']['error']==0){

            $extension = strtolower(pathinfo(

                $_FILES['slip']['name'],

                PATHINFO_EXTENSION

            ));

            $allowed = array(

                "jpg",
                "jpeg",
                "png",
                "pdf"

            );

            if(in_array($extension,$allowed)){

                $slip = "semester_fee_".time().".".$extension;

                $uploadDir = dirname(__DIR__)."/uploads/semester_fee/";

                if(!is_dir($uploadDir)){
                    mkdir($uploadDir,0777,true);
                }

                move_uploaded_file(
                    $_FILES['slip']['tmp_name'],
                    $uploadDir.$slip
                );

            }else{

                $message="Invalid file format.";
                $type="danger";

            }

        }

        /*=============================
        SAVE PAYMENT
        =============================*/

        if($message==""){

            $status="Pending";

            $insert=$conn->prepare("

            INSERT INTO semester_fee_payments

            (

            student_id,

            application_id,

            registration_no,

            amount,

            bank_name,

            transaction_id,

            slip,

            payment_date,

            status

            )

            VALUES

            (?,?,?,?,?,?,?,?,?)

            ");

            $insert->bind_param(

                "issdsssss",

                $student_id,

                $application['application_id'],

                $application['registration_no'],

                $totalFee,

                $bank_name,

                $transaction_id,

                $slip,

                $payment_date,

                $status

            );

            if($insert->execute()){

                header("Location: pay_semester_fee.php?success=1");
                exit;

            }else{

                die($conn->error);

            }

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Pay Semester Fee | GTU ERP</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial,Helvetica,sans-serif;

}

.main-card{

margin-top:40px;

border:none;

border-radius:18px;

box-shadow:0 10px 25px rgba(0,0,0,.12);

overflow:hidden;

}

.header{

background:#0B1F52;

color:#fff;

padding:25px;

text-align:center;

}

.header img{

width:90px;

height:90px;

margin-bottom:10px;

}

.bank-card{

background:#f8f9fa;

border-left:6px solid #0B1F52;

padding:20px;

border-radius:10px;

margin-bottom:20px;

}

.info-card{

background:#ffffff;

border:1px solid #dee2e6;

padding:20px;

border-radius:10px;

margin-bottom:20px;

}

.info-card h5{

margin-bottom:15px;

color:#0B1F52;

font-weight:bold;

}

.table td{

padding:12px;

vertical-align:middle;

}

.amount{

font-size:28px;

font-weight:bold;

color:#198754;

}

</style>

</head>

<body>

<div class="container">

<div class="card main-card">

<div class="header">

<img src="../assets/images/logo.png">

<h2>Global Tech University</h2>

<p class="mb-0">

Semester Fee Payment Portal

</p>

</div>

<div class="card-body">

<?php if($message!=""){ ?>

<div class="alert alert-<?php echo $type; ?>">

<?php echo $message; ?>

</div>

<?php } ?>

<div class="row">

<div class="col-lg-6">

<div class="bank-card">

<h4>

<i class="fas fa-university me-2"></i>

Bank Details

</h4>

<hr>

<table class="table">

<tr>

<td><b>Bank Name</b></td>

<td>Meezan Bank Limited</td>

</tr>

<tr>

<td><b>Account Title</b></td>

<td>Ali Hamza</td>

</tr>

<tr>

<td><b>Account Number</b></td>

<td>98690111864042</td>

</tr>

<tr>

<td><b>IBAN</b></td>

<td>PK87MEEZ0001234567890123</td>

</tr>

<tr>

<td><b>Branch</b></td>

<td>Main Campus Branch</td>

</tr>

</table>

</div>

</div>

<div class="col-lg-6">

<div class="info-card">

<h5>

<i class="fas fa-user-graduate me-2"></i>

Student Information

</h5>

<table class="table">

<tr>

<td><b>Name</b></td>

<td>

<?php

echo $studentData['first_name']." ".$studentData['last_name'];

?>

</td>

</tr>

<tr>

<td><b>Email</b></td>

<td>

<?php echo $studentData['email']; ?>

</td>

</tr>

<tr>

<td><b>Application ID</b></td>

<td>

<?php echo $application['application_id']; ?>

</td>

</tr>

<tr>

<td><b>Registration No</b></td>

<td>

<?php

echo empty($application['registration_no'])

?

"<span class='badge bg-warning'>Not Generated</span>"

:

$application['registration_no'];

?>

</td>

</tr>

<tr>

<td><b>Roll No</b></td>

<td>

<?php

echo empty($application['roll_no'])

?

"<span class='badge bg-secondary'>Not Issued</span>"

:

$application['roll_no'];

?>

</td>

</tr>

</table>

</div>

</div>

</div>

<div class="row mt-4">

<div class="col-lg-6">

<div class="card border-success mb-4">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-money-bill-wave me-2"></i>

Semester Fee Details

</h5>

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th>Admission Fee</th>

<td>Rs. <?php echo number_format($feeData['admission_fee']); ?></td>

</tr>

<tr>

<th>Tuition Fee</th>

<td>Rs. <?php echo number_format($feeData['tuition_fee']); ?></td>

</tr>

<tr>

<th>Exam Fee</th>

<td>Rs. <?php echo number_format($feeData['exam_fee']); ?></td>

</tr>

<tr>

<th>Library Fee</th>

<td>Rs. <?php echo number_format($feeData['library_fee']); ?></td>

</tr>

<tr>

<th>Lab Fee</th>

<td>Rs. <?php echo number_format($feeData['lab_fee']); ?></td>

</tr>

<tr>

<th>Other Fee</th>

<td>Rs. <?php echo number_format($feeData['other_fee']); ?></td>

</tr>

<tr class="table-success">

<th>Total Semester Fee</th>

<th class="text-success">

Rs. <?php echo number_format($totalFee); ?>

</th>

</tr>

</table>

</div>

</div>

</div>

<div class="col-lg-6">

<div class="card border-primary mb-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-circle-info me-2"></i>

Payment Instructions

</h5>

</div>

<div class="card-body">

<ul class="mb-0">

<li>Deposit the Semester Fee only in <b>Meezan Bank Limited</b>.</li>

<li>Write your <b>Registration Number</b> on the deposit slip.</li>

<li>Upload a clear receipt image after payment.</li>

<li>Allowed file types: JPG, JPEG, PNG, PDF.</li>

<li>Maximum file size: 5 MB.</li>

<li>Finance Office will verify your payment within 24-48 hours.</li>

<li>After approval, your <b>Roll Number</b> will automatically appear.</li>

<li>You will then be able to print your Admission Letter and Student ID Card.</li>

</ul>

</div>

</div>

<div class="alert alert-warning">

<h5>

<i class="fas fa-triangle-exclamation me-2"></i>

Important Notice

</h5>

<hr>

Do not upload fake receipts.

Fake or edited receipts may result in cancellation of your admission.

</div>

</div>

</div>
<?php

/*=========================================
IF PAYMENT NOT SUBMITTED
=========================================*/

if(!$alreadyPaid){

?>

<div class="card border-primary mt-4">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">

<i class="fas fa-upload me-2"></i>

Upload Semester Fee Receipt

</h4>

</div>

<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Bank Name

</label>

<input

type="text"

name="bank_name"

class="form-control"

value="Meezan Bank Limited"

readonly>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Transaction ID

</label>

<input

type="text"

name="transaction_id"

class="form-control"

placeholder="Enter Bank Transaction ID"

required>

</div>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Deposit Date

</label>

<input

type="date"

name="payment_date"

class="form-control"

required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Semester Fee Amount

</label>

<input

type="text"

class="form-control"

value="Rs. <?php echo number_format($totalFee); ?>"

readonly>

</div>

</div>

<div class="mb-4">

<label class="form-label">

Upload Paid Bank Slip

</label>

<input

type="file"

name="slip"

class="form-control"

accept=".jpg,.jpeg,.png,.pdf"

required>

<small class="text-muted">

Allowed: JPG, JPEG, PNG, PDF (Maximum 5MB)

</small>

</div>

<div class="text-center">

<button

type="submit"

name="submit_fee"

class="btn btn-success btn-lg px-5">

<i class="fas fa-paper-plane me-2"></i>

Submit Semester Fee

</button>

</div>

</form>

</div>

</div>

<?php

}

?>

<?php

/*=========================================
PAYMENT STATUS DISPLAY
=========================================*/

if($alreadyPaid){

?>

<div class="card mt-4">

<?php

if($paymentData['status']=="Pending"){

?>

<div class="card-header bg-warning text-dark">

<h4 class="mb-0">

<i class="fas fa-clock me-2"></i>

Semester Fee Verification Pending

</h4>

</div>

<div class="card-body">

<div class="alert alert-warning">

<h5>

Your payment is under verification.

</h5>

<p>

Finance Office is checking your uploaded receipt.

Please wait for approval.

</p>

</div>

<table class="table table-bordered">

<tr>

<th>Amount</th>

<td>

Rs. <?php echo number_format($paymentData['amount']); ?>

</td>

</tr>


<tr>

<th>Bank</th>

<td>

<?php echo $paymentData['bank_name']; ?>

</td>

</tr>


<tr>

<th>Transaction ID</th>

<td>

<?php echo $paymentData['transaction_id']; ?>

</td>

</tr>


<tr>

<th>Status</th>

<td>

<span class="badge bg-warning text-dark">

Pending

</span>

</td>

</tr>

</table>

</div>


<?php

}

elseif($paymentData['status']=="Approved"){

?>


<div class="card-header bg-success text-white">

<h4 class="mb-0">

<i class="fas fa-circle-check me-2"></i>

Semester Fee Approved

</h4>

</div>


<div class="card-body">


<div class="alert alert-success">

<h5>

Congratulations 🎉

</h5>

<p>

Your semester fee has been verified by Finance Office.

You are now officially enrolled.

</p>

</div>


<table class="table table-bordered">


<tr>

<th>Registration Number</th>

<td>

<?php

echo !empty($application['registration_no'])

?

$application['registration_no']

:

"Not Generated";

?>

</td>

</tr>


<tr>

<th>Roll Number</th>

<td>

<?php

echo !empty($application['roll_no'])

?

$application['roll_no']

:

"Not Generated";

?>

</td>

</tr>


<tr>

<th>Fee Amount</th>

<td>

Rs. <?php echo number_format($paymentData['amount']); ?>

</td>

</tr>


<tr>

<th>Verified Date</th>

<td>

<?php

echo date(

"d M Y",

strtotime($paymentData['verified_at'])

);

?>

</td>

</tr>


</table>


<div class="text-center mt-3">


<a href="print_fee_receipt.php?id=<?php echo $paymentData['id']; ?>"

class="btn btn-primary">

<i class="fas fa-print me-2"></i>

Print Fee Receipt

</a>


<a href="student_id_card.php"

class="btn btn-success">

<i class="fas fa-id-card me-2"></i>

Student ID Card

</a>


</div>


</div>


<?php

}

elseif($paymentData['status']=="Rejected"){

?>


<div class="card-header bg-danger text-white">

<h4 class="mb-0">

<i class="fas fa-times-circle me-2"></i>

Payment Rejected

</h4>

</div>


<div class="card-body">


<div class="alert alert-danger">

<h5>

Your payment was rejected.

</h5>

<p>

Please upload a valid bank receipt again.

</p>

</div>


<a href="pay_semester_fee.php"

class="btn btn-danger">

Upload Again

</a>


</div>


<?php

}

?>


</div>


<?php

}

?>

</div>

</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>

/* File Size Check */

document.querySelector("input[type='file']")?.addEventListener("change",function(){

let file=this.files[0];

if(file){

let size=file.size / 1024 / 1024;

if(size > 5){

alert("File size must be less than 5MB");

this.value="";

}

}

});


</script>


</body>

</html>