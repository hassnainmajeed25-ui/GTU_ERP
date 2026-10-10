<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Finance Office"){
    header("Location: ../admins/login.php");
    exit;
}

/*=========================================
GET ALL APPLICATIONS
=========================================*/

$sql="

SELECT

applications.id,
applications.program_id,
applications.session_id,
applications.shift,
applications.roll_no,

students.first_name,
students.last_name,

programs.program_code,

admission_sessions.session_name

FROM applications

INNER JOIN students
ON students.id=applications.student_id

INNER JOIN programs
ON programs.id=applications.program_id

INNER JOIN admission_sessions
ON admission_sessions.id=applications.session_id

ORDER BY applications.id ASC

";

$result=$conn->query($sql);

echo "<h2>Total Records: ".$result->num_rows."</h2>";

if(!$result){

die($conn->error);

}

/*=========================================
COUNTER ARRAY
=========================================*/

$counters=[];

$totalUpdated=0;

$totalSkipped=0;

$updatedStudents=[];

/*=========================================
START GENERATING ROLL NUMBERS
=========================================*/

while($row=$result->fetch_assoc()){

    $application_id=$row['id'];

    $programCode=strtoupper(trim($row['program_code']));

    $shift=strtolower(trim($row['shift']));

    $sessionName=strtolower(trim($row['session_name']));

    /*=========================================
    SESSION CODE
    =========================================*/

    preg_match('/20(\d{2})/',$sessionName,$yearMatch);

    $yearCode=isset($yearMatch[1]) ? $yearMatch[1] : date("y");

    if(strpos($sessionName,"fall")!==false){

        $sessionCode="F";

    }elseif(strpos($sessionName,"spring")!==false){

        $sessionCode="S";

    }else{

        $sessionCode="X";

    }

    /*=========================================
    SHIFT CODE
    =========================================*/

    switch($shift){

        case "morning":
            $shiftCode="M";
            break;

        case "evening":
            $shiftCode="E";
            break;

        case "replica":
            $shiftCode="R";
            break;

        default:
            $shiftCode="X";

    }

    /*=========================================
    COUNTER KEY
    =========================================*/

    $key=$programCode."-".$sessionCode.$yearCode."-".$shiftCode;

    if(!isset($counters[$key])){

        $counters[$key]=1;

    }else{

        $counters[$key]++;

    }

    /*=========================================
    NEW ROLL NUMBER
    =========================================*/

    $newRoll="GTU-".$programCode."-".$sessionCode.$yearCode."-".$shiftCode."-".str_pad($counters[$key],2,"0",STR_PAD_LEFT);

    /*=========================================
    SKIP IF SAME
    =========================================*/

    if($row['roll_no']==$newRoll){

        $totalSkipped++;

        continue;

    }

    /*=========================================
    UPDATE DATABASE
    =========================================*/

    $update=$conn->prepare("

    UPDATE applications

    SET roll_no=?

    WHERE id=?

    ");

    $update->bind_param(

    "si",

    $newRoll,

    $application_id

    );

    $update->execute();

    $totalUpdated++;

    $updatedStudents[]=[

        'name'=>$row['first_name']." ".$row['last_name'],

        'roll'=>$newRoll

    ];

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>

GTU ERP | Roll Number Generator

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet">

<style>

body{

background:#f4f6fb;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 10px 30px rgba(0,0,0,.08);

}

.table th{

background:#0B1F52;

color:white;

}

</style>

</head>

<body>

<div class="container mt-5">

<div class="card">

<div class="card-header bg-primary text-white">

<h3>

<i class="fas fa-id-card me-2"></i>

GTU Roll Number Generation Report

</h3>

</div>

<div class="card-body">

<div class="row text-center mb-4">

<div class="col-md-4">

<div class="alert alert-success">

<h3>

<?php echo $totalUpdated; ?>

</h3>

Updated Students

</div>

</div>

<div class="col-md-4">

<div class="alert alert-warning">

<h3>

<?php echo $totalSkipped; ?>

</h3>

Already Correct

</div>

</div>

<div class="col-md-4">

<div class="alert alert-info">

<h3>

<?php echo count($updatedStudents); ?>

</h3>

Generated Roll Numbers

</div>

</div>

</div>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th width="80">

#

</th>

<th>

Student Name

</th>

<th>

New Roll Number

</th>

</tr>

</thead>

<tbody>

<?php

$sr=1;

foreach($updatedStudents as $student){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<?php echo htmlspecialchars($student['name']); ?>

</td>

<td>

<b class="text-primary">

<?php echo htmlspecialchars($student['roll']); ?>

</b>

</td>

</tr>

<?php

}

if(count($updatedStudents)==0){

?>

<tr>

<td colspan="3" class="text-center text-danger">

No Record Updated.

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<div class="text-end mt-3">

<a

href="semester_fee.php"

class="btn btn-success">

<i class="fas fa-arrow-left me-2"></i>

Back To Finance Dashboard

</a>

</div>

</div>

</div>

</div>

</body>

</html>
