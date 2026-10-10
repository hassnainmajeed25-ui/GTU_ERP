<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

    header("Location: login.php");
    exit;

}

if(!isset($_GET['id'])){

    header("Location: manage_scholarships.php");
    exit;

}

$id=(int)$_GET['id'];

$stmt=$conn->prepare("

SELECT *

FROM scholarships

WHERE id=?

LIMIT 1

");

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

    header("Location: manage_scholarships.php");
    exit;

}

$row=$result->fetch_assoc();

$message="";

if(isset($_POST['update'])){

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

    AND id<>?

    LIMIT 1

    ");

    $check->bind_param(

    "si",

    $scholarship_name,

    $id

    );

    $check->execute();

    $exists=$check->get_result();

    if($exists->num_rows>0){

        $message="<div class='alert alert-danger'>Scholarship name already exists.</div>";

    }else{

        $update=$conn->prepare("

        UPDATE scholarships

        SET

        scholarship_name=?,

        scholarship_type=?,

        description=?,

        eligibility=?,

        discount_type=?,

        discount_value=?,

        last_date=?,

        status=?

        WHERE id=?

        ");

        $update->bind_param(

        "sssssissi",

        $scholarship_name,

        $scholarship_type,

        $description,

        $eligibility,

        $discount_type,

        $discount_value,

        $last_date,

        $status,

        $id

        );

        if($update->execute()){

            header("Location: manage_scholarships.php");
            exit;

        }else{

            $message="<div class='alert alert-danger'>Unable to update scholarship.</div>";

        }

    }

}

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Scholarship</title>

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

background:#0d6efd;

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

<i class="fas fa-edit me-2"></i>

Edit Scholarship

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

value="<?php echo htmlspecialchars($row['scholarship_name']); ?>"

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

<option value="Merit Scholarship" <?php if($row['scholarship_type']=="Merit Scholarship") echo "selected"; ?>>Merit Scholarship</option>

<option value="Need Based Scholarship" <?php if($row['scholarship_type']=="Need Based Scholarship") echo "selected"; ?>>Need Based Scholarship</option>

<option value="Sports Scholarship" <?php if($row['scholarship_type']=="Sports Scholarship") echo "selected"; ?>>Sports Scholarship</option>

<option value="Hafiz-e-Quran Scholarship" <?php if($row['scholarship_type']=="Hafiz-e-Quran Scholarship") echo "selected"; ?>>Hafiz-e-Quran Scholarship</option>

<option value="Sibling Scholarship" <?php if($row['scholarship_type']=="Sibling Scholarship") echo "selected"; ?>>Sibling Scholarship</option>

<option value="Employee Child Scholarship" <?php if($row['scholarship_type']=="Employee Child Scholarship") echo "selected"; ?>>Employee Child Scholarship</option>

</select>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Scholarship Description

</label>

<textarea

name="description"

class="form-control"

rows="4"><?php echo htmlspecialchars($row['description']); ?></textarea>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Eligibility Criteria

</label>

<textarea

name="eligibility"

class="form-control"

rows="4"><?php echo htmlspecialchars($row['eligibility']); ?></textarea>

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

<option value="Percentage" <?php if($row['discount_type']=="Percentage") echo "selected"; ?>>

Percentage (%)

</option>

<option value="Fixed" <?php if($row['discount_type']=="Fixed") echo "selected"; ?>>

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

value="<?php echo $row['discount_value']; ?>"

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

class="form-control"

value="<?php echo $row['last_date']; ?>">

</div>

<div class="col-md-6 mb-3">

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

name="update"

class="btn btn-primary">

<i class="fas fa-save me-2"></i>

Update Scholarship

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