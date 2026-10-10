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

if($_SESSION['department']!="Admission Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

/*=========================================
SEARCH
=========================================*/

$search="";

$where="";

if(isset($_GET['search'])){

    $search=trim($_GET['search']);

    $search=$conn->real_escape_string($search);

    $where="

    WHERE

    applications.application_id LIKE '%$search%'

    OR

    students.first_name LIKE '%$search%'

    OR

    students.last_name LIKE '%$search%'

    OR

    students.email LIKE '%$search%'

    ";

}

/*=========================================
APPLICATIONS LIST
=========================================*/

$sql="
SELECT

applications.id,
applications.application_id,
applications.application_status,
applications.created_at,

students.first_name,
students.last_name,
students.email,
students.mobile,

faculties.faculty_name,

programs.program_name,

application_payments.verification_status

FROM applications

INNER JOIN students
ON students.id=applications.student_id

LEFT JOIN faculties
ON faculties.id=applications.faculty_id

LEFT JOIN programs
ON programs.id=applications.program_id

LEFT JOIN application_payments
ON application_payments.application_id=applications.id

$where

ORDER BY applications.id DESC

";

$applications=$conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Manage Applications | Admission Office</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{
    background:#eef2f7;
    font-family:Arial,Helvetica,sans-serif;
}

/* Sidebar */

.sidebar{

    width:260px;
    height:100vh;
    background:#0B1F52;
    position:fixed;
    left:0;
    top:0;
    overflow-y:auto;

}

.sidebar .logo{

    color:#fff;
    text-align:center;
    font-size:24px;
    font-weight:bold;
    padding:25px 10px;
    border-bottom:1px solid rgba(255,255,255,.15);

}

.sidebar a{

    display:block;
    color:#fff;
    text-decoration:none;
    padding:15px 20px;
    transition:.3s;

}

.sidebar a:hover{

    background:#173a8b;

}

.sidebar a.active{

    background:#1d4ed8;

}

.main{

    margin-left:260px;
    padding:30px;

}

.topbar{

    background:#fff;
    border-radius:12px;
    box-shadow:0 3px 10px rgba(0,0,0,.1);
    padding:15px 25px;
    margin-bottom:30px;

    display:flex;
    justify-content:space-between;
    align-items:center;

}

.page-card{

    background:#fff;
    border:none;
    border-radius:15px;
    box-shadow:0 3px 10px rgba(0,0,0,.1);

}

.table th{

    background:#0B1F52;
    color:#fff;

}

</style>

</head>

<body>

<!-- Sidebar -->

<div class="sidebar">

<div class="logo">

GTU ERP

</div>

<a href="dashboard.php">

<i class="fas fa-chart-line me-2"></i>

Dashboard

</a>

<a href="manage_applications.php" class="active">

<i class="fas fa-file-alt me-2"></i>

Manage Applications

</a>

<a href="approved_applications.php">

<i class="fas fa-circle-check me-2"></i>

Approved Applications

</a>

<a href="rejected_applications.php">

<i class="fas fa-circle-xmark me-2"></i>

Rejected Applications

</a>

<a href="merit_list.php">

<i class="fas fa-ranking-star me-2"></i>

Merit List

</a>

<a href="selected_students.php">

<i class="fas fa-user-graduate me-2"></i>

Selected Students

</a>

<a href="reports.php">

<i class="fas fa-chart-column me-2"></i>

Reports

</a>

<a href="inbox.php">

<i class="fas fa-envelope me-2"></i>

Inbox

</a>

<a href="compose_message.php">

<i class="fas fa-paper-plane me-2"></i>

Compose Message

</a>

<a href="profile.php">

<i class="fas fa-user me-2"></i>

My Profile

</a>

<a href="../admins/logout.php">

<i class="fas fa-right-from-bracket me-2"></i>

Logout

</a>

</div>

<!-- Main -->

<div class="main">

<div class="topbar">

<div>

<h3 class="mb-0">

Manage Applications

</h3>

<small class="text-muted">

Admission Office Panel

</small>

</div>

<div>

<strong>

Welcome,

<?php echo $admin_name; ?>

</strong>

</div>

</div>

<!-- Search Card -->

<div class="card page-card mb-4">

<div class="card-body">

<form method="GET">

<div class="row">

<div class="col-md-10">

<input
type="text"
name="search"
class="form-control"
placeholder="Search by Application ID, Student Name or Email..."
value="<?php echo htmlspecialchars($search); ?>">

</div>

<div class="col-md-2">

<button
class="btn btn-primary w-100"
type="submit">

<i class="fas fa-search"></i>

Search

</button>

</div>

</div>

</form>

</div>

</div>
<!-- Applications Table -->

<div class="card page-card">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">

            <i class="fas fa-file-alt me-2"></i>

            Applications List

        </h5>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Application ID</th>

                        <th>Student</th>

                        <th>Faculty</th>

                        <th>Program</th>

                        <th>Status</th>

                        <th>Fee Status</th>

                        <th>Date</th>

                        <th width="220">Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php

                if($applications && $applications->num_rows > 0){

                    $sr=1;

                    while($row=$applications->fetch_assoc()){

                ?>

                <tr>

                    <td>

                        <?php echo $sr++; ?>

                    </td>

                    <td>

                        <strong>

                            <?php echo htmlspecialchars($row['application_id']); ?>

                        </strong>

                    </td>

                    <td>

                        <strong>

                            <?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?>

                        </strong>

                        <br>

                        <small class="text-muted">

                            <?php echo htmlspecialchars($row['email']); ?>

                        </small>

                        <br>

                        <small class="text-primary">

                            <?php echo htmlspecialchars($row['mobile']); ?>

                        </small>

                    </td>

                    <td>

                        <?php echo htmlspecialchars($row['faculty_name'] ?? '-'); ?>

                    </td>

                    <td>

                        <?php echo htmlspecialchars($row['program_name'] ?? '-'); ?>

                    </td>

                    <td>

                        <?php

                        switch($row['application_status']){

                            case "Submitted":

                                echo '<span class="badge bg-primary">Submitted</span>';

                                break;

                            case "Under Review":

                                echo '<span class="badge bg-warning text-dark">Under Review</span>';

                                break;

                            case "Approved":

                                echo '<span class="badge bg-success">Approved</span>';

                                break;

                            case "Rejected":

                                echo '<span class="badge bg-danger">Rejected</span>';

                                break;

                            case "Selected":

                                echo '<span class="badge bg-info">Selected</span>';

                                break;

                            default:

                                echo '<span class="badge bg-secondary">'.$row['application_status'].'</span>';

                        }

                        ?>

                    </td>

                    <td>

<?php

if($row['verification_status']=="Verified"){

    echo '<span class="badge bg-success">Verified</span>';

}elseif($row['verification_status']=="Rejected"){

    echo '<span class="badge bg-danger">Rejected</span>';

}else{

    echo '<span class="badge bg-warning text-dark">Pending</span>';

}

?>

</td>

                    <td>

                        <?php echo date("d M Y",strtotime($row['created_at'])); ?>

                    </td>

                    <td>

                        <a href="view_application.php?id=<?php echo $row['id']; ?>"

                           class="btn btn-sm btn-info text-white">

                           <i class="fas fa-eye"></i>

                           View

                        </a>

                    </td>

                </tr>

                <?php

                    }

                }else{

                ?>

                <tr>

                    <td colspan="8" class="text-center">

                        No applications found.

                    </td>

                </tr>

                <?php

                }

                ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
<!-- Footer -->

<div class="card mt-4 border-0 shadow">

    <div class="card-body text-center">

        <h5 class="text-primary">

            Global Tech University ERP

        </h5>

        <p class="mb-1">

            Admission Office Management System

        </p>

        <small class="text-muted">

            Logged in as:

            <strong>

                <?php echo htmlspecialchars($admin_name); ?>

            </strong>

        </small>

        <br>

        <small class="text-muted">

            <?php echo date("l, d F Y"); ?>

        </small>

    </div>

</div>

</div>
<!-- End Main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>