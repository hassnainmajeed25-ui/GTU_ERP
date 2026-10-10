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

$student_id = (int) $_SESSION['student_id'];

/*=========================================
GET APPLICATION
=========================================*/

$app = $conn->prepare("
    SELECT
        id,
        application_id
    FROM applications
    WHERE student_id = ?
    LIMIT 1
");

$app->bind_param("i", $student_id);
$app->execute();

$appResult = $app->get_result();

if ($appResult->num_rows == 0) {
    die("Please complete your admission application first.");
}

$application = $appResult->fetch_assoc();

$application_db_id = (int) $application['id'];

/*=========================================
GET EXISTING PAYMENT
=========================================*/

$check = $conn->prepare("
    SELECT *
    FROM application_payments
    WHERE application_id = ?
    LIMIT 1
");

$check->bind_param("i", $application_db_id);
$check->execute();

$feeResult = $check->get_result();

$alreadyPaid = false;
$fee = [];

if ($feeResult->num_rows > 0) {

    $alreadyPaid = true;
    $fee = $feeResult->fetch_assoc();
}

/*=========================================
MESSAGE
=========================================*/

$message = "";
$type = "";

/*=========================================
FORM SUBMISSION
=========================================*/

if (isset($_POST['submit'])) {

    $payment_date = trim($_POST['payment_date'] ?? '');

    $processing_fee = 1050;

    /*-------------------------------------
    CHECK DATE
    -------------------------------------*/

    if (empty($payment_date)) {

        $message = "Please select the deposit date.";
        $type = "danger";

    }

    /*-------------------------------------
    CHECK FILE
    -------------------------------------*/

    elseif (
        !isset($_FILES['receipt_image']) ||
        $_FILES['receipt_image']['error'] !== UPLOAD_ERR_OK
    ) {

        $message = "Please select a receipt file.";
        $type = "danger";

    }

    else {

        $file = $_FILES['receipt_image'];

        /*-------------------------------------
        MAXIMUM FILE SIZE = 5 MB
        -------------------------------------*/

        if ($file['size'] > (5 * 1024 * 1024)) {

            $message = "Receipt file must be less than 5 MB.";
            $type = "danger";

        }

        else {

            /*-------------------------------------
            FILE EXTENSION
            -------------------------------------*/

            $ext = strtolower(
                pathinfo(
                    $file['name'],
                    PATHINFO_EXTENSION
                )
            );

            $allowed_extensions = [
                'jpg',
                'jpeg',
                'png',
                'pdf'
            ];

            if (!in_array($ext, $allowed_extensions, true)) {

                $message =
                    "Only JPG, JPEG, PNG and PDF files are allowed.";

                $type = "danger";

            }

            else {

                /*-------------------------------------
                UPLOAD DIRECTORY
                -------------------------------------*/

                $upload_dir = "../uploads/receipts/";

                if (!is_dir($upload_dir)) {

                    if (!mkdir($upload_dir, 0777, true)) {

                        $message =
                            "Unable to create receipt upload folder.";

                        $type = "danger";
                    }
                }

                /*-------------------------------------
                CONTINUE IF DIRECTORY EXISTS
                -------------------------------------*/

                if (
                    $type === ""
                    &&
                    is_dir($upload_dir)
                ) {

                    /*-------------------------------------
                    CREATE UNIQUE FILE NAME
                    -------------------------------------*/

                    $receipt_image =
                        "REC_" .
                        time() .
                        "_" .
                        bin2hex(random_bytes(4)) .
                        "." .
                        $ext;

                    $target_file =
                        $upload_dir . $receipt_image;

                    /*-------------------------------------
                    MOVE UPLOADED FILE
                    -------------------------------------*/

                    if (
                        !move_uploaded_file(
                            $file['tmp_name'],
                            $target_file
                        )
                    ) {

                        $message =
                            "Receipt upload failed. Please try again.";

                        $type = "danger";

                    }

                    else {

                        /*=================================
                        EXISTING PAYMENT RECORD
                        =================================*/

                        if ($alreadyPaid) {

                            $payment_id =
                                (int) $fee['id'];

                            $current_receipt =
                                trim(
                                    (string)
                                    ($fee['receipt_image'] ?? '')
                                );

                            $current_status =
                                trim(
                                    (string)
                                    ($fee['verification_status'] ?? '')
                                );

                            /*
                            Allow upload when:

                            1. Existing receipt is empty
                            OR
                            2. Payment was rejected
                            */

                            if (
                                $current_receipt === ""
                                ||
                                $current_status === "Rejected"
                            ) {

                                $update = $conn->prepare("
                                    UPDATE application_payments
                                    SET
                                        processing_fee = ?,
                                        payment_date = ?,
                                        receipt_image = ?,
                                        verification_status = 'Pending'
                                    WHERE id = ?
                                ");

                                $update->bind_param(
                                    "dssi",
                                    $processing_fee,
                                    $payment_date,
                                    $receipt_image,
                                    $payment_id
                                );

                                if ($update->execute()) {

                                    $message =
                                        "Receipt uploaded successfully. Your payment is now pending verification.";

                                    $type = "success";

                                    /*
                                    Reload page after 1.5 seconds
                                    */

                                    header(
                                        "Refresh:1.5"
                                    );

                                }

                                else {

                                    /*
                                    Database update failed,
                                    remove uploaded file.
                                    */

                                    if (
                                        file_exists($target_file)
                                    ) {

                                        unlink($target_file);
                                    }

                                    $message =
                                        "Receipt uploaded but could not be saved in the database.";

                                    $type = "danger";
                                }

                            }

                            else {

                                /*
                                Payment already has a receipt
                                and is not rejected.
                                */

                                if (
                                    file_exists($target_file)
                                ) {

                                    unlink($target_file);
                                }

                                $message =
                                    "Your processing fee receipt has already been submitted.";

                                $type = "warning";
                            }

                        }

                        /*=================================
                        NO PAYMENT RECORD
                        =================================*/

                        else {

                            $verification_status =
                                "Pending";

                            $insert = $conn->prepare("
                                INSERT INTO application_payments
                                (
                                    application_id,
                                    processing_fee,
                                    payment_date,
                                    receipt_image,
                                    verification_status
                                )
                                VALUES
                                (?, ?, ?, ?, ?)
                            ");

                            $insert->bind_param(
                                "idsss",
                                $application_db_id,
                                $processing_fee,
                                $payment_date,
                                $receipt_image,
                                $verification_status
                            );

                            if ($insert->execute()) {

                                $message =
                                    "Processing fee uploaded successfully. Your payment is now pending verification.";

                                $type = "success";

                                header(
                                    "Refresh:1.5"
                                );

                            }

                            else {

                                /*
                                Database insert failed,
                                remove uploaded file.
                                */

                                if (
                                    file_exists($target_file)
                                ) {

                                    unlink($target_file);
                                }

                                $message =
                                    "Unable to save processing fee payment.";

                                $type = "danger";
                            }
                        }
                    }
                }
            }
        }
    }
}

/*=========================================
GET LATEST PAYMENT DATA
=========================================*/

$check2 = $conn->prepare("
    SELECT *
    FROM application_payments
    WHERE application_id = ?
    LIMIT 1
");

$check2->bind_param("i", $application_db_id);
$check2->execute();

$feeResult2 = $check2->get_result();

$alreadyPaid = false;
$fee = [];

if ($feeResult2->num_rows > 0) {

    $alreadyPaid = true;
    $fee = $feeResult2->fetch_assoc();
}

/*=========================================
CAN UPLOAD?
=========================================*/

$canUpload = false;

if (!$alreadyPaid) {

    $canUpload = true;

}
elseif (
    empty($fee['receipt_image'])
    ||
    $fee['verification_status'] === "Rejected"
) {

    $canUpload = true;
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1"
>

<title>Processing Fee Payment</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet"
>

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet"
>

<style>

body{
    background:#eef2f7;
    font-family:Arial,Helvetica,sans-serif;
}

.card{
    margin-top:40px;
    margin-bottom:40px;
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.10);
    overflow:hidden;
}

.card-header{
    background:#0B1F52;
    color:#fff;
    font-size:26px;
    font-weight:bold;
    padding:20px;
    text-align:center;
}

label{
    font-weight:bold;
}

.info-box{
    border-radius:12px;
}

.receipt-preview{
    max-width:500px;
    max-height:650px;
    object-fit:contain;
    border:1px solid #ddd;
    border-radius:10px;
    padding:5px;
    background:#fff;
}

.status-box{
    border-radius:12px;
}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header">

<i class="fas fa-money-check-dollar me-2"></i>

Processing Fee Payment

</div>

<div class="card-body">

<!-- =========================================
APPLICATION INFORMATION
========================================= -->

<div class="alert alert-info info-box">

<h5>

<i class="fas fa-file-invoice me-2"></i>

Application ID:

<strong>

<?php
echo htmlspecialchars(
    $application['application_id']
);
?>

</strong>

</h5>

<hr>

<h4>

Processing Fee:

<span class="text-success">

Rs. 1,050

</span>

</h4>

<p class="mb-0">

Upload your paid bank receipt below.

</p>

</div>


<!-- =========================================
MESSAGE
========================================= -->

<?php if ($message !== ""): ?>

<div class="alert alert-<?php echo htmlspecialchars($type); ?>">

<i class="fas
<?php
echo $type === "success"
    ? " fa-circle-check"
    : " fa-circle-exclamation";
?>
me-2"></i>

<?php
echo htmlspecialchars($message);
?>

</div>

<?php endif; ?>


<!-- =========================================
UPLOAD FORM
========================================= -->

<?php if ($canUpload): ?>

<form
method="POST"
enctype="multipart/form-data"
>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Deposit Date

</label>

<input
type="date"
name="payment_date"
class="form-control"
required
>

</div>


<div class="col-md-6 mb-3">

<label class="form-label">

Bank Deposit Receipt

</label>

<input
type="file"
name="receipt_image"
class="form-control"
accept=".jpg,.jpeg,.png,.pdf"
required
>

<small class="text-muted">

Allowed: JPG, JPEG, PNG, PDF
<br>
Maximum size: 5 MB

</small>

</div>

</div>


<div class="mt-4">

<button
type="submit"
name="submit"
class="btn btn-primary"
>

<i class="fas fa-upload me-2"></i>

Upload Processing Fee

</button>


<a
href="dashboard.php"
class="btn btn-secondary"
>

<i class="fas fa-arrow-left me-2"></i>

Back to Dashboard

</a>

</div>

</form>


<?php else: ?>


<!-- =========================================
PAYMENT ALREADY SUBMITTED
========================================= -->

<div class="alert alert-light border status-box">

<h4>

<i class="fas fa-circle-check text-success me-2"></i>

Processing Fee Already Submitted

</h4>

<hr>


<p>

<strong>Processing Fee:</strong>

Rs.

<?php

echo number_format(
    (float)$fee['processing_fee'],
    2
);

?>

</p>


<p>

<strong>Deposit Date:</strong>

<?php

$date = !empty($fee['payment_date'])
    ? $fee['payment_date']
    : ($fee['submitted_at'] ?? date('Y-m-d'));

echo date(
    "d M Y",
    strtotime($date)
);

?>

</p>


<p>

<strong>Status:</strong>

<?php

if (
    $fee['verification_status'] === "Pending"
) {

    echo '
    <span class="badge bg-warning text-dark">
        Pending Verification
    </span>
    ';

}
elseif (
    $fee['verification_status'] === "Verified"
) {

    echo '
    <span class="badge bg-success">
        Verified
    </span>
    ';

}
elseif (
    $fee['verification_status'] === "Rejected"
) {

    echo '
    <span class="badge bg-danger">
        Rejected
    </span>
    ';

}
else {

    echo '
    <span class="badge bg-secondary">
        ' .
        htmlspecialchars(
            $fee['verification_status']
        )
        .
        '
    </span>
    ';

}

?>


<!-- =========================================
UPLOADED RECEIPT
========================================= -->

<?php if (!empty($fee['receipt_image'])): ?>

<hr>

<h5>

<i class="fas fa-file-image me-2"></i>

Uploaded Receipt

</h5>


<?php

$receipt_name =
    basename(
        $fee['receipt_image']
    );

$receipt_url =
    "../uploads/receipts/" .
    rawurlencode(
        $receipt_name
    );

$receipt_extension =
    strtolower(
        pathinfo(
            $receipt_name,
            PATHINFO_EXTENSION
        )
    );

?>


<?php if ($receipt_extension === "pdf"): ?>

<a
href="<?php echo $receipt_url; ?>"
target="_blank"
class="btn btn-danger"
>

<i class="fas fa-file-pdf me-2"></i>

View PDF Receipt

</a>

<?php else: ?>

<div class="mt-3">

<img
src="<?php echo $receipt_url; ?>"
class="img-fluid receipt-preview"
alt="Uploaded Payment Receipt"
>

</div>

<br>

<a
href="<?php echo $receipt_url; ?>"
target="_blank"
class="btn btn-primary"
>

<i class="fas fa-up-right-from-square me-2"></i>

Open Receipt

</a>

<?php endif; ?>


<?php else: ?>

<div class="alert alert-danger mt-3">

<i class="fas fa-triangle-exclamation me-2"></i>

<strong>No receipt is currently saved.</strong>

<br>

Please upload your bank receipt.

</div>

<?php endif; ?>


<br><br>


<a
href="dashboard.php"
class="btn btn-secondary"
>

<i class="fas fa-arrow-left me-2"></i>

Back to Dashboard

</a>

</div>

<?php endif; ?>

</div>

</div>

</div>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>