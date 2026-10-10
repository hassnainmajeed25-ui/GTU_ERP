<?php

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';


/* =========================================================
   CHECK VERIFICATION SESSION
========================================================= */

if (
    !isset($_SESSION["verify_email"]) ||
    empty($_SESSION["verify_email"])
) {
    header("Location: register.php");
    exit;
}


$email = trim($_SESSION["verify_email"]);

$message = "";
$messageType = "";


/* =========================================================
   GET PENDING REGISTRATION
========================================================= */

$stmt = $conn->prepare(
    "SELECT
        id,
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
     FROM student_registration_otp
     WHERE email = ?
     ORDER BY id DESC
     LIMIT 1"
);


if (!$stmt) {
    die("Database error: " . $conn->error);
}


$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

$pending = $result->fetch_assoc();

$stmt->close();


/* =========================================================
   PENDING RECORD NOT FOUND
========================================================= */

if (!$pending) {

    unset($_SESSION["verify_email"]);

    $message = "Registration session not found. Please register again.";
    $messageType = "danger";

}


/* =========================================================
   OTP FORM SUBMISSION
========================================================= */

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($pending)
) {

    $otp = trim($_POST["otp"] ?? "");


    /* =====================================================
       OTP FORMAT
    ===================================================== */

    if (!preg_match('/^[0-9]{6}$/', $otp)) {

        $message = "Please enter the 6-digit OTP.";
        $messageType = "danger";

    }

    /* =====================================================
       MAX ATTEMPTS
    ===================================================== */

    elseif ((int)$pending["attempts"] >= 5) {

        $message =
            "Too many incorrect OTP attempts. Please register again.";

        $messageType = "danger";

    }

    /* =====================================================
       OTP EXPIRY
    ===================================================== */

    elseif (
        strtotime($pending["otp_expires_at"]) < time()
    ) {

        $message =
            "This OTP has expired. Please register again.";

        $messageType = "danger";

    }

    /* =====================================================
       VERIFY OTP
    ===================================================== */

    elseif (
        !password_verify(
            $otp,
            $pending["otp_hash"]
        )
    ) {

        $newAttempts =
            (int)$pending["attempts"] + 1;


        $updateAttempts = $conn->prepare(
            "UPDATE student_registration_otp
             SET attempts = ?
             WHERE id = ?"
        );


        if ($updateAttempts) {

            $updateAttempts->bind_param(
                "ii",
                $newAttempts,
                $pending["id"]
            );

            $updateAttempts->execute();

            $updateAttempts->close();
        }


        $remaining =
            max(0, 5 - $newAttempts);


        $message =
            "Incorrect OTP. You have " .
            $remaining .
            " attempt(s) remaining.";

        $messageType = "danger";

    }

    /* =====================================================
       OTP CORRECT
    ===================================================== */

    else {

        /* ================================================
           CHECK DUPLICATE EMAIL
        ================================================ */

        $checkEmail = $conn->prepare(
            "SELECT id
             FROM students
             WHERE email = ?
             LIMIT 1"
        );


        if (!$checkEmail) {

            $message =
                "Database error: " .
                $conn->error;

            $messageType = "danger";

        } else {

            $checkEmail->bind_param(
                "s",
                $pending["email"]
            );

            $checkEmail->execute();

            $emailResult =
                $checkEmail->get_result();

            $emailExists =
                $emailResult->num_rows > 0;

            $checkEmail->close();


            /* ============================================
               CHECK DUPLICATE MOBILE
            ============================================ */

            $checkMobile = $conn->prepare(
                "SELECT id
                 FROM students
                 WHERE mobile = ?
                 LIMIT 1"
            );


            if (!$checkMobile) {

                $message =
                    "Database error: " .
                    $conn->error;

                $messageType = "danger";

            } else {

                $checkMobile->bind_param(
                    "s",
                    $pending["mobile"]
                );

                $checkMobile->execute();

                $mobileResult =
                    $checkMobile->get_result();

                $mobileExists =
                    $mobileResult->num_rows > 0;

                $checkMobile->close();


                /* ========================================
                   CHECK DUPLICATE CNIC
                ======================================== */

                $checkCnic = $conn->prepare(
                    "SELECT id
                     FROM students
                     WHERE cnic = ?
                     LIMIT 1"
                );


                if (!$checkCnic) {

                    $message =
                        "Database error: " .
                        $conn->error;

                    $messageType = "danger";

                } else {

                    $checkCnic->bind_param(
                        "s",
                        $pending["cnic"]
                    );

                    $checkCnic->execute();

                    $cnicResult =
                        $checkCnic->get_result();

                    $cnicExists =
                        $cnicResult->num_rows > 0;

                    $checkCnic->close();


                    /* ====================================
                       DUPLICATE FOUND
                    ==================================== */

                    if (
                        $emailExists ||
                        $mobileExists ||
                        $cnicExists
                    ) {

                        $duplicateFields = [];


                        if ($emailExists) {
                            $duplicateFields[] =
                                "email";
                        }


                        if ($mobileExists) {
                            $duplicateFields[] =
                                "mobile number";
                        }


                        if ($cnicExists) {
                            $duplicateFields[] =
                                "CNIC";
                        }


                        $message =
                            "This registration cannot be completed because " .
                            implode(", ", $duplicateFields) .
                            " is already registered.";


                        $messageType = "danger";


                        /*
                         * Delete pending registration because
                         * this registration cannot be completed.
                         */

                        $deletePending = $conn->prepare(
                            "DELETE FROM student_registration_otp
                             WHERE id = ?"
                        );


                        if ($deletePending) {

                            $deletePending->bind_param(
                                "i",
                                $pending["id"]
                            );

                            $deletePending->execute();

                            $deletePending->close();
                        }


                        unset($_SESSION["verify_email"]);


                        /*
                         * We don't redirect here so the user
                         * can see the message.
                         */

                    }

                    /* ====================================
                       EVERYTHING AVAILABLE
                    ==================================== */

                    else {

                        /*
                         * Insert verified student.
                         *
                         * IMPORTANT:
                         * password_hash is already hashed
                         * during registration.
                         */

                        $insertStudent = $conn->prepare(
                            "INSERT INTO students
                            (
                                first_name,
                                last_name,
                                father_name,
                                email,
                                mobile,
                                cnic,
                                password
                            )
                            VALUES (?, ?, ?, ?, ?, ?, ?)"
                        );


                        if (!$insertStudent) {

                            $message =
                                "Could not prepare student account: " .
                                $conn->error;

                            $messageType = "danger";

                        } else {

                            $insertStudent->bind_param(
                                "sssssss",
                                $pending["first_name"],
                                $pending["last_name"],
                                $pending["father_name"],
                                $pending["email"],
                                $pending["mobile"],
                                $pending["cnic"],
                                $pending["password_hash"]
                            );


                            /* =================================
                               INSERT STUDENT
                            ================================= */

                            if ($insertStudent->execute()) {

                                $newStudentId =
                                    $conn->insert_id;


                                $insertStudent->close();


                                /* =============================
                                   DELETE PENDING REGISTRATION
                                ============================= */

                                $deletePending = $conn->prepare(
                                    "DELETE FROM student_registration_otp
                                     WHERE id = ?"
                                );


                                if ($deletePending) {

                                    $deletePending->bind_param(
                                        "i",
                                        $pending["id"]
                                    );

                                    $deletePending->execute();

                                    $deletePending->close();
                                }


                                /* =============================
                                   CLEAR VERIFICATION SESSION
                                ============================= */

                                unset(
                                    $_SESSION["verify_email"]
                                );


                                /* =============================
                                   SUCCESS MESSAGE
                                ============================= */

                                $_SESSION["registration_success"] =
                                    "Your account has been created successfully. Please login.";


                                header(
                                    "Location: login.php?registered=1"
                                );

                                exit;

                            } else {

                                /*
                                 * Extra protection:
                                 * In case database still reports
                                 * duplicate entry.
                                 */

                                $dbError =
                                    $insertStudent->error;

                                $insertStudent->close();


                                if (
                                    stripos(
                                        $dbError,
                                        "Duplicate entry"
                                    ) !== false
                                ) {

                                    $message =
                                        "This email, mobile number, or CNIC is already registered. Please use different information.";

                                } else {

                                    $message =
                                        "Could not create account: " .
                                        $dbError;
                                }


                                $messageType = "danger";
                            }
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
        Verify Email | GTU ERP
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


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

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background:
                linear-gradient(
                    135deg,
                    rgba(5, 12, 24, .96),
                    rgba(13, 27, 49, .94)
                );

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 25px;
        }


        .verify-card {

            width: 100%;

            max-width: 550px;

            background: white;

            border-radius: 24px;

            overflow: hidden;

            box-shadow:
                0 30px 70px
                rgba(0,0,0,.35);
        }


        .verify-header {

            background: #0b1220;

            color: white;

            text-align: center;

            padding: 35px 25px;

            border-bottom:
                4px solid #f5c542;
        }


        .icon-box {

            width: 75px;

            height: 75px;

            margin: auto;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #f5c542;

            color: #111827;

            border-radius: 50%;

            font-size: 32px;

            margin-bottom: 18px;
        }


        .verify-header h1 {

            margin: 0;

            font-size: 28px;

            font-weight: 800;
        }


        .verify-header p {

            color: #cbd5e1;

            margin:
                8px 0 0;
        }


        .verify-body {

            padding: 35px;
        }


        .email-box {

            background: #f8fafc;

            border:

                1px solid #e2e8f0;

            border-radius: 12px;

            padding: 15px;

            text-align: center;

            margin-bottom: 25px;

            color: #475569;
        }


        .email-box strong {

            color: #0b1220;
        }


        .otp-input {

            width: 100%;

            height: 65px;

            text-align: center;

            font-size: 30px;

            font-weight: 800;

            letter-spacing: 12px;

            border:

                2px solid #d5dbe5;

            border-radius: 12px;

            outline: none;
        }


        .otp-input:focus {

            border-color: #f5c542;

            box-shadow:
                0 0 0 4px
                rgba(245,197,66,.15);
        }


        .verify-btn {

            width: 100%;

            height: 53px;

            border: none;

            border-radius: 11px;

            background: #f5c542;

            color: #111827;

            font-weight: 800;

            font-size: 16px;

            margin-top: 20px;

            transition: .2s;
        }


        .verify-btn:hover {

            background: #e6b52e;

            transform:
                translateY(-2px);
        }


        .info-box {

            background: #eff6ff;

            border-left:
                4px solid #2563eb;

            padding: 15px;

            border-radius: 8px;

            color: #475569;

            font-size: 13px;

            margin-top: 25px;
        }


        .back-link {

            display: block;

            text-align: center;

            margin-top: 22px;

            color: #2563eb;

            text-decoration: none;

            font-weight: 700;
        }


        .alert {

            border-radius: 12px;

            font-weight: 600;
        }


        @media(max-width: 576px) {

            .verify-body {

                padding: 25px 20px;
            }

            .otp-input {

                letter-spacing: 7px;

                font-size: 25px;
            }

        }

    </style>

</head>


<body>


<div class="verify-card">


    <!-- HEADER -->

    <div class="verify-header">

        <div class="icon-box">

            <i class="fa-solid fa-envelope-circle-check"></i>

        </div>


        <h1>
            Verify Your Email
        </h1>


        <p>
            Enter the 6-digit OTP sent to your email
        </p>

    </div>


    <!-- BODY -->

    <div class="verify-body">


        <?php if ($message !== ""): ?>

            <div
                class="alert alert-<?php echo htmlspecialchars($messageType); ?>"
            >

                <i
                    class="fa-solid fa-circle-exclamation me-2"
                ></i>

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <?php if (isset($pending) && $pending): ?>


            <div class="email-box">

                <i
                    class="fa-solid fa-envelope me-2"
                ></i>

                OTP sent to:

                <br>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $email
                    );
                    ?>
                </strong>

            </div>


            <form
                method="POST"
                action=""
            >


                <label
                    class="form-label fw-bold mb-2"
                >
                    Enter OTP
                </label>


                <input
                    type="text"
                    name="otp"
                    class="otp-input"
                    maxlength="6"
                    minlength="6"
                    pattern="[0-9]{6}"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    placeholder="000000"
                    required
                >


                <button
                    type="submit"
                    class="verify-btn"
                >

                    <i
                        class="fa-solid fa-circle-check me-2"
                    ></i>

                    Verify OTP

                </button>


            </form>


            <div class="info-box">

                <i
                    class="fa-solid fa-shield-halved me-2"
                ></i>

                Your OTP is valid for
                <strong>10 minutes</strong>.
                You have a maximum of
                <strong>5 attempts</strong>.

            </div>


        <?php else: ?>


            <div class="text-center">

                <p class="text-muted">

                    Your verification session has ended.

                </p>


                <a
                    href="register.php"
                    class="btn btn-warning fw-bold"
                >

                    Register Again

                </a>

            </div>


        <?php endif; ?>


        <a
            href="register.php"
            class="back-link"
        >

            <i
                class="fa-solid fa-arrow-left me-1"
            ></i>

            Back to Registration

        </a>


    </div>


</div>


<script>

    const otpInput =
        document.querySelector(".otp-input");

    if (otpInput) {

        otpInput.addEventListener(
            "input",
            function () {

                this.value =
                    this.value
                    .replace(/\D/g, "")
                    .slice(0, 6);

            }
        );

    }

</script>


</body>

</html>