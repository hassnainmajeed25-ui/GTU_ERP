<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../vendor/autoload.php';
require_once '../config/mail.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$type = "";


/* =========================================================
   FORM SUBMISSION
========================================================= */

if (isset($_POST['send_otp'])) {

    $email = trim($_POST['email'] ?? "");
    $cnic  = trim($_POST['cnic'] ?? "");


    /* =====================================================
       BASIC VALIDATION
    ===================================================== */

    if ($email === "" || $cnic === "") {

        $message = "Please enter your registered email and CNIC.";
        $type = "danger";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $type = "danger";

    } else {


        /* =================================================
           FIND STUDENT
        ================================================= */

        $stmt = $conn->prepare(
            "SELECT id, first_name, last_name, email
             FROM students
             WHERE email = ? AND cnic = ?
             LIMIT 1"
        );


        if (!$stmt) {

            $message = "Database error: " . $conn->error;
            $type = "danger";

        } else {

            $stmt->bind_param(
                "ss",
                $email,
                $cnic
            );

            $stmt->execute();

            $result = $stmt->get_result();

            $student = $result->fetch_assoc();

            $stmt->close();


            /* =============================================
               STUDENT NOT FOUND
            ============================================= */

            if (!$student) {

                $message =
                    "Email or CNIC is incorrect.";

                $type = "warning";

            } else {


                /* =========================================
                   DELETE OLD OTP
                ========================================= */

                $deleteOld = $conn->prepare(
                    "DELETE FROM student_password_reset_otp
                     WHERE student_id = ?"
                );


                if ($deleteOld) {

                    $deleteOld->bind_param(
                        "i",
                        $student['id']
                    );

                    $deleteOld->execute();

                    $deleteOld->close();
                }


                /* =========================================
                   GENERATE 6 DIGIT OTP
                ========================================= */

                try {

                    $otp = (string) random_int(
                        100000,
                        999999
                    );

                } catch (Exception $e) {

                    $otp = (string) mt_rand(
                        100000,
                        999999
                    );
                }


                /* =========================================
                   HASH OTP
                ========================================= */

                $otpHash = password_hash(
                    $otp,
                    PASSWORD_DEFAULT
                );


                /* =========================================
                   OTP EXPIRY
                   10 MINUTES
                ========================================= */

                $expires = date(
                    "Y-m-d H:i:s",
                    time() + (10 * 60)
                );


                /* =========================================
                   SAVE OTP
                ========================================= */

                $insertOtp = $conn->prepare(
                    "INSERT INTO student_password_reset_otp
                    (
                        student_id,
                        email,
                        otp_hash,
                        otp_expires_at,
                        attempts
                    )
                    VALUES (?, ?, ?, ?, 0)"
                );


                if (!$insertOtp) {

                    $message =
                        "Could not create OTP: " .
                        $conn->error;

                    $type = "danger";

                } else {

                    $insertOtp->bind_param(
                        "isss",
                        $student['id'],
                        $email,
                        $otpHash,
                        $expires
                    );


                    if (!$insertOtp->execute()) {

                        $message =
                            "Could not save OTP: " .
                            $insertOtp->error;

                        $type = "danger";

                        $insertOtp->close();

                    } else {

                        $insertOtp->close();


                        /* =================================
                           SEND EMAIL
                        ================================= */

                        $mail = new PHPMailer(true);


                        try {

                            $mail->isSMTP();

                            $mail->Host = SMTP_HOST;

                            $mail->SMTPAuth = true;

                            $mail->Username =
                                SMTP_USERNAME;

                            $mail->Password =
                                SMTP_PASSWORD;

                            $mail->SMTPSecure =
                                PHPMailer::ENCRYPTION_STARTTLS;

                            $mail->Port =
                                SMTP_PORT;


                            /* -----------------------------
                               SENDER
                            ----------------------------- */

                            $mail->setFrom(
                                SMTP_FROM_EMAIL,
                                SMTP_FROM_NAME
                            );


                            /* -----------------------------
                               RECEIVER
                            ----------------------------- */

                            $mail->addAddress(
                                $student['email'],
                                $student['first_name'] .
                                " " .
                                $student['last_name']
                            );


                            $mail->isHTML(true);


                            $mail->Subject =
                                "GTU ERP - Password Reset OTP";


                            /* -----------------------------
                               EMAIL BODY
                            ----------------------------- */

                            $mail->Body = '

                            <!DOCTYPE html>

                            <html>

                            <head>

                                <meta charset="UTF-8">

                                <title>
                                    GTU Password Reset
                                </title>

                            </head>


                            <body style="
                                margin:0;
                                padding:0;
                                background:#f4f6f9;
                                font-family:Arial,Helvetica,sans-serif;
                            ">


                                <div style="
                                    padding:40px 15px;
                                ">


                                    <div style="
                                        max-width:600px;
                                        margin:auto;
                                        background:#ffffff;
                                        border-radius:18px;
                                        overflow:hidden;
                                        box-shadow:
                                        0 10px 35px
                                        rgba(0,0,0,.10);
                                    ">


                                        <div style="
                                            background:#0B1F52;
                                            padding:30px;
                                            text-align:center;
                                        ">

                                            <h1 style="
                                                color:#FDB913;
                                                margin:0;
                                                font-size:28px;
                                            ">
                                                Global Tech University
                                            </h1>

                                            <p style="
                                                color:#dbe4f0;
                                                margin:8px 0 0;
                                            ">
                                                Student ERP Portal
                                            </p>

                                        </div>


                                        <div style="
                                            padding:35px;
                                            text-align:center;
                                        ">


                                            <h2 style="
                                                color:#111827;
                                            ">
                                                Password Reset
                                            </h2>


                                            <p style="
                                                color:#4b5563;
                                                line-height:1.6;
                                            ">

                                                Hello <strong>' .
                                                htmlspecialchars(
                                                    $student['first_name'] .
                                                    " " .
                                                    $student['last_name']
                                                ) .
                                                '</strong>,

                                            </p>


                                            <p style="
                                                color:#4b5563;
                                                line-height:1.6;
                                            ">

                                                We received a request to
                                                reset your GTU ERP password.

                                            </p>


                                            <p style="
                                                color:#4b5563;
                                                line-height:1.6;
                                            ">

                                                Enter this 6-digit verification
                                                code to continue:

                                            </p>


                                            <div style="
                                                display:inline-block;
                                                background:#FDB913;
                                                color:#111827;
                                                font-size:34px;
                                                font-weight:bold;
                                                letter-spacing:10px;
                                                padding:18px 25px;
                                                border-radius:12px;
                                                margin:20px 0;
                                            ">

                                                ' . $otp . '

                                            </div>


                                            <p style="
                                                color:#6b7280;
                                                font-size:14px;
                                            ">

                                                This code will expire in
                                                <strong>
                                                    10 minutes
                                                </strong>.

                                            </p>


                                            <p style="
                                                color:#9ca3af;
                                                font-size:12px;
                                                margin-top:30px;
                                            ">

                                                If you did not request a
                                                password reset, you can safely
                                                ignore this email.

                                            </p>


                                        </div>


                                        <div style="
                                            background:#f8fafc;
                                            padding:18px;
                                            text-align:center;
                                            color:#6b7280;
                                            font-size:12px;
                                        ">

                                            © ' . date("Y") . '
                                            Global Tech University.
                                            All rights reserved.

                                        </div>


                                    </div>


                                </div>


                            </body>

                            </html>

                            ';


                            $mail->AltBody =
                                "GTU ERP Password Reset\n\n" .
                                "Your verification code is: " .
                                $otp .
                                "\n\nThis code expires in 10 minutes.";


                            $mail->send();


                            /* =================================
                               SAVE SESSION
                            ================================= */

                            $_SESSION[
                                'password_reset_email'
                            ] = $email;


                            $_SESSION[
                                'password_reset_student_id'
                            ] = $student['id'];


                            /* =================================
                               GO TO OTP PAGE
                            ================================= */

                            header(
                                "Location: reset-password.php"
                            );

                            exit;


                        } catch (Exception $e) {


                            /* =============================
                               DELETE OTP IF EMAIL FAILED
                            ============================= */

                            $deleteFailed = $conn->prepare(
                                "DELETE FROM student_password_reset_otp
                                 WHERE student_id = ?"
                            );


                            if ($deleteFailed) {

                                $deleteFailed->bind_param(
                                    "i",
                                    $student['id']
                                );

                                $deleteFailed->execute();

                                $deleteFailed->close();
                            }


                            $message =
                                "OTP email failed: " .
                                $mail->ErrorInfo;

                            $type = "danger";
                        }
                    }
                }
            }
        }
    }
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

<title>
    Forgot Password | GTU
</title>


<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>


<link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    rel="stylesheet"
>


<style>

body {

    background:
        linear-gradient(
            rgba(8,20,55,.90),
            rgba(8,20,55,.90)
        ),
        url('../assets/images/hero-building.jpg');

    background-size:cover;

    background-position:center;

    min-height:100vh;

    display:flex;

    justify-content:center;

    align-items:center;

    padding:30px;
}


.reset-card {

    background:#fff;

    border-radius:20px;

    box-shadow:
        0 20px 60px
        rgba(0,0,0,.35);

    overflow:hidden;
}


.left-panel {

    background:#0B1F52;

    color:#fff;

    padding:50px 35px;

    height:100%;
}


.left-panel img {

    width:90px;

    margin-bottom:20px;
}


.right-panel {

    padding:45px;
}


.form-control {

    height:52px;

    border-radius:12px;
}


.form-control:focus {

    border-color:#FDB913;

    box-shadow:
        0 0 0 .2rem
        rgba(253,185,19,.20);
}


.btn-reset {

    height:55px;

    border:none;

    border-radius:50px;

    background:#FDB913;

    font-weight:700;

    width:100%;

}


.btn-reset:hover {

    background:#e3a500;
}


.step-box {

    background:#f8fafc;

    border-left:
        4px solid #FDB913;

    padding:15px;

    border-radius:10px;

    margin-top:20px;

    color:#64748b;

    font-size:14px;
}


@media(max-width:767px) {

    .left-panel {

        padding:35px 25px;

    }

    .right-panel {

        padding:30px 20px;

    }

}

</style>

</head>


<body>


<div class="container">


<div class="row justify-content-center">


<div class="col-lg-9">


<div class="reset-card">


<div class="row g-0">


<!-- LEFT -->

<div class="col-lg-5">


<div class="left-panel">


<img
    src="../assets/images/logo.png"
    alt="GTU Logo"
>


<h2>
    Forgot Password?
</h2>


<p class="mt-3">

Enter your registered email and CNIC.
We will send a 6-digit verification
code to your email.

</p>


<hr>


<div class="step-box">

<i class="fas fa-shield-halved me-2"></i>

Your password will only be changed
after successful email verification.

</div>


<a
    href="login.php"
    class="btn btn-light mt-4"
>

<i class="fas fa-arrow-left me-2"></i>

Back to Login

</a>


</div>


</div>


<!-- RIGHT -->

<div class="col-lg-7">


<div class="right-panel">


<h3 class="mb-2">

<i class="fas fa-key me-2"></i>

Forgot Password

</h3>


<p class="text-muted mb-4">

Verify your identity to reset your password.

</p>


<?php if ($message != ""): ?>

<div class="alert alert-<?php
echo htmlspecialchars($type);
?>">

<?php
echo htmlspecialchars($message);
?>

</div>

<?php endif; ?>


<form method="POST">


<div class="mb-3">


<label class="form-label fw-bold">

Email Address

</label>


<input
    type="email"
    name="email"
    class="form-control"
    placeholder="Enter your registered email"
    value="<?php
        echo htmlspecialchars(
            $_POST['email'] ?? ''
        );
    ?>"
    required
>


</div>


<div class="mb-4">


<label class="form-label fw-bold">

CNIC / B-Form

</label>


<input
    type="text"
    name="cnic"
    class="form-control"
    placeholder="XXXXX-XXXXXXX-X"
    value="<?php
        echo htmlspecialchars(
            $_POST['cnic'] ?? ''
        );
    ?>"
    required
>


</div>


<button
    type="submit"
    name="send_otp"
    class="btn-reset"
>

<i class="fas fa-paper-plane me-2"></i>

Send Verification Code

</button>


</form>


<div class="text-center mt-4">

<small class="text-muted">

A 6-digit code will be sent to your
registered email.

</small>

</div>


</div>


</div>


</div>


</div>


</div>


</div>


</div>


</body>

</html>