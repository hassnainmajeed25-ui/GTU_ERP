<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';


/* =========================================================
   SESSION CHECK
========================================================= */

if (
    !isset($_SESSION['password_reset_email']) ||
    !isset($_SESSION['password_reset_student_id'])
) {

    header("Location: forgot_password.php");
    exit;
}


$email =
    $_SESSION['password_reset_email'];

$studentId =
    (int)$_SESSION['password_reset_student_id'];


$message = "";
$type = "";


/* =========================================================
   GET OTP RECORD
========================================================= */

$stmt = $conn->prepare(
    "SELECT
        id,
        student_id,
        email,
        otp_hash,
        otp_expires_at,
        attempts
     FROM student_password_reset_otp
     WHERE student_id = ?
       AND email = ?
     ORDER BY id DESC
     LIMIT 1"
);


if (!$stmt) {

    die(
        "Database error: " .
        $conn->error
    );
}


$stmt->bind_param(
    "is",
    $studentId,
    $email
);


$stmt->execute();


$result =
    $stmt->get_result();


$resetData =
    $result->fetch_assoc();


$stmt->close();


if (!$resetData) {

    unset(
        $_SESSION['password_reset_email'],
        $_SESSION['password_reset_student_id']
    );

    header(
        "Location: forgot_password.php"
    );

    exit;
}


/* =========================================================
   FORM SUBMISSION
========================================================= */

