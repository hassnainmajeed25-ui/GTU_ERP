<?php

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/mail.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/* =========================================================
   MESSAGE VARIABLES
========================================================= */

$message = "";
$messageType = "";


/* =========================================================
   FORM SUBMISSION
========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // -----------------------------------------------------
    // GET FORM DATA
    // -----------------------------------------------------

    $first_name  = trim($_POST["first_name"] ?? "");
    $last_name   = trim($_POST["last_name"] ?? "");
    $father_name  = trim($_POST["father_name"] ?? "");
    $email       = trim($_POST["email"] ?? "");
    $mobile      = trim($_POST["mobile"] ?? "");
    $cnic        = trim($_POST["cnic"] ?? "");
    $password    = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    // -----------------------------------------------------
    // VALIDATION
    // -----------------------------------------------------

    if (
        $first_name === "" ||
        $last_name === "" ||
        $father_name === "" ||
        $email === "" ||
        $mobile === "" ||
        $cnic === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {

        $message = "Please fill all required fields.";
        $messageType = "danger";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $messageType = "danger";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $messageType = "danger";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $messageType = "danger";

    } else {

        /* =================================================
           CHECK EXISTING STUDENT
        ================================================= */

        $checkStudent = $conn->prepare(
            "SELECT id
             FROM students
             WHERE email = ? OR cnic = ?
             LIMIT 1"
        );

        if (!$checkStudent) {

            $message = "Database error: " . $conn->error;
            $messageType = "danger";

        } else {

            $checkStudent->bind_param(
                "ss",
                $email,
                $cnic
            );

            $checkStudent->execute();

            $result = $checkStudent->get_result();

            if ($result->num_rows > 0) {

                $message = "An account already exists with this email or CNIC.";
                $messageType = "danger";

                $checkStudent->close();

            } else {

                $checkStudent->close();


                /* =========================================
                   DELETE OLD PENDING REGISTRATION
                ========================================= */

                $deleteOld = $conn->prepare(
                    "DELETE FROM student_registration_otp
                     WHERE email = ?"
                );

                if ($deleteOld) {

                    $deleteOld->bind_param(
                        "s",
                        $email
                    );

                    $deleteOld->execute();
                    $deleteOld->close();
                }


                /* =========================================
                   GENERATE OTP
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

                $otp_hash = password_hash(
                    $otp,
                    PASSWORD_DEFAULT
                );


                /* =========================================
                   HASH PASSWORD
                ========================================= */

                $password_hash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                /* =========================================
                   OTP EXPIRY
                   10 MINUTES
                ========================================= */

                $otp_expires_at = date(
                    "Y-m-d H:i:s",
                    time() + (10 * 60)
                );


                /* =========================================
                   SAVE PENDING REGISTRATION
                ========================================= */

                $insert = $conn->prepare(
                    "INSERT INTO student_registration_otp
                    (
                        first_name,
                        last_name,
                        father_name,
                        email,
                        mobile,
                        cnic,
                        password_hash,
                        otp_hash,
                        otp_expires_at,
                        attempts
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)"
                );


                if (!$insert) {

                    $message = "Database error: " . $conn->error;
                    $messageType = "danger";

                } else {

                    $insert->bind_param(
                        "sssssssss",
                        $first_name,
                        $last_name,
                        $father_name,
                        $email,
                        $mobile,
                        $cnic,
                        $password_hash,
                        $otp_hash,
                        $otp_expires_at
                    );


                    if (!$insert->execute()) {

                        $message = "Could not save registration: " . $insert->error;
                        $messageType = "danger";

                        $insert->close();

                    } else {

                        $insert->close();


                        /* =================================
                           SEND OTP EMAIL
                        ================================= */

                        $mail = new PHPMailer(true);


                        try {

                            // ---------------------------------
                            // SMTP SETTINGS
                            // ---------------------------------

                            $mail->isSMTP();

                            $mail->Host = SMTP_HOST;

                            $mail->SMTPAuth = true;

                            $mail->Username = SMTP_USERNAME;

                            $mail->Password = SMTP_PASSWORD;

                            $mail->SMTPSecure =
                                PHPMailer::ENCRYPTION_STARTTLS;

                            $mail->Port = SMTP_PORT;


                            // ---------------------------------
                            // SENDER
                            // ---------------------------------

                            $mail->setFrom(
                                SMTP_FROM_EMAIL,
                                SMTP_FROM_NAME
                            );


                            // ---------------------------------
                            // RECEIVER
                            // ---------------------------------

                            $mail->addAddress(
                                $email,
                                $first_name . " " . $last_name
                            );


                            // ---------------------------------
                            // EMAIL FORMAT
                            // ---------------------------------

                            $mail->isHTML(true);


                            $mail->Subject =
                                "GTU ERP - Email Verification OTP";


                            // ---------------------------------
                            // EMAIL BODY
                            // ---------------------------------

                            $mail->Body = '
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <meta charset="UTF-8">
                                <title>GTU ERP OTP</title>
                            </head>

                            <body style="
                                margin:0;
                                padding:0;
                                background:#f4f6f9;
                                font-family:Arial,Helvetica,sans-serif;
                            ">

                                <div style="
                                    width:100%;
                                    padding:40px 0;
                                ">

                                    <div style="
                                        max-width:600px;
                                        margin:auto;
                                        background:#ffffff;
                                        border-radius:16px;
                                        overflow:hidden;
                                        box-shadow:0 10px 30px rgba(0,0,0,0.10);
                                    ">

                                        <div style="
                                            background:#0b1220;
                                            padding:28px;
                                            text-align:center;
                                        ">

                                            <h1 style="
                                                color:#f5c542;
                                                margin:0;
                                                font-size:28px;
                                            ">
                                                Global Tech University
                                            </h1>

                                            <p style="
                                                color:#dbe4f0;
                                                margin:8px 0 0;
                                                font-size:14px;
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
                                                margin-top:0;
                                            ">
                                                Email Verification
                                            </h2>

                                            <p style="
                                                color:#4b5563;
                                                font-size:15px;
                                                line-height:1.6;
                                            ">
                                                Hello <strong>' .
                                                htmlspecialchars(
                                                    $first_name . " " . $last_name
                                                ) .
                                                '</strong>,
                                            </p>

                                            <p style="
                                                color:#4b5563;
                                                font-size:15px;
                                                line-height:1.6;
                                            ">
                                                Use the following OTP to verify
                                                your GTU student account:
                                            </p>


                                            <div style="
                                                display:inline-block;
                                                background:#f5c542;
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
                                                line-height:1.6;
                                            ">
                                                This OTP will expire in
                                                <strong>10 minutes</strong>.
                                            </p>


                                            <p style="
                                                color:#9ca3af;
                                                font-size:12px;
                                                margin-top:30px;
                                            ">
                                                If you did not request this
                                                registration, please ignore
                                                this email.
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


                            // ---------------------------------
                            // PLAIN TEXT VERSION
                            // ---------------------------------

                            $mail->AltBody =
                                "GTU ERP Email Verification\n\n" .
                                "Your OTP is: " . $otp . "\n\n" .
                                "This OTP will expire in 10 minutes.";


                            // ---------------------------------
                            // SEND
                            // ---------------------------------

                            $mail->send();


                            /* =================================
                               SAVE SESSION
                            ================================= */

                            $_SESSION["verify_email"] = $email;


                            /* =================================
                               REDIRECT TO OTP PAGE
                            ================================= */

                            header(
                                "Location: verify-otp.php"
                            );

                            exit;


                        } catch (Exception $e) {


                            /* =============================
                               DELETE FAILED PENDING RECORD
                            ============================= */

                            $deleteFailed = $conn->prepare(
                                "DELETE FROM student_registration_otp
                                 WHERE email = ?"
                            );

                            if ($deleteFailed) {

                                $deleteFailed->bind_param(
                                    "s",
                                    $email
                                );

                                $deleteFailed->execute();

                                $deleteFailed->close();
                            }


                            /* =============================
                               SHOW ACTUAL SMTP ERROR
                            ============================= */

                            $message =
                                "OTP email failed: " .
                                $mail->ErrorInfo;

                            $messageType = "danger";
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
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Student Registration | GTU ERP
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background:
                linear-gradient(
                    135deg,
                    rgba(5, 12, 24, 0.95),
                    rgba(13, 27, 49, 0.93)
                ),
                url("../assets/images/hero-building.jpg");

            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }


        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 45px 15px;
        }


        .register-card {
            width: 100%;
            max-width: 900px;
            background: rgba(255,255,255,0.98);
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 30px 70px rgba(0,0,0,0.35);
        }


        .top-section {
            background: #0b1220;
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 4px solid #f5c542;
        }


        .logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
            background: white;
            padding: 8px;
            border-radius: 50%;
            margin-bottom: 15px;
        }


        .top-section h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
        }


        .top-section p {
            margin: 8px 0 0;
            color: #cbd5e1;
        }


        .form-section {
            padding: 35px;
        }


        .section-title {
            font-weight: 800;
            color: #0b1220;
            margin-bottom: 25px;
        }


        .form-label {
            font-weight: 700;
            color: #263244;
        }


        .form-control {
            height: 50px;
            border-radius: 10px;
            border: 1px solid #d5dbe5;
            padding-left: 15px;
        }


        .form-control:focus {
            border-color: #f5c542;
            box-shadow:
                0 0 0 0.2rem rgba(245,197,66,0.20);
        }


        .btn-register {
            width: 100%;
            height: 52px;
            border: none;
            border-radius: 11px;
            background: #f5c542;
            color: #111827;
            font-weight: 800;
            font-size: 16px;
            transition: 0.25s;
        }


        .btn-register:hover {
            background: #e6b52e;
            transform: translateY(-2px);
        }


        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #64748b;
        }


        .login-link a {
            color: #0b5ed7;
            text-decoration: none;
            font-weight: 700;
        }


        .required {
            color: #dc3545;
        }


        .alert {
            border-radius: 12px;
            font-weight: 600;
        }


        .security-note {
            margin-top: 25px;
            padding: 15px;
            background: #f8fafc;
            border-left: 4px solid #f5c542;
            border-radius: 8px;
            color: #64748b;
            font-size: 13px;
        }


        @media (max-width: 768px) {

            .form-section {
                padding: 25px 20px;
            }

            .top-section {
                padding: 25px 15px;
            }

            .top-section h1 {
                font-size: 23px;
            }

        }

    </style>

