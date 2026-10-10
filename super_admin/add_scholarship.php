<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

    header("Location: login.php");
    exit;

}

$message="";

if(isset($_POST['save'])){

    $scholarship_name=trim($_POST['scholarship_name']);

    $scholarship_type=$_POST['scholarship_type'];

    $description=trim($_POST['description']);

    $eligibility=trim($_POST['eligibility']);

    $discount_type=$_POST['discount_type'];

    $discount_value=(float)$_POST['discount_value'];

    $last_date=$_POST['last_date'];

    $status=$_POST['status'];

    $check=$conn->prepare("

    SELECT id

    FROM scholarships

    WHERE scholarship_name=?

    LIMIT 1

    ");

    $check->bind_param("s",$scholarship_name);

    $check->execute();

    $result=$check->get_result();

    if($result->num_rows>0){

        $message="<div class='alert alert-danger'>Scholarship already exists.</div>";

    }else{

        $insert=$conn->prepare("

        INSERT INTO scholarships

        (

        scholarship_name,

        scholarship_type,

        description,

        eligibility,

        discount_type,

        discount_value,

        last_date,

        status

        )

        VALUES

        (?,?,?,?,?,?,?,?)

        ");

        $insert->bind_param(

        "sssssdss",

        $scholarship_name,

        $scholarship_type,

        $description,

        $eligibility,

        $discount_type,

        $discount_value,

        $last_date,

        $status

        );

        if($insert->execute()){

            header("Location: manage_scholarships.php");
            exit;

        }else{

            $message="<div class='alert alert-danger'>Unable to save scholarship.</div>";

        }

    }

}

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Scholarship</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#f4f6f9;

}

.card{

margin-top:35px;

border:none;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.10);

}

.card-header{

background:#198754;

color:#fff;

font-size:24px;

font-weight:bold;

padding:18px;

}

.form-control,
.form-select,
textarea{

border-radius:10px;

}

textarea{

resize:none;

}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card">

<div class="card-header">

<i class="fas fa-award me-2"></i>

Add New Scholarship

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Scholarship Name

</label>

<input

type="text"

name="scholarship_name"

class="form-control"

required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Scholarship Type

</label>

<select

name="scholarship_type"

class="form-select"

required>

<option value="">Select Type</option>

<option value="Merit Scholarship">Merit Scholarship</option>

<option value="Need Based Scholarship">Need Based Scholarship</option>

<option value="Sports Scholarship">Sports Scholarship</option>

<option value="Hafiz-e-Quran Scholarship">Hafiz-e-Quran Scholarship</option>

<option value="Sibling Scholarship">Sibling Scholarship</option>

<option value="Employee Child Scholarship">Employee Child Scholarship</option>

</select>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Scholarship Description

</label>

<textarea

name="description"

class="form-control"

rows="4"

placeholder="Enter complete scholarship details..."></textarea>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Eligibility Criteria

</label>

<textarea

name="eligibility"

class="form-control"

rows="4"

placeholder="Who can apply for this scholarship?"></textarea>

</div>
<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Discount Type

</label>

<select

name="discount_type"

class="form-select"

required>

<option value="Percentage">

Percentage (%)

</option>

<option value="Fixed">

Fixed Amount (Rs.)

</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Discount Value

</label>

<input

type="number"

name="discount_value"

class="form-control"

min="0"

step="0.01"

placeholder="Enter Discount"

required>

</div>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Last Apply Date

</label>

<input

type="date"

name="last_date"

class="form-control">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Status

</label>

<select

name="status"

class="form-select">

<option value="Active">

Active

</option>

<option value="Inactive">

Inactive

</option>

</select>

</div>

</div>

<div class="d-flex justify-content-between mt-4">

<a

href="manage_scholarships.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="save"

class="btn btn-success">

<i class="fas fa-save me-2"></i>

Save Scholarship

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>