if (isset($_POST['verify_and_reset'])) {

    $otp =
        trim($_POST['otp'] ?? "");

    $newPassword =
        $_POST['new_password'] ?? "";

    $confirmPassword =
        $_POST['confirm_password'] ?? "";


    /* =====================================================
       OTP VALIDATION
    ===================================================== */

    if (!preg_match('/^[0-9]{6}$/', $otp)) {

        $message =
            "Please enter the 6-digit verification code.";

        $type = "danger";

    }


    /* =====================================================
       ATTEMPT LIMIT
    ===================================================== */

    elseif ((int)$resetData['attempts'] >= 5) {

        $message =
            "Too many incorrect attempts. Please request a new OTP.";

        $type = "danger";

    }


    /* =====================================================
       EXPIRY
    ===================================================== */

    elseif (
        strtotime(
            $resetData['otp_expires_at']
        ) < time()
    ) {

        $message =
            "This verification code has expired. Please request a new one.";

        $type = "danger";

    }


    /* =====================================================
       OTP VERIFY
    ===================================================== */

    elseif (
        !password_verify(
            $otp,
            $resetData['otp_hash']
        )
    ) {

        $newAttempts =
            (int)$resetData['attempts'] + 1;


        $updateAttempts =
            $conn->prepare(
                "UPDATE student_password_reset_otp
                 SET attempts = ?
                 WHERE id = ?"
            );


        if ($updateAttempts) {

            $updateAttempts->bind_param(
                "ii",
                $newAttempts,
                $resetData['id']
            );

            $updateAttempts->execute();

            $updateAttempts->close();
        }


        $remaining =
            max(
                0,
                5 - $newAttempts
            );


        $message =
            "Incorrect verification code. " .
            $remaining .
            " attempt(s) remaining.";

        $type = "danger";

    }


    /* =====================================================
       PASSWORD VALIDATION
    ===================================================== */

    elseif (strlen($newPassword) < 6) {

        $message =
            "New password must be at least 6 characters.";

        $type = "danger";

    }


    elseif ($newPassword !== $confirmPassword) {

        $message =
            "New password and confirm password do not match.";

        $type = "danger";

    }


    /* =====================================================
       OTP + PASSWORD CORRECT
    ===================================================== */

    else {


        /* ================================================
           HASH NEW PASSWORD
        ================================================ */

        $passwordHash =
            password_hash(
                $newPassword,
                PASSWORD_DEFAULT
            );


        /* ================================================
           UPDATE STUDENT PASSWORD
        ================================================ */

        $update =
            $conn->prepare(
                "UPDATE students
                 SET password = ?
                 WHERE id = ?"
            );


        if (!$update) {

            $message =
                "Database error: " .
                $conn->error;

            $type = "danger";

        } else {

            $update->bind_param(
                "si",
                $passwordHash,
                $studentId
            );


            if ($update->execute()) {

                $update->close();


                /* =========================================
                   DELETE USED OTP
                ========================================= */

                $deleteOtp =
                    $conn->prepare(
                        "DELETE FROM student_password_reset_otp
                         WHERE student_id = ?"
                    );


                if ($deleteOtp) {

                    $deleteOtp->bind_param(
                        "i",
                        $studentId
                    );

                    $deleteOtp->execute();

                    $deleteOtp->close();
                }


                /* =========================================
                   CLEAR RESET SESSION
                ========================================= */

                unset(
                    $_SESSION['password_reset_email'],
                    $_SESSION['password_reset_student_id']
                );


                /* =========================================
                   LOGIN PAGE
                ========================================= */

                header(
                    "Location: login.php?reset=1"
                );

                exit;


            } else {

                $message =
                    "Could not update password: " .
                    $update->error;

                $type = "danger";

                $update->close();
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
    Verify & Reset Password | GTU
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
            rgba(8,20,55,.92),
            rgba(8,20,55,.92)
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


.card-box {

    width:100%;

    max-width:650px;

    background:#fff;

    border-radius:22px;

    overflow:hidden;

    box-shadow:
        0 25px 65px
        rgba(0,0,0,.35);
}


.card-header {

    background:#0B1F52;

    color:#fff;

    text-align:center;

    padding:35px;

    border-bottom:
        4px solid #FDB913;
}


.icon {

    width:75px;

    height:75px;

    background:#FDB913;

    color:#111827;

    border-radius:50%;

    display:flex;

    align-items:center;

    justify-content:center;

    margin:0 auto 18px;

    font-size:30px;
}


.card-header h2 {

    font-weight:800;

    margin:0;
}


.card-header p {

    color:#cbd5e1;

    margin-top:8px;

}


.card-body {

    padding:40px;
}


.form-control {

    height:52px;

    border-radius:12px;
}


.otp {

    height:65px;

    text-align:center;

    font-size:30px;

    font-weight:800;

    letter-spacing:10px;
}


.form-control:focus {

    border-color:#FDB913;

    box-shadow:
        0 0 0 .2rem
        rgba(253,185,19,.18);
}


.btn-reset {

    width:100%;

    height:55px;

    border:none;

    border-radius:50px;

    background:#FDB913;

    color:#111827;

    font-weight:800;

    font-size:16px;
}


.btn-reset:hover {

    background:#e3a500;
}


.email-box {

    background:#f8fafc;

    padding:15px;

    border-radius:12px;

    text-align:center;

    margin-bottom:25px;

    color:#64748b;
}


.email-box strong {

    color:#0B1F52;
}


@media(max-width:576px) {

    .card-body {

        padding:25px 20px;
    }

    .otp {

        letter-spacing:6px;

        font-size:25px;
    }

}

</style>

</head>


<body>


<div class="card-box">


<div class="card-header">


<div class="icon">

<i class="fas fa-lock-open"></i>

</div>


<h2>
    Verify & Reset Password
</h2>


<p>
    Verify the code and create your new password
</p>


</div>


<div class="card-body">


<?php if ($message != ""): ?>

<div class="alert alert-<?php
echo htmlspecialchars($type);
?>">

<?php
echo htmlspecialchars($message);
?>

</div>

<?php endif; ?>


<div class="email-box">

<i class="fas fa-envelope me-2"></i>

Verification code sent to:

<br>

<strong>

<?php
echo htmlspecialchars($email);
?>

</strong>

</div>


<form method="POST">


<!-- OTP -->

<div class="mb-4">


<label class="form-label fw-bold">

6-Digit Verification Code

</label>


<input
    type="text"
    name="otp"
    class="form-control otp"
    maxlength="6"
    minlength="6"
    pattern="[0-9]{6}"
    inputmode="numeric"
    autocomplete="one-time-code"
    placeholder="000000"
    required
>


</div>


<!-- NEW PASSWORD -->

<div class="mb-3">


<label class="form-label fw-bold">

New Password

</label>


<input
    type="password"
    name="new_password"
    id="new_password"
    class="form-control"
    minlength="6"
    required
>


</div>


<!-- CONFIRM -->

<div class="mb-3">


<label class="form-label fw-bold">

Confirm New Password

</label>


<input
    type="password"
    name="confirm_password"
    id="confirm_password"
    class="form-control"
    minlength="6"
    required
>


</div>


<!-- SHOW PASSWORD -->

<div class="form-check mb-4">


<input
    class="form-check-input"
    type="checkbox"
    id="showPassword"
>


<label
    class="form-check-label"
    for="showPassword"
>

Show Password

</label>


</div>


<button
    type="submit"
    name="verify_and_reset"
    class="btn-reset"
>

<i class="fas fa-key me-2"></i>

Verify Code & Change Password

</button>


</form>


<div class="text-center mt-4">

<small class="text-muted">

OTP is valid for 10 minutes and allows
a maximum of 5 attempts.

</small>

</div>


<div class="text-center mt-3">

<a
    href="login.php"
    class="text-decoration-none fw-bold"
>

<i class="fas fa-arrow-left me-1"></i>

Back to Login

</a>

</div>


</div>


</div>


<script>

const otp =
document.querySelector(".otp");

if (otp) {

    otp.addEventListener(
        "input",
        function() {

            this.value =
                this.value
                .replace(/\D/g, "")
                .slice(0, 6);

        }
    );

}


document
.getElementById("showPassword")
.addEventListener(
    "change",
    function() {

        const type =
            this.checked
            ? "text"
            : "password";

        document
        .getElementById("new_password")
        .type = type;


        document
        .getElementById("confirm_password")
        .type = type;

    }
);

</script>


</body>

</html>