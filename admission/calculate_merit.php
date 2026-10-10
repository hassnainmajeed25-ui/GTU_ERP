<?php

require_once '../config/config.php';
require_once '../config/database.php';

/* =========================================
   ADMIN LOGIN SECURITY
========================================= */

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

/* =========================================
   ADMISSION OFFICE SECURITY
========================================= */

if(!isset($_SESSION['department']) || $_SESSION['department'] !== "Admission Office"){
    header("Location: ../admins/login.php");
    exit;
}

/* =========================================
   CHECK APPLICATION ID
========================================= */

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid Application Request.");
}

$id = (int)$_GET['id'];

/* =========================================
   GET ACADEMIC RECORD
========================================= */

$sql = $conn->prepare("
    SELECT
        matric_total_marks,
        matric_obtained_marks,
        inter_total_marks,
        inter_obtained_marks
    FROM application_academic
    WHERE application_id=?
    LIMIT 1
");

if(!$sql){
    die("Database Query Error: " . $conn->error);
}

$sql->bind_param("i", $id);
$sql->execute();

$result = $sql->get_result();

if($result->num_rows == 0){
    die("Academic Record Not Found.");
}

$row = $result->fetch_assoc();

/* =========================================
   CONVERT VALUES TO NUMBERS
========================================= */

$matricTotal    = (int)$row['matric_total_marks'];
$matricObtained = (int)$row['matric_obtained_marks'];

$interTotal     = (int)$row['inter_total_marks'];
$interObtained  = (int)$row['inter_obtained_marks'];

/* =========================================
   VALIDATE MARKS BEFORE CALCULATION
========================================= */

if($matricTotal <= 0){
    die("Error: Matric total marks are invalid.");
}

if($interTotal <= 0){
    die("Error: Intermediate total marks are invalid.");
}

if($matricObtained < 0){
    die("Error: Matric obtained marks cannot be negative.");
}

if($interObtained < 0){
    die("Error: Intermediate obtained marks cannot be negative.");
}

if($matricObtained > $matricTotal){

    die("
        <h2 style='color:#dc3545;'>Invalid Matric Marks</h2>

        <p>
        Obtained marks cannot be greater than total marks.
        </p>

        <p>
        Total Marks: <strong>$matricTotal</strong><br>
        Obtained Marks: <strong>$matricObtained</strong>
        </p>

        <a href='merit_list.php'
           style='
           display:inline-block;
           padding:10px 20px;
           background:#0B1F52;
           color:white;
           text-decoration:none;
           border-radius:6px;'>
           Back to Merit List
        </a>
    ");

}

if($interObtained > $interTotal){

    die("
        <h2 style='color:#dc3545;'>Invalid Intermediate Marks</h2>

        <p>
        Obtained marks cannot be greater than total marks.
        </p>

        <p>
        Total Marks: <strong>$interTotal</strong><br>
        Obtained Marks: <strong>$interObtained</strong>
        </p>

        <a href='merit_list.php'
           style='
           display:inline-block;
           padding:10px 20px;
           background:#0B1F52;
           color:white;
           text-decoration:none;
           border-radius:6px;'>
           Back to Merit List
        </a>
    ");

}

/* =========================================
   CALCULATE PERCENTAGES
========================================= */

$matricPercentage =
    ($matricObtained / $matricTotal) * 100;

$interPercentage =
    ($interObtained / $interTotal) * 100;

/* =========================================
   MERIT CALCULATION
========================================= */

$merit =
    ($matricPercentage * 0.30)
    +
    ($interPercentage * 0.70);

$merit = round($merit, 2);

/* =========================================
   SAFETY CHECK
========================================= */

if($merit < 0 || $merit > 100){

    die("
        <h2 style='color:#dc3545;'>Merit Calculation Error</h2>

        <p>
        Calculated merit is outside the valid range.
        </p>

        <p>
        Merit: <strong>$merit%</strong>
        </p>

        <a href='merit_list.php'
           style='
           display:inline-block;
           padding:10px 20px;
           background:#0B1F52;
           color:white;
           text-decoration:none;
           border-radius:6px;'>
           Back to Merit List
        </a>
    ");

}

/* =========================================
   UPDATE APPLICATION MERIT
========================================= */

$update = $conn->prepare("
    UPDATE applications
    SET merit_marks=?
    WHERE id=?
");

if(!$update){
    die("Database Update Error: " . $conn->error);
}

$update->bind_param("di", $merit, $id);

if(!$update->execute()){
    die("Unable to save merit: " . $update->error);
}

/* =========================================
   SUCCESS
========================================= */

header("Location: merit_list.php?calculated=1");
exit;

?>