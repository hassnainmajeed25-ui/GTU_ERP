<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
STUDENT LOGIN AUTHENTICATION
=========================================*/

if($_SERVER['REQUEST_METHOD'] != "POST"){

    header("Location: login.php");
    exit;

}

/*=========================================
GET LOGIN DATA
=========================================*/

$roll_no = trim($_POST['roll_no']);
$password = trim($_POST['password']);

$error = "";

/*=========================================
EMPTY VALIDATION
=========================================*/

if(empty($roll_no) || empty($password)){

    $error = "Please enter Roll Number and CNIC.";

    $_SESSION['login_error'] = $error;

    header("Location: login.phpp");
    exit;

}

/*=========================================
CHECK STUDENT LOGIN
=========================================*/

$sql = "

SELECT

students.id,
students.first_name,
students.last_name,
students.roll_no,
students.registration_no,
students.password,
students.status,
students.program_id,

programs.program_name,

faculties.faculty_name

FROM students

LEFT JOIN programs
ON students.program_id = programs.id

LEFT JOIN faculties
ON programs.faculty_id = faculties.id

WHERE students.roll_no = ?

LIMIT 1

";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s",$roll_no);

$stmt->execute();

$result = $stmt->get_result();

/*=========================================
CHECK STUDENT FOUND
=========================================*/

if($result->num_rows == 0){

    $_SESSION['login_error'] = "Invalid Roll Number.";

    header("Location: login.php");
    exit;

}

$student = $result->fetch_assoc();

/*=========================================
CHECK STUDENT STATUS
=========================================*/

if($student['status'] != "Active"){

    $_SESSION['login_error'] = "Your account is not active.";

    header("Location: login.php");
    exit;

}

/*=========================================
VERIFY PASSWORD (CNIC)
=========================================*/

if(!password_verify($password,$student['password'])){

    $_SESSION['login_error']="Incorrect CNIC Password.";

    header("Location:login.php");
    exit;

}

/*=========================================
CREATE SESSION
=========================================*/

$_SESSION['student_id']        = $student['id'];

$_SESSION['student_name']      = $student['first_name']." ".$student['last_name'];

$_SESSION['roll_no']           = $student['roll_no'];

$_SESSION['registration_no']   = $student['registration_no'];

$_SESSION['program_name']      = $student['program_name'];

$_SESSION['faculty_name']      = $student['faculty_name'];

$_SESSION['program_id']        = $student['program_id'];

/*=========================================
LOGIN SUCCESS
=========================================*/

$_SESSION['student_login'] = true;

/*=========================================
UPDATE LAST LOGIN (OPTIONAL)
=========================================*/

/*

Agar future me students table me

last_login

column add karoge to ye code use karna.

$update = $conn->prepare("
UPDATE students
SET last_login = NOW()
WHERE id = ?
");

$update->bind_param(
"i",
$student['id']
);

$update->execute();

*/

/*=========================================
REDIRECT TO DASHBOARD
=========================================*/

header("Location: dashboard.php");
exit;

/*=========================================
CLOSE CONNECTION
=========================================*/

$stmt->close();

$conn->close();

?>