</head>


<body>


<div class="page-wrapper">


    <div class="register-card">


        <!-- HEADER -->

        <div class="top-section">

            <img
                src="../assets/images/logo.png"
                alt="GTU Logo"
                class="logo"
                onerror="this.style.display='none';"
            >

            <h1>
                Create Student Account
            </h1>

            <p>
                Register for the Global Tech University ERP Portal
            </p>

        </div>


        <!-- FORM -->

        <div class="form-section">


            <?php if ($message !== ""): ?>

                <div
                    class="alert alert-<?php echo htmlspecialchars($messageType); ?>"
                    role="alert"
                >

                    <i class="fa-solid fa-circle-exclamation me-2"></i>

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>

            <?php endif; ?>


            <h3 class="section-title">

                <i class="fa-solid fa-user-plus me-2"></i>

                Student Registration

            </h3>


            <form
                method="POST"
                action=""
                autocomplete="off"
            >


                <div class="row g-3">


                    <!-- FIRST NAME -->

                    <div class="col-md-6">

                        <label class="form-label">

                            First Name
                            <span class="required">*</span>

                        </label>

                        <input
                            type="text"
                            name="first_name"
                            class="form-control"
                            value="<?php echo htmlspecialchars($_POST["first_name"] ?? ""); ?>"
                            required
                        >

                    </div>


                    <!-- LAST NAME -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Last Name
                            <span class="required">*</span>

                        </label>

                        <input
                            type="text"
                            name="last_name"
                            class="form-control"
                            value="<?php echo htmlspecialchars($_POST["last_name"] ?? ""); ?>"
                            required
                        >

                    </div>


                    <!-- FATHER NAME -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Father's Name
                            <span class="required">*</span>

                        </label>

                        <input
                            type="text"
                            name="father_name"
                            class="form-control"
                            value="<?php echo htmlspecialchars($_POST["father_name"] ?? ""); ?>"
                            required
                        >

                    </div>


                    <!-- EMAIL -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Email Address
                            <span class="required">*</span>

                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
                            required
                        >

                    </div>


                    <!-- MOBILE -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Mobile Number
                            <span class="required">*</span>

                        </label>

                        <input
                            type="text"
                            name="mobile"
                            class="form-control"
                            placeholder="03XXXXXXXXX"
                            value="<?php echo htmlspecialchars($_POST["mobile"] ?? ""); ?>"
                            required
                        >

                    </div>


                    <!-- CNIC -->

                    <div class="col-md-6">

                        <label class="form-label">

                            CNIC
                            <span class="required">*</span>

                        </label>

                        <input
                            type="text"
                            name="cnic"
                            class="form-control"
                            placeholder="XXXXX-XXXXXXX-X"
                            value="<?php echo htmlspecialchars($_POST["cnic"] ?? ""); ?>"
                            required
                        >

                    </div>


                    <!-- PASSWORD -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Password
                            <span class="required">*</span>

                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required
                        >

                    </div>


                    <!-- CONFIRM PASSWORD -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Confirm Password
                            <span class="required">*</span>

                        </label>

                        <input
                            type="password"
                            name="confirm_password"
                            class="form-control"
                            required
                        >

                    </div>


                    <!-- BUTTON -->

                    <div class="col-12 mt-4">

                        <button
                            type="submit"
                            class="btn-register"
                        >

                            <i class="fa-solid fa-envelope-circle-check me-2"></i>

                            Create Account & Send OTP

                        </button>

                    </div>


                </div>


            </form>


            <div class="security-note">

                <i class="fa-solid fa-shield-halved me-2"></i>

                Your password is securely encrypted. After registration,
                a 6-digit verification code will be sent to your email.
                Your student account will only be created after successful
                OTP verification.

            </div>


            <div class="login-link">

                Already have an account?

                <a href="login.php">
                    Login Here
                </a>

            </div>


        </div>


    </div>


</div>


</body>

</html>