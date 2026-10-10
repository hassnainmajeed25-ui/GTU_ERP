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
TOTAL APPLICATIONS
=========================================*/

$totalApplications=0;

$result=$conn->query("
SELECT COUNT(*) AS total
FROM applications
");

if($result){

$row=$result->fetch_assoc();

$totalApplications=$row['total'];

}

/*=========================================
PENDING APPLICATIONS
=========================================*/

$pendingApplications=0;

$result=$conn->query("
SELECT COUNT(*) AS total
FROM applications
WHERE application_status='Submitted'
");

if($result){

$row=$result->fetch_assoc();

$pendingApplications=$row['total'];

}

/*=========================================
APPROVED APPLICATIONS
=========================================*/

$approvedApplications=0;

$result=$conn->query("
SELECT COUNT(*) AS total
FROM applications
WHERE application_status='Approved'
");

if($result){

$row=$result->fetch_assoc();

$approvedApplications=$row['total'];

}

/*=========================================
REJECTED APPLICATIONS
=========================================*/

$rejectedApplications=0;

$result=$conn->query("
SELECT COUNT(*) AS total
FROM applications
WHERE application_status='Rejected'
");

if($result){

$row=$result->fetch_assoc();

$rejectedApplications=$row['total'];

}

/*=========================================
LATEST APPLICATIONS
=========================================*/

$applications=$conn->query("

SELECT

applications.id,
applications.application_id,
applications.application_status,
applications.created_at,

students.first_name,
students.last_name,
students.email,
students.mobile

FROM applications

INNER JOIN students

ON applications.student_id=students.id

ORDER BY applications.id DESC

LIMIT 10

");

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Admission Office Dashboard</title>

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
    font-size:24px;
    font-weight:bold;
    text-align:center;
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

/* Main */

.main{

    margin-left:260px;
    padding:30px;

}

/* Topbar */

.topbar{

    background:#fff;
    border-radius:12px;
    padding:15px 25px;
    box-shadow:0 3px 10px rgba(0,0,0,.10);
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;

}

.topbar h4{

    margin:0;
    color:#0B1F52;
    font-weight:bold;

}

.topbar span{

    color:#666;

}

</style>

</head>

<body>
<div class="sidebar">

<div class="logo">

GTU ERP

</div>

<a href="dashboard.php" class="active">
<i class="fas fa-chart-line me-2"></i>
Dashboard
</a>

<a href="manage_applications.php">
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

<a href="student_inbox.php">
<i class="fas fa-share"></i>
Student Messages
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
<div class="main">

<div class="topbar">

<div>

<h4>

Welcome,

<?php echo $admin_name; ?>

</h4>

<span>

Admission Office Dashboard

</span>

</div>

<div>

<span class="badge bg-success fs-6">

Admission Office

</span>

</div>

</div>
<div class="row">

    <!-- Total Applications -->

    <div class="col-lg-3 col-md-6 mb-4">

        <div class="card border-0 shadow rounded-4">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h6 class="text-muted">

                            Total Applications

                        </h6>

                        <h2 class="fw-bold text-primary">

                            <?php echo $totalApplications; ?>

                        </h2>

                    </div>

                    <div>

                        <i class="fas fa-file-alt fa-3x text-primary"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Pending -->

    <div class="col-lg-3 col-md-6 mb-4">

        <div class="card border-0 shadow rounded-4">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h6 class="text-muted">

                            Pending Review

                        </h6>

                        <h2 class="fw-bold text-warning">

                            <?php echo $pendingApplications; ?>

                        </h2>

                    </div>

                    <div>

                        <i class="fas fa-hourglass-half fa-3x text-warning"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Approved -->

    <div class="col-lg-3 col-md-6 mb-4">

        <div class="card border-0 shadow rounded-4">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h6 class="text-muted">

                            Approved

                        </h6>

                        <h2 class="fw-bold text-success">

                            <?php echo $approvedApplications; ?>

                        </h2>

                    </div>

                    <div>

                        <i class="fas fa-circle-check fa-3x text-success"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Rejected -->

    <div class="col-lg-3 col-md-6 mb-4">

        <div class="card border-0 shadow rounded-4">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h6 class="text-muted">

                            Rejected

                        </h6>

                        <h2 class="fw-bold text-danger">

                            <?php echo $rejectedApplications; ?>

                        </h2>

                    </div>

                    <div>

                        <i class="fas fa-circle-xmark fa-3x text-danger"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<!-- Recent Applications -->

<div class="card border-0 shadow rounded-4 mt-4">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">

            <i class="fas fa-file-alt me-2"></i>

            Recent Applications

        </h5>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>Application ID</th>

                        <th>Student Name</th>

                        <th>Email</th>

                        <th>Mobile</th>

                        <th>Status</th>

                        <th>Date</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php

                if($applications && $applications->num_rows > 0){

                    while($row=$applications->fetch_assoc()){

                ?>

                    <tr>

                        <td>

                            <?php echo $row['application_id']; ?>

                        </td>

                        <td>

                            <?php echo $row['first_name'].' '.$row['last_name']; ?>

                        </td>

                        <td>

                            <?php echo $row['email']; ?>

                        </td>

                        <td>

                            <?php echo $row['mobile']; ?>

                        </td>

                        <td>

                            <?php

                            $status=$row['application_status'];

                            if($status=="Approved"){

                                echo '<span class="badge bg-success">Approved</span>';

                            }

                            elseif($status=="Rejected"){

                                echo '<span class="badge bg-danger">Rejected</span>';

                            }

                            elseif($status=="Under Review"){

                                echo '<span class="badge bg-warning text-dark">Under Review</span>';

                            }

                            else{

                                echo '<span class="badge bg-primary">'.$status.'</span>';

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

                        <td colspan="7" class="text-center">

                            No Applications Found

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
<!-- Quick Actions -->

<div class="row mt-4">

    <div class="col-lg-6">

        <div class="card border-0 shadow rounded-4">

            <div class="card-header bg-success text-white">

                <h5 class="mb-0">

                    <i class="fas fa-bolt me-2"></i>

                    Quick Actions

                </h5>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <a href="manage_applications.php" class="btn btn-primary w-100">

                            <i class="fas fa-file-alt mb-2"></i>

                            <br>

                            Manage Applications

                        </a>

                    </div>

                    <div class="col-md-6 mb-3">

                        <a href="merit_list.php" class="btn btn-warning w-100">

                            <i class="fas fa-ranking-star mb-2"></i>

                            <br>

                            Merit List

                        </a>

                    </div>

                    <a href="profile.php">

<i class="fas fa-user-circle me-2"></i>

Profile

</a>

                    <div class="col-md-6 mb-3">

                        <a href="reports.php" class="btn btn-info text-white w-100">

                            <i class="fas fa-chart-column mb-2"></i>

                            <br>

                            Reports

                        </a>

                    </div>

                    <div class="col-md-6 mb-3">

                        <a href="compose_message.php" class="btn btn-success w-100">

                            <i class="fas fa-paper-plane mb-2"></i>

                            <br>

                            Compose Message

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Dashboard Info -->

    <div class="col-lg-6">

        <div class="card border-0 shadow rounded-4">

            <div class="card-header bg-dark text-white">

                <h5 class="mb-0">

                    <i class="fas fa-circle-info me-2"></i>

                    Admission Office Information

                </h5>

            </div>

            <div class="card-body">

                <p>

                    Welcome,

                    <strong><?php echo $admin_name; ?></strong>

                </p>

                <hr>

                <p>

                    Department:

                    <strong>Admission Office</strong>

                </p>

                <p>

                    Today's Date:

                    <strong>

                        <?php echo date("d F Y"); ?>

                    </strong>

                </p>

                <p>

                    Current Time:

                    <strong>

                        <?php echo date("h:i A"); ?>

                    </strong>

                </p>

                <hr>

                <div class="alert alert-primary mb-0">

                    You can review applications, verify student information,
                    approve or reject applications, prepare merit lists and
                    communicate with applicants.

                </div>

            </div>

        </div>

    </div>

</div>

<!-- Footer -->

<div class="text-center mt-5 mb-3">

    <hr>

    <p class="text-muted">

        © <?php echo date("Y"); ?>

        Global Tech University ERP System

        | Admission Office Dashboard

    </p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>