<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

    header("Location: login.php");
    exit;

}

if(!isset($_GET['id'])){

    header("Location: manage_sessions.php");
    exit;

}

$id=(int)$_GET['id'];

$get=$conn->prepare("
SELECT *
FROM admission_sessions
WHERE id=?
LIMIT 1
");

$get->bind_param("i",$id);
$get->execute();

$result=$get->get_result();

if($result->num_rows==0){

    header("Location: manage_sessions.php");
    exit;

}

$row=$result->fetch_assoc();

$message="";

if(isset($_POST['update'])){

    $session_name=trim($_POST['session_name']);
    $start_date=$_POST['start_date'];
    $end_date=$_POST['end_date'];
    $status=$_POST['status'];

    $update=$conn->prepare("
    UPDATE admission_sessions
    SET
    session_name=?,
    start_date=?,
    end_date=?,
    status=?
    WHERE id=?
    ");

    $update->bind_param(
        "ssssi",
        $session_name,
        $start_date,
        $end_date,
        $status,
        $id
    );

    if($update->execute()){

        header("Location: manage_sessions.php");
        exit;

    }else{

        $message="<div class='alert alert-danger'>Update Failed.</div>";

    }

}

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Admission Session</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#f4f6f9;

}

.card{

margin-top:40px;

border:none;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.10);

}

.card-header{

background:#0d6efd;

color:#fff;

font-size:24px;

font-weight:bold;

padding:18px;

}

.form-control{

height:50px;

border-radius:10px;

}

.form-select{

height:50px;

border-radius:10px;

}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card">

<div class="card-header">

<i class="fas fa-edit me-2"></i>

Edit Admission Session

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Admission Session

</label>

<input

type="text"

name="session_name"

class="form-control"

value="<?php echo htmlspecialchars($row['session_name']); ?>"

required>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Start Date

</label>

<input

type="date"

name="start_date"

class="form-control"

value="<?php echo $row['start_date']; ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

End Date

</label>

<input

type="date"

name="end_date"

class="form-control"

value="<?php echo $row['end_date']; ?>"

required>

</div>

</div>

<div class="mb-4">

<label class="form-label">

Status

</label>

<select

name="status"

class="form-select">

<option value="Active" <?php if($row['status']=="Active") echo "selected"; ?>>

Active

</option>

<option value="Inactive" <?php if($row['status']=="Inactive") echo "selected"; ?>>

Inactive

</option>

</select>

</div>

<div class="d-flex justify-content-between">

<a

href="manage_sessions.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="update"

class="btn btn-primary">

<i class="fas fa-save me-2"></i>

Update Session

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>